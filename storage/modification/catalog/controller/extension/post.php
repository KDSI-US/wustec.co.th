<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionPost extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/post');
		$this->document->addStyle('catalog/view/javascript/wblog/css/style.css');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$this->load->model('catalog/category');

		if (isset($this->request->get['bpath'])) {
			 $bpath = '';

			$parts = explode('_', (string)$this->request->get['bpath']);

			$category_id = (int)array_pop($parts);

			foreach ($parts as $bpath_id) {
				if (!$bpath) {
					$bpath = $bpath_id;
				} else {
					$bpath .= '_' . $bpath_id;
				}

				$category_info = $this->model_catalog_category->getCategory($bpath_id);

				if ($category_info) {
					$data['breadcrumbs'][] = array(
						'text' => $category_info['name'],
						'href' => $this->url->link('product/category', 'bpath=' . $bpath)
					);
				}
			}

			// Set the last category breadcrumb
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$url = '';

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
					'text' => $category_info['name'],
					'href' => $this->url->link('product/category', 'bpath=' . $this->request->get['bpath'] . $url)
				);
			} 
		}

		if (isset($this->request->get['search']) || isset($this->request->get['tag'])) {
			$url = '';

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
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
				'text' => $this->language->get('text_search'),
				'href' => $this->url->link('extension/search', $url)
			);
		}

		if (isset($this->request->get['post_id'])) {
			$post_id = (int)$this->request->get['post_id'];
		} else {
			$post_id = 0;
		}

		$this->load->model('extension/blog');

		$post_info = $this->model_extension_blog->getblog($post_id);
		if($post_info){
			$url = '';

			if (isset($this->request->get['bpath'])) {
				$url .= '&bpath=' . $this->request->get['bpath'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
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
				'text' => $post_info['name'],
				'href' => $this->url->link('extension/post', $url . '&post_id=' . $this->request->get['post_id'])
			);

			$this->document->setTitle($post_info['meta_title']);
			$this->document->setDescription($post_info['meta_description']);
			$this->document->setKeywords($post_info['meta_keyword']);
			$this->document->addLink($this->url->link('extension/post', 'post_id=' . $this->request->get['post_id']), 'canonical');
			
			$data['heading_title'] 		= $post_info['name'];
			$data['username'] 	    	= $post_info['username'];
			$data['viewed'] 	    	= $post_info['viewed'];
			$data['totallike'] 	    	= $post_info['likes'];
			$data['totalcomment'] 	    = $post_info['comments'];
			$data['post_type']      	= $post_info['post_type'];
			$data['video_url']        	= $post_info['video_url'];
			
			if ($this->request->server['HTTPS']) {
				$server = $this->config->get('config_ssl');
			} else {
				$server = $this->config->get('config_url');
			}
			
			if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
				$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
			} else {
				$data['logo'] = '';
			}
			
			$data['storename'] = $this->config->get('config_name');
			
			$data['publisheddate'] 	    = date($this->language->get('date_format_short'),strtotime($post_info['date']));
			$data['publisheddatemodified'] 	    = date($this->language->get('date_format_short'),strtotime($post_info['date_modified']));
			$data['blog_url'] 	    = $this->url->link('extension/post', '&post_id=' . $this->request->get['post_id']);
			$data['text_select']   		= $this->language->get('text_select');
			$data['text_write']    		= $this->language->get('text_write');
			$data['text_login']    		= sprintf($this->language->get('text_login'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL'));
			
			
			$data['text_like']    		= $this->language->get('text_like');
			$data['text_postby']    	= $this->language->get('text_postby');
			$data['text_published']     = $this->language->get('text_published');
			$data['text_time_read']     = $this->language->get('text_time_read');
			$data['text_comment']    	= $this->language->get('text_comment');
			$data['text_note']     		= $this->language->get('text_note');
			$data['text_tags']     		= $this->language->get('text_tags');
			$data['text_related']  		= $this->language->get('text_related');
			$data['text_loading'] 	 	= $this->language->get('text_loading');
			$data['text_tax'] 	 		= $this->language->get('text_tax');
			$data['text_related_post'] 	= $this->language->get('text_related_post');
			
			$data['entry_name']    		= $this->language->get('entry_name');
			$data['entry_email']    	= $this->language->get('entry_email');
			$data['entry_comment']    	= $this->language->get('entry_comment');
			
			$data['button_cart']    	= $this->language->get('button_cart');
			$data['button_continue']    = $this->language->get('button_continue');
			$data['button_wishlist']    = $this->language->get('button_wishlist');
			$data['button_compare']    = $this->language->get('button_compare');
			
			$data['post_id']       		= (int)$this->request->get['post_id'];
			
			$data['customername'] = '';
			$data['customeremail'] = '';
			if($this->customer->isLogged()){
				$data['customername'] = $this->customer->getFirstName().' '.$this->customer->getLastName();
				$data['customeremail'] = $this->customer->getEmail();
			}
			
			$this->load->model('tool/image');
			
			$postpagesetting = array();
			$blogsetting = $this->config->get('blogsetting');
			if(isset($blogsetting['blog'])){
			  $postpagesetting   = $blogsetting['blog'];
			}
			
			if(isset($blogsetting['blog'])){
			$data['mlisting_layout'] = $blogsetting['blog']['layout_id'];
			}else{
			$data['mlisting_layout'] = '1';
			}
			
			if(isset($blogsetting['related_listing'])){
			$data['mrelated_listing'] = $blogsetting['related_listing']['layout_id'];
			}else{
			$data['mrelated_listing'] = '1';
			}
			
			if(isset($postpagesetting['thumbsize_w'])){
			 $thumbsize_w = $postpagesetting['thumbsize_w'];
			}else{
			 $thumbsize_w  = 500;
			}
			
			if(isset($postpagesetting['thumbsize_h'])){
			 $thumbsize_h = $postpagesetting['thumbsize_h'];
			}else{
			 $thumbsize_h  = 500;
			}
			
			if(!empty($postpagesetting['showthumb'])){
			   $data['mshowthumb'] = $postpagesetting['showthumb'];
			}else{
			   $data['mshowthumb'] = 0;
			}
			
			if(!empty($postpagesetting['show_publishdate'])){
			   $data['mshow_publishdate'] = $postpagesetting['show_publishdate'];
			}else{
			   $data['mshow_publishdate'] = 0;
			}
			
			if(!empty($postpagesetting['show_totalview'])){
			   $data['mshow_totalview'] = $postpagesetting['show_totalview'];
			}else{
			   $data['mshow_totalview'] = 0;
			}
			
			if(!empty($postpagesetting['show_author'])){
			   $data['mshow_author'] = $postpagesetting['show_author'];
			}else{
			   $data['mshow_author'] = 0;
			}
			
			if(!empty($postpagesetting['show_like'])){
			   $data['mshow_like'] = $postpagesetting['show_like'];
			}else{
			   $data['mshow_like'] = 0;
			}
			
			if(!empty($postpagesetting['allowlike'])){
			   $data['mallowlike'] = $postpagesetting['allowlike'];
			}else{
			   $data['mallowlike'] = 0;
			}
			
			if(!empty($postpagesetting['show_social_share'])){
			   $data['mshow_social_share'] = $postpagesetting['show_social_share'];
			}else{
			   $data['mshow_social_share'] = 0;
			}
			
			if(!empty($postpagesetting['allow_postcomment'])){
			   $data['mallow_postcomment'] = $postpagesetting['allow_postcomment'];
			}else{
			   $data['mallow_postcomment'] = 0;
			}
			
			if(!empty($postpagesetting['allow_guestcomment'])){
			   $allow_guestcomment = $postpagesetting['allow_guestcomment'];
			}else{
			   $allow_guestcomment = 0;
			}
			
			if(!empty($postpagesetting['snippet'])){
			   $data['snippet'] = $postpagesetting['snippet'];
			}else{
			   $data['snippet'] = 0;
			}
			
			if($allow_guestcomment){
				$data['guestcomment'] = true;
			}else{
				if(!$this->customer->isLogged()){
					$data['guestcomment'] = false;
				}else{
					$data['guestcomment'] = true;
				}
			}
			
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
			
			$data['allowcommentbypostid'] = $post_info['allowcoment'];
			
			
			if(!empty($postpagesetting['show_total_comment'])){
			   $data['mshow_total_comment'] = $postpagesetting['show_total_comment'];
			}else{
			   $data['mshow_total_comment'] = 0;
			}
			
			if(!empty($postpagesetting['display_comment'])){
			   $data['mdisplay_comment'] = $postpagesetting['display_comment'];
			}else{
			   $data['mdisplay_comment'] = 0;
			}

			
			if($post_info['image']){
			  $data['thumb'] = $this->model_tool_image->resize($post_info['image'], $thumbsize_w, $thumbsize_h);
			}else{
			  $data['thumb'] = '';
			}
			
			//$this->document->setThumb($this->model_tool_image->getImageUrl($post_info['image']));
			
			$data['hasliked'] = $this->model_extension_blog->checklike($this->request->get['post_id']);
			

			$data['images'] = array();

			
			$data['description'] = html_entity_decode($post_info['description'], ENT_QUOTES, 'UTF-8');
			
			$relatedsetting = array();
			if(isset($blogsetting['related_listing'])){
			  $relatedsetting  = $blogsetting['related_listing'];
			}
			
			if(!empty($relatedsetting['thumbsize_w'])){
				$relatedwidth = $relatedsetting['thumbsize_w'];
			}else{
				$relatedwidth = 200;
			}
			
			if(!empty($relatedsetting['thumbsize_h'])){
				$relatedheight = $relatedsetting['thumbsize_h'];
			}else{
				$relatedheight = 200;
			}
			
			if(!empty($relatedsetting['limit_row'])){
				$data['listing_row'] = $relatedsetting['limit_row'];
			}else{
				$data['listing_row'] = 3;
			}
			
			if(isset($relatedsetting['limit_row'])){
			$data['limit_row'] = $blogsetting['listing']['limit_row'];
			}else{
			$data['limit_row'] = 2;
			}
			
			if(!empty($relatedsetting['showtitle'])){
				$data['showtitle'] = $relatedsetting['showtitle'];
			}else{
				$data['showtitle'] = '';
			}
			
			if(!empty($relatedsetting['showdescription'])){
				$data['showdescription'] = $relatedsetting['showdescription'];
			}else{
				$data['showdescription'] = '';
			}
			
			if(!empty($relatedsetting['show_thumb'])){
				$data['show_thumb'] = $relatedsetting['show_thumb'];
			}else{
				$data['show_thumb'] = '';
			}
			
			if(!empty($relatedsetting['show_publishdate'])){
				$data['show_publishdate'] = $relatedsetting['show_publishdate'];
			}else{
				$data['show_publishdate'] = '';
			}
			
			if(!empty($relatedsetting['show_totalview'])){
				$data['show_totalview'] = $relatedsetting['show_totalview'];
			}else{
				$data['show_totalview'] = '';
			}
			
			if(!empty($relatedsetting['show_author'])){
				$data['show_author'] = $relatedsetting['show_author'];
			}else{
				$data['show_author'] = '';
			}
			
			if(!empty($relatedsetting['show_like'])){
				$data['show_like'] = $relatedsetting['show_like'];
			}else{
				$data['show_like'] = '';
			}
			
			if(!empty($relatedsetting['comment'])){
				$data['comment'] = $relatedsetting['comment'];
			}else{
				$data['comment'] = '';
			}
			
			$data['blogs'] = array();

			$results = $this->model_extension_blog->getPostRelated($this->request->get['post_id']);

			
			foreach ($results as $result){
				if($result['image']){
					$image = $this->model_tool_image->resize($result['image'], $relatedwidth, $relatedheight);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png',$relatedwidth, $relatedheight);
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
					'date' 				=> date($this->language->get('date_format_short'),strtotime($result['date'])),
					'href'        		=> $this->url->link('extension/post', 'post_id=' . $result['post_id'])
				);
			}
			
			
			$data['products'] = array();

			$results = $this->model_extension_blog->getProductRelated($this->request->get['post_id']);

			foreach ($results as $result){
					if ($result['image']) {
						$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
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
					$product_description = utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..';
					
				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}

				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => $product_description,
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $rating,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}

			$data['tags'] = array();

			if ($post_info['tag']) {
				$tags = explode(',', $post_info['tag']);

				foreach ($tags as $tag) {
					$data['tags'][] = array(
						'tag'  => trim($tag),
						'href' => $this->url->link('extension/search', 'tag=' . trim($tag))
					);
				}
			}

			$this->model_extension_blog->updateViewed($this->request->get['post_id']);

			$data['login'] = $this->load->controller('extension/login');
			$data['signup'] = $this->load->controller('extension/signup');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

					$this->response->setOutput($this->load->view('extension/post', $data));
		
			
		} else {
			$url = '';

			if (isset($this->request->get['bpath'])) {
				$url .= '&bpath=' . $this->request->get['bpath'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
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
				'href' => $this->url->link('extension/post', $url . '&post_id=' . $post_id)
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

	public function review() {
		$this->load->language('extension/post');

		$this->load->model('extension/blog');

		$data['text_no_reviews'] = $this->language->get('text_no_reviews');
		$data['text_user_comments'] = $this->language->get('text_user_comments');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$postpagesetting = array();
		$blogsetting = $this->config->get('blogsetting');
		if(isset($blogsetting['blog'])){
		  $postpagesetting   = $blogsetting['blog'];
		}
		
		if(isset($postpagesetting['comment_limit'])){
		 $comment_limit = $postpagesetting['comment_limit'];
		}else{
		 $comment_limit  = 10;
		}

		$data['reviews'] = array();

		$review_total = $this->model_extension_blog->getTotalCommentByPostId($this->request->get['post_id']);

		$results = $this->model_extension_blog->getCommentByPostId($this->request->get['post_id'], ($page - 1) * $comment_limit, $comment_limit);

		foreach ($results as $result){
			$data['reviews'][] = array(
				'author'     => $result['author'],
				'text'       => nl2br($result['text']),
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$pagination = new Pagination();
		$pagination->total = $review_total;
		$pagination->page = $page;
		$pagination->limit = $comment_limit;
		$pagination->url = $this->url->link('extension/post/review', 'post_id=' . $this->request->get['post_id'] . '&page={page}');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * $comment_limit) + 1 : 0, ((($page - 1) * $comment_limit) > ($review_total - $comment_limit)) ? $review_total : ((($page - 1) * $comment_limit) + $comment_limit), $review_total, ceil($review_total / $comment_limit));

				$this->response->setOutput($this->load->view('extension/comment', $data));
		}
	

	public function write(){
		$this->load->language('extension/post');

		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25)) {
				$json['error'] = $this->language->get('error_name');
			}
			
			if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email'])) {
				$json['error'] = $this->language->get('error_email');
			}

			if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
				$json['error'] = $this->language->get('error_text');
			}

			if (!isset($json['error'])){
				$this->load->model('extension/blog');

				$this->model_extension_blog->addComment($this->request->get['post_id'], $this->request->post);
				$blogsetting = $this->config->get('blogsetting');
				if(isset($blogsetting['blog'])){
					$postpagesetting   = $blogsetting['blog'];
				}
				if(!empty($postpagesetting['comment_permission'])){
					$json['success'] = $this->language->get('text_success1');
				}else{
					$json['success'] = $this->language->get('text_success');
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function like(){
		$json=array();
		$this->language->load('extension/post');
		$this->load->model('extension/blog');
		if($this->customer->isLogged()){
			if(isset($this->request->get['post_id'])){
				$post_id = $this->request->get['post_id'];
			}else{
				$post_id = 0;
			}
			
			$filterdata=array(
				'post_id' 	  => $post_id,
				'customer_id' => $this->customer->getId(),
				'status'	  => 1,	
			);
			
			$like_id = $this->model_extension_blog->addlikebypostid($filterdata);
			if($like_id){
				$json['success'] = $this->language->get('text_like_success');
			}
		}else{
		  $json['error'] = true;
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}