<?php
/* This file is under Git Control by KDSI. */
/* This file is under Git Control by KDSI. */
class ControllerExtensionAnnouncements extends Controller
{
	private $error = array();

	public function index()
	{
		$this->language->load('extension/announcements');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/announcements');

		$this->getList();
	}

	public function add()
	{
		$this->language->load('extension/announcements');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/announcements');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_announcements->addAnnouncement($this->request->post);

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

			$this->response->redirect($this->url->link('extension/announcements', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit()
	{
		$this->language->load('extension/announcements');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/announcements');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_announcements->editAnnouncement($this->request->get['announcement_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('extension/announcements', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->language->load('extension/announcements');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/announcements');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $announcement_id) {
				$this->model_extension_announcements->deleteAnnouncement($announcement_id);
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

			$this->response->redirect($this->url->link('extension/announcements', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList()
	{
		$this->load->model('tool/image');

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'a.announcement_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
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
			'href'      => $this->url->link('extension/announcements', 'user_token=' . $this->session->data['user_token'] . $url, true),
			'separator' => ' :: '
		);

		$data['add'] = $this->url->link('extension/announcements/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/announcements/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['announcements'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$announcement_total = $this->model_extension_announcements->getTotalAnnouncements($data);

		$results = $this->model_extension_announcements->getAnnouncements($filter_data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('extension/announcements/edit', 'user_token=' . $this->session->data['user_token'] . '&announcement_id=' . $result['announcement_id'] . $url, true)
			);

			$data['announcements'][] = array(
				'announcement_id'   => $result['announcement_id'],
				'image'             => $this->model_tool_image->resize($result['image']),
				'url'         			=> $result['url'],
				'title'       			=> $result['title'],
				'description' 			=> substr(strip_tags(html_entity_decode($result['text'], ENT_QUOTES, 'UTF-8')), 0, 100) . "...",
				'hit'               => $result['hit'],
				'added_by'          => $result['user_id'],
				'added_at'          => $result['added_at'],
				'status'            => ($result['status']) ? "<div style=\"color:green;\">Enabled</div>" : "<div style=\"color:red;\">Disabled</div>",
				'sort_order'        => $result['sort_order'],
				'selected'          => isset($this->request->post['selected']) && in_array($result['announcement_id'], $this->request->post['selected']),
				'action'            => $action,
				'edit'              => $this->url->link('extension/announcements/edit', 'user_token=' . $this->session->data['user_token'] . '&announcement_id=' . $result['announcement_id'] . $url, true),
				'delete'            => $this->url->link('extension/announcements/delete', 'user_token=' . $this->session->data['user_token'] . '&announcement_id=' . $result['announcement_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['column_title'] = $this->language->get('column_title');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_action'] = $this->language->get('column_action');
		$data['text_list'] = $this->language->get('text_list');
		$data['button_add'] = $this->language->get('button_add');
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

		$data['sort_title'] = $this->url->link('extension/announcements', 'user_token=' . $this->session->data['user_token'] . '&sort=ad.title' . $url, true);
		$data['sort_announcement_id'] = $this->url->link('extension/announcements', 'user_token=' . $this->session->data['user_token'] . '&sort=a.announcement_id' . $url, true);
		$data['sort_sort_order'] = $this->url->link('extension/announcements', 'user_token=' . $this->session->data['user_token'] . '&sort=i.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $announcement_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/announcements', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($announcement_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($announcement_total - $this->config->get('config_limit_admin'))) ? $announcement_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $announcement_total, ceil($announcement_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/announcement_list', $data));
	}

	protected function getForm()
	{
		$this->load->model('tool/image');

		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_form'] = !isset($this->request->get['announcement_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_browse'] = $this->language->get('text_browse');
		$data['text_clear'] = $this->language->get('text_clear');
		$data['text_image_manager'] = $this->language->get('text_image_manager');
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_url'] = $this->language->get('entry_url');
		$data['entry_keyword'] = $this->language->get('entry_keyword');
		$data['entry_meta_description'] = $this->language->get('entry_meta_description');
		$data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['button_getdata'] = $this->language->get('button_getdata');
		$data['entry_category'] = $this->language->get('entry_category');
		$data['help_keyword'] = $this->language->get('help_keyword');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_data'] = $this->language->get('tab_data');

		$data['entry_announcement_title'] = $this->language->get('entry_announcement_title');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['entry_announcement_desc'] = $this->language->get('entry_announcement_desc');
		$data['entry_announcement_img'] = $this->language->get('entry_announcement_img');

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

		if (isset($this->error['url'])) {
			$data['error_url'] = $this->error['url'];
		} else {
			$data['error_url'] = array();
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
			'href'      => $this->url->link('extension/announcements', 'user_token=' . $this->session->data['user_token'] . $url, true),
			'separator' => ' :: '
		);

		if (!isset($this->request->get['announcement_id'])) {
			$data['action'] = $this->url->link('extension/announcements/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/announcements/edit', 'user_token=' . $this->session->data['user_token'] . '&announcement_id=' . $this->request->get['announcement_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('extension/announcements', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['announcement_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$announcement_info = $this->model_extension_announcements->getAnnouncement($this->request->get['announcement_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['text'])) {
			$data['announcement_text'] = $this->request->post['text'];
		} elseif (isset($this->request->get['announcement_id'])) {
			$data['announcement_text'] = $this->model_extension_announcements->getAnnouncementText($this->request->get['announcement_id']);
		} else {
			$data['announcement_text'] = array();
		}

		/* Announcement Categories */
		$this->load->model('extension/announcements_category');

		if (isset($this->request->post['announcement_categories'])) {
			$categories = $this->request->post['announcement_categories'];
		} elseif (isset($this->request->get['announcement_id'])) {
			$announcement_id = $this->request->get['announcement_id'];
			$categories = $this->model_extension_announcements_category->getAnnouncementCategoryByID($announcement_id);
		} else {
			$categories = array();
		}

		$data['announcement_categories'] = array();

		if ($categories) {
			foreach ($categories as $category) {
				$data['announcement_categories'][] = array(
					'announcement_category_id' => $category['announcement_category_id'],
					'title'       							=> $category['title']
				);
			}
		}

		if (isset($this->request->post['keyword'])) {
			$data['keyword'] = $this->request->post['keyword'];
		} elseif (!empty($announcement_info)) {
			$data['keyword'] = $announcement_info['keyword'];
		} else {
			$data['keyword'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($announcement_info)) {
			$data['status'] = $announcement_info['status'];
		} else {
			$data['status'] = 1;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($announcement_info)) {
			$data['sort_order'] = $announcement_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}
/* 
		if (isset($this->request->post['chk_title'])) {
			$data['chk_title'] = $this->request->post['chk_title'];
		} elseif (!empty($announcement_info)) {
			$data['chk_title'] = $announcement_info['chk_title'];
		} else {
			$data['chk_title'] = 1;
		} */
/* 
		if (isset($this->request->post['chk_text'])) {
			$data['chk_text'] = $this->request->post['chk_text'];
		} elseif (!empty($announcement_info)) {
			$data['chk_text'] = $announcement_info['chk_text'];
		} else {
			$data['chk_text'] = 1;
		} */
/* 
		if (isset($this->request->post['chk_video_image'])) {
			$data['chk_video_image'] = $this->request->post['chk_video_image'];
		} elseif (!empty($announcement_info)) {
			$data['chk_video_image'] = $announcement_info['chk_video_image'];
		} else {
			$data['chk_video_image'] = 1;
		} */

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($announcement_info)) {
			$data['image'] = $announcement_info['image'];
		} else {
			$data['image'] = '';
		}
		
		if (isset($this->request->post['image'])) {
			$data['thumb'] = $this->request->post['image'];
		} elseif (!empty($announcement_info) && $announcement_info['image']) {
			$data['thumb'] = $this->model_tool_image->resize($announcement_info['image']);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/announcement_form', $data));
	}

	protected function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'extension/announcements')) {
			$this->error['warning'] = $this->language->get('error_permission');
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
		if (!$this->user->hasPermission('modify', 'extension/announcements')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
