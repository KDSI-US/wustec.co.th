<?php

class ControllerSaleTrackingNumber extends Controller
{
  private $error = array();

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

      return $this->load->view('sale/order_tracking_number', $data);
    }
  }
}
