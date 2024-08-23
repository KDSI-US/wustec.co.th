<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiExtensionPaymentCheque extends ocrestapicontroller {
	public function index() {
		$this->load->language('extension/payment/cheque');
		$data['title']  = $this->language->get('text_instruction');
		$info = $this->language->get('text_payable') . $this->config->get('payment_cheque_payable')."\n";
		$info .= $this->language->get('text_address') . $this->config->get('config_address');
		$data['info']  = nl2br($info);
		
		// $data['payment_cheque_payable']  = $this->config->get('payment_cheque_payable');
		// $data['address']  = $this->language->get('text_address');
		// $data['config_address']  = $this->config->get('config_address');
		$data['payment']  = $this->language->get('text_payment');
		$this->json['data'] = $data;
		return $this->sendResponse();
	}

	public function confirm() {
		$json = array();
		
		if ($this->session->data['payment_method']['code'] == 'cheque') {
			$this->load->language('extension/payment/cheque');

			$this->load->model('checkout/order');

			$comment  = $this->language->get('text_payable') . "\n";
			$comment .= $this->config->get('payment_cheque_payable') . "\n\n";
			$comment .= $this->language->get('text_address') . "\n";
			$comment .= $this->config->get('config_address') . "\n\n";
			$comment .= $this->language->get('text_payment') . "\n";
			
			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_cheque_order_status_id'), $comment, true);
			
			$json['redirect'] = $this->url->link('checkout/success');
		}
		
		$this->json['data'] = $data;
		return $this->sendResponse();
	}
}