<?php
/* This file is under Git Control by KDSI. */
class ControllerMobileappImageSetting extends Controller {
	public function index() {
		$this->load->model('setting/setting');
		$this->load->language('mobileapp/image_setting');
		
		$this->load->model('catalog/category');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('restapi_default', $this->request->post, $this->request->get['store_id']);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('mobileapp/image_setting', 'user_token=' . $this->session->data['user_token'], true));
		}

		$setting_info = $this->model_setting_setting->getSetting('restapi_default', 0);

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['image_category'])) {
			$data['error_image_category'] = $this->error['image_category'];
		} else {
			$data['error_image_category'] = '';
		}

		if (isset($this->error['image_thumb'])) {
			$data['error_image_thumb'] = $this->error['image_thumb'];
		} else {
			$data['error_image_thumb'] = '';
		}

		if (isset($this->error['image_popup'])) {
			$data['error_image_popup'] = $this->error['image_popup'];
		} else {
			$data['error_image_popup'] = '';
		}

		if (isset($this->error['image_product'])) {
			$data['error_image_product'] = $this->error['image_product'];
		} else {
			$data['error_image_product'] = '';
		}

		if (isset($this->error['image_additional'])) {
			$data['error_image_additional'] = $this->error['image_additional'];
		} else {
			$data['error_image_additional'] = '';
		}

		if (isset($this->error['image_related'])) {
			$data['error_image_related'] = $this->error['image_related'];
		} else {
			$data['error_image_related'] = '';
		}

		if (isset($this->error['image_compare'])) {
			$data['error_image_compare'] = $this->error['image_compare'];
		} else {
			$data['error_image_compare'] = '';
		}

		if (isset($this->error['image_wishlist'])) {
			$data['error_image_wishlist'] = $this->error['image_wishlist'];
		} else {
			$data['error_image_wishlist'] = '';
		}

		if (isset($this->error['image_cart'])) {
			$data['error_image_cart'] = $this->error['image_cart'];
		} else {
			$data['error_image_cart'] = '';
		}

		if (isset($this->error['image_location'])) {
			$data['error_image_location'] = $this->error['image_location'];
		} else {
			$data['error_image_location'] = '';
		}

		if (isset($this->error['image_category_icon'])) {
			$data['error_image_category_icon'] = $this->error['image_category_icon'];
		} else {
			$data['error_image_category_icon'] = '';
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=theme', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('mobileapp/image_setting', 'user_token=' . $this->session->data['user_token'] , true)
		);

		$data['action'] = $this->url->link('mobileapp/image_setting', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('mobileapp/image_setting', 'user_token=' . $this->session->data['user_token'] , true);
		
		if (isset($this->request->post['restapi_default_directory'])) {
			$data['restapi_default_directory'] = $this->request->post['restapi_default_directory'];
		} elseif (isset($setting_info['restapi_default_directory'])) {
			$data['restapi_default_directory'] = $setting_info['restapi_default_directory'];
		} else {
			$data['restapi_default_directory'] = 'default';
		}		

		$data['directories'] = array();

		$directories = glob(DIR_CATALOG . 'view/theme/*', GLOB_ONLYDIR);

		foreach ($directories as $directory) {
			$data['directories'][] = basename($directory);
		}

		if (isset($this->request->post['restapi_default_product_limit'])) {
			$data['restapi_default_product_limit'] = $this->request->post['restapi_default_product_limit'];
		} elseif (isset($setting_info['restapi_default_product_limit'])) {
			$data['restapi_default_product_limit'] = $setting_info['restapi_default_product_limit'];
		} else {
			$data['restapi_default_product_limit'] = 15;
		}		
		
		if (isset($this->request->post['restapi_default_status'])) {
			$data['restapi_default_status'] = $this->request->post['restapi_default_status'];
		} elseif (isset($setting_info['restapi_default_status'])) {
			$data['restapi_default_status'] = $setting_info['restapi_default_status'];
		} else {
			$data['restapi_default_status'] = '';
		}
		
		if (isset($this->request->post['restapi_default_product_description_length'])) {
			$data['restapi_default_product_description_length'] = $this->request->post['restapi_default_product_description_length'];
		} elseif (isset($setting_info['restapi_default_product_description_length'])) {
			$data['restapi_default_product_description_length'] = $setting_info['restapi_default_product_description_length'];
		} else {
			$data['restapi_default_product_description_length'] = 100;
		}
		
		if (isset($this->request->post['restapi_default_image_category_width_mobile'])) {
			$data['restapi_default_image_category_width_mobile'] = $this->request->post['restapi_default_image_category_width_mobile'];
		} elseif (isset($setting_info['restapi_default_image_category_width_mobile'])) {
			$data['restapi_default_image_category_width_mobile'] = $setting_info['restapi_default_image_category_width_mobile'];
		} else {
			$data['restapi_default_image_category_width_mobile'] = 80;		
		}
		
		if (isset($this->request->post['restapi_default_image_category_height_mobile'])) {
			$data['restapi_default_image_category_height_mobile'] = $this->request->post['restapi_default_image_category_height_mobile'];
		} elseif (isset($setting_info['restapi_default_image_category_height_mobile'])) {
			$data['restapi_default_image_category_height_mobile'] = $setting_info['restapi_default_image_category_height_mobile'];
		} else {
			$data['restapi_default_image_category_height_mobile'] = 80;
		}
		
		if (isset($this->request->post['restapi_default_image_thumb_width_mobile'])) {
			$data['restapi_default_image_thumb_width_mobile'] = $this->request->post['restapi_default_image_thumb_width_mobile'];
		} elseif (isset($setting_info['restapi_default_image_thumb_width_mobile'])) {
			$data['restapi_default_image_thumb_width_mobile'] = $setting_info['restapi_default_image_thumb_width_mobile'];
		} else {
			$data['restapi_default_image_thumb_width_mobile'] = 228;
		}
		
		if (isset($this->request->post['restapi_default_image_thumb_height_mobile'])) {
			$data['restapi_default_image_thumb_height_mobile'] = $this->request->post['restapi_default_image_thumb_height_mobile'];
		} elseif (isset($setting_info['restapi_default_image_thumb_height_mobile'])) {
			$data['restapi_default_image_thumb_height_mobile'] = $setting_info['restapi_default_image_thumb_height_mobile'];
		} else {
			$data['restapi_default_image_thumb_height_mobile'] = 228;		
		}
		
		if (isset($this->request->post['restapi_default_image_popup_width_mobile'])) {
			$data['restapi_default_image_popup_width_mobile'] = $this->request->post['restapi_default_image_popup_width_mobile'];
		} elseif (isset($setting_info['restapi_default_image_popup_width_mobile'])) {
			$data['restapi_default_image_popup_width_mobile'] = $setting_info['restapi_default_image_popup_width_mobile'];
		} else {
			$data['restapi_default_image_popup_width_mobile'] = 500;
		}
		
		if (isset($this->request->post['restapi_default_image_popup_height_mobile'])) {
			$data['restapi_default_image_popup_height_mobile'] = $this->request->post['restapi_default_image_popup_height_mobile'];
		} elseif (isset($setting_info['restapi_default_image_popup_height_mobile'])) {
			$data['restapi_default_image_popup_height_mobile'] = $setting_info['restapi_default_image_popup_height_mobile'];
		} else {
			$data['restapi_default_image_popup_height_mobile'] = 500;
		}
		
		if (isset($this->request->post['restapi_default_image_product_width_mobile'])) {
			$data['restapi_default_image_product_width_mobile'] = $this->request->post['restapi_default_image_product_width_mobile'];
		} elseif (isset($setting_info['restapi_default_image_product_width_mobile'])) {
			$data['restapi_default_image_product_width_mobile'] = $setting_info['restapi_default_image_product_width_mobile'];
		} else {
			$data['restapi_default_image_product_width_mobile'] = 228;
		}
		
		if (isset($this->request->post['restapi_default_image_product_height_mobile'])) {
			$data['restapi_default_image_product_height_mobile'] = $this->request->post['restapi_default_image_product_height_mobile'];
		} elseif (isset($setting_info['restapi_default_image_product_height_mobile'])) {
			$data['restapi_default_image_product_height_mobile'] = $setting_info['restapi_default_image_product_height_mobile'];
		} else {
			$data['restapi_default_image_product_height_mobile'] = 228;
		}
		
		if (isset($this->request->post['restapi_default_image_additional_width_mobile'])) {
			$data['restapi_default_image_additional_width_mobile'] = $this->request->post['restapi_default_image_additional_width_mobile'];
		} elseif (isset($setting_info['restapi_default_image_additional_width_mobile'])) {
			$data['restapi_default_image_additional_width_mobile'] = $setting_info['restapi_default_image_additional_width_mobile'];
		} else {
			$data['restapi_default_image_additional_width_mobile'] = 74;
		}
		
		if (isset($this->request->post['restapi_default_image_additional_height_mobile'])) {
			$data['restapi_default_image_additional_height_mobile'] = $this->request->post['restapi_default_image_additional_height_mobile'];
		} elseif (isset($setting_info['restapi_default_image_additional_height_mobile'])) {
			$data['restapi_default_image_additional_height_mobile'] = $setting_info['restapi_default_image_additional_height_mobile'];
		} else {
			$data['restapi_default_image_additional_height_mobile'] = 74;
		}
		
		if (isset($this->request->post['restapi_default_image_related_width_mobile'])) {
			$data['restapi_default_image_related_width_mobile'] = $this->request->post['restapi_default_image_related_width_mobile'];
		} elseif (isset($setting_info['restapi_default_image_related_width_mobile'])) {
			$data['restapi_default_image_related_width_mobile'] = $setting_info['restapi_default_image_related_width_mobile'];
		} else {
			$data['restapi_default_image_related_width_mobile'] = 80;
		}
		
		if (isset($this->request->post['restapi_default_image_related_height_mobile'])) {
			$data['restapi_default_image_related_height_mobile'] = $this->request->post['restapi_default_image_related_height_mobile'];
		} elseif (isset($setting_info['restapi_default_image_related_height_mobile'])) {
			$data['restapi_default_image_related_height_mobile'] = $setting_info['restapi_default_image_related_height_mobile'];
		} else {
			$data['restapi_default_image_related_height_mobile'] = 80;
		}
		
		if (isset($this->request->post['restapi_default_image_compare_width_mobile'])) {
			$data['restapi_default_image_compare_width_mobile'] = $this->request->post['restapi_default_image_compare_width_mobile'];
		} elseif (isset($setting_info['restapi_default_image_compare_width_mobile'])) {
			$data['restapi_default_image_compare_width_mobile'] = $setting_info['restapi_default_image_compare_width_mobile'];
		} else {
			$data['restapi_default_image_compare_width_mobile'] = 90;
		}
		
		if (isset($this->request->post['restapi_default_image_compare_height_mobile'])) {
			$data['restapi_default_image_compare_height_mobile'] = $this->request->post['restapi_default_image_compare_height_mobile'];
		} elseif (isset($setting_info['restapi_default_image_compare_height_mobile'])) {
			$data['restapi_default_image_compare_height_mobile'] = $setting_info['restapi_default_image_compare_height_mobile'];
		} else {
			$data['restapi_default_image_compare_height_mobile'] = 90;
		}
		
		if (isset($this->request->post['restapi_default_image_wishlist_width_mobile'])) {
			$data['restapi_default_image_wishlist_width_mobile'] = $this->request->post['restapi_default_image_wishlist_width_mobile'];
		} elseif (isset($setting_info['restapi_default_image_wishlist_width_mobile'])) {
			$data['restapi_default_image_wishlist_width_mobile'] = $setting_info['restapi_default_image_wishlist_width_mobile'];
		} else {
			$data['restapi_default_image_wishlist_width_mobile'] = 47;
		}
		
		if (isset($this->request->post['restapi_default_image_wishlist_height_mobile'])) {
			$data['restapi_default_image_wishlist_height_mobile'] = $this->request->post['restapi_default_image_wishlist_height_mobile'];
		} elseif (isset($setting_info['restapi_default_image_wishlist_height_mobile'])) {
			$data['restapi_default_image_wishlist_height_mobile'] = $setting_info['restapi_default_image_wishlist_height_mobile'];
		} else {
			$data['restapi_default_image_wishlist_height_mobile'] = 47;
		}
		
		if (isset($this->request->post['restapi_default_image_cart_width_mobile'])) {
			$data['restapi_default_image_cart_width_mobile'] = $this->request->post['restapi_default_image_cart_width_mobile'];
		} elseif (isset($setting_info['restapi_default_image_cart_width_mobile'])) {
			$data['restapi_default_image_cart_width_mobile'] = $setting_info['restapi_default_image_cart_width_mobile'];
		} else {
			$data['restapi_default_image_cart_width_mobile'] = 47;
		}
		
		if (isset($this->request->post['restapi_default_image_cart_height_mobile'])) {
			$data['restapi_default_image_cart_height_mobile'] = $this->request->post['restapi_default_image_cart_height_mobile'];
		} elseif (isset($setting_info['restapi_default_image_cart_height_mobile'])) {
			$data['restapi_default_image_cart_height_mobile'] = $setting_info['restapi_default_image_cart_height_mobile'];
		} else {
			$data['restapi_default_image_cart_height_mobile'] = 47;
		}
		
		if (isset($this->request->post['restapi_default_image_location_width_mobile'])) {
			$data['restapi_default_image_location_width_mobile'] = $this->request->post['restapi_default_image_location_width_mobile'];
		} elseif (isset($setting_info['restapi_default_image_location_width_mobile'])) {
			$data['restapi_default_image_location_width_mobile'] = $setting_info['restapi_default_image_location_width_mobile'];
		} else {
			$data['restapi_default_image_location_width_mobile'] = 268;
		}
		
		if (isset($this->request->post['restapi_default_image_location_height_mobile'])) {
			$data['restapi_default_image_location_height_mobile'] = $this->request->post['restapi_default_image_location_height_mobile'];
		} elseif (isset($setting_info['restapi_default_image_location_height_mobile'])) {
			$data['restapi_default_image_location_height_mobile'] = $setting_info['restapi_default_image_location_height_mobile'];
		} else {
			$data['restapi_default_image_location_height_mobile'] = 50;
		}
		
		if (isset($this->request->post['restapi_default_image_category_icon_width_mobile'])) {
			$data['restapi_default_image_category_icon_width_mobile'] = $this->request->post['restapi_default_image_category_icon_width_mobile'];
		} elseif (isset($setting_info['restapi_default_image_category_icon_width_mobile'])) {
			$data['restapi_default_image_category_icon_width_mobile'] = $setting_info['restapi_default_image_category_icon_width_mobile'];
		} else {
			$data['restapi_default_image_category_icon_width_mobile'] = 50;
		}

		if (isset($this->request->post['restapi_default_image_category_icon_height_mobile'])) {
			$data['restapi_default_image_category_icon_height_mobile'] = $this->request->post['restapi_default_image_category_icon_height_mobile'];
		} elseif (isset($setting_info['restapi_default_image_category_icon_height_mobile'])) {
			$data['restapi_default_image_category_icon_height_mobile'] = $setting_info['restapi_default_image_category_icon_height_mobile'];
		} else {
			$data['restapi_default_image_category_icon_height_mobile'] = 50;
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('mobileapp/image_setting', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'mobileapp/image_setting')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['restapi_default_image_category_width_mobile'] || !$this->request->post['restapi_default_image_category_height_mobile']) {
			$this->error['image_category'] = $this->language->get('error_image_category');
		}

		if (!$this->request->post['restapi_default_image_thumb_width_mobile'] || !$this->request->post['restapi_default_image_thumb_height_mobile']) {
			$this->error['image_thumb'] = $this->language->get('error_image_thumb');
		}

		if (!$this->request->post['restapi_default_image_popup_width_mobile'] || !$this->request->post['restapi_default_image_popup_height_mobile']) {
			$this->error['image_popup'] = $this->language->get('error_image_popup');
		}

		if (!$this->request->post['restapi_default_image_product_width_mobile'] || !$this->request->post['restapi_default_image_product_height_mobile']) {
			$this->error['image_product'] = $this->language->get('error_image_product');
		}

		if (!$this->request->post['restapi_default_image_additional_width_mobile'] || !$this->request->post['restapi_default_image_additional_height_mobile']) {
			$this->error['image_additional'] = $this->language->get('error_image_additional');
		}

		if (!$this->request->post['restapi_default_image_related_width_mobile'] || !$this->request->post['restapi_default_image_related_height_mobile']) {
			$this->error['image_related'] = $this->language->get('error_image_related');
		}

		if (!$this->request->post['restapi_default_image_compare_width_mobile'] || !$this->request->post['restapi_default_image_compare_height_mobile']) {
			$this->error['image_compare'] = $this->language->get('error_image_compare');
		}

		if (!$this->request->post['restapi_default_image_wishlist_width_mobile'] || !$this->request->post['restapi_default_image_wishlist_height_mobile']) {
			$this->error['image_wishlist'] = $this->language->get('error_image_wishlist');
		}

		if (!$this->request->post['restapi_default_image_cart_width_mobile'] || !$this->request->post['restapi_default_image_cart_height_mobile']) {
			$this->error['image_cart'] = $this->language->get('error_image_cart');
		}

		if (!$this->request->post['restapi_default_image_location_width_mobile'] || !$this->request->post['restapi_default_image_location_height_mobile']) {
			$this->error['image_location'] = $this->language->get('error_image_location');
		}

		return !$this->error;
	}
}