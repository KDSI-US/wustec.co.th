<?php
class ControllerExtensionModulePastorColumnLeft extends Controller
{
	public function index()
	{
		$this->load->language('extension/module/pastor_column_left');
		$data['heading_title'] = $this->language->get('heading_title');
		$this->load->model('extension/pastor_column_category');
		$this->load->model('extension/pastor_column');
		if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
		} else {
			$parts = array();
		}
		if (isset($parts[0])) {
			$data['pastor_column_category_id'] = $parts[0];
		} else {
			$data['pastor_column_category_id'] = 0;
		}
		$data['pastor_columns'] = array();
		$filter_data = array(
			'start' => 0,
			'limit' => 20
		);
		$pastor_column_info = $this->model_extension_pastor_column_category->getPastorColumnPhotos($filter_data);

		if ($pastor_column_info) {
			foreach ($pastor_column_info as $info) {
				$filter_data = array(
					'pastor_column_category_id' => $info['pastor_column_category_id']
				);
				$pastor_total = 0;
				$pastor_total = $this->model_extension_pastor_column->getTotalPastorColumns($filter_data);
				$pastor_column_left_count = $this->config->get('pastor_column_left_count');
				if ($pastor_column_left_count == 1) {
					$infotitle = $info['title'] . '  (' . ($pastor_total) . ')';
				} else {
					$infotitle = $info['title'];
				}
				$data['pastor_columns'][] = array(
					'pastor_column_category_id' => $info['pastor_column_category_id'],
					'title'	   		=> $infotitle,
					'chk_title'	=> $info['chk_title'],
					'href'	      => $this->url->link('extension/pastor_column_all' . '&pastor_column_category_id=' . $info['pastor_column_category_id'])
				);
			}
			if ($data['pastor_columns']) {
				return $this->load->view('extension/module/pastor_column_left', $data);
			}
		}
	}
}
