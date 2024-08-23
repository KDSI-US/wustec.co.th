<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionPastorColumn extends Controller
{
	private $error = array();

	public function index()
	{
		$this->language->load('extension/pastor_column');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/pastor_column');

		$this->getList();
	}

	public function add()
	{
		$this->language->load('extension/pastor_column');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/pastor_column');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_pastor_column->addPastorColumn($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/pastor_column', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit()
	{
		$this->language->load('extension/pastor_column');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/pastor_column');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_pastor_column->editPastorColumn($this->request->get['pastor_column_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/pastor_column', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->language->load('extension/pastor_column');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/pastor_column');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $pastor_column_id) {
				$this->model_extension_pastor_column->deletePastorColumn($pastor_column_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/pastor_column', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList()
	{
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'vg.pastor_column_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/pastor_column', 'user_token=' . $this->session->data['user_token'] . $url, true),
			'separator' => ' :: '
		);

		$data['add'] = $this->url->link('extension/pastor_column/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/pastor_column/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['pastor_columns'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

                $this->load->model('tool/image');
		$pastor_column_total = $this->model_extension_pastor_column->getTotalPastorColumns($data);
		$results = $this->model_extension_pastor_column->getPastorColumns($filter_data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('extension/pastor_column/edit', 'user_token=' . $this->session->data['user_token'] . '&pastor_column_id=' . $result['pastor_column_id'] . $url, true)
			);

			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $result['image'];
				$thumb = $result['image'];
			} else {
				$image = '';
				$thumb = 'no_image.png';
			}

			$data['pastor_columns'][] = array(
				'pastor_column_id'  => $result['pastor_column_id'],
				'image'             => $image,
                                'thumb'             => $this->model_tool_image->resize($thumb, 100, 100),
				'video_url'         => $result['video_url'],
				'title'             => $result['title'],
				'description'       => mb_strimwidth(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')),0,80,'...','utf-8'),
				'hit'               => $result['hit'],
				'added_by'          => $result['user_id'],
				'added_at'          => $result['added_at'],
				'status'            => ($result['status']) ? "<div style=\"color:green;\">Enabled</div>" : "<div style=\"color:red;\">Disabled</div>",
				'sort_order'        => $result['sort_order'],
				'selected'          => isset($this->request->post['selected']) && in_array($result['pastor_column_id'], $this->request->post['selected']),
				'action'            => $action,
				'edit'              => $this->url->link('extension/pastor_column/edit', 'user_token=' . $this->session->data['user_token'] . '&pastor_column_id=' . $result['pastor_column_id'] . $url, true),
				'delete'            => $this->url->link('extension/pastor_column/delete', 'user_token=' . $this->session->data['user_token'] . '&pastor_column_id=' . $result['pastor_column_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['column_title'] = $this->language->get('column_title');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_action'] = $this->language->get('column_action');
		$data['text_list'] = $this->language->get('text_list');
		$data['button_add'] = $this->language->get('button_add');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['text_confirm'] = $this->language->get('text_confirm');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_title'] = $this->url->link('extension/pastor_column', 'user_token=' . $this->session->data['user_token'] . '&sort=vgd.title' . $url, true);
		$data['sort_pastor_column_id'] = $this->url->link('extension/pastor_column', 'user_token=' . $this->session->data['user_token'] . '&sort=vg.pastor_column_id' . $url, true);
		$data['sort_sort_order'] = $this->url->link('extension/pastor_column', 'user_token=' . $this->session->data['user_token'] . '&sort=i.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $pastor_column_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/pastor_column', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pastor_column_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($pastor_column_total - $this->config->get('config_limit_admin'))) ? $pastor_column_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $pastor_column_total, ceil($pastor_column_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/pastor_column_list', $data));
	}

	protected function getForm()
	{
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_form'] = !isset($this->request->get['pastor_column_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_browse'] = $this->language->get('text_browse');
		$data['text_clear'] = $this->language->get('text_clear');
		$data['text_image_manager'] = $this->language->get('text_image_manager');
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_video_image'] = $this->language->get('entry_video_image');
		$data['entry_url'] = $this->language->get('entry_url');
		$data['entry_keyword'] = $this->language->get('entry_keyword');
		$data['entry_meta_description'] = $this->language->get('entry_meta_description');
		$data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['button_getdata'] = $this->language->get('button_getdata');
		$data['entry_category'] = $this->language->get('entry_category');
		$data['help_keyword'] = $this->language->get('help_keyword');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_data'] = $this->language->get('tab_data');

		$data['entry_pastor_column_title'] = $this->language->get('entry_pastor_column_title');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['entry_pastor_column_desc'] = $this->language->get('entry_pastor_column_desc');
		$data['entry_pastor_column_img'] = $this->language->get('entry_pastor_column_img');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		} else {
			$data['error_title'] = array();
		}

		if (isset($this->error['video_url'])) {
			$data['error_url'] = $this->error['video_url'];
		} else {
			$data['error_url'] = array();
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/pastor_column', 'user_token=' . $this->session->data['user_token'] . $url, true),
			'separator' => ' :: '
		);

		if (!isset($this->request->get['pastor_column_id'])) {
			$data['action'] = $this->url->link('extension/pastor_column/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/pastor_column/edit', 'user_token=' . $this->session->data['user_token'] . '&pastor_column_id=' . $this->request->get['pastor_column_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('extension/pastor_column', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['pastor_column_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$pastor_column_info = $this->model_extension_pastor_column->getPastorColumn($this->request->get['pastor_column_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['pastor_column_description'])) {
			$data['pastor_column_description'] = $this->request->post['pastor_column_description'];
		} elseif (isset($this->request->get['pastor_column_id'])) {
			$data['pastor_column_description'] = $this->model_extension_pastor_column->getPastorColumnDescriptions($this->request->get['pastor_column_id']);
		} else {
			$data['pastor_column_description'] = array();
		}

		/* Pastor Column Categories */
		$this->load->model('extension/pastor_column_category');

		if (isset($this->request->post['pastor_column_categories'])) {
			$categories = $this->request->post['pastor_column_categories'];
		} elseif (isset($this->request->get['pastor_column_id'])) {
			$pastor_column_id = $this->request->get['pastor_column_id'];
			$categories = $this->model_extension_pastor_column_category->getPastorColumnCategoryByID($pastor_column_id);
		} else {
			$categories = array();
		}

		$data['pastor_column_categories'] = array();

		if ($categories) {
			foreach ($categories as $category) {
				$data['pastor_column_categories'][] = array(
					'pastor_column_category_id' => $category['pastor_column_category_id'],
					'title'			    => $category['title']
				);
			}
		}

		if (isset($this->request->post['video_url'])) {
			$data['video_url'] = $this->request->post['video_url'];
		} elseif (!empty($pastor_column_info)) {
			$data['video_url'] = $pastor_column_info['video_url'];
		} else {
			$data['video_url'] = '';
		}

		if (isset($this->request->post['keyword'])) {
			$data['keyword'] = $this->request->post['keyword'];
		} elseif (!empty($pastor_column_info)) {
			$data['keyword'] = $pastor_column_info['keyword'];
		} else {
			$data['keyword'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($pastor_column_info)) {
			$data['status'] = $pastor_column_info['status'];
		} else {
			$data['status'] = 1;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($pastor_column_info)) {
			$data['sort_order'] = $pastor_column_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		if (isset($this->request->post['chk_title'])) {
			$data['chk_title'] = $this->request->post['chk_title'];
		} elseif (!empty($pastor_column_info)) {
			$data['chk_title'] = $pastor_column_info['chk_title'];
		} else {
			$data['chk_title'] = 1;
		}

		if (isset($this->request->post['chk_description'])) {
			$data['chk_description'] = $this->request->post['chk_description'];
		} elseif (!empty($pastor_column_info)) {
			$data['chk_description'] = $pastor_column_info['chk_description'];
		} else {
			$data['chk_description'] = 1;
		}

		if (isset($this->request->post['chk_image'])) {
			$data['chk_image'] = $this->request->post['chk_image'];
		} elseif (!empty($pastor_column_info)) {
			$data['chk_image'] = $pastor_column_info['chk_image'];
		} else {
			$data['chk_image'] = 1;
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image'])) {
                        $data['image_path'] = $this->request->post['image'];
		} elseif (!empty($pastor_column_info) && $pastor_column_info['image']) {
                        $data['image_path'] = $pastor_column_info['image'];
		} else {
                        $data['image_path'] = 'no_image.png';
		}
                $data['image'] = $this->model_tool_image->resize($data['image_path'], 100, 100);

		if (isset($this->request->post['video_image'])) {
			$data['video_image_path'] = $this->request->post['video_image'];
		} elseif (!empty($pastor_column_info) && $pastor_column_info['video_image']) {
			$data['video_image_path'] = $pastor_column_info['video_image'];
		} else {
			$data['video_image_path'] = 'no_image.png';
		}

		if (isset($this->request->post['data_images'])) {
                        $data_images = $this->request->post['data_images'];
                } elseif (isset($this->request->get['pastor_column_id'])) {
                        $data_images = $this->model_extension_pastor_column->getDataImages($this->request->get['pastor_column_id']);
                } else {
                        $data_images = array();
                }

                $data['data_images'] = array();
                foreach ($data_images as $data_image) {
                        if (is_file(DIR_IMAGE . $data_image['image'])) {
                                $image = $data_image['image'];
                                $thumb = $data_image['image'];
                        } else {
                                $image = '';
                                $thumb = 'no_image.png';
                        }
                        $data['data_images'][] = array(
                                'image'       => $image,
                                'thumb'       => $this->model_tool_image->resize($thumb, 100, 100),
                                'description' => $data_image['description'],
                                'sort_order'  => $data_image['sort_order']
                        );
                }

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/pastor_column_form', $data));
	}

	protected function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'extension/pastor_column')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->post['video_url'])) {
			$this->error['video_url'] = $this->language->get('error_url');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	protected function validateDelete()
	{
		if (!$this->user->hasPermission('modify', 'extension/pastor_column')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	public function getPastorColumnThumb()
	{
		$json = array();
		if (isset($this->request->get['url'])) {
			$url = $this->request->get['url'];
			$data = $this->get_youtube_data($this->get_youtube_id($url));
			$json = $data;
		}
		$this->response->setOutput(json_encode($json));
	}

	private function get_youtube_data($video_id)
	{
		$youtube_key = $this->config->get('config_pastor_column_youtube_key');
		$output = array();
		if (!empty($youtube_key)) {
			$youtube_url = "https://www.googleapis.com/youtube/v3/videos?id=" . $video_id . "&key=" . $youtube_key . "&part=snippet,contentDetails,statistics,status";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $youtube_url);
			curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$response = curl_exec($ch);
			$err = curl_error($ch);
			curl_close($ch);
			$temp_output =  json_decode($response);
			$output['thumb'] = $temp_output->items['0']->snippet->thumbnails->medium->url;
			$output['title'] = $temp_output->items['0']->snippet->title;
			$output['description'] = $temp_output->items['0']->snippet->description;
		} else {
			$output['error'] = 'Please Fill your Youtube API key. <b><a href="https://console.cloud.google.com/apis/api/youtube.googleapis.com/credentials">Click here to go to Youtube Developer Console.</a></b>';
		}
		return $output;
	}

	private function get_youtube_id($url)
	{
		$return = explode('=', $url);
		$return = 	$return[1];

		return $return;
	}
}
