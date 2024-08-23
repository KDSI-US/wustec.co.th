<?php
class ModelExtensionModuleEmailtemplateNewsletter extends Model {
	public function getCustomerPreference($customer_id) {
		$result = $this->db->query("SELECT * FROM " . DB_PREFIX . "emailtemplate_customer_preference WHERE customer_id = '" . (int)$customer_id . "' LIMIT 1");

		return $result->row;
	}

	public function getCustomerPreferenceByToken($token) {
		$result = $this->db->query("SELECT * FROM " . DB_PREFIX . "emailtemplate_customer_preference WHERE token = '" . $this->db->escape($token) . "' LIMIT 1");

		return $result->row;
	}

	public function addCustomerPreference($customer_id, $data) {
		$sql = "INSERT INTO " . DB_PREFIX . "emailtemplate_customer_preference SET `notification` = '" . (int)$data['notification'] . "', `showcase` = '" . (int)$data['showcase'] . "', customer_id = '" . (int)$customer_id . "',  `date_added` = NOW()";

		if (isset($data['token'])) {
			$sql .= ", `token` = '" . $this->db->escape($data['token']) . "'";
		}

		$this->db->query($sql);

		return $this->db->getLastId();
	}

	public function editCustomerPreference($customer_id, $data) {
		$sql = "UPDATE " . DB_PREFIX . "emailtemplate_customer_preference SET `notification` = '" . (int)$data['notification'] . "', `showcase` = '" . (int)$data['showcase'] . "', `date_added` = NOW()";

		if (isset($data['token'])) {
			$sql .= ", `token` = '" . $this->db->escape($data['token']) . "'";
		}

		$sql .= " WHERE customer_id = '" . (int)$customer_id . "'";

		$this->db->query($sql);

		return $this->db->countAffected();
	}

	public function deleteCustomerPreference($customer_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "emailtemplate_customer_preference WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function editNewsletter($customer_id, $newsletter) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET newsletter = '" . (int)$newsletter . "' WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function deleteEmailtemplateLogs($customer_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "emailtemplate_logs WHERE customer_id = '" . (int)$customer_id . "' AND emailtemplate_log_is_sent = 0 AND emailtemplate_key IN('customer.subscribe', 'customer.subscribe_admin', 'customer.unsubscribe', 'customer.unsubscribe_admin')");
	}
}