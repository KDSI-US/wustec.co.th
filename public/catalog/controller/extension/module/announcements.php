<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleAnnouncements extends Controller
{
	public function index()
	{
		$this->language->load('extension/announcement');

		$this->load->model('extension/announcement');
		$this->load->model('extension/announcement_category');

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

		if (isset($this->request->get['announcement_category_id'])) {
			$url .= '&announcement_category_id=' . $this->request->get['announcement_category_id'];
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

		$data['announcements'] = array();

		$announcement_category_id = '1';
		$this->request->get['announcement_category_id'] = '1';

		$announcement_category = $this->model_extension_announcement->getVideoGalleryCategoryData($announcement_category_id);
		$data['breadcrumbs'][] = array(
			'text'      => $announcement_category['title'],
			'href'      => $this->url->link('extension/announcements')
		);
		$data['heading_title'] = $announcement_category['title'];
		$this->document->setTitle($announcement_category['title']);

		$filter_data = array(
			'announcement_category_id' => $this->request->get['announcement_category_id'],
			'start'              => ($page - 1) * $limit,
			'limit'              => $limit
		);

		$video_total = $this->model_extension_announcement->getTotalVideoGalleries($filter_data);
		$announcement_data = $this->model_extension_announcement->getVideoGalleryByCategory($filter_data);
		foreach ($announcement_data as $announcement) {
			$time = strtotime($announcement['added_at']);
			$data['announcements'][] = array(
				'id' => $announcement['announcement_id'],
				'image' => $announcement['image'],
				'url' => $this->url->link('extension/announcement') . "&id=" . $announcement['announcement_id'],
				'text' => trim(strip_tags(html_entity_decode($announcement['text'], ENT_QUOTES, 'UTF-8'))),
				'date' => date('F j, Y', $time)
			);
		}

		$pagination = new Pagination();
		$pagination->total = $announcement_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('extension/announcements', '=' .  $url . '&page={page}');

		$data['pagination'] = $pagination->render();
		$data['results'] = sprintf($this->language->get('text_pagination'), ($video_total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($video_total - 5)) ? $video_total : ((($page - 1) * 5) + 5), $announcement_total, ceil($announcement_total / 5));
		$data['continue'] = $this->url->link('common/home');

		return $this->load->view('extension/module/announcements', $data);
	}
}
