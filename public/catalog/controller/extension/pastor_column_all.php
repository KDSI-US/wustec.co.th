<?php
class ControllerExtensionPastorColumnAll extends Controller
{
	public function index()
	{
		$this->language->load('extension/pastor_column_all');

		$this->document->addStyle('catalog/view/javascript/jquery/prettyphoto/prettyPhoto.css');
		$this->document->addScript('catalog/view/javascript/jquery/prettyphoto/jquery.prettyPhoto.js');

		$this->load->model('extension/pastor_column');
		$this->load->model('extension/pastor_column_category');

		$this->load->model('tool/image');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
			'separator' => false
		);

                if (isset($this->request->get['limit'])) {
                        $limit = $this->request->get['limit'];
                } else if ($this->config->get('config_limit_catalog') != '') {
                        $limit = $this->config->get('config_limit_catalog');
                } else {
                        $limit = "20";
                }

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['pastor_column_category_id'])) {
			$url .= '&pastor_column_category_id=' . $this->request->get['pastor_column_category_id'];
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

		$data['pastor_columns'] = array();

		if (isset($this->request->get['pastor_column_category_id'])) {
			$pastor_column_category_id = $this->request->get['pastor_column_category_id'];
			$data['category_id'] = $pastor_column_category_id;
		} else {
			$pastor_column_category_id = '';
		}

		$pastor_column_category = $this->model_extension_pastor_column->getPastorColumnCategoryData($pastor_column_category_id);
		if ($pastor_column_category) {
			$data['breadcrumbs'][] = array(
				'text'      => $pastor_column_category['title'],
				'href'      => $this->url->link('extension/pastor_column_all')

			);
			$data['heading_title'] = $pastor_column_category['title'];
			$this->document->setTitle($pastor_column_category['title']);

			$filter_data = array(
				'pastor_column_category_id' => $this->request->get['pastor_column_category_id'],
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit
			);

			$pastor_total = $this->model_extension_pastor_column->getTotalPastorColumns($filter_data);

			if (isset($this->request->get['pastor_column_id'])) {
				$data['pastor_column_id'] = $this->request->get['pastor_column_id'];
				$cData = $this->model_extension_pastor_column->getPastorColumnById($data['pastor_column_id']);
				$data['current_data'] = array(
					'image'       => ($cData['image']) ? $cData['image'] : 'no_image.png',
					'title'       => trim(strip_tags(html_entity_decode($cData['title'], ENT_QUOTES, 'UTF-8'))),
					'description' => trim(html_entity_decode($cData['description'], ENT_QUOTES, 'UTF-8')) 
				);
			}

			$pastor_column_data = $this->model_extension_pastor_column->getPastorColumnByCategory($filter_data);
			foreach ($pastor_column_data as $pastor_column) {
				$tempUrl = explode('=',$pastor_column['video_url']);
				$video_url_key = $tempUrl[1];
				$data['pastor_columns'][] = array(
					'pastor_column_id'  => $pastor_column['pastor_column_id'],
					'image'             => ($pastor_column['image']) ? $pastor_column['image'] : 'no_image.png',
					'video_image'       => $pastor_column['video_image'],
					'video_url'         => $pastor_column['video_url'] . '&feature=related',
					'url_key'           => $video_url_key,
					'title'       => trim(strip_tags(html_entity_decode($pastor_column['title'], ENT_QUOTES, 'UTF-8'))),
					'description' => trim(html_entity_decode($pastor_column['description'], ENT_QUOTES, 'UTF-8')) 
				);
			}

			$pagination = new Pagination();
			$pagination->total = $pastor_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('extension/pastor_column_all', '=' .  $url . '&page={page}');

			$data['pagination'] = $pagination->render();
			$data['results'] = sprintf($this->language->get('text_pagination'), ($pastor_total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($pastor_total - 5)) ? $pastor_total : ((($page - 1) * 5) + 5), $pastor_total, ceil($pastor_total / 5));
			$data['continue'] = $this->url->link('common/home');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('extension/pastor_column_all', $data));

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
				'text'      => 'Pastor Column Categories',
				'href'      => $this->url->link('extension/pastor_column_all', $url),

			);

			$this->document->setTitle($this->language->get('heading_title'));

			$data['heading_title'] = $this->language->get('heading_title');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['continue'] = $this->url->link('common/home');
			$filter_data = array(
				'pastor_column_category_id' => '',
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit
			);

			$data['pastor_column_category'] = array();
			$pastor_column_categories = $this->model_extension_pastor_column_category->getPastorColumnCategories($filter_data);

			foreach ($pastor_column_categories as $pastor_column_category) {
				$data['pastor_column_categories'][] = array(
					'category_id'  => $pastor_column_category['pastor_column_category_id'],
					'title'        => $pastor_column_category['title'],
					'image'        => "/image/".$pastor_column_category['image'],
					'description'  => html_entity_decode($pastor_column_category['description']),
					'href'         => $this->url->link('extension/pastor_column_all' . '&pastor_column_category_id=' . $pastor_column_category['pastor_column_category_id'])
				);
			}

			$pastor_column_category_total = $this->model_extension_pastor_column_category->getTotalPastorColumnCategory();

			$pagination = new Pagination();
			$pagination->total = $pastor_column_category_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('extension/pastor_column_all', '=' .  $url . '&page={page}');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($pastor_column_category_total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($pastor_column_category_total - 5)) ? $pastor_column_category_total : ((($page - 1) * 5) + 5), $pastor_column_category_total, ceil($pastor_column_category_total / 5));

			$data['continue'] = $this->url->link('common/home');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('extension/pastor_column_category', $data));
		}
	}
}
