<?php
/* This file is under Git Control by KDSI. */
class ModelModulePointsMpplan extends Model {
	public function addMpplan($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "mpplan SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', price = '" . (float)$data['price'] . "', duration_type = '" . $this->db->escape($data['duration_type']) . "', duration_value = '" . (int)$data['duration_value'] . "', first_bgcolor = '" . $this->db->escape($data['first_bgcolor']) . "', first_textcolor = '" . $this->db->escape($data['first_textcolor']) . "', second_bgcolor = '" . $this->db->escape($data['second_bgcolor']) . "', second_textcolor = '" . $this->db->escape($data['second_textcolor']) . "', date_added = NOW()");

		$mpplan_id = $this->db->getLastId();

		foreach ($data['mpplan_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mpplan_description SET mpplan_id = '" . (int)$mpplan_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', top_description = '" . $this->db->escape($value['top_description']) . "', bottom_description = '" . $this->db->escape($value['bottom_description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		if (isset($data['mpplan_store'])) {
			foreach ($data['mpplan_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mpplan_to_store SET mpplan_id = '" . (int)$mpplan_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['mpplan_product'])) {
			foreach ($data['mpplan_product'] as $product_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mpplan_to_product SET mpplan_id = '" . (int)$mpplan_id . "', product_id = '" . (int)$product_id . "'");
			}
		}

		if (isset($data['mpplan_discount'])) {
			foreach ($data['mpplan_discount'] as $customer_group_id => $mpplan_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mpplan_discount SET mpplan_id = '" . (int)$mpplan_id . "', customer_group_id = '" . (int)$customer_group_id . "', discount = '" . (int)$mpplan_discount['discount'] . "'");
			}
		}

		if (isset($data['mpplan_info'])) {
			foreach ($data['mpplan_info'] as $mpplan_info) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mpplan_info SET mpplan_id = '" . (int)$mpplan_id . "', sort_order = '" . $this->db->escape($mpplan_info['sort_order']) . "'");

				$mpplan_info_id = $this->db->getLastId();

				foreach ($mpplan_info['mpplan_info_description'] as $language_id => $mpplan_info_description) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "mpplan_info_description SET mpplan_id = '" . (int)$mpplan_id . "', mpplan_info_id = '" . (int)$mpplan_info_id . "', language_id = '" . (int)$language_id . "', name = '" .  $this->db->escape($mpplan_info_description['name']) . "'");
				}
			}
		}

		if (isset($data['mpplan_seo_url'])) {
			foreach ($data['mpplan_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'mpplan_id=" . (int)$mpplan_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}

		return $mpplan_id;
	}

	public function editMpplan($mpplan_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "mpplan SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', price = '" . (float)$data['price'] . "', duration_type = '" . $this->db->escape($data['duration_type']) . "', duration_value = '" . (int)$data['duration_value'] . "', first_bgcolor = '" . $this->db->escape($data['first_bgcolor']) . "', first_textcolor = '" . $this->db->escape($data['first_textcolor']) . "', second_bgcolor = '" . $this->db->escape($data['second_bgcolor']) . "', second_textcolor = '" . $this->db->escape($data['second_textcolor']) . "' WHERE mpplan_id = '" . (int)$mpplan_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "mpplan_description WHERE mpplan_id = '" . (int)$mpplan_id . "'");

		foreach ($data['mpplan_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mpplan_description SET mpplan_id = '" . (int)$mpplan_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', top_description = '" . $this->db->escape($value['top_description']) . "', bottom_description = '" . $this->db->escape($value['bottom_description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "mpplan_to_store WHERE mpplan_id = '" . (int)$mpplan_id . "'");

		if (isset($data['mpplan_store'])) {
			foreach ($data['mpplan_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mpplan_to_store SET mpplan_id = '" . (int)$mpplan_id . "', store_id = '" . (int)$store_id . "'");
			}
		}


		$this->db->query("DELETE FROM " . DB_PREFIX . "mpplan_to_product WHERE mpplan_id = '" . (int)$mpplan_id . "'");

		if (isset($data['mpplan_product'])) {
			foreach ($data['mpplan_product'] as $product_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mpplan_to_product SET mpplan_id = '" . (int)$mpplan_id . "', product_id = '" . (int)$product_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "mpplan_discount WHERE mpplan_id = '" . (int)$mpplan_id . "'");
		if (isset($data['mpplan_discount'])) {
			foreach ($data['mpplan_discount'] as $customer_group_id => $mpplan_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mpplan_discount SET mpplan_id = '" . (int)$mpplan_id . "', customer_group_id = '" . (int)$customer_group_id . "', discount = '" . (int)$mpplan_discount['discount'] . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "mpplan_info WHERE mpplan_id = '" . (int)$mpplan_id . "'");		
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpplan_info_description WHERE mpplan_id = '" . (int)$mpplan_id . "'");

		if (isset($data['mpplan_info'])) {
			foreach ($data['mpplan_info'] as $mpplan_info) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mpplan_info SET mpplan_id = '" . (int)$mpplan_id . "', sort_order = '" . $this->db->escape($mpplan_info['sort_order']) . "'");

				$mpplan_info_id = $this->db->getLastId();

				foreach ($mpplan_info['mpplan_info_description'] as $language_id => $mpplan_info_description) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "mpplan_info_description SET mpplan_id = '" . (int)$mpplan_id . "', mpplan_info_id = '" . (int)$mpplan_info_id . "', language_id = '" . (int)$language_id . "', name = '" .  $this->db->escape($mpplan_info_description['name']) . "'");
				}
			}
		}

		// SEO URL
		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'mpplan_id=" . (int)$mpplan_id . "'");

		if (isset($data['mpplan_seo_url'])) {
			foreach ($data['mpplan_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'mpplan_id=" . (int)$mpplan_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}

	}

	public function deleteMpplan($mpplan_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpplan WHERE mpplan_id = '" . (int)$mpplan_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpplan_description WHERE mpplan_id = '" . (int)$mpplan_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpplan_to_store WHERE mpplan_id = '" . (int)$mpplan_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpplan_info WHERE mpplan_id = '" . (int)$mpplan_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpplan_info_description WHERE mpplan_id = '" . (int)$mpplan_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpplan_to_product WHERE mpplan_id = '" . (int)$mpplan_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpplan_discount WHERE mpplan_id = '" . (int)$mpplan_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'mpplan_id=" . (int)$mpplan_id . "'");

		
	}

	public function getMpplan($mpplan_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "mpplan WHERE mpplan_id = '" . (int)$mpplan_id . "'");

		return $query->row;
	}

	public function getMpplanSeoUrls($mpplan_id) {
		$mpplan_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'mpplan_id=" . (int)$mpplan_id . "'");

		foreach ($query->rows as $result) {
			$mpplan_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $mpplan_seo_url_data;
	}

	public function getMpplans($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "mpplan mp LEFT JOIN " . DB_PREFIX . "mpplan_description mpd ON (mp.mpplan_id = mpd.mpplan_id) WHERE mpd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		$sort_data = array(
			'mpd.name',
			'mp.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY mpd.name";
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

	public function getMpplanDescriptions($mpplan_id) {
		$mpplan_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpplan_description WHERE mpplan_id = '" . (int)$mpplan_id . "'");

		foreach ($query->rows as $result) {
			$mpplan_description_data[$result['language_id']] = array(
				'name'            		=> $result['name'],
				'top_description' 		=> $result['top_description'],
				'bottom_description' 	=> $result['bottom_description'],
				'meta_title'       		=> $result['meta_title'],
				'meta_description' 		=> $result['meta_description'],
				'meta_keyword'     		=> $result['meta_keyword']
			);
		}

		return $mpplan_description_data;
	}

	public function getMpplanStores($mpplan_id) {
		$mpplan_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpplan_to_store WHERE mpplan_id = '" . (int)$mpplan_id . "'");

		foreach ($query->rows as $result) {
			$mpplan_store_data[] = $result['store_id'];
		}

		return $mpplan_store_data;
	}

	public function getMpplanProducts($mpplan_id) {
		$mpplan_product_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpplan_to_product WHERE mpplan_id = '" . (int)$mpplan_id . "'");

		foreach ($query->rows as $result) {
			$mpplan_product_data[] = $result['product_id'];
		}

		return $mpplan_product_data;
	}

	public function getMpplanInfos($mpplan_id) {
		$mpplan_info_data = array();

		$mpplan_info_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpplan_info WHERE mpplan_id = '" . (int)$mpplan_id . "'");

		foreach ($mpplan_info_query->rows as $mpplan_info) {
			$mpplan_info_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpplan_info_description WHERE mpplan_id = '" . (int)$mpplan_id . "'AND mpplan_info_id = '". (int)$mpplan_info['mpplan_info_id'] ."'");

			$mpplan_info_description_data = array();
			foreach ($mpplan_info_description_query->rows as $mpplan_info_description) {
					$mpplan_info_description_data[$mpplan_info_description['language_id']] = array(
					'name' => $mpplan_info_description['name']);
			}
				
			$mpplan_info_data[] = array(
				'sort_order'                  	=> $mpplan_info['sort_order'],
				'mpplan_info_description' 		=> $mpplan_info_description_data
			);
		}
		
		return $mpplan_info_data;
	}

	public function getMpplanDiscounts($mpplan_id) {
		$mpplan_discount_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpplan_discount WHERE mpplan_id = '" . (int)$mpplan_id . "'");

		foreach ($query->rows as $result) {
			$mpplan_discount_data[$result['customer_group_id']] = array(
				'discount'		=> $result['discount'],
			);
		}

		return $mpplan_discount_data;
	}

	public function getTotalMpplans() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mpplan");

		return $query->row['total'];
	}

	public function CreateMPPlanTable() {
		$query = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."mpplan'");
		if(!$query->num_rows) {
			$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mpplan` (`mpplan_id` int(11) NOT NULL AUTO_INCREMENT,`duration_type` varchar(10) NOT NULL COMMENT 'd=Days,m=months,y=years',`duration_value` int(11) NOT NULL,`price` decimal(10,4) NOT NULL,`sort_order` int(11) NOT NULL,`status` tinyint(4) NOT NULL,`first_bgcolor` varchar(100) NOT NULL,`first_textcolor` varchar(100) NOT NULL,`second_bgcolor` varchar(100) NOT NULL,`second_textcolor` varchar(100) NOT NULL,`date_added` datetime NOT NULL,PRIMARY KEY (`mpplan_id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0");
			$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mpplan_customer` (`mpplan_customer_id` int(11) NOT NULL AUTO_INCREMENT,`customer_id` int(11) NOT NULL,`order_id` int(11) NOT NULL,`order_product_id` int(11) NOT NULL,`mpplan_id` int(11) NOT NULL,`plan_name` varchar(255) NOT NULL,`plan_info` text NOT NULL,`start_date` datetime NOT NULL,`end_date` datetime NOT NULL,`duration_type` varchar(10) NOT NULL,`duration_value` int(11) NOT NULL,`active` tinyint(4) NOT NULL,PRIMARY KEY (`mpplan_customer_id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0");
			$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mpplan_description` (`mpplan_id` int(11) NOT NULL,`language_id` int(11) NOT NULL,`name` varchar(255) NOT NULL,`top_description` text NOT NULL,`bottom_description` text NOT NULL,`meta_title` varchar(255) NOT NULL,`meta_description` varchar(255) NOT NULL,`meta_keyword` varchar(255) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8");
			$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mpplan_discount` (`mpplan_id` int(11) NOT NULL,`customer_group_id` int(11) NOT NULL,`discount` int(11) NOT NULL COMMENT 'percent (%)') ENGINE=InnoDB DEFAULT CHARSET=utf8");
			$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mpplan_info` (`mpplan_info_id` int(11) NOT NULL AUTO_INCREMENT,`mpplan_id` int(11) NOT NULL,`sort_order` int(11) NOT NULL,PRIMARY KEY (`mpplan_info_id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0");
			$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mpplan_info_description` (`mpplan_info_description_id` int(11) NOT NULL AUTO_INCREMENT,`mpplan_info_id` int(11) NOT NULL,`mpplan_id` int(11) NOT NULL,`language_id` int(11) NOT NULL,`name` varchar(255) NOT NULL,PRIMARY KEY (`mpplan_info_description_id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0");
			$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mpplan_to_product` (`mpplan_id` int(11) NOT NULL,`product_id` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8");
			$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mpplan_to_store` (`mpplan_id` int(11) NOT NULL,`store_id` int(11) NOT NULL DEFAULT '0',PRIMARY KEY (`mpplan_id`,`store_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8");
		}
	}
}