<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiCheckoutCart extends OcrestapiController {
	public function index() {
		$this->checkPlugin();
		$language = $this->load->language('checkout/cart');
		
		
		$language = $this->load->language('ocrestapi/message');
		$this->load->model('ocrestapi/ocrestapi');
			$get_token= $this->get_request_token();
			
			$token = explode(' ', $get_token)[1];
			$voucher_data = $this->model_ocrestapi_ocrestapi->get_voucher_data($token);


			
		if ($this->cart->hasProducts() || !empty($voucher_data->vouchers)) {
			
			$this->load->model('tool/image');
			$this->load->model('tool/upload');
			$this->load->model('account/customer');
			$this->load->model('account/address');

			if ($this->customer->isLogged())
			{
				$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
				$this->session->data['shipping_address'] = $this->model_account_address->getAddress($customer_info['address_id']);
			}



			$data['products'] = array();

			$products = $this->cart->getProducts();
			
			foreach ($products as $product) {
				$product_total = 0;

				foreach ($products as $product_2) {
					if ($product_2['product_id'] == $product['product_id']) {
						$product_total += $product_2['quantity'];
					}
				}

				if ($product['image']) {
					$image = $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_height'));
				} else {
					$image = '';
				}

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
				
				// Display prices
				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$unit_price = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'));
					$price = $this->currency->format($unit_price, $this->session->data['currency']);

					$total = $this->currency->format($unit_price * $product['quantity'], $this->session->data['currency']);
				} else {
					$price = false;
					$total = false;
				}

				$recurring = '';

				if ($product['recurring']) {
					$frequencies = array(
						'day'        => $this->language->get('text_day'),
						'week'       => $this->language->get('text_week'),
						'semi_month' => $this->language->get('text_semi_month'),
						'month'      => $this->language->get('text_month'),
						'year'       => $this->language->get('text_year')
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
					'cart_id'   			=> $product['cart_id'],
					'thumb'     			=> $image,
					'product_id'		    => $product['product_id'],
					'name'      			=> $product['name'],
					'model'     			=> $product['model'],
					'option'    			=> $option_data,
					'recurring' 			=> $recurring,
					'quantity'  			=> $product['quantity'],
					'minimum_quantity'  	=> $product['minimum'],
					'stock'    			    => $product['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
					'reward'                => ($product['reward'] ? sprintf($this->language->get('text_points'), $product['reward']) : ''),
					'price'                 => $price,
					'total'                 => $total
				);
			}
			
			// Gift Voucher
			$data['vouchers'] = array();
			if (!empty($vaoucher_data)) {

				if(isset($vaoucher_data->vouchers)){
				foreach ($vaoucher_data->vouchers as $key => $voucher) {
					$image =  $this->model_ocrestapi_ocrestapi->get_voucher_theme_image($voucher->voucher_theme_id);
					
					$data['vouchers'][] = array(
						'key'         => $key,
						'thumb'         => !empty($image)?HTTP_SERVER.'image/'.$image:'',
						'description' => sprintf($this->language->get('text_for'), $this->currency->format($voucher->amount, $this->config->get('config_currency')), $voucher->to_name),
						'amount'      => $this->currency->format($voucher->amount, $this->config->get('config_currency'))
					
						// 'remove'      => $this->url->link('checkout/cart', 'remove=' . $key)
					);
				}
				}
				}
			// Totals
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
		
			$this->json['data'] = $data;

			
		}
		unset($language['backup']);
		$this->json['language'] = $language;
		return $this->sendResponse();
	}

	
	public function add() {
		$this->checkPlugin();
		$this->load->language('checkout/cart');
		$this->load->model('ocrestapi/ocrestapi');
		$language =  $this->load->language('ocrestapi/message');
		
		$json = array();
		if (isset($this->request->post['product_id'])) {
			$product_id = (int)$this->request->post['product_id'];
		} else {
			$product_id = 0;
		}


		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {
			if (isset($this->request->post['quantity'])) {
				$quantity = (int)$this->request->post['quantity'];
			} else {
				$quantity = 1;
			}

			if($this->request->post['quantity']<1){
				$json['quantity'] = $this->language->get('error_quantity');
			}

			$this->json['options']=$this->request->post['option'];
			if (isset($this->request->post['option'])) {
				$option = array_filter($this->request->post['option']);

			} else {
				$option = array();
			}
			
			$product_options = $this->model_catalog_product->getProductOptions($this->request->post['product_id']);
			if(!empty($product_options)){
				foreach ($product_options as $product_option) {
					if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
						$json['option'][$product_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_option['name']);
						$this->json['errors'] = $json;
					}                                               
				}
			}

				if(!$json || isset($this->request->post['option'])){
				$productoptions = $this->model_ocrestapi_ocrestapi->get_product_options($this->request->post['option'],$this->request->post['product_id']);
					foreach ($productoptions as $productoption) {
						if(!empty($productoption['quantity'])){
					if($this->request->post['quantity']>$productoption['quantity']){
						$this->json['errors']['quantity'] = $this->language->get('error_not_available');
					}
					}
				}
				}
		$recurrings = $this->model_catalog_product->getProfiles($product_info['product_id']);

		if ($recurrings) {
			if (!isset($this->request->post['recurring_id']) || empty($this->request->post['recurring_id'])) {
				$this->json['errors']['recurring_id'] = $this->language->get('error_recurring_required');
			}else{
				$recurring_id = $this->request->post['recurring_id'];
				$recurring_ids = array();

				foreach ($recurrings as $recurring) {
					$recurring_ids[] = $recurring['recurring_id'];
				}

				if (!in_array($recurring_id, $recurring_ids)) {
					$this->json['errors']['recurring_id'] = $this->language->get('error_recurring_required');
				}
			}
				
			}


		

			if(!empty($this->json['errors'])){
				$this->json['errors']['warning'] = $this->language->get('error_form_error');
				return $this->sendResponse();
				
			}
			

			

			if (isset($this->request->post['recurring_id'])) {
				$recurring_id = $this->request->post['recurring_id'];
			} else {
				$recurring_id = 0;
			}

		
			

			if (!$json) {
				$this->cart->add($this->request->post['product_id'], $quantity, $option, $recurring_id);

				$json['success'] = sprintf($this->language->get('message_success_cart'), $product_info['name']);

	

				// Totals
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
				$json['rest_session_id']=$this->session->getId();
				$json['total'] = $this->cart->countProducts();
			} else {
				$json['redirect'] = str_replace('&amp;', '&', $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']));
			}
			$this->json['data'] = $json;
			return $this->sendResponse();
		}
		
		return $this->sendResponse();
	}

	public function edit() {
		$this->checkPlugin();
		$this->load->model('ocrestapi/ocrestapi');
		$this->load->language('checkout/cart');
		$this->load->language('ocrestapi/message');
		// Update

		if(!isset($this->request->post['quantity']))
		{
			$this->request->post['quantity']=0;
		}
		if (!isset($this->request->post['quantity']) || empty($this->request->post['quantity'])) {
			$this->json['errors']['quantity'] = $this->language->get('error_quantity');
		}

		if(!isset($this->request->post['cart_id']) || empty($this->request->post['cart_id'])){
		 	$this->json['errors']['cart_id'] = $this->language->get('error_cart_id');
		}else{
			$cart_id = $this->model_ocrestapi_ocrestapi->check_cart_id($this->request->post['cart_id']);
			if($cart_id == false){
				$this->json['errors']['cart_id'] = $this->language->get('error_invelid_cart_id');
			}
		}

		if(isset($this->request->post['cart_id'])){
			$cart_id = $this->model_ocrestapi_ocrestapi->check_cart_id($this->request->post['cart_id']);
			if($cart_id == true){
			$option_data = $this->model_ocrestapi_ocrestapi->getProducts($this->request->post['cart_id']);
			
			foreach ($option_data as $option) {
					if(!empty($option['quantity'])){
				if($this->request->post['quantity']>$option['quantity']){
					$this->json['errors']['quantity'] = $this->language->get('error_not_available');
				}
				}
			}
		}
		}

			if(empty($this->json['errors'])){
			$this->cart->update($this->request->post['cart_id'], $this->request->post['quantity']);
			$this->json['data']['success'] = $this->language->get('text_remove');
			return $this->sendResponse();
			}else{
				$this->json['status'] = false;
				return $this->sendResponse();
			}
		
	}

	public function remove() {
		$this->checkPlugin();
		$this->load->language('checkout/cart');
		$this->load->model('ocrestapi/ocrestapi');

		$json = array();

		// Remove
		
		if (isset($this->request->post['cart_id'])) {
			$this->cart->remove($this->request->post['cart_id']);
			$get_token= $this->get_request_token();
			$token = explode(' ', $get_token)[1];
			$this->model_ocrestapi_ocrestapi->removevoucher($this->request->post['cart_id'],$token);
			

			$json['success'] = $this->language->get('text_remove');

			

			// Totals
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

			$json['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total, $this->config->get('config_currency')));
		
		}
		$this->json['data'] = $json;
		return $this->sendResponse();
		
	}
}
