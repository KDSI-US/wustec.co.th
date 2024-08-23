<?php
class ControllerExtensionMpBlogMpBlogComment extends Controller {
	use mpblog\trait_mpblog;
	private $error = [];

	public function __construct($registry) {
		parent :: __construct($registry);
		$this->igniteTraitMpBlog($registry);
	}

	public function index() {
		$this->load->language('mpblog/mpblogcomment');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpblog/mpblogcomment');

		$this->getList();
	}

	public function add() {
		$this->load->language('mpblog/mpblogcomment');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpblog/mpblogcomment');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_mpblog_mpblogcomment->addMpBlogComment($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_author'])) {
				$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('extension/mpblog/mpblogcomment', $this->token.'=' . $this->session->data[$this->token] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('mpblog/mpblogcomment');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpblog/mpblogcomment');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_mpblog_mpblogcomment->editMpBlogComment($this->request->get['mpblogcomment_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_author'])) {
				$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('extension/mpblog/mpblogcomment', $this->token.'=' . $this->session->data[$this->token] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('mpblog/mpblogcomment');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpblog/mpblogcomment');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $mpblogcomment_id) {
				$this->model_extension_mpblog_mpblogcomment->deleteMpBlogComment($mpblogcomment_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_author'])) {
				$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('extension/mpblog/mpblogcomment', $this->token.'=' . $this->session->data[$this->token] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_author'])) {
			$filter_author = $this->request->get['filter_author'];
		} else {
			$filter_author = null;
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

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
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
			'href' => $this->url->link('extension/mpblog/mpblogcomment', $this->token.'=' . $this->session->data[$this->token] . $url, true)
		];

		$data['add'] = $this->url->link('extension/mpblog/mpblogcomment/add', $this->token.'=' . $this->session->data[$this->token] . $url, true);
		$data['delete'] = $this->url->link('extension/mpblog/mpblogcomment/delete', $this->token.'=' . $this->session->data[$this->token] . $url, true);

		$data['mpblogcomments'] = [];

		$filter_data = [
			'filter_name'    => $filter_name,
			'filter_author'     => $filter_author,
			'filter_status'     => $filter_status,
			'filter_date_added' => $filter_date_added,
			'sort'              => $sort,
			'order'             => $order,
			'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'             => $this->config->get('config_limit_admin')
		];

		$mpblogcomment_total = $this->model_extension_mpblog_mpblogcomment->getTotalMpBlogComments($filter_data);

		$results = $this->model_extension_mpblog_mpblogcomment->getMpBlogComments($filter_data);

		foreach ($results as $result) {
			$data['mpblogcomments'][] = [
				'mpblogcomment_id'  => $result['mpblogcomment_id'],
				'name'       => $result['name'],
				'author'     => $result['author'],

				'status'     => ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'edit'       => $this->url->link('extension/mpblog/mpblogcomment/edit', $this->token.'=' . $this->session->data[$this->token] . '&mpblogcomment_id=' . $result['mpblogcomment_id'] . $url, true)
			];
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');

		$data['column_mpblogpost'] = $this->language->get('column_mpblogpost');
		$data['column_author'] = $this->language->get('column_author');

		$data['column_status'] = $this->language->get('column_status');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_mpblogpost'] = $this->language->get('entry_mpblogpost');
		$data['entry_author'] = $this->language->get('entry_author');

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

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
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

		$data['sort_mpblogpost'] = $this->url->link('extension/mpblog/mpblogcomment', $this->token.'=' . $this->session->data[$this->token] . '&sort=pd.name' . $url, true);
		$data['sort_author'] = $this->url->link('extension/mpblog/mpblogcomment', $this->token.'=' . $this->session->data[$this->token] . '&sort=r.author' . $url, true);

		$data['sort_status'] = $this->url->link('extension/mpblog/mpblogcomment', $this->token.'=' . $this->session->data[$this->token] . '&sort=r.status' . $url, true);
		$data['sort_date_added'] = $this->url->link('extension/mpblog/mpblogcomment', $this->token.'=' . $this->session->data[$this->token] . '&sort=r.date_added' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
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
		$pagination->total = $mpblogcomment_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/mpblog/mpblogcomment', $this->token.'=' . $this->session->data[$this->token] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($mpblogcomment_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($mpblogcomment_total - $this->config->get('config_limit_admin'))) ? $mpblogcomment_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $mpblogcomment_total, ceil($mpblogcomment_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_author'] = $filter_author;
		$data['filter_status'] = $filter_status;
		$data['filter_date_added'] = $filter_date_added;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['mpblogmenu'] = $this->load->controller('extension/mpblog/mpblogmenu');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->viewLoad('extension/mpblog/mpblogcomment_list', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['mpblogcomment_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');

		$data['entry_mpblogpost'] = $this->language->get('entry_mpblogpost');
		$data['entry_author'] = $this->language->get('entry_author');

		$data['entry_date_added'] = $this->language->get('entry_date_added');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_text'] = $this->language->get('entry_text');

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

		if (isset($this->error['author'])) {
			$data['error_author'] = $this->error['author'];
		} else {
			$data['error_author'] = '';
		}

		if (isset($this->error['text'])) {
			$data['error_text'] = $this->error['text'];
		} else {
			$data['error_text'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
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
			'href' => $this->url->link('extension/mpblog/mpblogcomment', $this->token.'=' . $this->session->data[$this->token] . $url, true)
		];

		if (!isset($this->request->get['mpblogcomment_id'])) {
			$data['action'] = $this->url->link('extension/mpblog/mpblogcomment/add', $this->token.'=' . $this->session->data[$this->token] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/mpblog/mpblogcomment/edit', $this->token.'=' . $this->session->data[$this->token] . '&mpblogcomment_id=' . $this->request->get['mpblogcomment_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('extension/mpblog/mpblogcomment', $this->token.'=' . $this->session->data[$this->token] . $url, true);

		if (isset($this->request->get['mpblogcomment_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$mpblogcomment_info = $this->model_extension_mpblog_mpblogcomment->getMpBlogComment($this->request->get['mpblogcomment_id']);
		}

		$data['get_token'] = $this->token;
		$data['token'] = $this->session->data[$this->token];

		$this->load->model('extension/mpblog/mpblogpost');

		if (isset($this->request->post['mpblogpost_id'])) {
			$data['mpblogpost_id'] = $this->request->post['mpblogpost_id'];
		} elseif (!empty($mpblogcomment_info)) {
			$data['mpblogpost_id'] = $mpblogcomment_info['mpblogpost_id'];
		} else {
			$data['mpblogpost_id'] = '';
		}

		if (isset($this->request->post['mpblogpost'])) {
			$data['mpblogpost'] = $this->request->post['mpblogpost'];
		} elseif (!empty($mpblogcomment_info)) {
			$data['mpblogpost'] = $mpblogcomment_info['mpblogpost'];
		} else {
			$data['mpblogpost'] = '';
		}

		if (isset($this->request->post['author'])) {
			$data['author'] = $this->request->post['author'];
		} elseif (!empty($mpblogcomment_info)) {
			$data['author'] = $mpblogcomment_info['author'];
		} else {
			$data['author'] = $this->user->getUserName();
		}

		if (isset($this->request->post['text'])) {
			$data['text'] = $this->request->post['text'];
		} elseif (!empty($mpblogcomment_info)) {
			$data['text'] = $mpblogcomment_info['text'];
		} else {
			$data['text'] = '';
		}


		if (isset($this->request->post['date_added'])) {
			$data['date_added'] = $this->request->post['date_added'];
		} elseif (!empty($mpblogcomment_info)) {
			$data['date_added'] = ($mpblogcomment_info['date_added'] != '0000-00-00 00:00' ? $mpblogcomment_info['date_added'] : '');
		} else {
			$data['date_added'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($mpblogcomment_info)) {
			$data['status'] = $mpblogcomment_info['status'];
		} else {
			$data['status'] = '';
		}

		$data['mpblogmenu'] = $this->load->controller('extension/mpblog/mpblogmenu');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->viewLoad('extension/mpblog/mpblogcomment_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/mpblog/mpblogcomment')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['mpblogpost_id']) {
			$this->error['mpblogpost'] = $this->language->get('error_mpblogpost');
		}

		if ((utf8_strlen($this->request->post['author']) < 3) || (utf8_strlen($this->request->post['author']) > 64)) {
			$this->error['author'] = $this->language->get('error_author');
		}

		if (utf8_strlen($this->request->post['text']) < 1) {
			$this->error['text'] = $this->language->get('error_text');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/mpblog/mpblogcomment')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}