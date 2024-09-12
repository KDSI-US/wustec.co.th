<?php
/* This file is under Git Control by KDSI. */
class ControllerInformationInformation extends Controller
{
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
		$this->load->language('information/contact');

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

			if($information_id == 34) {
				$this->response->setOutput($this->load->view('information/registration', $data));
			} elseif($information_id == 35) {
				$this->response->setOutput($this->load->view('information/report', $data));
			} else {
				$this->response->setOutput($this->load->view('information/information', $data));
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
}
