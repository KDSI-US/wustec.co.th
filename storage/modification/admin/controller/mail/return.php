<?php
/* This file is under Git Control by KDSI. */
class ControllerMailReturn extends Controller {
	public function index($route, $args, $output) {
		if (isset($args[0])) {
			$return_id = $args[0];
		} else {
			$return_id = '';
		}
		
		if (isset($args[1])) {
			$return_status_id = $args[1];
		} else {
			$return_status_id = '';
		}		
		
		if (isset($args[2])) {
			$comment = $args[2];
		} else {
			$comment = '';
		}
		
		if (isset($args[3])) {
			$notify = $args[3];
		} else {
			$notify = '';
		}		
		
		if ($notify) {
			$this->load->model('sale/return');
			
			$return_info = $this->model_sale_return->getReturn($return_id);
			
			if ($return_info) {
				$this->load->language('mail/return');

				$data['return_id'] = $return_id;
				$data['date_added'] = date($this->language->get('date_format_short'), strtotime($return_info['date_modified']));
				$data['return_status'] = $return_info['return_status'];
				$data['comment'] = strip_tags(html_entity_decode($comment, ENT_QUOTES, 'UTF-8'));


				// Prepare mail: admin.return_history
				$this->load->model('extension/module/emailtemplate');
				$this->load->model('sale/order');

				$order_info = $this->model_sale_order->getOrder($return_info['order_id']);

                $template_load = array(
                    'key' => 'admin.return_history',
                    'customer_id' => $return_info['customer_id']
                );

                if (isset($order_info['store_id'])) {
                    $template_load['store_id'] = $order_info['store_id'];
                    $template_load['payment_method'] = $order_info['payment_code'];
                    $template_load['shipping_method'] = $order_info['shipping_code'];
                }

                if (!empty($this->request->post['emailtemplate_id'])) {
                    $template_load['emailtemplate_id'] = $this->request->post['emailtemplate_id'];
                }

                if (isset($order_info['language_id'])) {
					$language_id = $order_info['language_id'];
				} elseif (!empty($return_info['customer_id'])) {
				    $this->load->model('customer/customer');

		            $customer_info = $this->model_customer_customer->getCustomer($return_info['customer_id']);

                    $language_id = $customer_info['language_id'];
				} else {
				    $language_id = $this->config->get('config_language_id');
				}

				$template_load['language_id'] = $language_id;

				$template = $this->model_extension_module_emailtemplate->load($template_load);

                if ($template) {
                    $this->load->model('localisation/language');

                    $language_info = $this->model_localisation_language->getLanguage($language_id);

                    if ($language_info) {
                        $language_code = $language_info['code'];
                    } else {
                        $language_code = $this->config->get('config_language');
                    }

                    $language = new Language($language_code);
                    $language->load($language_code);
                    $language->load('mail/return');
                    $language->load('extension/module/emailtemplate/return');

                    $template->addData($return_info);

                    if ($return_info['product_id']) {
                        $this->load->model('catalog/product');

                        $product_info = $this->model_catalog_product->getProduct($return_info['product_id']);

                        $template->addData($product_info, 'product');
                    }

                    if ($order_info) {
                        $template->addData($order_info, 'order');

                        $template->data['order_id'] = $order_info['order_id'];

                        $template->data['order_total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value']);

                        $template->data['return_link'] = $order_info['store_url'] . 'index.php?route=account/return/info&return_id=' . $return_id;
                    } else {
                        $template->data['return_link'] = HTTP_CATALOG . 'index.php?route=account/return/info&return_id=' . $return_id;
                    }

                    if (!empty($template->data['button_return_link'])) {
                        $template->data['return_link_text'] = $template->data['button_return_link'];
                    } else {
                        $template->data['return_link_text'] = $template->data['return_link'];
                    }

                    $return_reason_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "return_reason WHERE return_reason_id = '" . (int)$return_info['return_reason_id'] . "' AND language_id = '" . (int)$language_id . "'");

                    if ($return_reason_query->row) {
                        $template->data['reason'] = $return_reason_query->row['name'];
                    }

                    $return_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "return_status WHERE return_status_id = '" . (int)$return_info['return_status_id'] . "' AND language_id = '" . (int)$language_id . "'");

                    if ($return_status_query->row) {
                        $template->data['return_status'] = $return_status_query->row['name'];
                    }

                    $template->data['return_id'] = $return_id;

                    $template->data['date_added'] = date($language->get('date_format_short'), strtotime($return_info['date_added']));

                    $template->data['comment'] = (!empty($comment)) ? (strcmp($comment, strip_tags($html_str = html_entity_decode($comment, ENT_QUOTES, 'UTF-8'))) == 0) ? nl2br($comment) : $html_str : '';

                    $template->data['opened'] = $return_info['opened'] ? $language->get('text_yes') : $language->get('text_no');

                    $template->data['text_subject'] = sprintf($language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'), $return_id);
				    // Prepared mail: admin.return_history
                }
			
				$mail = new Mail($this->config->get('config_mail_engine'));
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

				$mail->setTo($return_info['email']);
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
				$mail->setSubject(sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'), $return_id));
				$mail->setText($this->load->view('mail/return', $data));
				
				// Send mail: admin.return_history
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