<?php
class ControllerMpCheckoutSocialLoginInstagramapi extends Controller {
	private $logger;
	public function __construct($registry) {
		require_once(DIR_SYSTEM .'library/mpcheckout/manager.php');
		parent :: __construct($registry);
		$this->logger = new Log('sociallogin.log');
	}

	private function getInstagramAccessToken($client_id, $client_secret, $redirect_uri, $code) {
		$url = 'https://api.instagram.com/oauth/access_token';
		
		$curlPost = 'client_id='. $client_id . '&redirect_uri=' . $redirect_uri . '&client_secret=' . $client_secret . '&code='. $code . '&grant_type=authorization_code';
		$curl = curl_init();		
		curl_setopt($curl, CURLOPT_URL, $url);		
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POST, 1);		
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);			
		$response = curl_exec($curl);

		$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);	
		
		if($http_code != '200')	{
			$this->logger->write('Failed to receieve access token from INSTAGRAM. CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl));
			$return['error'] = 'Failed to receieve access token from INSTAGRAM. CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl);
		} elseif ($response) {
			$data = json_decode($response,1);
			if(!isset($data['access_token'])) {
				$return['error'] = 'Plese refresh page.';
				$this->logger->write('INSTAGRAM Authorization Code for an Access Token ERROR: Unknow. ' . print_r($data, 1) );
			} else {
				$return['access_token'] = $data['access_token'];
			}
		} else {
			$return['error'] = 'Empty Gateway Response';
			$this->logger->write('INSTAGRAM Authorization Code for an Access Token ERROR: Empty Gateway Response');
		}

		curl_close($curl);
		
		return $return;
	}

	private function getInstragramUserProfileInfo($access_token) {
		$url = 'https://api.instagram.com/v1/users/self/?access_token=' . $access_token;	

		$curl = curl_init();		
		curl_setopt($curl, CURLOPT_URL, $url);		
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);	
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		$response = curl_exec($curl);
		$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$data = json_decode($response, 1);

		if($data['meta']['code'] != 200 || $http_code != 200) {
			$this->logger->write('Error : Failed to get user information from INSTAGRAM. Return data: '. print_r($data, 1) .' CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl));
			$return['error'] = 'Error : Failed to get user information from INSTAGRAM. CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl);
		} elseif ($response) {
			if(!isset($data['data'])) {
				$return['error'] = 'Plese refresh page.';
				$this->logger->write('INSTAGRAM ERROR: Unknow. ' . print_r($data, 1) );
			} else {
				$return['data'] = $data['data'];
			}
		} else {
			$return['error'] = 'Empty Gateway Response';
			$this->logger->write('INSTAGRAM Authorization Code for an Access Token ERROR: Empty Gateway Response');
		}
		curl_close($curl); 
		return $return;
	}

	public function index() {
		$this->load->language("mpcheckout/sociallogin/instagram");
		$this->load->model("mpcheckout/sociallogin");
		$this->load->model("account/customer");

		$mpcheckout_social_panel = $this->config->get('mpcheckout_social_panel');

		$client_id = isset($mpcheckout_social_panel['instagram']['appid']) ? $mpcheckout_social_panel['instagram']['appid'] : '';
		$client_secret = isset($mpcheckout_social_panel['instagram']['secret']) ? $mpcheckout_social_panel['instagram']['secret'] : '';
		$redirect_uri = $this->url->link('mpcheckout/sociallogin/instagramapi','', Mpcheckout\Manager::mpssl());

		$insta_access_token = false;
		if (isset($this->session->data['insta_access_token'])) {
			// user authenticated -> receive and set token
			$insta_access_token = $this->session->data['insta_access_token'];
		} else {

			// authentication in progress?
			if (isset($this->request->get['code'])) {
				
				// Get the access token 
				$response = $this->getInstagramAccessToken($client_id, $client_secret, $redirect_uri, $this->request->get['code']);

				if(!isset($response['access_token'])) {
					$this->session->data['error'] = $response['error'];
					$this->response->redirect($this->url->link('checkout/checkout','',Mpcheckout\Manager::mpssl()));
				}

				$insta_access_token = $response['access_token'];
				$this->session->data['mpcheckout']['insta_access_token'] = $response['access_token'];
				
			} else {
				// check whether an error occurred
				if (isset($this->request->get['error'])) {
					$this->logger->write('An error occurred: ' . $this->request->get['error']);
				}
			}
		}

		if(!$insta_access_token) {
			$this->response->redirect($this->url->link('checkout/checkout','',Mpcheckout\Manager::mpssl()));
		}

		
		$user_profile = $this->getInstragramUserProfileInfo($insta_access_token);

		if(isset($user_profile['error'])) {
			$this->session->data['error'] = $user_profile['error'];
			$this->response->redirect($this->url->link('checkout/checkout','',Mpcheckout\Manager::mpssl()));	
		}

		// here we play a game.
		/*check if instagram user is aready exist and email is entered or not.*/
		/*first save return response into db. Then we need customer email first.*/
		$username = $user_profile['data']['username'];
		$id = $user_profile['data']['id'];

		$instraUser = $this->model_mpcheckout_sociallogin->getMpSocialLoginInstaUser($user_profile['data']['id'], $user_profile['data']['username']);

		$firstname = '';
		$lastname = '';

		if(isset($user_profile['data']['full_name'])) {
			$firstname = $user_profile['data']['full_name'];
		}
		
		$customer_id = 0;

		if($instraUser) {
			if($instraUser['customer_id'] > 0) {		
				$customer_id = $instraUser['customer_id'];
			}
		} else {
			/*add instauser*/
			$this->model_mpcheckout_sociallogin->addMpSocialLoginInstaUser($user_profile['data']['id'], $user_profile['data']['username']);
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
			$this->session->data['mpcheckout']['type'] = 'INSTAGRAM';

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
		
	}

}