<?php
/* This file is under Git Control by KDSI. */
/**
 * Email Template
 *
 * @author Opencart-Templates
 *
 */
class EmailTemplate {
	private $built = false;
	private $css = null;
	private $customer;
	private $default_shortcodes = null;
	private $html = null;
	private $html_content = null;
	private $model_extension_module_emailtemplate = null;
	private $registry;
	private $text = null;
	private $twig;
	public $data = array();
	public $language_data = array();
	public $shortcodes = null;
	static $content_count = 3;
	static $version = '3.0.5.114';

    /**
	 * EmailTemplate constructor.
	 * @param Registry $registry
	 */
	public function __construct(Registry $registry) {
		$this->registry = $registry;
	}

	/**
	 * Get variable
	 *
	 * @param string $key
	 * @return Ambigous <string, multitype:>
	 */
	public function get($key) {
		return isset($this->data[$key]) ? $this->data[$key] : '';
	}

	/**
	 * Set variable
	 *
	 * @param string $key
	 * @param multitype: $value
	 * @param bool $overwrite
	 */
	public function set($key, $value = '', $overwrite = true) {
		if ($overwrite || (!$overwrite && !isset($this->data[$key]))) {
			$this->data[$key] = $value;
		}
	}

	/**
	 * Get customer info
	 * @return array
	 */
	public function getCustomer() {
		return $this->customer;
	}

	/**
	 * Set customer info
	 * @param array $customer_info
	 * @return array
	 */
	public function setCustomer(array $customer_info) {
		return $this->customer = $customer_info;
	}

	/**
	 * Appends template data
	 *
	 * [code]
	 * $template->addData($my_data_array, 'prefix'); // array(prefix)
	 * $template->addData('my_value', $my_value); // string=key,value
	 *
	 * @return object
	 */
	public function addData($param1, $param2 = '') {
		$protected = array('config', 'emailtemplate');

		if (is_array($param1)) {
			if ($param2) {
                $array = array();

				$prefix = rtrim($param2, "_") . "_";

				foreach ($param1 as $key => $value) {
				    if (substr($key, 0, strlen($param2)) == $param2) {
                        $new_key = $key;
                    } else {
                        $new_key = $prefix.$key;
                    }

					$array[$new_key] = $value;
				}
			} else {
			    $array = $param1;
            }

			foreach($protected as $var) {
				if (isset($array[$var])) {
					unset($array[$var]);
				}
			}

			$this->data = array_merge($this->data, $array);
		} elseif (is_string($param1) && $param2 != "" && !in_array($param1, $protected)) {
			$this->data[$param1] = $param2;
		}

		return $this;
	}

    static function getVersion() {
        return self::$version;
    }

	/**
	 * Build Template - call after load() but before fetch()
	 *
	 * @return boolean|Email_Template
	 */
	public function build() {
		if (empty($this->data['emailtemplate']) || empty($this->data['config'])) {
			trigger_error('Missing emailtemplate');
			exit;
		}

		$this->model_extension_module_emailtemplate->build($this);

		// Shadow
		foreach(array('shadow_top','shadow_bottom','shadow_left','shadow_right') as $var) {
			$cells = "";

			if (!empty($this->data['config'][$var]) && !empty($this->data['config'][$var]['start']) && $this->data['config'][$var]['end'] &&  $this->data['config'][$var]['length'] > 0) {
				$gradient = $this->_generateGradientArray($this->data['config'][$var]['start'], $this->data['config'][$var]['end'], $this->data['config'][$var]['length']);

				foreach($gradient as $hex => $width) {
					switch($var) {
						case 'shadow_top':
						case 'shadow_bottom':
							$cells .= "<tr class='email-shadow'><td bgcolor='#{$hex}' style='background:#{$hex}; height:1px; font-size:1px; line-height:0; mso-margin-top-alt:1px' height='1'> </td></tr>\n";
							break;
						default:
							$cells .= "<td class='email-shadow' bgcolor='#{$hex}' style='background:#{$hex}; width:{$width}px !important; font-size:1px; line-height:0; mso-margin-top-alt:1px' width='{$width}'> </td>\n";
							break;
					}

					$this->data['config'][$var]['bg'] = $cells;
				}
			}
		}

		$this->data['view_browser'] = '';

		if (!empty($this->data['emailtemplate_log_id'])) {
			if (empty($this->data['emailtemplate_log_enc'])) {
				$this->data['emailtemplate_log_enc'] = substr(md5(uniqid(rand(), true)), 0, 32);
			}

			if ($this->data['config']['log_read']) {
				$this->data['emailtemplate']['tracking_img'] = $this->data['store_url'] . 'index.php?route=extension/module/emailtemplate/record&id='. $this->data['emailtemplate_log_id'] . '&enc=' . $this->data['emailtemplate_log_enc'];
			}

			if ($this->data['config']['view_browser']) {
				$this->data['view_browser_url'] = $this->data['store_url'] . 'index.php?route=extension/module/emailtemplate/view&id=' . $this->data['emailtemplate_log_id'] . '&enc=' . $this->data['emailtemplate_log_enc'];

				if (!empty($this->data['text_view_browser'])) {
					$this->data['view_browser'] = sprintf($this->data['text_view_browser'], $this->data['view_browser_url']);
				}
			}
		}

		$this->data['preference_text'] = '';

		if (!isset($this->data['parse_shortcodes']) || $this->data['parse_shortcodes']) {
			if ($this->language_data) {
				$this->data = array_merge($this->replaceContent($this->language_data), $this->data);
			}

			$content_count = 3;

			for ($i=1; $i <= $content_count; $i++) {
				if (!empty($this->data['emailtemplate']['content' . $i])) {
					$this->data['emailtemplate']['content' . $i] = $this->renderContent(html_entity_decode($this->data['emailtemplate']['content' . $i], ENT_QUOTES, 'UTF-8'));
				}
			}

			if (!empty($this->data['config']['preference_text'])) {
				$this->data['preference_text'] = $this->renderContent(html_entity_decode($this->data['config']['preference_text'], ENT_QUOTES, 'UTF-8'));
			}

			foreach (array('heading', 'preheader_preview', 'subject', 'mail_to', 'mail_cc', 'mail_bcc', 'mail_from', 'mail_sender', 'mail_replyto', 'mail_replyto_name') as $var) {
				if (!empty($this->data['emailtemplate'][$var])) {
					$this->data['emailtemplate'][$var] = $this->replaceContent(html_entity_decode($this->data['emailtemplate'][$var], ENT_QUOTES, 'UTF-8'));
				}
			}

			foreach (array('head_text', 'header_html', 'page_footer_text', 'footer_text') as $var) {
				if (!isset($this->data['config'][$var])) {
					$this->data['config'][$var] = '';
				} else {
					$this->data['config'][$var] = $this->renderContent(html_entity_decode($this->data['config'][$var], ENT_QUOTES, 'UTF-8'));
				}

				if ($this->data['config'][$var] && preg_replace('/\s+/', '', strip_tags(html_entity_decode($this->data['config'][$var], ENT_QUOTES, 'UTF-8'))) == '') {
					$this->data['config'][$var] = '';
				}
			}

			if (!empty($this->data['emailtemplate']['comment'])) {
				$this->data['emailtemplate']['comment'] = $this->renderContent(html_entity_decode($this->data['emailtemplate']['comment'], ENT_QUOTES, 'UTF-8'));
			}
		}

		$this->built = true;

		return $this;
	}

    /**
     * Check required conditions called after adding template datas
     *
     * @return bool
     */
	public function check() {
        $condition = $this->model_extension_module_emailtemplate->checkTemplateCondition(0, $this->data['emailtemplate']['condition'], $this->data);

        if ($condition === false) {
            return false;
        }

        return true;
    }

	/**
	 * Fetch HTML Email
	 * @param string $filename
	 * @param string $content - if $filename is null then the content will be used as the body
	 */
	public function fetch($filename = null, $content = null) {
		if (!isset($this->data['emailtemplate'])) return false;

		if (!$this->built) {
			$this->build();
		}

		$this->html_content = '';

		if ($content) {
			$this->html_content = $this->renderContent(html_entity_decode($content, ENT_QUOTES, 'UTF-8'));
		} elseif ($filename) {
			$this->html_content = $this->renderTemplate($filename);
		} elseif ($this->data['emailtemplate']['template']) {
			$this->html_content = $this->renderTemplate($this->data['emailtemplate']['template']);
		}

		if (!$this->html_content) {
			for ($i=1; $i <= self::$content_count; $i++) {
				if (!empty($this->data['emailtemplate']['content' . $i])) {
					$this->html_content .= $this->data['emailtemplate']['content' . $i];
				}
			}
			$this->html_content = $this->renderContent($this->html_content);
		}

		$this->html = '';

		if ($this->shortcodes) {
			$this->html_content = str_replace($this->shortcodes['find'], $this->shortcodes['replace'], $this->html_content);
		}

		if (!empty($this->data['wrapper_tpl'])){
			$wrapper_file = $this->data['wrapper_tpl'];
		} else {
			$wrapper_file = '_main.twig';
		}

		if ($wrapper_file) {
			$this->html = str_replace('{CONTENT}', $this->html_content, $this->renderTemplate($wrapper_file));
		} else {
			$this->html = $this->html_content;
		}

        $this->html = preg_replace('/^ +/m', '', $this->htmlInlineCss());

		// Remove excess space between tags
        $this->html = preg_replace('/\>\s+\</m', '> <', $this->html);

        $array = explode("\n", $this->html);
        $this->html = "";
        foreach($array as $key => $val) {
            $this->html .= wordwrap($val, 420, "\n", true);
            $this->html .= "\n";
        }

		return $this->html;
	}

	/**
	 * Apply email template settings to Mail object
	 *
	 * @param object - Mail
	 * @return object
	 */
	public function hook(Mail &$mail) {
		if (!isset($this->data['emailtemplate'])) return $mail;

        $this->data['mail'] = $mail->getMailProperties();

		if (!$this->built) {
			$this->build();
		}

		if (is_null($this->html)) {
			$this->html = $this->fetch();
		}

		if ($this->html) {
			if ($this->data['emailtemplate']['mail_html']){
				$mail->setHtml($this->html);
			}

			if ($this->data['emailtemplate']['mail_plain_text']) {
				$mail->setText($this->getPlainText());
			}
		}

		if (!empty($this->data['emailtemplate']['subject'])) {
			$mail->setSubject($this->data['emailtemplate']['subject']);
		}

		if ($this->data['emailtemplate']['mail_to']) {
			$mail->setTo($this->data['emailtemplate']['mail_to']);
		}

        if ($this->data['emailtemplate']['mail_cc']) {
            $mail_cc = $this->data['emailtemplate']['mail_cc'];
            if (strpos($mail_cc, ',') !== false) {
                $mail_cc = preg_split('/, ?/', $mail_cc);
            }
            $mail->setCc($mail_cc);
        }

        if ($this->data['emailtemplate']['mail_bcc']) {
            $mail_bcc = $this->data['emailtemplate']['mail_bcc'];
            if (strpos($mail_bcc, ',') !== false) {
                $mail_bcc = preg_split('/, ?/', $mail_bcc);
            }
            $mail->setBcc($mail_bcc);
        }

		if ($this->data['emailtemplate']['mail_from']) {
			$mail->setFrom($this->data['emailtemplate']['mail_from']);
		}

		if ($this->data['emailtemplate']['mail_sender']) {
			$mail->setSender($this->data['emailtemplate']['mail_sender']);
		}

		if ($this->data['emailtemplate']['mail_replyto'] && $this->data['emailtemplate']['mail_replyto'] != $this->data['emailtemplate']['mail_to']) {
			$mail->setReplyTo($this->data['emailtemplate']['mail_replyto'], $this->data['emailtemplate']['mail_replyto_name']);
		}

		if ($this->data['emailtemplate']['mail_attachment']) {
			$attachments = explode(',', $this->data['emailtemplate']['mail_attachment']);
			$dir = substr(DIR_SYSTEM, 0, -7); // remove 'system/'

			foreach($attachments as $attachment){
				$mail->addAttachment($dir . $attachment);
			}
		}

		// Daily send limit per template
        if (!empty($this->data['emailtemplate']['send_max'])) {
            $total_template_logs = $this->model_extension_module_emailtemplate->getTotalTemplateLogs(array(
                'emailtemplate_key' => $this->data['emailtemplate']['key'],
                'email_to' => $this->data['mail']['to'],
                'days_ago' => 1
            ));

            if ($total_template_logs >= $this->data['emailtemplate']['send_max']) {
                $mail->setMailSend(false);
                $mail->setMailQueue(false);
                $this->data['send_mail'] = false;
            }
        }

        if (method_exists($mail, 'setMailQueue')) {
            if ($this->data['emailtemplate']['mail_queue']) {
                $mail->setMailQueue(true);
                $this->data['mail_queue'] = true;

                if (!is_dir(DIR_CACHE . '/mail_queue/')) {
                    mkdir(DIR_CACHE . '/mail_queue/', 0755, true);
                }

                file_put_contents(DIR_CACHE . '/mail_queue/' . $this->data['emailtemplate_log_enc'], $this->html);
            } else {
                $mail->setMailQueue(false);
                $this->data['mail_queue'] = false;
            }

			if (!empty($this->data['emailtemplate']['preference'])) {
				$customer_info = $this->getCustomer();

				if ($customer_info) {
					if ($this->data['emailtemplate']['preference'] == 'notification' && (isset($customer_info['newsletter_preference']['notification']) && !$customer_info['newsletter_preference']['notification'])) {
						$mail->setMailSend(false);
						$mail->setMailQueue(false);
						$this->data['send_mail'] = false;
					} elseif ($this->data['emailtemplate']['preference'] == 'newsletter' && (isset($customer_info['newsletter_preference']['newsletter']) && !$customer_info['newsletter'])) {
						$mail->setMailSend(false);
						$mail->setMailQueue(false);
						$this->data['send_mail'] = false;
					}
				}
			}
        }

		return $mail;
	}

	/**
	 * Get Plain Text - strip html
	 */
	public function getPlainText() {
		if ($this->html === null) {
			$this->fetch();
		}

		if ($this->html_content && $this->text === null) {
			$html2text = new EmailTemplateHtml2Text();
			$this->text = $html2text::convert($this->html_content);
		}

		return $this->text;
	}

	/**
	 * Get HTML email template
	 */
	public function getHtml() {
		if ($this->html === null) {
			$this->fetch();
		}

		return $this->html;
	}

	/**
	 * Get HTML inner content
	 */
	public function getHtmlContent() {
		if ($this->html_content === null) {
			$this->fetch();
		}

		return $this->html_content;
	}

	public function setDatabaseModel(Model $model) {
		$this->model_extension_module_emailtemplate = $model;
	}

	public function getShortcodes() {
		$shortcodes = array('find' => array(), 'replace' => array());

		foreach($this->data as $key => $var) {
			if (is_array($var)) {
				foreach($var as $key2 => $var2) {
					if((is_string($var2) || is_int($var2)) && !isset($shortcodes['find'][$key . '_' . $key2])) {
						$shortcodes['find'][$key . '_' . $key2] = '{{ '.$key.'.'.$key2.' }}';
						$shortcodes['replace'][] = $var2;
					}
				}
			} elseif((is_string($var) || is_int($var)) && !isset($shortcodes['find'][$key])) {
				$shortcodes['find'][$key] = '{{ '.$key.' }}';
				$shortcodes['replace'][] = $var;
			}
		}

		return $shortcodes;
	}

	private function htmlInlineCss(){
		if (!$this->css) {
			$mail_css_dir = DIR_CACHE . '/mail_css/';

			if (file_exists($mail_css_dir . $this->data['config']['emailtemplate_config_id'])) {
				$this->css = file_get_contents($mail_css_dir . $this->data['config']['emailtemplate_config_id']);
			} else {
				ob_start();

				extract(array(
					'config' => $this->data['config'],
					'direction' => $this->data['direction'],
					'lang' => $this->data['lang'],
				));

				include(modification(DIR_SYSTEM . 'library/emailtemplate/email_template.css.php'));

				$this->css = ob_get_contents();

				if (ob_get_length()) {
					ob_end_clean();
				}

				if (!is_dir($mail_css_dir)) {
					mkdir($mail_css_dir, 0755, true);
				}

				file_put_contents($mail_css_dir . $this->data['config']['emailtemplate_config_id'], $this->css);
			}
		}

		if ($this->css && $this->html) {
			require_once(DIR_SYSTEM . 'library/emailtemplate/emailtemplate_csstoinlinestyles.php');

			$cssToInlineStyles = new EmailTemplate_CssToInlineStyles();

			// Check if old version 1
			if (method_exists($cssToInlineStyles, 'setHTML')) {
				$cssToInlineStyles->setCSS($this->css);
				$cssToInlineStyles->setHTML($this->html);
				return $cssToInlineStyles->convert();
			} else {
				return $cssToInlineStyles->convert($this->html, $this->css);
			}
		}
	}

	public function getTemplatePath($file, $absolute = false) {
		if (defined('DIR_CATALOG')) {
			if (file_exists(DIR_TEMPLATE . 'extension/module/emailtemplate/mail/' . $file)) {
				$path = DIR_TEMPLATE . 'extension/module/emailtemplate/mail/';
				$local = 'extension/module/emailtemplate/mail/';
			} elseif (!empty($this->data['store_theme']) && file_exists(DIR_CATALOG . 'view/theme/' . $this->data['store_theme'] . '/template/extension/module/emailtemplate/' . $file)) {
				$path = DIR_CATALOG . 'view/theme/' . $this->data['store_theme'] . '/template/extension/module/emailtemplate/';
				$local = $this->data['store_theme'] . '/template/extension/module/emailtemplate/';
			} else {
				$path = DIR_CATALOG . 'view/theme/default/template/extension/module/emailtemplate/';
				$local = 'default/template/extension/module/emailtemplate/';
			}
		} else {
			if (isset($this->data['store_theme']) && file_exists(DIR_TEMPLATE . $this->data['store_theme'] . '/template/extension/module/emailtemplate/' . $file)) {
				$path = DIR_TEMPLATE . $this->data['store_theme'] . '/template/extension/module/emailtemplate/';
				$local = $this->data['store_theme'] . '/template/extension/module/emailtemplate/';
			} else {
				$path = DIR_TEMPLATE . 'default/template/extension/module/emailtemplate/';
				$local = 'default/template/extension/module/emailtemplate/';
			}
		}

		if (substr($file, -4) == '.tpl') {
			$ext = 'tpl';
			$file = substr($file, 0, -4);
		} elseif (substr($file, -5) == '.twig') {
			$ext = 'twig';
			$file = substr($file, 0, -5);
		} else {
			$ext = 'twig';
		}

		// RTL
		if ($this->data['direction'] == 'rtl' && file_exists($path . $file . '_rtl.' . $ext)) {
			$file .= '_rtl';
		}

		return ($absolute ? $path : $local) . $file . '.' . $ext;
	}

	private function renderTemplate($file) {
		$file_path = $this->getTemplatePath($file, true);

		if (!file_exists($file_path)) {
			trigger_error("Unable to find template file: " . $file_path);
			return false;
		}

		return $this->renderContent(file_get_contents(modification($file_path)), modification($file_path));
	}

	/**
	 * Replace content
	 *
	 * @param $content mixed
	 * @return array
	 */
	public function replaceContent ($content) {
		if ($content && is_string($content)){
			$this->shortcodes = $this->getShortcodes();

			$content = preg_replace('/\{\{([a-zA-Z0-9_ ]*?)\}\}/', '', str_replace($this->shortcodes['find'], $this->shortcodes['replace'], $content));
		}

		return $content;
	}

	/**
	 * Creates a template from string content
	 *
	 * @param $content
	 * @return string
	 */
    public function renderContent($content, $label = '') {
    	if ($content) {
			$this->twig = $this->getTemplateEngine();

			try {
				$template = $this->twig->createTemplate($content);

				return $template->render($this->data);
			} catch (Exception $e) {
				trigger_error("Unable to render " . $label . " (" . $this->data['emailtemplate']['key'] . ") " . $e->getMessage());
			}
		}
    }

    /**
     * @return \Twig\Environment|Twig_Environment
     * @throws Twig_Error_Loader
     * @throws \Twig\Error\LoaderError
     */
    public function getTemplateEngine() {
        if ($this->twig instanceof Twig_Environment || $this->twig instanceof \Twig\Environment) {
            return $this->twig;
        }

        $config = array(
            'autoescape' => false,
            'cache' => false
        );

        // Use namespaced latest twig e.g used in Journal theme
        if (class_exists('\Twig\Environment')) {
            $loader = new \Twig\Loader\FilesystemLoader(DIR_TEMPLATE);

            if (defined('DIR_CATALOG')) {
                if (is_dir(DIR_MODIFICATION . 'catalog/view/theme/')) {
                    $loader->addPath(DIR_MODIFICATION . 'catalog/view/theme/');
                }
                $loader->addPath(DIR_CATALOG . 'view/theme/');
            }

            $this->twig = new \Twig\Environment($loader, $config);
        } else {
            // include and register Twig auto-loader
            include_once(DIR_SYSTEM . 'library/template/Twig/Autoloader.php');

            \Twig_Autoloader::register();

            $loader = new \Twig_Loader_Filesystem(DIR_TEMPLATE);

            if (defined('DIR_CATALOG')) {
                if (is_dir(DIR_MODIFICATION . 'catalog/view/theme/')) {
                    $loader->addPath(DIR_MODIFICATION . 'catalog/view/theme/');
                }
                $loader->addPath(DIR_CATALOG . 'view/theme/');
            }

            $this->twig = new \Twig_Environment($loader, $config);
        }

        return $this->twig;
    }

	/**
	 * Generate array of hex values for shadow
	 * @param $from - HEX colour from
	 * @param $until - HEX colour from
	 * @param $length - distance of shadow
	 * @return Array(hex=>width)
	 */
	private function _generateGradientArray($from, $until, $length) {
		$from = ltrim($from,'#');
		$until = ltrim($until,'#');
		$from = array(hexdec(substr($from,0,2)),hexdec(substr($from,2,2)),hexdec(substr($from,4,2)));
		$until = array(hexdec(substr($until,0,2)),hexdec(substr($until,2,2)),hexdec(substr($until,4,2)));

		if ($length > 1) {
			$red=($until[0]-$from[0])/($length-1);
			$green=($until[1]-$from[1])/($length-1);
			$blue=($until[2]-$from[2])/($length-1);
			$return = array();

			for($i=0;$i<$length;$i++) {
				$newred=dechex($from[0]+round($i*$red));
				if (strlen($newred)<2) $newred="0".$newred;

				$newgreen=dechex($from[1]+round($i*$green));
				if (strlen($newgreen)<2) $newgreen="0".$newgreen;

				$newblue=dechex($from[2]+round($i*$blue));
				if (strlen($newblue)<2) $newblue="0".$newblue;

				$hex = $newred.$newgreen.$newblue;
				if (isset($return[$hex])) {
					$return[$hex] ++;
				} else {
					$return[$hex] = 1;
				}
			}
			return $return;
		} else {
			$red=($until[0]-$from[0]);
			$green=($until[1]-$from[1]);
			$blue=($until[2]-$from[2]);

			$newred=dechex($from[0]+round($red));
			if (strlen($newred)<2) $newred="0".$newred;

			$newgreen=dechex($from[1]+round($green));
			if (strlen($newgreen)<2) $newgreen="0".$newgreen;

			$newblue=dechex($from[2]+round($blue));
			if (strlen($newblue)<2) $newblue="0".$newblue;

			return array($newred.$newgreen.$newblue => $length);
		}

	}
}

/******************************************************************************
 * Copyright (c) 2010 Jevon Wright and others.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * or
 *
 * LGPL which is available at http://www.gnu.org/licenses/lgpl.html
 *
 *
 * Contributors:
 *    Jevon Wright - initial API and implementation
 ****************************************************************************/
class EmailTemplateHtml2Text {

	/**
	 * Tries to convert the given HTML into a plain text format - best suited for
	 * e-mail display, etc.
	 *
	 * <p>In particular, it tries to maintain the following features:
	 * <ul>
	 *   <li>Links are maintained, with the 'href' copied over
	 *   <li>Information in the &lt;head&gt; is lost
	 * </ul>
	 *
	 * @param string html the input HTML
	 * @return string the HTML converted, as best as possible, to text
	 * @throws Html2TextException if the HTML could not be loaded as a {@link DOMDocument}
	 */
	static function convert($html) {
		// replace &nbsp; with spaces
		$html = str_replace("&nbsp;", " ", $html);
		$html = str_replace("\xc2\xa0", " ", $html);

		$html = static::fixNewlines($html);

		$doc = new \DOMDocument();
		$internalErrors = libxml_use_internal_errors(true);
		if (!$doc->loadHTML($html)) {
			return false;
		}
		libxml_use_internal_errors($internalErrors);

		$output = static::iterateOverNode($doc);

		// remove leading and trailing spaces on each line
		$output = preg_replace("/[ \t]*\n[ \t]*/im", "\n", $output);
		$output = preg_replace("/ *\t */im", "\t", $output);

		// remove unnecessary empty lines
		$output = preg_replace("/\n\n\n*/im", "\n\n", $output);

		// remove leading and trailing whitespace
		$output = trim($output);

		return $output;
	}

	/**
	 * Unify newlines; in particular, \r\n becomes \n, and
	 * then \r becomes \n. This means that all newlines (Unix, Windows, Mac)
	 * all become \ns.
	 *
	 * @param string text text with any number of \r, \r\n and \n combinations
	 * @return string the fixed text
	 */
	static function fixNewlines($text) {
		// replace \r\n to \n
		$text = str_replace("\r\n", "\n", $text);
		// remove \rs
		$text = str_replace("\r", "\n", $text);

		return $text;
	}

	static function nextChildName($node) {
		// get the next child
		$nextNode = $node->nextSibling;
		while ($nextNode != null) {
			if ($nextNode instanceof \DOMElement) {
				break;
			}
			$nextNode = $nextNode->nextSibling;
		}
		$nextName = null;
		if ($nextNode instanceof \DOMElement && $nextNode != null) {
			$nextName = strtolower($nextNode->nodeName);
		}

		return $nextName;
	}

	static function prevChildName($node) {
		// get the previous child
		$nextNode = $node->previousSibling;
		while ($nextNode != null) {
			if ($nextNode instanceof \DOMElement) {
				break;
			}
			$nextNode = $nextNode->previousSibling;
		}
		$nextName = null;
		if ($nextNode instanceof \DOMElement && $nextNode != null) {
			$nextName = strtolower($nextNode->nodeName);
		}

		return $nextName;
	}

	static function iterateOverNode($node) {
		if ($node instanceof \DOMText) {
			// Replace whitespace characters with a space (equivilant to \s)
			return preg_replace("/[\\t\\n\\f\\r ]+/im", " ", $node->wholeText);
		}
		if ($node instanceof \DOMDocumentType) {
			// ignore
			return "";
		}

		$nextName = static::nextChildName($node);
		$prevName = static::prevChildName($node);

		$name = strtolower($node->nodeName);

		// start whitespace
		switch ($name) {
			case "hr":
				return "---------------------------------------------------------------\n";

			case "style":
			case "head":
			case "title":
			case "meta":
			case "script":
				// ignore these tags
				return "";

			case "h1":
			case "h2":
			case "h3":
			case "h4":
			case "h5":
			case "h6":
			case "ol":
			case "ul":
				// add two newlines, second line is added below
				$output = "\n";
				break;

			case "td":
			case "th":
				// add tab char to separate table fields
				$output = "\t";
				break;

			case "tr":
			case "p":
			case "div":
				// add one line
				$output = "\n";
				break;

			case "li":
				$output = "- ";
				break;

			default:
				// print out contents of unknown tags
				$output = "";
				break;
		}

		// debug
		//$output .= "[$name,$nextName]";

		if (isset($node->childNodes)) {
			for ($i = 0; $i < $node->childNodes->length; $i++) {
				$n = $node->childNodes->item($i);

				$text = static::iterateOverNode($n);

				$output .= $text;
			}
		}

		// end whitespace
		switch ($name) {
			case "style":
			case "head":
			case "title":
			case "meta":
			case "script":
				// ignore these tags
				return "";

			case "h1":
			case "h2":
			case "h3":
			case "h4":
			case "h5":
			case "h6":
				$output .= "\n";
				break;

			case "p":
			case "br":
				// add one line
				if ($nextName != "div")
					$output .= "\n";
				break;

			case "div":
				// add one line only if the next child isn't a div
				if (($nextName != "div" && $nextName != null) || ($node->hasAttribute('class') && strstr($node->getAttribute('class'), 'emailtemplateSpacing')))
					$output .= "\n";
				break;

			case "td":
				// add one line only if the next child isn't a div
				if ($node->hasAttribute('class') && strstr($node->getAttribute('class'), 'emailtemplateSpacing'))
					$output .= "\n\n";
				break;

			case "a":
				// links are returned in [text](link) format
				$href = $node->getAttribute("href");

				$output = trim($output);

				// remove double [[ ]] s from linking images
				if (substr($output, 0, 1) == "[" && substr($output, -1) == "]") {
					$output = substr($output, 1, strlen($output) - 2);

					// for linking images, the title of the <a> overrides the title of the <img>
					if ($node->getAttribute("title")) {
						$output = $node->getAttribute("title");
					}
				}

				// if there is no link text, but a title attr
				if (!$output && $node->getAttribute("title")) {
					$output = $node->getAttribute("title");
				}

				if ($href == null) {
					// it doesn't link anywhere
					if ($node->getAttribute("name") != null) {
						$output = "[$output]";
					}
				} else {
					if ($href == $output || $href == "mailto:$output" || $href == "http://$output" || $href == "https://$output") {
						// link to the same address: just use link
						$output;
					} else {
						// replace it
						if ($output) {
							$output = "[$output]($href)";
						} else {
							// empty string
							$output = $href;
						}
					}
				}

				// does the next node require additional whitespace?
				switch ($nextName) {
					case "h1": case "h2": case "h3": case "h4": case "h5": case "h6":
					$output .= "\n";
					break;
				}
				break;

			case "img":
				if ($node->getAttribute("title")) {
					$output = "[" . $node->getAttribute("title") . "]";
				} elseif ($node->getAttribute("alt")) {
					$output = "[" . $node->getAttribute("alt") . "]";
				} else {
					$output = "";
				}
				break;

			case "li":
				$output .= "\n";
				break;

			default:
				// do nothing
		}

		return $output;
	}

}
