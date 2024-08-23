<?php
/* This file is under Git Control by KDSI. */
class ControllerSupportRequestList extends Controller {
	private $error = array();

	public function index() {
		if (!$this->ticketuser->isLogged()) {
			$this->session->data['support_redirect'] = $this->url->link('support/request_list', '', true);

			$this->response->redirect($this->url->link('support/support', 'login=1', true));
		}

		$this->document->addScript('catalog/view/javascript/modulepoints/ticketsystem.js');
		$this->document->addStyle('catalog/view/theme/default/stylesheet/modulepoints/ticketsystem.css');

		if($this->config->get('ticketsetting_headerfooter')) {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/modulepoints/moduleheader.css');
		}

		$this->load->language('support/request_list');

		$this->load->model('module_ticket/support');

		$this->load->model('module_ticket/ticketrequest');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_support'),
			'href' => $this->url->link('support/support')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get($this->language->get('heading_title')),
			'href' => $this->url->link('support/request_list', '', true)
		);

		$data['heading_title'] = $this->language->get('heading_title');
		$data['banner_title'] = $this->language->get('banner_title');

		$data['column_date'] = $this->language->get('column_date');
		$data['column_department'] = $this->language->get('column_department');
		$data['column_subject'] = $this->language->get('column_subject');
		$data['column_ticketid'] = $this->language->get('column_ticketid');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_date_modified'] = $this->language->get('column_date_modified');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_submit'] = $this->language->get('button_submit');
		$data['button_view'] = $this->language->get('button_view');

		$data['text_no_results'] = $this->language->get('text_no_results');

		$data['link_submission'] = $this->url->link('support/request_form', '', true);

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('ticketsetting_banner'))) {
			$data['support_banner'] = $server . 'image/' . $this->config->get('ticketsetting_banner');
		} else {
			$data['support_banner'] = '';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['ticketrequests'] = array();

		$ticketrequest_total = $this->model_module_ticket_ticketrequest->getTotalTicketRequests();

		$results = $this->model_module_ticket_ticketrequest->getTicketRequests(($page - 1) * $this->config->get('ticketsetting_list_limit'), $this->config->get('ticketsetting_list_limit'));

		foreach ($results as $result) {
			$ticketdepartment_info = $this->model_module_ticket_support->getTicketdepartment($result['ticketdepartment_id']);
			$data['ticketrequests'][] = array(
				'ticketrequest_id'  => $result['ticketrequest_id'],
				'department'     	=> $ticketdepartment_info ? $ticketdepartment_info['title'] : '',
				'bgcolor'     		=> $result['bgcolor'],
				'textcolor'     	=> $result['textcolor'],
				'status'     		=> $result['status'],
				'subject'     		=> $result['subject'],
				'date_added' 		=> date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_modified' 	=> date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
				'view'       		=> $this->url->link('support/ticket_view', 'ticketrequest_id=' . $result['ticketrequest_id'], true),
			);
		}

		$pagination = new Pagination();
		$pagination->total = $ticketrequest_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('ticketsetting_list_limit');
		$pagination->url = $this->url->link('support/request_list', 'page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($ticketrequest_total) ? (($page - 1) * $this->config->get('ticketsetting_list_limit')) + 1 : 0, ((($page - 1) * $this->config->get('ticketsetting_list_limit')) > ($ticketrequest_total - $this->config->get('ticketsetting_list_limit'))) ? $ticketrequest_total : ((($page - 1) * $this->config->get('ticketsetting_list_limit')) + $this->config->get('ticketsetting_list_limit')), $ticketrequest_total, ceil($ticketrequest_total / $this->config->get('ticketsetting_list_limit')));

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');

		if($this->config->get('ticketsetting_headerfooter')) {
			$data['header'] = $this->load->controller('support/header');
			$data['footer'] = $this->load->controller('support/footer');
		} else {
			$data['header'] = $this->load->controller('common/header');
			$data['footer'] = $this->load->controller('common/footer');
		}


		$mytheme = null;
		if($this->config->get('config_theme')) {
			$mytheme = $this->config->get('config_theme');
		} else if($this->config->get('theme_default_directory')) {
			$mytheme = $this->config->get('theme_default_directory');
		} else if($this->config->get('config_template')) {
			$mytheme = $this->config->get('config_template');
		} else{
			$mytheme = 'default';
		}

		if($mytheme == '') {
			$mytheme = 'default';
		}


		if(VERSION < '2.2.0.0') {
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/support/request_list.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/support/request_list.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/support/request_list.tpl', $data));
			}
		} else {
			if($mytheme == 'journal3' && VERSION >= '3.0.0.0') {
				if($this->config->get('template_directory') == '') {
					$this->config->set('template_directory', 'journal3/template/');
				}

				$this->response->setOutput($this->load->view('support/request_list', $data));

				$this->config->set('template_engine', 'twig');
			} else {
				$this->response->setOutput($this->load->view('support/request_list', $data));
			}
		}
	}
}
