<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionsendblog extends Controller {
	public function index() {
		$this->load->model('extension/blogsend');
		$this->load->model('tool/image');
		if($this->config->get('module_blogsubscribe_template') && $this->config->get('module_blogsubscribe_status')) {
			$getlatest = $this->model_extension_blogsend->latestpost();
			if (is_file(DIR_IMAGE . $getlatest['image'])) {
				$image = $this->model_tool_image->resize($getlatest['image'], 600, 250);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}
			$datas['latespost'] = array(
				'post_id' => $getlatest['post_id'],
				'name' => $getlatest['name'],								'post_type'        	=> $getlatest['post_type'],				'video_url'        	=> $getlatest['video_url'],
				'short_description' => html_entity_decode($getlatest['short_description']),
				'image' => $image,
			);
			
			$displaypost = $this->model_extension_blogsend->latestfivepost();
			$datas['lastrecords'][] = array();
			foreach($displaypost as $result){
				if (is_file(DIR_IMAGE . $result['image'])) {
					$pimage = $this->model_tool_image->resize($result['image'], 600, 250);
				} else {
					$pimage = $this->model_tool_image->resize('no_image.png', 40, 40);
				}
				$datas['lastrecords'][] = array(
					'post_id' => $result['post_id'],
					'name' => $result['name'],										'post_type'        	=> $result['post_type'],					'video_url'        	=> $result['video_url'],
					'short_description' => html_entity_decode($result['short_description']),
					'image' => $pimage,
				);
			}
			$latest_html = $this->load->view('extension/latestblog_html', $datas);
			$display_html = $this->load->view('extension/displaypost_html', $datas);
			
			$lastemail = $this->model_extension_blogsend->lastemail();
			if(empty($lastemail) || $getlatest['post_id'] > $lastemail['post_id']){
				$subscribers = $this->model_extension_blogsend->subscribers();
				$i = 0;
				foreach($subscribers as $subscriber){
					$find = array(
						'[name]',
						'[latest_post]',
						'[display_older]',
						'[unsubscribe]',			
					);
					$replace = array(
						'name'			=> $getlatest['name'],
						'latest_post'	=> $latest_html,
						'display_older' => $display_html,
						'unsubscribe'	=> HTTP_SERVER.'index.php?route=extension/sendblog/unsubscribe&link='.$subscriber['email'],
					);
					
					$subject = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $this->config->get('module_blogsubscribe_subject')))));
					
					$message = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $this->config->get('module_blogsubscribe_template')))));
					
					$mail = new Mail($this->config->get('config_mail_engine'));
					$mail->parameter = $this->config->get('config_mail_parameter');
					$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
					$mail->smtp_username = $this->config->get('config_mail_smtp_username');
					$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
					$mail->smtp_port = $this->config->get('config_mail_smtp_port');
					$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
					$mail->setTo($subscriber['email']);
					$mail->setFrom($this->config->get('config_email'));
					$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
					$mail->setSubject($subject);
					$mail->setHtml(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
					$mail->send();
					
					$i++;
				}
				$this->model_extension_blogsend->sentpost($getlatest['post_id']);
				echo $i.' Email Sent';
			}
		}
	}
	
	public function unsubscribe() {
		$this->load->model('extension/blogsend');
		if(isset($this->request->get['link'])){
			$email = $this->request->get['link'];
			$this->model_extension_blogsend->unsubscribe($email);
			echo html_entity_decode($this->config->get('module_blogsubscribe_unsubscribe'));
		}
	}
}