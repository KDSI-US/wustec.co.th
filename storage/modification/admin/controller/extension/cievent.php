<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionCiEvent extends Controller {
	private $error = array();

	public function index() {
		if(!$this->config->get('module_cievent_setting_status')) {
			$this->response->redirect($this->url->link('marketplace/event', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->load->language('extension/cievent');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/cievent');

		$this->getList();
	}

	public function delete() {
		if(!$this->config->get('module_cievent_setting_status')) {
			$this->response->redirect($this->url->link('marketplace/event', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->load->language('extension/cievent');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/cievent');

		if (isset($this->request->post['selected']) && $this->validate()) {
			foreach ($this->request->post['selected'] as $event_id) {
				$this->model_extension_cievent->deleteEvent($event_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_code'])) {
				$url .= '&filter_code=' . $this->request->get['filter_code'];
			}

			if (isset($this->request->get['filter_trigger'])) {
				$url .= '&filter_trigger=' . $this->request->get['filter_trigger'];
			}

			if (isset($this->request->get['filter_action'])) {
				$url .= '&filter_action=' . $this->request->get['filter_action'];
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

			$this->response->redirect($this->url->link('marketplace/event', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		$this->document->addStyle('view/stylesheet/cievent/style.css');

		if (isset($this->request->get['filter_code'])) {
			$filter_code = $this->request->get['filter_code'];
		} else {
			$filter_code = null;
		}

		if (isset($this->request->get['filter_trigger'])) {
			$filter_trigger = $this->request->get['filter_trigger'];
		} else {
			$filter_trigger = null;
		}

		if (isset($this->request->get['filter_action'])) {
			$filter_action = $this->request->get['filter_action'];
		} else {
			$filter_action = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'code';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_code'])) {
			$url .= '&filter_code=' . $this->request->get['filter_code'];
		}

		if (isset($this->request->get['filter_trigger'])) {
			$url .= '&filter_trigger=' . $this->request->get['filter_trigger'];
		}

		if (isset($this->request->get['filter_action'])) {
			$url .= '&filter_action=' . $this->request->get['filter_action'];
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('marketplace/event', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['delete'] = $this->url->link('extension/cievent/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['saveevent_href'] = str_replace('&amp;', '&', $this->url->link('extension/cievent/saveEvent', 'user_token=' . $this->session->data['user_token'] . $url, true));

		$data['bulkstatus_href'] = str_replace('&amp;', '&', $this->url->link('extension/cievent/saveBulkStatus', 'user_token=' . $this->session->data['user_token'] . $url, true));

		$data['setting_href'] = $this->url->link('extension/module/cievent_setting', 'user_token=' . $this->session->data['user_token'], true);

		$data['event_href'] = $this->url->link('marketplace/event', 'user_token=' . $this->session->data['user_token'], true);

		$data['events'] = array();

		$filter_data = array(
			'filter_code' 	 => $filter_code,
			'filter_trigger' => $filter_trigger,
			'filter_action'  => $filter_action,
			'filter_status'  => $filter_status,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$event_total = $this->model_extension_cievent->getTotalEvents($filter_data);

		$results = $this->model_extension_cievent->getEvents($filter_data);

		foreach ($results as $result) {
			$data['events'][] = array(
				'event_id'   => $result['event_id'],
				'code'       => $result['code'],
				'trigger'    => $result['trigger'],
				'action'     => $result['action'],
				'sort_order' => $result['sort_order'],
				'status'     => $result['status'],
				'enable'     => $this->url->link('extension/cievent/enable', 'user_token=' . $this->session->data['user_token'] . '&event_id=' . $result['event_id'] . $url, true),
				'disable'    => $this->url->link('extension/cievent/disable', 'user_token=' . $this->session->data['user_token'] . '&event_id=' . $result['event_id'] . $url, true),
				'enabled'    => $result['status']
			);
		}

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

		$data['sort_code'] = $this->url->link('marketplace/event', 'user_token=' . $this->session->data['user_token'] . '&sort=code' . $url, true);
		$data['sort_sort_order'] = $this->url->link('marketplace/event', 'user_token=' . $this->session->data['user_token'] . '&sort=sort_order' . $url, true);
		$data['sort_status'] = $this->url->link('marketplace/event', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		$data['sort_trigger'] = $this->url->link('marketplace/event', 'user_token=' . $this->session->data['user_token'] . '&sort=trigger' . $url, true);
		$data['sort_action'] = $this->url->link('marketplace/event', 'user_token=' . $this->session->data['user_token'] . '&sort=action' . $url, true);

		$data['user_token'] = $this->session->data['user_token'];

		$url = '';

		if (isset($this->request->get['filter_code'])) {
			$url .= '&filter_code=' . $this->request->get['filter_code'];
		}

		if (isset($this->request->get['filter_trigger'])) {
			$url .= '&filter_trigger=' . $this->request->get['filter_trigger'];
		}

		if (isset($this->request->get['filter_action'])) {
			$url .= '&filter_action=' . $this->request->get['filter_action'];
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
		$pagination->total = $event_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('marketplace/event', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($event_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($event_total - $this->config->get('config_limit_admin'))) ? $event_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $event_total, ceil($event_total / $this->config->get('config_limit_admin')));

		$data['filter_code'] = $filter_code;
		$data['filter_trigger'] = $filter_trigger;
		$data['filter_action'] = $filter_action;
		$data['filter_status'] = $filter_status;
		$data['sort'] = $sort;
		$data['order'] = $order;


		$help_tooltip		 = 'You can use * to filter triggers';
		$help_tooltip		.= '<br>';
		$help_tooltip		.= '<br>';
		$help_tooltip		.= 'catalog/model/*';
		$help_tooltip		.= '<br>';
		$help_tooltip		.= 'catalog/model/*/after';
		$help_tooltip		.= '<br>';
		$help_tooltip		.= 'catalog/model/account/*/after';
		$help_tooltip		.= '<br>';
		$help_tooltip		.= 'catalog/model/account/customer/*/before';

		$data['help_tooltip'] = $help_tooltip;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/cievent', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/cievent')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if(!$this->config->get('module_cievent_setting_status')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_code']) || isset($this->request->get['filter_code'])) {
			$this->load->model('extension/cievent');

			if (isset($this->request->get['filter_code'])) {
				$filter_code = $this->request->get['filter_code'];
			} else {
				$filter_code = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = (int)$this->request->get['limit'];
			} else {
				$limit = 10;
			}

			$filter_data = array(
				'filter_code'  => $filter_code,
				'group_code'   => true,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_extension_cievent->getEvents($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'event_id' => $result['event_id'],
					'code'      => $result['code'],
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function saveEvent() {
		$this->load->language('extension/cievent');

		$this->load->model('extension/cievent');

		if(isset($this->request->get['event_id'])) {
			$event_id = $this->request->get['event_id'];
		} else {
			$event_id = 0;
		}

		$url = '';

		if (isset($this->request->get['filter_code'])) {
			$url .= '&filter_code=' . $this->request->get['filter_code'];
		}

		if (isset($this->request->get['filter_trigger'])) {
			$url .= '&filter_trigger=' . $this->request->get['filter_trigger'];
		}

		if (isset($this->request->get['filter_action'])) {
			$url .= '&filter_action=' . $this->request->get['filter_action'];
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

		$json = [];

		if(!$this->config->get('module_cievent_setting_status')) {
			$json['warning'] = $this->language->get('error_module_disabled');
		}

		if (!$this->user->hasPermission('modify', 'extension/cievent')) {
			$json['warning'] = $this->language->get('error_permission');
		}

		if($event_id) {
			$event_info = $this->model_extension_cievent->getEvent($event_id);
			if(!$event_info) {
				$json['warning'] = $this->language->get('error_not_found');
			}
		} else {
			if(empty($this->request->post['code'])) {
				$json['warning'] = $this->language->get('error_code');
			}
		}

		if(empty($this->request->post['trigger'])) {
			$json['warning'] = $this->language->get('error_trigger');
		}

		if(empty($this->request->post['action'])) {
			$json['warning'] = $this->language->get('error_action');
		}

		if(!$json) {
			if(!empty($event_info)) {
				$this->model_extension_cievent->editEvent($this->request->post, $event_info['event_id']);

				$json['event_udated'] = true;
			} else {
				$this->model_extension_cievent->addEvent($this->request->post);

				$json['event_inserted'] = true;
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$json['success'] = str_replace('&amp;', '&', $this->url->link('extension/cievent', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function saveBulkStatus() {
		$this->load->language('extension/cievent');

		$this->load->model('extension/cievent');

		$json = [];

		$url = '';

		if (isset($this->request->get['filter_code'])) {
			$url .= '&filter_code=' . $this->request->get['filter_code'];
		}

		if (isset($this->request->get['filter_trigger'])) {
			$url .= '&filter_trigger=' . $this->request->get['filter_trigger'];
		}

		if (isset($this->request->get['filter_action'])) {
			$url .= '&filter_action=' . $this->request->get['filter_action'];
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

		if(isset($this->request->get['status'])) {
			$status = $this->request->get['status'];
		} else {
			$status = 0;
		}

		if(!empty($this->request->get['event_id'])) {
			$selected = [$this->request->get['event_id']];
		} else if(!empty($this->request->post['selected'])) {
			$selected = $this->request->post['selected'];
		} else {
			$selected = [];
		}

		if(!$selected) {
			$json['warning'] = $this->language->get('error_no_select');
		}

		if (!$this->user->hasPermission('modify', 'extension/cievent')) {
			$json['warning'] = $this->language->get('error_permission');
		}

		if(!$this->config->get('module_cievent_setting_status')) {
			$json['warning'] = $this->language->get('error_module_disabled');
		}

		if(!$json) {
			foreach ($selected as $event_id) {
				if($status) {
					$this->model_extension_cievent->enableEvent($event_id);
				} else {
					$this->model_extension_cievent->disableEvent($event_id);
				}
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$json['success'] = str_replace('&amp;', '&', $this->url->link('extension/cievent', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getEvent() {
		$this->load->language('extension/cievent');

		$this->load->model('extension/cievent');

		$json = [];

		if(isset($this->request->get['event_id'])) {
			$event_id = $this->request->get['event_id'];
		} else {
			$event_id = 0;
		}

		$event_info = $this->model_extension_cievent->getEvent($event_id);

		if(!$event_info) {
			$json['warning'] = $this->language->get('error_not_found');
		}

		if(!$json) {
			$json['event_info'] = $event_info;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getEventbyField() {
		$this->load->language('extension/cievent');

		$this->load->model('extension/cievent');

		$json = [];

		if(isset($this->request->get['field'])) {
			$field = $this->request->get['field'];
		} else {
			$field = '';
		}

		$events = $this->model_extension_cievent->getEventbyField($field);

		if(!$events) {
			$json['warning'] = $this->language->get('error_not_found');
		}

		if(!$json) {
			$json['events'] = $events;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}