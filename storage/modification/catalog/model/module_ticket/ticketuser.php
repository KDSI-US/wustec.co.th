<?php
/* This file is under Git Control by KDSI. */
class ModelModuleTicketTicketuser extends Model {
	public function ForceRegistered($customer_id) {
		$customer_info = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '". (int)$customer_id ."'")->row;

		if($customer_info) {
			$ticketuser_info = $this->getTicketuserByEmail($customer_info['email']);
			if($ticketuser_info) {
				$ticketuser_id = $ticketuser_info['ticketuser_id'];
			} else {
				$this->db->query("INSERT INTO " . DB_PREFIX . "ticketuser SET store_id = '" . (int)$this->config->get('config_store_id') . "', language_id = '" . (int)$this->config->get('config_language_id') . "', name = '" . $this->db->escape($customer_info['firstname'] .' '. $customer_info['lastname']) . "', email = '" . $this->db->escape($customer_info['email']) . "', salt = '" . $this->db->escape($customer_info['salt']) . "', password = '" . $this->db->escape($customer_info['password']) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', status = '1', date_added = NOW()");

				$ticketuser_id = $this->db->getLastId();

				$this->db->query("UPDATE " . DB_PREFIX . "ticketrequest SET ticketuser_id = '". (int)$ticketuser_id ."' WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($customer_info['email'])) . "'");

				$ticketuser_requests = $this->db->query("SELECT ticketrequest_id FROM " . DB_PREFIX . "ticketrequest WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($customer_info['email'])) . "'")->rows;

				foreach ($ticketuser_requests as $ticketuser_request) {
					$this->db->query("UPDATE " . DB_PREFIX . "ticketrequest_chat SET ticketuser_id = '". (int)$ticketuser_id ."' WHERE ticketrequest_id = '" . (int)$ticketuser_request['ticketrequest_id'] . "'");
				}
			}

			return $ticketuser_id;
		}
	}

	public function addTicketUser($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "ticketuser SET store_id = '" . (int)$this->config->get('config_store_id') . "', language_id = '" . (int)$this->config->get('config_language_id') . "', name = '" . $this->db->escape($data['name']) . "', email = '" . $this->db->escape($data['email']) . "', salt = '" . $this->db->escape($salt = $this->token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', status = '1', date_added = NOW()");

		$ticketuser_id = $this->db->getLastId();

		$this->db->query("UPDATE " . DB_PREFIX . "ticketrequest SET ticketuser_id = '". (int)$ticketuser_id ."' WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($data['email'])) . "'");

		$ticketuser_requests = $this->db->query("SELECT ticketrequest_id FROM " . DB_PREFIX . "ticketrequest WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($data['email'])) . "'")->rows;

		foreach ($ticketuser_requests as $ticketuser_request) {
			$this->db->query("UPDATE " . DB_PREFIX . "ticketrequest_chat SET ticketuser_id = '". (int)$ticketuser_id ."' WHERE ticketrequest_id = '" . (int)$ticketuser_request['ticketrequest_id'] . "'");
		}


		$this->load->language('support/mail_user');
		$ticketsetting_email = (array)$this->config->get('ticketsetting_email');

		// Send Email To User when create a user
		if(!empty($ticketsetting_email['createusertouser']['status'])) {
			$subject = sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$message = sprintf($this->language->get('text_welcome'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8')) . "\n\n";

			$message .= $this->language->get('text_login') . "\n";

			$message .= str_replace('&amp;', '&', $this->url->link('support/support', 'login=1', true)) . "\n\n";
			$message .= $this->language->get('text_services') . "\n\n";
			$message .= $this->language->get('text_thanks') . "\n";
			$message .= html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');

			if(VERSION >= '3.0.0.0') {
				$mail = new Mail($this->config->get('config_mail_engine'));
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
			} else if(VERSION <= '2.0.1.1') {
		     	$mail = new Mail($this->config->get('config_mail'));
		    } else {
				$mail = new Mail();
				$mail->protocol = $this->config->get('config_mail_protocol');
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
			}

			$admin_email = $this->config->get('config_email');

			if ((utf8_strlen($this->config->get('ticketsetting_adminemail')) <= 96) && filter_var($this->config->get('ticketsetting_adminemail'), FILTER_VALIDATE_EMAIL)) {
				$admin_email = $this->config->get('ticketsetting_adminemail');
			}

			$mail->setTo($data['email']);
			$mail->setReplyTo($admin_email);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject($subject);
			$mail->setText($message);
			$mail->send();
		}

		// Send Email To Admin when create a user
		if(!empty($ticketsetting_email['createusertoadmin']['status'])) {
			$message  = $this->language->get('text_signup') . "\n\n";html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8') . "\n";
			$message .= $this->language->get('text_name') . ' ' . $data['name'] . "\n";
			$message .= $this->language->get('text_email') . ' '  .  $data['email'] . "\n";

			if(VERSION >= '3.0.0.0') {
				$mail = new Mail($this->config->get('config_mail_engine'));
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
			} else if(VERSION <= '2.0.1.1') {
		     	$mail = new Mail($this->config->get('config_mail'));
		    } else {
				$mail = new Mail();
				$mail->protocol = $this->config->get('config_mail_protocol');
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
			}

			$admin_email = $this->config->get('config_email');

			if ((utf8_strlen($this->config->get('ticketsetting_adminemail')) <= 96) && filter_var($this->config->get('ticketsetting_adminemail'), FILTER_VALIDATE_EMAIL)) {
				$admin_email = $this->config->get('ticketsetting_adminemail');
			}

			$mail->setTo($admin_email);
			$mail->setReplyTo($data['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode($this->language->get('text_new_user'), ENT_QUOTES, 'UTF-8'));
			$mail->setText($message);
			$mail->send();
		}
	}

	public function getTicketUser($ticketuser_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ticketuser WHERE ticketuser_id = '" . (int)$ticketuser_id . "'");

		return $query->row;
	}

	public function editTicketuser($data) {
		$this->db->query("UPDATE " . DB_PREFIX . "ticketuser SET name = '" . $this->db->escape($data['name']) . "', email = '" . $this->db->escape($data['email']) . "', image = '" . $this->db->escape($data['image']) . "', date_modified = NOW() WHERE ticketuser_id = '". (int)$this->ticketuser->getId() ."'");

		$this->db->query("UPDATE " . DB_PREFIX . "ticketrequest SET email = '" . $this->db->escape($data['email']) . "' WHERE ticketuser_id = '". (int)$this->ticketuser->getId() ."'");
	}

	public function editPassword($email, $password) {
		$this->db->query("UPDATE " . DB_PREFIX . "ticketuser SET salt = '" . $this->db->escape($salt = $this->token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($password)))) . "' WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
	}

	public function getTicketuserByEmail($email) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ticketuser WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}

	public function getTotalTicketusersByEmail($email) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "ticketuser WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row['total'];
	}

	public function getAdminUser($user_id) {
		$query = $this->db->query("SELECT *, (SELECT ug.name FROM `" . DB_PREFIX . "user_group` ug WHERE ug.user_group_id = u.user_group_id) AS user_group FROM `" . DB_PREFIX . "user` u WHERE u.user_id = '" . (int)$user_id . "'");

		return $query->row;
	}

	public function token($length = 32) {
		// Create random token
		$string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

		$max = strlen($string) - 1;

		$token = '';

		for ($i = 0; $i < $length; $i++) {
			$token .= $string[mt_rand(0, $max)];
		}

		return $token;
	}
}