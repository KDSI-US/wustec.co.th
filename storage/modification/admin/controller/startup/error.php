<?php
/* This file is under Git Control by KDSI. */
class ControllerStartupError extends Controller {
	public function index() {
		$this->registry->set('log', new Log($this->config->get('config_error_filename') ? $this->config->get('config_error_filename') : $this->config->get('error_filename')));
		
		set_error_handler(array($this, 'handler'));	
register_shutdown_function(array($this, 'logFatalErrors'));
	}
	
public function logFatalErrors() {
				$error = error_get_last();
				if ($error && $error['type'] === E_ERROR) {
					$this->log->write('PHP Fatal Error:  ' . $error['message'] . ' in ' . $error['file'] . ' on line ' . $error['line']);
				}
			}
	public function handler($code, $message, $file, $line) {
		// error suppressed with @
		if (error_reporting() === 0) {
			return false;
		}
	
		switch ($code) {
			case E_NOTICE:
			case E_USER_NOTICE:
				$error = 'Notice';
				break;
			case E_WARNING:
			case E_USER_WARNING:
				$error = 'Warning';
				break;
			case E_ERROR:
			case E_USER_ERROR:
				$error = 'Fatal Error';
				break;
			default:
				$error = 'Unknown';
				break;
		}
	
		if ($this->config->get('config_error_display')) {
			echo '<b>' . $error . '</b>: ' . $message . ' in <b>' . $file . '</b> on line <b>' . $line . '</b>';
		}
	
		if ($this->config->get('config_error_log')) {
			$this->log->write('PHP ' . $error . ':  ' . $message . ' in ' . $file . ' on line ' . $line);
		}
	
		return true;
	} 
} 
