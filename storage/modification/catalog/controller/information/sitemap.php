<?php
/* This file is under Git Control by KDSI. */
class ControllerInformationSitemap extends Controller {
	public function index() {
		$this->load->language('information/sitemap');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('information/sitemap')
		);


				/*XML START*/
		$data['text_blog'] = $this->language->get('text_blog');
		$this->load->model('extension/blogcategory');
		$this->load->model('catalog/product');
		$this->load->model('extension/blog');
		$blogcategories = $this->model_extension_blogcategory->getBlogCategories(0);
		$data['bloglink'] = $this->url->link('extension/home','','SSL');
		$data['blogcategories']=array();
		foreach ($blogcategories as $bcategory_1){
			$level_2_data = array();

			$bcategories_2 = $this->model_extension_blogcategory->getBlogCategories($bcategory_1['blog_category_id']);

			foreach ($bcategories_2 as $bcategory_2) {
				$level_3_data = array();

				$bcategories_3 = $this->model_extension_blogcategory->getBlogCategories($bcategory_2['blog_category_id']);

				foreach ($bcategories_3 as $bcategory_3) {
					$level_3_data[] = array(
						'name' => $bcategory_3['name'],
						'href' => $this->url->link('extension/blogcategory', 'bpath=' . $bcategory_1['blog_category_id'] . '_' . $bcategory_2['blog_category_id'] . '_' . $bcategory_3['blog_category_id'])
					);
				}

				$level_2_data[] = array(
					'name'     => $bcategory_2['name'],
					'children' => $level_3_data,
					'href'     => $this->url->link('extension/blogcategory', 'bpath=' . $bcategory_1['blog_category_id'] . '_' . $bcategory_2['blog_category_id'])
				);
			}

			$data['blogcategories'][] = array(
				'name'     => $bcategory_1['name'],
				'children' => $level_2_data,
				'href'     => $this->url->link('extension/blogcategory', 'bpath=' . $bcategory_1['blog_category_id'])
			);
		}
		/*XML END*/
				
		$this->load->model('catalog/category');

		$data['categories'] = array();

		$categories_1 = $this->model_catalog_category->getCategories(0);

		foreach ($categories_1 as $category_1) {
			$level_2_data = array();

			$categories_2 = $this->model_catalog_category->getCategories($category_1['category_id']);

			foreach ($categories_2 as $category_2) {
				$level_3_data = array();

				$categories_3 = $this->model_catalog_category->getCategories($category_2['category_id']);

				foreach ($categories_3 as $category_3) {
					$level_3_data[] = array(
						'name' => $category_3['name'],
						'href' => $this->url->link('product/category', 'path=' . $category_1['category_id'] . '_' . $category_2['category_id'] . '_' . $category_3['category_id'])
					);
				}

				$level_2_data[] = array(
					'name'     => $category_2['name'],
					'children' => $level_3_data,
					'href'     => $this->url->link('product/category', 'path=' . $category_1['category_id'] . '_' . $category_2['category_id'])
				);
			}

			$data['categories'][] = array(
				'name'     => $category_1['name'],
				'children' => $level_2_data,
				'href'     => $this->url->link('product/category', 'path=' . $category_1['category_id'])
			);
		}

		$data['special'] = $this->url->link('product/special');
		$data['account'] = $this->url->link('account/account', '', true);
		$data['edit'] = $this->url->link('account/edit', '', true);
		$data['password'] = $this->url->link('account/password', '', true);
		$data['address'] = $this->url->link('account/address', '', true);
		$data['history'] = $this->url->link('account/order', '', true);
		$data['download'] = $this->url->link('account/download', '', true);
		$data['cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);
		$data['search'] = $this->url->link('product/search');
		$data['contact'] = $this->url->link('information/contact');


            if ($this->config->get('module_galleria_status') && $this->config->get('module_galleria_sitemap')) {
                $this->load->model('extension/module/galleria');
                $data['galleries'] = array();
                $galleria_page_status = $this->config->get('module_galleria_page_status');
                if ($galleria_page_status) {
                    $data['galleries'][] = array(
                        'name' => $this->config->get('module_galleria_page_title')[$this->config->get('config_language_id')],
                        'href'  => $this->url->link('extension/module/galleria', '', true)
                    );
                }
                $results = $this->model_extension_module_galleria->getGalleries();
                foreach ($results as $result) {     
                    $data['galleries'][] = array(
                        'name' => $result['name'],
                        'href'  => $this->url->link('extension/module/galleria/info', ($result['inpage']&&$galleria_page_status ? 'galleria_path=1&galleria_id=' . $result['galleria_id'] : 'galleria_id=' . $result['galleria_id']), true)
                    );
                }
            }
            
		$this->load->model('catalog/information');

		$data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
			$data['informations'][] = array(
				'title' => $result['title'],
				'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
			);
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('information/sitemap', $data));
	}
}