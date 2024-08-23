<?php
class ControllerModulepointsPlansetting extends Controller {
	private $error = array();

	public function index() {
		if(isset($this->request->get['store_id'])) {
			$data['store_id'] = $this->request->get['store_id'];
		} else {
			$data['store_id'] = 0;
		}

		$this->load->language('modulepoints/plansetting');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		$this->load->model('catalog/product');
		
		$this->load->model('modulepoints/mpplan');
		$this->model_modulepoints_mpplan->CreateMPPlanTable();

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('mpplan', $this->request->post, $data['store_id']);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('modulepoints/plansetting', 'user_token=' . $this->session->data['user_token'] . '&store_id='. $data['store_id'], true));
		}

		// Stores
		$this->load->model('setting/store');

		$data['stores'] = array();

		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);

		$results = $this->model_setting_store->getStores();

		foreach ($results as $result) {
			$data['stores'][] = array(
				'store_id' => $result['store_id'],
				'name'     => $result['name']
			);
		}

		$store_info = $this->model_setting_store->getStore($data['store_id']);
		if($store_info) {
			$data['store_name'] = $store_info['name'];
		} else {
			$data['store_name'] = $this->language->get('text_default');
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');		
		$data['text_template_1']	= $this->language->get('text_template_1');
		$data['text_template_2']	= $this->language->get('text_template_2');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_product'] = $this->language->get('entry_product');
		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_product_size'] = $this->language->get('entry_product_size');
		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_height'] = $this->language->get('entry_height');
		$data['entry_title']	= $this->language->get('entry_title');
		$data['entry_sub_title']	= $this->language->get('entry_sub_title');
		$data['entry_top_description']	= $this->language->get('entry_top_description');
		$data['entry_bottom_description']	= $this->language->get('entry_bottom_description');
		$data['entry_meta_title']	= $this->language->get('entry_meta_title');
		$data['entry_meta_description']	= $this->language->get('entry_meta_description');
		$data['entry_meta_keyword']	= $this->language->get('entry_meta_keyword');
		$data['entry_product_status']	= $this->language->get('entry_product_status');
		$data['entry_buttontext']	= $this->language->get('entry_buttontext');
		$data['entry_design']	= $this->language->get('entry_design');

		$data['help_product'] = $this->language->get('help_product');
		$data['help_product_size'] = $this->language->get('help_product_size');

		$data['text_none'] = $this->language->get('text_none');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_linguial'] = $this->language->get('tab_linguial');
		$data['tab_information'] = $this->language->get('tab_information');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['product_size'])) {
			$data['error_product_size'] = $this->error['product_size'];
		} else {
			$data['error_product_size'] = '';
		}

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		} else {
			$data['error_title'] = '';
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('modulepoints/plansetting', 'user_token=' . $this->session->data['user_token'], true)
		);

		if(isset($data['store_id'])) {
			$data['action'] = $this->url->link('modulepoints/plansetting', 'user_token=' . $this->session->data['user_token'] . '&store_id='. $data['store_id'], true);
		} else {
			$data['action'] = $this->url->link('modulepoints/plansetting', 'user_token=' . $this->session->data['user_token'], true);
		}

		$data['cancel'] = $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$module_info = $this->model_setting_setting->getSetting('mpplan', $data['store_id']);

		if (isset($this->request->post['mpplan_status'])) {
			$data['mpplan_status'] = $this->request->post['mpplan_status'];
		} else if(isset($module_info['mpplan_status'])) {
			$data['mpplan_status'] = $module_info['mpplan_status'];
		} else {
			$data['mpplan_status'] = '';
		}

		if (isset($this->request->post['mpplan_design'])) {
			$data['mpplan_design'] = $this->request->post['mpplan_design'];
		} else if(isset($module_info['mpplan_design'])) {
			$data['mpplan_design'] = $module_info['mpplan_design'];
		} else {
			$data['mpplan_design'] = '1';
		}

		if (isset($this->request->post['mpplan_description'])) {
			$data['mpplan_description'] = $this->request->post['mpplan_description'];
		} else if(isset($module_info['mpplan_description'])) {
			$data['mpplan_description'] = (array)$module_info['mpplan_description'];
		} else {
			$data['mpplan_description'] = array();
		}

		if (isset($this->request->post['mpplan_product_id'])) {
			$data['mpplan_product_id'] = $this->request->post['mpplan_product_id'];
		} else if(isset($module_info['mpplan_product_id'])) {
			$data['mpplan_product_id'] = $module_info['mpplan_product_id'];
		} else {
			$data['mpplan_product_id'] = '';
		}

		if (isset($this->request->post['mpplan_product_width'])) {
			$data['mpplan_product_width'] = $this->request->post['mpplan_product_width'];
		} else if(isset($module_info['mpplan_product_width'])) {
			$data['mpplan_product_width'] = $module_info['mpplan_product_width'];
		} else {
			$data['mpplan_product_width'] = '';
		}

		if (isset($this->request->post['mpplan_product_height'])) {
			$data['mpplan_product_height'] = $this->request->post['mpplan_product_height'];
		} else if(isset($module_info['mpplan_product_height'])) {
			$data['mpplan_product_height'] = $module_info['mpplan_product_height'];
		}else {
			$data['mpplan_product_height'] = '';
		}

		if (isset($this->request->post['mpplan_status_id'])) {
			$data['mpplan_status_id'] = $this->request->post['mpplan_status_id'];
		} else if(isset($module_info['mpplan_status_id'])) {
			$data['mpplan_status_id'] = $module_info['mpplan_status_id'];
		} else {
			$data['mpplan_status_id'] = '';
		}

		if (isset($this->request->post['mpplan_product'])) {
			$data['mpplan_product'] = $this->request->post['mpplan_product'];
		} elseif (isset($module_info['mpplan_product_id'])) {
			$product_info = $this->model_catalog_product->getProduct($module_info['mpplan_product_id']);
			if ($product_info) {
				$data['mpplan_product'] = $product_info['name'];
			} else {
				$data['mpplan_product'] = '';
			}
		} else {
			$data['mpplan_product'] = '';
		}


		if (isset($this->request->post['mpplan_product_status'])) {
			$data['mpplan_product_status'] = $this->request->post['mpplan_product_status'];
		} else if(isset($module_info['mpplan_product_status'])) {
			$data['mpplan_product_status'] = $module_info['mpplan_product_status'];
		} else {
			$data['mpplan_product_status'] = '';
		}

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->config->set('template_engine', 'template');
		$this->response->setOutput($this->load->view('modulepoints/plansetting', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'modulepoints/plansetting')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if(!empty($this->request->post['mpplan_status'])) {
			foreach ($this->request->post['mpplan_description'] as $language_id => $value) {
				if ((utf8_strlen($value['title']) < 2) || (utf8_strlen($value['title']) > 255)) {
					$this->error['title'][$language_id] = $this->language->get('error_title');
				}

				if ((utf8_strlen($value['meta_title']) < 3) || (utf8_strlen($value['meta_title']) > 255)) {
					$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
				}
			}

			if (!$this->request->post['mpplan_product_width'] || !$this->request->post['mpplan_product_height']) {
				$this->error['product_size'] = $this->language->get('error_product_size');
			}

			$mpplan_product_id = isset($this->request->post['mpplan_product_id']) ? $this->request->post['mpplan_product_id'] : '';
			$product_info = $this->model_catalog_product->getProduct($mpplan_product_id);
			if (!$product_info) {
				$this->error['warning'] = $this->language->get('error_product');
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}
}