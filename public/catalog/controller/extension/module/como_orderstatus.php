<?php
class ControllerExtensionModuleComoorderstatus extends Controller {
    public function order_history() {
        $json = array();
        
        $key = '';
        if (isset($this->request->post['key'])) {
            $key = $this->request->post['key'];
        } elseif (isset($this->request->get['key'])) {
            $key = $this->request->get['key'];
        }

        if ($key) {
            $data = $this->securityCheck($key);
            if ($data) {
                $order_id = isset($data['order_id']) ? $data['order_id'] : 0;
                $order_status_id = isset($data['order_status_id']) ? $data['order_status_id'] : 0;
                $notify = isset($data['notify']) ? $data['notify'] : '';
                $comment = isset($data['comment']) ? $data['comment'] : '';
                $override = isset($data['override']) ? $data['override'] : '';

                if (isset($order_id) && $order_id && isset($order_status_id) && $order_status_id) {
                    $_POST = $data;
                    $this->request->post = $_POST; // important, used in addOrderHistory below (before event for send mail)
                    //$this->request->post['copytoadmin'] = true;
                    // here add to history
                    $this->load->model('checkout/order');
                    $this->model_checkout_order->addOrderHistory($order_id, $order_status_id, $comment, $notify, $override);
                } else {
                    $json['error'] = 'Invalid data!';
                }
            } else {
                $json['error'] = 'Security check failed!';
            }
        } else {
            $json['error'] = 'No security key given!';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

	private function securityCheck($key) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "session` WHERE `session_id` = '" . $key . "'");
        if (isset($query->row['data'])) {
            $data = json_decode($query->row['data'], true);
        } else {
            $data = array();
        }

        $this->securityEnd($key); // better is to imidiatelly remove

        return $data;
	}

	private function securityEnd($key) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "session` WHERE `session_id` = '" . $key . "'");
	}
}
