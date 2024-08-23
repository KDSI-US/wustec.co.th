<?php
class ControllerSupportRequestSuccess extends Controller {
	public function index() {
		$this->load->language('support/request_success');

		$this->document->addScript('catalog/view/javascript/modulepoints/ticketsystem.js');
		$this->document->addStyle('catalog/view/theme/default/stylesheet/modulepoints/ticketsystem.css');

		if($this->config->get('ticketsetting_headerfooter')) {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/modulepoints/moduleheader.css');
		}

		$ticketsetting_language = (array)$this->config->get('ticketsetting_success_language');

		$config_language_id = (int)$this->config->get('config_language_id');

		$heading_title = isset($ticketsetting_language[$config_language_id]['title']) ? $ticketsetting_language[$config_language_id]['title'] : '';
		$banner_title = isset($ticketsetting_language[$config_language_id]['banner_title']) ? $ticketsetting_language[$config_language_id]['banner_title'] : '';
		$notlogged_description = isset($ticketsetting_language[$config_language_id]['notlogged_description']) ? html_entity_decode($ticketsetting_language[$config_language_id]['notlogged_description'], ENT_QUOTES, 'UTF-8') : '';
		$logged_description = isset($ticketsetting_language[$config_language_id]['logged_description']) ? html_entity_decode($ticketsetting_language[$config_language_id]['logged_description'], ENT_QUOTES, 'UTF-8') : '';



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

		$this->document->setTitle($heading_title);

		$data['heading_title'] = $heading_title;
		$data['banner_title'] = $banner_title;
		$data['text_message'] = $notlogged_description;
		$data['text_message'] = $logged_description;

		$data['button_continue'] = $this->language->get('button_continue');

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
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/support/request_success.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/support/request_success.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/support/request_success.tpl', $data));
			}
		} else {
			if($mytheme == 'journal3' && VERSION >= '3.0.0.0') {
				if($this->config->get('template_directory') == '') {
					$this->config->set('template_directory', 'journal3/template/');
				}

				$this->response->setOutput($this->load->view('support/request_success', $data));

				$this->config->set('template_engine', 'twig');
			} else {
				$this->response->setOutput($this->load->view('support/request_success', $data));
			}
		}
	}
}