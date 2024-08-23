<?php
/* This file is under Git Control by KDSI. */
class ControllerSupportTicketuser extends Controller {
	public function login() {
		$this->load->language('support/ticketuser');

		$json = array();

		if ($this->ticketuser->isLogged()) {
			$json['redirect'] = $this->url->link('support/support', '', true);
		}

		if (!$json) {
			$this->load->model('module_ticket/ticketuser');

			// Check if ticket user has been approved.
			$ticketuser_info = $this->model_module_ticket_ticketuser->getTicketuserByEmail($this->request->post['email']);

			if ($ticketuser_info && empty($ticketuser_info['status'])) {
				$json['error']['warning'] = $this->language->get('error_approved');
			}

			if (!isset($json['error'])) {
				if (!$this->ticketuser->login($this->request->post['email'], $this->request->post['password'])) {
					$json['error']['warning'] = $this->language->get('error_login');
				}
			}
		}

		if (!$json) {
			if (isset($this->session->data['support_redirect']) && $this->session->data['support_redirect'] != $this->url->link('support/logout', '', true) && (strpos($this->session->data['support_redirect'], $this->config->get('config_url')) !== false || strpos($this->session->data['support_redirect'], $this->config->get('config_ssl')) !== false)) {
				$json['redirect'] = str_replace('&amp;', '&', $this->session->data['support_redirect']);
			} else {
				$json['redirect'] = $this->url->link('support/support', '', true);
			}

			unset($this->session->data['support_redirect']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function forgot() {
		$this->load->language('support/ticketuser');
		$this->load->model('module_ticket/ticketuser');

		$json = array();

		if ($this->ticketuser->isLogged()) {
			$json['redirect'] = $this->url->link('support/support', '', true);
		}

		if (!isset($this->request->post['email'])) {
			$json['error']['warning'] = $this->language->get('error_email');
		} elseif (!$this->model_module_ticket_ticketuser->getTotalTicketusersByEmail($this->request->post['email'])) {
			$json['error']['warning'] = $this->language->get('error_email');
		}

		$ticketuser_info = $this->model_module_ticket_ticketuser->getTicketuserByEmail($this->request->post['email']);

		if ($ticketuser_info && empty($ticketuser_info['status'])) {
			$json['error']['warning'] = $this->language->get('error_approved');
		}

		if(!$json) {
			$password = substr(sha1(uniqid(mt_rand(), true)), 0, 10);

			$this->model_module_ticket_ticketuser->editPassword($this->request->post['email'], $password);

			$ticketsetting_email = (array)$this->config->get('ticketsetting_email');

			// Send Email To User for forgot password
			if(!empty($ticketsetting_email['forgotpasswordtouser']['status'])) {
				$this->load->language('support/mail_userpassword');

				$subject = sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));

				$message  = sprintf($this->language->get('text_greeting'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8')) . "\n\n";
				$message .= $this->language->get('text_password') . "\n\n";
				$message .= $password;

				if(VERSION >= '3.0.0.0') {
					$mail = new Mail($this->config->get('config_mail_engine'));
					$mail->parameter = $this->config->get('config_mail_parameter');
					$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
					$mail->smtp_username = $this->config->get('config_mail_smtp_username');
					$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
					$mail->smtp_port = $this->config->get('config_mail_smtp_port');
					$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
				} else if(VERSION <= '2.0.1.1') {
			     	$mail = new Mail($this->config->get('config_mail'));
			    } else {
					$mail = new Mail();
					$mail->protocol = $this->config->get('config_mail_protocol');
					$mail->parameter = $this->config->get('config_mail_parameter');
					$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
					$mail->smtp_username = $this->config->get('config_mail_smtp_username');
					$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
					$mail->smtp_port = $this->config->get('config_mail_smtp_port');
					$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
				}

				$admin_email = $this->config->get('config_email');

				if ((utf8_strlen($this->config->get('ticketsetting_adminemail')) <= 96) && filter_var($this->config->get('ticketsetting_adminemail'), FILTER_VALIDATE_EMAIL)) {
					$admin_email = $this->config->get('ticketsetting_adminemail');
				}

				$mail->setTo($this->request->post['email']);
				$mail->setReplyTo($admin_email);
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
				$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
				$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
				$mail->send();
			}

			$json['success'] = $this->language->get('text_forgotsuccess');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function register() {
		$json = array();

		$this->load->language('support/ticketuser');

		$this->load->model('module_ticket/ticketuser');

		if ($this->ticketuser->isLogged()) {
			$json['redirect'] = $this->url->link('support/support', '', true);
		}

		if ((utf8_strlen(trim($this->request->post['name'])) < 1) || (utf8_strlen(trim($this->request->post['name'])) > 32)) {
			$json['error']['option']['name'] = $this->language->get('error_name');
		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$json['error']['option']['email'] = $this->language->get('error_found_email');
		}

		if ($this->model_module_ticket_ticketuser->getTotalTicketusersByEmail($this->request->post['email'])) {
			$json['error']['warning'] = $this->language->get('error_exists');
		}

		if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
			$json['error']['option']['password'] = $this->language->get('error_password');
		}

		if ($this->request->post['confirm'] != $this->request->post['password']) {
			$json['error']['option']['confirm'] = $this->language->get('error_confirm');
		}


		if(!$json) {
			$add_data=array(
				'name'	=> isset($this->request->post['name']) ? $this->request->post['name'] : '',
				'email'	=> isset($this->request->post['email']) ? $this->request->post['email'] : '',
				'password'	=> isset($this->request->post['password']) ? $this->request->post['password'] : '',
				'confirm'	=> isset($this->request->post['confirm']) ? $this->request->post['confirm'] : '',
			);

			$ticketuser_id = $this->model_module_ticket_ticketuser->addTicketUser($add_data);

			$this->ticketuser->login($this->request->post['email'], $this->request->post['password']);

			$json['success'] = true;

			if (isset($this->session->data['support_redirect']) && $this->session->data['support_redirect'] != $this->url->link('support/logout', '', true) && (strpos($this->session->data['support_redirect'], $this->config->get('config_url')) !== false || strpos($this->session->data['support_redirect'], $this->config->get('config_ssl')) !== false)) {
				$json['redirect'] = str_replace('&amp;', '&', $this->session->data['support_redirect']);
			} else {
				$json['redirect'] = $this->url->link('support/support', '', true);
			}

			unset($this->session->data['support_redirect']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function edit() {
		$json = array();

		$this->load->language('support/ticketuser');

		$this->load->model('module_ticket/ticketuser');

		if (!$this->ticketuser->isLogged()) {
			$json['redirect'] = $this->url->link('support/support', 'login=1', true);
		}

		if ((utf8_strlen(trim($this->request->post['name'])) < 1) || (utf8_strlen(trim($this->request->post['name'])) > 32)) {
			$json['error']['option']['name'] = $this->language->get('error_name');
		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$json['error']['option']['email'] = $this->language->get('error_email');
		}

		if (($this->ticketuser->getEmail() != $this->request->post['email']) && $this->model_module_ticket_ticketuser->getTotalTicketusersByEmail($this->request->post['email'])) {
			$json['error']['option']['email'] = $this->language->get('error_exists');
		}

		if(!$json) {
			$this->model_module_ticket_ticketuser->editTicketuser($this->request->post);

			$json['success'] = $this->language->get('text_editsuccess');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function editPassword() {
		$json = array();

		$this->load->language('support/ticketuser');

		$this->load->model('module_ticket/ticketuser');

		if (!$this->ticketuser->isLogged()) {
			$json['redirect'] = $this->url->link('support/support', 'login=1', true);
		}

		if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
			$json['error']['option']['password'] = $this->language->get('error_password');
		}

		if ($this->request->post['confirm'] != $this->request->post['password']) {
			$json['error']['option']['confirm'] = $this->language->get('error_confirm');
		}

		if(!$json) {
			$this->model_module_ticket_ticketuser->editPassword($this->ticketuser->getEmail(), $this->request->post['password']);

			$json['success'] = $this->language->get('text_passwordsuccess');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function imageupload() {
		$this->load->language('tool/upload');

		$this->load->model('tool/image');

		$json = array();

		if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
			// Sanitize the filename
			$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8')));

			// Validate the filename length
			if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 64)) {
				$json['error'] = $this->language->get('error_filename');
			}

			// Allowed file extension types
			$allowed = array();
			$file_ext_allowed = "png\njpe\njpeg\njpg\ngif\nbmp";
			$file_mime_allowed = "image/png\nimage/jpeg\nimage/gif\nimage/bmp";

			$extension_allowed = preg_replace('~\r?\n~', "\n", $file_ext_allowed);

			$filetypes = explode("\n", $extension_allowed);

			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}

			if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Allowed file mime types
			$allowed = array();

			$mime_allowed = preg_replace('~\r?\n~', "\n", $file_mime_allowed);

			$filetypes = explode("\n", $mime_allowed);

			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}

			if (!in_array($this->request->files['file']['type'], $allowed)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Check to see if any PHP files are trying to be uploaded
			$content = file_get_contents($this->request->files['file']['tmp_name']);

			if (preg_match('/\<\?php/i', $content)) {
/* This file is under Git Control by KDSI. */
				$json['error'] = $this->language->get('error_filetype');
			}

			// Return any upload error
			if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
				$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
			}
		} else {
			$json['error'] = $this->language->get('error_upload');
		}

		if (!$json) {
			$namedire = 'customer-profile';

			$dir = DIR_IMAGE . $namedire . '/';

			if (!file_exists($dir)) {
			    mkdir($dir, 0777, true);
			}

			if (is_file($dir . $filename)) {
				$parts = pathinfo($dir . $filename);
				if($parts) {
					$file = $parts['filename'] . '-' . rand(1, 5000) . '.'. $parts['extension'];
				} else{
					$file = $filename;
				}
			} else{
				$file = $filename;
			}

			move_uploaded_file($this->request->files['file']['tmp_name'], $dir . $file);

			// Hide the uploaded file name so people can not link to it directly.
			$this->load->model('tool/upload');

			$json['success'] = $this->language->get('text_upload');

			$customer_profile = $namedire . '/' . $file;

			$json['customer_profile'] = $customer_profile;

			if (is_file($dir . $file)) {
				$json['customer_thumb'] = $this->model_tool_image->resize($customer_profile, 60, 60);
			} else {
				$json['customer_thumb'] = $this->model_tool_image->resize($namedire . 'avtar.png', 60, 60);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}