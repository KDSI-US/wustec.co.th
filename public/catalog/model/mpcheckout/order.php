<?php
class ModelMpcheckoutOrder extends Model {
	public function getOrderDetails($order_id) {
		// Load the language for any mails that might be required to be sent out
		$this->load->model('checkout/order');
		
		$this->load->model('catalog/product');
		
		$this->load->model('tool/image');
		
		$order_info = $this->model_checkout_order->getOrder($order_id);
		
		if($order_info) {
			$this->language->load('mail/order_add');
			$this->language->load('mpcheckout/checkout');
			
			$order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_info['order_status_id'] . "' AND language_id = '" . (int)$order_info['language_id'] . "'");

			if ($order_status_query->num_rows) {
				$order_status = $order_status_query->row['name'];
			} else {
				$order_status = '';
			}
			
			$data = array();
			
			$data['text_order_detail'] = $this->language->get('text_order_detail');
			$data['text_instruction'] = $this->language->get('text_instruction');
			$data['text_order_id'] = $this->language->get('text_order_id');
			$data['text_date_added'] = $this->language->get('text_date_added');
			$data['text_payment_method'] = $this->language->get('text_payment_method');
			$data['text_shipping_method'] = $this->language->get('text_shipping_method');
			$data['text_email'] = $this->language->get('text_email');
			$data['text_telephone'] = $this->language->get('text_telephone');
			$data['text_ip'] = $this->language->get('text_ip');
			$data['text_order_status'] = $this->language->get('text_order_status');
			$data['text_payment_address'] = $this->language->get('text_payment_address');
			$data['text_shipping_address'] = $this->language->get('text_shipping_address');
			$data['text_image'] = $this->language->get('text_image');
			$data['text_product'] = $this->language->get('text_product');
			$data['text_model'] = $this->language->get('text_model');
			$data['text_quantity'] = $this->language->get('text_quantity');
			$data['text_price'] = $this->language->get('text_price');
			$data['text_total'] = $this->language->get('text_total');
			$data['text_footer'] = $this->language->get('text_footer');
			
			$data['order_id'] = $order_id;
			$data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));
			$data['payment_method'] = $order_info['payment_method'];
			$data['shipping_method'] = $order_info['shipping_method'];
			$data['email'] = $order_info['email'];
			$data['telephone'] = $order_info['telephone'];
			$data['ip'] = $order_info['ip'];
			$data['order_status'] = $order_status;

			if ($order_info['payment_address_format']) {
				$format = $order_info['payment_address_format'];
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
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
				'{country}'
			);

			$replace = array(
				'firstname' => $order_info['payment_firstname'],
				'lastname'  => $order_info['payment_lastname'],
				'company'   => $order_info['payment_company'],
				'address_1' => $order_info['payment_address_1'],
				'address_2' => $order_info['payment_address_2'],
				'city'      => $order_info['payment_city'],
				'postcode'  => $order_info['payment_postcode'],
				'zone'      => $order_info['payment_zone'],
				'zone_code' => $order_info['payment_zone_code'],
				'country'   => $order_info['payment_country']
			);

			$data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

			if ($order_info['shipping_address_format']) {
				$format = $order_info['shipping_address_format'];
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
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
				'{country}'
			);

			$replace = array(
				'firstname' => $order_info['shipping_firstname'],
				'lastname'  => $order_info['shipping_lastname'],
				'company'   => $order_info['shipping_company'],
				'address_1' => $order_info['shipping_address_1'],
				'address_2' => $order_info['shipping_address_2'],
				'city'      => $order_info['shipping_city'],
				'postcode'  => $order_info['shipping_postcode'],
				'zone'      => $order_info['shipping_zone'],
				'zone_code' => $order_info['shipping_zone_code'],
				'country'   => $order_info['shipping_country']
			);

			$data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

			$this->load->model('tool/upload');

			// Products
			$data['products'] = array();

			$order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
			
			foreach ($order_product_query->rows as $product) {
				$option_data = array();

				$order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$product['order_product_id'] . "'");

				foreach ($order_option_query->rows as $option) {
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
				
				$product_info = $this->model_catalog_product->getProduct($product['product_id']);
				
				$width = (!empty($module_info['order_success_image_product_width']) ? $module_info['order_success_image_product_width'] : 40);
				
				$height = (!empty($module_info['order_success_image_product_height']) ? $module_info['order_success_image_product_height'] : 40);
				
				if (!empty($product_info['image'])) {
					$image = $this->model_tool_image->resize($product_info['image'], $width, $height);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $width, $height);
				}
				
				$data['products'][] = array(
					'image'     => $image,
					'name'     => $product['name'],
					'model'    => $product['model'],
					'option'   => $option_data,
					'quantity' => $product['quantity'],
					'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
					'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
				);
			}

			// Vouchers
			$data['vouchers'] = array();

			$order_voucher_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");

			foreach ($order_voucher_query->rows as $voucher) {
				$data['vouchers'][] = array(
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']),
				);
			}

			// Order Totals
			$order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order ASC");

			foreach ($order_total_query->rows as $total) {
				$data['totals'][] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
				);
			}
				
			return $this->load->view('mpcheckout/success_details', $data);
		}
	}
	
	public function setDefaultShippingMethodInSession() {
		$custom_array = array();
		if (isset($this->session->data['shipping_address'])) {
			// Shipping Methods
			$method_data = array();

			$this->load->model('setting/extension');

			$results = $this->model_setting_extension->getExtensions('shipping');
			foreach ($results as $result) {
				if ($this->config->get('shipping_' . $result['code'] . '_status')) {
					$this->load->model('extension/shipping/' . $result['code']);
					$quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);

					if ($quote) {
						$method_data[$result['code']] = array(
							'title'      => $quote['title'],
							'quote'      => $quote['quote'],
							'sort_order' => $quote['sort_order'],
							'error'      => $quote['error']
						);

						foreach ($quote['quote'] as $quote_value) {
							$custom_array[] = array(
								'code' 		 => $quote_value['code'],
								'sort_order' => $quote['sort_order'],
							);
						}
					}
				}
			}

			$sort_order = array();

			foreach ($method_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $method_data);

			$sort_order = array();
			foreach ($custom_array as $key => $custom_arra) {
				$sort_order[$key] = $custom_arra['sort_order'];
			}
			array_multisort($sort_order, SORT_ASC, $custom_array);

			$this->session->data['shipping_methods'] = $method_data;
		}

		// set first code
		if($custom_array) {
			$shipping = explode('.', $custom_array[0]['code']);
			if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {				
			} else{
				$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
			}
		}
	}

	public function setFirstShippingMethodInSession() {
		$custom_array = array();
		if (isset($this->session->data['shipping_address'])) {
			// Shipping Methods
			$method_data = array();

			$this->load->model('setting/extension');

			$results = $this->model_setting_extension->getExtensions('shipping');
			foreach ($results as $result) {
				if ($this->config->get('shipping_' . $result['code'] . '_status')) {
					$this->load->model('extension/shipping/' . $result['code']);
					$quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);

					if ($quote) {						
						$method_data[$result['code']] = array(
							'title'      => $quote['title'],
							'quote'      => $quote['quote'],
							'sort_order' => $quote['sort_order'],
							'error'      => $quote['error']
						);

						foreach ($quote['quote'] as $quote_value) {
							$custom_array[] = $quote_value['code'];
						}
					}
				}
			}

			$sort_order = array();

			foreach ($method_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $method_data);

			$this->session->data['shipping_methods'] = $method_data;
		}

		// set first code
		if (isset($this->session->data['shipping_method']['code'])) {
			if($custom_array && !in_array($this->session->data['shipping_method']['code'], $custom_array)) {
				// again set Shipping session (code, cost) when prevoius code does not match
				if(isset($custom_array[0])) {
					$shipping = explode('.', $custom_array[0]);
					if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {				
					} else{
						$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
					}
				}
			} else {
				// again set Shipping session (code, cost) everytime
				$shipping = explode('.', $this->session->data['shipping_method']['code']);
				if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
				} else{
					$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
				}
			}
		}
	}

	public function setDefaultPaymentMethodInSession() {
		$custom_array = array();
		if (isset($this->session->data['payment_address'])) {
			// Totals
			$totals = array();
			$taxes = $this->cart->getTaxes();
			$total = 0;

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
					$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
				}
			}
			

			// Payment Methods
			$method_data = array();

			$this->load->model('setting/extension');

			$results = $this->model_setting_extension->getExtensions('payment');

			$recurring = $this->cart->hasRecurringProducts();

			foreach ($results as $result) {
				if ($this->config->get('payment_' . $result['code'] . '_status')) {
					if( VERSION > '2.2.0.0')  {
						$this->load->model('extension/payment/' . $result['code']);
						$method = $this->{'model_extension_payment_' . $result['code']}->getMethod($this->session->data['payment_address'], $total);
					} else {
						$this->load->model('payment/' . $result['code']);
						$method = $this->{'model_payment_' . $result['code']}->getMethod($this->session->data['payment_address'], $total);
					}


					if ($method) {
						if ($recurring) {
							if (property_exists($this->{'model_extension_payment_' . $result['code']}, 'recurringPayments') && $this->{'model_extension_payment_' . $result['code']}->recurringPayments()) {
								$method_data[$result['code']] = $method;
							}
						} else {
							$method_data[$result['code']] = $method;
						}

						$custom_array[] = array(
							'code' 		 => $result['code'],
							'sort_order' => $method['sort_order'],
						);
					}
				}
			}

			$sort_order = array();

			foreach ($method_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $method_data);

			$sort_order = array();
			foreach ($custom_array as $key => $custom_arra) {
				$sort_order[$key] = $custom_arra['sort_order'];
			}
			array_multisort($sort_order, SORT_ASC, $custom_array);

			$this->session->data['payment_methods'] = $method_data;
		}

		// set first code
		if($custom_array) {
			$payment = $custom_array[0]['code'];
			if (!isset($this->session->data['payment_methods'][$payment])) {
			} else{
				$this->session->data['payment_method'] = $this->session->data['payment_methods'][$payment];
			}
		}
	}

	public function setFirstPaymentMethodInSession() {
		$custom_array = array();
		if (isset($this->session->data['payment_address'])) {
			// Totals
			$totals = array();
			$taxes = $this->cart->getTaxes();
			$total = 0;

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
					$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
				}
			}
			

			// Payment Methods
			$method_data = array();

			$this->load->model('setting/extension');

			$results = $this->model_setting_extension->getExtensions('payment');

			$recurring = $this->cart->hasRecurringProducts();

			foreach ($results as $result) {
				if ($this->config->get('payment_' . $result['code'] . '_status')) {
					$this->load->model('extension/payment/' . $result['code']);
					$method = $this->{'model_extension_payment_' . $result['code']}->getMethod($this->session->data['payment_address'], $total);


					if ($method) {
						if ($recurring) {						
							if (property_exists($this->{'model_extension_payment_' . $result['code']}, 'recurringPayments') && $this->{'model_extension_payment_' . $result['code']}->recurringPayments()) {
								$method_data[$result['code']] = $method;
							}
						} else {
							$method_data[$result['code']] = $method;
						}

						$custom_array[] = $result['code'];
					}
				}
			}

			$sort_order = array();

			foreach ($method_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $method_data);

			$this->session->data['payment_methods'] = $method_data;
		}

		// set first code
		if (isset($this->session->data['payment_method']['code'])) {
			if($custom_array && !in_array($this->session->data['payment_method']['code'], $custom_array)) {
				// again set Payment session (code, cost) when prevoius code does not match
				if(isset($custom_array[0])) {
					$payment = $custom_array[0];
					if (!isset($this->session->data['payment_methods'][$payment])) {				
					} else{
						$this->session->data['payment_method'] = $this->session->data['payment_methods'][$payment];
					}
				}
			} else {
				// again set Payment session (code, cost) everytime
				$payment = $this->session->data['payment_method']['code'];
				if (!isset($this->session->data['payment_methods'][$payment])) {				
				} else {
					$this->session->data['payment_method'] = $this->session->data['payment_methods'][$payment];
				}
			}
		}
	}
}