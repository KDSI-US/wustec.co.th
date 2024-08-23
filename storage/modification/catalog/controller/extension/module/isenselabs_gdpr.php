<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleiSenselabsGdpr extends Controller {
    private $moduleName;
    private $modulePath;
    private $moduleModel;
    private $callModel;
    private $error  = array(); 
    private $data   = array();
  	private $storeId;
    private $moduleData = array(); 	
    
    public function __construct($registry) {
        parent::__construct($registry);
        
        // Config Loader
        $this->config->load('isenselabs/isenselabs_gdpr');
        
        /* Fill Main Variables - Begin */
        $this->moduleName           = $this->config->get('isenselabs_gdpr_name');
        $this->callModel            = $this->config->get('isenselabs_gdpr_model');
        $this->modulePath           = $this->config->get('isenselabs_gdpr_path');
        /* Fill Main Variables - End */
        
        // Load Model
        $this->load->model($this->modulePath);
        $this->load->model('setting/setting');
        
        // Model Instance
        $this->moduleModel          = $this->{$this->callModel};

        // Languages
        $this->language->load($this->modulePath);
		$language_strings = $this->language->load($this->modulePath);
        foreach ($language_strings as $code => $languageVariable) {
			$this->data[$code] = $languageVariable;
		}
		
		$this->data['breadcrumbs'] = array();
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_gdpr'),
			'href' => $this->url->link($this->modulePath, '', true)
		);
        
        // Variables
        $this->data['modulePath']   = $this->modulePath;
		
		if(version_compare(VERSION, '2.2.0.0', "<=")) {
			$this->ext = '.tpl';
		} else {
			$this->ext = '';
		}
        
        $this->storeId = $this->config->get('config_store_id');
        $moduleSettings = $this->model_setting_setting->getSetting($this->moduleName, $this->storeId);
		$this->moduleData = !empty($moduleSettings[$this->moduleName]) ? $moduleSettings[$this->moduleName] : array();

		if ($this->moduleData['UseCaptcha'] == 'yes' && (!$this->moduleData['SiteKey'] || !$this->moduleData['SecretKey'])) {
			$this->moduleData['UseCaptcha'] == 'no';
			$this->log->write('iSenseLabs GDPR ::: Disabled the Google ReCaptcha. Please check the SiteKey and SecretKey input!');
		}
    }
	
	public function index() {
		$this->document->setTitle($this->language->get('text_gdpr'));
		
		$this->data['heading_title'] = $this->language->get('heading_title');
	
		$this->data['edit'] = $this->url->link('account/edit', '', true);
		$this->data['password'] = $this->url->link('account/password', '', true);
		$this->data['address'] = $this->url->link('account/address', '', true);
		$this->data['newsletter'] = $this->url->link('account/newsletter', '', true);
		
		$this->data['download_gdpr_requests'] = $this->url->link($this->modulePath.'/download_gdpr_requests', '', true);
		$this->data['download_personal_info'] = $this->url->link($this->modulePath.'/download_personal_info', '', true);
		$this->data['download_addresses'] = $this->url->link($this->modulePath.'/download_addresses', '', true);
		$this->data['download_orders'] = $this->url->link($this->modulePath.'/download_orders', '', true);
		
		$this->data['personal_data_request'] = $this->url->link($this->modulePath.'/personal_data_request', '', true);
		$this->data['deletion_request'] = $this->url->link($this->modulePath.'/deletion_request', '', true);
		
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['column_right'] = $this->load->controller('common/column_right');
		$this->data['content_top'] = $this->load->controller('common/content_top');
		$this->data['content_bottom'] = $this->load->controller('common/content_bottom');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		
		$this->response->setOutput($this->load->view($this->modulePath.'/index' . $this->ext, $this->data));
	}
	
	public function download_gdpr_requests() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link($this->modulePath, '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}
		
		// @TODO : This should be changed
		$email = $this->customer->getEmail();
		
		$results = $this->moduleModel->getGdprRequests($email);
		
		if (!empty($results)) {
			$this->load->library('gdpr');
			$this->gdpr->newRequest('Downloaded GDPR Requests', $email);
			
			header('Content-Type: application/csv');
			header('Content-Disposition: attachment; filename=gdpr_requests.csv');
			header('Pragma: no-cache');
			
			$fp = fopen('php://output', 'w');
			//add BOM to fix UTF-8 in Excel
			fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
			// output the column headings
			fputcsv($fp, array('Request ID', 'Customer ID', 'Email', 'Type', 'User Agent', 'Accept Language', 'Client IP', 'Server IP', 'Request Added'), ';');
			foreach ($results as $field) {
				fputcsv($fp, $field, ';');
			}
		} else {
			$this->response->redirect($this->url->link($this->modulePath, '', true));
		}
	}
	
	public function download_personal_info() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link($this->modulePath, '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}
		
		// @TODO : This should be changed
		$email = $this->customer->getEmail();
		
		$results = $this->moduleModel->getCustomerPersonalInfo($email);

		if (!empty($results)) {
			$this->load->library('gdpr');
			$this->gdpr->newRequest('Downloaded Personal Info', $email);
			
			header('Content-Type: application/csv');
			header('Content-Disposition: attachment; filename=gdpr_personal_info.csv');
			header('Pragma: no-cache');
			
			$fp = fopen('php://output', 'w');
			//add BOM to fix UTF-8 in Excel
			fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
			// output the column headings
			fputcsv($fp, array('Customer ID', 'First Name', 'Last Name', 'Email', 'Telephone', 'Fax', 'Newsletter Subscription', 'IP', 'Date Registered'), ';');
			foreach ($results as $field) {
				fputcsv($fp, $field, ';');
			}
		} else {
			$this->response->redirect($this->url->link($this->modulePath, '', true));
		}
	}
	
	public function download_addresses() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link($this->modulePath, '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}
		
		// @TODO : This should be changed
		$email = $this->customer->getEmail();
		
		$results = $this->moduleModel->getCustomerAddresses($email);

		if (!empty($results)) {
			$this->load->library('gdpr');
			$this->gdpr->newRequest('Downloaded Addresses', $email);
			
			header('Content-Type: application/csv');
			header('Content-Disposition: attachment; filename=gdpr_addresses.csv');
			header('Pragma: no-cache');

			$fp = fopen('php://output', 'w');
			//add BOM to fix UTF-8 in Excel
			fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
			// output the column headings
			fputcsv($fp, array('First Name', 'Last Name', 'Company', 'Address 1', 'Address 2', 'City', 'Postcode', 'Country', 'Zone'), ';');
			foreach ($results as $field) {
				fputcsv($fp, $field, ';');
			}
		} else {
			$this->response->redirect($this->url->link($this->modulePath, '', true));
		}
	}
	
	public function download_orders() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link($this->modulePath, '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}
		
		// @TODO : This should be changed
		$email = $this->customer->getEmail();
		
		$results = $this->moduleModel->getCustomerOrders($email);

		if (!empty($results)) {
			$this->load->library('gdpr');
			$this->gdpr->newRequest('Downloaded Orders', $email);
			
			header('Content-Type: application/csv');
			header('Content-Disposition: attachment; filename=gdpr_orders.csv');
			header('Pragma: no-cache');

			$fp = fopen('php://output', 'w');
			//add BOM to fix UTF-8 in Excel
			fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
			// output the column headings
			fputcsv($fp, array('Order ID', 'Customer ID', 'First Name', 'Last Name', 'Email', 'Telephone', 'Fax', 'Payment First Name', 'Payment Last Name', 'Payment Company', 'Payment Address 1', 'Payment Address 2', 'Payment City', 'Payment Postcode', 'Payment Country', 'Payment Zone', 'Payment Method', 'Shipping First Name', 'Shipping Last Name', 'Shipping Company', 'Shipping Address 1', 'Shipping Address 2', 'Shipping City', 'Shipping Postcode', 'Shipping Country', 'Shipping Zone', 'Shipping Method', 'Products', 'Total', 'Order Status', 'Comment', 'Currency Code', 'IP', 'User Agent', 'Accept Language', 'Date Added', 'Date Modified'), ';');
			
			foreach ($results as $field) {
				fputcsv($fp, $field, ';');
			}
		} else {
			$this->response->redirect($this->url->link($this->modulePath, '', true));
		}
	}
	
	public function personal_data_request() {
		$this->document->setTitle($this->language->get('text_access_to_personal_data'));

		if (isset($this->moduleData['UseCaptcha']) && $this->moduleData['UseCaptcha']=='yes') {
			$this->document->addScript('https://www.google.com/recaptcha/api.js');
		}
		
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_access_to_personal_data'),
			'href' => $this->url->link($this->modulePath.'/personal_data_request', '', true)
		);
		
		if (isset($this->request->post['email'])) {
			$this->data['email'] = $this->request->post['email'];
		} else {
			$this->data['email'] = '';
		}

		if (isset($this->moduleData['UseCaptcha']) && $this->moduleData['UseCaptcha']=='yes') {
			$this->data['site_key'] = $this->moduleData['SiteKey'];
		} else {
			$this->data['site_key'] = '';
		}
		
		$this->data['UseCaptcha'] = isset($this->moduleData['UseCaptcha']) ? $this->moduleData['UseCaptcha'] : 'no';
		$this->data['error'] = '';
		
		$active_hours = !empty($this->moduleData['PersonalDataLinkLife']) ? (int)$this->moduleData['PersonalDataLinkLife'] : 5;

		if (($this->request->server['REQUEST_METHOD'] == 'POST' && !empty($this->data['email']))) {
			$checker = $this->moduleModel->checkIfEmailExists($this->data['email'], true);
			$this->data['success'] = true;
			
			if (isset($this->request->post['g-recaptcha-response'])) {
				$recaptcha = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($this->moduleData['SecretKey']) . '&response=' . $this->request->post['g-recaptcha-response'] . '&remoteip=' . $this->request->server['REMOTE_ADDR']);
	
				$recaptcha = json_decode($recaptcha, true);
				
				if (!$recaptcha['success']) {
					$this->data['error'] = $this->language->get('GDPR_captcha');
					$this->data['success'] = '';
				}
			}

			if ($checker) {
				if (($this->moduleData['UseCaptcha']=='no') || (isset($recaptcha) && !empty($recaptcha['success'])) ) {
					$submission_data = $this->moduleModel->insertSubmission($this->data['email'], 'personal_data_request');
					
					if (!empty($submission_data) && is_array($submission_data)) {
						$submission_data['active_hours'] = $active_hours;
						$mail_result = $this->moduleModel->sendPersonalDataMail($submission_data);
						$this->load->library('gdpr');
						$this->gdpr->newRequest('Personal Data Request', $this->data['email']);
					}

					$this->data['success'] = true;
				} else {
					$this->data['success'] = '';
				}
			}
		}
		
		$this->data['heading_title'] = $this->data['text_access_to_personal_data'];
		$this->data['heading_title_helper'] = $this->data['text_personal_data_helper'];
		$this->data['how_this_works'] = $this->data['text_how_this_works_data_request'];
		$this->data['how_this_works_helper'] = str_replace('%s%', $active_hours, $this->data['text_enter_your_email_data_request_helper']);
		$this->data['text_successful_request_helper'] = str_replace('%s%', $active_hours, $this->data['text_successful_data_request_helper']);
		
		$this->data['continue'] = $this->url->link('common/home', '', true);
		$this->data['action'] = $this->url->link($this->modulePath.'/personal_data_request', '', true);
		$this->data['back'] = $this->url->link($this->modulePath, '', true);
		
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['column_right'] = $this->load->controller('common/column_right');
		$this->data['content_top'] = $this->load->controller('common/content_top');
		$this->data['content_bottom'] = $this->load->controller('common/content_bottom');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		
		$this->response->setOutput($this->load->view($this->modulePath.'/request' . $this->ext, $this->data));

	}
	
	public function view_personal_data() {
		$this->document->setTitle($this->language->get('text_view_personal_data'));

		if (isset($this->request->get['hash'])) {
			$hash = $this->request->get['hash'];
		} else {
			$hash = '';
		}
		
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_view_personal_data'),
			'href' => $this->url->link($this->modulePath.'/view_personal_data', 'hash=' . $hash , true)
		);
		
		$active_hours = !empty($this->moduleData['PersonalDataLinkLife']) ? (int)$this->moduleData['PersonalDataLinkLife'] : 5;
		
		$get_data = $this->moduleModel->checkHash($hash, $active_hours);
		
		if ($get_data) {
			$this->data['show_data'] = true;
			$customer_info = $this->moduleModel->getCustomerPersonalInfo($get_data['email']);
			$customer_addresses = $this->moduleModel->getCustomerAddresses($get_data['email']);
			$language_id = $this->config->get('config_language_id');
			
			$this->data['customer_info'] = !empty($customer_info) ? reset($customer_info) : false;
			$this->data['customer_addresses'] = !empty($customer_addresses) ? $customer_addresses : false;
			$this->data['third_party_services'] = !empty($this->moduleData['ThirdPartyServices'][$language_id]) ? $this->moduleData['ThirdPartyServices'][$language_id] : '---';
			$this->data['other_services'] = !empty($this->moduleData['OtherServices'][$language_id]) ? $this->moduleData['OtherServices'][$language_id] : '---';
			
			if (!empty($this->data['customer_info']['newsletter_subscription']) && $this->data['customer_info']['newsletter_subscription']='Yes') {
				$this->data['customer_info']['newsletter_subscription'] = $this->data['text_subscribed'];
			} else if (!empty($this->data['customer_info']['newsletter_subscription']) && $this->data['customer_info']['newsletter_subscription']='No') {
				$this->data['customer_info']['newsletter_subscription'] = $this->data['text_unsubscribed'];
			}
			
			$this->data['guest_orders'] = array();
			if (empty($customer_info)) {
				$this->data['guest_orders'] = $this->moduleModel->getCustomerOrders($get_data['email']);
			}
			
		} else {
			$this->data['show_data'] = false;
		}
		
		$this->data['continue'] = $this->url->link('common/home', '', true);
		
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['column_right'] = $this->load->controller('common/column_right');
		$this->data['content_top'] = $this->load->controller('common/content_top');
		$this->data['content_bottom'] = $this->load->controller('common/content_bottom');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		
		$this->response->setOutput($this->load->view($this->modulePath.'/view_personal_data' . $this->ext, $this->data));
	}
	
	public function deletion_request() {
		$this->document->setTitle($this->language->get('text_right_to_be_forgotten'));
		
		if (isset($this->moduleData['UseCaptcha']) && $this->moduleData['UseCaptcha']=='yes') {
			$this->document->addScript('https://www.google.com/recaptcha/api.js');
		}

		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_right_to_be_forgotten'),
			'href' => $this->url->link($this->modulePath.'/deletion_request', '', true)
		);
		
		if (isset($this->request->post['email'])) {
			$this->data['email'] = $this->request->post['email'];
		} else {
			$this->data['email'] = '';
		}
		
		if (isset($this->moduleData['UseCaptcha']) && $this->moduleData['UseCaptcha']=='yes') {
			$this->data['site_key'] = $this->moduleData['SiteKey'];
		} else {
			$this->data['site_key'] = '';
		}

		$this->data['UseCaptcha'] = isset($this->moduleData['UseCaptcha']) ? $this->moduleData['UseCaptcha'] : 'no';

		$this->data['error'] = '';

		$active_hours = !empty($this->moduleData['RemovePersonalDataLinkLife']) ? (int)$this->moduleData['RemovePersonalDataLinkLife'] : 2;

		if (($this->request->server['REQUEST_METHOD'] == 'POST' && !empty($this->data['email']))) {
			$checker = $this->moduleModel->checkIfEmailExists($this->data['email'], true);
			$this->data['success'] = true;

			if (isset($this->request->post['g-recaptcha-response'])) {
				$recaptcha = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($this->moduleData['SecretKey']) . '&response=' . $this->request->post['g-recaptcha-response'] . '&remoteip=' . $this->request->server['REMOTE_ADDR']);
	
				$recaptcha = json_decode($recaptcha, true);
				
				if (!$recaptcha['success']) {
					$this->data['error'] = $this->language->get('GDPR_captcha');
					$this->data['success'] = '';
				}
			}

			if ($checker) {
				if (($this->moduleData['UseCaptcha']=='no') || (isset($recaptcha) && !empty($recaptcha['success'])) ) {
					$submission_data = $this->moduleModel->insertSubmission($this->data['email'], 'deletion_request');
					
					if (!empty($submission_data) && is_array($submission_data)) {
						$submission_data['active_hours'] = $active_hours;
						$mail_result = $this->moduleModel->sendDeleteDataMail($submission_data);
						$this->load->library('gdpr');
						$this->gdpr->newRequest('Right to be Forgotten Request', $this->data['email']);
					}

					$this->data['success'] = true;
				} else {
					$this->data['success'] = '';
				}
			}
			
		}
		
		$this->data['heading_title'] = $this->data['text_right_to_be_forgotten'];
		$this->data['heading_title_helper'] = $this->data['text_right_to_be_forgotten_helper'];
		$this->data['how_this_works'] = $this->data['text_how_this_works_data_request'];
		$this->data['how_this_works_helper'] = str_replace('%s%', $active_hours, $this->data['text_enter_your_email_data_deletion_helper']);
		$this->data['text_successful_request_helper'] = str_replace('%s%', $active_hours, $this->data['text_successful_data_deletion_helper']);
		
		$this->data['continue'] = $this->url->link('common/home', '', true);
		$this->data['action'] = $this->url->link($this->modulePath.'/deletion_request', '', true);
		$this->data['back'] = $this->url->link($this->modulePath, '', true);
		
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['column_right'] = $this->load->controller('common/column_right');
		$this->data['content_top'] = $this->load->controller('common/content_top');
		$this->data['content_bottom'] = $this->load->controller('common/content_bottom');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		
		$this->response->setOutput($this->load->view($this->modulePath.'/request' . $this->ext, $this->data));

	}
	
	public function delete_data() {
		$this->document->setTitle($this->language->get('text_right_to_be_forgotten'));

		if (isset($this->request->get['hash'])) {
			$hash = $this->request->get['hash'];
		} else {
			$hash = '';
		}
		
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_right_to_be_forgotten'),
			'href' => $this->url->link($this->modulePath.'/delete_data', 'hash=' . $hash, true)
		);
		
		$active_hours = !empty($this->moduleData['RemovePersonalDataLinkLife']) ? (int)$this->moduleData['RemovePersonalDataLinkLife'] : 2;
		
		$get_data = $this->moduleModel->checkHash($hash, $active_hours);

		if ($get_data) {
			$this->data['show_data'] = true;
			
		} else {
			$this->data['show_data'] = false;
		}
		
		$this->data['continue'] = $this->url->link('common/home', '', true);
		$this->data['cancel_deletion'] = $this->url->link($this->modulePath.'/cancel_deletion', 'hash=' . $hash, true);
		$this->data['accept_deletion'] = $this->url->link($this->modulePath.'/accept_deletion', 'hash=' . $hash, true);
		
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['column_right'] = $this->load->controller('common/column_right');
		$this->data['content_top'] = $this->load->controller('common/content_top');
		$this->data['content_bottom'] = $this->load->controller('common/content_bottom');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		
		$this->response->setOutput($this->load->view($this->modulePath.'/delete_data' . $this->ext, $this->data));
	}
	
	public function cancel_deletion() {
		if (isset($this->request->get['hash'])) {
			$hash = $this->request->get['hash'];
		} else {
			$hash = '';
		}
		
		if (!empty($hash)) {
			$this->moduleModel->cancelDeletion($hash);
		}
		
		$this->response->redirect($this->url->link('common/home', '', true));
	}
	
	public function accept_deletion() {
		if (isset($this->request->get['hash'])) {
			$hash = $this->request->get['hash'];
		} else {
			$hash = '';
		}
		
		$active_hours = !empty($this->moduleData['RemovePersonalDataLinkLife']) ? (int)$this->moduleData['RemovePersonalDataLinkLife'] : 2;
		
		$get_data = $this->moduleModel->checkHash($hash, $active_hours);
		$manual_deletion = (empty($this->moduleData['RTBMode']) || (isset($this->moduleData['RTBMode']) && $this->moduleData['RTBMode']=='0')) ? 1 : 0;
		
		$result = false;
		if (!empty($hash) && $get_data) {			
			$result = $this->moduleModel->acceptDeletion($get_data['email'], $manual_deletion);
		}
		
		if ($result) {
			$this->response->redirect($this->url->link($this->modulePath.'/successful_deletion_request', '', true));		
		} else {
			$this->response->redirect($this->url->link('common/home', '', true));
		}
	}
	
	public function successful_deletion_request() {
		$this->document->setTitle($this->language->get('successful_deletion_request'));
		
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('successful_deletion_request'),
			'href' => $this->url->link($this->modulePath.'/successful_deletion_request', '', true)
		);
		
		$this->data['manual_deletion'] = (empty($this->moduleData['RTBMode']) || (isset($this->moduleData['RTBMode']) && $this->moduleData['RTBMode']=='0')) ? 1 : 0;
		
		$this->data['continue'] = $this->url->link('common/home', '', true);
		
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['column_right'] = $this->load->controller('common/column_right');
		$this->data['content_top'] = $this->load->controller('common/content_top');
		$this->data['content_bottom'] = $this->load->controller('common/content_bottom');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		
		$this->response->setOutput($this->load->view($this->modulePath.'/successful_deletion_request' . $this->ext, $this->data));
	}
	
	public function cookie_consent_bar() {
		$language_id = $this->config->get('config_language_id');
		if (!empty($this->moduleData['Enabled']) && $this->moduleData['Enabled'] && !empty($this->moduleData['CC']['Enabled']) && $this->moduleData['CC']['Enabled']) {
			$data['enabled'] = true;
			$data['position'] = !empty($this->moduleData['CC']['Position']) ? $this->moduleData['CC']['Position'] : 'default';
			$data['show_pp_link'] = !empty($this->moduleData['CC']['LinkToPP']) ? (int)$this->moduleData['CC']['LinkToPP'] : '0';
			$data['track_pp_clicks'] = !empty($this->moduleData['CC']['TrackPPClicks']) ? (int)$this->moduleData['CC']['TrackPPClicks'] : '0';
			$data['close_action'] = !empty($this->moduleData['CC']['CloseAction']) ? $this->moduleData['CC']['CloseAction'] : 'nothing';
			$data['default_action'] = !empty($this->moduleData['CC']['DefaultAction']) ? $this->moduleData['CC']['DefaultAction'] : 'nothing';
			$data['message'] = !empty($this->moduleData['CC']['Message'][$language_id]) ? trim($this->moduleData['CC']['Message'][$language_id]) : 'Our website is using cookies!'; 
			$data['pp_text'] = !empty($this->moduleData['CC']['PPText'][$language_id]) ? trim($this->moduleData['CC']['PPText'][$language_id]) : 'Privacy Policy'; 
			$data['accept_text'] = !empty($this->moduleData['CC']['AcceptText'][$language_id]) ? trim($this->moduleData['CC']['AcceptText'][$language_id]) : 'Accept'; 
			$data['dismiss_text'] = !empty($this->moduleData['CC']['DismissText'][$language_id]) ? trim($this->moduleData['CC']['DismissText'][$language_id]) : 'Close'; 
			$data['cookie_settings_text'] = !empty($this->moduleData['CC']['CookiesText'][$language_id]) ? trim($this->moduleData['CC']['CookiesText'][$language_id]) : 'Cookie Preferences'; 
			$data['action'] = $this->url->link($this->modulePath.'/accept_pp_from_ccb', '', true);
			
			$data['banner_bg'] = !empty($this->moduleData['CC']['Style']['BannerBG']) ? $this->moduleData['CC']['Style']['BannerBG'] : '#000000'; 
			$data['banner_text'] = !empty($this->moduleData['CC']['Style']['BannerText']) ? $this->moduleData['CC']['Style']['BannerText'] : '#FFFFFF'; 
			$data['button_bg'] = !empty($this->moduleData['CC']['Style']['ButtonBG']) ? $this->moduleData['CC']['Style']['ButtonBG'] : '#F1D600'; 
			$data['button_text'] = !empty($this->moduleData['CC']['Style']['ButtonText']) ? $this->moduleData['CC']['Style']['ButtonText'] : '#000000'; 
			
			$data['custom_css'] = !empty($this->moduleData['CC']['Styles']) ? $this->moduleData['CC']['Styles'] : ''; 
            
            $opacity_check = !empty($this->moduleData['CC']['Style']['Opacity']) ? $this->moduleData['CC']['Style']['Opacity'] : ''; 
            if ($opacity_check != '') {
                $opacity = $opacity_check * 0.01;
                if ($opacity >= 0 && $opacity <= 1) {
                    $data['custom_css'] .= "div.cc-window {opacity:".$opacity."}";
                }
            }
            
            $privacy_policy_id = $this->config->get('config_account_id');
            if (!empty($this->moduleData['CC']['PageId']) && (int)$this->moduleData['CC']['PageId'] > 0) {
                $data['pp_link'] = $this->url->link('information/information', 'information_id=' . (int)$this->moduleData['CC']['PageId'], true);
            } else if (!empty($privacy_policy_id)) {
                $data['pp_link'] = $this->url->link('information/information', 'information_id=' . (int)$privacy_policy_id, true);
            } else {
                $data['pp_link'] = $this->url->link('common/home', '', true);
            }
			
			$data['always_show'] = !empty($this->moduleData['CC']['AlwaysShow']) ? (int)$this->moduleData['CC']['AlwaysShow'] : '0';
			$data['as_text'] = !empty($this->moduleData['CC']['ASButtonText'][$language_id]) ? $this->moduleData['CC']['ASButtonText'][$language_id] : 'Cookie Bar'; 
			
			$data['analytics_cookies_disable'] = array();
        	$data['marketing_cookies_disable'] = array();
			if (!empty($this->moduleData['CCC']['Analytics'])) {
       			$data['analytics_cookies_disable'] = preg_split('/\r\n|[\r\n]/', $this->moduleData['CCC']['Analytics']);
       		}
       		if (!empty($this->moduleData['CCC']['Marketing'])) {
       			$data['marketing_cookies_disable'] = preg_split('/\r\n|[\r\n]/', $this->moduleData['CCC']['Marketing']);
       		}
		
			$data['analytics_cookies_disable'] = implode(',', $data['analytics_cookies_disable']);
        	$data['marketing_cookies_disable'] = implode(',', $data['marketing_cookies_disable']);
						
			$data['disabled_cookie_sets'] = '';
			$data['apply_default_action'] = isset($this->moduleData['ApplyDefaultAction']) ? $this->moduleData['ApplyDefaultAction'] : 'no';
			$data['marketing_cookies_check'] = 'checked="checked"';
			$data['analytics_cookies_check'] = 'checked="checked"';
			if (isset($_COOKIE['cookieconsent_preferences_disabled'])) {
				$data['disabled_cookie_sets'] = $_COOKIE['cookieconsent_preferences_disabled'];
				$data['marketing_cookies_check'] = (strpos($data['disabled_cookie_sets'], 'marketing') !== false) ? '' : 'checked="checked"';
				$data['analytics_cookies_check'] = (strpos($data['disabled_cookie_sets'], 'analytics') !== false) ? '' : 'checked="checked"';
			} else if (!isset($_COOKIE['cookieconsent_status']) && !isset($_COOKIE['cookieconsent_preferences_disabled'])) {
				$data['marketing_cookies_check'] = '';
				$data['analytics_cookies_check'] = '';
				if ($data['default_action'] == 'nothing') {
					$data['disabled_cookie_sets'] = '';
					$data['analytics_cookies_check'] = $data['apply_default_action'] == 'yes' ? 'checked="checked"' : '';
					$data['marketing_cookies_check'] = $data['apply_default_action'] == 'yes' ? 'checked="checked"' : '';
				} else if ($data['default_action'] == 'analytics') { 
					$data['disabled_cookie_sets'] = 'analytics';
					$data['marketing_cookies_check'] = $data['apply_default_action'] == 'yes' ? 'checked="checked"' : '';
				} else if ($data['default_action'] == 'marketing') { 
					$data['disabled_cookie_sets'] = 'marketing';
					$data['analytics_cookies_check'] = $data['apply_default_action'] == 'yes' ? 'checked="checked"' : '';
				} else if ($data['default_action'] == 'analytics_marketing') { 
					$data['disabled_cookie_sets'] = 'analytics,marketing';
				}
			}
			
			$data['url_variations'] = array();
			if (!empty($_SERVER['HTTP_HOST'])) {
				$store_url = $_SERVER['HTTP_HOST'];
			} else {
				$store_url_data = parse_url(HTTP_SERVER);
				$store_url = $store_url_data['host'];	
			}
			$data['url_variations'][] = '.' . $store_url;
			$data['url_variations'][] = $store_url;
			
			/*=== Regex {2,7} : the 7 is top level domain char long, in this case for an example .website =====*/
			preg_match('/([a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,7})$/i', $store_url , $matches);
			if (!empty($matches[0])) {
				$data['url_variations'][] = '.' . $matches[0];	
				$data['url_variations'][] = $matches[0];	
			}
			
			$data['url_variations'] = array_unique($data['url_variations']);
			$data['url_variations'] = json_encode($data['url_variations']);
		} else {
			$data['enabled'] = false;
		}
		
		$this->language->load($this->modulePath);
		$language_strings = $this->language->load($this->modulePath);
        foreach ($language_strings as $code => $languageVariable) {
			$data[$code] = $languageVariable;
		}
		
		$this->response->setOutput($this->load->view($this->modulePath.'/cookie_consent_bar' . $this->ext, $data));
	}
    
    public function accept_pp_from_ccb() {
		if (!empty($this->moduleData['Enabled']) && $this->moduleData['Enabled']) {
			$customer_email = $this->customer->getEmail();
			$email = !empty($customer_email) ? $customer_email : 'guest_customer@isenselabs_gdpr.com';
            $pp_id = $this->config->get('config_account_id');
            
            if (!empty($this->moduleData['CC']['PageId']) && (int)$this->moduleData['CC']['PageId'] > 0) {
                $pp_id = (int) $this->moduleData['CC']['PageId'];
            } 
            
			if ($pp_id) {
				$this->load->library('gdpr');
				$this->gdpr->newAcceptanceRequest($pp_id, $email);
			}
		}
		return true;
	}
    
    public function newsletter_confirm() {
        $this->document->setTitle($this->language->get('text_confirm_subscription_subject'));
		
        $hash = !empty($this->request->get['hash']) ? $this->request->get['hash'] : '';
        $hash_array = explode('|||', base64_decode($hash));
        $email = !empty($hash_array[0]) ? $hash_array[0] : '';
        $customer_id = !empty($hash_array[1]) ? $hash_array[1] : '0';
        
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_confirm_subscription_subject'),
			'href' => $this->url->link($this->modulePath.'/newsletter_confirm', 'hash=' . $hash, true)
		);
        
        if ($this->moduleModel->checkIfEmailExists($email)) {
            $newsletter_status = $this->moduleModel->confirmNewsletterSubscription(1, $email);  
            $privacy_policy_id = $this->config->get('config_account_id');
            $this->load->library('gdpr');
            $this->gdpr->newOptin($privacy_policy_id, $email, 'newsletter', 'double opt-in');
        } else {
            $this->response->redirect($this->url->link('common/home', '', true));
        }
		
        $this->data['continue'] = $this->url->link('common/home', '', true);
		
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['column_right'] = $this->load->controller('common/column_right');
		$this->data['content_top'] = $this->load->controller('common/content_top');
		$this->data['content_bottom'] = $this->load->controller('common/content_bottom');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view($this->modulePath.'/successful_newsletter_confirm' . $this->ext, $this->data));
    }

	public function newsletter_confirm_j3() {
        $this->document->setTitle($this->language->get('text_confirm_subscription_subject'));
		
        $hash = !empty($this->request->get['hash']) ? $this->request->get['hash'] : '';
        $hash_array = explode('|||', base64_decode($hash));
        $email = !empty($hash_array[0]) ? $hash_array[0] : '';
        $customer_id = !empty($hash_array[1]) ? $hash_array[1] : '0';
        
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_confirm_subscription_subject'),
			'href' => $this->url->link($this->modulePath.'/newsletter_confirm_j3', 'hash=' . $hash, true)
		);
		
		if (!empty($email)) {
            $newsletter_status = $this->moduleModel->confirmJournal3NewsletterSubscription($email);  
            $privacy_policy_id = $this->config->get('config_account_id');
            $this->load->library('gdpr');
            $this->gdpr->newOptin($privacy_policy_id, $email, 'journal3 newsletter', 'double opt-in');
        } else {
            $this->response->redirect($this->url->link('common/home', '', true));
        }
		
        $this->data['continue'] = $this->url->link('common/home', '', true);
		
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['column_right'] = $this->load->controller('common/column_right');
		$this->data['content_top'] = $this->load->controller('common/content_top');
		$this->data['content_bottom'] = $this->load->controller('common/content_bottom');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view($this->modulePath.'/successful_newsletter_confirm' . $this->ext, $this->data));
    }

	public function newsletter_confirm_j2() {
        $this->document->setTitle($this->language->get('text_confirm_subscription_subject'));
		
        $hash = !empty($this->request->get['hash']) ? $this->request->get['hash'] : '';
        $hash_array = explode('|||', base64_decode($hash));
        $email = !empty($hash_array[0]) ? $hash_array[0] : '';
        $customer_id = !empty($hash_array[1]) ? $hash_array[1] : '0';
        
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_confirm_subscription_subject'),
			'href' => $this->url->link($this->modulePath.'/newsletter_confirm_j2', 'hash=' . $hash, true)
		);
		
		if (!empty($email)) {
            $newsletter_status = $this->moduleModel->confirmJournal2NewsletterSubscription($email);  
            $privacy_policy_id = $this->config->get('config_account_id');
            $this->load->library('gdpr');
            $this->gdpr->newOptin($privacy_policy_id, $email, 'journal2 newsletter', 'double opt-in');
        } else {
            $this->response->redirect($this->url->link('common/home', '', true));
        }
		
        $this->data['continue'] = $this->url->link('common/home', '', true);
		
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['column_right'] = $this->load->controller('common/column_right');
		$this->data['content_top'] = $this->load->controller('common/content_top');
		$this->data['content_bottom'] = $this->load->controller('common/content_bottom');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		
		$this->response->setOutput($this->load->view($this->modulePath.'/successful_newsletter_confirm' . $this->ext, $this->data));
    }
	
	public function getOptinsSettings() {
		$json = array();
		
		$policy_id = $this->config->get('config_account_id');

		if (!empty($this->moduleData['Enabled']) && $this->moduleData['Enabled'] && !empty($this->moduleData['EnabledContactFormOptIn']) && $this->moduleData['EnabledContactFormOptIn']==1 && !empty($policy_id)) {

			$this->load->model('catalog/information');
			$information_info = $this->model_catalog_information->getInformation($policy_id);

			if ($information_info) {
				$text_optin_checkbox = sprintf($this->language->get('text_optin_checkbox'), $this->url->link('information/information/agree', 'information_id=' . $policy_id, true), $information_info['title'], $information_info['title']);
				$text_optin_error = sprintf($this->language->get('text_optin_error'), $information_info['title']);
			} else {
				$text_optin_checkbox = sprintf($this->language->get('text_optin_checkbox'), 'javascript:void(0)', 'Privacy Policy', 'Privacy Policy');
				$text_optin_error = sprintf($this->language->get('text_optin_error'), 'Privacy Policy');
			}

			$json = array(
				'error' => false,
				'data' => array(
					'enabled' => true,
					'text_optin_checkbox' => $text_optin_checkbox,
					'text_optin_error' => $text_optin_error
				)
			);
		} else {
			$json = array(
				'error' => false,
				'data' => array(
					'enabled' => false
				)
			);
		}

		header('Content-Type: application/json');
		echo json_encode($json);
		exit;
	}

	public function disableAnalytics($eventRoute,  &$data, &$output) {
        $gdpr_name = $this->moduleName;

        $this->load->model('setting/setting');
		$moduleSettings = $this->model_setting_setting->getSetting($gdpr_name, $this->config->get('config_store_id'));

		$gdprData = !empty($moduleSettings[$gdpr_name]) ? $moduleSettings[$gdpr_name] : array();

        if (!empty($gdprData['Enabled']) && $gdprData['Enabled']) {
			$default_action = !empty($gdprData['CC']['DefaultAction']) ? $gdprData['CC']['DefaultAction'] : 'nothing';
			$apply_default_action = isset($gdprData['ApplyDefaultAction']) ? $gdprData['ApplyDefaultAction'] : 'no';
			$disabled_cookie_sets = '';
			$analytics_cookies_check = true;
			$marketing_cookies_check = true;
			if (isset($_COOKIE['cookieconsent_preferences_disabled'])) {
				$disabled_cookie_sets = $_COOKIE['cookieconsent_preferences_disabled'];
				$marketing_cookies_check = (strpos($disabled_cookie_sets, 'marketing') !== false) ? false : true;
				$analytics_cookies_check = (strpos($disabled_cookie_sets, 'marketing') !== false) ? false : true;
			} else if (!isset($_COOKIE['cookieconsent_status']) && !isset($_COOKIE['cookieconsent_preferences_disabled'])) {
				if ($default_action == 'nothing') {
					$disabled_cookie_sets = '';
					$analytics_cookies_check = true;
					$marketing_cookies_check = true;
				} else if ($default_action == 'analytics') {
					$disabled_cookie_sets = 'analytics';
					$analytics_cookies_check = false;
				} else if ($default_action == 'marketing') {
					$disabled_cookie_sets = 'marketing';
					$marketing_cookies_check = false;
				} else if ($default_action == 'analytics_marketing') {
					$disabled_cookie_sets = 'analytics,marketing';
					$marketing_cookies_check = false;
					$analytics_cookies_check = false;
				}
			}
			if (!$marketing_cookies_check) {
				$output = str_replace("fbq('init',", "fbq('consent', 'revoke');fbq('init',", $output);
			} else {
				$output = str_replace("fbq('init',", "fbq('consent', 'grant');fbq('init',", $output);
			}
			if (!$analytics_cookies_check) {
				$output = str_replace("gtag('js', new Date());", "gtag('consent', 'default', {'ad_storage': 'denied','analytics_storage': 'denied'});gtag('js', new Date());", $output);
			} else {
				$output = str_replace("gtag('js', new Date());", "gtag('consent', 'default', {'ad_storage': 'granted','analytics_storage': 'granted'});gtag('js', new Date());", $output);
			}
		}
	}

	public function disableAddThis($eventRoute,  &$data, &$output) {
        $gdpr_name = $this->moduleName;

        $this->load->model('setting/setting');
		$moduleSettings = $this->model_setting_setting->getSetting($gdpr_name, $this->config->get('config_store_id'));

		$gdprData = !empty($moduleSettings[$gdpr_name]) ? $moduleSettings[$gdpr_name] : array();

        if (!empty($gdprData['Enabled']) && $gdprData['Enabled']) {
			if (!empty($gdprData['CC']['Enabled']) && $gdprData['CC']['Enabled']) {
				$default_action = !empty($gdprData['CC']['DefaultAction']) ? $gdprData['CC']['DefaultAction'] : 'nothing';
				$apply_default_action = isset($gdprData['ApplyDefaultAction']) ? $gdprData['ApplyDefaultAction'] : 'no';
				$disabled_cookie_sets = '';
				$analytics_cookies_check = false;
				if (isset($_COOKIE['cookieconsent_preferences_disabled'])) {
					$disabled_cookie_sets = $_COOKIE['cookieconsent_preferences_disabled'];
					$analytics_cookies_check = (strpos($disabled_cookie_sets, 'analytics') !== false) ? true : false;
					} else if (!isset($_COOKIE['cookieconsent_status']) && !isset($_COOKIE['cookieconsent_preferences_disabled'])) {
						$analytics_cookies_check = '';
						if ($default_action == 'nothing') {
							$disabled_cookie_sets = '';
							$analytics_cookies_check = false;
						} else if ($default_action == 'analytics') {
							$disabled_cookie_sets = 'analytics';
							$analytics_cookies_check = $apply_default_action == 'yes' ? true : false;
						} else if ($default_action == 'analytics_marketing') {
							$disabled_cookie_sets = 'analytics,marketing';
							$analytics_cookies_check = $apply_default_action == 'yes' ? true : false;
						}
					}
				if ($analytics_cookies_check) {
					$output = str_replace('<div class="rating">', '<div class="rating"><script type="text/javascript">var addthis_config = {data_use_cookies_ondomain: !1, data_use_cookies: !1};</script>', $output);
				}
			}
		}
	}

	public function addCookieConsentBar($eventRoute,  &$data, &$output) {
        $gdpr_name = $this->moduleName;
        
        $this->load->model('setting/setting');
		$moduleSettings = $this->model_setting_setting->getSetting($gdpr_name, $this->config->get('config_store_id'));

		$gdprData = !empty($moduleSettings[$gdpr_name]) ? $moduleSettings[$gdpr_name] : array();

        $data['cookie_consent_bar'] = '';
        if (!empty($gdprData['Enabled']) && $gdprData['Enabled']) {
			if (!empty($gdprData['CC']['Enabled']) && $gdprData['CC']['Enabled']) {
				$data['cookie_consent_bar'] = true;
				$data['gdprModulePath'] = $this->modulePath;
				$data['analytics_cookies_check'] = false;
				$default_action = !empty($gdprData['CC']['DefaultAction']) ? $gdprData['CC']['DefaultAction'] : 'nothing';
				$apply_default_action = isset($gdprData['ApplyDefaultAction']) ? $gdprData['ApplyDefaultAction'] : 'no';
				$disabled_cookie_sets = '';
				$analytics_cookies_check = true;
				if (isset($_COOKIE['cookieconsent_preferences_disabled'])) {
					$disabled_cookie_sets = $_COOKIE['cookieconsent_preferences_disabled'];
					$analytics_cookies_check = (strpos($disabled_cookie_sets, 'analytics') !== false) ? false : true;
				} else if (!isset($_COOKIE['cookieconsent_status']) && !isset($_COOKIE['cookieconsent_preferences_disabled'])) {
					$analytics_cookies_check = '';
					if ($default_action == 'nothing') {
						$disabled_cookie_sets = '';
						$analytics_cookies_check = true;
					} else if ($default_action == 'analytics') {
						$disabled_cookie_sets = 'analytics';
						$analytics_cookies_check = $apply_default_action == 'yes' ? false : true;
					} else if ($default_action == 'analytics_marketing') {
						$disabled_cookie_sets = 'analytics,marketing';
						$analytics_cookies_check = $apply_default_action == 'yes' ? false : true;
					}
				}
				if ($analytics_cookies_check) {
					$data['analytics_cookies_check'] = $analytics_cookies_check;
				}
				$cookie_consent_bar_index = $this->load->view($this->modulePath . "/cc_bar", $data);
				$output = str_replace('</body>', $cookie_consent_bar_index . '</body>', $output);
			}
		}
	}

	public function addCookieConsentBarICustomFooter($eventRoute,  &$data, &$output) {
		$icustomfooter_data = $this->model_setting_setting->getSetting('icustomfooter', $this->config->get('config_store_id'));

		$icustomfooter = array();

		if (isset($icustomfooter_data['icustomfooter_settings'])) {
			$data = array();
			foreach ($icustomfooter_data as $key => $value) {
				$data[substr($key, (strlen('icustomfooter') + 1))] = $value;
			}
			$icustomfooter_data = array('icustomfooter' => $data);
		}
		
		if (!empty($icustomfooter_data['icustomfooter'])) {
			$icustomfooter = $icustomfooter_data['icustomfooter'];
		}

		if (isset($icustomfooter) && $icustomfooter['settings']['use_footer_with'] == 'themefooter') {
			return;
		} else {
			$gdpr_name = $this->moduleName;
			
			$this->load->model('setting/setting');
			$moduleSettings = $this->model_setting_setting->getSetting($gdpr_name, $this->config->get('config_store_id'));

			$gdprData = !empty($moduleSettings[$gdpr_name]) ? $moduleSettings[$gdpr_name] : array();

			$data['cookie_consent_bar'] = '';
			if (!empty($gdprData['Enabled']) && $gdprData['Enabled']) {
				if (!empty($gdprData['CC']['Enabled']) && $gdprData['CC']['Enabled']) {
					$data['cookie_consent_bar'] = true;
					$data['gdprModulePath'] = $this->modulePath;
					$data['analytics_cookies_check'] = false;
					$default_action = !empty($gdprData['CC']['DefaultAction']) ? $gdprData['CC']['DefaultAction'] : 'nothing';
					$apply_default_action = isset($gdprData['ApplyDefaultAction']) ? $gdprData['ApplyDefaultAction'] : 'no';
					$disabled_cookie_sets = '';
					$analytics_cookies_check = true;
					if (isset($_COOKIE['cookieconsent_preferences_disabled'])) {
						$disabled_cookie_sets = $_COOKIE['cookieconsent_preferences_disabled'];
						$analytics_cookies_check = (strpos($disabled_cookie_sets, 'analytics') !== false) ? false : true;
					} else if (!isset($_COOKIE['cookieconsent_status']) && !isset($_COOKIE['cookieconsent_preferences_disabled'])) {
						$analytics_cookies_check = '';
						if ($default_action == 'nothing') {
							$disabled_cookie_sets = '';
							$analytics_cookies_check = true;
						} else if ($default_action == 'analytics') {
							$disabled_cookie_sets = 'analytics';
							$analytics_cookies_check = $apply_default_action == 'yes' ? false : true;
						} else if ($default_action == 'analytics_marketing') {
							$disabled_cookie_sets = 'analytics,marketing';
							$analytics_cookies_check = $apply_default_action == 'yes' ? false : true;
						}
					}
					if ($analytics_cookies_check) {
						$data['analytics_cookies_check'] = $analytics_cookies_check;
					}
					$cookie_consent_bar_index = $this->load->view($this->modulePath . "/cc_bar", $data);
					$output = str_replace('<div id="icustomfooter-custom"', $cookie_consent_bar_index . '<div id="icustomfooter-custom"', $output);
				}
			}
		}
	}

	public function addGDPRTools($eventRoute,  &$data, &$output) {
        $gdpr_name = $this->moduleName;

        $this->load->language('account/account');

        $this->load->model('setting/setting');
		$moduleSettings = $this->model_setting_setting->getSetting($gdpr_name, $this->config->get('config_store_id'));

		$gdprData = !empty($moduleSettings[$gdpr_name]) ? $moduleSettings[$gdpr_name] : array();

		if ($this->config->get(('config_theme')) == 'journal3') {
			$account = '<div class="my-affiliates">';
		} elseif ($this->config->get(('config_theme')) == 'journal2' || $this->config->get(('config_template')) == 'journal2') {
			$account = '<h2 class="secondary-title">'.$this->language->get('text_my_newsletter').'</h2>';
		} else {
			$account = '<h2>'.$this->language->get('text_my_newsletter').'</h2>';
		}
		
        if (!empty($gdprData['Enabled']) && $gdprData['Enabled']) {
			$data['text_my_gdpr_tools_header'] = $this->language->get('text_my_gdpr_tools_header');
			$data['text_my_gdpr_tools'] = $this->language->get('text_gdpr');
			$data['link_my_gdpr_tools'] = $this->url->link($this->modulePath, '', true);
			$gdpr_tools = $this->load->view($this->modulePath . "/account", $data);
			$output = str_replace($account, $gdpr_tools . $account, $output);
		}
	}
}