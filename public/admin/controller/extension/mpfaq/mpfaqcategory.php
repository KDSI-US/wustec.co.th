<?php
class ControllerExtensionMpFaqMpfaqcategory extends Controller {
	private $error = [];

	public function index() {
		$this->load->language('mpfaq/mpfaqcategory');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpfaq/mpfaqcategory');

		/// Table Bnao
		$this->model_extension_mpfaq_mpfaqcategory->tablebnao();

		$this->getList();
	}

	public function add() {
		$this->load->language('mpfaq/mpfaqcategory');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpfaq/mpfaqcategory');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_mpfaq_mpfaqcategory->addMpfaqcategory($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/mpfaq/mpfaqcategory', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('mpfaq/mpfaqcategory');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpfaq/mpfaqcategory');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_mpfaq_mpfaqcategory->editMpfaqcategory($this->request->get['mpfaqcategory_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/mpfaq/mpfaqcategory', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('mpfaq/mpfaqcategory');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mpfaq/mpfaqcategory');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $mpfaqcategory_id) {
				$this->model_extension_mpfaq_mpfaqcategory->deleteMpfaqcategory($mpfaqcategory_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/mpfaq/mpfaqcategory', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'agd.name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/mpfaq/mpfaqcategory', 'user_token=' . $this->session->data['user_token'] . $url, true)
		];

		$data['add'] = $this->url->link('extension/mpfaq/mpfaqcategory/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/mpfaq/mpfaqcategory/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['mpfaqcategorys'] = [];

		$filter_data = [
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		];

		$mpfaqcategory_total = $this->model_extension_mpfaq_mpfaqcategory->getTotalMpfaqcategorys($filter_data);

		$results = $this->model_extension_mpfaq_mpfaqcategory->getMpfaqcategorys($filter_data);

		foreach ($results as $result) {
			$data['mpfaqcategorys'][] = [
				'mpfaqcategory_id' => $result['mpfaqcategory_id'],
				'name'               => $result['name'],
				'sort_order'         => $result['sort_order'],
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit'               => $this->url->link('extension/mpfaq/mpfaqcategory/edit', 'user_token=' . $this->session->data['user_token'] . '&mpfaqcategory_id=' . $result['mpfaqcategory_id'] . $url, true)
			];
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_action'] = $this->language->get('column_action');
		$data['column_status'] = $this->language->get('column_status');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = [];
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('extension/mpfaq/mpfaqcategory', 'user_token=' . $this->session->data['user_token'] . '&sort=fd.name' . $url, true);
		$data['sort_sort_order'] = $this->url->link('extension/mpfaq/mpfaqcategory', 'user_token=' . $this->session->data['user_token'] . '&sort=f.sort_order' . $url, true);
		$data['sort_status'] = $this->url->link('extension/mpfaq/mpfaqquestion', 'user_token=' . $this->session->data['user_token'] . '&sort=f.status' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $mpfaqcategory_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/mpfaq/mpfaqcategory', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($mpfaqcategory_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($mpfaqcategory_total - $this->config->get('config_limit_admin'))) ? $mpfaqcategory_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $mpfaqcategory_total, ceil($mpfaqcategory_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/mpfaq/mpfaqcategory_list', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['mpfaqcategory_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_status'] = $this->language->get('entry_status');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = [];
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/mpfaq/mpfaqcategory', 'user_token=' . $this->session->data['user_token'] . $url, true)
		];

		if (!isset($this->request->get['mpfaqcategory_id'])) {
			$data['action'] = $this->url->link('extension/mpfaq/mpfaqcategory/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/mpfaq/mpfaqcategory/edit', 'user_token=' . $this->session->data['user_token'] . '&mpfaqcategory_id=' . $this->request->get['mpfaqcategory_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('extension/mpfaq/mpfaqcategory', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['mpfaqcategory_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$mpfaqcategory_info = $this->model_extension_mpfaq_mpfaqcategory->getMpfaqcategory($this->request->get['mpfaqcategory_id']);
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['mpfaqcategory_description'])) {
			$data['mpfaqcategory_description'] = $this->request->post['mpfaqcategory_description'];
		} elseif (isset($this->request->get['mpfaqcategory_id'])) {
			$data['mpfaqcategory_description'] = $this->model_extension_mpfaq_mpfaqcategory->getMpfaqcategoryDescriptions($this->request->get['mpfaqcategory_id']);
		} else {
			$data['mpfaqcategory_description'] = [];
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($mpfaqcategory_info)) {
			$data['sort_order'] = $mpfaqcategory_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($mpfaqcategory_info)) {
			$data['status'] = $mpfaqcategory_info['status'];
		} else {
			$data['status'] = true;
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/mpfaq/mpfaqcategory_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/mpfaq/mpfaqcategory')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['mpfaqcategory_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 64)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/mpfaq/mpfaqcategory')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

}
