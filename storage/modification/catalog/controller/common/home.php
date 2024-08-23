<?php
/* This file is under Git Control by KDSI. */
class ControllerCommonHome extends Controller {
	public function index() {

                // iSense SEO Meta
                $meta_title = $this->config->get('config_meta_title');
                $meta_description = $this->config->get('config_meta_description');
                $meta_keywords = $this->config->get('config_meta_keyword');

                $meta_title = is_array($meta_title) && isset($meta_title[$this->config->get('config_language_id')]) ? $meta_title[$this->config->get('config_language_id')] : $meta_title;
                $meta_description = is_array($meta_description) && isset($meta_description[$this->config->get('config_language_id')]) ? $meta_description[$this->config->get('config_language_id')] : $meta_description;
        		$meta_keywords = is_array($meta_keywords) && isset($meta_keywords[$this->config->get('config_language_id')]) ? $meta_keywords[$this->config->get('config_language_id')] : $meta_keywords;
			
		
                $this->document->setTitle($meta_title);
			
		
                $this->document->setDescription($meta_description);
			
		
                $this->document->setKeywords($meta_keywords);
			

		if (isset($this->request->get['route'])) {
			$this->document->addLink($this->config->get('config_url'), 'canonical');
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');


		$int_key = $this->config->get('shipping_hitshippo_dhlexpress_int_key');
				if(isset($_GET['h1t_updat3_0rd3r']) && isset($_GET['key']) && isset($_GET['action'])){
					$order_id = $_GET['h1t_updat3_0rd3r'];
					$key = $_GET['key'];
					$action = $_GET['action'];
					$order_ids = explode(",",$order_id);
					
					if(isset($int_key) && ($int_key == $key)){
						if($action == 'processing'){
							foreach ($order_ids as $order_id) {
								$order_status_id = 2;
	$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', date_added = NOW()");
	$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
							}
						}else if($action == 'completed'){
							foreach ($order_ids as $order_id) {
								$order_status_id = 5;
	$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', date_added = NOW()");
	$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
								  	
							}
						}
					}
					die();
				}

		if(isset($_GET['h1t_updat3_sh1pp1ng']) && isset($_GET['key']) && isset($_GET['user_id']) && isset($_GET['carrier']) && isset($_GET['track']) && isset($_GET['pic_status'])){

					$order_id = $_GET['h1t_updat3_sh1pp1ng'];
					$key = $_GET['key'];
					$user_id = $_GET['user_id'];
					$carrier = $_GET['carrier'];
					$track = $_GET['track'];
					$pic_status = $_GET['pic_status'];
					$output['status'] = 'success';
					$output['tracking_num'] = $track;
					$output['label'] = "https://app.hitshipo.com/api/shipping_labels/".$user_id."/".$carrier."/order_".$order_id."_track_".$track."_label.pdf";
					$output['invoice'] = "https://app.hitshipo.com/api/shipping_labels/".$user_id."/".$carrier."/order_".$order_id."_track_".$track."_invoice.pdf";

				if(isset($int_key) && ($int_key == $key)){
		//$this->db->query("DELETE FROM " . DB_PREFIX . "hitshippo_dhl_details_new WHERE order_id = '" .$order_id. "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "hitshippo_dhl_details_new SET order_id = '".$order_id."', tracking_num = '" . $output['tracking_num']. "', shipping_label = '".$output['label']."', invoice = '".$output['invoice']."'");
				if(isset($pic_status) && ($pic_status == 'success')){
					$pic_res['confirm_no'] = $_GET['confirm_no'];
					$pic_res['ready_time'] = $_GET['ready_time'];
					$pic_res['pickup_date'] = $_GET['pickup_date'];
					//$this->db->query("DELETE FROM " . DB_PREFIX . "hitshippo_dhl_pickup_details WHERE order_id = '" .$order_id. "'");
					$this->db->query("INSERT INTO " . DB_PREFIX . "hitshippo_dhl_pickup_details SET order_id = '".$order_id."', status = 'success', confirm_no = '" . $pic_res['confirm_no']. "', ready_time = '".$pic_res['ready_time']."', pickup_date = '".$pic_res['pickup_date']."'");
					}
				}
				die();
			}
			
		$this->response->setOutput($this->load->view('common/home', $data));
	}
}
