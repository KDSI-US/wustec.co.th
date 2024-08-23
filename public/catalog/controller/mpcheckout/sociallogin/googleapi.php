<?php
class ControllerMpCheckoutSocialloginGoogleapi extends Controller {
	private $error = array();

	public function __construct($registry) {
		parent::__construct($registry);
		require_once(DIR_SYSTEM .'library/mpcheckout/manager.php');
	}

	public function index() {
		$this->load->language("mpcheckout/sociallogin/google");
		$this->load->model("mpcheckout/sociallogin");
		$this->load->model("account/customer");

		require_once(DIR_SYSTEM. "library/mpcheckout/google/autoload.php");

		$mpcheckout_social_panel = $this->config->get('mpcheckout_social_panel');

		$appid = isset($mpcheckout_social_panel['google']['appid']) ? $mpcheckout_social_panel['google']['appid'] : '';

		$secret = isset($mpcheckout_social_panel['google']['secret']) ? $mpcheckout_social_panel['google']['secret'] : '';

		$client = new Google_Client();
		$client->setClientId($appid);
		$client->setClientSecret($secret);
		$client->setRedirectUri($this->url->link('mpcheckout/sociallogin/googleapi', '', Mpcheckout\Manager::mpssl()));

		if (isset($this->request->get['code'])) {
			$client->authenticate($this->request->get['code']);
			$this->session->data['gcode'] = $client->getAccessToken();
			$this->response->redirect($this->url->link('mpcheckout/sociallogin/googleapi', '', Mpcheckout\Manager::mpssl()));
		}
		
		if (!isset($this->session->data['gcode']) || empty($this->session->data['gcode'])) {

			$this->session->data['error'] = $this->language->get('text_invalid_token');
			$this->response->redirect($this->url->link('checkout/checkout', '', Mpcheckout\Manager::mpssl()));
		}		

		$token = json_decode($this->session->data['gcode']);

		$user_profile = $client->verifyIdToken($token->id_token)->getAttributes();
		

		if(empty($user_profile) || empty($user_profile['payload']['email'])) {

			$this->session->data['error'] = $this->language->get('text_invalid_token');
			$this->response->redirect($this->url->link('checkout/checkout', '', Mpcheckout\Manager::mpssl()));
		}

		$email = $user_profile['payload']['email'];
		$name = '';
	
		if(isset($user_profile['payload']['name'])) {
			$name = $user_profile['payload']['name'];
		}

		$customer_info = $this->model_account_customer->getCustomerByEmail($email);

		if(empty($customer_info)) {
			$explode = explode(" ", $name);
			$lastname = '';			
			foreach ($explode as $key => $value) {
				if($key > 0) {
					$lastname .= $value ." ";
				}
			}

			$idata = array(
				'email' => $email,
				'firstname' => $explode[0],
				'lastname' => trim($lastname),
				'telephone' => '',
				'fax' => '',
			);

			$customer_id = $this->model_mpcheckout_sociallogin->addSocialCustomer($idata);

			$customer_info = $this->model_account_customer->getCustomerByEmail($email);
		}

		unset($this->session->data['social_action_by']);
		unset($this->session->data['gcode']);

		if(empty($customer_info)) {
			$this->session->data['error'] = $this->language->get('text_customer_missing');
			$this->response->redirect($this->url->link('checkout/checkout', '', Mpcheckout\Manager::mpssl()));
		}

		if(empty($customer_info['status'])) {
			$this->session->data['error'] = $this->language->get('text_pending_approval');
			$this->response->redirect($this->url->link('checkout/checkout', '', Mpcheckout\Manager::mpssl()));
		}		

		if ($customer_info && $this->customer->login($customer_info['email'], '', true)) {
			// Default Addresses
			$this->load->model('account/address');

			if ($this->config->get('config_tax_customer') == 'payment') {
				$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
			}

			if ($this->config->get('config_tax_customer') == 'shipping') {
				$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
			}
			
			$this->response->redirect($this->url->link('checkout/checkout', '', Mpcheckout\Manager::mpssl()));
		}
	}
}