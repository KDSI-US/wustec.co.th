<?php
class ControllerExtensionVideoGalleryAll extends Controller
{
	public function index()
	{
		$this->language->load('extension/video_gallery_all');

		$this->document->addStyle('catalog/view/javascript/jquery/prettyphoto/prettyPhoto.css');
		$this->document->addScript('catalog/view/javascript/jquery/prettyphoto/jquery.prettyPhoto.js');

		$this->load->model('extension/video_gallery');
		$this->load->model('extension/video_gallery_category');

		$this->load->model('tool/image');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
			'separator' => false
		);

		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
                        $limit = (isset($this->config->get('config_limit_catalog'))) ? $this->config->get('config_limit_catalog') : 20;
		}

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

		if (isset($this->request->get['video_gallery_category_id'])) {
			$video_gallery_category_id = $this->request->get['video_gallery_category_id'];
		} else {
			$video_gallery_category_id = '';
		}

		$video_gallery_category = $this->model_extension_video_gallery->getVideoGalleryCategoryData($video_gallery_category_id);
		if ($video_gallery_category) {
			$data['breadcrumbs'][] = array(
				'text'      => $video_gallery_category['title'],
				'href'      => $this->url->link('extension/video_gallery_all')

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
				$tempUrl = explode('=',$video_gallery['video_url']);
				$video_url_key = $tempUrl[1];
				$data['video_galleries'][] = array(
					'video_gallery_id'  => $video_gallery['video_gallery_id'],
					'video_image'       => $video_gallery['image'],
					'video_url'         => $video_gallery['video_url'] . '&feature=related',
					'url_key'           => $video_url_key,
					'video_title'       => trim(strip_tags(html_entity_decode($video_gallery['video_title'], ENT_QUOTES, 'UTF-8'))),
					'video_description' => trim(html_entity_decode($video_gallery['video_description'], ENT_QUOTES, 'UTF-8')) 
				);
			}

			$pagination = new Pagination();
			$pagination->total = $video_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('extension/video_gallery_all', '=' .  $url . '&page={page}');

			$data['pagination'] = $pagination->render();
			$data['results'] = sprintf($this->language->get('text_pagination'), ($video_total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($video_total - 5)) ? $video_total : ((($page - 1) * 5) + 5), $video_total, ceil($video_total / 5));
			$data['continue'] = $this->url->link('common/home');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('extension/video_gallery_all', $data));

		} else {

			$url = '';
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text'      => 'Video Categories',
				'href'      => $this->url->link('extension/video_gallery_all', $url),

			);

			$this->document->setTitle($this->language->get('heading_title'));

			$data['heading_title'] = $this->language->get('heading_title');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['continue'] = $this->url->link('common/home');
			$filter_data = array(
				'video_gallery_category_id' => '',
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit
			);

			$data['video_gallery_category'] = array();
			$video_categories = $this->model_extension_video_gallery_category->getVideoGalleryCategories($filter_data);

			foreach ($video_categories as $video_category) {
				$data['video_gallery_categories'][] = array(
					'category_id'  => $video_category['video_gallery_category_id'],
					'title'        => $video_category['title'],
					'image'        => "/image/".$video_category['image'],
					'description'  => html_entity_decode($video_category['description']),
					'href'         => $this->url->link('extension/video_gallery_all' . '&video_gallery_category_id=' . $video_category['video_gallery_category_id'])
				);
			}

			$video_gallery_category_total = $this->model_extension_video_gallery_category->getTotalVideoGalleryCategory();

			$pagination = new Pagination();
			$pagination->total = $video_gallery_category_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('extension/video_gallery_all', '=' .  $url . '&page={page}');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($video_gallery_category_total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($video_gallery_category_total - 5)) ? $video_gallery_category_total : ((($page - 1) * 5) + 5), $video_gallery_category_total, ceil($video_gallery_category_total / 5));

			$data['continue'] = $this->url->link('common/home');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('extension/video_gallery_category', $data));
		}
	}
}
