<?php
/* This file is under Git Control by KDSI. */
class ModelExtensionModuleMysqlJobs extends Model
{
	public function addMySQLJob($data)
	{
		$strSql = "
			INSERT INTO `" . DB_PREFIX . "mysql_jobs` 
			SET 
				`description` = '" . $this->db->escape($data['description']) . "', 
				`query` ='" . $this->db->escape($data['query']) . "', 
				`execution_next` = '" . $this->db->escape($data['date_exec_next']) . "', 
				`execution_hour` = '" . $this->db->escape($data['exec_hour']) . "', 
				`execution_last` = '0000-00-00', 
				`execution_Time` = '" . $this->db->escape($data['exec_time']) . "', 
				`execution_interval` ='" . $data['exec_interval'] . "', 
				`email` = '" . $this->db->escape($data['email']) . "', 
				`status` = '" . (int)$data['status'] . "', 
				`date_added` = NOW() 
		";
		$this->db->query($strSql);
	}

	public function editMySQLJob($data)
	{
		$strSql = "
			UPDATE `" . DB_PREFIX . "mysql_jobs` 
			SET 
				`description` = '" . $this->db->escape($data['description']) . "', 
				`query` ='" . $this->db->escape($data['query']) . "', 
				`execution_next` = '" . $this->db->escape($data['date_exec_next']) . "', 
				`execution_hour` = '" . $this->db->escape($data['exec_hour']) . "', 
				`execution_time` = '" . $this->db->escape($data['exec_time']) . "', 
				`execution_interval` = '" . $this->db->escape($data['exec_interval']) . "', 
				`email` = '" . $this->db->escape($data['email']) . "', 
				`status` = '" . (int)$data['status'] . "', 
				`date_added` = NOW() 
			WHERE 
				`id` = '" . $data['jobId'] . "' 
		";
		$this->db->query($strSql);
	}

	public function deleteMySQLJob($mysql_job_id)
	{
		$strSql = "
			DELETE FROM `" . DB_PREFIX . "mysql_jobs` 
			WHERE 
				`id` = '" . (int)$mysql_job_id . "' 
		";
		$this->db->query($strSql);
	}

	public function getMySQLJob($mysql_job_id)
	{
		$strSql = "
			SELECT DISTINCT * 
			FROM `" . DB_PREFIX . "mysql_jobs` 
			WHERE 
				`id` = '" . (int)$mysql_job_id . "' 
		";
		$query = $this->db->query($strSql);

		return $query->row;
	}

	public function execute($query)
	{
		$result = $this->db->query($query);
		if (!isset($result->rows)) {
			return null;
		}
		return $result->rows;
	}

	public function UpdateDate($mysql_job_id, $date)
	{
		$strSql = "
			UPDATE `" . DB_PREFIX . "mysql_jobs` 
			SET 
				`execution_last` ='" . date('Y-m-d') . "', 
				`execution_next` ='" . $date . "', 
				`execution_time` ='" . date("H:i:s") . "' 
			WHERE 
				`id` ='" . $mysql_job_id . "' 
		";
		$this->db->query($strSql);
	}

	public function getMySQLJobs($data = array())
	{
		$sql = "SELECT * FROM `" . DB_PREFIX . "mysql_jobs`";

		$query = $this->db->query($sql);

		try {
			return $query->rows;
		} catch (Exception $ex) {
		}
	}
}
