<?php
class ModelExtensionMpBlogMpBlogComment extends Model {
	public function addMpBlogComment($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogcomment SET author = '" . $this->db->escape($data['author']) . "', mpblogpost_id = '" . (int)$data['mpblogpost_id'] . "', text = '" . $this->db->escape(strip_tags($data['text'])) . "', status = '" . (int)$data['status'] . "', date_added = '" . $this->db->escape($data['date_added']) . "'");

		$mpblogcomment_id = $this->db->getLastId();

		return $mpblogcomment_id;
	}

	public function editMpBlogComment($mpblogcomment_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "mpblogcomment SET author = '" . $this->db->escape($data['author']) . "', mpblogpost_id = '" . (int)$data['mpblogpost_id'] . "', text = '" . $this->db->escape(strip_tags($data['text'])) . "', status = '" . (int)$data['status'] . "', date_added = '" . $this->db->escape($data['date_added']) . "', date_modified = NOW() WHERE mpblogcomment_id = '" . (int)$mpblogcomment_id . "'");

	}

	public function deleteMpBlogComment($mpblogcomment_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogcomment WHERE mpblogcomment_id = '" . (int)$mpblogcomment_id . "'");
	}

	public function getMpBlogComment($mpblogcomment_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT pd.name FROM " . DB_PREFIX . "mpblogpost_description pd WHERE pd.mpblogpost_id = r.mpblogpost_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS mpblogpost FROM " . DB_PREFIX . "mpblogcomment r WHERE r.mpblogcomment_id = '" . (int)$mpblogcomment_id . "'");

		return $query->row;
	}

	public function getMpBlogComments($data = array()) {
		$sql = "SELECT r.mpblogcomment_id, pd.name, r.author, r.status, r.date_added, r.text FROM " . DB_PREFIX . "mpblogcomment r LEFT JOIN " . DB_PREFIX . "mpblogpost_description pd ON (r.mpblogpost_id = pd.mpblogpost_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_author'])) {
			$sql .= " AND r.author LIKE '" . $this->db->escape($data['filter_author']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND r.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		$sort_data = array(
			'pd.name',
			'r.author',
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

	public function getTotalMpBlogComments($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mpblogcomment r LEFT JOIN " . DB_PREFIX . "mpblogpost_description pd ON (r.mpblogpost_id = pd.mpblogpost_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_author'])) {
			$sql .= " AND r.author LIKE '" . $this->db->escape($data['filter_author']) . "%'";
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

	public function getTotalMpBlogCommentsAwaitingApproval() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mpblogcomment WHERE status = '0'");

		return $query->row['total'];
	}
}