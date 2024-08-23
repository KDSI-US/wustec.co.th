<?php
/* This file is under Git Control by KDSI. */
class ControllerMpCheckoutAccountOptionButton extends Controller {
	public function __construct($registry) {
		parent::__construct($registry);
		require_once(DIR_SYSTEM .'library/mpcheckout/manager.php');
	}
	
	public function index() {
		$data = array();

		$this->content($data);
		
		return $this->load->view('mpcheckout/account_option_button', $data);
	}

	public function ajax() {
		$data = array();
		
		$this->content($data);
		
		return $this->load->view('mpcheckout/account_option_button', $data);
	}

	protected function content(&$data) {
		$this->load->language('mpcheckout/checkout');		

		$data['button_checkout'] = $this->language->get('button_checkout');


		$mpcheckout_account_button_description = $this->config->get('mpcheckout_account_button_description');
		
		if(!empty($mpcheckout_account_button_description[$this->config->get('config_language_id')]['register_panel'])) {
			 $this->language->setmpcheckoutlanguage('panel_register', $mpcheckout_account_button_description[$this->config->get('config_language_id')]['register_panel']);
		}

		if(!empty($mpcheckout_account_button_description[$this->config->get('config_language_id')]['guest_panel'])) {
			 $this->language->setmpcheckoutlanguage('panel_guest', $mpcheckout_account_button_description[$this->config->get('config_language_id')]['guest_panel']);
		}

		if(!empty($mpcheckout_account_button_description[$this->config->get('config_language_id')]['login_panel'])) {
			 $this->language->setmpcheckoutlanguage('panel_login', $mpcheckout_account_button_description[$this->config->get('config_language_id')]['login_panel']);
		}
		

		$data['panel_register'] = $this->language->get('panel_register');
		$data['panel_guest'] = $this->language->get('panel_guest');
		$data['panel_login'] = $this->language->get('panel_login');
		
		$mpcheckout_account_button = $this->config->get('mpcheckout_account_button');
		if(!empty($mpcheckout_account_button['account_buttons_status'])) {
			$data['account_buttons_status'] = $mpcheckout_account_button['account_buttons_status'];
		} else{
			$data['account_buttons_status'] = array('register', 'guest', 'login');
		}

		if(!empty($mpcheckout_account_button['default_account_button'])) {
			$data['default_account_button'] = $mpcheckout_account_button['default_account_button'];
		} else{
			$data['default_account_button'] = 'register';
		}

		$mpcheckout_social_panel = $this->config->get('mpcheckout_social_panel');	

		$mpcheckout_social_description = $this->config->get('mpcheckout_social_description');

		if(!empty($mpcheckout_social_description[$this->config->get('config_language_id')]['title'])) {
			 $this->language->setmpcheckoutlanguage('panel_sociallogin', $mpcheckout_social_description[$this->config->get('config_language_id')]['title']);
		}

		$data['panel_sociallogin'] = $this->language->get('panel_sociallogin');

		if(!empty($mpcheckout_social_panel['google']['status'])) {
			$data['social_google_status'] = true;
		} else {
			$data['social_google_status'] = false;
		}

		if(!empty($mpcheckout_social_panel['facebook']['status'])) {
			$data['social_facebook_status'] = true;
		} else {
			$data['social_facebook_status'] = false;
		}

		if(!empty($mpcheckout_social_panel['linkedin']['status'])) {
			$data['social_linkedin_status'] = true;
		} else {
			$data['social_linkedin_status'] = false;
		}
		/*new updates 28032018 starts*/
		if(!empty($mpcheckout_social_panel['instagram']['status'])) {
			$data['social_instagram_status'] = true;
		} else {
			$data['social_instagram_status'] = false;
		}

		if(!empty($mpcheckout_social_panel['twitter']['status'])) {
			$data['social_twitter_status'] = true;
		} else {
			$data['social_twitter_status'] = false;
		}
		/*new updates 28032018 ends*/
		if(!empty($mpcheckout_social_panel['facebook']['status'])) {
			session_start();
			
			defined('FACEBOOK_SDK_V4_SRC_DIR') || define('FACEBOOK_SDK_V4_SRC_DIR', DIR_SYSTEM. "library/mpcheckout/facebook/src/Facebook/");
			require_once DIR_SYSTEM. "library/mpcheckout/facebook/src/Facebook/autoload.php";

			$fb = new Facebook\Facebook([
				'app_id' 				=> isset($mpcheckout_social_panel['facebook']['appid']) ? $mpcheckout_social_panel['facebook']['appid'] : '',
				'app_secret' 			=> isset($mpcheckout_social_panel['facebook']['secret']) ? $mpcheckout_social_panel['facebook']['secret'] : '',
				//'default_graph_version' => 'v2.5',
			]);

			$helper = $fb->getRedirectLoginHelper();
			$data['facebook_href'] = $helper->getLoginUrl($this->url->link('mpcheckout/sociallogin/facebookapi','', true), ['email']);
		} else{
			$data['facebook_href'] = '';
		}

		if(!empty($mpcheckout_social_panel['google']['status'])) {
			require_once(DIR_SYSTEM. "library/mpcheckout/google/autoload.php");		

			$client = new Google_Client();

			$appid = isset($mpcheckout_social_panel['google']['appid']) ? $mpcheckout_social_panel['google']['appid'] : '';

			$secret = isset($mpcheckout_social_panel['google']['secret']) ? $mpcheckout_social_panel['google']['secret'] : '';
			$client->setClientId($appid);

			$client->setClientSecret($secret);

			$client->setRedirectUri($this->url->link('mpcheckout/sociallogin/googleapi','',true));

			$client->addScope(["email","profile"]);			

			$data['google_href'] = $client->createAuthUrl();
		} else{
			$data['google_href'] = '';
		}

		if(!empty($mpcheckout_social_panel['linkedin']['status'])) {
			$token = token(10);
	  		$this->session->data['linkedincsrf'] = $token;

	  		$appid = isset($mpcheckout_social_panel['linkedin']['appid']) ? $mpcheckout_social_panel['linkedin']['appid'] : '';

			$data['linkedin_href'] = 'https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id='.  $appid .'&redirect_uri='. $this->url->link('mpcheckout/sociallogin/linkedinapi','',true) .'&state='. $token .'&scope=r_basicprofile%20r_emailaddress';
		} else{
			$data['linkedin_href'] = '';
		}
		/*new updates 28032018 starts*/
		if(!empty($mpcheckout_social_panel['instagram']['status'])) {
			
	  		$appid = isset($mpcheckout_social_panel['instagram']['appid']) ? $mpcheckout_social_panel['instagram']['appid'] : '';

	  		$redirect_uri = $this->url->link('mpcheckout/sociallogin/instagramapi','',true);

			$data['instagram_href'] = 'https://api.instagram.com/oauth/authorize/?client_id=' . $appid . '&redirect_uri=' . urlencode($redirect_uri) . '&response_type=code&scope='.urlencode('basic');
			/*comments likes relationships*/
		} else{
			$data['instagram_href'] = '';
		}

		if(!empty($mpcheckout_social_panel['twitter']['status'])) {
			$data['twitter_href'] = $this->url->link('mpcheckout/sociallogin/twitterapi/getAuthorize','',true);
		} else{
			$data['twitter_href'] = '';
		}

		$data['email_prompt'] = false;
		$data['customer_id'] = 0;
		$data['id'] = 0;
		$data['username'] = '';
		$data['type'] = '';

		if(isset($this->session->data['mpcheckout']['customer_id'])) {
			$data['email_prompt'] = $this->session->data['mpcheckout']['email_prompt'];
			$data['customer_id'] = $this->session->data['mpcheckout']['customer_id'];
			$data['id'] = $this->session->data['mpcheckout']['id'];
			$data['username'] = $this->session->data['mpcheckout']['username'];
			$data['type'] = $this->session->data['mpcheckout']['type'];
		}

		$data['text_mpemail_title'] = $this->language->get('text_mpemail_title');
		$data['text_mpfacebook'] = $this->language->get('text_mpfacebook');
		$data['text_mpgoogle'] = $this->language->get('text_mpgoogle');
		$data['text_mplinkedin'] = $this->language->get('text_mplinkedin');
		$data['text_mpinstagram'] = $this->language->get('text_mpinstagram');
		$data['text_mptwitter'] = $this->language->get('text_mptwitter');
		$data['text_loading'] = $this->language->get('text_loading');			
		$data['entry_mpemail'] = $this->language->get('entry_mpemail');
		$data['button_mpsubmit'] = $this->language->get('button_mpsubmit');
		$data['button_mpcancel'] = $this->language->get('button_mpcancel');			

		/*new updates 28032018 ends*/
	}
	
	/*new updates 28032018 ends*/
	public function mpquickcheckoutGetCustomerEmail() {
		$json = array();
		$this->load->language('mpcheckout/checkout');
		$this->load->language('mpcheckout/sociallogin/linkedin');
		$this->load->model('account/customer');
		$this->load->model('mpcheckout/sociallogin');

		if(isset($this->session->data['mpcheckout']['customer_id']) && (isset($this->session->data['mpcheckout']['insta_access_token']) || (isset($this->session->data['mpcheckout']['oauth_token']) && isset($this->session->data['mpcheckout']['oauth_token_secret'])) || (isset($this->session->data['mpcheckout']['facebook_access_token'])))) {

			// validate email

			if ((utf8_strlen($this->request->post['mpcemail']) > 96) || !filter_var($this->request->post['mpcemail'], FILTER_VALIDATE_EMAIL)) {
				$json['error']['email'] = $this->language->get('error_mpemail');
			}

			if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['mpcemail'])) {
				$json['error']['exists'] = $this->language->get('error_mpexists');
			}

			if(isset($json['error'])) {
				$json['error']['warning'] = $this->language->get('error_mpcheck');
			}

			if(!$json) {

				$email = $this->request->post['mpcemail'];
				$id =  $this->session->data['mpcheckout']['id'];
				$customer_id = $this->session->data['mpcheckout']['customer_id'];
				$idata = array(
					'email' => $email,
					'firstname' => '',
					'lastname' => '',
					'telephone' => '',
					'fax' => '',
				);

				/*here we apply logic. if customer_id present but email not.*/
				if($this->session->data['mpcheckout']['customer_id'] == 0) {
					$customer_id = $this->model_mpcheckout_sociallogin->addSocialCustomer($idata);
				} else {
					$this->model_mpcheckout_sociallogin->addCustomerInfoByCustomerId($customer_id, $idata);
				}


				// here we link customer id with mpquicklogin table for instagram and twitter

				if(isset($this->session->data['mpcheckout']['insta_access_token'])) {
					$this->model_mpcheckout_sociallogin->addCustomerIdToInstaUser($id, $customer_id);
					
				} 
				if(isset($this->session->data['mpcheckout']['oauth_token']) && isset($this->session->data['mpcheckout']['oauth_token_secret'])) {
					$this->model_mpcheckout_sociallogin->addCustomerIdToTwitterUser($id, $customer_id);
				}
				if(isset($this->session->data['mpcheckout']['facebook_access_token'])) {
					$this->model_mpcheckout_sociallogin->addCustomerIdToFbUser($id, $customer_id);
				}

				/*unset the session of $this->session->data['mpcheckout'] so after refresh we not ask email again.*/
				if(isset($this->session->data['mpcheckout'])) {
					unset($this->session->data['mpcheckout']);
				}

				$customer_info = $this->model_account_customer->getCustomerByEmail($email);

				

				if(empty($customer_info)) {
					$json['error']['warning'] = $this->language->get('text_customer_missing');
				}

				if ($customer_info && !$customer_info['status']) {
					$json['error']['warning'] = $this->language->get('text_pending_approval');
				}		

				if (!$json && $customer_info && $this->customer->login($customer_info['email'], '', true)) {
					// Default Addresses
					$this->load->model('account/address');

					if ($this->config->get('config_tax_customer') == 'payment') {
						$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
					}

					if ($this->config->get('config_tax_customer') == 'shipping') {
						$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
					}
					$json['success'] = true;
					$json['redirect'] = str_replace('&amp;', '&', $this->url->link('checkout/checkout', '', true));
				}

			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	/*new updates 28032018 ends*/
}