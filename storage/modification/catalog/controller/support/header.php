<?php
/* This file is under Git Control by KDSI. */
class ControllerSupportHeader extends Controller {
	public function index() {
		require_once(DIR_SYSTEM.'library/modulepoints/ticketuser.php');
		if(VERSION <= '2.2.0.0') {
			global $registry;
			$ticketuser = new Modulepoints\Ticketuser($registry);
			$this->registry->set('ticketuser', $ticketuser);
		} else {
			$ticketuser = new Modulepoints\Ticketuser($this->registry);
			$this->registry->set('ticketuser', $ticketuser);
		}

		$this->load->language('support/header');

		$this->load->model('tool/image');

		if(VERSION <= '2.3.0.2') {
			$this->load->model('extension/extension');
			$data['analytics'] = array();
			$analytics = $this->model_extension_extension->getExtensions('analytics');

			foreach ($analytics as $analytic) {
				if ($this->config->get($analytic['code'] . '_status')) {
					if(VERSION > '2.2.0.0') {
						$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get($analytic['code'] . '_status'));
					} else if (VERSION == '2.2.0.0') {
						$data['analytics'][] = $this->load->controller('analytics/' . $analytic['code'], $this->config->get($analytic['code'] . '_status'));
					} else {
						$data['analytics'][] = $this->load->controller('analytics/' . $analytic['code']);
					}
				}
			}
		} else {
			$this->load->model('setting/extension');
			$data['analytics'] = array();
			$analytics = $this->model_setting_extension->getExtensions('analytics');
			foreach ($analytics as $analytic) {
				if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
					$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
				}
			}
		}


		if($this->config->get('ticketsetting_logo_link')) {
			$data['home'] = $this->config->get('ticketsetting_logo_link');
		} else {
			$data['home'] = $this->url->link('support/support');
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

		$this->load->model('module_ticket/ticketuser');

		if(($this->customer->isLogged() && !$this->ticketuser->isLogged()) || ($this->customer->isLogged() && $this->ticketuser->isLogged() && $this->customer->getEmail() != $this->ticketuser->getEmail())) {
			$ticketuser_id = $this->model_module_ticket_ticketuser->ForceRegistered($this->customer->getId());

			$ticketuser_info = $this->model_module_ticket_ticketuser->getTicketUser($ticketuser_id);
			if($ticketuser_info) {
				// Force Login
				$this->ticketuser->Forcelogin($ticketuser_info['email']);
			}
		}

		$data['title'] = $this->document->getTitle();

		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts();
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$data['store_name'] = $this->config->get('config_name');

		$data['submission_link_display'] = $this->config->get('ticketsetting_ticket_submission_link');
		$data['login_link_display'] = $this->config->get('ticketsetting_login_link');

		// Heading
		$data['heading_edit_profile'] = $this->language->get('heading_edit_profile');
		$data['heading_edit_password'] = $this->language->get('heading_edit_password');

		// Text
		$data['text_request_list'] = $this->language->get('text_request_list');
		$data['text_edit_profile'] = $this->language->get('text_edit_profile');
		$data['text_edit_password'] = $this->language->get('text_edit_password');
		$data['text_logout'] = $this->language->get('text_logout');

		$data['button_ticket_submission'] = $this->language->get('button_ticket_submission');
		$data['button_support_login'] = $this->language->get('button_support_login');
		$data['button_signup'] = $this->language->get('button_signup');
		$data['button_close'] = $this->language->get('button_close');
		$data['button_already'] = $this->language->get('button_already');
		$data['button_upload_image'] = $this->language->get('button_upload_image');
		$data['button_close'] = $this->language->get('button_close');
		$data['button_submit'] = $this->language->get('button_submit');

		$data['text_not_memeber'] = $this->language->get('text_not_memeber');
		$data['text_signup'] = $this->language->get('text_signup');
		$data['text_forgot'] = $this->language->get('text_forgot');
		$data['text_password'] = $this->language->get('text_password');

		$data['tab_login'] = $this->language->get('tab_login');
		$data['tab_register'] = $this->language->get('tab_register');
		$data['tab_forgot'] = $this->language->get('tab_forgot');

		$data['button_login'] = $this->language->get('button_login');
		$data['button_forgot'] = $this->language->get('button_forgot');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_password'] = $this->language->get('entry_password');
		$data['entry_password_confirm'] = $this->language->get('entry_password_confirm');
		$data['entry_image'] = $this->language->get('entry_image');

		$data['link_ticket_submission'] = $this->url->link('support/request_form', '', true);

		if (is_file(DIR_IMAGE . $this->config->get('ticketsetting_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('ticketsetting_logo');
		} else {
			$data['logo'] = '';
		}

		if ($this->ticketuser->getImage()) {
			$data['ticketuser_thumb'] = $this->model_tool_image->resize($this->ticketuser->getImage(), 60, 60);
			$data['photo_thumb'] = $this->model_tool_image->resize($this->ticketuser->getImage(), 30, 30);
		} else {
			$data['ticketuser_thumb'] = $this->model_tool_image->resize('no_photoimage.png', 60, 60);

			$data['photo_thumb'] = $this->model_tool_image->resize('no_photoimage.png', 30, 30);
		}

		// For page specific css
		if (isset($this->request->get['route'])) {
			if (isset($this->request->get['product_id'])) {
				$class = '-' . $this->request->get['product_id'];
			} elseif (isset($this->request->get['path'])) {
				$class = '-' . $this->request->get['path'];
			} elseif (isset($this->request->get['manufacturer_id'])) {
				$class = '-' . $this->request->get['manufacturer_id'];
			} elseif (isset($this->request->get['information_id'])) {
				$class = '-' . $this->request->get['information_id'];
			} else {
				$class = '';
			}

			$data['class'] = str_replace('/', '-', $this->request->get['route']) . $class;
		} else {
			$data['class'] = 'common-home';
		}

		$data['request_list'] = $this->url->link('support/request_list', '', true);
		$data['edit_profile'] = $this->url->link('support/edit', '', true);
		$data['edit_password'] = $this->url->link('support/password', '', true);
		$data['logout'] = $this->url->link('support/logout', '', true);

		$data['userphoto_display_header'] = $this->config->get('ticketsetting_userphoto_display_header');

		$data['logged'] = $this->ticketuser->isLogged();
		$data['name'] = $this->ticketuser->getName();
		$data['email'] = $this->ticketuser->getEmail();
		$data['image'] = $this->ticketuser->getImage();

		if(isset($this->request->get['login'])) {
			$data['login_popup'] = true;
		} else {
			$data['login_popup'] = false;
		}

		if(isset($this->request->get['login'])) {
			if(!isset($this->session->data['support_redirect'])) {
				$this->session->data['support_redirect'] = $this->url->link('support/request_list', '', true);
			}
		} else {
			unset($this->session->data['support_redirect']);
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
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/support/header.tpl')) {
				return $this->load->view($this->config->get('config_template') . '/template/support/header.tpl', $data);
			} else {
				return $this->load->view('default/template/support/header.tpl', $data);
			}
		} else if(VERSION <= '2.3.0.2') {
			return $this->load->view('support/header', $data);
		} else {
			if($mytheme == 'journal3' && VERSION >= '3.0.0.0') {
				if($this->config->get('template_directory') == '') {
					$this->config->set('template_directory', 'journal3/template/');
				}

				$this->config->set('template_engine', 'template');

				return $this->load->view('support/header', $data);
			} else {
				$this->config->set('template_engine', 'template');
				return $this->load->view('support/header', $data);
			}
		}
	}
}
