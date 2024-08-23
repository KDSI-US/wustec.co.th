<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiAccountReward extends OcrestapiController {
	public function index() {
		$this->checkPlugin();

		$language = $this->load->language('account/reward');

		$this->load->model('account/reward');

		if (isset($this->request->get['start'])) {
			$start = $this->request->get['start'];
		} else {
			$start = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = 10;
		}

		$data['rewards'] = array();

		$filter_data = array(
			'sort'  => 'date_added',
			'order' => 'DESC',
			'start' => $start,
			'limit' => $limit
		);

		$reward_total = $this->model_account_reward->getTotalRewards();

		$results = $this->model_account_reward->getRewards($filter_data);

		foreach ($results as $result) {
			$data['rewards'][] = array(
				'order_id'    => $result['order_id'],
				'points'      => $result['points'],
				'description' => $result['description'],
				'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added']))
				
			);
		}

		
		$data['total'] = (int)$this->customer->getRewardPoints();
        unset($language['backup']);
        $this->json['language'] = $language;
        $this->json['data'] = $data;
		return $this->sendResponse();
	}
}