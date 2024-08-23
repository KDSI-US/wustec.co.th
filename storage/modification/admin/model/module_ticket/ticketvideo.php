<?php
/* This file is under Git Control by KDSI. */
class ModelModuleTicketTicketVideo extends Model {
	public function addTicketVideo($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "ticketvideo SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', url = '" . $this->db->escape($data['url']) . "'");

		$ticketvideo_id = $this->db->getLastId();

		foreach ($data['ticketvideo_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "ticketvideo_description SET ticketvideo_id = '" . (int)$ticketvideo_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', sub_title = '" . $this->db->escape($value['sub_title']) . "'");
		}
		
		return $ticketvideo_id;
	}

	public function editTicketVideo($ticketvideo_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "ticketvideo SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', url = '" . $this->db->escape($data['url']) . "' WHERE ticketvideo_id = '" . (int)$ticketvideo_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "ticketvideo_description WHERE ticketvideo_id = '" . (int)$ticketvideo_id . "'");

		foreach ($data['ticketvideo_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "ticketvideo_description SET ticketvideo_id = '" . (int)$ticketvideo_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', sub_title = '" . $this->db->escape($value['sub_title']) . "'");
		}
	}

	public function deleteTicketVideo($ticketvideo_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ticketvideo WHERE ticketvideo_id = '" . (int)$ticketvideo_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ticketvideo_description WHERE ticketvideo_id = '" . (int)$ticketvideo_id . "'");
	}

	public function getTicketVideo($ticketvideo_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "ticketvideo WHERE ticketvideo_id = '" . (int)$ticketvideo_id . "'");

		return $query->row;
	}

	public function getTicketVideoDescription($ticketvideo_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "ticketvideo i LEFT JOIN " . DB_PREFIX . "ticketvideo_description id ON (i.ticketvideo_id = id.ticketvideo_id) WHERE i.ticketvideo_id = '" . (int)$ticketvideo_id . "' AND id.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getTicketVideos($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "ticketvideo i LEFT JOIN " . DB_PREFIX . "ticketvideo_description id ON (i.ticketvideo_id = id.ticketvideo_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_title'])) {
			$sql .= " AND id.title LIKE '" . $this->db->escape($data['filter_title']) . "%'";
		}

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

	public function getTicketVideoDescriptions($ticketvideo_id) {
		$ticketvideo_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ticketvideo_description WHERE ticketvideo_id = '" . (int)$ticketvideo_id . "'");


		foreach ($query->rows as $result) {
			$ticketvideo_description_data[$result['language_id']] = array(
				'title'            => $result['title'],
				'sub_title'        => $result['sub_title'],
			);
		}

		return $ticketvideo_description_data;
	}

	public function getTotalTicketVideos() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "ticketvideo");

		return $query->row['total'];
	}
}