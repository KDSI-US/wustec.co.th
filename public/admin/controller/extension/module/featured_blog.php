<?php
class ControllerExtensionModuleFeaturedBlog extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/featured_blog');

		$this->document->setTitle($this->language->get('heading_title1'));

		$this->load->model('setting/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('featured_blog', $this->request->post);
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$data['heading_title'] = $this->language->get('heading_title1');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_post'] = $this->language->get('entry_post');
		$data['entry_limit'] = $this->language->get('entry_limit');
		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_height'] = $this->language->get('entry_height');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_listing_layout'] = $this->language->get('entry_listing_layout');
		$data['entry_blog_per_row'] = $this->language->get('entry_blog_per_row');
		$data['entry_show_slider'] = $this->language->get('entry_show_slider');
		$data['entry_comment'] = $this->language->get('entry_comment');
		$data['entry_author'] = $this->language->get('entry_author');
		$data['entry_publish'] = $this->language->get('entry_publish');
		$data['entry_likes'] = $this->language->get('entry_likes');
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_viewed'] = $this->language->get('entry_viewed');
		$data['entry_short_description'] = $this->language->get('entry_short_description');
		$data['entry_title'] = $this->language->get('entry_title');
		

		$data['help_post'] = $this->language->get('help_post');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['width'])) {
			$data['error_width'] = $this->error['width'];
		} else {
			$data['error_width'] = '';
		}

		if (isset($this->error['height'])) {
			$data['error_height'] = $this->error['height'];
		} else {
			$data['error_height'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/featured_blog', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/featured_blog', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/featured_blog', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/featured_blog', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}

		$this->load->model('extension/post');

		$data['posts'] = array();

		if (isset($this->request->post['post'])) {
			$posts = $this->request->post['post'];
		} elseif (!empty($module_info)) {
			$posts = $module_info['post'];
		} else {
			$posts = array();
		}

		foreach ($posts as $post_id) {
			$post_info = $this->model_extension_post->getpost($post_id);

			if ($post_info) {
				$data['posts'][] = array(
					'post_id' => $post_info['post_id'],
					'name'       => $post_info['name']
				);
			}
		}

		if (isset($this->request->post['limit'])) {
			$data['limit'] = $this->request->post['limit'];
		} elseif (!empty($module_info)) {
			$data['limit'] = $module_info['limit'];
		} else {
			$data['limit'] = 5;
		}
		
		if (isset($this->request->post['limit_row'])) {
			$data['limit_row'] = $this->request->post['limit_row'];
		} elseif (!empty($module_info)) {
			$data['limit_row'] = $module_info['limit_row'];
		} else {
			$data['limit_row'] = 5;
		}

		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($module_info)) {
			$data['width'] = $module_info['width'];
		} else {
			$data['width'] = 200;
		}

		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($module_info)) {
			$data['height'] = $module_info['height'];
		} else {
			$data['height'] = 200;
		}

		if (isset($this->request->post['show_slider'])) {
			$data['show_slider'] = $this->request->post['show_slider'];
		} elseif (!empty($module_info['show_slider'])) {
			$data['show_slider'] = $module_info['show_slider'];
		} else {
			$data['show_slider'] = '';
		}
		
		if (isset($this->request->post['show_comment'])) {
			$data['show_comment'] = $this->request->post['show_comment'];
		} elseif (!empty($module_info['show_comment'])) {
			$data['show_comment'] = $module_info['show_comment'];
		} else {
			$data['show_comment'] = '';
		}
		
		if (isset($this->request->post['show_author'])) {
			$data['show_author'] = $this->request->post['show_author'];
		} elseif (!empty($module_info['show_author'])) {
			$data['show_author'] = $module_info['show_author'];
		} else {
			$data['show_author'] = '';
		}
		
		if (isset($this->request->post['show_publish'])) {
			$data['show_publish'] = $this->request->post['show_publish'];
		} elseif (!empty($module_info['show_publish'])) {
			$data['show_publish'] = $module_info['show_publish'];
		} else {
			$data['show_publish'] = '';
		}
		
		if (isset($this->request->post['show_like'])) {
			$data['show_like'] = $this->request->post['show_like'];
		} elseif (!empty($module_info['show_like'])) {
			$data['show_like'] = $module_info['show_like'];
		} else {
			$data['show_like'] = '';
		}
		
		if (isset($this->request->post['show_viewed'])) {
			$data['show_viewed'] = $this->request->post['show_viewed'];
		} elseif (!empty($module_info['show_viewed'])) {
			$data['show_viewed'] = $module_info['show_viewed'];
		} else {
			$data['show_viewed'] = '';
		}
		
		if (isset($this->request->post['show_image'])) {
			$data['show_image'] = $this->request->post['show_image'];
		} elseif (!empty($module_info['show_image'])) {
			$data['show_image'] = $module_info['show_image'];
		} else {
			$data['show_image'] = '';
		}
		
		if (isset($this->request->post['show_title'])) {
			$data['show_title'] = $this->request->post['show_title'];
		} elseif (!empty($module_info['show_title'])) {
			$data['show_title'] = $module_info['show_title'];
		} else {
			$data['show_title'] = '';
		}
		
		if (isset($this->request->post['show_short_description'])) {
			$data['show_short_description'] = $this->request->post['show_short_description'];
		} elseif (!empty($module_info['show_short_description'])) {
			$data['show_short_description'] = $module_info['show_short_description'];
		} else {
			$data['show_short_description'] = '';
		}
		
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info['status'])) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/featured_blog', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/featured_blog')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (!$this->request->post['width']) {
			$this->error['width'] = $this->language->get('error_width');
		}

		if (!$this->request->post['height']) {
			$this->error['height'] = $this->language->get('error_height');
		}

		return !$this->error;
	}
}