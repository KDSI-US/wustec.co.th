<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiExtensionModuleAccount extends OcrestapiController {
	public function index() {
		$this->checkPlugin();
		$this->load->language('extension/module/account');

		$data['logged'] = $this->customer->isLogged();
		$data['register'] ='extension/account/register';
		$data['login'] ='extension/account/login';
		$data['logout'] ='extension/account/logout';
		$data['forgotten'] ='extension/account/forgotten';
		$data['account'] ='extension/account/account';
		$data['edit'] ='extension/account/edit';
		$data['password'] ='extension/account/password';
		$data['address'] ='extension/account/address';
		$data['wishlist'] ='extension/account/wishlist';
		$data['order'] ='extension/account/order';
		$data['download'] ='extension/account/download';
		$data['reward'] ='extension/account/reward';
		$data['return'] ='extension/account/return';
		$data['transaction'] ='extension/account/transaction';
		$data['newsletter'] ='extension/account/newsletter';
		$data['recurring'] ='extension/account/recurring';

		$this->json['data'] = $data;
		
		return $this->sendResponse();
	}
}