<?php
class ModelExtensionModuleEmailTemplateNewsletter extends Model {
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

	public function install() {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "emailtemplate_customer_preference` (
  `customer_id` int(11) UNSIGNED NOT NULL,
  `notification` tinyint(1) NOT NULL,
  `showcase` tinyint(1) NOT NULL,
  `date_added` DATETIME NOT NULL,
  `token` VARCHAR(32) NULL DEFAULT NULL,
  PRIMARY KEY (`customer_id`)
)");

		$layout_route_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "layout_route` WHERE `route` = 'extension/module/emailtemplate_newsletter%'");

		if (!$layout_route_query->num_rows) {
			$layout_account_query = $this->db->query("SELECT layout_id FROM `" . DB_PREFIX . "layout` WHERE `name` = 'Account' LIMIT 1");

			if ($layout_account_query->num_rows) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "layout_route` (layout_id, route) VALUES (" . (int)$layout_account_query->row['layout_id'] .", 'extension/module/emailtemplate_newsletter%')");
			}
		}
    }

    public function uninstall() {
		//$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "emailtemplate_customer_preference`");

		$this->db->query("DELETE FROM `" . DB_PREFIX . "layout_route` WHERE `route` = 'extension/module/emailtemplate_newsletter%'");
    }

    /**
     * Table check exists
     *
     * @param $name
     * @return bool
     */
    public function tableExists($name) {
        if (!$name) return false;

        $result = $this->db->query("SELECT * FROM information_schema.tables WHERE table_schema = '" . DB_DATABASE . "' AND table_name = '" . DB_PREFIX . $this->db->escape($name) . "' LIMIT 1");

        return $result->num_rows ? true : false;
    }

    /**
     * Return Event by Code
     * - Duplicated due to missing from opencart pre 3.0.2.0
     * @param $code
     * @return mixed
     */
    public function getEventByCode($code) {
        $query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "event` WHERE `code` = '" . $this->db->escape($code) . "' LIMIT 1");

        return $query->row;
    }

}
