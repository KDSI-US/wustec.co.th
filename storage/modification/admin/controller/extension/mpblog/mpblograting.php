<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionMpBlogMpBlogRating extends Controller {
	use mpblog\trait_mpblog;
	private $error = [];

	public function __construct($registry) {
		parent :: __construct($registry);
		$this->igniteTraitMpBlog($registry);
	}

	public function index() {
		$this->load->language('mpblog/mpblograting');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpblog/mpblograting');

		$this->getList();
	}

	public function add() {
		$this->load->language('mpblog/mpblograting');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpblog/mpblograting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_mpblog_mpblograting->addMpBlogRating($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

			$this->response->redirect($this->url->link('extension/mpblog/mpblograting', $this->token.'=' . $this->session->data[$this->token] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('mpblog/mpblograting');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpblog/mpblograting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_mpblog_mpblograting->editMpBlogRating($this->request->get['mpblograting_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

			$this->response->redirect($this->url->link('extension/mpblog/mpblograting', $this->token.'=' . $this->session->data[$this->token] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('mpblog/mpblograting');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpblog/mpblograting');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $mpblograting_id) {
				$this->model_extension_mpblog_mpblograting->deleteMpBlogRating($mpblograting_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

			$this->response->redirect($this->url->link('extension/mpblog/mpblograting', $this->token.'=' . $this->session->data[$this->token] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
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

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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
			'href' => $this->url->link('extension/mpblog/mpblograting', $this->token.'=' . $this->session->data[$this->token] . $url, true)
		];

		$data['add'] = $this->url->link('extension/mpblog/mpblograting/add', $this->token.'=' . $this->session->data[$this->token] . $url, true);
		$data['delete'] = $this->url->link('extension/mpblog/mpblograting/delete', $this->token.'=' . $this->session->data[$this->token] . $url, true);

		$data['mpblogratings'] = [];

		$filter_data = [
			'filter_name'    => $filter_name,
			'filter_status'     => $filter_status,
			'filter_date_added' => $filter_date_added,
			'sort'              => $sort,
			'order'             => $order,
			'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'             => $this->config->get('config_limit_admin')
		];

		$mpblograting_total = $this->model_extension_mpblog_mpblograting->getTotalMpBlogRatings($filter_data);

		$results = $this->model_extension_mpblog_mpblograting->getMpBlogRatings($filter_data);

		foreach ($results as $result) {
			$data['mpblogratings'][] = [
				'mpblograting_id'  => $result['mpblograting_id'],
				'name'       => $result['name'],
				'rating'     => $result['rating'],
				'status'     => ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'edit'       => $this->url->link('extension/mpblog/mpblograting/edit', $this->token.'=' . $this->session->data[$this->token] . '&mpblograting_id=' . $result['mpblograting_id'] . $url, true)
			];
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['column_mpblogpost'] = $this->language->get('column_mpblogpost');
		$data['column_rating'] = $this->language->get('column_rating');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_mpblogpost'] = $this->language->get('entry_mpblogpost');
		$data['entry_rating'] = $this->language->get('entry_rating');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_date_added'] = $this->language->get('entry_date_added');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');

		$data['get_token'] = $this->token;
		$data['token'] = $this->session->data[$this->token];

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

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_mpblogpost'] = $this->url->link('extension/mpblog/mpblograting', $this->token.'=' . $this->session->data[$this->token] . '&sort=pd.name' . $url, true);
		$data['sort_rating'] = $this->url->link('extension/mpblog/mpblograting', $this->token.'=' . $this->session->data[$this->token] . '&sort=r.rating' . $url, true);
		$data['sort_status'] = $this->url->link('extension/mpblog/mpblograting', $this->token.'=' . $this->session->data[$this->token] . '&sort=r.status' . $url, true);
		$data['sort_date_added'] = $this->url->link('extension/mpblog/mpblograting', $this->token.'=' . $this->session->data[$this->token] . '&sort=r.date_added' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $mpblograting_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/mpblog/mpblograting', $this->token.'=' . $this->session->data[$this->token] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($mpblograting_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($mpblograting_total - $this->config->get('config_limit_admin'))) ? $mpblograting_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $mpblograting_total, ceil($mpblograting_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_status'] = $filter_status;
		$data['filter_date_added'] = $filter_date_added;

		$data['sort'] = $sort;
		$data['order'] = $order;
		
		$data['mpblogmenu'] = $this->load->controller('extension/mpblog/mpblogmenu');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->viewLoad('extension/mpblog/mpblograting_list', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['mpblograting_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_mpblogpost'] = $this->language->get('entry_mpblogpost');
		$data['entry_rating'] = $this->language->get('entry_rating');
		$data['entry_date_added'] = $this->language->get('entry_date_added');
		$data['entry_status'] = $this->language->get('entry_status');


		$data['help_mpblogpost'] = $this->language->get('help_mpblogpost');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['mpblogpost'])) {
			$data['error_mpblogpost'] = $this->error['mpblogpost'];
		} else {
			$data['error_mpblogpost'] = '';
		}

		if (isset($this->error['rating'])) {
			$data['error_rating'] = $this->error['rating'];
		} else {
			$data['error_rating'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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
			'href' => $this->url->link('extension/mpblog/mpblograting', $this->token.'=' . $this->session->data[$this->token] . $url, true)
		];

		if (!isset($this->request->get['mpblograting_id'])) {
			$data['action'] = $this->url->link('extension/mpblog/mpblograting/add', $this->token.'=' . $this->session->data[$this->token] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/mpblog/mpblograting/edit', $this->token.'=' . $this->session->data[$this->token] . '&mpblograting_id=' . $this->request->get['mpblograting_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('extension/mpblog/mpblograting', $this->token.'=' . $this->session->data[$this->token] . $url, true);

		if (isset($this->request->get['mpblograting_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$mpblograting_info = $this->model_extension_mpblog_mpblograting->getMpBlogRating($this->request->get['mpblograting_id']);
		}

		$data['get_token'] = $this->token;
		$data['token'] = $this->session->data[$this->token];

		$this->load->model('extension/mpblog/mpblogpost');

		if (isset($this->request->post['mpblogpost_id'])) {
			$data['mpblogpost_id'] = $this->request->post['mpblogpost_id'];
		} elseif (!empty($mpblograting_info)) {
			$data['mpblogpost_id'] = $mpblograting_info['mpblogpost_id'];
		} else {
			$data['mpblogpost_id'] = '';
		}

		if (isset($this->request->post['mpblogpost'])) {
			$data['mpblogpost'] = $this->request->post['mpblogpost'];
		} elseif (!empty($mpblograting_info)) {
			$data['mpblogpost'] = $mpblograting_info['mpblogpost'];
		} else {
			$data['mpblogpost'] = '';
		}

		if (isset($this->request->post['rating'])) {
			$data['rating'] = $this->request->post['rating'];
		} elseif (!empty($mpblograting_info)) {
			$data['rating'] = $mpblograting_info['rating'];
		} else {
			$data['rating'] = '';
		}

		if (isset($this->request->post['date_added'])) {
			$data['date_added'] = $this->request->post['date_added'];
		} elseif (!empty($mpblograting_info)) {
			$data['date_added'] = ($mpblograting_info['date_added'] != '0000-00-00 00:00' ? $mpblograting_info['date_added'] : '');
		} else {
			$data['date_added'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($mpblograting_info)) {
			$data['status'] = $mpblograting_info['status'];
		} else {
			$data['status'] = '';
		}

		$data['mpblogmenu'] = $this->load->controller('extension/mpblog/mpblogmenu');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->viewLoad('extension/mpblog/mpblograting_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/mpblog/mpblograting')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['mpblogpost_id']) {
			$this->error['mpblogpost'] = $this->language->get('error_mpblogpost');
		}

		if (!isset($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {
			$this->error['rating'] = $this->language->get('error_rating');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/mpblog/mpblograting')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}