<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiAccountAffiliate extends OcrestapiController {
	private $error = array();

	public function add() {

		$this->checkPlugin();
		
		if (!$this->customer->isLogged()) {

			return new Action('ocrestapi/affiliate/login');
		}

		$language = $this->load->language('account/affiliate');

		$this->load->model('account/customer');
 	  
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {

			if(! $this->validate()){
				$this->json['status'] = false;
				$this->json['errors']   = $this->error;
				return $this->sendResponse();
			}
			
			$this->model_account_customer->addAffiliate($this->customer->getId(), $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			$this->json['data'] = $this->session->data['success'];
		}
		 
		unset($language['backup']);
		$this->json['language'] = $language;
		return $this->sendResponse();
	}
	
	public function edit() {

		$this->checkPlugin();

		if (!$this->customer->isLogged()) {

			return new Action('ocrestapi/affiliate/login');

		}

		$language = $this->load->language('account/affiliate');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/customer');

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if(!$this->validate())
			{
				$this->json['status'] = false;
				$this->json['errors'] = $this->error;
				return $this->sendResponse();
			}
			$this->model_account_customer->editAffiliate($this->customer->getId(), $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			$this->json['data'] = $this->session->data['success'];
		
		}else{
		$this->getForm();
			
		}
		unset($language['backup']);
		$this->json['language'] = $language;
		return $this->sendResponse();
		
	}
		
	public function getForm() {

		$this->checkPlugin();
		if ($this->request->get['route'] == 'ocrestapi/account/affiliate/edit' && $this->request->server['REQUEST_METHOD'] != 'POST') {
			$affiliate_info = $this->model_account_customer->getAffiliate($this->customer->getId());
		}
		

		if (isset($this->request->post['company'])) {
			$data['company'] = $this->request->post['company'];
		} else if(!empty($affiliate_info)) {
			$data['company'] = $affiliate_info['company'];
		} else {
			$data['company'] = '';
		}
		
		if (isset($this->request->post['website'])) {
			$data['website'] = $this->request->post['website'];
		} elseif (!empty($affiliate_info)) {
			$data['website'] = $affiliate_info['website'];
		} else {
			$data['website'] = '';
		}
				
		if (isset($this->request->post['tax'])) {
			$data['tax'] = $this->request->post['tax'];
		} elseif (!empty($affiliate_info)) {
			$data['tax'] = $affiliate_info['tax'];
		} else {
			$data['tax'] = '';
		}

		if (isset($this->request->post['payment'])) {
			$data['payment'] = $this->request->post['payment'];
		} elseif (!empty($affiliate_info)) {
			$data['payment'] = $affiliate_info['payment'];
		} else {
			$data['payment'] = 'cheque';
		}

		if (isset($this->request->post['cheque'])) {
			$data['cheque'] = $this->request->post['cheque'];
		} elseif (!empty($affiliate_info)) {
			$data['cheque'] = $affiliate_info['cheque'];
		} else {
			$data['cheque'] = '';
		}

		if (isset($this->request->post['paypal'])) {
			$data['paypal'] = $this->request->post['paypal'];
		} elseif (!empty($affiliate_info)) {
			$data['paypal'] = $affiliate_info['paypal'];
		} else {
			$data['paypal'] = '';
		}

		if (isset($this->request->post['bank_name'])) {
			$data['bank_name'] = $this->request->post['bank_name'];
		} elseif (!empty($affiliate_info)) {
			$data['bank_name'] = $affiliate_info['bank_name'];
		} else {
			$data['bank_name'] = '';
		}

		if (isset($this->request->post['bank_branch_number'])) {
			$data['bank_branch_number'] = $this->request->post['bank_branch_number'];
		} elseif (!empty($affiliate_info)) {
			$data['bank_branch_number'] = $affiliate_info['bank_branch_number'];
		} else {
			$data['bank_branch_number'] = '';
		}

		if (isset($this->request->post['bank_swift_code'])) {
			$data['bank_swift_code'] = $this->request->post['bank_swift_code'];
		} elseif (!empty($affiliate_info)) {
			$data['bank_swift_code'] = $affiliate_info['bank_swift_code'];
		} else {
			$data['bank_swift_code'] = '';
		}

		if (isset($this->request->post['bank_account_name'])) {
			$data['bank_account_name'] = $this->request->post['bank_account_name'];
		} elseif (!empty($affiliate_info)) {
			$data['bank_account_name'] = $affiliate_info['bank_account_name'];
		} else {
			$data['bank_account_name'] = '';
		}

		if (isset($this->request->post['bank_account_number'])) {
			$data['bank_account_number'] = $this->request->post['bank_account_number'];
		} elseif (!empty($affiliate_info)) {
			$data['bank_account_number'] = $affiliate_info['bank_account_number'];
		} else {
			$data['bank_account_number'] = '';
		}

		// Custom Fields
		$this->load->model('account/custom_field');

		$data['custom_fields'] = $this->model_account_custom_field->getCustomFields($this->config->get('config_customer_group_id'));

		if (isset($this->request->post['custom_field'])) {
			$data['affiliate_custom_field'] = $this->request->post['custom_field'];
		} elseif (isset($affiliate_info)) {
			$data['affiliate_custom_field'] = json_decode($affiliate_info['custom_field'], true);
		} else {
			$data['affiliate_custom_field'] = array();
		}

		$affiliate_info = $this->model_account_customer->getAffiliate($this->customer->getId());

		if (!$affiliate_info && $this->config->get('config_affiliate_id')) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_affiliate_id'));

			if ($information_info) {
				$data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_affiliate_id'), true), $information_info['title'], $information_info['title']);
			} else {
				$data['text_agree'] = '';
			}
		} else {
			$data['text_agree'] = '';
		}

		if (isset($this->request->post['agree'])) {
			$data['agree'] = $this->request->post['agree'];
		} else {
			$data['agree'] = false;
		}
	

		$this->json['data'] = $data;
		return $this->sendResponse();
	}
	
	protected function validate() {

		if(isset($this->request->post['payment']))
		{
			if ($this->request->post['payment'] == 'cheque' && !$this->request->post['cheque']) {
				$this->error['cheque'] = $this->language->get('error_cheque');
			} elseif (($this->request->post['payment'] == 'paypal') && ((utf8_strlen($this->request->post['paypal']) > 96) || !filter_var($this->request->post['paypal'], FILTER_VALIDATE_EMAIL))) {
				$this->error['paypal'] = $this->language->get('error_paypal');
			} elseif ($this->request->post['payment'] == 'bank') {
				if ($this->request->post['bank_account_name'] == '') {
					$this->error['bank_account_name'] = $this->language->get('error_bank_account_name');
				}
		
				if ($this->request->post['bank_account_number'] == '') {
					$this->error['bank_account_number'] = $this->language->get('error_bank_account_number');
				}
			}
		}
		else{

			$this->error['payment'] = $this->language->get('error_payment_method');
		}

		
		// Custom field validation
		$this->load->model('account/custom_field');

		$custom_fields = $this->model_account_custom_field->getCustomFields($this->config->get('config_customer_group_id'));

		foreach ($custom_fields as $custom_field) {
			if ($custom_field['location'] == 'affiliate') {
				if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
					$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
					$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				}
			}
		}			
		
		// Validate agree only if customer not already an affiliate
		$affiliate_info = $this->model_account_customer->getAffiliate($this->customer->getId());
				
		if (!$affiliate_info && $this->config->get('config_affiliate_id')) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_affiliate_id'));

			if ($information_info && !isset($this->request->post['agree'])) {
				$this->error['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
			}
		}

		return !$this->error;
	}	
}