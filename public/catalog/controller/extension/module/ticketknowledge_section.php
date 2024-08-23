<?php
class ControllerExtensionModuleTicketknowledgeSection extends Controller {
	public function index() {
		$this->load->language('extension/module/ticketknowledge_section');
		
		$this->load->model('module_ticket/support');

		$data['heading_title'] = $this->language->get('heading_title');

		if (isset($this->request->get['ticketknowledge_section_id'])) {
			$data['ticketknowledge_section_id'] = (int)$this->request->get['ticketknowledge_section_id'];
		} else {
			$data['ticketknowledge_section_id'] = 0;
		}

		$ticketknowledge_sections = $this->model_module_ticket_support->getTicketknowledgeSections();
		$data['ticketknowledge_sections'] = array();
		foreach ($ticketknowledge_sections as $ticketknowledge_section) {
			$data['ticketknowledge_sections'][] = array(
				'ticketknowledge_section_id'		=> $ticketknowledge_section['ticketknowledge_section_id'],
				'icon_class'				=> $ticketknowledge_section['icon_class'],
				'title'						=> $ticketknowledge_section['title'],
				'sub_title'					=> $ticketknowledge_section['sub_title'],
				'href'						=> $this->url->link('support/knowledge_section', 'ticketknowledge_section_id='. $ticketknowledge_section['ticketknowledge_section_id'], true),
			);
		}

		return $this->load->view('extension/module/ticketknowledge_section', $data);
	}
}