<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiCommonHome extends OcrestapiController {
	public function index() {
		if($this->request->server['REQUEST_METHOD'] == 'GET') {
		if($this->validateToken(true)) {
			$this->checkPlugin();
		}else{
			$this->load_language();
		}
		
		$language = $this->load->language('product/category');
		$language = $this->load->language('ocrestapi/message');
		$this->load->model('ocrestapi/ocrestapi');
		$this->load->model('catalog/product');
		$this->load->model('catalog/category');
		$this->load->model('catalog/manufacturer');
		$this->load->model('tool/image');
		$default_thumb = $this->model_tool_image->resize('no_image.png', 300, 300);
		$banners_data = $this->model_ocrestapi_ocrestapi->get_banner_data();
		foreach ($banners_data as $banner_data) {
			$data['banner_data'][]= array(
				'image'		  => !empty($banner_data['image'])?$this->model_tool_image->resize($banner_data['image'],300,300):$default_thumb,
				
				'title' => $banner_data['title'],
				'subtitle' => $banner_data['subtitle'],
				'type' => $banner_data['type'],
				'name' => $banner_data['name'],
				'button_label' => $banner_data['button_label'],
				'link_id' => $banner_data['id']
			);
			
		}
		
		$categories = $this->get_categories(0);
		$data['categories'] = $categories;
		$data['brands'] = array();
		$filter_data = array(
			'sort' => 'rand()',
			'start' => 0,
			'limit' => 10
		); 
		$results = $this->model_ocrestapi_ocrestapi->getManufacturers($filter_data);
		foreach ($results as $result) {
			if (is_numeric(utf8_substr($result['name'], 0, 1))) {
				$key = '0 - 9';
			} else {
				$key = utf8_substr(utf8_strtoupper($result['name']), 0, 1);
			}

			
			$data['brands'][] = array(
				'name' => $result['name'],
				'thumb' => !empty($result['image'])? HTTP_SERVER.'image/'.$result['image']:$default_thumb,
				'manufacturer_id' => $result['manufacturer_id']
				
			);
		}
		$data['brands'] = array_values($data['brands']);
		if (isset($this->request->post['start'])) {
			$start = $this->request->post['start'];
		} else {
			$start = 0;
		}

		if (isset($this->request->post['limit'])) {
			$limit = (int)$this->request->post['limit'];
		} else {
			$limit = '4';
		}

		$filter_data = array(

			'start' => $start,
			'limit' => $limit
		);
		
		$results = $this->model_catalog_product->getProductSpecials($filter_data);

		foreach ($results as $result) {
			if ($result['image']) {
				$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
			}

			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$price = false;
			}

			if ((float)$result['special']) {
				$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$special = false;
			}

			if ($this->config->get('config_tax')) {
				$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
			} else {
				$tax = false;
			}

			if ($this->config->get('config_review_status')) {
				$rating = (int)$result['rating'];
			} else {
				$rating = false;
			}

			if($this->customer->isLogged()){
				$customer_id = $this->customer->getId();
				$wishlist_status = $this->model_ocrestapi_ocrestapi->get_wishlist_status($customer_id,$result['product_id']);
			}else{
				$wishlist_status = false;
			}

			$options=$this->model_catalog_product->getProductOptions($result['product_id']);
			if($options){
				$is_options=true;
			} else {
				$is_options=false;
			}
			$recurrings = $this->model_catalog_product->getProfiles($result['product_id']);
			$is_recuring_product = (!empty($recurrings)) ? true : false;

			$data['products'][] = array(
				'product_id'  => $result['product_id'],
				'thumb'       => $image,
				'name'        => $result['name'],
				'model'     			=> $result['model'],
				'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
				'price'       => $price,
				'special'     => $special,
				'tax'         => $tax,
				'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
				'rating'      => $result['rating'],
				'reviews'      => $result['reviews'],
				'wishlist_status'    => $wishlist_status,
				'is_options'    => $is_options,
				'is_recuring_product'	=> $is_recuring_product,
			);
		}

			unset($language['backup']);
		 	$this->json['language'] = $language;
			$this->json['data'] =$data;
		}else{
		$this->json['errors'] = [ 'http_method' => 'Only GET method is allowed.'];
		}
		
		return $this->sendResponse();
	}

	protected function getImage($category_id)
	{
		$query=$this->db->query("SELECT * FROM `" . DB_PREFIX . "mobile_category_image` WHERE category_id = '" . (int)$category_id . "'");
		return !empty($query->row)?$query->row['image']:'';
	}
	private function get_categories($category_id)
	{
		$this->load->model('tool/image');
		$width = $this->config->get('restapi_default_image_category_icon_width_mobile');
		$height = $this->config->get('restapi_default_image_category_icon_height_mobile');
		$default_thumb = $this->model_tool_image->resize('no_image.png', $width,$height);

		$categories = $this->model_catalog_category->getCategories($category_id);
		if(!empty($categories)) {
			foreach ($categories as $key => $category) {
				$thumb=$this->getImage($category['category_id']);
				
				$categories[$key]['icon'] = !empty($thumb)?$this->model_tool_image->resize($thumb,$width,$height):$default_thumb;
				$categories[$key]['image'] = !empty($category['image'])? HTTP_SERVER.'image/'.$category['image']:'';
			}
		}
		return $categories;
	}	
}
