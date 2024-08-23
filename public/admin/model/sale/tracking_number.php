<?php

class ModelSaleTrackingNumber extends Model
{

  public function getTrackingNumbers($order_id)
  {
    $strSql = "SELECT * 
		FROM `" . DB_PREFIX . "order_shipment` AS os 
		INNER JOIN `" . DB_PREFIX . "shipping_courier` AS sc 
		ON os.shipping_courier_id = sc.shipping_courier_id 
		WHERE os.order_id = '" . (int)$order_id . "' 
		ORDER BY date_added DESC";
    $query = $this->db->query($strSql);
	
    return $query->rows;
  }
}
