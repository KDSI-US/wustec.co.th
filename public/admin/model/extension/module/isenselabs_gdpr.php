<?php
class ModelExtensionModuleiSenseLabsGdpr extends Model {  
	private $modulePath;
    private $data = array();
	private $eventGroup = "isenselabs_gdpr";
  	
    public function __construct($registry) {
        parent::__construct($registry);
        
        $this->config->load('isenselabs/isenselabs_gdpr');
        $this->modulePath = $this->config->get('isenselabs_gdpr_path');
    }
	
    public function initDb() {
        // Create table for GDPR Requests
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "isense_gdpr_requests` (
            `request_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `customer_id` int(10)  NOT NULL DEFAULT '0',
            `email` varchar(255) NOT NULL,
            `type` varchar(255) NOT NULL,
            `user_agent` varchar(255) NOT NULL,
            `accept_language` varchar(100) NOT NULL,
			`client_ip` varchar(100) NOT NULL, 
			`server_ip` varchar(100) NOT NULL,
            `request_added` datetime NOT NULL,
            `store_id` int(10)  NOT NULL DEFAULT '0',
            PRIMARY KEY (`request_id`)
        ) CHARACTER SET utf8 COLLATE utf8_general_ci");
		
		// Create table for GDPR Policy Acceptances
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "isense_gdpr_policy_acceptances` (
            `acceptance_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `email` varchar(255) NOT NULL,
            `policy_id` varchar(255) NOT NULL,
            `name` varchar(255) NOT NULL,
            `content` longtext NOT NULL,
			`user_agent` varchar(255) NOT NULL,
            `accept_language` varchar(100) NOT NULL,
			`client_ip` varchar(100) NOT NULL, 
            `date_added` datetime NOT NULL,
            `store_id` int(10)  NOT NULL DEFAULT '0',
            PRIMARY KEY (`acceptance_id`)
        ) CHARACTER SET utf8 COLLATE utf8_general_ci");
		
		// Create table for GDPR Submissions
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "isense_gdpr_submissions` (
            `submission_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `email` varchar(255) NOT NULL,
			`type` varchar(255) NOT NULL,
            `hash` varchar(255) NOT NULL,
            `date_added` datetime NOT NULL,
            `store_id` int(10)  NOT NULL DEFAULT '0',
            PRIMARY KEY (`submission_id`)
        ) CHARACTER SET utf8 COLLATE utf8_general_ci");

        // Create table for GDPR Optins
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "isense_gdpr_optins` (
            `optin_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `email` varchar(255) NOT NULL,
            `policy_id` varchar(255) NOT NULL,
            `type` varchar(100) NOT NULL,
            `action` varchar(100) NOT NULL,
            `user_agent` varchar(255) NOT NULL,
            `accept_language` varchar(100) NOT NULL,
            `client_ip` varchar(100) NOT NULL, 
            `date_added` datetime NOT NULL,
            `store_id` int(10)  NOT NULL DEFAULT '0',
            PRIMARY KEY (`optin_id`)
        ) CHARACTER SET utf8 COLLATE utf8_general_ci");
		
		// Create table for GDPR Right to be Forgotten Feature
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "isense_gdpr_deletions` (
            `deletion_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `email` varchar(255) NOT NULL,
            `customer_id` int(10)  NOT NULL DEFAULT '0',
            `date_added` datetime NOT NULL,
			`date_deletion` datetime NOT NULL,
			`customer_data` tinyint(1) NOT NULL DEFAULT 0,
			`address_data` tinyint(1) NOT NULL DEFAULT 0,
			`order_data` tinyint(1) NOT NULL DEFAULT 0,
			`gdpr_data` tinyint(1) NOT NULL DEFAULT 0,
			`message` text NOT NULL,
			`notified` tinyint(1) NOT NULL DEFAULT 0,
			`status` tinyint(1) NOT NULL DEFAULT 0,
			`language_id` tinyint(1) NOT NULL DEFAULT 1,
			`store_id` tinyint(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (`deletion_id`)
        ) CHARACTER SET utf8 COLLATE utf8_general_ci");
        
        
        // GDPR Compliance 1.7/2.7/3.7
        $check_update_multi_store = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "isense_gdpr_requests` LIKE 'store_id'");
		if (!$check_update_multi_store->rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "isense_gdpr_requests` ADD `store_id` int(10)  NOT NULL DEFAULT '0'");
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "isense_gdpr_policy_acceptances` ADD `store_id` int(10)  NOT NULL DEFAULT '0'");
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "isense_gdpr_submissions` ADD `store_id` int(10)  NOT NULL DEFAULT '0'");
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "isense_gdpr_optins` ADD `store_id` int(10)  NOT NULL DEFAULT '0'");
		}
        
        // GDPR Compliance 1.9/2.9/3.9
        // Create table for the GDPR policies
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "isense_gdpr_policies` (
            `gdpr_policy_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `policy_id` varchar(255) NOT NULL,
            `title` varchar(255) NOT NULL,
            `description` longtext NOT NULL,
            `date_added` datetime NOT NULL,
            `language_id` int(10)  NOT NULL DEFAULT '0',
            `store_id` int(10)  NOT NULL DEFAULT '0',
            PRIMARY KEY (`gdpr_policy_id`)
        ) CHARACTER SET utf8 COLLATE utf8_general_ci");
        
        $check_for_policy_rows = $this->db->query("SELECT count(*) as `counter` FROM `" . DB_PREFIX . "isense_gdpr_policies`")->row['counter'];
        if ($check_for_policy_rows == 0) {
            $information_pages = $this->db->query("SELECT * FROM `" . DB_PREFIX . "information_description`");
            if ($information_pages->num_rows > 0) {
                $stores_query = $this->db->query("SELECT `store_id` FROM `" . DB_PREFIX. "store`");
                $stores = array();
                $stores[] = 0;
                if ($stores_query->num_rows) {
                    foreach ($stores_query->rows as $stor) {
                        $stores[] = $stor['store_id'];
                    }
                }
                foreach ($information_pages->rows as $info_row) {
                    foreach ($stores as $store_id) {
                        $insert_data = $this->db->query("INSERT INTO `" . DB_PREFIX . "isense_gdpr_policies` SET `policy_id` = '" . (int) $info_row['information_id'] . "', `title` = '" . $this->db->escape($info_row['title']) . "', `description` = '" . $this->db->escape($info_row['description']) . "', `date_added` = NOW(), `language_id` = '" . (int) $info_row['language_id'] . "', `store_id` = '" . $this->db->escape($store_id) . "'");
                    }
                }
            }
        }
        
    }
	
	public function setupEventHandlers() {
        $this->model_setting_event->deleteEventByCode($this->eventGroup);

        // Catalog events
        $this->model_setting_event->addEvent($this->eventGroup, "catalog/view/common/footer/after", $this->modulePath . "/addCookieConsentBar");
        $this->model_setting_event->addEvent($this->eventGroup, "catalog/view/extension/module/icustomfooter/icustomfooter/after", $this->modulePath . "/addCookieConsentBarICustomFooter");
        $this->model_setting_event->addEvent($this->eventGroup, "catalog/view/account/account/after", $this->modulePath . "/addGDPRTools");
		$this->model_setting_event->addEvent($this->eventGroup, "catalog/controller/extension/analytics/google/after", $this->modulePath . "/disableAnalytics");
		$this->model_setting_event->addEvent($this->eventGroup, "catalog/view/product/product/after", $this->modulePath . "/disableAddThis");
    }

	public function removeEventHandlers() {
        $this->model_setting_event->deleteEventByCode($this->eventGroup);
    }

	// Requests - Begin
	private function getRequestFilterData($data = array()) {
		$query = '';
		
		if (!empty($data['request_id'])) {
            $query .= " AND `request_id` = '" . (int)$this->db->escape($data['request_id']) . "'";
        }
		
        if (!empty($data['email'])) {
            $query .= " AND `email` LIKE '%" . $this->db->escape($data['email']) . "%'";
        }
		
		if (!empty($data['type'])) {
            $query .= " AND `type` = '" . $this->db->escape($data['type']) . "'";
        }
		
		if (!empty($data['user_agent'])) {
            $query .= " AND `user_agent` LIKE '%" . $this->db->escape($data['user_agent']) . "%'";
        }
		
		if (!empty($data['accept_language'])) {
            $query .= " AND `accept_language` LIKE '%" . $this->db->escape($data['accept_language']) . "%'";
        }
		
		if (!empty($data['client_ip'])) {
            $query .= " AND `client_ip` = '" . $this->db->escape($data['client_ip']) . "'";
        }
		
		if (!empty($data['server_ip'])) {
            $query .= " AND `server_ip` = '" . $this->db->escape($data['server_ip']) . "'";
        }
        
        if (!empty($data['date_start'])) {
            $query .= " AND `request_added` >= '" . $this->db->escape($data['date_start']) . " 00:00:00'";
        }
        
        if (!empty($data['date_end'])) {
            $query .= " AND `request_added` <= '" . $this->db->escape($data['date_end']) . " 23:59:59'";
        }
        
        if (!empty($data['store_id'])) {
            $query .= " AND `store_id` = '" . $this->db->escape($data['store_id']) . "'";
        }
		
		return $query;
	}
	
	public function getRequests($data = array(), $page = 1, $limit = 20, $export = false) {
		$query = "SELECT * FROM `" . DB_PREFIX . "isense_gdpr_requests` WHERE 1=1 ";

        $query .= $this->getRequestFilterData($data);
        
        if ($page) {
			$start = ($page - 1) * $limit;
		}
        
        if ($export) { 
            $query .= "ORDER BY `request_id` DESC";
        } else {
            $query .= "ORDER BY `request_id` DESC LIMIT ".$start.", ".$limit; 
        }
        
        $query = $this->db->query($query);

        return $query->rows;
	}
	
	public function getTotalRequests($data = array()) {
		$query = "SELECT COUNT(*) as `count`  FROM `" . DB_PREFIX . "isense_gdpr_requests` WHERE 1=1";
        
       	$query .= $this->getRequestFilterData($data);
		
		$query = $this->db->query($query);
        
		return $query->row['count']; 
	}
	// Requests - End
	
	
	// Policy Acceptances - Begin
	private function getPolicyAcceptancesFilterData($data = array()) {
		$query = '';
		
		if (!empty($data['acceptance_id'])) {
            $query .= " AND `pa`.`acceptance_id` = '" . (int)$this->db->escape($data['acceptance_id']) . "'";
        }
        
        if (!empty($data['email'])) {
            $query .= " AND `pa`.`email` LIKE '%" . $this->db->escape($data['email']) . "%'";
        }
        
        if (!empty($data['name'])) {
            $query .= " AND `pa`.`name` LIKE '%" . $this->db->escape($data['name']) . "%'";
        }

        if (!empty($data['date_start'])) {
            $query .= " AND `pa`.`date_added` >= '" . $this->db->escape($data['date_start']) . " 00:00:00'";
        }
        
        if (!empty($data['date_end'])) {
            $query .= " AND `pa`.`date_added` <= '" . $this->db->escape($data['date_end']) . " 23:59:59'";
        }
        
        if (!empty($data['store_id']) || $data['store_id'] == '0') {
            $query .= " AND `pa`.`store_id` = '" . $this->db->escape($data['store_id']) . "'";
        }
		
		return $query;
	}
	
	public function getPolicyAcceptances($data = array(), $page = 1, $limit = 20, $export = false) {
		$use_new_table = false;
        $check_if_gdpr_policy_table_exists = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "isense_gdpr_policies'");
        if ($check_if_gdpr_policy_table_exists->num_rows) {
            $use_new_table = true;
        }

        if($use_new_table) {
            $query = "SELECT `pa`.*, p.policy_id AS og_policy_id FROM `" . DB_PREFIX . "isense_gdpr_policy_acceptances` pa 
            JOIN `" . DB_PREFIX . "isense_gdpr_policies` p on (pa.policy_id = p.gdpr_policy_id)
            WHERE 1=1 ";
        } else {
            $query = "SELECT `pa`.* FROM `" . DB_PREFIX . "isense_gdpr_policy_acceptances` pa WHERE 1=1 ";
        }

        $query .= $this->getPolicyAcceptancesFilterData($data);
        
        if ($page) {
			$start = ($page - 1) * $limit;
		}
        
        if ($export) { 
            $query .= "ORDER BY `acceptance_id` DESC";
        } else {
            $query .= "ORDER BY `acceptance_id` DESC LIMIT ".$start.", ".$limit;
        }
        
        $query = $this->db->query($query);

        return $query->rows;
	}
	
	public function getTotalPolicyAcceptances($data = array()) {
		$query = "SELECT COUNT(*) as `count`  FROM `" . DB_PREFIX . "isense_gdpr_policy_acceptances` pa WHERE 1=1";
        
       	$query .= $this->getPolicyAcceptancesFilterData($data);
		
		$query = $this->db->query($query);
        
		return $query->row['count']; 
	}
	
	public function getPolicyAcceptanceContent($acceptance_id = 0) {
		if ($acceptance_id != 0) {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "isense_gdpr_policy_acceptances` WHERE `acceptance_id`='" . (int)$this->db->escape($acceptance_id) . "' LIMIT 1")->row;
            
            // GDPR Compliance 1.9/2.9/3.9
            $use_new_table = false;
            $check_if_gdpr_policy_table_exists = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "isense_gdpr_policies'");
            if ($check_if_gdpr_policy_table_exists->num_rows) {
                $use_new_table = true;
            }

            if ($use_new_table && !empty($query['policy_id'])) {   
                $policy_text = $this->db->query("SELECT * FROM `" . DB_PREFIX . "isense_gdpr_policies` WHERE `gdpr_policy_id`= '" . $query['policy_id'] . "' LIMIT 1");
                if ($policy_text->num_rows) {
                    $query['content'] = $policy_text->row['description'];
                }
            }
            // GDPR Compliance 1.9/2.9/3.9
            
			if (!empty($query['content'])) {
				return html_entity_decode($query['content']);
			} else {
				return '';
			}
		} else {
			return '';
		}
	}
	// Policy Acceptances - End

    // Opt-ins - Start

    private function getOptinsFilterData($data = array()) {
        $query = '';
        
        if (!empty($data['optin_id'])) {
            $query .= " AND `optin_id` = '" . (int)$this->db->escape($data['optin_id']) . "'";
        }
        
        if (!empty($data['email'])) {
            $query .= " AND `email` LIKE '%" . $this->db->escape($data['email']) . "%'";
        }

        if (!empty($data['type'])) {
            $query .= " AND `type` LIKE '%" . $this->db->escape($data['type']) . "%'";
        }

        if (!empty($data['action']) && ($data['action']!='-')) {
            $query .= " AND `action` LIKE '%" . $this->db->escape($data['action']) . "%'";
        }

        if (!empty($data['date_start'])) {
            $query .= " AND `date_added` >= '" . $this->db->escape($data['date_start']) . " 00:00:00'";
        }
        
        if (!empty($data['date_end'])) {
            $query .= " AND `date_added` <= '" . $this->db->escape($data['date_end']) . " 23:59:59'";
        }
        
        if (!empty($data['store_id'])) {
            $query .= " AND `store_id` = '" . $this->db->escape($data['store_id']) . "'";
        }
        
        return $query;
    }
    
    public function getOptins($data = array(), $page = 1, $limit = 20, $export = false) {
        $query = "SELECT * FROM `" . DB_PREFIX . "isense_gdpr_optins` WHERE 1=1 ";

        $query .= $this->getOptinsFilterData($data);
        
        if ($page) {
            $start = ($page - 1) * $limit;
        }
        
        if ($export) { 
            $query .= "ORDER BY `optin_id` DESC";
        } else {
            $query .= "ORDER BY `optin_id` DESC LIMIT ".$start.", ".$limit;
        }
        
        $query = $this->db->query($query);

        return $query->rows;
    }
    
    public function getTotalOptins($data = array()) {
        $query = "SELECT COUNT(*) as `count`  FROM `" . DB_PREFIX . "isense_gdpr_optins` WHERE 1=1";
        
        $query .= $this->getOptinsFilterData($data);
        
        $query = $this->db->query($query);
        
        return $query->row['count']; 
    }

    // Opt-ins - End
	
	// Deletions - Start

    private function getDeletionsFilterData($data = array()) {
        $query = '';
   
        if (!empty($data['email'])) {
            $query .= " AND `email` LIKE '%" . $this->db->escape($data['email']) . "%'";
        }

        if (!empty($data['status']) && ($data['status']!='-')) {
            $query .= " AND `status` LIKE '%" . $this->db->escape($data['status']) . "%'";
        }
		
		if (!empty($data['date_deletion'])) {
            $query .= " AND `date_deletion` = '" . $this->db->escape($data['date_deletion']) . " 00:00:00'";
        }

        if (!empty($data['date_start'])) {
            $query .= " AND `date_added` >= '" . $this->db->escape($data['date_start']) . " 00:00:00'";
        }
        
        if (!empty($data['date_end'])) {
            $query .= " AND `date_added` <= '" . $this->db->escape($data['date_end']) . " 23:59:59'";
        }
        
        if (!empty($data['store_id'])) {
            $query .= " AND `store_id` = '" . $this->db->escape($data['store_id']) . "'";
        }
        
        return $query;
    }
    
    public function getDeletions($data = array(), $page = 1, $limit = 20) {
        $query = "SELECT * FROM `" . DB_PREFIX . "isense_gdpr_deletions` WHERE 1=1 ";

        $query .= $this->getDeletionsFilterData($data);
        
        if ($page) {
            $start = ($page - 1) * $limit;
        }
        
        $query .= "ORDER BY `deletion_id` DESC LIMIT ".$start.", ".$limit;
        
        $query = $this->db->query($query);

        return $query->rows;
    }
    
    public function getTotalDeletions($data = array()) {
        $query = "SELECT COUNT(*) as `count`  FROM `" . DB_PREFIX . "isense_gdpr_deletions` WHERE 1=1";
        
        $query .= $this->getDeletionsFilterData($data);
        
        $query = $this->db->query($query);
        
        return $query->row['count']; 
    }
	
	public function getPendingDeletions($store_id = 0) {
		$query = "SELECT COUNT(*) as `count`  FROM `" . DB_PREFIX . "isense_gdpr_deletions` WHERE `status` = 0";
        
        $query .= " AND `store_id` = '" . $this->db->escape($store_id) . "'";
		
		$query = $this->db->query($query);
 		
		return $query->row['count']; 
	}
	
	public function getAwaitingDeletions($store_id = 0) {
		$query = "SELECT COUNT(*) as `count`  FROM `" . DB_PREFIX . "isense_gdpr_deletions` WHERE `date_deletion` >= CURDATE() AND `status`=2";
        
        $query .= " AND `store_id` = '" . $this->db->escape($store_id) . "'";
		
		$query = $this->db->query($query);
 		
		return $query->row['count']; 
	}
	
	public function getDeletion($deletion_id = 0) {
		$query = "SELECT *  FROM `" . DB_PREFIX . "isense_gdpr_deletions` WHERE `deletion_id` = '" . (int) $this->db->escape($deletion_id) . "' LIMIT 1";
		$query = $this->db->query($query);
		
 		return $query->row; 
	}
	
	public function updateDeletionStatus($deletion_id = 0, $status = 0, $message = "") {
		$query = "UPDATE`" . DB_PREFIX . "isense_gdpr_deletions` SET `status` = '" . (int) $this->db->escape($status) . "' WHERE `deletion_id` = '" . (int) $this->db->escape($deletion_id) . "'";
		$query = $this->db->query($query);
		
		if (!empty($message)) {
			$query = "UPDATE`" . DB_PREFIX . "isense_gdpr_deletions` SET `message` = '" . $this->db->escape($message) . "' WHERE `deletion_id` = '" . (int) $this->db->escape($deletion_id) . "'";
			$query = $this->db->query($query);
		}
        
		return true;
	}
	
	public function approveDeletion($data = array()) {
		$query = "UPDATE `" . DB_PREFIX . "isense_gdpr_deletions` 
			SET 
			`status` = '" . (int) $this->db->escape(2) . "',
			`customer_data` = '" . (int) $this->db->escape($data['customer_data']) . "',
			`address_data` = '" . (int) $this->db->escape($data['address_data']) . "',
			`gdpr_data` = '" . (int) $this->db->escape($data['gdpr_data']) . "',
			`order_data` = '" . (int) $this->db->escape($data['order_data']) . "',
			`message` = '" . $this->db->escape($data['message']) . "',
			`date_deletion` = '" . $this->db->escape($data['date_deletion']) . "'
			WHERE `deletion_id` = '" . (int) $this->db->escape($data['deletion_id']) . "'";
		$query = $this->db->query($query);
		
		return true;
	}
	
	public function sendDenyDeletionEmail($deletion_id = 0) {
		$data = $this->getDeletion($deletion_id);

		// Languages
		$this->load->model('localisation/language');
		$language_info = $this->model_localisation_language->getLanguage($data['language_id']);

		if (isset($language_info)) {
			$code = $language_info['code'];
		} else {
			$code = $this->config->get('config_admin_language');
		}

		$language = new Language($code);
		$language->load($code);
		$language_strings = $language->load($this->modulePath);
        foreach ($language_strings as $code => $languageVariable) {
			$this->data[$code] = $languageVariable;
		}
		
		if (empty($data['store_id'])) {
			$data['store_id'] = $this->config->get('config_store_id');
		}
		$store_data = $this->getCurrentStore($data['store_id']);
		
		$this->data['logo'] = $store_data['url'] . 'image/' . $this->config->get('config_logo');
		$this->data['store_name'] = $store_data['name'];
		$this->data['store_url'] = $store_data['url'];
		$this->data['text_main_message'] = str_replace('%s%', $data['email'], $this->data['text_deny_gdpr_request_main']);
		$this->data['text_greeting'] = $this->data['text_deny_message'];
		$this->data['message'] = $data['message'];
		
		$template = '';
		$template_path = $this->modulePath . '/mail/request_deny';

		$template = $this->load->view($template_path, $this->data);

		$email_parameters = array(
			'email' => $data['email'],
			'message' => $template,
			'subject' => $this->data['text_deny_deletion_subject'] . ' - ' . $store_data['name'],
			
		);
		
		return $this->sendMail($email_parameters);
	}
	
	public function sendApproveDeletionEmail($deletion_id = 0) {
		$data = $this->getDeletion($deletion_id);

		// Languages
		$this->load->model('localisation/language');
		$language_info = $this->model_localisation_language->getLanguage($data['language_id']);

		if (isset($language_info)) {
			$code = $language_info['code'];
		} else {
			$code = $this->config->get('config_admin_language');
		}

		$language = new Language($code);
		$language->load($code);
		$language_strings = $language->load($this->modulePath);
        foreach ($language_strings as $code => $languageVariable) {
			$this->data[$code] = $languageVariable;
		}
		
		if (empty($data['store_id'])) {
			$data['store_id'] = $this->config->get('config_store_id');
		}
		$store_data = $this->getCurrentStore($data['store_id']);
		
		$this->data['logo'] = $store_data['url'] . 'image/' . $this->config->get('config_logo');
		$this->data['store_name'] = $store_data['name'];
		$this->data['store_url'] = $store_data['url'];
		$this->data['text_main_message'] = str_replace('%s%', $data['email'], $this->data['text_approve_gdpr_request_main']);
		$this->data['text_greeting'] = $this->data['text_approve_message'];
		$this->data['data'] = $data;
		
		$template = '';
		$template_path = $this->modulePath . '/mail/request_approve';

		$template = $this->load->view($template_path, $this->data);

		$email_parameters = array(
			'email' => $data['email'],
			'message' => $template,
			'subject' => $this->data['text_approve_deletion_subject'] . ' - ' . $store_data['name'],
			
		);
		
		return $this->sendMail($email_parameters);
	}
	
	public function anonymizeData($deletion_id = 0) {	
		$deletion_data = $this->getDeletion($deletion_id);
		$customer_id = $deletion_data['customer_id'];
		$email = $deletion_data['email'];
		
		if ($customer_id != '0') {
			if ($deletion_data['customer_data']) {
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
			}

			if ($deletion_data['address_data']) {
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
			}

			if ($deletion_data['customer_data']) {
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
			}
	
		} // end if (customer_id) != 0 ->> 
		
		if (!empty($email)) {
			if ($customer_id == 0) $customer_id = mt_rand(100000, 999999);
			
			if ($deletion_data['order_data']) {
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
			
			if ($deletion_data['gdpr_data']) {
				$this->db->query("UPDATE `" . DB_PREFIX . "isense_gdpr_requests`
					SET
						`email` = 'gdpr_anonymized_" . $this->db->escape($customer_id) . "@email.com',
						`client_ip` =  '0.0.0.0',
						`user_agent` = '',
						`accept_language` = ''
					WHERE
						`email` = '" . $this->db->escape($email) . "'
				");
				
				$this->db->query("UPDATE `" . DB_PREFIX . "isense_gdpr_policy_acceptances`
					SET
						`email` = 'gdpr_anonymized_" . $this->db->escape($customer_id) . "@email.com',
						`client_ip` =  '0.0.0.0',
						`user_agent` = '',
						`accept_language` = ''
					WHERE
						`email` = '" . $this->db->escape($email) . "'
				");
				
				$this->db->query("UPDATE `" . DB_PREFIX . "isense_gdpr_optins`
					SET
						`email` = 'gdpr_anonymized_" . $this->db->escape($customer_id) . "@email.com',
						`client_ip` =  '0.0.0.0',
						`user_agent` = '',
						`accept_language` = ''
					WHERE
						`email` = '" . $this->db->escape($email) . "'
				");
				
				$this->db->query("UPDATE `" . DB_PREFIX . "isense_gdpr_submissions`
					SET
						`email` = 'gdpr_anonymized_" . $this->db->escape($customer_id) . "@email.com'
					WHERE
						`email` = '" . $this->db->escape($email) . "'
				");
			}
		}
		
		return true;
	
    }
	
	public function sendSuccessfulDeletionEmail($deletion_id = 0) {
		$data = $this->getDeletion($deletion_id);

		// Languages
		$this->load->model('localisation/language');
		$language_info = $this->model_localisation_language->getLanguage($data['language_id']);

		if (isset($language_info)) {
			$code = $language_info['code'];
		} else {
			$code = $this->config->get('config_admin_language');
		}

		$language = new Language($code);
		$language->load($code);
		$language_strings = $language->load($this->modulePath);
        foreach ($language_strings as $codes => $languageVariable) {
			$this->data[$codes] = $languageVariable;
		}
		
		if (empty($data['store_id'])) {
			$data['store_id'] = $this->config->get('config_store_id');
		}
		$store_data = $this->getCurrentStore($data['store_id']);
		
		$this->data['logo'] = $store_data['url'] . 'image/' . $this->config->get('config_logo');
		$this->data['store_name'] = $store_data['name'];
		$this->data['store_url'] = $store_data['url'];
		$this->data['text_main_message'] = str_replace('%s%', $data['email'], $this->data['text_approve_gdpr_request_main']);
		$this->data['text_greeting'] = $this->data['text_deletion_success_message'];
		$this->data['data'] = $data;
		
		$template = '';
		$template_path = $this->modulePath . '/mail/request_complete';

		$template = $this->load->view($template_path, $this->data);

		$email_parameters = array(
			'email' => $data['email'],
			'message' => $template,
			'subject' => $this->data['text_deletion_request_complete_subject'] . ' - ' . $store_data['name'],
			
		);
		
		return $this->sendMail($email_parameters);
	}
    // Deletions - End
	
	/*
	** Global functions
	** Start
	*/
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

	public function deleteGuestAcceptances() {
		$this->db->query("DELETE FROM `".DB_PREFIX."isense_gdpr_policy_acceptances` WHERE email NOT IN (SELECT c.email FROM `" . DB_PREFIX."customer` c)");
		return true;
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
            $storeURL = HTTP_CATALOG;
        } else {
            $storeURL = HTTPS_CATALOG;
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