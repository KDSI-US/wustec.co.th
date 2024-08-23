<?php
class ControllerModuleTicketTicketDepartment extends Controller {
	private $error = array();

	public function index() {
		if(VERSION <= '2.3.0.2') {
			$session_token_variable = 'token';
			$session_token = $this->session->data['token'];
		} else {
			$session_token_variable = 'user_token';
			$session_token = $this->session->data['user_token'];
		}

		$this->load->language('module_ticket/ticketdepartment');

		$this->document->addStyle('view/stylesheet/modulepoints/ticketsystem.css');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('module_ticket/ticketdepartment');

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

		$this->load->language('module_ticket/ticketdepartment');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('module_ticket/ticketdepartment');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_module_ticket_ticketdepartment->addTicketdepartment($this->request->post);

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

			$this->response->redirect($this->url->link('module_ticket/ticketdepartment', $session_token_variable .'=' . $session_token . $url, true));
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

		$this->load->language('module_ticket/ticketdepartment');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('module_ticket/ticketdepartment');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_module_ticket_ticketdepartment->editTicketdepartment($this->request->get['ticketdepartment_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('module_ticket/ticketdepartment', $session_token_variable .'=' . $session_token . $url, true));
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

		$this->load->language('module_ticket/ticketdepartment');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('module_ticket/ticketdepartment');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $ticketdepartment_id) {
				$this->model_module_ticket_ticketdepartment->deleteTicketdepartment($ticketdepartment_id);
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

			$this->response->redirect($this->url->link('module_ticket/ticketdepartment', $session_token_variable .'=' . $session_token . $url, true));
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
			$sort = 'id.title';
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
			'href' => $this->url->link('module_ticket/ticketdepartment', $session_token_variable .'=' . $session_token . $url, true)
		);

		$data['add'] = $this->url->link('module_ticket/ticketdepartment/add', $session_token_variable .'=' . $session_token . $url, true);
		$data['delete'] = $this->url->link('module_ticket/ticketdepartment/delete', $session_token_variable .'=' . $session_token . $url, true);

		$data['ticketdepartments'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$ticketdepartment_total = $this->model_module_ticket_ticketdepartment->getTotalTicketdepartments();

		$results = $this->model_module_ticket_ticketdepartment->getTicketdepartments($filter_data);

		foreach ($results as $result) {
			$data['ticketdepartments'][] = array(
				'ticketdepartment_id' => $result['ticketdepartment_id'],
				'title'          => $result['title'],
				'sort_order'     => $result['sort_order'],
				'icon_class'     => $result['icon_class'],
				'edit'           => $this->url->link('module_ticket/ticketdepartment/edit', $session_token_variable .'=' . $session_token . '&ticketdepartment_id=' . $result['ticketdepartment_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_title'] = $this->language->get('column_title');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_icon_class'] = $this->language->get('column_icon_class');
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

		$data['sort_title'] = $this->url->link('module_ticket/ticketdepartment', $session_token_variable .'=' . $session_token . '&sort=id.title' . $url, true);
		$data['sort_sort_order'] = $this->url->link('module_ticket/ticketdepartment', $session_token_variable .'=' . $session_token . '&sort=i.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $ticketdepartment_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('module_ticket/ticketdepartment', $session_token_variable .'=' . $session_token . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($ticketdepartment_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($ticketdepartment_total - $this->config->get('config_limit_admin'))) ? $ticketdepartment_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $ticketdepartment_total, ceil($ticketdepartment_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		if(VERSION <= '2.3.0.2') {
			$this->response->setOutput($this->load->view('module_ticket/ticketdepartment_list.tpl', $data));
		} else {
			$this->config->set('template_engine', 'template');
			$this->response->setOutput($this->load->view('module_ticket/ticketdepartment_list', $data));
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
		
		$data['heading_title'] = $this->language->get('heading_title');

		$this->document->addStyle('view/stylesheet/modulepoints/ticketsystem.css');

		$data['text_form'] = !isset($this->request->get['ticketdepartment_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_sub_title'] = $this->language->get('entry_sub_title');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_icon_class'] = $this->language->get('entry_icon_class');
		$data['entry_status'] = $this->language->get('entry_status');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_data'] = $this->language->get('tab_data');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		} else {
			$data['error_title'] = array();
		}

		if (isset($this->error['icon_class'])) {
			$data['error_icon_class'] = $this->error['icon_class'];
		} else {
			$data['error_icon_class'] = '';
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
			'href' => $this->url->link('module_ticket/ticketdepartment', $session_token_variable .'=' . $session_token . $url, true)
		);

		if (!isset($this->request->get['ticketdepartment_id'])) {
			$data['action'] = $this->url->link('module_ticket/ticketdepartment/add', $session_token_variable .'=' . $session_token . $url, true);
		} else {
			$data['action'] = $this->url->link('module_ticket/ticketdepartment/edit', $session_token_variable .'=' . $session_token . '&ticketdepartment_id=' . $this->request->get['ticketdepartment_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('module_ticket/ticketdepartment', $session_token_variable .'=' . $session_token . $url, true);

		if (isset($this->request->get['ticketdepartment_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$ticketdepartment_info = $this->model_module_ticket_ticketdepartment->getTicketdepartment($this->request->get['ticketdepartment_id']);
		}

		$data['session_token'] = $session_token;

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['ticketdepartment_description'])) {
			$data['ticketdepartment_description'] = $this->request->post['ticketdepartment_description'];
		} elseif (isset($this->request->get['ticketdepartment_id'])) {
			$data['ticketdepartment_description'] = $this->model_module_ticket_ticketdepartment->getTicketdepartmentDescriptions($this->request->get['ticketdepartment_id']);
		} else {
			$data['ticketdepartment_description'] = array();
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($ticketdepartment_info)) {
			$data['status'] = $ticketdepartment_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($ticketdepartment_info)) {
			$data['sort_order'] = $ticketdepartment_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		if (isset($this->request->post['icon_class'])) {
			$data['icon_class'] = $this->request->post['icon_class'];
		} elseif (!empty($ticketdepartment_info)) {
			$data['icon_class'] = $ticketdepartment_info['icon_class'];
		} else {
			$data['icon_class'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		if(VERSION <= '2.3.0.2') {
			$this->response->setOutput($this->load->view('module_ticket/ticketdepartment_form.tpl', $data));
		} else {
			$this->config->set('template_engine', 'template');
			$this->response->setOutput($this->load->view('module_ticket/ticketdepartment_form', $data));
		}
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'module_ticket/ticketdepartment')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['ticketdepartment_description'] as $language_id => $value) {
			if ((utf8_strlen($value['title']) < 3) || (utf8_strlen($value['title']) > 64)) {
				$this->error['title'][$language_id] = $this->language->get('error_title');
			}
		}

		if(empty($this->request->post['icon_class'])) {
			$this->error['icon_class'] = $this->language->get('error_icon_class');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'module_ticket/ticketdepartment')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}