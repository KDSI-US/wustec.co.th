<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiAccountEdit extends OcrestapiController {
	private $error = array();

	public function index() {
		$this->checkPlugin();
		

		$language=$this->load->language('account/edit');
		$language=$this->load->language('ocrestapi/message');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/customer');
 		
		if ($this->request->server['REQUEST_METHOD'] == 'POST')  {

			if (!$this->validate()) {
				$this->json['status'] = false;
				$this->json['errors'] = $this->error;
				return $this->sendResponse();
			}
			$this->model_account_customer->editCustomer($this->customer->getId(), $this->request->post);
			
			//$this->session->data['success'] = $this->language->get('text_success');	
			 // $data['success']= $this->language->get('text_success');
			//$data['success_url']=$this->url->link('account/account', '', true);
            $this->json['status']=true;
            $data['success']=$this->language->get('text_success');
			$this->json['data'] = $data;
          
			
			
			}else{

			$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
			unset($customer_info['customer_id']);
			unset($customer_info['customer_group_id']);
			unset($customer_info['language_id']);
			unset($customer_info['store_id']);
			unset($customer_info['fax']);
			unset($customer_info['password']);
			unset($customer_info['salt']);
			unset($customer_info['cart']);
			unset($customer_info['wishlist']);
			unset($customer_info['newsletter']);
			unset($customer_info['address_id']);
			unset($customer_info['custom_field']);
			unset($customer_info['ip']);
			unset($customer_info['status']);
			unset($customer_info['safe']);
			unset($customer_info['token']);
			unset($customer_info['code']);
			unset($customer_info['date_added']);
			$this->json['data'] = $customer_info;
			unset($language['backup']);
			$this->json['language'] = $language;
		}
		return $this->sendResponse();
		
	}

	protected function validate() {
		if (!isset($this->request->post['firstname'])){
			$this->error['firstname'] = $this->language->get('error_required_firstname');
		}elseif ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if (!isset($this->request->post['lastname'])){
			$this->error['lastname'] = $this->language->get('error_required_lastname');
		}elseif ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}
		if (!isset($this->request->post['email'])){
			$this->error['email'] = $this->language->get('error_required_email');
		}elseif ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}
		if(isset($this->request->post['email']) && !empty($this->request->post['email'])){
		if (($this->customer->getEmail() != $this->request->post['email']) && $this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$this->error['email'] = $this->language->get('error_exists');
		}
	}
		if (!isset($this->request->post['telephone'])){
			$this->error['telephone'] = $this->language->get('error_required_telephone');
		}elseif ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}

		// Custom field validation
		$this->load->model('account/custom_field');

		$custom_fields = $this->model_account_custom_field->getCustomFields('account', $this->config->get('config_customer_group_id'));

		foreach ($custom_fields as $custom_field) {
			if ($custom_field['location'] == 'account') {
				if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
					$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
					$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				}
			}
		}

		return !$this->error;
	}
}