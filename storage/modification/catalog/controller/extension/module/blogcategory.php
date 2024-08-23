<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleBlogcategory extends Controller {
	public function index() {
		$this->load->language('extension/module/category');

		$data['heading_title'] = $this->language->get('heading_title');

		if (isset($this->request->get['bpath'])) {
			$parts = explode('_', (string)$this->request->get['bpath']);
		} else {
			$parts = array();
		}

		if (isset($parts[0])) {
			$data['blog_category_id'] = $parts[0];
		} else {
			$data['blog_category_id'] = 0;
		}

		if (isset($parts[1])) {
			$data['child_id'] = $parts[1];
		} else {
			$data['child_id'] = 0;
		}
		
		$this->load->model('extension/blogcategory');

		$this->load->model('catalog/product');
		$this->load->model('extension/blog');

		$data['categories'] = array();

		$categories = $this->model_extension_blogcategory->getBlogCategories(0);
		foreach ($categories as $blogcategory){
			$children_data = array();

			if ($blogcategory['blog_category_id'] == $data['blog_category_id']) {
				$children = $this->model_extension_blogcategory->getBlogCategories($blogcategory['blog_category_id']);

				foreach($children as $child){
					$filter_data = array(
						'filter_blog_category_id' => $child['blog_category_id'], 'filter_sub_blogcategory' => true,
					);

					$children_data[] = array(
						'blog_category_id' => $child['blog_category_id'],
						'name' => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_extension_blog->getTotalblogs($filter_data) . ')' : ''),
						'href' => $this->url->link('extension/blogcategory', 'bpath=' . $blogcategory['blog_category_id'] . '_' . $child['blog_category_id'])
					);
				}
			}

			$filter_data = array(
				'filter_blog_category_id'  => $blogcategory['blog_category_id'],
				'filter_sub_blogcategory' => true
			);

			$data['categories'][] = array(
				'blog_category_id' => $blogcategory['blog_category_id'],
				'name'        => $blogcategory['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_extension_blog->getTotalblogs($filter_data) . ')' : ''),
				'children'    => $children_data,
				'href'        => $this->url->link('extension/blogcategory', 'bpath=' . $blogcategory['blog_category_id'])
			);
		}
		
				return $this->load->view('extension/module/blogcategory', $data);
		
		
	}
}