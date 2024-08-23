<?php
/* This file is under Git Control by KDSI. */
use \MpBlog\ModelCatalog as Model;
class ModelExtensionMpBlogMpBlogPost extends Model {
	
	public function updateViewed($mpblogpost_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "mpblogpost SET viewed = (viewed + 1) WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "'");
	}
	public function getMpBlogPost($mpblogpost_id) {
		$query = $this->db->query("SELECT DISTINCT *, pd.name AS name, p.image, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "mpblograting r1 WHERE r1.mpblogpost_id = p.mpblogpost_id AND r1.status = '1' GROUP BY r1.mpblogpost_id) AS rating, (SELECT COUNT(c1.mpblogcomment_id) AS total FROM " . DB_PREFIX . "mpblogcomment c1 WHERE c1.mpblogpost_id = p.mpblogpost_id AND c1.status = '1') AS comments, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mpblogcomment r2 WHERE r2.mpblogpost_id = p.mpblogpost_id AND r2.status = '1' GROUP BY r2.mpblogpost_id) AS mpblogcomments, p.sort_order FROM " . DB_PREFIX . "mpblogpost p LEFT JOIN " . DB_PREFIX . "mpblogpost_description pd ON (p.mpblogpost_id = pd.mpblogpost_id) LEFT JOIN " . DB_PREFIX . "mpblogpost_to_store p2s ON (p.mpblogpost_id = p2s.mpblogpost_id) WHERE p.mpblogpost_id = '" . (int)$mpblogpost_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");
		if ($query->num_rows) {
			return [
				'mpblogpost_id'       => $query->row['mpblogpost_id'],
				'name'             => $query->row['name'],
				'description'      => $query->row['description'],
				'sdescription'      => $query->row['sdescription'],
				'meta_title'       => $query->row['meta_title'],
				'meta_description' => $query->row['meta_description'],
				'meta_keyword'     => $query->row['meta_keyword'],
				'tag'              => $query->row['tag'],
				'image'            => $query->row['image'],
				'rating'           => round($query->row['rating']),
				'mpblogcomments'          => $query->row['mpblogcomments'] ? $query->row['mpblogcomments'] : 0,
				
				'sort_order'       => $query->row['sort_order'],
				'status'           => $query->row['status'],
				'date_added'       => $query->row['date_added'],
				'date_modified'    => $query->row['date_modified'],
				'date_available'           => $query->row['date_available'],
				'viewed'           => $query->row['viewed'],
				'comments'           => $query->row['comments'],
				'likes'           => $query->row['likes'],
				'author'           => $query->row['author'],
				'posttype'           => $query->row['posttype'],
				'video'           => $query->row['video'],
			];
		} else {
			return false;
		}
	}
	public function getMpBlogPosts($data = []) {
		$sql = "SELECT p.mpblogpost_id, (SELECT AVG(r1.rating) AS total FROM " . DB_PREFIX . "mpblograting r1 WHERE r1.mpblogpost_id = p.mpblogpost_id AND r1.status = '1' GROUP BY r1.mpblogpost_id) AS rating, (SELECT COUNT(c1.mpblogcomment_id) AS total FROM " . DB_PREFIX . "mpblogcomment c1 WHERE c1.mpblogpost_id = p.mpblogpost_id AND c1.status = '1') AS comments  ";
		if (!empty($data['filter_mpblogcategory_id'])) {
			if (!empty($data['filter_sub_mpblogcategory'])) {
				$sql .= " FROM " . DB_PREFIX . "mpblogcategory_path cp LEFT JOIN " . DB_PREFIX . "mpblogpost_to_mpblogcategory p2c ON (cp.mpblogcategory_id = p2c.mpblogcategory_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "mpblogpost_to_mpblogcategory p2c";
			}
			$sql .= " LEFT JOIN " . DB_PREFIX . "mpblogpost p ON (p2c.mpblogpost_id = p.mpblogpost_id)";
			
		} else {
			$sql .= " FROM " . DB_PREFIX . "mpblogpost p";
		}
		$sql .= " LEFT JOIN " . DB_PREFIX . "mpblogpost_description pd ON (p.mpblogpost_id = pd.mpblogpost_id) LEFT JOIN " . DB_PREFIX . "mpblogpost_to_store p2s ON (p.mpblogpost_id = p2s.mpblogpost_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
		if (!empty($data['filter_mpblogcategory_id'])) {
			if (!empty($data['filter_sub_mpblogcategory'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_mpblogcategory_id'] . "'";
			} else {
				$sql .= " AND p2c.mpblogcategory_id = '" . (int)$data['filter_mpblogcategory_id'] . "'";
			}
		}
		if (!empty($data['filter_name']) || !empty($data['filter_tag']) || !empty($data['filter_author'])) {
			$sql .= " AND (";
			if (!empty($data['filter_name'])) {
				$implode = [];
				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));
				foreach ($words as $word) {
					$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
				}
				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
				if (!empty($data['filter_description'])) {
					$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}
			$or = false;
			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
				$or = true;
			}
			if (!empty($data['filter_tag'])) {
				$implode = [];
				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));
				foreach ($words as $word) {
					$implode[] = "pd.tag LIKE '%" . $this->db->escape($word) . "%'";
				}
				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}
			if ($or) {
				$sql .= " OR ";
			}
			if (!empty($data['filter_author'])) {
				$implode = [];
				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_author'])));
				foreach ($words as $word) {
					$implode[] = "p.author LIKE '%" . $this->db->escape($word) . "%'";
				}
				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}
			$sql .= ")";
		}
		if (!empty($data['filter_date'])) {
			$sql .= " AND ( p.date_available = '". date('Y-m-d', strtotime($data['filter_date'])) ."' AND p.date_available <= NOW() )";
		}
		$archive = [];
		$sarchive = [];
		if(!empty($data['filter_year'])) {
			$archive[] = $data['filter_year'];
			$sarchive[] = '%Y';
		}
		if(!empty($data['filter_month'])) {
			$archive[] = $data['filter_month'];
			$sarchive[] = '%m';
		}
		if(!empty($archive)) {
			$sql .= " AND DATE_FORMAT(p.date_available, '". implode('-', $sarchive) ."') = '". implode('-', $archive) ."' ";
		}
		$sql .= " GROUP BY p.mpblogpost_id";
		$sort_data = [
			'pd.name',
			'rating',
			'comments',
			'p.viewed',
			'p.sort_order',
			'p.date_added'
		];
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";
		}
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(pd.name) DESC";
		} else {
			$sql .= " ASC, LCASE(pd.name) ASC";
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
		$mpblogpost_data = [];
		$query = $this->db->query($sql);
		foreach ($query->rows as $result) {
			$mpblogpost_data[$result['mpblogpost_id']] = $this->getMpBlogPost($result['mpblogpost_id']);
		}
		return $mpblogpost_data;
	}
	public function getLatestMpBlogPosts($limit) {

			$query = $this->db->query("SELECT p.mpblogpost_id FROM " . DB_PREFIX . "mpblogpost p LEFT JOIN " . DB_PREFIX . "mpblogpost_to_store p2s ON (p.mpblogpost_id = p2s.mpblogpost_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY p.date_added DESC LIMIT " . (int)$limit);
			foreach ($query->rows as $result) {
				$mpblogpost_data[$result['mpblogpost_id']] = $this->getMpBlogPost($result['mpblogpost_id']);
			}
			

		return $mpblogpost_data;
	}
	public function getPopularMpBlogPosts($limit) {
	
			$query = $this->db->query("SELECT p.mpblogpost_id FROM " . DB_PREFIX . "mpblogpost p LEFT JOIN " . DB_PREFIX . "mpblogpost_to_store p2s ON (p.mpblogpost_id = p2s.mpblogpost_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY p.viewed DESC, p.date_added DESC LIMIT " . (int)$limit);
	
			foreach ($query->rows as $result) {
				$mpblogpost_data[$result['mpblogpost_id']] = $this->getMpBlogPost($result['mpblogpost_id']);
			}
			
		
		return $mpblogpost_data;
	}
	public function getTrendingMpBlogPosts($limit) {
	
			$query = $this->db->query("SELECT p.mpblogpost_id, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "mpblograting r1 WHERE r1.mpblogpost_id = p.mpblogpost_id AND r1.status = '1' GROUP BY r1.mpblogpost_id) AS rating FROM " . DB_PREFIX . "mpblogpost p LEFT JOIN " . DB_PREFIX . "mpblogpost_to_store p2s ON (p.mpblogpost_id = p2s.mpblogpost_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY rating DESC, p.date_added DESC LIMIT " . (int)$limit);
	
			foreach ($query->rows as $result) {
				$mpblogpost_data[$result['mpblogpost_id']] = $this->getMpBlogPost($result['mpblogpost_id']);
			}
			
		
		return $mpblogpost_data;
	}
	public function getMpBlogPostImages($mpblogpost_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpblogpost_image WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "' ORDER BY sort_order ASC");
		return $query->rows;
	}
	public function getMpBlogPostRelated($mpblogpost_id) {
		$mpblogpost_data = [];
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpblogpost_related pr LEFT JOIN " . DB_PREFIX . "mpblogpost p ON (pr.related_id = p.mpblogpost_id) LEFT JOIN " . DB_PREFIX . "mpblogpost_to_store p2s ON (p.mpblogpost_id = p2s.mpblogpost_id) WHERE pr.mpblogpost_id = '" . (int)$mpblogpost_id . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");
		foreach ($query->rows as $result) {
			$mpblogpost_data[$result['related_id']] = $this->getMpBlogPost($result['related_id']);
		}
		return $mpblogpost_data;
	}
	public function getMpBlogPostRelatedProducts($mpblogpost_id) {
		$mpblogpost_data = [];
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpblogpost_relatedproduct pr LEFT JOIN " . DB_PREFIX . "product p ON (pr.related_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pr.mpblogpost_id = '" . (int)$mpblogpost_id . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");
		$this->load->model('catalog/product');
		foreach ($query->rows as $result) {
			$mpblogpost_data[$result['related_id']] = $this->model_catalog_product->getProduct($result['related_id']);
		}
		return $mpblogpost_data;
	}
	public function getMpBlogPostRelatedCategories($mpblogpost_id) {
		$mpblogpost_data = [];
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpblogpost_relatedcategory pr LEFT JOIN " . DB_PREFIX . "category p ON (pr.related_id = p.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store p2s ON (p.category_id = p2s.category_id) WHERE pr.mpblogpost_id = '" . (int)$mpblogpost_id . "' AND p.status = '1' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");
		$this->load->model('catalog/category');
		foreach ($query->rows as $result) {
			$mpblogpost_data[$result['related_id']] = $this->model_catalog_category->getCategory($result['related_id']);
		}
		return $mpblogpost_data;
	}
	public function getMpBlogCategories($mpblogpost_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpblogpost_to_mpblogcategory WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "'");
		return $query->rows;
	}
	public function getTotalMpBlogPosts($data = []) {
		$sql = "SELECT COUNT(DISTINCT p.mpblogpost_id) AS total";
		if (!empty($data['filter_mpblogcategory_id'])) {
			if (!empty($data['filter_sub_mpblogcategory'])) {
				$sql .= " FROM " . DB_PREFIX . "mpblogcategory_path cp LEFT JOIN " . DB_PREFIX . "mpblogpost_to_mpblogcategory p2c ON (cp.mpblogcategory_id = p2c.mpblogcategory_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "mpblogpost_to_mpblogcategory p2c";
			}
			$sql .= " LEFT JOIN " . DB_PREFIX . "mpblogpost p ON (p2c.mpblogpost_id = p.mpblogpost_id)";
			
		} else {
			$sql .= " FROM " . DB_PREFIX . "mpblogpost p";
		}
		$sql .= " LEFT JOIN " . DB_PREFIX . "mpblogpost_description pd ON (p.mpblogpost_id = pd.mpblogpost_id) LEFT JOIN " . DB_PREFIX . "mpblogpost_to_store p2s ON (p.mpblogpost_id = p2s.mpblogpost_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
		if (!empty($data['filter_mpblogcategory_id'])) {
			if (!empty($data['filter_sub_mpblogcategory'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_mpblogcategory_id'] . "'";
			} else {
				$sql .= " AND p2c.mpblogcategory_id = '" . (int)$data['filter_mpblogcategory_id'] . "'";
			}
		}
		if (!empty($data['filter_name']) || !empty($data['filter_tag']) || !empty($data['filter_author'])) {
			$sql .= " AND (";
			if (!empty($data['filter_name'])) {
				$implode = [];
				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));
				foreach ($words as $word) {
					$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
				}
				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
				if (!empty($data['filter_description'])) {
					$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}
			$or = false;
			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
				$or = true;
			}
			if (!empty($data['filter_tag'])) {
				$implode = [];
				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));
				foreach ($words as $word) {
					$implode[] = "pd.tag LIKE '%" . $this->db->escape($word) . "%'";
				}
				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}
			if ($or) {
				$sql .= " OR ";
			}
			if (!empty($data['filter_author'])) {
				$implode = [];
				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_author'])));
				foreach ($words as $word) {
					$implode[] = "p.author LIKE '%" . $this->db->escape($word) . "%'";
				}
				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}
			$sql .= ")";
		}
		if (!empty($data['filter_date'])) {
			$sql .= " AND ( p.date_available = '". date('Y-m-d', strtotime($data['filter_date'])) ."' AND p.date_available <= NOW() )";
		}
		$archive = [];
		$sarchive = [];
		if(!empty($data['filter_year'])) {
			$archive[] = $data['filter_year'];
			$sarchive[] = '%Y';
		}
		if(!empty($data['filter_month'])) {
			$archive[] = $data['filter_month'];
			$sarchive[] = '%m';
		}
		if(!empty($archive)) {
			$sql .= " AND DATE_FORMAT(p.date_available, '". implode('-', $sarchive) ."') = '". implode('-', $archive) ."' ";
		}
		$query = $this->db->query($sql);
		return $query->row['total'];
	}
	public function addRating($mpblogpost_id, $data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "mpblograting SET  customer_id = '" . (int)$this->customer->getId() . "', mpblogpost_id = '" . (int)$mpblogpost_id . "', status = '" . (int)$this->config->get('mpblog_blog_approve_rating') . "', rating = '" . (int)$data['rating'] . "', date_added = NOW()");
		$mpblograting_id = $this->db->getLastId();
		return $mpblograting_id;
	}
	public function addComment($mpblogpost_id, $data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogcomment SET author = '" . $this->db->escape($data['name']) . "', customer_id = '" . (int)$this->customer->getId() . "', mpblogpost_id = '" . (int)$mpblogpost_id . "', text = '" . $this->db->escape($data['text']) . "', status = '" . (int)$this->config->get('mpblog_blog_approve_comment') . "', date_added = NOW()");
		$mpblogcomment_id = $this->db->getLastId();
		return $mpblogcomment_id;
	}
	public function getCommentsByMpBlogPostId($mpblogpost_id, $start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}
		if ($limit < 1) {
			$limit = 20;
		}
		$query = $this->db->query("SELECT r.mpblogcomment_id, r.author, r.text, p.mpblogpost_id, pd.name, p.image, r.date_added FROM " . DB_PREFIX . "mpblogcomment r LEFT JOIN " . DB_PREFIX . "mpblogpost p ON (r.mpblogpost_id = p.mpblogpost_id) LEFT JOIN " . DB_PREFIX . "mpblogpost_description pd ON (p.mpblogpost_id = pd.mpblogpost_id) WHERE p.mpblogpost_id = '" . (int)$mpblogpost_id . "' AND p.date_available <= NOW() AND p.status = '1' AND r.status = '1' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY r.date_added DESC LIMIT " . (int)$start . "," . (int)$limit);
		return $query->rows;
	}
	public function getTotalCommentsByMpBlogPostId($mpblogpost_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mpblogcomment r LEFT JOIN " . DB_PREFIX . "mpblogpost p ON (r.mpblogpost_id = p.mpblogpost_id) LEFT JOIN " . DB_PREFIX . "mpblogpost_description pd ON (p.mpblogpost_id = pd.mpblogpost_id) WHERE p.mpblogpost_id = '" . (int)$mpblogpost_id . "' AND p.date_available <= NOW() AND p.status = '1' AND r.status = '1' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		return $query->row['total'];
	}
	public function likeMpBlogPost($mpblogpost_id, $num_likes = 1) {
		$this->db->query("UPDATE " . DB_PREFIX . "mpblogpost SET likes = (likes + ". (int)$num_likes .") WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "'");
	}
	public function likedMpBlogPost($mpblogpost_id) {
		if($this->customer->isLogged()) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpblogpost_like WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "' AND customer_id='". (int)$this->customer->getId() ."'");
		if($query->num_rows) {
			foreach($query->rows as $key => $row) {
			if($key == 0) {
			$this->db->query("UPDATE " . DB_PREFIX . "mpblogpost_like SET  	like_status=1 WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "' AND customer_id='". (int)$this->customer->getId() ."' AND mpblogpost_like_id='". (int)$row['mpblogpost_like_id'] ."' ");
			}
			if($key > 0) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost_like WHERE mpblogpost_like_id='". (int)$row['mpblogpost_like_id'] ."' ");	
			}
			}
		} else {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogpost_like SET  	like_status=1, mpblogpost_id = '" . (int)$mpblogpost_id . "', customer_id='". (int)$this->customer->getId() ."'");
		}
		
		}
	}
	public function unlikeMpBlogPost($mpblogpost_id, $num_likes = 1) {
		$this->db->query("UPDATE " . DB_PREFIX . "mpblogpost SET likes = (likes - ". (int)$num_likes ." > 0) WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "'");
	}
	public function unlikedMpBlogPost($mpblogpost_id) {
		if($this->customer->isLogged()) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpblogpost_like WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "' AND customer_id='". (int)$this->customer->getId() ."'");
		if($query->num_rows) {
			foreach($query->rows as $key => $row) {
			if($key == 0) {
			$this->db->query("UPDATE " . DB_PREFIX . "mpblogpost_like SET  	like_status=0 WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "' AND customer_id='". (int)$this->customer->getId() ."' AND mpblogpost_like_id='". (int)$row['mpblogpost_like_id'] ."' ");
			}
			if($key > 0) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogpost_like WHERE mpblogpost_like_id='". (int)$row['mpblogpost_like_id'] ."' ");	
			}
			}
		} else {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogpost_like SET  	like_status=0, mpblogpost_id = '" . (int)$mpblogpost_id . "', customer_id='". (int)$this->customer->getId() ."'");
		}
		}
	}
	public function totalMpBlogPostLikes($mpblogpost_id) {
		$query = $this->db->query("SELECT likes FROM " . DB_PREFIX . "mpblogpost WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "'");
		return ($query->num_rows) ? $query->row['likes'] : 0;
	}
	public function isLikeByMe($mpblogpost_id) {
		// if logged go for db
		if($this->customer->isLogged()) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpblogpost_like WHERE mpblogpost_id = '" . (int)$mpblogpost_id . "' AND customer_id='". (int)$this->customer->getId() ."' AND like_status=1");
			return $query->num_rows > 0;
		}
		// if not logged go for cookie
		if(!$this->customer->isLogged()) {
			return (isset($this->request->cookie['mpblog'. $mpblogpost_id .'liked']) && $this->request->cookie['mpblog'. $mpblogpost_id .'liked']==true);
		}
	}
	public function getBlogYears() {
		$strSql = "
			SELECT 
				date_format(p.date_available,'%Y') AS year, 
				date_format(p.date_available,'%m') AS month 
			FROM `" . DB_PREFIX . "mpblogpost` p 
			LEFT JOIN `" . DB_PREFIX . "mpblogpost_to_store` p2s 
			ON (p.mpblogpost_id = p2s.mpblogpost_id)  
			WHERE 
				p.status = '1' 
				AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' 
				AND p.date_available != '0000-00-00' 
			GROUP BY year, month 
			ORDER BY p.date_available DESC
		";
		$query = $this->db->query($strSql);
		return $query->rows;
	}
	public function getTotalBlogsMonth($year,$month) {
		$query = $this->db->query("SELECT COUNT(*) as total FROM " . DB_PREFIX . "mpblogpost p LEFT JOIN " . DB_PREFIX . "mpblogpost_to_store p2s ON (p.mpblogpost_id = p2s.mpblogpost_id) WHERE p.status = '1' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND date_format(p.date_available,'%Y-%m')='". $this->db->escape($year.'-'.$month) ."' ");
		return $query->row['total'];
	}
	public function getNextPrevMpBlogPost($mpblogcategory_id, $mpblogpost_id) {
		
		// get next blog link
		$sql = "SELECT p.mpblogpost_id, p.image, pd.name, p.video, p.posttype FROM `" . DB_PREFIX . "mpblogpost` p LEFT JOIN `" . DB_PREFIX . "mpblogpost_description` pd ON(p.mpblogpost_id=pd.mpblogpost_id)";
		if(!empty($mpblogcategory_id)) {
			$sql .= " LEFT JOIN `" . DB_PREFIX . "mpblogpost_to_mpblogcategory` as p2c ON(p.mpblogpost_id=p2c.mpblogpost_id) ";
		}
		$sql .= " WHERE pd.language_id='". (int)$this->config->get('config_language_id') ."' AND p.mpblogpost_id = (SELECT MIN(p1.mpblogpost_id) FROM `" . DB_PREFIX . "mpblogpost` p1 ";
		if(!empty($mpblogcategory_id)) {
			$sql .= " LEFT JOIN `" . DB_PREFIX . "mpblogpost_to_mpblogcategory` as p2c1 ON(p1.mpblogpost_id=p2c1.mpblogpost_id) ";
		}
		$sql .= " WHERE p1.mpblogpost_id > ". $mpblogpost_id ." ";
		if(!empty($mpblogcategory_id)) {
			$sql .= " AND p2c1.mpblogcategory_id='". $mpblogcategory_id ."'  ";
		}
		$sql .= " ) ";
		if(!empty($mpblogcategory_id)) {
			$sql .= " AND p2c.mpblogcategory_id='". $mpblogcategory_id ."'  ";
		}
		$nextmpblogpost = $this->db->query($sql);
		// get previous blog link
		$sql = "SELECT p.mpblogpost_id, p.image, pd.name, p.video, p.posttype FROM `" . DB_PREFIX . "mpblogpost` p LEFT JOIN `" . DB_PREFIX . "mpblogpost_description` pd ON(p.mpblogpost_id=pd.mpblogpost_id)";
		if(!empty($mpblogcategory_id)) {
			$sql .= " LEFT JOIN `" . DB_PREFIX . "mpblogpost_to_mpblogcategory` as p2c ON(p.mpblogpost_id=p2c.mpblogpost_id) ";
		}
		$sql .= " WHERE pd.language_id='". (int)$this->config->get('config_language_id') ."' AND p.mpblogpost_id = (SELECT MIN(p1.mpblogpost_id) FROM `" . DB_PREFIX . "mpblogpost` p1";
		if(!empty($mpblogcategory_id)) {
			$sql .= " LEFT JOIN `" . DB_PREFIX . "mpblogpost_to_mpblogcategory` as p2c1 ON(p1.mpblogpost_id=p2c1.mpblogpost_id) ";
		}
		$sql .= " WHERE p1.mpblogpost_id < ". $mpblogpost_id ." ";
		if(!empty($mpblogcategory_id)) {
			$sql .= " AND p2c1.mpblogcategory_id='". $mpblogcategory_id ."'  ";
		}
		$sql .= " ) ";
		if(!empty($mpblogcategory_id)) {
			$sql .= " AND p2c.mpblogcategory_id='". $mpblogcategory_id ."'  ";
		}
		$prevmpblogpost = $this->db->query($sql);
		return ['next' =>  $nextmpblogpost->row, 'prev' => $prevmpblogpost->row];
	}
	public function mpYouTubeThumb($url) {
		$youtubeVideoId = '';
		$smallImage1 = '';	
		$smallImage2 = '';	
		$smallImage3 = '';	
		$hdThumb = '';	
		$defaultThumb = '';	
		$youTubeThumb = false;
	    if(!empty($url)) {
		$parts = parse_url($url);
		if(isset($parts['query'])) {
			parse_str($parts['query'], $query);
			if(isset($query['v'])) {
				$youtubeVideoId = $query['v'];
			}
		}			
		if(isset($parts['host']) && $parts['host'] == 'youtu.be') {
			$urlParts = explode('/', $parts['path']);
			$youtubeVideoId = end($urlParts); 
		}
		if(isset($parts['host']) && ($parts['host'] == 'youtu.be' || $parts['host'] == 'www.youtube.com') && empty($youtubeVideoId)) {
			$urlParts = explode('/', $parts['path']);
			$youtubeVideoId = end($urlParts); 	
		}
	    }
		if(!empty($youtubeVideoId)) {
			$smallImage1 = 'https://img.youtube.com/vi/'. $youtubeVideoId .'/1.jpg';
			$smallImage2 = 'https://img.youtube.com/vi/'. $youtubeVideoId .'/2.jpg';
			$smallImage3 = 'https://img.youtube.com/vi/'. $youtubeVideoId .'/3.jpg';
			$hdThumb = 'https://img.youtube.com/vi/'. $youtubeVideoId .'/maxresdefault.jpg';
			$defaultThumb = 'https://img.youtube.com/vi/'. $youtubeVideoId .'/hqdefault.jpg';
			$youTubeThumb = true;
		}
		return [
			'smallImage1' => $smallImage1,
			'smallImage2' => $smallImage2,
			'smallImage3' => $smallImage3,
			'hdThumb' => $hdThumb,
			'defaultThumb' => $defaultThumb,
			'youTubeThumb' => $youTubeThumb,
			'youtubeVideoId' => $youtubeVideoId,
		];
	}

	public function mpVideoEmbedURL($url) {
		$videoId = '';
		$origin = 'youtube';
		$embedUrl = '';
		if(!empty($url)) {
			$parts = parse_url($url);

			if(isset($parts['query'])) {
				parse_str($parts['query'], $query);
				if(isset($query['v'])) {
					$videoId = $query['v'];

				}
			}			
			if(empty($videoId) && isset($parts['host']) && ($parts['host'] == 'youtu.be' || $parts['host'] == 'www.youtube.com' )) {
				$urlParts = explode('/', $parts['path']);
				// remove any empty arrays from trailing
				if (utf8_strlen(end($parts)) == 0) {
					array_pop($parts);
				}
				$videoId = end($urlParts); 
		 	}

		 	if(isset($parts['host']) && ($parts['host'] == 'youtu.be' || $parts['host'] == 'www.youtube.com' || $parts['host'] == 'youtube.com') ) {
		 		$origin = 'youtube';
		 	}
		}

		if(!empty($videoId)) {
			if($origin == 'youtube') {
				$embedUrl = 'https://www.youtube.com/embed/' . $videoId;
			}
		}

		return $embedUrl;
	}

	public function themename() {
		$custom_themename = '';
		if($this->config->get('theme_default_directory')) {
			$custom_themename = $this->config->get('theme_default_directory');
		} else if($this->config->get('config_template')) {
			$custom_themename = $this->config->get('config_template');
		} else{
			$custom_themename = 'default';
		}

		if(empty($custom_themename)) {
			$custom_themename = 'default';
		}

		return $custom_themename;
	}

	public function themeclass() {
		if($this->config->get('theme_default_directory')) {
			$custom_themename = $this->config->get('theme_default_directory');
		} else if($this->config->get('config_template')) {
			$custom_themename = $this->config->get('config_template');
		} else{
			$custom_themename = 'default';
		}

		if(empty($custom_themename)) {
			$custom_themename = 'default';
		}

		if(isset($custom_themename) && $custom_themename == 'journal2') {
			$mblog_class = 'journal-mblog';
		} else{
			$mblog_class = 'default-mblog';
		}

		return $mblog_class;

	}

	public function mpssl() {
		if(VERSION >= '2.1.0.2') {
			return true;
		} else{
			return 'SSL';
		}
	}

	
	public function	subscribeEmailExists($email) {
		$query = $this->db->query("SELECT mpblogsubscribers_id FROM " . DB_PREFIX . "mpblogsubscribers mbs WHERE mbs.email LIKE '%". $this->db->escape($email) ."%'");
		return $query->row;
	}

	public function subscribeMe($email) {
		$status = 0;
		if(!$this->config->get('mpblog_subscribeapprove')) {
			$this->config->set('mpblog_subscribeapprove', 'AUTO');
		}
		if($this->config->get('mpblog_subscribeapprove') == 'AUTO') {
			$status = 1;
		}

		$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogsubscribers SET email='". $this->db->escape($email) ."', customer_id='". (int)$this->customer->getId() ."', approval_by='". $this->db->escape($this->config->get('mpblog_subscribeapprove')) ."', store_id='". (int)$this->config->get('config_store_id') ."', language_id='". (int)$this->config->get('config_language_id') ."', status='". $status ."', date_added=NOW()");
		$mpblogsubscribers_id = $this->db->getLastId();

		// send subscribe email here
		if (isset($this->request->server['HTTPS'])) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$logo = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$logo = '';
		}

		$adminemail = $this->config->get('mpblog_subscribeadminmail');
		$admin_email = $this->config->get('config_email');
		if (utf8_strlen($adminemail) > 0 && filter_var($adminemail, FILTER_VALIDATE_EMAIL)) {
			$admin_email = $adminemail;
		}

		$subscribemail = $this->config->get('mpblog_subscribemail');
		$email_to_subscriber = false;

		// subscriber get autoapprove and approval mail status is enable
		if($this->config->get('mpblog_subscribeapprove') == 'AUTO' && $this->config->get('mpblog_subscribeapproval_status')) {

			$email_subject = !empty($subscribemail['approval'][$this->config->get('config_language_id')]['subject']) ? $subscribemail['approval'][$this->config->get('config_language_id')]['subject'] : '';

			$email_message = !empty($subscribemail['approval'][$this->config->get('config_language_id')]['message']) ? $subscribemail['approval'][$this->config->get('config_language_id')]['message'] : '';

			$find = [
				'[STORE_NAME]',
				'[STORE_LINK]',
				'[LOGO]',
				'[EMAIL]',
			];
		
			$replace = [
				'STORE_NAME'					=> $this->config->get('config_name'),
				'STORE_LINK'					=> $this->url->link('common/home', '', true),
				'LOGO'							=> '<a href="'. $this->url->link('common/home', '', true) .'"><img src="'. $logo .'" alt="'. $this->config->get('config_name') .'" title="'. $this->config->get('config_name') .'" /></a>',
				'EMAIL'							=> $email,
			];
			
			if(!empty($email_subject)) {
				$email_subject = str_replace(["\r\n", "\r", "\n"], '', preg_replace(["/\s\s+/", "/\r\r+/", "/\n\n+/"], '', trim(str_replace($find, $replace, $email_subject))));
			} else {
				$email_subject = '';
			}
			
			if(!empty($email_message)) {
				$email_message = str_replace(["\r\n", "\r", "\n"], '', preg_replace(["/\s\s+/", "/\r\r+/", "/\n\n+/"], '', trim(str_replace($find, $replace, $email_message))));
			} else {
				$email_message = '';
			}
			$email_to_subscriber = true;
		}

		// subscriber get approved by admin and pending approval mail status is enable
		if($this->config->get('mpblog_subscribeapprove') == 'ADMIN' && $this->config->get('mpblog_subscribepending_status')) {

			$email_subject = !empty($subscribemail['pending'][$this->config->get('config_language_id')]['subject']) ? $subscribemail['pending'][$this->config->get('config_language_id')]['subject'] : '';

			$email_message = !empty($subscribemail['pending'][$this->config->get('config_language_id')]['message']) ? $subscribemail['pending'][$this->config->get('config_language_id')]['message'] : '';

			$find = [
				'[STORE_NAME]',
				'[STORE_LINK]',
				'[LOGO]',
				'[EMAIL]',
			];
		
			$replace = [
				'STORE_NAME'					=> $this->config->get('config_name'),
				'STORE_LINK'					=> $this->url->link('common/home', '', true),
				'LOGO'							=> '<a href="'. $this->url->link('common/home', '', true) .'"><img src="'. $logo .'" alt="'. $this->config->get('config_name') .'" title="'. $this->config->get('config_name') .'" /></a>',
				'EMAIL'							=> $email,
			];
			
			if(!empty($email_subject)) {
				$email_subject = str_replace(["\r\n", "\r", "\n"], '', preg_replace(["/\s\s+/", "/\r\r+/", "/\n\n+/"], '', trim(str_replace($find, $replace, $email_subject))));
			} else {
				$email_subject = '';
			}
			
			if(!empty($email_message)) {
				$email_message = str_replace(["\r\n", "\r", "\n"], '', preg_replace(["/\s\s+/", "/\r\r+/", "/\n\n+/"], '', trim(str_replace($find, $replace, $email_message))));
			} else {
				$email_message = '';
			}
			$email_to_subscriber = true;
		}

		// subscriber get approved by verification email and subscriber confirmation mail status is enable
		if($this->config->get('mpblog_subscribeapprove') == 'CODE' && $this->config->get('mpblog_subscribeconfirm_status')) {

			$email_subject = !empty($subscribemail['confirm'][$this->config->get('config_language_id')]['subject']) ? $subscribemail['confirm'][$this->config->get('config_language_id')]['subject'] : '';

			$email_message = !empty($subscribemail['confirm'][$this->config->get('config_language_id')]['message']) ? $subscribemail['confirm'][$this->config->get('config_language_id')]['message'] : '';

			do {
				$code = token(10);
				// check if code exits
				// 1=code generate, 0=verified, 2=canceled
				$codequery = $this->db->query("SELECT code FROM ". DB_PREFIX ."mpblogsubscribers_verification WHERE `code`='". $this->db->escape($code) ."' AND status!=1");

			}while($codequery->num_rows>0);

			// insert code for verification
			$this->db->query("INSERT INTO ". DB_PREFIX ."mpblogsubscribers_verification SET `code`='". $this->db->escape($code) ."', status=1, action='SUBSCRIBE', mpblogsubscribers_id='". (int)$mpblogsubscribers_id ."', date_added=NOW()");

			$find = [
				'[STORE_NAME]',
				'[STORE_LINK]',
				'[LOGO]',
				'[EMAIL]',
				'[CONFIRMATION_LINK]',
				'[CONFIRMATION_CODE]',
			];
		
			$replace = [
				'STORE_NAME'	=> $this->config->get('config_name'),
				'STORE_LINK'	=> $this->url->link('common/home', '', true),
				'LOGO'	=> '<a href="'. $this->url->link('common/home', '', true) .'"><img src="'. $logo .'" alt="'. $this->config->get('config_name') .'" title="'. $this->config->get('config_name') .'" /></a>',
				'EMAIL'	=> $email,
				'CONFIRMATION_LINK'	=> $this->url->link('extension/mpblog/subscriber_verification', '&v='.$code, true),
				'CONFIRMATION_CODE'	=> $code,
			];
			
			if(!empty($email_subject)) {
				$email_subject = str_replace(["\r\n", "\r", "\n"], '', preg_replace(["/\s\s+/", "/\r\r+/", "/\n\n+/"], '', trim(str_replace($find, $replace, $email_subject))));
			} else {
				$email_subject = '';
			}
			
			if(!empty($email_message)) {
				$email_message = str_replace(["\r\n", "\r", "\n"], '', preg_replace(["/\s\s+/", "/\r\r+/", "/\n\n+/"], '', trim(str_replace($find, $replace, $email_message))));
			} else {
				$email_message = '';
			}
			$email_to_subscriber = true;
		}

		if($email_to_subscriber) {
			$mail = $this->getMailObject();

			$mail->setTo($email);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject($email_subject);
			$mail->setHtml(html_entity_decode($email_message, ENT_QUOTES, 'UTF-8'));
			$mail->send();
		}

		// new subscriber mail goes to admin and admin mail status is enable
		if($this->config->get('mpblog_subscribeadminmail_status')) {

			$email_subject = !empty($subscribemail['admin'][$this->config->get('config_language_id')]['subject']) ? $subscribemail['admin'][$this->config->get('config_language_id')]['subject'] : '';

			$email_message = !empty($subscribemail['admin'][$this->config->get('config_language_id')]['message']) ? $subscribemail['admin'][$this->config->get('config_language_id')]['message'] : '';

			$find = [
				'[STORE_NAME]',
				'[STORE_LINK]',
				'[LOGO]',				
				'[EMAIL]',
			];
		
			$replace = [
				'STORE_NAME'					=> $this->config->get('config_name'),
				'STORE_LINK'					=> $this->url->link('common/home', '', true),
				'LOGO'							=> '<a href="'. $this->url->link('common/home', '', true) .'"><img src="'. $logo .'" alt="'. $this->config->get('config_name') .'" title="'. $this->config->get('config_name') .'" /></a>',
				'EMAIL'							=> $email,
			];
			
			if(!empty($email_subject)) {
				$email_subject = str_replace(["\r\n", "\r", "\n"], '', preg_replace(["/\s\s+/", "/\r\r+/", "/\n\n+/"], '', trim(str_replace($find, $replace, $email_subject))));
			} else {
				$email_subject = '';
			}
			
			if(!empty($email_message)) {
				$email_message = str_replace(["\r\n", "\r", "\n"], '', preg_replace(["/\s\s+/", "/\r\r+/", "/\n\n+/"], '', trim(str_replace($find, $replace, $email_message))));
			} else {
				$email_message = '';
			}


			$mail = $this->getMailObject();

			$mail->setTo($admin_email);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject($email_subject);
			$mail->setHtml(html_entity_decode($email_message, ENT_QUOTES, 'UTF-8'));
			$mail->send();
		}
		return $mpblogsubscribers_id;
	}

	public function unSubscribeMe($email) {
		$this->db->query("UPDATE " . DB_PREFIX . "mpblogsubscribers SET date_modified=NOW() WHERE email='". $this->db->escape($email) ."'");

		// send unsubscribe email here
		if (isset($this->request->server['HTTPS'])) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$logo = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$logo = '';
		}

		$adminemail = $this->config->get('mpblog_subscribeadminmail');
		$admin_email = $this->config->get('config_email');
		if (utf8_strlen($adminemail) > 0 && filter_var($adminemail, FILTER_VALIDATE_EMAIL)) {
			$admin_email = $adminemail;
		}

		$subscribemail = $this->config->get('mpblog_subscribemail');
		$email_to_subscriber = false;

		// subscriber get approved by verification email and subscriber confirmation mail status is enable
		if($this->config->get('mpblog_unsubscribe_status')) {

			$email_subject = !empty($subscribemail['unsubscribe'][$this->config->get('config_language_id')]['subject']) ? $subscribemail['unsubscribe'][$this->config->get('config_language_id')]['subject'] : '';

			$email_message = !empty($subscribemail['unsubscribe'][$this->config->get('config_language_id')]['message']) ? $subscribemail['unsubscribe'][$this->config->get('config_language_id')]['message'] : '';

			do {
                $code = token(10);
                // check if code exits
                // 1=code generate, 0=verified, 2=canceled
                $codequery = $this->db->query("SELECT code FROM ". DB_PREFIX ."mpblogsubscribers_verification WHERE `code`='". $this->db->escape($code) ."' AND status!=1");

            }while($codequery->num_rows>0);

            // insert code for verification
            $this->db->query("INSERT INTO ". DB_PREFIX ."mpblogsubscribers_verification SET `code`='". $this->db->escape($code) ."', status=1, action='UNSUBSCRIBE', mpblogsubscribers_id='". (int)$mpblogsubscribers_id ."', date_added=NOW()");

			$find = [
				'[STORE_NAME]',
				'[STORE_LINK]',
				'[LOGO]',
				'[EMAIL]',
				'[CONFIRMATION_LINK]',
				'[CONFIRMATION_CODE]',
			];
		
			$replace = [
				'STORE_NAME'					=> $this->config->get('config_name'),
				'STORE_LINK'					=> $this->url->link('common/home', '', true),
				'LOGO'							=> '<a href="'. $this->url->link('common/home', '', true) .'"><img src="'. $logo .'" alt="'. $this->config->get('config_name') .'" title="'. $this->config->get('config_name') .'" /></a>',
				'EMAIL'							=> $email,
				'CONFIRMATION_LINK' => $this->url->link('extension/mpblog/subscriber_verification', '&v='.$code, true),
        'CONFIRMATION_CODE' => $code,
			];
			
			if(!empty($email_subject)) {
				$email_subject = str_replace(["\r\n", "\r", "\n"], '', preg_replace(["/\s\s+/", "/\r\r+/", "/\n\n+/"], '', trim(str_replace($find, $replace, $email_subject))));
			} else {
				$email_subject = '';
			}
			
			if(!empty($email_message)) {
				$email_message = str_replace(["\r\n", "\r", "\n"], '', preg_replace(["/\s\s+/", "/\r\r+/", "/\n\n+/"], '', trim(str_replace($find, $replace, $email_message))));
			} else {
				$email_message = '';
			}
			$email_to_subscriber = true;
		}

		if($email_to_subscriber) {
			$mail = $this->getMailObject();

			$mail->setTo($email);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject($email_subject);
			$mail->setHtml(html_entity_decode($email_message, ENT_QUOTES, 'UTF-8'));
			$mail->send();
		}
	}

	public function getSubscriberVerificaionCode($mpblogsubscribers_id) {
		$query = $this->db->query("SELECT * FROM ". DB_PREFIX ."mpblogsubscribers_verification WHERE status=1 AND mpblogsubscribers_id='". (int)$mpblogsubscribers_id ."' ORDER BY date_added DESC");
		return $query->row;
	}

	public function verifySubscirbeCode($code) {
		return $this->db->query("SELECT * FROM ". DB_PREFIX ."mpblogsubscribers_verification WHERE status=1 AND code='". $this->db->escape($code) ."'");
	}

	public function expireVerification($code) {
		$this->db->query("UPDATE ". DB_PREFIX ."mpblogsubscribers_verification SET status=0,date_modified=NOW() WHERE code='". $this->db->escape($code) ."'");
	}
	public function updateSubscirberStatus($mpblogsubscribers_id, $status) {
		$this->db->query("UPDATE ". DB_PREFIX ."mpblogsubscribers SET status='". (int)$status ."', date_modified=NOW() WHERE mpblogsubscribers_id='". (int)$mpblogsubscribers_id ."'");
	}

	public function getSubscriberById($mpblogsubscribers_id) {
		$query = $this->db->query("SELECT * FROM ". DB_PREFIX ."mpblogsubscribers WHERE mpblogsubscribers_id='". (int)$mpblogsubscribers_id ."'");
		return $query->row;
	}
	
}



if(!function_exists('token')) {
	function token($length = 32) {
		// Create random token
		$string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		
		$max = strlen($string) - 1;
		
		$token = '';
		
		for ($i = 0; $i < $length; $i++) {
			$token .= $string[mt_rand(0, $max)];
		}	
		
		return $token;
	}
}
