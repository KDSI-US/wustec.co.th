<?php
class ModelModuleTicketTicketrequest extends Model {
	public function deleteTicketrequest($ticketrequest_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ticketrequest WHERE ticketrequest_id = '" . (int)$ticketrequest_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ticketrequest_chat WHERE ticketrequest_id = '" . (int)$ticketrequest_id . "'");

	}

	public function getTicketrequest($ticketrequest_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "ticketrequest WHERE ticketrequest_id = '" . (int)$ticketrequest_id . "'");

		return $query->row;
	}

	public function getTicketrequests($data = array()) {
		$sql = "SELECT *, (SELECT ts.name FROM " . DB_PREFIX . "ticketstatus_description ts WHERE ts.ticketstatus_id = t.ticketstatus_id AND ts.language_id = '" . (int)$this->config->get('config_language_id') . "') AS ticketstatus, (SELECT td.title FROM " . DB_PREFIX . "ticketdepartment_description td WHERE td.ticketdepartment_id = t.ticketdepartment_id AND td.language_id = '" . (int)$this->config->get('config_language_id') . "') AS ticketdepartment, (SELECT tu.name FROM " . DB_PREFIX . "ticketuser tu WHERE tu.ticketuser_id = t.ticketuser_id) AS ticketuser_name FROM " . DB_PREFIX . "ticketrequest t WHERE t.ticketrequest_id > 0";

		$implode = array();

		if (!empty($data['filter_email'])) {
			$implode[] = "t.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (!empty($data['filter_subject'])) {
			$implode[] = "t.subject LIKE '" . $this->db->escape($data['filter_subject']) . "%'";
		}

		if (!empty($data['filter_ticketrequest_id'])) {
			$implode[] = "t.ticketrequest_id = '" . (int)$data['filter_ticketrequest_id'] . "'";
		}

		if (!empty($data['filter_ticketstatus'])) {
			$implode[] = "t.ticketstatus_id = '" . (int)$data['filter_ticketstatus'] . "'";
		}

		if (!empty($data['filter_ticketdepartment'])) {
			$implode[] = "t.ticketdepartment_id = '" . (int)$data['filter_ticketdepartment'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(t.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$implode[] = "DATE(t.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$sort_data = array(
			't.ticketrequest_id',
			't.ticketdepartment_id',
			't.subject',
			't.email',
			'ticketstatus',
			't.ticketdepartment',
			't.date_added',
			't.date_modified',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY t.date_modified";
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

	public function getTotalTicketrequests($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "ticketrequest t WHERE ticketrequest_id > 0";

		$implode = array();

		if (!empty($data['filter_email'])) {
			$implode[] = "t.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (!empty($data['filter_subject'])) {
			$implode[] = "t.subject LIKE '" . $this->db->escape($data['filter_subject']) . "%'";
		}

		if (!empty($data['filter_ticketrequest_id'])) {
			$implode[] = "t.ticketrequest_id = '" . (int)$data['filter_ticketrequest_id'] . "'";
		}

		if (!empty($data['filter_ticketstatus'])) {
			$implode[] = "t.ticketstatus_id = '" . (int)$data['filter_ticketstatus'] . "'";
		}

		if (!empty($data['filter_ticketdepartment'])) {
			$implode[] = "t.ticketdepartment_id = '" . (int)$data['filter_ticketdepartment'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(t.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$implode[] = "DATE(t.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTicketRequestChats($ticketrequest_id, $data = array()) {
		$sql = "SELECT tc.ticketrequest_chat_id, tc.ticketrequest_id, tc.ticketuser_id, tc.message_from_user_id, tc.client_type, tc.message, tc.attachments, tc.date_added FROM `" . DB_PREFIX . "ticketrequest_chat` tc LEFT JOIN `" . DB_PREFIX . "ticketrequest` t ON(tc.ticketrequest_id = t.ticketrequest_id) WHERE tc.ticketrequest_id = '" . (int)$ticketrequest_id . "' ORDER BY tc.ticketrequest_chat_id ASC";

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

	public function getTotalTicketRequestChats($ticketrequest_id) {
		$query = $this->db->query("SELECT COUNT(*) as total FROM `" . DB_PREFIX . "ticketrequest_chat` tc LEFT JOIN `" . DB_PREFIX . "ticketrequest` t ON(tc.ticketrequest_id = t.ticketrequest_id) WHERE tc.ticketrequest_id = '" . (int)$ticketrequest_id . "'");

		return $query->row['total'];
	}

	public function addTicketRequestChat($ticketrequest_id, $data) {
		if(!empty($data['attachments'])) {
			$attachments_json_encode = json_encode($data['attachments']);
		} else {
			$attachments_json_encode = '';
		}

		$this->db->query("INSERT INTO " . DB_PREFIX . "ticketrequest_chat SET ticketrequest_id = '". (int)$ticketrequest_id ."', ticketuser_id = '". (int)$data['ticketuser_id'] ."', message_from_user_id = '". (int)$data['message_from_user_id'] ."', message = '". $this->db->escape($data['message']) ."', client_type = '". $this->db->escape($data['client_type']) ."', attachments = '". $this->db->escape($attachments_json_encode) ."', date_added = NOW()");

		$this->db->query("UPDATE " . DB_PREFIX . "ticketrequest SET ticketstatus_id = '". (int)$data['ticketstatus_id'] ."', date_modified = NOW() WHERE ticketrequest_id = '". (int)$ticketrequest_id ."'");

		$this->load->language('module_ticket/mail_ticketreply');
		$this->load->model('tool/upload');
		$this->load->model('user/user');
		$ticketsetting_email = (array)$this->config->get('ticketsetting_email');

		$ticketrequest_info = $this->getTicketRequest($ticketrequest_id);

		// Send Email To Admin when user send a reply
		if(!empty($ticketsetting_email['adminreplytouser']['status']) && $ticketrequest_info) {
			$catalog_url = isset($this->request->server['HTTPS']) ? HTTPS_CATALOG : HTTP_CATALOG;

			$subject = sprintf($this->language->get('text_subject'), $ticketrequest_info['subject']);

			$link = str_replace('&amp;', '&', $catalog_url .'index.php?route=support/ticket_view&ticketrequest_id=' . $ticketrequest_id);
			$user_info = $this->model_user_user->getUser($this->user->getId());
			if($user_info) {
				$admin_user = $user_info['firstname'] .' '. $user_info['lastname'];
			} else {
				$admin_user = '';
			}

			$message = sprintf($this->language->get('text_welcome'), $admin_user, html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'), $ticketrequest_id) . "<br/><br/>";

			$message .= $this->language->get('text_additional') . "<br/>";

			$message .= $link . "<br/><br/>";

			$message .= nl2br($data['message']);
			$message .= "<br/><br/>";

			$file_data = array();
			if(!empty($data['attachments'])) {
				foreach($data['attachments'] as $attachment_value) {
					$upload_info = $this->model_tool_upload->getUploadByCode($attachment_value);
					if ($upload_info) {
						$orginal_name = DIR_UPLOAD . $upload_info['filename'];
						$temp_file_name = DIR_UPLOAD . $upload_info['name'];
						copy($orginal_name, $temp_file_name);
						$file_data[] = $temp_file_name;
					}
				}
			}

			$html = '';
			$html .= '<html>';
			$html .= '<head></thead>';
			$html .= '<body style="line-height: 25px;">';
			$html .= $message;
			$html .= '</body>';
			$html .= '</html>';

			if(VERSION >= '3.0.0.0') {
				$mail = new Mail($this->config->get('config_mail_engine'));
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
			} else if(VERSION <= '2.0.1.1') {
		     	$mail = new Mail($this->config->get('config_mail'));
		    } else {
				$mail = new Mail();
				$mail->protocol = $this->config->get('config_mail_protocol');
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
			}

			$admin_email = $this->config->get('config_email');

			if ((utf8_strlen($this->config->get('ticketsetting_adminemail')) <= 96) && filter_var($this->config->get('ticketsetting_adminemail'), FILTER_VALIDATE_EMAIL)) {
				$admin_email = $this->config->get('ticketsetting_adminemail');
			}

			$mail->setTo($ticketrequest_info['email']);
			$mail->setReplyTo($admin_email);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject($subject);
			$mail->setHtml(html_entity_decode($html, ENT_QUOTES, 'UTF-8'));
			if(!empty($file_data)) {
				foreach ($file_data as $file_data_name) {
					$mail->addAttachment($file_data_name);
				}
			}

			$mail->send();
		}

		if(!empty($file_data)) {
			foreach ($file_data as $file_data_name) {
				unlink( $file_data_name );
			}
		}
	}
}