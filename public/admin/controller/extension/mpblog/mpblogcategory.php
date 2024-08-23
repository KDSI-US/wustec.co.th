<?php
class ControllerExtensionMpBlogMpBlogCategory extends Controller {
	use mpblog\trait_mpblog;
	private $error = [];

	public function __construct($registry) {
		parent :: __construct($registry);
		$this->igniteTraitMpBlog($registry);
	}

	public function index() {
		$this->load->language('mpblog/mpblogcategory');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpblog/mpblogcategory');

		$this->getList();
	}

	public function add() {
		$this->load->language('mpblog/mpblogcategory');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpblog/mpblogcategory');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_mpblog_mpblogcategory->addMpBlogCategory($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_nameid'])) {
				$url .= '&filter_nameid=' . $this->request->get['filter_nameid'];
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

			$this->response->redirect($this->url->link('extension/mpblog/mpblogcategory', $this->token.'=' . $this->session->data[$this->token] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('mpblog/mpblogcategory');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpblog/mpblogcategory');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_mpblog_mpblogcategory->editMpBlogCategory($this->request->get['mpblogcategory_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_nameid'])) {
				$url .= '&filter_nameid=' . $this->request->get['filter_nameid'];
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

			$this->response->redirect($this->url->link('extension/mpblog/mpblogcategory', $this->token.'=' . $this->session->data[$this->token] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('mpblog/mpblogcategory');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpblog/mpblogcategory');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $mpblogcategory_id) {
				$this->model_extension_mpblog_mpblogcategory->deleteMpBlogCategory($mpblogcategory_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_nameid'])) {
				$url .= '&filter_nameid=' . $this->request->get['filter_nameid'];
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

			$this->response->redirect($this->url->link('extension/mpblog/mpblogcategory', $this->token.'=' . $this->session->data[$this->token] . $url, true));
		}

		$this->getList();
	}

	public function repair() {
		$this->load->language('mpblog/mpblogcategory');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpblog/mpblogcategory');

		if ($this->validateRepair()) {
			$this->model_extension_mpblog_mpblogcategory->repairMpBlogCategories();

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_nameid'])) {
				$url .= '&filter_nameid=' . $this->request->get['filter_nameid'];
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

			$this->response->redirect($this->url->link('extension/mpblog/mpblogcategory', $this->token.'=' . $this->session->data[$this->token] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_name'])) {
			$filter_name = html_entity_decode(($this->request->get['filter_name']), ENT_QUOTES, 'UTF-8');
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_nameid'])) {
			$filter_nameid = $this->request->get['filter_nameid'];
		} else {
			$filter_nameid = null;
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

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_nameid'])) {
			$url .= '&filter_nameid=' . $this->request->get['filter_nameid'];
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
			'href' => $this->url->link('common/dashboard', $this->token.'=' . $this->session->data[$this->token], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/mpblog/mpblogcategory', $this->token.'=' . $this->session->data[$this->token] . $url, true)
		];

		$data['user_token'] =  $this->session->data[$this->token];


		$data['add'] = $this->url->link('extension/mpblog/mpblogcategory/add', $this->token.'=' . $this->session->data[$this->token] . $url, true);
		$data['delete'] = $this->url->link('extension/mpblog/mpblogcategory/delete', $this->token.'=' . $this->session->data[$this->token] . $url, true);
		$data['repair'] = $this->url->link('extension/mpblog/mpblogcategory/repair', $this->token.'=' . $this->session->data[$this->token] . $url, true);

		$data['mpblogcategories'] = [];

		$filter_data = [
			'filter_name'  => $filter_name,
			'filter_nameid'  => $filter_nameid,
			'filter_status'  => $filter_status,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		];

		$mpblogcategory_total = $this->model_extension_mpblog_mpblogcategory->getTotalMpBlogCategories($filter_data);

		$results = $this->model_extension_mpblog_mpblogcategory->getMpBlogCategories($filter_data);

		foreach ($results as $result) {
			$data['mpblogcategories'][] = [
				'mpblogcategory_id' => $result['mpblogcategory_id'],
				'name'        => $result['name'],
				'sort_order'  => $result['sort_order'],
				'status'  		=> $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit'        => $this->url->link('extension/mpblog/mpblogcategory/edit', $this->token.'=' . $this->session->data[$this->token] . '&mpblogcategory_id=' . $result['mpblogcategory_id'] . $url, true),
				'delete'      => $this->url->link('extension/mpblog/mpblogcategory/delete', $this->token.'=' . $this->session->data[$this->token] . '&mpblogcategory_id=' . $result['mpblogcategory_id'] . $url, true)
			];
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_enabled'] = $this->language->get('text_enabled');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_status'] = $this->language->get('entry_status');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_rebuild'] = $this->language->get('button_rebuild');

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
			$data['selected'] = [];
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

		$data['sort_name'] = $this->url->link('extension/mpblog/mpblogcategory', $this->token.'=' . $this->session->data[$this->token] . '&sort=name' . $url, true);
		$data['sort_status'] = $this->url->link('extension/mpblog/mpblogcategory', $this->token.'=' . $this->session->data[$this->token] . '&sort=status' . $url, true);
		$data['sort_sort_order'] = $this->url->link('extension/mpblog/mpblogcategory', $this->token.'=' . $this->session->data[$this->token] . '&sort=sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_nameid'])) {
			$url .= '&filter_nameid=' . $this->request->get['filter_nameid'];
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
		$pagination->total = $mpblogcategory_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/mpblog/mpblogcategory', $this->token.'=' . $this->session->data[$this->token] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($mpblogcategory_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($mpblogcategory_total - $this->config->get('config_limit_admin'))) ? $mpblogcategory_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $mpblogcategory_total, ceil($mpblogcategory_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_nameid'] = $filter_nameid;
		$data['filter_status'] = $filter_status;
		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['mpblogmenu'] = $this->load->controller('extension/mpblog/mpblogmenu');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->viewLoad('extension/mpblog/mpblogcategory_list', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['mpblogcategory_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_meta_title'] = $this->language->get('entry_meta_title');
		$data['entry_meta_description'] = $this->language->get('entry_meta_description');
		$data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
		$data['entry_keyword'] = $this->language->get('entry_keyword');
		$data['entry_parent'] = $this->language->get('entry_parent');
		
		$data['entry_store'] = $this->language->get('entry_store');
		$data['entry_image'] = $this->language->get('entry_image');
		
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_status'] = $this->language->get('entry_status');
		

		
		$data['help_keyword'] = $this->language->get('help_keyword');
		

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_data'] = $this->language->get('tab_data');
		

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = [];
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = [];
		}

		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
		}

		if (isset($this->error['parent'])) {
			$data['error_parent'] = $this->error['parent'];
		} else {
			$data['error_parent'] = '';
		}
		
		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_nameid'])) {
			$url .= '&filter_nameid=' . $this->request->get['filter_nameid'];
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
			'href' => $this->url->link('common/dashboard', $this->token.'=' . $this->session->data[$this->token], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/mpblog/mpblogcategory', $this->token.'=' . $this->session->data[$this->token] . $url, true)
		];

		if (!isset($this->request->get['mpblogcategory_id'])) {
			$data['action'] = $this->url->link('extension/mpblog/mpblogcategory/add', $this->token.'=' . $this->session->data[$this->token] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/mpblog/mpblogcategory/edit', $this->token.'=' . $this->session->data[$this->token] . '&mpblogcategory_id=' . $this->request->get['mpblogcategory_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('extension/mpblog/mpblogcategory', $this->token.'=' . $this->session->data[$this->token] . $url, true);

		if (isset($this->request->get['mpblogcategory_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$mpblogcategory_info = $this->model_extension_mpblog_mpblogcategory->getMpBlogCategory($this->request->get['mpblogcategory_id']);
		}

		$data['get_token'] = $this->token;
		$data['token'] = $this->session->data[$this->token];

		$data['languages'] = $this->getLanguages();

		if (isset($this->request->post['mpblogcategory_description'])) {
			$data['mpblogcategory_description'] = $this->request->post['mpblogcategory_description'];
		} elseif (isset($this->request->get['mpblogcategory_id'])) {
			$data['mpblogcategory_description'] = $this->model_extension_mpblog_mpblogcategory->getMpBlogCategoryDescriptions($this->request->get['mpblogcategory_id']);
		} else {
			$data['mpblogcategory_description'] = [];
		}

		if (isset($this->request->post['path'])) {
			$data['path'] = $this->request->post['path'];
		} elseif (!empty($mpblogcategory_info)) {
			$data['path'] = $mpblogcategory_info['path'];
		} else {
			$data['path'] = '';
		}

		if (isset($this->request->post['parent_id'])) {
			$data['parent_id'] = $this->request->post['parent_id'];
		} elseif (!empty($mpblogcategory_info)) {
			$data['parent_id'] = $mpblogcategory_info['parent_id'];
		} else {
			$data['parent_id'] = 0;
		}

		
		$this->load->model('setting/store');

		$data['stores'] = [];
		
		$data['stores'][] = [
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		];
		
		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = [
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			];
		}

		if (isset($this->request->post['mpblogcategory_store'])) {
			$data['mpblogcategory_store'] = $this->request->post['mpblogcategory_store'];
		} elseif (isset($this->request->get['mpblogcategory_id'])) {
			$data['mpblogcategory_store'] = $this->model_extension_mpblog_mpblogcategory->getMpBlogCategoryStores($this->request->get['mpblogcategory_id']);
		} else {
			$data['mpblogcategory_store'] = [0];
		}

		if (isset($this->request->post['mpblogcategory_seo_url'])) {
			$data['mpblogcategory_seo_url'] = $this->request->post['mpblogcategory_seo_url'];
		} elseif (isset($this->request->get['mpblogcategory_id'])) {
			$data['mpblogcategory_seo_url'] = $this->model_extension_mpblog_mpblogcategory->getMpblogSeoUrls($this->request->get['mpblogcategory_id']);
		} else {
			$data['mpblogcategory_seo_url'] = [];
		}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($mpblogcategory_info)) {
			$data['image'] = $mpblogcategory_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($mpblogcategory_info) && is_file(DIR_IMAGE . $mpblogcategory_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($mpblogcategory_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($mpblogcategory_info)) {
			$data['sort_order'] = $mpblogcategory_info['sort_order'];
		} else {
			$data['sort_order'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($mpblogcategory_info)) {
			$data['status'] = $mpblogcategory_info['status'];
		} else {
			$data['status'] = true;
		}

		$data['text_editor'] = $this->textEditor($data);

		$data['mpblogmenu'] = $this->load->controller('extension/mpblog/mpblogmenu');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->viewLoad('extension/mpblog/mpblogcategory_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/mpblog/mpblogcategory')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['mpblogcategory_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 2) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}

			if ((utf8_strlen($value['meta_title']) < 3) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
		}

		if (isset($this->request->get['mpblogcategory_id']) && $this->request->post['parent_id']) {
			$results = $this->model_extension_mpblog_mpblogcategory->getMpBlogCategoryPath($this->request->post['parent_id']);
			
			foreach ($results as $result) {
				if ($result['path_id'] == $this->request->get['mpblogcategory_id']) {
					$this->error['parent'] = $this->language->get('error_parent');
					
					break;
				}
			}
		}

		if ($this->request->post['mpblogcategory_seo_url']) {
			$this->load->model('design/seo_url');
			
			foreach ($this->request->post['mpblogcategory_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						if (count(array_keys($language, $keyword)) > 1) {
							$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
						}						
						
						$seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);
						
						foreach ($seo_urls as $seo_url) {
							if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['mpblogcategory_id']) || (($seo_url['query'] != 'mpblogcategory_id=' . $this->request->get['mpblogcategory_id'])))) {
								$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_keyword');
								
								break;
							}
						}
					}
				}
			}
		}
		
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/mpblog/mpblogcategory')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateRepair() {
		if (!$this->user->hasPermission('modify', 'extension/mpblog/mpblogcategory')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = [];

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/mpblog/mpblogcategory');

			$filter_data = [
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			];

			$results = $this->model_extension_mpblog_mpblogcategory->getMpBlogCategories($filter_data);

			foreach ($results as $result) {
				$json[] = [
					'mpblogcategory_id' => $result['mpblogcategory_id'],
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
}
