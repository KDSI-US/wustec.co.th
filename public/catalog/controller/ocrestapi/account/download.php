<?php
require_once(DIR_SYSTEM . 'engine/ocrestapicontroller.php');
class ControllerOcrestapiAccountDownload extends OcrestapiController {
	public function index() {
		$this->checkPlugin();

		$language = $this->load->language('account/download');

		$this->load->model('account/download');

		if (isset($this->request->get['start'])) {
			$start = $this->request->get['start'];
		} else {
			$start = 0;
		}


		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = '20';
		}

		$data['downloads'] = array();

		$download_total = $this->model_account_download->getTotalDownloads();

		$results = $this->model_account_download->getDownloads($start ,$limit);

		foreach ($results as $result) {
			if (file_exists(DIR_DOWNLOAD . $result['filename'])) {
				$size = filesize(DIR_DOWNLOAD . $result['filename']);

				$i = 0;

				$suffix = array(
					'B',
					'KB',
					'MB',
					'GB',
					'TB',
					'PB',
					'EB',
					'ZB',
					'YB'
				);

				while (($size / 1024) > 1) {
					$size = $size / 1024;
					$i++;
				}

				$data['downloads'][] = array(
					'order_id'   => $result['order_id'],
					'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
					'name'       => $result['name'],
					'size'       => round(substr($size, 0, strpos($size, '.') + 4), 2) . $suffix[$i]
					
				);
			}
		}
		unset($language['backup']);
		$this->json['language'] = $language;
		$this->json['data'] = $data;
		return $this->sendResponse();
		
	}

	public function download() {
		$this->checkPlugin();
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('ocrestapi/account/download', '', true);
			return new Action('ocrestapi/account/login');
			
		}

		$this->load->model('account/download');

		if (isset($this->request->get['download_id'])) {
			$download_id = $this->request->get['download_id'];
		} else {
			$download_id = 0;
		}

		$download_info = $this->model_account_download->getDownload($download_id);

		if ($download_info) {
			$file = DIR_DOWNLOAD . $download_info['filename'];
			$mask = basename($download_info['mask']);

			if (!headers_sent()) {
				if (file_exists($file)) {
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename="' . ($mask ? $mask : basename($file)) . '"');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					header('Content-Length: ' . filesize($file));

					if (ob_get_level()) {
						ob_end_clean();
					}

					readfile($file, 'rb');

					exit();
				} else {
					exit('Error: Could not find file ' . $file . '!');
				}
			} else {
				exit('Error: Headers already sent out!');
			}
		} else {
			return new Action('ocrestapi/account/download');
			
		}
	}
}