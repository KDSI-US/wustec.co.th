<?php
/* This file is under Git Control by KDSI. */

class ControllerExtensionModuleEmailTemplateNewsletter extends Controller {

	public function index() {
		if (!$this->config->get('module_emailtemplate_newsletter_status') || !$this->config->get('module_emailtemplate_newsletter_preference')) {
			$this->response->redirect($this->url->link('account/newsletter', '', true));
		}

		$this->load->language('extension/module/emailtemplate_newsletter');

		$this->load->model('extension/module/emailtemplate_newsletter');
		$this->load->model('account/customer');

		if (isset($this->request->get['token'])) {
			$customer_preference_info = $this->model_extension_module_emailtemplate_newsletter->getCustomerPreferenceByToken($this->request->get['token']);

			if (!$customer_preference_info) {
				$this->session->data['login_redirect'] = $this->url->link('extension/module/emailtemplate_newsletter', '', true);

				$this->response->redirect($this->url->link('account/login', '', true));
			}

			$customer_id = $customer_preference_info['customer_id'];
		} else {
			if (!$this->customer->isLogged()) {
				$this->session->data['login_redirect'] = $this->url->link('extension/module/emailtemplate_newsletter', '', true);

				$this->response->redirect($this->url->link('account/login', '', true));
			}

			$customer_id = $this->customer->getId();

			$customer_preference_info = $this->model_extension_module_emailtemplate_newsletter->getCustomerPreference($customer_id);
		}

		$customer_info = $this->model_account_customer->getCustomer($customer_id);

		if (empty($customer_info)) {
			$this->session->data['login_redirect'] = $this->url->link('extension/module/emailtemplate_newsletter', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$customer_preference_data = array(
				'notification' => null,
				'showcase' => null
			);

			if (!empty($this->request->get['action']) && $this->request->get['action'] == 'unsubscribe') {
				$customer_preference_data['notification'] = 0;

				$customer_preference_data['showcase'] = 0;
			} else {
				if ($this->config->get('module_emailtemplate_newsletter_notification')) {
					$customer_preference_data['notification'] = !empty($this->request->post['preference_notification']) ? 1 : 0;
				}

				if ($this->config->get('module_emailtemplate_newsletter_showcase')) {
					$customer_preference_data['showcase'] = !empty($this->request->post['preference_showcase']) ? 1 : 0;
				}
			}

			$customer_preference_info = $this->model_extension_module_emailtemplate_newsletter->getCustomerPreference($customer_id);

			if (!$customer_preference_info) {
				$this->model_extension_module_emailtemplate_newsletter->addCustomerPreference($customer_id, $customer_preference_data);
			} else {
				$this->model_extension_module_emailtemplate_newsletter->editCustomerPreference($customer_id, $customer_preference_data);
			}

			if ($this->config->get('module_emailtemplate_newsletter_newsletter')) {
				if (!empty($this->request->get['action']) && $this->request->get['action'] == 'unsubscribe') {
					$newsletter = 0;
					$route = '/model/account/customer/editNewsletter/before';
					$args = array($newsletter);

					$this->eventNewsletter($route, $args);

					$this->model_extension_module_emailtemplate_newsletter->editNewsletter($customer_id, $newsletter);
				} else {
					$newsletter = empty($this->request->post['preference_newsletter']) ? 0 : 1;
					$route = '/model/account/customer/editNewsletter/before';
					$args = array($newsletter);

					$this->eventNewsletter($route, $args);

					$this->model_extension_module_emailtemplate_newsletter->editNewsletter($customer_id, $newsletter);
				}
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('account/account', '', true));
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_preference'),
			'href' => $this->url->link('extension/module/emailtemplate_newsletter', '', true)
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['action'] = $this->url->link('extension/module/emailtemplate_newsletter', '', true);

		$data['module_emailtemplate_newsletter_newsletter'] = $this->config->get('module_emailtemplate_newsletter_newsletter');
		$data['module_emailtemplate_newsletter_notification'] = $this->config->get('module_emailtemplate_newsletter_notification');
		$data['module_emailtemplate_newsletter_showcase'] = $this->config->get('module_emailtemplate_newsletter_showcase');

		$data['preference_newsletter'] = $customer_info['newsletter'];

		$customer_preference_info = $this->model_extension_module_emailtemplate_newsletter->getCustomerPreference($customer_id);

		if (isset($this->request->post['preference_notification'])) {
			$data['preference_notification'] = $this->request->post['preference_notification'];
		} elseif (isset($customer_preference_info['notification'])) {
			$data['preference_notification'] = $customer_preference_info['notification'];
		} else {
			$data['preference_notification'] = '';
		}
		if (isset($this->request->post['preference_showcase'])) {
			$data['preference_showcase'] = $this->request->post['preference_showcase'];
		} elseif (isset($customer_preference_info['showcase'])) {
			$data['preference_showcase'] = $customer_preference_info['showcase'];
		} else {
			$data['preference_showcase'] = '';
		}

		$data['back'] = $this->url->link('account/account', '', true);

		$data['entry_newsletter'] = sprintf($this->language->get('entry_newsletter'), $this->config->get('config_name'));
		$data['text_info'] = sprintf($this->language->get('text_info'), $customer_info['email']);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('extension/module/emailtemplate_newsletter', $data));
	}

	public function unsubscribe() {
		if (!$this->config->get('module_emailtemplate_newsletter_status') || !$this->config->get('module_emailtemplate_newsletter_preference')) {
			$this->response->redirect($this->url->link('account/newsletter', '', true));
		}

		$this->load->language('extension/module/emailtemplate_newsletter');

		$this->load->model('extension/module/emailtemplate_newsletter');
		$this->load->model('account/customer');

		$url = '';

		if (isset($this->request->get['token'])) {
			$url .= 'token=' . $this->request->get['token'];

			$customer_preference_info = $this->model_extension_module_emailtemplate_newsletter->getCustomerPreferenceByToken($this->request->get['token']);

			if (!$customer_preference_info) {
				$this->session->data['login_redirect'] = $this->url->link('extension/module/emailtemplate_newsletter', $url, true);

				$this->response->redirect($this->url->link('account/login', '', true));
			}

			$customer_id = $customer_preference_info['customer_id'];
		} else {
			if (!$this->customer->isLogged()) {
				$this->session->data['login_redirect'] = $this->url->link('extension/module/emailtemplate_newsletter', $url, true);

				$this->response->redirect($this->url->link('account/login', '', true));
			}

			$customer_id = $this->customer->getId();
		}

		$newsletter = 0;
		$route = '/model/account/customer/editNewsletter/before';
		$args = array($newsletter);

		$this->eventNewsletter($route, $args);

		$this->model_extension_module_emailtemplate_newsletter->editNewsletter($customer_id, 0);

		$this->session->data['success'] = $this->language->get('text_success');

		$this->response->redirect($this->url->link('account/account', '', true));
	}

	private function _send($emailtemplate_key, $customer_info){
		// Prepare mail
		$template_data = array(
			'key' => $emailtemplate_key,
			'customer_id' => $customer_info['customer_id'],
			'customer_group_id' => $customer_info['customer_group_id'],
			'language_id' => $customer_info['language_id'],
			'store_id' => $customer_info['store_id']
		);

		$template = $this->model_extension_module_emailtemplate->load($template_data);

		if ($template) {
			// Overwrite customer newsletter preferences with post request because this event is called before saved.
			if ($this->config->get('module_emailtemplate_newsletter_status') && $this->config->get('module_emailtemplate_newsletter_preference')) {
				$template_customer = $template->getCustomer();

				if ($template_customer) {
					$newsletter_preference = $this->model_extension_module_emailtemplate_newsletter->getCustomerPreference($template_customer['customer_id']);

					if ($newsletter_preference) {
						$template_customer['newsletter_preference'] = array(
							'token' => $newsletter_preference['token']
						);

						if ($this->config->get('module_emailtemplate_newsletter_showcase')) {
							if (!empty($this->request->post['preference_showcase'])) {
								$template_customer['newsletter_preference']['showcase'] = $this->request->post['preference_showcase'];
							}
						}

						if ($this->config->get('module_emailtemplate_newsletter_newsletter')) {
							if (!empty($this->request->post['preference_newsletter'])) {
								$template_customer['newsletter'] = $this->request->post['preference_newsletter'];
							}
						}

						if ($this->config->get('module_emailtemplate_newsletter_notification')) {
							if (!empty($this->request->post['preference_notification'])) {
								$template_customer['newsletter_preference']['notification'] = $this->request->post['preference_notification'];
							}
						}
					}

					if ($template->data['emailtemplate']['type'] != 'admin' && !empty($template->data['emailtemplate']['preference'])) {
						switch ($template->data['emailtemplate']['preference']) {
							case 'newsletter':
								if (empty($template_customer['newsletter'])) {
									return false;
								}
								break;
							case 'notification':
								if (empty($this->request->post['preference_notification'])) {
									return false;
								}
								break;
						}
					}

					$template->setCustomer($template_customer);
				}
			}

			$template->addData($customer_info, 'customer');

			$template->data['datetime'] = date($this->language->get('datetime_format'), time());

			$template->data['subject'] = $this->language->get('text_subject');

			$template->data['account_login'] = $this->url->link('account/login');

			if (!empty($template->data['button_account_login'])) {
				$template->data['account_login_text'] = $template->data['button_account_login'];
			} else {
				$template->data['account_login_text'] = $template->data['account_login'];
			}

			if (preg_match('/_admin$/', $emailtemplate_key)) {
				if (defined('HTTP_ADMIN')) {
					$admin_url = HTTP_ADMIN;
				} else {
					$admin_url = HTTPS_SERVER . 'admin/';
				}

				$template->data['admin_customer_link'] = $admin_url . 'index.php?route=customer/customer&filter_email=' . $customer_info['email'];

				$template->data['admin_unlock_link'] = $admin_url . 'index.php?route=customer/customer/unlock&email=' . $customer_info['email'];
			}
			// Prepared mail

			$template->build();

			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($customer_info['email']);
			$mail->setFrom($template->data['store_email']);
			$mail->setSender($template->data['store_name']);
			$mail->setSubject($template->data['store_name']);

			$template->hook($mail);

			$mail->send();

			$this->model_extension_module_emailtemplate->sent();
		}
	}

	// catalog/model/account/customer/editNewsletter/before
	public function eventNewsletter(&$route, &$args) {
		if (!$this->config->get('module_emailtemplate_newsletter_status')) {
			return null;
		}

		$this->load->model('extension/module/emailtemplate_newsletter');

		if ($this->customer->isLogged()) {
			$customer_id = $this->customer->getId();
		} else {
			if (empty($this->request->get['token'])) {
				return null;
			}

			$customer_preference_info = $this->model_extension_module_emailtemplate_newsletter->getCustomerPreferenceByToken($this->request->get['token']);

			if (!$customer_preference_info) {
				return null;
			}

			$customer_id = $customer_preference_info['customer_id'];
		}

		$newsletter = $args[0];

		$this->load->model('extension/module/emailtemplate');
		$this->load->model('account/customer');

		$customer_info = $this->model_account_customer->getCustomer($customer_id);

		if ($customer_info['newsletter'] != $newsletter) {
			$this->model_extension_module_emailtemplate_newsletter->deleteEmailtemplateLogs($customer_info['customer_id']);

			if ($newsletter) {
				if ($this->config->get('module_emailtemplate_newsletter_subscribe')) {
					$this->_send('customer.subscribe', $customer_info);
				}

				if ($this->config->get('module_emailtemplate_newsletter_subscribe_admin')) {
					$this->_send('customer.subscribe_admin', $customer_info);
				}
			} else {
				if ($this->config->get('module_emailtemplate_newsletter_unsubscribe')) {
					$this->_send('customer.unsubscribe', $customer_info);
				}

				if ($this->config->get('module_emailtemplate_newsletter_unsubscribe_admin')) {
					$this->_send('customer.unsubscribe_admin', $customer_info);
				}
			}
		}
	}

	// catalog/model/account/addCustomer/after
	public function eventAddCustomer(&$route, &$args, &$output) {
		if (!$this->config->get('module_emailtemplate_newsletter_status') || !$this->config->get('module_emailtemplate_newsletter_preference')) {
			return null;
		}

		$this->load->model('account/customer');
		$this->load->model('extension/module/emailtemplate_newsletter');

		$customer_info = $args[0];
		$customer_id = $output;

		if ($customer_id) {
			if (empty($customer_info['newsletter'])) {
				$customer_preference_data = array(
					'notification' => 0,
					'showcase' => 0
				);
			} else {
				if (empty($this->request->post['preference_newsletter'])) {
					$this->model_account_customer->editNewsletter(0);
				}

				$customer_preference_data = array(
					'notification' => !empty($this->request->post['preference_notification']) ? 1 : 0,
					'showcase' => !empty($this->request->post['preference_showcase']) ? 1 : 0
				);
			}

			$customer_preference_info = $this->model_extension_module_emailtemplate_newsletter->getCustomerPreference($customer_id);

			if (!$customer_preference_info) {
				$this->model_extension_module_emailtemplate_newsletter->addCustomerPreference($customer_id, $customer_preference_data);
			} else {
				$this->model_extension_module_emailtemplate_newsletter->editCustomerPreference($customer_id, $customer_preference_data);
			}
		}
	}
}
