<?php
class ModelExtensionBlogsend extends Model {
	public function latestpost(){
		$query = $this->db->query("SELECT * FROM `". DB_PREFIX ."post` p LEFT JOIN `". DB_PREFIX ."post_description` pd ON p.post_id = pd.post_id WHERE p.post_id > 0 ORDER BY p.post_id DESC LIMIT 1");
		return $query->row;
	}
	public function latestfivepost(){
		$query = $this->db->query("SELECT * FROM `". DB_PREFIX ."post` p LEFT JOIN `". DB_PREFIX ."post_description` pd ON p.post_id = pd.post_id WHERE p.post_id > 0 ORDER BY p.post_id DESC LIMIT 1, 4");
		return $query->rows;
	}
	public function unsubscribe($email){
		$this->db->query("UPDATE `". DB_PREFIX ."blogsubscribe` SET status = 0 WHERE email = '". $email ."'");
	}
	public function lastemail(){
		$query = $this->db->query("SELECT * FROM `". DB_PREFIX ."blogemaillist` ORDER BY blogemail_id DESC LIMIT 1");
		return $query->row;
	}
	public function subscribers(){
		$query = $this->db->query("SELECT * FROM `". DB_PREFIX ."blogsubscribe` WHERE status = 1");
		return $query->rows;
	}
	public function sentpost($data){
		$this->db->query("INSERT INTO `". DB_PREFIX ."blogemaillist` SET post_id = '". $data ."', date_added = NOW()");
	}
}