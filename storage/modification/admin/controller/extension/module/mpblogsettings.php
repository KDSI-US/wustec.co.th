<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleMpblogSettings extends Controller {
	use mpblog\trait_mpblog;
	private $error = [];
	private $installed = [];
	private $layouts = [
		[
			'name' => 'Mp Blog Page',
			'path' => 'extension/mpblog/blog',
			'module' => [
				[
					'position' => 'column_right',
					'modules' => [
						'mpblogsearch',
						'mpblogcategory',
						'mpblogarchive',
						'mpblogfeatured'
					]
				],
				[
					'position' => 'column_left',
					'modules' => [
						'mpbloglatest',
						'mpblogpopular'
					]
				],
				[
					'position' => 'content_bottom',
					'modules' => [
						'mpblogallinone',
						'mpblogtrending'
					]
				]
			]
		],
		[
			'name' => 'Mp All Blogs (Home Page)',
			'path' => 'extension/mpblog/blogs',
			'module' => [
				[
					'position' => 'column_right',
					'modules' => [
						'mpblogsearch',
						'mpblogcategory',
						'mpblogarchive',
						'mpblogfeatured'
					]
				]
			]
		],
		[
			'name' => 'Mp Blog Category Page',
			'path' => 'extension/mpblog/blogcategory',
			'module' => [
				[
					'position' => 'column_right',
					'modules' => [
						'mpblogsearch',
						'mpblogcategory',
						'mpblogarchive',
						'mpblogfeatured'
					]
				]
			]
		]
	];
	private $modules = [
		[
			'name' => 'M-Blog All In One',
			'path' => 'extension/module/mpblogallinone',
			'code' => 'mpblogallinone',
			'modules' => true
		],
		[
			'name' => 'M-Blog Calender',
			'path' => 'extension/module/mpblogarchive',
			'code' => 'mpblogarchive',
			'modules' => false
		],
		[
			'name' => 'M-Blog Category',
			'path' => 'extension/module/mpblogcategory',
			'code' => 'mpblogcategory',
			'modules' => false
		],
		[
			'name' => 'M-Blog Featured',
			'path' => 'extension/module/mpblogfeatured',
			'code' => 'mpblogfeatured',
			'modules' => true
		],
		[
			'name' => 'M-Blog Latest',
			'path' => 'extension/module/mpbloglatest',
			'code' => 'mpbloglatest',
			'modules' => true
		],
		[
			'name' => 'M-Blog Popular',
			'path' => 'extension/module/mpblogpopular',
			'code' => 'mpblogpopular',
			'modules' => true
		],
		[
			'name' => 'M-Blog Search',
			'path' => 'extension/module/mpblogsearch',
			'code' => 'mpblogsearch',
			'modules' => false
		],
		[
			'name' => 'M-Blog Subscribe',
			'path' => 'extension/module/mpblogsubscribe',
			'code' => 'mpblogsubscribe',
			'modules' => false
		],
		[
			'name' => 'M-Blog Trending',
			'path' => 'extension/module/mpblogtrending',
			'code' => 'mpblogtrending',
			'modules' => true
		]
	];
	private $files = [
		'extension/mpblog/mpblogcategory',
		'extension/mpblog/mpblogcomment',
		'extension/mpblog/mpblogdashboard',
		'extension/mpblog/mpblogmenu',
		'extension/mpblog/mpblogpost',
		'extension/mpblog/mpblograting',
		'extension/mpblog/mpblogsubscriber',
		'extension/module/mpblogallinone',
		'extension/module/mpblogarchive',
		'extension/module/mpblogcategory',
		'extension/module/mpblogfeatured',
		'extension/module/mpbloglatest',
		'extension/module/mpblogpopular',
		'extension/module/mpblogsearch',
		'extension/module/mpblogsettings',
		'extension/module/mpblogsubscribe',
		'extension/module/mpblogtrending',
	];

	public function __construct($registry) {
		parent :: __construct($registry);
		$this->igniteTraitMpBlog($registry);
	}

	public function install() {
		// install database tables
		$this->load->model('extension/mpblog/mpbloginstall');
		$this->model_extension_mpblog_mpbloginstall->install();

		// Add permissions to extension files dynamically
		$this->addFilesInPermissions($this->files);
		$this->installModules($this->modules);
		$this->addLayouts($this->layouts);
	}

	private function installModules(array $modules) {

		// $this->model_file['extension/module']['obj']

		$this->load->model($this->model_file['extension/extension']['path']);
		$this->load->model($this->model_file['extension/module']['path']);

		foreach ($modules as $module) {
				$this->{$this->model_file['extension/extension']['obj']}->install('module', $module['code']);

				$this->addFilesInPermissions([$module['path']]);

				// Call install method if it exsits
				$this->load->controller($module['path'] . '/install');
		}

	}

	private function addLayouts(array $addlayouts) {
		$this->load->model('design/layout');

		foreach ($addlayouts as $addlayout) {
			$sql = $this->db->query("SELECT * FROM " . DB_PREFIX . "layout_route WHERE route='". $this->db->escape($addlayout['path']) ."'");
			if (!$sql->num_rows) {
				$layout_route = [];
				$layout_route[] = [
					'store_id' => (int)$this->config->get('config_store_id'),
					'route' => $addlayout['path']
				];

				$layout_module = [];
				foreach ($addlayout['module'] as $module) {
					foreach ($module['modules'] as $sort_order => $code) {
						$layout_module[] = [
							'code' => $code,
							'position' => $module['position'],
							'sort_order' => $sort_order,
						];
					}
				}

				$this->model_design_layout->addLayout([
					'name' => $addlayout['name'],
					'layout_route' => $layout_route,
					'layout_module' => $layout_module
				]);
			}
		}
	}

	private function detectLayoutsForAdd() {
		$notlayouts = [];

		foreach ($this->layouts as $addlayout) {
			$sql = $this->db->query("SELECT * FROM " . DB_PREFIX . "layout_route WHERE route='". $this->db->escape($addlayout['path']) ."'");
			if (!$sql->num_rows) {
				$notlayouts[] = $addlayout;
			}
		}
		return $notlayouts;
	}

	public function updateLayouts() {
		$json = [];
		$this->load->language('extension/module/mpblogsettings');

		$this->addLayouts($this->detectLayoutsForAdd());
		$this->session->data['success'] = $this->language->get('text_success_layouts_update');
		$json['redirect'] = str_replace("&amp;", "&", $this->url->link('extension/module/mpblogsettings', $this->token.'=' . $this->session->data[$this->token], true));

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function addFilesInPermissions(array $files) {
		if ($this->user->hasPermission('modify', 'extension/module/mpblogsettings')) {
			$this->load->model('user/user_group');
			foreach ($files as $file) {
				// remove list of files from permissions array to avoid troubles
				$this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', $file);
				$this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', $file);

				$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', $file);
				$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', $file);
			}
		}
	}

	private function detectFilesForPermissions() {
		$this->load->model('user/user_group');
		$user_group = $this->model_user_user_group->getUserGroup($this->user->getGroupId());

		$files = [];

		foreach ($this->files as $file) {
			if (!in_array($file, $user_group['permission']['access']) || !in_array($file, $user_group['permission']['modify'])) {
				$files[] = $file;
			}
		}

		return $files;
	}

	public function updatePermissions() {
		$json = [];
		$this->load->language('extension/module/mpblogsettings');

		$this->addFilesInPermissions($this->detectFilesForPermissions());
		$this->session->data['success'] = $this->language->get('text_success_files_permission');
		$json['redirect'] = str_replace("&amp;", "&", $this->url->link('extension/module/mpblogsettings', $this->token.'=' . $this->session->data[$this->token], true));

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function moduleIsInstalled($module) {
		if (empty($this->installed['module'])) {
			$this->load->model($this->model_file['extension/extension']['path']);
			$this->installed['module'] = $this->{$this->model_file['extension/extension']['obj']}->getInstalled('module');
		}

		return in_array($module, $this->installed['module']);
	}

	private function labelEnableDisable($status) {
		$text = $this->language->get('text_disabled');
		$label = 'danger';
		if ($status) {
			$text = $this->language->get('text_enabled');
			$label = 'success';
		}
		return '<span class="text-'. $label .'">' . $text . '</span>';
	}

	public function getMenu() {
		$this->load->language('mpblog/mpblogmenu');
		$menu = [];
		$children = [];

		if ($this->user->hasPermission('access', 'extension/mpblog/mpblogdashboard')) {
			$children[] = [
				'id'       => 'mpblog-dashboard',
				'icon'	   => 'fa-dashboard',
				'name'	   => $this->language->get('text_mpblogdashboard'),
				'href'     => $this->url->link('extension/mpblog/mpblogdashboard', $this->token.'=' . $this->session->data[$this->token], true),
				'children' => []
			];
		}
		if ($this->user->hasPermission('access', 'extension/mpblog/mpblogcategory')) {
			$children[] = [
				'id'       => 'mpblog-category',
				'icon'	   => 'fa-list-ul',
				'name'	   => $this->language->get('text_mpblog_category'),
				'href'     => $this->url->link('extension/mpblog/mpblogcategory', $this->token.'=' . $this->session->data[$this->token], true),
				'children' => []
			];
		}
		if ($this->user->hasPermission('access', 'extension/mpblog/mpblogpost')) {
			$children[] = [
				'id'       => 'mpblog-post',
				'icon'	   => 'fa-pencil-square-o',
				'name'	   => $this->language->get('text_mpblog_post'),
				'href'     => $this->url->link('extension/mpblog/mpblogpost', $this->token.'=' . $this->session->data[$this->token], true),
				'children' => []
			];
		}
		if ($this->user->hasPermission('access', 'extension/mpblog/mpblogcomment')) {
			$children[] = [
				'id'       => 'mpblog-comment',
				'icon'	   => 'fa-comments',
				'name'	   => $this->language->get('text_mpblog_comment'),
				'href'     => $this->url->link('extension/mpblog/mpblogcomment', $this->token.'=' . $this->session->data[$this->token], true),
				'children' => []
			];
		}
		if ($this->user->hasPermission('access', 'extension/mpblog/mpblograting')) {
			$children[] = [
				'id'       => 'mpblog-rating',
				'icon'	   => 'fa-star',
				'name'	   => $this->language->get('text_mpblog_rating'),
				'href'     => $this->url->link('extension/mpblog/mpblograting', $this->token.'=' . $this->session->data[$this->token], true),
				'children' => []
			];
		}
		if ($this->user->hasPermission('access', 'extension/mpblog/mpblogsubscriber')) {
			$children[] = [
				'id'       => 'mpblog-subscribers',
				'icon'	   => 'fa-users',
				'name'	   => $this->language->get('text_mpblog_subscriber'),
				'href'     => $this->url->link('extension/mpblog/mpblogsubscriber', $this->token.'=' . $this->session->data[$this->token], true),
				'children' => []
			];
		}
		if ($this->user->hasPermission('access', 'extension/module/mpblogsettings')) {
			$children[] = [
				'id'       => 'mpblog-setting',
				'icon'	   => 'fa-wrench',
				'name'	   => $this->language->get('text_mpblogsetting') .' - '. $this->labelEnableDisable($this->config->get('mpblog_status')),
				'href'     => $this->url->link('extension/module/mpblogsettings', $this->token.'=' . $this->session->data[$this->token], true),
				'children' => []
			];
		}

		$extensions = [];

		foreach ($this->modules as $module) {
			if ($this->moduleIsInstalled($module['code']) && $this->user->hasPermission('access', $module['path'])) {

				$this->load->language('extension/module/' . $module['code'], $module['code']);

				$subchildren = [];

				if ($module['modules']) {

						$this->load->model($this->model_file['extension/module']['path']);
						$code_modules = $this->{$this->model_file['extension/module']['obj']}->getModulesByCode($module['code']);

						$subchildren[] = [
							'id'       => 'mpblog-extension-' . $module['code'] . '-add',
							'name'     => sprintf($this->language->get('text_module_mpblog_add'), strip_tags($this->language->get($module['code'])->get('heading_title'))),
							'href'     => $this->url->link($module['path'], $this->token.'=' . $this->session->data[$this->token], true),
							'children' => []
						];
						foreach ($code_modules as $code_module) {
							$code_module_setting = json_decode($code_module['setting'], 1);
							$subchildren[] = [
								'id'       => 'mpblog-extension-' . $module['code'] . '-edit-'.$code_module['module_id'],
								'name'     => $code_module['name'] .' - '. $this->labelEnableDisable($code_module_setting['status']),
								'href'     => $this->url->link($module['path'], $this->token.'=' . $this->session->data[$this->token] . '&module_id=' . $code_module['module_id'], true),
								'children' => []
							];
						}
						$extensions[] = [
							'id'       => 'mpblog-extension-' . $module['code'],
							'name'     => strip_tags($this->language->get($module['code'])->get('heading_title')),
							'href'     => '',
							'icon'	   => '',
							'children' => $subchildren
						];

				} else {


						$extensions[] = [
							'id'       => 'mpblog-extension-' . $module['code'],
							'name'     => strip_tags($this->language->get($module['code'])->get('heading_title')) .' - '. $this->labelEnableDisable($this->config->get('module_' . $module['code'] . '_status')),
							'href'     => $this->url->link($module['path'], $this->token.'=' . $this->session->data[$this->token], true),
							'icon'	   => '',
							'children' => $subchildren
						];

				}
			}

		}

		if ($extensions) {
			$children[] = [
				'id'       => 'mpblog-extension',
				'name'     => $this->language->get('text_extension'),
				'href'     => '',
				'icon'	   => '',
				'children' => $extensions
			];
		}

		if ($children) {
			$menu = [
				'id'       => 'menu-mpblog',
				'icon'	   => 'fa-file-text-o',
				'name'     => $this->language->get('text_mpblog'),
				'href'     => '',
				'children' => $children
			];
		}

		// || !$this->config->get('module_mpblogsettings_status')
		if (!$this->moduleIsInstalled('mpblogsettings')) {
			return [];
		}
		return $menu;
	}

	public function index() {
		$this->load->language('extension/module/mpblogsettings');

		$data['store_id'] = $store_id = 0;
		if (isset($this->request->get['store_id'])) {
			$data['store_id'] = $store_id = $this->request->get['store_id'];
		}

		// show a alert message for files that are not in premissions list
		if ($this->user->hasPermission('modify', 'extension/module/mpblogsettings')) {
			$data['files'] = $this->detectFilesForPermissions();
		} else {
			$data['files'] = [];
		}

		// show a alert message for blog layouts that can be added if want
		if ($this->user->hasPermission('modify', 'extension/module/mpblogsettings')) {
			$data['layouts'] = $this->detectLayoutsForAdd();
		} else {
			$data['layouts'] = [];
		}

		$data['layouts_info'] = [];
		foreach ($this->layouts as $layout) {
			$data['layouts_info'][] = [
				'name' => $layout['name'],
				'path' => $layout['path'],
			];
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

			$post = [];
			$post['module_mpblog_status'] = $this->request->post['mpblog_status'];
			$this->model_setting_setting->editSetting('mpblog', $post, $store_id);

			$this->model_setting_setting->editSetting('mpblog', $this->request->post, $store_id);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/module/mpblogsettings', $this->token.'=' . $this->session->data[$this->token].'&store_id='. $store_id, true));
		}
		
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_top'] = $this->language->get('text_top');
		$data['text_bottom'] = $this->language->get('text_bottom');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_store'] = $this->language->get('text_store');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_grid'] = $this->language->get('text_grid');
		$data['text_list'] = $this->language->get('text_list');


		$data['text_column'] = $this->language->get('text_column');
		$data['text_column_title'] = $this->language->get('text_column_title');
		$data['text_column_defination'] = $this->language->get('text_column_defination');

		$data['text_img_size_title'] = $this->language->get('text_img_size_title');
		$data['text_img_size_defination'] = $this->language->get('text_img_size_defination');
		$data['text_cimg_size_title'] = $this->language->get('text_cimg_size_title');
		$data['text_cimg_size_defination'] = $this->language->get('text_cimg_size_defination');

		$data['text_socialmedia_title'] = $this->language->get('text_socialmedia_title');
		$data['text_socialmedia_defination'] = $this->language->get('text_socialmedia_defination');

		$data['text_comment_title'] = $this->language->get('text_comment_title');
		$data['text_comment_defination'] = $this->language->get('text_comment_defination');

		$data['text_edit'] = $this->language->get('text_edit');	
	
		$data['text_category_home'] = $this->language->get('text_category_home');
		$data['text_category_post_count'] = $this->language->get('text_category_post_count');
		$data['text_category_show_image'] = $this->language->get('text_category_show_image');
		$data['text_category_show_description'] = $this->language->get('text_category_show_description');
		$data['text_category_page_limit'] = $this->language->get('text_category_page_limit');
		$data['text_category_design'] = $this->language->get('text_category_design');

		$data['text_blog_show_image'] = $this->language->get('text_blog_show_image');
		$data['text_blog_show_image_popup'] = $this->language->get('text_blog_show_image_popup');
		$data['text_blog_show_description'] = $this->language->get('text_blog_show_description');
		$data['text_blog_show_sdescription'] = $this->language->get('text_blog_show_sdescription');
		$data['text_blog_sdescription_length'] = $this->language->get('text_blog_sdescription_length');
		$data['text_blog_page_limit'] = $this->language->get('text_blog_page_limit');
		$data['text_blog_show_author'] = $this->language->get('text_blog_show_author');
		$data['text_blog_show_date'] = $this->language->get('text_blog_show_date');
		$data['text_blog_date_format'] = $this->language->get('text_blog_date_format');
		$data['text_blog_show_comment'] = $this->language->get('text_blog_show_comment');
		$data['text_blog_use_comment'] = $this->language->get('text_blog_use_comment');
		$data['text_blog_allow_comment'] = $this->language->get('text_blog_allow_comment');
		$data['text_blog_approve_comment'] = $this->language->get('text_blog_approve_comment');
		$data['text_blog_captcha_comment'] = $this->language->get('text_blog_captcha_comment');
		$data['text_blog_show_rating'] = $this->language->get('text_blog_show_rating');
		$data['text_blog_allow_rating'] = $this->language->get('text_blog_allow_rating');
		$data['text_blog_approve_rating'] = $this->language->get('text_blog_approve_rating');
		$data['text_blog_guest_rating'] = $this->language->get('text_blog_guest_rating');
		$data['text_blog_show_readmore'] = $this->language->get('text_blog_show_readmore');
		$data['text_blog_show_viewcount'] = $this->language->get('text_blog_show_viewcount');
		$data['text_blog_show_sharethis'] = $this->language->get('text_blog_show_sharethis');
		$data['text_blog_show_nextprev'] = $this->language->get('text_blog_show_nextprev');
		$data['text_blog_show_nextprev_title'] = $this->language->get('text_blog_show_nextprev_title');
		$data['text_blog_show_tags'] = $this->language->get('text_blog_show_tags');
		$data['text_blog_view'] = $this->language->get('text_blog_view');
		$data['text_category_view'] = $this->language->get('text_category_view');
		$data['text_blog_show_viewsocial'] = $this->language->get('text_blog_show_viewsocial');
		$data['text_blog_show_sociallocation'] = $this->language->get('text_blog_show_sociallocation');
		$data['text_blog_show_viewwishlist'] = $this->language->get('text_blog_show_viewwishlist');
		$data['text_blog_design'] = $this->language->get('text_blog_design');


		$data['text_comment_default'] = $this->language->get('text_comment_default');
		$data['text_comment_default_guest'] = $this->language->get('text_comment_default_guest');

		$data['text_comment_facebook'] = $this->language->get('text_comment_facebook');
		$data['text_facebook_appid'] = $this->language->get('text_facebook_appid');
		$data['text_facebook_nocomment'] = $this->language->get('text_facebook_nocomment');
		$data['text_facebook_color'] = $this->language->get('text_facebook_color');
		$data['text_facebook_colorlight'] = $this->language->get('text_facebook_colorlight');
		$data['text_facebook_colordark'] = $this->language->get('text_facebook_colordark');
		$data['text_facebook_order'] = $this->language->get('text_facebook_order');
		$data['text_facebook_ordersocial'] = $this->language->get('text_facebook_ordersocial');
		$data['text_facebook_orderreverse_time'] = $this->language->get('text_facebook_orderreverse_time');
		$data['text_facebook_ordertime'] = $this->language->get('text_facebook_ordertime');
		$data['text_facebook_width'] = $this->language->get('text_facebook_width');

		$data['text_comment_google'] = $this->language->get('text_comment_google');


		$data['text_comment_disqus'] = $this->language->get('text_comment_disqus');
		$data['text_comment_disqus_code'] = $this->language->get('text_comment_disqus_code');
		$data['text_comment_disqus_count'] = $this->language->get('text_comment_disqus_count');


		$data['legend_blogpage'] = $this->language->get('legend_blogpage');
		$data['legend_bloglist'] = $this->language->get('legend_bloglist');
		$data['legend_design'] = $this->language->get('legend_design');
		$data['legend_image_sizes'] = $this->language->get('legend_image_sizes');
		$data['legend_comment'] = $this->language->get('legend_comment');
		
		$data['legend_msg_unsubscribe'] = $this->language->get('legend_msg_unsubscribe');
		$data['legend_msg_confirm'] = $this->language->get('legend_msg_confirm');
		$data['legend_msg_invalid'] = $this->language->get('legend_msg_invalid');


		$data['info_subscribe_approval'] = $this->language->get('info_subscribe_approval');
		$data['info_subscribe_pending'] = $this->language->get('info_subscribe_pending');
		$data['info_subscribe_pending'] = $this->language->get('info_subscribe_pending');
		$data['info_subscribe_confirm'] = $this->language->get('info_subscribe_confirm');
		$data['info_subscribe_confirm_page'] = $this->language->get('info_subscribe_confirm_page');
		$data['info_unsubscribe'] = $this->language->get('info_unsubscribe');
		$data['info_unsubscribe_page'] = $this->language->get('info_unsubscribe_page');
		$data['info_invalidurl'] = $this->language->get('info_invalidurl');

		$data['info_email_blogadd'] = $this->language->get('info_email_blogadd');
		$data['info_email_blogedit'] = $this->language->get('info_email_blogedit');
		

		$data['info_title_rssfeed'] = $this->language->get('info_title_rssfeed');
		$data['info_text_rssfeed'] = $this->language->get('info_text_rssfeed');
		
		$data['text_rssfeed_title'] = $this->language->get('text_rssfeed_title');
		$data['text_rssfeed_description'] = $this->language->get('text_rssfeed_description');
		$data['text_rssfeed_format'] = $this->language->get('text_rssfeed_format');
		
		$data['text_rssfeed_limit'] = $this->language->get('text_rssfeed_limit');
		$data['text_rssfeed_web_master'] = $this->language->get('text_rssfeed_web_master');
		$data['text_rssfeed_copy_write'] = $this->language->get('text_rssfeed_copy_write');
		
		$data['text_autoapprove'] = $this->language->get('text_autoapprove');
		$data['text_adminapprove'] = $this->language->get('text_adminapprove');
		$data['text_confirmapprove'] = $this->language->get('text_confirmapprove');
		

		$data['entry_social_icon'] = $this->language->get('entry_social_icon');
		$data['entry_social_href'] = $this->language->get('entry_social_href');
		$data['entry_social_name'] = $this->language->get('entry_social_name');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['placeholder_social_icon'] = $this->language->get('placeholder_social_icon');
		$data['placeholder_social_href'] = $this->language->get('placeholder_social_href');
		$data['placeholder_social_name'] = $this->language->get('placeholder_social_name');
		$data['placeholder_sort_order'] = $this->language->get('placeholder_sort_order');
		$data['entry_support'] = $this->language->get('entry_support');
		$data['entry_status'] = $this->language->get('entry_status');

		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_height'] = $this->language->get('entry_height');

		$data['entry_image_category'] = $this->language->get('entry_image_category');
		$data['entry_image_category_thumb'] = $this->language->get('entry_image_category_thumb');


		$data['entry_image_post_thumb'] = $this->language->get('entry_image_post_thumb');
		$data['entry_image_post_popup'] = $this->language->get('entry_image_post_popup');
		$data['entry_image_post'] = $this->language->get('entry_image_post');
		$data['entry_image_post_additional'] = $this->language->get('entry_image_post_additional');
		$data['entry_image_post_related'] = $this->language->get('entry_image_post_related');
		
		$data['entry_subscribeapprove'] = $this->language->get('entry_subscribeapprove');
		$data['entry_subscribeadminmail'] = $this->language->get('entry_subscribeadminmail');
		$data['entry_mail_subject'] = $this->language->get('entry_mail_subject');
		$data['entry_mail_message'] = $this->language->get('entry_mail_message');
		$data['entry_mail_send'] = $this->language->get('entry_mail_send');
		$data['entry_page_title'] = $this->language->get('entry_page_title');
		$data['entry_page_content'] = $this->language->get('entry_page_content');
		
		$data['tab_setting'] = $this->language->get('tab_setting');
		$data['tab_mpblogcategory'] = $this->language->get('tab_mpblogcategory');
		$data['tab_mpblogpost'] = $this->language->get('tab_mpblogpost');
		$data['tab_mpblogmodule'] = $this->language->get('tab_mpblogmodule');
		
		$data['tab_emails'] = $this->language->get('tab_emails');
		$data['tab_email_blogadd'] = $this->language->get('tab_email_blogadd');
		$data['tab_email_blogedit'] = $this->language->get('tab_email_blogedit');

		$data['tab_subscribers'] = $this->language->get('tab_subscribers');
		$data['tab_email_subscribeadmin'] = $this->language->get('tab_email_subscribeadmin');
		$data['tab_email_subscribeapproval'] = $this->language->get('tab_email_subscribeapproval');
		$data['tab_email_subscribepending'] = $this->language->get('tab_email_subscribepending');
		$data['tab_email_subscribeconfirm'] = $this->language->get('tab_email_subscribeconfirm');
		$data['tab_email_unsubscribe'] = $this->language->get('tab_email_unsubscribe');
		$data['tab_email_invalidurl'] = $this->language->get('tab_email_invalidurl');
		
		$data['tab_mpblogcomments'] = $this->language->get('tab_mpblogcomments');

		$data['tab_mpblogcomments_default'] = $this->language->get('tab_mpblogcomments_default');
		$data['tab_mpblogcomments_facebook'] = $this->language->get('tab_mpblogcomments_facebook');
		$data['tab_mpblogcomments_google'] = $this->language->get('tab_mpblogcomments_google');
		$data['tab_mpblogcomments_disqus'] = $this->language->get('tab_mpblogcomments_disqus');

		$data['tab_mpblogrss'] = $this->language->get('tab_mpblogrss');
		$data['tab_mpblogdoc'] = $this->language->get('tab_mpblogdoc');
		$data['tab_mpsupport'] = $this->language->get('tab_mpsupport');
		$data['tab_mpblogs_listing'] = $this->language->get('tab_mpblogs_listing');
		$data['tab_mpblogs_view'] = $this->language->get('tab_mpblogs_view');

		$data['help_category_home'] = $this->language->get('help_category_home');
		$data['help_blog_captcha_comment'] = $this->language->get('help_blog_captcha_comment');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_support'] = $this->language->get('button_support');
		$data['button_design_add'] = $this->language->get('button_design_add');
		$data['button_social_add'] = $this->language->get('button_social_add');
		$data['button_design_remove'] = $this->language->get('button_design_remove');
		$data['button_remove'] = $this->language->get('button_remove');
		
		$data['button_shortcodes'] = $this->language->get('button_shortcodes');
		// module files add into permissions list start
		$data['button_files_permission'] = $this->language->get('button_files_permission');
		// module files add into permissions list end

		// module blog layouts add into Design > Layout list start
		$data['button_add_layouts'] = $this->language->get('button_add_layouts');
		// module blog layouts add into Design > Layout list end

		$data['sh_code'] = $this->language->get('sh_code');
		$data['sh_name'] = $this->language->get('sh_name');
		$data['sh_logo'] = $this->language->get('sh_logo');
		$data['sh_storename'] = $this->language->get('sh_storename');
		$data['sh_storelink'] = $this->language->get('sh_storelink');
		$data['sh_email'] = $this->language->get('sh_email');
		$data['sh_confirmlink'] = $this->language->get('sh_confirmlink');
		$data['sh_confirmcode'] = $this->language->get('sh_confirmcode');

		$data['sh_blog_name'] = $this->language->get('sh_blog_name');
		$data['sh_blog_thumb'] = $this->language->get('sh_blog_thumb');
		$data['sh_blog_description'] = $this->language->get('sh_blog_description');
		$data['sh_blog_shortdescription'] = $this->language->get('sh_blog_shortdescription');
		
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
		
		if (isset($this->error['subscribemail'])) {
			$data['error_subscribemail'] = $this->error['subscribemail'];
		} else {
			$data['error_subscribemail'] = [];
		}

		if (isset($this->error['emails'])) {
			$data['error_emails'] = $this->error['emails'];
		} else {
			$data['error_emails'] = [];
		}
		
		if (isset($this->error['image_post_thumb'])) {
			$data['error_image_post_thumb'] = $this->error['image_post_thumb'];
		} else {
			$data['error_image_post_thumb'] = '';
		}
		
		if (isset($this->error['image_post_popup'])) {
			$data['error_image_post_popup'] = $this->error['image_post_popup'];
		} else {
			$data['error_image_post_popup'] = '';
		}

		if (isset($this->error['image_post'])) {
			$data['error_image_post'] = $this->error['image_post'];
		} else {
			$data['error_image_post'] = '';
		}

		if (isset($this->error['image_post_additional'])) {
			$data['error_image_post_additional'] = $this->error['image_post_additional'];
		} else {
			$data['error_image_post_additional'] = '';
		}

		if (isset($this->error['image_post_related'])) {
			$data['error_image_post_related'] = $this->error['image_post_related'];
		} else {
			$data['error_image_post_related'] = '';
		}

		// blog category
		if (isset($this->error['image_category'])) {
			$data['error_image_category'] = $this->error['image_category'];
		} else {
			$data['error_image_category'] = '';
		}

		
		if (isset($this->error['image_category_thumb'])) {
			$data['error_image_category_thumb'] = $this->error['image_category_thumb'];
		} else {
			$data['error_image_category_thumb'] = '';
		}
		

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $this->token.'=' . $this->session->data[$this->token], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/mpblogsettings', $this->token.'=' . $this->session->data[$this->token], true)
		];

		if (isset($store_id)) {
			$data['action'] = $this->url->link('extension/module/mpblogsettings', $this->token.'=' . $this->session->data[$this->token].'&store_id='. $store_id, true);
		} else {
			$data['action'] = $this->url->link('extension/module/mpblogsettings', $this->token.'=' . $this->session->data[$this->token], true);
		}

		$frontUrl = new Url(HTTP_CATALOG, HTTPS_CATALOG);
		$data['rss_href'] = $frontUrl->link('extension/mpblog/rss', '', true);

		$data['cancel'] = $this->url->link($this->extension_page_path, $this->token.'=' . $this->session->data[$this->token] . '&type=module', true);

		$data['get_token'] = $this->token;
		$data['token'] = $this->session->data[$this->token];

		$data['languages'] = $this->getLanguages();

		$module_info = $this->model_setting_setting->getSetting('mpblog', $store_id);

		if (!isset($module_info['mpblog_category_post_count']))   {
			$data['configuration'] = $this->language->get('text_configuration');
		} else if ((isset($module_info['mpblog_category_post_count']) && is_null($module_info['mpblog_category_post_count']) )) {
			$data['configuration'] = $this->language->get('text_configuration');
		} else {
			$data['configuration'] = '';
		}

		// setting
		if (isset($this->request->post['mpblog_status'])) {
			$data['mpblog_status'] = $this->request->post['mpblog_status'];
		} else if (isset($module_info['mpblog_status'])) {
			$data['mpblog_status'] = $module_info['mpblog_status'];
		} else {
			$data['mpblog_status'] = '0';
		}

		// category
		if (isset($this->request->post['mpblog_home_category'])) {
			$data['mpblog_home_category'] = $this->request->post['mpblog_home_category'];
		} else if (isset($module_info['mpblog_home_category'])) {
			$data['mpblog_home_category'] = $module_info['mpblog_home_category'];
		} else {
			$data['mpblog_home_category'] = 0;
		}

		$this->load->model('extension/mpblog/mpblogcategory');

		$categories = $this->model_extension_mpblog_mpblogcategory->getMpBlogCategories();
		$data['mpblog_categories'] = [];
		foreach ($categories as $key => $category) {
			$data['mpblog_categories'][] = [
				'mpblogcategory_id' => $category['mpblogcategory_id'],
				'name' => $category['name'],
			];
		}

		if (isset($this->request->post['mpblog_category_post_count'])) {
			$data['mpblog_category_post_count'] = $this->request->post['mpblog_category_post_count'];
		} else if (isset($module_info['mpblog_category_post_count'])) {
			$data['mpblog_category_post_count'] = $module_info['mpblog_category_post_count'];
		}  else {
			$data['mpblog_category_post_count'] = 0;
		}

		if (isset($this->request->post['mpblog_category_image'])) {
			$data['mpblog_category_image'] = $this->request->post['mpblog_category_image'];
		} else if (isset($module_info['mpblog_category_image'])) {
			$data['mpblog_category_image'] = $module_info['mpblog_category_image'];
		}  else {
			$data['mpblog_category_image'] = 1;
		}

		if (isset($this->request->post['mpblog_category_description'])) {
			$data['mpblog_category_description'] = $this->request->post['mpblog_category_description'];
		} elseif (isset($module_info['mpblog_category_description'])) {
			$data['mpblog_category_description'] = $module_info['mpblog_category_description'];
		} else {
			$data['mpblog_category_description'] = 1;
		}

		if (isset($this->request->post['mpblog_category_page_limit'])) {
			$data['mpblog_category_page_limit'] = $this->request->post['mpblog_category_page_limit'];
		} elseif (isset($module_info['mpblog_category_page_limit'])) {
			$data['mpblog_category_page_limit'] = $module_info['mpblog_category_page_limit'];
		} else {
			$data['mpblog_category_page_limit'] = 15;
		}

		if (isset($this->request->post['mpblog_category_design'])) {
			$data['mpblog_category_design'] = $this->request->post['mpblog_category_design'];
		} elseif (isset($module_info['mpblog_category_design'])) {
			$data['mpblog_category_design'] = $module_info['mpblog_category_design'];
		} else {
			$data['mpblog_category_design'] = 3;
		}

		if (isset($this->request->post['mpblog_image_category_width'])) {
	      $data['mpblog_image_category_width'] = $this->request->post['mpblog_image_category_width'];
	    } elseif (isset($module_info['mpblog_image_category_width'])) {
	      $data['mpblog_image_category_width'] = $module_info['mpblog_image_category_width'];
		} else {
	      $data['mpblog_image_category_width'] = 405;
	    }
	    
	    if (isset($this->request->post['mpblog_image_category_height'])) {
	      $data['mpblog_image_category_height'] = $this->request->post['mpblog_image_category_height'];
	    } elseif (isset($module_info['mpblog_image_category_height'])) {
	      $data['mpblog_image_category_height'] = $module_info['mpblog_image_category_height'];
		} else {
	      $data['mpblog_image_category_height'] = 251;
	    }

	    if (isset($this->request->post['mpblog_image_category_thumb_width'])) {
	      $data['mpblog_image_category_thumb_width'] = $this->request->post['mpblog_image_category_thumb_width'];
	    } elseif (isset($module_info['mpblog_image_category_thumb_width'])) {
	      $data['mpblog_image_category_thumb_width'] = $module_info['mpblog_image_category_thumb_width'];
		} else {
	      $data['mpblog_image_category_thumb_width'] = 405;
	    }

	    if (isset($this->request->post['mpblog_image_category_thumb_height'])) {
	      $data['mpblog_image_category_thumb_height'] = $this->request->post['mpblog_image_category_thumb_height'];
	    } elseif (isset($module_info['mpblog_image_category_thumb_height'])) {
	      $data['mpblog_image_category_thumb_height'] = $module_info['mpblog_image_category_thumb_height'];
		} else {
	      $data['mpblog_image_category_thumb_height'] = 251;
	    }


	    // blog
		if (isset($this->request->post['mpblog_blog_image'])) {
			$data['mpblog_blog_image'] = $this->request->post['mpblog_blog_image'];
		} elseif (isset($module_info['mpblog_blog_image'])) {
			$data['mpblog_blog_image'] = $module_info['mpblog_blog_image'];
		} else {
			$data['mpblog_blog_image'] = 1;
		}

		if (isset($this->request->post['mpblog_blog_image_popup'])) {
			$data['mpblog_blog_image_popup'] = $this->request->post['mpblog_blog_image_popup'];
		} elseif (isset($module_info['mpblog_blog_image_popup'])) {
			$data['mpblog_blog_image_popup'] = $module_info['mpblog_blog_image_popup'];
		} else {
			$data['mpblog_blog_image_popup'] = 1;
		}

		if (isset($this->request->post['mpblog_blog_description'])) {
			$data['mpblog_blog_description'] = $this->request->post['mpblog_blog_description'];
		} elseif (isset($module_info['mpblog_blog_description'])) {
			$data['mpblog_blog_description'] = $module_info['mpblog_blog_description'];
		} else {
			$data['mpblog_blog_description'] = 1;
		}

		if (isset($this->request->post['mpblog_blog_sdescription'])) {
			$data['mpblog_blog_sdescription'] = $this->request->post['mpblog_blog_sdescription'];
		} elseif (isset($module_info['mpblog_blog_sdescription'])) {
			$data['mpblog_blog_sdescription'] = $module_info['mpblog_blog_sdescription'];
		} else {
			$data['mpblog_blog_sdescription'] = 1;
		}

		if (isset($this->request->post['mpblog_blog_sdescription_length'])) {
			$data['mpblog_blog_sdescription_length'] = $this->request->post['mpblog_blog_sdescription_length'];
		} elseif (isset($module_info['mpblog_blog_sdescription_length'])) {
			$data['mpblog_blog_sdescription_length'] = $module_info['mpblog_blog_sdescription_length'];
		} else {
			$data['mpblog_blog_sdescription_length'] = 250;
		}

		if (isset($this->request->post['mpblog_blog_page_limit'])) {
			$data['mpblog_blog_page_limit'] = $this->request->post['mpblog_blog_page_limit'];
		} elseif (isset($module_info['mpblog_blog_page_limit'])) {
			$data['mpblog_blog_page_limit'] = $module_info['mpblog_blog_page_limit'];
		} else {
			$data['mpblog_blog_page_limit'] = 15;
		}

		if (isset($this->request->post['mpblog_blog_author'])) {
			$data['mpblog_blog_author'] = $this->request->post['mpblog_blog_author'];
		} elseif (isset($module_info['mpblog_blog_author'])) {
			$data['mpblog_blog_author'] = $module_info['mpblog_blog_author'];
		} else {
			$data['mpblog_blog_author'] = 1;
		}

		if (isset($this->request->post['mpblog_blog_date'])) {
			$data['mpblog_blog_date'] = $this->request->post['mpblog_blog_date'];
		} elseif (isset($module_info['mpblog_blog_date'])) {
			$data['mpblog_blog_date'] = $module_info['mpblog_blog_date'];
		} else {
			$data['mpblog_blog_date'] = 1;
		}

		if (isset($this->request->post['mpblog_blog_date_format'])) {
			$data['mpblog_blog_date_format'] = $this->request->post['mpblog_blog_date_format'];
		} elseif (isset($module_info['mpblog_blog_date_format'])) {
			$data['mpblog_blog_date_format'] = $module_info['mpblog_blog_date_format'];
		} else {
			$data['mpblog_blog_date_format'] = $this->language->get('date_format_short');
		}

		if (isset($this->request->post['mpblog_blog_show_comment'])) {
			$data['mpblog_blog_show_comment'] = $this->request->post['mpblog_blog_show_comment'];
		} elseif (isset($module_info['mpblog_blog_show_comment'])) {
			$data['mpblog_blog_show_comment'] = $module_info['mpblog_blog_show_comment'];
		} else {
			$data['mpblog_blog_show_comment'] = 1;
		}

		if (isset($this->request->post['mpblog_blog_use_comment'])) {
			$data['mpblog_blog_use_comment'] = $this->request->post['mpblog_blog_use_comment'];
		} elseif (isset($module_info['mpblog_blog_use_comment'])) {
			$data['mpblog_blog_use_comment'] = $module_info['mpblog_blog_use_comment'];
		} else {
			$data['mpblog_blog_use_comment'] = 1;
		}

		if (isset($this->request->post['mpblog_blog_allow_comment'])) {
			$data['mpblog_blog_allow_comment'] = $this->request->post['mpblog_blog_allow_comment'];
		} elseif (isset($module_info['mpblog_blog_allow_comment'])) {
			$data['mpblog_blog_allow_comment'] = $module_info['mpblog_blog_allow_comment'];
		} else {
			$data['mpblog_blog_allow_comment'] = 1;
		}

		if (isset($this->request->post['mpblog_blog_approve_comment'])) {
			$data['mpblog_blog_approve_comment'] = $this->request->post['mpblog_blog_approve_comment'];
		} elseif (isset($module_info['mpblog_blog_approve_comment'])) {
			$data['mpblog_blog_approve_comment'] = $module_info['mpblog_blog_approve_comment'];
		} else {
			$data['mpblog_blog_approve_comment'] = 1;
		}

		if (isset($this->request->post['mpblog_blog_captcha_comment'])) {
			$data['mpblog_blog_captcha_comment'] = $this->request->post['mpblog_blog_captcha_comment'];
		} elseif (isset($module_info['mpblog_blog_captcha_comment'])) {
			$data['mpblog_blog_captcha_comment'] = $module_info['mpblog_blog_captcha_comment'];
		} else {
			$data['mpblog_blog_captcha_comment'] = 1;
		}

		if (isset($this->request->post['mpblog_blog_show_rating'])) {
			$data['mpblog_blog_show_rating'] = $this->request->post['mpblog_blog_show_rating'];
		} elseif (isset($module_info['mpblog_blog_show_rating'])) {
			$data['mpblog_blog_show_rating'] = $module_info['mpblog_blog_show_rating'];
		} else {
			$data['mpblog_blog_show_rating'] = 1;
		}

		if (isset($this->request->post['mpblog_blog_allow_rating'])) {
			$data['mpblog_blog_allow_rating'] = $this->request->post['mpblog_blog_allow_rating'];
		} elseif (isset($module_info['mpblog_blog_allow_rating'])) {
			$data['mpblog_blog_allow_rating'] = $module_info['mpblog_blog_allow_rating'];
		} else {
			$data['mpblog_blog_allow_rating'] = 1;
		}

		if (isset($this->request->post['mpblog_blog_approve_rating'])) {
			$data['mpblog_blog_approve_rating'] = $this->request->post['mpblog_blog_approve_rating'];
		} elseif (isset($module_info['mpblog_blog_approve_rating'])) {
			$data['mpblog_blog_approve_rating'] = $module_info['mpblog_blog_approve_rating'];
		} else {
			$data['mpblog_blog_approve_rating'] = 1;
		}

		if (isset($this->request->post['mpblog_blog_guest_rating'])) {
			$data['mpblog_blog_guest_rating'] = $this->request->post['mpblog_blog_guest_rating'];
		} elseif (isset($module_info['mpblog_blog_guest_rating'])) {
			$data['mpblog_blog_guest_rating'] = $module_info['mpblog_blog_guest_rating'];
		} else {
			$data['mpblog_blog_guest_rating'] = 1;
		}

		if (isset($this->request->post['mpblog_blog_show_readmore'])) {
			$data['mpblog_blog_show_readmore'] = $this->request->post['mpblog_blog_show_readmore'];
		} elseif (isset($module_info['mpblog_blog_show_readmore'])) {
			$data['mpblog_blog_show_readmore'] = $module_info['mpblog_blog_show_readmore'];
		} else {
			$data['mpblog_blog_show_readmore'] = 1;
		}

		if (isset($this->request->post['mpblog_blog_show_tags'])) {
			$data['mpblog_blog_show_tags'] = $this->request->post['mpblog_blog_show_tags'];
		} elseif (isset($module_info['mpblog_blog_show_tags'])) {
			$data['mpblog_blog_show_tags'] = $module_info['mpblog_blog_show_tags'];
		} else {
			$data['mpblog_blog_show_tags'] = 0;
		}

		if (isset($this->request->post['mpblog_blog_viewcount'])) {
			$data['mpblog_blog_viewcount'] = $this->request->post['mpblog_blog_viewcount'];
		} elseif (isset($module_info['mpblog_blog_viewcount'])) {
			$data['mpblog_blog_viewcount'] = $module_info['mpblog_blog_viewcount'];
		} else {
			$data['mpblog_blog_viewcount'] = 1;
		}

		if (isset($this->request->post['mpblog_blog_sharethis'])) {
			$data['mpblog_blog_sharethis'] = $this->request->post['mpblog_blog_sharethis'];
		} elseif (isset($module_info['mpblog_blog_sharethis'])) {
			$data['mpblog_blog_sharethis'] = $module_info['mpblog_blog_sharethis'];
		} else {
			$data['mpblog_blog_sharethis'] = 1;
		}

		if (isset($this->request->post['mpblog_blog_nextprev'])) {
			$data['mpblog_blog_nextprev'] = $this->request->post['mpblog_blog_nextprev'];
		} elseif (isset($module_info['mpblog_blog_nextprev'])) {
			$data['mpblog_blog_nextprev'] = $module_info['mpblog_blog_nextprev'];
		} else {
			$data['mpblog_blog_nextprev'] = 1;
		}

		if (isset($this->request->post['mpblog_blog_nextprev_title'])) {
			$data['mpblog_blog_nextprev_title'] = $this->request->post['mpblog_blog_nextprev_title'];
		} elseif (isset($module_info['mpblog_blog_nextprev_title'])) {
			$data['mpblog_blog_nextprev_title'] = $module_info['mpblog_blog_nextprev_title'];
		} else {
			$data['mpblog_blog_nextprev_title'] = 1;
		}

		if (isset($this->request->post['mpblog_blog_view'])) {
			$data['mpblog_blog_view'] = $this->request->post['mpblog_blog_view'];
		} elseif (isset($module_info['mpblog_blog_view'])) {
			$data['mpblog_blog_view'] = $module_info['mpblog_blog_view'];
		} else {
			$data['mpblog_blog_view'] = 'GRID';
		}

		if (isset($this->request->post['mpblog_category_view'])) {
			$data['mpblog_category_view'] = $this->request->post['mpblog_category_view'];
		} elseif (isset($module_info['mpblog_category_view'])) {
			$data['mpblog_category_view'] = $module_info['mpblog_category_view'];
		} else {
			$data['mpblog_category_view'] = 'GRID';
		}

		if (isset($this->request->post['mpblog_blog_sociallocation'])) {
			$data['mpblog_blog_sociallocation'] = $this->request->post['mpblog_blog_sociallocation'];
		} elseif (isset($module_info['mpblog_blog_sociallocation'])) {
			$data['mpblog_blog_sociallocation'] = (array)$module_info['mpblog_blog_sociallocation'];
		} else {
			$data['mpblog_blog_sociallocation'] = ['TOP','BOTTOM'];
		}

		if (isset($this->request->post['mpblog_blog_viewsocial'])) {
			$data['mpblog_blog_viewsocial'] = $this->request->post['mpblog_blog_viewsocial'];
		} elseif (isset($module_info['mpblog_blog_viewsocial'])) {
			$data['mpblog_blog_viewsocial'] = $module_info['mpblog_blog_viewsocial'];
		} else {
			$data['mpblog_blog_viewsocial'] = 1;
		}

		if (isset($this->request->post['mpblog_blog_viewwishlist'])) {
			$data['mpblog_blog_viewwishlist'] = $this->request->post['mpblog_blog_viewwishlist'];
		} elseif (isset($module_info['mpblog_blog_viewwishlist'])) {
			$data['mpblog_blog_viewwishlist'] = $module_info['mpblog_blog_viewwishlist'];
		} else {
			$data['mpblog_blog_viewwishlist'] = 1;
		}

		if (isset($this->request->post['mpblog_blog_design'])) {
			$data['mpblog_blog_design'] = $this->request->post['mpblog_blog_design'];
		} elseif (isset($module_info['mpblog_blog_design'])) {
			$data['mpblog_blog_design'] = $module_info['mpblog_blog_design'];
		} else {
			$data['mpblog_blog_design'] = 3;
		}

		if (isset($this->request->post['mpblog_image_post_thumb_width'])) {
			$data['mpblog_image_post_thumb_width'] = $this->request->post['mpblog_image_post_thumb_width'];
		} elseif (isset($module_info['mpblog_image_post_thumb_width'])) {
			$data['mpblog_image_post_thumb_width'] = $module_info['mpblog_image_post_thumb_width'];
		} else {
			$data['mpblog_image_post_thumb_width'] = 408;
		}

		if (isset($this->request->post['mpblog_image_post_thumb_height'])) {
			$data['mpblog_image_post_thumb_height'] = $this->request->post['mpblog_image_post_thumb_height'];
		} elseif (isset($module_info['mpblog_image_post_thumb_height'])) {
			$data['mpblog_image_post_thumb_height'] = $module_info['mpblog_image_post_thumb_height'];
		} else {
			$data['mpblog_image_post_thumb_height'] = 251;
		}

		if (isset($this->request->post['mpblog_image_post_popup_width'])) {
			$data['mpblog_image_post_popup_width'] = $this->request->post['mpblog_image_post_popup_width'];
		} elseif (isset($module_info['mpblog_image_post_popup_width'])) {
			$data['mpblog_image_post_popup_width'] = $module_info['mpblog_image_post_popup_width'];
		} else {
			$data['mpblog_image_post_popup_width'] = 1140;
		}
		
		if (isset($this->request->post['mpblog_image_post_popup_height'])) {
			$data['mpblog_image_post_popup_height'] = $this->request->post['mpblog_image_post_popup_height'];
		} elseif (isset($module_info['mpblog_image_post_popup_height'])) {
			$data['mpblog_image_post_popup_height'] = $module_info['mpblog_image_post_popup_height'];
		} else {
			$data['mpblog_image_post_popup_height'] = 700;
		}
		
		if (isset($this->request->post['mpblog_image_post_width'])) {
			$data['mpblog_image_post_width'] = $this->request->post['mpblog_image_post_width'];
		} elseif (isset($module_info['mpblog_image_post_width'])) {
			$data['mpblog_image_post_width'] = $module_info['mpblog_image_post_width'];
		} else {
			$data['mpblog_image_post_width'] = 1140;
		}
		
		if (isset($this->request->post['mpblog_image_post_height'])) {
			$data['mpblog_image_post_height'] = $this->request->post['mpblog_image_post_height'];
		} elseif (isset($module_info['mpblog_image_post_height'])) {
			$data['mpblog_image_post_height'] = $module_info['mpblog_image_post_height'];
		} else {
			$data['mpblog_image_post_height'] = 700;
		}
		
		if (isset($this->request->post['mpblog_image_post_additional_width'])) {
			$data['mpblog_image_post_additional_width'] = $this->request->post['mpblog_image_post_additional_width'];
		} elseif (isset($module_info['mpblog_image_post_additional_width'])) {
			$data['mpblog_image_post_additional_width'] = $module_info['mpblog_image_post_additional_width'];
		} else {
			$data['mpblog_image_post_additional_width'] = 220;
		}
		
		if (isset($this->request->post['mpblog_image_post_additional_height'])) {
			$data['mpblog_image_post_additional_height'] = $this->request->post['mpblog_image_post_additional_height'];
		} elseif (isset($module_info['mpblog_image_post_additional_height'])) {
			$data['mpblog_image_post_additional_height'] = $module_info['mpblog_image_post_additional_height'];
		} else {
			$data['mpblog_image_post_additional_height'] = 135;
		}
		
		if (isset($this->request->post['mpblog_image_post_related_width'])) {
			$data['mpblog_image_post_related_width'] = $this->request->post['mpblog_image_post_related_width'];
		} elseif (isset($module_info['mpblog_image_post_related_width'])) {
			$data['mpblog_image_post_related_width'] = $module_info['mpblog_image_post_related_width'];
		} else {
			$data['mpblog_image_post_related_width'] = 408;
		}
		
		if (isset($this->request->post['mpblog_image_post_related_height'])) {
			$data['mpblog_image_post_related_height'] = $this->request->post['mpblog_image_post_related_height'];
		} elseif (isset($module_info['mpblog_image_post_related_height'])) {
			$data['mpblog_image_post_related_height'] = $module_info['mpblog_image_post_related_height'];
		} else {
			$data['mpblog_image_post_related_height'] = 251;
		}

		
		// module subscribers
		if (isset($this->request->post['mpblog_subscribeapprove'])) {
			$data['mpblog_subscribeapprove'] = $this->request->post['mpblog_subscribeapprove'];
		} elseif (isset($module_info['mpblog_subscribeapprove'])) {
			$data['mpblog_subscribeapprove'] = $module_info['mpblog_subscribeapprove'];
		} else {
			$data['mpblog_subscribeapprove'] = 'AUTO';
		}
		if (isset($this->request->post['mpblog_subscribeadminmail_status'])) {
			$data['mpblog_subscribeadminmail_status'] = $this->request->post['mpblog_subscribeadminmail_status'];
		} elseif (isset($module_info['mpblog_subscribeadminmail_status'])) {
			$data['mpblog_subscribeadminmail_status'] = $module_info['mpblog_subscribeadminmail_status'];
		} else {
			$data['mpblog_subscribeadminmail_status'] = 0;
		}
		if (isset($this->request->post['mpblog_subscribeapproval_status'])) {
			$data['mpblog_subscribeapproval_status'] = $this->request->post['mpblog_subscribeapproval_status'];
		} elseif (isset($module_info['mpblog_subscribeapproval_status'])) {
			$data['mpblog_subscribeapproval_status'] = $module_info['mpblog_subscribeapproval_status'];
		} else {
			$data['mpblog_subscribeapproval_status'] = 0;
		}
		if (isset($this->request->post['mpblog_subscribepending_status'])) {
			$data['mpblog_subscribepending_status'] = $this->request->post['mpblog_subscribepending_status'];
		} elseif (isset($module_info['mpblog_subscribepending_status'])) {
			$data['mpblog_subscribepending_status'] = $module_info['mpblog_subscribepending_status'];
		} else {
			$data['mpblog_subscribepending_status'] = 0;
		}
		if (isset($this->request->post['mpblog_subscribeconfirm_status'])) {
			$data['mpblog_subscribeconfirm_status'] = $this->request->post['mpblog_subscribeconfirm_status'];
		} elseif (isset($module_info['mpblog_subscribeconfirm_status'])) {
			$data['mpblog_subscribeconfirm_status'] = $module_info['mpblog_subscribeconfirm_status'];
		} else {
			$data['mpblog_subscribeconfirm_status'] = 0;
		}
		if (isset($this->request->post['mpblog_unsubscribe_status'])) {
			$data['mpblog_unsubscribe_status'] = $this->request->post['mpblog_unsubscribe_status'];
		} elseif (isset($module_info['mpblog_unsubscribe_status'])) {
			$data['mpblog_unsubscribe_status'] = $module_info['mpblog_unsubscribe_status'];
		} else {
			$data['mpblog_unsubscribe_status'] = 0;
		}
		if (isset($this->request->post['mpblog_subscribeadminmail'])) {
			$data['mpblog_subscribeadminmail'] = $this->request->post['mpblog_subscribeadminmail'];
		} elseif (isset($module_info['mpblog_subscribeadminmail'])) {
			$data['mpblog_subscribeadminmail'] = $module_info['mpblog_subscribeadminmail'];
		} else {
			$data['mpblog_subscribeadminmail'] = $this->config->get('config_email');
		}

		if (isset($this->request->post['mpblog_subscribemail'])) {
			$data['mpblog_subscribemail'] = $this->request->post['mpblog_subscribemail'];
		} elseif (isset($module_info['mpblog_subscribemail'])) {
			$data['mpblog_subscribemail'] = $module_info['mpblog_subscribemail'];
		} else {
			$data['mpblog_subscribemail'] = [];
		}

		if (isset($this->request->post['mpblog_blogadd_status'])) {
			$data['mpblog_blogadd_status'] = $this->request->post['mpblog_blogadd_status'];
		} elseif (isset($module_info['mpblog_blogadd_status'])) {
			$data['mpblog_blogadd_status'] = $module_info['mpblog_blogadd_status'];
		} else {
			$data['mpblog_blogadd_status'] = 0;
		}

		if (isset($this->request->post['mpblog_blogedit_status'])) {
			$data['mpblog_blogedit_status'] = $this->request->post['mpblog_blogedit_status'];
		} elseif (isset($module_info['mpblog_blogedit_status'])) {
			$data['mpblog_blogedit_status'] = $module_info['mpblog_blogedit_status'];
		} else {
			$data['mpblog_blogedit_status'] = 0;
		}


		if (isset($this->request->post['mpblog_emails'])) {
			$data['mpblog_emails'] = $this->request->post['mpblog_emails'];
		} elseif (isset($module_info['mpblog_emails'])) {
			$data['mpblog_emails'] = $module_info['mpblog_emails'];
		} else {
			$data['mpblog_emails'] = [];
		}
		
		// module social media

		if (isset($this->request->post['mpblog_social'])) {
			$data['mpblog_socials'] = $this->request->post['mpblog_social'];
		} elseif (isset($module_info['mpblog_social'])) {
			$data['mpblog_socials'] = $module_info['mpblog_social'];
		} else {
			$data['mpblog_socials'] = [];
		}

		// comments

		// default
		if (isset($this->request->post['mpblog_comments_default'])) {
			$data['mpblog_comments_default'] = $this->request->post['mpblog_comments_default'];
		} elseif (isset($module_info['mpblog_comments_default'])) {
			$data['mpblog_comments_default'] = $module_info['mpblog_comments_default'];
		} else {
			$data['mpblog_comments_default'] = 1;
		}

		if (isset($this->request->post['mpblog_comments_default_guest'])) {
			$data['mpblog_comments_default_guest'] = $this->request->post['mpblog_comments_default_guest'];
		} elseif (isset($module_info['mpblog_comments_default_guest'])) {
			$data['mpblog_comments_default_guest'] = $module_info['mpblog_comments_default_guest'];
		} else {
			$data['mpblog_comments_default_guest'] = 0;
		}


		// facebook

		if (isset($this->request->post['mpblog_comments_facebook'])) {
			$data['mpblog_comments_facebook'] = $this->request->post['mpblog_comments_facebook'];
		} elseif (isset($module_info['mpblog_comments_facebook'])) {
			$data['mpblog_comments_facebook'] = $module_info['mpblog_comments_facebook'];
		} else {
			$data['mpblog_comments_facebook'] = 0;
		}
		
		if (isset($this->request->post['mpblog_facebook_appid'])) {
			$data['mpblog_facebook_appid'] = $this->request->post['mpblog_facebook_appid'];
		} elseif (isset($module_info['mpblog_facebook_appid'])) {
			$data['mpblog_facebook_appid'] = $module_info['mpblog_facebook_appid'];
		} else {
			$data['mpblog_facebook_appid'] = '';
		}

		if (isset($this->request->post['mpblog_facebook_nocomment'])) {
			$data['mpblog_facebook_nocomment'] = $this->request->post['mpblog_facebook_nocomment'];
		} elseif (isset($module_info['mpblog_facebook_nocomment'])) {
			$data['mpblog_facebook_nocomment'] = $module_info['mpblog_facebook_nocomment'];
		} else {
			$data['mpblog_facebook_nocomment'] = 10;
		}

		if (isset($this->request->post['mpblog_facebook_color'])) {
			$data['mpblog_facebook_color'] = $this->request->post['mpblog_facebook_color'];
		} elseif (isset($module_info['mpblog_facebook_color'])) {
			$data['mpblog_facebook_color'] = $module_info['mpblog_facebook_color'];
		} else {
			$data['mpblog_facebook_color'] = 'light'; // light, dark
		}

		if (isset($this->request->post['mpblog_facebook_order'])) {
			$data['mpblog_facebook_order'] = $this->request->post['mpblog_facebook_order'];
		} elseif (isset($module_info['mpblog_facebook_order'])) {
			$data['mpblog_facebook_order'] = $module_info['mpblog_facebook_order'];
		} else {
			$data['mpblog_facebook_order'] = 'time'; //social, reverse_time, time
		}

		if (isset($this->request->post['mpblog_facebook_width'])) {
			$data['mpblog_facebook_width'] = $this->request->post['mpblog_facebook_width'];
		} elseif (isset($module_info['mpblog_facebook_width'])) {
			$data['mpblog_facebook_width'] = $module_info['mpblog_facebook_width'];
		} else {
			$data['mpblog_facebook_width'] = '500'; // min 320
		}

		// google

		if (isset($this->request->post['mpblog_comments_google'])) {
			$data['mpblog_comments_google'] = $this->request->post['mpblog_comments_google'];
		} elseif (isset($module_info['mpblog_comments_google'])) {
			$data['mpblog_comments_google'] = $module_info['mpblog_comments_google'];
		} else {
			$data['mpblog_comments_google'] = 0;
		}

		// disqus

		if (isset($this->request->post['mpblog_comments_disqus'])) {
			$data['mpblog_comments_disqus'] = $this->request->post['mpblog_comments_disqus'];
		} elseif (isset($module_info['mpblog_comments_disqus'])) {
			$data['mpblog_comments_disqus'] = $module_info['mpblog_comments_disqus'];
		} else {
			$data['mpblog_comments_disqus'] = 0;
		}

		if (isset($this->request->post['mpblog_comment_disqus_code'])) {
			$data['mpblog_comment_disqus_code'] = $this->request->post['mpblog_comment_disqus_code'];
		} elseif (isset($module_info['mpblog_comment_disqus_code'])) {
			$data['mpblog_comment_disqus_code'] = $module_info['mpblog_comment_disqus_code'];
		} else {
			$data['mpblog_comment_disqus_code'] = '';
		}

		if (isset($this->request->post['mpblog_comment_disqus_count'])) {
			$data['mpblog_comment_disqus_count'] = $this->request->post['mpblog_comment_disqus_count'];
		} elseif (isset($module_info['mpblog_comment_disqus_count'])) {
			$data['mpblog_comment_disqus_count'] = $module_info['mpblog_comment_disqus_count'];
		} else {
			$data['mpblog_comment_disqus_count'] = '';
		}

		// rss feed

		if (isset($this->request->post['mpblog_rssfeed_title'])) {
			$data['mpblog_rssfeed_title'] = $this->request->post['mpblog_rssfeed_title'];
		} elseif (isset($module_info['mpblog_rssfeed_title'])) {
			$data['mpblog_rssfeed_title'] = $module_info['mpblog_rssfeed_title'];
		} else {
			$data['mpblog_rssfeed_title'] =  $this->config->get('config_name') . ' Blog Feed';
		}
		
		if (isset($this->request->post['mpblog_rssfeed_description'])) {
			$data['mpblog_rssfeed_description'] = $this->request->post['mpblog_rssfeed_description'];
		} elseif (isset($module_info['mpblog_rssfeed_description'])) {
			$data['mpblog_rssfeed_description'] = $module_info['mpblog_rssfeed_description'];
		} else {
			$data['mpblog_rssfeed_description'] =  $this->config->get('config_name') . ' Blog Feed Description';
		}

		if (isset($this->request->post['mpblog_rssfeed_format'])) {
			$data['mpblog_rssfeed_format'] = $this->request->post['mpblog_rssfeed_format'];
		} elseif (isset($module_info['mpblog_rssfeed_format'])) {
			$data['mpblog_rssfeed_format'] = $module_info['mpblog_rssfeed_format'];
		} else {
			$data['mpblog_rssfeed_format'] =  'Atom';
		}

		if (isset($this->request->post['mpblog_rssfeed_limit'])) {
			$data['mpblog_rssfeed_limit'] = $this->request->post['mpblog_rssfeed_limit'];
		} elseif (isset($module_info['mpblog_rssfeed_limit'])) {
			$data['mpblog_rssfeed_limit'] = $module_info['mpblog_rssfeed_limit'];
		} else {
			$data['mpblog_rssfeed_limit'] =  50;
		}

		if (isset($this->request->post['mpblog_rssfeed_web_master'])) {
			$data['mpblog_rssfeed_web_master'] = $this->request->post['mpblog_rssfeed_web_master'];
		} elseif (isset($module_info['mpblog_rssfeed_web_master'])) {
			$data['mpblog_rssfeed_web_master'] = $module_info['mpblog_rssfeed_web_master'];
		} else {
			$data['mpblog_rssfeed_web_master'] =  'support@modulepoints.com (Module Points)';
		}
		
		if (isset($this->request->post['mpblog_rssfeed_copy_write'])) {
			$data['mpblog_rssfeed_copy_write'] = $this->request->post['mpblog_rssfeed_copy_write'];
		} elseif (isset($module_info['mpblog_rssfeed_copy_write'])) {
			$data['mpblog_rssfeed_copy_write'] = $module_info['mpblog_rssfeed_copy_write'];
		} else {
			$data['mpblog_rssfeed_copy_write'] =  '(&copy;) Module Points | M-Blog';
		}


		$this->load->model('setting/store');
		$data['stores'] = $this->model_setting_store->getStores();

		$store_info = $this->model_setting_store->getStore($store_id);
		if ($store_info) {
			$data['store_name'] = $store_info['name'];
		} else {
			$data['store_name'] = $this->language->get('text_default');
		}

		$data['text_editor'] = $this->textEditor($data);

		$data['mpblogmenu'] = $this->load->controller('extension/mpblog/mpblogmenu');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->viewLoad('extension/module/mpblogsettings', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/module/mpblogsettings')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		

		if ($this->request->post['mpblog_subscribeadminmail_status']) {
			foreach ($this->request->post['mpblog_subscribemail']['admin'] as $language_id => $value) {

				if ((utf8_strlen($value['subject']) < 3) || (utf8_strlen($value['subject']) > 255)) {
					$this->error['subscribemail']['admin'][$language_id]['subject'] = $this->language->get('error_subject');
				}

				if ((utf8_strlen($value['message']) < 10)) {
					$this->error['subscribemail']['admin'][$language_id]['message'] = $this->language->get('error_message');
				}
			}
		}

		if ($this->request->post['mpblog_subscribeapproval_status']) {
			foreach ($this->request->post['mpblog_subscribemail']['approval'] as $language_id => $value) {
				if ((utf8_strlen($value['subject']) < 3) || (utf8_strlen($value['subject']) > 255)) {
					$this->error['subscribemail']['approval'][$language_id]['subject'] = $this->language->get('error_subject');
				}

				if ((utf8_strlen($value['message']) < 10)) {
					$this->error['subscribemail']['approval'][$language_id]['message'] = $this->language->get('error_message');
				}
			}
		}

		if ($this->request->post['mpblog_subscribepending_status']) {
			foreach ($this->request->post['mpblog_subscribemail']['pending'] as $language_id => $value) {
				if ((utf8_strlen($value['subject']) < 3) || (utf8_strlen($value['subject']) > 255)) {
					$this->error['subscribemail']['pending'][$language_id]['subject'] = $this->language->get('error_subject');
				}

				if ((utf8_strlen($value['message']) < 10)) {
					$this->error['subscribemail']['pending'][$language_id]['message'] = $this->language->get('error_message');
				}
			}
		}

		if ($this->request->post['mpblog_subscribeconfirm_status']) {
			foreach ($this->request->post['mpblog_subscribemail']['confirm'] as $language_id => $value) {
				if ((utf8_strlen($value['subject']) < 3) || (utf8_strlen($value['subject']) > 255)) {
					$this->error['subscribemail']['confirm'][$language_id]['subject'] = $this->language->get('error_subject');
				}

				if ((utf8_strlen($value['message']) < 10)) {
					$this->error['subscribemail']['confirm'][$language_id]['message'] = $this->language->get('error_message');
				}

				if ((utf8_strlen($value['title']) < 3) || (utf8_strlen($value['title']) > 255)) {
					$this->error['subscribemail']['confirm'][$language_id]['title'] = $this->language->get('error_pagetitle');
				}

				if ((utf8_strlen($value['content']) < 10)) {
					$this->error['subscribemail']['confirm'][$language_id]['content'] = $this->language->get('error_pagecontent');
				}
			}
		}

		if ($this->request->post['mpblog_unsubscribe_status']) {
			foreach ($this->request->post['mpblog_subscribemail']['unsubscribe'] as $language_id => $value) {
				if ((utf8_strlen($value['subject']) < 3) || (utf8_strlen($value['subject']) > 255)) {
					$this->error['subscribemail']['unsubscribe'][$language_id]['subject'] = $this->language->get('error_subject');
				}

				if ((utf8_strlen($value['message']) < 10)) {
					$this->error['subscribemail']['unsubscribe'][$language_id]['message'] = $this->language->get('error_message');
				}

				if ((utf8_strlen($value['title']) < 3) || (utf8_strlen($value['title']) > 255)) {
					$this->error['subscribemail']['unsubscribe'][$language_id]['title'] = $this->language->get('error_pagetitle');
				}

				if ((utf8_strlen($value['content']) < 10)) {
					$this->error['subscribemail']['unsubscribe'][$language_id]['content'] = $this->language->get('error_pagecontent');
				}
			}
		}

		if ($this->request->post['mpblog_subscribeconfirm_status'] || $this->request->post['mpblog_unsubscribe_status']) {

			foreach ($this->request->post['mpblog_subscribemail']['invalid'] as $language_id => $value) {
				
				if ((utf8_strlen($value['title']) < 3) || (utf8_strlen($value['title']) > 255)) {
					$this->error['subscribemail']['invalid'][$language_id]['title'] = $this->language->get('error_pagetitle');
				}

				if ((utf8_strlen($value['content']) < 10)) {
					$this->error['subscribemail']['invalid'][$language_id]['content'] = $this->language->get('error_pagecontent');
				}
			}
		}

		// Blog Add/Edit Emails
		if ($this->request->post['mpblog_blogadd_status']) {
			foreach ($this->request->post['mpblog_emails']['blogadd'] as $language_id => $value) {
				if ((utf8_strlen($value['subject']) < 3) || (utf8_strlen($value['subject']) > 255)) {
					$this->error['emails']['blogadd'][$language_id]['subject'] = $this->language->get('error_subject');
				}

				if ((utf8_strlen($value['message']) < 10)) {
					$this->error['emails']['blogadd'][$language_id]['message'] = $this->language->get('error_message');
				}
			}
		}

		if ($this->request->post['mpblog_blogedit_status']) {
			foreach ($this->request->post['mpblog_emails']['blogedit'] as $language_id => $value) {
				if ((utf8_strlen($value['subject']) < 3) || (utf8_strlen($value['subject']) > 255)) {
					$this->error['emails']['blogedit'][$language_id]['subject'] = $this->language->get('error_subject');
				}

				if ((utf8_strlen($value['message']) < 10)) {
					$this->error['emails']['blogedit'][$language_id]['message'] = $this->language->get('error_message');
				}
			}
		}
		
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		return !$this->error;
	}

	// public function dashboard(){
	// 	$data['get_token'] = $this->token;
	// 	$data['token'] = $this->session->data[$this->token];


	// 	$data['header'] = $this->load->controller('common/header');
	// 	$data['column_left'] = $this->load->controller('common/column_left');
	// 	$data['footer'] = $this->load->controller('common/footer');

	// 	$this->response->setOutput($this->viewLoad('extension/mpblog/dashboard', $data));
 	
	// }
}