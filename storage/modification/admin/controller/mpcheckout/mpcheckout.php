<?php
/* This file is under Git Control by KDSI. */
class ControllerMpcheckoutMpcheckout extends Controller {
	private $error = array();
	/*new updates 28032018 starts*/
	public function install() {
		/*--
		-- Table structure for table `" . DB_PREFIX . "mpsociallogin`
		--*/

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mpsociallogin` (
		  `mpsociallogin_id` int(11) NOT NULL AUTO_INCREMENT,
		  `id` varchar(255) NOT NULL,
		  `username` varchar(255) NOT NULL,
		  `type` varchar(50) NOT NULL,
		  `customer_id` int(11) NOT NULL,
		  `date_added` datetime NOT NULL,
		  `date_modified` datetime NOT NULL,
		  PRIMARY KEY (`mpsociallogin_id`)
		) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
		");
	}
	/*new updates 28032018 ends*/
	public function index() {
		/*new updates 28032018 starts*/$this->install();/*new updates 28032018 ends*/
		$this->document->addStyle('view/stylesheet/mpcheckout/mpcheckout.css');
		$this->document->addStyle('view/javascript/mpcheckout/colorpicker/css/bootstrap-colorpicker.css');
		$this->document->addScript('view/javascript/mpcheckout/colorpicker/js/bootstrap-colorpicker.js');

		if(isset($this->request->get['store_id'])) {
			$data['store_id'] = $this->request->get['store_id'];
		}else{
			$data['store_id'] = 0;
		}

		$this->load->language('mpcheckout/mpcheckout');

		$this->document->setTitle(strip_tags($this->language->get('heading_title')));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('mpcheckout', $this->request->post, $data['store_id']);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('mpcheckout/mpcheckout', 'user_token=' . $this->session->data['user_token'].'&store_id='. $data['store_id'], true));	
		}

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_panel'] = $this->language->get('tab_panel');
		$data['tab_fields'] = $this->language->get('tab_fields');
		$data['tab_langsetting'] = $this->language->get('tab_langsetting');
		$data['tab_successpage'] = $this->language->get('tab_successpage');
		$data['tab_mpdesign'] = $this->language->get('tab_mpdesign');
		$data['tab_mpcheckoutdoc'] = $this->language->get('tab_mpcheckoutdoc');
		$data['tab_modulepoints'] = $this->language->get('tab_modulepoints');
		
		$data['tab_google'] = $this->language->get('tab_google');				
		$data['tab_facebook'] = $this->language->get('tab_facebook');		
		$data['tab_linkedin'] = $this->language->get('tab_linkedin');		
		/*new updates 28032018 starts*/$data['tab_instagram'] = $this->language->get('tab_instagram');
		$data['tab_twitter'] = $this->language->get('tab_twitter');/*new updates 28032018 ends*/

		$data['fieldstabs_account_address']	= $this->language->get('fieldstabs_account_address');
		$data['fieldstabs_payment_address'] = $this->language->get('fieldstabs_payment_address');
		$data['fieldstabs_shipping_address'] = $this->language->get('fieldstabs_shipping_address');
		
		$data['navtabs_account_buttons'] = $this->language->get('navtabs_account_buttons');
		$data['navtabs_account_panel'] = $this->language->get('navtabs_account_panel');
		$data['navtabs_social_panel'] = $this->language->get('navtabs_social_panel');
		$data['navtabs_date_panel'] = $this->language->get('navtabs_date_panel');
		$data['navtabs_payment_address'] = $this->language->get('navtabs_payment_address');
		$data['navtabs_shipping_address'] = $this->language->get('navtabs_shipping_address');
		$data['navtabs_payment_methods'] = $this->language->get('navtabs_payment_methods');
		$data['navtabs_shipping_methods'] = $this->language->get('navtabs_shipping_methods');
		$data['navtabs_shoppingcart'] = $this->language->get('navtabs_shoppingcart');
		$data['navtabs_checkout_order'] = $this->language->get('navtabs_checkout_order');

		
		$data['navtabs_lang_page'] = $this->language->get('navtabs_lang_page');
		$data['navtabs_lang_account_buttons'] = $this->language->get('navtabs_lang_account_buttons');
		$data['navtabs_lang_account_panel'] = $this->language->get('navtabs_lang_account_panel');
		$data['navtabs_lang_login_panel'] = $this->language->get('navtabs_lang_login_panel');
		$data['navtabs_lang_payment_address'] = $this->language->get('navtabs_lang_payment_address');
		$data['navtabs_lang_shipping_address'] = $this->language->get('navtabs_lang_shipping_address');
		$data['navtabs_lang_payment_methods'] = $this->language->get('navtabs_lang_payment_methods');
		$data['navtabs_lang_shipping_methods'] = $this->language->get('navtabs_lang_shipping_methods');
		$data['navtabs_lang_shoppingcart'] = $this->language->get('navtabs_lang_shoppingcart');
		$data['navtabs_lang_checkout_order'] = $this->language->get('navtabs_lang_checkout_order');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_width'] = $this->language->get('text_width');
		$data['text_height'] = $this->language->get('text_height');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_store'] = $this->language->get('text_store');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_register'] = $this->language->get('text_register');
		$data['text_guest'] = $this->language->get('text_guest');
		$data['text_login'] = $this->language->get('text_login');
		$data['text_display_yes_required'] = $this->language->get('text_display_yes_required');
		$data['text_display_yes'] = $this->language->get('text_display_yes');
		$data['text_display_no'] = $this->language->get('text_display_no');
		$data['text_minimum'] = $this->language->get('text_minimum');
		$data['text_maximum'] = $this->language->get('text_maximum');
		$data['text_checkout_1'] = $this->language->get('text_checkout_1');
		$data['text_checkout_2'] = $this->language->get('text_checkout_2');

		$data['legend_paymentmethod_image'] = $this->language->get('legend_paymentmethod_image');
		$data['legend_shippingmethod_image'] = $this->language->get('legend_shippingmethod_image');
		$data['legend_account_fields'] = $this->language->get('legend_account_fields');
		$data['legend_payment_fields'] = $this->language->get('legend_payment_fields');
		$data['legend_shipping_fields'] = $this->language->get('legend_shipping_fields');
		$data['legend_disabled_dates'] = $this->language->get('legend_disabled_dates');

		$data['entry_captcha'] = $this->language->get('entry_captcha');
		$data['entry_appid'] = $this->language->get('entry_appid');
		$data['entry_secret'] = $this->language->get('entry_secret');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_overlay'] = $this->language->get('entry_overlay');
		$data['entry_confirm_autotrigger_order'] = $this->language->get('entry_confirm_autotrigger_order');
		$data['entry_redirect'] = $this->language->get('entry_redirect');
		$data['entry_country'] = $this->language->get('entry_country');
		$data['entry_zone'] = $this->language->get('entry_zone');
		$data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$data['entry_register_button'] = $this->language->get('entry_register_button');
		$data['entry_guest_button'] = $this->language->get('entry_guest_button');
		$data['entry_login_button'] = $this->language->get('entry_login_button');
		$data['entry_default_account_button'] = $this->language->get('entry_default_account_button');
		$data['entry_account_buttons'] = $this->language->get('entry_account_buttons');
		$data['entry_cart_status'] = $this->language->get('entry_cart_status');
		$data['entry_show_product_image'] = $this->language->get('entry_show_product_image');
		$data['entry_product_image_size'] = $this->language->get('entry_product_image_size');
		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_height'] = $this->language->get('entry_height');
		$data['entry_account_terms'] = $this->language->get('entry_account_terms');
		$data['entry_chekout_terms'] = $this->language->get('entry_chekout_terms');
		$data['entry_newsletter_subscribe'] = $this->language->get('entry_newsletter_subscribe');
		$data['entry_show_weight'] = $this->language->get('entry_show_weight');
		$data['entry_qty_update'] = $this->language->get('entry_qty_update');
		$data['entry_continue_shopping'] = $this->language->get('entry_continue_shopping');
		$data['entry_stopcartpage'] = $this->language->get('entry_stopcartpage');
		$data['entry_show_comment'] = $this->language->get('entry_show_comment');
		$data['entry_default_account_id'] = $this->language->get('entry_default_account_id');
		$data['entry_default_checkout_id'] = $this->language->get('entry_default_checkout_id');
		$data['entry_default_payment_method'] = $this->language->get('entry_default_payment_method');
		$data['entry_default_shipping_method'] = $this->language->get('entry_default_shipping_method');
		$data['entry_css'] = $this->language->get('entry_css');
		$data['entry_date_status'] = $this->language->get('entry_date_status');
		$data['entry_date_required'] = $this->language->get('entry_date_required');
		$data['entry_minimum_maximum_days'] = $this->language->get('entry_minimum_maximum_days');
		$data['entry_disabled_dates'] = $this->language->get('entry_disabled_dates');
		$data['entry_disables_weeks'] = $this->language->get('entry_disables_weeks');
		
		$data['entry_lang_heading_title'] = $this->language->get('entry_lang_heading_title');
		$data['entry_lang_register_panel'] = $this->language->get('entry_lang_register_panel');
		$data['entry_lang_guest_panel'] = $this->language->get('entry_lang_guest_panel');
		$data['entry_lang_login_panel'] = $this->language->get('entry_lang_login_panel');
		$data['entry_lang_personal_title'] = $this->language->get('entry_lang_personal_title');
		$data['entry_lang_password'] = $this->language->get('entry_lang_password');
		$data['entry_lang_more_details'] = $this->language->get('entry_lang_more_details');
		$data['entry_lang_login_title'] = $this->language->get('entry_lang_login_title');
		$data['entry_lang_login_button'] = $this->language->get('entry_lang_login_button');
		$data['entry_lang_payment_address_title'] = $this->language->get('entry_lang_payment_address_title');
		$data['entry_lang_shipping_address_title'] = $this->language->get('entry_lang_shipping_address_title');
		$data['entry_lang_social_title'] = $this->language->get('entry_lang_social_title');
		$data['entry_lang_address_not_required'] = $this->language->get('entry_lang_address_not_required');
		$data['entry_lang_payment_method_title'] = $this->language->get('entry_lang_payment_method_title');
		$data['entry_lang_shipping_method_title'] = $this->language->get('entry_lang_shipping_method_title');
		$data['entry_lang_date_title'] = $this->language->get('entry_lang_date_title');
		$data['entry_lang_date_field'] = $this->language->get('entry_lang_date_field');
		$data['entry_lang_method_not_required'] = $this->language->get('entry_lang_method_not_required');
		$data['entry_lang_cart_title'] = $this->language->get('entry_lang_cart_title');
		$data['entry_lang_confirm_order_title'] = $this->language->get('entry_lang_confirm_order_title');
		$data['entry_lang_confirm_order_continue_button'] = $this->language->get('entry_lang_confirm_order_continue_button');
		$data['entry_lang_confirm_order_button'] = $this->language->get('entry_lang_confirm_order_button');
		$data['entry_lang_confirm_order_comment'] = $this->language->get('entry_lang_confirm_order_comment');
		$data['entry_lang_message_register'] = $this->language->get('entry_lang_message_register');
		$data['entry_lang_message_logged'] = $this->language->get('entry_lang_message_logged');
		$data['entry_lang_personal_guest_title'] = $this->language->get('entry_lang_personal_guest_title');
		$data['entry_success_title'] = $this->language->get('entry_success_title');
		$data['entry_success_customer_message'] = $this->language->get('entry_success_customer_message');
		$data['entry_success_guest_message'] = $this->language->get('entry_success_guest_message');
		$data['entry_success_continue_button'] = $this->language->get('entry_success_continue_button');
		$data['entry_success_product'] = $this->language->get('entry_success_product');
		$data['entry_succees_image_size'] = $this->language->get('entry_succees_image_size');
		$data['entry_success_promote'] = $this->language->get('entry_success_promote');
		$data['entry_success_promote_title'] = $this->language->get('entry_success_promote_title');
		$data['entry_auto_trigger_payment_method'] = $this->language->get('entry_auto_trigger_payment_method');
		$data['entry_success_status'] = $this->language->get('entry_success_status');
		$data['entry_postcode'] = $this->language->get('entry_postcode');
		$data['entry_newsletter_subscribe_check'] = $this->language->get('entry_newsletter_subscribe_check');
		$data['entry_template'] = $this->language->get('entry_template');
		
		
		$data['column_field_name'] = $this->language->get('column_field_name');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_payment_method'] = $this->language->get('column_payment_method');
		$data['column_shipping_method'] = $this->language->get('column_shipping_method');


		$data['navtabs_color_buttons'] = $this->language->get('navtabs_color_buttons');
		$data['navtabs_color_tables'] = $this->language->get('navtabs_color_tables');
		$data['navtabs_color_panels'] = $this->language->get('navtabs_color_panels');
		$data['navtabs_color_container'] = $this->language->get('navtabs_color_container');
		$data['navtabs_color_account'] = $this->language->get('navtabs_color_account');
		$data['navtabs_color_success'] = $this->language->get('navtabs_color_success');

		$data['entry_background_account_panel'] = $this->language->get('entry_background_account_panel');
		$data['entry_font_account_panel'] = $this->language->get('entry_font_account_panel');
		$data['entry_background_hover_account_panel'] = $this->language->get('entry_background_hover_account_panel');
		$data['entry_font_hover_account_panel'] = $this->language->get('entry_font_hover_account_panel');

		$data['entry_background_container'] = $this->language->get('entry_background_container');
		$data['entry_background_container_heading'] = $this->language->get('entry_background_container_heading');
		$data['entry_font_container_heading'] = $this->language->get('entry_font_container_heading');

		$data['entry_background_panel_icon'] = $this->language->get('entry_background_panel_icon');
		$data['entry_font_panel_icon'] = $this->language->get('entry_font_panel_icon');
		$data['entry_background_panel'] = $this->language->get('entry_background_panel');
		$data['entry_font_panel'] = $this->language->get('entry_font_panel');
		$data['entry_background_panel_heading'] = $this->language->get('entry_background_panel_heading');
		$data['entry_font_panel_body'] = $this->language->get('entry_font_panel_body');
		$data['entry_border_panel_body'] = $this->language->get('entry_border_panel_body');
		$data['entry_border_panel_confirm'] = $this->language->get('entry_border_panel_confirm');
		$data['entry_border_panel_default'] = $this->language->get('entry_border_panel_default');

		$data['entry_background_table'] = $this->language->get('entry_background_table');
		$data['entry_font_table_data'] = $this->language->get('entry_font_table_data');
		$data['entry_border_table_data'] = $this->language->get('entry_border_table_data');
		$data['entry_border_top_table_data'] = $this->language->get('entry_border_top_table_data');
		$data['entry_order_total_color'] = $this->language->get('entry_order_total_color');
		$data['entry_font_order_total_color'] = $this->language->get('entry_font_order_total_color');

		$data['entry_background_button'] = $this->language->get('entry_background_button');
		$data['entry_font_button'] = $this->language->get('entry_font_button');
		$data['entry_border_button'] = $this->language->get('entry_border_button');
		$data['entry_background_hover_button'] = $this->language->get('entry_background_hover_button');
		$data['entry_font_hover_button'] = $this->language->get('entry_font_hover_button');
		$data['entry_border_hover_button'] = $this->language->get('entry_border_hover_button');
		
		$data['entry_background_success_table'] = $this->language->get('entry_background_success_table');
		$data['entry_font_success_table'] = $this->language->get('entry_font_success_table');

		$data['entry_print_status'] = $this->language->get('entry_print_status');
		$data['entry_googleanalytics'] = $this->language->get('entry_googleanalytics');
		$data['entry_success_print_button'] = $this->language->get('entry_success_print_button');

		$data['info_accountbutton'] = $this->language->get('info_accountbutton');
		$data['info_accountbutton_info'] = $this->language->get('info_accountbutton_info');

		$data['info_shoppingcart'] = $this->language->get('info_shoppingcart');
		$data['info_shoppingcart_info'] = $this->language->get('info_shoppingcart_info');

		$data['info_autotrigger'] = $this->language->get('info_autotrigger');
		$data['info_autotrigger_info'] = $this->language->get('info_autotrigger_info');

		$data['info_checkoutpage'] = $this->language->get('info_checkoutpage');
		$data['info_checkoutpage_info'] = $this->language->get('info_checkoutpage_info');

		$data['info_success_page'] = $this->language->get('info_success_page');
		$data['info_success_page_info'] = $this->language->get('info_success_page_info');

		$data['info_success_promote'] = $this->language->get('info_success_promote');
		$data['info_success_promote_info'] = $this->language->get('info_success_promote_info');

		$data['info_color_heading'] = $this->language->get('info_color_heading');
		$data['info_color_description'] = $this->language->get('info_color_description');

		$data['info_css_heading'] = $this->language->get('info_css_heading');
		$data['info_css_description'] = $this->language->get('info_css_description');

		$data['info_googleanalytics'] = $this->language->get('info_googleanalytics');
		
		$data['column_icon'] = $this->language->get('column_icon');
		$data['column_image'] = $this->language->get('column_image');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_qty'] = $this->language->get('column_qty');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_total'] = $this->language->get('column_total');
		$data['column_remove'] = $this->language->get('column_remove');

		$data['help_confirm_autotrigger_order'] = $this->language->get('help_confirm_autotrigger_order');
		$data['help_auto_trigger_payment_method'] = $this->language->get('help_auto_trigger_payment_method');
		$data['help_country'] = $this->language->get('help_country');
		$data['help_zone'] = $this->language->get('help_zone');
		$data['help_customer_group'] = $this->language->get('help_customer_group');
		
		$data['help_guest_button'] = $this->language->get('entry_guest_button');
		$data['help_login_button'] = $this->language->get('entry_login_button');
		$data['help_default_account_button'] = $this->language->get('help_default_account_button');
		$data['help_account_buttons'] = $this->language->get('help_account_buttons');
		$data['help_account_terms'] = $this->language->get('help_account_terms');
		$data['help_chekout_terms'] = $this->language->get('help_chekout_terms');
		$data['help_success_product'] = $this->language->get('help_success_product');
		$data['help_success_promote'] = $this->language->get('help_success_promote');
		$data['help_postcode'] = $this->language->get('help_postcode');

		$data['button_savechanges'] = $this->language->get('button_savechanges');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$front_url = isset($this->request->server['HTTPS']) ? HTTPS_CATALOG : HTTP_CATALOG;
		$data['link_google_uri']   = $front_url .'index.php?route=mpcheckout/sociallogin/googleapi';
		$data['link_facebook_uri'] = $front_url .'index.php?route=mpcheckout/sociallogin/facebookapi';
		$data['link_linkedin_uri'] = $front_url .'index.php?route=mpcheckout/sociallogin/linkedinapi';
		/*new updates 28032018 starts*/$data['link_instagram_uri'] = $front_url .'index.php?route=mpcheckout/sociallogin/instagramapi';
		$data['link_twitter_uri'] = $front_url .'index.php?route=mpcheckout/sociallogin/twitterapi';/*new updates 28032018 ends*/

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['country'])) {
			$data['error_country'] = $this->error['country'];
		} else {
			$data['error_country'] = '';
		}

		if (isset($this->error['zone'])) {
			$data['error_zone'] = $this->error['zone'];
		} else {
			$data['error_zone'] = '';
		}

		if (isset($this->error['account_buttons_status'])) {
			$data['error_account_buttons_status'] = $this->error['account_buttons_status'];
		} else {
			$data['error_account_buttons_status'] = '';
		}

		if (isset($this->error['default_account_button'])) {
			$data['error_default_account_button'] = $this->error['default_account_button'];
		} else {
			$data['error_default_account_button'] = '';
		}

		if (isset($this->error['image_cart'])) {
			$data['error_image_cart'] = $this->error['image_cart'];
		} else {
			$data['error_image_cart'] = '';
		}

		if (isset($this->error['success_image_size'])) {
			$data['error_success_image_size'] = $this->error['success_image_size'];
		} else {
			$data['error_success_image_size'] = '';
		}

		if (isset($this->error['days'])) {
			$data['error_days'] = $this->error['days'];
		} else {
			$data['error_days'] = '';
		}

		if (isset($this->error['google_appid'])) {
			$data['error_google_appid'] = $this->error['google_appid'];
		} else {
			$data['error_google_appid'] = '';
		}

		if (isset($this->error['google_secret'])) {
			$data['error_google_secret'] = $this->error['google_secret'];
		} else {
			$data['error_google_secret'] = '';
		}

		if (isset($this->error['facebook_appid'])) {
			$data['error_facebook_appid'] = $this->error['facebook_appid'];
		} else {
			$data['error_facebook_appid'] = '';
		}

		if (isset($this->error['facebook_secret'])) {
			$data['error_facebook_secret'] = $this->error['facebook_secret'];
		} else {
			$data['error_facebook_secret'] = '';
		}

		if (isset($this->error['linkedin_appid'])) {
			$data['error_linkedin_appid'] = $this->error['linkedin_appid'];
		} else {
			$data['error_linkedin_appid'] = '';
		}

		if (isset($this->error['linkedin_secret'])) {
			$data['error_linkedin_secret'] = $this->error['linkedin_secret'];
		} else {
			$data['error_linkedin_secret'] = '';
		}
		/*new updates 28032018 starts*/
		if (isset($this->error['instagram_appid'])) {
			$data['error_instagram_appid'] = $this->error['instagram_appid'];
		} else {
			$data['error_instagram_appid'] = '';
		}

		if (isset($this->error['instagram_secret'])) {
			$data['error_instagram_secret'] = $this->error['instagram_secret'];
		} else {
			$data['error_instagram_secret'] = '';
		}
		
		if (isset($this->error['twitter_appid'])) {
			$data['error_twitter_appid'] = $this->error['twitter_appid'];
		} else {
			$data['error_twitter_appid'] = '';
		}

		if (isset($this->error['twitter_secret'])) {
			$data['error_twitter_secret'] = $this->error['twitter_secret'];
		} else {
			$data['error_twitter_secret'] = '';
		}
		/*new updates 28032018 ends*/
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('mpcheckout/mpcheckout', 'user_token=' . $this->session->data['user_token'], true)
		);

		if(isset($data['store_id'])) {
			$data['action'] = $this->url->link('mpcheckout/mpcheckout', 'user_token=' . $this->session->data['user_token'].'&store_id='. $data['store_id'], true);
		} else{
			$data['action'] = $this->url->link('mpcheckout/mpcheckout', 'user_token=' . $this->session->data['user_token'], true);
		}

		$data['cancel'] = $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'], true);

		$module_info = $this->model_setting_setting->getSetting('mpcheckout', $data['store_id']);

		if (isset($this->request->post['mpcheckout_status'])) {
			$data['mpcheckout_status'] = $this->request->post['mpcheckout_status'];
		} else if(isset($module_info['mpcheckout_status'])) {
			$data['mpcheckout_status'] = $module_info['mpcheckout_status'];
		} else {
			$data['mpcheckout_status'] = '';
		}

		if (isset($this->request->post['mpcheckout_template'])) {
			$data['mpcheckout_template'] = $this->request->post['mpcheckout_template'];
		} else if(isset($module_info['mpcheckout_template'])) {
			$data['mpcheckout_template'] = $module_info['mpcheckout_template'];
		} else {
			$data['mpcheckout_template'] = 'checkout_1';
		}

		if (isset($this->request->post['mpcheckout_captcha'])) {
			$data['mpcheckout_captcha'] = $this->request->post['mpcheckout_captcha'];
		} else if(isset($module_info['mpcheckout_captcha'])) {
			$data['mpcheckout_captcha'] = $module_info['mpcheckout_captcha'];
		} else {
			$data['mpcheckout_captcha'] = '';
		}
		
		if (isset($this->request->post['mpcheckout_success_status'])) {
			$data['mpcheckout_success_status'] = $this->request->post['mpcheckout_success_status'];
		} else if(isset($module_info['mpcheckout_success_status'])) {
			$data['mpcheckout_success_status'] = $module_info['mpcheckout_success_status'];
		} else {
			$data['mpcheckout_success_status'] = '';
		}

		if (isset($this->request->post['mpcheckout_country_id'])) {
			$data['mpcheckout_country_id'] = $this->request->post['mpcheckout_country_id'];
		} else if(isset($module_info['mpcheckout_country_id'])) {
			$data['mpcheckout_country_id'] = $module_info['mpcheckout_country_id'];
		} else {
			$data['mpcheckout_country_id'] = $this->config->get('config_country_id');
		}

		if (isset($this->request->post['mpcheckout_zone_id'])) {
			$data['mpcheckout_zone_id'] = $this->request->post['mpcheckout_zone_id'];
		} else if(isset($module_info['mpcheckout_zone_id'])) {
			$data['mpcheckout_zone_id'] = $module_info['mpcheckout_zone_id'];
		} else {
			$data['mpcheckout_zone_id'] = $this->config->get('config_zone_id');
		}

		if (isset($this->request->post['mpcheckout_postcode'])) {
			$data['mpcheckout_postcode'] = $this->request->post['mpcheckout_postcode'];
		} else if(isset($module_info['mpcheckout_postcode'])) {
			$data['mpcheckout_postcode'] = $module_info['mpcheckout_postcode'];
		} else {
			$data['mpcheckout_postcode'] = $this->config->get('mpcheckout_postcode');
		}

		if (isset($this->request->post['mpcheckout_stopcartpage'])) {
			$data['mpcheckout_stopcartpage'] = $this->request->post['mpcheckout_stopcartpage'];
		} else if(isset($module_info['mpcheckout_stopcartpage'])) {
			$data['mpcheckout_stopcartpage'] = $module_info['mpcheckout_stopcartpage'];
		} else {
			$data['mpcheckout_stopcartpage'] = '1';
		}

		if (isset($this->request->post['mpcheckout_account_button'])) {
			$data['mpcheckout_account_button'] = $this->request->post['mpcheckout_account_button'];
		} else if(isset($module_info['mpcheckout_account_button'])) {
			$data['mpcheckout_account_button'] = $module_info['mpcheckout_account_button'];
		} else {
			$data['mpcheckout_account_button'] = array();
		}

		if (isset($this->request->post['mpcheckout_confirm_panel'])) {
			$data['mpcheckout_confirm_panel'] = $this->request->post['mpcheckout_confirm_panel'];
		} else if(isset($module_info['mpcheckout_confirm_panel'])) {
			$data['mpcheckout_confirm_panel'] = $module_info['mpcheckout_confirm_panel'];
		} else {
			$data['mpcheckout_confirm_panel'] = array();
		}

		if (isset($this->request->post['mpcheckout_date_panel'])) {
			$data['mpcheckout_date_panel'] = $this->request->post['mpcheckout_date_panel'];
		} else if(isset($module_info['mpcheckout_date_panel'])) {
			$data['mpcheckout_date_panel'] = $module_info['mpcheckout_date_panel'];
		} else {
			$data['mpcheckout_date_panel'] = array();
		}


		if (isset($this->request->post['mpcheckout_account_panel'])) {
			$data['mpcheckout_account_panel'] = $this->request->post['mpcheckout_account_panel'];

			$data['mpcheckout_account_panel']['fields']['email'] = 1;
			$data['mpcheckout_account_panel']['fields']['password'] = 1;
		} else if(isset($module_info['mpcheckout_account_panel'])) {
			$data['mpcheckout_account_panel'] = $module_info['mpcheckout_account_panel'];

			$data['mpcheckout_account_panel']['fields']['email'] = 1;
			$data['mpcheckout_account_panel']['fields']['password'] = 1;
		} else {
			$data['mpcheckout_account_panel'] = array();

			// Default Fields
			$data['mpcheckout_account_panel']['fields'] = array();
			$data['mpcheckout_account_panel']['fields']['firstname'] = 1;
			$data['mpcheckout_account_panel']['fields']['lastname'] = 1;
			$data['mpcheckout_account_panel']['fields']['email'] = 1;
			$data['mpcheckout_account_panel']['fields']['telephone']	= 1;
			$data['mpcheckout_account_panel']['fields']['fax'] = 1;
			$data['mpcheckout_account_panel']['fields']['company'] = 1;
			$data['mpcheckout_account_panel']['fields']['address_1']	= 1;
			$data['mpcheckout_account_panel']['fields']['address_2']	= 1;
			$data['mpcheckout_account_panel']['fields']['city'] = 1;
			$data['mpcheckout_account_panel']['fields']['postcode'] = 1;
			$data['mpcheckout_account_panel']['fields']['country'] = 1;
			$data['mpcheckout_account_panel']['fields']['zone']	= 1;
			$data['mpcheckout_account_panel']['fields']['password'] = 1;
			$data['mpcheckout_account_panel']['fields']['confirm_password'] = 1;
		}

		if (isset($this->request->post['mpcheckout_payment_address_panel'])) {
			$data['mpcheckout_payment_address_panel'] = $this->request->post['mpcheckout_payment_address_panel'];
		} else if(isset($module_info['mpcheckout_payment_address_panel'])) {
			$data['mpcheckout_payment_address_panel'] = $module_info['mpcheckout_payment_address_panel'];
		} else {
			$data['mpcheckout_payment_address_panel'] = array();

			// Default Fields
			$data['mpcheckout_payment_address_panel']['fields'] = array();
			$data['mpcheckout_payment_address_panel']['fields']['firstname'] = 1;
			$data['mpcheckout_payment_address_panel']['fields']['lastname'] = 1;
			$data['mpcheckout_payment_address_panel']['fields']['company'] = 1;
			$data['mpcheckout_payment_address_panel']['fields']['address_1']	= 1;
			$data['mpcheckout_payment_address_panel']['fields']['address_2']	= 1;
			$data['mpcheckout_payment_address_panel']['fields']['city'] = 1;
			$data['mpcheckout_payment_address_panel']['fields']['postcode'] = 1;
			$data['mpcheckout_payment_address_panel']['fields']['country'] = 1;
			$data['mpcheckout_payment_address_panel']['fields']['zone']	= 1;
		}

		if (isset($this->request->post['mpcheckout_shipping_address_panel'])) {
			$data['mpcheckout_shipping_address_panel'] = $this->request->post['mpcheckout_shipping_address_panel'];
		} else if(isset($module_info['mpcheckout_shipping_address_panel'])) {
			$data['mpcheckout_shipping_address_panel'] = $module_info['mpcheckout_shipping_address_panel'];
		} else {
			$data['mpcheckout_shipping_address_panel'] = array();

			// Default Fields
			$data['mpcheckout_shipping_address_panel']['fields'] = array();
			$data['mpcheckout_shipping_address_panel']['fields']['firstname'] = 1;
			$data['mpcheckout_shipping_address_panel']['fields']['lastname'] = 1;
			$data['mpcheckout_shipping_address_panel']['fields']['company']	= 1;
			$data['mpcheckout_shipping_address_panel']['fields']['address_1']	= 1;
			$data['mpcheckout_shipping_address_panel']['fields']['address_2']	= 1;
			$data['mpcheckout_shipping_address_panel']['fields']['city'] = 1;
			$data['mpcheckout_shipping_address_panel']['fields']['postcode'] = 1;
			$data['mpcheckout_shipping_address_panel']['fields']['country'] = 1;
			$data['mpcheckout_shipping_address_panel']['fields']['zone']	= 1;
		}

		if (isset($this->request->post['mpcheckout_social_panel'])) {
			$data['mpcheckout_social_panel'] = $this->request->post['mpcheckout_social_panel'];
		} else if(isset($module_info['mpcheckout_social_panel'])) {
			$data['mpcheckout_social_panel'] = $module_info['mpcheckout_social_panel'];
		} else {
			$data['mpcheckout_social_panel'] = array();
		}

		if (isset($this->request->post['mpcheckout_social_description'])) {
			$data['mpcheckout_social_description'] = $this->request->post['mpcheckout_social_description'];
		} else if(isset($module_info['mpcheckout_social_description'])) {
			$data['mpcheckout_social_description'] = $module_info['mpcheckout_social_description'];
		} else {
			$data['mpcheckout_social_description'] = array();
		}

		if (isset($this->request->post['mpcheckout_payment_method_panel'])) {
			$data['mpcheckout_payment_method_panel'] = $this->request->post['mpcheckout_payment_method_panel'];
		} else if(isset($module_info['mpcheckout_payment_method_panel'])) {
			$data['mpcheckout_payment_method_panel'] = $module_info['mpcheckout_payment_method_panel'];
		} else {
			$data['mpcheckout_payment_method_panel'] = array();
		}

		if (isset($this->request->post['mpcheckout_payment_method_table'])) {
			$mpcheckout_payment_method_tables = $this->request->post['mpcheckout_payment_method_table'];
		} else if(isset($module_info['mpcheckout_payment_method_table'])) {
			$mpcheckout_payment_method_tables = $module_info['mpcheckout_payment_method_table'];
		} else {
			$mpcheckout_payment_method_tables = array();
		}

		$this->load->model('tool/image');

		$data['mpcheckout_payment_method_tables'] = array();
		foreach ($mpcheckout_payment_method_tables as $key => $mpcheckout_payment_method_table) {
			if (!empty($mpcheckout_payment_method_table['image']) && is_file(DIR_IMAGE . $mpcheckout_payment_method_table['image'])) {
				$thumb = $this->model_tool_image->resize($mpcheckout_payment_method_table['image'], 100, 100);
			} else {
				$thumb = $this->model_tool_image->resize('no_image.png', 100, 100);
			}

			$data['mpcheckout_payment_method_tables'][$key] = array(
				'image'			=> $mpcheckout_payment_method_table['image'],
				'thumb'			=> $thumb,
			);
		}

		if (isset($this->request->post['mpcheckout_shipping_method_table'])) {
			$mpcheckout_shipping_method_tables = $this->request->post['mpcheckout_shipping_method_table'];
		} else if(isset($module_info['mpcheckout_shipping_method_table'])) {
			$mpcheckout_shipping_method_tables = $module_info['mpcheckout_shipping_method_table'];
		} else {
			$mpcheckout_shipping_method_tables = array();
		}

		$this->load->model('tool/image');

		$data['mpcheckout_shipping_method_tables'] = array();
		foreach ($mpcheckout_shipping_method_tables as $key => $mpcheckout_shipping_method_table) {
			if (!empty($mpcheckout_shipping_method_table['image']) && is_file(DIR_IMAGE . $mpcheckout_shipping_method_table['image'])) {
				$thumb = $this->model_tool_image->resize($mpcheckout_shipping_method_table['image'], 100, 100);
			} else {
				$thumb = $this->model_tool_image->resize('no_image.png', 100, 100);
			}

			$data['mpcheckout_shipping_method_tables'][$key] = array(
				'image'			=> $mpcheckout_shipping_method_table['image'],
				'thumb'			=> $thumb,
			);
		}
		

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		
		if (isset($this->request->post['mpcheckout_shipping_method_panel'])) {
			$data['mpcheckout_shipping_method_panel'] = $this->request->post['mpcheckout_shipping_method_panel'];
		} else if(isset($module_info['mpcheckout_shipping_method_panel'])) {
			$data['mpcheckout_shipping_method_panel'] = $module_info['mpcheckout_shipping_method_panel'];
		} else {
			$data['mpcheckout_shipping_method_panel'] = array();
		}

		if (isset($this->request->post['mpcheckout_shoppingcart_panel'])) {
			$data['mpcheckout_shoppingcart_panel'] = $this->request->post['mpcheckout_shoppingcart_panel'];
		} else if(isset($module_info['mpcheckout_shoppingcart_panel'])) {
			$data['mpcheckout_shoppingcart_panel'] = $module_info['mpcheckout_shoppingcart_panel'];
		} else {
			$data['mpcheckout_shoppingcart_panel'] = array();
		}


		if (isset($this->request->post['mpcheckout_page_description'])) {
			$data['mpcheckout_page_description'] = $this->request->post['mpcheckout_page_description'];
		} else if(isset($module_info['mpcheckout_page_description'])) {
			$data['mpcheckout_page_description'] = $module_info['mpcheckout_page_description'];
		} else {
			$data['mpcheckout_page_description'] = array();
		}

		if (isset($this->request->post['mpcheckout_date_description'])) {
			$data['mpcheckout_date_description'] = $this->request->post['mpcheckout_date_description'];
		} else if(isset($module_info['mpcheckout_date_description'])) {
			$data['mpcheckout_date_description'] = $module_info['mpcheckout_date_description'];
		} else {
			$data['mpcheckout_date_description'] = array();
		}

		if (isset($this->request->post['mpcheckout_account_button_description'])) {
			$data['mpcheckout_account_button_description'] = $this->request->post['mpcheckout_account_button_description'];
		} else if(isset($module_info['mpcheckout_account_button_description'])) {
			$data['mpcheckout_account_button_description'] = $module_info['mpcheckout_account_button_description'];
		} else {
			$data['mpcheckout_account_button_description'] = array();
		}

		if (isset($this->request->post['mpcheckout_account_panel_description'])) {
			$data['mpcheckout_account_panel_description'] = $this->request->post['mpcheckout_account_panel_description'];
		} else if(isset($module_info['mpcheckout_account_panel_description'])) {
			$data['mpcheckout_account_panel_description'] = $module_info['mpcheckout_account_panel_description'];
		} else {
			$data['mpcheckout_account_panel_description'] = array();
		}

		if (isset($this->request->post['mpcheckout_login_panel_description'])) {
			$data['mpcheckout_login_panel_description'] = $this->request->post['mpcheckout_login_panel_description'];
		} else if(isset($module_info['mpcheckout_login_panel_description'])) {
			$data['mpcheckout_login_panel_description'] = $module_info['mpcheckout_login_panel_description'];
		} else {
			$data['mpcheckout_login_panel_description'] = array();
		}

		if (isset($this->request->post['mpcheckout_payment_address_description'])) {
			$data['mpcheckout_payment_address_description'] = $this->request->post['mpcheckout_payment_address_description'];
		} else if(isset($module_info['mpcheckout_payment_address_description'])) {
			$data['mpcheckout_payment_address_description'] = $module_info['mpcheckout_payment_address_description'];
		} else {
			$data['mpcheckout_payment_address_description'] = array();
		}

		if (isset($this->request->post['mpcheckout_shipping_address_description'])) {
			$data['mpcheckout_shipping_address_description'] = $this->request->post['mpcheckout_shipping_address_description'];
		} else if(isset($module_info['mpcheckout_shipping_address_description'])) {
			$data['mpcheckout_shipping_address_description'] = $module_info['mpcheckout_shipping_address_description'];
		} else {
			$data['mpcheckout_shipping_address_description'] = array();
		}
		
		if (isset($this->request->post['mpcheckout_payment_method_description'])) {
			$data['mpcheckout_payment_method_description'] = $this->request->post['mpcheckout_payment_method_description'];
		} else if(isset($module_info['mpcheckout_payment_method_description'])) {
			$data['mpcheckout_payment_method_description'] = $module_info['mpcheckout_payment_method_description'];
		} else {
			$data['mpcheckout_payment_method_description'] = array();
		}
		
		if (isset($this->request->post['mpcheckout_shipping_method_description'])) {
			$data['mpcheckout_shipping_method_description'] = $this->request->post['mpcheckout_shipping_method_description'];
		} else if(isset($module_info['mpcheckout_shipping_method_description'])) {
			$data['mpcheckout_shipping_method_description'] = $module_info['mpcheckout_shipping_method_description'];
		} else {
			$data['mpcheckout_shipping_method_description'] = array();
		}
		
		if (isset($this->request->post['mpcheckout_cart_description'])) {
			$data['mpcheckout_cart_description'] = $this->request->post['mpcheckout_cart_description'];
		} else if(isset($module_info['mpcheckout_cart_description'])) {
			$data['mpcheckout_cart_description'] = $module_info['mpcheckout_cart_description'];
		} else {
			$data['mpcheckout_cart_description'] = array();
		}
		
		if (isset($this->request->post['mpcheckout_confirm_order_description'])) {
			$data['mpcheckout_confirm_order_description'] = $this->request->post['mpcheckout_confirm_order_description'];
		} else if(isset($module_info['mpcheckout_confirm_order_description'])) {
			$data['mpcheckout_confirm_order_description'] = $module_info['mpcheckout_confirm_order_description'];
		} else {
			$data['mpcheckout_confirm_order_description'] = array();
		}

		if (isset($this->request->post['mpcheckout_success_description'])) {
			$data['mpcheckout_success_description'] = $this->request->post['mpcheckout_success_description'];
		} else if(isset($module_info['mpcheckout_success_description'])) {
			$data['mpcheckout_success_description'] = $module_info['mpcheckout_success_description'];
		} else {
			$data['mpcheckout_success_description'] = array();
		}

		if (isset($this->request->post['mpcheckout_success_width'])) {
			$data['mpcheckout_success_width'] = $this->request->post['mpcheckout_success_width'];
		} else if(isset($module_info['mpcheckout_success_width'])) {
			$data['mpcheckout_success_width'] = $module_info['mpcheckout_success_width'];
		} else {
			$data['mpcheckout_success_width'] = '150';
		}
		if (isset($this->request->post['mpcheckout_success_height'])) {
			$data['mpcheckout_success_height'] = $this->request->post['mpcheckout_success_height'];
		} else if(isset($module_info['mpcheckout_success_height'])) {
			$data['mpcheckout_success_height'] = $module_info['mpcheckout_success_height'];
		} else {
			$data['mpcheckout_success_height'] = '150';
		}

		if (isset($this->request->post['mpcheckout_success_promote'])) {
			$data['mpcheckout_success_promote'] = $this->request->post['mpcheckout_success_promote'];
		} else if(isset($module_info['mpcheckout_success_promote'])) {
			$data['mpcheckout_success_promote'] = $module_info['mpcheckout_success_promote'];
		} else {
			$data['mpcheckout_success_promote'] = '';
		}

		if (isset($this->request->post['mpcheckout_success_promote_title'])) {
			$data['mpcheckout_success_promote_title'] = $this->request->post['mpcheckout_success_promote_title'];
		} else if(isset($module_info['mpcheckout_success_promote_title'])) {
			$data['mpcheckout_success_promote_title'] = (array)$module_info['mpcheckout_success_promote_title'];
		} else {
			$data['mpcheckout_success_promote_title'] = array();
		}

		if (isset($this->request->post['mpcheckout_color'])) {
			$data['mpcheckout_color'] = $this->request->post['mpcheckout_color'];
		} else if(isset($module_info['mpcheckout_color'])) {
			$data['mpcheckout_color'] = (array)$module_info['mpcheckout_color'];
		} else {
			$data['mpcheckout_color'] = array();
		}

		if (isset($this->request->post['mpcheckout_css'])) {
			$data['mpcheckout_css'] = $this->request->post['mpcheckout_css'];
		} else if(isset($module_info['mpcheckout_css'])) {
			$data['mpcheckout_css'] = $module_info['mpcheckout_css'];
		} else {
			$data['mpcheckout_css'] = '';
		}

		if (isset($this->request->post['mpcheckout_googleanalytics'])) {
			$data['mpcheckout_googleanalytics'] = $this->request->post['mpcheckout_googleanalytics'];
		} else if(isset($module_info['mpcheckout_googleanalytics'])) {
			$data['mpcheckout_googleanalytics'] = $module_info['mpcheckout_googleanalytics'];
		} else {
			$data['mpcheckout_googleanalytics'] = '';
		}

		if (isset($this->request->post['mpcheckout_print_status'])) {
			$data['mpcheckout_print_status'] = $this->request->post['mpcheckout_print_status'];
		} else if(isset($module_info['mpcheckout_print_status'])) {
			$data['mpcheckout_print_status'] = $module_info['mpcheckout_print_status'];
		} else {
			$data['mpcheckout_print_status'] = '';
		}

		// Products
		$this->load->model('catalog/product');

		if (isset($this->request->post['mpcheckout_success_product'])) {
			$product_products = $this->request->post['mpcheckout_success_product'];
		} elseif (isset($module_info['mpcheckout_success_product'])) {
			$product_products = $module_info['mpcheckout_success_product'];
		} else {
			$product_products = array();
		}

		$data['product_products'] = array();

		foreach ($product_products as $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);
			if ($product_info) {
				$data['product_products'][] = array(
					'product_id' => $product_info['product_id'],
					'name'        => $product_info['name']
				);
			}
		}

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$this->load->model('setting/store');
		$data['stores'] = $this->model_setting_store->getStores();

		$store_info = $this->model_setting_store->getStore($data['store_id']);
		if($store_info) {
			$data['store_name'] = $store_info['name'];
		}else{
			$data['store_name'] = $this->language->get('text_default');
		}

		$this->load->model('localisation/country');
		$data['countries'] = $this->model_localisation_country->getCountries();

		if(VERSION > '2.0.3.1') {
			$this->load->model('customer/customer_group');
			$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();
		} else{
			$this->load->model('sale/customer_group');
			$data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();
		}

		$this->load->model('catalog/information');
		$data['informations'] = $this->model_catalog_information->getInformations();

		$this->load->model('setting/extension');
		$payment_methods = $this->model_setting_extension->getInstalled('payment');
		$data['payment_methods'] = array();
		foreach ($payment_methods as $key => $payment_method) {
			$payment_method_basename = basename($payment_method, '.php');
				if( VERSION > '2.2.0.0' ) {
					$this->load->language('extension/payment/' . $payment_method_basename);
				} else{
					$this->load->language('payment/' . $payment_method_basename);
				}

				$data['payment_methods'][] = array(
					'name'       => $this->language->get('heading_title'),
					'code'       => $payment_method_basename,
				);
		}

		$shipping_methods = $this->model_setting_extension->getInstalled('shipping');
		$data['shipping_methods'] = array();
		foreach ($shipping_methods as $key => $shipping_method) {
			$shipping_method_basename = basename($shipping_method, '.php');
				if( VERSION > '2.2.0.0' ) {
					$this->load->language('extension/shipping/' . $shipping_method_basename);
				} else {
					$this->load->language('shipping/' . $shipping_method_basename);
				}

				$data['shipping_methods'][] = array(
					'name'       => $this->language->get('heading_title'),
					'code'       => $shipping_method_basename,
				);
		}

		// Display Columns
		// Account Fields
		$data['account_fields'] = array();
		$data['account_fields'][] = array(
			'title'			=> $this->language->get('column_firstname'),
			'code'			=> 'firstname',
		);		
		$data['account_fields'][] = array(
			'title'			=> $this->language->get('column_lastname'),
			'code'			=> 'lastname',
		);
		$data['account_fields'][] = array(
			'title'			=> $this->language->get('column_email'),
			'code'			=> 'email',
		);
		$data['account_fields'][] = array(
			'title'			=> $this->language->get('column_telephone'),
			'code'			=> 'telephone',
		);
		$data['account_fields'][] = array(
			'title'			=> $this->language->get('column_fax'),
			'code'			=> 'fax',
		);
		$data['account_fields'][] = array(
			'title'			=> $this->language->get('column_company'),
			'code'			=> 'company',
		);
		$data['account_fields'][] = array(
			'title'			=> $this->language->get('column_address_1'),
			'code'			=> 'address_1',
		);
		$data['account_fields'][] = array(
			'title'			=> $this->language->get('column_address_2'),
			'code'			=> 'address_2',
		);
		$data['account_fields'][] = array(
			'title'			=> $this->language->get('column_city'),
			'code'			=> 'city',
		);
		$data['account_fields'][] = array(
			'title'			=> $this->language->get('column_postcode'),
			'code'			=> 'postcode',
		);
		$data['account_fields'][] = array(
			'title'			=> $this->language->get('column_country'),
			'code'			=> 'country',
		);
		$data['account_fields'][] = array(
			'title'			=> $this->language->get('column_state'),
			'code'			=> 'zone',
		);
		$data['account_fields'][] = array(
			'title'			=> $this->language->get('column_password'),
			'code'			=> 'password',
		);
		$data['account_fields'][] = array(
			'title'			=> $this->language->get('column_confirm'),
			'code'			=> 'confirm_password',
		);


		// Address Fields
		$data['address_fields'] = array();
		$data['address_fields'][] = array(
			'title'			=> $this->language->get('column_firstname'),
			'code'			=> 'firstname',
		);		
		$data['address_fields'][] = array(
			'title'			=> $this->language->get('column_lastname'),
			'code'			=> 'lastname',
		);
		$data['address_fields'][] = array(
			'title'			=> $this->language->get('column_company'),
			'code'			=> 'company',
		);
		$data['address_fields'][] = array(
			'title'			=> $this->language->get('column_address_1'),
			'code'			=> 'address_1',
		);
		$data['address_fields'][] = array(
			'title'			=> $this->language->get('column_address_2'),
			'code'			=> 'address_2',
		);
		$data['address_fields'][] = array(
			'title'			=> $this->language->get('column_city'),
			'code'			=> 'city',
		);
		$data['address_fields'][] = array(
			'title'			=> $this->language->get('column_postcode'),
			'code'			=> 'postcode',
		);
		$data['address_fields'][] = array(
			'title'			=> $this->language->get('column_country'),
			'code'			=> 'country',
		);
		$data['address_fields'][] = array(
			'title'			=> $this->language->get('column_state'),
			'code'			=> 'zone',
		);

		// Weeks
		$data['weeks'] = array();		
		$data['weeks'][] = array(
			'week_title'			=> $this->language->get('text_sunday'),
			'week_number'			=> '0',
		);
		$data['weeks'][] = array(
			'week_title'			=> $this->language->get('text_monday'),
			'week_number'			=> '1',
		);

		$data['weeks'][] = array(
			'week_title'			=> $this->language->get('text_tuesday'),
			'week_number'			=> '2',
		);
		$data['weeks'][] = array(
			'week_title'			=> $this->language->get('text_wednesday'),
			'week_number'			=> '3',
		);
		$data['weeks'][] = array(
			'week_title'			=> $this->language->get('text_thrusday'),
			'week_number'			=> '4',
		);
		$data['weeks'][] = array(
			'week_title'			=> $this->language->get('text_friday'),
			'week_number'			=> '5',
		);
		$data['weeks'][] = array(
			'week_title'			=> $this->language->get('text_saturday'),
			'week_number'			=> '6',
		);

		if(!isset($data['mpcheckout_account_button']['account_buttons_status'])) {
      		$data['mpcheckout_account_button']['account_buttons_status'] = array('register', 'guest', 'login');
     	}

     	if(!isset($data['mpcheckout_account_button']['default_account_button'])) {
  	  		$data['mpcheckout_account_button']['default_account_button'] = 'register';
      	}

      	if(!isset($data['mpcheckout_account_panel']['default_account_id'])) {
 	   	  	$data['mpcheckout_account_panel']['default_account_id'] = '1';
      	}

      	if(!isset($data['mpcheckout_account_panel']['newsletter_subscribe'])) {
        	$data['mpcheckout_account_panel']['newsletter_subscribe'] = '1';
      	}

      	if(!isset($data['mpcheckout_date_panel']['minimum_days'])) {
        	$data['mpcheckout_date_panel']['minimum_days'] = '1';
      	}

      	if(!isset($data['mpcheckout_date_panel']['maximum_days'])) {
    	    $data['mpcheckout_date_panel']['maximum_days'] = '30';
      	}			

      	if(!isset($data['mpcheckout_shoppingcart_panel']['cart_status'])) {
   			$data['mpcheckout_shoppingcart_panel']['cart_status'] = '1';
      	}

      	if(!isset($data['mpcheckout_shoppingcart_panel']['show_weight'])) {
        	$data['mpcheckout_shoppingcart_panel']['show_weight'] = '1';
  		}

  		if(!isset($data['mpcheckout_shoppingcart_panel']['qty_update'])) {
            $data['mpcheckout_shoppingcart_panel']['qty_update'] = '1';
      	}

      	if(!isset($data['mpcheckout_shoppingcart_panel']['show_product_image'])) {
            $data['mpcheckout_shoppingcart_panel']['show_product_image'] = '1';
      	}

      	if(!isset($data['mpcheckout_shoppingcart_panel']['product_image_width'])) {
    	   $data['mpcheckout_shoppingcart_panel']['product_image_width'] = '75';
      	}	

      	if(!isset($data['mpcheckout_shoppingcart_panel']['product_image_height'])) {
	    	$data['mpcheckout_shoppingcart_panel']['product_image_height'] = '75';
      	}

      	if(!isset($data['mpcheckout_confirm_panel']['default_checkout_id'])) {
	        $data['mpcheckout_confirm_panel']['default_checkout_id'] = '1';
      	}

      	if(!isset($data['mpcheckout_confirm_panel']['continue_shopping_button'])) {
        	$data['mpcheckout_confirm_panel']['continue_shopping_button'] = '1';
      	}

      	if(!isset($data['mpcheckout_confirm_panel']['show_comment'])) {
        	$data['mpcheckout_confirm_panel']['show_comment'] = '1';
      	}

      	if(!isset($data['mpcheckout_confirm_panel']['overlay'])) {
        	$data['mpcheckout_confirm_panel']['overlay'] = '1';
      	}

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('mpcheckout/mpcheckout', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'mpcheckout/mpcheckout')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ($this->request->post['mpcheckout_country_id'] == '') {
			$this->error['country'] = $this->language->get('error_country');
		}

		if (!isset($this->request->post['mpcheckout_zone_id']) || $this->request->post['mpcheckout_zone_id'] == '' || !is_numeric($this->request->post['mpcheckout_zone_id'])) {
			$this->error['zone'] = $this->language->get('error_zone');
		}

		if (empty($this->request->post['mpcheckout_account_button']['account_buttons_status'])) {
			$this->error['account_buttons_status'] = $this->language->get('error_account_buttons_status');
		}

		if (!empty($this->request->post['mpcheckout_account_button']['account_buttons_status']) && !in_array($this->request->post['mpcheckout_account_button']['default_account_button'], $this->request->post['mpcheckout_account_button']['account_buttons_status'])) {
			$this->error['default_account_button'] = $this->language->get('error_default_account_button');
		}


		if($this->request->post['mpcheckout_shoppingcart_panel']['show_product_image']) {
			if (!$this->request->post['mpcheckout_shoppingcart_panel']['product_image_width'] || !$this->request->post['mpcheckout_shoppingcart_panel']['product_image_height']) {
				$this->error['image_cart'] = $this->language->get('error_image_cart');
			}

		}

		if($this->request->post['mpcheckout_success_promote'] && $this->request->post['mpcheckout_success_status']) {
			if (!$this->request->post['mpcheckout_success_width'] || !$this->request->post['mpcheckout_success_height']) {
				$this->error['success_image_size'] = $this->language->get('error_image_cart');
			}
		}

		if(!empty($this->request->post['mpcheckout_date_panel']['status'])) {
			if($this->request->post['mpcheckout_date_panel']['minimum_days'] < 0) {
				$this->error['days'] = $this->language->get('error_days');	
			}

			if($this->request->post['mpcheckout_date_panel']['maximum_days'] <= 0) {
				$this->error['days'] = $this->language->get('error_days');	
			}			
		}

		if(!empty($this->request->post['mpcheckout_social_panel']['google']['status'])) {
			if(!$this->request->post['mpcheckout_social_panel']['google']['appid']) {
				$this->error['google_appid'] = $this->language->get('error_appid');	
			}

			if(!$this->request->post['mpcheckout_social_panel']['google']['secret']) {
				$this->error['google_secret'] = $this->language->get('error_secret');	
			}
		}

		if(!empty($this->request->post['mpcheckout_social_panel']['facebook']['status'])) {
			if(!$this->request->post['mpcheckout_social_panel']['facebook']['appid']) {
				$this->error['facebook_appid'] = $this->language->get('error_appid');	
			}

			if(!$this->request->post['mpcheckout_social_panel']['facebook']['secret']) {
				$this->error['facebook_secret'] = $this->language->get('error_secret');	
			}
		}

		if(!empty($this->request->post['mpcheckout_social_panel']['linkedin']['status'])) {
			if(!$this->request->post['mpcheckout_social_panel']['linkedin']['appid']) {
				$this->error['linkedin_appid'] = $this->language->get('error_appid');	
			}

			if(!$this->request->post['mpcheckout_social_panel']['linkedin']['secret']) {
				$this->error['linkedin_secret'] = $this->language->get('error_secret');	
			}
		}
		/*new updates 28032018 starts*/
		if(!empty($this->request->post['mpcheckout_social_panel']['instagram']['status'])) {
			if(!$this->request->post['mpcheckout_social_panel']['instagram']['appid']) {
				$this->error['instagram_appid'] = $this->language->get('error_appid');	
			}

			if(!$this->request->post['mpcheckout_social_panel']['instagram']['secret']) {
				$this->error['instagram_secret'] = $this->language->get('error_secret');	
			}
		}
		if(!empty($this->request->post['mpcheckout_social_panel']['twitter']['status'])) {
			if(!$this->request->post['mpcheckout_social_panel']['twitter']['appid']) {
				$this->error['twitter_appid'] = $this->language->get('error_appid');	
			}

			if(!$this->request->post['mpcheckout_social_panel']['twitter']['secret']) {
				$this->error['twitter_secret'] = $this->language->get('error_secret');	
			}
		}
		/*new updates 28032018 starts*/
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	public function country() {
		$json = array();

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}