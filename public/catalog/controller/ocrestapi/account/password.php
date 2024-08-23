<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiAccountPassword extends OcrestapiController {
	private $error = array();

	public function index() {
		$this->checkPlugin();
		$language = $this->load->language('account/password');
		$language = $this->load->language('ocrestapi/account/address');
		$this->load->model('ocrestapi/ocrestapi');
		if($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (!$this->validate()) {
				$this->json['status'] = false;
				$this->json['errors']   = $this->error;
				return $this->sendResponse();
			}
			$this->load->model('account/customer');
			
			$this->model_account_customer->editPassword($this->customer->getEmail(), $this->request->post['password']);
			$data['success']=$this->language->get('text_success');
			$this->json['data'] = $data;
			return $this->sendResponse();
		}

		if (isset($this->request->post['old_password'])) {
			$data['old_password'] = $this->request->post['old_password'];
		} else {
			$data['old_password'] = '';
		}

		if (isset($this->request->post['password'])) {
			$data['password'] = $this->request->post['password'];
		} else {
			$data['password'] = '';
		}

		if (isset($this->request->post['confirm'])) {
			$data['confirm'] = $this->request->post['confirm'];
		} else {
			$data['confirm'] = '';
		}
		unset($language['backup']);
		$this->json['language'] = $language;
		return $this->sendResponse();
	}

	protected function validate() {
		if (!isset($this->request->post['old_password'])) {
			$this->error['old_password'] = $this->language->get('error_oldpassword');
		
		}else{
			$password = $this->model_ocrestapi_ocrestapi->checkpassword($this->customer->getEmail(),$this->request->post['old_password']);
		if($password == false)
		{
			$this->error['old_password'] = $this->language->get('error_match_oldpassword');

		}
	}

		if(!isset($this->request->post['password'])){
		$this->error['password'] = $this->language->get('error_password');
		}else{
			if(!isset($this->request->post['password']) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
				$this->error['password'] = $this->language->get('error_password');
			}
		
			if (!isset($this->request->post['confirm']) || $this->request->post['password']!=$this->request->post['confirm'])  {
				$this->error['confirm'] = $this->language->get('error_confirm');
			}
		}

		return !$this->error;
	}
}