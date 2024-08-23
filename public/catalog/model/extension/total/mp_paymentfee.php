<?php
class ModelExtensionTotalMpPaymentfee extends Model {
	public function getTotal($total) {
		if ($this->config->get('total_mp_paymentfee_status') && !empty($this->session->data['payment_method']['code'])) {
			$this->load->language('extension/total/mp_paymentfee');
			$rules = (array)$this->config->get('total_mp_paymentfee_rule');

			$customer_group_id = $this->customer->isLogged() ? $this->customer->getGroupId() : $this->config->get('config_customer_group_id');

			foreach($rules as $rule) {
				if($rule['code'] == $this->session->data['payment_method']['code'] && !empty($rule['status'])) {
					if(isset($rule['groups'][$customer_group_id])) {
						$explode_groups_rule = $rule['groups'][$customer_group_id];

						if(isset($explode_groups_rule['ruletype']) && $explode_groups_rule['ruletype'] == 1) {
						} else {
							$explode_groups_rule['fee'] = '-'. $explode_groups_rule['fee'];
						}

						if(isset($explode_groups_rule['condition']) && $explode_groups_rule['condition'] == 1) {
							$kart_total = $this->cart->getSubTotal();
						} else {
							$kart_total = $this->cart->getTotal();
						}

						if($kart_total >= $explode_groups_rule['total']) {
							if (isset($explode_groups_rule['type']) && $explode_groups_rule['type'] == 1) {
								$payment_fee = $kart_total / 100 * $explode_groups_rule['fee'];
								$text_percent = $explode_groups_rule['fee']. '%';
							} else {
								$payment_fee = $explode_groups_rule['fee'];
								$text_percent = number_format($explode_groups_rule['fee'], 2);
							}

							if($payment_fee) {
								if(!empty($rule['description'][$this->config->get('config_language_id')]['title'])) {
									$text_mp_paymentfee =  sprintf($rule['description'][$this->config->get('config_language_id')]['title'], $text_percent);
									$text_mp_paymentfee = html_entity_decode($text_mp_paymentfee, ENT_QUOTES, 'UTF-8');
								} else {
									$text_mp_paymentfee =  sprintf($this->language->get('text_mp_paymentfee'), $text_percent);
								}

								$total['totals'][] = [
									'code'       	=> 'mp_paymentfee',
									'title'      	=> $text_mp_paymentfee,
									'value'      	=> $payment_fee,
									'sort_order' 	=> $this->config->get('total_mp_paymentfee_sort_order')
								];

								if ($this->config->get('total_mp_paymentfee_tax')) {
									$tax_rates = $this->tax->getRates($payment_fee, $this->config->get('total_mp_paymentfee_tax'));

									foreach ($tax_rates as $tax_rate) {
										if (!isset($total['taxes'][$tax_rate['tax_rate_id']])) {
											$total['taxes'][$tax_rate['tax_rate_id']] = $tax_rate['amount'];
										} else {
											$total['taxes'][$tax_rate['tax_rate_id']] += $tax_rate['amount'];
										}
									}
								}


								$total['total'] += $payment_fee;

								break;
							}
						}
					}
				}
			}
		}
	}


	public function getCostbyPaymentCode($code) {
		if ($this->config->get('total_mp_paymentfee_status') && !empty($code)) {
			$this->load->language('extension/total/mp_paymentfee');
			$rules = (array)$this->config->get('total_mp_paymentfee_rule');

			$customer_group_id = $this->customer->isLogged() ? $this->customer->getGroupId() : $this->config->get('config_customer_group_id');

			foreach($rules as $rule) {
				if($rule['code'] == $code && !empty($rule['status'])) {
					if(isset($rule['groups'][$customer_group_id])) {
						$explode_groups_rule = $rule['groups'][$customer_group_id];
						if(isset($explode_groups_rule['ruletype']) && $explode_groups_rule['ruletype'] == 1) {
						} else {
							$explode_groups_rule['fee'] = '-'. $explode_groups_rule['fee'];
						}

						if(isset($explode_groups_rule['condition']) && $explode_groups_rule['condition'] == 1) {
							$kart_total = $this->cart->getSubTotal();
						} else {
							$kart_total = $this->cart->getTotal();
						}


						if($kart_total >= $explode_groups_rule['total']) {
							if (isset($explode_groups_rule['type']) && $explode_groups_rule['type'] == 1) {
								$payment_fee = $kart_total / 100 * $explode_groups_rule['fee'];

								/* Show In Currency Format */
								$show_cost_format = $kart_total / 100 * $explode_groups_rule['fee'];
								$show_cost_format = $this->currency->format($this->tax->calculate($show_cost_format, $this->config->get('total_mp_paymentfee_tax'), $this->config->get('config_tax')), $this->session->data['currency']);

								/* Show In Percent */
								// $show_cost_format = $explode_groups_rule['fee']. '%';
							} else {
								$payment_fee = $explode_groups_rule['fee'];

								/* Show In Currency Format */
								$show_cost_format = number_format($explode_groups_rule['fee'], 2);
								$show_cost_format = $this->currency->format($this->tax->calculate($show_cost_format, $this->config->get('total_mp_paymentfee_tax'), $this->config->get('config_tax')), $this->session->data['currency']);
							}

							if($payment_fee) {
								return $show_cost_format;
								break;
							}
						}
					}
				}
			}
		}
	}
}