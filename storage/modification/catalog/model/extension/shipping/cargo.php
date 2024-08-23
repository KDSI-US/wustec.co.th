<?php
/* This file is under Git Control by KDSI. */
class ModelExtensionShippingCargo extends Model {
	function getQuote($address) {
		$this->load->language('extension/shipping/cargo');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('shipping_cargo_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if (!$this->config->get('shipping_cargo_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$quote_data = array();

			$quote_data['cargo'] = array(
				'code'         => 'cargo.cargo',
				'title'        => $this->language->get('text_description'),
				'cost'         => 0.00,
				'tax_class_id' => 0,
				'text'         => 'Cargo Shipping Fee Explanation is Here.',
				'cargo_instruction' => nl2br($this->config->get('shipping_cargo_instruction')),
				'cargo_instruction_text_color' => $this->config->get('shipping_cargo_instruction_text_color')
			);

			$method_data = array(
				'code'       => 'cargo',
				'title'      => $this->language->get('text_title'),
				'quote'      => $quote_data,
				'sort_order' => $this->config->get('shipping_cargo_sort_order'),
				'error'      => false
			);
		}

		return $method_data;
	}
}