<?php
/* This file is under Git Control by KDSI. */

class ControllerExtensionModuleInvoiceImages extends Controller {

	private $error = [];

	public function index() {
		$this->load->language('extension/module/invoice_images');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');
		if (('POST' == $this->request->server['REQUEST_METHOD']) && $this->validate()) {
			$this->model_setting_setting->editSetting('module_invoice_images', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		$data['breadcrumbs'] = [];
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
		];
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true),
		];
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/invoice_images', 'user_token=' . $this->session->data['user_token'], true),
		];
		$data['action'] = $this->url->link('extension/module/invoice_images', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		if (isset($this->request->post['module_invoice_images_status'])) {
			$data['invoice_images_status'] = $this->request->post['module_invoice_images_status'];
		} else {
			$data['invoice_images_status'] = $this->config->get('module_invoice_images_status');
		}
		if (isset($this->request->post['module_invoice_images_width'])) {
			$data['invoice_images_width'] = abs((int)$this->request->post['module_invoice_images_width']);
		} else {
			$data['invoice_images_width'] = $this->config->get('module_invoice_images_width');
		}
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/invoice_images', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/invoice_images')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
}
