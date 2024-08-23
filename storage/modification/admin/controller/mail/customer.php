<?php
/* This file is under Git Control by KDSI. */
class ControllerMailCustomer extends Controller {
	public function approve(&$route, &$args, &$output) {
		$this->load->model('customer/customer');

		$customer_info = $this->model_customer_customer->getCustomer($args[0]);

		if ($customer_info) {
			$this->load->model('setting/store');

			$store_info = $this->model_setting_store->getStore($customer_info['store_id']);

			if ($store_info) {
				$store_name = html_entity_decode($store_info['name'], ENT_QUOTES, 'UTF-8');
				$store_url = $store_info['url'];
			} else {
				$store_name = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
				$store_url = HTTP_CATALOG;
			}

			$this->load->model('localisation/language');

			$language_info = $this->model_localisation_language->getLanguage($customer_info['language_id']);

			if ($language_info) {
				$language_code = $language_info['code'];
			} else {
				$language_code = $this->config->get('config_language');
			}

			$language = new Language($language_code);
			$language->load($language_code);
			$language->load('mail/customer_approve');

			$subject = sprintf($language->get('text_subject'), $store_name);

			$data['text_welcome'] = sprintf($language->get('text_welcome'), $store_name);

			$data['login'] = $store_url . 'index.php?route=account/login';
			$data['store'] = $store_name;


			// Prepare mail: admin.customer_approve
			$this->load->model('extension/module/emailtemplate');

			$template_load = array(
				'key' => 'admin.customer_approve',
				'customer_id' => $customer_info['customer_id'],
				'customer_group_id' => $customer_info['customer_group_id'],
				'language_id' => $customer_info['language_id'],
				'store_id' => $customer_info['store_id']
			);

			$template = $this->model_extension_module_emailtemplate->load($template_load);

            if ($template) {
                $template->addData($customer_info);

                if (!empty($args[0]['custom_field'])) {
                    $this->load->model('account/custom_field');

                    $custom_fields = $this->model_account_custom_field->getCustomFields($customer_info['customer_group_id']);

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

                $template->data['account_login'] = $store_url . '&amp;email=' . $customer_info['email'];

                if (!empty($template->data['button_account_login'])) {
                    $template->data['account_login_text'] = $template->data['button_account_login'];
                } else {
                    $template->data['account_login_text'] = $template->data['account_login'];
                }
			    // Prepared mail: admin.customer_approve
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
			$mail->setSender($store_name);
			$mail->setSubject($subject);
			$mail->setText($this->load->view('mail/customer_approve', $data));
			
		// Send mail: admin.customer_approve
		if ($template && $template->check()) {
		    $template->build();
		    $template->hook($mail);

            $mail->send();

            $this->model_extension_module_emailtemplate->sent();
        }
		}
	}

	public function deny(&$route, &$args, &$output) {
		$this->load->model('customer/customer');

		$customer_info = $this->model_customer_customer->getCustomer($args[0]);

		if ($customer_info) {
			$this->load->model('setting/store');

			$store_info = $this->model_setting_store->getStore($customer_info['store_id']);

			if ($store_info) {
				$store_name = html_entity_decode($store_info['name'], ENT_QUOTES, 'UTF-8');
				$store_url = $store_info['url'];
			} else {
				$store_name = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
				$store_url = HTTP_CATALOG;
			}

			$this->load->model('localisation/language');

			$language_info = $this->model_localisation_language->getLanguage($customer_info['language_id']);

			if ($language_info) {
				$language_code = $language_info['code'];
			} else {
				$language_code = $this->config->get('config_language');
			}

			$language = new Language($language_code);
			$language->load($language_code);
			$language->load('mail/customer_deny');

			$subject = sprintf($language->get('text_subject'), $store_name);

			$data['text_welcome'] = sprintf($language->get('text_welcome'), $store_name);

			$data['contact'] = $store_url . 'index.php?route=information/contact';
			$data['store'] = $store_name;


			// Prepare mail: admin.customer_deny
			$this->load->model('extension/module/emailtemplate');

			$template_load = array(
				'key' => 'admin.customer_deny',
				'customer_id' => $customer_info['customer_id'],
				'customer_group_id' => $customer_info['customer_group_id'],
				'language_id' => $customer_info['language_id'],
				'store_id' => $customer_info['store_id']
			);

			$template = $this->model_extension_module_emailtemplate->load($template_load);

            if ($template) {
			    $template->addData($customer_info);
			    // Prepared mail: admin.customer_deny
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
			$mail->setSender($store_name);
			$mail->setSubject($subject);
			$mail->setText($this->load->view('mail/customer_deny', $data));
			
		// Send mail: admin.customer_deny
		if ($template && $template->check()) {
		    $template->build();
            $template->hook($mail);

            $mail->send();

            $this->model_extension_module_emailtemplate->sent();
        }
		}
	}
}