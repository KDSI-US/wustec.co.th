<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleMysqlJobs extends Controller
{
	private $error = array();

	public function index()
	{
		$this->load->language('extension/module/mysql_jobs');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/module/mysql_jobs');

		$this->install();
		$this->getList();
	}

	public function install()
	{
		$strSql = "
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mysql_jobs` 
			(
				id INT AUTO_INCREMENT, 
				date_added DATE, 
				description TEXT, 
				query TEXT, 
				execution_last DATE DEFAULT NULL, 
				execution_next DATE, 
				execution_hour INT, 
				execution_time TIME, 
				execution_interval INT DEFAULT 1, 
				email TEXT, 
				status TINYINT(1), 
				PRIMARY KEY (id) 
			);
		";
		$this->db->query($strSql);
	}

	public function uninstall()
	{
		// $this->db->query("DROP TABLE IN EXISTS " . DB_PREFIX . "jobs");
	}

	public function cron()
	{
		$this->load->model('extension/module/mysql_jobs');
		$mysql_jobs = $this->model_extension_module_mysql_jobs->getMySQLJobs();

		foreach ($mysql_jobs as $mysql_job) {
			if ($mysql_job['status'] == 1) {
				if (date("Y-m-d") >= $mysql_job['execution_next']) {
					if (date("H") == $mysql_job['execution_hour']) {
						$this->execute($mysql_job['id'], true);
						$this->model_extension_module_mysql_jobs->UpdateDate($mysql_job['id'], date('Y-m-d', strtotime(date('Y-m-d') . ' + ' . $mysql_job['execution_interval'] . ' days')));
					}
				}
			}
		}
	}

	public function add()
	{
		$this->load->language('extension/module/mysql_jobs');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/module/mysql_jobs');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_module_mysql_jobs->addMySQLJob($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/module/mysql_jobs', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->getForm();
	}

	public function edit()
	{
		$this->load->language('extension/module/mysql_jobs');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/module/mysql_jobs');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_module_mysql_jobs->editMySQLJob($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/module/mysql_jobs', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->load->language('extension/module/mysql_jobs');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/module/mysql_jobs');

		if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $mysql_job_id) {
				$this->model_extension_module_mysql_jobs->deleteMySQLJob($mysql_job_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/module/mysql_jobs', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->getList();
	}

	protected function getList()
	{
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/mysql_jobs', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['add'] = $this->url->link('extension/module/mysql_jobs/add', 'user_token=' . $this->session->data['user_token'], true);
		$data['delete'] = $this->url->link('extension/module/mysql_jobs/delete', 'user_token=' . $this->session->data['user_token'], true);

		$data['jobs'] = array();

		$results = $this->model_extension_module_mysql_jobs->getMySQLJobs();

		foreach ($results as $result) {
			$data['jobs'][] = array(
				'id'  	=> $result['id'],
				'description' => $result['description'],
				'exec_last'   => (($result['execution_last'] !== '0000-00-00') ? date($this->language->get('date_format_short'), strtotime($result['execution_last'])) : '-'),
				'exec_next'   => date($this->language->get('date_format_short'), strtotime($result['execution_next'])),
				'exec_time'	  => $result['execution_time'],
				'email' 		  => $result['email'],
				'status'      => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'edit'			  => $this->url->link('extension/module/mysql_jobs/edit', 'user_token=' . $this->session->data['user_token'] . '&id=' . $result['id'], true),
				'execute'		  => $this->url->link('extension/module/mysql_jobs/execute', 'user_token=' . $this->session->data['user_token'] . '&id=' . $result['id'], true),
				'preview'		  => $this->url->link('extension/module/mysql_jobs/preview', 'user_token=' . $this->session->data['user_token'] . '&id=' . $result['id'], true)
			);
		}

		$data['system_time'] = Date("H:i:s");

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/mysql_jobs_list', $data));
	}

	protected function getForm()
	{

		$data['text_form'] = !isset($this->request->get['id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->get['id'])) {
			$data['id'] = $this->request->get['id'];
		} else {
			$data['id'] = 0;
		}

		/* Errors */
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['description'])) {
			$data['error_description'] = $this->error['description'];
		} else {
			$data['error_description'] = '';
		}

		if (isset($this->error['query'])) {
			$data['error_query'] = $this->error['query'];
		} else {
			$data['error_query'] = '';
		}

		if (isset($this->error['date_exec_next'])) {
			$data['error_date_exec_next'] = $this->error['date_exec_next'];
		} else {
			$data['error_date_exec_next'] = '';
		}

		if (isset($this->error['exec_interval'])) {
			$data['error_exec_interval'] = $this->error['exec_interval'];
		} else {
			$data['error_exec_interval'] = '';
		}

		/* Breadcrumbs */
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/mysql_jobs', 'user_token=' . $this->session->data['user_token'], true)
		);

		/* Links */
		if (!isset($this->request->get['id'])) {
			$data['action'] = $this->url->link('extension/module/mysql_jobs/add', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/mysql_jobs/edit', 'user_token=' . $this->session->data['user_token'] . '&id=' . $this->request->get['id'], true);
		}

		$data['cancel'] = $this->url->link('extension/module/mysql_jobs', 'user_token=' . $this->session->data['user_token'], true);

		/* Methods */
		if (isset($this->request->get['id']) && (!$this->request->server['REQUEST_METHOD'] != 'POST')) {
			$mysql_job_info = $this->model_extension_module_mysql_jobs->getMySQLJob($this->request->get['id']);
		}

		/* Fields */
		if (isset($this->request->post['description'])) {
			$data['description'] = $this->request->post['description'];
		} elseif (!empty($mysql_job_info)) {
			$data['description'] = $mysql_job_info['description'];
		} else {
			$data['description'] = '';
		}

		if (isset($this->request->post['query'])) {
			$data['query'] = $this->request->post['query'];
		} elseif (!empty($mysql_job_info)) {
			$data['query'] = $mysql_job_info['query'];
		} else {
			$data['query'] = '';
		}

		if (isset($this->request->post['date_exec_next'])) {
			$data['date_exec_next'] = $this->request->post['date_exec_next'];
		} elseif (!empty($mysql_job_info)) {
			$data['date_exec_next'] = $mysql_job_info['execution_next'];
		} else {
			$data['date_exec_next'] = '';
		}

		if (isset($this->request->post['exec_hour'])) {
			$data['execution_hour'] = $this->request->post['exec_hour'];
		} elseif (!empty($mysql_job_info)) {
			$data['execution_hour'] = $mysql_job_info['execution_hour'];
		} else {
			$data['execution_hour'] = '';
		}

		if (isset($this->request->post['time'])) {
			$data['time'] = $this->request->post['time'];
		} elseif (!empty($mysql_job_info)) {
			$data['time'] = $mysql_job_info['execution_time'];
		} else {
			$data['time'] = '';
		}

		if (isset($this->request->post['interval'])) {
			$data['interval'] = $this->request->post['interval'];
		} elseif (!empty($mysql_job_info)) {
			$data['interval'] = $mysql_job_info['execution_interval'];
		} else {
			$data['interval'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($mysql_job_info)) {
			$data['email'] = $mysql_job_info['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($contest_info)) {
			$data['status'] = $contest_info['status'];
		} else {
			$data['status'] = true;
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/mysql_jobs_form', $data));
	}

	protected function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'extension/module/mysql_jobs')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (utf8_strlen($this->request->post['description']) < 1) {
			$this->error['warning'] = $this->language->get('error_description');
		}

		if (utf8_strlen($this->request->post['query']) == 0) {
			$this->error['warning'] = $this->language->get('error_query');
		}

		if (utf8_strlen($this->request->post['exec_interval']) == 0) {
			$this->request->post['exec_interval'] = 0;
		} else if (!is_numeric($this->request->post['exec_interval'])) {
			$this->error['warning'] = $this->language->get('error_interval');
		}

		return !$this->error;
	}


	public function preview($mysql_job_id = null)
	{
		$this->load->language('extension/module/mysql_jobs');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/module/mysql_jobs');

		if ($mysql_job_id != null) {
			$mysql_job_info = $this->model_extension_module_mysql_jobs->getMySQLJob($mysql_job_id);
		} else {
			$mysql_job_info = $this->model_extension_module_mysql_jobs->getMySQLJob($this->request->get['id']);
		}

		$query = $this->model_extension_module_mysql_jobs->execute(html_entity_decode($mysql_job_info['query'], ENT_QUOTES, 'UTF-8'));

		$data = array();
		$data['name'] = $mysql_job_info['description'];
		$data['dateTime'] = date("m/d/Y");
		$data['query'] = $query;

		if ($mysql_job_id != null) {
			return $this->load->view('extension/module/mysql_jobs_preview', $data);
		} else {
			$this->response->setOutput($this->load->view('extension/module/mysql_jobs_preview', $data));
		}
	}


	public function execute($id = null)
	{
		$this->load->language('extension/module/mysql_jobs');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/module/mysql_jobs');

		if ($id == null) {
			$mysql_job_info = $this->model_extension_module_mysql_jobs->getMySQLJob($this->request->get['id']);
		} else {
			$mysql_job_info = $this->model_extension_module_mysql_jobs->getMySQLJob($id);
		}

		try {
			$mysql_job = $this->model_extension_module_mysql_jobs->execute($mysql_job_info['query']);
		} catch (Exception $e) {
			$this->session->data['success'] = $e->getMessage();
			$this->response->redirect($this->url->link('extension/module/mysql_jobs', 'user_token=' . $this->session->data['user_token'], true));
		}

		/* create email */
		if (utf8_strlen($mysql_job_info['email']) > 0) {
			if (count($mysql_job) > 0) {
				$message  = '<html dir="ltr" lang="en">' . "\n";
				$message .= '  <head>' . "\n";
				$message .= '    <title>' . $this->request->post['subject'] . '</title>' . "\n";
				$message .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
				$message .= '  </head>' . "\n";
				$message .= '  <body>' . html_entity_decode($this->request->post['message'], ENT_QUOTES, 'UTF-8') . '</body>' . "\n";
				$message .= '</html>' . "\n";
				$message .= $this->preview($mysql_job_info['id']);

				$mail = new Mail($this->config->get('config_mail_engine'));
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

				$mail->setTo($mysql_job_info['email']);
				$mail->setFrom('noreply@system.local');
				$mail->setSender($this->config->get('config_name'));
				$mail->setSubject($this->config->get('config_name') . ' | ' . $mysql_job_info['description']);
				$mail->setHtml($message);
				$mail->send();
			}
		}

		if ($id == null) {
			$this->session->data['success'] = $this->language->get('text_exec_success');

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));

			$this->response->redirect($this->url->link('extension/module/mysql_jobs', 'user_token=' . $this->session->data['user_token'], true));
		}
	}
}
