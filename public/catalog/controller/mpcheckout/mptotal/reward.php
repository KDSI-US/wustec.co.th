<?php
class ControllerMpcheckoutMptotalReward extends Controller {
	public function index() {
		$points = $this->customer->getRewardPoints();

		$points_total = 0;

		foreach ($this->cart->getProducts() as $product) {
			if ($product['points']) {
				$points_total += $product['points'];
			}
		}

		if ($points && $points_total && $this->config->get('total_reward_status')) {
			$this->load->language('mpcheckout/mptotal/reward');

			$data['heading_title'] = sprintf($this->language->get('heading_title'), $points);

			$data['text_loading'] = $this->language->get('text_loading');

			$data['entry_reward'] = sprintf($this->language->get('entry_reward'), $points_total);

			$data['button_reward'] = $this->language->get('button_reward');

			if (isset($this->session->data['reward'])) {
				$data['reward'] = $this->session->data['reward'];
			} else {
				$data['reward'] = '';
			}

			return $this->load->view('mpcheckout/mptotal/reward', $data);
		}
	}

	public function reward() {
		$this->load->language('mpcheckout/mptotal/reward');

		$json = array();

		$points = $this->customer->getRewardPoints();

		$points_total = 0;

		foreach ($this->cart->getProducts() as $product) {
			if ($product['points']) {
				$points_total += $product['points'];
			}
		}

		if (empty($this->request->post['reward'])) {
			$json['error'] = $this->language->get('error_reward');
		}

		if ($this->request->post['reward'] > $points) {
			$json['error'] = sprintf($this->language->get('error_points'), $this->request->post['reward']);
		}

		if ($this->request->post['reward'] > $points_total) {
			$json['error'] = sprintf($this->language->get('error_maximum'), $points_total);
		}

		if (!$json) {
			$this->session->data['reward'] = abs($this->request->post['reward']);

			$json['refresh_cart'] = true;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
