<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiAccountRegister extends OcrestapiController {
	private $error = array();

	public function index() {
		$this->load_language();

		$language = $this->load->language('account/register');

		$this->load->model('account/customer');
	
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (!$this->validate()) {
				$this->json['status'] = false;
				$this->json['errors'] = $this->error;
				return $this->sendResponse();
			}
			$customer_id = $this->model_account_customer->addCustomer($this->request->post);
			//print_r($customer_id);die;
			// Clear any previous login attempts for unregistered accounts.
			$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);

			$this->customer->login($this->request->post['email'], $this->request->post['password']);
			
			$data = $this->model_account_customer->getCustomer($customer_id);

			if ($this->customer->isLogged()){
				$tokendetail = $this->getLoggedinToken();
				$this->json['token'] = $tokendetail['data']['access_token'];
			}else{
				$this->json['token']='';
			}
			$this->json['data'] = $data;
			return $this->sendResponse();
		}

		$data['customer_groups'] = array();
		
		if (is_array($this->config->get('config_customer_group_display'))) {
			$this->load->model('account/customer_group');

			$customer_groups = $this->model_account_customer_group->getCustomerGroups();

			foreach ($customer_groups as $customer_group) {
				if (in_array($customer_group['customer_group_id'], $this->config->get('config_customer_group_display'))) {
					$data['customer_groups'][] = $customer_group;
				}
			}
		}

		// Custom Fields
		/*$data['custom_fields'] = array();
		
		$this->load->model('account/custom_field');
		$custom_fields = $this->model_account_custom_field->getCustomFields();

		foreach ($custom_fields as $custom_field) {
			if ($custom_field['location'] == 'account') {
				$data['custom_fields'][] = $custom_field;
			}
		}*/

		// Captcha

		if ($this->config->get('config_account_id')) {
			$this->load->model('catalog/information');
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
			if ($information_info) {
				$data['text_agree']['information_id'] = $this->config->get('config_account_id');
				$data['text_agree']['title'] =  $information_info['title'];
			} else {
				$data['text_agree'] = [];
			}
		} else {
			$data['text_agree'] = [];
		}
		
		unset($language['backup']);
		$this->json['language'] = $language;
		$this->json['data'] = $data;
		return $this->sendResponse();


	}

	private function validate() {
		if (!isset($this->request->post['firstname']) || ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32))) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if (!isset($this->request->post['lastname']) || ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32))) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}

		if (!isset($this->request->post['email']) || ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL))) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if (!isset($this->request->post['email']) || $this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$this->error['email'] = $this->language->get('error_exists');
		}

		if (!isset($this->request->post['telephone']) || ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32))) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}else if(!is_numeric($this->request->post['telephone'])){
			$this->error['telephone'] = $this->language->get('error_telephone');
		}

		// Customer Group
		if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->post['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		// Custom field validation
		$this->load->model('account/custom_field');

		$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

		foreach ($custom_fields as $custom_field) {
			if ($custom_field['location'] == 'account') {
				if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
					$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
					$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				}
			}
		}
 
		if (!isset($this->request->post['password']) || ((utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 20))) {
			$this->error['password'] = $this->language->get('error_password');
		}

		if (isset($this->request->post['password']) && ($this->request->post['confirm'] != $this->request->post['password'])) {
			$this->error['confirm'] = $this->language->get('error_confirm');
		}

		// Captcha
		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
			$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

			if ($captcha) {
				$this->error['captcha'] = $captcha;
			}
		}

		// Agree to terms
		if ($this->config->get('config_account_id')) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

			if ($information_info && !isset($this->request->post['agree'])) {
				$this->error['agree'] = sprintf($this->language->get('error_agree'), $information_info['title']);
			}else if(empty($this->request->post['agree']) && $this->request->post['agree']==0){
				$this->error['agree'] = sprintf($this->language->get('error_agree'), $information_info['title']);
			}
		}
		
		return !$this->error;
	}

	// public function customfield() {
	// 	$json = array();

	// 	$this->load->model('account/custom_field');

	// 	// Customer Group
	// 	if (isset($this->request->get['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->get['customer_group_id'], $this->config->get('config_customer_group_display'))) {
	// 		$customer_group_id = $this->request->get['customer_group_id'];
	// 	} else {
	// 		$customer_group_id = $this->config->get('config_customer_group_id');
	// 	}

	// 	$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

	// 	foreach ($custom_fields as $custom_field) {
	// 		$json[] = array(
	// 			'custom_field_id' => $custom_field['custom_field_id'],
	// 			'required'        => $custom_field['required']
	// 		);
	// 	}

	// 	$this->response->addHeader('Content-Type: application/json');
	// 	$this->response->setOutput(json_encode($json));
	// }
}