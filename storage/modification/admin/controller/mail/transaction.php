<?php
/* This file is under Git Control by KDSI. */
class ControllerMailTransaction extends Controller {
	public function index($route, $args, $output) {
		if (isset($args[0])) {
			$customer_id = $args[0];
		} else {
			$customer_id = '';
		}
		
		if (isset($args[1])) {
			$description = $args[1];
		} else {
			$description = '';
		}		
		
		if (isset($args[2])) {
			$amount = $args[2];
		} else {
			$amount = '';
		}
		
		if (isset($args[3])) {
			$order_id = $args[3];
		} else {
			$order_id = '';
		}
			
		$this->load->model('customer/customer');
						
		$customer_info = $this->model_customer_customer->getCustomer($customer_id);

		if ($customer_info) {
			$this->load->language('mail/transaction');

			$this->load->model('setting/store');

			$store_info = $this->model_setting_store->getStore($customer_info['store_id']);

			if ($store_info) {
				$store_name = $store_info['name'];
			} else {
				$store_name = $this->config->get('config_name');
			}

			$data['text_received'] = sprintf($this->language->get('text_received'), $this->currency->format($amount, $this->config->get('config_currency')));
			$data['text_total'] = sprintf($this->language->get('text_total'), $this->currency->format($this->model_customer_customer->getTransactionTotal($customer_id), $this->config->get('config_currency')));
			

			// Prepare mail: admin.customer_transaction
			$this->load->model('customer/customer');
			$this->load->model('sale/order');
			$this->load->model('extension/module/emailtemplate');

			$template_load = array(
			    'key' =>'admin.customer_transaction',
			    'customer_id' => $customer_info['customer_id'],
				'customer_group_id' => $customer_info['customer_group_id'],
				'language_id' => $customer_info['language_id'],
				'store_id' => $customer_info['store_id']
            );

			$template = $this->model_extension_module_emailtemplate->load($template_load);

            if ($template) {
                $template->data['amount'] = $amount;
                $template->data['description'] = $description;

                $template->addData($customer_info, 'customer');

                if ($order_id) {
                    $order_info = $this->model_sale_order->getOrder($order_id);

                    $template->addData($order_info, 'order');
                }

                $affiliate_info = $this->model_customer_customer->getAffiliate($customer_info['customer_id']);

                if ($affiliate_info) {
                    $template->addData($affiliate_info, 'affiliate');
                }

                $template->data['customer_transaction_received'] = $this->currency->format($amount, $this->config->get('config_currency'));
                $template->data['customer_transaction_total'] = $this->currency->format($this->model_customer_customer->getTransactionTotal($customer_id), $this->config->get('config_currency'));

                $template->data['transaction_received'] = sprintf($template->data['text_transaction_received'], $template->data['customer_transaction_received']);
                $template->data['transaction_total'] = sprintf($template->data['text_transaction_total'], $template->data['customer_transaction_total']);
			    // Prepared mail: admin.customer_transaction
            }
		
			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($customer_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8')));
			$mail->setText($this->load->view('mail/transaction', $data));
			
			// Send mail: admin.customer_transaction
            if ($template && $template->check()) {
                $template->build();
			    $template->hook($mail);

                $mail->send();

                $this->model_extension_module_emailtemplate->sent();
            }
		}
	}		
}	
