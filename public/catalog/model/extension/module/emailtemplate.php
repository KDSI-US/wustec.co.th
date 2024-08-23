<?php
class ModelExtensionModuleEmailTemplate extends Model {

    private $content_count = 3;

    /**
     * Load Template
     * @param string|number|array $load template key OR template id array()
     * @param array $template_data
     * @param null $conditions
     * @return bool
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

        if (!$templates) {
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

        $customer_group_id = null;

        if ($this->customer && $this->customer->isLogged()) {
            $customer_id = $this->customer->getId();
            $customer_group_id = $this->customer->getGroupId();
        }  else {
            if (!empty($load['customer_id'])) {
                $customer_id = $load['customer_id'];
            } else {
                $customer_id = 0;
            }

            if (!empty($load['customer_group_id'])) {
                $customer_group_id = $load['customer_group_id'];
            }
        }

        $customer_info = array();

        if ($customer_id) {
            $this->load->model('account/customer');
            $customer_info = $this->model_account_customer->getCustomer($customer_id);
        } elseif (!empty($load['email'])) {
            $this->load->model('account/customer');
            $customer_info = $this->model_account_customer->getCustomerByEmail($load['email']);
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

        $store_info = array();

        $stores = $this->model_setting_store->getStores();

        foreach ($stores as $store) {
            if ($store['store_id'] == $store_id) {
                $store_info = $store;
            }
        }

        $store_info = array_merge(
            $this->model_setting_setting->getSetting("config", $store_id),
            $store_info
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
            $this->emailtemplate->data['store_url'] = HTTPS_SERVER ? HTTPS_SERVER : HTTP_SERVER;
        }

        if (empty($this->emailtemplate->data['store_ssl'])) {
            $this->emailtemplate->data['store_ssl'] = HTTPS_SERVER;
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
                $unserialized = @unserialize(base64_decode($this->emailtemplate->data['config'][$var]));
                $this->emailtemplate->data['config'][$var] = ($unserialized !== false) ? $unserialized : $this->emailtemplate->data['config'][$var];

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

        foreach (array('header_border_radius','footer_border_radius','page_border_radius', 'showcase_border_radius') as $var) {
            if (isset($this->emailtemplate->data['config'][$var]) && !is_array($this->emailtemplate->data['config'][$var])) {
                $this->emailtemplate->data['config'][$var] = array_map('trim', explode(',', $this->emailtemplate->data['config'][$var]));
                foreach ($this->emailtemplate->data['config'][$var] as $val) {
                    if ((int)$val) {
                        $this->emailtemplate->data['config']['border_radius'] = true;
                    }
                }
            }
        }

        $this->emailtemplate->data['config']['has_section'] = false;

        foreach (array('body_section_bg_color', 'footer_section_bg_color', 'header_section_bg_color', 'showcase_section_bg_color') as $var) {
            if (!empty($this->emailtemplate->data['config'][$var])) {
                $this->emailtemplate->data['config']['has_section'] = true;
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
            }
        }

        $this->emailtemplate->data['store_theme_dir'] = $this->emailtemplate->data['store_theme'] . '/extension/module/emailtemplate/';

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

        $this->emailtemplate->data['login_url'] = $this->url->link('account/login');
        $this->emailtemplate->data['cart_url'] = $this->url->link('checkout/cart');
        $this->emailtemplate->data['contact_url'] = $this->url->link('information/contact');
        $this->emailtemplate->data['home_url'] = $this->url->link('common/home');
        $this->emailtemplate->data['privacy_url'] = $this->url->link('information/information', 'information_id=' . $this->config->get('config_account_id'));

        foreach (array('login_url', 'contact_url', 'cart_url', 'home_url', 'privacy_url') as $var) {
            if ($this->emailtemplate->data[$var] && substr($this->emailtemplate->data[$var], 0, 4) != 'http') {
                $this->emailtemplate->data[$var] = $this->emailtemplate->data['store_url'] . $this->emailtemplate->data[$var];
            }
        }

        if ($emailtemplate['wrapper_tpl']) {
            $this->emailtemplate->data['wrapper_tpl'] = $emailtemplate['wrapper_tpl'];
        } elseif ($emailtemplate_config['wrapper_tpl']) {
            $this->emailtemplate->data['wrapper_tpl'] = $emailtemplate_config['wrapper_tpl'];
        }

        if (!isset($this->emailtemplate->data['emailtemplate_log_id']) && ($this->emailtemplate->data['emailtemplate']['mail_queue'] == 1 || $this->emailtemplate->data['emailtemplate']['log'] == 1 || ($this->emailtemplate->data['emailtemplate']['log'] == 0 && $this->emailtemplate->data['config']['log']))) {
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

                foreach ($cart_products as $cart_product) {
                    if (strpos($this->emailtemplate->data['cart_subject_products'], $cart_product['name']) === false) {
                        $this->emailtemplate->data['cart_subject_products'] .= ($this->emailtemplate->data['cart_subject_products'] ? ', ' : '') . strip_tags(html_entity_decode($cart_product['name'], ENT_QUOTES, 'UTF-8'));
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

        if (!empty($this->emailtemplate->data['config']['showcase_setting']['price_tax'])) {
            $this->tax = $this->getTax();
        }

        if (!$this->currency) {
            $this->currency = new Cart\Currency($this->registry);
        }

        $products = array();
        $order_products = array();

        $store_id = $this->emailtemplate->data['store_id'];
        $customer_group_id = $this->emailtemplate->data['customer_group_id'];
        $language_id = $this->emailtemplate->data['language_id'];

        $customer_info = $this->emailtemplate->getCustomer();

        if ($this->config->get('module_emailtemplate_newsletter_showcase') && $customer_info && (!isset($customer_info['newsletter_preference']['showcase']) || $customer_info['newsletter_preference']['showcase'] == 0)) {
            return $showcase_products;
        }

        $product_condition = array('language_id' => $language_id, 'store_id' => $store_id, 'customer_group_id' => $customer_group_id, 'customer_id' => $customer_id);

        if (!empty($this->emailtemplate->data['config']['showcase_setting']['min_stock'])) {
            $product_condition['min_stock'] = $this->emailtemplate->data['config']['showcase_setting']['min_stock'];
        }

        if ($related && $customer_info && !empty($this->emailtemplate->data['order_id'])) {
            $this->load->model('account/order');

            $order_info = $this->model_account_order->getOrder($this->emailtemplate->data['order_id']);

            if ($order_info && ($order_info['customer_id'] == $customer_info['customer_id'] || $order_info['email'] == $customer_info['email'])) {
                $result = $this->model_account_order->getOrderProducts($this->emailtemplate->get('order_id'));

                if ($result) {
                    foreach ($result as $row) {
                        $order_products[$row['product_id']] = $row;
                    }

                    foreach ($result as $row) {
                        $result2 = $this->model_extension_module_emailtemplate_product->getProductRelated($row['product_id'], $product_condition);

                        if ($result2) {
                            foreach ($result2 as $row2) {
                                if (!isset($products[$row2['product_id']]) && !isset($order_products[$row2['product_id']])) {
                                    $products[$row2['product_id']] = $row2;
                                }
                            }
                        }
                    }
                }
            }
        }

        if (count($products) < $limit) {
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

        if (isset($this->session->data['currency'])) {
            $currency_code = $this->session->data['currency'];
        } else {
            $currency_code = $this->config->get('config_currency');
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

                if ((float)$product['price'] && (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price'))) {
                    if (!empty($this->emailtemplate->data['config']['showcase_setting']['price_tax'])) {
                        $price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->emailtemplate->data['store_tax']), $currency_code);
                    } else {
                        $price = $this->currency->format($product['price'], $currency_code);
                    }
                } else {
                    $price = false;
                }

                if ((float)$product['special']) {
                    if (!empty($this->emailtemplate->data['config']['showcase_setting']['price_tax'])) {
                        $special = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->emailtemplate->data['store_tax']), $currency_code);
                    } else {
                        $special = $this->currency->format($product['special'], $currency_code);
                    }
                } else {
                    $special = false;
                }

                $url = $this->url->link('product/product', 'product_id=' . $product['product_id']);

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

            return $showcase_products;
        }
    }

    protected function getCartProducts() {
        $this->load->model('catalog/product');
        $this->load->model('tool/image');

        $customer_info = $this->emailtemplate->getCustomer();

        if (!$customer_info) {
            return false;
        }

        $carts = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE (customer_id = '" . (int)$customer_info['customer_id'] . "' OR (customer_id = 0 AND session_id = '" . $this->db->escape($this->session->getId()) . "'))");

        if (!$carts->rows) {
            return false;
        }

        if ($customer_info && $customer_info['language_id']) {
            $language_id = $customer_info['language_id'];
        }
        if (!$language_id) {
            $language_id = $this->config->get('config_language_id');
        }

        if (isset($this->session->data['currency'])) {
            $currency_code = $this->session->data['currency'];
        } else {
            $currency_code = $this->config->get('config_currency');
        }

        $currency_value = $this->currency->getValue($currency_code);

        $products = array();

        foreach($carts->rows as $cart) {
            $product_info = $this->model_catalog_product->getProduct($cart['product_id']);

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
                    $option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$cart['product_id'] . "' AND od.language_id = '" . (int)$language_id . "'");

                    if ($option_query->num_rows) {
                        if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio') {
                            $option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$language_id . "'");

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
                                $option_value_query = $this->db->query("SELECT pov.option_value_id, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix, ovd.name FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (pov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$language_id . "'");

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
                    'url' => $this->url->link('product/product', 'product_id=' . $cart['product_id']),
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

        $this->load->model('checkout/order');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        $this->load->model('tool/upload');

        $order_info = $this->model_checkout_order->getOrder($this->emailtemplate->data['order_id']);

        if (!$order_info || ($order_info['customer_id'] && $this->emailtemplate->data['customer_id'] != $order_info['customer_id'])) {
            return false;
        }

        $products = array();
        $totals = array();
        $vouchers = array();

        $order_products = $this->model_checkout_order->getOrderProducts($order_info['order_id']);

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

                    $product_info = $this->model_catalog_product->getProduct($product['product_id']);

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
                        'url' => $this->url->link('product/product', 'product_id=' . $product['product_id']),
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

        $order_vouchers = $this->model_checkout_order->getOrderVouchers($order_info['order_id']);

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
            $order_totals = $this->model_checkout_order->getOrderTotals($order_info['order_id']);

            if ($order_totals) {
                foreach ($order_totals as $total) {
                    $totals[] = array(
                        'title' => $total['title'],
                        'text' => !empty($total['text']) ? $total['text'] : $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
                    );
                }
            }
        }

        return array('products' => $products, 'vouchers' => $vouchers, 'totals' => $totals);
    }

    /**
     * Get Template Log
     * @param array $id
     * @return array
     */
    public function getTemplateLog($data) {
        $query = "SELECT * FROM " . DB_PREFIX . "emailtemplate_logs";

        $where = array();

        if (is_array($data)) {
            if (isset($data['emailtemplate_log_id'])) {
                $where[] = "`emailtemplate_log_id` = '" . (int)$data['emailtemplate_log_id'] . "'";
            }

            if (isset($data['emailtemplate_log_enc'])) {
                $where[] = "`emailtemplate_log_enc` = '" . $this->db->escape($data['emailtemplate_log_enc']) . "'";
            }
        } else {
            $where[] = "`emailtemplate_log_id` = '" . (int)$data . "'";
        }

        $query .= " WHERE " . implode(" AND ", $where) . " LIMIT 1";

        $result = $this->_fetch($query);

        return $result->row;
    }

    /**
     * Create Log
     */
    public function createTemplateLog() {
        $query = "INSERT INTO " . DB_PREFIX . "emailtemplate_logs (`emailtemplate_log_id`, `emailtemplate_log_added`) VALUES (NULL, NOW())";

        $this->db->query($query);

        return $this->db->getLastId();
    }

    /**
     * Record Products In Showcase
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

    public function updateTemplateLog($emailtemplate_log_id, $data) {
        $query = "UPDATE " . DB_PREFIX . "emailtemplate_logs SET `emailtemplate_log_sent` = " . ($data['emailtemplate_log_is_sent'] ? 'NOW()' : 'NULL') . ", emailtemplate_log_enc = '" . $this->db->escape($data['emailtemplate_log_enc']) . "', emailtemplate_log_to = '" . $this->db->escape($data['emailtemplate_log_to']) . "', emailtemplate_log_from = '" . $this->db->escape($data['emailtemplate_log_from']) . "', emailtemplate_log_sender = '" . $this->db->escape($data['emailtemplate_log_sender']) . "', emailtemplate_log_heading = '" . $this->db->escape($data['emailtemplate_log_heading']) . "', emailtemplate_log_reply_to = '" . $this->db->escape($data['emailtemplate_log_reply_to']) . "', emailtemplate_log_cc = '" . $this->db->escape($data['emailtemplate_log_cc']) . "', emailtemplate_log_bcc = '" . $this->db->escape($data['emailtemplate_log_bcc']) . "', emailtemplate_log_subject = '" . $this->db->escape($data['emailtemplate_log_subject']) . "', emailtemplate_log_content = '" . $this->db->escape($data['emailtemplate_log_content']) . "', emailtemplate_log_is_sent = " . ($data['emailtemplate_log_is_sent'] ? (int)$data['emailtemplate_log_is_sent'] : 'NULL') . ", emailtemplate_id = '" . (int)$data['emailtemplate_id'] .  "', emailtemplate_key = '" . $this->db->escape($data['emailtemplate_key']) . "', store_id = '" . (int)$data['store_id'] . "', language_id = '" . (int)$data['language_id'] . "', customer_id = '" . (int)$data['customer_id'] . "' WHERE emailtemplate_log_id = " . (int)$emailtemplate_log_id;

        $this->db->query($query);
    }

    public function readTemplateLog($emailtemplate_log_id, $enc) {
        $query = "UPDATE " . DB_PREFIX . "emailtemplate_logs SET emailtemplate_log_read = NOW() WHERE emailtemplate_log_id = '" . (int)$emailtemplate_log_id . "' AND emailtemplate_log_enc = '" . $this->db->escape($enc) . "' AND emailtemplate_log_read IS NULL";

        $this->db->query($query);
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

        return $where;
    }

    /**
     * Get Templates
     *
     * @return array
     */
    public function getTemplates($data = array()) {
        if (!empty($data['language_id'])) {
            $query = "SELECT e.*, ed.* FROM " . DB_PREFIX . "emailtemplate e LEFT JOIN `" . DB_PREFIX . "emailtemplate_description` ed ON(ed.emailtemplate_id = e.emailtemplate_id)";
        } else {
            $query = "SELECT e.* FROM " . DB_PREFIX . "emailtemplate e";
        }

        $where = $this->_getTemplatesCondition($data);

        if (!empty($where)) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }

        $sort_data = array(
            'label' => 'e.`emailtemplate_label`',
            'key' => 'e.`emailtemplate_key`',
            'template' => 'e.`emailtemplate_template`',
            'modified' => 'e.`emailtemplate_modified`',
            'shortcodes' => 'e.`emailtemplate_shortcodes`',
            'status' => 'e.`emailtemplate_status`',
            'id' => 'e.`emailtemplate_id`',
            'store' => 'e.`store_id`',
            'customer' => 'e.`customer_group_id`',
            'language' => 'ed.`language_id`'
        );

        if (isset($data['sort']) && in_array($data['sort'], array_keys($sort_data))) {
            $query .= " ORDER BY " . $sort_data[$data['sort']];
        } else {
            $query .= " ORDER BY e.`emailtemplate_label`";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $query .= " DESC";
        } else {
            $query .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }
            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }
            $query .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $result = $this->_fetch($query);

        return $result->rows;
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

        if (!empty($data['language_id'])) {
            $where[] = "ed.`language_id` = '".(int)$data['language_id']."'";
        }

        if (!empty($data['customer_group_id'])) {
            $where[] = "e.`customer_group_id` = '".(int)$data['customer_group_id']."'";
        }

        if (!empty($data['emailtemplate_key'])) {
            $where[] = "e.`emailtemplate_key` = '".$this->db->escape($data['emailtemplate_key'])."'";
        }

        if (isset($data['emailtemplate_status']) && $data['emailtemplate_status'] != "") {
            $where[] = "e.`emailtemplate_status` = '".$this->db->escape($data['emailtemplate_status'])."'";
        } else {
            $where[] = "e.`emailtemplate_status` = 1";
        }

        if (isset($data['emailtemplate_id'])) {
            if (is_array($data['emailtemplate_id'])) {
                $ids = array();
                foreach($data['emailtemplate_id'] as $id) { $ids[] = (int)$id; }
                $where[] = "e.`emailtemplate_id` IN('".implode("', '", $ids)."')";
            } else {
                $where[] = "e.`emailtemplate_id` = '".(int)$data['emailtemplate_id']."'";
            }
        }

        return $where;
    }

    /**
     * Get Template
     * @param array $data
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

        $result = $this->_fetch($query);

        return ($limit == 1) ? $result->row : $result->rows;
    }

    /**
     * Get template shortcodes
     */
    public function getTemplateShortcodes($emailtemplate_id) {
        $query = "SELECT * FROM `" . DB_PREFIX . "emailtemplate_shortcode` WHERE `emailtemplate_id` = '" . (int)$emailtemplate_id . "' ORDER BY `emailtemplate_shortcode_id` ASC";

        $result = $this->_fetch($query);

        return $result->rows;
    }

    /**
     * Insert Template Short Codes
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

    /**
     * Get Email Template Config
     *
     * @param int||array $identifier
     * @param bool $outputFormatting
     * @return array
     */
    public function getConfig($data, $outputFormatting = false) {
        $where = array();

        if (is_array($data)) {
            if (isset($data['store_id'])) {
                $where[] = "`store_id` = '".(int)$data['store_id']."'";
            }

            if (isset($data['language_id'])) {
                $where[] = "(`language_id` = '".(int)$data['language_id']."' OR `language_id` = 0)";
            }
        } elseif (is_numeric($data)) {
            $where[] = "`emailtemplate_config_id` = '" . (int)$data . "'";
        } else {
            return false;
        }

        $query = "SELECT * FROM " . DB_PREFIX . "emailtemplate_config";

        if (!empty($where)) {
            $query .= " WHERE " . implode(" AND ", $where);
        }

        $query .= " ORDER BY `language_id` DESC LIMIT 1";

        $result = $this->_fetch($query);

        return $result->row;
    }

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

        if (isset($data['status']) && $data['status'] != "") {
            $where[] = "AND ec.`emailtemplate_config_status` = '".$this->db->escape($data['status'])."'";
        } else {
            $where[] = "AND ec.`emailtemplate_config_status` = 1";
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
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }
            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }
            $query .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $result = $this->_fetch($query);

        return $result->rows;
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

    private function _truncate($str, $length = 100, $breakWords = true, $append = '...') {
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
    private function _fetch($query) {
        $queryName = 'emailtemplate_sql_'.md5($query);

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
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }
    }
}
