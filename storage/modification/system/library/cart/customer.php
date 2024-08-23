<?php
/* This file is under Git Control by KDSI. */
namespace Cart;
class Customer {
	private $customer_id;

			 	/* MP Membership Starts */
				private $mpplan_customer_id;
				/* MP Membership Ends */
						
	private $load;
	private $firstname;
	private $lastname;
	private $customer_group_id;
	private $email;
	private $telephone;
	private $newsletter;
	private $address_id;
	private $tax_id;
	private $seller_permit;
	private $approved;
	private $denied;
	private $has_seller_permit_file;


	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->load = $registry->get('load');
		$this->session = $registry->get('session');

		if (isset($this->session->data['customer_id'])) {
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "' AND status = '1'");

			if ($customer_query->num_rows) {

				/* MP Membership Starts */
				if($this->config->get('mpplan_status')) {
					$today = date('Y-m-d H:i:s');
					$total_mpplan = $this->db->query("SELECT COUNT(*) as total FROM " . DB_PREFIX . "mpplan_customer WHERE customer_id = '". (int)$customer_query->row['customer_id'] ."'");
					$mpplan_info = $this->db->query("SELECT mp.* FROM " . DB_PREFIX . "mpplan_customer mp LEFT JOIN ". DB_PREFIX ."order o ON (mp.order_id = o.order_id) WHERE mp.customer_id = '". (int)$customer_query->row['customer_id'] ."' AND o.order_status_id = '". (int)$this->config->get('mpplan_status_id') ."' AND '" . $this->db->escape($today) . "' BETWEEN mp.start_date and mp.end_date AND active = '1' LIMIT 0,1")->row;
					if(empty($mpplan_info) && $total_mpplan->row['total']) {
						$this->db->query("UPDATE " . DB_PREFIX . "mpplan_customer SET active = '0' WHERE customer_id = '". (int)$customer_query->row['customer_id'] ."'");

						$hasactive_plan = $this->db->query("SELECT mp.* FROM " . DB_PREFIX . "mpplan_customer mp LEFT JOIN ". DB_PREFIX ."order o ON (mp.order_id = o.order_id) WHERE mp.customer_id = '". (int)$customer_query->row['customer_id'] ."' AND o.order_status_id = '". (int)$this->config->get('mpplan_status_id') ."' AND '" . $this->db->escape($today) . "' BETWEEN mp.start_date and mp.end_date ORDER BY mp.mpplan_customer_id DESC LIMIT 0,1")->row;

						if($hasactive_plan) {
							$this->db->query("UPDATE " . DB_PREFIX . "mpplan_customer SET active = '1' WHERE customer_id = '". (int)$customer_query->row['customer_id'] ."' AND mpplan_customer_id = '". (int)$hasactive_plan['mpplan_customer_id'] ."'");

							$this->mpplan_customer_id = $hasactive_plan['mpplan_customer_id'];
						}
					} else if(!empty($mpplan_info)) {
						$this->mpplan_customer_id = $mpplan_info['mpplan_customer_id'];
					}
				}
				/* MP Membership Ends */
			
				$this->customer_id = $customer_query->row['customer_id'];
				$this->firstname = $customer_query->row['firstname'];
				$this->lastname = $customer_query->row['lastname'];
				$this->customer_group_id = $customer_query->row['customer_group_id'];
				$this->email = $customer_query->row['email'];
				$this->telephone = $customer_query->row['telephone'];
				$this->newsletter = $customer_query->row['newsletter'];
				$this->address_id = $customer_query->row['address_id'];
				$this->tax_id = $customer_query->row['tax_id'];
				$this->seller_permit = $customer_query->row['seller_permit'];
				$this->approved = $customer_query->row['approved'];
				$this->denied = $customer_query->row['denied'];
				$this->has_seller_permit_file = ($customer_query->row['seller_permit_file'] == "") ? "0" : "1";


				// Customer IP Changed?
                        if (isset($this->request->get['route']) && !in_array($this->request->get['route'], array('common/login', 'common/logout')) && (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest')) {
                            $user = new User($registry);

                            if (!$user->isLogged()) {
                                $customer_ip_query = $this->db->query("(SELECT ip FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$this->customer_id . "' AND ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "') UNION (SELECT ip FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$this->customer_id . "' AND ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "')");

                                if (!$customer_ip_query->num_rows) {
                                    $this->load->controller('extension/module/emailtemplate_security/customerIpChanged', array($this->customer_id));
                                }
                            }
                        }
            
				$this->db->query("UPDATE " . DB_PREFIX . "customer SET language_id = '" . (int)$this->config->get('config_language_id') . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE customer_id = '" . (int)$this->customer_id . "'");

				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "' AND ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'");

				if (!$query->num_rows) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "customer_ip SET customer_id = '" . (int)$this->session->data['customer_id'] . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', date_added = NOW()");
				}
			} else {
				$this->logout();
			}
		}
	}

  public function login($email, $password, $override = false) {
		if ($override) {
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "' AND status = '1'");
		} else {
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($password) . "'))))) OR password = '" . $this->db->escape(md5($password)) . "') AND status = '1'");
		}

		if ($customer_query->num_rows) {
			$this->session->data['customer_id'] = $customer_query->row['customer_id'];


				/* MP Membership Starts */
				if($this->config->get('mpplan_status')) {
					$today = date('Y-m-d H:i:s');
					$total_mpplan = $this->db->query("SELECT COUNT(*) as total FROM " . DB_PREFIX . "mpplan_customer WHERE customer_id = '". (int)$customer_query->row['customer_id'] ."'");
					$mpplan_info = $this->db->query("SELECT mp.* FROM " . DB_PREFIX . "mpplan_customer mp LEFT JOIN ". DB_PREFIX ."order o ON (mp.order_id = o.order_id) WHERE mp.customer_id = '". (int)$customer_query->row['customer_id'] ."' AND o.order_status_id = '". (int)$this->config->get('mpplan_status_id') ."' AND '" . $this->db->escape($today) . "' BETWEEN mp.start_date and mp.end_date AND active = '1' LIMIT 0,1")->row;
					if(empty($mpplan_info) && $total_mpplan->row['total']) {
						$this->db->query("UPDATE " . DB_PREFIX . "mpplan_customer SET active = '0' WHERE customer_id = '". (int)$customer_query->row['customer_id'] ."'");

						$hasactive_plan = $this->db->query("SELECT mp.* FROM " . DB_PREFIX . "mpplan_customer mp LEFT JOIN ". DB_PREFIX ."order o ON (mp.order_id = o.order_id) WHERE mp.customer_id = '". (int)$customer_query->row['customer_id'] ."' AND o.order_status_id = '". (int)$this->config->get('mpplan_status_id') ."' AND '" . $this->db->escape($today) . "' BETWEEN mp.start_date and mp.end_date ORDER BY mp.mpplan_customer_id DESC LIMIT 0,1")->row;

						if($hasactive_plan) {
							$this->db->query("UPDATE " . DB_PREFIX . "mpplan_customer SET active = '1' WHERE customer_id = '". (int)$customer_query->row['customer_id'] ."' AND mpplan_customer_id = '". (int)$hasactive_plan['mpplan_customer_id'] ."'");

							$this->mpplan_customer_id = $hasactive_plan['mpplan_customer_id'];
						}
					} else if(!empty($mpplan_info)) {
						$this->mpplan_customer_id = $mpplan_info['mpplan_customer_id'];
					}
				}
				/* MP Membership Ends */
			
			$this->customer_id = $customer_query->row['customer_id'];
			$this->firstname = $customer_query->row['firstname'];
			$this->lastname = $customer_query->row['lastname'];
			$this->customer_group_id = $customer_query->row['customer_group_id'];
			$this->email = $customer_query->row['email'];
			$this->telephone = $customer_query->row['telephone'];
			$this->newsletter = $customer_query->row['newsletter'];
			$this->address_id = $customer_query->row['address_id'];
				$this->tax_id = $customer_query->row['tax_id'];
				$this->seller_permit = $customer_query->row['seller_permit'];
				$this->approved = $customer_query->row['approved'];
				$this->denied = $customer_query->row['denied'];
				$this->has_seller_permit_file = ($customer_query->row['seller_permit_file'] == "") ? "0" : "1";

		
			// Customer IP Changed?
			if (!$override) {
                $customer_ip_query = $this->db->query("(SELECT ip FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$this->customer_id . "' AND ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "') UNION (SELECT ip FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$this->customer_id . "' AND ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "')");

                if (!$customer_ip_query->num_rows) {
                    $this->load->controller('extension/module/emailtemplate_security/customerIpChanged', array($this->customer_id));
                }
            }
            
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET language_id = '" . (int)$this->config->get('config_language_id') . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE customer_id = '" . (int)$this->customer_id . "'");

			return true;
		} else {
			return false;
		}
	}

	public function logout() {
		unset($this->session->data['customer_id']);

				/* MP Membership Starts */
				$this->mpplan_customer_id = '';
				/* MP Membership Ends */
					

		$this->customer_id = '';
		$this->firstname = '';
		$this->lastname = '';
		$this->customer_group_id = '';
		$this->email = '';
		$this->telephone = '';
		$this->newsletter = '';
		$this->address_id = '';
		$this->tax_id = '';
		$this->seller_permit = '';
		$this->approved = '';
		$this->denied = '';
		$this->has_seller_permit_file = '';
	}

	public function isLogged() {
		return $this->customer_id;
	}

	public function getId() {
		return $this->customer_id;
	}


			/* MP Membership Starts */
			public function getActivePlan() {
				return $this->mpplan_customer_id;
			}
			/* MP Membership Ends */
			
	public function getFirstName() {
		return $this->firstname;
	}

	public function getLastName() {
		return $this->lastname;
	}

	public function getGroupId() {
		return $this->customer_group_id;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getTelephone() {
		return $this->telephone;
	}

	public function getNewsletter() {
		return $this->newsletter;
	}

	public function getAddressId() {
		return $this->address_id;
	}


	public function getTaxId() {
		return $this->tax_id;
	}

	public function getSellerPermit() {
		return $this->seller_permit;
	}

	public function getApproved() {
		return $this->approved;
	}

	public function getDenied() {
		return $this->denied;
	}

	public function hasSellerPermitFile() {
		return $this->has_seller_permit_file;
	}
		
	public function getBalance() {
		$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$this->customer_id . "'");

		return $query->row['total'];
	}

	public function getRewardPoints() {
		$query = $this->db->query("SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$this->customer_id . "'");

		return $query->row['total'];
	}
}
