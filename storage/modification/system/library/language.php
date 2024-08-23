<?php
/* This file is under Git Control by KDSI. */
/**
 * @package		OpenCart
 * @author		Daniel Kerr
 * @copyright	Copyright (c) 2005 - 2017, OpenCart, Ltd. (https://www.opencart.com/)
 * @license		https://opensource.org/licenses/GPL-3.0
 * @link		https://www.opencart.com
*/

/**
* Language class
*/
class Language {
	private $default = 'en-gb';
	private $directory;
private $path = DIR_LANGUAGE;
	public $data = array();
	
	/**
	 * Constructor
	 *
	 * @param	string	$file
	 *
 	*/
	public function __construct($directory = '') {
		$this->directory = $directory;
	}
	
	/**
     * 
     *
     * @param	string	$key
	 * 
	 * @return	string
     */

			/**** MP checkout Starts ****/
			public function setmpcheckoutlanguage($key, $value) {
				$this->data[$key] = $value;
			}
			/**** MP checkout Ends ****/
			
	public function setPath($path) {
		if (!is_dir($path)) {
			trigger_error('Error: check path exists: '.$path);
			exit;
		}

		$this->path = $path;
	}
	
	public function get($key) {
		return (isset($this->data[$key]) ? $this->data[$key] : $key);
	}
	
	public function set($key, $value) {
		$this->data[$key] = $value;
	}
	
	/**
     * 
     *
	 * @return	array
     */	
	public function all() {
		return $this->data;
	}
	
	/**
     * 
     *
     * @param	string	$filename
	 * @param	string	$key
	 * 
	 * @return	array
     */	
	public function load($filename, $key = '') {
		if (!$key) {
			$_ = array();
	
			$file = $this->path . $this->default . '/' . $filename . '.php';
	
			if (is_file($file)) {
				require(modification($file));
			}
	
			$file = $this->path . $this->directory . '/' . $filename . '.php';
			
			if (is_file($file)) {
				require(modification($file));
			} 
	
			$this->data = array_merge($this->data, $_);
		} else {
			// Put the language into a sub key
			$this->data[$key] = new Language($this->directory);
			$this->data[$key]->load($filename);
		}
		
		return $this->data;
	}
}