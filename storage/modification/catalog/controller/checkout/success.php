<?php
/* This file is under Git Control by KDSI. */
class ControllerCheckoutSuccess extends Controller {
	public function index() {

		if($this->config->get('mpcheckout_status') && $this->config->get('mpcheckout_success_status')) {
			return new Action('mpcheckout/success');
		}
			
		$this->load->language('checkout/success');

		if(isset($this->session->data['shipping_method']))
		{
			$ship_data = $this->session->data['shipping_method'];
			if(!empty($ship_data) && isset($ship_data['mod_name']) && $ship_data['mod_name'] == 'hitshippo_dhlexpress')
			{
				$int_key = $this->config->get('shipping_hitshippo_dhlexpress_int_key');
				if (!empty($int_key)) 
				{
					$order_id = $this->session->data['order_id'];
					$customer_id = isset($this->session->data['customer_id'])?$this->session->data['customer_id']:'';
					$customer_address = $this->session->data['shipping_address'];
					$cus_f_name = $customer_address['firstname'];
					$cus_l_name = $customer_address['lastname'];
					$cus_company = empty($customer_address['company']) ? $cus_f_name : $customer_address['company'];
					$cus_addr_1 = $customer_address['address_1'];
					$cus_addr_2 = $customer_address['address_2'];
					$cus_city = $customer_address['city'];
					$cus_state = $customer_address['zone'];
					$cus_postcode = $customer_address['postcode'];
					$cus_country = $customer_address['iso_code_2'];
					$cus_phone = $this->customer->getTelephone();
					$cus_email = $this->customer->getEmail();
					$current_carrier = $ship_data['service_code'];

					$items = $this->cart->getProducts();
					$pack_products = array();
						
					foreach ( $items as $item ) 
					{
						$product = array();
						$product['product_name'] = $item['name'];
						$product['product_quantity'] = $item['quantity'];
						$product['price'] = number_format((float) round(($item['total']),2) , 2, '.', '');
						$product['width'] = round($item['width'],2);
						$product['height'] = round($item['height'],2);
						$product['depth'] = round($item['height'],2);
						$product['weight'] = round($item['weight'],2);
						$pack_products[] = $product;
					}
					$hitshippo_test = $this->config->get('shipping_hitshippo_dhlexpress_test');
					$mode = 'live';
					if(isset($hitshippo_test) && $hitshippo_test == 1)
					{
						$mode = 'test';
					}
					$auto_label = $this->config->get('shipping_hitshippo_dhlexpress_auto_label');
					$execution = 'manual';
					if(isset($auto_label) && $auto_label == 1)
					{
						$execution = 'auto';
					}

					$plt = $this->config->get('shipping_hitshippo_dhlexpress_plt');
					$air_bill = $this->config->get('shipping_hitshippo_dhlexpress_airway');
					$sd = $this->config->get('shipping_hitshippo_dhlexpress_sat');
					$cod = $this->config->get('shipping_hitshippo_dhlexpress_cod');
					$ship_content = $this->config->get('shipping_hitshippo_dhlexpress_shipment_content');
					$insurance = $this->config->get('shipping_hitshippo_dhlexpress_insurance');
					
					$pic_execu_type = 'manual';
					$pic_execu = (!$this->config->get('shipping_hitshippo_dhlexpress_pickup_auto')) ? '' : $this->config->get('shipping_hitshippo_dhlexpress_pickup_auto');
					if(isset($pic_execu) && $pic_execu == 1)
					{
						$pic_execu_type = 'auto';
					}
					$translation = (!empty($this->config->get('shipping_hitshippo_dhlexpress_translation')) && $this->config->get('shipping_hitshippo_dhlexpress_translation') == true) ? "Y" : "N";
					$data = array();
					$data['integrated_key'] = $int_key;
					$data['order_id'] = $order_id;
					$data['exec_type'] = $execution;
					$data['mode'] = $mode;
					$data['ship_price'] = $ship_data['cost'];
					$data['meta'] = array(
						"site_id" => $this->config->get('shipping_hitshippo_dhlexpress_key'),
						"password"  => $this->config->get('shipping_hitshippo_dhlexpress_password'),
						"accountnum" => $this->config->get('shipping_hitshippo_dhlexpress_account'),
						"t_company" => $cus_company,
						"t_address1" => $cus_addr_1,
						"t_address2" => $cus_addr_2,
						"t_city" => $cus_city,
						"t_state" => $cus_state,
						"t_postal" => $cus_postcode,
						"t_country" => $cus_country,
						"t_name" => $cus_f_name . ' '. $cus_l_name,
						"t_phone" => $cus_phone,
						"t_email" => $cus_email,
						"dutiable" => $this->config->get('shipping_hitshippo_dhlexpress_duty_type'),
						"insurance" => ($insurance == 1) ? "yes" : "no",
						"pack_this" => "Y",
						"products" => $pack_products,
						"pack_algorithm" => $this->config->get('shipping_hitshippo_dhlexpress_packing_type'),
						"max_weight" => $this->config->get('shipping_hitshippo_dhlexpress_wight_b'),
						"plt" => ($plt == 1) ? "Y" : "N",
						"airway_bill" => ($air_bill == 1) ? "Y" : "N",
						"sd" => ($sd == 1) ? "Y" : "N",
						"cod" => ($cod == 1) ? "Y" : "N",
						"service_code" => $current_carrier,
						"shipment_content" => ($ship_content ? $ship_content : 'Normal product'),
						"s_company" => $this->config->get('shipping_hitshippo_dhlexpress_company_name'),
						"s_address1" => $this->config->get('shipping_hitshippo_dhlexpress_address1'),
						"s_address2" => $this->config->get('shipping_hitshippo_dhlexpress_address2'),
						"s_city" => $this->config->get('shipping_hitshippo_dhlexpress_city'),
						"s_state" => $this->config->get('shipping_hitshippo_dhlexpress_state'),
						"s_postal" => $this->config->get('shipping_hitshippo_dhlexpress_postcode'),
						"s_country" => $this->config->get('shipping_hitshippo_dhlexpress_country_code'),
						"s_name" => $this->config->get('shipping_hitshippo_dhlexpress_shipper_name'),
						"s_phone" => $this->config->get('shipping_hitshippo_dhlexpress_phone_num'),
						"s_email" => $this->config->get('shipping_hitshippo_dhlexpress_email_addr'),
						"label_size" => $this->config->get('shipping_hitshippo_dhlexpress_dropoff_type'),
						"sent_email_to" => $this->config->get('shipping_hitshippo_dhlexpress_send_mail_to'),
						"pic_exec_type" => $pic_execu_type,
						"pic_loc_type" => (!$this->config->get('shipping_hitshippo_dhlexpress_pickup_loc_type')) ? '' : $this->config->get('shipping_hitshippo_dhlexpress_pickup_loc_type'),
						"pic_pac_loc" => (!$this->config->get('shipping_hitshippo_dhlexpress_pic_pack_lac')) ? '' : $this->config->get('shipping_hitshippo_dhlexpress_pic_pack_lac'),
						"pic_contact_per" => (!$this->config->get('shipping_hitshippo_dhlexpress_picper')) ? '' : $this->config->get('shipping_hitshippo_dhlexpress_picper'),
						"pic_contact_no" => (!$this->config->get('shipping_hitshippo_dhlexpress_piccon')) ? '' : $this->config->get('shipping_hitshippo_dhlexpress_piccon'),
						"pic_door_to" => (!$this->config->get('shipping_hitshippo_dhlexpress_pickup_del_type')) ? '' : $this->config->get('shipping_hitshippo_dhlexpress_pickup_del_type'),
						"pic_type" => (!$this->config->get('shipping_hitshippo_dhlexpress_pickup_type')) ? '' : $this->config->get('shipping_hitshippo_dhlexpress_pickup_type'),
						"pic_days_after" => (!$this->config->get('shipping_hitshippo_dhlexpress_pickup_days_after')) ? '' : $this->config->get('shipping_hitshippo_dhlexpress_pickup_days_after'),
						"pic_open_time" => (!$this->config->get('shipping_hitshippo_dhlexpress_pic_open_time')) ? '' : $this->config->get('shipping_hitshippo_dhlexpress_pic_open_time'),
						"pic_close_time" => (!$this->config->get('shipping_hitshippo_dhlexpress_pic_close_time')) ? '' : $this->config->get('shipping_hitshippo_dhlexpress_pic_close_time'),
						"translation" => $translation,
						"translation_key" => (!$this->config->get('shipping_hitshippo_dhlexpress_translation_key')) ? '' : $this->config->get('shipping_hitshippo_dhlexpress_translation_key'),
					);
					$curl = curl_init();
					curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode( $data));
					curl_setopt_array($curl, array(
						CURLOPT_URL            				=> "https://app.hitshipo.com/api/dhl_order.php",
						CURLOPT_RETURNTRANSFER 				=> false,
						CURLOPT_POST 		   						=> true,
						CURLOPT_USERAGENT 	   				=> 'api',
						CURLOPT_FORBID_REUSE 	   			=> true,
						CURLOPT_MAXREDIRS      				=> 10,
						CURLOPT_HEADER         				=> false,
						CURLOPT_TIMEOUT        				=> 30,
						CURLOPT_CONNECTTIMEOUT				=> 1,
						CURLOPT_DNS_CACHE_TIMEOUT     => 10,
						CURLOPT_FRESH_CONNECT        	=> true,
						CURLOPT_CUSTOMREQUEST  				=> 'POST',
					));
					curl_exec($curl);   
					curl_close($curl);
				}
			}
		}
			

		if (isset($this->session->data['order_id'])) {
			$this->cart->clear();

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
			unset($this->session->data['totals']);
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_basket'),
			'href' => $this->url->link('checkout/cart')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_checkout'),
			'href' => $this->url->link('checkout/checkout', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_success'),
			'href' => $this->url->link('checkout/success')
		);

		if ($this->customer->isLogged()) {
			$data['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('account/account', '', true), $this->url->link('account/order', '', true), $this->url->link('account/download', '', true), $this->url->link('information/contact'));
		} else {
			$data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));
		}

		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/success', $data));
	}
}