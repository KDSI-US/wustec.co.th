<?php
class ControllerExtensionModuleEmailTemplateCron extends Controller {
	public function index() {
		if (!defined("OPENCART_CLI_MODE") || OPENCART_CLI_MODE === FALSE) {
			exit;
		}

		//if (empty($this->request->get['token']) || $this->request->get['token'] != $this->config->get('emailtemplate_token')){
		if (empty($_SERVER['argv'][3]) || $_SERVER['argv'][3] != $this->config->get('emailtemplate_token')) {
			trigger_error('Cron token error: ' . print_r($_SERVER['argv'], true));
			exit;
		}

		$this->load->model('extension/module/emailtemplate');

		$logs = $this->model_extension_module_emailtemplate->getTemplateLogs(array(
			'emailtemplate_log_is_sent' => 0
		));

		if ($logs) {
			foreach ($logs as $i => $log) {
				$mail = new Mail($this->config->get('config_mail_engine'));
				$mail->protocol = !empty($log['emailtemplate_log_protocol']) ? $log['emailtemplate_log_protocol'] : $this->config->get('config_mail_protocol');
				$mail->parameter = !empty($log['emailtemplate_log_parameter']) ? $log['emailtemplate_log_parameter'] : $this->config->get('config_mail_parameter');

				$smtp = unserialize(base64_decode($log['emailtemplate_log_smtp']));
				$mail->smtp_hostname = isset($smtp['hostname']) ? $smtp['hostname'] : $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = isset($smtp['username']) ? $smtp['username'] : $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = isset($smtp['password']) ? $smtp['password'] : $this->config->get('config_mail_smtp_password');
				$mail->smtp_port = isset($smtp['port']) ? $smtp['port'] : $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = isset($smtp['timeout']) ? $smtp['timeout'] : $this->config->get('config_mail_smtp_timeout');

				$mail->setTo($log['emailtemplate_log_to']);
				$mail->setFrom($log['emailtemplate_log_from']);

				$file = DIR_CACHE . 'mail_queue/' . $log['emailtemplate_log_id'];

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

					$template = $this->model_extension_module_emailtemplate->load($template_load);

					if (!$template) {
						$template_load['emailtemplate_id'] = 1;
						$template = $this->model_extension_module_emailtemplate->load($template_load);
						if (!$template) continue;
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

				if ($log['emailtemplate_log_text']) {
					$mail->setText(html_entity_decode($log['emailtemplate_log_text'], ENT_QUOTES, 'UTF-8'));
				}

				if ($log['emailtemplate_log_cc']) {
					$mail->setCc($log['emailtemplate_log_cc']);
				}

				if ($log['emailtemplate_log_bcc']) {
					$mail->setBcc($log['emailtemplate_log_bcc']);
				}

				if ($log['emailtemplate_log_attachment']) {
					$log['emailtemplate_log_attachment'] = unserialize(base64_decode($log['emailtemplate_log_attachment']));
					foreach ($log['emailtemplate_log_attachment'] as $attachment) {
						if (file_exists($attachment)) {
							$mail->addAttachment($attachment);
						}
					}
				}

				$mail->setMailQueue(false);

				$mail->send();

				$this->db->query("UPDATE " . DB_PREFIX . "emailtemplate_logs SET emailtemplate_log_sent = NOW(), emailtemplate_log_is_sent = 1 WHERE emailtemplate_log_id = " . (int)$log['emailtemplate_log_id']);
			}

			$this->cache->get('emailtemplate_mail_queue', false);
		}
	}

}