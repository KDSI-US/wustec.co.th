<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiCommoncategory extends OcrestapiController {

	public function index() {
		if($this->request->server['REQUEST_METHOD'] == 'GET') {
		$this->load_language();
		$language = $this->load->language('common/menu');
		$language = $this->load->language('ocrestapi/message');

		// Menu
		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$categories = $this->get_categories(0);
		$data['categories'] = $categories;

		$this->json['data'] = $data;
		unset($language['backup']);
		$this->json['language'] =$language;
	}else{
		$this->json['errors'] = [ 'http_method' => 'Only GET method is allowed.'];
	}
		return $this->sendResponse();
	
	}
	protected function getImage($category_id)
	{
		$query=$this->db->query("SELECT * FROM `" . DB_PREFIX . "mobile_category_image` WHERE category_id = '" . (int)$category_id . "'");
		return !empty($query->row)?$query->row['image']:'';
	}
	private function get_categories($category_id)
	{
		$this->load->model('tool/image');
		$width=$this->config->get('restapi_default_image_category_icon_width_mobile');
		$height=$this->config->get('restapi_default_image_category_icon_height_mobile');
		$default_thumb = $this->model_tool_image->resize('no_image.png', $width,$height);

		$categories = $this->model_catalog_category->getCategories($category_id);
		if(!empty($categories)) {
			foreach ($categories as $key => $category) {
				$thumb=$this->getImage($category['category_id']);
				
				$categories[$key]['icon'] = !empty($thumb)?$this->model_tool_image->resize($thumb,$width,$height):$default_thumb;
				$categories[$key]['image'] = !empty($category['image'])? HTTP_SERVER.'image/'.$category['image']:'';
				$categories[$key]['children'] = $this->get_categories($category['category_id']);
			}
		}
		return $categories;
	}
}
