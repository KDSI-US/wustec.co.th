<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiProductProduct extends OcrestapiController {
	private $error = array();

	public function index() {
		if($this->validateToken(true)) {
			$this->checkPlugin();
		}else{
			$this->load_language();
		}

		$language = $this->load->language('product/product');
		$language = $this->load->language('ocrestapi/message');

		$this->load->model('catalog/category');
		$this->load->model('ocrestapi/ocrestapi');
		$this->load->model('catalog/review');

		if (isset($this->request->get['path'])) {
			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = $path_id;
				} else {
					$path .= '_' . $path_id;
				}

				$category_info = $this->model_catalog_category->getCategory($path_id);
			}

			// Set the last category breadcrumb
			$category_info = $this->model_catalog_category->getCategory($category_id);
		}

		$this->load->model('catalog/manufacturer');
		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}

		$this->load->model('catalog/product');
		$this->load->model('catalog/review');
		$this->load->model('ocrestapi/ocrestapi');
		$product_info = $this->model_catalog_product->getProduct($product_id);
		
		
		if ($product_info) {
			


			$data['heading_title'] = $product_info['name'];

			$data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('ocrestapi/account/login', '', true), $this->url->link('ocrestapi/account/register', '', true));

			$this->load->model('catalog/review');

			$data['tab_review'] = sprintf($this->language->get('tab_review'), $product_info['reviews']);

			$data['product_id'] = (int)$this->request->get['product_id'];
			$data['manufacturer'] = $product_info['manufacturer'];
			$data['manufacturers'] = 'ocrestapi/product/manufacturer/info';
			$data['manufacturer_id'] = $product_info['manufacturer_id'];

			$data['model'] = $product_info['model'];
			$data['reward'] = $product_info['reward'];
			$data['points'] = $product_info['points'];
			$data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');

			if ($product_info['quantity'] <= 0) {
				$data['stock'] = $product_info['stock_status'];
			} elseif ($this->config->get('config_stock_display')) {
				$data['stock'] = $product_info['quantity'];
			} else {
				$data['stock'] = $this->language->get('text_instock');
			}

			$this->load->model('tool/image');
			$data['images'] = array();

			if ($product_info['image']) {
				$data['images'][0]['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
			} else {
				$data['popup'] = '';
			}

			if ($product_info['image']) {
				$data['images'][0]['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
			} else {
				$data['thumb'] = '';
			}


			$results = $this->model_catalog_product->getProductImages($this->request->get['product_id']);

			$i=1;
			foreach ($results as $result) {
				$data['images'][$i] = array(
					'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')),
					'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'))
				);
				$i++;
			}

			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {

				$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['price'] = false;
			}
			
			if ((float)$product_info['special']) {
				$data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['special'] = false;
			}
			
			if ($this->config->get('config_tax')) {
				$data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
			} else {
				$data['tax'] = false;
			}

			$discounts = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id']);

			$data['discounts'] = array();

			foreach ($discounts as $discount) {
				$data['discounts'][] = array(
					'quantity' => $discount['quantity'],
					'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'])
				);
			}

			$data['options'] = array();

			foreach ($this->model_catalog_product->getProductOptions($this->request->get['product_id']) as $option) {
				$product_option_value_data = array();

				foreach ($option['product_option_value'] as $option_value) {
					if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
						if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
							$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
						} else {
							$price = false;
						}

						$product_option_value_data[] = array(
							'product_option_value_id' => $option_value['product_option_value_id'],
							'option_value_id'         => $option_value['option_value_id'],
							'name'                    => $option_value['name'],
							'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
							'price'                   => $price,
							'price_prefix'            => $option_value['price_prefix']
						);

					}
				}

				$data['options'][] = array(
					'product_option_id'    => $option['product_option_id'],
					'product_option_value' => $product_option_value_data,
					'option_id'            => $option['option_id'],
					'name'                 => $option['name'],
					'type'                 => $option['type'],
					'value'                => $option['value'],
					'required'             => $option['required']
				);
			}

			if ($product_info['minimum']) {
				$data['minimum'] = $product_info['minimum'];
			} else {
				$data['minimum'] = 1;
			}

			$data['review_status'] = $this->config->get('config_review_status');

			if ($this->config->get('config_review_guest') || $this->customer->isLogged()) {
				$data['review_guest'] = true;
			} else {
				$data['review_guest'] = false;
			}

			if ($this->customer->isLogged()) {
				$data['customer_name'] = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();
			} else {
				$data['customer_name'] = '';
			}

			$data['reviews_count'] = sprintf((int)$product_info['reviews']);
			$data['rating'] = (int)$product_info['rating'];
			
			$data['rating_data'] = $this->model_ocrestapi_ocrestapi->get_product_rating($this->request->get['product_id']);
			// print_r($this->customer->isLogged());die;
			if($this->customer->isLogged()){
			$customer_id = $this->customer->getId();
			$data['wishlist_status'] = $this->model_ocrestapi_ocrestapi->get_wishlist_status($customer_id,$product_id);
			}
			

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
			} else {
				$data['captcha'] = '';
			}

			$data['share'] = 'ocrestapi/product/product';

			$data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($this->request->get['product_id']);

			$data['products'] = array();

			$results = $this->model_catalog_product->getProductRelated($this->request->get['product_id']);
			
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
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
					$wishlist_status='';
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
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'model'       => $result['model'],
					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $rating,
					'review_count'      => $result['reviews'],
					 'wishlist_status'      => $wishlist_status,
					// 'href'        => 'ocrestapi/product/product',
					'is_options'	=> $is_options,
					'is_recuring_product'	=> $is_recuring_product,
				);
			}

			$data['tags'] = array();

			if ($product_info['tag']) {
				$tags = explode(',', $product_info['tag']);

				foreach ($tags as $tag) {
					$data['tags'][] = array(
						'tag'  => trim($tag),
						'href' => 'ocrestapi/product/search'
					);
				}
			}
			$this->load->model('ocrestapi/ocrestapi');
			$results = $this->model_ocrestapi_ocrestapi->get_reviews_by_productId($this->request->get['product_id']);

			
			$data['reviews'] = array();
			foreach ($results as $result) {
				$data['reviews'][] = array(
					'review_id'     => $result['review_id'],
					'author'     => $result['author'],
					'text'       => nl2br($result['text']),
					'rating'     => (int)$result['rating'],
					'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
				);
			}
		
			$data['recurrings'] = $this->model_catalog_product->getProfiles($this->request->get['product_id']);

			$this->model_catalog_product->updateViewed($this->request->get['product_id']);
			unset($language['backup']);
			$this->json['language'] = $language;
			$this->json['data'] = $data;
			return $this->sendResponse();

		} else {
			

			unset($language['backup']);
			$this->json['language'] = $language;			 
			$this->json['data'] = isset($data)?$data:'';				
			 
			return $this->sendResponse();
		}
	}

	public function review() {
		 
		if($this->validateToken(true)) {
			$this->checkPlugin();
		}else{
			$this->load_language();
		}
		$language = $this->load->language('product/product');

		$this->load->model('catalog/review');
		$this->load->model('catalog/product');
		$this->load->model('ocrestapi/ocrestapi');

	
		if (isset($this->request->get['start'])) {
			$start = $this->request->get['start'];
		} else {
			$start = 0;
		}

		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = '5';
		}
		

		$data['reviews'] = array();
		$results = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id'],$start,$limit);

		foreach ($results as $result) {
			$data['reviews'][] = array(
				'review_id'     => $result['review_id'],
				'author'     => $result['author'],
				'text'       => nl2br($result['text']),
				'rating'     => (int)$result['rating'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$data['rating_data'] = $this->model_ocrestapi_ocrestapi->get_product_rating($this->request->get['product_id']);
		unset($language['backup']);
		$this->json['language'] = $language;
		$this->json['data'] = $data;
		return $this->sendResponse();
	}

	public function write() {
		$this->checkPlugin();
		$language = $this->load->language('product/product');
		$language = $this->load->language('ocrestapi/message');

		$json = array();
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if(!$this->validate())
			{
				$this->json['status'] = false;
				$this->json['errors'] = $this->error;
				return $this->sendResponse();
			}
			
			$this->load->model('catalog/review');
				if(isset($this->request->get['product_id']) && !empty($this->request->get['product_id']))
				{

				$this->model_catalog_review->addReview($this->request->get['product_id'], $this->request->post);

				$this->json['data']['success'] = $this->language->get('text_success');
				}
				else
				{
					$this->json['errors'] = $this->language->get('error_warnings');
				}
				
			return $this->sendResponse();
			}
			unset($language['backup']);
			$this->json['language'] = $language;
			return $this->sendResponse();
		}
		
	
	public function validate()
	{

		if(!isset($this->request->post['name'])){
			$this->error['name'] = $this->language->get('error_input_name');
			}else if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25))
			 {
					$this->error['name'] = $this->language->get('error_name');
			}
			if(!isset($this->request->post['text'])){
				$this->error['text'] = $this->language->get('error_input_text');
			}else if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
			$this->error['text'] = $this->language->get('error_text');
			}
							
			if(!isset($this->request->post['rating'])){
			$this->error['rating'] = $this->language->get('error_input_rating');
			}else if (empty($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {
				$this->error['rating']= $this->language->get('error_rating');
			}
		return !$this->error;					
	}
	public function getRecurringDescription() {
		$this->load->language('product/product');
		$this->load->model('catalog/product');

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		if (isset($this->request->post['recurring_id'])) {
			$recurring_id = $this->request->post['recurring_id'];
		} else {
			$recurring_id = 0;
		}

		if (isset($this->request->post['quantity'])) {
			$quantity = $this->request->post['quantity'];
		} else {
			$quantity = 1;
		}

		$product_info = $this->model_catalog_product->getProduct($product_id);
		
		$recurring_info = $this->model_catalog_product->getProfile($product_id, $recurring_id);

		$json = array();

		if ($product_info && $recurring_info) {
			if (!$json) {
				$frequencies = array(
					'day'        => $this->language->get('text_day'),
					'week'       => $this->language->get('text_week'),
					'semi_month' => $this->language->get('text_semi_month'),
					'month'      => $this->language->get('text_month'),
					'year'       => $this->language->get('text_year'),
				);

				if ($recurring_info['trial_status'] == 1) {
					$price = $this->currency->format($this->tax->calculate($recurring_info['trial_price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$trial_text = sprintf($this->language->get('text_trial_description'), $price, $recurring_info['trial_cycle'], $frequencies[$recurring_info['trial_frequency']], $recurring_info['trial_duration']) . ' ';
				} else {
					$trial_text = '';
				}

				$price = $this->currency->format($this->tax->calculate($recurring_info['price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

				if ($recurring_info['duration']) {
					$text = $trial_text . sprintf($this->language->get('text_payment_description'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				} else {
					$text = $trial_text . sprintf($this->language->get('text_payment_cancel'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				}

				$json['success'] = $text;
			}
		}

		$this->json['data'] = $json;
		return $this->sendResponse();
	}
}
