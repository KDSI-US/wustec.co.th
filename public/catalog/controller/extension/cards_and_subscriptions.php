<?php
//==============================================================================
// Braintree Payment Gateway Pro v2022-7-12
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
// 
// All code within this file is copyright Clear Thinking, LLC.
// You may not copy or reuse code within this file without written permission.
//==============================================================================

//namespace Opencart\Catalog\Controller\Extension\Braintree\Extension;
//class CardsAndSubscriptions extends \Opencart\System\Engine\Model {

class ControllerExtensionCardsAndSubscriptions extends Controller {
	
	private $type = 'extension';
	private $name = 'cards_and_subscriptions';
	
	//==============================================================================
	// index()
	//==============================================================================
	public function index() {
		$data['type'] = $this->type;
		$data['name'] = $this->name;

		$settings = $this->getSettings();
		$data['settings'] = $settings;
		$data['language'] = (isset($this->session->data['language'])) ? $this->session->data['language'] : $this->config->get('config_language');
		$data = array_merge($data, $this->load->language('account/address'));
		
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link($settings['extension_route'], '', 'SSL');
			$this->response->redirect($this->url->link('account/login', '', 'SSL'));
		}
		
		// Create countries array
		$data['countries'] = array();
		
		$store_country = (int)$this->config->get('config_country_id');
		$country_query = $this->db->query("(SELECT * FROM " . DB_PREFIX . "country WHERE country_id = " . $store_country . ") UNION (SELECT * FROM " . DB_PREFIX . "country WHERE country_id != " . $store_country . ")");
		
		foreach ($country_query->rows as $country) {
			$data['countries'][$country['iso_code_2']] = $country['name'];
		}
		
		// Get customer info
		$data['customer_name'] = $this->customer->getFirstName() . ' ' . $this->customer->getLastName();
		$address_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE address_id = " . (int)$this->customer->getAddressId());
		$data['address'] = ($address_query->num_rows) ? $address_query->row : array();
		
		$sources = array();
		$subscriptions = array();
		
		// Create client token
		$gateway = $this->loadBraintree($settings);
		$braintree_customer_id = 'customer_' . $this->customer->getId();
		
		try {
			$customer = $gateway->customer()->find($braintree_customer_id);
		} catch (Exception $e) {
			$customer_data = array(
				'id'		=> $braintree_customer_id,
				'firstName'	=> $this->customer->getFirstName(),
				'lastName'	=> $this->customer->getLastName(),
				'email'		=> $this->customer->getEmail(),
				'phone'		=> $this->customer->getTelephone(),
			);
			$customer = $gateway->customer()->create($customer_data);
		}
		
		$data['client_token'] = $gateway->clientToken()->generate(array('customerId' => $braintree_customer_id));
		
		// Create cards array
		$data['cards'] = array();
		
		if (!empty($customer->creditCards)) {
			foreach ($customer->creditCards as $cc) {
				$card = array(
					'default'		=> $cc->isDefault(),
					'id'			=> $cc->token,
					'subscriptions'	=> $cc->subscriptions,
					'text'			=> $cc->cardType . ' ' . $cc->last4 . ' (' . $cc->expirationMonth . '/' . substr($cc->expirationYear, 2, 4) . ')',
					'type'			=> $cc->cardType,
				);
				if ($card['default']) {
					array_unshift($data['cards'], $card);
				} else {
					$data['cards'][] = $card;
				}
			}
		}
		
		// Create subscriptions array
		$data['subscriptions'] = array();
		
		if ($settings['subscriptions'] && $settings['allow_customers_to_cancel']) {
			$plans = array();
			
			try {
				$braintree_plans = $gateway->plan()->all();
				foreach ($braintree_plans as $braintree_plan) {
					$plans[$braintree_plan->id] = $braintree_plan;
				}
			} catch (Exception $e) {
				// do nothing
			}
			
			try {
				$payment_methods = (object)array_merge((array)$customer->creditCards, (array)$customer->paypalAccounts);
				
				foreach ($payment_methods as $pm) {
					foreach ($pm->subscriptions as $subscription) {
						if ($subscription->status != 'Active' && $subscription->status != 'Pending') continue;
						
						if (!empty($plans[$subscription->planId])) {
							$plan_name = $plans[$subscription->planId]->name;
							$plan_currency = $plans[$subscription->planId]->currencyIsoCode;
							$plan_interval = ' / ' . ($plans[$subscription->planId]->billingFrequency == 1 ? 'month' : $plans[$subscription->planId]->billingFrequency . ' months');
						} else {
							$plan_name = $subscription->planId;
							$plan_currency = $this->config->get('config_currency');
							$plan_interval = '';
						}
						
						$data['subscriptions'][] = array(
							'id'	=> $subscription->id,
							'card'	=> (!empty($pm->email)) ? 'PayPal' : $pm->cardType . ' ' . $pm->last4 . ' (' . $pm->expirationMonth . '/' . substr($pm->expirationYear, 2, 4) . ')',
							'last'	=> ($subscription->status == 'Active' && !empty($subscription->billingPeriodStartDate)) ? date($data['date_format_short'], $subscription->billingPeriodStartDate->format('U')) : '---',
							'next'	=> date($data['date_format_short'], $subscription->nextBillingDate->format('U')),
							'plan'	=> $plan_name . ' (' . $this->currency->format($subscription->price, $plan_currency) . $plan_interval . ')',
						);
					}
				}
			} catch (Exception $e) {
				// do nothing
			}
		}
		
		// Breadcrumbs
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text'		=> $data['text_home'],
			'href'		=> $this->url->link('common/home'),
		);
		$data['breadcrumbs'][] = array(
			'text'		=> $data['text_account'],
			'href'		=> $this->url->link('account/account', '', 'SSL'),
		);
		$data['breadcrumbs'][] = array(
			'text'		=> $settings['cards_page_heading_' . $data['language']],
			'href'		=> $this->url->link($settings['extension_route'], '', 'SSL'),
		);
		
		// Render
		$this->document->setTitle($settings['cards_page_heading_' . $data['language']]);
		$data['heading_title'] = $settings['cards_page_heading_' . $data['language']];
		$data['back'] = $this->url->link('account/account', '', 'SSL');
		
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		
		$theme = (version_compare(VERSION, '2.2', '<')) ? $this->config->get('config_template') : str_replace('theme_', '', $this->config->get('config_theme'));
		$template = (file_exists(DIR_TEMPLATE . $theme . '/template/' . $this->type . '/' . $this->name . '.twig')) ? $theme : 'default';
		
		if (version_compare(VERSION, '4.0', '<')) {
			$template_file = DIR_TEMPLATE . $template . '/template/extension/' . $this->type . '/' . $this->name . '.twig';
		} elseif (defined('DIR_EXTENSION')) {
			$template_file = DIR_EXTENSION . 'braintree/catalog/view/template/' . $this->type . '/' . $this->name . '.twig';
		}
		
		if (is_file($template_file)) {
			extract($data);
			
			ob_start();
			if (version_compare(VERSION, '4.0', '<')) {
				require(class_exists('VQMod') ? \VQMod::modCheck(modification($template_file)) : modification($template_file));
			} else {
				require(class_exists('VQMod') ? \VQMod::modCheck($template_file) : $template_file);
			}
			$output = ob_get_clean();
			
			if (version_compare(VERSION, '4.0', '>=')) {
				$output = str_replace($settings['extension_route'] . '/', $settings['extension_route'] . '|', $output);
			}
			
			echo $output;
		} else {
			echo 'Error loading template file';
		}
	}
	
	//==============================================================================
	// Private functions
	//==============================================================================
	private function getSettings() {
		//$code = (version_compare(VERSION, '3.0', '<') ? '' : $this->type . '_') . $this->name;
		$code = (version_compare(VERSION, '3.0', '<') ? '' : 'payment_') . 'braintree';
		
		$settings = array();
		$settings_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `code` = '" . $this->db->escape($code) . "' ORDER BY `key` ASC");
		
		foreach ($settings_query->rows as $setting) {
			$value = $setting['value'];
			if ($setting['serialized']) {
				$value = (version_compare(VERSION, '2.1', '<')) ? unserialize($setting['value']) : json_decode($setting['value'], true);
			}
			$split_key = preg_split('/_(\d+)_?/', str_replace($code . '_', '', $setting['key']), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
			
				if (count($split_key) == 1)	$settings[$split_key[0]] = $value;
			elseif (count($split_key) == 2)	$settings[$split_key[0]][$split_key[1]] = $value;
			elseif (count($split_key) == 3)	$settings[$split_key[0]][$split_key[1]][$split_key[2]] = $value;
			elseif (count($split_key) == 4)	$settings[$split_key[0]][$split_key[1]][$split_key[2]][$split_key[3]] = $value;
			else 							$settings[$split_key[0]][$split_key[1]][$split_key[2]][$split_key[3]][$split_key[4]] = $value;
		}
		
		if (version_compare(VERSION, '4.0', '<')) {
			$settings['extension_route'] = $this->type . '/' . $this->name;
		} else {
			$settings['extension_route'] = 'extension/braintree/' . $this->type . '/' . $this->name;
		}
		
		return $settings;
	}
	
	private function loadBraintree($settings) {
		if (version_compare(VERSION, '4.0', '<')) {
			require_once(DIR_SYSTEM . 'library/braintree/lib/Braintree.php');
		} elseif (defined('DIR_EXTENSION')) {
			require_once(DIR_EXTENSION . 'braintree/system/library/braintree/lib/Braintree.php');
		}
		
		$gateway = new \Braintree\Gateway(array(
			'environment'	=> $settings['server_mode'],
			'merchantId'	=> $settings[$settings['server_mode'] . '_merchant_id'],
			'publicKey'		=> $settings[$settings['server_mode'] . '_public_key'],
			'privateKey'	=> $settings[$settings['server_mode'] . '_private_key'],
		));
		
		return $gateway;
	}
	
	//==============================================================================
	// modifyCard() functions
	//==============================================================================
	public function modifyCard() {
		$settings = $this->getSettings();
		$gateway = $this->loadBraintree($settings);
		
		try {
			if ($this->request->get['request'] == 'make_default') {
				
				$result = $gateway->customer()->update('customer_' . $this->customer->getId(), array(
					'creditCard' => array(
						'options' => array(
							'makeDefault'			=> true,
							'updateExistingToken'	=> $this->request->get['id'],
							'verifyCard'			=> false,
						)
					)
				));
				
			} elseif ($this->request->get['request'] == 'delete_card') {
				
				$result = $gateway->paymentMethod()->find($this->request->get['id']);
				if ($result->billingAddress) {
					$gateway->address()->delete($result->billingAddress->customerId, $result->billingAddress->id);
				}
				$result = $gateway->paymentMethod()->delete($this->request->get['id']);
				
			} elseif ($this->request->get['request'] == 'add_card') {
				
				$billing_address = array(
					'firstName'			=> html_entity_decode($this->customer->getFirstName(), ENT_QUOTES, 'UTF-8'),
					'lastName'			=> html_entity_decode($this->customer->getLastName(), ENT_QUOTES, 'UTF-8'),
					'streetAddress' 	=> html_entity_decode($this->request->get['address1'], ENT_QUOTES, 'UTF-8'),
					'extendedAddress'	=> html_entity_decode($this->request->get['address2'], ENT_QUOTES, 'UTF-8'),
					'locality'			=> html_entity_decode($this->request->get['city'], ENT_QUOTES, 'UTF-8'),
					'region'			=> html_entity_decode($this->request->get['zone'], ENT_QUOTES, 'UTF-8'),
					'postalCode'		=> html_entity_decode($this->request->get['postcode'], ENT_QUOTES, 'UTF-8'),
					'countryCodeAlpha2'	=> html_entity_decode($this->request->get['country'], ENT_QUOTES, 'UTF-8')
				);
				
				$result = $gateway->customer()->update('customer_' . $this->customer->getId(), array(
					'creditCard' => array(
						'paymentMethodNonce'	=> $this->request->get['id'],
						'billingAddress'		=> array_merge($billing_address, array(
							'options'	=> array('updateExisting' => true)
						)),
						'options' => array(
							'makeDefault'	=> true,
							'verifyCard'	=> true,
						)
					)
				));
				
			} elseif ($this->request->get['request'] == 'edit_subscription') {
				
				$ids = explode('|', $this->request->get['id']);
				$result = $gateway->subscription()->update($ids[0], array(
					'paymentMethodToken'	=> $ids[1],
				));
				
			} elseif ($this->request->get['request'] == 'cancel_subscription') {
				
				$result = $gateway->subscription()->cancel($this->request->get['id']);
				
			}
		} catch (Exception $e) {
			$error = $e->getMessage();
		}
		
		if (empty($error) && empty($result->success)) {
			$error = $result->message;
		}
		
		if (!empty($error)) {
			$error = str_replace('payment_method_nonce does not contain a valid payment instrument type.', '', $error);
			$error = str_replace('Credit card must include number, payment_method_nonce, or venmo_sdk_payment_method_code.', '', $error);
			$error = str_replace('Addresses must have at least one field filled in.', 'Postcode must be filled in.', $error);
			$error = str_replace("\n\n", "\n", $error);
			$this->log->write('BRAINTREE CARD/SUBSCRIPTION PAGE ERROR: ' . $error);
			echo $error;
		}
	}
	
	//==============================================================================
	// getZones() functions
	//==============================================================================
	public function getZones() {
		$output = '';
		$this->load->model('localisation/zone');
		
		$country_id = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE iso_code_2 = '" . $this->db->escape($this->request->get['country']) . "'")->row['country_id'];
		$zones = $this->model_localisation_zone->getZonesByCountryId($country_id);
		
		foreach ($zones as $zone) {
			$output .= '<option value="' . $zone['name'] . '">' . $zone['name'] . '</option>';
		}
		
		echo $output;
	}
}
?>