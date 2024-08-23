<?php
class ModelExtensionMpBlogMpBlogRating extends Model {
	public function addMpBlogRating($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "mpblograting SET  mpblogpost_id = '" . (int)$data['mpblogpost_id'] . "', rating = '" . (int)$data['rating'] . "', status = '" . (int)$data['status'] . "', date_added = '" . $this->db->escape($data['date_added']) . "'");

		$mpblograting_id = $this->db->getLastId();

		return $mpblograting_id;
	}

	public function editMpBlogRating($mpblograting_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "mpblograting SET mpblogpost_id = '" . (int)$data['mpblogpost_id'] . "', rating = '" . (int)$data['rating'] . "', status = '" . (int)$data['status'] . "', date_added = '" . $this->db->escape($data['date_added']) . "', date_modified = NOW() WHERE mpblograting_id = '" . (int)$mpblograting_id . "'");
	}

	public function deleteMpBlogRating($mpblograting_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpblograting WHERE mpblograting_id = '" . (int)$mpblograting_id . "'");
	}

	public function getMpBlogRating($mpblograting_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT pd.name FROM " . DB_PREFIX . "mpblogpost_description pd WHERE pd.mpblogpost_id = r.mpblogpost_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS mpblogpost FROM " . DB_PREFIX . "mpblograting r WHERE r.mpblograting_id = '" . (int)$mpblograting_id . "'");

		return $query->row;
	}

	public function getMpBlogRatings($data = array()) {
		$sql = "SELECT r.mpblograting_id, pd.name, r.rating, r.status, r.date_added FROM " . DB_PREFIX . "mpblograting r LEFT JOIN " . DB_PREFIX . "mpblogpost_description pd ON (r.mpblogpost_id = pd.mpblogpost_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND r.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		$sort_data = array(
			'pd.name',
			'r.rating',
			'r.status',
			'r.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY r.date_added";
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

	public function getTotalMpBlogRatings($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mpblograting r LEFT JOIN " . DB_PREFIX . "mpblogpost_description pd ON (r.mpblogpost_id = pd.mpblogpost_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}
		
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND r.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalMpBlogRatingsAwaitingApproval() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mpblograting WHERE status = '0'");

		return $query->row['total'];
	}
}