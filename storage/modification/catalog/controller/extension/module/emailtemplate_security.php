<?php
/* This file is under Git Control by KDSI. */

class ControllerExtensionModuleEmailTemplateSecurity extends Controller {

	// catalog/model/account/customer/editPassword/after
	public function eventPasswordChanged(&$route, &$args, &$output) {
		if (empty($args[0]) || !$this->config->get('module_emailtemplate_security_status') || !$this->config->get('module_emailtemplate_security_password_changed')) {
			return null;
		}

		$email = $args[0];

		$this->load->model('account/customer');

		$customer_info = $this->model_account_customer->getCustomerByEmail($email);

		// Prepare mail: customer.password_changed
		$this->load->model('extension/module/emailtemplate');

		$template_data = array(
			'key' => 'customer.password_changed',
			'customer_id' => $customer_info['customer_id'],
			'customer_group_id' => $customer_info['customer_group_id'],
			'language_id' => $customer_info['language_id'],
			'store_id' => $customer_info['store_id']
		);

		$template = $this->model_extension_module_emailtemplate->load($template_data);

		if ($template) {
			$template->addData($customer_info, 'customer');

			$template->data['datetime'] = date($this->language->get('datetime_format'), time());

			$template->data['subject'] = $this->language->get('text_subject');

			$template->data['account_login'] = $this->url->link('account/login');

			if (!empty($template->data['button_account_login'])) {
				$template->data['account_login_text'] = $template->data['button_account_login'];
			} else {
				$template->data['account_login_text'] = $template->data['account_login'];
			}
			// Prepared mail: customer.password_changed

			$template->build();

			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($customer_info['email']);
			$mail->setFrom($template->data['store_email']);
			$mail->setSender($template->data['store_name']);
			$mail->setSubject($template->data['store_name']);

			// Send mail: customer.password_changed
            if ($template && $template->check()) {
                $template->hook($mail);

                $mail->send();

                $this->model_extension_module_emailtemplate->sent();
            }
		}
	}

	// No event available so called manually from: system/cart/customer.php
	public function customerIpChanged($data) {
		if (empty($data[0]) || !$this->config->get('module_emailtemplate_security_status') || !$this->config->get('module_emailtemplate_security_ip_changed')) {
			return null;
		}

		$customer_id = (int)$data[0];

		$this->load->model('account/customer');

		$max_login_attempts = $this->config->get('config_login_attempts');

		$customer_info = $this->model_account_customer->getCustomer($customer_id);

		if (!$customer_info) {
			return null; // missing customer
		}

		if (!empty($this->request->server['HTTP_CLIENT_IP'])) {
			$ip_address = $this->request->server['HTTP_CLIENT_IP'];
		} elseif (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
			$ip_address = $this->request->server['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip_address = $this->request->server['REMOTE_ADDR'];
		}

		$customer_ips = $this->model_account_customer->getIps($customer_id);

		$ips = array($customer_info['ip']);

		foreach ($customer_ips as $customer_ip) {
			$ips[] = $customer_ip['ip'];
		}

		if (in_array($ip_address, $ips)) {
			return null; // no change
		}

		$this->load->model('extension/module/emailtemplate');

		$template_data = array(
			'key' => 'customer.ip_changed',
			'customer_id' => $customer_info['customer_id'],
			'customer_group_id' => $customer_info['customer_group_id'],
			'language_id' => $customer_info['language_id'],
			'store_id' => $customer_info['store_id']
		);

		$template = $this->model_extension_module_emailtemplate->load($template_data);

		if ($template) {
			$this->load->language('extension/module/emailtemplate/customer_ip_changed');

			$template->data['login_info_date'] = date($this->language->get('datetime_format'));

			if (!empty($this->request->server['HTTP_USER_AGENT'])) {
				$template->data['login_info_browser'] = $this->request->server['HTTP_USER_AGENT'];
			}

			if (!empty($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
				$template->data['login_info_locale'] = extension_loaded('intl') ? Locale::acceptFromHttp($this->request->server['HTTP_ACCEPT_LANGUAGE']) : $this->request->server['HTTP_ACCEPT_LANGUAGE'];
			}

			$template->addData($customer_info, 'customer');

			$template->data['config_login_attempts'] = $max_login_attempts;

			$template->data['email'] = $customer_info['email'];

			$template->data['customer_ip'] = $ip_address;

			$template->build();

			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($customer_info['email']);
			$mail->setSender(trim($customer_info['firstname'] . ' ' . $customer_info['lastname']));
			$mail->setFrom($template->data['store_email']);
			$mail->setSubject($template->data['store_name']);

			$template->hook($mail);

			$mail->send();

			$this->model_extension_module_emailtemplate->sent();
		}
	}

	/**
	 * Send Email to Admin Notifying them of customer reaching max login attempts
	 * - email template: customer.max_login_admin
	 *
	 * @param $route
	 * @param $args
	 * @param $output
	 */
	public function eventLoginLimit(&$route, &$args, &$output) {
		if (!$this->config->get('module_emailtemplate_security_status') || !$this->config->get('module_emailtemplate_security_login_limit')) {
			return null;
		}

		$this->load->model('account/customer');

		$max_login_attempts = $this->config->get('config_login_attempts');

		$customer_email = $args[0];

		$login_info = $this->model_account_customer->getLoginAttempts($customer_email);

		if (!$login_info || ($login_info['total'] < $max_login_attempts)) {
			return null;
		}

		$customer_info = $this->model_account_customer->getCustomerByEmail($customer_email);

		if (!$customer_info) {
			return null;
		}

		$this->load->model('extension/module/emailtemplate');

		$template_data = array(
			'key' => 'customer.max_login_admin',
			'customer_id' => $customer_info['customer_id'],
			'customer_group_id' => $customer_info['customer_group_id'],
			'language_id' => $customer_info['language_id'],
			'store_id' => $customer_info['store_id']
		);

		$template = $this->model_extension_module_emailtemplate->load($template_data);

		if ($template) {
			$this->load->language('extension/module/emailtemplate/customer_max_login_admin');

			$login_info['login_info_added'] = date($this->language->get('datetime_format'), strtotime($login_info['date_added']));

			$customer_info['date_added'] = date($this->language->get('date_format_short'), strtotime($customer_info['date_added']));

			$customer_info['status'] = $customer_info['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled');

			$template->addData($login_info, 'login_attempt');

			$template->addData($customer_info, 'customer');

			if (!empty($this->request->server['HTTP_USER_AGENT'])) {
				$template->data['login_info_browser'] = $this->request->server['HTTP_USER_AGENT'];
			}

			if (!empty($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
				$template->data['login_info_locale'] = extension_loaded('intl') ? Locale::acceptFromHttp($this->request->server['HTTP_ACCEPT_LANGUAGE']) : $this->request->server['HTTP_ACCEPT_LANGUAGE'];
			}

			$template->data['config_login_attempts'] = $max_login_attempts;

			$template->data['email'] = $customer_email;

			if (defined('HTTP_ADMIN')) {
				$admin_url = HTTP_ADMIN;
			} else {
				$admin_url = HTTPS_SERVER . 'admin/';
			}

			$template->data['admin_customer_link'] = $admin_url . 'index.php?route=customer/customer&filter_email=' . $customer_info['email'];

			$template->data['admin_unlock_link'] = $admin_url . 'index.php?route=customer/customer/unlock&email=' . $customer_info['email'];

			$template->build();

			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($template->data['store_email']);
			$mail->setFrom($template->data['store_email']);
			$mail->setReplyTo($customer_info['email'], $customer_info['firstname']);
			$mail->setSender($template->data['store_name']);
			$mail->setSubject($template->data['store_name']);

			$template->hook($mail);

			$mail->send();

			$this->model_extension_module_emailtemplate->sent();
		}
	}

}
