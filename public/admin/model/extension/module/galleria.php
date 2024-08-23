<?php

class ModelExtensionModuleGalleria extends Model {

	public function install() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "galleria` (
			`galleria_id` INT(11) NOT NULL AUTO_INCREMENT,
			`inpage` INT(1) NOT NULL DEFAULT '1',
			`status` INT(1) NOT NULL DEFAULT '1',
			PRIMARY KEY (`galleria_id`)
		) COLLATE='UTF8_GENERAL_CI'	ENGINE=MyISAM;");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "galleria_description` (
			`galleria_id` INT(11) NOT NULL DEFAULT '0',
			`language_id` INT(11) NOT NULL DEFAULT '0',
			`name` VARCHAR(255) NOT NULL,
			`description` TEXT NOT NULL,
			`meta_title` VARCHAR(255) NOT NULL,
			`meta_description` TEXT NOT NULL,
			`meta_keyword` TEXT NOT NULL,
			PRIMARY KEY (`galleria_id`, `language_id`)
		) COLLATE='UTF8_GENERAL_CI'	ENGINE=MyISAM;");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "galleria_image` (
		   	`galleria_image_id` INT(11) NOT NULL AUTO_INCREMENT,
		   	`galleria_id` INT(11) NOT NULL,
		  	`image` VARCHAR(255) NOT NULL,
		  	`sort_order` INT(3) NOT NULL DEFAULT '0',
		  	PRIMARY KEY (`galleria_image_id`)
		) COLLATE='UTF8_GENERAL_CI' ENGINE=MyISAM;");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "galleria_image_description` (
		   	`galleria_image_id` INT(11) NOT NULL,
		   	`galleria_id` INT(11) NOT NULL,
		  	`language_id` INT(11) NOT NULL,
		  	`name` VARCHAR(255) NOT NULL,
		  	`description` TEXT NOT NULL,
		  	PRIMARY KEY (`galleria_image_id`, `language_id`)
		) COLLATE='UTF8_GENERAL_CI' ENGINE=MyISAM;");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "galleria_to_store` (
			`galleria_id` INT(11) NOT NULL,
			`store_id` INT(11) NOT NULL DEFAULT '0',
			PRIMARY KEY (`galleria_id`, `store_id`)
		) COLLATE='UTF8_GENERAL_CI'	ENGINE=MyISAM;");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "galleria_to_product` (
			`galleria_id` INT(11) NOT NULL,
			`product_id` INT(11) NOT NULL
		) COLLATE='UTF8_GENERAL_CI'	ENGINE=MyISAM;");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "galleria_to_category` (
			`galleria_id` INT(11) NOT NULL,
			`category_id` INT(11) NOT NULL
		) COLLATE='UTF8_GENERAL_CI'	ENGINE=MyISAM;");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "galleria_to_manufacturer` (
			`galleria_id` INT(11) NOT NULL,
			`manufacturer_id` INT(11) NOT NULL
		) COLLATE='UTF8_GENERAL_CI'	ENGINE=MyISAM;");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "galleria_to_information` (
			`galleria_id` INT(11) NOT NULL,
			`information_id` INT(11) NOT NULL
		) COLLATE='UTF8_GENERAL_CI'	ENGINE=MyISAM;");
	}

	public function uninstall() {
		$this->db->query("DROP TABLE `" . DB_PREFIX . "galleria`;");
		$this->db->query("DROP TABLE `" . DB_PREFIX . "galleria_description`;");
		$this->db->query("DROP TABLE `" . DB_PREFIX . "galleria_image`;");
		$this->db->query("DROP TABLE `" . DB_PREFIX . "galleria_image_description`;");
		$this->db->query("DROP TABLE `" . DB_PREFIX . "galleria_to_store`;");
		$this->db->query("DROP TABLE `" . DB_PREFIX . "galleria_to_product`;");
		$this->db->query("DROP TABLE `" . DB_PREFIX . "galleria_to_category`;");
		$this->db->query("DROP TABLE `" . DB_PREFIX . "galleria_to_manufacturer`;");
		$this->db->query("DROP TABLE `" . DB_PREFIX . "galleria_to_information`;");
	}

	public function addGallery($data) {

		$this->db->query("INSERT INTO " . DB_PREFIX . "galleria SET status = '" . (int)$data['status'] . "', inpage = '" . (int)$data['inpage'] . "'");

		$galleria_id = $this->db->getLastId();

		foreach ($data['galleria_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "galleria_description SET 
				galleria_id = '" . (int)$galleria_id . "', 
				language_id = '" . (int)$language_id . "', 
				name = '" . $this->db->escape($value['name']) . "', 
				description = '" . $this->db->escape($value['description']) . "',
				meta_title = '" . $this->db->escape($value['meta_title']) . "', 
				meta_description = '" . $this->db->escape($value['meta_description']) . "', 
				meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		if (isset($data['galleria_seo_url'])) {
			foreach ($data['galleria_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $seo_url) {
					if (trim($seo_url)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'galleria_id=" . (int)$galleria_id . "', keyword = '" . $this->db->escape($seo_url) . "'");
					}
				}
			}
		}

		if (isset($data['album_image'])) {
			foreach ($data['album_image'] as $album_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "galleria_image SET galleria_id = '" . (int)$galleria_id . "', image = '" . $this->db->escape($album_image['image']) . "', sort_order = '" . (int)$album_image['sort_order'] . "'");
				$image_id = $this->db->getLastId();
				if (!empty($album_image['description'])) {
					foreach ($album_image['description'] as $language_id => $image_description) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "galleria_image_description SET galleria_image_id = '" . (int)$image_id . "', galleria_id = '" . (int)$galleria_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($image_description['name']) . "', description = '" . $this->db->escape($image_description['description']) . "'");
					}
				}
				
			}
		}

		if (!empty($data['category'])) {
			foreach ($data['category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "galleria_to_category SET galleria_id = '" . (int)$galleria_id . "', category_id = '" . $category_id . "'");
			}
		}

		if (!empty($data['manufacturer'])) {
			foreach ($data['manufacturer'] as $manufacturer_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "galleria_to_manufacturer SET galleria_id = '" . (int)$galleria_id . "', manufacturer_id = '" . $manufacturer_id . "'");
			}
		}

		if (!empty($data['product'])) {
			foreach ($data['product'] as $product_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "galleria_to_product SET galleria_id = '" . (int)$galleria_id . "', product_id = '" . $product_id . "'");
			}
		}

		if (!empty($data['information'])) {
			foreach ($data['information'] as $information_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "galleria_to_information SET galleria_id = '" . (int)$galleria_id . "', information_id = '" . $information_id . "'");
			}
		}

		if (isset($data['galleria_store'])) {
			foreach ($data['galleria_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "galleria_to_store SET galleria_id = '" . (int)$galleria_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->cache->delete('galleria');

		return $galleria_id;
	}

	public function editGallery($galleria_id, $data) {

		$this->db->query("UPDATE " . DB_PREFIX . "galleria SET status = '" . (int)$data['status'] . "', inpage = '" . (int)$data['inpage'] . "' WHERE galleria_id = '" . (int)$galleria_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "galleria_description WHERE galleria_id = '" . (int)$galleria_id . "'");

		foreach ($data['galleria_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "galleria_description SET 
				galleria_id = '" . (int)$galleria_id . "', 
				language_id = '" . (int)$language_id . "', 
				name = '" . $this->db->escape($value['name']) . "', 
				description = '" . $this->db->escape($value['description']) . "',
				meta_title = '" . $this->db->escape($value['meta_title']) . "', 
				meta_description = '" . $this->db->escape($value['meta_description']) . "', 
				meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'galleria_id=" . (int)$galleria_id . "'");
		
		if (isset($data['galleria_seo_url'])) {
			foreach ($data['galleria_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $seo_url) {
					if (trim($seo_url)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'galleria_id=" . (int)$galleria_id . "', keyword = '" . $this->db->escape($seo_url) . "'");
					}
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "galleria_image WHERE galleria_id = '" . (int)$galleria_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "galleria_image_description WHERE galleria_id = '" . (int)$galleria_id . "'");

		if (isset($data['album_image'])) {
			$data['album_image'] = array_reverse($data['album_image']);
			foreach ($data['album_image'] as $album_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "galleria_image SET galleria_id = '" . (int)$galleria_id . "', image = '" . $this->db->escape($album_image['image']) . "', sort_order = '" . (int)$album_image['sort_order'] . "'");
				$image_id = $this->db->getLastId();
				if (!empty($album_image['description'])) {
					foreach ($album_image['description'] as $language_id => $image_description) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "galleria_image_description SET galleria_image_id = '" . (int)$image_id . "', galleria_id = '" . (int)$galleria_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($image_description['name']) . "', description = '" . $this->db->escape($image_description['description']) . "'");
					}
				}
				
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "galleria_to_category WHERE galleria_id = '" . (int)$galleria_id . "'");
		
		if (!empty($data['category'])) {
			foreach ($data['category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "galleria_to_category SET galleria_id = '" . (int)$galleria_id . "', category_id = '" . $category_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "galleria_to_manufacturer WHERE galleria_id = '" . (int)$galleria_id . "'");

		if (!empty($data['manufacturer'])) {
			foreach ($data['manufacturer'] as $manufacturer_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "galleria_to_manufacturer SET galleria_id = '" . (int)$galleria_id . "', manufacturer_id = '" . $manufacturer_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "galleria_to_product WHERE galleria_id = '" . (int)$galleria_id . "'");

		if (!empty($data['product'])) {
			foreach ($data['product'] as $product_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "galleria_to_product SET galleria_id = '" . (int)$galleria_id . "', product_id = '" . $product_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "galleria_to_information WHERE galleria_id = '" . (int)$galleria_id . "'");

		if (!empty($data['information'])) {
			foreach ($data['information'] as $information_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "galleria_to_information SET galleria_id = '" . (int)$galleria_id . "', information_id = '" . $information_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "galleria_to_store WHERE galleria_id = '" . (int)$galleria_id . "'");

		if (isset($data['galleria_store'])) {
			foreach ($data['galleria_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "galleria_to_store SET galleria_id = '" . (int)$galleria_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->cache->delete('galleria');

	}

	public function deleteGallery($galleria_id) {

		$this->db->query("DELETE FROM " . DB_PREFIX . "galleria WHERE galleria_id = '" . (int)$galleria_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "galleria_description WHERE galleria_id = '" . (int)$galleria_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "galleria_image WHERE galleria_id = '" . (int)$galleria_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "galleria_image_description WHERE galleria_id = '" . (int)$galleria_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'galleria_id=" . (int)$galleria_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "galleria_to_store WHERE galleria_id = '" . (int)$galleria_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "galleria_to_product WHERE galleria_id = '" . (int)$galleria_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "galleria_to_category WHERE galleria_id = '" . (int)$galleria_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "galleria_to_manufacturer WHERE galleria_id = '" . (int)$galleria_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "galleria_to_information WHERE galleria_id = '" . (int)$galleria_id . "'");
		$this->cache->delete('galleria');

	}

	public function getGallery($galleria_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "galleria g 
			 	LEFT JOIN " . DB_PREFIX . "galleria_description gd ON (g.galleria_id = gd.galleria_id) 
			 	WHERE g.galleria_id = '" . (int)$galleria_id . "' AND gd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getGalleries($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "galleria g 
				LEFT JOIN " . DB_PREFIX . "galleria_description gd ON (g.galleria_id = gd.galleria_id)  
				WHERE gd.language_id = '" . (int)$this->config->get('config_language_id')."'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND gd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND g.status = '" . (int)$data['filter_status'] . "'";
		}

		$sql .= " GROUP BY g.galleria_id";

		$sort_data = array(
			'gd.name'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY g.galleria_id";
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

	public function getGalleryDescriptions($galleria_id) {
		$galleria_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "galleria_description WHERE galleria_id = '" . (int)$galleria_id . "'");

		foreach ($query->rows as $result) {
			$galleria_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'  	   => $result['description'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword']
			);
		}

		return $galleria_description_data;
	}

	public function getGalleryStores($galleria_id) {
		$galleria_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "galleria_to_store WHERE galleria_id = '" . (int)$galleria_id . "'");

		foreach ($query->rows as $result) {
			$galleria_store_data[] = $result['store_id'];
		}

		return $galleria_store_data;
	}

	public function getTotalGalleries($data = array()) {
		$sql = "SELECT COUNT(DISTINCT g.galleria_id) AS total FROM " . DB_PREFIX . "galleria g LEFT JOIN " . DB_PREFIX . "galleria_description gd ON (g.galleria_id = gd.galleria_id)";

		$sql .= " WHERE gd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND gd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND g.status = '" . (int)$data['filter_status'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getGallerySeoUrls($galleria_id) {
		$galleria_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'galleria_id=" . (int)$galleria_id . "'");

		foreach ($query->rows as $result) {
			$galleria_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $galleria_seo_url_data;
	}
	public function getGalleryByName($keyword) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "galleria_description WHERE name = '" . $keyword . "'");

		return $query->row;
	}

	public function getAlbumImages($galleria_id) {
		$album_images = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "galleria_image WHERE galleria_id = " . (int)$galleria_id . " ORDER BY sort_order ASC");

		foreach ($query->rows as $result) {
			$album_images[] = array(
				'image' => $result['image'],
				'sort_order' => $result['sort_order'],
				'description' => $this->getAlbumImageDescription($result['galleria_image_id'])
			);
		}

		return $album_images;
	}

	public function getAlbumImageDescription($image_id) {
		$album_image_description = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "galleria_image_description WHERE galleria_image_id = " . (int)$image_id);

		foreach ($query->rows as $result) {
			$album_image_description[$result['language_id']] = array(
				'name' => $result['name'],
				'description' => $result['description']
			);
		}

		return $album_image_description;
	}

	public function getGalleryProducts($galleria_id) {
		$galleria_products = array();

		$query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "galleria_to_product WHERE galleria_id = " . (int)$galleria_id);

		foreach ($query->rows as $result) {
			$galleria_products[] = $result['product_id'];
		}

		return $galleria_products;
	}

	public function getGalleryCategories($galleria_id) {
		$galleria_categories = array();

		$query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "galleria_to_category WHERE galleria_id = " . (int)$galleria_id);

		foreach ($query->rows as $result) {
			$galleria_categories[] = $result['category_id'];
		}

		return $galleria_categories;
	}

	public function getGalleryManufacturers($galleria_id) {
		$galleria_manufacturers = array();

		$query = $this->db->query("SELECT manufacturer_id FROM " . DB_PREFIX . "galleria_to_manufacturer WHERE galleria_id = " . (int)$galleria_id);

		foreach ($query->rows as $result) {
			$galleria_manufacturers[] = $result['manufacturer_id'];
		}

		return $galleria_manufacturers;
	}

	public function getGalleryInformations($galleria_id) {
		$galleria_products = array();

		$query = $this->db->query("SELECT information_id FROM " . DB_PREFIX . "galleria_to_information WHERE galleria_id = " . (int)$galleria_id);

		foreach ($query->rows as $result) {
			$galleria_products[] = $result['information_id'];
		}

		return $galleria_products;
	}

	public function getInformations($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "'";

			if (!empty($data['filter_name'])) {
				$sql .= " AND id.title LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
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
		} else {
			$information_data = $this->cache->get('information.' . (int)$this->config->get('config_language_id'));

			if (!$information_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY id.title");

				$information_data = $query->rows;

				$this->cache->set('information.' . (int)$this->config->get('config_language_id'), $information_data);
			}

			return $information_data;
		}
	}

	public function addGallerySeoUrl($data = array()) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'extension/module/galleria'");
		if (isset($data['module_galleria_page_seo_url'])) {
			foreach ($data['module_galleria_page_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $seo_url) {
					if (trim($seo_url)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'extension/module/galleria', keyword = '" . $this->db->escape($seo_url) . "'");
					}
				}
			}
		}
	}

}
