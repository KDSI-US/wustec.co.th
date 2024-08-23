<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleFeatured extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/featured');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['banner_third'] = $this->load->controller('common/banner_third');
		$data['discount_code'] = $this->load->controller('common/discount_code');
			

		$data['products'] = array();

		if (!$setting['limit']) {
			$setting['limit'] = 4;
		}

		if (!empty($setting['product'])) {
			$products = array_slice($setting['product'], 0, (int)$setting['limit']);

			foreach ($products as $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);

				if ($product_info) {

			$this->load->model('account/wishlist');
			$wishlist = $this->model_account_wishlist->getWishlist($this->customer->getId());
			$data['wishlist'] = (in_array($product_id, array_column($wishlist, 'product_id')) ? true : false);
			
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $setting['width'], $setting['height']);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
					}


					//added for image swap
					$images = $this->model_catalog_product->getProductImages($product_info['product_id']);
					if(isset($images[0]['image']) && !empty($images)){
						$images = $images[0]['image']; 
					} else {
						$images = $image;
					}
			
					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price = false;
					}

					if (!is_null($product_info['special']) && (float)$product_info['special'] >= 0) {
						$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
						$tax_price = (float)$product_info['special'];
					} else {
						$special = false;
						$tax_price = (float)$product_info['price'];
					}
		
					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format($tax_price, $this->session->data['currency']);
					} else {
						$tax = false;
					}

					if ($this->config->get('config_review_status')) {
						$rating = $product_info['rating'];
					} else {
						$rating = false;
					}


				/* Number of Color of Product */
				$color_count = $this->model_catalog_product->getProductNumberOfColor($product_info['product_id']). " colors available";

				/* Start - Color Option  */
				$data['options'] = array();
				foreach ($this->model_catalog_product->getProductOptions($product_info['product_id']) as $option) {
					$product_option_value_data = array();
					foreach ($option['product_option_value'] as $option_value) {
		
						if (isset($data['PRO_settings']['show_out_of_stock']) && $data['PRO_settings']['show_out_of_stock'] && $option_value['subtract'] && ($option_value['quantity'] <= 0)) {
							$option_value['subtract'] = false;
						}
				
						if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
							if ($option_value['option_image']) {
								/* Maximum size */
								$option_image_popup = $this->model_tool_image->resize($option_value['option_image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
		
								/* Preview size */
								$option_image_preview = $this->model_tool_image->resize($option_value['option_image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
		
								/* Thumb size */
								/* $option_image_additional_thumb = $this->model_tool_image->resize($option_value['option_image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height')); */

								$option_image_pathinfo['dn']  = pathinfo($option_value['option_image'], PATHINFO_DIRNAME);
								$option_image_pathinfo['ds']  = DIRECTORY_SEPARATOR;
								$option_image_pathinfo['fn']  = pathinfo($option_value['option_image'], PATHINFO_FILENAME);
								$option_image_pathinfo['ext'] = pathinfo($option_value['option_image'], PATHINFO_EXTENSION);
		
								$product_option_value_images[$option_value['product_option_value_id']] = array(
									'image_pathinfo' => $option_image_pathinfo,
									'image'          => $option_image_preview,
									'image_large'    => $option_image_popup
								);
							}

							$product_option_value_data[] = array(
								'product_option_value_id' => $option_value['product_option_value_id'],
								'option_value_id'         => $option_value['option_value_id'],
								'name'                    => $option_value['name'],
								'image'                   => $this->model_tool_image->resize($option_value['image'], 516, 731),
								'option_image'	          => $this->model_tool_image->resize($option_value['option_image'], 280, 420),
								'option_image_color_swatch'	=> $this->model_tool_image->resize($option_value['option_image_color_swatch'], 40, 40),
								'quantity'     	          => $option_value['quantity'],
								'master_option_value'     => $option_value['master_option_value']
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
						'master_option'          => $option['master_option'],
						'master_option_value'    => isset($option['master_option_value']) ? $option['master_option_value'] : null,
						'required'             => $option['required']
					);
				}			
				/* End - Color Option*/
			
				$product_id = isset($product_info['product_id']) ? $product_info['product_id'] : ($result['product_id'] ? $result['product_id'] : 0);
					$data['products'][] = array(
					'wishlist' => (in_array($product_id, array_column($wishlist, 'product_id')) ? true : false),
						'product_id'  => $product_info['product_id'],
						'thumb'       => $image,
						'name'        => $product_info['name'],
						'description' => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
						'price'       => $price,
						'special'     => $special,
						'tax'         => $tax,
						'rating'      => $rating,
						'color_count' => $color_count,
						'options'     => $data['options'],

						'model'         => $product_info['model'],
						'qty'    	  	=> $product_info['quantity'],
						'brand'       	=> $product_info['manufacturer'],
						'review'      	=> $product_info['reviews'],
						'quick'        	=> $this->url->link('product/quick_view','&product_id=' . $product_info['product_id']),
						'percentsaving' => round((($product_info['price'] - $product_info['special'])/$product_info['price'])*100, 0),
						'thumb_swap'  	=> $this->model_tool_image->resize($images , $setting['width'], $setting['height']),
			
						'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
					);
				}
			}
		}

		if ($data['products']) {
			return $this->load->view('extension/module/featured', $data);
		}
	}
}