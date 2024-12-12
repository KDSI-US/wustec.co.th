<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionAnnouncement extends Controller
{
	public function index()
	{
		$this->load->language('extension/announcement');
		$this->load->model('extension/announcement');

		$this->load->model('tool/image');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
			'separator' => false
		);

		$data['text_empty'] = $this->language->get('text_empty');

		$data['announcements'] = array();

		$announcement_category_id = '1';

		$announcement_category = $this->model_extension_announcement->getAnnouncementCategoryData($announcement_category_id);
		$data['breadcrumbs'][] = array(
			'text'      => $announcement_category['title'],

		);
		$data['heading_title'] = $announcement_category['title'];
		$this->document->setTitle($announcement_category['title']);

		$filter_data = array(
			'announcement_category_id' => $announcement_category_id,
			'id' => $this->request->get['id'],
			'status' => '1'
		);

		$announcement = $this->model_extension_announcement->getAnnouncementByCategory($filter_data);
		if($announcement) {
			$time = strtotime($announcement[0]['added_at']);
			$data['announcement'] = array(
				'announcement_id'  => $announcement[0]['announcement_id'],
				'image'       => $this->model_tool_image->resize($announcement[0]['image']),
				'title'       => trim(strip_tags(html_entity_decode($announcement[0]['title'], ENT_QUOTES, 'UTF-8'))),
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
		else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}
}
