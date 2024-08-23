<?php
class ModelModuleTicketTicketstatus extends Model {
	public function addTicketstatus($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "ticketstatus SET sort_order = '" . (int)$data['sort_order'] . "', bgcolor = '" . $this->db->escape($data['bgcolor']) . "', textcolor = '" . $this->db->escape($data['textcolor']) . "'");

		$ticketstatus_id = $this->db->getLastId();

		foreach ($data['ticketstatus_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "ticketstatus_description SET ticketstatus_id = '" . (int)$ticketstatus_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}
		
		return $ticketstatus_id;
	}

	public function editTicketstatus($ticketstatus_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "ticketstatus SET sort_order = '" . (int)$data['sort_order'] . "', bgcolor = '" . $this->db->escape($data['bgcolor']) . "', textcolor = '" . $this->db->escape($data['textcolor']) . "' WHERE ticketstatus_id = '". (int)$ticketstatus_id ."'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "ticketstatus_description WHERE ticketstatus_id = '" . (int)$ticketstatus_id . "'");
		foreach ($data['ticketstatus_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "ticketstatus_description SET ticketstatus_id = '" . (int)$ticketstatus_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}
	}

	public function deleteTicketstatus($ticketstatus_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ticketstatus WHERE ticketstatus_id = '" . (int)$ticketstatus_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ticketstatus_description WHERE ticketstatus_id = '" . (int)$ticketstatus_id . "'");
	}

	public function getTicketstatus($ticketstatus_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ticketstatus WHERE ticketstatus_id = '" . (int)$ticketstatus_id . "'");

		return $query->row;
	}

	public function getTicketstatusFull($ticketstatus_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ticketstatus t LEFT JOIN ". DB_PREFIX ."ticketstatus_description td ON(t.ticketstatus_id = td.ticketstatus_id) WHERE t.ticketstatus_id = '" . (int)$ticketstatus_id . "' AND td.language_id = '". (int)$this->config->get('config_language_id') ."'");

		return $query->row;
	}

	public function getTicketstatuses($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "ticketstatus t LEFT JOIN " . DB_PREFIX . "ticketstatus_description td ON (t.ticketstatus_id = td.ticketstatus_id) WHERE td.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		$sort_data = array(
			'td.name',
			't.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY td.name";
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

	public function getTicketstatusDescriptions($ticketstatus_id) {
		$ticketstatus_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ticketstatus_description WHERE ticketstatus_id = '" . (int)$ticketstatus_id . "'");

		foreach ($query->rows as $result) {
			$ticketstatus_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $ticketstatus_data;
	}

	public function getTotalTicketstatuses() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "ticketstatus");

		return $query->row['total'];
	}
}