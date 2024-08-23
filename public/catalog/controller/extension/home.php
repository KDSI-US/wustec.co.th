<?php
class ControllerExtensionHome extends Controller {
	public function index() {
		$this->load->language('extension/blog');
		
		$this->load->model('extension/blog');
		$this->load->model('tool/image');
		
		$this->document->addStyle('catalog/view/javascript/wblog/css/style.css');
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/home','','SSL')
		);

		$data['heading_title'] = $this->language->get('heading_title');
		
		//Home Setting
		$blogsetting = $this->config->get('blogsetting');
		if(isset($blogsetting['description'][$this->config->get('config_language_id')])){
			$data['heading'] = $blogsetting['description'][$this->config->get('config_language_id')]['name'];
		}else{
			$data['heading'] = '';
		}
		
		if(isset($blogsetting['description'][$this->config->get('config_language_id')])){
			$data['description'] = html_entity_decode($blogsetting['description'][$this->config->get('config_language_id')]['description'], ENT_QUOTES, 'UTF-8');
		}else{
			$data['description'] = '';
		}
		
		if(isset($blogsetting['description'][$this->config->get('config_language_id')])){
			$data['meta_title'] = html_entity_decode($blogsetting['description'][$this->config->get('config_language_id')]['meta_title'], ENT_QUOTES, 'UTF-8');
		}else{
			$data['meta_title'] = '';
		}
		
		/* if(isset($blogsetting['description'][$this->config->get('config_language_id')])){
			$data['meta_keyword'] = html_entity_decode($blogsetting['description'][$this->config->get('config_language_id')]['meta_keyword'], ENT_QUOTES, 'UTF-8');
		}else{
			$data['meta_keyword'] = '';
		} */
		
		if(isset($blogsetting['description'][$this->config->get('config_language_id')])){
			$data['meta_description'] = html_entity_decode($blogsetting['description'][$this->config->get('config_language_id')]['meta_description'], ENT_QUOTES, 'UTF-8');
		}else{
			$data['meta_description'] = '';
		}
		
		
		$this->document->setTitle($data['meta_title']);
		$this->document->setDescription($data['meta_description']);
		//$this->document->setKeywords($data['meta_keyword']);
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
			$data['blogsetting_on'] = 'ON';
		}
		
		if ($this->config->get('blogsetting_readmore' . $language_id)) {
			$data['blogsetting_readmore'] = $this->config->get('blogsetting_readmore' . $language_id);
		} else {
			$data['blogsetting_readmore'] = 'Read More';
		}
		
		$blogsetting = $this->config->get('blogsetting');
		
		if(isset($blogsetting['listing'])){
		$data['listing_layout'] = $blogsetting['listing']['layout_id'];
		}else{
		$data['listing_layout'] = 1;
		}
		
		if(isset($blogsetting['listing'])){
		$data['limit_row'] = $blogsetting['listing']['limit_row'];
		}else{
		$data['limit_row'] = 2;
		}
		
		if(!empty($blogsetting['listing']['limit'])){
		 $limit = trim($blogsetting['listing']['limit']);
		}else{
		 $limit = 10;
		}
		
		if(isset($blogsetting['listing'])){
			$data['thumbsize_w'] = trim($blogsetting['listing']['thumbsize_w']);
		}else{
			$data['thumbsize_w'] = '350';
		}
		
		if(isset($blogsetting['listing'])){
		$data['thumbsize_h'] = trim($blogsetting['listing']['thumbsize_h']);
		}else{
		$data['thumbsize_h'] = '200';
		}
		
		if(!empty($blogsetting['listing']['showtitle'])){
		$data['showtitle'] = $blogsetting['listing']['showtitle'];
		}else{
		$data['showtitle'] = '';
		}
		
		if(!empty($blogsetting['listing']['showdescription'])){
		$data['showdescription'] = $blogsetting['listing']['showdescription'];
		}else{
		$data['showdescription'] = '';
		}
		
		if(!empty($blogsetting['listing']['show_thumb'])){
		$data['show_thumb'] = $blogsetting['listing']['show_thumb'];
		}else{
		$data['show_thumb'] = '';
		}
		
		if(!empty($blogsetting['listing']['show_publishdate'])){
		$data['show_publishdate'] = $blogsetting['listing']['show_publishdate'];
		}else{
		$data['show_publishdate'] = '';
		}
		
		if(!empty($blogsetting['listing']['show_author'])){
		$data['show_author'] = $blogsetting['listing']['show_author'];
		}else{
		$data['show_author'] = '';
		}

		if(!empty($blogsetting['listing']['show_totalview'])){
		$data['show_totalview'] = $blogsetting['listing']['show_totalview'];
		}else{
		$data['show_totalview'] = '';
		}

		if(!empty($blogsetting['listing']['show_like'])){
		$data['show_like'] = $blogsetting['listing']['show_like'];
		}else{
		$data['show_like'] = '';
		}

		if(!empty($blogsetting['listing']['comment'])){
		$data['comment'] = $blogsetting['listing']['comment'];
		}else{
		$data['comment'] = '';
		}
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$data['blogs']=array();
		$filter_data = array(
			'sort'  => 'p.date_added',
			'order' => 'DESC',
			'start' => ($page - 1) * $limit,
			'limit' => $limit
		);
		
		$blog_total = $this->model_extension_blog->getTotalblogs($filter_data);
		$results = $this->model_extension_blog->getblogs($filter_data);
		
		foreach($results as $result){
			if(isset($result['post_id']) && $result['post_id']){
				if($result['image'] && file_exists(DIR_IMAGE.$result['image'])){
					$image = $this->model_tool_image->resize($result['image'],$data['thumbsize_w'],$data['thumbsize_h']);
				}else{
					$image = $this->model_tool_image->resize('no_image.jpg',$data['thumbsize_w'],$data['thumbsize_h']);
				}
				
				$data['blogs'][]=array(
					'post_id'  	  		=> $result['post_id'],
					'image'       		=> $image,
					'name'        		=> $result['name'],
					'post_type'        	=> $result['post_type'],
					'video_url'        	=> $result['video_url'],
					'comments'    		=> $result['comments'],
					'likes'    	  		=> $result['likes'],
					'username'    		=> $result['username'],
					'viewed'    		=> $result['viewed'],
					'short_description' => html_entity_decode($result['short_description']),
					'date' 		=> date($this->language->get('date_format_short'),strtotime($result['date'])),
					'name'        		=> $result['name'],
					'href'        		=> $this->url->link('extension/post', 'post_id=' . $result['post_id'])
				);
			}
		}
		
		
		$pagination = new Pagination();
		$pagination->total = $blog_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('extension/home', '&page={page}');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($blog_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($blog_total - $limit)) ? $blog_total : ((($page - 1) * $limit) + $limit), $blog_total, ceil($blog_total / $limit));
		
		
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		
				$this->response->setOutput($this->load->view('extension/home', $data));
	
	}
}