<?php
/* This file is under Git Control by KDSI. */
class ModelExtensionMpBlogMpBlogPost extends Model {
	public function addMpBlogPost($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogpost SET author = '" . $this->db->escape($data['author']) . "', date_available = '" . $this->db->escape($data['date_available']) . "', video = '" . $this->db->escape($data['video']) . "', posttype = '" . $this->db->escape($data['posttype']) . "', status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_added = NOW()");

		$mpblogpost_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "mpblogpost SET image = '" . $this->db->escape($data['image']) . "' WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "'");
		}

		foreach ($data['mpblogpost_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogpost_description SET mpblogpost_id = '" . (int)$mpblogpost_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', sdescription = '" . $this->db->escape($value['sdescription']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		if (isset($data['mpblogpost_store'])) {
			foreach ($data['mpblogpost_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogpost_to_store SET mpblogpost_id = '" . (int)$mpblogpost_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		
		if (isset($data['mpblogpost_mpblogcategory'])) {
			foreach ($data['mpblogpost_mpblogcategory'] as $mpblogcategory_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogpost_to_mpblogcategory SET mpblogpost_id = '" . (int)$mpblogpost_id . "', mpblogcategory_id = '" . (int)$mpblogcategory_id . "'");
			}
		}

		if (isset($data['mpblogpost_image'])) {
			foreach ($data['mpblogpost_image'] as $mpblogpost_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogpost_image SET mpblogpost_id = '" . (int)$mpblogpost_id . "', image = '" . $this->db->escape($mpblogpost_image['image']) . "', sort_order = '" . (int)$mpblogpost_image['sort_order'] . "'");
			}
		}

		
		if (isset($data['mpblogpost_related'])) {
			foreach ($data['mpblogpost_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost_related WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogpost_related SET mpblogpost_id = '" . (int)$mpblogpost_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost_related WHERE mpblogpost_id = '" . (int)$related_id . "' AND related_id = '" . (int)$mpblogpost_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogpost_related SET mpblogpost_id = '" . (int)$related_id . "', related_id = '" . (int)$mpblogpost_id . "'");
			}
		}

		if (isset($data['mpblogpost_relatedcategory'])) {
			foreach ($data['mpblogpost_relatedcategory'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost_relatedcategory WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogpost_relatedcategory SET mpblogpost_id = '" . (int)$mpblogpost_id . "', related_id = '" . (int)$related_id . "'");
			}
		}

		if (isset($data['mpblogpost_relatedproduct'])) {
			foreach ($data['mpblogpost_relatedproduct'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost_relatedproduct WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogpost_relatedproduct SET mpblogpost_id = '" . (int)$mpblogpost_id . "', related_id = '" . (int)$related_id . "'");
			}
		}
		// SEO URL
		if (isset($data['mpblogpost_seo_url'])) {
			foreach ($data['mpblogpost_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'mpblogpost_id=" . (int)$mpblogpost_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}
		

		$this->emailBlogAdd($mpblogpost_id);
		

		return $mpblogpost_id;
	}

	public function editMpBlogPost($mpblogpost_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "mpblogpost SET author = '" . $this->db->escape($data['author']) . "', date_available = '" . $this->db->escape($data['date_available']) . "', video = '" . $this->db->escape($data['video']) . "', posttype = '" . $this->db->escape($data['posttype']) . "', status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_modified = NOW() WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "mpblogpost SET image = '" . $this->db->escape($data['image']) . "' WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost_description WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "'");

		foreach ($data['mpblogpost_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogpost_description SET mpblogpost_id = '" . (int)$mpblogpost_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', sdescription = '" . $this->db->escape($value['sdescription']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost_to_store WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "'");

		if (isset($data['mpblogpost_store'])) {
			foreach ($data['mpblogpost_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogpost_to_store SET mpblogpost_id = '" . (int)$mpblogpost_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost_to_mpblogcategory WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "'");

		if (isset($data['mpblogpost_mpblogcategory'])) {
			foreach ($data['mpblogpost_mpblogcategory'] as $mpblogcategory_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogpost_to_mpblogcategory SET mpblogpost_id = '" . (int)$mpblogpost_id . "', mpblogcategory_id = '" . (int)$mpblogcategory_id . "'");
			}
		}


		$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost_image WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "'");

		if (isset($data['mpblogpost_image'])) {
			foreach ($data['mpblogpost_image'] as $mpblogpost_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogpost_image SET mpblogpost_id = '" . (int)$mpblogpost_id . "', image = '" . $this->db->escape($mpblogpost_image['image']) . "', sort_order = '" . (int)$mpblogpost_image['sort_order'] . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost_related WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost_related WHERE related_id = '" . (int)$mpblogpost_id . "'");

		if (isset($data['mpblogpost_related'])) {
			foreach ($data['mpblogpost_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost_related WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogpost_related SET mpblogpost_id = '" . (int)$mpblogpost_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost_related WHERE mpblogpost_id = '" . (int)$related_id . "' AND related_id = '" . (int)$mpblogpost_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogpost_related SET mpblogpost_id = '" . (int)$related_id . "', related_id = '" . (int)$mpblogpost_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost_relatedcategory WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "'");
		if (isset($data['mpblogpost_relatedcategory'])) {
			foreach ($data['mpblogpost_relatedcategory'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost_relatedcategory WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogpost_relatedcategory SET mpblogpost_id = '" . (int)$mpblogpost_id . "', related_id = '" . (int)$related_id . "'");
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost_relatedproduct WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "'");
		if (isset($data['mpblogpost_relatedproduct'])) {
			foreach ($data['mpblogpost_relatedproduct'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost_relatedproduct WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogpost_relatedproduct SET mpblogpost_id = '" . (int)$mpblogpost_id . "', related_id = '" . (int)$related_id . "'");
			}
		}

		// SEO URL
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'mpblogpost_id=" . (int)$mpblogpost_id . "'");
		if (isset($data['mpblogpost_seo_url'])) {
			foreach ($data['mpblogpost_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'mpblogpost_id=" . (int)$mpblogpost_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}
		

		$this->emailBlogEdit($mpblogpost_id);
		
	}

	public function deleteMpBlogPost($mpblogpost_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost_description WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "'");
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost_related WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost_related WHERE related_id = '" . (int)$mpblogpost_id . "'");


		$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost_relatedcategory WHERE related_id = '" . (int)$mpblogpost_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost_relatedproduct WHERE related_id = '" . (int)$mpblogpost_id . "'");




		$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost_to_mpblogcategory WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "'");
		

		$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost_to_store WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "'");

		// SEO URL
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'mpblogpost_id=" . (int)$mpblogpost_id . "'");
	}

	public function getMpBlogPostSeoUrls($mpblogpost_id) {
		$seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'mpblogpost_id=" . (int)$mpblogpost_id . "'");

		foreach ($query->rows as $result) {
			$seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $seo_url_data;
	}
	public function getMpBlogPost($mpblogpost_id) {
		/*, (SELECT keyword FROM " . DB_PREFIX . "seo_url WHERE query = 'mpblogpost_id=" . (int)$mpblogpost_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "') AS keyword*/
		$query = $this->db->query("SELECT DISTINCT *, (SELECT COUNT(pc.mpblogcomment_id) AS total FROM " . DB_PREFIX . "mpblogcomment pc WHERE pc.mpblogpost_id=p.mpblogpost_id) AS totalcomments, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "mpblograting r1 WHERE r1.mpblogpost_id = p.mpblogpost_id AND r1.status = '1' GROUP BY r1.mpblogpost_id) AS rating FROM " . DB_PREFIX . "mpblogpost p LEFT JOIN " . DB_PREFIX . "mpblogpost_description pd ON (p.mpblogpost_id = pd.mpblogpost_id) WHERE p.mpblogpost_id = '" . (int)$mpblogpost_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getMpBlogPosts($data = array()) {
		$sql = "SELECT *, (SELECT COUNT(pc.mpblogcomment_id) AS total FROM " . DB_PREFIX . "mpblogcomment pc WHERE pc.mpblogpost_id=p.mpblogpost_id) AS totalcomments, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "mpblograting r1 WHERE r1.mpblogpost_id = p.mpblogpost_id AND r1.status = '1' GROUP BY r1.mpblogpost_id) AS rating FROM " . DB_PREFIX . "mpblogpost p LEFT JOIN " . DB_PREFIX . "mpblogpost_description pd ON (p.mpblogpost_id = pd.mpblogpost_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}
		
		if (!empty($data['filter_author'])) {
			$sql .= " AND p.author LIKE '" . $this->db->escape($data['filter_author']) . "%'";
		}


		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}


		$sql .= " GROUP BY p.mpblogpost_id";

		$sort_data = array(
			'pd.name',
			'p.likes',
			'p.viewed',
			'p.author',
			'p.status',
			'p.sort_order',
			'p.date_added',
			'p.date_available',
			'p.date_modified',
			'totalcomments',
			'rating',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pd.name";
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

	public function getMpBlogPostsByMpBlogCategoryId($mpblogcategory_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpblogpost p LEFT JOIN " . DB_PREFIX . "mpblogpost_description pd ON (p.mpblogpost_id = pd.mpblogpost_id) LEFT JOIN " . DB_PREFIX . "mpblogpost_to_mpblogcategory p2c ON (p.mpblogpost_id = p2c.mpblogpost_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2c.mpblogcategory_id = '" . (int)$mpblogcategory_id . "' ORDER BY pd.name ASC");

		return $query->rows;
	}

	public function getMpBlogPostDescriptions($mpblogpost_id) {
		$mpblogpost_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpblogpost_description WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "'");

		foreach ($query->rows as $result) {
			$mpblogpost_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description'],
				'sdescription'      => $result['sdescription'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'tag'              => $result['tag']
			);
		}

		return $mpblogpost_description_data;
	}

	public function getMpBlogPostImages($mpblogpost_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpblogpost_image WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getMpBlogPostMpBlogCategories($mpblogpost_id) {
		$mpblogpost_mpblogcategory_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpblogpost_to_mpblogcategory WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "'");

		foreach ($query->rows as $result) {
			$mpblogpost_mpblogcategory_data[] = $result['mpblogcategory_id'];
		}

		return $mpblogpost_mpblogcategory_data;
	}

	
	public function getMpBlogPostStores($mpblogpost_id) {
		$mpblogpost_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpblogpost_to_store WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "'");

		foreach ($query->rows as $result) {
			$mpblogpost_store_data[] = $result['store_id'];
		}

		return $mpblogpost_store_data;
	}

	public function getMpBlogPostRelated($mpblogpost_id) {
		$mpblogpost_related_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpblogpost_related WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "'");

		foreach ($query->rows as $result) {
			$mpblogpost_related_data[] = $result['related_id'];
		}

		return $mpblogpost_related_data;
	}

	public function getMpBlogPostRelatedCategories($mpblogpost_id) {
		$mpblogpost_related_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpblogpost_relatedcategory WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "'");

		foreach ($query->rows as $result) {
			$mpblogpost_related_data[] = $result['related_id'];
		}

		return $mpblogpost_related_data;
	}
	
	public function getMpBlogPostRelatedProducts($mpblogpost_id) {
		$mpblogpost_related_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpblogpost_relatedproduct WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "'");

		foreach ($query->rows as $result) {
			$mpblogpost_related_data[] = $result['related_id'];
		}

		return $mpblogpost_related_data;
	}

	public function getTotalMpBlogPosts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.mpblogpost_id) AS total FROM " . DB_PREFIX . "mpblogpost p LEFT JOIN " . DB_PREFIX . "mpblogpost_description pd ON (p.mpblogpost_id = pd.mpblogpost_id)";

		$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}
		
		if (!empty($data['filter_author'])) {
			$sql .= " AND p.author LIKE '" . $this->db->escape($data['filter_author']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_image']) && !is_null($data['filter_image'])) {
			if ($data['filter_image'] == 1) {
				$sql .= " AND (p.image IS NOT NULL AND p.image <> '' AND p.image <> 'no_image.png')";
			} else {
				$sql .= " AND (p.image IS NULL OR p.image = '' OR p.image = 'no_image.png')";
			}
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	
	public function getTotalMpBlogPostsByMpBlogAuthorId($author) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mpblogpost WHERE author = '" . (int)$author . "'");

		return $query->row['total'];
	}
	
	public function emailBlogAdd($mpblogpost_id) {
		$this->load->model('setting/setting');
		$this->load->model('localisation/language');
		$this->load->model('extension/mpblog/mpblogsubscriber');
		$this->load->model('tool/image');

		$blogpost_stores = $this->getMpBlogPostStores($mpblogpost_id);
		$blogpost_info = $this->getMpBlogPost($mpblogpost_id);
		$languages = $this->model_localisation_language->getLanguages();
		
		if($blogpost_info && $blogpost_stores) {

			$blogpost_description = $this->getMpBlogPostDescriptions($blogpost_info['mpblogpost_id']);

			foreach($blogpost_stores as $store_id) {

				$mpblog = $this->model_setting_setting->getSetting('mpblog', $store_id);
				if(!$mpblog) {
					$mpblog = $this->model_setting_setting->getSetting('mpblog', 0);
				}
				$config = $this->model_setting_setting->getSetting('config', $store_id);
				if(!$config) {
					$config = $this->model_setting_setting->getSetting('config', 0);
				}
				$mpblog_emails = $mpblog['mpblog_emails'];
				if($mpblog['mpblog_blogadd_status'] && $mpblog_emails) {

					$url_maker = new Url($config['config_url'], $config['config_ssl']);

					// send subscribe email here
					if (isset($this->request->server['HTTPS'])) {
						$server = $config['config_ssl'];
					} else {
						$server = $config['config_url'];
					}

					if (is_file(DIR_IMAGE . $config['config_logo'])) {
						$logo = $server . 'image/' . $config['config_logo'];
					} elseif (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
						$logo = $server . 'image/' . $this->config->get('config_logo');
					} else {
						$logo = '';
					}

					if (is_file(DIR_IMAGE . $blogpost_info['image'])) {
						$blog_thumb = $this->model_tool_image->resize($blogpost_info['image'], 100, 100);
					} else {
						$blog_thumb = '';
					}



					$subscribers = array();
					foreach ($languages as $language) {
						//get subscribers according to store id
						$subscribers = $this->model_mpblog_mpblogsubscriber->getSubscibers(array(
							'filter_store_id' => $store_id,
							'filter_language_id' => $language['language_id'],
							'filter_status' => 1
						));

						if($subscribers) {

							$email_subject = !empty($mpblog_emails['blogadd'][$language['language_id']]['subject']) ? $mpblog_emails['blogadd'][$language['language_id']]['subject'] : '';

							$email_message = !empty($mpblog_emails['blogadd'][$language['language_id']]['message']) ? $mpblog_emails['blogadd'][$language['language_id']]['message'] : '';

							$find = array(
								'[STORE_NAME]',
								'[STORE_LINK]',
								'[LOGO]',
								'[BLOG_NAME]',
								'[BLOG_THUMB]',
								'[BLOG_DESCRIPTION]',
								'[BLOG_SHORTDESCRIPTION]',
							);
						
							$replace = array(
								'STORE_NAME'					=> $config['config_name'],
								'STORE_LINK'					=> $url_maker->link('common/home', '', true),
								'LOGO'							=> '<a href="'. $url_maker->link('common/home', '', true) .'"><img src="'. $logo .'" alt="'. $config['config_name'] .'" title="'. $config['config_name'] .'" /></a>',
								'BLOG_NAME' => $blogpost_description[$language['language_id']]['name'],
								'BLOG_THUMB' => $blog_thumb ? '<img style="max-width: 100%;" src="'. $blog_thumb .'" alt="'. $blogpost_description[$language['language_id']]['name'] .'" title="'. $blogpost_description[$language['language_id']]['name'] .'" >' : '',
								'BLOG_DESCRIPTION' => $blogpost_description[$language['language_id']]['description'],
								'BLOG_SHORTDESCRIPTION' => $blogpost_description[$language['language_id']]['sdescription'],
								
							);

							if(!empty($email_subject)) {
								$email_subject = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $email_subject))));
							} else {
								$email_subject = '';
							}
							
							if(!empty($email_message)) {
								$email_message = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $email_message))));
							} else {
								$email_message = '';
							}

							$mail = new Mail();
							$mail->protocol = $this->config->get('config_mail_protocol');
							$mail->parameter = $this->config->get('config_mail_parameter');
							$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
							$mail->smtp_username = $this->config->get('config_mail_smtp_username');
							$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
							$mail->smtp_port = $this->config->get('config_mail_smtp_port');
							$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

							$subscriber = (array)array_pop($subscribers);

							$mail->setTo($subscriber['email']);
							$mail->setFrom($this->config->get('config_email'));
							$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
							$mail->setSubject($email_subject);
							$mail->setHtml(html_entity_decode($email_message, ENT_QUOTES, 'UTF-8'));
							$mail->send();

							foreach ($subscribers as $subscriber) {
								if ($subscriber['email'] && filter_var($subscriber['email'], FILTER_VALIDATE_EMAIL)) {
									$mail->setTo($subscriber['email']);
									$mail->send();
								}
							}
						}
					}
				}
			}
		}
	}
	public function emailBlogEdit($mpblogpost_id) {
		$this->load->model('setting/setting');
		$this->load->model('localisation/language');
		$this->load->model('extension/mpblog/mpblogsubscriber');
		$this->load->model('tool/image');
		
		$blogpost_stores = $this->getMpBlogPostStores($mpblogpost_id);
		$blogpost_info = $this->getMpBlogPost($mpblogpost_id);
		$languages = $this->model_localisation_language->getLanguages();
		
		if($blogpost_info && $blogpost_stores) {

			$blogpost_description = $this->getMpBlogPostDescriptions($blogpost_info['mpblogpost_id']);

			foreach($blogpost_stores as $store_id) {

				$mpblog = $this->model_setting_setting->getSetting('mpblog', $store_id);
				if(!$mpblog) {
					$mpblog = $this->model_setting_setting->getSetting('mpblog', 0);
				}
				$config = $this->model_setting_setting->getSetting('config', $store_id);
				if(!$config) {
					$config = $this->model_setting_setting->getSetting('config', 0);
				}
				$mpblog_emails = $mpblog['mpblog_emails'];
				if($mpblog['mpblog_blogadd_status'] && $mpblog_emails) {

					$url_maker = new Url($config['config_url'], $config['config_ssl']);

					// send subscribe email here
					if (isset($this->request->server['HTTPS'])) {
						$server = $config['config_ssl'];
					} else {
						$server = $config['config_url'];
					}

					if (is_file(DIR_IMAGE . $config['config_logo'])) {
						$logo = $server . 'image/' . $config['config_logo'];
					} elseif (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
						$logo = $server . 'image/' . $this->config->get('config_logo');
					} else {
						$logo = '';
					}

					if (is_file(DIR_IMAGE . $blogpost_info['image'])) {
						$blog_thumb = $this->model_tool_image->resize($blogpost_info['image'], 100, 100);
					} else {
						$blog_thumb = '';
					}



					$subscribers = array();
					foreach ($languages as $language) {
						//get subscribers according to store id
						$subscribers = $this->model_mpblog_mpblogsubscriber->getSubscibers(array(
							'filter_store_id' => $store_id,
							'filter_language_id' => $language['language_id'],
							'filter_status' => 1
						));

						if($subscribers) {

							$email_subject = !empty($mpblog_emails['blogedit'][$language['language_id']]['subject']) ? $mpblog_emails['blogedit'][$language['language_id']]['subject'] : '';

							$email_message = !empty($mpblog_emails['blogedit'][$language['language_id']]['message']) ? $mpblog_emails['blogedit'][$language['language_id']]['message'] : '';

							$find = array(
								'[STORE_NAME]',
								'[STORE_LINK]',
								'[LOGO]',
								'[BLOG_NAME]',
								'[BLOG_THUMB]',
								'[BLOG_DESCRIPTION]',
								'[BLOG_SHORTDESCRIPTION]',
							);
						
							$replace = array(
								'STORE_NAME'					=> $config['config_name'],
								'STORE_LINK'					=> $url_maker->link('common/home', '', true),
								'LOGO'							=> '<a href="'. $url_maker->link('common/home', '', true) .'"><img src="'. $logo .'" alt="'. $config['config_name'] .'" title="'. $config['config_name'] .'" /></a>',
								'BLOG_NAME' => $blogpost_description[$language['language_id']]['name'],
								'BLOG_THUMB' => $blog_thumb ? '<img style="max-width: 100%;" src="'. $blog_thumb .'" alt="'. $blogpost_description[$language['language_id']]['name'] .'" title="'. $blogpost_description[$language['language_id']]['name'] .'" >' : '',
								'BLOG_DESCRIPTION' => $blogpost_description[$language['language_id']]['description'],
								'BLOG_SHORTDESCRIPTION' => $blogpost_description[$language['language_id']]['sdescription'],
							);

							if(!empty($email_subject)) {
								$email_subject = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $email_subject))));
							} else {
								$email_subject = '';
							}
							
							if(!empty($email_message)) {
								$email_message = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $email_message))));
							} else {
								$email_message = '';
							}

							$mail = new Mail();
							$mail->protocol = $this->config->get('config_mail_protocol');
							$mail->parameter = $this->config->get('config_mail_parameter');
							$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
							$mail->smtp_username = $this->config->get('config_mail_smtp_username');
							$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
							$mail->smtp_port = $this->config->get('config_mail_smtp_port');
							$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

							$subscriber = (array)array_pop($subscribers);

							$mail->setTo($subscriber['email']);
							$mail->setFrom($this->config->get('config_email'));
							$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
							$mail->setSubject($email_subject);
							$mail->setHtml(html_entity_decode($email_message, ENT_QUOTES, 'UTF-8'));
							$mail->send();

							foreach ($subscribers as $subscriber) {
								if ($subscriber['email'] && filter_var($subscriber['email'], FILTER_VALIDATE_EMAIL)) {
									$mail->setTo($subscriber['email']);
									$mail->send();
								}
							}
						}
					}
				}
			}
		}
	}
	
}
