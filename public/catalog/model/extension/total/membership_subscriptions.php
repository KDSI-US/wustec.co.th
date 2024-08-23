<?php
class ModelExtensionTotalMembershipSubscriptions extends Model {
	public function getTotal($total) {
		if($this->customer->getActivePlan() && $this->config->get('mpplan_status')) {
			$this->load->language('extension/total/membership_subscriptions');
			
			$this->load->model('membership/subscriptions');
			
			$discount_total = 0;
			$sub_total = $this->cart->getSubTotal();

			$customer_plan = $this->model_membership_subscriptions->getCustomerPlan($this->customer->getId(), $this->customer->getActivePlan());
			if($customer_plan) {			
				$discount_value = $this->model_membership_subscriptions->getCustomerGroupDiscount($customer_plan['mpplan_id'], $this->config->get('config_customer_group_id'));

				if($discount_value) {
					$plan_products = $this->model_membership_subscriptions->getMpplanProducts($customer_plan['mpplan_id']);

					foreach ($this->cart->getProducts() as $product) {
						$discount = 0;

						if(in_array($product['product_id'], $plan_products)) {
							$discount += $this->tax->calculate($product['total'], $product['tax_class_id'], $this->config->get('config_tax')) / 100 * $discount_value;
						}

						$discount_total += $discount;
					}

					if ($discount_total > 0) {
						$total['totals'][] = array(
							'code'       => 'membership_subscriptions',
							'title'      => sprintf($this->language->get('text_membership_subscriptions'), $customer_plan['plan_name'], $discount_value),
							'value'      => -$discount_total,
							'sort_order' => $this->config->get('total_membership_subscriptions_sort_order')
						);

						$total['total'] -= $discount_total;
					}
				}				
			}
		}
	}
}