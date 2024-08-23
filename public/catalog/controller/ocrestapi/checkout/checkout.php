<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiCheckoutCheckout extends OcrestapiController {
	public function index() {
		$this->checkPlugin();
		$language = $this->load->language('checkout/checkout');
		$language = $this->load->language('ocrestapi/message');

		$this->load->model('ocrestapi/ocrestapi');
		$this->load->model('account/address');
		
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
 
		// $shipping_address = $voucher_data->shipping_address;
	
		// Validate minimum quantity requirements.
		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$product_total = 0;
			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$this->response->redirect($this->url->link('checkout/cart'));
			}
		}

		$this->load->model('tool/image');
		$this->load->model('tool/upload');
		$data['products'] = array();

		foreach ($this->cart->getProducts() as $product) {
			$option_data = array();

			foreach ($product['option'] as $option) {
				if ($option['type'] != 'file') {
					$value = $option['value'];
				} else {
					$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

					if ($upload_info) {
						$value = $upload_info['name'];
					} else {
						$value = '';
					}
				}

				$option_data[] = array(
					'name'  => $option['name'],
					'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
				);
			}

			$recurring = '';

			if ($product['recurring']) {
				$frequencies = array(
					'day'        => $this->language->get('text_day'),
					'week'       => $this->language->get('text_week'),
					'semi_month' => $this->language->get('text_semi_month'),
					'month'      => $this->language->get('text_month'),
					'year'       => $this->language->get('text_year'),
				);

				if ($product['recurring']['trial']) {
					$recurring = sprintf($this->language->get('text_trial_description'), $this->currency->format($this->tax->calculate($product['recurring']['trial_price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency')), $product['recurring']['trial_cycle'], $frequencies[$product['recurring']['trial_frequency']], $product['recurring']['trial_duration']) . ' ';
				}

				if ($product['recurring']['duration']) {
					$recurring .= sprintf($this->language->get('text_payment_description'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency')), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
				} else {
					$recurring .= sprintf($this->language->get('text_payment_cancel'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency')), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
				}
			}

			$data['products'][] = array(
				'cart_id'    		=> $product['cart_id'],
				'product_id' 		=> $product['product_id'],
				'name'      		=> $product['name'],
				'model'      		=> $product['model'],
				'option'     		=> $option_data,
				'recurring'  		=> $recurring,
				'quantity'   		=> $product['quantity'],
				'subtract'   		=> $product['subtract'],
				'price'      		=> $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency')),
				'total'      		=> $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'], $this->config->get('config_currency'))
			);
		}

		$this->load->model('ocrestapi/ocrestapi');

		// Gift Voucher
		// print_r($voucher_data->vouchers); die;
		// $data['vouchers'] = array();
		// $get_token= $this->get_request_token();
		// $token = explode(' ', $get_token)[1];
		// $voucher_data = $this->model_ocrestapi_ocrestapi->get_voucher_data($token);

		// if (!empty($voucher_data)) {
		// 	if(isset($voucher_data->voucher)){
		// 		 echo count($voucher_data); die;

		// 		foreach($voucher_data  as  $key=>$vouchers) {
		// 			print_r($vouchers->$key); 
		// 			// $data['vouchers'][] = array(
		// 			// 	'voucher'         => $vouchers['voucher'],
		// 			// 	'coupon' =>$vouchers['coupon']
		// 			// );
		// 		}
		// 		die;
				 
		// 	}
		// }

		$this->load->model('setting/extension');

		$totals = array();
		$taxes = $this->cart->getTaxes();
		$total = 0;	
		
		// Because __call can not keep var references so we put them into an array. 			
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
		
		
		$data['shipping_required'] = $this->cart->hasShipping();
		$data['addresses'] = [];

		$results = $this->model_account_address->getAddresses();
        $customer_id = $this->customer->getId();
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
		if(!empty($default_addressid)) {
			// Payment Address
			$payment_address = $this->model_account_address->getAddress($default_addressid);
			if (isset($payment_address)) {
				$method_data = array();

				$this->load->model('setting/extension');

				$results = $this->model_setting_extension->getExtensions('payment');
				$recurring = $this->cart->hasRecurringProducts();

				foreach ($results as $result) {
					if ($this->config->get('payment_' . $result['code'] . '_status')) {
						$this->load->model('extension/payment/' . $result['code']);
						$method = $this->{'model_extension_payment_' . $result['code']}->getMethod($payment_address, $total);
						if ($method) {
							if ($recurring) {
								if (property_exists($this->{'model_extension_payment_' . $result['code']}, 'recurringPayments') && $this->{'model_extension_payment_' . $result['code']}->recurringPayments()) {
									$method_data[] = $method;
								}
							} else {
								$method_data[] = $method;
							}
						}
					}
				}
				$sort_order = array();
				foreach ($method_data as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}
			
				array_multisort($sort_order, SORT_ASC, $method_data);
				$data['payment_methods'] = $method_data;
			}

			// Shipping address
			$shipping_address = $this->model_account_address->getAddress($default_addressid);
			if (isset($shipping_address)) {
				$method_data = array();

				$this->load->model('setting/extension');

				$results = $this->model_setting_extension->getExtensions('shipping');
				foreach ($results as $result) {
					if ($this->config->get('shipping_' . $result['code'] . '_status')) {
						$this->load->model('extension/shipping/' . $result['code']);

						$quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($shipping_address);
						if ($quote) {
							$method_data[] = array(
								'title'      => $quote['title'],
								'quote'      => array_values($quote['quote']),
								'sort_order' => $quote['sort_order'],
								'error'      => $quote['error']
							);

						}
					}
				}

				$sort_order = array();

				foreach ($method_data as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $method_data);
				$data['shipping_methods'] = $method_data;
			}
		}
		unset($language['backup']);
		$this->json['language'] = $language;
		$this->json['data'] = $data;
		return $this->sendResponse();
	}

	
	
}