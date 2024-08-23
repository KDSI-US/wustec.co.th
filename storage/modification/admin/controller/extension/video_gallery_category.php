<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionVideoGalleryCategory extends Controller
{
	private $error = array();

	public function index()
	{
		$this->language->load('extension/video_gallery_category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/video_gallery_category');

		$this->getList();
	}

	public function add()
	{
		$this->language->load('extension/video_gallery_category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/video_gallery_category');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_video_gallery_category->addVideoGalleryCategory($this->request->post);

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

			$this->response->redirect($this->url->link('extension/video_gallery_category', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit()
	{
		$this->language->load('extension/video_gallery_category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/video_gallery_category');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_video_gallery_category->editVideoGalleryCategory($this->request->get['video_gallery_category_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('extension/video_gallery_category', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->language->load('extension/video_gallery_category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/video_gallery_category');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $video_gallery_category_id) {
				$this->model_extension_video_gallery_category->deleteVideoGalleryCategory($video_gallery_category_id);
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

			$this->response->redirect($this->url->link('extension/video_gallery_category', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList()
	{
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'vd.title';
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/video_gallery_category', 'user_token=' . $this->session->data['user_token'] . $url, true),
			'separator' => ' :: '
		);

		$data['add'] = $this->url->link('extension/video_gallery_category/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/video_gallery_category/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['video_gallery_categories'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);

		$video_gallery_category_total = $this->model_extension_video_gallery_category->getTotalVideoGalleryCategories();

		$results = $this->model_extension_video_gallery_category->getVideoGalleryCategories($filter_data);

		if ($results) {
			foreach ($results as $result) {
				$action = array();
				$action[] = array(
					'text' => $this->language->get('text_edit'),
					'href' => $this->url->link('extension/video_gallery_category/update', 'user_token=' . $this->session->data['user_token'] . '&video_gallery_category_id=' . $result['video_gallery_category_id'] . $url, true)
				);
				$data['video_gallery_categories'][] = array(
					'video_gallery_category_id' => $result['video_gallery_category_id'],
					'title'            => $result['title'],
					'image'            => "/image/" . $result['image'],
					'description'      => strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')),
					'meta_keyword'     => strip_tags(html_entity_decode($result['meta_keyword'], ENT_QUOTES, 'UTF-8')),
					'meta_description' => strip_tags(html_entity_decode($result['meta_description'], ENT_QUOTES, 'UTF-8')),
					'sort_order'       => $result['sort_order'],
					'selected'         => isset($this->request->post['selected']) && in_array($result['video_gallery_category_id'], $this->request->post['selected']),
					'action'           => $action,
					'edit'             => $this->url->link('extension/video_gallery_category/edit', 'user_token=' . $this->session->data['user_token'] . '&video_gallery_category_id=' . $result['video_gallery_category_id'] . $url, true),
					'delete'           => $this->url->link('extension/video_gallery_category/delete', 'user_token=' . $this->session->data['user_token'] . '&video_gallery_category_id=' . $result['video_gallery_category_id'] . $url, true)
				);
			}
		}

		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_list'] = $this->language->get('text_list');
		$data['column_title'] = $this->language->get('column_title');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_action'] = $this->language->get('column_action');
		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['text_confirm'] = $this->language->get('text_confirm');

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

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_title'] = $this->url->link('extension/video_gallery_category', 'user_token=' . $this->session->data['user_token'] . '&sort=vd.title' . $url, true);
		$data['sort_sort_order'] = $this->url->link('extension/video_gallery_category', 'user_token=' . $this->session->data['user_token'] . '&sort=i.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $video_gallery_category_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->url = $this->url->link('extension/video_gallery_category', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($video_gallery_category_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($video_gallery_category_total - $this->config->get('config_limit_admin'))) ? $video_gallery_category_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $video_gallery_category_total, ceil($video_gallery_category_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/video_gallery_category_list', $data));
	}

	protected function getForm()
	{
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['video_gallery_category_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		$data['text_default'] = $this->language->get('text_default');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_browse'] = $this->language->get('text_browse');
		$data['text_clear'] = $this->language->get('text_clear');
		$data['text_image_manager'] = $this->language->get('text_image_manager');
		$data['help_keyword'] = $this->language->get('help_keyword');
		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_keyword'] = $this->language->get('entry_keyword');
		$data['entry_meta_description'] = $this->language->get('entry_meta_description');
		$data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_image'] = $this->language->get('entry_image');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_data'] = $this->language->get('tab_data');
		$data['tab_showhide'] = $this->language->get('tab_showhide');
		$data['entry_video_gallery_title'] = $this->language->get('entry_video_gallery_title');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['entry_video_gallery_desc'] = $this->language->get('entry_video_gallery_desc');
		$data['entry_video_gallery_img'] = $this->language->get('entry_video_gallery_img');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		} else {
			$data['error_title'] = array();
		}

		if (isset($this->error['description'])) {
			$data['error_description'] = $this->error['description'];
		} else {
			$data['error_description'] = array();
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/video_gallery_category', 'user_token=' . $this->session->data['user_token'] . $url, true),
			'separator' => ' :: '
		);

		if (!isset($this->request->get['video_gallery_category_id'])) {
			$data['action'] = $this->url->link('extension/video_gallery_category/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/video_gallery_category/edit', 'user_token=' . $this->session->data['user_token'] . '&video_gallery_category_id=' . $this->request->get['video_gallery_category_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('extension/video_gallery_category', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['video_gallery_category_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$video_gallery_category_id = $this->request->get['video_gallery_category_id'];
			$video_gallery_category_info = $this->model_extension_video_gallery_category->getVideoGalleryCategory($video_gallery_category_id);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['video_gallery_category_description'])) {
			$data['video_gallery_category_description'] = $this->request->post['video_gallery_category_description'];
		} elseif (isset($this->request->get['video_gallery_category_id'])) {
			$video_gallery_category_id = $this->request->get['video_gallery_category_id'];
			$data['video_gallery_category_description'] = $this->model_extension_video_gallery_category->getVideoGalleryDescriptionByCategory($video_gallery_category_id);
		} else {
			$data['video_gallery_category_description'] = array();
		}

		if (isset($this->request->post['keyword'])) {
			$data['keyword'] = $this->request->post['keyword'];
		} elseif (!empty($video_gallery_category_info)) {
			$data['keyword'] = $video_gallery_category_info['keyword'];
		} else {
			$data['keyword'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($video_gallery_category_info)) {
			$data['status'] = $video_gallery_category_info['status'];
		} else {
			$data['status'] = 1;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($video_gallery_category_info)) {
			$data['sort_order'] = $video_gallery_category_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		if (isset($this->request->post['chk_video_category_title'])) {
			$data['chk_video_category_title'] = $this->request->post['chk_video_category_title'];
		} elseif (!empty($video_gallery_category_info)) {
			$data['chk_video_category_title'] = $video_gallery_category_info['chk_video_category_title'];
		} else {
			$data['chk_video_category_title'] = 1;
		}

		if (isset($this->request->post['chk_video_category_description'])) {
			$data['chk_video_category_description'] = $this->request->post['chk_video_category_description'];
		} elseif (!empty($video_gallery_category_info)) {
			$data['chk_video_category_description'] = $video_gallery_category_info['chk_video_category_description'];
		} else {
			$data['chk_video_category_description'] = 1;
		}

		if (isset($this->request->post['chk_video_category_image'])) {
			$data['chk_video_category_image'] = $this->request->post['chk_video_category_image'];
		} elseif (!empty($video_gallery_category_info)) {
			$data['chk_video_category_image'] = $video_gallery_category_info['chk_video_category_image'];
		} else {
			$data['chk_video_category_image'] = 1;
		}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($video_gallery_category_info)) {
			$data['image'] = $video_gallery_category_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && file_exists(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($video_gallery_category_info) && $video_gallery_category_info['image'] && file_exists(DIR_IMAGE . $video_gallery_category_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($video_gallery_category_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/video_gallery_category_form', $data));
	}

	protected function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'extension/video_gallery_category')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['video_gallery_category_description'] as $language_id => $value) {
			if ((utf8_strlen($value['title']) < 3) || (utf8_strlen($value['title']) > 64)) {
				$this->error['title'][$language_id] = $this->language->get('error_title');
			}

			if (utf8_strlen($value['description']) < 3) {
				$this->error['description'][$language_id] = $this->language->get('error_description');
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	protected function validateDelete()
	{
		if (!$this->user->hasPermission('modify', 'extension/video_gallery_category')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	public function autocomplete()
	{
		$json = array();
		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/video_gallery_category');
			$data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start' => 0,
				'limit' => $this->config->get('config_admin_limit')
			);

			$results = $this->model_extension_video_gallery_category->getVideoGalleryCategories($data);
			foreach ($results as $result) {
				$json[] = array(
					'video_gallery_category_id' => $result['video_gallery_category_id'],
					'title' => strip_tags(html_entity_decode($result['title'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['title'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->setOutput(json_encode($json));
	}
}
