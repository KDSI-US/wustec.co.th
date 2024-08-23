<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiCheckoutConfirm extends ocrestapicontroller {
	public function index() {
		$this->checkPlugin();
		$this->load->model('ocrestapi/ocrestapi');
		$this->load->language('ocrestapi/message');
		$this->load->language('tool/upload');
 
		$get_token= $this->get_request_token();
		 
		$token = explode(' ', $get_token)[1];
		 
		$voucher_data = $this->model_ocrestapi_ocrestapi->get_voucher_data($token);

		if ((!$this->cart->hasProducts() && empty($voucher_data->vouchers))) {
			$this->json['status'] = false;
			$this->sendResponse();
		}
		

		if((!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$this->json['status'] = false;
			// $this->json['errors']['stock'] = "fdffd";
			$this->sendResponse();
		}

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
		$data['vouchers'] = array();
		$get_token= $this->get_request_token();
		$token = explode(' ', $get_token)[1];

		$voucher_data = $this->model_ocrestapi_ocrestapi->get_voucher_data($token);
		if (!empty($voucher_data)) {
			if(isset($voucher_data->vouchers)){
				foreach ($voucher_data->vouchers as $key => $voucher) {
					$data['vouchers'][] = array(
						'key'         => $key,
						'description' => sprintf($this->language->get('text_for'), $this->currency->format($voucher->amount, $this->config->get('config_currency')), $voucher->to_name),
						'amount'      => $this->currency->format($voucher->amount, $this->config->get('config_currency'))
					);
				}
			}
		}

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

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {			 


			if(!isset($this->request->post['confirm']) || empty($this->request->post['confirm'])) {
				$this->json['error']['warning'] = $this->language->get('error_chekout_confirm'); 
				$this->sendResponse();
			}
			$redirect = '';
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
					$redirect = $this->url->link('checkout/cart');
					break;
				}
			}

			if (!$redirect) {
				$order_data = array();
				$totals = array();
				$taxes = $this->cart->getTaxes();
				$total = 0;

				// Because __call can not keep var references so we put them into an array.
				$total_data = array(
					'totals' => &$totals,
					'taxes'  => &$taxes,
					'total'  => &$total
				);

				$this->load->model('setting/extension');
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

				$order_data['totals'] = $totals;

				$language = $this->load->language('checkout/checkout');
				unset($language['language']);
				$this->json['language'] = $language;
				$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
				$order_data['store_id'] = $this->config->get('config_store_id');
				$order_data['store_name'] = $this->config->get('config_name');

				if ($order_data['store_id']) {
					$order_data['store_url'] = $this->config->get('config_url');
				} else {
					if ($this->request->server['HTTPS']) {
						$order_data['store_url'] = HTTPS_SERVER;
					} else {
						$order_data['store_url'] = HTTP_SERVER;
					}
				}
			
				$this->load->model('account/customer');

				if ($this->customer->isLogged()) {
					$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
					$order_data['customer_id'] = $this->customer->getId();
					$order_data['customer_group_id'] = $customer_info['customer_group_id'];
					$order_data['firstname'] = $customer_info['firstname'];
					$order_data['lastname'] = $customer_info['lastname'];
					$order_data['email'] = $customer_info['email'];
					$order_data['telephone'] = $customer_info['telephone'];
					$order_data['custom_field'] = json_decode($customer_info['custom_field'], true);
				} 
				$this->load->model('account/address');
				$oauth_data = $this->model_ocrestapi_ocrestapi->get_auth_data($token);
				// print_r($oauth_data); die;
				if(isset($oauth_data['payment_address_id'])){
					$address_id = $oauth_data['payment_address_id'];
					$payment_address = $this->model_account_address->getAddress($address_id);
				}
				if(isset($payment_address)){
					$order_data['payment_firstname']   	= $payment_address['firstname'];
					$order_data['payment_lastname']    	= $payment_address['lastname'];
					$order_data['payment_company']     	= $payment_address['company'];
					$order_data['payment_address_1']   	= $payment_address['address_1'];
					$order_data['payment_address_2']   	= $payment_address['address_2'];
					$order_data['payment_city']        	= $payment_address['city'];
					$order_data['payment_postcode']    	= $payment_address['postcode'];
					$order_data['payment_zone']        	= $payment_address['zone'];
					$order_data['payment_zone_id']     	= $payment_address['zone_id'];
					$order_data['payment_country']     	= $payment_address['country'];
					$order_data['payment_country_id']  	= $payment_address['country_id'];
					$order_data['payment_address_format'] 	= $payment_address['address_format'];
					$order_data['payment_custom_field'] 	= (isset($payment_address['custom_field']) ? $payment_address['custom_field'] : array());
				}else{
					$order_data['payment_firstname']   	= '';
					$order_data['payment_lastname']    	= '';
					$order_data['payment_company']     	= '';
					$order_data['payment_address_1']   	= '';
					$order_data['payment_address_2']   	= '';
					$order_data['payment_city']        	= '';
					$order_data['payment_postcode']    	= '';
					$order_data['payment_zone']        	= '';
					$order_data['payment_zone_id']     	= '';
					$order_data['payment_country']     	= '';
					$order_data['payment_country_id']  	= '';
					$order_data['payment_address_format'] 	= '';
				}
			
				$oauth_data = $this->model_ocrestapi_ocrestapi->get_auth_data($token);
				
				if (isset($oauth_data['payment_method']['title'])) {
					$order_data['payment_method'] = $oauth_data['payment_method']['title'];
				} else {
					$order_data['payment_method'] = '';
				}

				if (isset($oauth_data['payment_method']['code'])) {
					$order_data['payment_code'] = $oauth_data['payment_method']['code'];
				} else {
					$order_data['payment_code'] = '';
				}
			
				$oauth_data = $this->model_ocrestapi_ocrestapi->get_auth_data($token);
				if ($this->cart->hasShipping() && isset($oauth_data['shipping_address_id'])) {
					$address_id = $oauth_data['shipping_address_id'];
					$shipping_address = $this->model_account_address->getAddress($address_id);

					if(isset($shipping_address)){
						$order_data['shipping_firstname']   	= $shipping_address['firstname'];
						$order_data['shipping_lastname']    	= $shipping_address['lastname'];
						$order_data['shipping_company']     	= $shipping_address['company'];
						$order_data['shipping_address_1']   	= $shipping_address['address_1'];
						$order_data['shipping_address_2']   	= $shipping_address['address_2'];
						$order_data['shipping_city']        	= $shipping_address['city'];
						$order_data['shipping_postcode']    	= $shipping_address['postcode'];
						$order_data['shipping_zone']        	= $shipping_address['zone'];
						$order_data['shipping_zone_id']     	= $shipping_address['zone_id'];
						$order_data['shipping_country']     	= $shipping_address['country'];
						$order_data['shipping_country_id']  	= $shipping_address['country_id'];
						$order_data['shipping_address_format']  = $shipping_address['address_format'];
						$order_data['shipping_custom_field']    = (isset($shipping_address['custom_field']) ? $shipping_address['custom_field'] : array());
					}	

					if(isset($oauth_data['shipping_method'])){
						if (isset($oauth_data['shipping_method']['title'])) {
							$order_data['shipping_method'] = $oauth_data['shipping_method']['title'];
						} else {
							$order_data['shipping_method'] = '';
						}

						if (isset($oauth_data['shipping_method']['code'])) {
							$order_data['shipping_code'] = $oauth_data['shipping_method']['code'];
						} else {
							$order_data['shipping_code'] = '';
						}
					}
				} else {
					$order_data['shipping_firstname'] = '';
					$order_data['shipping_lastname'] = '';
					$order_data['shipping_company'] = '';
					$order_data['shipping_address_1'] = '';
					$order_data['shipping_address_2'] = '';
					$order_data['shipping_city'] = '';
					$order_data['shipping_postcode'] = '';
					$order_data['shipping_zone'] = '';
					$order_data['shipping_zone_id'] = '';
					$order_data['shipping_country'] = '';
					$order_data['shipping_country_id'] = '';
					$order_data['shipping_address_format'] = '';
					$order_data['shipping_custom_field'] = array();
					$order_data['shipping_method'] = '';
					$order_data['shipping_code'] = '';
				}

				$order_data['products'] = array();
				foreach ($this->cart->getProducts() as $product) {
					$option_data = array();

					foreach ($product['option'] as $option) {
						$option_data[] = array(
							'product_option_id'       => $option['product_option_id'],
							'product_option_value_id' => $option['product_option_value_id'],
							'option_id'               => $option['option_id'],
							'option_value_id'         => $option['option_value_id'],
							'name'                    => $option['name'],
							'value'                   => $option['value'],
							'type'                    => $option['type']
						);
					}

					$order_data['products'][] = array(
						'product_id' => $product['product_id'],
						'name'       => $product['name'],
						'model'      => $product['model'],
						'option'     => $option_data,
						'download'   => $product['download'],
						'quantity'   => $product['quantity'],
						'subtract'   => $product['subtract'],
						'price'      => $product['price'],
						'total'      => $product['total'],
						'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
						'reward'     => $product['reward']
					);
				}

				// Gift Voucher
				$order_data['vouchers'] = array();
				if (isset($voucher_data)) {
					if(!empty($voucher_data->vouchers)){
						foreach ($voucher_data->vouchers as $voucher) {
							$order_data['vouchers'][] = array(
								'description'      => sprintf($this->language->get('text_for'), $this->currency->format($voucher->amount, $this->config->get('config_currency')), $voucher->amount),
								'code'             => token(10),
								'to_name'          => $voucher->to_name,
								'to_email'         => $voucher->to_email,
								'from_name'        => $voucher->from_name,
								'from_email'       => $voucher->from_email,
								'voucher_theme_id' => $voucher->voucher_theme_id,
								'message'          => $voucher->message,
								'amount'           => $voucher->amount
							);
						}
					}
				}

				$order_data['comment'] = '';
				$order_data['total'] = $total_data['total'];
				if (isset($this->request->cookie['tracking'])) {

					$order_data['tracking'] = $this->request->cookie['tracking'];

					$subtotal = $this->cart->getSubTotal();

					// Affiliate
					$affiliate_info = $this->model_account_customer->getAffiliateByTracking($this->request->cookie['tracking']);

					if ($affiliate_info) {
						$order_data['affiliate_id'] = $affiliate_info['customer_id'];
						$order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
					} else {
						$order_data['affiliate_id'] = 0;
						$order_data['commission'] = 0;
					}

					// Marketing
					$this->load->model('checkout/marketing');

					$marketing_info = $this->model_checkout_marketing->getMarketingByCode($this->request->cookie['tracking']);

					if ($marketing_info) {
						$order_data['marketing_id'] = $marketing_info['marketing_id'];
					} else {
						$order_data['marketing_id'] = 0;
					}
				} else {
					$order_data['affiliate_id'] = 0;
					$order_data['commission'] = 0;
					$order_data['marketing_id'] = 0;
					$order_data['tracking'] = '';
				}

				$order_data['language_id'] = $this->config->get('config_language_id');
				$order_data['currency_id'] = $this->currency->getId($this->config->get('config_currency'));
				$order_data['currency_code'] = $this->session->data['currency'];
				$order_data['currency_value'] = $this->currency->getValue($this->config->get('config_currency'));
				$order_data['ip'] = $this->request->server['REMOTE_ADDR'];

				if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
					$order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
				} elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
					$order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
				} else {
					$order_data['forwarded_ip'] = '';
				}

				if (isset($this->request->server['HTTP_USER_AGENT'])) {
					$order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
				} else {
					$order_data['user_agent'] = '';
				}

				if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
					$order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
				} else {
					$order_data['accept_language'] = '';
				}

				$this->load->model('checkout/order');
				
				$data['order_id'] = $this->model_checkout_order->addOrder($order_data);

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
						'cart_id'    => $product['cart_id'],
						'product_id' => $product['product_id'],
						'name'       => $product['name'],
						'model'      => $product['model'],
						'option'     => $option_data,
						'recurring'  => $recurring,
						'quantity'   => $product['quantity'],
						'subtract'   => $product['subtract'],
						'price'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency')),
						'total'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'], $this->config->get('config_currency')),
						'href'       => $this->url->link('product/product', 'product_id=' . $product['product_id'])
					);
				}
				$this->load->model('ocrestapi/ocrestapi');
				$this->load->language('ocrestapi/message');
			
				// Gift Voucher
				$data['vouchers'] = array();

				if (!empty($voucher_data)) {
					if(isset($voucher_data->vouchers)){
						foreach ($voucher_data->vouchers as $voucher) {
							$data['vouchers'][] = array(
								'description' => sprintf($this->language->get('text_for'), $this->currency->format($voucher->amount, $this->config->get('config_currency')), $voucher->amount),
								'amount'      => $this->currency->format($voucher->amount, $this->config->get('config_currency'))

							);
						}
					}
				}

				///totals///
				$data['totals'] = array();

				foreach ($order_data['totals'] as $total) {
					$data['totals'][] = array(
						'title' => $total['title'],
						'text'  => $this->currency->format($total['value'], $this->config->get('config_currency'))
					);
				}
			
				if ($oauth_data['payment_method']['code'] == 'cod') {
					$this->load->model('checkout/order');

					$this->model_checkout_order->addOrderHistory($data['order_id'], $this->config->get('payment_cod_order_status_id'));
					if (isset($data['order_id'])) {
						$this->cart->clear();

						$this->model_ocrestapi_ocrestapi->clear_voucher($token);
						
					}
				}else if ($oauth_data['payment_method']['code'] == 'cheque') {
					$this->load->language('extension/payment/cheque');
					$this->load->model('checkout/order');
					$comment  = $this->language->get('text_payable') . "\n";
					$comment .= $this->config->get('payment_cheque_payable') . "\n\n";
					$comment .= $this->language->get('text_address') . "\n";
					$comment .= $this->config->get('config_address') . "\n\n";
					$comment .= $this->language->get('text_payment') . "\n";

					$this->model_checkout_order->addOrderHistory($data['order_id'], $this->config->get('payment_cheque_order_status_id'), $comment, true);
					if (isset($data['order_id'])) {
						$this->cart->clear();

						$this->model_ocrestapi_ocrestapi->clear_voucher($token);

					}
				}else if ($oauth_data['payment_method']['code'] == 'bank_transfer') {
					$this->load->language('extension/payment/bank_transfer');

					$this->load->model('checkout/order');

					$comment  = $this->language->get('text_instruction') . "\n\n";
					$comment .= $this->config->get('payment_bank_transfer_bank' . $this->config->get('config_language_id')) . "\n\n";
					$comment .= $this->language->get('text_payment');

					$this->model_checkout_order->addOrderHistory($data['order_id'], $this->config->get('payment_bank_transfer_order_status_id'), $comment, true);
					if (isset($data['order_id'])) {
						$this->cart->clear();

						$this->model_ocrestapi_ocrestapi->clear_voucher($token);
						
					}
				}
				$this->json['data'] = $data;
				return $this->sendResponse();
			} else {
				$data['redirect'] = $redirect;
			}
		}
		$this->json['data'] = $data;
		return $this->sendResponse();
	}
}
