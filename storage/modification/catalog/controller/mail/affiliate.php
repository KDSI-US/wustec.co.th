<?php
/* This file is under Git Control by KDSI. */
class ControllerMailAffiliate extends Controller {
	public function index(&$route, &$args, &$output) {
		$this->load->language('mail/affiliate');
        
		$data['text_welcome'] = sprintf($this->language->get('text_welcome'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
		$data['text_login'] = $this->language->get('text_login');
		$data['text_approval'] = $this->language->get('text_approval');
		$data['text_service'] = $this->language->get('text_service');
		$data['text_thanks'] = $this->language->get('text_thanks');

		$this->load->model('account/customer_group');
		
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getGroupId();
		} else {
			$customer_group_id = $args[1]['customer_group_id'];
		}
		
		$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);
		
		if ($customer_group_info) {
			$data['approval'] = ($this->config->get('config_affiliate_approval') || $customer_group_info['approval']);
		} else {
			$data['approval'] = '';
		}		
		
		$data['login'] = $this->url->link('affiliate/login', '', true);
		$data['store'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');


		// Prepare mail: affiliate.register
		$this->load->model('account/customer');

		if ($this->customer && $this->customer->isLogged()) {
			$customer_id = $this->customer->getId();
		} else {
			$customer_id = $args[0];
		}

		$customer_info = $this->model_account_customer->getCustomer($customer_id);

		if (!$customer_info) {
			trigger_error('Unable to find customer: ' . $args[0]['customer_id']);
			return false;
		}

		$this->load->model('extension/module/emailtemplate');

		$template_load = array(
		    'key' => 'affiliate.register',
		    'customer_id' => $customer_info['customer_id'],
		    'customer_group_id' => $customer_info['customer_group_id'],
		    'language_id' => $customer_info['language_id'],
		    'store_id' => $customer_info['store_id']
        );

		$template = $this->model_extension_module_emailtemplate->load($template_load, $data);

        if ($template) {
            $template->addData($args[1], 'affiliate');

            $template->addData($customer_info, 'customer');

            $template->data['affiliate_login'] = $this->url->link('affiliate/login');

            if (!empty($template->data['button_affiliate_login'])) {
                $template->data['affiliate_login_text'] = $template->data['button_affiliate_login'];
            } else {
                $template->data['affiliate_login_text'] = $template->data['affiliate_login'];
            }

            if (!empty($args[0]['custom_field'])) {
                $this->load->model('account/custom_field');

                $custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

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

            $template->data['customer_group'] = $customer_group_info['name'];

            if ($customer_group_info && ($this->config->get('config_affiliate_approval') || $customer_group_info['approval'])) {
                $template->data['affiliate_text'] = $this->language->get('text_approval');
            } else {
                $template->data['affiliate_text'] = $this->language->get('text_login');
            }
            // Prepared mail: affiliate.register
        }

		$mail = new Mail($this->config->get('config_mail_engine'));
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

		if ($this->customer->isLogged()) {
			$mail->setTo($this->customer->getEmail());
		} else {
			$mail->setTo($args[1]['email']);
		}
		
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
		$mail->setSubject(sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8')));
		$mail->setText($this->load->view('mail/affiliate', $data));
		// Send mail: affiliate.register
		if ($template && $template->check()) {
		    $template->build();
		    $template->hook($mail);
        }

		$mail->send();

		$this->model_extension_module_emailtemplate->sent();
 	}
	
	public function alert(&$route, &$args, &$output) {
		// Send to main admin email if new affiliate email is enabled
		if (in_array('affiliate', (array)$this->config->get('config_mail_alert'))) {
			$this->load->language('mail/affiliate');
			
			$data['text_signup'] = $this->language->get('text_signup');
			$data['text_website'] = $this->language->get('text_website');
			$data['text_firstname'] = $this->language->get('text_firstname');
			$data['text_lastname'] = $this->language->get('text_lastname');
			$data['text_customer_group'] = $this->language->get('text_customer_group');
			$data['text_email'] = $this->language->get('text_email');
			$data['text_telephone'] = $this->language->get('text_telephone');
			
			if ($this->customer->isLogged()) {
				$customer_group_id = $this->customer->getGroupId();
			
				$data['firstname'] = $this->customer->getFirstName();
				$data['lastname'] = $this->customer->getLastName();
				$data['email'] = $this->customer->getEmail();
				$data['telephone'] = $this->customer->getTelephone();
			} else {	
				$customer_group_id = $args[1]['customer_group_id'];
				
				$data['firstname'] = $args[1]['firstname'];
				$data['lastname'] = $args[1]['lastname'];	
				$data['email'] = $args[1]['email'];
				$data['telephone'] = $args[1]['telephone'];		
			}
			
			$data['website'] = html_entity_decode($args[1]['website'], ENT_QUOTES, 'UTF-8');
			$data['company'] = $args[1]['company'];
							
			$this->load->model('account/customer_group');

			$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);
			
			if ($customer_group_info) {
				$data['customer_group'] = $customer_group_info['name'];
			} else {
				$data['customer_group'] = '';
			}
			

		// Prepare mail: affiliate.register_admin
		$affiliate_info = $args[1];

		if ($this->customer && $this->customer->isLogged()) {
			$customer_id = $this->customer->getId();
		} else {
			$customer_id = $args[0];
		}

		$customer_info = $this->model_account_customer->getCustomer($customer_id);

		if (!$customer_info) {
			trigger_error('Unable to find customer: ' . $args[0]['customer_id']);
			return false;
		}

		// Load template
		$this->load->model('extension/module/emailtemplate');

		$template_load = array(
		    'key' => 'affiliate.register_admin',
		    'customer_id' => $customer_info['customer_id'],
		    'customer_group_id' => $customer_info['customer_group_id'],
		    'language_id' => $customer_info['language_id'],
		    'store_id' => $customer_info['store_id']
        );

		$template = $this->model_extension_module_emailtemplate->load($template_load, $data);

        if ($template) {
            // Add data to: affiliate.register_admin
            $template->addData($args[1], 'affiliate');

            $template->addData($customer_info, 'customer');

            $template->data['affiliate_login'] = $this->url->link('affiliate/login');

            if (!empty($template->data['button_affiliate_login'])) {
                $template->data['affiliate_login_text'] = $template->data['button_affiliate_login'];
            } else {
                $template->data['affiliate_login_text'] = $template->data['affiliate_login'];
            }

            if (!empty($args[0]['custom_field'])) {
                $this->load->model('account/custom_field');

                $custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

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

            $template->data['customer_group'] = $customer_group_info['name'];

            if (defined('HTTP_ADMIN')) {
                $admin_url = HTTP_ADMIN;
            } elseif ($this->config->get('config_ssl')) {
                $admin_url = $this->config->get('config_ssl') . 'admin/';
            } else {
                $admin_url = $this->config->get('config_url') . 'admin/';
            }

            if ($customer_group_info && ($this->config->get('config_affiliate_approval') || $customer_group_info['approval'])) {
                $template->data['subject'] = $template->data['text_approve_subject_admin'];

                $template->data['affiliate_link'] = $admin_url . 'index.php?route=customer/customer_approval&filter_type=affiliate&filter_email=' . $customer_info['email'];

                $template->data['status'] = $template->data['text_admin_approve'];
            } else {
                $template->data['subject'] = $template->data['text_subject_admin'];

                $template->data['affiliate_link'] = $admin_url . 'index.php?route=customer/customer/edit&customer_id=' . $affiliate_info['customer_id'];

                $template->data['status'] = $template->data[$affiliate_info['status'] ? 'text_enabled' : 'text_disabled'];
            }

            if (isset($template->data['button_affiliate_link']) && $template->data['button_affiliate_link'] != 'button_affiliate_link') {
                $template->data['affiliate_link_text'] =  $template->data['button_affiliate_link'];
            } else {
                $template->data['affiliate_link_text'] =  $template->data['affiliate_link'];
            }
		    // Prepared mail: affiliate.register_admin
		}

			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($this->config->get('config_email'));
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));			
			$mail->setSubject(html_entity_decode($this->language->get('text_new_affiliate'), ENT_QUOTES, 'UTF-8'));
			$mail->setText($this->load->view('mail/affiliate_alert', $data));
			// Send mail: affiliate.register_admin
			if ($template && $template->check()) {
			    $template->build();
			    $template->hook($mail);
            }

			$mail->send();

			$this->model_extension_module_emailtemplate->sent();

			// Send to additional alert emails if new affiliate email is enabled
			$emails = explode(',', $this->config->get('config_mail_alert_email'));

			foreach ($emails as $email) {
				if (utf8_strlen($email) > 0 && filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$mail->setTo($email);
					$mail->send();
				}
			}
		}		
	}
}		