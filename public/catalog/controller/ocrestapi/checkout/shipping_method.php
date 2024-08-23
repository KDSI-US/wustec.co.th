<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiCheckoutShippingMethod extends ocrestapicontroller {
	public function index() {
		$this->checkPlugin();
		$language = $this->load->language('checkout/checkout');

		$this->load->model('account/address');
		$this->load->model('ocrestapi/ocrestapi');
		
		$get_token= $this->get_request_token();
		$token = explode(' ', $get_token)[1];
		$oauth_data = $this->model_ocrestapi_ocrestapi->get_auth_data($token);
		if(!isset($oauth_data['shipping_address_id'])) {
			$this->json['errors']['shipping_address'] = 'Shippping address is not selected.';
			$this->sendResponse();
		}
		$address_id = $oauth_data['shipping_address_id'];

		 
		$shipping_address = $this->model_account_address->getAddress($address_id);
		$shipping_methods = array();
		if ($shipping_address) {
			$this->load->model('setting/extension');
			$results = $this->model_setting_extension->getExtensions('shipping');
			foreach ($results as $result) {
				if ($this->config->get('shipping_' . $result['code'] . '_status')) {
					$this->load->model('extension/shipping/' . $result['code']);
					$quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($shipping_address);
					if ($quote) {
						$shipping_methods[$result['code']] = array(
							'title'      => $quote['title'],
							'quote'      => array_values($quote['quote']),
							'sort_order' => $quote['sort_order'],
							'error'      => $quote['error']
						);

					}
				}
			}

			$sort_order = array();

			foreach ($shipping_methods as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $shipping_methods);			
			
		}

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$json = [];
			if (isset($this->request->post['comment'])) {
				$data['comment'] = $this->request->post['comment'];
			} else {
				$data['comment'] = '';
			}

			if (!isset($this->request->post['shipping_method'])) {
				$json['shipping_method'] = $this->language->get('error_shipping');
			} else {			
				$shipping = explode('.', $this->request->post['shipping_method']);
				if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($shipping_methods[$shipping[0]]['quote'])) {
					$json['shipping_method'] = $this->language->get('error_shipping');
					$json['shipping_methods'] = $shipping_methods;
				}
			}
			$this->json['errors'] = $json;
			if (!$json) {
				$shipping_method = $shipping_methods[$shipping[0]]['quote'][0];


			$comment = strip_tags(isset($this->request->post['comment'])?$this->request->post['comment']:'');
				$shipping_method = array(
					'shipping_method' => $shipping_method,
					'comment' => $comment
				);
				$get_token= $this->get_request_token();
				$token = explode(' ', $get_token)[1];
				$this->add_shipping_method($shipping_method,$token);				
			}
			return $this->sendResponse();		
		}

		if (empty($shipping_methods)) {
			$this->json['errors']['error_warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
		} else {
			$data['shipping_methods'] = array_values($shipping_methods);
		}

		unset($language['language']);
		$this->json['language'] = $language;
		$this->json['data'] = isset($data)?$data:'';
		return $this->sendResponse();
	}

	public function add_shipping_method($shipping_method, $token) {
		$this->load->model('ocrestapi/ocrestapi');
		$oauth_data = $this->model_ocrestapi_ocrestapi->get_auth_data($token);
		$oauth_data['shipping_method'] = $shipping_method['shipping_method'];
		if(!empty($oauth_data['shipping_method'])){
			$this->session->data['shipping_method']=$oauth_data['shipping_method'];		 
		}
	}
	
}