<?php
class ModelModuleTicketTicketDepartment extends Model {
	public function addTicketdepartment($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "ticketdepartment SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', icon_class = '" . $this->db->escape($data['icon_class']) . "'");

		$ticketdepartment_id = $this->db->getLastId();

		foreach ($data['ticketdepartment_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "ticketdepartment_description SET ticketdepartment_id = '" . (int)$ticketdepartment_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', sub_title = '" . $this->db->escape($value['sub_title']) . "'");
		}
		
		return $ticketdepartment_id;
	}

	public function editTicketdepartment($ticketdepartment_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "ticketdepartment SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', icon_class = '" . $this->db->escape($data['icon_class']) . "' WHERE ticketdepartment_id = '" . (int)$ticketdepartment_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "ticketdepartment_description WHERE ticketdepartment_id = '" . (int)$ticketdepartment_id . "'");

		foreach ($data['ticketdepartment_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "ticketdepartment_description SET ticketdepartment_id = '" . (int)$ticketdepartment_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', sub_title = '" . $this->db->escape($value['sub_title']) . "'");
		}
	}

	public function deleteTicketdepartment($ticketdepartment_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ticketdepartment WHERE ticketdepartment_id = '" . (int)$ticketdepartment_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ticketdepartment_description WHERE ticketdepartment_id = '" . (int)$ticketdepartment_id . "'");
	}

	public function getTicketdepartment($ticketdepartment_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "ticketdepartment WHERE ticketdepartment_id = '" . (int)$ticketdepartment_id . "'");

		return $query->row;
	}

	public function getTicketdepartmentFull($ticketdepartment_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "ticketdepartment t LEFT JOIN ". DB_PREFIX ."ticketdepartment_description td ON (t.ticketdepartment_id = td.ticketdepartment_id) WHERE t.ticketdepartment_id = '" . (int)$ticketdepartment_id . "' AND td.language_id = '". (int)$this->config->get('config_language_id') ."'");

		return $query->row;
	}

	public function getTicketdepartmentByLanguage($ticketdepartment_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "ticketdepartment i LEFT JOIN " . DB_PREFIX . "ticketdepartment_description id ON (i.ticketdepartment_id = id.ticketdepartment_id) WHERE i.ticketdepartment_id = '" . (int)$ticketdepartment_id . "' AND id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i.status = '1'");

		return $query->row;
	}

	public function getTicketdepartments($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "ticketdepartment i LEFT JOIN " . DB_PREFIX . "ticketdepartment_description id ON (i.ticketdepartment_id = id.ticketdepartment_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		$sort_data = array(
			'id.title',
			'i.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY id.title";
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

	public function getTicketdepartmentDescriptions($ticketdepartment_id) {
		$ticketdepartment_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ticketdepartment_description WHERE ticketdepartment_id = '" . (int)$ticketdepartment_id . "'");

		foreach ($query->rows as $result) {
			$ticketdepartment_description_data[$result['language_id']] = array(
				'title'            => $result['title'],
				'sub_title'        => $result['sub_title'],
			);
		}

		return $ticketdepartment_description_data;
	}

	public function getTotalTicketdepartments() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "ticketdepartment");

		return $query->row['total'];
	}
}