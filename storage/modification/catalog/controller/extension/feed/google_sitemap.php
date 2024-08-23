<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionFeedGoogleSitemap extends Controller {

				/*XML START*/
	protected function getblogCategories($parent_id, $current_path = '') {
		$output = '';

		$results = $this->model_extension_blogcategory->getBlogCategories($parent_id);

		foreach ($results as $result) {
			if (!$current_path) {
				$new_path = $result['blog_category_id'];
			} else {
				$new_path = $current_path . '_' . $result['blog_category_id'];
			}

			$output .= '<url>';
			$output .= '<loc>' . $this->url->link('extension/blogcategory', 'bpath=' . $new_path) . '</loc>';
			$output .= '<changefreq>weekly</changefreq>';
			$output .= '<priority>0.7</priority>';
			$output .= '</url>';

			$posts = $this->model_extension_blog->getblogs(array('filter_blog_category_id' => $result['blog_category_id']));

			foreach ($posts as $post) {
				$output .= '<url>';
				$output .= '<loc>' . $this->url->link('extension/post', 'bpath=' . $new_path . '&post_id=' . $post['post_id']) . '</loc>';
				$output .= '<changefreq>weekly</changefreq>';
				$output .= '<priority>1.0</priority>';
				$output .= '</url>';
			}

			$output .= $this->getblogCategories($result['blog_category_id'], $new_path);
		}

		return $output;
	}
	/*XML*/
				
	public function index() {
		if ($this->config->get('feed_google_sitemap_status')) {
			$output  = '<?xml version="1.0" encoding="UTF-8"?>';
			$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

			$this->load->model('catalog/product');
			$this->load->model('tool/image');

			$products = $this->model_catalog_product->getProducts();

			foreach ($products as $product) {
				$output .= '<url>';
				$output .= '  <loc>' . $this->url->link('product/product', 'product_id=' . $product['product_id']) . '</loc>';
				$output .= '  <changefreq>weekly</changefreq>';
				$output .= '  <lastmod>' . date('Y-m-d\TH:i:sP', strtotime($product['date_modified'])) . '</lastmod>';
				$output .= '  <priority>1.0</priority>';

				if ($product['image']) {
					$output .= '  <image:image>';
					$output .= '  <image:loc>' . $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')) . '</image:loc>';
					$output .= '  <image:caption>' . $product['name'] . '</image:caption>';
					$output .= '  <image:title>' . $product['name'] . '</image:title>';
					$output .= '  </image:image>';
				}

				$output .= '</url>';
			}


				/*XML START*/
			$this->load->model('extension/blog');
			$this->load->model('extension/blogcategory');
			$blogs = $this->model_extension_blog->getblogs(array());

			foreach ($blogs as $blog) {
				if ($product['image']) {
					$output .= '<url>';
					$output .= '<loc>' . $this->url->link('extension/post', 'post_id=' . $blog['post_id']) . '</loc>';
					$output .= '<changefreq>weekly</changefreq>';
					$output .= '<lastmod>' . date('Y-m-d\TH:i:sP', strtotime($blog['date_modified'])) . '</lastmod>';
					$output .= '<priority>1.0</priority>';
					$output .= '<image:image>';
					$output .= '<image:loc>' . $this->model_tool_image->resize($blog['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
					$output .= '<image:caption>' . $blog['name'] . '</image:caption>';
					$output .= '<image:title>' . $blog['name'] . '</image:title>';
					$output .= '</image:image>';
					$output .= '</url>';
					
				}
			}
			$output .= $this->getblogCategories(0);
			/*XML END*/
				
			$this->load->model('catalog/category');

			$output .= $this->getCategories(0);

			$this->load->model('catalog/manufacturer');

			$manufacturers = $this->model_catalog_manufacturer->getManufacturers();

			foreach ($manufacturers as $manufacturer) {
				$output .= '<url>';
				$output .= '  <loc>' . $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer['manufacturer_id']) . '</loc>';
				$output .= '  <changefreq>weekly</changefreq>';
				$output .= '  <priority>0.7</priority>';
				$output .= '</url>';
			}

			$this->load->model('catalog/information');

			$informations = $this->model_catalog_information->getInformations();

			foreach ($informations as $information) {
				$output .= '<url>';
				$output .= '  <loc>' . $this->url->link('information/information', 'information_id=' . $information['information_id']) . '</loc>';
				$output .= '  <changefreq>weekly</changefreq>';
				$output .= '  <priority>0.5</priority>';
				$output .= '</url>';
			}


            if ($this->config->get('module_galleria_status') && $this->config->get('module_galleria_sitemap')) {
                $galleria_page_status = $this->config->get('module_galleria_page_status');
                if ($galleria_page_status) {
                    $output .= '<url>';
                    $output .= '<loc>' . $this->url->link('extension/module/galleria', '', true) . '</loc>';
                    $output .= '<changefreq>weekly</changefreq>';
                    $output .= '<priority>0.7</priority>';
                    $output .= '</url>';
                }
                $this->load->model('extension/module/galleria');
                $galleries = $this->model_extension_module_galleria->getGalleries();
                foreach ($galleries as $gallery) {
                    $output .= '<url>';
                    $output .= '<loc>' . $this->url->link('extension/module/galleria/info', ($gallery['inpage']&&$galleria_page_status ? 'galleria_path=1&galleria_id=' . $gallery['galleria_id'] : 'galleria_id=' . $gallery['galleria_id']), true) . '</loc>';
                    $output .= '<changefreq>weekly</changefreq>';
                    $output .= '<priority>0.5</priority>';
                    $output .= '</url>';
                }
            }
            
			$output .= '</urlset>';

			$this->response->addHeader('Content-Type: application/xml');
			$this->response->setOutput($output);
		}
	}

	protected function getCategories($parent_id) {
		$output = '';

		$results = $this->model_catalog_category->getCategories($parent_id);

		foreach ($results as $result) {
			$output .= '<url>';
			$output .= '  <loc>' . $this->url->link('product/category', 'path=' . $result['category_id']) . '</loc>';
			$output .= '  <changefreq>weekly</changefreq>';
			$output .= '  <priority>0.7</priority>';
			$output .= '</url>';

			$output .= $this->getCategories($result['category_id']);
		}

		return $output;
	}
}
