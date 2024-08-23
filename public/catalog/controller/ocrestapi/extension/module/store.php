<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiExtensionModuleStore extends OcrestapiController {
	public function index() {
		$this->checkPlugin();
		
		$status = true;

		if ($this->config->get('module_store_admin')) {
			$this->user = new Cart\User($this->registry);

			$status = $this->user->isLogged();
		}

		if ($status) {
			$this->load->language('extension/module/store');

			$data['store_id'] = $this->config->get('config_store_id');

			$data['stores'] = array();

			$data['stores'][] = array(
				'store_id' => 0,
				'name'     => $this->language->get('text_default'),
			);

			$this->load->model('setting/store');

			$results = $this->model_setting_store->getStores();

			foreach ($results as $result) {
				$data['stores'][] = array(
					'store_id' => $result['store_id'],
					'name'     => $result['name'],
					'url'      => $result['url'] . 'index.php?route=common/home&session_id=' . $this->session->getId()
				);
			}

			$this->json['data'] = $data;
		
			return $this->sendResponse();

		}
	}
}
