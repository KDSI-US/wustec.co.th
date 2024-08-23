<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleVideoGalleryLeft extends Controller
{
	private $error = array();

	public function index()
	{
		$this->load->language('extension/module/video_gallery_left');

		$this->document->setTitle($this->language->get('heading_title1'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_video_gallery_left', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$data['heading_title1'] = $this->language->get('heading_title1');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_video_count'] = $this->language->get('entry_video_count');
		$data['entry_no'] = $this->language->get('entry_no');
		$data['entry_yes'] = $this->language->get('entry_yes');

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
			'text' => $this->language->get('heading_title1'),
			'href' => $this->url->link('extension/module/video_gallery_left', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/video_gallery_left', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_video_gallery_left_status'])) {
			$data['module_video_gallery_left_status'] = $this->request->post['module_video_gallery_left_status'];
		} else {
			$data['module_video_gallery_left_status'] = $this->config->get('module_video_gallery_left_status');
		}

		if (isset($this->request->post['module_video_gallery_left_count'])) {
			$data['module_video_gallery_left_count'] = $this->request->post['module_video_gallery_left_count'];
		} else {
			$data['module_video_gallery_left_count'] = $this->config->get('module_video_gallery_left_count');
		}


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/video_gallery_left', $data));
	}

	protected function validate()
	{
		if (!$this->user->hasPermission('modify', 'extension/module/video_gallery_left')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
