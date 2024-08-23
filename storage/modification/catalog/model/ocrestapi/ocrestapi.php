<?php
/* This file is under Git Control by KDSI. */
class ModelOcrestapiOcrestapi extends Model {
	public function checkpassword($email,$password)
	{
		
		$email_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($password) . "'))))) OR password = '" . $this->db->escape(md5($password)) . "') AND status = '1'");
		if($email_query->num_rows){
			return true;
		}else{
			 return false;
		}

	}

	public function get_reviews_by_productId($product_id) {
		

		$query = $this->db->query("SELECT r.review_id, r.author, r.rating, r.text, p.product_id, pd.name, p.price, p.image, r.date_added FROM " . DB_PREFIX . "review r LEFT JOIN " . DB_PREFIX . "product p ON (r.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND p.date_available <= NOW() AND p.status = '1' AND r.status = '1' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY r.date_added DESC LIMIT 5");

		
		return $query->rows;
	}
	
	public function getReturnsProduct($start, $limit) {
		

		$query = $this->db->query("SELECT r.return_id, r.order_id, r.firstname, r.lastname, r.product, r.product_id, r.model, r.quantity, rs.name as status, r.date_added FROM `" . DB_PREFIX . "return` r LEFT JOIN " . DB_PREFIX . "return_status rs ON (r.return_status_id = rs.return_status_id) WHERE r.customer_id = '" . $this->customer->getId() . "' AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY r.return_id DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}
	public function get_product_rating($product_id)
	{
		$query = $this->db->query("SELECT TRUNCATE(AVG(rating),1) as average, COUNT(rating) AS total_rating, (SELECT COUNT(*) FROM " . DB_PREFIX . "review WHERE product_id = '".$product_id."' AND status='1' AND rating='1') as one_star_rating , (SELECT COUNT(*) FROM " . DB_PREFIX . "review WHERE product_id = '".$product_id."' AND status='1' AND rating='2') as two_star_rating , (SELECT COUNT(*) FROM " . DB_PREFIX . "review WHERE product_id = '".$product_id."' AND status='1' AND rating='3') as three_star_rating , (SELECT COUNT(*) FROM " . DB_PREFIX . "review WHERE product_id = '".$product_id."' AND status='1' AND rating='4') as four_star_rating , (SELECT COUNT(*) FROM " . DB_PREFIX . "review WHERE product_id = '".$product_id."' AND status='1' AND rating='5') as five_star_rating FROM " . DB_PREFIX . "review WHERE product_id = '".$product_id."' AND status='1'");
		return $query->row;

	}
	
	public function get_wishlist_status($customer_id ,$product_id)
	{	
		$query =  $this->db->query(" SELECT product_id FROM " . DB_PREFIX . "customer_wishlist WHERE product_id ='".$product_id."' AND customer_id='".$customer_id."'");
		if($query->num_rows>0)
		{	
			return true;
		}else{
			return false;
		}
	}
	
	public function get_banner_data()
	{
		$query = $this->db->query(" SELECT * FROM " . DB_PREFIX . "mobile_banner");

        	return $query->rows;
	}

	public function get_voucher_data($token)
	{
		$query = $this->db->query("SELECT data FROM oauth_access_tokens WHERE access_token = '" . $token . "'");
		return json_decode($query->row['data']);
	}



	public function clear_voucher($token){
		
		 $oauth_data = $this->get_auth_data($token);
	
		if(isset($oauth_data)){
			unset($oauth_data['vouchers']);
			unset($oauth_data['shipping_method']);
			unset($oauth_data['payment_method']);
			$this->session->data = $oauth_data;
		}
			
	}
	public function get_auth_data($token)
	{
		$query = $this->db->query("SELECT  data FROM oauth_access_tokens WHERE access_token = '" . $token . "'");
		return json_decode($query->row['data'],true);

		// $query = $this->db->query("SELECT * FROM oauth_access_tokens WHERE data LIKE '%voucher%'");
		 
		// return json_decode($query->rows[3]['data'],true);

	}

	public function addVoucherdata($vouchers,$token)
	{	
		
		$oauth_data = $this->get_auth_data($token);
		$oauth_data['vouchers'][mt_rand()]=$vouchers;
		$this->session->data['vouchers']=$oauth_data['vouchers'];
		
	}

	
	public function removevoucher($key,$token)
	{
		 	$oauth_data = $this->get_auth_data($token);
			if(isset($oauth_data) && !empty($oauth_data['vouchers'])){
				unset($oauth_data['vouchers'][$key]);
				$this->session->data = $oauth_data;
			}
	}
	public function get_voucher_theme_image($voucher_theme_id)
	{
		$query = $this->db->query(" SELECT image FROM " . DB_PREFIX . "voucher_theme WHERE voucher_theme_id='".$voucher_theme_id."'");
		return $query->row['image'];
	}

	public function checkVoucherThemeID($voucher_theme_id='')
	{
		$query = $this->db->query(" SELECT * FROM " . DB_PREFIX . "voucher_theme WHERE voucher_theme_id='".$voucher_theme_id."'");
		if($query->num_rows>0)
		{
			return true;
		}
		else
		{
		 return false;
		}

		 
	}
	
	public function check_cart_id($cart_id)
	{
		$query =  $this->db->query(" SELECT * FROM " . DB_PREFIX . "cart WHERE cart_id ='".$cart_id."'");

		if($query->num_rows){
			return true;
		}else{
			 return false;
		}
		
	}
	public function getProducts($cart_id) {

		$product_query = array();
		$option_data = array();

		$cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE cart_id='".$cart_id."'");
		
		foreach ($cart_query->rows as $cart) {
			$product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store p2s LEFT JOIN " . DB_PREFIX . "product p ON (p2s.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p2s.product_id = '" . (int)$cart['product_id'] . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1'");
				if (isset($product_query->row['quantity'])) {
					$option_data[] = $product_query->row;
				}

				foreach (json_decode($cart['option']) as $product_option_id => $value) {

					$option_query = $this->db->query("SELECT od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$cart['product_id'] . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");
					if ($option_query->num_rows) {
						if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio') {
							$option_value_query = $this->db->query("SELECT ovd.name, pov.quantity FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

							if ($option_value_query->num_rows) {

								$option_data[] = array(
									'name'                    => $option_query->row['name'],
									'value'                   => $option_value_query->row['name'],
									'type'                    => $option_query->row['type'],
									'quantity'                => $option_value_query->row['quantity']
									
									
								);
							}
						} elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
							foreach ($value as $product_option_value_id) {
								$option_value_query = $this->db->query("SELECT pov.quantity, ovd.name FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (pov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

								if ($option_value_query->num_rows) {
									

								
									$option_data[] = array(
										
										'name'                    => $option_query->row['name'],
										'value'                   => $option_value_query->row['name'],
										'type'                    => $option_query->row['type'],
										'quantity'                => $option_value_query->row['quantity']
									);
								}
							}
						} elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
							$option_data[] = array(
								
								'name'                    => $option_query->row['name'],
								'value'                   => $value,
								'type'                    => $option_query->row['type'],
								'quantity'                => ''
							);
							
						}
					}
				}
			
		}
			
		return $option_data;
	}
	public function get_product_options($option,$product_id) {
		$option_data = array();
		$product_query = array();
		$product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store p2s LEFT JOIN " . DB_PREFIX . "product p ON (p2s.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p2s.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1'");
				if (isset($product_query->row['quantity'])) {
					$option_data[] = $product_query->row;
				}
		if(isset($option)) {
		
				foreach ($option as $product_option_id => $value) {

					$option_query = $this->db->query("SELECT od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . $product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");
					if ($option_query->num_rows) {
						if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio') {
							$option_value_query = $this->db->query("SELECT ovd.name, pov.quantity FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

							if ($option_value_query->num_rows) {

								$option_data[] = array(
									'name'                    => $option_query->row['name'],
									'value'                   => $option_value_query->row['name'],
									'type'                    => $option_query->row['type'],
									'quantity'                => $option_value_query->row['quantity']
									
									
								);
							}
						} elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
							foreach ($value as $product_option_value_id) {
								$option_value_query = $this->db->query("SELECT pov.quantity, ovd.name FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (pov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

								if ($option_value_query->num_rows) {
									

								
									$option_data[] = array(
										
										'name'                    => $option_query->row['name'],
										'value'                   => $option_value_query->row['name'],
										'type'                    => $option_query->row['type'],
										'quantity'                => $option_value_query->row['quantity']
									);
								}
							}
						} elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
							$option_data[] = array(
								
								'name'                    => $option_query->row['name'],
								'value'                   => $value,
								'type'                    => $option_query->row['type'],
								'quantity'                => ''
							);
						}
					}
				}
			
		}
			
		return $option_data;
	}

	/*public function getfilter_Products($data = array()) {
		
		$sql = "SELECT p.product_id, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special_";

		

		if (!empty($data['filter_category_id'])) {

				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
			}

			} else {

				$sql .= " FROM " . DB_PREFIX . "product p";
			}

			if(!empty($data['special_product']) && $data['special_product'] =='true')
			{

				$sql .= " LEFT JOIN " . DB_PREFIX . "product_special prs ON (p.product_id = prs.product_id)";
			}


			$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

			if(!empty($data['special_product']) && $data['special_product'] =='true')
			{
				
				$sql .= "AND p.product_id = prs.product_id";
			}

		

		if (!empty($data['filter_category_id'])) {
				
			$category_ids = explode(',', $data['filter_category_id']);
			foreach ($category_ids as $cat_id) {

				$implode[]  = (int)$cat_id;
			
			}
				$sql .= " AND p2c.category_id IN (" . implode(',', $implode) . ")";
			

			if (!empty($data['filter_filter'])) {
				$implode = array();

				$filters = explode(',', $data['filter_filter']);

				foreach ($filters as $filter_id) {
					$implode[] = (int)$filter_id;
				}

				$sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
			}
		}

		
		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
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

				if (!empty($data['filter_description'])) {
					$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

				foreach ($words as $word) {
					$implode[] = "pd.tag LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}

			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}

			$sql .= ")";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$manufacturer_ids = explode(',', $data['filter_manufacturer_id']);
			foreach ($manufacturer_ids as $manufacturer_id) {

				$implode[]  = (int)$manufacturer_id;
			
			}
			$sql .= " AND p.manufacturer_id IN (" . implode(',', $implode) . ")";
		}

		$sql .= " GROUP BY p.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.quantity',
			'p.price',
			'rating',
			'p.sort_order',
			'p.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} elseif ($data['sort'] == 'p.price') {
				$sql .= " ORDER BY (CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END)";
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
		// echo $sql;die;
		$product_data = array();

		$query = $this->db->query($sql);
		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
		}
	

		return $product_data;
	}*/

	public function getfilter_Products($data = array()) {
		$sql = "SELECT p.product_id, p2c.category_id, 
		(SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, 
		(SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special,
		(SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating
		FROM `".DB_PREFIX."product_to_store` as p2s LEFT 
		JOIN `" . DB_PREFIX . "product` as p ON (p2s.product_id = p.product_id) 
		JOIN `".DB_PREFIX."product_description` as pd ON p.product_id = pd.product_id 
		JOIN `".DB_PREFIX."product_to_category` p2c ON p.product_id=p2c.product_id 
		LEFT JOIN " . DB_PREFIX . "product_special prs ON (p.product_id = prs.product_id) 
		WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p.status = '1' AND p.date_available <= NOW()";

		if(!empty($data['filter_name'])) {
			// $sql .= " AND (pd.name LIKE '%".$data['filter_name']."%'  OR  pd.description LIKE '%".$data['filter_name']."%' ";
			$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));
			$implode = [];
			foreach ($words as $word) {
				$implode['tag'][] = "pd.tag LIKE '%" . $this->db->escape($word) . "%'";
				$implode['name'][] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
				$implode['description'][] = "pd.description LIKE '%" . $this->db->escape($word) . "%'";
			}
			if (!empty($implode['tag'])) {
				$sql .= " AND ( ( " . implode(" AND ", $implode['name']) . " ) OR ( " . implode(" AND ", $implode['description']) . " ) OR ( " . implode(" AND ", $implode['tag']) . " ) )";
			}
			// $sql .= " )";
		}

		if(!empty($data['filter_category_id'])) {
			$implode = [];
			$category_ids = explode(',', $data['filter_category_id']);
			foreach ($category_ids as $cat_id) {
				$implode[]  = (int)$cat_id;
			}
			$sql .= " AND p2c.category_id IN (" . implode(',', $implode) . ")";
		}

		if(!empty($data['filter_manufacturer_id'])) {
			$implode = [];
			$manufacturer_ids = explode(',', $data['filter_manufacturer_id']);
			foreach ($manufacturer_ids as $manufacturer_id) {
				$implode[]  = (int)$manufacturer_id;
			}
			$sql .= " AND p.manufacturer_id IN (" . implode(',', $implode) . ")";
		}

		// var_dump($data);
		if($data['special_product']) {
			$sql .= " AND prs.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((prs.date_start = '0000-00-00' OR prs.date_start < NOW()) AND (prs.date_end = '0000-00-00' OR prs.date_end > NOW()))";
		}

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.quantity',
			'p.price',
			'rating',
			'p.sort_order',
			'p.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} elseif ($data['sort'] == 'p.price') {
				$sql .= " ORDER BY (CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END)";
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


		// echo $sql;


		$query = $this->db->query($sql);

		$product_data = [];
		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
		}
		return $product_data;
	}

	public function getManufacturers($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";


			if (isset($data['sort'])) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY name";
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
			$manufacturer_data = $this->cache->get('manufacturer.' . (int)$this->config->get('config_store_id'));

			if (!$manufacturer_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY name");

				$manufacturer_data = $query->rows;

				$this->cache->set('manufacturer.' . (int)$this->config->get('config_store_id'), $manufacturer_data);
			}

			return $manufacturer_data;
		}
	}

	
}