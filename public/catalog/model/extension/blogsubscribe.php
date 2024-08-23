<?php
class ModelExtensionBlogsubscribe extends Model {
	public function checkemail($data){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blogsubscribe WHERE email = '" . $this->db->escape(utf8_strtolower($data['bsubscribe'])) . "'");
		return $query->row;
	}
	public function substatus($data){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blogsubscribe WHERE email = '" . $this->db->escape(utf8_strtolower($data['bsubscribe'])) . "' AND status = 1");
		return $query->row;
	}
	public function updatesub($data){
		$query = $this->db->query("UPDATE " . DB_PREFIX . "blogsubscribe SET status = 1 WHERE email = '".$data['bsubscribe']."'");
	}
	public function insertsub($data){
		$query = $this->db->query("INSERT INTO " . DB_PREFIX . "blogsubscribe SET status = 1, email = '".$data['bsubscribe']."'");
	}
}