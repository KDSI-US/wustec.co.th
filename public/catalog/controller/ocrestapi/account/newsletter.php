<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiAccountNewsletter extends OcrestapiController {
	public function index() {

		$this->checkPlugin();
		

		$language = $this->load->language('account/newsletter');
		
		//$this->document->setTitle($this->language->get('heading_title'));

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$this->load->model('account/customer');

			$this->model_account_customer->editNewsletter($this->request->post['newsletter']);

			$this->json['data']['success'] = $this->language->get('text_success');
			//return new Action('ocrestapi/account/account');
			return $this->sendResponse();
		}

		
		$data['newsletter'] = $this->customer->getNewsletter();

		
		unset($language['backup']);
		$this->json['language'] = $language;
		$this->json['data'] = $data;
		return $this->sendResponse();
	}
}