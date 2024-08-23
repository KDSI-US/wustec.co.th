<?php
/* This file is under Git Control by KDSI. */
class ControllerMpCheckoutSignup extends Controller {
	public function __construct($registry) {
		parent::__construct($registry);
		require_once(DIR_SYSTEM .'library/mpcheckout/manager.php');
	}

	public function index() {
		$data = array();

		$this->content($data);

		return $this->load->view('mpcheckout/signup', $data);
	}
	public function ajax() {
		$data = array();

		$this->content($data);

		$this->response->setOutput($this->load->view('mpcheckout/signup', $data));
	}

	protected function content(&$data) {
		$this->load->language('mpcheckout/checkout');

		$mpcheckout_account_panel_description = $this->config->get('mpcheckout_account_panel_description');
		$mpcheckout_shipping_address_description = $this->config->get('mpcheckout_shipping_address_description');
		if(!empty($mpcheckout_account_panel_description[$this->config->get('config_language_id')]['personal_title'])) {
			 $this->language->setmpcheckoutlanguage('panel_signup_details', $mpcheckout_account_panel_description[$this->config->get('config_language_id')]['personal_title']);
		}

		if(!empty($mpcheckout_account_panel_description[$this->config->get('config_language_id')]['personal_guest_title'])) {
			 $this->language->setmpcheckoutlanguage('panel_guest_details', $mpcheckout_account_panel_description[$this->config->get('config_language_id')]['personal_guest_title']);
		}

		if(!empty($mpcheckout_account_panel_description[$this->config->get('config_language_id')]['password'])) {
			 $this->language->setmpcheckoutlanguage('panel_password', $mpcheckout_account_panel_description[$this->config->get('config_language_id')]['password']);
		}

		if(!empty($mpcheckout_account_panel_description[$this->config->get('config_language_id')]['more_details'])) {
			 $this->language->setmpcheckoutlanguage('panel_others', $mpcheckout_account_panel_description[$this->config->get('config_language_id')]['more_details']);
		}

		if(!empty($mpcheckout_shipping_address_description[$this->config->get('config_language_id')]['address_not_required'])) {
			 $this->language->setmpcheckoutlanguage('text_norequire_saddress', $mpcheckout_shipping_address_description[$this->config->get('config_language_id')]['address_not_required']);
		}

		$data['button_checkout'] = $this->language->get('button_checkout');

		$data['text_select'] = $this->language->get('text_select');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['text_norequire_saddress'] = $this->language->get('text_norequire_saddress');

		$data['button_upload'] = $this->language->get('button_upload');


		$data['hold_firstname']	= $this->language->get('hold_firstname');
		$data['hold_lastname']	= $this->language->get('hold_lastname');
		$data['hold_email']	=  $this->language->get('hold_email');
		$data['hold_telephone'] = $this->language->get('hold_telephone');
		$data['hold_fax'] = $this->language->get('hold_fax');
		$data['hold_company'] = $this->language->get('hold_company');
		$data['hold_address_1'] = $this->language->get('hold_address_1');
		$data['hold_address_2'] = $this->language->get('hold_address_2');
		$data['hold_city'] = $this->language->get('hold_city');
		$data['hold_postcode'] = $this->language->get('hold_postcode');
		$data['hold_password'] = $this->language->get('hold_password');
		$data['hold_confirm_password'] = $this->language->get('hold_confirm_password');


		$data['panel_signup_details'] = $this->language->get('panel_signup_details');
		$data['panel_guest_details'] = $this->language->get('panel_guest_details');
		$data['panel_password'] = $this->language->get('panel_password');
		$data['panel_others'] = $this->language->get('panel_others');

		$data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$data['entry_firstname'] = $this->language->get('entry_firstname');
		$data['entry_lastname'] = $this->language->get('entry_lastname');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_telephone'] = $this->language->get('entry_telephone');
		$data['entry_fax'] = $this->language->get('entry_fax');
		$data['entry_company'] = $this->language->get('entry_company');
		$data['entry_address_1'] = $this->language->get('entry_address_1');
		$data['entry_address_2'] = $this->language->get('entry_address_2');
		$data['entry_city'] = $this->language->get('entry_city');
		$data['entry_postcode'] = $this->language->get('entry_postcode');
		$data['entry_country'] = $this->language->get('entry_country');
		$data['entry_zone'] = $this->language->get('entry_zone');
		$data['entry_password'] = $this->language->get('entry_password');
		$data['entry_confirm_password'] = $this->language->get('entry_confirm_password');

		$data['text_newsletter'] = $this->language->get('text_newsletter');
		$data['text_same_address'] = $this->language->get('text_same_address');

		$mpcheckout_account_panel = $this->config->get('mpcheckout_account_panel');
		if(!empty($mpcheckout_account_panel['fields'])) {
			$data['account_fields'] = (array)$mpcheckout_account_panel['fields'];
		} else{
			$data['account_fields'] = array();
		}

		if (!empty($mpcheckout_account_panel['account_id'])) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($mpcheckout_account_panel['account_id']);

			if ($information_info) {
				$data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $mpcheckout_account_panel['account_id'], Mpcheckout\Manager::mpssl()), $information_info['title'], $information_info['title']);
			} else {
				$data['text_agree'] = '';
			}
		} else {
			$data['text_agree'] = '';
		}

		if (isset($this->request->post['agree'])) {
			$data['agree'] = $this->request->post['agree'];
		} else {
			$data['agree'] = false;
		}

		if (isset($this->session->data['payment_address']['address_id'])) {
			$data['address_id'] = $this->session->data['payment_address']['address_id'];
		} else {
			$data['address_id'] = $this->customer->getAddressId();
		}

		$this->load->model('account/address');

		if($this->customer->getId()) {
			$data['addresses'] = $this->model_account_address->getAddresses();
		} else {
			$data['addresses'] = array();
		}

		if (!empty($this->session->data['payment_address']['country_id'])) {
			$data['country_id'] = $this->session->data['payment_address']['country_id'];
		} else {
			$data['country_id'] = $this->config->get('mpcheckout_country_id');
		}

		if (!empty($this->session->data['payment_address']['zone_id'])) {
			$data['zone_id'] = $this->session->data['payment_address']['zone_id'];
		} else {
			$data['zone_id'] = $this->config->get('mpcheckout_zone_id');
		}

		if (!empty($this->session->data['payment_address']['postcode'])) {
			$data['postcode'] = $this->session->data['payment_address']['postcode'];
		} else {
			$data['postcode'] = $this->config->get('mpcheckout_postcode');
		}

		// Country
		$this->load->model('localisation/country');
		$data['countries'] = $this->model_localisation_country->getCountries();

		// Custom Fields
		$this->load->model('account/custom_field');
		$data['custom_fields'] = $this->model_account_custom_field->getCustomFields();


		// Customer Groups
		$data['customer_groups'] = array();
		if (is_array($this->config->get('config_customer_group_display'))) {
			$this->load->model('account/customer_group');

			$customer_groups = $this->model_account_customer_group->getCustomerGroups();

			foreach ($customer_groups  as $customer_group) {
				if (in_array($customer_group['customer_group_id'], $this->config->get('config_customer_group_display'))) {
					$data['customer_groups'][] = $customer_group;
				}
			}
		}

		$mpcheckout_account_panel = $this->config->get('mpcheckout_account_panel');

		$mpcheckout_customer_group_id = (!empty($mpcheckout_account_panel['customer_group_id']) ? $mpcheckout_account_panel['customer_group_id'] : $this->config->get('config_customer_group_id'));

		$data['newsletter_subscribe_status'] = (!empty($mpcheckout_account_panel['newsletter_subscribe']) ? $mpcheckout_account_panel['newsletter_subscribe'] : '');
		$data['newsletter_subscribe_check'] = (!empty($mpcheckout_account_panel['newsletter_subscribe_check']) ? $mpcheckout_account_panel['newsletter_subscribe_check'] : '');
		$data['delivery_address_check'] = (!empty($mpcheckout_account_panel['delivery_address_check']) ? $mpcheckout_account_panel['delivery_address_check'] : '');

		$data['customer_group_id'] = $mpcheckout_customer_group_id;

		$data['shipping_required'] = $this->cart->hasShipping();

		if(!empty($mpcheckout_account_panel['default_account_id'])) {
			$data['default_terms'] = true;
		} else{
			$data['default_terms'] = false;
		}

		$mpcheckout_account_button = $this->config->get('mpcheckout_account_button');
		if(!empty($mpcheckout_account_button['default_account_button'])) {
			$data['default_account_button'] = $mpcheckout_account_button['default_account_button'];
		} else{
			$data['default_account_button'] = 'register';
		}

		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && $this->config->get('mpcheckout_captcha')) {
			$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
		} else {
			$data['captcha'] = '';
		}
	}
}