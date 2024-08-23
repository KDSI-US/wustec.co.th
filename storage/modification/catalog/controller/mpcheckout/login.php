<?php
/* This file is under Git Control by KDSI. */
class ControllerMpCheckoutLogin extends Controller {
	public function __construct($registry) {
		parent::__construct($registry);
		require_once(DIR_SYSTEM .'library/mpcheckout/manager.php');
	}
	
	public function index() {
		$data = array();

		$this->content($data);
		
		return $this->load->view('mpcheckout/login', $data);
	}
	public function ajax() {
		$data = array();
		
		$this->content($data);
		
		$this->response->setOutput($this->load->view('mpcheckout/login', $data));
	}

	protected function content(&$data) {
		$this->load->language('mpcheckout/checkout');		

		$mpcheckout_login_panel_description = $this->config->get('mpcheckout_login_panel_description');
		if(!empty($mpcheckout_login_panel_description[$this->config->get('config_language_id')]['title'])) {
			 $this->language->setmpcheckoutlanguage('panel_login', $mpcheckout_login_panel_description[$this->config->get('config_language_id')]['title']);
		}

		if(!empty($mpcheckout_login_panel_description[$this->config->get('config_language_id')]['login_button'])) {
			 $this->language->setmpcheckoutlanguage('button_login', $mpcheckout_login_panel_description[$this->config->get('config_language_id')]['login_button']);
		}

		$data['panel_login'] = $this->language->get('panel_login');

		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_password'] = $this->language->get('entry_password');

		$data['hold_email'] = $this->language->get('hold_email');
		$data['hold_password'] = $this->language->get('hold_password');	
		$data['text_forgotten'] = $this->language->get('text_forgotten');	

		$data['button_login'] = $this->language->get('button_login');

		$data['forgotten'] = $this->url->link('account/forgotten', '', true);
	}

	public function save() {
		$this->load->language('mpcheckout/checkout');

		$json = array();

		if ($this->customer->isLogged()) {
			$json['redirect'] = $this->url->link(Mpcheckout\Manager::MPREDIRECT, '', Mpcheckout\Manager::mpssl());
		}

		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link(Mpcheckout\Manager::MPREDIRECT, '', Mpcheckout\Manager::mpssl());
		}

		if (!$json) {
			$this->load->model('account/customer');

			if(VERSION > '2.0.0.0') {
				// Check how many login attempts have been made.
				$login_info = $this->model_account_customer->getLoginAttempts($this->request->post['email']);

				if ($login_info && ($login_info['total'] >= $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
					$json['error']['login'] = $this->language->get('error_attempts');
				}

				// Check how many login attempts have been made.
				$login_info = $this->model_account_customer->getLoginAttempts($this->request->post['email']);

				if ($login_info && ($login_info['total'] >= $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
					$json['error']['warning'] = $this->language->get('error_attempts');
				}
			}

			// Check if customer has been approved.
			$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

			if ($customer_info && !$customer_info['status']) {
				$json['error']['warning'] = $this->language->get('error_approved');
			}

			if (!isset($json['error'])) {
				if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {
					$json['error']['warning'] = $this->language->get('error_login');

					if(VERSION > '2.0.0.0') {
						$this->model_account_customer->addLoginAttempt($this->request->post['email']);
					}
				} else {
					if(VERSION > '2.0.0.0') {
						$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
					}
				}
			}
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
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}