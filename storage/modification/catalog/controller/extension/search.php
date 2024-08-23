<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionSearch extends Controller {
	public function index() {
		$this->load->language('extension/search');

		$this->load->model('extension/blogcategory');

		$this->load->model('extension/blog');

		$this->load->model('tool/image');
		
		$this->document->addStyle('catalog/view/javascript/wblog/css/style.css');

		if (isset($this->request->get['bsearch'])) {
			$search = $this->request->get['bsearch'];
		} else {
			$search = '';
		}

		if (isset($this->request->get['tag'])) {
			$tag = $this->request->get['tag'];
		} elseif (isset($this->request->get['bsearch'])) {
			$tag = $this->request->get['bsearch'];
		} else {
			$tag = '';
		}

		if (isset($this->request->get['description'])) {
			$description = $this->request->get['description'];
		} else {
			$description = '';
		}

		if (isset($this->request->get['blog_category_id'])) {
			$blog_category_id = $this->request->get['blog_category_id'];
		} else {
			$blog_category_id = 0;
		}

		if (isset($this->request->get['sub_blogcategory'])) {
			$sub_blogcategory = $this->request->get['sub_blogcategory'];
		} else {
			$sub_blogcategory = '';
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

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = $this->config->get('config_post_limit');
		}

		if(isset($this->request->get['bsearch'])){
			$this->document->setTitle($this->language->get('heading_title') .  ' - ' . $this->request->get['bsearch']);
		}elseif(isset($this->request->get['tag'])){
			$this->document->setTitle($this->language->get('heading_title') .  ' - ' . $this->language->get('heading_tag') . $this->request->get['tag']);
		} else {
			$this->document->setTitle($this->language->get('heading_title'));
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$url = '';

		if (isset($this->request->get['bsearch'])) {
			$url .= '&search=' . urlencode(html_entity_decode($this->request->get['bsearch'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['tag'])) {
			$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['description'])) {
			$url .= '&description=' . $this->request->get['description'];
		}

		if (isset($this->request->get['blog_category_id'])) {
			$url .= '&blog_category_id=' . $this->request->get['blog_category_id'];
		}

		if (isset($this->request->get['sub_blogcategory'])) {
			$url .= '&sub_blogcategory=' . $this->request->get['sub_blogcategory'];
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
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/search', $url)
		);

		if (isset($this->request->get['bsearch'])) {
			$data['heading_title'] = $this->language->get('heading_title') .  ' - ' . $this->request->get['bsearch'];
		} else {
			$data['heading_title'] = $this->language->get('heading_title');
		}

		$data['text_empty'] = $this->language->get('text_empty');
		$data['text_search'] = $this->language->get('text_search');
		$data['text_keyword'] = $this->language->get('text_keyword');
		$data['text_category'] = $this->language->get('text_category');
		$data['text_sub_category'] = $this->language->get('text_sub_category');
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

		$data['entry_search'] = $this->language->get('entry_search');
		$data['entry_description'] = $this->language->get('entry_description');

		$data['button_search'] = $this->language->get('button_search');
		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');
		$data['button_list'] = $this->language->get('button_list');
		$data['button_grid'] = $this->language->get('button_grid');

		$data['compare'] = $this->url->link('extension/compare');
		
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

		$this->load->model('extension/blogcategory');

		// 3 Level blogcategory Search
		$data['categories'] = array();

		$categories_1 = $this->model_extension_blogcategory->getBlogCategories(0);

		foreach ($categories_1 as $blogcategory_1) {
			$level_2_data = array();

			$categories_2 = $this->model_extension_blogcategory->getBlogCategories($blogcategory_1['blog_category_id']);

			foreach($categories_2 as $blogcategory_2){
				$level_3_data = array();

				$categories_3 = $this->model_extension_blogcategory->getBlogCategories($blogcategory_2['blog_category_id']);

				foreach ($categories_3 as $blogcategory_3) {
					$level_3_data[] = array(
						'blog_category_id' => $blogcategory_3['blog_category_id'],
						'name'        => $blogcategory_3['name'],
					);
				}

				$level_2_data[] = array(
					'blog_category_id' => $blogcategory_2['blog_category_id'],
					'name'        => $blogcategory_2['name'],
					'children'    => $level_3_data
				);
			}

			$data['categories'][] = array(
				'blog_category_id' => $blogcategory_1['blog_category_id'],
				'name'        => $blogcategory_1['name'],
				'children'    => $level_2_data
			);
		}

		$data['blogs'] = array();
		$listingsetting = array();
		$blogsetting = $this->config->get('blogsetting');
		if(isset($blogsetting['search_listing'])){
		  $search_listing   = $blogsetting['search_listing'];
		}
	
		
		if(!empty($search_listing['layout_id'])){
			$data['layout_id'] = trim($search_listing['layout_id']);
		}else{
			$data['layout_id']  = 350;
		}
		
		
		if(!empty($search_listing['thumbsize_w'])){
			$thumbsize_w = trim($search_listing['thumbsize_w']);
		}else{
			$thumbsize_w  = 350;
		}
		
		if(!empty($search_listing['thumbsize_h'])){
			$thumbsize_h = trim($search_listing['thumbsize_h']);
		}else{
			$thumbsize_h  = 200;
		}
		
		if(!empty($search_listing['limit_row'])){
			$data['limit_row'] = $search_listing['limit_row'];
		}else{
			$data['limit_row'] = 0;
		}
		
		if(!empty($search_listing['limit'])){
			$limit = $search_listing['limit'];
		}else{
			$limit = 10;
		}
		
		if(!empty($search_listing['showtitle'])){
			$data['showtitle'] = $search_listing['showtitle'];
		}else{
			$data['showtitle'] = '';
		}
		
		if(!empty($search_listing['showdescription'])){
			$data['showdescription'] = $search_listing['showdescription'];
		}else{
			$data['showdescription'] = '';
		}
			
		if(!empty($search_listing['show_thumb'])){
			$data['show_thumb'] = $search_listing['show_thumb'];
		}else{
			$data['show_thumb'] = '';
		}
			
		if(!empty($search_listing['show_publishdate'])){
			$data['show_publishdate'] = $search_listing['show_publishdate'];
		}else{
			$data['show_publishdate'] = '';
		}
			
		if(!empty($search_listing['show_totalview'])){
			$data['show_totalview'] = $search_listing['show_totalview'];
		}else{
			$data['show_totalview'] = '';
		}
			
		if(!empty($search_listing['show_author'])){
			$data['show_author'] = $search_listing['show_author'];
		}else{
			$data['show_author'] = '';
		}
			
		if(!empty($search_listing['show_like'])){
			$data['show_like'] = $search_listing['show_like'];
		}else{
			$data['show_like'] = '';
		}
			
		if(!empty($search_listing['comment'])){
			$data['comment'] = $search_listing['comment'];
		}else{
			$data['comment'] = '';
		}
		if (isset($this->request->get['bsearch']) || isset($this->request->get['tag'])) {
			$filter_data = array(
				'filter_name'         => $search,
				'filter_tag'          => $tag,
				'filter_description'  => $description,
				'filter_blog_category_id'  => $blog_category_id,
				'filter_sub_blogcategory' => $sub_blogcategory,
				'sort'                => $sort,
				'order'               => $order,
				'start'               => ($page - 1) * $limit,
				'limit'               => $limit
			);
			
			$post_total = $this->model_extension_blog->getTotalblogs($filter_data);

			$results = $this->model_extension_blog->getblogs($filter_data);
			
			foreach($results as $result){
				if($result['image']){
					$image = $this->model_tool_image->resize($result['image'],$thumbsize_w,$thumbsize_h);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png',$thumbsize_w,$thumbsize_h);
				}

				$data['blogs'][] = array(
					'post_id'  			=> $result['post_id'],
					'image'       		=> $image,
					'post_type'        	=> $result['post_type'],
					'video_url'        	=> $result['video_url'],
					'comments'    		=> $result['comments'],
					'likes'    	  		=> $result['likes'],
					'username'    		=> $result['username'],
					'viewed'    		=> $result['viewed'],
					'short_description' 		=> $result['short_description'],
					'date' 		=> date($this->language->get('date_format_short'),strtotime($result['date'])),
					'name'        		=> $result['name'],
					'href'        		=> $this->url->link('extension/post', 'post_id=' . $result['post_id'] . $url)
				);
			}

			$url = '';

			if (isset($this->request->get['bsearch'])) {
				$url .= '&search=' . urlencode(html_entity_decode($this->request->get['bsearch'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['blog_category_id'])) {
				$url .= '&blog_category_id=' . $this->request->get['blog_category_id'];
			}

			if (isset($this->request->get['sub_blogcategory'])) {
				$url .= '&sub_blogcategory=' . $this->request->get['sub_blogcategory'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}


			$url = '';

			if (isset($this->request->get['bsearch'])) {
				$url .= '&search=' . urlencode(html_entity_decode($this->request->get['bsearch'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['blog_category_id'])) {
				$url .= '&blog_category_id=' . $this->request->get['blog_category_id'];
			}

			if (isset($this->request->get['sub_blogcategory'])) {
				$url .= '&sub_blogcategory=' . $this->request->get['sub_blogcategory'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			$url = '';

			if (isset($this->request->get['bsearch'])) {
				$url .= '&search=' . urlencode(html_entity_decode($this->request->get['bsearch'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['blog_category_id'])) {
				$url .= '&blog_category_id=' . $this->request->get['blog_category_id'];
			}

			if (isset($this->request->get['sub_blogcategory'])) {
				$url .= '&sub_blogcategory=' . $this->request->get['sub_blogcategory'];
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
			$pagination->total = $post_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('extension/search', $url . '&page={page}');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($post_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($post_total - $limit)) ? $post_total : ((($page - 1) * $limit) + $limit), $post_total, ceil($post_total / $limit));

			// http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
			if ($page == 1) {
			    $this->document->addLink($this->url->link('extension/search', '', 'SSL'), 'canonical');
			} elseif ($page == 2) {
			    $this->document->addLink($this->url->link('extension/search', '', 'SSL'), 'prev');
			} else {
			    $this->document->addLink($this->url->link('extension/search', $url . '&page='. ($page - 1), 'SSL'), 'prev');
			}

			if ($limit && ceil($post_total / $limit) > $page) {
			    $this->document->addLink($this->url->link('extension/search', $url . '&page='. ($page + 1), 'SSL'), 'next');
			}
		}

		$data['search'] = $search;
		$data['description'] = $description;
		$data['blog_category_id'] = $blog_category_id;
		$data['sub_category'] = $sub_blogcategory;

		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['limit'] = $limit;

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		
				$this->response->setOutput($this->load->view('extension/search', $data));
		
	}
}