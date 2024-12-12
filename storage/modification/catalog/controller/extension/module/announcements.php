<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleAnnouncements extends Controller
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

		$limit = "6";

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['video_gallery_category_id'])) {
			$url .= '&video_gallery_category_id=' . $this->request->get['video_gallery_category_id'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
			$data['sort'] = $this->request->get['sort'];
		} else {
			$url .= '&sort=vg.added_at';
			$data['sort'] = "vg.added_at";
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
			$data['order'] = $this->request->get['order'];
		} else {
			$url .= '&order=DESC';
			$data['order'] = "DESC";
		}

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		$data['text_empty'] = $this->language->get('text_empty');

		$data['video_galleries'] = array();

		$video_gallery_category_id = '1';
		$this->request->get['video_gallery_category_id'] = '1';

		$video_gallery_category = $this->model_extension_video_gallery->getVideoGalleryCategoryData($video_gallery_category_id);
		$data['breadcrumbs'][] = array(
			'text'      => $video_gallery_category['title'],
			'href'      => $this->url->link('extension/announcements')
		);
		$data['heading_title'] = $video_gallery_category['title'];
		$this->document->setTitle($video_gallery_category['title']);

		$filter_data = array(
			'video_gallery_category_id' => $this->request->get['video_gallery_category_id'],
			'start'              => ($page - 1) * $limit,
			'limit'              => $limit
		);

		$video_total = $this->model_extension_video_gallery->getTotalVideoGalleries($filter_data);
		$video_gallery_data = $this->model_extension_video_gallery->getVideoGalleryByCategory($filter_data);
		foreach ($video_gallery_data as $announcement) {
			$time = strtotime($announcement['added_at']);
			$data['announcements'][] = array(
				'id'  => $announcement['video_gallery_id'],
				'image'       => $announcement['image'],
				'url'								=> $this->url->link('extension/announcement') . "?id=" . $announcement['video_gallery_id'],
				'video_title'       => trim(strip_tags(html_entity_decode($announcement['video_title'], ENT_QUOTES, 'UTF-8'))),
				'video_description' => trim(html_entity_decode($announcement['video_description'], ENT_QUOTES, 'UTF-8')),
				'date'							=> date('F j, Y', $time),
				'text'							=> substr(trim(html_entity_decode($announcement['text'], ENT_QUOTES, 'UTF-8')), 0, 250) . "..."
			);
		}

		$data['continue'] = $this->url->link('common/home');

		return $this->load->view('extension/module/announcements', $data);
	}
}
