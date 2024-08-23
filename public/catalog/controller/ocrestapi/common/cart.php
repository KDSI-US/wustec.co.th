<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiCommonCart extends ocrestapicontroller {
	public function index() {	
			if($this->validateToken(true)) {
			$this->checkPlugin();
				$total = 0;
				$this->load->model('account/wishlist');
				$this->load->model('ocrestapi/ocrestapi');
					$get_token= $this->get_request_token();
				$token = explode(' ', $get_token)[1];
				$oauth_data = $this->model_ocrestapi_ocrestapi->get_auth_data($token);
				if ($this->cart->hasProducts() || !empty($oauth_data['vouchers'])) {
						$this->json['data']['items'] = $this->cart->countProducts() + (isset($oauth_data['vouchers']) ? count($oauth_data['vouchers']) : 0);
					}else{
						$this->json['data']['items']=$total;
					}

		

			$this->json['data']['wishlist_items'] = !empty($this->model_account_wishlist->getTotalWishlist())?$this->model_account_wishlist->getTotalWishlist():$total;
			}else{
				$this->json['data']['items']=0;
				$this->json['data']['wishlist_items']=0;
			}		
			
		
		
		$this->sendResponse();
	}

	
}