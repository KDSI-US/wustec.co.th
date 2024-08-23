<?php
class ControllerMpcheckoutMptotalCoupon extends Controller {
	public function index() {
		if ($this->config->get('total_coupon_status')) {
			$this->load->language('mpcheckout/mptotal/coupon');

			$data['heading_title'] = $this->language->get('heading_title');

			$data['text_loading'] = $this->language->get('text_loading');

			$data['entry_coupon'] = $this->language->get('entry_coupon');

			$data['button_coupon'] = $this->language->get('button_coupon');

			if (isset($this->session->data['coupon'])) {
				$data['coupon'] = $this->session->data['coupon'];
			} else {
				$data['coupon'] = '';
			}

			return $this->load->view('mpcheckout/mptotal/coupon', $data);
		}
	}

	public function coupon() {
		$this->load->language('mpcheckout/mptotal/coupon');

		$json = array();

		if(VERSION > '2.2.0.0') {
			$this->load->model('extension/total/coupon');
		} else if(VERSION <= '2.0.3.1') {
			$this->load->model('checkout/coupon');
		} else{
			$this->load->model('total/coupon');
		}

		if (isset($this->request->post['coupon'])) {
			$coupon = $this->request->post['coupon'];
		} else {
			$coupon = '';
		}

		if(VERSION > '2.2.0.0') {
			$coupon_info = $this->model_extension_total_coupon->getCoupon($coupon);
		} else if(VERSION <= '2.0.3.1') {
			$coupon_info = $this->model_checkout_coupon->getCoupon($coupon);
		} else{
			$coupon_info = $this->model_total_coupon->getCoupon($coupon);
		}

		if (empty($this->request->post['coupon'])) {
			$json['error'] = $this->language->get('error_empty');

			unset($this->session->data['coupon']);
		} elseif ($coupon_info) {
			$this->session->data['coupon'] = $this->request->post['coupon'];

			$json['refresh_cart'] = true;
		} else {
			$json['error'] = $this->language->get('error_coupon');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
