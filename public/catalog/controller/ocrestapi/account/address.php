<?php
require_once (DIR_SYSTEM.'engine/ocrestapicontroller.php');
class ControllerOcrestapiAccountAddress extends OcrestapiController {
	private $error = array();

	public function index() {
		$this->checkPlugin();
	
		$this->load->language('account/address');
		$this->load->language('ocrestapi/account/address');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('account/address');
		$this->getList();
	}

	public function add() {


		$this->checkPlugin();
		$data = [];

		$language = $this->load->language('account/address');
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
				// echo "kkk";print_r($this->request->post); die;
			if(!$this->validateForm()){
				$this->json['status'] = false;
				$this->json['errors'] = $this->error;
				return $this->sendResponse();
			}
			$this->load->model('account/address');

			$address_id=$this->model_account_address->addAddress($this->customer->getId(), $this->request->post);
			$this->json['data']['address_id'] = $address_id;
			
			$this->json['data']['success'] = $this->language->get('text_add');
			$this->sendResponse();
			
		}else{
			$this->load->model('localisation/country');
			$this->load->model('localisation/zone');
			$countries = $this->model_localisation_country->getCountries();	
				$states = [];
               	foreach ($countries as $country) {
               		$states[$country['country_id']] = $this->model_localisation_zone->getZonesByCountryId($country['country_id']);
               	}
               	$data['country_id'] = $this->config->get('config_country_id');
               	$data['states'] = $states;
				
				unset($language['backup']);
				$this->json['language'] = $language;
               	$data['countries'] = $countries;
			$this->json['data'] = $data;
		}
		
		$this->sendResponse();
	}

	public function edit() {
		$this->checkPlugin();

		$language =  $this->load->language('account/address');
		$language =  $this->load->language('ocrestapi/account/address');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/address');
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
				if(! $this->validateForm())
				{
				$this->json['status'] = false;
				$this->json['errors'] = $this->error;
				return $this->sendResponse();
				}
			$this->model_account_address->editAddress($this->request->get['address_id'], $this->request->post);
			$this->json['data']['address_id'] = $this->request->get['address_id'];
		
			$this->json['data']['success'] = $this->language->get('text_edit');
			$this->sendResponse();
			
		}else{
			$this->load->model('localisation/country');
			$this->load->model('localisation/zone');
			$this->load->model('ocrestapi/ocrestapi');
			$address_id = $this->request->get['address_id'];
			$address = $this->model_account_address->getAddress($address_id);
			$default_addressid= $this->customer->getAddressId();
			if(empty($address)){
				$this->json['status'] = false;
				$this->json['errors'] = ['invalid_address_id'=>$this->language->get('error_invalid_address_id')];

			}else{
               	$countries = $this->model_localisation_country->getCountries();
               	$states = [];
               	foreach ($countries as $country) {
               		$states[$country['country_id']] = $this->model_localisation_zone->getZonesByCountryId($country['country_id']);
               	}
               	$data['states'] = $states;
				$data['address'] = $address;
				$data['address']['default'] = ($address_id==$default_addressid)?'1':'0';
               	$data['countries'] = $countries;
			   	$this->json['data'] = $data;
			}		
			unset($language['backup']);
		$this->json['language'] = $language;
		$this->sendResponse();
		}
		
	}

	public function delete() {

		$this->checkPlugin();

		$this->load->language('account/address');
		
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/address');

			
		if (isset($this->request->post['address_id'])) {

			if(!$this->validateDelete())
			{
					$this->json['status'] = false;
					$this->json['errors'] = $this->error;
				return $this->sendResponse();
			}
			
			$this->model_account_address->deleteAddress($this->request->post['address_id']);
			$this->session->data['success'] = $this->language->get('text_delete');
			$this->json['data']['success'] = $this->session->data['success'];

		}
		
		$this->sendResponse();
	}

	protected function getList() {
 	if($this->request->server['REQUEST_METHOD'] == 'GET') {
		$this->checkPlugin();
		$this->load_language();
		$data['addresses'] = array();

		$results = $this->model_account_address->getAddresses();
        $customer_id=$this->customer->getId();
        $default_addressid= $this->customer->getAddressId();


		foreach ($results as $result) {
			if ($result['address_format']) {
				$format = $result['address_format'];
			} else {
				$format = '{firstname} {lastname}'."\n".'{company}'."\n".'{address_1}'."\n".'{address_2}'."\n".'{city} {postcode}'."\n".'{zone}'."\n".'{country}';
			}

			$find = array(
				'{firstname}',
				'{lastname}',
				'{company}',
				'{address_1}',
				'{address_2}',
				'{city}',
				'{postcode}',
				'{zone}',
				'{zone_code}',
				'{country}',
			);

			$replace = array(
				'firstname' => $result['firstname'],
				'lastname'  => $result['lastname'],
				'company'   => $result['company'],
				'address_1' => $result['address_1'],
				'address_2' => $result['address_2'],
				'city'      => $result['city'],
				'postcode'  => $result['postcode'],
				'zone'      => $result['zone'],
				'zone_code' => $result['zone_code'],
				'country'   => $result['country'],
			);
          
			$data['addresses'][] = array(
				'address_id' => $result['address_id'],
				'firstname'  => $result['firstname'],
				'lastname'   => $result['lastname'],
				'company'    => $result['company'],
				'address_1'  => $result['address_1'],
				'address_2'  =>  $result['address_2'],
				'city'       => $result['city'],
				'postcode'   => $result['postcode'],
				'default'    => ($default_addressid==$result['address_id'])?1:0,
				'zone'       => $result['zone'],
				'zone_code'  => $result['zone_code'],
				'country'    => $result['country'],
				'address'    => str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))))
				
			);
			$this->json['data'] = $data;
		}
		}else{
			$this->json['errors'] = [ 'http_method' => 'Only GET method is allowed.'];
		}

		
		$language=$this->load->language('account/address');
		unset( $language['backup']);
		$this->json['language'] = $language;

		return $this->sendResponse();
	}



	protected function validateForm() {



		$this->load->language('ocrestapi/account/address');
		 
		if(!isset($this->request->post['firstname'])) {
			$this->error['firstname'] = $this->language->get('error_required_firstname');
		}elseif ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if(!isset($this->request->post['lastname'])) {
			$this->error['lastname'] = $this->language->get('error_required_lastname');
		}elseif ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}

		if(!isset($this->request->post['address_1'])) {
			$this->error['address_1'] = $this->language->get('error_required_address1');
		}elseif ((utf8_strlen(trim($this->request->post['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['address_1'])) > 128)) {
			$this->error['address_1'] = $this->language->get('error_address_1');
		}

		if(!isset($this->request->post['city'])) {
			$this->error['city'] = $this->language->get('error_required_city');
		}elseif ((utf8_strlen(trim($this->request->post['city'])) < 2) || (utf8_strlen(trim($this->request->post['city'])) > 128)) {
			$this->error['city'] = $this->language->get('error_city');
		}
		if(!isset($this->request->post['address_2'])){
			$this->request->post['address_2']='';
		}

		if(!isset($this->request->post['company'])){
			$this->request->post['company']='';
		}

		if(!isset($this->request->post['country_id'])) {
			$this->error['country_id'] = $this->language->get('error_required_country');
			}else{
				$this->load->model('localisation/country');
				$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);				
				 
				if ($this->request->post['country_id'] == '' || !is_numeric($this->request->post['country_id'])) {
					$this->error['country_id'] = $this->language->get('error_country');
				}
			}


			if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '' || !is_numeric($this->request->post['zone_id'])) {
				$this->error['zone_id'] = $this->language->get('error_zone');
			}

			if(!isset($this->request->post['postcode'])) {
			$this->error['postcode'] = $this->language->get('error_postcode');
			}
			else{
				if ($this->request->post['postcode'] == '' || !is_numeric($this->request->post['postcode'])) {
					$this->error['postcode'] = $this->language->get('error_postcode');
				}
			}
			
		// Custom field validation
		$this->load->model('account/custom_field');

		$custom_fields = $this->model_account_custom_field->getCustomFields($this->config->get('config_customer_group_id'));

		foreach ($custom_fields as $custom_field) {
			if ($custom_field['location'] == 'address') {
				if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
					$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
					$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				}
			}
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if ($this->model_account_address->getTotalAddresses() == 1) {
			$this->error['warning'] = $this->language->get('error_delete');
		}

		if ($this->customer->getAddressId() == $this->request->post['address_id']) {
			$this->error['warning'] = $this->language->get('error_default');
		}

		return !$this->error;
	}
}