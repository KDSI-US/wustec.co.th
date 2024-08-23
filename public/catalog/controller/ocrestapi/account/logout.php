<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiAccountLogout extends OcrestapiController {
	public function index() {
		$this->checkPlugin();

		if ($this->customer->isLogged()) {
			$this->customer->logout();

			

			return new Action('ocrestapi/account/logout');
		}

		$this->load->language('account/logout');

		

		
		$this->json['data'] = $data;
		return $this->sendResponse();
		
	}
}
