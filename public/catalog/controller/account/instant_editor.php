<?php
/* This file is under Git Control by KDSI. */

class ControllerAccountInstantEditor extends Controller
{
	private $error = array();

	public function index()
	{
		$this->load->language('account/edit');
		$json = array();

		if (!$this->customer->isLogged()){
			$json["error"] = "You have been logged out. Please log into your account again.";
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}

		$this->load->model('account/customer');

		if ($this->validate()){
			if (isset($this->request->post['firstname'])) {
				$this->model_account_customer->editFirstname($this->request->post['firstname']);
			} elseif (isset($this->request->post['lastname'])) {
				$this->model_account_customer->editLastname($this->request->post['lastname']);
			} elseif (isset($this->request->post['telephone'])) {
				$this->model_account_customer->editTelephone($this->request->post['telephone']);
			} elseif (isset($this->request->post['tax_id'])) {
				$this->model_account_customer->editTaxID($this->request->post['tax_id']);
			} elseif (isset($this->request->post['seller_permit'])) {
				$this->model_account_customer->editSellerPermit($this->request->post['seller_permit']);
			} else {
			}

			$json["success"] = "Saved";
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		} else {
			$json["error"] = $this->error;
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}


	protected function validate() {
		if (isset($this->request->post['firstname']) && ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32))) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if (isset($this->request->post['lastname']) && ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32))) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}

		if (isset($this->request->post['telephone']) && ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32))) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}

		if (isset($this->request->post['tax_id']) && ((utf8_strlen(trim($this->request->post['tax_id'])) > 50))) {
			$this->error['tax_id'] = $this->language->get('error_tax_id');
		}

		if (isset($this->request->post['seller_permit']) && ((utf8_strlen(trim($this->request->post['seller_permit'])) < 1) || (utf8_strlen(trim($this->request->post['seller_permit'])) > 50))) {
			$this->error['seller_permit'] = $this->language->get('error_seller_permit');
		}
		
		return !$this->error;
	}
}
