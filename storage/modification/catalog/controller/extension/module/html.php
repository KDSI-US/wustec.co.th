<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleHTML extends Controller {
	public function index($setting) {
		if (isset($setting['module_description'][$this->config->get('config_language_id')])) {
			$data['heading_title'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['title'], ENT_QUOTES, 'UTF-8');
			$html = $setting['module_description'][$this->config->get('config_language_id')]['description'];

			$date_seoul = new DateTime("now", new DateTimeZone('Asia/Seoul') );
			$html = str_replace("{{ date_seoul }}", $date_seoul->format('Y-m-d'), $html);
			$html = str_replace("{{ date_now }}", date('Y-m-d'), $html);

			$data['html'] = html_entity_decode($html, ENT_QUOTES, 'UTF-8');

			return $this->load->view('extension/module/html', $data);
		}
	}
}
