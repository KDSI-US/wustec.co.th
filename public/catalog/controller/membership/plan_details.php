<?php
class ControllerMembershipPlanDetails extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('membership/plan_details');
		
		$this->load->model('membership/prices');
		
		$this->load->model('tool/image');

		$this->document->addScript('catalog/view/javascript/modulepoints/membership.js');

		$this->document->addStyle('catalog/view/javascript/modulepoints/membership.css');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$mpplan_description = $this->config->get('mpplan_description');		

		$config_language_id = $this->config->get('config_language_id');
		$text_prices = !empty($mpplan_description[$config_language_id]['title']) ? $mpplan_description[$config_language_id]['title'] : $this->language->get('text_prices');

		$data['breadcrumbs'][] = array(
			'text' => $text_prices,
			'href' => $this->url->link('membership/prices')
		);


		if (isset($this->request->get['mpplan_id'])) {
			$mpplan_id = (int)$this->request->get['mpplan_id'];
		} else {
			$mpplan_id = 0;
		}

		$mpplan_info = $this->model_membership_prices->getMpplan($mpplan_id);
		if ($mpplan_info && $this->config->get('mpplan_status')) {
			$this->document->setTitle($mpplan_info['meta_title']);
			$this->document->setDescription($mpplan_info['meta_description']);
			$this->document->setKeywords($mpplan_info['meta_keyword']);

			$data['breadcrumbs'][] = array(
				'text' => $mpplan_info['name'],
				'href' => $this->url->link('membership/plan_details', 'mpplan_id=' .  $mpplan_id)
			);

			$data['heading_title'] = $mpplan_info['name'];

			$data['button_continue'] = $this->language->get('button_continue');

			$data['top_description'] = html_entity_decode($mpplan_info['top_description'], ENT_QUOTES, 'UTF-8');
			$data['bottom_description'] = html_entity_decode($mpplan_info['bottom_description'], ENT_QUOTES, 'UTF-8');
			
			$data['text_duration'] = $this->language->get('text_duration');			
			$data['text_model'] = $this->language->get('text_model');
			$data['text_regualr_price'] = $this->language->get('text_regualr_price');
			$data['text_membership_price'] = $this->language->get('text_membership_price');
			$data['text_no_product'] = $this->language->get('text_no_product');

			$data['button_view_product'] = $this->language->get('button_view_product');
			$data['button_cart'] = $this->language->get('button_cart');

			$filter_data = array(
				'mpplan_id'			=> $mpplan_info['mpplan_id'],
			);
			$products = $this->model_membership_prices->getProducts($filter_data);

			if($this->customer->isLogged()) {
				$customer_group_id = $this->customer->getGroupId();
			} else {
				$customer_group_id = $this->config->get('config_customer_group_id');
			}

			$data['products'] = array();
			foreach ($products as $product_info) {
				$plan_discount = $this->model_membership_prices->getMpplanDiscount($mpplan_info['mpplan_id'], $customer_group_id);

				if ($product_info['image']) {
					$thumb = $this->model_tool_image->resize($product_info['image'], $this->config->get('mpplan_product_width'), $this->config->get('mpplan_product_height'));
				} else {
					$thumb = $this->model_tool_image->resize('placeholder.png', $this->config->get('mpplan_product_width'),$this->config->get('mpplan_product_height'));
				}

				$price = false;
				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'));
				}
				if ((float)$product_info['special']) {
					$price = $this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax'));
				}

				if($price && $plan_discount['discount']) {
					$membership_price = $price - (($price * $plan_discount['discount']) / 100);
				} else {
					$membership_price = $price;
				}

				$save_price = $price - $membership_price;

				$data['products'][] = array(
					'product_id'  => $product_info['product_id'],
					'thumb'       => $thumb,
					'name'        => $product_info['name'],
					'model'       => $product_info['model'],
					'price'       => $this->currency->format($price, $this->session->data['currency']),
					'membership_price' 	=> $this->currency->format($membership_price, $this->session->data['currency']),
					'save_price'  		=> $this->currency->format($save_price, $this->session->data['currency']),
					'href'        		=> $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
				);
			}

			$data['custom_themename'] = $this->model_membership_prices->getactiveTheme();
			
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('membership/plan_details', $data));
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('membership/plan_details', 'mpplan_id=' . $mpplan_id)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');

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
