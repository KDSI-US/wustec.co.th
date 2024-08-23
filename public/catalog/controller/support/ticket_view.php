<?php
class ControllerSupportTicketView extends Controller {
	private $error = array();

	public function index() {
		if (isset($this->request->get['ticketrequest_id'])) {
			$ticketrequest_id = $this->request->get['ticketrequest_id'];
		} else {
			$ticketrequest_id = 0;
		}

		if (!$this->ticketuser->isLogged()) {
			$this->session->data['support_redirect'] = $this->url->link('support/ticket_view', 'ticketrequest_id=' . $ticketrequest_id, true);

			$this->response->redirect($this->url->link('support/support', 'login=1', true));
		}

		$this->document->addScript('catalog/view/javascript/modulepoints/ticketsystem.js');
		$this->document->addStyle('catalog/view/theme/default/stylesheet/modulepoints/ticketsystem.css');

		if($this->config->get('ticketsetting_headerfooter')) {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/modulepoints/moduleheader.css');
		}

		$this->load->language('support/ticket_view');

		$this->load->model('module_ticket/support');

		$this->load->model('module_ticket/ticketuser');

		$this->load->model('module_ticket/ticketrequest');

		$this->load->model('tool/upload');

		$this->load->model('tool/image');

		$data['heading_title'] = $this->language->get('heading_title');
		$data['banner_title'] = sprintf($this->language->get('banner_title'), $ticketrequest_id);

		$data['userphoto_display'] = $this->config->get('ticketsetting_userphoto_display');
		$data['staffphoto_display'] = $this->config->get('ticketsetting_staffphoto_display');

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('ticketsetting_banner'))) {
			$data['support_banner'] = $server . 'image/' . $this->config->get('ticketsetting_banner');
		} else {
			$data['support_banner'] = '';
		}



		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';
		if (isset($this->request->get['page'])) {
			$url .= '&page='. $this->request->get['page'];
		}

		$mytheme = null;
		if($this->config->get('config_theme')) {
			$mytheme = $this->config->get('config_theme');
		} else if($this->config->get('theme_default_directory')) {
			$mytheme = $this->config->get('theme_default_directory');
		} else if($this->config->get('config_template')) {
			$mytheme = $this->config->get('config_template');
		} else{
			$mytheme = 'default';
		}

		if($mytheme == '') {
			$mytheme = 'default';
		}

		$ticketrequest_info = $this->model_module_ticket_ticketrequest->getTicketRequest($ticketrequest_id);

		if ($ticketrequest_info) {
			$this->document->setTitle($this->language->get('heading_title'));

			$data['column_department'] = $this->language->get('column_department');
			$data['column_date'] = $this->language->get('column_date');
			$data['column_status'] = $this->language->get('column_status');

			$data['text_client'] = $this->language->get('text_client');
			$data['text_loading'] = $this->language->get('text_loading');

			$data['entry_attachments'] = $this->language->get('entry_attachments');
			$data['entry_message'] = $this->language->get('entry_message');

			$data['button_add_file'] = $this->language->get('button_add_file');
			$data['button_submit'] = $this->language->get('button_submit');
			$data['button_submit_close'] = $this->language->get('button_submit_close');
			$data['button_cancel'] = $this->language->get('button_cancel');
			$data['button_request_list'] = $this->language->get('button_request_list');

			$data['request_list'] = $this->url->link('support/request_list', '', true);

			$data['reply_action'] = str_replace('&amp;', '&', $this->url->link('support/ticket_view/reply', 'ticketrequest_id='. $ticketrequest_id . $url, true));
			$data['ticketrequest_id'] = $ticketrequest_info['ticketrequest_id'];

			if($ticketrequest_info['ticketstatus_id'] == $this->config->get('ticketsetting_ticketstatus_closed_id')) {
				$data['ticket_close'] = true;
			} else {
				$data['ticket_close'] = false;
			}


			$data['department_name'] = $ticketrequest_info['department_name'];
			$data['ticketuser_name'] = $ticketrequest_info['ticketuser_name'];
			$data['ticketuser_email'] = $ticketrequest_info['ticketuser_email'];
			$data['email'] = $ticketrequest_info['email'];
			$data['status'] = $ticketrequest_info['status'];
			$data['bgcolor'] = $ticketrequest_info['bgcolor'];
			$data['textcolor'] = $ticketrequest_info['textcolor'];
			$data['subject'] = $ticketrequest_info['subject'];
			$data['message'] = nl2br($ticketrequest_info['message']);
			$data['date_added'] = date($this->language->get('date_format_short'), strtotime($ticketrequest_info['date_added']));
			$attachments = (array)$ticketrequest_info['attachments'];

			$data['attachments'] = array();
			foreach($attachments as $attachment) {
				$upload_info = $this->model_tool_upload->getUploadByCode($attachment);
				if ($upload_info) {
					$data['attachments'][] = array(
						'name'		=> $upload_info['name'],
						'href'  	=> $this->url->link('support/ticket_view/download', 'code=' . $upload_info['code'], true)
					);
				}
			}

			// Chats
			$chats_total = $this->model_module_ticket_ticketrequest->getTotalTicketRequestChats($ticketrequest_id);

			$chats = $this->model_module_ticket_ticketrequest->getTicketRequestChats($ticketrequest_id, ($page - 1) * $this->config->get('ticketsetting_reply_list_limit'), $this->config->get('ticketsetting_reply_list_limit'));
			$data['chats'] = array();
			foreach ($chats as $chat) {
				if($chat['client_type'] == 'staff') {

					$user_info = $this->model_module_ticket_ticketuser->getAdminUser($chat['message_from_user_id']);
					if($user_info) {
						$client_name  = $user_info['firstname'] .' '. $user_info['lastname'];
					} else {
						$client_name  = $this->language->get('text_administrator');
					}

					$client_type_name = $this->language->get('text_staff');

					if ($data['staffphoto_display']) {
						if (!empty($user_info['image'])) {
							$profile_photo = $this->model_tool_image->resize($user_info['image'], 40, 40);
						} else {
							$profile_photo = $this->model_tool_image->resize('no_photoimage.png', 40, 40);
						}
					} else {
						$profile_photo = '';
					}
				} else {
					$user_info = $this->model_module_ticket_ticketuser->getTicketUser($chat['message_from_user_id']);
					if($user_info) {
						$client_name  = $user_info['name'];
					} else {
						$client_name  = $this->ticketuser->getName();
					}

					$client_type_name = $this->language->get('text_client');

					if ($data['userphoto_display']) {
						if (!empty($user_info['image'])) {
							$profile_photo = $this->model_tool_image->resize($user_info['image'], 40, 40);
						} else {
							$profile_photo = $this->model_tool_image->resize('no_photoimage.png', 40, 40);
						}
					} else {
						$profile_photo = '';
					}
				}

				$attachments_chats = ($chat['attachments']) ? json_decode($chat['attachments']) : array();
				$attachments_chats_data = array();
				foreach($attachments_chats as $attachments_chat) {
					$upload_info = $this->model_tool_upload->getUploadByCode($attachments_chat);
					if ($upload_info) {
						$attachments_chats_data[] = array(
							'name'		=> $upload_info['name'],
							'href'  	=> $this->url->link('support/ticket_view/download', 'code=' . $upload_info['code'], true)
						);
					}
				}

				$data['chats'][] = array(
					'message'			=> nl2br($chat['message']),
					'client_type'		=> $chat['client_type'],
					'client_type_name'	=> $client_type_name,
					'profile_photo'		=> $profile_photo,
					'client_name'		=> $client_name,
					'attachments'		=> $attachments_chats_data,
					'date_added'		=> date($this->language->get('date_format_short'), strtotime($chat['date_added'])),
				);
			}

			$pagination = new Pagination();
			$pagination->total = $chats_total;
			$pagination->page = $page;
			$pagination->limit = $this->config->get('ticketsetting_reply_list_limit');
			$pagination->url = $this->url->link('support/ticket_view', 'ticketrequest_id='. $ticketrequest_id .'&page={page}', true);

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($chats_total) ? (($page - 1) * $this->config->get('ticketsetting_reply_list_limit')) + 1 : 0, ((($page - 1) * $this->config->get('ticketsetting_reply_list_limit')) > ($chats_total - $this->config->get('ticketsetting_reply_list_limit'))) ? $chats_total : ((($page - 1) * $this->config->get('ticketsetting_reply_list_limit')) + $this->config->get('ticketsetting_reply_list_limit')), $chats_total, ceil($chats_total / $this->config->get('ticketsetting_reply_list_limit')));

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');

			if($this->config->get('ticketsetting_headerfooter')) {
				$data['header'] = $this->load->controller('support/header');
				$data['footer'] = $this->load->controller('support/footer');
			} else {
				$data['header'] = $this->load->controller('common/header');
				$data['footer'] = $this->load->controller('common/footer');
			}

			if(VERSION < '2.2.0.0') {
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/support/ticket_view.tpl')) {
					$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/support/ticket_view.tpl', $data));
				} else {
					$this->response->setOutput($this->load->view('default/template/support/ticket_view.tpl', $data));
				}
			} else {
				if($mytheme == 'journal3' && VERSION >= '3.0.0.0') {
					if($this->config->get('template_directory') == '') {
						$this->config->set('template_directory', 'journal3/template/');
					}

					$this->response->setOutput($this->load->view('support/ticket_view', $data));

					$this->config->set('template_engine', 'twig');
				} else {
					$this->response->setOutput($this->load->view('support/ticket_view', $data));
				}
			}
		} else {
			$this->document->setTitle($this->language->get('heading_title'));

			$data['heading_title'] = $this->language->get('heading_title');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_support'),
				'href' => $this->url->link('support/support', '', true)
			);


			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_my_tickets'),
				'href' => $this->url->link('support/request_list', '', true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('support/ticket_view', 'ticketrequest_id='. $ticketrequest_id, true)
			);

			$data['continue'] = $this->url->link('support/request_list', '', true);

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');

			if($this->config->get('ticketsetting_headerfooter')) {
				$data['header'] = $this->load->controller('support/header');
				$data['footer'] = $this->load->controller('support/footer');
			} else {
				$data['header'] = $this->load->controller('common/header');
				$data['footer'] = $this->load->controller('common/footer');
			}

			if(VERSION < '2.2.0.0') {
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/support/not_found.tpl')) {
					$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/support/not_found.tpl', $data));
				} else {
					$this->response->setOutput($this->load->view('default/template/support/not_found.tpl', $data));
				}
			} else {
				if($mytheme == 'journal3' && VERSION >= '3.0.0.0') {
					if($this->config->get('template_directory') == '') {
						$this->config->set('template_directory', 'journal3/template/');
					}

					$this->response->setOutput($this->load->view('support/not_found', $data));

					$this->config->set('template_engine', 'twig');
				} else {
					$this->response->setOutput($this->load->view('support/not_found', $data));
				}
			}
		}
	}

	public function download() {
		$this->load->model('tool/upload');

		if (isset($this->request->get['code'])) {
			$code = $this->request->get['code'];
		} else {
			$code = 0;
		}

		$upload_info = $this->model_tool_upload->getUploadByCode($code);

		if ($upload_info) {
			$file = DIR_UPLOAD . $upload_info['filename'];
			$mask = basename($upload_info['name']);

			if (!headers_sent()) {
				if (is_file($file)) {
					header('Content-Type: application/octet-stream');
					header('Content-Description: File Transfer');
					header('Content-Disposition: attachment; filename="' . ($mask ? $mask : basename($file)) . '"');
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					header('Content-Length: ' . filesize($file));

					readfile($file, 'rb');
					exit;
				} else {
					exit('Error: Could not find file ' . $file . '!');
				}
			} else {
				exit('Error: Headers already sent out!');
			}
		} else {
			$this->document->setTitle($this->language->get('text_order'));

			$data['heading_title'] = $this->language->get('text_order');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('account/order', '', true)
			);

			$data['continue'] = $this->url->link('account/order', '', true);

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	public function reply() {
		$json = array();

		$this->load->language('support/ticket_view');

		$this->load->model('module_ticket/ticketrequest');

		if (isset($this->request->get['type'])) {
			$type = $this->request->get['type'];
		} else {
			$type = '';
		}

		$url = '';
		if (isset($this->request->get['page'])) {
			$url .= '&page='. $this->request->get['page'];
		}

		if (isset($this->request->get['ticketrequest_id'])) {
			$ticketrequest_id = $this->request->get['ticketrequest_id'];
		} else {
			$ticketrequest_id = 0;
		}

		$ticketrequest_info = $this->model_module_ticket_ticketrequest->getTicketRequest($ticketrequest_id);
		if (!$ticketrequest_info) {
			$json['warning'] = $this->language->get('error_ticketnot_found');
		}

		if ($ticketrequest_info) {
			if($ticketrequest_info['ticketstatus_id'] == $this->config->get('ticketsetting_ticketstatus_closed_id')) {
				$json['warning'] = $this->language->get('error_ticket_closed');
			}
		}

		if (utf8_strlen($this->request->post['message']) < 10) {
			$json['warning'] = $this->language->get('error_message');
		}

		if(!$json) {
			$add_data = array(
				'ticketuser_id'			=> $this->ticketuser->getId(),
				'message_from_user_id'	=> $this->ticketuser->getId(),
				'client_type'			=> 'client',
				'message'				=> isset($this->request->post['message']) ? $this->request->post['message'] : '',
				'attachments'			=> isset($this->request->post['attachments']) ? (array)$this->request->post['attachments'] : array(),
				'type'					=> $type,
			);

			$this->model_module_ticket_ticketrequest->addTicketRequestChat($ticketrequest_id, $add_data);

			$json['success'] = true;
			$json['redirect'] = str_replace('&amp;', '&', $this->url->link('support/ticket_view', '&ticketrequest_id='. $ticketrequest_id . $url, true));
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
