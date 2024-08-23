<?php
/* This file is under Git Control by KDSI. */
class ControllerSupportFooter extends Controller {
	public function index() {
		$this->load->language('support/footer');

		$data['scripts'] = $this->document->getScripts('footer');

		$ticketsetting_language = (array)$this->config->get('ticketsetting_language');

		$data['text_message'] = isset($ticketsetting_language[$this->config->get('config_language_id')]['footer']) ? html_entity_decode($ticketsetting_language[$this->config->get('config_language_id')]['footer'], ENT_QUOTES, 'UTF-8') : '';

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
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/support/footer.tpl')) {
				return $this->load->view($this->config->get('config_template') . '/template/support/footer.tpl', $data);
			} else {
				return $this->load->view('default/template/support/footer.tpl', $data);
			}
		} else if(VERSION <= '2.3.0.2') {
			return $this->load->view('support/footer', $data);
		} else {
			if($mytheme == 'journal3' && VERSION >= '3.0.0.0') {
				if($this->config->get('template_directory') == '') {
					$this->config->set('template_directory', 'journal3/template/');
				}

				$this->config->set('template_engine', 'template');

				return $this->load->view('support/footer', $data);
			} else {
				$this->config->set('template_engine', 'template');
				return $this->load->view('support/footer', $data);
			}
		}
	}
}
