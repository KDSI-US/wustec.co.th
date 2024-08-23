<?php
class ModelExtensionPastorColumnCategory extends Model
{
	public function getPastorColumnPhotos($data)
	{
		$strSql = "
			SELECT * 
			FROM `" . DB_PREFIX . "pastor_column_category` vg 
			LEFT JOIN `" . DB_PREFIX . "pastor_column_category_description` vgcd 
			ON(vg.pastor_column_category_id = vgcd.pastor_column_category_id) 
			WHERE 
				vgcd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
		";
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

	public function getTotalPastorColumns($data)
	{
		$strSql = "
			SELECT count(*) AS total 
			FROM `" . DB_PREFIX . "pastor_column` vg 
			LEFT JOIN `" . DB_PREFIX . "pastor_column_description` vgd 
			ON (vg.pastor_column_id = vgd.pastor_column_id) 
		";
		$query = $this->db->query($strSql);
		return $query->row['total'];
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
}
