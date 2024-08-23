<?php
class ModelExtensionModuleiSenseLabsGdpr extends Model {  
	private $modulePath;
    private $error  = array(); 
    private $data   = array();
  	
    public function __construct($registry) {
        parent::__construct($registry);
        
        $this->config->load('isenselabs/isenselabs_gdpr');
        $this->modulePath           = $this->config->get('isenselabs_gdpr_path');
    }
	
    /*
	** Right to be forgotten
	** Begin
	*/
	// Anonymize customer data
    public function anonymizeData($customer_id = 0, $email = "") {	
		if ($customer_id != '0') {
			// Anonymize personal customer data
			$this->db->query("UPDATE `" . DB_PREFIX . "customer`
				SET
					`firstname` = 'GDPR',
					`lastname` = 'Anonymized " . (int) $this->db->escape($customer_id) . "',
					`email` = 'gdpr_anonymized_" . $this->db->escape($customer_id) . "@email.com',
					`telephone` = '0000000000',
					`fax` = '',
					`cart` = '',
					`wishlist` = '',
					`newsletter` = '0',
					`custom_field` = '',
					`ip` = '0.0.0.0',
					`status` = '0'
				WHERE
					`customer_id` = '" . (int) $this->db->escape($customer_id) . "'
			");

			// Anonymize customer address data
			$this->db->query("UPDATE `" . DB_PREFIX . "address`
				SET
					`firstname` = 'GDPR',
					`lastname` = 'Anonymized " . (int) $this->db->escape($customer_id) . "',
					`company` = 'Unknown',
					`address_1` = 'Unknown',
					`address_2` = 'Unknown',
					`city` = 'Unknown',
					`postcode` = '0',
					`custom_field` = '[]'
				WHERE
					`customer_id` = '" . (int) $this->db->escape($customer_id) . "'
			");


			// Anonymize customer cart data
			$carttable_check = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "cart'")->rows;
			if (!empty($carttable_check)) {
				$this->db->query("DELETE FROM `" . DB_PREFIX . "cart` WHERE `customer_id` = '" . (int) $this->db->escape($customer_id) . "'");
			}
			
			// Anonymize customer wishlist data
			$wishlish_check = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "customer_wishlist'")->rows;
			if (!empty($wishlist_check)) {
				$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_wishlist` WHERE `customer_id` = '" . (int) $this->db->escape($customer_id) . "'");
			}

			// Clear customer activity
			$this->db->query("UPDATE `" . DB_PREFIX . "customer_activity`
				SET
					`ip` =  '0.0.0.0',
					`key` = '',
					`data` = ''
				WHERE
					`customer_id` = '" . (int) $this->db->escape($customer_id) . "'
			");

			// Clear customer IPs
			$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_ip` WHERE `customer_id` = '" . (int) $this->db->escape($customer_id) . "'");
	
		} // end if (customer_id) != 0 ->> 
		
		if (!empty($email)) {
			// Anonymize order data for a customer
			if ($customer_id == 0) $customer_id = mt_rand(100000, 999999);
			$this->db->query("UPDATE `" . DB_PREFIX . "order`
				SET
					`firstname` = 'GDPR',
					`lastname` = 'Anonymized " . (int) $this->db->escape($customer_id) . "',
					`email` = 'gdpr_anonymized_" . $this->db->escape($customer_id) . "@email.com',
					`telephone` = '0000000000',
					`fax` = '0000000000',
					`payment_firstname` = 'GDPR',
					`payment_lastname` = 'Anonymized " . (int) $this->db->escape($customer_id) . "',
					`payment_company` = 'Unknown',
					`payment_address_1` = 'Unknown',
					`payment_address_2` = 'Unknown',
					`payment_city` = 'Unknown',
					`payment_postcode` = '0',
					`payment_country` = 'Unknown',
					`payment_zone` = 'Unknown',
					`payment_custom_field` = '[]',
					`payment_method` = 'Unknown',
					`shipping_firstname` = 'GDPR',
					`shipping_lastname` = 'Anonymized " . (int) $this->db->escape($customer_id) . "',
					`shipping_company` = 'Unknown',
					`shipping_address_1` = 'Unknown',
					`shipping_address_2` = 'Unknown',
					`shipping_city` = 'Unknown',
					`shipping_postcode` = '0',
					`shipping_country` = 'Unknown',
					`shipping_zone` = 'Unknown',
					`shipping_custom_field` = '[]',
					`shipping_method` = 'Unknown',
					`tracking` = '',
					`comment` = '',
					`ip` =  '0.0.0.0',
					`forwarded_ip` =  '',
					`user_agent` = '',
					`accept_language` = ''
				WHERE
					`email` = '" . $this->db->escape($email) . "'
			");
		}
		
		return true;
	
    }
	/*
	** Right to be forgotten
	** End
	*/
	
	/*
	** Right to data portability requests
	** Begin
	*/
	public function getCustomerPersonalInfo($email) {
		if (!empty($email)) {
			$data = $this->db->query("SELECT `customer_id`, `firstname`, `lastname`, `email`, `telephone`, `fax`, IF(`newsletter` = 0, 'No', 'Yes') as `newsletter_subscription`, `ip`, `date_added` FROM `" . DB_PREFIX . "customer` WHERE `email` = '" . $this->db->escape($email) . "' LIMIT 1")->rows;
			
			return $data;
		} else {
			return array();
		}
	}
	
	public function getCustomerOrders($email) {
		if (!empty($email)) {
			$data = $this->db->query("SELECT order_id, customer_id, firstname, lastname, email, telephone, fax, payment_firstname, payment_lastname, payment_company, payment_address_1, payment_address_2, payment_city, payment_postcode, payment_country, payment_zone, payment_method, shipping_firstname, shipping_lastname, shipping_company, shipping_address_1, shipping_address_2, shipping_city, shipping_postcode, shipping_country, shipping_zone, shipping_method, '' as `products`, total, (SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = o.language_id) AS order_status, comment, currency_code, ip, user_agent, accept_language, date_added, date_modified
			FROM `" . DB_PREFIX . "order` o WHERE `email` = '" . $this->db->escape($email) . "' AND `store_id` = '" . (int)$this->config->get('config_store_id') . "'")->rows;
			
			if (!empty($data)) {
				foreach ($data as &$result) {
					$products = $this->db->query("SELECT CONCAT(quantity, 'x ',name) as `product` FROM `" . DB_PREFIX . "order_product` WHERE order_id='" . (int)$result['order_id'] . "' ")->rows;
					$product_string = "";
					if (!empty($products)) {
						foreach ($products as $product) {
							$product_string .= $product['product'] . ", ";
						}
					}
					$result['products'] = rtrim($product_string, ", ");
				}

				return $data;
			} else {
				return array();
			}
		} else {
			return array();
		}
	}
	
	public function getCustomerAddresses($email) {
		if (!empty($email)) {
			$data = $this->db->query("SELECT a.firstname, a.lastname, a.company, a.address_1, a.address_2, a.city, a.postcode, cc.name as `country`, z.name as `zone` FROM `" . DB_PREFIX . "address` a
			JOIN `" . DB_PREFIX . "customer` c on (c.customer_id=a.customer_id)
			LEFT JOIN `" . DB_PREFIX . "country` cc on (cc.country_id=a.country_id)
			LEFT JOIN `" . DB_PREFIX . "zone` z on (z.zone_id=a.zone_id)
			WHERE `email` = '" . $this->db->escape($email) . "'")->rows;
			
			return $data;
		} else {
			return array();
		}
	}
	
	public function getGdprRequests($email) {
		if (!empty($email)) {
			$data = $this->db->query("SELECT * FROM `" . DB_PREFIX . "isense_gdpr_requests` WHERE `email` = '" . $this->db->escape($email) . "' AND `store_id` = '" . (int)$this->config->get('config_store_id') . "'")->rows;
			
			return $data;
		} else {
			return array();
		}
	}
	/*
	** Right to data portability requests
	** End
	*/
	
	/*
	** Access to personal data
	** Begin
	*/	
	public function sendPersonalDataMail($data = array()) {
		// Languages
		$language_strings = $this->language->load($this->modulePath);
        foreach ($language_strings as $code => $languageVariable) {
			$this->data[$code] = $languageVariable;
		}
		
		if (empty($data['store_id'])) {
			$data['store_id'] = $this->config->get('config_store_id');
		}
		$store_data = $this->getCurrentStore($data['store_id']);
		
		$this->data['link'] = $this->url->link($this->modulePath . '/view_personal_data', 'hash=' . $data['hash'], true);
		$this->data['logo'] = $store_data['url'] . 'image/' . $this->config->get('config_logo');
		$this->data['store_name'] = $store_data['name'];
		$this->data['store_url'] = $store_data['url'];
		$this->data['text_active_link_helper'] = str_replace('%s%', $data['active_hours'], $this->data['text_active_link_helper']);
		
		$email_parameters = array(
			'email' => $data['email'],
			'message' => $this->load->view($this->modulePath . '/mail/personal_data_request', $this->data),
			'subject' => $this->data['text_personal_data_subject'] . ' - ' . $store_data['name'],
			
		);
		
		return $this->sendMail($email_parameters);
	}
	/*
	** Access to personal data
	** End
	*/
	
	/*
	** Right to be forgotten
	** Begin
	*/
	public function sendDeleteDataMail($data = array()) {
        // Languages
		$language_strings = $this->language->load($this->modulePath);
        foreach ($language_strings as $code => $languageVariable) {
			$this->data[$code] = $languageVariable;
		}
		
		if (empty($data['store_id'])) {
			$data['store_id'] = $this->config->get('config_store_id');
		}
		$store_data = $this->getCurrentStore($data['store_id']);
		
		$this->data['link'] = $this->url->link($this->modulePath . '/delete_data', 'hash=' . $data['hash'], true);
		$this->data['logo'] = $store_data['url'] . 'image/' . $this->config->get('config_logo');
		$this->data['store_name'] = $store_data['name'];
		$this->data['store_url'] = $store_data['url'];
		$this->data['text_active_link_helper'] = str_replace('%s%', $data['active_hours'], $this->data['text_active_link_helper']);
		$this->data['text_button_view_data'] = $this->data['text_button_delete_data'];
		$this->data['text_greeting'] = $this->data['text_greeting_delete'];
		
		$email_parameters = array(
			'email' => $data['email'],
			'message' => $this->load->view($this->modulePath . '/mail/delete_request', $this->data),
			'subject' => $this->data['text_delete_data_subject'] . ' - ' . $store_data['name'],
			
		);
		
		return $this->sendMail($email_parameters);
	}
	
	public function cancelDeletion($hash = "") {
		if (!empty($hash)) {
			$result = $this->db->query("DELETE FROM `" . DB_PREFIX . "isense_gdpr_submissions` WHERE `hash` = '" . $this->db->escape($hash) . "' LIMIT 1");
		} 		
		
		return true;
	}
	
	public function acceptDeletion($email = "", $manual_deletion = true) {
		$language_id = $this->config->get('config_language_id');
		$store_id = $this->config->get('config_store_id');
		$customer_id = 0;
		
		if (!empty($email)) {
			$get_customer_data = $this->db->query("SELECT `customer_id` FROM `" . DB_PREFIX . "customer` WHERE `email` = '" . $this->db->escape($email) . "' LIMIT 1")->row;
			
			if (!empty($get_customer_data['customer_id'])) {
				$customer_id = $get_customer_data['customer_id'];
			}
			
			if ($manual_deletion) {
				
				$result = $this->db->query("INSERT INTO `" . DB_PREFIX . "isense_gdpr_deletions`
					SET
						`email` = '" . $this->db->escape($email) . "',
						`customer_id` = '" . (int) $this->db->escape($customer_id) . "',
						`date_added` = NOW(),
						`language_id` = '" . (int) $this->db->escape($language_id) . "',
						`store_id` = '" . (int) $this->db->escape($store_id) . "'
				");

				// Send Admin Mail
				$language_strings = $this->language->load($this->modulePath);
				foreach ($language_strings as $code => $languageVariable) {
					$this->data[$code] = $languageVariable;
				}

				$store_data = $this->getCurrentStore($store_id);

				$this->data['logo'] = $store_data['url'] . 'image/' . $this->config->get('config_logo');
				$this->data['store_name'] = $store_data['name'];
				$this->data['store_url'] = $store_data['url'];
				$this->data['text_greeting'] = str_replace("%s%", $email ,$this->data['text_greeting_rtb']);

				$email_parameters = array(
					'email' => $this->config->get('config_email'),
                    'message' => $this->load->view($this->modulePath . '/mail/delete_request_admin', $this->data),
					'subject' => $this->data['text_right_to_be_forgotten_request'] . ' - ' . $store_data['name'],

				);

				$mail = $this->sendMail($email_parameters);
			} else {
				$result = $this->anonymizeData($customer_id, $email);
			}
		} 		
		
		return true;
	}
	/*
	** Right to be forgotten
	** End
	*/
    
    /*
	** Newsletter double opt-in
	** Begin
	*/
	public function sendNewsletterOptinEmail($data = array()) {
		// Languages
		$language_strings = $this->language->load($this->modulePath);
        foreach ($language_strings as $code => $languageVariable) {
			$this->data[$code] = $languageVariable;
		}
		
		if (empty($data['store_id'])) {
			$data['store_id'] = $this->config->get('config_store_id');
		}
		$store_data = $this->getCurrentStore($data['store_id']);
        
        $data['hash'] = $data['email'] . '|||' . $data['customer_id']; 
        $data['hash'] = base64_encode($data['hash']); 
		
		$this->data['link'] = $this->url->link($this->modulePath . '/newsletter_confirm', 'hash=' . $data['hash'], true);
		$this->data['logo'] = $store_data['url'] . 'image/' . $this->config->get('config_logo');
		$this->data['store_name'] = $store_data['name'];
		$this->data['store_url'] = $store_data['url'];
		$this->data['text_button_view_data'] = $this->data['text_button_confirm_subscription'];
		$this->data['text_greeting'] = $this->data['text_confirm_subscription'];

		$email_parameters = array(
			'email' => $data['email'],
			'message' => $this->load->view($this->modulePath . '/mail/newsletter_confirm', $this->data),
			'subject' => $this->data['text_confirm_subscription_subject'] . ' - ' . $store_data['name'],
			
		);
		
		return $this->sendMail($email_parameters);
	}
    
    public function confirmNewsletterSubscription($newsletter_status, $email) {
		$result = $this->db->query("UPDATE `" . DB_PREFIX . "customer` SET `newsletter` = '" . (int)$newsletter_status . "' WHERE `email` = '" . $this->db->escape($email) . "'");
        
        return true;
	}
    /*
	** Newsletter double opt-in
	** End
	*/

	/*
	** Newsletter Journal3 double opt-in
	** Begin
	*/
	public function sendJournal3NewsletterOptinEmail($data = array()) {
		// Languages
		$language_strings = $this->language->load($this->modulePath);
        foreach ($language_strings as $code => $languageVariable) {
			$this->data[$code] = $languageVariable;
		}
		
		if (empty($data['store_id'])) {
			$data['store_id'] = $this->config->get('config_store_id');
		}
		$store_data = $this->getCurrentStore($data['store_id']);
        
        $data['hash'] = $data['email'] . '|||' . $data['customer_id']; 
        $data['hash'] = base64_encode($data['hash']); 
		
		$this->data['link'] = $this->url->link($this->modulePath . '/newsletter_confirm_j3', 'hash=' . $data['hash'], true);
		$this->data['logo'] = $store_data['url'] . 'image/' . $this->config->get('config_logo');
		$this->data['store_name'] = $store_data['name'];
		$this->data['store_url'] = $store_data['url'];
		$this->data['text_button_view_data'] = $this->data['text_button_confirm_subscription'];
		$this->data['text_greeting'] = $this->data['text_confirm_subscription'];

		$email_parameters = array(
			'email' => $data['email'],
			'message' => $this->load->view($this->modulePath . '/mail/newsletter_confirm_j3', $this->data),
			'subject' => $this->data['text_confirm_subscription_subject'] . ' - ' . $store_data['name'],
			
		);
		
		return $this->sendMail($email_parameters);
	}
    
    public function confirmJournal3NewsletterSubscription($email) {
		$remoteADDR = $this->request->server['REMOTE_ADDR'] ? $this->request->server['REMOTE_ADDR'] : '127.0.0.1';
		$result = $this->db->query("INSERT INTO `" . DB_PREFIX . "journal3_newsletter` SET `name` = '', `email` = '" . $this->db->escape($email) . "', `ip` = '" . $this->db->escape($remoteADDR) . "', `store_id` = '" . (int)$this->config->get('config_store_id') . "'");
        
        return true;
	}
    /*
	** Newsletter Journal3 double opt-in
	** End
	*/

	/*
	** Newsletter Journal2 double opt-in
	** Begin
	*/
	public function sendJournal2NewsletterOptinEmail($data = array()) {
		// Languages
		$language_strings = $this->language->load($this->modulePath);
        foreach ($language_strings as $code => $languageVariable) {
			$this->data[$code] = $languageVariable;
		}
		
		if (empty($data['store_id'])) {
			$data['store_id'] = $this->config->get('config_store_id');
		}
		$store_data = $this->getCurrentStore($data['store_id']);
        
        $data['hash'] = $data['email'] . '|||' . $data['customer_id']; 
        $data['hash'] = base64_encode($data['hash']); 
		
		$this->data['link'] = $this->url->link($this->modulePath . '/newsletter_confirm_j2', 'hash=' . $data['hash'], true);
		$this->data['logo'] = $store_data['url'] . 'image/' . $this->config->get('config_logo');
		$this->data['store_name'] = $store_data['name'];
		$this->data['store_url'] = $store_data['url'];
		$this->data['text_button_view_data'] = $this->data['text_button_confirm_subscription'];
		$this->data['text_greeting'] = $this->data['text_confirm_subscription'];

		$email_parameters = array(
			'email' => $data['email'],
			'message' => $this->load->view($this->modulePath . '/mail/newsletter_confirm_j2', $this->data),
			'subject' => $this->data['text_confirm_subscription_subject'] . ' - ' . $store_data['name'],
			
		);
		
		return $this->sendMail($email_parameters);
	}
    
    public function confirmJournal2NewsletterSubscription($email) {
		$result = $this->db->query("INSERT INTO `" . DB_PREFIX . "journal2_newsletter` SET `email` = '" . $this->db->escape($email) . "', `token` = '" . sha1(mt_rand()) . "', `store_id` = '" . (int)$this->config->get('config_store_id') . "'");
        
        return true;
	}
    /*
	** Newsletter Journal2 double opt-in
	** End
	*/

	/*
	** Global functions
	** Start
	*/
	public function checkIfEmailExists($email = "", $guest_check = false) {
		if (!empty($email)) {
			$data = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` WHERE `email` = '" . $this->db->escape($email) . "' LIMIT 1")->num_rows;
			if (!$data && $guest_check) {
				$data = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE `email` = '" . $this->db->escape($email) . "' LIMIT 1")->num_rows;
			}
			
			return $data;
		} else {
			return false;	
		}
	}
	
	public function checkHash($hash = "", $active_hours = 5) {
		if (!empty($hash)) {
			$result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "isense_gdpr_submissions` WHERE `hash` = '" . $this->db->escape($hash) . "' AND `date_added` >= (NOW() - INTERVAL " . (int)$active_hours . " HOUR) LIMIT 1");
			
			if (!empty($result)) {
				return $result->row;
			}
		} 		
		
		return false;
	}
	
	public function insertSubmission($email = "", $type = "") {
		if (!empty($email) && !empty($type)) {
			try {
				$code = token(25);

				$this->db->query("INSERT INTO `" . DB_PREFIX . "isense_gdpr_submissions`
					SET
						`email` = '" . $this->db->escape($email) . "',
						`type` = '" . $this->db->escape($type) . "',
						`hash` = '" . $this->db->escape($code) . "',
						`date_added` = NOW()
					");
				
				return array('id' => $this->db->getLastId(),
							 'hash' => $code,
							 'email' => $email
							 );
			} catch (Exception $e) {
				$this->log->write('iSenseLabs GDPR ::: Error when inserting a new submission to the GDRP submission list. Additional data: ' . $email . ' //// ' . $type);
				$this->log->write('iSenseLabs GDPR ::: ^^^^ Exception error: '. $e->getMessage());
			}
			
			return true;
		} else {
			return false;
		}
	}
	
	public function sendMail($data = array()) {
		if (empty($data['store_id'])) {
			$data['store_id'] = $this->config->get('config_store_id');
		}
		$store_data = $this->getCurrentStore($data['store_id']);
		
		$mail = new Mail($this->config->get('config_mail_engine'));
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

		$mail->setTo($data['email']);
		$mail->setFrom($store_data['store_email']);
		$mail->setSender(html_entity_decode($store_data['name'], ENT_QUOTES, 'UTF-8'));
		$mail->setSubject(html_entity_decode($data['subject'], ENT_QUOTES, 'UTF-8'));
		$mail->setHtml($data['message']);
		$mail->send();

		if ($mail) 
			return true;
		else
			return false;
	}
	
	private function getCurrentStore($store_id) {    
        if($store_id && $store_id != 0) {
            $store = $this->getStore($store_id);
            $store['store_email'] = $this->db->query("SELECT `value` FROM `" . DB_PREFIX . "setting` WHERE `key`= 'config_email' AND `store_id`=".$this->db->escape($store_id))->row['value'];
        } else {
            $store['store_id'] = 0;
            $store['name'] = $this->config->get('config_name');
            $store['url'] = $this->getCatalogURL();
            $store['store_email'] = $this->config->get('config_email');
        }
        return $store;
    }
	
	private function getCatalogURL() {
        if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
            $storeURL = HTTP_SERVER;
        } else {
            $storeURL = HTTPS_SERVER;
        } 
        return $storeURL;
    }
	
	public function getStore($store_id) {    
        if($store_id && $store_id != 0) {
            $store = $this->getStoreData($store_id);
        } else {
            $store['store_id'] = 0;
            $store['name'] = $this->config->get('config_name');
            $store['url'] = $this->getCatalogURL();
        }
        return $store;
    }
	
	private function getStoreData($store_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "store WHERE store_id = '" . (int)$store_id . "'");

		return $query->row;
	}
	/*
	** Global functions
	** End
	*/   
}