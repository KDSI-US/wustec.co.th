<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiAccountVoucher extends ocrestapicontroller {
	private $error = array();

	public function index() {
		
			$this->checkPlugin();
		
		$language = $this->load->language('account/voucher');
		$language = $this->load->language('ocrestapi/message');
		 $this->load->model('ocrestapi/ocrestapi');

		
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if(!$this->validate())
			{
				$this->json['status'] = false;
				$this->json['errors'] = $this->error;
				return $this->sendResponse();
			}
			 
			 $get_token= $this->get_request_token();

			$token = explode(' ', $get_token)[1];
			if(isset($token)){
				$this->model_ocrestapi_ocrestapi->addVoucherdata($this->request->post, $token);
				 
			}
			
			 return $this->sendResponse(); 
		}
			
		$data['help_amount'] = sprintf($this->language->get('help_amount'), $this->currency->format($this->config->get('config_voucher_min'), $this->config->get('config_currency')), $this->currency->format($this->config->get('config_voucher_max'), $this->config->get('config_currency')));

		if (isset($this->request->post['to_name'])) {
			$data['to_name'] = $this->request->post['to_name'];
		} else {
			$data['to_name'] = '';
		}

		if (isset($this->request->post['to_email'])) {
			$data['to_email'] = $this->request->post['to_email'];
		} else {
			$data['to_email'] = '';
		}

		if (isset($this->request->post['from_name'])) {
			$data['from_name'] = $this->request->post['from_name'];
		} elseif ($this->customer->isLogged()) {
			$data['from_name'] = $this->customer->getFirstName() . ' '  . $this->customer->getLastName();
		} else {
			$data['from_name'] = '';
		}

		if (isset($this->request->post['from_email'])) {
			$data['from_email'] = $this->request->post['from_email'];
		} elseif ($this->customer->isLogged()) {
			$data['from_email'] = $this->customer->getEmail();
		} else {
			$data['from_email'] = '';
		}

		$this->load->model('extension/total/voucher_theme');

		$data['voucher_themes'] = $this->model_extension_total_voucher_theme->getVoucherThemes();

		if (isset($this->request->post['voucher_theme_id'])) {
			$data['voucher_theme_id'] = $this->request->post['voucher_theme_id'];
		} else {
			$data['voucher_theme_id'] = '';
		}

		if (isset($this->request->post['message'])) {
			$data['message'] = $this->request->post['message'];
		} else {
			$data['message'] = '';
		}

		if (isset($this->request->post['amount'])) {
			$data['amount'] = $this->request->post['amount'];
		} else {
			$data['amount'] = $this->currency->format($this->config->get('config_voucher_min'), $this->config->get('config_currency'), false, false);
		}

		if (isset($this->request->post['agree'])) {
			$data['agree'] = $this->request->post['agree'];
		} else {
			$data['agree'] = false;
		}
		unset($language['backup']);
		$this->json['language'] = $language;
			$this->json['data'] = $data;
			
			return $this->sendResponse();
	}
	protected function validate() {
		if(!isset($this->request->post['to_name']) || empty($this->request->post['to_name'])){
			$this->error['to_name'] = $this->language->get('error_required_to_name');
		}else if ((utf8_strlen($this->request->post['to_name']) < 1) || (utf8_strlen($this->request->post['to_name']) > 64)) {
			$this->error['to_name'] = $this->language->get('error_to_name');
		}

		if(!isset($this->request->post['to_email']) || empty($this->request->post['to_email'])){
			$this->error['to_email'] = $this->language->get('error_required_to_email');
		}else if ((utf8_strlen($this->request->post['to_email']) > 96) || !filter_var($this->request->post['to_email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['to_email'] = $this->language->get('error_email');
		}

		if(!isset($this->request->post['from_name']) || empty($this->request->post['from_name'])){
			$this->error['from_name'] = $this->language->get('error_required_from_name');
		}else if ((utf8_strlen($this->request->post['from_name']) < 1) || (utf8_strlen($this->request->post['from_name']) > 64)) {
			$this->error['from_name'] = $this->language->get('error_from_name');
		}

		if(!isset($this->request->post['from_email']) || empty($this->request->post['from_email'])){
			$this->error['from_email'] = $this->language->get('error_required_from_email');
		}else if ((utf8_strlen($this->request->post['from_email']) > 96) || !filter_var($this->request->post['from_email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['from_email'] = $this->language->get('error_email');
		}

		if (!isset($this->request->post['voucher_theme_id']) || empty($this->request->post['voucher_theme_id']) && !is_numeric($this->request->post['voucher_theme_id'])) {
			$this->error['voucher_theme_id'] = $this->language->get('error_theme');
		}else {

			$voucher_theme_id = $this->model_ocrestapi_ocrestapi->checkVoucherThemeID($this->request->post['voucher_theme_id']);
			
			if($voucher_theme_id == false){
				$this->error['voucher_theme_id'] = $this->language->get('error_theme');
			}
		}

		if(!isset($this->request->post['amount']) || !is_numeric($this->request->post['amount'])){
			$this->error['amount'] = $this->language->get('error_required_amount');
		}else if (($this->currency->convert($this->request->post['amount'], $this->config->get('config_currency'), $this->config->get('config_currency')) < $this->config->get('config_voucher_min')) || ($this->currency->convert($this->request->post['amount'], $this->config->get('config_currency'), $this->config->get('config_currency')) > $this->config->get('config_voucher_max')) && $this->request->post['amount'] < 1) {
			$this->error['amount'] = sprintf($this->language->get('error_amount'), $this->currency->format($this->config->get('config_voucher_min'), $this->config->get('config_currency')), $this->currency->format($this->config->get('config_voucher_max'), $this->config->get('config_currency')));
		}

		if(!isset($this->request->post['message'])){
			$this->error['message'] = $this->language->get('error_required_message');
		}

		if (!isset($this->request->post['agree'])) {
			$this->error['agree'] = $this->language->get('error_agree');
		}else if(empty($this->request->post['agree']) && $this->request->post['agree']==0){
				$this->error['agree'] = $this->language->get('error_agree');
			}

		return !$this->error;
	}
}
