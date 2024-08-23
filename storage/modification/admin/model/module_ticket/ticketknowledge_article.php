<?php
/* This file is under Git Control by KDSI. */
class ModelModuleTicketTicketKnowledgeArticle extends Model {
	public function addTicketKnowledgeArticle($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "ticketknowledge_article SET sort_order = '" . (int)$data['sort_order'] . "', ticketknowledge_section_id = '" . (int)$data['ticketknowledge_section_id'] . "', status = '" . (int)$data['status'] . "'");

		$ticketknowledge_article_id = $this->db->getLastId();

		foreach ($data['ticketknowledge_article_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "ticketknowledge_article_description SET ticketknowledge_article_id = '" . (int)$ticketknowledge_article_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', banner_title = '" . $this->db->escape($value['banner_title']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		if (isset($data['ticketknowledge_article_related'])) {
			foreach ($data['ticketknowledge_article_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "ticketknowledge_article_related WHERE ticketknowledge_article_id = '" . (int)$ticketknowledge_article_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "ticketknowledge_article_related SET ticketknowledge_article_id = '" . (int)$ticketknowledge_article_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "ticketknowledge_article_related WHERE ticketknowledge_article_id = '" . (int)$related_id . "' AND related_id = '" . (int)$ticketknowledge_article_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "ticketknowledge_article_related SET ticketknowledge_article_id = '" . (int)$related_id . "', related_id = '" . (int)$ticketknowledge_article_id . "'");
			}
		}
		
		return $ticketknowledge_article_id;
	}

	public function editTicketKnowledgeArticle($ticketknowledge_article_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "ticketknowledge_article SET sort_order = '" . (int)$data['sort_order'] . "', ticketknowledge_section_id = '" . (int)$data['ticketknowledge_section_id'] . "', status = '" . (int)$data['status'] . "' WHERE ticketknowledge_article_id = '" . (int)$ticketknowledge_article_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "ticketknowledge_article_description WHERE ticketknowledge_article_id = '" . (int)$ticketknowledge_article_id . "'");

		foreach ($data['ticketknowledge_article_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "ticketknowledge_article_description SET ticketknowledge_article_id = '" . (int)$ticketknowledge_article_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', banner_title = '" . $this->db->escape($value['banner_title']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "ticketknowledge_article_related WHERE ticketknowledge_article_id = '" . (int)$ticketknowledge_article_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ticketknowledge_article_related WHERE related_id = '" . (int)$ticketknowledge_article_id . "'");

		if (isset($data['ticketknowledge_article_related'])) {
			foreach ($data['ticketknowledge_article_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "ticketknowledge_article_related WHERE ticketknowledge_article_id = '" . (int)$ticketknowledge_article_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "ticketknowledge_article_related SET ticketknowledge_article_id = '" . (int)$ticketknowledge_article_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "ticketknowledge_article_related WHERE ticketknowledge_article_id = '" . (int)$related_id . "' AND related_id = '" . (int)$ticketknowledge_article_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "ticketknowledge_article_related SET ticketknowledge_article_id = '" . (int)$related_id . "', related_id = '" . (int)$ticketknowledge_article_id . "'");
			}
		}
	}

	public function deleteTicketKnowledgeArticle($ticketknowledge_article_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ticketknowledge_article WHERE ticketknowledge_article_id = '" . (int)$ticketknowledge_article_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ticketknowledge_article_description WHERE ticketknowledge_article_id = '" . (int)$ticketknowledge_article_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ticketknowledge_article_related WHERE ticketknowledge_article_id = '" . (int)$ticketknowledge_article_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ticketknowledge_article_related WHERE related_id = '" . (int)$ticketknowledge_article_id . "'");
	}

	public function getTicketknowledgeArticleRelated($ticketknowledge_article_id) {
		$ticketknowledge_article_related_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ticketknowledge_article_related WHERE ticketknowledge_article_id = '" . (int)$ticketknowledge_article_id . "'");

		foreach ($query->rows as $result) {
			$ticketknowledge_article_related_data[] = $result['related_id'];
		}

		return $ticketknowledge_article_related_data;
	}

	public function getTicketKnowledgeArticle($ticketknowledge_article_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "ticketknowledge_article i LEFT JOIN " . DB_PREFIX . "ticketknowledge_article_description id ON (i.ticketknowledge_article_id = id.ticketknowledge_article_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i.ticketknowledge_article_id = '" . (int)$ticketknowledge_article_id . "'");

		return $query->row;
	}

	public function getTicketKnowledgeArticles($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "ticketknowledge_article i LEFT JOIN " . DB_PREFIX . "ticketknowledge_article_description id ON (i.ticketknowledge_article_id = id.ticketknowledge_article_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "'";

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

	public function getTicketKnowledgeArticleDescriptions($ticketknowledge_article_id) {
		$ticketknowledge_article_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ticketknowledge_article_description WHERE ticketknowledge_article_id = '" . (int)$ticketknowledge_article_id . "'");

		foreach ($query->rows as $result) {
			$ticketknowledge_article_description_data[$result['language_id']] = array(
				'title'            => $result['title'],
				'banner_title'     => $result['banner_title'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'description'      => $result['description'],
			);
		}

		return $ticketknowledge_article_description_data;
	}

	public function getTotalTicketKnowledgeArticles() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "ticketknowledge_article");

		return $query->row['total'];
	}
}