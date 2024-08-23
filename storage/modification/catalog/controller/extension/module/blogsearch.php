<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleblogsearch extends Controller {
	public function index() {
		$this->load->language('common/search');

		$data['text_search'] = $this->language->get('text_search');
		
		if (isset($this->request->get['bsearch'])) {
			$data['search'] = $this->request->get['bsearch'];
		} else {
			$data['search'] = '';
		}
		
				return $this->load->view('extension/module/blogsearch', $data);
		
	}
}