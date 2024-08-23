<?php
/* This file is under Git Control by KDSI. */
class ControllerMailForgotten extends Controller {
	public function index(&$route, &$args, &$output) {			            
		$this->load->language('mail/forgotten');

		$data['text_greeting'] = sprintf($this->language->get('text_greeting'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
		$data['text_change'] = $this->language->get('text_change');
		$data['text_ip'] = $this->language->get('text_ip');
		
		$data['reset'] = str_replace('&amp;', '&', $this->url->link('account/reset', 'code=' . $args[1], true));
		$data['ip'] = $this->request->server['REMOTE_ADDR'];
		
			// Prepare mail: customer.forgotten
			$this->load->model('extension/module/emailtemplate');

			$this->load->model('account/customer');

			$customer_info = $this->model_account_customer->getCustomerByEmail($args[0]);

			$template_load = array('key' => 'customer.forgotten');

			if ($customer_info) {
				$template_load['customer_id'] = $customer_info['customer_id'];
				$template_load['customer_group_id'] = $customer_info['customer_group_id'];
				$template_load['language_id'] = $customer_info['language_id'];
			}

			$template = $this->model_extension_module_emailtemplate->load($template_load, $data);

            if ($template) {
                if (isset($this->request->post['email'])) {
                    $template->addData($this->request->post['email']);
                }

                if ($customer_info) {
                    $template->addData($customer_info, 'customer');
                }

                if (!empty($template->data['text_greeting'])) {
                    $template->data['text_greeting'] = sprintf($template->data['text_greeting'], $template->data['store_name']);
                }

                if (isset($template->data['text_ip'])) {
                    $template->data['text_ip'] = sprintf($template->data['text_ip'], $this->request->server['REMOTE_ADDR']);
                }

                $template->data['password_link'] = $this->url->link('account/reset', 'email=' . urlencode($args[0]) . '&code=' . $args[1]);

                if (!empty($template->data['button_password_link'])) {
                    $template->data['password_link_text'] = $template->data['button_password_link'];
                } else {
                    $template->data['password_link_text'] = $template->data['password_link'];
                }

                $template->data['account_login'] = $this->url->link('account/login');

                if (!empty($template->data['button_account_login'])) {
                    $template->data['account_login_text'] = $template->data['button_account_login'];
                } else {
                    $template->data['account_login_text'] = $template->data['account_login'];
                }
			    // Prepared mail: customer.forgotten
            }
		
		$mail = new Mail($this->config->get('config_mail_engine'));
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

		$mail->setTo($args[0]);
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
		$mail->setSubject(html_entity_decode(sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8')), ENT_QUOTES, 'UTF-8'));
		$mail->setText($this->load->view('mail/forgotten', $data));
		// Send mail: customer.forgotten
			if ($template && $template->check()) {
			    $template->build();
			    $template->hook($mail);

                $mail->send();

                $this->model_extension_module_emailtemplate->sent();
            }
	}
}
