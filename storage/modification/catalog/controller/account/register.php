<?php
/* This file is under Git Control by KDSI. */
class ControllerAccountRegister extends Controller {
	private $error = array();

	public function index() {
		if ($this->customer->isLogged()) {
			$this->response->redirect($this->url->link('account/account', '', true));
		}

		$this->load->language('account/register');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

		$this->load->model('account/customer');

		
		$modulestatus=$this->config->get('registermanager_status');
		$registerstatus=$this->config->get('registermanager_arstatus');

		if($this->request->server['REQUEST_METHOD'] == 'POST') {
			if(!empty($modulestatus) && !empty($registerstatus)) {
				$checkregstatus = $this->validatetmd();
			} else {
				$checkregstatus = $this->validate();
			}
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $checkregstatus) {
			
			
		$customer_id = $this->model_account_customer->addCustomer($this->request->post);
		if(isset($this->request->post['addressstatus'])) {
			$this->request->post['default']=1;
			$this->load->model('account/address');
			$this->model_account_address->addAddress($customer_id, $this->request->post);
		}
			

			// Clear any previous login attempts for unregistered accounts.
			$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);

			$this->customer->login($this->request->post['email'], $this->request->post['password']);

			unset($this->session->data['guest']);

			$this->response->redirect($this->url->link('account/success'));
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_register'),
			'href' => $this->url->link('account/register', '', true)
		);
		$data['text_account_already'] = sprintf($this->language->get('text_account_already'), $this->url->link('account/login', '', true));

		$modulestatus=$this->config->get('registermanager_status');
		$registerstatus=$this->config->get('registermanager_arstatus');
		if(!empty($modulestatus) && !empty($registerstatus)) {

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
				$data['entry_postcode'] = $allalbles['postcodelabel'][$this->config->get('config_language_id')];
			}
			
			if(!empty($allalbles['countrylabel'][$this->config->get('config_language_id')])) {
				$data['entry_country'] = $allalbles['countrylabel'][$this->config->get('config_language_id')];
			}
			
			if(!empty($allalbles['zonelabel'][$this->config->get('config_language_id')])) {
				$data['entry_zone'] = $allalbles['zonelabel'][$this->config->get('config_language_id')];
			}
			
			if(!empty($allalbles['pwdlabel'][$this->config->get('config_language_id')])) {
				$data['entry_password'] = $allalbles['pwdlabel'][$this->config->get('config_language_id')];
			}
			
			if(!empty($allalbles['cpwdlabel'][$this->config->get('config_language_id')])) {
				$data['entry_confirm'] = $allalbles['cpwdlabel'][$this->config->get('config_language_id')];
			}
			
			if(!empty($allalbles['taxidlabel'][$this->config->get('config_language_id')])) {
				$data['entry_tax_id'] = $allalbles['taxidlabel'][$this->config->get('config_language_id')];
			}
			
			if(!empty($allalbles['sellerpermitlabel'][$this->config->get('config_language_id')])) {
				$data['entry_seller_permit'] = $allalbles['sellerpermitlabel'][$this->config->get('config_language_id')];
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
			
			$data['firstnamerequiredstatus']	= $this->config->get('registermanager_fnamerequired');
			$data['lastnamerequiredstatus']		= $this->config->get('registermanager_lastnamerequired');
			$data['emailrequiredstatus']		= $this->config->get('registermanager_emailrequired');
			$data['phonerequiredstatus']		= $this->config->get('registermanager_phonerequired');
			$data['add1requiredstatus']			= $this->config->get('registermanager_add1required');
			$data['add2requiredstatus']			= $this->config->get('registermanager_add2required');
			$data['cityrequiredstatus']			= $this->config->get('registermanager_cityrequired');
			$data['postcoderequiredstatus']		= $this->config->get('registermanager_postcodrequired');
			$data['countryrequiredstatus']		= $this->config->get('registermanager_countryrequired');
			$data['zonerequiredstatus']			= $this->config->get('registermanager_zonerequired');
			$data['pwdrequiredstatus']			= $this->config->get('registermanager_pwdrequired');
			$data['cpwdrequiredstatus']			= $this->config->get('registermanager_cpwdrequired');
			$data['subscriberequiredstatus']	= $this->config->get('registermanager_subscriberequired');
			$data['taxidrequiredstatus']		= $this->config->get('registermanager_taxid_required');
			$data['sellerpermitrequiredstatus'] = $this->config->get('registermanager_sellerpermit_required');
						
            $data['showcaptcha']			= $this->config->get('registermanager_captchastatus');

			$data['firstnamestatus'] 		= $this->config->get('registermanager_fnamestatus');
			$data['lastnamestatus'] 		= $this->config->get('registermanager_lastnamestatus');
			$data['emailstatus'] 			= $this->config->get('registermanager_emailstatus');
			$data['phonestatus'] 			= $this->config->get('registermanager_phonestatus');
			$data['add1status'] 			= $this->config->get('registermanager_add1status');
			$data['add2status'] 			= $this->config->get('registermanager_add2status');
			$data['citystatus'] 			= $this->config->get('registermanager_citystatus');
			$data['postcodestatus'] 		= $this->config->get('registermanager_postcodstatus');
			$data['countrystatus'] 	    	= $this->config->get('registermanager_countrystatus');
			$data['zonestatus'] 	    	= $this->config->get('registermanager_zonestatus');
			$data['pwdstatus'] 	        	= $this->config->get('registermanager_pwdstatus');
			$data['cpwdstatus'] 	    	= $this->config->get('registermanager_cpwdstatus');
			$data['subscribestatus'] 		= $this->config->get('registermanager_subscribestatus');
			$data['privacystatus'] 	    	= $this->config->get('registermanager_privacystatus');
			$data['privacyautochk']			= $this->config->get('registermanager_privacyautochk');
			$data['taxidstatus']			= $this->config->get('registermanager_taxidstatus');
			$data['sellerpermitstatus']		= $this->config->get('registermanager_sellerpermitstatus');
			
			$data['showcaptcha']			= $this->config->get('registermanager_captchastatus');
			$data['firstnamesort_order']	= $this->config->get('registermanager_fnamesortorder');
			$data['lastnamesort_order']		= $this->config->get('registermanager_lastnamesortorder');
			$data['emailsort_order']		= $this->config->get('registermanager_emailsortorder');
			$data['phonesort_order']		= $this->config->get('registermanager_phonesortorder');
			$data['add1sort_order']			= $this->config->get('registermanager_add1sortorder');
			$data['add2sort_order']			= $this->config->get('registermanager_add2sortorder');
			$data['citysort_order']			= $this->config->get('registermanager_citysortorder');
			$data['postcodesort_order']		= $this->config->get('registermanager_postcodsortorder');
			$data['countrysort_order']		= $this->config->get('registermanager_countrysortorder');
			$data['zonesort_order']			= $this->config->get('registermanager_zonesortorder');
			$data['pwdsort_order']			= $this->config->get('registermanager_pwdsortorder');
			$data['cpwdsort_order']			= $this->config->get('registermanager_cpwdsortorder');
			$data['subscribesort_order']	= $this->config->get('registermanager_subscribesortorder');
			$data['privacysort_order']		= $this->config->get('registermanager_privacysortorder');
			$data['taxidsort_order']		= $this->config->get('registermanager_taxidorder');
			$data['sellerpermitsort_order']	= $this->config->get('registermanager_sellerpermitorder');
		}
		
		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();
			

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['firstname'])) {
			$data['error_firstname'] = $this->error['firstname'];
		} else {
			$data['error_firstname'] = '';
		}

		if (isset($this->error['lastname'])) {
			$data['error_lastname'] = $this->error['lastname'];
		} else {
			$data['error_lastname'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}

		if (isset($this->error['custom_field'])) {
			$data['error_custom_field'] = $this->error['custom_field'];
		} else {
			$data['error_custom_field'] = array();
		}

		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}


			$data['address_1'] = '';
			$data['address_2'] = '';
			$data['city'] = '';
			$data['postcode'] = '';
			$data['country_id'] = $this->config->get('config_country_id');
			$data['zone_id'] = $this->config->get('config_zone_id');
			$data['tax_id'] = "";
			$data['seller_permit'] = "";

			if (isset($this->request->post['address_1']))     $data['address_1'] = $this->request->post['address_1'];
			if (isset($this->request->post['address_2']))     $data['address_2'] = $this->request->post['address_2'];
			if (isset($this->request->post['city']))          $data['city'] = $this->request->post['city'];
			if (isset($this->request->post['postcode']))      $data['postcode'] = $this->request->post['postcode'];
			if (isset($this->request->post['country_id']))    $data['country_id'] = $this->request->post['country_id'];
			if (isset($this->request->post['zone_id']))       $data['zone_id'] = $this->request->post['zone_id'];
			if (isset($this->request->post['tax_id']))        $data['tax_id'] = $this->request->post['tax_id'];
			if (isset($this->request->post['seller_permit'])) $data['seller_permit'] = $this->request->post['seller_permit'];
			
			$data['error_address_1'] = '';
			$data['error_address_2'] = '';
			$data['error_city'] = '';
			$data['error_postcode'] = '';
			$data['error_country'] = '';
			$data['error_zone'] = '';
			$data['error_tax_id'] = '';
			$data['error_seller_permit'] = '';
			
			if (isset($this->error['address_1']))          $data['error_address_1'] = $this->error['address_1'];
			if (isset($this->error['address_2']))          $data['error_address_2'] = $this->error['address_2'];
			if (isset($this->error['city'])) 			   $data['error_city'] = $this->error['city'];
			if (isset($this->error['postcode']))           $data['error_postcode'] = $this->error['postcode'];
			if (isset($this->error['country_id']))         $data['error_country'] = $this->error['country_id'];
			if (isset($this->error['zone_id']))            $data['error_zone'] = $this->error['zone_id'];
			if (isset($this->error['tax_id']))             $data['error_tax_id'] = $this->error['tax_id'];
			if (isset($this->error['seller_permit']))      $data['error_seller_permit'] = $this->error['seller_permit'];
			
		if (isset($this->error['confirm'])) {
			$data['error_confirm'] = $this->error['confirm'];
		} else {
			$data['error_confirm'] = '';
		}


		if (isset($this->error['tax_id'])) {
			$data['error_tax_id'] = $this->error['tax_id'];
		} else {
			$data['error_tax_id'] = '';
		}

		if (isset($this->error['seller_permit'])) {
			$data['error_seller_permit'] = $this->error['seller_permit'];
		} else {
			$data['error_seller_permit'] = '';
		}
		
		$data['action'] = $this->url->link('account/register', '', true);

		$data['customer_groups'] = array();

		if (is_array($this->config->get('config_customer_group_display'))) {
			$this->load->model('account/customer_group');

			$customer_groups = $this->model_account_customer_group->getCustomerGroups();

			foreach ($customer_groups as $customer_group) {
				if (in_array($customer_group['customer_group_id'], $this->config->get('config_customer_group_display'))) {
					$data['customer_groups'][] = $customer_group;
				}
			}
		}

		if (isset($this->request->post['customer_group_id'])) {
			$data['customer_group_id'] = $this->request->post['customer_group_id'];
		} else {
			$data['customer_group_id'] = $this->config->get('config_customer_group_id');
		}

		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} else {
			$data['lastname'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} else {
			$data['telephone'] = '';
		}


		if (isset($this->request->post['tax_id'])) {
			$data['tax_id'] = $this->request->post['tax_id'];
		} else {
			$data['tax_id'] = '';
		}

		if (isset($this->request->post['seller_permit'])) {
			$data['seller_permit'] = $this->request->post['seller_permit'];
		} else {
			$data['seller_permit'] = '';
		}
		
		// Custom Fields
		$data['custom_fields'] = array();
		
		$this->load->model('account/custom_field');
		
		$custom_fields = $this->model_account_custom_field->getCustomFields();
		
		foreach ($custom_fields as $custom_field) {
			if ($custom_field['location'] == 'account') {
				$data['custom_fields'][] = $custom_field;
			}
		}
		
		if (isset($this->request->post['custom_field']['account'])) {
			$data['register_custom_field'] = $this->request->post['custom_field']['account'];
		} else {
			$data['register_custom_field'] = array();
		}


			$data['custom_fields1']=array();
			foreach ($custom_fields as $custom_field) {
				if ($custom_field['location'] == 'address') {
					$data['custom_fields1'][] = $custom_field;
				}
			}
			if (isset($this->request->post['custom_field']['address'])) {
				$data['address_custom_field'] = $this->request->post['custom_field']['address'];
			} else {
				$data['address_custom_field'] = array();
			}
			
		if (isset($this->request->post['password'])) {
			$data['password'] = $this->request->post['password'];
		} else {
			$data['password'] = '';
		}

		if (isset($this->request->post['confirm'])) {
			$data['confirm'] = $this->request->post['confirm'];
		} else {
			$data['confirm'] = '';
		}

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
			$data['entry_preference_showcase'] = $oLanguage->get('entry_preference_showcase');

			$data['preference_notification'] = (isset($this->request->post['preference_notification'])) ? $this->request->post['preference_notification'] : 1;
			$data['preference_showcase'] = (isset($this->request->post['preference_showcase'])) ? $this->request->post['preference_showcase'] : 1;
		}
        
		if (isset($this->request->post['newsletter'])) {
			$data['newsletter'] = $this->request->post['newsletter'];
		} else {
			$data['newsletter'] = '';
		}

		// Captcha
		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
			$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'), $this->error);
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

		if (isset($this->request->post['agree'])) {
			$data['agree'] = $this->request->post['agree'];
		} else {
			$data['agree'] = false;
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

              $data['recaptcha_status'] = $this->config->get('captcha_google_recaptcha_v3_status');
              $data['recaptcha_score'] = $this->config->get('captcha_google_recaptcha_v3_score');
              $data['recaptcha_sitekey'] = $this->config->get('captcha_google_recaptcha_v3_sitekey');
           

		
			if(!empty($modulestatus) && !empty($registerstatus)) {
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
					$this->response->setOutput($this->load->view('extension/tmdregister', $data));
			} else {
					$this->response->setOutput($this->load->view('account/register', $data));
			}
			
	}

	private function validate() {

		/* Tax ID */
		if (utf8_strlen(trim($this->request->post['tax_id'])) > 50) {
			$this->error['tax_id'] = $this->language->get('error_tax_id');
		}

		/* Seller's Permit */
		if ((utf8_strlen(trim($this->request->post['seller_permit'])) < 1) || (utf8_strlen(trim($this->request->post['seller_permit'])) > 50)) {
			$this->error['seller_permit'] = $this->language->get('error_seller_permit');
		}
		
		if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$this->error['warning'] = $this->language->get('error_exists');
		}

		if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
			$this->error['telephone'] = $this->language->get('error_telephone');
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
			if ($custom_field['location'] == 'account') {
				if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
					$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
					$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				}
			}
		}

		if ((utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
			$this->error['password'] = $this->language->get('error_password');
		}

		if ($this->request->post['confirm'] != $this->request->post['password']) {
			$this->error['confirm'] = $this->language->get('error_confirm');
		}

		// Captcha
		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
			$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

			if ($captcha) {
				$this->error['captcha'] = $captcha;
			}
		}

		// Agree to terms
		if ($this->config->get('config_account_id')) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

			if ($information_info && !isset($this->request->post['agree'])) {
				$this->error['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
			}
		}
		

		if($this->config->get('captcha_google_recaptcha_v3_status') == 1) {
			$this->load->language('extension/captcha/google_recaptcha_v3_error');
			if (isset($this->request->post['recaptcha_response'])) {
				$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$this->config->get('captcha_google_recaptcha_v3_secretkey').'&response='.$this->request->post['recaptcha_response']);
				$recaptcha = json_decode($verifyResponse, true);
				if ($recaptcha['success']  && ($recaptcha['score'] >= $this->config->get('captcha_google_recaptcha_v3_score'))) {
					/* Success Google reCAPTCHA */
				} else {
					$this->error['warning'] = $this->language->get('error_verify');
				}
			}
		}
            
		return !$this->error;
	}


	private function validatetmd() {
		$modulestatus=$this->config->get('registermanager_status');
		if(empty($modulestatus)) {
			$this->validatetmd();
		}
		
		$fnamestatus 	 		= $this->config->get('registermanager_fnamestatus');
		$lnamestatus 	 		= $this->config->get('registermanager_lastnamestatus');
		$emailstatus 	 		= $this->config->get('registermanager_emailstatus');
		$phonestatus 	 		= $this->config->get('registermanager_phonestatus');
		$add1status    	 		= $this->config->get('registermanager_add1status');
		$add2status    	 		= $this->config->get('registermanager_add2status');
		$citystatus    	 		= $this->config->get('registermanager_citystatus');
		$postcodstatus   		= $this->config->get('registermanager_postcodstatus');
		$countrystatus   		= $this->config->get('registermanager_countrystatus');
		$zonestatus      		= $this->config->get('registermanager_zonestatus');
		$pwdstatus    	 		= $this->config->get('registermanager_pwdstatus');
		$cpwdstatus      		= $this->config->get('registermanager_cpwdstatus');
		$subscribestatus 		= $this->config->get('registermanager_subscribestatus');
		$privacystatus   		= $this->config->get('registermanager_privacystatus');
		$privacyautochk  		= $this->config->get('registermanager_privacyautochk');
		$taxidstatus   			= $this->config->get('registermanager_taxidstatus');
		$sellerpermitstatus		= $this->config->get('registermanager_sellerpermitstatus');

		$fnamerequired			= $this->config->get('registermanager_fnamerequired');
		$lnamerequired			= $this->config->get('registermanager_lastnamerequired');
		$emailrequired			= $this->config->get('registermanager_emailrequired');
		$phonerequired			= $this->config->get('registermanager_phonerequired');
		$add1required			= $this->config->get('registermanager_add1required');
		$add2required			= $this->config->get('registermanager_add2required');
		$cityrequired			= $this->config->get('registermanager_cityrequired');
		$postcoderequired		= $this->config->get('registermanager_postcodrequired');
		$countryrequired		= $this->config->get('registermanager_countryrequired');
		$zonerequired			= $this->config->get('registermanager_zonerequired');
		$pwdrequired			= $this->config->get('registermanager_pwdrequired');
		$cpwdrequired			= $this->config->get('registermanager_cpwdrequired');
		$taxidrequired			= $this->config->get('registermanager_taxid_required');
		$sellerpermitrequired 	= $this->config->get('registermanager_sellerpermit_required');
		$captchastatus			= $this->config->get('registermanager_captchastatus');
		$allalbles				= $this->config->get('registermanager_register');
		
		if($fnamestatus == 1) {
			if(!empty($fnamerequired)) {
				if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
					$this->error['firstname']=$allalbles['firstnamerror'][$this->config->get('config_language_id')];
				}
			}
		}
		if($lnamestatus == 1) {
			if(!empty($lnamerequired)) {
				if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
					$this->error['lastname'] = $allalbles['lastnamerror'][$this->config->get('config_language_id')];
				}
			}
		}
		if($emailstatus == 1) {
			if(!empty($emailrequired)) {
				if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
					$this->error['email'] = $allalbles['emailerror'][$this->config->get('config_language_id')];
				}
			}
		}
		if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$this->error['warning'] = $this->language->get('error_exists');
		}
		if($phonestatus == 1) { 
			if(!empty($phonerequired)) {
				if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
					$this->error['telephone'] = $allalbles['phonerror'][$this->config->get('config_language_id')];
				}
			}
		}
		if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->post['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		/*  Custom field validation   */
		$this->load->model('account/custom_field');
		$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

		foreach ($custom_fields as $custom_field) {
			if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
				$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
			} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
				$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
			}
		}
		if($pwdstatus == 1) {
			if(!empty($pwdrequired)) {
				if ((utf8_strlen($this->request->post['password']) < 4)) {
					$this->error['password'] = $allalbles['pwderror'][$this->config->get('config_language_id')];
				}
			}
		}
		if($cpwdstatus == 1) {
			if(!empty($cpwdrequired)) {
				if ($this->request->post['confirm'] != $this->request->post['password']) {
					$this->error['confirm'] = $allalbles['cpwderror'][$this->config->get('config_language_id')];
				}
			}
		}
		if($taxidstatus == 1) {
			if(!empty($taxidrequired)) {
				if ((utf8_strlen(trim($this->request->post['tax_id'])) < 1) || (utf8_strlen(trim($this->request->post['tax_id'])) > 32)) {
					$this->error['tax_id']=$allalbles['taxiderror'][$this->config->get('config_language_id')];
				}
			}
		}
		if($sellerpermitstatus == 1) {
			if(!empty($sellerpermitrequired)) {
				if ((utf8_strlen(trim($this->request->post['seller_permit'])) < 1) || (utf8_strlen(trim($this->request->post['seller_permit'])) > 32)) {
					$this->error['seller_permit']=$allalbles['sellerpermiterror'][$this->config->get('config_language_id')];
				}
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
				$this->error['warning'] = sprintf($error_agree, $information_info['title']);
			}
		}
		if($add1status == 1) {
			if(!empty($add1required)) {
				if(!empty($allalbles['add1error'][$this->config->get('config_language_id')])) {
					$add1error = $allalbles['add1error'][$this->config->get('config_language_id')];
				} else {								
					$add1error = $this->language->get('error_address_1');
				}
				if ((utf8_strlen($this->request->post['address_1']) < 3) || (utf8_strlen($this->request->post['address_1']) > 32)) {
					$this->error['address_1'] = $add1error;
				}
			}
		}
		if($add2status == 1) {
			if(!empty($add2required)) {
				if(!empty($allalbles['add2error'][$this->config->get('config_language_id')])) {
					$add2error = $allalbles['add2error'][$this->config->get('config_language_id')];
				} else {								
					$add2error = $this->language->get('error_address_2');
				}
				if ((utf8_strlen($this->request->post['address_2']) < 3) || (utf8_strlen($this->request->post['address_2']) > 32)) {
					$this->error['address_2'] = $add2error;
				}
			}
		}
		if($citystatus == 1) {
			if(!empty($cityrequired)) {
				if(!empty($allalbles['cityerror'][$this->config->get('config_language_id')])) {
					$cityerror = $allalbles['cityerror'][$this->config->get('config_language_id')];
				} else {								
					$cityerror = $this->language->get('error_city');
				}
				if ((utf8_strlen($this->request->post['city']) < 3) || (utf8_strlen($this->request->post['city']) > 32)) {
					$this->error['city'] = $cityerror;
				}
			}
		}
		if($postcodstatus == 1) {
			if(!empty($postcoderequired)) {
				if(!empty($allalbles['postcoderror'][$this->config->get('config_language_id')])) {
					$postcoderror = $allalbles['postcoderror'][$this->config->get('config_language_id')];
				} else {								
					$postcoderror = $this->language->get('error_postcode');
				}
				if ((utf8_strlen($this->request->post['postcode']) < 3) || (utf8_strlen($this->request->post['postcode']) > 32)) {
					$this->error['postcode'] = $postcoderror;
				}
			}
		}
		if($countrystatus == 1) {
			if(!empty($countryrequired)) {
				if(!empty($allalbles['countryerror'][$this->config->get('config_language_id')])) {
					$countryerror = $allalbles['countryerror'][$this->config->get('config_language_id')];
				} else {								
					$countryerror = $this->language->get('error_country');
				}
				if ($this->request->post['country_id'] == '' || !is_numeric($this->request->post['country_id'])) {
					$this->error['country_id'] = $countryerror;
				}
			}
		}
		if($zonestatus == 1) {
			if(!empty($zonerequired)) {
				if(!empty($allalbles['zonerror'][$this->config->get('config_language_id')])) {
					$zonerror = $allalbles['zonerror'][$this->config->get('config_language_id')];
				} else {								
					$zonerror = $this->language->get('error_zone');
				}
				if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '' || !is_numeric($this->request->post['zone_id'])) {
					$this->error['zone_id'] = $zonerror;
				}
			}
		}
		if(!empty($captchastatus)) {
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');
				if ($captcha) {
					if(!empty($allalbles['captchaerror'][$this->config->get('config_language_id')])) {
						$this->error['captcha'] = $allalbles['captchaerror'][$this->config->get('config_language_id')];
					} else {
						$this->error['captcha'] = $captcha;
					}
				}
			}
		}
		if (defined('JOURNAL3_ACTIVE')) {
			if (\Journal3\Utils\Request::isAjax() && $this->error) {
				echo json_encode(array('status' => 'error', 'response' => $this->error), true);
				exit;
			}
		}


		if($this->config->get('captcha_google_recaptcha_v3_status') == 1) {
			$this->load->language('extension/captcha/google_recaptcha_v3_error');
			if (isset($this->request->post['recaptcha_response'])) {
				$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$this->config->get('captcha_google_recaptcha_v3_secretkey').'&response='.$this->request->post['recaptcha_response']);
				$recaptcha = json_decode($verifyResponse, true);
				if ($recaptcha['success']  && ($recaptcha['score'] >= $this->config->get('captcha_google_recaptcha_v3_score'))) {
					/* Success Google reCAPTCHA */
				} else {
					$this->error['warning'] = $this->language->get('error_verify');
				}
			}
		}
            
		return !$this->error;
	}
			
	public function customfield() {
		$json = array();

		$this->load->model('account/custom_field');

		// Customer Group
		if (isset($this->request->get['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->get['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->get['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
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