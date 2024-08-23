<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiAccountRecurring extends ocrestapicontroller {
	public function index() {
		$this->checkPlugin();
		

		$language = $this->load->language('account/recurring');
		

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

		$data['recurrings'] = array();

		$this->load->model('account/recurring');

		$recurring_total = $this->model_account_recurring->getTotalOrderRecurrings();

		$results = $this->model_account_recurring->getOrderRecurrings($start,$limit);

		foreach ($results as $result) {
			if ($result['status']) {
				$status = $this->language->get('text_status_' . $result['status']);
			} else {
				$status = '';
			}

			$data['recurrings'][] = array(
				'order_recurring_id' => $result['order_recurring_id'],
				'product'            => $result['product_name'],
				'status'             => $status,
				'date_added'         => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'view'               => $this->url->link('ocrestapi/account/recurring/info', 'order_recurring_id=' . $result['order_recurring_id'], true),
			);
		}

		unset($language['backup']);
		$this->json['language'] = $language;
		$this->json['data'] = $data;
		
		return $this->sendResponse();
	}

	public function info() {
		$this->checkPlugin();
		$language = $this->load->language('account/recurring');

		if (isset($this->request->get['order_recurring_id'])) {
			$order_recurring_id = $this->request->get['order_recurring_id'];
		} else {
			$order_recurring_id = 0;
		}

		

		$this->load->model('account/recurring');

		$recurring_info = $this->model_account_recurring->getOrderRecurring($order_recurring_id);

		if ($recurring_info) {
			$this->document->setTitle($this->language->get('text_recurring'));

			$url = '';

			

			$data['order_recurring_id'] = $this->request->get['order_recurring_id'];
			$data['date_added'] = date($this->language->get('date_format_short'), strtotime($recurring_info['date_added']));

			if ($recurring_info['status']) {
				$data['status'] = $this->language->get('text_status_' . $recurring_info['status']);
			} else {
				$data['status'] = '';
			}

			$data['payment_method'] = $recurring_info['payment_method'];

			$data['order_id'] = $recurring_info['order_id'];
			$data['product_name'] = $recurring_info['product_name'];
			$data['product_quantity'] = $recurring_info['product_quantity'];
			$data['recurring_description'] = $recurring_info['recurring_description'];
			$data['reference'] = $recurring_info['reference'];

			// Transactions
			$data['transactions'] = array();

			$results = $this->model_account_recurring->getOrderRecurringTransactions($this->request->get['order_recurring_id']);

			foreach ($results as $result) {
				$data['transactions'][] = array(
					'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
					'type'       => $result['type'],
					'amount'     => $this->currency->format($result['amount'], $recurring_info['currency_code'])
				);
			}

			$data['order'] = $this->url->link('ocrestapi/account/order/info', 'order_id=' . $recurring_info['order_id'], true);
			$data['product'] = $this->url->link('ocrestapi/product/product', 'product_id=' . $recurring_info['product_id'], true);

			unset($language['backup']);
			$this->json['language'] = $language;
			$this->json['data'] = $data;
			
			return $this->sendResponse();
		} 
	}
}
