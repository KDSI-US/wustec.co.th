<?php
/* This file is under Git Control by KDSI. */
use \MpBlog\ControllerCatalog as Controller;
class ControllerExtensionMpBlogSubscriberVerification extends Controller {
	use \MpBlog\trait_mpblog_catalog;

	public function __construct($registry) {
		parent :: __construct($registry);
		$this->igniteTraitMpBlogCatalog($registry);
	}

	public function index() {
		if ($this->config->get('mpblog_status')) {
			$this->load->language('mpblog/subscriber_verification');
			$this->load->model('extension/mpblog/mpblogpost');


			$data['heading_title'] = $this->language->get('heading_title');
			$data['text_message'] = $this->language->get('text_url_expire');

			if (isset($this->request->server['HTTPS'])) {
				$server = $this->config->get('config_ssl');
			} else {
				$server = $this->config->get('config_url');
			}

			if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
				$logo = $server . 'image/' . $this->config->get('config_logo');
			} else {
				$logo = '';
			}

			$code = '';
			$email = '';
			if (isset($this->request->get['v'])) {
				$code = $this->request->get['v'];
			}

			$subscribemail = (array)$this->config->get('mpblog_subscribemail');

			$page_title = !empty($subscribemail['invalid'][$this->config->get('config_language_id')]['title']) ? $subscribemail['invalid'][$this->config->get('config_language_id')]['title'] : '';

			$page_message = !empty($subscribemail['invalid'][$this->config->get('config_language_id')]['content']) ? $subscribemail['invalid'][$this->config->get('config_language_id')]['content'] : '';

			$find = [
				'[STORE_NAME]',
				'[STORE_LINK]',
				'[LOGO]',
				'[EMAIL]',
				'[CODE]',
			];
		
			$replace = [
				'STORE_NAME'					=> $this->config->get('config_name'),
				'STORE_LINK'					=> $this->url->link('common/home', '', true),
				'LOGO'							=> '<a href="'. $this->url->link('common/home', '', true) .'"><img src="'. $logo .'" alt="'. $this->config->get('config_name') .'" title="'. $this->config->get('config_name') .'" /></a>',
				'EMAIL'							=> $email,
				'CODE'							=> $code,
			];
			
			if (!empty($page_title)) {
				$page_title = str_replace(["\r\n", "\r", "\n"], '', preg_replace(["/\s\s+/", "/\r\r+/", "/\n\n+/"], '', trim(str_replace($find, $replace, $page_title))));

				$data['heading_title'] = $page_title;
			}
			
			if (!empty($page_message)) {
				$page_message = str_replace(["\r\n", "\r", "\n"], '', preg_replace(["/\s\s+/", "/\r\r+/", "/\n\n+/"], '', trim(str_replace($find, $replace, $page_message))));

				$data['text_message'] = html_entity_decode($page_message, ENT_QUOTES, 'UTF-8');
			}

			$this->document->setTitle($data['heading_title']);

			$this->document->addStyle('catalog/view/theme/default/stylesheet/mpblog/mpblog.css');

			$data['breadcrumbs'] = [];

			$data['breadcrumbs'][] = [
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			];

			$data['breadcrumbs'][] = [
				'text' => $this->language->get('text_blog'),
				'href' => $this->url->link('extension/mpblog/blogs', '', true)
			];

			
			if (!empty($this->request->get['v'])) {

				$result = $this->model_extension_mpblog_mpblogpost->verifySubscirbeCode($this->request->get['v']);
				if ($result->num_rows) {
					// update the status of subscriber and update expire verification link

					if ($result->row['action'] == 'SUBSCRIBE') {

						$this->model_extension_mpblog_mpblogpost->updateSubscirberStatus($result->row['mpblogsubscribers_id'], 1);
						$this->model_extension_mpblog_mpblogpost->expireVerification($result->row['code']);

						$subscriber_info = $this->model_extension_mpblog_mpblogpost->getSubscriberById($result->row['mpblogsubscribers_id']);
						// send approval email.
						if ($this->config->get('mpblog_subscribeapproval_status') && $subscriber_info) {
							$email = $subscriber_info['email'];

							$email_subject = !empty($subscribemail['approval'][$this->config->get('config_language_id')]['subject']) ? $subscribemail['approval'][$this->config->get('config_language_id')]['subject'] : '';

							$email_message = !empty($subscribemail['approval'][$this->config->get('config_language_id')]['message']) ? $subscribemail['approval'][$this->config->get('config_language_id')]['message'] : '';

							$find = [
								'[STORE_NAME]',
								'[STORE_LINK]',
								'[LOGO]',
								'[EMAIL]',
							];
						
							$replace = [
								'STORE_NAME'					=> $this->config->get('config_name'),
								'STORE_LINK'					=> $this->url->link('common/home', '', true),
								'LOGO'							=> '<a href="'. $this->url->link('common/home', '', true) .'"><img src="'. $logo .'" alt="'. $this->config->get('config_name') .'" title="'. $this->config->get('config_name') .'" /></a>',
								'EMAIL'							=> $email,
							];
							
							if (!empty($email_subject)) {
								$email_subject = str_replace(["\r\n", "\r", "\n"], '', preg_replace(["/\s\s+/", "/\r\r+/", "/\n\n+/"], '', trim(str_replace($find, $replace, $email_subject))));
							} else {
								$email_subject = '';
							}
							
							if (!empty($email_message)) {
								$email_message = str_replace(["\r\n", "\r", "\n"], '', preg_replace(["/\s\s+/", "/\r\r+/", "/\n\n+/"], '', trim(str_replace($find, $replace, $email_message))));
							} else {
								$email_message = '';
							}

							$mail = $this->getMailObject();

							$mail->setTo($email);
							$mail->setFrom($this->config->get('config_email'));
							$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
							$mail->setSubject($email_subject);
							$mail->setHtml(html_entity_decode($email_message, ENT_QUOTES, 'UTF-8'));
							$mail->send();
						}

						$page_title = !empty($subscribemail['confirm'][$this->config->get('config_language_id')]['title']) ? $subscribemail['confirm'][$this->config->get('config_language_id')]['title'] : '';

						$page_message = !empty($subscribemail['confirm'][$this->config->get('config_language_id')]['content']) ? $subscribemail['confirm'][$this->config->get('config_language_id')]['content'] : '';

						$find = [
							'[STORE_NAME]',
							'[STORE_LINK]',
							'[LOGO]',
							'[EMAIL]',
							'[CODE]',
						];
					
						$replace = [
							'STORE_NAME'					=> $this->config->get('config_name'),
							'STORE_LINK'					=> $this->url->link('common/home', '', true),
							'LOGO'							=> '<a href="'. $this->url->link('common/home', '', true) .'"><img src="'. $logo .'" alt="'. $this->config->get('config_name') .'" title="'. $this->config->get('config_name') .'" /></a>',
							'EMAIL'							=> $email,
							'CODE'							=> $code,
						];
						
						if (!empty($page_title)) {
							$page_title = str_replace(["\r\n", "\r", "\n"], '', preg_replace(["/\s\s+/", "/\r\r+/", "/\n\n+/"], '', trim(str_replace($find, $replace, $page_title))));
							$data['heading_title'] = $page_title;
						}
						
						if (!empty($page_message)) {
							$page_message = str_replace(["\r\n", "\r", "\n"], '', preg_replace(["/\s\s+/", "/\r\r+/", "/\n\n+/"], '', trim(str_replace($find, $replace, $page_message))));
							$data['text_message'] = html_entity_decode($page_message, ENT_QUOTES, 'UTF-8');
						}
					}
					
					if ($result->row['action'] == 'UNSUBSCRIBE') {
						$this->model_extension_mpblog_mpblogpost->updateSubscirberStatus($result->row['mpblogsubscribers_id'], 0);
						$this->model_extension_mpblog_mpblogpost->expireVerification($result->row['code']);

						$page_title = !empty($subscribemail['unsubscribe'][$this->config->get('config_language_id')]['title']) ? $subscribemail['unsubscribe'][$this->config->get('config_language_id')]['title'] : '';

						$page_message = !empty($subscribemail['unsubscribe'][$this->config->get('config_language_id')]['content']) ? $subscribemail['unsubscribe'][$this->config->get('config_language_id')]['content'] : '';

						$find = [
							'[STORE_NAME]',
							'[STORE_LINK]',
							'[LOGO]',
							'[EMAIL]',
							'[CODE]',
						];
					
						$replace = [
							'STORE_NAME'					=> $this->config->get('config_name'),
							'STORE_LINK'					=> $this->url->link('common/home', '', true),
							'LOGO'							=> '<a href="'. $this->url->link('common/home', '', true) .'"><img src="'. $logo .'" alt="'. $this->config->get('config_name') .'" title="'. $this->config->get('config_name') .'" /></a>',
							'EMAIL'							=> $email,
							'CODE'							=> $code,
						];
						
						if (!empty($page_title)) {
							$page_title = str_replace(["\r\n", "\r", "\n"], '', preg_replace(["/\s\s+/", "/\r\r+/", "/\n\n+/"], '', trim(str_replace($find, $replace, $page_title))));
							$data['heading_title'] = $page_title;
						}
						
						if (!empty($page_message)) {
							$page_message = str_replace(["\r\n", "\r", "\n"], '', preg_replace(["/\s\s+/", "/\r\r+/", "/\n\n+/"], '', trim(str_replace($find, $replace, $page_message))));
							$data['text_message'] = html_entity_decode($page_message, ENT_QUOTES, 'UTF-8');
						}

					}
					
				}
			}

			$data['continue'] = $this->url->link('extension/mpblog/blogs', '', true);
			$data['button_continue'] = $this->language->get('button_continue');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('mpblog/subscriber_verification', $data));
		}
	}
}