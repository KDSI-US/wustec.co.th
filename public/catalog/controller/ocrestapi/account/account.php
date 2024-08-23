<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiAccountAccount extends OcrestapiController {
	public function index() {
		 $this->load_language();
		 $this->load->model('setting/extension');
		if($this->request->server['REQUEST_METHOD'] == 'GET') {

		$language = $this->load->language('account/account');
		$language = $this->load->language('extension/module/account');
		$language = $this->load->language('ocrestapi/account/address');
		 $this->load->language('common/language');
		 $this->load->model('localisation/currency');
		 $this->load->model('localisation/language');
		$data = array();

		
		if ($this->config->get('config_logo') && file_exists(DIR_IMAGE . $this->config->get('config_logo'))) {
                $data['logo'] = HTTP_SERVER.'image/'. $this->config->get('config_logo');
                } else {
                $data['logo'] = '';
                }
                $data['store_name'] = $this->config->get('config_name');
                if($this->config->get('config_icon')) {
                $data['icon'] = HTTP_SERVER.'image/'. $this->config->get('config_icon');
	            }else{
	            	 $data['icon'] = '';
	            }
              
                $data['telephone'] = $this->config->get('config_telephone');
		
		

				$data['languages'] = array();

				$results = $this->model_localisation_language->getLanguages();

				foreach ($results as $result) {

					if ($result['status']) {
						$data['languages'][] = array(
							'name' => $result['name'],
							'code' => $result['code']
						);
					}	
				}


			$data['currencies'] = array();

			$results = $this->model_localisation_currency->getCurrencies();
			
			foreach ($results as $result) {
				if ($result['status']) {
					$data['currencies'][] = array(
						'title'        => $result['title'],
						'code'         => $result['code'],
						'symbol_left'  => $result['symbol_left'],
						'symbol_right' => $result['symbol_right']
					);
				}
			}
		$this->json['data'] = $data;
		unset($language['backup']);
		$this->json['language'] = $language;
		}else{
			$this->json['errors'] = ['http_method' => 'Only GET method is allowed.'];
		}
		
		return $this->sendResponse();
	}

	
}
