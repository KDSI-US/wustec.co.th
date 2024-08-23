<?php
/* This file is under Git Control by KDSI. */
class ControllerMpcheckoutCheckout extends Controller {
	public function __construct($registry) {
		parent::__construct($registry);
		require_once(DIR_SYSTEM .'library/mpcheckout/manager.php');
	}

	private function themeclass() {
		if($this->config->get('config_theme')) {			
     		$custom_themename = $this->config->get('config_theme');
    	} else if($this->config->get('theme_default_directory')) {    		
			$custom_themename = $this->config->get('theme_default_directory');
		} else if($this->config->get('config_template')) {			
			$custom_themename = $this->config->get('config_template');
		} else{
			$custom_themename = 'default';
		}

		if(strpos($this->config->get('config_template'), 'journal2') === 0) {
			$custom_themename = 'journal2';
		}
		if (defined('JOURNAL3_ACTIVE')) {
			$custom_themename = 'journal3';
		}
		
		if(empty($custom_themename)) {
			$custom_themename = 'default';
		}

		if(isset($custom_themename) && $custom_themename == 'journal2') {
			$mcheckout_class = 'journal-mcheckout';
		} else{
			$mcheckout_class = 'default-mcheckout';
		}

		return $mcheckout_class;

	}

	private function themename() {
		if($this->config->get('config_theme')) {			
     		$custom_themename = $this->config->get('config_theme');
    	} else if($this->config->get('theme_default_directory')) {    		
			$custom_themename = $this->config->get('theme_default_directory');
		} else if($this->config->get('config_template')) {			
			$custom_themename = $this->config->get('config_template');
		} else{
			$custom_themename = 'default';
		}

		if(strpos($this->config->get('config_template'), 'journal2') === 0) {
			$custom_themename = 'journal2';
		}
		if (defined('JOURNAL3_ACTIVE')) {
			$custom_themename = 'journal3';
		}
		
		if(empty($custom_themename)) {
			$custom_themename = 'default';
		}

		return $custom_themename;
	}


	public function index() {
		if(!$this->config->get('mpcheckout_status')) {
			$this->response->redirect($this->url->link('checkout/checkout', '', Mpcheckout\Manager::mpssl()));
		}else if(isset($this->request->get['route']) && $this->request->get['route'] == 'mpcheckout/checkout') {
			$this->response->redirect($this->url->link('checkout/checkout', '', Mpcheckout\Manager::mpssl()));
		}

		$this->load->language('mpcheckout/checkout');

		$data['themename'] = $this->themename();
		$data['themeclass'] = $this->themeclass();

		if (file_exists(DIR_TEMPLATE . $data['themename'] . '/stylesheet/mpcheckout/checkout.css')) {
			$this->document->addStyle('catalog/view/theme/'. $data['themename'] .'/stylesheet/mpcheckout/checkout.css');
		} else {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/mpcheckout/checkout.css');
		}


		$this->document->addScript('catalog/view/javascript/mpcheckout/checkout.js');

		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

		$mpcheckout_page_description = $this->config->get('mpcheckout_page_description');
		if(!empty($mpcheckout_page_description[$this->config->get('config_language_id')]['heading_title'])) {
			 $this->language->setmpcheckoutlanguage('heading_title', $mpcheckout_page_description[$this->config->get('config_language_id')]['heading_title']);
		}

		if($this->customer->isLogged()) {
			$data['customer_message'] = !empty($mpcheckout_page_description[$this->config->get('config_language_id')]['message_logged']) ? html_entity_decode($mpcheckout_page_description[$this->config->get('config_language_id')]['message_logged'], ENT_QUOTES, 'UTF-8') : '';
		} else {
			$data['customer_message'] = !empty($mpcheckout_page_description[$this->config->get('config_language_id')]['message_register']) ? html_entity_decode($mpcheckout_page_description[$this->config->get('config_language_id')]['message_register'], ENT_QUOTES, 'UTF-8') : '';
		}

		$mpcheckout_color = $this->config->get('mpcheckout_color');	

		$data['background_container_heading'] 	= isset($mpcheckout_color['background_container_heading']) ? $mpcheckout_color['background_container_heading'] : '';

		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$data['heading_title'] = $this->language->get('heading_title');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', '', Mpcheckout\Manager::mpssl())
		);

		if(!$this->config->get('mpcheckout_stopcartpage')) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_cart'),
				'href' => $this->url->link('checkout/cart', '', Mpcheckout\Manager::mpssl())
			);
		}

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link(Mpcheckout\Manager::MPREDIRECT, '', Mpcheckout\Manager::mpssl())
		);

		if ($this->cart->hasProducts() || !empty($this->session->data['vouchers'])) {
			if (isset($this->session->data['error'])) {
				$data['error_warning'] = $this->session->data['error'];

				unset($this->session->data['error']);
			} else {
				$data['error_warning'] = '';
			}

			if ($this->config->get('config_customer_price') && !$this->customer->isLogged()) {
				$data['attention'] = sprintf($this->language->get('text_login'), $this->url->link('account/login'), $this->url->link('account/register'));
			} else {
				$data['attention'] = '';
			}

			if (isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];

				unset($this->session->data['success']);
			} else {
				$data['success'] = '';
			}

			$data['logged'] = $this->customer->isLogged();

			/* For SHipping Method =============== */
			if($data['logged']) {
				// Default Shipping Address
				$this->load->model('account/address');
				$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());

				/*unset($this->session->data['shipping_method']);
				unset($this->session->data['shipping_methods']);*/
			}

			if(empty($this->session->data['shipping_address']['country_id'])) {
				$this->session->data['shipping_address'] = array();

				$this->session->data['shipping_address']['firstname'] = '';
				$this->session->data['shipping_address']['lastname'] = '';
				$this->session->data['shipping_address']['company'] = '';
				$this->session->data['shipping_address']['address_1'] = '';
				$this->session->data['shipping_address']['address_2'] = '';
				$this->session->data['shipping_address']['postcode'] = '';
				$this->session->data['shipping_address']['city'] = '';
				$this->session->data['shipping_address']['country_id'] = $this->config->get('mpcheckout_country_id');
				$this->session->data['shipping_address']['zone_id'] = '';

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

				$this->session->data['shipping_address']['custom_field'] = array();
			}

			/* For Payment Method =============== */
			if($data['logged']) {
				// Default Payment Address
				$this->load->model('account/address');
				$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());

				/*unset($this->session->data['payment_method']);
				unset($this->session->data['payment_methods']);*/
			}

			if(empty($this->session->data['payment_address']['country_id'])) {
				$this->session->data['payment_address'] = array();

				$this->session->data['payment_address']['firstname'] = '';
				$this->session->data['payment_address']['lastname'] = '';
				$this->session->data['payment_address']['company'] = '';
				$this->session->data['payment_address']['address_1'] = '';
				$this->session->data['payment_address']['address_2'] = '';
				$this->session->data['payment_address']['postcode'] = '';
				$this->session->data['payment_address']['city'] = '';
				$this->session->data['payment_address']['country_id'] = $this->config->get('mpcheckout_country_id');
				$this->session->data['payment_address']['zone_id'] = '';
				
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

				$this->session->data['payment_address']['custom_field'] = array();
			}

			$mpcheckout_account_button = $this->config->get('mpcheckout_account_button');
			$mpcheckout_shoppingcart_panel = $this->config->get('mpcheckout_shoppingcart_panel');
			$mpcheckout_date_panel = $this->config->get('mpcheckout_date_panel');
			$mpcheckout_account_panel = $this->config->get('mpcheckout_account_panel');

			if(!empty($mpcheckout_account_button['default_account_button'])) {
				$data['default_account_button'] = $mpcheckout_account_button['default_account_button'];
			} else{
				$data['default_account_button'] = 'register';
			}

			if(!empty($mpcheckout_account_panel['delivery_address_check'])) {
				$data['delivery_address_check'] = $mpcheckout_account_panel['delivery_address_check'];
			} else{
				$data['delivery_address_check'] = '';
			}

			if(!empty($mpcheckout_shoppingcart_panel['cart_status'])) {
				$data['cart_status'] = true;
			} else{
				$data['cart_status'] = false;
			}

			if(!empty($mpcheckout_date_panel['status'])) {
				$data['delivery_date_status'] = true;
			} else{
				$data['delivery_date_status'] = false;
			}

			if(isset($this->session->data['agree_session'])) {
				unset($this->session->data['agree_session']);
			}

			if(isset($this->session->data['comment_session'])) {
				unset($this->session->data['comment_session']);
			}

			if(isset($this->session->data['shipping_cargo_comment_session'])) {
				unset($this->session->data['shipping_cargo_comment_session']);
			}

			$mpcheckout_confirm_order_description = $this->config->get('mpcheckout_confirm_order_description');
			if(!empty($mpcheckout_confirm_order_description[$this->config->get('config_language_id')]['title'])) {
				 $this->language->setmpcheckoutlanguage('panel_confirm_order', $mpcheckout_confirm_order_description[$this->config->get('config_language_id')]['title']);
			}

			$data['panel_confirm_order'] = $this->language->get('panel_confirm_order');


			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$data['shoppingcart_controller'] = $this->load->controller('mpcheckout/shoppingcart');
			$data['account_option_button_controller'] = $this->load->controller('mpcheckout/account_option_button');
			$data['login_controller'] = $this->load->controller('mpcheckout/login');
			$data['shipping_method_controller'] = $this->load->controller('mpcheckout/shipping_method');
			$data['payment_method_controller'] = $this->load->controller('mpcheckout/payment_method');	
			$data['delivery_date_controller'] = $this->load->controller('mpcheckout/delivery_date');
			$data['checkout_button_controller'] = $this->load->controller('mpcheckout/checkout_button');
			$data['checkout_style_controller'] = $this->load->controller('mpcheckout/checkout_style');

			$data['signup_controller'] = $this->load->controller('mpcheckout/signup');			
			$data['shipping_address_controller'] = $this->load->controller('mpcheckout/shipping_address');
			$data['payment_address_controller'] = $this->load->controller('mpcheckout/payment_address');
						

			$mpcheckout_confirm_panel = $this->config->get('mpcheckout_confirm_panel');
			if(!empty($mpcheckout_confirm_panel['show_comment'])) {
				$data['show_comment'] = true;
			} else{
				$data['show_comment'] = false;
			}

			$data['shipping_required'] = $this->cart->hasShipping();
			
			if($this->config->get('mpcheckout_template')) {
				$data['mpcheckout_template'] = $this->config->get('mpcheckout_template');
			} else {
				$data['mpcheckout_template'] = 'checkout_1';
			}

			$this->response->setOutput($this->load->view('mpcheckout/'. $data['mpcheckout_template'], $data));
		} else {
			$data['heading_title'] = $this->language->get('heading_title');

			$data['text_error'] = $this->language->get('text_empty');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['continue'] = $this->url->link('common/home', '', Mpcheckout\Manager::mpssl());

			unset($this->session->data['success']);

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	public function country() {
		$json = array();

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function customfield() {
		$json = array();

		$this->load->model('account/custom_field');

		$mpcheckout_account_panel = $this->config->get('mpcheckout_account_panel');
		$mpcheckout_customer_group_id = (!empty($mpcheckout_account_panel['customer_group_id']) ? $mpcheckout_account_panel['customer_group_id'] : $this->config->get('config_customer_group_id'));

		// Customer Group
		if (isset($this->request->get['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->get['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->get['customer_group_id'];
		} else if ($this->customer->isLogged()) {
			$customer_group_id = $this->config->get('config_customer_group_id');
		} else {
			$customer_group_id = $mpcheckout_customer_group_id;
		}

		$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

		foreach ($custom_fields as $custom_field) {
			$json[] = array(
				'custom_field_id' => $custom_field['custom_field_id'],
				'required'        => $custom_field['required']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
