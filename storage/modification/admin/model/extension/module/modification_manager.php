<?php
/* This file is under Git Control by KDSI. */
class ModelExtensionModuleModificationManager extends Model {
	public function addModification($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "modification SET date_modified = NOW(), code = '" . $this->db->escape($data['code']) . "', name = '" . $this->db->escape($data['name']) . "', author = '" . $this->db->escape($data['author']) . "', version = '" . $this->db->escape($data['version']) . "', link = '" . $this->db->escape($data['link']) . "', xml = '" . $this->db->escape($data['xml']) . "', status = '" . (int)$data['status'] . "', date_added = NOW()");

		$mod_id = $this->db->getLastId();
		$strSql = "INSERT INTO `" . DB_PREFIX . "modification_order` SET `modification_id` = '" . (int)$mod_id . "', `extension_install_id` = '" . (int)$data['extension_install_id'] . "', `sort_order` = '0' ";
		$this->db->query($strSql);
			
	}

	public function deleteModification($modification_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "modification_order` WHERE modification_id = '" . (int)$modification_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE modification_id = '" . (int)$modification_id . "'");
	}

	public function enableModification($modification_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "modification SET status = '1', date_modified = NOW() WHERE modification_id = '" . (int)$modification_id . "'");
	}

	public function disableModification($modification_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "modification SET status = '0', date_modified = NOW() WHERE modification_id = '" . (int)$modification_id . "'");
	}


	public function editModification($modification_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "modification SET code = '" . $this->db->escape($data['code']) . "', name = '" . $this->db->escape($data['name']) . "', author = '" . $this->db->escape($data['author']) . "', version = '" . $this->db->escape($data['version']) . "', link = '" . $this->db->escape($data['link']) . "', xml = '" . $this->db->escape($data['xml']) . "', date_modified = NOW() WHERE modification_id = '" . (int)$modification_id . "'");
	}


	public function UpdateSorter($modification_id, $sort_order) {
		$this->db->query("UPDATE `" . DB_PREFIX . "modification_order` SET `sort_order` = '" . (int)$sort_order . "' WHERE modification_id = '" . (int)$modification_id . "'");
	}
			
	public function getModification($modification_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "modification WHERE modification_id = '" . (int)$modification_id . "'");

		return $query->row;
	}

	public function getModifications($data = array()) {

		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX ."modification_order` ( 
				`modification_id` int(11) NOT NULL, 
				`extension_install_id` int(11) NOT NULL, 
				`sort_order` int(11) NOT NULL, 
				PRIMARY KEY (`modification_id`) 
			) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci";
		$query = $this->db->query($sql);
		$qu = $this->db->query("DESCRIBE " . DB_PREFIX . "modification_order `sort_order`");
		if ($qu->num_rows == 0) {
			$strSql = "ALTER TABLE `" . DB_PREFIX ."modification_order` 
				ADD `sort_order` int(11) 
				CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL 
				AFTER `modification_id`;";
			$sqladd = $this->db->query($strSql);
		}
		$strSql = "INSERT IGNORE INTO `" . DB_PREFIX ."modification_order` ( 
				`modification_id`, 
				`extension_install_id` 
			) SELECT 
				`modification_id`, 
				`extension_install_id` 
			  FROM `" . DB_PREFIX ."modification`";
		$this->db->query($strSql);
			
		$sql = "SELECT * FROM " . DB_PREFIX . "modification";
		$sql .= " AS m LEFT JOIN `" . DB_PREFIX . "modification_order` mo ON (m.modification_id = mo.modification_id)";

		$cond = array();

		if (!empty($data['filter_name'])) {
			$cond[] = " `name` LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_xml'])) {
			$cond[] = " `xml` LIKE '%" . $this->db->escape($data['filter_xml']) . "%'";
		}

		if (!empty($data['filter_author'])) {
			$cond[] = " `author` LIKE '" . $this->db->escape($data['filter_author']) . "%'";
		}

		if ($cond) {
			$sql .= " WHERE " . implode(' AND ', $cond);
		}

		$sort_data = array(
			'date_modified' => 'GREATEST(COALESCE(date_modified, 0), COALESCE(date_added, 0))',
			'name' => 'name',
			'author' => 'author',
			'sort_order' => 'sort_order',
			'version' => 'version',
			'status' => 'status',
			'date_added' => 'date_added'
		);

		if (isset($data['sort']) && isset($sort_data[$data['sort']])) {
			$sql .= " ORDER BY " . $sort_data[$data['sort']];
		} else {
			$sql .= " ORDER BY date_modified";
		}

		if (isset($data['order']) && ($data['order'] == 'ASC')) {
			$sql .= " ASC";
		} else {
			$sql .= " DESC";
		}

		if (!isset($data['sort']) || $data['sort'] == 'date_modified') {
			$sql .= ", date_added";

			if (isset($data['order']) && ($data['order'] == 'ASC')) {
				$sql .= " ASC";
			} else {
				$sql .= " DESC";
			}
		}


		if (isset($data['sort'])&&($data['sort'] == 'sort_order')) {
			$sql .= " , name " ;
			$sql .= isset($data['order'])&&($data['order'] == 'DESC') ? " ASC" : " DESC" ;
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

	public function getTotalModifications($data = array()) {

		$cond = array();

		if (!empty($data['filter_name'])) {
			$cond[] = " `name` LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_xml'])) {
			$cond[] = " `xml` LIKE '%" . $this->db->escape($data['filter_xml']) . "%'";
		}

		if (!empty($data['filter_author'])) {
			$cond[] = " `author` LIKE '%" . $this->db->escape($data['filter_author']) . "%'";
		}

		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "modification";

		if ($cond) {
			$sql .= " WHERE " . implode(' AND ', $cond);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getModificationByCode($code) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "modification WHERE code = '" . $this->db->escape($code) . "'");

		return $query->row;
	}

	public function install() {
		$data = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "modification`");
		$column_names = array();

		foreach ($data->rows as $row) {
			$column_names[] = $row['Field'];

			switch($row['Field']) {
				case 'xml' && strtoupper($row['Type']) == 'TEXT':
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "modification` CHANGE `xml` `xml` MEDIUMTEXT NOT NULL");
					break;
			}
		}

		if (!in_array('date_modified', $column_names)) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "modification` ADD COLUMN  `date_modified` datetime");

			$this->db->query("UPDATE `" . DB_PREFIX . "modification` SET `date_modified` = `date_added` WHERE `date_modified` = '0000-00-00 00:00:00'");
		}
	}
}
