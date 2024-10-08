<?php
class ControllerExtensionModuleMpvideo extends Controller {
	public function index($setting) {
		if ($this->config->get('gallery_setting_status')) {
			$this->load->language('extension/module/mpvideo');

			static $module = 0;

			$this->load->model('extension/mpphotogallery/video');
			$this->load->model('tool/image');

			$this->document->addStyle('catalog/view/javascript/mpphotogallery/owl-carousel/owl.carousel.css');
			$this->document->addScript('catalog/view/javascript/mpphotogallery/owl-carousel/owl.carousel.js');

			$this->document->addStyle('catalog/view/javascript/mpphotogallery/assets/style.css');
			$this->document->addStyle('catalog/view/javascript/mpphotogallery/assets/smgallery/css/lightgallery.css');
			$this->document->addScript('catalog/view/javascript/mpphotogallery/assets/smgallery/js/lightgallery-all.js');
			$this->document->addScript('catalog/view/javascript/mpphotogallery/assets/smgallery/js/jquery.mousewheel.min.js');

			$data['heading_title'] = isset($setting['video_description'][$this->config->get('config_language_id')]['title']) ? $setting['video_description'][$this->config->get('config_language_id')]['title'] : $this->language->get('heading_title');

			$data['limit'] = !empty($setting['limit']) ? $setting['limit'] : 5;
			$data['carousel'] = !empty($setting['carousel']) ? $setting['carousel'] : '';
			$data['extitle'] = !empty($setting['extitle']) ? $setting['extitle'] : '';

			$data['videos'] = [];

			if ($data['carousel']) {
				$videos = $this->model_extension_mpphotogallery_video->getVideosByGallery($setting['gallery_id']);
			} else {
				$videos = $this->model_extension_mpphotogallery_video->getVideosByGallery($setting['gallery_id'], $setting['limit']);
			}

			foreach ($videos as $video_info) {
				if ($video_info['video_thumb'] && file_exists(DIR_IMAGE . $video_info['video_thumb'])) {
					$thumb = $this->model_tool_image->resize($video_info['video_thumb'], $setting['width'], $setting['height']);
				} else {
					$thumb = $this->model_tool_image->resize('no_image.png', $setting['width'], $setting['height']);
				}

				$data['videos'][] = array(
					'gallery_id'  	=> $video_info['gallery_id'],
					'name'  		=> $video_info['name'],
					'link'  		=> $video_info['link'],
					'thumb'       	=> $thumb,
				);
			}

			$data['module'] = $module++;

			$data['gallery_setting_color'] = $this->config->get('gallery_setting_color');

			$data['text_view'] = $this->language->get('text_view');
			$data['view'] = $this->url->link('extension/mpphotgallery/album_video', '', true);

			if ($data['videos']) {
				return $this->load->view('extension/module/mpvideo', $data);
			}
		}
	}
}