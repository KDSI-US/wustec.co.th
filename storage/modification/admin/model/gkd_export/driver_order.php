<?php
/* This file is under Git Control by KDSI. */
class ModelGkdExportDriverOrder extends Model {
  private $langIdToCode = array();
  
  public function getItems($data = array(), $count = false) {
    // custom fields
		$custom_fields = $payment_custom_fields = $shipping_custom_fields = array();

		$filter_data = array(
			'sort'  => 'cf.sort_order',
			'order' => 'ASC'
		);
    
    if (version_compare(VERSION, '2', '>=')) {
      if (version_compare(VERSION, '2.1', '>=')) {  
        $this->load->model('customer/custom_field');
        $cf_query = $this->model_customer_custom_field->getCustomFields($filter_data);
      } else {
        $this->load->model('sale/custom_field');
        $cf_query = $this->model_sale_custom_field->getCustomFields($filter_data);
      }
      
      foreach ($cf_query as $custom_field) {
        if ($custom_field['location'] == 'account') {
          $custom_fields[] = $custom_field['custom_field_id'];
        } else if ($custom_field['location'] == 'address') {
          $payment_custom_fields[] = $shipping_custom_fields[] = $custom_field['custom_field_id'];
        }
      }
    }
    
    $customFieldIdToName = array();

    if (version_compare(VERSION, '2', '>=')) {
      if (version_compare(VERSION, '2.1', '>=')) {  
        $this->load->model('customer/custom_field');
        $cf_query = $this->model_customer_custom_field->getCustomFields(array('sort' => 'cf.sort_order', 'order' => 'ASC'));
      } else {
        $this->load->model('sale/custom_field');
        $cf_query = $this->model_sale_custom_field->getCustomFields(array('sort' => 'cf.sort_order', 'order' => 'ASC'));
      }
    }

    foreach ($cf_query as $custom_field) {
      $customFieldIdToName[$custom_field['custom_field_id']] = $custom_field['name'];
      /*
      $custom_fields[] = array(
        'custom_field_id'    => $custom_field['custom_field_id'],
        'custom_field_value' => $this->model_customer_custom_field->getCustomFieldValues($custom_field['custom_field_id']),
        'name'               => $custom_field['name'],
        'value'              => $custom_field['value'],
        'type'               => $custom_field['type'],
        'location'           => $custom_field['location'],
        'sort_order'         => $custom_field['sort_order']
      );
      */
    }
    
    $select = ($count) ? 'COUNT(*) AS total' : "o.*, cgd.name AS customer_group, CONCAT(o.firstname, ' ', o.lastname) AS customer, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS order_status";
    
    $sql = "SELECT ".$select." FROM `" . DB_PREFIX . "order` o";
    
    $sql .= " LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (o.customer_group_id = cgd.customer_group_id)";
    
		if (!empty($data['filter_order_status'])) {
			$implode = array();

			//$order_statuses = explode(',', $data['filter_order_status']);

			foreach ($data['filter_order_status'] as $order_status_id) {
				$implode[] = "o.order_status_id = '" . (int)$order_status_id . "'";
			}

			if ($implode) {
				$sql .= " WHERE (" . implode(" OR ", $implode) . ")";
			}
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}
    
    $sql .= " AND cgd.language_id = o.language_id";
    
    if (isset($data['filter_store']) && $data['filter_store'] !== '') {
			$sql .= " AND o.store_id = '" . (int)$data['filter_store'] . "'";
    }
    
		if (!empty($data['filter_order_id_min'])) {
			$sql .= " AND o.order_id >= '" . (int)$data['filter_order_id_min'] . "'";
		}
    
    if (!empty($data['filter_order_id_max'])) {
			$sql .= " AND o.order_id <= '" . (int)$data['filter_order_id_max'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}

    if (!empty($data['filter_customer_group'])) {
			$implode = array();
      
			foreach ($data['filter_customer_group'] as $customer_group_id) {
				$implode[] = "o.customer_group_id = '" . (int)$customer_group_id . "'";
			}

			if ($implode) {
				$sql .= " AND (" . implode(" OR ", $implode) . ")";
			}
		}
    
    if (!empty($data['filter_interval'])) {
			$sql .= " AND o.date_added >= '" . $this->db->escape(date('Y-m-d H:i:s', strtotime($data['filter_interval']))) . "'";
		}
    
		if (!empty($data['filter_date_added_min'])) {
			$sql .= " AND DATE(o.date_added) >= DATE('" . $this->db->escape($data['filter_date_added_min']) . "')";
		}
    
    if (!empty($data['filter_date_added_max'])) {
			$sql .= " AND DATE(o.date_added) <= DATE('" . $this->db->escape($data['filter_date_added_max']) . "')";
		}

		if (!empty($data['filter_date_modified_min'])) {
			$sql .= " AND DATE(o.date_modified) >= DATE('" . $this->db->escape($data['filter_date_modified_min']) . "')";
		}
    
    if (!empty($data['filter_date_modified_max'])) {
			$sql .= " AND DATE(o.date_modified) <= DATE('" . $this->db->escape($data['filter_date_modified_max']) . "')";
		}

		if (!empty($data['filter_total_min'])) {
			$sql .= " AND o.total >= '" . (float)$data['filter_total_min'] . "'";
		}
    
    if (!empty($data['filter_total_max'])) {
			$sql .= " AND o.total <= '" . (float)$data['filter_total_max'] . "'";
		}

		$sort_data = array(
			'o.order_id',
			'customer',
			'order_status',
			'o.date_added',
			'o.date_modified',
			'o.total'
		);
    
    // return count
    if ($count) {
      return $this->db->query($sql)->row['total'];
    }
    
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY o.order_id";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
    //echo $sql; die;
		$query = $this->db->query($sql);

    $result = array();
    $i = 0;
    foreach ($query->rows as &$row) {
      // calculate total
      
      if (!empty($data['date_format'])) {
        $row['date_added'] = date($data['date_format'], strtotime($row['date_added']));
        $row['date_modified'] = date($data['date_format'], strtotime($row['date_modified']));
      }
      
      if (!empty($row['total'])) {
        $row['total_in_order_currency'] = round($row['total'] * $row['currency_value'], 2);
      }
      
      if (empty($data['export_fields']) || in_array('totals', $data['export_fields']) || in_array('tax_title', $data['export_fields']) || in_array('tax_value', $data['export_fields'])) {
        $row += $this->getOrderTotals($row['order_id']);
      }
      
      // set custom field values
      foreach (array('', 'payment_', 'shipping_') as $cfType) {
        if (!empty($row[$cfType.'custom_field'])) {
          $cfDecoded = json_decode($row[$cfType.'custom_field'], true);
        } else {
          $cfDecoded = array();
        }
        
        foreach (${$cfType.'custom_fields'} as $cfId) {
          $cfNodeName = preg_replace('/[^a-z0-9]+/', '_', strtolower($customFieldIdToName[$cfId]));
          
          if ($cfNodeName == '_') {
            $cfNodeName = $cfId;
          }
          
          if (isset($cfDecoded[$cfId])) {
            $row[$cfType.'custom_field_'.$cfNodeName] = $cfDecoded[$cfId];
          } else {
            $row[$cfType.'custom_field_'.$cfNodeName] = '';
          }
          
          if (!empty($data['export_fields']) && in_array('custom_field', $data['export_fields'])) {
            $data['export_fields'][] = $cfType.'custom_field_'.$cfNodeName;
          }
        }
      }
      
      // set order products
      if (empty($data['export_fields']) || (in_array('product_id', $data['export_fields']) || in_array('product_name', $data['export_fields']) || in_array('product_model', $data['export_fields']))) {
        $products = $this->getOrderProducts($row['order_id'], $data['export_format']);
      } else {
        $products = array();
      }
      
      unset($row['custom_field'], $row['shipping_custom_field'], $row['payment_custom_field'], $row['payment_country_id'], $row['payment_zone_id'], $row['shipping_country_id'], $row['shipping_zone_id'], $row['order_status_id'], $row['marketing_id'], $row['language_id'], $row['currency_id']);
      
      if ($data['export_format'] == 'xml') {
        $result[$i] = $row;
        $result[$i]['products'] = $products;
        $i++;
      } else {
        if (empty($products)) {
          $result[$i] = $row;
          $result[$i]['product_id'] = '';
          $result[$i]['product_name'] = '';
          $result[$i]['product_model'] = '';
          $result[$i]['product_quantity'] = '';
          $result[$i]['product_price'] = '';
          $result[$i]['product_total'] = '';
          $result[$i]['product_tax'] = '';
          $result[$i]['product_reward'] = '';
          
          if (!empty($data['export_fields'])) {
            $result[$i] = array_merge(array_flip($data['export_fields']), array_intersect_key($result[$i], array_flip($data['export_fields'])));
          }
        
          $i++;
        } else {
          foreach ($products as $product) {
            $result[$i] = $row;
            $result[$i]['product_id'] = $product['product_id'];
            $result[$i]['product_name'] = $product['name'];
            $result[$i]['product_model'] = $product['model'];
            $result[$i]['product_quantity'] = $product['quantity'];
            $result[$i]['product_price'] = $product['price'];
            $result[$i]['product_total'] = $product['total'];
            $result[$i]['product_tax'] = $product['tax'];
            $result[$i]['product_reward'] = $product['reward'];
            
            if (isset($product['sku'])) {
              $result[$i]['product_sku'] = $product['sku'];
            }
            
            $options = $this->getOrderOptions($row['order_id'], $product['order_product_id']);
            
            $optionArray = array();
            
            if (!empty($options)) {
              foreach($options as $option) {
                $optionArray[] = $option['name'].': '.$option['value'];
              }
            }
            
            $result[$i]['product_options'] = implode('|', $optionArray);
            
            if (!empty($data['export_fields'])) {
              $result[$i] = array_merge(array_flip($data['export_fields']), array_intersect_key($result[$i], array_flip($data['export_fields'])));
            }
            
            $i++;
          }
        }
      }
    }

		return $result;
	}
  
  public function getOrderProducts($order_id, $format = '') {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
    
    if ($format == 'xml') {
      foreach ($query->rows as &$row) {
        $options = $this->getOrderOptions($row['order_id'], $row['order_product_id']);
        
        $row['options'] = $options;
      }
    }
    
		return $query->rows;
	}
  
  public function getOrderOptions($order_id, $order_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

		return $query->rows;
	}
  
  public function getOrderTotals($order_id, $asArray = false) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order")->rows;

    $data = array();
    
    foreach ($query as $total) {
      $data[$total['code'].'_title'] = $total['title'];
      $data[$total['code'].'_value'] = $total['value'];
    }
    
		return $data;
	}
  
  public function getTotalItems($data = array()) {
    return $this->getItems($data, true);
  }
}