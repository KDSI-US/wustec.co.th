<?php
class ControllerExtensionModuleEmailtemplateEventCustom extends Controller {

	/**
	 * Check if customer_group_id has changed
	 * Event: admin/model/customer/customer/editCustomer/before
	 */
	public function beforeEditCustomer(&$route, &$args) {
		if (empty($args[0]) || empty($args[1]['customer_group_id'])) {
			return null;
		}

		if (isset($this->session->data['customer_group_changed'])) {
			unset($this->session->data['customer_group_changed']);
		}

		$customer_id = $args[0];
		$customer_group_id = $args[1]['customer_group_id'];

		$this->load->model('customer/customer');

		$customer_info = $this->model_customer_customer->getCustomer($customer_id);

		if (!$customer_info) {
			trigger_error('Error: unable to find customer: ' . $customer_id);
			return null;
		}

		// Customer group changed?
		if ($customer_info['customer_group_id'] != $customer_group_id && $customer_group_id != $this->config->get('config_customer_group_id')) {
			$this->session->data['customer_group_changed'] = true;
		}
	}

	/**
	 * Send email if customer_group_id has changed.
	 * Event: admin/model/customer/customer/editCustomer/after
	 */
	public function afterEditCustomer(&$route, &$args, &$output) {
		if (!isset($this->session->data['customer_group_changed'])) {
			return null;
		}

		unset($this->session->data['customer_group_changed']);

		$customer_id = $args[0];

		$this->load->model('customer/customer');

		$customer_info = $this->model_customer_customer->getCustomer($customer_id);

		if (!$customer_info) {
			trigger_error('Error: unable to find customer: ' . $customer_id);
			return null;
		}

		$template_load = array(
			'key' => 'admin.customer_group',
			'customer_id' => $customer_info['customer_id'],
			'customer_group_id' => $customer_info['customer_group_id'],
			'language_id' => $customer_info['language_id'],
			'store_id' => $customer_info['store_id'],
		);

		$this->load->model('extension/module/emailtemplate');

		$template = $this->model_extension_module_emailtemplate->load($template_load);

		if ($template) {
			$template->addData($customer_info);

			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($customer_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));

			$template->hook($mail);

			$mail->send();

			$this->model_extension_module_emailtemplate->sent();
		}
	}
}