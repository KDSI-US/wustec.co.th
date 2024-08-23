<?php
/* This file is under Git Control by KDSI. */
class ControllerSupportRequestForm extends Controller {
	private $error = array();

	public function index() {
		$url = '';

		if(isset($this->request->get['ticketdepartment_id'])) {
			$url = '&ticketdepartment_id='. $this->request->get['ticketdepartment_id'];
		}

		$this->load->language('support/request_form');

		$this->load->model('module_ticket/support');

		$this->load->model('module_ticket/ticketrequest');

		$this->load->model('tool/upload');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$ticketrequest_id = $this->model_module_ticket_ticketrequest->addTicketRequest($this->request->post);

			$this->response->redirect($this->url->link('support/request_success'));
		}



		$ticketsetting_language = (array)$this->config->get('ticketsetting_submit_language');

		$config_language_id = (int)$this->config->get('config_language_id');

		$heading_title = isset($ticketsetting_language[$config_language_id]['title']) ? $ticketsetting_language[$config_language_id]['title'] : '';
		$banner_title = isset($ticketsetting_language[$config_language_id]['banner_title']) ? $ticketsetting_language[$config_language_id]['banner_title'] : '';
		$meta_title = isset($ticketsetting_language[$config_language_id]['meta_title']) ? $ticketsetting_language[$config_language_id]['meta_title'] : '';
		$meta_description = isset($ticketsetting_language[$config_language_id]['meta_description']) ? $ticketsetting_language[$config_language_id]['meta_description'] : '';
		$meta_keyword = isset($ticketsetting_language[$config_language_id]['meta_keyword']) ? $ticketsetting_language[$config_language_id]['meta_keyword'] : '';

		$this->document->setTitle($meta_title);
		$this->document->setDescription($meta_description);
		$this->document->setKeywords($meta_keyword);

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_support'),
			'href' => $this->url->link('support/support')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get($heading_title),
			'href' => $this->url->link('support/request_form', ''. $url, true)
		);

		$this->document->addScript('catalog/view/javascript/modulepoints/ticketsystem.js');
		$this->document->addStyle('catalog/view/theme/default/stylesheet/modulepoints/ticketsystem.css');

		if($this->config->get('ticketsetting_headerfooter')) {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/modulepoints/moduleheader.css');
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['subject'])) {
			$data['error_subject'] = $this->error['subject'];
		} else {
			$data['error_subject'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['message'])) {
			$data['error_message'] = $this->error['message'];
		} else {
			$data['error_message'] = '';
		}

		if (isset($this->error['ticketdepartment'])) {
			$data['error_ticketdepartment'] = $this->error['ticketdepartment'];
		} else {
			$data['error_ticketdepartment'] = '';
		}

		if (isset($this->error['captcha'])) {
			$data['error_captcha'] = $this->error['captcha'];
		} else {
			$data['error_captcha'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['subject'])) {
			$data['subject'] = $this->request->post['subject'];
		} else {
			$data['subject'] = '';
		}

		if (isset($this->request->post['message'])) {
			$data['message'] = $this->request->post['message'];
		} else {
			$data['message'] = '';
		}

		if (isset($this->request->post['attachments'])) {
			$attachments = $this->request->post['attachments'];
		} else {
			$attachments = array();
		}

		$data['attachments'] = array();
		foreach ($attachments as $attachment) {
			$upload_info = $this->model_tool_upload->getUploadByCode($attachment);

			if ($upload_info) {
				$data['attachments'][] = array(
					'name'		=> $upload_info['name'],
					'code'			=> $attachment,
				);
			}
		}

		if (isset($this->request->post['ticketdepartment_id'])) {
			$data['ticketdepartment_id'] = $this->request->post['ticketdepartment_id'];
		} else if(isset($this->request->get['ticketdepartment_id'])) {
			$data['ticketdepartment_id'] = $this->request->get['ticketdepartment_id'];
		} else {
			$data['ticketdepartment_id'] = '';
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('ticketsetting_banner'))) {
			$data['support_banner'] = $server . 'image/' . $this->config->get('ticketsetting_banner');
		} else {
			$data['support_banner'] = '';
		}

		$data['heading_title'] = $heading_title;
		$data['banner_title'] = $banner_title;

		$data['ticketdepartments'] = $this->model_module_ticket_support->getTicketdepartments();

		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_choose'] = $this->language->get('entry_choose');
		$data['entry_subject'] = $this->language->get('entry_subject');
		$data['entry_message'] = $this->language->get('entry_message');
		$data['entry_attachments'] = $this->language->get('entry_attachments');

		$data['text_select'] = $this->language->get('text_select');

		$data['button_submit'] = $this->language->get('button_submit');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_request_list'] = $this->language->get('button_request_list');
		$data['text_loading'] = $this->language->get('text_loading');

		$data['button_upload'] = $this->language->get('button_upload');
		$data['button_add_file'] = $this->language->get('button_add_file');

		$data['request_list'] = $this->url->link('support/request_list', '', true);
		$data['cancel'] = $this->url->link('support/support', '', true);

		$data['action'] = $this->url->link('support/request_form', ''. $url, true);

		// Captcha
		if ($this->config->get($this->config->get('config_captcha') . '_status') && $this->config->get('ticketsetting_captcha')) {
			$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'), $this->error);
		} else {
			$data['captcha'] = '';
		}

		$data['logged'] = $this->ticketuser->isLogged();

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');

		if($this->config->get('ticketsetting_headerfooter')) {
			$data['header'] = $this->load->controller('support/header');
			$data['footer'] = $this->load->controller('support/footer');
		} else {
			$data['header'] = $this->load->controller('common/header');
			$data['footer'] = $this->load->controller('common/footer');
		}

		$mytheme = null;
		if($this->config->get('config_theme')) {
			$mytheme = $this->config->get('config_theme');
		} else if($this->config->get('theme_default_directory')) {
			$mytheme = $this->config->get('theme_default_directory');
		} else if($this->config->get('config_template')) {
			$mytheme = $this->config->get('config_template');
		} else{
			$mytheme = 'default';
		}

		if($mytheme == '') {
			$mytheme = 'default';
		}


		if(VERSION < '2.2.0.0') {
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/support/request_form.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/support/request_form.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/support/request_form.tpl', $data));
			}
		} else {
			if($mytheme == 'journal3' && VERSION >= '3.0.0.0') {
				if($this->config->get('template_directory') == '') {
					$this->config->set('template_directory', 'journal3/template/');
				}

				$this->response->setOutput($this->load->view('support/request_form', $data));

				$this->config->set('template_engine', 'twig');
			} else {
				$this->response->setOutput($this->load->view('support/request_form', $data));
			}
		}
	}

	private function validate() {
		if(isset($this->request->post['ticketdepartment_id'])) {
			$ticketdepartment_id = $this->request->post['ticketdepartment_id'];
		} else {
			$ticketdepartment_id = 0;
		}

		if(!$this->ticketuser->isLogged()) {
			if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
				$this->error['email'] = $this->language->get('error_email');
			}
		}

		if ((utf8_strlen(trim($this->request->post['subject'])) < 3) || (utf8_strlen(trim($this->request->post['subject'])) > 255)) {
			$this->error['subject'] = $this->language->get('error_subject');
		}

		if (utf8_strlen($this->request->post['message']) < 10) {
			$this->error['message'] = $this->language->get('error_message');
		}

		$ticketdepartment_info = $this->model_module_ticket_support->getTicketdepartment($ticketdepartment_id);
		if(!$ticketdepartment_info) {
			$this->error['ticketdepartment'] = $this->language->get('error_ticketdepartment');
		}

		// Captcha
		if ($this->config->get($this->config->get('config_captcha') . '_status') && $this->config->get('ticketsetting_captcha')) {
			$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

			if ($captcha) {
				$this->error['captcha'] = $captcha;
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	public function fileupload() {
		$this->load->language('tool/upload');

		$json = array();

		if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
			// Sanitize the filename
			$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8')));

			// Validate the filename length
			if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 64)) {
				$json['error'] = $this->language->get('error_filename');
			}

			// Allowed file extension types
			$allowed = array();

			$extension_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_ext_allowed'));

			$filetypes = explode("\n", $extension_allowed);

			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}

			if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Allowed file mime types
			$allowed = array();

			$mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_mime_allowed'));

			$filetypes = explode("\n", $mime_allowed);

			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}

			if (!in_array($this->request->files['file']['type'], $allowed)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Check to see if any PHP files are trying to be uploaded
			$content = file_get_contents($this->request->files['file']['tmp_name']);

			if (preg_match('/\<\?php/i', $content)) {
/* This file is under Git Control by KDSI. */
				$json['error'] = $this->language->get('error_filetype');
			}

			// Return any upload error
			if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
				$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
			}
		} else {
			$json['error'] = $this->language->get('error_upload');
		}

		if (!$json) {
			$file = $filename . '.' . token(32);

			move_uploaded_file($this->request->files['file']['tmp_name'], DIR_UPLOAD . $file);

			// Hide the uploaded file name so people can not link to it directly.
			$this->load->model('tool/upload');

			$json['code'] = $this->model_tool_upload->addUpload($filename, $file);

			$json['filename'] = $filename;

			$json['success'] = $this->language->get('text_upload');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
