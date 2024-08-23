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
* Mail class
*/
class Mail {
	protected $to;
	protected $from;
	protected $sender;
	protected $reply_to;
	protected $bcc;
	protected $cc;
	protected $mail_queue;
	protected $mail_send;
	protected $reply_to_name;
	protected $subject;
	protected $text;
	protected $html;
	protected $attachments = array();
	public $parameter;

	/**
	 * Constructor
	 *
	 * @param	string	$adaptor
	 *
 	*/
	public function __construct($adaptor = 'mail') {
		$class = 'Mail\\' . $adaptor;
		
		if (class_exists($class)) {
			$this->adaptor = new $class();
		} else {
			trigger_error('Error: Could not load mail adaptor ' . $adaptor . '!');
			exit();
		}	
	}
	
	/**
     * 
     *
     * @param	mixed	$to
     */
	public function setTo($to) {
		$this->to = $to;
	}
	
	/**
     * 
     *
     * @param	string	$from
     */
	public function setFrom($from) {
		$this->from = $from;
	}
	
	/**
     * 
     *
     * @param	string	$sender
     */
	public function setSender($sender) {
		$this->sender = $sender;
	}
	
	/**
     * 
     *
     * @param	string	$reply_to
     */
	public function setReplyTo($reply_to, $reply_to_name = '') {
		if ($reply_to_name) {
			$this->reply_to_name = $reply_to_name;
		}
	
		$this->reply_to = $reply_to;
	}
	
	/**
     * 
     *
     * @param	string	$subject
     */
	public function setSubject($subject) {
		$this->subject = $subject;
	}
	
	/**
     * 
     *
     * @param	string	$text
     */
	public function setText($text) {
		$this->text = $text;
	}
	
	/**
     * 
     *
     * @param	string	$html
     */
	public function setHtml($html) {
		$this->html = $html;
	}
	
	/**
     * 
     *
     * @param	string	$filename
     */
	public function addAttachment($filename) {
		$this->attachments[] = $filename;
	}
	
	/**
     * 
     *
     */
	public function setMailQueue($option) {
		$this->mail_queue = (bool)$option;
	}
	public function setMailSend($option) {
		$this->mail_send = (bool)$option;
	}
	public function getSubject() {
		return $this->subject;
	}

	public function setCc($address) {
		$this->cc = $address;
	}

	public function setBcc($address) {
		$this->bcc = $address;
	}

	public function getMailProperties() {
		return array(
			'mail_queue' => $this->mail_queue,
			'smtp' => array(
				'hostname' => $this->smtp_hostname,
				'username' => $this->smtp_username,
				//'password' => $this->smtp_password,
				'post' => $this->smtp_port,
				'timeout' => $this->smtp_timeout
			),
			//'protocol' => $this->protocol,
			'parameter' => $this->parameter,
			'attachments' => $this->attachments,
			'to' => $this->to,
			'from' => $this->from,
			'sender' => $this->sender,
			'reply_to' => $this->reply_to,
			'reply_to_name' => $this->reply_to_name,
			'cc' => $this->cc,
			'bcc' => $this->bcc,
			'subject' => $this->subject,
			'text' => $this->text
		);
	}
	
	public function send() {
		if ($this->mail_send === false) {
			return true; // unsubscribed
		}

		if ($this->mail_queue) {
			return true; // send later via cron
		}

		if (!$this->to) {
			throw new \Exception('Error: E-Mail to required!');
		}

		if (!$this->from) {
			throw new \Exception('Error: E-Mail from required!');
		}

		if (!$this->sender) {
			throw new \Exception('Error: E-Mail sender required!');
		}

		if (!$this->subject) {
			throw new \Exception('Error: E-Mail subject required!');
		}

		if ((!$this->text) && (!$this->html)) {
			throw new \Exception('Error: E-Mail message required!');
		}
		
		foreach (get_object_vars($this) as $key => $value) {
			$this->adaptor->$key = $value;
		}
		
		$this->adaptor->send();
	}
}