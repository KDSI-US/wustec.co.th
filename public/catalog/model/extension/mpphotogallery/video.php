<?php
class ModelExtensionMpPhotoGalleryVideo extends Model {
	public function getVideoDescription($gallery_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "gallery_video gp LEFT JOIN " . DB_PREFIX . "gallery_video_description gpd ON (gp.gallery_video_id = gpd.gallery_video_id) WHERE gp.gallery_id = '" . (int)$gallery_id . "' AND gpd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY gp.sort_order ASC");
		return $query->rows;
	}

	public function getAlbumVideoDescription($gallery_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "gallery_video gp LEFT JOIN " . DB_PREFIX . "gallery_video_description gpd ON (gp.gallery_video_id = gpd.gallery_video_id) WHERE gp.gallery_id = '" . (int)$gallery_id . "' AND gpd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY gp.highlight DESC, gp.sort_order ASC");

		return $query->rows;
	}

	public function getVideosByGallery($gallery_id, $limit = '') {
		$start = 0;

		$sql = "SELECT * FROM " . DB_PREFIX . "gallery_video gp LEFT JOIN " . DB_PREFIX . "gallery_video_description gpd ON (gp.gallery_video_id = gpd.gallery_video_id) WHERE gp.gallery_id = '" . (int)$gallery_id . "' AND gpd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY gp.sort_order ASC";

		if ($limit) {
			$sql .= " LIMIT " . (int)$start . "," . (int)$limit;
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getGalleryVideos($gallery_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "gallery_video gp WHERE gp.gallery_id = '" . (int)$gallery_id . "' ORDER BY gp.sort_order ASC");

		return $query->rows;
	}

	public function getGallery($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "gallery` g LEFT JOIN " . DB_PREFIX . "gallery_description gd ON (g.gallery_id = gd.gallery_id) LEFT JOIN " . DB_PREFIX . "gallery_video gp ON (g.gallery_id = gp.gallery_id) WHERE g.gallery_id = gp.gallery_id AND gd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY gp.gallery_id ORDER BY g.sort_order ASC";

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

		$sql = "SELECT COUNT(DISTINCT g.gallery_id) AS total FROM " . DB_PREFIX . "gallery g LEFT JOIN " . DB_PREFIX . "gallery_description gd ON (g.gallery_id = gd.gallery_id) LEFT JOIN " . DB_PREFIX . "gallery_video gp ON (g.gallery_id = gp.gallery_id) WHERE g.gallery_id = gp.gallery_id AND gd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY gp.gallery_id";

			$query = $this->db->query($sql);

		return $query->num_rows;
	}
}