<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleGalleria extends Controller {
	private $error = array();
	private $token_var;
	private $extension_var;
	private $prefix;

	public function __construct($registry) {
		parent::__construct($registry);
		$this->token_var = (version_compare(VERSION, '3.0', '>=')) ? 'user_token' : 'token';
		$this->extension_var = (version_compare(VERSION, '3.0', '>=')) ? 'marketplace' : 'extension';
		$this->prefix = (version_compare(VERSION, '3.0', '>=')) ? 'module_' : '';
	}

	public function install() {
		$this->load->model('extension/module/galleria');
		$this->model_extension_module_galleria->install();
	}

	public function uninstall() {
		$this->load->model('extension/module/galleria');
		$this->model_extension_module_galleria->uninstall();
	}

	public function index() {
		$data = $this->load->language('extension/module/galleria');

		$heading_title = $this->language->get('heading_title');
		$this->document->setTitle($heading_title);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$this->model_setting_setting->editSetting($this->prefix . 'galleria', $this->request->post);

			$this->load->model('extension/module/galleria');
			$this->model_extension_module_galleria->addGallerySeoUrl($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			if (isset($this->request->post['apply'])) {
				$this->response->redirect($this->url->link('extension/module/galleria', $this->token_var . '=' . $this->session->data[$this->token_var], true));
			} else {
				$this->response->redirect($this->url->link($this->extension_var . '/extension', $this->token_var . '=' . $this->session->data[$this->token_var] . '&type=module', true));
			}
		}

		$this->document->addStyle('view/javascript/summernote/summernote.css');
		$this->document->addScript('view/javascript/summernote/summernote.js');
		$this->document->addScript('view/javascript/summernote/opencart.js');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $this->token_var . '=' . $this->session->data[$this->token_var], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link($this->extension_var . '/extension', $this->token_var . '=' . $this->session->data[$this->token_var] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $heading_title,
			'href' => $this->url->link('extension/module/galleria', $this->token_var . '=' . $this->session->data[$this->token_var], true)
		);

		$this->load->model('extension/module/galleria');

		$data['prefix'] = $this->prefix;
		$data['token_var'] = $this->token_var;
		$data[$this->token_var] = $this->session->data[$this->token_var];
		$data['action'] = $this->url->link('extension/module/galleria', $this->token_var . '=' . $this->session->data[$this->token_var], true);
		$data['cancel'] = $this->url->link($this->extension_var . '/extension', $this->token_var . '=' . $this->session->data[$this->token_var] . '&type=module', true);

		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();
		$data['languages'] = $languages;

		$this->load->model('setting/store');

		$data['stores'] = array();
		
		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);
		
		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}

		if (isset($this->request->post[$this->prefix . 'galleria_page_seo_url'])) {
			$data[$this->prefix . 'galleria_page_seo_url'] = $this->request->post[$this->prefix . 'galleria_page_seo_url'];
		} else {
			$data[$this->prefix . 'galleria_page_seo_url'] = $this->config->get($this->prefix . 'galleria_page_seo_url');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_unique'])) {
			$data[$this->prefix . 'galleria_unique'] = $this->request->post[$this->prefix . 'galleria_unique'];
		} else {
			$data[$this->prefix . 'galleria_unique'] = $this->config->get($this->prefix . 'galleria_unique');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_status'])) {
			$data[$this->prefix . 'galleria_status'] = $this->request->post[$this->prefix . 'galleria_status'];
		} else {
			$data[$this->prefix . 'galleria_status'] = $this->config->get($this->prefix . 'galleria_status');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_page_status'])) {
			$data[$this->prefix . 'galleria_page_status'] = $this->request->post[$this->prefix . 'galleria_page_status'];
		} elseif ($this->config->get($this->prefix . 'galleria_page_status')) {
			$data[$this->prefix . 'galleria_page_status'] = $this->config->get($this->prefix . 'galleria_page_status');
		} else {
			$data[$this->prefix . 'galleria_page_status'] = 1;
		}
		if (isset($this->request->post[$this->prefix . 'galleria_page_menu'])) {
			$data[$this->prefix . 'galleria_page_menu'] = $this->request->post[$this->prefix . 'galleria_page_menu'];
		} else {
			$data[$this->prefix . 'galleria_page_menu'] = $this->config->get($this->prefix . 'galleria_page_menu');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_page_menu_title'])) {
			$data[$this->prefix . 'galleria_page_menu_title'] = $this->request->post[$this->prefix . 'galleria_page_menu_title'];
		} else {
			$data[$this->prefix . 'galleria_page_menu_title'] = $this->config->get($this->prefix . 'galleria_page_menu_title');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_page_title'])) {
			$data[$this->prefix . 'galleria_page_title'] = $this->request->post[$this->prefix . 'galleria_page_title'];
		} else {
			$data[$this->prefix . 'galleria_page_title'] = $this->config->get($this->prefix . 'galleria_page_title');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_page_description'])) {
			$data[$this->prefix . 'galleria_page_description'] = $this->request->post[$this->prefix . 'galleria_page_description'];
		} else {
			$data[$this->prefix . 'galleria_page_description'] = $this->config->get($this->prefix . 'galleria_page_description');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_page_meta_title'])) {
			$data[$this->prefix . 'galleria_page_meta_title'] = $this->request->post[$this->prefix . 'galleria_page_meta_title'];
		} else {
			$data[$this->prefix . 'galleria_page_meta_title'] = $this->config->get($this->prefix . 'galleria_page_meta_title');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_page_meta_description'])) {
			$data[$this->prefix . 'galleria_page_meta_description'] = $this->request->post[$this->prefix . 'galleria_page_meta_description'];
		} else {
			$data[$this->prefix . 'galleria_page_meta_description'] = $this->config->get($this->prefix . 'galleria_page_meta_description');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_page_meta_keyword'])) {
			$data[$this->prefix . 'galleria_page_meta_keyword'] = $this->request->post[$this->prefix . 'galleria_page_meta_keyword'];
		} else {
			$data[$this->prefix . 'galleria_page_meta_keyword'] = $this->config->get($this->prefix . 'galleria_page_meta_keyword');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_page_view'])) {
			$data[$this->prefix . 'galleria_page_view'] = $this->request->post[$this->prefix . 'galleria_page_view'];
		} else {
			$data[$this->prefix . 'galleria_page_view'] = $this->config->get($this->prefix . 'galleria_page_view');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_page_grid'])) {
			$data[$this->prefix . 'galleria_page_grid'] = $this->request->post[$this->prefix . 'galleria_page_grid'];
		} elseif ($this->config->get($this->prefix . 'galleria_page_grid')) {
			$data[$this->prefix . 'galleria_page_grid'] = $this->config->get($this->prefix . 'galleria_page_grid');
		} else {
			$data[$this->prefix . 'galleria_page_grid'] = 4;
		}
		if (isset($this->request->post[$this->prefix . 'galleria_page_limit'])) {
			$data[$this->prefix . 'galleria_page_limit'] = $this->request->post[$this->prefix . 'galleria_page_limit'];
		} elseif ($this->config->get($this->prefix . 'galleria_page_limit')) {
			$data[$this->prefix . 'galleria_page_limit'] = $this->config->get($this->prefix . 'galleria_page_limit');
		} else {
			$data[$this->prefix . 'galleria_page_limit'] = 12;
		}
		if (isset($this->request->post[$this->prefix . 'galleria_page_album_title'])) {
			$data[$this->prefix . 'galleria_page_album_title'] = $this->request->post[$this->prefix . 'galleria_page_album_title'];
		} else {
			$data[$this->prefix . 'galleria_page_album_title'] = $this->config->get($this->prefix . 'galleria_page_album_title');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_page_album_description'])) {
			$data[$this->prefix . 'galleria_page_album_description'] = $this->request->post[$this->prefix . 'galleria_page_album_description'];
		} else {
			$data[$this->prefix . 'galleria_page_album_description'] = $this->config->get($this->prefix . 'galleria_page_album_description');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_page_album_text'])) {
			$data[$this->prefix . 'galleria_page_album_text'] = $this->request->post[$this->prefix . 'galleria_page_album_text'];
		} else {
			$data[$this->prefix . 'galleria_page_album_text'] = $this->config->get($this->prefix . 'galleria_page_album_text');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_album_view'])) {
			$data[$this->prefix . 'galleria_album_view'] = $this->request->post[$this->prefix . 'galleria_album_view'];
		} else {
			$data[$this->prefix . 'galleria_album_view'] = $this->config->get($this->prefix . 'galleria_album_view');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_album_title'])) {
			$data[$this->prefix . 'galleria_album_title'] = $this->request->post[$this->prefix . 'galleria_album_title'];
		} else {
			$data[$this->prefix . 'galleria_album_title'] = $this->config->get($this->prefix . 'galleria_album_title');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_album_description'])) {
			$data[$this->prefix . 'galleria_album_description'] = $this->request->post[$this->prefix . 'galleria_album_description'];
		} else {
			$data[$this->prefix . 'galleria_album_description'] = $this->config->get($this->prefix . 'galleria_album_description');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_album_image_title'])) {
			$data[$this->prefix . 'galleria_album_image_title'] = $this->request->post[$this->prefix . 'galleria_album_image_title'];
		} else {
			$data[$this->prefix . 'galleria_album_image_title'] = $this->config->get($this->prefix . 'galleria_album_image_title');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_album_image_description'])) {
			$data[$this->prefix . 'galleria_album_image_description'] = $this->request->post[$this->prefix . 'galleria_album_image_description'];
		} else {
			$data[$this->prefix . 'galleria_album_image_description'] = $this->config->get($this->prefix . 'galleria_album_image_description');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_image_description_status'])) {
			$data[$this->prefix . 'galleria_image_description_status'] = $this->request->post[$this->prefix . 'galleria_image_description_status'];
		} else {
			$data[$this->prefix . 'galleria_image_description_status'] = $this->config->get($this->prefix . 'galleria_image_description_status');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_page_css'])) {
			$data[$this->prefix . 'galleria_page_css'] = $this->request->post[$this->prefix . 'galleria_page_css'];
		} else {
			$data[$this->prefix . 'galleria_page_css'] = $this->config->get($this->prefix . 'galleria_page_css');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_image_grid'])) {
			$data[$this->prefix . 'galleria_image_grid'] = $this->request->post[$this->prefix . 'galleria_image_grid'];
		} elseif ($this->config->get($this->prefix . 'galleria_image_grid')) {
			$data[$this->prefix . 'galleria_image_grid'] = $this->config->get($this->prefix . 'galleria_image_grid');
		} else {
		   $data[$this->prefix . 'galleria_image_grid'] = 4 ;
		}
		if (isset($this->request->post[$this->prefix . 'galleria_album_css'])) {
			$data[$this->prefix . 'galleria_album_css'] = $this->request->post[$this->prefix . 'galleria_album_css'];
		} else {
			$data[$this->prefix . 'galleria_album_css'] = $this->config->get($this->prefix . 'galleria_album_css');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_image_title_status'])) {
			$data[$this->prefix . 'galleria_image_title_status'] = $this->request->post[$this->prefix . 'galleria_image_title_status'];
		} else {
			$data[$this->prefix . 'galleria_image_title_status'] = $this->config->get($this->prefix . 'galleria_image_title_status');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_album_width'])) {
			$data[$this->prefix . 'galleria_album_width'] = $this->request->post[$this->prefix . 'galleria_album_width'];
		} elseif ($this->config->get($this->prefix . 'galleria_album_width')) {
			$data[$this->prefix . 'galleria_album_width'] = $this->config->get($this->prefix . 'galleria_album_width');
		} else {
			$data[$this->prefix . 'galleria_album_width'] = 400;
		}
		if (isset($this->request->post[$this->prefix . 'galleria_album_height'])) {
			$data[$this->prefix . 'galleria_album_height'] = $this->request->post[$this->prefix . 'galleria_album_height'];
		} elseif ($this->config->get($this->prefix . 'galleria_album_height')) {
			$data[$this->prefix . 'galleria_album_height'] = $this->config->get($this->prefix . 'galleria_album_height');
		} else {
			$data[$this->prefix . 'galleria_album_height'] = 300;
		}
		if (isset($this->request->post[$this->prefix . 'galleria_image_width'])) {
			$data[$this->prefix . 'galleria_image_width'] = $this->request->post[$this->prefix . 'galleria_image_width'];
		} elseif ($this->config->get($this->prefix . 'galleria_image_width')) {
			$data[$this->prefix . 'galleria_image_width'] = $this->config->get($this->prefix . 'galleria_image_width');
		} else {
			$data[$this->prefix . 'galleria_image_width'] = 400;
		}
		if (isset($this->request->post[$this->prefix . 'galleria_image_height'])) {
			$data[$this->prefix . 'galleria_image_height'] = $this->request->post[$this->prefix . 'galleria_image_height'];
		} elseif ($this->config->get($this->prefix . 'galleria_image_height')) {
			$data[$this->prefix . 'galleria_image_height'] = $this->config->get($this->prefix . 'galleria_image_height');
		} else {
			$data[$this->prefix . 'galleria_image_height'] = 300;
		}
		if (isset($this->request->post[$this->prefix . 'galleria_album_animation'])) {
			$data[$this->prefix . 'galleria_album_animation'] = $this->request->post[$this->prefix . 'galleria_album_animation'];
		} else {
			$data[$this->prefix . 'galleria_album_animation'] = $this->config->get($this->prefix . 'galleria_album_animation');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_image_animation'])) {
			$data[$this->prefix . 'galleria_image_animation'] = $this->request->post[$this->prefix . 'galleria_image_animation'];
		} else {
			$data[$this->prefix . 'galleria_image_animation'] = $this->config->get($this->prefix . 'galleria_image_animation');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_sitemap'])) {
			$data[$this->prefix . 'galleria_sitemap'] = $this->request->post[$this->prefix . 'galleria_sitemap'];
		} else {
			$data[$this->prefix . 'galleria_sitemap'] = $this->config->get($this->prefix . 'galleria_sitemap');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_widget_view'])) {
			$data[$this->prefix . 'galleria_widget_view'] = $this->request->post[$this->prefix . 'galleria_widget_view'];
		} else {
			$data[$this->prefix . 'galleria_widget_view'] = $this->config->get($this->prefix . 'galleria_widget_view');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_widget_title'])) {
			$data[$this->prefix . 'galleria_widget_title'] = $this->request->post[$this->prefix . 'galleria_widget_title'];
		} else {
			$data[$this->prefix . 'galleria_widget_title'] = $this->config->get($this->prefix . 'galleria_widget_title');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_widget_description'])) {
			$data[$this->prefix . 'galleria_widget_description'] = $this->request->post[$this->prefix . 'galleria_widget_description'];
		} else {
			$data[$this->prefix . 'galleria_widget_description'] = $this->config->get($this->prefix . 'galleria_widget_description');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_widget_image_title'])) {
			$data[$this->prefix . 'galleria_widget_image_title'] = $this->request->post[$this->prefix . 'galleria_widget_image_title'];
		} else {
			$data[$this->prefix . 'galleria_widget_image_title'] = $this->config->get($this->prefix . 'galleria_widget_image_title');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_widget_image_description'])) {
			$data[$this->prefix . 'galleria_widget_image_description'] = $this->request->post[$this->prefix . 'galleria_widget_image_description'];
		} else {
			$data[$this->prefix . 'galleria_widget_image_description'] = $this->config->get($this->prefix . 'galleria_widget_image_description');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_widget_grid'])) {
			$data[$this->prefix . 'galleria_widget_grid'] = $this->request->post[$this->prefix . 'galleria_widget_grid'];
		} elseif ($this->config->get($this->prefix . 'galleria_widget_grid')) {
			$data[$this->prefix . 'galleria_widget_grid'] = $this->config->get($this->prefix . 'galleria_widget_grid');
		} else {
		   $data[$this->prefix . 'galleria_widget_grid'] = 4 ;
		}
		if (isset($this->request->post[$this->prefix . 'galleria_widget_animation'])) {
			$data[$this->prefix . 'galleria_widget_animation'] = $this->request->post[$this->prefix . 'galleria_widget_animation'];
		} else {
			$data[$this->prefix . 'galleria_widget_animation'] = $this->config->get($this->prefix . 'galleria_widget_animation');
		}
		if (isset($this->request->post[$this->prefix . 'galleria_widget_width'])) {
			$data[$this->prefix . 'galleria_widget_width'] = $this->request->post[$this->prefix . 'galleria_widget_width'];
		} elseif ($this->config->get($this->prefix . 'galleria_widget_width')) {
			$data[$this->prefix . 'galleria_widget_width'] = $this->config->get($this->prefix . 'galleria_widget_width');
		} else {
			$data[$this->prefix . 'galleria_widget_width'] = 400;
		}
		if (isset($this->request->post[$this->prefix . 'galleria_widget_height'])) {
			$data[$this->prefix . 'galleria_widget_height'] = $this->request->post[$this->prefix . 'galleria_widget_height'];
		} elseif ($this->config->get($this->prefix . 'galleria_widget_height')) {
			$data[$this->prefix . 'galleria_widget_height'] = $this->config->get($this->prefix . 'galleria_widget_height');
		} else {
			$data[$this->prefix . 'galleria_widget_height'] = 300;
		}
		if (isset($this->request->post[$this->prefix . 'galleria_widget_css'])) {
			$data[$this->prefix . 'galleria_widget_css'] = $this->request->post[$this->prefix . 'galleria_widget_css'];
		} else {
			$data[$this->prefix . 'galleria_widget_css'] = $this->config->get($this->prefix . 'galleria_widget_css');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/galleria/setting', $data));
	}

	public function add() {
		$this->load->language('extension/module/galleria');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/galleria');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_module_galleria->addGallery($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('extension/module/galleria/galleria_list', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('extension/module/galleria');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/galleria');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_module_galleria->editGallery($this->request->get['galleria_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('extension/module/galleria/galleria_list', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('extension/module/galleria');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/galleria');

		if (isset($this->request->post['selected'])&&$this->user->hasPermission('modify', 'extension/module/galleria')) {
			foreach ($this->request->post['selected'] as $galleria_id) {
				$this->model_extension_module_galleria->deleteGallery($galleria_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('extension/module/galleria/galleria_list', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	public function galleria_list () {
		$this->load->language('extension/module/galleria');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/module/galleria'); 
		$this->getList();
	}

	protected function getList() {
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

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
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
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/galleria/galleria_list', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('extension/module/galleria/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/module/galleria/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['galleria'] = array();

		$filter_data = array(
			'filter_name'     => $filter_name,
			'filter_status'   => $filter_status,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$galleria_total = $this->model_extension_module_galleria->getTotalGalleries($filter_data);

		$results = $this->model_extension_module_galleria->getGalleries($filter_data);

		foreach ($results as $result) {
			$data['galleria'][] = array(
				'galleria_id'  => $result['galleria_id'],
				'name'        => $result['name'],
				'view'        => HTTPS_CATALOG.'index.php?route=extension/module/galleria/info'.'&galleria_id=' . $result['galleria_id'],
				'edit'        => $this->url->link('extension/module/galleria/edit', 'user_token=' . $this->session->data['user_token'] . '&galleria_id=' . $result['galleria_id'] . $url, true),
				'delete'      => $this->url->link('extension/module/galleria/delete', 'user_token=' . $this->session->data['user_token'] . '&galleria_id=' . $result['galleria_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

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

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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

		$data['sort_name'] = $this->url->link('extension/module/galleria/galleria_list', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);

		$url = '';
		
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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
		$pagination->total = $galleria_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/module/galleria/galleria_list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($galleria_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($galleria_total - $this->config->get('config_limit_admin'))) ? $galleria_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $galleria_total, ceil($galleria_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_status'] = $filter_status;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/galleria/galleria_list', $data));
	}

	protected function getForm() {
		$this->document->addStyle('view/javascript/summernote/summernote.css');
		$this->document->addScript('view/javascript/summernote/summernote.js');
		$this->document->addScript('view/javascript/summernote/opencart.js');
		$this->document->addScript('view/javascript/jquery/jquery-ui/jquery-ui.min.js');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
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
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/galleria/galleria_list', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['galleria_id'])) {
			$data['action'] = $this->url->link('extension/module/galleria/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/module/galleria/edit', 'user_token=' . $this->session->data['user_token'] . '&galleria_id=' . $this->request->get['galleria_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('extension/module/galleria/galleria_list', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['galleria_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$galleria_info = $this->model_extension_module_galleria->getGallery($this->request->get['galleria_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['galleria_description'])) {
			$data['galleria_description'] = $this->request->post['galleria_description'];
		} elseif (isset($this->request->get['galleria_id'])) {
			$data['galleria_description'] = $this->model_extension_module_galleria->getGalleryDescriptions($this->request->get['galleria_id']);
		} else {
			$data['galleria_description'] = array();
		}

		$data['heading_title'] = !isset($this->request->get['galleria_id']) ? $this->language->get('text_add_page') : $data['galleria_description'][$this->config->get('config_language_id')]['name'];
		$data['text_form'] = !isset($this->request->get['galleria_id']) ? $this->language->get('text_add_page') : $this->language->get('text_edit_page');

		$this->load->model('setting/store');

		$data['stores'] = array();
		
		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);
		
		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}

		if (isset($this->request->post['galleria_store'])) {
			$data['galleria_store'] = $this->request->post['galleria_store'];
		} elseif (isset($this->request->get['galleria_id'])) {
			$data['galleria_store'] = $this->model_extension_module_galleria->getGalleryStores($this->request->get['galleria_id']);
		} else {
			$data['galleria_store'] = array(0);
		}

		if (isset($this->request->post['galleria_seo_url'])) {
			$data['galleria_seo_url'] = $this->request->post['galleria_seo_url'];
		} elseif (isset($this->request->get['galleria_id'])) {
			$data['galleria_seo_url'] = $this->model_extension_module_galleria->getGallerySeoUrls($this->request->get['galleria_id']);
		} else {
			$data['galleria_seo_url'] = array();
		}

		if (isset($this->request->post['album_images'])) {
			$album_images = $this->request->post['album_images'];
		} elseif (isset($this->request->get['galleria_id'])) {
			$album_images = $this->model_extension_module_galleria->getAlbumImages($this->request->get['galleria_id']);
		} else {
			$album_images = array();
		}

		$data['album_images'] = array();

		$this->load->model('tool/image');

		foreach ($album_images as $album_image) {
			if (is_file(DIR_IMAGE . $album_image['image'])) {
				$image = $album_image['image'];
				$thumb = $album_image['image'];
			} else {
				$image = '';
				$thumb = 'no_image.png';
			}

			$data['album_images'][] = array(
				'image'       => $image,
				'thumb'       => $this->model_tool_image->resize($thumb, 100, 100),
				'description' => $album_image['description'],
				'sort_order'  => $album_image['sort_order']
			);
		}

		if (isset($this->request->post['inpage'])) {
			$data['inpage'] = $this->request->post['inpage'];
		} elseif (!empty($galleria_info)) {
			$data['inpage'] = $galleria_info['inpage'];
		} else {
			$data['inpage'] = 1;
		}
		
                if (isset($this->request->post['date_published'])) {
                        $data['date_published'] = $this->request->post['date_published'];
                } elseif (!empty($galleria_info)) {
                        $data['date_published'] = $galleria_info['date_published'];
                } else {
                        $data['date_published'] = null;
                }

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($galleria_info)) {
			$data['status'] = $galleria_info['status'];
		} else {
			$data['status'] = true;
		}

		$data['image_title_status'] = $this->config->get('module_galleria_image_title_status');
		$data['image_description_status'] = $this->config->get('module_galleria_image_description_status');

		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('catalog/manufacturer');
		$this->load->model('catalog/information');

		// Products
		$data['products'] = array();

		if (isset($this->request->post['product'])) {
			$products = $this->request->post['product'];
		} elseif (isset($this->request->get['galleria_id'])) {
			$products = $this->model_extension_module_galleria->getGalleryProducts($this->request->get['galleria_id']);
		} else {
			$products = array();
		}

		if ($products) {
			foreach ($products as $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);

				if ($product_info) {
					$data['products'][] = array(
						'product_id' => $product_info['product_id'],
						'name'       => $product_info['name']
					);
				}
			}
		}
		
		// Categories
		$data['categories'] = array();

		if (isset($this->request->post['category'])) {
			$categories = $this->request->post['category'];
		} elseif (isset($this->request->get['galleria_id'])) {
			$categories = $this->model_extension_module_galleria->getGalleryCategories($this->request->get['galleria_id']);
		} else {
			$categories = array();
		}

		if ($categories) {
			foreach ($categories as $category_id) {
				$category_info = $this->model_catalog_category->getCategory($category_id);

				if ($category_info) {
					$data['categories'][] = array(
						'category_id'    => $category_info['category_id'],
						'name'           => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
					);
				}
			}
		}

		// Manufacturers
		$data['manufacturers'] = array();

		if (isset($this->request->post['manufacturer'])) {
			$manufacturers = $this->request->post['manufacturer'];
		} elseif (isset($this->request->get['galleria_id'])) {
			$manufacturers = $this->model_extension_module_galleria->getGalleryManufacturers($this->request->get['galleria_id']);
		} else {
			$manufacturers = array();
		}

		if ($manufacturers) {
			foreach ($manufacturers as $manufacturer_id) {
				$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($manufacturer_id);

				if ($manufacturer_info) {
					$data['manufacturers'][] = array(
						'manufacturer_id'    => $manufacturer_info['manufacturer_id'],
						'name'               => $manufacturer_info['name']
					);
				}
			}
		}

		// Informations
		$data['informations'] = array();

		if (isset($this->request->post['information'])) {
			$informations = $this->request->post['information'];
		} elseif (isset($this->request->get['galleria_id'])) {
			$informations = $this->model_extension_module_galleria->getGalleryInformations($this->request->get['galleria_id']);
		} else {
			$informations = array();
		}

		if ($informations) {
			foreach ($informations as $information_id) {
				$information_info = $this->model_catalog_information->getInformationDescriptions($information_id);

				if ($information_info) {
					$data['informations'][] = array(
						'information_id' => $information_id,
						'name'       => $information_info[$this->config->get('config_language_id')]['title']
					);
				}
			}
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/galleria/form', $data));
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/module/galleria');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 10
			);

			$results = $this->model_extension_module_galleria->getGalleries($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'galleria_id' => $result['galleria_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function autocompleteInformation() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/module/galleria');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 10
			);

			$results = $this->model_extension_module_galleria->getInformations($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'information_id' => $result['information_id'],
					'name'        => strip_tags(html_entity_decode($result['title'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/galleria')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ($this->request->post['module_galleria_unique'] && $this->request->post['module_galleria_page_seo_url']) {
				$this->load->model('design/seo_url');
				foreach ($this->request->post['module_galleria_page_seo_url'] as $store_id => $language) {
					foreach ($language as $language_id => $seo_url_keyword) {
						if (!empty($seo_url_keyword)) {
							if (count(array_keys($language, $seo_url_keyword)) > 1) {
								$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
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

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/module/galleria')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ($this->config->get('module_galleria_unique') && $this->request->post['galleria_seo_url']) {
				$this->load->model('design/seo_url');
				
				foreach ($this->request->post['galleria_seo_url'] as $store_id => $language) {
					foreach ($language as $language_id => $seo_url_keyword) {
						if (!empty($seo_url_keyword)) {
							if (count(array_keys($language, $seo_url_keyword)) > 1) {
								$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
							}
							
							$seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($seo_url_keyword);
							foreach ($seo_urls as $seo_url) {
								if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['galleria_id']) || (($seo_url['query'] != 'galleria_id=' . $this->request->get['galleria_id'])))) {
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
}
