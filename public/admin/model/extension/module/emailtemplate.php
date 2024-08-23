<?php
class ModelExtensionModuleEmailTemplate extends Model {
	private $content_count = 3;
	private $currency;
	private $tax;
	private $original_templates = array(
		'admin.affiliate_approve',
		'admin.affiliate_deny',
		'admin.customer_approve',
		'admin.customer_create',
		'admin.customer_deny',
		'admin.customer_history',
		'admin.customer_reward',
		'admin.customer_transaction',
		'admin.newsletter',
		'admin.return_history',
		'admin.voucher',
		'affiliate.register',
		'affiliate.register_admin',
		'customer.forgotten',
		'customer.register',
		'customer.register_admin',
		'information.contact',
		'information.contact_customer',
		'order.customer',
		'order.admin',
		'order.return',
		'order.return_customer',
		'order.update',
		'order.voucher',
		'product.review'
	);

	private $modification_names = array('emailtemplates', 'emailtemplates_core', 'emailtemplates_newsletter', 'emailtemplates_security');

	/**
	 * Load Email Template
	 *
	 * @param mixed   $load
	 *        null    load default email template (1)
	 *        array   load email template using array key(s)
	 *        int     load email template using `emailtemplate_id`
	 *        string  load email template using `emailtemplate_key`
	 * @param array   extra data used to load template
	 * @param array|null   default conditions
	 * @return object EmailTemplate
	 */
	public function load($load = null, $template_data = array(), $conditions = null) {
		if (is_null($load)) {
			$filter = array('emailtemplate_id' => 1);
		} elseif (is_numeric($load) && $load) {
			$filter = array('emailtemplate_id' => $load);
		} elseif (is_string($load) && $load) {
			$filter = array('emailtemplate_key' => $load);
		} elseif (is_array($load)) {
			$filter = array();

			foreach (array('emailtemplate_id', 'emailtemplate_config_id') as $var) {
				if (!empty($load[$var])) {
					$filter[$var] = $load[$var];
				}
			}

			if (isset($load['emailtemplate_key'])) {
				$filter['emailtemplate_key'] = $load['emailtemplate_key'];
			} elseif (isset($load['key'])) {
				$filter['emailtemplate_key'] = $load['key'];
			}
		}

		if (empty($filter)) {
			$filter = array('emailtemplate_id' => 1);
		}

		$templates = $this->getTemplates($filter);

		if (count($templates) == 0) {
			return false;
		}

		if (isset($load['language_id']) && $load['language_id'] > 0) {
			$language_id = $load['language_id'];
		} else {
			$language_id = $this->config->get("config_language_id");
		}

		if (isset($load['store_id'])) {
			$store_id = $load['store_id'];
		} else {
			$store_id = $this->config->get("config_store_id");
		}
		if (!$store_id || $store_id < 0) {
			$store_id = 0;
		}

		if (!empty($load['customer_id'])) {
			$customer_id = $load['customer_id'];
		} else {
			$customer_id = 0;
		}

		$customer_group_id = null;

		if (!empty($load['customer_group_id'])) {
			$customer_group_id = $load['customer_group_id'];
		}

		$customer_info = array();

		if ($customer_id) {
			$this->load->model('customer/customer');
			$customer_info = $this->model_customer_customer->getCustomer($customer_id);
		} elseif (!empty($load['email'])) {
			$this->load->model('customer/customer');
			$customer_info = $this->model_customer_customer->getCustomerByEmail($load['email']);
		}

		if (is_null($customer_group_id)) {
			if ($customer_info) {
				$customer_group_id = $customer_info['customer_group_id'];
			} else {
				$customer_group_id = $this->config->get("config_customer_group_id");
			}
		}

        if (!is_array($conditions)) {
            $conditions = array();
        }

        $conditions = array_merge($conditions, array(
            'language_id' => $language_id,
            'customer_group_id' => $customer_group_id,
            'store_id' => $store_id
        ));

        foreach (array(
                     'order_status_id',
                     'payment_method',
                     'shipping_method'
                 ) as $var) {
            if (isset($load[$var])) {
                $conditions[$var] = $load[$var];
            }
        }

        if ($template_data && is_array($template_data)) {
            $template_data = array_merge($template_data, $conditions);
        } else {
            $template_data = $conditions;
        }

        $exclude_conditions = isset($load['exclude_conditions']) ? $load['exclude_conditions'] : array();

        foreach ($templates as $i => $template) {
            $templates[$i]['power'] = 0;

            foreach ($conditions as $_key => $_value) {
                if (isset($exclude_conditions[$_key])) continue;

                $templates[$i]['power'] = $templates[$i]['power'] << 1;

                if (isset($template[$_key]) && $template[$_key] != 'NULL') {
                    switch($_key) {
                        case 'store_id':
                            if (!is_null($template[$_key])) {
                                if ((int)$template[$_key] === (int)$_value) {
                                    $templates[$i]['power'] += 1;
                                } else {
                                    unset($templates[$i]);
                                    break 2;
                                }
                            }
                            break;

                        default:
                            if ($template[$_key]) {
                                if($template[$_key] == $_value) {
                                    $templates[$i]['power'] += 1;
                                } else {
                                    unset($templates[$i]);
                                    break 2;
                                }
                            }
                    }
                }
            }

            if (!isset($templates[$i]))
                continue;

            $conditionPower = $this->checkTemplateCondition($templates[$i]['power'], $template['emailtemplate_condition'], $template_data);

            if (is_numeric($conditionPower)) {
                $templates[$i]['power'] = $conditionPower;
            } else {
                return false;
            }
        }

        if (count($templates) == 0) {
            return false;
        }

		$emailtemplate = reset($templates);

		if (count($templates) > 1) {
			foreach ($templates as $template) {
				if ($template['emailtemplate_default']) {
					$emailtemplate = $template;
					break;
				}
			}

			foreach ($templates as $template) {
				if ($template['power'] > $emailtemplate['power']) {
					$emailtemplate = $template;
				}
			}
		}

        if ($emailtemplate['power'] < 0) {
            return false;
        }

        $description = $this->getTemplateDescription(array(
			'emailtemplate_id' => $emailtemplate['emailtemplate_id'],
			'language_id' => $language_id
		), 1);

		if (!$description) {
			$description = $this->getTemplateDescription(array(
				'emailtemplate_id' => $emailtemplate['emailtemplate_id']
			), 1);
		}

		if (!$description) {
            trigger_error('Missing language for email template #' . $emailtemplate['emailtemplate_id']);
			return false;
		}

		foreach($emailtemplate as $key => $val) {
			if (substr($key, 0, 14) == 'emailtemplate_' && substr($key, -3) != '_id') {
				unset($emailtemplate[$key]);
				$emailtemplate[substr($key, 14)] = $val;
			}
		}

		foreach($description as $key => $val) {
			if (isset($emailtemplate[$key])) continue;

			if (substr($key, 0, 26) == 'emailtemplate_description_' && substr($key, -3) != '_id') {
				$emailtemplate[substr($key, 26)] = $val;
			} else {
				$emailtemplate[$key] = $val;
			}
		}

		if (!empty($filter['emailtemplate_config_id'])) {
			$template_config = $this->getConfig($filter['emailtemplate_config_id']);
		} elseif ($emailtemplate['emailtemplate_config_id']) {
			$template_config = $this->getConfig($emailtemplate['emailtemplate_config_id']);
		} else {
			$configs = $this->getConfigs((array('sort' => 'emailtemplate_config_id', 'order')));

			// Remove invalid configs
			foreach ($configs as $i => $config) {
				$is_admin_config = ($emailtemplate['type'] == 'admin' || substr($emailtemplate['key'], -6) == '.admin') ? true : false;

				foreach ($conditions as $_key => $_value) {
					if ((isset($config[$_key]) && $config[$_key] > -1) && $config[$_key] != $_value) {
						unset($configs[$i]);
						continue;
					}
				}

				// Admin only?
				if (!empty($config['emailtemplate_config_admin']) && !$is_admin_config) {
					unset($configs[$i]);
					continue;
				}
			}

			// Select by power
			if (count($configs) > 1) {
				foreach ($configs as $i => $config) {
					$configs[$i]['power'] = 0;
					if (!empty($config['emailtemplate_config_admin'])) {
						$configs[$i]['power'] += 1;
					}
					foreach ($conditions as $_key => $_value) {
						if (isset($config[$_key]) && $config[$_key] == $_value) {
							$configs[$i]['power'] += 1;
						}
					}
				}

				reset($configs);
				$template_config = current($configs);

				foreach ($configs as $config) {
					if ($config['power'] > $template_config['power']) {
						$template_config = $config;
					}
				}
			} else {
				reset($configs);
				$template_config = current($configs);
			}
		}

		if (empty($template_config)) {
			$template_config = $this->getConfig(1);

			if ($template_config['store_id'] > -1) {
				$store_id = $template_config['store_id'];
			}
		}

		$emailtemplate_config = array();

		foreach($template_config as $key => $val) {
			if (substr($key, 0, 21) == 'emailtemplate_config_' && substr($key, -3) != '_id') {
				unset($emailtemplate_config[$key]);
				$emailtemplate_config[substr($key, 21)] = $val;
			} else {
				$emailtemplate_config[$key] = $val;
			}
		}

		$this->load->model('extension/module/emailtemplate_newsletter');

		// Start adding data
		$this->load->library('emailtemplate');

        $this->emailtemplate->setDatabaseModel($this);

		$this->emailtemplate->data['store_id'] = $store_id;
		$this->emailtemplate->data['language_id'] = $language_id;
		$this->emailtemplate->data['customer_group_id'] = $customer_group_id;

		if (!empty($customer_info) && empty($load['disable_newsletter_preference'])) {
			if ($this->config->get('module_emailtemplate_newsletter_status') && $this->config->get('module_emailtemplate_newsletter_preference')) {
				$newsletter_preference = $this->model_extension_module_emailtemplate_newsletter->getCustomerPreference($customer_info['customer_id']);

				if ($newsletter_preference) {
					$customer_info['newsletter_preference'] = array(
						'token' => $newsletter_preference['token']
					);

					if ($this->config->get('module_emailtemplate_newsletter_showcase')) {
						$customer_info['newsletter_preference']['showcase'] = $newsletter_preference['showcase'];
					}

					if ($this->config->get('module_emailtemplate_newsletter_newsletter')) {
						$customer_info['newsletter_preference']['newsletter'] = $customer_info['newsletter'];
					}

					if ($this->config->get('module_emailtemplate_newsletter_notification')) {
						$customer_info['newsletter_preference']['notification'] = $newsletter_preference['notification'];
					}
				}

				if (($emailtemplate['type'] != 'admin' || substr($emailtemplate['key'], -6) != '.admin') && !empty($emailtemplate['preference'])) {
					switch ($emailtemplate['preference']) {
						case 'newsletter':
							if (!$customer_info['newsletter']) {
								return false;
							}
							break;
						case 'notification':
							if (empty($newsletter_preference['notification'])) {
								return false;
							}
							break;
					}
				}
			} else {
				$this->emailtemplate->data['emailtemplate']['preference'] = false;
			}

			$this->emailtemplate->data['customer_id'] = $customer_info['customer_id'];
			$this->emailtemplate->data['customer_email'] = $customer_info['email'];

			$this->emailtemplate->setCustomer($customer_info);
		}

		if (!empty($this->request->server['HTTP_CLIENT_IP'])) {
			$ip = $this->request->server['HTTP_CLIENT_IP'];
		} elseif (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
			$ip = $this->request->server['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $this->request->server['REMOTE_ADDR'];
		}

		$this->emailtemplate->data['ip'] = $ip;

		// Fetch store data
		$this->load->model('setting/store');
		$this->load->model('setting/setting');

		$store_info = array_merge(
			$this->model_setting_setting->getSetting("config", $store_id),
			$this->model_setting_store->getStore($store_id)
		);

		$config_keys = array('title', 'name', 'url', 'ssl', 'owner', 'address', 'email', 'telephone', 'fax', 'country_id', 'currency', 'zone_id', 'tax', 'tax_default', 'theme', 'customer_price');

        foreach ($config_keys as $key) {
            if (isset($store_info[$key])) {
                $this->emailtemplate->data['store_' . $key] = $store_info[$key];
            } elseif (isset($store_info['config_' . $key])) {
                $this->emailtemplate->data['store_' . $key] = $store_info['config_' . $key];
            } else {
                $this->emailtemplate->data['store_' . $key] = $this->config->get('config_' . $key);
            }
        }

		if (empty($this->emailtemplate->data['store_url'])) {
			$this->emailtemplate->data['store_url'] = HTTPS_CATALOG ? HTTPS_CATALOG : HTTP_CATALOG;
		}

		if (empty($this->emailtemplate->data['store_ssl'])) {
			$this->emailtemplate->data['store_ssl'] = HTTPS_CATALOG;
		}

        if (!isset($this->emailtemplate->data['store_title'])) {
            $this->emailtemplate->data['store_title'] = $this->emailtemplate->data['store_name'];
        }

		$this->emailtemplate->data['store_name'] = html_entity_decode($this->emailtemplate->data['store_name'], ENT_QUOTES, 'UTF-8');
        $this->emailtemplate->data['store_title'] = html_entity_decode($this->emailtemplate->data['store_title'], ENT_QUOTES, 'UTF-8');

		if (defined('HTTP_IMAGE')) {
			$image_url = HTTP_IMAGE;
		} else {
			$image_url = $this->emailtemplate->data['store_url'] . 'image/';
		}

		// Add EmailTemplate
		$this->emailtemplate->data['emailtemplate'] = $emailtemplate;

		$this->emailtemplate->data['config'] = $emailtemplate_config;

		// Add any extra data used to load template
		if ($template_data && is_array($template_data)) {
			foreach ($template_data as $i => $val) {
				if (is_array($val) && !empty($this->emailtemplate->data[$i])) {
					$this->emailtemplate->data[$i] = array_merge($this->emailtemplate->data[$i], $template_data[$i]);
					unset($template_data[$i]);
				}
			}

			$this->emailtemplate->data = array_merge($this->emailtemplate->data, $template_data);
		}

		if (!empty($this->emailtemplate->data['config']['body_font_family'])) {
			$body_font_family = html_entity_decode($this->emailtemplate->data['config']['body_font_family'], ENT_QUOTES, 'UTF-8');

			// Replace double quotes
			if (strpos($body_font_family, '"') !== false) {
				$this->emailtemplate->data['config']['body_font_family'] = str_replace('"', "'", $body_font_family);
			}
		}

		foreach (array('header_html', 'head_text', 'page_footer_text', 'footer_text', 'showcase_title') as $var) {
			if (!empty($this->emailtemplate->data['config'][$var])) {
				if (is_string($this->emailtemplate->data['config'][$var])) {
					$this->emailtemplate->data['config'][$var] = $this->emailtemplate->data['config'][$var];
				} elseif (isset($this->emailtemplate->data['config'][$var][$language_id])) {
					$this->emailtemplate->data['config'][$var] = $this->emailtemplate->data['config'][$var][$language_id];
				} else {
					$this->emailtemplate->data['config'][$var] = '';
				}

				if (trim(strip_tags($this->emailtemplate->data['config'][$var])) == '') {
					$this->emailtemplate->data['config'][$var] = '';
				}
			} else {
				$this->emailtemplate->data['config'][$var] = '';
			}
		}

		foreach (array(
					 'header_border_top', 'header_border_bottom', 'header_border_right', 'header_border_left',
					 'footer_border_top', 'footer_border_bottom', 'footer_border_right', 'footer_border_left',
					 'page_border_top', 'page_border_bottom', 'page_border_right', 'page_border_left',
					 'showcase_border_top', 'showcase_border_bottom', 'showcase_border_right', 'showcase_border_left'
				 ) as $var) {
			if (empty($this->emailtemplate->data['config'][$var])) {
				$this->emailtemplate->data['config'][$var] = array();
			} elseif ($this->emailtemplate->data['config'][$var] && !is_array($this->emailtemplate->data['config'][$var])) {
				$this->emailtemplate->data['config'][$var] = array_map('trim', explode(',', $this->emailtemplate->data['config'][$var], 2));
			}
		}

		foreach (array('header_padding', 'header_spacing', 'footer_padding', 'footer_spacing', 'page_padding', 'page_spacing', 'showcase_padding') as $var) {
			if (empty($this->emailtemplate->data['config'][$var])) {
				$this->emailtemplate->data['config'][$var] = array();
			} elseif ($this->emailtemplate->data['config'][$var] && !is_array($this->emailtemplate->data['config'][$var])) {
				$this->emailtemplate->data['config'][$var] = array_map('trim', explode(',', $this->emailtemplate->data['config'][$var]));
			}
		}

		$this->emailtemplate->data['config']['border_radius'] = false;

		foreach (array(
			         'header_border_radius','footer_border_radius','page_border_radius', 'showcase_border_radius'
		         ) as $var) {
			if (!empty($this->emailtemplate->data['config'][$var])) {
				if (!is_array($this->emailtemplate->data['config'][$var])) {
					$this->emailtemplate->data['config'][$var] = array_map('trim', explode(',', $this->emailtemplate->data['config'][$var]));
				}
				foreach ($this->emailtemplate->data['config'][$var] as $val) {
					if ((int)$val) {
						$this->emailtemplate->data['config']['border_radius'] = true;
					}
				}
			}
		}

		$this->emailtemplate->data['config']['has_section'] = false;

		// Has section check for color OR page shadow
		if (empty($this->emailtemplate->data['config']['page_shadow']) || $this->emailtemplate->data['config']['page_shadow'] == 'combine-shadow') {
			foreach (array('body_section_bg_color', 'footer_section_bg_color', 'header_section_bg_color', 'showcase_section_bg_color') as $var) {
				if (!empty($this->emailtemplate->data['config'][$var])) {
					$this->emailtemplate->data['config']['has_section'] = true;
				}
			}
		}

        foreach(array('showcase_setting','order_products','order_update','cart_setting', 'preference_text') as $var) {
			if (empty($this->emailtemplate->data['config'][$var])) {
				$this->emailtemplate->data['config'][$var] = array();
			} elseif ($this->emailtemplate->data['config'][$var] && !is_array($this->emailtemplate->data['config'][$var])) {
                $unserialized = @unserialize(base64_decode($this->emailtemplate->data['config'][$var]));
                $this->emailtemplate->data['config'][$var] = ($unserialized !== false) ? $unserialized : $this->emailtemplate->data['config'][$var];
            }
        }

        if ($customer_info && $this->emailtemplate->data['emailtemplate']['type'] != 'admin' && isset($this->emailtemplate->data['config']['preference_text']) && isset($emailtemplate['preference']) && !empty($this->emailtemplate->data['config']['preference_text'][$emailtemplate['preference']])) {
			$val = $this->emailtemplate->data['config']['preference_text'][$emailtemplate['preference']];
			if (is_string($val)) {
				$this->emailtemplate->data['config']['preference_text'] =  html_entity_decode($val, ENT_QUOTES, 'UTF-8');
			} elseif (is_array($val) && isset($val[$language_id])) {
				$this->emailtemplate->data['config']['preference_text'] =  html_entity_decode($val[$language_id], ENT_QUOTES, 'UTF-8');
			} else {
				$this->emailtemplate->data['config']['preference_text'] =  '';
			}
		} else {
			$this->emailtemplate->data['config']['preference_text'] =  '';
		}

		// Shadow
		foreach(array('top','bottom','left','right') as $var) {
			if (isset($this->emailtemplate->data['config']['shadow_'.$var]) && !is_array($this->emailtemplate->data['config']['shadow_'.$var])) {
				$unserialized = @unserialize(base64_decode($this->emailtemplate->data['config']['shadow_'.$var]));
				$this->emailtemplate->data['config']['shadow_'.$var] = ($unserialized !== false) ? $unserialized : $this->emailtemplate->data['config']['shadow_'.$var];
			} else {
                $this->emailtemplate->data['config']['shadow_'.$var] = array(
                    'length' => 0
                );
            }
		}

		foreach (array('top', 'bottom') as $v) {
			foreach (array('left', 'right') as $h) {
				if (!empty($this->emailtemplate->data['config']['shadow_'.$v][$h.'_img'])) {
					$this->emailtemplate->data['config']['shadow_'.$v][$h.'_img'] = ($this->emailtemplate->data['config']['shadow_'.$v][$h.'_img']) ? $image_url . $this->emailtemplate->data['config']['shadow_'.$v][$h.'_img'] : '';
					$this->emailtemplate->data['config']['shadow_'.$v][$h.'_img_height'] = (int)$this->emailtemplate->data['config']['shadow_'.$v]['length'] + (int)$this->emailtemplate->data['config']['shadow_'.$v]['overlap'];
					$this->emailtemplate->data['config']['shadow_'.$v][$h.'_img_width'] = (int)$this->emailtemplate->data['config']['shadow_'.$h]['length'] + (int)$this->emailtemplate->data['config']['shadow_'.$h]['overlap'];
				}
			}
		}

		foreach(array('left', 'right') as $col) {
			if (isset($this->emailtemplate->data['config']['shadow_top'][$col.'_img']) && file_exists(DIR_IMAGE . $this->emailtemplate->data['config']['shadow_top'][$col.'_img'])) {
				$this->emailtemplate->data['config']['shadow_top'][$col.'_thumb'] = $image_url . $this->emailtemplate->data['config']['shadow_top'][$col.'_img'];
			}

			if (isset($this->emailtemplate->data['config']['shadow_bottom'][$col.'_img']) && file_exists(DIR_IMAGE . $this->emailtemplate->data['config']['shadow_bottom'][$col.'_img'])) {
				$this->emailtemplate->data['config']['shadow_bottom'][$col.'_thumb'] = $image_url . $this->emailtemplate->data['config']['shadow_bottom'][$col.'_img'];
			}
		}

		if ($this->emailtemplate->data['config']['body_bg_image']) {
			$this->emailtemplate->data['config']['body_bg_image'] = $image_url . $this->emailtemplate->data['config']['body_bg_image'];
		}

		if ($this->emailtemplate->data['config']['header_bg_image']) {
			$this->emailtemplate->data['config']['header_bg_image'] = $image_url . $this->emailtemplate->data['config']['header_bg_image'];
		}

		if ($this->emailtemplate->data['config']['logo']) {
            if (!empty($this->emailtemplate->data['config']['logo_resize']) && !empty($this->emailtemplate->data['config']['logo_width']) && !empty($this->emailtemplate->data['config']['logo_height'])) {
				$this->load->model('tool/image');

                $this->emailtemplate->data['config']['logo'] = $this->model_tool_image->resize($this->emailtemplate->data['config']['logo'], $this->emailtemplate->data['config']['logo_width'], $this->emailtemplate->data['config']['logo_height']);
			} else {
				$this->emailtemplate->data['config']['logo'] = $image_url . $this->emailtemplate->data['config']['logo'];
			}

			// Fix spaces etc
			if ($this->emailtemplate->data['config']['logo'] && strpos($this->emailtemplate->data['config']['logo'], '%') === false) {
				$url_parse = parse_url($this->emailtemplate->data['config']['logo']);

				$logo_url = '';

				if (!empty($url_parse["scheme"]) && !empty($url_parse["host"])) {
					$logo_url .= $url_parse['scheme'] . '://' . $url_parse['host'] . '/';
				}

				$logo_path = trim(str_replace(basename($_SERVER['SCRIPT_NAME']), '', $url_parse["path"]), '/');

				$logo_url .= implode('/', array_map('rawurlencode', explode('/', $logo_path)));

				$this->emailtemplate->data['config']['logo'] = $logo_url;
			}

			// Fail safe check
			if (substr($this->emailtemplate->data['config']['logo'], 0, 4) != 'http') {
				$this->emailtemplate->data['config']['logo'] = $this->emailtemplate->data['store_url'] . ltrim($this->emailtemplate->data['config']['logo'], '/');
			} else {
                // Replace admin url with store
                $this->emailtemplate->data['config']['logo'] = str_replace(array(HTTP_SERVER, HTTPS_SERVER), $this->emailtemplate->data['store_url'], $this->emailtemplate->data['config']['logo']);
            }
		}

		$this->emailtemplate->data['config']['template_dir'] = DIR_CATALOG . 'view/theme/' . $this->emailtemplate->data['store_theme'] . '/template/extension/module/emailtemplate/';

		// Default to px if no unit
		$unit = preg_replace('/[0-9]+/', '', $this->emailtemplate->data['config']['email_width']);

		if (!$unit) {
			$unit = 'px';
			$this->emailtemplate->data['config']['email_width'] .= $unit;
		}

		if ($unit == 'px') {
            $this->emailtemplate->data['config']['email_full_width'] = (int)$this->emailtemplate->data['config']['email_width'];

            if ($this->emailtemplate->data['config']['shadow_left'] || $this->emailtemplate->data['config']['shadow_right']) {
                $this->emailtemplate->data['config']['email_full_width'] += ((int)$this->emailtemplate->data['config']['shadow_right']['length'] + (int)$this->emailtemplate->data['config']['shadow_right']['length']);
            }

			$this->emailtemplate->data['config']['email_inner_width'] = (int)$this->emailtemplate->data['config']['email_width'];

			if ($this->emailtemplate->data['config']['page_padding'] && count($this->emailtemplate->data['config']['page_padding']) == 4){
				$this->emailtemplate->data['config']['email_inner_width'] -= (int)$this->emailtemplate->data['config']['page_padding'][1] + (int)$this->emailtemplate->data['config']['page_padding'][3];
			}

			if ($this->emailtemplate->data['config']['page_border_left'] && count($this->emailtemplate->data['config']['page_border_left']) == 2){
				$this->emailtemplate->data['config']['email_inner_width'] -= (int)$this->emailtemplate->data['config']['page_border_left'][0];
			}

			if ($this->emailtemplate->data['config']['page_border_right'] && count($this->emailtemplate->data['config']['page_border_right']) == 2){
				$this->emailtemplate->data['config']['email_inner_width'] -= (int)$this->emailtemplate->data['config']['page_border_right'][0];
			}
		} else {
			$this->emailtemplate->data['config']['email_full_width'] = (int)$this->emailtemplate->data['config']['email_width'];

			$this->emailtemplate->data['config']['email_inner_width'] = (int)$this->emailtemplate->data['config']['email_width'];
		}

		$email_inner_width = $this->emailtemplate->data['config']['email_inner_width'];

		$this->emailtemplate->data['config']['email_inner_width'] .= $unit;
		$this->emailtemplate->data['config']['email_full_width'] .= $unit;

		// Language files
		$this->load->model('localisation/language');

		$language_info = $this->model_localisation_language->getLanguage($language_id);

		if (!$language_info) {
			$language_info = $this->model_localisation_language->getLanguage($this->config->get('config_language_id'));
		}

		$oLanguage = new Language($language_info['code']);

		if (method_exists($oLanguage, 'setPath') && substr($emailtemplate['key'], 0, 6) != 'admin.' && defined('DIR_CATALOG')) {
			$oLanguage->setPath(DIR_CATALOG . 'language/');
		}

		$oLanguage->load($language_info['code']);

        $this->emailtemplate->language_data = $oLanguage->load('extension/module/emailtemplate/emailtemplate');

        if ($this->emailtemplate->data['emailtemplate']['language_files']) {
            $language_files = array_map('trim', explode(',', $this->emailtemplate->data['emailtemplate']['language_files']));

            if ($language_files) {
                foreach ($language_files as $language_file) {
                    $language_data = $oLanguage->load(trim($language_file));

                    if ($language_data) {
                        $this->emailtemplate->language_data = array_merge($this->emailtemplate->language_data, $language_data);
                    }
                }
            }
        }

		if ($this->emailtemplate->language_data) {
			$this->emailtemplate->data = array_merge($this->emailtemplate->data, $this->emailtemplate->language_data);
		}

		$this->emailtemplate->data['lang'] = $oLanguage->get('code');
		$this->emailtemplate->data['direction'] = ($oLanguage->get('direction') && $oLanguage->get('direction') != 'direction') ? $oLanguage->get('direction') : 'ltr';

        $this->emailtemplate->data['date_now'] = date($oLanguage->get('date_format_short'));
        $this->emailtemplate->data['datetime_now'] = date($oLanguage->get('date_format_long'));

		$this->emailtemplate->data['login_url'] = $this->emailtemplate->data['store_url'] . 'index.php?route=account/login';
		$this->emailtemplate->data['cart_url'] = $this->emailtemplate->data['store_url'] . 'index.php?route=checkout/cart';
		$this->emailtemplate->data['contact_url'] = $this->emailtemplate->data['store_url'] . 'index.php?route=information/contact';
		$this->emailtemplate->data['home_url'] = $this->emailtemplate->data['store_url'] . 'index.php?route=common/home';
		$this->emailtemplate->data['privacy_url'] = $this->emailtemplate->data['store_url'] . 'index.php?route=information/information&information_id=' . $this->config->get('config_account_id');

        if ($emailtemplate['wrapper_tpl']) {
            $this->emailtemplate->data['wrapper_tpl'] = $emailtemplate['wrapper_tpl'];
        } elseif ($emailtemplate_config['wrapper_tpl']) {
            $this->emailtemplate->data['wrapper_tpl'] = $emailtemplate_config['wrapper_tpl'];
        }

		if (!isset($this->emailtemplate->data['emailtemplate_log_id']) &&
            ($this->emailtemplate->data['emailtemplate']['mail_queue'] == 1 ||
                $this->emailtemplate->data['emailtemplate']['log'] == 1 ||
                ($this->emailtemplate->data['emailtemplate']['log'] == 0 && $this->emailtemplate->data['config']['log']))
        ) {
			$this->emailtemplate->data['emailtemplate_log_id'] = $this->createTemplateLog();
		}

		return $this->emailtemplate;
	}

    public function build() {
        if(!empty($this->emailtemplate->data['customer_id'])) {
            $customer_id = $this->emailtemplate->data['customer_id'];
        }  else {
            $customer_id = null;
        }

        if(!empty($this->emailtemplate->data['store_id'])) {
            $store_id = $this->emailtemplate->data['store_id'];
        }  else {
            $store_id = null;
        }

        $this->emailtemplate->data['showcase_selection'] = array();

        if (!empty($this->emailtemplate->data['config']['showcase_setting']['limit'])) {
            $showcase_limit = $this->emailtemplate->data['config']['showcase_setting']['limit'];
        } else {
            $showcase_limit = 4;
        }

        if (!empty($this->emailtemplate->data['config']['showcase_setting']['related'])) {
            $showcase_related = $this->emailtemplate->data['config']['showcase_setting']['related'];
        } else {
            $showcase_related = 1;
        }

        if (!empty($this->emailtemplate->data['config']['showcase_setting']['per_row'])) {
            $showcase_per_row = $this->emailtemplate->data['config']['showcase_setting']['per_row'];
        } else {
            $showcase_per_row = 4;
        }

		$this->emailtemplate->data['showcase_selection'] = array();

		if ($this->emailtemplate->data['emailtemplate']['showcase'] != 'none' && $this->emailtemplate->data['config']['showcase'] != 'none') {
			if ($this->emailtemplate->data['emailtemplate']['showcase_selection']) {
                $showcase_selection = $this->emailtemplate->data['emailtemplate']['showcase_selection'];
            } else {
                $showcase_selection = $this->emailtemplate->data['config']['showcase_selection'];
            }

            $showcase = $this->emailtemplate->data['emailtemplate']['showcase'];

            if ($showcase !== 0) {
                $showcase = $this->emailtemplate->data['config']['showcase'];
            }

			$this->emailtemplate->data['showcase_selection'] = $this->getShowcase($customer_id, $showcase, $showcase_limit, $showcase_related, $showcase_selection);
		}

		if (!empty($this->emailtemplate->data['showcase_selection'])) {
			if (count($this->emailtemplate->data['showcase_selection']) < $showcase_per_row) {
				$this->emailtemplate->data['config']['showcase_item_width'] = 100 / count($this->emailtemplate->data['showcase_selection']);
			} else {
				$this->emailtemplate->data['config']['showcase_item_width'] = 100 / $showcase_per_row;
			}
			$this->emailtemplate->data['config']['showcase_item_width'] = (int)$this->emailtemplate->data['config']['showcase_item_width'] . '%';
		}

		$customer_info = $this->emailtemplate->getCustomer();

		if ($customer_info) {
			if ($this->config->get('module_emailtemplate_newsletter_status') && $this->config->get('module_emailtemplate_newsletter_preference')) {
				if (!empty($customer_info['newsletter_preference']['token'])) {
					$token = $customer_info['newsletter_preference']['token'];
				} else {
					$customer_preference_info = $this->model_extension_module_emailtemplate_newsletter->getCustomerPreference($customer_info['customer_id']);

					$token = token(32);

					if ($customer_preference_info) {
						$customer_preference_data = array(
							'notification' => $customer_preference_info['notification'],
							'showcase' => $customer_preference_info['showcase'],
							'token' => $token
						);

						$this->model_extension_module_emailtemplate_newsletter->editCustomerPreference($customer_info['customer_id'], $customer_preference_data);
					}
				}

				if ($token) {
					$this->emailtemplate->data['unsubscribe_url'] = $this->emailtemplate->data['store_url'] . 'index.php?route=extension/module/emailtemplate_newsletter/unsubscribe&token=' . $token;
					$this->emailtemplate->data['preference_url'] = $this->emailtemplate->data['store_url'] . 'index.php?route=extension/module/emailtemplate_newsletter&token=' . $token;
				}
			}
		}

		$this->emailtemplate->data['html_cart_product'] = '';

		if (!empty($this->emailtemplate->data['emailtemplate']['cart_product']) && $customer_id) {
            $cart_products = $this->getCartProducts();

            if ($cart_products) {
                $this->emailtemplate->data['cart_subject_products'] = '';

                foreach ($cart_products as $order_product) {
                    if (strpos($this->emailtemplate->data['cart_subject_products'], $order_product['name']) === false) {
                        $this->emailtemplate->data['cart_subject_products'] .= ($this->emailtemplate->data['cart_subject_products'] ? ', ' : '') . strip_tags(html_entity_decode($order_product['name'], ENT_QUOTES, 'UTF-8'));
                    }
                }

                $this->emailtemplate->data['cart_subject_products'] = trim($this->emailtemplate->data['cart_subject_products']);

                $length = 32;

                if (strlen($this->emailtemplate->data['cart_subject_products']) > $length) {
                    $this->emailtemplate->data['cart_subject_products'] = substr($this->emailtemplate->data['cart_subject_products'], 0, strrpos(substr($this->emailtemplate->data['cart_subject_products'], 0, $length), ' ')) . '...';
                }

                if (!empty($this->emailtemplate->data['config']['cart_setting']['layout'])) {
                    $cart_products_layout = $this->emailtemplate->data['config']['cart_setting']['layout'];
                } else {
                    $cart_products_layout = 'clean';
                }

                $twig = $this->emailtemplate->getTemplateEngine();

                try {
					$file_path = $this->emailtemplate->getTemplatePath('order_products/' . $cart_products_layout . '.twig');

					$template = $twig->loadTemplate($file_path);

                    $template_data = $this->emailtemplate->language_data;
					$template_data['config'] = $this->emailtemplate->data['config'];
					$template_data['emailtemplate'] = $this->emailtemplate->data['emailtemplate'];
                    $template_data['key'] = 'cart_product';
                    $template_data['products'] = $cart_products;

                    $this->emailtemplate->data['html_cart_product'] = $template->render($template_data);
                } catch (Exception $e) {
					throw $e;
                }
            }
        }

		$this->emailtemplate->data['html_order_product'] = '';

		if ((!isset($this->request->post['order_summary']) && !empty($this->emailtemplate->data['emailtemplate']['order_product'])) || (!empty($this->request->post['order_summary']) && (!empty($this->request->post['order_summary_products']) || !empty($this->request->post['order_summary_vouchers'])))) {
            $ordered = array();

            if (isset($this->emailtemplate->data['products']) || isset($this->emailtemplate->data['vouchers'])) {
                if (isset($this->emailtemplate->data['products'])) {
                    $ordered['products'] = $this->emailtemplate->data['products'];
                }

                if (isset($this->emailtemplate->data['vouchers'])) {
                    $ordered['vouchers'] = $this->emailtemplate->data['vouchers'];
                }

                if (isset($this->emailtemplate->data['totals'])) {
                    $ordered['totals'] = $this->emailtemplate->data['totals'];
                }
            } else {
                $ordered = $this->getOrdered();
            }

			if ($ordered) {
                $this->emailtemplate->data['order_subject_products'] = '';

                if (!empty($ordered['products']) && is_array($ordered['products'])) {
					foreach ($ordered['products'] as $order_product) {
						if (strpos($this->emailtemplate->data['order_subject_products'], $order_product['name']) === false) {
							$this->emailtemplate->data['order_subject_products'] .= ($this->emailtemplate->data['order_subject_products'] ? ', ' : '') . strip_tags(html_entity_decode($order_product['name'], ENT_QUOTES, 'UTF-8'));
						}
					}
				}

                if (!empty($ordered['vouchers']) && is_array($ordered['vouchers'])) {
                    foreach ($ordered['vouchers'] as $order_voucher) {
                        if (strpos($this->emailtemplate->data['order_subject_products'], $order_voucher['description']) === false) {
                            $this->emailtemplate->data['order_subject_products'] .= ($this->emailtemplate->data['order_subject_products'] ? ', ' : '') . strip_tags(html_entity_decode($order_voucher['description'], ENT_QUOTES, 'UTF-8'));
                        }
                    }
                }

                if ($this->emailtemplate->data['order_subject_products']) {
					$this->emailtemplate->data['order_subject_products'] = trim($this->emailtemplate->data['order_subject_products']);

					$length = 32;

					if (strlen($this->emailtemplate->data['order_subject_products']) > $length) {
						$this->emailtemplate->data['order_subject_products'] = substr($this->emailtemplate->data['order_subject_products'], 0, strrpos(substr($this->emailtemplate->data['order_subject_products'], 0, $length), ' ')) . '...';
					}
				}

                if ($this->emailtemplate->data['emailtemplate']['key'] == 'order.update' && !empty($this->emailtemplate->data['config']['order_update']['layout'])) {
                    $order_products_layout = $this->emailtemplate->data['config']['order_update']['layout'];
                } elseif (in_array($this->emailtemplate->data['emailtemplate']['key'], array('order.confirm', 'order.customer', 'order.admin')) && !empty($this->emailtemplate->data['config']['order_products']['layout'])) {
                    $order_products_layout = $this->emailtemplate->data['config']['order_products']['layout'];
                } else {
                    $order_products_layout = 'default';
                }

                $twig = $this->emailtemplate->getTemplateEngine();

                try {
					$file_path = $this->emailtemplate->getTemplatePath('order_products/' . $order_products_layout . '.twig');

					$template = $twig->loadTemplate($file_path);

					$template->data = $this->emailtemplate->language_data;
					$template->data['config'] = $this->emailtemplate->data['config'];
					$template->data['emailtemplate'] = $this->emailtemplate->data['emailtemplate'];
					$template->data['key'] = 'order_product';

					if (isset($this->emailtemplate->data['order'])) {
						$template->data['order'] = $this->emailtemplate->data['order'];
					}

					// All vars that being with order_
					foreach($this->emailtemplate->data as $key => $val) {
						if (is_string($key) && strpos($key, 'order_') === 0) {
							$template->data[$key] = $val;
						}
					}

					if (isset($ordered['products'])) {
						$template->data['products'] = $ordered['products'];
					}
					if (!empty($ordered['vouchers']) && is_array($ordered['vouchers'])) {
						$template->data['vouchers'] = $ordered['vouchers'];
					}
					if (!empty($ordered['totals']) && is_array($ordered['totals'])) {
						$template->data['totals'] = $ordered['totals'];
					}

                    $this->emailtemplate->data['html_order_product'] = $template->render($template->data);
                } catch (Exception $e) {
					throw $e;
                }
            }
        }
    }

	/**
	 * Perform actions after email has been sent.
	 * @param $emailtemplate EmailTemplate
	 */
	public function sent($emailtemplate = false) {
		// Compatible with old code
		if (!$emailtemplate) {
			$emailtemplate = $this->emailtemplate;
		}
		if (!$emailtemplate || !$emailtemplate instanceof EmailTemplate || empty($emailtemplate->data['emailtemplate']) || (isset($emailtemplate->data['send_mail']) && !$emailtemplate->data['send_mail'])) {
			return false;
		}

		// Clear attachments
		if (isset($emailtemplate->data['emailtemplate_invoice_pdf']) && file_exists($emailtemplate->data['emailtemplate_invoice_pdf'])) {
			unlink($emailtemplate->data['emailtemplate_invoice_pdf']);
		}

		// Shortcodes
		if ($emailtemplate->data['emailtemplate']['shortcodes'] == 0) {
			$this->insertTemplateShortcodes($emailtemplate->data['emailtemplate']['emailtemplate_id'], $emailtemplate->data, $emailtemplate->language_data);
		}

		$this->recordProductsInShowcase();

		// Log
		if (!empty($emailtemplate->data['emailtemplate_log_id'])) {
			$log_data = array(
				'customer_group_id' => $emailtemplate->data['customer_group_id'],
				'customer_id' => isset($emailtemplate->data['customer_id']) ? $emailtemplate->data['customer_id'] : null,
				'emailtemplate_config_id' => $emailtemplate->data['config']['emailtemplate_config_id'],
				'emailtemplate_id' => $emailtemplate->data['emailtemplate']['emailtemplate_id'],
				'emailtemplate_key' => $emailtemplate->data['emailtemplate']['key'],
                'emailtemplate_log_cc' => is_array($emailtemplate->data['mail']['cc']) ? implode(', ', $emailtemplate->data['mail']['cc']) : $emailtemplate->data['mail']['cc'],
                'emailtemplate_log_bcc' => is_array($emailtemplate->data['mail']['bcc']) ? implode(', ', $emailtemplate->data['mail']['bcc']) : $emailtemplate->data['mail']['bcc'],
				'emailtemplate_log_content' => $emailtemplate->getHtmlContent(),
				'emailtemplate_log_enc' => $emailtemplate->data['emailtemplate_log_enc'],
				'emailtemplate_log_from' => $emailtemplate->data['mail']['from'],
				'emailtemplate_log_heading' => $emailtemplate->data['emailtemplate']['heading'],
				'emailtemplate_log_is_sent' => (!empty($emailtemplate->data['mail_queue']) || (!isset($emailtemplate->data['mail_queue']) && !empty($emailtemplate->data['mail']['mail_queue']))) ? 0 : 1,
				'emailtemplate_log_reply_to' => $emailtemplate->data['mail']['reply_to'],
				'emailtemplate_log_sender' => $emailtemplate->data['mail']['sender'],
				'emailtemplate_log_subject' => $emailtemplate->data['mail']['subject'],
				'emailtemplate_log_to' => $emailtemplate->data['mail']['to'],
				'language_id' => $emailtemplate->data['language_id'],
				'order_id' => isset($emailtemplate->data['order_id']) ? $emailtemplate->data['order_id'] : 0,
				'store_id' => $emailtemplate->data['store_id']
			);

			$this->updateTemplateLog($emailtemplate->data['emailtemplate_log_id'], $log_data);
		}
	}

	private function getTax() {
		if (!$this->tax) {
			$this->tax = new Cart\Tax($this->registry);

			if (isset($this->emailtemplate->data['shipping_country_id']) && isset($this->emailtemplate->data['shipping_zone_id'])) {
				$this->tax->setShippingAddress($this->emailtemplate->data['shipping_country_id'], $this->emailtemplate->data['shipping_zone_id']);
			} elseif ($this->emailtemplate->data['store_tax_default'] == 'shipping') {
				$this->tax->setShippingAddress($this->emailtemplate->data['store_country_id'], $this->config->get('config_zone_id'));
			}

			if (isset($this->emailtemplate->data['shipping_country_id']) && isset($this->emailtemplate->data['shipping_zone_id'])) {
				$this->tax->setPaymentAddress($this->emailtemplate->data['payment_country_id'], $this->emailtemplate->data['payment_zone_id']);
			} elseif ($this->emailtemplate->data['store_tax_default'] == 'payment') {
				$this->tax->setPaymentAddress($this->emailtemplate->data['store_country_id'], $this->emailtemplate->data['store_zone_id']);
			}

			$this->tax->setStoreAddress($this->emailtemplate->data['store_country_id'], $this->emailtemplate->data['store_zone_id']);
		}

		return $this->tax;
	}

	public function getShowcase($customer_id, $type = '', $limit = 6, $related = false, $selection = null) {
		$showcase_products = array();

		$this->load->model('extension/module/emailtemplate/product');
		$this->load->model('tool/image');

		if (!$this->currency) {
			$this->currency = new Cart\Currency($this->registry);
		}

		if (!empty($this->emailtemplate->data['config']['showcase_setting']['price_tax'])) {
			$tax = $this->getTax();
		}

		$products = array();
		$order_products = array();

		$store_id = $this->emailtemplate->data['store_id'];
		$customer_group_id = $this->emailtemplate->data['customer_group_id'];
		$language_id = $this->emailtemplate->data['language_id'];

        $config_customer_price = isset($this->emailtemplate->data['store_customer_price']) ? $this->emailtemplate->data['store_customer_price'] : 0;
        $config_currency = isset($this->emailtemplate->data['store_currency']) ? $this->emailtemplate->data['store_currency'] : $this->config->get('config_currency');

		$customer_info = $this->emailtemplate->getCustomer();

		if ($this->config->get('module_emailtemplate_newsletter_showcase') && $customer_info && (!isset($customer_info['newsletter_preference']['showcase']) || $customer_info['newsletter_preference']['showcase'] == 0)) {
			return $showcase_products;
		}

        $product_condition = array('language_id' => $language_id, 'store_id' => $store_id, 'customer_group_id' => $customer_group_id, 'customer_id' => $customer_id);

        if (!empty($this->emailtemplate->data['config']['showcase_setting']['min_stock'])) {
            $product_condition['min_stock'] = $this->emailtemplate->data['config']['showcase_setting']['min_stock'];
        }

		if ($related && $customer_info && !empty($this->emailtemplate->data['order_id'])) {
            $this->load->model('sale/order');

			$order_info = $this->model_sale_order->getOrder($this->emailtemplate->data['order_id']);

			if ($order_info && ($order_info['customer_id'] == $customer_info['customer_id'] || $order_info['email'] == $customer_info['email'])) {
				$result = $this->model_sale_order->getOrderProducts($this->emailtemplate->data['order_id']);

				if ($result) {
					foreach ($result as $row) {
						$order_products[$row['product_id']] = $row;
					}

					foreach ($result as $row) {
						$result2 = $this->model_extension_module_emailtemplate_product->getProductRelated($row['product_id'], $product_condition);

						if ($result2) {
							foreach ($result2 as $row2) {
								if (!isset($products[$row2['product_id']]) && !isset($order_products[$row2['product_id']]) && (empty($this->emailtemplate->data['config']['showcase_setting']['min_stock']) || $row2['stock'] > 0)) {
									$products[$row2['product_id']] = $row2;
								}
							}
						}
					}
				}
			}
		}

        if (count($products) < $limit) {
			$result = false;

			switch($type) {
				case 'products':
					if ($selection) {
						$result = array();
						$selection = explode(',', $selection);
						foreach($selection as $product_id) {
							if ($product_id && !isset($products[$product_id])) {
								$row = $this->model_extension_module_emailtemplate_product->getProduct($product_id, $product_condition);
								if ($row) {
									$result[] = $row;
								}
							}
						}
					}
					break;

				case 'bestsellers':
					$result = $this->model_extension_module_emailtemplate_product->getBestSellerProducts($limit, $product_condition);
					break;

				case 'specials':
					$result = $this->model_extension_module_emailtemplate_product->getProductSpecials($limit, $product_condition);
					break;

				case 'popular':
					$result = $this->model_extension_module_emailtemplate_product->getPopularProducts($limit, $product_condition);
					break;

				case 'random':
					$result = $this->model_extension_module_emailtemplate_product->getRandomProducts($limit, $product_condition);
					break;

				case 'latest':
				default:
					$result = $this->model_extension_module_emailtemplate_product->getLatestProducts($limit, $product_condition);
					break;
			}

			if(!empty($result)){
				foreach($result as $row) {
					if ($limit && count($products) >= $limit) {
						break;
					}
					if (!isset($products[$row['product_id']]) && !isset($order_products[$row['product_id']])) {
						$products[$row['product_id']] = $row;
					}
				}
			}
		}

		if (count($products) > $limit) {
			rsort($products);
			$products = array_slice($products, -$limit, $limit, true);
		}

        if (!empty($this->emailtemplate->data['config']['showcase_setting']['description'])) {
            $description_limit = $this->emailtemplate->data['config']['showcase_setting']['description'];
        } else {
            $description_limit = 0;
        }

		if (!empty($products)) {
			foreach($products as $product) {
				if (!isset($product['product_id'])) continue;

				if ($product['image'] && !empty($this->emailtemplate->data['config']['showcase_setting']['image'])) {
					$image_width = !empty($this->emailtemplate->data['config']['showcase_setting']['image_width']) ? $this->emailtemplate->data['config']['showcase_setting']['image_width'] : 100;
					$image_height = !empty($this->emailtemplate->data['config']['showcase_setting']['image_height']) ? $this->emailtemplate->data['config']['showcase_setting']['image_height'] : 100;

                    $product_image_url = $this->model_tool_image->resize($product['image'], $image_width, $image_height);

                    // Fix spaces etc
                    if ($product_image_url && strpos($product_image_url, '%') === false) {
                        $url_parse = parse_url($product_image_url);
                        if (!empty($url_parse["path"])) {
                            $url_path = trim(str_replace(basename($_SERVER['SCRIPT_NAME']), '', $url_parse["path"]), '/');
                            $product_image_url = (isset($url_parse['scheme'], $url_parse['host']) ? $url_parse['scheme'] . '://' . $url_parse['host'] : '') . '/' . implode('/', array_map('rawurlencode', explode('/', $url_path)));
                        }
                    }

                    // Replace admin url with store
                    $product_image_url = str_replace(array(HTTP_SERVER, HTTPS_SERVER), $this->emailtemplate->data['store_url'], $product_image_url);

					$product['image'] = $product_image_url;
				}

				if ((float)$product['price'] && !$config_customer_price) {
					if (isset($tax)) {
						$price = $this->currency->format($tax->calculate($product['price'], $product['tax_class_id'], $this->emailtemplate->data['store_tax']), $config_currency);
					} else {
						$price = $this->currency->format($product['price'], $config_currency);
					}
				} else {
					$price = false;
				}

				if (!$config_customer_price && (float)$product['special']) {
					if (isset($tax)) {
						$special = $this->currency->format($tax->calculate($product['special'], $product['tax_class_id'], $this->emailtemplate->data['store_tax']), $config_currency);
					} else {
						$special = $this->currency->format($product['special'], $config_currency);
					}
				} else {
					$special = false;
				}

				$url = $this->emailtemplate->data['store_url'] . 'index.php?route=product/product&product_id=' . $product['product_id'];

				$showcase = array(
					'product_id' => $product['product_id'],
					'image' => $product['image'],
					'name' => $product['name'],
					'rating' => round($product['rating']),
					'reviews' => $product['reviews'] ? $product['reviews'] : 0,
					'name_short' => $this->_truncate($product['name'], 30, ''),
					'price' => $price,
					'special' => $special,
					'url' => $url
				);

                if ($description_limit) {
                    $showcase['description'] = $this->_truncate($product['description'], $description_limit);
                } else {
                    $showcase['description'] = '';
                }

				if ($showcase['name_short'] != $showcase['name']) {
					$showcase['preview'] = $showcase['name'] . ' - ' . $showcase['description'];
				} else {
					$showcase['preview'] = $showcase['description'];
				}

				$showcase_products[] = $showcase;
			}
		}

		return $showcase_products;
	}

    protected function getCartProducts() {
		$this->load->model('extension/module/emailtemplate/product');
		$this->load->model('tool/image');

        $customer_info = $this->emailtemplate->getCustomer();

        if (!$customer_info) {
            return false;
        }

        $carts = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE api_id = 0 AND customer_id = '" . (int)$customer_info['customer_id'] . "'");

        if (!$carts->rows) {
            return false;
        }

        if (isset($this->session->data['currency'])) {
            $currency_code = $this->session->data['currency'];
        } else {
            $currency_code = $this->config->get('config_currency');
        }

		if (!$this->currency) {
			$this->currency = new Cart\Currency($this->registry);
		}

		$currency_value = $this->currency->getValue($currency_code);

		$this->getTax();

        $product_condition = array('language_id' => $customer_info['language_id'], 'store_id' => $customer_info['store_id'], 'customer_group_id' => $customer_info['customer_group_id']);

        if (!empty($this->emailtemplate->data['config']['showcase_setting']['min_stock'])) {
            $product_condition['min_stock'] = $this->emailtemplate->data['config']['showcase_setting']['min_stock'];
        }

        $products = array();

        foreach($carts->rows as $cart) {
            $product_info = $this->model_extension_module_emailtemplate_product->getProduct($cart['product_id'], $product_condition);

            if (!$product_info) {
                continue;
            }

            $stock = false;

            if ($product_info && ($cart['quantity'] > 0)) {
                $option_price = 0;
                $option_points = 0;
                $option_weight = 0;

                $option_data = array();

                foreach (json_decode($cart['option']) as $product_option_id => $value) {
                    $option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$cart['product_id'] . "' AND od.language_id = '" . (int)$customer_info['language_id'] . "'");

                    if ($option_query->num_rows) {
                        if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio') {
                            $option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$customer_info['language_id'] . "'");

                            if ($option_value_query->num_rows) {
                                if ($option_value_query->row['price_prefix'] == '+') {
                                    $option_price += $option_value_query->row['price'];
                                } elseif ($option_value_query->row['price_prefix'] == '-') {
                                    $option_price -= $option_value_query->row['price'];
                                }

                                if ($option_value_query->row['points_prefix'] == '+') {
                                    $option_points += $option_value_query->row['points'];
                                } elseif ($option_value_query->row['points_prefix'] == '-') {
                                    $option_points -= $option_value_query->row['points'];
                                }

                                if ($option_value_query->row['weight_prefix'] == '+') {
                                    $option_weight += $option_value_query->row['weight'];
                                } elseif ($option_value_query->row['weight_prefix'] == '-') {
                                    $option_weight -= $option_value_query->row['weight'];
                                }

                                if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $cart['quantity']))) {
                                    $stock = false;
                                }

                                $option_row = array(
                                    'product_option_id' => $product_option_id,
                                    'product_option_value_id' => $value,
                                    'option_id' => $option_query->row['option_id'],
                                    'option_value_id' => $option_value_query->row['option_value_id'],
                                    'name' => $option_query->row['name'],
                                    'value' => $option_value_query->row['name'],
                                    'type' => $option_query->row['type'],
                                    'quantity' => $option_value_query->row['quantity'],
                                    'subtract' => $option_value_query->row['subtract'],
                                );

                                if (!empty($this->emailtemplate->data['config']['cart_setting']['price'])) {
									if ($this->tax) {
										$option_row['price'] = $this->currency->format($this->tax->calculate($option_value_query->row['price'], $product_info['tax_class_id'], $this->emailtemplate->data['store_tax']), $currency_code, $currency_value);
									} else {
										$option_row['price'] = $this->currency->format($option_value_query->row['price'], $currency_code, $currency_value);
									}
                                }

                                $option_data[] = $option_row;
                            }
                        } elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
                            foreach ($value as $product_option_value_id) {
                                $option_value_query = $this->db->query("SELECT pov.option_value_id, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix, ovd.name FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (pov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$customer_info['language_id'] . "'");

                                if ($option_value_query->num_rows) {
                                    if ($option_value_query->row['price_prefix'] == '+') {
                                        $option_price += $option_value_query->row['price'];
                                    } elseif ($option_value_query->row['price_prefix'] == '-') {
                                        $option_price -= $option_value_query->row['price'];
                                    }

                                    if ($option_value_query->row['points_prefix'] == '+') {
                                        $option_points += $option_value_query->row['points'];
                                    } elseif ($option_value_query->row['points_prefix'] == '-') {
                                        $option_points -= $option_value_query->row['points'];
                                    }

                                    if ($option_value_query->row['weight_prefix'] == '+') {
                                        $option_weight += $option_value_query->row['weight'];
                                    } elseif ($option_value_query->row['weight_prefix'] == '-') {
                                        $option_weight -= $option_value_query->row['weight'];
                                    }

                                    if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $cart['quantity']))) {
                                        $stock = false;
                                    }

                                    $option_row = array(
                                        'product_option_id' => $product_option_id,
                                        'product_option_value_id' => $product_option_value_id,
                                        'option_id' => $option_query->row['option_id'],
                                        'option_value_id' => $option_value_query->row['option_value_id'],
                                        'name' => $option_query->row['name'],
                                        'value' => $option_value_query->row['name'],
                                        'type' => $option_query->row['type'],
                                        'quantity' => $option_value_query->row['quantity'],
                                        'subtract' => $option_value_query->row['subtract'],
                                    );

                                    if (!empty($this->emailtemplate->data['config']['cart_setting']['price'])) {
										if ($this->tax) {
											$option_row['price'] = $this->currency->format($this->tax->calculate($option_value_query->row['price'], $product_info['tax_class_id'], $this->emailtemplate->data['store_tax']), $currency_code, $currency_value);
										} else {
											$option_row['price'] = $this->currency->format($option_value_query->row['price'], $currency_code, $currency_value);
										}
                                    }

                                    $option_data[] = $option_row;
                                }
                            }
                        } elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
                            $option_data[] = array(
                                'product_option_id' => $product_option_id,
                                'product_option_value_id' => '',
                                'option_id' => $option_query->row['option_id'],
                                'option_value_id' => '',
                                'name' => $option_query->row['name'],
                                'value' => $value,
                                'type' => $option_query->row['type'],
                                'quantity' => '',
                                'subtract' => '',
                            );
                        }
                    }
                }

                $price = $product_info['price'];

                // Product Discounts
                $discount_quantity = 0;

                foreach ($carts->rows as $cart_2) {
                    if ($cart_2['product_id'] == $cart['product_id']) {
                        $discount_quantity += $cart_2['quantity'];
                    }
                }

                $product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$cart['product_id'] . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND quantity <= '" . (int)$discount_quantity . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");

                if ($product_discount_query->num_rows) {
                    $price = $product_discount_query->row['price'];
                }

                // Product Specials
                $product_special_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$cart['product_id'] . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");

                if ($product_special_query->num_rows) {
                    $price = $product_special_query->row['price'];
                }

                // Reward Points
                $product_reward_query = $this->db->query("SELECT points FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$cart['product_id'] . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'");

                if ($product_reward_query->num_rows) {
                    $reward = $product_reward_query->row['points'];
                } else {
                    $reward = 0;
                }

                // Stock
                if (!$product_info['quantity'] || ($product_info['quantity'] < $cart['quantity'])) {
                    $stock = false;
                }

                $row = array(
                    'cart_id' => $cart['cart_id'],
                    'product_id' => $product_info['product_id'],
                    'name' => $product_info['name'],
                    'quantity' => $cart['quantity'],
                    'minimum' => $product_info['minimum'],
                    'subtract' => $product_info['subtract'],
                    'stock' => $stock,
                    'url' => $this->emailtemplate->data['store_url'] . 'index.php?route=product/product&product_id=' . $cart['product_id'],
                    'reward' => $reward * $cart['quantity'],
                    'points' => ($product_info['points'] ? ($product_info['points'] + $option_points) * $cart['quantity'] : 0),
                    'tax_class_id' => $product_info['tax_class_id'],
                );

                if (!empty($this->emailtemplate->data['config']['cart_setting']['image']) && !empty($product_info['image'])) {
                    $image_width = isset($this->emailtemplate->data['config']['cart_setting']['image_width']) ? $this->emailtemplate->data['config']['cart_setting']['image_width'] : 100;
                    $image_height = isset($this->emailtemplate->data['config']['cart_setting']['image_height']) ? $this->emailtemplate->data['config']['cart_setting']['image_height'] : 100;

                    $row['image'] = $this->model_tool_image->resize($product_info['image'], $image_width, $image_height);

                    // Fix spaces etc
                    if (strpos($row['image'], '%') !== false) {
                        $url_parse = parse_url($row['image']);
                        $url_path = trim(str_replace(basename($_SERVER['SCRIPT_NAME']), '', $url_parse["path"]), '/');
                        $row['image'] = $url_parse['scheme'] . '://' . $url_parse['host'] . '/' . implode('/', array_map('rawurlencode', explode('/', $url_path)));
                    }

                    if (substr($row['image'], 0, 4) != 'http') {
                        $row['image'] = $this->emailtemplate->data['store_url'] . ltrim($row['image'], '/');
                    } else {
                        // Replace admin url with store
                        $row['image'] = str_replace(array(HTTP_SERVER, HTTPS_SERVER), $this->emailtemplate->data['store_url'], $row['image']);
                    }
                }

                if (!empty($this->emailtemplate->data['config']['cart_setting']['price']) && (float)($price + $option_price)) {
					if ($this->tax) {
						$row['price'] = $this->currency->format($this->tax->calculate($price + $option_price, $product_info['tax_class_id'], $this->emailtemplate->data['store_tax']), $currency_code, $currency_value);
					} else {
						$row['price'] = $this->currency->format($price + $option_price, $currency_code, $currency_value);
					}

					if ($this->tax) {
						$row['total'] = $this->currency->format($this->tax->calculate(($price + $option_price) * $cart['quantity'], $product_info['tax_class_id'], $this->emailtemplate->data['store_tax']), $currency_code, $currency_value);
					} else {
						$row['total'] = $this->currency->format(($price + $option_price) * $cart['quantity'], $currency_code, $currency_value);
					}
                }

                if (!empty($this->emailtemplate->data['config']['cart_setting']['model'])) {
                    $row['model'] = $product_info['model'];
                }

				if (!empty($this->emailtemplate->data['config']['order_update']['sku'])) {
					$row['sku'] = $product_info['sku'];
				}

                if (!empty($this->emailtemplate->data['config']['cart_setting']['option'])) {
                    $row['option'] = $option_data;
                }

                if (!empty($this->emailtemplate->data['config']['cart_setting']['rating'])) {
                    $row['rating'] = $product_info['rating'];
                    $row['reviews'] = $product_info['reviews'];
                }

                if (!empty($this->emailtemplate->data['config']['cart_setting']['description']) && $product_info['description']) {
                    $row['description'] = utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, (int)$this->emailtemplate->data['config']['cart_setting']['description']) . '...';
                }

                $products[] = $row;
            }
        }

        return $products;
    }

    protected function getOrdered() {
        if (empty($this->emailtemplate->data['order_id'])) {
			return false;
		}

		$this->load->model('sale/order');
        $this->load->model('extension/module/emailtemplate/product');
        $this->load->model('tool/image');
        $this->load->model('tool/upload');

        $order_info = $this->model_sale_order->getOrder($this->emailtemplate->data['order_id']);

        if (!$order_info || ($order_info['customer_id'] && $this->emailtemplate->data['customer_id'] != $order_info['customer_id'])) {
            return false;
        }

		if (!$this->currency) {
			$this->currency = new Cart\Currency($this->registry);
		}

		$this->getTax();

        $product_condition = array('language_id' => $order_info['language_id'], 'store_id' => $order_info['store_id'], 'customer_group_id' => $order_info['customer_group_id'], 'customer_id' => $order_info['customer_id']);

        if (!empty($this->emailtemplate->data['config']['showcase_setting']['min_stock'])) {
            $product_condition['min_stock'] = $this->emailtemplate->data['config']['showcase_setting']['min_stock'];
        }

        $products = array();
        $totals = array();
        $vouchers = array();

        $order_products = $this->model_sale_order->getOrderProducts($order_info['order_id']);

        if ($order_products) {
			if (empty($order_product_query)) {
				$order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_info['order_id'] . "'");
			}

			if (!empty($this->request->post['order_summary_products'])) {
				$order_summary_list = explode(',', $this->request->post['order_summary_products']);
			} else {
				$order_summary_list = false;
			}

			if (!empty($this->emailtemplate->data['config']['order_products']['option_length'])) {
				$option_length = $this->emailtemplate->data['config']['order_products']['option_length'];
			} else {
				$option_length = 120;
			}

			if ($order_product_query->rows) {
				foreach ($order_product_query->rows as $product) {

					if ($order_summary_list && !in_array($product['product_id'], $order_summary_list)) {
						continue;
					}

					$product_info = $this->model_extension_module_emailtemplate_product->getProduct($product['product_id'], $product_condition);

					if (!$product_info) {
					    continue;
                    }

					$option_data = array();

					$option_price = 0;

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

						if ($option['price_prefix'] == '+') {
							$option_price += $option['price'];
						} elseif ($option['price_prefix'] == '-') {
							$option_price -= $option['price'];
						}

						$option_row = array(
							'name' => $option['name'],
							'value' => utf8_strlen($value) > $option_length ? utf8_substr($value, 0, $option_length) . '..' : $value
						);

						if ((float)$option['price'] && !empty($this->emailtemplate->data['config']['order_update']['price'])) {
							$option_row['price_prefix'] = $option['price_prefix'];
                            $option_row['price'] = $this->currency->format($option['price'], $order_info['currency_code'], $order_info['currency_value']);
						}

						$option_data[] = $option_row;
					}

					$row = array(
						'product_id' => $product_info['product_id'],
						'name' => $product['name'],
						'model' => $product['model'],
						'quantity' => $product['quantity'],
						'url' => $this->emailtemplate->data['store_url'] . 'index.php?route=product/product&product_id=' . $product['product_id'],
						'option' => $option_data,
					);

					if (!empty($this->emailtemplate->data['config']['order_update']['image']) && !empty($product_info['image'])) {
						$image_width = !empty($this->emailtemplate->data['config']['order_update']['image_width']) ? $this->emailtemplate->data['config']['order_update']['image_width'] : 100;
						$image_height = !empty($this->emailtemplate->data['config']['order_update']['image_height']) ? $this->emailtemplate->data['config']['order_update']['image_height'] : 100;

						$row['image'] = $this->model_tool_image->resize($product_info['image'], $image_width, $image_height);

						// Fix spaces etc
						if (strpos($row['image'], '%') !== false) {
							$url_parse = parse_url($row['image']);
							$url_path = trim(str_replace(basename($_SERVER['SCRIPT_NAME']), '', $url_parse["path"]), '/');
							$row['image'] = $url_parse['scheme'] . '://' . $url_parse['host'] . '/' . implode('/', array_map('rawurlencode', explode('/', $url_path)));
						}

						if (substr($row['image'], 0, 4) != 'http') {
							$row['image'] = $this->emailtemplate->data['store_url'] . ltrim($row['image'], '/');
						} else {
							// Replace admin url with store
							$row['image'] = str_replace(array(HTTP_SERVER, HTTPS_SERVER), $this->emailtemplate->data['store_url'], $row['image']);
						}
					}

					if (!empty($this->emailtemplate->data['config']['order_update']['price']) && (float)($product['price'] + $option_price)) {
                        if ($this->config->get('config_tax') && !empty($product['tax'])) {
                            $row['price'] = $this->currency->format($product['price'] + $product['tax'], $order_info['currency_code'], $order_info['currency_value']);
                            $row['total'] = $this->currency->format($product['total'] + ($product['tax'] * $product['quantity']), $order_info['currency_code'], $order_info['currency_value']);
                        } else {
                            $row['price'] = $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value']);
                            $row['total'] = $this->currency->format($product['total'], $order_info['currency_code'], $order_info['currency_value']);
                        }
					}

					if (!empty($this->emailtemplate->data['config']['order_update']['model'])) {
						$row['model'] = $product_info['model'];
					}

					if (!empty($this->emailtemplate->data['config']['order_update']['sku'])) {
						$row['sku'] = $product_info['sku'];
					}

					if (!empty($this->emailtemplate->data['config']['order_update']['option'])) {
						$row['option'] = $option_data;
					}

					if (!empty($this->emailtemplate->data['config']['order_update']['rating'])) {
						$row['rating'] = $product_info['rating'];
						$row['reviews'] = $product_info['reviews'];
					}

					if (!empty($this->emailtemplate->data['config']['order_update']['description']) && $product_info['description']) {
						$row['description'] = utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, (int)$this->emailtemplate->data['config']['order_update']['description']) . '...';
					}

					$products[] = $row;
				}
			}
        }

		$order_vouchers = $this->model_sale_order->getOrderVouchers($order_info['order_id']);

		if ($order_vouchers) {
			$order_summary_list = false;

			if (!empty($this->request->post['order_summary_vouchers'])) {
				$order_summary_list = explode(',', $this->request->post['order_summary_vouchers']);
			}

			foreach ($order_vouchers as $voucher) {
				if ($order_summary_list && !in_array($voucher['voucher_id'], $order_summary_list)) {
					continue;
				}

				$vouchers[] = array(
					'description' => $voucher['description'],
					'amount' => !empty($voucher['text']) ? $voucher['text'] : $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']),
				);
			}
		}

		if (!empty($this->emailtemplate->data['config']['order_update']['price'])) {
			$order_totals = $this->model_sale_order->getOrderTotals($order_info['order_id']);

			if ($order_totals) {
				foreach ($order_totals as $total) {
					$totals[] = array(
						'title' => $total['title'],
						'text' => !empty($total['text']) ? $total['text'] : $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
					);
				}
			}
		}

        return array('products' => $products, 'totals' => $totals, 'vouchers' => $vouchers);
    }

	/**
	 * Record Products In Showcase
	 *
	 */
	public function recordProductsInShowcase() {
		if (empty($this->emailtemplate->data['config']) ||
			empty($this->emailtemplate->data['config']['showcase_setting']) ||
			empty($this->emailtemplate->data['config']['showcase_setting']['cycle']) ||
			empty($this->emailtemplate->data['showcase_selection']) ||
			empty($this->emailtemplate->data['customer_id'])) {
			return false;
		}

		$customer_id = $this->emailtemplate->data['customer_id'];

		foreach($this->emailtemplate->data['showcase_selection'] as $showcase_selection) {
			if (empty($showcase_selection['product_id']))
				continue;

			$product_id = $showcase_selection['product_id'];

			$query = "SELECT 1 FROM " . DB_PREFIX . "emailtemplate_showcase_log WHERE `customer_id` = '" . (int)$customer_id . "' AND product_id = '" . (int)$product_id . "'";
			$result = $this->db->query($query);

			if ($result->row) {
				$query = "UPDATE " . DB_PREFIX . "emailtemplate_showcase_log SET `emailtemplate_showcase_log_count` = `emailtemplate_showcase_log_count` + 1, emailtemplate_showcase_log_modified = NOW() WHERE `customer_id` = '" . (int)$customer_id . "' AND product_id = '" . (int)$product_id . "'";
			} else {
				$query = "INSERT INTO " . DB_PREFIX . "emailtemplate_showcase_log (`customer_id`, `product_id`, emailtemplate_showcase_log_count, emailtemplate_showcase_log_modified) VALUES ('" . (int)$customer_id . "', '" . (int)$product_id . "', 1, NOW())";
			}

			$this->db->query($query);
		}

		return true;
	}

	/**
	 * Get Email Template Config
	 *
	 * @param int||array $identifier
	 * @return array
	 */
	public function getConfig($data = false) {
		$where = array();

		if (is_array($data)) {
			if (isset($data['store_id'])) {
				$where[] = "`store_id` = '". (int)$data['store_id'] ."'";
			}
			if (isset($data['language_id'])) {
				$where[] = "(`language_id` = '". (int)$data['language_id']."' OR `language_id` = 0)";
			}
		} elseif (is_numeric($data)) {
			$where[] = "`emailtemplate_config_id` = '" . (int)$data . "'";
		}

		$query = "SELECT * FROM " . DB_PREFIX . "emailtemplate_config";

		if (!empty($where)) {
			$query .= " WHERE " . implode(" AND ", $where);
		}

		$query .= " ORDER BY `language_id` DESC LIMIT 1";

		$result = $this->_fetch($query, 'config');

		if ($result) {
			$return = $result->row;

			foreach (EmailTemplateConfigDAO::describe() as $col => $type) {
				if (!isset($return[$col])) continue;

				if ($type == EmailTemplateConfigDAO::SERIALIZE && $return[$col]) {
					$unserialized = @unserialize(base64_decode($return[$col]));
					$return[$col] = ($unserialized !== false) ? $unserialized : $return[$col];
				}
			}

			return $return;
		}
	}

	/**
	 * Return array of configs
	 * @param array - $data
	 */
	public function getConfigs($data = array()) {
		$where = array();

		if (isset($data['language_id'])) {
			$where[] = "AND ec.`language_id` = '".(int)$data['language_id']."'";
		} elseif (isset($data['_language_id'])) {
			$where[] = "AND ec.`language_id` IN(0, '".(int)$data['_language_id']."')";
		}

		if (isset($data['store_id'])) {
			$where[] = "AND ec.`store_id` = '".(int)$data['store_id']."'";
		} elseif (isset($data['_store_id'])) {
			$where[] = "AND ec.`store_id` IN(0, '".(int)$data['_store_id']."')";
		}

		if (isset($data['customer_group_id'])) {
			$where[] = "AND ec.`customer_group_id` = '".(int)$data['customer_group_id']."'";
		} elseif (isset($data['_customer_group_id'])) {
			$where[] = "OR ec.`customer_group_id` = '".(int)$data['_customer_group_id']."'";
		}

		if (isset($data['emailtemplate_config_id'])) {
			if (is_array($data['emailtemplate_config_id'])) {
				$ids = array();
				foreach($data['emailtemplate_config_id'] as $id) { $ids[] = (int)$id; }
				$where[] = "AND ec.`emailtemplate_config_id` IN('".implode("', '", $ids)."')";
			} else {
				$where[] = "AND ec.`emailtemplate_config_id` = '".(int)$data['emailtemplate_config_id']."'";
			}
		}

		if (isset($data['not_emailtemplate_config_id'])) {
			if (is_array($data['not_emailtemplate_config_id'])) {
				$ids = array();
				foreach($data['not_emailtemplate_config_id'] as $id) { $ids[] = (int)$id; }
				$where[] = "AND ec.`emailtemplate_config_id` NOT IN('".implode("', '", $ids)."')";
			} else {
				$where[] = "AND ec.`emailtemplate_config_id` != '".(int)$data['not_emailtemplate_config_id']."'";
			}
		}

		$query = "SELECT ec.* FROM " . DB_PREFIX . "emailtemplate_config ec";
		if (!empty($where)) {
			$query .= ' WHERE ' . ltrim(implode(' ', $where), 'AND');
		}

		$sort_data = array(
			'emailtemplate_config_id',
			'emailtemplate_config_name',
			'emailtemplate_config_modified',
			'store_id',
			'language_id',
			'customer_group_id'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$query .= " ORDER BY `" . $data['sort'] . "`";
		} else {
			$query .= " ORDER BY ec.`emailtemplate_config_name`";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$query .= " DESC";
		} else {
			$query .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if (!isset($data['start']) || $data['start'] < 0) {
				$data['start'] = 0;
			}
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
			$query .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$result = $this->_fetch($query, 'configs');

		$rows = $result->rows;

		if ($rows) {
			$cols = EmailTemplateConfigDAO::describe();

			foreach ($rows as $key => &$row) {
				foreach ($row as $col => $val) {
					if (isset($cols[$col]) && $cols[$col] == EmailTemplateConfigDAO::SERIALIZE) {
						if ($val) {
							$unserialized = @unserialize(base64_decode($row[$col]));
							$row[$col] = ($unserialized !== false) ? $unserialized : $row[$col];
						}
					}
				}
			}
		}

		return $rows;
	}

	/**
	 * Add new Email Template Config by cloning an existing one.
	 *
	 * @return new row identifier
	 */
	public function cloneConfig($id, $data = array()) {
		$id = (int)$id;
		$inserts = array();
		$cols = EmailTemplateConfigDAO::describe("emailtemplate_config_id", "store_id", "language_id", "customer_group_id");

		if (isset($data['store_id'])) {
			$store_id = (int)$data['store_id'];
		} else {
			$store_id = null;
		}

		if (isset($data['language_id'])) {
			$language_id = (int)$data['language_id'];
		} else {
			$language_id = 0;
		}

		if (isset($data['customer_group_id'])) {
			$customer_group_id = (int)$data['customer_group_id'];
		} else {
			$customer_group_id = 0;
		}

		$colsInsert = '';
		foreach($cols as $col => $type) {
			if (!array_key_exists($col, $data)) {
				$value = "`" . $this->db->escape($col) . "`";
			} else {
				switch ($type) {
					case EmailTemplateAbstract::INT:
						if (strtoupper($data[$col]) == 'NULL') {
							$value = 'NULL';
						} else {
							$value = (int)$data[$col];
						}
						break;
					case EmailTemplateAbstract::FLOAT:
						$value = floatval($data[$col]);
						break;
					case EmailTemplateAbstract::DATE_NOW:
						$value = 'NOW()';
						break;
					case EmailTemplateAbstract::SERIALIZE:
						$value = base64_encode(serialize($data[$col]));
						break;
					default:
						$value = $this->db->escape($data[$col]);
				}
				$value = "'{$value}'";
			}

			$colsInsert .= "{$value}, ";
		}

		$stmnt = "INSERT INTO " . DB_PREFIX . "emailtemplate_config (".implode(', ', array_keys($cols)).", store_id, language_id, customer_group_id, emailtemplate_config_modified)
                  SELECT ".$colsInsert." '{$store_id}', '{$language_id}', '{$customer_group_id}', NOW() FROM " . DB_PREFIX . "emailtemplate_config WHERE emailtemplate_config_id = '". (int)$id . "'";
		$this->db->query($stmnt);

		$emailtemplate_config_id = $this->db->getLastId();

		/*$stmnt = "UPDATE " . DB_PREFIX . "emailtemplate_config SET emailtemplate_config_name = CONCAT(emailtemplate_config_name, ' - {$emailtemplate_config_id}') WHERE emailtemplate_config_id = '{$emailtemplate_config_id}'";
		$this->db->query($stmnt);*/

		$this->clear();

		return $emailtemplate_config_id;
	}

	/**
	 * Edit existing config
	 *
	 * @param int - emailtemplate.emailtemplate_id
	 * @param array - column => value
	 * @return int affected row count
	 */
	public function updateConfig($id, array $data) {
		if (empty($data) && !is_numeric($id)) return false;

		$cols = EmailTemplateConfigDAO::describe();
		$updates = $this->_build_query($cols, $data);
		if (!$updates) return false;

		$sql = "UPDATE " . DB_PREFIX . "emailtemplate_config SET ".implode(", ", $updates) . " WHERE emailtemplate_config_id = '". (int)$id . "'";
		$this->db->query($sql);

		$affected = $this->db->countAffected();

		if ($affected) {
			$this->db->query("UPDATE " . DB_PREFIX . "emailtemplate_config SET `emailtemplate_config_modified` = NOW() WHERE `emailtemplate_config_id` = '" . (int)$id . "'");
		}

        $this->clear();

        return ($affected > 0) ? $affected : false;
	}

	/**
	 * Restore config row
	 */
	public function restoreDefaultConfig() {
		$file = DIR_APPLICATION . 'model/extension/module/emailtemplate/install/install.config.sql';

		if (file_exists($file)) {
			$stmnts = $this->_parse_sql($file);

			if (isset($stmnts[2]) && substr($stmnts[2], 0, 11) == 'INSERT INTO') {
				$this->db->query($stmnts[2]);

				$this->load->library('emailtemplate');

				$this->load->model('setting/setting');

				$store_config = $this->model_setting_setting->getSetting("config", 0);

				$config_data = $this->model_extension_module_emailtemplate->getConfig(1);

				$config_data['emailtemplate_config_name'] = $this->config->get('config_name');
				$config_data['emailtemplate_config_version'] = EmailTemplate::$version;

				if (!empty($store_config['config_logo']) && file_exists(DIR_IMAGE . $store_config['config_logo'])) {
					$config_data['emailtemplate_config_logo'] = $store_config['config_logo'];

					list($config_data['emailtemplate_config_logo_width'], $config_data['emailtemplate_config_logo_height']) = getimagesize(DIR_IMAGE . $store_config['config_logo']);
				}

				$replace_language_vars = defined('REPLACE_LANGUAGE_VARIABLES') ? REPLACE_LANGUAGE_VARIABLES : true;

				if ($replace_language_vars) {
                    if (!empty($store_config['config_url'])) {
                        $store_url = $store_config['config_url'];
                    } else {
                        $store_url = HTTPS_CATALOG ? HTTPS_CATALOG : HTTP_CATALOG;
                    }

                    $store_replace = array(
                        '{{ store_address }}' => $store_config['config_address'],
                        '{{ store_url }}' => $store_url,
                        '{{ store_telephone }}' => $store_config['config_telephone'],
                        '{{ contact_url }}' => $store_url . 'index.php?route=information/contact'
                    );

                    foreach(array('emailtemplate_config_head_text', 'emailtemplate_config_header_text', 'emailtemplate_config_page_footer_text', 'emailtemplate_config_footer_text') as $var) {
                        if (!empty($config_data[$var])) {
                            if (is_array($config_data[$var])) {
                                foreach($config_data[$var] as $i => $val) {
                                    if (is_string($val)) {
                                        $config_data[$var][$i] = str_replace(array_keys($store_replace), array_values($store_replace), $val);
                                    }
                                }
                            } elseif (is_string($config_data[$var])) {
                                $config_data[$var] = str_replace(array_keys($store_replace), array_values($store_replace), $config_data[$var]);
                            }
                        }
                    }
				}

				$this->updateConfig(1, $config_data);

				return true;
			}
		}
	}

	/**
	 * Delete config row
	 *
	 * @param mixed array||int - emailtemplate.id
	 * @return int - row count effected
	 */
	public function deleteConfig($data) {
		$affected = 0;
		$ids = array();

		if (is_array($data)) {
			foreach($data as $item) {
				$ids[] = (int)$item;
			}
		} else {
			$ids[] = (int)$data;
		}

		if (count($ids)) {
			$queries = array();
			$queries[] = "DELETE FROM " . DB_PREFIX . "emailtemplate_config WHERE emailtemplate_config_id IN('".implode("', '", $ids)."')";
			if (array_search(1, $ids) === false) {
				$queries[] = "UPDATE " . DB_PREFIX . "emailtemplate SET emailtemplate_config_id = '' WHERE emailtemplate_config_id IN('".implode("', '", $ids)."')";
			}

			foreach($queries as $query) {
				$this->db->query($query);
				$affected += $this->db->countAffected();
			}

			$this->clear();

			if (array_search(1, $ids) !== false) {
				$this->restoreDefaultConfig();
			}
		}
		return $affected;
	}

    /**
     * @param int $power
     * @param array|string $conditions
     * @param array $template_data
     * @return bool|int
     */
    public function checkTemplateCondition($power, $conditions, $template_data) {
        if ($conditions) {
            if (!is_array($conditions)) {
                $unserialized = @unserialize(base64_decode($conditions));
                $conditions = ($unserialized !== false) ? $unserialized : $conditions;
            }

            if (is_array($conditions)) {
                foreach ($conditions as $condition) {
                    $power = $power << 1;
                    $key = trim($condition['key']);
                    $operator = html_entity_decode($condition['operator'], ENT_COMPAT, "UTF-8");

                    $passed = false;

                    // Special condition for chekcing manufacturer/category in products
                    if (!empty($template_data['products']) && is_array($template_data['products'])) {
                        if ($key == 'manufacturer') {
                            foreach ($template_data['products'] as $product) {
                                if (isset($product['manufacturer_id'])) {
                                    if ($product['manufacturer_id'] != $condition['value']) {
                                        $power += 1;
                                        $passed = true;
                                        break;
                                    }
                                } else {
                                    $this->load->model('extension/module/emailtemplate/product');

                                    $product_info = $this->model_extension_module_emailtemplate_product->getProduct($product['product_id']);

                                    if ($product_info && (
                                            ($operator == '==' && $product_info['manufacturer_id'] == $condition['value']) ||
                                            ($operator == '!=' && $product_info['manufacturer_id'] != $condition['value']))) {
                                        $power += 1;
                                        $passed = true;
                                        break;
                                    }
                                }
                            }
                        } elseif ($key == 'category') {
                            $this->load->model('catalog/product');

                            foreach ($template_data['products'] as $product) {
                                $product_categories = $this->model_catalog_product->getCategories($product['product_id']);

                                $categories = array();
                                foreach ($product_categories as $product_category) {
                                    $categories[] = $product_category['category_id'];
                                }

                                if ($categories && (
                                        ($operator == '==' && in_array($condition['value'], $categories)) ||
                                        ($operator == '!=' && !in_array($condition['value'], $categories)))) {
                                    $power += 1;
                                    $passed = true;
                                    break;
                                }
                            }
                        }

                        if (!$passed) {
                            $power--;
                            continue;
                        }
                    }

                    if (!isset($template_data[$key])) {
                        //trigger_error('Warning email template condition missing data: ' . $key);
                        continue;
                    }

                    $value = $template_data[$key];

                    switch ($operator) {
                        case '==':
                            if ($value == $condition['value']) {
                                $power += 1;
                                $passed = true;
                            }
                            break;
                        case '!=':
                            if ($value != $condition['value']) {
                                $power += 1;
                                $passed = true;
                            }
                            break;
                        case '>':
                            if ($value > $condition['value']) {
                                $power += 1;
                                $passed = true;
                            }
                            break;
                        case '<':
                            if ($value < $condition['value']) {
                                $power += 1;
                                $passed = true;
                            }
                            break;
                        case '>=':
                            if ($value >= $condition['value']) {
                                $power += 1;
                                $passed = true;
                            }
                            break;
                        case '<=':
                            if ($value <= $condition['value']) {
                                $power += 1;
                                $passed = true;
                            }
                            break;
                        case
                        'IN':
                            $haystack = explode(',', $condition['value']);
                            if (is_array($haystack) && in_array($value, $haystack)) {
                                $power += 1;
                                $passed = true;
                            }
                            break;
                        case 'NOTIN':
                            $haystack = explode(',', $condition['value']);
                            if (is_array($haystack) && !in_array($value, $haystack)) {
                                $power += 1;
                                $passed = true;
                            }
                            break;
                    }

                    if (!empty($condition['required']) && !$passed) {
                        return false;
                    }
                }
            }
        }

        return $power;
    }

	/**
	 * Get Template
	 * @param int $ident
	 * @param int $language_id
	 * @param int $keyCleanUp
	 * @return array
	 */
	public function getTemplate($ident, $language_id = null, $keyCleanUp = false) {
		$return = array();

		if (is_numeric($ident)) {
			$where = "`emailtemplate_id` = '" . (int)$ident . "'";
		} else {
			$where = "`emailtemplate_key` = '" . $this->db->escape($ident) . "' AND `emailtemplate_default` = 1";
		}

		$query = "SELECT * FROM " . DB_PREFIX . "emailtemplate WHERE " . $where . " LIMIT 1";
		$result = $this->_fetch($query, 'emailtemplate');

		if ($result->row) {
			$return = $result->row;

			$cols = EmailTemplateDAO::describe();

			foreach($cols as $col => $type) {
				if (!isset($return[$col])) continue;

				if ($type == EmailTemplateDAO::SERIALIZE && $return[$col]) {
					$unserialized = @unserialize(base64_decode($return[$col]));
					$return[$col] = ($unserialized !== false) ? $unserialized : $return[$col];
				}

				if ($keyCleanUp) {
					$key = (strpos($col, 'emailtemplate_') === 0 && substr($col, -3) != '_id') ? substr($col, 14) : $col;
					if (!isset($return[$key])) {
						$return[$key] = $return[$col];
						unset($return[$col]);
					}
				}
			}

			if ($language_id) {
				$result = $this->getTemplateDescription(array('emailtemplate_id' => $return['emailtemplate_id'], 'language_id' => $language_id), 1);

				if ($result) {
					$cols = EmailTemplateDescriptionDAO::describe();
					foreach($cols as $col => $type) {
						$key = $col;
						if ($keyCleanUp) {
							$key = (strpos($col, 'emailtemplate_description_') === 0 && substr($col, -3) != '_id') ? substr($col, 24) : $col;
						}

						if (!isset($return[$key])) {
							$return[$key] = $result[$col];
							unset($result[$col]);
						}
					}
				}
			}
		}

		return $return;
	}

	/**
	 * Get Template
	 * @param int $id
	 * @return array
	 */
	public function getTemplateDescription($data = array(), $limit = null) {
		$where = array();
		$query = "SELECT * FROM " . DB_PREFIX . "emailtemplate_description";

		if (isset($data['emailtemplate_id'])) {
			$where[] = "`emailtemplate_id` = '".(int)$data['emailtemplate_id']."'";
		} else {
			return array();
		}

		if (isset($data['language_id'])) {
			$where[] = "`language_id` = '".(int)$data['language_id']."'";
		}

		if (!empty($where)) {
			$query .= ' WHERE ' . implode(' AND ', $where);
		}
		if (is_numeric($limit)) {
			$query .= ' LIMIT ' . (int)$limit;
		}

		$result = $this->_fetch($query, 'emailtemplate_description');

		return ($limit == 1) ? $result->row : $result->rows;
	}

	/**
	 * Return array of templates
	 * @param array - $data
	 */
	public function getTemplates($data = array(), $keyCleanUp = false) {
		$query = "SELECT e.*, (SELECT e3.emailtemplate_modified FROM " . DB_PREFIX . "emailtemplate e3 WHERE e3.emailtemplate_key = e.emailtemplate_key ORDER BY e3.emailtemplate_modified DESC LIMIT 1) as modified";

        if (isset($data['last_sent'])) {
            $query .= ", (SELECT COUNT(*) FROM " . DB_PREFIX . "emailtemplate_logs el2 WHERE e.emailtemplate_key = el2.emailtemplate_key) AS total_sent";
        }

		$query .= " FROM " . DB_PREFIX . "emailtemplate e INNER JOIN " . DB_PREFIX . "emailtemplate_description ed ON(e.emailtemplate_id = ed.emailtemplate_id)";

        if (isset($data['last_sent'])) {
            $query .= " LEFT JOIN " . DB_PREFIX . "emailtemplate_logs el ON(el.emailtemplate_log_id = (SELECT el2.emailtemplate_log_id FROM " . DB_PREFIX . "emailtemplate_logs el2 WHERE e.emailtemplate_key = el2.emailtemplate_key ORDER BY el2.emailtemplate_log_sent DESC LIMIT 1))";
        }

		$where = $this->_getTemplatesCondition($data);

		if (!empty($where)) {
			$query .= ' WHERE ' . implode(' AND ', $where);
		}

		$query .= ' GROUP BY e.emailtemplate_id';

		$sort_data = array(
			'label' => 'e.`emailtemplate_label`',
			'key' => 'e.`emailtemplate_key`',
			'template' => 'e.`emailtemplate_template`',
			'modified' => 'modified',
			'default' => 'e.`emailtemplate_default` DESC, e.`emailtemplate_label`',
			'shortcodes' => 'e.`emailtemplate_shortcodes`',
			'status' => 'e.`emailtemplate_status`',
			'id' => 'e.`emailtemplate_id`',
			'config' => 'e.`emailtemplate_config_id`',
			'store' => 'e.`store_id`',
			'customer' => 'e.`customer_group_id`',
			'last_sent' => 'GREATEST(el.emailtemplate_log_sent, el.emailtemplate_log_added)',
			'language' => 'ed.`language_id`'
		);

		if (isset($data['sort']) && in_array($data['sort'], array_keys($sort_data))) {
			$query .= " ORDER BY " . $sort_data[$data['sort']];

			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$query .= " DESC";
			} else {
				$query .= " ASC";
			}
		} else {
			$query .= " ORDER BY e.`emailtemplate_modified` DESC, e.`emailtemplate_label` ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if (!isset($data['start']) || $data['start'] < 0) {
				$data['start'] = 0;
			}
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
			$query .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

        $result = $this->_fetch($query, 'emailtemplates');

        if (empty($result->rows)) {
            return array();
        }

		$rows = $result->rows;

		$cols = EmailTemplateDAO::describe();

		foreach($rows as $key => &$row) {
			foreach($row as $col => $val) {
				if (isset($cols[$col]) && $cols[$col] == EmailTemplateDAO::SERIALIZE) {
					if ($val) {
						$unserialized = @unserialize(base64_decode($val));
						$val = ($unserialized !== false) ? $unserialized : $val;
					}
				}

				if ($keyCleanUp) {
					$key = (strpos($col, 'emailtemplate_') === 0 && substr($col, -3) != '_id') ? substr($col, 14) : $col;

					if (!array_key_exists($key, $row)) {
						$row[$key] = $val;
						unset($row[$col]);
					}
				}
			}
		}

		return $rows;
	}

	public function getTotalTemplates($data) {
		$query = "SELECT COUNT(DISTINCT e.emailtemplate_id) AS total FROM " . DB_PREFIX . "emailtemplate e INNER JOIN " . DB_PREFIX . "emailtemplate_description ed ON (e.emailtemplate_id = ed.emailtemplate_id)";

		$where = $this->_getTemplatesCondition($data);

		if (!empty($where)) {
			$query .= ' WHERE ' . implode(' AND ', $where);
		}

		$result = $this->_fetch($query, 'emailtemplates_total');
		return $result->row['total'];
	}

	private function _getTemplatesCondition($data) {
		$where = array();

		if (isset($data['store_id'])) {
			if (is_numeric($data['store_id'])) {
				$where[] = "e.`store_id` = '".(int)$data['store_id']."'";
			} else {
				$where[] = "e.`store_id` IS NULL";
			}
		}

		if (!empty($data['customer_group_id'])) {
			$where[] = "e.`customer_group_id` = '".(int)$data['customer_group_id']."'";
		}

		if (!empty($data['emailtemplate_key'])) {
			$where[] = "e.`emailtemplate_key` = '".$this->db->escape($data['emailtemplate_key'])."'";
		}

		if (isset($data['emailtemplate_type'])) {
            $cond_emailtemplate_type = "`emailtemplate_type` = '".$this->db->escape($data['emailtemplate_type'])."'";

			// Also filter keys that ends with admin
			if ($data['emailtemplate_type'] == 'admin') {
                $where[] = "(e.`emailtemplate_key` LIKE '%.admin' OR " . $cond_emailtemplate_type . ")";
            } else {
                $where[] = $cond_emailtemplate_type;
            }
		}

		if (isset($data['emailtemplate_content']) && $data['emailtemplate_content'] != "") {
			$query_cols = array('subject', 'heading', 'preview');

			if (isset($data['emailtemplate_content_full'])) {
                for ($i = 1; $i <= $this->content_count; $i++) {
                    $query_cols[] = 'content' . $i;
                }
            }

			$query_where = array();

			foreach ($query_cols as $query_col) {
				$query_where[] = "ed.`emailtemplate_description_" . $this->db->escape($query_col) . "` LIKE '%" . $this->db->escape($data['emailtemplate_content']) . "%'";
			}

			$where[] = '(' . implode(' OR ', $query_where) . ')';
		}

		if (isset($data['emailtemplate_status']) && $data['emailtemplate_status'] != "") {
			$where[] = "e.`emailtemplate_status` = '".$this->db->escape($data['emailtemplate_status'])."'";
		}

		if (isset($data['emailtemplate_default'])) {
			$where[] = "e.`emailtemplate_default` = '" . (int)$data['emailtemplate_default'] . "'";
		}

		if (!empty($data['emailtemplate_preference'])) {
			$where[] = "e.`emailtemplate_preference` = '" . $this->db->escape($data['emailtemplate_preference']) . "'";
		}

		if (isset($data['emailtemplate_id'])) {
			if (is_array($data['emailtemplate_id'])) {
				$ids = array();
				foreach($data['emailtemplate_id'] as $id) { $ids[] = (int)$id; }
				$where[] = "e.`emailtemplate_id` IN('".implode("', '", $ids)."')";
			} else {
				$where[] = "e.`emailtemplate_id` = '".(int)$data['emailtemplate_id']."'";
			}
		} else {
			$where[] = "e.`emailtemplate_id` != 1";
		}

		return $where;
	}

	public function getTemplateLog($id) {
		$query = "SELECT * FROM " . DB_PREFIX . "emailtemplate_logs WHERE `emailtemplate_log_id` = '". (int)$id . "' LIMIT 1";
		$result = $this->db->query($query);

		return $result->row;
	}

	public function getTemplateLogs($data = array(), $keyCleanUp = false) {
		$query = "SELECT el.* FROM `" . DB_PREFIX . "emailtemplate_logs` el";

		$where = $this->_getTemplateLogsCondition($data);

		if (!empty($where)) {
			$query .= ' WHERE ' . implode(' AND ', $where);
		}

		$sort_data = array(
			'id' => 'el.`emailtemplate_log_id`',
			'template' => 'el.`emailtemplate_id`',
			'store_id' => 'el.`store_id`',
			'sent' => 'el.`emailtemplate_log_sent` IS NULL DESC, el.`emailtemplate_log_sent`',
			'added' => 'el.`emailtemplate_log_added`',
			'to' => 'el.`emailtemplate_log_to`',
			'from' => 'el.`emailtemplate_log_from`',
			'sender' => 'el.`emailtemplate_log_sender`',
			'subject' => 'el.`emailtemplate_log_subject`'
		);
		if (isset($data['sort']) && in_array($data['sort'], array_keys($sort_data))) {
			$query .= " ORDER BY " . $sort_data[$data['sort']];

			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$query .= " DESC";
			} else {
				$query .= " ASC";
			}
		} else {
			$query .= " ORDER BY el.`emailtemplate_log_sent` IS NULL DESC, el.`emailtemplate_log_sent` DESC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if (!isset($data['start']) || $data['start'] < 0) {
				$data['start'] = 0;
			}
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
			$query .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$result = $this->db->query($query);

		if (empty($result->rows)) return array();

		foreach($result->rows as $i => $row) {
			foreach($row as $col => $val) {
				if ($keyCleanUp) {
					$key = (strpos($col, 'emailtemplate_log_') === 0 && substr($col, -3) != '_id') ? substr($col, 18) : $col;
					if (!isset($result->rows[$i][$key])) {
						unset($result->rows[$i][$col]);
						$result->rows[$i][$key] = $val;
					}
				}
			}
		}

		return $result->rows;
	}

	public function getTotalTemplateLogs($data = array()) {
		$query = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "emailtemplate_logs` el";

		$where = $this->_getTemplateLogsCondition($data);

		if (!empty($where)) {
			$query .= ' WHERE ' . implode(' AND ', $where);
		}

		$result = $this->db->query($query);
		if (empty($result->row)) return array();

		return $result->row['total'];
	}

	private function _getTemplateLogsCondition($data) {
		$where = array();

		if (isset($data['store_id']) && is_numeric($data['store_id'])) {
			if (is_numeric($data['store_id'])) {
				$where[] = "el.`store_id` = '".(int)$data['store_id']."'";
			}
		}

		if (!empty($data['language_id'])) {
			$where[] = "el.`language_id` = '".(int)$data['language_id']."'";
		}

		if (!empty($data['customer_id'])) {
			$where[] = "(el.`customer_id` = '".(int)$data['customer_id']."' OR emailtemplate_log_to = (SELECT email FROM " . DB_PREFIX . "customer WHERE customer_id = '".(int)$data['customer_id']."' LIMIT 1))";
		}

		if (isset($data['emailtemplate_log_is_sent'])) {
			if ($data['emailtemplate_log_is_sent']) {
				$where[] = "`emailtemplate_log_is_sent` = '" . (int)$data['emailtemplate_log_is_sent'] . "'";
			} elseif ($data['emailtemplate_log_is_sent'] === false) {
				$where[] = "`emailtemplate_log_is_sent` IS NULL";
			} else {
				$where[] = "(`emailtemplate_log_is_sent` IS NULL OR `emailtemplate_log_is_sent` = 0)";
			}
		} else {
			$where[] = "`emailtemplate_log_sent` IS NOT NULL";
		}

		if (isset($data['emailtemplate_id']) && $data['emailtemplate_id'] !== '') {
			if ($data['emailtemplate_id'] == 'missing') {
				$where[] = "el.`emailtemplate_id` NOT IN(SELECT e.emailtemplate_id FROM " . DB_PREFIX . "emailtemplate e)";
			} elseif ($data['emailtemplate_id']) {
				$where[] = "el.`emailtemplate_id` = '" . (int)$data['emailtemplate_id'] . "'";
			} elseif (is_array($data['emailtemplate_id'])) {
				$ids = array();
				foreach ($data['emailtemplate_id'] as $id) {
					$ids[] = (int)$id;
				}
				$where[] = "el.`emailtemplate_id` IN('" . implode("', '", $ids) . "')";
			} else {
				$where[] = "el.`emailtemplate_id` NOT IN(SELECT e.emailtemplate_id FROM " . DB_PREFIX . "emailtemplate e)";
			}
		}

		if (!empty($data['emailtemplate_key'])) {
			if ($data['emailtemplate_key'] == 'missing') {
				$where[] = "el.`emailtemplate_key` NOT IN(SELECT e.emailtemplate_key FROM " . DB_PREFIX . "emailtemplate e)";
			} else {
				$emailtemplate_key = $emailtemplate_key = preg_replace('/\.[0-9]*$/', '', $data['emailtemplate_key']);
				$where[] = "el.`emailtemplate_key` = '" . $this->db->escape($emailtemplate_key) . "'";
			}
		}

        if (!empty($data['days_ago'])) {
            $where[] = "el.`emailtemplate_log_added` >= DATE(NOW()) - INTERVAL " . (int)$data['days_ago'] . " DAY";
        }

        if (!empty($data['email_to'])) {
            $where[] = "el.`emailtemplate_log_to` = '" . $this->db->escape($data['email_to']) . "'";
        }

        if (!empty($data['email'])) {
            $where[] = "(el.`emailtemplate_log_to` = '" . $this->db->escape($data['email']) . "'".
                " OR el.`emailtemplate_log_from` = '" . $this->db->escape($data['email']) . "'".
                " OR el.`emailtemplate_log_reply_to` = '" . $this->db->escape($data['email']) . "'".
                " OR el.`emailtemplate_log_cc` = '" . $this->db->escape($data['email']) . "'".
                " OR el.`emailtemplate_log_bcc` = '" . $this->db->escape($data['email']) . "')";
        }

		return $where;
	}

	public function isSentLoggingEnabled() {
		$result = $this->db->query("SELECT emailtemplate_log FROM " . DB_PREFIX . "emailtemplate WHERE emailtemplate_log = 1 union SELECT emailtemplate_config_log FROM " . DB_PREFIX . "emailtemplate_config WHERE emailtemplate_config_log = 1 LIMIT 1");

		return $result->row ? true : false;
	}

	public function getLastTemplateLogId() {
		$query = "SELECT MAX(emailtemplate_log_id) as emailtemplate_log_id FROM `" . DB_PREFIX . "emailtemplate_logs`";
		$result = $this->db->query($query);

		return $result->row['emailtemplate_log_id'];
	}

	public function insertTemplate(array $data) {
		if (empty($data)) return false;

        $cols = EmailTemplateDAO::describe('emailtemplate_id');

        $inserts = $this->_build_query($cols, $data);
        if (empty($inserts)) return false;

        $this->db->query("INSERT INTO " . DB_PREFIX . "emailtemplate SET ".implode(", ", $inserts));

        $new_id = $this->db->getLastId();

        $has_language_description = false;

        foreach ($data as $key => $item) {
            if (substr($key, 0, 26) == 'emailtemplate_description_') {
                $has_language_description = true;
            }
        }

        if ($has_language_description) {
            $this->load->model('localisation/language');

            $languages = $this->model_localisation_language->getLanguages();

            $cols = EmailTemplateDescriptionDAO::describe('emailtemplate_description_id', 'emailtemplate_id', 'language_id');
            $descriptions = array();

            foreach ($languages as $language) {
                $langId = $language['language_id'];

                if (!isset($descriptions[$langId])) {
                    $descriptions[$langId] = array();
                }

                foreach ($cols as $col => $type) {
                    if (!isset($data[$col])) {
                        $descriptions[$langId][$col] = '';
                    } elseif (is_array($data[$col]) && isset($data[$col][$langId])) {
                        $descriptions[$langId][$col] = $data[$col][$langId];
                    } elseif (is_string($data[$col])) {
                        $descriptions[$langId][$col] = $data[$col];
                    }
                }
            }

            foreach ($descriptions as $langId => $data) {
                $data['language_id'] = (int)$langId;
                $data['emailtemplate_id'] = $new_id;

                $this->insertTemplateDescription($data);
            }
        }

		if (!empty($data['default_emailtemplate_id'])) {
			$data = $this->getTemplateShortcodes($data['default_emailtemplate_id']);

			$shortcodes = array();

			foreach($data as $row) {
				$shortcodes[$row['emailtemplate_shortcode_code']] = $row['emailtemplate_shortcode_example'];
			}

			$this->insertTemplateShortcodes($new_id, $shortcodes);
		}

		$this->clear();

		return $new_id;
	}

	/**
	 * Add new template description row
	 *
	 * @return new row identifier
	 */
	public function insertTemplateDescription(array $data) {
		if (empty($data)) return false;

		$cols = EmailTemplateDescriptionDAO::describe('emailtemplate_description_id');

		$inserts = $this->_build_query($cols, $data);
		if (empty($inserts)) return false;

		$sql = "INSERT INTO " . DB_PREFIX . "emailtemplate_description SET ".implode(", ", $inserts);
		$this->db->query($sql);

		$new_id = $this->db->getLastId();

		$this->clear();

		return $new_id;
	}

	/**
	 * Insert Template Shortcodes(data)
	 */
	public function insertTemplateShortcodes($id, $data = array(), $language_data = array()) {
		if (isset($data['insert_shortcodes']) && !$data['insert_shortcodes']) return false;

		$this->db->query("DELETE FROM " . DB_PREFIX . "emailtemplate_shortcode WHERE `emailtemplate_id` = '". (int)$id . "'");

		$inserts = array();

		$accept_type = array('boolean', 'integer', 'double', 'string', 'NULL');

		foreach($data as $key => $example) {
			if (in_array($key,  array('config', 'emailtemplate', 'showcase_selection', 'password', 'confirm', 'mail')) || isset($language_data[$key]))
				continue;

			if (is_array($example)) {
				foreach($example as $example2 => $val) {
					if (in_array(gettype($val), $accept_type)) {
						$inserts[$key . '.' . $example2] = "('" . (int)$id . "', 'auto', '" . $this->db->escape($key . '.' . $example2) . "', '" . $this->db->escape($val) . "')";
					} elseif (is_array($val)) {
						$inserts[$key] = "('" . (int)$id . "', 'auto_serialize', '" . $this->db->escape($key) . "', '" . $this->db->escape(base64_encode(serialize($example))) . "')";
						continue;
					}
				}
			} elseif (in_array(gettype($example), $accept_type)) {
				$inserts[$key] = "('" . (int)$id . "', 'auto', '" . $this->db->escape($key) . "', '" . $this->db->escape($example) . "')";
			}
		}

		if (is_array($language_data)) {
			foreach($language_data as $key => $example) {
				if (in_array($key,  array('config', 'emailtemplate', 'showcase_selection'))) continue;

				if (is_array($example)) {
					foreach($example as $example2 => $val) {
						if (in_array(gettype($val), $accept_type)) {
							$inserts[$key . '.' . $example2] = "('" . (int)$id . "', 'language', '" . $this->db->escape($key . '.' . $example2) . "', '" . $this->db->escape($val) . "')";
						}
					}
				} elseif (in_array(gettype($example), $accept_type)) {
					$inserts[$key] = "('" . (int)$id . "', 'language', '" . $this->db->escape($key) . "', '" . $this->db->escape($example) . "')";
				}
			}
		}

		if ($inserts) {
			$insert_query = "INSERT INTO " . DB_PREFIX . "emailtemplate_shortcode (emailtemplate_id, emailtemplate_shortcode_type, emailtemplate_shortcode_code, emailtemplate_shortcode_example) VALUES " . implode(", ", $inserts);
			$this->db->query($insert_query);

		}

		$this->db->query("UPDATE " . DB_PREFIX . "emailtemplate SET `emailtemplate_shortcodes` = '1' WHERE `emailtemplate_id` = '". (int)$id . "'");

		$this->clear();
	}

	public function insertDefaultTemplateShortcodes($id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "emailtemplate_shortcode WHERE `emailtemplate_id` = '". (int)$id . "'");

		$result = $this->db->query("SELECT emailtemplate_key FROM " . DB_PREFIX . "emailtemplate WHERE `emailtemplate_id` = '". (int)$id . "' LIMIT 1");

		if ($result->row) {
			$file = DIR_APPLICATION . 'model/extension/module/emailtemplate/install/shortcodes/' . $result->row['emailtemplate_key'] . '.sql';

			if (file_exists($file)) {
				$stmnts = $this->_parse_sql($file);

				foreach($stmnts as $stmnt) {
					$stmnt = str_replace('{_ID}', (int)$id, $stmnt);

					$this->db->query($stmnt);
				}

				// Store Data
                $config_keys = array('title', 'name', 'url', 'ssl', 'owner', 'address', 'email', 'telephone', 'fax', 'country_id', 'currency', 'zone_id', 'tax', 'tax_default', 'theme', 'customer_price');

				$query = "INSERT INTO `" . DB_PREFIX . "emailtemplate_shortcode` (`emailtemplate_shortcode_code`, `emailtemplate_shortcode_type`, `emailtemplate_shortcode_example`, `emailtemplate_id`) VALUES ";

				foreach($config_keys as $i => $key) {
					$value = $this->config->get('config_'.$key);

					if($key == 'url' && !$value) {
						$value = HTTP_CATALOG;
					}

					$query .= ($i == 0 ? '' : ', ') . "('". $this->db->escape('store_'.$key) . "', 'auto', '". $this->db->escape($value) . "', " . (int)$id . ")";
				}

				$this->db->query($query);

				$this->db->query("UPDATE " . DB_PREFIX . "emailtemplate SET `emailtemplate_shortcodes` = '1' WHERE `emailtemplate_id` = '". (int)$id . "'");
			}
		}

		$this->clear();
	}

	/**
	 * Edit existing template row
	 *
	 * @param int - emailtemplate.id
	 * @param array - column => value
	 * @return returns true if row was updated with new data
	 */
	public function updateTemplate($id, array $data)
	{
		$affected = 0;

		$cols = EmailTemplateDAO::describe('emailtemplate_id');

		$updates = $this->_build_query($cols, $data);

		if ($updates) {
			$sql = "UPDATE " . DB_PREFIX . "emailtemplate SET " . implode(", ", $updates) . " WHERE `emailtemplate_id` = '" . (int)$id . "'";
			$this->db->query($sql);

			$affected += $this->db->countAffected();
		}

		$cols = EmailTemplateDescriptionDAO::describe('emailtemplate_description', 'emailtemplate_id', 'language_id');
		$descriptions = array();

		foreach ($cols as $col => $type) {
			if (isset($data[$col]) && is_array($data[$col])) {
				foreach ($data[$col] as $langId => $val) {
					if (!isset($descriptions[$langId])) {
						$descriptions[$langId] = array();
					}
					$descriptions[$langId][$col] = $val;
				}
			}
		}

		foreach ($descriptions as $langId => $data) {
			$langId = (int)$langId;
			$updates = $this->_build_query($cols, $data);
			if (empty($updates)) continue;

			$result = $this->db->query("SELECT count(`emailtemplate_id`) AS total FROM " . DB_PREFIX . "emailtemplate_description WHERE `emailtemplate_id` = '" . (int)$id . "' AND `language_id` = " . (int)$langId);
			if ($result->row['total'] == 0) {
				$query = "INSERT INTO " . DB_PREFIX . "emailtemplate_description SET `emailtemplate_id` = '" . (int)$id . "', `language_id` = '{$langId}', " . implode(", ", $updates);
			} else {
				$query = "UPDATE " . DB_PREFIX . "emailtemplate_description SET " . implode(", ", $updates) . " WHERE `emailtemplate_id` = '" . (int)$id . "' AND `language_id` = " . (int)$langId;
			}

			$this->db->query($query);

			if ($affected == 0 && $this->db->countAffected()) {
				$this->db->query("UPDATE " . DB_PREFIX . "emailtemplate SET `emailtemplate_modified` = NOW() WHERE `emailtemplate_id` = '" . (int)$id . "'");
			}
			$affected += $this->db->countAffected();
		}

        $this->clear();

        return ($affected > 0) ? $affected : false;
	}

	/**
	 * Edit existing template row
	 *
	 * @param int - emailtemplate.id
	 * @param int - language_id
	 * @param array - column => value
	 * @return returns true if row was updated with new data
	 */
	public function updateTemplateDescription($id, $language_id, array $data)
	{
		$cols = EmailTemplateDescriptionDAO::describe('emailtemplate_description', 'emailtemplate_id', 'language_id');

        $updates = $this->_build_query($cols, $data);
        if (empty($updates)) return false;

        $query = "UPDATE " . DB_PREFIX . "emailtemplate_description SET " . implode(", ", $updates) . " WHERE `emailtemplate_id` = '" . (int)$id . "' AND `language_id` = " . (int)$language_id;
        $result = $this->db->query($query);

        $affected = $this->db->countAffected();

        if ($affected) {
            $this->db->query("UPDATE " . DB_PREFIX . "emailtemplate SET `emailtemplate_modified` = NOW() WHERE `emailtemplate_id` = '" . (int)$id . "'");
            $this->clear();
        }

        return ($affected > 0) ? $affected : false;
	}

    /**
     * @param $id
     * @param false $status
     * @return false
     */
	public function updateTemplatesStatus($id, $status = false) {
		if ($status) {
			$status = 1;
		} else {
			$status = 0;
		}

		$sql = "UPDATE " . DB_PREFIX . "emailtemplate SET emailtemplate_status = '{$status}' WHERE `emailtemplate_id` != 1 AND `emailtemplate_id` = '". (int)$id . "'";

		$this->db->query($sql);

		$affected = $this->db->countAffected();

        $this->clear();

        return ($affected > 0) ? $affected : false;
	}

	/**
	 * Delete template row
	 *
	 * @param mixed array||int - emailtemplate.id
	 * @return int - row count effected
	 */
	public function deleteTemplate($data) {
		$chk = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX . "emailtemplate'");
		if (!$chk->num_rows) {
			return false;
		}

		$ids = $keys = array();
		if (is_array($data)) {
			foreach($data as $var) {
				$ids[] = (int)$var;
			}
		} elseif (is_numeric($data)) {
			$ids[] = (int)$data;
		} elseif (is_string($data)) {
			$keys[] = (string)$data;
		}  else {
			return false;
		}

		if ($keys) {
			if (($key = array_search(1, $keys)) !== false) {
				unset($keys[$key]);
			}

			foreach ($keys as $key) {
				$sql = "SELECT emailtemplate_id FROM " . DB_PREFIX . "emailtemplate WHERE emailtemplate_key = '" . $this->db->escape($key) . "'";
				$result = $this->db->query($sql);
				foreach ($result->rows as $row) {
					$ids[] = $row['emailtemplate_id'];
				}
			}
		} else {
			if (($key = array_search(1, $ids)) !== false) {
				unset($ids[$key]);
			}

			foreach($ids as $id) {
				$sql = "SELECT emailtemplate_id FROM " . DB_PREFIX . "emailtemplate WHERE emailtemplate_key = (SELECT emailtemplate_key FROM " . DB_PREFIX . "emailtemplate WHERE emailtemplate_id = '". (int)$id . "' AND emailtemplate_default = 1 LIMIT 1) AND emailtemplate_id != '". (int)$id . "'";
				$result = $this->db->query($sql);
				foreach($result->rows as $row) {
					$ids[] = $row['emailtemplate_id'];
				}
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "emailtemplate_description` WHERE `emailtemplate_id` IN('".implode("', '", $ids)."')");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "emailtemplate_shortcode` WHERE `emailtemplate_id` IN('".implode("', '", $ids)."')");

		$this->db->query("DELETE FROM " . DB_PREFIX . "emailtemplate WHERE `emailtemplate_id` IN('".implode("', '", $ids)."')");
		$affected = $this->db->countAffected();

        $this->clear();

        return ($affected > 0) ? $affected : false;
	}

	/**
	 * Delete template row
	 *
	 * @param mixed array||int - emailtemplate.id
	 * @return int - row count effected
	 */
	public function deleteLogs($data) {
		if (empty($data)) return false;

		$ids = array();
		if (is_array($data)) {
			foreach($data as $var) {
				$ids[] = (int)$var;
			}
		} else {
			$ids[] = (int)$data;
		}

		$query = "DELETE FROM `" . DB_PREFIX . "emailtemplate_logs` WHERE `emailtemplate_log_id` IN('".implode("', '", $ids)."')";

		$this->db->query($query);

		$affected = $this->db->countAffected();

		return $affected;
	}

	/**
	 * Delete template row
	 *
	 * @param mixed array
	 * @return int - row count effected
	 */
	public function deleteTemplateDescription($data) {
		$where = array();
		if (isset($data['language_id'])) {
			$where[] = "`language_id` = '" . (int)$data['language_id'] . "'";
		}

		$query = "DELETE FROM `" . DB_PREFIX . "emailtemplate_description` WHERE ".implode("', '", $where);
		$this->db->query($query);

		$affected = $this->db->countAffected();

        $this->clear();

        return ($affected > 0) ? $affected : false;
	}

	/**
	 * Delete template row
	 *
	 * @param mixed array||int - emailtemplate.id
	 * @return int - row count effected
	 */
	public function cleanLogs($data = array()) {
		$query = "DELETE el FROM " . DB_PREFIX . "emailtemplate_logs el WHERE emailtemplate_log_added < DATE_SUB(NOW(), INTERVAL 1 YEAR)";

		if (isset($data['emailtemplate_id'])) {
			$query .= " AND emailtemplate_id = '" . (int)$data['emailtemplate_id'] . "'";
		}

		$this->db->query($query);

		$affected = $this->db->countAffected();

		// Clean blank logs
		$query = "DELETE FROM " . DB_PREFIX . "emailtemplate_logs WHERE emailtemplate_log_to = '' OR (emailtemplate_log_sent = NULL AND DATEDIFF(NOW(), emailtemplate_log_added) > 10)";

		$this->db->query($query);

		$affected += $this->db->countAffected();

		// Disable sending if customer preferences changed
		/*if ($this->config->get('module_emailtemplate_newsletter_status')) {
			$query = "UPDATE " . DB_PREFIX . "emailtemplate_logs el
			INNER JOIN " . DB_PREFIX . "emailtemplate e ON(el.emailtemplate_key = e.emailtemplate_key) INNER JOIN " . DB_PREFIX . "emailtemplate_customer_preference ecp ON(el.customer_id = ecp.customer_id) INNER JOIN " . DB_PREFIX . "customer c ON(el.customer_id = c.customer_id) SET emailtemplate_log_is_sent = 0 WHERE el.customer_id IS NOT NULL AND e.emailtemplate_type != 'admin' AND ((e.emailtemplate_preference = 'notification' AND ecp.notification = 1) OR (e.emailtemplate_preference = 'newsletter' AND c.newsletter))";

			$this->db->query($query);
		}*/

		// Clean content
		$query = "UPDATE `" . DB_PREFIX . "emailtemplate_logs` SET emailtemplate_log_content = NULL, emailtemplate_log_heading = NULL WHERE DATEDIFF(CURDATE(), GREATEST(emailtemplate_log_added, COALESCE(emailtemplate_log_sent, 0))) > 180 AND emailtemplate_log_content IS NOT NULL";

		$this->db->query($query);

		$affected += $this->db->countAffected();

		return $affected;
	}

	/**
	 * Get template keys enum types
	 */
	public function getTemplateKeys() {
		$return = array();
		$query = "SELECT `emailtemplate_key`, count(`emailtemplate_id`) AS `total`
					FROM " . DB_PREFIX . "emailtemplate
				   WHERE `emailtemplate_default` = 1 AND `emailtemplate_key` != ''
				GROUP BY `emailtemplate_key`
				ORDER BY `emailtemplate_key` ASC";
		$result = $this->db->query($query);

		foreach($result->rows as $row) {
			$return[] = array(
				'value' => $row['emailtemplate_key'],
				'label' => $row['emailtemplate_key'] . ($row['total'] > 1 ? (' ('.$row['total'].')') : '')
			);
		}

		return $return;
	}

	/**
	 * Get template types
	 */
	public function getTemplateTypes() {
		$query = "SELECT `emailtemplate_type` FROM " . DB_PREFIX . "emailtemplate WHERE `emailtemplate_default` = 1 AND `emailtemplate_type` != '' GROUP BY `emailtemplate_type` ORDER BY `emailtemplate_type` ASC";
		$result = $this->db->query($query);

		$return = array();

		foreach($result->rows as $row) {
			$return[] = $row['emailtemplate_type'];
		}

		return $return;
	}

	/**
	 * Get template shortcodes
	 */
	public function getTemplateShortcodes($data) {
		$query = "SELECT es.* FROM `" . DB_PREFIX . "emailtemplate_shortcode` es";

		$where = $this->_templateShortcodesCondition($data);

		if (!empty($where)) {
			$query .= ' WHERE ' . implode(' AND ', $where);
		}

		$sort_data = array(
			'id' => 'es.`emailtemplate_shortcode_id`',
			'emailtemplate_id' => 'es.`emailtemplate_id`',
			'code' => 'es.`emailtemplate_shortcode_code`',
			'example' => 'es.`emailtemplate_shortcode_example`',
			'type' => 'es.`emailtemplate_shortcode_type`'
		);
		if (isset($data['sort']) && in_array($data['sort'], array_keys($sort_data))) {
			$query .= " ORDER BY " . $sort_data[$data['sort']];
		} else {
			$query .= " ORDER BY es.`emailtemplate_shortcode_code`";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$query .= " DESC";
		} else {
			$query .= " ASC";
		}

		if (is_array($data) && (isset($data['start']) || isset($data['limit']))) {
			if (!isset($data['start']) || $data['start'] < 0) {
				$data['start'] = 0;
			}
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
			$query .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$result = $this->_fetch($query, 'emailtemplate_shortcodes');

		return $result->rows;
	}

	/**
	 * Get template shortcodes
	 * @param array - $data
	 */
	public function getTotalTemplateShortcodes($data = array()) {
		$query = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "emailtemplate_shortcode` es";

		$where = $this->_templateShortcodesCondition($data);

		if (!empty($where)) {
			$query .= ' WHERE ' . implode(' AND ', $where);
		}

		$result = $this->db->query($query);
		if (empty($result->row)) return array();

		return $result->row['total'];
	}

	/**
	 * Get total default templates installed
	 * @param int - $total
	 */
	public function getTotalDefaultTemplates() {
		$query = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "emailtemplate WHERE emailtemplate_default = 1 AND emailtemplate_key IN('" . implode("', '", $this->original_templates) . "')";
		$result = $this->db->query($query);

		return $result->row ? $result->row['total'] : 0;
	}

	/**
	 * Get template for restore
	 * @param array - $data
	 */
	public function getTemplatesRestore() {
		$query = "SELECT emailtemplate_key FROM " . DB_PREFIX . "emailtemplate WHERE emailtemplate_default = 1 AND emailtemplate_id != 1 GROUP BY emailtemplate_key";
		$result = $this->db->query($query);

		$template_keys = array();

		foreach($result->rows as $row) {
			$template_keys[] = $row['emailtemplate_key'];
		}

		return array_diff($this->original_templates, $template_keys);
	}

	/**
	 * Edit shortcode
	 *
	 * @param int - emailtemplate_shortcode.emailtemplate_shortcode_id
	 * @param array - column => value
	 * @return int affected row count
	 */
	public function updateTemplateShortcode($id, array $data) {
		if (empty($data) && !is_numeric($id)) return false;
		$cols = EmailTemplateShortCodesDAO::describe();

		$updates = $this->_build_query($cols, $data);
		if (!$updates) return false;

		$sql = "UPDATE " . DB_PREFIX . "emailtemplate_shortcode SET ".implode(", ", $updates) . " WHERE emailtemplate_shortcode_id = '". (int)$id . "'";
		$this->db->query($sql);

		$affected = $this->db->countAffected();

        $this->clear();

        return ($affected > 0) ? $affected : false;
	}

	/**
	 * Delete template shortcode(s)
	 * Detech if template is custom and deletes shortcodes for custom templates
	 *
	 * @param int template_id
	 * @param array selected emailtemplate_shortcode_id - if empty deletes all
	 * @return int - row count effected
	 */
	public function deleteTemplateShortcodes($id, $data = array()) {
		$where = array();

		$related = $this->db->query("SELECT emailtemplate_id FROM " . DB_PREFIX . "emailtemplate WHERE emailtemplate_id != '" . (int)$id . "' AND emailtemplate_key = (SELECT emailtemplate_key FROM " . DB_PREFIX . "emailtemplate WHERE emailtemplate_id = '" . (int)$id . "')");

		$ids = array($id);

		if ($related->rows) {
			foreach($related->rows as $row) {
				$ids[] = (int)$row['emailtemplate_id'];
			}
		}

		$where[] = "`emailtemplate_id` IN('" . implode(', ', $ids) . "')";

		if (isset($data['emailtemplate_shortcode_id'])) {
			if (is_array($data['emailtemplate_shortcode_id'])) {
				$ids = array();
				foreach($data['emailtemplate_shortcode_id'] as $emailtemplate_shortcode_id) {
					$ids[] = (int)$emailtemplate_shortcode_id;
				}
				$where[] = "`emailtemplate_shortcode_id` IN(". implode(', ', $ids) .")";
			} else {
				$where[] = "`emailtemplate_shortcode_id` = '". (int)$data['emailtemplate_shortcode_id'] . "'";
			}
		}

		if ($where) {
			$this->db->query("DELETE FROM `" . DB_PREFIX . "emailtemplate_shortcode` WHERE ".implode(" AND ", $where));
			$affected = $this->db->countAffected();

			$this->db->query("UPDATE " . DB_PREFIX . "emailtemplate SET `emailtemplate_shortcodes` = 0 WHERE `emailtemplate_id` = " . (int)$id);

			$this->clear();

			return ($affected > 0) ? $affected : false;
		}
	}

	public function install()
	{
		$this->load->language('extension/module/emailtemplate');

		$files = array('install.sql', 'install.config.sql');

		foreach ($files as $filename) {
			$file = DIR_APPLICATION . 'model/extension/module/emailtemplate/install/' . $filename;

			if (file_exists($file)) {
				$stmnts = $this->_parse_sql($file);

				foreach ($stmnts as $stmnt) {
					$this->db->query($stmnt);
				}
			}
		}

		$this->db->query("UPDATE `" . DB_PREFIX . "emailtemplate_description` SET language_id  = '" . (int)$this->config->get('config_language_id') . "' WHERE emailtemplate_id = 1");

		$this->installTemplate('main');

		// Increase `modification` length
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "modification` CHANGE `xml` `xml` MEDIUMTEXT NOT NULL");

		$this->_checkDB();

		$this->updateEvents();

		$this->clear();
	}

	public function upgradeExtension(){
		$this->load->language('extension/module/emailtemplate');

		$file = DIR_APPLICATION . 'model/extension/module/emailtemplate/install/install.sql';

		if (file_exists($file)) {
			$stmnts = $this->_parse_sql($file);

			foreach($stmnts as $stmnt) {
				$this->db->query($stmnt);
			}
		}

		$config_cols = array();

		$config_cols['customer_group_id'] = "`customer_group_id` int(11) NOT NULL";
		$config_cols['emailtemplate_config_body_bg_image_position'] = "`emailtemplate_config_body_bg_image_position` varchar(32)";
		$config_cols['emailtemplate_config_body_bg_image_repeat'] = "`emailtemplate_config_body_bg_image_repeat` varchar(32)";
		$config_cols['emailtemplate_config_body_bg_image'] = "`emailtemplate_config_body_bg_image` varchar(255)";
		$config_cols['emailtemplate_config_body_font_custom'] = "`emailtemplate_config_body_font_custom` varchar(128)";
		$config_cols['emailtemplate_config_body_font_family'] = "`emailtemplate_config_body_font_family` varchar(128)";
		$config_cols['emailtemplate_config_body_font_size'] = "`emailtemplate_config_body_font_size` smallint(6)";
		$config_cols['emailtemplate_config_body_font_url'] = "`emailtemplate_config_body_font_url` varchar(128)";
		$config_cols['emailtemplate_config_cart_setting'] = "`emailtemplate_config_cart_setting` text NULL";
		$config_cols['emailtemplate_config_email_responsive'] = "`emailtemplate_config_email_responsive` tinyint(1)";
		$config_cols['emailtemplate_config_footer_bg_color'] = "`emailtemplate_config_footer_bg_color` varchar(32)";
		$config_cols['emailtemplate_config_footer_border_bottom'] = "`emailtemplate_config_footer_border_bottom` varchar(128)";
		$config_cols['emailtemplate_config_footer_border_left'] = "`emailtemplate_config_footer_border_left` varchar(128)";
		$config_cols['emailtemplate_config_footer_border_radius'] = "`emailtemplate_config_footer_border_radius` varchar(64)";
		$config_cols['emailtemplate_config_footer_border_right'] = "`emailtemplate_config_footer_border_right` varchar(128)";
		$config_cols['emailtemplate_config_footer_border_top'] = "`emailtemplate_config_footer_border_top` varchar(128)";
		$config_cols['emailtemplate_config_footer_font_size'] = "`emailtemplate_config_footer_font_size` smallint(6)";
		$config_cols['emailtemplate_config_footer_padding'] = "`emailtemplate_config_footer_padding` varchar(32)";
		$config_cols['emailtemplate_config_footer_spacing'] = "`emailtemplate_config_footer_spacing` varchar(32)";
		$config_cols['emailtemplate_config_footer_status'] = "`emailtemplate_config_footer_status` tinyint(1) NULL";
		$config_cols['emailtemplate_config_header_border_bottom'] = "`emailtemplate_config_header_border_bottom` varchar(128)";
		$config_cols['emailtemplate_config_header_border_left'] = "`emailtemplate_config_header_border_left` varchar(128)";
		$config_cols['emailtemplate_config_header_border_radius'] = "`emailtemplate_config_header_border_radius` varchar(32)";
		$config_cols['emailtemplate_config_header_border_right'] = "`emailtemplate_config_header_border_right` varchar(128)";
		$config_cols['emailtemplate_config_header_border_top'] = "`emailtemplate_config_header_border_top` varchar(128)";
		$config_cols['emailtemplate_config_header_html'] = "`emailtemplate_config_header_html` TEXT NULL DEFAULT NULL";
		$config_cols['emailtemplate_config_header_padding'] = "`emailtemplate_config_header_padding` varchar(32)";
		$config_cols['emailtemplate_config_header_spacing'] = "`emailtemplate_config_header_spacing` varchar(32)";
		$config_cols['emailtemplate_config_header_status'] = "`emailtemplate_config_header_status` tinyint(1) NULL";
		$config_cols['emailtemplate_config_link_style'] = "`emailtemplate_config_link_style` text";
		$config_cols['emailtemplate_config_log_read'] = "`emailtemplate_config_log_read` tinyint(1) NULL DEFAULT NULL";
		$config_cols['emailtemplate_config_log'] = "`emailtemplate_config_log` tinyint(1) NULL DEFAULT NULL";
		$config_cols['emailtemplate_config_logo_resize'] = "`emailtemplate_config_logo_resize` tinyint(1)";
		$config_cols['emailtemplate_config_order_products'] = "`emailtemplate_config_order_products` text NULL";
		$config_cols['emailtemplate_config_order_update'] = "`emailtemplate_config_order_update` text NULL";
		$config_cols['emailtemplate_config_page_border_radius'] = "`emailtemplate_config_page_border_radius` varchar(64)";
		$config_cols['emailtemplate_config_page_border_top'] = "`emailtemplate_config_page_border_top` varchar(128)";
		$config_cols['emailtemplate_config_page_padding'] = "`emailtemplate_config_page_padding` varchar(32)";
		$config_cols['emailtemplate_config_page_spacing'] = "`emailtemplate_config_page_spacing` varchar(32)";
		$config_cols['emailtemplate_config_showcase_bg_color'] = "`emailtemplate_config_showcase_bg_color` varchar(32)";
		$config_cols['emailtemplate_config_showcase_border_bottom'] = "`emailtemplate_config_showcase_border_bottom` varchar(128)";
		$config_cols['emailtemplate_config_showcase_border_left'] = "`emailtemplate_config_showcase_border_left` varchar(128)";
		$config_cols['emailtemplate_config_showcase_border_radius'] = "`emailtemplate_config_showcase_border_radius` varchar(64)";
		$config_cols['emailtemplate_config_showcase_border_right'] = "`emailtemplate_config_showcase_border_right` varchar(128)";
		$config_cols['emailtemplate_config_showcase_border_top'] = "`emailtemplate_config_showcase_border_top` varchar(128)";
		$config_cols['emailtemplate_config_showcase_padding'] = "`emailtemplate_config_showcase_padding` varchar(32)";
		$config_cols['emailtemplate_config_showcase_section_bg_color'] = "`emailtemplate_config_showcase_section_bg_color` varchar(32)";
		$config_cols['emailtemplate_config_showcase_selection'] = "`emailtemplate_config_showcase_selection` varchar(255)";
		$config_cols['emailtemplate_config_showcase_setting'] = "`emailtemplate_config_showcase_setting` text";
		$config_cols['emailtemplate_config_showcase_title'] = "`emailtemplate_config_showcase_title` text";
		$config_cols['emailtemplate_config_showcase'] = "`emailtemplate_config_showcase` varchar(32)";
		$config_cols['emailtemplate_config_style'] = "`emailtemplate_config_style` varchar(64)";
		$config_cols['emailtemplate_config_version'] = "`emailtemplate_config_version` varchar(64) NOT NULL";
		$config_cols['emailtemplate_config_view_browser_theme'] = "`emailtemplate_config_view_browser_theme` tinyint(1) NULL DEFAULT NULL";
		$config_cols['emailtemplate_config_wrapper_tpl'] = "`emailtemplate_config_wrapper_tpl` varchar(255) NOT NULL";

		$config_cols['emailtemplate_config_admin'] = "`emailtemplate_config_admin` TINYINT(1)  NULL  DEFAULT NULL";
		$config_cols['emailtemplate_config_body_font_source'] = "`emailtemplate_config_body_font_source` text";
		$config_cols['emailtemplate_config_css_custom'] = "`emailtemplate_config_css_custom` text";
		$config_cols['emailtemplate_config_page_border_bottom'] = "`emailtemplate_config_page_border_bottom` varchar(128)";
		$config_cols['emailtemplate_config_page_border_left'] = "`emailtemplate_config_page_border_left` varchar(128)";
		$config_cols['emailtemplate_config_page_border_right'] = "`emailtemplate_config_page_border_right` varchar(128)";
		$config_cols['emailtemplate_config_page_shadow'] = "`emailtemplate_config_page_shadow` VARCHAR(32) NULL DEFAULT NULL";
		$config_cols['emailtemplate_config_preference_text'] = "`emailtemplate_config_preference_text` TEXT NULL DEFAULT NULL";
		$config_cols['emailtemplate_config_unsubscribe'] = "`emailtemplate_config_unsubscribe` tinyint(1) NULL DEFAULT NULL";
		$config_cols['emailtemplate_config_view_browser'] = "`emailtemplate_config_view_browser` tinyint(1) NULL DEFAULT NULL";

		foreach ($config_cols as $config_col => $config_col_query) {
			$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "emailtemplate_config` LIKE '" . $config_col . "'");
			if (!$chk->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "emailtemplate_config` ADD " . $config_col_query);
			}
		}

		$this->db->query("UPDATE `" . DB_PREFIX . "emailtemplate_config` SET		
			`emailtemplate_config_email_width` = '860px',
			`emailtemplate_config_email_responsive` = 1,
			`emailtemplate_config_wrapper_tpl` = '_main.tpl',
			`emailtemplate_config_page_padding` = '30, 30, 40, 30',
			`emailtemplate_config_body_font_size` = '14',
			`emailtemplate_config_body_font_family` = 'Arial, \'Helvetica Neue\', Helvetica, sans-serif',
			`emailtemplate_config_body_font_color` = '#333333',
			`emailtemplate_config_body_link_color` = '#28B0EC',
			`emailtemplate_config_footer_text` = 'YToxOntpOjE7czo2MDA2OiImbHQ7dGFibGUgY2VsbHBhZGRpbmc9JnF1b3Q7MCZxdW90OyBjZWxsc3BhY2luZz0mcXVvdDswJnF1b3Q7IGNsYXNzPSZxdW90O3RhYmxlU3RhY2smcXVvdDsgc3R5bGU9JnF1b3Q7d2lkdGg6MTAwJSZxdW90OyZndDsNCgkmbHQ7dGJvZHkmZ3Q7DQoJCSZsdDt0ciBjbGFzcz0mcXVvdDt0YWJsZS1yb3ctc3RhY2smcXVvdDsmZ3Q7DQoJCQkmbHQ7dGQgY2xhc3M9JnF1b3Q7dGFibGUtY2VsbC1zdGFjayZxdW90OyBzdHlsZT0mcXVvdDtmb250LXNpemU6MHB4O3ZlcnRpY2FsLWFsaWduOnRvcDt3aWR0aDozMyU7JnF1b3Q7Jmd0Ow0KCQkJJmx0O3RhYmxlIGJvcmRlcj0mcXVvdDswJnF1b3Q7IGNlbGxwYWRkaW5nPSZxdW90OzAmcXVvdDsgY2VsbHNwYWNpbmc9JnF1b3Q7MCZxdW90OyBjbGFzcz0mcXVvdDt0YWJsZS1zdGFjayZxdW90OyBzdHlsZT0mcXVvdDtib3JkZXItY29sbGFwc2U6Y29sbGFwc2U7IGJvcmRlcjpub25lOyBmb250LXNpemU6MTRweDsgbXNvLXRhYmxlLWxzcGFjZTowcHQ7IG1zby10YWJsZS1yc3BhY2U6MHB0O3dpZHRoOjEwMCUmcXVvdDsmZ3Q7DQoJCQkJJmx0O3RoZWFkJmd0Ow0KCQkJCQkmbHQ7dHImZ3Q7DQoJCQkJCQkmbHQ7dGggc3R5bGU9JnF1b3Q7d2lkdGg6MTBweCZxdW90OyZndDsmYW1wO25ic3A7Jmx0Oy90aCZndDsNCgkJCQkJCSZsdDt0aCBzdHlsZT0mcXVvdDt0ZXh0LWFsaWduOmNlbnRlciZxdW90OyZndDtWaXNpdCBVcyZsdDsvdGgmZ3Q7DQoJCQkJCSZsdDsvdHImZ3Q7DQoJCQkJJmx0Oy90aGVhZCZndDsNCgkJCQkmbHQ7dGJvZHkmZ3Q7DQoJCQkJCSZsdDt0ciZndDsNCgkJCQkJCSZsdDt0ZCBzdHlsZT0mcXVvdDt3aWR0aDoxMHB4JnF1b3Q7Jmd0OyZhbXA7bmJzcDsmbHQ7L3RkJmd0Ow0KCQkJCQkJJmx0O3RkIHN0eWxlPSZxdW90O2ZvbnQtc2l6ZToxM3B4OyBsaW5lLWhlaWdodDoyMHB4OyBwYWRkaW5nOjhweCAwIDNweDsgdGV4dC1hbGlnbjpjZW50ZXImcXVvdDsmZ3Q7DQoJCQkJCQkmbHQ7ZGl2Jmd0O0FkZHJlc3MgMSZsdDsvZGl2Jmd0Ow0KCQkJCQkJJmx0Oy90ZCZndDsNCgkJCQkJJmx0Oy90ciZndDsNCgkJCQkmbHQ7L3Rib2R5Jmd0Ow0KCQkJJmx0Oy90YWJsZSZndDsNCgkJCSZsdDsvdGQmZ3Q7DQoJCQkmbHQ7dGQgY2xhc3M9JnF1b3Q7dGFibGUtY2VsbC1zdGFjayZxdW90OyBzdHlsZT0mcXVvdDtmb250LXNpemU6MHB4O3ZlcnRpY2FsLWFsaWduOnRvcDt3aWR0aDozMyU7JnF1b3Q7Jmd0Ow0KCQkJJmx0O3RhYmxlIGFsaWduPSZxdW90O2xlZnQmcXVvdDsgYm9yZGVyPSZxdW90OzAmcXVvdDsgY2VsbHBhZGRpbmc9JnF1b3Q7MCZxdW90OyBjZWxsc3BhY2luZz0mcXVvdDswJnF1b3Q7IGNsYXNzPSZxdW90O3RhYmxlLXN0YWNrJnF1b3Q7IHN0eWxlPSZxdW90O2JvcmRlci1jb2xsYXBzZTpjb2xsYXBzZTsgYm9yZGVyOm5vbmU7IGZvbnQtc2l6ZToxNHB4OyBtc28tdGFibGUtbHNwYWNlOjBwdDsgbXNvLXRhYmxlLXJzcGFjZTowcHQ7IHdpZHRoOjEwMCUmcXVvdDsmZ3Q7DQoJCQkJJmx0O3RoZWFkJmd0Ow0KCQkJCQkmbHQ7dHImZ3Q7DQoJCQkJCQkmbHQ7dGggc3R5bGU9JnF1b3Q7d2lkdGg6MTBweCZxdW90OyZndDsmYW1wO25ic3A7Jmx0Oy90aCZndDsNCgkJCQkJCSZsdDt0aCBzdHlsZT0mcXVvdDt0ZXh0LWFsaWduOmNlbnRlciZxdW90OyZndDtTdGF5IENvbm5lY3RlZCZsdDsvdGgmZ3Q7DQoJCQkJCSZsdDsvdHImZ3Q7DQoJCQkJJmx0Oy90aGVhZCZndDsNCgkJCQkmbHQ7dGJvZHkmZ3Q7DQoJCQkJCSZsdDt0ciZndDsNCgkJCQkJCSZsdDt0ZCBzdHlsZT0mcXVvdDt3aWR0aDoxMHB4JnF1b3Q7Jmd0OyZhbXA7bmJzcDsmbHQ7L3RkJmd0Ow0KCQkJCQkJJmx0O3RkIHN0eWxlPSZxdW90O2ZvbnQtc2l6ZToxM3B4OyBsaW5lLWhlaWdodDoyMHB4OyBwYWRkaW5nOjhweCAwIDNweDsgdGV4dC1hbGlnbjpjZW50ZXImcXVvdDsmZ3Q7DQogICAgICAgICAgICAgICAgICAgICAgICAgICZsdDt0YWJsZSBhbGlnbj0mcXVvdDtjZW50ZXImcXVvdDsgYm9yZGVyPSZxdW90OzAmcXVvdDsgY2VsbHBhZGRpbmc9JnF1b3Q7MCZxdW90OyBjZWxsc3BhY2luZz0mcXVvdDswJnF1b3Q7IHN0eWxlPSZxdW90O2JvcmRlci1jb2xsYXBzZTpjb2xsYXBzZTsgYm9yZGVyOm5vbmU7IGZvbnQtc2l6ZToxNHB4OyBtc28tdGFibGUtbHNwYWNlOjBwdDsgbXNvLXRhYmxlLXJzcGFjZTowcHQ7d2lkdGg6YXV0byAhaW1wb3J0YW50OyZxdW90OyZndDsNCgkJCQkJCQkmbHQ7dGJvZHkmZ3Q7DQoJCQkJCQkJCSZsdDt0ciZndDsNCgkJCQkJCQkJCSZsdDt0ZCBzdHlsZT0mcXVvdDt0ZXh0LWFsaWduOmNlbnRlciZxdW90OyZndDsmbHQ7YSBocmVmPSZxdW90OyZxdW90OyBzdHlsZT0mcXVvdDtkaXNwbGF5OmJsb2NrO3BhZGRpbmc6OHB4IDNweCA4cHggNnB4O2JhY2tncm91bmQtY29sb3I6I2M0YzRjNDtib3JkZXItcmFkaXVzOjUwJSAwIDAgNTAlOyZxdW90OyB0YXJnZXQ9JnF1b3Q7X2JsYW5rJnF1b3Q7Jmd0OyZsdDtpbWcgYWx0PSZxdW90OyZxdW90OyBzcmM9JnF1b3Q7aHR0cDovL29wZW5jYXJ0X2FkdmFuY2VkXzMwMjEubG9jYWwvaW1hZ2UvY2F0YWxvZy9lbWFpbHRlbXBsYXRlL2ljb24vZmFjZWJvb2sucG5nJnF1b3Q7IHN0eWxlPSZxdW90O2Rpc3BsYXk6aW5saW5lLWJsb2NrOyBoZWlnaHQ6MjZweDsgd2lkdGg6MjZweCZxdW90OyZndDsmbHQ7L2EmZ3Q7Jmx0Oy90ZCZndDsNCgkJCQkJCQkJCSZsdDt0ZCBzdHlsZT0mcXVvdDt0ZXh0LWFsaWduOmNlbnRlciZxdW90OyZndDsmbHQ7YSBocmVmPSZxdW90OyZxdW90OyBzdHlsZT0mcXVvdDtkaXNwbGF5OmJsb2NrO3BhZGRpbmc6OHB4IDNweCA4cHggM3B4O2JhY2tncm91bmQtY29sb3I6I2M0YzRjNDtib3JkZXItcmFkaXVzOjA7JnF1b3Q7IHRhcmdldD0mcXVvdDtfYmxhbmsmcXVvdDsmZ3Q7Jmx0O2ltZyBhbHQ9JnF1b3Q7JnF1b3Q7IHNyYz0mcXVvdDtodHRwOi8vb3BlbmNhcnRfYWR2YW5jZWRfMzAyMS5sb2NhbC9pbWFnZS9jYXRhbG9nL2VtYWlsdGVtcGxhdGUvaWNvbi90d2l0dGVyLnBuZyZxdW90OyBzdHlsZT0mcXVvdDtkaXNwbGF5OmlubGluZS1ibG9jazsgaGVpZ2h0OjI2cHg7IHdpZHRoOjI2cHgmcXVvdDsmZ3Q7Jmx0Oy9hJmd0OyZsdDsvdGQmZ3Q7DQoJCQkJCQkJCQkmbHQ7dGQgc3R5bGU9JnF1b3Q7dGV4dC1hbGlnbjpjZW50ZXImcXVvdDsmZ3Q7Jmx0O2EgaHJlZj0mcXVvdDsmcXVvdDsgc3R5bGU9JnF1b3Q7ZGlzcGxheTpibG9jaztwYWRkaW5nOjhweCAzcHggOHB4IDNweDtiYWNrZ3JvdW5kLWNvbG9yOiNjNGM0YzQ7Ym9yZGVyLXJhZGl1czowOyZxdW90OyB0YXJnZXQ9JnF1b3Q7X2JsYW5rJnF1b3Q7Jmd0OyZsdDtpbWcgYWx0PSZxdW90OyZxdW90OyBzcmM9JnF1b3Q7aHR0cDovL29wZW5jYXJ0X2FkdmFuY2VkXzMwMjEubG9jYWwvaW1hZ2UvY2F0YWxvZy9lbWFpbHRlbXBsYXRlL2ljb24vbGlua2VkaW4ucG5nJnF1b3Q7IHN0eWxlPSZxdW90O2Rpc3BsYXk6aW5saW5lLWJsb2NrOyBoZWlnaHQ6MjZweDsgd2lkdGg6MjZweCZxdW90OyZndDsmbHQ7L2EmZ3Q7Jmx0Oy90ZCZndDsNCgkJCQkJCQkJCSZsdDt0ZCBzdHlsZT0mcXVvdDt0ZXh0LWFsaWduOmNlbnRlciZxdW90OyZndDsmbHQ7YSBocmVmPSZxdW90OyZxdW90OyBzdHlsZT0mcXVvdDtkaXNwbGF5OmJsb2NrO3BhZGRpbmc6OHB4IDZweCA4cHggM3B4O2JhY2tncm91bmQtY29sb3I6I2M0YzRjNDtib3JkZXItcmFkaXVzOjAgNTAlIDUwJSAwOyZxdW90OyB0YXJnZXQ9JnF1b3Q7X2JsYW5rJnF1b3Q7Jmd0OyZsdDtpbWcgYWx0PSZxdW90OyZxdW90OyBzcmM9JnF1b3Q7aHR0cDovL29wZW5jYXJ0X2FkdmFuY2VkXzMwMjEubG9jYWwvaW1hZ2UvY2F0YWxvZy9lbWFpbHRlbXBsYXRlL2ljb24veW91dHViZS5wbmcmcXVvdDsgc3R5bGU9JnF1b3Q7ZGlzcGxheTppbmxpbmUtYmxvY2s7IGhlaWdodDoyNnB4OyB3aWR0aDoyNnB4JnF1b3Q7Jmd0OyZsdDsvYSZndDsmbHQ7L3RkJmd0Ow0KCQkJCQkJCQkmbHQ7L3RyJmd0Ow0KCQkJCQkJCSZsdDsvdGJvZHkmZ3Q7DQoJCQkJCQkmbHQ7L3RhYmxlJmd0Ow0KCQkJCQkmbHQ7L3RkJmd0OyZsdDsvdHImZ3Q7DQoJCQkJJmx0Oy90Ym9keSZndDsNCgkJCSZsdDsvdGFibGUmZ3Q7DQoJCQkmbHQ7L3RkJmd0Ow0KCQkJJmx0O3RkIGNsYXNzPSZxdW90O3RhYmxlLWNlbGwtc3RhY2smcXVvdDsgc3R5bGU9JnF1b3Q7Zm9udC1zaXplOjBweDt2ZXJ0aWNhbC1hbGlnbjp0b3A7d2lkdGg6MzMlOyZxdW90OyZndDsNCgkJCSZsdDt0YWJsZSBhbGlnbj0mcXVvdDtsZWZ0JnF1b3Q7IGJvcmRlcj0mcXVvdDswJnF1b3Q7IGNlbGxwYWRkaW5nPSZxdW90OzAmcXVvdDsgY2VsbHNwYWNpbmc9JnF1b3Q7MCZxdW90OyBjbGFzcz0mcXVvdDt0YWJsZS1zdGFjayZxdW90OyBzdHlsZT0mcXVvdDtib3JkZXItY29sbGFwc2U6Y29sbGFwc2U7IGJvcmRlcjpub25lOyBmb250LXNpemU6MTRweDsgbXNvLXRhYmxlLWxzcGFjZTowcHQ7IG1zby10YWJsZS1yc3BhY2U6MHB0OyB3aWR0aDoxMDAlJnF1b3Q7Jmd0Ow0KCQkJCSZsdDt0aGVhZCZndDsNCgkJCQkJJmx0O3RyJmd0Ow0KCQkJCQkJJmx0O3RoIHN0eWxlPSZxdW90O3dpZHRoOjEwcHgmcXVvdDsmZ3Q7JmFtcDtuYnNwOyZsdDsvdGgmZ3Q7DQoJCQkJCQkmbHQ7dGggc3R5bGU9JnF1b3Q7dGV4dC1hbGlnbjpjZW50ZXImcXVvdDsmZ3Q7Q3VzdG9tZXIgU2VydmljZXMmbHQ7L3RoJmd0Ow0KCQkJCQkmbHQ7L3RyJmd0Ow0KCQkJCSZsdDsvdGhlYWQmZ3Q7DQoJCQkJJmx0O3Rib2R5Jmd0Ow0KCQkJCQkmbHQ7dHImZ3Q7DQoJCQkJCQkmbHQ7dGQgc3R5bGU9JnF1b3Q7d2lkdGg6MTBweCZxdW90OyZndDsmYW1wO25ic3A7Jmx0Oy90ZCZndDsNCgkJCQkJCSZsdDt0ZCBzdHlsZT0mcXVvdDtmb250LXNpemU6MTNweDsgbGluZS1oZWlnaHQ6MjBweDsgcGFkZGluZzo4cHggMCAzcHg7IHRleHQtYWxpZ246Y2VudGVyJnF1b3Q7Jmd0Ow0KCQkJCQkJJmx0O2RpdiZndDtDYWxsIHVzOiAmbHQ7YSBocmVmPSZxdW90O3RlbDoxMjM0NTY3ODkmcXVvdDsgc3R5bGU9JnF1b3Q7Y29sb3I6aW5oZXJpdDsmcXVvdDsmZ3Q7MTIzNDU2Nzg5Jmx0Oy9hJmd0OyZsdDsvZGl2Jmd0Ow0KDQoJCQkJCQkmbHQ7ZGl2Jmd0Ow0KCQkJCQkJJmx0O3RhYmxlIGNlbGxwYWRkaW5nPSZxdW90OzAmcXVvdDsgY2VsbHNwYWNpbmc9JnF1b3Q7MCZxdW90OyBjbGFzcz0mcXVvdDtsaW5rJnF1b3Q7IHN0eWxlPSZxdW90O3dpZHRoOjEwMCUmcXVvdDsmZ3Q7DQoJCQkJCQkJJmx0O3Rib2R5Jmd0Ow0KCQkJCQkJCQkmbHQ7dHImZ3Q7DQoJCQkJCQkJCQkmbHQ7dGQgc3R5bGU9JnF1b3Q7dGV4dC1hbGlnbjpjZW50ZXImcXVvdDsmZ3Q7Jmx0O2EgaHJlZj0mcXVvdDtodHRwOi8vb3BlbmNhcnRfYWR2YW5jZWRfMzAyMS5sb2NhbC9pbmRleC5waHA/cm91dGU9aW5mb3JtYXRpb24vY29udGFjdCZxdW90OyB0YXJnZXQ9JnF1b3Q7X2JsYW5rJnF1b3Q7Jmd0OyZsdDtzdHJvbmcmZ3Q7Q29udGFjdCBVcyZsdDsvc3Ryb25nJmd0OyZsdDsvYSZndDsmbHQ7L3RkJmd0Ow0KCQkJCQkJCQkmbHQ7L3RyJmd0Ow0KCQkJCQkJCSZsdDsvdGJvZHkmZ3Q7DQoJCQkJCQkmbHQ7L3RhYmxlJmd0Ow0KCQkJCQkJJmx0Oy9kaXYmZ3Q7DQoJCQkJCQkmbHQ7L3RkJmd0Ow0KCQkJCQkmbHQ7L3RyJmd0Ow0KCQkJCSZsdDsvdGJvZHkmZ3Q7DQoJCQkmbHQ7L3RhYmxlJmd0Ow0KCQkJJmx0Oy90ZCZndDsNCgkJJmx0Oy90ciZndDsNCgkmbHQ7L3Rib2R5Jmd0Ow0KJmx0Oy90YWJsZSZndDsNCg0KJmx0O2RpdiBzdHlsZT0mcXVvdDt0ZXh0LWFsaWduOmNlbnRlciZxdW90OyZndDsmbHQ7YnImZ3Q7DQpQb3dlcmVkIGJ5ICZsdDthIGhyZWY9JnF1b3Q7aHR0cDovL3d3dy5vcGVuY2FydC5jb20vJnF1b3Q7IHRhcmdldD0mcXVvdDtfYmxhbmsmcXVvdDsmZ3Q7T3BlbmNhcnQmbHQ7L2EmZ3Q7LCBEZXNpZ25lZCBCeSAmbHQ7YSBocmVmPSZxdW90O2h0dHA6Ly93d3cub3BlbmNhcnQtdGVtcGxhdGVzLmNvLnVrLyZxdW90OyB0YXJnZXQ9JnF1b3Q7X2JsYW5rJnF1b3Q7Jmd0O09wZW5jYXJ0LXRlbXBsYXRlcyZsdDsvYSZndDsuJmx0Oy9kaXYmZ3Q7DQoiO30=',
			`emailtemplate_config_footer_padding` = '15, 0, 10, 0',
			`emailtemplate_config_footer_status` = 1,
			`emailtemplate_config_head_text` = 'YToxOntpOjE7czo4NToiJmx0O2RpdiZndDt7eyBwcmVmZXJlbmNlX3RleHQgfX0mbHQ7L2RpdiZndDsmbHQ7ZGl2Jmd0O3t7IHZpZXdfYnJvd3NlciB9fSZsdDsvZGl2Jmd0OyI7fQ==',
			`emailtemplate_config_header_bg_color` = '#515151',
			`emailtemplate_config_header_bg_image` = 'catalog/emailtemplate/head-bg.jpg',
			`emailtemplate_config_header_border_left` = '1, #cccccc',
			`emailtemplate_config_header_border_right` = '1, #cccccc',
			`emailtemplate_config_header_padding` = '10, 20, 10, 20',
			`emailtemplate_config_header_status` = 1,
			`emailtemplate_config_logo_resize` = 1,
			`emailtemplate_config_showcase` = 'latest',
			`emailtemplate_config_showcase_bg_color` = '#FFFFFF',
			`emailtemplate_config_showcase_padding` = '10, 30, 50, 30',
			`emailtemplate_config_showcase_title` = 'YToxOntpOjE7czoxNzoiWW91IG1heSBhbHNvIGxpa2UiO30=',
			`emailtemplate_config_showcase_border_left` = '1, #cccccc',
			`emailtemplate_config_showcase_border_right` = '1, #cccccc',
			`emailtemplate_config_showcase_setting` = 'YTo4OntzOjU6ImxpbWl0IjtzOjE6IjQiO3M6NzoicGVyX3JvdyI7czowOiIiO3M6NToiY3ljbGUiO3M6MToiMSI7czo3OiJyZWxhdGVkIjtzOjE6IjEiO3M6NToicHJpY2UiO3M6MToiMSI7czo1OiJpbWFnZSI7czoxOiIxIjtzOjY6InJhdGluZyI7czoxOiIwIjtzOjExOiJkZXNjcmlwdGlvbiI7czowOiIiO30=',
			`emailtemplate_config_cart_setting` = 'YTo5OntzOjY6ImxheW91dCI7czowOiIiO3M6NToiaW1hZ2UiO3M6MToiMSI7czoxMToiaW1hZ2Vfd2lkdGgiO3M6MzoiMTAwIjtzOjEyOiJpbWFnZV9oZWlnaHQiO3M6MzoiMTAwIjtzOjExOiJkZXNjcmlwdGlvbiI7czozOiIxMDAiO3M6NToicHJpY2UiO3M6MToiMSI7czo2OiJyYXRpbmciO3M6MToiMSI7czo1OiJtb2RlbCI7czoxOiIxIjtzOjY6Im9wdGlvbiI7czoxOiIxIjt9',
			`emailtemplate_config_order_products` = 'YTo5OntzOjY6ImxheW91dCI7czo3OiJkZWZhdWx0IjtzOjU6ImltYWdlIjtzOjE6IjAiO3M6MTE6ImltYWdlX3dpZHRoIjtzOjM6IjEwMCI7czoxMjoiaW1hZ2VfaGVpZ2h0IjtzOjM6IjEwMCI7czoxMToiZGVzY3JpcHRpb24iO3M6MDoiIjtzOjU6InByaWNlIjtzOjE6IjEiO3M6NjoicmF0aW5nIjtzOjE6IjAiO3M6MTU6InF1YW50aXR5X2NvbHVtbiI7czoxOiIxIjtzOjExOiJhZG1pbl9zdG9jayI7czoxOiIwIjt9',
			`emailtemplate_config_order_update` = 'YTo5OntzOjY6ImxheW91dCI7czowOiIiO3M6NToiaW1hZ2UiO3M6MToiMCI7czoxMToiaW1hZ2Vfd2lkdGgiO3M6MDoiIjtzOjEyOiJpbWFnZV9oZWlnaHQiO3M6MDoiIjtzOjExOiJkZXNjcmlwdGlvbiI7czowOiIiO3M6NToicHJpY2UiO3M6MToiMCI7czo2OiJyYXRpbmciO3M6MToiMCI7czo1OiJtb2RlbCI7czoxOiIxIjtzOjY6Im9wdGlvbiI7czoxOiIxIjt9',
			`emailtemplate_config_style` = 'border',
			`emailtemplate_config_log` = 0,
			`emailtemplate_config_log_read` = 0
		");

		$this->load->model('setting/setting');

		$settings = $this->model_setting_setting->getSetting('emailtemplate');

		if (empty($settings['emailtemplate_token'])) {
			$settings['emailtemplate_token'] = sha1(uniqid(mt_rand(), 1));
		}

		$this->model_setting_setting->editSetting('emailtemplate', $settings);

		$this->db->query("UPDATE `" . DB_PREFIX . "emailtemplate_description` SET language_id  = '".(int)$this->config->get('config_language_id')."' WHERE emailtemplate_id = 1");

        // Replace file references to old tpl with twig extensions
        $this->db->query("UPDATE `" . DB_PREFIX . "emailtemplate` SET emailtemplate_template = REPLACE(emailtemplate_template, '.tpl', '.twig')");
        $this->db->query("UPDATE `" . DB_PREFIX . "emailtemplate_config` SET emailtemplate_config_wrapper_tpl = REPLACE(emailtemplate_config_wrapper_tpl, '.tpl', '.twig')");

		// Increase `modification` length
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "modification` CHANGE `xml` `xml` MEDIUMTEXT NOT NULL");

		$this->_checkDb();

		$this->updateEvents();

		$this->clear();
	}

	/**
	 * Insert Template from SQL file
	 *
	 * @param string $key
	 * @return int template_id
	 */
	public function installTemplate($key) {
		$emailtemplate = $this->getTemplate($key);

		if ($emailtemplate) {
			$this->deleteTemplate($key);
		}

		$emailtemplate_id = false;

		// Template
		$file = DIR_APPLICATION . 'model/extension/module/emailtemplate/install/template/' . $key . '.sql';

		if (!file_exists($file)) {
			trigger_error('Error: Could not find template: ' .  $file . '!');
			exit();
		}

		foreach($this->_parse_sql($file) as $i => $stmnt) {
			if ($emailtemplate_id) {
				$stmnt = str_replace('{_ID}', (int)$emailtemplate_id, $stmnt);
			}

			$this->db->query($stmnt);

			if ($i == 0) {
				$emailtemplate_id = $this->db->getLastId();
			}
		}

		$emailtemplate = $this->getTemplate($emailtemplate_id);

		if(!$emailtemplate) return false;

		// Template Descriptions
		$this->load->model('localisation/language');

		$languages = $this->model_localisation_language->getLanguages();

		$emailtemplates_description = $this->getTemplateDescription(array('emailtemplate_id' => $emailtemplate_id), 1);

		$replace_language_vars = defined('REPLACE_LANGUAGE_VARIABLES') ? REPLACE_LANGUAGE_VARIABLES : true;

		foreach ($languages as $language) {
			$data = $emailtemplates_description;

			if ($replace_language_vars) {
				$oLanguage = new Language($language['code']);

				if (method_exists($oLanguage, 'setPath') && substr($emailtemplate['emailtemplate_key'], 0, 6) != 'admin.' && defined('DIR_CATALOG')) {
					$oLanguage->setPath(DIR_CATALOG . 'language/');
				}

				$oLanguage->load($language['code']);

				$oLanguage->load('extension/module/emailtemplate/emailtemplate');

				$langData = array();

				foreach(explode(',', $emailtemplate['emailtemplate_language_files']) as $language_file) {
					if ($language_file) {
						$_langData = $oLanguage->load(trim($language_file));
						if ($_langData) {
							$langData = array_merge($langData, $_langData);
						}
					}
				}

				$find = array();
				$replace = array();

				foreach($langData as $i => $val) {
					if ((is_string($val) && (strpos($val, '%s') === false) || is_int($val))) {
						$find[$i] = '{{ '.$i.' }}';
						$replace[$i] = $val;
					}
				}

				foreach($data as $col => $val) {
					if ($val && is_string($val)) {
						$data[$col] = str_replace($find, $replace, $val);
					}
				}
			}

			$data['language_id'] = $language['language_id'];

			$data['emailtemplate_id'] = $emailtemplate_id;

			$this->insertTemplateDescription($data);
		}

		$this->deleteTemplateDescription(array('language_id' => 0, 'emailtemplate_id' => $emailtemplate_id));

		$this->insertDefaultTemplateShortcodes($emailtemplate_id);

		$this->clear();

		return $emailtemplate_id;
	}

	/**
	 * Apply upgrade queries
	 */
	public function upgrade() {
	    $this->load->library('emailtemplate');

		$result = $this->db->query("SELECT emailtemplate_config_version FROM " . DB_PREFIX . "emailtemplate_config WHERE emailtemplate_config_id = 1 LIMIT 1");

		$current_ver = $result->row['emailtemplate_config_version'];

        if (is_float($current_ver) || strpos($current_ver, ".") !== false) {
            $dir = DIR_APPLICATION . 'model/extension/module/emailtemplate/upgrade/';

            $upgrades = glob($dir . '*.sql');
            natsort($upgrades);

            foreach ($upgrades as $i => $file) {
                $ver = substr(substr($file, 0, -4), strlen($dir));

                if (version_compare($current_ver, $ver) >= 0) {
                    continue;
                }

                $stmnts = $this->_parse_sql($file);

                foreach ($stmnts as $stmnt) {
                    $this->db->query($stmnt);
                }
            }

            // 3.0.0.0 twig shortcodes
            // emailtemplate_description
            $cols = array(
                'emailtemplate_description_subject',
                'emailtemplate_description_preview',
                'emailtemplate_description_content1',
                'emailtemplate_description_content2',
                'emailtemplate_description_content3',
                'emailtemplate_description_comment'
            );

            $result = $this->db->query("SELECT emailtemplate_id, language_id, " . implode(', ', $cols) . " FROM " . DB_PREFIX . "emailtemplate_description");

            foreach ($result->rows as $row) {
                foreach ($cols as $col) {
                    if ($row[$col]) {
                        $row[$col] = preg_replace('/{\$(.+?)}/', '{{ $1 }}', $row[$col]);
                    }
                }

                $updates = array();
                foreach ($cols as $col) {
                    if (!empty($row[$col])) {
                        $updates[] = "`" . $col . "` = '" . $this->db->escape($row[$col]) . "'";
                    }
                }

                if ($updates) {
                    $this->db->query("UPDATE " . DB_PREFIX . "emailtemplate_description SET " . implode(', ', $updates) . " WHERE emailtemplate_id = '" . (int)$row['emailtemplate_id'] . "' AND language_id = '" . (int)$row['language_id'] . "' LIMIT 1");
                }
            }

            $cols = array(
                'emailtemplate_label',
                'emailtemplate_template',
                'emailtemplate_mail_to',
                'emailtemplate_mail_cc',
                'emailtemplate_mail_bcc',
                'emailtemplate_mail_from',
                'emailtemplate_mail_sender',
                'emailtemplate_mail_replyto',
                'emailtemplate_mail_replyto_name',
                'emailtemplate_mail_attachment',
            );
            $result = $this->db->query("SELECT emailtemplate_id, " . implode(', ', $cols) . " FROM " . DB_PREFIX . "emailtemplate");
            foreach($result->rows as $row) {
                foreach($cols as $col) {
                    if ($row[$col]) {
                        $row[$col] = preg_replace('/{\$(.+?)}/', '{{ $1 }}', $row[$col]);
                    }
                }

                $updates = array();
                foreach($cols as $col) {
                    if (!empty($row[$col])) {
                        $updates[] = "`" . $col . "` = '" . $this->db->escape($row[$col]) . "'";
                    }
                }

                if ($updates) {
                    $this->db->query("UPDATE " . DB_PREFIX . "emailtemplate SET " . implode(', ', $updates) . " WHERE emailtemplate_id = '" . (int)$row['emailtemplate_id'] . "' LIMIT 1");
                }
            }

        }

		if ($this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "cron'")->num_rows !== 0) {
			if (file_exists(DIR_APPLICATION . 'model/extension/module/cron.php')) {
				$this->load->model('extension/module/cron');

				$model_cron = $this->model_extension_module_cron;
			} elseif (file_exists(DIR_APPLICATION . 'model/setting/cron.php')) {
				$this->load->model('setting/cron');

				$model_cron = $this->model_setting_cron;
			}

			if (!empty($model_cron)) {
				$cron = $model_cron->getCronByCode('module_emailtemplate');

				if (!$cron) {
					$model_cron->addCron('module_emailtemplate', 'hour', 'extension/module/emailtemplate/cron', 1);
				}
			}
        }

        $this->db->query("UPDATE " . DB_PREFIX . "emailtemplate_config SET emailtemplate_config_version = '" . $this->db->escape(EmailTemplate::getVersion()) . "'");

		$this->db->query("UPDATE " . DB_PREFIX . "emailtemplate SET emailtemplate_type = 'customer' WHERE emailtemplate_key = 'admin.newsletter'");

		if (version_compare($current_ver, '3.0.5.8') <= 0) {
            $this->db->query("UPDATE " . DB_PREFIX . "emailtemplate SET emailtemplate_order_product = 1 WHERE LEFT(emailtemplate_key, 6)  = 'order.'");

			$this->db->query("UPDATE " . DB_PREFIX . "emailtemplate SET emailtemplate_preference = 'essential'");
			$this->db->query("UPDATE " . DB_PREFIX . "emailtemplate SET emailtemplate_preference = 'notification' WHERE emailtemplate_key IN('information.contact_customer')");
			$this->db->query("UPDATE " . DB_PREFIX . "emailtemplate SET emailtemplate_preference = 'newsletter' WHERE emailtemplate_key IN('admin.newsletter')");

			$file = DIR_APPLICATION . 'model/extension/module/emailtemplate/install/install.config.sql';

			$lines = file($file);

			foreach ($lines as $lineNumber => $line) {
				$search = '`emailtemplate_config_preference_text` =';
				if (strpos($line, $search) !== false) {
					$preference_text = substr(trim($line), strlen($search)+2, -2);

					$this->db->query("UPDATE " . DB_PREFIX . "emailtemplate_config SET emailtemplate_config_preference_text = '" . $this->db->escape($preference_text) . "' WHERE emailtemplate_config_preference_text = ''");
				}
			}
		}

		// Check default template exists
        $default_template_result = $this->db->query("SELECT emailtemplate_id FROM " . DB_PREFIX . "emailtemplate WHERE emailtemplate_id = 1");
        if (!$default_template_result->row) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "emailtemplate` (`emailtemplate_id`, `emailtemplate_key`, `emailtemplate_label`, `emailtemplate_type`, `emailtemplate_template`, `emailtemplate_mail_to`, `emailtemplate_mail_cc`, `emailtemplate_mail_bcc`, `emailtemplate_mail_from`, `emailtemplate_mail_html`, `emailtemplate_mail_plain_text`, `emailtemplate_mail_sender`, `emailtemplate_mail_replyto`, `emailtemplate_mail_replyto_name`, `emailtemplate_mail_attachment`, `emailtemplate_language_files`, `emailtemplate_wrapper_tpl`, `emailtemplate_status`, `emailtemplate_default`, `emailtemplate_shortcodes`, `emailtemplate_showcase`, `emailtemplate_condition`, `emailtemplate_modified`, `emailtemplate_config_id`, `store_id`, `customer_group_id`, `order_status_id`) VALUES
            (1, 'main', 'Default', '', '', '', '', '', '', 1, 0, '', '', '', '', 'extension/module/emailtemplate/emailtemplate', '', 1, 1, 1, 1, '', NOW(), 0, NULL, 0, 0)");

            $this->db->query("INSERT INTO `" . DB_PREFIX . "emailtemplate_description` (`emailtemplate_id`, `language_id`, `emailtemplate_description_subject`, `emailtemplate_description_preview`) VALUES
            (1, 0, '', '')");
        }

        $this->_checkDb();

		$this->updateEvents();

		$this->updateModification('core');
		$this->updateModification();

		if ($this->config->get('module_emailtemplate_newsletter_status')) {
			$this->updateModification('newsletter');
		}

		if ($this->config->get('module_emailtemplate_security_status')) {
			$this->updateModification('security');
		}

		$this->clear();

		return true;
	}

	/**
	 * Method handles removing table
	 */
	public function uninstall() {
		// Events
		$events = $this->getEvents();

		if ($events) {
			foreach ($events as $event) {
				$this->deleteEvent($event['event_id']);
			}
		}

		// Tables
		$queries = array();
		$queries[] = "DROP TABLE IF EXISTS " . DB_PREFIX . "emailtemplate";
		$queries[] = "DROP TABLE IF EXISTS " . DB_PREFIX . "emailtemplate_config";
		$queries[] = "DROP TABLE IF EXISTS `" . DB_PREFIX . "emailtemplate_description`";
		$queries[] = "DROP TABLE IF EXISTS `" . DB_PREFIX . "emailtemplate_event`";
		$queries[] = "DROP TABLE IF EXISTS `" . DB_PREFIX . "emailtemplate_logs`";
		$queries[] = "DROP TABLE IF EXISTS `" . DB_PREFIX . "emailtemplate_shortcode`";
		$queries[] = "DROP TABLE IF EXISTS `" . DB_PREFIX . "emailtemplate_showcase_log`";

		foreach($queries as $query) {
			$this->db->query($query);
		}

		$this->load->model('setting/modification');

		foreach($this->modification_names as $modification_name) {
			$modification_info = $this->model_setting_modification->getModificationByCode($modification_name);

			if ($modification_info) {
				$this->model_setting_modification->deleteModification($modification_info['modification_id']);
			}
		}

		$this->clear();

		return true;
	}

	/**
	 * Create Log
	 */
	public function createTemplateLog() {
		$query = "INSERT INTO " . DB_PREFIX . "emailtemplate_logs (`emailtemplate_log_id`, `emailtemplate_log_added`) VALUES (NULL, NOW())";

		$this->db->query($query);

		$this->clear();

		return $this->db->getLastId();
	}

	public function updateTemplateLog($emailtemplate_log_id, $data) {
        $query = "UPDATE " . DB_PREFIX . "emailtemplate_logs SET `emailtemplate_log_sent` = " . ($data['emailtemplate_log_is_sent'] ? 'NOW()' : 'NULL') . ", emailtemplate_log_enc = '" . $this->db->escape($data['emailtemplate_log_enc']) . "', emailtemplate_log_to = '" . $this->db->escape($data['emailtemplate_log_to']) . "', emailtemplate_log_from = '" . $this->db->escape($data['emailtemplate_log_from']) . "', emailtemplate_log_sender = '" . $this->db->escape($data['emailtemplate_log_sender']) . "', emailtemplate_log_heading = '" . $this->db->escape($data['emailtemplate_log_heading']) . "', emailtemplate_log_reply_to = '" . $this->db->escape($data['emailtemplate_log_reply_to']) . "', emailtemplate_log_cc = '" . $this->db->escape($data['emailtemplate_log_cc']) . "', emailtemplate_log_bcc = '" . $this->db->escape($data['emailtemplate_log_bcc']) . "', emailtemplate_log_subject = '" . $this->db->escape($data['emailtemplate_log_subject']) . "', emailtemplate_log_content = '" . $this->db->escape($data['emailtemplate_log_content']) . "', emailtemplate_log_is_sent = " . ($data['emailtemplate_log_is_sent'] ? (int)$data['emailtemplate_log_is_sent'] : 'NULL') . ", emailtemplate_id = '" . (int)$data['emailtemplate_id'] .  "', emailtemplate_key = '" . $this->db->escape($data['emailtemplate_key']) . "', store_id = '" . (int)$data['store_id'] . "', language_id = '" . (int)$data['language_id'] . "', customer_id = '" . (int)$data['customer_id'] . "' WHERE emailtemplate_log_id = " . (int)$emailtemplate_log_id;

		$this->db->query($query);
	}

	/**
	 * Get template files
	 *
	 * @return array
	 */
	public function getTemplateFiles() {
		$return = array(
			'catalog' => array(),
			'admin' => array(),
			'dirs' => array()
		);

		$base = substr(DIR_SYSTEM, 0, -7);

		$dir = 'catalog/view/theme/default/template/extension/module/emailtemplate/';
		$return['dirs']['catalog'] = $dir;
		$files = glob($base.$dir.'*.twig');
		if ($files) {
			foreach($files as $file) {
				$filename = basename($file);
				if ($filename[0] == '_') continue;
				$return['catalog'][] = $filename;
			}
		}

		$dir = str_replace($base, '', DIR_TEMPLATE) .'extension/module/emailtemplate/mail/';
		$return['dirs']['admin'] = $dir;
		$files = glob($base.$dir.'*.twig');
		if ($files) {
			foreach($files as $file) {
				$filename = basename($file);
				if ($filename[0] == '_') continue;
				$return['admin'][] = $filename;
			}
		}

		return $return;
	}

	/**
	 * Table check exists
	 *
	 * @param $name
	 * @return bool
	 */
	public function tableExists($name) {
		if (!$name) return false;

		$result = $this->db->query("SELECT * FROM information_schema.tables WHERE table_schema = '" . DB_DATABASE . "' AND table_name = '" . DB_PREFIX . $this->db->escape($name) . "' LIMIT 1");

		return $result->num_rows ? true : false;
	}

	/**
	 * Check version of files with databse
	 */
	public function checkVersion() {
        $this->load->library('emailtemplate');

        $result = $this->db->query("SELECT `emailtemplate_config_version` FROM " . DB_PREFIX . "emailtemplate_config WHERE `emailtemplate_config_id` = 1 LIMIT 1");

        if ($result->row && version_compare(EmailTemplate::getVersion(), $result->row['emailtemplate_config_version']) > 0) {
			return $result->row['emailtemplate_config_version'];
		}

		return false;
	}

	/**
	 * Get all stores
	 */
	public function getStores() {
		$stores = array();

		$stores[] = array(
			'store_id' => 0,
			'name' => $this->config->get('config_name'),
			'url' => HTTP_CATALOG,
			'ssl' => HTTPS_CATALOG
		);

		$this->load->model('setting/store');

		$result = $this->model_setting_store->getStores();

		if ($result) {
			foreach ($result as $row) {
				$stores[] = array(
					'store_id' => $row['store_id'],
					'name' => $row['name'],
					'url' => $row['url'],
					'ssl' => $row['ssl']
				);
			}
		}

		return $stores;
	}

	public function getUrl($route, $key, $value) {
		$url = "index.php?route=" . rawurlencode($route) . "&" . rawurlencode($key) . "=" . rawurlencode($value);

		/*if ($this->config->get('config_seo_url')) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "'");
			if (!empty($query->row['keyword'])) {
				$url = $query->row['keyword'];
			}
		}*/

		return $url;
	}

	public function getEvents($data = array()) {
		if (!empty($data) && isset($data['emailtemplate_key'])) {
			$cond = "e.event_id = (SELECT ee.event_id FROM emailtemplate_event WHERE emailtemplate_id = '" . $this->db->escape($data['emailtemplate_key']) . "' LIMIT 1)";
		} else {
			$cond = "(e.code LIKE 'emailtemplate_%' OR e.action LIKE 'extension/module/emailtemplate%')";
		}

		$query = "SELECT * FROM `" . DB_PREFIX . "event` e WHERE ". $cond ." ORDER BY `trigger` ASC, `sort_order` ASC";

		$result = $this->db->query($query);

		return $result->rows;
	}

	public function getEmailTemplateEvents($data = array()) {
		$query = "SELECT * FROM `" . DB_PREFIX . "emailtemplate_event` ee";

		if (isset($data['emailtemplate_key'])) {
			$query .= " WHERE emailtemplate_key = '" . $this->db->escape($data['emailtemplate_key']) . "'";
		}

		$result = $this->db->query($query);

		return $result->rows;
	}

	public function addEvent($data) {
		if (!isset($data['code'], $data['trigger'], $data['emailtemplate_key'])) {
			trigger_error('Missing data');
			return false;
		}

		if (!isset($data['sort_order'])) $data['sort_order'] = 0;
		if (!isset($data['status'])) $data['status'] = 1;

		$query = $this->db->query("SELECT event_id FROM " . DB_PREFIX . "event WHERE `code` = '" . $this->db->escape($data['code']) . "'");

		if ($query->row) {
			$event_id = $query->row['event_id'];
		} else {
			$this->load->model('setting/event');

			$event_id = $this->model_setting_event->addEvent($data['code'], $data['trigger'], $data['action'], $data['status'], $data['sort_order']);

            $this->db->query("INSERT INTO " . DB_PREFIX . "emailtemplate_event SET `event_id` = '" . (int)$event_id . "', `emailtemplate_key` = '" . $this->db->escape($data['emailtemplate_key']) . "'");
        }

        return $event_id;
	}

	public function updateEvents() {
		$events = array();

		$events[] = array('emailtemplate_key' => 'order.return', 'code' => 'mail_account_return', 'trigger' => 'catalog/model/account/return/addReturn/after', 'action' => 'extension/module/emailtemplate/event/returnCreate');
		$events[] = array('emailtemplate_key' => 'admin.customer_create', 'code' => 'admin_mail_customer_add', 'trigger' => 'admin/model/customer/customer/addCustomer/after', 'action' => 'extension/module/emailtemplate/event/customerCreate');
        $events[] = array('emailtemplate_key' => 'admin.customer_history', 'code' => 'admin_mail_customer_history', 'trigger' => 'admin/model/customer/customer/addHistory/after', 'action' => 'extension/module/emailtemplate/event/customerHistory');

		foreach($events as $event) {
			$this->addEvent($event);
		}
	}

	public function deleteEvent($event_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "emailtemplate_event WHERE `event_id` = '" . (int)$event_id . "'");

		$this->load->model('setting/event');

		$this->model_setting_event->deleteEvent($event_id);

		return true;
	}

	public function getCustomerHistory($customer_history_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_history WHERE customer_history_id = '" . (int)$customer_history_id . "'");

		return $query->row;
	}

	/**
	 * Update modification
	 */
	public function updateModification($name = '') {
		$this->load->library('emailtemplate');

		$this->load->model('setting/modification');

		$extension_install_id = 0;

		$query = $this->db->query("SELECT extension_id FROM `" . DB_PREFIX . "extension` WHERE `type` = 'module' AND `code` = 'emailtemplate' LIMIT 1");

		if ($query && !empty($query->row['extension_id'])) {
			$extension_install_id = $query->row['extension_id'];
		}

		switch(strtolower($name)){
			case 'core':
			case 'newsletter':
			case 'security':
				if ($name == 'core') {
					$file = DIR_APPLICATION . 'model/extension/module/emailtemplate/install/install.xml';
				} elseif ($name == 'newsletter') {
					$file = DIR_APPLICATION . 'model/extension/module/emailtemplate/install/newsletter.xml';
				} elseif ($name == 'security') {
					$file = DIR_APPLICATION . 'model/extension/module/emailtemplate/install/security.xml';
				}

				if (!file_exists($file)) {
					trigger_error('Missing install file: ' . $file);
					exit;
				}

				$modification_data = array(
					'extension_install_id' => $extension_install_id,
					'name' => "Email Templates " . ucfirst($name),
					'code' => "emailtemplates_" . $name,
					'author' => "Opencart-Templates",
					'version' => EmailTemplate::getVersion(),
					'link' => "https://www.opencart-templates.co.uk/advanced_professional_email_template",
					'xml' => file_get_contents($file),
					'status' => 1
				);

				$modification_info = $this->model_setting_modification->getModificationByCode("emailtemplates_" . $name);

				if ($modification_info) {
					$this->model_setting_modification->deleteModification($modification_info['modification_id']);
				}

				if(!empty($modification_data)){
					$this->model_setting_modification->addModification($modification_data);
				}
				break;

			default:
				$chk = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX . "emailtemplate'");
				if ($chk->num_rows) {
					$query = $this->db->query("SELECT emailtemplate_key FROM " . DB_PREFIX . "emailtemplate WHERE emailtemplate_status = 1 GROUP BY emailtemplate_key");

					if ($query->rows) {
						$modification_data = array(
							'extension_install_id' => $extension_install_id,
							'name' => "Email Templates",
							'code' => "emailtemplates",
							'author' => "Opencart-Templates",
							'version' => EmailTemplate::getVersion(),
							'link' => "https://www.opencart-templates.co.uk/advanced_professional_email_template",
							'status' => 1
						);

						$modification_index = array();

						$modification_data['xml'] = "<modification>
	<name>" . $modification_data['name'] . "</name>
	<code>" . $modification_data['code'] . "</code>
	<author>" . $modification_data['author'] . "</author>
	<version>" . $modification_data['version'] . "</version>
	<link>" . $modification_data['link'] . "</link>";

						foreach ($query->rows as $row) {
							// Remove key .index
							$emailtemplate_key = $emailtemplate_key = preg_replace('/\.[0-9]*$/', '', $row['emailtemplate_key']);

							if (isset($modification_index[$emailtemplate_key])) continue;

							$file = DIR_APPLICATION . 'model/extension/module/emailtemplate/install/modification/' . $emailtemplate_key . '.xml';

							if (file_exists($file)) {
								$modification_index[$emailtemplate_key] = true;

								$modification_data['xml'] .= "\n\n" . file_get_contents($file);
							}
						}

						$modification_data['xml'] .= "\n</modification>";
					}
				}

				$modification_info = $this->model_setting_modification->getModificationByCode("emailtemplates");

				if ($modification_info) {
					$this->model_setting_modification->deleteModification($modification_info['modification_id']);
				}

				if(!empty($modification_data)){
					$this->model_setting_modification->addModification($modification_data);
				}
				break;
		}

		$this->clear();
	}

	public function formatAddress($address, $address_prefix = '', $format = null) {
		$find = array();
		$replace = array();

		if ($address_prefix != "") {
			$address_prefix = trim($address_prefix, '_') . '_';
		}

		if (is_null($format) || !is_string($format) || $format == '') {
			$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
		}

		$vars = array(
			'firstname',
			'lastname',
			'telephone',
			'company',
			'address_1',
			'address_2',
			'city',
			'postcode',
			'zone',
			'zone_code',
			'country'
		);

        foreach ($vars as $var) {
            if ($address_prefix && isset($address[$address_prefix.$var])) {
                $value = $address[$address_prefix.$var];
            } elseif (isset($address[$var])) {
                $value = $address[$var];
            } else {
                $value = '';
            }

            if (is_numeric($value) || is_string($value)|| is_null($value)|| is_bool($value)) {
                $find[$var] = '{'.$var.'}';
                $replace[$var] = $value;
            }
        }

        foreach(array('custom_field', $address_prefix . 'custom_field') as $var) {
            if (isset($address[$var]) && is_array($address[$var])) {
                foreach ($address[$var] as $custom_field_id => $custom_field) {
                    if (!isset($custom_field['value'])) {
                        continue;
                    }

                    $var = 'custom_field_' . $custom_field_id;
                    $value = $custom_field['value'];

                    if (is_numeric($value) || is_string($value) || is_null($value) || is_bool($value)) {
                        $find[$var] = '{custom_field_' . $custom_field_id . '}';
                        $replace[$var] = $value;
                    }
                }
            }
        }

		return trim(str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', str_replace($find, $replace, $format))));
	}

	private function _templateShortcodesCondition($data) {
		$where = array();

		if (is_array($data)) {
			if (isset($data['emailtemplate_shortcode_id']) && $data['emailtemplate_shortcode_id'] != 0) {
				$where[] = "es.`emailtemplate_shortcode_id` = '".(int)$data['emailtemplate_shortcode_id']."'";
			}

			if (isset($data['emailtemplate_id']) && $data['emailtemplate_id'] != 0) {
				$where[] = "es.`emailtemplate_id` = '".(int)$data['emailtemplate_id']."'";
			}

			if (!empty($data['emailtemplate_shortcode_type'])) {
				$where[] = "es.`emailtemplate_shortcode_type` = '".$this->db->escape($data['emailtemplate_shortcode_type'])."'";
			}

			if (isset($data['emailtemplate_key'])) {
				$result = $this->db->query("SELECT emailtemplate_id FROM " . DB_PREFIX . "emailtemplate WHERE emailtemplate_key = '".$this->db->escape($data['emailtemplate_key'])."' AND emailtemplate_default = 1 LIMIT 1");
				if (!empty($result->row)) {
					$where[] = "es.`emailtemplate_id` = '".(int)$result->row['emailtemplate_id']."'";
				}
			}

			if (isset($data['filter_shortcodes_search'])) {
				$where[] = "(es.`emailtemplate_shortcode_code` LIKE '%" . $this->db->escape($data['filter_shortcodes_search']) . "%' OR es.`emailtemplate_shortcode_example` LIKE '%" . $this->db->escape($data['filter_shortcodes_search']) . "%')";
			}
		} else {
			$where[] = "es.`emailtemplate_id` = '".(int)$data."'";
		}

		return $where;
	}

	/**
	 * Method builds mysql for INSERT/UPDATE
	 *
	 * @param array $cols
	 * @param array $data
	 * @return array
	 */
	private function _build_query($cols, $data, $withoutCols = false) {
		if (empty($data)) return $data;
		$return = array();

		foreach ($cols as $col => $type) {
			if (!array_key_exists($col, $data)) continue;

			switch ($type) {
				case EmailTemplateAbstract::INT:
					if (strtoupper($data[$col]) == 'NULL') {
						$value = 'NULL';
					} else {
						$value = (int)$data[$col];
					}
					break;
				case EmailTemplateAbstract::FLOAT:
					$value = floatval($data[$col]);
					break;
				case EmailTemplateAbstract::DATE_NOW:
					$value = 'NOW()';
					break;
				case EmailTemplateAbstract::SERIALIZE:
					if ($data[$col]) {
						$value = "'".base64_encode(serialize($data[$col]))."'";
					} else {
						$value = 'NULL';
					}
					break;
				default:
					$value = "'".$this->db->escape((string)$data[$col])."'";
			}

			if ($withoutCols) {
				$return[] = "'{$value}'";
			} else {
				$return[] = " `".$this->db->escape($col)."` = {$value}";
			}
		}

		return empty($return) ? false : $return;
	}

	private function _checkDb() {
		// Add language_id to `customers`
		$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "customer` LIKE 'language_id'");
		if (!$chk->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` ADD `language_id` int(11) NOT NULL DEFAULT '0' AFTER `store_id`");
		}

		// Add weight to `orders`
		$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'weight'");
		if (!$chk->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD `weight` decimal(15,8) NOT NULL DEFAULT '0.00000000' AFTER `invoice_prefix`");
		}

		// Add notified to `customer_history`
		$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "customer_history` LIKE 'notified'");
		if (!$chk->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer_history` ADD `notified` TINYINT(1) NULL DEFAULT NULL;");
		}

		// Fix trigger events
		if (version_compare(VERSION, '3.0.0.0') >= 0 && version_compare(VERSION, '3.1.0.0') < 0) {
			$this->db->query("UPDATE `" . DB_PREFIX . "event` SET `trigger` = 'catalog/model/account/customer/addAffiliate/after' WHERE `code` IN ('mail_affiliate_add', 'mail_affiliate_alert')");
			$this->db->query("UPDATE `" . DB_PREFIX . "event` SET `trigger` = 'admin/model/sale/return/addReturnHistory/after' WHERE `code` = 'admin_mail_return'");
		}

		$missing_cols = array();

		// Check for missing columns
        $result = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "emailtemplate");
        $cols = array();
        foreach ($result->rows as $row) {
            $cols[] = $row['Field'];
        }
        $diff = array_diff(array_keys(EmailTemplateDAO::describe()), $cols);
        if ($diff) {
            $missing_cols = array_merge($diff, $missing_cols);
        }

        $result = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "emailtemplate_config");
        $cols = array();
        foreach ($result->rows as $row) {
            $cols[] = $row['Field'];
        }
        $diff = array_diff(array_keys(EmailTemplateConfigDAO::describe()), $cols);
        if ($diff) {
            $missing_cols = array_merge($diff, $missing_cols);
        }

        $result = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "emailtemplate_shortcode");
        $cols = array();
        foreach ($result->rows as $row) {
            $cols[] = $row['Field'];
        }
        $diff = array_diff(array_keys(EmailTemplateShortCodesDAO::describe()), $cols);
        if ($diff) {
            $missing_cols = array_merge($diff, $missing_cols);
        }

        $result = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "emailtemplate_description");
        $cols = array();
        foreach ($result->rows as $row) {
            $cols[] = $row['Field'];
        }
        $diff = array_diff(array_keys(EmailTemplateDescriptionDAO::describe()), $cols);
        if ($diff) {
            $missing_cols = array_merge($diff, $missing_cols);
        }

		if ($missing_cols) {
		    echo '<pre>';
		    print_r($missing_cols);
		    die('Missing Columns');
        }
	}

	/**
	 * Parse SQL file and split into single sql statements.
	 *
	 * @param string $sql - file path
	 * @return array
	 */
	private function _parse_sql($file) {
		$sql = @fread(@fopen($file, 'r'), @filesize($file)) or die('problem reading sql:'.$file);
		$sql = str_replace(" `oc_db_name` ", " `" . DB_DATABASE, $sql);
		$sql = str_replace(" `oc_", " `" . DB_PREFIX, $sql);

		$lines = explode("\n", $sql);
		$linecount = count($lines);
		$sql = "";
		for ($i = 0; $i < $linecount; $i++) {
			if (($i != ($linecount - 1)) || (strlen($lines[$i]) > 0)) {
				if (isset($lines[$i][0]) && $lines[$i][0] != "#") {
					$sql .= $lines[$i] . "\n";
				} else {
					$sql .= "\n";
				}
				$lines[$i] = "";
			}
		}

		$tokens = explode(';', $sql);
		$sql = "";

		$queries = array();
		$matches = array();

		$token_count = count($tokens);
		for ($i = 0; $i < $token_count; $i++) {

			if (($i != ($token_count - 1)) || (strlen($tokens[$i] > 0))) {
				$total_quotes = preg_match_all("/'/", $tokens[$i], $matches);
				$escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$i], $matches);
				$unescaped_quotes = $total_quotes - $escaped_quotes;

				if (($unescaped_quotes % 2) == 0) {
					$queries[] = trim($tokens[$i]);
					$tokens[$i] = "";
				} else {
					$temp = $tokens[$i] . ';';
					$tokens[$i] = "";
					$complete_stmt = false;

					for ($j = $i + 1; (!$complete_stmt && ($j < $token_count)); $j++) {
						$total_quotes = preg_match_all("/'/", $tokens[$j], $matches);
						$escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$j], $matches);
						$unescaped_quotes = $total_quotes - $escaped_quotes;

						if (($unescaped_quotes % 2) == 1) {
							$queries[] = trim($temp . $tokens[$j]);
							$tokens[$j] = "";
							$temp = "";
							$complete_stmt = true;
							$i = $j;
						} else {
							$temp .= $tokens[$j] . ';';
							$tokens[$j] = "";
						}

					}
				}
			}
		}

		return $queries;
	}

	/**
	 * Truncate Text
	 *
	 * @param string $text
	 * @param int $limit
	 * @param string $ellipsis
	 * @return string
	 */
	private function _truncate($str, $length = true, $breakWords = true, $append = '...') {
		if ($length === null) {
			$length = 100;
		} elseif ($length === true) {
			$length = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length');
		}

		$str = strip_tags(html_entity_decode($str, ENT_QUOTES, 'UTF-8'));

		$strLength = utf8_strlen($str);
		if ($strLength <= $length) {
			return $str;
		}

		if (!$breakWords) {
			while ($length < $strLength AND preg_match('/^\pL$/', utf8_substr($str, $length, 1))) {
				$length++;
			}
		}

		$str = utf8_substr($str, 0, $length) . $append;
		$str = preg_replace('/\s{3,}/',' ', $str);
		$str = trim($str);

		return $str;
	}

	/**
	 * Fetch query with caching
	 */
	private function _fetch($query, $key = '', $cache = false) {
	    if (!$cache) {
            return $this->db->query($query);
        }

		$queryName = 'emailtemplate_sql_'. (($key) ? $key . '_' : '') . md5($query);

		$result = $this->cache->get($queryName);

		if ($result) {
			$result = (object)$result;
		} else {
			$result = $this->db->query($query);

			$this->cache->set($queryName, $result);
		}

		return $result;
	}

	/**
	 * Delete all cache files for emailtemplate
	 */
	public function clear($prefix = 'emailtemplate_sql_') {
		switch ($this->config->get('cache_engine')) {
			case 'apc':
			case 'mem':
			case 'memcached':
				$keys = $this->cache->getAllKeys();
				if ($keys) {
					foreach($keys as $key) {
                        // with/without cache prefix
                        if (substr($key, 0, strlen($prefix)) == $prefix) {
                            $this->cache->delete($key);
                        } elseif (substr($key, 0, strlen(CACHE_PREFIX . $prefix)) == CACHE_PREFIX . $prefix) {
                            $this->cache->delete(substr($key, strlen(CACHE_PREFIX)));
                        }
					}
				} elseif ($keys === false) {
					$this->cache->flush(); // flush all cache not all memcache versions include getAllKeys method.
				}
				break;
			default:
				$key = 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $prefix) . '*';
				$files = glob(DIR_CACHE . $key);
				if ($files) {
					foreach ($files as $file) {
						@unlink($file);

						if (file_exists($file)) {
							trigger_error('Warning: Unable to delete ' . $file);
						}
					}
				}
		}

		// CSS config cache
		$files = glob(DIR_CACHE . 'mail_css/*');
		if ($files) {
			foreach ($files as $file) {
				@unlink($file);

				if (file_exists($file)) {
					trigger_error('Warning: Unable to delete ' . $file);
				}
			}
		}
	}
}


/**
 * Data Access Object - Abstract
 */
abstract class EmailTemplateAbstract
{
	/**
	 * Data Types
	 */
	const INT = "INT";
	const TEXT = "TEXT";
	const SERIALIZE = "SERIALIZE";
	const FLOAT = "FLOAT";
	const DATE_NOW = "DATE_NOW";

	/**
	 * Filter from array, by unsetting element(s)
	 * @param string/array $filter - match array key
	 * @param array to be filtered
	 * @return array
	 */
	protected static function filterArray($filter, $array) {
		if ($filter === null) return $array;

		if (is_array($filter)) {
			foreach($filter as $f) {
				unset($array[$f]);
			}
		} else {
			unset($array[$filter]);
		}

		return $array;
	}

}

/**
 * Email Templates `emailtemplate`
 */
class EmailTemplateDAO extends EmailTemplateAbstract
{
	/**
	 * Columns & Data Types.
	 * @see EmailTemplateDAOAbstract::describe()
	 */
	public static function describe() {
		$filter = func_get_args();
		$cols = array(
            'customer_group_id' => EmailTemplateAbstract::INT,
            'emailtemplate_condition' => EmailTemplateAbstract::SERIALIZE,
            'emailtemplate_config_id' => EmailTemplateAbstract::INT,
            'emailtemplate_default' => EmailTemplateAbstract::INT,
            'emailtemplate_id' => EmailTemplateAbstract::INT,
            'emailtemplate_key' => EmailTemplateAbstract::TEXT,
            'emailtemplate_label' => EmailTemplateAbstract::TEXT,
            'emailtemplate_language_files' => EmailTemplateAbstract::TEXT,
            'emailtemplate_log' => EmailTemplateAbstract::INT,
            'emailtemplate_mail_attachment' => EmailTemplateAbstract::TEXT,
            'emailtemplate_mail_bcc' => EmailTemplateAbstract::TEXT,
            'emailtemplate_mail_cc' => EmailTemplateAbstract::TEXT,
            'emailtemplate_mail_from' => EmailTemplateAbstract::TEXT,
            'emailtemplate_mail_html' => EmailTemplateAbstract::INT,
            'emailtemplate_mail_plain_text' => EmailTemplateAbstract::INT,
            'emailtemplate_mail_queue' => EmailTemplateAbstract::INT,
            'emailtemplate_mail_replyto' => EmailTemplateAbstract::TEXT,
            'emailtemplate_mail_replyto_name' => EmailTemplateAbstract::TEXT,
            'emailtemplate_mail_sender' => EmailTemplateAbstract::TEXT,
            'emailtemplate_mail_to' => EmailTemplateAbstract::TEXT,
            'emailtemplate_modified' => EmailTemplateAbstract::DATE_NOW,
            'emailtemplate_shortcodes' => EmailTemplateAbstract::INT,
            'emailtemplate_showcase' => EmailTemplateAbstract::TEXT,
            'emailtemplate_showcase_selection' => EmailTemplateAbstract::TEXT,
            'emailtemplate_cart_product' => EmailTemplateAbstract::INT,
            'emailtemplate_order_product' => EmailTemplateAbstract::INT,
			'emailtemplate_preference' => EmailTemplateAbstract::TEXT,
			'emailtemplate_send_max' => EmailTemplateAbstract::INT,
			'emailtemplate_status' => EmailTemplateAbstract::INT,
			'emailtemplate_template' => EmailTemplateAbstract::TEXT,
			'emailtemplate_type' => EmailTemplateAbstract::TEXT,
            'emailtemplate_wrapper_tpl' => EmailTemplateAbstract::TEXT,
			'payment_method' => EmailTemplateAbstract::TEXT,
			'shipping_method' => EmailTemplateAbstract::TEXT,
            'event_id' => EmailTemplateAbstract::INT,
            'order_status_id' => EmailTemplateAbstract::INT,
            'store_id' => EmailTemplateAbstract::INT
		);

		return (!$filter)? $cols : self::filterArray($filter, $cols);
	}
}

/**
 * Email Templates `emailtemplate_description`
 */
class EmailTemplateDescriptionDAO extends EmailTemplateAbstract
{
	/**
	 * Columns & Data Types.
	 * @see EmailTemplateDAOAbstract::describe()
	 */
	public static function describe() {
		$filter = func_get_args();
        $cols = array(
            'emailtemplate_description_comment' => EmailTemplateAbstract::TEXT,
            'emailtemplate_description_content1' => EmailTemplateAbstract::TEXT,
            'emailtemplate_description_content2' => EmailTemplateAbstract::TEXT,
            'emailtemplate_description_content3' => EmailTemplateAbstract::TEXT,
            'emailtemplate_description_heading' => EmailTemplateAbstract::TEXT,
            'emailtemplate_description_preview' => EmailTemplateAbstract::TEXT,
            'emailtemplate_description_cart_title' => EmailTemplateAbstract::TEXT,
            'emailtemplate_description_order_title' => EmailTemplateAbstract::TEXT,
            'emailtemplate_description_showcase_title' => EmailTemplateAbstract::TEXT,
            'emailtemplate_description_subject' => EmailTemplateAbstract::TEXT,
            'emailtemplate_id' => EmailTemplateAbstract::INT,
            'language_id' => EmailTemplateAbstract::INT,
        );

		return (!$filter)? $cols : self::filterArray($filter, $cols);
	}
}

/**
 * Email Templates `emailtemplate_shortcode`
 */
class EmailTemplateShortCodesDAO extends EmailTemplateAbstract
{
	/**
	 * Columns & Data Types.
	 * @see EmailTemplateDAOAbstract::describe()
	 */
	public static function describe() {
		$filter = func_get_args();
		$cols = array(
            'emailtemplate_id' => EmailTemplateAbstract::INT,
            'emailtemplate_shortcode_code' => EmailTemplateAbstract::TEXT,
            'emailtemplate_shortcode_example' => EmailTemplateAbstract::TEXT,
            'emailtemplate_shortcode_id' => EmailTemplateAbstract::INT,
            'emailtemplate_shortcode_type' => EmailTemplateAbstract::TEXT,
		);

		return (!$filter)? $cols : self::filterArray($filter, $cols);
	}
}


/**
 * Config `emailtemplate_config`
 */
class EmailTemplateConfigDAO extends EmailTemplateAbstract
{
	/**
	 * Columns & Data Types.
	 * @see EmailTemplateAbstract::describe()
	 */
	public static function describe() {
		$filter = func_get_args();
        $cols = array(
            'customer_group_id' => EmailTemplateAbstract::INT,
			'emailtemplate_config_admin' => EmailTemplateAbstract::INT,
            'emailtemplate_config_body_bg_color' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_body_bg_image' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_body_bg_image_position' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_body_bg_image_repeat' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_body_font_color' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_body_font_custom' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_body_font_family' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_body_font_size' => EmailTemplateAbstract::INT,
            'emailtemplate_config_body_font_url' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_body_font_source' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_body_heading_color' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_body_link_color' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_body_section_bg_color' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_cart_setting' => EmailTemplateAbstract::SERIALIZE,
            'emailtemplate_config_css_custom' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_email_responsive' => EmailTemplateAbstract::INT,
            'emailtemplate_config_email_width' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_footer_bg_color' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_footer_border_bottom' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_footer_border_left' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_footer_border_radius' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_footer_border_right' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_footer_border_top' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_footer_font_color' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_footer_font_size' => EmailTemplateAbstract::INT,
            'emailtemplate_config_footer_padding' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_footer_section_bg_color' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_footer_spacing' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_footer_status' => EmailTemplateAbstract::INT,
            'emailtemplate_config_footer_text' => EmailTemplateAbstract::SERIALIZE,
            'emailtemplate_config_head_text' => EmailTemplateAbstract::SERIALIZE,
            'emailtemplate_config_header_bg_color' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_header_bg_image' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_header_border_bottom' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_header_border_left' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_header_border_radius' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_header_border_right' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_header_border_top' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_header_height' => EmailTemplateAbstract::INT,
            'emailtemplate_config_header_html' => EmailTemplateAbstract::SERIALIZE,
            'emailtemplate_config_header_section_bg_color' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_header_padding' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_header_spacing' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_header_status' => EmailTemplateAbstract::INT,
            'emailtemplate_config_id' => EmailTemplateAbstract::INT,
            'emailtemplate_config_link_style' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_log' => EmailTemplateAbstract::INT,
            'emailtemplate_config_log_read' => EmailTemplateAbstract::INT,
            'emailtemplate_config_logo' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_logo_align' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_logo_font_color' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_logo_font_size' => EmailTemplateAbstract::INT,
            'emailtemplate_config_logo_height' => EmailTemplateAbstract::INT,
            'emailtemplate_config_logo_width' => EmailTemplateAbstract::INT,
            'emailtemplate_config_logo_resize' => EmailTemplateAbstract::INT,
            'emailtemplate_config_name' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_order_products' => EmailTemplateAbstract::SERIALIZE,
            'emailtemplate_config_order_update' => EmailTemplateAbstract::SERIALIZE,
            'emailtemplate_config_page_align' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_page_bg_color' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_page_border_bottom' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_page_border_left' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_page_border_radius' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_page_border_right' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_page_border_top' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_page_footer_text' => EmailTemplateAbstract::SERIALIZE,
            'emailtemplate_config_page_padding' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_page_shadow' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_page_spacing' => EmailTemplateAbstract::TEXT,
			'emailtemplate_config_preference_text' => EmailTemplateAbstract::SERIALIZE,
            'emailtemplate_config_shadow_bottom' => EmailTemplateAbstract::SERIALIZE,
            'emailtemplate_config_shadow_left' => EmailTemplateAbstract::SERIALIZE,
            'emailtemplate_config_shadow_right' => EmailTemplateAbstract::SERIALIZE,
            'emailtemplate_config_shadow_top' => EmailTemplateAbstract::SERIALIZE,
            'emailtemplate_config_showcase' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_showcase_bg_color' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_showcase_border_bottom' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_showcase_border_left' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_showcase_border_radius' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_showcase_border_right' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_showcase_border_top' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_showcase_setting' => EmailTemplateAbstract::SERIALIZE,
            'emailtemplate_config_showcase_padding' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_showcase_section_bg_color' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_showcase_selection' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_showcase_title' => EmailTemplateAbstract::SERIALIZE,
            'emailtemplate_config_status' => EmailTemplateAbstract::INT,
            'emailtemplate_config_style' => EmailTemplateAbstract::TEXT,
            'emailtemplate_config_text_align' => EmailTemplateAbstract::TEXT,
			'emailtemplate_config_unsubscribe' => EmailTemplateAbstract::TEXT,
			'emailtemplate_config_version' => EmailTemplateAbstract::TEXT,
			'emailtemplate_config_view_browser' => EmailTemplateAbstract::INT,
            'emailtemplate_config_view_browser_theme' => EmailTemplateAbstract::INT,
            'emailtemplate_config_wrapper_tpl' => EmailTemplateAbstract::TEXT,
            'language_id' => EmailTemplateAbstract::INT,
            'store_id' => EmailTemplateAbstract::INT
        );

		return (!$filter)? $cols : self::filterArray($filter, $cols);
	}
}
