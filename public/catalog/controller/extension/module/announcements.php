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
		foreach ($video_gallery_data as $video_gallery) {
			$time = strtotime($video_gallery['added_at']);
			$data['video_galleries'][] = array(
				'id'  => $video_gallery['video_gallery_id'],
				'image'       => $video_gallery['image'],
				'url'								=> $this->url->link('extension/announcement') . "?id=" . $video_gallery['video_gallery_id'],
				'video_title'       => trim(strip_tags(html_entity_decode($video_gallery['video_title'], ENT_QUOTES, 'UTF-8'))),
				'video_description' => trim(html_entity_decode($video_gallery['video_description'], ENT_QUOTES, 'UTF-8')),
				'date'							=> date('F j, Y', $time)
			);
		}

		$pagination = new Pagination();
		$pagination->total = $video_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('extension/announcements', '=' .  $url . '&page={page}');

		$data['pagination'] = $pagination->render();
		$data['results'] = sprintf($this->language->get('text_pagination'), ($video_total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($video_total - 5)) ? $video_total : ((($page - 1) * 5) + 5), $video_total, ceil($video_total / 5));
		$data['continue'] = $this->url->link('common/home');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		return $this->load->view('extension/video_gallery_all', $data);
	}
}
