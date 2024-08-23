<?php
/* This file is under Git Control by KDSI. */

class ControllerAccountDashboardAccountDashboard extends Controller
{
	private $error = array();

	public function index()
	{

		$this->load->language('account_dashboard/account_dashboard');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->addStyle('view/javascript/colorpicker/css/colorpicker.css');
		$this->document->addScript('view/javascript/colorpicker/js/colorpicker.js');

		$this->load->model('setting/setting');

		if (isset($this->request->get['store_id'])) {
			$store_id = $this->request->get['store_id'];
		} else {
			$store_id = 0;
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('account_dashboard', $this->request->post, $store_id);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('account_dashboard/account_dashboard', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

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

		if (isset($this->error['affiliate_title'])) {
			$data['error_affiliate_title'] = $this->error['affiliate_title'];
		} else {
			$data['error_affiliate_title'] = array();
		}

		if (isset($this->error['image'])) {
			$data['error_image'] = $this->error['image'];
		} else {
			$data['error_image'] = array();
		}

		if (isset($this->error['affiliate_image'])) {
			$data['error_affiliate_image'] = $this->error['affiliate_image'];
		} else {
			$data['error_affiliate_image'] = array();
		}

		if (isset($this->error['link'])) {
			$data['errorlink'] = $this->error['link'];
		} else {
			$data['errorlink'] = array();
		}

		if (isset($this->error['affiliate_link'])) {
			$data['error_affiliate_link'] = $this->error['affiliate_link'];
		} else {
			$data['error_affiliate_link'] = array();
		}

		if (isset($this->error['icon_class'])) {
			$data['error_icon_class'] = $this->error['icon_class'];
		} else {
			$data['error_icon_class'] = array();
		}

		if (isset($this->error['affiliate_icon_class'])) {
			$data['error_affiliate_icon_class'] = $this->error['affiliate_icon_class'];
		} else {
			$data['error_affiliate_icon_class'] = array();
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account_dashboard/account_dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['store_id'] = $store_id;
		if (isset($store_id)) {
			$data['action'] = $this->url->link('account_dashboard/account_dashboard', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $store_id, true);
		} else {
			$data['action'] = $this->url->link('account_dashboard/account_dashboard', 'user_token=' . $this->session->data['user_token'], true);
		}

		$this->load->model('setting/store');
		$data['stores'] = $this->model_setting_store->getStores();

		if ($this->request->server['REQUEST_METHOD'] != 'POST') {
			$module_info = $this->model_setting_setting->getSetting('account_dashboard',  $store_id);
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['account_dashboard_status'])) {
			$data['account_dashboard_status'] = $this->request->post['account_dashboard_status'];
		} elseif (!empty($module_info)) {
			$data['account_dashboard_status'] = $module_info['account_dashboard_status'];
		} else {
			$data['account_dashboard_status'] = '';
		}

		if (isset($this->request->post['account_dashboard_customer_picture_status'])) {
			$data['account_dashboard_customer_picture_status'] = $this->request->post['account_dashboard_customer_picture_status'];
		} elseif (!empty($module_info)) {
			$data['account_dashboard_customer_picture_status'] = $module_info['account_dashboard_customer_picture_status'];
		} else {
			$data['account_dashboard_customer_picture_status'] = '';
		}

		if (isset($this->request->post['account_dashboard_latest_orders'])) {
			$data['account_dashboard_latest_orders'] = $this->request->post['account_dashboard_latest_orders'];
		} elseif (!empty($module_info)) {
			$data['account_dashboard_latest_orders'] = $module_info['account_dashboard_latest_orders'];
		} else {
			$data['account_dashboard_latest_orders'] = 1;
		}

		if (isset($this->request->post['account_dashboard_total_wishlists'])) {
			$data['account_dashboard_total_wishlists'] = $this->request->post['account_dashboard_total_wishlists'];
		} elseif (!empty($module_info)) {
			$data['account_dashboard_total_wishlists'] = $module_info['account_dashboard_total_wishlists'];
		} else {
			$data['account_dashboard_total_wishlists'] = 1;
		}

		if (isset($this->request->post['account_dashboard_total_rewardpoints'])) {
			$data['account_dashboard_total_rewardpoints'] = $this->request->post['account_dashboard_total_rewardpoints'];
		} elseif (!empty($module_info)) {
			$data['account_dashboard_total_rewardpoints'] = $module_info['account_dashboard_total_rewardpoints'];
		} else {
			$data['account_dashboard_total_rewardpoints'] = 1;
		}

		if (isset($this->request->post['account_dashboard_total_orders'])) {
			$data['account_dashboard_total_orders'] = $this->request->post['account_dashboard_total_orders'];
		} elseif (!empty($module_info)) {
			$data['account_dashboard_total_orders'] = $module_info['account_dashboard_total_orders'];
		} else {
			$data['account_dashboard_total_orders'] = 1;
		}
		if (isset($this->request->post['account_dashboard_total_transactions'])) {
			$data['account_dashboard_total_transactions'] = $this->request->post['account_dashboard_total_transactions'];
		} elseif (!empty($module_info)) {
			$data['account_dashboard_total_transactions'] = $module_info['account_dashboard_total_transactions'];
		} else {
			$data['account_dashboard_total_transactions'] = 1;
		}
		if (isset($this->request->post['account_dashboard_theme_color'])) {
			$data['account_dashboard_theme_color'] = $this->request->post['account_dashboard_theme_color'];
		} elseif (!empty($module_info)) {
			$data['account_dashboard_theme_color'] = $module_info['account_dashboard_theme_color'];
		} else {
			$data['account_dashboard_theme_color'] = '';
		}

		if (isset($this->request->post['account_dashboard_text_color'])) {
			$data['account_dashboard_text_color'] = $this->request->post['account_dashboard_text_color'];
		} elseif (!empty($module_info)) {
			$data['account_dashboard_text_color'] = $module_info['account_dashboard_text_color'];
		} else {
			$data['account_dashboard_text_color'] = '';
		}

		if (isset($this->request->post['account_dashboard_customcss'])) {
			$data['account_dashboard_customcss'] = $this->request->post['account_dashboard_customcss'];
		} elseif (!empty($module_info)) {
			$data['account_dashboard_customcss'] = $module_info['account_dashboard_customcss'];
		} else {
			$data['account_dashboard_customcss'] = '';
		}

		if (isset($this->request->post['account_dashboard_file_ext_allowed'])) {
			$data['account_dashboard_file_ext_allowed'] = $this->request->post['account_dashboard_file_ext_allowed'];
		} elseif (!empty($module_info)) {
			$data['account_dashboard_file_ext_allowed'] = $module_info['account_dashboard_file_ext_allowed'];
		} else {
			$data['account_dashboard_file_ext_allowed'] = "png\njpe\njpeg\njpg\ngif\nbmp";
		}

		if (isset($this->request->post['account_dashboard_file_mime_allowed'])) {
			$data['account_dashboard_file_mime_allowed'] = $this->request->post['account_dashboard_file_mime_allowed'];
		} elseif (!empty($module_info)) {
			$data['account_dashboard_file_mime_allowed'] = $module_info['account_dashboard_file_mime_allowed'];
		} else {
			$data['account_dashboard_file_mime_allowed'] = "image/png\nimage/jpeg\nimage/gif\nimage/bmp";
		}

		if (isset($this->request->post['account_dashboard_template'])) {
			$data['account_dashboard_template'] = $this->request->post['account_dashboard_template'];
		} elseif (!empty($module_info)) {
			$data['account_dashboard_template'] = $module_info['account_dashboard_template'];
		} else {
			$data['account_dashboard_template'] = '';
		}


		if (isset($this->request->post['account_dashboard_link'])) {
			$links = $this->request->post['account_dashboard_link'];
		} elseif (!empty($module_info)) {
			$links = (!empty($module_info['account_dashboard_link'])) ? (array)$module_info['account_dashboard_link'] : array();
		} else {
			$links = array();
		}

		function linksSort($a, $b)
		{
			return $a['sort_order'] - $b['sort_order'];
		}


		uasort($links, 'linksSort');

		$this->load->model('tool/image');

		$data['links'] = array();

		foreach ($links as $key =>  $link) {

			if (is_file(DIR_IMAGE . $link['image'])) {
				$thumb = $this->model_tool_image->resize($link['image'], 100, 100);
			} else {
				$thumb = $this->model_tool_image->resize('no_image.png', 100, 100);
			}

			$data['links'][$key] = array(
				'description'		=> isset($link['description']) ?  $link['description'] : array(),
				'link'				=> isset($link['link']) ?  $link['link'] : '',
				'status'			=> isset($link['status']) ?  $link['status'] : '',
				'type'				=> isset($link['type']) ?  $link['type'] : '',
				'icon_class'		=> isset($link['icon_class']) ?  $link['icon_class'] : '',
				'sort_order'		=> isset($link['sort_order']) ?  $link['sort_order'] : '',
				'image'				=> isset($link['image']) ?  $link['image'] : '',
				'thumb'				=> $thumb,
			);
		}

		if (isset($this->request->post['account_dashboard_affiliate_link'])) {
			$affiliate_links = $this->request->post['account_dashboard_affiliate_link'];
		} elseif (!empty($module_info)) {
			$affiliate_links = (!empty($module_info['account_dashboard_affiliate_link'])) ? (array)$module_info['account_dashboard_affiliate_link'] : array();
		} else {
			$affiliate_links = array();
		}

		function linksAffiliateSort($a, $b)
		{
			return $a['sort_order'] - $b['sort_order'];
		}


		uasort($affiliate_links, 'linksAffiliateSort');

		$this->load->model('tool/image');

		$data['affiliate_links'] = array();

		foreach ($affiliate_links as $key =>  $affiliate_link) {

			if (is_file(DIR_IMAGE . $affiliate_link['image'])) {
				$thumb = $this->model_tool_image->resize($affiliate_link['image'], 100, 100);
			} else {
				$thumb = $this->model_tool_image->resize('no_image.png', 100, 100);
			}

			$data['affiliate_links'][$key] = array(
				'description'		=> isset($affiliate_link['description']) ?  $affiliate_link['description'] : array(),
				'link'				=> isset($affiliate_link['link']) ?  $affiliate_link['link'] : '',
				'status'			=> isset($affiliate_link['status']) ?  $affiliate_link['status'] : '',
				'affiliate_type'	=> isset($affiliate_link['affiliate_type']) ?  $affiliate_link['affiliate_type'] : '',
				'type'				=> isset($affiliate_link['type']) ?  $affiliate_link['type'] : '',
				'icon_class'		=> isset($affiliate_link['icon_class']) ?  $affiliate_link['icon_class'] : '',
				'sort_order'		=> isset($affiliate_link['sort_order']) ?  $affiliate_link['sort_order'] : '',
				'image'				=> isset($affiliate_link['image']) ?  $affiliate_link['image'] : '',
				'thumb'				=> $thumb,
			);
		}


		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$this->load->model('tool/image');
		$data['templates'] = array();
		$data['templates'][] = array(
			'template_id'		=> 'template_1',
			'name'				=> $this->language->get('text_template_1'),
		);

		$data['templates'][] = array(
			'template_id'		=> 'template_2',
			'name'				=> $this->language->get('text_template_2'),
		);

		$data['templates'][] = array(
			'template_id'		=> 'template_3',
			'name'				=> $this->language->get('text_template_3'),
		);

		$data['config_language_id'] = $this->config->get('config_language_id');

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('account_dashboard/account_dashboard', $data));
	}

	protected function validate()
	{
		if (!$this->user->hasPermission('modify', 'account_dashboard/account_dashboard')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (isset($this->request->post['account_dashboard_link'])) {

			foreach ($this->request->post['account_dashboard_link'] as $row => $description) {

				foreach ($description['description'] as $language_id => $value) {
					if ((utf8_strlen($value['title']) < 1) || (utf8_strlen($value['title']) > 128)) {
						$this->error['title'][$row][$language_id] = $this->language->get('error_title');
					}
				}


				if (!$description['link']) {
					$this->error['link'][$row] = $this->language->get('error_link');
				}

				if ($description['type'] == 'image') {
					if (empty($description['image'])) {
						$this->error['image'][$row] = $this->language->get('error_image');
					}
				} else {
					if (empty($description['icon_class'])) {
						$this->error['icon_class'][$row] = $this->language->get('error_icon_class');
					}
				}
			}
		}

		if (isset($this->request->post['account_dashboard_affiliate_link'])) {

			foreach ($this->request->post['account_dashboard_affiliate_link'] as $row => $affiliate_description) {

				foreach ($affiliate_description['description'] as $language_id => $affiliate_value) {
					if ((utf8_strlen($affiliate_value['title']) < 1) || (utf8_strlen($affiliate_value['title']) > 128)) {
						$this->error['affiliate_title'][$row][$language_id] = $this->language->get('error_title');
					}
				}


				if (!$affiliate_description['link']) {
					$this->error['affiliate_link'][$row] = $this->language->get('error_link');
				}

				if ($affiliate_description['type'] == 'image') {
					if (empty($affiliate_description['image'])) {
						$this->error['affiliate_image'][$row] = $this->language->get('error_image');
					}
				} else {
					if (empty($affiliate_description['icon_class'])) {
						$this->error['affiliate_icon_class'][$row] = $this->language->get('error_icon_class');
					}
				}
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	public function template()
	{
		if ($this->request->server['HTTPS']) {
			$server = HTTPS_CATALOG;
		} else {
			$server = HTTP_CATALOG;
		}

		if (isset($this->request->get['template'])) {
			$template = $this->request->get['template'];
		} else {
			$template = '';
		}

		if (is_file(DIR_IMAGE . 'account_dashboard/' . $template . '.png')) {
			$this->response->setOutput($server . 'image/account_dashboard/' . $template . '.png');
		} else {
			$this->response->setOutput($server . 'image/no_image.png');
		}
	}
}