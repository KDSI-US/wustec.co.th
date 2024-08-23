<?php
/* This file is under Git Control by KDSI. */
class ControllerMpCheckoutShippingAddress extends Controller {
	public function __construct($registry) {
		parent::__construct($registry);
		require_once(DIR_SYSTEM .'library/mpcheckout/manager.php');
	}

	public function index() {
		$data = array();

		$this->content($data);

		return $this->load->view('mpcheckout/shipping_address', $data);
	}

	public function ajax() {
		$data = array();

		$this->content($data);

		$this->response->setOutput($this->load->view('mpcheckout/shipping_address', $data));
	}

	protected function content(&$data) {
		$this->load->language('mpcheckout/checkout');

		$mpcheckout_shipping_address_description = $this->config->get('mpcheckout_shipping_address_description');

		if(!empty($mpcheckout_shipping_address_description[$this->config->get('config_language_id')]['title'])) {
			 $this->language->setmpcheckoutlanguage('panel_shipping_details', $mpcheckout_shipping_address_description[$this->config->get('config_language_id')]['title']);
		}

		$data['text_address_existing'] = $this->language->get('text_address_existing');
		$data['text_address_new'] = $this->language->get('text_address_new');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_loading'] = $this->language->get('text_loading');

		$data['button_upload'] = $this->language->get('button_upload');
		$data['button_checkout'] = $this->language->get('button_checkout');

		$data['panel_shipping_details'] = $this->language->get('panel_shipping_details');

		$data['entry_postcode'] = $this->language->get('entry_postcode');
		$data['entry_firstname'] = $this->language->get('entry_firstname');
		$data['entry_lastname'] = $this->language->get('entry_lastname');
		$data['entry_company'] = $this->language->get('entry_company');
		$data['entry_country'] = $this->language->get('entry_country');
		$data['entry_address_1'] = $this->language->get('entry_address_1');
		$data['entry_address_2'] = $this->language->get('entry_address_2');
		$data['entry_city'] = $this->language->get('entry_city');
		$data['entry_zone'] = $this->language->get('entry_zone');

		$data['hold_postcode'] = $this->language->get('hold_postcode');
		$data['hold_firstname']	= $this->language->get('hold_firstname');
		$data['hold_lastname']	= $this->language->get('hold_lastname');
		$data['hold_company']	= $this->language->get('hold_company');
		$data['hold_address_1']	= $this->language->get('hold_address_1');
		$data['hold_address_2']	= $this->language->get('hold_address_2');
		$data['hold_city']	= $this->language->get('hold_city');

		if (isset($this->session->data['shipping_address']['address_id'])) {
			$data['address_id'] = $this->session->data['shipping_address']['address_id'];
		} else {
			$data['address_id'] = $this->customer->getAddressId();
		}

		$this->load->model('account/address');

		if($this->customer->getId()) {
			$data['addresses'] = $this->model_account_address->getAddresses();
		} else {
			$data['addresses'] = array();
		}

		if (isset($this->session->data['shipping_address']['postcode'])) {
			$data['postcode'] = $this->session->data['shipping_address']['postcode'];
		} else {
			$data['postcode'] = '';
		}

		if (!empty($this->session->data['shipping_address']['country_id'])) {
			$data['country_id'] = $this->session->data['shipping_address']['country_id'];
		} else {
			$data['country_id'] = $this->config->get('mpcheckout_country_id');
		}

		if (!empty($this->session->data['shipping_address']['zone_id'])) {
			$data['zone_id'] = $this->session->data['shipping_address']['zone_id'];
		} else {
			$data['zone_id'] = $this->config->get('mpcheckout_zone_id');
		}

		$data['logged'] = $this->customer->isLogged();

		$mpcheckout_shipping_address_panel = $this->config->get('mpcheckout_shipping_address_panel');
		if(!empty($mpcheckout_shipping_address_panel['fields'])) {
			$data['address_fields'] = (array)$mpcheckout_shipping_address_panel['fields'];
		} else{
			$data['address_fields'] = array();
		}

		$mpcheckout_account_panel = $this->config->get('mpcheckout_account_panel');
		$mpcheckout_customer_group_id = (!empty($mpcheckout_account_panel['customer_group_id']) ? $mpcheckout_account_panel['customer_group_id'] : $this->config->get('config_customer_group_id'));

		if(isset($this->request->post['signup']['customer_group_id'])) {
			$customer_group_id = $this->request->post['signup']['customer_group_id'];
		} else if ($this->customer->isLogged()) {
			$customer_group_id = $this->config->get('config_customer_group_id');
		} else {
			$customer_group_id = $mpcheckout_customer_group_id;
		}

		// Country
		$this->load->model('localisation/country');
		$data['countries'] = $this->model_localisation_country->getCountries();

		// Custom Fields
		$this->load->model('account/custom_field');
		$data['custom_fields'] = $this->model_account_custom_field->getCustomFields($customer_group_id);


		$data['logged_display_ship_not_required'] = false;
		if($this->customer->isLogged()) {
			if(!$this->cart->hasShipping()) {
				$data['logged_display_ship_not_required'] = true;
			}
		}
	}
}