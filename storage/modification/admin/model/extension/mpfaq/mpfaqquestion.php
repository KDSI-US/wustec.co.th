<?php
/* This file is under Git Control by KDSI. */
class ModelExtensionMpFaqMpfaqquestion extends Model {
	public function addMpfaqquestion($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "mpfaqquestion SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', mpfaqcategory_id = '" . (int)$data['mpfaqcategory_id'] . "', date_modified = NOW(), date_added = NOW()");	

		$mpfaqquestion_id = $this->db->getLastId();

		foreach ($data['mpfaqquestion_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mpfaqquestion_description SET mpfaqquestion_id = '" . (int)$mpfaqquestion_id . "', language_id = '" . (int)$language_id . "', mpfaqcategory_id = '" . (int)$data['mpfaqcategory_id'] . "', question = '" . $this->db->escape($value['question']) . "', answer = '" . $this->db->escape($value['answer']) . "'");
		}

		$this->cache->delete('mpfaqquestion');

		return $mpfaqquestion_id;
	}

	public function editMpfaqquestion($mpfaqquestion_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "mpfaqquestion SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', mpfaqcategory_id = '" . (int)$data['mpfaqcategory_id'] . "', date_modified = NOW() WHERE mpfaqquestion_id = '" . (int)$mpfaqquestion_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "mpfaqquestion_description WHERE mpfaqquestion_id = '" . (int)$mpfaqquestion_id . "'");

		foreach ($data['mpfaqquestion_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mpfaqquestion_description SET mpfaqquestion_id = '" . (int)$mpfaqquestion_id . "', language_id = '" . (int)$language_id . "', mpfaqcategory_id = '" . (int)$data['mpfaqcategory_id'] . "', question = '" . $this->db->escape($value['question']) . "', answer = '" . $this->db->escape($value['answer']) . "'");
		}

		$this->cache->delete('mpfaqquestion');
	}

	public function deleteMpfaqquestion($mpfaqquestion_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpfaqquestion WHERE mpfaqquestion_id = '" . (int)$mpfaqquestion_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpfaqquestion_description WHERE mpfaqquestion_id = '" . (int)$mpfaqquestion_id . "'");

		$this->cache->delete('mpfaqquestion');
	}	

	public function getMpfaqquestion($mpfaqquestion_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpfaqquestion f LEFT JOIN " . DB_PREFIX . "mpfaqquestion_description fd ON (f.mpfaqquestion_id = fd.mpfaqquestion_id) WHERE f.mpfaqquestion_id = '" . (int)$mpfaqquestion_id . "' AND fd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getMpfaqquestions($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "mpfaqquestion f LEFT JOIN " . DB_PREFIX . "mpfaqquestion_description fd ON (f.mpfaqquestion_id = fd.mpfaqquestion_id) LEFT JOIN " . DB_PREFIX . "mpfaqcategory_description mfcd ON(f.mpfaqcategory_id = mfcd.mpfaqcategory_id) WHERE fd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_question'])) {
			$sql .= " AND fd.question LIKE '" . $this->db->escape($data['filter_question']) . "%'";
		}

		if (!empty($data['filter_category'])) {
			$sql .= " AND mfcd.name LIKE '" . $this->db->escape($data['filter_category']) . "%'";
		}		

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND f.status = '" . (int)$data['filter_status'] . "'";
		}

		$sql .= " GROUP BY f.mpfaqquestion_id";

		$sort_data = array(
			'f.status',
			'fd.question',
			'f.sort_order',
			'mfcd.name',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY sort_order";
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

	public function getMpfaqquestionDescriptions($mpfaqquestion_id) {
		$mpfaqquestion_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpfaqquestion_description WHERE mpfaqquestion_id = '" . (int)$mpfaqquestion_id . "'");

		foreach ($query->rows as $result) {
			$mpfaqquestion_description_data[$result['language_id']] = array(
				'question'             => $result['question'],
				'answer'      => $result['answer']
			);
		}

		return $mpfaqquestion_description_data;
	}
	
	public function getTotalMpfaqquestions() {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mpfaqquestion f LEFT JOIN " . DB_PREFIX . "mpfaqquestion_description fd ON (f.mpfaqquestion_id = fd.mpfaqquestion_id) LEFT JOIN " . DB_PREFIX . "mpfaqcategory_description mfcd ON(f.mpfaqcategory_id = mfcd.mpfaqcategory_id)";

		$sql .= " WHERE fd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_question'])) {
			$sql .= " AND fd.question LIKE '" . $this->db->escape($data['filter_question']) . "%'";
		}

		if (!empty($data['filter_category'])) {
			$sql .= " AND mfcd.name LIKE '" . $this->db->escape($data['filter_category']) . "%'";
		}		

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND f.status = '" . (int)$data['filter_status'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}	
	
}
