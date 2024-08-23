<?php
class ControllerMpcheckoutShoppingcart extends Controller {
	public function __construct($registry) {
		parent::__construct($registry);
		require_once(DIR_SYSTEM .'library/mpcheckout/manager.php');
	}

	public function index() {
		$data = array();

		$this->load->model('mpcheckout/order');
		// Shipping Method
		if(!isset($this->session->data['shipping_method'])) {
			// $this->model_mpcheckout_order->setDefaultShippingMethodInSession();
		}

		if(isset($this->session->data['shipping_method'])) {
			// $this->model_mpcheckout_order->setFirstShippingMethodInSession();
		}

		// Payment  Method
		if(!isset($this->session->data['payment_method'])) {
			// $this->model_mpcheckout_order->setDefaultPaymentMethodInSession();
		}

		if(isset($this->session->data['payment_method'])) {
			// $this->model_mpcheckout_order->setFirstPaymentMethodInSession();
		}

		$this->content($data);

		if($this->config->get('mpcheckout_template') == 'checkout_1') {
			$mpcheckout_template_shoppingcart = 'shoppingcart_1';
		} else {
			$mpcheckout_template_shoppingcart = 'shoppingcart_2';
		}

		return $this->load->view('mpcheckout/'. $mpcheckout_template_shoppingcart , $data);
	}

	protected function content(&$data) {
		$this->load->language('mpcheckout/checkout');

		$this->load->model('tool/image');
		$this->load->model('tool/upload');

		$mpcheckout_cart_description = $this->config->get('mpcheckout_cart_description');
		if(!empty($mpcheckout_cart_description[$this->config->get('config_language_id')]['title'])) {
			 $this->language->setmpcheckoutlanguage('panel_shopping_cart', $mpcheckout_cart_description[$this->config->get('config_language_id')]['title']);
		}

		$mpcheckout_shoppingcart_panel = $this->config->get('mpcheckout_shoppingcart_panel');

		if (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
			$data['error_warning'] = $this->language->get('error_stock');
		} else{
			$data['error_warning'] = '';
		}

		$data['panel_shopping_cart'] = $this->language->get('panel_shopping_cart');

		$data['text_recurring_item'] = $this->language->get('text_recurring_item');
		$data['text_next'] = $this->language->get('text_next');
		$data['text_next_choice'] = $this->language->get('text_next_choice');

		$data['column_image'] = $this->language->get('column_image');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_total'] = $this->language->get('column_total');
		$data['column_remove'] = $this->language->get('column_remove');

		$data['button_update'] = $this->language->get('button_update');
		$data['button_remove'] = $this->language->get('button_remove');
		$data['button_shopping'] = $this->language->get('button_shopping');
		$data['button_checkout'] = $this->language->get('button_checkout');

		if(!empty($mpcheckout_shoppingcart_panel['qty_update'])) {
			$data['qty_update'] = $mpcheckout_shoppingcart_panel['qty_update'];
		} else {
			$data['qty_update'] = '';
		}

		if(!empty($mpcheckout_shoppingcart_panel['show_product_image'])) {
			$data['show_product_image'] = $mpcheckout_shoppingcart_panel['show_product_image'];
		} else {
			$data['show_product_image'] = '';
		}

		if(!empty($mpcheckout_shoppingcart_panel['product_image_width'])) {
			$width = $mpcheckout_shoppingcart_panel['product_image_width'];
		} else {
			$width = '75';
		}

		if(!empty($mpcheckout_shoppingcart_panel['product_image_height'])) {
			$height = $mpcheckout_shoppingcart_panel['product_image_height'];
		} else {
			$height = '75';
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

			if ($product['minimum'] > $product_total) {
				$data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
			}

			if ($product['image']) {
				$image = $this->model_tool_image->resize($product['image'], $width, $height);
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
					'year'       => $this->language->get('text_year'),
				);

				if ($product['recurring']['trial']) {
					$recurring = sprintf($this->language->get('text_trial_description'), $this->currency->format($this->tax->calculate($product['recurring']['trial_price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['trial_cycle'], $frequencies[$product['recurring']['trial_frequency']], $product['recurring']['trial_duration']) . ' ';
				}

				if ($product['recurring']['duration']) {
					$recurring .= sprintf($this->language->get('text_payment_description'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
				} else {
					$recurring .= sprintf($this->language->get('text_payment_cancel'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
				}
			}

			$data['products'][] = array(
				'cart_id'   => isset($product['cart_id']) ? $product['cart_id'] : $product['key'],
				'product_id'=> $product['product_id'],
				'thumb'     => $image,
				'name'      => $product['name'],
				'model'     => $product['model'],
				'option'    => $option_data,
				'recurring' => $recurring,
				'quantity'  => $product['quantity'],
				'stock'     => $product['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
				'reward'    => ($product['reward'] ? sprintf($this->language->get('text_points'), $product['reward']) : ''),
				'price'     => $price,
				'total'     => $total,
				'href'      => $this->url->link('product/product', 'product_id=' . $product['product_id'], Mpcheckout\Manager::mpssl())
			);
		}

		// Gift Voucher
		$data['vouchers'] = array();

		if (!empty($this->session->data['vouchers'])) {
			foreach ($this->session->data['vouchers'] as $key => $voucher) {
				$data['vouchers'][] = array(
					'key'         => $key,
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'], $this->session->data['currency']),
					'remove'      => $this->url->link('checkout/cart', 'remove=' . $key, Mpcheckout\Manager::mpssl())
				);
			}
		}

		$this->load->model('setting/extension');

		$data['modules'] = array();

		$files = glob(DIR_APPLICATION . '/controller/mpcheckout/mptotal/*.php');

		if ($files) {
			foreach ($files as $file) {
				$result = $this->load->controller('mpcheckout/mptotal/' . basename($file, '.php'));

				if ($result) {
					$data['modules'][] = $result;
				}
			}
		}
// $this->log->write($this->cart->getWeight(). ' weight: cartshiping '. print_r($this->session->data['shipping_method'], 1));

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
					'text'  => $this->currency->format($total['value'], $this->session->data['currency'])
				);
			}

		if (!empty($mpcheckout_shoppingcart_panel['show_weight'])) {
			$data['weight'] = $this->weight->format($this->cart->getWeight(), $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point'));
		} else {
			$data['weight'] = '';
		}
	}

	public function refresh() {
		$json = array();

		/* mismatch_shipping_case starts */
		if(isset($this->request->post['ship_session'])) {
			$ship_session = html_entity_decode($this->request->post['ship_session']);
			$ship_session = json_decode($ship_session, 1);

			if(isset($this->session->data['shipping_method'])) {
				if($this->session->data['shipping_method']['code'] === $ship_session['code']) {
					if(strtolower($this->session->data['shipping_method']['title']) !== strtolower($ship_session['title'])) {
						$json['mismatch_shipping_case'] = true;

						$this->session->data['shipping_method'] = $ship_session;
					}
				}
			}
		}
		/* mismatch_shipping_case ends */

		// Update Session customer group ID refresh
		if(isset($this->request->post['accountoption'])) {
			$accountoption = $this->request->post['accountoption'];
		} else if ($this->customer->isLogged()) {
			$accountoption = 'logged';
		} else{
			$accountoption = 'guest';
		}

		if($accountoption == 'register' || $accountoption == 'guest') {
			if(isset($this->request->post['signup']['customer_group_id'])) {
				if (isset($this->session->data['customer'])) {
					$this->session->data['customer']['customer_group_id'] = $this->request->post['signup']['customer_group_id'];
				} else {
					$this->session->data['customer'] = array();
					$this->session->data['customer']['customer_group_id'] = $this->request->post['signup']['customer_group_id'];
				}
			}

			if(isset($this->request->post['signup']['customer_group_id'])) {
				if (isset($this->session->data['guest'])) {
						$this->session->data['guest']['customer_group_id'] = $this->request->post['signup']['customer_group_id'];
				} else {
					$this->session->data['guest'] = array();
					$this->session->data['guest']['customer_group_id'] = $this->request->post['signup']['customer_group_id'];
				}
			}
		}

		$this->load->language('mpcheckout/checkout');

		$mpcheckout_shipping_address_description = $this->config->get('mpcheckout_shipping_address_description');

		if(!empty($mpcheckout_shipping_address_description[$this->config->get('config_language_id')]['address_not_required'])) {
			 $this->language->setmpcheckoutlanguage('text_norequire_saddress', $mpcheckout_shipping_address_description[$this->config->get('config_language_id')]['address_not_required']);
		}

		// Validate cart has products.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers']))) {
			$json['redirect'] = $this->url->link(Mpcheckout\Manager::MPREDIRECT, '', Mpcheckout\Manager::mpssl());
		}

		$json['html'] = $this->index();

		$json['shipping_required'] = $this->cart->hasShipping();
		$json['text_norequire_saddress'] = $this->language->get('text_norequire_saddress');

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function editrefresh() {
		$this->load->language('mpcheckout/checkout');


		$mpcheckout_shipping_address_description = $this->config->get('mpcheckout_shipping_address_description');

		if(!empty($mpcheckout_shipping_address_description[$this->config->get('config_language_id')]['address_not_required'])) {
			 $this->language->setmpcheckoutlanguage('text_norequire_saddress', $mpcheckout_shipping_address_description[$this->config->get('config_language_id')]['address_not_required']);
		}

		$json = array();

		// Update
		if (!empty($this->request->post['key']) && !empty($this->request->post['quantity'])) {
			$this->cart->update($this->request->post['key'], $this->request->post['quantity']);

			unset($this->session->data['reward']);
		}

		$json['html'] = $this->index();

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
					$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
				}
			}

			$sort_order = array();

			foreach ($totals as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $totals);
		}

		// Validate cart has products.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers']))) {
			$json['redirect'] = $this->url->link(Mpcheckout\Manager::MPREDIRECT, '', Mpcheckout\Manager::mpssl());
		}

		$json['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total, $this->session->data['currency']));

		$json['shipping_required'] = $this->cart->hasShipping();
		$json['text_norequire_saddress'] = $this->language->get('text_norequire_saddress');

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function removerefresh() {
		$this->load->language('mpcheckout/checkout');

		$mpcheckout_shipping_address_description = $this->config->get('mpcheckout_shipping_address_description');

		if(!empty($mpcheckout_shipping_address_description[$this->config->get('config_language_id')]['address_not_required'])) {
			 $this->language->setmpcheckoutlanguage('text_norequire_saddress', $mpcheckout_shipping_address_description[$this->config->get('config_language_id')]['address_not_required']);
		}

		$json = array();

		// Remove
		if (isset($this->request->post['key'])) {
			$this->cart->remove($this->request->post['key']);

			unset($this->session->data['vouchers'][$this->request->post['key']]);

			unset($this->session->data['reward']);

			$json['html'] = $this->index();

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
						$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
					}
				}

				$sort_order = array();

				foreach ($totals as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $totals);
			}

			// Validate cart has products.
			if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers']))) {
				$json['redirect'] = $this->url->link(Mpcheckout\Manager::MPREDIRECT, '', Mpcheckout\Manager::mpssl());
			}

			$json['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total, $this->session->data['currency']));

			$json['shipping_required'] = $this->cart->hasShipping();
			$json['text_norequire_saddress'] = $this->language->get('text_norequire_saddress');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}