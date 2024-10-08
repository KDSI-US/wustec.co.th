<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionMpPhotoGalleryAlbumphoto extends Controller {
	private $error = [];

	public function index() {
		if ($this->config->get('gallery_setting_status')) {
			$this->load->language('mpphotogallery/album_photo');

			$this->load->model('extension/mpphotogallery/photo');

			$this->load->model('extension/mpphotogallery/album');

			$this->load->model('tool/image');

			$this->document->setTitle($this->language->get('heading_title'));

			$this->document->addStyle('catalog/view/javascript/mpphotogallery/assets/style.css');
			$this->document->addStyle('catalog/view/javascript/mpphotogallery/assets/smgallery/css/lightgallery.css');
			$this->document->addScript('catalog/view/javascript/mpphotogallery/assets/smgallery/js/lightgallery-all.js');
			$this->document->addScript('catalog/view/javascript/mpphotogallery/assets/smgallery/js/jquery.mousewheel.min.js');

			if ($this->request->server['HTTPS']) {
				$server = $this->config->get('config_ssl');
			} else {
				$server = $this->config->get('config_url');
			}

			if (isset($this->request->get['page']) && (int)$this->request->get['page']) {
				$page = (int)$this->request->get['page'];
			} else {
				$page = 1;
			}

			if ((int)$this->config->get('gallery_setting_albumphoto_limit')) {
				$limit = (int)$this->config->get('gallery_setting_albumphoto_limit');
			} else {
				$limit = 20;
			}

			$data['heading_title'] = $this->language->get('heading_title');
			$data['text_no_results'] = $this->language->get('text_no_results');

			$data['display_description'] = $this->config->get('gallery_setting_albumphoto_description');
			$data['cursive_font'] = $this->config->get('gallery_setting_albumphoto_cursive_font');

			$data['breadcrumbs'] = [];
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			);

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/mpphotogallery/album_photo', $url)
			);

			$this->load->model('tool/image');

			$data['gallerys'] = [];

			$filter_data = array(
				'start' => ($page - 1) * $limit,
				'limit' => $limit,
			);

			$data['gallerys'] = [];

			$gallery_total = $this->model_extension_mpphotogallery_photo->getTotalGallerys($filter_data);
			$gallery_info = $this->model_extension_mpphotogallery_photo->getGallery($filter_data);

			// 07-05-2022: updation task start
			$popup_width = 500;
			if ((int)$this->config->get('gallery_setting_popup_width')) {
				$popup_width = (int)$this->config->get('gallery_setting_popup_width');
			}
			$popup_height = 729;
			if ((int)$this->config->get('gallery_setting_popup_height')) {
				$popup_height = (int)$this->config->get('gallery_setting_popup_height');
			}
			// 07-05-2022: updation task end

			foreach ($gallery_info as $album) {
				// 07-05-2022: updation task start
				if (!$album['width']) {
					$album['width'] = (int)$this->config->get('gallery_setting_albumphoto_width');
				}
				if (!$album['width']) {
					$album['width'] = 213;
				}
				if (!$album['height']) {
					$album['height'] = (int)$this->config->get('gallery_setting_albumphoto_height');
				}
				if (!$album['height']) {
					$album['height'] = 310;
				}
				// 07-05-2022: updation task end
				$gallery_photos = $this->model_extension_mpphotogallery_photo->getAlbumPhotoDescription($album['gallery_id']);
				$photos = [];
				$highlight = false;
				foreach ($gallery_photos as $key => $gallery_photo) {
					if ($key == 0) {
						if ($gallery_photo['photo'] && file_exists(DIR_IMAGE . $gallery_photo['photo'])) {
							$image = $this->model_tool_image->resize($gallery_photo['photo'], $popup_width, $popup_height);
							$popup = $this->model_tool_image->resize($gallery_photo['photo'], $popup_width, $popup_height);
						} else {
							$image = $this->model_tool_image->resize('no_image.png', $popup_width, $popup_height);
							$popup = $this->model_tool_image->resize('no_image.png', $popup_width, $popup_height);
						}
					} else {
						if ($gallery_photo['photo'] && file_exists(DIR_IMAGE . $gallery_photo['photo'])) {
							$image = $this->model_tool_image->resize($gallery_photo['photo'], $album['width'], $album['height']);
							$popup = $this->model_tool_image->resize($gallery_photo['photo'], $popup_width, $popup_height);
						} else {
							$image = $this->model_tool_image->resize('no_image.png', $album['width'], $album['height']);
							$popup = $this->model_tool_image->resize('no_image.png', $popup_width, $popup_height);
						}
					}

					$photos[] = array(
						'image'		=> $image,
						'popup'		=> $popup,
						'name'		=> $gallery_photo['name'],
						'link'		=> $gallery_photo['link'],
					);
				}

				$data['gallerys'][] = array(
					'gallery_id'   		=> $album['gallery_id'],
					'title'        		=> $album['title'],
					'top_description'   => html_entity_decode($album['top_description'], ENT_QUOTES, 'UTF-8'),
					'photos'       		=> $photos,
				);
			}

			$data['gallery_setting_color'] = $this->config->get('gallery_setting_color');

			$url = '';

			$pagination = new Pagination();
			$pagination->total = $gallery_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('extension/mpphotogallery/album_photo', $url . '&page={page}');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($gallery_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($gallery_total - $limit)) ? $gallery_total : ((($page - 1) * $limit) + $limit), $gallery_total, ceil($gallery_total / $limit));

			$data['limit'] = $limit;

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('mpphotogallery/album_photo', $data));
		}
	}
}