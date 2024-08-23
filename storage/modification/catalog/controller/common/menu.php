<?php
/* This file is under Git Control by KDSI. */
class ControllerCommonMenu extends Controller {
	public function index() {
		$this->load->language('common/menu');

		// Menu
		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$this->load->model('catalog/manufacturer');
		$this->load->model('tool/image');
			

		$data['categories'] = array();

		$categories = $this->model_catalog_category->getCategories(0);


		$results = $this->model_catalog_manufacturer->getManufacturers();
		//print_r($results);
		foreach ($results as $result) {
			$data['manufacturers'][] = array(
				'name' => $result['name'],
				'thumb' => 'image/'. $result['image'],
				'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id'])
			);
		}
		$data['home'] = $this->url->link('common/home');
		$data['text_blog'] = $this->language->get('text_blog');
		$data['all_blogs'] = $this->url->link('information/blogger/blogs');
		$data['login'] = $this->url->link('account/login', '', true);
		$data['manufacturer'] = $this->url->link('product/manufacturer');
		$data['contact'] = $this->url->link('information/contact');
		$data['newcollection'] = $this->url->link('product/special', '', true);
		$data['sitemap'] = $this->url->link('information/sitemap');
		$data['voucher'] = $this->url->link('account/voucher', '', true);
		$data['affiliate'] = $this->url->link('affiliate/login', '', true);
		$data['special'] = $this->url->link('product/special');
		$data['about'] = $this->url->link('information/information&information_id=4');
		$data['search'] = $this->load->controller('common/search');
			

				$data['wblog'] = $this->url->link('extension/home','','SSL');
				$language_id = $this->config->get('config_language_id');
				if ($this->config->get('blogsetting_menu' . $language_id)) {
					$data['blogsetting_menu'] = $this->config->get('blogsetting_menu' . $language_id);
				} else {
					$data['blogsetting_menu'] = '';
				}
				
		foreach ($categories as $category) {

	 			if($category['image']){
	   				 $image = $this->model_tool_image->resize($category['image'], 100, 100);
						} else {
	    			$image = false;
				}
			
			if ($category['top']) {
				// Level 2
				$children_data = array();

				$children = $this->model_catalog_category->getCategories($category['category_id']);

				foreach ($children as $child) {
					$filter_data = array(
						'filter_category_id'  => $child['category_id'],
						'filter_sub_category' => true
					);


					/* 2 Level Sub Categories START */
					$childs_data = array();
					$child_2 = $this->model_catalog_category->getCategories($child['category_id']);
					foreach ($child_2 as $childs) {
						$filter_data1 = array(
							'filter_category_id'  => $childs['category_id'],
							'filter_sub_category' => true
						);
						$childs_data[] = array(
						'name'  => $childs['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data1) . ')' : ''),
						'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'] . '_' . $childs['category_id']));
					}
					/* 2 Level Sub Categories END */

			
					$children_data[] = array(
						'name'  => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),

						'childs' => $childs_data,
						'column'   => $child['column'] ? $child['column'] : 1,
						'image'  => $child['image'] ? $this->model_tool_image->resize($child['image'], 225, 155) : false,
			
						'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
					);
				}

				// Level 1
				$data['categories'][] = array(
					'name'     => $category['name'],
					'children' => $children_data,
					'column'   => $category['column'] ? $category['column'] : 1,
					'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
				);
			}
		}


		if($data['categories'] && $this->config->get('mpplan_status')) {
			$this->load->language('membership/information');
			$data['categories'][] = array(
				'name'     => $this->language->get('menu_membership'),
				'children' => array(),
				'column'   => 1,
				'href'     => $this->url->link('membership/prices', '', true),
			);
		}
			

			if ($mpphotogallerymenu = $this->load->controller('extension/mpphotogallery/album/mpphotogalleryMenu')) {
				$data['categories'][] = $mpphotogallerymenu;
			}
			

                if ($this->config->get('module_galleria_page_status') && $this->config->get('module_galleria_page_menu')) {
                    $data['categories'][] = array(
                        'name'     => $this->config->get('module_galleria_page_menu_title')[$this->config->get('config_language_id')],
                        'children' => '',
                        'column'   => 1,
                        'href'     => $this->url->link('extension/module/galleria', '', true)
                    );
                }

		/* mps */
		if($this->config->get('mpblog_status')) {
			$this->load->language('mpblog/blogmenu');
			$mblog_link = $this->url->link('extension/mpblog/blogs','',true);
			$text_mblog = $this->language->get('text_mblog');
			if($this->config->get('mpblog_home_category')) {
				$this->load->model('extension/mpblog/mpblogcategory');
				$mpblog_catinfo = $this->model_extension_mpblog_mpblogcategory->getMpBlogCategory($this->config->get('mpblog_home_category'));
				if($mpblog_catinfo) {
					$mblog_link = $this->url->link('extension/mpblog/blogcategory','mpcpath='.$mpblog_catinfo['mpblogcategory_id'] ,true);
					$text_mblog = $mpblog_catinfo['name'];
				}
			}
			$mpblogmenu = [
				'name'     => $text_mblog,
				'children' => [],
				'column'   => 1,
				'href'     => $mblog_link
			];
			//array_unshift($data['categories'], $mpblogmenu);
			array_push($data['categories'], $mpblogmenu);
		}
		/* mpe */
			
		return $this->load->view('common/menu', $data);
	}
}
