<?php

class ControllerExtensionModuleEmailTemplateEvent extends Controller
{
	public function afterEvent(&$route, &$args, &$output)
	{
		$query = "SELECT * FROM `" . DB_PREFIX . "event` e INNER JOIN `" . DB_PREFIX . "emailtemplate_event` ee ON(ee.event_id = e.event_id) WHERE e.trigger LIKE '%". $this->db->escape(trim($route, '/')) ."/after' LIMIT 1";

		$result = $this->db->query($query);

		if (!$result->rows) return null;

		$this->load->model('extension/module/emailtemplate');

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
				$this->load->model('checkout/order');

				$order_info = $this->model_checkout_order->getOrder($args['order_id']);

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

			if ($this->customer && $this->customer->isLogged()) {
				$customer_id = $this->customer->getId();
			} elseif (isset($args['customer_id'])) {
				$customer_id = $args['customer_id'];
			}

			if ($customer_id) {
				$this->load->model('account/customer');

				$customer_info = $this->model_account_customer->getCustomer($customer_id);

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

			if (!$template) {
				continue;
			}

			if ($args && is_array($args)) {
				foreach($args as $i => $arg) {
					if (is_array($arg)) {
						$template->addData($arg);
					} elseif (is_string($i)) {
						$template->addData($i, $arg);
					}
				}
			}

			if ($output) {
				$template->addData($output);
			}

			if ($this->customer && $this->customer->isLogged()) {
				$this->load->model('account/customer');

				$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

				$template->addData($customer_info, 'customer');
			} elseif (isset($args['customer_id'])) {
				$this->load->model('account/customer');

				$customer_info = $this->model_account_customer->getCustomer($args['customer_id']);

				$template->addData($customer_info, 'customer');
			}

			if (isset($args['order_id'])) {
				$this->load->model('checkout/order');

				$order_info = $this->model_checkout_order->getOrder($args['order_id']);

				$template->addData($order_info, 'order');
			}

			$template->build();

			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			// Set mail defaults
			$mail->setTo($template->data['store_email']);
			$mail->setFrom($template->data['store_email']);
			$mail->setSender($template->data['store_name']);
			$mail->setSubject($template->data['store_name']);

			$template->hook($mail);

			$mail->send();

			$this->model_extension_module_emailtemplate->sent($template);
		}
	}

	public function returnCreate(&$route, &$args, &$output)
	{
		$this->load->model('account/return');

		$return_id = $output;

		$return_info = $this->model_account_return->getReturn($return_id);

		if (!$return_info) {
		    $msg = 'Unable to find return - ';
		    if ($return_id) {
                $msg .= ' using return_id=\''. $return_id . '\'.';
            } else {
		        $msg .= ' check catalog/model/account/return.php addReturn() is returning the return_id.';
            }
			trigger_error($msg);
			return null;
		}

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($return_info['order_id']);

		$this->load->language('mail/return');

		$this->load->model('extension/module/emailtemplate');

        $return_data = $return_info;

        if ($order_info && $order_info['email'] == $return_info['email']) {
            $return_data['order'] = $order_info;

            $return_data['order_date'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));

            if ($order_info['invoice_no']) {
                $return_data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
            } else {
                $return_data['invoice_no'] = '';
            }

            $return_data['order_date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));

            $return_data['order_total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value']);

            $return_data['order_payment_address'] = $this->model_extension_module_emailtemplate->formatAddress($order_info, 'payment', $order_info['payment_address_format']);
            $return_data['order_shipping_address'] = $this->model_extension_module_emailtemplate->formatAddress($order_info, 'shipping', $order_info['shipping_address_format']);
        }

        if ($this->customer && $this->customer->isLogged() && $this->customer->getEmail() == $return_info['email']) {
            $this->load->model('account/customer');

            $customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

            if ($customer_info) {
                $return_data['customer'] = $customer_info;
            }
        }

        $return_data['opened'] = $return_info['opened'] ? $this->language->get('text_yes') : $this->language->get('text_no');

        $return_data['return_date'] = date($this->language->get('date_format_short'), strtotime($return_info['date_ordered']));

        $return_data['comment'] = $return_info['comment'] ? nl2br($return_info['comment']) : '';

		$template_load = array(
			'key' => 'order.return'
		);

		if ($order_info && $order_info['email'] == $return_info['email']) {
			$template_load['customer_id'] = $order_info['customer_id'];
			$template_load['customer_group_id'] = $order_info['customer_group_id'];
			$template_load['store_id'] = $order_info['store_id'];
			$template_load['language_id'] = $order_info['language_id'];
		}

		$template = $this->model_extension_module_emailtemplate->load($template_load);

		if ($template) {
			$template->addData($return_data);

			if (defined('HTTP_ADMIN')) {
				$admin_url = HTTP_ADMIN;
			} else {
				$admin_url = HTTPS_SERVER . 'admin/';
			}

			$template->data['return_link'] = $admin_url . 'index.php?route=' . rawurlencode('sale/return/info') . '&return_id=' . $return_info['return_id'];

			if (!empty($template->data['button_return_link'])) {
				$template->data['return_link_text'] = $template->data['button_return_link'];
			} else {
				$template->data['return_link_text'] = $template->data['return_link'];
			}

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
			$mail->setReplyTo($return_info['email']);
			$mail->setSender($return_info['firstname'] . ' ' . $return_info['lastname']);

			$template->hook($mail);

			$mail->send();

			$this->model_extension_module_emailtemplate->sent($template);
		}

        // Notify customer?
        $template_load['key'] = 'order.return_customer';

        $template = $this->model_extension_module_emailtemplate->load($template_load);

        if ($template) {
            $template->addData($return_data);

            $template->build();

            $mail = new Mail($this->config->get('config_mail_engine'));
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
            $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

            $mail->setTo($return_info['email']);
            $mail->setFrom($template->data['store_email']);
            $mail->setSender($template->data['store_name']);
            $mail->setSubject($template->data['store_name']);

            $template->hook($mail);

            $mail->send();

            $this->model_extension_module_emailtemplate->sent($template);
        }
	}
}
