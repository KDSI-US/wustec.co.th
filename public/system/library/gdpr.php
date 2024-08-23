<?php
class Gdpr {
	/* 
	** Our GDPR module comes with a library which can be
	** used for integration with other third-party modules
	**
	** This is useful if you are using a third-party extension
	** which forces your customers to agree to a policy. If this
	** extension is using non-OpenCart functions, this means that 
	** our module will not record those actions automatically.
	** With our libray, you will be able to do that with just
	** a couple of lines.
	**
	** For more information visit: https://www.isenselabs.com
	**
	** Usage:
	** $this->load->library('gdpr');
	** $this->gdpr->{method_name};
	**
	** Example:
	** $this->load->library('gdpr');
	** $this->gdpr->newRequest('Download Data', 'testemail@gmail.com');
	**
	** For more information visit: https://www.isenselabs.com
	*/
	
	/*
	** Getting all OpenCart libraries required for the GDPR module to work correctly
	*/
	public function __construct($registry) {
		// Core OpenCart
		$this->config 		= $registry->get('config');
		$this->db 			= $registry->get('db');
		$this->request 		= $registry->get('request');
		$this->session 		= $registry->get('session');
		$this->customer 	= $registry->get('customer');
		$this->log 			= $registry->get('log');
		
		// Module Settings		
        $this->storeId      = $this->config->get('config_store_id');
        $setingsQuery = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$this->storeId . "' AND `code` = 'isenselabs_gdpr'");
		foreach ($setingsQuery->rows as $result) {
			if (!$result['serialized']) {
				$moduleSettings[$result['key']] = $result['value'];
			} else {
				$moduleSettings[$result['key']] = json_decode($result['value'], true);
			}
		}
        $this->moduleData	= !empty($moduleSettings['isenselabs_gdpr']) ? $moduleSettings['isenselabs_gdpr'] : array();;
	}
	
	/*
	** Adding a new GDPR Request
	*/
	public function newRequest($type = '', $email = '') {	
		$client_ip 			= !empty($this->request->server['REMOTE_ADDR']) ? $this->request->server['REMOTE_ADDR'] : '0.0.0.0';
		$server_ip 			= !empty($this->request->server['SERVER_ADDR']) ? $this->request->server['SERVER_ADDR'] : '0.0.0.0';
		$user_agent 		= !empty($this->request->server['HTTP_USER_AGENT']) ? $this->request->server['HTTP_USER_AGENT'] : 'not provided';
		$accept_language	= !empty($this->request->server['HTTP_ACCEPT_LANGUAGE']) ? $this->request->server['HTTP_ACCEPT_LANGUAGE'] : 'not provided';
		
		if (!empty($this->moduleData['Enabled']) && $this->moduleData['Enabled'] == '1' && !empty($email) && !empty($type)) {
			try {
				$customer_check = $this->db->query("SELECT `customer_id` FROM `" . DB_PREFIX . "customer` WHERE `email` = '" . $this->db->escape($email) . "' LIMIT 1")->row;
				if (!empty($customer_check)) {
					$customer_id = $customer_check['customer_id'];
				} else {
					$customer_id = 0;
				}
				
				$this->db->query("INSERT INTO `" . DB_PREFIX . "isense_gdpr_requests`
				SET
					`customer_id` = '" . (int)$customer_id . "',
					`email` = '" . $this->db->escape($email) . "',
					`type` = '" . $this->db->escape($type) . "',
					`user_agent` = '" . $this->db->escape($user_agent) . "',
					`accept_language` = '" . $this->db->escape($accept_language) . "',
					`client_ip` = '" . $this->db->escape($client_ip) . "',
					`server_ip` = '" . $this->db->escape($server_ip) . "',
                    `store_id` = '" . $this->db->escape($this->storeId) . "',
					`request_added` = NOW()
				");

			} catch (Exception $e) {
				$this->log->write('iSenseLabs GDPR ::: Error when adding a new request to the GDRP requests list. Additional data: ' . $user_agent . ' //// ' . $client_ip);
				$this->log->write('iSenseLabs GDPR ::: ^^^^ Exception error: '. $e->getMessage());
			}
			
			return true;
		} else {
			$this->log->write('iSenseLabs GDPR ::: Error when adding a new request to the GDRP requests list. Additional data: ' . $user_agent . ' //// ' . $client_ip);
			$this->log->write('iSenseLabs GDPR ::: ^^^^ REASON: $email or $type was missing, or the module is disabled.');
		}
		
		return false;
	}
	
	/*
	** When a Customer Accepts a policy on the store
	*/
	public function newAcceptanceRequest($page_id = '', $email = '', $log_error = true) {
		$client_ip 			= !empty($this->request->server['REMOTE_ADDR']) ? $this->request->server['REMOTE_ADDR'] : '0.0.0.0';
		$user_agent 		= !empty($this->request->server['HTTP_USER_AGENT']) ? $this->request->server['HTTP_USER_AGENT'] : 'not provided';
		$accept_language	= !empty($this->request->server['HTTP_ACCEPT_LANGUAGE']) ? $this->request->server['HTTP_ACCEPT_LANGUAGE'] : 'not provided';
		
		if (!empty($this->moduleData['Enabled']) && $this->moduleData['Enabled'] == '1' && !empty($email) && !empty($page_id)) {
			try {
				$page_info = $this->db->query("SELECT * FROM `" . DB_PREFIX . "isense_gdpr_policies` WHERE `policy_id` = '" . (int)$page_id . "' AND `language_id` = '" . (int)$this->config->get('config_language_id') . "' AND `store_id` = '" . 0 . "' ORDER BY `date_added` DESC LIMIT 1")->row;
				
				if ($page_info) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "isense_gdpr_policy_acceptances`
					SET
						`email` = '" . $this->db->escape($email) . "',
						`policy_id` = '" . (int)$this->db->escape($page_info['gdpr_policy_id']) . "',
						`name` = '" . $this->db->escape($page_info['title']) . "',
						`content` = '',
						`user_agent` = '" . $this->db->escape($user_agent) . "',
						`accept_language` = '" . $this->db->escape($accept_language) . "',
						`client_ip` = '" . $this->db->escape($client_ip) . "',
                        `store_id` = '" . $this->db->escape($this->storeId) . "',
						`date_added` = NOW()
					");
				} else {
					$this->log->write('iSenseLabs GDPR ::: Error when adding a new policy acceptance to the GDRP acceptances list.');
					$this->log->write('iSenseLabs GDPR ::: ^^^^ REASON: $page_id was not pointing to an actual page.');
				}
			} catch (Exception $e) {
				$this->log->write('iSenseLabs GDPR ::: Error when adding a new policy acceptance to the GDRP acceptances list.');
				$this->log->write('iSenseLabs GDPR ::: ^^^^ Exception error: '. $e->getMessage());
			}
			
			return true;
		} else {
			if ($log_error) {
				$this->log->write('iSenseLabs GDPR ::: Error when adding a new policy acceptance to the GDRP acceptances list.');
				$this->log->write('iSenseLabs GDPR ::: ^^^^ REASON: $email or $page_id was missing, or the module is disabled.');
			}
		}
		
		return false;
	}

	/*
	** Opt-in/Opt-out
	*/
	public function newOptin($page_id = '', $email = '', $type = '', $action = 'opt-in') {
		$client_ip 			= !empty($this->request->server['REMOTE_ADDR']) ? $this->request->server['REMOTE_ADDR'] : '0.0.0.0';
		$user_agent 		= !empty($this->request->server['HTTP_USER_AGENT']) ? $this->request->server['HTTP_USER_AGENT'] : 'not provided';
		$accept_language	= !empty($this->request->server['HTTP_ACCEPT_LANGUAGE']) ? $this->request->server['HTTP_ACCEPT_LANGUAGE'] : 'not provided';
		
		if (!empty($this->moduleData['Enabled']) && $this->moduleData['Enabled'] == '1' && !empty($email) && !empty($page_id)) {
			try {
				$page_info = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE i.information_id = '" . (int)$page_id . "' AND id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1'")->row;
				
				if ($page_info) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "isense_gdpr_optins`
					SET
						`email` = '" . $this->db->escape($email) . "',
						`policy_id` = '" . (int)$this->db->escape($page_id) . "',
						`type` = '" . $this->db->escape($type) . "',
						`action` = '" . $this->db->escape($action) . "',
						`user_agent` = '" . $this->db->escape($user_agent) . "',
						`accept_language` = '" . $this->db->escape($accept_language) . "',
						`client_ip` = '" . $this->db->escape($client_ip) . "',
                        `store_id` = '" . $this->db->escape($this->storeId) . "',
						`date_added` = NOW()
					");
				} else {
					$this->log->write('iSenseLabs GDPR ::: Error when adding a new ' . $action . ' to the GDRP Opt-in/Opt-out list.');
					$this->log->write('iSenseLabs GDPR ::: ^^^^ REASON: $page_id was not pointing to an actual page.');
				}
			} catch (Exception $e) {
				$this->log->write('iSenseLabs GDPR ::: Error when adding a new ' . $action . ' to the Opt-in/Opt-out list.');
				$this->log->write('iSenseLabs GDPR ::: ^^^^ Exception error: '. $e->getMessage());
			}
			
			return true;
		} else {
			$this->log->write('iSenseLabs GDPR ::: Error when adding a new ' . $action . ' to the Opt-in/Opt-out list.');
			$this->log->write('iSenseLabs GDPR ::: ^^^^ REASON: $email or $page_id was missing, or the module is disabled.');
		}
		
		return false;
	}
}