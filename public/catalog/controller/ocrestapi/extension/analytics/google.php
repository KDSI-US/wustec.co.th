<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
// require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiExtensionAnalyticsGoogle extends OcrestapiController {
    public function index() {
    	$this->checkPlugin();
		$data = html_entity_decode($this->config->get('analytics_google_code'), ENT_QUOTES, 'UTF-8');

		$this->json['data']	= $data;

		$this->sendResponse();
	}
}
