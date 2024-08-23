<?php
/* This file is under Git Control by KDSI. */
require_once (DIR_SYSTEM.'engine/ocrestapicontroller.php');

class ControllerOcrestapiOcrestapi extends OcrestapiController {

	/*Get Oauth token*/
	public function token() {

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {

			/*check rest api is enabled*/
			if (!$this->config->get('module_ocrestapi_status')) {
				$this->json["error"][] = 'Opencart Rest API is disabled. Enable it!';
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
						}
					}

					$this->session->data['token_id'] = $token['access_token'];
					$this->session->data['language'] = $oldSession['language'];
					$this->session->data['currency'] = $oldSession['currency'];

				} else {

					if (isset($token['error_description'])) {
						$this->json["error"][] = $token['error_description'];
					} else {
						$this->json["error"][] = "Token problem. Invalid token.";
					}

					$this->statusCode = 400;
				}
			}

		} else {
			$this->statusCode     = 405;
			$this->allowedHeaders = array("POST");
		}

		return $this->sendResponse();
	}

	public function session() {

		$this->checkPlugin();

		if ($_SERVER['REQUEST_METHOD'] === 'GET') {
			$this->session->start();
			$this->json['data'] = array('session' => $this->session->getId());
		} else {
			$this->statusCode     = 405;
			$this->allowedHeaders = array("GET");
		}

		return $this->sendResponse();
	}

}