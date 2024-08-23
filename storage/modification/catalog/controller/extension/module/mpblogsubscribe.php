<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleMpBlogSubscribe extends Controller {
	public function index() {
		if ($this->config->get('mpblog_status')) {
			static $module = 0;

			$this->load->language('extension/module/mpblogsubscribe');

			$this->document->addScript('catalog/view/javascript/mpblog/mpblog.js');

			$data['text_subscribe'] = $this->language->get('text_subscribe');
			$data['text_email'] = $this->language->get('text_email');

			$data['button_subscribe'] = $this->language->get('button_subscribe');
			$data['button_unsubscribe'] = $this->language->get('button_unsubscribe');

			$data['url'] = $this->url->link('extension/mpblog/blogs','',true);
			$data['module'] = $module++;
			return $this->load->view('extension/module/mpblogsubscribe', $data);
		
		}
	}

	public function subscribe() {
		$this->response->addHeader('Content-Type: application/json');
		$json = [];
		if ($this->config->get('mpblog_status')) {
			$this->load->language('extension/module/mpblogsubscribe');
			$this->load->model('extension/mpblog/mpblogpost');

			if ((utf8_strlen($this->request->post['msubscribe']) > 96) || !filter_var($this->request->post['msubscribe'], FILTER_VALIDATE_EMAIL)) {
				$json['error']['email'] = $this->language->get('error_email');
			} else {

				// check email already subscribe-unsubscribe
				$results = $this->model_extension_mpblog_mpblogpost->subscribeEmailExists($this->request->post['msubscribe']);
				if (!empty($results)) {
					$json['error']['exists'] = $this->language->get('error_msubscrieber_exists');
				}
			}


			if (!$json) {

				$json['success'] = $this->language->get('text_msubscrieb_success');

				$mpblogsubscribers_id = $this->model_extension_mpblog_mpblogpost->subscribeMe($this->request->post['msubscribe']);

				if ($this->config->get('mpblog_subscribeapprove') == 'CODE' && $this->config->get('mpblog_subscribeconfirm_status')) {
					// we can't show verificaiton link success message, this will be spam and allow fake email to register.
					$json['success'] .= sprintf($this->language->get('text_msubscrieb_verification'), $this->request->post['msubscribe']);
				}
				if ($this->config->get('mpblog_subscribeapprove') == 'ADMIN' && $this->config->get('mpblog_subscribepending_status')) {
					$json['success'] .= sprintf($this->language->get('text_msubscrieb_adminapproval'), $this->request->post['msubscribe']);
				}
			}
		}
		$this->response->setOutput(json_encode($json));
	}

	public function unSubscribe() {
		$this->response->addHeader('Content-Type: application/json');
		$json = [];
		if ($this->config->get('mpblog_status')) {
			$this->load->language('extension/module/mpblogsubscribe');
			$this->load->model('extension/mpblog/mpblogpost');
			
			if ((utf8_strlen($this->request->post['msubscribe']) > 96) || !filter_var($this->request->post['msubscribe'], FILTER_VALIDATE_EMAIL)) {
				$json['error']['email'] = $this->language->get('error_email');
			} else {

				// check email already subscribe-unsubscribe
				$results = $this->model_extension_mpblog_mpblogpost->subscribeEmailExists($this->request->post['msubscribe']);
				if (empty($results)) {
					$json['error']['nonexists'] = $this->language->get('error_msubscrieber_nonexists');
				}
			}

			if (!$json) {
				$this->model_extension_mpblog_mpblogpost->unSubscribeMe($this->request->post['msubscribe']);

				$json['unsubscribe_popup'] = false;

				if ($this->config->get('mpblog_unsubscribe_status')) {

					$popup_title = !empty($subscribemail['unsubscribe'][$this->config->get('config_language_id')]['title']) ? $subscribemail['unsubscribe'][$this->config->get('config_language_id')]['title'] : '';

					$popup_content = !empty($subscribemail['unsubscribe'][$this->config->get('config_language_id')]['content']) ? $subscribemail['unsubscribe'][$this->config->get('config_language_id')]['content'] : '';

					$find = [
						'[STORE_NAME]',
						'[STORE_LINK]',
						'[LOGO]',
						'[EMAIL]',
					];
				
					$replace = [
						'STORE_NAME'					=> $this->config->get('config_name'),
						'STORE_LINK'					=> $this->url->link('common/home', '', true),
						'LOGO'							=> '<a href="'. $this->url->link('common/home', '', true) .'"><img src="'. $logo .'" alt="'. $this->config->get('config_name') .'" title="'. $this->config->get('config_name') .'" /></a>',
						'EMAIL'							=> $email,
					];
					
					if (!empty($popup_title)) {
						$popup_title = str_replace(["\r\n", "\r", "\n"], '', preg_replace(["/\s\s+/", "/\r\r+/", "/\n\n+/"], '', trim(str_replace($find, $replace, $popup_title))));
					}

					if (!empty($popup_content)) {
						$popup_content = str_replace(["\r\n", "\r", "\n"], '', preg_replace(["/\s\s+/", "/\r\r+/", "/\n\n+/"], '', trim(str_replace($find, $replace, $popup_content))));
					}

					if (!empty($popup_title) && !empty($popup_content)) {
						$json['unsubscribe_popup'] = true;
						$json['popup_title'] = $popup_title;
						$json['popup_content'] = $popup_content;
					}
				}

				$json['success'] = $this->language->get('text_munsubscrieb_success');
			}
		}	
		$this->response->setOutput(json_encode($json));
	}
}