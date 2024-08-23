<?php
/* This file is under Git Control by KDSI. */
class ControllerMailRegister extends Controller {
	public function index(&$route, &$args, &$output) {
        if ($this->request->get['route'] == 'affiliate/register') {
            return null;
        }
        
		$this->load->language('mail/register');

		$data['text_welcome'] = sprintf($this->language->get('text_welcome'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
		$data['text_login'] = $this->language->get('text_login');
		$data['text_approval'] = $this->language->get('text_approval');
		$data['text_service'] = $this->language->get('text_service');
		$data['text_thanks'] = $this->language->get('text_thanks');

		$this->load->model('account/customer_group');
			
		if (isset($args[0]['customer_group_id'])) {
			$customer_group_id = $args[0]['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}
					
		$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);
		
		if ($customer_group_info) {
			$data['approval'] = $customer_group_info['approval'];
		} else {
			$data['approval'] = '';
		}
			
		$data['login'] = $this->url->link('account/login', '', true);		
		$data['store'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');

		$modulestatus=$this->config->get('registermanager_status');
		if(!empty($modulestatus)) {
			$tmdmessage = $this->config->get('registermanager_email')[$this->config->get('config_language_id')]['emailmessage'];
			$tmdsubject= $this->config->get('registermanager_email')[$this->config->get('config_language_id')]['subject'];
			
			if(!empty($tmdmessage)&& !empty($tmdsubject)) {
				$this->load->model('account/customer');
				$this->load->model('localisation/country');
				$this->load->model('localisation/zone');
				$this->load->model('account/address');
				
				$customerinfos = $args[0];
					
				$countryinfos  = $this->model_localisation_country->getCountry($customerinfos['country_id']);
				$zoneinfos     = $this->model_localisation_zone->getZone($customerinfos['zone_id']);
				
				$find=array(
					'{firstname}',
					'{lastname}',
					'{email}',
					'{telephone}',
					'{address1}',
					'{address2}',
					'{citys}',
					'{postcodes}',
					'{countryname}',
					'{zonename}',
				);
				
				$address1 = '';
				$address2 = '';
				$citys = '';
				$postcodes = '';
				$countryname = '';
				$zonename = '';
				
				if(isset($customerinfos['address_1'])) $address1 = $customerinfos['address_1'];
				if(isset($customerinfos['address_2'])) $address2 = $customerinfos['address_2'];
				if(isset($customerinfos['city']))      $citys = $customerinfos['city'];
				if(isset($customerinfos['postcode']))  $postcodes = $customerinfos['postcode'];
				if(isset($countryinfos['name']))       $countryname = $countryinfos['name'];
				if(isset($zoneinfos['name'])) 		   $zonename = $zoneinfos['name'];
				
				$replace = array(
					'firstname'		=> $customerinfos['firstname'],
					'lastname'		=> $customerinfos['lastname'],
					'email'			=> $customerinfos['email'],
					'telephone'		=> $customerinfos['telephone'],
					'address1'		=> $address1,
					'address2'		=> $address2,
					'citys'			=> $citys,
					'postcodes'		=> $postcodes,
					'countryname'	=> $countryname,
					'zonename'		=> $zonename
				);
				
				$subject = html_entity_decode(str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $tmdsubject)))));
				$message = html_entity_decode(str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $tmdmessage)))));
				
			} else {
			
				$subject=sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
				$message=$this->load->view('mail/register', $data);
				
			}
		}
			


			// Prepare mail: customer.register
			$this->load->model('account/customer');
			$this->load->model('extension/module/emailtemplate');

			$this->load->language('extension/module/emailtemplate/customer');

			if (!empty($args[0]['customer_id'])) {
				$customer_info = $this->model_account_customer->getCustomer($args[0]['customer_id']);
			} else {
				$customer_info = $this->model_account_customer->getCustomerByEmail($args[0]['email']);
			}

			if (empty($customer_info)) {
                trigger_error('Unable to find customer by email: ' . $args[0]['email']);
                return false;
            }

			$template_load = array(
			    'key' => 'customer.register',
			    'customer_id' => $customer_info['customer_id'],
			    'customer_group_id' => $customer_info['customer_group_id'],
			    'language_id' => $customer_info['language_id'],
			    'store_id' => $customer_info['store_id']
            );

			$template = $this->model_extension_module_emailtemplate->load($template_load);

            if ($template) {
                $template->addData($args[0]);

                $template->addData($customer_info, 'customer');

                $template->data['newsletter'] = $this->language->get((isset($args[0]['newsletter']) && $args[0]['newsletter'] == 1) ? 'text_yes' : 'text_no');

                $template->data['account_login'] = $this->url->link('account/login');

                if (!empty($template->data['button_account_login'])) {
                    $template->data['account_login_text'] = $template->data['button_account_login'];
                } else {
                    $template->data['account_login_text'] = $template->data['account_login'];
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

                if ($customer_group_info['approval']) {
                    $template->data['customer_text'] = $this->language->get('text_approval');
                } else {
                    $template->data['customer_text'] = $this->language->get('text_login');
                }

                if (!empty($customer_info['address_id'])) {
                    $this->load->model('account/address');

                    $address = $this->model_account_address->getAddress($customer_info['address_id']);

                    if ($address) {
                        $country = '';
                        $iso_code_2 = '';
                        $iso_code_3 = '';
                        $address_format = '';
                        $zone = '';
                        $zone_code = '';

                        if (!empty($address['country_id'])) {
                            $this->load->model('localisation/country');

                            $country_info = $this->model_localisation_country->getCountry($address['country_id']);

                            if ($country_info) {
                                $country = $country_info['name'];
                                $iso_code_2 = $country_info['iso_code_2'];
                                $iso_code_3 = $country_info['iso_code_3'];
                                $address_format = $country_info['address_format'];
                            }
                        }

                        if (!empty($address['zone_id'])) {
                            $this->load->model('localisation/zone');

                            $zone_info = $this->model_localisation_zone->getZone($address['zone_id']);

                            if ($zone_info) {
                                $zone = $zone_info['name'];
                                $zone_code = $zone_info['code'];
                            }
                        }

                        $address_data = array(
                            'address_id'     => $customer_info['address_id'],
                            'firstname'      => $address['firstname'],
                            'lastname'       => $address['lastname'],
                            'company'        => $address['company'],
                            'address_1'      => $address['address_1'],
                            'address_2'      => $address['address_2'],
                            'postcode'       => $address['postcode'],
                            'city'           => $address['city'],
                            'zone_id'        => $address['zone_id'],
                            'zone'           => $zone,
                            'zone_code'      => $zone_code,
                            'country_id'     => $address['country_id'],
                            'country'        => $country,
                            'iso_code_2'     => $iso_code_2,
                            'iso_code_3'     => $iso_code_3
                        );

                        $find = array();
                        $replace = array();

                        foreach(array_keys($address_data) as $key) {
                            $find[$key] = '{'.$key.'}';
                            $replace[$key] =  $address_data[$key];
                        }

                        if (isset($address['custom_field']) && is_array($address['custom_field'])) {
                            foreach ($address['custom_field'] as $custom_field_id => $custom_field) {
                                $find[] = '{custom_field_' . $custom_field_id . '}';
                                $replace[] = isset($custom_field['value']) ? $custom_field['value'] : '';
                            }
                        }

                        if (!$address_format) {
                            $address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
                        }

                        $template->data['address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $address_format))));
                        $template->data['address_zone'] = $zone;
                        $template->data['address_country'] = $country;
                    }
                }
			    // Prepared mail: customer.register
			}
	
		$mail = new Mail($this->config->get('config_mail_engine'));
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

		$mail->setTo($args[0]['email']);
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
		
			$mail->setSubject($subject);
			$modulestatus = $this->config->get('registermanager_email');
			if(!empty($modulestatus)) {
				$mail->setHtml($message);
			} else {
				$mail->setText($this->load->view('mail/register', $data));
			}
			
		// Send mail: customer.register
			if ($template && $template->check()) {
			    $template->build();
			    $template->hook($mail);

                $mail->send();

                $this->model_extension_module_emailtemplate->sent();
            } 
	}
	
	public function alert(&$route, &$args, &$output) {
		// Send to main admin email if new account email is enabled
		if (in_array('account', (array)$this->config->get('config_mail_alert'))) {
			$this->load->language('mail/register');
			
			$data['text_signup'] = $this->language->get('text_signup');
			$data['text_firstname'] = $this->language->get('text_firstname');
			$data['text_lastname'] = $this->language->get('text_lastname');
			$data['text_customer_group'] = $this->language->get('text_customer_group');
			$data['text_email'] = $this->language->get('text_email');
			$data['text_telephone'] = $this->language->get('text_telephone');
			
			$data['firstname'] = $args[0]['firstname'];
			$data['lastname'] = $args[0]['lastname'];
			
			$this->load->model('account/customer_group');
			
			if (isset($args[0]['customer_group_id'])) {
				$customer_group_id = $args[0]['customer_group_id'];
			} else {
				$customer_group_id = $this->config->get('config_customer_group_id');
			}
			
			$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);
			
			if ($customer_group_info) {
				$data['customer_group'] = $customer_group_info['name'];
			} else {
				$data['customer_group'] = '';
			}
			
			$data['email'] = $args[0]['email'];
			$data['telephone'] = $args[0]['telephone'];

		// Prepare mail: customer.register_admin
		$this->load->model('account/customer');
		$this->load->model('extension/module/emailtemplate');

		$this->load->language('extension/module/emailtemplate/customer');

		if (!empty($args[0]['customer_id'])) {
			$customer_info = $this->model_account_customer->getCustomer($args[0]['customer_id']);
		} else {
			$customer_info = $this->model_account_customer->getCustomerByEmail($args[0]['email']);
		}

		if (empty($customer_info)) {
            trigger_error('Unable to find customer by email: ' . $args[0]['email']);
            return false;
        }

		$template_load = array(
		    'key' => 'customer.register_admin',
            'customer_id' => $customer_info['customer_id'],
            'customer_group_id' => $customer_info['customer_group_id'],
            'language_id' => $customer_info['language_id'],
            'store_id' => $customer_info['store_id']
        );

		$template = $this->model_extension_module_emailtemplate->load($template_load);

        if ($template) {
            $template->addData($args[0]);

            $template->addData($customer_info, 'customer');

            $template->data['newsletter'] = $this->language->get((isset($args[0]['newsletter']) && $args[0]['newsletter'] == 1) ? 'text_yes' : 'text_no');

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

            if (!empty($args[0]['address_1'])) {
                $country = '';
                $iso_code_2 = '';
                $iso_code_3 = '';
                $address_format = '';
                $zone = '';
                $zone_code = '';

                if (!empty($args[0]['country_id'])) {
                    $this->load->model('localisation/country');

                    $country_info = $this->model_localisation_country->getCountry($args[0]['country_id']);

                    if ($country_info) {
                        $country = $country_info['name'];
                        $iso_code_2 = $country_info['iso_code_2'];
                        $iso_code_3 = $country_info['iso_code_3'];
                        $address_format = $country_info['address_format'];
                    }
                }

                if (!empty($args[0]['zone_id'])) {
                    $this->load->model('localisation/zone');

                    $zone_info = $this->model_localisation_zone->getZone($args[0]['zone_id']);

                    if ($zone_info) {
                        $zone = $zone_info['name'];
                        $zone_code = $zone_info['code'];
                    }
                }

                $address_data = array(
                    'firstname'      => $args[0]['firstname'],
                    'lastname'       => $args[0]['lastname'],
                    'company'        => $args[0]['company'],
                    'address_1'      => $args[0]['address_1'],
                    'address_2'      => $args[0]['address_2'],
                    'postcode'       => $args[0]['postcode'],
                    'city'           => $args[0]['city'],
                    'zone_id'        => $args[0]['zone_id'],
                    'zone'           => $zone,
                    'zone_code'      => $zone_code,
                    'country_id'     => $args[0]['country_id'],
                    'country'        => $country,
                    'iso_code_2'     => $iso_code_2,
                    'iso_code_3'     => $iso_code_3
                );

                $find = array();
                $replace = array();

                foreach(array_keys($address_data) as $key) {
                    $find[$key] = '{'.$key.'}';
                    $replace[$key] =  $address_data[$key];
                }

                if (isset($address['custom_field']) && is_array($address['custom_field'])) {
                    foreach ($address['custom_field'] as $custom_field_id => $custom_field) {
                        $find[] = '{custom_field_' . $custom_field_id . '}';
                        $replace[] = isset($custom_field['value']) ? $custom_field['value'] : '';
                    }
                }

                if (!$address_format) {
                    $address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
                }

                $template->data['address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $address_format))));
                $template->data['address_zone'] = $zone;
                $template->data['address_country'] = $country;
            }

            if (!empty($customer_info['address_id'])) {
                $this->load->model('account/address');

                $address = $this->model_account_address->getAddress($customer_info['address_id']);

                if ($address) {
                    $country = '';
                    $iso_code_2 = '';
                    $iso_code_3 = '';
                    $address_format = '';
                    $zone = '';
                    $zone_code = '';

                    if (!empty($address['country_id'])) {
                        $this->load->model('localisation/country');

                        $country_info = $this->model_localisation_country->getCountry($address['country_id']);

                        if ($country_info) {
                            $country = $country_info['name'];
                            $iso_code_2 = $country_info['iso_code_2'];
                            $iso_code_3 = $country_info['iso_code_3'];
                            $address_format = $country_info['address_format'];
                        }
                    }

                    if (!empty($address['zone_id'])) {
                        $this->load->model('localisation/zone');

                        $zone_info = $this->model_localisation_zone->getZone($address['zone_id']);

                        if ($zone_info) {
                            $zone = $zone_info['name'];
                            $zone_code = $zone_info['code'];
                        }
                    }

                    $address_data = array(
                        'address_id'     => $customer_info['address_id'],
                        'firstname'      => $address['firstname'],
                        'lastname'       => $address['lastname'],
                        'company'        => $address['company'],
                        'address_1'      => $address['address_1'],
                        'address_2'      => $address['address_2'],
                        'postcode'       => $address['postcode'],
                        'city'           => $address['city'],
                        'zone_id'        => $address['zone_id'],
                        'zone'           => $zone,
                        'zone_code'      => $zone_code,
                        'country_id'     => $address['country_id'],
                        'country'        => $country,
                        'iso_code_2'     => $iso_code_2,
                        'iso_code_3'     => $iso_code_3
                    );

                    $find = array();
                    $replace = array();

                    foreach(array_keys($address_data) as $key) {
                        $find[$key] = '{'.$key.'}';
                        $replace[$key] =  $address_data[$key];
                    }

                    if (isset($address['custom_field']) && is_array($address['custom_field'])) {
                        foreach ($address['custom_field'] as $custom_field_id => $custom_field) {
                            $find[] = '{custom_field_' . $custom_field_id . '}';
                            $replace[] = isset($custom_field['value']) ? $custom_field['value'] : '';
                        }
                    }

                    if (!$address_format) {
                        $address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
                    }

                    $template->data['address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $address_format))));
                    $template->data['address_zone'] = $zone;
                    $template->data['address_country'] = $country;
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

            if ($customer_group_info['approval']) {
                $template->data['customer_link'] = $admin_url . 'index.php?route=customer/customer_approval&filter_email=' . $customer_info['email'];

                $template->data['text_register_subject_admin'] = $template->data['text_register_approve_subject_admin'];

                $template->data['text_admin_heading'] = $template->data['text_customer_approve'];

                $template->data['status'] = $this->language->get('text_admin_approve');
            } else {
                $template->data['customer_link'] = $admin_url . 'index.php?route=customer/customer/edit&customer_id=' . $customer_info['customer_id'];

                $template->data['text_register_subject_admin'] = $template->data['text_register_subject_admin'];

                $template->data['text_admin_heading'] = $template->data['text_new_customer'];

                $template->data['status'] = $this->language->get($customer_info['status'] ? 'text_enabled' : 'text_disabled');
            }

            $template->data['text_customer_link'] = $template->data['text_customer_link'];

            if (isset($template->data['button_customer_link']) && $template->data['button_customer_link'] != 'button_customer_link') {
                $template->data['customer_link_text'] =  $template->data['button_customer_link'];
            } else {
                $template->data['customer_link_text'] =  $template->data['customer_link'];
            }
		    // Prepared mail: customer.register_admin
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
			$mail->setSubject(html_entity_decode($this->language->get('text_new_customer'), ENT_QUOTES, 'UTF-8'));
			$mail->setText($this->load->view('mail/register_alert', $data));
			// Send mail: customer.register_admin
			if ($template && $template->check()) {
			    $template->build();
			    $template->fetch();
			    $template->hook($mail);
            }

			$mail->send();

			$this->model_extension_module_emailtemplate->sent();

			// Send to additional alert emails if new account email is enabled
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