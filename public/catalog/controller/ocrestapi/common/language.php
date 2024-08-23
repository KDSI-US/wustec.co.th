<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiCommonLanguage extends OcrestapiController {

	public function index() {
		
		$this->load_language();
		$this->load->language('common/language');

		//$data['action'] ='common/language/language';

		//$data['code'] = $this->session->data['language'];
		
		$this->load->model('localisation/language');

		$data['languages'] = array();

		$results = $this->model_localisation_language->getLanguages();
		foreach ($results as $result) {

			if ($result['status']) {
				$data['languages'][] = array(
					'name' => $result['name'],
					'code' => $result['code']
				);
			}

		}


		$this->json['data'] =$data;
		return $this->sendResponse();
	}

	public function language() {
		$this->load_language();
		
		if (isset($this->request->post['code'])) {
			$data['language'] = $this->request->post['code'];
		}

		$this->json['data'] =isset($data)?$data:'';
		return $this->sendResponse();
	}
}