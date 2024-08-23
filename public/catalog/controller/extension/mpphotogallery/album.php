<?php
class ControllerExtensionMpPhotoGalleryAlbum extends Controller {
	private $error = [];

	public function mpphotogalleryMenu() {
		$menu = [];
		if($this->config->get('gallery_setting_status')) {
			$this->load->language('mpphotogallery/gallery_link');
			$children = [];

			$children[] = array(
				'name'  => $this->language->get('mptext_photo'),
				'href'  => $this->url->link('extension/mpphotogallery/album_photo', '', true),
			);

			$children[] = array(
				'name'  => $this->language->get('mptext_video'),
				'href'  => $this->url->link('extension/mpphotogallery/album_video', '', true),
			);

			$menu = array(
				'name'     => $this->language->get('mptext_gallery'),
				'children' => $children,
				'column'   => 1,
				'href'     => $this->url->link('extension/mpphotogallery/album', '', true),
			);
		}

		return $menu;
	}

	public function index() {
		if ($this->config->get('gallery_setting_status')) {
			$this->load->language('mpphotogallery/album');
			
			$this->load->model('extension/mpphotogallery/album');
			
			$this->load->model('tool/image');	

			$this->document->setTitle($this->language->get('heading_title'));

			$data['heading_title'] = $this->language->get('heading_title');
			$data['text_no_results'] = $this->language->get('text_no_results');
			$data['text_viewed'] = $this->language->get('text_viewed');

			$this->document->addStyle('catalog/view/javascript/mpphotogallery/assets/style.css');

			$data['gallery_setting_album_description'] = $this->config->get('gallery_setting_album_description'); 
			
			if (isset($this->request->get['page']) && (int)$this->request->get['page']) {
				$page = (int)$this->request->get['page'];
			} else {
				$page = 1;
			}
			
			if ((int)$this->config->get('gallery_setting_album_limit')) {
				$limit = (int)$this->config->get('gallery_setting_album_limit');
			} else {
				$limit = 20;
			}
			
			$data['breadcrumbs'] = [];

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home', '', true)
			);
			
			$url = '';			
				
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/mpphotogallery/album', $url, true)
			);				

			$this->load->model('tool/image');
			
			$data['gallerys'] = [];
			
			$filter_data = array(
				'start' => ($page - 1) * $limit,
				'limit' => $limit,
			);

			$data['text_photos'] = $this->language->get('text_photos');

			$gallery_total = $this->model_extension_mpphotogallery_album->getTotalGallerys($filter_data);
				
			$gallery_info = $this->model_extension_mpphotogallery_album->getGallery($filter_data);
			
			foreach ($gallery_info as $gallery) {			
				if ($gallery) {
					if ($gallery['image'] && file_exists(DIR_IMAGE . $gallery['image'])) {
						$image = $this->model_tool_image->resize($gallery['image'], (int)$this->config->get('gallery_setting_album_width'), (int)$this->config->get('gallery_setting_album_height'));
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', (int)$this->config->get('gallery_setting_album_width'), (int)$this->config->get('gallery_setting_album_height'));
					}

					$data['gallerys'][] = array(
						'gallery_id'  => $gallery['gallery_id'],
						'viewed'  	  => $gallery['viewed'],
						'image'       => $image,
						'title'        => $gallery['title'],
						'total_photos' => $this->model_extension_mpphotogallery_album->getTotalGalleryPhotos($gallery['gallery_id']),
						'description' => html_entity_decode($gallery['description'], ENT_QUOTES, 'UTF-8'),
						'href'        => $this->url->link('extension/mpphotogallery/photo', 'gallery_id=' . $gallery['gallery_id'] . $url)
					);
				}
			}
			
			$data['gallery_setting_color'] = $this->config->get('gallery_setting_color'); 
			
			$url = '';

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$pagination = new Pagination();
			$pagination->total = $gallery_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('extension/mpphotogallery/album', $url . '&page={page}');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($gallery_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($gallery_total - $limit)) ? $gallery_total : ((($page - 1) * $limit) + $limit), $gallery_total, ceil($gallery_total / $limit));
			
			$data['limit'] = $limit;

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('mpphotogallery/album', $data));
		}
	}
}