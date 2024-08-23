<?php
/* This file is under Git Control by KDSI. */
class ControllerSupportKnowledgeArticle extends Controller {
	private $error = array();

	public function index() {
		$this->document->addScript('catalog/view/javascript/modulepoints/ticketsystem.js');
		$this->document->addStyle('catalog/view/theme/default/stylesheet/modulepoints/ticketsystem.css');

		if($this->config->get('ticketsetting_headerfooter')) {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/modulepoints/moduleheader.css');
		}

		$this->load->language('support/knowledge_article');

		$this->load->model('module_ticket/support');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_support'),
			'href' => $this->url->link('support/support')
		);

		if (isset($this->request->get['ticketknowledge_section_id'])) {
			$ticketknowledge_section_id = (int)$this->request->get['ticketknowledge_section_id'];
		} else {
			$ticketknowledge_section_id = 0;
		}

		$ticketknowledge_section_info = $this->model_module_ticket_support->getTicketknowledgeSection($ticketknowledge_section_id);
		if($ticketknowledge_section_info) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get($ticketknowledge_section_info['title']),
				'href' => $this->url->link('support/knowledge_section', 'ticketknowledge_section_id=' .  $ticketknowledge_section_id)
			);
		}



		if (isset($this->request->get['ticketknowledge_article_id'])) {
			$ticketknowledge_article_id = (int)$this->request->get['ticketknowledge_article_id'];
		} else {
			$ticketknowledge_article_id = 0;
		}

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

		$ticketknowledge_article_info = $this->model_module_ticket_support->getTicketknowledgeArticle($ticketknowledge_article_id);

		if ($ticketknowledge_article_info) {
			$this->document->setTitle($ticketknowledge_article_info['meta_title']);
			$this->document->setDescription($ticketknowledge_article_info['meta_description']);
			$this->document->setKeywords($ticketknowledge_article_info['meta_keyword']);

			$url = '';
			if(isset($this->request->get['ticketknowledge_section_id'])) {
				$url .= '&ticketknowledge_section_id='. $this->request->get['ticketknowledge_section_id'];
			}

			if(isset($this->request->get['ticketknowledge_article_id'])) {
				$url .= '&ticketknowledge_article_id='. $this->request->get['ticketknowledge_article_id'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $ticketknowledge_article_info['title'],
				'href' => $this->url->link('support/knowledge_article', ''. $url, true)
			);

			$data['heading_title'] = $ticketknowledge_article_info['title'];
			$data['banner_title'] = $ticketknowledge_article_info['banner_title'];
			$data['description'] = html_entity_decode($ticketknowledge_article_info['description'], ENT_QUOTES, 'UTF-8');

			$relateds = $this->model_module_ticket_support->getTicketknowledgeArticleRelated($ticketknowledge_article_id);

			$data['relateds'] = array();
			foreach($relateds as $related) {
				$data['relateds'][] = array(
					'ticketknowledge_article_id'		=> $ticketknowledge_article_id,
					'title'		=> $related['title'],
					'href'		=> $this->url->link('support/knowledge_article', '&ticketknowledge_article_id='. $related['ticketknowledge_article_id'], true),
				);
			}

			$data['text_related'] = $this->language->get('text_related');

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
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/support/knowledge_article.tpl')) {
					$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/support/knowledge_article.tpl', $data));
				} else {
					$this->response->setOutput($this->load->view('default/template/support/knowledge_article.tpl', $data));
				}
			} else {
				if($mytheme == 'journal3' && VERSION >= '3.0.0.0') {
					if($this->config->get('template_directory') == '') {
						$this->config->set('template_directory', 'journal3/template/');
					}

					$this->response->setOutput($this->load->view('support/knowledge_article', $data));

					$this->config->set('template_engine', 'twig');
				} else {
					$this->response->setOutput($this->load->view('support/knowledge_article', $data));
				}
			}
		} else {
			$url = '';
			if(isset($this->request->get['ticketknowledge_section_id'])) {
				$url .= '&ticketknowledge_section_id='. $this->request->get['ticketknowledge_section_id'];
			}

			if(isset($this->request->get['ticketknowledge_article_id'])) {
				$url .= '&ticketknowledge_article_id='. $this->request->get['ticketknowledge_article_id'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('support/knowledge_article', ''. $url, true)
			);

			$this->document->setTitle($this->language->get('banner_title_notfound'));

			$data['heading_title'] = $this->language->get('banner_title_notfound');

			$data['banner_title'] = $this->language->get('banner_title_notfound');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['continue'] = $this->url->link('support/support', '', true);

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
}
