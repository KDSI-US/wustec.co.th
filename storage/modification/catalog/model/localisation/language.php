<?php
/* This file is under Git Control by KDSI. */
class ModelLocalisationLanguage extends Model {
	public function getLanguage($language_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "language WHERE language_id = '" . (int)$language_id . "'");

		return $query->row;
	}

	public function getLanguageByCode($language_code) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "language WHERE code = '" . (int)$language_code . "'");

		return $query->row;
	}
	
	public function getLanguages() {
		$language_data = $this->cache->get('catalog.language');

		if (!$language_data) {
			$language_data = array();

			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "language WHERE status = '1' ORDER BY sort_order, name");

			foreach ($query->rows as $result) {
				$language_data[$result['code']] = array(
					'language_id' => $result['language_id'],
					'name'        => $result['name'],
					'code'        => $result['code'],
					'locale'      => $result['locale'],
					'image'       => $result['image'],
					'directory'   => $result['directory'],
					'sort_order'  => $result['sort_order'],
					'status'      => $result['status']
				);
			}

			$this->cache->set('catalog.language', $language_data);
		}

		return $language_data;
	}
}