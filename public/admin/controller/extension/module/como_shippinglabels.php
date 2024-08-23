<?php
class ControllerExtensionModuleComoShippingLabels extends Controller {
	private $error = array(); 
    private $setting_defaults    = array();
    private $route_extension     = 'extension/module/como_shippinglabels';
    private $route_mod           = 'extension/module/';
    private $setting_code        = 'module_como_shippinglabels';
    private $extension_code      = 'como_shippinglabels';
    private $module_name         = 'Como Shipping Labels';
    private $location_stylesheet = 'view/stylesheet/';
    private $location_javascript = 'view/javascript/';

    protected function setting_defaults_init() {
        $this->setting_defaults = array(
            'name'                   => '',
            'status'                 => 1,
            'sender'                 => '',
            'addressformat'          => '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{country}',
            'addressformat_usefromorder' => 0,
            'senderformat'           => '{company}' . "\n" . '{address}' . "\n" . '{city} {postcode}' . "\n" . '{country}' . "\n" . '-' . "\n" . 'Phone {telephone}' . "\n" . 'Email {email}' . "\n" . 'Url {url}',
            'orderformat'            => '{date_added} Ord. {order_id}',
            'senderaddress'          => '',
            'sendercity'             => '',
            'senderpostcode'         => '',
            'senderregion'           => '',
            'sendercountry'          => '',
            'sendertelephone'        => '',
            'showorderphone'         => 1,
            'showorderbarcode'       => 0,
            'showqrcodeaddress'      => 0,
            'showqrcodephone'        => 1,
            'showqrcodeorder'        => 1,
            'labelbox_unit'          => 'px',
            'labelbox_width'         => 365,
            'labelbox_height'        => 240,
            'labelbox_rotate'        => 0,
            'margin_left'            => 0,
            'margin_top'             => 0,
            'distance_x'             => 2,
            'distance_y'             => 2,
            'labelsperpage'          => 4,
            'logo_height'            => 60,
            'qrcode_size'            => 70,
            'orderbarcode_height'    => 12,
            'orderbarcode_width'     => 240,
            'orderbarcode_type'      => 'C128B',
            'button_text'            => htmlentities('<i class="fa fa-envelope"></i>'),
            'button_color'           => '',
            'button_backcolor'       => '',
            'button_quickprint_show' => 1,
            'button_quickprint_text' => htmlentities('<i class="fa fa-envelope"></i> <i class="fa fa-print"></i>'),
            'printsetup_printer'     => '',
            'printsetup_silentprint' => 0,
            'senderlogo'             => '',
            'stylesheet'             => 'como_shipping_labels.css',
            'label_template'         => 'como_shipping_labels.twig',
        );
    }

    public function install() {
		$this->load->language($this->route_extension);
        $this->setting_defaults_init();
        $this->setting_defaults['name'] = $this->language->get('text_baselabel');
        foreach ($this->setting_defaults as $key => $defaultValue) {
            $configkey = 'config_' . $key . '_shippinglabel';  // comparability with Opencart 2.3 module version
            if (!is_null($this->config->get($configkey))) {
                // get data from old format
                $this->setting_defaults[$key] = $this->config->get($configkey);
            }
        }

		$this->load->model('setting/module');
        $this->model_setting_module->addModule($this->extension_code, $this->setting_defaults);

        // Add sample labels
        $aJsonData = [
            //'{"name":"BASIC LABEL","status":"1","sender":"Compumotor Ltd.","senderformat":"{company}\r\n{address}\r\n{city} {postcode}\r\n{country}\r\n-\r\nPhone {telephone}\r\nEmail {email}\r\nUrl {url}","senderaddress":"Kleopatra str. 44","sendercity":"Sofia","senderpostcode":"1223","senderregion":"Sofia region","sendercountry":"Bulgariq","sendertelephone":"+359888123456","senderlogo":"catalog\/logo.gif","addressformat":"{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city} {postcode}\r\n{country}\r\n{zone}\r\n{country}, {payment_method} {weight} ","addressformat_usefromorder":"0","showorderphone":"1","orderformat":"{date_added} Ord. {order_id}","showorderbarcode":"0","orderbarcode_height":"12","orderbarcode_width":"240","orderbarcode_type":"C128B","labelbox_unit":"px","labelbox_width":"365","labelbox_height":"240","labelbox_rotate":"0","margin_left":"0","margin_top":"0","distance_x":"2","distance_y":"2","labelsperpage":"4","logo_height":"60","qrcode_size":"70","showqrcodeaddress":"1","showqrcodephone":"1","showqrcodeorder":"1","stylesheet":"como_shipping_labels.css","label_template":"como_shipping_labels.twig","button_text":"&lt;i class=&quot;fa fa-envelope&quot;&gt;&lt;\/i&gt; basic","button_color":"rgb(255, 255, 255)","button_backcolor":"rgb(153, 0, 255)","button_quickprint_show":"1","button_quickprint_text":"&lt;i class=&quot;fa fa-envelope&quot;&gt;&lt;\/i&gt; &lt;i class=&quot;fa fa-print&quot;&gt;&lt;\/i&gt;","printsetup_printer":"Bullzip PDF Printer","printsetup_silentprint":"1"}',
            '{"name":"SIMPLIFIED LABEL","status":"1","sender":"Compumotor Ltd.","senderformat":"{company}\r\n{address}\r\n{city} {postcode}\r\n{country}\r\n-\r\nPhone {telephone}\r\nEmail {email}\r\nUrl {url}","senderaddress":"Kliment Ohridski str.","sendercity":"Ruse","senderpostcode":"1223","senderregion":"Ruse region","sendercountry":"Bulgaria","sendertelephone":"+359888123456","senderlogo":"","addressformat":"{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city} {postcode}\r\n{country}","addressformat_usefromorder":"0","showorderphone":"1","orderformat":"{date_added} Ord. {order_id}","showorderbarcode":"0","orderbarcode_height":"12","orderbarcode_width":"240","orderbarcode_type":"C128B","labelbox_unit":"px","labelbox_width":"250","labelbox_height":"180","labelbox_rotate":"0","margin_left":"0","margin_top":"0","distance_x":"2","distance_y":"2","labelsperpage":"4","logo_height":"60","qrcode_size":"70","showqrcodeaddress":"0","showqrcodephone":"0","showqrcodeorder":"1","stylesheet":"como_shipping_labels.css","label_template":"como_shipping_labels.twig","button_text":"&lt;i class=&quot;fa fa-envelope&quot;&gt;&lt;\/i&gt; simple","button_color":"","button_backcolor":"rgb(0, 0, 255)","button_quickprint_show":"1","button_quickprint_text":"&lt;i class=&quot;fa fa-envelope&quot;&gt;&lt;\/i&gt; &lt;i class=&quot;fa fa-print&quot;&gt;&lt;\/i&gt;","printsetup_printer":"Bullzip PDF Printer","printsetup_silentprint":"1"}',
            '{"name":"BOX LABEL","status":"1","sender":"Era Nova Ltd.","senderformat":"{company}\r\n{address}\r\n{city} {postcode}\r\n{country}\r\n-\r\nPhone {telephone}\r\nEmail {email}\r\nUrl {url}","senderaddress":"Komando Alanes str.","sendercity":"Raid","senderpostcode":"1223","senderregion":"Comarten","sendercountry":"New Kingdom","sendertelephone":"+359888123456","senderlogo":"catalog\/logo.png","addressformat":"&lt;b&gt;{firstname} {lastname}&lt;\/b&gt;\r\n&lt;b&gt;{company}&lt;\/b&gt;\r\n{address_1}\r\n{address_2}\r\n{city} {postcode}\r\n{country}","addressformat_usefromorder":"0","showorderphone":"1","orderformat":"EM95383{order_id,4}MY","showorderbarcode":"1","orderbarcode_height":"40","orderbarcode_width":"240","orderbarcode_type":"C128B","labelbox_unit":"mm","labelbox_width":"105","labelbox_height":"148","labelbox_rotate":"0","margin_left":"0","margin_top":"0","distance_x":"2","distance_y":"2","labelsperpage":"4","logo_height":"60","qrcode_size":"90","showqrcodeaddress":"0","showqrcodephone":"0","showqrcodeorder":"1","stylesheet":"como_shipping_labels.css","label_template":"como_shipping_labels_box.twig","button_text":"&lt;i class=&quot;fa fa-envelope&quot;&gt;&lt;\/i&gt; box","button_color":"rgb(255, 0, 0)","button_backcolor":"","button_quickprint_show":"1","button_quickprint_text":"&lt;i class=&quot;fa fa-envelope&quot;&gt;&lt;\/i&gt; &lt;i class=&quot;fa fa-print&quot;&gt;&lt;\/i&gt;","printsetup_printer":"Bullzip PDF Printer","printsetup_silentprint":"1"}',
            '{"name":"STICKY LABEL 89x28","status":"1","sender":"Era Nova Ltd.","senderformat":"{company}\r\n{address}\r\n{city} {postcode}\r\n\r\n","senderaddress":"Komando Alanes str.","sendercity":"Raid","senderpostcode":"1223","senderregion":"Comarten","sendercountry":"New Kingdom","sendertelephone":"+359888123456","senderlogo":"catalog\/logo.png","addressformat":"{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{city} {postcode}","addressformat_usefromorder":"0","showorderphone":"0","orderformat":"Ord. {order_id}","showorderbarcode":"0","orderbarcode_height":"40","orderbarcode_width":"240","orderbarcode_type":"C128B","labelbox_unit":"mm","labelbox_width":"89","labelbox_height":"28","labelbox_rotate":"0","margin_left":"10","margin_top":"5","distance_x":"2","distance_y":"2","labelsperpage":"4","logo_height":"60","qrcode_size":"90","showqrcodeaddress":"0","showqrcodephone":"0","showqrcodeorder":"0","stylesheet":"como_shipping_labels.css","label_template":"como_shipping_labels_sticky89x28.twig","button_text":"&lt;i class=&quot;fa fa-envelope&quot;&gt;&lt;\/i&gt; 89x28","button_color":"rgb(255, 0, 0)","button_backcolor":"","button_quickprint_show":"1","button_quickprint_text":"&lt;i class=&quot;fa fa-envelope&quot;&gt;&lt;\/i&gt; &lt;i class=&quot;fa fa-print&quot;&gt;&lt;\/i&gt;","printsetup_printer":"Bullzip PDF Printer","printsetup_silentprint":"1"}',
            '{"name":"STICKY LABEL 28x89","status":"1","sender":"Era Nova Ltd.","senderformat":"{company}\r\n{address}\r\n{city} {postcode}\r\n\r\n","senderaddress":"Komando Alanes str.","sendercity":"Raid","senderpostcode":"1223","senderregion":"Comarten","sendercountry":"New Kingdom","sendertelephone":"+359888123456","senderlogo":"catalog\/logo.png","addressformat":"{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{city} {postcode}","addressformat_usefromorder":"0","showorderphone":"0","orderformat":"Ord. {order_id}","showorderbarcode":"0","orderbarcode_height":"40","orderbarcode_width":"240","orderbarcode_type":"C128B","labelbox_unit":"mm","labelbox_width":"28","labelbox_height":"89","labelbox_rotate":"0","margin_left":"10","margin_top":"5","distance_x":"2","distance_y":"2","labelsperpage":"4","logo_height":"60","qrcode_size":"90","showqrcodeaddress":"0","showqrcodephone":"0","showqrcodeorder":"0","stylesheet":"como_shipping_labels.css","label_template":"como_shipping_labels_sticky28x89.twig","button_text":"&lt;i class=&quot;fa fa-envelope&quot;&gt;&lt;\/i&gt; 28x89","button_color":"rgb(255, 0, 0)","button_backcolor":"","button_quickprint_show":"1","button_quickprint_text":"&lt;i class=&quot;fa fa-envelope&quot;&gt;&lt;\/i&gt; &lt;i class=&quot;fa fa-print&quot;&gt;&lt;\/i&gt;","printsetup_printer":"Bullzip PDF Printer","printsetup_silentprint":"1"}',
        ];
        $aData = [];
        foreach ($aJsonData as $jsonData) {
            $data = json_decode($jsonData, true);
            // add missing settings, if any
            foreach ($this->setting_defaults as $key => $defaultValue) {
                if (!isset($data[$key])) {
                    $data[$key] = $defaultValue;
                }
            }
            $this->model_setting_module->addModule($this->extension_code, $data);
        }

        $this->opencartClearCache();

        $this->setUsergroupPermissions($this->route_extension);
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

	public function index() {   
		$this->load->language($this->route_extension);

		$this->document->setTitle($this->language->get('heading_title_clean'));
        $this->document->addStyle($this->location_stylesheet . 'como_shippinglabels.css');

        // color picker
        $this->document->addStyle('https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.1/spectrum.min.css');
        $this->document->addScript('https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.1/spectrum.min.js');
        // jquery confirm
        $this->document->addScript('https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js');
        $this->document->addStyle('https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css');

		$this->load->model('setting/module');
		$this->load->model('tool/image');
		$this->load->model('localisation/language');
		
		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->request->post && $this->validate()) {
            // Module settings
			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule($this->extension_code, $this->request->post);
                $this->session->data['success'] = $this->language->get('text_success');
                $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
            } else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
                $this->session->data['success'] = $this->language->get('text_success');

                if (isset($this->request->get['copy_module']) && $this->request->get['copy_module']) {
                    // copy module
                    $this->request->post['name'] = $this->request->post['name'] . ' - COPY';
                    $this->model_setting_module->addModule($this->extension_code, $this->request->post);
                    $this->request->get['module_id'] = $this->db->getLastId();
                    $this->session->data['success'] = $this->session->data['success'] . '<br /><i class="fa fa-copy"></i>' . $this->request->post['name'];
                } elseif (isset($this->request->get['delete_module']) && $this->request->get['delete_module']) {
                    // delete module
                    $this->model_setting_module->deleteModule($this->request->get['module_id']);
                    $this->request->get['module_id'] = 0;
                    $this->session->data['success'] = $this->session->data['success'] . '<br /><i class="fa fa-trash-o"></i> ' . $this->request->post['name'];
                    $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
                }

                $this->response->redirect($this->url->link($this->route_extension, 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true));
			}
		}

        $data['module_id'] = isset($this->request->get['module_id']) ? $this->request->get['module_id'] : 0;

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['user_token'] = $this->session->data['user_token'];
		$data['no_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
       
 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
   		);

        $modules = $this->model_setting_module->getModulesByCode($this->extension_code);
        foreach ($modules as $module) {
			$data['breadcrumbs'][] = array(
				'text' => ($module['module_id'] == $data['module_id'] ? '<span class="module_name">' . strtoupper($module['name']) . '</span>' : $module['name']),
				'href' => $this->url->link($this->route_extension, 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module['module_id'], true)
			);
		}
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('button_add'),
            'href' => $this->url->link($this->route_extension, 'user_token=' . $this->session->data['user_token'], true)
        );
		
		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link($this->route_extension, 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link($this->route_extension, 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
			$data['action_copy'] = $this->url->link($this->route_extension, 'user_token=' . $this->session->data['user_token'] . '&copy_module=1&module_id=' . $this->request->get['module_id'], true);
			$data['action_delete'] = $this->url->link($this->route_extension, 'user_token=' . $this->session->data['user_token'] . '&delete_module=1&module_id=' . $this->request->get['module_id'], true);
		}
		
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}

        $this->setting_defaults_init();
        foreach ($this->setting_defaults as $key => $defaultValue) {
            if (isset($this->request->post[$key])) {
                $data[$key] = $this->request->post[$key];
            } elseif (isset($module_info[$key])) {
                $data[$key] = $module_info[$key];
            } else {
                $data[$key] = $defaultValue;
            }
        }

		if (isset($this->request->post['senderlogo']) && is_file(DIR_IMAGE . $this->request->post['senderlogo'])) {
			$data['senderlogo_thumb'] = $this->model_tool_image->resize($this->request->post['senderlogo'], 100, 100);
		} elseif (isset($module_info['senderlogo']) && $module_info['senderlogo'] && is_file(DIR_IMAGE . $module_info['senderlogo'])) {
			$data['senderlogo_thumb'] = $this->model_tool_image->resize($module_info['senderlogo'], 100, 100);
		} else {
			$data['senderlogo_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
        
        // stylesheets in default theme
        $data['stylesheets_default'] = $this->getLabelStylesheets();
        $data['stylesheets_preffix'] = $this->getStylesheetsPrefix();
		
        // predefined and custom templates in default theme
        $data['label_templates'] = $this->getLabelTemplates();
        $data['templates_preffix'] = $this->getTemplatePrefix();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view($this->route_extension, $data));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', $this->route_extension)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 128)) {
			$this->error['name'] = $this->language->get('error_name');
		}
		
		return !$this->error;
	}

	public function comoShippinglabel($module_id = null, $quickprint = false) {
        if (isset($this->request->get['module_id'])) {
			$module_id = $this->request->get['module_id'];
		}
        if ($module_id) {
            $this->load->model('setting/module');
            $module_info = $this->model_setting_module->getModule($module_id);
        }
        if (isset($this->request->get['quickprint'])) {
			$quickprint = $this->request->get['quickprint'];
		}

        if (isset($module_info['sender'])) {
            $this->load->language($this->route_mod . 'como_shipping_labels');

            $data['title'] = $this->language->get('text_title');
            $data['quickprint'] = $quickprint;

            if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
                $data['base'] = HTTPS_SERVER;
            } else {
                $data['base'] = HTTP_SERVER;
            }

            $data['direction'] = $this->language->get('direction');
            $data['lang'] = $this->language->get('code');

            $this->load->model('tool/image');

            if ($module_info['senderlogo']) {
                //$data['senderlogo'] = $this->model_tool_image->resize($module_info['senderlogo'], 100, 100);
                $data['senderlogo'] = ($this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG) . 'image/' . $module_info['senderlogo'];
            } else {
                $data['senderlogo'] = '';
            }

            $data['addressformat_usefromorder'] = isset($module_info['addressformat_usefromorder']) ? $module_info['addressformat_usefromorder'] : 0;
            $data['showorderphone'] = is_null($module_info['showorderphone']) ? 1 : $module_info['showorderphone'];
            $data['showorderbarcode'] = $module_info['showorderbarcode'];
            $data['showqrcodeaddress'] = $module_info['showqrcodeaddress'];
            $data['showqrcodephone'] = $module_info['showqrcodephone'];
            $data['showqrcodeorder'] = isset($module_info['showqrcodeorder']) ? $module_info['showqrcodeorder'] : 1;
            $data['labelbox_unit'] = isset($module_info['labelbox_unit']) && $module_info['labelbox_unit'] ? $module_info['labelbox_unit'] : 'px';
            $data['labelbox_width'] = $module_info['labelbox_width'] ? $module_info['labelbox_width'] : 365;
            $data['labelbox_height'] = $module_info['labelbox_height'] ? $module_info['labelbox_height'] : 240;
            $data['labelbox_rotate'] = isset($module_info['labelbox_rotate']) && $module_info['labelbox_rotate'] ? $module_info['labelbox_rotate'] : 0;
            $data['margin_left'] = isset($module_info['margin_left']) ? $module_info['margin_left'] : 0;
            $data['margin_top'] = isset($module_info['margin_top']) ? $module_info['margin_top'] : 0;
            $data['distance_x'] = $module_info['distance_x'] ? $module_info['distance_x'] : 2;
            $data['distance_y'] = $module_info['distance_y'] ? $module_info['distance_y'] : 2;
            $data['labelsperpage'] = is_null($module_info['labelsperpage']) ? 4 : $module_info['labelsperpage'];
            $data['logo_height'] = $module_info['logo_height'] ? $module_info['logo_height'] : 60;
            $data['qrcode_size'] = $module_info['qrcode_size'] ? $module_info['qrcode_size'] : 70;
            $data['orderbarcode_height'] = $module_info['orderbarcode_height'] ? $module_info['orderbarcode_height'] : 120;
            $data['orderbarcode_width'] = $module_info['orderbarcode_width'] ? $module_info['orderbarcode_width'] : 240;
            $data['orderbarcode_type'] = isset($module_info['orderbarcode_type']) && $module_info['orderbarcode_type'] ? $module_info['orderbarcode_type'] : 'C128B';
            $data['button_text'] = isset($module_info['button_text']) && $module_info['button_text'] ? $module_info['button_text'] : '<i class="fa fa-envelope"></i>';
            $data['button_quickprint_text'] = isset($module_info['button_quickprint_text']) && $module_info['button_quickprint_text'] ? $module_info['button_quickprint_text'] : '<i class="fa fa-envelope"></i> <i class="fa fa-print"></i>';
            $data['printsetup_printer'] = isset($module_info['printsetup_printer']) && $module_info['printsetup_printer'] ? $module_info['printsetup_printer'] : '';
            $data['printsetup_silentprint'] = isset($module_info['printsetup_silentprint']) && $module_info['printsetup_silentprint'] ? $module_info['printsetup_silentprint'] : 0;
            $data['button_color'] = isset($module_info['button_color']) ? $module_info['button_color'] : '';
            $data['button_backcolor'] = isset($module_info['button_backcolor']) ? $module_info['button_backcolor'] : '';
            $data['button_quickprint_show'] = isset($module_info['button_quickprint_show']) ? $module_info['button_quickprint_show'] : 0;

            $data['text_to'] = $this->language->get('text_to');
            $data['text_recipient'] = $this->language->get('text_recipient');
            $data['text_sender'] = $this->language->get('text_sender');
            $data['text_qr_code_address'] = $this->language->get('text_qr_code_address');
            $data['text_qr_code_phone'] = $this->language->get('text_qr_code_phone');

            $this->load->model('sale/order');

            $this->load->model('setting/setting');

            $orders = array();
            if (isset($this->request->post['selected'])) {
                $orders = $this->request->post['selected'];
            } elseif (isset($this->request->get['orderlist'])) {
                $orders = explode('-', trim($this->request->get['orderlist'], '-'));
            } elseif (isset($this->request->get['order_id'])) {
                $orders[] = $this->request->get['order_id'];
            } elseif (isset($this->request->get['checked'])) {
                $orders = array_map('urldecode', json_decode($this->request->get['checked']));
            }

            // BARCODE
            // $bcW (int) Width of a single bar element in pixels.
            $bcW = 1;
            $url_params = '&bcType=' . $data['orderbarcode_type'] . '&bcW=' . $bcW . '&bcH={height}&bcText={data}';
            $data['url_barcode'] = $this->url->link($this->route_extension . '/comoBarcode', 'user_token=' . $this->session->data['user_token'] . $url_params, true);
            // QR CODE
            $url_params = '&bcType=QRCODE&bcW=10&bcH=10&bcText={data}';
            $data['url_qrcode'] = $this->url->link($this->route_extension . '/comoQRcode', 'user_token=' . $this->session->data['user_token'] . $url_params, true);

            // for order weight calculation
            $weight_class_id = $this->config->get('config_weight_class_id');
            $weight_unit = $this->weight->getUnit($weight_class_id);

            if ($orders) {
                foreach ($orders as $order_id) {
                    $order_info = $this->model_sale_order->getOrder($order_id);

                    if ($order_info) {
                        // add additional info
                        $order_info['weight'] = $this->getOrderWeight($order_info['order_id'], $weight_class_id);
                        $order_info['weight_formatted'] = $this->getOrderWeight($order_info['order_id'], $weight_class_id) . ' ' . $weight_unit;
                        $order_info['tracking_number'] = $this->getTracking_number($order_info['order_id'], $order_info['shipping_code']);
                        // Order contend
                        $content = '';
                        $products = $this->model_sale_order->getOrderProducts($order_id);
                        foreach ($products as $product) {
                            $content .= ($content ? ', ' : '') . $product['name'];
                        }
                        $order_info['content'] = $content;
                        $order_info['products'] = $products;

                        $store_info = $this->model_setting_setting->getSetting('config', $order_info['store_id']);

                        $date_added = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));

                        if ($store_info) {
                            $store_address = $store_info['config_address'];
                            $store_email = $store_info['config_email'];
                            $store_telephone = $store_info['config_telephone'];
                            $store_fax = $store_info['config_fax'];
                        } else {
                            $store_address = $this->config->get('config_address');
                            $store_email = $this->config->get('config_email');
                            $store_telephone = $this->config->get('config_telephone');
                            $store_fax = $this->config->get('config_fax');
                        }

                        $shipping_address_format = '';
                        if (!isset($module_info['addressformat_usefromorder'])) {
                           $module_info['addressformat_usefromorder'] = 0; 
                        }
                        if ($order_info['shipping_address_format'] && $module_info['addressformat_usefromorder']) {
                            $shipping_address_format = $order_info['shipping_address_format'];
                        } elseif ($order_info['payment_address_format'] && $module_info['addressformat_usefromorder']) {
                            $shipping_address_format = $order_info['payment_address_format'];
                        } elseif ($module_info['addressformat']) {
                            $shipping_address_format = html_entity_decode($module_info['addressformat']);
                        }
                        if (!$shipping_address_format) {
                            $shipping_address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
                        }

                        if ($module_info['senderformat']) {
                            $sender_address_format = html_entity_decode($module_info['senderformat']);
                        } else {
                            $sender_address_format = '{company}' . "\n" . '{address}' . "\n" . '{city} {postcode}' . "\n" . '{country}' . "\n" . '-' . "\n" . 'Phone {telephone}' . "\n" . 'Email {email}' . "\n" . 'Url {url}';
                        }

                        if ($module_info['senderaddress']) {
                            $sender_address = $module_info['senderaddress'];
                        } else {
                            $sender_address = '';
                        }

                        // FORMATING replaces - BOLD
                        $find = array(
                            '{firstname}',
                            '{lastname}',
                            '{company}',
                            '{city}'
                        );
                        $replace = array(
                            '<span class="adrname">{firstname}</span>',
                            '<span class="adrname">{lastname}</span>',
                            '<span class="adrcompany">{company}</span>',
                            '<span class="adrcity">{city}</span>'
                        );
                        $shipping_address_format_clean = $shipping_address_format;
                        $shipping_address_format = str_replace($find, $replace, $shipping_address_format);
                        $sender_address_format_clean = $sender_address_format;
                        $sender_address_format = str_replace($find, $replace, $sender_address_format);

                        // FORMATING replaces - FA ICONS
                        $find = array(
                            'Phone ',
                            'Email ',
                            'Url '
                        );
                        $replace = array(
                            '<i class="fa fa-phone"></i> ',
                            '<i class="fa fa-at"></i> ',
                            '<i class="fa fa-globe"></i> '
                        );
                        $sender_address_format = str_replace($find, $replace, $sender_address_format);

                        // RECEIVER replaces
                        // 1. - shipping address is used, if empty - then payment address
                        $useShippingAddr = $order_info['shipping_address_1'] || $order_info['shipping_address_2'] || $order_info['shipping_company'] || $order_info['shipping_city'];
                        $shipping_address = ''; // return from func
                        $shipping_address_clean = ''; // return from func
                        $shipping_address_parts = array(); // return from func
                        $this->comoShippinglabel_adrRepl($shipping_address, $shipping_address_clean, $shipping_address_parts, $useShippingAddr, $order_info, $shipping_address_format, $shipping_address_format_clean);
                        // 2. - shipping address is used
                        $shipping_address_s = ''; // return from func
                        $shipping_address_s_clean = ''; // return from func
                        $shipping_address_s_parts  = array(); // return from func
                        $this->comoShippinglabel_adrRepl($shipping_address_s, $shipping_address_s_clean, $shipping_address_s_parts, true, $order_info, $shipping_address_format, $shipping_address_format_clean);
                        // 3. - payment address is used
                        $shipping_address_p = ''; // return from func
                        $shipping_address_p_clean = ''; // return from func
                        $shipping_address_p_parts  = array(); // return from func
                        $this->comoShippinglabel_adrRepl($shipping_address_p, $shipping_address_p_clean, $shipping_address_p_parts, false, $order_info, $shipping_address_format, $shipping_address_format_clean);

                        // SENDER replaces
                        $find = array(
                            '{company}',
                            '{address}',
                            '{city}',
                            '{postcode}',
                            '{region}',
                            '{country}',
                            '{telephone}',
                            '{email}',
                            '{url}',
                            '{order_id}',
                            '{date_added}',
                            '{invoice_no}',
                            '{invoice_prefix}',
                            '{comment}',
                            '{weight}',
                            '{weight_formatted}',
                            '{content}',
                            '{payment_method}',
                            '{payment_code}',
                            '{shipping_method}',
                            '{shipping_code}',
                            '{tracking_number}',
                        );
                        $replace = array(
                            'company'   => $module_info['sender'] ? $module_info['sender'] : $this->config->get('config_owner'),
                            'address'   => $module_info['senderaddress'] ? $module_info['senderaddress'] : $this->config->get('config_address'),
                            'city'      => $module_info['sendercity'],
                            'postcode'  => $module_info['senderpostcode'],
                            'region'    => $module_info['senderregion'],
                            'country'   => $module_info['sendercountry'],
                            'telephone' => $module_info['sendertelephone'] ? $module_info['sendertelephone'] : $store_telephone,
                            'email'     => $store_email,
                            'url'       => rtrim($order_info['store_url'], '/'),
                            'order_id'   => $order_id,
                            'date_added' => $date_added,
                            'invoice_no' => $order_info['invoice_no'],
                            'invoice_prefix' => $order_info['invoice_prefix'],
                            'comment' => $order_info['comment'],
                            'weight' => $order_info['weight'],
                            'weight_formatted' => $order_info['weight_formatted'],
                            'content' => $order_info['content'],
                            'payment_method' => $order_info['payment_method'],
                            'payment_code' => $order_info['payment_code'],
                            'shipping_method' => $order_info['shipping_method'],
                            'shipping_code' => $order_info['shipping_code'],
                            'tracking_number' => $order_info['tracking_number'],
                        );
                        $sender_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $sender_address_format))));
                        $sender_address_clean = trim(str_replace($find, $replace, $sender_address_format_clean));

                        if ($module_info['orderformat']) {
                            $order_barcode_format = $module_info['orderformat'];
                        } else {
                            $order_barcode_format = '{date_added} Ord. {order_id}';
                        }

                        // BARCODE replaces
                        $find = array(
                            '{date_added}',
                            '{order_id}',
                            '{order_id,3}',
                            '{order_id,4}',
                            '{order_id,5}',
                            '{order_id,6}',
                            '{invoice_no}',
                            '{invoice_prefix}',
                            '{customer_id}',
                            '{username}'
                        );
                        $replace = array(
                            'date_added'     => $date_added,
                            'order_id'       => $order_id,
                            'order_id,3'       => str_pad($order_id, 3, '0', STR_PAD_LEFT),
                            'order_id,4'       => str_pad($order_id, 4, '0', STR_PAD_LEFT),
                            'order_id,5'       => str_pad($order_id, 5, '0', STR_PAD_LEFT),
                            'order_id,6'       => str_pad($order_id, 6, '0', STR_PAD_LEFT),
                            'invoice_no'     => $order_info['invoice_no'],
                            'invoice_prefix' => $order_info['invoice_prefix'],
                            'customer_id'    => $order_info['customer_id'],
                            'username'       => $this->user->getUserName()
                        );
                        $order_barcode = trim(str_replace($find, $replace, $order_barcode_format));

                        $data['orders'][] = array(
                            'order_id'	               => $order_id,
                            'order_barcode'            => $order_barcode, 
                            'date_added'               => $date_added,
                            'invoice_no'               => $order_info['invoice_no'],
                            'invoice_prefix'           => $order_info['invoice_prefix'],
                            'store_name'               => $order_info['store_name'],
                            'store_url'                => rtrim($order_info['store_url'], '/'),
                            'store_address'            => nl2br($store_address),
                            'store_email'              => $store_email,
                            'store_telephone'          => $store_telephone,
                            'store_fax'                => $store_fax,
                            'email'                    => $order_info['email'],
                            'telephone'                => $order_info['telephone'],
                            'shipping_method'          => $order_info['shipping_method'],
                            'shipping_code'            => $order_info['shipping_code'],
                            'tracking_number'          => $order_info['tracking_number'],
                            'payment_method'           => $order_info['payment_method'],
                            'payment_code'             => $order_info['payment_code'],
                            'total'                    => $order_info['total'],
                            'total_formatted'          => $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value']),
                            'comment'                  => $order_info['comment'],
                            'weight'                   => $order_info['weight'],
                            'weight_formatted'         => $order_info['weight_formatted'],
                            'content'                  => $order_info['content'],
                            'products'                 => $order_info['products'],
                            'shipping_address'         => $shipping_address,
                            'shipping_address_clean'   => str_replace(array("\r\n", "\r", "\n"), '%0D%0A', $shipping_address_clean),
                            'shipping_address_parts'   => $shipping_address_parts,
                            'shipping_address_s'       => $shipping_address_s,
                            'shipping_address_s_clean' => str_replace(array("\r\n", "\r", "\n"), '%0D%0A', $shipping_address_s_clean),
                            'shipping_address_s_parts' => $shipping_address_s_parts,
                            'shipping_address_p'       => $shipping_address_p,
                            'shipping_address_p_clean' => str_replace(array("\r\n", "\r", "\n"), '%0D%0A', $shipping_address_p_clean),
                            'shipping_address_p_parts' => $shipping_address_p_parts,
                            'sender_address'           => $sender_address,
                            'sender_address_clean'     => str_replace("<br />","%0D%0A", $sender_address_clean)
                        );
                    }
                }
            }
            //print_r($data);

            if (!isset($module_info['stylesheet']) || !file_exists($this->location_stylesheet . $module_info['stylesheet'])) {
                if (file_exists($this->location_stylesheet . 'como_shipping_labels_custom.css')) {
                    $module_info['stylesheet'] = 'como_shipping_labels_custom.css';
                } else {
                    $module_info['stylesheet'] = 'como_shipping_labels.css';
                }
            }
            $data['como_shipping_labels_css'] = $this->location_stylesheet . $module_info['stylesheet'];

            if (!isset($module_info['label_template']) || !file_exists('view/template/' . $this->route_mod . $module_info['label_template'])) {
                if (file_exists('view/template/' . $this->route_mod . 'como_shipping_labels_custom.twig')) {
                    $module_info['label_template'] = 'como_shipping_labels_custom.twig';
                } else {
                    $module_info['label_template'] = 'como_shipping_labels.twig';
                }
            }
            $template_justfilename = pathinfo($module_info['label_template'], PATHINFO_FILENAME);
            $template = $this->route_mod . $template_justfilename;
            
            $data['include_print'] = $this->load->view($this->route_mod . 'como_shipping_labels_print', $data);

            $this->response->setOutput($this->load->view($template, $data));
        }
	}

    public function getTracking_number($order_id, $shipping_code) {
        $tracking_number = '';

        if ($shipping_code) {
            $arr = explode('.', $shipping_code);
            $shipping_code = isset($arr[0]) ? trim($arr[0]) : '';
        }

        if ($shipping_code == 'econt') {
            // Econt express
            $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "econt_loading WHERE `order_id` = '" . (int)$order_id . "'");
            if (isset($query->row['loading_num']) && $query->row['loading_num']) {
                $tracking_number = $query->row['loading_num'];
            }
        } elseif ($shipping_code == 'speedy') {
            // Speedy express
            $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "speedy_order WHERE `order_id` = '" . (int)$order_id . "'");
            if (isset($query->row['bol_id']) && $query->row['bol_id']) {
                $tracking_number = $query->row['bol_id'];
            }
        }

        if (!$tracking_number) {
            // Compatibility with Como Delivery Tracking code
            // https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=
            $db_check = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "como_tracking_codes'");
            if($db_check->num_rows) {
                $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "como_tracking_codes WHERE `order_id` = '" . (int)$order_id . "' ORDER BY tracking_code_id DESC");
                if (isset($query->row['tracking_code']) && $query->row['tracking_code']) {
                    $tracking_number = $query->row['tracking_code'];
                }
            }
        }

        if (!$tracking_number) {
            // Compatibility with Shipment / Courier details Tracker
            // https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=19340
            // https://www.huntbee.com/extensions-modules/opencart-extensions/shipment-courier-details-tracker
            $db_check = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "hb_shipment_order_info'");
            if($db_check->num_rows) {
                $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "hb_shipment_order_info WHERE `order_id` = '" . (int)$order_id . "'");
                if (isset($query->row['code']) && $query->row['code']) {
                    $tracking_number = $query->row['code'];
                }
            }
        }

        if (!$tracking_number) {
            // Check if column for tracking code is added from any module
            $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "order WHERE `order_id` = '" . (int)$order_id . "'");
            if (isset($query->row['tracking_no']) && $query->row['tracking_no']) {
                $tracking_number = $query->row['tracking_no'];
            }
            if (isset($query->row['tracking_nom']) && $query->row['tracking_nom']) {
                $tracking_number = $query->row['tracking_nom'];
            }
            if (isset($query->row['tracking_code']) && $query->row['tracking_code']) {
                $tracking_number = $query->row['tracking_code'];
            }
        }
        
        return trim($tracking_number);
    }

    public function getOrderWeight($order_id, $weight_class_id) {
        $weight = 0;

        $query = $this->db->query("SELECT p.weight, op.quantity, p.weight_class_id FROM " . DB_PREFIX . "order_product op LEFT JOIN " . DB_PREFIX . "product p ON (op.product_id = p.product_id) WHERE op.order_id = '" . (int)$order_id . "'");	  				

        foreach ($query->rows as $row) {
            $weight += $this->weight->convert($row['weight'] * $row['quantity'], $row['weight_class_id'], $weight_class_id);
        }

        return $weight;
    } 

    // RECEIVER replaces
	public function comoShippinglabel_adrRepl(&$address, &$address_clean, &$address_parts, $useShippingAddr, $order_info, $address_format, $address_format_clean) {

        $date_added = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));

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
            '{country}',
            '{telephone}',
            '{email}',
            '{total}',
            '{total_formatted}',
            '{order_id}',
            '{date_added}',
            '{invoice_no}',
            '{invoice_prefix}',
            '{comment}',
            '{weight}',
            '{weight_formatted}',
            '{content}',
            '{payment_method}',
            '{payment_code}',
            '{shipping_method}',
            '{shipping_code}',
            '{tracking_number}',
            'Phone ',
        );
        $address_parts['firstname'] = $useShippingAddr ? $order_info['shipping_firstname'] : $order_info['payment_firstname'];
        $address_parts['lastname'] = $useShippingAddr ? $order_info['shipping_lastname'] : $order_info['payment_lastname'];
        $address_parts['company'] = $useShippingAddr ? $order_info['shipping_company'] : $order_info['payment_company'];
        $address_parts['address_1'] = $useShippingAddr ? $order_info['shipping_address_1'] : $order_info['payment_address_1'];
        $address_parts['address_2'] = $useShippingAddr ? $order_info['shipping_address_2'] : $order_info['payment_address_2'];
        $address_parts['city'] = $useShippingAddr ? $order_info['shipping_city'] : $order_info['payment_city'];
        $address_parts['postcode'] = $useShippingAddr ? $order_info['shipping_postcode'] : $order_info['payment_postcode'];
        $address_parts['zone'] = $useShippingAddr ? $order_info['shipping_zone'] : $order_info['payment_zone'];
        $address_parts['zone_code'] = $useShippingAddr ? $order_info['shipping_zone_code'] : $order_info['payment_zone_code'];
        $address_parts['country'] = $useShippingAddr ? $order_info['shipping_country'] : $order_info['payment_country'];
        $replace = array(
            'firstname' => $address_parts['firstname'],
            'lastname'  => $address_parts['lastname'],
            'company'   => $address_parts['company'],
            'address_1' => $address_parts['address_1'],
            'address_2' => $address_parts['address_2'],
            'city'      => $address_parts['city'],
            'postcode'  => $address_parts['postcode'],
            'zone'      => $address_parts['zone'],
            'zone_code' => $address_parts['zone_code'],
            'country'   => $address_parts['country'],
            'telephone' => $order_info['telephone'],
            'email' => $order_info['email'],
            'total'     => $order_info['total'],
            'total_formatted' => $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value']),
            'order_id'   => $order_info['order_id'],
            'date_added' => $date_added,
            'invoice_no' => $order_info['invoice_no'],
            'invoice_prefix' => $order_info['invoice_prefix'],
            'comment' => $order_info['comment'],
            'weight' => $order_info['weight'],
            'weight_formatted' => $order_info['weight_formatted'],
            'content' => $order_info['content'],
            'payment_method' => $order_info['payment_method'],
            'payment_code' => $order_info['payment_code'],
            'shipping_method' => $order_info['shipping_method'],
            'shipping_code' => $order_info['shipping_code'],
            'tracking_number' => $order_info['tracking_number'],
            '<i class="fa fa-phone"></i> ',
        );
        //print_r($find);
        //print_r($replace);
        
        $address = str_replace($find, $replace, $address_format);
        $address_clean = str_replace($find, $replace, $address_format_clean);

        // clean some empties
        $find = array(
            '<span class="adrname"></span>',
            '<span class="adrname"></span>',
            '<span class="adrcompany"></span>',
            '<span class="adrcity"></span>',
            '<b></b>',
            '<i></i>'
        );
        $replace = '';
        $address = trim(str_replace($find, $replace, $address));
        $address_clean = trim(str_replace($find, $replace, $address_clean));
        
        $address = preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', $address);
        $address = str_replace(array("\r\n", "\r", "\n"), '<br />', $address);
        
        return;
	}

	public function comoBarcode() {
		if (isset($this->request->get['bcText'])) {
			$bcText = rawurldecode($this->request->get['bcText']);
        } else {
			$bcText = '';
		}
		if (isset($this->request->get['bcType'])) {
			$bcType = $this->request->get['bcType'];
        } else {
			$bcType = 'C128B';
		}
		if (isset($this->request->get['bcW'])) {
			$bcW = $this->request->get['bcW'];
        } else {
			$bcW = 1;
		}
		if (isset($this->request->get['bcH'])) {
			$bcH = $this->request->get['bcH'];
        } else {
			$bcH = 20;
		}

        // include 1D barcode class
        require_once(DIR_SYSTEM . 'library/como_tcpdf/tcpdf_barcodes_1d.php');

        // set the barcode content and type
        $barcodeobj = new TCPDFBarcode($bcText, $bcType);

        // output the barcode as PNG image
        $barcodeobj->getBarcodePNG($bcW, $bcH, array(0,0,0));
    }
    
	public function comoQRcode() {
		if (isset($this->request->get['bcText'])) {
			$bcText = rawurldecode($this->request->get['bcText']);
        } else {
			$bcText = '';
		}
		if (isset($this->request->get['bcType'])) {
			$bcType = $this->request->get['bcType'];
        } else {
			$bcType = 'QRCODE';
		}
		if (isset($this->request->get['bcW'])) {
			$bcW = $this->request->get['bcW'];
        } else {
			$bcW = 10;
		}
		if (isset($this->request->get['bcH'])) {
			$bcH = $this->request->get['bcH'];
        } else {
			$bcH = 10;
		}

        // include 2D barcode class
        require_once(DIR_SYSTEM . 'library/como_tcpdf/tcpdf_barcodes_2d.php');

        // set the barcode content and type
        $barcodeobj = new TCPDF2DBarcode($bcText, $bcType);

        // output the barcode as PNG image
        $barcodeobj->getBarcodePNG($bcW, $bcH, array(0,0,0));
    }

    // get all active labels (modules) and return data for building calling buttons
	public function getShippingLabelsButtons($potOrderParam) {
        $this->load->language($this->route_mod . 'como_shipping_labels');
		$this->load->model('setting/module');
        $modules = $this->model_setting_module->getModulesByCode($this->extension_code);
        foreach ($modules as $key => $module) {
            //$modules[$key]['settings'] = $this->model_setting_module->getModule($module['module_id']);
            $modules[$key]['setting'] = json_decode($module['setting'], true);
            if ($modules[$key]['setting']['status']) {
                $modules[$key]['href'] = $this->url->link($this->route_extension . '/comoShippinglabel', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . (int)$module['module_id'] . ($potOrderParam ? '&order_id=' . (int)$this->request->get['order_id'] : ''), true);
                $modules[$key]['href_quickprint'] = $this->url->link($this->route_extension . '/comoShippinglabel', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . (int)$module['module_id'] . '&quickprint=1' . ($potOrderParam ? '&order_id=' . (int)$this->request->get['order_id'] : ''), true);
                $modules[$key]['title'] = $this->language->get('button_shippinglabel') . ' - ' . $module['name'];
                $modules[$key]['title_quickprint'] = $modules[$key]['title'];
                $modules[$key]['setting']['button_text'] = isset($modules[$key]['setting']['button_text']) && $modules[$key]['setting']['button_text'] ? html_entity_decode($modules[$key]['setting']['button_text']) : '<i class="fa fa-envelope"></i>';
                $modules[$key]['setting']['button_quickprint_text'] = isset($modules[$key]['setting']['button_quickprint_text']) && $modules[$key]['setting']['button_quickprint_text'] ? html_entity_decode($modules[$key]['setting']['button_quickprint_text']) : '<i class="fa fa-envelope"></i> <i class="fa fa-print"></i>';
            } else {
                unset($modules[$key]);
            }
		}

        return $modules;
    }

    protected function opencartClearCache() {
        $directories = glob(DIR_CACHE . '*', GLOB_ONLYDIR);
        if ($directories) {
            foreach ($directories as $directory) {
                $this->recursiveDelDir($directory);
            }
        }
	}
    protected function recursiveDelDir($directory) {
        if (is_dir($directory)) {
            foreach (glob($directory . '/*') as $file) {
                if (is_dir($file)) { 
                    $this->recursiveDelDir($file);
                } else {
                    unlink($file);
                }
            }
            @rmdir($directory);
        }
    }

    public function ajaxTemplateCopy() {
        return $this->ajaxTemplate('copy');
	}

    public function ajaxTemplateRename() {
        return $this->ajaxTemplate('rename');
	}

    public function ajaxTemplateDelete() {
        return $this->ajaxTemplate('delete');
	}

    public function ajaxTemplateGet() {
        return $this->ajaxTemplate('get');
	}

    public function ajaxTemplateSave() {
        return $this->ajaxTemplate('save');
	}

    public function ajaxTemplate($action) {
        $this->load->language($this->route_extension);

		$json = array();

		if (!$this->user->hasPermission('modify', $this->route_extension)) {
			$json['error'] = $this->language->get('error_permission');
		} elseif (isset($this->request->post['template']) && $this->request->post['template']) {
            $template = $this->request->post['template'];
            $path_parts = pathinfo($template);
            if ($path_parts['extension'] == 'css') {
                $templatedir = $this->getStylesheetsDir();
            } else {
                $templatedir = $this->getTemplateDir();
            }
            if (file_exists($templatedir . $template)) {
                if ($action == 'copy') {
                    $templateNew = $path_parts['filename'] . '_copy.' . $path_parts['extension'];
                    if (copy($templatedir . $template, $templatedir . $templateNew)) {
                        $json['templateNew'] = $templateNew;
                    } else {
                        $json['error'] = $this->language->get('error_filecopy') . $template;
                    }
                } elseif ($action == 'rename') {
                    if (isset($this->request->post['nameNew']) && $this->request->post['nameNew']) {
                        $nameNew = $this->request->post['nameNew'];
                        if (rename($templatedir . $template, $templatedir . $nameNew)) {
                            $json['templateNew'] = $nameNew;
                        } else {
                            $json['error'] = $this->language->get('error_filecopy') . $template;
                        }
                    } else {
                        $json['error'] = $this->language->get('error_data');
                    }
                } elseif ($action == 'delete') {
                    if (!unlink($templatedir . $template)) {
                        $json['error'] = $this->language->get('error_filedelete') . $template;
                    }
                } elseif ($action == 'get') {
                    $json['content'] = file_get_contents($templatedir . $template);
                } elseif ($action == 'save') {
                    if (isset($this->request->post['content'])) {
                        $content = html_entity_decode($this->request->post['content']);
                        $markUTF8 = "\xEF\xBB\xBF";
                        if (strpos($content, $markUTF8) <> 0) {
                            // ensure UTF-8 code page
                            $content = $markUTF8 . $content;
                        }
                        $result = file_put_contents($templatedir . $template, $content);
                        if ($result === false) {
                            $json['error'] = $this->language->get('error_filewrite') . $template;
                        } else {
                            $json['success'] = $this->language->get('text_savesuccess');
                        }
                    } else {
                        $json['error'] = $this->language->get('error_data');
                    }
                }
                if ($path_parts['extension'] == 'css') {
                    $json['templates'] = $this->getLabelStylesheets();
                } else {
                    $json['templates'] = $this->getLabelTemplates();
                }
            } else {
                $json['error'] = $this->language->get('error_notexist') . $template;
            }
		} else {
			$json['error'] = $this->language->get('error_data');
        }

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }

	protected function getTemplateDir() {
        return DIR_APPLICATION . 'view/template/' . $this->route_mod;
	}

	protected function getTemplatePrefix() {
        return 'como_shipping_labels';
	}

	protected function getLabelTemplates() {
        $files = array();
        foreach (glob($this->getTemplateDir() . $this->getTemplatePrefix() . '*.twig') as $filename) {
            $files[] = basename($filename);
        }

        return $files; 
	}

	protected function getLabelStylesheets() {
        $files = array();
        foreach (glob($this->getStylesheetsDir() . $this->getStylesheetsPrefix() . '*.css') as $filename) {
            $files[] = basename($filename);
        }

        return $files; 
	}

	protected function getStylesheetsDir() {
        return DIR_APPLICATION . $this->location_stylesheet;
	}

	protected function getStylesheetsPrefix() {
        return 'como_shipping_labels';
	}
}
