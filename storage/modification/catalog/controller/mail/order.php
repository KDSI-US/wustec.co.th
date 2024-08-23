<?php
/* This file is under Git Control by KDSI. */
class ControllerMailOrder extends Controller {
	public function index(&$route, &$args) {
		if (isset($args[0])) {
			$order_id = $args[0];
		} else {
			$order_id = 0;
		}

		if (isset($args[1])) {
			$order_status_id = $args[1];
		} else {
			$order_status_id = 0;
		}	

		if (isset($args[2])) {
			$comment = $args[2];
		} else {
			$comment = '';
		}
		
		if (isset($args[3])) {
			$notify = $args[3];
		} else {
			$notify = '';
		}
						
		// We need to grab the old order status ID
		$order_info = $this->model_checkout_order->getOrder($order_id);
		
		if ($order_info) {
			// If order status is 0 then becomes greater than 0 send main html email
			if (!$order_info['order_status_id'] && $order_status_id) {
				$this->add($order_info, $order_status_id, $comment, $notify);
			} 
			
			// If order status is not 0 then send update text email
			if ($order_info['order_status_id'] && $order_status_id && $notify) {
				$this->edit($order_info, $order_status_id, $comment, $notify);
			}		
		}
	}
		
	public function add($order_info, $order_status_id, $comment, $notify) {
		// Check for any downloadable products
		$download_status = false;

		$order_products = $this->model_checkout_order->getOrderProducts($order_info['order_id']);
		
		foreach ($order_products as $order_product) {
			// Check if there are any linked downloads
			$product_download_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product_to_download` WHERE product_id = '" . (int)$order_product['product_id'] . "'");

			if ($product_download_query->row['total']) {
				$download_status = true;
			}
		}
		
		// Load the language for any mails that might be required to be sent out
		$language = new Language($order_info['language_code']);
		$language->load($order_info['language_code']);
		$language->load('mail/order_add');

		// HTML Mail
		$data['title'] = sprintf($language->get('text_subject'), $order_info['store_name'], $order_info['order_id']);

		$data['text_greeting'] = sprintf($language->get('text_greeting'), $order_info['store_name']);
		$data['text_link'] = $language->get('text_link');
		$data['text_download'] = $language->get('text_download');
		$data['text_order_detail'] = $language->get('text_order_detail');
		$data['text_instruction'] = $language->get('text_instruction');
		$data['text_order_id'] = $language->get('text_order_id');
		$data['text_date_added'] = $language->get('text_date_added');
		$data['text_payment_method'] = $language->get('text_payment_method');
		$data['text_shipping_method'] = $language->get('text_shipping_method');
		$data['text_email'] = $language->get('text_email');
		$data['text_telephone'] = $language->get('text_telephone');
		$data['text_ip'] = $language->get('text_ip');
		$data['text_order_status'] = $language->get('text_order_status');
		$data['text_payment_address'] = $language->get('text_payment_address');
		$data['text_shipping_address'] = $language->get('text_shipping_address');
		$data['text_product'] = $language->get('text_product');
		$data['text_model'] = $language->get('text_model');
		$data['text_quantity'] = $language->get('text_quantity');
		$data['text_price'] = $language->get('text_price');
		$data['text_total'] = $language->get('text_total');
		$data['text_footer'] = $language->get('text_footer');

		$data['logo'] = $order_info['store_url'] . 'image/' . $this->config->get('config_logo');
		$data['store_name'] = $order_info['store_name'];
		$data['store_url'] = $order_info['store_url'];
		$data['customer_id'] = $order_info['customer_id'];
		$data['link'] = $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_info['order_id'];

		if ($download_status) {
			$data['download'] = $order_info['store_url'] . 'index.php?route=account/download';
		} else {
			$data['download'] = '';
		}

		$data['order_id'] = $order_info['order_id'];
		$data['date_added'] = date($language->get('date_format_short'), strtotime($order_info['date_added']));
		$data['payment_method'] = $order_info['payment_method'];
		$data['shipping_method'] = $order_info['shipping_method'];
		$data['email'] = $order_info['email'];
		$data['telephone'] = $order_info['telephone'];
		$data['ip'] = $order_info['ip'];

		$order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order_info['language_id'] . "'");
	
		if ($order_status_query->num_rows) {
			$data['order_status'] = $order_status_query->row['name'];
		} else {
			$data['order_status'] = '';
		}

		if ($comment && $notify) {
			$data['comment'] = nl2br($comment);
		} else {
			$data['comment'] = '';
		}

		if ($order_info['payment_address_format']) {
			$format = $order_info['payment_address_format'];
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
			'firstname' => $order_info['payment_firstname'],
			'lastname'  => $order_info['payment_lastname'],
			'company'   => $order_info['payment_company'],
			'address_1' => $order_info['payment_address_1'],
			'address_2' => $order_info['payment_address_2'],
			'city'      => $order_info['payment_city'],
			'postcode'  => $order_info['payment_postcode'],
			'zone'      => $order_info['payment_zone'],
			'zone_code' => $order_info['payment_zone_code'],
			'country'   => $order_info['payment_country']
		);

		$data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

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

		$data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

		$this->load->model('tool/upload');

		// Products
		$data['products'] = array();

		foreach ($order_products as $order_product) {
			$option_data = array();

			$order_options = $this->model_checkout_order->getOrderOptions($order_info['order_id'], $order_product['order_product_id']);

			foreach ($order_options as $order_option) {
				if ($order_option['type'] != 'file') {
					$value = $order_option['value'];
				} else {
					$upload_info = $this->model_tool_upload->getUploadByCode($order_option['value']);

					if ($upload_info) {
						$value = $upload_info['name'];
					} else {
						$value = '';
					}
				}

				$option_data[] = array(
					'order_option_id'  => $order_option['order_option_id'],
					'name'  => $order_option['name'],
					'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
				);
			}

			$data['products'][] = array(
				'product_id'  => $order_product['product_id'],
				'order_product_id'  => $order_product['order_product_id'],
				'name'     => $order_product['name'],
				'model'    => $order_product['model'],
				'option'   => $option_data,
				'quantity' => $order_product['quantity'],
				'price'    => $this->currency->format($order_product['price'] + ($this->config->get('config_tax') ? $order_product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
				'total'    => $this->currency->format($order_product['total'] + ($this->config->get('config_tax') ? ($order_product['tax'] * $order_product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
			);
		}

		// Vouchers
		$data['vouchers'] = array();

		$order_vouchers = $this->model_checkout_order->getOrderVouchers($order_info['order_id']);

		foreach ($order_vouchers as $order_voucher) {
			$data['vouchers'][] = array(
				'description' => $order_voucher['description'],
				'amount'      => $this->currency->format($order_voucher['amount'], $order_info['currency_code'], $order_info['currency_value']),
			);
		}

		// Order Totals
		$data['totals'] = array();
		
		$order_totals = $this->model_checkout_order->getOrderTotals($order_info['order_id']);

		foreach ($order_totals as $order_total) {
			$data['totals'][] = array(
				'title' => $order_total['title'],
				'text'  => $this->currency->format($order_total['value'], $order_info['currency_code'], $order_info['currency_value']),
			);
		}
	
		$this->load->model('setting/setting');
		
		$from = $this->model_setting_setting->getSettingValue('config_email', $order_info['store_id']);
		
		if (!$from) {
			$from = $this->config->get('config_email');
		}
		

		// Prepare mail: order.customer
		$language->load('product/product'); // required for stock status
		$language->load('extension/module/emailtemplate/order');

		$this->load->model('tool/image');
		$this->load->model('catalog/product');
		$this->load->model('extension/module/emailtemplate');

		$template_load = array(
			'key' => 'order.customer',
			'customer_id' => $order_info['customer_id'],
            'customer_group_id' => $order_info['customer_group_id'],
            'language_id' => $order_info['language_id'],
            'order_status_id' => $order_status_id,
            'store_id' => $order_info['store_id'],
            'payment_method' => $order_info['payment_code'],
            'shipping_method' => $order_info['shipping_code']
		);

		$template_filter = array();

		if (!empty($order_info['customer_id']) && !isset($total_customer_orders)) {
			$total_customer_orders_query = $this->db->query("SELECT count(1) as total FROM `" . DB_PREFIX . "order` WHERE customer_id = '" . (int)$order_info['customer_id'] . "'");

			if ($total_customer_orders_query->row) {
				$total_customer_orders = $total_customer_orders_query->row['total'];
				$template_filter['total_customer_orders'] = $total_customer_orders;
			}
		}

		$template = $this->model_extension_module_emailtemplate->load($template_load, $data, $template_filter);

        if ($template) {
            $template->addData($order_info);

            if (!empty($order_info['customer_group_id'])) {
                $this->load->model('account/customer_group');

                $customer_group = $this->model_account_customer_group->getCustomerGroup($order_info['customer_group_id']);

                if ($customer_group) {
                    $template->data['customer_group'] = array(
                        'name' => $customer_group['name'],
                        'description' => $customer_group['description']
                    );
                }
            }

            if (!empty($order_info['affiliate_id']) && file_exists(DIR_APPLICATION . 'model/account/affiliate.php')) {
                $this->load->model('account/affiliate');

                $affiliate_info = $this->model_account_affiliate->getAffiliate($order_info['affiliate_id']);

                if ($affiliate_info) {
                    $template->data['affiliate'] = $affiliate_info;
                }
            }

            // Custom fields
            if (!empty($order_info['custom_field']) || !empty($order_info['payment_custom_field'])) {
                $this->load->model('account/custom_field');

                // Cleanup
                foreach(array('custom_field', 'payment_custom_field', 'shipping_custom_field') as $var) {
                    if (isset($template->data[$var])) {
                        unset($template->data[$var]);
                    }
                }

                if (!empty($order_info['customer_group_id'])) {
                    $customer_group_id = $order_info['customer_group_id'];
                } else {
                    $customer_group_id = $this->config->get('config_customer_group_id');
                }

                $custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

                foreach ($custom_fields as $custom_field) {
                    if (isset($order_info['payment_custom_field'][$custom_field['custom_field_id']])) {
                        $template->data['custom_field_' . $custom_field['custom_field_id'] . '_name'] = $custom_field['name'];

                        if ($custom_field['custom_field_value']) {
                            foreach ($custom_field['custom_field_value'] as $custom_field_value) {
                                if (is_array($order_info['payment_custom_field'][$custom_field['custom_field_id']])) {
                                    if (in_array($custom_field_value['custom_field_value_id'], $order_info['payment_custom_field'][$custom_field['custom_field_id']])) {
                                        $template->data['custom_field_' . $custom_field['custom_field_id'] . '_value_' . $custom_field_value['custom_field_value_id']] = $custom_field_value['name'];
                                    }
                                } else {
                                    if ($custom_field_value['custom_field_value_id'] == $order_info['payment_custom_field'][$custom_field['custom_field_id']]) {
                                        $template->data['custom_field_' . $custom_field['custom_field_id'] . '_value'] = $custom_field_value['name'];
                                    }
                                }
                            }
                        } else {
                            $template->data['custom_field_' . $custom_field['custom_field_id'] . '_value'] = $order_info['payment_custom_field'][$custom_field['custom_field_id']];
                        }
                    } elseif (isset($order_info['custom_field'][$custom_field['custom_field_id']])) {
                        $template->data['custom_field_' . $custom_field['custom_field_id'] . '_name'] = $custom_field['name'];

                        if ($custom_field['custom_field_value']) {
                            foreach ($custom_field['custom_field_value'] as $custom_field_value) {
                                if (is_array($order_info['custom_field'][$custom_field['custom_field_id']])) {
                                    if (in_array($custom_field_value['custom_field_value_id'], $order_info['custom_field'][$custom_field['custom_field_id']])) {
                                        $template->data['custom_field_' . $custom_field['custom_field_id'] . '_value_' . $custom_field_value['custom_field_value_id']] = $custom_field_value['name'];
                                    }
                                } else {
                                    if ($custom_field_value['custom_field_value_id'] == $order_info['custom_field'][$custom_field['custom_field_id']]) {
                                        $template->data['custom_field_' . $custom_field['custom_field_id'] . '_value'] = $custom_field_value['name'];
                                    }
                                }
                            }
                        } else {
                            $template->data['custom_field_' . $custom_field['custom_field_id'] . '_value'] = $order_info['custom_field'][$custom_field['custom_field_id']];
                        }
                    }
                }
            }

            // Address
            foreach(array('payment', 'shipping') as $var) {
                if ($order_info[$var . '_address_format']) {
                    $format = $order_info[$var . '_address_format'];
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
                    'firstname' => $order_info[$var . '_firstname'],
                    'lastname'  => $order_info[$var . '_lastname'],
                    'company'   => $order_info[$var . '_company'],
                    'address_1' => $order_info[$var . '_address_1'],
                    'address_2' => $order_info[$var . '_address_2'],
                    'city'      => $order_info[$var . '_city'],
                    'postcode'  => $order_info[$var . '_postcode'],
                    'zone'      => $order_info[$var . '_zone'],
                    'zone_code' => $order_info[$var . '_zone_code'],
                    'country'   => $order_info[$var . '_country']
                );

                if (isset($order_info['custom_field'])) {
                    foreach ($order_info['custom_field'] as $custom_field_id => $custom_field) {
                        $find[] = '{custom_field_' . $custom_field_id . '}';
                        $replace[] = isset($custom_field['value']) ? $custom_field['value'] : '';
                    }
                }

                if (!empty($order_info[$var . '_custom_field']) && is_array($order_info[$var . '_custom_field'])) {
                    foreach ($order_info[$var . '_custom_field'] as $custom_field_id => $custom_field) {
                        $find[] = '{custom_field_' . $custom_field_id . '}';
                        $replace[] = isset($custom_field['value']) ? $custom_field['value'] : $custom_field;
                    }
                }

                $template->data[$var . '_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
            }

            $order_status_query = $this->db->query("SELECT `name` FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order_info['language_id'] . "' LIMIT 1");

            if ($order_status_query->row) {
                $order_status = $order_status_query->row['name'];

                $template->data['order_status'] = $order_status;
            }

            if ($this->config->get('config_processing_status') && in_array($order_status_id, $this->config->get('config_processing_status'))) {
                $template->data['order_processing_status'] = true;
            } elseif ($this->config->get('config_complete_status') && in_array($order_status_id, $this->config->get('config_complete_status'))) {
                $template->data['order_complete_status'] = true;
            }

            $template->data['total_customer_orders'] = isset($total_customer_orders) ? $total_customer_orders : 0;

            $products_stock = array();

            if (!empty($template->data['config']['order_products']['option_length'])) {
                $option_length = $template->data['config']['order_products']['option_length'];
            } else {
                $option_length = 120;
            }

            // Add extra data to products array
            $template->data['products'] = $data['products'];

            foreach ($template->data['products'] as $i => $product) {
                $order_product = false;
                foreach ($order_products as $row) {
                    if ($product['order_product_id'] == $row['order_product_id']) {
                        $order_product = $row;
                        break;
                    }
                }

                $product_data = $this->model_catalog_product->getProduct($order_product['product_id']);

                if ($product_data) {
                    if ($product['option']) {
                        $order_options = $this->model_checkout_order->getOrderOptions($order_info['order_id'], $order_product['order_product_id']);

                        foreach ($product['option'] as $ii => $product_option) {
                            $order_option = false;
                            foreach ($order_options as $order_option) {
                                if ($order_option['order_option_id'] == $product_option['order_option_id']) {
                                    break;
                                }
                            }

                            if ($order_option && $order_option['type'] != 'file') {
                                $value = $order_option['value'];
                                $template->data['products'][$i]['option'][$ii]['value'] = utf8_strlen($value) > $option_length ? utf8_substr($value, 0, $option_length) . '..' : $value;
                            }
                        }

                        $product_data['option_value'] = array();

                        $order_option_query = $this->db->query("SELECT oo.* FROM " . DB_PREFIX . "order_option oo WHERE oo.order_product_id = '" . (int)$product['order_product_id'] . "' AND oo.order_id = '" . (int)$order_info['order_id'] . "'");

                        if ($order_option_query->rows) {
                            foreach ($order_option_query->rows as $order_option) {
                                $order_option_value_query = $this->db->query("SELECT pov.*, ov.image FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) WHERE pov.product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "'");

                                if ($order_option_value_query->rows) {
                                    foreach ($order_option_value_query->rows as $order_option_value) {
                                        $product_data['option_value'][] = array(
                                            'order_option_id' => $order_option['order_option_id'],
                                            'product_option_value_id' => $order_option['product_option_value_id'],
                                            'type' => $order_option['type'],
                                            'image' => $order_option_value['image'],
                                            'price' => $order_option_value['price'],
                                            'price_prefix' => $order_option_value['price_prefix'],
                                            //'stock_quantity' => $order_option_value['quantity'],
                                            'value' => $order_option['value']
                                        );
                                    }
                                }
                            }
                        }
                    }

                    $image = '';

                    if (!empty($template->data['config']['order_products']['image'])) {
                        $image = $product_data['image'];

                        if (!empty($product_data['option_value']) && !empty($template->data['config']['order_products']['option_image'])) {
                            foreach($product_data['option_value'] as $product_option_value) {
                                if ($product_option_value['image']) {
                                    $image = $product_option_value['image'];
                                    break;
                                }
                            }
                        }

                        if ($image) {
                            $image_width = isset($template->data['config']['order_products']['image_width']) ? $template->data['config']['order_products']['image_width']: 100;
                            $image_height = isset($template->data['config']['order_products']['image_height']) ? $template->data['config']['order_products']['image_height']: 100;

                            if ($image_width && $image_height) {
                                $image = $this->model_tool_image->resize($image, $image_width, $image_height);
                                if (substr($image, 0, 4) != 'http') {
                                    $image = $order_info['store_url'] . ltrim($image, '/');
                                }
                            }
                        }
                    }

                    $url = $this->url->link('product/product', 'product_id='.$order_product['product_id']);

                    // Products Stock
                    /*if (!isset($products_stock[$order_product['product_id']]['stock_quantity'])) {
                        $products_stock[$order_product['product_id']]['stock_quantity'] = (int)$product_data['quantity'];
                    }

                    if ($product_data['subtract']) {
                        $products_stock[$order_product['product_id']]['stock_quantity'] -= (int)$product['quantity'];
                    }*/

                    if ($product_data['quantity'] <= 0) {
                        $stock_status = $product_data['stock_status'];
                    } elseif ($this->config->get('config_stock_display')) {
                        $stock_status = $product_data['quantity'];
                    } else {
                        $stock_status = $this->language->get('text_instock');
                    }

                    $template->data['products'][$i] = array_merge($template->data['products'][$i], array(
                        'url' => $url,
                        'image' => !empty($image) ? $image : '',
                        'weight' => ($product_data['weight'] > 0) ? $this->weight->format($product_data['weight'], $product_data['weight_class_id']) : 0,
                        'manufacturer' => $product_data['manufacturer'],
                        'stock_status' => $stock_status
                    ));

                    if (!empty($template->data['config']['order_products']['description']) && $product_data['description']) {
                        $template->data['products'][$i]['description'] = utf8_substr(strip_tags(html_entity_decode($product_data['description'], ENT_QUOTES, 'UTF-8')), 0, 200) . '..';
                    }

                    if (!empty($template->data['config']['order_products']['model'])) {
                        $template->data['products'][$i]['model'] = $product_data['model'];
                    } elseif (isset($template->data['products'][$i]['model'])) {
                        unset($template->data['products'][$i]['model']);
                    }

                    if (!empty($template->data['config']['order_products']['sku'])) {
                        $template->data['products'][$i]['sku'] = $product_data['sku'];
                    } elseif (isset($template->data['products'][$i]['sku'])) {
                        unset($template->data['products'][$i]['sku']);
                    }

                    if (!empty($template->data['config']['order_products']['rating'])) {
                        $template->data['products'][$i]['rating'] = $product_data['rating'];
                        $template->data['products'][$i]['reviews'] = $product_data['reviews'];
                    }
                }
            }

            // Products Stock
            /*foreach ($template->data['products'] as $i => $product) {
                if (isset($products_stock[$product['product_id']]['stock_quantity'])) {
                    $template->data['products'][$i]['stock_quantity'] = $products_stock[$product['product_id']]['stock_quantity'];
                }
            }*/

            $template->data['order_comment'] = nl2br($order_info['comment']);

            if ($order_info['comment'] != $comment) {
                $template->data['comment'] = nl2br($order_info['comment']);

                $template->data['instruction'] = nl2br($comment);
            } else {
                $template->data['comment'] = nl2br($comment);

                $template->data['instruction'] = '';
            }

            $template->data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));

            $template->data['date_modified'] = date($this->language->get('date_format_short'), strtotime($order_info['date_modified']));

            if ($order_info['invoice_no']) {
                $template->data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
            } else {
                $template->data['invoice_no'] = '';
            }

            $template->data['order_total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value']);

            $template->data['order_subject_products'] = '';

            if (!empty($template->data['products'])) {
                foreach ($template->data['products'] as $order_product) {
                    if (strpos($template->data['order_subject_products'], $order_product['name']) === false) {
                        $template->data['order_subject_products'] .= ($template->data['order_subject_products'] ? ', ' : '') . strip_tags($order_product['name']);
                    }
                }
            }

            if (!empty($template->data['vouchers'])) {
                foreach ($template->data['vouchers'] as $order_voucher) {
                    if (strpos($template->data['order_subject_products'], $order_voucher['description']) === false) {
                        $template->data['order_subject_products'] .= ($template->data['order_subject_products'] ? ', ' : '') . strip_tags($order_voucher['description']);
                    }
                }
            }

            if ($template->data['order_subject_products']) {
                $template->data['order_subject_products'] = trim(html_entity_decode($template->data['order_subject_products'], ENT_QUOTES, 'UTF-8'));

                $length = 32;

                if (strlen($template->data['order_subject_products']) > $length) {
                    $template->data['order_subject_products'] = substr($template->data['order_subject_products'], 0, strrpos(substr($template->data['order_subject_products'], 0, $length), ' ')) . '...';
                }
            }

            if ($order_info['customer_id']) {
                $template->data['order_link'] = $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_info['order_id'];

                if ($this->language->get('button_order_link') && $this->language->get('button_order_link') != 'button_order_link') {
                    $template->data['order_link_text'] = $this->language->get('button_order_link');
                } else {
                    $template->data['order_link_text'] = $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_info['order_id'];
                }
            }

            if ($download_status) {
                $template->data['download_link'] = $order_info['store_url'] . 'index.php?route=account/download';

                if ($this->language->get('button_download_link') && $this->language->get('button_download_link') != 'button_download_link') {
                    $template->data['download_link_text'] = $this->language->get('button_download_link');
                } else {
                    $template->data['download_link_text'] = $template->data['download_link'];
                }
            }
		    // Prepared mail: order.customer
		}
		
		$mail = new Mail($this->config->get('config_mail_engine'));
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

		$mail->setTo($order_info['email']);
		$mail->setFrom($from);
		$mail->setSender(html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
		$mail->setSubject(html_entity_decode(sprintf($language->get('text_subject'), $order_info['store_name'], $order_info['order_id']), ENT_QUOTES, 'UTF-8'));
		if (empty($template)) $mail->setHtml($this->load->view('mail/order_add', $data));
		
		// Send mail: order.customer
		if ($template && $template->check()) {
		    $template->build();
		    $template->hook($mail);
        }

		$mail->send();

		$this->model_extension_module_emailtemplate->sent();
	}
	
	public function edit($order_info, $order_status_id, $comment) {
		$language = new Language($order_info['language_code']);
		$language->load($order_info['language_code']);
		$language->load('mail/order_edit');

		$data['text_order_id'] = $language->get('text_order_id');
		$data['text_date_added'] = $language->get('text_date_added');
		$data['text_order_status'] = $language->get('text_order_status');
		$data['text_link'] = $language->get('text_link');
		$data['text_comment'] = $language->get('text_comment');
		$data['text_footer'] = $language->get('text_footer');

		$data['order_id'] = $order_info['order_id'];
		$data['date_added'] = date($language->get('date_format_short'), strtotime($order_info['date_added']));
		
		$order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order_info['language_id'] . "'");
	
		if ($order_status_query->num_rows) {
			$data['order_status'] = $order_status_query->row['name'];
		} else {
			$data['order_status'] = '';
		}

		if ($order_info['customer_id']) {
			$data['link'] = $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_info['order_id'];
		} else {
			$data['link'] = '';
		}

		$data['comment'] = strip_tags($comment);

		$this->load->model('setting/setting');
		
		$from = $this->model_setting_setting->getSettingValue('config_email', $order_info['store_id']);
		
		if (!$from) {
			$from = $this->config->get('config_email');
		}
		
		$mail = new Mail($this->config->get('config_mail_engine'));

		// Prepare mail: order.update
		$this->load->model('extension/module/emailtemplate');

		$language->load('extension/module/emailtemplate/order');

		$template_load = array(
			'key' => 'order.update',
			'customer_id' => $order_info['customer_id'],
            'customer_group_id' => $order_info['customer_group_id'],
            'language_id' => $order_info['language_id'],
            'order_status_id' => $order_status_id,
            'store_id' => $order_info['store_id'],
            'payment_method' => $order_info['payment_code'],
            'shipping_method' => $order_info['shipping_code']
		);

		if (!empty($this->request->post['emailtemplate_id'])) {
			$template_load['emailtemplate_id'] = $this->request->post['emailtemplate_id'];
		}

		$template_filter = array();

		if (!empty($order_info['customer_id']) && !isset($total_customer_orders)) {
			$total_customer_orders_query = $this->db->query("SELECT count(1) as total FROM `" . DB_PREFIX . "order` WHERE customer_id = '" . (int)$order_info['customer_id'] . "'");

			if ($total_customer_orders_query->row) {
				$total_customer_orders = $total_customer_orders_query->row['total'];
				$template_filter['total_customer_orders'] = $total_customer_orders;
			}
		}

		$template = $this->model_extension_module_emailtemplate->load($template_load, $data, $template_filter);

        if ($template) {
            $template->addData($order_info);

            if (!empty($order_info['customer_group_id'])) {
                $this->load->model('account/customer_group');

                $customer_group = $this->model_account_customer_group->getCustomerGroup($order_info['customer_group_id']);

				if ($customer_group) {
					$template->data['customer_group'] = array(
						'name' => $customer_group['name'],
						'description' => $customer_group['description']
					);
				}
            }

            if (!empty($order_info['affiliate_id']) && file_exists(DIR_APPLICATION . 'model/account/affiliate.php')) {
                $this->load->model('account/affiliate');

                $template->data['affiliate'] = $this->model_account_affiliate->getAffiliate($order_info['affiliate_id']);
            }

            $template->data['order_status'] = $data['order_status'];

            $template->data['order_status_id'] = $order_status_id;

            $template->data['total_customer_orders'] = isset($total_customer_orders) ? $total_customer_orders : 0;

            $template->data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));

            $template->data['date_modified'] = date($this->language->get('date_format_short'), strtotime($order_info['date_modified']));

            $template->data['date_relative_hours'] = floor((time() - strtotime($order_info['date_added'])) / 3600);

            if ($order_info['invoice_no']) {
                $template->data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
            } else {
                $template->data['invoice_no'] = '';
            }

            if ($order_info['order_status_id'] != $order_status_id){
                $template->data['prev_order_status_id'] = $order_info['order_status_id'];
            }

            $template->data['order_total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value']);

            $comment = html_entity_decode($comment, ENT_QUOTES, 'UTF-8');

            $template->data['comment'] = (trim(strip_tags($comment)) != '') ? $comment : '';

            if ($order_info['comment']) {
                $template->data['order_comment'] = nl2br($order_info['comment']);
            }

            if ($order_info['comment'] != $comment) {
                $template->data['instruction'] = nl2br($comment);
            } else {
                $template->data['instruction'] = '';
            }

            if ($order_info['customer_id']) {
                $template->data['order_link'] = $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_info['order_id'];

                if (!empty($template->data['button_order_link'])) {
                    $template->data['order_link_text'] = $template->data['button_order_link'];
                } else {
                    $template->data['order_link_text'] = $template->data['order_link'];
                }
            }

            // Custom fields
            if (!empty($order_info['custom_field']) || !empty($order_info['payment_custom_field'])) {
                $this->load->model('account/custom_field');

				// Cleanup
				foreach(array('custom_field', 'payment_custom_field', 'shipping_custom_field') as $var) {
					if (isset($template->data[$var])) {
						unset($template->data[$var]);
					}
				}

                if (!empty($order_info['customer_group_id'])) {
                    $customer_group_id = $order_info['customer_group_id'];
                } else {
                    $customer_group_id = $this->config->get('config_customer_group_id');
                }

                $custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

                foreach ($custom_fields as $custom_field) {
                    if (isset($order_info['payment_custom_field'][$custom_field['custom_field_id']])) {
                        $template->data['custom_field_' . $custom_field['custom_field_id'] . '_name'] = $custom_field['name'];

                        if ($custom_field['custom_field_value']) {
                            foreach ($custom_field['custom_field_value'] as $custom_field_value) {
                                if (is_array($order_info['payment_custom_field'][$custom_field['custom_field_id']])) {
                                    if (in_array($custom_field_value['custom_field_value_id'], $order_info['payment_custom_field'][$custom_field['custom_field_id']])) {
                                        $template->data['custom_field_' . $custom_field['custom_field_id'] . '_value_' . $custom_field_value['custom_field_value_id']] = $custom_field_value['name'];
                                    }
                                } else {
                                    if ($custom_field_value['custom_field_value_id'] == $order_info['payment_custom_field'][$custom_field['custom_field_id']]) {
                                        $template->data['custom_field_' . $custom_field['custom_field_id'] . '_value'] = $custom_field_value['name'];
                                    }
                                }
                            }
                        } else {
                            $template->data['custom_field_' . $custom_field['custom_field_id'] . '_value'] = $order_info['payment_custom_field'][$custom_field['custom_field_id']];
                        }
                    } elseif (isset($order_info['custom_field'][$custom_field['custom_field_id']])) {
                        $template->data['custom_field_' . $custom_field['custom_field_id'] . '_name'] = $custom_field['name'];

                        if ($custom_field['custom_field_value']) {
                            foreach ($custom_field['custom_field_value'] as $custom_field_value) {
                                if (is_array($order_info['custom_field'][$custom_field['custom_field_id']])) {
                                    if (in_array($custom_field_value['custom_field_value_id'], $order_info['custom_field'][$custom_field['custom_field_id']])) {
                                        $template->data['custom_field_' . $custom_field['custom_field_id'] . '_value_' . $custom_field_value['custom_field_value_id']] = $custom_field_value['name'];
                                    }
                                } else {
                                    if ($custom_field_value['custom_field_value_id'] == $order_info['custom_field'][$custom_field['custom_field_id']]) {
                                        $template->data['custom_field_' . $custom_field['custom_field_id'] . '_value'] = $custom_field_value['name'];
                                    }
                                }
                            }
                        } else {
                            $template->data['custom_field_' . $custom_field['custom_field_id'] . '_value'] = $order_info['custom_field'][$custom_field['custom_field_id']];
                        }
                    }
                }
            }

			// Address
            foreach(array('payment', 'shipping') as $var) {
                if ($order_info[$var . '_address_format']) {
                    $format = $order_info[$var . '_address_format'];
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
                    'firstname' => $order_info[$var . '_firstname'],
                    'lastname'  => $order_info[$var . '_lastname'],
                    'company'   => $order_info[$var . '_company'],
                    'address_1' => $order_info[$var . '_address_1'],
                    'address_2' => $order_info[$var . '_address_2'],
                    'city'      => $order_info[$var . '_city'],
                    'postcode'  => $order_info[$var . '_postcode'],
                    'zone'      => $order_info[$var . '_zone'],
                    'zone_code' => $order_info[$var . '_zone_code'],
                    'country'   => $order_info[$var . '_country']
                );

                if (isset($order_info['custom_field'])) {
                    foreach ($order_info['custom_field'] as $custom_field_id => $custom_field) {
                        $find[] = '{custom_field_' . $custom_field_id . '}';
                        $replace[] = isset($custom_field['value']) ? $custom_field['value'] : '';
                    }
                }

                if (!empty($order_info[$var . '_custom_field']) && is_array($order_info[$var . '_custom_field'])) {
                    foreach ($order_info[$var . '_custom_field'] as $custom_field_id => $custom_field) {
                        $find[] = '{custom_field_' . $custom_field_id . '}';
                        $replace[] = isset($custom_field['value']) ? $custom_field['value'] : $custom_field;
                    }
                }

                $template->data[$var . '_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
            }

            $template->data['products'] = array();

			$order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_info['order_id'] . "'");

			$this->load->model('tool/image');
			$this->load->model('tool/upload');
			$this->load->model('catalog/product');

			foreach ($order_product_query->rows as $product) {
				$product_data = $this->model_catalog_product->getProduct($product['product_id']);

				$order_product = array(
					'product_id' => $product['product_id'],
                    'name' => $product['name'],
                    'model' => $product['model'],
                    'quantity' => $product['quantity'],
                    'price' => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
                    'total' => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
                    'option' => '',
                    'url' => '',
                    'image' => '',
                    'weight' => 0,
                    'description' => '',
                    'manufacturer' => '',
                    'sku' => '',
                    'stock_status' => ''
				);

				if ($product_data) {
					$option_data = array();

					$order_option_query = $this->db->query("SELECT oo.*, pov.* FROM " . DB_PREFIX . "order_option oo LEFT JOIN " . DB_PREFIX . "product_option_value pov ON (pov.product_option_value_id = oo.product_option_value_id) WHERE oo.order_id = '" . (int)$order_info['order_id'] . "' AND oo.order_product_id = '" . (int)$product['order_product_id'] . "'");

					foreach ($order_option_query->rows as $option) {
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

						if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option['price']) {
							$price = $this->currency->format($this->tax->calculate($option['price'], $product_data['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
						} else {
							$price = 0;
						}

						$option_data[] = array(
							'name'  => $option['name'],
							'price' => $price,
							'price_prefix' => isset($option['price_prefix']) ? $option['price_prefix'] : '',
							'value' => (utf8_strlen($value) > 120 ? utf8_substr($value, 0, 120) . '..' : $value)
						);
					}

					if (isset($product_data['image'])) {
						$image_width = isset($template->data['config']['order_products']['image_width']) ? $template->data['config']['order_products']['image_width']: 100;
						$image_height = isset($template->data['config']['order_products']['image_height']) ? $template->data['config']['order_products']['image_height']: 100;

						if ($image_width && $image_height) {
							$image = $this->model_tool_image->resize($product_data['image'], $image_width, $image_height);

							if (substr($image, 0, 4) != 'http') {
								$image = $order_info['store_url'] . ltrim($image, '/');
							}
						}
					} else {
						$image = '';
					}

					$order_product['option'] = $option_data;
					$order_product['image'] = $image;
					$order_product['url'] = $this->url->link('product/product', 'product_id='.$product['product_id'], true);
					if ($product_data['weight'] > 0) {
						$order_product['weight'] = $this->weight->format($product_data['weight'], $product_data['weight_class_id']);
					}
					$order_product['description'] = utf8_substr(strip_tags(html_entity_decode($product_data['description'], ENT_QUOTES, 'UTF-8')), 0, 200) . '..';
					$order_product['manufacturer'] = $product_data['manufacturer'];
					$order_product['sku'] = $product_data['sku'];
					$order_product['stock_status'] = $product_data['stock_status'];
				}

				$template->data['products'][] = $order_product;
			}

			$template->data['vouchers'] = array();

			$order_voucher_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = " . (int)$order_info['order_id']);

			foreach ($order_voucher_query->rows as $voucher) {
				$template->data['vouchers'][] = array(
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']),
				);
			}

			$order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_info['order_id'] . "' ORDER BY sort_order ASC");

			foreach ($order_total_query->rows as $total) {
				$template->data['totals'][] = array(
					'title' => $total['title'],
					'text'  => !empty($total['text']) ? $total['text'] : $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
				);
			}
		    // Prepared mail: order.update
        }
		
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

		$mail->setTo($order_info['email']);
		$mail->setFrom($from);
		$mail->setSender(html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
		$mail->setSubject(html_entity_decode(sprintf($language->get('text_subject'), $order_info['store_name'], $order_info['order_id']), ENT_QUOTES, 'UTF-8'));
		$mail->setText($this->load->view('mail/order_edit', $data));
		// Send mail: order.update
		if ($template && $template->check()) {
		    $template->build();
		    $template->hook($mail);
        }

		$mail->send();

		$this->model_extension_module_emailtemplate->sent();
	}
	
	// Admin Alert Mail
	public function alert(&$route, &$args) {
		if (isset($args[0])) {
			$order_id = $args[0];
		} else {
			$order_id = 0;
		}
		
		if (isset($args[1])) {
			$order_status_id = $args[1];
		} else {
			$order_status_id = 0;
		}	
		
		if (isset($args[2])) {
			$comment = $args[2];
		} else {
			$comment = '';
		}
		
		if (isset($args[3])) {
			$notify = $args[3];
		} else {
			$notify = '';
		}

		$order_info = $this->model_checkout_order->getOrder($order_id);
		
		if ($order_info && !$order_info['order_status_id'] && $order_status_id && in_array('order', (array)$this->config->get('config_mail_alert'))) {	
			$this->load->language('mail/order_alert');
			
			// HTML Mail
			$data['text_received'] = $this->language->get('text_received');
			$data['text_order_id'] = $this->language->get('text_order_id');
			$data['text_date_added'] = $this->language->get('text_date_added');
			$data['text_order_status'] = $this->language->get('text_order_status');
			$data['text_product'] = $this->language->get('text_product');
			$data['text_total'] = $this->language->get('text_total');
			$data['text_comment'] = $this->language->get('text_comment');
			
			$data['order_id'] = $order_info['order_id'];
			$data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));

			$order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

			if ($order_status_query->num_rows) {
				$data['order_status'] = $order_status_query->row['name'];
			} else {
				$data['order_status'] = '';
			}

			$this->load->model('tool/upload');
			
			$data['products'] = array();

			$order_products = $this->model_checkout_order->getOrderProducts($order_id);

			foreach ($order_products as $order_product) {
				$option_data = array();
				
				$order_options = $this->model_checkout_order->getOrderOptions($order_info['order_id'], $order_product['order_product_id']);
				
				foreach ($order_options as $order_option) {
					if ($order_option['type'] != 'file') {
						$value = $order_option['value'];
					} else {
						$upload_info = $this->model_tool_upload->getUploadByCode($order_option['value']);
	
						if ($upload_info) {
							$value = $upload_info['name'];
						} else {
							$value = '';
						}
					}

					$option_data[] = array(
					'order_option_id'  => $order_option['order_option_id'],
						'name'  => $order_option['name'],
						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
					);					
				}
					
				$data['products'][] = array(
				'product_id'  => $order_product['product_id'],
				'order_product_id'  => $order_product['order_product_id'],
					'name'     => $order_product['name'],
					'model'    => $order_product['model'],
					'quantity' => $order_product['quantity'],
					'option'   => $option_data,
					'total'    => html_entity_decode($this->currency->format($order_product['total'] + ($this->config->get('config_tax') ? ($order_product['tax'] * $order_product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']), ENT_NOQUOTES, 'UTF-8')
				);
			}
			
			$data['vouchers'] = array();
			
			$order_vouchers = $this->model_checkout_order->getOrderVouchers($order_id);

			foreach ($order_vouchers as $order_voucher) {
				$data['vouchers'][] = array(
					'description' => $order_voucher['description'],
					'amount'      => html_entity_decode($this->currency->format($order_voucher['amount'], $order_info['currency_code'], $order_info['currency_value']), ENT_NOQUOTES, 'UTF-8')
				);					
			}

			$data['totals'] = array();
			
			$order_totals = $this->model_checkout_order->getOrderTotals($order_id);

			foreach ($order_totals as $order_total) {
				$data['totals'][] = array(
					'title' => $order_total['title'],
					'value' => html_entity_decode($this->currency->format($order_total['value'], $order_info['currency_code'], $order_info['currency_value']), ENT_NOQUOTES, 'UTF-8')
				);
			}

			$data['comment'] = strip_tags($order_info['comment']);


			// Prepare mail: order.admin
			$this->load->language('product/product'); // required for stock status
			$this->load->language('mail/order_alert');
			$this->load->language('extension/module/emailtemplate/order');

			$this->load->model('tool/image');
            $this->load->model('catalog/product');
            $this->load->model('extension/module/emailtemplate');

			$template_load = array(
				'key' => 'order.admin',
				'customer_id' => $order_info['customer_id'],
				'customer_group_id' => $order_info['customer_group_id'],
				'language_id' => $order_info['language_id'],
				'order_status_id' => $order_status_id,
				'store_id' => $order_info['store_id'],
                'payment_method' => $order_info['payment_code'],
                'shipping_method' => $order_info['shipping_code']
			);

			$template_filter = array();

			if (!empty($order_info['customer_id']) && !isset($total_customer_orders)) {
				$total_customer_orders_query = $this->db->query("SELECT count(1) as total FROM `" . DB_PREFIX . "order` WHERE customer_id = '" . (int)$order_info['customer_id'] . "'");

				if ($total_customer_orders_query->row) {
					$total_customer_orders = $total_customer_orders_query->row['total'];

					$template_filter['total_customer_orders'] = $total_customer_orders;
				}
			}

			$template = $this->model_extension_module_emailtemplate->load($template_load, $data, $template_filter);

            if ($template) {
                $template->addData($order_info);

                $template->data['total_customer_orders'] = isset($total_customer_orders) ? $total_customer_orders : 0;

                $template->data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));

                $template->data['date_modified'] = date($this->language->get('date_format_short'), strtotime($order_info['date_modified']));

                if ($order_info['invoice_no']) {
                    $template->data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
                } else {
                    $template->data['invoice_no'] = '';
                }

                // Address
                foreach(array('payment', 'shipping') as $var) {
                    if ($order_info[$var . '_address_format']) {
                        $format = $order_info[$var . '_address_format'];
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
                        'firstname' => $order_info[$var . '_firstname'],
                        'lastname'  => $order_info[$var . '_lastname'],
                        'company'   => $order_info[$var . '_company'],
                        'address_1' => $order_info[$var . '_address_1'],
                        'address_2' => $order_info[$var . '_address_2'],
                        'city'      => $order_info[$var . '_city'],
                        'postcode'  => $order_info[$var . '_postcode'],
                        'zone'      => $order_info[$var . '_zone'],
                        'zone_code' => $order_info[$var . '_zone_code'],
                        'country'   => $order_info[$var . '_country']
                    );

                    if (isset($order_info['custom_field'])) {
                        foreach ($order_info['custom_field'] as $custom_field_id => $custom_field) {
                            $find[] = '{custom_field_' . $custom_field_id . '}';
                            $replace[] = isset($custom_field['value']) ? $custom_field['value'] : '';
                        }
                    }

                    if (!empty($order_info[$var . '_custom_field']) && is_array($order_info[$var . '_custom_field'])) {
                        foreach ($order_info[$var . '_custom_field'] as $custom_field_id => $custom_field) {
                            $find[] = '{custom_field_' . $custom_field_id . '}';
                            $replace[] = isset($custom_field['value']) ? $custom_field['value'] : $custom_field;
                        }
                    }

                    $template->data[$var . '_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
                }

                if (!empty($order_info['customer_group_id'])) {
                    $this->load->model('account/customer_group');

                    $customer_group = $this->model_account_customer_group->getCustomerGroup($order_info['customer_group_id']);

                    if ($customer_group) {
                        $template->data['customer_group'] = array(
                            'name' => $customer_group['name'],
                            'description' => $customer_group['description']
                        );
                    }
                }

                if (!empty($order_info['affiliate_id']) && file_exists(DIR_APPLICATION . 'model/account/affiliate.php')) {
                    $this->load->model('account/affiliate');

                    $affiliate_info = $this->model_account_affiliate->getAffiliate($order_info['affiliate_id']);

                    if ($affiliate_info) {
                        $template->data['affiliate'] = $affiliate_info;
                    }
                }

                // Custom fields
                if (!empty($order_info['custom_field']) || !empty($order_info['payment_custom_field'])) {
                    $this->load->model('account/custom_field');

                    // Cleanup
					foreach(array('custom_field', 'payment_custom_field', 'shipping_custom_field') as $var) {
						if (isset($template->data[$var])) {
							unset($template->data[$var]);
						}
					}

                    if (!empty($order_info['customer_group_id'])) {
                        $customer_group_id = $order_info['customer_group_id'];
                    } else {
                        $customer_group_id = $this->config->get('config_customer_group_id');
                    }

                    $custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

                    foreach ($custom_fields as $custom_field) {
                        if (isset($order_info['payment_custom_field'][$custom_field['custom_field_id']])) {
                            $template->data['custom_field_' . $custom_field['custom_field_id'] . '_name'] = $custom_field['name'];

                            if ($custom_field['custom_field_value']) {
                                foreach ($custom_field['custom_field_value'] as $custom_field_value) {
                                    if (is_array($order_info['payment_custom_field'][$custom_field['custom_field_id']])) {
                                        if (in_array($custom_field_value['custom_field_value_id'], $order_info['payment_custom_field'][$custom_field['custom_field_id']])) {
                                            $template->data['custom_field_' . $custom_field['custom_field_id'] . '_value_' . $custom_field_value['custom_field_value_id']] = $custom_field_value['name'];
                                        }
                                    } else {
                                        if ($custom_field_value['custom_field_value_id'] == $order_info['payment_custom_field'][$custom_field['custom_field_id']]) {
                                            $template->data['custom_field_' . $custom_field['custom_field_id'] . '_value'] = $custom_field_value['name'];
                                        }
                                    }
                                }
                            } else {
                                $template->data['custom_field_' . $custom_field['custom_field_id'] . '_value'] = $order_info['payment_custom_field'][$custom_field['custom_field_id']];
                            }
                        } elseif (isset($order_info['custom_field'][$custom_field['custom_field_id']])) {
                            $template->data['custom_field_' . $custom_field['custom_field_id'] . '_name'] = $custom_field['name'];

                            if ($custom_field['custom_field_value']) {
                                foreach ($custom_field['custom_field_value'] as $custom_field_value) {
                                    if (is_array($order_info['custom_field'][$custom_field['custom_field_id']])) {
                                        if (in_array($custom_field_value['custom_field_value_id'], $order_info['custom_field'][$custom_field['custom_field_id']])) {
                                            $template->data['custom_field_' . $custom_field['custom_field_id'] . '_value_' . $custom_field_value['custom_field_value_id']] = $custom_field_value['name'];
                                        }
                                    } else {
                                        if ($custom_field_value['custom_field_value_id'] == $order_info['custom_field'][$custom_field['custom_field_id']]) {
                                            $template->data['custom_field_' . $custom_field['custom_field_id'] . '_value'] = $custom_field_value['name'];
                                        }
                                    }
                                }
                            } else {
                                $template->data['custom_field_' . $custom_field['custom_field_id'] . '_value'] = $order_info['custom_field'][$custom_field['custom_field_id']];
                            }
                        }
                    }
                }

                $order_status_query = $this->db->query("SELECT `name` FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order_info['language_id'] . "' LIMIT 1");

                if ($order_status_query->row) {
                    $order_status = $order_status_query->row['name'];

                    $template->data['order_status'] = $order_status;
                }

                    if ($this->config->get('config_processing_status') && in_array($order_status_id, $this->config->get('config_processing_status'))) {
                        $template->data['order_processing_status'] = true;
                    } elseif ($this->config->get('config_complete_status') && in_array($order_status_id, $this->config->get('config_complete_status'))) {
                        $template->data['order_complete_status'] = true;
                    }

                $template->data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));

                if (defined('HTTP_ADMIN')) {
                    $admin_url = HTTP_ADMIN;
                } elseif (!empty($order_info['store_ssl'])) {
                    $admin_url = $order_info['store_ssl'] . 'admin/';
                } else {
                    $admin_url = $order_info['store_url'] . 'admin/';
                }

                $template->data['admin_order_link'] = $admin_url . 'index.php?route=sale/order/info&order_id=' . $order_info['order_id'];

                if ($this->language->get('button_order_link') && $this->language->get('button_order_link') != 'button_order_link') {
                    $template->data['admin_order_link_text'] =  $this->language->get('button_order_link');
                } else {
                    $template->data['admin_order_link_text'] =  $template->data['admin_order_link'];
                }

                if (!empty($order_info['weight'])) {
                    $template->data['order_weight'] = $this->weight->format($order_info['weight'], $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point'));
                }

                $template->data['order_comment'] = nl2br($order_info['comment']);

                if ($order_info['comment'] != $comment) {
                    $template->data['comment'] = nl2br($order_info['comment']);

                    $template->data['instruction'] = nl2br($comment);
                } else {
                    $template->data['comment'] = nl2br($comment);

                    $template->data['instruction'] = '';
                }

                $template->data['order_total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value']);

                $products_stock = array();

                if (!empty($template->data['config']['order_products']['option_length'])) {
                    $option_length = $template->data['config']['order_products']['option_length'];
                } else {
                    $option_length = 120;
                }

                // Add extra data to products array
                $template->data['products'] = $data['products'];

                foreach ($template->data['products'] as $i => $product) {
                    $order_product = false;
                    foreach ($order_products as $row) {
                        if ($product['order_product_id'] == $row['order_product_id']) {
                            $order_product = $row;
                            break;
                        }
                    }

                    $product_data = $this->model_catalog_product->getProduct($order_product['product_id']);

                    if ($product_data) {
                        if ($product['option']) {
                            $order_options = $this->model_checkout_order->getOrderOptions($order_info['order_id'], $order_product['order_product_id']);

                            foreach ($product['option'] as $ii => $product_option) {
                                $order_option = false;
                                foreach ($order_options as $order_option) {
                                    if ($order_option['order_option_id'] == $product_option['order_option_id']) {
                                        break;
                                    }
                                }

                                if ($order_option && $order_option['type'] != 'file') {
                                    $value = $order_option['value'];
                                    $template->data['products'][$i]['option'][$ii]['value'] = utf8_strlen($value) > $option_length ? utf8_substr($value, 0, $option_length) . '..' : $value;
                                }
                            }

                            $product_data['option_value'] = array();

                            $order_option_query = $this->db->query("SELECT oo.* FROM " . DB_PREFIX . "order_option oo WHERE oo.order_product_id = '" . (int)$product['order_product_id'] . "' AND oo.order_id = '" . (int)$order_info['order_id'] . "'");

                            if ($order_option_query->rows) {
                                foreach ($order_option_query->rows as $order_option) {
                                    $order_option_value_query = $this->db->query("SELECT pov.*, ov.image FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) WHERE pov.product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "'");

                                    if ($order_option_value_query->rows) {
                                        foreach ($order_option_value_query->rows as $order_option_value) {
                                            $product_data['option_value'][] = array(
                                                'order_option_id' => $order_option['order_option_id'],
                                                'product_option_value_id' => $order_option['product_option_value_id'],
                                                'type' => $order_option['type'],
                                                'image' => $order_option_value['image'],
                                                'price' => $order_option_value['price'],
                                                'price_prefix' => $order_option_value['price_prefix'],
                                                'stock_quantity' => $order_option_value['quantity'],
                                                'value' => $order_option['value']
                                            );
                                        }
                                    }
                                }
                            }
                        }

                        $image = '';

                        if (!empty($template->data['config']['order_products']['image'])) {
                            $image = $product_data['image'];

                            if (!empty($product_data['option_value']) && !empty($template->data['config']['order_products']['option_image'])) {
                                foreach($product_data['option_value'] as $product_option_value) {
                                    if ($product_option_value['image']) {
                                        $image = $product_option_value['image'];
                                        break;
                                    }
                                }
                            }

                            if ($image) {
                                $image_width = isset($template->data['config']['order_products']['image_width']) ? $template->data['config']['order_products']['image_width']: 100;
                                $image_height = isset($template->data['config']['order_products']['image_height']) ? $template->data['config']['order_products']['image_height']: 100;

                                if ($image_width && $image_height) {
                                    $image = $this->model_tool_image->resize($image, $image_width, $image_height);
                                    if (substr($image, 0, 4) != 'http') {
                                        $image = $order_info['store_url'] . ltrim($image, '/');
                                    }
                                }
                            }
                        }

                        $url = $this->url->link('product/product', 'product_id='.$order_product['product_id']);

                        if (!empty($template->data['config']['order_products']['admin_stock'])) {
                            if (!isset($products_stock[$order_product['product_id']]['stock_quantity'])) {
                                $products_stock[$order_product['product_id']]['stock_quantity'] = (int)$product_data['quantity'];
                            }

                            if ($product_data['subtract']) {
                                $products_stock[$order_product['product_id']]['stock_quantity'] -= (int)$product['quantity'];
                            }
                        }

                        if (!empty($product_data['option_value']) && $template->data['products'][$i]['option']) {
                            foreach ($product_data['option_value'] as $product_option_value) {
                                if ($product_option_value['stock_quantity']) {
                                    foreach($template->data['products'][$i]['option'] as $ii => $product_option) {
                                        if ($product_option_value['order_option_id'] == $product_option['order_option_id']) {
                                            if ($product_option_value['type'] != 'file') {
                                                $value = $product_option_value['value'];
                                            } else {
                                                $value = utf8_substr($product_option_value['value'], 0, utf8_strrpos($product_option_value['value'], '.'));
                                            }

                                            $template->data['products'][$i]['option'][$ii] = array_merge($template->data['products'][$i]['option'][$ii], array(
                                                'value' => utf8_strlen($value) > $option_length ? utf8_substr($value, 0, $option_length) . '..' : $value,
                                                'price' => (float)$product_option_value['price'] ? $this->currency->format($this->tax->calculate($product_option_value['price'], $product_data['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']) : 0,
                                                'price_value' => (float)$product_option_value['price'],
                                                'price_prefix' => isset($product_option_value['price_prefix']) ? $product_option_value['price_prefix'] : '',
                                                'stock_quantity' => isset($product_option_value['quantity']) ? ((int)$product_option_value['quantity'] - (int)$product['quantity']) : '',
                                                'stock_subtract' => isset($product_option_value['subtract']) ? $product_option_value['subtract'] : ''
                                            ));

                                            break 2;
                                        }
                                    }
                                }
                            }
                        }

                        if ($product_data['quantity'] <= 0) {
                            $stock_status = $product_data['stock_status'];
                        } elseif ($this->config->get('config_stock_display')) {
                            $stock_status = $product_data['quantity'];
                        } else {
                            $stock_status = $this->language->get('text_instock');
                        }

                        $template->data['products'][$i] = array_merge($template->data['products'][$i], array(
                            'product_id' => $product_data['product_id'],
                            'url' => $url,
                            'image' => !empty($image) ? $image : '',
                            'weight' => ($product_data['weight'] > 0) ? $this->weight->format($product_data['weight'], $product_data['weight_class_id']) : 0,
                            'price'    => $this->currency->format($order_product['price'] + ($this->config->get('config_tax') ? $order_product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
                            'manufacturer' => $product_data['manufacturer'],
                            'stock_status' => $stock_status
                        ));

                        if (!empty($template->data['config']['order_products']['model'])) {
                            $template->data['products'][$i]['model'] = $product_data['model'];
                        } elseif (isset($template->data['products'][$i]['model'])) {
                            unset($template->data['products'][$i]['model']);
                        }

                        if (!empty($template->data['config']['order_products']['sku'])) {
                            $template->data['products'][$i]['sku'] = $product_data['sku'];
                        } elseif (isset($template->data['products'][$i]['sku'])) {
                            unset($template->data['products'][$i]['sku']);
                        }

                        if (!empty($template->data['config']['order_products']['description']) && $product_data['description']) {
                            $template->data['products'][$i]['description'] = utf8_substr(strip_tags(html_entity_decode($product_data['description'], ENT_QUOTES, 'UTF-8')), 0, 200) . '..';
                        }
                    }
                }

                // Products Stock
                if (!empty($template->data['config']['order_products']['admin_stock'])) {
                    foreach ($template->data['products'] as $i => $product) {
                        if (isset($products_stock[$product['product_id']]['stock_quantity'])) {
                            $template->data['products'][$i]['stock_quantity'] = $products_stock[$product['product_id']]['stock_quantity'];
                        }
                    }
                }

                // Vouchers
                $template->data['vouchers'] = array();

                $order_vouchers = $this->model_checkout_order->getOrderVouchers($order_info['order_id']);

                foreach ($order_vouchers as $order_voucher) {
                    $template->data['vouchers'][] = array(
                        'description' => $order_voucher['description'],
                        'amount'      => $this->currency->format($order_voucher['amount'], $order_info['currency_code'], $order_info['currency_value']),
                    );
                }

                // Order Totals
                $template->data['totals'] = array();

                $order_totals = $this->model_checkout_order->getOrderTotals($order_info['order_id']);

                foreach ($order_totals as $order_total) {
                    $template->data['totals'][] = array(
                        'title' => $order_total['title'],
                        'text'  => !empty($order_total['text']) ? $order_total['text'] : $this->currency->format($order_total['value'], $order_info['currency_code'], $order_info['currency_value']),
                    );
                }

                $template->data['order_subject_products'] = '';

                if (!empty($template->data['products'])) {
                    foreach ($template->data['products'] as $order_product) {
                        if (strpos($template->data['order_subject_products'], $order_product['name']) === false) {
                            $template->data['order_subject_products'] .= ($template->data['order_subject_products'] ? ', ' : '') . strip_tags($order_product['name']);
                        }
                    }
                }

                if (!empty($template->data['vouchers'])) {
                    foreach ($template->data['vouchers'] as $order_voucher) {
                        if (strpos($template->data['order_subject_products'], $order_voucher['description']) === false) {
                            $template->data['order_subject_products'] .= ($template->data['order_subject_products'] ? ', ' : '') . strip_tags($order_voucher['description']);
                        }
                    }
                }

                if ($template->data['order_subject_products']) {
                    $template->data['order_subject_products'] = trim(html_entity_decode($template->data['order_subject_products'], ENT_QUOTES, 'UTF-8'));

                    $length = 32;

                    if (strlen($template->data['order_subject_products']) > $length) {
                        $template->data['order_subject_products'] = substr($template->data['order_subject_products'], 0, strrpos(substr($template->data['order_subject_products'], 0, $length), ' ')) . '...';
                    }
                }
			    // Prepared mail: order.admin
            }
		
			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($this->config->get('config_email'));
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode(sprintf($this->language->get('text_subject'), $this->config->get('config_name'), $order_info['order_id']), ENT_QUOTES, 'UTF-8'));
			$mail->setText($this->load->view('mail/order_alert', $data));
			// Send mail: order.admin
			if ($template && $template->check()) {
			    $template->build();
			    $template->hook($mail);
            }

			$mail->send();

			$this->model_extension_module_emailtemplate->sent();

			// Send to additional alert emails
			$emails = explode(',', $this->config->get('config_mail_alert_email'));

			foreach ($emails as $email) {
				$email = trim($email);
				if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$mail->setTo($email);
					$mail->send();
				}
			}
		}
	}
}
