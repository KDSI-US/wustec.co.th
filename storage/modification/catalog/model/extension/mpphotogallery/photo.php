<?php
/* This file is under Git Control by KDSI. */
class ModelExtensionMpPhotoGalleryPhoto extends Model {
	public function getPhoto($gallery_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "gallery g LEFT JOIN " . DB_PREFIX . "gallery_description gd ON (g.gallery_id = gd.gallery_id) WHERE g.gallery_id = '" . (int)$gallery_id . "' AND gd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND g.status = '1'");

		if ($query->num_rows) {
			return array(
				'gallery_id'       => $query->row['gallery_id'],
				'image'            => $query->row['image'],
				'status'           => $query->row['status'],
				'description'      => $query->row['description'],
				'sort_order'       => $query->row['sort_order'],
				'date_modified'    => $query->row['date_modified'],
				'language_id'      => $query->row['language_id'],
				'title'            => $query->row['title'],
				'date_added'       => $query->row['date_added']
			);
		} else {
			return false;
		}
	}

	public function getPhotoDescription($gallery_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "gallery_photo gp LEFT JOIN " . DB_PREFIX . "gallery_photo_description gpd ON (gp.gallery_photo_id = gpd.gallery_photo_id) WHERE gp.gallery_id = '" . (int)$gallery_id . "' AND gpd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY gp.sort_order ASC");
		return $query->rows;
	}

	public function getAlbumPhotoDescription($gallery_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "gallery_photo gp LEFT JOIN " . DB_PREFIX . "gallery_photo_description gpd ON (gp.gallery_photo_id = gpd.gallery_photo_id) WHERE gp.gallery_id = '" . (int)$gallery_id . "' AND gpd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY gp.highlight DESC, gp.sort_order ASC");

		return $query->rows;
	}

	public function getPhotosByGallery($gallery_id, $limit = '') {
		$start = 0;

		$sql = "SELECT * FROM " . DB_PREFIX . "gallery_photo gp LEFT JOIN " . DB_PREFIX . "gallery_photo_description gpd ON (gp.gallery_photo_id = gpd.gallery_photo_id) WHERE gp.gallery_id = '" . (int)$gallery_id . "' AND gpd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY gp.sort_order ASC";

		if ($limit) {
			$sql .= " LIMIT " . (int)$start . "," . (int)$limit;
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getGalleryPhotos($gallery_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "gallery_photo gp WHERE gp.gallery_id = '" . (int)$gallery_id . "' ORDER BY gp.sort_order ASC");

		return $query->rows;
	}

	public function getGallery($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "gallery` g LEFT JOIN " . DB_PREFIX . "gallery_description gd ON (g.gallery_id = gd.gallery_id) LEFT JOIN " . DB_PREFIX . "gallery_photo gp ON (g.gallery_id = gp.gallery_id) WHERE g.gallery_id = gp.gallery_id AND gd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY gp.gallery_id ORDER BY g.sort_order ASC";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 4;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalGallerys($data = array()) {

		$sql = "SELECT COUNT(DISTINCT g.gallery_id) AS total FROM " . DB_PREFIX . "gallery g LEFT JOIN " . DB_PREFIX . "gallery_description gd ON (g.gallery_id = gd.gallery_id) LEFT JOIN " . DB_PREFIX . "gallery_photo gp ON (g.gallery_id = gp.gallery_id) WHERE g.gallery_id = gp.gallery_id AND gd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY gp.gallery_id";

			$query = $this->db->query($sql);

		return $query->num_rows;
	}
}