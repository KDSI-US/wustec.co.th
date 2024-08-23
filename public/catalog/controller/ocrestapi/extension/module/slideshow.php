<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiExtensionModuleSlideshow extends OcrestapiController {
	public function index($setting) {

		$this->load_language();
		//die;
		static $module = 0;		
		$this->load->model('design/banner');
		$this->load->model('tool/image');
		
		$data = array();

		$results = $this->model_design_banner->getBanner($setting['banner_id']);
		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$data[] = array(
					'title' => $result['title'],
					'image' => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height'])
				);
			}
		}
		
		$this->json['data'] =$data;
		return $this->sendResponse();
	}
}