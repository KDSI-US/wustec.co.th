<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiCheckoutPaymentMethod extends ocrestapicontroller {
	public function index() {
		$this->checkPlugin();
		$language = $this->load->language('checkout/checkout');
		$language = $this->load->language('extension/payment/bank_transfer');;
		
		$this->load->model('account/address');
		$this->load->model('ocrestapi/ocrestapi');
		
		$get_token= $this->get_request_token();
		$token = explode(' ', $get_token)[1];
		$oauth_data = $this->model_ocrestapi_ocrestapi->get_auth_data($token);
		
		if(!isset($oauth_data['payment_address_id'])) {
			$this->json['errors']['payment_address'] = 'Billing address is not selected.';
			$this->sendResponse();
		}

		$address_id = $oauth_data['payment_address_id'];
		$payment_address = $this->model_account_address->getAddress($address_id);
		if ($payment_address) {
			$this->load->model('setting/extension');

			if ($this->config->get('config_checkout_id')) {
				$this->load->model('catalog/information');
				$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));
				$language['text_agree'] = sprintf(strip_tags($this->language->get('error_agree')), $information_info['title']);
			}

			$totals = array();
			$taxes = $this->cart->getTaxes();
			$total = 0;	

			$total_data = array(
				'totals' => &$totals,
				'taxes'  => &$taxes,
				'total'  => &$total
			);
		
			// Display prices
			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$sort_order = array();
				$results = $this->model_setting_extension->getExtensions('total');


				foreach ($results as $key => $value) {
					$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
				}
				array_multisort($sort_order, SORT_ASC, $results);
				// echo '<pre>'; print_r($this->session->data);die;
				foreach ($results as $result) {
					if ($this->config->get('total_' . $result['code'] . '_status')) {
						$this->load->model('extension/total/' . $result['code']);
						// We have to put the totals in an array so that they pass by reference.
						$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
					}
				}

				$sort_order = array();
				foreach ($totals as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}
				array_multisort($sort_order, SORT_ASC, $totals);
			}

			$data['totals'] = array();
			foreach ($totals as $total) {
				$data['totals'][] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value'], $this->config->get('config_currency'))
				);
	        	$data['total'] = $this->currency->format($total['value'], $this->config->get('config_currency'));
			}

			$payment_methods = array();		

			$results = $this->model_setting_extension->getExtensions('payment');
			$recurring = $this->cart->hasRecurringProducts();

			foreach ($results as $result) {
				if ($this->config->get('payment_' . $result['code'] . '_status')) {
					$this->load->model('extension/payment/' . $result['code']);
					$method = $this->{'model_extension_payment_' . $result['code']}->getMethod($payment_address, $total);
					if ($method) {
						if ($recurring) {
							if (property_exists($this->{'model_extension_payment_' . $result['code']}, 'recurringPayments') && $this->{'model_extension_payment_' . $result['code']}->recurringPayments()) {
								$payment_methods[$result['code']] = $method;
							}
						} else {
							$payment_methods[$result['code']] = $method;
						}
					}
				}
			}
			$sort_order = array();
			foreach ($payment_methods as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}
		
			array_multisort($sort_order, SORT_ASC, $payment_methods);
			$data['payment_methods'] = array_values($payment_methods);
		}		

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$json = [];
			$get_token= $this->get_request_token();
			$token = explode(' ', $get_token)[1];
			
			if (!isset($this->request->post['payment_method'])) {
				$json['payment_method'] = $this->language->get('error_payment');
			} else if (!isset($payment_methods[$this->request->post['payment_method']])) {
				$json['payment_method'] = $this->language->get('error_payment');
			}

			if ($this->config->get('config_checkout_id')) {
				$this->load->model('catalog/information');
				$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));
				if ($information_info && !isset($this->request->post['agree'])) {
					$json['agree'] = sprintf($this->language->get('error_agree'), $information_info['title']);
				}else if(empty($this->request->post['agree']) && $this->request->post['agree']==0){
					$json['agree'] = sprintf($this->language->get('error_agree'), $information_info['title']);
				}
			}

			$this->json['errors'] = $json;
			if (empty($json)) {
				$payment_method = $payment_methods[$this->request->post['payment_method']];
				$this->add_payment_method($payment_method,$token);
				if($payment_method['code'] != 'cod') {
					$this->json['data']['payment'] = $this->load->controller('ocrestapi/extension/payment/' . $this->request->post['payment_method']);
				}
			}
			return $this->sendResponse();
		}
		unset($language['backup']);
		$this->json['language'] = $language;
		$this->json['data'] = $data;
		return $this->sendResponse();
		
	}

	public function add_payment_method($payment_method,$token)
	{
		$this->load->model('ocrestapi/ocrestapi');
		$oauth_data = $this->model_ocrestapi_ocrestapi->get_auth_data($token);
		$oauth_data['payment_method'] =$payment_method;
		$this->session->data['payment_method']=$oauth_data['payment_method'];
	}
	
}
