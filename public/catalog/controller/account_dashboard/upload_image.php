<?php

class ControllerAccountDashboardUploadImage extends Controller
{
	public function index()
	{
		$this->load->language('tool/upload');

		$json = array();

		if (!empty($this->request->files['profile_picture']['name']) && is_file($this->request->files['profile_picture']['tmp_name'])) {
			// Sanitize the filename
			$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['profile_picture']['name'], ENT_QUOTES, 'UTF-8')));

			// Validate the filename length
			if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 64)) {
				$json['error'] = $this->language->get('error_filename');
			}

			// Allowed file extension types
			$allowed = array();

			$extension_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('account_dashboard_file_ext_allowed'));

			$filetypes = explode("\n", $extension_allowed);

			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}

			if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Allowed file mime types
			$allowed = array();

			$mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('account_dashboard_file_mime_allowed'));

			$filetypes = explode("\n", $mime_allowed);

			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}

			if (!in_array($this->request->files['profile_picture']['type'], $allowed)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Check to see if any PHP files are trying to be uploaded
			$content = file_get_contents($this->request->files['profile_picture']['tmp_name']);

			if (preg_match('/\<\?php/i', $content)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Return any upload error
			if ($this->request->files['profile_picture']['error'] != UPLOAD_ERR_OK) {
				$json['error'] = $this->language->get('error_upload_' . $this->request->files['profile_picture']['error']);
			}
		} else {
			$json['error'] = $this->language->get('error_upload');
		}

		if (!$json) {
			$dir_name = 'profile_picture';
			$dir_upload = DIR_IMAGE . $dir_name . '/';


			if (!file_exists($dir_upload)) {
				mkdir($dir_upload, 0777, true);
			}

			if (is_file($dir_upload . $filename)) {
				$path_parts = pathinfo($dir_upload . $filename);
				if ($path_parts) {
					$file = $path_parts['filename'] . '-' . rand(1, 1000000) . '.' . $path_parts['extension'];
				} else {
					$file = $filename;
				}
			} else {
				$file = $filename;
			}

			move_uploaded_file($this->request->files['profile_picture']['tmp_name'], $dir_upload . $file);

			// Hide the uploaded file name so people can not link to it directly.
			$this->load->model('tool/upload');

			$json['profile_picture'] = $dir_name . '/' . $file;

			$json['success'] = $this->language->get('text_upload');

			$this->load->model('tool/image');
			if (is_file($dir_upload . $file)) {
				$json['profile_picture_thumb'] = $this->model_tool_image->resize($json['profile_picture'], 125, 125);
			} else {
				$json['profile_picture_thumb'] = $this->model_tool_image->resize('no_image.png', 125, 125);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}