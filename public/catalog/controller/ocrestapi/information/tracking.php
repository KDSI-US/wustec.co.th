<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiInformationTracking extends OcrestapiController {
	public function index() {
		$this->checkPlugin();
		$this->load->language('information/tracking');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('ocrestapi/common/home')
		);
		
		$this->json['data'] = $data;
		
		return $this->sendResponse();
	}
	
	public function track() {
		$json = array();
		
		$this->load->model('account/shipping');
		
		$this->model_account_shipping->getShippingByCode($this->request->get['code']);
		
	}
}