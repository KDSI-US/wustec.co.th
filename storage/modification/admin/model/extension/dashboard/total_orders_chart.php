<?php
/* This file is under Git Control by KDSI. */

class ModelExtensionDashboardTotalOrdersChart extends Model
{

	public function getTotalOrdersAndCustomers()
	{
		$month_order_data = array();
		$invoice_order_data = array();
		$implode = array();
		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}
		$strSql1 = "SELECT `date_added` FROM `" . DB_PREFIX . "order` ORDER BY `order_id` LIMIT 1";
		$query1 = $this->db->query($strSql1);
		if ($query1->num_rows > 0) {
			$order_date = strtotime($query1->row['date_added']);
		} else {
			$order_date = strtotime(date("Y-m-d"));
		}

		$strSql2 = "SELECT date_added FROM`" . DB_PREFIX . "customer` ORDER BY customer_id LIMIT 1";
		$query2 = $this->db->query($strSql2);
		if ($query2->num_rows > 0) {
			$customer_date = strtotime($query2->row['date_added']);
		} else {
			$customer_date = strtotime(date("Y-m-d"));
		}

		if ($order_date < $customer_date) {
			$min_date = $order_date;
		} else {
			$min_date = $customer_date;
		}

		$min_year = date('Y', $min_date);
		$min_month = date('m', $min_date);
		$min_day = date('d', $min_date);

		$today = strtotime(date("Y-m-d"));
		$total_days = ceil(abs($today - $min_date) / 86400);

		for ($i = 0; $i <= $total_days + 1; $i++) {
			$order_data[date('Y-m-d', mktime(0, 0, 0, $min_month, $i + $min_day, $min_year))] = array(
				'total' => 0
			);
			$customer_data[date('Y-m-d', mktime(0, 0, 0, $min_month, $i + $min_day, $min_year))] = array(
				'total' => 0
			);
		}

		$strSql3 = "SELECT 
				COUNT(*) AS total, 
				date_added 
			FROM `" . DB_PREFIX . "order` 
			WHERE order_status_id IN(" . implode(",", $implode) . ") 
			GROUP BY DATE(date_added)";
		$query3 = $this->db->query($strSql3);
		foreach ($query3->rows as $result) {
			$order_data[date('Y-m-d', strtotime($result['date_added']))] = array(
				'total' => $result['total']
			);
		}

		$strSql4 = "SELECT 
				COUNT(*) AS total, 
				date_added 
			FROM `" . DB_PREFIX . "customer` 
			WHERE status = '" . (int)1 . "' 
			GROUP BY DATE(date_added)";
		$query4 = $this->db->query($strSql4);
		foreach ($query4->rows as $result) {
			$customer_data[date('Y-m-d', strtotime($result['date_added']))] = array(
				'total' => $result['total']
			);
		}

		$final_data = array();
		for ($i = 0; $i <= $total_days + 1; $i++) {
			$dt = date('Y-m-d', mktime(0, 0, 0, $min_month, $i + $min_day, $min_year));
			$final_data[] = array(
				'date' => $dt,
				'column-1' => $order_data[$dt]['total'],
				'column-2' => $customer_data[$dt]['total']
			);
		}
		return $final_data;
	}
}