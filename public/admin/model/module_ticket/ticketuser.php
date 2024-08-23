<?php
class ModelModuleTicketTicketuser extends Model {
	public function editTicketuser($ticketuser_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "ticketuser SET name = '" . $this->db->escape($data['name']) . "',  email = '" . $this->db->escape($data['email']) . "', status = '" . (int)$data['status'] . "' WHERE ticketuser_id = '" . (int)$ticketuser_id . "'");

		if ($data['password']) {
			$this->db->query("UPDATE " . DB_PREFIX . "ticketuser SET salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' WHERE ticketuser_id = '" . (int)$ticketuser_id . "'");
		}
	}

	public function deleteTicketuser($ticketuser_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ticketuser WHERE ticketuser_id = '" . (int)$ticketuser_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ticketrequest WHERE ticketuser_id = '" . (int)$ticketuser_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ticketrequest_chat WHERE ticketuser_id = '" . (int)$ticketuser_id . "'");
	}

	public function getTicketuser($ticketuser_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "ticketuser WHERE ticketuser_id = '" . (int)$ticketuser_id . "'");

		return $query->row;
	}

	public function getTicketuserByEmail($email) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "ticketuser WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}

	public function getTicketusers($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "ticketuser t WHERE t.ticketuser_id>0";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "t.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$implode[] = "t.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (!empty($data['filter_ip'])) {
			$implode[] = "t.ip = '" . (int)$data['filter_ip'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "t.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(t.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$sort_data = array(
			't.name',
			't.email',
			't.status',
			't.ip',
			't.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY t.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
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

	public function getTotalTicketusers($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "ticketuser t WHERE t.ticketuser_id>0";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "t.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$implode[] = "t.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (!empty($data['filter_ip'])) {
			$implode[] = "t.ip = '" . (int)$data['filter_ip'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "t.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(t.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}
