<?php
/* This file is under Git Control by KDSI. */
class ModelExtensionAnnouncementsCategory extends Model
{
	public function addAnnouncementCategory($data)
	{
		$strSql = "
			INSERT 
			INTO `" . DB_PREFIX . "announcement_category` 
			SET 
				`sort_order` = '" . (int)$data['sort_order'] . "', 
				`chk_category_title` = '" . (int)$data['chk_category_title'] . "', 
				`chk_category_description` = '" . (int)$data['chk_category_description'] . "', 
				`chk_category_image` = '" . (int)$data['chk_category_image'] . "', 
				`status` = '" . (int)$data['status'] . "' 
		";
		$this->db->query($strSql);

		$announcement_category_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$strSql = "
				UPDATE `" . DB_PREFIX . "announcement_category` 
				SET 
					`image` = '" . $this->db->escape(html_entity_decode($data['image'],ENT_QUOTES,'UTF-8')) . "' 
				WHERE 
					`announcement_category_id` = '" . (int)$announcement_category_id . "'
			";
			$this->db->query($strSql);
		}

		foreach ($data['announcement_category_description'] as $language_id => $value) {
			$strSql = "
				INSERT 
				INTO `" . DB_PREFIX . "announcement_category_description` 
				SET 
					`announcement_category_id` = '" . (int)$announcement_category_id . "', 
					`language_id` = '" . (int)$language_id . "', 
					`title` = '" . $this->db->escape($value['title']) . "', 
					`meta_keyword` = '" . $this->db->escape($value['meta_keyword']) . "', 
					`meta_description` = '" . $this->db->escape($value['meta_description']) . "', 
					`description` = '" . $this->db->escape($value['description']) . "' 
			";
			$this->db->query($strSql);
		}

		if ($data['keyword']) {
			$strSql = "
				INSERT 
				INTO `" . DB_PREFIX . "seo_url` 
				SET 
					`query` = 'announcement_category_id=" . (int)$announcement_category_id . "', 
					`keyword` = '" . $this->db->escape($data['keyword']) . "' 
			";
			$this->db->query($strSql);
		}

		$this->cache->delete('announcement_category');
	}

	public function editAnnouncementCategory($announcement_category_id, $data)
	{
		$strSql = "
			UPDATE `" . DB_PREFIX . "announcement_category` 
			SET 
				`sort_order` = '" . (int)$data['sort_order'] . "', 
				`chk_category_title` = '" . (int)$data['chk_category_title'] . "', 
				`chk_category_description` = '" . (int)$data['chk_category_description'] . "', 
				`chk_category_image` = '" . (int)$data['chk_category_image'] . "', 
				`status` = '" . (int)$data['status'] . "' 
			WHERE 
				`announcement_category_id` = '" . (int)$announcement_category_id . "' 
		";
		$this->db->query($strSql);

		if (isset($data['image'])) {
			$strSql = "
				UPDATE `" . DB_PREFIX . "announcement_category` 
				SET 
					`image` = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' 
				WHERE 
					`announcement_category_id` = '" . (int)$announcement_category_id . "' 
			";
			$this->db->query($strSql);
		}

		$strSql = "
			DELETE 
			FROM `" . DB_PREFIX . "announcement_category_description` 
			WHERE 
				`announcement_category_id` = '" . (int)$announcement_category_id . "' 
		";
		$this->db->query($strSql);

		foreach ($data['announcement_category_description'] as $language_id => $value) {
			$strSql = "
				INSERT 
				INTO `" . DB_PREFIX . "announcement_category_description` 
				SET 
					`announcement_category_id` = '" . (int)$announcement_category_id . "', 
					`language_id` = '" . (int)$language_id . "', 
					`title` = '" . $this->db->escape($value['title']) . "', 
					`meta_keyword` = '" . $this->db->escape($value['meta_keyword']) . "', 
					`meta_description` = '" . $this->db->escape($value['meta_description']) . "', 
					`description` = '" . $this->db->escape($value['description']) . "' 
			";
			$this->db->query($strSql);
		}

		$strSql = "
			DELETE 
			FROM `" . DB_PREFIX . "seo_url` 
			WHERE 
				`query` = 'announcement_category_id=" . (int)$announcement_category_id . "' 
		";
		$this->db->query($strSql);

		if ($data['keyword']) {
			$strSql = "
				INSERT 
				INTO `" . DB_PREFIX . "seo_url` 
				SET 
					`query` = 'announcement_category_id=" . (int)$announcement_category_id . "', 
					`keyword` = '" . $this->db->escape($data['keyword']) . "'
			";
			$this->db->query($strSql);
		}

		$this->cache->delete('announcement_category');
	}

	public function deleteAnnouncementCategory($announcement_category_id)
	{
		$strSql = "
			DELETE 
			FROM `" . DB_PREFIX . "announcement_category` 
			WHERE 
				`announcement_category_id` = '" . (int)$announcement_category_id . "'
		";
		$this->db->query($strSql);
		$strSql = "
			DELETE 
			FROM `" . DB_PREFIX . "announcement_category_description` 
			WHERE 
				`announcement_category_id` = '" . (int)$announcement_category_id . "'
		";
		$this->db->query($strSql);
		$strSql = "
			DELETE 
			FROM `" . DB_PREFIX . "seo_url` 
			WHERE 
				`query` = 'announcement_category_id=" . (int)$announcement_category_id . "' 
		";
		$this->db->query($strSql);
		$this->cache->delete('announcement_category');
	}

	public function getAnnouncementCategory($announcement_category_id)
	{
		$strSql = "
			SELECT DISTINCT 
				*, 
				(SELECT keyword 
					FROM `" . DB_PREFIX . "seo_url` 
					WHERE `query` = 'announcement_category_id=" . (int)$announcement_category_id . "' 
				) AS keyword 
			FROM `" . DB_PREFIX . "announcement_category_description` AS acd 
			LEFT JOIN `" . DB_PREFIX . "announcement_category` ac 
			ON (acd.announcement_category_id = ac.announcement_category_id) 
			WHERE 
				acd.announcement_category_id = '" . (int)$announcement_category_id . "' 
		";
		$query = $this->db->query($strSql);
		return $query->row;
	}

	public function getAllAnnouncementCategories()
	{
		$strSql = "
			SELECT * 
			FROM `" . DB_PREFIX . "announcement_category` ac 
			LEFT JOIN `" . DB_PREFIX . "announcement_category_description` acd 
			ON (ac.announcement_category_id = acd.announcement_category_id) 
			WHERE 1 
		";
		$query = $this->db->query($strSql);
		return $query->rows;
	}

	public function getAnnouncementCategoryByID($announcement_id)
	{
		$strSql = "
			SELECT 
				ac.*, 
				acd.* 
			FROM `" . DB_PREFIX . "announcement_to_category` a2c 
			LEFT JOIN `" . DB_PREFIX . "announcement_category` ac 
			ON (ac.announcement_category_id = a2c.announcement_category_id) 
			LEFT JOIN `" . DB_PREFIX . "announcement_category_description` acd 
			ON (ac.announcement_category_id = acd.announcement_category_id) 
			WHERE 
				a2c.announcement_id = '" . (int)$announcement_id . "' 
		";
		$query = $this->db->query($strSql);
		return $query->rows;
	}

	public function getTotalAnnouncementCategory()
	{
		$strSql = "
			SELECT count(*) AS total 
			FROM `" . DB_PREFIX . "announcement_category` ac 
			LEFT JOIN `" . DB_PREFIX . "announcement_category_description` acd 
			ON (ac.announcement_category_id = acd.announcement_category_id)
		";
		$query = $this->db->query($strSql);
		return $query->row['total'];
	}

	public function getAnnouncementCategories($data = array())
	{
		if ($data) {

			$strSql = "
				SELECT * 
				FROM `" . DB_PREFIX . "announcement_category` ac 
				LEFT JOIN `" . DB_PREFIX . "announcement_category_description` acd 
				ON (ac.announcement_category_id = acd.announcement_category_id) 
				WHERE acd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
			";
			if (!empty($data['filter_name'])) {
				$strSql .= " AND acd.title LIKE '" . $this->db->escape($data['filter_name']) . "%' ";
			}
			$sort_data = array(
				'acd.title',
				'ac.sort_order'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$strSql .= " ORDER BY " . $data['sort'] . " ";
			} else {
				$strSql .= " ORDER BY acd.title ";
			}

			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$strSql .= " DESC ";
			} else {
				$strSql .= " ASC ";
			}

			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}
				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}
				$strSql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'] . " ";
			}
			$query = $this->db->query($strSql);

			return $query->rows;

		} else {

			$announcement_category_data = $this->cache->get('announcement_category.' . (int)$this->config->get('config_language_id'));
			if (!$announcement_category_data) {
				$strSql = "
					SELECT 
						ac.*, 
						acd.* 
					FROM `" . DB_PREFIX . "announcement_category` AS ac 
					LEFT JOIN `" . DB_PREFIX . "announcement_category_description` AS acd 
					ON (ac.announcement_category_id = acd.announcement_category_id) 
					WHERE 
						acd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
					ORDER BY acd.title 
				";
				$query = $this->db->query($strSql);
				$announcement_category_data = $query->rows;
				$this->cache->set('announcement_category.' . (int)$this->config->get('config_language_id'), $announcement_category_data);
			}

			return $announcement_category_data;

		}
	}

	public function getAnnouncementDescriptionByCategory($announcement_category_id)
	{
		$announcement_category_description_data = array();
		$strSql = "
			SELECT * 
			FROM `" . DB_PREFIX . "announcement_category_description` AS acd 
			WHERE 
				`announcement_category_id` = '" . (int)$announcement_category_id . "' 
		";
		$query = $this->db->query($strSql);
		foreach ($query->rows as $result) {
			$announcement_category_description_data[$result['language_id']] = array(
				'title'       => $result['title'],
				'description' => $result['description'],
				'meta_keyword' => $result['meta_keyword'],
				'meta_description' => $result['meta_description']
			);
		}
		return $announcement_category_description_data;
	}

	public function getTotalAnnouncementCategories()
	{
		$strSql = "
			SELECT 
				COUNT(*) AS total 
			FROM `" . DB_PREFIX . "announcement_category` 
		";
		$query = $this->db->query($strSql);
		return $query->row['total'];
	}
}
