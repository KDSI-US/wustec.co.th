<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestApiAccountWishList extends OcrestapiController {
	public function index() {

		$this->checkPlugin();

	

		$language=$this->load->language('account/wishlist');

		$this->load->model('account/wishlist');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['products'] = array();

		$results = $this->model_account_wishlist->getWishlist();
 
		foreach ($results as $result) {
			$product_info = $this->model_catalog_product->getProduct($result['product_id']);
			// print_r($product_info);
			// die;
			if ($product_info) {
				if ($product_info['image']) {
					$image = $this->model_tool_image->resize($product_info['image'], $this->config->get('restapi_' . $this->config->get('config_theme') . '_image_wishlist_width_mobile'), $this->config->get('restapi_' . $this->config->get('config_theme') . '_image_wishlist_height_mobile'));
				} else {
					$image = false;
				}

				if ($product_info['quantity'] <= 0) {
					$stock = $product_info['stock_status'];
				} elseif ($this->config->get('config_stock_display')) {
					$stock = $product_info['quantity'];
				} else {
					$stock = $this->language->get('text_instock');
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$product_info['special']) {
					$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				$options=$this->model_catalog_product->getProductOptions($product_info['product_id']);
				if($options){
					$is_options=true;
				} else {
					$is_options=false;
				}

				$recurrings = $this->model_catalog_product->getProfiles($result['product_id']);
				$is_recuring_product = (!empty($recurrings)) ? true : false;

				$data['products'][] = array(
					'product_id' => $product_info['product_id'],
					'thumb'      => $image,
					'name'       => $product_info['name'],
					'model'      => $product_info['model'],
					'minimum'     => $product_info['minimum'] > 0 ? $product_info['minimum'] : 1,
					'stock'      => $stock,
					'price'      => $price,
					'special'    => $special,
					'rating'=>(int)$product_info['rating'],
					'reviews' => (int)$product_info['reviews'],
					'is_options'	=> $is_options,
					'is_recuring_product'	=> $is_recuring_product,
				);
			} else {
				$this->model_account_wishlist->deleteWishlist($result['product_id']);
			}
		}
        unset($language['backup']);
		$this->json['data']	= $data;
		$this->json['language']	= $language;

		return $this->sendResponse();
	}

	public function add() {
		
		if($this->validateToken(true)) {
			$this->checkPlugin();
		}else{
			$this->load_language();
		}

		$this->load->language('account/wishlist');
		$this->load->language('ocrestapi/message');

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		$this->load->model('catalog/product');


		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {

			if ($this->customer->isLogged()) {
			
				// Edit customers cart
				
				$this->load->model('account/wishlist');

				$this->model_account_wishlist->addWishlist($this->request->post['product_id']);

				$this->json['data']['success'] = sprintf($this->language->get('text_wishlist_success_message'), $product_info['name']);
			} else {

				$this->json['errors']['warning'] = sprintf($this->language->get('error_wishlist_login'), $product_info['name']);
				
			}			

		}
		return $this->sendResponse();
	
	}

	public function delete() {

		$this->checkPlugin();

		$language=$this->load->language('account/wishlist');
		$language=$this->load->language('ocrestapi/message');

		$this->load->model('account/wishlist');
		if (isset($this->request->post['product_id'])) {

			$product_id = $this->request->post['product_id'];
			$this->load->model('catalog/product');

			$product_info = $this->model_catalog_product->getProduct($product_id);

			// Remove Wishlist
			$this->model_account_wishlist->deleteWishlist($this->request->post['product_id']);

			$data['success'] = sprintf($this->language->get('text_wishlist_remove_product'), $product_info['name']);
			$this->json['data'] = $data;
		}else{
			$this->json['status'] = false;
		}
			$this->sendResponse();		
	}

}
