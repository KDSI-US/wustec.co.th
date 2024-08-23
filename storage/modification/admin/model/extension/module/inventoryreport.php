<?php
/* This file is under Git Control by KDSI. */
class ModelExtensionModuleinventoryreport extends Model
{
	public function createTable()
	{
		$strSql = "
			SHOW columns 
			FROM `" . DB_PREFIX . "product` 
			WHERE `field`='sr_costprice'
			";
		$result = $this->db->query($strSql)->rows;
		if (count($result) == 0) {
			$strSql = "
				ALTER TABLE `" . DB_PREFIX . "product` 
				ADD sr_costprice decimal(15,4);
				";
			$this->db->query($strSql);
		}
		$strSql = "
			SHOW columns 
			FROM `" . DB_PREFIX . "product_option_value` 
			WHERE `field`='sr_costprice' 
			";
		$result = $this->db->query($strSql)->rows;
		if (count($result) == 0) {
			$strSql = "
				ALTER TABLE `" . DB_PREFIX . "product_option_value` 
				ADD sr_costprice decimal(15,4);";
			$this->db->query($strSql);
		}
	}

	public function deleteTable()
	{

		//nothing happens

	}
}
