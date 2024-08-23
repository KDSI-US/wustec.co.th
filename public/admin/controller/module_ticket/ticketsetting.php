<?php
class ControllerModuleTicketTicketSetting extends Controller {
	private $error = array();

	public function index() {
		if(VERSION <= '2.3.0.2') {
			$session_token_variable = 'token';
			$session_token = $this->session->data['token'];
		} else {
			$session_token_variable = 'user_token';
			$session_token = $this->session->data['user_token'];
		}

		if(isset($this->request->get['store_id'])) {
			$data['store_id'] = $this->request->get['store_id'];
		} else {
			$data['store_id'] = 0;
		}

		$this->document->addStyle('view/stylesheet/modulepoints/ticketsystem.css');

		$this->load->language('module_ticket/ticketsetting');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
		
		$this->load->model('tool/image');

		$this->load->model('module_ticket/createtable');
		$this->model_module_ticket_createtable->Createtable();

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('ticketsetting', $this->request->post, $data['store_id']);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('module_ticket/ticketsetting', $session_token_variable .'=' . $session_token.'&store_id='. $data['store_id'], true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		// Stores
		$this->load->model('setting/store');

		$data['stores'] = array();

		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);

		$results = $this->model_setting_store->getStores();

		foreach ($results as $result) {
			$data['stores'][] = array(
				'store_id' => $result['store_id'],
				'name'     => $result['name']
			);
		}

		$store_info = $this->model_setting_store->getStore($data['store_id']);
		if($store_info) {
			$data['store_name'] = $store_info['name'];
		}else{
			$data['store_name'] = $this->language->get('text_default');
		}

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_store'] = $this->language->get('text_store');
		$data['text_module_headerfooter'] = $this->language->get('text_module_headerfooter');
		$data['text_theme_headerfooter'] = $this->language->get('text_theme_headerfooter');
		$data['text_show'] = $this->language->get('text_show');
		$data['text_hide'] = $this->language->get('text_hide');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_show'] = $this->language->get('text_show');
		$data['text_hide'] = $this->language->get('text_hide');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_subject'] = $this->language->get('entry_subject');
		$data['entry_message'] = $this->language->get('entry_message');
		$data['entry_list_limit'] = $this->language->get('entry_list_limit');
		$data['entry_adminreply_close_status'] = $this->language->get('entry_adminreply_close_status');
		$data['entry_reply_list_limit'] = $this->language->get('entry_reply_list_limit');
		$data['entry_headerfooter'] = $this->language->get('entry_headerfooter');
		$data['entry_footer'] = $this->language->get('entry_footer');
		$data['entry_logo'] = $this->language->get('entry_logo');
		$data['entry_banner'] = $this->language->get('entry_banner');
		$data['entry_ticketsubmission'] = $this->language->get('entry_ticketsubmission');
		$data['entry_videos'] = $this->language->get('entry_videos');
		$data['entry_knowledgebase'] = $this->language->get('entry_knowledgebase');
		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_banner_title'] = $this->language->get('entry_banner_title');
		$data['entry_meta_title'] = $this->language->get('entry_meta_title');
		$data['entry_meta_description'] = $this->language->get('entry_meta_description');
		$data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
		$data['entry_ticket_submission_link'] = $this->language->get('entry_ticket_submission_link');
		$data['entry_login_link'] = $this->language->get('entry_login_link');
		$data['entry_captcha'] = $this->language->get('entry_captcha');
		$data['entry_ticketstatus'] = $this->language->get('entry_ticketstatus');
		$data['entry_success_logged_description'] = $this->language->get('entry_success_logged_description');
		$data['entry_success_notlogged_description'] = $this->language->get('entry_success_notlogged_description');
		$data['entry_closed_ticketstatus'] = $this->language->get('entry_closed_ticketstatus');
		$data['entry_logo_link'] = $this->language->get('entry_logo_link');
		$data['entry_userphoto_display'] = $this->language->get('entry_userphoto_display');
		$data['entry_staffphoto_display'] = $this->language->get('entry_staffphoto_display');
		$data['entry_adminemail'] = $this->language->get('entry_adminemail');

		$data['help_ticketstatus'] = $this->language->get('help_ticketstatus');
		$data['help_closed_ticketstatus'] = $this->language->get('help_closed_ticketstatus');
		
		$data['legend_header'] = $this->language->get('legend_header');
		$data['legend_footer'] = $this->language->get('legend_footer');
		$data['legend_widgets'] = $this->language->get('legend_widgets');
		$data['legend_banner'] = $this->language->get('legend_banner');
		$data['legend_ticketsubmit'] = $this->language->get('legend_ticketsubmit');
		$data['legend_ticketlistpage'] = $this->language->get('legend_ticketlistpage');
		$data['legend_ticketreply'] = $this->language->get('legend_ticketreply');
		$data['legend_admin_panel'] = $this->language->get('legend_admin_panel');
		
		$data['navtab_landing_page'] = $this->language->get('navtab_landing_page');
		$data['navtab_ticketsubmit_page'] = $this->language->get('navtab_ticketsubmit_page');
		$data['navtab_ticketlist_page'] = $this->language->get('navtab_ticketlist_page');
		$data['navtab_ticketview_page'] = $this->language->get('navtab_ticketview_page');
		$data['navtab_ticketsuccess_page'] = $this->language->get('navtab_ticketsuccess_page');

		$data['emailtab_createtickettouser'] = $this->language->get('emailtab_createtickettouser');
		$data['emailtab_createtickettoadmin'] = $this->language->get('emailtab_createtickettoadmin');
		$data['emailtab_adminreplytouser'] = $this->language->get('emailtab_adminreplytouser');
		$data['emailtab_userreplytoadmin'] = $this->language->get('emailtab_userreplytoadmin');		
		$data['emailtab_createusertouser'] = $this->language->get('emailtab_createusertouser');
		$data['emailtab_createusertoadmin'] = $this->language->get('emailtab_createusertoadmin');
		$data['emailtab_forgotpasswordtouser'] = $this->language->get('emailtab_forgotpasswordtouser');
		
		$data['tab_email_user'] = $this->language->get('tab_email_user');
		$data['tab_email_admin'] = $this->language->get('tab_email_admin');


		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_savechanges'] = $this->language->get('button_savechanges');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_headerfooter'] = $this->language->get('tab_headerfooter');
		$data['tab_emailnotification'] = $this->language->get('tab_emailnotification');
		$data['tab_language_setting'] = $this->language->get('tab_language_setting');
		$data['tab_landing'] = $this->language->get('tab_landing');
		$data['tab_page'] = $this->language->get('tab_page');
		$data['tab_support'] = $this->language->get('tab_support');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['ticketstatus_id'])) {
			$data['error_ticketstatus_id'] = $this->error['ticketstatus_id'];
		} else {
			$data['error_ticketstatus_id'] = '';
		}

		if (isset($this->error['ticketstatus_closed_id'])) {
			$data['error_ticketstatus_closed_id'] = $this->error['ticketstatus_closed_id'];
		} else {
			$data['error_ticketstatus_closed_id'] = '';
		}

		if (isset($this->error['list_limit'])) {
			$data['error_list_limit'] = $this->error['list_limit'];
		} else {
			$data['error_list_limit'] = '';
		}

		if (isset($this->error['reply_list_limit'])) {
			$data['error_reply_list_limit'] = $this->error['reply_list_limit'];
		} else {
			$data['error_reply_list_limit'] = '';
		}

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		} else {
			$data['error_title'] = '';
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = '';
		}

		if (isset($this->error['submit_banner_title'])) {
			$data['error_submit_banner_title'] = $this->error['submit_banner_title'];
		} else {
			$data['error_submit_banner_title'] = '';
		}

		if (isset($this->error['submit_title'])) {
			$data['error_submit_title'] = $this->error['submit_title'];
		} else {
			$data['error_submit_title'] = '';
		}

		if (isset($this->error['submit_meta_title'])) {
			$data['error_submit_meta_title'] = $this->error['submit_meta_title'];
		} else {
			$data['error_submit_meta_title'] = '';
		}

		if (isset($this->error['success_banner_title'])) {
			$data['error_success_banner_title'] = $this->error['success_banner_title'];
		} else {
			$data['error_success_banner_title'] = '';
		}

		if (isset($this->error['success_title'])) {
			$data['error_success_title'] = $this->error['success_title'];
		} else {
			$data['error_success_title'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $session_token_variable .'=' . $session_token, true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('module_ticket/ticketsetting', $session_token_variable .'=' . $session_token, true)
		);

		if(isset($data['store_id'])) {
			$data['action'] = $this->url->link('module_ticket/ticketsetting', $session_token_variable .'=' . $session_token . '&store_id='. $data['store_id'], true);
		} else{
			$data['action'] = $this->url->link('module_ticket/ticketsetting', $session_token_variable .'=' . $session_token, true);
		}

		$data['cancel'] = $this->url->link('common/dashboard', $session_token_variable .'=' . $session_token, true);

		$module_info = $this->model_setting_setting->getSetting('ticketsetting', $data['store_id']);

		if (isset($this->request->post['ticketsetting_status'])) {
			$data['ticketsetting_status'] = $this->request->post['ticketsetting_status'];
		} else if(isset($module_info['ticketsetting_status'])) {
			$data['ticketsetting_status'] = $module_info['ticketsetting_status'];
		} else {
			$data['ticketsetting_status'] = '';
		}

		$data['ticketsetting_headerfooter'] = '1';

		if (isset($this->request->post['ticketsetting_widgets'])) {
			$data['ticketsetting_widgets'] = $this->request->post['ticketsetting_widgets'];
		} else if(isset($module_info['ticketsetting_widgets'])) {
			$data['ticketsetting_widgets'] = (array)$module_info['ticketsetting_widgets'];
		} else {
			$data['ticketsetting_widgets'] = array(
				'ticketsubmission'	=> 1,
				'videos'			=> 1,
				'knowledgebase'		=> 1,
			);
		}

		if (isset($this->request->post['ticketsetting_captcha'])) {
			$data['ticketsetting_captcha'] = $this->request->post['ticketsetting_captcha'];
		} else if(isset($module_info['ticketsetting_captcha'])) {
			$data['ticketsetting_captcha'] = $module_info['ticketsetting_captcha'];
		} else {
			$data['ticketsetting_captcha'] = '';
		}

		if (isset($this->request->post['ticketsetting_ticketstatus_id'])) {
			$data['ticketsetting_ticketstatus_id'] = $this->request->post['ticketsetting_ticketstatus_id'];
		} else if(isset($module_info['ticketsetting_ticketstatus_id'])) {
			$data['ticketsetting_ticketstatus_id'] = $module_info['ticketsetting_ticketstatus_id'];
		} else {
			$data['ticketsetting_ticketstatus_id'] = '';
		}
		
		if (isset($this->request->post['ticketsetting_ticketstatus_closed_id'])) {
			$data['ticketsetting_ticketstatus_closed_id'] = $this->request->post['ticketsetting_ticketstatus_closed_id'];
		} else if(isset($module_info['ticketsetting_ticketstatus_closed_id'])) {
			$data['ticketsetting_ticketstatus_closed_id'] = $module_info['ticketsetting_ticketstatus_closed_id'];
		} else {
			$data['ticketsetting_ticketstatus_closed_id'] = '';
		}

		if (isset($this->request->post['ticketsetting_language'])) {
			$data['ticketsetting_language'] = $this->request->post['ticketsetting_language'];
		} else if(isset($module_info['ticketsetting_language'])) {
			$data['ticketsetting_language'] = (array)$module_info['ticketsetting_language'];
		} else {
			$data['ticketsetting_language'] = array();
		}

		if (isset($this->request->post['ticketsetting_submit_language'])) {
			$data['ticketsetting_submit_language'] = $this->request->post['ticketsetting_submit_language'];
		} else if(isset($module_info['ticketsetting_submit_language'])) {
			$data['ticketsetting_submit_language'] = (array)$module_info['ticketsetting_submit_language'];
		} else {
			$data['ticketsetting_submit_language'] = array();
		}

		if (isset($this->request->post['ticketsetting_success_language'])) {
			$data['ticketsetting_success_language'] = $this->request->post['ticketsetting_success_language'];
		} else if(isset($module_info['ticketsetting_success_language'])) {
			$data['ticketsetting_success_language'] = (array)$module_info['ticketsetting_success_language'];
		} else {
			$data['ticketsetting_success_language'] = array();
		}

		if (isset($this->request->post['ticketsetting_ticket_submission_link'])) {
			$data['ticketsetting_ticket_submission_link'] = $this->request->post['ticketsetting_ticket_submission_link'];
		} else if(isset($module_info['ticketsetting_ticket_submission_link'])) {
			$data['ticketsetting_ticket_submission_link'] = $module_info['ticketsetting_ticket_submission_link'];
		} else {
			$data['ticketsetting_ticket_submission_link'] = '1';
		}

		if (isset($this->request->post['ticketsetting_login_link'])) {
			$data['ticketsetting_login_link'] = $this->request->post['ticketsetting_login_link'];
		} else if(isset($module_info['ticketsetting_login_link'])) {
			$data['ticketsetting_login_link'] = $module_info['ticketsetting_login_link'];
		} else {
			$data['ticketsetting_login_link'] = '1';
		}

		if (isset($this->request->post['ticketsetting_userphoto_display_header'])) {
			$data['ticketsetting_userphoto_display_header'] = $this->request->post['ticketsetting_userphoto_display_header'];
		} else if(isset($module_info['ticketsetting_userphoto_display_header'])) {
			$data['ticketsetting_userphoto_display_header'] = $module_info['ticketsetting_userphoto_display_header'];
		} else {
			$data['ticketsetting_userphoto_display_header'] = '1';
		}
		
		if (isset($this->request->post['ticketsetting_userphoto_display'])) {
			$data['ticketsetting_userphoto_display'] = $this->request->post['ticketsetting_userphoto_display'];
		} else if(isset($module_info['ticketsetting_userphoto_display'])) {
			$data['ticketsetting_userphoto_display'] = $module_info['ticketsetting_userphoto_display'];
		} else {
			$data['ticketsetting_userphoto_display'] = '1';
		}

		if (isset($this->request->post['ticketsetting_staffphoto_display'])) {
			$data['ticketsetting_staffphoto_display'] = $this->request->post['ticketsetting_staffphoto_display'];
		} else if(isset($module_info['ticketsetting_staffphoto_display'])) {
			$data['ticketsetting_staffphoto_display'] = $module_info['ticketsetting_staffphoto_display'];
		} else {
			$data['ticketsetting_staffphoto_display'] = '1';
		}

		if (isset($this->request->post['ticketsetting_logo_link'])) {
			$data['ticketsetting_logo_link'] = $this->request->post['ticketsetting_logo_link'];
		} else if(isset($module_info['ticketsetting_logo_link'])) {
			$data['ticketsetting_logo_link'] = $module_info['ticketsetting_logo_link'];
		} else {
			$data['ticketsetting_logo_link'] = 'index.php?route=support/support';
		}

		if (isset($this->request->post['ticketsetting_list_limit'])) {
			$data['ticketsetting_list_limit'] = $this->request->post['ticketsetting_list_limit'];
		}else if(isset($module_info['ticketsetting_list_limit'])) {
			$data['ticketsetting_list_limit'] = $module_info['ticketsetting_list_limit'];
		} else {
			$data['ticketsetting_list_limit'] = '20';
		}

		if (isset($this->request->post['ticketsetting_adminreply_close_status'])) {
			$data['ticketsetting_adminreply_close_status'] = $this->request->post['ticketsetting_adminreply_close_status'];
		} else if(isset($module_info['ticketsetting_adminreply_close_status'])) {
			$data['ticketsetting_adminreply_close_status'] = $module_info['ticketsetting_adminreply_close_status'];
		} else {
			$data['ticketsetting_adminreply_close_status'] = '';
		}

		if (isset($this->request->post['ticketsetting_reply_list_limit'])) {
			$data['ticketsetting_reply_list_limit'] = $this->request->post['ticketsetting_reply_list_limit'];
		} else if(isset($module_info['ticketsetting_reply_list_limit'])) {
			$data['ticketsetting_reply_list_limit'] = $module_info['ticketsetting_reply_list_limit'];
		} else {
			$data['ticketsetting_reply_list_limit'] = '20';
		}

		if (isset($this->request->post['ticketsetting_logo'])) {
			$data['ticketsetting_logo'] = $this->request->post['ticketsetting_logo'];
		} else if(isset($module_info['ticketsetting_logo'])) {
			$data['ticketsetting_logo'] = $module_info['ticketsetting_logo'];
		} else {
			$data['ticketsetting_logo'] = '';
		}

		if (isset($data['ticketsetting_logo']) && is_file(DIR_IMAGE . $data['ticketsetting_logo'])) {
			$data['logo'] = $this->model_tool_image->resize($data['ticketsetting_logo'], 100, 100);
		} else {
			$data['logo'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		if (isset($this->request->post['ticketsetting_banner'])) {
			$data['ticketsetting_banner'] = $this->request->post['ticketsetting_banner'];
		} else if(isset($module_info['ticketsetting_banner'])) {
			$data['ticketsetting_banner'] = $module_info['ticketsetting_banner'];
		} else {
			$data['ticketsetting_banner'] = '';
		}

		if (isset($data['ticketsetting_banner']) && is_file(DIR_IMAGE . $data['ticketsetting_banner'])) {
			$data['banner'] = $this->model_tool_image->resize($data['ticketsetting_banner'], 100, 100);
		} else {
			$data['banner'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->post['ticketsetting_adminemail'])) {
			$data['ticketsetting_adminemail'] = $this->request->post['ticketsetting_adminemail'];
		} else if(isset($module_info['ticketsetting_adminemail'])) {
			$data['ticketsetting_adminemail'] = $module_info['ticketsetting_adminemail'];
		} else {
			$data['ticketsetting_adminemail'] = $this->config->get('config_email');
		}

		if (isset($this->request->post['ticketsetting_email'])) {
			$data['ticketsetting_email'] = $this->request->post['ticketsetting_email'];
		} else if(isset($module_info['ticketsetting_email'])) {
			$data['ticketsetting_email'] = (array)$module_info['ticketsetting_email'];
		} else {
			$data['ticketsetting_email'] = array(
				'createtickettouser'		=> array(
					'status'	=> 1,
				),
				'adminreplytouser'			=> array(
					'status'	=> 1,
				),
				'createusertouser'			=> array(
					'status'	=> 1,
				),
				'forgotpasswordtouser'		=> array(
					'status'	=> 1,
				),
				'createtickettoadmin'		=> array(
					'status'	=> 1,
				),
				'userreplytoadmin'			=> array(
					'status'	=> 1,
				),
				'createusertoadmin'			=> array(
					'status'	=> 1,
				),
			);
		}

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$this->load->model('module_ticket/ticketstatus');
		$data['ticketstatuses'] = $this->model_module_ticket_ticketstatus->getTicketstatuses();

		$data['session_token'] = $session_token;

		$data['summernote'] = '';

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		if(VERSION <= '2.3.0.2') {
			$this->response->setOutput($this->load->view('module_ticket/ticketsetting.tpl', $data));	
		} else {
			$this->config->set('template_engine', 'template');
			$this->response->setOutput($this->load->view('module_ticket/ticketsetting', $data));
		}
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module_ticket/ticketsetting')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->post['ticketsetting_widgets']['ticketsubmission']) && empty($this->request->post['ticketsetting_widgets']['videos']) && empty($this->request->post['ticketsetting_widgets']['knowledgebase'])) {
			$this->error['warning'] = $this->language->get('error_widgets');
		}

		if (empty($this->request->post['ticketsetting_ticketstatus_id'])) {
			$this->error['ticketstatus_id'] = $this->language->get('error_ticketstatus_id');
		}

		if (empty($this->request->post['ticketsetting_ticketstatus_closed_id'])) {
			$this->error['ticketstatus_closed_id'] = $this->language->get('error_ticketstatus_closed_id');
		}

		if (!$this->request->post['ticketsetting_list_limit']) {
			$this->error['list_limit'] = $this->language->get('error_list_limit');
		}

		if (!$this->request->post['ticketsetting_reply_list_limit']) {
			$this->error['reply_list_limit'] = $this->language->get('error_reply_list_limit');
		}

		if ((utf8_strlen($this->request->post['ticketsetting_adminemail']) > 96) || !filter_var($this->request->post['ticketsetting_adminemail'], FILTER_VALIDATE_EMAIL)) {
			$this->error['ticketsetting_adminemail'] = $this->language->get('error_adminemail');
		}

		foreach ($this->request->post['ticketsetting_language'] as $language_id => $value) {
			if ((utf8_strlen($value['title']) < 3) || (utf8_strlen($value['title']) > 255)) {
				$this->error['title'][$language_id] = $this->language->get('error_title');
			}

			if ((utf8_strlen($value['meta_title']) < 3) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
		}

		foreach ($this->request->post['ticketsetting_submit_language'] as $language_id => $value) {
			if ((utf8_strlen($value['banner_title']) < 3) || (utf8_strlen($value['banner_title']) > 255)) {
				$this->error['submit_banner_title'][$language_id] = $this->language->get('error_heading_title');
			}

			if ((utf8_strlen($value['title']) < 3) || (utf8_strlen($value['title']) > 255)) {
				$this->error['submit_title'][$language_id] = $this->language->get('error_title');
			}

			if ((utf8_strlen($value['meta_title']) < 3) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['submit_meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
		}

		foreach ($this->request->post['ticketsetting_success_language'] as $language_id => $value) {
			if ((utf8_strlen($value['banner_title']) < 3) || (utf8_strlen($value['banner_title']) > 255)) {
				$this->error['success_banner_title'][$language_id] = $this->language->get('error_heading_title');
			}

			if ((utf8_strlen($value['title']) < 3) || (utf8_strlen($value['title']) > 255)) {
				$this->error['success_title'][$language_id] = $this->language->get('error_title');
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}
}