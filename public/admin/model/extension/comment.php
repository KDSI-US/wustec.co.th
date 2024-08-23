<?php
class ModelExtensioncomment extends Model {
	public function addcomment($data) {
		

		$this->db->query("INSERT INTO " . DB_PREFIX . "post_comment SET author = '" . $this->db->escape($data['author']) . "', post_id = '" . (int)$data['post_id'] . "', text = '" . $this->db->escape(strip_tags($data['text'])) . "', status = '" . (int)$data['status'] . "', date_added = NOW()");

		$comment_id = $this->db->getLastId();

		
		

		return $comment_id;
	}

	public function editcomment($comment_id, $data) {
		

		$this->db->query("UPDATE " . DB_PREFIX . "post_comment SET author = '" . $this->db->escape($data['author']) . "', post_id = '" . (int)$data['post_id'] . "', text = '" . $this->db->escape(strip_tags($data['text'])) . "',  status = '" . (int)$data['status'] . "', date_modified = NOW() WHERE comment_id = '" . (int)$comment_id . "'");

		

		
	}

	public function deletecomment($comment_id) {
		

		$this->db->query("DELETE FROM " . DB_PREFIX . "post_comment WHERE comment_id = '" . (int)$comment_id . "'");

		

		
	}

	public function getcomment($comment_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT pd.name FROM " . DB_PREFIX . "post_description pd WHERE pd.post_id = r.post_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS post FROM " . DB_PREFIX . "post_comment r WHERE r.comment_id = '" . (int)$comment_id . "'");

		return $query->row;
	}

	public function getcomments($data = array()) {
		$sql = "SELECT r.comment_id, pd.name, r.author, r.status, r.date_added FROM " . DB_PREFIX . "post_comment r LEFT JOIN " . DB_PREFIX . "post_description pd ON (r.post_id = pd.post_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_post'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_post']) . "%'";
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

	public function getTotalcomments($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "post_comment r LEFT JOIN " . DB_PREFIX . "post_description pd ON (r.post_id = pd.post_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_post'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_post']) . "%'";
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

	public function getTotalcommentsAwaitingApproval() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "post_comment WHERE status = '0'");

		return $query->row['total'];
	}
}