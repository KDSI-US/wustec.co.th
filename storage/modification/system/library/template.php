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
* Template class
*/
class Template {
	private $adaptor;
	
	/**
	 * Constructor
	 *
	 * @param	string	$adaptor
	 *
 	*/
  	public function __construct($adaptor) {
	    $class = 'Template\\' . $adaptor;

		if (class_exists($class)) {
			$this->adaptor = new $class();
		} else {
			throw new \Exception('Error: Could not load template adaptor ' . $adaptor . '!');
		}
	}
	
	/**
	 * 
	 *
	 * @param	string	$key
	 * @param	mixed	$value
 	*/	
	public function set($key, $value) {
		$this->adaptor->set($key, $value);
	}
	
	/**
	 * 
	 *
	 * @param	string	$template
	 * @param	bool	$cache
	 *
	 * @return	string
 	*/	
	public function render($template, $cache = false) {

  		if(VERSION >= '3.0.0.0') {
			if (!file_exists(DIR_TEMPLATE. $template. '.twig') && !file_exists(DIR_TEMPLATE. $template. '.tpl')) {
				if($template) {
					$template_array = explode('/', $template);
					if($template_array)  {
						$template_array[0] = 'default';
					}

					if($template_array) {
						$template = implode('/', $template_array);
					}
				}
			}
		}
 			
		return $this->adaptor->render($template, $cache);
	}
}
