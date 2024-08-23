<?php
namespace mpblog;
// use in catalog only
trait trait_mpblog_catalog {


	private $extension_path = 'extension/';
	private $extension_prefix = ['module' => '', 'payment' => '', 'shipping' => '', 'total' =>  '', 'captcha' =>  ''];

	public function igniteTraitMpBlogCatalog($registry) {

		if (VERSION <= '2.2.0.0') {
			$this->extension_path = '';
		}

		if (VERSION >= '3.0.0.0') {

			$this->extension_prefix = [
				'module' => 'module_',
				'payment' => 'payment_',
				'shipping' => 'shipping_',
				'total' => 'total_',
				'captcha' => 'captcha_',
			];
		}
	}

	public function getMailObject() {
		if (VERSION >= '3.0.0.0') {
			$mail = new \Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
		} else if (VERSION >= '2.0.2.0') {
			$mail = new \Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
		} else {
			$mail = new \Mail($this->config->get('config_mail'));
		}

		return $mail;
	}
}

if (!function_exists('token')) {
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
