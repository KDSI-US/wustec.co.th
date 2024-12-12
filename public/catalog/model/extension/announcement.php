<?php
/* This file is under Git Control by KDSI. */
class ModelExtensionAnnouncement extends Model
{
	public function getAnnouncementByCategory($data)
	{
		$strSql = "
			SELECT DISTINCT * 
			FROM `" . DB_PREFIX . "announcement` a 
			LEFT JOIN `" . DB_PREFIX . "announcement_description` ad 
			ON (a.announcement_id = ad.announcement_id) 
			LEFT JOIN `" . DB_PREFIX . "announcement_to_category` a2c 
			ON (a.announcement_id = a2c.announcement_id) 
			WHERE 
				a2c.announcement_category_id = '" . (int)$data['announcement_category_id'] . "' 
				AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' 
		";

		if (isset($data['id'])) {
			$strSql .= " AND a.announcement_id = '" . $data['id'] . "'";
		}
		
		if (isset($data['status'])) {
			$strSql .= " AND a.status = '" . $data['status'] . "'";
		}

		if (isset($data['sort'])) {
			$strSql .= " ORDER BY " . $data['sort'] . " ";
		} else {
			$strSql .= " ORDER BY a.added_at ";
		}

		if (isset($data['order']) && ($data['order'] == 'ASC')) {
			$strSql .= " ASC ";
		} else {
			$strSql .= " DESC ";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
			$strSql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		$query = $this->db->query($strSql);
		return $query->rows;
	}

	public function getTotalAnnouncements($data)
	{
		$strSql = "
			SELECT count(*) AS total 
			FROM `" . DB_PREFIX . "announcement` 
		";
		$query = $this->db->query($strSql);
		return $query->row['total'];
	}
	
	public function getTotalAnnouncementByCategory($data)
	{
		$strSql = "
			SELECT count(*) AS total 
			FROM `" . DB_PREFIX . "announcement` a 
			LEFT JOIN `" . DB_PREFIX . "announcement_description` ad 
			ON (a.announcement_id = ad.announcement_id) 
			LEFT JOIN `" . DB_PREFIX . "announcement_to_category` a2c 
			ON (a.announcement_id = a2c.announcement_id) 
			WHERE 
				a2c.announcement_category_id = '" . (int)$data['announcement_category_id'] . "' 
				AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' 
		";
		$query = $this->db->query($strSql);
		return $query->row['total'];
	}

	public function getAnnouncementCategoryData($announcement_category_id)
	{
		$strSql = "
			SELECT * 
			FROM `" . DB_PREFIX . "announcement_category` ac 
			LEFT JOIN `" . DB_PREFIX . "announcement_category_description` acd 
			ON(ac.announcement_category_id = acd.announcement_category_id) 
			WHERE 
				ac.announcement_category_id = '" . $announcement_category_id . "' 
				AND acd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
		";
		$query = $this->db->query($strSql);
		return $query->row;
	}
}
