<?php
/* This file is under Git Control by KDSI. */
class ModelExtensionVideoGalleryCategory extends Model
{
	public function addVideoGalleryCategory($data)
	{
		$strSql = "
			INSERT 
			INTO `" . DB_PREFIX . "video_gallery_category` 
			SET 
				`sort_order` = '" . (int)$data['sort_order'] . "', 
				`chk_video_category_title` = '" . (int)$data['chk_video_category_title'] . "', 
				`chk_video_category_description` = '" . (int)$data['chk_video_category_description'] . "', 
				`chk_video_category_image` = '" . (int)$data['chk_video_category_image'] . "', 
				`status` = '" . (int)$data['status'] . "' 
		";
		$this->db->query($strSql);

		$video_gallery_category_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$strSql = "
				UPDATE `" . DB_PREFIX . "video_gallery_category` 
				SET 
					`image` = '" . $this->db->escape(html_entity_decode($data['image'],ENT_QUOTES,'UTF-8')) . "' 
				WHERE 
					`video_gallery_category_id` = '" . (int)$video_gallery_category_id . "'
			";
			$this->db->query($strSql);
		}

		foreach ($data['video_gallery_category_description'] as $language_id => $value) {
			$strSql = "
				INSERT 
				INTO `" . DB_PREFIX . "video_gallery_category_description` 
				SET 
					`video_gallery_category_id` = '" . (int)$video_gallery_category_id . "', 
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
					`query` = 'video_gallery_category_id=" . (int)$video_gallery_category_id . "', 
					`keyword` = '" . $this->db->escape($data['keyword']) . "' 
			";
			$this->db->query($strSql);
		}

		$this->cache->delete('video_gallery_category');
	}

	public function editVideoGalleryCategory($video_gallery_category_id, $data)
	{
		$strSql = "
			UPDATE `" . DB_PREFIX . "video_gallery_category` 
			SET 
				`sort_order` = '" . (int)$data['sort_order'] . "', 
				`chk_video_category_title` = '" . (int)$data['chk_video_category_title'] . "', 
				`chk_video_category_description` = '" . (int)$data['chk_video_category_description'] . "', 
				`chk_video_category_image` = '" . (int)$data['chk_video_category_image'] . "', 
				`status` = '" . (int)$data['status'] . "' 
			WHERE 
				`video_gallery_category_id` = '" . (int)$video_gallery_category_id . "' 
		";
		$this->db->query($strSql);

		if (isset($data['image'])) {
			$strSql = "
				UPDATE `" . DB_PREFIX . "video_gallery_category` 
				SET 
					`image` = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' 
				WHERE 
					`video_gallery_category_id` = '" . (int)$video_gallery_category_id . "' 
			";
			$this->db->query($strSql);
		}

		$strSql = "
			DELETE 
			FROM `" . DB_PREFIX . "video_gallery_category_description` 
			WHERE 
				`video_gallery_category_id` = '" . (int)$video_gallery_category_id . "' 
		";
		$this->db->query($strSql);

		foreach ($data['video_gallery_category_description'] as $language_id => $value) {
			$strSql = "
				INSERT 
				INTO `" . DB_PREFIX . "video_gallery_category_description` 
				SET 
					`video_gallery_category_id` = '" . (int)$video_gallery_category_id . "', 
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
				`query` = 'video_gallery_category_id=" . (int)$video_gallery_category_id . "' 
		";
		$this->db->query($strSql);

		if ($data['keyword']) {
			$strSql = "
				INSERT 
				INTO `" . DB_PREFIX . "seo_url` 
				SET 
					`query` = 'video_gallery_category_id=" . (int)$video_gallery_category_id . "', 
					`keyword` = '" . $this->db->escape($data['keyword']) . "'
			";
			$this->db->query($strSql);
		}

		$this->cache->delete('video_gallery_category');
	}

	public function deleteVideoGalleryCategory($video_gallery_category_id)
	{
		$strSql = "
			DELETE 
			FROM `" . DB_PREFIX . "video_gallery_category` 
			WHERE 
				`video_gallery_category_id` = '" . (int)$video_gallery_category_id . "'
		";
		$this->db->query($strSql);
		$strSql = "
			DELETE 
			FROM `" . DB_PREFIX . "video_gallery_category_description` 
			WHERE 
				`video_gallery_category_id` = '" . (int)$video_gallery_category_id . "'
		";
		$this->db->query($strSql);
		$strSql = "
			DELETE 
			FROM `" . DB_PREFIX . "seo_url` 
			WHERE 
				`query` = 'video_gallery_category_id=" . (int)$video_gallery_category_id . "' 
		";
		$this->db->query($strSql);
		$this->cache->delete('video_gallery_category');
	}

	public function getVideoGalleryCategory($video_gallery_category_id)
	{
		$strSql = "
			SELECT DISTINCT 
				*, 
				(SELECT keyword 
					FROM `" . DB_PREFIX . "seo_url` 
					WHERE `query` = 'video_gallery_category_id=" . (int)$video_gallery_category_id . "' 
				) AS keyword 
			FROM `" . DB_PREFIX . "video_gallery_category_description` AS vgcd 
			LEFT JOIN `" . DB_PREFIX . "video_gallery_category` vgc 
			ON (vgcd.video_gallery_category_id = vgc.video_gallery_category_id) 
			WHERE 
				vgcd.video_gallery_category_id = '" . (int)$video_gallery_category_id . "' 
		";
		$query = $this->db->query($strSql);
		return $query->row;
	}

	public function getAllVideoGalleryCategories()
	{
		$strSql = "
			SELECT * 
			FROM `" . DB_PREFIX . "video_gallery_category` vgc 
			LEFT JOIN `" . DB_PREFIX . "video_gallery_category_description` vgcd 
			ON (vgc.video_gallery_category_id = vgcd.video_gallery_category_id) 
			WHERE 1 
		";
		$query = $this->db->query($strSql);
		return $query->rows;
	}

	public function getVideoGalleryCategoryByID($video_gallery_id)
	{
		$strSql = "
			SELECT 
				vgc.*, 
				vgcd.* 
			FROM `" . DB_PREFIX . "video_gallery_to_category` vg2c 
			LEFT JOIN `" . DB_PREFIX . "video_gallery_category` vgc 
			ON (vgc.video_gallery_category_id = vg2c.video_gallery_category_id) 
			LEFT JOIN `" . DB_PREFIX . "video_gallery_category_description` vgcd 
			ON (vgc.video_gallery_category_id = vgcd.video_gallery_category_id) 
			WHERE 
				vg2c.video_gallery_id = '" . (int)$video_gallery_id . "' 
		";
		$query = $this->db->query($strSql);
		return $query->rows;
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

	public function getVideoGalleryDescriptionByCategory($video_gallery_category_id)
	{
		$video_gallery_category_description_data = array();
		$strSql = "
			SELECT * 
			FROM `" . DB_PREFIX . "video_gallery_category_description` AS vgcd 
			WHERE 
				`video_gallery_category_id` = '" . (int)$video_gallery_category_id . "' 
		";
		$query = $this->db->query($strSql);
		foreach ($query->rows as $result) {
			$video_gallery_category_description_data[$result['language_id']] = array(
				'title'       => $result['title'],
				'description' => $result['description'],
				'meta_keyword' => $result['meta_keyword'],
				'meta_description' => $result['meta_description']
			);
		}
		return $video_gallery_category_description_data;
	}

	public function getTotalVideoGalleryCategories()
	{
		$strSql = "
			SELECT 
				COUNT(*) AS total 
			FROM `" . DB_PREFIX . "video_gallery_category` 
		";
		$query = $this->db->query($strSql);
		return $query->row['total'];
	}
}
