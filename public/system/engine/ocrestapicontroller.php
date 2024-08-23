<?php
error_reporting(E_ALL&~E_NOTICE);
abstract class OcrestapiController extends Controller {
	public $statusCode                = 200;
	public $post                      = array();
	public $allowedHeaders            = array("GET", "POST", "PUT", "DELETE");
	public $accessControlAllowHeaders = array("Content-Type", "Authorization", "X-Requested-With",
		"X-Oc-Merchant-Language", "X-Oc-Currency", "X-Oc-Image-Dimension", "X-Oc-Store-Id", "X-Oc-Session", "X-Oc-Include-Meta");
	public $json = array("status" => true, "errors" => array(), "data" => array());

	public $multilang       = 0;
	public $opencartVersion = "";
	public $urlPrefix       = "";
	public $includeMeta     = true;

	public $apiRequest = null;

	private $httpVersion = "HTTP/1.1";

	public function checkUnauth()
	{ 
		$this->load_language();
	}

	public function checkPlugin() {
		/*check rest api is enabled*/

		$headers = $this->getRequestHeaders();
		// print_r($headers); die;
		//if(isset($headers['authorization']) && !empty($headers['authorization'])){

		if (!$this->config->get('module_ocrestapi_status')) {
			$this->json["errors"][] = 'Opencart Rest API is disabled. Enable it!';
			$this->statusCode      = 403;
			$this->sendResponse();
		}

		if (!$this->ipValidation()) {
			$this->statusCode = 403;
			$this->sendResponse();
		}

		$this->opencartVersion = str_replace(".", "", VERSION);
		$this->urlPrefix       = $this->request->server['HTTPS']?HTTPS_SERVER:HTTP_SERVER;

		$this->validateToken();

		$token = $this->getTokenValue();

		$this->update_session($token['access_token'], json_decode($token['data'], true));

		$this->setSystemParameters();
		$this->load_language();

	// } else if(isset($headers['x-oc-session']) && !empty($headers['x-oc-session'])  && !isset($headers['authorization'])){

	// 		$this->load_language();
	// } else {
	// 	$this->load_language();
	// }

	}

	public function sendResponse() {

		$statusMessage = $this->getHttpStatusMessage($this->statusCode);

		//fix missing allowed OPTIONS header
		$this->allowedHeaders[] = "OPTIONS";

		if ($this->statusCode != 200) {
			if (!isset($this->json["errors"])) {
				$this->json["errors"][] = $statusMessage;
			}
			$this->json["status"] = false;

			if ($this->statusCode == 405 && $_SERVER['REQUEST_METHOD'] !== 'OPTIONS') {
				$this->response->addHeader('Allow: '.implode(",", $this->allowedHeaders));
			}
			//enable OPTIONS header
			if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
				$this->statusCode      = 200;
				$this->json["status"] = true;
				$this->json["errors"]   = array();
			}else{
				if($this->statusCode == 401) {
					http_response_code(401);
				}
			}
		} else {

			if (!empty($this->json["errors"])) {
				$this->statusCode      = 400;
				$this->json["status"] = false;
			}
			//add cart errors to the response
			// if (isset($this->json["cart_error"]) && !empty($this->json["cart_error"])) {
			// 	$this->json["errors"] = $this->json["cart_error"];
			// 	unset($this->json["cart_error"]);
			// }
		}

		// $this->json["errors"] = array_values($this->json["errors"]);
		$this->json["errors"] = $this->json["errors"];

		if (isset($this->request->server['HTTP_ORIGIN'])) {
			$this->response->addHeader('Access-Control-Allow-Origin: '.$this->request->server['HTTP_ORIGIN']);
			$this->response->addHeader('Access-Control-Allow-Methods: '.implode(", ", $this->allowedHeaders));
			$this->response->addHeader('Access-Control-Allow-Headers: '.implode(", ", $this->accessControlAllowHeaders));
			$this->response->addHeader('Access-Control-Allow-Credentials: true');
		}

		$this->load->model('account/customer');

		if (isset($this->session->data['token_id']) || isset($_SESSION['token_id'])) {
			$token                                  = $this->session->data['token_id'];
			$this->session->data['rest_session_id'] = $this->session->getId();
			$this->model_account_customer->updateSession($this->session->data, $token);
		}

		if (isset($this->session->data['customer_id']) && !empty($this->session->data['customer_id'])) {
			
			$this->model_account_customer->updateCustomerData($this->session, $this->session->data['customer_id']);
		}

		//$this->response->addHeader($this->httpVersion . " " . $this->statusCode . " " . $statusMessage);

		$this->response->addHeader('Content-Type: application/json; charset=utf-8');

		if (defined('JSON_UNESCAPED_UNICODE')) {
			$this->response->setOutput(json_encode($this->json, JSON_UNESCAPED_UNICODE));
		} else {
			$this->response->setOutput($this->rawJsonEncode($this->json));
		}

		// print_r($this->statusCode);die;
		$this->response->output();
		exit;
	}

	public function getHttpStatusMessage($statusCode) {
		$httpStatus = array(
			200=> 'OK',
			400=> 'Bad Request',
			401=> 'Unauthorized',
			403=> 'Forbidden',
			404=> 'Not Found',
			405=> 'Method Not Allowed',
		);

		return ($httpStatus[$statusCode])?$httpStatus[$statusCode]:$httpStatus[500];
	}

	private function rawJsonEncode($input, $flags = 0) {
		$fails = implode('|', array_filter(array(
					'\\\\',
					$flags&JSON_HEX_TAG?'u003[CE]':'',
					$flags&JSON_HEX_AMP?'u0026':'',
					$flags&JSON_HEX_APOS?'u0027':'',
					$flags&JSON_HEX_QUOT?'u0022':'',
				)));
		$pattern  = "/\\\\(?:(?:$fails)(*SKIP)(*FAIL)|u([0-9a-fA-F]{4}))/";
		$callback = function ($m) {
			return html_entity_decode("&#x$m[1];", ENT_QUOTES, 'UTF-8');
		};
		return preg_replace_callback($pattern, $callback, json_encode($input, $flags));
	}

	public function validateToken($check = false) {

		// Handle a request to a resource and authenticate the access token
		$server = $this->getOauthServer();

		$this->apiRequest = OAuth2\Request::createFromGlobals();

		if (!$server->verifyResourceRequest($this->apiRequest)) {

			$serverResp = $server->getResponse();

			// $error_description = 'invelid token';
			 $error_description = $serverResp->getParameter('error_description');
			if($check == true){
				return false;
			}
			$this->json['errors'] = array(
				'error_description' => !empty($error_description)
				 && $error_description != "NULL"?$error_description:$serverResp->getStatusText()
			);

			$this->statusCode = $serverResp->getStatusCode();

			$this->sendResponse();

		}
		if($check == true){
			return true;
		}
	}

	public function getOauthServer() {
		//$dsn      = DB_DRIVER.':dbname='.DB_DATABASE.';host='.DB_HOSTNAME;
		$dsn      = 'mysql:dbname='.DB_DATABASE.';host='.DB_HOSTNAME;
		$username = DB_USERNAME;
		$password = DB_PASSWORD;

		$_POST['grant_type'] = $_GET['grant_type'] = 'client_credentials';
		// Autoloading (composer is preferred, but for this example let's just do this)
		require_once (DIR_SYSTEM.'oauth2-server-php/src/OAuth2/Autoloader.php');
		OAuth2\Autoloader::register();

		$config = array(
			'id_lifetime'     => $this->config->get('module_ocrestapi_token_ttl'),
			'access_lifetime' => $this->config->get('module_ocrestapi_token_ttl')
		);
		// $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
		$storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));

		// Pass a storage object or array of storage objects to the OAuth2 server class
		$oauthServer = new OAuth2\Server($storage, $config);

		// Add the "Client Credentials" grant type (it is the simplest of the grant types)
		$oauthServer->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
		
		//return $storage;
		return $oauthServer;
	}

	private function getTokenValue() {
		$server = $this->getOauthServer();

		return $server->getAccessTokenData($this->apiRequest);
	}

	private function update_session($token, $data) {

		if (isset($data['rest_session_id'])) {
			$oldSession = $this->session->getId();
			$this->session->start($data['rest_session_id']);
			$data['old_session_id'] = $oldSession;
		}

		if (!empty($data)) {
			$this->session->data = $data;
		}

		$this->session->data['token_id'] = $token;

		if (isset($data['customer_id']) && !empty($data['customer_id'])) {
			$this->load->model('account/customer');
			$customer_info = $this->model_account_customer->loginCustomerById($data['customer_id']);

			if ($customer_info) {
				$this->customer->login($customer_info['email'], "", true);
			}

			if ($this->customer->isLogged()) {
				// Logged in customers
				$this->config->set('config_customer_group_id', $this->customer->getGroupId());
			} elseif (isset($this->session->data['guest']) && isset($this->session->data['guest']['customer_group_id'])) {
				$this->config->set('config_customer_group_id', $this->session->data['guest']['customer_group_id']);
			}
		}

		$this->tax->unsetRates();

	
		if (isset($this->session->data['shipping_address']['country_id'])) {
			$this->tax->setShippingAddress($this->session->data['shipping_address']['country_id'], $this->session->data['shipping_address']['zone_id']);
		} elseif ($this->config->get('config_tax_default') == 'shipping') {
			$this->tax->setShippingAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
		}

		if (isset($this->session->data['payment_address']['country_id'])) {
			$this->tax->setPaymentAddress($this->session->data['payment_address']['country_id'], $this->session->data['payment_address']['zone_id']);
		} elseif ($this->config->get('config_tax_default') == 'payment') {
			$this->tax->setPaymentAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
		}

		$this->tax->setStoreAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));

		$this->registry->set('cart', new Cart\Cart($this->registry));
	}

	private function setSystemParameters() {

		$headers = $this->getRequestHeaders();
		//set currency
		if (isset($headers['x-oc-currency'])) {
			$currency = $headers['x-oc-currency'];
			if (!empty($currency)) {
				$this->currency->setRestCurrencyCode($currency);
				$this->session->data['currency'] = $currency;
			} else {
				$this->currency->setRestCurrencyCode($this->session->data['currency']);
			}
		} else {
			$this->currency->setRestCurrencyCode($this->session->data['currency']);
		}

		//show meta information in the response
		//        if (isset($headers['x-oc-include-meta'])) {
		//            $this->includeMeta = true;
		//        }

		//set store ID
		if (isset($headers['x-oc-store-id'])) {
			$this->config->set('config_store_id', $headers['x-oc-store-id']);
		}

		$this->load->model('localisation/language');
		$allLanguages = $this->model_localisation_language->getLanguages();

		if (count($allLanguages) > 1) {
			$this->multilang = 1;
		}

		

		if (isset($headers['x-oc-image-dimension'])) {
			$d = $headers['x-oc-image-dimension'];
			$d = explode('x', $d);
			$this->config->set('config_ocrestapi_image_width', $d[0]);
			$this->config->set('config_ocrestapi_image_height', $d[1]);
		} else {
			$this->config->set('config_ocrestapi_image_width', 500);
			$this->config->set('config_ocrestapi_image_height', 500);
		}
	}

	public function getpages($total_count,$limit){
		$ceil=1;
		$ceil=ceil($total_count/$limit);
		$returnarray=array();
		if($ceil){
			for($i=1;$i<=$ceil;$i++){
				$returnarray[]=array('text'=>$i,'value'=>$i);
			}
		}
		return $returnarray;
	}

// 	public function load_language()
// 	{
// 		$headers = $this->getRequestHeaders();

// 		$this->getPost();
// 		//set language


// 		if((isset($headers['x-oc-session']) && !empty($headers['x-oc-session'])) && (!isset($headers['authorization']) && empty($headers['authorization'])) ) {
			
// 			$oldSession = $this->session->getId();
			
// 			$this->session->start($headers['x-oc-session']);
// 			$data['old_session_id'] = $oldSession;

// 		} else if(isset($headers['authorization']) && !empty($headers['authorization'])){

// 			$this->validateToken();

// 		$token = $this->getTokenValue();

// 		if((isset($headers['x-oc-session']) && !empty($headers['x-oc-session']))){
// 			$token['session_id']=$headers['x-oc-session'];
// 			$json_decode_data=json_decode($token['data']);
// 			$json_decode_data->rest_session_id=$headers['x-oc-session'];
// 			$token['data']=json_encode($json_decode_data);
// 		}

// //print_r($token['data']); die;
// 		$this->update_session($token['access_token'], json_decode($token['data'], true));



// 			$oldSession = $this->session->getId();
			
// 			$this->session->start($headers['x-oc-session']);

// 			$data['old_session_id'] = $oldSession;

// 		}
		
// 		//print_r($headers); die;
// 		if (isset($headers['x-oc-merchant-language'])) {
// 			$osc_lang = $headers['x-oc-merchant-language'];

// 			$languages = array();
// 			$this->load->model('localisation/language');
// 			$all = $this->model_localisation_language->getLanguages();

// 			foreach ($all as $result) {
// 				$languages[$result['code']] = $result;
// 			}
// 			if (isset($languages[$osc_lang])) {
// 				$this->session->data['language'] = $osc_lang;
// 				$this->config->set('config_language', $osc_lang);
// 				$this->config->set('config_language_id', $languages[$osc_lang]['language_id']);

// 				if (isset($languages[$osc_lang]['directory']) && !empty($languages[$osc_lang]['directory'])) {
// 					$directory = $languages[$osc_lang]['directory'];
// 				} else {
// 				}
// 				$directory = $languages[$osc_lang]['code'];
				
// 				$language = new \Language($directory);
// 				$language->load($directory);
// 				$this->registry->set('language', $language);
// 			}
// 		}
// 	}
	public function load_language()
	{
		$headers = $this->getRequestHeaders();
		$this->getPost();
		if (isset($headers['rest_session_id'])) {
			$oldSession = $this->session->getId();
			$this->session->start($headers['rest_session_id']);
			$data['old_session_id'] = $oldSession;
		}
		
		//set language
		if (isset($headers['x-oc-merchant-language'])) {
			$osc_lang = $headers['x-oc-merchant-language'];

			$languages = array();
			$this->load->model('localisation/language');
			$all = $this->model_localisation_language->getLanguages();

			foreach ($all as $result) {
				$languages[$result['code']] = $result;
			}
			if (isset($languages[$osc_lang])) {
				$this->session->data['language'] = $osc_lang;
				$this->config->set('config_language', $osc_lang);
				$this->config->set('config_language_id', $languages[$osc_lang]['language_id']);

				if (isset($languages[$osc_lang]['directory']) && !empty($languages[$osc_lang]['directory'])) {
					$directory = $languages[$osc_lang]['directory'];
				} else {
				}
				$directory = $languages[$osc_lang]['code'];
				
				$language = new \Language($directory);
				$language->load($directory);
				$this->registry->set('language', $language);
			}
		}
	}

	public function getRequestHeaders() {
		$arh     = array();
		$rx_http = '/\AHTTP_/';

		foreach ($_SERVER as $key => $val) {
			if (preg_match($rx_http, $key)) {
				$arh_key = preg_replace($rx_http, '', $key);

				$rx_matches = explode('_', $arh_key);

				if (count($rx_matches) > 0 and strlen($arh_key) > 2) {
					foreach ($rx_matches as $ak_key => $ak_val) {
						$rx_matches[$ak_key] = ucfirst($ak_val);
					}

					$arh_key = implode('-', $rx_matches);
				}

				$arh[strtolower($arh_key)] = $val;
			}
		}

		return ($arh);
	}
	public function get_request_token()
	{
		$headers = $this->getRequestHeaders();
	if(!empty($headers['authorization'])){
		$token = $headers['authorization'];
		return $token;
	}else{
		return false;
	}
	}
	public function getPost() {

		if ($this->request->server['REQUEST_METHOD'] != 'POST'){
			return;
		}
		$request = false;

		$post = $this->request->post;
		if (is_array($post) && !empty($post)) {
			$request = true;
			$this->request->post = $post;
			// return $post;
		}
		$jsonPost = json_decode(file_get_contents('php://input'), true);
		if (is_array($jsonPost) && !empty($jsonPost)) {
			$request = true;
			$this->request->post = $jsonPost;
			// die('kjkj');
			// return $jsonPost;
		}

		if(!$request) {
			$this->statusCode      = 400;
			$this->json['error'][] = 'Invalid request body, please validate the json object';
			$this->sendResponse();
		}

		// return $post;
	}

	public function returnDeprecated() {
		$this->statusCode    = 400;
		$this->json['error'] = "This service has been removed for security reasons.Please contact us for more information.";

		return $this->sendResponse();
	}

	public function clearTokensTable($token = null, $sessionid = null) {
		//delete all previous token to this session and delete all expired session
		$this->load->model('account/customer');
		$this->model_account_customer->clearTokens($token, $sessionid);
	}

	private function ipValidation() {
		$allowedIPs = $this->config->get('module_ocrestapi_allowed_ip');

		if (!empty($allowedIPs)) {
			$ips = explode(",", $allowedIPs);

			$ips = array_map(
				function ($ip) {
					return trim($ip);
				},
				$ips
			);

			if (!in_array($_SERVER['REMOTE_ADDR'], $ips)
				 || (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && !in_array($_SERVER["HTTP_X_FORWARDED_FOR"], $ips))
			) {
				return false;
			} else {
				return true;
			}
		}
		return true;
	}

	public function getLoggedinToken() {

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {

			/*check rest api is enabled*/
			if (!$this->config->get('module_ocrestapi_status')) {
				$this->json["errors"][] = 'Opencart Rest API is disabled. Enable it!';
			} else {

				 $server = $this->getOauthServer();

				$this->request->post['grant_type']    = 'client_credentials';
				$this->request->post['client_id']     = $this->config->get('module_ocrestapi_client_id');
				$this->request->post['client_secret'] = $this->config->get('module_ocrestapi_client_secret');
				$_SERVER['PHP_AUTH_USER']             = $this->config->get('module_ocrestapi_client_id');
				$_SERVER['PHP_AUTH_PW']               = $this->config->get('module_ocrestapi_client_secret');
				header('PHP_AUTH_USER: '.$this->config->get('module_ocrestapi_client_id'));
				header('PHP_AUTH_PW: '.$this->config->get('module_ocrestapi_client_secret'));
				$str     = $this->config->get('module_ocrestapi_client_id').':'.$this->config->get('module_ocrestapi_client_secret');
				$authstr = base64_encode($str);
				header('AUTHORIZATION: '.$authstr);

				$input = file_get_contents('php://input');
				$post  = json_decode($input, true);

				$oldToken     = isset($post['old_token'])?$post['old_token']:null;
				$oldTokenData = null;
				$this->load->model('account/customer');

				if (!empty($oldToken)) {
					$oldTokenData = $this->model_account_customer->loadOldToken($oldToken);
				}

				$token = $server->handleTokenRequest(OAuth2\Request::createFromGlobals())->getParameters();

				if (!empty($oldTokenData)) {
					$this->model_account_customer->loadSessionToNew($oldTokenData['data'], $token['access_token']);
					$this->model_account_customer->deleteOldToken($oldToken);
				}

				if (isset($token['access_token'])) {
					//clear token table
					$this->clearTokensTable($token['access_token'], $this->session->getId());

					unset($token['scope']);

					$token['expires_in'] = (int) $token['expires_in'];

					$this->json['data'] = $token;

					$oldSession = $this->session->data;

					if (empty($oldTokenData)) {
						$this->customer->logout();
						$this->session->start(0);
					} else {

						if (isset($oldTokenData['data'])) {
							$data = json_decode($oldTokenData['data'], true);
							$this->session->start($data['rest_session_id']);
							$this->update_session($token['access_token'], json_decode($data, true));
						}
					}

					$this->session->data['token_id']    = $token['access_token'];
					$this->session->data['language']    = $oldSession['language'];
					$this->session->data['currency']    = $oldSession['currency'];
					$this->session->data['customer_id'] = $oldSession['customer_id'];

				} else {

					if (isset($token['error_description'])) {
						$this->json["errors"][] = $token['error_description'];
					} else {
						$this->json["errors"][] = "Token problem. Invalid token.";
					}

					$this->statusCode = 400;
				}
			}

		} else {
			$this->statusCode     = 405;
			$this->allowedHeaders = array("POST");
		}

		return $this->json;
		// return $this->sendResponse();

	}

}