<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleiSenseLabsGdpr extends Controller {
    private $moduleName;
    private $modulePath;
    private $moduleModel;
	private $moduleVersion;
    private $extensionsLink;
    private $callModel;
    private $error  = array();
    private $data   = array();
    private $store_id;

    public function __construct($registry) {
        parent::__construct($registry);

        // Config Loader
        $this->config->load('isenselabs/isenselabs_gdpr');

        // Token
        $this->data['token_string'] = $this->config->get('isenselabs_gdpr_token_string');
        $this->data['token']        = $this->session->data[$this->data['token_string']];

        /* Fill Main Variables - Begin */
        $this->moduleName           = $this->config->get('isenselabs_gdpr_name');
        $this->callModel            = $this->config->get('isenselabs_gdpr_model');
        $this->modulePath           = $this->config->get('isenselabs_gdpr_path');
        $this->moduleVersion        = $this->config->get('isenselabs_gdpr_version');
        $this->extensionsLink       = $this->url->link($this->config->get('isenselabs_gdpr_link'), $this->data['token_string'] . '=' . $this->data['token'] . $this->config->get('isenselabs_gdpr_link_params'), 'SSL');

        /* Fill Main Variables - End */

        // Load Model
        $this->load->model($this->modulePath);

        // Model Instance
        $this->moduleModel          = $this->{$this->callModel};

        // Multi-Store
        $this->load->model('setting/store');
        // Settings
        $this->load->model('setting/setting');
        // Multi-Lingual
        $this->load->model('localisation/language');

        // Languages
        $this->language->load($this->modulePath);
		$language_strings = $this->language->load($this->modulePath);
        foreach ($language_strings as $code => $languageVariable) {
			$this->data[$code] = $languageVariable;
		}

        // Variables
        $this->data['moduleName']   = $this->moduleName;
        $this->data['modulePath']   = $this->modulePath;
		$this->data['mid']			= $this->config->get('isenselabs_gdpr_mid');

		if(version_compare(VERSION, '2.2.0.0', "<=")) {
			$this->ext = '.tpl';
		} else {
			$this->ext = '';
		}
        
        // Store Data
		if (!isset($this->request->get['store_id'])) {
            $this->request->get['store_id'] = 0;
            $this->store_id = $this->request->get['store_id'];
        } else if (isset($this->request->get['store_id'])) {
            $this->store_id = (int) $this->request->get['store_id'];
        } else {
            $this->store_id = 0;
        }
    }

	public function index() {
		$resource_prefix = '?v=' . $this->moduleVersion;
		$this->document->addStyle('view/stylesheet/'.$this->moduleName.'/'.$this->moduleName.'.css' . $resource_prefix);	
		$this->document->addScript('view/javascript/'.$this->moduleName.'/'.$this->moduleName.'.js' . $resource_prefix);
		$this->document->addScript('view/javascript/'.$this->moduleName.'/bootbox.js' . $resource_prefix);

        /* Database Checks */
        $this->moduleModel->initDb();

        // Title
		$this->document->setTitle($this->language->get('heading_title'));
        $this->data['heading_title'] = $this->data['heading_title'] . ' ' . $this->moduleVersion;

		$this->data['store'] = $this->getCurrentStore($this->store_id);

        $this->load->model('setting/store');
        $this->data['stores'] = array_merge(array(0 => $this->getCurrentStore(0)), $this->model_setting_store->getStores());

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if (!empty($this->request->post['OaXRyb1BhY2sgLSBDb21'])) {
				$this->request->post[$this->moduleName]['LicensedOn'] = $this->request->post['OaXRyb1BhY2sgLSBDb21'];
			}
			if (!empty($this->request->post['cHRpbWl6YXRpb24ef4fe'])) {
				$this->request->post[$this->moduleName]['License'] = json_decode(base64_decode($this->request->post['cHRpbWl6YXRpb24ef4fe']),true);
			}
            
			$this->model_setting_setting->editSetting($this->moduleName, $this->request->post, $this->store_id);

			$this->moduleModel->removeEventHandlers();
            if ($this->request->post[$this->moduleName]['Enabled'] == 1){
                $this->moduleModel->setupEventHandlers();
            }

			$this->session->data['success'] = $this->language->get('text_success');
			
            $this->response->redirect($this->url->link($this->modulePath, $this->data['token_string'] . '=' . $this->data['token'] . '&store_id='.$this->store_id, 'SSL'));
		}

        // Sucess & Error messages
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		// Breadcrumbs
  		$this->data['breadcrumbs'] = array();
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/dashboard', $this->data['token_string'] . '=' . $this->data['token'], 'SSL'),
      		'separator' => false
   		);
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->extensionsLink,
      		'separator' => ' :: '
   		);
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link($this->modulePath, $this->data['token_string'] . '=' . $this->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

        // Variables for the view
		$this->data['languages']                = $this->model_localisation_language->getLanguages();
		foreach ($this->data['languages'] as $key => $value) {
			if(version_compare(VERSION, '2.2.0.0', "<")) {
				$this->data['languages'][$key]['flag_url'] = 'view/image/flags/'.$this->data['languages'][$key]['image'];
			} else {
				$this->data['languages'][$key]['flag_url'] = 'language/'.$this->data['languages'][$key]['code'].'/'.$this->data['languages'][$key]['code'].'.png"';
			}
		}
		$this->data['action']					= $this->url->link($this->modulePath, $this->data['token_string'] . '=' . $this->data['token'] . '&store_id=' . $this->store_id, 'SSL');
		$this->data['cancel']					= $this->extensionsLink;
        $this->data['config_language_id']       = $this->config->get('config_language_id');
        $this->data['moduleSettings']			= $this->model_setting_setting->getSetting($this->moduleName, $this->store_id);
        $this->data['moduleData']				= (isset($this->data['moduleSettings'][$this->moduleName])) ? $this->data['moduleSettings'][$this->moduleName] : array();
		$this->data['storeId']					= $this->store_id;
				
		$module_status = array(
            'group' => $this->config->get('isenselabs_gdpr_status_group'),
            'value' => $this->config->get('isenselabs_gdpr_status_value')
        );
		
		if (!empty($this->data['moduleData']['Enabled'])) {
            $this->model_setting_setting->editSetting($module_status['group'], array($module_status['value'] => '1'));
		} else {
			$this->model_setting_setting->editSetting($module_status['group'], array($module_status['value'] => '0'));
		}

		// License data
		$this->data['licensedData']				= empty($this->data['moduleData']['LicensedOn']) ? base64_decode('ICAgIDxkaXYgY2xhc3M9ImFsZXJ0IGFsZXJ0LWRhbmdlciBmYWRlIGluIj4NCiAgICAgICAgPGJ1dHRvbiB0eXBlPSJidXR0b24iIGNsYXNzPSJjbG9zZSIgZGF0YS1kaXNtaXNzPSJhbGVydCIgYXJpYS1oaWRkZW49InRydWUiPsOXPC9idXR0b24+DQogICAgICAgIDxoND5XYXJuaW5nISBZb3UgYXJlIHJ1bm5pbmcgdW5saWNlbnNlZCB2ZXJzaW9uIG9mIHRoZSBtb2R1bGUhPC9oND4NCiAgICAgICAgPHA+WW91IGFyZSBydW5uaW5nIGFuIHVubGljZW5zZWQgdmVyc2lvbiBvZiB0aGlzIG1vZHVsZSEgWW91IG5lZWQgdG8gZW50ZXIgeW91ciBsaWNlbnNlIGNvZGUgdG8gZW5zdXJlIHByb3BlciBmdW5jdGlvbmluZywgYWNjZXNzIHRvIHN1cHBvcnQgYW5kIHVwZGF0ZXMuPC9wPjxkaXYgc3R5bGU9ImhlaWdodDo1cHg7Ij48L2Rpdj4NCiAgICAgICAgPGEgY2xhc3M9ImJ0biBidG4tZGFuZ2VyIiBocmVmPSJqYXZhc2NyaXB0OnZvaWQoMCkiIG9uY2xpY2s9IiQoJ2FbaHJlZj0jaXNlbnNlLXN1cHBvcnRdJykudHJpZ2dlcignY2xpY2snKSI+RW50ZXIgeW91ciBsaWNlbnNlIGNvZGU8L2E+DQogICAgPC9kaXY+') : '';
		$storeLicense = $this->getCurrentStore($this->store_id);
		$storeLicenseUrl = parse_url($storeLicense['url']);
		$hostname = (!empty($storeLicenseUrl['host'])) ? $storeLicenseUrl['host'] : '' ;
		$hostname = (strstr($hostname,'http://') === false) ? 'http://' . $hostname : $hostname;
		$this->data['domain']					= base64_encode($hostname);
		$this->data['domainRaw']				= $hostname;
		$this->data['timeNow']					= time();
		$this->data['licenseEncoded']			= !empty($this->data['moduleData']['License']) ? base64_encode(json_encode($this->data['moduleData']['License'])) : '';
		$this->data['supportTicketLink']		= 'http://isenselabs.com/tickets/open/' . base64_encode('Support Request') . '/' . base64_encode('472'). '/' . base64_encode($_SERVER['SERVER_NAME']);
		$this->data['licenseExpireDate'] 		= !empty($this->data['moduleData']['License']) ? date("F j, Y", strtotime($this->data['moduleData']['License']['licenseExpireDate'])) : "";

		$this->data['moduleEnabled']			= !empty($this->data['moduleData']['Enabled']) && $this->data['moduleData']['Enabled'] == 1 ? true : false;
		$this->data['ccEnabled']				= !empty($this->data['moduleData']['CC']['Enabled']) && $this->data['moduleData']['CC']['Enabled'] == 1 ? true : false;

		$this->data['data_security_breach_link']= $this->url->link('marketing/contact', $this->data['token_string'] . '=' . $this->data['token'], true);
		
		/* Dashboard checker links */
		$array_check_3 = array('[link]');
		$array_check_3_replace = array(
			'<a target="_blank" href="' . $storeLicense['url'] . 'index.php?route=' . $this->modulePath . '"><strong>' . $this->data['text_link_test'] . '</strong></a>');
		$this->data['text_gdpr_check_3_helper'] = str_replace($array_check_3, $array_check_3_replace, $this->data['text_gdpr_check_3_helper']);
		
		$array_check_4 = array('[link]', '[link2]', '[linkstart]', '[linkend]');
		$array_check_4_replace = array(
			'<a target="_blank" href="' . $this->url->link('catalog/information', $this->data['token_string'] . '=' . $this->data['token'], true) . '"><strong>' . $this->data['text_link_view'] . '</strong></a>', 
			'<a target="_blank" href="' . $this->url->link('catalog/information/add', $this->data['token_string'] . '=' . $this->data['token'], true) . '"><strong>' . $this->data['text_link_add'] . '</strong></a>', 
			'<a target="_blank" href="https://isenselabs.com/posts/gdpr-opencart"><strong>',
			'</strong></a>');
		$this->data['text_gdpr_check_4_helper'] = str_replace($array_check_4, $array_check_4_replace, $this->data['text_gdpr_check_4_helper']);
		$this->data['text_gdpr_check_5_helper'] = str_replace($array_check_4, $array_check_4_replace, $this->data['text_gdpr_check_5_helper']);
		
		$array_check_6 = array('[link]');
		$array_check_6_replace = array(
			'<a target="_blank" href="' . $storeLicense['url'] . 'index.php?route=' . $this->modulePath . '/personal_data_request"><strong>' . $this->data['text_link_view'] . '</strong></a>');
		$this->data['text_gdpr_check_6_helper'] = str_replace($array_check_6, $array_check_6_replace, $this->data['text_gdpr_check_6_helper']);
		
		$array_check_7 = array('[link]');
		$array_check_7_replace = array(
			'<a target="_blank" href="' . $storeLicense['url'] . 'index.php?route=' . $this->modulePath . '/deletion_request"><strong>' . $this->data['text_link_view'] . '</strong></a>');
		$this->data['text_gdpr_check_7_helper'] = str_replace($array_check_7, $array_check_7_replace, $this->data['text_gdpr_check_7_helper']);
		
		$array_check_8 = array('[link]');
		$array_check_8_replace = array(
			'<a target="_blank" href="' . $storeLicense['url'] . 'index.php?route=account/account"><strong>' . $this->data['text_link_view'] . '</strong></a>');
		$this->data['text_gdpr_check_8_helper'] = str_replace($array_check_8, $array_check_8_replace, $this->data['text_gdpr_check_8_helper']);
		/* Dashboard checker links */
		
		/* Deletions Counters */
		$this->data['pending_deletions'] = $this->moduleModel->getPendingDeletions($this->store_id);
		$this->data['awaiting_deletions'] = $this->moduleModel->getAwaitingDeletions($this->store_id);
        
        /* Information Pages */
        $this->load->model('catalog/information');
		$this->data['informations'] = $this->model_catalog_information->getInformations();

		// View variables
		$this->data['header']                   = $this->load->controller('common/header');
		$this->data['column_left']              = $this->load->controller('common/column_left');
		$this->data['footer']                   = $this->load->controller('common/footer');

		$this->data['moduleTabs'] 				= $this->getTabs('main');
		
		foreach ($this->data['moduleTabs'] as $key => $tab) {
			$tab['template'] = str_replace('\\', '/', $tab['template']);
			$this->data['moduleTabs'][$key]['content'] = $this->load->view($tab['template'], $this->data);
		}

		$this->response->setOutput($this->load->view($this->modulePath.'/'.$this->moduleName . $this->ext, $this->data));
	}

	private function getTabs($type = 'main') {
        $dir =
            'extension' . DIRECTORY_SEPARATOR .
            'module' . DIRECTORY_SEPARATOR .
            $this->moduleName . DIRECTORY_SEPARATOR;

        $result = array();

        switch ($type) {
            case 'main':
                $name_map = array(
                    'tab_controlpanel' => array(
                        'name' => 'Control Panel',
                        'id' => 'controlpanel-tab'
                    ),
                    'tab_cookieconsent' => array(
                        'name' => 'Cookie Consent',
                        'id' => 'cookieconsent-tab'
                    ),
                    'tab_personaldata' => array(
                        'name' => 'Third-party',
                        'id' => 'personaldata-tab'
                    ),
                    'tab_datasecurity' => array(
                        'name' => 'Data Security',
                        'id' => 'datasecurity-tab'
                    ),
                    'tab_dashboard' => array(
                        'name' => 'Dashboard',
                        'id' => 'home-tab'
                    ),
                    'tab_support' => array(
                        'name' => 'Support',
                        'id' => 'isense-support'
                    )
                );
                break;
            default:
                $name_map = array();
                break;
        }

        if (!function_exists('modification_vqmod')) {
            function modification_vqmod($file) {
                if (class_exists('VQMod')) {
                    return VQMod::modCheck(modification($file), $file);
                } else {
                    return modification($file);
                }
            }
        }

        foreach ($name_map as $file => $info) {
            $result[] = array(
                'file'     => modification_vqmod($dir . $file . '.twig'),
				'template' => modification_vqmod($dir . $file),
                'name' => $info['name'],
                'id' => $info['id']
            );
        }

        return $result;
    }


	public function get_requests() {
        $filter_url = '';

        if (!empty($this->request->get['page'])) {
            $this->data['page'] = (int) $this->request->get['page'];
        } else {
			$this->data['page'] = 1;
		}

        if (!empty($this->request->get['request_id'])) {
            $data['request_id'] = $this->request->get['request_id'];
            $filter_url .= '&request_id=' . $data['request_id'];
        } else {
            $data['request_id'] = '';
        }

		if (!empty($this->request->get['email'])) {
            $data['email'] = $this->request->get['email'];
            $filter_url .= '&email=' . $data['email'];
        } else {
            $data['email'] = '';
        }

		if (!empty($this->request->get['type'])) {
            $data['type'] = $this->request->get['type'];
            $filter_url .= '&type=' . $data['type'];
        } else {
            $data['type'] = '';
        }

		if (!empty($this->request->get['accept_language'])) {
            $data['accept_language'] = $this->request->get['accept_language'];
            $filter_url .= '&accept_language=' . $data['accept_language'];
        } else {
            $data['accept_language'] = '';
        }

		if (!empty($this->request->get['user_agent'])) {
            $data['user_agent'] = $this->request->get['user_agent'];
            $filter_url .= '&user_agent=' . $data['user_agent'];
        } else {
            $data['user_agent'] = '';
        }

		if (!empty($this->request->get['client_ip'])) {
            $data['client_ip'] = $this->request->get['client_ip'];
            $filter_url .= '&client_ip=' . $data['client_ip'];
        } else {
            $data['client_ip'] = '';
        }

		if (!empty($this->request->get['server_ip'])) {
            $data['server_ip'] = $this->request->get['server_ip'];
            $filter_url .= '&server_ip=' . $data['server_ip'];
        } else {
            $data['server_ip'] = '';
        }

		if (!empty($this->request->get['date_start'])) {
            $data['date_start'] = $this->request->get['date_start'];
            $filter_url .= '&date_start=' . $data['date_start'];
        } else {
            $data['date_start'] = '';
        }

		if (!empty($this->request->get['date_end'])) {
            $data['date_end'] = $this->request->get['date_end'];
            $filter_url .= '&date_end=' . $data['date_end'];
        } else {
            $data['date_end'] = '';
        }
        
        $data['store_id']           = $this->store_id;
        $filter_url                 .= '&store_id'. $this->store_id;

		$this->data['filter_data']	= $data;
        $this->data['limit']        = 30;

		$this->data['sources']      = $this->moduleModel->getRequests($data, $this->data['page'], $this->data['limit']);
        $this->data['total']        = $this->moduleModel->getTotalRequests($data);

        $pagination					= new Pagination();
        $pagination->total			= $this->data['total'];
        $pagination->page			= $this->data['page'];
        $pagination->limit			= $this->data['limit'];
        $pagination->url			= $this->url->link($this->modulePath.'/get_requests', $this->data['token_string'] . '=' . $this->data['token'] . '&page={page}' . $filter_url, 'SSL');

		$this->data['pagination']   = $pagination->render();

		$this->data['results']      = sprintf($this->language->get('text_pagination'), ($this->data['total']) ? (($this->data['page'] - 1) * $this->data['limit']) + 1 : 0, ((($this->data['page'] - 1) * $this->data['limit']) > ($this->data['total'] - $this->data['limit'])) ? $this->data['total'] : ((($this->data['page'] - 1) * $this->data['limit']) + $this->data['limit']), $this->data['total'], ceil($this->data['total'] / $this->data['limit']));

        $this->response->setOutput($this->load->view($this->modulePath.'/tab_requests' . $this->ext, $this->data));
    }
    
    public function export_requests() {
        if (!empty($this->request->get['request_id'])) {
            $data['request_id'] = $this->request->get['request_id'];
        } else {
            $data['request_id'] = '';
        }
		
		if (!empty($this->request->get['email'])) {
            $data['email'] = $this->request->get['email'];
        } else {
            $data['email'] = '';
        }
		
		if (!empty($this->request->get['type'])) {
            $data['type'] = $this->request->get['type'];
        } else {
            $data['type'] = '';
        }
        
		if (!empty($this->request->get['accept_language'])) {
            $data['accept_language'] = $this->request->get['accept_language'];
        } else {
            $data['accept_language'] = '';
        }	
		
		if (!empty($this->request->get['user_agent'])) {
            $data['user_agent'] = $this->request->get['user_agent'];
        } else {
            $data['user_agent'] = '';
        }
		
		if (!empty($this->request->get['client_ip'])) {
            $data['client_ip'] = $this->request->get['client_ip'];
        } else {
            $data['client_ip'] = '';
        }
		
		if (!empty($this->request->get['server_ip'])) {
            $data['server_ip'] = $this->request->get['server_ip'];
        } else {
            $data['server_ip'] = '';
        }
		
		if (!empty($this->request->get['date_start'])) {
            $data['date_start'] = $this->request->get['date_start'];
        } else {
            $data['date_start'] = '';
        }
		
		if (!empty($this->request->get['date_end'])) {
            $data['date_end'] = $this->request->get['date_end'];
        } else {
            $data['date_end'] = '';
        }
        
        $data['store_id'] = $this->store_id;
		
		$results = $this->moduleModel->getRequests($data, 1, 20, true);
        
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=gdpr_requests.csv');
        header('Pragma: no-cache');

        $fp = fopen('php://output', 'w');
        //add BOM to fix UTF-8 in Excel
        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
        // output the column headings
        fputcsv($fp, array($this->data['column_request_id'], $this->data['text_column_customer_id'], $this->data['column_email'], $this->data['column_type'], $this->data['column_user_agent'], $this->data['column_accept_language'], $this->data['column_client_ip'], $this->data['column_server_ip'], $this->data['column_date']), ';');
        foreach ($results as $field) {
            fputcsv($fp, $field, ';');
        }
        
        exit;
    }

	public function get_acceptances() {
        $filter_url = '';

        if (!empty($this->request->get['page'])) {
            $this->data['page'] = (int) $this->request->get['page'];
        } else {
			$this->data['page'] = 1;
		}

        if (!empty($this->request->get['acceptance_id'])) {
            $data['acceptance_id'] = $this->request->get['acceptance_id'];
            $filter_url .= '&acceptance_id=' . $data['acceptance_id'];
        } else {
            $data['acceptance_id'] = '';
        }

		if (!empty($this->request->get['email'])) {
            $data['email'] = $this->request->get['email'];
            $filter_url .= '&email=' . $data['email'];
        } else {
            $data['email'] = '';
        }

		if (!empty($this->request->get['name'])) {
            $data['name'] = $this->request->get['name'];
            $filter_url .= '&name=' . $data['name'];
        } else {
            $data['name'] = '';
        }

		if (!empty($this->request->get['date_start'])) {
            $data['date_start'] = $this->request->get['date_start'];
            $filter_url .= '&date_start=' . $data['date_start'];
        } else {
            $data['date_start'] = '';
        }

		if (!empty($this->request->get['date_end'])) {
            $data['date_end'] = $this->request->get['date_end'];
            $filter_url .= '&date_end=' . $data['date_end'];
        } else {
            $data['date_end'] = '';
        }
        
        $data['store_id']           = $this->store_id;
        $filter_url                 .= '&store_id'. $this->store_id;

		$this->data['filter_data']	= $data;
        $this->data['limit']        = 30;

		$this->data['sources']      = $this->moduleModel->getPolicyAcceptances($data, $this->data['page'], $this->data['limit']);
        $this->data['total']        = $this->moduleModel->getTotalPolicyAcceptances($data);

		$this->data['storeId']					= $this->store_id;
		$this->data['token_string'] = $this->config->get('isenselabs_gdpr_token_string');
        $this->data['token']        = $this->session->data[$this->data['token_string']];
		$this->data['modulePath']   = $this->modulePath;
        
        // GDPR Compliance 1.9/2.9/3.9
        $use_new_table = false;
        $check_if_gdpr_policy_table_exists = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "isense_gdpr_policies'");
        if ($check_if_gdpr_policy_table_exists->num_rows) {
            $use_new_table = true;
        }
        // GDPR Compliance 1.9/2.9/3.9

		foreach ($this->data['sources'] as &$result) {
			if ($use_new_table) {
                $result['link'] = $this->url->link('catalog/information/edit', 'information_id=' . $result['og_policy_id'] . '&' . $this->data['token_string'] . '=' . $this->data['token'], 'SSL');
            } else {
                $result['link'] = $this->url->link('catalog/information/edit', 'information_id=' . $result['policy_id'] . '&' . $this->data['token_string'] . '=' . $this->data['token'], 'SSL');
            }
			$result['download'] = $this->url->link($this->modulePath.'/get_specific_acceptance', $this->data['token_string'] . '=' . $this->data['token'] . '&acceptance_id=' . $result['acceptance_id'], 'SSL');
            
            // GDPR Compliance 1.9/2.9/3.9
            if ($use_new_table) {
                $policy_text = $this->db->query("SELECT * FROM `" . DB_PREFIX . "isense_gdpr_policies` WHERE `gdpr_policy_id`= '" . $result['policy_id'] . "' LIMIT 1");
                if ($policy_text->num_rows) {
                    $result['content'] = $policy_text->row['description'];
                }
            }
            // GDPR Compliance 1.9/2.9/3.9
			
			if (function_exists('mb_substr')) {
				$result['content'] = mb_substr(trim(strip_tags(html_entity_decode($result['content']))),0,50,'UTF-8') . '...';
			} else {
				$result['content'] = substr(trim(strip_tags(html_entity_decode($result['content']))),0,50) . '...';	
			}
		}

        $pagination					= new Pagination();
        $pagination->total			= $this->data['total'];
        $pagination->page			= $this->data['page'];
        $pagination->limit			= $this->data['limit'];
        $pagination->url			= $this->url->link($this->modulePath.'/get_acceptances', $this->data['token_string'] . '=' . $this->data['token'] . '&page={page}' . $filter_url, 'SSL');

		$this->data['pagination']   = $pagination->render();

		$this->data['results']      = sprintf($this->language->get('text_pagination'), ($this->data['total']) ? (($this->data['page'] - 1) * $this->data['limit']) + 1 : 0, ((($this->data['page'] - 1) * $this->data['limit']) > ($this->data['total'] - $this->data['limit'])) ? $this->data['total'] : ((($this->data['page'] - 1) * $this->data['limit']) + $this->data['limit']), $this->data['total'], ceil($this->data['total'] / $this->data['limit']));

        $this->response->setOutput($this->load->view($this->modulePath.'/tab_acceptances' . $this->ext, $this->data));
    }
    
    public function export_acceptances() {
        if (!empty($this->request->get['acceptance_id'])) {
            $data['acceptance_id'] = $this->request->get['acceptance_id'];
        } else {
            $data['acceptance_id'] = '';
        }
		
		if (!empty($this->request->get['email'])) {
            $data['email'] = $this->request->get['email'];
        } else {
            $data['email'] = '';
        }
		
		if (!empty($this->request->get['name'])) {
            $data['name'] = $this->request->get['name'];
        } else {
            $data['name'] = '';
        }

		if (!empty($this->request->get['date_start'])) {
            $data['date_start'] = $this->request->get['date_start'];
        } else {
            $data['date_start'] = '';
        }
		
		if (!empty($this->request->get['date_end'])) {
            $data['date_end'] = $this->request->get['date_end'];
        } else {
            $data['date_end'] = '';
        }
        
        $data['store_id'] = $this->store_id;
		
		$results = $this->moduleModel->getPolicyAcceptances($data, 1, 20, true);
		
		header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=gdpr_acceptances.csv');
        header('Pragma: no-cache');

        $fp = fopen('php://output', 'w');
        //add BOM to fix UTF-8 in Excel
        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
        // output the column headings
        fputcsv($fp, array($this->data['column_acceptance_id'], $this->data['column_email'], $this->data['text_column_policy_id'], $this->data['column_name'], $this->data['column_user_agent'], $this->data['column_accept_language'], $this->data['column_client_ip'], $this->data['column_date']), ';');
        foreach ($results as $field) {
            unset($field['content']);
            fputcsv($fp, $field, ';');
        }
        
        exit;
    }

	public function get_specific_acceptance() {
		if (!empty($this->request->get['acceptance_id'])) {
            header('Content-Type: text/html; charset=utf-8');
			$content = $this->moduleModel->getPolicyAcceptanceContent($this->request->get['acceptance_id']);
			echo $content; exit;
		} else {
			return false;
		}
	}
	
	public function get_optins() {
        $filter_url = '';
        
        if (!empty($this->request->get['page'])) {
            $this->data['page'] = (int) $this->request->get['page'];
        } else {
            $this->data['page'] = 1;    
        }
        
        if (!empty($this->request->get['optin_id'])) {
            $data['optin_id'] = $this->request->get['optin_id'];
            $filter_url .= '&optin_id=' . $data['optin_id'];
        } else {
            $data['optin_id'] = '';
        }
        
        if (!empty($this->request->get['email'])) {
            $data['email'] = $this->request->get['email'];
            $filter_url .= '&email=' . $data['email'];
        } else {
            $data['email'] = '';
        }

        if (!empty($this->request->get['type'])) {
            $data['type'] = $this->request->get['type'];
            $filter_url .= '&type=' . $data['type'];
        } else {
            $data['type'] = '';
        }

        if (!empty($this->request->get['action'])) {
            $data['action'] = $this->request->get['action'];
            $filter_url .= '&action=' . $data['action'];
        } else {
            $data['action'] = '-';
        }

        if (!empty($this->request->get['date_start'])) {
            $data['date_start'] = $this->request->get['date_start'];
            $filter_url .= '&date_start=' . $data['date_start'];
        } else {
            $data['date_start'] = '';
        }
        
        if (!empty($this->request->get['date_end'])) {
            $data['date_end'] = $this->request->get['date_end'];
            $filter_url .= '&date_end=' . $data['date_end'];
        } else {
            $data['date_end'] = '';
        }
        
        $data['store_id']           = $this->store_id;
        $filter_url                 .= '&store_id'. $this->store_id;
        
        $this->data['filter_data']  = $data;
        $this->data['limit']        = 30;
        
        $this->data['sources']      = $this->moduleModel->getOptins($data, $this->data['page'], $this->data['limit']);
        $this->data['total']        = $this->moduleModel->getTotalOptins($data);
        
        foreach ($this->data['sources'] as &$result) {
            $result['link'] = $this->url->link('catalog/information/edit', 'information_id=' . $result['policy_id'] . '&' . $this->data['token_string'] . '=' . $this->data['token'], 'SSL');
        }
        
        $pagination                 = new Pagination();
        $pagination->total          = $this->data['total'];
        $pagination->page           = $this->data['page'];
        $pagination->limit          = $this->data['limit']; 
        $pagination->url            = $this->url->link($this->modulePath.'/get_optins', $this->data['token_string'] . '=' . $this->data['token'] . '&page={page}' . $filter_url, 'SSL');

        $this->data['pagination']   = $pagination->render();

        $this->data['results']      = sprintf($this->language->get('text_pagination'), ($this->data['total']) ? (($this->data['page'] - 1) * $this->data['limit']) + 1 : 0, ((($this->data['page'] - 1) * $this->data['limit']) > ($this->data['total'] - $this->data['limit'])) ? $this->data['total'] : ((($this->data['page'] - 1) * $this->data['limit']) + $this->data['limit']), $this->data['total'], ceil($this->data['total'] / $this->data['limit']));
        
        $this->response->setOutput($this->load->view($this->modulePath.'/tab_optins' . $this->ext, $this->data));
    }
    
    public function export_optins() {
        if (!empty($this->request->get['optin_id'])) {
            $data['optin_id'] = $this->request->get['optin_id'];
        } else {
            $data['optin_id'] = '';
        }
        
        if (!empty($this->request->get['email'])) {
            $data['email'] = $this->request->get['email'];
        } else {
            $data['email'] = '';
        }

        if (!empty($this->request->get['type'])) {
            $data['type'] = $this->request->get['type'];
        } else {
            $data['type'] = '';
        }

        if (!empty($this->request->get['action'])) {
            $data['action'] = $this->request->get['action'];
        } else {
            $data['action'] = '-';
        }

        if (!empty($this->request->get['date_start'])) {
            $data['date_start'] = $this->request->get['date_start'];
        } else {
            $data['date_start'] = '';
        }
        
        if (!empty($this->request->get['date_end'])) {
            $data['date_end'] = $this->request->get['date_end'];
        } else {
            $data['date_end'] = '';
        }
        
        $data['store_id'] = $this->store_id;
        
        $results = $this->moduleModel->getOptins($data, 1, 20, true);
        
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=gdpr_optins.csv');
        header('Pragma: no-cache');

        $fp = fopen('php://output', 'w');
        //add BOM to fix UTF-8 in Excel
        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
        // output the column headings
        fputcsv($fp, array($this->data['column_optin_id'], $this->data['column_email'], $this->data['column_type'], $this->data['column_action'], $this->data['column_user_agent'], $this->data['column_accept_language'], $this->data['column_client_ip'], $this->data['column_date']), ';');
        foreach ($results as $field) {
            unset($field['policy_id']);
            fputcsv($fp, $field, ';');
        }

        exit;
    }
	
	public function get_deletions() {
        $filter_url = '';
        
        if (!empty($this->request->get['page'])) {
            $this->data['page'] = (int) $this->request->get['page'];
        } else {
            $this->data['page'] = 1;    
        }

        if (!empty($this->request->get['email'])) {
            $data['email'] = $this->request->get['email'];
            $filter_url .= '&email=' . $data['email'];
        } else {
            $data['email'] = '';
        }

        if (isset($this->request->get['status'])) {
            $data['status'] = $this->request->get['status'];
            $filter_url .= '&status=' . $data['status'];
        } else {
            $data['status'] = '-';
        }
		
		if (!empty($this->request->get['date_deletion'])) {
            $data['date_deletion'] = $this->request->get['date_deletion'];
            $filter_url .= '&date_deletion=' . $data['date_deletion'];
        } else {
            $data['date_deletion'] = '';
        }

        if (!empty($this->request->get['date_start'])) {
            $data['date_start'] = $this->request->get['date_start'];
            $filter_url .= '&date_start=' . $data['date_start'];
        } else {
            $data['date_start'] = '';
        }
        
        if (!empty($this->request->get['date_end'])) {
            $data['date_end'] = $this->request->get['date_end'];
            $filter_url .= '&date_end=' . $data['date_end'];
        } else {
            $data['date_end'] = '';
        }
        
        $data['store_id']           = $this->store_id;
        $filter_url                 .= '&store_id'. $this->store_id;
        
        $this->data['filter_data']  = $data;
        $this->data['limit']        = 30;
        
        $this->data['sources']      = $this->moduleModel->getDeletions($data, $this->data['page'], $this->data['limit']);
        $this->data['total']        = $this->moduleModel->getTotalDeletions($data);
        
        foreach ($this->data['sources'] as &$result) {
			$result['status_code'] = $result['status'];
            switch ($result['status']) {
				case 0:
					$result['status'] = $this->data['text_pending_status'];
					break;
				case 1;
					$result['status'] = $this->data['text_cancelled_status'];
					break;
				case 2:
					$result['status'] = $this->data['text_awaiting_deletion_status'];
					break;
				case 3: 
					$result['status'] = $this->data['text_deleted_status'];
					break;
				default:
					$result['status'] = $this->data['text_pending_status'];
					break;
			}
			
			$result['customer_data'] = $result['customer_data'] ? $this->data['text_yes'] : $this->data['text_no'];
			$result['address_data'] = $result['address_data'] ? $this->data['text_yes'] : $this->data['text_no'];
			$result['gdpr_data'] = $result['gdpr_data'] ? $this->data['text_yes'] : $this->data['text_no'];
			$result['order_data'] = $result['order_data'] ? $this->data['text_yes'] : $this->data['text_no'];
        }
        
        $pagination                 = new Pagination();
        $pagination->total          = $this->data['total'];
        $pagination->page           = $this->data['page'];
        $pagination->limit          = $this->data['limit']; 
        $pagination->url            = $this->url->link($this->modulePath.'/get_deletions', $this->data['token_string'] . '=' . $this->data['token'] . '&page={page}' . $filter_url, 'SSL');

        $this->data['pagination']   = $pagination->render();

        $this->data['results']      = sprintf($this->language->get('text_pagination'), ($this->data['total']) ? (($this->data['page'] - 1) * $this->data['limit']) + 1 : 0, ((($this->data['page'] - 1) * $this->data['limit']) > ($this->data['total'] - $this->data['limit'])) ? $this->data['total'] : ((($this->data['page'] - 1) * $this->data['limit']) + $this->data['limit']), $this->data['total'], ceil($this->data['total'] / $this->data['limit']));
        
        $this->response->setOutput($this->load->view($this->modulePath.'/tab_deletions' . $this->ext, $this->data));
    }
	
	public function deny_deletion() {
		if (!empty($this->request->get['deletion_id'])) {
			$this->data['deletion_id'] = $this->request->get['deletion_id'];
		} else {
			$this->data['deletion_id'] = 0;
		}
		
		$this->data['deny_form'] = false;
		$deletion_data = $this->moduleModel->getDeletion($this->data['deletion_id']);
		if (!empty($deletion_data)) {
			$this->data['deny_form'] = true;
			$this->data['deletion_data'] = $deletion_data;
			$this->data['language_data'] = $this->model_localisation_language->getLanguage($deletion_data['language_id']);
			$this->data['store_data'] = $this->getCurrentStore($deletion_data['store_id']);
		}
		
		$this->response->setOutput($this->load->view($this->modulePath.'/deletion_deny' . $this->ext, $this->data));	
	}
	
	public function deny_deletion_action() {
		$json = array();
        
		if (!empty($this->request->post['deny_request_text'])) {
            $message = $this->request->post['deny_request_text'];
        } else {
            $message = '';
        }
        
 		if (!empty($this->request->post['deletion_id'])) {
            $deletion_id = (int) $this->request->post['deletion_id'];
        } else {
            $deletion_id = 0;
        }
		
		if ($deletion_id && !empty($message)) {
			$result 	= $this->moduleModel->updateDeletionStatus($deletion_id, 1, $message);
			$sendEmail 	= $this->moduleModel->sendDenyDeletionEmail($deletion_id);
		} else {
			$result = false;
		}
        
        if ($result) {
            $json['success'] = true;
        } else {
            $json['error'] = true;
        }
        
        echo json_encode($json);
        exit;
	}
	
	public function approve_deletion() {
		if (!empty($this->request->get['deletion_id'])) {
			$this->data['deletion_id'] = $this->request->get['deletion_id'];
		} else {
			$this->data['deletion_id'] = 0;
		}		
		
		$this->data['approve_form'] = false;
		$deletion_data = $this->moduleModel->getDeletion($this->data['deletion_id']);
		if (!empty($deletion_data)) {
			$this->data['approve_form'] = true;
			$this->data['deletion_data'] = $deletion_data;
			$this->data['language_data'] = $this->model_localisation_language->getLanguage($deletion_data['language_id']);
			$this->data['store_data'] = $this->getCurrentStore($deletion_data['store_id']);
		}
		
		$this->response->setOutput($this->load->view($this->modulePath.'/deletion_approve' . $this->ext, $this->data));	
	}
	
	public function approve_deletion_action() {
		$json = array();

 		if (!empty($this->request->post['deletion_id'])) {
            $deletion_id = (int) $this->request->post['deletion_id'];
        } else {
            $deletion_id = 0;
        }
		
		if ($deletion_id) {
			$result 	= $this->moduleModel->approveDeletion($this->request->post);
			$sendEmail 	= $this->moduleModel->sendApproveDeletionEmail($deletion_id);
		} else {
			$result = false;
		}				
        
        if ($result) {
            $json['success'] = true;
        } else {
            $json['error'] = true;
        }
        
        echo json_encode($json);
        exit;
	}
	
	public function actual_deletion() {
		$json = array();

 		if (!empty($this->request->post['deletion_id'])) {
            $deletion_id = (int) $this->request->post['deletion_id'];
        } else {
            $deletion_id = 0;
        }
		
		if ($deletion_id) {
			$result 	= $this->moduleModel->anonymizeData($deletion_id);
			$sendEmail 	= $this->moduleModel->sendSuccessfulDeletionEmail($deletion_id);
			$sendEmail 	= $this->moduleModel->updateDeletionStatus($deletion_id, 3);
		} else {
			$result = false;
		}				
        
        if ($result) {
            $json['success'] = true;
        } else {
            $json['error'] = true;
        }
        
        echo json_encode($json);
        exit;
	}

	public function deleteGuestAcceptances() {
		$json = array();
		if ($this->validate()) {
			$result 	= $this->moduleModel->deleteGuestAcceptances();
			if ($result) {
				$json['success'] = true;
				$json['success_message'] = $this->language->get('text_guest_delete_success');
			} else {
				$json['error'] = true;
				$json['error_message'] = $this->language->get('error_guest_delete');
			}

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		} else {
			$json['error'] = true;
            $json['error_message'] = $this->error['warning'];
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}

    public function install() {
        /* Database Checks */
        $this->moduleModel->initDb();
    }

	public function uninstall() {
        $this->moduleModel->removeEventHandlers();
    }

	protected function validate() {
		if (!$this->user->hasPermission('modify', $this->modulePath)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

    // Get current store
	private function getCurrentStore($store_id) {
        $this->load->model("setting/store");

        if($store_id && $store_id != 0) {
            $store = $this->model_setting_store->getStore($store_id);
        } else {
            $store['store_id'] = 0;
            $store['name'] = $this->config->get('config_name');
            $store['url'] = HTTP_CATALOG;
            $store['ssl'] = HTTPS_CATALOG;
        }
        return $store;
    }
}
