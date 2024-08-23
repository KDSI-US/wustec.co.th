<?php
/* This file is under Git Control by KDSI. */
class ControllerMpcheckoutPaymentMethod extends Controller {
	public function __construct($registry) {
		parent::__construct($registry);
		require_once(DIR_SYSTEM .'library/mpcheckout/manager.php');
	}

	public function index() {
		$data = array();

		$this->content($data);

		return $this->load->view('mpcheckout/payment_method', $data);
	}
	public function ajax() {
		// Add Shipping Address When Change Country Or Zone
		if ($this->customer->isLogged()) {
			if(!empty($this->request->post['same_address']) && isset($this->request->post['payment_address']['payment_address']) && $this->request->post['payment_address']['payment_address'] == 'existing') {
				$address_id = isset($this->request->post['payment_address']['address_id']) ? $this->request->post['payment_address']['address_id'] : '';

				$this->load->model('account/address');
				$this->session->data['shipping_address'] = $this->model_account_address->getAddress($address_id);
			} else if(empty($this->request->post['same_address']) && isset($this->request->post['shipping_address']['shipping_address']) && $this->request->post['shipping_address']['shipping_address'] == 'existing') {
				$address_id = isset($this->request->post['shipping_address']['address_id']) ? $this->request->post['shipping_address']['address_id'] : '';

				$this->load->model('account/address');
				$this->session->data['shipping_address'] = $this->model_account_address->getAddress($address_id);
			} else {
				if(isset($this->request->post['same_address']) && $this->request->post['same_address'] == '1') {
					$this->session->data['shipping_address']['country_id'] = isset($this->request->post['payment_address']['country_id']) ? $this->request->post['payment_address']['country_id'] : '';
					$this->session->data['shipping_address']['zone_id'] = isset($this->request->post['payment_address']['zone_id']) ? $this->request->post['payment_address']['zone_id'] : '';

					$this->session->data['shipping_address']['firstname'] = isset($this->request->post['payment_address']['firstname']) ? $this->request->post['payment_address']['firstname'] : '';
					$this->session->data['shipping_address']['lastname'] = isset($this->request->post['payment_address']['lastname']) ? $this->request->post['payment_address']['lastname'] : '';
					$this->session->data['shipping_address']['company'] = isset($this->request->post['payment_address']['company']) ? $this->request->post['payment_address']['company'] : '';
					$this->session->data['shipping_address']['address_1'] = isset($this->request->post['payment_address']['address_1']) ? $this->request->post['payment_address']['address_1'] : '';
					$this->session->data['shipping_address']['address_2'] = isset($this->request->post['payment_address']['address_2']) ? $this->request->post['payment_address']['address_2'] : '';
					$this->session->data['shipping_address']['postcode'] = isset($this->request->post['payment_address']['postcode']) ? $this->request->post['payment_address']['postcode'] : '';
					$this->session->data['shipping_address']['city'] = isset($this->request->post['payment_address']['city']) ? $this->request->post['payment_address']['city'] : '';

					$this->load->model('localisation/country');
					$country_info = $this->model_localisation_country->getCountry($this->session->data['shipping_address']['country_id']);
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

					$this->load->model('localisation/zone');
					$zone_info = $this->model_localisation_zone->getZone($this->session->data['shipping_address']['zone_id']);
					if ($zone_info) {
						$this->session->data['shipping_address']['zone'] = $zone_info['name'];
						$this->session->data['shipping_address']['zone_code'] = $zone_info['code'];
					} else {
						$this->session->data['shipping_address']['zone'] = '';
						$this->session->data['shipping_address']['zone_code'] = '';
					}

					if (isset($this->request->post['payment_address']['custom_field']['address'])) {
						$this->session->data['shipping_address']['custom_field'] = $this->request->post['payment_address']['custom_field']['address'];
					} else {
						$this->session->data['shipping_address']['custom_field'] = array();
					}
				} else {
					$this->session->data['shipping_address']['country_id'] = isset($this->request->post['shipping_address']['country_id']) ? $this->request->post['shipping_address']['country_id'] : '';
					$this->session->data['shipping_address']['zone_id'] = isset($this->request->post['shipping_address']['zone_id']) ? $this->request->post['shipping_address']['zone_id'] : '';

					$this->session->data['shipping_address']['firstname'] = isset($this->request->post['shipping_address']['firstname']) ? $this->request->post['shipping_address']['firstname'] : '';
					$this->session->data['shipping_address']['lastname'] = isset($this->request->post['shipping_address']['lastname']) ? $this->request->post['shipping_address']['lastname'] : '';
					$this->session->data['shipping_address']['company'] = isset($this->request->post['shipping_address']['company']) ? $this->request->post['shipping_address']['company'] : '';
					$this->session->data['shipping_address']['address_1'] = isset($this->request->post['shipping_address']['address_1']) ? $this->request->post['shipping_address']['address_1'] : '';
					$this->session->data['shipping_address']['address_2'] = isset($this->request->post['shipping_address']['address_2']) ? $this->request->post['shipping_address']['address_2'] : '';
					$this->session->data['shipping_address']['postcode'] = isset($this->request->post['shipping_address']['postcode']) ? $this->request->post['shipping_address']['postcode'] : '';
					$this->session->data['shipping_address']['city'] = isset($this->request->post['shipping_address']['city']) ? $this->request->post['shipping_address']['city'] : '';

					$this->load->model('localisation/country');
					$country_info = $this->model_localisation_country->getCountry($this->session->data['shipping_address']['country_id']);
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

					$this->load->model('localisation/zone');
					$zone_info = $this->model_localisation_zone->getZone($this->session->data['shipping_address']['zone_id']);
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
		} else {
			if(isset($this->request->post['same_address']) && $this->request->post['same_address'] == '1') {
				$this->session->data['shipping_address']['country_id'] = isset($this->request->post['signup']['country_id']) ? $this->request->post['signup']['country_id'] : '';
				$this->session->data['shipping_address']['zone_id'] = isset($this->request->post['signup']['zone_id']) ? $this->request->post['signup']['zone_id'] : '';
				$this->session->data['shipping_address']['firstname'] = isset($this->request->post['signup']['firstname']) ? $this->request->post['signup']['firstname'] : '';
				$this->session->data['shipping_address']['lastname'] = isset($this->request->post['signup']['lastname']) ? $this->request->post['signup']['lastname'] : '';
				$this->session->data['shipping_address']['company'] = isset($this->request->post['signup']['company']) ? $this->request->post['signup']['company'] : '';
				$this->session->data['shipping_address']['address_1'] = isset($this->request->post['signup']['address_1']) ? $this->request->post['signup']['address_1'] : '';
				$this->session->data['shipping_address']['address_2'] = isset($this->request->post['signup']['address_2']) ? $this->request->post['signup']['address_2'] : '';
				$this->session->data['shipping_address']['postcode'] = isset($this->request->post['signup']['postcode']) ? $this->request->post['signup']['postcode'] : '';
				$this->session->data['shipping_address']['city'] = isset($this->request->post['signup']['city']) ? $this->request->post['signup']['city'] : '';

				$this->load->model('localisation/country');
				$country_info = $this->model_localisation_country->getCountry($this->session->data['shipping_address']['country_id']);
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

				$this->load->model('localisation/zone');
				$zone_info = $this->model_localisation_zone->getZone($this->session->data['shipping_address']['zone_id']);
				if ($zone_info) {
					$this->session->data['shipping_address']['zone'] = $zone_info['name'];
					$this->session->data['shipping_address']['zone_code'] = $zone_info['code'];
				} else {
					$this->session->data['shipping_address']['zone'] = '';
					$this->session->data['shipping_address']['zone_code'] = '';
				}

				if (isset($this->request->post['signup']['custom_field']['address'])) {
					$this->session->data['shipping_address']['custom_field'] = $this->request->post['signup']['custom_field']['address'];
				} else {
					$this->session->data['shipping_address']['custom_field'] = array();
				}
			} else{
				$this->session->data['shipping_address']['country_id'] = isset($this->request->post['shipping_address']['country_id']) ? $this->request->post['shipping_address']['country_id'] : '';
				$this->session->data['shipping_address']['zone_id'] = isset($this->request->post['shipping_address']['zone_id']) ? $this->request->post['shipping_address']['zone_id'] : '';

				$this->session->data['shipping_address']['firstname'] = isset($this->request->post['shipping_address']['firstname']) ? $this->request->post['shipping_address']['firstname'] : '';
				$this->session->data['shipping_address']['lastname'] = isset($this->request->post['shipping_address']['lastname']) ? $this->request->post['shipping_address']['lastname'] : '';
				$this->session->data['shipping_address']['company'] = isset($this->request->post['shipping_address']['company']) ? $this->request->post['shipping_address']['company'] : '';
				$this->session->data['shipping_address']['address_1'] = isset($this->request->post['shipping_address']['address_1']) ? $this->request->post['shipping_address']['address_1'] : '';
				$this->session->data['shipping_address']['address_2'] = isset($this->request->post['shipping_address']['address_2']) ? $this->request->post['shipping_address']['address_2'] : '';
				$this->session->data['shipping_address']['postcode'] = isset($this->request->post['shipping_address']['postcode']) ? $this->request->post['shipping_address']['postcode'] : '';
				$this->session->data['shipping_address']['city'] = isset($this->request->post['shipping_address']['city']) ? $this->request->post['shipping_address']['city'] : '';

				$this->load->model('localisation/country');
				$country_info = $this->model_localisation_country->getCountry($this->session->data['shipping_address']['country_id']);
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

				$this->load->model('localisation/zone');
				$zone_info = $this->model_localisation_zone->getZone($this->session->data['shipping_address']['zone_id']);
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

		// Add Payment Address When Change Country Or Zone
		if ($this->customer->isLogged()) {
			if(isset($this->request->post['payment_address']['payment_address']) && $this->request->post['payment_address']['payment_address'] == 'existing') {
				$address_id = isset($this->request->post['payment_address']['address_id']) ? $this->request->post['payment_address']['address_id'] : '';
				$this->load->model('account/address');
				$this->session->data['payment_address'] = $this->model_account_address->getAddress($address_id);
			} else {
				$this->session->data['payment_address']['country_id'] = isset($this->request->post['payment_address']['country_id']) ? $this->request->post['payment_address']['country_id'] : '';
				$this->session->data['payment_address']['zone_id'] = isset($this->request->post['payment_address']['zone_id']) ? $this->request->post['payment_address']['zone_id'] : '';

				$this->session->data['payment_address']['firstname'] = isset($this->request->post['payment_address']['firstname']) ? $this->request->post['payment_address']['firstname'] : '';
				$this->session->data['payment_address']['lastname'] = isset($this->request->post['payment_address']['lastname']) ? $this->request->post['payment_address']['lastname'] : '';
				$this->session->data['payment_address']['company'] = isset($this->request->post['payment_address']['company']) ? $this->request->post['payment_address']['company'] : '';
				$this->session->data['payment_address']['address_1'] = isset($this->request->post['payment_address']['address_1']) ? $this->request->post['payment_address']['address_1'] : '';
				$this->session->data['payment_address']['address_2'] = isset($this->request->post['payment_address']['address_2']) ? $this->request->post['payment_address']['address_2'] : '';
				$this->session->data['payment_address']['postcode'] = isset($this->request->post['payment_address']['postcode']) ? $this->request->post['payment_address']['postcode'] : '';
				$this->session->data['payment_address']['city'] = isset($this->request->post['payment_address']['city']) ? $this->request->post['payment_address']['city'] : '';

				$this->load->model('localisation/country');
				$country_info = $this->model_localisation_country->getCountry($this->session->data['payment_address']['country_id']);
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

				$this->load->model('localisation/zone');
				$zone_info = $this->model_localisation_zone->getZone($this->session->data['payment_address']['zone_id']);
				if ($zone_info) {
					$this->session->data['payment_address']['zone'] = $zone_info['name'];
					$this->session->data['payment_address']['zone_code'] = $zone_info['code'];
				} else {
					$this->session->data['payment_address']['zone'] = '';
					$this->session->data['payment_address']['zone_code'] = '';
				}

				if (isset($this->request->post['payment_address']['custom_field']['address'])) {
					$this->session->data['payment_address']['custom_field'] = $this->request->post['payment_address']['custom_field']['address'];
				} else {
					$this->session->data['payment_address']['custom_field'] = array();
				}
			}
		} else{
			$this->session->data['payment_address']['country_id'] = isset($this->request->post['signup']['country_id']) ? $this->request->post['signup']['country_id'] : '';
			$this->session->data['payment_address']['zone_id'] = isset($this->request->post['signup']['zone_id']) ? $this->request->post['signup']['zone_id'] : '';

			$this->session->data['payment_address']['firstname'] = isset($this->request->post['signup']['firstname']) ? $this->request->post['signup']['firstname'] : '';
			$this->session->data['payment_address']['lastname'] = isset($this->request->post['signup']['lastname']) ? $this->request->post['signup']['lastname'] : '';
			$this->session->data['payment_address']['company'] = isset($this->request->post['signup']['company']) ? $this->request->post['signup']['company'] : '';
			$this->session->data['payment_address']['address_1'] = isset($this->request->post['signup']['address_1']) ? $this->request->post['signup']['address_1'] : '';
			$this->session->data['payment_address']['address_2'] = isset($this->request->post['signup']['address_2']) ? $this->request->post['signup']['address_2'] : '';
			$this->session->data['payment_address']['postcode'] = isset($this->request->post['signup']['postcode']) ? $this->request->post['signup']['postcode'] : '';
			$this->session->data['payment_address']['city'] = isset($this->request->post['signup']['city']) ? $this->request->post['signup']['city'] : '';

			$this->load->model('localisation/country');
			$country_info = $this->model_localisation_country->getCountry($this->session->data['payment_address']['country_id']);
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

			$this->load->model('localisation/zone');
			$zone_info = $this->model_localisation_zone->getZone($this->session->data['payment_address']['zone_id']);
			if ($zone_info) {
				$this->session->data['payment_address']['zone'] = $zone_info['name'];
				$this->session->data['payment_address']['zone_code'] = $zone_info['code'];
			} else {
				$this->session->data['payment_address']['zone'] = '';
				$this->session->data['payment_address']['zone_code'] = '';
			}

			if (isset($this->request->post['signup']['custom_field']['address'])) {
				$this->session->data['payment_address']['custom_field'] = $this->request->post['signup']['custom_field']['address'];
			} else {
				$this->session->data['payment_address']['custom_field'] = array();
			}
		}

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

		$data = array();

		$this->content($data);

		$this->response->setOutput($this->load->view('mpcheckout/payment_method', $data));

	}

	protected function content(&$data) {
		$this->load->language('mpcheckout/checkout');

		$mpcheckout_payment_method_description = $this->config->get('mpcheckout_payment_method_description');
		if(!empty($mpcheckout_payment_method_description[$this->config->get('config_language_id')]['title'])) {
			 $this->language->setmpcheckoutlanguage('panel_payment_method', $mpcheckout_payment_method_description[$this->config->get('config_language_id')]['title']);
		}

		$data['panel_payment_method'] = $this->language->get('panel_payment_method');

		$data['text_payment_method'] = $this->language->get('text_payment_method');
		$data['text_comments'] = $this->language->get('text_comments');
		$data['text_loading'] = $this->language->get('text_loading');

		$data['button_continue'] = $this->language->get('button_continue');

		if (empty($this->session->data['payment_methods'])) {
			$data['error_warning'] = sprintf($this->language->get('error_no_payment'), $this->url->link('information/contact', '', Mpcheckout\Manager::mpssl()));
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['payment_methods'])) {
			$data['payment_methods'] = $this->session->data['payment_methods'];
		} else {
			$data['payment_methods'] = array();
		}

		if (isset($this->session->data['payment_method']['code'])) {
			$data['code'] = $this->session->data['payment_method']['code'];
		} else {
			$data['code'] = '';
		}

		$data['scripts'] = $this->document->getScripts();

		if ($this->config->get('config_checkout_id')) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));

			if ($information_info) {
				$data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_checkout_id'), Mpcheckout\Manager::mpssl()), $information_info['title'], $information_info['title']);
			} else {
				$data['text_agree'] = '';
			}
		} else {
			$data['text_agree'] = '';
		}

		if (isset($this->session->data['agree'])) {
			$data['agree'] = $this->session->data['agree'];
		} else {
			$data['agree'] = '';
		}

		$data['mpcheckout_payment_method_tables'] = array();
		$this->load->model('tool/image');
		$mpcheckout_payment_method_tables = (array)$this->config->get('mpcheckout_payment_method_table');
		if($mpcheckout_payment_method_tables) {
			foreach ($mpcheckout_payment_method_tables as $key => $mpcheckout_payment_method_table) {
				if (!empty($mpcheckout_payment_method_table['image']) && is_file(DIR_IMAGE . $mpcheckout_payment_method_table['image'])) {
					$thumb = $this->model_tool_image->resize($mpcheckout_payment_method_table['image'], 40, 40);
				} else {
					$thumb = '';
				}

				$data['mpcheckout_payment_method_tables'][$key] = array(
					'thumb'			=> $thumb,
				);
			}
		}
	}

	public function save() {
		$this->load->language('mpcheckout/checkout');

		$json = array();

		// Validate if payment address has been set.
		if (!isset($this->session->data['payment_address'])) {
			$json['redirect'] = $this->url->link(Mpcheckout\Manager::MPREDIRECT, '', Mpcheckout\Manager::mpssl());
		}

		if (!isset($this->request->post['payment_method'])) {
			$json['error']['warning'] = $this->language->get('error_payment');
		} elseif (!isset($this->session->data['payment_methods'][$this->request->post['payment_method']])) {
			$json['error']['warning'] = $this->language->get('error_payment');
		}

		if (!$json) {
			$this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];

			$json['success'] = true;
		}

		if(isset($json['error'])) {
			unset($this->session->data['payment_method']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
