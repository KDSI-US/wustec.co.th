<?php
/* This file is under Git Control by KDSI. */
class ControllerInformationInformation extends Controller
{
	private $error = array();

	public function index()
	{
		$login_required = $this->config->get("module_kdsi_login_required");
		if ($login_required['status_information']) {
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
				if (in_array($route, $safe_route)) {
					$redirect = false;
				}
			}
			if ($redirect) {
				if ($route == 'information/information') {
					$information_id = $this->request->get['information_id'];
					$this->session->data['redirect'] = $this->url->link('information/information', 'information_id=' . $information_id, true);
				} elseif ($route == 'information/contact') {
					$this->session->data['redirect'] = $this->url->link('information/contact', '', true);
				} elseif ($route == 'information/sitemap') {
					$this->session->data['redirect'] = $this->url->link('information/sitemap', '', true);
				}
				$this->response->redirect($this->url->link('account/login', '', true));
			}
		}

		$approval_required = $this->config->get("module_approval_required");
		$approved = (int)$this->customer->getApproved();
		if ($approval_required['status_information']) {
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
				if (in_array($route, $safe_route)) {
					$redirect = false;
				}
			}
			if ($redirect) {
				if ($route == 'information/information') {
					$information_id = $this->request->get['information_id'];
					$this->session->data['redirect'] = $this->url->link('information/information', 'information_id=' . $information_id, true);
				} elseif ($route == 'information/contact') {
					$this->session->data['redirect'] = $this->url->link('information/contact', '', true);
				} elseif ($route == 'information/sitemap') {
					$this->session->data['redirect'] = $this->url->link('information/sitemap', '', true);
				}
				$this->response->redirect($this->url->link('account/edit', '', true));
			}
		}

		$this->load->language('information/information');

		$this->load->model('catalog/information');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		if (isset($this->request->get['information_id'])) {
			$information_id = (int)$this->request->get['information_id'];
		} else {
			$information_id = 0;
		}

		$information_info = $this->model_catalog_information->getInformation($information_id);

		if ($information_id == 34) {
			$this->registrationIndex($information_id);
		} elseif ($information_id == 35) {
			$this->registrationIndex($information_id);
		} else {
			if ($information_info) {

				$filter_galleria = array(
					'source' => 'information',
					'source_id'  => $information_info['information_id']
				);
				$data['galleria'] = $this->load->controller('extension/module/galleria/widget', $filter_galleria);

				$this->document->setTitle($information_info['meta_title']);
				$this->document->setDescription($information_info['meta_description']);
				$this->document->setKeywords($information_info['meta_keyword']);

				$data['breadcrumbs'][] = array(
					'text' => $information_info['title'],
					'href' => $this->url->link('information/information', 'information_id=' .  $information_id)
				);

				$data['heading_title'] = $information_info['title'];

				$data['description'] = html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8');

				$data['action'] = $this->url->link('information/contact', '', true);
				$data['continue'] = $this->url->link('common/home');

				$data['column_left'] = $this->load->controller('common/column_left');
				$data['column_right'] = $this->load->controller('common/column_right');
				$data['content_top'] = $this->load->controller('common/content_top');
				$data['content_bottom'] = $this->load->controller('common/content_bottom');
				$data['footer'] = $this->load->controller('common/footer');
				$data['header'] = $this->load->controller('common/header');

				$this->response->setOutput($this->load->view('information/information', $data));
			} else {
				$data['breadcrumbs'][] = array(
					'text' => $this->language->get('text_error'),
					'href' => $this->url->link('information/information', 'information_id=' . $information_id)
				);

				$this->load->controller('extension/module/isenselabs_seo/notFoundPageHandler');
				$this->document->setTitle($this->language->get('text_error'));

				$data['heading_title'] = $this->language->get('text_error');

				$data['text_error'] = $this->language->get('text_error');

				$data['continue'] = $this->url->link('common/home');

				$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

				$data['column_left'] = $this->load->controller('common/column_left');
				$data['column_right'] = $this->load->controller('common/column_right');
				$data['content_top'] = $this->load->controller('common/content_top');
				$data['content_bottom'] = $this->load->controller('common/content_bottom');
				$data['footer'] = $this->load->controller('common/footer');
				$data['header'] = $this->load->controller('common/header');

				$this->response->setOutput($this->load->view('error/not_found', $data));
			}
		}
	}

	public function agree()
	{
		$this->load->model('catalog/information');

		if (isset($this->request->get['information_id'])) {
			$information_id = (int)$this->request->get['information_id'];
		} else {
			$information_id = 0;
		}

		$output = '';

		$information_info = $this->model_catalog_information->getInformation($information_id);

		if ($information_info) {

			$filter_galleria = array(
				'source' => 'information',
				'source_id'  => $information_info['information_id']
			);
			$data['galleria'] = $this->load->controller('extension/module/galleria/widget', $filter_galleria);

			$output .= html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8') . "\n";
		}

		$this->response->addHeader('X-Robots-Tag: noindex');

		$this->response->setOutput($output);
	}

	public function registrationIndex($information_id)
	{
		if($information_id == 34) {
			$this->load->language('information/registration');
			$key = 'information.supplier';
		} else {
			$this->load->language('information/report');
			$key = 'information.csr';
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) { // Change subject with employee and to email based on form
			// Prepare mail: information.csr
			$this->load->model('extension/module/emailtemplate');

			$template_load = array(
				'key' => $key,
				'email' => $this->request->post['email']
			);

			$template = $this->model_extension_module_emailtemplate->load($template_load);

			if ($template) {
				//$template->addData($this->request->post);

				$template->data['name'] = html_entity_decode($this->request->post['name'], ENT_QUOTES, 'UTF-8');

				$template->data['email'] = $this->request->post['email'];

				if (!empty($this->request->post['employee'])) {
					$template->data['employee'] = $this->request->post['employee'];
				}
				if (!empty($this->request->post['phone'])) {
					$template->data['phone'] = $this->request->post['phone'];
				}

				if (!empty($this->request->post['company'])) {
					$template->data['company'] = $this->request->post['company'];
				}

				if (!empty($this->request->post['enquiry'])) {
					$template->data['enquiry'] = html_entity_decode(str_replace("\n", "<br />", $this->request->post['enquiry']), ENT_QUOTES, 'UTF-8');
				}

				if (isset($template->data['text_ip'])) {
					$template->data['text_ip'] = sprintf($template->data['text_ip'], $this->request->server['REMOTE_ADDR']);
				}

				if (defined('HTTP_ADMIN')) {
					$admin_url = HTTP_ADMIN;
				} else {
					$admin_url = HTTPS_SERVER . 'admin/';
				}

				// Prepared mail: information.csr

				$mail = new Mail($this->config->get('config_mail_engine'));
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

				$mail->setTo($template->data['emailtemplate']['mail_to']);
				$mail->setFrom($this->config->get('config_email'));

				$mail->setReplyTo($this->request->post['email']);
				$mail->setSender(html_entity_decode($this->request->post['name'], ENT_QUOTES, 'UTF-8'));
				// Send mail: information.csr
				if ($template && $template->check()) {
					$mail->setReplyTo($template->data['email'], $template->data['name']);
					
					$template->build();
					$template->hook($mail);
					$mail->setSubject(html_entity_decode(sprintf($template->data['emailtemplate']['subject'] . $this->request->post['subject']), ENT_QUOTES, 'UTF-8'));

					$mail->send();

					$this->model_extension_module_emailtemplate->sent();
				}
			} 

			// Prepare mail: information.csr
			//$this->load->model('extension/module/emailtemplate');
/*
			$template_load = array(
				'key' => 'information.csr'
			);

			$template = $this->model_extension_module_emailtemplate->load($template_load);

			if ($template) {
				$template->data['name'] = html_entity_decode($this->request->post['name'], ENT_QUOTES, 'UTF-8');

				if (!empty($this->request->post['enquiry'])) {
					$template->data['enquiry'] = html_entity_decode(str_replace("\n", "<br />", $this->request->post['enquiry']), ENT_QUOTES, 'UTF-8');
				}

				if (!empty($customer_info)) {
					$template->data['customer'] = $customer_info;
				}
				// Prepared mail: information.csr

				// Send mail: information.csr
				if ($template && $template->check()) {
					$template->build();
					$template->hook($mail);

					// $mail->send();

					$this->model_extension_module_emailtemplate->sent();
				}
			}*/
			// Prepare mail: information.contact_customer

			$template_load = array(
				'key' => 'information.contact_customer',
				'email' => $this->request->post['email']
			);

			$template = $this->model_extension_module_emailtemplate->load($template_load);

			if ($template) {

				$template->data['name'] = html_entity_decode($this->request->post['name'], ENT_QUOTES, 'UTF-8');

				$template->data['email'] = $this->request->post['email'];

				if (!empty($this->request->post['enquiry'])) {
					$template->data['enquiry'] = html_entity_decode(str_replace("\n", "<br />", $this->request->post['enquiry']), ENT_QUOTES, 'UTF-8');
				}

				// Prepared mail: information.contact_customer

				$mail->setTo($this->request->post['email']);
				$mail->setFrom($this->config->get('config_email'));

				$mail->setReplyTo($this->config->get('config_email'), $this->config->get('config_name'));
				$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
				$mail->setSubject(html_entity_decode(sprintf($this->language->get('email_subject'), $this->request->post['name']), ENT_QUOTES, 'UTF-8'));
				// Send mail: information.contact_customer
				if ($template && $template->check()) {
					$template->build();
					$template->hook($mail);

					$mail->send();

					$this->model_extension_module_emailtemplate->sent();
				}
			}


			$file = DIR_SYSTEM . 'library/gdpr.php';
			if (is_file($file)) {
				$privacy_policy_id = $this->config->get('config_account_id');
				$this->config->load('isenselabs/isenselabs_gdpr');
				$gdpr_name = $this->config->get('isenselabs_gdpr_name');

				$this->load->model('setting/setting');
				$moduleSettings = $this->model_setting_setting->getSetting($gdpr_name, $this->config->get('config_store_id'));
				$gdpr_data = !empty($moduleSettings[$gdpr_name]) ? $moduleSettings[$gdpr_name] : array();

				if (!empty($gdpr_data['EnabledContactFormOptIn']) && ($gdpr_data['EnabledContactFormOptIn'] == 1) && $privacy_policy_id) {
					$this->load->library('gdpr');
					$this->gdpr->newOptin($privacy_policy_id, $this->request->post['email'], 'contact_form', 'acceptance');
				}
			}

			$this->response->redirect($this->url->link('information/contact/success'));
		}

		$this->load->model('catalog/information');
		if (isset($this->request->get['information_id'])) {
			$information_id = (int)$this->request->get['information_id'];
		} else {
			$information_id = 0;
		}
		
		$data['breadcrumbs'] = array();
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);
		
		$information_info = $this->model_catalog_information->getInformation($information_id);

		if ($information_info) {
			$this->document->setTitle($information_info['meta_title']);
			$this->document->setDescription($information_info['meta_description']);
			$this->document->setKeywords($information_info['meta_keyword']);
			
			$data['breadcrumbs'][] = array(
				'text' => $information_info['title'],
				'href' => $this->url->link('information/information', 'information_id=' .  $information_id)
			);
			
			if (isset($this->error['name'])) {
				$data['error_name'] = $this->error['name'];
			} else {
				$data['error_name'] = '';
			}

			if (isset($this->error['email'])) {
				$data['error_email'] = $this->error['email'];
			} else {
				$data['error_email'] = '';
			}
			
			if (isset($this->error['employee'])) {
				$data['error_employee'] = $this->error['employee'];
			} else {
				$data['error_employee'] = '';
			}

			if (isset($this->error['enquiry'])) {
				$data['error_enquiry'] = $this->error['enquiry'];
			} else {
				$data['error_enquiry'] = '';
			}

			$data['button_submit'] = $this->language->get('button_submit');

			$this->load->model('tool/image');

			if ($this->config->get('config_image')) {
				$data['image'] = $this->model_tool_image->resize($this->config->get('config_image'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_location_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_location_height'));
			} else {
				$data['image'] = false;
			}

			$data['address'] = nl2br($this->config->get('config_address'));
			$data['telephone'] = $this->config->get('config_telephone');
			$data['office_email'] = $this->config->get('config_email');
			$data['fax'] = $this->config->get('config_fax');
			$data['open'] = nl2br($this->config->get('config_open'));
			$data['comment'] = $this->config->get('config_comment');
			$data['contact_details'] =  html_entity_decode($this->config->get('config_contact_details'), ENT_QUOTES, 'UTF-8');

			if (isset($this->request->post['name'])) {
				$data['name'] = $this->request->post['name'];
			} else {
				$data['name'] = $this->customer->getFirstName();
			}

			if (isset($this->request->post['email'])) {
				$data['email'] = $this->request->post['email'];
			} else {
				$data['email'] = $this->customer->getEmail();
			}

			if (isset($this->request->post['enquiry'])) {
				$data['enquiry'] = $this->request->post['enquiry'];
			} else {
				$data['enquiry'] = '';
			}

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('contact', (array)$this->config->get('config_captcha_page'))) {
				$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'), $this->error);
			} else {
				$data['captcha'] = '';
			}

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$data['action'] = $this->url->link('information/information', 'information_id=' . $information_id, true);
			if ($information_id == 34) {
			$this->response->setOutput($this->load->view('information/registration', $data));
			} else {
			$this->response->setOutput($this->load->view('information/report', $data));
			}
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('information/information', 'information_id=' . $information_id)
			);

			$this->load->controller('extension/module/isenselabs_seo/notFoundPageHandler');
			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	protected function validate()
	{
		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 32)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (!filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if($this->request->post['employee']) {
			if (!(((int)$this->request->post['employee'] == 'a wus employee') || ((int)$this->request->post['employee'] == 'not a wus employee') || ((int)$this->request->post['employee'] == 'other'))) {
				$this->error['employee'] = $this->language->get('error_employee');
			}
		}

		if ((utf8_strlen($this->request->post['enquiry']) < 10) || (utf8_strlen($this->request->post['enquiry']) > 3000)) {
			$this->error['enquiry'] = $this->language->get('error_enquiry');
		}

		// Captcha
		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('contact', (array)$this->config->get('config_captcha_page'))) {
			$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

			if ($captcha) {
				$this->error['captcha'] = $captcha;
			}
		}

		return !$this->error;
	}

	public function success()
	{
		$this->load->language('information/contact');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('information/contact')
		);

		$data['text_message'] = $this->language->get('text_message');

		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/success', $data));
	}
}
