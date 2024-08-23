<?php
class ControllerExtensiondashboard extends Controller {
	private $error = array();
	
	public function index(){
		$this->load->language('extension/dashboard');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('extension/post');
		
		$this->model_extension_post->Createtable();
		$this->model_extension_post->Createtable2();

		$this->load->model('setting/setting');
		
		$data['heading_title'] = $this->language->get('heading_title');
		$data['tab_page'] = $this->language->get('tab_page');
		
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		
		$data['entry_showtitle'] = $this->language->get('entry_showtitle');
		$data['entry_showdescription'] = $this->language->get('entry_showdescription');
		$data['entry_srno'] = $this->language->get('entry_srno');
		$data['entry_post'] = $this->language->get('entry_post');
		$data['entry_author'] = $this->language->get('entry_author');
		$data['entry_action'] = $this->language->get('entry_action');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['heading_recent_top_comment'] = $this->language->get('heading_recent_top_comment');
		$data['heading_recent_top_post'] = $this->language->get('heading_recent_top_post');
		$data['text_t_post'] = $this->language->get('text_t_post');
		$data['text_t_cpost'] = $this->language->get('text_t_cpost');
		$data['text_pending_comment'] = $this->language->get('text_pending_comment');
		$data['text_no_results'] = $this->language->get('text_no_results');
		
		//TAB
		$data['tab_extensionhome'] = $this->language->get('tab_extensionhome');
		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_data'] = $this->language->get('tab_data');
		
		
		
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
			'href' => $this->url->link('extension/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);
		
		
		$this->load->model('extension/post');
		$this->load->model('extension/comment');
		$this->load->model('extension/category');
		$this->load->model('tool/image');
		$this->load->model('user/user');
		
		
		//Total Posts
		$data['post_total'] = $this->model_extension_post->getTotalposts(array());
		
		//Total Categories
		$data['category_total'] = $this->model_extension_category->getTotalCategories(array());
		
		//Total Comment
		$filter_comment=array(
		  'filter_status' => false,
		);
		$data['comment_total'] = $this->model_extension_comment->getTotalcomments($filter_comment);
		
		$filter_data=array(
		  'sort'	=> 'p.viewed',
		  'order'	=> 'DESC',
		  'start'	=> 0,
		  'limit'   => 5,
		);
		
		$data['posts']=array();
		$results = $this->model_extension_post->getposts($filter_data);
		foreach($results as $result){
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}
			
			$user_info = $this->model_user_user->getUser($result['user_id']);
			if($user_info){
				$username = $user_info['firstname'].' '.$user_info['lastname'];
			}else{
				$username = '';
			}
			
			$data['posts'][] = array(
				'post_id'	 	=> $result['post_id'],
				'image'      	=> $image,
				'name'       	=> $result['name'],
				'username'   	=> $username,
				'viewed'   		=> $result['viewed'],
				'status'     	=> ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit'       	=> $this->url->link('extension/post/edit', 'user_token=' . $this->session->data['user_token'] . '&post_id=' . $result['post_id'], 'SSL')
			);
		}
		
		///Recent Comment
		$data['comments']=array();
		$filter_data=array(
		  'sort'	=> 'r.date_added',
		  'order'	=> 'DESC',
		  'start'	=> 0,
		  'limit'   => 5,
		);
		$cresults = $this->model_extension_comment->getcomments($filter_data);
		foreach($cresults as $cresult){
			$data['comments'][] = array(
				'comment_id'  => $cresult['comment_id'],
				'name'        => $cresult['name'],
				'author'      => $cresult['author'],
				'status'      => ($cresult['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'date_added'  => date($this->language->get('date_format_short'), strtotime($cresult['date_added'])),
				'edit'        => $this->url->link('extension/comment/edit', 'user_token=' . $this->session->data['user_token'] . '&comment_id=' . $cresult['comment_id'], 'SSL')
			);
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['webxheader'] = $this->load->controller('extension/webxheader');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/dashboard', $data));
	}

	protected function validate(){
		if (!$this->user->hasPermission('modify', 'extension/dashboard')){
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		return !$this->error;
	}
}