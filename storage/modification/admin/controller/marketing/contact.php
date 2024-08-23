<?php
/* This file is under Git Control by KDSI. */
class ControllerMarketingContact extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('marketing/contact');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['user_token'] = $this->session->data['user_token'];


        $this->document->addScript('view/javascript/summernote/summernote.js');
        $this->document->addScript('view/javascript/summernote/opencart.js');
        $this->document->addStyle('view/javascript/summernote/summernote.css');

		$this->load->model('localisation/language');
		$this->load->model('extension/module/emailtemplate');

		$this->load->language('extension/module/emailtemplate/newsletter');

        $templates = $this->model_extension_module_emailtemplate->getTemplates(array(
			'emailtemplate_key' => 'admin.newsletter'
		));

		$data['email_templates'] = array();

		foreach($templates as $row) {
			$data['email_templates'][] = array(
				'value' => $row['emailtemplate_id'],
				'label' => $row['emailtemplate_label'] . (!empty($row['emailtemplate_default']) ? ' (' . strip_tags($this->language->get('text_default')) . ')': '')
			);
		}

		$data['languages'] = $this->model_localisation_language->getLanguages();

        $data['user_token'] = $this->session->data['user_token'];

        $data['summernote_language'] = $this->language->get('summernote');
        if ($data['summernote_language'] == 'summernote') $data['summernote_language'] = 'en';
	
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('marketing/contact', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['cancel'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true);

		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();

		$this->load->model('customer/customer_group');

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/emailtemplate/marketing_contact', $data));
	}

	public function send() {
		$this->load->language('marketing/contact');

		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (!$this->user->hasPermission('modify', 'marketing/contact')) {
				$json['error']['warning'] = $this->language->get('error_permission');
			}

			if (empty($json['error'])) {
				$this->load->model('localisation/language');

				$languages = $this->model_localisation_language->getLanguages();

				foreach ($languages as $language) {
					if (empty($this->request->post['subject'][$language['language_id']])) {
						$json['error']['subject'][$language['language_id']] = $this->language->get('error_subject');
					}

					if (empty($this->request->post['message'][$language['language_id']]) || trim(strip_tags(html_entity_decode($this->request->post['message'][$language['language_id']], ENT_QUOTES, 'UTF-8'))) == '') {
						$json['error']['message'][$language['language_id']] = $this->language->get('error_message');
					}
				}

				if (!empty($json['error'])) {
					$json['error']['warning'] = $this->language->get('error_warning');
				}
			}

			if (!$json) {
				$this->load->model('setting/store');
				$this->load->model('setting/setting');
				$this->load->model('customer/customer');
				$this->load->model('sale/order');

				$store_info = $this->model_setting_store->getStore($this->request->post['store_id']);

				if ($store_info) {
					$store_name = $store_info['name'];
				} else {
					$store_name = $this->config->get('config_name');
				}

				$setting = $this->model_setting_setting->getSetting('config', $this->request->post['store_id']);

				$store_email = isset($setting['config_email']) ? $setting['config_email'] : $this->config->get('config_email');

				if (isset($this->request->get['page'])) {
					$page = (int)$this->request->get['page'];
				} else {
					$page = 1;
				}

				$email_total = 0;

				$emails = array();

				switch ($this->request->post['to']) {
					case 'newsletter':
						$customer_data = array(
							'filter_newsletter' => 1,
							'start'             => ($page - 1) * 10,
							'limit'             => 10
						);

						$email_total = $this->model_customer_customer->getTotalCustomers($customer_data);

						$results = $this->model_customer_customer->getCustomers($customer_data);

						foreach ($results as $result) {
							$emails[] = array(
								'email' => $result['email'],
								'customer_id' => isset($result['customer_id']) ? $result['customer_id'] : 0,
								'store_id' => isset($result['store_id']) ? $result['store_id'] : 0,
								'language_id' => isset($result['language_id']) ? $result['language_id'] : 0
							);
						}
						break;
					case 'customer_all':
						$customer_data = array(
							'start' => ($page - 1) * 10,
							'limit' => 10
						);

						$email_total = $this->model_customer_customer->getTotalCustomers($customer_data);

						$results = $this->model_customer_customer->getCustomers($customer_data);

						foreach ($results as $result) {
							$emails[] = array(
								'email' => $result['email'],
								'customer_id' => isset($result['customer_id']) ? $result['customer_id'] : 0,
								'store_id' => isset($result['store_id']) ? $result['store_id'] : 0,
								'language_id' => isset($result['language_id']) ? $result['language_id'] : 0
							);
						}
						break;
					case 'customer_group':
						$customer_data = array(
							'filter_customer_group_id' => $this->request->post['customer_group_id'],
							'start'                    => ($page - 1) * 10,
							'limit'                    => 10
						);

						$email_total = $this->model_customer_customer->getTotalCustomers($customer_data);

						$results = $this->model_customer_customer->getCustomers($customer_data);

						foreach ($results as $result) {
							$emails[] = array(
								'email' => $result['email'],
								'customer_id' => isset($result['customer_id']) ? $result['customer_id'] : 0,
								'store_id' => isset($result['store_id']) ? $result['store_id'] : 0,
								'language_id' => isset($result['language_id']) ? $result['language_id'] : 0
							);
						}
						break;
					case 'customer':
						if (!empty($this->request->post['customer'])) {
							$customers = array_slice($this->request->post['customer'], ($page - 1) * 10, 10);

							foreach ($customers as $customer_id) {
								$customer_info = $this->model_customer_customer->getCustomer($customer_id);

								if ($customer_info) {
									$email_total = isset($this->request->post['customer']) ? count($this->request->post['customer']) : 1;;

									$emails[] = array(
										'customer' => $customer_info,
										'email' => $customer_info['email'],
										'customer_id' => $customer_info['customer_id'],
										'store_id' => $customer_info['store_id'],
										'language_id' => $customer_info['language_id']
									);
								}
							}

							$email_total = count($emails);
						}
						break;
					case 'affiliate_all':
						$affiliate_data = array(
							'filter_affiliate' => 1,
							'start'            => ($page - 1) * 10,
							'limit'            => 10
						);

						$email_total = $this->model_customer_customer->getTotalCustomers($affiliate_data);

						$results = $this->model_customer_customer->getCustomers($affiliate_data);

						foreach ($results as $result) {
							$emails[] = array(
								'email' => $result['email'],
								'affiliate_id' => $result['affiliate_id']
							);
						}
						break;
					case 'affiliate':
						if (!empty($this->request->post['affiliate'])) {
							$affiliates = array_slice($this->request->post['affiliate'], ($page - 1) * 10, 10);

							foreach ($affiliates as $affiliate_id) {
								$affiliate_info = $this->model_customer_customer->getCustomer($affiliate_id);

								if ($affiliate_info) {
									$email_total = 1;

									$emails[] = array(
										'affiliate' => $affiliate_info,
										'email' => $affiliate_info['email'],
										'affiliate_id' => $affiliate_info['affiliate_id']
									);
								}
							}

							$email_total = count($this->request->post['affiliate']);
						}
						break;
					case 'product':
						if (isset($this->request->post['product'])) {
							$email_total = $this->model_sale_order->getTotalEmailsByProductsOrdered($this->request->post['product']);

							$results = $this->model_sale_order->getEmailsByProductsOrdered($this->request->post['product'], ($page - 1) * 10, 10);

							foreach ($results as $result) {
								$emails[] = array(
								'email' => $result['email'],
								'customer_id' => isset($result['customer_id']) ? $result['customer_id'] : 0,
								'store_id' => isset($result['store_id']) ? $result['store_id'] : 0,
								'language_id' => isset($result['language_id']) ? $result['language_id'] : 0
							);
							}
						}
						break;
				}

				if ($emails) {
					$json['success'] = $this->language->get('text_success');

					$start = ($page - 1) * 10;
					$end = $start + 10;

					if($page == 1 && $email_total < 10) {
						$json['success'] = sprintf($this->language->get('text_sent'), $email_total, $email_total);
					} else if($page == 1 && $email_total > 10) {
						$json['success'] = sprintf($this->language->get('text_sent'), 10, $email_total);
					} else if($page > 1 && $email_total < ($page * 10)) {
						$json['success'] = sprintf($this->language->get('text_sent'), $email_total, $email_total);
					} else {
						$json['success'] = sprintf($this->language->get('text_sent'), ($start * $page), $email_total);
					}

					if ($end < $email_total) {
						$json['next'] = str_replace('&amp;', '&', $this->url->link('marketing/contact/send', 'user_token=' . $this->session->data['user_token'] . '&page=' . ($page + 1), true));
					} else {
						$json['next'] = '';
					}

					if (isset($this->request->get['sent_total'])) {
                        $sent_total = $this->request->get['sent_total'];
                    } else {
                        $sent_total = 0;
                    }

		            foreach ($emails as $email_info) {
						$email = is_array($email_info) ? $email_info['email'] : $email_info;

						if (!empty($email_info['language_id'])) {
							$language_id = $email_info['language_id'];
						} else {
							$language_id = $this->config->get('config_language_id');
						}

						if (isset($email_info['store_id']) && $this->request->post['store_id'] == 0) {
 							$store_id = $email_info['store_id'];
						} else {
							$store_id = $this->request->post['store_id'];
						}

						// Prepare mail: admin.newsletter
						$this->load->model('extension/module/emailtemplate');

						$template_load = array(
							'key' => 'admin.newsletter',
							'language_id' => $language_id,
							'email' => $email,
							'store_id' => $store_id
						);

						if (isset($email_info['customer']) && isset($email_info['customer']['customer_id'])) {
							$template_load['customer_id'] = $email_info['customer']['customer_id'];
						} elseif (!empty($email_info['customer_id'])) {
                            $template_load['customer_id'] = $email_info['customer_id'];
                        } else {
                            $this->load->model('customer/customer');

                            $customer_info = $this->model_customer_customer->getCustomerByEmail($email);

                            if ($customer_info) {
                                $template_load['customer_id'] = $email_info['customer_id'];
                            }
						}

						if (!empty($this->request->post['emailtemplate_id'])) {
                            $template_load['emailtemplate_id'] = $this->request->post['emailtemplate_id'];
                        }

						$template = $this->model_extension_module_emailtemplate->load($template_load);

                        if ($template) {
                            if (isset($email_info['customer'])) {
                                $template->addData($email_info['customer']);
                                unset($email_info['customer']);
                            } elseif (isset($template_load['customer_id'])) {
                                $customer_info = $this->model_customer_customer->getCustomer($template_load['customer_id']);

                                if ($customer_info) {
                                    $template->addData($customer_info);
                                }
                            }

                            if (isset($email_info['affiliate'])) {
                                $template->addData($email_info['affiliate']);
                                unset($email_info['affiliate']);
                            } elseif (isset($email_info['affiliate_id'])) {
                                $affiliate_info = $this->model_sale_affiliate->getAffiliate($email_info['affiliate_id']);
                                $template->addData($affiliate_info);
                            }

                            if (!empty($this->request->post['subject'][$language_id])) {
                                $template->data['emailtemplate']['subject'] = $this->request->post['subject'][$language_id];
                            } else {
                                $template->data['emailtemplate']['subject'] = $this->config->get('config_name');
                            }

                            if (!empty($this->request->post['preview'][$language_id])) {
                                $template->data['emailtemplate']['preheader_preview'] = $this->request->post['preview'][$language_id];
                            }

                            if (!empty($this->request->post['message'][$language_id]) && is_array($this->request->post['message'])) {
                                $template->data['message'] = $this->request->post['message'][$language_id];
                            } else {
                                $template->data['message'] = '';
                            }

                            if (is_array($email_info)) {
                                $template->addData($email_info);
                            } else {
                                $template->addData('email', $email_info);
                            }
						    // Prepared mail: admin.newsletter
                        }
		
					$message  = '<html dir="ltr" lang="' . $this->language->get('code') . '">' . "\n";
					$message .= '  <head>' . "\n";
					$message .= '    <title></title>' . "\n";
					$message .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
					$message .= '  </head>' . "\n";
					$message .= '  <body>' . html_entity_decode(($template ? $template->data['message'] : ''), ENT_QUOTES, 'UTF-8') . '</body>' . "\n";
					$message .= '</html>' . "\n";

					
						if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
							$mail = new Mail($this->config->get('config_mail_engine'));
							$mail->parameter = $this->config->get('config_mail_parameter');
							$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
							$mail->smtp_username = $this->config->get('config_mail_smtp_username');
							$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
							$mail->smtp_port = $this->config->get('config_mail_smtp_port');
							$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

							$mail->setTo($email);
							$mail->setFrom($store_email);
							$mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
							
							$mail->setHtml($message);
							// Send mail: admin.newsletter
							if ($template && $template->check()) {
								if ($template->data['message']) {
									$template->data['message'] = $template->replaceContent($template->data['message']);
								}

								$template->build();

                                // Disable preference checking if not newsletter
                                $module_status = ($this->config->get('module_emailtemplate_newsletter_status') && $this->config->get('module_emailtemplate_newsletter_preference'));
                                if ($module_status && isset($this->request->post['to']) && in_array($this->request->post['to'], array('customer', 'customer_all'))) {
                                    $template->data['emailtemplate']['preference'] = false;
                                }

								$mail->setSubject($template->data['emailtemplate']['subject']);

								if (trim(strip_tags($template->data['emailtemplate']['content1'])) == '') {
                                    $template->fetch(null, $template->data['message']);
								}

								$template->hook($mail);

								$mail->send();

								$this->model_extension_module_emailtemplate->sent();

								$sent_total++;
							}
						}
					if ($json['next'] && $sent_total) {
						$json['next'] .= '&sent_total=' . $sent_total;
        			}

        			$json['success'] = sprintf($this->language->get('text_sent'), $sent_total, $email_total);
					}
				} else {
					$json['error']['email'] = $this->language->get('error_email');
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
