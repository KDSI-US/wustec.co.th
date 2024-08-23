<?php

class ModelExtensionReportLowStock extends Model
{

	public function getProducts($data = array())
	{
		$sql = "
			SELECT * 
			FROM `" . DB_PREFIX . "product` AS p 
			LEFT JOIN `" . DB_PREFIX . "product_description` AS pd 
			ON (p.product_id = pd.product_id) 
			WHERE 
				pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ";

		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%' ";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%' ";
		}

		if (!empty($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%' ";
		}

		if (isset($data['filter_quantity']) && $data['filter_quantity'] !== '') {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "' ";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "' ";
		}

		if (isset($data['filter_quantity_check']) && !is_null($data['filter_quantity_check'])) {
			$sql .= " AND p.quantity <=  '" . (int)$data['filter_quantity_check'] . "' ";
		}

		$sql .= " GROUP BY p.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.price',
			'p.quantity',
			'p.status',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'] . " ";
		} else {
			$sql .= " ORDER BY pd.name ";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC ";
		} else {
			$sql .= " ASC ";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'] . " ";
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}
	public function getTotalProducts($data = array())
	{
		$sql = "
			SELECT 
				COUNT(DISTINCT p.product_id) AS total 
			FROM `" . DB_PREFIX . "product` AS p 
			LEFT JOIN `" . DB_PREFIX . "product_description` AS pd 
			ON (p.product_id = pd.product_id) ";

		$sql .= "WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ";

		if (isset($data['filter_quantity_check']) && !is_null($data['filter_quantity_check'])) {
			$sql .= " AND p.quantity >=  '" . (int)$data['filter_quantity_check'] . "' ";
		}


		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%' ";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%' ";
		}

		if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%' ";
		}

		if (isset($data['filter_quantity']) && $data['filter_quantity'] !== '') {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "' ";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "' ";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getProductSpecials($product_id)
	{
		$strSql = "
			SELECT * 
			FROM `" . DB_PREFIX . "product_special` 
			WHERE 
				`product_id` = '" . (int)$product_id . "' 
			ORDER BY priority, price ";
		$query = $this->db->query($strSql);

		return $query->rows;
	}
}
