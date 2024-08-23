<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleMpBlogArchive extends Controller {
	use mpblog\trait_mpblog;
	private $error = [];

	public function __construct($registry) {
		parent :: __construct($registry);
		$this->igniteTraitMpBlog($registry);
	}

	public function index() {
		$this->load->language('extension/module/mpblogarchive');

		$this->document->setTitle(strip_tags($this->language->get('heading_title')));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_mpblogarchive', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link($this->extension_page_path, $this->token.'=' . $this->session->data[$this->token] . '&type=module', true));
		}

		$data['heading_title'] = strip_tags($this->language->get('heading_title'));

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_blogcount'] = $this->language->get('entry_blogcount');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $this->token.'=' . $this->session->data[$this->token], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link($this->extension_page_path, $this->token.'=' . $this->session->data[$this->token] . '&type=module', true)
		];

		$data['breadcrumbs'][] = [
			'text' => strip_tags($this->language->get('heading_title')),
			'href' => $this->url->link('extension/module/mpblogarchive', $this->token.'=' . $this->session->data[$this->token], true)
		];

		$data['action'] = $this->url->link('extension/module/mpblogarchive', $this->token.'=' . $this->session->data[$this->token], true);

		$data['cancel'] = $this->url->link($this->extension_page_path, $this->token.'=' . $this->session->data[$this->token] . '&type=module', true);

		if (isset($this->request->post['module_mpblogarchive_status'])) {
			$data['module_mpblogarchive_status'] = $this->request->post['module_mpblogarchive_status'];
		} else {
			$data['module_mpblogarchive_status'] = $this->config->get('module_mpblogarchive_status');
		}

		if (isset($this->request->post['module_mpblogarchive_blogcount'])) {
			$data['module_mpblogarchive_blogcount'] = $this->request->post['module_mpblogarchive_blogcount'];
		} else {
			$data['module_mpblogarchive_blogcount'] = $this->config->get('module_mpblogarchive_blogcount');
		}

		$data['mpblogmenu'] = $this->load->controller('extension/mpblog/mpblogmenu');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->viewLoad('extension/module/mpblogarchive', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/mpblogarchive')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}