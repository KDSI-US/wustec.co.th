<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleMpBlogArchive extends Controller {
	public function index() {
		if ($this->config->get('mpblog_status')) {
		static $module = 0;

		$this->load->language('extension/module/mpblogarchive');

		$this->load->model('extension/mpblog/mpblogpost');

		if (isset($this->request->get['y'])) {
			$y = $this->request->get['y'];
		} else {
			$y = null;
		}

		if (isset($this->request->get['m'])) {
			$m = $this->request->get['m'];
		} else {
			$m = null;
		}

		$data['heading_title'] = $this->language->get('heading_title');
			

			
		$month = [
			'01' => $this->language->get('text_january'),
			'02' => $this->language->get('text_february'),
			'03' => $this->language->get('text_march'),
			'04' => $this->language->get('text_april'),
			'05' => $this->language->get('text_may'),
			'06' => $this->language->get('text_june'),
			'07' => $this->language->get('text_july'),
			'08' => $this->language->get('text_august'),
			'09' => $this->language->get('text_september'),
			'10' => $this->language->get('text_october'),
			'11' => $this->language->get('text_november'),
			'12' => $this->language->get('text_december')
		];

		$results = $this->model_extension_mpblog_mpblogpost->getBlogYears();

		$data['hrefs'] = [];
		foreach ($results as $row) {

			$total = '';
			if ($this->config->get('module_mpblogarchive_blogcount')) {
			$total = ' ( ' . $this->model_extension_mpblog_mpblogpost->getTotalBlogsMonth($row['year'], $row['month']) .' )';
			}

			$data['hrefs'][] = [
				'title' => $month[$row['month']] .' ' . $row['year'] . $total,
				'href' => $this->url->link('extension/mpblog/blogs','y='.$row['year'].'&m='.$row['month'] ),
				'selected' => (($row['year'] == $y) && ($row['month'] == $m) ),
			];
		}

		$data['y'] = $y;
		$data['m'] = $m;

		$data['module'] = $module++;
		
		return $this->load->view('extension/module/mpblogarchive', $data);

		}
	}
}
