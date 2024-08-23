<?php
class ModelExtensionPastorColumn extends Model
{
	public function getPastorColumnById($pastor_column_id)
	{
		$strSql = "
			SELECT DISTINCT * 
			FROM `" . DB_PREFIX . "pastor_column` vg 
			LEFT JOIN `" . DB_PREFIX . "pastor_column_description` vgd 
			ON (vg.pastor_column_id = vgd.pastor_column_id) 
			WHERE 
				vg.pastor_column_id = '" . (int)$pastor_column_id . "' 
				AND vgd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
			 	AND vg.status = '1' 
		";
		$query = $this->db->query($strSql);
		return $query->row;
	}

	public function getPastorColumnByCategory($data)
	{
		$strSql = "
			SELECT DISTINCT * 
			FROM `" . DB_PREFIX . "pastor_column` vg 
			LEFT JOIN `" . DB_PREFIX . "pastor_column_description` vgd 
			ON (vg.pastor_column_id = vgd.pastor_column_id) 
			LEFT JOIN `" . DB_PREFIX . "pastor_column_to_category` vg2c 
			ON (vg.pastor_column_id = vg2c.pastor_column_id) 
			WHERE 
				vg2c.pastor_column_category_id = '" . (int)$data['pastor_column_category_id'] . "' 
				AND vgd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
			 	AND vg.status = '1' 
		";

		if (isset($data['sort'])) {
			$strSql .= " ORDER BY " . $data['sort'] . " ";
		} else {
			$strSql .= " ORDER BY vg.added_at ";
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

	public function getTotalPastorColumns($data)
	{
		$strSql = "
			SELECT count(*) AS total 
			FROM `" . DB_PREFIX . "pastor_column` 
		";
		$query = $this->db->query($strSql);
		return $query->row['total'];
	}
	
	public function getTotalPastorColumnByCategory($data)
	{
		$strSql = "
			SELECT count(*) AS total 
			FROM `" . DB_PREFIX . "pastor_column` vg 
			LEFT JOIN `" . DB_PREFIX . "pastor_column_description` vgd 
			ON (vg.pastor_column_id = vgd.pastor_column_id) 
			LEFT JOIN `" . DB_PREFIX . "pastor_column_to_category` vg2c 
			ON (vg.pastor_column_id = vg2c.pastor_column_id) 
			WHERE 
				vg2c.pastor_column_category_id = '" . (int)$data['pastor_column_category_id'] . "' 
				AND vgd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
		";
		$query = $this->db->query($strSql);
		return $query->row['total'];
	}

	public function getPastorColumnCategoryData($pastor_column_category_id)
	{
		$strSql = "
			SELECT * 
			FROM `" . DB_PREFIX . "pastor_column_category` vgc 
			LEFT JOIN `" . DB_PREFIX . "pastor_column_category_description` vgcd 
			ON(vgc.pastor_column_category_id = vgcd.pastor_column_category_id) 
			WHERE 
				vgc.pastor_column_category_id = '" . $pastor_column_category_id . "' 
				AND vgcd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
		";
		$query = $this->db->query($strSql);
		return $query->row;
	}
}
