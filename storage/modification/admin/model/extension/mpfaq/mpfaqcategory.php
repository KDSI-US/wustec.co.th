<?php
/* This file is under Git Control by KDSI. */
class ModelExtensionMpFaqMpfaqcategory extends Model {
	public function tablebnao() {

		/*--
		-- Table structure for table `oc_mpfaqcategory`
		--*/

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mpfaqcategory` (
		  `mpfaqcategory_id` int(11) NOT NULL AUTO_INCREMENT,
		  `sort_order` int(3) NOT NULL,
		  `status` tinyint(4) NOT NULL,
		  PRIMARY KEY (`mpfaqcategory_id`)
		) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;");


		/*--
		-- Table structure for table `oc_mpfaqcategory_description`
		--
		*/
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mpfaqcategory_description` (
		  `mpfaqcategory_id` int(11) NOT NULL,
		  `language_id` int(11) NOT NULL,
		  `name` varchar(64) NOT NULL,
		  PRIMARY KEY (`mpfaqcategory_id`,`language_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		/*--
		-- Table structure for table `oc_mpfaqquestion`
		--
		*/
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mpfaqquestion` (
		  `mpfaqquestion_id` int(11) NOT NULL AUTO_INCREMENT,
		  `mpfaqcategory_id` int(11) NOT NULL,
		  `sort_order` int(11) NOT NULL DEFAULT '0',
		  `status` tinyint(1) NOT NULL DEFAULT '0',
		  `date_added` datetime NOT NULL,
		  `date_modified` datetime NOT NULL,
		  PRIMARY KEY (`mpfaqquestion_id`)
		) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;");


		/*--
		-- Table structure for table `oc_mpfaqquestion_description`
		--
		*/
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mpfaqquestion_description` (
		  `mpfaqquestion_id` int(11) NOT NULL,
		  `mpfaqcategory_id` int(11) NOT NULL,
		  `language_id` int(11) NOT NULL,
		  `question` varchar(255) NOT NULL,
		  `answer` text NOT NULL
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

	}

	public function addMpfaqcategory($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "mpfaqcategory SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "'");

		$mpfaqcategory_id = $this->db->getLastId();

		foreach ($data['mpfaqcategory_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mpfaqcategory_description SET mpfaqcategory_id = '" . (int)$mpfaqcategory_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}

		return $mpfaqcategory_id;
	}

	public function editMpfaqcategory($mpfaqcategory_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "mpfaqcategory SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "' WHERE mpfaqcategory_id = '" . (int)$mpfaqcategory_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "mpfaqcategory_description WHERE mpfaqcategory_id = '" . (int)$mpfaqcategory_id . "'");

		foreach ($data['mpfaqcategory_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mpfaqcategory_description SET mpfaqcategory_id = '" . (int)$mpfaqcategory_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}
	}

	public function deleteMpfaqcategory($mpfaqcategory_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpfaqcategory WHERE mpfaqcategory_id = '" . (int)$mpfaqcategory_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpfaqcategory_description WHERE mpfaqcategory_id = '" . (int)$mpfaqcategory_id . "'");

		$q = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpfaqquestion WHERE mpfaqcategory_id = '" . (int)$mpfaqcategory_id . "'")->rows;

		foreach ($q as $key => $value) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "mpfaqquestion_description WHERE mpfaqquestion_id = '" . (int)$value['mpfaqquestion_id'] . "'");

			$this->db->query("DELETE FROM " . DB_PREFIX . "mpfaqquestion WHERE mpfaqcategory_id = '" . (int)$mpfaqcategory_id . "'");

		}

	}

	public function getMpfaqcategory($mpfaqcategory_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpfaqcategory f LEFT JOIN " . DB_PREFIX . "mpfaqcategory_description fd ON (f.mpfaqcategory_id = fd.mpfaqcategory_id) WHERE f.mpfaqcategory_id = '" . (int)$mpfaqcategory_id . "' AND fd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getMpfaqcategorys($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "mpfaqcategory f LEFT JOIN " . DB_PREFIX . "mpfaqcategory_description fd ON (f.mpfaqcategory_id = fd.mpfaqcategory_id) WHERE fd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND lower(fd.name) LIKE '". $this->db->escape(utf8_strtolower($data['filter_name'])) ."%'";
		}

		$sort_data = array(
			'fd.name',
			'f.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY fd.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getMpfaqcategoryDescriptions($mpfaqcategory_id) {
		$mpfaqcategory_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpfaqcategory_description WHERE mpfaqcategory_id = '" . (int)$mpfaqcategory_id . "'");

		foreach ($query->rows as $result) {
			$mpfaqcategory_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $mpfaqcategory_data;
	}

	public function getTotalMpfaqcategorys($data=array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mpfaqcategory f LEFT JOIN " . DB_PREFIX . "mpfaqcategory_description fd ON (f.mpfaqcategory_id = fd.mpfaqcategory_id) WHERE fd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND LCASE(fd.name) LIKE '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
		}


		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}