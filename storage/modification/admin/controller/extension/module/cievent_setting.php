<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleCieventSetting extends Controller {
	private $error = array();

	public function install() {
		$this->load->model('user/user_group');

		$this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'extension/cievent');
			$this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'extension/cievent');

			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/cievent');
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/cievent');
	}

	public function index() {
		$this->load->language('extension/module/cievent_setting');

		$this->document->setTitle($this->language->get('heading_title_page'));

		$data['heading_title'] = $this->language->get('heading_title_page');

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_cievent_setting', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/module/cievent_setting', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

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
			'text' => $this->language->get('heading_title_page'),
			'href' => $this->url->link('extension/module/cievent_setting', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/cievent_setting', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$data['event_href'] = $this->url->link('marketplace/event', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_cievent_setting_status'])) {
			$data['module_cievent_setting_status'] = $this->request->post['module_cievent_setting_status'];
		} else {
			$data['module_cievent_setting_status'] = $this->config->get('module_cievent_setting_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/cievent_setting', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/cievent_setting')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}