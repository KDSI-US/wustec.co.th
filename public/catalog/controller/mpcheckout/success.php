<?php
class ControllerMpcheckoutSuccess extends Controller {
	public function __construct($registry) {
		parent::__construct($registry);
		require_once(DIR_SYSTEM .'library/mpcheckout/manager.php');
	}

	public function index() {
		$this->load->language('checkout/success');
		$this->load->model('checkout/order');
		$this->load->model('mpcheckout/order');
		$this->load->model('tool/image');
		$this->load->model('catalog/product');

		if (!empty($this->session->data['order_id'])) {
			$this->session->data['mporder_id'] = $this->session->data['order_id'];
		}

		if (!empty($this->session->data['mporder_id'])) {
			$order_id = $this->session->data['mporder_id'];
		} else {
			$order_id = 0;
		}

		if (isset($this->session->data['order_id'])) {
			$this->cart->clear();

			// Add to activity log
			if ($this->config->get('config_customer_activity')) {
				$this->load->model('account/activity');

				if ($this->customer->isLogged()) {
					$activity_data = array(
						'customer_id' => $this->customer->getId(),
						'name'        => $this->customer->getFirstName() . ' ' . $this->customer->getLastName(),
						'order_id'    => $this->session->data['order_id']
					);

					$this->model_account_activity->addActivity('order_account', $activity_data);
				} else {
					$activity_data = array(
						'name'     => $this->session->data['guest']['firstname'] . ' ' . $this->session->data['guest']['lastname'],
						'order_id' => $this->session->data['order_id']
					);

					$this->model_account_activity->addActivity('order_guest', $activity_data);
				}
			}

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
			unset($this->session->data['totals']);
		}

		// Page Message
		$mpcheckout_success_description = $this->config->get('mpcheckout_success_description');
		if(!empty($mpcheckout_success_description[$this->config->get('config_language_id')]['heading_title'])) {
		 	$page_title  = $mpcheckout_success_description[$this->config->get('config_language_id')]['heading_title'];
		} else{
			$page_title = $this->language->get('heading_title');
		}

		// Description
		$page_description = '';

		if($this->customer->isLogged()) {
			if(!empty($mpcheckout_success_description[$this->config->get('config_language_id')]['customer_message'])) {
				 $page_description =  $mpcheckout_success_description[$this->config->get('config_language_id')]['customer_message'];
			}
		} else{
			if(!empty($mpcheckout_success_description[$this->config->get('config_language_id')]['guest_message'])) {
				 $page_description =  $mpcheckout_success_description[$this->config->get('config_language_id')]['guest_message'];
			}
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', '', Mpcheckout\Manager::mpssl())
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_basket'),
			'href' => $this->url->link('checkout/cart', '', Mpcheckout\Manager::mpssl())
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_checkout'),
			'href' => $this->url->link('checkout/checkout', '', Mpcheckout\Manager::mpssl())
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_success'),
			'href' => $this->url->link('checkout/success', '', Mpcheckout\Manager::mpssl())
		);

		$order_info =  $this->model_checkout_order->getOrder($order_id);
		if($order_info) {
			$find = array(
				'{firstname}',
				'{lastname}',
				'{order_id}',
				'{order_status}',
				'{order_details}',
				'{order_total_amount}',
			);

			$html = $this->model_mpcheckout_order->getOrderDetails($order_id); 
			
			$replace = array(
				'firstname' => $order_info['payment_firstname'],
				'lastname' => $order_info['payment_lastname'],
				'order_id' => $order_id,
				'order_status' => $order_info['order_status'],
				'order_details' => $html,
				'order_total_amount' => ($order_info) ? $this->currency->format($order_info['total'], $order_info['currency_code']) : '',
			);

			if(!empty($page_title)) {
				 $this->language->setmpcheckoutlanguage('heading_title', str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $page_title)))));
			}
				
			if(!empty($page_description)) {
				$page_description = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $page_description))));
			}else{
				$page_description = '';
			}
		}

		$this->document->setTitle($this->language->get('heading_title'));
		$data['heading_title'] = $this->language->get('heading_title');


		// Continue Button
		if(!empty($mpcheckout_success_description[$this->config->get('config_language_id')]['continue_button'])) {
			 $this->language->setmpcheckoutlanguage('button_continue', $mpcheckout_success_description[$this->config->get('config_language_id')]['continue_button']);			 
		}

		// Print Button
		if(!empty($mpcheckout_success_description[$this->config->get('config_language_id')]['print_button'])) {
			 $this->language->setmpcheckoutlanguage('button_print', $mpcheckout_success_description[$this->config->get('config_language_id')]['print_button']);			 
		}

		$data['button_print'] = $this->language->get('button_print');
		$data['print_link'] = $this->url->link('mpcheckout/success/print_invoice', '', Mpcheckout\Manager::mpssl());
		
		$data['print_status'] = $this->config->get('mpcheckout_print_status');
		/* NEW CODE ENDS */

		$data['button_continue'] = $this->language->get('button_continue');

		$data['text_message'] = (!empty($page_description) ? html_entity_decode($page_description, ENT_QUOTES, 'UTF-8') : '');

		$data['continue'] = $this->url->link('common/home', '', Mpcheckout\Manager::mpssl());

		// Featured Products
		$data['text_tax'] = $this->language->get('text_tax');

		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');

		$mpcheckout_success_promote_title = $this->config->get('mpcheckout_success_promote_title');
		if($mpcheckout_success_promote_title[$this->config->get('config_language_id')]['title']) {
			$data['promote_title'] = $mpcheckout_success_promote_title[$this->config->get('config_language_id')]['title'];
		} else{
			$data['promote_title'] = '';
		}
		
		$data['products'] = array();
		if($this->config->get('mpcheckout_success_promote')) {
			$products = $this->config->get('mpcheckout_success_product');
			if($this->config->get('mpcheckout_success_width')) {
				$width = $this->config->get('mpcheckout_success_width');
			} else{
				$width = '150';
			}

			if($this->config->get('mpcheckout_success_height')) {
				$height = $this->config->get('mpcheckout_success_height');
			} else{
				$height = '150';
			}

			if($products) {
				foreach ($products as $product_id) {
					$product_info = $this->model_catalog_product->getProduct($product_id);

					if ($product_info) {
						if ($product_info['image']) {
							$image = $this->model_tool_image->resize($product_info['image'], $width, $height);
						} else {
							$image = $this->model_tool_image->resize('placeholder.png', $width, $height);
						}

						if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
							$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
						} else {
							$price = false;
						}

						if ((float)$product_info['special']) {
							$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
						} else {
							$special = false;
						}

						if ($this->config->get('config_tax')) {
							$tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
						} else {
							$tax = false;
						}

						if ($this->config->get('config_review_status')) {
							$rating = $product_info['rating'];
						} else {
							$rating = false;
						}

						$data['products'][] = array(
							'product_id'  => $product_info['product_id'],
							'thumb'       => $image,
							'name'        => $product_info['name'],
							'description' => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get($this->config->get('config_theme') . '_product_description_length')) . '..',
							'price'       => $price,
							'special'     => $special,
							'tax'         => $tax,
							'rating'      => $rating,
							'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
						);
					}
				}
			}
		}

		if(!$order_info) {
			$data['text_message'] = '';
		}

		$mpcheckout_color = $this->config->get('mpcheckout_color');	

		$data['background_success_table']		= isset($mpcheckout_color['background_success_table']) ? $mpcheckout_color['background_success_table'] : '';
		$data['font_success_table'] 			= isset($mpcheckout_color['font_success_table']) ? $mpcheckout_color['font_success_table'] : '';

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('mpcheckout/success', $data));
	}

	public function print_invoice() {
		$this->load->language('checkout/success');
		$this->load->model('checkout/order');
		$this->load->model('mpcheckout/order');
		$this->load->model('tool/image');
		$this->load->model('catalog/product');

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

		$data['title'] = $this->document->getTitle();

		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts();
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$data['name'] = $this->config->get('config_name');

		$data['home'] = $this->url->link('common/home');

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		if (!empty($this->session->data['order_id'])) {
			$this->session->data['mporder_id'] = $this->session->data['order_id'];
		}

		if (!empty($this->session->data['mporder_id'])) {
			$order_id = $this->session->data['mporder_id'];
		} else {
			$order_id = 0;
		}

		// Page Message
		$data['heading_title'] = $this->language->get('heading_title');

		$order_info =  $this->model_checkout_order->getOrder($order_id);
		if($order_info) {
			$html = $this->model_mpcheckout_order->getOrderDetails($order_id);
		} else {
			$html = '';
		}

		if($order_info) {
			$data['text_message'] = $html;
		} else {
			$data['text_message'] = '';
		}

		$mpcheckout_color = $this->config->get('mpcheckout_color');	

		$data['background_success_table']		= isset($mpcheckout_color['background_success_table']) ? $mpcheckout_color['background_success_table'] : '';
		$data['font_success_table'] 			= isset($mpcheckout_color['font_success_table']) ? $mpcheckout_color['font_success_table'] : '';

		if($html && $this->config->get('mpcheckout_print_status')) {
			$this->response->setOutput($this->load->view('mpcheckout/printinvoice', $data));
		}
	}
}