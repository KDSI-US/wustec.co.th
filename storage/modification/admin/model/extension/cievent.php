<?php
/* This file is under Git Control by KDSI. */
class ModelExtensionCiEvent extends Model {
	public function addEvent($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "event` SET `code` = '" . $this->db->escape($data['code']) . "', `trigger` = '" . $this->db->escape($data['trigger']) . "', `action` = '" . $this->db->escape($data['action']) . "', `sort_order` = '" . (int)$data['sort_order'] . "', `status` = '" . (int)$data['status'] . "'");
	
		return $this->db->getLastId();
	}

	public function editEvent($data, $event_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "event` SET `trigger` = '" . $this->db->escape($data['trigger']) . "', `action` = '" . $this->db->escape($data['action']) . "', `sort_order` = '" . (int)$data['sort_order'] . "', `status` = '" . (int)$data['status'] . "' WHERE `event_id` = '" . (int)$event_id . "'");
	}

	public function getEvent($event_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "event` WHERE `event_id` = '" . (int)$event_id . "'");

		return $query->row;
	}

	public function getEventbyField($field) {
		$query = $this->db->query("SELECT `". $field. "` FROM `" . DB_PREFIX . "event` WHERE `event_id` > 0 GROUP BY `". $field ."`");

		return $query->rows;
	}

	public function deleteEvent($event_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "event` WHERE `event_id` = '" . (int)$event_id . "'");
	}

	public function enableEvent($event_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "event` SET `status` = '1' WHERE event_id = '" . (int)$event_id . "'");
	}
	
	public function disableEvent($event_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "event` SET `status` = '0' WHERE event_id = '" . (int)$event_id . "'");
	}

	public function getEvents($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "event` WHERE event_id > 0";

		$this->filter($sql, $data);

		if(!empty($data['group_code'])) {
			$sql .= " GROUP BY `code`";
		}

		$sort_data = array(
			'code',
			'trigger',
			'action',
			'sort_order',
			'status',
			'date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY `" . $data['sort'] . "`";
		} else {
			$sql .= " ORDER BY sort_order";
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

	public function filter(&$sql, &$data = array()) {
		if (!empty($data['filter_code'])) {
			$sql .= " AND `code` LIKE '%" . $this->db->escape($data['filter_code']) . "%'";
		}

		if (!empty($data['filter_trigger'])) {
			// 'catalog/model/checkout/order/*/after'
			// 'catalog/model/checkout/*/after'
			// 'catalog/model/*/after'
			$trigger = str_replace(array('\*', '\?'), array('.*', '.'), preg_quote($this->db->escape($data['filter_trigger']), '/'));
			$sql .= " AND `trigger`  REGEXP '\^{$trigger}$'";
		}

		if (!empty($data['filter_action'])) {
			$sql .= " AND `action` LIKE '%" . $this->db->escape($data['filter_action']) . "%'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND status = '" . (int)$data['filter_status'] . "'";
		}
	}

	public function getTotalEvents($data = []) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "event` WHERE event_id > 0";

		$this->filter($sql, $data);

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}