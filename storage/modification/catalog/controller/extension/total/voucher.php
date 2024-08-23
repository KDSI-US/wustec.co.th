<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionTotalVoucher extends Controller {
	public function index() {
		if ($this->config->get('total_voucher_status')) {
			$this->load->language('extension/total/voucher');

			if (isset($this->session->data['voucher'])) {
				$data['voucher'] = $this->session->data['voucher'];
			} else {
				$data['voucher'] = '';
			}

			return $this->load->view('extension/total/voucher', $data);
		}
	}

	public function voucher() {
		$this->load->language('extension/total/voucher');

		$json = array();

		$this->load->model('extension/total/voucher');

		if (isset($this->request->post['voucher'])) {
			$voucher = $this->request->post['voucher'];
		} else {
			$voucher = '';
		}

		$voucher_info = $this->model_extension_total_voucher->getVoucher($voucher);

		if (empty($this->request->post['voucher'])) {
			$json['error'] = $this->language->get('error_empty');
		} elseif ($voucher_info) {
			$this->session->data['voucher'] = $this->request->post['voucher'];

			$this->session->data['success'] = $this->language->get('text_success');

			$json['redirect'] = $this->url->link('checkout/cart');
		} else {
			$json['error'] = $this->language->get('error_voucher');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function send($route, $args, $output) {
		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($args[0]);

		// If order status in the complete range create any vouchers that where in the order need to be made available.
		if (in_array($order_info['order_status_id'], $this->config->get('config_complete_status'))) {
			$voucher_query = $this->db->query("SELECT *, vtd.name AS theme FROM `" . DB_PREFIX . "voucher` v LEFT JOIN " . DB_PREFIX . "voucher_theme vt ON (v.voucher_theme_id = vt.voucher_theme_id) LEFT JOIN " . DB_PREFIX . "voucher_theme_description vtd ON (vt.voucher_theme_id = vtd.voucher_theme_id) WHERE v.order_id = '" . (int)$order_info['order_id'] . "' AND vtd.language_id = '" . (int)$order_info['language_id'] . "'");

			if ($voucher_query->num_rows) {
				// Send out any gift voucher mails
				$language = new Language($order_info['language_code']);
				$language->load($order_info['language_code']);
				$language->load('mail/voucher');

				foreach ($voucher_query->rows as $voucher) {
					// HTML Mail
					$data = array();

					$data['title'] = sprintf($language->get('text_subject'), $voucher['from_name']);

					$data['text_greeting'] = sprintf($language->get('text_greeting'), $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']));
					$data['text_from'] = sprintf($language->get('text_from'), $voucher['from_name']);
					$data['text_message'] = $language->get('text_message');
					$data['text_redeem'] = sprintf($language->get('text_redeem'), $voucher['code']);
					$data['text_footer'] = $language->get('text_footer');

					if (is_file(DIR_IMAGE . $voucher['image'])) {
						$data['image'] = $this->config->get('config_url') . 'image/' . $voucher['image'];
					} else {
						$data['image'] = '';
					}

					$data['store_name'] = $order_info['store_name'];
					$data['store_url'] = $order_info['store_url'];
					$data['message'] = nl2br($voucher['message']);

	    			// Prepare mail: order.voucher
					$this->load->model('extension/module/emailtemplate');

					$template_load = array(
					    'key' => 'order.voucher',
					    'customer_id' => $order_info['customer_id'],
					    'customer_group_id' => $order_info['customer_group_id'],
					    'language_id' => $order_info['language_id'],
					    'store_id' => $order_info['store_id'],
                        'payment_method' => $order_info['payment_code'],
                        'shipping_method' => $order_info['shipping_code']
                    );

					$template = $this->model_extension_module_emailtemplate->load($template_load);

                    if ($template) {
                        $template->addData($voucher);

                        $template_data = $data;
                        unset($template_data['store_name']);
                        unset($template_data['store_url']);
                        $template->addData($template_data);

                        $template->addData($order_info, 'order');

                        $template->data['store_id'] = $order_info['store_id'];

                        $template->data['language_id'] = $order_info['language_id'];

                        $template->data['amount'] = $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']);

                        $template->data['order_total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value']);

                        if ($voucher['image'] && file_exists(DIR_IMAGE . $voucher['image'])) {
                            $template->data['image'] = $template->data['store_url'] . 'image/' . $voucher['image'];

                            list($template->data['image_width'], $template->data['image_height']) = getimagesize(DIR_IMAGE . $voucher['image']);
                        }

                        $template->data['text_heading'] = sprintf($template->data['text_heading'], $template->data['store_name']);
                        $template->data['text_subject'] = sprintf($template->data['text_subject'], html_entity_decode($template->data['store_name'], ENT_QUOTES, 'UTF-8'));

                        $template->data['text_greeting'] = sprintf($this->language->get('text_greeting'), $this->currency->format($voucher_info['amount'], $this->config->get('config_currency')));
						$template->data['text_from'] = sprintf($this->language->get('text_from'), $voucher_info['from_name']);

                        $template->data['voucher_url'] = $template->data['store_url'];

                        if (!empty($template->data['button_voucher_url'])) {
                            $template->data['voucher_url_text'] = $template->data['button_voucher_url'];
                        } else {
                            $template->data['voucher_url_text'] = $template->data['voucher_url'];
                        }
					    // Prepared mail: order.voucher
		

					$mail = new Mail($this->config->get('config_mail_engine'));
					$mail->parameter = $this->config->get('config_mail_parameter');
					$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
					$mail->smtp_username = $this->config->get('config_mail_smtp_username');
					$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
					$mail->smtp_port = $this->config->get('config_mail_smtp_port');
					$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

					$mail->setTo($voucher['to_email']);
					$mail->setFrom($this->config->get('config_email'));
					$mail->setSender(html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
					$mail->setSubject(html_entity_decode(sprintf($language->get('text_subject'), $voucher['from_name']), ENT_QUOTES, 'UTF-8'));
					if (empty($template)) $mail->setHtml($this->load->view('mail/voucher', $data));
					// Send mail: order.voucher
                        if ($template && $template->check()) {
                        	$template->build();
                        	$template->hook($mail);

                        	$mail->send();

                        	$this->model_extension_module_emailtemplate->sent();
						}
                    }
				}
			}
		}
	}
}
