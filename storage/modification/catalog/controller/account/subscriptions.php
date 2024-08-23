<?php
/* This file is under Git Control by KDSI. */
class ControllerAccountSubscriptions extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/subscriptions', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->document->addScript('catalog/view/javascript/modulepoints/membership.js');

		$this->document->addStyle('catalog/view/javascript/modulepoints/membership.css');		

		$this->load->language('account/subscriptions');
		
		$this->load->model('membership/subscriptions');

		$this->document->setTitle($this->language->get('heading_title'));

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
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/subscriptions', '', true)
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		} 

		if($this->config->get('mpplan_status')) {
			$data['heading_title'] = $this->language->get('heading_title');
			
			$data['text_no_results'] = $this->language->get('text_no_results');
			
			$data['text_myplan'] = $this->language->get('text_myplan');
			$data['text_current_priority'] = $this->language->get('text_current_priority');
			$data['text_current_plan'] = $this->language->get('text_current_plan');
			$data['text_current_start_date'] = $this->language->get('text_current_start_date');
			$data['text_current_end_date'] = $this->language->get('text_current_end_date');
			$data['text_current_status'] = $this->language->get('text_current_status');
			$data['text_current_action'] = $this->language->get('text_current_action');

			$data['text_default'] = $this->language->get('text_default');
			$data['button_view'] = $this->language->get('button_view');

			// Current Plans
			$current_plans = $this->model_membership_subscriptions->getCurrentPlans($this->customer->getId());
			$data['current_plans'] = array();
			foreach ($current_plans as $current_plan) {
				$data['current_plans'][] = array(
					'mpplan_customer_id'		=> $current_plan['mpplan_customer_id'],
					'active'		=> $current_plan['active'],
					'plan_name'		=> $current_plan['plan_name'],
					'start_date'	=> date($this->language->get('active_date_format'), strtotime($current_plan['start_date'])),
					'end_date'		=> date($this->language->get('active_date_format'), strtotime($current_plan['end_date'])),
					'status'		=> $this->language->get('text_activate'),
					'href'			=> $this->url->link('membership/plan_details','mpplan_id='. $current_plan['mpplan_id'], true),
				);
			}	

			$data['custom_themename'] = $this->model_membership_subscriptions->getactiveTheme();

			$data['planactive_controller'] = $this->load->controller('account/subscriptions/ActivePlan');
			$data['plan_history_controller'] = $this->load->controller('account/subscriptions/PlanHistory');
			$data['payment_history_controller'] = $this->load->controller('account/subscriptions/PaymentHistory');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
			
			$this->response->setOutput($this->load->view('account/subscriptions', $data));
		} else {
			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['continue'] = $this->url->link('account/account');

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

	public function ActivePlan() {
		$this->load->language('account/subscriptions');
		
		$this->load->model('membership/subscriptions');
		
		$this->load->model('account/order');

		$data['text_active_info'] = $this->language->get('text_active_info');
		$data['text_active_plan'] = $this->language->get('text_active_plan');
		$data['text_active_startdate'] = $this->language->get('text_active_startdate');
		$data['text_active_expdate'] = $this->language->get('text_active_expdate');
		$data['text_active'] = $this->language->get('text_active');
		$data['text_no_results'] = $this->language->get('text_no_results');

		// Active Plan
		$active_plan = $this->model_membership_subscriptions->getActivePlan($this->customer->getId());
		
		if($active_plan) {
			$data['active_plan']['plan_name'] = sprintf($this->language->get('text_active_plan_name'), $active_plan['plan_name']);
			$data['active_plan']['name'] = $active_plan['plan_name'];
			$data['active_plan']['start_date'] = date($this->language->get('active_date_format'), strtotime($active_plan['start_date']));
			$data['active_plan']['end_date'] = date($this->language->get('active_date_format'), strtotime($active_plan['end_date']));
			$data['active_plan']['plan_info'] = !empty($active_plan['plan_info']) ? json_decode($active_plan['plan_info'], 1) : array();
		} else {
			$data['active_plan'] = array();
		}

		if(isset($this->request->get['ajax'])) {
			$this->response->setOutput($this->load->view('account/subscriptions_active', $data));
		} else {
			return $this->load->view('account/subscriptions_active', $data);
		}
	}

	public function PlanHistory() {
		$this->load->language('account/subscriptions');
		
		$this->load->model('membership/subscriptions');
		
		$this->load->model('account/order');
		
		$this->load->model('membership/prices');

		$data['text_no_results'] = $this->language->get('text_no_results');

		$data['text_plan_history'] = $this->language->get('text_plan_history');
		$data['text_history_planname'] = $this->language->get('text_history_planname');
		$data['text_history_start_date'] = $this->language->get('text_history_start_date');
		$data['text_history_expired_date'] = $this->language->get('text_history_expired_date');
		$data['text_history_status'] = $this->language->get('text_history_status');
		$data['text_history_action'] = $this->language->get('text_history_action');
		
		$data['button_renew'] = $this->language->get('button_renew');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$plan_limit = 10;

		// OLD Plans
		$old_plans = $this->model_membership_subscriptions->getOldPlans($this->customer->getId(), ($page - 1) * $plan_limit, $plan_limit);
		$data['old_plans'] = array();
		foreach ($old_plans as $old_plan) {
			$order_product_info = $this->model_account_order->getOrderProduct($old_plan['order_id'], $old_plan['order_product_id']);

			$plan_info = $this->model_membership_prices->getMpplan($old_plan['mpplan_id']);

			if ($order_product_info && $plan_info) {
				$reorder = true;
				$product_id = $order_product_info['product_id'];
			} else {
				$reorder = false;
				$product_id = 0;
			}

			$data['old_plans'][] = array(
				'mpplan_id'		=> $old_plan['mpplan_id'],
				'mpplan_customer_id'		=> $old_plan['mpplan_customer_id'],
				'plan_name'		=> $old_plan['plan_name'],
				'start_date'	=> date($this->language->get('active_date_format'), strtotime($old_plan['start_date'])),
				'end_date'		=> date($this->language->get('active_date_format'), strtotime($old_plan['end_date'])),
				'status'		=> $this->language->get('text_deactivate'),
				'product_id'	=> $product_id,
				'reorder'		=> $reorder,
			);
		}

		$history_total = $this->model_membership_subscriptions->getTotalOldPlans($this->customer->getId());

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = $plan_limit;
		$pagination->url = $this->url->link('account/subscriptions/PlanHistory', '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * $plan_limit) + 1 : 0, ((($page - 1) * $plan_limit) > ($history_total - $plan_limit)) ? $history_total : ((($page - 1) * $plan_limit) + $plan_limit), $history_total, ceil($history_total / $plan_limit));

		if(isset($this->request->get['ajax'])) {
			$this->response->setOutput($this->load->view('account/subscriptions_plan_history', $data));
		} else {
			return $this->load->view('account/subscriptions_plan_history', $data);
		}
	}

	public function PaymentHistory() {
		$this->load->language('account/subscriptions');
		
		$this->load->model('membership/subscriptions');

		$data['text_no_results'] = $this->language->get('text_no_results');		
		
		$data['text_payment_history'] = $this->language->get('text_payment_history');
		$data['text_payment_planname'] = $this->language->get('text_payment_planname');
		$data['text_payment_orderdate'] = $this->language->get('text_payment_orderdate');
		$data['text_payment_duration'] = $this->language->get('text_payment_duration');
		$data['text_payment_price'] = $this->language->get('text_payment_price');
		$data['text_payment_status'] = $this->language->get('text_payment_status');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$plan_limit = 10;
		
		// Payment Histories
		$payment_histories = $this->model_membership_subscriptions->getPaymentHistoryPlans($this->customer->getId(), ($page - 1) * $plan_limit, $plan_limit);
		$data['payment_histories'] = array();
		foreach ($payment_histories as $payment_history) {
			$duration = '';
			switch ($payment_history['duration_type']) {
				case 'd':
				$duration = sprintf($this->language->get('text_days'), $payment_history['duration_value']);
			    break;
			    case 'm':
				$duration = sprintf($this->language->get('text_months'), $payment_history['duration_value']);
			    break;
			    case 'y':
				$duration = sprintf($this->language->get('text_years'), $payment_history['duration_value']);
			    break;
			}

			$data['payment_histories'][] = array(
				'plan_name'			=> $payment_history['plan_name'],
				'date_added'		=> date($this->language->get('active_date_format'), strtotime($payment_history['date_added'])),
				'price'				=> $this->currency->format($payment_history['price'], $payment_history['currency_code'], $payment_history['currency_value']),
				'order_status'		=> $payment_history['order_status'],
				'duration'			=> $duration,
			);
		}

		$history_total = $this->model_membership_subscriptions->getTotalPaymentHistoryPlans($this->customer->getId());

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = $plan_limit;
		$pagination->url = $this->url->link('account/subscriptions/PaymentHistory', '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * $plan_limit) + 1 : 0, ((($page - 1) * $plan_limit) > ($history_total - $plan_limit)) ? $history_total : ((($page - 1) * $plan_limit) + $plan_limit), $history_total, ceil($history_total / $plan_limit));

		if(isset($this->request->get['ajax'])) {
			$this->response->setOutput($this->load->view('account/subscriptions_payment_history', $data));
		} else {
			return $this->load->view('account/subscriptions_payment_history', $data);
		}
	}

	public function addActivePlan() {
		$this->load->language('account/subscriptions');
		
		$this->load->model('membership/subscriptions');

		$json = array();

		if(isset($this->request->post['id'])) {
			$mpplan_customer_id = $this->request->post['id'];
		} else {
			$mpplan_customer_id = 0;
		}

		$customer_plan_info = $this->model_membership_subscriptions->getCustomerPlan($this->customer->getId(), $mpplan_customer_id);
		
		if(!$customer_plan_info) {
			$json['redirect'] = $this->url->link('account/subscriptions', '', true);
		}

		if(!$json) {
			$this->model_membership_subscriptions->addDefaultPlan($this->customer->getId(), $mpplan_customer_id);

			$default_plan = $this->model_membership_subscriptions->getDefaultPlan($this->customer->getId());

			if($default_plan) {
				$json['success'] = $this->language->get('text_success_active');
				$json['default_id'] = $default_plan['mpplan_customer_id'];
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


}