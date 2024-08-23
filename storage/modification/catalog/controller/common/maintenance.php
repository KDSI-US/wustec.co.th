<?php
/* This file is under Git Control by KDSI. */
class ControllerCommonMaintenance extends Controller {
	public function index() {

			/*Ticket system starts*/
		require_once(DIR_SYSTEM.'library/modulepoints/ticketuser.php');
		if(VERSION <= '2.2.0.0') {
			global $registry;
			$ticketuser = new Modulepoints\Ticketuser($registry);			
			$this->registry->set('ticketuser', $ticketuser);
		} else {
			$ticketuser = new Modulepoints\Ticketuser($this->registry);		
			$this->registry->set('ticketuser', $ticketuser);
		}
			/*Ticket system ends*/
			
		$this->load->language('common/maintenance');

		$this->document->setTitle($this->language->get('heading_title'));

		if ($this->request->server['SERVER_PROTOCOL'] == 'HTTP/1.1') {
			$this->response->addHeader('HTTP/1.1 503 Service Unavailable');
		} else {
			$this->response->addHeader('HTTP/1.0 503 Service Unavailable');
		}

		$this->response->addHeader('Retry-After: 3600');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_maintenance'),
			'href' => $this->url->link('common/maintenance')
		);

		$data['message'] = $this->language->get('text_message');

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('common/maintenance', $data));
	}
}
