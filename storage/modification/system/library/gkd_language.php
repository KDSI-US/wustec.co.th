<?php
/* This file is under Git Control by KDSI. */
class GkdLanguage {
	private $default = 'en-gb';
	private $directory;
	private $data = array();
  protected $DIR_LANGUAGE;

	public function __construct($directory = '') {
		$this->directory = $directory;
    $this->DIR_LANGUAGE = defined('DIR_CATALOG') ? DIR_CATALOG . 'language/' : DIR_LANGUAGE;
	}

	public function get($key) {
		return (isset($this->data[$key]) ? $this->data[$key] : $key);
	}
	
	public function set($key, $value) {
		$this->data[$key] = $value;
	}
	
	// Please dont use the below function i'm thinking getting rid of it.
	public function all() {
		return $this->data;
	}
	
	// Please dont use the below function i'm thinking getting rid of it.
	public function merge(&$data) {
		array_merge($this->data, $data);
	}
			
	public function load($filename, &$data = array()) {
		$_ = array();

		$file = $this->DIR_LANGUAGE . 'english/' . $filename . '.php';
		
		// Compatibility code for old extension folders
		$old_file = $this->DIR_LANGUAGE . 'english/' . str_replace('extension/', '', $filename) . '.php';
		
		if (is_file($file)) {
			require($file);
		} elseif (is_file($old_file)) {
			require($old_file);
		}

		$file = $this->DIR_LANGUAGE . $this->default . '/' . $filename . '.php';

		// Compatibility code for old extension folders
		$old_file = $this->DIR_LANGUAGE . $this->default . '/' . str_replace('extension/', '', $filename) . '.php';
		
		if (is_file($file)) {
			require($file);
		} elseif (is_file($old_file)) {
			require($old_file);
		}

		$file = $this->DIR_LANGUAGE . $this->directory . '/' . $filename . '.php';

		// Compatibility code for old extension folders
		$old_file = $this->DIR_LANGUAGE . $this->directory . '/' . str_replace('extension/', '', $filename) . '.php';
		
		if (is_file($file)) {
			require($file);
		} elseif (is_file($old_file)) {
			require($old_file);
		}

		$this->data = array_merge($this->data, $_);

		return $this->data;
	}
}
