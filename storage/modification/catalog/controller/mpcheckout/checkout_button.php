<?php
/* This file is under Git Control by KDSI. */
class ControllerMpCheckoutCheckoutButton extends Controller {
	public function __construct($registry) {
		parent::__construct($registry);
		require_once(DIR_SYSTEM .'library/mpcheckout/manager.php');
	}

	public function index() {
		$data = array();

		$this->content($data);


	   	return $this->load->view('mpcheckout/checkout_button', $data);

	}

	public function ajax() {
		$data = array();

		$this->content($data);

		$this->response->setOutput($this->load->view('mpcheckout/checkout_button', $data));

	}

	public function LastConfirmButton() {
		$this->load->language('mpcheckout/checkout');

		$mpcheckout_confirm_order_description = $this->config->get('mpcheckout_confirm_order_description');
		if(!empty($mpcheckout_confirm_order_description[$this->config->get('config_language_id')]['title'])) {
			 $this->language->setmpcheckoutlanguage('panel_confirm_order', $mpcheckout_confirm_order_description[$this->config->get('config_language_id')]['title']);
		}

		if(!empty($mpcheckout_confirm_order_description[$this->config->get('config_language_id')]['comment_placeholder'])) {
			 $this->language->setmpcheckoutlanguage('comment_placeholder', $mpcheckout_confirm_order_description[$this->config->get('config_language_id')]['comment_placeholder']);
		}

		if(!empty($mpcheckout_confirm_order_description[$this->config->get('config_language_id')]['continue_button'])) {
			 $this->language->setmpcheckoutlanguage('button_continue_shopping', $mpcheckout_confirm_order_description[$this->config->get('config_language_id')]['continue_button']);
		}

		if(!empty($mpcheckout_confirm_order_description[$this->config->get('config_language_id')]['button'])) {
			 $this->language->setmpcheckoutlanguage('button_checkout', $mpcheckout_confirm_order_description[$this->config->get('config_language_id')]['button']);
		}

		$data['panel_confirm_order'] = $this->language->get('panel_confirm_order');
		$data['comment_placeholder'] = $this->language->get('comment_placeholder');

		$data['text_agree'] = $this->language->get('text_agree');

		if(isset($this->request->post['agree'])) {
			$data['agree'] = $this->request->post['agree'];
		} else{
			$data['agree'] = '';
		}

		if(isset($this->request->post['shipping_cargo_comment'])) {
			$data['shipping_cargo_comment'] = $this->request->post['shipping_cargo_comment'];
		} else{
			$data['shipping_cargo_comment'] = '';
		}

		if(isset($this->request->post['comment'])) {
			$data['comment'] = $this->request->post['comment'];
		} else{
			$data['comment'] = '';
		}

		$mpcheckout_confirm_panel = $this->config->get('mpcheckout_confirm_panel');
		if (!empty($mpcheckout_confirm_panel['checkout_id'])) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($mpcheckout_confirm_panel['checkout_id']);

			if ($information_info) {
				$data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $mpcheckout_confirm_panel['checkout_id'], Mpcheckout\Manager::mpssl()), $information_info['title'], $information_info['title']);
			} else {
				$data['text_agree'] = '';
			}
		} else {
			$data['text_agree'] = '';
		}

		$data['button_checkout'] = $this->language->get('button_checkout');
		$data['button_continue'] = $this->language->get('button_continue');
		$data['button_continue_shopping'] = $this->language->get('button_continue_shopping');


		$data['continue_shopping'] = $this->url->link('common/home', '', Mpcheckout\Manager::mpssl());

		$mpcheckout_confirm_panel = $this->config->get('mpcheckout_confirm_panel');
		if(!empty($mpcheckout_confirm_panel['continue_shopping_button'])) {
			$data['continue_shopping_button'] = true;
		} else{
			$data['continue_shopping_button'] = false;
		}

		if(!empty($mpcheckout_confirm_panel['show_comment'])) {
			$data['show_comment'] = true;
		} else{
			$data['show_comment'] = false;
		}

		if(!empty($mpcheckout_confirm_panel['autotrigger_order']) && !empty($mpcheckout_confirm_panel['autotrigger_payments']) && in_array($this->session->data['payment_method']['code'], $mpcheckout_confirm_panel['autotrigger_payments'])) {
			$data['autotrigger_order'] = true;
		} else{
			$data['autotrigger_order'] = false;
		}

		$data['payment'] = $this->load->controller('extension/payment/' . $this->session->data['payment_method']['code']);


		$this->response->setOutput($this->load->view('mpcheckout/checkout_button', $data));

	}

	protected function content(&$data) {
		$this->load->language('mpcheckout/checkout');

		$mpcheckout_confirm_order_description = $this->config->get('mpcheckout_confirm_order_description');
		if(!empty($mpcheckout_confirm_order_description[$this->config->get('config_language_id')]['title'])) {
			 $this->language->setmpcheckoutlanguage('panel_confirm_order', $mpcheckout_confirm_order_description[$this->config->get('config_language_id')]['title']);
		}

		if(!empty($mpcheckout_confirm_order_description[$this->config->get('config_language_id')]['comment_placeholder'])) {
			 $this->language->setmpcheckoutlanguage('comment_placeholder', $mpcheckout_confirm_order_description[$this->config->get('config_language_id')]['comment_placeholder']);
		}

		if(!empty($mpcheckout_confirm_order_description[$this->config->get('config_language_id')]['continue_button'])) {
			 $this->language->setmpcheckoutlanguage('button_continue_shopping', $mpcheckout_confirm_order_description[$this->config->get('config_language_id')]['continue_button']);
		}

		if(!empty($mpcheckout_confirm_order_description[$this->config->get('config_language_id')]['button'])) {
			 $this->language->setmpcheckoutlanguage('button_checkout', $mpcheckout_confirm_order_description[$this->config->get('config_language_id')]['button']);
		}

		$data['panel_confirm_order'] = $this->language->get('panel_confirm_order');
		$data['comment_placeholder'] = $this->language->get('comment_placeholder');

		$mpcheckout_confirm_panel = $this->config->get('mpcheckout_confirm_panel');
		if (!empty($mpcheckout_confirm_panel['checkout_id'])) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($mpcheckout_confirm_panel['checkout_id']);

			if ($information_info) {
				$data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $mpcheckout_confirm_panel['checkout_id'], Mpcheckout\Manager::mpssl()), $information_info['title'], $information_info['title']);
			} else {
				$data['text_agree'] = '';
			}
		} else {
			$data['text_agree'] = '';
		}

		if(isset($this->session->data['agree_session'])) {
			$data['agree'] = $this->session->data['agree_session'];
		} else if(!empty($mpcheckout_confirm_panel['default_checkout_id'])) {
			$data['agree'] = true;
		} else{
			$data['agree'] = false;
		}

		if(isset($this->session->data['shipping_cargo_comment_session'])) {
			$data['shipping_cargo_comment'] = $this->session->data['shipping_cargo_comment_session'];
		} else{
			$data['shipping_cargo_comment'] = '';
		}

		if(isset($this->session->data['comment_session'])) {
			$data['comment'] = $this->session->data['comment_session'];
		} else{
			$data['comment'] = '';
		}

		$data['button_checkout'] = $this->language->get('button_checkout');
		$data['button_continue'] = $this->language->get('button_continue');
		$data['button_continue_shopping'] = $this->language->get('button_continue_shopping');

		$data['continue_shopping'] = $this->url->link('common/home', '', Mpcheckout\Manager::mpssl());

		$mpcheckout_confirm_panel = $this->config->get('mpcheckout_confirm_panel');
		if(!empty($mpcheckout_confirm_panel['continue_shopping_button'])) {
			$data['continue_shopping_button'] = true;
		} else{
			$data['continue_shopping_button'] = false;
		}

		if(!empty($mpcheckout_confirm_panel['show_comment'])) {
			$data['show_comment'] = true;
		} else{
			$data['show_comment'] = false;
		}
	}

	public function createValidOrder() {
		$this->load->language('mpcheckout/checkout');

		$this->load->model('account/customer');
		$this->load->model('account/customer_group');
		$this->load->model('account/address');
		$this->load->model('account/custom_field');
		$this->load->model('catalog/information');
		$this->load->model('checkout/order');

		$json = array();

		if(isset($this->request->post['accountoption'])) {
			$accountoption = $this->request->post['accountoption'];
		} else if ($this->customer->isLogged()) {
			$accountoption = 'logged';
		} else{
			$accountoption = 'guest';
		}


		// Account Option Validation
		if($accountoption != 'logged') {
			if ($this->customer->isLogged()) {
				$json['redirect'] = $this->url->link(Mpcheckout\Manager::MPREDIRECT, '', Mpcheckout\Manager::mpssl());
				$this->response->setoutput(json_encode($json));
		    	$this->response->output();
		    	exit();
			}
		}

		if ($this->customer->isLogged()) {
			$accountoption = 'logged';
		}


		/* working */

		// Cart Validation
		// Validate cart has products and has stock.
		if (!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) {
			$json['redirect'] = $this->url->link(Mpcheckout\Manager::MPREDIRECT, '', Mpcheckout\Manager::mpssl());
			$this->response->setoutput(json_encode($json));
		    $this->response->output();
		    exit();
		}

		// Validate cart has products and has stock.
		if (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout')) {
			$json['error']['cart_warning'] = $this->language->get('error_stock');
		}

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
				$json['error']['cart_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);

				break;
			}
		}

		// Guest Validation
		if($accountoption == 'guest') {
			// Validate if customer is already logged out.
			if ($this->customer->isLogged()) {
				$json['redirect'] = $this->url->link(Mpcheckout\Manager::MPREDIRECT, '', Mpcheckout\Manager::mpssl());
				$this->response->setoutput(json_encode($json));
			    $this->response->output();
			    exit();
			}

			$mpcheckout_account_panel = $this->config->get('mpcheckout_account_panel');

			if(isset($mpcheckout_account_panel['fields']['firstname']) && $mpcheckout_account_panel['fields']['firstname'] == 1) {
				if ((utf8_strlen(trim($this->request->post['signup']['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['signup']['firstname'])) > 32)) {
					$json['error']['signup']['firstname'] = $this->language->get('error_firstname');
				}
			}

			if(isset($mpcheckout_account_panel['fields']['lastname']) && $mpcheckout_account_panel['fields']['lastname'] == 1) {
				if ((utf8_strlen(trim($this->request->post['signup']['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['signup']['lastname'])) > 32)) {
					$json['error']['signup']['lastname'] = $this->language->get('error_lastname');
				}
			}

			if ((utf8_strlen($this->request->post['signup']['email']) > 96) || !filter_var($this->request->post['signup']['email'], FILTER_VALIDATE_EMAIL)) {
				$json['error']['signup']['email'] = $this->language->get('error_email');
				$json['error']['signup']['warning'] = $this->language->get('error_email');
			}

			if(isset($mpcheckout_account_panel['fields']['telephone']) && $mpcheckout_account_panel['fields']['telephone'] == 1) {
				if ((utf8_strlen($this->request->post['signup']['telephone']) < 3) || (utf8_strlen($this->request->post['signup']['telephone']) > 32)) {
					$json['error']['signup']['telephone'] = $this->language->get('error_telephone');
				}
			}

			if(isset($mpcheckout_account_panel['fields']['fax']) && $mpcheckout_account_panel['fields']['fax'] == 1) {
				if ((utf8_strlen($this->request->post['signup']['fax']) < 3) || (utf8_strlen($this->request->post['signup']['fax']) > 32)) {
					$json['error']['signup']['fax'] = $this->language->get('error_fax');
				}
			}

			if(isset($mpcheckout_account_panel['fields']['company']) && $mpcheckout_account_panel['fields']['company'] == 1) {
				if ((utf8_strlen($this->request->post['signup']['company']) < 3) || (utf8_strlen($this->request->post['signup']['company']) > 128)) {
					$json['error']['signup']['company'] = $this->language->get('error_company');
				}
			}

			if(isset($mpcheckout_account_panel['fields']['address_1']) && $mpcheckout_account_panel['fields']['address_1'] == 1) {
				if ((utf8_strlen(trim($this->request->post['signup']['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['signup']['address_1'])) > 128)) {
					$json['error']['signup']['address_1'] = $this->language->get('error_address_1');
				}
			}

			if(isset($mpcheckout_account_panel['fields']['address_2']) && $mpcheckout_account_panel['fields']['address_2'] == 1) {
				if ((utf8_strlen(trim($this->request->post['signup']['address_2'])) < 3) || (utf8_strlen(trim($this->request->post['signup']['address_2'])) > 128)) {
					$json['error']['signup']['address_2'] = $this->language->get('error_address_2');
				}
			}

			if(isset($mpcheckout_account_panel['fields']['city']) && $mpcheckout_account_panel['fields']['city'] == 1) {
				if ((utf8_strlen(trim($this->request->post['signup']['city'])) < 2) || (utf8_strlen(trim($this->request->post['signup']['city'])) > 128)) {
					$json['error']['signup']['city'] = $this->language->get('error_city');
				}
			}

			/* $this->load->model('localisation/country');
			$country_info = $this->model_localisation_country->getCountry($this->request->post['signup']['country_id']);
			if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['signup']['postcode'])) < 2 || utf8_strlen(trim($this->request->post['signup']['postcode'])) > 10)) {
				$json['error']['signup']['postcode'] = $this->language->get('error_postcode');
			} */

			if(isset($mpcheckout_account_panel['fields']['postcode']) && $mpcheckout_account_panel['fields']['postcode'] == 1) {
				if (utf8_strlen(trim($this->request->post['signup']['postcode'])) < 2 || utf8_strlen(trim($this->request->post['signup']['postcode'])) > 10) {
					$json['error']['signup']['postcode'] = $this->language->get('error_postcode');
				}
			}

			if(isset($mpcheckout_account_panel['fields']['country']) && $mpcheckout_account_panel['fields']['country'] == 1) {
				if ($this->request->post['signup']['country_id'] == '') {
					$json['error']['signup']['country'] = $this->language->get('error_country');
				}
			}

			if(isset($mpcheckout_account_panel['fields']['zone']) && $mpcheckout_account_panel['fields']['zone'] == 1) {
				if (!isset($this->request->post['signup']['zone_id']) || $this->request->post['signup']['zone_id'] == '' || !is_numeric($this->request->post['signup']['zone_id'])) {
					$json['error']['signup']['zone'] = $this->language->get('error_zone');
				}
			}

			$mpcheckout_customer_group_id = (!empty($mpcheckout_account_panel['customer_group_id']) ? $mpcheckout_account_panel['customer_group_id'] : $this->config->get('config_customer_group_id'));

			// Customer Group
			if (isset($this->request->post['signup']['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['signup']['customer_group_id'], $this->config->get('config_customer_group_display'))) {
				$customer_group_id = $this->request->post['signup']['customer_group_id'];
			} else if ($this->customer->isLogged()) {
				$customer_group_id = $this->config->get('config_customer_group_id');
			} else {
				$customer_group_id = $mpcheckout_customer_group_id;
			}

			// Custom field validation
			$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

			foreach ($custom_fields as $custom_field) {
				if ($custom_field['required'] && empty($this->request->post['signup']['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
					$json['error']['signup']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['signup']['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
					$json['error']['signup']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				}
			}

			if (isset($json['error']) && !isset($json['error']['signup']['warning'])) {
				$json['error']['signup']['warning'] = $this->language->get('error_carefully');
			}

			// Guest Shipping Address
			if (empty($this->request->post['same_address']) && $this->cart->hasShipping()) {
				$mpcheckout_shipping_address_panel = $this->config->get('mpcheckout_shipping_address_panel');

				if(isset($mpcheckout_shipping_address_panel['fields']['firstname']) && $mpcheckout_shipping_address_panel['fields']['firstname'] == 1) {
					if ((utf8_strlen(trim($this->request->post['shipping_address']['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['shipping_address']['firstname'])) > 32)) {
						$json['error']['shipping']['firstname'] = $this->language->get('error_firstname');
					}
				}

				if(isset($mpcheckout_shipping_address_panel['fields']['lastname']) && $mpcheckout_shipping_address_panel['fields']['lastname'] == 1) {
					if ((utf8_strlen(trim($this->request->post['shipping_address']['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['shipping_address']['lastname'])) > 32)) {
						$json['error']['shipping']['lastname'] = $this->language->get('error_lastname');
					}
				}

				if(isset($mpcheckout_shipping_address_panel['fields']['company']) && $mpcheckout_shipping_address_panel['fields']['company'] == 1) {
					if ((utf8_strlen($this->request->post['shipping_address']['company']) < 3) || (utf8_strlen($this->request->post['shipping_address']['company']) > 128)) {
						$json['error']['shipping']['company'] = $this->language->get('error_company');
					}
				}

				if(isset($mpcheckout_shipping_address_panel['fields']['address_1']) && $mpcheckout_shipping_address_panel['fields']['address_1'] == 1) {
					if ((utf8_strlen(trim($this->request->post['shipping_address']['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['shipping_address']['address_1'])) > 128)) {
						$json['error']['shipping']['address_1'] = $this->language->get('error_address_1');
					}
				}

				if(isset($mpcheckout_shipping_address_panel['fields']['address_2']) && $mpcheckout_shipping_address_panel['fields']['address_2'] == 1) {
					if ((utf8_strlen(trim($this->request->post['shipping_address']['address_2'])) < 3) || (utf8_strlen(trim($this->request->post['shipping_address']['address_2'])) > 128)) {
						$json['error']['shipping']['address_2'] = $this->language->get('error_address_2');
					}
				}

				if(isset($mpcheckout_shipping_address_panel['fields']['city']) && $mpcheckout_shipping_address_panel['fields']['city'] == 1) {
					if ((utf8_strlen($this->request->post['shipping_address']['city']) < 2) || (utf8_strlen($this->request->post['shipping_address']['city']) > 32)) {
						$json['error']['shipping']['city'] = $this->language->get('error_city');
					}
				}

				/*$this->load->model('localisation/country');
				$country_info = $this->model_localisation_country->getCountry($this->request->post['shipping_address']['country_id']);

				if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['shipping_address']['postcode'])) < 2 || utf8_strlen(trim($this->request->post['shipping_address']['postcode'])) > 10)) {
					$json['error']['shipping']['postcode'] = $this->language->get('error_postcode');
				}*/

				if(isset($mpcheckout_shipping_address_panel['fields']['postcode']) && $mpcheckout_shipping_address_panel['fields']['postcode'] == 1) {
					if (utf8_strlen(trim($this->request->post['shipping_address']['postcode'])) < 2 || utf8_strlen(trim($this->request->post['shipping_address']['postcode'])) > 10) {
						$json['error']['shipping']['postcode'] = $this->language->get('error_postcode');
					}
				}

				if(isset($mpcheckout_shipping_address_panel['fields']['country']) && $mpcheckout_shipping_address_panel['fields']['country'] == 1) {
					if ($this->request->post['shipping_address']['country_id'] == '') {
						$json['error']['shipping']['country'] = $this->language->get('error_country');
					}
				}

				if(isset($mpcheckout_shipping_address_panel['fields']['zone']) && $mpcheckout_shipping_address_panel['fields']['zone'] == 1) {
					if (!isset($this->request->post['shipping_address']['zone_id']) || $this->request->post['shipping_address']['zone_id'] == '' || !is_numeric($this->request->post['shipping_address']['zone_id'])) {
						$json['error']['shipping']['zone'] = $this->language->get('error_zone');
					}
				}

				$mpcheckout_account_panel = $this->config->get('mpcheckout_account_panel');
				$mpcheckout_customer_group_id = (!empty($mpcheckout_account_panel['customer_group_id']) ? $mpcheckout_account_panel['customer_group_id'] : $this->config->get('config_customer_group_id'));

				// Customer Group
				if (isset($this->request->post['signup']['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['signup']['customer_group_id'], $this->config->get('config_customer_group_display'))) {
					$customer_group_id = $this->request->post['signup']['customer_group_id'];
				} else if ($this->customer->isLogged()) {
					$customer_group_id = $this->config->get('config_customer_group_id');
				} else {
					$customer_group_id = $mpcheckout_customer_group_id;
				}

				// Custom field validation
				$custom_fields = $this->model_account_custom_field->getCustomFields($mpcheckout_customer_group_id);

				foreach ($custom_fields as $custom_field) {
					if (($custom_field['location'] == 'address') && $custom_field['required'] && empty($this->request->post['shipping_address']['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
						$json['error']['shipping']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
					} elseif (($custom_field['location'] == 'address') && ($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['shipping_address']['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
	                    $json['error']['shipping']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
	                }
				}

				if (isset($json['error']) && !isset($json['error']['shipping']['warning'])) {
					$json['error']['shipping']['warning'] = $this->language->get('error_carefully');
				}
			}

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && $this->config->get('mpcheckout_captcha')) {
				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

				if ($captcha) {
					$json['error']['captcha'] = $captcha;
					$json['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'), $json['error']);
					$json['error']['signup']['warning'] = $captcha;
				}
			}

			if (!$json) {
				$this->session->data['account'] = 'guest';

				$this->session->data['guest']['customer_group_id'] = $customer_group_id;
				$this->session->data['guest']['firstname'] = isset($this->request->post['signup']['firstname']) ? $this->request->post['signup']['firstname'] : '';
				$this->session->data['guest']['lastname'] = isset($this->request->post['signup']['lastname']) ? $this->request->post['signup']['lastname'] : '';
				$this->session->data['guest']['email'] = $this->request->post['signup']['email'];
				$this->session->data['guest']['telephone'] = isset($this->request->post['signup']['telephone']) ? $this->request->post['signup']['telephone'] : '';
				$this->session->data['guest']['fax'] = isset($this->request->post['signup']['fax']) ? $this->request->post['signup']['fax'] : '';

				if (isset($this->request->post['signup']['custom_field']['account'])) {
					$this->session->data['guest']['custom_field'] = $this->request->post['signup']['custom_field']['account'];
				} else {
					$this->session->data['guest']['custom_field'] = array();
				}

				$this->session->data['payment_address']['firstname'] = isset($this->request->post['signup']['firstname']) ? $this->request->post['signup']['firstname'] : '';
				$this->session->data['payment_address']['lastname'] = isset($this->request->post['signup']['lastname']) ? $this->request->post['signup']['lastname'] : '';
				$this->session->data['payment_address']['company'] = isset($this->request->post['signup']['company']) ? $this->request->post['signup']['company'] : '';
				$this->session->data['payment_address']['address_1'] = isset($this->request->post['signup']['address_1']) ? $this->request->post['signup']['address_1'] : '';
				$this->session->data['payment_address']['address_2'] = isset($this->request->post['signup']['address_2']) ? $this->request->post['signup']['address_2'] : '';
				$this->session->data['payment_address']['postcode'] = isset($this->request->post['signup']['postcode']) ? $this->request->post['signup']['postcode'] : '';
				$this->session->data['payment_address']['city'] = isset($this->request->post['signup']['city']) ? $this->request->post['signup']['city'] : '';
				$this->session->data['payment_address']['country_id'] = isset($this->request->post['signup']['country_id']) ? $this->request->post['signup']['country_id'] : '';
				$this->session->data['payment_address']['zone_id'] = isset($this->request->post['signup']['zone_id']) ? $this->request->post['signup']['zone_id'] : '';

				$this->load->model('localisation/country');

				$country_info = $this->model_localisation_country->getCountry($this->request->post['signup']['country_id']);

				if ($country_info) {
					$this->session->data['payment_address']['country'] = $country_info['name'];
					$this->session->data['payment_address']['iso_code_2'] = $country_info['iso_code_2'];
					$this->session->data['payment_address']['iso_code_3'] = $country_info['iso_code_3'];
					$this->session->data['payment_address']['address_format'] = $country_info['address_format'];
				} else {
					$this->session->data['payment_address']['country'] = '';
					$this->session->data['payment_address']['iso_code_2'] = '';
					$this->session->data['payment_address']['iso_code_3'] = '';
					$this->session->data['payment_address']['address_format'] = '';
				}

				if (isset($this->request->post['signup']['custom_field']['address'])) {
					$this->session->data['payment_address']['custom_field'] = $this->request->post['signup']['custom_field']['address'];
				} else {
					$this->session->data['payment_address']['custom_field'] = array();
				}

				$this->load->model('localisation/zone');

				$this->request->post['signup']['zone_id'] = isset($this->request->post['signup']['zone_id']) ? $this->request->post['signup']['zone_id'] : '';

				$zone_info = $this->model_localisation_zone->getZone($this->request->post['signup']['zone_id']);

				if ($zone_info) {
					$this->session->data['payment_address']['zone'] = $zone_info['name'];
					$this->session->data['payment_address']['zone_code'] = $zone_info['code'];
				} else {
					$this->session->data['payment_address']['zone'] = '';
					$this->session->data['payment_address']['zone_code'] = '';
				}

				$this->session->data['shipping_cargo_comment'] = isset($this->request->post['shipping_cargo_comment']) ? $this->request->post['shipping_cargo_comment'] : '';

				/*if (!empty($this->request->post['same_address']) && $this->cart->hasShipping()) {
					$this->session->data['guest']['shipping_address'] = $this->request->post['same_address'];
				} else {
					$this->session->data['guest']['shipping_address'] = false;
				}*/

				if (!empty($this->request->post['same_address']) && $this->cart->hasShipping()) {
					$this->session->data['shipping_address'] = $this->session->data['payment_address'];
				} else {
					$this->session->data['shipping_address']['firstname'] = isset($this->request->post['shipping_address']['firstname']) ? $this->request->post['shipping_address']['firstname'] : '';
					$this->session->data['shipping_address']['lastname'] = isset($this->request->post['shipping_address']['lastname']) ? $this->request->post['shipping_address']['lastname'] : '';
					$this->session->data['shipping_address']['company'] = isset($this->request->post['shipping_address']['company']) ? $this->request->post['shipping_address']['company'] : '';
					$this->session->data['shipping_address']['address_1'] = isset($this->request->post['shipping_address']['address_1']) ? $this->request->post['shipping_address']['address_1'] : '';
					$this->session->data['shipping_address']['address_2'] = isset($this->request->post['shipping_address']['address_2']) ? $this->request->post['shipping_address']['address_2'] : '';
					$this->session->data['shipping_address']['postcode'] = isset($this->request->post['shipping_address']['postcode']) ? $this->request->post['shipping_address']['postcode'] : '';
					$this->session->data['shipping_address']['city'] = isset($this->request->post['shipping_address']['city']) ? $this->request->post['shipping_address']['city'] : '';
					$this->session->data['shipping_address']['country_id'] = isset($this->request->post['shipping_address']['country_id']) ? $this->request->post['shipping_address']['country_id'] : '';
					$this->session->data['shipping_address']['zone_id'] = isset($this->request->post['shipping_address']['zone_id']) ? $this->request->post['shipping_address']['zone_id'] : '';

					$country_info = $this->model_localisation_country->getCountry($this->session->data['shipping_address']['country_id']);

					$zone_info = $this->model_localisation_zone->getZone($this->session->data['shipping_address']['zone_id']);

					if ($country_info) {
						$this->session->data['shipping_address']['country'] = $country_info['name'];
						$this->session->data['shipping_address']['iso_code_2'] = $country_info['iso_code_2'];
						$this->session->data['shipping_address']['iso_code_3'] = $country_info['iso_code_3'];
						$this->session->data['shipping_address']['address_format'] = $country_info['address_format'];
					} else {
						$this->session->data['shipping_address']['country'] = '';
						$this->session->data['shipping_address']['iso_code_2'] = '';
						$this->session->data['shipping_address']['iso_code_3'] = '';
						$this->session->data['shipping_address']['address_format'] = '';
					}

					if ($zone_info) {
						$this->session->data['shipping_address']['zone'] = $zone_info['name'];
						$this->session->data['shipping_address']['zone_code'] = $zone_info['code'];
					} else {
						$this->session->data['shipping_address']['zone'] = '';
						$this->session->data['shipping_address']['zone_code'] = '';
					}

					if (isset($this->request->post['shipping_address']['custom_field']['address'])) {
						$this->session->data['shipping_address']['custom_field'] = $this->request->post['shipping_address']['custom_field']['address'];
					} else {
						$this->session->data['shipping_address']['custom_field'] = array();
					}
				}
			}
		}

		// Login
		if($accountoption == 'login') {
			// Validate if customer is already logged out.
			if ($this->customer->isLogged()) {
				$json['redirect'] = $this->url->link(Mpcheckout\Manager::MPREDIRECT, '', Mpcheckout\Manager::mpssl());
				$this->response->setoutput(json_encode($json));
			    $this->response->output();
			    exit();
			}

			// Check how many login attempts have been made.
			$login_info = $this->model_account_customer->getLoginAttempts($this->request->post['email']);

			if ($login_info && ($login_info['total'] >= $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
				$json['error']['login'] = $this->language->get('error_attempts');
			}

			// Check if customer has been approved.
			$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

			if ($customer_info && !$customer_info['approved']) {
				$json['error']['login'] = $this->language->get('error_approved');
			}


			if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {
				$json['error']['login'] = $this->language->get('error_login');

				$this->model_account_customer->addLoginAttempt($this->request->post['email']);
			} else {
				$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
			}


			if (!$json) {
				// Unset guest
				unset($this->session->data['guest']);

				// Default Shipping Address
				$this->load->model('account/address');

				if ($this->config->get('config_tax_customer') == 'payment') {
					$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
				}

				if ($this->config->get('config_tax_customer') == 'shipping') {
					$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
				}

				// Wishlist
				if (isset($this->session->data['wishlist']) && is_array($this->session->data['wishlist'])) {
					$this->load->model('account/wishlist');

					foreach ($this->session->data['wishlist'] as $key => $product_id) {
						$this->model_account_wishlist->addWishlist($product_id);

						unset($this->session->data['wishlist'][$key]);
					}
				}

				// Add to activity log
				if ($this->config->get('config_customer_activity')) {
					$this->load->model('account/activity');

					$activity_data = array(
						'customer_id' => $this->customer->getId(),
						'name'        => $this->customer->getFirstName() . ' ' . $this->customer->getLastName()
					);

					$this->model_account_activity->addActivity('login', $activity_data);
				}

				$json['redirect'] = $this->url->link(Mpcheckout\Manager::MPREDIRECT, '', Mpcheckout\Manager::mpssl());
				$this->response->setoutput(json_encode($json));
			    $this->response->output();
			    exit();
			}
		}

		// Logged Payment Address
		if(in_array($accountoption, array('logged'))) {
			if (isset($this->request->post['payment_address']['payment_address']) && $this->request->post['payment_address']['payment_address'] == 'existing') {
				$this->load->model('account/address');

				if (empty($this->request->post['payment_address']['address_id'])) {
					$json['error']['payment']['warning'] = $this->language->get('error_address');
				} elseif (!in_array($this->request->post['payment_address']['address_id'], array_keys($this->model_account_address->getAddresses()))) {
					$json['error']['payment']['warning'] = $this->language->get('error_address');
				}

				if (!$json) {
					// Default Payment Address
					$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->request->post['payment_address']['address_id']);
				}
			} else {
				$mpcheckout_payment_address_panel = $this->config->get('mpcheckout_payment_address_panel');

				if(isset($mpcheckout_payment_address_panel['fields']['firstname']) && $mpcheckout_payment_address_panel['fields']['firstname'] == 1) {
					if ((utf8_strlen(trim($this->request->post['payment_address']['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['payment_address']['firstname'])) > 32)) {
						$json['error']['payment']['firstname'] = $this->language->get('error_firstname');
					}
				}

				if(isset($mpcheckout_payment_address_panel['fields']['lastname']) && $mpcheckout_payment_address_panel['fields']['lastname'] == 1) {
					if ((utf8_strlen(trim($this->request->post['payment_address']['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['payment_address']['lastname'])) > 32)) {
						$json['error']['payment']['lastname'] = $this->language->get('error_lastname');
					}
				}

				if(isset($mpcheckout_payment_address_panel['fields']['company']) && $mpcheckout_payment_address_panel['fields']['company'] == 1) {
					if ((utf8_strlen($this->request->post['payment_address']['company']) < 3) || (utf8_strlen($this->request->post['payment_address']['company']) > 128)) {
						$json['error']['payment']['company'] = $this->language->get('error_company');
					}
				}

				if(isset($mpcheckout_payment_address_panel['fields']['address_1']) && $mpcheckout_payment_address_panel['fields']['address_1'] == 1) {
					if ((utf8_strlen(trim($this->request->post['payment_address']['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['payment_address']['address_1'])) > 128)) {
						$json['error']['payment']['address_1'] = $this->language->get('error_address_1');
					}
				}

				if(isset($mpcheckout_payment_address_panel['fields']['address_2']) && $mpcheckout_payment_address_panel['fields']['address_2'] == 1) {
					if ((utf8_strlen(trim($this->request->post['payment_address']['address_2'])) < 3) || (utf8_strlen(trim($this->request->post['payment_address']['address_2'])) > 128)) {
						$json['error']['payment']['address_2'] = $this->language->get('error_address_2');
					}
				}

				if(isset($mpcheckout_payment_address_panel['fields']['city']) && $mpcheckout_payment_address_panel['fields']['city'] == 1) {
					if ((utf8_strlen($this->request->post['payment_address']['city']) < 2) || (utf8_strlen($this->request->post['payment_address']['city']) > 32)) {
						$json['error']['payment']['city'] = $this->language->get('error_city');
					}
				}

				/*$this->load->model('localisation/country');
				$country_info = $this->model_localisation_country->getCountry($this->request->post['payment_address']['country_id']);

				if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['payment_address']['postcode'])) < 2 || utf8_strlen(trim($this->request->post['payment_address']['postcode'])) > 10)) {
					$json['error']['payment']['postcode'] = $this->language->get('error_postcode');
				}*/

				if(isset($mpcheckout_payment_address_panel['fields']['postcode']) && $mpcheckout_payment_address_panel['fields']['postcode'] == 1) {
					if (utf8_strlen(trim($this->request->post['payment_address']['postcode'])) < 2 || utf8_strlen(trim($this->request->post['payment_address']['postcode'])) > 10) {
						$json['error']['payment']['postcode'] = $this->language->get('error_postcode');
					}
				}

				if(isset($mpcheckout_payment_address_panel['fields']['country']) && $mpcheckout_payment_address_panel['fields']['country'] == 1) {
					if ($this->request->post['payment_address']['country_id'] == '') {
						$json['error']['payment']['country'] = $this->language->get('error_country');
					}
				}

				if(isset($mpcheckout_payment_address_panel['fields']['zone']) && $mpcheckout_payment_address_panel['fields']['zone'] == 1) {
					if (!isset($this->request->post['payment_address']['zone_id']) || $this->request->post['payment_address']['zone_id'] == '' || !is_numeric($this->request->post['payment_address']['zone_id'])) {
						$json['error']['payment']['zone'] = $this->language->get('error_zone');
					}
				}

				$mpcheckout_account_panel = $this->config->get('mpcheckout_account_panel');
				$mpcheckout_customer_group_id = (!empty($mpcheckout_account_panel['customer_group_id']) ? $mpcheckout_account_panel['customer_group_id'] : $this->config->get('config_customer_group_id'));

				// Customer Group
				if (isset($this->request->post['signup']['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['signup']['customer_group_id'], $this->config->get('config_customer_group_display'))) {
					$customer_group_id = $this->request->post['signup']['customer_group_id'];
				} else if ($this->customer->isLogged()) {
					$customer_group_id = $this->config->get('config_customer_group_id');
				} else {
					$customer_group_id = $mpcheckout_customer_group_id;
				}

				// Custom field validation
				$custom_fields = $this->model_account_custom_field->getCustomFields($mpcheckout_customer_group_id);

				foreach ($custom_fields as $custom_field) {
					if (($custom_field['location'] == 'address') && $custom_field['required'] && empty($this->request->post['payment_address']['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
						$json['error']['payment']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
					} elseif (($custom_field['location'] == 'address') && ($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['payment_address']['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
	                    $json['error']['payment']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
	                }
				}

				if (isset($json['error']) && !isset($json['error']['payment']['warning'])) {
					$json['error']['payment']['warning'] = $this->language->get('error_carefully');
				}

				if (!$json) {
					// Default Payment Address
					$this->request->post['payment_address']['firstname'] = isset($this->request->post['payment_address']['firstname']) ? $this->request->post['payment_address']['firstname'] : '';

					$this->request->post['payment_address']['lastname'] = isset($this->request->post['payment_address']['lastname']) ? $this->request->post['payment_address']['lastname'] : '';

					$this->request->post['payment_address']['company'] = isset($this->request->post['payment_address']['company']) ? $this->request->post['payment_address']['company'] : '';

					$this->request->post['payment_address']['address_1'] = isset($this->request->post['payment_address']['address_1']) ? $this->request->post['payment_address']['address_1'] : '';

					$this->request->post['payment_address']['address_2'] = isset($this->request->post['payment_address']['address_2']) ? $this->request->post['payment_address']['address_2'] : '';

					$this->request->post['payment_address']['city'] = isset($this->request->post['payment_address']['city']) ? $this->request->post['payment_address']['city'] : '';

					$this->request->post['payment_address']['postcode'] = isset($this->request->post['payment_address']['postcode']) ? $this->request->post['payment_address']['postcode'] : '';

					$this->request->post['payment_address']['country_id'] = isset($this->request->post['payment_address']['country_id']) ? $this->request->post['payment_address']['country_id'] : '';

					$this->request->post['payment_address']['zone_id'] = isset($this->request->post['payment_address']['zone_id']) ? $this->request->post['payment_address']['zone_id'] : '';

					/*$this->request->post['payment_address']['custom_field'] = (isset($this->request->post['payment_address']['custom_field']['address']) ? $this->request->post['payment_address']['custom_field']['address'] : array()); */

					$address_id = $this->model_account_address->addAddress($this->customer->getId(), $this->request->post['payment_address']);
					$this->session->data['payment_address'] = $this->model_account_address->getAddress($address_id);
				}
			}
		}

		// Shipping Address
		if(in_array($accountoption, array('logged')) && $this->cart->hasShipping()) {
			if (!empty($this->request->post['same_address']) && isset($this->request->post['payment_address']['payment_address']) && $this->request->post['payment_address']['payment_address'] == 'existing') {
				$this->load->model('account/address');

				if (empty($this->request->post['payment_address']['address_id'])) {
					$json['error']['payment']['warning'] = $this->language->get('error_address');
				} elseif (!in_array($this->request->post['payment_address']['address_id'], array_keys($this->model_account_address->getAddresses()))) {
					$json['error']['payment']['warning'] = $this->language->get('error_address');
				}

				if (!$json) {
					// Default Payment Address
					$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->request->post['payment_address']['address_id']);
				}
			} else if (empty($this->request->post['same_address']) && isset($this->request->post['shipping_address']['shipping_address']) && $this->request->post['shipping_address']['shipping_address'] == 'existing') {
				$this->load->model('account/address');

				if (empty($this->request->post['shipping_address']['address_id'])) {
					$json['error']['shipping']['warning'] = $this->language->get('error_address');
				} elseif (!in_array($this->request->post['shipping_address']['address_id'], array_keys($this->model_account_address->getAddresses()))) {
					$json['error']['shipping']['warning'] = $this->language->get('error_address');
				}

				if (!$json) {
					// Default Payment Address
					$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->request->post['shipping_address']['address_id']);
				}
			} else {
				if (empty($this->request->post['same_address'])) {
					$mpcheckout_shipping_address_panel = $this->config->get('mpcheckout_shipping_address_panel');

					if(isset($mpcheckout_shipping_address_panel['fields']['firstname']) && $mpcheckout_shipping_address_panel['fields']['firstname'] == 1) {
						if ((utf8_strlen(trim($this->request->post['shipping_address']['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['shipping_address']['firstname'])) > 32)) {
							$json['error']['shipping']['firstname'] = $this->language->get('error_firstname');
						}
					}

					if(isset($mpcheckout_shipping_address_panel['fields']['lastname']) && $mpcheckout_shipping_address_panel['fields']['lastname'] == 1) {
						if ((utf8_strlen(trim($this->request->post['shipping_address']['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['shipping_address']['lastname'])) > 32)) {
							$json['error']['shipping']['lastname'] = $this->language->get('error_lastname');
						}
					}

					if(isset($mpcheckout_shipping_address_panel['fields']['company']) && $mpcheckout_shipping_address_panel['fields']['company'] == 1) {
						if ((utf8_strlen(trim($this->request->post['shipping_address']['company'])) < 3) || (utf8_strlen(trim($this->request->post['shipping_address']['company'])) > 128)) {
							$json['error']['shipping']['company'] = $this->language->get('error_company');
						}
					}

					if(isset($mpcheckout_shipping_address_panel['fields']['address_1']) && $mpcheckout_shipping_address_panel['fields']['address_1'] == 1) {
						if ((utf8_strlen(trim($this->request->post['shipping_address']['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['shipping_address']['address_1'])) > 128)) {
							$json['error']['shipping']['address_1'] = $this->language->get('error_address_1');
						}
					}

					if(isset($mpcheckout_shipping_address_panel['fields']['address_2']) && $mpcheckout_shipping_address_panel['fields']['address_2'] == 1) {
						if ((utf8_strlen(trim($this->request->post['shipping_address']['address_2'])) < 3) || (utf8_strlen(trim($this->request->post['shipping_address']['address_2'])) > 128)) {
							$json['error']['shipping']['address_2'] = $this->language->get('error_address_2');
						}
					}

					if(isset($mpcheckout_shipping_address_panel['fields']['city']) && $mpcheckout_shipping_address_panel['fields']['city'] == 1) {
						if ((utf8_strlen($this->request->post['shipping_address']['city']) < 2) || (utf8_strlen($this->request->post['shipping_address']['city']) > 32)) {
							$json['error']['shipping']['city'] = $this->language->get('error_city');
						}
					}

					/*$this->load->model('localisation/country');
					$country_info = $this->model_localisation_country->getCountry($this->request->post['shipping_address']['country_id']);

					if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['shipping_address']['postcode'])) < 2 || utf8_strlen(trim($this->request->post['shipping_address']['postcode'])) > 10)) {
						$json['error']['shipping']['postcode'] = $this->language->get('error_postcode');
					}*/

					if(isset($mpcheckout_shipping_address_panel['fields']['postcode']) && $mpcheckout_shipping_address_panel['fields']['postcode'] == 1) {
						if (utf8_strlen(trim($this->request->post['shipping_address']['postcode'])) < 2 || utf8_strlen(trim($this->request->post['shipping_address']['postcode'])) > 10) {
							$json['error']['shipping']['postcode'] = $this->language->get('error_postcode');
						}
					}

					if(isset($mpcheckout_shipping_address_panel['fields']['country']) && $mpcheckout_shipping_address_panel['fields']['country'] == 1) {
						if ($this->request->post['shipping_address']['country_id'] == '') {
							$json['error']['shipping']['country'] = $this->language->get('error_country');
						}
					}

					if(isset($mpcheckout_shipping_address_panel['fields']['zone']) && $mpcheckout_shipping_address_panel['fields']['zone'] == 1) {
						if (!isset($this->request->post['shipping_address']['zone_id']) || $this->request->post['shipping_address']['zone_id'] == '' || !is_numeric($this->request->post['shipping_address']['zone_id'])) {
							$json['error']['shipping']['zone'] = $this->language->get('error_zone');
						}
					}

					$mpcheckout_account_panel = $this->config->get('mpcheckout_account_panel');
					$mpcheckout_customer_group_id = (!empty($mpcheckout_account_panel['customer_group_id']) ? $mpcheckout_account_panel['customer_group_id'] : $this->config->get('config_customer_group_id'));

					// Customer Group
					if (isset($this->request->post['signup']['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['signup']['customer_group_id'], $this->config->get('config_customer_group_display'))) {
						$customer_group_id = $this->request->post['signup']['customer_group_id'];
					} else if ($this->customer->isLogged()) {
						$customer_group_id = $this->config->get('config_customer_group_id');
					} else {
						$customer_group_id = $mpcheckout_customer_group_id;
					}

					// Custom field validation
					$custom_fields = $this->model_account_custom_field->getCustomFields($mpcheckout_customer_group_id);

					foreach ($custom_fields as $custom_field) {
						if (($custom_field['location'] == 'address') && $custom_field['required'] && empty($this->request->post['shipping_address']['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
							$json['error']['shipping']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
						} elseif (($custom_field['location'] == 'address') && ($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['shipping_address']['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
		                    $json['error']['shipping']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
		                }
					}
				}

				if (isset($json['error']) && !isset($json['error']['shipping']['warning'])) {
					$json['error']['shipping']['warning'] = $this->language->get('error_carefully');
				}

				if (!$json) {
					// Default Payment Address
					if (!empty($this->request->post['same_address'])) {
						if(!empty($this->session->data['payment_address'])) {
							$this->session->data['shipping_address'] = $this->session->data['payment_address'];
						}
					} else {
						$this->request->post['shipping_address']['firstname'] = isset($this->request->post['shipping_address']['firstname']) ? $this->request->post['shipping_address']['firstname'] : '';

						$this->request->post['shipping_address']['lastname'] = isset($this->request->post['shipping_address']['lastname']) ? $this->request->post['shipping_address']['lastname'] : '';

						$this->request->post['shipping_address']['company'] = isset($this->request->post['shipping_address']['company']) ? $this->request->post['shipping_address']['company'] : '';

						$this->request->post['shipping_address']['address_1'] = isset($this->request->post['shipping_address']['address_1']) ? $this->request->post['shipping_address']['address_1'] : '';

						$this->request->post['shipping_address']['address_2'] = isset($this->request->post['shipping_address']['address_2']) ? $this->request->post['shipping_address']['address_2'] : '';

						$this->request->post['shipping_address']['city'] = isset($this->request->post['shipping_address']['city']) ? $this->request->post['shipping_address']['city'] : '';

						$this->request->post['shipping_address']['postcode'] = isset($this->request->post['shipping_address']['postcode']) ? $this->request->post['shipping_address']['postcode'] : '';

						$this->request->post['shipping_address']['country_id'] = isset($this->request->post['shipping_address']['country_id']) ? $this->request->post['shipping_address']['country_id'] : '';

						$this->request->post['shipping_address']['zone_id'] = isset($this->request->post['shipping_address']['zone_id']) ? $this->request->post['shipping_address']['zone_id'] : '';

						/*$this->request->post['shipping_address']['custom_field'] = (isset($this->request->post['shipping_address']['custom_field']['address']) ? $this->request->post['shipping_address']['custom_field']['address'] : array()); */

						$address_id = $this->model_account_address->addAddress($this->customer->getId(), $this->request->post['shipping_address']);
						$this->session->data['shipping_address'] = $this->model_account_address->getAddress($address_id);
					}
				}
			}
		}

		// For Shipping Method
		if ($this->cart->hasShipping()) {
			if (!isset($this->session->data['shipping_address'])) {
				$json['error']['warning'] = $this->language->get('error_required_shipping_address');
			}

			if (!isset($this->request->post['shipping_method'])) {
				$json['error']['shipping_method']['warning'] = $this->language->get('error_shipping');
			} else {
				$shipping = explode('.', $this->request->post['shipping_method']);

				if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
					$json['error']['shipping_method']['warning'] = $this->language->get('error_shipping');
				}
			}

			if (!$json) {
				$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
			}
		}

		// For Payment Method
		if (!isset($this->request->post['payment_method'])) {
			$json['error']['payment_method']['warning'] = $this->language->get('error_payment');
		} elseif (!isset($this->session->data['payment_methods'][$this->request->post['payment_method']])) {
			$json['error']['payment_method']['warning'] = $this->language->get('error_payment');
		}

		if (!$json) {
			$this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];
			$this->session->data['shipping_cargo_comment'] = isset($this->request->post['shipping_cargo_comment']) ? strip_tags($this->request->post['shipping_cargo_comment']) : '';


			$this->session->data['comment'] = isset($this->request->post['comment']) ? strip_tags($this->request->post['comment']) : '';
		}


		$mpcheckout_confirm_panel = $this->config->get('mpcheckout_confirm_panel');
		if (!empty($mpcheckout_confirm_panel['checkout_id'])) {
			$information_info = $this->model_catalog_information->getInformation($mpcheckout_confirm_panel['checkout_id']);

			if ($information_info && !isset($this->request->post['agree'])) {
				$json['error']['comment_warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
			}
		}

		// Signup Validation
		if($accountoption == 'register') {
			$mpcheckout_account_panel = $this->config->get('mpcheckout_account_panel');

			// Validate if customer is already logged out.
			if ($this->customer->isLogged()) {
				$json['redirect'] = $this->url->link(Mpcheckout\Manager::MPREDIRECT, '', Mpcheckout\Manager::mpssl());
				$this->response->setoutput(json_encode($json));
			    $this->response->output();
			    exit();
			}

			if ($this->cart->hasShipping()) {
				if (!isset($this->session->data['shipping_method'])) {
					$json['error']['shipping_method']['warning'] = $this->url->link(Mpcheckout\Manager::MPREDIRECT, '', Mpcheckout\Manager::mpssl());
				}
			}

			if (!isset($this->session->data['payment_method'])) {
				$json['error']['payment_method']['warning'] = $this->url->link(Mpcheckout\Manager::MPREDIRECT, '', Mpcheckout\Manager::mpssl());
			}

			if(isset($mpcheckout_account_panel['fields']['firstname']) && $mpcheckout_account_panel['fields']['firstname'] == 1) {
				if ((utf8_strlen(trim($this->request->post['signup']['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['signup']['firstname'])) > 32)) {
					$json['error']['signup']['firstname'] = $this->language->get('error_firstname');
				}
			}

			if(isset($mpcheckout_account_panel['fields']['lastname']) && $mpcheckout_account_panel['fields']['lastname'] == 1) {
				if ((utf8_strlen(trim($this->request->post['signup']['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['signup']['lastname'])) > 32)) {
					$json['error']['signup']['lastname'] = $this->language->get('error_lastname');
				}
			}

			if(isset($mpcheckout_account_panel['fields']['telephone']) && $mpcheckout_account_panel['fields']['telephone'] == 1) {
				if ((utf8_strlen($this->request->post['signup']['telephone']) < 3) || (utf8_strlen($this->request->post['signup']['telephone']) > 32)) {
					$json['error']['signup']['telephone'] = $this->language->get('error_telephone');
				}
			}

			if(isset($mpcheckout_account_panel['fields']['fax']) && $mpcheckout_account_panel['fields']['fax'] == 1) {
				if ((utf8_strlen($this->request->post['signup']['fax']) < 3) || (utf8_strlen($this->request->post['signup']['fax']) > 32)) {
					$json['error']['signup']['fax'] = $this->language->get('error_fax');
				}
			}

			if(isset($mpcheckout_account_panel['fields']['company']) && $mpcheckout_account_panel['fields']['company'] == 1) {
				if ((utf8_strlen($this->request->post['signup']['company']) < 3) || (utf8_strlen($this->request->post['signup']['company']) > 32)) {
					$json['error']['signup']['company'] = $this->language->get('error_company');
				}
			}

			if(isset($mpcheckout_account_panel['fields']['address_1']) && $mpcheckout_account_panel['fields']['address_1'] == 1) {
				if ((utf8_strlen(trim($this->request->post['signup']['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['signup']['address_1'])) > 128)) {
					$json['error']['signup']['address_1'] = $this->language->get('error_address_1');
				}
			}

			if(isset($mpcheckout_account_panel['fields']['address_2']) && $mpcheckout_account_panel['fields']['address_2'] == 1) {
				if ((utf8_strlen(trim($this->request->post['signup']['address_2'])) < 3) || (utf8_strlen(trim($this->request->post['signup']['address_2'])) > 128)) {
					$json['error']['signup']['address_2'] = $this->language->get('error_address_2');
				}
			}

			if(isset($mpcheckout_account_panel['fields']['city']) && $mpcheckout_account_panel['fields']['city'] == 1) {
				if ((utf8_strlen(trim($this->request->post['signup']['city'])) < 2) || (utf8_strlen(trim($this->request->post['signup']['city'])) > 128)) {
					$json['error']['signup']['city'] = $this->language->get('error_city');
				}
			}

			/*$this->load->model('localisation/country');
			$country_info = $this->model_localisation_country->getCountry($this->request->post['signup']['country_id']);

			if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['signup']['postcode'])) < 2 || utf8_strlen(trim($this->request->post['signup']['postcode'])) > 10)) {
				$json['error']['signup']['postcode'] = $this->language->get('error_postcode');
			}*/

			if(isset($mpcheckout_account_panel['fields']['postcode']) && $mpcheckout_account_panel['fields']['postcode'] == 1) {
				if (utf8_strlen(trim($this->request->post['signup']['postcode'])) < 2 || utf8_strlen(trim($this->request->post['signup']['postcode'])) > 10) {
					$json['error']['signup']['postcode'] = $this->language->get('error_postcode');
				}
			}

			if(isset($mpcheckout_account_panel['fields']['country']) && $mpcheckout_account_panel['fields']['country'] == 1) {
				if ($this->request->post['signup']['country_id'] == '') {
					$json['error']['signup']['country'] = $this->language->get('error_country');
				}
			}

			if(isset($mpcheckout_account_panel['fields']['zone']) && $mpcheckout_account_panel['fields']['zone'] == 1) {
				if (!isset($this->request->post['signup']['zone_id']) || $this->request->post['signup']['zone_id'] == '' || !is_numeric($this->request->post['signup']['zone_id'])) {
					$json['error']['signup']['zone'] = $this->language->get('error_zone');
				}
			}

			if ((utf8_strlen($this->request->post['signup']['password']) < 4) || (utf8_strlen($this->request->post['signup']['password']) > 20)) {
				$json['error']['signup']['password'] = $this->language->get('error_password');
				$json['error']['signup']['warning'] = $this->language->get('error_password');
			}

			if(isset($mpcheckout_account_panel['fields']['confirm_password']) && $mpcheckout_account_panel['fields']['confirm_password'] == 1) {
				if ($this->request->post['signup']['confirm_password'] != $this->request->post['signup']['password']) {
					$json['error']['signup']['confirm_password'] = $this->language->get('error_confirm');
					$json['error']['signup']['warning'] = $this->language->get('error_confirm');
				}
			}

			if ((utf8_strlen($this->request->post['signup']['email']) > 96) || !filter_var($this->request->post['signup']['email'], FILTER_VALIDATE_EMAIL)) {
				$json['error']['signup']['email'] = $this->language->get('error_email');
				$json['error']['signup']['warning'] = $this->language->get('error_email');
			}

			if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['signup']['email'])) {
				$json['error']['signup']['email'] = $this->language->get('error_exists');
				$json['error']['signup']['warning'] = $this->language->get('error_exists');
			}

			if (!empty($mpcheckout_account_panel['account_id'])) {
				$information_info = $this->model_catalog_information->getInformation($mpcheckout_account_panel['account_id']);

				if ($information_info && !isset($this->request->post['signup']['agree'])) {
					$json['error']['signup_privacy']['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
				}
			}

			$mpcheckout_account_panel = $this->config->get('mpcheckout_account_panel');
			$mpcheckout_customer_group_id = (!empty($mpcheckout_account_panel['customer_group_id']) ? $mpcheckout_account_panel['customer_group_id'] : $this->config->get('config_customer_group_id'));

			// Customer Group
			if (isset($this->request->post['signup']['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['signup']['customer_group_id'], $this->config->get('config_customer_group_display'))) {
				$customer_group_id = $this->request->post['signup']['customer_group_id'];
			} else if ($this->customer->isLogged()) {
				$customer_group_id = $this->config->get('config_customer_group_id');
			} else {
				$customer_group_id = $mpcheckout_customer_group_id;
			}

			// Custom field validation
			$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

			foreach ($custom_fields as $custom_field) {
				if ($custom_field['required'] && empty($this->request->post['signup']['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
					$json['error']['signup']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['signup']['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
					$json['error']['signup']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				}
			}

			if (isset($json['error']) && !isset($json['error']['signup']['warning'])) {
				$json['error']['signup']['warning'] = $this->language->get('error_carefully');
			}


			// Register Shipping Address
			if (empty($this->request->post['same_address']) && $this->cart->hasShipping()) {
				$mpcheckout_shipping_address_panel = $this->config->get('mpcheckout_shipping_address_panel');

				if(isset($mpcheckout_shipping_address_panel['fields']['firstname']) && $mpcheckout_shipping_address_panel['fields']['firstname'] == 1) {
					if ((utf8_strlen(trim($this->request->post['shipping_address']['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['shipping_address']['firstname'])) > 32)) {
						$json['error']['shipping']['firstname'] = $this->language->get('error_firstname');
					}
				}

				if(isset($mpcheckout_shipping_address_panel['fields']['lastname']) && $mpcheckout_shipping_address_panel['fields']['lastname'] == 1) {
					if ((utf8_strlen(trim($this->request->post['shipping_address']['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['shipping_address']['lastname'])) > 32)) {
						$json['error']['shipping']['lastname'] = $this->language->get('error_lastname');
					}
				}

				if(isset($mpcheckout_shipping_address_panel['fields']['company']) && $mpcheckout_shipping_address_panel['fields']['company'] == 1) {
					if ((utf8_strlen(trim($this->request->post['shipping_address']['company'])) < 3) || (utf8_strlen(trim($this->request->post['shipping_address']['company'])) > 128)) {
						$json['error']['shipping']['company'] = $this->language->get('error_company');
					}
				}

				if(isset($mpcheckout_shipping_address_panel['fields']['address_1']) && $mpcheckout_shipping_address_panel['fields']['address_1'] == 1) {
					if ((utf8_strlen(trim($this->request->post['shipping_address']['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['shipping_address']['address_1'])) > 128)) {
						$json['error']['shipping']['address_1'] = $this->language->get('error_address_1');
					}
				}

				if(isset($mpcheckout_shipping_address_panel['fields']['address_2']) && $mpcheckout_shipping_address_panel['fields']['address_2'] == 1) {
					if ((utf8_strlen(trim($this->request->post['shipping_address']['address_2'])) < 3) || (utf8_strlen(trim($this->request->post['shipping_address']['address_2'])) > 128)) {
						$json['error']['shipping']['address_2'] = $this->language->get('error_address_2');
					}
				}

				if(isset($mpcheckout_shipping_address_panel['fields']['city']) && $mpcheckout_shipping_address_panel['fields']['city'] == 1) {
					if ((utf8_strlen($this->request->post['shipping_address']['city']) < 2) || (utf8_strlen($this->request->post['shipping_address']['city']) > 32)) {
						$json['error']['shipping']['city'] = $this->language->get('error_city');
					}
				}

				/*$this->load->model('localisation/country');
				$country_info = $this->model_localisation_country->getCountry($this->request->post['shipping_address']['country_id']);

				if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['shipping_address']['postcode'])) < 2 || utf8_strlen(trim($this->request->post['shipping_address']['postcode'])) > 10)) {
					$json['error']['shipping']['postcode'] = $this->language->get('error_postcode');
				}*/

				if(isset($mpcheckout_shipping_address_panel['fields']['postcode']) && $mpcheckout_shipping_address_panel['fields']['postcode'] == 1) {
					if (utf8_strlen(trim($this->request->post['shipping_address']['postcode'])) < 2 || utf8_strlen(trim($this->request->post['shipping_address']['postcode'])) > 10) {
						$json['error']['shipping']['postcode'] = $this->language->get('error_postcode');
					}
				}

				if(isset($mpcheckout_shipping_address_panel['fields']['country']) && $mpcheckout_shipping_address_panel['fields']['country'] == 1) {
					if ($this->request->post['shipping_address']['country_id'] == '') {
						$json['error']['shipping']['country'] = $this->language->get('error_country');
					}
				}

				if(isset($mpcheckout_shipping_address_panel['fields']['zone']) && $mpcheckout_shipping_address_panel['fields']['zone'] == 1) {
					if (!isset($this->request->post['shipping_address']['zone_id']) || $this->request->post['shipping_address']['zone_id'] == '' || !is_numeric($this->request->post['shipping_address']['zone_id'])) {
						$json['error']['shipping']['zone'] = $this->language->get('error_zone');
					}
				}

				$mpcheckout_account_panel = $this->config->get('mpcheckout_account_panel');
				$mpcheckout_customer_group_id = (!empty($mpcheckout_account_panel['customer_group_id']) ? $mpcheckout_account_panel['customer_group_id'] : $this->config->get('config_customer_group_id'));

				// Customer Group
				if (isset($this->request->post['signup']['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['signup']['customer_group_id'], $this->config->get('config_customer_group_display'))) {
					$customer_group_id = $this->request->post['signup']['customer_group_id'];
				} else if ($this->customer->isLogged()) {
					$customer_group_id = $this->config->get('config_customer_group_id');
				} else {
					$customer_group_id = $mpcheckout_customer_group_id;
				}

				// Custom field validation
				$custom_fields = $this->model_account_custom_field->getCustomFields($mpcheckout_customer_group_id);

				foreach ($custom_fields as $custom_field) {
					if (($custom_field['location'] == 'address') && $custom_field['required'] && empty($this->request->post['shipping_address']['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
						$json['error']['shipping']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
					} elseif (($custom_field['location'] == 'address') && ($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['shipping_address']['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
	                    $json['error']['shipping']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
	                }
				}

				if (isset($json['error']) && !isset($json['error']['shipping']['warning'])) {
					$json['error']['shipping']['warning'] = $this->language->get('error_carefully');
				}
			}

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && $this->config->get('mpcheckout_captcha')) {
				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

				if ($captcha) {
					$json['error']['captcha'] = $captcha;
					$json['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'), $json['error']);
					$json['error']['signup']['warning'] = $captcha;
				}
			}

			if (!$json) {
				$this->request->post['signup']['firstname'] = isset($this->request->post['signup']['firstname']) ? $this->request->post['signup']['firstname'] : '';

				$this->request->post['signup']['lastname'] = isset($this->request->post['signup']['lastname']) ? $this->request->post['signup']['lastname'] : '';

				$this->request->post['signup']['email'] = isset($this->request->post['signup']['email']) ? $this->request->post['signup']['email'] : '';

				$this->request->post['signup']['telephone'] = isset($this->request->post['signup']['telephone']) ? $this->request->post['signup']['telephone'] : '';

				$this->request->post['signup']['fax'] = isset($this->request->post['signup']['fax']) ? $this->request->post['signup']['fax'] : '';

				$this->request->post['signup']['company'] = isset($this->request->post['signup']['company']) ? $this->request->post['signup']['company'] : '';

				$this->request->post['signup']['address_1'] = isset($this->request->post['signup']['address_1']) ? $this->request->post['signup']['address_1'] : '';

				$this->request->post['signup']['address_2'] = isset($this->request->post['signup']['address_2']) ? $this->request->post['signup']['address_2'] : '';

				$this->request->post['signup']['city'] = isset($this->request->post['signup']['city']) ? $this->request->post['signup']['city'] : '';

				$this->request->post['signup']['postcode'] = isset($this->request->post['signup']['postcode']) ? $this->request->post['signup']['postcode'] : '';

				$this->request->post['signup']['country_id'] = isset($this->request->post['signup']['country_id']) ? $this->request->post['signup']['country_id'] : '';

				$this->request->post['signup']['zone_id'] = isset($this->request->post['signup']['zone_id']) ? $this->request->post['signup']['zone_id'] : '';

				$this->request->post['signup']['default'] = true;

				$customer_id = $this->model_account_customer->addCustomer($this->request->post['signup']);
				$customer_info = $this->model_account_customer->getCustomer($customer_id);

				$address_id = $this->model_account_address->addAddress($customer_id, $this->request->post['signup']);

				// Clear any previous login attempts for unregistered accounts.
				$this->model_account_customer->deleteLoginAttempts($this->request->post['signup']['email']);

				$this->session->data['account'] = 'register';
				$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);

				if ($customer_group_info && !empty($customer_info['status']) && !$customer_group_info['approval']) {
					$this->customer->login($this->request->post['signup']['email'], $this->request->post['signup']['password']);

					// Call Cart Construct
					$this->cart->__construct($this->registry);

					// Default Payment Address
					$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());

					if ($this->cart->hasShipping()) {
						if (!empty($this->request->post['same_address'])) {
							$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
						} else {
							$this->request->post['shipping_address']['firstname'] = isset($this->request->post['shipping_address']['firstname']) ? $this->request->post['shipping_address']['firstname'] : '';

							$this->request->post['shipping_address']['lastname'] = isset($this->request->post['shipping_address']['lastname']) ? $this->request->post['shipping_address']['lastname'] : '';

							$this->request->post['shipping_address']['company'] = isset($this->request->post['shipping_address']['company']) ? $this->request->post['shipping_address']['company'] : '';

							$this->request->post['shipping_address']['address_1'] = isset($this->request->post['shipping_address']['address_1']) ? $this->request->post['shipping_address']['address_1'] : '';

							$this->request->post['shipping_address']['address_2'] = isset($this->request->post['shipping_address']['address_2']) ? $this->request->post['shipping_address']['address_2'] : '';

							$this->request->post['shipping_address']['city'] = isset($this->request->post['shipping_address']['city']) ? $this->request->post['shipping_address']['city'] : '';

							$this->request->post['shipping_address']['postcode'] = isset($this->request->post['shipping_address']['postcode']) ? $this->request->post['shipping_address']['postcode'] : '';

							$this->request->post['shipping_address']['country_id'] = isset($this->request->post['shipping_address']['country_id']) ? $this->request->post['shipping_address']['country_id'] : '';

							$this->request->post['shipping_address']['zone_id'] = isset($this->request->post['shipping_address']['zone_id']) ? $this->request->post['shipping_address']['zone_id'] : '';

							/* $this->request->post['shipping_address']['custom_field'] = (isset($this->request->post['shipping_address']['custom_field']['address']) ? $this->request->post['shipping_address']['custom_field']['address'] : array()); */

							$address_id = $this->model_account_address->addAddress($customer_id, $this->request->post['shipping_address']);
							$this->session->data['shipping_address'] = $this->model_account_address->getAddress($address_id);

						}
					}
				} else {
					$json['redirect'] = $this->url->link('account/success', '', Mpcheckout\Manager::mpssl());
					$this->response->setOutput(json_encode($json));
				    $this->response->output();
				    exit();

				    // $json['error']['signup']['warning'] = $this->language->get('error_approved');
				}
			}
		}

		// Devliery Date
		$mpcheckout_date_panel = $this->config->get('mpcheckout_date_panel');
		if (!empty($mpcheckout_date_panel['status']) && $this->cart->hasShipping()) {
			$disabled_dates_array = !empty($mpcheckout_date_panel['disabled_dates']) ? explode(',', $mpcheckout_date_panel['disabled_dates']) : array();
			$disables_weeks_array = !empty($mpcheckout_date_panel['disables_weeks']) ? $mpcheckout_date_panel['disables_weeks'] : array();
			$delivery_date = isset($this->request->post['delivery_date']) ? $this->request->post['delivery_date'] : '';

			if(!empty($mpcheckout_date_panel['required']) && empty($delivery_date)) {
				$json['error']['deliverydate'] = $this->language->get('error_empty_deliverydate');
			}

			if(!empty($delivery_date)) {
				if(in_array($delivery_date, $disabled_dates_array)) {
					$json['error']['deliverydate'] = $this->language->get('error_disableddays_deliverydate');
				}
			}

			$delivery_date_number = date("w", strtotime($delivery_date));
			if(in_array($delivery_date_number, $disables_weeks_array)) {
				$json['error']['deliverydate'] = $this->language->get('error_weekdisabled_deliverydate');
			}
		}

		if(!$json) {
			// Add Order
			if ($this->cart->hasShipping()) {
				if (!isset($this->session->data['shipping_address'])) {
					$json['redirect'] = $this->url->link(Mpcheckout\Manager::MPREDIRECT, '', Mpcheckout\Manager::mpssl());
					$this->response->setoutput(json_encode($json));
				    $this->response->output();
				    exit();
				}

				if (!isset($this->session->data['shipping_method'])) {
					$json['redirect'] = $this->url->link(Mpcheckout\Manager::MPREDIRECT, '', Mpcheckout\Manager::mpssl());
					$this->response->setoutput(json_encode($json));
				    $this->response->output();
				    exit();
				}
			} else {
				unset($this->session->data['shipping_address']);
				unset($this->session->data['shipping_method']);
				unset($this->session->data['shipping_methods']);
			}

			if (!isset($this->session->data['payment_address'])) {
				$json['redirect'] = $this->url->link(Mpcheckout\Manager::MPREDIRECT, '', Mpcheckout\Manager::mpssl());
				$this->response->setoutput(json_encode($json));
			    $this->response->output();
			    exit();
			}

			if (!isset($this->session->data['payment_method'])) {
				$json['redirect'] = $this->url->link(Mpcheckout\Manager::MPREDIRECT, '', Mpcheckout\Manager::mpssl());
				$this->response->setoutput(json_encode($json));
			    $this->response->output();
			    exit();
			}

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
					$json['redirect'] = $this->url->link(Mpcheckout\Manager::MPREDIRECT, '', Mpcheckout\Manager::mpssl());
					$this->response->setoutput(json_encode($json));
				    $this->response->output();
				    exit();

					break;
				}
			}

			if (empty($json['redirect'])) {
				// Default Opencart code
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
						$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
					}
				}

				$sort_order = array();

				foreach ($totals as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $totals);

				$order_data['totals'] = $totals;

				$order_data['total'] = $total_data['total'];

				$this->load->language('checkout/checkout');

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

				if ($this->customer->isLogged()) {
					$this->load->model('account/customer');

					$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

					$order_data['customer_id'] = $this->customer->getId();
					$order_data['customer_group_id'] = $customer_info['customer_group_id'];
					$order_data['firstname'] = $customer_info['firstname'];
					$order_data['lastname'] = $customer_info['lastname'];
					$order_data['email'] = $customer_info['email'];
					$order_data['telephone'] = $customer_info['telephone'];
					$order_data['fax'] = $customer_info['fax'];
					$order_data['custom_field'] = json_decode($customer_info['custom_field'], true);
				} elseif (isset($this->session->data['guest'])) {
					$order_data['customer_id'] = 0;
					$order_data['customer_group_id'] = $this->session->data['guest']['customer_group_id'];
					$order_data['firstname'] = $this->session->data['guest']['firstname'];
					$order_data['lastname'] = $this->session->data['guest']['lastname'];
					$order_data['email'] = $this->session->data['guest']['email'];
					$order_data['telephone'] = $this->session->data['guest']['telephone'];
					$order_data['fax'] = $this->session->data['guest']['fax'];
					$order_data['custom_field'] = $this->session->data['guest']['custom_field'];
				}

				$order_data['payment_firstname'] = $this->session->data['payment_address']['firstname'];
				$order_data['payment_lastname'] = $this->session->data['payment_address']['lastname'];
				$order_data['payment_company'] = $this->session->data['payment_address']['company'];
				$order_data['payment_address_1'] = $this->session->data['payment_address']['address_1'];
				$order_data['payment_address_2'] = $this->session->data['payment_address']['address_2'];
				$order_data['payment_city'] = $this->session->data['payment_address']['city'];
				$order_data['payment_postcode'] = $this->session->data['payment_address']['postcode'];
				$order_data['payment_zone'] = $this->session->data['payment_address']['zone'];
				$order_data['payment_zone_id'] = $this->session->data['payment_address']['zone_id'];
				$order_data['payment_country'] = $this->session->data['payment_address']['country'];
				$order_data['payment_country_id'] = $this->session->data['payment_address']['country_id'];
				$order_data['payment_address_format'] = $this->session->data['payment_address']['address_format'];
				$order_data['payment_custom_field'] = (isset($this->session->data['payment_address']['custom_field']) ? $this->session->data['payment_address']['custom_field'] : array());

				if (isset($this->session->data['payment_method']['title'])) {
					$order_data['payment_method'] = $this->session->data['payment_method']['title'];
				} else {
					$order_data['payment_method'] = '';
				}

				if (isset($this->session->data['payment_method']['code'])) {
					$order_data['payment_code'] = $this->session->data['payment_method']['code'];
				} else {
					$order_data['payment_code'] = '';
				}

				if ($this->cart->hasShipping()) {
					$order_data['shipping_firstname'] = $this->session->data['shipping_address']['firstname'];
					$order_data['shipping_lastname'] = $this->session->data['shipping_address']['lastname'];
					$order_data['shipping_company'] = $this->session->data['shipping_address']['company'];
					$order_data['shipping_address_1'] = $this->session->data['shipping_address']['address_1'];
					$order_data['shipping_address_2'] = $this->session->data['shipping_address']['address_2'];
					$order_data['shipping_city'] = $this->session->data['shipping_address']['city'];
					$order_data['shipping_postcode'] = $this->session->data['shipping_address']['postcode'];
					$order_data['shipping_zone'] = $this->session->data['shipping_address']['zone'];
					$order_data['shipping_zone_id'] = $this->session->data['shipping_address']['zone_id'];
					$order_data['shipping_country'] = $this->session->data['shipping_address']['country'];
					$order_data['shipping_country_id'] = $this->session->data['shipping_address']['country_id'];
					$order_data['shipping_address_format'] = $this->session->data['shipping_address']['address_format'];
					$order_data['shipping_custom_field'] = (isset($this->session->data['shipping_address']['custom_field']) ? $this->session->data['shipping_address']['custom_field'] : array());

					if (isset($this->session->data['shipping_method']['title'])) {
						$order_data['shipping_method'] = $this->session->data['shipping_method']['title'];
					} else {
						$order_data['shipping_method'] = '';
					}

					if (isset($this->session->data['shipping_method']['code'])) {
						$order_data['shipping_code'] = $this->session->data['shipping_method']['code'];
					} else {
						$order_data['shipping_code'] = '';
					}

					if (isset($this->session->data['shipping_cargo_comment'])) {
						$order_data['shipping_cargo_comment'] = $this->session->data['shipping_cargo_comment'];
					} else {
						$order_data['shipping_cargo_comment'] = '';
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
					$order_data['shipping_cargo_comment'] = '';
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
						'mpplan_details'    => isset($product['mpplan_details']) ? $product['mpplan_details'] : array(),
						'reward'     => $product['reward']
					);
				}

				// Gift Voucher
				$order_data['vouchers'] = array();

				if (!empty($this->session->data['vouchers'])) {
					foreach ($this->session->data['vouchers'] as $voucher) {
						$v_token = token(10);

						$order_data['vouchers'][] = array(
							'description'      => $voucher['description'],
							'code'             => $v_token,
							'to_name'          => $voucher['to_name'],
							'to_email'         => $voucher['to_email'],
							'from_name'        => $voucher['from_name'],
							'from_email'       => $voucher['from_email'],
							'voucher_theme_id' => $voucher['voucher_theme_id'],
							'message'          => $voucher['message'],
							'amount'           => $voucher['amount']
						);
					}
				}

				/* Comment for Cargo Shipping */
				$order_data['shipping_cargo_comment'] = $this->session->data['shipping_cargo_comment'];

				// Comment
				$order_data['comment'] = $this->session->data['comment'];

				// Delivery Date Comment
				if(!empty($delivery_date)) {
					$mpcheckout_date_description = $this->config->get('mpcheckout_date_description');
					if(!empty($mpcheckout_date_description[$this->config->get('config_language_id')]['field_title'])) {
						$entry_delivery_date = $mpcheckout_date_description[$this->config->get('config_language_id')]['field_title'];
					} else {
						$entry_delivery_date = $this->language->get('entry_delivery_date');
					}

					if($order_data['comment']) {
						$order_data['comment'] .= "<br/><br/>";
					}

					$order_data['comment'] .= $entry_delivery_date;
					$order_data['comment'] .= ': ';
					$order_data['comment'] .= $delivery_date;
				}

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
				$order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
				$order_data['currency_code'] = $this->session->data['currency'];
				$order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
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

				// Add Order
				$this->session->data['order_id'] = $this->model_checkout_order->addOrder($order_data);

				$json['agree'] = isset($this->request->post['agree']) ? $this->request->post['agree'] : '';
				$this->session->data['comment'] = isset($this->request->post['comment']) ? strip_tags($this->request->post['comment']) : '';
				$json['comment'] = isset($this->request->post['comment']) ? strip_tags($this->request->post['comment']) : '';
				$this->session->data['shipping_cargo_comment'] = isset($this->request->post['shipping_cargo_comment']) ? strip_tags($this->request->post['shipping_cargo_comment']) : '';
				$json['shipping_cargo_comment'] = isset($this->request->post['shipping_cargo_comment']) ? strip_tags($this->request->post['shipping_cargo_comment']) : '';
				$json['lastconfirmbutton'] = true;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function saveAgreeSession() {
		$json = array();

		$this->session->data['agree_session'] = $this->request->post['agree_session'];

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function saveCommentSession() {
		$json = array();

		$this->session->data['comment_session'] = isset($this->request->post['comment_session']) ? $this->request->post['comment_session'] : '';

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function saveCargoShippingCommentSession() {
		$json = array();

		$this->session->data['shipping_cargo_comment_session'] = isset($this->request->post['shipping_cargo_comment_session']) ? $this->request->post['shipping_cargo_comment_session'] : '';

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}