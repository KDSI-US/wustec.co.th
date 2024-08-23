<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleMpPhotoGallerySetting extends Controller {
	use mpphotogallery\trait_mpphotogallery;
	private $error = [];
	private $installed = [];
	private $files = [
		'extension/mpphotogallery/gallery',
		'extension/mpphotogallery/mtabs',
		'extension/module/mpgallery',
		'extension/module/mpphoto',
		'extension/module/mpphotogallery_setting',
		'extension/module/mpvideo'
	];

	public function __construct($registry) {
		parent :: __construct($registry);
		$this->igniteTraitMpPhotoGallery($registry);
	}

	public function install() {
		// install database tables
		$this->load->model('extension/mpphotogallery/gallery');
		$this->model_extension_mpphotogallery_gallery->CreateMPGalleryTable();

		// Add permissions to extension files dynamically
		$this->addFilesInPermissions($this->files);
	}


	private function addFilesInPermissions(array $files) {
		if ($this->user->hasPermission('modify', 'extension/module/mpphotogallery_setting')) {
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
		$this->load->language('extension/module/mpphotogallery_setting');

		$this->addFilesInPermissions($this->detectFilesForPermissions());
		$this->session->data['success'] = $this->language->get('text_success_files_permission');
		$json['redirect'] = str_replace("&amp;", "&", $this->url->link('extension/module/mpphotogallery_setting', $this->token.'=' . $this->session->data[$this->token], true));

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

	public function getMenu() {
		$this->load->language('mpphotogallery/gallery_menu');
		$menu = [];
		$children = [];

		if ($this->user->hasPermission('access', 'extension/module/mpphotogallery_setting')) {
			$children[] = [
				'name'     => $this->language->get('text_settings'),
				'icon'	   => 'fa-cog',
				'href'     => $this->url->link('extension/module/mpphotogallery_setting', $this->token.'=' . $this->session->data[$this->token], true),
				'children' => []
			];
		}

		if ($this->user->hasPermission('access', 'extension/mpphotogallery/gallery')) {
			$children[] = [
				'name'     => $this->language->get('text_mpphotogallery_gallery_add'),
				'icon'	   => 'fa-plus',
				'href'     => $this->url->link('extension/mpphotogallery/gallery/add', $this->token.'=' . $this->session->data[$this->token], true),
				'children' => []
			];
			$children[] = [
				'name'     => $this->language->get('text_mpphotogallery_gallery'),
				'icon'	   => 'fa-list',
				'href'     => $this->url->link('extension/mpphotogallery/gallery', $this->token.'=' . $this->session->data[$this->token], true),
				'children' => []
			];
		}

		if ($this->moduleIsInstalled('mpgallery') && $this->user->hasPermission('access', 'extension/module/mpgallery')) {

			$this->load->model($this->model_file['extension/module']['path']);
			$mpgallery_modules = $this->{$this->model_file['extension/module']['obj']}->getModulesByCode('mpgallery');

			$subchildren = [];
			$subchildren[] = [
				'name'     => $this->language->get('text_mppgm_gallery_add'),
				'href'     => $this->url->link('extension/module/mpgallery', $this->token.'=' . $this->session->data[$this->token], true),
				'children' => []
			];
			foreach ($mpgallery_modules as $mpgallery_module) {
				$subchildren[] = [
					'name'     => $mpgallery_module['name'],
					'href'     => $this->url->link('extension/module/mpgallery', $this->token.'=' . $this->session->data[$this->token] . '&module_id=' . $mpgallery_module['module_id'], true),
					'children' => []
				];
			}
			$children[] = [
				'name'     => $this->language->get('text_mppgm_gallery'),
				'href'     => '',
				'icon'	   => 'fa-puzzle-piece',
				'children' => $subchildren
			];
		}
		if ($this->moduleIsInstalled('mpphoto') && $this->user->hasPermission('access', 'extension/module/mpphoto')) {
			$this->load->model($this->model_file['extension/module']['path']);
			$mpgallery_modules = $this->{$this->model_file['extension/module']['obj']}->getModulesByCode('mpphoto');

			$subchildren = [];
			$subchildren[] = [
				'name'     => $this->language->get('text_mppgm_gallery_add'),
				'href'     => $this->url->link('extension/module/mpphoto', $this->token.'=' . $this->session->data[$this->token], true),
				'children' => []
			];
			foreach ($mpgallery_modules as $mpgallery_module) {
				$subchildren[] = [
					'name'     => $mpgallery_module['name'],
					'href'     => $this->url->link('extension/module/mpphoto', $this->token.'=' . $this->session->data[$this->token] . '&module_id=' . $mpgallery_module['module_id'], true),
					'children' => []
				];
			}

			$children[] = [
				'name'     => $this->language->get('text_mppgm_photo'),
				'href'     => '',
				'icon'	   => 'fa-puzzle-piece',
				'children' => $subchildren
			];
		}
		if ($this->moduleIsInstalled('mpvideo') && $this->user->hasPermission('access', 'extension/module/mpvideo')) {
			$this->load->model($this->model_file['extension/module']['path']);
			$mpgallery_modules = $this->{$this->model_file['extension/module']['obj']}->getModulesByCode('mpvideo');

			$subchildren = [];
			$subchildren[] = [
				'name'     => $this->language->get('text_mppgm_gallery_add'),
				'href'     => $this->url->link('extension/module/mpvideo', $this->token.'=' . $this->session->data[$this->token], true),
				'children' => []
			];
			foreach ($mpgallery_modules as $mpgallery_module) {
				$subchildren[] = [
					'name'     => $mpgallery_module['name'],
					'href'     => $this->url->link('extension/module/mpvideo', $this->token.'=' . $this->session->data[$this->token] . '&module_id=' . $mpgallery_module['module_id'], true),
					'children' => []
				];
			}

			$children[] = [
				'name'     => $this->language->get('text_mppgm_video'),
				'href'     => '',
				'icon'	   => 'fa-puzzle-piece',
				'children' => $subchildren
			];
		}

		if ($children) {
			$menu = [
				'id'       => 'mpphotogallery_setting',
				'icon'	   => 'fa-file-image-o',
				'name'     => $this->language->get('text_mpphotogallery'),
				'href'     => '',
				'children' => $children
			];
		}

		// || !$this->config->get('module_mpphotogallery_setting_status')
		if (!$this->moduleIsInstalled('mpphotogallery_setting')) {
			return [];
		}
		return $menu;
	}

	public function index() {
		$this->load->language('extension/module/mpphotogallery_setting');

		$this->document->addStyle('view/javascript/mpphotogallery/colorpicker/css/bootstrap-colorpicker.css');
		$this->document->addScript('view/javascript/mpphotogallery/colorpicker/js/bootstrap-colorpicker.js');

		// show a alert message for files that are not in premissions list
		if ($this->user->hasPermission('modify', 'extension/module/mpphotogallery_setting')) {
			$data['files'] = $this->detectFilesForPermissions();
		} else {
			$data['files'] = [];
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('gallery_setting', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/module/mpphotogallery_setting', $this->token.'=' . $this->session->data[$this->token], true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_gsetting'] = $this->language->get('text_gsetting');
		$data['text_colors'] = $this->language->get('text_colors');
		// gallery for product task starts
		$data['text_product'] = $this->language->get('text_product');
		$data['text_gallery_album'] = $this->language->get('text_gallery_album');
		$data['text_gallery_album_photos'] = $this->language->get('text_gallery_album_photos');
		// gallery for product task ends
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_countphoto'] = $this->language->get('entry_countphoto');
		$data['entry_photo_limit'] = $this->language->get('entry_photo_limit');
		$data['entry_album_limit'] = $this->language->get('entry_album_limit');
		$data['entry_popup'] = $this->language->get('entry_popup');
		// 07-05-2022: updation task start
		$data['entry_album'] = $this->language->get('entry_album');
		$data['entry_album_video'] = $this->language->get('entry_album_video');
		$data['entry_album_photo'] = $this->language->get('entry_album_photo');
		// 07-05-2022: updation task end
		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_height'] = $this->language->get('entry_height');
		$data['entry_albumphoto_description'] = $this->language->get('entry_albumphoto_description');
		$data['entry_album_description'] = $this->language->get('entry_album_description');
		$data['entry_albumphoto_limit'] = $this->language->get('entry_albumphoto_limit');
		$data['entry_social_status'] = $this->language->get('entry_social_status');
		$data['entry_cursive_font'] = $this->language->get('entry_cursive_font');
		$data['entry_albumvideo_description'] = $this->language->get('entry_albumvideo_description');
		$data['entry_albumvideo_limit'] = $this->language->get('entry_albumvideo_limit');
		$data['entry_video_image'] = $this->language->get('entry_video_image');
		// gallery for product task starts
		$data['entry_product_title'] = $this->language->get('entry_product_title');
		$data['entry_galleryproduct_size'] = $this->language->get('entry_galleryproduct_size');
		$data['entry_galleryproduct_photo_size'] = $this->language->get('entry_galleryproduct_photo_size');
		$data['entry_galleryproduct_viewas'] = $this->language->get('entry_galleryproduct_viewas');
		// 07-05-2022: updation task start
		$data['entry_override_video'] = $this->language->get('entry_override_video');
		$data['entry_override_image'] = $this->language->get('entry_override_image');
		// 07-05-2022: updation task end
		$data['entry_carousel'] = $this->language->get('entry_carousel');
		$data['entry_extitle'] = $this->language->get('entry_extitle');
		// gallery for product task ends
		//Colors

		$data['entry_color_title_text'] = $this->language->get('entry_color_title_text');
		$data['entry_color_albumtitle_text'] = $this->language->get('entry_color_albumtitle_text');
		$data['entry_albumtitle_bg'] = $this->language->get('entry_albumtitle_bg');
		$data['entry_photo_tilte_color'] = $this->language->get('entry_photo_tilte_color');
		$data['entry_photo_zoomicon_color'] = $this->language->get('entry_photo_zoomicon_color');
		$data['entry_photo_hoverbg_color'] = $this->language->get('entry_photo_hoverbg_color');
		$data['entry_albumsapge_title_text'] = $this->language->get('entry_albumsapge_title_text');
		$data['entry_albums_detail_text'] = $this->language->get('entry_albums_detail_text');
		$data['entry_sharethis_bg'] = $this->language->get('entry_sharethis_bg');
		$data['entry_sharethis_color'] = $this->language->get('entry_sharethis_color');
		$data['entry_albums_wrapbg'] = $this->language->get('entry_albums_wrapbg');
		$data['entry_extitle_bgcolor'] = $this->language->get('entry_extitle_bgcolor');
		$data['entry_extitle_textcolor'] = $this->language->get('entry_extitle_textcolor');
		$data['entry_extitle_bordercolor'] = $this->language->get('entry_extitle_bordercolor');
		$data['entry_exview_all_color'] = $this->language->get('entry_exview_all_color');
		$data['entry_carousel_arrow_bgcolor'] = $this->language->get('entry_carousel_arrow_bgcolor');
		$data['entry_carousel_arrow_color'] = $this->language->get('entry_carousel_arrow_color');
		$data['entry_show_videos'] = $this->language->get('entry_show_videos');


		// 07-05-2022: updation task start
		$data['help_album'] = $this->language->get('help_album');
		$data['help_album_video'] = $this->language->get('help_album_video');
		$data['help_album_photo'] = $this->language->get('help_album_photo');
		// 07-05-2022: updation task end
		$data['help_popup'] = $this->language->get('help_popup');
		$data['help_video_image'] = $this->language->get('help_video_image');
		// gallery for product task starts
		$data['help_galleryproduct_size'] = $this->language->get('help_galleryproduct_size');
		$data['help_galleryproduct_photo_size'] = $this->language->get('help_galleryproduct_photo_size');
		// gallery for product task ends

		$data['fieldset_general'] = $this->language->get('fieldset_general');
		$data['fieldset_album_page'] = $this->language->get('fieldset_album_page');
		$data['fieldset_photo_page'] = $this->language->get('fieldset_photo_page');
		$data['fieldset_albumn_photo'] = $this->language->get('fieldset_albumn_photo');
		$data['fieldset_albumn_allphoto'] = $this->language->get('fieldset_albumn_allphoto');
		$data['fieldset_albumns'] = $this->language->get('fieldset_albumns');
		$data['fieldset_sharethis'] = $this->language->get('fieldset_sharethis');
		$data['fieldset_extension'] = $this->language->get('fieldset_extension');
		$data['fieldset_albumn_video'] = $this->language->get('fieldset_albumn_video');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

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

		if (isset($this->error['album_limit'])) {
			$data['error_album_limit'] = $this->error['album_limit'];
		} else {
			$data['error_album_limit'] = '';
		}

		if (isset($this->error['albumphoto_limit'])) {
			$data['error_albumphoto_limit'] = $this->error['albumphoto_limit'];
		} else {
			$data['error_albumphoto_limit'] = '';
		}

		if (isset($this->error['popup_size'])) {
			$data['error_popup_size'] = $this->error['popup_size'];
		} else {
			$data['error_popup_size'] = '';
		}

		if (isset($this->error['video_size'])) {
			$data['error_video_size'] = $this->error['video_size'];
		} else {
			$data['error_video_size'] = '';
		}

		if (isset($this->error['album_size'])) {
			$data['error_album_size'] = $this->error['album_size'];
		} else {
			$data['error_album_size'] = '';
		}
		// 07-05-2022: updation task start
		if (isset($this->error['albumphoto_size'])) {
			$data['error_albumphoto_size'] = $this->error['albumphoto_size'];
		} else {
			$data['error_albumphoto_size'] = '';
		}
		if (isset($this->error['albumvideo_size'])) {
			$data['error_albumvideo_size'] = $this->error['albumvideo_size'];
		} else {
			$data['error_albumvideo_size'] = '';
		}
		// 07-05-2022: updation task end
		if (isset($this->error['albumvideo_limit'])) {
			$data['error_albumvideo_limit'] = $this->error['albumvideo_limit'];
		} else {
			$data['error_albumvideo_limit'] = '';
		}
		// gallery for product task starts
		if (isset($this->error['galleryproduct_description'])) {
			$data['error_galleryproduct_description'] = $this->error['galleryproduct_description'];
		} else {
			$data['error_galleryproduct_description'] = [];
		}
		if (isset($this->error['galleryproduct_size'])) {
			$data['error_galleryproduct_size'] = $this->error['galleryproduct_size'];
		} else {
			$data['error_galleryproduct_size'] = '';
		}
		if (isset($this->error['galleryproduct_photo_size'])) {
			$data['error_galleryproduct_photo_size'] = $this->error['galleryproduct_photo_size'];
		} else {
			$data['error_galleryproduct_photo_size'] = '';
		}
		// gallery for product task ends

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $this->token.'=' . $this->session->data[$this->token], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/mpphotogallery_setting', $this->token.'=' . $this->session->data[$this->token], true)
		);

		$data['get_token'] = $this->token;
		$data['token'] = $this->session->data[$this->token];

		$data['action'] = $this->url->link('extension/module/mpphotogallery_setting', $this->token.'=' . $this->session->data[$this->token], true);

		$data['cancel'] = $this->url->link('extension/extension', $this->token.'=' . $this->session->data[$this->token] . '&type=module', true);
		// gallery for product task starts
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		// gallery for product task ends
		$module_info = $this->model_setting_setting->getSetting('gallery_setting');

		if (isset($this->request->post['gallery_setting_status'])) {
			$data['gallery_setting_status'] = $this->request->post['gallery_setting_status'];
		} elseif (isset($module_info['gallery_setting_status'])) {
			$data['gallery_setting_status'] = $module_info['gallery_setting_status'];
		} else {
			$data['gallery_setting_status'] = '';
		}

		if (isset($this->request->post['gallery_setting_album_limit'])) {
			$data['gallery_setting_album_limit'] = $this->request->post['gallery_setting_album_limit'];
		} elseif (isset($module_info['gallery_setting_album_limit'])) {
			$data['gallery_setting_album_limit'] = $module_info['gallery_setting_album_limit'];
		} else {
			$data['gallery_setting_album_limit'] = 20;
		}

		if (isset($this->request->post['gallery_setting_popup_width'])) {
			$data['gallery_setting_popup_width'] = $this->request->post['gallery_setting_popup_width'];
		} elseif (isset($module_info['gallery_setting_popup_width'])) {
			$data['gallery_setting_popup_width'] = $module_info['gallery_setting_popup_width'];
		} else {
			$data['gallery_setting_popup_width'] = 500;
		}

		if (isset($this->request->post['gallery_setting_popup_height'])) {
			$data['gallery_setting_popup_height'] = $this->request->post['gallery_setting_popup_height'];
		} elseif (isset($module_info['gallery_setting_popup_height'])) {
			$data['gallery_setting_popup_height'] = $module_info['gallery_setting_popup_height'];
		} else {
			$data['gallery_setting_popup_height'] = 729;
		}

		if (isset($this->request->post['gallery_setting_color'])) {
			$data['gallery_setting_color'] = $this->request->post['gallery_setting_color'];
		} elseif (isset($module_info['gallery_setting_color'])) {
			$data['gallery_setting_color'] = $module_info['gallery_setting_color'];
		} else {
			$data['gallery_setting_color'] = [];
		}

		if (isset($this->request->post['gallery_setting_social_status'])) {
			$data['gallery_setting_social_status'] = $this->request->post['gallery_setting_social_status'];
		} elseif (isset($module_info['gallery_setting_social_status'])) {
			$data['gallery_setting_social_status'] = $module_info['gallery_setting_social_status'];
		} else {
			$data['gallery_setting_social_status'] = 1;
		}

		if (isset($this->request->post['gallery_setting_album_width'])) {
			$data['gallery_setting_album_width'] = $this->request->post['gallery_setting_album_width'];
		} elseif (isset($module_info['gallery_setting_album_width'])) {
			$data['gallery_setting_album_width'] = $module_info['gallery_setting_album_width'];
		} else {
			$data['gallery_setting_album_width'] = 213;
		}

		if (isset($this->request->post['gallery_setting_album_height'])) {
			$data['gallery_setting_album_height'] = $this->request->post['gallery_setting_album_height'];
		} elseif (isset($module_info['gallery_setting_album_height'])) {
			$data['gallery_setting_album_height'] = $module_info['gallery_setting_album_height'];
		} else {
			$data['gallery_setting_album_height'] = 310;
		}
		// 07-05-2022: updation task start
		if (isset($this->request->post['gallery_setting_albumphoto_width'])) {
			$data['gallery_setting_albumphoto_width'] = $this->request->post['gallery_setting_albumphoto_width'];
		} elseif (isset($module_info['gallery_setting_albumphoto_width'])) {
			$data['gallery_setting_albumphoto_width'] = $module_info['gallery_setting_albumphoto_width'];
		} else {
			$data['gallery_setting_albumphoto_width'] = 213;
		}

		if (isset($this->request->post['gallery_setting_albumphoto_height'])) {
			$data['gallery_setting_albumphoto_height'] = $this->request->post['gallery_setting_albumphoto_height'];
		} elseif (isset($module_info['gallery_setting_albumphoto_height'])) {
			$data['gallery_setting_albumphoto_height'] = $module_info['gallery_setting_albumphoto_height'];
		} else {
			$data['gallery_setting_albumphoto_height'] = 310;
		}
		// 07-05-2022: updation task end
		if (isset($this->request->post['gallery_setting_show_videos'])) {
			$data['gallery_setting_show_videos'] = $this->request->post['gallery_setting_show_videos'];
		} elseif (isset($module_info['gallery_setting_show_videos'])) {
			$data['gallery_setting_show_videos'] = $module_info['gallery_setting_show_videos'];
		} else {
			$data['gallery_setting_show_videos'] = true;
		}

		if (isset($this->request->post['gallery_setting_albumphoto_description'])) {
			$data['gallery_setting_albumphoto_description'] = $this->request->post['gallery_setting_albumphoto_description'];
		} elseif (isset($module_info['gallery_setting_albumphoto_description'])) {
			$data['gallery_setting_albumphoto_description'] = $module_info['gallery_setting_albumphoto_description'];
		} else {
			$data['gallery_setting_albumphoto_description'] = 1;
		}

		if (isset($this->request->post['gallery_setting_album_description'])) {
			$data['gallery_setting_album_description'] = $this->request->post['gallery_setting_album_description'];
		} elseif (isset($module_info['gallery_setting_album_description'])) {
			$data['gallery_setting_album_description'] = $module_info['gallery_setting_album_description'];
		} else {
			$data['gallery_setting_album_description'] = 1;
		}

		if (isset($this->request->post['gallery_setting_albumphoto_limit'])) {
			$data['gallery_setting_albumphoto_limit'] = $this->request->post['gallery_setting_albumphoto_limit'];
		} elseif (isset($module_info['gallery_setting_albumphoto_limit'])) {
			$data['gallery_setting_albumphoto_limit'] = $module_info['gallery_setting_albumphoto_limit'];
		} else {
			$data['gallery_setting_albumphoto_limit'] = 20;
		}

		if (isset($this->request->post['gallery_setting_photo_cursive_font'])) {
			$data['gallery_setting_photo_cursive_font'] = $this->request->post['gallery_setting_photo_cursive_font'];
		} elseif (isset($module_info['gallery_setting_photo_cursive_font'])) {
			$data['gallery_setting_photo_cursive_font'] = $module_info['gallery_setting_photo_cursive_font'];
		} else {
			$data['gallery_setting_photo_cursive_font'] = '';
		}

		if (isset($this->request->post['gallery_setting_albumphoto_cursive_font'])) {
			$data['gallery_setting_albumphoto_cursive_font'] = $this->request->post['gallery_setting_albumphoto_cursive_font'];
		} elseif (isset($module_info['gallery_setting_albumphoto_cursive_font'])) {
			$data['gallery_setting_albumphoto_cursive_font'] = $module_info['gallery_setting_albumphoto_cursive_font'];
		} else {
			$data['gallery_setting_albumphoto_cursive_font'] = '';
		}

		if (isset($this->request->post['gallery_setting_albumvideo_cursive_font'])) {
			$data['gallery_setting_albumvideo_cursive_font'] = $this->request->post['gallery_setting_albumvideo_cursive_font'];
		} elseif (isset($module_info['gallery_setting_albumvideo_cursive_font'])) {
			$data['gallery_setting_albumvideo_cursive_font'] = $module_info['gallery_setting_albumvideo_cursive_font'];
		} else {
			$data['gallery_setting_albumvideo_cursive_font'] = '';
		}

		if (isset($this->request->post['gallery_setting_albumvideo_description'])) {
			$data['gallery_setting_albumvideo_description'] = $this->request->post['gallery_setting_albumvideo_description'];
		} elseif (isset($module_info['gallery_setting_albumvideo_description'])) {
			$data['gallery_setting_albumvideo_description'] = $module_info['gallery_setting_albumvideo_description'];
		} else {
			$data['gallery_setting_albumvideo_description'] = 1;
		}

		if (isset($this->request->post['gallery_setting_albumvideo_limit'])) {
			$data['gallery_setting_albumvideo_limit'] = $this->request->post['gallery_setting_albumvideo_limit'];
		} elseif (isset($module_info['gallery_setting_albumvideo_limit'])) {
			$data['gallery_setting_albumvideo_limit'] = $module_info['gallery_setting_albumvideo_limit'];
		} else {
			$data['gallery_setting_albumvideo_limit'] = 20;
		}
		// 07-05-2022: updation task start
		if (isset($this->request->post['gallery_setting_albumvideo_width'])) {
			$data['gallery_setting_albumvideo_width'] = $this->request->post['gallery_setting_albumvideo_width'];
		} elseif (isset($module_info['gallery_setting_albumvideo_width'])) {
			$data['gallery_setting_albumvideo_width'] = $module_info['gallery_setting_albumvideo_width'];
		} else {
			$data['gallery_setting_albumvideo_width'] = 213;
		}

		if (isset($this->request->post['gallery_setting_albumvideo_height'])) {
			$data['gallery_setting_albumvideo_height'] = $this->request->post['gallery_setting_albumvideo_height'];
		} elseif (isset($module_info['gallery_setting_albumvideo_height'])) {
			$data['gallery_setting_albumvideo_height'] = $module_info['gallery_setting_albumvideo_height'];
		} else {
			$data['gallery_setting_albumvideo_height'] = 310;
		}
		// 07-05-2022: updation task end
		// gallery for product task starts
		if (isset($this->request->post['gallery_setting_galleryproduct_status'])) {
			$data['gallery_setting_galleryproduct_status'] = $this->request->post['gallery_setting_galleryproduct_status'];
		} elseif (isset($module_info['gallery_setting_galleryproduct_status'])) {
			$data['gallery_setting_galleryproduct_status'] = $module_info['gallery_setting_galleryproduct_status'];
		} else {
			$data['gallery_setting_galleryproduct_status'] = 0;
		}
		if (isset($this->request->post['gallery_setting_galleryproduct_description'])) {
			$data['gallery_setting_galleryproduct_description'] = $this->request->post['gallery_setting_galleryproduct_description'];
		} elseif (isset($module_info['gallery_setting_galleryproduct_description'])) {
			$data['gallery_setting_galleryproduct_description'] = (array)$module_info['gallery_setting_galleryproduct_description'];
		} else {
			$data['gallery_setting_galleryproduct_description'] = [];
		}
		if (isset($this->request->post['gallery_setting_galleryproduct_viewas'])) {
			$data['gallery_setting_galleryproduct_viewas'] = $this->request->post['gallery_setting_galleryproduct_viewas'];
		} elseif (isset($module_info['gallery_setting_galleryproduct_viewas'])) {
			$data['gallery_setting_galleryproduct_viewas'] = $module_info['gallery_setting_galleryproduct_viewas'];
		} else {
			$data['gallery_setting_galleryproduct_viewas'] = 'GAP';
		}
		// 07-05-2022: updation task start
		if (isset($this->request->post['gallery_setting_galleryproduct_override_video'])) {
			$data['gallery_setting_galleryproduct_override_video'] = $this->request->post['gallery_setting_galleryproduct_override_video'];
		} elseif (isset($module_info['gallery_setting_galleryproduct_override_video'])) {
			$data['gallery_setting_galleryproduct_override_video'] = $module_info['gallery_setting_galleryproduct_override_video'];
		} else {
			$data['gallery_setting_galleryproduct_override_video'] = '';
		}
		if (isset($this->request->post['gallery_setting_galleryproduct_override_image'])) {
			$data['gallery_setting_galleryproduct_override_image'] = $this->request->post['gallery_setting_galleryproduct_override_image'];
		} elseif (isset($module_info['gallery_setting_galleryproduct_override_image'])) {
			$data['gallery_setting_galleryproduct_override_image'] = $module_info['gallery_setting_galleryproduct_override_image'];
		} else {
			$data['gallery_setting_galleryproduct_override_image'] = '';
		}
		// 07-05-2022: updation task end
		if (isset($this->request->post['gallery_setting_galleryproduct_width'])) {
			$data['gallery_setting_galleryproduct_width'] = $this->request->post['gallery_setting_galleryproduct_width'];
		} elseif (isset($module_info['gallery_setting_galleryproduct_width'])) {
			$data['gallery_setting_galleryproduct_width'] = $module_info['gallery_setting_galleryproduct_width'];
		} else {
			$data['gallery_setting_galleryproduct_width'] = '213';
		}
		if (isset($this->request->post['gallery_setting_galleryproduct_height'])) {
			$data['gallery_setting_galleryproduct_height'] = $this->request->post['gallery_setting_galleryproduct_height'];
		} elseif (isset($module_info['gallery_setting_galleryproduct_height'])) {
			$data['gallery_setting_galleryproduct_height'] = $module_info['gallery_setting_galleryproduct_height'];
		} else {
			$data['gallery_setting_galleryproduct_height'] = '310';
		}
		if (isset($this->request->post['gallery_setting_galleryproduct_photo_width'])) {
			$data['gallery_setting_galleryproduct_photo_width'] = $this->request->post['gallery_setting_galleryproduct_photo_width'];
		} elseif (isset($module_info['gallery_setting_galleryproduct_photo_width'])) {
			$data['gallery_setting_galleryproduct_photo_width'] = $module_info['gallery_setting_galleryproduct_photo_width'];
		} else {
			$data['gallery_setting_galleryproduct_photo_width'] = '200';
		}
		if (isset($this->request->post['gallery_setting_galleryproduct_photo_height'])) {
			$data['gallery_setting_galleryproduct_photo_height'] = $this->request->post['gallery_setting_galleryproduct_photo_height'];
		} elseif (isset($module_info['gallery_setting_galleryproduct_photo_height'])) {
			$data['gallery_setting_galleryproduct_photo_height'] = $module_info['gallery_setting_galleryproduct_photo_height'];
		} else {
			$data['gallery_setting_galleryproduct_photo_height'] = '200';
		}
		if (isset($this->request->post['gallery_setting_galleryproduct_carousel'])) {
			$data['gallery_setting_galleryproduct_carousel'] = $this->request->post['gallery_setting_galleryproduct_carousel'];
		} elseif (isset($module_info['gallery_setting_galleryproduct_carousel'])) {
			$data['gallery_setting_galleryproduct_carousel'] = $module_info['gallery_setting_galleryproduct_carousel'];
		} else {
			$data['gallery_setting_galleryproduct_carousel'] = '';
		}
		if (isset($this->request->post['gallery_setting_galleryproduct_extitle'])) {
			$data['gallery_setting_galleryproduct_extitle'] = $this->request->post['gallery_setting_galleryproduct_extitle'];
		} elseif (isset($module_info['gallery_setting_galleryproduct_extitle'])) {
			$data['gallery_setting_galleryproduct_extitle'] = $module_info['gallery_setting_galleryproduct_extitle'];
		} else {
			$data['gallery_setting_galleryproduct_extitle'] = '';
		}
		// gallery for product task ends
		$data['mtabs'] = $this->load->controller('extension/mpphotogallery/mtabs');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->viewLoad('extension/module/mpphotogallery_setting', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/mpphotogallery_setting')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['gallery_setting_album_limit']) {
			$this->error['album_limit'] = $this->language->get('error_album_limit');
		}

		if (!$this->request->post['gallery_setting_albumphoto_limit']) {
			$this->error['albumphoto_limit'] = $this->language->get('error_albumphoto_limit');
		}

		if (!$this->request->post['gallery_setting_popup_width'] || !$this->request->post['gallery_setting_popup_height']) {
			$this->error['popup_size'] = $this->language->get('error_popup_size');
		}

		if (!$this->request->post['gallery_setting_album_width'] || !$this->request->post['gallery_setting_album_height']) {
			$this->error['album_size'] = $this->language->get('error_album_size');
		}
		// 07-05-2022: updation task start
		if (!$this->request->post['gallery_setting_albumphoto_width'] || !$this->request->post['gallery_setting_albumphoto_height']) {
			$this->error['albumphoto_size'] = $this->language->get('error_albumphoto_size');
		}
		if (!$this->request->post['gallery_setting_albumvideo_width'] || !$this->request->post['gallery_setting_albumvideo_height']) {
			$this->error['albumvideo_size'] = $this->language->get('error_albumvideo_size');
		}
		// 07-05-2022: updation task end
		// gallery for product task starts

		// gallery for product task ends

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}
}