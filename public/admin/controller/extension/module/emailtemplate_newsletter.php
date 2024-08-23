<?php
class ControllerExtensionModuleEmailTemplateNewsletter extends Controller {
	private $error = array();

	static $_fields = array(
		'confirm_subscription',
		'newsletter',
		'notification',
		'preference',
		'showcase',
		'status',
		'subscribe',
		'subscribe_admin',
		'unsubscribe',
		'unsubscribe_admin'
	);

	public function index() {
		$modules = $this->db->query("SELECT extension_id FROM `" . DB_PREFIX . "extension` WHERE `type` = 'module' AND `code` = 'emailtemplate_newsletter' LIMIT 1");

		if (!$modules->num_rows) {
			$this->session->data['warning'] = $this->language->get('text_warning_install');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$this->load->language('extension/module/emailtemplate_newsletter');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/event');
		$this->load->model('setting/modification');
		$this->load->model('setting/setting');
		$this->load->model('extension/module/emailtemplate');
		$this->load->model('extension/module/emailtemplate_newsletter');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_emailtemplate_newsletter', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/module/emailtemplate_newsletter', 'user_token=' . $this->session->data['user_token'], true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$events = array(
			'mail_newsletter',
			'mail_newsletter_register',
			'mail_newsletter_add',
			'mail_newsletter_edit',
			'mail_newsletter_delete'
		);

		foreach($events as $event_code) {
			$event_info = $this->model_extension_module_emailtemplate_newsletter->getEventByCode($event_code);

			if (!$event_info || !$event_info['status']) {
				$data['error_event'] = sprintf($this->language->get('error_missing_event'), $event_code);

				break;
			}
		}

		$templates = array(
			'module_emailtemplate_newsletter_subscribe' => 'customer.subscribe',
			'module_emailtemplate_newsletter_unsubscribe' => 'customer.unsubscribe',
			'module_emailtemplate_newsletter_subscribe_admin' => 'customer.subscribe_admin',
			'module_emailtemplate_newsletter_subscribe_admin' => 'customer.subscribe_admin'
		);

		foreach($templates as $key=> $template_key) {
			if ($this->config->get($key)) {
				$template_info = $this->model_extension_module_emailtemplate->getTemplate($template_key);

				if (!$template_info) {
					$data['error_template'] = sprintf($this->language->get('error_missing_template'), $template_key);

					break;
				}
			}
		}

		$modification_info = $this->model_setting_modification->getModificationByCode("emailtemplates_newsletter");

		if (!$modification_info || !$modification_info['status']) {
			$data['error_modification'] = $this->language->get('error_missing_modification');
		}

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_emailtemplate'),
			'href' => $this->url->link('extension/module/emailtemplate', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_name'),
			'href' => $this->url->link('extension/module/emailtemplate_newsletter', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/emailtemplate_newsletter', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('extension/module/emailtemplate', 'user_token=' . $this->session->data['user_token'], true);

		foreach (self::$_fields as $field) {
			if (isset($this->request->post['module_emailtemplate_newsletter_' . $field])) {
				$data['module_emailtemplate_newsletter_' . $field] = $this->request->post['module_emailtemplate_newsletter_' . $field];
			} else {
				$data['module_emailtemplate_newsletter_' . $field] = $this->config->get('module_emailtemplate_newsletter_' . $field);
			}
		}

		$template_info = $this->model_extension_module_emailtemplate->getTemplate('customer.subscribe');

		if ($template_info) {
			$data['edit_emailtemplate_newsletter_subscribe'] = $this->url->link('extension/module/emailtemplate/template', 'user_token=' . $this->session->data['user_token'] . '&id=' . $template_info['emailtemplate_id'], true);
		}

		$template_info = $this->model_extension_module_emailtemplate->getTemplate('customer.unsubscribe');

		if ($template_info) {
			$data['edit_emailtemplate_newsletter_unsubscribe'] = $this->url->link('extension/module/emailtemplate/template', 'user_token=' . $this->session->data['user_token'] . '&id=' . $template_info['emailtemplate_id'], true);
		}

		$template_info = $this->model_extension_module_emailtemplate->getTemplate('customer.subscribe_admin');

		if ($template_info) {
			$data['edit_emailtemplate_newsletter_subscribe_admin'] = $this->url->link('extension/module/emailtemplate/template', 'user_token=' . $this->session->data['user_token'] . '&id=' . $template_info['emailtemplate_id'], true);
		}

		$template_info = $this->model_extension_module_emailtemplate->getTemplate('customer.unsubscribe_admin');

		if ($template_info) {
			$data['edit_emailtemplate_newsletter_unsubscribe_admin'] = $this->url->link('extension/module/emailtemplate/template', 'user_token=' . $this->session->data['user_token'] . '&id=' . $template_info['emailtemplate_id'], true);
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/emailtemplate_newsletter', $data));
	}

	public function install(){
        $this->load->model('extension/module/emailtemplate_newsletter');

	    // Check emailtemplates installed otherwise uninstall.
        if (!$this->config->get('module_emailtemplate_status') || !$this->model_extension_module_emailtemplate_newsletter->tableExists('emailtemplate')) {
            $this->load->model('setting/extension');
            $this->model_setting_extension->uninstall('module', 'emailtemplate_newsletter');
            $this->uninstall();
            return false;
        }

		$this->load->language('extension/module/emailtemplate_newsletter');

		$this->load->model('extension/module/emailtemplate_newsletter');

		$this->model_extension_module_emailtemplate_newsletter->install();

		$this->load->model('setting/setting');

		$settings = $this->model_setting_setting->getSetting('module_emailtemplate_newsletter');
		$settings['module_emailtemplate_newsletter_status'] = 1;
		$settings['module_emailtemplate_newsletter_preference'] = 1;
		$settings['module_emailtemplate_newsletter_notification'] = 1;
		$settings['module_emailtemplate_newsletter_newsletter'] = 1;
		$settings['module_emailtemplate_newsletter_showcase'] = 1;

		$this->model_setting_setting->editSetting('module_emailtemplate_newsletter', $settings);

		$this->load->model('setting/event');

		$event_code = 'mail_newsletter';
		$event_info = $this->model_extension_module_emailtemplate_newsletter->getEventByCode($event_code);

		if ($event_info) {
			if ($event_info['status'] == 0) {
				$this->model_setting_event->editStatus($event_info['event_id'], 1);
			}
		} else {
			$this->model_setting_event->addEvent($event_code, 'catalog/model/account/customer/editNewsletter/before', 'extension/module/emailtemplate_newsletter/eventNewsletter');
		}

		$event_code = 'mail_newsletter_register';
		$event_info = $this->model_extension_module_emailtemplate_newsletter->getEventByCode($event_code);

		if ($event_info) {
			if ($event_info['status'] == 0) {
				$this->model_setting_event->editStatus($event_info['event_id'], 1);
			}
		} else {
			$this->model_setting_event->addEvent($event_code, 'catalog/model/account/customer/addCustomer/after', 'extension/module/emailtemplate_newsletter/eventAddCustomer');
		}

		$event_code = 'mail_newsletter_add';
		$event_info = $this->model_extension_module_emailtemplate_newsletter->getEventByCode($event_code);

		if ($event_info) {
			if ($event_info['status'] == 0) {
				$this->model_setting_event->editStatus($event_info['event_id'], 1);
			}
		} else {
			$this->model_setting_event->addEvent($event_code, 'admin/model/customer/customer/addCustomer/after', 'extension/module/emailtemplate_newsletter/eventAddCustomer');
		}

		$event_code = 'mail_newsletter_edit';
		$event_info = $this->model_extension_module_emailtemplate_newsletter->getEventByCode($event_code);

		if ($event_info) {
			if ($event_info['status'] == 0) {
				$this->model_setting_event->editStatus($event_info['event_id'], 1);
			}
		} else {
			$this->model_setting_event->addEvent($event_code, 'admin/model/customer/customer/editCustomer/after', 'extension/module/emailtemplate_newsletter/eventEditCustomer');
		}

		$event_code = 'mail_newsletter_delete';
		$event_info = $this->model_extension_module_emailtemplate_newsletter->getEventByCode($event_code);

		if ($event_info) {
			if ($event_info['status'] == 0) {
				$this->model_setting_event->editStatus($event_info['event_id'], 1);
			}
		} else {
			$this->model_setting_event->addEvent($event_code, 'admin/model/customer/customer/deleteCustomer/after', 'extension/module/emailtemplate_newsletter/eventDeleteCustomer');
		}

		$this->load->model('extension/module/emailtemplate');

		$this->load->language('extension/module/emailtemplate_newsletter');

		$this->_addTemplate(array(
			'emailtemplate_key' => 'customer.subscribe',
			'emailtemplate_label' => 'Subscribe',
			'emailtemplate_type' => 'customer',
			'emailtemplate_preference' => 'notification',
			'emailtemplate_cart_product' => 1,
			'emailtemplate_default' => 1,
			'emailtemplate_mail_to' => '{{ customer_email }}',
			'emailtemplate_mail_from' => '{{ store_email }}',
			'emailtemplate_mail_sender' => '{{ store_name }}'
		));

		$this->_addTemplate(array(
			'emailtemplate_key' => 'customer.unsubscribe',
			'emailtemplate_label' => 'Unsubscribe',
			'emailtemplate_type' => 'customer',
			'emailtemplate_preference' => 'notification',
			'emailtemplate_cart_product' => 1,
			'emailtemplate_default' => 1,
			'emailtemplate_mail_to' => '{{ customer_email }}',
			'emailtemplate_mail_from' => '{{ store_email }}',
			'emailtemplate_mail_sender' => '{{ store_name }}'
		));

		$this->_addTemplate(array(
			'emailtemplate_key' => 'customer.subscribe_admin',
			'emailtemplate_label' => 'Subscribe - Admin',
			'emailtemplate_type' => 'admin',
			'emailtemplate_showcase' => 'none',
			'emailtemplate_default' => 1,
			'emailtemplate_mail_queue' => 1,
			'emailtemplate_mail_to' => '{{ store_email }}',
			'emailtemplate_mail_from' => '{{ store_email }}',
			'emailtemplate_mail_sender' => '{{ store_name }}',
			'emailtemplate_mail_replyto' => '{{ customer_email }}',
		));

		$this->_addTemplate(array(
			'emailtemplate_key' => 'customer.unsubscribe_admin',
			'emailtemplate_label' => 'Unsubscribe - Admin',
			'emailtemplate_type' => 'admin',
			'emailtemplate_showcase' => 'none',
			'emailtemplate_default' => 1,
			'emailtemplate_mail_queue' => 1,
			'emailtemplate_mail_to' => '{{ store_email }}',
			'emailtemplate_mail_from' => '{{ store_email }}',
			'emailtemplate_mail_sender' => '{{ store_name }}',
			'emailtemplate_mail_replyto' => '{{ customer_email }}',
		));

		$this->model_extension_module_emailtemplate->clear();

		$this->model_extension_module_emailtemplate->updateModification('newsletter');

		$this->load->model('setting/modification');

		$extension_install_id = 0;

		$query = $this->db->query("SELECT extension_id FROM `" . DB_PREFIX . "extension` WHERE `type` = 'module' AND `code` = 'emailtemplate_newsletter' LIMIT 1");

		if ($query && !empty($query->row['extension_id'])) {
			$extension_install_id = $query->row['extension_id'];
		}

		$file = DIR_APPLICATION . 'model/extension/module/emailtemplate/install/newsletter.xml';

		if (!file_exists($file)) {
			trigger_error('Missing install file: ' . $file);
			exit;
		}

		$modification_data = array(
			'extension_install_id' => $extension_install_id,
			'name' => "Email Templates Newsletter",
			'code' => "emailtemplates_newsletter",
			'author' => "Opencart-Templates",
			'version' => EmailTemplate::getVersion(),
			'link' => "https://www.opencart-templates.co.uk/advanced_professional_email_template",
			'xml' => file_get_contents($file),
			'status' => 1
		);

		$modification_info = $this->model_setting_modification->getModificationByCode("emailtemplates_newsletter");

		if ($modification_info) {
			$modification_data['status'] = $modification_info['status'];

			$this->model_setting_modification->deleteModification($modification_info['modification_id']);
		}

		if(!empty($modification_data)){
			$this->model_setting_modification->addModification($modification_data);
		}
	}

	public function uninstall() {
		$this->load->language('extension/module/emailtemplate_newsletter');

		$this->load->model('extension/module/emailtemplate_newsletter');

		$this->model_extension_module_emailtemplate_newsletter->uninstall();

        $this->load->model('setting/setting');

        $this->model_setting_setting->deleteSetting('module_emailtemplate_newsletter');

		$this->load->model('setting/event');

		$this->model_setting_event->deleteEventByCode('mail_newsletter');
		$this->model_setting_event->deleteEventByCode('mail_newsletter_register');
		$this->model_setting_event->deleteEventByCode('mail_newsletter_add');
		$this->model_setting_event->deleteEventByCode('mail_newsletter_edit');
		$this->model_setting_event->deleteEventByCode('mail_newsletter_delete');

		$this->load->model('setting/modification');

		$modification_info = $this->model_setting_modification->getModificationByCode("emailtemplates_newsletter");

		if ($modification_info) {
			$this->model_setting_modification->deleteModification($modification_info['modification_id']);
		}
	}



	// admin/model/customer/customer/addCustomer/after
	public function eventAddCustomer($route, $args, $output) {
		if (!empty($output)) {
			$this->load->model('extension/module/emailtemplate_newsletter');

			$customer_preference_data = array(
				'notification' => isset($args[0]['preference_notification']) ? 1 : 0,
				'showcase' => isset($args[0]['preference_showcase']) ? 1 : 0,
				'token' => token(32)
			);

			$this->model_extension_module_emailtemplate_newsletter->addCustomerPreference($output, $customer_preference_data);
		}
	}

	// admin/model/customer/customer/editCustomer/after
	public function eventEditCustomer($route, $data) {
		if (!empty($data[0])) {
			$this->load->model('extension/module/emailtemplate_newsletter');

			$customer_preference_data = array(
				'notification' => isset($data[1]['preference_notification']) ? 1 : 0,
				'showcase' => isset($data[1]['preference_showcase']) ? 1 : 0,
			);

			$customer_preference_info = $this->model_extension_module_emailtemplate_newsletter->getCustomerPreference($data[0]);

			if (!$customer_preference_info) {
				$customer_preference_data['token'] = token(32);

				$this->model_extension_module_emailtemplate_newsletter->addCustomerPreference($data[0], $customer_preference_data);
			} else {
				$this->model_extension_module_emailtemplate_newsletter->editCustomerPreference($data[0], $customer_preference_data);
			}
		}
	}

	// admin/model/customer/customer/deleteCustomer/after
	public function eventDeleteCustomer($route, $data) {
		if ($this->config->get('module_emailtemplate_newsletter_status') && !empty($data[0])) {
			$this->load->model('extension/module/emailtemplate_newsletter');

			$this->model_extension_module_emailtemplate_newsletter->deleteCustomerPreference($data[0]);
		}
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/emailtemplate_newsletter')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	private function _addTemplate($template_data) {
		if (empty($this->model_localisation_language)) {
			$this->load->model('localisation/language');
		}
		if (empty($this->model_extension_module_emailtemplate)) {
			$this->load->model('extension/module/emailtemplate');
		}

		$languages = $this->model_localisation_language->getLanguages();

		$replace_language_vars = defined('REPLACE_LANGUAGE_VARIABLES') ? REPLACE_LANGUAGE_VARIABLES : true;

		$template_info = $this->model_extension_module_emailtemplate->getTemplate($template_data['emailtemplate_key']);

		if (!$template_info) {
			$emailtemplate_id = $this->model_extension_module_emailtemplate->insertTemplate($template_data);

			$language_key = str_replace('.', '_', $template_data['emailtemplate_key']);

			foreach ($languages as $language) {
				$template_description_data = array(
					'emailtemplate_description_cart_title' => $this->language->get('text_' . $language_key . '_cart_title') != 'text_' . $language_key . '_cart_title' ? $this->language->get('text_' . $language_key . '_cart_title') : '',
					'emailtemplate_description_heading' => $this->language->get('text_' . $language_key . '_heading') != 'text_' . $language_key . '_heading' ? $this->language->get('text_' . $language_key . '_heading') : '',
					'emailtemplate_description_content1' => $this->language->get('text_' . $language_key . '_content1') != 'text_' . $language_key . '_content1' ? $this->language->get('text_' . $language_key . '_content1') : '',
					'emailtemplate_description_content2' => $this->language->get('text_' . $language_key . '_content2') != 'text_' . $language_key . '_content2' ? $this->language->get('text_' . $language_key . '_content2') : '',
					'emailtemplate_description_content3' => $this->language->get('text_' . $language_key . '_content3') != 'text_' . $language_key . '_content3' ? $this->language->get('text_' . $language_key . '_content3') : '',
					'emailtemplate_description_subject' => $this->language->get('text_' . $language_key . '_subject') != 'text_' . $language_key . '_subject' ? $this->language->get('text_' . $language_key . '_subject') : ''
				);

				if ($replace_language_vars) {
					$oLanguage = new Language($language['code']);

					if (method_exists($oLanguage, 'setPath') && substr($template_data['emailtemplate_key'], 0, 6) != 'admin.' && defined('DIR_CATALOG')) {
						$oLanguage->setPath(DIR_CATALOG . 'language/');
					}

					$oLanguage->load($language['code']);
					$langData = $oLanguage->load('extension/module/emailtemplate/emailtemplate');

					if (!empty($template_data['emailtemplate_language_files'])) {
						$language_files = explode(',', $template_data['emailtemplate_language_files']);
						if ($language_files) {
							foreach ($language_files as $language_file) {
								if ($language_file) {
									$_langData = $oLanguage->load(trim($language_file));
									if ($_langData) {
										$langData = array_merge($langData, $_langData);
									}
								}
							}
						}
					}

					$find = array();
					$replace = array();

					foreach ($langData as $i => $val) {
						if ((is_string($val) && (strpos($val, '%s') === false) || is_int($val))) {
							$find[$i] = '{{ ' . $i . ' }}';
							$replace[$i] = $val;
						}
					}

					foreach ($template_description_data as $col => $val) {
						if ($val && is_string($val)) {
							$template_description_data[$col] = str_replace($find, $replace, $val);
						}
					}

				}

				$template_description_data['language_id'] = $language['language_id'];

				$template_description_data['emailtemplate_id'] = $emailtemplate_id;

				$this->model_extension_module_emailtemplate->insertTemplateDescription($template_description_data);
			}
		}
	}
}
