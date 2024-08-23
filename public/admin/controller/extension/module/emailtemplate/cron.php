<?php
class ControllerExtensionModuleEmailTemplateCron extends Controller {
    public function index() {
        $this->load->model('extension/module/emailtemplate');
        $this->load->model('setting/setting');
        $this->load->model('setting/store');

        $this->language->load('extension/module/emailtemplate');

		$return = array();

		$this->model_extension_module_emailtemplate->cleanLogs();

        $logs = $this->model_extension_module_emailtemplate->getTemplateLogs(array(
            'emailtemplate_log_is_sent' => false,
			'sort' => 'added',
			'limit' => 10
        ));

		$total_sent = isset($this->request->get['sent']) ? (int)$this->request->get['sent'] : 0;
		$total_sent += count($logs);

		if (isset($this->request->get['total'])) {
			$total_logs = (int)$this->request->get['total'];
		} else {
			$total_logs = $this->model_extension_module_emailtemplate->getTotalTemplateLogs(array(
				'emailtemplate_log_is_sent' => false
			));
		}

        if ($logs) {
			foreach ($logs as $i => $log) {
				if (!empty($log['store_id'])) {
					$store_id = $log['store_id'];
				} else {
					$store_id = $this->config->get('config_store_id');
				}

				$store_info = array_merge(
					$this->model_setting_setting->getSetting("config", $store_id),
					$this->model_setting_store->getStore($store_id)
				);

				$mail = new Mail(isset($store_info['config_mail_engine']) ? $store_info['config_mail_engine'] : $this->config->get('config_mail_engine'));
				$mail->parameter = isset($store_info['config_mail_parameter']) ? $store_info['config_mail_parameter'] : $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = isset($store_info['config_mail_smtp_hostname']) ? $store_info['config_mail_smtp_hostname'] : $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = isset($store_info['config_mail_smtp_username']) ? $store_info['config_mail_smtp_username'] : $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode((isset($store_info['config_mail_smtp_password']) ? $store_info['config_mail_smtp_password'] : $this->config->get('config_mail_smtp_password')), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = isset($store_info['config_mail_smtp_port']) ? $store_info['config_mail_smtp_port'] : $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = isset($store_info['config_mail_smtp_timeout']) ? $store_info['config_mail_smtp_timeout'] : $this->config->get('config_mail_smtp_timeout');

				$mail->setTo($log['emailtemplate_log_to']);
				$mail->setFrom($log['emailtemplate_log_from']);

				$file = DIR_CACHE . 'mail_queue/' . $log['emailtemplate_log_enc'];

				if (file_exists($file)) {
					$mail->setHtml(file_get_contents($file));
					unlink($file);
				} else {
					// Load template if html not found
					$template_load = array(
						'emailtemplate_id' => $log['emailtemplate_id'],
						'store_id' => $log['store_id'],
						'language_id' => $log['language_id']
					);

					if (!empty($log['customer_id'])) {
						$template_load['customer_id'] = $log['customer_id'];
					}

					if (!empty($log['customer_group_id'])) {
						$template_load['customer_group_id'] = $log['customer_group_id'];
					}

					$template = $this->model_extension_module_emailtemplate->load($template_load);

					if (!$template) {
						unset($template_load['emailtemplate_id']);
						$template_load['emailtemplate_key'] = $log['emailtemplate_key'];

						$template = $this->model_extension_module_emailtemplate->load($template_load);

						if (!$template) {
							continue;
						}
					}

					$template->build();

					$template->fetch(null, $log['emailtemplate_log_content']);

					$mail->setHtml($template->getHtml());
				}

				if ($log['emailtemplate_log_sender']) {
					$mail->setSender(html_entity_decode($log['emailtemplate_log_sender'], ENT_QUOTES, 'UTF-8'));
				}

				if ($log['emailtemplate_log_subject']) {
					$mail->setSubject(html_entity_decode($log['emailtemplate_log_subject'], ENT_QUOTES, 'UTF-8'));
				}

				if ($log['emailtemplate_log_content']) {
					$mail->setText(strip_tags(html_entity_decode($log['emailtemplate_log_content'], ENT_QUOTES, 'UTF-8')));
				}

				if ($log['emailtemplate_log_cc']) {
					$mail->setCc($log['emailtemplate_log_cc']);
				}

				if ($log['emailtemplate_log_reply_to']) {
					$mail->setReplyTo($log['emailtemplate_log_reply_to'], html_entity_decode($log['emailtemplate_log_sender'], ENT_QUOTES, 'UTF-8'));
				}

				if (method_exists($mail, 'setMailQueue')) {
					$mail->setMailQueue(false);
				}

				$mail->send();

				$this->db->query("UPDATE " . DB_PREFIX . "emailtemplate_logs SET emailtemplate_log_sent = NOW(), emailtemplate_log_is_sent = 1 WHERE emailtemplate_log_id = " . (int)$log['emailtemplate_log_id']);
			}
		}

		if (isset($this->request->get['user_token'])) {
			$return['success'] = sprintf($this->language->get('success_send_part'), $total_sent, $total_logs);

			if ($total_sent < $total_logs) {
				$return['next'] = str_replace('&amp;', '&', $this->url->link('extension/module/emailtemplate/cron', 'user_token=' . $this->session->data['user_token'] . '&sent=' . $total_sent . '&total=' . $total_logs, true));
			}

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($return));
		}
    }

}