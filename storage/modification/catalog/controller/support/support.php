<?php
/* This file is under Git Control by KDSI. */
class ControllerSupportSupport extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('support/support');

		$this->load->model('module_ticket/support');

		$ticketsetting_language = (array)$this->config->get('ticketsetting_language');

		$config_language_id = (int)$this->config->get('config_language_id');

		$heading_title = isset($ticketsetting_language[$config_language_id]['title']) ? $ticketsetting_language[$config_language_id]['title'] : '';
		$meta_title = isset($ticketsetting_language[$config_language_id]['meta_title']) ? $ticketsetting_language[$config_language_id]['meta_title'] : '';
		$meta_description = isset($ticketsetting_language[$config_language_id]['meta_description']) ? $ticketsetting_language[$config_language_id]['meta_description'] : '';
		$meta_keyword = isset($ticketsetting_language[$config_language_id]['meta_keyword']) ? $ticketsetting_language[$config_language_id]['meta_keyword'] : '';

		$this->document->setTitle($meta_title);
		$this->document->setDescription($meta_description);
		$this->document->setKeywords($meta_keyword);

		$this->document->addScript('catalog/view/javascript/modulepoints/ticketsystem.js');
		$this->document->addStyle('catalog/view/theme/default/stylesheet/modulepoints/ticketsystem.css');

		if($this->config->get('ticketsetting_headerfooter')) {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/modulepoints/moduleheader.css');
		}

		$this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');

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

		$data['tab_ticket_submission'] = $this->language->get('tab_ticket_submission');
		$data['tab_video_guide'] = $this->language->get('tab_video_guide');
		$data['tab_knowledge_base'] = $this->language->get('tab_knowledge_base');

		$data['text_departments'] = $this->language->get('text_departments');

		$ticketsetting_widgets = (array)$this->config->get('ticketsetting_widgets');
		$data['ticketsubmission_status'] = isset($ticketsetting_widgets['ticketsubmission']) ? $ticketsetting_widgets['ticketsubmission'] : '';
		$data['videos_status'] = isset($ticketsetting_widgets['videos']) ? $ticketsetting_widgets['videos'] : '';
		$data['knowledgebase_status'] = isset($ticketsetting_widgets['knowledgebase']) ? $ticketsetting_widgets['knowledgebase'] : '';

		$ticketdepartments = $this->model_module_ticket_support->getTicketdepartments();
		$data['ticketdepartments'] = array();
		foreach ($ticketdepartments as $ticketdepartment) {
			$data['ticketdepartments'][] = array(
				'ticketdepartment_id'		=> $ticketdepartment['ticketdepartment_id'],
				'icon_class'				=> $ticketdepartment['icon_class'],
				'title'						=> $ticketdepartment['title'],
				'sub_title'					=> $ticketdepartment['sub_title'],
				'href'						=> $this->url->link('support/request_form', 'ticketdepartment_id='. $ticketdepartment['ticketdepartment_id'], true),
			);
		}
		$ticketknowledge_sections = $this->model_module_ticket_support->getTicketknowledgeSections();
		$data['ticketknowledge_sections'] = array();
		foreach ($ticketknowledge_sections as $ticketknowledge_section) {
			$data['ticketknowledge_sections'][] = array(
				'ticketknowledge_section_id'		=> $ticketknowledge_section['ticketknowledge_section_id'],
				'icon_class'				=> $ticketknowledge_section['icon_class'],
				'title'						=> $ticketknowledge_section['title'],
				'sub_title'					=> $ticketknowledge_section['sub_title'],
				'href'						=> $this->url->link('support/knowledge_section', 'ticketknowledge_section_id='. $ticketknowledge_section['ticketknowledge_section_id'], true),
			);
		}

		$ticketvideos = $this->model_module_ticket_support->getTicketvideos();
		$data['ticketvideos'] = array();
		foreach ($ticketvideos as $ticketvideo) {
			$data['ticketvideos'][] = array(
				'ticketdepartment_id'		=> $ticketvideo['ticketvideo_id'],
				'url'						=> $ticketvideo['url'],
				'title'						=> $ticketvideo['title'],
				'sub_title'					=> $ticketvideo['sub_title'],
			);
		}

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
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/support/support.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/support/support.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/support/support.tpl', $data));
			}
		} else {
			if($mytheme == 'journal3' && VERSION >= '3.0.0.0') {
				if($this->config->get('template_directory') == '') {
					$this->config->set('template_directory', 'journal3/template/');
				}

				$this->response->setOutput($this->load->view('support/support', $data));

				$this->config->set('template_engine', 'twig');

			} else {
				$this->response->setOutput($this->load->view('support/support', $data));
			}
		}
	}
}