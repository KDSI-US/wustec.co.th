<?php
/* This file is under Git Control by KDSI. */

class ControllerSaleTrackingNumber extends Controller
{
  private $error = array();

  public function callTrackingInfoTab()
  {
    $this->load->language('sale/tracking_number');

    if (isset($this->request->get['order_id'])) {
      $order_id = $this->request->get['order_id'];
    } else {
      $order_id = 0;
    }

    $this->load->model('sale/tracking_number');

    $tracking_info = $this->model_sale_tracking_number->getTrackingNumbers($order_id);

    if ($tracking_info) {
      $data['user_token'] = $this->session->data['user_token'];
      $data['order_id'] = (int)$this->request->get['order_id'];
      $data['tracking_info'] = $tracking_info;
      $data['customer_info'] = $this->model_sale_tracking_number->getCustomerInfo($order_id);
			$data['shipping_couriers'] = $this->model_sale_tracking_number->getShippingCourier();
    }

    /* The URL we send API requests to */
		$data['api_url'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;

    /* API login */
    /*
		$this->load->model('user/api');
    $api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

    if ($api_info && $this->user->hasPermission('modify', 'sale/order')) {
      $session = new Session($this->config->get('session_engine'), $this->registry);
      $session->start();
      $data['api_token'] = $session->getId();
    } else {
      $data['api_token'] = '';
    }

    $this->load->model('sale/order');
    $order_info = $this->model_sale_order->getOrder($order_id);
    $data['store_id'] = $order_info['store_id'];
		*/

    $this->response->setOutput($this->load->view('sale/order_tracking_number', $data));
	}
	
  public function trackingInfoTab()
  {
    $this->load->language('sale/tracking_number');

    if (isset($this->request->get['order_id'])) {
      $order_id = $this->request->get['order_id'];
    } else {
      $order_id = 0;
    }

    $this->load->model('sale/tracking_number');

    $tracking_info = $this->model_sale_tracking_number->getTrackingNumbers($order_id);

    if ($tracking_info) {
      $data['user_token'] = $this->session->data['user_token'];
      $data['order_id'] = (int)$this->request->get['order_id'];
      $data['tracking_info'] = $tracking_info;
      $data['customer_info'] = $this->model_sale_tracking_number->getCustomerInfo($order_id);

      $data['shipping_couriers'] = $this->model_sale_tracking_number->getShippingCourier();
    }

    /* Set order_id when Tracking Number Not Exist  */
    $data['user_token'] = $this->session->data['user_token'];
    $data['order_id'] = (int)$this->request->get['order_id'];
    
    /* The URL we send API requests to  */
    $data['api_url'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;

		return $this->load->view('sale/order_tracking_number', $data);
  }
}
