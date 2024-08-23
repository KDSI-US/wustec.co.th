<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionStore extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/store');
		$this->load->language('extension/blog_setting');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('extension/store');

		$this->getList();
	}

	public function edit(){
		$this->load->language('extension/blog_setting');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/post');
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('blogsetting', $this->request->post, $this->request->get['store_id']);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/store', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], 'SSL'));
		}

		$this->getForm();
	}

	protected function getList(){
		$url = '';

		if (isset($this->request->get['page'])){
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('setting/store', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['add'] = $this->url->link('setting/store/add', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['delete'] = $this->url->link('setting/store/delete', 'user_token=' . $this->session->data['user_token'], 'SSL');

		$data['stores'] = array();

		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->config->get('config_name') . $this->language->get('text_default'),
			'url'      => HTTP_CATALOG,
			'edit'     => $this->url->link('extension/blog_setting', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$store_total = $this->model_extension_store->getTotalStores();

		$results = $this->model_extension_store->getStores();

		foreach ($results as $result) {
			$data['stores'][] = array(
				'store_id' => $result['store_id'],
				'name'     => $result['name'],
				'url'      => $result['url'],
				'edit'     => $this->url->link('extension/store/edit', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $result['store_id'], 'SSL')
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_url'] = $this->language->get('column_url');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}
		
		$data['webxheader'] = $this->load->controller('extension/webxheader');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/store_list', $data));
	}

	public function getForm(){
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['store_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		
		$data['heading_title'] = $this->language->get('heading_title');
		$data['tab_page'] = $this->language->get('tab_page');
		
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		
		$data['entry_showtitle'] = $this->language->get('entry_showtitle');
		$data['entry_showdescription'] = $this->language->get('entry_showdescription');
		$data['entrypagination'] = $this->language->get('entrypagination');
		
		$data['action'] = $this->url->link('extension/store/edit', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], 'SSL');
		
		$data['cancel'] = $this->url->link('extension/store', 'user_token=' . $this->session->data['user_token'], 'SSL');
		
		//TAB
		$data['tab_bloghome'] = $this->language->get('tab_bloghome');
		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_blog_listing'] = $this->language->get('tab_blog_listing');
		$data['tab_data'] = $this->language->get('tab_data');
		$data['tab_blog_post'] = $this->language->get('tab_blog_post');
		$data['tab_related_post'] = $this->language->get('tab_related_post');
		$data['tab_category_post'] = $this->language->get('tab_category_post');
		$data['tab_search_post'] = $this->language->get('tab_search_post');
		$data['tab_language'] = $this->language->get('tab_language');
		
		$data['entry_readmore'] = $this->language->get('entry_readmore');
		$data['entry_on'] = $this->language->get('entry_on');
		$data['entry_postby'] = $this->language->get('entry_postby');
		$data['entry_menu'] = $this->language->get('entry_menu');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_meta_title'] = $this->language->get('entry_meta_title');
		$data['entry_meta_description'] = $this->language->get('entry_meta_description');
		$data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
		$data['entry_blog_per_row'] = $this->language->get('entry_blog_per_row');
		$data['entry_blog_per_page'] = $this->language->get('entry_blog_per_page');
		$data['entry_thumb_size'] = $this->language->get('entry_thumb_size');
		$data['entry_blog_publish_date'] = $this->language->get('entry_blog_publish_date');
		$data['entry_blog_comment_per_page'] = $this->language->get('entry_blog_comment_per_page');
		$data['entry_blog_display_comment'] = $this->language->get('entry_blog_display_comment');
		$data['entry_blog_display_no_comment'] = $this->language->get('entry_blog_display_no_comment');
		$data['entry_blog_display_no_views'] = $this->language->get('entry_blog_display_no_views');
		$data['entry_blog_display_author'] = $this->language->get('entry_blog_display_author');
		$data['entry_blog_display_likes'] = $this->language->get('entry_blog_display_likes');
		$data['entry_blog_display_social_shares'] = $this->language->get('entry_blog_display_social_shares');
		$data['entry_blog_display_no_shares'] = $this->language->get('entry_blog_display_no_shares');
		$data['entry_blog_thumb_image'] = $this->language->get('entry_blog_thumb_image');
		$data['entry_listing_layout'] = $this->language->get('entry_listing_layout');
		$data['entry_blog_comment_permission'] = $this->language->get('entry_blog_comment_permission');
		$data['entry_comment_notification'] = $this->language->get('entry_comment_notification');
		$data['entry_guestcomment'] = $this->language->get('entry_guestcomment');
		$data['entry_blog_display_allowlikes'] = $this->language->get('entry_blog_display_allowlikes');
		$data['entry_keyword'] = $this->language->get('entry_keyword');
		$data['help_keyword'] = $this->language->get('help_keyword');
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}
		
		if (isset($this->error['listing_limit'])) {
			$data['error_listing_limit'] = $this->error['listing_limit'];
		} else {
			$data['error_listing_limit'] = '';
		}
		
		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = array();
		}
		
		if (isset($this->error['listing_blog_thumb_w'])) {
			$data['error_blog_thumb_w'] = $this->error['listing_blog_thumb_w'];
		} else {
			$data['error_blog_thumb_w'] = '';
		}
		
		if (isset($this->error['listing_blog_thumb_h'])) {
			$data['error_blog_thumb_h'] = $this->error['listing_blog_thumb_h'];
		} else {
			$data['error_blog_thumb_h'] = '';
		}
		
		if (isset($this->error['blogthumbsize_w'])) {
			$data['error_blogthumbsize_w'] = $this->error['blogthumbsize_w'];
		} else {
			$data['error_blogthumbsize_w'] = '';
		}
		
		if (isset($this->error['blogthumbsize_h'])) {
			$data['error_blogthumbsize_h'] = $this->error['blogthumbsize_h'];
		} else {
			$data['error_blogthumbsize_h'] = '';
		}
		
		
		if (isset($this->error['relatedthumbsize_w'])) {
			$data['error_relatedthumbsize_w'] = $this->error['relatedthumbsize_w'];
		} else {
			$data['error_relatedthumbsize_w'] = '';
		}
		
		if (isset($this->error['relatedthumbsize_h'])) {
			$data['error_relatedthumbsize_h'] = $this->error['relatedthumbsize_h'];
		} else {
			$data['error_relatedthumbsize_h'] = '';
		}
		
		if (isset($this->error['catethumbsize_w'])) {
			$data['error_catethumbsize_w'] = $this->error['catethumbsize_w'];
		} else {
			$data['error_catethumbsize_w'] = '';
		}
		
		if (isset($this->error['catethumbsize_h'])) {
			$data['error_catethumbsize_h'] = $this->error['catethumbsize_h'];
		} else {
			$data['error_catethumbsize_h'] = '';
		}
		
		if (isset($this->error['searchthumbsize_w'])) {
			$data['searchthumbsize_w'] = $this->error['searchthumbsize_w'];
		} else {
			$data['searchthumbsize_w'] = '';
		}
		
		if (isset($this->error['searchthumbsize_h'])) {
			$data['searchthumbsize_h'] = $this->error['searchthumbsize_h'];
		} else {
			$data['searchthumbsize_h'] = '';
		}
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if(isset($this->error['warning'])){
			$data['error_warning'] = $this->error['warning'];
		}else{
			$data['error_warning'] = '';
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/blog_setting', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$this->load->model('localisation/language');

		$languages = $this->model_localisation_language->getLanguages();
		
		$data['layouts']=array('1'=>'Layout 1','2'=> 'Layout 2','3'=> 'Layout 3');
		
		$store_info = $this->model_setting_setting->getSetting('blogsetting', $this->request->get['store_id']);
		
		if(isset($this->request->post['blogsetting'])){
			$blogsetting = $this->request->post['blogsetting'];
		}elseif(!empty($store_info['blogsetting'])){
			$blogsetting = $store_info['blogsetting'];
		}else{
			$blogsetting = array();
		}
		
		if(isset($blogsetting['description'])){
			$data['blogdescription'] = $blogsetting['description'];
		}else{
			$data['blogdescription'] = array();
		}
		
		if(isset($blogsetting['listing'])){
			$data['bloglisting'] = $blogsetting['listing'];
		}else{
			$data['bloglisting'] = array();
		}
		
		if(isset($this->request->post['keyword'])){
			$data['keyword'] = $this->request->post['keyword'];
		}else{
			$data['keyword'] = $this->model_extension_post->gethomeblogurl('blog/home');
		}
		
		if(isset($blogsetting['blog'])){
			$data['blogpage'] = $blogsetting['blog'];
		}else{
			$data['blogpage'] = array();
		}
		
		if(isset($blogsetting['related_listing'])){
			$data['related_listing'] = $blogsetting['related_listing'];
		}else{
			$data['related_listing'] = array();
		}
		
		if(isset($blogsetting['search_listing'])){
			$data['search_listing'] = $blogsetting['search_listing'];
		}else{
			$data['search_listing'] = array();
		}
		
		if(isset($blogsetting['category_listing'])){
			$data['category_listing'] = $blogsetting['category_listing'];
		}else{
			$data['category_listing'] = array();
		}
		
		$data['languages'] = $languages;
		foreach ($languages as $language){
			if (isset($this->request->post['blogsetting_menu' . $language['language_id']])) {
				$data['blogsetting_menu' . $language['language_id']] = $this->request->post['blogsetting_menu' . $language['language_id']];
			} elseif(!empty($store_info['blogsetting_menu' . $language['language_id']])){
				$data['blogsetting_menu' . $language['language_id']] = $store_info['blogsetting_menu' . $language['language_id']];
			}else {
				$data['blogsetting_menu' . $language['language_id']] = '';
			}
			
			if (isset($this->request->post['blogsetting_postby' . $language['language_id']])) {
				$data['blogsetting_postby' . $language['language_id']] = $this->request->post['blogsetting_postby' . $language['language_id']];
			} elseif(!empty($store_info['blogsetting_postby' . $language['language_id']])){
				$data['blogsetting_postby' . $language['language_id']] = $store_info['blogsetting_postby' . $language['language_id']];
			} else {
				$data['blogsetting_postby' . $language['language_id']] = '';
			}
			
			if (isset($this->request->post['blogsetting_on' . $language['language_id']])) {
				$data['blogsetting_on' . $language['language_id']] = $this->request->post['blogsetting_on' . $language['language_id']];
			} elseif(!empty($store_info['blogsetting_on' . $language['language_id']])){
				$data['blogsetting_on' . $language['language_id']] = $store_info['blogsetting_on' . $language['language_id']];
			}else{
				$data['blogsetting_on' . $language['language_id']] = '';
			}
			
			if (isset($this->request->post['blogsetting_readmore' . $language['language_id']])) {
				$data['blogsetting_readmore' . $language['language_id']] = $this->request->post['blogsetting_readmore' . $language['language_id']];
			} elseif(!empty($store_info['blogsetting_readmore' . $language['language_id']])){
				$data['blogsetting_readmore' . $language['language_id']] = $store_info['blogsetting_readmore' . $language['language_id']];
			} else {
				$data['blogsetting_readmore' . $language['language_id']] = '';
			}
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['webxheader'] = $this->load->controller('extension/webxheader');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/blog_setting', $data));
	}

	protected function validate(){
		if (!$this->user->hasPermission('modify', 'extension/store')){
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		foreach ($this->request->post['blogsetting']['description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 2) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}

			if ((utf8_strlen($value['meta_title']) < 3) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
		}
		
		
		if(empty($this->request->post['blogsetting']['listing']['limit'])){
			$this->error['listing_limit'] = $this->language->get('error_listing_limit');
		}
		
		if(!$this->request->post['blogsetting']['listing']['thumbsize_w']){
			$this->error['listing_blog_thumb_w'] = $this->language->get('error_blog_thumb_w');
		}
		
		if(empty($this->request->post['blogsetting']['listing']['thumbsize_h'])){
			$this->error['listing_blog_thumb_h'] = $this->language->get('error_blog_thumb_h');
		}
		
		if(empty($this->request->post['blogsetting']['blog']['thumbsize_w'])){
			$this->error['blogthumbsize_w'] = $this->language->get('error_thumbsize_w');
		}
		
		if(empty($this->request->post['blogsetting']['blog']['thumbsize_h'])){
			$this->error['blogthumbsize_h'] = $this->language->get('error_thumbsize_h');
		}
		
		if(empty($this->request->post['blogsetting']['related_listing']['thumbsize_w'])){
			$this->error['relatedthumbsize_w'] = $this->language->get('error_thumbsize_w');
		}
		
		if(empty($this->request->post['blogsetting']['related_listing']['thumbsize_h'])){
			$this->error['relatedthumbsize_h'] = $this->language->get('error_thumbsize_h');
		}
		
		if(empty($this->request->post['blogsetting']['category_listing']['thumbsize_w'])){
			$this->error['catethumbsize_w'] = $this->language->get('error_thumbsize_w');
		}
		
		if(empty($this->request->post['blogsetting']['category_listing']['thumbsize_h'])){
			$this->error['catethumbsize_h'] = $this->language->get('error_thumbsize_h');
		}
		
		if(empty($this->request->post['blogsetting']['search_listing']['thumbsize_w'])){
			$this->error['searchthumbsize_w'] = $this->language->get('error_thumbsize_w');
		}
		
		if(empty($this->request->post['blogsetting']['search_listing']['thumbsize_h'])){
			$this->error['searchthumbsize_h'] = $this->language->get('error_thumbsize_h');
		}
		
		if($this->error){
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		return !$this->error;
	}
}