<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiAccountTracking extends OcrestapiController {
	public function index() {
		$data=array();
		$this->checkPlugin();
		if (!$this->customer->isLogged()) {
			return new Action('ocrestapi/account/account');
	    	}

		$this->load->model('account/customer');

		$affiliate_info = $this->model_account_customer->getAffiliate($this->customer->getId());
			
		$language=$this->load->language('account/tracking');
		if ($affiliate_info) {
	
			
	
			$data['code'] = $affiliate_info['tracking'];
	
			//$data['continue'] = $this->url->link('account/account', '', true);
	       
		    $this->json['data'] = $data;
		    
	       
			 //$this->response->setOutput($this->load->view('account/tracking', $data));
	    	 } 
		   unset($language['backup']);
		    $this->json['language'] = $language;
	        return $this->sendResponse();
	    }

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			if (isset($this->request->get['tracking'])) {
				$tracking = $this->request->get['tracking'];
			} else {
				$tracking = '';
			}
			
			$this->load->model('catalog/product');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_catalog_product->getProducts($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'link' => str_replace('&amp;', '&', $this->url->link('product/product', 'product_id=' . $result['product_id'] . '&tracking=' . $tracking))
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}