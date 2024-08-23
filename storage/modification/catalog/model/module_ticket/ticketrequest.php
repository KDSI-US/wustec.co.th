<?php
/* This file is under Git Control by KDSI. */
class ModelModuleTicketTicketRequest extends Model {
	public function addTicketRequest($data) {
		$this->load->model('module_ticket/ticketuser');

		if(!empty($data['attachments'])) {
			$attachments_json_encode = json_encode($data['attachments']);
		} else {
			$attachments_json_encode = '';
		}

		if($this->ticketuser->isLogged()) {
			$email = $this->ticketuser->getEmail();
			$ticketuser_id = $this->ticketuser->getId();
			$name = $this->ticketuser->getName();
		} else {
			$email = $data['email'];
			$userinfo = $this->model_module_ticket_ticketuser->getTicketuserByEmail($email);
			if($userinfo) {
				$ticketuser_id = $userinfo['ticketuser_id'];
				$name = $userinfo['name'];

			} else {
				$ticketuser_id = 0;
				$name = '';
			}
		}

		$this->db->query("INSERT INTO " . DB_PREFIX . "ticketrequest SET ticketuser_id = '". (int)$ticketuser_id ."', ticketdepartment_id = '". (int)$data['ticketdepartment_id'] ."', email = '". $this->db->escape($email) ."', subject = '". $this->db->escape($data['subject']) ."', message = '". $this->db->escape($data['message']) ."', attachments = '". $this->db->escape($attachments_json_encode) ."', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', ticketstatus_id = '". (int)$this->config->get('ticketsetting_ticketstatus_id') ."', date_added = NOW(), date_modified = NOW()");

		$ticketrequest_id = $this->db->getLastId();

		// Send Email To User when create a ticket
		$this->load->language('support/mail_ticketrequest');
		$this->load->model('tool/upload');
		$ticketsetting_email = (array)$this->config->get('ticketsetting_email');

		$adminurl = new Url(HTTP_SERVER . 'admin/', HTTPS_SERVER . 'admin/');

		if(!empty($ticketsetting_email['createtickettouser']['status'])) {
			$subject = sprintf($this->language->get('text_subject'), $data['subject']);

			$link = str_replace('&amp;', '&', $this->url->link('support/ticket_view', 'ticketrequest_id=' . $ticketrequest_id, true));

			$message = sprintf($this->language->get('text_welcome'), $ticketrequest_id) . "<br/><br/>";

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

			$mail->setTo($email);
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

		// Send Email To Admin when create a ticket
		if(!empty($ticketsetting_email['createtickettoadmin']['status'])) {
			$subject = sprintf($this->language->get('text_admin_subject'), $data['subject']);

			$message = $this->language->get('text_admin_welcome') . "<br/><br/>";

			$message .= $this->language->get('text_equiry_details') . "<br/><br/>";
			$message .= sprintf($this->language->get('mail_subject'), $data['subject']) . "<br/>";
			if($name) {
				$message .= sprintf($this->language->get('mail_name'), $name) . "<br/>";
			}
			$message .= sprintf($this->language->get('mail_email'), $email) . "<br/>";
			$message .= sprintf($this->language->get('mail_ticketrequest_id'), $ticketrequest_id) . "<br/>";

			$ticketdepartment_info = $this->model_module_ticket_support->getTicketdepartment($data['ticketdepartment_id']);
			if($ticketdepartment_info) {
				$department_name = $ticketdepartment_info['title'];
			} else {
				$department_name = '';
			}

			$message .= sprintf($this->language->get('mail_department'), $department_name) . "<br/>";
			$message .= sprintf($this->language->get('mail_ip'), $this->request->server['REMOTE_ADDR']) . "<br/><br/>";

			$message .= $this->language->get('mail_equiry') . "<br/><br/>";

			$message .= $this->language->get('mail_ticketrequest_url') . "<br/><br/>";

			$link = str_replace('&amp;', '&', $adminurl->link('module_ticket/ticketrequest/info', 'ticketrequest_id=' . $ticketrequest_id, true));

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

			$mail->setTo($admin_email);
			$mail->setReplyTo($email);
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

		return $ticketrequest_id;
	}

	public function addTicketRequestChat($ticketrequest_id, $data) {
		if(!empty($data['attachments'])) {
			$attachments_json_encode = json_encode($data['attachments']);
		} else {
			$attachments_json_encode = '';
		}


		$this->db->query("INSERT INTO " . DB_PREFIX . "ticketrequest_chat SET ticketrequest_id = '". (int)$ticketrequest_id ."', ticketuser_id = '". (int)$data['ticketuser_id'] ."', message_from_user_id = '". (int)$data['message_from_user_id'] ."', message = '". $this->db->escape($data['message']) ."', client_type = '". $this->db->escape($data['client_type']) ."', attachments = '". $this->db->escape($attachments_json_encode) ."', date_added = NOW()");

		$this->db->query("UPDATE " . DB_PREFIX . "ticketrequest SET date_modified = NOW() WHERE ticketrequest_id = '". (int)$ticketrequest_id ."'");

		if($data['type'] == 'close') {
			// Ticket Status Will be Close.
			$this->db->query("UPDATE " . DB_PREFIX . "ticketrequest SET ticketstatus_id = '". (int)$this->config->get('ticketsetting_ticketstatus_closed_id') ."' WHERE ticketrequest_id = '". (int)$ticketrequest_id ."'");
		}

		// Send Email To User when user send a reply
		$this->load->language('support/mail_ticketreply');
		$this->load->model('tool/upload');
		$ticketsetting_email = (array)$this->config->get('ticketsetting_email');

		$adminurl = new Url(HTTP_SERVER . 'admin/', HTTPS_SERVER . 'admin/');

		$ticketrequest_info = $this->getTicketRequest($ticketrequest_id);

		// Send Email To Admin when user send a reply
		if(!empty($ticketsetting_email['userreplytoadmin']['status']) && $ticketrequest_info) {
			$subject = sprintf($this->language->get('text_admin_subject'), $ticketrequest_info['subject']);

			$message = sprintf($this->language->get('text_admin_welcome'), $this->ticketuser->getName()) . "<br/><br/>";

			$message .= $this->language->get('text_equiry_details') . "<br/><br/>";
			$message .= sprintf($this->language->get('mail_subject'), $ticketrequest_info['subject']) . "<br/>";
			$message .= sprintf($this->language->get('mail_name'), $this->ticketuser->getName()) . "<br/>";
			$message .= sprintf($this->language->get('mail_email'), $this->ticketuser->getEmail()) . "<br/>";
			$message .= sprintf($this->language->get('mail_ticketrequest_id'), $ticketrequest_id) . "<br/>";

			$message .= $this->language->get('mail_message') . "<br/><br/>";

			$message .= $this->language->get('mail_ticketrequest_url') . "<br/><br/>";

			$link = str_replace('&amp;', '&', $adminurl->link('module_ticket/ticketrequest/info', 'ticketrequest_id=' . $ticketrequest_id, true));

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

			$mail->setTo($admin_email);
			$mail->setReplyTo($this->ticketuser->getEmail());
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

	public function getTicketRequests($start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 1;
		}

		$query = $this->db->query("SELECT t.ticketrequest_id, t.ticketdepartment_id, t.subject, ts.bgcolor, ts.textcolor, tsd.name as status, t.date_added, t.date_modified FROM `" . DB_PREFIX . "ticketrequest` t LEFT JOIN " . DB_PREFIX . "ticketstatus ts ON (t.ticketstatus_id = ts.ticketstatus_id) LEFT JOIN " . DB_PREFIX . "ticketstatus_description tsd ON (ts.ticketstatus_id = tsd.ticketstatus_id) WHERE t.ticketuser_id = '" . (int)$this->ticketuser->getId() . "' AND t.ticketstatus_id > '0' AND tsd.language_id = '". (int)$this->config->get('config_language_id') ."' ORDER BY t.date_modified DESC LIMIT " . (int)$start . "," . (int)$limit);
		return $query->rows;
	}

	public function getTicketRequest($ticketrequest_id) {
		$query = $this->db->query("SELECT t.ticketrequest_id, t.ticketdepartment_id, t.email, t.subject, t.message, t.attachments, t.ticketstatus_id, t.ip, ts.bgcolor, ts.textcolor, tsd.name as status, t.date_added, t.date_modified FROM `" . DB_PREFIX . "ticketrequest` t LEFT JOIN " . DB_PREFIX . "ticketstatus ts ON (t.ticketstatus_id = ts.ticketstatus_id) LEFT JOIN " . DB_PREFIX . "ticketstatus_description tsd ON (ts.ticketstatus_id = tsd.ticketstatus_id) WHERE t.ticketuser_id = '" . (int)$this->ticketuser->getId() . "' AND t.ticketstatus_id > '0' AND t.ticketrequest_id = '". (int)$ticketrequest_id ."' AND tsd.language_id = '". (int)$this->config->get('config_language_id') ."'");

		if ($query->num_rows) {
			$this->load->model('module_ticket/support');
			$ticketdepartment_info = $this->model_module_ticket_support->getTicketdepartment($query->row['ticketdepartment_id']);
			if($ticketdepartment_info) {
				$department_name = $ticketdepartment_info['title'];
			} else {
				$department_name = '';
			}

			if($this->ticketuser->isLogged()) {
				$ticketuser_name = $this->ticketuser->getName();
				$ticketuser_email = $this->ticketuser->getEmail();
			} else {
				$ticketuser_name = '';
				$ticketuser_email = '';
			}

			return array(
				'ticketrequest_id'				=> $query->row['ticketrequest_id'],
				'ticketdepartment_id'			=> $query->row['ticketdepartment_id'],
				'department_name'				=> $department_name,
				'ticketuser_name'				=> $ticketuser_name,
				'ticketuser_email'				=> $ticketuser_email,
				'email'							=> $query->row['email'],
				'subject'						=> $query->row['subject'],
				'message'						=> $query->row['message'],
				'attachments'					=> ($query->row['attachments']) ? json_decode($query->row['attachments']) : array(),
				'status'						=> $query->row['status'],
				'bgcolor'						=> $query->row['bgcolor'],
				'textcolor'						=> $query->row['textcolor'],
				'ip'							=> $query->row['ip'],
				'ticketstatus_id'				=> $query->row['ticketstatus_id'],
				'date_added'					=> $query->row['date_added'],
				'date_modified'					=> $query->row['date_modified'],
			);
		} else {
			return false;
		}
	}

	public function getTotalTicketRequests() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "ticketrequest` t WHERE t.ticketuser_id = '" . (int)$this->ticketuser->getId() . "' AND t.ticketstatus_id > '0'");

		return $query->row['total'];
	}

	public function getTicketRequestChats($ticketrequest_id, $start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 1;
		}

		$query = $this->db->query("SELECT tc.ticketrequest_chat_id, tc.ticketrequest_id, tc.ticketuser_id, tc.message_from_user_id, tc.client_type, tc.message, tc.attachments, tc.date_added FROM `" . DB_PREFIX . "ticketrequest_chat` tc LEFT JOIN `" . DB_PREFIX . "ticketrequest` t ON(tc.ticketrequest_id = t.ticketrequest_id) WHERE t.ticketuser_id = '" . (int)$this->ticketuser->getId() . "' AND tc.ticketrequest_id = '" . (int)$ticketrequest_id . "' ORDER BY tc.ticketrequest_chat_id ASC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalTicketRequestChats($ticketrequest_id) {
		$query = $this->db->query("SELECT COUNT(*) as total FROM `" . DB_PREFIX . "ticketrequest_chat` tc LEFT JOIN `" . DB_PREFIX . "ticketrequest` t ON(tc.ticketrequest_id = t.ticketrequest_id) WHERE t.ticketuser_id = '" . (int)$this->ticketuser->getId() . "' AND tc.ticketrequest_id = '" . (int)$ticketrequest_id . "'");

		return $query->row['total'];
	}
}