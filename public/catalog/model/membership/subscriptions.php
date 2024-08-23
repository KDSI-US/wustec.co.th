<?php
/* This file is under Git Control by KDSI. */
class ModelMembershipSubscriptions extends Model
{
	public function getActivePlan($customer_id)
	{
		$today = date('Y-m-d H:i:s');
		$sql = "SELECT mp.* FROM " . DB_PREFIX . "mpplan_customer mp LEFT JOIN " . DB_PREFIX . "order o ON (mp.order_id = o.order_id) WHERE mp.customer_id = '" . (int)$customer_id . "' AND o.order_status_id = '" . (int)$this->config->get('mpplan_status_id') . "'";

		$sql .= " AND '" . $this->db->escape($today) . "' BETWEEN mp.start_date and mp.end_date";

		$sql .= " AND active = '1'";

		$query = $this->db->query($sql);

		return $query->row;
	}

	public function getCurrentPlans($customer_id)
	{
		$today = date('Y-m-d H:i:s');
		$sql = "SELECT mp.* FROM " . DB_PREFIX . "mpplan_customer mp LEFT JOIN " . DB_PREFIX . "order o ON (mp.order_id = o.order_id) WHERE mp.customer_id = '" . (int)$customer_id . "' AND o.order_status_id = '" . (int)$this->config->get('mpplan_status_id') . "'";

		$sql .= " AND '" . $this->db->escape($today) . "' BETWEEN mp.start_date and mp.end_date";

		$sql .= " ORDER BY o.date_added DESC";

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getOldPlans($customer_id, $start = 0, $limit = 10)
	{
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$today = date('Y-m-d H:i:s');
		$sql = "SELECT mp.* FROM " . DB_PREFIX . "mpplan_customer mp LEFT JOIN " . DB_PREFIX . "order o ON (mp.order_id = o.order_id) WHERE mp.customer_id = '" . (int)$customer_id . "' AND o.order_status_id = '" . (int)$this->config->get('mpplan_status_id') . "'";

		$sql .= " AND end_date < '" . $today . "'";

		$sql .= " ORDER BY o.date_added DESC";

		$sql .= " LIMIT " . (int)$start . "," . (int)$limit;

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalOldPlans($customer_id)
	{
		$today = date('Y-m-d H:i:s');
		$sql = "SELECT COUNT(*) as total FROM " . DB_PREFIX . "mpplan_customer mp LEFT JOIN " . DB_PREFIX . "order o ON (mp.order_id = o.order_id) WHERE mp.customer_id = '" . (int)$customer_id . "' AND o.order_status_id = '" . (int)$this->config->get('mpplan_status_id') . "'";

		$sql .= " AND end_date < '" . $today . "'";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getPaymentHistoryPlans($customer_id, $start = 0, $limit = 10)
	{
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$sql = "SELECT o.currency_code,o.currency_value,mp.plan_name,mp.duration_type,mp.duration_value,o.date_added,op.price,os.name as order_status FROM " . DB_PREFIX . "mpplan_customer mp LEFT JOIN " . DB_PREFIX . "order o ON (mp.order_id = o.order_id) LEFT JOIN " . DB_PREFIX . "order_product op ON(mp.order_product_id=op.order_product_id) LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id) WHERE mp.customer_id = '" . (int)$customer_id . "' AND o.order_status_id = '" . (int)$this->config->get('mpplan_status_id') . "'";

		$sql .= " ORDER BY o.date_added DESC";

		$sql .= " LIMIT " . (int)$start . "," . (int)$limit;

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalPaymentHistoryPlans($customer_id)
	{
		$sql = "SELECT COUNT(*) as total FROM " . DB_PREFIX . "mpplan_customer mp LEFT JOIN " . DB_PREFIX . "order o ON (mp.order_id = o.order_id) LEFT JOIN " . DB_PREFIX . "order_product op ON(mp.order_product_id=op.order_product_id) LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id) WHERE mp.customer_id = '" . (int)$customer_id . "' AND o.order_status_id = '" . (int)$this->config->get('mpplan_status_id') . "'";

		$sql .= " ORDER BY o.date_added DESC";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getCustomerPlan($customer_id, $mpplan_customer_id)
	{
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpplan_customer WHERE customer_id = '" . (int)$customer_id . "' AND mpplan_customer_id = '" . (int)$mpplan_customer_id . "'");

		return $query->row;
	}

	public function getCustomerGroupDiscount($mpplan_id, $customer_group_id)
	{
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpplan_discount WHERE mpplan_id = '" . (int)$mpplan_id . "' AND customer_group_id = '" . (int)$customer_group_id . "'");

		if ($query->row) {
			$discount = $query->row['discount'];
		} else {
			$discount = 0;
		}

		return $discount;
	}

	public function getMpplanProducts($mpplan_id)
	{
		$product_data = array();
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpplan_to_product WHERE mpplan_id = '" . (int)$mpplan_id . "'");

		foreach ($query->rows as $row) {
			$product_data[] = $row['product_id'];
		}

		return $product_data;
	}

	public function addDefaultPlan($customer_id, $mpplan_customer_id)
	{
		$this->db->query("UPDATE " . DB_PREFIX . "mpplan_customer SET active = '0' WHERE customer_id = '" . (int)$customer_id . "'");

		$this->db->query("UPDATE " . DB_PREFIX . "mpplan_customer SET active = '1' WHERE customer_id = '" . (int)$customer_id . "' AND mpplan_customer_id = '" . (int)$mpplan_customer_id . "'");
	}

	public function getDefaultPlan($customer_id)
	{
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpplan_customer WHERE customer_id = '" . (int)$customer_id . "' AND active = '1'");

		return $query->row;
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
