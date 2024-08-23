<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionMpBlogMpBlogPost extends Controller {
	use mpblog\trait_mpblog;
	private $error = [];

	public function __construct($registry) {
		parent :: __construct($registry);
		$this->igniteTraitMpBlog($registry);
	}

	public function index() {
		$this->load->language('mpblog/mpblogpost');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpblog/mpblogpost');

		$this->getList();
	}

	public function add() {
		$this->load->language('mpblog/mpblogpost');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpblog/mpblogpost');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_mpblog_mpblogpost->addMpBlogPost($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}


			if (isset($this->request->get['filter_author'])) {
				$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_author'])) {
				$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('extension/mpblog/mpblogpost', $this->token.'=' . $this->session->data[$this->token] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('mpblog/mpblogpost');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpblog/mpblogpost');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_mpblog_mpblogpost->editMpBlogPost($this->request->get['mpblogpost_id'], $this->request->post);

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

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/mpblog/mpblogpost', $this->token.'=' . $this->session->data[$this->token] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('mpblog/mpblogpost');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpblog/mpblogpost');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $mpblogpost_id) {
				$this->model_extension_mpblog_mpblogpost->deleteMpBlogPost($mpblogpost_id);
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

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/mpblog/mpblogpost', $this->token.'=' . $this->session->data[$this->token] . $url, true));
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

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pd.name';
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

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
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
			'href' => $this->url->link('extension/mpblog/mpblogpost', $this->token.'=' . $this->session->data[$this->token] . $url, true)
		];

		$data['add'] = $this->url->link('extension/mpblog/mpblogpost/add', $this->token.'=' . $this->session->data[$this->token] . $url, true);
		$data['copy'] = $this->url->link('extension/mpblog/mpblogpost/copy', $this->token.'=' . $this->session->data[$this->token] . $url, true);
		$data['delete'] = $this->url->link('extension/mpblog/mpblogpost/delete', $this->token.'=' . $this->session->data[$this->token] . $url, true);

		$data['mpblogposts'] = [];

		$filter_data = [
			'filter_name'	  => $filter_name,
			'filter_author'	  => $filter_author,
			'filter_status'   => $filter_status,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		];

		$this->load->model('tool/image');

		$mpblogpost_total = $this->model_extension_mpblog_mpblogpost->getTotalMpBlogPosts($filter_data);

		$results = $this->model_extension_mpblog_mpblogpost->getMpBlogPosts($filter_data);

		foreach ($results as $result) {

			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$data['mpblogposts'][] = [
				'mpblogpost_id' => $result['mpblogpost_id'],
				'image'      => $image,
				'name'       => $result['name'],
				'totalcomments'	=> $result['totalcomments'],
				'viewed'       => $result['viewed'],
				'author'      => $result['author'],
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit'       => $this->url->link('extension/mpblog/mpblogpost/edit', $this->token.'=' . $this->session->data[$this->token] . '&mpblogpost_id=' . $result['mpblogpost_id'] . $url, true)
			];
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_image'] = $this->language->get('column_image');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_totalcomment'] = $this->language->get('column_totalcomment');
		$data['column_author'] = $this->language->get('column_author');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_author'] = $this->language->get('entry_author');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_posttype'] = $this->language->get('entry_posttype');
		$data['entry_image'] = $this->language->get('entry_image');
		

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

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('extension/mpblog/mpblogpost', $this->token.'=' . $this->session->data[$this->token] . '&sort=pd.name' . $url, true);
		$data['sort_totalcomment'] = $this->url->link('extension/mpblog/mpblogpost', $this->token.'=' . $this->session->data[$this->token] . '&sort=totalcomments' . $url, true);
		$data['sort_author'] = $this->url->link('extension/mpblog/mpblogpost', $this->token.'=' . $this->session->data[$this->token] . '&sort=p.author' . $url, true);
		$data['sort_status'] = $this->url->link('extension/mpblog/mpblogpost', $this->token.'=' . $this->session->data[$this->token] . '&sort=p.status' . $url, true);
		$data['sort_order'] = $this->url->link('extension/mpblog/mpblogpost', $this->token.'=' . $this->session->data[$this->token] . '&sort=p.sort_order' . $url, true);

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

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $mpblogpost_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/mpblog/mpblogpost', $this->token.'=' . $this->session->data[$this->token] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($mpblogpost_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($mpblogpost_total - $this->config->get('config_limit_admin'))) ? $mpblogpost_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $mpblogpost_total, ceil($mpblogpost_total / $this->config->get('config_limit_admin')));


		$data['filter_name'] = $filter_name;
		$data['filter_author'] = $filter_author;
		$data['filter_status'] = $filter_status;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['mpblogmenu'] = $this->load->controller('extension/mpblog/mpblogmenu');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->viewLoad('extension/mpblog/mpblogpost_list', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['mpblogpost_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_none'] = $this->language->get('text_none');
		
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_plus'] = $this->language->get('text_plus');
		$data['text_minus'] = $this->language->get('text_minus');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_post_artical'] = $this->language->get('text_post_artical');
		$data['text_post_video'] = $this->language->get('text_post_video');
		$data['text_post_images'] = $this->language->get('text_post_images');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_sdescription'] = $this->language->get('entry_sdescription');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_meta_title'] = $this->language->get('entry_meta_title');
		$data['entry_meta_description'] = $this->language->get('entry_meta_description');
		$data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
		$data['entry_keyword'] = $this->language->get('entry_keyword');
		$data['entry_author'] = $this->language->get('entry_author');
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_additional_image'] = $this->language->get('entry_additional_image');
		$data['entry_store'] = $this->language->get('entry_store');
		$data['entry_mpblogcategory'] = $this->language->get('entry_mpblogcategory');
		$data['entry_related'] = $this->language->get('entry_related');
		$data['entry_relatedcategory'] = $this->language->get('entry_relatedcategory');
		$data['entry_relatedproduct'] = $this->language->get('entry_relatedproduct');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_date_available'] = $this->language->get('entry_date_available');
		
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_posttype'] = $this->language->get('entry_posttype');
		$data['entry_video'] = $this->language->get('entry_video');
		$data['entry_tag'] = $this->language->get('entry_tag');

		$data['help_keyword'] = $this->language->get('help_keyword');
		$data['help_mpblogcategory'] = $this->language->get('help_mpblogcategory');
		$data['help_related'] = $this->language->get('help_related');
		$data['help_author'] = $this->language->get('help_author');
		$data['help_tag'] = $this->language->get('help_tag');
		$data['help_video'] = $this->language->get('help_video');
		$data['help_posttype'] = $this->language->get('help_posttype');
		$data['help_relatedcategory'] = $this->language->get('help_relatedcategory');
		$data['help_relatedproduct'] = $this->language->get('help_relatedproduct');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_image_add'] = $this->language->get('button_image_add');
		$data['button_remove'] = $this->language->get('button_remove');
		
		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_data'] = $this->language->get('tab_data');
		$data['tab_links'] = $this->language->get('tab_links');
		$data['tab_image'] = $this->language->get('tab_image');
		

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

		if (isset($this->error['author'])) {
			$data['error_author'] = $this->error['author'];
		} else {
			$data['error_author'] = '';
		}

		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
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
			'href' => $this->url->link('extension/mpblog/mpblogpost', $this->token.'=' . $this->session->data[$this->token] . $url, true)
		];

		if (!isset($this->request->get['mpblogpost_id'])) {
			$data['action'] = $this->url->link('extension/mpblog/mpblogpost/add', $this->token.'=' . $this->session->data[$this->token] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/mpblog/mpblogpost/edit', $this->token.'=' . $this->session->data[$this->token] . '&mpblogpost_id=' . $this->request->get['mpblogpost_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('extension/mpblog/mpblogpost', $this->token.'=' . $this->session->data[$this->token] . $url, true);

		if (isset($this->request->get['mpblogpost_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$mpblogpost_info = $this->model_extension_mpblog_mpblogpost->getMpBlogPost($this->request->get['mpblogpost_id']);
		}

		$data['get_token'] = $this->token;
		$data['token'] = $this->session->data[$this->token];

		$data['languages'] = $this->getLanguages();

		if (isset($this->request->post['mpblogpost_description'])) {
			$data['mpblogpost_description'] = $this->request->post['mpblogpost_description'];
		} elseif (isset($this->request->get['mpblogpost_id'])) {
			$data['mpblogpost_description'] = $this->model_extension_mpblog_mpblogpost->getMpBlogPostDescriptions($this->request->get['mpblogpost_id']);
		} else {
			$data['mpblogpost_description'] = [];
		}

		if (isset($this->request->post['author'])) {
			$data['author'] = $this->request->post['author'];
		} elseif (!empty($mpblogpost_info)) {
			$data['author'] = $mpblogpost_info['author'];
		} else {
			$data['author'] = $this->user->getUserName();
		}

		
		if (isset($this->request->post['viewed'])) {
			$data['viewed'] = $this->request->post['viewed'];
		} elseif (!empty($mpblogpost_info)) {
			$data['viewed'] = $mpblogpost_info['viewed'];
		} else {
			$data['viewed'] = 0;
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


		if (isset($this->request->post['mpblogpost_store'])) {
			$data['mpblogpost_store'] = $this->request->post['mpblogpost_store'];
		} elseif (isset($this->request->get['mpblogpost_id'])) {
			$data['mpblogpost_store'] = $this->model_extension_mpblog_mpblogpost->getMpBlogPostStores($this->request->get['mpblogpost_id']);
		} else {
			$data['mpblogpost_store'] = [0];
		}
		
		if (isset($this->request->post['video'])) {
			$data['video'] = $this->request->post['video'];
		} elseif (!empty($mpblogpost_info)) {
			$data['video'] = $mpblogpost_info['video'];
		} else {
			$data['video'] = '';
		}

		if (isset($this->request->post['date_available'])) {
			$data['date_available'] = $this->request->post['date_available'];
		} elseif (!empty($mpblogpost_info)) {
			$data['date_available'] = ($mpblogpost_info['date_available'] != '0000-00-00') ? $mpblogpost_info['date_available'] : '';
		} else {
			$data['date_available'] = ['Y-m-d'];
		}


		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($mpblogpost_info)) {
			$data['status'] = $mpblogpost_info['status'];
		} else {
			$data['status'] = true;
		}
		
		if (isset($this->request->post['posttype'])) {
			$data['posttype'] = $this->request->post['posttype'];
		} elseif (!empty($mpblogpost_info)) {
			$data['posttype'] = $mpblogpost_info['posttype'];
		} else {
			$data['posttype'] = 'ARTICAL';
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($mpblogpost_info)) {
			$data['sort_order'] = $mpblogpost_info['sort_order'];
		} else {
			$data['sort_order'] = 1;
		}

		// MpBlogCategories
		$this->load->model('extension/mpblog/mpblogcategory');

		if (isset($this->request->post['mpblogpost_mpblogcategory'])) {
			$mpblogcategories = $this->request->post['mpblogpost_mpblogcategory'];
		} elseif (isset($this->request->get['mpblogpost_id'])) {
			$mpblogcategories = $this->model_extension_mpblog_mpblogpost->getMpBlogPostMpBlogCategories($this->request->get['mpblogpost_id']);
		} else {
			$mpblogcategories = [];
		}

		$data['mpblogpost_mpblogcategories'] = [];

		foreach ($mpblogcategories as $mpblogcategory_id) {
			$mpblogcategory_info = $this->model_extension_mpblog_mpblogcategory->getMpBlogCategory($mpblogcategory_id);

			if ($mpblogcategory_info) {
				$data['mpblogpost_mpblogcategories'][] = [
					'mpblogcategory_id' => $mpblogcategory_info['mpblogcategory_id'],
					'name'        => ($mpblogcategory_info['path']) ? $mpblogcategory_info['path'] . ' &gt; ' . $mpblogcategory_info['name'] : $mpblogcategory_info['name']
				];
			}
		}

		if (isset($this->request->post['mpblogpost_seo_url'])) {
			$data['mpblogpost_seo_url'] = $this->request->post['mpblogpost_seo_url'];
		} elseif (isset($this->request->get['mpblogpost_id'])) {
			$data['mpblogpost_seo_url'] = $this->model_extension_mpblog_mpblogpost->getMpBlogPostSeoUrls($this->request->get['mpblogpost_id']);
		} else {
			$data['mpblogpost_seo_url'] = [];
		}

		// Image
		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($mpblogpost_info)) {
			$data['image'] = $mpblogpost_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($mpblogpost_info) && is_file(DIR_IMAGE . $mpblogpost_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($mpblogpost_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		// Images
		if (isset($this->request->post['mpblogpost_image'])) {
			$mpblogpost_images = $this->request->post['mpblogpost_image'];
		} elseif (isset($this->request->get['mpblogpost_id'])) {
			$mpblogpost_images = $this->model_extension_mpblog_mpblogpost->getMpBlogPostImages($this->request->get['mpblogpost_id']);
		} else {
			$mpblogpost_images = [];
		}

		$data['mpblogpost_images'] = [];

		foreach ($mpblogpost_images as $mpblogpost_image) {
			if (is_file(DIR_IMAGE . $mpblogpost_image['image'])) {
				$image = $mpblogpost_image['image'];
				$thumb = $mpblogpost_image['image'];
			} else {
				$image = '';
				$thumb = 'no_image.png';
			}

			$data['mpblogpost_images'][] = [
				'image'      => $image,
				'thumb'      => $this->model_tool_image->resize($thumb, 100, 100),
				'sort_order' => $mpblogpost_image['sort_order']
			];
		}

		// Related Blogs
		
		if (isset($this->request->post['mpblogpost_related'])) {
			$mpblogposts = $this->request->post['mpblogpost_related'];
		} elseif (isset($this->request->get['mpblogpost_id'])) {
			$mpblogposts = $this->model_extension_mpblog_mpblogpost->getMpBlogPostRelated($this->request->get['mpblogpost_id']);
		} else {
			$mpblogposts = [];
		}

		$data['mpblogpost_relateds'] = [];

		foreach ($mpblogposts as $mpblogpost_id) {
			$related_info = $this->model_extension_mpblog_mpblogpost->getMpBlogPost($mpblogpost_id);

			if ($related_info) {
				$data['mpblogpost_relateds'][] = [
					'mpblogpost_id' => $related_info['mpblogpost_id'],
					'name'       => $related_info['name']
				];
			}
		}

		// Related Categories
		if (isset($this->request->post['mpblogpost_relatedcategory'])) {
			$mpblogposts = $this->request->post['mpblogpost_relatedcategory'];
		} elseif (isset($this->request->get['mpblogpost_id'])) {
			$mpblogposts = $this->model_extension_mpblog_mpblogpost->getMpBlogPostRelatedCategories($this->request->get['mpblogpost_id']);
		} else {
			$mpblogposts = [];
		}

		$data['mpblogpost_relatedcategories'] = [];

		$this->load->model('catalog/category');

		foreach ($mpblogposts as $category_id) {
			$relatedcategory_info = $this->model_catalog_category->getCategory($category_id);

			if ($relatedcategory_info) {
				$data['mpblogpost_relatedcategories'][] = [
					'category_id' => $relatedcategory_info['category_id'],
					'name'       => $relatedcategory_info['name']
				];
			}
		}

		// Related Products
		if (isset($this->request->post['mpblogpost_relatedproduct'])) {
			$mpblogposts = $this->request->post['mpblogpost_relatedproduct'];
		} elseif (isset($this->request->get['mpblogpost_id'])) {
			$mpblogposts = $this->model_extension_mpblog_mpblogpost->getMpBlogPostRelatedProducts($this->request->get['mpblogpost_id']);
		} else {
			$mpblogposts = [];
		}

		$data['mpblogpost_relatedproducts'] = [];

		$this->load->model('catalog/product');

		foreach ($mpblogposts as $product_id) {
			$relatedproduct_info = $this->model_catalog_product->getProduct($product_id);

			if ($relatedproduct_info) {
				$data['mpblogpost_relatedproducts'][] = [
					'product_id' => $relatedproduct_info['product_id'],
					'name'       => $relatedproduct_info['name']
				];
			}
		}

		$data['text_editor'] = $this->textEditor($data);

		$data['mpblogmenu'] = $this->load->controller('extension/mpblog/mpblogmenu');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->viewLoad('extension/mpblog/mpblogpost_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/mpblog/mpblogpost')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['mpblogpost_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}

			if ((utf8_strlen($value['meta_title']) < 3) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
		}

		if ((utf8_strlen($this->request->post['author']) < 1) || (utf8_strlen($this->request->post['author']) > 64)) {
			$this->error['author'] = $this->language->get('error_model');
		}

		if ($this->request->post['mpblogpost_seo_url']) {
			$this->load->model('design/seo_url');
			
			foreach ($this->request->post['mpblogpost_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						if (count(array_keys($language, $keyword)) > 1) {
							$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
						}						
						
						$seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);
						
						foreach ($seo_urls as $seo_url) {
							if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['mpblogpost_id']) || (($seo_url['query'] != 'mpblogpost_id=' . $this->request->get['mpblogpost_id'])))) {
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
		if (!$this->user->hasPermission('modify', 'extension/mpblog/mpblogpost')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = [];

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_author'])) {
			$this->load->model('extension/mpblog/mpblogpost');


			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_author'])) {
				$filter_author = $this->request->get['filter_author'];
			} else {
				$filter_author = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = [
				'filter_name'  => $filter_name,
				'filter_author' => $filter_author,
				'start'        => 0,
				'limit'        => $limit
			];

			$results = $this->model_extension_mpblog_mpblogpost->getMpBlogPosts($filter_data);

			foreach ($results as $result) {

				$json[] = [
					'mpblogpost_id' => $result['mpblogpost_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
				];
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function autocompleteCategory() {
		$json = [];

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/category');

			$filter_data = [
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			];

			$results = $this->model_catalog_category->getCategories($filter_data);

			foreach ($results as $result) {
				$json[] = [
					'category_id' => $result['category_id'],
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

	public function autocompleteProduct() {
		$json = [];

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/product');
			$this->load->model('catalog/option');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			
			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = [
				'filter_name'  => $filter_name,
				'start'        => 0,
				'limit'        => $limit
			];

			$results = $this->model_catalog_product->getProducts($filter_data);

			foreach ($results as $result) {
				$json[] = [
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
				];
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
