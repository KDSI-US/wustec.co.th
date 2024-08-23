<?php
/* This file is under Git Control by KDSI. */
class ModelModuleTicketTicketKnowledgeSection extends Model {
	public function addTicketKnowledgeSection($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "ticketknowledge_section SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', icon_class = '" . $this->db->escape($data['icon_class']) . "'");

		$ticketknowledge_section_id = $this->db->getLastId();

		foreach ($data['ticketknowledge_section_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "ticketknowledge_section_description SET ticketknowledge_section_id = '" . (int)$ticketknowledge_section_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', sub_title = '" . $this->db->escape($value['sub_title']) . "', banner_title = '" . $this->db->escape($value['banner_title']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}
		
		return $ticketknowledge_section_id;
	}

	public function editTicketKnowledgeSection($ticketknowledge_section_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "ticketknowledge_section SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', icon_class = '" . $this->db->escape($data['icon_class']) . "' WHERE ticketknowledge_section_id = '" . (int)$ticketknowledge_section_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "ticketknowledge_section_description WHERE ticketknowledge_section_id = '" . (int)$ticketknowledge_section_id . "'");

		foreach ($data['ticketknowledge_section_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "ticketknowledge_section_description SET ticketknowledge_section_id = '" . (int)$ticketknowledge_section_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', sub_title = '" . $this->db->escape($value['sub_title']) . "', banner_title = '" . $this->db->escape($value['banner_title']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}
	}

	public function deleteTicketKnowledgeSection($ticketknowledge_section_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ticketknowledge_section WHERE ticketknowledge_section_id = '" . (int)$ticketknowledge_section_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ticketknowledge_section_description WHERE ticketknowledge_section_id = '" . (int)$ticketknowledge_section_id . "'");
	}

	public function getTicketKnowledgeSection($ticketknowledge_section_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "ticketknowledge_section WHERE ticketknowledge_section_id = '" . (int)$ticketknowledge_section_id . "'");

		return $query->row;
	}

	public function getTicketKnowledgeSectionDescription($ticketknowledge_section_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "ticketknowledge_section i LEFT JOIN " . DB_PREFIX . "ticketknowledge_section_description id ON (i.ticketknowledge_section_id = id.ticketknowledge_section_id) WHERE i.ticketknowledge_section_id = '" . (int)$ticketknowledge_section_id . "' AND id.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getTicketKnowledgeSections($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "ticketknowledge_section i LEFT JOIN " . DB_PREFIX . "ticketknowledge_section_description id ON (i.ticketknowledge_section_id = id.ticketknowledge_section_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "'";

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

	public function getTicketKnowledgeSectionDescriptions($ticketknowledge_section_id) {
		$ticketknowledge_section_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ticketknowledge_section_description WHERE ticketknowledge_section_id = '" . (int)$ticketknowledge_section_id . "'");


		foreach ($query->rows as $result) {
			$ticketknowledge_section_description_data[$result['language_id']] = array(
				'title'            => $result['title'],
				'sub_title'        => $result['sub_title'],
				'banner_title'     => $result['banner_title'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'description'      => $result['description'],
			);
		}

		return $ticketknowledge_section_description_data;
	}

	public function getTotalTicketKnowledgeSections() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "ticketknowledge_section");

		return $query->row['total'];
	}
}