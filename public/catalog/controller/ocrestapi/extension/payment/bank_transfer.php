<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiExtensionPaymentBankTransfer extends ocrestapicontroller {
	public function index() {
		$this->load->language('extension/payment/bank_transfer');
		$data['title']  = $this->language->get('text_instruction');
		$data['description']  = $this->language->get('text_description');
		$data['info'] = nl2br($this->config->get('payment_bank_transfer_bank' . $this->config->get('config_language_id')));
		$data['payment'] = $this->language->get('text_payment');
		$this->json['data'] = $data;
		return $this->sendResponse();
	}

	public function confirm() {
		$json = array();
		$this->checkPlugin();
		
			if ($this->request->post['payment_method']['code'] == 'bank_transfer') {

			$this->load->language('extension/payment/bank_transfer');

			$this->load->model('checkout/order');

			$comment  = $this->language->get('text_instruction') . "\n\n";
			
			$comment .= $this->config->get('payment_bank_transfer_bank' . $this->config->get('config_language_id')) . "\n\n";
			$comment .= $this->language->get('text_payment');

			$this->model_checkout_order->addOrderHistory($this->request->post['order_id'], $this->config->get('payment_bank_transfer_order_status_id'), $comment, true);
		
			$json['redirect'] = $this->url->link('checkout/success');
		}
		
		$this->json['data'] = $json;
		return $this->sendResponse();		
	}
}