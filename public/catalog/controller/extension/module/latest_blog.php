<?php
class ControllerExtensionModuleLatestblog extends Controller {
	public function index($setting) {
	
		$this->load->language('extension/module/latest_blog');
		
		$this->document->addStyle('catalog/view/javascript/wblog/css/style.css');
		$this->document->addStyle('catalog/view/javascript/jquery/owl-carousel/owl.carousel.css');
		$this->document->addScript('catalog/view/javascript/jquery/owl-carousel/owl.carousel.min.js');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_tax'] = $this->language->get('text_tax');

		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');
		
		$language_id = $this->config->get('config_language_id');
		if($this->config->get('blogsetting_postby' . $language_id)){
			$data['blogsetting_postby'] = $this->config->get('blogsetting_postby' . $language_id);
		} else {
			$data['blogsetting_postby'] = 'Post By :';
		}
		
		if($this->config->get('blogsetting_on' . $language_id)){
			$data['blogsetting_on'] = $this->config->get('blogsetting_on' . $language_id);
		}else{
			$data['blogsetting_on'] = 'ON';
		}
		
		if($this->config->get('blogsetting_readmore' . $language_id)){
			$data['blogsetting_readmore'] = $this->config->get('blogsetting_readmore' . $language_id);
		}else{
			$data['blogsetting_readmore'] = 'Read More';
		}

		$this->load->model('extension/blog');

		$this->load->model('tool/image');
		
			
		if(!empty($setting['bloglayout_id'])){
			$data['bloglayout_id'] = $setting['bloglayout_id'];
		}else{
			$data['bloglayout_id'] = '1';
		}
		
		if(!empty($setting['limit_row'])){
			$data['limit_row'] = (int)$setting['limit_row'];
		}else{
			$data['limit_row'] = 1;
		}
		
		if(!empty($setting['show_title'])){
			$data['showtitle'] = $setting['show_title'];
		}else{
			$data['showtitle'] = '';
		}
		
		if(!empty($setting['show_image'])){
			$data['show_thumb'] = $setting['show_image'];
		}else{
			$data['show_thumb'] = '';
		}
		
		if(!empty($setting['show_publish'])){
			$data['show_publishdate'] = $setting['show_publish'];
		}else{
			$data['show_publishdate'] = '';
		}
		
		if(!empty($setting['show_viewed'])){
			$data['show_totalview'] = $setting['show_viewed'];
		}else{
			$data['show_totalview'] = '';
		}
		
		if(!empty($setting['show_short_description'])){
			$data['show_short_description'] = $setting['show_short_description'];
		}else{
			$data['show_short_description'] = '';
		}
		
		if(!empty($setting['show_author'])){
			$data['show_author'] = $setting['show_author'];
		}else{
			$data['show_author'] = '';
		}
		
		if(!empty($setting['show_like'])){
			$data['show_like'] = $setting['show_like'];
		}else{
			$data['show_like'] = '';
		}
		
		if(!empty($setting['show_comment'])){
			$data['comment'] = $setting['show_comment'];
		}else{
			$data['comment'] = '';
		}
		
		if(!empty($setting['show_slider'])){
			$data['show_slider'] = $setting['show_slider'];
		}else{
			$data['show_slider'] = '';
		}
		
		$data['blogs'] = array();

		$filter_data = array(
			'sort'  => 'p.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => $setting['limit']
		);
		
		$data['thumbsize_w'] = $setting['width'];
		$data['thumbsize_h'] = $setting['height'];

		$results = $this->model_extension_blog->getblogs($filter_data);
		if($results){
			foreach($results as $result){
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
				}
				
				$data['blogs'][]=array(
					'post_id'  	  		=> $result['post_id'],
					'image'       		=> $image,
					'name'        		=> $result['name'],
					'comments'    		=> $result['comments'],
					'likes'    	  		=> $result['likes'],
					'post_type'        	=> $result['post_type'],
					'video_url'        	=> $result['video_url'],
					'username'    		=> $result['username'],
					'viewed'    		=> $result['viewed'],
					'short_description' => substr($result['short_description'],0,170),
					'date' 		=> date($this->language->get('date_format_short'),strtotime($result['date'])),
					'name'        		=> $result['name'],
					'href'        		=> $this->url->link('extension/post', 'post_id=' . $result['post_id'])
				);
			}
			if ($data['blogs']) {
						return $this->load->view('extension/module/latest_blog', $data);
				}
			}
		}
	
}