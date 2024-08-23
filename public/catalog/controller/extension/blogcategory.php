<?php
class ControllerExtensionBlogcategory extends Controller {
	public function index() {
		$this->load->language('extension/blog_category');

		$this->load->model('extension/blogcategory');

		$this->load->model('extension/blog');

		$this->load->model('tool/image');
		
		$this->document->addStyle('catalog/view/javascript/wblog/css/style.css');

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
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		if (isset($this->request->get['bpath'])) {
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

			$bpath = '';

			$parts = explode('_', (string)$this->request->get['bpath']);

			$blog_category_id = (int)array_pop($parts);

			foreach ($parts as $bpath_id) {
				if (!$bpath) {
					$bpath = (int)$bpath_id;
				} else {
					$bpath .= '_' . (int)$bpath_id;
				}

				$blog_category_info = $this->model_extension_blogcategory->getblog_category($bpath_id);

				if ($blog_category_info) {
					$data['breadcrumbs'][] = array(
						'text' => $blog_category_info['name'],
						'href' => $this->url->link('extension/blogcategory', 'bpath=' . $bpath . $url)
					);
				}
			}
		} else {
			$blog_category_id = 0;
		}

		$blog_category_info = $this->model_extension_blogcategory->getblog_category($blog_category_id);
		
		if ($blog_category_info) {
			$this->document->setTitle($blog_category_info['meta_title']);
			$this->document->setDescription($blog_category_info['meta_description']);
			$this->document->setKeywords($blog_category_info['meta_keyword']);
			$this->document->addLink($this->url->link('extension/blogcategory', 'bpath=' . $this->request->get['bpath']), 'canonical');

			$data['heading_title'] = $blog_category_info['name'];

			$data['text_refine'] = $this->language->get('text_refine');
			$data['text_empty'] = $this->language->get('text_empty');
			$data['text_quantity'] = $this->language->get('text_quantity');
			$data['text_manufacturer'] = $this->language->get('text_manufacturer');
			$data['text_model'] = $this->language->get('text_model');
			$data['text_price'] = $this->language->get('text_price');
			$data['text_tax'] = $this->language->get('text_tax');
			$data['text_points'] = $this->language->get('text_points');
			$data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
			$data['text_sort'] = $this->language->get('text_sort');
			$data['text_limit'] = $this->language->get('text_limit');
			$data['text_like']    		= $this->language->get('text_like');
			$data['text_postby']    	= $this->language->get('text_postby');
			$data['text_published']     = $this->language->get('text_published');
			$data['text_time_read']     = $this->language->get('text_time_read');
			$data['text_comment']    	= $this->language->get('text_comment');

			$data['button_cart'] = $this->language->get('button_cart');
			$data['button_wishlist'] = $this->language->get('button_wishlist');
			$data['button_compare'] = $this->language->get('button_compare');
			$data['button_continue'] = $this->language->get('button_continue');
			$data['button_list'] = $this->language->get('button_list');
			$data['button_grid'] = $this->language->get('button_grid');
			
			
			$language_id = $this->config->get('config_language_id');
			if ($this->config->get('blogsetting_menu' . $language_id)) {
				$data['blogsetting_menu'] = $this->config->get('blogsetting_menu' . $language_id);
			} else {
				$data['blogsetting_menu'] = '';
			}
				
			if ($this->config->get('blogsetting_postby' . $language_id)) {
				$data['blogsetting_postby'] = $this->config->get('blogsetting_postby' . $language_id);
			} else {
				$data['blogsetting_postby'] = 'Post By :';
			}
			
			if ($this->config->get('blogsetting_on' . $language_id)) {
				$data['blogsetting_on'] = $this->config->get('blogsetting_on' . $language_id);
			} else {
				$data['blogsetting_on'] = '';
			}
			
			if ($this->config->get('blogsetting_readmore' . $language_id)) {
				$data['blogsetting_readmore'] = $this->config->get('blogsetting_readmore' . $language_id);
			} else {
				$data['blogsetting_readmore'] = '';
			}

			// Set the last blog_category breadcrumb
			$data['breadcrumbs'][] = array(
				'text' => $blog_category_info['name'],
				'href' => $this->url->link('extension/blogcategory', 'bpath=' . $this->request->get['bpath'])
			);

			if ($blog_category_info['image']) {
				$data['thumb'] = $this->model_tool_image->resize($blog_category_info['image'], 350, 350);
			} else {
				$data['thumb'] = '';
			}

			$data['description'] = html_entity_decode($blog_category_info['description'], ENT_QUOTES, 'UTF-8');
			$data['compare'] = $this->url->link('extension/compare');

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

			$results = $this->model_extension_blogcategory->getBlogCategories($blog_category_id);

			foreach($results as $result){
				$filter_data = array(
					'filter_blog_category_id'  => $result['blog_category_id'],
					'filter_sub_blog_category' => true
				);

				$data['categories'][] = array(
					'name'  => $result['name'] . ($this->config->get('config_blog_count') ? ' (' . $this->model_catalog_blog->getTotalblogs($filter_data) . ')' : ''),
					'href'  => $this->url->link('extension/blogcategory', 'bpath=' . $this->request->get['bpath'] . '_' . $result['blog_category_id'] . $url)
				);
			}
			
			$listingsetting = array();
			$blogsetting = $this->config->get('blogsetting');
			if(isset($blogsetting['category_listing'])){
			  $listingsetting   = $blogsetting['category_listing'];
			}
			
			if(!empty($listingsetting['thumbsize_w'])){
				$thumbsize_w = trim($listingsetting['thumbsize_w']);
			}else{
				$thumbsize_w  = 350;
			}
			
			if(!empty($listingsetting['thumbsize_h'])){
				$thumbsize_h = trim($listingsetting['thumbsize_h']);
			}else{
				$thumbsize_h  = 200;
			}
			
			if(!empty($listingsetting['layout_id'])){
				$data['category_layout'] = $listingsetting['layout_id'];
			}else{
				$data['category_layout']  = 1;
			}
			
			if(!empty($listingsetting['limit_row'])){
				$data['limit_row'] = $listingsetting['limit_row'];
			}else{
				$data['limit_row'] = 1;
			}
			
			if(!empty($listingsetting['limit'])){
				$limit = $listingsetting['limit'];
			}else{
				$limit = 10;
			}
			
			if(!empty($listingsetting['showtitle'])){
				$data['showtitle'] = $listingsetting['showtitle'];
			}else{
				$data['showtitle'] = '';
			}
			
			if(!empty($listingsetting['showdescription'])){
				$data['showdescription'] = $listingsetting['showdescription'];
			}else{
				$data['showdescription'] = '';
			}
			
			if(!empty($listingsetting['show_thumb'])){
				$data['show_thumb'] = $listingsetting['show_thumb'];
			}else{
				$data['show_thumb'] = '';
			}
			
			if(!empty($listingsetting['show_publishdate'])){
				$data['show_publishdate'] = $listingsetting['show_publishdate'];
			}else{
				$data['show_publishdate'] = '';
			}
			
			if(!empty($listingsetting['show_totalview'])){
				$data['show_totalview'] = $listingsetting['show_totalview'];
			}else{
				$data['show_totalview'] = '';
			}
			
			if(!empty($listingsetting['show_author'])){
				$data['show_author'] = $listingsetting['show_author'];
			}else{
				$data['show_author'] = '';
			}
			
			if(!empty($listingsetting['show_like'])){
				$data['show_like'] = $listingsetting['show_like'];
			}else{
				$data['show_like'] = '';
			}
			
			if(!empty($listingsetting['comment'])){
				$data['comment'] = $listingsetting['comment'];
			}else{
				$data['comment'] = '';
			}
			
			$data['blogs'] = array();

			$filter_data = array(
				'filter_blog_category_id' => $blog_category_id,
				'sort'               	  => $sort,
				'order'              	  => $order,
				'start'              	  => ($page - 1) * $limit,
				'limit'              	  => $limit
			);

			$blog_total = $this->model_extension_blog->getTotalblogs($filter_data);
			
			$results = $this->model_extension_blog->getblogs($filter_data);

			foreach($results as $result){
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'],$thumbsize_w,$thumbsize_h);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png',$thumbsize_w,$thumbsize_h);
				}

				$data['blogs'][] = array(
					'post_id'  	  		=> $result['post_id'],
					'image'       		=> $image,
					'name'        		=> $result['name'],
					'post_type'        	=> $result['post_type'],
					'video_url'        	=> $result['video_url'],
					'comments'    		=> $result['comments'],
					'likes'    	  		=> $result['likes'],
					'username'    		=> $result['username'],
					'viewed'    		=> $result['viewed'],
					'short_description' => $result['short_description'],
					'date' 		=> date($this->language->get('date_format_short'),strtotime($result['date'])),
					'name'        		=> $result['name'],
					'href'        		=> $this->url->link('extension/post', 'bpath=' . $this->request->get['bpath'] . '&post_id=' . $result['post_id'] . $url)
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
				'href'  => $this->url->link('extension/blogcategory', 'bpath=' . $this->request->get['bpath'] . '&sort=p.sort_order&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_asc'),
				'value' => 'pd.name-ASC',
				'href'  => $this->url->link('extension/blogcategory', 'bpath=' . $this->request->get['bpath'] . '&sort=pd.name&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_desc'),
				'value' => 'pd.name-DESC',
				'href'  => $this->url->link('extension/blogcategory', 'bpath=' . $this->request->get['bpath'] . '&sort=pd.name&order=DESC' . $url)
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

			$limits = array_unique(array($this->config->get('config_blog_limit'), 25, 50, 75, 100));

			sort($limits);

			foreach($limits as $value) {
				$data['limits'][] = array(
					'text'  => $value,
					'value' => $value,
					'href'  => $this->url->link('extension/blogcategory', 'bpath=' . $this->request->get['bpath'] . $url . '&limit=' . $value)
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
			$pagination->total = $blog_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('extension/blogcategory', 'bpath=' . $this->request->get['bpath'] . $url . '&page={page}');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($blog_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($blog_total - $limit)) ? $blog_total : ((($page - 1) * $limit) + $limit), $blog_total, ceil($blog_total / $limit));

			$data['sort'] = $sort;
			$data['order'] = $order;
			$data['limit'] = $limit;

			$data['continue'] = $this->url->link('common/home');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			
			
					$this->response->setOutput($this->load->view('extension/blogcategory', $data));
			
		} else {
			$url = '';

			if (isset($this->request->get['bpath'])) {
				$url .= '&bpath=' . $this->request->get['bpath'];
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
				'href' => $this->url->link('blog/blogcategory', $url)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');

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