<?php
/* This file is under Git Control by KDSI. */
trait admin_controller_install_trait{

    /**
     * @Endpoint
     */
    private function add_all_events() {
        $this->load->model('setting/event');

        $this->model_setting_event->addEvent(
            'powerslip_event_listener',
            'admin/view/common/column_left/before',
            'extension/module/powerslip/event_listener_add_menu'
        );

        $this->model_setting_event->addEvent(
            'powerslip_event_listener',
            'admin/view/sale/order_list/after',
            'extension/module/powerslip/event_listener_add_dropdown_button_in_order_list'
        );
    }

    /**
     * @Endpoint
     */
    private function delete_all_events() {
        $this->load->model('setting/event');
        $this->model_setting_event->deleteEventByCode("powerslip_event_listener");
    }


    /**
     * @Endpoint
     */
    public function event_listener_add_menu($route, &$data, &$template) {

//        if($this->config->get('powerslip_cfg_log')) $this->log->write("[powerslip] in event_listener_add_menu");
        $this->load->language('extension/module/powerslip_pub');

        $menu = array(
            'name'     => $this->language->get('text_menu_powerslip_templates'),
            'href'     => $this->url->link('powerslip/powerslip_template', 'user_token=' . $this->session->data['user_token'], true),
            'children' => array()
        );

        foreach ($data['menus'] as $k => $m){
            if($m['id'] === 'menu-design'){
//                if($this->config->get('powerslip_cfg_log')) $this->log->write("[powerslip] menu found: " . var_export($m, true));
                $m['children'][] = $menu;

                $data['menus'][$k] = $m;
                break;
            }
        }
    }

    /**
     * @Endpoint
     * @param $route
     * @param $data
     * @param $output
     */
    public function event_listener_add_dropdown_button_in_order_list($route, &$data, &$output) {
        $hook      = '<button type="submit" id="button-shipping" form="form-order"';
        $injection = $this->get_dropdown_for_order_list() . $hook;
        $output    = str_replace($hook, $injection, $output);
    }

    public function get_dropdown_for_order_list() {
        $data = array();

        $this->load->model('powerslip/powerslip_template');
        $results = $this->model_powerslip_powerslip_template->getPowerslipTemplates();
        foreach ($results as $result){
            $data['powerslip_templates'][] = array(
                'name'   => $result['template_name'],
                'action' => $this->url->link('extension/module/powerslip/print_orders', 'user_token=' . $this->session->data['user_token'] . '&template_index=' . $result['id'] , 'SSL'),
            );
        }

        return $this->load->view('extension/module/powerslip/fragments/powerslip_dropdown_in_order_list', $data);
    }
}