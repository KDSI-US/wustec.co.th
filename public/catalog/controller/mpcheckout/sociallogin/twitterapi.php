<?php
require DIR_SYSTEM ."library/mpcheckout/twitter/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

class ControllerMpCheckoutSocialLoginTwitterapi extends Controller {
	private $logger;
	public function __construct($registry) {
		require_once(DIR_SYSTEM .'library/mpcheckout/manager.php');
		parent :: __construct($registry);
		$this->logger = new Log('sociallogin.log');

		if(!isset($this->session->data['mp_twitter_getAuthorize_counter'])) {
			$this->session->data['mp_twitter_getAuthorize_counter'] = 0;
		}

	}

	public function getAuthorize() {
		$this->load->language("mpcheckout/sociallogin/twitter");
		$this->load->model("mpcheckout/sociallogin");
		$this->load->model("account/customer");

		$mpcheckout_social_panel = $this->config->get('mpcheckout_social_panel');


		$client_id = isset($mpcheckout_social_panel['twitter']['appid']) ? $mpcheckout_social_panel['twitter']['appid'] : '';
		$client_secret = isset($mpcheckout_social_panel['twitter']['secret']) ? $mpcheckout_social_panel['twitter']['secret'] : '';
		$redirect_uri = $this->url->link('mpcheckout/sociallogin/twitterapi','',Mpcheckout\Manager::mpssl());


		$connection = new TwitterOAuth($client_id, $client_secret);

		try {

			$request_token = $connection->oauth("oauth/request_token", array("oauth_callback" => $redirect_uri));

			$this->session->data['mpcheckout']['oauth_token'] = $request_token['oauth_token'];
			$this->session->data['mpcheckout']['oauth_token_secret'] = $request_token['oauth_token_secret'];

			$url = $connection->url("oauth/authorize", array("oauth_token" => $request_token['oauth_token']));

			$this->session->data['mp_twitter_getAuthorize_counter']++;

			header('Location: ' . $url);
			
		} catch (Exception $e) {
			$message = json_decode($e->getMessage(), 1);
			$this->logger->write("\n\n --------------------------------------- \n\n" . 'An error occurred: Twitter :: '. print_r($e->getMessage(), 1)  .' . Trace Stack :: '.  print_r($e->getTraceAsString(), 1) . "\n\n --------------------------------------- \n\n " );
			$this->session->data['error'] = $this->language->get('text_bad_authentication') . ' ' . $message['errors'][0]['message'];
			$this->response->redirect($this->url->link('checkout/checkout','',Mpcheckout\Manager::mpssl()));
		}

	}


	public function index() {
		$this->load->language("mpcheckout/sociallogin/twitter");
		$this->load->model("mpcheckout/sociallogin");
		$this->load->model("account/customer");

		$mpcheckout_social_panel = $this->config->get('mpcheckout_social_panel');

		$client_id = isset($mpcheckout_social_panel['twitter']['appid']) ? $mpcheckout_social_panel['twitter']['appid'] : '';
		$client_secret = isset($mpcheckout_social_panel['twitter']['secret']) ? $mpcheckout_social_panel['twitter']['secret'] : '';
		$redirect_uri = $this->url->link('mpcheckout/sociallogin/twitterapi','',Mpcheckout\Manager::mpssl());



		if(isset($this->request->get['oauth_token']) || isset($this->request->get['oauth_verifier'])) {	
			$connection = new TwitterOAuth($client_id, $client_secret, $this->session->data['mpcheckout']['oauth_token'], $this->session->data['mpcheckout']['oauth_token_secret']);

			try {
				$access_token = $connection->oauth('oauth/access_token', array('oauth_verifier' => $this->request->get['oauth_verifier'], 'oauth_token'=> $this->request->get['oauth_token']));				
			} catch (Exception $e) {
				$this->logger->write("\n\n --------------------------------------- \n\n" . 'An error occurred: Twitter get tokens get expire. Twitter :: '. print_r($e->getMessage(), 1)  .' . Trace Stack :: '.  print_r($e->getTraceAsString(), 1) . "\n\n --------------------------------------- \n\n " );
				$this->session->data['error'] = $this->language->get('text_token_expire');
				$this->response->redirect($this->url->link('checkout/checkout','',Mpcheckout\Manager::mpssl()));
			}


			$connection = new TwitterOAuth($client_id, $client_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);

			try {
				$user_info = $connection->get('account/verify_credentials');
			} catch (Exception $e) {
				$this->logger->write("\n\n --------------------------------------- \n\n" . 'An error occurred: Fail to get customer information. Twitter :: '. print_r($e->getMessage(), 1)  .' . Trace Stack :: '.  print_r($e->getTraceAsString(), 1) . "\n\n --------------------------------------- \n\n " );
				$this->session->data['error'] = $this->language->get('text_customer_missing');
				$this->response->redirect($this->url->link('checkout/checkout','',Mpcheckout\Manager::mpssl()));
			}
			
			$oauth_token = $access_token['oauth_token'];
			$oauth_token_secret = $access_token['oauth_token_secret'];

			$this->session->data['mpcheckout']['oauth_token'] = $oauth_token;
			$this->session->data['mpcheckout']['oauth_token_secret'] = $oauth_token_secret;

			if(empty($user_info)) {				
				$this->session->data['error'] = $this->language->get('text_customer_missing');
				$this->response->redirect($this->url->link('checkout/checkout','',Mpcheckout\Manager::mpssl()));
			}

			// here we play a game.
			/*check if twitter user is aready exist and email is entered or not.*/
			/*first save return response into db. Then we need customer email first.*/
			$username = $user_info->screen_name;
			$id = $user_info->id_str;

			$twitterUser = $this->model_mpcheckout_sociallogin->getMpSocialLoginTwitterUser($id, $username);

			$firstname = '';
			$lastname = '';

			if(isset($user_info->name)) {
				$firstname = $user_info->name;
			}
			
			$customer_id = 0;

			if($twitterUser) {
				if($twitterUser['customer_id'] > 0) {		
					$customer_id = $twitterUser['customer_id'];
				}
			} else {
				/*add twitter user*/
				$this->model_mpcheckout_sociallogin->addMpSocialLoginTwitterUser($id, $username);
			}

			$email_prompt = false;
			$email = '';

			if($customer_id != 0) {
				$customerById = $this->model_mpcheckout_sociallogin->getCustomerByCustomerId($customer_id);
				if($customerById) {
					// check if customer has email. if not then ask for email.
					if(empty($customerById['email'])) {
						$email_prompt = true;
						$email = $customerById['email'];
					}
				} else {
					$customer_id = 0;
				}
			}

			if($customer_id == 0 || $email_prompt == true) {
				$this->session->data['mpcheckout']['email_prompt'] = true;
				$this->session->data['mpcheckout']['customer_id'] = $customer_id;
				$this->session->data['mpcheckout']['id'] = $id;
				$this->session->data['mpcheckout']['username'] = $username;
				$this->session->data['mpcheckout']['type'] = 'TWITTER';

				$this->response->redirect($this->url->link('checkout/checkout', '', Mpcheckout\Manager::mpssl()));			
			} else {
				// initiate login process
				$customer_info = $this->model_account_customer->getCustomerByEmail($email);

				if(empty($customer_info)) {
					$idata = array(
						'email' => $email,
						'firstname' => $firstname,
						'lastname' => trim($lastname),
						'telephone' => '',
						'fax' => '',
					);

					$customer_id = $this->model_mpcheckout_sociallogin->addSocialCustomer($idata);

					$customer_info = $this->model_account_customer->getCustomerByEmail($email);
				}

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
			
			
		} else {
			
			if($this->session->data['mp_twitter_getAuthorize_counter'] <= 3) {
				$this->logger->write('An error occurred: oauth_token or oauth_verifier is missing. mp_twitter_getAuthorize_counter ('. $this->session->data['mp_twitter_getAuthorize_counter'] .') ');
				$this->url->link('mpcheckout/sociallogin/twitterapi/getAuthorize','',Mpcheckout\Manager::mpssl());
			} else {
				$this->logger->write('An error occurred: oauth_token or oauth_verifier is missing. mp_twitter_getAuthorize_counter ('. $this->session->data['mp_twitter_getAuthorize_counter'] .') limit exceed. Loop detects');
				$this->url->link('checkout/checkout','',Mpcheckout\Manager::mpssl());
			}

		}

	}

}