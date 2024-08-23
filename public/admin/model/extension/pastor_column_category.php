<?php
class ModelExtensionPastorColumnCategory extends Model
{
	public function addPastorColumnCategory($data)
	{
		$strSql = "
			INSERT 
			INTO `" . DB_PREFIX . "pastor_column_category` 
			SET 
				`sort_order` = '" . (int)$data['sort_order'] . "', 
				`chk_title` = '" . (int)$data['chk_title'] . "', 
				`chk_description` = '" . (int)$data['chk_description'] . "', 
				`chk_image` = '" . (int)$data['chk_image'] . "', 
				`status` = '" . (int)$data['status'] . "' 
		";
		$this->db->query($strSql);

		$pastor_column_category_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$strSql = "
				UPDATE `" . DB_PREFIX . "pastor_column_category` 
				SET 
					`image` = '" . $this->db->escape(html_entity_decode($data['image'],ENT_QUOTES,'UTF-8')) . "' 
				WHERE 
					`pastor_column_category_id` = '" . (int)$pastor_column_category_id . "'
			";
			$this->db->query($strSql);
		}

		foreach ($data['pastor_column_category_description'] as $language_id => $value) {
			$strSql = "
				INSERT 
				INTO `" . DB_PREFIX . "pastor_column_category_description` 
				SET 
					`pastor_column_category_id` = '" . (int)$pastor_column_category_id . "', 
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
					`query` = 'pastor_column_category_id=" . (int)$pastor_column_category_id . "', 
					`keyword` = '" . $this->db->escape($data['keyword']) . "' 
			";
			$this->db->query($strSql);
		}

		$this->cache->delete('pastor_column_category');
	}

	public function editPastorColumnCategory($pastor_column_category_id, $data)
	{
		$strSql = "
			UPDATE `" . DB_PREFIX . "pastor_column_category` 
			SET 
				`sort_order` = '" . (int)$data['sort_order'] . "', 
				`chk_title` = '" . (int)$data['chk_title'] . "', 
				`chk_description` = '" . (int)$data['chk_description'] . "', 
				`chk_image` = '" . (int)$data['chk_image'] . "', 
				`status` = '" . (int)$data['status'] . "' 
			WHERE 
				`pastor_column_category_id` = '" . (int)$pastor_column_category_id . "' 
		";
		$this->db->query($strSql);

		if (isset($data['image'])) {
			$strSql = "
				UPDATE `" . DB_PREFIX . "pastor_column_category` 
				SET 
					`image` = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' 
				WHERE 
					`pastor_column_category_id` = '" . (int)$pastor_column_category_id . "' 
			";
			$this->db->query($strSql);
		}

		$strSql = "
			DELETE 
			FROM `" . DB_PREFIX . "pastor_column_category_description` 
			WHERE 
				`pastor_column_category_id` = '" . (int)$pastor_column_category_id . "' 
		";
		$this->db->query($strSql);

		foreach ($data['pastor_column_category_description'] as $language_id => $value) {
			$strSql = "
				INSERT 
				INTO `" . DB_PREFIX . "pastor_column_category_description` 
				SET 
					`pastor_column_category_id` = '" . (int)$pastor_column_category_id . "', 
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
				`query` = 'pastor_column_category_id=" . (int)$pastor_column_category_id . "' 
		";
		$this->db->query($strSql);

		if ($data['keyword']) {
			$strSql = "
				INSERT 
				INTO `" . DB_PREFIX . "seo_url` 
				SET 
					`query` = 'pastor_column_category_id=" . (int)$pastor_column_category_id . "', 
					`keyword` = '" . $this->db->escape($data['keyword']) . "'
			";
			$this->db->query($strSql);
		}

		$this->cache->delete('pastor_column_category');
	}

	public function deletePastorColumnCategory($pastor_column_category_id)
	{
		$strSql = "
			DELETE 
			FROM `" . DB_PREFIX . "pastor_column_category` 
			WHERE 
				`pastor_column_category_id` = '" . (int)$pastor_column_category_id . "'
		";
		$this->db->query($strSql);
		$strSql = "
			DELETE 
			FROM `" . DB_PREFIX . "pastor_column_category_description` 
			WHERE 
				`pastor_column_category_id` = '" . (int)$pastor_column_category_id . "'
		";
		$this->db->query($strSql);
		$strSql = "
			DELETE 
			FROM `" . DB_PREFIX . "seo_url` 
			WHERE 
				`query` = 'pastor_column_category_id=" . (int)$pastor_column_category_id . "' 
		";
		$this->db->query($strSql);
		$this->cache->delete('pastor_column_category');
	}

	public function getPastorColumnCategory($pastor_column_category_id)
	{
		$strSql = "
			SELECT DISTINCT 
				*, 
				(SELECT keyword 
					FROM `" . DB_PREFIX . "seo_url` 
					WHERE `query` = 'pastor_column_category_id=" . (int)$pastor_column_category_id . "' 
				) AS keyword 
			FROM `" . DB_PREFIX . "pastor_column_category_description` AS vgcd 
			LEFT JOIN `" . DB_PREFIX . "pastor_column_category` vgc 
			ON (vgcd.pastor_column_category_id = vgc.pastor_column_category_id) 
			WHERE 
				vgcd.pastor_column_category_id = '" . (int)$pastor_column_category_id . "' 
		";
		$query = $this->db->query($strSql);
		return $query->row;
	}

	public function getAllPastorColumnCategories()
	{
		$strSql = "
			SELECT * 
			FROM `" . DB_PREFIX . "pastor_column_category` vgc 
			LEFT JOIN `" . DB_PREFIX . "pastor_column_category_description` vgcd 
			ON (vgc.pastor_column_category_id = vgcd.pastor_column_category_id) 
			WHERE 1 
		";
		$query = $this->db->query($strSql);
		return $query->rows;
	}

	public function getPastorColumnCategoryByID($pastor_column_id)
	{
		$strSql = "
			SELECT 
				vgc.*, 
				vgcd.* 
			FROM `" . DB_PREFIX . "pastor_column_to_category` vg2c 
			LEFT JOIN `" . DB_PREFIX . "pastor_column_category` vgc 
			ON (vgc.pastor_column_category_id = vg2c.pastor_column_category_id) 
			LEFT JOIN `" . DB_PREFIX . "pastor_column_category_description` vgcd 
			ON (vgc.pastor_column_category_id = vgcd.pastor_column_category_id) 
			WHERE 
				vg2c.pastor_column_id = '" . (int)$pastor_column_id . "' 
		";
		$query = $this->db->query($strSql);
		return $query->rows;
	}

	public function getTotalPastorColumnCategory()
	{
		$strSql = "
			SELECT count(*) AS total 
			FROM `" . DB_PREFIX . "pastor_column_category` vgc 
			LEFT JOIN `" . DB_PREFIX . "pastor_column_category_description` vgcd 
			ON (vgc.pastor_column_category_id = vgcd.pastor_column_category_id)
		";
		$query = $this->db->query($strSql);
		return $query->row['total'];
	}

	public function getPastorColumnCategories($data = array())
	{
		if ($data) {

			$strSql = "
				SELECT * 
				FROM `" . DB_PREFIX . "pastor_column_category` vgc 
				LEFT JOIN `" . DB_PREFIX . "pastor_column_category_description` vgcd 
				ON (vgc.pastor_column_category_id = vgcd.pastor_column_category_id) 
				WHERE vgcd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
			";
			if (!empty($data['filter_name'])) {
				$strSql .= " AND vgcd.title LIKE '" . $this->db->escape($data['filter_name']) . "%' ";
			}
			$sort_data = array(
				'vgcd.title',
				'vgc.sort_order'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$strSql .= " ORDER BY " . $data['sort'] . " ";
			} else {
				$strSql .= " ORDER BY vgcd.title ";
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

			$pastor_column_category_data = $this->cache->get('pastor_column_category.' . (int)$this->config->get('config_language_id'));
			if (!$pastor_column_category_data) {
				$strSql = "
					SELECT 
						vgc.*, 
						vgcd.* 
					FROM `" . DB_PREFIX . "pastor_column_category` AS vgc 
					LEFT JOIN `" . DB_PREFIX . "pastor_column_category_description` AS vgcd 
					ON (vgc.pastor_column_category_id = vgcd.pastor_column_category_id) 
					WHERE 
						vgcd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
					ORDER BY vgcd.title 
				";
				$query = $this->db->query($strSql);
				$pastor_column_category_data = $query->rows;
				$this->cache->set('pastor_column_category.' . (int)$this->config->get('config_language_id'), $pastor_column_category_data);
			}

			return $pastor_column_category_data;

		}
	}

	public function getPastorColumnDescriptionByCategory($pastor_column_category_id)
	{
		$pastor_column_category_description_data = array();
		$strSql = "
			SELECT * 
			FROM `" . DB_PREFIX . "pastor_column_category_description` AS vgcd 
			WHERE 
				`pastor_column_category_id` = '" . (int)$pastor_column_category_id . "' 
		";
		$query = $this->db->query($strSql);
		foreach ($query->rows as $result) {
			$pastor_column_category_description_data[$result['language_id']] = array(
				'title'       => $result['title'],
				'description' => $result['description'],
				'meta_keyword' => $result['meta_keyword'],
				'meta_description' => $result['meta_description']
			);
		}
		return $pastor_column_category_description_data;
	}

	public function getTotalPastorColumnCategories()
	{
		$strSql = "
			SELECT 
				COUNT(*) AS total 
			FROM `" . DB_PREFIX . "pastor_column_category` 
		";
		$query = $this->db->query($strSql);
		return $query->row['total'];
	}
}
