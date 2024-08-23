<?php
/* This file is under Git Control by KDSI. */
class ControllerCommonHeader extends Controller {
	public function index() {

        $file = DIR_SYSTEM . 'library/gdpr.php';
		$data['cookie_consent_bar'] = '';
		if (is_file($file)) {
			$this->config->load('isenselabs/isenselabs_gdpr');
			$gdpr_name = $this->config->get('isenselabs_gdpr_name');

			$this->load->model('setting/setting');
			$moduleSettings = $this->model_setting_setting->getSetting($gdpr_name, $this->config->get('config_store_id'));
			$gdprData = !empty($moduleSettings[$gdpr_name]) ? $moduleSettings[$gdpr_name] : array();

			if (!empty($gdprData['Enabled']) && $gdprData['Enabled']) {

			  $this->document->addScript('catalog/view/javascript/'. $gdpr_name . '/utils.js');

			  if (!empty($gdprData['CC']['Enabled']) && $gdprData['CC']['Enabled']) {
				if (defined('JOURNAL3_ACTIVE')) {
					$this->document->addScript('catalog/view/javascript/'. $gdpr_name . '/journal3/cookiemanager.js');
					$this->document->addScript('catalog/view/javascript/'. $gdpr_name . '/journal3/cookieconsent.min.js');
				} else {
					$this->document->addScript('catalog/view/javascript/'. $gdpr_name . '/cookiemanager.js');
					$this->document->addScript('catalog/view/javascript/'. $gdpr_name . '/cookieconsent.min.js');
				}
				$this->document->addStyle('catalog/view/javascript/' . $gdpr_name . '/cookieconsent.min.css');
				$data['cookie_consent_bar'] = true;
				$data['gdprModulePath'] = $this->config->get('isenselabs_gdpr_path');
			  }
			}
        }
        

        $this->load->controller('extension/module/sweetalert/inject_scripts');
            

            $login_required = $this->config->get("module_kdsi_login_required");
            if ($login_required['status_store']) {
                $redirect = true;
                if (isset($this->request->get['route'])) {
                    $route = $this->request->get['route'];
                } else {
                    $route = 'common/home';
                }
                $safe_route = array(
                    'account/register', 
                    'account/login',
                    'account/forgotten',
                );
                if ($this->customer->isLogged()) {
                    $redirect = false;
                } elseif (isset($this->request->get["route"])) {
                    if(in_array($route, $safe_route)) {
                        $redirect = false;
                    }
                }
                if ($redirect) {
                    $this->session->data['redirect'] = $this->url->link('common/home', '', true);
                    $this->response->redirect($this->url->link('account/login', '', true));
                }
            }
            
		
		$approval_required = $this->config->get("module_approval_required");
		$approved = (int)$this->customer->getApproved();
		if ($approval_required['status_store']) {
			$redirect = true;
			if (isset($this->request->get['route'])) {
				$route = $this->request->get['route'];
			} else {
				$route = 'common/home';
			}
			$safe_route = array(
				'account/register', 
				'account/login',
				'account/forgotten',
				'account/account', 
				'account/edit'
			);
			if ($approved == 1) {
				$redirect = false;
			} elseif (isset($this->request->get["route"])) {
				if(in_array($route, $safe_route)) {
					$redirect = false;
				}
			}
			if ($redirect) {
				$this->session->data['redirect'] = $this->url->link('common/home', '', true);
				$this->response->redirect($this->url->link('account/edit', '', true));
			}
		}
			
		// Analytics
		$this->load->model('setting/extension');

		$data['analytics'] = array();

		$analytics = $this->model_setting_extension->getExtensions('analytics');

		foreach ($analytics as $analytic) {
			if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
				$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
			}
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}


			/*Ticket system starts*/
			$this->load->language('support/menu_ticket');
			
			$data['menu_support'] = $this->language->get('menu_support');
			
			$data['mpsupport'] = $this->url->link('support/support', '', true);

			$data['ticketsetting_status'] = $this->config->get('ticketsetting_status');
			/*Ticket system ends*/
			
		$data['title'] = $this->document->getTitle();

		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();

		$data['mpmetas'] = $this->load->controller('extension/mpblog/meta');
		$data['gallerympmetas'] = $this->document->getgalleryMpMeta();
			
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts('header');
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$data['top_level_domain'] = $_SERVER['HTTP_HOST'];
		$arrSearch = array(
			'{{ top_level_domain }}',
			'{{ base }}',
			'{{ title }}',
			'{{ description }}'
			);
		$arrReplace = array(
			$data['top_level_domain'],
			$data['base'],
			$data['title'],
			$data['description']
			);

		$config_meta_twitter = str_replace($arrSearch, $arrReplace, $this->config->get('config_meta_twitter'));
		$config_meta_opengraph = str_replace($arrSearch, $arrReplace, $this->config->get('config_meta_opengraph'));
		$data['meta_twitter'] = html_entity_decode($config_meta_twitter, ENT_QUOTES, 'UTF-8');
		$data['meta_opengraph'] = html_entity_decode($config_meta_opengraph, ENT_QUOTES, 'UTF-8');
		$data['favicons'] = html_entity_decode($this->config->get('config_favicons'), ENT_QUOTES, 'UTF-8');

		$data['name'] = $this->config->get('config_name');

		$data['mytemplate'] = $this->config->get('theme_default_directory');
		$data['theme_default_header_top_account'] = $this->config->get('theme_default_header_top_account');
			

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$this->load->language('common/header');

		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
		} else {
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}

		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));
		

		$data['firstname'] = $this->customer->getFirstName();
		$data['lastname'] = $this->customer->getLastName();
			
		$data['home'] = $this->url->link('common/home');

			$data['topbar_message'] = html_entity_decode($this->config->get('config_topbar_message'), ENT_QUOTES, 'UTF-8');
			if ($this->config->get('config_topbar')) {
				$data['topbar'] = $this->config->get('config_topbar');
			} else {
				$data['topbar'] = false;
			}
			
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['logged'] = $this->customer->isLogged();
		$data['account'] = $this->url->link('account/account', '', true);
		$data['register'] = $this->url->link('account/register', '', true);
		$data['login'] = $this->url->link('account/login', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['transaction'] = $this->url->link('account/transaction', '', true);
		$data['download'] = $this->url->link('account/download', '', true);
		$data['logout'] = $this->url->link('account/logout', '', true);
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);
		$data['contact'] = $this->url->link('information/contact');
		$data['telephone'] = $this->config->get('config_telephone');
		
		$data['language'] = $this->load->controller('common/language');
		$data['currency'] = $this->load->controller('common/currency');
		$data['search'] = $this->load->controller('common/search');
		$data['cart'] = $this->load->controller('common/cart');
		$data['menu'] = $this->load->controller('common/menu');
$data['megamenu'] = $this->load->controller('extension/module/so_megamenu');

		$data['header_left'] = $this->load->controller('common/header_left');
		$data['header_top'] = $this->load->controller('common/header_top');
		$data['header_top_left'] = $this->load->controller('common/header_top_left');
		$data['text_blog'] = $this->language->get('text_blog');
		$data['all_blogs'] = $this->url->link('information/blogger/blogs');
		$data['compare'] = $this->url->link('product/compare', '', true);

		
		// For page specific css
		if (isset($this->request->get['route'])) {
			if (isset($this->request->get['product_id'])) {
				$class = '-' . $this->request->get['product_id'];
			} elseif (isset($this->request->get['path'])) {
				$class = '-' . $this->request->get['path'];
			} elseif (isset($this->request->get['manufacturer_id'])) {
				$class = '-' . $this->request->get['manufacturer_id'];
			} elseif (isset($this->request->get['information_id'])) {
				$class = '-' . $this->request->get['information_id'];
			} else {
				$class = '';
			}

			$data['class'] = str_replace('/', '-', $this->request->get['route']) . $class;
		} else {
			$data['class'] = 'common-home';
		}
		
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
			


			$data['at_css'] = html_entity_decode($this->config->get('config_at_custom_css') ? $this->config->get('config_at_custom_css') : '', ENT_QUOTES, 'UTF-8');
			$data['at_custom_head'] = html_entity_decode($this->config->get('config_at_custom_head') ? $this->config->get('config_at_custom_head') : '', ENT_QUOTES, 'UTF-8');
			
		return $this->load->view('common/header', $data);
	}
}
