<?php
/* This file is under Git Control by KDSI. */
class ModelExtensionVideoGallery extends Model
{
	public function getVideoGalleryByCategory($data)
	{
		$strSql = "
			SELECT DISTINCT * 
			FROM `" . DB_PREFIX . "video_gallery` vg 
			LEFT JOIN `" . DB_PREFIX . "video_gallery_description` vgd 
			ON (vg.video_gallery_id = vgd.video_gallery_id) 
			LEFT JOIN `" . DB_PREFIX . "video_gallery_to_category` vg2c 
			ON (vg.video_gallery_id = vg2c.video_gallery_id) 
			WHERE 
				vg2c.video_gallery_category_id = '" . (int)$data['video_gallery_category_id'] . "' 
				AND vgd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
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

	public function getTotalVideoGalleries($data)
	{
		$strSql = "
			SELECT count(*) AS total 
			FROM `" . DB_PREFIX . "video_gallery` 
		";
		$query = $this->db->query($strSql);
		return $query->row['total'];
	}
	
	public function getTotalVideoGalleryByCategory($data)
	{
		$strSql = "
			SELECT count(*) AS total 
			FROM `" . DB_PREFIX . "video_gallery` vg 
			LEFT JOIN `" . DB_PREFIX . "video_gallery_description` vgd 
			ON (vg.video_gallery_id = vgd.video_gallery_id) 
			LEFT JOIN `" . DB_PREFIX . "video_gallery_to_category` vg2c 
			ON (vg.video_gallery_id = vg2c.video_gallery_id) 
			WHERE 
				vg2c.video_gallery_category_id = '" . (int)$data['video_gallery_category_id'] . "' 
				AND vgd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
		";
		$query = $this->db->query($strSql);
		return $query->row['total'];
	}

	public function getVideoGalleryCategoryData($video_gallery_category_id)
	{
		$strSql = "
			SELECT * 
			FROM `" . DB_PREFIX . "video_gallery_category` vgc 
			LEFT JOIN `" . DB_PREFIX . "video_gallery_category_description` vgcd 
			ON(vgc.video_gallery_category_id = vgcd.video_gallery_category_id) 
			WHERE 
				vgc.video_gallery_category_id = '" . $video_gallery_category_id . "' 
				AND vgcd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
		";
		$query = $this->db->query($strSql);
		return $query->row;
	}
}
