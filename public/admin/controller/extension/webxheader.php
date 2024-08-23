<?php
class ControllerExtensionWebxheader extends Controller {
	public function index() {
		
		//$this->load->language('blog/webx_header');

		
		$data['webx_dashboard'] = $this->url->link('extension/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['webx_post'] = $this->url->link('extension/post', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['webx_category'] = $this->url->link('extension/category', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['webx_comment'] = $this->url->link('extension/comment', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['webx_subscribe'] = $this->url->link('extension/subscribe', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['webx_setting'] = $this->url->link('extension/store', 'user_token=' . $this->session->data['user_token'], 'SSL');
		
		

		return $this->load->view('extension/webx_header', $data);
	}
}
