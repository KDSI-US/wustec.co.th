<?php

/**
 * @Component
 * Class ControllerExtensionModulePowerslip
 */
require_once(DIR_SYSTEM . "library/powerslip/admin_controller_install_trait.php");
class ControllerExtensionModulePowerslip extends Controller {
    use admin_controller_install_trait;

    private $module_code = "powerslip";

    /**
     *
     */
    private function get_default_template() {

        $default_template = array(
            "name"             => "",
            "width"            => "21",
            "height"           => "29.7",
            "font_size"        => "12",
            "inter_slip_space" => "2",
            "is_rtl"           => "false",

            'fields' => array(),

            'product_fields'  => array(),
            'product_lefts'   => array(),
            'product_rights'  => array(),
            'product_tops'    => array(),
            'product_vspaces' => array(),

            'freetext_fields' => array(),
            'freetext_lefts'  => array(),
            'freetext_rights' => array(),
            'freetext_tops'   => array(),
        );

        return $default_template;
    }


    /**
     *
     */
    public function install() {

        $defaultSettings = array(
            "powerslip_templates"            => array($this->get_default_template()),
            "powerslip_cfg_extension_status" => true,
            "powerslip_cfg_log"              => true,
        );

        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting($this->module_code, $defaultSettings);
        $this->model_setting_setting->editSetting('module_' . $this->module_code, array('module_' . $this->module_code . '_status' => true));

        $this->load->model('extension/module/powerslip');
        $this->model_extension_module_powerslip->install();

        $this->add_all_events();
    }

    public function uninstall() {
        $this->load->model('extension/module/powerslip');
        $this->model_extension_module_powerslip->uninstall();

        $this->delete_all_events();
    }

    /**
     *
     */
    public function index() {

        $module_route  = 'extension/module/powerslip';
        $modules_route = 'marketplace/extension';

        $this->load->language('extension/module/powerslip');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {

            $this->model_setting_setting->editSetting($this->module_code, $this->request->post);

            //Let the extension show as enabled/disabled in the module list
            if ($this->request->post['powerslip_cfg_extension_status']) {
                $this->model_setting_setting->editSetting('module_' . $this->module_code, array('module_' . $this->module_code . '_status' => true));
            } else {
                $this->model_setting_setting->editSetting('module_' . $this->module_code, array('module_' . $this->module_code . '_status' => false));
            }

            $this->session->data['success'] = $this->language->get('text_success');


            if (isset($this->request->post['submit_btn'])) {
                $this->response->redirect($this->url->link($module_route, 'type=module&user_token=' . $this->session->data['user_token'], 'SSL'));
            } else {
                $this->response->redirect($this->url->link($modules_route, 'type=module&user_token=' . $this->session->data['user_token'], 'SSL'));
            }
        }


        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'text'      => $this->language->get('text_home'),
            'separator' => FALSE
        );

        $data['breadcrumbs'][] = array(
            'href'      => $this->url->link($modules_route, 'user_token=' . $this->session->data['user_token'] . '&type=module', 'SSL'),
            'text'      => $this->language->get('text_module'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'href'      => $this->url->link($module_route, 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'text'      => $this->language->get('heading_title'),
            'separator' => ' :: '
        );


        $data['action'] = $this->url->link($module_route, 'user_token=' . $this->session->data['user_token'], 'SSL');

        $data['cancel'] = $this->url->link($modules_route, 'user_token=' . $this->session->data['user_token'], 'SSL');

        $data['action_add'] = $this->url->link("powerslip/powerslip_template", 'user_token=' . $this->session->data['user_token'], 'SSL');

        $data['heading_title'] = $this->language->get('heading_title');
        $data['button_save']   = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no']  = $this->language->get('text_no');

        $data['error_warning'] = '';

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }


        $config_data = array(
            'powerslip_cfg_extension_status',
            'powerslip_cfg_log'
        );

        foreach ($config_data as $conf) {
            if (isset($this->request->post[$conf])) {
                $data[$conf] = $this->request->post[$conf];
            } else {
                $data[$conf] = $this->config->get($conf);
            }
        }

        /*
         *
         */
        $data['tab_troubleshooting'] = $this->load->view('extension/module/powerslip/settings/powerslip_troubleshooting', $data);

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/powerslip/settings/powerslip', $data));
    }


    /**
     *
     */


    /**
     * get "address" custom fields
     */
    private function get_custom_fields() {

        $this->load->model('customer/custom_field');
        $custom_fields    = $this->model_customer_custom_field->getCustomFields();
        $my_custom_fields = array();
        foreach ($custom_fields as $custom_field) {
            if ($custom_field['location'] == 'address' && $custom_field['status'] == 1) {
                $my_custom_fields[] = $custom_field;
            }
        }

        if ($this->config->get('powerslip_cfg_log')) $this->log->write('[powerslip] ' . print_r($my_custom_fields, true));

        return $my_custom_fields;
    }

    /**
     * @Endpoint
     */
    public function print_orders() {

        $this->load->model('extension/module/powerslip_paper');

        $data = $this->model_extension_module_powerslip_paper->get_data_to_print_orders();

        $this->response->setOutput($this->load->view('extension/module/powerslip/sale/powerslip_papers', $data));
    }


}