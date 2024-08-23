<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiAccountSuccess extends OcrestapiController {
	public function index() {
		$this->checkPlugin();
		$language = $this->load->language('ocrestapi/account/success');

	

		if ($this->customer->isLogged()) {
			$data['text_message'] = sprintf($this->language->get('text_message'), $this->url->link('information/contact'));
		} else {
			$data['text_message'] = sprintf($this->language->get('text_approval'), $this->config->get('config_name'), $this->url->link('information/contact'));
		}

		if ($this->cart->hasProducts()) {
			$data['continue'] = $this->url->link('checkout/cart');
		} else {
			$data['continue'] = $this->url->link('account/account', '', true);
		}
		unset($language['backup']);
		$this->json['language'] = $language;
		$this->json['data'] = $data;
		return $this->sendResponse();

		
	}
}