<?php

class ControllerExtensionModuleEmailTemplate extends Controller {

	protected $data = array();

	private $error = array();

	private $_css = array(
		'view/stylesheet/module/emailtemplate.css?v=1'
	);

	private $_js = array(
		'view/javascript/emailtemplate/core.js?v=2'
	);

	private $content_count = 3;

	public function __construct($registry) {
		parent::__construct($registry);

		$this->load->library('emailtemplate');

		$this->load->language('extension/module/emailtemplate');

		$this->load->model('extension/module/emailtemplate');
	}

	/**
	 * List Of Email Templates & Config
	 */
	public function index() {
		if (!$this->installed()) {
			$this->response->redirect($this->url->link('extension/module/emailtemplate/installer', 'user_token='.$this->session->data['user_token'], true));
		}

		if ($this->model_extension_module_emailtemplate->checkVersion() !== false) {
			$this->response->redirect($this->url->link('extension/module/emailtemplate/upgrade', 'user_token='.$this->session->data['user_token'], true));
		}

		$url = '';

		if (isset($this->request->get['filter_type'])) {
			$url .= '&filter_type=' . $this->request->get['filter_type'];
		}

		if (isset($this->request->get['filter_preference'])) {
			$url .= '&filter_preference=' . $this->request->get['filter_preference'];
		}

		if (isset($this->request->post['filter_content'])) {
			$url .= '&filter_content=' . $this->request->post['filter_content'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		} else {
			$url .= '&sort=modified';
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		} else {
			$url .= '&order=DESC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['action'])) {
			if (empty($this->request->post['selected'])) {
				$this->session->data['attention'] = $this->language->get('error_template_selection_empty');

				$this->response->redirect($this->url->link('extension/module/emailtemplate', 'user_token='.$this->session->data['user_token'] . $url, true));
			}

			switch($this->request->get['action']) {
				case 'delete':
					$affected = $this->model_extension_module_emailtemplate->deleteTemplate($this->request->post['selected']);

					if ($affected) {
						$this->session->data['success'] = sprintf($this->language->get('success_delete_template'), $affected, (($affected > 1) ? "'s" : ""));

						$this->session->data['attention'] = sprintf($this->language->get('text_template_changed'), $this->url->link('extension/module/emailtemplate/rebuild_modifications', 'user_token=' . $this->session->data['user_token'], true));
					}
					break;

				case 'disable':
				case 'enable':
					foreach ($this->request->post['selected'] as $id) {
						$this->model_extension_module_emailtemplate->updateTemplatesStatus($id, ($this->request->get['action'] == 'enable'));
					}

					$this->session->data['success'] = $this->language->get('success_status_template_update');

					$this->session->data['attention'] = sprintf($this->language->get('text_template_changed'), $this->url->link('extension/module/emailtemplate/rebuild_modifications', 'user_token='.$this->session->data['user_token'], true));
					break;

				case 'delete_shortcode':
					foreach ($this->request->post['selected'] as $id) {
						$this->model_extension_module_emailtemplate->deleteTemplateShortcodes($id);
					}

					$this->session->data['success'] = $this->language->get('success_delete_shortcode');
					break;
			}

			$this->response->redirect($this->url->link('extension/module/emailtemplate', 'user_token='.$this->session->data['user_token'] . $url, true));
		}

		$this->model_extension_module_emailtemplate->cleanLogs();

		$this->_setTitle();

		$this->_messages();

		$this->_breadcrumbs();

		$url = '';

		if (isset($this->request->post['filter_type'])) {
			$url .= '&filter_type=' . $this->request->post['filter_type'];
		}

		if (isset($this->request->post['filter_content'])) {
			$url .= '&filter_content=' . $this->request->post['filter_content'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		} else {
			$url .= '&sort=modified';
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		} else {
			$url .= '&order=DESC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->data['action'] = $this->url->link('extension/module/emailtemplate', 'user_token='.$this->session->data['user_token'] . $url, true);
		$this->data['action_insert_template'] = $this->url->link('extension/module/emailtemplate/template', 'user_token='.$this->session->data['user_token'], true);
		$this->data['cancel'] = $this->url->link('marketplace/extension', 'user_token='.$this->session->data['user_token'] . '&type=module', true);
		$this->data['clear_cache_url'] = $this->url->link('extension/module/emailtemplate/clear', 'user_token='.$this->session->data['user_token'], true);
		$this->data['config_url'] = $this->url->link('extension/module/emailtemplate/config', 'user_token='.$this->session->data['user_token'] . '&id=1', true);
		$this->data['cron_url'] = $this->url->link('extension/module/emailtemplate/cron', 'user_token='.$this->session->data['user_token'] . '&redirect', true);
		$this->data['modification_url'] = $this->url->link('extension/module/emailtemplate/rebuild_modifications', 'user_token='.$this->session->data['user_token'], true);
		$this->data['logs_url'] = $this->url->link('extension/module/emailtemplate/logs', 'user_token='.$this->session->data['user_token'], true);

		$this->data['support_url'] = 'http://support.opencart-templates.co.uk/open.php';

		if (defined('VERSION')) {
			$ocVer = VERSION;
		} else {
			$ocVer = '';
		}

		$i = 1;
		foreach(array('name'=>$this->config->get("config_owner").' - '.$this->config->get("config_name"), 'email'=>$this->config->get("config_email"), 'protocol'=>$this->config->get("config_mail_protocol"), 'storeUrl'=>HTTP_CATALOG, 'version'=>EmailTemplate::getVersion(), 'opencartVersion'=>$ocVer, 'phpVersion'=>phpversion()) as $key=>$val) {
			$this->data['support_url'] .= (($i == 1) ? '?' : '&amp;') . $key . '=' . html_entity_decode($val,ENT_QUOTES,'UTF-8');
			$i++;
		}

        $emailtemplate_configs = $this->model_extension_module_emailtemplate->getConfigs(array(), true, true);

        if ($emailtemplate_configs) {
            $this->data['action_configs'] = array();

            foreach($emailtemplate_configs as $row) {
                $this->data['action_configs'][] = array(
                    'id' => $row['emailtemplate_config_id'],
                    'name' => $row['emailtemplate_config_name'],
                    'url' =>$this->url->link('extension/module/emailtemplate/config', 'user_token='.$this->session->data['user_token'] . '&id=' . $row['emailtemplate_config_id'], true)
                );
            }
        }

		$this->data['templates_restore'] = array();

		$template_restore = $this->model_extension_module_emailtemplate->getTemplatesRestore();

		if ($template_restore) {
			foreach($template_restore as $key) {
				$this->data['templates_restore'][] = array(
					'name' => $key,
					'url' => $this->url->link('extension/module/emailtemplate/template_restore', 'user_token='.$this->session->data['user_token'] . '&key='. $key, true)
				);
			}
		}

		$total_unsent_logs = $this->model_extension_module_emailtemplate->getTotalTemplateLogs(array(
			'emailtemplate_log_is_sent' => false
		));

		if ($total_unsent_logs) {
			$this->data['error_unsent'] = sprintf($this->language->get('text_error_unsent'), $total_unsent_logs);
		}

		$this->data['emailtemplate_modules'] = array();

		$files = glob(DIR_APPLICATION . 'controller/extension/module/emailtemplate_*.php');

		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');

				$check = $this->db->query("SELECT * FROM `" . DB_PREFIX . "extension` WHERE `code` = '" . $this->db->escape($extension) . "'");

				if ($check->num_rows) {
					$this->load->language('extension/module/' . $extension, 'extension');

					$this->data['emailtemplate_modules'][$this->language->get('extension')->get('heading_name')] = array(
						'name' => $this->language->get('extension')->get('heading_name'),
						'url' => $this->url->link('extension/module/' . $extension, 'user_token=' . $this->session->data['user_token'], true)
					);
				}
			}

            ksort($this->data['emailtemplate_modules']);

            $this->data['emailtemplate_modules'] = array_values($this->data['emailtemplate_modules']);
		}

		$file = DIR_APPLICATION . 'controller/extension/module/pdf_invoice.php';

		if (file_exists($file)) {
			$check = $this->db->query("SELECT * FROM `" . DB_PREFIX . "extension` WHERE `code` = 'pdf_invoice'");

			if ($check->num_rows) {
				$this->load->language('extension/module/pdf_invoice', 'extension');

				$this->data['emailtemplate_modules'][$this->language->get('extension')->get('heading_name')] = array(
					'name' => $this->language->get('extension')->get('heading_name'),
					'url' => $this->url->link('extension/module/pdf_invoice', 'user_token=' . $this->session->data['user_token'], true)
				);
			}
		}

		if ($this->data['emailtemplate_modules']) {
			ksort($this->data['emailtemplate_modules']);

			$this->data['emailtemplate_modules'] = array_values($this->data['emailtemplate_modules']);
		}

		$this->data['emailtemplate_types'] = $this->model_extension_module_emailtemplate->getTemplateTypes();

		if (isset($this->request->get['filter_type'])) {
			$this->data['emailtemplate_type'] = $this->request->get['filter_type'];
		} else {
			$this->data['emailtemplate_type'] = '';
		}

		if (isset($this->request->get['filter_content'])) {
			$this->data['filter_content'] = $this->request->get['filter_content'];
		} else {
			$this->data['filter_content'] = '';
		}

		if (isset($this->request->get['filter_preference'])) {
			$this->data['filter_preference'] = $this->request->get['filter_preference'];
		} else {
			$this->data['filter_preference'] = '';
		}

		$this->data['version'] = EmailTemplate::getVersion();

		$this->load->model('localisation/language');

		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		$this->_js[] = 'view/javascript/emailtemplate/extension.js?v=3';

		$this->_output('extension/module/emailtemplate/extension');
	}

	/**
	 * Config Form
	 */
	public function config() {
		if (isset($this->request->get['action'])) {
			switch($this->request->get['action']) {
				case 'create':
					if (!empty($this->request->post['id'])) {
						$copy_id = $this->request->post['id'];
					} else if (!empty($this->request->get['id'])) {
						$copy_id = $this->request->get['id'];
					}

					if ($copy_id && $this->_validateConfigCreate($this->request->post)) {
						$newId = $this->model_extension_module_emailtemplate->cloneConfig($copy_id, $this->request->post);

						if ($newId) {
							$this->session->data['success'] = $this->language->get('success_config');
							$this->response->redirect($this->url->link('extension/module/emailtemplate/config', 'user_token='.$this->session->data['user_token'].'&id='.$newId, true));
						}
					}
					break;
				case 'delete':
					if (isset($this->request->get['id']) && $this->model_extension_module_emailtemplate->deleteConfig($this->request->get['id'])) {
						if ($this->request->get['id'] == 0) {
							$this->session->data['success'] = $this->language->get('success_config_restore');
						} else {
							$this->session->data['success'] = $this->language->get('success_config_delete');
						}
					}

					$this->response->redirect($this->url->link('extension/module/emailtemplate/config', 'user_token='.$this->session->data['user_token'] . '&id=1', true));
					break;
			}
		}

		// Save
		if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateConfig($this->request->post)) {
			$request = $this->request->post;

			// check style changed
			$config = $this->model_extension_module_emailtemplate->getConfig($this->request->get['id'], true);
            if (isset($request['emailtemplate_config_style']) && $request['emailtemplate_config_style'] != $config['emailtemplate_config_style']) {
				$request = $this->_config_style($request);
			}

			// Combine
			foreach(array(
                        'emailtemplate_config_header_spacing' => 2,
                        'emailtemplate_config_footer_spacing' => 2,
                        'emailtemplate_config_page_spacing' => 2,

                        'emailtemplate_config_footer_padding' => 4,
                        'emailtemplate_config_header_padding' => 4,
                        'emailtemplate_config_page_padding' => 4,
                        'emailtemplate_config_showcase_padding' => 4,

				        'emailtemplate_config_header_border_radius' => 4,
				        'emailtemplate_config_header_border_top' => 2,
				        'emailtemplate_config_header_border_bottom' => 2,
				        'emailtemplate_config_header_border_right' => 2,
				        'emailtemplate_config_header_border_left' => 2,

				        'emailtemplate_config_footer_border_radius' => 4,
				        'emailtemplate_config_footer_border_top' => 2,
				        'emailtemplate_config_footer_border_bottom' => 2,
				        'emailtemplate_config_footer_border_right' => 2,
				        'emailtemplate_config_footer_border_left' => 2,

				        'emailtemplate_config_page_border_radius' => 4,
				        'emailtemplate_config_page_border_top' => 2,
				        'emailtemplate_config_page_border_bottom' => 2,
				        'emailtemplate_config_page_border_right' => 2,
				        'emailtemplate_config_page_border_left' => 2,

				        'emailtemplate_config_showcase_border_radius' => 4,
				        'emailtemplate_config_showcase_border_top' => 2,
				        'emailtemplate_config_showcase_border_bottom' => 2,
				        'emailtemplate_config_showcase_border_right' => 2,
				        'emailtemplate_config_showcase_border_left' => 2
			        ) as $key => $length){
				if (empty($request[$key])) {
					$request[$key] = '';
				}

				if (is_array($request[$key]) && count($request[$key]) == $length){
					ksort($request[$key]);

					// Remove white space
					foreach($request[$key] as $i => $val) {
						$request[$key][$i] = preg_replace('/\s+/','',$val);
					}

					$request[$key] = implode(', ', $request[$key]);
				}
			}

			// Set box shadow
			if (isset($request['emailtemplate_config_page_shadow']) && $request['emailtemplate_config_page_shadow'] == 'box-shadow') {
				$request['emailtemplate_config_page_shadow'] = $request['emailtemplate_config_page_box_shadow'];
			}

			if ($this->model_extension_module_emailtemplate->updateConfig($this->request->get['id'], $request)) {
				$this->session->data['success'] = $this->language->get('success_config');
			}

			if (!empty($this->request->post['setting'])) {
				$this->load->model('setting/setting');

				$this->model_setting_setting->editSetting('emailtemplate', $this->request->post['setting']);
			}

			$this->response->redirect($this->url->link('extension/module/emailtemplate/config', 'user_token='.$this->session->data['user_token'].'&id='.$this->request->get['id'], true));
		}

		$this->_messages();

		$this->_css[] = 'view/javascript/bootstrap/css/bootstrap-colorpicker.min.css';
        $this->_css[] = 'view/javascript/summernote/summernote.css';

        $this->_js[] = 'view/javascript/summernote/summernote.js';
        $this->_js[] = 'view/javascript/summernote/summernote-image-attributes.js';
        $this->_js[] = 'view/javascript/summernote/opencart.js';
		$this->_js[] = 'view/javascript/bootstrap/js/bootstrap-colorpicker.min.js';
		$this->_js[] = 'view/javascript/emailtemplate/config.js?v=3';

		if (isset($this->request->get['id'])) {
			$this->_config_form();
			$this->_output('extension/module/emailtemplate/config');
		} else {
			$this->_config_form_create();
			$this->_output('extension/module/emailtemplate/config_create_form');
		}
	}

	/**
	 * Template Details
	 */
	public function template() {
		if (isset($this->request->get['id'], $this->request->get['action'])) {
			switch($this->request->get['action']) {
				case 'delete':
					$affected = $this->model_extension_module_emailtemplate->deleteTemplate($this->request->get['id']);

					if ($affected) {
						$this->session->data['success'] = sprintf($this->language->get('success_delete_template'), $affected, (($affected > 1) ? "'s" : ""));

						$this->session->data['attention'] = sprintf($this->language->get('text_template_changed'), $this->url->link('extension/module/emailtemplate/rebuild_modifications', 'user_token='.$this->session->data['user_token'], true));

						$this->response->redirect($this->url->link('extension/module/emailtemplate', 'user_token='.$this->session->data['user_token'], true));
					}
					break;

				case 'delete_shortcode':
					if (isset($this->request->post['shortcode_selection'])) {
						if ($this->model_extension_module_emailtemplate->deleteTemplateShortcodes($this->request->get['id'], array('emailtemplate_shortcode_id' => $this->request->post['shortcode_selection']))) {
							$this->session->data['success'] = $this->language->get('success_delete_shortcode');
						}
					} else {
						if ($this->model_extension_module_emailtemplate->deleteTemplateShortcodes($this->request->get['id'])) {
							$this->session->data['success'] = $this->language->get('success_delete_shortcode');
						}
					}
					break;

				case 'delete_event':
					if (isset($this->request->post['event_selection'])) {
						foreach($this->request->post['event_selection'] as $event_id) {
							if ($this->model_extension_module_emailtemplate->deleteEvent($event_id)) {
								$this->session->data['success'] = $this->language->get('success_delete_event');
							}
						}
					}
					break;

				case 'default_shortcode':
					if ($this->model_extension_module_emailtemplate->insertDefaultTemplateShortcodes($this->request->get['id'])) {
						$this->session->data['success'] = $this->language->get('text_success');
					}
					break;
			}

			$url = '&id='.$this->request->get['id'];

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/module/emailtemplate/template', 'user_token='.$this->session->data['user_token'] . $url, true));
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateTemplate($this->request->post)) {
			$url = '';

			if (isset($this->request->get['id'])) {
				// Update
				$request = $this->request->post;

				$original = $this->model_extension_module_emailtemplate->getTemplate($this->request->get['id']);

				for ($i=0; $i < $this->content_count; $i++) {
					$var = 'emailtemplate_description_content' . $i;
					if (!empty($request[$var])) {
						foreach ($request[$var] as $langId => $val) {
							$request[$var][$langId] = $this->_cleanupContent($val);
						}
					}
				}

				if ($this->model_extension_module_emailtemplate->updateTemplate($this->request->get['id'], $request)) {
					$this->session->data['success'] = $this->language->get('text_success');

					if($original['emailtemplate_status'] != $request['emailtemplate_status']){
						$this->session->data['attention'] = sprintf($this->language->get('text_template_changed'), $this->url->link('extension/module/emailtemplate/rebuild_modifications', 'user_token='.$this->session->data['user_token'], true));
					}
				}
				$url .= '&id='.$this->request->get['id'];
			} else {
				// Insert
				$request = $this->request->post;

				// Key
				if (!$request['emailtemplate_key'] && $request['emailtemplate_key_select']) {
					$defaultTemplate = $this->model_extension_module_emailtemplate->getTemplate($request['emailtemplate_key_select']);

					$request['default_emailtemplate_id'] = $defaultTemplate['emailtemplate_id'];

					unset($defaultTemplate['emailtemplate_id']);
					unset($defaultTemplate['emailtemplate_label']);
					unset($defaultTemplate['emailtemplate_modified']);

					$request = array_merge($defaultTemplate, $request);

					$result = $this->model_extension_module_emailtemplate->getTemplateDescription(array('emailtemplate_id' => $request['default_emailtemplate_id']));

					foreach($result as $row) {
						foreach($row as $col => $val) {
							if(!isset($request[$col]) || !is_array($request[$col])){
								$request[$col] = array();
							}
							$request[$col][$row['language_id']] = $val;
						}
					}

					$request['emailtemplate_key'] = $request['emailtemplate_key_select'];
					$request['emailtemplate_default'] = 0;
					$request['emailtemplate_shortcodes'] = 0;

					if (!isset($request['store_id'])) {
						$request['store_id'] = 'NULL';
					}

					$emailtemplate_id = $this->model_extension_module_emailtemplate->insertTemplate($request);

					$this->model_extension_module_emailtemplate->insertDefaultTemplateShortcodes($emailtemplate_id);
				} else {
					$request['emailtemplate_default'] = 1;
					$request['emailtemplate_shortcodes'] = 0;

					$emailtemplate_id = $this->model_extension_module_emailtemplate->insertTemplate($request);

					$this->load->model('localisation/language');

					$languages = $this->model_localisation_language->getLanguages();

					foreach ($languages as $language) {
						$template_description_data = array(
							'emailtemplate_id' => $emailtemplate_id,
							'language_id' => $language['language_id'],
						);

						$this->model_extension_module_emailtemplate->insertTemplateDescription($template_description_data);
					}
				}

                // Sync copy template
                if (!empty($request['emailtemplate_copy_id']) && $emailtemplate_id) {
                    $copy_template = $this->model_extension_module_emailtemplate->getTemplate($request['emailtemplate_copy_id']);
                    $copy_descriptions = $this->model_extension_module_emailtemplate->getTemplateDescription(array('emailtemplate_id' => $request['emailtemplate_copy_id']));

                    $protected_vars = array('emailtemplate_id', 'emailtemplate_key', 'emailtemplate_label', 'emailtemplate_default', 'emailtemplate_type', 'emailtemplate_preference', 'emailtemplate_condition', 'emailtemplate_modified',
                                            'store_id', 'customer_group_id', 'order_status_id', 'payment_method', 'shipping_method');

                    // Remove fields which could have been changed in the request
                    foreach($protected_vars as $protected_var) {
                        if (array_key_exists($protected_var, $copy_template))
                            unset($copy_template[$protected_var]);
                    }

                    $this->model_extension_module_emailtemplate->updateTemplate($emailtemplate_id, $copy_template);

                    foreach($copy_descriptions as $i => $copy_description) {
                        $copy_description[$copy_description['language_id']] = $copy_description;

                        $this->model_extension_module_emailtemplate->updateTemplateDescription($emailtemplate_id, $copy_description['language_id'], $copy_description);
                    }
                }

				if ($emailtemplate_id) {
					$url .= '&id=' . $emailtemplate_id;
					$this->session->data['success'] = $this->language->get('success_insert');
				}
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/module/emailtemplate/template', 'user_token='.$this->session->data['user_token'] . $url, true));
		}

        $this->_css[] = 'view/javascript/summernote/summernote.css';
        $this->_js[] = 'view/javascript/summernote/summernote.js';
        $this->_js[] = 'view/javascript/summernote/summernote-image-attributes.js';
        $this->_js[] = 'view/javascript/summernote/opencart.js';

        $this->_js[] = 'view/javascript/bootstrap/js/bootstrap-colorpicker.min.js';
		$this->_js[] = 'view/javascript/emailtemplate/template.js?v=4';

		if (isset($this->request->get['id'])) {
			$this->_template_form();
			$this->_output('extension/module/emailtemplate/template_form');
		} else {
			$this->_template_form_create();
			$this->_output('extension/module/emailtemplate/template_create_form');
		}
	}

	/**
	 * Logs
	 */
	public function logs() {
		$this->_setTitle($this->language->get('heading_logs'));

		if (!empty($this->request->post['selected'])) {
			$result = $this->model_extension_module_emailtemplate->deleteLogs($this->request->post['selected']);

			if ($result) {
				$this->session->data['success'] = sprintf($this->language->get('success_delete_log'), $result);
			}

			$this->response->redirect($this->url->link('extension/module/emailtemplate/logs', 'user_token='.$this->session->data['user_token'], true));
		}

		if (!empty($this->request->get['action']) && $this->request->get['action'] == 'clean') {
			$filter = array();

			if (isset($this->request->post['filter_emailtemplate_id'])) {
				$filter['filter_emailtemplate_id'] = $this->request->post['filter_emailtemplate_id'];
			}

			$result = $this->model_extension_module_emailtemplate->cleanLogs($filter);

			$this->session->data['success'] = sprintf($this->language->get('success_delete_log'), $result, ($result > 1 ? '\'s' : ''));

			$this->response->redirect($this->url->link('extension/module/emailtemplate/logs', 'user_token='.$this->session->data['user_token'], true));
		}

		$this->_messages();

		$this->_breadcrumbs(array('heading_logs' => array(
			'link' => 'extension/module/emailtemplate/logs'
		)));

		$this->model_extension_module_emailtemplate->cleanLogs();

		$this->data['action'] = $this->url->link('extension/module/emailtemplate/logs', 'user_token='.$this->session->data['user_token'], true);

		$this->data['cancel'] = $this->url->link('extension/module/emailtemplate', 'user_token='.$this->session->data['user_token'], true);

		$this->data['sent_logging_enabled'] = $this->model_extension_module_emailtemplate->isSentLoggingEnabled();

		if (empty($this->data['sent_logging_enabled'])) {
			$this->data['error_attention'] = $this->language->get('text_enable_logging');
		}

		$total_unsent_logs = $this->model_extension_module_emailtemplate->getTotalTemplateLogs(array(
			'emailtemplate_log_is_sent' => false
		));

		if ($total_unsent_logs) {
			$this->data['error_unsent'] = sprintf($this->language->get('text_error_unsent'), $total_unsent_logs);
		}

		$this->_js[] = 'view/javascript/emailtemplate/logs.js?v=3';

		$this->_output('extension/module/emailtemplate/logs');
	}

	public function fetch_logs() {
		$this->load->model('customer/customer');
		$this->load->model('customer/customer_group');

		if (isset($this->request->get['filter_store_id'])) {
			$this->data['filter_store_id'] = $this->request->get['filter_store_id'];
		} else {
			$this->data['filter_store_id'] = null;
		}

		if (isset($this->request->get['filter_emailtemplate_id'])) {
			$this->data['filter_emailtemplate_id'] = $this->request->get['filter_emailtemplate_id'];
		} else {
			$this->data['filter_emailtemplate_id'] = '';
		}

		if (isset($this->request->get['filter_emailtemplate_key'])) {
			$this->data['filter_emailtemplate_key'] = $this->request->get['filter_emailtemplate_key'];
		} else {
			$this->data['filter_emailtemplate_key'] = '';
		}

		if (isset($this->request->get['filter_customer_id'])) {
			$this->data['filter_customer_id'] = $this->request->get['filter_customer_id'];
		} else {
			$this->data['filter_customer_id'] = '';
		}

        if (isset($this->request->get['filter_email'])) {
            $this->data['filter_email'] = $this->request->get['filter_email'];
        } else {
            $this->data['filter_email'] = '';
        }

		if (isset($this->request->get['filter_sent'])) {
			$this->data['filter_sent'] = $this->request->get['filter_sent'];
		} else {
			$this->data['filter_sent'] = 1;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = $this->data['filter_sent'] ? 'sent' : 'added';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit']) && $this->request->get['limit'] <= 100) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = $this->config->get('config_limit_admin');
		}

		$filter = array(
			'start' => ($page - 1) * $limit,
			'limit' => $limit,
			'sort' => $sort,
			'order' => $order,
			'emailtemplate_id' => $this->data['filter_emailtemplate_id'],
			'emailtemplate_key' => $this->data['filter_emailtemplate_key'],
			'store_id' => $this->data['filter_store_id'],
			'email' => $this->data['filter_email'],
			'customer_id' => $this->data['filter_customer_id'],
			'emailtemplate_log_is_sent' => $this->data['filter_sent']
		);

		$result = $this->model_extension_module_emailtemplate->getTemplateLogs($filter);

		$total = $this->model_extension_module_emailtemplate->getTotalTemplateLogs($filter);

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=ASC';
		} else {
			$url .= '&order=DESC';
		}

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		if (isset($this->request->get['filter_emailtemplate_id'])) {
			$url .= '&filter_emailtemplate_id=' . $this->request->get['filter_emailtemplate_id'];
		}

		if (isset($this->request->get['filter_customer_id'])) {
			$url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
		}

		if (isset($this->request->get['filter_store_id'])) {
			$url .= '&filter_store_id=' . $this->request->get['filter_store_id'];
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . $this->request->get['filter_email'];
		}

		$link = $this->url->link('extension/module/emailtemplate/fetch_logs', 'user_token='.$this->session->data['user_token'] . $url . '&page={page}', true);

		$this->_renderPagination($link, $page, $total, $limit, 'select');

		$this->data['logs'] = array();

		foreach($result as $row) {
			$log_data = array(
				'action' => array(),
				'id' => $row['emailtemplate_log_id'],
				'to' => $row['emailtemplate_log_to'],
				'from' => $row['emailtemplate_log_from'],
				'sender' => html_entity_decode($row['emailtemplate_log_sender'], ENT_QUOTES, 'UTF-8'),
				'read' => '',
				'sent' => '',
				'preview' => ''
			);

			if ($row['emailtemplate_log_subject']) {
				$log_data['subject'] = $this->_truncate_str($row['emailtemplate_log_subject'], 50);
			}

			if ($row['emailtemplate_log_added'] && $row['emailtemplate_log_added'] != '0000-00-00 00:00:00') {
				$log_data['added'] = date($this->language->get('datetime_format'), strtotime($row['emailtemplate_log_added']));
			}

			if ($row['emailtemplate_log_sent'] && $row['emailtemplate_log_sent'] != '0000-00-00 00:00:00') {
				$log_data['sent'] = date($this->language->get('datetime_format'), strtotime($row['emailtemplate_log_sent']));
			}

			if ($row['emailtemplate_log_read'] && $row['emailtemplate_log_read'] != '0000-00-00 00:00:00') {
				$log_data['read'] = date($this->language->get('datetime_format'), strtotime($row['emailtemplate_log_read']));
			}

			if ($row['emailtemplate_id']) {
                $emailtemplate = $this->model_extension_module_emailtemplate->getTemplate($row['emailtemplate_id'], $row['language_id']);
            } elseif ($row['emailtemplate_key']) {
				$emailtemplate = $this->model_extension_module_emailtemplate->getTemplate($row['emailtemplate_key'], $row['language_id']);
			} else {
                $emailtemplate = false;
            }

            if ($emailtemplate) {
                $log_data['emailtemplate_label'] = $emailtemplate['emailtemplate_label'];
                $log_data['emailtemplate_key'] = $emailtemplate['emailtemplate_key'];
                $log_data['emailtemplate_type'] = $emailtemplate['emailtemplate_type'];
                $log_data['emailtemplate_preference'] = $emailtemplate['emailtemplate_preference'];
                $log_data['emailtemplate_url'] = $this->url->link('extension/module/emailtemplate/template', 'user_token='.$this->session->data['user_token'] . '&id=' . $emailtemplate['emailtemplate_id'], true);

                if ($row['emailtemplate_log_content']) {
                    $log_data['download_url'] = $this->url->link('extension/module/emailtemplate/download', 'user_token='.$this->session->data['user_token'] . '&emailtemplate_log_id=' . $row['emailtemplate_log_id'], true);
                    $log_data['resend'] = $this->url->link('extension/module/emailtemplate/send_email', 'user_token='.$this->session->data['user_token'] . '&emailtemplate_log_id=' . $row['emailtemplate_log_id'], true);
                }
            }

			$customer_info = false;

			if ($row['customer_id']) {
				$customer_info = $this->model_customer_customer->getCustomer($row['customer_id']);
			}

			if (!$customer_info) {
				$customer_info = $this->model_customer_customer->getCustomerByEmail($row['emailtemplate_log_to']);
			}

			if ($customer_info) {
				$log_data['customer_name'] = $customer_info['firstname'] . ' ' . $customer_info['lastname'];
				$log_data['customer_id'] = $customer_info['customer_id'];
				$log_data['customer_url'] = $this->url->link('customer/customer/edit', 'user_token='.$this->session->data['user_token'] . '&customer_id=' . $customer_info['customer_id'], true);
			}

			$this->data['logs'][] = $log_data;
		}

		$this->data['emailtemplates'] = array();

		$emailtemplates = $this->model_extension_module_emailtemplate->getTemplates(array('sort' => 'label', 'order' => 'ASC'));

		if ($emailtemplates) {
			foreach ($emailtemplates as $emailtemplate) {
				$emailtemplate_logs_filter = array(
					'emailtemplate_id' => $emailtemplate['emailtemplate_id'],
					'store_id' => $this->data['filter_store_id'],
					'email' => $this->data['filter_email'],
					'customer_id' => $this->data['filter_customer_id'],
					'emailtemplate_log_is_sent' => $this->data['filter_sent']
				);

				$count = $this->model_extension_module_emailtemplate->getTotalTemplateLogs($emailtemplate_logs_filter);

				$this->data['emailtemplates'][] = array(
					'emailtemplate_id' => $emailtemplate['emailtemplate_id'],
					'emailtemplate_key' => $emailtemplate['emailtemplate_key'],
					'emailtemplate_status' => $emailtemplate['emailtemplate_status'],
					'label' => $emailtemplate['emailtemplate_label'],
					'count' => $count
				);
			}
		}

		$emailtemplate_configs = $this->model_extension_module_emailtemplate->getConfigs();

		foreach ($emailtemplate_configs as $emailtemplate_config) {
			if ($emailtemplate_config['emailtemplate_config_log_read']) {
				$this->data['log_read_enabled'] = true;

				break;
			}
		}

		$emailtemplate_logs_filter = array(
			'store_id' => $this->data['filter_store_id'],
			'email' => $this->data['filter_email'],
			'customer_id' => $this->data['filter_customer_id'],
			'emailtemplate_log_is_sent' => $this->data['filter_sent']
		);

		$this->data['total_template_logs'] = $this->model_extension_module_emailtemplate->getTotalTemplateLogs($emailtemplate_logs_filter);

		$emailtemplate_logs_filter = array(
			'emailtemplate_key' => 'missing',
			'store_id' => $this->data['filter_store_id'],
			'email' => $this->data['filter_email'],
			'customer_id' => $this->data['filter_customer_id'],
			'emailtemplate_log_is_sent' => $this->data['filter_sent']
		);

		$this->data['total_missing_template_logs'] = $this->model_extension_module_emailtemplate->getTotalTemplateLogs($emailtemplate_logs_filter);

		$emailtemplate_configs = $this->model_extension_module_emailtemplate->getConfigs(array(), true, true);

		if ($emailtemplate_configs) {
			$this->data['emailtemplate_configs'] = array();

			foreach($emailtemplate_configs as $row) {
				$this->data['emailtemplate_configs'][] = array(
					'emailtemplate_config_id' => $row['emailtemplate_config_id'],
					'emailtemplate_config_name' => $row['emailtemplate_config_name']
				);
			}
		}

		$this->data['stores'] = $this->model_extension_module_emailtemplate->getStores();

		$this->data['filter_customer'] = '';

		if (isset($this->request->get['filter_customer_id'])) {
			$customer = $this->model_customer_customer->getCustomer($this->request->get['filter_customer_id']);

			if ($customer) {
				$this->data['filter_customer'] = strip_tags(html_entity_decode($customer['firstname'] . ' ' . $customer['lastname'], ENT_QUOTES, 'UTF-8'));
			}
		}

		$url = '';
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		if (isset($this->request->get['filter_emailtemplate_id'])) {
			$url .= '&filter_emailtemplate_id=' . $this->request->get['filter_emailtemplate_id'];
		}

		if (isset($this->request->get['filter_customer_id'])) {
			$url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . $this->request->get['filter_customer'];
		}

		if (isset($this->request->get['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
		}

		if (isset($this->request->get['filter_store_id'])) {
			$url .= '&filter_store_id=' . $this->request->get['filter_store_id'];
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . $this->request->get['filter_email'];
		}

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['total'] = $total;

		foreach(array('subject', 'to', 'from',  'added', 'sent', 'read', 'store', 'emailtemplate') as $var) {
			$this->data['sort_'.$var] = $this->url->link('extension/module/emailtemplate/fetch_logs', 'user_token='.$this->session->data['user_token'] . '&sort=' . $var . $url, true);
		}

		$this->_output('extension/module/emailtemplate/_logs');
	}


	/**
	 * Get Template & Parse Tags
	 */
	public function get_template() {
		if (!isset($this->request->get['id']) || !isset($this->request->get['output'])) return false;

		$return = array();

		$output = explode(',', $this->request->get['output']);

		$template_load = array(
			'emailtemplate_id' => $this->request->get['id']
		);

		$template_data = array(
			'emailtemplate_log_id' => false
		);

		if (isset($this->request->get['store_id'])) {
			$template_load['store_id'] = $this->request->get['store_id'];
		}

		$this->load->model('localisation/language');

		$languages = $this->model_localisation_language->getLanguages();

		foreach($languages as $language) {
			$template_load['language_id'] = $language['language_id'];

			$template = $this->model_extension_module_emailtemplate->load($template_load, $template_data);

			if ($template) {
				$template->data['insert_shortcodes'] = false;

				if (isset($this->request->get['parse'])) {
					$template->data['parse_shortcodes'] = $this->request->get['parse'];
				}

				$template->build();

				$return[$language['language_id']] = array();

				foreach($output as $var) {
					if (isset($template->data['emailtemplate'][$var])) {
						$return[$language['language_id']][$var] = html_entity_decode($template->data['emailtemplate'][$var], ENT_QUOTES, 'UTF-8');
					}
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($return));
		return true;
	}

	/**
	 * Get Template Options
	 */
	public function template_option() {
		if (!isset($this->request->get['id'])) {
			return false;
		}

		$emailtemplate = $this->model_extension_module_emailtemplate->getTemplate($this->request->get['id']);

		if (!$emailtemplate) return false;

		$this->data['stores'] = $this->model_extension_module_emailtemplate->getStores();

		$this->load->model('customer/customer_group');

		$this->data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		// Extra options if order type OR key begins with 'order.'
		if ($emailtemplate['emailtemplate_type'] == 'order' || substr($emailtemplate['emailtemplate_key'], 0, 6) == 'order.') {
			$this->load->model('localisation/order_status');

			$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

			$this->data['text_help_subject'] = $this->language->get('text_help_subject');

			$this->load->model('setting/extension');

			$this->data['payment_methods'] = array();

			$extensions = $this->model_setting_extension->getInstalled('payment');

			foreach ($extensions as $code) {
				if ($this->config->has('payment_' . $code . '_status')) {
					$this->load->language('extension/payment/' . $code);

					$this->data['payment_methods'][] = array(
						'name'   => strip_tags($this->language->get('heading_title')),
						'code'   => $code
					);
				}
			}

			$this->data['shipping_methods'] = array();

			$extensions = $this->model_setting_extension->getInstalled('shipping');

			foreach ($extensions as $code) {
				if ($this->config->has('shipping_' . $code . '_status')) {
					$this->load->language('extension/shipping/' . $code);

					$this->data['shipping_methods'][] = array(
						'name'   => strip_tags($this->language->get('heading_title')),
						'code'   => $code
					);
				}
			}
		}

		$this->_output('extension/module/emailtemplate/_template_option');
	}

	/**
	 * Edit Shortcode
	 */
	public function template_shortcode() {
		if (!isset($this->request->get['id'])) {
			$this->response->redirect($this->url->link('extension/module/emailtemplate', 'user_token='.$this->session->data['user_token'], true));
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateTemplateShortcode($this->request->post)) {
			$url = '';
			$return = array();

			if (isset($this->request->get['id'])) {
				if ($this->model_extension_module_emailtemplate->updateTemplateShortcode($this->request->get['id'], $this->request->post)) {
					$return['success'] = $this->language->get('text_success');
				}
			} else {
				$id = $this->model_extension_module_emailtemplate->insertTemplateShortcode($this->request->post);
				if ($id) {
					$url .= '&id='.$id;
					$return['success'] = $this->language->get('success_insert');
				}
			}

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($return));

			return true;
		}

		$this->_template_shortcode_form();

		$this->_css[] = 'view/stylesheet/emailtemplate/modal.css';

		$this->_output('extension/module/emailtemplate/template_shortcode_form');
	}

	/**
	 * Template Event
	 */
	public function template_event() {
		if (!isset($this->request->get['emailtemplate_key'])) {
			trigger_error('Missing emailtemplate_key');
			exit();
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateTemplateEvent($this->request->post)) {
			$return = array();

			$data = array();

			$data['emailtemplate_key'] = $this->request->get['emailtemplate_key'];

			$data['trigger'] = $this->request->post['trigger'];

			$data['code'] = $this->request->post['code'];

			if (substr($data['code'], 0, 14) != 'emailtemplate_') {
				$data['code'] = 'emailtemplate_' . $data['code'];
			}

			$data['action'] = $this->request->post['action'];

			if ($this->model_extension_module_emailtemplate->addEvent($data)) {
				$return['success'] = $this->language->get('text_success');
			}

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($return));

			return true;
		}

		$this->_messages();

		$this->data['action'] = $this->url->link('extension/module/emailtemplate/template_event', 'user_token='.$this->session->data['user_token'] . '&emailtemplate_key=' . $this->request->get['emailtemplate_key'], true);

		$this->data['cancel'] = $this->url->link('extension/module/emailtemplate/template', 'user_token='.$this->session->data['user_token'] . '&emailtemplate_key=' . $this->request->get['emailtemplate_key'], true);

		$this->_css[] = 'view/stylesheet/emailtemplate/modal.css';

		$this->_output('extension/module/emailtemplate/template_event_form');
	}

	/**
	 * Update Template Quick Actions
	 * - clear_shortcodes: Clear template shortcodes
	 * - enable/disable: change template status
	 */
	public function update() {
		$return = array();

		if (!empty($this->request->get['action'])) {
		    $ids = array();
		    if (!empty($this->request->get['id'])) {
		        $ids = array($this->request->get['id']);
            } elseif (!empty($this->request->get['key'])) {
                $templates = $this->model_extension_module_emailtemplate->getTemplates(array('emailtemplate_key' => $this->request->get['key']));

                foreach ($templates as $template) {
                    $ids[] = $template['emailtemplate_id'];
                }
            }

		    if ($ids) {
                switch($this->request->get['action']) {
                    case 'clear_shortcodes':
                        foreach ($ids as $id) {
                            $this->model_extension_module_emailtemplate->deleteTemplateShortcodes($id);
                        }

                        $return['success'] = $this->language->get('success_delete_shortcode');
                        break;
                    case 'enable':
                    case 'disable':
                        foreach ($ids as $id) {
                            $this->model_extension_module_emailtemplate->updateTemplatesStatus($id, ($this->request->get['action'] == 'enable'));
                        }

                        $this->model_extension_module_emailtemplate->updateModification();

                        $return['success'] = $this->language->get('success_status_template_update');

                        $return['warning'] = sprintf($this->language->get('text_modifications_refresh'), $this->url->link('marketplace/modification/refresh', 'user_token=' . $this->session->data['user_token'] . '&redirect=extension/module/emailtemplate', true));
                        break;
                }
			}

		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($return));
	}

	/**
	 * Get Templates
	 */
	public function templates() {
		if (isset($this->request->get['store_id']) && is_numeric($this->request->get['store_id'])) {
			$store_id = $this->request->get['store_id'];
		} else {
			$store_id = NULL;
		}

		if (isset($this->request->get['customer_group_id'])) {
			$customer_group_id = $this->request->get['customer_group_id'];
		} else {
			$customer_group_id = '';
		}

		if (isset($this->request->get['language_id'])) {
			$language_id = $this->request->get['language_id'];
		} else {
			$language_id = $this->config->get('config_language_id');
		}

		if (isset($this->request->get['key'])) {
			$emailtemplate_key = $this->request->get['key'];
		} else {
			$emailtemplate_key = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'modified';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$limit = $this->config->get('config_limit_admin');

		$filter = array(
			'language_id' => $language_id,
			'store_id' => $store_id,
			'customer_group_id' => $customer_group_id,
			'emailtemplate_key' => $emailtemplate_key,
			'emailtemplate_default' => 1,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $limit,
			'limit' => $limit
		);

		if (!empty($this->request->get['filter_content'])) {
			$filter['emailtemplate_content'] = $this->request->get['filter_content'];
		}

		if (!empty($this->request->get['filter_preference'])) {
			$filter['emailtemplate_preference'] = $this->request->get['filter_preference'];
		}

		if (!empty($this->request->get['filter_type'])) {
			$filter['emailtemplate_type'] = $this->request->get['filter_type'];
		}

		if (isset($this->request->get['default'])) {
			$filter['emailtemplate_default'] = $this->request->get['default'];
		}

		$emailtemplate_configs = $this->model_extension_module_emailtemplate->getConfigs();

		foreach ($emailtemplate_configs as $emailtemplate_config) {
			if ($emailtemplate_config['emailtemplate_config_log']) {
                $this->data['sent_logging_enabled'] = true;
                $filter['last_sent'] = true;
				break;
			}
		}

		$templates_total = $this->model_extension_module_emailtemplate->getTotalTemplates($filter);

        // Empty results run as full search
        if (!$templates_total && !empty($this->request->get['filter_content'])) {
            $filter['emailtemplate_content_full'] = true;

            $templates_total = $this->model_extension_module_emailtemplate->getTotalTemplates($filter);
        }

		$results = $this->model_extension_module_emailtemplate->getTemplates($filter);

		$this->data['templates'] = array();

		foreach ($results as $item) {
			$row = array(
				'id' 		  	=> $item['emailtemplate_id'],
				'emailtemplate_config_id' => $item['emailtemplate_config_id'],
				'store_id' 		=> $item['store_id'],
				'customer_group_id' => $item['customer_group_id'],
				'key'    	  	=> $item['emailtemplate_key'],
				'name'    	  	=> $item['emailtemplate_label'] ? $item['emailtemplate_label'] : $item['emailtemplate_key'],
				'label'    	  	=> $item['emailtemplate_label'],
				'template'    	=> $item['emailtemplate_template'],
				'status'      	=> $item['emailtemplate_status'],
				'default'      	=> $item['emailtemplate_default'],
				'shortcodes'    => $item['emailtemplate_shortcodes'],
				'action'		=> $this->url->link('extension/module/emailtemplate/template', 'user_token='.$this->session->data['user_token'] . '&id=' . $item['emailtemplate_id'], true),
			);

			if (isset($this->data['sent_logging_enabled'])) {
                $row['total_sent'] = isset($item['total_sent']) ? $item['total_sent'] :  $this->model_extension_module_emailtemplate->getTotalTemplateLogs(array('emailtemplate_key' => $item['emailtemplate_key']));

                if ($row['total_sent']) {
                    $row['url_sent'] = $this->url->link('extension/module/emailtemplate/logs', 'user_token=' . $this->session->data['user_token'] . '&filter_sent=1&filter_emailtemplate_key=' . $item['emailtemplate_key'], true);
                }

                $template_logs = $this->model_extension_module_emailtemplate->getTemplateLogs(array(
                    'emailtemplate_id' => $item['emailtemplate_id'],
                    'sort' => 'added',
                    'order' => 'DESC',
                    'limit' => 1
                ));

                if ($template_logs) {
                    $last_sent = strtotime(max($template_logs[0]['emailtemplate_log_sent'], $template_logs[0]['emailtemplate_log_added']));

                    if (date('Ymd') == date('Ymd', $last_sent)) {
                        $row['last_sent'] = date('H:i', $last_sent);
                    } else {
                        $row['last_sent'] = date($this->language->get('date_format_short'), $last_sent);
                    }
                }
            }

			if (!empty($item['emailtemplate_config_id'])) {
				foreach($emailtemplate_configs as $emailtemplate_config) {
					if ($emailtemplate_config['emailtemplate_config_id'] != $item['emailtemplate_config_id'])
						continue;

					$row['config'] = $emailtemplate_config['emailtemplate_config_name'];
					$row['config_url'] = $this->url->link('extension/module/emailtemplate/config', 'user_token=' . $this->session->data['user_token'] . '&id=' . $item['emailtemplate_config_id'], true);

					break;
				}
			}

			$modified = strtotime($item['modified']);

			if (date('Ymd') == date('Ymd', $modified)) {
				$row['modified'] = date('H:i', $modified);
			} else {
				$row['modified'] = date($this->language->get('date_format_short'), $modified);
			}

			$row['custom_templates'] = $this->model_extension_module_emailtemplate->getTemplates(array(
				'emailtemplate_key' => $item['emailtemplate_key'],
				'emailtemplate_default' => 0
			));

			foreach ($row['custom_templates'] as $i => $custom_templates) {
				$row['custom_templates'][$i]['action'] = $this->url->link('extension/module/emailtemplate/template', 'user_token='.$this->session->data['user_token'] . '&id=' . $custom_templates['emailtemplate_id'], true);
			}

			if ($item['store_id'] >= 0) {
				$stores = $this->model_extension_module_emailtemplate->getStores($item['store_id']);

				if (isset($stores[$row['store_id']])) {
					$row['store'] = $stores[$row['store_id']];
				} else {
					$row['store'] = current($stores);
				}
			}

			if ($item['customer_group_id']) {
				$this->load->model('customer/customer_group');

				$row['customer_group'] = $this->model_customer_customer_group->getCustomerGroup($item['customer_group_id']);
			}

			$this->data['templates'][] = $row;
		}

		$url = '';

		if (isset($this->request->get['filter_type'])) {
			$url .= '&filter_type=' . $this->request->get['filter_type'];
		}

		if (isset($this->request->get['filter_content'])) {
			$url .= '&filter_content=' . $this->request->get['filter_content'];
		}

		if (isset($this->request->get['filter_preference'])) {
			$url .= '&filter_preference=' . $this->request->get['filter_preference'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		$this->data['action'] = $this->url->link('extension/module/emailtemplate', 'user_token='.$this->session->data['user_token'] . $url, true);
		$this->data['reset'] = $this->url->link('extension/module/emailtemplate', 'user_token='.$this->session->data['user_token'], true);

		$this->data['sort_label'] = $this->url->link('extension/module/emailtemplate/templates', 'user_token='.$this->session->data['user_token'] . '&sort=label' . $url, true);
		$this->data['sort_key'] = $this->url->link('extension/module/emailtemplate/templates', 'user_token='.$this->session->data['user_token'] . '&sort=key' . $url, true);
		$this->data['sort_shortcodes'] = $this->url->link('extension/module/emailtemplate/templates', 'user_token='.$this->session->data['user_token'] . '&sort=shortcodes' . $url, true);
		$this->data['sort_config'] = $this->url->link('extension/module/emailtemplate/templates', 'user_token='.$this->session->data['user_token'] . '&sort=config' . $url, true);
		$this->data['sort_status'] = $this->url->link('extension/module/emailtemplate/templates', 'user_token='.$this->session->data['user_token'] . '&sort=status' . $url, true);
		$this->data['sort_modified'] = $this->url->link('extension/module/emailtemplate/templates', 'user_token='.$this->session->data['user_token'] . '&sort=modified' . $url, true);
		$this->data['sort_last_sent'] = $this->url->link('extension/module/emailtemplate/templates', 'user_token='.$this->session->data['user_token'] . '&sort=last_sent' . $url, true);

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['filter_type'])) {
			$url .= '&filter_type=' . $this->request->get['filter_type'];
		}

		if (isset($this->request->get['filter_content'])) {
			$url .= '&filter_content=' . $this->request->get['filter_content'];
		}

		if (isset($this->request->get['filter_preference'])) {
			$url .= '&filter_preference=' . $this->request->get['filter_preference'];
		}

		$link = $this->url->link('extension/module/emailtemplate/templates', 'user_token='.$this->session->data['user_token'] . $url . '&page={page}', true);

		$this->_renderPagination($link, $page, $templates_total, $limit);

		$this->_output('extension/module/emailtemplate/_templates');
	}

	/**
	 * Restore Template
	 */
	public function template_restore($data = array()) {
		if (!isset($this->request->get['key'])) {
			$this->response->redirect($this->url->link('extension/module/emailtemplate', 'user_token='.$this->session->data['user_token'], true));
		}

		$template_info = $this->model_extension_module_emailtemplate->getTemplate($this->request->get['key']);

		if ($template_info) {
			$this->model_extension_module_emailtemplate->deleteTemplate($template_info['emailtemplate_id']);
		}

		$new_id = $this->model_extension_module_emailtemplate->installTemplate($this->request->get['key']);

		if ($new_id) {
			$this->session->data['success'] = $this->language->get('success_restore');

			$this->session->data['attention'] = sprintf($this->language->get('text_template_changed'), $this->url->link('extension/module/emailtemplate/rebuild_modifications', 'user_token='.$this->session->data['user_token'], true));

			$this->response->redirect($this->url->link('extension/module/emailtemplate/template', 'user_token='.$this->session->data['user_token'] . '&id='.$new_id, true));
		}
	}

	/**
	 * Fetch Template & Parse Tags
	 */
	public function fetch_template() {
		$template_data = array();

		if (!empty($this->request->get['id'])) {
			$template_data['emailtemplate_id'] = $this->request->get['id'];
		} elseif(!empty($this->request->get['emailtemplate_id'])) {
			$template_data['emailtemplate_id'] = $this->request->get['emailtemplate_id'];
		}

		if (!empty($this->request->get['key'])) {
			$template_data['emailtemplate_key'] = $this->request->get['key'];
		}

		if (isset($this->request->get['store_id'])) {
			$template_data['store_id'] = $this->request->get['store_id'];
		}

		if (isset($this->request->get['language_id'])) {
			$template_data['language_id'] = $this->request->get['language_id'];
		}

		if (isset($this->request->get['customer_id'])) {
			$template_data['customer_id'] = $this->request->get['customer_id'];
		}

		if (isset($this->request->get['order_status_id'])) {
			$template_data['order_status_id'] = $this->request->get['order_status_id'];
		}

		if (isset($this->request->get['order_id'])) {
			$this->load->model('sale/order');

			$order_info = $this->model_sale_order->getOrder($this->request->get['order_id']);

			if ($order_info) {
				$template_data['order_id'] = $order_info['order_id'];
				$template_data['payment_method'] = $order_info['payment_code'];
				$template_data['shipping_method'] = $order_info['shipping_code'];

                if (empty($template_data['order_status_id'])) {
                    $template_data['order_status_id'] = $order_info['order_status_id'];
                }
			}
		}

		if (empty($template_data)) return false;

		$template_data['emailtemplate_log_id'] = false;

		$template = $this->model_extension_module_emailtemplate->load($template_data);

		if ($template) {
			$template->data['insert_shortcodes'] = false;

			if (isset($this->request->get['parse'])) {
				$template->data['parse_shortcodes'] = $this->request->get['parse'];
			}

			if (isset($this->request->get['customer_id'])) {
				$this->load->model('customer/customer');

				$customer_info = $this->model_customer_customer->getCustomer($this->request->get['customer_id']);

				if ($customer_info) {
					$template->addData($customer_info);
				}
			}

			if (!empty($order_info)) {
				$template->addData($order_info);

				$language = new Language($order_info['language_code']);
				$language->load($order_info['language_code']);

				$template->data['date_added'] = date($language->get('date_format_short'), strtotime($order_info['date_added']));
				$template->data['date_modified'] = date($this->language->get('date_format_short'), strtotime($order_info['date_modified']));

				$template->data['order_total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value']);

				if (!empty($order_info['customer_group_id'])) {
					$this->load->model('customer/custom_field');

					$custom_fields = $this->model_customer_custom_field->getCustomFields($order_info['customer_group_id']);

					foreach ($custom_fields as $custom_field) {
						if (!empty($template->data['order']['custom_field'][$custom_field['custom_field_id']])) {
							$template->data['order']['custom_field'][$custom_field['custom_field_id']] = array(
								'name' => $custom_field['name'],
								'value' => $template->data['order']['custom_field'][$custom_field['custom_field_id']]
							);
						}

						if (isset($template->data['order']['shipping_custom_field'][$custom_field['custom_field_id']])) {
							$template->data['order']['shipping_custom_field'][$custom_field['custom_field_id']] = array(
								'name' => $custom_field['name'],
								'value' => $template->data['order']['shipping_custom_field'][$custom_field['custom_field_id']]
							);
						}

						if (isset($template->data['order']['payment_custom_field'][$custom_field['custom_field_id']])) {
							$template->data['order']['payment_custom_field'][$custom_field['custom_field_id']] = array(
								'name' => $custom_field['name'],
								'value' => $template->data['order']['payment_custom_field'][$custom_field['custom_field_id']]
							);
						}
					}
				}

				$template->data['payment_address'] = $this->model_extension_module_emailtemplate->formatAddress($order_info, 'payment', $order_info['payment_address_format']);
				$template->data['shipping_address'] = $this->model_extension_module_emailtemplate->formatAddress($order_info, 'shipping', $order_info['shipping_address_format']);
			}

			if (isset($template_data['order_status_id'])) {
                $this->load->model('localisation/order_status');

                $order_status_info = $this->model_localisation_order_status->getOrderStatus($template_data['order_status_id']);

                if ($order_status_info) {
                    $template->data['new_order_status_id'] = $order_status_info['order_status_id'];
                    $template->data['new_order_status'] = $order_status_info['name'];
                }
			}

			if (isset($this->request->get['return_id'])) {
				$this->load->model('sale/return');

				$return_info = $this->model_sale_return->getReturn($this->request->get['return_id']);

				if ($return_info) {
					$template->addData($return_info);

					if ($return_info['order_id'] && !isset($this->request->get['order_id'])) {
						$this->load->model('sale/order');

						$order_info = $this->model_sale_order->getOrder($return_info['order_id']);

						if ($order_info) {
							$template->addData($order_info);

							$template->data['date_added'] = date($language->get('date_format_short'), strtotime($order_info['date_added']));
							$template->data['date_modified'] = date($this->language->get('date_format_short'), strtotime($order_info['date_modified']));

							$template->data['order_total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value']);

							if (!empty($order_info['customer_group_id'])) {
								$this->load->model('customer/custom_field');

								$custom_fields = $this->model_customer_custom_field->getCustomFields($order_info['customer_group_id']);

								foreach ($custom_fields as $custom_field) {
									if (!empty($template->data['order']['custom_field'][$custom_field['custom_field_id']])) {
										$template->data['order']['custom_field'][$custom_field['custom_field_id']] = array(
											'name' => $custom_field['name'],
											'value' => $template->data['order']['custom_field'][$custom_field['custom_field_id']]
										);
									}

									if (isset($template->data['order']['shipping_custom_field'][$custom_field['custom_field_id']])) {
										$template->data['order']['shipping_custom_field'][$custom_field['custom_field_id']] = array(
											'name' => $custom_field['name'],
											'value' => $template->data['order']['shipping_custom_field'][$custom_field['custom_field_id']]
										);
									}

									if (isset($template->data['order']['payment_custom_field'][$custom_field['custom_field_id']])) {
										$template->data['order']['payment_custom_field'][$custom_field['custom_field_id']] = array(
											'name' => $custom_field['name'],
											'value' => $template->data['order']['payment_custom_field'][$custom_field['custom_field_id']]
										);
									}
								}
							}

							$template->data['payment_address'] = $this->model_extension_module_emailtemplate->formatAddress($order_info, 'payment', $order_info['payment_address_format']);
							$template->data['shipping_address'] = $this->model_extension_module_emailtemplate->formatAddress($order_info, 'shipping', $order_info['shipping_address_format']);
						}
					}
				}
			}

			$template->build();

			if (!empty($this->request->post['replace'])) {
				if (isset($template->data['parse_shortcodes']) && !$template->data['parse_shortcodes']) {
					echo html_entity_decode($this->request->post['replace'], ENT_QUOTES, 'UTF-8');
				} else {
					echo $template->renderContent(html_entity_decode($this->request->post['replace'], ENT_QUOTES, 'UTF-8'));
				}
			} elseif (isset($this->request->get['output'])) {
				if (isset($template->data['emailtemplate'][$this->request->get['output']])) {
					if (isset($template->data['parse_shortcodes']) && !$template->data['parse_shortcodes']) {
						echo html_entity_decode($template->data['emailtemplate'][$this->request->get['output']], ENT_QUOTES, 'UTF-8');
					} else {
						echo $template->renderContent(html_entity_decode($template->data['emailtemplate'][$this->request->get['output']], ENT_QUOTES, 'UTF-8'));
					}
				}
			} else {
				$template->data['wrapper_tpl'] = false;

				echo $template->fetch();
			}
		}
		exit;
	}

	/**
	 * Fetch Template Log
	 */
	public function fetch_log($request = array()) {
		$request = array_merge($request, $this->request->get);

		if (empty($request['id'])) {
			return false;
		}

		$log = $this->model_extension_module_emailtemplate->getTemplateLog($request['id']);

		if (empty($log)) {
			return false;
		}

		$return = array(
			'key' => $log['emailtemplate_key'],
			'to' => $log['emailtemplate_log_to'],
			'from' => $log['emailtemplate_log_from'],
			'sender' => $log['emailtemplate_log_sender'],
			'reply_to' => $log['emailtemplate_log_reply_to']
		);

		$return['subject'] = $this->_truncate_str($log['emailtemplate_log_subject'], 50);

		if ($log['emailtemplate_log_sent'] && $log['emailtemplate_log_sent'] != '0000-00-00 00:00:00') {
			$time = strtotime($log['emailtemplate_log_sent']);

			if (date('Ymd') == date('Ymd', $time)) {
				$return['sent'] = date($this->language->get('time_format'), $time);
			} else {
				$return['sent'] = date($this->language->get('date_format_long'), $time);
			}
		}

		if ($log['emailtemplate_log_read'] && $log['emailtemplate_log_read'] != '0000-00-00 00:00:00') {
			$time = strtotime($log['emailtemplate_log_read']);

			if (date('Ymd') == date('Ymd', $time)) {
				$return['read'] = date($this->language->get('time_format'), $time);
			} else {
				$return['read'] = date($this->language->get('date_format_short'), $time);
			}
		}

		$template_load = array(
			'key' => $log['emailtemplate_key'],
			'customer_id' => $log['customer_id'],
			'customer_group_id' => $log['customer_group_id'],
			'language_id' => $log['language_id'],
			'order_id' => $log['order_id'],
			'store_id' => $log['store_id']
		);

		$template = $this->model_extension_module_emailtemplate->load($template_load, array('emailtemplate_log_id' => false));

		if (!$template) {
			unset($template_load['key']);

			$template = $this->model_extension_module_emailtemplate->load($template_load, array('emailtemplate_log_id' => false));
		}

		if ($template) {
			$template->build();

			$return['resend'] = $this->url->link('extension/module/emailtemplate/send_email', 'user_token='.$this->session->data['user_token'] . '&emailtemplate_log_id=' . $log['emailtemplate_log_id'], true);

			$template->data['emailtemplate']['heading'] = html_entity_decode($log['emailtemplate_log_heading'], ENT_QUOTES, 'UTF-8');
			$template->data['emailtemplate']['subject'] = html_entity_decode($log['emailtemplate_log_subject'], ENT_QUOTES, 'UTF-8');

			$content = html_entity_decode($log['emailtemplate_log_content'], ENT_QUOTES, 'UTF-8');

			$return['html'] = $template->fetch(null, $content);

			if (isset($request['output']) && $request['output'] == 'html') {
				echo $return['html'];
				exit;
			} else {
                $this->load->model('customer/customer');

                $customer_info = false;

                if ($log['customer_id']) {
                    $customer_info = $this->model_customer_customer->getCustomer($log['customer_id']);
                }

                if (!$customer_info) {
                    $customer_info = $this->model_customer_customer->getCustomerByEmail($log['emailtemplate_log_to']);
                }

                if ($customer_info) {
                    $return['customer_name'] = $customer_info['firstname'] . ' ' . $customer_info['lastname'];
                    $return['customer_id'] = $customer_info['customer_id'];
                    $return['customer_url'] = $this->url->link('customer/customer/edit', 'user_token='.$this->session->data['user_token'] . '&customer_id=' . $customer_info['customer_id'], true);
                }

				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode($return));
				return true;
			}
		}
	}

	/**
	 * Download
     * emailtemplate_log_id - download html
	 */
    public function download() {
        if (!$this->user->hasPermission('modify', 'extension/module/emailtemplate')) {
            $this->session->data['error'] = $this->language->get('error_permission');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token='.$this->session->data['user_token'] . '&type=module', true));
        }

        if (!empty($this->request->get['emailtemplate_log_id'])) {
            $this->load->model('extension/module/emailtemplate');

            $log = $this->model_extension_module_emailtemplate->getTemplateLog($this->request->get['emailtemplate_log_id']);

            if (empty($log)) {
                return false;
            }

            $template_load = array(
                'key' => $log['emailtemplate_key'],
                'customer_id' => $log['customer_id'],
                'customer_group_id' => $log['customer_group_id'],
                'language_id' => $log['language_id'],
                'order_id' => $log['order_id'],
                'store_id' => $log['store_id']
            );

            $template = $this->model_extension_module_emailtemplate->load($template_load, array('emailtemplate_log_id' => false));

            if (!$template) {
                unset($template_load['key']);

                $template = $this->model_extension_module_emailtemplate->load($template_load, array('emailtemplate_log_id' => false));
            }

            if ($template) {
                $template->build();

                $content = html_entity_decode($log['emailtemplate_log_content'], ENT_QUOTES, 'UTF-8');

                $html = $template->fetch(null, $content);

                header('Content-type: text/html');
                header('Content-Disposition: attachment; filename="' . $log['emailtemplate_key'] . '-' . $log['emailtemplate_log_id'] . '.html"');

                echo $html;
                exit;
            }
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=ASC';
        } else {
            $url .= '&order=DESC';
        }

        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        if (isset($this->request->get['filter_emailtemplate_id'])) {
            $url .= '&filter_emailtemplate_id=' . $this->request->get['filter_emailtemplate_id'];
        }

        if (isset($this->request->get['filter_customer_id'])) {
            $url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
        }

        if (isset($this->request->get['filter_store_id'])) {
            $url .= '&filter_store_id=' . $this->request->get['filter_store_id'];
        }

        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . $this->request->get['filter_email'];
        }

        $this->response->redirect($this->url->link('extension/module/emailtemplate/logs', 'user_token=' . $this->session->data['user_token'] . $url, true));
    }

	/**
	 * Load example email
	 */
	public function preview_email(){
		if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
			$this->response->redirect($this->url->link('extension/module/emailtemplate', 'user_token=' . $this->session->data['user_token'], true));
		}

		$language_id = 0;
		if (isset($this->request->get['language_id'])) {
			$language_id = (int)$this->request->get['language_id'];
		} elseif (isset($this->request->post['language_id'])) {
			$language_id = (int)$this->request->post['language_id'];
		}
		if (!$language_id) {
			$language_id = $this->config->get('config_language_id');
		}

		$overwrite = array();

		if (!empty($_POST['data'])) {
			parse_str($_POST['data'], $overwrite);

			// Set box shadow
			if (isset($overwrite['emailtemplate_config_page_shadow'], $overwrite['emailtemplate_config_page_box_shadow']) && $overwrite['emailtemplate_config_page_shadow'] == 'box-shadow') {
				$overwrite['emailtemplate_config_page_shadow'] = $overwrite['emailtemplate_config_page_box_shadow'];
			}

			foreach ($overwrite as $key => $val) {
				if (substr($key, -3) != '_id') {
					if (substr($key, 0, 26) == 'emailtemplate_description_') {
						if (is_array($val) && isset($val[$language_id])) {
							$val = $val[$language_id];
						}
						unset($overwrite[$key]);
						$overwrite[substr($key, 26)] = $val;
					} elseif (substr($key, 0, 21) == 'emailtemplate_config_') {
						if (is_array($val) && isset($val[$language_id]) && (substr($key, -6) == '_text' || substr($key, -6) == '_title')) {
							$val = $val[$language_id];
						}
						unset($overwrite[$key]);
						$overwrite[substr($key, 21)] = $val;
					} elseif (substr($key, 0, 14) == 'emailtemplate_') {
						unset($overwrite[$key]);
						$overwrite[substr($key, 14)] = $val;
					}
				}
			}
		}

		$load_data = array();

		if (isset($this->request->get['emailtemplate_id'])) {
			$load_data['emailtemplate_id'] = $this->request->get['emailtemplate_id'];
		}

		if (isset($this->request->get['emailtemplate_config_id'])) {
			$load_data['emailtemplate_config_id'] = $this->request->get['emailtemplate_config_id'];

			if (!empty($overwrite)) {
				$overwrite = array('config' => $overwrite);
			}
		}

		if (isset($this->request->get['store_id'])) {
			$load_data['store_id'] = $this->request->get['store_id'];
		}

		$load_data['language_id'] = $language_id;

		$load_data['type'] = 'demo';

		$overwrite['emailtemplate_log_id'] = false;

		$template = $this->model_extension_module_emailtemplate->load($load_data, $overwrite);

		if (!$template) {
			unset($load_data['language_id']);
			$template = $this->model_extension_module_emailtemplate->load($load_data, $overwrite);
		}

		if (!$template) {
			unset($load_data['store_id']);
			$template = $this->model_extension_module_emailtemplate->load($load_data, $overwrite);
		}

		if (!$template) {
			$template = $this->model_extension_module_emailtemplate->load(1, $overwrite);

            if (!$template) {
                trigger_error('Unable to load emailtemplate_id=1');
                exit;
            }
		}

        if (!empty($this->request->get['emailtemplate_id']) && $overwrite) {
            $overwrite['emailtemplate_id'] = $this->request->get['emailtemplate_id'];

            $template->data['emailtemplate'] = array_merge($template->data['emailtemplate'], $overwrite);
		}

		// Load default shortcodes as data
		$default_shortcodes = $this->model_extension_module_emailtemplate->getTemplateShortcodes($template->data['emailtemplate']['emailtemplate_id']);

		if ($default_shortcodes) {
			foreach ($default_shortcodes as $row) {
                if (!isset($template->data[$row['emailtemplate_shortcode_code']])) {
                    if ($row['emailtemplate_shortcode_type'] == 'auto_serialize' && $row['emailtemplate_shortcode_example']) {
                        $example = @unserialize(base64_decode($row['emailtemplate_shortcode_example']));
                    } else {
                        $example = $row['emailtemplate_shortcode_example'];
                    }

                    $template->data[$row['emailtemplate_shortcode_code']] = $example;
                }
			}
		}

		if (trim(strip_tags($template->data['emailtemplate']['content1'])) == '') {
			$this->load->model('localisation/language');

			$language_info = $this->model_localisation_language->getLanguage($language_id);

			if (!$language_info) {
				$language_info = $this->model_localisation_language->getLanguage($this->config->get('config_language_id'));
			}

			$oLanguage = new Language($language_info['code']);
			$oLanguage->load($language_info['code']);
			$oLanguage->load('extension/module/emailtemplate/emailtemplate');

			$template->data['emailtemplate']['heading'] = $oLanguage->get('text_example_heading');
			$template->data['emailtemplate']['content1'] = $oLanguage->get('text_example');
		}

		$template->build();

		echo $template->getHtml();
		exit;
	}

	/**
	 * Clear email template cache
	 */
	public function clear() {
		$this->model_extension_module_emailtemplate->clear();
		$this->model_extension_module_emailtemplate->updateEvents();

		$this->session->data['success'] = $this->language->get('success_clear_cache');

		$this->response->redirect($this->url->link('extension/module/emailtemplate', 'user_token='.$this->session->data['user_token'], true));
	}

	/**
	 * Test email
	 */
	public function send_email() {
		if (isset($this->request->get['emailtemplate_config_id'])) {
			$config = $this->model_extension_module_emailtemplate->getConfig($this->request->get['emailtemplate_config_id'], true);

			$this->load->model('setting/store');

			$store_info = $this->model_setting_store->getStore($config['store_id']);

			if (isset($this->request->post['send_test_email'])) {
				$to = $this->request->post['send_test_email'];
			} elseif ($store_info && !empty($store_info['email'])) {
				$to = $store_info['email'];
			} else {
				$to = $this->config->get('config_email');
			}

			if (isset($this->request->get['language_id'])) {
				$language_id = $this->request->get['language_id'];
			} elseif (!empty($config['language_id']) && $config['language_id'] > 0) {
				$language_id = $config['language_id'];
			} else {
				$language_id = $this->config->get('config_language_id');
			}

			$overwrite = array();

			if (!empty($_POST['data'])) {
				parse_str($_POST['data'], $overwrite);

				// Set box shadow
				if (isset($overwrite['emailtemplate_config_page_shadow'], $overwrite['emailtemplate_config_page_box_shadow']) && $overwrite['emailtemplate_config_page_shadow'] == 'box-shadow') {
					$overwrite['emailtemplate_config_page_shadow'] = $overwrite['emailtemplate_config_page_box_shadow'];
				}

				foreach ($overwrite as $key => $val) {
					if (substr($key, -3) != '_id') {
						if (substr($key, 0, 26) == 'emailtemplate_description_') {
							if (is_array($val) && isset($val[$language_id])) {
								$val = $val[$language_id];
							}
							unset($overwrite[$key]);
							$overwrite[substr($key, 26)] = $val;
						} elseif (substr($key, 0, 21) == 'emailtemplate_config_') {
							if (is_array($val) && isset($val[$language_id]) && (substr($key, -6) == '_text' || substr($key, -6) == '_title')) {
								$val = $val[$language_id];
							}
							unset($overwrite[$key]);
							$overwrite[substr($key, 21)] = $val;
						} elseif (substr($key, 0, 14) == 'emailtemplate_') {
							unset($overwrite[$key]);
							$overwrite[substr($key, 14)] = $val;
						}
					}
				}
			}

			if (!empty($overwrite['language_id']) && $overwrite['language_id'] <= 0) {
				unset($overwrite['language_id']);
			}

			if (!empty($overwrite['store_id']) && $overwrite['store_id'] < 0) {
				unset($overwrite['store_id']);
			}

			if (!empty($overwrite['customer_group_id']) && $overwrite['customer_group_id'] <= 0) {
				unset($overwrite['customer_group_id']);
			}

			$template_load = array(
				'emailtemplate_config_id' => $this->request->get['emailtemplate_config_id'],
				'emailtemplate_id' => 1,
				'language_id' => $language_id
			);

			if (!empty($config['store_id']) && $config['store_id'] >= 0) {
				$template_load['store_id'] = $config['store_id'];
			}

			$this->_sendTestEmail($to, $template_load, $overwrite);

			$return = array();
			$return['success'] = sprintf($this->language->get('success_send'), 1);

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($return));

		} elseif (isset($this->request->get['emailtemplate_id'])) {
			$overwrite = array();

			$language_id = 0;
			if (isset($this->request->get['language_id'])) {
				$language_id = (int)$this->request->get['language_id'];
			} elseif (isset($this->request->post['language_id'])) {
				$language_id = (int)$this->request->post['language_id'];
			}
			if (!$language_id) {
				$language_id = $this->config->get('config_language_id');
			}

			if (isset($_POST['data'])) {
				parse_str($_POST['data'], $overwrite);

				// Set box shadow
				if (isset($overwrite['emailtemplate_config_page_shadow'], $overwrite['emailtemplate_config_page_box_shadow']) && $overwrite['emailtemplate_config_page_shadow'] == 'box-shadow') {
					$overwrite['emailtemplate_config_page_shadow'] = $overwrite['emailtemplate_config_page_box_shadow'];
				}

				foreach ($overwrite as $key => $val) {
					if (substr($key, -3) != '_id') {
						if (substr($key, 0, 26) == 'emailtemplate_description_') {
							if (is_array($val) && isset($val[$language_id])) {
								$val = $val[$language_id];
							}
							unset($overwrite[$key]);
							$overwrite[substr($key, 26)] = $val;
						} elseif (substr($key, 0, 21) == 'emailtemplate_config_') {
							if (is_array($val) && isset($val[$language_id]) && (substr($key, -6) == '_text' || substr($key, -6) == '_title')) {
								$val = $val[$language_id];
							}
							unset($overwrite[$key]);
							$overwrite[substr($key, 21)] = $val;
						} elseif (substr($key, 0, 14) == 'emailtemplate_') {
							unset($overwrite[$key]);
							$overwrite[substr($key, 14)] = $val;
						}
					}
				}
			}

			if (!empty($overwrite['language_id']) && $overwrite['language_id'] <= 0) {
				unset($overwrite['language_id']);
			}

			if (!empty($overwrite['store_id']) && $overwrite['store_id'] < 0) {
				unset($overwrite['store_id']);
			}

			if (!empty($overwrite['customer_group_id']) && $overwrite['customer_group_id'] <= 0) {
				unset($overwrite['customer_group_id']);
			}

			$template_load = array(
				'emailtemplate_id' => $this->request->get['emailtemplate_id'],
				'language_id' => $language_id
			);

			if (isset($this->request->post['send_test_email'])) {
				$to = $this->request->post['send_test_email'];
			} else {
				$to = $this->config->get('config_email');
			}

			$this->_sendTestEmail($to, $template_load, $overwrite, false);

			$return = array();
			$return['success'] = sprintf($this->language->get('success_send'), 1);

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($return));

		} elseif (isset($this->request->get['emailtemplate_log_id'])) {
			$return = array();

			$result = $this->_sendEmailTemplateLog($this->request->get['emailtemplate_log_id']);

			if ($result) {
				$return['success'] = sprintf($this->language->get('success_send'), 1);
			}

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($return));
		} else {
			if (isset($this->request->post['send_test_email'])) {
				$to = $this->request->post['send_test_email'];
			} else {
				$to = $this->config->get('config_email');
			}

			$this->_sendTestEmail($to, $this->config->get('config_store_id'));

			$this->session->data['success'] = sprintf($this->language->get('success_send'), 1);

			$this->response->redirect($this->url->link('extension/module/emailtemplate', 'user_token='.$this->session->data['user_token'], true));
		}
	}

	/**
	 * Rebuild module modifications
	 */
	public function rebuild_modifications() {
		$this->model_extension_module_emailtemplate->updateModification('core');

		$this->model_extension_module_emailtemplate->updateModification();

		if ($this->config->get('module_emailtemplate_newsletter_status')) {
			$this->model_extension_module_emailtemplate->updateModification('newsletter');
		}

		if ($this->config->get('module_emailtemplate_security_status')) {
			$this->model_extension_module_emailtemplate->updateModification('security');
		}

		$this->session->data['attention'] = sprintf($this->language->get('text_modifications_refresh'), $this->url->link('marketplace/modification/refresh', 'user_token='.$this->session->data['user_token'] . '&redirect=extension/module/emailtemplate', true));

		if (isset($this->request->server['HTTP_REFERER'])) {
			$referer = html_entity_decode($this->request->server['HTTP_REFERER'], ENT_QUOTES, 'UTF-8');

			if ($referer) {
				$url = parse_url($referer, PHP_URL_QUERY);

				parse_str($url, $url_query);

				if (isset($url_query['route']) && isset($url_query['id'])) {
					$this->response->redirect($this->url->link($url_query['route'], 'user_token='.$this->session->data['user_token'] . '&id=' . $url_query['id'], true));
				}
			}
		}

		$this->response->redirect($this->url->link('extension/module/emailtemplate', 'user_token='.$this->session->data['user_token'], true));
	}

	/**
	 * Check module installed
	 */
	public function installed() {
		$chk = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = 'module' AND `code` = 'emailtemplate' LIMIT 1");

		if (!$chk->num_rows) {
			return false;
		}

		$this->load->model('setting/modification');

		// Modification added
		$modification_info = $this->model_setting_modification->getModificationByCode("emailtemplates_core");

		if (!$modification_info) {
			$this->model_extension_module_emailtemplate->updateModification('core');

			$modification_info = $this->model_setting_modification->getModificationByCode("emailtemplates_core");

			if (!$modification_info) {
				$this->session->data['error'] = sprintf($this->language->get('error_missing_modifications'), $this->url->link('marketplace/modification/refresh', 'user_token='.$this->session->data['user_token'] . '&redirect=extension/module/emailtemplate', true));
				return false;
			}
		}

		// Modifications refreshed?
		if (!method_exists('Language', 'setPath')) {
			$this->session->data['error'] = sprintf($this->language->get('error_refresh_modifications'), $this->url->link('marketplace/modification/refresh', 'user_token='.$this->session->data['user_token'] . '&redirect=extension/module/emailtemplate', true));
			return false;
		}

		// Emailtemplate table?
		if (!$this->model_extension_module_emailtemplate->tableExists('emailtemplate')) {
			if ($this->model_extension_module_emailtemplate->tableExists('emailtemplate_config')) {
				$this->model_extension_module_emailtemplate->upgradeExtension();

				$this->response->redirect($this->url->link('extension/module/emailtemplate', 'user_token='.$this->session->data['user_token'], true));
			}

			$this->session->data['error'] = sprintf($this->language->get('error_missing_module'), $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));

			return false;
		}

		// Install
		if (!$this->config->get('module_emailtemplate_installed')) {
			// Auto upgrade
			if ($this->model_extension_module_emailtemplate->getTotalDefaultTemplates() > 0) {
				$this->load->model('setting/setting');

				$module_data = $this->model_setting_setting->getSetting('module_emailtemplate');
				$module_data['module_emailtemplate_installed'] = 1;

				$this->model_setting_setting->editSetting('module_emailtemplate', $module_data);
			} else {
				return false;
			}
		}

		return true;
	}

	/**
	 * Opencart module install
	 */
	public function install() {
		$this->load->model('setting/setting');

		$settings = $this->model_setting_setting->getSetting('module_emailtemplate');
		$settings['module_emailtemplate_status'] = 1;

		$this->model_setting_setting->editSetting('module_emailtemplate', $settings);

		if ($this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "cron'")->num_rows !== 0) {
			if (file_exists(DIR_APPLICATION . 'model/extension/module/cron.php')) {
				$this->load->model('extension/module/cron');

				$model_cron = $this->model_extension_module_cron;
			} elseif (file_exists(DIR_APPLICATION . 'model/setting/cron.php')) {
				$this->load->model('setting/cron');

				$model_cron = $this->model_setting_cron;
			}

			if (!empty($model_cron)) {
				$cron = $model_cron->getCronByCode('module_emailtemplate');

				if (!$cron) {
					$model_cron->addCron('module_emailtemplate', 'hour', 'extension/module/emailtemplate/cron', 1);
				}
			}
        }

		$this->model_extension_module_emailtemplate->install();

		$this->model_extension_module_emailtemplate->updateModification('core');
	}

	/**
	 * Install module
	 */
	public function installer() {
		$modules = $this->db->query("SELECT * FROM `" . DB_PREFIX . "extension` WHERE `type` = 'module' AND `code` = 'emailtemplate'");

		if (!$modules->num_rows) {
			$this->session->data['success'] = $this->language->get('text_warning_install');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

        $this->data['heading_install'] = sprintf($this->language->get('heading_install'), EmailTemplate::getVersion());

        $this->_setTitle($this->data['heading_install']);

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$this->load->model('setting/setting');

			$stores = $this->model_extension_module_emailtemplate->getStores();

			foreach($stores as $store) {
				$store_config = $this->model_setting_setting->getSetting("config", $store['store_id']);

				$config_data = $this->model_extension_module_emailtemplate->getConfig(1);

				$config_data['emailtemplate_config_name'] = $store["name"];
				$config_data['emailtemplate_config_version'] = EmailTemplate::$version;
				$config_data['store_id'] = $store["store_id"];

				$this->model_setting_setting->deleteSetting('emailtemplate', $store["store_id"]);

				$store_logo = (is_array($store_config['config_logo'])) ? array_pop($store_config['config_logo']) : $store_config['config_logo'];
				if ($store_logo && file_exists(DIR_IMAGE . $store_logo)) {
					$config_data['emailtemplate_config_logo'] = $store_logo;

					list($config_data['emailtemplate_config_logo_width'], $config_data['emailtemplate_config_logo_height']) = getimagesize(DIR_IMAGE . $store_logo);
				}

				$replace_language_vars = defined('REPLACE_LANGUAGE_VARIABLES') ? REPLACE_LANGUAGE_VARIABLES : true;

				if ($replace_language_vars) {
                    if (!empty($store_config['config_url'])) {
                        $store_url = $store_config['config_url'];
                    } else {
                        $store_url = HTTPS_CATALOG ? HTTPS_CATALOG : HTTP_CATALOG;
                    }

                    $store_replace = array(
                        '{{ store_address }}' => $store_config['config_address'],
                        '{{ store_url }}' => $store_url,
                        '{{ store_telephone }}' => $store_config['config_telephone'],
                        '{{ contact_url }}' => $store_url . 'index.php?route=information/contact'
                    );

                    foreach(array('emailtemplate_config_head_text', 'emailtemplate_config_header_text', 'emailtemplate_config_page_footer_text', 'emailtemplate_config_footer_text') as $var) {
                        if (!empty($config_data[$var])) {
                            if (is_array($config_data[$var])) {
                                foreach($config_data[$var] as $i => $val) {
                                    if (is_string($val)) {
                                        $config_data[$var][$i] = str_replace(array_keys($store_replace), array_values($store_replace), $val);
                                    }
                                }
                            } elseif (is_string($config_data[$var])) {
                                $config_data[$var] = str_replace(array_keys($store_replace), array_values($store_replace), $config_data[$var]);
                            }
                        }
                    }
				}

				if ($store["store_id"] == 0) {
					$this->model_extension_module_emailtemplate->updateConfig(1, $config_data);
				} else {
					$this->model_extension_module_emailtemplate->cloneConfig(1, $config_data);
				}
			}

			if (isset($this->request->post['original_templates'])) {
				foreach (array_keys($this->request->post['original_templates']) as $key) {
					$template_info = $this->model_extension_module_emailtemplate->getTemplate($key);

					if (!$template_info) {
						$this->model_extension_module_emailtemplate->installTemplate($key);
					}
				}
			}

			$this->model_extension_module_emailtemplate->updateModification();

			$this->load->model('setting/setting');

			$module_data = array(
				'module_emailtemplate_status' => 1,
				'module_emailtemplate_installed' => 1
			);

			$this->model_setting_setting->editSetting('module_emailtemplate', $module_data);

			$this->session->data['success'] = sprintf($this->language->get('install_success'), $this->url->link('marketplace/modification/refresh', 'user_token='.$this->session->data['user_token'] . '&redirect=extension/module/emailtemplate', true));

			$this->response->redirect($this->url->link('extension/module/emailtemplate', 'user_token='.$this->session->data['user_token'], true));
		}

		$this->_messages();

		$this->_breadcrumbs();

		if (version_compare(PHP_VERSION, '5.6.0', '<')) {
			$this->data['error_warning'] = sprintf($this->language->get('error_php_version'), PHP_VERSION);
		}

		$this->data['action'] = $this->url->link('extension/module/emailtemplate/installer', 'user_token='.$this->session->data['user_token'], true);
		$this->data['cancel'] = $this->url->link('marketplace/extension', 'user_token='.$this->session->data['user_token'] . '&type=module', true);

		$this->_output('extension/module/emailtemplate/install');
	}

	/**
	 * Delete module settings for each store.
	 */
	public function uninstall() {
		if (!$this->user->hasPermission('modify', 'extension/module/emailtemplate')) {
			$this->session->data['error'] = $this->language->get('error_permission');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token='.$this->session->data['user_token'] . '&type=module', true));
		}

		$this->load->model('setting/store');
		$this->load->model('setting/setting');

		$stores = $this->model_setting_store->getStores();

		if ($stores) {
			foreach ($stores as $store) {
				$this->model_setting_setting->deleteSetting("emailtemplate", $store['store_id']);
			}
		}

		$this->model_setting_setting->deleteSetting('module_emailtemplate');

		if ($this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "cron'")->num_rows !== 0) {
			if (file_exists(DIR_APPLICATION . 'model/extension/module/cron.php')) {
				$this->load->model('extension/module/cron');

				$model_cron = $this->model_extension_module_cron;
			} elseif (file_exists(DIR_APPLICATION . 'model/setting/cron.php')) {
				$this->load->model('setting/cron');

				$model_cron = $this->model_setting_cron;
			}

			if (!empty($model_cron)) {
				$cron = $model_cron->getCronByCode('module_emailtemplate');

				if ($cron) {
					$model_cron->deleteCronByCode('module_emailtemplate');
				}
			}
        }

		$this->model_extension_module_emailtemplate->uninstall();

		$this->session->data['success'] = sprintf($this->language->get('uninstall_success'), $this->url->link('marketplace/modification/refresh', 'user_token='.$this->session->data['user_token'] . '&redirect=extension/module/emailtemplate', true));
	}

	/**
	 * Upgrade Extension
	 */
	public function upgrade() {
		$this->load->model('setting/setting');

		$module_data = array(
			'module_emailtemplate_status' => 1,
			'module_emailtemplate_installed' => 1,
		);

		$this->model_setting_setting->editSetting('module_emailtemplate', $module_data);

		$this->model_extension_module_emailtemplate->upgrade();

		$this->session->data['success'] = sprintf($this->language->get('upgrade_success'), $this->url->link('marketplace/modification/refresh', 'user_token='.$this->session->data['user_token'] . '&redirect=extension/module/emailtemplate', true));

		$this->response->redirect($this->url->link('extension/module/emailtemplate', 'user_token='.$this->session->data['user_token'], true));
	}


	/**
	 * Get Extension Form
	 */
	private function _config_form() {
		$this->_breadcrumbs(array('heading_config' => array(
			'link' => 'extension/module/emailtemplate/config',
			'params' => '&id='.$this->request->get['id']
		)));

		$this->load->model('localisation/language');

		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		$emailtemplate_config = $this->model_extension_module_emailtemplate->getConfig($this->request->get['id']);

		if (!$emailtemplate_config) {
			$this->response->redirect($this->url->link('extension/module/emailtemplate', 'user_token='.$this->session->data['user_token'], true));
		}

		$this->data['id'] = $this->data['emailtemplate_config_id'] = $this->request->get['id'];

		$this->data['emailtemplate_config'] = array();

		foreach(EmailTemplateConfigDAO::describe() as $col => $type) {
			$key = (strpos($col, 'emailtemplate_config_') === 0 && substr($col, -3) != '_id') ? substr($col, 21) : $col;

			if (isset($this->request->post[$col])) {
				$val = $this->request->post[$col];
			} elseif (isset($emailtemplate_config[$col])) {
				$val = $emailtemplate_config[$col];
			} else {
				$val = '';
			}

			$this->data['emailtemplate_config'][$key] = $val;
		}

		// filter languages if condition set.
		if ($emailtemplate_config['language_id']) {
			$this->data['config_languages'] = array();

			foreach($this->data['languages'] as $language) {
				if ($emailtemplate_config['language_id'] == $language['language_id']) {
					$this->data['config_languages'][] = $language;
					break;
				}
			}
		}
		if (empty($this->data['config_languages'])) {
			$this->data['config_languages'] = $this->data['languages'];
		}

		$this->data['setting'] = array();

		/*if (isset($this->request->post['setting']['emailtemplate_token'])) {
			$this->data['setting']['emailtemplate_token'] = $this->request->post['setting']['emailtemplate_token'];
		} elseif ($this->config->get('emailtemplate_token')) {
			$this->data['setting']['emailtemplate_token'] = $this->config->get('emailtemplate_token');
		} else {
			$this->data['setting']['emailtemplate_token'] = sha1(uniqid(mt_rand(), 1));
		}*/

		$this->load->model('tool/image');

		if (!empty($emailtemplate_config['emailtemplate_config_modified'])) {
			$modified = strtotime($emailtemplate_config['emailtemplate_config_modified']);

			if (date('Ymd') == date('Ymd', $modified)) {
				$this->data['emailtemplate_config']['modified'] = date($this->language->get('time_format'), $modified);
			} else {
				$this->data['emailtemplate_config']['modified'] = date($this->language->get('date_format_short'), $modified);
			}
		}

		if ($this->data['emailtemplate_config']['logo'] && file_exists(DIR_IMAGE . $this->data['emailtemplate_config']['logo'])) {
            $this->data['emailtemplate_config']['logo_thumb'] = $this->model_tool_image->resize($this->data['emailtemplate_config']['logo'], 100, 100);
		} else {
			$this->data['emailtemplate_config']['logo_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		if ($this->data['emailtemplate_config']['header_bg_image'] && file_exists(DIR_IMAGE . $this->data['emailtemplate_config']['header_bg_image'])) {
			$this->data['emailtemplate_config']['header_bg_image_thumb'] = $this->model_tool_image->resize($this->data['emailtemplate_config']['header_bg_image'], 100, 100);
		} else {
			$this->data['emailtemplate_config']['header_bg_image_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		if ($this->data['emailtemplate_config']['body_bg_image'] && file_exists(DIR_IMAGE . $this->data['emailtemplate_config']['body_bg_image'])) {
			$this->data['emailtemplate_config']['body_bg_image_thumb'] = $this->model_tool_image->resize($this->data['emailtemplate_config']['body_bg_image'], 100, 100);
		} else {
			$this->data['emailtemplate_config']['body_bg_image_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		foreach(array('footer_text', 'header_html', 'head_text', 'page_footer_text') as $var) {
			if (!isset($this->data['emailtemplate_config'][$var]) || !is_array($this->data['emailtemplate_config'][$var])) {
				$this->data['emailtemplate_config'][$var] = array();
			}

			foreach ($this->data['languages'] as $language) {
				if (isset($this->data['emailtemplate_config'][$var][$language['language_id']])) {
					$val = html_entity_decode($this->data['emailtemplate_config'][$var][$language['language_id']], ENT_QUOTES, 'UTF-8');
				} else {
					$val = '';
				}

				$this->data['emailtemplate_config'][$var][$language['language_id']] = $val;
			}
		}

		$preference_types = array('essential', 'notification', 'newsletter');

		if (!isset($this->data['emailtemplate_config']['preference_text']) || !is_array($this->data['emailtemplate_config']['preference_text'])) {
			$this->data['emailtemplate_config']['preference_text'] = array();
		}

		foreach ($preference_types as $var) {
			if (!isset($this->data['emailtemplate_config']['preference_text'][$var]) || !is_array($this->data['emailtemplate_config']['preference_text'][$var])) {
				$this->data['emailtemplate_config']['preference_text'][$var] = array();
			}

			foreach ($this->data['languages'] as $language) {
				if (isset($this->data['emailtemplate_config']['preference_text'][$var][$language['language_id']])) {
					$val = $this->data['emailtemplate_config']['preference_text'][$var][$language['language_id']];
				} else {
					$val = '';
				}

				$this->data['emailtemplate_config']['preference_text'][$var][$language['language_id']] = $val;
			}
		}

		foreach(array(
			        'header_border_top', 'header_border_bottom', 'header_border_right', 'header_border_left',
			        'footer_border_top', 'footer_border_bottom', 'footer_border_right', 'footer_border_left',
			        'page_border_top', 'page_border_bottom', 'page_border_right', 'page_border_left',
			        'showcase_border_top', 'showcase_border_bottom', 'showcase_border_right', 'showcase_border_left'
		        ) as $var) {
			if (!isset($this->data['emailtemplate_config'][$var])) {
				$this->data['emailtemplate_config'][$var] = '';
			}
			if (!is_array($this->data['emailtemplate_config'][$var])) {
				$this->data['emailtemplate_config'][$var] = explode(', ', $this->data['emailtemplate_config'][$var]);
			}
			if (!is_array($this->data['emailtemplate_config'][$var])) {
				$this->data['emailtemplate_config'][$var] = array($this->data['emailtemplate_config'][$var], '');
			}
			if (!isset($this->data['emailtemplate_config'][$var][0]) || $this->data['emailtemplate_config'][$var][0] == '') {
				$this->data['emailtemplate_config'][$var][0] = 0;
			}
			if (!isset($this->data['emailtemplate_config'][$var][1]) || $this->data['emailtemplate_config'][$var][1] == '') {
				$this->data['emailtemplate_config'][$var][1] = '';
			}
		}

        foreach (array('header_spacing', 'footer_spacing', 'page_spacing') as $var) {
            if (!isset($this->data['emailtemplate_config'][$var])) {
                $this->data['emailtemplate_config'][$var] = '';
            }
            if (!is_array($this->data['emailtemplate_config'][$var])) {
                $this->data['emailtemplate_config'][$var] = explode(', ', $this->data['emailtemplate_config'][$var]);
            }
            if (!isset($this->data['emailtemplate_config'][$var][0]) || $this->data['emailtemplate_config'][$var][0] == '') {
                $this->data['emailtemplate_config'][$var][0] = 0;
            }
            if (!isset($this->data['emailtemplate_config'][$var][1]) || $this->data['emailtemplate_config'][$var][1] == '') {
                $this->data['emailtemplate_config'][$var][1] = 0;
            }
        }

		foreach (array('header_padding', 'page_padding', 'footer_padding', 'showcase_padding', 'header_border_radius', 'footer_border_radius', 'page_border_radius', 'showcase_border_radius') as $var) {
			if (!isset($this->data['emailtemplate_config'][$var])) {
				$this->data['emailtemplate_config'][$var] = '';
			}
			if ($this->data['emailtemplate_config'][$var] && is_string($this->data['emailtemplate_config'][$var])) {
				$this->data['emailtemplate_config'][$var] = explode(', ', $this->data['emailtemplate_config'][$var]);
			}
			if (!is_array($this->data['emailtemplate_config'][$var])) {
				$this->data['emailtemplate_config'][$var] = array((int)$this->data['emailtemplate_config'][$var]);
			}
			for ($i = 0; $i < 4; $i++) {
				if (!isset($this->data['emailtemplate_config'][$var][$i]) || $this->data['emailtemplate_config'][$var][$i] == '') {
					$this->data['emailtemplate_config'][$var][$i] = 0;
				}
			}
		}

		if (defined("HTTP_IMAGE")) {
			$url =  HTTP_IMAGE;
		} elseif ($this->config->get('config_url')) {
			$url = $this->config->get('config_url') . 'image/';
		} else {
			$url = HTTP_CATALOG . 'image/';
		}

		$showcase_setting_defaults = array(
			'description' => 0,
			'cycle' => 1,
			'rating' => 1,
			'related' => 1,
			'limit' => 4,
			'price' => 1,
			'price_tax' => 0,
			'image' => 1,
			'image_width' => 100,
			'image_height' => 100,
			'per_row' => 4,
		);

		if (!isset($this->data['emailtemplate_config']['showcase_setting']) || !is_array($this->data['emailtemplate_config']['showcase_setting'])) {
			$this->data['emailtemplate_config']['showcase_setting'] = $showcase_setting_defaults;
		} elseif ($this->data['emailtemplate_config']['showcase_setting'] && !is_array($this->data['emailtemplate_config']['showcase_setting'])) {
			$this->data['emailtemplate_config']['showcase_setting'] = unserialize(base64_decode($this->data['emailtemplate_config']['showcase_setting']));
		}

		foreach ($showcase_setting_defaults as $var => $val) {
			if (!isset($this->data['emailtemplate_config']['showcase_setting'][$var])) {
				$this->data['emailtemplate_config']['showcase_setting'][$var] = $val;
			}
		}

		$order_products_defaults = array(
			'description' => 0,
			'rating' => 0,
			'model' => 1,
			'sku' => 0,
			'price' => 1,
			'image' => 1,
			'quantity_column' => 1,
			'admin_stock' => 1,
			'option_image' => 1,
			'option_length' => 120,
			'image_width' => 100,
			'image_height' => 100,
			'layout' => 'default',
		);

		if (!isset($this->data['emailtemplate_config']['order_products']) || !is_array($this->data['emailtemplate_config']['order_products'])) {
			$this->data['emailtemplate_config']['order_products'] = $order_products_defaults;
		} elseif ($this->data['emailtemplate_config']['order_products'] && !is_array($this->data['emailtemplate_config']['order_products'])) {
			$this->data['emailtemplate_config']['order_products'] = unserialize(base64_decode($this->data['emailtemplate_config']['order_products']));
		}

		foreach ($order_products_defaults as $var => $val) {
			if (!isset($this->data['emailtemplate_config']['order_products'][$var])) {
				$this->data['emailtemplate_config']['order_products'][$var] = $val;
			}
		}

		$order_update_defaults = array(
			'description' => 0,
			'rating' => 0,
			'model' => 1,
			'sku' => 0,
			'price' => 1,
			'image' => 1,
			'quantity_column' => 1,
			'option' => 1,
			'option_image' => 1,
			'option_length' => 120,
			'image_width' => 100,
			'image_height' => 100,
			'layout' => 'default',
		);

		if (!isset($this->data['emailtemplate_config']['order_update']) || !is_array($this->data['emailtemplate_config']['order_update'])) {
			$this->data['emailtemplate_config']['order_update'] = $order_update_defaults;
		} elseif ($this->data['emailtemplate_config']['order_update'] && !is_array($this->data['emailtemplate_config']['order_update'])) {
			$this->data['emailtemplate_config']['order_update'] = unserialize(base64_decode($this->data['emailtemplate_config']['order_update']));
		}

		foreach ($order_update_defaults as $var => $val) {
			if (!isset($this->data['emailtemplate_config']['order_update'][$var])) {
				$this->data['emailtemplate_config']['order_update'][$var] = $val;
			}
		}

		$cart_setting_defaults = array(
			'description' => 0,
			'rating' => 0,
			'model' => 1,
			'sku' => 0,
			'price' => 1,
			'image' => 1,
			'quantity_column' => 1,
			'option' => 1,
			'option_image' => 1,
			'option_length' => 120,
			'image_width' => 100,
			'image_height' => 100,
			'layout' => 'default',
		);

		if (!isset($this->data['emailtemplate_config']['cart_setting']) || !is_array($this->data['emailtemplate_config']['cart_setting'])) {
			$this->data['emailtemplate_config']['cart_setting'] = $cart_setting_defaults;
		} elseif ($this->data['emailtemplate_config']['cart_setting'] && !is_array($this->data['emailtemplate_config']['cart_setting'])) {
			$this->data['emailtemplate_config']['cart_setting'] = unserialize(base64_decode($this->data['emailtemplate_config']['cart_setting']));
		}

		foreach ($cart_setting_defaults as $var => $val) {
			if (!isset($this->data['emailtemplate_config']['cart_setting'][$var])) {
				$this->data['emailtemplate_config']['cart_setting'][$var] = $val;
			}
		}

		foreach (array('top','bottom','left','right') as $var) {
			if ($this->data['emailtemplate_config']['shadow_'.$var] && is_string($this->data['emailtemplate_config']['shadow_'.$var])) {
				$this->data['emailtemplate_config']['shadow_'.$var] = unserialize(base64_decode($this->data['emailtemplate_config']['shadow_'.$var]));
			} elseif (!is_array($this->data['emailtemplate_config']['shadow_'.$var])) {
				$this->data['emailtemplate_config']['shadow_'.$var] = array('start' => '', 'end' => '', 'overlap' => 0, 'length' => 0);
			}
		}

        foreach (array('left', 'right') as $col) {
            if (!empty($this->data['emailtemplate_config']['shadow_top'][$col.'_img']) && file_exists(DIR_IMAGE . $this->data['emailtemplate_config']['shadow_top'][$col.'_img'])) {
                $this->data['emailtemplate_config']['shadow_top'][$col.'_thumb'] = $url . $this->data['emailtemplate_config']['shadow_top'][$col.'_img'];
            } else {
                $this->data['emailtemplate_config']['shadow_top'][$col.'_img'] = '';
                $this->data['emailtemplate_config']['shadow_top'][$col.'_thumb'] = '';
            }

            if (!empty($this->data['emailtemplate_config']['shadow_bottom'][$col.'_img']) && file_exists(DIR_IMAGE . $this->data['emailtemplate_config']['shadow_bottom'][$col.'_img'])) {
                $this->data['emailtemplate_config']['shadow_bottom'][$col.'_thumb'] = $url . $this->data['emailtemplate_config']['shadow_bottom'][$col.'_img'];
            } else {
                $this->data['emailtemplate_config']['shadow_bottom'][$col.'_img'] = '';
                $this->data['emailtemplate_config']['shadow_bottom'][$col.'_thumb'] = '';
            }
        }

		$this->data['action_delete'] = $this->url->link('extension/module/emailtemplate/config', 'user_token='.$this->session->data['user_token'] . '&id=' . $this->request->get['id'] . '&action=delete', true);

		if ($this->request->get['id'] == 1) {
			$this->_setTitle($this->language->get('button_default') . ' ' . $this->language->get('heading_config'));
		} else {
			$this->_setTitle($this->data['emailtemplate_config']['name'] . ' ' . $this->language->get('heading_config'));

			$this->data['action_default'] = $this->url->link('extension/module/emailtemplate/config', 'user_token='.$this->session->data['user_token'] . '&id=1', true);
		}

		$this->load->model('localisation/language');

		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		$this->data['stores'] = $this->model_extension_module_emailtemplate->getStores();

		$this->load->model('customer/customer_group');

		$this->data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		$this->data['url_insert_config'] = $this->url->link('extension/module/emailtemplate/config', 'user_token='.$this->session->data['user_token'], true);

		$this->data['url_send_email'] = $this->url->link('extension/module/emailtemplate/send_email', 'user_token='.$this->session->data['user_token'] . '&emailtemplate_config_id=' . $this->request->get['id'], true);

		$emailtemplate_configs = $this->model_extension_module_emailtemplate->getConfigs(array(), true, true);

		if ($emailtemplate_configs) {
			$this->data['action_configs'] = array();

			foreach($emailtemplate_configs as $row) {
				$this->data['action_configs'][] = array(
					'id' => $row['emailtemplate_config_id'],
					'name' => $row['emailtemplate_config_name'],
					'url' =>$this->url->link('extension/module/emailtemplate/config', 'user_token='.$this->session->data['user_token'] . '&id=' . $row['emailtemplate_config_id'], true)
				);
			}
		}

		$this->data['action'] = $this->url->link('extension/module/emailtemplate/config', 'user_token='.$this->session->data['user_token'] . '&id=' . $this->request->get['id'], true);

		$this->data['cancel'] = $this->url->link('extension/module/emailtemplate', 'user_token='.$this->session->data['user_token'], true);

		$this->load->model('tool/image');

		$this->data['no_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$this->data['no_shadow_image'] = $this->model_tool_image->resize('no_image.png', 17, 17);

		# Installed Themes
		$this->data['themes'] = array();
		$directories = glob(DIR_CATALOG . 'view/theme/*', GLOB_ONLYDIR);
		foreach ($directories as $directory) {
			$this->data['themes'][] = basename($directory);
		}

		# Order Product Templates
		$this->data['order_products_templates'] = array();
		if (is_dir(DIR_CATALOG . 'view/theme/default/template/extension/module/emailtemplate/order_products')) {
			$files = glob(DIR_CATALOG . 'view/theme/default/template/extension/module/emailtemplate/order_products/*.twig', GLOB_BRACE);
			foreach ($files as $file) {
				$this->data['order_products_templates'][] = pathinfo($file, PATHINFO_FILENAME);
			}
		}

		if (!empty($this->data['emailtemplate_config']['language_id'])) {
			$language_id = $this->data['emailtemplate_config']['language_id'];
		} else {
			$language_id = $this->config->get('config_language_id');
		}

		if (isset($this->data['emailtemplate_config']['store_id'])) {
			$store_id = $this->data['emailtemplate_config']['store_id'];
		} else {
			$store_id = 0;
		}

		$customer_group_id = $this->config->get('config_customer_group_id');

		if ($this->data['emailtemplate_config']['showcase'] == 'products' && $this->data['emailtemplate_config']['showcase_selection']) {
			$showcase_selection = explode(',', $this->data['emailtemplate_config']['showcase_selection']);

			if ($showcase_selection) {
				$this->load->model('extension/module/emailtemplate/product');

				$this->data['showcase_selection'] = array();

				$product_condition = array('language_id' => $language_id, 'store_id' => $store_id, 'customer_group_id' => $customer_group_id);

				foreach($showcase_selection as $product_id) {
					$product_info = $this->model_extension_module_emailtemplate_product->getProduct($product_id, $product_condition);

                    if (!$product_info) {
                        continue;
                    }

					$this->data['showcase_selection'][] = array(
						'product_id' => (int)$product_id,
						'name' => $product_info ? $product_info['name'] : $this->language->get('text_missing')
					);
				}
			}
		}

        $this->data['config_email'] = $this->config->get('config_email');

        $data['summernote_language'] = $this->language->get('summernote');
        if ($data['summernote_language'] == 'summernote') $data['summernote_language'] = 'en';
	}

	/**
	 * Get create config form
	 */
	private function _config_form_create() {
		$this->_messages();

		$this->data['action'] = $this->url->link('extension/module/emailtemplate/config', 'user_token='.$this->session->data['user_token'] . '&action=create', true);
		$this->data['cancel'] = $this->url->link('extension/module/emailtemplate', 'user_token='.$this->session->data['user_token'], true);

		$this->data['insertMode'] = true;

		$this->data['emailtemplate_config'] = array();

		foreach(EmailTemplateConfigDAO::describe() as $col => $type) {
			$key = (strpos($col, 'emailtemplate_config_') === 0 && substr($col, -3) != '_id') ? substr($col, 21) : $col;
			if (isset($this->request->post[$col])) {
				$this->data['emailtemplate_config'][$key] = $this->request->post[$col];
			} else {
				$this->data['emailtemplate_config'][$key] = '';
			}
		}

		$this->data['emailtemplate_configs'] = $this->model_extension_module_emailtemplate->getConfigs(array(), true, true);

		$this->load->model('localisation/language');

		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		$this->data['stores'] = $this->model_extension_module_emailtemplate->getStores();

		$this->load->model('customer/customer_group');

		$this->data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

        $data['summernote_language'] = $this->language->get('summernote');
        if ($data['summernote_language'] == 'summernote') $data['summernote_language'] = 'en';

		$this->_setTitle($this->language->get('heading_config_create'));

		$this->_breadcrumbs(array('heading_config_create' => array(
			'link' => 'extension/module/emailtemplate/config'
		)));
	}

	/**
	 * Get Templates
	 */
	private function _config_style(array $data) {
		foreach(array('top', 'bottom', 'left', 'right') as $place) {
			$data['emailtemplate_config_header_border_'.$place] = '';
			$data['emailtemplate_config_header_border_radius_'.$place] = '';

			$data['emailtemplate_config_footer_border_'.$place] = '';
			$data['emailtemplate_config_footer_border_radius_'.$place] = '';

			$data['emailtemplate_config_page_border_'.$place] = '';
			$data['emailtemplate_config_page_border_radius_'.$place] = '';

			$data['emailtemplate_config_showcase_border_'.$place] = '';
			$data['emailtemplate_config_showcase_border_radius_'.$place] = '';

			foreach(array('length', 'overlap', 'start', 'end', 'left_img', 'right_img') as $var) {
				$data['emailtemplate_config_shadow_'.$place][$var] = '';
				$data['emailtemplate_config_shadow_'.$place][$var] = '';
				$data['emailtemplate_config_shadow_'.$place][$var] = '';
				$data['emailtemplate_config_shadow_'.$place][$var] = '';
			}
		}

		$data['emailtemplate_config_header_section_bg_color'] = '';
		$data['emailtemplate_config_header_border_radius'] = '';
		$data['emailtemplate_config_header_border_top'] = '';
		$data['emailtemplate_config_header_border_bottom'] = '';
		$data['emailtemplate_config_header_border_left'] = '';
		$data['emailtemplate_config_header_border_right'] = '';

		$data['emailtemplate_config_body_bg_color'] = '';
		$data['emailtemplate_config_body_section_bg_color'] = '';
		$data['emailtemplate_config_page_border_radius'] = '';
		$data['emailtemplate_config_page_border_top'] = '';
		$data['emailtemplate_config_page_border_bottom'] = '';
		$data['emailtemplate_config_page_border_left'] = '';
		$data['emailtemplate_config_page_border_right'] = '';

		$data['emailtemplate_config_showcase_bg_color'] = '';
		$data['emailtemplate_config_showcase_section_bg_color'] = '';
		$data['emailtemplate_config_showcase_border_radius'] = '';
		$data['emailtemplate_config_showcase_border_top'] = '';
		$data['emailtemplate_config_showcase_border_bottom'] = '';
		$data['emailtemplate_config_showcase_border_left'] = '';
		$data['emailtemplate_config_showcase_border_right'] = '';

		$data['emailtemplate_config_footer_bg_color'] = '';
		$data['emailtemplate_config_footer_section_bg_color'] = '';
		$data['emailtemplate_config_footer_border_radius'] = '';
		$data['emailtemplate_config_footer_border_top'] = '';
		$data['emailtemplate_config_footer_border_bottom'] = '';
		$data['emailtemplate_config_footer_border_left'] = '';
		$data['emailtemplate_config_footer_border_right'] = '';

		switch($data['emailtemplate_config_style']) {
			case 'white':
				$data['emailtemplate_config_wrapper_tpl'] = '_main.twig';
				$data['emailtemplate_config_body_bg_color'] = '#FFFFFF';
				$data['emailtemplate_config_page_bg_color'] = '#F9F9F9';
				$data['emailtemplate_config_body_font_color'] = '#333333';

				$data['emailtemplate_config_shadow_top'] = array(
					'length' => '',
					'overlap' => '',
					'start' => '',
					'end' => '',
					'left_img' => '',
					'right_img' => ''
				);

				$data['emailtemplate_config_shadow_bottom'] = array(
					'length' => 9,
					'overlap' => 8,
					'start' => '#d4d4d4',
					'end' => '#ffffff',
					'left_img' => 'catalog/emailtemplate/white/bottom_left.png',
					'right_img' => 'catalog/emailtemplate/white/bottom_right.png'
				);

				$data['emailtemplate_config_shadow_left'] = array(
					'length' => 9,
					'overlap' => 8,
					'start' => '#ffffff',
					'end' => '#d4d4d4'
				);

				$data['emailtemplate_config_shadow_right'] = array(
					'length' => 9,
					'overlap' => 8,
					'start' => '#d4d4d4',
					'end' => '#ffffff'
				);
				break;

			case 'page':
				$data['emailtemplate_config_wrapper_tpl'] = '_main.twig';
				$data['emailtemplate_config_body_bg_color'] = '#F9F9F9';
				$data['emailtemplate_config_page_bg_color'] = '#FFFFFF';
				$data['emailtemplate_config_showcase_bg_color'] = '#FFFFFF';
				$data['emailtemplate_config_body_font_color'] = '#333333';

				$data['emailtemplate_config_shadow_top'] = array(
					'length' => '',
					'overlap' => '',
					'start' => '',
					'end' => '',
					'left_img' => '',
					'right_img' => ''
				);

				$data['emailtemplate_config_shadow_bottom'] = array(
					'length' => 9,
					'overlap' => 8,
					'start' => '#d4d4d4',
					'end' => '#f8f8f8',
					'left_img' => 'catalog/emailtemplate/gray/bottom_left.png',
					'right_img' => 'catalog/emailtemplate/gray/bottom_right.png'
				);

				$data['emailtemplate_config_shadow_left'] = array(
					'length' => 9,
					'overlap' => 8,
					'start' => '#f8f8f8',
					'end' => '#d4d4d4'
				);

				$data['emailtemplate_config_shadow_right'] = array(
					'length' => 9,
					'overlap' => 8,
					'start' => '#d4d4d4',
					'end' => '#f8f8f8'
				);
				break;

			case 'clean':
				$data['emailtemplate_config_wrapper_tpl'] = '_main.twig';
				$data['emailtemplate_config_body_bg_color'] = '#FFFFFF';
				$data['emailtemplate_config_page_bg_color'] = '#FFFFFF';
				$data['emailtemplate_config_showcase_bg_color'] = '#FFFFFF';
				$data['emailtemplate_config_body_font_color'] = '#333333';
				break;

			case 'border':
				$data['emailtemplate_config_wrapper_tpl'] = '_main.twig';
				$data['emailtemplate_config_body_bg_color'] = '#FFFFFF';
				$data['emailtemplate_config_page_bg_color'] = '#FFFFFF';
				$data['emailtemplate_config_showcase_bg_color'] = '#FFFFFF';
				$data['emailtemplate_config_body_font_color'] = '#333333';

                $data['emailtemplate_config_page_border_bottom'] =
                $data['emailtemplate_config_page_border_left'] =
                $data['emailtemplate_config_page_border_right'] = '2, #cfcfcf';

				foreach(array('bottom', 'left', 'right') as $place) {
					$data['emailtemplate_config_shadow_'.$place] = array(
                        'length' => '',
                        'overlap' => '',
                        'start' => '',
                        'end' => '',
                        'left_img' => '',
                        'right_img' => ''
                    );
				}
				break;

			case 'crisp':
				$data['emailtemplate_config_wrapper_tpl'] = '_main.twig';
				$data['emailtemplate_config_body_bg_color'] = '#575757';
				$data['emailtemplate_config_body_section_bg_color'] = '#FFFFFF';
				$data['emailtemplate_config_body_font_color'] = '#333333';

				$data['emailtemplate_config_header_bg_color'] = '';
				$data['emailtemplate_config_header_bg_image'] = '';
				$data['emailtemplate_config_header_section_bg_color'] = '#FFFFFF';

				$data['emailtemplate_config_page_bg_color'] = '#FFFFFF';
				$data['emailtemplate_config_page_border_radius'] = '8, 8, 0, 0';

				$data['emailtemplate_config_page_border_bottom'] =
                $data['emailtemplate_config_page_border_left'] =
                $data['emailtemplate_config_page_border_right'] = '2, #cfcfcf';

				$data['emailtemplate_config_showcase_bg_color'] = '#FFFFFF';
				$data['emailtemplate_config_showcase_section_bg_color'] = '#e6e6e6';
				$data['emailtemplate_config_showcase_border_bottom'] =
				$data['emailtemplate_config_showcase_border_left'] =
				$data['emailtemplate_config_showcase_border_right'] = '2, #cfcfcf';

				$data['emailtemplate_config_footer_bg_color'] = '#575757';

				foreach(array('top', 'bottom', 'left', 'right') as $place) {
					$data['emailtemplate_config_shadow_'.$place] = array(
						'length' => '',
						'overlap' => '',
						'start' => '',
						'end' => '',
						'left_img' => '',
						'right_img' => ''
					);
				}
				break;

			case 'sections':
				$data['emailtemplate_config_wrapper_tpl'] = '_main.twig';
				$data['emailtemplate_config_body_bg_color'] = '#FFFFFF';
				$data['emailtemplate_config_page_bg_color'] = '#dfdfdf';
				$data['emailtemplate_config_footer_bg_color'] = '#bcbcbc';
				$data['emailtemplate_config_body_section_bg_color'] = '#FFFFFF';
				$data['emailtemplate_config_body_font_color'] = '#333333';
				$data['emailtemplate_config_header_section_bg_color'] = $data['emailtemplate_config_header_bg_color'];

				foreach(array('top', 'bottom', 'left', 'right') as $place) {
					$data['emailtemplate_config_shadow_'.$place] = array(
						'length' => '',
						'overlap' => '',
						'start' => '',
						'end' => '',
						'left_img' => '',
						'right_img' => ''
					);
				}
				break;
		}

		return $data;
	}

	/**
	 * Get Template Shortcodes
	 */
	public function shortcodes(){
		if (!isset($this->request->get['id'])) {
			return false;
		}

		$this->data['shortcode_emailtemplate_id'] = $this->request->get['id'];

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'code';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = $this->config->get('config_limit_admin');
		}

		$filter = array(
			'emailtemplate_id'  => $this->data['shortcode_emailtemplate_id'],
			'start' => ($page - 1) * $limit,
			'limit' => $limit,
			'sort'  => $sort,
			'order' => $order
		);

		if (empty($this->request->post['filter_shortcodes_language'])) {
			$filter['emailtemplate_shortcode_type'] = 'auto';
		} else {
			$filter['emailtemplate_shortcode_type'] = '';
		}

		if (!empty($this->request->post['filter_shortcodes_search'])) {
			$filter['filter_shortcodes_search'] = $this->request->post['filter_shortcodes_search'];
		}

		$results = $this->model_extension_module_emailtemplate->getTemplateShortcodes($filter);

		$total = $this->model_extension_module_emailtemplate->getTotalTemplateShortcodes($filter);

		$this->data['shortcodes'] = array();

		foreach ($results as $item) {
			$example = $item['emailtemplate_shortcode_example'];
			if (is_string($example)) {
				$example = html_entity_decode($item['emailtemplate_shortcode_example'], ENT_QUOTES, 'UTF-8');

				if (strlen($example) > 300) {
					$example = strip_tags($example);
					$example = substr($example, 0, 300);
					$example = substr($example, 0, strrpos($example, ' '));
					$example .= '...';
				}
			}

			$this->data['shortcodes'][] = array(
				'id' 	   => $item['emailtemplate_shortcode_id'],
				'code' 	   => $item['emailtemplate_shortcode_code'],
				'type' 	   => $item['emailtemplate_shortcode_type'],
				'example'  => $example,
				'url_edit'  => $this->url->link('extension/module/emailtemplate/template_shortcode', 'user_token='.$this->session->data['user_token'].'&id='.$item['emailtemplate_shortcode_id'], true)
			);
		}

		$url = '';
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['id'])) {
			$url .= '&id='.$this->request->get['id'];
		}

		$link = $this->url->link('extension/module/emailtemplate/shortcodes', 'user_token='.$this->session->data['user_token'] . $url . '&page={page}', true) . '#tab-shortcodes';

		$this->_renderPagination($link, $page, $total, $limit, 'select');

		$url = '';
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}
		if (isset($this->request->get['id'])) {
			$url .= '&id='.$this->request->get['id'];
		}

		$this->data['sort_code'] = $this->url->link('extension/module/emailtemplate/shortcodes', 'user_token='.$this->session->data['user_token'] . '&sort=code' . $url, true);
		$this->data['sort_example'] = $this->url->link('extension/module/emailtemplate/shortcodes', 'user_token='.$this->session->data['user_token'] . '&sort=example' . $url, true);

		$url = '';
		if (isset($this->request->get['id'])) {
			$url .= '&id='.$this->request->get['id'];
		}
		if ($sort) {
			$url .= '&sort=' . $sort;
		}
		if ($order) {
			$url .= '&order=' . $order;
		}

		$this->data['action'] = $this->url->link('extension/module/emailtemplate/shortcodes', 'user_token='.$this->session->data['user_token'] . $url, true);

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->response->setOutput($this->load->view('extension/module/emailtemplate/_shortcode', $this->data));
	}

	/**
	 * Get template form
	 */
	private function _template_form() {
		if (empty($this->request->get['id'])) {
			$this->response->redirect($this->url->link('extension/module/emailtemplate', 'user_token='.$this->session->data['user_token'], true));
		}

		$this->load->model('tool/image');

		$this->_messages();

		$this->load->model('localisation/language');

		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		$emailtemplate = $this->model_extension_module_emailtemplate->getTemplate($this->request->get['id'], 0);

		if (!$emailtemplate) {
		    $this->session->data['error'] = sprintf($this->language->get('error_template_missing'), $this->request->get['id']);

			$this->response->redirect($this->url->link('extension/module/emailtemplate', 'user_token='.$this->session->data['user_token'], true));
		}

		$results = $this->model_extension_module_emailtemplate->getTemplateDescription(array('emailtemplate_id' => $emailtemplate['emailtemplate_id']));

		$emailtemplate_descriptions = array();
		foreach($results as $row) {
			$emailtemplate_descriptions[$row['language_id']] = $row;
		}

		foreach($this->data['languages'] as $language) {
			if (isset($emailtemplate_descriptions[$language['language_id']])) {
				$emailtemplate['descriptions'][$language['language_id']] = $emailtemplate_descriptions[$language['language_id']];
			} else {
				$emailtemplate['descriptions'][$language['language_id']] = current($emailtemplate_descriptions);
				$emailtemplate['descriptions'][$language['language_id']]['language_id'] = $language['language_id'];
			}
		}

		$config = $this->model_extension_module_emailtemplate->getConfig(array(
			'store_id' => ($emailtemplate['store_id']) ? $emailtemplate['store_id'] : 0
		));

		if (!$config) {
			$config = $this->model_extension_module_emailtemplate->getConfig(1);
		}

		if (!$config) {
			trigger_error('Error: unable to find email template config');
			return false;
		}

		$this->data['emailtemplate_config'] = $config;

		// Default and similar templates
		$this->data['emailtemplates'] = array();

		$templates = $this->model_extension_module_emailtemplate->getTemplates(array('emailtemplate_key' => $emailtemplate['emailtemplate_key']));

		if ($templates) {
			foreach($templates as $template) {
				if ($template['emailtemplate_default']) {
					$this->data['default_emailtemplate_id'] = $template['emailtemplate_id'];

					$this->data['template_default_url'] = $this->url->link('extension/module/emailtemplate/template', 'user_token='.$this->session->data['user_token'] . '&id='.$template['emailtemplate_id'], true);
					$this->data['template_default_label'] = $template['emailtemplate_label'] . ' - ' . strip_tags($this->language->get('text_default'));
				} else {
					$this->data['emailtemplates'][] = array(
						'emailtemplate_id' => $template['emailtemplate_id'],
                        'emailtemplate_status' => $template['emailtemplate_status'],
                        'name' => $template['emailtemplate_label'],
						'url' => $this->url->link('extension/module/emailtemplate/template', 'user_token='.$this->session->data['user_token'] . '&id='.$template['emailtemplate_id'], true)
					);
				}
			}
		}

		$this->data['action'] = $this->url->link('extension/module/emailtemplate/template', 'user_token='.$this->session->data['user_token'] . '&id=' . $this->request->get['id'], true);

		$this->data['action_insert_template'] = $this->url->link('extension/module/emailtemplate/template', 'user_token='.$this->session->data['user_token'] . '&key=' . $emailtemplate['emailtemplate_key'], true);

        $this->data['title'] = $emailtemplate['emailtemplate_label'] . ' ' . $this->language->get('heading_template_edit') .
            ($emailtemplate['emailtemplate_default'] ? ' (' . strip_tags($this->language->get('text_default')) . ')': '');

        $this->_setTitle($this->data['title']);

		$this->_breadcrumbs(array('heading_template_edit' => array(
			'link' => 'extension/module/emailtemplate/template',
			'params' => '&id='.$this->request->get['id']
		)));

		$this->data['cancel'] = $this->url->link('extension/module/emailtemplate', 'user_token='.$this->session->data['user_token'], true);

		$this->data['action_event_add'] = $this->url->link('extension/module/emailtemplate/template_event', 'emailtemplate_key=' . $emailtemplate['emailtemplate_key'] . '&user_token='.$this->session->data['user_token'], true);

        $this->data['clear_cache_url'] = $this->url->link('extension/module/emailtemplate/clear', 'user_token='.$this->session->data['user_token'], true);
        $this->data['modification_url'] = $this->url->link('extension/module/emailtemplate/rebuild_modifications', 'user_token='.$this->session->data['user_token'], true);
        $this->data['logs_url'] = $this->url->link('extension/module/emailtemplate/logs', 'user_token='.$this->session->data['user_token'] . '&filter_emailtemplate_id=' . $emailtemplate['emailtemplate_id'], true);

		$this->data['emailtemplate_events'] = $this->model_extension_module_emailtemplate->getEmailTemplateEvents(array('emailtemplate_key' => $emailtemplate['emailtemplate_key']));

		$emailtemplate_shortcodes = $this->model_extension_module_emailtemplate->getTemplateShortcodes(array('emailtemplate_id' => $emailtemplate['emailtemplate_id']));

		if (!$emailtemplate_shortcodes) {
			$emailtemplate_shortcodes = $this->model_extension_module_emailtemplate->getTemplateShortcodes(array('emailtemplate_key' => $emailtemplate['emailtemplate_key']));
		}

		if ($emailtemplate_shortcodes) {
			$this->data['emailtemplate_shortcodes'] = $emailtemplate_shortcodes;
		}

		$this->load->model('setting/event');

		foreach($this->data['emailtemplate_events'] as $i => $emailtemplate_event) {
			$event_info = $this->model_setting_event->getEvent($emailtemplate_event['event_id']);

			if ($event_info) {
				$this->data['emailtemplate_events'][$i] = array_merge($event_info, $this->data['emailtemplate_events'][$i]);
				$this->data['emailtemplate_events'][$i]['status'] = $this->language->get($event_info['status'] ? 'text_enabled' : 'text_disabled');
			}
		}

		$this->data['emailtemplate'] = array();

		foreach(EmailTemplateDAO::describe() as $col => $type) {
			$key = (strpos($col, 'emailtemplate_') === 0 && substr($col, -3) != '_id') ? substr($col, 14) : $col;
			if (isset($this->request->post[$col])) {
				$this->data['emailtemplate'][$key] = $this->request->post[$col];
			} elseif (isset($emailtemplate[$col])) {
				$this->data['emailtemplate'][$key] = $emailtemplate[$col];
			} else {
				$this->data['emailtemplate'][$key] = '';
			}
		}

		$this->data['content_count'] = (int)$this->content_count;

		$descriptionCols = EmailTemplateDescriptionDAO::describe();

		$this->data['emailtemplate_description'] = array();

		foreach($this->data['languages'] as &$language) {
			$row = array();

			if ($language['language_id'] == $this->config->get('config_language_id')) {
				$language['default'] = 1;
			} else {
				$language['default'] = 0;
			}

			foreach($descriptionCols as $col => $type) {
				$key = (strpos($col, 'emailtemplate_description_') === 0) ? substr($col, 26) : $col;

				if (isset($this->request->post[$col][$language['language_id']])) {
					$value = $this->request->post[$col][$language['language_id']];
				} elseif (isset($emailtemplate['descriptions'][$language['language_id']][$col])) {
					$value = $emailtemplate['descriptions'][$language['language_id']][$col];
				} else {
					$value = '';
				}

				$row[$key] = $value;
			}

			$this->data['emailtemplate_description'][$language['language_id']] = $row;
		}
		unset($language);

		$modified = strtotime($this->data['emailtemplate']['modified']);
		if (date('Ymd') == date('Ymd', $modified)) {
			$this->data['emailtemplate']['modified'] = date($this->language->get('time_format'), $modified);
		} else {
			$this->data['emailtemplate']['modified'] = date($this->language->get('date_format_short'), $modified);
		}

		$this->data['emailtemplate_files'] = $this->model_extension_module_emailtemplate->getTemplateFiles();

		if ($this->data['emailtemplate']['emailtemplate_config_id']) {
			$this->data['config_url'] = $this->url->link('extension/module/emailtemplate/config', 'user_token='.$this->session->data['user_token'] . '&id=' . $this->data['emailtemplate']['emailtemplate_config_id'], true);
		} else {
			$this->data['config_url'] = $this->url->link('extension/module/emailtemplate/config', 'user_token='.$this->session->data['user_token'] . '&id=1', true);
		}

		$this->data['emailtemplate_configs'] = $this->model_extension_module_emailtemplate->getConfigs(array(), true, true);

		$this->data['no_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$this->data['stores'] = $this->model_extension_module_emailtemplate->getStores();

        $this->load->model('catalog/category');

        $this->data['categories'] = $this->model_catalog_category->getCategories(array());

        $this->load->model('catalog/manufacturer');

        $this->data['manufacturers'] = $this->model_catalog_manufacturer->getManufacturers(array());

		$this->load->model('customer/customer_group');

		$this->data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		// Extra options if order type OR key begins with 'order.'
		if ($this->data['emailtemplate']['type'] == 'order' || substr($this->data['emailtemplate']['key'], 0, 6) == 'order.') {
			$this->load->model('localisation/order_status');

			$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

			$this->load->model('setting/extension');

			$this->data['payment_methods'] = array();

			$extensions = $this->model_setting_extension->getInstalled('payment');

			foreach ($extensions as $code) {
				if ($this->config->has('payment_' . $code . '_status')) {
					$this->load->language('extension/payment/' . $code);

					$this->data['payment_methods'][] = array(
						'name'   => strip_tags($this->language->get('heading_title')),
						'code'   => $code
					);
				}
			}

			$this->data['shipping_methods'] = array();

			$extensions = $this->model_setting_extension->getInstalled('shipping');

			foreach ($extensions as $code) {
				if ($this->config->has('shipping_' . $code . '_status')) {
					$this->load->language('extension/shipping/' . $code);

					$this->data['shipping_methods'][] = array(
						'name'   => strip_tags($this->language->get('heading_title')),
						'code'   => $code
					);
				}
			}
		}

		if ($this->data['emailtemplate']['showcase'] == 'products' && $this->data['emailtemplate']['showcase_selection']) {
			$showcase_selection = explode(',', $this->data['emailtemplate']['showcase_selection']);

			if ($showcase_selection) {
				$this->load->model('extension/module/emailtemplate/product');

				$this->data['showcase_selection'] = array();

				foreach($showcase_selection as $product_id) {
					$product_info = $this->model_extension_module_emailtemplate_product->getProduct($product_id);

                    if (!$product_info) {
                        continue;
                    }

					$this->data['showcase_selection'][] = array(
						'product_id' => (int)$product_id,
						'name' => $product_info ? $product_info['name'] : $this->language->get('text_missing')
					);
				}
			}
		}

		$this->data['config_email'] = $this->config->get('config_email');

        $data['summernote_language'] = $this->language->get('summernote');
        if ($data['summernote_language'] == 'summernote') $data['summernote_language'] = 'en';
	}

	/**
	 * Get create template form
	 */
	private function _template_form_create() {
		$this->_messages();

		$this->data['action'] = $this->url->link('extension/module/emailtemplate/template', 'user_token='.$this->session->data['user_token'], true);
		$this->data['cancel'] = $this->url->link('extension/module/emailtemplate', 'user_token='.$this->session->data['user_token'], true);

		$this->data['user_token'] = $this->session->data['user_token'];

		$this->data['insertMode'] = true;

		$this->data['emailtemplate_keys'] = $this->model_extension_module_emailtemplate->getTemplateKeys();

		$this->data['emailtemplate'] = array();

		foreach(EmailTemplateDAO::describe() as $col => $type) {
			$key = (strpos($col, 'emailtemplate_') === 0 && substr($col, -3) != '_id') ? substr($col, 14) : $col;
			if (isset($this->request->post[$col])) {
				$this->data['emailtemplate'][$key] = $this->request->post[$col];
			} else {
				$this->data['emailtemplate'][$key] = '';
			}
		}

		if (isset($this->request->post['emailtemplate_key_select'])) {
			$this->data['emailtemplate']['key_select'] = $this->request->post['emailtemplate_key_select'];
		} elseif (!empty($this->request->get['key'])) {
			$this->data['emailtemplate']['key_select'] = $this->request->get['key'];

			$emailtemplate = $this->model_extension_module_emailtemplate->getTemplate($this->request->get['key']);

			if ($emailtemplate) {
				$this->data['emailtemplate']['label'] = $emailtemplate['emailtemplate_label'] . ' - ' . $this->language->get('text_custom');
				$this->data['emailtemplate']['type'] = $emailtemplate['emailtemplate_type'];
			}
		} else {
			$this->data['emailtemplate']['key_select'] = '';
		}

        // Default and similar templates
        $this->data['copy_emailtemplates'] = array();

		if ($this->data['emailtemplate']['key_select']) {
            $templates = $this->model_extension_module_emailtemplate->getTemplates(array('emailtemplate_key' => $this->data['emailtemplate']['key_select']));

            if ($templates) {
                foreach($templates as $template) {
                    $this->data['copy_emailtemplates'][] = array(
                        'emailtemplate_id' => $template['emailtemplate_id'],
                        'emailtemplate_key' => $template['emailtemplate_key'],
                        'label' => $template['emailtemplate_label'] . (!$template['emailtemplate_default'] ? (' - ' . $this->language->get('text_custom')) : '')
                    );
                }
            }
        }

		$this->_setTitle($this->language->get('heading_template_create'));

		$this->_breadcrumbs(array('heading_template_create' => array(
			'link' => 'extension/module/emailtemplate/template'
		)));

        $data['summernote_language'] = $this->language->get('summernote');
        if ($data['summernote_language'] == 'summernote') $data['summernote_language'] = 'en';
	}

	/**
	 * Get template shortcode form
	 */
	private function _template_shortcode_form() {
		$this->_messages();

		$this->data['action'] = $this->url->link('extension/module/emailtemplate/template_shortcode', 'user_token='.$this->session->data['user_token'] . '&id=' . $this->request->get['id'], true);

		$shortcodes = $this->model_extension_module_emailtemplate->getTemplateShortcodes(array('emailtemplate_shortcode_id' => $this->request->get['id']));
		$shortcode = $shortcodes[0];

		$this->_breadcrumbs(array('heading_template_shortcode' => array(
			'link' => 'extension/module/emailtemplate/template_shortcode',
			'params' => '&id='.$this->request->get['id']
		)));

		$this->_setTitle($this->language->get('heading_template_shortcode') . ' &raquo; ' . $shortcode['emailtemplate_shortcode_code']);

		$this->data['cancel'] = $this->url->link('extension/module/emailtemplate/template', 'user_token='.$this->session->data['user_token'] . '&id=' . $shortcode['emailtemplate_id'], true);

		$this->data['shortcode'] = array();

		foreach(EmailTemplateShortCodesDAO::describe() as $col => $type) {
			$key = (strpos($col, 'emailtemplate_shortcode_') === 0 && substr($col, -3) != '_id') ? substr($col, 24) : $col;
			if (isset($this->request->post[$col])) {
				$this->data['shortcode'][$key] = $this->request->post[$col];
			} elseif (isset($shortcode[$col])) {
				$this->data['shortcode'][$key] = $shortcode[$col];
			} else {
				$this->data['shortcode'][$key] = '';
			}
		}
	}

	/**
	 * Send Test Email with demo template
	 * @param $toAddress
	 * @param array $template_data
	 * @param array $overwrite
	 * @param bool $preview
	 * @return bool
	 */
	private function _sendTestEmail($toAddress, $template_data = array(), $overwrite = array(), $preview = true) {
		if (empty($template_data)) {
			$template_data = array(
				'emailtemplate_id' => 1,
				'store_id' => 0
			);
		}

		// $overwrite['emailtemplate_log_id'] = false; // Disable sent log for test emails

		$template = $this->model_extension_module_emailtemplate->load($template_data, $overwrite);

		if (!$template) return false;

		if (isset($template_data['emailtemplate_id']) && $template_data['emailtemplate_id'] != 1) {
			$template->data['emailtemplate'] = array_merge($template->data['emailtemplate'], $overwrite);

			// Load default shortcodes as data
			$default_shortcodes = $this->model_extension_module_emailtemplate->getTemplateShortcodes($template->data['emailtemplate']['emailtemplate_id']);

			if ($default_shortcodes) {
				foreach ($default_shortcodes as $row) {
					if (!isset($template->data[$row['emailtemplate_shortcode_code']])) {
						$template->data[$row['emailtemplate_shortcode_code']] = $row['emailtemplate_shortcode_example'];
					}
				}
			}
		}

		$template->data['insert_shortcodes'] = false;
		$template->data['parse_shortcodes'] = true;

		$mail = new Mail($this->config->get('config_mail_engine'));
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

        $mail->setTo($toAddress);
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
		$mail->setSubject(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));

		if ($preview) {
			$this->load->language('extension/module/emailtemplate/emailtemplate');

			$template->fetch(null, $this->language->get('text_example'));
		}

		$template->build();

		$mail->setText($template->getPlainText());

		$template->hook($mail);

		$mail->send();

		$this->model_extension_module_emailtemplate->sent();

		return true;
	}

	/**
	 * Send Test Email with demo template
	 */
	private function _sendEmailTemplateLog($emailtemplate_log_id) {
		$log = $this->model_extension_module_emailtemplate->getTemplateLog($emailtemplate_log_id);

		if ($log) {
			$this->load->model('setting/store');
			$this->load->model('setting/setting');

			if (!empty($log['store_id'])) {
				$store_id = $log['store_id'];
			} else {
				$store_id = $this->config->get('config_store_id');
			}

			$store_info = array_merge(
				$this->model_setting_setting->getSetting("config", $store_id),
				$this->model_setting_store->getStore($store_id)
			);

			$mail = new Mail(isset($store_info['config_mail_engine']) ? $store_info['config_mail_engine'] : $this->config->get('config_mail_engine'));
			$mail->parameter = isset($store_info['config_mail_parameter']) ? $store_info['config_mail_parameter'] : $this->config_>get('config_mail_parameter');
			$mail->smtp_hostname = isset($store_info['config_mail_smtp_hostname']) ? $store_info['config_mail_smtp_hostname'] : $this->config_>get('config_mail_smtp_hostname');
			$mail->smtp_username = isset($store_info['config_mail_smtp_username']) ? $store_info['config_mail_smtp_username'] : $this->config_>get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode((isset($store_info['config_mail_smtp_password']) ? $store_info['config_mail_smtp_password'] : $this->config_>get('config_mail_smtp_password')), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = isset($store_info['config_mail_smtp_port']) ? $store_info['config_mail_smtp_port'] : $this->config_>get('config_mail_smtp_port');
			$mail->smtp_timeout = isset($store_info['config_mail_smtp_timeout']) ? $store_info['config_mail_smtp_timeout'] : $this->config_>get('config_mail_smtp_timeout');

			$mail->setTo($log['emailtemplate_log_to']);
			$mail->setFrom($log['emailtemplate_log_from']);

			$file = DIR_CACHE . 'mail_queue/' . $log['emailtemplate_log_enc'];

			if (file_exists($file)) {
				$mail->setHtml(file_get_contents($file));

				@unlink($file);

				if (file_exists($file)) {
					trigger_error('Warning: Unable to delete ' . $file);
				}
			} else {
				// Load template if html not found
				$template_load = array(
					'emailtemplate_id' => $log['emailtemplate_id'],
					'customer_id' => $log['customer_id'],
					'customer_group_id' => $log['customer_group_id'],
					'store_id' => $log['store_id'],
					'language_id' => $log['language_id']
				);

				$template_data = array(
					'emailtemplate_log_id' => $emailtemplate_log_id,
					'emailtemplate_log_enc' => $log['emailtemplate_log_enc']
				);

				$template = $this->model_extension_module_emailtemplate->load($template_load, $template_data);

				if (!$template) {
					$template_load['emailtemplate_id'] = 1;
					$template = $this->model_extension_module_emailtemplate->load($template_load, $template_data);
					if (!$template) {
                        trigger_error('Unable to load emailtemplate_id=1');
                        return false;
                    }
				}

				$template->data['insert_shortcodes'] = false;

				$template->data['emailtemplate']['heading'] = html_entity_decode($log['emailtemplate_log_heading'], ENT_QUOTES, 'UTF-8');
				$template->data['emailtemplate']['subject'] = html_entity_decode($log['emailtemplate_log_subject'], ENT_QUOTES, 'UTF-8');

				$template->build();

				$mail->setHtml($template->fetch(null, $log['emailtemplate_log_content']));
			}

			if ($log['emailtemplate_log_sender']) {
				$mail->setSender(html_entity_decode($log['emailtemplate_log_sender'], ENT_QUOTES, 'UTF-8'));
			}

			if ($log['emailtemplate_log_subject']) {
				$mail->setSubject(html_entity_decode($log['emailtemplate_log_subject'], ENT_QUOTES, 'UTF-8'));
			}

			if ($log['emailtemplate_log_content']) {
				$mail->setText(strip_tags(html_entity_decode($log['emailtemplate_log_content'], ENT_QUOTES, 'UTF-8')));
			}

			if ($log['emailtemplate_log_cc']) {
				$mail->setCc($log['emailtemplate_log_cc']);
			}

            if (method_exists($mail, 'setMailQueue')) {
                $mail->setMailQueue(false);
            }

            $mail->send();

			$this->db->query("UPDATE " . DB_PREFIX . "emailtemplate_logs SET emailtemplate_log_sent = NOW(), emailtemplate_log_is_sent = 1 WHERE emailtemplate_log_id = " . (int)$log['emailtemplate_log_id']);
		}

		return true;
	}

	/**
	 * Populates $this->data with error_* keys using data from $this->error
	 */
	private function _messages() {
		# Attention
		if (isset($this->session->data['attention'])) {
			$this->data['error_attention'] = $this->session->data['attention'];
			unset($this->session->data['attention']);
		} else {
			$this->data['error_attention'] = '';
		}

		# Error
		if (isset($this->session->data['error'])) {
			$this->data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} else {
			$this->data['error_warning'] = '';
		}
		foreach ($this->error as $key => $val) {
			$this->data["error_{$key}"] = $val;
		}

		# Success
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
	}

	/**
	 * Populates breadcrumbs array for $this->data
	 */
	private function _breadcrumbs($crumbs = array(), $home = true) {
		$bc = array();
		$bc_map = array(
			'text_home' => array('link' => 'common/dashboard', 'params' => ''),
			'text_extensions' => array('link' => 'marketplace/extension', 'params' => '&type=module')
		);

		if ($home) {
			$bc_map = array_merge($bc_map, array('heading_templates' => array('link' => 'extension/module/emailtemplate')));
		}
		$bc_map = array_merge($bc_map, $crumbs);

		foreach ($bc_map as $name => $item) {
			$bc[]= array(
				'text' => $this->language->get($name),
				'href' => $this->url->link($item['link'], 'user_token='.$this->session->data['user_token'] . (isset($item['params']) ? $item['params'] : ''), true)
			);
		}
		$this->data['breadcrumbs'] = $bc;
	}

	private function _validateConfigCreate($data)
	{
		if (!$this->user->hasPermission('modify', 'extension/module/emailtemplate')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!isset($data['emailtemplate_config_name']) || $data['emailtemplate_config_name'] == '') {
			$this->error['emailtemplate_config_name'] = $this->language->get('error_required');
		}

		if ($this->error) {
			if (!isset($this->error['warning'])) {
				$this->error['warning'] = $this->language->get('error_warning');
			}
			return false;
		} else {
			return true;
		}
	}

	private function _validateConfig($data) {
		if (!$this->user->hasPermission('modify', 'extension/module/emailtemplate')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!isset($data['emailtemplate_config_name']) || $data['emailtemplate_config_name'] == '') {
			$this->error['emailtemplate_config_name'] = $this->language->get('error_required');
		}

		# Validate logo contains space or special character
		if ($data['emailtemplate_config_logo']) {
			$logo = $data['emailtemplate_config_logo'];
			if ($logo && preg_match('/[^\w.-]/', basename($logo))) {
				$this->error['emailtemplate_config_logo'] = sprintf($this->language->get('error_logo_filename'), $logo);
			}
		}

		if ($this->error) {
			if (!isset($this->error['warning'])) {
				$this->error['warning'] = $this->language->get('error_warning');
			}
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Validate template form data
	 */
	private function _validateTemplate($data) {
		if (!$this->user->hasPermission('modify', 'extension/module/emailtemplate')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		// #1 key empty and select no set #2 either empty
		if (($data['emailtemplate_key'] == '' && !isset($data['emailtemplate_key_select'])) ||
			($data['emailtemplate_key'] == '' && empty($data['emailtemplate_key_select']))) {
			$this->error['emailtemplate_key'] = $this->language->get('error_required');
		} elseif ($data['emailtemplate_key'] != '' && !empty($data['emailtemplate_key_select'])) {
			$this->error['emailtemplate_key'] = $this->language->get('error_key_select');
		}

		if (empty($data['emailtemplate_label'])) {
			$this->error['emailtemplate_label'] = $this->language->get('error_required');
		}

		if (empty($data['emailtemplate_type'])) {
			$this->error['emailtemplate_type'] = $this->language->get('error_required');
		}

		if (isset($data['emailtemplate_mail_attachment']) && $data['emailtemplate_mail_attachment']) {
			$attachments = explode(',', $data['emailtemplate_mail_attachment']);
			$dir = substr(DIR_SYSTEM, 0, -7); // remove 'system/'

			foreach($attachments as $attachment){
				$attachment = trim($attachment);
				if (!file_exists($dir.$attachment)) {
					$this->error['emailtemplate_mail_attachment'] = sprintf($this->language->get('error_file_not_exists'), $dir.$attachment);
					break;
				}
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Validate template shortcode form data
	 */
	private function _validateTemplateShortcode($data) {
		if (!$this->user->hasPermission('modify', 'extension/module/emailtemplate')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!isset($data['emailtemplate_shortcode_code']) || $data['emailtemplate_shortcode_code'] == '') {
			$this->error['emailtemplate_shortcode_code'] = $this->language->get('error_required');
		}

		if (!isset($data['emailtemplate_shortcode_type']) || $data['emailtemplate_shortcode_type'] == '') {
			$this->error['emailtemplate_shortcode_type'] = $this->language->get('error_required');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Validate template event form data
	 */
	private function _validateTemplateEvent($data) {
		if (!$this->user->hasPermission('modify', 'extension/module/emailtemplate')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($data['trigger'])) {
			$this->error['trigger'] = $this->language->get('error_required');
		}

		if (empty($data['code'])) {
			$this->error['code'] = $this->language->get('error_required');
		}

		if (empty($data['action'])) {
			$this->error['action'] = $this->language->get('error_required');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Truncate Text
	 *
	 * @param string $text
	 * @param int $limit
	 * @param string $ellipsis
	 * @return string
	 */
	private function _truncate_str($str, $length = 100, $breakWords = true, $append = '...') {
		$str = strip_tags(html_entity_decode($str, ENT_QUOTES, 'UTF-8'));

		$strLength = utf8_strlen($str);
		if ($strLength <= $length) {
			return $str;
		}

		if (!$breakWords) {
			while ($length < $strLength AND preg_match('/^\pL$/', utf8_substr($str, $length, 1))) {
				$length++;
			}
		}

		$str = utf8_substr($str, 0, $length) . $append;
		$str = preg_replace('/\s{3,}/',' ', $str);
		$str = trim($str);

		return $str;
	}


	/**
	 * Output Page
	 *
	 * @param string $template - template file path
	 * @param array $children
	 */
	private function _setTitle($title = '') {
		if ($title == '') {
			$title = $this->language->get('heading_title');
		} else {
			$title .= ' - ' . $this->language->get('heading_title');
		}

		$this->data['title'] = $title;

		$this->document->setTitle(strip_tags($title));

		return $this;
	}

	/**
	 * Output Page
	 *
	 * @param string $template - template file path
	 */
	private function _output($tpl) {
		if ($this->_css) {
			foreach($this->_css as $file) {
				$this->document->addStyle($file);
			}
		}

		if ($this->_js) {
			foreach($this->_js as $file) {
				$this->document->addScript($file);
			}
		}

		$this->data['header'] = $this->load->controller('common/header');
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view($tpl, $this->data));
	}

	/**
	 * Pagination
	 *
	 * @param string $url
	 * @param int $page - current page number
	 * @param int $total - total rows count
	 */
	private function _renderPagination($url, $page, $total, $limit = null, $style = '') {
		$pagination = new Pagination();
		$pagination->total = $total;
		$pagination->paging_style = $style;
		$pagination->page = $page;
		$pagination->limit = ($limit == null) ? $this->config->get('config_limit_admin') : $limit;
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $url;

		$this->data['pagination'] = $pagination->render();

		$this->data['pagination_results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $pagination->limit) + 1 : 0, ((($page - 1) * $pagination->limit) > ($pagination->total - $pagination->limit)) ? $pagination->total : ((($page - 1) * $pagination->limit) + $pagination->limit), $pagination->total, ceil($pagination->total / $pagination->limit));
	}

	private function _cleanupContent($text) {
		// https://stackoverflow.com/a/17948345/560287
		preg_match_all('/\{\%\s*(.*)\s*\%\}|\{\{(?!%)\s*((?:[^\s])*)\s*(?<!%)\}\}/i', $text, $matches);

		if (isset($matches[0]) && count($matches[0])) {
			foreach ($matches[0] as $match) {
				// Decode
				$clean_match = html_entity_decode($match, ENT_QUOTES,'UTF-8');

				// Replace unicode nbsp with space
				$clean_match = str_replace("\xC2\xA0", ' ', $clean_match);

				$text = str_replace($match, $clean_match, $text);
			}
		}

		return $text;
	}
}
