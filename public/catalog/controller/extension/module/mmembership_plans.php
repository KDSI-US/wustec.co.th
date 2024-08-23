<?php
class ControllerExtensionModuleMmembershipPlans extends Controller {
	public function index() {
		$this->load->language('membership/prices');
		
		$this->load->model('membership/prices');
		
		$this->load->model('catalog/product');

		$this->document->addScript('catalog/view/javascript/modulepoints/membership.js');

		$this->document->addStyle('catalog/view/javascript/modulepoints/membership.css');

		$product_info = $this->model_catalog_product->getProduct($this->config->get('mpplan_product_id'));

		if ($this->config->get('mpplan_status') && $product_info) {
			$mpplan_description = $this->config->get('mpplan_description');
			
			$config_language_id = $this->config->get('config_language_id');

			$data['heading_title'] = !empty($mpplan_description[$config_language_id]['title']) ? $mpplan_description[$config_language_id]['title'] : $this->language->get('heading_title');
			$data['sub_title'] = !empty($mpplan_description[$config_language_id]['sub_title']) ? $mpplan_description[$config_language_id]['sub_title'] : '';
			$data['meta_description'] = !empty($mpplan_description[$config_language_id]['meta_description']) ? $mpplan_description[$config_language_id]['meta_description'] : '';
			$data['meta_keyword'] = !empty($mpplan_description[$config_language_id]['meta_keyword']) ? $mpplan_description[$config_language_id]['meta_keyword'] : '';
			$data['meta_title'] = !empty($mpplan_description[$config_language_id]['meta_title']) ? $mpplan_description[$config_language_id]['meta_title'] : '';

			$data['top_description'] = trim(html_entity_decode($mpplan_description[$config_language_id]['top_description'], ENT_QUOTES, 'UTF-8'));
			$data['bottom_description'] = trim(html_entity_decode($mpplan_description[$config_language_id]['bottom_description'], ENT_QUOTES, 'UTF-8'));
						
			$data['text_duration'] = $this->language->get('text_duration');

			$data['column_plan'] = $this->language->get('column_plan');			
			$data['column_feature'] = $this->language->get('column_feature');

			$data['button_buy'] = $this->language->get('button_buy');
			$data['button_details'] = $this->language->get('button_details');

			if($product_info) {
				$data['product_id'] = $product_info['product_id'];
				$data['minimum'] = $product_info['minimum'] ? $product_info['minimum'] : 1;
			} else {
				$data['product_id'] = 0;
				$data['minimum'] = 1;
			}

			$results = $this->model_membership_prices->getMpplans();

			$data['mpplans'] = array();
			foreach ($results as $result) {
				$duration = '';
				switch ($result['duration_type']) {
					case 'd':
					$duration = sprintf($this->language->get('text_days'), $result['duration_value']);
				    break;
				    case 'm':
					$duration = sprintf($this->language->get('text_months'), $result['duration_value']);
				    break;
				    case 'y':
					$duration = sprintf($this->language->get('text_years'), $result['duration_value']);
				    break;
				}

				if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$result['price']) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				$data['mpplans'][] = array(
					'mpplan_id'			=> $result['mpplan_id'],
					'name'				=> $result['name'],
					'product_id'			=> $result['product_id'],
					'first_bgcolor'		=> $result['first_bgcolor'],
					'first_textcolor'	=> $result['first_textcolor'],
					'second_bgcolor'	=> $result['second_bgcolor'],
					'second_textcolor'	=> $result['second_textcolor'],
					'duration'			=> $duration,
					'price'				=> $price,
					'informations' 		=> $this->model_membership_prices->getMpplanInfo($result['mpplan_id']),
					'href' 				=> $this->url->link('membership/plan_details', 'mpplan_id='. $result['mpplan_id'], true),
				);	
			}

			if($this->config->get('module_mmembership_plans_design') == '1') {
				return $this->load->view('extension/module/mmembership_planscols', $data);
			} else {
				return $this->load->view('extension/module/mmembership_plans', $data);
			}
		}
	}
}