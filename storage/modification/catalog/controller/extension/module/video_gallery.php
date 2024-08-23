<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleVideoGallery extends Controller
{
	public function index($setting)
	{
		$this->load->language('extension/module/video_gallery');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_tax'] = $this->language->get('text_tax');

		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');

		$this->load->model('extension/video_gallery_category');
		$this->load->model('extension/video_gallery');

		$this->document->addStyle('catalog/view/javascript/jquery/swiper/css/swiper.min.css');
		$this->document->addStyle('catalog/view/javascript/jquery/swiper/css/opencart.css');
		$this->document->addScript('catalog/view/javascript/jquery/swiper/js/swiper.jquery.js');

		$this->load->model('tool/image');

		$data['video_galleries'] = array();

		$filter_data = array(
			'start' => 0,
			'limit' => 20
		);

		$video_gallery_info = $this->model_extension_video_gallery_category->getVideophotos($filter_data);

		if ($video_gallery_info) {
			foreach ($video_gallery_info as $info) {
				$filter_data = array(
					'video_gallery_category_id' => $info['video_gallery_category_id']
				);
				$video_total = 0;
				$video_total = $this->model_extension_video_gallery->getTotalVideoGalleries($filter_data);
				if ($info['image']) {
					$image = $this->model_tool_image->resize($info['image'], 360, 200);
				} else {
					$image = false;
				}
				$data['video_galleries'][] = array(
					'video_gallery_category_id' => $info['video_gallery_category_id'],
					'title'	   		=> $info['title'],
					'description'	=> html_entity_decode($info['description']),
					'chk_video_category_title'	=> $info['chk_video_category_title'],
					'chk_video_category_description'	=> $info['chk_video_category_description'],
					'chk_video_category_image'	  => $info['chk_video_category_image'],
					'image'	      => $image,
					'href'	      => $this->url->link('extension/video_gallery_all' . '&video_gallery_category_id=' . $info['video_gallery_category_id'])
				);
			}
			if ($data['video_galleries']) {
				return $this->load->view('extension/module/video_gallery', $data);
			}
		}
	}
}
