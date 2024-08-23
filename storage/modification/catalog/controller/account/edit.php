<?php
/* This file is under Git Control by KDSI. */
class ControllerAccountEdit extends Controller {
	private $error = array();

	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/edit', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/edit');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

		$this->load->model('account/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_account_customer->editCustomer($this->customer->getId(), $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

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
			'text' => $this->language->get('text_edit'),
			'href' => $this->url->link('account/edit', '', true)
		);

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['firstname'])) {
			$data['error_firstname'] = $this->error['firstname'];
		} else {
			$data['error_firstname'] = '';
		}

		if (isset($this->error['lastname'])) {
			$data['error_lastname'] = $this->error['lastname'];
		} else {
			$data['error_lastname'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}


		if (isset($this->error['tax_id'])) {
			$data['error_tax_id'] = $this->error['tax_id'];
		} else {
			$data['error_tax_id'] = '';
		}

		if (isset($this->error['seller_permit'])) {
			$data['error_seller_permit'] = $this->error['seller_permit'];
		} else {
			$data['error_seller_permit'] = '';
		}
		
		if (isset($this->error['custom_field'])) {
			$data['error_custom_field'] = $this->error['custom_field'];
		} else {
			$data['error_custom_field'] = array();
		}

		$data['action'] = $this->url->link('account/edit', '', true);

		if ($this->request->server['REQUEST_METHOD'] != 'POST') {
			$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
		}


		$data['entry_image'] = $this->language->get('entry_image');
		$data['button_image'] = $this->language->get('button_image');
		$data['button_clear'] = $this->language->get('button_clear');
		
		$this->load->model('tool/image');

		if (isset($this->request->post['profile_picture'])) {
			$data['profile_picture'] = $this->request->post['profile_picture'];
		} elseif (!empty($customer_info)) {
			$data['profile_picture'] = isset($customer_info['profile_picture']) ? $customer_info['profile_picture'] : '';
		} else {
			$data['profile_picture'] = '';
		}

		if (is_file(DIR_IMAGE . $data['profile_picture'])) {
			if (strtolower(substr(strrchr($data['profile_picture'], '.'), 1)) == 'pdf'){
				$data['profile_picture_thumb'] = $this->model_tool_image->resize('pdf_file.png', 45, 45);	
			} else{
				$data['profile_picture_thumb'] = $this->model_tool_image->resize($data['profile_picture'], 125, 125);
			}
		} else {
			$data['profile_picture_thumb'] = $this->model_tool_image->resize('no_image.png', 125, 125);	
		}

		if (isset($this->request->post['seller_permit_file'])) {
			$data['seller_permit_file'] = $this->request->post['seller_permit_file'];
		} elseif (!empty($customer_info)) {
			$data['seller_permit_file'] = isset($customer_info['seller_permit_file']) ? $customer_info['seller_permit_file'] : '';
		} else {
			$data['seller_permit_file'] = '';
		}

		if (is_file(DIR_IMAGE . $data['seller_permit_file'])) {
			if (strtolower(substr(strrchr($data['seller_permit_file'], '.'), 1)) == 'pdf'){
				$data['seller_permit_file_thumb'] = $this->model_tool_image->resize('pdf_file.png', 45, 45);
			} else{
				$data['seller_permit_file_thumb'] = $this->model_tool_image->resize($data['seller_permit_file'], 125, 125);
			}
		} else {
			$data['seller_permit_file_thumb'] = $this->model_tool_image->resize('no_image.png', 125, 125);
		}
		
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 125, 125);	
		$data['account_dashboard_status'] = $this->config->get('account_dashboard_status');
		$data['picture_status'] = $this->config->get('account_dashboard_customer_picture_status');
			
		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname'];
		} elseif (!empty($customer_info)) {
			$data['firstname'] = $customer_info['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} elseif (!empty($customer_info)) {
			$data['lastname'] = $customer_info['lastname'];
		} else {
			$data['lastname'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($customer_info)) {
			$data['email'] = $customer_info['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} elseif (!empty($customer_info)) {
			$data['telephone'] = $customer_info['telephone'];
		} else {
			$data['telephone'] = '';
		}


		if (isset($this->request->post['tax_id'])) {
			$data['tax_id'] = $this->request->post['tax_id'];
		} elseif (!empty($customer_info)) {
			$data['tax_id'] = $customer_info['tax_id'];
		} else {
			$data['tax_id'] = '';
		}

		if (isset($this->request->post['seller_permit'])) {
			$data['seller_permit'] = $this->request->post['seller_permit'];
		} elseif (!empty($customer_info)) {
			$data['seller_permit'] = $customer_info['seller_permit'];
		} else {
			$data['seller_permit'] = '';
		}

		if ($customer_info['approved'] == 1) {
			$data['is_approved'] = 1;
		} else {
			$data['is_approved'] = 0;
		}

		if ($customer_info['denied'] == 1) {
			$data['is_denied'] = 1;
		} else {
			$data['is_denied'] = 0;
		}

		$data['alert_verification_required'] = $this->config->get('account_dashboard_alert_verification_required');
		$data['alert_verification_pending'] = $this->config->get('account_dashboard_alert_verification_pending');
		$data['status_verification_required'] = $this->config->get('account_dashboard_status_verification_required');
		$data['status_verification_pending'] = $this->config->get('account_dashboard_status_verification_pending');
		$data['status_verification_verified'] = $this->config->get('account_dashboard_status_verification_verified');
		$data['alert_verification_denied'] = $this->config->get('account_dashboard_alert_verification_denied');
		$data['status_verification_denied'] = $this->config->get('account_dashboard_status_verification_denied');		
		
		if (isset($this->request->post['custom_field']['account'])) {
			$data['account_custom_field'] = $this->request->post['custom_field']['account'];
		} elseif (isset($customer_info)) {
			$data['account_custom_field'] = json_decode($customer_info['custom_field'], true);
		} else {
			$data['account_custom_field'] = array();
		}

		// Custom Fields
		$data['custom_fields'] = array();

		$this->load->model('tool/upload');
		$this->load->model('account/custom_field');

		$custom_fields = $this->model_account_custom_field->getCustomFields($this->config->get('config_customer_group_id'));

		foreach ($custom_fields as $custom_field) {
			if ($custom_field['location'] == 'account') {
				if($custom_field['type'] == 'file' && isset($data['account_custom_field'][$custom_field['custom_field_id']])) {
					$code = $data['account_custom_field'][$custom_field['custom_field_id']];

					$data['account_custom_field'][$custom_field['custom_field_id']] = array();

					$upload_result = $this->model_tool_upload->getUploadByCode($code);
					
					if($upload_result) {
						$data['account_custom_field'][$custom_field['custom_field_id']]['name'] = $upload_result['name'];
						$data['account_custom_field'][$custom_field['custom_field_id']]['code'] = $upload_result['code'];
					} else {
						$data['account_custom_field'][$custom_field['custom_field_id']]['name'] = "";
						$data['account_custom_field'][$custom_field['custom_field_id']]['code'] = $code;
					}
					$data['custom_fields'][] = $custom_field;
				} else {
					$data['custom_fields'][] = $custom_field;
				}
			}
		}

		$data['back'] = $this->url->link('account/account', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/edit', $data));
	}

	protected function validate() {
		if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if (($this->customer->getEmail() != $this->request->post['email']) && $this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$this->error['warning'] = $this->language->get('error_exists');
		}

		if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}


		/* Tax ID */
		if ((utf8_strlen(trim($this->request->post['tax_id'])) > 50)) {
			$this->error['tax_id'] = $this->language->get('error_tax_id');
		}

		/* Seller's Permit */
		if ((utf8_strlen(trim($this->request->post['seller_permit'])) < 1) || (utf8_strlen(trim($this->request->post['seller_permit'])) > 50)) {
			$this->error['seller_permit'] = $this->language->get('error_seller_permit');
		}
		
		// Custom field validation
		$this->load->model('account/custom_field');

		$custom_fields = $this->model_account_custom_field->getCustomFields($this->config->get('config_customer_group_id'));

		foreach ($custom_fields as $custom_field) {
			if ($custom_field['location'] == 'account') {
				if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
					$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
					$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				}
			}
		}

		return !$this->error;
	}
}