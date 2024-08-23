<?php
class ControllerModuleTicketTicketKnowledgeArticle extends Controller {
	private $error = array();

	public function index() {
		if(VERSION <= '2.3.0.2') {
			$session_token_variable = 'token';
			$session_token = $this->session->data['token'];
		} else {
			$session_token_variable = 'user_token';
			$session_token = $this->session->data['user_token'];
		}

		$this->load->language('module_ticket/ticketknowledge_article');

		$this->document->addStyle('view/stylesheet/modulepoints/ticketsystem.css');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('module_ticket/ticketknowledge_article');

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

		$this->load->language('module_ticket/ticketknowledge_article');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('module_ticket/ticketknowledge_article');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_module_ticket_ticketknowledge_article->addTicketKnowledgeArticle($this->request->post);

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

			$this->response->redirect($this->url->link('module_ticket/ticketknowledge_article', $session_token_variable .'=' . $session_token . $url, true));
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

		$this->load->language('module_ticket/ticketknowledge_article');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('module_ticket/ticketknowledge_article');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_module_ticket_ticketknowledge_article->editTicketKnowledgeArticle($this->request->get['ticketknowledge_article_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('module_ticket/ticketknowledge_article', $session_token_variable .'=' . $session_token . $url, true));
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

		$this->load->language('module_ticket/ticketknowledge_article');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('module_ticket/ticketknowledge_article');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $ticketknowledge_article_id) {
				$this->model_module_ticket_ticketknowledge_article->deleteTicketKnowledgeArticle($ticketknowledge_article_id);
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

			$this->response->redirect($this->url->link('module_ticket/ticketknowledge_article', $session_token_variable .'=' . $session_token . $url, true));
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
			'href' => $this->url->link('module_ticket/ticketknowledge_article', $session_token_variable .'=' . $session_token . $url, true)
		);

		$data['add'] = $this->url->link('module_ticket/ticketknowledge_article/add', $session_token_variable .'=' . $session_token . $url, true);
		$data['delete'] = $this->url->link('module_ticket/ticketknowledge_article/delete', $session_token_variable .'=' . $session_token . $url, true);

		$data['ticketknowledge_articles'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$ticketknowledge_article_total = $this->model_module_ticket_ticketknowledge_article->getTotalTicketKnowledgeArticles();

		$results = $this->model_module_ticket_ticketknowledge_article->getTicketKnowledgeArticles($filter_data);

		foreach ($results as $result) {
			$data['ticketknowledge_articles'][] = array(
				'ticketknowledge_article_id' => $result['ticketknowledge_article_id'],
				'title'          => $result['title'],
				'sort_order'     => $result['sort_order'],
				'edit'           => $this->url->link('module_ticket/ticketknowledge_article/edit', $session_token_variable .'=' . $session_token . '&ticketknowledge_article_id=' . $result['ticketknowledge_article_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_title'] = $this->language->get('column_title');
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

		$data['sort_title'] = $this->url->link('module_ticket/ticketknowledge_article', $session_token_variable .'=' . $session_token . '&sort=id.title' . $url, true);
		$data['sort_sort_order'] = $this->url->link('module_ticket/ticketknowledge_article', $session_token_variable .'=' . $session_token . '&sort=i.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $ticketknowledge_article_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('module_ticket/ticketknowledge_article', $session_token_variable .'=' . $session_token . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($ticketknowledge_article_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($ticketknowledge_article_total - $this->config->get('config_limit_admin'))) ? $ticketknowledge_article_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $ticketknowledge_article_total, ceil($ticketknowledge_article_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		if(VERSION <= '2.3.0.2') {
			$this->response->setOutput($this->load->view('module_ticket/ticketknowledge_article_list.tpl', $data));
		} else {
			$this->config->set('template_engine', 'template');
			$this->response->setOutput($this->load->view('module_ticket/ticketknowledge_article_list', $data));
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

		$data['text_form'] = !isset($this->request->get['ticketknowledge_article_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_none'] = $this->language->get('text_none');

		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_related'] = $this->language->get('entry_related');
		$data['entry_banner_title'] = $this->language->get('entry_banner_title');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_meta_title'] = $this->language->get('entry_meta_title');
		$data['entry_meta_description'] = $this->language->get('entry_meta_description');
		$data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
		$data['entry_ticketknowledge_section'] = $this->language->get('entry_ticketknowledge_section');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_status'] = $this->language->get('entry_status');

		$data['help_ticketknowledge_section'] = $this->language->get('help_ticketknowledge_section');
		$data['help_related'] = $this->language->get('help_related');

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

		if (isset($this->error['banner_title'])) {
			$data['error_banner_title'] = $this->error['banner_title'];
		} else {
			$data['error_banner_title'] = array();
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = array();
		}

		if (isset($this->error['ticketknowledge_section'])) {
			$data['error_ticketknowledge_section'] = $this->error['ticketknowledge_section'];
		} else {
			$data['error_ticketknowledge_section'] = array();
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
			'href' => $this->url->link('module_ticket/ticketknowledge_article', $session_token_variable .'=' . $session_token . $url, true)
		);

		if (!isset($this->request->get['ticketknowledge_article_id'])) {
			$data['action'] = $this->url->link('module_ticket/ticketknowledge_article/add', $session_token_variable .'=' . $session_token . $url, true);
		} else {
			$data['action'] = $this->url->link('module_ticket/ticketknowledge_article/edit', $session_token_variable .'=' . $session_token . '&ticketknowledge_article_id=' . $this->request->get['ticketknowledge_article_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('module_ticket/ticketknowledge_article', $session_token_variable .'=' . $session_token . $url, true);

		if (isset($this->request->get['ticketknowledge_article_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$ticketknowledge_article_info = $this->model_module_ticket_ticketknowledge_article->getTicketKnowledgeArticle($this->request->get['ticketknowledge_article_id']);
		}

		$data['session_token'] = $session_token;

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['ticketknowledge_article_description'])) {
			$data['ticketknowledge_article_description'] = $this->request->post['ticketknowledge_article_description'];
		} elseif (isset($this->request->get['ticketknowledge_article_id'])) {
			$data['ticketknowledge_article_description'] = $this->model_module_ticket_ticketknowledge_article->getTicketKnowledgeArticleDescriptions($this->request->get['ticketknowledge_article_id']);
		} else {
			$data['ticketknowledge_article_description'] = array();
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($ticketknowledge_article_info)) {
			$data['status'] = $ticketknowledge_article_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($ticketknowledge_article_info)) {
			$data['sort_order'] = $ticketknowledge_article_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		$this->load->model('module_ticket/ticketknowledge_section');

		if (isset($this->request->post['ticketknowledge_section_id'])) {
			$data['ticketknowledge_section_id'] = $this->request->post['ticketknowledge_section_id'];
		} elseif (!empty($ticketknowledge_article_info)) {
			$data['ticketknowledge_section_id'] = $ticketknowledge_article_info['ticketknowledge_section_id'];
		} else {
			$data['ticketknowledge_section_id'] = 0;
		}

		if (isset($this->request->post['ticketknowledge_section'])) {
			$data['ticketknowledge_section'] = $this->request->post['ticketknowledge_section'];
		} elseif (!empty($ticketknowledge_article_info)) {
			$ticketknowledge_section_info = $this->model_module_ticket_ticketknowledge_section->getTicketKnowledgeSectionDescription($ticketknowledge_article_info['ticketknowledge_section_id']);

			if ($ticketknowledge_section_info) {
				$data['ticketknowledge_section'] = $ticketknowledge_section_info['title'];
			} else {
				$data['ticketknowledge_section'] = '';
			}
		} else {
			$data['ticketknowledge_section'] = '';
		}

		if (isset($this->request->post['ticketknowledge_article'])) {
			$ticketknowledge_articles = $this->request->post['ticketknowledge_article'];
		} elseif (isset($this->request->get['ticketknowledge_article_id'])) {
			$ticketknowledge_articles = $this->model_module_ticket_ticketknowledge_article->getTicketknowledgeArticleRelated($this->request->get['ticketknowledge_article_id']);
		} else {
			$ticketknowledge_articles = array();
		}

		$data['ticketknowledge_article_relateds'] = array();

		foreach ($ticketknowledge_articles as $ticketknowledge_article_id) {
			$related_info = $this->model_module_ticket_ticketknowledge_article->getTicketKnowledgeArticle($ticketknowledge_article_id);

			if ($related_info) {
				$data['ticketknowledge_article_relateds'][] = array(
					'ticketknowledge_article_id' => $related_info['ticketknowledge_article_id'],
					'title'       				 => $related_info['title']
				);
			}
		}

		$data['summernote'] = '';

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		if(VERSION <= '2.3.0.2') {
			$this->response->setOutput($this->load->view('module_ticket/ticketknowledge_article_form.tpl', $data));
		} else {
			$this->config->set('template_engine', 'template');
			$this->response->setOutput($this->load->view('module_ticket/ticketknowledge_article_form', $data));
		}
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'module_ticket/ticketknowledge_article')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['ticketknowledge_article_description'] as $language_id => $value) {
			if ((utf8_strlen($value['title']) < 3) || (utf8_strlen($value['title']) > 255)) {
				$this->error['title'][$language_id] = $this->language->get('error_title');
			}

			if ((utf8_strlen($value['banner_title']) < 3) || (utf8_strlen($value['banner_title']) > 255)) {
				$this->error['banner_title'][$language_id] = $this->language->get('error_banner_title');
			}

			if ((utf8_strlen($value['meta_title']) < 3) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
		}

		if(empty($this->request->post['ticketknowledge_section'])) {
			$this->error['ticketknowledge_section'] = $this->language->get('error_ticketknowledge_section');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'module_ticket/ticketknowledge_article')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		if(VERSION <= '2.3.0.2') {
			$session_token_variable = 'token';
			$session_token = $this->session->data['token'];
		} else {
			$session_token_variable = 'user_token';
			$session_token = $this->session->data['user_token'];
		}

		$json = array();

		if (isset($this->request->get['filter_title'])) {
			$this->load->model('module_ticket/ticketknowledge_article');

			$filter_data = array(
				'filter_title' => $this->request->get['filter_title'],
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_module_ticket_ticketknowledge_article->getTicketKnowledgeArticles($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'ticketknowledge_article_id' => $result['ticketknowledge_article_id'],
					'title'            			  => strip_tags(html_entity_decode($result['title'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['title'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}