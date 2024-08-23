<?php
class ModelExtensionModulePowerslipDao extends Model {


    /**
     *
     */
    public function get_product_data($product, $order_id){

        $this->load->language('sale/order');

        $this->load->model('tool/upload');
        $this->load->model('sale/order');
        $this->load->model('catalog/product');



        $product_info = $this->model_catalog_product->getProduct($product['product_id']);

        $option_data = array();

        $options = $this->model_sale_order->getOrderOptions($order_id, $product['order_product_id']);

        foreach ($options as $option) {
            if ($option['type'] != 'file') {
                $value = $option['value'];
            } else {
                $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

                if ($upload_info) {
                    $value = $upload_info['name'];
                } else {
                    $value = '';
                }
            }

            $option_data[] = array(
                'name'  => $option['name'],
                'value' => $value
            );
        }

        return array(
            'name'           => $product_info['name'],
            //'powerslip_name' => $product_info['powerslip_name'],
            'price'          => $product_info['price'],
            'model'          => $product_info['model'],
            'option'         => $option_data,
            'quantity'       => $product['quantity'],
            'location'       => $product_info['location'],
            'sku'            => $product_info['sku'],
            'upc'            => $product_info['upc'],
            'ean'            => $product_info['ean'],
            'jan'            => $product_info['jan'],
            'isbn'           => $product_info['isbn'],
            'mpn'            => $product_info['mpn'],
            'weight'         => $this->weight->format($product_info['weight'], $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point'))
        );
    }



    public function startsWith($haystack, $needle){
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }



    /**
     *
     */
    public function get_formatted_shipping_address($order_info) {

        if ($order_info['shipping_address_format']) {
            $format = $order_info['shipping_address_format'];
        } else {
            $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
        }

        $find = array(
            '{firstname}',
            '{lastname}',
            '{company}',
            '{address_1}',
            '{address_2}',
            '{city}',
            '{postcode}',
            '{zone}',
            '{zone_code}',
            '{country}'
        );

        $replace = array(
            'firstname' => $order_info['shipping_firstname'],
            'lastname'  => $order_info['shipping_lastname'],
            'company'   => $order_info['shipping_company'],
            'address_1' => $order_info['shipping_address_1'],
            'address_2' => $order_info['shipping_address_2'],
            'city'      => $order_info['shipping_city'],
            'postcode'  => $order_info['shipping_postcode'],
            'zone'      => $order_info['shipping_zone'],
            'zone_code' => $order_info['shipping_zone_code'],
            'country'   => $order_info['shipping_country']
        );

        $shipping_address = str_replace($find, $replace, $format);
        $shipping_address = nl2br($shipping_address);

        return $shipping_address;
    }

}