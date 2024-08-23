<?php
/* This file is under Git Control by KDSI. */
class ModelExtensionVideoGalleryCategory extends Model
{
	public function getVideoGalleryPhotos($data)
	{
		$strSql = "
			SELECT * 
			FROM `" . DB_PREFIX . "video_gallery_category` vg 
			LEFT JOIN `" . DB_PREFIX . "video_gallery_category_description` vgcd 
			ON(vg.video_gallery_category_id = vgcd.video_gallery_category_id) 
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

	public function getTotalVideoGalleries($data)
	{
		$strSql = "
			SELECT count(*) AS total 
			FROM `" . DB_PREFIX . "video_gallery` vg 
			LEFT JOIN `" . DB_PREFIX . "video_gallery_description` vgd 
			ON (vg.video_gallery_id = vgd.video_gallery_id) 
		";
		$query = $this->db->query($strSql);
		return $query->row['total'];
	}

	public function getTotalVideoGalleryCategory()
	{
		$strSql = "
			SELECT count(*) AS total 
			FROM `" . DB_PREFIX . "video_gallery_category` vgc 
			LEFT JOIN `" . DB_PREFIX . "video_gallery_category_description` vgcd 
			ON (vgc.video_gallery_category_id = vgcd.video_gallery_category_id)
		";
		$query = $this->db->query($strSql);
		return $query->row['total'];
	}

	public function getVideoGalleryCategories($data = array())
	{
		if ($data) {

			$strSql = "
				SELECT * 
				FROM `" . DB_PREFIX . "video_gallery_category` vgc 
				LEFT JOIN `" . DB_PREFIX . "video_gallery_category_description` vgcd 
				ON (vgc.video_gallery_category_id = vgcd.video_gallery_category_id) 
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

			$video_gallery_category_data = $this->cache->get('video_gallery_category.' . (int)$this->config->get('config_language_id'));
			if (!$video_gallery_category_data) {
				$strSql = "
					SELECT 
						vgc.*, 
						vgcd.* 
					FROM `" . DB_PREFIX . "video_gallery_category` AS vgc 
					LEFT JOIN `" . DB_PREFIX . "video_gallery_category_description` AS vgcd 
					ON (vgc.video_gallery_category_id = vgcd.video_gallery_category_id) 
					WHERE 
						vgcd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
					ORDER BY vgcd.title 
				";
				$query = $this->db->query($strSql);
				$video_gallery_category_data = $query->rows;
				$this->cache->set('video_gallery_category.' . (int)$this->config->get('config_language_id'), $video_gallery_category_data);
			}

			return $video_gallery_category_data;

		}
	}
}
