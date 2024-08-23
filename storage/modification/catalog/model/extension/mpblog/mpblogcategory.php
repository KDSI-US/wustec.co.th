<?php
/* This file is under Git Control by KDSI. */
use \MpBlog\ModelCatalog as Model;
class ModelExtensionMpBlogMpBlogCategory extends Model {
	
	public function getMpBlogCategory($mpblogcategory_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mpblogcategory c LEFT JOIN " . DB_PREFIX . "mpblogcategory_description cd ON (c.mpblogcategory_id = cd.mpblogcategory_id) LEFT JOIN " . DB_PREFIX . "mpblogcategory_to_store c2s ON (c.mpblogcategory_id = c2s.mpblogcategory_id) WHERE c.mpblogcategory_id = '" . (int)$mpblogcategory_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");

		return $query->row;
	}

	public function getMpBlogCategories($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpblogcategory c LEFT JOIN " . DB_PREFIX . "mpblogcategory_description cd ON (c.mpblogcategory_id = cd.mpblogcategory_id) LEFT JOIN " . DB_PREFIX . "mpblogcategory_to_store c2s ON (c.mpblogcategory_id = c2s.mpblogcategory_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");

		return $query->rows;
	}

	public function getTotalMpBlogCategoriesByMpBlogCategoryId($parent_id = 0) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mpblogcategory c LEFT JOIN " . DB_PREFIX . "mpblogcategory_to_store c2s ON (c.mpblogcategory_id = c2s.mpblogcategory_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");

		return $query->row['total'];
	}
	
}
