<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiInformationSitemap extends OcrestapiController {
	public function index() {
		if($this->validateToken(true)) {
			$this->checkPlugin();
		}else{
			$this->load_language();
		}
		 if($this->request->server['REQUEST_METHOD'] == 'GET') {
		$this->load->language('information/sitemap');

		$this->load->model('catalog/category');
		$this->load->model('catalog/product');

		$data['categories'] = array();

		$categories_1 = $this->model_catalog_category->getCategories(0);

		foreach ($categories_1 as $category_1) {
			$level_2_data = array();

			$categories_2 = $this->model_catalog_category->getCategories($category_1['category_id']);

			foreach ($categories_2 as $category_2) {
				$level_3_data = array();

				$categories_3 = $this->model_catalog_category->getCategories($category_2['category_id']);

				foreach ($categories_3 as $category_3) {
					$level_3_data[] = array(
						'name' => $category_3['name']
					);
				}

				$level_2_data[] = array(
					'name'     => $category_2['name'],
					'children' => $level_3_data
					
				);
			}

			$data['categories'][] = array(
				'name'     => $category_1['name'],
				'children' => $level_2_data
			);
		}
		
		$this->load->model('catalog/information');

		$data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
			$data['informations'][] = array(
				'title' => $result['title']
				
			);
		}
		$this->json['data'] = $data;
		}else{
			$this->json['errors'] = [ 'http_method' => 'Only GET method is allowed.'];
		}
		return $this->sendResponse();
	}
}