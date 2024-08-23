<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiCheckoutSuccess extends ocrestapicontroller {
	public function index() {
		$this->checkPlugin();
		$language = $this->load->language('checkout/success');
		$language = $this->load->language('ocrestapi/message');

		if (isset($data['order_id'])) {
			$this->cart->clear();		
		}
		
		if ($this->customer->isLogged()) {
			$data['text_message'] = $this->language->get('text_checkout_success');
		}
		unset($language['backup']);
		$this->json['language'] = $language;
		$this->json['data'] = $data;
		return $this->sendResponse();
	}
}