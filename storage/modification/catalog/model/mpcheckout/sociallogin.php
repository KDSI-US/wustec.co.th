<?php
/* This file is under Git Control by KDSI. */
class ModelMpcheckoutSociallogin extends Model {
	public function getSocialCustomer($email) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}	

	public function addSocialCustomer($data) {
		if ($this->config->get('fblogin_customer_group')) {
			$customer_group_id = $this->config->get('fblogin_customer_group');
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$customer_data = array(
			'customer_group_id'		=> $customer_group_id,
			'firstname'				=> isset($data['firstname']) ? $data['firstname'] : '',
			'lastname'				=> isset($data['lastname']) ? $data['lastname'] : '',
			'email'					=> isset($data['email']) ? $data['email'] : '',
			'telephone'				=> isset($data['telephone']) ? $data['telephone'] : '',
			'fax'					=> isset($data['fax']) ? $data['fax'] : '',
			'company'				=> isset($data['company']) ? $data['company'] : '',
			'address_1'				=> isset($data['address_1']) ? $data['address_1'] : '',
			'address_2'				=> isset($data['address_2']) ? $data['address_2'] : '',
			'city'					=> isset($data['city']) ? $data['city'] : '',
			'postcode'				=> isset($data['postcode']) ? $data['postcode'] : '',
			'country_id'			=> isset($data['country_id']) ? $data['country_id'] : '',
			'zone_id'				=> isset($data['zone_id']) ? $data['zone_id'] : '',
			'password'				=> '123456xxxxx',
		);

		// Add Default Customer
		$this->load->model('account/customer');
		$customer_id = $this->model_account_customer->addCustomer($customer_data);

		return $customer_id;
	}

	public function editToken($customer_id, $token) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET token = '" . $this->db->escape($token) . "' WHERE customer_id = '" . (int)$customer_id . "'");
	}
	/*new updates 28032018 starts*/
	public function getMpSocialLoginFbUser($id, $username) {
		$query = $this->db->query("SELECT * FROM ". DB_PREFIX ."mpsociallogin WHERE id='". $this->db->escape($id) ."' AND username='". $this->db->escape($username) ."' AND type='FB'");
		return $query->row;
	}

	public function addMpSocialLoginFbUser($id, $username) {
		$this->db->query("INSERT INTO ". DB_PREFIX ."mpsociallogin SET id='". $this->db->escape($id) ."', username='". $this->db->escape($username) ."', type='FB', date_added=NOW()");
	}

	public function addCustomerIdToFbUser($id, $customer_id) {
		$this->db->query("UPDATE ". DB_PREFIX ."mpsociallogin SET customer_id='". (int)$customer_id ."', date_modified=NOW() WHERE id='". $this->db->escape($id) ."' AND type='FB'");
	}
	public function getMpSocialLoginInstaUser($id, $username) {
		$query = $this->db->query("SELECT * FROM ". DB_PREFIX ."mpsociallogin WHERE id='". $this->db->escape($id) ."' AND username='". $this->db->escape($username) ."' AND type='INSTAGRAM'");
		return $query->row;
	}

	public function addMpSocialLoginInstaUser($id, $username) {
		$this->db->query("INSERT INTO ". DB_PREFIX ."mpsociallogin SET id='". $this->db->escape($id) ."', username='". $this->db->escape($username) ."', type='INSTAGRAM', date_added=NOW()");
	}

	public function addCustomerIdToInstaUser($id, $customer_id) {
		$this->db->query("UPDATE ". DB_PREFIX ."mpsociallogin SET customer_id='". (int)$customer_id ."', date_modified=NOW() WHERE id='". $this->db->escape($id) ."' AND type='INSTAGRAM'");
	}

	public function getMpSocialLoginTwitterUser($id, $username) {
		$query = $this->db->query("SELECT * FROM ". DB_PREFIX ."mpsociallogin WHERE id='". $this->db->escape($id) ."' AND username='". $this->db->escape($username) ."' AND type='TWITTER'");
		return $query->row;
	}

	public function addMpSocialLoginTwitterUser($id, $username) {
		$this->db->query("INSERT INTO ". DB_PREFIX ."mpsociallogin SET id='". $this->db->escape($id) ."', username='". $this->db->escape($username) ."', type='TWITTER', date_added=NOW()");
	}

	public function addCustomerIdToTwitterUser($id, $customer_id) {
		$this->db->query("UPDATE ". DB_PREFIX ."mpsociallogin SET customer_id='". (int)$customer_id ."', date_modified=NOW() WHERE id='". $this->db->escape($id) ."' AND type='TWITTER'");
	}


	public function getCustomerByCustomerId($customer_id) {
		$query = $this->db->query("SELECT * FROM ". DB_PREFIX ."customer WHERE customer_id='". (int)$customer_id ."' ");
		return $query->row;
	}

	public function addCustomerInfoByCustomerId($customer_id, $data) {

		$implode = array();

		if(!empty($data['email'])) {
			$implode[] = "email='". $this->db->escape($data['email']) ."'";
		}
		
		if(!empty($data['firstname'])) {
			$implode[] = "firstname='". $this->db->escape($data['firstname']) ."'";
		}
		if(!empty($data['lastname'])) {
			$implode[] = "lastname='". $this->db->escape($data['lastname']) ."'";
		}
		if(!empty($data['telephone'])) {
			$implode[] = "telephone='". $this->db->escape($data['telephone']) ."'";
		}
		if(!empty($data['fax'])) {
			$implode[] = "fax='". $this->db->escape($data['fax']) ."'";
		}

		if($implode) {
			$sql = "UPDATE ". DB_PREFIX ."customer SET ";
			$sql .= implode(", ", $implode);
			$sql .= " WHERE customer_id='". (int)$customer_id ."'";

			$this->db->query($sql);
			return true;
		}
		return false;
	}
	/*new updates 28032018 ends*/
}