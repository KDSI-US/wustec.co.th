<?php
/* This file is under Git Control by KDSI. */
class ControllerCustomerCustomerApproval extends Controller {
	public function index() {
		$this->load->language('customer/customer_approval');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['filter_tax_id'])) {
			$filter_tax_id = $this->request->get['filter_tax_id'];
		} else {
			$filter_tax_id = '';
		}

		if (isset($this->request->get['filter_seller_permit'])) {
			$filter_seller_permit = $this->request->get['filter_seller_permit'];
		} else {
			$filter_seller_permit = '';
		}
		

		if(isset($this->request->get['input_reason']) && $this->request->get['input_reason'] == "1"){
			return $this->reasonDenied((int)$this->request->get['customer_id']);
		}

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = '';
		}

		if (isset($this->request->get['filter_customer_group_id'])) {
			$filter_customer_group_id = $this->request->get['filter_customer_group_id'];
		} else {
			$filter_customer_group_id = '';
		}

		if (isset($this->request->get['filter_type'])) {
			$filter_type = $this->request->get['filter_type'];
		} else {
			$filter_type = '';
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = '';
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
		}

		if (isset($this->request->get['filter_type'])) {
			$url .= '&filter_type=' . $this->request->get['filter_type'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}


		if (isset($this->request->get['filter_tax_id'])) {
			$url .= '&filter_tax_id=' . $this->request->get['filter_tax_id'];
		}

		if (isset($this->request->get['filter_seller_permit'])) {
			$url .= '&filter_seller_permit=' . $this->request->get['filter_seller_permit'];
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('customer/customer_approval', 'user_token=' . $this->session->data['user_token'], true)
		);
		
		$data['filter_name'] = $filter_name;
		$data['filter_email'] = $filter_email;
		$data['filter_customer_group_id'] = $filter_customer_group_id;
		$data['filter_type'] = $filter_type;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_tax_id'] = $filter_tax_id;
		$data['filter_seller_permit'] = $filter_seller_permit;

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('customer/customer_group');

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('customer/customer_approval', $data));
	}

	public function customer_approval() {
		$this->load->model('tool/image');
		$this->load->language('customer/customer_approval');

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = '';
		}

		if (isset($this->request->get['filter_customer_group_id'])) {
			$filter_customer_group_id = $this->request->get['filter_customer_group_id'];
		} else {
			$filter_customer_group_id = '';
		}

		if (isset($this->request->get['filter_type'])) {
			$filter_type = $this->request->get['filter_type'];
		} else {
			$filter_type = '';
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = '';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}


		if (isset($this->request->get['filter_tax_id'])) {
			$filter_tax_id = $this->request->get['filter_tax_id'];
		} else {
			$filter_tax_id = '';
		}

		if (isset($this->request->get['filter_seller_permit'])) {
			$filter_seller_permit = $this->request->get['filter_seller_permit'];
		} else {
			$filter_seller_permit = '';
		}
		
		$data['customer_approvals'] = array();

		$filter_data = array(
			'filter_name'              => $filter_name,
			'filter_email'             => $filter_email,
			'filter_customer_group_id' => $filter_customer_group_id,
			'filter_type'              => $filter_type,
			'filter_date_added'        => $filter_date_added,
			'filter_tax_id'            => $filter_tax_id,
			'filter_seller_permit'     => $filter_seller_permit,
			'start'                    => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                    => $this->config->get('config_limit_admin')
		);

		$this->load->model('customer/customer_approval');

		$customer_approval_total = $this->model_customer_customer_approval->getTotalCustomerApprovals($filter_data);

		$results = $this->model_customer_customer_approval->getCustomerApprovals($filter_data);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['seller_permit_file'])) {
				$result['has_seller_permit_file'] = true;
				$result['seller_permit_file'] = str_replace($_SERVER['DOCUMENT_ROOT'],"",DIR_IMAGE) . $result['seller_permit_file'];
			} else {
				$result['has_seller_permit_file'] = false;
				$result['seller_permit_file'] = str_replace($_SERVER['DOCUMENT_ROOT'],"",DIR_IMAGE) . 'no_image.png';
			}
		
			$data['customer_approvals'][] = array(
				'customer_id'    => $result['customer_id'],
				'name'           => $result['name'],
				'email'          => $result['email'],
				'customer_group' => $result['customer_group'],
				'type'           => $this->language->get('text_' . $result['type']),
				'date_added'     => date($this->language->get('datetime_format'), strtotime($result['date_added'])),
				'tax_id'         => $result['tax_id'],
				'seller_permit'  => $result['seller_permit'],
				'seller_permit_file'  => $result['seller_permit_file'],
				'has_seller_permit_file'  => $result['has_seller_permit_file'],
				'approved'         => $result['approved'],
				'approve'        => $this->url->link('customer/customer_approval/approve', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $result['customer_id'] . '&type=' . $result['type'], true),
				'deny'           => $this->url->link('customer/customer_approval/deny', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $result['customer_id'] . '&type=' . $result['type'], true),
				'reason_denied'   => $this->url->link('customer/customer_approval', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $result['customer_id'] . '&input_reason=1' . '&type=' . $result['type'], true),
				'edit'           => $this->url->link('customer/customer/edit', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $result['customer_id'], true)
			);
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
		}

		if (isset($this->request->get['filter_type'])) {
			$url .= '&filter_type=' . $this->request->get['filter_type'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}


		if (isset($this->request->get['filter_tax_id'])) {
			$url .= '&filter_tax_id=' . $this->request->get['filter_tax_id'];
		}

		if (isset($this->request->get['filter_seller_permit'])) {
			$url .= '&filter_seller_permit=' . $this->request->get['filter_seller_permit'];
		}
		
		$pagination = new Pagination();
		$pagination->total = $customer_approval_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('customer/customer_approval/customer_approval', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($customer_approval_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($customer_approval_total - $this->config->get('config_limit_admin'))) ? $customer_approval_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $customer_approval_total, ceil($customer_approval_total / $this->config->get('config_limit_admin')));

		$this->response->setOutput($this->load->view('customer/customer_approval_list', $data));
	}

	public function approve() {
		$this->load->language('customer/customer_approval');

		$json = array();

		if (!$this->user->hasPermission('modify', 'customer/customer_approval')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('customer/customer_approval');

			if ($this->request->get['type'] == 'customer') {
				$this->model_customer_customer_approval->approveCustomer($this->request->get['customer_id']);
			} elseif ($this->request->get['type'] == 'affiliate') {
				$this->model_customer_customer_approval->approveAffiliate($this->request->get['customer_id']);
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	public function reasonDenied($customer_id) 
	{

		$this->load->language('customer/customer_approval');

		if (isset($this->request->post['reason_denied'])) {
			$reason_denied = $this->request->post['reason_denied'];
		} else {
			$reason_denied = '';
		}

		if (isset($this->request->post['input_other_reason_denied'])) {
			$input_other_reason_denied = $this->request->post['input_other_reason_denied'];
		} else {
			$input_other_reason_denied = '';
		}
		
		$data = array();
		$data['customer_id'] = $customer_id;
		$data['reason_denied'] = $reason_denied;
		$data['input_other_reason_denied'] = $input_other_reason_denied;
		$data['text_reason_denied'] = $this->language->get('text_reason_denied');
		$data['select_reason_denied'] = $this->language->get('select_reason_denied');
		$data['entry_other_reason_denied'] = $this->language->get('entry_other_reason_denied');
		$data['other_reason_denied'] = $this->language->get('other_reason_denied');
		$data['input_other_reason_denied'] = $this->language->get('input_other_reason_denied');
		
		if ( $reason_denied != '' || $input_other_reason_denied != '') {
			$json = array();

			if (!$this->user->hasPermission('modify', 'customer/customer_approval')) {
				$json['error'] = $this->language->get('error_permission');
			} else {
				$this->load->model('customer/customer_approval');

				if ($this->request->get['type'] == 'customer') {
					$reason_denied = $this->language->get($reason_denied);
					$this->model_customer_customer_approval->denyCustomer($customer_id, ($reason_denied == 'other') ? $input_other_reason_denied : $reason_denied);
				}

				$json['success'] = $this->language->get('text_success');
			}

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
		$this->response->setOutput($this->load->view('customer/customer_reason_denied_form', $data));
	}
		
	public function deny() {
		$this->load->language('customer/customer_approval');

		$json = array();

		if (!$this->user->hasPermission('modify', 'customer/customer_approval')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('customer/customer_approval');

			if ($this->request->get['type'] == 'customer') {
				$this->model_customer_customer_approval->denyCustomer($this->request->get['customer_id']);
			} elseif ($this->request->get['type'] == 'affiliate') {
				$this->model_customer_customer_approval->denyAffiliate($this->request->get['customer_id']);
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
