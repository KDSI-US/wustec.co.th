<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiExtensionModuleBanner extends OcrestapiController {
	public function index($setting) {
		$this->checkPlugin();
		static $module = 0;

		$this->load->model('design/banner');
		$this->load->model('tool/image');

		$data['banners'] = array();

		$results = $this->model_design_banner->getBanner($setting['banner_id']);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$data['banners'][] = array(
					'title' => $result['title'],
					'link'  => $result['link'],
					'image' => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height'])
				);
			}
		}

		$data['module'] = $module++;

		$this->json['data'] = $data;
		
		return $this->sendResponse();

		
	} 
}