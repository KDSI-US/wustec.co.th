<?php
class ControllerModuleTicketTicketrequest extends Controller {
	private $error = array();

	public function index() {
		if(VERSION <= '2.3.0.2') {
			$session_token_variable = 'token';
			$session_token = $this->session->data['token'];
		} else {
			$session_token_variable = 'user_token';
			$session_token = $this->session->data['user_token'];
		}

		$this->document->addStyle('view/stylesheet/modulepoints/ticketsystem.css');

		$this->load->language('module_ticket/ticketrequest');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('module_ticket/ticketrequest');

		$this->load->model('module_ticket/ticketdepartment');

		$this->load->model('module_ticket/ticketstatus');

		$this->load->model('module_ticket/createtable');
		$this->model_module_ticket_createtable->Createtable();

		$this->getList();
	}

	public function delete() {
		if(VERSION <= '2.3.0.2') {
			$session_token_variable = 'token';
			$session_token = $this->session->data['token'];
		} else {
			$session_token_variable = 'user_token';
			$session_token = $this->session->data['user_token'];
		}

		$this->load->language('module_ticket/ticketrequest');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('module_ticket/ticketrequest');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $ticketrequest_id) {
				$this->model_module_ticket_ticketrequest->deleteTicketrequest($ticketrequest_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . $this->request->get['filter_email'];
			}

			if (isset($this->request->get['filter_subject'])) {
				$url .= '&filter_subject=' . $this->request->get['filter_subject'];
			}

			if (isset($this->request->get['filter_ticketrequest_id'])) {
				$url .= '&filter_ticketrequest_id=' . $this->request->get['filter_ticketrequest_id'];
			}

			if (isset($this->request->get['filter_ticketstatus'])) {
				$url .= '&filter_ticketstatus=' . $this->request->get['filter_ticketstatus'];
			}

			if (isset($this->request->get['filter_ticketdepartment'])) {
				$url .= '&filter_ticketdepartment=' . $this->request->get['filter_ticketdepartment'];
			}

			if (isset($this->request->get['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
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

			$this->response->redirect($this->url->link('module_ticket/ticketrequest', $session_token_variable .'=' . $session_token . $url, true));
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

		$this->document->addStyle('view/stylesheet/modulepoints/ticketsystem.css');

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}

		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = null;
		}

		if (isset($this->request->get['filter_subject'])) {
			$filter_subject = $this->request->get['filter_subject'];
		} else {
			$filter_subject = null;
		}

		if (isset($this->request->get['filter_ticketrequest_id'])) {
			$filter_ticketrequest_id = $this->request->get['filter_ticketrequest_id'];
		} else {
			$filter_ticketrequest_id = null;
		}

		if (isset($this->request->get['filter_ticketstatus'])) {
			$filter_ticketstatus = $this->request->get['filter_ticketstatus'];
		} else {
			$filter_ticketstatus = null;
		}

		if (isset($this->request->get['filter_ticketdepartment'])) {
			$filter_ticketdepartment = $this->request->get['filter_ticketdepartment'];
		} else {
			$filter_ticketdepartment = null;
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 't.date_modified';
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

		$url = '';

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . $this->request->get['filter_email'];
		}

		if (isset($this->request->get['filter_subject'])) {
			$url .= '&filter_subject=' . $this->request->get['filter_subject'];
		}

		if (isset($this->request->get['filter_ticketrequest_id'])) {
			$url .= '&filter_ticketrequest_id=' . $this->request->get['filter_ticketrequest_id'];
		}

		if (isset($this->request->get['filter_ticketstatus'])) {
			$url .= '&filter_ticketstatus=' . $this->request->get['filter_ticketstatus'];
		}

		if (isset($this->request->get['filter_ticketdepartment'])) {
			$url .= '&filter_ticketdepartment=' . $this->request->get['filter_ticketdepartment'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $session_token_variable .'=' . $session_token, true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('module_ticket/ticketrequest', $session_token_variable .'=' . $session_token . $url, true)
		);

		$data['add'] = $this->url->link('module_ticket/ticketrequest/add', $session_token_variable .'=' . $session_token . $url, true);
		$data['delete'] = $this->url->link('module_ticket/ticketrequest/delete', $session_token_variable .'=' . $session_token . $url, true);

		$data['ticketrequests'] = array();

		$filter_data = array(
			'filter_date_added' 		=> $filter_date_added,
			'filter_email' 				=> $filter_email,
			'filter_subject' 			=> $filter_subject,
			'filter_ticketrequest_id'	=> $filter_ticketrequest_id,
			'filter_ticketstatus' 		=> $filter_ticketstatus,
			'filter_ticketdepartment' 	=> $filter_ticketdepartment,
			'filter_date_modified' 		=> $filter_date_modified,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$ticketrequest_total = $this->model_module_ticket_ticketrequest->getTotalTicketrequests($filter_data);

		$results = $this->model_module_ticket_ticketrequest->getTicketrequests($filter_data);

		foreach ($results as $result) {
			$ticketstatus_info = $this->model_module_ticket_ticketstatus->getTicketstatus($result['ticketstatus_id']);
			$data['ticketrequests'][] = array(
				'ticketrequest_id'  => $result['ticketrequest_id'],
				'subject'           => $result['subject'],
				'ticketuser_name'   => $result['ticketuser_name'],
				'ticketdepartment'  => $result['ticketdepartment'],
				'bgcolor'     	 	=> ($ticketstatus_info ? $ticketstatus_info['bgcolor'] : ''),
				'textcolor'     	=> ($ticketstatus_info ? $ticketstatus_info['textcolor'] : ''),
				'email'     		=> $result['email'],
				'ticketstatus'      => $result['ticketstatus'],
				'date_added'     	=> date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_modified'     => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
				'view'              => $this->url->link('module_ticket/ticketrequest/info', $session_token_variable .'=' . $session_token . '&ticketrequest_id=' . $result['ticketrequest_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['entry_subject'] = $this->language->get('entry_subject');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_date_added'] = $this->language->get('entry_date_added');
		$data['entry_date_modified'] = $this->language->get('entry_date_modified');
		$data['entry_ticketdepartment'] = $this->language->get('entry_ticketdepartment');
		$data['entry_ticketrequest_id'] = $this->language->get('entry_ticketrequest_id');
		$data['entry_email'] = $this->language->get('entry_email');

		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_subject'] = $this->language->get('column_subject');
		$data['column_ticketrequest_id'] = $this->language->get('column_ticketrequest_id');
		$data['column_email'] = $this->language->get('column_email');
		$data['column_ticketstatus'] = $this->language->get('column_ticketstatus');
		$data['column_ticketdepartment'] = $this->language->get('column_ticketdepartment');
		$data['column_date_modified'] = $this->language->get('column_date_modified');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');

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

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . $this->request->get['filter_email'];
		}

		if (isset($this->request->get['filter_subject'])) {
			$url .= '&filter_subject=' . $this->request->get['filter_subject'];
		}

		if (isset($this->request->get['filter_ticketrequest_id'])) {
			$url .= '&filter_ticketrequest_id=' . $this->request->get['filter_ticketrequest_id'];
		}

		if (isset($this->request->get['filter_ticketstatus'])) {
			$url .= '&filter_ticketstatus=' . $this->request->get['filter_ticketstatus'];
		}

		if (isset($this->request->get['filter_ticketdepartment'])) {
			$url .= '&filter_ticketdepartment=' . $this->request->get['filter_ticketdepartment'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_ticketrequest_id'] = $this->url->link('module_ticket/ticketrequest', $session_token_variable .'=' . $session_token . '&sort=t.ticketrequest_id' . $url, true);
		$data['sort_subject'] = $this->url->link('module_ticket/ticketrequest', $session_token_variable .'=' . $session_token . '&sort=t.subject' . $url, true);
		$data['sort_email'] = $this->url->link('module_ticket/ticketrequest', $session_token_variable .'=' . $session_token . '&sort=t.email' . $url, true);
		$data['sort_ticketstatus'] = $this->url->link('module_ticket/ticketrequest', $session_token_variable .'=' . $session_token . '&sort=ticketstatus' . $url, true);
		$data['sort_ticketdepartment'] = $this->url->link('module_ticket/ticketrequest', $session_token_variable .'=' . $session_token . '&sort=ticketdepartment' . $url, true);
		$data['sort_date_added'] = $this->url->link('module_ticket/ticketrequest', $session_token_variable .'=' . $session_token . '&sort=t.date_added' . $url, true);
		$data['sort_date_modified'] = $this->url->link('module_ticket/ticketrequest', $session_token_variable .'=' . $session_token . '&sort=t.date_modified' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . $this->request->get['filter_email'];
		}

		if (isset($this->request->get['filter_subject'])) {
			$url .= '&filter_subject=' . $this->request->get['filter_subject'];
		}

		if (isset($this->request->get['filter_ticketrequest_id'])) {
			$url .= '&filter_ticketrequest_id=' . $this->request->get['filter_ticketrequest_id'];
		}

		if (isset($this->request->get['filter_ticketstatus'])) {
			$url .= '&filter_ticketstatus=' . $this->request->get['filter_ticketstatus'];
		}

		if (isset($this->request->get['filter_ticketdepartment'])) {
			$url .= '&filter_ticketdepartment=' . $this->request->get['filter_ticketdepartment'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $ticketrequest_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('module_ticket/ticketrequest', $session_token_variable .'=' . $session_token . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($ticketrequest_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($ticketrequest_total - $this->config->get('config_limit_admin'))) ? $ticketrequest_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $ticketrequest_total, ceil($ticketrequest_total / $this->config->get('config_limit_admin')));

		$data['filter_date_added']	= $filter_date_added;
		$data['filter_email']	= $filter_email;
		$data['filter_subject']	= $filter_subject;
		$data['filter_ticketrequest_id']	= $filter_ticketrequest_id;
		$data['filter_ticketstatus']	= $filter_ticketstatus;
		$data['filter_ticketdepartment']	= $filter_ticketdepartment;
		$data['filter_date_modified']	= $filter_date_modified;
		$data['sort'] = $sort;
		$data['order'] = $order;


		$this->load->model('module_ticket/ticketstatus');
		$data['ticketstatuses'] = $this->model_module_ticket_ticketstatus->getTicketstatuses();

		$this->load->model('module_ticket/ticketdepartment');
		$data['ticketdepartments'] = $this->model_module_ticket_ticketdepartment->getTicketdepartments();

		$data['session_token'] = $session_token;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		if(VERSION <= '2.3.0.2') {
			$this->response->setOutput($this->load->view('module_ticket/ticketrequest_list.tpl', $data));
		} else {
			$this->config->set('template_engine', 'template');
			$this->response->setOutput($this->load->view('module_ticket/ticketrequest_list', $data));
		}
	}

	public function info() {
		if(VERSION <= '2.3.0.2') {
			$session_token_variable = 'token';
			$session_token = $this->session->data['token'];
		} else {
			$session_token_variable = 'user_token';
			$session_token = $this->session->data['user_token'];
		}

		$this->load->language('module_ticket/ticketrequest');

		$this->document->addStyle('view/stylesheet/modulepoints/ticketsystem.css');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addStyle('view/stylesheet/modulepoints/ticketsystem.css');

		$this->load->model('module_ticket/ticketrequest');

		$this->load->model('module_ticket/ticketstatus');

		$this->load->model('module_ticket/ticketuser');

		$this->load->model('module_ticket/ticketdepartment');

		$this->load->model('user/user');

		$this->load->model('tool/upload');

		$this->load->model('tool/image');

		$url = '';

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . $this->request->get['filter_email'];
		}

		if (isset($this->request->get['filter_subject'])) {
			$url .= '&filter_subject=' . $this->request->get['filter_subject'];
		}

		if (isset($this->request->get['filter_ticketrequest_id'])) {
			$url .= '&filter_ticketrequest_id=' . $this->request->get['filter_ticketrequest_id'];
		}

		if (isset($this->request->get['filter_ticketstatus'])) {
			$url .= '&filter_ticketstatus=' . $this->request->get['filter_ticketstatus'];
		}

		if (isset($this->request->get['filter_ticketdepartment'])) {
			$url .= '&filter_ticketdepartment=' . $this->request->get['filter_ticketdepartment'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
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

		if (isset($this->request->get['ticketrequest_id'])) {
			$ticketrequest_id = $this->request->get['ticketrequest_id'];
		} else {
			$ticketrequest_id = 0;
		}

		if (isset($this->request->get['chat_page'])) {
			$chat_page = $this->request->get['chat_page'];
		} else {
			$chat_page = 1;
		}

		$chat_url = '';
		if (isset($this->request->get['ticketrequest_id'])) {
			$chat_url .= '&ticketrequest_id=' . $this->request->get['ticketrequest_id'];
		}

		if (isset($this->request->get['chat_page'])) {
			$chat_url .= '&chat_page=' . $this->request->get['chat_page'];
		}

		$data['userphoto_display'] = $this->config->get('ticketsetting_userphoto_display');
		$data['staffphoto_display'] = $this->config->get('ticketsetting_staffphoto_display');

		$ticketrequest_info = $this->model_module_ticket_ticketrequest->getTicketrequest($ticketrequest_id);

		if ($ticketrequest_info) {
			$data['heading_title'] = $this->language->get('heading_title');
			$data['heading_title_info'] = $this->language->get('heading_title_info');

			$data['text_ticket'] = $this->language->get('text_ticket');

			$data['text_subject'] = $this->language->get('text_subject');
			$data['text_department'] = $this->language->get('text_department');
			$data['text_view'] = $this->language->get('text_view');
			$data['text_status'] = $this->language->get('text_status');
			$data['text_registered_status'] = $this->language->get('text_registered_status');
			$data['text_name'] = $this->language->get('text_name');
			$data['text_email'] = $this->language->get('text_email');
			$data['text_ip'] = $this->language->get('text_ip');
			$data['text_date_added'] = $this->language->get('text_date_added');
			$data['text_date_modified'] = $this->language->get('text_date_modified');
			$data['text_reply'] = $this->language->get('text_reply');
			$data['text_loading'] = $this->language->get('text_loading');

			$data['entry_attachments'] = $this->language->get('entry_attachments');
			$data['entry_status'] = $this->language->get('entry_status');

			$data['button_add_file'] = $this->language->get('button_add_file');
			$data['button_cancel'] = $this->language->get('button_cancel');
			$data['button_submit'] = $this->language->get('button_submit');

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', $session_token_variable .'=' . $session_token, true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('module_ticket/ticketrequest', $session_token_variable .'=' . $session_token . $url, true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title_info'),
				'href' => $this->url->link('module_ticket/ticketrequest/info', $session_token_variable .'=' . $session_token . $url . $chat_url, true)
			);

			if (isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];

				unset($this->session->data['success']);
			} else {
				$data['success'] = '';
			}

			$data['cancel'] = $this->url->link('module_ticket/ticketrequest', $session_token_variable .'=' . $session_token . $url, true);
			$data['reply_action'] = str_replace('&amp;', '&', $this->url->link('module_ticket/ticketrequest/reply', $session_token_variable .'=' . $session_token . $url . $chat_url, true));

			$ticketstatus_info = $this->model_module_ticket_ticketstatus->getTicketstatusFull($ticketrequest_info['ticketstatus_id']);
			$ticketdepartment_info = $this->model_module_ticket_ticketdepartment->getTicketdepartmentFull($ticketrequest_info['ticketdepartment_id']);
			$ticketuser_info = $this->model_module_ticket_ticketuser->getTicketuser($ticketrequest_info['ticketuser_id']);

			if($ticketuser_info) {
				$data['ticketuser'] = $ticketuser_info['name'];
				$data['ticketuser_link'] = $this->url->link('module_ticket/ticketuser/edit', 'ticketuser_id='. $ticketuser_info['ticketuser_id'] .'&token='.$session_token, true);
				$data['registered_status'] = $this->language->get('text_yes');
			} else {
				$data['ticketuser'] = '';
				$data['ticketuser_link'] = '';
				$data['registered_status'] = $this->language->get('text_no');
			}

			if($ticketdepartment_info) {
				$data['ticketdepartment'] = $ticketdepartment_info['title'];
			} else {
				$data['ticketdepartment'] = '';
			}

			if($ticketstatus_info) {
				$data['status'] = $ticketstatus_info['name'];
				$data['bgcolor'] = $ticketstatus_info['bgcolor'];
				$data['textcolor'] = $ticketstatus_info['textcolor'];
			} else {
				$data['status'] = '';
				$data['bgcolor'] = '';
				$data['textcolor'] = '';
			}

			$data['ticketrequest_id'] = $ticketrequest_info['ticketrequest_id'];
			$data['ticketuser_id'] = $ticketrequest_info['ticketuser_id'];
			$data['ticketdepartment_id'] = $ticketrequest_info['ticketdepartment_id'];
			$data['ticketstatus_id'] = $ticketrequest_info['ticketstatus_id'];
			$data['email'] = $ticketrequest_info['email'];
			$data['subject'] = $ticketrequest_info['subject'];
			$data['message'] = nl2br($ticketrequest_info['message']);
			$attachments = ($ticketrequest_info['attachments']) ? json_decode($ticketrequest_info['attachments']) : array();


			$data['ip'] = $ticketrequest_info['ip'];
			$data['date_added'] = date($this->language->get('date_format_short'), strtotime($ticketrequest_info['date_added']));
			$data['date_modified'] = date($this->language->get('date_format_short'), strtotime($ticketrequest_info['date_modified']));

			$data['attachments'] = array();
			foreach($attachments as $attachment) {
				$upload_info = $this->model_tool_upload->getUploadByCode($attachment);
				if ($upload_info) {
					$data['attachments'][] = array(
						'name'		=> $upload_info['name'],
						'href'  => $this->url->link('tool/upload/download', $session_token_variable .'=' . $session_token . '&code=' . $upload_info['code'], true)
					);
				}
			}

			// Chats
			$filter_data = array(
				'start'                => ($chat_page - 1) * $this->config->get('ticketsetting_reply_list_limit'),
				'limit'                => $this->config->get('ticketsetting_reply_list_limit')
			);
			$chats_total = $this->model_module_ticket_ticketrequest->getTotalTicketRequestChats($ticketrequest_info['ticketrequest_id']);

			$chats = $this->model_module_ticket_ticketrequest->getTicketRequestChats($ticketrequest_id, $filter_data);
			$data['chats'] = array();
			foreach ($chats as $chat) {
				if($chat['client_type'] == 'staff') {

					$user_info = $this->model_user_user->getUser($chat['message_from_user_id']);
					if($user_info) {
						$client_name  = $user_info['firstname'] .' '. $user_info['lastname'];
					} else {
						$client_name  = $this->language->get('text_administrator');
					}

					$client_type_name = $this->language->get('text_staff');

					if ($data['staffphoto_display']) {
						if (!empty($user_info['image'])) {
							$profile_photo = $this->model_tool_image->resize($user_info['image'], 40, 40);
						} else {
							$profile_photo = $this->model_tool_image->resize('no_photoimage.png', 40, 40);
						}
					} else {
						$profile_photo = '';
					}
				} else {
					$user_info = $this->model_module_ticket_ticketuser->getTicketuser($chat['message_from_user_id']);
					if($user_info) {
						$client_name  = $user_info['name'];
					} else {
						$client_name  = $this->ticketuser->getName();
					}

					$client_type_name = $this->language->get('text_client');
					if ($data['userphoto_display']) {
						if (!empty($user_info['image'])) {
							$profile_photo = $this->model_tool_image->resize($user_info['image'], 40, 40);
						} else {
							$profile_photo = $this->model_tool_image->resize('no_photoimage.png', 40, 40);
						}
					} else {
						$profile_photo = '';
					}
				}

				$attachments_chats = ($chat['attachments']) ? json_decode($chat['attachments']) : array();
				$attachments_chats_data = array();
				foreach($attachments_chats as $attachments_chat) {
					$upload_info = $this->model_tool_upload->getUploadByCode($attachments_chat);
					if ($upload_info) {
						$attachments_chats_data[] = array(
							'name'		=> $upload_info['name'],
							'href'  	=> $this->url->link('tool/upload/download', $session_token_variable .'=' . $session_token . '&code=' . $upload_info['code'], true),//support/ticket_view/download
						);
					}
				}

				$data['chats'][] = array(
					'message'			=> nl2br($chat['message']),
					'client_type'		=> $chat['client_type'],
					'client_type_name'	=> $client_type_name,
					'profile_photo'		=> $profile_photo,
					'client_name'		=> $client_name,
					'attachments'		=> $attachments_chats_data,
					'date_added'		=> date($this->language->get('date_format_short'), strtotime($chat['date_added'])),
				);
			}



			$pagination = new Pagination();
			$pagination->total = $chats_total;
			$pagination->page = $chat_page;
			$pagination->limit = $this->config->get('ticketsetting_reply_list_limit');
			$pagination->url = $this->url->link('module_ticket/ticketrequest/info', $session_token_variable .'=' . $session_token . $url . '&ticketrequest_id='. $ticketrequest_id .'&chat_page={page}', true);

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($chats_total) ? (($chat_page - 1) * $this->config->get('ticketsetting_reply_list_limit')) + 1 : 0, ((($chat_page - 1) * $this->config->get('ticketsetting_reply_list_limit')) > ($chats_total - $this->config->get('ticketsetting_reply_list_limit'))) ? $chats_total : ((($chat_page - 1) * $this->config->get('ticketsetting_reply_list_limit')) + $this->config->get('ticketsetting_reply_list_limit')), $chats_total, ceil($chats_total / $this->config->get('ticketsetting_reply_list_limit')));

			$this->load->model('module_ticket/ticketstatus');
			$data['ticketstatuses'] = $this->model_module_ticket_ticketstatus->getTicketstatuses();

			$data['session_token'] = $session_token;

			$data['reply_section'] = true;
			if($data['ticketstatus_id'] == $this->config->get('ticketsetting_ticketstatus_closed_id')) {
				$data['reply_section'] = false;
			}

			if($this->config->get('ticketsetting_adminreply_close_status')) {
				$data['reply_section'] = true;
			}

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');


			if(VERSION <= '2.3.0.2') {
				$this->response->setOutput($this->load->view('module_ticket/ticketrequest_info.tpl', $data));
			} else {
				$this->config->set('template_engine', 'template');
				$this->response->setOutput($this->load->view('module_ticket/ticketrequest_info', $data));
			}
		} else {
			return new Action('error/not_found');
		}
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'module_ticket/ticketrequest')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function fileupload() {
		if(VERSION <= '2.3.0.2') {
			$session_token_variable = 'token';
			$session_token = $this->session->data['token'];
		} else {
			$session_token_variable = 'user_token';
			$session_token = $this->session->data['user_token'];
		}

		$this->load->language('tool/upload');

		$json = array();

		if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
			// Sanitize the filename
			$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8')));

			// Validate the filename length
			if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 64)) {
				$json['error'] = $this->language->get('error_filename');
			}

			// Allowed file extension types
			$allowed = array();

			$extension_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_ext_allowed'));

			$filetypes = explode("\n", $extension_allowed);

			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}

			if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Allowed file mime types
			$allowed = array();

			$mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_mime_allowed'));

			$filetypes = explode("\n", $mime_allowed);

			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}

			if (!in_array($this->request->files['file']['type'], $allowed)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Check to see if any PHP files are trying to be uploaded
			$content = file_get_contents($this->request->files['file']['tmp_name']);

			if (preg_match('/\<\?php/i', $content)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Return any upload error
			if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
				$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
			}
		} else {
			$json['error'] = $this->language->get('error_upload');
		}

		if (!$json) {
			$file = $filename . '.' . token(32);

			move_uploaded_file($this->request->files['file']['tmp_name'], DIR_UPLOAD . $file);

			// Hide the uploaded file name so people can not link to it directly.
			$this->load->model('tool/upload');

			$json['code'] = $this->model_tool_upload->addUpload($filename, $file);

			$json['filename'] = $filename;

			$json['success'] = $this->language->get('text_upload');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function reply() {
		if(VERSION <= '2.3.0.2') {
			$session_token_variable = 'token';
			$session_token = $this->session->data['token'];
		} else {
			$session_token_variable = 'user_token';
			$session_token = $this->session->data['user_token'];
		}

		$json = array();

		$this->load->language('module_ticket/ticketrequest');

		$this->load->model('module_ticket/ticketrequest');

		$url = '';

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . $this->request->get['filter_email'];
		}

		if (isset($this->request->get['filter_subject'])) {
			$url .= '&filter_subject=' . $this->request->get['filter_subject'];
		}

		if (isset($this->request->get['filter_ticketrequest_id'])) {
			$url .= '&filter_ticketrequest_id=' . $this->request->get['filter_ticketrequest_id'];
		}

		if (isset($this->request->get['filter_ticketstatus'])) {
			$url .= '&filter_ticketstatus=' . $this->request->get['filter_ticketstatus'];
		}

		if (isset($this->request->get['filter_ticketdepartment'])) {
			$url .= '&filter_ticketdepartment=' . $this->request->get['filter_ticketdepartment'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
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

		if (isset($this->request->get['ticketrequest_id'])) {
			$url .= '&ticketrequest_id=' . $this->request->get['ticketrequest_id'];
		}

		if (isset($this->request->get['chat_page'])) {
			$url .= '&chat_page=' . $this->request->get['chat_page'];
		}

		if (isset($this->request->get['ticketrequest_id'])) {
			$ticketrequest_id = $this->request->get['ticketrequest_id'];
		} else {
			$ticketrequest_id = 0;
		}

		$ticketrequest_info = $this->model_module_ticket_ticketrequest->getTicketrequest($ticketrequest_id);
		if (!$ticketrequest_info) {
			$json['warning'] = $this->language->get('error_ticketnot_found');
		}

		if (utf8_strlen($this->request->post['message']) < 10) {
			$json['warning'] = $this->language->get('error_message');
		}

		if (empty($this->request->post['ticketstatus_id'])) {
			$json['warning'] = $this->language->get('error_ticketstatus');
		}

		if(!$json) {
			$add_data = array(
				'ticketuser_id'			=> $ticketrequest_info['ticketuser_id'],
				'message_from_user_id'	=> $this->user->getId(),
				'client_type'			=> 'staff',
				'message'				=> isset($this->request->post['message']) ? $this->request->post['message'] : '',
				'attachments'			=> isset($this->request->post['attachments']) ? (array)$this->request->post['attachments'] : array(),
				'ticketstatus_id'			=> isset($this->request->post['ticketstatus_id']) ? $this->request->post['ticketstatus_id'] : '',
			);

			$this->model_module_ticket_ticketrequest->addTicketRequestChat($ticketrequest_id, $add_data);

			$json['success'] = true;
			$json['redirect'] = str_replace('&amp;', '&', $this->url->link('module_ticket/ticketrequest/info', $session_token_variable .'='. $session_token . $url, true));

			$this->session->data['success'] = $this->language->get('text_success_reply');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}