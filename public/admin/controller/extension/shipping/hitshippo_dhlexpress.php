<?php
class ControllerExtensionShippinghitshippodhlexpress extends Controller {
	private $error = array();
	public function install() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hitshippo_dhl_details_new` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `order_id` text NOT NULL,
		  `tracking_num` text NOT NULL,
		  `shipping_label` text COLLATE utf8_bin NOT NULL,
		  `invoice` text COLLATE utf8_bin NOT NULL,
		  `return_label` text COLLATE utf8_bin  NULL,
		  `return_invoice` text COLLATE utf8_bin  NULL,
		  `one` text COLLATE utf8_bin  NULL,
		  `two` text COLLATE utf8_bin  NULL,
		  `three` text COLLATE utf8_bin  NULL,
		  PRIMARY KEY (`id`)
		)");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hitshippo_dhl_pickup_details` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `order_id` text NOT NULL,
		  `status` text NOT NULL,
		  `confirm_no` text COLLATE utf8_bin NOT NULL,
		  `ready_time` text COLLATE utf8_bin NOT NULL,
		  `pickup_date` text COLLATE utf8_bin  NULL,
		  `one` text COLLATE utf8_bin  NULL,
		  `two` text COLLATE utf8_bin  NULL,
		  `three` text COLLATE utf8_bin  NULL,
		  PRIMARY KEY (`id`)
		)");
	}
	public function index() {
		$this->install();
		$this->load->language('extension/shipping/hitshippo_dhlexpress');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && isset($_POST['btn_licence_activation'])) {

			if (!$this->request->post['shipping_hitshippo_dhlexpress_licence_licence'] && empty($this->request->post['shipping_hitshippo_dhlexpress_licence_licence'])) {
			return 'Enter Licence Key';
			}
			if (!$this->request->post['shipping_hitshippo_dhlexpress_licence_email'] && empty($this->request->post['shipping_hitshippo_dhlexpress_licence_email'])) {
				return 'Enter Purchased Email Address';
			}

				$licence_key = trim($this->request->post['shipping_hitshippo_dhlexpress_licence_licence']);
				$licence_email = trim($this->request->post['shipping_hitshippo_dhlexpress_licence_email']);
				$licenc_check_url = "https://hittechmarket.com/?edd_action=check_license&item_id=2288&license=".$licence_key;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_URL, $licenc_check_url);
				$result = curl_exec($ch);
				curl_close($ch);
				$obj = json_decode($result);
				
				if(isset($obj->success) && $obj->success == true)
				{
					if(isset($obj->license) && ($obj->license == 'inactive' || $obj->license == 'valid'))
					{
						if(isset($obj->customer_email) && $obj->customer_email == $licence_email)
						{
							if(isset($obj->activations_left) && $obj->activations_left != '0')
							{
								$licenc_activate_url = "https://hittechmarket.com/?edd_action=activate_license&item_id=2288&license=".$licence_key;
								$ch = curl_init();
								curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($ch, CURLOPT_URL, $licenc_activate_url);
								$result = curl_exec($ch);
								curl_close($ch);
								$obj = json_decode($result);
								
								if(isset($obj->expires))
								{
									$this->request->post['shipping_hitshippo_dhlexpress_licence_expires'] = $obj->expires;
									$this->request->post['shipping_hitshippo_dhlexpress_licence_status_licence'] = 'Activated';
								}

								}else
							{
								$this->error['warning'] = 'Licence Is Already Activated In another one Site. Report HIT TECH.';
							}

						}else
						{
							$this->error['warning'] = 'Purchased Email Address is Invalid.';
						}

					}else
					{
						$this->error['warning'] = 'The Licence is not valid for this Product.';
					}
				}
				else{
					$this->error['warning'] = 'invalid Licence Key';
				}
		$this->model_setting_setting->editSetting('shipping_hitshippo_dhlexpress_licence', $this->request->post);
			
		}else if (($this->request->server['REQUEST_METHOD'] == 'POST') && isset($_POST['btn_renew_activation'])) {

			if (!$this->request->post['shipping_hitshippo_dhlexpress_renew_licence'] && empty($this->request->post['shipping_hitshippo_dhlexpress_renew_licence'])) {
				$this->error['warning'] = 'Enter Licence Key.';
			return 'Enter Licence Key';
			}
			if (!$this->request->post['shipping_hitshippo_dhlexpress_renew_email'] && empty($this->request->post['shipping_hitshippo_dhlexpress_renew_email'])) {
				$this->error['warning'] = 'Enter Purchased Email Address.';
				return 'Enter Purchased Email Address';
			}

				$licence_key = trim($this->request->post['shipping_hitshippo_dhlexpress_renew_licence']);
				$licence_email = trim($this->request->post['shipping_hitshippo_dhlexpress_renew_email']);
				$licenc_check_url = "https://hittechmarket.com/?edd_action=check_license&item_id=2288&license=".$licence_key;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_URL, $licenc_check_url);
				$result = curl_exec($ch);
				curl_close($ch);
				$obj = json_decode($result);
				
				if(isset($obj->success) && $obj->success == true)
				{
					if(isset($obj->license) && ($obj->license == 'inactive' || $obj->license == 'valid'))
					{
						if(isset($obj->customer_email) && $obj->customer_email == $licence_email)
						{
							
							$this->request->post['shipping_hitshippo_dhlexpress_renew_expires'] = $obj->expires;
						}else
						{
							$this->error['warning'] = 'Purchased Email Address is Invalid.';
						}

					}else
					{
						$this->error['warning'] = 'The Licence is not valid for this Product.';
					}
				}
				else{
					$this->error['warning'] = 'invalid Licence Key';
				}
		$this->model_setting_setting->editSetting('shipping_hitshippo_dhlexpress_renew', $this->request->post);
			
		}else if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('shipping_hitshippo_dhlexpress', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true));
		}



		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['key'])) {
			$data['error_key'] = $this->error['key'];
		} else {
			$data['error_key'] = '';
		}

		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}

		if (isset($this->error['account'])) {
			$data['error_account'] = $this->error['account'];
		} else {
			$data['error_account'] = '';
		}
		
		if (isset($this->error['postcode'])) {
			$data['error_postcode'] = $this->error['postcode'];
		} else {
			$data['error_postcode'] = '';
		}

		if (isset($this->error['dimension'])) {
			$data['error_dimension'] = $this->error['dimension'];
		} else {
			$data['error_dimension'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/shipping/hitshippo_dhlexpress', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/shipping/hitshippo_dhlexpress', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true);

		
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_test'])) {
			$data['shipping_hitshippo_dhlexpress_test'] = $this->request->post['shipping_hitshippo_dhlexpress_test'];
		} else {
			$data['shipping_hitshippo_dhlexpress_test'] = $this->config->get('shipping_hitshippo_dhlexpress_test');
		}
		
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_key'])) {
			$data['shipping_hitshippo_dhlexpress_key'] = $this->request->post['shipping_hitshippo_dhlexpress_key'];
		} else {
			$data['shipping_hitshippo_dhlexpress_key'] = $this->config->get('shipping_hitshippo_dhlexpress_key');
		}

		if (isset($this->request->post['shipping_hitshippo_dhlexpress_password'])) {
			$data['shipping_hitshippo_dhlexpress_password'] = $this->request->post['shipping_hitshippo_dhlexpress_password'];
		} else {
			$data['shipping_hitshippo_dhlexpress_password'] = $this->config->get('shipping_hitshippo_dhlexpress_password');
		}

		if (isset($this->request->post['shipping_hitshippo_dhlexpress_account'])) {
			$data['shipping_hitshippo_dhlexpress_account'] = $this->request->post['shipping_hitshippo_dhlexpress_account'];
		} else {
			$data['shipping_hitshippo_dhlexpress_account'] = $this->config->get('shipping_hitshippo_dhlexpress_account');
		}
		
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_status'])) {
			$data['shipping_hitshippo_dhlexpress_status'] = $this->request->post['shipping_hitshippo_dhlexpress_status'];
		} else {
			$data['shipping_hitshippo_dhlexpress_status'] = $this->config->get('shipping_hitshippo_dhlexpress_status');
		}
		
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_sort_order'])) {
			$data['shipping_hitshippo_dhlexpress_sort_order'] = $this->request->post['shipping_hitshippo_dhlexpress_sort_order'];
		} else {
			$data['shipping_hitshippo_dhlexpress_sort_order'] = $this->config->get('shipping_hitshippo_dhlexpress_sort_order');
		}

		if (isset($this->request->post['shipping_hitshippo_dhlexpress_shipper_name'])) {
			$data['shipping_hitshippo_dhlexpress_shipper_name'] = $this->request->post['shipping_hitshippo_dhlexpress_shipper_name'];
		} else {
			$data['shipping_hitshippo_dhlexpress_shipper_name'] = $this->config->get('shipping_hitshippo_dhlexpress_shipper_name');
		}
		
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_company_name'])) {
			$data['shipping_hitshippo_dhlexpress_company_name'] = $this->request->post['shipping_hitshippo_dhlexpress_company_name'];
		} else {
			$data['shipping_hitshippo_dhlexpress_company_name'] = $this->config->get('shipping_hitshippo_dhlexpress_company_name');
		}
		
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_phone_num'])) {
			$data['shipping_hitshippo_dhlexpress_phone_num'] = $this->request->post['shipping_hitshippo_dhlexpress_phone_num'];
		} else {
			$data['shipping_hitshippo_dhlexpress_phone_num'] = $this->config->get('shipping_hitshippo_dhlexpress_phone_num');
		}
		
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_email_addr'])) {
			$data['shipping_hitshippo_dhlexpress_email_addr'] = $this->request->post['shipping_hitshippo_dhlexpress_email_addr'];
		} else {
			$data['shipping_hitshippo_dhlexpress_email_addr'] = $this->config->get('shipping_hitshippo_dhlexpress_email_addr');
		}
		
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_address1'])) {
			$data['shipping_hitshippo_dhlexpress_address1'] = $this->request->post['shipping_hitshippo_dhlexpress_address1'];
		} else {
			$data['shipping_hitshippo_dhlexpress_address1'] = $this->config->get('shipping_hitshippo_dhlexpress_address1');
		}
		
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_address2'])) {
			$data['shipping_hitshippo_dhlexpress_address2'] = $this->request->post['shipping_hitshippo_dhlexpress_address2'];
		} else {
			$data['shipping_hitshippo_dhlexpress_address2'] = $this->config->get('shipping_hitshippo_dhlexpress_address2');
		}
		
		
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_city'])) {
			$data['shipping_hitshippo_dhlexpress_city'] = $this->request->post['shipping_hitshippo_dhlexpress_city'];
		} else {
			$data['shipping_hitshippo_dhlexpress_city'] = $this->config->get('shipping_hitshippo_dhlexpress_city');
		}
		
		
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_state'])) {
			$data['shipping_hitshippo_dhlexpress_state'] = $this->request->post['shipping_hitshippo_dhlexpress_state'];
		} else {
			$data['shipping_hitshippo_dhlexpress_state'] = $this->config->get('shipping_hitshippo_dhlexpress_state');
		}
		
		
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_country_code'])) {
			$data['shipping_hitshippo_dhlexpress_country_code'] = $this->request->post['shipping_hitshippo_dhlexpress_country_code'];
		} else {
			$data['shipping_hitshippo_dhlexpress_country_code'] = $this->config->get('shipping_hitshippo_dhlexpress_country_code');
		}
		$data['countrylist'] = array(
			'AF' => 'Afghanistan',
			'AX' => 'Aland Islands',
			'AL' => 'Albania',
			'DZ' => 'Algeria',
			'AS' => 'American Samoa',
			'AD' => 'Andorra',
			'AO' => 'Angola',
			'AI' => 'Anguilla',
			'AQ' => 'Antarctica',
			'AG' => 'Antigua and Barbuda',
			'AR' => 'Argentina',
			'AM' => 'Armenia',
			'AW' => 'Aruba',
			'AU' => 'Australia',
			'AT' => 'Austria',
			'AZ' => 'Azerbaijan',
			'BS' => 'Bahamas',
			'BH' => 'Bahrain',
			'BD' => 'Bangladesh',
			'BB' => 'Barbados',
			'BY' => 'Belarus',
			'BE' => 'Belgium',
			'BZ' => 'Belize',
			'BJ' => 'Benin',
			'BM' => 'Bermuda',
			'BT' => 'Bhutan',
			'BO' => 'Bolivia',
			'BQ' => 'Bonaire, Saint Eustatius and Saba',
			'BA' => 'Bosnia and Herzegovina',
			'BW' => 'Botswana',
			'BV' => 'Bouvet Island',
			'BR' => 'Brazil',
			'IO' => 'British Indian Ocean Territory',
			'VG' => 'British Virgin Islands',
			'BN' => 'Brunei',
			'BG' => 'Bulgaria',
			'BF' => 'Burkina Faso',
			'BI' => 'Burundi',
			'KH' => 'Cambodia',
			'CM' => 'Cameroon',
			'CA' => 'Canada',
			'CV' => 'Cape Verde',
			'KY' => 'Cayman Islands',
			'CF' => 'Central African Republic',
			'TD' => 'Chad',
			'CL' => 'Chile',
			'CN' => 'China',
			'CX' => 'Christmas Island',
			'CC' => 'Cocos Islands',
			'CO' => 'Colombia',
			'KM' => 'Comoros',
			'CK' => 'Cook Islands',
			'CR' => 'Costa Rica',
			'HR' => 'Croatia',
			'CU' => 'Cuba',
			'CW' => 'Curacao',
			'CY' => 'Cyprus',
			'CZ' => 'Czech Republic',
			'CD' => 'Democratic Republic of the Congo',
			'DK' => 'Denmark',
			'DJ' => 'Djibouti',
			'DM' => 'Dominica',
			'DO' => 'Dominican Republic',
			'TL' => 'East Timor',
			'EC' => 'Ecuador',
			'EG' => 'Egypt',
			'SV' => 'El Salvador',
			'GQ' => 'Equatorial Guinea',
			'ER' => 'Eritrea',
			'EE' => 'Estonia',
			'ET' => 'Ethiopia',
			'FK' => 'Falkland Islands',
			'FO' => 'Faroe Islands',
			'FJ' => 'Fiji',
			'FI' => 'Finland',
			'FR' => 'France',
			'GF' => 'French Guiana',
			'PF' => 'French Polynesia',
			'TF' => 'French Southern Territories',
			'GA' => 'Gabon',
			'GM' => 'Gambia',
			'GE' => 'Georgia',
			'DE' => 'Germany',
			'GH' => 'Ghana',
			'GI' => 'Gibraltar',
			'GR' => 'Greece',
			'GL' => 'Greenland',
			'GD' => 'Grenada',
			'GP' => 'Guadeloupe',
			'GU' => 'Guam',
			'GT' => 'Guatemala',
			'GG' => 'Guernsey',
			'GN' => 'Guinea',
			'GW' => 'Guinea-Bissau',
			'GY' => 'Guyana',
			'HT' => 'Haiti',
			'HM' => 'Heard Island and McDonald Islands',
			'HN' => 'Honduras',
			'HK' => 'Hong Kong',
			'HU' => 'Hungary',
			'IS' => 'Iceland',
			'IN' => 'India',
			'ID' => 'Indonesia',
			'IR' => 'Iran',
			'IQ' => 'Iraq',
			'IE' => 'Ireland',
			'IM' => 'Isle of Man',
			'IL' => 'Israel',
			'IT' => 'Italy',
			'CI' => 'Ivory Coast',
			'JM' => 'Jamaica',
			'JP' => 'Japan',
			'JE' => 'Jersey',
			'JO' => 'Jordan',
			'KZ' => 'Kazakhstan',
			'KE' => 'Kenya',
			'KI' => 'Kiribati',
			'XK' => 'Kosovo',
			'KW' => 'Kuwait',
			'KG' => 'Kyrgyzstan',
			'LA' => 'Laos',
			'LV' => 'Latvia',
			'LB' => 'Lebanon',
			'LS' => 'Lesotho',
			'LR' => 'Liberia',
			'LY' => 'Libya',
			'LI' => 'Liechtenstein',
			'LT' => 'Lithuania',
			'LU' => 'Luxembourg',
			'MO' => 'Macao',
			'MK' => 'Macedonia',
			'MG' => 'Madagascar',
			'MW' => 'Malawi',
			'MY' => 'Malaysia',
			'MV' => 'Maldives',
			'ML' => 'Mali',
			'MT' => 'Malta',
			'MH' => 'Marshall Islands',
			'MQ' => 'Martinique',
			'MR' => 'Mauritania',
			'MU' => 'Mauritius',
			'YT' => 'Mayotte',
			'MX' => 'Mexico',
			'FM' => 'Micronesia',
			'MD' => 'Moldova',
			'MC' => 'Monaco',
			'MN' => 'Mongolia',
			'ME' => 'Montenegro',
			'MS' => 'Montserrat',
			'MA' => 'Morocco',
			'MZ' => 'Mozambique',
			'MM' => 'Myanmar',
			'NA' => 'Namibia',
			'NR' => 'Nauru',
			'NP' => 'Nepal',
			'NL' => 'Netherlands',
			'NC' => 'New Caledonia',
			'NZ' => 'New Zealand',
			'NI' => 'Nicaragua',
			'NE' => 'Niger',
			'NG' => 'Nigeria',
			'NU' => 'Niue',
			'NF' => 'Norfolk Island',
			'KP' => 'North Korea',
			'MP' => 'Northern Mariana Islands',
			'NO' => 'Norway',
			'OM' => 'Oman',
			'PK' => 'Pakistan',
			'PW' => 'Palau',
			'PS' => 'Palestinian Territory',
			'PA' => 'Panama',
			'PG' => 'Papua New Guinea',
			'PY' => 'Paraguay',
			'PE' => 'Peru',
			'PH' => 'Philippines',
			'PN' => 'Pitcairn',
			'PL' => 'Poland',
			'PT' => 'Portugal',
			'PR' => 'Puerto Rico',
			'QA' => 'Qatar',
			'CG' => 'Republic of the Congo',
			'RE' => 'Reunion',
			'RO' => 'Romania',
			'RU' => 'Russia',
			'RW' => 'Rwanda',
			'BL' => 'Saint Barthelemy',
			'SH' => 'Saint Helena',
			'KN' => 'Saint Kitts and Nevis',
			'LC' => 'Saint Lucia',
			'MF' => 'Saint Martin',
			'PM' => 'Saint Pierre and Miquelon',
			'VC' => 'Saint Vincent and the Grenadines',
			'WS' => 'Samoa',
			'SM' => 'San Marino',
			'ST' => 'Sao Tome and Principe',
			'SA' => 'Saudi Arabia',
			'SN' => 'Senegal',
			'RS' => 'Serbia',
			'SC' => 'Seychelles',
			'SL' => 'Sierra Leone',
			'SG' => 'Singapore',
			'SX' => 'Sint Maarten',
			'SK' => 'Slovakia',
			'SI' => 'Slovenia',
			'SB' => 'Solomon Islands',
			'SO' => 'Somalia',
			'ZA' => 'South Africa',
			'GS' => 'South Georgia and the South Sandwich Islands',
			'KR' => 'South Korea',
			'SS' => 'South Sudan',
			'ES' => 'Spain',
			'LK' => 'Sri Lanka',
			'SD' => 'Sudan',
			'SR' => 'Suriname',
			'SJ' => 'Svalbard and Jan Mayen',
			'SZ' => 'Swaziland',
			'SE' => 'Sweden',
			'CH' => 'Switzerland',
			'SY' => 'Syria',
			'TW' => 'Taiwan',
			'TJ' => 'Tajikistan',
			'TZ' => 'Tanzania',
			'TH' => 'Thailand',
			'TG' => 'Togo',
			'TK' => 'Tokelau',
			'TO' => 'Tonga',
			'TT' => 'Trinidad and Tobago',
			'TN' => 'Tunisia',
			'TR' => 'Turkey',
			'TM' => 'Turkmenistan',
			'TC' => 'Turks and Caicos Islands',
			'TV' => 'Tuvalu',
			'VI' => 'U.S. Virgin Islands',
			'UG' => 'Uganda',
			'UA' => 'Ukraine',
			'AE' => 'United Arab Emirates',
			'GB' => 'United Kingdom',
			'US' => 'United States',
			'UM' => 'United States Minor Outlying Islands',
			'UY' => 'Uruguay',
			'UZ' => 'Uzbekistan',
			'VU' => 'Vanuatu',
			'VA' => 'Vatican',
			'VE' => 'Venezuela',
			'VN' => 'Vietnam',
			'WF' => 'Wallis and Futuna',
			'EH' => 'Western Sahara',
			'YE' => 'Yemen',
			'ZM' => 'Zambia',
			'ZW' => 'Zimbabwe',
		);

		$data['pickup_loc_type'] = array('B' =>'B (Business)','R' =>'R (Residence)','C' =>'C (Business/Residence)');
		$data['pickup_del_type'] = array('DD' => 'DD (DoorToDoor)','DA' => 'DA (DoorToAirport)','DC' => 'DC (DoorToDoor non-complaint)');
		$data['pickup_type'] = array('S' => 'S-SameDayPickup','A' => 'A-AdvancedPickup');

		
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_postcode'])) {
			$data['shipping_hitshippo_dhlexpress_postcode'] = $this->request->post['shipping_hitshippo_dhlexpress_postcode'];
		} else {
			$data['shipping_hitshippo_dhlexpress_postcode'] = $this->config->get('shipping_hitshippo_dhlexpress_postcode');
		}
		
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_realtime_rates'])) {
			$data['shipping_hitshippo_dhlexpress_realtime_rates'] = $this->request->post['shipping_hitshippo_dhlexpress_realtime_rates'];
		} else {
			$data['shipping_hitshippo_dhlexpress_realtime_rates'] = $this->config->get('shipping_hitshippo_dhlexpress_realtime_rates');
		}
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_insurance'])) {
			$data['shipping_hitshippo_dhlexpress_insurance'] = $this->request->post['shipping_hitshippo_dhlexpress_insurance'];
		} else {
			$data['shipping_hitshippo_dhlexpress_insurance'] = $this->config->get('shipping_hitshippo_dhlexpress_insurance');
		}

		if (isset($this->request->post['shipping_hitshippo_dhlexpress_translation'])) {
			$data['shipping_hitshippo_dhlexpress_translation'] = $this->request->post['shipping_hitshippo_dhlexpress_translation'];
		} else {
			$data['shipping_hitshippo_dhlexpress_translation'] = $this->config->get('shipping_hitshippo_dhlexpress_translation');
		}

		if (isset($this->request->post['shipping_hitshippo_dhlexpress_translation_key'])) {
			$data['shipping_hitshippo_dhlexpress_translation_key'] = $this->request->post['shipping_hitshippo_dhlexpress_translation_key'];
		} else {
			$data['shipping_hitshippo_dhlexpress_translation_key'] = $this->config->get('shipping_hitshippo_dhlexpress_translation_key');
		}
		
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_display_time'])) {
			$data['shipping_hitshippo_dhlexpress_display_time'] = $this->request->post['shipping_hitshippo_dhlexpress_display_time'];
		} else {
			$data['shipping_hitshippo_dhlexpress_display_time'] = $this->config->get('shipping_hitshippo_dhlexpress_display_time');
		}
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_front_end_logs'])) {
			$data['shipping_hitshippo_dhlexpress_front_end_logs'] = $this->request->post['shipping_hitshippo_dhlexpress_front_end_logs'];
		} else {
			$data['shipping_hitshippo_dhlexpress_front_end_logs'] = $this->config->get('shipping_hitshippo_dhlexpress_front_end_logs');
		}
			
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_rate_type'])) {
			$data['shipping_hitshippo_dhlexpress_rate_type'] = $this->request->post['shipping_hitshippo_dhlexpress_rate_type'];
		} else {
			$data['shipping_hitshippo_dhlexpress_rate_type'] = $this->config->get('shipping_hitshippo_dhlexpress_rate_type');
		}

		if (isset($this->request->post['shipping_hitshippo_dhlexpress_pay_con'])) {
			$data['shipping_hitshippo_dhlexpress_pay_con'] = $this->request->post['shipping_hitshippo_dhlexpress_pay_con'];
		} else {
			$data['shipping_hitshippo_dhlexpress_pay_con'] = $this->config->get('shipping_hitshippo_dhlexpress_pay_con');
		}

		if (isset($this->request->post['shipping_hitshippo_dhlexpress_cus_pay_con'])) {
			$data['shipping_hitshippo_dhlexpress_cus_pay_con'] = $this->request->post['shipping_hitshippo_dhlexpress_cus_pay_con'];
		} else {
			$data['shipping_hitshippo_dhlexpress_cus_pay_con'] = $this->config->get('shipping_hitshippo_dhlexpress_cus_pay_con');
		}
		
		
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_service'])) {
			$data['shipping_hitshippo_dhlexpress_service'] = $this->request->post['shipping_hitshippo_dhlexpress_service'];
		} elseif ($this->config->has('shipping_hitshippo_dhlexpress_service')) {
			$data['shipping_hitshippo_dhlexpress_service'] = $this->config->get('shipping_hitshippo_dhlexpress_service');
		} else {
			$data['shipping_hitshippo_dhlexpress_service'] = array();
		}

		$data['services'] = array();

		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_1'),
			'value' => '1'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_2'),
			'value' => '2'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_3'),
			'value' => '3'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_4'),
			'value' => '4'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_5'),
			'value' => '5'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_7'),
			'value' => '7'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_8'),
			'value' => '8'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_9'),
			'value' => '9'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_B'),
			'value' => 'B'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_C'),
			'value' => 'C'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_D'),
			'value' => 'D'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_E'),
			'value' => 'E'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_F'),
			'value' => 'F'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_G'),
			'value' => 'G'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_H'),
			'value' => 'H'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_I'),
			'value' => 'I'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_J'),
			'value' => 'J'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_K'),
			'value' => 'K'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_L'),
			'value' => 'L'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_M'),
			'value' => 'M'
		);

		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_N'),
			'value' => 'N'
		);
		
		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_O'),
			'value' => 'O'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_P'),
			'value' => 'P'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_Q'),
			'value' => 'Q'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_R'),
			'value' => 'R'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_S'),
			'value' => 'S'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_T'),
			'value' => 'T'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_U'),
			'value' => 'U'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_V'),
			'value' => 'V'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_W'),
			'value' => 'W'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_X'),
			'value' => 'X'
		);
		$data['services'][] = array(
			'text'  => $this->language->get('text_dhl_Y'),
			'value' => 'y'
		);
		
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_weight'])) {
			$data['shipping_hitshippo_dhlexpress_weight'] = $this->request->post['shipping_hitshippo_dhlexpress_weight'];
		} else {
			$data['shipping_hitshippo_dhlexpress_weight'] = $this->config->get('shipping_hitshippo_dhlexpress_weight');
		}
		
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_packing_type'])) {
			$data['shipping_hitshippo_dhlexpress_packing_type'] = $this->request->post['shipping_hitshippo_dhlexpress_packing_type'];
		} else {
			$data['shipping_hitshippo_dhlexpress_packing_type'] = $this->config->get('shipping_hitshippo_dhlexpress_packing_type');
		}
		
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_per_item'])) {
			$data['shipping_hitshippo_dhlexpress_per_item'] = $this->request->post['shipping_hitshippo_dhlexpress_per_item'];
		} else {
			$data['shipping_hitshippo_dhlexpress_per_item'] = $this->config->get('shipping_hitshippo_dhlexpress_per_item');
		}
		
		
			
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_wight_b'])) {
			$data['shipping_hitshippo_dhlexpress_wight_b'] = $this->request->post['shipping_hitshippo_dhlexpress_wight_b'];
		} else {
			$data['shipping_hitshippo_dhlexpress_wight_b'] = $this->config->get('shipping_hitshippo_dhlexpress_wight_b');
		}
		
				
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_weight_c'])) {
			$data['shipping_hitshippo_dhlexpress_weight_c'] = $this->request->post['shipping_hitshippo_dhlexpress_weight_c'];
		} else {
			$data['shipping_hitshippo_dhlexpress_weight_c'] = $this->config->get('shipping_hitshippo_dhlexpress_weight_c');
		}
		
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_int_key'])) {
			$data['shipping_hitshippo_dhlexpress_int_key'] = $this->request->post['shipping_hitshippo_dhlexpress_int_key'];
		} else {
			$data['shipping_hitshippo_dhlexpress_int_key'] = $this->config->get('shipping_hitshippo_dhlexpress_int_key');
		}
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_auto_label'])) {
			$data['shipping_hitshippo_dhlexpress_auto_label'] = $this->request->post['shipping_hitshippo_dhlexpress_auto_label'];
		} else {
			$data['shipping_hitshippo_dhlexpress_auto_label'] = $this->config->get('shipping_hitshippo_dhlexpress_auto_label');
		}
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_send_mail_to'])) {
			$data['shipping_hitshippo_dhlexpress_send_mail_to'] = $this->request->post['shipping_hitshippo_dhlexpress_send_mail_to'];
		} else {
			$data['shipping_hitshippo_dhlexpress_send_mail_to'] = $this->config->get('shipping_hitshippo_dhlexpress_send_mail_to');
		}	
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_plt'])) {
			$data['shipping_hitshippo_dhlexpress_plt'] = $this->request->post['shipping_hitshippo_dhlexpress_plt'];
		} else {
			$data['shipping_hitshippo_dhlexpress_plt'] = $this->config->get('shipping_hitshippo_dhlexpress_plt');
		}

		if (isset($this->request->post['shipping_hitshippo_dhlexpress_sat'])) {
			$data['shipping_hitshippo_dhlexpress_sat'] = $this->request->post['shipping_hitshippo_dhlexpress_sat'];
		} else {
			$data['shipping_hitshippo_dhlexpress_sat'] = $this->config->get('shipping_hitshippo_dhlexpress_sat');
		}

		if (isset($this->request->post['shipping_hitshippo_dhlexpress_cod'])) {
			$data['shipping_hitshippo_dhlexpress_cod'] = $this->request->post['shipping_hitshippo_dhlexpress_cod'];
		} else {
			$data['shipping_hitshippo_dhlexpress_cod'] = $this->config->get('shipping_hitshippo_dhlexpress_cod');
		}
		
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_email_trach'])) {
			$data['shipping_hitshippo_dhlexpress_email_trach'] = $this->request->post['shipping_hitshippo_dhlexpress_email_trach'];
		} else {
			$data['shipping_hitshippo_dhlexpress_email_trach'] = $this->config->get('shipping_hitshippo_dhlexpress_email_trach');
		}
				
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_airway'])) {
			$data['shipping_hitshippo_dhlexpress_airway'] = $this->request->post['shipping_hitshippo_dhlexpress_airway'];
		} else {
			$data['shipping_hitshippo_dhlexpress_airway'] = $this->config->get('shipping_hitshippo_dhlexpress_airway');
		}
				
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_dropoff_type'])) {
			$data['shipping_hitshippo_dhlexpress_dropoff_type'] = $this->request->post['shipping_hitshippo_dhlexpress_dropoff_type'];
		} else {
			$data['shipping_hitshippo_dhlexpress_dropoff_type'] = $this->config->get('shipping_hitshippo_dhlexpress_dropoff_type');
		}
				
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_duty_type'])) {
			$data['shipping_hitshippo_dhlexpress_duty_type'] = $this->request->post['shipping_hitshippo_dhlexpress_duty_type'];
		} else {
			$data['shipping_hitshippo_dhlexpress_duty_type'] = $this->config->get('shipping_hitshippo_dhlexpress_duty_type');
		}
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_output_type'])) {
			$data['shipping_hitshippo_dhlexpress_output_type'] = $this->request->post['shipping_hitshippo_dhlexpress_output_type'];
		} else {
			$data['shipping_hitshippo_dhlexpress_output_type'] = $this->config->get('shipping_hitshippo_dhlexpress_output_type');
		}
		
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_shipment_content'])) {
			$data['shipping_hitshippo_dhlexpress_shipment_content'] = $this->request->post['shipping_hitshippo_dhlexpress_shipment_content'];
		} else {
			$data['shipping_hitshippo_dhlexpress_shipment_content'] = $this->config->get('shipping_hitshippo_dhlexpress_shipment_content');
		}
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_logo'])) {
			$data['shipping_hitshippo_dhlexpress_logo'] = $this->request->post['shipping_hitshippo_dhlexpress_logo'];
		} else {
			$data['shipping_hitshippo_dhlexpress_logo'] = $this->config->get('shipping_hitshippo_dhlexpress_logo');
		}
		
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_pickup_auto'])) {
			$data['shipping_hitshippo_dhlexpress_pickup_auto'] = $this->request->post['shipping_hitshippo_dhlexpress_pickup_auto'];
		} else {
			$data['shipping_hitshippo_dhlexpress_pickup_auto'] = $this->config->get('shipping_hitshippo_dhlexpress_pickup_auto');
		}

		if (isset($this->request->post['shipping_hitshippo_dhlexpress_pickup_loc_type'])) {
			$data['shipping_hitshippo_dhlexpress_pickup_loc_type'] = $this->request->post['shipping_hitshippo_dhlexpress_pickup_loc_type'];
		} else {
			$data['shipping_hitshippo_dhlexpress_pickup_loc_type'] = $this->config->get('shipping_hitshippo_dhlexpress_pickup_loc_type');
		}

		if (isset($this->request->post['shipping_hitshippo_dhlexpress_pickup_del_type'])) {
			$data['shipping_hitshippo_dhlexpress_pickup_del_type'] = $this->request->post['shipping_hitshippo_dhlexpress_pickup_del_type'];
		} else {
			$data['shipping_hitshippo_dhlexpress_pickup_del_type'] = $this->config->get('shipping_hitshippo_dhlexpress_pickup_del_type');
		}

		if (isset($this->request->post['shipping_hitshippo_dhlexpress_pickup_type'])) {
			$data['shipping_hitshippo_dhlexpress_pickup_type'] = $this->request->post['shipping_hitshippo_dhlexpress_pickup_type'];
		} else {
			$data['shipping_hitshippo_dhlexpress_pickup_type'] = $this->config->get('shipping_hitshippo_dhlexpress_pickup_type');
		}

		if (isset($this->request->post['shipping_hitshippo_dhlexpress_pickup_days_after'])) {
			$data['shipping_hitshippo_dhlexpress_pickup_days_after'] = $this->request->post['shipping_hitshippo_dhlexpress_pickup_days_after'];
		} else {
			$data['shipping_hitshippo_dhlexpress_pickup_days_after'] = $this->config->get('shipping_hitshippo_dhlexpress_pickup_days_after');
		}

		if (isset($this->request->post['shipping_hitshippo_dhlexpress_pic_pack_lac'])) {
			$data['shipping_hitshippo_dhlexpress_pic_pack_lac'] = $this->request->post['shipping_hitshippo_dhlexpress_pic_pack_lac'];
		} else {
			$data['shipping_hitshippo_dhlexpress_pic_pack_lac'] = $this->config->get('shipping_hitshippo_dhlexpress_pic_pack_lac');
		}

		if (isset($this->request->post['shipping_hitshippo_dhlexpress_picper'])) {
			$data['shipping_hitshippo_dhlexpress_picper'] = $this->request->post['shipping_hitshippo_dhlexpress_picper'];
		} else {
			$data['shipping_hitshippo_dhlexpress_picper'] = $this->config->get('shipping_hitshippo_dhlexpress_picper');
		}
			if (isset($this->request->post['shipping_hitshippo_dhlexpress_piccon'])) {
			$data['shipping_hitshippo_dhlexpress_piccon'] = $this->request->post['shipping_hitshippo_dhlexpress_piccon'];
		} else {
			$data['shipping_hitshippo_dhlexpress_piccon'] = $this->config->get('shipping_hitshippo_dhlexpress_piccon');
		}
			if (isset($this->request->post['shipping_hitshippo_dhlexpress_pic_open_time'])) {
			$data['shipping_hitshippo_dhlexpress_pic_open_time'] = $this->request->post['shipping_hitshippo_dhlexpress_pic_open_time'];
		} else {
			$data['shipping_hitshippo_dhlexpress_pic_open_time'] = $this->config->get('shipping_hitshippo_dhlexpress_pic_open_time');
		}
			if (isset($this->request->post['shipping_hitshippo_dhlexpress_pic_close_time'])) {
			$data['shipping_hitshippo_dhlexpress_pic_close_time'] = $this->request->post['shipping_hitshippo_dhlexpress_pic_close_time'];
		} else {
			$data['shipping_hitshippo_dhlexpress_pic_close_time'] = $this->config->get('shipping_hitshippo_dhlexpress_pic_close_time');
		}

		//licence
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_licence_licence'])) {
			$data['shipping_hitshippo_dhlexpress_licence_licence'] = $this->request->post['shipping_hitshippo_dhlexpress_licence_licence'];
		} else {
			$data['shipping_hitshippo_dhlexpress_licence_licence'] = $this->config->get('shipping_hitshippo_dhlexpress_licence_licence');
		}
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_licence_email'])) {
			$data['shipping_hitshippo_dhlexpress_licence_email'] = $this->request->post['shipping_hitshippo_dhlexpress_licence_email'];
		} else {
			$data['shipping_hitshippo_dhlexpress_licence_email'] = $this->config->get('shipping_hitshippo_dhlexpress_licence_email');
		}
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_licence_expires'])) {
			$data['shipping_hitshippo_dhlexpress_licence_expires'] = $this->request->post['shipping_hitshippo_dhlexpress_licence_expires'];
		} else {
			$data['shipping_hitshippo_dhlexpress_licence_expires'] = $this->config->get('shipping_hitshippo_dhlexpress_licence_expires');
		}
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_licence_status_licence'])) {
			$data['shipping_hitshippo_dhlexpress_licence_status_licence'] = $this->request->post['shipping_hitshippo_dhlexpress_licence_status_licence'];
		} else {
			$data['shipping_hitshippo_dhlexpress_licence_status_licence'] = $this->config->get('shipping_hitshippo_dhlexpress_licence_status_licence');
		}
		
		//renew 
		if (isset($this->request->post['shipping_hitshippo_dhlexpress_renew_expires'])) {
			$data['shipping_hitshippo_dhlexpress_renew_expires'] = $this->request->post['shipping_hitshippo_dhlexpress_renew_expires'];
		} else {
			$data['shipping_hitshippo_dhlexpress_renew_expires'] = $this->config->get('shipping_hitshippo_dhlexpress_renew_expires');
		}

		if($data['shipping_hitshippo_dhlexpress_licence_expires'])
		{
			$curdate=strtotime(date('d-m-Y',strtotime('now')));
			$expire_date=strtotime( date('d-m-Y', strtotime($data['shipping_hitshippo_dhlexpress_licence_expires']) ) );
			if($expire_date < $curdate)
			{
				if($data['shipping_hitshippo_dhlexpress_renew_expires'])
				{
					$expire_date = $expire_date=strtotime( date('d-m-Y', strtotime($data['shipping_hitshippo_dhlexpress_renew_expires']) ) );
					if($expire_date < $curdate)
					{
						$data['shipping_hitshippo_dhlexpress_renew_status_licence'] = 'Expired';
					}
				}else
				{
					$data['shipping_hitshippo_dhlexpress_renew_status_licence'] = 'Expired';
				}			
			}
			$data['shipping_hitshippo_dhlexpress_licence_expires'] = date('D-M-Y',$expire_date);

		}

		//thilak
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('extension/shipping/hitshippo_dhlexpress', $data));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/shipping/hitshippo_dhlexpress')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['shipping_hitshippo_dhlexpress_key']) {
			$this->error['key'] = $this->language->get('error_key');
		}

		if (!$this->request->post['shipping_hitshippo_dhlexpress_password']) {
			$this->error['password'] = $this->language->get('error_password');
		}

		if (!$this->request->post['shipping_hitshippo_dhlexpress_account']) {
			$this->error['account'] = $this->language->get('error_account');
		}

		if (!$this->request->post['shipping_hitshippo_dhlexpress_postcode']) {
			$this->error['postcode'] = $this->language->get('error_postcode');
		}

		return !$this->error;
	}
}
