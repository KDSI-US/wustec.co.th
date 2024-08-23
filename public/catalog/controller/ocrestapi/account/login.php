<?php
require_once (DIR_SYSTEM.'engine/ocrestapicontroller.php');
class ControllerOcrestapiAccountLogin extends OcrestapiController {
	private $error = array();

	public function index() {
		$this->load_language();
	
		$this->load->model('account/customer');
		$language = $this->load->language('account/login');
		$language = $this->load->language('ocrestapi/message');
		

		if($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (!$this->validate()) {
				$this->json['status'] = false;
				$this->json['errors'] = $this->error;
				return $this->sendResponse();
			}
			if ($this->customer->isLogged()) {
				
				$customer_id =  $this->customer->getId();
				
				$language = $this->load->language('account/account');
				$tokendetail = $this->getLoggedinToken();

				
				$this->json['token'] = $tokendetail['data']['access_token'];
				$customer = $this->model_account_customer->getCustomer($customer_id);

				unset($customer['password']);
				unset($customer['salt']);
				unset($customer['token']);
				unset($customer['code']);
				
				$this->json['data'] = $customer;
				return $this->sendResponse();
			}
		}
		unset($language['backup']);
		$this->json['language'] = $language;
		return $this->sendResponse();
	}
	
	protected function validate() {
		
	

			// Check how many login attempts have been made.
			if (!isset($this->request->post['password']) || empty($this->request->post['password'])) {
				$this->error['password'] = $this->language->get('error_required_password');
			}
			if (!isset($this->request->post['email']) || empty($this->request->post['email'])) {
				$this->error['email'] = $this->language->get('error_required_email');
			}else if(!filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)){
				$this->error['email'] = $this->language->get('error_valid_email');	
			}	
			// $this->error['test']=$post;
			// return !$this->error;		
		//}

		if (!$this->error) {

			$this->load->language('account/login');
			$login_info = $this->model_account_customer->getLoginAttempts($this->request->post['email']);
			if ($login_info && ($login_info['total'] >= $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
				$this->error['attempts'] = $this->language->get('error_attempts');
			}

			// Check if customer has been approved.
			$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);
			if ($customer_info && !$customer_info['status']) {
				$this->error['approved'] = $this->language->get('error_approved');
			}

			if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {
				$this->error['login'] = $this->language->get('error_login');
				$this->model_account_customer->addLoginAttempt($this->request->post['email']);
			} else {
				$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
			}

		}

		return !$this->error;
	}
}
