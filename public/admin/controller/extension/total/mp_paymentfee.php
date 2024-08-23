<?php
class ControllerExtensionTotalMpPaymentfee extends Controller {
	private $error = [];

	// trait start
	public function getLanguages() {
		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();

		if (VERSION >= '2.2.0.0') {
			foreach ($languages as &$language) {
				$language['lang_flag'] = 'language/'.$language['code'].'/'.$language['code'].'.png';
			}
		} else {
			foreach ($languages as &$language) {
				$language['lang_flag'] = 'view/image/flags/'.$language['image'].'';
			}
		}
		return $languages;
	}
	public function getCustomerGroups() {
		if (VERSION < '2.2.0.0') {
			$this->load->model('sale/customer_group');
			$model_customer_group = 'model_sale_customer_group';
		} else {
			$this->load->model('customer/customer_group');
			$model_customer_group = 'model_customer_customer_group';
		}
		return $this->{$model_customer_group}->getCustomerGroups();
	}
	// trait end

	public function index() {

		$this->document->addStyle('view/stylesheet/mp_paymentfee/stylesheet.css');

		$this->load->language('extension/total/mp_paymentfee');

		if (VERSION <= '2.3.0.2') {
			$this->load->model('extension/extension');
		} else {
			$this->load->model('setting/extension');
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('total_mp_paymentfee', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/total/mp_paymentfee', 'user_token=' . $this->session->data['user_token'] . '&type=total', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_fixed'] = $this->language->get('text_fixed');
		$data['text_percent'] = $this->language->get('text_percent');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_none'] = $this->language->get('text_none');

		$data['column_customer_group'] = $this->language->get('column_customer_group');
		$data['column_total'] = $this->language->get('column_total');
		$data['column_condition'] = $this->language->get('column_condition');
		$data['column_fee'] = $this->language->get('column_fee');
		$data['column_ruletype'] = $this->language->get('column_ruletype');
		$data['column_type'] = $this->language->get('column_type');

		$data['text_rulefee'] = $this->language->get('text_rulefee');
		$data['text_rulediscount'] = $this->language->get('text_rulediscount');

		$data['condition_sub_total'] = $this->language->get('condition_sub_total');
		$data['condition_total'] = $this->language->get('condition_total');

		$data['tab_setting'] = $this->language->get('tab_setting');
		$data['tab_feerules'] = $this->language->get('tab_feerules');
		$data['tab_support'] = $this->language->get('tab_support');

		$data['tab_paymentfee_rules'] = $this->language->get('tab_paymentfee_rules');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_tax_class'] = $this->language->get('entry_tax_class');
		$data['entry_fee'] = $this->language->get('entry_fee');
		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_payment_method'] = $this->language->get('entry_payment_method');

		$data['tab_mp_paymentfee_rules'] = $this->language->get('tab_mp_paymentfee_rules');

		$data['heading_payment'] = $this->language->get('heading_payment');
		$data['heading_group'] = $this->language->get('heading_group');


		$data['button_add_feerule'] = $this->language->get('button_add_feerule');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		} else {
			$data['error_title'] = [];
		}

		if (isset($this->error['fee'])) {
			$data['error_fee'] = $this->error['fee'];
		} else {
			$data['error_fee'] = [];
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/total/mp_paymentfee', 'user_token=' . $this->session->data['user_token'], true)
		];

		$data['action'] = $this->url->link('extension/total/mp_paymentfee', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true);

		if (VERSION <= '2.3.0.2') {
			$payment_methods = $this->model_extension_extension->getInstalled('payment');
		} else {
			$payment_methods = $this->model_setting_extension->getInstalled('payment');
		}

		$data['payment_methods'] = [];
		foreach ($payment_methods as $key => $payment_method) {
			$payment_method_basename = basename($payment_method, '.php');
				if ( VERSION > '2.2.0.0' ) {
					$this->load->language('extension/payment/' . $payment_method_basename);
				} else {
					$this->load->language('payment/' . $payment_method_basename);
				}

				$data['payment_methods'][] = [
					'name'       => $this->language->get('heading_title'),
					'code'       => $payment_method_basename,
				];
		}

		// Customer Groups
		$data['customer_groups'] = $this->getCustomerGroups();

		// Tax Classes
		$this->load->model('localisation/tax_class');
		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		// Languages
		$data['languages'] = $this->getLanguages();

		if (isset($this->request->post['total_mp_paymentfee_status'])) {
			$data['total_mp_paymentfee_status'] = $this->request->post['total_mp_paymentfee_status'];
		} else {
			$data['total_mp_paymentfee_status'] = $this->config->get('total_mp_paymentfee_status');
		}

		if (isset($this->request->post['total_mp_paymentfee_sort_order'])) {
			$data['total_mp_paymentfee_sort_order'] = $this->request->post['total_mp_paymentfee_sort_order'];
		} else if ($this->config->has('total_mp_paymentfee_sort_order')) {
			$data['total_mp_paymentfee_sort_order'] = $this->config->get('total_mp_paymentfee_sort_order');
		} else {
			$data['total_mp_paymentfee_sort_order'] = 4;
		}

		if (isset($this->request->post['total_mp_paymentfee_tax'])) {
			$data['total_mp_paymentfee_tax'] = $this->request->post['total_mp_paymentfee_tax'];
		} else {
			$data['total_mp_paymentfee_tax'] = $this->config->get('total_mp_paymentfee_tax');
		}

		if (isset($this->request->post['total_mp_paymentfee_rule'])) {
			$mp_paymentfee_rules = $this->request->post['total_mp_paymentfee_rule'];
		} else {
			$mp_paymentfee_rules = (array)$this->config->get('total_mp_paymentfee_rule');
		}

		$data['mp_paymentfee_rules'] = [];
		foreach ($mp_paymentfee_rules as $mp_paymentfee_rule) {
			$p_basename = basename($mp_paymentfee_rule['code'], '.php');
			if ( VERSION > '2.2.0.0' ) {
				$this->load->language('extension/payment/' . $p_basename);
			} else {
				$this->load->language('payment/' . $p_basename);
			}

			$data['mp_paymentfee_rules'][] = [
				'payment_name'				=> $this->language->get('heading_title'),
				'description'				=> (isset($mp_paymentfee_rule['description'])) ? $mp_paymentfee_rule['description'] : [],
				'code'						=> $mp_paymentfee_rule['code'],
				'status'						=> (isset($mp_paymentfee_rule['status']) ? $mp_paymentfee_rule['status'] : ''),
				'groups'					=> (isset($mp_paymentfee_rule['groups'])) ? $mp_paymentfee_rule['groups'] : [],
			];
		}

		$data['config_language_id'] = $this->config->get('config_language_id');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->config->set('template_engine', 'template');

		$this->response->setOutput($this->load->view('extension/total/mp_paymentfee', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/total/mp_paymentfee')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (isset($this->request->post['total_mp_paymentfee_rule']) && !empty($this->request->post['total_mp_paymentfee_status'])) {
			foreach ($this->request->post['total_mp_paymentfee_rule'] as $key => $mp_paymentfee_rule) {
				if (!empty($mp_paymentfee_rule['status'])) {
					if ($mp_paymentfee_rule['groups']) {
						foreach ($mp_paymentfee_rule['groups'] as $customer_group_id => $group) {
							if (!(int)$group['fee'] || $group['fee'] == '') {
								$this->error['fee'][$key][$customer_group_id] = $this->language->get('error_fee');
							}
						}
					}

					if (isset($mp_paymentfee_rule['description'])) {
						foreach ($mp_paymentfee_rule['description'] as $language_id => $description) {
							if ((utf8_strlen($description['title']) < 2) || (utf8_strlen($description['title']) > 255)) {
								$this->error['title'][$key][$language_id] = $this->language->get('error_title');
							}
						}
					}
				}
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}
}