<?php
class ControllerMpcheckoutSocialloginLinkedinapi extends Controller {
	private $logger;
	public function __construct($registry) {
		parent :: __construct($registry);
		$this->logger = new Log('mpscoaillogin.log');

		require_once(DIR_SYSTEM .'library/mpcheckout/manager.php');
	}

	private function getLinkedInProfile($access_token) {

		$return = array();

		$url = 'https://api.linkedin.com/v1/people/~:(id,firstName,lastName,email-address)?format=json';



		$headers = array();
		$headers[] = 'Content-Type: application/json';
		$headers[] = 'x-li-format: json';
		$headers[] = 'Authorization: Bearer ' . $access_token ;

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		$response = curl_exec($curl);

		if (curl_error($curl)) {
			$return['error'] = 'CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl);

			$this->logger->write('LINKED IN Make authenticated requests CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl));
		} elseif ($response) {
			$return = json_decode($response,1);
		} else {
			$return['error'] = 'Empty Gateway Response';

			$this->logger->write('LINKED IN Make authenticated requests ERROR: Empty Gateway Response');
		}
		curl_close($curl);
		return $return;
	}		

	private function getLinkedInAccessToken($code, $redirect_uri) {

		$return = array();

		$url = 'https://www.linkedin.com/oauth/v2/accessToken';

		$mpcheckout_social_panel = $this->config->get('mpcheckout_social_panel');

		$appid = isset($mpcheckout_social_panel['linkedin']['appid']) ? $mpcheckout_social_panel['linkedin']['appid'] : '';

		$secret = isset($mpcheckout_social_panel['linkedin']['secret']) ? $mpcheckout_social_panel['linkedin']['secret'] : '';

		$post_data = array();
		$post_data['grant_type'] = 'authorization_code';
		$post_data['code'] = $code;
		$post_data['redirect_uri'] = $redirect_uri;
		$post_data['client_id'] = $appid;
		$post_data['client_secret'] = $secret;

		$headers = array();
		$headers[] = 'Content-Type: application/x-www-form-urlencoded';

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_data,'','&') );
		$response = curl_exec($curl);

		if (curl_error($curl)) {
			$return['error'] = 'CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl);

			$this->logger->write('LINKED IN Exchange Authorization Code for an Access Token CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl));
		} elseif ($response) {

			$response = json_decode($response,1);

			if(isset($response['error'])) {
				/*{"error":"invalid_request","error_description":"Unable to retrieve access token: appid/redirect uri/code verifier does not match authorization code. Or authorization code expired. Or external member binding exists"}*/
				$return['error'] = 'Please re-authorize app again.';
				$this->logger->write('LINKED IN Exchange Authorization Code for an Access Token RE-AUTHORIZE ERROR: ' . $response['error']);
			}

			if(isset($response['access_token']) && isset($response['expires_in'])) {
				$return = $response;
			} else {
				$return['error'] = 'Please refresh page.';
			}
		} else {
			$return['error'] = 'Empty Gateway Response';

			$this->logger->write('LINKED IN Exchange Authorization Code for an Access Token ERROR: Empty Gateway Response');
		}
		curl_close($curl);
		return $return;
	}

	public function index() {
		$this->load->language("mpcheckout/sociallogin/linkedin");
		$this->load->model("mpcheckout/sociallogin");
		$this->load->model("account/customer");

		if(!isset($this->request->get['code'])) {
			$this->response->redirect($this->url->link('checkout/checkout','',true));
		}

		if(isset($this->request->get['state']) && $this->request->get['state'] != $this->session->data['linkedincsrf']) {
			$this->response->redirect($this->url->link('checkout/checkout','',true));
		}

		$response = $this->getLinkedInAccessToken($this->request->get['code'], $this->url->link('mpcheckout/sociallogin/linkedinapi','',true));

		if(isset($response['error'])) {
			$this->session->data['error'] = $response['error'];
			$this->response->redirect($this->url->link('checkout/checkout','',true));
		}

		$user_profile = $this->getLinkedInProfile($response['access_token']);

		if(isset($user_profile['error'])) {
			$this->session->data['error'] = $user_profile['error'];
			$this->response->redirect($this->url->link('account/login','',true));	
		}

		$firstname = $user_profile['firstName'];
		$lastname = $user_profile['lastName'];
		$email = $user_profile['emailAddress'];

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