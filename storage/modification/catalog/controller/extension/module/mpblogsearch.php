<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleMpBlogSearch extends Controller {
	public function index() {
		if ($this->config->get('mpblog_status')) {

			$this->load->language('extension/module/mpblogsearch');

			$this->document->addScript('catalog/view/javascript/mpblog/mpblog.js');

			$data['text_search'] = $this->language->get('text_search');

			$data['search'] = '';
			if (isset($this->request->get['search'])) {
				$data['search'] = $this->request->get['search'];
			}

			$data['url'] = $this->url->link('extension/mpblog/blogs','',true);
			return $this->load->view('extension/module/mpblogsearch', $data);
		
		}
	}
}