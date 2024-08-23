<?php
/* This file is under Git Control by KDSI. */
class ControllerCheckoutRegister extends Controller {
	public function index() {
		$this->load->language('checkout/checkout');
			
		$modulestatus=$this->config->get('registermanager_status');
		$registerstatus=$this->config->get('registermanager_checkregister');
		
		if(!empty($modulestatus && $registerstatus)) {
			$allalbles = $this->config->get('registermanager_register');
			if(!empty($allalbles['firstnamelabel'][$this->config->get('config_language_id')])) {
				$data['entry_firstname']	= $allalbles['firstnamelabel'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['lastnamelabel'][$this->config->get('config_language_id')])) {
				$data['entry_lastname'] 	= $allalbles['lastnamelabel'][$this->config->get('config_language_id')];
			}		
			if(!empty($allalbles['emaillabel'][$this->config->get('config_language_id')])) {
				$data['entry_email'] 	= $allalbles['emaillabel'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['phonelabel'][$this->config->get('config_language_id')])) {
				$data['entry_telephone']	= $allalbles['phonelabel'][$this->config->get('config_language_id')];
			}			
			if(!empty($allalbles['add1label'][$this->config->get('config_language_id')])) {
				$data['entry_address_1'] = $allalbles['add1label'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['add2label'][$this->config->get('config_language_id')])) {
				$data['entry_address_2'] = $allalbles['add2label'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['citylabel'][$this->config->get('config_language_id')])) {
				$data['entry_city']	= $allalbles['citylabel'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['postcodelabel'][$this->config->get('config_language_id')])) {
				$data['entry_postcode']		= $allalbles['postcodelabel'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['countrylabel'][$this->config->get('config_language_id')])) {
				$data['entry_country']		= $allalbles['countrylabel'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['zonelabel'][$this->config->get('config_language_id')])) {
				$data['entry_zone']		    = $allalbles['zonelabel'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['pwdlabel'][$this->config->get('config_language_id')])) {
				$data['entry_password']		= $allalbles['pwdlabel'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['cpwdlabel'][$this->config->get('config_language_id')])) {
				$data['entry_confirm']		= $allalbles['cpwdlabel'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['subscribe'][$this->config->get('config_language_id')])) {
				$data['entry_newsletter']	= $allalbles['subscribe'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['accounttitle'][$this->config->get('config_language_id')])) {
				$data['heading_title']	    = $allalbles['accounttitle'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['personaltitle'][$this->config->get('config_language_id')])) {
				$data['text_your_details']	= $allalbles['personaltitle'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['addtitle'][$this->config->get('config_language_id')])) {
				$data['text_your_address']	= $allalbles['addtitle'][$this->config->get('config_language_id')];
			}	
			if(!empty($allalbles['alreadytitle'][$this->config->get('config_language_id')])) {
				$account_already = $allalbles['alreadytitle'][$this->config->get('config_language_id')];
				$data['text_account_already'] = html_entity_decode(sprintf($account_already , $this->url->link('account/login', '', true)));
			}
			if(!empty($allalbles['passtitle'][$this->config->get('config_language_id')])) {
				$data['text_your_password']	= $allalbles['passtitle'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['newsletter'][$this->config->get('config_language_id')])) {
				$data['text_newsletter']	= $allalbles['newsletter'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['privacylabel'][$this->config->get('config_language_id')])) {
				$data['text_agree']			= $allalbles['privacylabel'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['submitbutton'][$this->config->get('config_language_id')])) {
				$data['button_continue']	= $allalbles['submitbutton'][$this->config->get('config_language_id')];
			}
            if(!empty($allalbles['captcha'][$this->config->get('config_language_id')])) {
				$data['captchalable']	= $allalbles['captcha'][$this->config->get('config_language_id')];
			}

			$data['firstnamerequiredstatus']  = $this->config->get('registermanager_fnamerequired');
			$data['lastnamerequiredstatus']   = $this->config->get('registermanager_lastnamerequired');
			$data['emailrequiredstatus'] 	  = $this->config->get('registermanager_emailrequired');
			$data['phonerequiredstatus'] 	  = $this->config->get('registermanager_phonerequired');
			$data['add1requiredstatus'] 	  = $this->config->get('registermanager_add1required');
			$data['add2requiredstatus'] 	  = $this->config->get('registermanager_add2required');
			$data['cityrequiredstatus'] 	  = $this->config->get('registermanager_cityrequired');
			$data['postcoderequiredstatus']   = $this->config->get('registermanager_postcodrequired');
			$data['countryrequiredstatus']    = $this->config->get('registermanager_countryrequired');
			$data['zonerequiredstatus']       = $this->config->get('registermanager_zonerequired');
			$data['pwdrequiredstatus']        = $this->config->get('registermanager_pwdrequired');
			$data['cpwdrequiredstatus']       = $this->config->get('registermanager_cpwdrequired');
			$data['subscriberequiredstatus']  = $this->config->get('registermanager_subscriberequired');
			$data['privacyrequiredstatus']    = $this->config->get('registermanager_privacyrequired');
            $data['showcaptcha']			  = $this->config->get('registermanager_captchastatus');
			$data['firstnamestatus'] 		  = $this->config->get('registermanager_fnamestatus');
			$data['lastnamestatus'] 		  = $this->config->get('registermanager_lastnamestatus');
			$data['emailstatus'] 			  = $this->config->get('registermanager_emailstatus');
			$data['phonestatus'] 			  = $this->config->get('registermanager_phonestatus');
			$data['add1status'] 			  = $this->config->get('registermanager_add1status');
			$data['add2status'] 			  = $this->config->get('registermanager_add2status');
			$data['citystatus'] 			  = $this->config->get('registermanager_citystatus');
			$data['postcodestatus'] 		  = $this->config->get('registermanager_postcodstatus');
			$data['countrystatus'] 	    	  = $this->config->get('registermanager_countrystatus');
			$data['zonestatus'] 	    	  = $this->config->get('registermanager_zonestatus');
			$data['pwdstatus'] 	        	  = $this->config->get('registermanager_pwdstatus');
			$data['cpwdstatus'] 	    	  = $this->config->get('registermanager_cpwdstatus');
			$data['subscribestatus'] 		  = $this->config->get('registermanager_subscribestatus');
			$data['privacystatus'] 	    	  = $this->config->get('registermanager_privacystatus');
			$data['privacyautochk'] 		  = $this->config->get('registermanager_privacyautochk');

			$data['newsletter'] 	= '';

			$data['firstnamesort_order']	= $this->config->get('registermanager_fnamesortorder');
			$data['lastnamesort_order'] 	= $this->config->get('registermanager_lastnamesortorder');
			$data['emailsort_order'] 		= $this->config->get('registermanager_emailsortorder');
			$data['phonesort_order'] 		= $this->config->get('registermanager_phonesortorder');
			$data['add1sort_order'] 		= $this->config->get('registermanager_add1sortorder');
			$data['add2sort_order'] 		= $this->config->get('registermanager_add2sortorder');
			$data['citysort_order'] 		= $this->config->get('registermanager_citysortorder');
			$data['postcodesort_order'] 	= $this->config->get('registermanager_postcodsortorder');
			$data['countrysort_order']  	= $this->config->get('registermanager_countrysortorder');
			$data['zonesort_order']     	= $this->config->get('registermanager_zonesortorder');
			$data['pwdsort_order']      	= $this->config->get('registermanager_pwdsortorder');
			$data['cpwdsort_order']     	= $this->config->get('registermanager_cpwdsortorder');
		}
			
		
		$data['entry_newsletter'] = sprintf($this->language->get('entry_newsletter'), $this->config->get('config_name'));

        if ($this->config->get('module_emailtemplate_newsletter_status') && $this->config->get('module_emailtemplate_newsletter_preference')) {
			// Create new language to prevent overwriting
			$language_code = !empty($this->session->data['language']) ? $this->session->data['language'] : $this->config->get('config_language');

			$oLanguage = new Language($language_code);
			$oLanguage->load('extension/module/emailtemplate_newsletter');

   			$data['module_emailtemplate_newsletter_status'] = $this->config->get('module_emailtemplate_newsletter_status');
			$data['module_emailtemplate_newsletter_notification'] = $this->config->get('module_emailtemplate_newsletter_notification');
			$data['module_emailtemplate_newsletter_showcase'] = $this->config->get('module_emailtemplate_newsletter_showcase');

			$data['text_preference'] = $oLanguage->get('text_preference');
			$data['entry_preference_essential'] = $oLanguage->get('entry_preference_essential');
			$data['entry_preference_notification'] = $oLanguage->get('entry_preference_notification');
			$data['entry_preference_newsletter'] = $oLanguage->get('entry_preference_newsletter');
			$data['entry_preference_showcase'] = $oLanguage->get('entry_preference_showcase');

			$data['preference_notification'] = (isset($this->request->post['preference_notification'])) ? $this->request->post['preference_notification'] : 1;
			$data['preference_showcase'] = (isset($this->request->post['preference_showcase'])) ? $this->request->post['preference_showcase'] : 1;
		}
        

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

		$data['customer_group_id'] = $this->config->get('config_customer_group_id');

		if (isset($this->session->data['shipping_address']['postcode'])) {
			$data['postcode'] = $this->session->data['shipping_address']['postcode'];
		} else {
			$data['postcode'] = '';
		}

		if (isset($this->session->data['shipping_address']['country_id'])) {
			$data['country_id'] = $this->session->data['shipping_address']['country_id'];
		} else {
			$data['country_id'] = $this->config->get('config_country_id');
		}

		if (isset($this->session->data['shipping_address']['zone_id'])) {
			$data['zone_id'] = $this->session->data['shipping_address']['zone_id'];
		} else {
			$data['zone_id'] = '';
		}

		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		// Custom Fields
		$this->load->model('account/custom_field');

		$data['custom_fields'] = $this->model_account_custom_field->getCustomFields();

		// Captcha
		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
			$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
		} else {
			$data['captcha'] = '';
		}

		if ($this->config->get('config_account_id')) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

			if ($information_info) {
				$data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_account_id'), true), $information_info['title']);
			} else {
				$data['text_agree'] = '';
			}
		} else {
			$data['text_agree'] = '';
		}

			
		if(!empty($modulestatus && $registerstatus)) {
			if($data['privacyautochk']) {
				$data['agree']=true;
			}
			if ($this->config->get('config_account_id')) {
				$this->load->model('catalog/information');
				$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
				if ($information_info) {
					if(!empty($allalbles['privacylabel'][$this->config->get('config_language_id')])) {
						$text_agree = $allalbles['privacylabel'][$this->config->get('config_language_id')];
						$data['text_agree'] = html_entity_decode(sprintf($text_agree, $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_account_id'), true), $information_info['title'], $information_info['title']));
					} else {
						$data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_account_id'), true), $information_info['title'], $information_info['title']);
					}
				} else {
					$data['text_agree'] = '';
				}
			} else {
				$data['text_agree'] = '';
			}
		}
			
		$data['shipping_required'] = $this->cart->hasShipping();
		
		
		if(!empty($modulestatus && $registerstatus)) {
			$this->response->setOutput($this->load->view('checkout/tmdregister', $data));
		} else {
			$this->response->setOutput($this->load->view('checkout/register', $data));
		}
			
	}


	public function tmdsave() {
	
		$modulestatus=$this->config->get('registermanager_status');
		$registerstatus=$this->config->get('registermanager_checkregister');
		
		$fnamestatus 	 = $this->config->get('registermanager_fnamestatus');
		$lnamestatus 	 = $this->config->get('registermanager_lastnamestatus');
		$emailstatus 	 = $this->config->get('registermanager_emailstatus');
		$phonestatus 	 = $this->config->get('registermanager_phonestatus');
		$add1status    	 = $this->config->get('registermanager_add1status');
		$add2status    	 = $this->config->get('registermanager_add2status');
		$citystatus    	 = $this->config->get('registermanager_citystatus');
		$postcodstatus   = $this->config->get('registermanager_postcodstatus');
		$countrystatus   = $this->config->get('registermanager_countrystatus');
		$zonestatus      = $this->config->get('registermanager_zonestatus');
		$pwdstatus    	 = $this->config->get('registermanager_pwdstatus');
		$cpwdstatus      = $this->config->get('registermanager_cpwdstatus');
		$subscribestatus = $this->config->get('registermanager_subscribestatus');
		$privacystatus   = $this->config->get('registermanager_privacystatus');
		$privacyautochk  = $this->config->get('registermanager_privacyautochk');
		
		$fnamerequired		= $this->config->get('registermanager_fnamerequired');
		$lnamerequired		= $this->config->get('registermanager_lastnamerequired');
		$emailrequired		= $this->config->get('registermanager_emailrequired');
		$phonerequired		= $this->config->get('registermanager_phonerequired');
		$add1required		= $this->config->get('registermanager_add1required');
		$add2required		= $this->config->get('registermanager_add2required');
		$cityrequired		= $this->config->get('registermanager_cityrequired');
		$postcoderequired	= $this->config->get('registermanager_postcodrequired');
		$countryrequired	= $this->config->get('registermanager_countryrequired');
		$zonerequired		= $this->config->get('registermanager_zonerequired');
		$pwdrequired		= $this->config->get('registermanager_pwdrequired');
		$cpwdrequired		= $this->config->get('registermanager_cpwdrequired');
			
		$allalbles			= $this->config->get('registermanager_register');
	
		$this->load->language('checkout/checkout');

		$json = array();

		/* Validate if customer is already logged out. */
		if ($this->customer->isLogged()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', true);
		}

		/* Validate cart has products and has stock.  */
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');
		}

		/* Validate minimum quantity requirements. */
		$products = $this->cart->getProducts();
		foreach ($products as $product) {
			$product_total = 0;
			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}
			if ($product['minimum'] > $product_total) {
				$json['redirect'] = $this->url->link('checkout/cart');
				break;
			}
		}
		
		if (!$json) {
			$this->load->model('account/customer');
			if($fnamestatus == 1) {
				if(!empty($fnamerequired)) {
					if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
						$json['error']['firstname'] = $allalbles['firstnamerror'][$this->config->get('config_language_id')];
					}
				}
			}
			if($lnamestatus == 1) {
				if(!empty($lnamerequired)) {
					if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
						$json['error']['lastname'] = $allalbles['lastnamerror'][$this->config->get('config_language_id')];
					}
				}
			}
			if($emailstatus == 1) {
				if(!empty($emailrequired)) {
					if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
						$json['error']['email'] = $allalbles['emailerror'][$this->config->get('config_language_id')];
					}
				}
			}
			if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
				$json['error']['warning'] = $this->language->get('error_exists');
			}
			if($phonestatus == 1) { 
				if(!empty($phonerequired)) {
					if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
						$json['error']['telephone'] = $allalbles['phonerror'][$this->config->get('config_language_id')];
					}
				}
			}
			if($add2status == 1) {
				if(!empty($add2required)) {
					if ((utf8_strlen(trim($this->request->post['address_2'])) < 3) || (utf8_strlen(trim($this->request->post['address_2'])) > 255)) {
						$json['error']['address_2'] = $allalbles['add2error'][$this->config->get('config_language_id')];
					}
				}
			}
			if($add1status == 1) {
				if(!empty($add1required)) {
					if ((utf8_strlen(trim($this->request->post['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['address_1'])) > 128)) {
						$json['error']['address_1'] = $allalbles['add1error'][$this->config->get('config_language_id')];
					}
				}
			}
			if($citystatus == 1) {
				if(!empty($cityrequired)) {
					if ((utf8_strlen(trim($this->request->post['city'])) < 2) || (utf8_strlen(trim($this->request->post['city'])) > 128)) {
						$json['error']['city'] = $allalbles['cityerror'][$this->config->get('config_language_id')];
					}
				}
			}
			$this->load->model('localisation/country');
			$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);
			if($postcodstatus == 1) {
				if(!empty($postcoderequired)) {
					if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
						$json['error']['postcode'] = $allalbles['postcoderror'][$this->config->get('config_language_id')];
					}
				}
			}
			if($countrystatus == 1) {
				if(!empty($countryrequired)) {
					if ($this->request->post['country_id'] == '') {
						$json['error']['country'] = $allalbles['countryerror'][$this->config->get('config_language_id')];
					}
				}
			}
			if($zonestatus == 1) {
				if(!empty($zonerequired)) {
					if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '' || !is_numeric($this->request->post['zone_id'])) {
						$json['error']['zone'] = $allalbles['zonerror'][$this->config->get('config_language_id')];
					}
				}
			}
			if($pwdstatus == 1) {
				if(!empty($pwdrequired)) {
					if ((utf8_strlen($this->request->post['password']) < 4)) {
						$json['error']['password'] = $allalbles['pwderror'][$this->config->get('config_language_id')];
					}
				}
			}

			if($cpwdstatus == 1) {
				if(!empty($cpwdrequired)) {
					if ($this->request->post['confirm'] != $this->request->post['password']) {
						$json['error']['confirm'] = $allalbles['cpwderror'][$this->config->get('config_language_id')];
					}
				}
				if ($this->config->get('config_account_id')) {
					$this->load->model('catalog/information');
					$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
					if(!empty($allalbles['privacyerror'][$this->config->get('config_language_id')])) {
						$error_agree	= $allalbles['privacyerror'][$this->config->get('config_language_id')];
					} else {
						$error_agree =	sprintf($this->language->get('error_agree'), $information_info['title']);
					}
					if ($information_info && !isset($this->request->post['agree'])) {
						$json['error']['warning'] = sprintf($error_agree, $information_info['title']);
					}
				}

				/* Customer Group  */
				if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
					$customer_group_id = $this->request->post['customer_group_id'];
				} else {
					$customer_group_id = $this->config->get('config_customer_group_id');
				}

				/* Custom field validation */
				$this->load->model('account/custom_field');
				$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);
				foreach ($custom_fields as $custom_field) {
					if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
						$json['error']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
					} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
						$json['error']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
					}
				}

				/* Captcha */
				if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
					$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');
					if ($captcha) {
						if(!empty($allalbles['captchaerror'][$this->config->get('config_language_id')])) {
							$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');
							$captcha = $allalbles['captchaerror'][$this->config->get('config_language_id')];
						} else {
							$captcha =$captcha;
						}
						$json['error']['captcha'] = $captcha;
					}
				}
			}

			if (!$json) {
				$customer_id = $this->model_account_customer->addCustomer($this->request->post);

				/* Default Payment Address */
				$this->load->model('account/address');
				$address_id = $this->model_account_address->addAddress($customer_id, $this->request->post);
				
				/* Set the address as default */
				$this->model_account_customer->editAddressId($customer_id, $address_id);
				
				/* Clear any previous login attempts for unregistered accounts. */
				$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
				$this->session->data['account'] = 'register';
				$this->load->model('account/customer_group');
				$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);

				if ($customer_group_info && !$customer_group_info['approval']) {
					$this->customer->login($this->request->post['email'], $this->request->post['password']);
					
					/* Default Payment Address */
					$this->load->model('account/address');
					$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
					if (!empty($this->request->post['shipping_address'])) {
						$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
					}
				} else {
					$json['redirect'] = $this->url->link('account/success');
				}

				unset($this->session->data['guest']);
				unset($this->session->data['shipping_method']);
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['payment_method']);
				unset($this->session->data['payment_methods']);

				/* Add to activity log */
				if ($this->config->get('config_customer_activity')) {
					$this->load->model('account/activity');
					$activity_data = array(
						'customer_id' => $customer_id,
						'name'        => $this->request->post['firstname'] . ' ' . $this->request->post['lastname']
					);
					$this->model_account_activity->addActivity('register', $activity_data);
				}
			}

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}
			
	public function save() {
		$this->load->language('checkout/checkout');
			
		$modulestatus=$this->config->get('registermanager_status');
		$registerstatus=$this->config->get('registermanager_checkregister');
		
		if(!empty($modulestatus && $registerstatus)) {
			$allalbles = $this->config->get('registermanager_register');
			if(!empty($allalbles['firstnamelabel'][$this->config->get('config_language_id')])) {
				$data['entry_firstname']	= $allalbles['firstnamelabel'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['lastnamelabel'][$this->config->get('config_language_id')])) {
				$data['entry_lastname'] 	= $allalbles['lastnamelabel'][$this->config->get('config_language_id')];
			}		
			if(!empty($allalbles['emaillabel'][$this->config->get('config_language_id')])) {
				$data['entry_email'] 	= $allalbles['emaillabel'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['phonelabel'][$this->config->get('config_language_id')])) {
				$data['entry_telephone']	= $allalbles['phonelabel'][$this->config->get('config_language_id')];
			}			
			if(!empty($allalbles['add1label'][$this->config->get('config_language_id')])) {
				$data['entry_address_1'] = $allalbles['add1label'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['add2label'][$this->config->get('config_language_id')])) {
				$data['entry_address_2'] = $allalbles['add2label'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['citylabel'][$this->config->get('config_language_id')])) {
				$data['entry_city']	= $allalbles['citylabel'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['postcodelabel'][$this->config->get('config_language_id')])) {
				$data['entry_postcode']		= $allalbles['postcodelabel'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['countrylabel'][$this->config->get('config_language_id')])) {
				$data['entry_country']		= $allalbles['countrylabel'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['zonelabel'][$this->config->get('config_language_id')])) {
				$data['entry_zone']		    = $allalbles['zonelabel'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['pwdlabel'][$this->config->get('config_language_id')])) {
				$data['entry_password']		= $allalbles['pwdlabel'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['cpwdlabel'][$this->config->get('config_language_id')])) {
				$data['entry_confirm']		= $allalbles['cpwdlabel'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['subscribe'][$this->config->get('config_language_id')])) {
				$data['entry_newsletter']	= $allalbles['subscribe'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['accounttitle'][$this->config->get('config_language_id')])) {
				$data['heading_title']	    = $allalbles['accounttitle'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['personaltitle'][$this->config->get('config_language_id')])) {
				$data['text_your_details']	= $allalbles['personaltitle'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['addtitle'][$this->config->get('config_language_id')])) {
				$data['text_your_address']	= $allalbles['addtitle'][$this->config->get('config_language_id')];
			}	
			if(!empty($allalbles['alreadytitle'][$this->config->get('config_language_id')])) {
				$account_already = $allalbles['alreadytitle'][$this->config->get('config_language_id')];
				$data['text_account_already'] = html_entity_decode(sprintf($account_already , $this->url->link('account/login', '', true)));
			}
			if(!empty($allalbles['passtitle'][$this->config->get('config_language_id')])) {
				$data['text_your_password']	= $allalbles['passtitle'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['newsletter'][$this->config->get('config_language_id')])) {
				$data['text_newsletter']	= $allalbles['newsletter'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['privacylabel'][$this->config->get('config_language_id')])) {
				$data['text_agree']			= $allalbles['privacylabel'][$this->config->get('config_language_id')];
			}
			if(!empty($allalbles['submitbutton'][$this->config->get('config_language_id')])) {
				$data['button_continue']	= $allalbles['submitbutton'][$this->config->get('config_language_id')];
			}
            if(!empty($allalbles['captcha'][$this->config->get('config_language_id')])) {
				$data['captchalable']	= $allalbles['captcha'][$this->config->get('config_language_id')];
			}

			$data['firstnamerequiredstatus']  = $this->config->get('registermanager_fnamerequired');
			$data['lastnamerequiredstatus']   = $this->config->get('registermanager_lastnamerequired');
			$data['emailrequiredstatus'] 	  = $this->config->get('registermanager_emailrequired');
			$data['phonerequiredstatus'] 	  = $this->config->get('registermanager_phonerequired');
			$data['add1requiredstatus'] 	  = $this->config->get('registermanager_add1required');
			$data['add2requiredstatus'] 	  = $this->config->get('registermanager_add2required');
			$data['cityrequiredstatus'] 	  = $this->config->get('registermanager_cityrequired');
			$data['postcoderequiredstatus']   = $this->config->get('registermanager_postcodrequired');
			$data['countryrequiredstatus']    = $this->config->get('registermanager_countryrequired');
			$data['zonerequiredstatus']       = $this->config->get('registermanager_zonerequired');
			$data['pwdrequiredstatus']        = $this->config->get('registermanager_pwdrequired');
			$data['cpwdrequiredstatus']       = $this->config->get('registermanager_cpwdrequired');
			$data['subscriberequiredstatus']  = $this->config->get('registermanager_subscriberequired');
			$data['privacyrequiredstatus']    = $this->config->get('registermanager_privacyrequired');
            $data['showcaptcha']			  = $this->config->get('registermanager_captchastatus');
			$data['firstnamestatus'] 		  = $this->config->get('registermanager_fnamestatus');
			$data['lastnamestatus'] 		  = $this->config->get('registermanager_lastnamestatus');
			$data['emailstatus'] 			  = $this->config->get('registermanager_emailstatus');
			$data['phonestatus'] 			  = $this->config->get('registermanager_phonestatus');
			$data['add1status'] 			  = $this->config->get('registermanager_add1status');
			$data['add2status'] 			  = $this->config->get('registermanager_add2status');
			$data['citystatus'] 			  = $this->config->get('registermanager_citystatus');
			$data['postcodestatus'] 		  = $this->config->get('registermanager_postcodstatus');
			$data['countrystatus'] 	    	  = $this->config->get('registermanager_countrystatus');
			$data['zonestatus'] 	    	  = $this->config->get('registermanager_zonestatus');
			$data['pwdstatus'] 	        	  = $this->config->get('registermanager_pwdstatus');
			$data['cpwdstatus'] 	    	  = $this->config->get('registermanager_cpwdstatus');
			$data['subscribestatus'] 		  = $this->config->get('registermanager_subscribestatus');
			$data['privacystatus'] 	    	  = $this->config->get('registermanager_privacystatus');
			$data['privacyautochk'] 		  = $this->config->get('registermanager_privacyautochk');

			$data['newsletter'] 	= '';

			$data['firstnamesort_order']	= $this->config->get('registermanager_fnamesortorder');
			$data['lastnamesort_order'] 	= $this->config->get('registermanager_lastnamesortorder');
			$data['emailsort_order'] 		= $this->config->get('registermanager_emailsortorder');
			$data['phonesort_order'] 		= $this->config->get('registermanager_phonesortorder');
			$data['add1sort_order'] 		= $this->config->get('registermanager_add1sortorder');
			$data['add2sort_order'] 		= $this->config->get('registermanager_add2sortorder');
			$data['citysort_order'] 		= $this->config->get('registermanager_citysortorder');
			$data['postcodesort_order'] 	= $this->config->get('registermanager_postcodsortorder');
			$data['countrysort_order']  	= $this->config->get('registermanager_countrysortorder');
			$data['zonesort_order']     	= $this->config->get('registermanager_zonesortorder');
			$data['pwdsort_order']      	= $this->config->get('registermanager_pwdsortorder');
			$data['cpwdsort_order']     	= $this->config->get('registermanager_cpwdsortorder');
		}
			

		$json = array();

		// Validate if customer is already logged out.
		if ($this->customer->isLogged()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', true);
		}

		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');
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
				$json['redirect'] = $this->url->link('checkout/cart');

				break;
			}
		}

		if (!$json) {
			$this->load->model('account/customer');

			if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
				$json['error']['firstname'] = $this->language->get('error_firstname');
			}

			if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
				$json['error']['lastname'] = $this->language->get('error_lastname');
			}

			if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
				$json['error']['email'] = $this->language->get('error_email');
			}

			if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
				$json['error']['warning'] = $this->language->get('error_exists');
			}

			if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
				$json['error']['telephone'] = $this->language->get('error_telephone');
			}

			if ((utf8_strlen(trim($this->request->post['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['address_1'])) > 128)) {
				$json['error']['address_1'] = $this->language->get('error_address_1');
			}

			if ((utf8_strlen(trim($this->request->post['city'])) < 2) || (utf8_strlen(trim($this->request->post['city'])) > 128)) {
				$json['error']['city'] = $this->language->get('error_city');
			}

			$this->load->model('localisation/country');

			$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

			if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
				$json['error']['postcode'] = $this->language->get('error_postcode');
			}

			if ($this->request->post['country_id'] == '') {
				$json['error']['country'] = $this->language->get('error_country');
			}

			if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '' || !is_numeric($this->request->post['zone_id'])) {
				$json['error']['zone'] = $this->language->get('error_zone');
			}

			if ((utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
				$json['error']['password'] = $this->language->get('error_password');
			}

			if ($this->request->post['confirm'] != $this->request->post['password']) {
				$json['error']['confirm'] = $this->language->get('error_confirm');
			}

			if ($this->config->get('config_account_id')) {
				$this->load->model('catalog/information');

				$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

				if ($information_info && !isset($this->request->post['agree'])) {
					$json['error']['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
				}
			}

			// Customer Group
			if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
				$customer_group_id = $this->request->post['customer_group_id'];
			} else {
				$customer_group_id = $this->config->get('config_customer_group_id');
			}

			// Custom field validation
			$this->load->model('account/custom_field');

			$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

			foreach ($custom_fields as $custom_field) {
				if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
					$json['error']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
					$json['error']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				}
			}

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

				if ($captcha) {
					$json['error']['captcha'] = $captcha;
				}
			}
		}

		if (!$json) {
			$customer_id = $this->model_account_customer->addCustomer($this->request->post);

			// Default Payment Address
			$this->load->model('account/address');
				
			$address_id = $this->model_account_address->addAddress($customer_id, $this->request->post);
			
			// Set the address as default
			$this->model_account_customer->editAddressId($customer_id, $address_id);
			
			// Clear any previous login attempts for unregistered accounts.
			$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);

			$this->session->data['account'] = 'register';

			$this->load->model('account/customer_group');

			$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);

			if ($customer_group_info && !$customer_group_info['approval']) {
				$this->customer->login($this->request->post['email'], $this->request->post['password']);

				$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());

				if (!empty($this->request->post['shipping_address'])) {
					$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
				}
			} else {
				$json['redirect'] = $this->url->link('account/success');
			}

			unset($this->session->data['guest']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
