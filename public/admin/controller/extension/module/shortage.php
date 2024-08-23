<?php

class ControllerExtensionModuleShortage extends Controller
{
	private $error = array();

	public function index()
	{
		$this->load->language('extension/module/shortage');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/module');
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (isset($this->request->post['shortagestatus'])) {
				$this->request->post['status'] = $this->request->post['shortagestatus'];
				unset($this->request->post['shortagestatus']);
				if (!isset($this->request->get['module_id'])) {
					$this->model_setting_module->addModule('shortage', $this->request->post);
				} else {
					$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
				}
			}
			if (isset($this->request->post['status'])) {
				$this->request->post['shortagestatus'] = $this->request->post['status'];
				unset($this->request->post['status']);
				$this->model_setting_setting->editSetting('shortage', $this->request->post);
			}

			$this->cache->delete('product');
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
		if (isset($this->error['shortagelimit'])) {
			$data['error_limit'] = $this->error['shortagelimit'];
		} else {
			$data['error_limit'] = '';
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
				'href' => $this->url->link('extension/module/shortage', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/shortage', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/shortage', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/shortage', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
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

		if (isset($this->request->post['shortagelimit'])) {
			$data['limit'] = $this->request->post['shortagelimit'];
		} elseif (!empty($module_info)) {
			$data['limit'] = $module_info['shortagelimit'];
		} else {
			$data['limit'] = '';
		}

		if (isset($this->request->post['shortagestatus'])) {
			$data['shortagestatus'] = $this->request->post['shortagestatus'];
		} elseif (!empty($module_info)) {
			$data['shortagestatus'] = $module_info['status'];
		} else {
			$data['shortagestatus'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('extension/module/shortage', $data)); //--to show the page
	}

	protected function validate()
	{
		if (!$this->user->hasPermission('modify', 'extension/module/shortage')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (!$this->request->post['shortagelimit']) {
			$this->error['shortagelimit'] = $this->language->get('error_limit');
		}
		return !$this->error;
	}

}
