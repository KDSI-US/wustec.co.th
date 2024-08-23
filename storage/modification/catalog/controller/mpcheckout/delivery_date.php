<?php
/* This file is under Git Control by KDSI. */
class ControllerMpCheckoutDeliveryDate extends Controller {
	public function __construct($registry) {
		parent::__construct($registry);
		require_once(DIR_SYSTEM .'library/mpcheckout/manager.php');
	}
	
	public function index() {
		$data = array();

		$this->content($data);
		
		return $this->load->view('mpcheckout/delivery_date', $data);		
	}

	public function ajax() {
		$data = array();
		
		$this->content($data);
		
		$this->response->setOutput($this->load->view('mpcheckout/delivery_date', $data));
	}

	protected function content(&$data) {
		$this->load->language('mpcheckout/checkout');

		$mpcheckout_date_description = $this->config->get('mpcheckout_date_description');
		if(!empty($mpcheckout_date_description[$this->config->get('config_language_id')]['title'])) {
			 $this->language->setmpcheckoutlanguage('panel_delivery_date', $mpcheckout_date_description[$this->config->get('config_language_id')]['title']);
		}

		if(!empty($mpcheckout_date_description[$this->config->get('config_language_id')]['field_title'])) {
			 $this->language->setmpcheckoutlanguage('entry_delivery_date', $mpcheckout_date_description[$this->config->get('config_language_id')]['field_title']);
		}

		$data['panel_delivery_date'] = $this->language->get('panel_delivery_date');
		$data['entry_delivery_date'] = $this->language->get('entry_delivery_date');
		
		$mpcheckout_date_panel = $this->config->get('mpcheckout_date_panel');

		if(!empty($mpcheckout_date_panel['required'])) {
			$data['delivery_date_required'] = true;
		} else{
			$data['delivery_date_required'] = false;
		}

		if(!empty($mpcheckout_date_panel['minimum_days'])) {
			$data['minimum_days'] = (int)$mpcheckout_date_panel['minimum_days'];
		} else{
			$data['minimum_days'] = 0;
		}

	    $data['estimate_date'] = date('Y-m-d', strtotime('+'. (int)$data['minimum_days'] .' day', strtotime(date('Y-m-d H:i:s'))));

		if(!empty($mpcheckout_date_panel['maximum_days'])) {
			$data['maximum_days'] = $mpcheckout_date_panel['maximum_days'];
		} else{
			$data['maximum_days'] = 30;
		}

		if(!empty($mpcheckout_date_panel['disables_weeks'])) {
			$data['disables_weeks'] = $mpcheckout_date_panel['disables_weeks'];
		} else{
			$data['disables_weeks'] = array();
		}

		if(!empty($mpcheckout_date_panel['disabled_dates'])) {
			$data['disabled_dates'] = explode(',', $mpcheckout_date_panel['disabled_dates']);
		} else{
			$data['disabled_dates'] = array();
		}

		// Pick next Available date from disabled dates		
		$i = 1;
		$tmdate = $data['estimate_date'];
		while (in_array($data['estimate_date'], $data['disabled_dates'])) {
			$data['estimate_date'] = date('Y-m-d', strtotime($tmdate . ' +' . $i . ' day'));
			$i++;
		}

		// Pick next Available date from disabled week number
		$i = 1;
		$tmdate = $data['estimate_date'];
		while (in_array(date("w", strtotime($data['estimate_date'])), $data['disables_weeks'])) {
			$data['estimate_date'] = date('Y-m-d', strtotime($tmdate . ' +' . $i . ' day'));
		    $i++;
		}

		$mpcheckout_shipping_method_panel = $this->config->get('mpcheckout_shipping_method_panel');

		$data['shipping_required'] = $this->cart->hasShipping();

		if($this->config->get('mpcheckout_template')) {
			$data['mpcheckout_template'] = $this->config->get('mpcheckout_template');
		} else {
			$data['mpcheckout_template'] = 'checkout_1';
		}
	}
}