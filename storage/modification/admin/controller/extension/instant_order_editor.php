<?php
/* This file is under Git Control by KDSI. */

class ControllerExtensionInstantOrderEditor extends Controller
{
	public function index()
	{
		/* modify tracking no from advanced orders list */
		$this->load->model('extension/instant_order_editor');
		if (isset($this->request->get['type']) && $this->request->get['type'] == "change_trackingno") {
			echo $this->model_extension_instant_order_editor->changeTrackingNo((int)$this->request->get['order_id'], $this->request->get['tracking_no'], $this->request->get['language']);
			die();
		}

		if (isset($this->request->get['type']) && $this->request->get['type'] == "change_order_status") {
			echo $this->model_extension_instant_order_editor->changeOrderStatus((int)$this->request->get['order_id'], $this->request->get['order_status_id']);
			die();
		}

		$data = [];
		$this->load->language('extension/instant_order_editor');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->addStyle('view/stylesheet/ioe.css');
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('setting/store', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['header'] = $this->load->controller('common/header');
		$data['token'] = $this->session->data['user_token'];
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['table_url'] = 'index.php?route=extension/instant_order_editor/table&user_token=' . $this->session->data['user_token'];

		$api_token = $this->getApiToken();
		$data['api_token'] = $this->getApiToken();
		$host = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;
		$data['api_url'] = $host . 'index.php?' . 'api_token=' . $data['api_token'];
		$host = $this->request->server['HTTPS'] ? HTTPS_SERVER : HTTP_SERVER;
		$data['url'] = $host . 'index.php?' . 'user_token=' . $this->session->data['user_token'];

		$this->response->setOutput($this->load->view('extension/instant_order_editor', $data));
	}

	public function table()
	{
		/* shorter var */
		$get = $this->request->get;
		$post = $this->request->post;
		$this->load->language('extension/instant_order_editor');
		/* $this->document->setTitle($this->language->get('heading_title')); */
		$this->load->model('extension/instant_order_editor');
		$this->load->model('localisation/currency');
		$this->load->model('customer/customer_group');
		$this->load->model('localisation/order_status');
		$this->load->model('setting/store');

		$page  = isset($get['page']) ? (int)$get['page'] : 1;
		$data = [
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin'),
			'order' => isset($post['sort']) ? $post['sort'] : 'o.order_id#desc',
			'filter' => isset($post['filter']) ? $post['filter'] : []
		];
		$data['product_autocomplete_url'] = 'index.php?route=catalog/product/autocomplete&user_token=' . $this->session->data['user_token'];
		$data['customer_autocomplete_url'] = 'index.php?route=customer/customer/autocomplete&user_token=' . $this->session->data['user_token'];
		if (isset($get['data'])) {
			$data = array_merge($get['data'], $data);
		}
		$currency = $this->model_localisation_currency->getCurrencyByCode($this->config->get('config_currency'));
		$data['orders'] = $this->model_extension_instant_order_editor->getOrders($data);

		foreach ($data['orders'] as &$order) {
			$order['amount'] = $this->currency->format($order['total'], $order['currency_code'], $order['currency_value']);
			$order['total'] = $this->currency->format($order['total'], $currency['code'], $currency['value']);
			$order['tracking_no'] = $this->model_extension_instant_order_editor->getMaxOrderTrackingNumber($order['order_id']);
			foreach ($order['products'] as &$product) {
				$product['price'] = $this->currency->format($product['price'] + $product['tax'], $order['currency_code'], $order['currency_value']);
			}
			/* used to display the date in the column */
			$order['modified_date'] = date($this->language->get('date_format_short'), strtotime($order['date_modified']));
			$order['added_date'] = date($this->language->get('date_format_short'), strtotime($order['date_added']));
		}

		$pagination = new Pagination();
		$pagination->total = $this->model_extension_instant_order_editor->getTotalOrders($data);
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');

		/* Remove path and page query attributes */
		$url = $this->request->server['QUERY_STRING'];
		$url = preg_replace('/(&(amp;)?)?route=[^&]+/', '', $url);
		$url = preg_replace('/&(amp;)?page=[0-9]+/', '', $url);
		$url = preg_replace('/&amp;/', '&', $url);

		$pagination->url = $this->url->link(
			'extension/instant_order_editor/table',
			$url . '&page={page}',
			true
		);
		$data['pagination'] = $pagination->render();
		$data['results'] = $this->results_stats($pagination->total, $page);
		$data['currencies'] = $this->model_localisation_currency->getCurrencies();
		$data['stores'] = $this->model_setting_store->getStores();
		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		$data['url'] = ($this->request->server['HTTPS'] ? HTTPS_SERVER : HTTP_SERVER) .
			'index.php?' .
			'user_token=' . $this->session->data['user_token'];
		$data['link'] = 'index.php?route=extension/instant_order_editor&user_token=' . $this->session->data['user_token'];

		$this->response->setOutput($this->load->view('extension/ioe_table', $data));
	}

	public function edit()
	{
	}

	public function delete()
	{
		$this->load->language('extension/instant_order_editor');
		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		}
		$order_id = 0;
		$this->response->addHeader('Content-Type: application/json');
		if ($this->user->hasPermission('modify', 'sale/order')) {
			$this->model_checkout_order->deleteOrder($order_id);
			$json['success'] = $this->language->get('text_success');
		} else {
			$json['error'] = $this->language->get('error_not_found');
		}
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Prepare the result stats for the page
	 * @param  int $total
	 * @param  int $page
	 * @return string
	 */
	protected function results_stats($total, $page)
	{
		$limit = $this->config->get('config_limit_admin');
		$this->load->language('extension/instant_order_editor');
		$text = $this->language->get('text_pagination');
		$from = ($total) ? (($page - 1) * $limit) + 1 : 0;
		$to = $total;
		if ((($page - 1) * $limit) <= ($total - $limit)) {
			$to = (($page - 1) * $limit) + $limit;
		}
		return sprintf($text, $from, $to, $total, ceil($total / $limit));
	}

	protected function getApiToken()
	{
		$this->load->model('user/api');
		$api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));
		if ($api_info && $this->user->hasPermission('modify', 'sale/order')) {
			$session = new Session($this->config->get('session_engine'), $this->registry);
			$session->start();
			$this->model_user_api->deleteApiSessionBySessionId($session->getId());
			$this->model_user_api->addApiSession(
				$api_info['api_id'],
				$session->getId(),
				$this->request->server['REMOTE_ADDR']
			);
			$session->data['api_id'] = $api_info['api_id'];
			return $session->getId();
		}
		return null;
	}
}
