<?php
/* This file is under Git Control by KDSI. */

class ControllerAccountDashboardAccountDashboard extends Controller
{
	private $ssl;

	public function __construct($resgistry)
	{
		parent::__construct($resgistry);
		$this->ssl = true;

		if (VERSION <= '2.1.0.2') {
			$this->ssl = 'SSL';
		}
	}

	public function index()
	{
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account_dashboard/account_dashboard');

		$this->load->model('account/order');
		$this->load->model('account/download');
		$this->load->model('account/reward');
		$this->load->model('account/address');
		$this->load->model('account/transaction');
		$this->load->model('tool/image');
		$this->load->model('account/customer');

		if ($this->config->get('theme_default_directory')) {
			$data['theme_name'] = $this->config->get('theme_default_directory');
		} else if ($this->config->get('config_template')) {
			$data['theme_name'] = $this->config->get('config_template');
		} else {
			$data['theme_name'] = 'default';
		}

		if (empty($data['theme_name'])) {
			$data['theme_name'] = 'default';
		}

		if ($this->config->get('account_dashboard_template')) {
			$data['template_id'] = $this->config->get('account_dashboard_template');
		} else {
			$data['template_id'] = 'template_1';
		}

		if (file_exists(DIR_TEMPLATE . $data['theme_name'] . '/stylesheet/account_dashboard.css')) {
			$this->document->addStyle('catalog/view/theme/' . $data['theme_name'] . '/stylesheet/account_dashboard.css');
		} else {
			$this->document->addStyle('catalog/view/stylesheet/account_dashboard.css');
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', '', $this->ssl)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', $this->ssl)
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['customer_name'] = $this->customer->getFirstName() . ' ' . $this->customer->getLastName();
		$data['customer_email'] = $this->customer->getEmail();
		$data['customer_telephone'] = $this->customer->getTelephone();
		$data['customer_tax_id'] = $this->customer->getTaxId();
		$data['customer_seller_permit'] = $this->customer->getSellerPermit();
		$data['customer_approved'] = $this->customer->getApproved();
		$data['customer_denied'] = $this->customer->getDenied();
		$data['customer_has_seller_permit_file'] = $this->customer->hasSellerPermitFile();
		$data['alert_verification_info'] = $this->config->get('account_dashboard_alert_verification_info');


		$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

		if (!empty($customer_info['profile_picture']) && is_file(DIR_IMAGE . $customer_info['profile_picture'])) {
			$data['profile_picture_thumb'] = $this->model_tool_image->resize($customer_info['profile_picture'], 100, 100);
		} else {
			$data['profile_picture_thumb'] = '';
		}

		// Configuration Fields
		$data['display_orders'] = $this->config->get('account_dashboard_latest_orders');

		$data['display_total_orders'] = $this->config->get('account_dashboard_total_orders');

		$data['display_total_wishlist'] = $this->config->get('account_dashboard_total_wishlists');

		$data['display_reward_points'] = $this->config->get('account_dashboard_total_rewardpoints');

		$data['display_total_transactions'] = $this->config->get('account_dashboard_total_transactions');

		$data['picture_status'] = $this->config->get('account_dashboard_customer_picture_status');

		$data['total_orders'] = $this->model_account_order->getTotalOrders();
		$data['total_reward_points'] = $this->model_account_reward->getTotalPoints();
		$data['total_transactions'] = $this->model_account_transaction->getTotalAmount();
		$data['total_transactions'] = $this->currency->format($data['total_transactions'], $this->session->data['currency']);


		if (VERSION > '2.0.3.1') {
			$this->load->model('account/wishlist');
			$data['total_wishlist'] = $this->model_account_wishlist->getTotalWishlist();
		} else {
			$data['total_wishlist'] = (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0);
		}

		$data['display_total_panels'] = 0;
		if ($data['display_total_orders']) {
			$data['display_total_panels'] += 1;
		}

		if ($data['display_total_wishlist']) {
			$data['display_total_panels'] += 1;
		}

		if ($data['display_reward_points']) {
			$data['display_total_panels'] += 1;
		}

		if ($data['display_total_transactions']) {
			$data['display_total_panels'] += 1;
		}

		$col_class = 'col-lg-12 xl-100';
		if ($data['display_total_panels'] == 1) {
			$data['col_class'] = 'col-lg-12 xl-100';
		} else if ($data['display_total_panels'] == 2) {
			$data['col_class'] = 'col-lg-6 xl-50';
		} else if ($data['display_total_panels'] == 3) {
			$data['col_class'] = 'col-lg-4 xl-33';
		} else if ($data['display_total_panels'] == 4) {
			$data['col_class'] = 'col-lg-6 xl-50';
		}

		$data['link_edit'] = $this->url->link('account/edit', '', $this->ssl);
		$data['link_password'] = $this->url->link('account/password', '', $this->ssl);
		$data['link_address'] = $this->url->link('account/address', '', $this->ssl);
		$data['link_return'] = $this->url->link('account/return', '', $this->ssl);
		$data['link_transaction'] = $this->url->link('account/transaction', '', $this->ssl);
		$data['link_newsletter'] = $this->url->link('account/newsletter', '', $this->ssl);
		$data['link_recurring'] = $this->url->link('account/recurring', '', $this->ssl);
		$data['link_wishlist'] = $this->url->link('account/wishlist', '', $this->ssl);
		$data['link_order'] = $this->url->link('account/order', '', $this->ssl);
		$data['link_download'] = $this->url->link('account/download', '', $this->ssl);
		$data['link_logout'] = $this->url->link('account/logout', '', $this->ssl);

		$data['view_order'] = $this->url->link('account/order', '', $this->ssl);
		$data['view_wishlists'] = $this->url->link('account/wishlist', '', $this->ssl);
		$data['view_reward'] = $this->url->link('account/reward', '', $this->ssl);
		$data['view_transactions'] = $this->url->link('account/transaction', '', $this->ssl);

		$data['orders'] = array();

		$this->load->model('account/order');
		$results = $this->model_account_order->getOrders(0, 10);

		foreach ($results as $result) {
			$product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);
			$voucher_total = $this->model_account_order->getTotalOrderVouchersByOrderId($result['order_id']);

			$data['orders'][] = array(
				'order_id'   => $result['order_id'],
				'name'       => $result['firstname'] . ' ' . $result['lastname'],
				'status'     => $result['status'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'tracking_number' => $this->model_account_order->getMaxOrderTrackingNumber($result['order_id']),
				'products'   => ($product_total + $voucher_total),
				'total'      => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				'view'       => $this->url->link('account/order/info', 'order_id=' . $result['order_id'], $this->ssl),
			);
		}

		$data['themecolor'] = $this->config->get('account_dashboard_theme_color');
		$data['textcolor'] = $this->config->get('account_dashboard_text_color');
		$data['customcss'] = $this->config->get('account_dashboard_customcss');

		$data['custom_links'] = array();
		$menu_links = (array)$this->config->get('account_dashboard_link');
		if ($menu_links) {
			foreach ($menu_links as $menu_link) {
				if ($menu_link['status']) {

					$image_thumb = '';
					if ($menu_link['type'] == 'image') {
						if (is_file(DIR_IMAGE . $menu_link['image'])) {
							$image_thumb = $this->model_tool_image->resize($menu_link['image'], 50, 50);
						} else {
							$image_thumb = $this->model_tool_image->resize('no_image.png', 50, 50);
						}
					}

					$data['custom_links'][] = array(
						'title'			=> isset($menu_link['description'][$this->config->get('config_language_id')]['title']) ? $menu_link['description'][$this->config->get('config_language_id')]['title'] : '',
						'link'			=> $menu_link['link'],
						'type'			=> $menu_link['type'],
						'icon'			=> $menu_link['icon_class'],
						'image'			=> $image_thumb,
						'sort_order'	=> $menu_link['sort_order'],
					);
				}
			}
		}

		function linksSort($a, $b)
		{
			return $a['sort_order'] - $b['sort_order'];
		}

		usort($data['custom_links'], 'linksSort');

		// Affiliate Links
		$affiliate_info = $this->model_account_customer->getAffiliate($this->customer->getId());
		if (empty($affiliate_info)) {
			$affiliate_account = 0;
		} else {
			$affiliate_account = 1;
		}

		$data['affiliate_links'] = array();
		$affiliate_links = (array)$this->config->get('account_dashboard_affiliate_link');
		if ($affiliate_links) {
			foreach ($affiliate_links as $affiliate_link) {
				if ($affiliate_link['status']) {
					if ($affiliate_link['affiliate_type'] == $affiliate_account) {
						$image_thumb = '';
						if ($affiliate_link['type'] == 'image') {
							if (is_file(DIR_IMAGE . $affiliate_link['image'])) {
								$image_thumb = $this->model_tool_image->resize($affiliate_link['image'], 50, 50);
							} else {
								$image_thumb = $this->model_tool_image->resize('no_image.png', 50, 50);
							}
						}

						$data['affiliate_links'][] = array(
							'title'			=> isset($affiliate_link['description'][$this->config->get('config_language_id')]['title']) ? $affiliate_link['description'][$this->config->get('config_language_id')]['title'] : '',
							'link'			=> $affiliate_link['link'],
							'type'			=> $affiliate_link['type'],
							'icon'			=> $affiliate_link['icon_class'],
							'image'			=> $image_thumb,
							'sort_order'	=> $affiliate_link['sort_order'],
						);
					}
				}
			}
		}

		function linksAffiliateSort($a, $b)
		{
			return $a['sort_order'] - $b['sort_order'];
		}

		usort($data['custom_links'], 'linksAffiliateSort');

		if (isset($data['theme_name']) && $data['theme_name'] == 'journal2') {
			$data['journal_class'] = 'journal-dashboard';
		} else {
			$data['journal_class'] = '';
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		if (VERSION < '2.2.0.0') {
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account_dashboard/' . $data['template_id'] . '.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account_dashboard/' . $data['template_id'] . '.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/account_dashboard/' . $data['template_id'] . '.tpl', $data));
			}
		} else {
			$this->response->setOutput($this->load->view('account_dashboard/' . $data['template_id'], $data));
		}
	}
}