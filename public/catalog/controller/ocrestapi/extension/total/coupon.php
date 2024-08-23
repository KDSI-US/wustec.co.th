<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');

class ControllerOcrestapiExtensionTotalCoupon extends OcrestapiController {
	public function index() {
		$this->checkPlugin();
		if ($this->config->get('total_coupon_status')) {
			$language = $this->load->language('extension/total/coupon');

			if (isset($this->session->data['coupon'])) {
				$data['coupon'] = $this->session->data['coupon'];
			} else {
				$data['coupon'] = '';
			}
			unset($language['backup']);
			$this->json['language'] = $language;
			$this->json['data']	= $data;
			$this->sendResponse();
		}
	}

	public function coupon() {
		$this->checkPlugin();
		$language = $this->load->language('extension/total/coupon');

		$json = array();

		$this->load->model('extension/total/coupon');
		if (isset($this->request->post['coupon'])) {
			$coupon = $this->request->post['coupon'];
		} else {
			$coupon = '';
		}

		$coupon_info = $this->model_extension_total_coupon->getCoupon($coupon);
		if (!isset($this->request->post['coupon']) && empty($this->request->post['coupon'])) {
			$json['coupon'] = $this->language->get('error_empty');

			unset($this->session->data['coupon']);
			$this->json['errors']	= $json;
			
			} elseif (!$json && $coupon_info) {
			$this->session->data['coupon'] = $this->request->post['coupon'];

			$data['success'] = $this->language->get('text_success');
			$this->json['data']	= $data;
			} else {
			$json['coupon'] = $this->language->get('error_coupon');
			$this->json['errors']	= $json;
		}
		unset($language['backup']);
		$this->json['language'] = $language;
		
		$this->sendResponse();
	}
}
