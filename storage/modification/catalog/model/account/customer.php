<?php
/* This file is under Git Control by KDSI. */
class ModelAccountCustomer extends Model {

	public function editPasswordById($customer_id, $password) 
	{
		$strSql = "UPDATE `" . DB_PREFIX . "customer` 
			SET `password` = '" . $this->db->escape(md5($password)) . "' 
			WHERE `customer_id` = '" . (int)$customer_id . "' ";
		$this->db->query($strSql);
	}

	public function editCustomerById($customer_id, $data) 
	{
		$strSql = "UPDATE `" . DB_PREFIX . "customer` 
			SET 
				`firstname` = '" . $this->db->escape($data['firstname']) . "', 
				`lastname` = '" . $this->db->escape($data['lastname']) . "', 
				`email` = '" . $this->db->escape($data['email']) . "', 
				`telephone` = '" . $this->db->escape($data['telephone']) . "', 
				`fax` = '" . $this->db->escape($data['fax']) . "' 
			WHERE `customer_id` = '" . (int)$customer_id . "' ";
		$this->db->query($strSql);
	}

	public function getCustomersMod($data = array()) 
	{
		$sql = "SELECT 
				*, 
				CONCAT(c.firstname, ' ', c.lastname) AS name, 
				cgd.name AS customer_group 
			FROM `" . DB_PREFIX . "customer` AS c 
			LEFT JOIN `" . DB_PREFIX . "customer_group_description` AS cgd 
				ON (c.customer_group_id = cgd.customer_group_id) 
			WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "' ";
			
		$implode = array();

		if (!empty($data['filter_name'])) 
		{
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%' ";
		}
		if (!empty($data['filter_email'])) 
		{
			$implode[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%' ";
		}
		if (isset($data['filter_newsletter']) && !is_null($data['filter_newsletter'])) 
		{
			$implode[] = "c.newsletter = '" . (int)$data['filter_newsletter'] . "' ";
		}
		if (!empty($data['filter_customer_group_id'])) 
		{
			$implode[] = "c.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "' ";
		}
		if (!empty($data['filter_ip'])) 
		{
			$implode[] = "c.customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "') ";
		}
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) 
		{
			$implode[] = "c.status = '" . (int)$data['filter_status'] . "' ";
		}
		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) 
		{
			$implode[] = "c.approved = '" . (int)$data['filter_approved'] . "' ";
		}
		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "') ";
		}
		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'name',
			'c.email',
			'customer_group',
			'c.status',
			'c.approved',
			'c.ip',
			'c.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) 
		{
			$sql .= " ORDER BY " . $data['sort'] . " ";
		} 
		else 
		{
			$sql .= " ORDER BY name ";
		}
		if (isset($data['order'])) $sql .= $data['order'] . " ";
		if (isset($data['start']) || isset($data['limit'])) 
		{
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
			$sql .= "LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		$query = $this->db->query($sql);

		return $query->rows;
	}

    public function clearTokens($token, $sessionid) 
	{
        if(!empty($token)) 
		{
			$strSql = "
				DELETE FROM `oauth_access_tokens` 
				WHERE 
					`session_id` = '" . $this->db->escape($sessionid) . "' 
					AND `access_token` != '" . $this->db->escape($token) . "' ";
            $this->db->query($strSql);
        }
		$strSql = "
			DELETE FROM `oauth_access_tokens` 
			WHERE `expires` < '" . date('Y-m-d', strtotime("-30 days")) . "' ";
        $this->db->query($strSql);
    }

	public function loginCustomerById($customer_id) 
	{
		$strSql = "
			SELECT `email` 
			FROM `" . DB_PREFIX . "customer` 
			WHERE `customer_id` = '" . (int)$customer_id . "' ";
		$query = $this->db->query($strSql);
		return $query->row;
	}

	public function updateCustomerData($session, $customer_id) 
	{
		$cart = isset($session->data['cart']) ? json_encode($session->data['cart']) : '';
		$wishlist = isset($session->data['wishlist']) ? json_encode($session->data['wishlist']) : '';
		$strSql = "UPDATE `" . DB_PREFIX . "customer` 
			SET 
				`cart` = '" . $cart . "', 
				`wishlist` = '" . $wishlist . "' 
			WHERE `customer_id` = '" . (int)$customer_id . "' ";
		$this->db->query($strSql);
	}

	public function updateSession($session, $access_token) 
	{
		$strSql = "Update `oauth_access_tokens` 
			SET 
				`data` = '" . $this->db->escape(json_encode($session)) . "', 
				`expires` = 'expires' 
			WHERE `access_token` = '" . $access_token . "' ";
		$query = $this->db->query($strSql);
	}

	public function loadOldToken($access_token) 
	{
		$strSql = "SELECT * 
			FROM `oauth_access_tokens` 
			WHERE `access_token` = '" . $this->db->escape($access_token) . "' ";
		$query = $this->db->query($strSql);
		return $query->row;
	}

	public function deleteOldToken($access_token) 
	{
		$strSql = "DELETE FROM `oauth_access_tokens` 
			WHERE `access_token` = '" . $this->db->escape($access_token) . "' ";
		$this->db->query($strSql);
	}

	public function loadSessionToNew($session, $access_token) 
	{
		$strSql = "Update `oauth_access_tokens` 
			SET 
				`data` = '" . $this->db->escape($session) . "', 
				`expires` = 'expires' 
			WHERE `access_token` = '" . $this->db->escape($access_token) . "' ";
		$query = $this->db->query($strSql);
	}
			
	public function addCustomer($data) {
		if (isset($data['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($data['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $data['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$this->load->model('account/customer_group');

		$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);

				$strQry = "INSERT INTO `" . DB_PREFIX . "customer` 
			SET 
				`customer_group_id` = '" . (int)$customer_group_id . "', 
				`store_id` = '" . (int)$this->config->get('config_store_id') . "', 
				`language_id` = '" . (int)$this->config->get('config_language_id') . "', 
				`firstname` = '" . $this->db->escape($data['firstname']) . "', 
				`lastname` = '" . $this->db->escape($data['lastname']) . "', 
				`email` = '" . $this->db->escape($data['email']) . "', 
				`telephone` = '" . $this->db->escape($data['telephone']) . "', 
				`custom_field` = '" . $this->db->escape(isset($data['custom_field']['account']) ? json_encode($data['custom_field']['account']) : '') . "', 
				`salt` = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', 
				`newsletter` = '" . (isset($data['newsletter']) ? (int)$data['newsletter'] : 0) . "', 
				`ip` = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', 
				`status` = '" . (int)!$customer_group_info['approval'] . "', 
				`date_added` = NOW(), 
				`tax_id` = '" . $this->db->escape($data['tax_id']) . "',
				`seller_permit` = '" . $this->db->escape($data['seller_permit']) . "',
				`approved` = 0;";
		$this->db->query($strQry);

		$customer_id = $this->db->getLastId();

		$file = DIR_SYSTEM . 'library/gdpr.php';
		if (is_file($file)) {
			$privacy_policy_id = $this->config->get('config_account_id');
			$newsletter_check = (isset($data['newsletter']) && !empty($data['newsletter'])) ? (int)$data['newsletter'] : 0;
			$this->config->load('isenselabs/isenselabs_gdpr');
			$gdpr_name = $this->config->get('isenselabs_gdpr_name');

			$this->load->model('setting/setting');
			$moduleSettings = $this->model_setting_setting->getSetting($gdpr_name, $this->config->get('config_store_id'));
			$gdpr_data = !empty($moduleSettings[$gdpr_name]) ? $moduleSettings[$gdpr_name] : array();

			if (!empty($gdpr_data['NewsletterDoubleOptIn']) && ($gdpr_data['NewsletterDoubleOptIn'] == '1') && !empty($data['email']) && $newsletter_check) {
			  $gdpr_path = $this->config->get('isenselabs_gdpr_path');
			  $gdpr_model = $this->config->get('isenselabs_gdpr_model');
			  $this->load->model($gdpr_path);
			  $gdpr_data = array(
				  'store_id' => $this->config->get('config_store_id'),
				  'email' => $data['email'],
				  'customer_id' => $customer_id
			  );
			  $email_message = $this->{$gdpr_model}->sendNewsletterOptinEmail($gdpr_data);
			  $customer_newsletter = $this->db->query("UPDATE `" . DB_PREFIX . "customer` SET `newsletter` = '0' WHERE `customer_id` = '" . $this->db->escape($customer_id) . "'");
			} else if (!empty($gdpr_data['NewsletterOptIn']) && ($gdpr_data['NewsletterOptIn'] == '1') && $privacy_policy_id && !empty($data['email']) && $newsletter_check) {
			  $this->load->library('gdpr');
			  $this->gdpr->newOptin($privacy_policy_id, $data['email'], 'newsletter', 'opt-in');
			}
        }
        

        $file = DIR_SYSTEM . 'library/gdpr.php';
		if (is_file($file)) {
			if ($this->config->get('config_account_id')) {
				$this->load->library('gdpr');
				$this->gdpr->newAcceptanceRequest($this->config->get('config_account_id'), $data['email']);
			}
		}
        

		// trigger event eventAddCustomer
		$this->load->controller('extension/module/emailtemplate_newsletter/eventAddCustomer', $this->getCustomer($customer_id));
        

		if (1) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_approval` SET customer_id = '" . (int)$customer_id . "', type = 'customer', date_added = NOW()");
		}
		
		return $customer_id;
	}

	public function editCustomer($customer_id, $data) {

		if(isset($data['profile_picture'])) {
			$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "customer` WHERE `Field` = 'profile_picture'");
			if(!$query->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` ADD `profile_picture` TEXT NOT NULL AFTER `store_id`");
			}
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET profile_picture = '" . $this->db->escape($data['profile_picture']) . "' WHERE customer_id = '" . (int)$customer_id . "'");
		}
			
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', tax_id = '" . $this->db->escape($data['tax_id']) . "', seller_permit = '" . $this->db->escape($data['seller_permit']) . "', seller_permit_file = '" . $this->db->escape($data['seller_permit_file']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']['account']) ? json_encode($data['custom_field']['account']) : '') . "' WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function editPassword($email, $password) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($password)))) . "', code = '' WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
	}

	public function editAddressId($customer_id, $address_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
	}
	
	public function editCode($email, $code) {
		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET code = '" . $this->db->escape($code) . "' WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
	}

	public function editNewsletter($newsletter) {

		$file = DIR_SYSTEM . 'library/gdpr.php';
		if (is_file($file)) {
			$privacy_policy_id = $this->config->get('config_account_id');
			$customer_email = $this->customer->getEmail();
			$customer_id = $this->customer->getId();
			$this->config->load('isenselabs/isenselabs_gdpr');
			$gdpr_name = $this->config->get('isenselabs_gdpr_name');

			$this->load->model('setting/setting');
			$moduleSettings = $this->model_setting_setting->getSetting($gdpr_name, $this->config->get('config_store_id'));
			$gdpr_data = !empty($moduleSettings[$gdpr_name]) ? $moduleSettings[$gdpr_name] : array();

			if (!empty($gdpr_data['NewsletterDoubleOptIn']) && ($gdpr_data['NewsletterDoubleOptIn'] == '1') && !empty($customer_email) && isset($newsletter) && $newsletter == '1') {
			  $gdpr_path = $this->config->get('isenselabs_gdpr_path');
			  $gdpr_model = $this->config->get('isenselabs_gdpr_model');
			  $this->load->model($gdpr_path);
			  $gdpr_data = array(
				  'store_id' => $this->config->get('config_store_id'),
				  'email' => $customer_email,
				  'customer_id' => $customer_id
			  );
			  $email_message = $this->{$gdpr_model}->sendNewsletterOptinEmail($gdpr_data);
			  return true;
			}

			if (!empty($gdpr_data['NewsletterOptIn']) && ($gdpr_data['NewsletterOptIn'] == '1') && $privacy_policy_id && !empty($customer_email) && isset($newsletter)) {
			  $this->load->library('gdpr');
			  $this->gdpr->newOptin($privacy_policy_id, $customer_email, 'newsletter', $newsletter ? 'opt-in' : 'opt-out');
			}
        }
        
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET newsletter = '" . (int)$newsletter . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	}


	public function editSellerPermitFile($seller_permit_file) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET seller_permit_file = '" . $seller_permit_file . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	}

	public function editProfilePicture($profile_picture) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET profile_picture = '" . $profile_picture . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	}

	public function editFirstname($firstname) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET firstname = '" . $firstname . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	}

	public function editLastname($lastname) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET lastname = '" . $lastname . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	}

	public function editTelephone($telephone) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET telephone = '" . $telephone . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	}

	public function editTaxID($tax_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET tax_id = '" . $tax_id . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	}

	public function editSellerPermit($seller_permit) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET seller_permit = '" . $seller_permit . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	}
	
	public function getCustomer($customer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row;
	}

	public function getCustomerByEmail($email) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}

	public function getCustomerByCode($code) {
		$query = $this->db->query("SELECT customer_id, firstname, lastname, email FROM `" . DB_PREFIX . "customer` WHERE code = '" . $this->db->escape($code) . "' AND code != ''");

		return $query->row;
	}

	public function getCustomerByToken($token) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE token = '" . $this->db->escape($token) . "' AND token != ''");

		$this->db->query("UPDATE " . DB_PREFIX . "customer SET token = ''");

		return $query->row;
	}
	
	public function getTotalCustomersByEmail($email) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row['total'];
	}

	public function addTransaction($customer_id, $description, $amount = '', $order_id = 0) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET customer_id = '" . (int)$customer_id . "', order_id = '" . (float)$order_id . "', description = '" . $this->db->escape($description) . "', amount = '" . (float)$amount . "', date_added = NOW()");
	}

	public function deleteTransactionByOrderId($order_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");
	}

	public function getTransactionTotal($customer_id) {
		$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}
	
	public function getTotalTransactionsByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}
	
	public function getRewardTotal($customer_id) {
		$query = $this->db->query("SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getIps($customer_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_ip` WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->rows;
	}

	public function addLoginAttempt($email) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_login WHERE email = '" . $this->db->escape(utf8_strtolower((string)$email)) . "'");

		if (!$query->num_rows) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_login SET email = '" . $this->db->escape(utf8_strtolower((string)$email)) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', total = 1, date_added = '" . $this->db->escape(date('Y-m-d H:i:s')) . "', date_modified = '" . $this->db->escape(date('Y-m-d H:i:s')) . "'");
		} else {
			$this->db->query("UPDATE " . DB_PREFIX . "customer_login SET total = (total + 1), date_modified = '" . $this->db->escape(date('Y-m-d H:i:s')) . "' WHERE customer_login_id = '" . (int)$query->row['customer_login_id'] . "'");
		}
	}

	public function getLoginAttempts($email) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_login` WHERE email = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}

	public function deleteLoginAttempts($email) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_login` WHERE email = '" . $this->db->escape(utf8_strtolower($email)) . "'");
	}
	
	public function addAffiliate($customer_id, $data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "customer_affiliate SET `customer_id` = '" . (int)$customer_id . "', `company` = '" . $this->db->escape($data['company']) . "', `website` = '" . $this->db->escape($data['website']) . "', `tracking` = '" . $this->db->escape(token(64)) . "', `commission` = '" . (float)$this->config->get('config_affiliate_commission') . "', `tax` = '" . $this->db->escape($data['tax']) . "', `payment` = '" . $this->db->escape($data['payment']) . "', `cheque` = '" . $this->db->escape($data['cheque']) . "', `paypal` = '" . $this->db->escape($data['paypal']) . "', `bank_name` = '" . $this->db->escape($data['bank_name']) . "', `bank_branch_number` = '" . $this->db->escape($data['bank_branch_number']) . "', `bank_swift_code` = '" . $this->db->escape($data['bank_swift_code']) . "', `bank_account_name` = '" . $this->db->escape($data['bank_account_name']) . "', `bank_account_number` = '" . $this->db->escape($data['bank_account_number']) . "', `status` = '" . (int)!$this->config->get('config_affiliate_approval') . "'");
		
		if ($this->config->get('config_affiliate_approval')) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_approval` SET customer_id = '" . (int)$customer_id . "', type = 'affiliate', date_added = NOW()");
		}		
	}
		
	public function editAffiliate($customer_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer_affiliate SET `company` = '" . $this->db->escape($data['company']) . "', `website` = '" . $this->db->escape($data['website']) . "', `commission` = '" . (float)$this->config->get('config_affiliate_commission') . "', `tax` = '" . $this->db->escape($data['tax']) . "', `payment` = '" . $this->db->escape($data['payment']) . "', `cheque` = '" . $this->db->escape($data['cheque']) . "', `paypal` = '" . $this->db->escape($data['paypal']) . "', `bank_name` = '" . $this->db->escape($data['bank_name']) . "', `bank_branch_number` = '" . $this->db->escape($data['bank_branch_number']) . "', `bank_swift_code` = '" . $this->db->escape($data['bank_swift_code']) . "', `bank_account_name` = '" . $this->db->escape($data['bank_account_name']) . "', `bank_account_number` = '" . $this->db->escape($data['bank_account_number']) . "' WHERE `customer_id` = '" . (int)$customer_id . "'");
	}
	
	public function getAffiliate($customer_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_affiliate` WHERE `customer_id` = '" . (int)$customer_id . "'");

		return $query->row;
	}
	
	public function getAffiliateByTracking($tracking) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_affiliate` WHERE `tracking` = '" . $this->db->escape($tracking) . "'");

		return $query->row;
	}			
}