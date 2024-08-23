<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiCheckoutShippingAddress extends ocrestapicontroller {
	public function index() {
		$this->checkPlugin();
		$language = $this->load->language('checkout/checkout');
		unset($language['backup']);
		$this->json['language'] = $language;

		$this->load->model('account/address');
		$this->load->model('account/custom_field');
		$this->load->model('ocrestapi/ocrestapi');
		
		$get_token= $this->get_request_token();
		$token = explode(' ', $get_token)[1];
		
		$voucher_data = $this->model_ocrestapi_ocrestapi->get_voucher_data($token);
		
		// Validate cart has products and has stock
		if ((!$this->cart->hasProducts() && empty($voucher_data->vouchers))) {
			$this->json['status'] = false;
			$this->sendResponse();
		}

		if((!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$this->json['status'] = false;
			// $this->json['errors']['stock'] = "fdffd";
			$this->sendResponse();
		}



		$results = $this->model_account_address->getAddresses();
		$default_addressid = $this->customer->getAddressId();
        $data['default_address'] = $default_addressid;

		foreach ($results as $result) {
			if ($result['address_format']) {
				$format = $result['address_format'];
			} else {
				$format = '{firstname} {lastname}'."\n".'{company}'."\n".'{address_1}'."\n".'{address_2}'."\n".'{city} {postcode}'."\n".'{zone}'."\n".'{country}';
			}

			$find = array(
				'{firstname}',
				'{lastname}',
				'{company}',
				'{address_1}',
				'{address_2}',
				'{city}',
				'{postcode}',
				'{zone}',
				'{zone_code}',
				'{country}',
			);

			$replace = array(
				'firstname' => $result['firstname'],
				'lastname'  => $result['lastname'],
				'company'   => $result['company'],
				'address_1' => $result['address_1'],
				'address_2' => $result['address_2'],
				'city'      => $result['city'],
				'postcode'  => $result['postcode'],
				'zone'      => $result['zone'],
				'zone_code' => $result['zone_code'],
				'country'   => $result['country'],
			);
          
			$data['addresses'][] = array(
				'address_id' => $result['address_id'],
				'firstname'  => $result['firstname'],
				'lastname'   => $result['lastname'],
				'company'    => $result['company'],
				'address_1'  => $result['address_1'],
				'address_2'  => $result['address_2'],
				'city'       => $result['city'],
				'postcode'   => $result['postcode'],
				'default'    => ($default_addressid==$result['address_id'])?1:0,
				'zone'       => $result['zone'],
				'zone_code'  => $result['zone_code'],
				'country'    => $result['country'],
				'address'    => str_replace(array("\r\n", "\r", "\n"), ', ', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), ', ', trim(str_replace($find, $replace, $format))))
			);
		}


		// Custom Fields
		$data['custom_fields'] = array();
		$custom_fields = $this->model_account_custom_field->getCustomFields($this->config->get('config_customer_group_id'));
		foreach ($custom_fields as $custom_field) {
			if ($custom_field['location'] == 'address') {
				$data['custom_fields'][] = $custom_field;
			}
		}		
		$this->json['data'] = $data;
		return $this->sendResponse();
	}

	public function save() {
		$this->checkPlugin();
		$this->load->language('checkout/checkout');
		$this->load->language('ocrestapi/message');
		$this->load->model('ocrestapi/ocrestapi');
		$json = array();
		
		$this->load->model('account/address');
			
			 
				if (empty($this->request->post['address_id'])) {
					$json['address'] = $this->language->get('error_address');
				} elseif (!in_array($this->request->post['address_id'], array_keys($this->model_account_address->getAddresses()))) {					 
					$json['address_id'] = $this->language->get('error_address');
				}
				$this->json['errors'] = $json;
				if (empty($json)) {
					
					$get_token= $this->get_request_token();
					$token = explode(' ', $get_token)[1];
			 		$this->add_shipping_address_id($this->request->post['address_id'],$token);
					$this->json['data'] = [];

				}
		return $this->sendResponse();
		
	}

	public function add_shipping_address_id($address_id,$token) {
		
		$this->load->model('ocrestapi/ocrestapi');
		$oauth_data = $this->model_ocrestapi_ocrestapi->get_auth_data($token);
	
		 $oauth_data['shipping_address_id'] = $address_id;
		if(!empty($oauth_data['shipping_address_id'])){	
		$this->session->data['shipping_address_id']=$oauth_data['shipping_address_id'];
		 
		}
	}
}