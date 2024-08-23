<?php
class ControllerExtensionModuleApprovalRequired extends Controller {
	
	private $error = array();

	public function index() {
		$this->load->language('extension/module/approval_required');

		$this->document->setTitle($this->language->get('title'));

		$this->load->model('setting/setting');
        $this->load->model('setting/store');
        
        if(!isset($this->request->get['store_id'])) {
            $data['get_store_id'] = 0;
        } else {
            $data['get_store_id'] = $this->request->get['store_id'];
        }
        
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            
            $this->model_setting_setting->editSetting('module_approval_required', $this->request->post, $data['get_store_id']);
			
            $this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('title'),
			'href' => $this->url->link('extension/module/approval_required', 'user_token=' . $this->session->data['user_token'], true)
		);
        
        $data['action'] = $this->url->link('extension/module/approval_required', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $data['get_store_id'], true);
		
        $data['change_action'] = $this->url->link('extension/module/approval_required', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
        
        if (isset($data['get_store_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$store_info = $this->model_setting_setting->getSetting('module_approval_required', $data['get_store_id']);
		}

		if (isset($this->request->post['module_approval_required'])) {
			$data['module_approval_required'] = $this->request->post['module_approval_required'];
		} else {
			$data['module_approval_required'] = @$store_info['module_approval_required'];
		}
    
        
        $data['stores'] = array_merge( array(0 => array('store_id' => '0', 'name' => $this->config->get('config_name') . ' (' . $this->language->get('text_default').')', 'url' => HTTP_SERVER,  'ssl' => HTTPS_SERVER) ),  $this->model_setting_store->getStores() );

        $data['user_token'] = $this->session->data['user_token'];
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/approval_required', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/approval_required')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}