<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiProductListing extends OcrestapiController {
	public function index() {
		if($this->validateToken(true)) {
			$this->checkPlugin();
		}else{
			$this->load_language();
		}
		if($this->request->server['REQUEST_METHOD'] == 'GET') {
			$language = $this->load->language('product/category');
			$language = $this->load->language('ocrestapi/message');

			$this->load->model('catalog/category');
			$this->load->model('catalog/product');
			$this->load->model('ocrestapi/ocrestapi');
			$this->load->model('tool/image');
			unset($language['backup']);
			$this->json['language'] = $language;

			if (isset($this->request->get['search'])) {
				$search = $this->request->get['search'];
			} else {
				$search = '';
			}

			/*if (isset($this->request->get['tag'])) {
				$tag = $this->request->get['tag'];
			} 
			elseif (isset($this->request->get['search'])) {
				$tag = $this->request->get['search'];
			} 
			else {
				$tag = '';
			}*/

			/*if (isset($this->request->get['description'])) {
				$description = $this->request->get['description'];
			} else {
				$description = '';
			}*/

			if (isset($this->request->get['special']) && $this->request->get['special']==1) {
				$special_product = true;
			} else {
				$special_product = false;
			}

			if (isset($this->request->get['category_ids'])) {
				$category_ids = $this->request->get['category_ids'];
			} else {
				$category_ids = '0';
			}

			/*if (isset($this->request->post['filter'])) {
				$filter = $this->request->post['filter'];
			} else {
				$filter = '';
			}*/

			if (isset($this->request->get['sort'])) {
				$sort = $this->request->get['sort'];
			} else {
				$sort = 'p.sort_order';
			}

			if (isset($this->request->get['order'])) {
				$order = $this->request->get['order'];
			} else {
				$order = 'ASC';
			}

			if (isset($this->request->get['start'])) {
				$start = $this->request->get['start'];
			} else {
				$start = 0;
			}

			if (isset($this->request->get['manufacturer_ids'])) {
				$manufacturer_ids = $this->request->get['manufacturer_ids'];
			} else {
				$manufacturer_ids = '';
			}

			if (isset($this->request->get['limit'])) {				
				$limit = (int)$this->request->get['limit'];
			} else {
				$limit = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
			}

			$data['products'] = array();

			$filter_data = array(
				'filter_name'         		=> $search,
				// 'filter_tag'          		=> $tag,
				// 'filter_filter'       		=> $filter,
				'filter_category_id'  		=> $category_ids,
				'sort'                		=> $sort,
				'order'               		=> $order,
				'start'               		=> $start,
				'filter_manufacturer_id'	=> $manufacturer_ids,
				'special_product'	        => $special_product,
				'limit'               		=> $limit
			);
			
			$results = $this->model_ocrestapi_ocrestapi->getfilter_Products($filter_data);
			if(!empty($results)){

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
						'model'        => $result['model'],
						'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
						'price'       => $price,
						'special'     => $special,
						'tax'         => $tax,
						'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
						'rating'      => $result['rating'],
						'reviews'      => $result['reviews'],
						'wishlist_status'      => $wishlist_status,
						'is_options'	=> $is_options,
						'is_recuring_product'	=> $is_recuring_product,
					);
					
				}
				
				}
				$data['sorts'] = array();

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_default'),
					'value' => 'p.sort_order-ASC',
					'sort'  => 'p.sort_order',
					'order'  => 'ASC'
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_name_asc'),
					'value' => 'pd.name-ASC',
					'sort'  => 'pd.name',
					'order'  => 'ASC'
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_name_desc'),
					'value' => 'pd.name-DESC',
					'sort'  => 'pd.name',
					'order'  => 'DESC'
					
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_price_asc'),
					'value' => 'p.price-ASC',
					'sort'  => 'p.price',
					'order'  => 'ASC'
					
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_price_desc'),
					'value' => 'p.price-DESC',
					'sort'  => 'p.price',
					'order'  => 'DESC'
					
				);

				if ($this->config->get('config_review_status')) {
					$data['sorts'][] = array(
						'text'  => $this->language->get('text_rating_desc'),
						'value' => 'rating-DESC',
						'sort'  => 'rating',
						'order'  => 'DESC'
					);

					$data['sorts'][] = array(
						'text'  => $this->language->get('text_rating_asc'),
						'value' => 'rating-ASC',
						'sort'  => 'rating',
						'order'  => 'ASC'
					);
				}

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_model_asc'),
					'value' => 'p.model-ASC',
					'sort'  => 'p.model',
					'order'  => 'ASC'
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_model_desc'),
					'value' => 'p.model-DESC',
					'sort'  => 'p.model',
					'order'  => 'DESC'
				);


				$this->json['data'] = $data;
				return $this->sendResponse();

			
		}else{
			$this->json['errors'] = [ 'http_method' => 'Only GET method is allowed.'];
		}

			return $this->sendResponse();
		}
	}
