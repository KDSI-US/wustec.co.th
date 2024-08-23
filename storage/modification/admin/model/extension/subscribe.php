<?php
/* This file is under Git Control by KDSI. */
class ModelExtensionsubscribe extends Model {
	public function addcomment($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "blogsubscribe SET email = '" . $this->db->escape($data['email']) . "', status = '" . (int)$data['status'] . "'");
		$subscribe_id = $this->db->getLastId();
		return $subscribe_id;
	}

	public function editsubscribe($subscribe_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "blogsubscribe SET email = '" . $this->db->escape($data['email']) . "', status = '" . (int)$data['status'] . "' WHERE subscribe_id = '" . (int)$subscribe_id . "'");
	}

	public function deletesubscribe($subscribe_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "blogsubscribe WHERE subscribe_id = '" . (int)$subscribe_id . "'");
	}

	public function getsubscriber($subscribe_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blogsubscribe WHERE subscribe_id = '" . (int)$subscribe_id . "'");
		return $query->row;
	}

	public function getsubscribers($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "blogsubscribe WHERE subscribe_id > 0";

		if (isset($data['filter_sub'])) {
			$sql .= " AND status = '" . $data['filter_sub'] . "'";
		}

		if (!empty($data['filter_email'])) {
			$sql .= " AND email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}
		
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalsubscribe($data = array()) {
		$sql = "SELECT COUNT(*) As total FROM " . DB_PREFIX . "blogsubscribe WHERE subscribe_id > 0";

		if (!empty($data['filter_sub'])) {
			$sql .= " AND status = '" . $data['filter_sub'] . "%'";
		}

		if (!empty($data['filter_email'])) {
			$sql .= " AND email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

}