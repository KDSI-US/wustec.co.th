<?php
class ModelExtensionMpPhotoGalleryAlbum extends Model {
	public function updateViewed($gallery_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "gallery SET viewed = (viewed + 1) WHERE gallery_id = '" . (int)$gallery_id . "'");
	}

	public function getGalleryinfo($gallery_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "gallery` g LEFT JOIN " . DB_PREFIX . "gallery_description gd ON (g.gallery_id = gd.gallery_id) WHERE g.gallery_id = '" . (int)$gallery_id . "' AND gd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getGallery($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "gallery` g LEFT JOIN " . DB_PREFIX . "gallery_description gd ON (g.gallery_id = gd.gallery_id) WHERE gd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY g.date_added DESC, g.sort_order ASC";

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

		$sql = "SELECT COUNT(DISTINCT g.gallery_id) AS total FROM " . DB_PREFIX . "gallery g LEFT JOIN " . DB_PREFIX . "gallery_description gd ON (g.gallery_id = gd.gallery_id)";

			$sql .= " WHERE gd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

			$query = $this->db->query($sql);

		return $query->row['total'];

	}

	public function getTotalGalleryPhotos($gallery_id) {
		$query = $this->db->query("SELECT COUNT(*) as total FROM " . DB_PREFIX . "gallery_photo WHERE gallery_id = '" . (int)$gallery_id . "' ORDER BY sort_order ASC");

		return $query->row['total'];
	}
}