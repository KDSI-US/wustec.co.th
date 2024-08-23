<?php
namespace Modulepoints;
class Ticketuser {
	private $ticketuser_id;
	private $name;
	private $email;
	private $image;

	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');

		if (isset($this->session->data['ticketuser_id'])) {
			$ticketuser_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ticketuser WHERE ticketuser_id = '" . (int)$this->session->data['ticketuser_id'] . "' AND status = '1'");

			if ($ticketuser_query->num_rows) {
				$this->ticketuser_id = $ticketuser_query->row['ticketuser_id'];
				$this->name = $ticketuser_query->row['name'];
				$this->email = $ticketuser_query->row['email'];
				$this->image = $ticketuser_query->row['image'];

				$this->db->query("UPDATE " . DB_PREFIX . "ticketuser SET language_id = '" . (int)$this->config->get('config_language_id') . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE ticketuser_id = '" . (int)$this->ticketuser_id . "'");
			} else {
				$this->logout();
			}
		}
	}

	public function login($email, $password, $override = false) {
		if ($override) {
			$ticketuser_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ticketuser WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "' AND status = '1'");
		} else {
			$ticketuser_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ticketuser WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($password) . "'))))) OR password = '" . $this->db->escape(md5($password)) . "') AND status = '1'");
		}

		if ($ticketuser_query->num_rows) {
			$this->session->data['ticketuser_id'] = $ticketuser_query->row['ticketuser_id'];

			$this->ticketuser_id = $ticketuser_query->row['ticketuser_id'];
			$this->name = $ticketuser_query->row['name'];
			$this->email = $ticketuser_query->row['email'];
			$this->image = $ticketuser_query->row['image'];

			$this->db->query("UPDATE " . DB_PREFIX . "ticketuser SET language_id = '" . (int)$this->config->get('config_language_id') . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE ticketuser_id = '" . (int)$this->ticketuser_id . "'");

			return true;
		} else {
			return false;
		}
	}

	public function Forcelogin($email) {
		$ticketuser_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ticketuser WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "' AND status = '1'");		

		if ($ticketuser_query->num_rows) {
			$this->session->data['ticketuser_id'] = $ticketuser_query->row['ticketuser_id'];

			$this->ticketuser_id = $ticketuser_query->row['ticketuser_id'];
			$this->name = $ticketuser_query->row['name'];
			$this->email = $ticketuser_query->row['email'];
			$this->image = $ticketuser_query->row['image'];

			$this->db->query("UPDATE " . DB_PREFIX . "ticketuser SET language_id = '" . (int)$this->config->get('config_language_id') . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE ticketuser_id = '" . (int)$this->ticketuser_id . "'");

			return true;
		} else {
			return false;
		}
	}

	public function logout() {
		unset($this->session->data['ticketuser_id']);

		$this->ticketuser = '';
		$this->name = '';
		$this->email = '';
		$this->image = '';
	}

	public function isLogged() {
		return $this->ticketuser_id;
	}

	public function getId() {
		return $this->ticketuser_id;
	}

	public function getName() {
		return $this->name;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getImage() {
		return $this->image;
	}
}
