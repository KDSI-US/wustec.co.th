<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleSweetalert extends Controller {
    
	private $error = array();
    private $module_code = "sweetalert";

	public function install() {

        $defaultSettings = array(
            "sweetalert_cfg_auto_close_seconds"         => "",
            "sweetalert_cfg_allow_outside_click"        => "false",
            "sweetalert_cfg_apply_on_product_page_only" => "false",
            "sweetalert_cfg_log"                        => true,
            "sweetalert_cfg_extension_status"           => true,
        );
	    
		$this->load->model('setting/setting');
		$this->model_setting_setting->editSetting($this->module_code, $defaultSettings);
        $this->model_setting_setting->editSetting('module_' . $this->module_code, array('module_' . $this->module_code . '_status' => true));

        // 		$this->load->model('extension/module/sweetalert');
// 		$this->model_extension_module_sweetalert->install();
		
	}
	
	public function index() {
		
	    $module_route = 'extension/module/sweetalert';
        $modules_route = 'marketplace/extension';
	    
		
		$this->load->language('extension/module/sweetalert');

		
		$this->document->setTitle($this->language->get('heading_title'));
		
		//Load the settings model. You can also add any other models you want to load here.
		$this->load->model('setting/setting');
		
		//Save the settings if the user has submitted the admin form (ie if someone has pressed save).
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
		    
			$this->model_setting_setting->editSetting($this->module_code, $this->request->post);

            //Let the extension show as enabled/disabled in the module list
            $this->model_setting_setting->editSetting('module_' . $this->module_code, array('module_' . $this->module_code . '_status' => (bool)$this->request->post['sweetalert_cfg_extension_status']));

			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->response->redirect($this->url->link($modules_route, 'user_token=' . $this->session->data['user_token'] . '&type=module', 'SSL'));
		}

		$text_strings = array(
			'heading_title',
			'text_enabled',
			'text_disabled',
			'text_home',
	        'text_yes',
	        'text_no',
			'button_save',
			'button_cancel'
		);
		
		foreach ($text_strings as $text) {
			$data[$text] = $this->language->get($text);
		}
		
 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}


		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		$data['breadcrumbs'][] = array(
	        'href'      => $this->url->link($modules_route, 'user_token=' . $this->session->data['user_token'] . '&type=module', 'SSL'),
	        'text'      => $this->language->get('text_module'),
	        'separator' => ' :: '
   		);
   		 
   		$data['breadcrumbs'][] = array(
	        'href'      => $this->url->link($module_route, 'user_token=' . $this->session->data['user_token'] . '&type=module', 'SSL'),
	        'text'      => $this->language->get('heading_title'),
	        'separator' => ' :: '
   		);
   		 
   		
   		$data['action'] = $this->url->link($module_route, 'user_token=' . $this->session->data['user_token'] . '&type=module', 'SSL');
   		 
   		$data['cancel'] = $this->url->link($modules_route, 'user_token=' . $this->session->data['user_token'] . '&type=module', 'SSL');
   		 
		
		$config_data = array(
            "sweetalert_cfg_auto_close_seconds",
            "sweetalert_cfg_allow_outside_click",
            "sweetalert_cfg_apply_on_product_page_only",
	        "sweetalert_cfg_log",
            "sweetalert_cfg_extension_status"
		);
		
		foreach ($config_data as $conf) {
		    if (isset($this->request->post[$conf])) {
		        $data[$conf] = $this->request->post[$conf];
		    } else {
		        $data[$conf] = $this->config->get($conf);
		    }
		}
		

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('extension/module/sweetalert', $data));
	}
	
}