<?php
/* This file is under Git Control by KDSI. */
class ControllerCommonFooter extends Controller {
	public function index() {

		if($this->config->get('jade_footer_status')) {
			return $this->load->controller('common/jade_footer');
		}
				

		$data['sweetalert_fragment_in_header'] = $this->load->controller('extension/module/sweetalert/fragment_in_header');
            
		$this->load->language('common/footer');

		$this->load->model('catalog/information');

		$data['informations'] = array();

			/*Ticket system starts*/
			$this->load->language('support/menu_ticket');
			
			$data['menu_support'] = $this->language->get('menu_support');
			
			$data['mpsupport'] = $this->url->link('support/support', '', true);

			$data['ticketsetting_status'] = $this->config->get('ticketsetting_status');
			/*Ticket system ends*/
			

        $file = DIR_SYSTEM . 'library/gdpr.php';
		if (is_file($file)) {
			$this->config->load('isenselabs/isenselabs_gdpr');
			$gdpr_name = $this->config->get('isenselabs_gdpr_name');
			$gdpr_path = $this->config->get('isenselabs_gdpr_path');
			$this->load->language($gdpr_path);

			$this->load->model('setting/setting');
			$moduleSettings = $this->model_setting_setting->getSetting($gdpr_name, $this->config->get('config_store_id'));
			$gdpr_settings = !empty($moduleSettings[$gdpr_name]) ? $moduleSettings[$gdpr_name] : array();

			if (!empty($gdpr_settings['Enabled']) && $gdpr_settings['Enabled'] == '1' && !empty($gdpr_settings['LinkInFooter']) && $gdpr_settings['LinkInFooter'] == '1') {
				$data['informations'][] = array(
					'title' => $this->language->get('text_gdpr'),
					'href'  => $this->url->link($gdpr_path, '', true)
				);
			}
		}
        

		foreach ($this->model_catalog_information->getInformations() as $result) {
			if ($result['bottom']) {
				$data['informations'][] = array(
					'title' => $result['title'],
					'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
				);
			}
		}

		$data['contact'] = $this->url->link('information/contact');
		$data['return'] = $this->url->link('account/return/add', '', true);
		$data['sitemap'] = $this->url->link('information/sitemap');
		$data['tracking'] = $this->url->link('information/tracking');
		$data['manufacturer'] = $this->url->link('product/manufacturer');
		$data['voucher'] = $this->url->link('account/voucher', '', true);
		$data['affiliate'] = $this->url->link('affiliate/login', '', true);

                if ($this->config->get('module_galleria_page_status') && $this->config->get('module_galleria_page_menu')) {
                    $data['text_galleria'] = $this->config->get('module_galleria_page_menu_title')[$this->config->get('config_language_id')];
                    $data['galleria_href'] = $this->url->link('extension/module/galleria', '', true);
                }
            
		$data['special'] = $this->url->link('product/special');
		$data['account'] = $this->url->link('account/account', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		//$data['newsletter'] = $this->url->link('account/newsletter', '', true);
		if ($this->config->get('module_emailtemplate_newsletter_status') && $this->config->get('module_emailtemplate_newsletter_preference')) {
			$data['newsletter'] = $this->url->link('extension/module/emailtemplate_newsletter', '', true);
		} else {
			$data['newsletter'] = $this->url->link('account/newsletter', '', true);
		}
        

		$data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));


		$data['footer_right'] = $this->load->controller('common/footer_right');
		$data['footer_bottom'] = $this->load->controller('common/footer_bottom');
		$data['footer_left'] = $this->load->controller('common/footer_left');

		// Manufacture
		$this->load->model('catalog/manufacturer');
		$data['manufacturer_list'] = array();
		$manufacturers = $this->model_catalog_manufacturer->getManufacturers();
		foreach ($manufacturers as $manufacturer_list) {
			$data['manufacturer_list'][] = array(
				'name' => $manufacturer_list['name'],
				'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer_list['manufacturer_id'])
			);
		}
		//End Manufacture
			
		// Whos Online
		if ($this->config->get('config_customer_online')) {
			$this->load->model('tool/online');

			if (isset($this->request->server['REMOTE_ADDR'])) {
				$ip = $this->request->server['REMOTE_ADDR'];
			} else {
				$ip = '';
			}

			if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
				$url = ($this->request->server['HTTPS'] ? 'https://' : 'http://') . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
			} else {
				$url = '';
			}

			if (isset($this->request->server['HTTP_REFERER'])) {
				$referer = $this->request->server['HTTP_REFERER'];
			} else {
				$referer = '';
			}

			$this->model_tool_online->addOnline($ip, $this->customer->getId(), $url, $referer);
		}


		$data['footer_right'] = $this->load->controller('common/footer_right');
		$data['footer_top'] = $this->load->controller('common/footer_top');
			
		$data['scripts'] = $this->document->getScripts('footer');

            $data['page_route'] = !empty($this->request->get['route']) ? $this->request->get['route'] : 'common/home';
            

		$data['module_footer_social_icons_status'] = $this->config->get('module_footer_social_icons_status');

		$data['whatsapp_no'] = $this->config->get('module_footer_social_icons_whatsapp_link');
		$data['blog_link'] = $this->config->get('module_footer_social_icons_blog_link');
		$data['youtube_link'] = $this->config->get('module_footer_social_icons_youtube_link');
		$data['facebook_link'] = $this->config->get('module_footer_social_icons_facebook_link');
		$data['twitter_link'] = $this->config->get('module_footer_social_icons_twitter_link');
		$data['pinterest_link'] = $this->config->get('module_footer_social_icons_pinterest_link');
		$data['googlep_link'] = $this->config->get('module_footer_social_icons_googlep_link');
		$data['linkedin_link'] = $this->config->get('module_footer_social_icons_linkedin_link');
		$data['instagram_link'] = $this->config->get('module_footer_social_icons_instagram_link');
			
		$data['styles'] = $this->document->getStyles('footer');
		

			$data['at_js'] = html_entity_decode($this->config->get('config_at_custom_js') ? $this->config->get('config_at_custom_js') : '', ENT_QUOTES, 'UTF-8');
			
		return $this->load->view('common/footer', $data);
	}
}
