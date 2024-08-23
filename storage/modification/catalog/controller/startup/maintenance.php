<?php
/* This file is under Git Control by KDSI. */
class ControllerStartupMaintenance extends Controller {
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
			
		if ($this->config->get('config_maintenance')) {
			// Route
			if (isset($this->request->get['route']) && $this->request->get['route'] != 'startup/router') {
				$route = $this->request->get['route'];
			} else {
				$route = $this->config->get('action_default');
			}			
			
			$ignore = array(
				'common/language/language',
				'common/currency/currency'
			);
			
			// Show site if logged in as admin
			$this->user = new Cart\User($this->registry);

			if ((substr($route, 0, 17) != 'extension/payment' && substr($route, 0, 3) != 'api') && !in_array($route, $ignore) && !$this->user->isLogged()) {
				return new Action('common/maintenance');
			}
		}
	}
}
