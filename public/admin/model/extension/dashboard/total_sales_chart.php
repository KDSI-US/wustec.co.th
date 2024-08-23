<?php

class ModelExtensionDashboardTotalSalesChart extends Model
{

	public function getTotalSales()
	{

		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}

		$strSql1 = "SELECT `date_added` FROM `" . DB_PREFIX . "order` ORDER BY `order_id` LIMIT 1";
		$query1 = $this->db->query($strSql1);
		if ($query1->num_rows > 0) {
			$min_date = strtotime($query1->row['date_added']);
		} else {
			$min_date = strtotime(date("Y-m-d"));
		}
		$min_year = date('Y', $min_date);
		$min_month = date('m', $min_date);
		$min_day = date('d', $min_date);

		$today = strtotime(date("Y-m-d"));
		$total_days = ceil(abs($today - $min_date) / 86400);

		for ($i = 0; $i <= $total_days + 1; $i++) {
			$sale_data[date('Y-m-d', mktime(0, 0, 0, $min_month, $i + $min_day, $min_year))] = array(
				'total' => 0
			);
		}

		$strSql2 = "SELECT 
				SUM(total) AS total, 
				date_added 
			FROM `" . DB_PREFIX . "order` 
			WHERE `order_status_id` IN(" . implode(",", $implode) . ") 
			GROUP BY DATE(date_added)";
		$query2 = $this->db->query($strSql2);
		foreach ($query2->rows as $result2) {
			$sale_data[date('Y-m-d', strtotime($result2['date_added']))] = array(
				'total' => $result2['total']
			);
		}

		$final_data = array();
		for ($i = 0; $i <= $total_days + 1; $i++) {
			$dt = date('Y-m-d', mktime(0, 0, 0, $min_month, $i + $min_day, $min_year));
			$final_data[] = array(
				'date' => $dt,
				'column-1' => $sale_data[$dt]['total']
			);
		}

		return $final_data;
	}
}