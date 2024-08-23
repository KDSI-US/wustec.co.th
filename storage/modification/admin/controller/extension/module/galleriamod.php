<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleGalleriamod extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/galleria');
		$data['heading_title'] = $this->language->get('heading_title_mod');

		$this->document->setTitle($this->language->get('heading_title_mod'));

		$this->load->model('setting/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('galleriamod', $this->request->post);
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/galleriamod', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/galleriamod', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/galleriamod', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/galleriamod', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}

		$this->load->model('extension/module/galleria');

		$data['albums'] = array();

		if (!empty($this->request->post['album'])) {
			$albums = $this->request->post['album'];
		} elseif (!empty($module_info['album'])) {
			$albums = $module_info['album'];
		} else {
			$albums = array();
		}

		foreach ($albums as $album_id) {
			$album_info = $this->model_extension_module_galleria->getGallery($album_id);

			if ($album_info) {
				$data['albums'][] = array(
					'album_id' => $album_info['galleria_id'],
					'name'       => $album_info['name']
				);
			}
		}

		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($module_info)) {
			$data['width'] = $module_info['width'];
		} else {
			$data['width'] = 400;
		}

		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($module_info)) {
			$data['height'] = $module_info['height'];
		} else {
			$data['height'] = 300;
		}

		if (isset($this->request->post['view'])) {
			$data['view'] = $this->request->post['view'];
		} elseif (!empty($module_info)) {
			$data['view'] = $module_info['view'];
		} else {
			$data['view'] = 0;
		}

		if (isset($this->request->post['album_title'])) {
			$data['album_title'] = $this->request->post['album_title'];
		} elseif (!empty($module_info)) {
			$data['album_title'] = $module_info['album_title'];
		} else {
			$data['album_title'] = 2;
		}

		if (isset($this->request->post['album_description'])) {
			$data['album_description'] = $this->request->post['album_description'];
		} elseif (!empty($module_info)) {
			$data['album_description'] = $module_info['album_description'];
		} else {
			$data['album_description'] = 2;
		}

		if (isset($this->request->post['image_title'])) {
			$data['image_title'] = $this->request->post['image_title'];
		} elseif (!empty($module_info)) {
			$data['image_title'] = $module_info['image_title'];
		} else {
			$data['image_title'] = 0;
		}

		if (isset($this->request->post['image_description'])) {
			$data['image_description'] = $this->request->post['image_description'];
		} elseif (!empty($module_info)) {
			$data['image_description'] = $module_info['image_description'];
		} else {
			$data['image_description'] = 0;
		}

		if (isset($this->request->post['grid'])) {
			$data['grid'] = $this->request->post['grid'];
		} elseif (!empty($module_info)) {
			$data['grid'] = $module_info['grid'];
		} else {
			$data['grid'] = 4;
		}

		if (isset($this->request->post['animation'])) {
			$data['animation'] = $this->request->post['animation'];
		} elseif (!empty($module_info)) {
			$data['animation'] = $module_info['animation'];
		} else {
			$data['animation'] = 0;
		}

		if (isset($this->request->post['css'])) {
			$data['css'] = $this->request->post['css'];
		} elseif (!empty($module_info)) {
			$data['css'] = $module_info['css'];
		} else {
			$data['css'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/galleriamod', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/galleriamod')) {
			$this->error['warning'] = $this->language->get('error_permission');
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
