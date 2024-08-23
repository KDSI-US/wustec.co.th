<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionMpPhotoGalleryGallery extends Controller {
	use mpphotogallery\trait_mpphotogallery;
	private $error = [];

	public function __construct($registry) {
		parent :: __construct($registry);
		$this->igniteTraitMpPhotoGallery($registry);
	}

	public function index() {
		$this->load->language('mpphotogallery/gallery');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpphotogallery/gallery');
		$this->load->model('tool/image');

		$this->model_extension_mpphotogallery_gallery->CreateMPGalleryTable();

		$this->getList();
	}

	public function add() {
		$this->load->language('mpphotogallery/gallery');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpphotogallery/gallery');
		$this->load->model('tool/image');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_mpphotogallery_gallery->addGallery($this->request->post);

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

			$this->response->redirect($this->url->link('extension/mpphotogallery/gallery', $this->token.'=' . $this->session->data[$this->token] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('mpphotogallery/gallery');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpphotogallery/gallery');
		$this->load->model('tool/image');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_mpphotogallery_gallery->editGallery($this->request->get['gallery_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('extension/mpphotogallery/gallery', $this->token.'=' . $this->session->data[$this->token] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('mpphotogallery/gallery');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpphotogallery/gallery');
		$this->load->model('tool/image');
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $gallery_id) {
				$this->model_extension_mpphotogallery_gallery->deleteGallery($gallery_id);
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

			$this->response->redirect($this->url->link('extension/mpphotogallery/gallery', $this->token.'=' . $this->session->data[$this->token] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'gd.title';
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

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('extension/module/mpphotogallery_setting', $this->token.'=' . $this->session->data[$this->token], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/mpphotogallery/gallery', $this->token.'=' . $this->session->data[$this->token] . $url, true)
		);

		$data['get_token'] = $this->token;
		$data['token'] = $this->session->data[$this->token];

		$url_front = new Url(HTTP_CATALOG, HTTPS_CATALOG);

		$data['add'] = $this->url->link('extension/mpphotogallery/gallery/add', $this->token.'=' . $this->session->data[$this->token] . $url, true);
		$data['delete'] = $this->url->link('extension/mpphotogallery/gallery/delete', $this->token.'=' . $this->session->data[$this->token] . $url, true);

		$data['gallerys'] = [];

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$gallery_total = $this->model_extension_mpphotogallery_gallery->getTotalGallerys();

		$results = $this->model_extension_mpphotogallery_gallery->getGallerys($filter_data);

		foreach ($results as $result) {

			if ($result['image'] && file_exists(DIR_IMAGE . $result['image'])) {
				$thumb = $this->model_tool_image->resize($result['image'], 50, 50);
			} else {
				$thumb = $this->model_tool_image->resize('no_image.png', 50, 50);
			}

			$data['gallerys'][] = array(
				'gallery_id'  => $result['gallery_id'],
				'title'       => $result['title'],
				'status'       => $result['status'],
				'viewed'       => $result['viewed'],
				'status_str'       => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'thumb'       => $thumb,
				'sort_order' => $result['sort_order'],
				'edit'       => $this->url->link('extension/mpphotogallery/gallery/edit', $this->token.'=' . $this->session->data[$this->token] . '&gallery_id=' . $result['gallery_id'] . $url, true),
				'view'       => $url_front->link('gallery/photo', 'gallery_id=' . $result['gallery_id'], true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_image'] = $this->language->get('column_image');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_viewed'] = $this->language->get('column_viewed');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_view'] = $this->language->get('button_view');
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

		$data['sort_name'] = $this->url->link('extension/mpphotogallery/gallery', $this->token.'=' . $this->session->data[$this->token] . '&sort=gd.title' . $url, true);
		$data['sort_viewed'] = $this->url->link('extension/mpphotogallery/gallery', $this->token.'=' . $this->session->data[$this->token] . '&sort=g.viewed' . $url, true);
		$data['sort_sort_order'] = $this->url->link('extension/mpphotogallery/gallery', $this->token.'=' . $this->session->data[$this->token] . '&sort=g.sort_order' . $url, true);
		$data['sort_status'] = $this->url->link('extension/mpphotogallery/gallery', $this->token.'=' . $this->session->data[$this->token] . '&sort=g.status' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $gallery_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/mpphotogallery/gallery', $this->token.'=' . $this->session->data[$this->token] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($gallery_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($gallery_total - $this->config->get('config_limit_admin'))) ? $gallery_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $gallery_total, ceil($gallery_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['mtabs'] = $this->load->controller('extension/mpphotogallery/mtabs');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->viewLoad('extension/mpphotogallery/gallery_list', $data));

	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['gallery_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_enabled'] = $this->language->get('text_enabled');

		$data['entry_title'] = $this->language->get('entry_title');
		// 07-05-2022: updation task start
		$data['entry_store'] = $this->language->get('entry_store');
		// 07-05-2022: updation task end
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_top_description'] = $this->language->get('entry_top_description');
		$data['entry_bottom_description'] = $this->language->get('entry_bottom_description');
		$data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
		$data['entry_meta_description'] = $this->language->get('entry_meta_description');
		$data['entry_meta_title'] = $this->language->get('entry_meta_title');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_photo'] = $this->language->get('entry_photo');
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_keyword'] = $this->language->get('entry_keyword');
		$data['entry_photo_image'] = $this->language->get('entry_photo_image');
		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_height'] = $this->language->get('entry_height');
		$data['entry_highlight'] = $this->language->get('entry_highlight');
		$data['entry_link'] = $this->language->get('entry_link');
		$data['entry_video_thumb_size'] = $this->language->get('entry_video_thumb_size');
		// gallery for product task starts
		$data['entry_product'] = $this->language->get('entry_product');
		// 07-05-2022: updation task start
		$data['column_product_image'] = $this->language->get('column_product_image');
		$data['column_product_name'] = $this->language->get('column_product_name');
		$data['column_gallery_video'] = $this->language->get('column_gallery_video');
		$data['column_gallery_image'] = $this->language->get('column_gallery_image');
		$data['column_remove'] = $this->language->get('column_remove');
		// 07-05-2022: updation task end
		// gallery for product task ends
		$data['entry_video_thumb'] = $this->language->get('entry_video_thumb');

		$data['text_highlight'] = $this->language->get('text_highlight');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');

		$data['help_keyword'] = $this->language->get('help_keyword');
		$data['help_photo_image'] = $this->language->get('help_photo_image');
		$data['help_video_thumb_image'] = $this->language->get('help_video_thumb_image');
		// gallery for product task starts
		$data['help_product'] = $this->language->get('help_product');
		// gallery for product task ends

		$data['tab_albumsetting'] = $this->language->get('tab_albumsetting');
		$data['tab_languagesetting'] = $this->language->get('tab_languagesetting');
		$data['tab_photo'] = $this->language->get('tab_photo');
		$data['tab_video'] = $this->language->get('tab_video');
		// gallery for product task starts
		$data['tab_links'] = $this->language->get('tab_links');
		// gallery for product task ends
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_photo_add'] = $this->language->get('button_photo_add');
		$data['button_remove'] = $this->language->get('button_remove');
		$data['button_video_add'] = $this->language->get('button_video_add');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = [];
		}

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		} else {
			$data['error_title'] = [];
		}

		if (isset($this->error['gallery_photo'])) {
			$data['error_gallery_photo'] = $this->error['gallery_photo'];
		} else {
			$data['error_gallery_photo'] = [];
		}

		if (isset($this->error['gallery_video'])) {
			$data['error_gallery_video'] = $this->error['gallery_video'];
		} else {
			$data['error_gallery_video'] = [];
		}

		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
		}
		// 07-05-2022: updation task start
		if (VERSION >= '3.0.0.0') {
			if (isset($this->error['keyword'])) {
				$data['error_keyword'] = $this->error['keyword'];
			} else {
				$data['error_keyword'] = [];
			}
		}
		// 07-05-2022: updation task end

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

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('extension/module/mpphotogallery_setting', $this->token.'=' . $this->session->data[$this->token], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/mpphotogallery/gallery', $this->token.'=' . $this->session->data[$this->token] . $url, true)
		);

		$data['get_token'] = $this->token;
		$data['token'] = $this->session->data[$this->token];

		if (!isset($this->request->get['gallery_id'])) {
			$data['action'] = $this->url->link('extension/mpphotogallery/gallery/add', $this->token.'=' . $this->session->data[$this->token] . $url, true);
			$data['gallery_id'] = '';
		} else {
			$data['action'] = $this->url->link('extension/mpphotogallery/gallery/edit', $this->token.'=' . $this->session->data[$this->token] . '&gallery_id=' . $this->request->get['gallery_id'] . $url, true);
			$data['gallery_id'] = $this->request->get['gallery_id'];
		}

		$data['cancel'] = $this->url->link('extension/mpphotogallery/gallery', $this->token.'=' . $this->session->data[$this->token] . $url, true);

		// 07-05-2022: updation task start
		$data['summernote'] = '';
		// 07-05-2022: updation task end

		if (isset($this->request->get['gallery_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$gallery_info = $this->model_extension_mpphotogallery_gallery->getGallery($this->request->get['gallery_id']);
		}

		$data['languages'] = $this->getLanguages();

		if (isset($this->request->post['gallery_description'])) {
			$data['gallery_description'] = $this->request->post['gallery_description'];
		} elseif (isset($this->request->get['gallery_id'])) {
			$data['gallery_description'] = $this->model_extension_mpphotogallery_gallery->getGalleryDescriptions($this->request->get['gallery_id']);
		} else {
			$data['gallery_description'] = [];
		}

		if (isset($this->request->post['title'])) {
			$data['title'] = $this->request->post['title'];
		} elseif (!empty($gallery_info)) {
			$data['title'] = $gallery_info['title'];
		} else {
			$data['title'] = '';
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($gallery_info)) {
			$data['sort_order'] = $gallery_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($gallery_info)) {
			$data['status'] = $gallery_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($gallery_info)) {
			$data['width'] = $gallery_info['width'];
		} else {
			$data['width'] = 200;
		}

		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($gallery_info)) {
			$data['height'] = $gallery_info['height'];
		} else {
			$data['height'] = 200;
		}
		// 07-05-2022: updation task start
		if (VERSION >= '3.0.0.0') {
			if (isset($this->request->post['gallery_seo_url'])) {
				$data['gallery_seo_url'] = $this->request->post['gallery_seo_url'];
			} elseif (isset($this->request->get['gallery_id'])) {
				$data['gallery_seo_url'] = $this->model_extension_mpphotogallery_gallery->getGallerySeoUrls($this->request->get['gallery_id']);
			} else {
				$data['gallery_seo_url'] = [];
			}

		} else {
			if (isset($this->request->post['keyword'])) {
				$data['keyword'] = $this->request->post['keyword'];
			} elseif (!empty($gallery_info)) {
				$data['keyword'] = $gallery_info['keyword'];
			} else {
				$data['keyword'] = '';
			}

		}
		// 07-05-2022: updation task end

		if (isset($this->request->post['description'])) {
			$data['description'] = $this->request->post['description'];
		} elseif (!empty($gallery_info)) {
			$data['description'] = $gallery_info['description'];
		} else {
			$data['description'] = '';
		}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($gallery_info)) {
			$data['image'] = $gallery_info['image'];
		} else {
			$data['image'] = '';
		}

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($gallery_info) && is_file(DIR_IMAGE . $gallery_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($gallery_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		// Photos
		if (isset($this->request->post['gallery_photo'])) {
			$gallery_photos = $this->request->post['gallery_photo'];
		} elseif (isset($this->request->get['gallery_id'])) {
			$gallery_photos = $this->model_extension_mpphotogallery_gallery->getGalleryPhotos($this->request->get['gallery_id']);

		} else {
			$gallery_photos = [];
		}

		$data['gallery_photos'] = [];

		foreach ($gallery_photos as $gallery_photo) {
			if (is_file(DIR_IMAGE . $gallery_photo['photo'])) {
				$photo = $gallery_photo['photo'];
				$thumb = $gallery_photo['photo'];
			} else {
				$photo = '';
				$thumb = 'no_image.png';
			}


			$data['gallery_photos'][] = array(
				'gallery_photo_id'    => $gallery_photo['gallery_photo_id'],
				'photo'     					=> $photo,
				'thumb'     					=> $this->model_tool_image->resize($thumb, 100, 100),
				'highlight' 					=> isset($gallery_photo['highlight']) ? $gallery_photo['highlight'] : '',
				'link' 							=> $gallery_photo['link'],
				'sort_order' 					=> $gallery_photo['sort_order'],
				'gallery_photo_description'     => $gallery_photo['gallery_photo_description'],
			);
		}

		$this->load->model('setting/store');

		$data['stores'] = [];

		$data['stores'][] = array(
			'store_id' => '0',
			'name'     => $this->language->get('text_default')
		);

		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}

		// Videos
		if (isset($this->request->post['gallery_video'])) {
			$gallery_videos = $this->request->post['gallery_video'];
		} elseif (isset($this->request->get['gallery_id'])) {
			$gallery_videos = $this->model_extension_mpphotogallery_gallery->getGalleryVideos($this->request->get['gallery_id']);

		} else {
			$gallery_videos = [];
		}

		$data['gallery_videos'] = [];

		foreach ($gallery_videos as $gallery_video) {
			if (is_file(DIR_IMAGE . $gallery_video['video_thumb'])) {
				$video_thumb = $gallery_video['video_thumb'];
				$thumb = $gallery_video['video_thumb'];
			} else {
				$video_thumb = '';
				$thumb = 'no_image.png';
			}


			$data['gallery_videos'][] = array(
				'gallery_video_id'   			=> $gallery_video['gallery_video_id'],
				'video_thumb'     				=> $video_thumb,
				'thumb'     					=> $this->model_tool_image->resize($thumb, 100, 100),
				'link' 							=> $gallery_video['link'],
				'highlight' 					=> isset($gallery_video['highlight']) ? $gallery_video['highlight'] : '',
				'sort_order' 					=> $gallery_video['sort_order'],
				'gallery_video_description'     => $gallery_video['gallery_video_description'],
			);
		}


		if (isset($this->request->post['video_width'])) {
			$data['video_width'] = $this->request->post['video_width'];
		} elseif (!empty($gallery_info)) {
			$data['video_width'] = $gallery_info['video_width'];
		} else {
			$data['video_width'] = 200;
		}

		if (isset($this->request->post['video_height'])) {
			$data['video_height'] = $this->request->post['video_height'];
		} elseif (!empty($gallery_info)) {
			$data['video_height'] = $gallery_info['video_height'];
		} else {
			$data['video_height'] = 200;
		}

		// gallery for product task starts
		if (isset($this->request->post['gallery_products'])) {
			$gallery_products = $this->request->post['gallery_products'];
		} elseif (isset($this->request->get['gallery_id'])) {
			$gallery_products = $this->model_extension_mpphotogallery_gallery->getGalleryProducts($this->request->get['gallery_id']);
		} else {
			$gallery_products = [];
		}

		$data['gallery_products'] = [];

		$this->load->model('catalog/product');
		// 07-05-2022: updation task start
		foreach ($gallery_products as $gallery_product) {
			$product_info = $this->model_catalog_product->getProduct($gallery_product['product_id']);

			if ($product_info) {
				if ($product_info['image'] && file_exists(DIR_IMAGE . $product_info['image'])) {
					$thumb = $this->model_tool_image->resize($product_info['image'], 65, 65);
				} else {
					$thumb = $this->model_tool_image->resize('no_image.png', 65, 65);
				}
				$data['gallery_products'][] = array(
					'product_id' => $product_info['product_id'],
					'name'       => $product_info['name'],
					'thumb'       => $thumb,
					'video'       => $gallery_product['video'],
					'image'       => $gallery_product['image'],
				);
			}
		}
		// 07-05-2022: updation task end

		// gallery for product task ends

		$data['mtabs'] = $this->load->controller('extension/mpphotogallery/mtabs');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->viewLoad('extension/mpphotogallery/gallery_form', $data));

	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/mpphotogallery/gallery')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		foreach ($this->request->post['gallery_description'] as $language_id => $value) {
			if ((utf8_strlen($value['title']) < 2) || (utf8_strlen($value['title']) > 255)) {
				$this->error['title'][$language_id] = $this->language->get('error_title');
			}

			if ((utf8_strlen($value['meta_title']) < 3) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
		}

		if (isset($this->request->post['gallery_photo'])) {
			foreach ($this->request->post['gallery_photo'] as $gallery_photo_id => $gallery_photo) {
				foreach ($gallery_photo['gallery_photo_description'] as $language_id => $gallery_photo_description) {
					if ((utf8_strlen($gallery_photo_description['name']) < 1) || (utf8_strlen($gallery_photo_description['name']) > 128)) {
						$this->error['gallery_photo'][$gallery_photo_id][$language_id] = $this->language->get('error_gallery_photo');
					}
				}
			}
		}

		if (isset($this->request->post['gallery_video'])) {
			foreach ($this->request->post['gallery_video'] as $gallery_video_id => $gallery_video) {
				foreach ($gallery_video['gallery_video_description'] as $language_id => $gallery_video_description) {
					if ((utf8_strlen($gallery_video_description['name']) < 1) || (utf8_strlen($gallery_video_description['name']) > 128)) {
						$this->error['gallery_video'][$gallery_video_id][$language_id] = $this->language->get('error_gallery_video');
					}
				}
			}
		}

		// 07-05-2022: updation task start
		if (VERSION >= '3.0.0.0') {
			if ($this->request->post['gallery_seo_url']) {
				$this->load->model('design/seo_url');

				foreach ($this->request->post['gallery_seo_url'] as $store_id => $language) {
					foreach ($language as $language_id => $keyword) {
						if (!empty($keyword)) {
							if (count(array_keys($language, $keyword)) > 1) {
								$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
							}

							$seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);

							foreach ($seo_urls as $seo_url) {
								if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['gallery_id']) || (($seo_url['query'] != 'gallery_id=' . $this->request->get['gallery_id'])))) {
									$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_keyword');

									break;
								}
							}
						}
					}
				}
			}
		} else {

			if (utf8_strlen($this->request->post['keyword']) > 0) {
				$this->load->model('catalog/url_alias');

				$url_alias_info = $this->model_catalog_url_alias->getUrlAlias($this->request->post['keyword']);

				if ($url_alias_info && isset($this->request->get['gallery_id']) && $url_alias_info['query'] != 'gallery_id=' . $this->request->get['gallery_id']) {
					$this->error['keyword'] = sprintf($this->language->get('error_keyword'));
				}

				if ($url_alias_info && !isset($this->request->get['gallery_id'])) {
					$this->error['keyword'] = sprintf($this->language->get('error_keyword'));
				}
			}
		}
		// 07-05-2022: updation task end


		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/mpphotogallery/gallery')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = [];

		if (isset($this->request->get['filter_name'])) {
			$this->load->language('mpphotogallery/gallery');

			$this->load->model('extension/mpphotogallery/gallery');
			$this->load->model('tool/image');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
			);

			$gallerys = $this->model_extension_mpphotogallery_gallery->getGallerys($filter_data);

			foreach ($gallerys as $gallery) {
				$gallery_photo_data = [];

				$gallery_photos = $this->model_extension_mpphotogallery_gallery->getGalleryPhotos($gallery['gallery_id']);

					foreach ($gallery_photos as $gallery_photo) {
						if (is_file(DIR_IMAGE . $gallery_photo['photo'])) {
							$photo = $this->model_tool_image->resize($gallery_photo['photo'], 50, 50);
						} else {
							$photo = $this->model_tool_image->resize('no_image.png', 50, 50);
						}

						$gallery_photo_data[] = array(
							'gallery_photo_id' => $gallery_photo['gallery_photo_id'],
							'photo'           => $photo
						);
					}

					$sort_order = [];

					foreach ($gallery_photo_data as $key => $value) {
						$sort_order[$key] = $value['photo'];
					}

					array_multisort($sort_order, SORT_ASC, $gallery_photo_data);

				$json[] = array(
					'gallery_id'    => $gallery['gallery_id'],
					'title'         => strip_tags(html_entity_decode($gallery['title'], ENT_QUOTES, 'UTF-8')),
					'gallery_photo' => $gallery_photo_data
				);
			}
		}

		$sort_order = [];

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['title'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	// gallery for product task starts
	// 07-05-2022: updation task start
	public function productAutocomplete() {
		$json = [];

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/product');
			$this->load->model('tool/image');

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

			$filter_data = array(
				'filter_name'  => $filter_name,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_catalog_product->getProducts($filter_data);

			foreach ($results as $result) {
				if ($result['image'] && file_exists(DIR_IMAGE . $result['image'])) {
					$thumb = $this->model_tool_image->resize($result['image'], 65, 65);
				} else {
					$thumb = $this->model_tool_image->resize('no_image.png', 65, 65);
				}

				$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'thumb'      => $thumb,
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	// 07-05-2022: updation task end
	// gallery for product task ends
}