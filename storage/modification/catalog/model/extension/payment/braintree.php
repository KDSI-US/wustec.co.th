<?php
/* This file is under Git Control by KDSI. */
//==============================================================================
// Braintree Payment Gateway Pro v2022-7-12
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
// 
// All code within this file is copyright Clear Thinking, LLC.
// You may not copy or reuse code within this file without written permission.
//==============================================================================

//namespace Opencart\Catalog\Model\Extension\Braintree\Payment;
//class Braintree extends \Opencart\System\Engine\Model {

class ModelExtensionPaymentBraintree extends Model {
	
	private $type = 'payment';
	private $name = 'braintree';
	
	//==============================================================================
	// recurringPayments()
	//==============================================================================
	public function recurringPayments() {
		return true;
	}
	
	//==============================================================================
	// getMethod()
	//==============================================================================
	public function getMethod($address, $total = 0) {
		$settings = $this->getSettings();
		
		$current_geozones = array();
		$geozones = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '0' OR zone_id = '" . (int)$address['zone_id'] . "')");
		foreach ($geozones->rows as $geozone) {
			$current_geozones[] = $geozone['geo_zone_id'];
		}
		if (empty($current_geozones)) {
			$current_geozones = array(0);
		}
		
		$language = (isset($this->session->data['language'])) ? $this->session->data['language'] : $this->config->get('config_language');
		
		if (empty($total)) {
			$order_totals = $this->getOrderTotals();
			$total = $order_totals['total'];
		}
		
		if (!$settings['status'] ||
			($settings['min_total'] && (float)$settings['min_total'] > $total) ||
			($settings['max_total'] && (float)$settings['max_total'] < $total) ||
			!array_intersect(array($this->config->get('config_store_id')), explode(';', $settings['stores'])) ||
			!array_intersect($current_geozones, explode(';', $settings['geo_zones'])) ||
			!array_intersect(array((int)$this->customer->getGroupId()), explode(';', $settings['customer_groups'])) ||
			!in_array($this->session->data['currency'], explode(';', $settings['currencies']))
		) {
			return array();
		} else {
			return array(
				'code'			=> $this->name,
				'sort_order'	=> $settings['sort_order'],
				'terms'			=> html_entity_decode($settings['terms_' . $language], ENT_QUOTES, 'UTF-8'),
				'title'			=> html_entity_decode($settings['title_' . $language], ENT_QUOTES, 'UTF-8'),
			);
		}
	}
	
	//==============================================================================
	// getSettings()
	//==============================================================================
	private function getSettings() {
		$code = (version_compare(VERSION, '3.0', '<') ? '' : $this->type . '_') . $this->name;
		
		$settings = array();
		$settings_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `code` = '" . $this->db->escape($code) . "' ORDER BY `key` ASC");
		
		foreach ($settings_query->rows as $setting) {
			$value = $setting['value'];
			if ($setting['serialized']) {
				$value = (version_compare(VERSION, '2.1', '<')) ? unserialize($setting['value']) : json_decode($setting['value'], true);
			}
			$split_key = preg_split('/_(\d+)_?/', str_replace($code . '_', '', $setting['key']), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
			
				if (count($split_key) == 1)	$settings[$split_key[0]] = $value;
			elseif (count($split_key) == 2)	$settings[$split_key[0]][$split_key[1]] = $value;
			elseif (count($split_key) == 3)	$settings[$split_key[0]][$split_key[1]][$split_key[2]] = $value;
			elseif (count($split_key) == 4)	$settings[$split_key[0]][$split_key[1]][$split_key[2]][$split_key[3]] = $value;
			else 							$settings[$split_key[0]][$split_key[1]][$split_key[2]][$split_key[3]][$split_key[4]] = $value;
		}
		
		if (version_compare(VERSION, '4.0', '<')) {
			$settings['extension_route'] = 'extension/' . $this->type . '/' . $this->name;
		} else {
			$settings['extension_route'] = 'extension/' . $this->name . '/' . $this->type . '/' . $this->name;
		}
		
		return $settings;
	}
	
	//==============================================================================
	// getOrderTotals()
	//==============================================================================
	private function getOrderTotals($stop_before = '') {
		$prefix = (version_compare(VERSION, '3.0', '<')) ? '' : 'total_';
		$order_total_extensions = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = 'total' ORDER BY `code` ASC")->rows;
		
		$sort_order = array();
		foreach ($order_total_extensions as $key => $value) {
			$sort_order[$key] = $this->config->get($prefix . $value['code'] . '_sort_order');
		}
		array_multisort($sort_order, SORT_ASC, $order_total_extensions);
		
		$order_totals = array();
		$total = 0;
		$taxes = $this->cart->getTaxes();
		$reference_array = array('totals' => &$order_totals, 'total' => &$total, 'taxes' => &$taxes);
		
		foreach ($order_total_extensions as $ot) {
			if (!empty($stop_before) && $ot['code'] == $stop_before) {
				break;
			}
			if (!$this->config->get($prefix . $ot['code'] . '_status') || $ot['code'] == 'intermediate_order_total') {
				continue;
			}
			
			if (version_compare(VERSION, '2.2', '<')) {
				$this->load->model('total/' . $ot['code']);
				$this->{'model_total_' . $ot['code']}->getTotal($order_totals, $total, $taxes);
			} elseif (version_compare(VERSION, '2.3', '<')) {
				$this->load->model('total/' . $ot['code']);
				$this->{'model_total_' . $ot['code']}->getTotal($reference_array);
			} elseif (version_compare(VERSION, '4.0', '<')) {
				$this->load->model('extension/total/' . $ot['code']);
				$this->{'model_extension_total_' . $ot['code']}->getTotal($reference_array);
			} else {
				$this->load->model('extension/' . $ot['extension'] . '/total/' . $ot['code']);
				$getTotalFunction = $this->{'model_extension_' . $ot['extension'] . '_total_' . $ot['code']}->getTotal;
				$getTotalFunction($order_totals, $taxes, $total);
			}
		}
		
		return $reference_array;
	}
}
?>