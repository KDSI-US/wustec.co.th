<?php
class ControllerExtensionModuleRelatedOptions extends Controller {
	const VERSION = '1.0.0';
	private $error = array();
	private $config_default = array(
			'module_related_options_status'   => 1,
			'module_related_options_settings' => array(
						'price_adjustment_on' => 0,
						'price_animate_on'    => 0,
						'price_multiply'      => 0,
						'decimal_places'      => 2,
						'show_out_of_stock'   => 1,
						'buy_out_of_stock'    => 1,
						'option_image_change' => 1
			)
		);

	public function install() {
		$query = $this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."product_option` LIKE 'master_option%'");
		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_option`
				 ADD COLUMN `master_option` int(11) NOT NULL DEFAULT '0',
				 ADD COLUMN `master_option_value` int(11) NOT NULL DEFAULT '0';");
		}

		$query = $this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."product_option_value` LIKE 'master_option%'");
		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_option_value`
				 ADD COLUMN `master_option_value` int(11) NOT NULL DEFAULT '0';");
		}

		// v3.2.0 and above
		$query = $this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."product_option_value` LIKE 'option_model'");
		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_option_value`
				 ADD COLUMN `option_model` varchar(255) DEFAULT NULL;");
		}

		// v3.2.0 and above
		$query = $this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."product_option_value` LIKE 'option_image'");
		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_option_value`
				 ADD COLUMN `option_image` varchar(255) DEFAULT NULL;");
		}

		$this->load->model('setting/setting');
		if (!$this->config->get('module_related_options_settings')) {
			$this->model_setting_setting->editSetting('module_related_options', $this->config_default);
		}

		$this->clearSystemCache();
	}

	public function uninstall() {
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_option`
			DROP COLUMN `master_option`;");
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_option`
			DROP COLUMN `master_option_value`;");
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_option_value`
			DROP COLUMN `master_option_value`;");
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_option_value`
			DROP COLUMN `option_model`;");
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_option_value`
			DROP COLUMN `option_image`;");

		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('module_related_options_version');

		$this->clearSystemCache();
	}

	public function update() {
		// Convert old settings (PRO <3.1) to new version
		$settings = $this->config_default['module_related_options_settings'];

		$old_settings = $this->config->get('related_options');

		if ($old_settings) {
			$equivalent = array(
				'price_animate_on'  =>'animate_price',
				'show_out_of_stock' =>'residue_on',
				'buy_out_of_stock'  =>'show_disabled',
				'decimal_places'    =>'price_residue'
			);

			foreach ($settings as $key => $value) {
				if (array_key_exists($key, $old_settings)) {
					$settings[$key] = $old_settings[$key];
				} else {
					$settings[$key] = $old_settings[ $equivalent[$key] ];
				}
			}

			$this->config_default['module_related_options_settings'] = $settings;

			$this->model_setting_setting->editSetting('module_related_options', $this->config_default);
			$this->model_setting_setting->deleteSetting('related_options');
		}
		// Convert end

		// v3.2.0 and above
		$query = $this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."product_option_value` LIKE 'option_model'");
		if (!$query->num_rows) {
			$this->install();
		}

		// v3.2.0 and above
		$query = $this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."product_option_value` LIKE 'option_image'");
		if (!$query->num_rows) {
			$this->install();
		}

		$this->clearSystemCache();
	}

	public function index() {
		$this->load->language('extension/module/related_options');

		$this->document->setTitle(strip_tags($this->language->get('heading_title')));

		$this->load->model('setting/setting');

		// If the module has been updated
		if (self::VERSION != $this->config->get('module_related_options_version')) {
			$this->update();

			$this->model_setting_setting->editSetting('module_related_options_version', ['module_related_options_version' => self::VERSION]);
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$selectors = array(
				"option_selector",
				"price_selector",
				"price_old_selector",
				"quantity_selector"
			);

			// Cleaning selectors
			foreach ($selectors as $selector_name) {
				if (isset($this->request->post['module_related_options_settings'][$selector_name])) {
					$selector = trim(html_entity_decode($this->request->post['module_related_options_settings'][$selector_name], ENT_QUOTES, 'UTF-8'));

					// Remove quotes at the beginning and end of a line
					$selector = trim($selector, "\"'");

					// Replace single quotes
					$selector = str_replace("'", '"', $selector);

					$forbidden_symbols = array("`", "/", "\\", "?", "<", "{", "}", "%", "&");
					$selector = str_replace($forbidden_symbols, "", $selector);

					$this->request->post['module_related_options_settings'][$selector_name] = trim(htmlspecialchars($selector, ENT_COMPAT, 'UTF-8'));
				}
			}

			$this->model_setting_setting->editSetting('module_related_options', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		//$data['heading_title'] = $this->language->get('heading_title');

		//$data['button_save'] = $this->language->get('button_save');
		//$data['button_cancel'] = $this->language->get('button_cancel');

		//$data['text_yes'] = $this->language->get('text_yes');
		//$data['text_no'] = $this->language->get('text_no');
		//$data['text_settings'] = $this->language->get('text_settings');
		//$data['entry_price_adjustment'] = $this->language->get('entry_price_adjustment');
		//$data['entry_price_animate'] = $this->language->get('entry_price_animate');
		//$data['entry_show_out_of_stock'] = $this->language->get('entry_show_out_of_stock');
		//$data['entry_buy_out_of_stock'] = $this->language->get('entry_buy_out_of_stock');
		//$data['entry_decimal_places'] = $this->language->get('entry_decimal_places');
		//$data['help_instructions'] = $this->language->get('help_instructions');
		//$data['help_price_adjustment'] = $this->language->get('help_price_adjustment');
		//$data['help_decimal_places'] = $this->language->get('help_decimal_places');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
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
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/module/related_options',  'user_token=' . $this->session->data['user_token'], true),
		);

		$data['action'] = $this->url->link('extension/module/related_options', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_related_options_settings'])) {
			$data['module_settings'] = $this->request->post['module_related_options_settings'];
		} elseif ($this->config->get('module_related_options_settings')) {
			$data['module_settings'] = $this->config->get('module_related_options_settings');
		} else {
			$data['module_settings'] = $this->config_default['module_related_options_settings'];
		}

		if ((int)ini_get('max_input_vars') < 3000) {
			$data['php_max_input_vars'] = ini_get('max_input_vars');
		}

		if (isset($this->request->post['module_related_options_status'])) {
			$data['module_related_options_status'] = $this->request->post['module_related_options_status'];
		} else {
			$data['module_related_options_status'] = $this->config->get('module_related_options_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/related_options', $data));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/related_options')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	protected function clearSystemCache() {
		$cache_directory = DIR_CACHE;

		if (file_exists($cache_directory)) {
			$result = '';
			$directory = new RecursiveDirectoryIterator($cache_directory);
			$files = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::CHILD_FIRST);

			foreach($files as $file) {
				if (($file->getFilename() === '.') || ($file->getFilename() === '..') || ($file->getFilename() === 'index.html') || ($file->getFilename() === 'index.htm')) {
					continue;
				}

				if ($file->isDir()){
					@rmdir($file->getRealPath());
					$result .= 'Remove folder `' . $file . '`' . PHP_EOL;
				} else {
					@unlink($file->getRealPath());
					$result .= 'Remove file `' . $file . '`' . PHP_EOL;
				}
			}

		} else {
			$result = sprintf($this->language->get('text_cache_folder_not_found'), $cache_directory);
		}

		return $result;
	}

	// Not used. For the future
	private function getThemeName() {
		return $this->config->get('config_theme');
	}

	// Not used. For the future
	private function checkRequirements() {
		$error = null;

		if (phpversion() < '5.4') {
			$error = 'Warning: You need to use PHP5.4+ or above for module to work!';
		}

		if (!ini_get('file_uploads')) {
			$error = 'Warning: file_uploads needs to be enabled!';
		}

		if (!extension_loaded('zlib')) {
			$error = 'Warning: ZLIB extension needs to be loaded for module to work!';
		}

		return array($error === null, $error);
	}


}
