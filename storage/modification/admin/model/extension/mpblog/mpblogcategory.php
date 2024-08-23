<?php
/* This file is under Git Control by KDSI. */
class ModelExtensionMpBlogMpBlogCategory extends Model {
	public function addMpBlogCategory($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogcategory SET parent_id = '" . (int)$data['parent_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', date_modified = NOW(), date_added = NOW()");

		$mpblogcategory_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "mpblogcategory SET image = '" . $this->db->escape($data['image']) . "' WHERE mpblogcategory_id = '" . (int)$mpblogcategory_id . "'");
		}

		foreach ($data['mpblogcategory_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogcategory_description SET mpblogcategory_id = '" . (int)$mpblogcategory_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		// MySQL Hierarchical Data Closure Table Pattern
		$level = 0;

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mpblogcategory_path` WHERE mpblogcategory_id = '" . (int)$data['parent_id'] . "' ORDER BY `level` ASC");

		foreach ($query->rows as $result) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "mpblogcategory_path` SET `mpblogcategory_id` = '" . (int)$mpblogcategory_id . "', `path_id` = '" . (int)$result['path_id'] . "', `level` = '" . (int)$level . "'");

			$level++;
		}

		$this->db->query("INSERT INTO `" . DB_PREFIX . "mpblogcategory_path` SET `mpblogcategory_id` = '" . (int)$mpblogcategory_id . "', `path_id` = '" . (int)$mpblogcategory_id . "', `level` = '" . (int)$level . "'");

		
		if (isset($data['mpblogcategory_store'])) {
			foreach ($data['mpblogcategory_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogcategory_to_store SET mpblogcategory_id = '" . (int)$mpblogcategory_id . "', store_id = '" . (int)$store_id . "'");
			}
		}


		// SEO URL
		if (isset($data['mpblogcategory_seo_url'])) {
			foreach ($data['mpblogcategory_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'mpblogcategory_id=" . (int)$mpblogcategory_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}

		return $mpblogcategory_id;

	}

	public function editMpBlogCategory($mpblogcategory_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "mpblogcategory SET parent_id = '" . (int)$data['parent_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', date_modified = NOW() WHERE mpblogcategory_id = '" . (int)$mpblogcategory_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "mpblogcategory SET image = '" . $this->db->escape($data['image']) . "' WHERE mpblogcategory_id = '" . (int)$mpblogcategory_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogcategory_description WHERE mpblogcategory_id = '" . (int)$mpblogcategory_id . "'");

		foreach ($data['mpblogcategory_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogcategory_description SET mpblogcategory_id = '" . (int)$mpblogcategory_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		// MySQL Hierarchical Data Closure Table Pattern
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mpblogcategory_path` WHERE path_id = '" . (int)$mpblogcategory_id . "' ORDER BY level ASC");

		if ($query->rows) {
			foreach ($query->rows as $mpblogcategory_path) {
				// Delete the path below the current one
				$this->db->query("DELETE FROM `" . DB_PREFIX . "mpblogcategory_path` WHERE mpblogcategory_id = '" . (int)$mpblogcategory_path['mpblogcategory_id'] . "' AND level < '" . (int)$mpblogcategory_path['level'] . "'");

				$path = array();

				// Get the nodes new parents
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mpblogcategory_path` WHERE mpblogcategory_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Get whats left of the nodes current path
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mpblogcategory_path` WHERE mpblogcategory_id = '" . (int)$mpblogcategory_path['mpblogcategory_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Combine the paths with a new level
				$level = 0;

				foreach ($path as $path_id) {
					$this->db->query("REPLACE INTO `" . DB_PREFIX . "mpblogcategory_path` SET mpblogcategory_id = '" . (int)$mpblogcategory_path['mpblogcategory_id'] . "', `path_id` = '" . (int)$path_id . "', level = '" . (int)$level . "'");

					$level++;
				}
			}
		} else {
			// Delete the path below the current one
			$this->db->query("DELETE FROM `" . DB_PREFIX . "mpblogcategory_path` WHERE mpblogcategory_id = '" . (int)$mpblogcategory_id . "'");

			// Fix for records with no paths
			$level = 0;

			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mpblogcategory_path` WHERE mpblogcategory_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

			foreach ($query->rows as $result) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "mpblogcategory_path` SET mpblogcategory_id = '" . (int)$mpblogcategory_id . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

				$level++;
			}

			$this->db->query("REPLACE INTO `" . DB_PREFIX . "mpblogcategory_path` SET mpblogcategory_id = '" . (int)$mpblogcategory_id . "', `path_id` = '" . (int)$mpblogcategory_id . "', level = '" . (int)$level . "'");
		}


		$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogcategory_to_store WHERE mpblogcategory_id = '" . (int)$mpblogcategory_id . "'");

		if (isset($data['mpblogcategory_store'])) {
			foreach ($data['mpblogcategory_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogcategory_to_store SET mpblogcategory_id = '" . (int)$mpblogcategory_id . "', store_id = '" . (int)$store_id . "'");
			}
		}


		// SEO URL
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'mpblogcategory_id=" . (int)$mpblogcategory_id . "'");
		
		if (isset($data['mpblogcategory_seo_url'])) {
			foreach ($data['mpblogcategory_seo_url']as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'mpblogcategory_id=" . (int)$mpblogcategory_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}

	}

	public function deleteMpBlogCategory($mpblogcategory_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogcategory_path WHERE mpblogcategory_id = '" . (int)$mpblogcategory_id . "'");

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpblogcategory_path WHERE path_id = '" . (int)$mpblogcategory_id . "'");

		foreach ($query->rows as $result) {
			$this->deleteMpBlogCategory($result['mpblogcategory_id']);
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogcategory WHERE mpblogcategory_id = '" . (int)$mpblogcategory_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogcategory_description WHERE mpblogcategory_id = '" . (int)$mpblogcategory_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogcategory_to_store WHERE mpblogcategory_id = '" . (int)$mpblogcategory_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost_to_mpblogcategory WHERE mpblogcategory_id = '" . (int)$mpblogcategory_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'mpblogcategory_id=" . (int)$mpblogcategory_id . "'");
	}

	public function getMpblogSeoUrls($mpblogcategory_id) {
		$mpblogcategory_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'mpblogcategory_id=" . (int)$mpblogcategory_id . "'");

		foreach ($query->rows as $result) {
			$mpblogcategory_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $mpblogcategory_seo_url_data;
	}

	public function repairMpBlogCategories($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpblogcategory WHERE parent_id = '" . (int)$parent_id . "'");

		foreach ($query->rows as $mpblogcategory) {
			// Delete the path below the current one
			$this->db->query("DELETE FROM `" . DB_PREFIX . "mpblogcategory_path` WHERE mpblogcategory_id = '" . (int)$mpblogcategory['mpblogcategory_id'] . "'");

			// Fix for records with no paths
			$level = 0;

			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mpblogcategory_path` WHERE mpblogcategory_id = '" . (int)$parent_id . "' ORDER BY level ASC");

			foreach ($query->rows as $result) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "mpblogcategory_path` SET mpblogcategory_id = '" . (int)$mpblogcategory['mpblogcategory_id'] . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

				$level++;
			}

			$this->db->query("REPLACE INTO `" . DB_PREFIX . "mpblogcategory_path` SET mpblogcategory_id = '" . (int)$mpblogcategory['mpblogcategory_id'] . "', `path_id` = '" . (int)$mpblogcategory['mpblogcategory_id'] . "', level = '" . (int)$level . "'");

			$this->repairMpBlogCategories($mpblogcategory['mpblogcategory_id']);
		}
	}

	public function getMpBlogCategory($mpblogcategory_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT GROUP_CONCAT(cd1.name ORDER BY level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') FROM " . DB_PREFIX . "mpblogcategory_path cp LEFT JOIN " . DB_PREFIX . "mpblogcategory_description cd1 ON (cp.path_id = cd1.mpblogcategory_id AND cp.mpblogcategory_id != cp.path_id) WHERE cp.mpblogcategory_id = c.mpblogcategory_id AND cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY cp.mpblogcategory_id) AS path FROM " . DB_PREFIX . "mpblogcategory c LEFT JOIN " . DB_PREFIX . "mpblogcategory_description cd2 ON (c.mpblogcategory_id = cd2.mpblogcategory_id) WHERE c.mpblogcategory_id = '" . (int)$mpblogcategory_id . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getMpBlogCategories($data = array()) {
		$sql = "SELECT cp.mpblogcategory_id AS mpblogcategory_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, c1.parent_id, c1.sort_order, c1.status FROM " . DB_PREFIX . "mpblogcategory_path cp LEFT JOIN " . DB_PREFIX . "mpblogcategory c1 ON (cp.mpblogcategory_id = c1.mpblogcategory_id) LEFT JOIN " . DB_PREFIX . "mpblogcategory c2 ON (cp.path_id = c2.mpblogcategory_id) LEFT JOIN " . DB_PREFIX . "mpblogcategory_description cd1 ON (cp.path_id = cd1.mpblogcategory_id) LEFT JOIN " . DB_PREFIX . "mpblogcategory_description cd2 ON (cp.mpblogcategory_id = cd2.mpblogcategory_id) WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$names = array_map("trim", explode(">",  preg_replace('%\s+%u'," ",$data['filter_name'])));
			$implode = array();
			if(!empty($names)) {
				foreach($names as $name) {
				$implode[] = " cd2.name LIKE '%" . $this->db->escape($name) . "%'"	;
				}
				$sql .= " AND (". implode(" OR ", $implode) .") ";
			}
			
		}

		/*if(!empty($data['filter_nameid'])) {
			$sql .= " AND c1.mpblogcategory_id='" . $this->db->escape($data['filter_nameid']) . "'";
		}*/

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND c1.status='" . $this->db->escape($data['filter_status']) . "'";
		}

		$sql .= " GROUP BY cp.mpblogcategory_id";

		$sort_data = array(
			'name',
			'sort_order',
			'status',
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

	public function getMpBlogCategoryDescriptions($mpblogcategory_id) {
		$mpblogcategory_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpblogcategory_description WHERE mpblogcategory_id = '" . (int)$mpblogcategory_id . "'");

		foreach ($query->rows as $result) {
			$mpblogcategory_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'description'      => $result['description']
			);
		}

		return $mpblogcategory_description_data;
	}
	
	public function getMpBlogCategoryPath($mpblogcategory_id) {
		$query = $this->db->query("SELECT mpblogcategory_id, path_id, level FROM " . DB_PREFIX . "mpblogcategory_path WHERE mpblogcategory_id = '" . (int)$mpblogcategory_id . "'");

		return $query->rows;
	}
	
	public function getMpBlogCategoryStores($mpblogcategory_id) {
		$mpblogcategory_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpblogcategory_to_store WHERE mpblogcategory_id = '" . (int)$mpblogcategory_id . "'");

		foreach ($query->rows as $result) {
			$mpblogcategory_store_data[] = $result['store_id'];
		}

		return $mpblogcategory_store_data;
	}


	public function getTotalMpBlogCategories($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mpblogcategory c1 LEFT JOIN " . DB_PREFIX . "mpblogcategory_description cd2 ON (c1.mpblogcategory_id = cd2.mpblogcategory_id) WHERE cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$names = array_map("trim", explode(">",  preg_replace('%\s+%u'," ",$data['filter_name'])));
			$implode = array();
			if(!empty($names)) {
				foreach($names as $name) {
				$implode[] = " cd2.name LIKE '%" . $this->db->escape($name) . "%'"	;
				}
				$sql .= " AND (". implode(" OR ", $implode) .") ";
			}
			
		}


		/*if(!empty($data['filter_nameid'])) {
			$sql .= " AND c1.mpblogcategory_id='" . $this->db->escape($data['filter_nameid']) . "'";
		}
*/
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND c1.status='" . $this->db->escape($data['filter_status']) . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

}
