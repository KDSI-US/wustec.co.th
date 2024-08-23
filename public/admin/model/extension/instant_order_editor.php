<?php
class ModelExtensionInstantOrderEditor extends Model
{
	protected $fields = [
		'o.order_id'							=> 'int',
		'o.invoice_no'							=> 'int',
		'o.invoice_prefix'						=> 'varchar',
		'o.store_id'							=> 'int',
		'o.store_name' 							=> 'varchar',
		'o.store_url' 							=> 'varchar',
		'o.customer_id' 						=> 'int',
		'o.customer_group_id'					=> 'int',
		'o.firstname' 							=> 'varchar',
		'o.lastname'							=> 'varchar',
		'o.email'								=> 'varchar',
		'o.telephone' 							=> 'varchar',
		'o.fax'									=> 'varchar',
		'o.custom_field' 						=> 'text',
		'o.payment_firstname' 					=> 'varchar',
		'o.payment_lastname' 					=> 'varchar',
		'o.payment_company' 					=> 'varchar',
		'o.payment_address_1' 					=> 'varchar',
		'o.payment_address_2' 					=> 'varchar',
		'o.payment_city' 						=> 'varchar',
		'o.payment_postcode' 					=> 'varchar',
		'o.payment_country' 					=> 'varchar',
		'o.payment_country_id' 					=> 'int',
		'o.payment_zone' 						=> 'varchar',
		'o.payment_zone_id' 					=> 'int',
		'o.payment_address_format' 				=> 'text',
		'o.payment_custom_field' 				=> 'text',
		'o.payment_method' 						=> 'varchar',
		'o.payment_code' 						=> 'varchar',
		'o.shipping_firstname' 					=> 'varchar',
		'o.shipping_lastname' 					=> 'varchar',
		'o.shipping_company' 					=> 'varchar',
		'o.shipping_address_1' 					=> 'varchar',
		'o.shipping_address_2' 					=> 'varchar',
		'o.shipping_city' 						=> 'varchar',
		'o.shipping_postcode' 					=> 'varchar',
		'o.shipping_country' 					=> 'varchar',
		'o.shipping_country_id' 				=> 'int',
		'o.shipping_zone' 						=> 'varchar',
		'o.shipping_zone_id' 					=> 'int',
		'o.shipping_address_format' 			=> 'text',
		'o.shipping_custom_field' 				=> 'text',
		'o.shipping_method' 					=> 'varchar',
		'o.shipping_code' 						=> 'varchar',
		'o.comment' 							=> 'text',
		'o.total' 								=> 'decimal',
		'o.order_status_id' 					=> 'int',
		'o.affiliate_id' 						=> 'int',
		'o.commission' 							=> 'decimal',
		'o.marketing_id' 						=> 'int',
		'o.tracking' 							=> 'varchar',
		'o.language_id' 						=> 'int',
		'o.currency_id' 						=> 'int',
		'o.currency_code' 						=> 'varchar',
		'o.currency_value' 						=> 'decimal',
		'o.ip' 									=> 'varchar',
		'o.forwarded_ip' 						=> 'varchar',
		'o.user_agent' 							=> 'varchar',
		'o.accept_language' 					=> 'varchar',
		'o.date_added' 							=> 'alias',
		'o.date_modified' 						=> 'alias',
		'o.customer_name' 						=> 'alias',
		'o.payment_address'						=> 'alias',
		'o.shipping_address'					=> 'alias',
		'op.product_id' 						=> 'int',
		'os.name'								=> 'varchar',
		'o.store_name'							=> 'alias',
		'o.tracking_no' 						=> 'varchar',
	];

	protected $aliases = [
		'o.customer_name' 						=> "CONCAT(c.firstname, ' ', c.lastname)",
		'o.store_name' 							=> "IF (o.store_id != 0, s.name, 'Default Store')",
		'o.payment_address' 					=> "CONCAT(
				o.payment_firstname, ' ', o.payment_lastname, ' ',
				o.payment_company, ' ',
				o.payment_address_1, ' ', o.payment_address_2, ' ',
				o.payment_city, ' ', o.payment_postcode, ' ',
				o.payment_country, ' ', o.payment_zone
			)",
		'o.shipping_address' 					=> "CONCAT(
				o.shipping_firstname, ' ', o.shipping_lastname, ' ',
				o.shipping_company, ' ',
				o.shipping_address_1, ' ', o.shipping_address_2, ' ',
				o.shipping_city, ' ', o.shipping_postcode, ' ',
				o.shipping_country, ' ', o.shipping_zone
			)",
		'o.date_added' 							=> "DATE_FORMAT(o.date_added, '%Y-%m-%d')",
		'o.date_modified' 						=> "DATE_FORMAT(o.date_modified, '%Y-%m-%d')"
	];

	public function getOrders($data = [])
	{
		/* Add product inner join if the search contain also product */
		$product_sql = '';
		if ($this->hasProductFilter($data['filter'])) {
			$product_sql = " INNER JOIN `" . DB_PREFIX . "order_product` AS op ON (op.order_id = o.order_id) ";
		}
		$sql = "
			SELECT 
				o.*, 
				CONCAT(c.firstname, ' ', c.lastname) AS customer_name, 
				os.name AS order_status_name, 
				cur.title AS currency_name, 
				cgd.name AS customer_group_name, 
				CONCAT( 
					o.payment_firstname, ' ', o.payment_lastname, ' ', 
					o.payment_company, ' ', 
					o.payment_address_1, ' ', o.payment_address_2, ' ', 
					o.payment_city, ' ', o.payment_postcode, ' ', 
					o.payment_country, ' ', o.payment_zone 
				) AS payment_address, 
				CONCAT( 
					o.shipping_firstname, ' ', o.shipping_lastname, ' ', 
					o.shipping_company, ' ', 
					o.shipping_address_1, ' ', o.shipping_address_2, ' ', 
					o.shipping_city, ' ', o.shipping_postcode, ' ', 
					o.shipping_country, ' ', o.shipping_zone 
				) AS shipping_address, 
				IF (o.store_id != 0, s.name, 'Default Store') AS store_name 
			FROM 
				`" . DB_PREFIX . "customer_group` AS cg, 
				`" . DB_PREFIX . "customer_group_description` AS cgd, 
				`" . DB_PREFIX . "order_status` AS os, 
				`" . DB_PREFIX . "order` AS o 
				" . $product_sql . " 
			LEFT JOIN `" . DB_PREFIX . "customer` AS c ON (c.customer_id = o.customer_id) 
			LEFT JOIN `" . DB_PREFIX . "currency` AS cur ON (cur.currency_id = o.currency_id) 
			LEFT JOIN `" . DB_PREFIX . "store` AS s ON (s.store_id = o.store_id) 
			WHERE 
				cg.customer_group_id = o.customer_group_id  
				AND cg.customer_group_id = cgd.customer_group_id 
				AND o.order_status_id = os.order_status_id 
				AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' 
				AND cgd.language_id = '" . (int)$this->config->get('config_language_id') . "' ";
		/* where */
		if (isset($data['filter'])) {
			foreach ($data['filter'] as $value) {
				$sql .= $this->filter($value['field'], $value['value']);
			}
		}
		/* order */
		if (isset($data['order'])) {
			$sql .= $this->order($data['order']);
		}
		/* limit */
		$data['start'] = isset($data['start']) ? (int)$data['start'] : 0;
		$data['limit'] = isset($data['limit']) ? (int)$data['limit'] : 20;
		$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		$query = $this->db->query($sql);
		$rows = $query->rows;
		foreach ($rows as $key => $row) {
			$rows[$key]['products'] = $this->getProducts($row['order_id']);
		}
		return $rows;
	}

	protected function hasProductFilter($data)
	{
		$product_filter = array_filter($data, function ($v) {
			if ($v['field'] == 'op.product_id') {
				return true;
			}
		});
		if (count($product_filter) > 0) {
			return true;
		}
		return false;
	}

	/**
	 * find number of rows
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function getTotalOrders($data = [])
	{
		$product_sql = '';
		if ($this->hasProductFilter($data['filter'])) {
			$product_sql = " INNER JOIN `" . DB_PREFIX . "order_product` AS op ON (op.order_id = o.order_id) ";
		}
		$sql = "
			SELECT 
				COUNT(*) AS total 
			FROM 
				`" . DB_PREFIX . "order` AS o 
				" . $product_sql . " 
			WHERE 1=1 ";
		/* where */
		if (isset($data['filter'])) {
			foreach ($data['filter'] as $value) {
				$sql .= $this->filter($value['field'], $value['value']);
			}
		}
		$query = $this->db->query($sql);
		return $query->row['total'];
	}

	/**
	 * Make where condition for the sql
	 * @param string $field
	 * @param string $value
	 * @return $sql;
	 */
	protected function filter($field, $value)
	{
		if (!in_array($field, array_keys($this->fields)) && !in_array($field, array_keys($this->aliases))) {
			return '';
		}
		$sql = '';
		switch ($this->fields[$field]) {
			case 'decimal':
			case 'int':
			case 'datetime':
				if (preg_match('/^([0-9\.\-]+)(\>|\&gt;)$/', $value, $matches)) { //case 10>
					$sql = " AND ";
					$sql .= $field . " >= '" . $this->db->escape($matches[1]) . "' ";
				} elseif (preg_match('/^(\<|\&lt;)([0-9\.\-]+)$/', $value, $matches)) { //case <10
					$sql = " AND ";
					$sql .= $field . " <= '" . $this->db->escape($matches[2]) . "' ";
				} elseif (preg_match('/^([0-9\.\-]+)(<|\&lt;)([0-9\.\-]+)$/', $value, $matches)) { //case 5<10
					$sql = " AND ";
					$sql .= $field;
					$sql .= " BETWEEN ";
					$sql .= "'" . $this->db->escape($matches[1]) . "' ";
					$sql .= " AND ";
					$sql .= "'" . $this->db->escape($matches[3]) . "' ";
				} else { /* exact number */
					$sql = " AND ";
					$sql .= $field . " = '" . $this->db->escape($value) . "' ";
				}
				break;
			case 'varchar':
				$sql = " AND ";
				$sql .= " LCASE(" . $field . ") ";
				$sql .= " LIKE '%" . $this->db->escape(mb_strtolower($value)) . "%' ";
				break;
			case 'alias':
				$sql = " AND ";
				$sql .= $this->aliases[$field];
				$sql .= " LIKE '%" . $this->db->escape(mb_strtolower($value)) . "%' ";
				break;
		}
		return $sql;
	}

	/**
	 * Prepare the order part of the statement
	 * @param  array $order
	 * @return $sql
	 */
	protected function order($order = [])
	{
		/* if the order is not an array, convert it to array */
		if (!is_array($order)) {
			$order = [$order];
		}
		$order_sql = [];
		foreach ($order as $field) {
			/* find if the field matches the format field#asc or field#desc */
			if (
				preg_match(
					'/^([a-z][0-9a-z\._]+)(#(desc|asc))?$/isU',
					mb_strtolower($field),
					$matches
				) &&
				in_array($matches[1], array_keys($this->fields))
			) {
				$direction = 'ASC';
				if (isset($matches[3])) {
					$direction = mb_strtoupper($matches[3]);
				}
				$field = $matches[1];
				if (in_array($matches[1], array_keys($this->aliases))) {
					$field = $this->aliases[$matches[1]];
				}
				$order_sql[] = $field . ' ' . $direction;
			}
		}
		$sql = ' ';
		if ($order_sql) {
			$sql = ' ORDER BY ';
			$sql .= implode(', ', $order_sql);
		}
		return $sql;
	}

	/**
	 * This function will be executed on every save action, so we keep log what
	 * has changed
	 * @param [type] $order_id [description]
	 * @param string $comment  [description]
	 */
	protected function addHistory($order_id, $comment = 'something has changed')
	{
		$this->load->model('sale/order');
		$order = $this->model_sale_order->getOrder($order_id);
		/* Add order history */
		$strSql = "
			INSERT INTO 
				`" . DB_PREFIX . "order_history` 
			SET 
				`order_id` = '" . (int)$order_id . "', 
				`order_status_id` = '" . (int)$order['order_status_id'] . "', 
				`notify` = false, 
				`comment` = '" . $this->db->escape($comment) . "', 
				`date_added` = NOW()";
		$this->db->query($strSql);
	}

	/**
	 * Return list of products per order
	 * @param  [type] $order_id [description]
	 * @return [type]           [description]
	 */
	protected function getProducts($order_id)
	{
		$strSql = "
			SELECT * 
			FROM `" . DB_PREFIX . "order_product` 
			WHERE `order_id` = '" . (int)$order_id . "' ";
		$query = $this->db->query($strSql);
		return $query->rows;
	}

	public function changeTrackingNo($order_id, $tracking_no, $language = null)
	{
		if ($language == null || $language == 0) {
			$selected_language = (int)$this->config->get('config_language_id');
		} else {
			$selected_language = (int)$language;
		}
		if ($order_id > 0) {
			$strSql = "
				UPDATE `" . DB_PREFIX . "order` 
				SET 
					`tracking_no` = '" . $this->db->escape($tracking_no) . "' 
				WHERE 
					`order_id` = '" . (int)$order_id . "' 
					AND `language_id` = '" . $selected_language . "' ";
			$query = $this->db->query($strSql);
			$strSql = "
				SELECT `tracking_no` 
				FROM `" . DB_PREFIX . "order` 
				WHERE 
					`order_id` = '" . (int)$order_id . "' 
					AND `language_id` = '" . $selected_language . "' ";
			$query = $this->db->query($strSql);
		}
		/* $this->cache->delete('product'); */
		return $query->row['tracking_no'];
	}

	public function changeOrderStatus($order_id, $order_status_id)
	{
		$strSql = "
			SELECT 
				`order_status_id` 
			FROM `" . DB_PREFIX . "order` 
			WHERE `order_id` = '" . (int)$order_id . "' ";
		$query = $this->db->query($strSql);
		if (isset($query->row['order_status_id'])) {
			$strSql = "
				UPDATE `" . DB_PREFIX . "order` 
				SET 
					`order_status_id` = '" . (int)$order_status_id . "' 
				WHERE 
					`order_id` = '" . (int)$order_id . "' ";
			$query = $this->db->query($strSql);
			/* $this->cache->delete('product'); */

			$strSql = "
				SELECT 
					o.order_status_id,
					os.name AS order_status_name 
				FROM `" . DB_PREFIX . "order` AS o 
				INNER JOIN `" . DB_PREFIX . "order_status` AS os 
				ON (o.order_status_id = os.order_status_id) 
				WHERE 
					o.order_id = '" . (int)$order_id . "' ";
			$query = $this->db->query($strSql);
			return $query->row['order_status_name'];
		} else {
			return "Error";
		}
	}
}
