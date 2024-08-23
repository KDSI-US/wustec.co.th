<?php
/* This file is under Git Control by KDSI. */
class ControllerAccountNewsletter extends Controller {
	public function unsubscribe() {
        $this->response->redirect($this->url->link('extension/module/emailtemplate_newsletter/unsubscribe'));
 	}
 	  
	public function index() {
     if ($this->config->get('module_emailtemplate_newsletter_status') && $this->config->get('module_emailtemplate_newsletter_preference')) {
            $this->response->redirect($this->url->link('extension/module/emailtemplate_newsletter', '', true));
        }
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/newsletter', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/newsletter');

		$this->document->setTitle($this->language->get('heading_title'));

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$this->load->model('account/customer');

			$this->model_account_customer->editNewsletter($this->request->post['newsletter']);

			
		$file = DIR_SYSTEM . 'library/gdpr.php';
		if (is_file($file)) {
			$this->config->load('isenselabs/isenselabs_gdpr');
			$gdpr_name = $this->config->get('isenselabs_gdpr_name');

			$this->load->model('setting/setting');
			$moduleSettings = $this->model_setting_setting->getSetting($gdpr_name, $this->config->get('config_store_id'));
			$gdpr_data = !empty($moduleSettings[$gdpr_name]) ? $moduleSettings[$gdpr_name] : array();

			if (!empty($gdpr_data['NewsletterDoubleOptIn']) && ($gdpr_data['NewsletterDoubleOptIn'] == '1') && isset($this->request->post['newsletter']) && $this->request->post['newsletter'] == '1') {
				$gdpr_path = $this->config->get('isenselabs_gdpr_path');
				$gdpr_language_files = $this->language->load($gdpr_path);
				$this->session->data['success'] = $gdpr_language_files['text_succes_message_after_edit_newsletter'];
			}
		} else {
			$this->session->data['success'] = $this->language->get('text_success');
		}
        

			$this->response->redirect($this->url->link('account/account', '', true));
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_newsletter'),
			'href' => $this->url->link('account/newsletter', '', true)
		);

		$data['action'] = $this->url->link('account/newsletter', '', true);

		$data['newsletter'] = $this->customer->getNewsletter();

		$data['back'] = $this->url->link('account/account', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/newsletter', $data));
	}
}