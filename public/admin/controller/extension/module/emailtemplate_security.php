<?php
class ControllerExtensionModuleEmailTemplateSecurity extends Controller {
	private $error = array();

	public function index() {
		$modules = $this->db->query("SELECT * FROM `" . DB_PREFIX . "extension` WHERE `type` = 'module' AND `code` = 'emailtemplate_security'");

		if (!$modules->num_rows) {
			$this->session->data['warning'] = $this->language->get('text_warning_install');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$this->load->language('extension/module/emailtemplate_security');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
		$this->load->model('setting/event');
		$this->load->model('setting/modification');
		$this->load->model('extension/module/emailtemplate');
		$this->load->model('extension/module/emailtemplate_security');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_emailtemplate_security', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/module/emailtemplate_security', 'user_token=' . $this->session->data['user_token'], true));
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
			'module_emailtemplate_security_ip_changed' => 'emailtemplate_password_changed',
			'module_emailtemplate_security_login_limit' => 'emailtemplate_login_limit'
		);

		foreach($events as $key => $event_code) {
			if ($this->config->get($key)) {
				$event_info = $this->model_extension_module_emailtemplate_security->getEventByCode($event_code);

				if (!$event_info || !$event_info['status']) {
					$data['error_event'] = sprintf($this->language->get('error_missing_event'), $event_code);

					break;
				}
			}
		}

		$templates = array(
			'module_emailtemplate_security_ip_changed' => 'customer.ip_changed',
			'module_emailtemplate_security_password_changed' => 'customer.password_changed',
			'module_emailtemplate_security_login_limit' => 'customer.max_login_admin'
		);

		foreach($templates as $key => $template_key) {
			if ($this->config->get($key)) {
				$template_info = $this->model_extension_module_emailtemplate->getTemplate($template_key);

				if (!$template_info) {
					$data['error_template'] = sprintf($this->language->get('error_missing_template'), $template_key);

					break;
				}
			}
		}

		$modification_info = $this->model_setting_modification->getModificationByCode("emailtemplates_security");

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
			'href' => $this->url->link('extension/module/emailtemplate_security', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/emailtemplate_security', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('extension/module/emailtemplate', 'user_token=' . $this->session->data['user_token'], true);

		if (isset($this->request->post['module_emailtemplate_security_status'])) {
			$data['module_emailtemplate_security_status'] = $this->request->post['module_emailtemplate_security_status'];
		} else {
			$data['module_emailtemplate_security_status'] = $this->config->get('module_emailtemplate_security_status');
		}

		if (isset($this->request->post['module_emailtemplate_security_password_changed'])) {
			$data['module_emailtemplate_security_password_changed'] = $this->request->post['module_emailtemplate_security_password_changed'];
		} else {
			$data['module_emailtemplate_security_password_changed'] = $this->config->get('module_emailtemplate_security_password_changed');
		}

		if (isset($this->request->post['module_emailtemplate_security_ip_changed'])) {
			$data['module_emailtemplate_security_ip_changed'] = $this->request->post['module_emailtemplate_security_ip_changed'];
		} else {
			$data['module_emailtemplate_security_ip_changed'] = $this->config->get('module_emailtemplate_security_ip_changed');
		}

		if (isset($this->request->post['module_emailtemplate_security_login_limit'])) {
			$data['module_emailtemplate_security_login_limit'] = $this->request->post['module_emailtemplate_security_login_limit'];
		} else {
			$data['module_emailtemplate_security_login_limit'] = $this->config->get('module_emailtemplate_security_login_limit');
		}

		$data['text_customer_max_login'] = sprintf($this->language->get('text_customer_max_login'), $this->config->get('config_login_attempts'));

		$template_info = $this->model_extension_module_emailtemplate->getTemplate('customer.ip_changed');

		if ($template_info) {
			$data['edit_emailtemplate_security_ip_changed'] = $this->url->link('extension/module/emailtemplate/template', 'user_token=' . $this->session->data['user_token'] . '&id=' . $template_info['emailtemplate_id'], true);
		}

		$template_info = $this->model_extension_module_emailtemplate->getTemplate('customer.password_changed');

		if ($template_info) {
			$data['edit_emailtemplate_security_password_changed'] = $this->url->link('extension/module/emailtemplate/template', 'user_token=' . $this->session->data['user_token'] . '&id=' . $template_info['emailtemplate_id'], true);
		}

		$template_info = $this->model_extension_module_emailtemplate->getTemplate('customer.max_login_admin');

		if ($template_info) {
			$data['edit_emailtemplate_security_login_limit'] = $this->url->link('extension/module/emailtemplate/template', 'user_token=' . $this->session->data['user_token'] . '&id=' . $template_info['emailtemplate_id'], true);
		}

		// Events
		$event_info = $this->model_extension_module_emailtemplate_security->getEventByCode('emailtemplate_password_changed');

		if ($event_info) {
			$data['event_emailtemplate_password_changed'] = $event_info;
		}

		$event_info = $this->model_extension_module_emailtemplate_security->getEventByCode('emailtemplate_login_limit');

		if ($event_info) {
			$data['event_emailtemplate_login_limit'] = $event_info;
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/emailtemplate_security', $data));
	}

	public function install(){
        $this->load->model('extension/module/emailtemplate_security');

        // Check emailtemplates installed otherwise uninstall.
        if (!$this->config->get('module_emailtemplate_status') || !$this->model_extension_module_emailtemplate_security->tableExists('emailtemplate')) {
            $this->load->model('setting/extension');
            $this->model_setting_extension->uninstall('module', 'emailtemplate_security');
            $this->uninstall();
            return false;
        }

        $this->load->model('setting/setting');

        $settings = $this->model_setting_setting->getSetting('module_emailtemplate_security');
        $settings['module_emailtemplate_security_status'] = 1;
        $settings['module_emailtemplate_security_password_changed'] = 1;
        $settings['module_emailtemplate_security_ip_changed'] = 1;
        $settings['module_emailtemplate_security_login_limit'] = 1;

        $this->model_setting_setting->editSetting('module_emailtemplate_security', $settings);

        // Event
        $this->load->model('setting/event');

        $this->model_setting_event->addEvent('emailtemplate_password_changed', 'catalog/model/account/customer/editPassword/after', 'extension/module/emailtemplate_security/eventPasswordChanged');
        $this->model_setting_event->addEvent('emailtemplate_login_limit', 'catalog/model/account/customer/addLoginAttempt/after', 'extension/module/emailtemplate_security/eventLoginLimit');

        $this->load->model('extension/module/emailtemplate');

		$this->load->language('extension/module/emailtemplate_security');

		$this->_addTemplate(array(
			'emailtemplate_key' => 'customer.ip_changed',
			'emailtemplate_label' => 'Alert - IP Address Changed',
			'emailtemplate_type' => 'customer',
			'emailtemplate_preference' => 'notification',
			'emailtemplate_default' => 1
		));

		$this->_addTemplate(array(
			'emailtemplate_key' => 'customer.password_changed',
			'emailtemplate_label' => 'Alert - Password Changed',
			'emailtemplate_type' => 'customer',
			'emailtemplate_preference' => 'notification',
			'emailtemplate_cart_product' => 1,
			'emailtemplate_default' => 1
		));

		$this->_addTemplate(array(
			'emailtemplate_key' => 'customer.max_login_admin',
			'emailtemplate_label' => 'Alert - Max Login Attempts',
			'emailtemplate_type' => 'admin',
			'emailtemplate_default' => 1
		));

		$this->model_extension_module_emailtemplate->clear();

		$this->model_extension_module_emailtemplate->updateModification('security');
	}

	public function uninstall() {
        $this->load->model('setting/setting');

        $this->model_setting_setting->deleteSetting('module_emailtemplate_security');

		$this->load->model('setting/event');

		$this->model_setting_event->deleteEventByCode('emailtemplate_password_changed');
		$this->model_setting_event->deleteEventByCode('emailtemplate_ip_changed');
		$this->model_setting_event->deleteEventByCode('emailtemplate_login_limit');
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/emailtemplate_security')) {
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
