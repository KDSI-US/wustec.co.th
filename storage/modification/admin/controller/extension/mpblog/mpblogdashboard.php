<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionMpBlogMpBlogDashboard extends Controller {
	use mpblog\trait_mpblog;
	private $error = [];

	public function __construct($registry) {
		parent :: __construct($registry);
		$this->igniteTraitMpBlog($registry);
	}

	public function index() {
		$this->load->language('mpblog/mpblogdashboard');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['mpblogcategory'] = $this->url->link('extension/mpblog/mpblogcategory', $this->token.'=' . $this->session->data[$this->token], true);
		$data['mpblogpost'] = $this->url->link('extension/mpblog/mpblogpost', $this->token.'=' . $this->session->data[$this->token], true);
		$data['mpblogcomment'] = $this->url->link('extension/mpblog/mpblogcomment', $this->token.'=' . $this->session->data[$this->token], true);
		$data['mpblogreview'] = $this->url->link('extension/mpblog/mpblogreview', $this->token.'=' . $this->session->data[$this->token], true);
		$data['mpblog'] = $this->url->link('extension/mpblog/mpblog', $this->token.'=' . $this->session->data[$this->token], true);
		
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_total_blogs'] = $this->language->get('text_total_blogs');
		$data['text_total_categories'] = $this->language->get('text_total_categories');
		$data['text_total_comments'] = $this->language->get('text_total_comments');
		$data['text_total_ratings'] = $this->language->get('text_total_ratings');
		$data['text_edit'] = $this->language->get('text_edit');

		$data['text_view_blogs'] = $this->language->get('text_view_blogs');
		$data['text_view_categories'] = $this->language->get('text_view_categories');
		$data['text_view_comments'] = $this->language->get('text_view_comments');
		$data['text_view_ratings'] = $this->language->get('text_view_ratings');
		$data['text_blog_show_search'] = $this->language->get('text_blog_show_search');

		$data['text_latest_blogs'] = $this->language->get('text_latest_blogs');
		$data['text_latest_comments'] = $this->language->get('text_latest_comments');
		$data['text_top_rated_blogs'] = $this->language->get('text_top_rated_blogs');
		$data['text_top_viewed_blogs'] = $this->language->get('text_top_viewed_blogs');
		$data['text_no_blogs'] = $this->language->get('text_no_blogs');
		$data['text_no_comments'] = $this->language->get('text_no_comments');

		$data['column_image'] = $this->language->get('column_image');
		$data['column_blog'] = $this->language->get('column_blog');
		$data['column_date_available'] = $this->language->get('column_date_available');
		$data['column_author'] = $this->language->get('column_author');
		$data['column_published'] = $this->language->get('column_published');
		$data['column_action'] = $this->language->get('column_action');
		$data['column_commentby'] = $this->language->get('column_commentby');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_comment'] = $this->language->get('column_comment');

		$data['button_viewall'] = $this->language->get('button_viewall');

		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}	

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $this->token.'=' . $this->session->data[$this->token], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/mpblog/mpblog', $this->token.'=' . $this->session->data[$this->token], true)
		];

		$data['get_token'] = $this->token;
		$data['token'] = $this->session->data[$this->token];
		
		// blog post
		$this->load->model('extension/mpblog/mpblogpost');
		$data['total_blogs'] = $this->model_extension_mpblog_mpblogpost->getTotalMpBlogPosts();
		$data['mpblogs'] = $this->url->link('extension/mpblog/mpblogpost', $this->token.'=' . $this->session->data[$this->token] , true);

		// blog category
		$this->load->model('extension/mpblog/mpblogcategory');
		$data['total_categories'] = $this->model_extension_mpblog_mpblogcategory->getTotalMpBlogCategories();
		$data['mpcategory'] = $this->url->link('extension/mpblog/mpblogcategory', $this->token.'=' . $this->session->data[$this->token] , true);

		// blog comment
		$this->load->model('extension/mpblog/mpblogcomment');
		$data['total_comments'] = $this->model_extension_mpblog_mpblogcomment->getTotalMpBlogComments();
		$data['mpcomment'] = $this->url->link('extension/mpblog/mpblogcomment', $this->token.'=' . $this->session->data[$this->token] , true);

		// blog rating
		$this->load->model('extension/mpblog/mpblograting');
		$data['total_ratings'] = $this->model_extension_mpblog_mpblograting->getTotalMpBlogRatings();
		$data['mprating'] = $this->url->link('extension/mpblog/mpblograting', $this->token.'=' . $this->session->data[$this->token] , true);

		
		$this->load->model('tool/image');

		// Latest Blogs
		$filter_data = [
			'sort' => 'p.date_available',
			'order' => 'DESC',
			'start' => 0,
			'limit' => 5,
		];

		$latest_blogs = $this->model_extension_mpblog_mpblogpost->getMpBlogPosts($filter_data);

		$data['latest_blogs'] = [];

		foreach ($latest_blogs as $latest_blog) {
			if (!empty($latest_blog['image']) && file_exists(DIR_IMAGE . $latest_blog['image'])) {
				$thumb = $this->model_tool_image->resize($latest_blog['image'], 40, 40);
			} else {
				$thumb = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$data['latest_blogs'][] = [
				'thumb' => $thumb,
				'name' => $latest_blog['name'],
				'author' => $latest_blog['author'],
				'likes' => $latest_blog['likes'],
				'viewed' => $latest_blog['viewed'],
				'rating' => round($latest_blog['rating'],2),
				'date_available' => date($this->language->get('date_format_short'), strtotime($latest_blog['date_available'])) ,
				'status' => ($latest_blog['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'href' => $this->url->link('extension/mpblog/mpblogpost/edit', $this->token.'=' .$this->session->data[$this->token] .'&mpblogpost_id=' . $latest_blog['mpblogpost_id'], true ) ,
			];
		}

		$data['all_blogs'] = $this->url->link('extension/mpblog/mpblogpost', $this->token.'=' .$this->session->data[$this->token] , true );

		// Latest Comments
		$filter_data = [
			'sort' => 'r.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => 5,
		];

		$latest_comments = $this->model_extension_mpblog_mpblogcomment->getMpBlogComments($filter_data);


		$data['latest_comments'] = [];

		foreach ($latest_comments as $latest_comment) {
			
			$data['latest_comments'][] = [
				'name' => $latest_comment['name'],
				'text' => substr($latest_comment['text'], 0, 100) ,
				'author' => $latest_comment['author'],
				'status' => ($latest_comment['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'date_added' => date($this->language->get('date_format_short'), strtotime($latest_comment['date_added'])) ,
				'href' => $this->url->link('extension/mpblog/mpblogcomment/edit', $this->token.'=' .$this->session->data[$this->token] .'&mpblogcomment_id=' . $latest_comment['mpblogcomment_id'], true ) ,
			];
		}

		$data['all_comments'] = $this->url->link('extension/mpblog/mpblogcomment', $this->token.'=' .$this->session->data[$this->token] , true );


		// Top Rated Blogs
		$filter_data = [
			'sort' => 'rating',
			'order' => 'DESC',
			'start' => 0,
			'limit' => 5,
		];

		$toprated_blogs = $this->model_extension_mpblog_mpblogpost->getMpBlogPosts($filter_data);

		$data['toprated_blogs'] = [];

		foreach ($toprated_blogs as $toprated_blog) {
			if (!empty($toprated_blog['image']) && file_exists(DIR_IMAGE . $toprated_blog['image'])) {
				$thumb = $this->model_tool_image->resize($toprated_blog['image'], 40, 40);
			} else {
				$thumb = $this->model_tool_image->resize('no_image.png', 40, 40);
			}
			$data['toprated_blogs'][] = [
				'thumb' => $thumb,
				'name' => $toprated_blog['name'],
				'author' => $toprated_blog['author'],
				'likes' => $toprated_blog['likes'],
				'viewed' => $toprated_blog['viewed'],
				'rating' => round($toprated_blog['rating'], 2),
				'date_available' => date($this->language->get('date_format_short'), strtotime($toprated_blog['date_available'])) ,
				'status' => ($toprated_blog['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'href' => $this->url->link('extension/mpblog/mpblogpost/edit', $this->token.'=' .$this->session->data[$this->token] .'&mpblogpost_id=' . $toprated_blog['mpblogpost_id'], true ) ,
			];
		}

		// Top Viewed Blogs
		$filter_data = [
			'sort' => 'p.viewed',
			'order' => 'DESC',
			'start' => 0,
			'limit' => 5,
		];

		$mostviewed_blogs = $this->model_extension_mpblog_mpblogpost->getMpBlogPosts($filter_data);

		$data['mostviewed_blogs'] = [];

		foreach ($mostviewed_blogs as $mostviewed_blog) {
			if (!empty($mostviewed_blog['image']) && file_exists(DIR_IMAGE . $mostviewed_blog['image'])) {
				$thumb = $this->model_tool_image->resize($mostviewed_blog['image'], 40, 40);
			} else {
				$thumb = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$data['mostviewed_blogs'][] = [
				'thumb' => $thumb,
				'name' => $mostviewed_blog['name'],
				'author' => $mostviewed_blog['author'],
				'likes' => $mostviewed_blog['likes'],
				'viewed' => $mostviewed_blog['viewed'],
				'rating' => round($mostviewed_blog['rating'],2),
				'date_available' => date($this->language->get('date_format_short'), strtotime($mostviewed_blog['date_available'])) ,
				'status' => ($mostviewed_blog['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'href' => $this->url->link('extension/mpblog/mpblogpost/edit', $this->token.'=' .$this->session->data[$this->token] .'&mpblogpost_id=' . $mostviewed_blog['mpblogpost_id'], true ) ,
			];
		}


		$data['mpblogmenu'] = $this->load->controller('extension/mpblog/mpblogmenu');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->viewLoad('extension/mpblog/mpblogdashboard', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/mpblog/mpblogdashboard')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

}