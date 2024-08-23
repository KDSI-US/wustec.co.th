<?php
class ModelExtensionModuleEmailTemplateProduct extends Model {

	public function getProduct($product_id, $cond = array()) {
        $language_id = !empty($cond['language_id']) ? $cond['language_id'] : $this->config->get('config_language_id');
        $customer_group_id = !empty($cond['customer_group_id']) ? $cond['customer_group_id'] : $this->config->get('config_customer_group_id');
        $store_id = isset($cond['store_id']) ? $cond['store_id'] : 0;

        $sql = "SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$customer_group_id . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$customer_group_id . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND customer_group_id = '" . (int)$customer_group_id . "') AS reward, (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int)$language_id . "') AS stock_status, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int)$language_id . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int)$language_id . "') AS length_class, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, p.sort_order FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)";

        $sql .= " WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$language_id . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = " . (int)$store_id;

        if (!empty($cond['min_stock'])) {
            $sql .= " AND p.quantity > " . (int)$cond['min_stock'];
        }

		$query = $this->db->query($sql);

		if ($query->num_rows) {
			return array(
				'product_id'       => $query->row['product_id'],
				'name'             => $query->row['name'],
				'description'      => $query->row['description'],
				'meta_description' => $query->row['meta_description'],
				'meta_keyword'     => $query->row['meta_keyword'],
				'tag'              => isset($query->row['tag']) ? $query->row['tag'] : '',
				'model'            => $query->row['model'],
				'sku'              => isset($query->row['sku']) ? $query->row['sku'] : '',
				'upc'              => isset($query->row['upc']) ? $query->row['upc'] : '',
				'ean'              => isset($query->row['ean']) ? $query->row['ean'] : '',
				'jan'              => isset($query->row['jan']) ? $query->row['jan'] : '',
				'isbn'             => isset($query->row['isbn']) ? $query->row['isbn'] : '',
				'mpn'              => isset($query->row['mpn']) ? $query->row['mpn'] : '',
				'location'         => $query->row['location'],
				'quantity'         => $query->row['quantity'],
				'stock_status'     => $query->row['stock_status'],
				'image'            => $query->row['image'],
				'manufacturer_id'  => $query->row['manufacturer_id'],
				'manufacturer'     => $query->row['manufacturer'],
				'price'            => ($query->row['discount'] ? $query->row['discount'] : $query->row['price']),
				'special'          => $query->row['special'],
				'reward'           => $query->row['reward'],
				'points'           => $query->row['points'],
				'tax_class_id'     => $query->row['tax_class_id'],
				'date_available'   => $query->row['date_available'],
				'weight'           => $query->row['weight'],
				'weight_class_id'  => $query->row['weight_class_id'],
				'length'           => $query->row['length'],
				'width'            => $query->row['width'],
				'height'           => $query->row['height'],
				'length_class_id'  => $query->row['length_class_id'],
				'subtract'         => $query->row['subtract'],
				'rating'           => round($query->row['rating']),
				'reviews'          => $query->row['reviews'] ? $query->row['reviews'] : 0,
				'minimum'          => $query->row['minimum'],
				'sort_order'       => $query->row['sort_order'],
				'status'           => $query->row['status'],
				'date_added'       => $query->row['date_added'],
				'date_modified'    => $query->row['date_modified'],
				'viewed'           => $query->row['viewed']
			);
		} else {
			return false;
		}
	}

	public function getProductSpecials($limit, $cond = array()) {
        $customer_id = !empty($cond['customer_id']) ? $cond['customer_id'] : 0;
        $language_id = !empty($cond['language_id']) ? $cond['language_id'] : $this->config->get('config_language_id');
        $customer_group_id = !empty($cond['customer_group_id']) ? $cond['customer_group_id'] : $this->config->get('config_customer_group_id');
        $store_id = isset($cond['store_id']) ? $cond['store_id'] : 0;

		$sql = "SELECT DISTINCT ps.product_id FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)";

		if ($customer_id) {
			$sql .= " LEFT JOIN " . DB_PREFIX . "emailtemplate_showcase_log esl ON (p.product_id = esl.product_id AND esl.customer_id = '" . (int)$customer_id . "')";
		}

		$sql .= " WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = " . (int)$store_id . " AND ps.customer_group_id = '" . (int)$customer_group_id . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))";

		if ($customer_id) {
			$sql .= " AND p.product_id NOT IN (SELECT cop.product_id FROM " . DB_PREFIX . "order_product cop, `" . DB_PREFIX . "order` co WHERE co.order_id = cop.order_id AND co.customer_id = '" . (int)$customer_id . "')";
		}

        if (!empty($cond['min_stock'])) {
            $sql .= " AND p.quantity > " . (int)$cond['min_stock'];
        }

		$sql .= " GROUP BY ps.product_id";

		if ($customer_id) {
			$sql .= " ORDER BY (esl.emailtemplate_showcase_log_count IS NULL) DESC, esl.emailtemplate_showcase_log_count ASC, p.sort_order ASC, LCASE(pd.name) ASC";
		} else {
			$sql .= " ORDER BY p.sort_order ASC, LCASE(pd.name) ASC";
		}

		$sql .= " LIMIT " . (int)$limit;

		$query = $this->db->query($sql);

		$product_data = array();

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id'], array('language_id' => $language_id, 'store_id' => $store_id, 'customer_group_id' => $customer_group_id));
		}

		return $product_data;
	}

	public function getLatestProducts($limit, $cond = array()) {
        $customer_id = !empty($cond['customer_id']) ? $cond['customer_id'] : 0;
        $language_id = !empty($cond['language_id']) ? $cond['language_id'] : $this->config->get('config_language_id');
        $customer_group_id = !empty($cond['customer_group_id']) ? $cond['customer_group_id'] : $this->config->get('config_customer_group_id');
        $store_id = isset($cond['store_id']) ? $cond['store_id'] : 0;

		$sql = "SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)";

		if ($customer_id) {
			$sql .= " LEFT JOIN " . DB_PREFIX . "emailtemplate_showcase_log esl ON (p.product_id = esl.product_id AND esl.customer_id = '" . (int)$customer_id . "')";
		}

		$sql .= " WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = " . (int)$store_id;

		if ($customer_id) {
			$sql .= " AND p.product_id NOT IN (SELECT cop.product_id FROM " . DB_PREFIX . "order_product cop, `" . DB_PREFIX . "order` co WHERE co.order_id = cop.order_id AND co.customer_id = '" . (int)$customer_id . "')";
		}

        if (!empty($cond['min_stock'])) {
            $sql .= " AND p.quantity > " . (int)$cond['min_stock'];
        }

		if ($customer_id) {
			$sql .= " ORDER BY (esl.emailtemplate_showcase_log_count IS NULL) DESC, esl.emailtemplate_showcase_log_count ASC, p.date_added DESC";
		} else {
			$sql .= " ORDER BY p.date_added DESC";
		}

		$sql .= " LIMIT " . (int)$limit;

		$query = $this->db->query($sql);

		$product_data = array();

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id'], array('language_id' => $language_id, 'store_id' => $store_id, 'customer_group_id' => $customer_group_id));
		}

		return $product_data;
	}

	public function getPopularProducts($limit, $cond = array()) {
        $customer_id = !empty($cond['customer_id']) ? $cond['customer_id'] : 0;
        $language_id = !empty($cond['language_id']) ? $cond['language_id'] : $this->config->get('config_language_id');
        $customer_group_id = !empty($cond['customer_group_id']) ? $cond['customer_group_id'] : $this->config->get('config_customer_group_id');
        $store_id = isset($cond['store_id']) ? $cond['store_id'] : 0;

		$sql = "SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)";

		if ($customer_id) {
			$sql .= " LEFT JOIN " . DB_PREFIX . "emailtemplate_showcase_log esl ON (p.product_id = esl.product_id AND esl.customer_id = '" . (int)$customer_id . "')";
		}

        if (!empty($cond['min_stock'])) {
            $sql .= " AND p.quantity > " . (int)$cond['min_stock'];
        }

		$sql .= " WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = " . (int)$store_id;

		if ($customer_id) {
			$sql .= " AND p.product_id NOT IN (SELECT cop.product_id FROM " . DB_PREFIX . "order_product cop, `" . DB_PREFIX . "order` co WHERE co.order_id = cop.order_id AND co.customer_id = '" . (int)$customer_id . "')";
		}

		if ($customer_id) {
			$sql .= " ORDER BY (esl.emailtemplate_showcase_log_count IS NULL) DESC, esl.emailtemplate_showcase_log_count ASC, p.viewed, p.date_added DESC";
		} else {
			$sql .= " ORDER BY p.viewed, p.date_added DESC";
		}

		$sql .= " LIMIT " . (int)$limit;

		$query = $this->db->query($sql);

		$product_data = array();

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id'], array('language_id' => $language_id, 'store_id' => $store_id, 'customer_group_id' => $customer_group_id));
		}

		return $product_data;
	}

	public function getRandomProducts($limit, $cond = array()) {
        $language_id = !empty($cond['language_id']) ? $cond['language_id'] : $this->config->get('config_language_id');
        $customer_group_id = !empty($cond['customer_group_id']) ? $cond['customer_group_id'] : $this->config->get('config_customer_group_id');
        $store_id = isset($cond['store_id']) ? $cond['store_id'] : 0;

		$sql = "SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)";

		$sql .= " WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = " . (int)$store_id;

        if (!empty($cond['min_stock'])) {
            $sql .= " AND p.quantity > " . (int)$cond['min_stock'];
        }

		$sql .= " ORDER BY rand() LIMIT " . (int)$limit;

		$query = $this->db->query($sql);

		$product_data = array();

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id'], array('language_id' => $language_id, 'store_id' => $store_id, 'customer_group_id' => $customer_group_id));
		}

		return $product_data;
	}

	public function getBestSellerProducts($limit, $cond = array()) {
        $customer_id = !empty($cond['customer_id']) ? $cond['customer_id'] : 0;
        $language_id = !empty($cond['language_id']) ? $cond['language_id'] : $this->config->get('config_language_id');
        $customer_group_id = !empty($cond['customer_group_id']) ? $cond['customer_group_id'] : $this->config->get('config_customer_group_id');
        $store_id = isset($cond['store_id']) ? $cond['store_id'] : 0;

		$sql = "SELECT op.product_id, COUNT(*) AS total FROM " . DB_PREFIX . "order_product op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id) LEFT JOIN `" . DB_PREFIX . "product` p ON (op.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)";

		if ($customer_id) {
			$sql .= " LEFT JOIN " . DB_PREFIX . "emailtemplate_showcase_log esl ON (p.product_id = esl.product_id AND esl.customer_id = '" . (int)$customer_id . "')";
		}

		$sql .= " WHERE o.order_status_id > '0' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = " . (int)$store_id;

		if ($customer_id) {
			$sql .= " AND p.product_id NOT IN (SELECT cop.product_id FROM " . DB_PREFIX . "order_product cop, `" . DB_PREFIX . "order` co WHERE co.order_id = cop.order_id AND co.customer_id = '" . (int)$customer_id . "')";
		}

        if (!empty($cond['min_stock'])) {
            $sql .= " AND p.quantity > " . (int)$cond['min_stock'];
        }

		$sql .= " GROUP BY op.product_id";

		if ($customer_id) {
			$sql .= " ORDER BY (esl.emailtemplate_showcase_log_count IS NULL) DESC, esl.emailtemplate_showcase_log_count ASC, total DESC";
		} else {
			$sql .= " ORDER BY total DESC";
		}

		$sql .= " LIMIT " . (int)$limit;

		$query = $this->db->query($sql);

		$product_data = array();

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id'], array('language_id' => $language_id, 'store_id' => $store_id, 'customer_group_id' => $customer_group_id));
		}

		return $product_data;
	}

	public function getProductRelated($product_id, $cond = array()) {
        $customer_id = !empty($cond['customer_id']) ? $cond['customer_id'] : 0;
        $language_id = !empty($cond['language_id']) ? $cond['language_id'] : $this->config->get('config_language_id');
        $customer_group_id = !empty($cond['customer_group_id']) ? $cond['customer_group_id'] : $this->config->get('config_customer_group_id');
        $store_id = isset($cond['store_id']) ? $cond['store_id'] : 0;

		$sql = "SELECT * FROM " . DB_PREFIX . "product_related pr LEFT JOIN " . DB_PREFIX . "product p ON (pr.related_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)";

		if ($customer_id) {
			$sql .= " LEFT JOIN " . DB_PREFIX . "emailtemplate_showcase_log esl ON (p.product_id = esl.product_id AND esl.customer_id = '" . (int)$customer_id . "')";
		}

		$sql .= " WHERE pr.product_id = '" . (int)$product_id . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = " . (int)$store_id;

		if ($customer_id) {
			$sql .= " AND p.product_id NOT IN (SELECT cop.product_id FROM " . DB_PREFIX . "order_product cop, `" . DB_PREFIX . "order` co WHERE co.order_id = cop.order_id AND co.customer_id = '" . (int)$customer_id . "')";
		}

        if (!empty($cond['min_stock'])) {
            $sql .= " AND p.quantity > " . (int)$cond['min_stock'];
        }

		if ($customer_id) {
			$sql .= " ORDER BY (esl.emailtemplate_showcase_log_count IS NULL) DESC, esl.emailtemplate_showcase_log_count ASC, p.date_added DESC";
		} else {
			$sql .= " ORDER BY p.date_added DESC";
		}

		$query = $this->db->query($sql);

		$product_data = array();

		foreach ($query->rows as $result) {
			$product_data[$result['related_id']] = $this->getProduct($result['related_id'], array('language_id' => $language_id, 'store_id' => $store_id, 'customer_group_id' => $customer_group_id, 'customer_id' => $customer_id));
		}

		return $product_data;
	}

	public function getProductSpecialPrice($product_id, $cond = array()) {
        $customer_group_id = !empty($cond['customer_group_id']) ? $cond['customer_group_id'] : $this->config->get('config_customer_group_id');

        $result = $this->db->query("SELECT ps.price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = '" . (int)$product_id . "' AND ps.customer_group_id = '" . (int)$customer_group_id . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1");

        if ($result->rows) {
            return $result->rows[0]['price'];
        }
    }

}
