<?php
/* This file is under Git Control by KDSI. */

class ModelSaleTrackingNumber extends Model
{

  public function getShippingCourier()
  {
    $strSql = "SELECT sc.* FROM `" . DB_PREFIX . "shipping_courier` sc INNER JOIN `" . DB_PREFIX . "extension` ex ON sc.shipping_courier_code = ex.code";
    $query = $this->db->query($strSql);
	
    return $query->rows;
  }
			

	public function getCustomerInfo($order_id) {
    $strSql = "SELECT firstname, telephone FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int) $order_id. "'";
    $query = $this->db->query($strSql);
	
    return $query->row;
  }
			
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
