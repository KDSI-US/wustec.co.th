<?php
/* This file is under Git Control by KDSI. */
class ModelMembershipPrices extends Model
{
	public function getMpplans()
	{
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpplan mp LEFT JOIN " . DB_PREFIX . "mpplan_description mpd ON (mp.mpplan_id = mpd.mpplan_id) LEFT JOIN " . DB_PREFIX . "mpplan_to_store i2s ON (mp.mpplan_id = i2s.mpplan_id) WHERE mpd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND mp.status = '1' ORDER BY mp.sort_order,mpd.name ASC");
		return $query->rows;
	}

	public function getMpplan($mpplan_id)
	{
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpplan mp LEFT JOIN " . DB_PREFIX . "mpplan_description mpd ON (mp.mpplan_id = mpd.mpplan_id) LEFT JOIN " . DB_PREFIX . "mpplan_to_store i2s ON (mp.mpplan_id = i2s.mpplan_id)  WHERE mpd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND mp.mpplan_id = '" . (int)$mpplan_id . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND mp.status = '1'");

		return $query->row;
	}

	public function getMpplanInfo($mpplan_id)
	{
		$query = $this->db->query("SELECT mpd.name FROM " . DB_PREFIX . "mpplan_info mp LEFT JOIN " . DB_PREFIX . "mpplan_info_description mpd ON (mp.mpplan_info_id = mpd.mpplan_info_id) WHERE mp.mpplan_id = '" . (int)$mpplan_id . "' AND mpd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY mp.sort_order,mpd.name ASC");

		return $query->rows;
	}

	public function getProducts($data = array())
	{
		$this->load->model('catalog/product');

		$sql = "SELECT p.product_id, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special";

		$sql .= " FROM " . DB_PREFIX . "product p";

		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "mpplan_to_product m2p ON (p.product_id = m2p.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND m2p.mpplan_id = '" . (int)$data['mpplan_id'] . "'";

		$sql .= " GROUP BY p.product_id";
		$sql .= " ORDER BY p.sort_order, LCASE(pd.name) ASC";


		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$product_data = array();

		$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
		}

		return $product_data;
	}

	public function getMpplanDiscount($mpplan_id, $customer_group_id)
	{
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpplan_discount WHERE mpplan_id = '" . (int)$mpplan_id . "' AND customer_group_id = '" . (int)$customer_group_id . "'");

		return $query->row;
	}

	public function getProductPlans($product_id)
	{
		$strSql = "
			SELECT * 
			FROM `" . DB_PREFIX . "mpplan_to_product` AS mp2p 
			LEFT JOIN `" . DB_PREFIX . "mpplan` AS mp 
			ON(mp2p.mpplan_id = mp.mpplan_id) 
			LEFT JOIN `" . DB_PREFIX . "mpplan_description` AS mpd 
			ON (mp.mpplan_id = mpd.mpplan_id) 
			WHERE 
				mp2p.product_id = '" . (int)$product_id . "' 
				AND mpd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
				AND mp.status = '1' 
			ORDER BY mp.sort_order,mpd.name ASC";
		$query = $this->db->query($strSql);

		return $query->rows;
	}

	public function getactiveTheme()
	{
		/* Theme Work Starts */
		if ($this->config->get('config_theme')) {
			$custom_themename = $this->config->get('config_theme');
		} else if ($this->config->get('theme_default_directory')) {
			$custom_themename = $this->config->get('theme_default_directory');
		} else if ($this->config->get('config_template')) {
			$custom_themename = $this->config->get('config_template');
		} else {
			$custom_themename = 'default';
		}

		if (strpos($this->config->get('config_template'), 'journal2') === 0) {
			$custom_themename = 'journal2';
		}
		if (defined('JOURNAL3_ACTIVE')) {
			$custom_themename = 'journal3';
		}

		if (empty($custom_themename)) {
			$custom_themename = 'default';
		}

		return $custom_themename;
		/* Theme Work Ends */
	}
}
