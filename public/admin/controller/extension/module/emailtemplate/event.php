<?php
class ControllerExtensionModuleEmailtemplateEvent extends Controller {

	public function afterEvent(&$route, &$args, &$output) {
		$query = "SELECT * FROM `" . DB_PREFIX . "event` e INNER JOIN `" . DB_PREFIX . "emailtemplate_event` ee ON(ee.event_id = e.event_id) WHERE e.trigger LIKE '%". $this->db->escape(trim($route, '/')) ."/after' LIMIT 1";

		$result = $this->db->query($query);

		if (!$result->rows) return null;

		$this->load->model('extension/module/emailtemplate');

		$this->load->model('setting/setting');
		$this->load->model('setting/store');

		foreach($result->rows as $row) {
			$template_data = array();

			$template_load = array(
				'key' => $row['emailtemplate_key']
			);

			if (isset($args['language_id'])) {
				$template_load['language_id'] = $args['language_id'];
			}

			$customer_id = 0;

			if (isset($args['order_id'])) {
				$this->load->model('sale/order');

				$order_info = $this->model_sale_order->getOrder($args['order_id']);

				if ($order_info) {
					$template_load['store_id'] = $order_info['store_id'];
					$template_load['customer_id'] = $customer_id = $order_info['customer_id'];
					$template_load['language_id'] = $order_info['language_id'];

					foreach ($order_info as $key => $val) {
						if (!isset($template_data['order_' . $key])) {
							$template_data['order_' . $key] = $val;
						}
					}
				}
			}

			if (isset($args['customer_id'])) {
				$customer_id = $args['customer_id'];
			}

			if ($customer_id) {
				$this->load->model('customer/customer');

				$customer_info = $this->model_customer_customer->getCustomer($customer_id);

				if ($customer_info) {
					$template_load['customer_id'] = $customer_id;
					$template_load['customer_group_id'] = $customer_info['customer_group_id'];
					$template_load['language_id'] = $customer_info['language_id'];

					foreach ($customer_info as $key => $val) {
						if (!isset($template_data['customer_' . $key])) {
							$template_data['customer_' . $key] = $val;
						}
					}
				}
			}

			$template = $this->model_extension_module_emailtemplate->load($template_load, $template_data);

			if ($template) {
				if ($args && is_array($args)) {
					foreach ($args as $i => $arg) {
						if (is_array($arg)) {
							$template->addData($arg);
						} elseif (is_string($i)) {
							$template->addData($i, $arg);
						} else {
							$template->addData('arg' . $i, $arg);
						}
					}
				}

				if ($output) {
					$template->addData('output', $output);
				}

				$this->load->model('setting/store');
				$this->load->model('setting/setting');

				if (!empty($order_info)) {
					$store_id = $order_info['store_id'];
				} elseif (!empty($customer_info)) {
					$store_id = $customer_info['store_id'];
				} else {
					$store_id = $this->config->get('config_store_id');
				}

				$store_info = array_merge(
					$this->model_setting_setting->getSetting("config", $store_id),
					$this->model_setting_store->getStore($store_id)
				);

				$template->build();

				$mail = new Mail(isset($store_info['config_mail_engine']) ? $store_info['config_mail_engine'] : $this->config->get('config_mail_engine'));
				$mail->parameter = isset($store_info['config_mail_parameter']) ? $store_info['config_mail_parameter'] : $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = isset($store_info['config_mail_smtp_hostname']) ? $store_info['config_mail_smtp_hostname'] : $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = isset($store_info['config_mail_smtp_username']) ? $store_info['config_mail_smtp_username'] : $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode((isset($store_info['config_mail_smtp_password']) ? $store_info['config_mail_smtp_password'] : $this->config->get('config_mail_smtp_password')), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = isset($store_info['config_mail_smtp_port']) ? $store_info['config_mail_smtp_port'] : $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = isset($store_info['config_mail_smtp_timeout']) ? $store_info['config_mail_smtp_timeout'] : $this->config->get('config_mail_smtp_timeout');

				$mail->setTo($template->data['store_email']);
				$mail->setFrom($template->data['store_email']);
				$mail->setSender($template->data['store_name']);
				$mail->setSubject($template->data['store_name']);

				$template->hook($mail);

				$mail->send();

				$this->model_extension_module_emailtemplate->sent();
			}
		}
	}

	// admin/model/customer/customer/addCustomer/after
	public function customerCreate(&$route, &$args, &$output) {
		if (empty($args[0]['notify'])) {
			return null;
		}

		$this->load->model('customer/customer');
		$this->load->model('customer/customer_group');
		$this->load->model('extension/module/emailtemplate');
		$this->load->model('localisation/language');
		$this->load->model('setting/setting');
		$this->load->model('setting/store');

		$customer_info = $this->model_customer_customer->getCustomer($output);

		if (!$customer_info) {
			trigger_error('Error: unable to find customer: ' . $output);
			return null;
		}

		$store_info = array_merge(
			$this->model_setting_setting->getSetting("config", $customer_info['store_id']),
			$this->model_setting_store->getStore($customer_info['store_id'])
		);

		$language_info = $this->model_localisation_language->getLanguage($customer_info['language_id']);

		if ($language_info) {
			$language_code = $language_info['code'];
		} else {
			$language_code = $store_info['config_language'];
		}

		$language = new Language($language_code);
		$language->load($language_code);
		$language->load('extension/module/emailtemplate/customer');

		$customer_group_info = $this->model_customer_customer_group->getCustomerGroup($customer_info['customer_group_id']);

		$template_load = array(
			'key' => 'admin.customer_create',
			'customer_id' => $customer_info['customer_id'],
			'customer_group_id' => $customer_info['customer_group_id'],
			'language_id' => $customer_info['language_id'],
			'store_id' => $customer_info['store_id'],
		);

		$template = $this->model_extension_module_emailtemplate->load($template_load);

		if ($template) {
			$template_data = $args[0];

			$template_data['newsletter'] = $this->language->get((isset($args[0]['newsletter']) && $args[0]['newsletter'] == 1) ? 'text_yes' : 'text_no');

			$template_data['account_login'] = $template->data['store_url'] . 'index.php?route=account/login';

			if ($this->language->get('button_account_login') && $this->language->get('button_account_login') != 'button_account_login') {
				$template->data['account_login_text'] = $template->data['button_account_login'];
			} else {
				$template->data['account_login_text'] = $template_data['account_login'];
			}

			$template->addData($customer_info, 'customer');

			if (!empty($args[0]['custom_field'])) {
				$this->load->model('customer/custom_field');

				$custom_fields = $this->model_customer_custom_field->getCustomFields($customer_info['customer_group_id']);

				foreach ($custom_fields as $custom_field) {
					if (isset($args[0]['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
						$custom_field_key = 'custom_field_' . ($custom_field['location'] != 'account' ? $custom_field['location'] . '_' : '') . $custom_field['custom_field_id'];
						$custom_field_value = $args[0]['custom_field'][$custom_field['location']][$custom_field['custom_field_id']];

						$template->data[$custom_field_key . '_name'] = $custom_field['name'];
						$template->data[$custom_field_key . '_value'] = '';

						if ($custom_field['custom_field_value']) {
							foreach ($custom_field['custom_field_value'] as $custom_field_value_info) {
								if (is_array($custom_field_value)) {
									if (in_array($custom_field_value_info['custom_field_value_id'], $custom_field_value)) {
										$template->data[$custom_field_key . '_value_' . $custom_field_value_info['custom_field_value_id']] = $custom_field_value_info['name'];
									}
								} else {
									if ($custom_field_value_info['custom_field_value_id'] == $custom_field_value) {
										$template->data[$custom_field_key . '_value'] = $custom_field_value_info['name'];
									}
								}
							}
						} else {
							$template->data[$custom_field_key . '_value'] = $custom_field_value;
						}
					}
				}
			}

			$template_data['customer_group'] = $customer_group_info['name'];

			if (!$customer_info['status']) {
				$template_data['customer_text'] = $this->language->get('text_approval');
			} else {
				$template_data['customer_text'] = $this->language->get('text_login');
			}

			$template->addData($template_data);

			$mail = new Mail(isset($store_info['config_mail_engine']) ? $store_info['config_mail_engine'] : $this->config->get('config_mail_engine'));
			$mail->parameter = isset($store_info['config_mail_parameter']) ? $store_info['config_mail_parameter'] : $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = isset($store_info['config_mail_smtp_hostname']) ? $store_info['config_mail_smtp_hostname'] : $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = isset($store_info['config_mail_smtp_username']) ? $store_info['config_mail_smtp_username'] : $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode((isset($store_info['config_mail_smtp_password']) ? $store_info['config_mail_smtp_password'] : $this->config->get('config_mail_smtp_password')), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = isset($store_info['config_mail_smtp_port']) ? $store_info['config_mail_smtp_port'] : $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = isset($store_info['config_mail_smtp_timeout']) ? $store_info['config_mail_smtp_timeout'] : $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($customer_info['email']);
			$mail->setFrom($store_info['config_email']);
			$mail->setSender(html_entity_decode($store_info['config_name'], ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode($store_info['config_name'], ENT_QUOTES, 'UTF-8'));

			$template->hook($mail);

			$mail->send();

			$this->model_extension_module_emailtemplate->sent();
		}
	}

	/**
	 * Send email after customer history is created
	 * key: admin.customer_history
	 *
	 * @param $route
	 * @param $args (customer_id, comment, notify)
	 * @param $output
	 * @return bool
	 */
	public function customerHistory(&$route, &$args, &$output) {
		if (empty($args[0]) || empty($args[2])) {
			return null;
		}

		$customer_history_id = $output;
		$customer_id = $args[0];
		$comment = $args[1];

		$this->load->model('customer/customer');
		$this->load->model('extension/module/emailtemplate');
		$this->load->model('localisation/language');
		$this->load->model('setting/setting');
		$this->load->model('setting/store');

		$customer_history_info = $this->model_extension_module_emailtemplate->getCustomerHistory($customer_history_id);

		if (!$customer_history_info) {
			trigger_error('Error: unable to find customer history: ' . $customer_history_id);
			return null;
		}

		$customer_info = $this->model_customer_customer->getCustomer($customer_id);

		if (!$customer_info) {
			trigger_error('Error: unable to find customer: ' . $customer_id);
			return null;
		}

		$store_info = array_merge(
			$this->model_setting_setting->getSetting("config", $customer_info['store_id']),
			$this->model_setting_store->getStore($customer_info['store_id'])
		);

		$language_info = $this->model_localisation_language->getLanguage($customer_info['language_id']);

		if ($language_info) {
			$language_code = $language_info['code'];
		} else {
			$language_code = $store_info['config_language'];
		}

		$language = new Language($language_code);
		$language->load($language_code);
		$language->load('extension/module/emailtemplate/customer');

		$template_load = array(
			'key' => 'admin.customer_history',
			'customer_id' => $customer_info['customer_id'],
			'customer_group_id' => $customer_info['customer_group_id'],
			'language_id' => $customer_info['language_id'],
			'store_id' => $customer_info['store_id'],
		);

		$template = $this->model_extension_module_emailtemplate->load($template_load);

		if ($template) {
			$template->addData($customer_info);

			$affiliate_info = $this->model_customer_customer->getAffiliate($customer_info['customer_id']);

			if ($affiliate_info) {
				$template->addData($affiliate_info, 'affiliate');
			}

			$template->data['date_added'] = date($this->language->get('date_format_short'), strtotime($customer_history_info['date_added']));

			$template->data['comment'] = html_entity_decode($comment, ENT_QUOTES, 'UTF-8');

			$template_data['account_login'] = $template->data['store_url'] . 'index.php?route=account/login';

			if ($this->language->get('button_account_login') && $this->language->get('button_account_login') != 'button_account_login') {
				$template->data['account_login_text'] = $template->data['button_account_login'];
			} else {
				$template->data['account_login_text'] = $template_data['account_login'];
			}

			if (!empty($args[0]['custom_field'])) {
				$this->load->model('account/custom_field');

				$custom_fields = $this->model_customer_custom_field->getCustomFields($customer_info['customer_group_id']);

				foreach ($custom_fields as $custom_field) {
					if (isset($args[0]['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
						$custom_field_key = 'custom_field_' . ($custom_field['location'] != 'account' ? $custom_field['location'] . '_' : '') . $custom_field['custom_field_id'];
						$custom_field_value = $args[0]['custom_field'][$custom_field['location']][$custom_field['custom_field_id']];

						$template->data[$custom_field_key . '_name'] = $custom_field['name'];
						$template->data[$custom_field_key . '_value'] = '';

						if ($custom_field['custom_field_value']) {
							foreach ($custom_field['custom_field_value'] as $custom_field_value_info) {
								if (is_array($custom_field_value)) {
									if (in_array($custom_field_value_info['custom_field_value_id'], $custom_field_value)) {
										$template->data[$custom_field_key . '_value_' . $custom_field_value_info['custom_field_value_id']] = $custom_field_value_info['name'];
									}
								} else {
									if ($custom_field_value_info['custom_field_value_id'] == $custom_field_value) {
										$template->data[$custom_field_key . '_value'] = $custom_field_value_info['name'];
									}
								}
							}
						} else {
							$template->data[$custom_field_key . '_value'] = $custom_field_value;
						}
					}
				}
			}

			$mail = new Mail(isset($store_info['config_mail_engine']) ? $store_info['config_mail_engine'] : $this->config->get('config_mail_engine'));
			$mail->parameter = isset($store_info['config_mail_parameter']) ? $store_info['config_mail_parameter'] : $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = isset($store_info['config_mail_smtp_hostname']) ? $store_info['config_mail_smtp_hostname'] : $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = isset($store_info['config_mail_smtp_username']) ? $store_info['config_mail_smtp_username'] : $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode((isset($store_info['config_mail_smtp_password']) ? $store_info['config_mail_smtp_password'] : $this->config->get('config_mail_smtp_password')), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = isset($store_info['config_mail_smtp_port']) ? $store_info['config_mail_smtp_port'] : $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = isset($store_info['config_mail_smtp_timeout']) ? $store_info['config_mail_smtp_timeout'] : $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($customer_info['email']);
			$mail->setFrom($store_info['config_email']);
			$mail->setSender(html_entity_decode($store_info['config_name'], ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode($store_info['config_name'], ENT_QUOTES, 'UTF-8'));

			$template->addData($template_data);

			$template->hook($mail);

			$mail->send();

			$this->model_extension_module_emailtemplate->sent();
		}
	}
}	