<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleMpgallery extends Controller {
	use mpphotogallery\trait_mpphotogallery;
	private $error = [];

	public function __construct($registry) {
		parent :: __construct($registry);
		$this->igniteTraitMpPhotoGallery($registry);
	}

	public function index() {
		$this->load->language('extension/module/mpgallery');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model($this->model_file['extension/module']['path']);
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->{$this->model_file['extension/module']['obj']}->addModule('mpgallery', $this->request->post);
			} else {
				$this->{$this->model_file['extension/module']['obj']}->editModule($this->request->get['module_id'], $this->request->post);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link($this->extension_page_path, $this->token.'=' . $this->session->data[$this->token] . '&type=module', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_gallery'] = $this->language->get('entry_gallery');
		$data['entry_limit'] = $this->language->get('entry_limit');
		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_height'] = $this->language->get('entry_height');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_carousel'] = $this->language->get('entry_carousel');
		$data['entry_extitle'] = $this->language->get('entry_extitle');

		$data['help_gallery'] = $this->language->get('help_gallery');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		} else {
			$data['error_title'] = [];
		}

		if (isset($this->error['width'])) {
			$data['error_width'] = $this->error['width'];
		} else {
			$data['error_width'] = '';
		}

		if (isset($this->error['height'])) {
			$data['error_height'] = $this->error['height'];
		} else {
			$data['error_height'] = '';
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('extension/module/mpphotogallery_setting', $this->token.'=' . $this->session->data[$this->token], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link($this->extension_page_path, $this->token.'=' . $this->session->data[$this->token] . '&type=module', true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/mpgallery', $this->token.'=' . $this->session->data[$this->token], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/mpgallery', $this->token.'=' . $this->session->data[$this->token] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/mpgallery', $this->token.'=' . $this->session->data[$this->token], true);
		} else {
			$data['action'] = $this->url->link('extension/module/mpgallery', $this->token.'=' . $this->session->data[$this->token] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link($this->extension_page_path, $this->token.'=' . $this->session->data[$this->token] . '&type=module', true);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->{$this->model_file['extension/module']['obj']}->getModule($this->request->get['module_id']);
		}

		$data['get_token'] = $this->token;
		$data['token'] = $this->session->data[$this->token];

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}

		$this->load->model('extension/mpphotogallery/gallery');

		$data['gallerys'] = [];

		if (!empty($this->request->post['gallery'])) {
			$gallerys = $this->request->post['gallery'];
		} elseif (!empty($module_info['gallery'])) {
			$gallerys = $module_info['gallery'];
		} else {
			$gallerys = [];
		}

		foreach ($gallerys as $gallery_id) {
			$gallery_info = $this->model_extension_mpphotogallery_gallery->getGallery($gallery_id);

			if ($gallery_info) {
				$data['gallerys'][] = array(
					'gallery_id' => $gallery_info['gallery_id'],
					'title'       => $gallery_info['title']
				);
			}
		}

		if (isset($this->request->post['limit'])) {
			$data['limit'] = $this->request->post['limit'];
		} elseif (!empty($module_info)) {
			$data['limit'] = $module_info['limit'];
		} else {
			$data['limit'] = 5;
		}

		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($module_info)) {
			$data['width'] = $module_info['width'];
		} else {
			$data['width'] = 200;
		}

		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($module_info)) {
			$data['height'] = $module_info['height'];
		} else {
			$data['height'] = 200;
		}

		if (isset($this->request->post['carousel'])) {
			$data['carousel'] = $this->request->post['carousel'];
		} elseif (!empty($module_info)) {
			$data['carousel'] = isset($module_info['carousel']) ? $module_info['carousel'] : '';
		} else {
			$data['carousel'] = '';
		}

		if (isset($this->request->post['extitle'])) {
			$data['extitle'] = $this->request->post['extitle'];
		} elseif (!empty($module_info)) {
			$data['extitle'] = isset($module_info['extitle']) ? $module_info['extitle'] : '1';
		} else {
			$data['extitle'] = '1';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}

		if (isset($this->request->post['gall_description'])) {
			$data['gall_description'] = $this->request->post['gall_description'];
		} elseif (!empty($module_info['gall_description'])) {
			$data['gall_description'] = $module_info['gall_description'];
		} else {
			$data['gall_description'] = [];
		}

		$data['languages'] = $this->getLanguages();

		$data['mtabs'] = $this->load->controller('extension/mpphotogallery/mtabs');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->viewLoad('extension/module/mpgallery', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/mpgallery')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['gall_description'] as $language_id => $value) {
			if ((utf8_strlen($value['title']) < 1) || (utf8_strlen($value['title']) > 255)) {
				$this->error['title'][$language_id] = $this->language->get('error_title');
			}
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (!$this->request->post['width']) {
			$this->error['width'] = $this->language->get('error_width');
		}

		if (!$this->request->post['height']) {
			$this->error['height'] = $this->language->get('error_height');
		}

		return !$this->error;
	}
}
