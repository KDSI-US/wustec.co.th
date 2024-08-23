<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiCommonCurrency extends OcrestapiController {
	public function index() {
		$this->load_language();
		$this->load->language('common/currency');
		//$data['action'] = 'common/currency/currency';

		$data['code'] = $this->session->data['currency'];

		$this->load->model('localisation/currency');

		$data['currencies'] = array();

		$results = $this->model_localisation_currency->getCurrencies();
		
		foreach ($results as $result) {
			if ($result['status']) {
				$data['currencies'][] = array(
					'title'        => $result['title'],
					'code'         => $result['code'],
					'symbol_left'  => $result['symbol_left'],
					'symbol_right' => $result['symbol_right']
				);
			}
		}

		$this->json['data'] =$data;
		return $this->sendResponse();
	}

	public function currency() {
		$this->load_language();
		if (isset($this->request->post['code'])) {
			$data['currency'] = $this->request->post['code'];
		
		}
		$this->json['data'] =isset($data)?$data:'';
		return $this->sendResponse();
		
	}
}