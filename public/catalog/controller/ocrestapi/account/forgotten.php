<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiAccountForgotten extends OcrestapiController {
	private $error = array();

	public function index() {	
		$this->load_language();
		$language = $this->load->language('account/forgotten');
		$language = $this->load->language('ocrestapi/message');
		$this->load->model('account/customer');
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST')  { 

			if(!$this->validate()){
				$this->json['status'] = false;
				$this->json['errors'] = $this->error;
				return $this->sendResponse();
			}

			$data['otp']=rand(000000,999999);
			while (strlen($data['otp'])!=6) {
				$data['otp']=rand(000000,999999);
			}

			$this->load->model('setting/setting');

			$from = $this->model_setting_setting->getSettingValue('config_email', 0);

			if (!$from) {
				$from = $this->config->get('config_email');
			}

			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			// $mail->setTo($order_info['email']);
			// $mail->setTo('pankajsoni.letscms@gmail.com');
			$mail->setTo($this->request->post['email']);
			// $mail->setTo('letscmsdev@gmail.com');
			$mail->setFrom($from);
			$mail->setSender(html_entity_decode('test', ENT_QUOTES, 'UTF-8'));
			$mail->setSubject('Reset Password Otp');
			$mail->setHtml($data['otp']);
			// $mail->addAttachment($this->get_invoice_pdf($order_id));
			$mail->send();
			// return $mail;















			$data['email'] = $this->request->post['email'];
			$this->json['data'] = $data;
			return $this->sendResponse();
		}
        unset($language['backup']);
		$this->json['language'] = $language;
		return $this->sendResponse();
	}

	protected function validate() {
		if (!isset($this->request->post['email'])) {
			$this->error['email'] = $this->language->get('error_required_email');
			} elseif(!filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)){
				$this->error['email'] = $this->language->get('error_valid_email');	
			}
			elseif (!$this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$this->error['email'] = $this->language->get('error_email');
		}	
		return !$this->error;
	}

	public function updatepassword()
	{
		$this->load_language();
		$this->load->model('account/customer');
		

		if(isset($this->request->post['email'])){
			$email = $this->request->post['email']; 
		}else {
			$email = '';
		}

		if(isset($this->request->post['password'])){
			$password = $this->request->post['password']; 
		}else {
			$password = '';
		}

	 	$this->model_account_customer->editPassword($email,$password);
	 	$this->sendResponse();
	}
}

