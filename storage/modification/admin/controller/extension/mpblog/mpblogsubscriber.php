<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionMpBlogMpBlogSubscriber extends Controller {
	use mpblog\trait_mpblog;
	private $error = [];

	public function __construct($registry) {
		parent :: __construct($registry);
		$this->igniteTraitMpBlog($registry);
	}

	public function index() {
		$this->load->language('mpblog/mpblogsubscriber');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpblog/mpblogsubscriber');

		$this->getList();
	}

	public function add() {
		$this->load->language('mpblog/mpblogsubscriber');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpblog/mpblogsubscriber');


		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			if (!isset($this->request->post['language_id'])) {
				$this->request->post['language_id'] = $this->config->get('config_language_id');
			}
			if (!isset($this->request->post['store_id'])) {
				$this->request->post['store_id'] = 0;
			}
			// check if email has registered with website
			$this->request->post['customer_id'] = 0;
			// load customer using version condition.

			$this->load->model('customer/customer');
			$customer_info = $this->model_customer_customer->getCustomerByEmail($email);
			if ($customer_info) {
				$this->request->post['customer_id'] = $customer_info['customer_id'];
			}

			$this->model_extension_mpblog_mpblogsubscriber->addSubscriber($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_customer'])) {
				$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

			$this->response->redirect($this->url->link('extension/mpblog/mpblogsubscriber', $this->token.'=' . $this->session->data[$this->token] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('mpblog/mpblogsubscriber');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpblog/mpblogsubscriber');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_mpblog_mpblogsubscriber->editSubscriber($this->request->get['mpblogsubscribers_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_customer'])) {
				$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

			$this->response->redirect($this->url->link('extension/mpblog/mpblogsubscriber', $this->token.'=' . $this->session->data[$this->token] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('mpblog/mpblogsubscriber');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpblog/mpblogsubscriber');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $mpblogsubscribers_id) {
				$this->model_extension_mpblog_mpblogsubscriber->deleteSubscriber($mpblogsubscribers_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_customer'])) {
				$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

			$this->response->redirect($this->url->link('extension/mpblog/mpblogsubscriber', $this->token.'=' . $this->session->data[$this->token] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = null;
		}

		if (isset($this->request->get['filter_customer'])) {
			$filter_customer = $this->request->get['filter_customer'];
		} else {
			$filter_customer = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = null;
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'r.date_added';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $this->token.'=' . $this->session->data[$this->token], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/mpblog/mpblogsubscriber', $this->token.'=' . $this->session->data[$this->token] . $url, true)
		];

		$data['add'] = $this->url->link('extension/mpblog/mpblogsubscriber/add', $this->token.'=' . $this->session->data[$this->token] . $url, true);
		$data['delete'] = $this->url->link('extension/mpblog/mpblogsubscriber/delete', $this->token.'=' . $this->session->data[$this->token] . $url, true);

		$data['mpblogsubscribers'] = [];

		$filter_data = [
			'filter_email'    => $filter_email,
			'filter_customer'    => $filter_customer,
			'filter_status'     => $filter_status,
			'filter_date_added' => $filter_date_added,
			'filter_date_modified' => $filter_date_modified,
			'sort'              => $sort,
			'order'             => $order,
			'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'             => $this->config->get('config_limit_admin')
		];

		$mpblogsubscriber_total = $this->model_extension_mpblog_mpblogsubscriber->getTotalSubscibers($filter_data);

		$results = $this->model_extension_mpblog_mpblogsubscriber->getSubscibers($filter_data);

		$this->load->model('setting/store');
		$this->load->model('localisation/language');

		foreach ($results as $result) {
			$store = '';
			if ($result['store_id']==0) {
				$store = $this->language->get('text_default');
			} else {
				$store_info = $this->model_setting_store->getStore($result['store_id']);
				if ($store_info) {
					$store = $store_info['name'];
				}
			}

			$language = $this->language->get('text_missing');
			$language_info = $this->model_localisation_language->getLanguage($result['language_id']);
			if ($language_info) {
				$language = $language_info['name'];
			}

			$data['mpblogsubscribers'][] = [
				'mpblogsubscribers_id'  => $result['mpblogsubscribers_id'],
				'customername'       => !empty($result['customername']) ? $result['customername'] : $this->language->get('text_guest'),
				'customer_id'       => $result['customer_id'],
				'customer'       => $result['customer_id']  ? $this->url->link('customer/customer', 'customer_id='.$result['customer_id'].'&user_token=' . $this->session->data[$this->token], true) : '',
				'email'       => $result['email'],
				'store'       => $store,
				'language'       => $language,
				'status'     => ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'date_added' => ($result['date_added'] != '0000-00-00 00:00:00') ? date($this->language->get('date_format_short'), strtotime($result['date_added'])) : '',
				'date_modified' => ($result['date_modified'] != '0000-00-00 00:00:00') ? date($this->language->get('date_format_short'), strtotime($result['date_modified'])) : '',
				'edit'       => $this->url->link('extension/mpblog/mpblogsubscriber/edit', $this->token.'=' . $this->session->data[$this->token] . '&mpblogsubscribers_id=' . $result['mpblogsubscribers_id'] . $url, true)
			];
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['column_email'] = $this->language->get('column_email');
		$data['column_customer'] = $this->language->get('column_customer');
		$data['column_store'] = $this->language->get('column_store');
		$data['column_language'] = $this->language->get('column_language');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_date_modified'] = $this->language->get('column_date_modified');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_customer'] = $this->language->get('entry_customer');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_date_added'] = $this->language->get('entry_date_added');
		$data['entry_date_modified'] = $this->language->get('entry_date_modified');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');

		$data['user_token'] = $this->session->data[$this->token];

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

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

		$data['sort_email'] = $this->url->link('extension/mpblog/mpblogsubscriber', $this->token.'=' . $this->session->data[$this->token] . '&sort=mbs.email' . $url, true);
		$data['sort_customer_id'] = $this->url->link('extension/mpblog/mpblogsubscriber', $this->token.'=' . $this->session->data[$this->token] . '&sort=mbs.customername' . $url, true);
		$data['sort_store_id'] = $this->url->link('extension/mpblog/mpblogsubscriber', $this->token.'=' . $this->session->data[$this->token] . '&sort=mbs.store_id' . $url, true);
		$data['sort_language_id'] = $this->url->link('extension/mpblog/mpblogsubscriber', $this->token.'=' . $this->session->data[$this->token] . '&sort=mbs.language_id' . $url, true);
		$data['sort_status'] = $this->url->link('extension/mpblog/mpblogsubscriber', $this->token.'=' . $this->session->data[$this->token] . '&sort=mbs.status' . $url, true);
		$data['sort_date_added'] = $this->url->link('extension/mpblog/mpblogsubscriber', $this->token.'=' . $this->session->data[$this->token] . '&sort=mbs.date_added' . $url, true);
		$data['sort_date_modified'] = $this->url->link('extension/mpblog/mpblogsubscriber', $this->token.'=' . $this->session->data[$this->token] . '&sort=mbs.date_modified' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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
		$pagination->total = $mpblogsubscriber_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/mpblog/mpblogsubscriber', $this->token.'=' . $this->session->data[$this->token] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($mpblogsubscriber_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($mpblogsubscriber_total - $this->config->get('config_limit_admin'))) ? $mpblogsubscriber_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $mpblogsubscriber_total, ceil($mpblogsubscriber_total / $this->config->get('config_limit_admin')));

		$data['filter_email'] = $filter_email;
		$data['filter_customer'] = $filter_customer;
		$data['filter_status'] = $filter_status;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_date_modified'] = $filter_date_modified;

		$data['sort'] = $sort;
		$data['order'] = $order;
		
		$data['mpblogmenu'] = $this->load->controller('extension/mpblog/mpblogmenu');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->viewLoad('extension/mpblog/mpblogsubscriber_list', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['mpblogsubscribers_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		

		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_sendmail'] = $this->language->get('entry_sendmail');
		$data['entry_status'] = $this->language->get('entry_status');
		
		$data['help_sendmail'] = $this->language->get('help_sendmail');


		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $this->token.'=' . $this->session->data[$this->token], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/mpblog/mpblogsubscriber', $this->token.'=' . $this->session->data[$this->token] . $url, true)
		];

		if (!isset($this->request->get['mpblogsubscribers_id'])) {
			$data['action'] = $this->url->link('extension/mpblog/mpblogsubscriber/add', $this->token.'=' . $this->session->data[$this->token] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/mpblog/mpblogsubscriber/edit', $this->token.'=' . $this->session->data[$this->token] . '&mpblogsubscribers_id=' . $this->request->get['mpblogsubscribers_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('extension/mpblog/mpblogsubscriber', $this->token.'=' . $this->session->data[$this->token] . $url, true);

		if (isset($this->request->get['mpblogsubscribers_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$subscriber_info = $this->model_extension_mpblog_mpblogsubscriber->getSubsciber($this->request->get['mpblogsubscribers_id']);
		}

		$data['user_token'] = $this->session->data[$this->token];

		$this->load->model('extension/mpblog/mpblogpost');

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($subscriber_info)) {
			$data['email'] = $subscriber_info['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($subscriber_info)) {
			$data['status'] = $subscriber_info['status'];
		} else {
			$data['status'] = '';
		}

		$data['mpblogmenu'] = $this->load->controller('extension/mpblog/mpblogmenu');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->viewLoad('extension/mpblog/mpblogsubscriber_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/mpblog/mpblogsubscriber')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
				$this->error['email'] = $this->language->get('error_email');
		}

		$subscriber_info = [];
		if (!empty($this->request->post['email'])) {
			$subscriber_info = $this->model_extension_mpblog_mpblogsubscriber->subscribeEmailExists($this->request->post['email']);
		}

		if (!isset($this->request->get['mpblogsubscribers_id'])) {
			if ($subscriber_info) {
				$this->error['warning'] = $this->language->get('error_exists');
			}
		} else {
			if ($subscriber_info && ($this->request->get['mpblogsubscribers_id'] != $subscriber_info['mpblogsubscribers_id'])) {
				$this->error['warning'] = $this->language->get('error_exists');
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/mpblog/mpblogsubscriber')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = [];

		if (isset($this->request->get['filter_email']) || isset($this->request->get['filter_customer'])) {

			$this->load->model('extension/mpblog/mpblogsubscriber');

			if (isset($this->request->get['filter_email'])) {
				$filter_email = $this->request->get['filter_email'];
			} else {
				$filter_email = '';
			}

			if (isset($this->request->get['filter_customer'])) {
				$filter_customer = $this->request->get['filter_customer'];
			} else {
				$filter_customer = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = [
				'filter_email'  => $filter_email,
				'filter_customer'  => $filter_customer,
				'start'        => 0,
				'limit'        => $limit
			];

			$results = $this->model_extension_mpblog_mpblogsubscriber->getSubscibers($filter_data);

			foreach ($results as $result) {
				$json[] = [
					'mpblogsubscribers_id' => $result['mpblogsubscribers_id'],
					'email'       => strip_tags(html_entity_decode($result['email'], ENT_QUOTES, 'UTF-8')),
					'customer'       => strip_tags(html_entity_decode($result['customername'], ENT_QUOTES, 'UTF-8')),
				];
			}

		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}