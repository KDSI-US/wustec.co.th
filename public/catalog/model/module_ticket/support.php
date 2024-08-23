<?php
class ModelModuleTicketSupport extends Model {
	public function getTicketdepartment($ticketdepartment_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "ticketdepartment i LEFT JOIN " . DB_PREFIX . "ticketdepartment_description id ON (i.ticketdepartment_id = id.ticketdepartment_id) WHERE i.ticketdepartment_id = '" . (int)$ticketdepartment_id . "' AND id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i.status = '1'");

		return $query->row;
	}

	public function getTicketdepartments() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ticketdepartment i LEFT JOIN " . DB_PREFIX . "ticketdepartment_description id ON (i.ticketdepartment_id = id.ticketdepartment_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i.status = '1' ORDER BY i.sort_order, LCASE(id.title) ASC");

		return $query->rows;
	}

	public function getTicketvideos() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ticketvideo i LEFT JOIN " . DB_PREFIX . "ticketvideo_description id ON (i.ticketvideo_id = id.ticketvideo_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i.status = '1' ORDER BY i.sort_order, LCASE(id.title) ASC");

		return $query->rows;
	}

	public function getTicketknowledgeSections() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ticketknowledge_section i LEFT JOIN " . DB_PREFIX . "ticketknowledge_section_description id ON (i.ticketknowledge_section_id = id.ticketknowledge_section_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i.status = '1' ORDER BY i.sort_order, LCASE(id.title) ASC");

		return $query->rows;
	}

	public function getTicketknowledgeSection($ticketknowledge_section_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ticketknowledge_section i LEFT JOIN " . DB_PREFIX . "ticketknowledge_section_description id ON (i.ticketknowledge_section_id = id.ticketknowledge_section_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i.status = '1' AND i.ticketknowledge_section_id = '". (int)$ticketknowledge_section_id ."'");

		return $query->row;
	}

	public function getTicketknowledgeArticles($ticketknowledge_section_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ticketknowledge_article i LEFT JOIN " . DB_PREFIX . "ticketknowledge_article_description id ON (i.ticketknowledge_article_id = id.ticketknowledge_article_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i.status = '1' AND i.ticketknowledge_section_id = '". (int)$ticketknowledge_section_id ."' ORDER BY i.sort_order, LCASE(id.title) ASC");

		return $query->rows;
	}

	public function getTicketknowledgeArticle($ticketknowledge_article_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ticketknowledge_article i LEFT JOIN " . DB_PREFIX . "ticketknowledge_article_description id ON (i.ticketknowledge_article_id = id.ticketknowledge_article_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i.status = '1' AND i.ticketknowledge_article_id = '". (int)$ticketknowledge_article_id ."'");

		return $query->row;
	}

	public function getTicketknowledgeArticleRelated($ticketknowledge_article_id) {
		$ticketknowledge_article_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ticketknowledge_article_related tr LEFT JOIN " . DB_PREFIX . "ticketknowledge_article t ON (tr.related_id = t.ticketknowledge_article_id) WHERE tr.ticketknowledge_article_id = '" . (int)$ticketknowledge_article_id . "' AND t.status = '1'");

		foreach ($query->rows as $result) {
			$ticketknowledge_article_data[$result['related_id']] = $this->getTicketKnowledgeArticle($result['related_id']);
		}

		return $ticketknowledge_article_data;
	}
}