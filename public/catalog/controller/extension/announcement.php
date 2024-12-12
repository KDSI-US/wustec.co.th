<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionAnnouncement extends Controller
{
	public function index()
	{
		$this->language->load('extension/video_gallery_all');

		$this->load->model('extension/video_gallery');
		$this->load->model('extension/video_gallery_category');

		$this->load->model('tool/image');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
			'separator' => false
		);

		$data['text_empty'] = $this->language->get('text_empty');

		$data['video_galleries'] = array();

		$video_gallery_category_id = '1';

		$video_gallery_category = $this->model_extension_video_gallery->getVideoGalleryCategoryData($video_gallery_category_id);
		$data['breadcrumbs'][] = array(
			'text'      => $video_gallery_category['title'],
			'href'      => $this->url->link('extension/announcements')

		);
		$data['heading_title'] = $video_gallery_category['title'];
		$this->document->setTitle($video_gallery_category['title']);

		$filter_data = array(
			'video_gallery_category_id' => $video_gallery_category_id,
			'id' => $this->request->get['id']
		);

		$announcement = $this->model_extension_video_gallery->getVideoGalleryByCategory($filter_data);
		$tempUrl = explode('=',$announcement[0]['video_url']);
		$video_url_key = $tempUrl[1];
		$time = strtotime($announcement[0]['added_at']);
		$data['announcement'] = array(
			'video_gallery_id'  => $announcement[0]['video_gallery_id'],
			'image'       => $announcement[0]['image'],
			'url_key'           => $video_url_key,
			'title'       => trim(strip_tags(html_entity_decode($announcement[0]['video_title'], ENT_QUOTES, 'UTF-8'))),
			'video_description' => trim(html_entity_decode($announcement[0]['video_description'], ENT_QUOTES, 'UTF-8')),
			'date'							=> date('F j, Y', $time),
			'text'							=> html_entity_decode($announcement[0]['text'], ENT_QUOTES, 'UTF-8')
		);

		$data['continue'] = $this->url->link('common/home');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('extension/announcement', $data));
	}
}
