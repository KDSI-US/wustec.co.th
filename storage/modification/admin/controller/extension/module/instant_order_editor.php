<?php
/* This file is under Git Control by KDSI. */

class ControllerExtensionModuleInstantOrderEditor extends Controller
{
	public function install()
	{
		$this->load->model('setting/event');
		$this->model_setting_event->addEvent(
			'instant_order_editor_header',
			'admin/view/common/header/before',
			'extension/module/instant_order_editor/addScript'
		);
		$this->model_setting_event->addEvent(
			'instant_order_editor_menu',
			'admin/view/common/column_left/before',
			'extension/module/instant_order_editor/addMenu'
		);
	}

	public function uninstall()
	{
		$this->load->model('setting/event');
		$this->model_setting_event->deleteEventByCode('instant_order_editor_header');
		$this->model_setting_event->deleteEventByCode('instant_order_editor_menu');
	}

	public function addScript($eventRoute, &$data)
	{
		$data['scripts'][] = 'view/javascript/ioe.js';
	}

	public function addMenu($eventRoute, &$data)
	{
		$this->load->language('extension/instant_order_editor');
		foreach ($data['menus'] as $key => $menu) {
			if ($menu['id'] == 'menu-sale') {
				$data['menus'][$key]['children'] = array_merge(
					[
						[
							'id' => 'instant-order-editor',
							'icon' => 'fa fa-shopping-cart fa-fw',
							'name' => $this->language->get('ioe_menu'),
							'href' => $this->url->link(
								'extension/instant_order_editor',
								'user_token=' . $this->session->data['user_token'],
								true
							),
							'children' => array()
						]
					],
					$data['menus'][$key]['children']
				);
			}
		}
	}
}
