<?php
class ModelExtensionMpPhotoGalleryProduct extends Model {

	// 07-05-2022: updation task start
	public function getProductGalleries($product_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "gallery_product` WHERE product_id = '" . (int)$product_id . "'");
		return $query->rows;
	}
	// 07-05-2022: updation task end
}