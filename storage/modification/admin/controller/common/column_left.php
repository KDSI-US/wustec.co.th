<?php
/* This file is under Git Control by KDSI. */
class ControllerCommonColumnLeft extends Controller {
	public function index() {
		if (isset($this->request->get['user_token']) && isset($this->session->data['user_token']) && ($this->request->get['user_token'] == $this->session->data['user_token'])) {
			$this->load->language('common/column_left');

			// Create a 3 level menu array
			// Level 2 can not have children

			// Menu
			$data['menus'][] = array(
				'id'       => 'menu-dashboard',
				'icon'	   => 'fa-dashboard',
				'name'	   => $this->language->get('text_dashboard'),
				'href'     => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
				'children' => array()
			);

			// Catalog

			/* mps */
			if ($mpblog = $this->load->controller('extension/module/mpblogsettings/getMenu')) {
				$data['menus'][] =	$mpblog;
			}
			/* mpe */
			
			$catalog = array();

			if ($this->user->hasPermission('access', 'catalog/category')) {
				$catalog[] = array(
					'name'	   => $this->language->get('text_category'),
					'href'     => $this->url->link('catalog/category', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'catalog/product')) {
				$catalog[] = array(
					'name'	   => $this->language->get('text_product'),
					'href'     => $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}


			if ($this->user->hasPermission('access', 'extension/module/image_checker')) {
				$catalog[] = array(
					'name'	   => $this->language->get('text_image_checker'),
					'href'     => $this->url->link('extension/module/image_checker', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}
            
			if ($this->user->hasPermission('access', 'catalog/recurring')) {
				$catalog[] = array(
					'name'	   => $this->language->get('text_recurring'),
					'href'     => $this->url->link('catalog/recurring', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'catalog/filter')) {
				$catalog[] = array(
					'name'	   => $this->language->get('text_filter'),
					'href'     => $this->url->link('catalog/filter', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			// Attributes
			$attribute = array();

			if ($this->user->hasPermission('access', 'catalog/attribute')) {
				$attribute[] = array(
					'name'     => $this->language->get('text_attribute'),
					'href'     => $this->url->link('catalog/attribute', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'catalog/attribute_group')) {
				$attribute[] = array(
					'name'	   => $this->language->get('text_attribute_group'),
					'href'     => $this->url->link('catalog/attribute_group', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($attribute) {
				$catalog[] = array(
					'name'	   => $this->language->get('text_attribute'),
					'href'     => '',
					'children' => $attribute
				);
			}

			if ($this->user->hasPermission('access', 'catalog/option')) {
				$catalog[] = array(
					'name'	   => $this->language->get('text_option'),
					'href'     => $this->url->link('catalog/option', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'catalog/manufacturer')) {
				$catalog[] = array(
					'name'	   => $this->language->get('text_manufacturer'),
					'href'     => $this->url->link('catalog/manufacturer', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'catalog/download')) {
				$catalog[] = array(
					'name'	   => $this->language->get('text_download'),
					'href'     => $this->url->link('catalog/download', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'catalog/review')) {
				$catalog[] = array(
					'name'	   => $this->language->get('text_review'),
					'href'     => $this->url->link('catalog/review', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'catalog/information')) {
				$catalog[] = array(
					'name'	   => $this->language->get('text_information'),
					'href'     => $this->url->link('catalog/information', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}


			// Mobile App
			$mobileapp = array();
			
			if ($this->user->hasPermission('access', 'mobileapp/banners')) 
			{
				$mobileapp[] = array(
					'name'	   => $this->language->get('text_app_banner'),
					'href'     => $this->url->link('mobileapp/banners', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'mobileapp/image_setting')) 
			{
				$mobileapp[] = array(
					'name'	   => $this->language->get('text_app_image'),
					'href'     => $this->url->link('mobileapp/image_setting', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'mobileapp/category')) 
			{
				$mobileapp[] = array(
					'name'	   => $this->language->get('text_app_categories'),
					'href'     => $this->url->link('mobileapp/category', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($mobileapp) 
			{
				$catalog[] = array(
					'name'	   => $this->language->get('text_app_restapi'),
					'href'     => '',
					'children' => $mobileapp
				);
			}
			

			if ($catalog) {
				$data['menus'][] = array(
					'id'       => 'menu-catalog',
					'icon'	   => 'fa-tags',
					'name'	   => $this->language->get('text_catalog'),
					'href'     => '',
					'children' => $catalog
				);
			}


			if ($mpfaqquestion = $this->load->controller('extension/mpfaq/mpfaqquestion/getMenu')) {
				$data['menus'][] = $mpfaqquestion;
			}
			

			/* MP Membership Starts */
			$mpmembership = array();
			$this->load->language('modulepoints/menu_membership');
			if ($this->user->hasPermission('access', 'modulepoints/mpplan')) {
				$mpmembership[] = array(
					'name'	   => $this->language->get('menu_plan'),
					'href'     => $this->url->link('modulepoints/mpplan', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'modulepoints/plansetting')) {
				$mpmembership[] = array(
					'name'	   => $this->language->get('menu_plansetting'),
					'href'     => $this->url->link('modulepoints/plansetting', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($mpmembership) {
				$data['menus'][] = array(
					'id'       => 'menu-membership',
					'icon'	   => 'fa-user',
					'name'	   => $this->language->get('menu_membership'),
					'href'     => '',
					'children' => $mpmembership
				);
			}
			/* MP Membership Ends */
			

			if ($mpphotogallery = $this->load->controller('extension/module/mpphotogallery_setting/getMenu')) {
				$data['menus'][] = $mpphotogallery;
			}
			
			/* XML START */
			$wblog = array();
			if ($this->user->hasPermission('access', 'extension/dashboard')) {
				$wblog[] = array(
					'name'	   => $this->language->get('text_wblog'),
					'href'     => $this->url->link('extension/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL'),
					'children' => array()
				);			
			}
			
			if ($wblog) {	
				$data['menus'][] = array(
					'id'       => 'menu-report',
					'icon'	   => 'fa-list-alt', 
					'name'	   => $this->language->get('text_wblog'),
					'href'     => '',
					'children' => $wblog
				);	
			}
			/* XML ENDS */

			$announcements = array();
			
			if ($this->user->hasPermission('access', 'extension/announcements')) {
				$announcements[] = array(
					'name'	   => $this->language->get('text_add_announcements'),
					'href'     => $this->url->link('extension/announcements', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}
			
			if ($announcements) {
				$data['menus'][] = array(
					'id'       => 'menu-announcements',
					'icon'	   => 'fa fa-newspaper-o',
					'name'	   => $this->language->get('text_announcements'),
					'href'     => '',
					'children' => $announcements
				);
			}
			// Extension

			$galleria = array();
			if ($this->user->hasPermission('access', 'extension/module/galleria')) {
				if ($this->config->get('module_galleria_status')) {
					$galleria[] = array(
						'name'     => $this->language->get('text_galleria_list'),
						'href'     => $this->url->link('extension/module/galleria/galleria_list', 'user_token=' . $this->session->data['user_token'], true),
						'children' => array()
					);
				}
				$galleria[] = array(
					'name'     => $this->language->get('text_galleria_setting'),
					'href'     => $this->url->link('extension/module/galleria', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}
			if ($galleria) {
				$data['menus'][] = array(
					'id'       => 'menu-image-galery',
					'icon'	   => 'fa fa-camera',
					'name'     => $this->language->get('text_galleria'),
					'href'     => '',
					'children' => $galleria
				);
			}

			$marketplace = array();

			if ($this->user->hasPermission('access', 'marketplace/marketplace')) {
				$marketplace[] = array(
					'name'	   => $this->language->get('text_marketplace'),
					'href'     => $this->url->link('marketplace/marketplace', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'marketplace/installer')) {
				$marketplace[] = array(
					'name'	   => $this->language->get('text_installer'),
					'href'     => $this->url->link('marketplace/installer', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

$extension_children = array();
			$extension_types = array(
				'analytics'	=> 'Analytics',
				'fraud'		=> 'Anti-Fraud',
				'captcha'	=> 'Captchas',
				'dashboard'	=> 'Dashboard',
				'feed'		=> 'Feeds',
				'menu'		=> 'Menu',
				'module'	=> 'Modules',
				'total'		=> 'Order Totals',
				'payment'	=> 'Payments',
				'report'	=> 'Reports',
				'shipping'	=> 'Shipping',
				'theme'		=> 'Themes',
			);
			foreach ($extension_types as $key => $value) {
				if (!$this->user->hasPermission('access', 'extension/extension/' . $key)) continue;
				$extension_children[] = array(
					'name'		=> $value,
					'href'		=> $this->url->link('marketplace/extension', 'type=' . $key . '&user_token=' . $this->session->data['user_token'], true),
					'children'	=> array(),
				);
			}
			if ($this->user->hasPermission('access', 'marketplace/extension')) {
				$marketplace[] = array(
					'name'	   => $this->language->get('text_extension'),
					'href'		=> '',
			'children'	=> $extension_children,
				);
			}

			if ($this->user->hasPermission('access', 'marketplace/modification')) {
				$marketplace[] = array(
					'name'	   => $this->language->get('text_modification'),
					'href'     => $this->url->link('marketplace/modification', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}


      $this->load->model('setting/extension');

      if (!in_array('universal_import', $this->model_setting_extension->getInstalled('module'))) {
        $marketplace[] = array(
					'name'	   => '<img style="vertical-align:top" src="view/universal_import/img/icon.png"/> Install Universal Import Pro',
					'href'     => $this->url->link('extension/extension/module/install', 'extension=universal_import&redir=1&user_token=' . $this->session->data['user_token'], true),
					'children' => array()		
				);
      } else if ($this->user->hasPermission('access', 'module/universal_import')) {
				$marketplace[] = array(
					'name'	   => '<img style="vertical-align:top" src="view/universal_import/img/icon.png"/> Universal Import Pro',
					'href'     => $this->url->link('module/universal_import', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()		
				);
			}
      
			if ($this->user->hasPermission('access', 'marketplace/event')) {
				$marketplace[] = array(
					'name'	   => $this->language->get('text_event'),
					'href'     => $this->url->link('marketplace/event', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}


		$lookbook = array();
		if ($this->user->hasPermission('access', 'extension/module/so_lookbook')) {
			$lookbook[] = array(
				'name'     => $this->language->get('text_so_lookbook_manage'),
				'href'     => $this->url->link('extension/module/so_lookbook', 'user_token=' . $this->session->data['user_token'], true),
				'children' => array()       
			);
		}
		if ($this->user->hasPermission('access', 'extension/module/so_lookbook_slider')) {
			$lookbook[] = array(
				'name'     => $this->language->get('text_so_lookbook_slider_manage'),
				'href'     => $this->url->link('extension/module/so_lookbook_slider', 'user_token=' . $this->session->data['user_token'], true),
				'children' => array()       
			);
		}
		if ($this->user->hasPermission('access', 'extension/module/so_lookbook_config')) {
			$lookbook[] = array(
				'name'     => $this->language->get('text_so_lookbook_config'),
				'href'     => $this->url->link('extension/module/so_lookbook_config', 'user_token=' . $this->session->data['user_token'], true),
				'children' => array()       
			);
		}
		if ($lookbook) {
			$marketplace[] = array(
				'name'     => $this->language->get('text_so_lookbook'),
				'href'     => '',
				'children' => $lookbook
			);
		}
            
            if ($this->user->hasPermission('access', 'extension/module/emailtemplate')) {
                $marketplace[] = array(
                    'name'     => $this->language->get('text_emailtemplate'),
                    'href'     => $this->url->link('extension/module/emailtemplate', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => array()
                );
            }
        

			$this->load->language('extension/jade_footer_link');

			$jade_footer = array();

			if ($this->user->hasPermission('access', 'extension/jade_footer')) {
				$jade_footer[] = array(
					'name'	   => $this->language->get('text_jade_footersetting'),
					'href'     => $this->url->link('extension/jade_footer', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'extension/jade_customfooter')) {
				$jade_footer[] = array(
					'name'	   => $this->language->get('text_jade_customfooter'),
					'href'     => $this->url->link('extension/jade_customfooter', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'extension/jade_footer_copyright')) {
				$jade_footer[] = array(
					'name'	   => $this->language->get('text_jade_footer_copyright'),
					'href'     => $this->url->link('extension/jade_footer_copyright', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'extension/jade_customfooter_newsletter')) {
				$jade_footer[] = array(
					'name'	   => $this->language->get('text_jade_footer_newsletter'),
					'href'     => $this->url->link('extension/jade_customfooter_newsletter', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($jade_footer) {
				$data['menus'][] = array(
					'id'       => 'menu-footer',
					'icon'	   => 'fa-list',
					'name'	   => $this->language->get('text_jade_footer'),
					'href'     => '',
					'children' => $jade_footer
				);
			}
				
			if ($marketplace) {
				$data['menus'][] = array(
					'id'       => 'menu-extension',
					'icon'	   => 'fa-puzzle-piece',
					'name'	   => $this->language->get('text_extension'),
					'href'     => '',
					'children' => $marketplace
				);
			}


			/**** MP checkout Starts ****/
			if(VERSION >= '2.1.0.2') {
				$ssl = true;
			} else{
				$ssl = 'SSL';
			}
			$this->language->load('mpcheckout/adminlink');
			if ($this->user->hasPermission('access', 'mpcheckout/mpcheckout')) {
				$data['menus'][] = array(
					'id'       => 'menu-extension',
					'icon'	   => 'fa-bolt', 
					'name'	   => $this->language->get('text_mpcheckout'),
					'href'     => $this->url->link('mpcheckout/mpcheckout', 'user_token=' . $this->session->data['user_token'], $ssl),
					'children' => array()
				);
			}
			/**** MP checkout Ends ****/
			

			/* Ticket System Starts */
			if(VERSION <= '2.3.0.2') {
				$module_ticket = array();
				$this->load->language('module_ticket/menu');

				if ($this->user->hasPermission('access', 'module_ticket/ticketsetting')) {
					$module_ticket[] = array(
						'name'	   => $this->language->get('text_ticketsetting'),
						'href'     => $this->url->link('module_ticket/ticketsetting', 'token=' . $this->session->data['token'], true),
						'children' => array()		
					);	
				}

				if ($this->user->hasPermission('access', 'module_ticket/ticketdepartment')) {
					$module_ticket[] = array(
						'name'	   => $this->language->get('text_ticketdepartment'),
						'href'     => $this->url->link('module_ticket/ticketdepartment', 'token=' . $this->session->data['token'], true),
						'children' => array()		
					);
				}

				if ($this->user->hasPermission('access', 'module_ticket/ticketvideo')) {
					$module_ticket[] = array(
						'name'	   => $this->language->get('text_ticketvideo'),
						'href'     => $this->url->link('module_ticket/ticketvideo', 'token=' . $this->session->data['token'], true),
						'children' => array()		
					);
				}

				if ($this->user->hasPermission('access', 'module_ticket/ticketstatus')) {
					$module_ticket[] = array(
						'name'	   => $this->language->get('text_ticketstatus'),
						'href'     => $this->url->link('module_ticket/ticketstatus', 'token=' . $this->session->data['token'], true),
						'children' => array()		
					);
				}
				
				if ($this->user->hasPermission('access', 'module_ticket/ticketrequest')) {
					$module_ticket[] = array(
						'name'	   => $this->language->get('text_ticketrequest'),
						'href'     => $this->url->link('module_ticket/ticketrequest', 'token=' . $this->session->data['token'], true),
						'children' => array()		
					);
				}

				if ($this->user->hasPermission('access', 'module_ticket/ticketuser')) {
					$module_ticket[] = array(
						'name'	   => $this->language->get('text_ticketuser'),
						'href'     => $this->url->link('module_ticket/ticketuser', 'token=' . $this->session->data['token'], true),
						'children' => array()		
					);
				}
				
				$ticketknowledge = array();
				
				if ($this->user->hasPermission('access', 'module_ticket/ticketknowledge_section')) {
					$ticketknowledge[] = array(
						'name'	   => $this->language->get('text_ticketknowledge_section'),
						'href'     => $this->url->link('module_ticket/ticketknowledge_section', 'token=' . $this->session->data['token'], true),
						'children' => array()		
					);	
				}

				if ($this->user->hasPermission('access', 'module_ticket/ticketknowledge_article')) {
					$ticketknowledge[] = array(
						'name'	   => $this->language->get('text_ticketknowledge_article'),
						'href'     => $this->url->link('module_ticket/ticketknowledge_article', 'token=' . $this->session->data['token'], true),
						'children' => array()		
					);	
				}
				
				if ($ticketknowledge) {
					$module_ticket[] = array(
						'name'	   => $this->language->get('text_ticketknowledge'),
						'href'     => '',
						'children' => $ticketknowledge		
					);
				}

				if ($module_ticket) {
					$data['menus'][] = array(
						'id'       => 'menu-module-ticket',
						'icon'	   => 'fa-life-ring', 
						'name'	   => $this->language->get('text_module_ticket'),
						'href'     => '',
						'children' => $module_ticket
					);	
				}
				} else {
					$module_ticket = array();
					$this->load->language('module_ticket/menu');

					if ($this->user->hasPermission('access', 'module_ticket/ticketsetting')) {
						$module_ticket[] = array(
							'name'	   => $this->language->get('text_ticketsetting'),
							'href'     => $this->url->link('module_ticket/ticketsetting', 'user_token=' . $this->session->data['user_token'], true),
							'children' => array()		
						);	
					}

					if ($this->user->hasPermission('access', 'module_ticket/ticketdepartment')) {
						$module_ticket[] = array(
							'name'	   => $this->language->get('text_ticketdepartment'),
							'href'     => $this->url->link('module_ticket/ticketdepartment', 'user_token=' . $this->session->data['user_token'], true),
							'children' => array()		
						);
					}

					if ($this->user->hasPermission('access', 'module_ticket/ticketvideo')) {
						$module_ticket[] = array(
							'name'	   => $this->language->get('text_ticketvideo'),
							'href'     => $this->url->link('module_ticket/ticketvideo', 'user_token=' . $this->session->data['user_token'], true),
							'children' => array()		
						);
					}

					if ($this->user->hasPermission('access', 'module_ticket/ticketstatus')) {
						$module_ticket[] = array(
							'name'	   => $this->language->get('text_ticketstatus'),
							'href'     => $this->url->link('module_ticket/ticketstatus', 'user_token=' . $this->session->data['user_token'], true),
							'children' => array()		
						);
					}
					
					if ($this->user->hasPermission('access', 'module_ticket/ticketrequest')) {
						$module_ticket[] = array(
							'name'	   => $this->language->get('text_ticketrequest'),
							'href'     => $this->url->link('module_ticket/ticketrequest', 'user_token=' . $this->session->data['user_token'], true),
							'children' => array()		
						);
					}

					if ($this->user->hasPermission('access', 'module_ticket/ticketuser')) {
						$module_ticket[] = array(
							'name'	   => $this->language->get('text_ticketuser'),
							'href'     => $this->url->link('module_ticket/ticketuser', 'user_token=' . $this->session->data['user_token'], true),
							'children' => array()		
						);
					}
					
					$ticketknowledge = array();
					
					if ($this->user->hasPermission('access', 'module_ticket/ticketknowledge_section')) {
						$ticketknowledge[] = array(
							'name'	   => $this->language->get('text_ticketknowledge_section'),
							'href'     => $this->url->link('module_ticket/ticketknowledge_section', 'user_token=' . $this->session->data['user_token'], true),
							'children' => array()		
						);	
					}

					if ($this->user->hasPermission('access', 'module_ticket/ticketknowledge_article')) {
						$ticketknowledge[] = array(
							'name'	   => $this->language->get('text_ticketknowledge_article'),
							'href'     => $this->url->link('module_ticket/ticketknowledge_article', 'user_token=' . $this->session->data['user_token'], true),
							'children' => array()		
						);	
					}
					
					if ($ticketknowledge) {
						$module_ticket[] = array(
							'name'	   => $this->language->get('text_ticketknowledge'),
							'href'     => '',
							'children' => $ticketknowledge		
						);
					}

					if ($module_ticket) {
						$data['menus'][] = array(
							'id'       => 'menu-module-ticket',
							'icon'	   => 'fa-life-ring', 
							'name'	   => $this->language->get('text_module_ticket'),
							'href'     => '',
							'children' => $module_ticket
						);	
					}
				}
			/* Ticket System Ends */

			$nav_menu = array();
			if ($this->user->hasPermission('access', 'extension/module/so_megamenu')) {
				if ($this->config->get('module_so_megamenu_status')) {
					$nav_menu[] = array(
						'name'     => $this->language->get('text_nav_menu_list'),
						'href'     => $this->url->link('extension/module/so_megamenu', 'user_token=' . $this->session->data['user_token'], true),
						'children' => array()
					);
				}
				$nav_menu[] = array(
					'name'     => $this->language->get('text_nav_menu_setting'),
					'href'     => $this->url->link('extension/module/so_megamenu', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}
			if ($nav_menu) {
				$data['menus'][] = array(
					'id'       => 'menu-image-galery',
					'icon'	   => 'fa fa-bars',
					'name'     => $this->language->get('text_nav_menu'),
					'href'     => '',
					'children' => $nav_menu
				);
			}
			
			// Design
			$design = array();

			if ($this->user->hasPermission('access', 'design/layout')) {
				$design[] = array(
					'name'	   => $this->language->get('text_layout'),
					'href'     => $this->url->link('design/layout', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'design/theme')) {
				$design[] = array(
					'name'	   => $this->language->get('text_theme'),
					'href'     => $this->url->link('design/theme', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'design/translation')) {
				$design[] = array(
					'name'	   => $this->language->get('text_language_editor'),
					'href'     => $this->url->link('design/translation', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'design/banner')) {
				$design[] = array(
					'name'	   => $this->language->get('text_banner'),
					'href'     => $this->url->link('design/banner', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'design/custom_css_file')) {
        $design[] = array(
          'name'	=> $this->language->get('text_custom_css_file'),
          'href'	=> $this->url->link('design/custom_css_file', 'user_token=' . $this->session->data['user_token'], true),
          'children'	=> array()
        );
      }
			if ($this->user->hasPermission('access', 'design/seo_url')) {
				$design[] = array(
					'name'	   => $this->language->get('text_seo_url'),
					'href'     => $this->url->link('design/seo_url', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}


			if ($this->user->hasPermission('access', 'account_dashboard/account_dashboard')) {
				$design[] = array(
					'name'	   => $this->language->get('text_account_dashboard'),
					'href'     => $this->url->link('account_dashboard/account_dashboard', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()		
				);
			}
			

	if ($this->user->hasPermission('access', 'design/theme')) {
		$file = DIR_SYSTEM . 'library/gdpr.php';
		if (is_file($file)) {
			$this->load->model('setting/setting');
			$gdpr_settings = $this->model_setting_setting->getSetting('isenselabs_gdpr', $this->config->get('config_store_id'));

			if (!empty($gdpr_settings['isenselabs_gdpr']['Enabled']) && $gdpr_settings['isenselabs_gdpr']['Enabled'] == '1' && !empty($gdpr_settings['isenselabs_gdpr']['LinkInMenu']) && $gdpr_settings['isenselabs_gdpr']['LinkInMenu']=='1') {
				$this->config->load('isenselabs/isenselabs_gdpr');
				$data['menus'][] = array(
					'id'       => 'menu-isenselabs-gdpr',
					'icon'	   => 'fa-list-alt',
					'name'	   => 'GDPR Requests',
					'href'     => $this->url->link($this->config->get('isenselabs_gdpr_path'), $this->config->get('isenselabs_gdpr_token_string').'=' . $this->session->data['user_token'], true).'#requests-tab',
					'children'  => array()
				);
			}
        	}
	}
        
			if ($design) {
				$data['menus'][] = array(
					'id'       => 'menu-design',
					'icon'	   => 'fa-television',
					'name'	   => $this->language->get('text_design'),
					'href'     => '',
					'children' => $design
				);
			}

			// Sales
			$sale = array();

			if ($this->user->hasPermission('access', 'sale/order')) {
				$sale[] = array(
					'name'	   => $this->language->get('text_order'),
					'href'     => $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'sale/recurring')) {
				$sale[] = array(
					'name'	   => $this->language->get('text_order_recurring'),
					'href'     => $this->url->link('sale/recurring', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'sale/return')) {
				$sale[] = array(
					'name'	   => $this->language->get('text_return'),
					'href'     => $this->url->link('sale/return', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			// Voucher
			$voucher = array();

			if ($this->user->hasPermission('access', 'sale/voucher')) {
				$voucher[] = array(
					'name'	   => $this->language->get('text_voucher'),
					'href'     => $this->url->link('sale/voucher', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'sale/voucher_theme')) {
				$voucher[] = array(
					'name'	   => $this->language->get('text_voucher_theme'),
					'href'     => $this->url->link('sale/voucher_theme', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($voucher) {
				$sale[] = array(
					'name'	   => $this->language->get('text_voucher'),
					'href'     => '',
					'children' => $voucher
				);
			}

			if ($sale) {
				$data['menus'][] = array(
					'id'       => 'menu-sale',
					'icon'	   => 'fa-shopping-cart',
					'name'	   => $this->language->get('text_sale'),
					'href'     => '',
					'children' => $sale
				);
			}

			// Customer
			$customer = array();

			if ($this->user->hasPermission('access', 'customer/customer')) {
				$customer[] = array(
					'name'	   => $this->language->get('text_customer'),
					'href'     => $this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'customer/customer_group')) {
				$customer[] = array(
					'name'	   => $this->language->get('text_customer_group'),
					'href'     => $this->url->link('customer/customer_group', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'customer/customer_approval')) {
				$customer[] = array(
					'name'	   => $this->language->get('text_customer_approval'),
					'href'     => $this->url->link('customer/customer_approval', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'customer/customers_denied')) {
				$customer[] = array(
					'name'	   => $this->language->get('text_customers_denied'),
					'href'     => $this->url->link('customer/customers_denied', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}
			if ($this->user->hasPermission('access', 'customer/custom_field')) {
				$customer[] = array(
					'name'	   => $this->language->get('text_custom_field'),
					'href'     => $this->url->link('customer/custom_field', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($customer) {
				$data['menus'][] = array(
					'id'       => 'menu-customer',
					'icon'	   => 'fa-user',
					'name'	   => $this->language->get('text_customer'),
					'href'     => '',
					'children' => $customer
				);
			}

			// Marketing
			$marketing = array();

			if ($this->user->hasPermission('access', 'marketing/marketing')) {
				$marketing[] = array(
					'name'	   => $this->language->get('text_marketing'),
					'href'     => $this->url->link('marketing/marketing', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'marketing/coupon')) {
				$marketing[] = array(
					'name'	   => $this->language->get('text_coupon'),
					'href'     => $this->url->link('marketing/coupon', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'marketing/contact')) {
				$marketing[] = array(
					'name'	   => $this->language->get('text_contact'),
					'href'     => $this->url->link('marketing/contact', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($marketing) {
				$data['menus'][] = array(
					'id'       => 'menu-marketing',
					'icon'	   => 'fa-share-alt',
					'name'	   => $this->language->get('text_marketing'),
					'href'     => '',
					'children' => $marketing
				);
			}

			// System
			$system = array();

			if ($this->user->hasPermission('access', 'setting/setting')) {
				$system[] = array(
					'name'	   => $this->language->get('text_setting'),
					'href'     => $this->url->link('setting/store', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			// Users
			$user = array();

			if ($this->user->hasPermission('access', 'user/user')) {
				$user[] = array(
					'name'	   => $this->language->get('text_users'),
					'href'     => $this->url->link('user/user', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'user/user_permission')) {
				$user[] = array(
					'name'	   => $this->language->get('text_user_group'),
					'href'     => $this->url->link('user/user_permission', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'user/api')) {
				$user[] = array(
					'name'	   => $this->language->get('text_api'),
					'href'     => $this->url->link('user/api', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($user) {
				$system[] = array(
					'name'	   => $this->language->get('text_users'),
					'href'     => '',
					'children' => $user
				);
			}

			// Localisation
			$localisation = array();

			if ($this->user->hasPermission('access', 'localisation/location')) {
				$localisation[] = array(
					'name'	   => $this->language->get('text_location'),
					'href'     => $this->url->link('localisation/location', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'localisation/language')) {
				$localisation[] = array(
					'name'	   => $this->language->get('text_language'),
					'href'     => $this->url->link('localisation/language', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'localisation/currency')) {
				$localisation[] = array(
					'name'	   => $this->language->get('text_currency'),
					'href'     => $this->url->link('localisation/currency', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'localisation/stock_status')) {
				$localisation[] = array(
					'name'	   => $this->language->get('text_stock_status'),
					'href'     => $this->url->link('localisation/stock_status', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'localisation/order_status')) {
				$localisation[] = array(
					'name'	   => $this->language->get('text_order_status'),
					'href'     => $this->url->link('localisation/order_status', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			// Returns
			$return = array();

			if ($this->user->hasPermission('access', 'localisation/return_status')) {
				$return[] = array(
					'name'	   => $this->language->get('text_return_status'),
					'href'     => $this->url->link('localisation/return_status', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'localisation/return_action')) {
				$return[] = array(
					'name'	   => $this->language->get('text_return_action'),
					'href'     => $this->url->link('localisation/return_action', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'localisation/return_reason')) {
				$return[] = array(
					'name'	   => $this->language->get('text_return_reason'),
					'href'     => $this->url->link('localisation/return_reason', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($return) {
				$localisation[] = array(
					'name'	   => $this->language->get('text_return'),
					'href'     => '',
					'children' => $return
				);
			}

			if ($this->user->hasPermission('access', 'localisation/country')) {
				$localisation[] = array(
					'name'	   => $this->language->get('text_country'),
					'href'     => $this->url->link('localisation/country', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'localisation/zone')) {
				$localisation[] = array(
					'name'	   => $this->language->get('text_zone'),
					'href'     => $this->url->link('localisation/zone', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'localisation/geo_zone')) {
				$localisation[] = array(
					'name'	   => $this->language->get('text_geo_zone'),
					'href'     => $this->url->link('localisation/geo_zone', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			// Tax
			$tax = array();

			if ($this->user->hasPermission('access', 'localisation/tax_class')) {
				$tax[] = array(
					'name'	   => $this->language->get('text_tax_class'),
					'href'     => $this->url->link('localisation/tax_class', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'localisation/tax_rate')) {
				$tax[] = array(
					'name'	   => $this->language->get('text_tax_rate'),
					'href'     => $this->url->link('localisation/tax_rate', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($tax) {
				$localisation[] = array(
					'name'	   => $this->language->get('text_tax'),
					'href'     => '',
					'children' => $tax
				);
			}

			if ($this->user->hasPermission('access', 'localisation/length_class')) {
				$localisation[] = array(
					'name'	   => $this->language->get('text_length_class'),
					'href'     => $this->url->link('localisation/length_class', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'localisation/weight_class')) {
				$localisation[] = array(
					'name'	   => $this->language->get('text_weight_class'),
					'href'     => $this->url->link('localisation/weight_class', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($localisation) {
				$system[] = array(
					'name'	   => $this->language->get('text_localisation'),
					'href'     => '',
					'children' => $localisation
				);
			}

			// Tools
			$maintenance = array();

			if ($this->user->hasPermission('access', 'tool/upgrade')) {
				$maintenance[] = array(
					'name'	   => $this->language->get('text_upgrade'),
					'href'     => $this->url->link('tool/upgrade', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'tool/backup')) {
				$maintenance[] = array(
					'name'	   => $this->language->get('text_backup'),
					'href'     => $this->url->link('tool/backup', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'tool/upload')) {
				$maintenance[] = array(
					'name'	   => $this->language->get('text_upload'),
					'href'     => $this->url->link('tool/upload', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'tool/log')) {
				$maintenance[] = array(
					'name'	   => $this->language->get('text_log'),
					'href'     => $this->url->link('tool/log', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($maintenance) {
				$system[] = array(
					'id'       => 'menu-maintenance',
					'icon'	   => 'fa-cog',
					'name'	   => $this->language->get('text_maintenance'),
					'href'     => '',
					'children' => $maintenance
				);
			}

			if ($system) {
				$data['menus'][] = array(
					'id'       => 'menu-system',
					'icon'	   => 'fa-cog',
					'name'	   => $this->language->get('text_system'),
					'href'     => '',
					'children' => $system
				);
			}


			$report = array();

			if ($this->user->hasPermission('access', 'report/report')) {
				$report[] = array(
					'name'	   => $this->language->get('text_reports'),
					'href'     => $this->url->link('report/report', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}


			if ($this->user->hasPermission('access', 'extension/report/inventory')) {
					$report[] = array(
						'name'	   => $this->language->get('text_report_product_inventory'),
						'href'     => $this->url->link('extension/report/inventory', 'user_token=' . $this->session->data['user_token'], true),
						'children' => array()
					);
				}
			

          if ($this->user->hasPermission('access', 'extension/module/mysql_jobs')) {
            $report[] = array(
              'name'	=> $this->language->get('text_mysql_jobs'),
              'href'	=> $this->url->link('extension/module/mysql_jobs', 'user_token=' . $this->session->data['user_token'], true),
              'children'	=> array()
            );
          }
			if ($this->user->hasPermission('access', 'report/online')) {
				$report[] = array(
					'name'	   => $this->language->get('text_online'),
					'href'     => $this->url->link('report/online', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($this->user->hasPermission('access', 'report/statistics')) {
				$report[] = array(
					'name'	   => $this->language->get('text_statistics'),
					'href'     => $this->url->link('report/statistics', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}

			if ($report) {
				$data['menus'][] = array(
					'id'       => 'menu-report',
					'icon'	   => 'fa-bar-chart',
					'name'	   => $this->language->get('text_reports'),
					'href'     => '',
					'children' => $report
				);
			}

			// Stats
			if ($this->user->hasPermission('access', 'report/statistics')) {
				$this->load->model('sale/order');

				$order_total = (float)$this->model_sale_order->getTotalOrders();

				$this->load->model('report/statistics');

				$complete_total = (float)$this->model_report_statistics->getValue('order_complete');

				if ($complete_total && $order_total) {
					$data['complete_status'] = round(($complete_total / $order_total) * 100);
				} else {
					$data['complete_status'] = 0;
				}

				$processing_total = (float)$this->model_report_statistics->getValue('order_processing');

				if ($processing_total && $order_total) {
					$data['processing_status'] = round(($processing_total / $order_total) * 100);
				} else {
					$data['processing_status'] = 0;
				}

				$other_total = (float)$this->model_report_statistics->getValue('order_other');

				if ($other_total && $order_total) {
					$data['other_status'] = round(($other_total / $order_total) * 100);
				} else {
					$data['other_status'] = 0;
				}

				$data['statistics_status'] = true;
			} else {
				$data['statistics_status'] = false;
			}

			return $this->load->view('common/column_left', $data);
		}
	}
}
