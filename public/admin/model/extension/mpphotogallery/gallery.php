<?php
class ModelExtensionMpPhotoGalleryGallery extends Model {
	public function addGallery($data) {

		$this->db->query("INSERT INTO `" . DB_PREFIX . "gallery` SET status = '" . $this->db->escape($data['status']) . "', sort_order = '" . (int)$data['sort_order'] . "', width = '" . (int)$data['width'] . "', height = '" . (int)$data['height'] . "', video_width = '" . (int)$data['video_width'] . "', video_height = '" . (int)$data['video_height'] . "', date_added = NOW()");

		$gallery_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "gallery SET image = '" . $this->db->escape($data['image']) . "' WHERE gallery_id = '" . (int)$gallery_id . "'");
		}

		foreach ($data['gallery_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "gallery_description SET gallery_id = '" . (int)$gallery_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "', top_description = '" . $this->db->escape($value['top_description']) . "', bottom_description = '" . $this->db->escape($value['bottom_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "'");
		}

		if (isset($data['gallery_photo'])) {
			foreach ($data['gallery_photo'] as $gallery_photo) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "gallery_photo SET gallery_id = '" . (int)$gallery_id . "', photo = '" . $this->db->escape(html_entity_decode($gallery_photo['photo'],  ENT_QUOTES, 'UTF-8')) . "', link = '" . $this->db->escape($gallery_photo['link']) . "', sort_order = '" . (int)$gallery_photo['sort_order'] . "', highlight = '" . (isset($gallery_photo['highlight']) ? (int)$gallery_photo['highlight'] : '') . "'");

				$gallery_photo_id = $this->db->getLastId();

				foreach ($gallery_photo['gallery_photo_description'] as $language_id => $gallery_photo_description) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "gallery_photo_description SET gallery_photo_id = '" . (int)$gallery_photo_id . "', language_id = '" . (int)$language_id . "', gallery_id = '" . (int)$gallery_id . "', name = '" . $this->db->escape($gallery_photo_description['name']) . "'");
				}
			}
		}
		// gallery for product task starts
		// 07-05-2022: updation task start
		if (isset($data['gallery_products'])) {
			foreach ($data['gallery_products'] as $gallery_product) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "gallery_product SET gallery_id = '" . (int)$gallery_id . "', product_id = '" . (int)$gallery_product['product_id'] . "', video = '" . (int)$gallery_product['video'] . "', image = '" . (int)$gallery_product['image'] . "'");
			}
		}
		// 07-05-2022: updation task end
		// gallery for product task ends

		if (isset($data['gallery_video'])) {
			foreach ($data['gallery_video'] as $gallery_video) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "gallery_video SET gallery_id = '" . (int)$gallery_id . "', video_thumb = '" . $this->db->escape(html_entity_decode($gallery_video['video_thumb'],  ENT_QUOTES, 'UTF-8')) . "', link = '" . $this->db->escape($gallery_video['link']) . "', sort_order = '" . (int)$gallery_video['sort_order'] . "', highlight = '" . (isset($gallery_video['highlight']) ? (int)$gallery_video['highlight'] : '') . "'");

				$gallery_video_id = $this->db->getLastId();

				foreach ($gallery_video['gallery_video_description'] as $language_id => $gallery_video_description) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "gallery_video_description SET gallery_video_id = '" . (int)$gallery_video_id . "', language_id = '" . (int)$language_id . "', gallery_id = '" . (int)$gallery_id . "', name = '" . $this->db->escape($gallery_video_description['name']) . "'");
				}
			}
		}
		// 07-05-2022: updation task start
		if (VERSION >= '3.0.0.0') {

			if (isset($data['gallery_seo_url'])) {
				foreach ($data['gallery_seo_url'] as $store_id => $language) {
					foreach ($language as $language_id => $keyword) {
						if (!empty($keyword)) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'gallery_id=" . (int)$gallery_id . "', keyword = '" . $this->db->escape($keyword) . "'");
						}
					}
				}
			}

		} else {

			if ($data['keyword']) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'gallery_id=" . (int)$gallery_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
			}

		}
		// 07-05-2022: updation task end

		return $gallery_id;
	}

	public function editGallery($gallery_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "gallery` SET  image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "', status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "', width = '" . (int)$data['width'] . "', height = '" . (int)$data['height'] . "', video_width = '" . (int)$data['video_width'] . "', video_height = '" . (int)$data['video_height'] . "', date_modified = NOW() WHERE gallery_id = '" . (int)$gallery_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "gallery_description WHERE gallery_id = '" . (int)$gallery_id . "'");

		foreach ($data['gallery_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "gallery_description SET gallery_id = '" . (int)$gallery_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "', top_description = '" . $this->db->escape($value['top_description']) . "', bottom_description = '" . $this->db->escape($value['bottom_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "gallery_photo WHERE gallery_id = '" . (int)$gallery_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "gallery_photo_description WHERE gallery_id = '" . (int)$gallery_id . "'");

		if (isset($data['gallery_photo'])) {
			foreach ($data['gallery_photo'] as $gallery_photo) {
				if ($gallery_photo['gallery_photo_id']) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "gallery_photo SET gallery_photo_id = '" . (int)$gallery_photo['gallery_photo_id'] . "', gallery_id = '" . (int)$gallery_id . "', photo = '" . $this->db->escape(html_entity_decode($gallery_photo['photo'], ENT_QUOTES, 'UTF-8')) . "', link = '" . $this->db->escape($gallery_photo['link']) . "', sort_order = '" . (int)$gallery_photo['sort_order'] . "', highlight = '" . (isset($gallery_photo['highlight']) ? (int)$gallery_photo['highlight'] : '') . "'");
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "gallery_photo SET gallery_id = '" . (int)$gallery_id . "', photo = '" . $this->db->escape(html_entity_decode($gallery_photo['photo'], ENT_QUOTES, 'UTF-8')) . "', link = '" . $this->db->escape($gallery_photo['link']) . "', sort_order = '" . (int)$gallery_photo['sort_order'] . "', highlight = '" . (isset($gallery_photo['highlight']) ? (int)$gallery_photo['highlight'] : '') . "'");
				}

				$gallery_photo_id = $this->db->getLastId();

				foreach ($gallery_photo['gallery_photo_description'] as $language_id => $gallery_photo_description) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "gallery_photo_description SET gallery_photo_id = '" . (int)$gallery_photo_id . "', language_id = '" . (int)$language_id . "', gallery_id = '" . (int)$gallery_id . "', name = '" . $this->db->escape($gallery_photo_description['name']) . "'");
				}
			}
		}
		// gallery for product task starts
		$this->db->query("DELETE FROM " . DB_PREFIX . "gallery_product WHERE gallery_id = '" . (int)$gallery_id . "'");
		// gallery for product task ends
		$this->db->query("DELETE FROM " . DB_PREFIX . "gallery_video WHERE gallery_id = '" . (int)$gallery_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "gallery_video_description WHERE gallery_id = '" . (int)$gallery_id . "'");

		// gallery for product task starts
		// 07-05-2022: updation task start
		if (isset($data['gallery_products'])) {
			foreach ($data['gallery_products'] as $gallery_product) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "gallery_product SET gallery_id = '" . (int)$gallery_id . "', product_id = '" . (int)$gallery_product['product_id'] . "', video = '" . (int)$gallery_product['video'] . "', image = '" . (int)$gallery_product['image'] . "'");
			}
		}
		// 07-05-2022: updation task end
		// gallery for product task ends

		if (isset($data['gallery_video'])) {
			foreach ($data['gallery_video'] as $gallery_video) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "gallery_video SET gallery_id = '" . (int)$gallery_id . "', video_thumb = '" . $this->db->escape(html_entity_decode($gallery_video['video_thumb'],  ENT_QUOTES, 'UTF-8')) . "', link = '" . $this->db->escape($gallery_video['link']) . "', sort_order = '" . (int)$gallery_video['sort_order'] . "', highlight = '" . (isset($gallery_video['highlight']) ? (int)$gallery_video['highlight'] : '') . "'");

				$gallery_video_id = $this->db->getLastId();

				foreach ($gallery_video['gallery_video_description'] as $language_id => $gallery_video_description) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "gallery_video_description SET gallery_video_id = '" . (int)$gallery_video_id . "', language_id = '" . (int)$language_id . "', gallery_id = '" . (int)$gallery_id . "', name = '" . $this->db->escape($gallery_video_description['name']) . "'");
				}
			}
		}


		// 07-05-2022: updation task start
		if (VERSION >= '3.0.0.0') {

			// SEO URL
			$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'gallery_id=" . (int)$gallery_id . "'");

			if (isset($data['gallery_seo_url'])) {
				foreach ($data['gallery_seo_url']as $store_id => $language) {
					foreach ($language as $language_id => $keyword) {
						if (!empty($keyword)) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'gallery_id=" . (int)$gallery_id . "', keyword = '" . $this->db->escape($keyword) . "'");
						}
					}
				}
			}

		} else {

			$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'gallery_id=" . (int)$gallery_id . "'");

			if ($data['keyword']) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'gallery_id=" . (int)$gallery_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
			}

		}
		// 07-05-2022: updation task end
	}

	public function deleteGallery($gallery_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "gallery` WHERE gallery_id = '" . (int)$gallery_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "gallery_description WHERE gallery_id = '" . (int)$gallery_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "gallery_photo WHERE gallery_id = '" . (int)$gallery_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "gallery_photo_description WHERE gallery_id = '" . (int)$gallery_id . "'");
		// gallery for product task starts
		$this->db->query("DELETE FROM " . DB_PREFIX . "gallery_product WHERE gallery_id = '" . (int)$gallery_id . "'");
		// gallery for product task ends
		$this->db->query("DELETE FROM " . DB_PREFIX . "gallery_video WHERE gallery_id = '" . (int)$gallery_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "gallery_video_description WHERE gallery_id = '" . (int)$gallery_id . "'");

		// 07-05-2022: updation task start
		if (VERSION >= '3.0.0.0') {

			$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'gallery_id=" . (int)$gallery_id . "'");


		} else {

			$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'gallery_id=" . (int)$gallery_id . "'");

		}
		// 07-05-2022: updation task end

	}

	// 07-05-2022: updation task start
	public function getGallerySeoUrls($gallery_id) {
		$gallery_seo_url_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'gallery_id=" . (int)$gallery_id . "'");

		foreach ($query->rows as $result) {
			$gallery_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $gallery_seo_url_data;
	}
	// 07-05-2022: updation task end

	public function getGallery($gallery_id) {
		// 07-05-2022: updation task start
		$sql_keyword = "";
		if (VERSION < '3.0.0.0') {
			$sql_keyword = ", (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'gallery_id=" . (int)$gallery_id . "') AS keyword ";
		}
		// 07-05-2022: updation task end
		$query = $this->db->query("SELECT DISTINCT * {$sql_keyword} FROM `" . DB_PREFIX . "gallery` g LEFT JOIN " . DB_PREFIX . "gallery_description gd ON (g.gallery_id = gd.gallery_id) WHERE g.gallery_id = '" . (int)$gallery_id . "' AND gd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getGallerys($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "gallery` g LEFT JOIN " . DB_PREFIX . "gallery_description gd ON (g.gallery_id = gd.gallery_id) WHERE gd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND gd.title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sort_data = array(
			'gd.title',
			'g.sort_order',
			'g.status',
			'g.viewed',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY gd.title";
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

	public function getGalleryDescriptions($gallery_id) {
		$gallery_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "gallery_description WHERE gallery_id = '" . (int)$gallery_id . "'");

		foreach ($query->rows as $result) {
			$gallery_data[$result['language_id']] = array(
				'title' 			=> $result['title'],
				'description' 		=> $result['description'],
				'top_description' 	=> $result['top_description'],
				'bottom_description'=> $result['bottom_description'],
				'meta_title'		=> $result['meta_title'],
				'meta_description'	=> $result['meta_description'],
				'meta_keyword'		=> $result['meta_keyword'],
			);
		}

		return $gallery_data;
	}

	public function getGalleryPhoto($gallery_photo_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "gallery_photo gp LEFT JOIN " . DB_PREFIX . "gallery_photo_description ghd ON (gp.gallery_photo_id = ghd.gallery_photo_id) WHERE gp.gallery_photo_id = '" . (int)$gallery_photo_id . "' AND ghd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getGalleryPhotos($gallery_id) {
		$gallery_photo_data = array();

		$gallery_photo_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "gallery_photo gp WHERE gp.gallery_id = '" . (int)$gallery_id . "' ORDER BY gp.sort_order");

		foreach ($gallery_photo_query->rows as $gallery_photo) {
			$gallery_photo_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "gallery_photo gp LEFT JOIN " . DB_PREFIX . "gallery_photo_description ghd ON (gp.gallery_photo_id = ghd.gallery_photo_id) WHERE gp.gallery_photo_id = '" . (int)$gallery_photo['gallery_photo_id'] . "'");

			$gallery_photo_description_data = array();
			foreach ($gallery_photo_description_query->rows as $gallery_photo_description) {
				$gallery_photo_description_data[$gallery_photo_description['language_id']] = array(
					'name'            => $gallery_photo_description['name'],
				);
			}

			$gallery_photo_data[] = array(
				'gallery_photo_id' 			=> $gallery_photo['gallery_photo_id'],
				'photo'           			=> $gallery_photo['photo'],
				'highlight'     			=> $gallery_photo['highlight'],
				'sort_order'     			=> $gallery_photo['sort_order'],
				'link'     					=> $gallery_photo['link'],
				'gallery_photo_description'	=> $gallery_photo_description_data,
			);
		}


		return $gallery_photo_data;
	}

	public function getGalleryPhotoDescriptions($gallery_id) {
		$gallery_photo_data = array();

		$gallery_photo_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "gallery_photo WHERE gallery_id = '" . (int)$gallery_id . "' ORDER BY sort_order");

		foreach ($gallery_photo_query->rows as $gallery_photo) {
			$gallery_photo_description_data = array();

			$gallery_photo_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "gallery_photo_description WHERE gallery_photo_id = '" . (int)$gallery_photo['gallery_photo_id'] . "'");

			foreach ($gallery_photo_description_query->rows as $gallery_photo_description) {
				$gallery_photo_description_data[$gallery_photo_description['language_id']] = array('name' => $gallery_photo_description['name']);
			}

			$gallery_photo_data[] = array(
				'gallery_photo_id'          => $gallery_photo['gallery_photo_id'],
				'gallery_photo_description' => $gallery_photo_description_data,
				'image'                    	=> $gallery_photo['image'],
				'highlight'                	=> $gallery_photo['highlight'],
				'sort_order'               	=> $gallery_photo['sort_order']
			);
		}

		return $gallery_photo_data;
	}



	public function getGalleryVideos($gallery_id) {
		$gallery_video_data = array();

		$gallery_video_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "gallery_video gp WHERE gp.gallery_id = '" . (int)$gallery_id . "' ORDER BY gp.sort_order");

		foreach ($gallery_video_query->rows as $gallery_video) {
			$gallery_video_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "gallery_video gp LEFT JOIN " . DB_PREFIX . "gallery_video_description ghd ON (gp.gallery_video_id = ghd.gallery_video_id) WHERE gp.gallery_video_id = '" . (int)$gallery_video['gallery_video_id'] . "'");

			$gallery_video_description_data = array();
			foreach ($gallery_video_description_query->rows as $gallery_video_description) {
				$gallery_video_description_data[$gallery_video_description['language_id']] = array(
					'name'            => $gallery_video_description['name'],
				);
			}

			$gallery_video_data[] = array(
				'gallery_video_id' 			=> $gallery_video['gallery_video_id'],
				'video_thumb'           	=> $gallery_video['video_thumb'],
				'sort_order'     			=> $gallery_video['sort_order'],
				'link'     					=> $gallery_video['link'],
				'highlight'     			=> $gallery_video['highlight'],
				'gallery_video_description'	=> $gallery_video_description_data,
			);
		}

		return $gallery_video_data;
	}

	public function getTotalGallerys() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "gallery`");

		return $query->row['total'];
	}
	// gallery for product task starts
	// 07-05-2022: updation task start
	public function getGalleryProducts($gallery_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "gallery_product WHERE gallery_id = '" . (int)$gallery_id . "'");
		return $query->rows;
	}
	// 07-05-2022: updation task end
	// gallery for product task ends

	public function CreateMPGalleryTable() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "gallery` (`gallery_id` int(11) NOT NULL AUTO_INCREMENT,`image` varchar(255) NOT NULL,`status` tinyint(4) NOT NULL,`sort_order` int(11) NOT NULL,`width` varchar(10) NOT NULL,`height` varchar(10) NOT NULL,`viewed` int(11) NOT NULL,`video_height` VARCHAR(10) NOT NULL,`video_width` VARCHAR(10) NOT NULL,`date_added` datetime NOT NULL,`date_modified` datetime NOT NULL,PRIMARY KEY (`gallery_id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0");


		$this->db->query("ALTER TABLE `" . DB_PREFIX . "gallery` CHANGE `date_added` `date_added` DATETIME NOT NULL");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "gallery_description` (`gallery_id` int(11) NOT NULL,`language_id` int(11) NOT NULL,`title` varchar(255) NOT NULL,`description` text NOT NULL,`top_description` text NOT NULL,`bottom_description` text NOT NULL,`meta_title` varchar(255) NOT NULL,`meta_description` text NOT NULL,`meta_keyword` text NOT NULL, PRIMARY KEY (`gallery_id`,`language_id`), KEY `title` (`title`)) ENGINE=InnoDB DEFAULT CHARSET=utf8");

			// $this->db->query("ALTER TABLE `" . DB_PREFIX . "gallery_description` ADD PRIMARY KEY (`gallery_id`,`language_id`), ADD KEY `title` (`title`)");


		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "gallery_photo` (`gallery_photo_id` int(11) NOT NULL AUTO_INCREMENT,`gallery_id` int(11) NOT NULL,`photo` varchar(255) NOT NULL,`link` varchar(255) NOT NULL,`sort_order` int(11) NOT NULL,`status` tinyint(4) NOT NULL,`highlight` tinyint(4) NOT NULL,PRIMARY KEY (`gallery_photo_id`), KEY `gallery_id` (`gallery_id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0");

		// $this->db->query("ALTER TABLE `" . DB_PREFIX . "gallery_photo` ADD INDEX (`gallery_id`)");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "gallery_photo_description` (`gallery_photo_id` int(11) NOT NULL,`gallery_id` int(11) NOT NULL,`language_id` int(11) NOT NULL,`name` varchar(255) NOT NULL,`description` text NOT NULL, PRIMARY KEY (`gallery_photo_id`,`gallery_id`,`language_id`), KEY `name` (`name`)) ENGINE=InnoDB DEFAULT CHARSET=utf8");

		$this->db->query("ALTER TABLE `" . DB_PREFIX . "gallery_photo_description` CHANGE `description` `description` text NOT NULL");

		// $this->db->query("ALTER TABLE `" . DB_PREFIX . "gallery_photo_description` ADD PRIMARY KEY (`gallery_photo_id`,`gallery_id`,`language_id`), ADD KEY `name` (`name`)");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "gallery_video` (`gallery_video_id` int(11) NOT NULL AUTO_INCREMENT,`gallery_id` int(11) NOT NULL,`video_thumb` varchar(255) NOT NULL,`link` varchar(255) NOT NULL,`highlight` tinyint(4) NOT NULL,`sort_order` int(11) NOT NULL,`status` tinyint(4) NOT NULL,PRIMARY KEY (`gallery_video_id`), KEY `gallery_id` (`gallery_id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0");

		// $this->db->query("ALTER TABLE `" . DB_PREFIX . "gallery_video` ADD INDEX (`gallery_id`)");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "gallery_video_description` (`gallery_video_id` int(11) NOT NULL,`gallery_id` int(11) NOT NULL,`language_id` int(11) NOT NULL,`name` varchar(255) NOT NULL,`description` int(11) NOT NULL, PRIMARY KEY (`gallery_video_id`,`gallery_id`,`language_id`), KEY `name` (`name`)) ENGINE=InnoDB DEFAULT CHARSET=utf8");

		$this->db->query("ALTER TABLE `" . DB_PREFIX . "gallery_video_description` CHANGE `description` `description` text NOT NULL");
		// $this->db->query("ALTER TABLE `" . DB_PREFIX . "gallery_video_description` ADD PRIMARY KEY (`gallery_video_id`,`gallery_id`,`language_id`), ADD KEY `name` (`name`)");

		// gallery for product task starts
		// 07-05-2022: updation task start
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "gallery_product` (`gallery_id` int(11) NOT NULL, `product_id` int(11) NOT NULL, `video` int(1) NOT NULL, `image` int(1) NOT NULL, PRIMARY KEY (`gallery_id`,`product_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8");

		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "gallery_product` WHERE `Field` = 'video'");
		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "gallery_product` ADD `video` int(1) NOT NULL AFTER `product_id`");
		}
		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "gallery_product` WHERE `Field` = 'image'");
		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "gallery_product` ADD `image` int(1) NOT NULL AFTER `product_id`");
			$this->db->query("UPDATE `" . DB_PREFIX . "gallery_product` SET `image`='1'");
		}
		// 07-05-2022: updation task end
		// gallery for product task ends


		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "gallery` WHERE `Field` = 'video_height'");
		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "gallery` ADD `video_height` VARCHAR(10) NOT NULL AFTER `viewed`");
		}
		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "gallery` WHERE `Field` = 'video_width'");
		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "gallery` ADD `video_width` VARCHAR(10) NOT NULL AFTER `viewed`");
		}

	}
}