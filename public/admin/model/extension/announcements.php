<?php
/* This file is under Git Control by KDSI. */
class ModelExtensionAnnouncements extends Model
{
	public function install()
	{
		$strSql = "
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "announcement` (
				`announcement_id` int(11) NOT NULL AUTO_INCREMENT,
				`url` varchar(500) NOT NULL,
				`image` varchar(255) NOT NULL,
				`text` mediumtext NOT NULL,
				`category_id` int(11) NOT NULL,
				`sort_order` int(3) NOT NULL DEFAULT '0',
				`status` tinyint(1) NOT NULL DEFAULT '1',
				`hit` INT(11) NOT NULL DEFAULT '0',
				`chk_title` tinyint(1) NOT NULL,
				`chk_description` tinyint(1) NOT NULL,
				`chk_image` tinyint(1) NOT NULL,
				`added_by` INT(11) NULL DEFAULT NULL,
				`added_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`announcement_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;
		";
		$this->db->query($strSql);

		$strSql = "
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "announcement_description` (
				`announcement_id` int(11) NOT NULL,
				`language_id` int(11) NOT NULL,
				`title` varchar(64) NOT NULL,
				`announcement_description` text NOT NULL,
				`name` varchar(255) NOT NULL,
				PRIMARY KEY (`announcement_id`,`language_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
		";
		$this->db->query($strSql);

		$strSql = "
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "announcement_to_category` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`announcement_id` int(11) NOT NULL,
				`announcement_category_id` int(11) NOT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;
		";
		$this->db->query($strSql);

		$strSql = "
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "announcement_category` (
				`announcement_category_id` int(11) NOT NULL AUTO_INCREMENT,
				`sort_order` int(3) NOT NULL DEFAULT '0',
				`image` varchar(255) NOT NULL,
				`status` tinyint(1) NOT NULL DEFAULT '1',
				`chk_category_title` tinyint(1) NOT NULL,
				`chk_category_description` tinyint(1) NOT NULL,
				`chk_category_image` tinyint(1) NOT NULL,
				`boxlist` tinyint(1) NOT NULL,
				PRIMARY KEY (`announcement_category_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;
		";
		$this->db->query($strSql);

		$strSql = "
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "announcement_category_description` (
				`announcement_category_id` int(11) NOT NULL,
				`language_id` int(11) NOT NULL,
				`meta_description` text NOT NULL,
				`meta_keyword` text NOT NULL,
				`title` varchar(64) NOT NULL,
				`description` text NOT NULL,
				PRIMARY KEY (`announcement_category_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
		";
		$this->db->query($strSql);

		if (!$this->config->get('config_admin_limit')) {
			$strSql = "
				INSERT INTO `kdsi_setting` 
				(`setting_id`, `store_id`, `code`, `key`, `value`, `serialized`, `added_at`, `added_by`) VALUES 
				(NULL, '0', 'config', 'config_admin_limit', '25', '0', current_timestamp(), NULL)  
			";
			$this->db->query($strSql);
		}

		if (!$this->config->get('config_catalog_limit')) {
			$strSql = "
				INSERT INTO `kdsi_setting` 
				(`setting_id`, `store_id`, `code`, `key`, `value`, `serialized`, `added_at`, `added_by`) VALUES 
				(NULL, '0', 'config', 'config_catalog_limit', '21', '0', current_timestamp(), NULL) 
			";
			$this->db->query($strSql);
		}
	}

	public function uninstall()
	{
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "announcement`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "announcement_description`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "announcement_to_category`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "announcement_category`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "announcement_category_description`");
	}

	public function addAnnouncement($data)
	{
		$strSql = "
			INSERT 
			INTO `" . DB_PREFIX . "announcement` 
			SET 
				`url` = '" . $this->db->escape($data['url']) . "', 
				`image` = '" . $this->db->escape(html_entity_decode($data['text'], ENT_QUOTES, 'UTF-8')) . "' ,
				`sort_order` = '" . (int)$data['sort_order'] . "', 
				`status` = '" . (int)$data['status'] . "' 
		";
		$this->db->query($strSql);

		$announcement_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$strSql = "
				UPDATE `" . DB_PREFIX . "announcement` 
				SET 
					`image` = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' 
				WHERE 
					`announcement_id` = '" . (int)$announcement_id . "' 
			";
			$this->db->query($strSql);
		}

		foreach ($data['announcement_description'] as $language_id => $value) {
			$strSql = "
				INSERT 
				INTO `" . DB_PREFIX . "announcement_description` 
				SET 
					`announcement_id` = '" . (int)$announcement_id . "', 
					`language_id` = '" . (int)$language_id . "', 
					`title` = '" . $this->db->escape($value['title']) . "', 
					`announcement_description` = '" . $this->db->escape($value['description']) . "' 
			";
			$this->db->query($strSql);
		}

		if (isset($data['announcement_categories'])) {
			foreach ($data['announcement_categories'] as $announcement_category_id) {
				$strSql = "
					INSERT 
					INTO `" . DB_PREFIX . "announcement_to_category` 
					SET 
						`announcement_id` = '" . (int)$announcement_id . "', 
						`announcement_category_id` = '" . (int)$announcement_category_id . "' 
				";
				$this->db->query($strSql);
			}
		}

		if ($data['keyword']) {
			$strSql = "
				INSERT 
				INTO `" . DB_PREFIX . "seo_url` 
				SET 
					`query` = 'announcement_id=" . (int)$announcement_id . "', 
					`keyword` = '" . $this->db->escape($data['keyword']) . "' 
			";
			$this->db->query($strSql);
		}

		$this->cache->delete('announcement');
	}

	public function editAnnouncement($announcement_id, $data)
	{
		$strSql = "
			UPDATE `" . DB_PREFIX . "announcement` 
			SET 
				`url` = '" . $this->db->escape($data['url']) . "', 
				`text` = '" . $this->db->escape(html_entity_decode($data['text'], ENT_QUOTES, 'UTF-8')) . "' ,
				`sort_order` = '" . (int)$data['sort_order'] . "', 
				`status` = '" . (int)$data['status'] . "' 
			WHERE 
				`announcement_id` = '" . (int)$announcement_id . "' 
		";
		$this->db->query($strSql);
		if (isset($data['image'])) {
			$strSql = "
				UPDATE `" . DB_PREFIX . "announcement` 
				SET 
					`image` = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' 
				WHERE 
					`announcement_id` = '" . (int)$announcement_id . "' 
			";
			$this->db->query($strSql);
		}

		$strSql = "
			DELETE 
			FROM `" . DB_PREFIX . "announcement_description` 
			WHERE 
				`announcement_id` = '" . (int)$announcement_id . "' 
		";
		$this->db->query($strSql);

		foreach ($data['announcement_description'] as $language_id => $value) {
			$strSql = "
				INSERT 
				INTO `" . DB_PREFIX . "announcement_description` 
				SET 
					`announcement_id` = '" . (int)$announcement_id . "', 
					`language_id` = '" . (int)$language_id . "', 
					`title` = '" . $this->db->escape($value['title']) . "', 
					`announcement_description` = '" . $this->db->escape($value['description']) . "' 
			";
			$this->db->query($strSql);
		}

		$strSql = "
			DELETE 
			FROM `" . DB_PREFIX . "announcement_to_category` 
			WHERE 
				`announcement_id` = '" . (int)$announcement_id . "' 
		";
		$this->db->query($strSql);

		if (isset($data['announcement_category'])) {
			foreach ($data['announcement_category'] as $announcement_category_id) {
				$strSql = "
					INSERT 
					INTO `" . DB_PREFIX . "announcement_to_category` 
					SET 
						`announcement_id` = '" . (int)$announcement_id . "', 
						`announcement_category_id` = '" . (int)$announcement_category_id . "' 
				";
				$this->db->query($strSql);
			}
		}

		$strSql = "
			DELETE 
			FROM `" . DB_PREFIX . "seo_url` 
			WHERE `query` = 'announcement_id=" . (int)$announcement_id . "' 
		";
		$this->db->query($strSql);

		if ($data['keyword']) {
			$strSql = "
				INSERT 
				INTO `" . DB_PREFIX . "seo_url` 
				SET 
					`query` = 'announcement_id=" . (int)$announcement_id . "', 
					`keyword` = '" . $this->db->escape($data['keyword']) . "' 
			";
			$this->db->query($strSql);
		}

		$this->cache->delete('announcement');
	}

	public function deleteAnnouncement($announcement_id)
	{
		$this->db->query("DELETE FROM `" . DB_PREFIX . "announcement` WHERE `announcement_id` = '" . (int)$announcement_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "announcement_description` WHERE `announcement_id` = '" . (int)$announcement_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE `query` = 'announcement_id=" . (int)$announcement_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "announcement_to_category` WHERE `announcement_id` = '" . (int)$announcement_id . "'");
		$this->cache->delete('announcement');
	}

	public function getAnnouncement($announcement_id)
	{
		$strSql = "
			SELECT DISTINCT 
				*, 
				(
					SELECT 
						keyword 
					FROM `" . DB_PREFIX . "seo_url` 
					WHERE 
						`query` = 'announcement_id=" . (int)$announcement_id . "'
				) AS keyword 
			FROM `" . DB_PREFIX . "announcement` 
			WHERE `announcement_id` = '" . (int)$announcement_id . "' 
		";
		$query = $this->db->query($strSql);
		return $query->row;
	}

	public function getAnnouncements($data = array())
	{
		if ($data) {
			$sql = "
				SELECT 
					a.*, 
					ad.*, 
					u.username AS user_id  
				FROM `" . DB_PREFIX . "announcement` a 
				LEFT JOIN `" . DB_PREFIX . "announcement_description` ad 
				ON (a.announcement_id = ad.announcement_id) 
				LEFT JOIN `" . DB_PREFIX . "user` u 
				ON (a.added_by = u.user_id) 
				WHERE 
					ad.language_id = '" . (int)$this->config->get('config_language_id') . "' 
			";
			$sort_data = array(
				'a.announcement_id',
				'a.added_at',
				'a.sort_order',
				'ad.title'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY a.announcement_id";
			}

			if (isset($data['order']) && ($data['order'] == 'ASC')) {
				$sql .= " ASC";
			} else {
				$sql .= " DESC";
			}

			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}

				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}

			$query = $this->db->query($sql);

			return $query->rows;

		} else {

			$announcement_data = $this->cache->get('announcement.' . (int)$this->config->get('config_language_id'));

			if (!$announcement_data) {
				$strSql = "
					SELECT 
						a.*, 
						ad.*,
						u.username AS user_id  
					FROM `" . DB_PREFIX . "announcement` a 
					LEFT JOIN `" . DB_PREFIX . "announcement_description` ad 
					ON (a.announcement_id = ad.announcement_id) 
					LEFT JOIN `" . DB_PREFIX . "user` u 
					ON (a.added_by = u.user_id) 
					WHERE 
						ad.language_id = '" . (int)$this->config->get('config_language_id') . "' 
					ORDER BY a.announcement_id DESC";
				$query = $this->db->query($strSql);
				$announcement_data = $query->rows;

				$this->cache->set('announcement.' . (int)$this->config->get('config_language_id'), $announcement_data);
			}

			return $announcement_data;
		}
	}

	public function getAnnouncementByCategory($announcement_category_id)
	{
		$strSql = "
			SELECT DISTINCT 
				* 
			FROM `" . DB_PREFIX . "announcement` AS a 
			LEFT JOIN `" . DB_PREFIX . "announcement_to_category` a2c 
			ON (a.announcement_id = a2c.announcement_id) 
			WHERE 
				a2c.announcement_category_id = '" . (int)$announcement_category_id . "' 
		";
		$query = $this->db->query($strSql);
		return $query->row;
	}

	public function getAnnouncementDescriptions($announcement_id)
	{
		$announcement_description_data = array();
		$strSql = "
			SELECT 
				* 
			FROM `" . DB_PREFIX . "announcement_description` 
			WHERE 
				`announcement_id` = '" . (int)$announcement_id . "' 
		";
		$query = $this->db->query($strSql);

		foreach ($query->rows as $result) {
			$announcement_description_data[$result['language_id']] = array(
				'title'       => $result['title'],
				'announcement_description'       => $result['description']
			);
		}

		return $announcement_description_data;
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

	public function getAnnouncementToCategories($announcement_id)
	{
		$announcement_to_category_data = array();
		$strSql = "
			SELECT 
				* 
			FROM `" . DB_PREFIX . "announcement_to_category` 
			WHERE 
				`announcement_id` = '" . (int)$announcement_id . "' 
		";
		$query = $this->db->query($strSql);

		foreach ($query->rows as $result) {
			$announcement_to_category_data[] = $result['announcement_category_id'];
		}

		return $announcement_to_category_data;
	}
}
