<?php
class ControllerModulepointsMpplan extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('modulepoints/mpplan');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('modulepoints/mpplan');

		$this->load->model('modulepoints/mpplan');
		$this->model_modulepoints_mpplan->CreateMPPlanTable();

		$this->getList();
	}

	public function add() {
		$this->load->language('modulepoints/mpplan');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('modulepoints/mpplan');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_modulepoints_mpplan->addMpplan($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('modulepoints/mpplan', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('modulepoints/mpplan');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('modulepoints/mpplan');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_modulepoints_mpplan->editMpplan($this->request->get['mpplan_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('modulepoints/mpplan', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('modulepoints/mpplan');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('modulepoints/mpplan');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $mpplan_id) {
				$this->model_modulepoints_mpplan->deleteMpplan($mpplan_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('modulepoints/mpplan', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'mpd.title';
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

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('modulepoints/mpplan', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('modulepoints/mpplan/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('modulepoints/mpplan/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['mpplans'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$mpplan_total = $this->model_modulepoints_mpplan->getTotalMpplans();

		$results = $this->model_modulepoints_mpplan->getMpplans($filter_data);

		foreach ($results as $result) {
			$data['mpplans'][] = array(
				'mpplan_id' => $result['mpplan_id'],
				'name'          => $result['name'],
				'sort_order'     => $result['sort_order'],
				'edit'           => $this->url->link('modulepoints/mpplan/edit', 'user_token=' . $this->session->data['user_token'] . '&mpplan_id=' . $result['mpplan_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');

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

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('modulepoints/mpplan', 'user_token=' . $this->session->data['user_token'] . '&sort=mpd.name' . $url, true);
		$data['sort_sort_order'] = $this->url->link('modulepoints/mpplan', 'user_token=' . $this->session->data['user_token'] . '&sort=mp.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $mpplan_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('modulepoints/mpplan', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($mpplan_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($mpplan_total - $this->config->get('config_limit_admin'))) ? $mpplan_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $mpplan_total, ceil($mpplan_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->config->set('template_engine', 'template');
		$this->response->setOutput($this->load->view('modulepoints/mpplan_list', $data));
	}

	protected function getForm() {
		$this->document->addStyle('view/javascript/modulepoints/colorpicker/css/bootstrap-colorpicker.css');
		$this->document->addScript('view/javascript/modulepoints/colorpicker/js/bootstrap-colorpicker.js');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['mpplan_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_days'] = $this->language->get('text_days');
		$data['text_months'] = $this->language->get('text_months');
		$data['text_years'] = $this->language->get('text_years');		

		$data['entry_name'] = $this->language->get('entry_name');		
		$data['entry_info'] = $this->language->get('entry_info');
		$data['entry_top_description'] = $this->language->get('entry_top_description');
		$data['entry_bottom_description'] = $this->language->get('entry_bottom_description');
		$data['entry_meta_title'] = $this->language->get('entry_meta_title');
		$data['entry_meta_description'] = $this->language->get('entry_meta_description');
		$data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
		$data['entry_keyword'] = $this->language->get('entry_keyword');
		$data['entry_store'] = $this->language->get('entry_store');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_price'] = $this->language->get('entry_price');
		$data['entry_duration'] = $this->language->get('entry_duration');
		$data['entry_product'] = $this->language->get('entry_product');
		$data['entry_discount'] = $this->language->get('entry_discount');
		$data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$data['entry_discount'] = $this->language->get('entry_discount');
		$data['entry_discount_value'] = $this->language->get('entry_discount_value');
		$data['entry_first_bgcolor'] = $this->language->get('entry_first_bgcolor');
		$data['entry_first_textcolor'] = $this->language->get('entry_first_textcolor');
		$data['entry_second_bgcolor'] = $this->language->get('entry_second_bgcolor');
		$data['entry_second_textcolor'] = $this->language->get('entry_second_textcolor');

		$data['help_keyword'] = $this->language->get('help_keyword');
		$data['help_product'] = $this->language->get('help_product');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_info_add'] = $this->language->get('button_info_add');
		$data['button_remove'] = $this->language->get('button_remove');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_data'] = $this->language->get('tab_data');
		$data['tab_price'] = $this->language->get('tab_price');
		$data['tab_link'] = $this->language->get('tab_link');
		$data['tab_info'] = $this->language->get('tab_info');
		$data['tab_color'] = $this->language->get('tab_color');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		if (isset($this->error['description'])) {
			$data['error_description'] = $this->error['description'];
		} else {
			$data['error_description'] = array();
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = array();
		}

		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = array();
		}
		
		if (isset($this->error['info'])) {
			$data['error_info'] = $this->error['info'];
		} else {
			$data['error_info'] = array();
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('modulepoints/mpplan', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['mpplan_id'])) {
			$data['action'] = $this->url->link('modulepoints/mpplan/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('modulepoints/mpplan/edit', 'user_token=' . $this->session->data['user_token'] . '&mpplan_id=' . $this->request->get['mpplan_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('modulepoints/mpplan', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['mpplan_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$mpplan_info = $this->model_modulepoints_mpplan->getMpplan($this->request->get['mpplan_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['mpplan_description'])) {
			$data['mpplan_description'] = $this->request->post['mpplan_description'];
		} elseif (isset($this->request->get['mpplan_id'])) {
			$data['mpplan_description'] = $this->model_modulepoints_mpplan->getMpplanDescriptions($this->request->get['mpplan_id']);
		} else {
			$data['mpplan_description'] = array();
		}

		$this->load->model('setting/store');
		$data['stores'] = $this->model_setting_store->getStores();

		if (isset($this->request->post['mpplan_store'])) {
			$data['mpplan_store'] = $this->request->post['mpplan_store'];
		} elseif (isset($this->request->get['mpplan_id'])) {
			$data['mpplan_store'] = $this->model_modulepoints_mpplan->getMpplanStores($this->request->get['mpplan_id']);
		} else {
			$data['mpplan_store'] = array(0);
		}

		if (isset($this->request->post['mpplan_product'])) {
			$products = $this->request->post['mpplan_product'];
		} elseif (isset($this->request->get['mpplan_id'])) {
			$products = $this->model_modulepoints_mpplan->getMpplanProducts($this->request->get['mpplan_id']);
		} else {
			$products = array();
		}

		$data['mpplan_products'] = array();
		$this->load->model('catalog/product');
		foreach ($products as $product_id) {
			$related_info = $this->model_catalog_product->getProduct($product_id);

			if ($related_info) {
				$data['mpplan_products'][] = array(
					'product_id' => $related_info['product_id'],
					'name'       => $related_info['name']
				);
			}
		}

		if (isset($this->request->post['mpplan_seo_url'])) {
			$data['mpplan_seo_url'] = $this->request->post['mpplan_seo_url'];
		} elseif (isset($this->request->get['mpplan_id'])) {
			$data['mpplan_seo_url'] = $this->model_modulepoints_mpplan->getMpplanSeoUrls($this->request->get['mpplan_id']);
		} else {
			$data['mpplan_seo_url'] = array();
		}

		if (isset($this->request->post['price'])) {
			$data['price'] = $this->request->post['price'];
		} elseif (!empty($mpplan_info)) {
			$data['price'] = $mpplan_info['price'];
		} else {
			$data['price'] = '';
		}

		if (isset($this->request->post['duration_type'])) {
			$data['duration_type'] = $this->request->post['duration_type'];
		} elseif (!empty($mpplan_info)) {
			$data['duration_type'] = $mpplan_info['duration_type'];
		} else {
			$data['duration_type'] = 'd';
		}

		if (isset($this->request->post['duration_value'])) {
			$data['duration_value'] = $this->request->post['duration_value'];
		} elseif (!empty($mpplan_info)) {
			$data['duration_value'] = $mpplan_info['duration_value'];
		} else {
			$data['duration_value'] = 7;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($mpplan_info)) {
			$data['status'] = $mpplan_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($mpplan_info)) {
			$data['sort_order'] = $mpplan_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		if (isset($this->request->post['first_bgcolor'])) {
			$data['first_bgcolor'] = $this->request->post['first_bgcolor'];
		} elseif (!empty($mpplan_info)) {
			$data['first_bgcolor'] = $mpplan_info['first_bgcolor'];
		} else {
			$data['first_bgcolor'] = '#5da22e';
		}
		
		if (isset($this->request->post['first_textcolor'])) {
			$data['first_textcolor'] = $this->request->post['first_textcolor'];
		} elseif (!empty($mpplan_info)) {
			$data['first_textcolor'] = $mpplan_info['first_textcolor'];
		} else {
			$data['first_textcolor'] = '#ffffff';
		}

		if (isset($this->request->post['second_bgcolor'])) {
			$data['second_bgcolor'] = $this->request->post['second_bgcolor'];
		} elseif (!empty($mpplan_info)) {
			$data['second_bgcolor'] = $mpplan_info['second_bgcolor'];
		} else {
			$data['second_bgcolor'] = '#70b139';
		}

		if (isset($this->request->post['second_textcolor'])) {
			$data['second_textcolor'] = $this->request->post['second_textcolor'];
		} elseif (!empty($mpplan_info)) {
			$data['second_textcolor'] = $mpplan_info['second_textcolor'];
		} else {
			$data['second_textcolor'] = '#ffffff';
		}

		if(VERSION > '2.0.3.1') {
			$this->load->model('customer/customer_group');
			$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();
		} else{
			$this->load->model('sale/customer_group');
			$data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();
		}

		if (isset($this->request->post['mpplan_discount'])) {
			$data['mpplan_discount'] = $this->request->post['mpplan_discount'];
		} elseif (isset($this->request->get['mpplan_id'])) {
			$data['mpplan_discount'] = $this->model_modulepoints_mpplan->getMpplanDiscounts($this->request->get['mpplan_id']);
		} else {
			$data['mpplan_discount'] = array();
		}

		if (isset($this->request->post['mpplan_info'])) {
			$data['mpplan_infos'] = $this->request->post['mpplan_info'];
		} elseif (isset($this->request->get['mpplan_id'])) {
			$data['mpplan_infos'] = $this->model_modulepoints_mpplan->getMpplanInfos($this->request->get['mpplan_id']);
		} else {
			$data['mpplan_infos'] = array();
		}

		$data['stores'] = array();
		
		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);
		
		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->config->set('template_engine', 'template');
		$this->response->setOutput($this->load->view('modulepoints/mpplan_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'modulepoints/mpplan')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['mpplan_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 64)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}

			if ((utf8_strlen($value['meta_title']) < 3) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
		}

		if (isset($this->request->post['mpplan_info'])) {
			foreach ($this->request->post['mpplan_info'] as $row => $value) {
				if(isset($value['mpplan_info_description'])) {
					foreach ($value['mpplan_info_description'] as $language_id => $description) {
						if ((utf8_strlen($description['name']) < 2) || (utf8_strlen($description['name']) > 255)) {
							$this->error['info'][$row][$language_id] = $this->language->get('error_info');
						}
					}
				}
			}
		}

		if ($this->request->post['mpplan_seo_url']) {
			$this->load->model('design/seo_url');
			
			foreach ($this->request->post['mpplan_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						if (count(array_keys($language, $keyword)) > 1) {
							$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
						}

						$seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);
	
						foreach ($seo_urls as $seo_url) {
							if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['mpplan_id']) || ($seo_url['query'] != 'mpplan_id=' . $this->request->get['mpplan_id']))) {		
								$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_keyword');
				
								break;
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

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'modulepoints/mpplan')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}