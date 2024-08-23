<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionMpFaqMpfaqquestion extends Controller {
	private $error = [];

	public function getMenu() {
		$menus = [];
		$menu = [];

		$this->load->language('mpfaq/mpfaq_menu');


			if ($this->user->hasPermission('access', 'extension/mpfaq/mpfaqcategory')) {
				$menu[] = [
					'name'	   => $this->language->get('text_faq_category'),
					'href'     => $this->url->link('extension/mpfaq/mpfaqcategory', 'user_token=' . $this->session->data['user_token'], true),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'extension/mpfaq/mpfaqquestion')) {
				$menu[] = [
					'name'	   => $this->language->get('text_faq_question'),
					'href'     => $this->url->link('extension/mpfaq/mpfaqquestion', 'user_token=' . $this->session->data['user_token'], true),
					'children' => []
				];
			}

			if ($menu) {
				$menus = [
					'id'       => 'menu-mpfaq',
					'icon'	   => 'fa-question-circle',
					'name'	   => $this->language->get('text_mpfaq'),
					'href'     => '',
					'children' => $menu
				];
			}
			return $menus;
	}

	public function index() {
		$this->load->language('mpfaq/mpfaqquestion');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpfaq/mpfaqquestion');

		/// Table Bnao
		$this->load->model('extension/mpfaq/mpfaqcategory');
		$this->model_extension_mpfaq_mpfaqcategory->tablebnao();

		$this->getList();
	}

	public function add() {
		$this->load->language('mpfaq/mpfaqquestion');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpfaq/mpfaqquestion');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_mpfaq_mpfaqquestion->addMpfaqquestion($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_question'])) {
				$url .= '&filter_question=' . urlencode(html_entity_decode($this->request->get['filter_question'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_category'])) {
				$url .= '&filter_category=' . urlencode(html_entity_decode($this->request->get['filter_category'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
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

			$this->response->redirect($this->url->link('extension/mpfaq/mpfaqquestion', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('mpfaq/mpfaqquestion');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpfaq/mpfaqquestion');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_mpfaq_mpfaqquestion->editMpfaqquestion($this->request->get['mpfaqquestion_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_question'])) {
				$url .= '&filter_question=' . urlencode(html_entity_decode($this->request->get['filter_question'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_category'])) {
				$url .= '&filter_category=' . urlencode(html_entity_decode($this->request->get['filter_category'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
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

			$this->response->redirect($this->url->link('extension/mpfaq/mpfaqquestion', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('mpfaq/mpfaqquestion');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpfaq/mpfaqquestion');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $mpfaqquestion_id) {
				$this->model_extension_mpfaq_mpfaqquestion->deleteMpfaqquestion($mpfaqquestion_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_question'])) {
				$url .= '&filter_question=' . urlencode(html_entity_decode($this->request->get['filter_question'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_category'])) {
				$url .= '&filter_category=' . urlencode(html_entity_decode($this->request->get['filter_category'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
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

			$this->response->redirect($this->url->link('extension/mpfaq/mpfaqquestion', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {

		if (isset($this->request->get['filter_question'])) {
			$filter_question = $this->request->get['filter_question'];
		} else {
			$filter_question = null;
		}

		if (isset($this->request->get['filter_category'])) {
			$filter_category = $this->request->get['filter_category'];
		} else {
			$filter_category = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
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

		if (isset($this->request->get['filter_question'])) {
			$url .= '&filter_question=' . urlencode(html_entity_decode($this->request->get['filter_question'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category=' . urlencode(html_entity_decode($this->request->get['filter_category'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
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

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/mpfaq/mpfaqquestion', 'user_token=' . $this->session->data['user_token'] . $url, true)
		];

		$data['add'] = $this->url->link('extension/mpfaq/mpfaqquestion/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/mpfaq/mpfaqquestion/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		
		$data['mpfaqquestions'] = [];

		$filter_data = [
			'filter_question'	  => $filter_question,
			'filter_category'	  => $filter_category,
			'filter_status'   => $filter_status,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		];

		$this->load->model('extension/mpfaq/mpfaqcategory');

		$mpfaqquestion_total = $this->model_extension_mpfaq_mpfaqquestion->getTotalMpfaqquestions();

		$results = $this->model_extension_mpfaq_mpfaqquestion->getMpfaqquestions($filter_data);
		foreach ($results as $result) {
			$data['mpfaqquestions'][] = [
				'name' 				 => $result['name'],
				'mpfaqquestion_id' 	 => $result['mpfaqquestion_id'],
				'question'     		 => $result['question'],
				'sort_order' 		 => $result['sort_order'],
				'status'   			 => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit'        => $this->url->link('extension/mpfaq/mpfaqquestion/edit', 'user_token=' . $this->session->data['user_token'] . '&mpfaqquestion_id=' . $result['mpfaqquestion_id'] . $url, true),
				'delete'      => $this->url->link('extension/mpfaq/mpfaqquestion/delete', 'user_token=' . $this->session->data['user_token'] . '&mpfaqquestion_id=' . $result['mpfaqquestion_id'] . $url, true)
			];
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['column_question'] = $this->language->get('column_question');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_action'] = $this->language->get('column_action');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_category'] = $this->language->get('column_category');

		$data['entry_question'] = $this->language->get('entry_question');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_category'] = $this->language->get('entry_category');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_rebuild'] = $this->language->get('button_rebuild');
		$data['button_filter'] = $this->language->get('button_filter');

		$data['user_token'] = $this->session->data['user_token'];

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
			$data['selected'] = [];
		}

		$url = '';

		if (isset($this->request->get['filter_question'])) {
			$url .= '&filter_question=' . urlencode(html_entity_decode($this->request->get['filter_question'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category=' . urlencode(html_entity_decode($this->request->get['filter_category'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_question'] = $this->url->link('extension/mpfaq/mpfaqquestion', 'user_token=' . $this->session->data['user_token'] . '&sort=fd.question' . $url, true);
		$data['sort_sort_order'] = $this->url->link('extension/mpfaq/mpfaqquestion', 'user_token=' . $this->session->data['user_token'] . '&sort=f.sort_order' . $url, true);
		$data['sort_status'] = $this->url->link('extension/mpfaq/mpfaqquestion', 'user_token=' . $this->session->data['user_token'] . '&sort=f.status' . $url, true);
		$data['sort_category'] = $this->url->link('extension/mpfaq/mpfaqquestion', 'user_token=' . $this->session->data['user_token'] . '&sort=mfcd.name' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_question'])) {
			$url .= '&filter_question=' . urlencode(html_entity_decode($this->request->get['filter_question'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category=' . urlencode(html_entity_decode($this->request->get['filter_category'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $mpfaqquestion_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/mpfaq/mpfaqquestion', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($mpfaqquestion_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($mpfaqquestion_total - $this->config->get('config_limit_admin'))) ? $mpfaqquestion_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $mpfaqquestion_total, ceil($mpfaqquestion_total / $this->config->get('config_limit_admin')));

		$data['filter_question'] = $filter_question;
		$data['filter_category'] = $filter_category;
		$data['filter_status'] = $filter_status;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/mpfaq/mpfaqquestion_list', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['mpfaqquestion_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_question'] = $this->language->get('entry_question');
		$data['entry_answer'] = $this->language->get('entry_answer');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_category'] = $this->language->get('entry_category');

		$data['help_category'] = $this->language->get('help_category');
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['question'])) {
			$data['error_question'] = $this->error['question'];
		} else {
			$data['error_question'] = [];
		}

		if (isset($this->error['mpfaqcategory'])) {
			$data['error_category'] = $this->error['mpfaqcategory'];
		} else {
			$data['error_category'] = [];
		}

		$url = '';

		if (isset($this->request->get['filter_question'])) {
			$url .= '&filter_question=' . urlencode(html_entity_decode($this->request->get['filter_question'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category=' . urlencode(html_entity_decode($this->request->get['filter_category'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
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

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/mpfaq/mpfaqquestion', 'user_token=' . $this->session->data['user_token'] . $url, true)
		];

		if (!isset($this->request->get['mpfaqquestion_id'])) {
			$data['action'] = $this->url->link('extension/mpfaq/mpfaqquestion/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/mpfaq/mpfaqquestion/edit', 'user_token=' . $this->session->data['user_token'] . '&mpfaqquestion_id=' . $this->request->get['mpfaqquestion_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('extension/mpfaq/mpfaqquestion', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['mpfaqquestion_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$mpfaqquestion_info = $this->model_extension_mpfaq_mpfaqquestion->getMpfaqquestion($this->request->get['mpfaqquestion_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['mpfaqquestion_description'])) {
			$data['mpfaqquestion_description'] = $this->request->post['mpfaqquestion_description'];
		} elseif (isset($this->request->get['mpfaqquestion_id'])) {
			$data['mpfaqquestion_description'] = $this->model_extension_mpfaq_mpfaqquestion->getMpfaqquestionDescriptions($this->request->get['mpfaqquestion_id']);
		} else {
			$data['mpfaqquestion_description'] = [];
		}		

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($mpfaqquestion_info)) {
			$data['sort_order'] = $mpfaqquestion_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($mpfaqquestion_info)) {
			$data['status'] = $mpfaqquestion_info['status'];
		} else {
			$data['status'] = true;
		}	

		$this->load->model('extension/mpfaq/mpfaqcategory');

		if (isset($this->request->post['mpfaqcategory_id'])) {
			$data['mpfaqcategory_id'] = $this->request->post['mpfaqcategory_id'];
		} elseif (!empty($mpfaqquestion_info)) {
			$data['mpfaqcategory_id'] = $mpfaqquestion_info['mpfaqcategory_id'];
		} else {
			$data['mpfaqcategory_id'] = 0;
		}

		if (isset($this->request->post['mpfaqcategory'])) {
			$data['mpfaqcategory'] = $this->request->post['mpfaqcategory'];
		} elseif (!empty($mpfaqquestion_info)) {
			$mpfaqcategory_info = $this->model_extension_mpfaq_mpfaqcategory->getMpfaqcategory($mpfaqquestion_info['mpfaqcategory_id']);
			
			if ($mpfaqcategory_info) {
				$data['mpfaqcategory'] = $mpfaqcategory_info['name'];
			} else {
				$data['mpfaqcategory'] = '';
			}
		} else {
			$data['mpfaqcategory'] = '';
		}

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/mpfaq/mpfaqquestion_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/mpfaq/mpfaqquestion')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['mpfaqquestion_description'] as $language_id => $value) {
			if ((utf8_strlen($value['question']) < 2) || (utf8_strlen($value['question']) > 255)) {
				$this->error['question'][$language_id] = $this->language->get('error_question');
			}			
		}

		if (empty($this->request->post['mpfaqcategory'])) {
			$this->error['mpfaqcategory'] = $this->language->get('error_category');
		}
		
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/mpfaq/mpfaqquestion')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = [];

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/mpfaq/mpfaqcategory');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			$filter_data = [
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			];

			$results = $this->model_extension_mpfaq_mpfaqcategory->getMpfaqcategorys($filter_data);

			foreach ($results as $result) {
				$json[] = [
					'mpfaqcategory_id' => $result['mpfaqcategory_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				];
			}
		}

		$sort_order = [];

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function quautocomplete() {
		$json = [];

		if (isset($this->request->get['filter_question'])) {
			$this->load->model('extension/mpfaq/mpfaqquestion');

			if (isset($this->request->get['filter_question'])) {
				$filter_question = $this->request->get['filter_question'];
			} else {
				$filter_question = '';
			}

			$filter_data = [
				'filter_question' => $this->request->get['filter_question'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			];

			$results = $this->model_extension_mpfaq_mpfaqquestion->getMpfaqquestions($filter_data);
			foreach ($results as $result) {
				$json[] = [
					'mpfaqquestion_id' => $result['mpfaqquestion_id'],
					'question'        => strip_tags(html_entity_decode($result['question'], ENT_QUOTES, 'UTF-8'))
				];
			}
		}

		$sort_order = [];

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['question'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}
