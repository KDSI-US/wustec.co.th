<?php
/* This file is under Git Control by KDSI. */
class ControllerAccountSuccess extends Controller {
	public function index() {
		$this->load->language('account/success');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_success'),
			'href' => $this->url->link('account/success')
		);

		if ($this->customer->isLogged()) {
			$data['text_message'] = sprintf($this->language->get('text_message'), $this->url->link('information/contact'));
		} else {
			$data['text_message'] = sprintf($this->language->get('text_approval'), $this->config->get('config_name'), $this->url->link('information/contact'));
		}


		$modulestatus=$this->config->get('registermanager_status');

		if(!empty($modulestatus)) {
			if ($this->customer->isLogged()) {
				$data['heading_title']  = $this->config->get('registermanager_title')[$this->config->get('config_language_id')]['title'];
				
				$this->load->model('account/customer');
				$this->load->model('localisation/country');
				$this->load->model('localisation/zone');
				$this->load->model('account/address');
				
				$customerinfos = $this->model_account_customer->getCustomer($this->customer->getId());
				$addressinfos  = $this->model_account_address->getAddress($customerinfos['address_id']);
				$countryinfos  = $this->model_localisation_country->getCountry($addressinfos['country_id']);
				$zoneinfos     = $this->model_localisation_zone->getZone($addressinfos['zone_id']);
				
				$find=array(
					'{customer_id}',
					'{firstname}',
					'{lastname}',
					'{email}',
					'{telephone}',
					'{address1}',
					'{address2}',
					'{citys}',
					'{postcodes}',
					'{countryname}'
				);
				
				$address1='';
				$address2='';
				$citys='';
				$postcodes='';
				$countryname='';
				$zonename='';
				
				if(isset($addressinfos['address_1'])) $address1 = $addressinfos['address_1'];
				if(isset($addressinfos['address_2'])) $address2 = $addressinfos['address_2'];
				if(isset($addressinfos['city'])) $citys = $addressinfos['city'];
				if(isset($addressinfos['postcode'])) $postcodes = $addressinfos['postcode'];
				if(isset($countryinfos['name'])) $countryname = $countryinfos['name'];
				if(isset($zoneinfos['name'])) $zonename = $zoneinfos['name'];

				$replace=array(
					'customer_id'	=> $customerinfos['customer_id'],
					'firstname'		=> $customerinfos['firstname'],
					'lastname'		=> $customerinfos['lastname'],
					'email'			=> $customerinfos['email'],
					'telephone'		=> $customerinfos['telephone'],
					'address1'		=> $address1,
					'address2'		=> $address2,
					'citys'			=> $citys,
					'postcodes'		=> $postcodes,
					'countryname'	=> $countryname,
					'zonename'		=> $zonename
				);
				
				$message = $this->config->get('registermanager_success')[$this->config->get('config_language_id')]['success'];
				$data['text_message'] = html_entity_decode(str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $message, $title)))));
				
			} else {
			
				$message = $this->config->get('registermanager_unapprovesuccess')[$this->config->get('config_language_id')]['unapprovesuccess'];
				$data['text_message'] = html_entity_decode($message);
				
			}
		}
			
		if ($this->cart->hasProducts()) {
			$data['continue'] = $this->url->link('checkout/cart');
		} else {
			$data['continue'] = $this->url->link('account/account', '', true);
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/success', $data));
	}
}