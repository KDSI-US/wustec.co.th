<?php
class ControllerExtensionModuleMpBlogCategory extends Controller {
	public function index() {
		if ($this->config->get('mpblog_status')) {
			$this->load->language('extension/module/mpblogcategory');

			$data['heading_title'] = $this->language->get('heading_title');

			if (isset($this->request->get['mpcpath'])) {
				$parts = explode('_', (string)$this->request->get['mpcpath']);
			} else {
				$parts = [];
			}

			if (isset($parts[0])) {
				$data['mpblogcategory_id'] = $parts[0];
			} else {
				$data['mpblogcategory_id'] = 0;
			}

			if (isset($parts[1])) {
				$data['child_id'] = $parts[1];
			} else {
				$data['child_id'] = 0;
			}

			$this->load->model('extension/mpblog/mpblogcategory');

			$this->load->model('extension/mpblog/mpblogpost');

			$data['mpblogcategories'] = [];

			$mpblogcategories = $this->model_extension_mpblog_mpblogcategory->getMpBlogCategories(0);

			foreach ($mpblogcategories as $mpblogcategory) {
				$children_data = [];

				if ($mpblogcategory['mpblogcategory_id'] == $data['mpblogcategory_id']) {
					$children = $this->model_extension_mpblog_mpblogcategory->getMpBlogCategories($mpblogcategory['mpblogcategory_id']);

					foreach ($children as $child) {
						$filter_data = ['filter_mpblogcategory_id' => $child['mpblogcategory_id'], 'filter_sub_mpblogcategory' => false];

						$children_data[] = [
							'mpblogcategory_id' => $child['mpblogcategory_id'],
							'name' => $child['name'] . ($this->config->get('mpblog_category_post_count') ? ' (' . $this->model_extension_mpblog_mpblogpost->getTotalMpBlogPosts($filter_data) . ')' : ''),
							'href' => $this->url->link('extension/mpblog/blogcategory', 'mpcpath=' . $mpblogcategory['mpblogcategory_id'] . '_' . $child['mpblogcategory_id'])
						];
					}
				}

				$filter_data = [
					'filter_mpblogcategory_id'  => $mpblogcategory['mpblogcategory_id'],
					'filter_sub_mpblogcategory' => false
				];

				$data['mpblogcategories'][] = [
					'mpblogcategory_id' => $mpblogcategory['mpblogcategory_id'],
					'name'        => $mpblogcategory['name'] . ($this->config->get('mpblog_category_post_count') ? ' (' . $this->model_extension_mpblog_mpblogpost->getTotalMpBlogPosts($filter_data) . ')' : ''),
					'children'    => $children_data,
					'href'        => $this->url->link('extension/mpblog/blogcategory', 'mpcpath=' . $mpblogcategory['mpblogcategory_id'])
				];
			}

			return $this->load->view('extension/module/mpblogcategory', $data);
		}
	}
}