<?php
/* This file is under Git Control by KDSI. */
/* This file is under Git Control by KDSI. */
class ModelExtensionModuleImageChecker extends Model {
  public function getProductImages($data = array()) {

		$sql = "SELECT p.product_id, p.model, p.quantity, p.`status`, pd.`name`, pov.option_image as `image`, ovd.`name` as color_name
    FROM dbcapella_opencart_dev.kdsi_product p
    INNER JOIN dbcapella_opencart_dev.kdsi_product_description pd
    ON p.product_id = pd.product_id
    INNER JOIN dbcapella_opencart_dev.kdsi_product_option po
    ON p.product_id = po.product_id
    INNER JOIN dbcapella_opencart_dev.kdsi_product_option_value pov
    ON po.product_option_id = pov.product_option_id
    INNER JOIN dbcapella_opencart_dev.kdsi_option_description od 
    ON po.option_id = od.option_id
    INNER JOIN dbcapella_opencart_dev.kdsi_option_value_description ovd 
    ON pov.option_value_id = ovd.option_value_id
    WHERE od.`name` = 'COLOR' AND pov.option_image > ''";

    if (!empty($data['filter_stock'])) {
      //$search_filter_stock = $this->db->escape($data['filter_stock']);
      if ($data['filter_stock'] == "with_stock") {
        $sql .= " AND p.quantity > 0";
      }
      if ($data['filter_stock'] == "no_stock") {
        $sql .= " AND p.quantity < 1";
      }
    }

    if (!empty($data['filter_status'])) {
      //$search_filter_status = $this->db->escape($data['filter_status']);
      if ($data['filter_status'] == "status_1") {
        $sql .= " AND p.status=1";
      }
      if ($data['filter_status'] == 'status_0') {
        $sql .= " AND p.status=0";
      }
    }

    $query = $this->db->query($sql);

		return $query->rows;
	}
}
