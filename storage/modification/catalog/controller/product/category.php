<?php
/* This file is under Git Control by KDSI. */
class ControllerProductCategory extends Controller {
	public function index() {

            $login_required = $this->config->get("module_kdsi_login_required");
            if ($login_required['status_category']) {
                $redirect = true;
                if (isset($this->request->get['route'])) {
                    $route = $this->request->get['route'];
                } else {
                    $route = 'common/home';
                }
                $safe_route = array(
                    'account/register', 
                    'account/login',
                    'account/forgotten',
                );
                if ($this->customer->isLogged()) {
                    $redirect = false;
                } elseif (isset($this->request->get["route"])) {
                    if(in_array($route, $safe_route)) {
                        $redirect = false;
                    }
                }
                if ($redirect) {
                    $path = $this->request->get['path'];
                    $this->session->data['redirect'] = $this->url->link('product/category', 'path=' . $path, true);
                    $this->response->redirect($this->url->link('account/login', '', true));
                }
            }
            

		$approval_required = $this->config->get("module_approval_required");
		$approved = (int)$this->customer->getApproved();
		if ($approval_required['status_category']) {
			$redirect = true;
			if (isset($this->request->get['route'])) {
				$route = $this->request->get['route'];
			} else {
				$route = 'common/home';
			}
			$safe_route = array(
				'account/register', 
				'account/login',
				'account/forgotten',
				'account/account', 
				'account/edit'
			);
			if ($approved == 1) {
				$redirect = false;
			} elseif (isset($this->request->get["route"])) {
				if(in_array($route, $safe_route)) {
					$redirect = false;
				}
			}
			if ($redirect) {
				$path = $this->request->get['path'];
				$this->session->data['redirect'] = $this->url->link('product/category', 'path=' . $path, true);
				$this->response->redirect($this->url->link('account/edit', '', true));
			}
		}
			
		$this->load->language('product/category');

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		if (isset($this->request->get['filter'])) {
			$filter = $this->request->get['filter'];
		} else {
			$filter = '';
		}

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

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		if (isset($this->request->get['path'])) {
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

			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = (int)$path_id;
				} else {
					$path .= '_' . (int)$path_id;
				}

				$category_info = $this->model_catalog_category->getCategory($path_id);

				if ($category_info) {
					$data['breadcrumbs'][] = array(
						'text' => $category_info['name'],
						'href' => $this->url->link('product/category', 'path=' . $path . $url)
					);
				}
			}
		} else {
			$category_id = 0;
		}

		$category_info = $this->model_catalog_category->getCategory($category_id);

            $this->config->load('isenselabs/isenselabs_seo');
            $isense_module_path = $this->config->get('isenselabs_seo_path');
            $isense_call_model  = $this->config->get('isenselabs_seo_model');
            $this->load->model($isense_module_path);

            $breadcrumb_check = $this->{$isense_call_model}->getSetting('breadcrumb_categories');

            if ($category_info && $breadcrumb_check) {
                $cats = $this->{$isense_call_model}->getPathCategoryPage($category_id);
                $path = '';
                $parts = explode('_', (string)$cats);

                if ($parts) {
                    $data['breadcrumbs'] = array();
                    $data['breadcrumbs'][] = array(
                        'text' => $this->language->get('text_home'),
                        'href' => $this->url->link('common/home')
                    );

                    foreach ($parts as $path_id) {
                        $cat_info = $this->model_catalog_category->getCategory($path_id);
                        if (isset($cat_info) && $cat_info['category_id']!= $category_id) {
                            $data['breadcrumbs'][] = array(
                                'text'      => $cat_info['name'],
                                'href'      => $this->url->link('product/category', 'path=' . $cat_info['category_id']),
                                'separator' => $this->language->get('text_separator')
                            );
                        }
                    }
                }
            }
			

		if ($category_info) {
			$this->document->setTitle($category_info['meta_title']);
			$this->document->setDescription($category_info['meta_description']);
			$this->document->setKeywords($category_info['meta_keyword']);

			$data['heading_title'] = $category_info['name'];

			$data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));

			// Set the last category breadcrumb
			$data['breadcrumbs'][] = array(
				'text' => $category_info['name'],
				'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'])
			);

			if ($category_info['image']) {
				$data['thumb'] = $this->model_tool_image->resize($category_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height'));
			} else {
				$data['thumb'] = '';
			}

			$data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
			$data['compare'] = $this->url->link('product/compare');

			$data['video_url'] = $category_info['video_url'];
			$data['video_name'] = $category_info['video_name'];
			$data['video_width'] = $category_info['video_width'];
			$data['video_height'] = $category_info['video_height'];

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['categories'] = array();

			$results = $this->model_catalog_category->getCategories($category_id);

			foreach ($results as $result) {
				$filter_data = array(
					'filter_category_id'  => $result['category_id'],
					'filter_sub_category' => true
				);

				$data['categories'][] = array(
					'name' => $result['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
					'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '_' . $result['category_id'] . $url)
				);
			}

			$data['products'] = array();

			$filter_data = array(
				'filter_category_id' => $category_id,
				'filter_filter'      => $filter,
				'sort'               => $sort,
				'order'              => $order,
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit
			);

			$product_total = $this->model_catalog_product->getTotalProducts($filter_data);

			$results = $this->model_catalog_product->getProducts($filter_data);


			$this->load->model('account/wishlist');
			$wishlist = $this->model_account_wishlist->getWishlist($this->customer->getId());
			
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
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
					$tax = $this->currency->format($tax_price, $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}


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
								// Maximum size
								$option_image_popup = $this->model_tool_image->resize($option_value['option_image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
		
								// Preview size
								$option_image_preview = $this->model_tool_image->resize($option_value['option_image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
		
								// Thumb size
								//$option_image_additional_thumb = $this->model_tool_image->resize($option_value['option_image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'));

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
			
				/* Number of Color of Product */
				$color_count = $this->model_catalog_product->getProductNumberOfColor($result['product_id']). " colors available";
			
				$product_id = isset($product_info['product_id']) ? $product_info['product_id'] : ($result['product_id'] ? $result['product_id'] : 0);
				$data['products'][] = array(
					'wishlist' => (in_array($product_id, array_column($wishlist, 'product_id')) ? true : false),
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $result['rating'],
					'options'     => $data['options'],
					'color_count' => $color_count,

					'percentsaving' 	 => round((($result['price'] - $result['special'])/$result['price'])*100, 0),
					'qty'    	  => $result['quantity'],
					'brand'       => $result['manufacturer'],
					'review'      => $result['reviews'],
					'quick'       => $this->url->link('product/quick_view','&product_id=' . $result['product_id']),
					'thumb_swap'  => $this->model_tool_image->resize($images, $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), 
					$this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height')),
			
					'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'] . $url)
				);
			}

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['sorts'] = array();

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_default'),
				'value' => 'p.sort_order-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.sort_order&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_asc'),
				'value' => 'pd.name-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_desc'),
				'value' => 'pd.name-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=DESC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_asc'),
				'value' => 'p.price-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_desc'),
				'value' => 'p.price-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=DESC' . $url)
			);

			if ($this->config->get('config_review_status')) {
				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_desc'),
					'value' => 'rating-DESC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=DESC' . $url)
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_asc'),
					'value' => 'rating-ASC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=ASC' . $url)
				);
			}

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_asc'),
				'value' => 'p.model-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.model&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_desc'),
				'value' => 'p.model-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.model&order=DESC' . $url)
			);

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			$data['limits'] = array();

			$limits = array_unique(array($this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit'), 25, 50, 75, 100));

			sort($limits);

			foreach($limits as $value) {
				$data['limits'][] = array(
					'text'  => $value,
					'value' => $value,
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=' . $value)
				);
			}

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$pagination = new Pagination();
			$pagination->total = $product_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&page={page}');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));

			// http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
			if ($page == 1) {
			    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id']), 'canonical');
			} else {
				$this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page='. $page), 'canonical');
			}
			
			if ($page > 1) {
			    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . (($page - 2) ? '&page='. ($page - 1) : '')), 'prev');
			}

			if ($limit && ceil($product_total / $limit) > $page) {
			    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page='. ($page + 1)), 'next');
			}

			$data['sort'] = $sort;
			$data['order'] = $order;
			$data['limit'] = $limit;

                $filter_galleria = array(
                    'source' => 'category',
                    'source_id'  => $category_info['category_id']
                );
                $data['galleria'] = $this->load->controller('extension/module/galleria/widget', $filter_galleria);
            

			if (!isset($this->request->get['sort'])) $data['products'] = $this->ocutilities->array_shuffle($data['products']);

			$data['continue'] = $this->url->link('common/home');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('product/category', $data));
		} else {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('product/category', $url)
			);

$this->load->controller('extension/module/isenselabs_seo/notFoundPageHandler');
			$this->document->setTitle($this->language->get('text_error'));

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}
}
