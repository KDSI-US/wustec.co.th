<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleblogsubscribe extends Controller {
	public function index() {
		$this->load->language('extension/blogsubscribe');

		$data['text_subscribe'] = $this->language->get('text_subscribe');
		$data['text_email'] = $this->language->get('text_email');
		
		if (isset($this->request->get['bsearch'])) {
			$data['search'] = $this->request->get['bsearch'];
		} else {
			$data['search'] = '';
		}
		
		return $this->load->view('extension/module/blogsubscribe', $data);
		
	}
	public function addemail(){
		$json=array();
		$this->load->language('extension/blogsubscribe');
		$this->load->model('extension/blogsubscribe');
		$this->load->model('extension/blogsend');
		$this->load->model('tool/image');
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$postemail = $this->request->post;
			if (filter_var($postemail['bsubscribe'], FILTER_VALIDATE_EMAIL)) {
				$subemail = $this->model_extension_blogsubscribe->checkemail($postemail);
				if($subemail){
					$substatus = $this->model_extension_blogsubscribe->substatus($postemail);
					if($substatus){
						$json['error'] = $this->language->get('text_already_sub');
					}else{
						$this->model_extension_blogsubscribe->updatesub($postemail);
						$json['success'] = $this->language->get('text_again_subscibe');
					}
				}else{
					if($this->config->get('module_blogsubscribe_template') && $this->config->get('module_blogsubscribe_status')) {
						$getlatest = $this->model_extension_blogsend->latestpost();
						if(!empty($getlatest)){
						if (is_file(DIR_IMAGE . $getlatest['image'])) {
							$image = $this->model_tool_image->resize($getlatest['image'], 600, 250);
						} else {
							$image = $this->model_tool_image->resize('no_image.png', 40, 40);
						}
						$datas['latespost'] = array(
							'post_id' => $getlatest['post_id'],
							'name' => $getlatest['name'],														'post_type'        	=> $getlatest['post_type'],							'video_url'        	=> $getlatest['video_url'],
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
								'name' => $result['name'],																'post_type'        	=> $result['post_type'],								'video_url'        	=> $result['video_url'],
								'short_description' => html_entity_decode($result['short_description']),
								'image' => $pimage,
							);
						}
						$latest_html = $this->load->view('extension/latestblog_html', $datas);
						$display_html = $this->load->view('extension/displaypost_html', $datas);
						
						$lastemail = $this->model_extension_blogsend->lastemail();
						if(empty($lastemail) || $getlatest['post_id'] > $lastemail['post_id']){
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
									'unsubscribe'	=> '',
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
								$mail->setTo($postemail['bsubscribe']);
								$mail->setFrom($this->config->get('config_email'));
								$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
								$mail->setSubject($subject);
								$mail->setHtml(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
								$mail->send();
								}
					}
					}
				
					$this->model_extension_blogsubscribe->insertsub($postemail);
					$json['success'] = $this->language->get('text_subscribe_email');
				}	
			}else{
				$json['error'] = $this->language->get('text_invalid_email');
			}
		}
		print_r(json_encode($json));
		
	}
	private function validate() {
		if ((utf8_strlen($this->request->post['bsubscribe']) > 96) || !filter_var($this->request->post['bsubscribe'], FILTER_VALIDATE_EMAIL)) {
			$this->error['bsubscribe'] = $this->language->get('error_email');
		}
	}
}