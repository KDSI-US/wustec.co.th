<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiExtensionTotalShipping extends OcrestapiController {
	public function index() {
		$this->checkPlugin();

		if ($this->config->get('total_shipping_status') && $this->config->get('total_shipping_estimator') && $this->cart->hasShipping()) {
			$this->load->language('extension/total/shipping');
			if (isset($this->session->data['shipping_address']['country_id'])) {
				$data['country_id'] = $this->session->data['shipping_address']['country_id'];
			} else {
				$data['country_id'] = $this->config->get('config_country_id');
			}

			$this->load->model('localisation/country');

			$data['countries'] = $this->model_localisation_country->getCountries();

			if (isset($this->session->data['shipping_address']['zone_id'])) {
				$data['zone_id'] = $this->session->data['shipping_address']['zone_id'];
			} else {
				$data['zone_id'] = '';
			}

			if (isset($this->session->data['shipping_address']['postcode'])) {
				$data['postcode'] = $this->session->data['shipping_address']['postcode'];
			} else {
				$data['postcode'] = '';
			}
			
			$this->json['data']	= $data;

			$this->sendResponse();
		}
	}

	public function quote() {
		$this->checkPlugin();
		$this->load->language('extension/total/shipping');

		$json = array();

		if (!$this->cart->hasProducts()) {
			$json['error']['warning'] = $this->language->get('error_product');
		}

		if (!$this->cart->hasShipping()) {
			$json['error']['warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
		}

		if (!isset($this->request->post['country_id']) || $this->request->post['country_id'] == '') {
			$json['country_id'] = $this->language->get('error_country');
		}

		if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '') {
			$json['zone_id'] = $this->language->get('error_zone');
		}
		 

		$this->load->model('localisation/country');
		if(isset($this->request->post['country_id'])){
			$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);
		}
		
		if(isset($country_info)){

			if (!isset($this->request->post['postcode']) || $this->request->post['postcode'] == '') {
			$json['postcode'] = $this->language->get('error_postcode');
			}
			else{				

				if ($country_info && $country_info['postcode_required'] || (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
					$json['postcode'] = $this->language->get('error_postcode');
				}
			}
		}

		$this->json['errors'] = $json;

		if (empty($json)) {
			$this->tax->setShippingAddress($this->request->post['country_id'], $this->request->post['zone_id']);

			if ($country_info) {
				$country = $country_info['name'];
				$iso_code_2 = $country_info['iso_code_2'];
				$iso_code_3 = $country_info['iso_code_3'];
				$address_format = $country_info['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';
				$address_format = '';
			}

			$this->load->model('localisation/zone');

			$zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);

			if ($zone_info) {
				$zone = $zone_info['name'];
				$zone_code = $zone_info['code'];
			} else {
				$zone = '';
				$zone_code = '';
			}

			$data['shipping_address'] = array(
				'firstname'      => '',
				'lastname'       => '',
				'company'        => '',
				'address_1'      => '',
				'address_2'      => '',
				'postcode'       => $this->request->post['postcode'],
				'city'           => '',
				'zone_id'        => $this->request->post['zone_id'],
				'zone'           => $zone,
				'zone_code'      => $zone_code,
				'country_id'     => $this->request->post['country_id'],
				'country'        => $country,
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format
			);

			$quote_data = array();

			$this->load->model('setting/extension');

			$results = $this->model_setting_extension->getExtensions('shipping');

			foreach ($results as $result) {
				if ($this->config->get('shipping_' . $result['code'] . '_status')) {
					$this->load->model('extension/shipping/' . $result['code']);

					$quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($data['shipping_address']);

					if ($quote) {
						$quote_data[$result['code']] = array(
							'title'      => $quote['title'],
							'quote'      => array_values($quote['quote']),
							'sort_order' => $quote['sort_order'],
							'error'      => $quote['error']
						);
					}
				}
			}

			$sort_order = array();

			//$shipping_methods = $quote_data;

			if (!empty($quote_data)) {

				foreach ($quote_data as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $quote_data);

				$this->load->model('ocrestapi/ocrestapi');
				$get_token= $this->get_request_token();
				$token = explode(' ', $get_token)[1];
				$oauth_data = $this->model_ocrestapi_ocrestapi->get_auth_data($token);
				$this->session->data['shipping_quote_data'] = $quote_data;
				$this->json['data']['shipping_method'] = array_values($quote_data);
			} else {
				$json['error']['warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
			}
			
		}
		$this->sendResponse();
	}

	public function shipping() {
		$this->checkPlugin();
		$this->load->model('ocrestapi/ocrestapi');
		$this->load->language('extension/total/shipping');
		
		
		$json = array();

		if (!empty($this->request->post['shipping_method'])) {

			$shipping = explode('.', $this->request->post['shipping_method']);
			$get_token= $this->get_request_token();
			$token = explode(' ', $get_token)[1];
			$oauth_data = $this->model_ocrestapi_ocrestapi->get_auth_data($token);

			if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($oauth_data['shipping_quote_data'][$shipping[0]]['quote'][0])) {

				$this->json['errors']['shipping_method'] = $this->language->get('error_shipping');
			}
		
		} else {
			$this->json['errors']['shipping_method'] = $this->language->get('error_shipping');
		}

		if (empty($this->json['errors'])) {

			$shipping = explode('.', $this->request->post['shipping_method']);

			$this->session->data['shipping_method'] = $oauth_data['shipping_quote_data'][$shipping[0]]['quote'][0];

			$data['success'] = $this->language->get('text_success');
			$this->json['data'] = $data;
		}
		$this->sendResponse();
	}

	
}