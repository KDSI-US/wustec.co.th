<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleAnnouncements extends Controller
{
	public function index()
	{
		$this->load->model('extension/announcement');

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

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
			$data['sort'] = $this->request->get['sort'];
		} else {
			$url .= '&sort=a.added_at';
			$data['sort'] = "a.added_at";
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

		$announcement_category = $this->model_extension_announcement->getAnnouncementCategoryData($announcement_category_id);
		$data['breadcrumbs'][] = array(
			'text'      => $announcement_category['title'],
			'href'      => $this->url->link('common/home')
		);
		$data['heading_title'] = $announcement_category['title'];
		$this->document->setTitle($announcement_category['title']);

		$filter_data = array(
			'announcement_category_id' => $announcement_category_id,
			'start'              => ($page - 1) * $limit,
			'limit'              => $limit,
			'status'						=> '1'
		);

		$announcement_data = $this->model_extension_announcement->getAnnouncementByCategory($filter_data);
		foreach ($announcement_data as $announcement) {
			$time = strtotime($announcement['added_at']);
			$data['announcements'][] = array(
				'id'  							=> $announcement['announcement_id'],
				'image'       			=> $this->model_tool_image->resize($announcement['image']),
				'url'								=> $this->url->link('extension/announcement') . "?id=" . $announcement['announcement_id'],
				'title'       			=> trim(strip_tags(html_entity_decode($announcement['title'], ENT_QUOTES, 'UTF-8'))),
				'date'							=> date('F j, Y', $time),
				'text'							=> substr(trim(html_entity_decode($announcement['text'], ENT_QUOTES, 'UTF-8')), 0, 250) . "..."
			);
		}

		$data['continue'] = $this->url->link('common/home');

		return $this->load->view('extension/module/announcements', $data);
	}
}
