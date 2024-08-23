<?php

/**
 * Class ModelExtensionModulePowerslipPaper
 * @Component
 */
class ModelExtensionModulePowerslipPaper extends Model {

    /**
     * @return array
     * @Endpoint
     */
    public function get_data_to_print_orders() {
        $this->load->language('sale/order');

        $data['title'] = 'Packing Slip';

        if ($this->request->server['HTTPS']) {
            $data['base'] = HTTPS_SERVER;
        } else {
            $data['base'] = HTTP_SERVER;
        }


        $this->load->model('extension/module/powerslip_dao');
        $this->load->model('extension/module/powerslip_helper');
        $this->load->model('sale/order');
        $this->load->model('setting/setting');
        $this->load->model('powerslip/powerslip_template');

        $papers = array();

        /*
         * Get selected template.
         */
        $template_index            = $this->request->get['template_index'];
        $powerslip_template_record = $this->model_powerslip_powerslip_template->getPowerslipTemplate($template_index);
        if ($this->config->get('powerslip_cfg_log')) $this->log->write("[powerslip] template is: " . var_export($powerslip_template_record, true));
        $powerslip_template = json_decode($powerslip_template_record['raw'], true);
        $fields             = isset($powerslip_template['fields']) ? $powerslip_template['fields'] : array();
        $data['is_rtl']     = isset($powerslip_template['is_rtl']);


        $order_ids = array();

        if (isset($this->request->post['selected'])) {
            $order_ids = $this->request->post['selected'];
        } elseif (isset($this->request->get['order_id'])) {
            $order_ids[] = $this->request->get['order_id'];
        }

        if ($this->config->get('powerslip_cfg_log')) $this->log->write("[powerslip] Selected orders to print: " . print_r($order_ids, true));


        /*
         *
         */
        foreach ($order_ids as $order_id) {

            $paper    = $this->render_sheet($order_id, $powerslip_template, $fields);
            $papers[] = $paper;

        }    //end foreach


        $data['papers'] = $papers;
        return $data;
    }


    public function render_sheet($order_id, $powerslip_template, $fields) {

        $order_info = $this->model_sale_order->getOrder($order_id);

        $store_info = $this->model_setting_setting->getSetting('config', $order_info['store_id']);

        if ($store_info) {
            $store_owner     = $store_info['config_owner'];
            $store_address   = $store_info['config_address'];
            $store_email     = $store_info['config_email'];
            $store_telephone = $store_info['config_telephone'];
            $store_fax       = $store_info['config_fax'];
        } else {
            $store_owner     = $this->config->get('config_owner');
            $store_address   = $this->config->get('config_address');
            $store_email     = $this->config->get('config_email');
            $store_telephone = $this->config->get('config_telephone');
            $store_fax       = $this->config->get('config_fax');
        }

        if ($order_info['invoice_no']) {
            $invoice_no = $order_info['invoice_prefix'] . $order_info['invoice_no'];
        } else {
            $invoice_no = '';
        }


        /*
         * to be used far below
         */
        $products_data = array();
        $products      = $this->model_sale_order->getOrderProducts($order_id);
        foreach ($products as $product) {
            $products_data[] = $this->model_extension_module_powerslip_dao->get_product_data($product, $order_id);
        }


        /*            $data = array(
                        'invoice_no'         => $invoice_no,
                        'date_added'         => date($this->language->get('date_format_short'), strtotime($order_info['date_added'])),
                        'store_name'         => $order_info['store_name'],
                        'store_url'          => rtrim($order_info['store_url'], '/'),
                        'store_address'      => nl2br($store_address),
                        'store_email'        => $store_email,
                        'store_telephone'    => $store_telephone,
                        'store_fax'          => $store_fax,
                        'email'              => $order_info['email'],
                        'telephone'          => $order_info['telephone'],
                        'shipping_address'   => $shipping_address,
                    );*/


        /*
         * get order_total
         */
        $this->load->model('sale/order');
        $totals          = $this->model_sale_order->getOrderTotals($order_info['order_id']);
        $order_total     = 0;
        $order_sub_total = 0;
        $shipping_cost   = 0;
        foreach ($totals as $total) {
            $value = $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']);
            if ($total['code'] == 'total') $order_total = $value;
            if ($total['code'] == 'sub_total') $order_sub_total = $value;
            if ($total['code'] == 'shipping') $shipping_cost = $value;
        }


        foreach ($fields as $key => $field) {

            if (!is_array($field)) {
                if ($this->config->get('powerslip_cfg_log')) $this->log->write("[powerslip] field is not an array. Ignoring!");
                continue;
            }

            /*
             * Positioning
             */

            //top
            $field['top_in_cm'] = isset($field['top']) ? "top: " . $field['top'] . 'cm;' : '';

            //left or right
            //                if($data['is_rtl']){
            //                    $field['left_or_right_in_cm'] = $field['right'] ? "right: " . $field['right'] . 'cm;' : '';
            //                }else{
            //                    $field['left_or_right_in_cm'] = $field['left']  ? "left: "  . $field['left']  . 'cm;' : '';
            //                }
            if ($field['left']) {
                $field['left_or_right_in_cm'] = $field['left'] ? "left: " . $field['left'] . 'cm;' : '';
            } else {
                $field['left_or_right_in_cm'] = $field['right'] ? "right: " . $field['right'] . 'cm;' : '';
            }

            //vspace (line-height)
            $field['vspace_in_cm'] = isset($field['vspace']) ? "line-height: " . $field['vspace'] . 'cm;' : '';


            /*
             *
             */

            if ($field['slug'] === 'firstname') $field['payload'] = $order_info['shipping_firstname'];
            if ($field['slug'] === 'lastname') $field['payload'] = $order_info['shipping_lastname'];
            if ($field['slug'] === 'telephone') $field['payload'] = $order_info['telephone'];
            if ($field['slug'] === 'company') $field['payload'] = $order_info['shipping_company'];
            if ($field['slug'] === 'address1') $field['payload'] = $order_info['shipping_address_1'];
            if ($field['slug'] === 'address2') $field['payload'] = $order_info['shipping_address_2'];
            if ($field['slug'] === 'city') $field['payload'] = $order_info['shipping_city'];
            if ($field['slug'] === 'postcode') $field['payload'] = $order_info['shipping_postcode'];
            if ($field['slug'] === 'zone') $field['payload'] = $order_info['shipping_zone'];
            if ($field['slug'] === 'zone_code') $field['payload'] = $order_info['shipping_zone_code'];
            if ($field['slug'] === 'country') $field['payload'] = $order_info['shipping_country'];

            if ($field['slug'] === 'order_id') $field['payload'] = $order_info['order_id'];
            if ($field['slug'] === 'total') $field['payload'] = $order_total;
            if ($field['slug'] === 'sub_total') $field['payload'] = $order_sub_total;
            if ($field['slug'] === 'shipping_cost') $field['payload'] = $shipping_cost;
            if ($field['slug'] === 'comment') $field['payload'] = nl2br($order_info['comment']);


            if ($field['slug'] == 'shipping_address_firstname') $field['payload'] = $order_info['shipping_firstname'];
            if ($field['slug'] == 'shipping_address_lastname') $field['payload'] = $order_info['shipping_lastname'];
            if ($field['slug'] == 'shipping_address_company') $field['payload'] = $order_info['shipping_company'];
            if ($field['slug'] == 'shipping_address_1') $field['payload'] = $order_info['shipping_address_1'];
            if ($field['slug'] == 'shipping_address_2') $field['payload'] = $order_info['shipping_address_2'];
            if ($field['slug'] == 'shipping_address_city') $field['payload'] = $order_info['shipping_city'];
            if ($field['slug'] == 'shipping_address_postcode') $field['payload'] = $order_info['shipping_postcode'];
            if ($field['slug'] == 'shipping_address_zone') $field['payload'] = $order_info['shipping_zone'];
            if ($field['slug'] == 'shipping_address_country') $field['payload'] = $order_info['shipping_country'];
            if ($field['slug'] == 'shipping_address_formatted') $field['payload'] = $this->model_extension_module_powerslip_helper->get_formatted_shipping_address($order_info);
            if ($this->model_extension_module_powerslip_dao->startsWith($field['slug'], 'shipping_address_custom_field')) $field['payload'] = $this->get_custom_field_value($field['slug'], $order_info);

            if ($field['slug'] == 'payment_address_firstname') $field['payload'] = $order_info['payment_firstname'];
            if ($field['slug'] == 'payment_address_lastname') $field['payload'] = $order_info['payment_lastname'];
            if ($field['slug'] == 'payment_address_company') $field['payload'] = $order_info['payment_company'];
            if ($field['slug'] == 'payment_address_1') $field['payload'] = $order_info['payment_address_1'];
            if ($field['slug'] == 'payment_address_2') $field['payload'] = $order_info['payment_address_2'];
            if ($field['slug'] == 'payment_address_city') $field['payload'] = $order_info['payment_city'];
            if ($field['slug'] == 'payment_address_postcode') $field['payload'] = $order_info['payment_postcode'];
            if ($field['slug'] == 'payment_address_zone') $field['payload'] = $order_info['payment_zone'];
            if ($field['slug'] == 'payment_address_country') $field['payload'] = $order_info['payment_country'];
            if ($this->model_extension_module_powerslip_dao->startsWith($field['slug'], 'payment_address_custom_field')) $field['payload'] = $this->get_custom_field_value($field['slug'], $order_info);


            if ($field['slug'] === 'store_owner_name') $field['payload'] = $store_owner;
            if ($field['slug'] === 'store_name') $field['payload'] = $order_info['store_name'];
            if ($field['slug'] === 'store_address') $field['payload'] = nl2br($store_address);
            if ($field['slug'] === 'store_telephone') $field['payload'] = $store_telephone;

            if ($field['slug'] === 'current_date') $field['payload'] = date("d/m/Y");
            if ($field['slug'] === 'current_time') $field['payload'] = date("H:i:s");

            //Allow use of html tags in free_text fields
            //if ($field['slug'] === 'freetext') $field['payload'] = $field['text_content'];
            if ($field['slug'] === 'freetext') $field['payload'] = html_entity_decode($field['text_content']);

            if ($field['slug'] === 'product_loop') $field['payload'] = $this->get_product_elements($products_data, $order_info, $field);


            $powerslip_template['fields'][$key] = $field;
        }


        $powerslip_template['width_in_cm']         = $powerslip_template['width'] ? "width:" . $powerslip_template['width'] . 'cm;' : '';
        $powerslip_template['height_in_cm']        = $powerslip_template['height'] ? "height: " . $powerslip_template['height'] . 'cm;' : '';
        $powerslip_template['font_size_in_pt']     = $powerslip_template['font_size'] ? "font-size: " . $powerslip_template['font_size'] . 'pt;' : '';
        $powerslip_template['margin_bottom_in_cm'] = $powerslip_template['inter_slip_space'] ? "margin-bottom: " . $powerslip_template['inter_slip_space'] . 'cm;' : '';


        $paper = $this->load->view('extension/module/powerslip/sale/powerslip_paper', $powerslip_template);
        return $paper;
    }


    /**
     *
     */
    private function get_product_elements($products_info, $order_info, $field) {

        $args = array(
            'products_info' => $products_info,
            'order_info'    => $order_info,
            'loop_template' => $field['product_loop_template'],
        );


        /*
         *
         */
        $data['rows']  = $this->do_product_loop_rendering($args);
        $data['field'] = $field;

        /*
         *
         */
        return $this->load->view('extension/module/powerslip/sale/powerslip_product_loop', $data);
    }


    /**
     *
     */
    private function do_product_loop_rendering($args) {

        $rows = array();

        $products_info = $args['products_info'];
        $order_info    = $args['order_info'];
        $loop_template = $args['loop_template'];


        $find = array(
            //            '{product_powerslip_name}',
            '{product_name}',
            '{product_price}',
            '{product_quantity}',
        );

        foreach ($products_info as $product_info) {

            $replace = array(
                //                '{product_powerslip_name}' => $product_info['powerslip_name'],
                '{product_name}'     => $product_info['name'],
                '{product_price}'    => $this->currency->format($product_info['price'], $order_info['currency_code']),
                '{product_quantity}' => $product_info['quantity'],
            );

            $row    = str_replace($find, $replace, $loop_template);
            $row    = nl2br($row);
            $rows[] = $row;
        }


        return $rows;
    }


    /**
     * Add support for custom fields:
     */
    private function get_custom_field_value($prefixed_customer_field, $order_info) {
        $this->load->model('extension/module/powerslip_dao');

        /*
         * replace prefix
         */
        if ($this->model_extension_module_powerslip_dao->startsWith($prefixed_customer_field, "shipping_address_custom_field_")) {
            $custom_field_id    = str_replace("shipping_address_custom_field_", "", $prefixed_customer_field);
            $custom_field_value = $order_info['shipping_custom_field'][$custom_field_id];

        } else {
            $custom_field_id    = str_replace("payment_address_custom_field_", "", $prefixed_customer_field);
            $custom_field_value = $order_info['payment_custom_field'][$custom_field_id];
        }

        return $custom_field_value;
    }


}