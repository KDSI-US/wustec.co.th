<?php
class ControllerExtensionModuleMpBlogSearch extends Controller {
	use mpblog\trait_mpblog;
	private $error = [];

	public function __construct($registry) {
		parent :: __construct($registry);
		$this->igniteTraitMpBlog($registry);
	}

	public function index() {
		$this->load->language('extension/module/mpblogsearch');

		$this->document->setTitle(strip_tags($this->language->get('heading_title')));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_mpblogsearch', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link($this->extension_page_path, $this->token.'=' . $this->session->data[$this->token] . '&type=module', true));
		}

		$data['heading_title'] = strip_tags($this->language->get('heading_title'));

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_status'] = $this->language->get('entry_status');

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
			'href' => $this->url->link('extension/module/mpblogsearch', $this->token.'=' . $this->session->data[$this->token], true)
		];

		$data['action'] = $this->url->link('extension/module/mpblogsearch', $this->token.'=' . $this->session->data[$this->token], true);

		$data['cancel'] = $this->url->link($this->extension_page_path, $this->token.'=' . $this->session->data[$this->token] . '&type=module', true);

		if (isset($this->request->post['module_mpblogsearch_status'])) {
			$data['module_mpblogsearch_status'] = $this->request->post['module_mpblogsearch_status'];
		} else {
			$data['module_mpblogsearch_status'] = $this->config->get('module_mpblogsearch_status');
		}

		$data['mpblogmenu'] = $this->load->controller('extension/mpblog/mpblogmenu');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->viewLoad('extension/module/mpblogsearch', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/mpblogsearch')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}