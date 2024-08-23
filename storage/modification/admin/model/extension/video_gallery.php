<?php
/* This file is under Git Control by KDSI. */
class ModelExtensionVideoGallery extends Model
{
	public function install()
	{
		$strSql = "
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "video_gallery` (
				`video_gallery_id` int(11) NOT NULL AUTO_INCREMENT,
				`video_url` varchar(500) NOT NULL,
				`image` varchar(255) NOT NULL,
				`category_id` int(11) NOT NULL,
				`sort_order` int(3) NOT NULL DEFAULT '0',
				`status` tinyint(1) NOT NULL DEFAULT '1',
				`hit` INT(11) NOT NULL DEFAULT '0',
				`chk_video_title` tinyint(1) NOT NULL,
				`chk_video_description` tinyint(1) NOT NULL,
				`chk_video_image` tinyint(1) NOT NULL,
				`added_by` INT(11) NULL DEFAULT NULL,
				`added_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`video_gallery_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;
		";
		$this->db->query($strSql);

		$strSql = "
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "video_gallery_description` (
				`video_gallery_id` int(11) NOT NULL,
				`language_id` int(11) NOT NULL,
				`video_title` varchar(64) NOT NULL,
				`video_description` text NOT NULL,
				`name` varchar(255) NOT NULL,
				PRIMARY KEY (`video_gallery_id`,`language_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
		";
		$this->db->query($strSql);

		$strSql = "
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "video_gallery_to_category` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`video_gallery_id` int(11) NOT NULL,
				`video_gallery_category_id` int(11) NOT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;
		";
		$this->db->query($strSql);

		$strSql = "
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "video_gallery_category` (
				`video_gallery_category_id` int(11) NOT NULL AUTO_INCREMENT,
				`sort_order` int(3) NOT NULL DEFAULT '0',
				`image` varchar(255) NOT NULL,
				`status` tinyint(1) NOT NULL DEFAULT '1',
				`chk_video_category_title` tinyint(1) NOT NULL,
				`chk_video_category_description` tinyint(1) NOT NULL,
				`chk_video_category_image` tinyint(1) NOT NULL,
				`boxlist` tinyint(1) NOT NULL,
				PRIMARY KEY (`video_gallery_category_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;
		";
		$this->db->query($strSql);

		$strSql = "
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "video_gallery_category_description` (
				`video_gallery_category_id` int(11) NOT NULL,
				`language_id` int(11) NOT NULL,
				`meta_description` text NOT NULL,
				`meta_keyword` text NOT NULL,
				`title` varchar(64) NOT NULL,
				`description` text NOT NULL,
				PRIMARY KEY (`video_gallery_category_id`)
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
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "video_gallery`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "video_gallery_description`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "video_gallery_to_category`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "video_gallery_category`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "video_gallery_category_description`");
	}

	public function addVideoGallery($data)
	{
		$strSql = "
			INSERT 
			INTO `" . DB_PREFIX . "video_gallery` 
			SET 
				`video_url` = '" . $this->db->escape($data['video_url']) . "', 
				`sort_order` = '" . (int)$data['sort_order'] . "', 
				`status` = '" . (int)$data['status'] . "' 
		";
		$this->db->query($strSql);

		$video_gallery_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$strSql = "
				UPDATE `" . DB_PREFIX . "video_gallery` 
				SET 
					`image` = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' 
				WHERE 
					`video_gallery_id` = '" . (int)$video_gallery_id . "' 
			";
			$this->db->query($strSql);
		}

		foreach ($data['video_gallery_description'] as $language_id => $value) {
			$strSql = "
				INSERT 
				INTO `" . DB_PREFIX . "video_gallery_description` 
				SET 
					`video_gallery_id` = '" . (int)$video_gallery_id . "', 
					`language_id` = '" . (int)$language_id . "', 
					`video_title` = '" . $this->db->escape($value['video_title']) . "', 
					`video_description` = '" . $this->db->escape($value['video_description']) . "' 
			";
			$this->db->query($strSql);
		}

		if (isset($data['video_gallery_categories'])) {
			foreach ($data['video_gallery_categories'] as $video_gallery_category_id) {
				$strSql = "
					INSERT 
					INTO `" . DB_PREFIX . "video_gallery_to_category` 
					SET 
						`video_gallery_id` = '" . (int)$video_gallery_id . "', 
						`video_gallery_category_id` = '" . (int)$video_gallery_category_id . "' 
				";
				$this->db->query($strSql);
			}
		}

		if ($data['keyword']) {
			$strSql = "
				INSERT 
				INTO `" . DB_PREFIX . "seo_url` 
				SET 
					`query` = 'video_gallery_id=" . (int)$video_gallery_id . "', 
					`keyword` = '" . $this->db->escape($data['keyword']) . "' 
			";
			$this->db->query($strSql);
		}

		$this->cache->delete('video_gallery');
	}

	public function editVideoGallery($video_gallery_id, $data)
	{
		$strSql = "
			UPDATE `" . DB_PREFIX . "video_gallery` 
			SET 
				`video_url` = '" . $this->db->escape($data['video_url']) . "', 
				`sort_order` = '" . (int)$data['sort_order'] . "', 
				`status` = '" . (int)$data['status'] . "' 
			WHERE 
				`video_gallery_id` = '" . (int)$video_gallery_id . "' 
		";
		$this->db->query($strSql);
		if (isset($data['image'])) {
			$strSql = "
				UPDATE `" . DB_PREFIX . "video_gallery` 
				SET 
					`image` = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' 
				WHERE 
					`video_gallery_id` = '" . (int)$video_gallery_id . "' 
			";
			$this->db->query($strSql);
		}

		$strSql = "
			DELETE 
			FROM `" . DB_PREFIX . "video_gallery_description` 
			WHERE 
				`video_gallery_id` = '" . (int)$video_gallery_id . "' 
		";
		$this->db->query($strSql);

		foreach ($data['video_gallery_description'] as $language_id => $value) {
			$strSql = "
				INSERT 
				INTO `" . DB_PREFIX . "video_gallery_description` 
				SET 
					`video_gallery_id` = '" . (int)$video_gallery_id . "', 
					`language_id` = '" . (int)$language_id . "', 
					`video_title` = '" . $this->db->escape($value['video_title']) . "', 
					`video_description` = '" . $this->db->escape($value['video_description']) . "' 
			";
			$this->db->query($strSql);
		}

		$strSql = "
			DELETE 
			FROM `" . DB_PREFIX . "video_gallery_to_category` 
			WHERE 
				`video_gallery_id` = '" . (int)$video_gallery_id . "' 
		";
		$this->db->query($strSql);

		if (isset($data['video_gallery_category'])) {
			foreach ($data['video_gallery_category'] as $video_gallery_category_id) {
				$strSql = "
					INSERT 
					INTO `" . DB_PREFIX . "video_gallery_to_category` 
					SET 
						`video_gallery_id` = '" . (int)$video_gallery_id . "', 
						`video_gallery_category_id` = '" . (int)$video_gallery_category_id . "' 
				";
				$this->db->query($strSql);
			}
		}

		$strSql = "
			DELETE 
			FROM `" . DB_PREFIX . "seo_url` 
			WHERE `query` = 'video_gallery_id=" . (int)$video_gallery_id . "' 
		";
		$this->db->query($strSql);

		if ($data['keyword']) {
			$strSql = "
				INSERT 
				INTO `" . DB_PREFIX . "seo_url` 
				SET 
					`query` = 'video_gallery_id=" . (int)$video_gallery_id . "', 
					`keyword` = '" . $this->db->escape($data['keyword']) . "' 
			";
			$this->db->query($strSql);
		}

		$this->cache->delete('video_gallery');
	}

	public function deleteVideoGallery($video_gallery_id)
	{
		$this->db->query("DELETE FROM `" . DB_PREFIX . "video_gallery` WHERE `video_gallery_id` = '" . (int)$video_gallery_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "video_gallery_description` WHERE `video_gallery_id` = '" . (int)$video_gallery_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE `query` = 'video_gallery_id=" . (int)$video_gallery_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "video_gallery_to_category` WHERE `video_gallery_id` = '" . (int)$video_gallery_id . "'");
		$this->cache->delete('video_gallery');
	}

	public function getVideoGallery($video_gallery_id)
	{
		$strSql = "
			SELECT DISTINCT 
				*, 
				(
					SELECT 
						keyword 
					FROM `" . DB_PREFIX . "seo_url` 
					WHERE 
						`query` = 'video_gallery_id=" . (int)$video_gallery_id . "'
				) AS keyword 
			FROM `" . DB_PREFIX . "video_gallery` 
			WHERE `video_gallery_id` = '" . (int)$video_gallery_id . "' 
		";
		$query = $this->db->query($strSql);
		return $query->row;
	}

	public function getVideoGalleries($data = array())
	{
		if ($data) {
			$sql = "
				SELECT 
					vg.*, 
					vgd.*, 
					u.username AS user_id  
				FROM `" . DB_PREFIX . "video_gallery` vg 
				LEFT JOIN `" . DB_PREFIX . "video_gallery_description` vgd 
				ON (vg.video_gallery_id = vgd.video_gallery_id) 
				LEFT JOIN `" . DB_PREFIX . "user` u 
				ON (vg.added_by = u.user_id) 
				WHERE 
					vgd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
			";
			$sort_data = array(
				'vg.video_gallery_id',
				'vg.added_at',
				'vg.sort_order',
				'vgd.video_title'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY vg.video_gallery_id";
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

			$video_gallery_data = $this->cache->get('video_gallery.' . (int)$this->config->get('config_language_id'));

			if (!$video_gallery_data) {
				$strSql = "
					SELECT 
						vg.*, 
						vgd.*,
						u.username AS user_id  
					FROM `" . DB_PREFIX . "video_gallery` vg 
					LEFT JOIN `" . DB_PREFIX . "video_gallery_description` vgd 
					ON (vg.video_gallery_id = vgd.video_gallery_id) 
					LEFT JOIN `" . DB_PREFIX . "user` u 
					ON (vg.added_by = u.user_id) 
					WHERE 
						vgd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
					ORDER BY vg.video_gallery_id DESC";
				$query = $this->db->query($strSql);
				$video_gallery_data = $query->rows;

				$this->cache->set('video_gallery.' . (int)$this->config->get('config_language_id'), $video_gallery_data);
			}

			return $video_gallery_data;
		}
	}

	public function getVideoGalleryByCategory($video_gallery_category_id)
	{
		$strSql = "
			SELECT DISTINCT 
				* 
			FROM `" . DB_PREFIX . "video_gallery` AS vg 
			LEFT JOIN `" . DB_PREFIX . "video_gallery_to_category` vg2c 
			ON (vg.video_gallery_id = vg2c.video_gallery_id) 
			WHERE 
				vg2c.video_gallery_category_id = '" . (int)$video_gallery_category_id . "' 
		";
		$query = $this->db->query($strSql);
		return $query->row;
	}

	public function getVideoGalleryDescriptions($video_gallery_id)
	{
		$video_gallery_description_data = array();
		$strSql = "
			SELECT 
				* 
			FROM `" . DB_PREFIX . "video_gallery_description` 
			WHERE 
				`video_gallery_id` = '" . (int)$video_gallery_id . "' 
		";
		$query = $this->db->query($strSql);

		foreach ($query->rows as $result) {
			$video_gallery_description_data[$result['language_id']] = array(
				'video_title'       => $result['video_title'],
				'video_description'       => $result['video_description']
			);
		}

		return $video_gallery_description_data;
	}

	public function getTotalVideoGalleries($data)
	{
		$strSql = "
			SELECT count(*) AS total 
			FROM `" . DB_PREFIX . "video_gallery` 
		";
		$query = $this->db->query($strSql);
		return $query->row['total'];
	}

	public function getVideoGalleryToCategories($video_gallery_id)
	{
		$video_gallery_category_data = array();
		$strSql = "
			SELECT 
				* 
			FROM `" . DB_PREFIX . "video_gallery_to_category` 
			WHERE 
				`video_gallery_id` = '" . (int)$video_gallery_id . "' 
		";
		$query = $this->db->query($strSql);

		foreach ($query->rows as $result) {
			$video_gallery_to_category_data[] = $result['video_gallery_category_id'];
		}

		return $video_gallery_to_category_data;
	}
}
