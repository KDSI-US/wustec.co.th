<?php
class ModelExtensionblog extends Model {
	
	public function getblogarchives(){
		
		
	}
	
	public function getblog($post_id){
		$query = $this->db->query("SELECT *, p.author as username,(SELECT COUNT(*) FROM ".DB_PREFIX."post_like WHERE post_id = p.post_id) as likes,(SELECT COUNT(*) FROM ".DB_PREFIX."post_comment WHERE post_id = p.post_id AND status =1) As comments FROM " . DB_PREFIX . "post p LEFT JOIN " . DB_PREFIX . "post_description pd ON (p.post_id=pd.post_id) LEFT JOIN ". DB_PREFIX ."post_to_store p2s ON (p.post_id=p2s.post_id) WHERE p.post_id = '".(int)$post_id."' AND pd.language_id= '". (int)$this->config->get('config_language_id') ."' AND p2s.store_id='". (int)$this->config->get('config_store_id') ."' AND p.status = 1 AND DATE(p.date) <= now() ORDER BY p.sort_order ASC");
		
		return $query->row;
	}
	
	public function updateViewed($post_id){
		$this->db->query("UPDATE " . DB_PREFIX . "post SET viewed = (viewed + 1) WHERE post_id = '" . (int)$post_id . "'");
	}
	
	public function getblogs($data){
		 if (!empty($data['filter_blog_category_id'])){
			if (!empty($data['filter_sub_blog_category'])){
				$sql = "SELECT * FROM " . DB_PREFIX . "blog_category_path cp LEFT JOIN " . DB_PREFIX . "post_to_blog_category p2c ON (cp.blog_category_id = p2c.blog_category_id)";
			} else {
				$sql = "SELECT * FROM " . DB_PREFIX . "post_to_blog_category p2c";
			}
			$sql .= " LEFT JOIN " . DB_PREFIX . "post p ON (p2c.post_id = p.post_id)";
		 } else {
			$sql = "SELECT * FROM " . DB_PREFIX . "post p";
		 }
		 
		 $sql .= " LEFT JOIN " . DB_PREFIX . "post_description pd ON (p.post_id = pd.post_id) LEFT JOIN " . DB_PREFIX . "post_to_store p2s ON (p.post_id = p2s.post_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND  p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND DATE(p.date) <= now()";
		 
		 if(!empty($data['filter_blog_category_id'])){
			if (!empty($data['filter_sub_blog_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_blog_category_id'] . "'";
			} else {
				$sql .= " AND p2c.blog_category_id = '" . (int)$data['filter_blog_category_id'] . "'";
			}
		 }
		 
		 if (!empty($data['filter_name']) || !empty($data['filter_tag'])){
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if(!empty($data['filter_description'])){
					$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$sql .= "pd.tag LIKE '%" . $this->db->escape($data['filter_tag']) . "%'";
			}

			$sql .= ")";
		}
		
		$sql .= " GROUP BY p.post_id";

		$sort_data = array(
			'p.date',
			'pd.name',
			'p.sort_order',
			'p.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name'){
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			}else{
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

		$post_data = array();

		$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$post_data[$result['post_id']] = $this->getblog($result['post_id']);
		}

		return $post_data;
	}
	
	public function getTotalblogs($data){
		 if (!empty($data['filter_blog_category_id'])){
			if (!empty($data['filter_sub_blog_category'])){
				$sql = "SELECT * FROM " . DB_PREFIX . "blog_category_path cp LEFT JOIN " . DB_PREFIX . "post_to_blog_category p2c ON (cp.blog_category_id = p2c.blog_category_id)";
			} else {
				$sql = "SELECT * FROM " . DB_PREFIX . "post_to_blog_category p2c";
			}
			$sql .= " LEFT JOIN " . DB_PREFIX . "post p ON (p2c.post_id = p.post_id)";
		 } else {
			$sql = "SELECT * FROM " . DB_PREFIX . "post p";
		 }
		 
		 $sql .= " LEFT JOIN " . DB_PREFIX . "post_description pd ON (p.post_id = pd.post_id) LEFT JOIN " . DB_PREFIX . "post_to_store p2s ON (p.post_id = p2s.post_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND  p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND DATE(p.date) <= now()";
		 
		 if(!empty($data['filter_blog_category_id'])){
			if (!empty($data['filter_sub_blog_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_blog_category_id'] . "'";
			} else {
				$sql .= " AND p2c.blog_category_id = '" . (int)$data['filter_blog_category_id'] . "'";
			}
		 }
		 
		 if (!empty($data['filter_name']) || !empty($data['filter_tag'])){
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if(!empty($data['filter_description'])){
					$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$sql .= "pd.tag LIKE '%" . $this->db->escape($data['filter_tag']) . "%'";
			}

			$sql .= ")";
		}
		
		$sql .= " GROUP BY p.post_id";

		$sort_data = array(
			'pd.name',
			'p.sort_order',
			'p.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name'){
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			}else{
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
		
		$query = $this->db->query($sql);
		
		return count($query->rows);
	}
	
	public function getPostImages($post_id){
	  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "post_image WHERE post_id = '" . (int)$post_id . "' ORDER BY sort_order ASC");

	  return $query->rows;
	}
	
	public function getPostRelated($post_id){
		$post_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "post_related pr LEFT JOIN " . DB_PREFIX . "post p ON (pr.related_id = p.post_id) LEFT JOIN " . DB_PREFIX . "post_to_store p2s ON (p.post_id = p2s.post_id) WHERE pr.post_id = '" . (int)$post_id . "' AND p.status = '1' AND p.date <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		foreach ($query->rows as $result) {
			$post_data[$result['related_id']] = $this->getblog($result['related_id']);
		}

		return $post_data;
	}
	
	public function addComment($post_id, $data){
		$postpagesetting = array();
		$blogsetting = $this->config->get('blogsetting');
		if(isset($blogsetting['blog'])){
		  $postpagesetting   = $blogsetting['blog'];
		}
		
		if(!empty($postpagesetting['comment_permission'])){
			$status = true;
		}else{
			$status = false;
		}
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "post_comment SET author = '" . $this->db->escape($data['name']) . "', email = '".$this->db->escape($data['email'])."', customer_id = '" . (int)$this->customer->getId() . "', post_id = '" . (int)$post_id . "', text = '" . $this->db->escape($data['text']) . "', date_added = NOW(), status = '".(int)$status."'");

		$comment_id = $this->db->getLastId();
		
		if(!empty($postpagesetting['comment_notification'])){
			$this->load->language('mail/comment');
			
			$post_info = $this->getblog($post_id);

			$subject = sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));

			$message  = $this->language->get('text_waiting') . "\n";
			$message .= sprintf($this->language->get('text_product'), html_entity_decode($post_info['name'], ENT_QUOTES, 'UTF-8')) . "\n";
			$message .= sprintf($this->language->get('text_reviewer'), html_entity_decode($data['name'], ENT_QUOTES, 'UTF-8')) . "\n";
			$message .= sprintf($this->language->get('text_email'), $data['email']) . "\n";
			$message .= $this->language->get('text_review') . "\n";
			$message .= html_entity_decode($data['text'], ENT_QUOTES, 'UTF-8') . "\n\n";

			$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($this->config->get('config_email'));
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject($subject);
			$mail->setText($message);
			$mail->send();

			// Send to additional alert emails
			$emails = explode(',', $this->config->get('config_mail_alert_email'));

			foreach ($emails as $email) {
				if ($email && preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $email)) {
					$mail->setTo($email);
					$mail->send();
				}
			}
	   }
	}
	
	public function getCommentByPostId($post_id, $start = 0, $limit = 20){
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 20;
		}

		$query = $this->db->query("SELECT r.comment_id, r.author, r.email, r.text, p.post_id, pd.name, p.image, r.date_added FROM " . DB_PREFIX . "post_comment r LEFT JOIN " . DB_PREFIX . "post p ON (r.post_id = p.post_id) LEFT JOIN " . DB_PREFIX . "post_description pd ON (p.post_id = pd.post_id) WHERE p.post_id = '" . (int)$post_id . "' AND p.status = '1' AND r.status = '1' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY r.date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalCommentByPostId($post_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "post_comment r LEFT JOIN " . DB_PREFIX . "post p ON (r.post_id = p.post_id) LEFT JOIN " . DB_PREFIX . "post_description pd ON (p.post_id = pd.post_id) WHERE p.post_id = '" . (int)$post_id . "' AND p.status = '1' AND r.status = '1' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");


		return $query->row['total'];
	}
	
	public function addlikebypostid($data){
		$this->db->query("INSERT INTO ".DB_PREFIX."post_like SET post_id = '".(int)$data['post_id']."',customer_id = '".(int)$data['customer_id']."',status = '".(int)$data['status']."'");
		return $this->db->getLastId();
	}
	
	public function checklike($post_id){
		$query = $this->db->query("SELECT * FROM ".DB_PREFIX."post_like WHERE post_id = '".(int)$post_id."' AND customer_id = '".(int)$this->customer->getId()."' AND status  = 1");
		if($query->row){
			return true;
		}else{
			return false;
		}
	}
	
	public function getProductRelated($post_id){
		$this->load->model('catalog/product');
		$product_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "post_related_product pr LEFT JOIN " . DB_PREFIX . "product p ON (pr.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pr.post_id = '" . (int)$post_id . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
		}
		
		return $product_data;
	}
}
