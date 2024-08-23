<?php
class ControllerModuleTicketTicketstatus extends Controller {
	private $error = array();

	public function index() {
		if(VERSION <= '2.3.0.2') {
			$session_token_variable = 'token';
			$session_token = $this->session->data['token'];
		} else {
			$session_token_variable = 'user_token';
			$session_token = $this->session->data['user_token'];
		}

		$this->load->language('module_ticket/ticketstatus');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addStyle('view/stylesheet/modulepoints/ticketsystem.css');

		$this->load->model('module_ticket/ticketstatus');

		$this->load->model('module_ticket/createtable');
		$this->model_module_ticket_createtable->Createtable();

		$this->getList();
	}

	public function add() {
		if(VERSION <= '2.3.0.2') {
			$session_token_variable = 'token';
			$session_token = $this->session->data['token'];
		} else {
			$session_token_variable = 'user_token';
			$session_token = $this->session->data['user_token'];
		}

		$this->load->language('module_ticket/ticketstatus');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('module_ticket/ticketstatus');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_module_ticket_ticketstatus->addTicketstatus($this->request->post);

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

			$this->response->redirect($this->url->link('module_ticket/ticketstatus', $session_token_variable .'=' . $session_token . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		if(VERSION <= '2.3.0.2') {
			$session_token_variable = 'token';
			$session_token = $this->session->data['token'];
		} else {
			$session_token_variable = 'user_token';
			$session_token = $this->session->data['user_token'];
		}

		$this->load->language('module_ticket/ticketstatus');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('module_ticket/ticketstatus');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_module_ticket_ticketstatus->editTicketstatus($this->request->get['ticketstatus_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('module_ticket/ticketstatus', $session_token_variable .'=' . $session_token . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		if(VERSION <= '2.3.0.2') {
			$session_token_variable = 'token';
			$session_token = $this->session->data['token'];
		} else {
			$session_token_variable = 'user_token';
			$session_token = $this->session->data['user_token'];
		}

		$this->load->language('module_ticket/ticketstatus');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('module_ticket/ticketstatus');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $ticketstatus_id) {
				$this->model_module_ticket_ticketstatus->deleteTicketstatus($ticketstatus_id);
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

			$this->response->redirect($this->url->link('module_ticket/ticketstatus', $session_token_variable .'=' . $session_token . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if(VERSION <= '2.3.0.2') {
			$session_token_variable = 'token';
			$session_token = $this->session->data['token'];
		} else {
			$session_token_variable = 'user_token';
			$session_token = $this->session->data['user_token'];
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 't.sort_order';
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
			'href' => $this->url->link('common/dashboard', $session_token_variable .'=' . $session_token, true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('module_ticket/ticketstatus', $session_token_variable .'=' . $session_token . $url, true)
		);

		$data['add'] = $this->url->link('module_ticket/ticketstatus/add', $session_token_variable .'=' . $session_token . $url, true);
		$data['delete'] = $this->url->link('module_ticket/ticketstatus/delete', $session_token_variable .'=' . $session_token . $url, true);

		$data['ticketstatuses'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$ticketstatus_total = $this->model_module_ticket_ticketstatus->getTotalTicketstatuses();

		$results = $this->model_module_ticket_ticketstatus->getTicketstatuses($filter_data);

		foreach ($results as $result) {
			$data['ticketstatuses'][] = array(
				'ticketstatus_id' => $result['ticketstatus_id'],
				'name'           => $result['name'],
				'bgcolor'     	 => $result['bgcolor'],
				'textcolor'   	 => $result['textcolor'],
				'sort_order'     => $result['sort_order'],
				'edit'           => $this->url->link('module_ticket/ticketstatus/edit', $session_token_variable .'=' . $session_token . '&ticketstatus_id=' . $result['ticketstatus_id'] . $url, true)
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

		$data['sort_name'] = $this->url->link('module_ticket/ticketstatus', $session_token_variable .'=' . $session_token . '&sort=td.name' . $url, true);
		$data['sort_sort_order'] = $this->url->link('module_ticket/ticketstatus', $session_token_variable .'=' . $session_token . '&sort=t.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $ticketstatus_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('module_ticket/ticketstatus', $session_token_variable .'=' . $session_token . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($ticketstatus_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($ticketstatus_total - $this->config->get('config_limit_admin'))) ? $ticketstatus_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $ticketstatus_total, ceil($ticketstatus_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		if(VERSION <= '2.3.0.2') {
			$this->response->setOutput($this->load->view('module_ticket/ticketstatus_list.tpl', $data));
		} else {
			$this->config->set('template_engine', 'template');
			$this->response->setOutput($this->load->view('module_ticket/ticketstatus_list', $data));
		}
	}

	protected function getForm() {
		if(VERSION <= '2.3.0.2') {
			$session_token_variable = 'token';
			$session_token = $this->session->data['token'];
		} else {
			$session_token_variable = 'user_token';
			$session_token = $this->session->data['user_token'];
		}

		$this->document->addStyle('view/javascript/modulepoints/colorpicker/css/bootstrap-colorpicker.css');
		$this->document->addScript('view/javascript/modulepoints/colorpicker/js/bootstrap-colorpicker.js');

		$this->document->addStyle('view/stylesheet/modulepoints/ticketsystem.css');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['ticketstatus_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_bgcolor'] = $this->language->get('entry_bgcolor');
		$data['entry_textcolor'] = $this->language->get('entry_textcolor');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');


		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

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
			'href' => $this->url->link('common/dashboard', $session_token_variable .'=' . $session_token, true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('module_ticket/ticketstatus', $session_token_variable .'=' . $session_token . $url, true)
		);

		if (!isset($this->request->get['ticketstatus_id'])) {
			$data['action'] = $this->url->link('module_ticket/ticketstatus/add', $session_token_variable .'=' . $session_token . $url, true);
		} else {
			$data['action'] = $this->url->link('module_ticket/ticketstatus/edit', $session_token_variable .'=' . $session_token . '&ticketstatus_id=' . $this->request->get['ticketstatus_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('module_ticket/ticketstatus', $session_token_variable .'=' . $session_token . $url, true);

		if (isset($this->request->get['ticketstatus_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$ticketstatus_info = $this->model_module_ticket_ticketstatus->getTicketstatus($this->request->get['ticketstatus_id']);
		}

		$data['token'] = $session_token;

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['ticketstatus_description'])) {
			$data['ticketstatus_description'] = $this->request->post['ticketstatus_description'];
		} elseif (isset($this->request->get['ticketstatus_id'])) {
			$data['ticketstatus_description'] = $this->model_module_ticket_ticketstatus->getTicketstatusDescriptions($this->request->get['ticketstatus_id']);
		} else {
			$data['ticketstatus_description'] = array();
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($ticketstatus_info)) {
			$data['sort_order'] = $ticketstatus_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		if (isset($this->request->post['bgcolor'])) {
			$data['bgcolor'] = $this->request->post['bgcolor'];
		} elseif (!empty($ticketstatus_info)) {
			$data['bgcolor'] = $ticketstatus_info['bgcolor'];
		} else {
			$data['bgcolor'] = '';
		}

		if (isset($this->request->post['textcolor'])) {
			$data['textcolor'] = $this->request->post['textcolor'];
		} elseif (!empty($ticketstatus_info)) {
			$data['textcolor'] = $ticketstatus_info['textcolor'];
		} else {
			$data['textcolor'] = '';
		}

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		if(VERSION <= '2.3.0.2') {
			$this->response->setOutput($this->load->view('module_ticket/ticketstatus_form.tpl', $data));
		} else {
			$this->config->set('template_engine', 'template');
			$this->response->setOutput($this->load->view('module_ticket/ticketstatus_form', $data));
		}
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'module_ticket/ticketstatus')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['ticketstatus_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 32)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'module_ticket/ticketstatus')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}