<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiAccountTransaction extends OcrestapiController {
	public function index() {
		$this->checkPlugin();

		

		$language = $this->load->language('account/transaction');

		

		$this->load->model('account/transaction');
		
		$data['column_amount'] = sprintf($this->language->get('column_amount'), $this->config->get('config_currency'));

		if (isset($this->request->get['start'])) {
			$start = $this->request->get['start'];
		} else {
			$start = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = 10;
		}

		$data['transactions'] = array();

		$filter_data = array(
			'sort'  => 'date_added',
			'order' => 'DESC',
			'start' => $start,
			'limit' => $limit
		);

		$transaction_total = $this->model_account_transaction->getTotalTransactions();

		$results = $this->model_account_transaction->getTransactions($filter_data);

		foreach ($results as $result) {
			$data['transactions'][] = array(
				'amount'      => $this->currency->format($result['amount'], $this->config->get('config_currency')),
				'description' => $result['description'],
				'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		
        unset($language['backup']);
        $this->json['language'] =$language;
		$this->json['data']	= $data;

		return $this->sendResponse();
	}
}