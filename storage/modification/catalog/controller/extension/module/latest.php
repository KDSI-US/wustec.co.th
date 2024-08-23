<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleLatest extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/latest');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['products'] = array();

		$filter_data = array(
			'sort'  => 'p.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => $setting['limit']
		);
			

		$results = $this->model_catalog_product->getProducts($filter_data);

		if ($results) {

			$this->load->model('account/wishlist');
			$wishlist = $this->model_account_wishlist->getWishlist($this->customer->getId());
			
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
				}


				//added for image swap
				$images = $this->model_catalog_product->getProductImages($result['product_id']);
				if (isset($images[0]['image']) && !empty($images)) {
					$images = $images[0]['image']; 
				} else {
					$images = $image;
				}
			
				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if (!is_null($result['special']) && (float)$result['special'] >= 0) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$tax_price = (float)$result['special'];
				} else {
					$special = false;
					$tax_price = (float)$result['price'];
				}
	
				if ($this->config->get('config_tax')) {

					$tax_price = (float)$result['special'] ? $result['special'] : $result['price'];
			
					$tax = $this->currency->format($tax_price, $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = $result['rating'];
				} else {
					$rating = false;
				}

				/* Number of Color of Product */
				$color_count = $this->model_catalog_product->getProductNumberOfColor($result['product_id']). " colors available";

				/* Start - Color Option  */
				$data['options'] = array();
				foreach ($this->model_catalog_product->getProductOptions($result['product_id']) as $option) {
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
				if ($result['price'] != 0) {
				$data['products'][] = array(
					'wishlist' => (in_array($product_id, array_column($wishlist, 'product_id')) ? true : false),
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'rating'      => $rating,
					'color_count' => $color_count,
					'options'     => $data['options'],

					'model'         => $result['model'],
					'qty'			=> $result['quantity'],
					'quick'			=> $this->url->link('product/quick_view','&product_id=' . $result['product_id']),
					'thumb_swap'	=> $this->model_tool_image->resize($images , $setting['width'], $setting['height']),
					'percentsaving'	=> round((($result['price'] - $result['special'])/$result['price'])*100, 0),
			
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
				}
			}

			return $this->load->view('extension/module/latest', $data);
		}
	}
}
