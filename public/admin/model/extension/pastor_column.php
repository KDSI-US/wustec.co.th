<?php
class ModelExtensionPastorColumn extends Model
{
	public function install()
	{
		$strSql = "
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "pastor_column` (
				`pastor_column_id` int(11) NOT NULL AUTO_INCREMENT,
				`video_url` varchar(500) NOT NULL,
				`image` varchar(255) NOT NULL,
				`category_id` int(11) NOT NULL,
				`sort_order` int(3) NOT NULL DEFAULT '0',
				`status` tinyint(1) NOT NULL DEFAULT '1',
				`hit` INT(11) NOT NULL DEFAULT '0',
				`chk_pastor_column_title` tinyint(1) NOT NULL,
				`chk_pastor_column_description` tinyint(1) NOT NULL,
				`chk_pastor_column_image` tinyint(1) NOT NULL,
				`added_by` INT(11) NULL DEFAULT NULL,
				`added_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`pastor_column_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;
		";
		$this->db->query($strSql);

		$strSql = "
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "pastor_column_description` (
				`pastor_column_id` int(11) NOT NULL,
				`language_id` int(11) NOT NULL,
				`title` varchar(64) NOT NULL,
				`description` text NOT NULL,
				`name` varchar(255) NOT NULL,
				PRIMARY KEY (`pastor_column_id`,`language_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
		";
		$this->db->query($strSql);

		$strSql = "
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "pastor_column_to_category` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`pastor_column_id` int(11) NOT NULL,
				`pastor_column_category_id` int(11) NOT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;
		";
		$this->db->query($strSql);

		$strSql = "
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "pastor_column_category` (
				`pastor_column_category_id` int(11) NOT NULL AUTO_INCREMENT,
				`sort_order` int(3) NOT NULL DEFAULT '0',
				`image` varchar(255) NOT NULL,
				`status` tinyint(1) NOT NULL DEFAULT '1',
				`chk_title` tinyint(1) NOT NULL,
				`chk_description` tinyint(1) NOT NULL,
				`chk_image` tinyint(1) NOT NULL,
				`boxlist` tinyint(1) NOT NULL,
				PRIMARY KEY (`pastor_column_category_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;
		";
		$this->db->query($strSql);

		$strSql = "
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "pastor_column_category_description` (
				`pastor_column_category_id` int(11) NOT NULL,
				`language_id` int(11) NOT NULL,
				`meta_description` text NOT NULL,
				`meta_keyword` text NOT NULL,
				`title` varchar(64) NOT NULL,
				`description` text NOT NULL,
				PRIMARY KEY (`pastor_column_category_id`)
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
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "pastor_column`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "pastor_column_description`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "pastor_column_to_category`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "pastor_column_category`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "pastor_column_category_description`");
	}

	public function addPastorColumn($data)
	 {
		$strSql = "
			INSERT 
			INTO `" . DB_PREFIX . "pastor_column` 
			SET 
				`video_url` = '" . $this->db->escape($data['video_url']) . "', 
				`sort_order` = '" . (int)$data['sort_order'] . "', 
				`chk_title` = '" . (int)$data['chk_title'] . "', 
				`chk_description` = '" . (int)$data['chk_description'] . "', 
				`chk_image` = '" . (int)$data['chk_image'] . "', 
				`status` = '" . (int)$data['status'] . "' 
		";
		$this->db->query($strSql);

		$pastor_column_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$strSql = "
				UPDATE `" . DB_PREFIX . "pastor_column` 
				SET 
					`image` = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' 
				WHERE 
					`pastor_column_id` = '" . (int)$pastor_column_id . "' 
			";
			$this->db->query($strSql);
		}

		foreach ($data['pastor_column_description'] as $language_id => $value) {
			$strSql = "
				INSERT 
				INTO `" . DB_PREFIX . "pastor_column_description` 
				SET 
					`pastor_column_id` = '" . (int)$pastor_column_id . "', 
					`language_id` = '" . (int)$language_id . "', 
					`title` = '" . $this->db->escape($value['title']) . "', 
					`description` = '" . $this->db->escape($value['description']) . "' 
			";
			$this->db->query($strSql);
		}

		if (isset($data['pastor_column_categories'])) {
			foreach ($data['pastor_column_categories'] as $pastor_column_category_id) {
				$strSql = "
					INSERT 
					INTO `" . DB_PREFIX . "pastor_column_to_category` 
					SET 
						`pastor_column_id` = '" . (int)$pastor_column_id . "', 
						`pastor_column_category_id` = '" . (int)$pastor_column_category_id . "' 
				";
				$this->db->query($strSql);
			}
		}

		if ($data['keyword']) {
			$strSql = "
				INSERT 
				INTO `" . DB_PREFIX . "seo_url` 
				SET 
					`query` = 'pastor_column_id=" . (int)$pastor_column_id . "', 
					`keyword` = '" . $this->db->escape($data['keyword']) . "' 
			";
			$this->db->query($strSql);
		}

		$this->cache->delete('pastor_column');
	}

	public function editPastorColumn($pastor_column_id, $data)
	{
		$strSql = "
			UPDATE `" . DB_PREFIX . "pastor_column` 
			SET 
				`video_url` = '" . $this->db->escape($data['video_url']) . "', 
				`sort_order` = '" . (int)$data['sort_order'] . "', 
				`chk_title` = '" . (int)$data['chk_title'] . "', 
				`chk_description` = '" . (int)$data['chk_description'] . "', 
				`chk_image` = '" . (int)$data['chk_image'] . "', 
				`status` = '" . (int)$data['status'] . "' 
			WHERE 
				`pastor_column_id` = '" . (int)$pastor_column_id . "' 
		";
		$this->db->query($strSql);
		if (isset($data['image']) && $data['image'] != '') {
			$strSql = "
				UPDATE `" . DB_PREFIX . "pastor_column` 
				SET 
					`image` = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' 
				WHERE 
					`pastor_column_id` = '" . (int)$pastor_column_id . "' 
			";
			$this->db->query($strSql);
		}
		if (isset($data['video_image']) && $data['video_image'] != '') {
			$strSql = "
				UPDATE `" . DB_PREFIX . "pastor_column` 
				SET 
					`video_image` = '" . $this->db->escape(html_entity_decode($data['video_image'], ENT_QUOTES, 'UTF-8')) . "' 
				WHERE 
					`pastor_column_id` = '" . (int)$pastor_column_id . "' 
			";
			$this->db->query($strSql);
		}

		$strSql = "
			DELETE 
			FROM `" . DB_PREFIX . "pastor_column_description` 
			WHERE 
				`pastor_column_id` = '" . (int)$pastor_column_id . "' 
		";
		$this->db->query($strSql);

		foreach ($data['pastor_column_description'] as $language_id => $value) {
			$strSql = "
				INSERT 
				INTO `" . DB_PREFIX . "pastor_column_description` 
				SET 
					`pastor_column_id` = '" . (int)$pastor_column_id . "', 
					`language_id` = '" . (int)$language_id . "', 
					`title` = '" . $this->db->escape($value['title']) . "', 
					`description` = '" . $this->db->escape($value['description']) . "' 
			";
			$this->db->query($strSql);
		}

		$strSql = "
			DELETE 
			FROM `" . DB_PREFIX . "pastor_column_to_category` 
			WHERE 
				`pastor_column_id` = '" . (int)$pastor_column_id . "' 
		";
		$this->db->query($strSql);

		if (isset($data['pastor_column_category'])) {
			foreach ($data['pastor_column_category'] as $pastor_column_category_id) {
				$strSql = "
					INSERT 
					INTO `" . DB_PREFIX . "pastor_column_to_category` 
					SET 
						`pastor_column_id` = '" . (int)$pastor_column_id . "', 
						`pastor_column_category_id` = '" . (int)$pastor_column_category_id . "' 
				";
				$this->db->query($strSql);
			}
		}

		$strSql = "
			DELETE 
			FROM `" . DB_PREFIX . "seo_url` 
			WHERE `query` = 'pastor_column_id=" . (int)$pastor_column_id . "' 
		";
		$this->db->query($strSql);

		if ($data['keyword']) {
			$strSql = "
				INSERT 
				INTO `" . DB_PREFIX . "seo_url` 
				SET 
					`query` = 'pastor_column_id=" . (int)$pastor_column_id . "', 
					`keyword` = '" . $this->db->escape($data['keyword']) . "' 
			";
			$this->db->query($strSql);
		}

		$this->cache->delete('pastor_column');
	}

	public function deletePastorColumn($pastor_column_id)
	{
		$this->db->query("DELETE FROM `" . DB_PREFIX . "pastor_column` WHERE `pastor_column_id` = '" . (int)$pastor_column_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "pastor_column_description` WHERE `pastor_column_id` = '" . (int)$pastor_column_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'pastor_column_id` = '" . (int)$pastor_column_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "pastor_column_to_category` WHERE `pastor_column_id` = '" . (int)$pastor_column_id . "'");
		$this->cache->delete('pastor_column');
	}

	public function getPastorColumn($pastor_column_id)
	{
		$strSql = "
			SELECT DISTINCT 
				*, 
				(
					SELECT 
						keyword 
					FROM `" . DB_PREFIX . "seo_url` 
					WHERE 
						`query` = 'pastor_column_id=" . (int)$pastor_column_id . "'
				) AS keyword 
			FROM `" . DB_PREFIX . "pastor_column` 
			WHERE `pastor_column_id` = '" . (int)$pastor_column_id . "' 
		";
		$query = $this->db->query($strSql);
		return $query->row;
	}

	public function getPastorColumns($data = array())
	{
		if ($data) {
			$sql = "
				SELECT 
					vg.*, 
					vgd.*, 
					u.username AS user_id  
				FROM `" . DB_PREFIX . "pastor_column` vg 
				LEFT JOIN `" . DB_PREFIX . "pastor_column_description` vgd 
				ON (vg.pastor_column_id = vgd.pastor_column_id) 
				LEFT JOIN `" . DB_PREFIX . "user` u 
				ON (vg.added_by = u.user_id) 
				WHERE 
					vgd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
			";
			$sort_data = array(
				'vg.pastor_column_id',
				'vg.added_at',
				'vg.sort_order',
				'vgd.title'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY vg.pastor_column_id";
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

			$pastor_column_data = $this->cache->get('pastor_column.' . (int)$this->config->get('config_language_id'));

			if (!$pastor_column_data) {
				$strSql = "
					SELECT 
						vg.*, 
						vgd.*,
						u.username AS user_id  
					FROM `" . DB_PREFIX . "pastor_column` vg 
					LEFT JOIN `" . DB_PREFIX . "pastor_column_description` vgd 
					ON (vg.pastor_column_id = vgd.pastor_column_id) 
					LEFT JOIN `" . DB_PREFIX . "user` u 
					ON (vg.added_by = u.user_id) 
					WHERE 
						vgd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
					ORDER BY vg.pastor_column_id DESC";
				$query = $this->db->query($strSql);
				$pastor_column_data = $query->rows;

				$this->cache->set('pastor_column.' . (int)$this->config->get('config_language_id'), $pastor_column_data);
			}

			return $pastor_column_data;
		}
	}

	public function getPastorColumnByCategory($pastor_column_category_id)
	{
		$strSql = "
			SELECT DISTINCT 
				* 
			FROM `" . DB_PREFIX . "pastor_column` AS vg 
			LEFT JOIN `" . DB_PREFIX . "pastor_column_to_category` vg2c 
			ON (vg.pastor_column_id = vg2c.pastor_column_id) 
			WHERE 
				vg2c.pastor_column_category_id = '" . (int)$pastor_column_category_id . "' 
		";
		$query = $this->db->query($strSql);
		return $query->row;
	}

	public function getPastorColumnDescriptions($pastor_column_id)
	{
		$pastor_column_description_data = array();
		$strSql = "
			SELECT 
				* 
			FROM `" . DB_PREFIX . "pastor_column_description` 
			WHERE 
				`pastor_column_id` = '" . (int)$pastor_column_id . "' 
		";
		$query = $this->db->query($strSql);

		foreach ($query->rows as $result) {
			$pastor_column_description_data[$result['language_id']] = array(
				'title'         => $result['title'],
				'description'   => $result['description']
			);
		}

		return $pastor_column_description_data;
	}

	public function getTotalPastorColumns($data)
	{
		$strSql = "
			SELECT count(*) AS total 
			FROM `" . DB_PREFIX . "pastor_column` 
		";
		$query = $this->db->query($strSql);
		return $query->row['total'];
	}

	public function getPastorColumnToCategories($pastor_column_id)
	{
		$pastor_column_category_data = array();
		$strSql = "
			SELECT 
				* 
			FROM `" . DB_PREFIX . "pastor_column_to_category` 
			WHERE 
				`pastor_column_id` = '" . (int)$pastor_column_id . "' 
		";
		$query = $this->db->query($strSql);

		foreach ($query->rows as $result) {
			$pastor_column_to_category_data[] = $result['pastor_column_category_id'];
		}

		return $pastor_column_to_category_data;
	}

        public function getDataImages($data_id) {
                $data_images = array();

		$strSql = "
			SELECT * FROM `" . DB_PREFIX . "pastor_column_image` 
			WHERE `data_id` = '" . (int)$data_id . "' 
			ORDER BY sort_order ASC
		";
		$query = $this->db->query($strSql);

                foreach ($query->rows as $result) {
                        $data_images[] = array(
                                'image' => $result['image'],
                                'sort_order' => $result['sort_order'],
                                'description' => $this->getDataImageDescription($result['image_id'])
                        );
                }

                return $data_images;
        }

	public function getDataImageDescription($image_id) {
                $data_image_description = array();

		$strSql = "
			SELECT * FROM `" . DB_PREFIX . "pastor_column_image_description` 
			WHERE  
				`image_id` = '" . (int)$image_id . "' 
		";
                $query = $this->db->query($strSql);

                foreach ($query->rows as $result) {
                        $data_image_description[$result['language_id']] = array(
                                'name' => $result['name'],
                                'description' => $result['description']
                        );
                }

                return $data_image_description;
        }

}
