<?php
class ControllerExtensionModuleblogsubscribe extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/blogsubscribe');

		$this->document->setTitle($this->language->get('heading_title1'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_blogsubscribe', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$data['heading_title'] = $this->language->get('heading_title1');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_template'] = $this->language->get('entry_template');
		$data['entry_subject'] = $this->language->get('entry_subject');
		$data['entry_unsubtext'] = $this->language->get('entry_unsubtext');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
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
			'href' => $this->url->link('extension/module/blogsubscribe', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/blogsubscribe', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_blogsubscribe_status'])) {
			$data['module_blogsubscribe_status'] = $this->request->post['module_blogsubscribe_status'];
		} else {
			$data['module_blogsubscribe_status'] = $this->config->get('module_blogsubscribe_status');
		}
		
		if (isset($this->request->post['module_blogsubscribe_subject'])) {
			$data['module_blogsubscribe_subject'] = $this->request->post['module_blogsubscribe_subject'];
		} else {
			$data['module_blogsubscribe_subject'] = $this->config->get('module_blogsubscribe_subject');
		}
		
		if (isset($this->request->post['module_blogsubscribe_template'])) {
			$data['module_blogsubscribe_template'] = $this->request->post['module_blogsubscribe_template'];
		} else {
			$data['module_blogsubscribe_template'] = $this->config->get('module_blogsubscribe_template');
		}
		if (isset($this->request->post['module_blogsubscribe_unsubscribe'])) {
			$data['module_blogsubscribe_unsubscribe'] = $this->request->post['module_blogsubscribe_unsubscribe'];
		} else {
			$data['module_blogsubscribe_unsubscribe'] = $this->config->get('module_blogsubscribe_unsubscribe');
		}
		
		$data['sitelink'] = HTTP_CATALOG . 'index.php?route=extension/sendblog';

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/blogsubscribe', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/blogsubscribe')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}