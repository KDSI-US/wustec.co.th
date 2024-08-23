<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleComoorderstatus extends Controller {

    // current version
    private $version = '1.0';

    private $error = array();

    private function checkDatabase() {
        $db_check = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "order_status_como'");
        if(!$db_check->num_rows) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "order_status_como` (
                `order_status_id` int(11) NOT NULL,
                `lbcolor` VARCHAR(64) NOT NULL,
                `color` VARCHAR(64) NOT NULL,
                `bgcolor` VARCHAR(64) NOT NULL,
                PRIMARY KEY (`order_status_id`)
            )");
            $this->db->query("INSERT INTO `" . DB_PREFIX . "order_status_como` (`order_status_id`, `lbcolor`, `color`, `bgcolor`) VALUES(1, '#0066ff', '', '')");
            $this->db->query("INSERT INTO `" . DB_PREFIX . "order_status_como` (`order_status_id`, `lbcolor`, `color`, `bgcolor`) VALUES(2, '#ff00ff', '', '')");
            $this->db->query("INSERT INTO `" . DB_PREFIX . "order_status_como` (`order_status_id`, `lbcolor`, `color`, `bgcolor`) VALUES(3, '#008000', '', '')");
            $this->db->query("INSERT INTO `" . DB_PREFIX . "order_status_como` (`order_status_id`, `lbcolor`, `color`, `bgcolor`) VALUES(5, '#00a38e', '', '')");
            $this->db->query("INSERT INTO `" . DB_PREFIX . "order_status_como` (`order_status_id`, `lbcolor`, `color`, `bgcolor`) VALUES(15, '#008000', '', '')");
            $this->db->query("INSERT INTO `" . DB_PREFIX . "order_status_como` (`order_status_id`, `lbcolor`, `color`, `bgcolor`) VALUES(17, '#4f00ff', '', '')");
            $this->db->query("INSERT INTO `" . DB_PREFIX . "order_status_como` (`order_status_id`, `lbcolor`, `color`, `bgcolor`) VALUES(18, '#ff4721', '', '')");
            $this->db->query("INSERT INTO `" . DB_PREFIX . "order_status_como` (`order_status_id`, `lbcolor`, `color`, `bgcolor`) VALUES(19, '#808000', '', '')");
        }
    }

    public function install() {
        $this->checkDatabase();

        $this->setUsergroupPermissions('extension/module/como_orderstatus');
    }

    // set access permissions to all user groups
	private function setUsergroupPermissions($route, $typeperm = 'access') {
        $this->load->model('user/user_group');
        $user_groups = $this->model_user_user_group->getUserGroups();
        if ($user_groups && is_array($user_groups)) {
            foreach($user_groups as $user_group) {
                $user_group['permission'] = json_decode($user_group['permission'], true);
                if (!isset($user_group['permission'][$typeperm]) || !in_array($route, $user_group['permission'][$typeperm])) {
                    $this->model_user_user_group->addPermission($user_group['user_group_id'], $typeperm, $route);
                }
            }
        }
    }

    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS " . DB_PREFIX . "como_orderstatus");
    }

    public function index() {
        $this->checkDatabase();

        $this->load->language('extension/module/como_orderstatus');

        $this->document->setTitle($this->language->get('heading_title_clean'));
        $this->document->addStyle('view/stylesheet/como_orderstatus.css');
        // Load Spectrum color picker
        $this->document->addScript('https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.1/spectrum.min.js');
        $this->document->addStyle('https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.1/spectrum.min.css');

        $data['link_statuscolors'] = $this->url->link('localisation/order_status', 'user_token=' . $this->session->data['user_token'], true);

        $this->load->model('setting/setting');

        // Save the settings on POST
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->request->post && $this->validate()) {
            // Save settings
            $this->model_setting_setting->editSetting('module_como_orderstatus', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/module/como_orderstatus', 'user_token=' . $this->session->data['user_token'], true));
        }

        // get module config data
        $config_data = array(
            'module_como_orderstatus_status',
            'module_como_orderstatus_colors_status',
            'module_como_orderstatus_colors_fontsize',
            'module_como_orderstatus_colors_fontweight',
            'module_como_orderstatus_colors_bgcolor',
            'module_como_orderstatus_colors_align',
            'module_como_orderstatus_quickedit_status',
            'module_como_orderstatus_quickedit_notify_status',
            'module_como_orderstatus_quickedit_notify_default',
            'module_como_orderstatus_quickedit_comment_status',
            'module_como_orderstatus_quickedit_history_status',
            'module_como_orderstatus_quickview_status',
            'module_como_orderstatus_quickview_showoption',
        );
        foreach ($config_data as $conf) {
            $data[$conf] = $this->config->get($conf);
        }
        if (is_null($data['module_como_orderstatus_status'])) {
            $data['module_como_orderstatus_status'] = 1;
        }
        if (is_null($data['module_como_orderstatus_colors_status'])) {
            $data['module_como_orderstatus_colors_status'] = 1;
        }
        if (is_null($data['module_como_orderstatus_colors_fontsize'])) {
            $data['module_como_orderstatus_colors_fontsize'] = '11px';
        }
        if (is_null($data['module_como_orderstatus_colors_fontweight'])) {
            $data['module_como_orderstatus_colors_fontweight'] = 'bold';
        }
        if (is_null($data['module_como_orderstatus_colors_bgcolor'])) {
            $data['module_como_orderstatus_colors_bgcolor'] = '#f3f3f3';
        }
        if (is_null($data['module_como_orderstatus_colors_align'])) {
            $data['module_como_orderstatus_colors_align'] = 'left';
        }
        if (is_null($data['module_como_orderstatus_quickedit_status'])) {
            $data['module_como_orderstatus_quickedit_status'] = 1;
        }
        if (is_null($data['module_como_orderstatus_quickedit_notify_status'])) {
            $data['module_como_orderstatus_quickedit_notify_status'] = 1;
        }
        if (is_null($data['module_como_orderstatus_quickedit_notify_default'])) {
            $data['module_como_orderstatus_quickedit_notify_default'] = 0;
        }
        if (is_null($data['module_como_orderstatus_quickedit_comment_status'])) {
            $data['module_como_orderstatus_quickedit_comment_status'] = 1;
        }
        if (is_null($data['module_como_orderstatus_quickedit_history_status'])) {
            $data['module_como_orderstatus_quickedit_history_status'] = 1;
        }
        if (is_null($data['module_como_orderstatus_quickview_status'])) {
            $data['module_como_orderstatus_quickview_status'] = 1;
        }
        if (is_null($data['module_como_orderstatus_quickview_showoption'])) {
            $data['module_como_orderstatus_quickview_showoption'] = 0;
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
		} elseif (isset($this->session->data['error'])) {
            $data['error_warning'] = $this->session->data['error'];
        } else {
            $data['error_warning'] = '';
        }
		if (isset($this->session->data['warning'])) {
			$data['error_warning'] = $this->session->data['warning'];
			unset($this->session->data['warning']);
		} else {
			$data['error_warning'] = '';
		}
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('extension/module/como_orderstatus', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/module/como_orderstatus', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/como_orderstatus', $data));
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/como_orderstatus')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function quickeditHtml() {
		$json = array();

        $this->load->language('extension/module/como_orderstatus');
        $this->load->language('sale/order');
		$this->load->model('sale/order');

		if (isset($this->request->post['order_id'])) {
			$data['order_id'] = $this->request->post['order_id'];
		} else {
			$data['order_id'] = 0;
		}
		if (isset($this->request->post['order_id_selected'])) {
			$data['order_id_selected'] = json_decode(html_entity_decode($this->request->post['order_id_selected']), true);
            if (!is_array($data['order_id_selected'])) {
                $data['order_id_selected'] = array();
            }
		} else {
			$data['order_id_selected'] = array();
		}
        // add $data['order_id'] to selected array, if not allready there
        if (!in_array($data['order_id'], $data['order_id_selected'])) {
            $data['order_id_selected'][] = $data['order_id'];
        }

        $data['order_id_selected_json'] = json_encode($data['order_id_selected']);

		$order_info = $this->model_sale_order->getOrder($data['order_id']);

		if ($order_info) {
			$data['user_token'] = $this->session->data['user_token'];
            $this->load->model('localisation/order_status');
            $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
            $data['order_status_id'] = $order_info['order_status_id'];
			$data['comment'] = html_entity_decode(html_entity_decode(nl2br($order_info['comment'])));
            $data['quickedit_comment_status'] = $this->config->get('module_como_orderstatus_quickedit_comment_status');
            $data['quickedit_notify_status'] = $this->config->get('module_como_orderstatus_quickedit_notify_status');
            $data['quickedit_notify_default'] = $this->config->get('module_como_orderstatus_quickedit_notify_default') ? 1 : 0;
            $data['quickedit_history_status'] = $this->config->get('module_como_orderstatus_quickedit_history_status');
            
            if ($data['quickedit_history_status']) {
                $data['histories'] = array();
                $this->load->model('sale/order');
                $results = $this->model_sale_order->getOrderHistories($data['order_id'], 0, 10); // limit 10
                foreach ($results as $result) {
                    $data['histories'][] = array(
                        'notify'     => $result['notify'] ? $this->language->get('text_yes') : $this->language->get('text_no'),
                        'status'     => $result['status'],
                        'comment'    => html_entity_decode(html_entity_decode(nl2br($result['comment']))),
                        'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
                    );
                }
            }

            // For Como Order History Advanced compatability
            $data['templatecommentaction'] = is_null($this->config->get('module_comoordhistory_templatecommentaction')) ? 1 : $this->config->get('module_comoordhistory_templatecommentaction');

            // For Como Tracking codes compatability
            $data['como_tracking_status'] = $this->config->get('module_como_tracking_status');
            $data['como_tracking_buttonorderhist'] = $data['como_tracking_status'] && $this->config->get('module_como_tracking_buttonorderhist');

            $json['success'] = 'success';
            sort($data['order_id_selected']);
            $json['title'] = $this->language->get('modal_history_title') . implode(', ', $data['order_id_selected']);
            if (count($data['order_id_selected']) > 1) {
                $json['title'] .= '<br /><span style="color: red;">' . $this->language->get('modal_history_groupchange') . '</span>';
            }
            $json['html'] = $this->load->view('extension/module/como_orderstatus_updatehistory', $data);
        } else {
            $json['error'] = $this->language->get('error_order_id');
        }

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }

    public function quickeditSave() {
        $this->load->language('extension/module/como_orderstatus');

		$json = array();

        if ($this->user->hasPermission('modify', 'sale/order')) {
            if (isset($this->request->post['order_id_save'])) {
                $order_id_save = json_decode(html_entity_decode($this->request->post['order_id_save']), true);
                if (!is_array($order_id_save)) {
                    $order_id_save = array();
                }
            } else {
                $order_id_save = array();
            }
            $order_id = $this->request->post['order_id'];
            // add $order_id to selected array, if not allready there
            if (!in_array($order_id, $order_id_save)) {
                $order_id_save[] = $order_id;
            }

            $this->load->model('sale/order');

            $json['html_status'] = array();

            foreach($order_id_save as $order_id) {
                $post_data = array(
                    'order_id' => $order_id,
                    'order_status_id' => $this->request->post['order_status_id'],
                    'notify' => isset($this->request->post['notify']) ? $this->request->post['notify'] : ($this->config->get('module_como_orderstatus_quickedit_notify_default') ? 1 : 0),
                    'copytoadmin' => isset($this->request->post['copytoadmin']) ? $this->request->post['copytoadmin'] : 0,
                    'comment' => isset($this->request->post['comment']) ? $this->request->post['comment'] : '',
                    'override' => '0',
                );

                $order_info = $this->model_sale_order->getOrder($order_id);
                if ($order_info) {
                    // get last id for later check
                    $lastOrderHist1 = $this->getLastOrderHistory($order_id);
                    if (!isset($lastOrderHist1['order_history_id'])) {
                        $lastOrderHist1['order_history_id'] = 0;
                    }
                    
                    $result = $this->addOrderHistory($post_data);

                    // get last id to see if added
                    $lastOrderHist2 = $this->getLastOrderHistory($order_id);
                    if (!isset($lastOrderHist2['order_history_id'])) {
                        $lastOrderHist2['order_history_id'] = 0;
                    }
                    
                    if (isset($result['error'])) {
                        $json['error'] = $result['error'];
                    } elseif ((int)$lastOrderHist2['order_history_id'] <= (int)$lastOrderHist1['order_history_id']) {
                        $json['error'] = $this->language->get('error_notadded') . $order_id;
                    } else {
                        $order_info = $this->model_sale_order->getOrder($order_id);
                        $this->load->model('extension/module/como_orderstatus');
                        $json['html_status'][$order_id] = $this->model_extension_module_como_orderstatus->getOrderStatusName($order_info);
                        $json['success'] = 'success';
                        // send mail - it is done by event: catalog/model/checkout/order/addOrderHistory/before =>	mail/order
                    }
                } else {
                    $json['error'] = $this->language->get('error_order_id') . $order_id;
                }
            }
        } else {
            $json['error'] = $this->language->get('error_permission_order');
        }

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }

	private function getLastOrderHistory($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order_id . "' ORDER BY order_history_id DESC LIMIT 1");

		return (array)$query->row;
	}

	private function addOrderHistory($post_data) {
        $key = $this->securityStart($post_data);
        $post_data['key'] = $key;
        
        $url = HTTPS_CATALOG . 'index.php?route=extension/module/como_orderstatus/order_history&key=' . $key;

        $result = array();

        if (false) {
            // вариант с curl
            $defaults = array(
                CURLOPT_POST => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_AUTOREFERER => false,
                CURLOPT_VERBOSE => 1,
                CURLOPT_SSLVERSION => CURL_SSLVERSION_DEFAULT,
                CURLOPT_HEADER => 0,
                CURLOPT_USERAGENT => $this->request->server['HTTP_USER_AGENT'],
                CURLOPT_URL => $url,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_FORBID_REUSE => true,
                CURLOPT_RETURNTRANSFER => true,
                //CURLOPT_POSTFIELDS => http_build_query($post_data, '', "&"),
                CURLOPT_TIMEOUT => 60,
            );
            $curl = curl_init();
            curl_setopt_array($curl, $defaults);
            $curl_result = curl_exec($curl);
            $result_info = curl_getinfo($curl);
            curl_close($curl);
            if (isset($result_info['http_code']) && $result_info['http_code'] != 200) {
                $result['error'] = 'curl error, http_code = ' . $result_info['http_code'];
            } elseif ($curl_result === false) {
                $result['error'] = 'curl error';
            }
        } else {
            // вариант с file_get_contents
            $result = file_get_contents($url);
            if ($result) {
                $result = json_decode($result);
            }
        }

		return $result;
	}

	private function securityStart($data) {
        $key = rand();
        $this->db->query("INSERT INTO `" . DB_PREFIX . "session` SET `session_id` = '" . $key . "', `data` = '" . $this->db->escape(json_encode($data)) . "'");
        
        return $key;
	}

    public function quickviewOrder() {
		$json = array();

        $this->load->language('extension/module/como_orderstatus');
        $this->load->language('sale/order');
		$this->load->model('sale/order');

		if (isset($this->request->post['order_id'])) {
			$data['order_id'] = $this->request->post['order_id'];
		} else {
			$data['order_id'] = 0;
		}

		$order_info = $this->model_sale_order->getOrder($data['order_id']);

		if ($order_info) {
			$data['user_token'] = $this->session->data['user_token'];
			$data['store_id'] = $order_info['store_id'];
			$data['store_name'] = $order_info['store_name'];
			
			if ($order_info['store_id'] == 0) {
				$data['store_url'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;
			} else {
				$data['store_url'] = $order_info['store_url'];
			}

			if ($order_info['invoice_no']) {
				$data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
			} else {
				$data['invoice_no'] = '';
			}

			$data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));

			$data['firstname'] = $order_info['firstname'];
			$data['lastname'] = $order_info['lastname'];

            $data['customer_id'] = $order_info['customer_id'];
			if ($order_info['customer_id']) {
				$data['customer'] = $this->url->link('customer/customer/edit', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $order_info['customer_id'], true);
			} else {
				$data['customer'] = '';
			}

			$this->load->model('customer/customer_group');

			$customer_group_info = $this->model_customer_customer_group->getCustomerGroup($order_info['customer_group_id']);

			if ($customer_group_info) {
				$data['customer_group'] = $customer_group_info['name'];
			} else {
				$data['customer_group'] = '';
			}

			$data['email'] = $order_info['email'];
			$data['telephone'] = $order_info['telephone'];

			$data['shipping_method'] = $order_info['shipping_method'];
            $data['shipping_code'] = $order_info['shipping_code']; // For Como Tracking codes compatability
			$data['payment_method'] = $order_info['payment_method'];

			// Payment Address
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

			// Shipping Address
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

			// Uploaded files
			$this->load->model('tool/upload');

			$data['products'] = array();

			$products = $this->model_sale_order->getOrderProducts($data['order_id']);

			foreach ($products as $product) {
				$option_data = array();

				$options = $this->model_sale_order->getOrderOptions($data['order_id'], $product['order_product_id']);

				foreach ($options as $option) {
					if ($option['type'] != 'file') {
						$option_data[] = array(
							'name'  => $option['name'],
							'value' => $option['value'],
							'type'  => $option['type']
						);
					} else {
						$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

						if ($upload_info) {
							$option_data[] = array(
								'name'  => $option['name'],
								'value' => $upload_info['name'],
								'type'  => $option['type'],
								'href'  => $this->url->link('tool/upload/download', 'user_token=' . $this->session->data['user_token'] . '&code=' . $upload_info['code'], true)
							);
						}
					}
				}

				$data['products'][] = array(
					'order_product_id' => $product['order_product_id'],
					'product_id'       => $product['product_id'],
					'name'    	 	   => $product['name'],
					'model'    		   => $product['model'],
					'option'   		   => $option_data,
					'quantity'		   => $product['quantity'],
					'price'    		   => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
					'total'    		   => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
					'href'     		   => $this->url->link('catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $product['product_id'], true)
				);
			}

			$data['vouchers'] = array();

			$vouchers = $this->model_sale_order->getOrderVouchers($data['order_id']);

			foreach ($vouchers as $voucher) {
				$data['vouchers'][] = array(
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']),
					'href'        => $this->url->link('sale/voucher/edit', 'user_token=' . $this->session->data['user_token'] . '&voucher_id=' . $voucher['voucher_id'], true)
				);
			}

			$data['totals'] = array();

			$totals = $this->model_sale_order->getOrderTotals($data['order_id']);

			foreach ($totals as $total) {
				$data['totals'][] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value'])
				);
			}

			$data['comment'] = nl2br($order_info['comment']);

			$this->load->model('customer/customer');

			$data['reward'] = $order_info['reward'];

			$data['reward_total'] = $this->model_customer_customer->getTotalCustomerRewardsByOrderId($data['order_id']);

			$data['affiliate_firstname'] = $order_info['affiliate_firstname'];
			$data['affiliate_lastname'] = $order_info['affiliate_lastname'];

			if ($order_info['affiliate_id']) {
				$data['affiliate'] = $this->url->link('customer/customer/edit', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $order_info['affiliate_id'], true);
			} else {
				$data['affiliate'] = '';
			}

			$data['commission'] = $this->currency->format($order_info['commission'], $order_info['currency_code'], $order_info['currency_value']);

			$this->load->model('customer/customer');

			$data['commission_total'] = $this->model_customer_customer->getTotalTransactionsByOrderId($data['order_id']);

            // adjustments
            $this->load->model('setting/store');
            $data['store_total'] = $this->model_setting_store->getTotalStores();
            $data['orderstatus_quickview_showoption'] = $this->config->get('module_como_orderstatus_quickview_showoption');

            // For Como Tracking codes compatability
            $data['como_tracking_status'] = $this->config->get('module_como_tracking_status');
            if ($data['como_tracking_status']) {
                $this->load->controller('extension/module/como_tracking/collectTrackingCodes', array('order_id' => $data['order_id']));
            }
            
            $json['success'] = 'success';
            $json['title'] = $this->language->get('modal_quickview_title') . $data['order_id'];
            $json['html'] = $this->load->view('extension/module/como_orderstatus_quickvieworder', $data);
        } else {
            $json['error'] = $this->language->get('error_order_id');
        }

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
}
