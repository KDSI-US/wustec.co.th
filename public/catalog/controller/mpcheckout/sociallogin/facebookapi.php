<?php
class ControllerMpcheckoutSocialloginFacebookApi extends Controller {
	private $error = array();

	public function __construct($registry) {
		parent::__construct($registry);
		require_once(DIR_SYSTEM .'library/mpcheckout/manager.php');
	}

	public function index() {
		session_start();
		
		$this->load->language("mpcheckout/sociallogin/facebook");
		$this->load->model("mpcheckout/sociallogin");

		defined('FACEBOOK_SDK_V4_SRC_DIR') || define('FACEBOOK_SDK_V4_SRC_DIR', DIR_SYSTEM. "library/mpcheckout/facebook/src/Facebook/");
		require_once DIR_SYSTEM. "library/mpcheckout/facebook/src/Facebook/autoload.php";

		$mpcheckout_social_panel = $this->config->get('mpcheckout_social_panel');

		$fb = new Facebook\Facebook([
			'app_id' => isset($mpcheckout_social_panel['facebook']['appid']) ? $mpcheckout_social_panel['facebook']['appid'] : '',
			'app_secret' => isset($mpcheckout_social_panel['facebook']['secret']) ? $mpcheckout_social_panel['facebook']['secret'] : '',
			//'default_graph_version' => 'v2.5',
		]);

		$helper = $fb->getRedirectLoginHelper();
		$_SESSION['FBRLH_state']=isset( $_GET['state'] ) ? $_GET['state']  : '';
		if (isset($_GET['state'])) {
		  $helper->getPersistentDataHandler()->set('state', $_GET['state']);
		}

		try {
		  $accessToken = $helper->getAccessToken();
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			$this->log->write('Graph returned an error: ' . $e->getMessage());
			$this->session->data['error'] = $this->language->get('text_fb_grapherror');
			$this->response->redirect($this->url->link('checkout/checkout', '', Mpcheckout\Manager::mpssl()));
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			$this->log->write('Facebook SDK returned an error: ' . $e->getMessage());
			$this->session->data['error'] = $this->language->get('text_fb_validationfail');
			$this->response->redirect($this->url->link('checkout/checkout', '', Mpcheckout\Manager::mpssl()));
		}

		if (isset($accessToken)) {
			// Logged in!
			$this->session->data['facebook_access_token'] = (string) $accessToken;

			// Now you can redirect to another page and use the
			// access token from $_SESSION['facebook_access_token']
			$this->response->redirect($this->url->link('mpcheckout/sociallogin/facebookapi', '', Mpcheckout\Manager::mpssl()));
		} elseif ($helper->getError()) {
		  // The user denied the request
			$this->log->write('Facebook User Denied The Request: ' . $helper->getError());
		  	$this->session->data['error'] = $this->language->get('text_fb_notauthorized');
			$this->response->redirect($this->url->link('checkout/checkout', '', Mpcheckout\Manager::mpssl()));
		}
		
		if (!isset($this->session->data['facebook_access_token']) || empty($this->session->data['facebook_access_token'])) {

			$this->session->data['error'] = $this->language->get('text_invalid_token');
			$this->response->redirect($this->url->link('checkout/checkout', '', Mpcheckout\Manager::mpssl()));
		}		

		// Sets the default fallback access token so we don't have to pass it to each request
		$fb->setDefaultAccessToken($this->session->data['facebook_access_token']);

		try {
			// Send a GET request
			$response = $fb->get('/me?locale=en_US&fields=name,id,first_name,last_name,email,locale');
			$userNode = $response->getGraphUser();
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			$this->log->write('Graph returned an error: ' . $e->getMessage());
			$this->session->data['error'] = $this->language->get('text_fb_grapherror');
			$this->response->redirect($this->url->link('checkout/checkout', '', Mpcheckout\Manager::mpssl()));
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			$this->log->write('Facebook SDK returned an error: ' . $e->getMessage());
			$this->session->data['error'] = $this->language->get('text_fb_validationfail');
			$this->response->redirect($this->url->link('checkout/checkout', '', Mpcheckout\Manager::mpssl()));
		}

		$user_profile = $response->getDecodedBody();
		
		/*facebook login without email starts*/
		$email = '';

		$username = $user_profile['name'];
		$id = $user_profile['id'];
		$fbUser = $this->model_mpcheckout_sociallogin->getMpSocialLoginFbUser($user_profile['id'], $user_profile['name']);

		// here we play a game.
		/*check if fb user is aready exist and email is entered or not.*/
		/*first save return response into db. Then we need customer email first.*/

		$customer_id = 0;

		if($fbUser) {
			if($fbUser['customer_id'] > 0) {
				$customer_id = $fbUser['customer_id'];
			}
		} else {
			/*add fbuser*/
			$this->model_mpcheckout_sociallogin->addMpSocialLoginFbUser($user_profile['id'], $user_profile['name']);
		}

		// facebook not return user email.
		if(!empty($user_profile) && empty($user_profile['email'])) {
			$this->session->data['mpsociallogin']['facebook_access_token'] = $this->session->data['facebook_access_token'];

			$email_prompt = false;
			$email = '';

			if($customer_id != 0) {
				$customerById = $this->model_mpcheckout_sociallogin->getCustomerByCustomerId($customer_id);
				if($customerById) {
					// check if customer has email. if not then ask for email.
					$email = $customerById['email'];
					if(empty($customerById['email'])) {
						$email_prompt = true;
					}
				} else {
					$customer_id = 0;
				}
			}

			if($customer_id == 0 || $email_prompt == true) {
				$this->session->data['mpsociallogin']['email_prompt'] = true;
				$this->session->data['mpsociallogin']['customer_id'] = $customer_id;
				$this->session->data['mpsociallogin']['id'] = $id;
				$this->session->data['mpsociallogin']['username'] = $username;
				$this->session->data['mpsociallogin']['type'] = 'FB';

				$this->response->redirect($this->url->link('account/login', '', true));
			}
		}

		if(empty($email) && (empty($user_profile) || empty($user_profile['email']))) {
			$this->session->data['error'] = $this->language->get('text_email_notfound');
			$this->response->redirect($this->url->link('account/login', '', true));
		}

		if(!empty($user_profile['email']) && empty($email)) {

			// if earlier facebook user email not return and do signup to our website and use email example@test.com. But now they add email to facebook account test@example.com.
			// in this case first we check that if same faceook user is registered with us or not. if yes, then try to get their email. if email found then we ignore facebook email. Other wise we use facebook email.
			// $fbUser = $this->model_mpcheckout_sociallogin->getMpSocialLoginFbUser($user_profile['id'], $user_profile['name']);
			if($fbUser) {

				$customerById = $this->model_mpcheckout_sociallogin->getCustomerByCustomerId($fbUser['customer_id']);

				if($customerById) {

					$email = $customerById['email'];

					// if email is empty but we have customer. Then we insert facebook return email to customer id.
					if(empty($email)) {
						$email = $user_profile['email'];

						$idata = array();

						$idata['email'] = $email;

						if(empty($customerById['firstname'])) {
							$idata['firstname'] = $user_profile['first_name'];
						}

						if(empty($customerById['lastname'])) {
							$idata['lastname'] = $user_profile['last_name'];
						}

						$this->model_mpcheckout_sociallogin->addCustomerInfoByCustomerId($fbUser['customer_id'], $idata);
					}
				}
			}

			if(empty($email)) {
				$email = $user_profile['email'];
			}
		}

		/*facebook login without email ends*/

		$firstname = $user_profile['first_name'];
		$lastname = $user_profile['last_name'];
		
		$customer_info = $this->model_mpcheckout_sociallogin->getSocialCustomer($email);

		if(empty($customer_info)) {
			$idata = array(
				'email' 	=> $email,
				'firstname' => $firstname,
				'lastname' 	=> $lastname,
				'telephone' => '',
				'fax' 		=> '',
			);

			$customer_id = $this->model_mpcheckout_sociallogin->addSocialCustomer($idata);
		
			$customer_info = $this->model_mpcheckout_sociallogin->getSocialCustomer($email);
		}

		unset($this->session->data['facebook_access_token']);

		if(empty($customer_info)) {
			$this->session->data['error'] = $this->language->get('text_customer_missing');
			$this->response->redirect($this->url->link('checkout/checkout', '', Mpcheckout\Manager::mpssl()));
		}

		/* here we add customer id to fb user */
		if($fbUser && $fbUser['customer_id'] == 0) {
			$this->model_mpcheckout_sociallogin->addCustomerIdToFbUser($user_profile['id'], $customer_info['customer_id']);
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