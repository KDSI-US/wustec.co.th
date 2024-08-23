<?php
class ModelExtensionMpBlogMpBlogSubscriber extends Model {
	public function	editSubscriber($mpblogsubscribers_id, $data) {
		$send_approvalmail = false;
		if($data['sendmail']==1) {
			$subscriber_info = $this->getSubsciber($mpblogsubscribers_id);
			$send_approvalmail = ($subscriber_info && $subscriber_info['status']==0 && $data['status']==1);
		}

		$this->db->query("UPDATE " . DB_PREFIX . "mpblogsubscribers SET email='". $this->db->escape($data['email']) ."', status='". (int)$data['status'] ."', date_modified=NOW() WHERE mpblogsubscribers_id='". (int)$mpblogsubscribers_id ."'");
		// send approval email when new status is 1 and old status is 0
		if($send_approvalmail) {
			$this->sendApprovalMail($mpblogsubscribers_id);
		}
	}

	public function	addSubscriber($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "mpblogsubscribers SET email='". $this->db->escape($data['email']) ."', language_id='". (int)$data['language_id'] ."', store_id='". (int)$data['store_id'] ."', status='". (int)$data['status'] ."', customer_id='". (int)$data['customer_id'] ."', date_added=NOW()");

		$mpblogsubscribers_id = $this->db->getLastId();
		
		// send approval email when status is 1
		if($data['sendmail']==1 && $data['status']==1) {
			$this->sendApprovalMail($mpblogsubscribers_id);
		}
	}

	public function	deleteSubscriber($mpblogsubscribers_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpblogsubscribers WHERE mpblogsubscribers_id='". (int)$mpblogsubscribers_id ."'");
		
	}

	public function	subscribeEmailExists($email) {
		$query = $this->db->query("SELECT mpblogsubscribers_id FROM " . DB_PREFIX . "mpblogsubscribers mbs WHERE mbs.email LIKE '%". $this->db->escape($email) ."%'");
		return $query->row;
	}
	
	public function getSubsciber($mpblogsubscribers_id) {
		$query = $this->db->query("SELECT *, (SELECT CONCAT(c.firstname,' ',c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id=mbs.customer_id ) as customer FROM " . DB_PREFIX . "mpblogsubscribers mbs WHERE mbs.mpblogsubscribers_id='". (int)$mpblogsubscribers_id ."' ");
		return $query->row;
	}
	public function getSubscibers(array $data=array()) {
		$sql = "SELECT *, (SELECT CONCAT(c.firstname,' ',c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id=mbs.customer_id ) as customername FROM " . DB_PREFIX . "mpblogsubscribers mbs WHERE mbs.mpblogsubscribers_id>0 ";

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND mbs.status = '". $data['filter_status'] ."'";
		}

		if (isset($data['filter_store_id']) && !is_null($data['filter_store_id'])) {
			$sql .= " AND mbs.store_id = '". $data['filter_store_id'] ."'";
		}

		if (isset($data['filter_language_id']) && !is_null($data['filter_language_id'])) {
			$sql .= " AND mbs.language_id = '". $data['filter_language_id'] ."'";
		}

		if (!empty($data['filter_email'])) {
			$sql .= " AND mbs.email = '". $data['filter_email'] ."'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(mbs.date_added) = DATE('". $this->db->escape($data['filter_date_added']) ."')";
		}

		if (!empty($data['filter_date_modified'])) {
			$sql .= " AND DATE(mbs.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if (!empty($data['filter_customer'])) {
			$sql .= " AND mbs.customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer c1 WHERE CONCAT(c1.firstname,' ',c1.lastname) LIKE '". $this->db->escape($data['filter_customer']) ."%')";
		}

		$sql .= " GROUP BY mbs.mpblogsubscribers_id";
		$sort_data = array(
			'mbs.customer_id',
			'customername',
			'mbs.store_id',
			'mbs.language_id',
			'mbs.mpblogsubscribers_id',
			'mbs.status',
			'mbs.date_added',
			'mbs.date_modified'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			
			$sql .= " ORDER BY " . $data['sort'];
			
		} else {
			$sql .= " ORDER BY mbs.date_added";
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

	public function getTotalSubscibers($data = array()) {
		$sql = "SELECT COUNT(DISTINCT mbs.mpblogsubscribers_id) AS total FROM " . DB_PREFIX . "mpblogsubscribers mbs WHERE mbs.mpblogsubscribers_id>0";
		
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND mbs.status = '". $data['filter_status'] ."'";
		}

		if (isset($data['filter_store_id']) && !is_null($data['filter_store_id'])) {
			$sql .= " AND mbs.store_id = '". $data['filter_store_id'] ."'";
		}

		if (isset($data['filter_language_id']) && !is_null($data['filter_language_id'])) {
			$sql .= " AND mbs.language_id = '". $data['filter_language_id'] ."'";
		}

		if (!empty($data['filter_email'])) {
			$sql .= " AND mbs.email = '". $data['filter_email'] ."'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(mbs.date_added) = DATE('". $this->db->escape($data['filter_date_added']) ."')";
		}

		if (!empty($data['filter_date_modified'])) {
			$sql .= " AND DATE(mbs.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if (!empty($data['filter_customer'])) {
			$sql .= " AND mbs.customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer c1 WHERE CONCAT(c1.firstname,' ',c1.lastname) LIKE '". $this->db->escape($data['filter_customer']) ."%')";
		}

		$query = $this->db->query($sql);
		return $query->row['total'];
	}

	public function sendApprovalMail($mpblogsubscribers_id) {
		$this->load->model('setting/setting');
		$subscriber_info = $this->getSubsciber($mpblogsubscribers_id);

		if($subscriber_info) {

		$mpblog = $this->model_setting_setting->getSetting('mpblog', $subscriber_info['store_id']);
		if(!$mpblog) {
			$mpblog = $this->model_setting_setting->getSetting('mpblog', 0);
		}
		$config = $this->model_setting_setting->getSetting('config', $subscriber_info['store_id']);
		if(!$config) {
			$config = $this->model_setting_setting->getSetting('config', 0);
		}
		$subscribemail = $mpblog['mpblog_subscribemail'];

		if($mpblog['mpblog_subscribeapproval_status'] && $subscribemail) {
			$email = $subscriber_info['email'];

			$url_maker = new Url($config['config_url'], $config['config_ssl']);

			// send subscribe email here
			if (isset($this->request->server['HTTPS'])) {
				$server = $config['config_ssl'];
			} else {
				$server = $config['config_url'];
			}

			if (is_file(DIR_IMAGE . $config['config_logo'])) {
				$logo = $server . 'image/' . $config['config_logo'];
			} elseif (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
				$logo = $server . 'image/' . $this->config->get('config_logo');
			} else {
				$logo = '';
			}

			$email_subject = !empty($subscribemail['approval'][$subscriber_info['language_id']]['subject']) ? $subscribemail['approval'][$subscriber_info['language_id']]['subject'] : '';

			$email_message = !empty($subscribemail['approval'][$subscriber_info['language_id']]['message']) ? $subscribemail['approval'][$subscriber_info['language_id']]['message'] : '';

			$find = array(
				'[STORE_NAME]',
				'[STORE_LINK]',
				'[LOGO]',
				'[EMAIL]',
			);
		
			$replace = array(
				'STORE_NAME'					=> $config['config_name'],
				'STORE_LINK'					=> $url_maker->link('common/home', '', true),
				'LOGO'							=> '<a href="'. $url_maker->link('common/home', '', true) .'"><img src="'. $logo .'" alt="'. $config['config_name'] .'" title="'. $config['config_name'] .'" /></a>',
				'EMAIL'							=> $email,
				
			);

			if(!empty($email_subject)) {
				$email_subject = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $email_subject))));
			} else {
				$email_subject = '';
			}
			
			if(!empty($email_message)) {
				$email_message = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $email_message))));
			} else {
				$email_message = '';
			}

			$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($email);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject($email_subject);
			$mail->setHtml(html_entity_decode($email_message, ENT_QUOTES, 'UTF-8'));
			$mail->send();
		}
		}
	}
}

if(!function_exists('token')) {
	function token($length = 32) {
		// Create random token
		$string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		
		$max = strlen($string) - 1;
		
		$token = '';
		
		for ($i = 0; $i < $length; $i++) {
			$token .= $string[mt_rand(0, $max)];
		}	
		
		return $token;
	}
}