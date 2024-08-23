<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiInformationInformation extends OcrestapiController {
	public function index() {
		if($this->request->server['REQUEST_METHOD'] == 'GET') {
		if($this->validateToken(true)) {
			$this->checkPlugin();
		}else{
			$this->load_language();
		}
		$language = $this->load->language('information/information');
		$language = $this->load->language('common/footer');
		$language = $this->load->language('ocrestapi/message');

		$this->load->model('catalog/information');
			unset($language['backup']);
			$this->json['language'] = $language;
		if (isset($this->request->get['information_id'])) {
			$information_id = (int)$this->request->get['information_id'];
		} else {
			$information_id = 0;
		}
		if(isset($this->request->get['information_id'])){
				$information_info = $this->model_catalog_information->getInformation($information_id);

				if ($information_info) {
					$this->document->setTitle($information_info['meta_title']);
					$this->document->setDescription($information_info['meta_description']);
					$this->document->setKeywords($information_info['meta_keyword']);
					$data['heading_title'] = $information_info['title'];

					$data['description'] = html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8');

					$this->json['data'] = $data;
				
					
				}
			}else{

				$data['informations'] = array();

				foreach ($this->model_catalog_information->getInformations() as $result) {
					if ($result['bottom']) {
						$data['informations'][] = array(
							'information_id' => $result['information_id'],
							'title' => $result['title']
						);
					}
				}
				$this->json['data'] = $data;
			}
		}else{
			$this->json['errors'] = [ 'http_method' => 'Only GET method is allowed.'];
		}
		return $this->sendResponse();
	}

	public function agree() {
		$this->checkPlugin();
		$this->load->model('catalog/information');

		if (isset($this->request->get['information_id'])) {
			$information_id = (int)$this->request->get['information_id'];
		} else {
			$information_id = 0;
		}

		$output = '';

		$information_info = $this->model_catalog_information->getInformation($information_id);

		if ($information_info) {
			$output .= html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8') . "\n";
		}

		$this->response->setOutput($output);
	}
}