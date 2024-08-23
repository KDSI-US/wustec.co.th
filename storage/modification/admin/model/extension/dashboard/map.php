<?php
/* This file is under Git Control by KDSI. */
class ModelExtensionDashboardMap extends Model {
	public function getTotalOrdersByCountry() {
		$implode = array();

			$config_complete_status_string = implode(',', $this->config->get('config_complete_status'));
			
		
		if (is_array($this->config->get('config_complete_status'))) {
			foreach ($this->config->get('config_complete_status') as $order_status_id) {
				$implode[] = (int)$order_status_id;
			}
		}
		
		if ($implode) {

			$query = $this->db->query("SELECT COUNT(*) AS total, SUM(o.total) AS amount, z.code as iso_code_2 FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "zone` z ON (o.payment_zone_id = z.zone_id) WHERE o.order_status_id IN ($config_complete_status_string) GROUP BY o.payment_zone_id");
			
			$query = $this->db->query("SELECT COUNT(*) AS total, SUM(o.total) AS amount, c.iso_code_2 FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "country` c ON (o.payment_country_id = c.country_id) WHERE o.order_status_id IN(" . implode(',', $implode) . ") GROUP BY o.payment_country_id");

			return $query->rows;
		} else {
			return array();
		}
	}
}
