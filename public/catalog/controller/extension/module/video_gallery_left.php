<?php
class ControllerExtensionModuleVideoGalleryLeft extends Controller
{
	public function index()
	{
		$this->load->language('extension/module/video_gallery_left');
		$data['heading_title'] = $this->language->get('heading_title');
		$this->load->model('extension/video_gallery_category');
		$this->load->model('extension/video_gallery');
		if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
		} else {
			$parts = array();
		}
		if (isset($parts[0])) {
			$data['video_gallery_category_id'] = $parts[0];
		} else {
			$data['video_gallery_category_id'] = 0;
		}
		$data['video_galleries'] = array();
		$filter_data = array(
			'start' => 0,
			'limit' => 20
		);
		$video_gallery_info = $this->model_extension_video_gallery_category->getVideoGalleryPhotos($filter_data);

		if ($video_gallery_info) {
			foreach ($video_gallery_info as $info) {
				$filter_data = array(
					'video_gallery_category_id' => $info['video_gallery_category_id']
				);
				$video_total = 0;
				$video_total = $this->model_extension_video_gallery->getTotalVideoGalleries($filter_data);
				$video_gallery_left_count = $this->config->get('video_gallery_left_count');
				if ($video_gallery_left_count == 1) {
					$infotitle = $info['title'] . '  (' . ($video_total) . ')';
				} else {
					$infotitle = $info['title'];
				}
				$data['video_galleries'][] = array(
					'video_gallery_category_id' => $info['video_gallery_category_id'],
					'title'	   		=> $infotitle,
					'chk_video_category_title'	=> $info['chk_video_category_title'],
					'href'	      => $this->url->link('extension/video_gallery_all' . '&video_gallery_category_id=' . $info['video_gallery_category_id'])
				);
			}
			if ($data['video_galleries']) {
				return $this->load->view('extension/module/video_gallery_left', $data);
			}
		}
	}
}
