<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiProductManufacturer extends OcrestapiController {
	public function index() {
		if($this->validateToken(true)) {
			$this->checkPlugin();
		}else{
			$this->load_language();
		}
		$language = $this->load->language('product/manufacturer');

		$this->load->model('catalog/manufacturer');

		$this->load->model('tool/image');
		$data['brands'] = array();

		$results = $this->model_catalog_manufacturer->getManufacturers();

		foreach ($results as $result) {
			if (is_numeric(utf8_substr($result['name'], 0, 1))) {
				$key = '0 - 9';
			} else {
				$key = utf8_substr(utf8_strtoupper($result['name']), 0, 1);
			}

			if (!isset($data['brands'][$key])) {
				$data['brands'][$key]['name'] = $key;
			}

			$data['brands'][$key]['manufacturer'][] = array(
				'name' => $result['name'],
				'manufacturer_id' => $result['manufacturer_id']
				
			);
		}
		
		$data['brands'] = array_values($data['brands']);
		unset($language['backup']);
		$this->json['language'] = $language;
		$this->json['data'] = $data;
		return $this->sendResponse();
	}


}
