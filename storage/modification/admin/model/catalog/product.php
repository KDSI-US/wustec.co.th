<?php
/* This file is under Git Control by KDSI. */
class ModelCatalogProduct extends Model {
	public function addProduct($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "product SET 
                model = '" . $this->db->escape(strtoupper($data['model'])) . "', `style_code` = '" . $this->db->escape(strtoupper($data['style_code'])) . "',
             lookbook_id = '" . $this->db->escape($data['lookbook_id']) . "', sku = '" . $this->db->escape($data['sku']) . "', upc = '" . $this->db->escape($data['upc']) . "', ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', msrp = '" . (float)$data['msrp'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_added = NOW(), date_modified = NOW()");

		$product_id = $this->db->getLastId();

      // handle extra data for universal import
      $univimp_extra = array();
      
      if (!empty($data['import_batch'])) {
        $univimp_extra[] = 'import_batch = "' . $this->db->escape($data['import_batch']) . '"';
      }
      
      if (!empty($data['gkd_extra_fields']) && defined('GKD_UNIV_IMPORT')) {
        foreach ($data['gkd_extra_fields'] as $extra_field) {
          if (isset($data[$extra_field])) {
            $univimp_extra[] = '`' . $this->db->escape($extra_field) .'` = "' . $this->db->escape($data[$extra_field]) . '"';
          }
        }
      }
      
      if (!empty($univimp_extra)) {
        $this->db->query("UPDATE ".DB_PREFIX."product SET " . implode(',', $univimp_extra) . " WHERE product_id = '" . (int)$product_id . "'");
      }
    

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		foreach ($data['product_description'] as $language_id => $value) {
			
      // handle extra data for universal import
      $univimp_extra_desc = '';

      if (!empty($data['gkd_extra_desc_fields']) && defined('GKD_UNIV_IMPORT')) {
        foreach ($data['gkd_extra_desc_fields'] as $extra_field) {
          if (isset($value[$extra_field])) {
            $univimp_extra_desc .= '`' . $this->db->escape($extra_field) .'` = "' . $this->db->escape($value[$extra_field]) . '", ';
          }
        }
      }
      
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET " . $univimp_extra_desc . " product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					// Removes duplicates
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "' AND language_id = '" . (int)$language_id . "'");

						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

						$product_option_id = $this->db->getLastId();

			$this->db->query("UPDATE " . DB_PREFIX . "product_option SET master_option = '" . (int)$product_option['master_option'] . "' WHERE product_option_id = '" . $product_option_id . "'");
			

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");

			$product_option_value_id = $this->db->getLastId();
			if ((int)$product_option['master_option']) {
				$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET master_option_value = '" . (int)$product_option_value['master_option_value'] . "' WHERE product_option_value_id = '" . $product_option_value_id . "'");
			}
			if ($product_option_value['option_image'] != "") {
				$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET option_image = '" . $this->db->escape($product_option_value['option_image']) . "' WHERE product_option_value_id = '" . $product_option_value_id . "'");
			}
			if ($product_option_value['product_option_value_id']) {
				$old_id = $product_option_value['product_option_value_id'];
				$matching_table[$old_id]['new_id'] = $product_option_value_id;
			}

		if ($product_option_value['option_image_color_swatch'] != "") {
			$strQry = "UPDATE `" . DB_PREFIX . "product_option_value` 
				SET 
					`option_image_color_swatch` = '" . $this->db->escape($product_option_value['option_image_color_swatch']) . "' 
				WHERE 
					`product_option_value_id` = '" . $product_option_value_id . "'";
			$this->db->query($strQry);
		}
			
			
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");

			$product_option_id = $this->db->getLastId();
			$this->db->query("UPDATE " . DB_PREFIX . "product_option SET master_option = '" . (int)$product_option['master_option'] . "', master_option_value = '" . (int)$product_option['master_option_value'] . "' WHERE product_option_id = '" . $this->db->getLastId() . "'");
			
				}
			}
		}


			if (isset($matching_table)) {
				$query = $this->db->query("SELECT product_option_id, master_option_value FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "' AND master_option_value != '0' ORDER BY product_option_id ASC");
				if ($query->num_rows) {
					foreach ($query->rows as $result) {
						$old_id = $result['master_option_value'];
						$new_id = $matching_table[$old_id]['new_id'];
						$this->db->query("UPDATE " . DB_PREFIX . "product_option SET master_option_value = '" . (int)$new_id . "' WHERE product_option_id = '" . (int)$result['product_option_id'] . "'");
					}
				}
				$query = $this->db->query("SELECT product_option_value_id, master_option_value FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "' AND master_option_value != '0' ORDER BY product_option_value_id ASC");
				if ($query->num_rows) {
					foreach ($query->rows as $result) {
						$old_id = $result['master_option_value'];
						$new_id = $matching_table[$old_id]['new_id'];

						$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET master_option_value = '" . (int)$new_id . "' WHERE product_option_value_id = '" . (int)$result['product_option_value_id'] . "'");
					}
				}
			}
			
		if (isset($data['product_recurring'])) {
			foreach ($data['product_recurring'] as $recurring) {

				$query = $this->db->query("SELECT `product_id` FROM `" . DB_PREFIX . "product_recurring` WHERE `product_id` = '" . (int)$product_id . "' AND `customer_group_id = '" . (int)$recurring['customer_group_id'] . "' AND `recurring_id` = '" . (int)$recurring['recurring_id'] . "'");

				if (!$query->num_rows) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = '" . (int)$product_id . "', customer_group_id = '" . (int)$recurring['customer_group_id'] . "', `recurring_id` = '" . (int)$recurring['recurring_id'] . "'");
				}
			}
		}
		
		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}

		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
			}
		}

		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		if (isset($data['product_filter'])) {
			foreach ($data['product_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");

      // Universal Import/Export - apply filters to cateogories
        if (!empty($data['uiep_filter_to_category'])) {
          if (!empty($data['product_filter']) && !empty($data['product_category'])) {
            foreach ($data['product_category'] as $category_id) {
              foreach ($data['product_filter'] as $filter_id) {
                $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "category_filter SET category_id = '" . (int)$category_id . "', filter_id = '" . (int)$filter_id . "'");
              }
            }
          }
  			}
      
			}
		}

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $product_reward) {
				if ((int)$product_reward['points'] > 0) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$product_reward['points'] . "'");
				}
			}
		}
		
		// SEO URL

			$this->config->load('isenselabs/isenselabs_seo');
			$seo_model_loader = $this->config->get('isenselabs_seo_model');
			$seo_module_path = $this->config->get('isenselabs_seo_path');
			$this->load->model($seo_module_path);
            

            // SEO Pack :: Auto-generate images name
            if ($this->{$seo_model_loader}->getSetting('images_filename_product')) {
                $this->{$seo_model_loader}->generateImageNames(
                    $this->{$seo_model_loader}->getSetting('images_filename_product_additional'),
                    $product_id
                );
            }
            
		if (isset($data['product_seo_url'])) {
			foreach ($data['product_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($keyword) . "'");

               	} else {
					$autogenerate = $this->{$seo_model_loader}->getSetting('url_product_autogenerate');
					if ($autogenerate) {
                       $url_create = $this->{$seo_model_loader}->SeoProductURLs(array(array('product_id' => $product_id)), $language_id, $store_id);
					}
            
					}
				}
			}
		}
		

            if (!empty($data['h1'])) {
                foreach ($data['h1'] as $lang_id => $h1) {
                    $data_checker = $this->db->query("SELECT `product_id` FROM `" . DB_PREFIX . "seo_product_description` WHERE product_id='" . (int)$product_id . "' AND `language_id` = '" . $this->db->escape($lang_id) . "'");

                    if ($data_checker->num_rows == 0) {
                        $this->db->query("INSERT INTO `" . DB_PREFIX . "seo_product_description` SET product_id='" . (int)$product_id . "', `h1` = '" . $this->db->escape($h1) . "', `language_id` = " . $this->db->escape($lang_id) . "");
                    } else {
                        $this->db->query("UPDATE `" . DB_PREFIX . "seo_product_description` SET `h1` = '" . $this->db->escape($h1) . "' WHERE product_id='" . (int)$product_id . "' AND `language_id` = '" . $this->db->escape($lang_id) . "'");
                    }
                }
            }

            if (!empty($data['h2'])) {
                foreach ($data['h2'] as $lang_id => $h2) {
                    $data_checker = $this->db->query("SELECT `product_id` FROM `" . DB_PREFIX . "seo_product_description` WHERE product_id='" . (int)$product_id . "' AND `language_id` = '" . $this->db->escape($lang_id) . "'");

                    if ($data_checker->num_rows == 0) {
                        $this->db->query("INSERT INTO `" . DB_PREFIX . "seo_product_description` SET product_id='" . (int)$product_id . "', `h2` = '" . $this->db->escape($h2) . "', `language_id` = " . $this->db->escape($lang_id) . "");
                    } else {
                        $this->db->query("UPDATE `" . DB_PREFIX . "seo_product_description` SET `h2` = '" . $this->db->escape($h2) . "' WHERE product_id='" . (int)$product_id . "' AND `language_id` = '" . $this->db->escape($lang_id) . "'");
                    }
                }
            }
            
		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}


		$this->cache->delete('product');

		return $product_id;
	}

	public function editProduct($product_id, $data) {

      // handle extra data for universal import
      $univimp_extra = array();
      
      if (!empty($data['import_batch'])) {
        $univimp_extra[] = 'import_batch = "' . $this->db->escape($data['import_batch']) . '"';
      }
      
      if (!empty($data['gkd_extra_fields']) && defined('GKD_UNIV_IMPORT')) {
        foreach ($data['gkd_extra_fields'] as $extra_field) {
          if (isset($data[$extra_field])) {
            $univimp_extra[] = '`' . $this->db->escape($extra_field) .'` = "' . $this->db->escape($data[$extra_field]) . '"';
          }
        }
      }
      
      if (!empty($univimp_extra)) {
        $this->db->query("UPDATE ".DB_PREFIX."product SET " . implode(',', $univimp_extra) . " WHERE product_id = '" . (int)$product_id . "'");
      }
    
		$this->db->query("UPDATE " . DB_PREFIX . "product SET 
                model = '" . $this->db->escape(strtoupper($data['model'])) . "', `style_code` = '" . $this->db->escape(strtoupper($data['style_code'])) . "',
             lookbook_id = '" . $this->db->escape($data['lookbook_id']) . "', sku = '" . $this->db->escape($data['sku']) . "', upc = '" . $this->db->escape($data['upc']) . "', ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', msrp = '" . (float)$data['msrp'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");

		foreach ($data['product_description'] as $language_id => $value) {
			
      // handle extra data for universal import
      $univimp_extra_desc = '';

      if (!empty($data['gkd_extra_desc_fields']) && defined('GKD_UNIV_IMPORT')) {
        foreach ($data['gkd_extra_desc_fields'] as $extra_field) {
          if (isset($value[$extra_field])) {
            $univimp_extra_desc .= '`' . $this->db->escape($extra_field) .'` = "' . $this->db->escape($value[$extra_field]) . '", ';
          }
        }
      }
      
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET " . $univimp_extra_desc . " product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");

		if (!empty($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					// Removes duplicates
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");

		$product_option_value_array = array();
		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {

        if (defined('GKD_UNIV_IMPORT') && !isset($product_option['product_option_id'])) {
          $product_option['product_option_id'] = '';
        }
      
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

						$product_option_id = $this->db->getLastId();

			$this->db->query("UPDATE " . DB_PREFIX . "product_option SET master_option = '" . (int)$product_option['master_option'] . "' WHERE product_option_id = '" . $product_option_id . "'");
			

						foreach ($product_option['product_option_value'] as $product_option_value) {
							if (isset($product_option_value['product_option_value_id']) && ($product_option_value['master_option_value'] == "0" || in_array($product_option_value['master_option_value'], $product_option_value_array))){
							array_push($product_option_value_array, $product_option_value['product_option_value_id']);

        if (defined('GKD_UNIV_IMPORT') && !isset($product_option_value['product_option_value_id'])) {
          $product_option_value['product_option_value_id'] = '';
        }
      
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "', product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");

			$product_option_value_id = $this->db->getLastId();
			if ((int)$product_option['master_option']) {
				$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET master_option_value = '" . (int)$product_option_value['master_option_value'] . "' WHERE product_option_value_id = '" . $product_option_value_id . "'");
			}
			if ($product_option_value['option_image'] != "") {
				$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET option_image = '" . $this->db->escape($product_option_value['option_image']) . "' WHERE product_option_value_id = '" . $product_option_value_id . "'");
			}
				}
			if ($product_option_value['option_image_color_swatch'] != "") {
				$strQry = "UPDATE `" . DB_PREFIX . "product_option_value`
					SET
						`option_image_color_swatch` = '" . $this->db->escape($product_option_value['option_image_color_swatch']) . "'
					WHERE
						`product_option_value_id` = '" . $product_option_value_id . "'";
				$this->db->query($strQry);
			}

			
						}
					}

/*
		if ($product_option_value['option_image_color_swatch'] != "") {
			$strQry = "UPDATE `" . DB_PREFIX . "product_option_value` 
				SET 
					`option_image_color_swatch` = '" . $this->db->escape($product_option_value['option_image_color_swatch']) . "' 
				WHERE 
					`product_option_value_id` = '" . $product_option_value_id . "'";
			$this->db->query($strQry);
		}
			
*/
				} else {

        if (defined('GKD_UNIV_IMPORT') && !isset($product_option['product_option_id'])) {
          $product_option['product_option_id'] = '';
        }
      
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");

			$product_option_id = $this->db->getLastId();
			$this->db->query("UPDATE " . DB_PREFIX . "product_option SET master_option = '" . (int)$product_option['master_option'] . "', master_option_value = '" . (int)$product_option['master_option_value'] . "' WHERE product_option_id = '" . $this->db->getLastId() . "'");
			
				}
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = " . (int)$product_id);

		if (isset($data['product_recurring'])) {
			foreach ($data['product_recurring'] as $product_recurring) {
				$query = $this->db->query("SELECT `product_id` FROM `" . DB_PREFIX . "product_recurring` WHERE `product_id` = '" . (int)$product_id . "' AND `customer_group_id` = '" . (int)$product_recurring['customer_group_id'] . "' AND `recurring_id` = '" . (int)$product_recurring['recurring_id'] . "'");

				if (!$query->num_rows) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = '" . (int)$product_id . "', `customer_group_id` = '" . (int)$product_recurring['customer_group_id'] . "', `recurring_id` = '" . (int)$product_recurring['recurring_id'] . "'");
				}				
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_filter'])) {
			foreach ($data['product_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");

      // Universal Import/Export - apply filters to cateogories
        if (!empty($data['uiep_filter_to_category'])) {
          if (!empty($data['product_filter']) && !empty($data['product_category'])) {
            foreach ($data['product_category'] as $category_id) {
              foreach ($data['product_filter'] as $filter_id) {
                $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "category_filter SET category_id = '" . (int)$category_id . "', filter_id = '" . (int)$filter_id . "'");
              }
            }
          }
  			}
      
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $value) {
				if ((int)$value['points'] > 0) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$value['points'] . "'");
				}
			}
		}
		
		// SEO URL
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$product_id . "'");
		

			$this->config->load('isenselabs/isenselabs_seo');
			$seo_model_loader = $this->config->get('isenselabs_seo_model');
			$seo_module_path = $this->config->get('isenselabs_seo_path');
			$this->load->model($seo_module_path);
            

            // SEO Pack :: Auto-generate images name
            if ($this->{$seo_model_loader}->getSetting('images_filename_product')) {
                $this->{$seo_model_loader}->generateImageNames(
                    $this->{$seo_model_loader}->getSetting('images_filename_product_additional'),
                    $product_id
                );
            }
            
		if (isset($data['product_seo_url'])) {
			foreach ($data['product_seo_url']as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($keyword) . "'");

               	} else {
					$autogenerate = $this->{$seo_model_loader}->getSetting('url_product_autogenerate');
					if ($autogenerate) {
                       $url_create = $this->{$seo_model_loader}->SeoProductURLs(array(array('product_id' => $product_id)), $language_id, $store_id);
					}
            
					}
				}
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");


            if (!empty($data['h1'])) {
                foreach ($data['h1'] as $lang_id => $h1) {
                    $data_checker = $this->db->query("SELECT `product_id` FROM `" . DB_PREFIX . "seo_product_description` WHERE product_id='" . (int)$product_id . "' AND `language_id` = '" . $this->db->escape($lang_id) . "'");

                    if ($data_checker->num_rows == 0) {
                        $this->db->query("INSERT INTO `" . DB_PREFIX . "seo_product_description` SET product_id='" . (int)$product_id . "', `h1` = '" . $this->db->escape($h1) . "', `language_id` = " . $this->db->escape($lang_id) . "");
                    } else {
                        $this->db->query("UPDATE `" . DB_PREFIX . "seo_product_description` SET `h1` = '" . $this->db->escape($h1) . "' WHERE product_id='" . (int)$product_id . "' AND `language_id` = '" . $this->db->escape($lang_id) . "'");
                    }
                }
            }

            if (!empty($data['h2'])) {
                foreach ($data['h2'] as $lang_id => $h2) {
                    $data_checker = $this->db->query("SELECT `product_id` FROM `" . DB_PREFIX . "seo_product_description` WHERE product_id='" . (int)$product_id . "' AND `language_id` = '" . $this->db->escape($lang_id) . "'");

                    if ($data_checker->num_rows == 0) {
                        $this->db->query("INSERT INTO `" . DB_PREFIX . "seo_product_description` SET product_id='" . (int)$product_id . "', `h2` = '" . $this->db->escape($h2) . "', `language_id` = " . $this->db->escape($lang_id) . "");
                    } else {
                        $this->db->query("UPDATE `" . DB_PREFIX . "seo_product_description` SET `h2` = '" . $this->db->escape($h2) . "' WHERE product_id='" . (int)$product_id . "' AND `language_id` = '" . $this->db->escape($lang_id) . "'");
                    }
                }
            }
            
		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->cache->delete('product');
	}

	public function copyProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p WHERE p.product_id = '" . (int)$product_id . "'");

		if ($query->num_rows) {
			$data = $query->row;

			$data['sku'] = '';
			$data['upc'] = '';
			$data['viewed'] = '0';
			$data['keyword'] = '';

            $data['h1'] = array();
            $data['h2'] = array();
            
			$data['status'] = '0';

			$data['product_attribute'] = $this->getProductAttributes($product_id);
			$data['product_description'] = $this->getProductDescriptions($product_id);
			$data['product_discount'] = $this->getProductDiscounts($product_id);
			$data['product_filter'] = $this->getProductFilters($product_id);
			$data['product_image'] = $this->getProductImages($product_id);
			$data['product_option'] = $this->getProductOptions($product_id);
			$data['product_related'] = $this->getProductRelated($product_id);
			$data['product_reward'] = $this->getProductRewards($product_id);
			$data['product_special'] = $this->getProductSpecials($product_id);
			$data['product_category'] = $this->getProductCategories($product_id);
			$data['product_download'] = $this->getProductDownloads($product_id);
			$data['product_layout'] = $this->getProductLayouts($product_id);
			$data['product_store'] = $this->getProductStores($product_id);
			$data['product_recurrings'] = $this->getRecurrings($product_id);

			$this->addProduct($data);
		}
	}

 
		//KDSI PRODUCTS IMAGE UPLOADER ->
		public function getUnlikUrls($id, $value) {
			$query = $this->db->query("
				SELECT EXISTS(
					SELECT DISTINCT image FROM " . DB_PREFIX . "product_image WHERE image LIKE '%$value%' AND product_id!=$id 
					UNION SELECT DISTINCT image FROM " . DB_PREFIX . "product WHERE image LIKE '%$value%' AND product_id!=$id 
					UNION SELECT DISTINCT image FROM " . DB_PREFIX . "manufacturer WHERE image LIKE '%$value%' 
					UNION SELECT DISTINCT image FROM " . DB_PREFIX . "banner_image WHERE image LIKE '%$value%' 
					UNION SELECT DISTINCT image FROM " . DB_PREFIX . "category WHERE image LIKE '%$value%' 
					UNION SELECT DISTINCT image FROM " . DB_PREFIX . "option_value WHERE image LIKE '%$value%' 
					UNION SELECT DISTINCT description FROM " . DB_PREFIX . "product_description WHERE description LIKE '%$value%' AND product_id!=$id 
					UNION SELECT DISTINCT description FROM " . DB_PREFIX . "information_description WHERE description LIKE '%$value%' 
					UNION SELECT DISTINCT description FROM " . DB_PREFIX . "category_description WHERE description LIKE '%$value%'
				) AS result
			");
			return $query;
		}

		public function parseProductImage($id) {
			$query = $this->db->query("
				SELECT DISTINCT image FROM " . DB_PREFIX . "product_image WHERE product_id=$id 
				UNION SELECT DISTINCT image FROM " . DB_PREFIX . "product WHERE product_id=$id
			");
			$query_d = $this->db->query("SELECT DISTINCT description FROM " . DB_PREFIX . "product_description WHERE product_id=$id");
			$query->rows[] = $query_d->rows[0];
			return $query->rows;
		}
		//<-KDSI PRODUCTS IMAGE UPLOADER
		
	public function deleteProduct($product_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_recurring WHERE product_id = " . (int)$product_id);
		$this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_product WHERE product_id = '" . (int)$product_id . "'");

		$this->cache->delete('product');
	}

	public function getProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getProducts($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";


    if (!empty($data['filter_import_batch'])) {
			$sql .= " AND p.import_batch = '" . $this->db->escape($data['filter_import_batch']) . "'";
		}
      
		if (!empty($data['filter_name'])) {
			$search_filter_names = explode(' ', $this->db->escape($data['filter_name']));
			$sql .= " AND pd.name LIKE '%" . implode("%' AND pd.name LIKE '%", $search_filter_names) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$search_filter_names = explode(' ', $this->db->escape($data['filter_model']));
			$sql .= " AND p.model LIKE '%" . implode("%' AND p.model LIKE '%", $search_filter_names) . "%'";
		}

		if (!empty($data['filter_style_code'])) {
			$search_filter_names = explode(' ', $this->db->escape($data['filter_style_code']));
			$sql .= " AND p.style_code LIKE '%" . implode("%' AND p.style_code LIKE '%", $search_filter_names) . "%'";
		}
			

		if (!empty($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}

		if (!empty($data['filter_msrp'])) {
			$sql .= " AND p.msrp LIKE '" . $this->db->escape($data['filter_msrp']) . "%'";
		}
			
		if (isset($data['filter_quantity']) && $data['filter_quantity'] !== '') {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_quantity_check']) && !is_null($data['filter_quantity_check'])) {
			$sql .= " AND p.quantity <=  '" . (int)$data['filter_quantity_check'] . "'";
		}
			
		$sql .= " GROUP BY p.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.price',
			'p.msrp',
			'p.quantity',
			'p.status',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pd.name";
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

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getProductsByCategoryId($category_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2c.category_id = '" . (int)$category_id . "' ORDER BY pd.name ASC");

		return $query->rows;
	}

	public function getProductDescriptions($product_id) {
		$product_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'tag'              => $result['tag']
			);
		}

		return $product_description_data;
	}

	public function getProductCategories($product_id) {
		$product_category_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_category_data[] = $result['category_id'];
		}

		return $product_category_data;
	}

	public function getProductFilters($product_id) {
		$product_filter_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_filter_data[] = $result['filter_id'];
		}

		return $product_filter_data;
	}

	public function getProductAttributes($product_id) {
		$product_attribute_data = array();

		$product_attribute_query = $this->db->query("SELECT attribute_id FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' GROUP BY attribute_id");

		foreach ($product_attribute_query->rows as $product_attribute) {
			$product_attribute_description_data = array();

			$product_attribute_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

			foreach ($product_attribute_description_query->rows as $product_attribute_description) {
				$product_attribute_description_data[$product_attribute_description['language_id']] = array('text' => $product_attribute_description['text']);
			}

			$product_attribute_data[] = array(
				'attribute_id'                  => $product_attribute['attribute_id'],
				'product_attribute_description' => $product_attribute_description_data
			);
		}

		return $product_attribute_data;
	}

	public function getProductOptions($product_id) {
		$product_option_data = array();

		$product_option_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option` po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.sort_order ASC, po.product_option_id ASC");

		foreach ($product_option_query->rows as $product_option) {
			$product_option_value_data = array();

			$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON(pov.option_value_id = ov.option_value_id) WHERE pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' ORDER BY pov.product_option_value_id ASC");

			foreach ($product_option_value_query->rows as $product_option_value) {
				$product_option_value_data[] = array(
					'product_option_value_id' => $product_option_value['product_option_value_id'],
					'option_value_id'         => $product_option_value['option_value_id'],
					'quantity'                => $product_option_value['quantity'],
					'subtract'                => $product_option_value['subtract'],
					'price'                   => $product_option_value['price'],
					'price_prefix'            => $product_option_value['price_prefix'],
					'points'                  => $product_option_value['points'],
					'points_prefix'           => $product_option_value['points_prefix'],
					'weight'                  => $product_option_value['weight'],

			'master_option_value'		=> $product_option_value['master_option_value'],
			'option_image'				=> $product_option_value['option_image'],
			

							'option_image_color_swatch'		=> $product_option_value['option_image_color_swatch'],
			
					'weight_prefix'           => $product_option_value['weight_prefix']
				);
			}

			$product_option_data[] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => $product_option['value'],

			'master_option'             => $product_option['master_option'],
			'master_option_value'       => $product_option['master_option_value'],
			'master_option_data'        => $this->getProductOption($product_id, $product_option['master_option']),
			
				'required'             => $product_option['required']
			);
		}

		return $product_option_data;
	}

	public function getProductOptionValue($product_id, $product_option_value_id) {
		$query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getOptionValueDescription($option_value_id) {
		$query = $this->db->query("SELECT ovd.name FROM " . DB_PREFIX . "option_value_description ovd WHERE ovd.option_value_id = '" . (int)$option_value_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		return $query->row;
	}

	public function getParentOptionValueDescription($parent_product_option_value_id) {
		$query = $this->db->query("SELECT ovd.name FROM " . DB_PREFIX . "product_option_value ov INNER JOIN dbcapella_opencart_dev.kdsi_option_value_description ovd ON ov.option_value_id = ovd.option_value_id WHERE ov.product_option_value_id = '" . (int)$parent_product_option_value_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getParentOptionName($parent_option_id) {
		$query = $this->db->query("SELECT od.`name` FROM " . DB_PREFIX . "option_description od WHERE od.option_id = " . (int)$parent_option_id);

		return $query->row;
	}
			
	public function getProductOption($product_id, $option_id) {
		$product_option_data = array();
		$product_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "' AND po.option_id = '" . (int)$option_id . "' ORDER BY o.sort_order");
		$product_option = $product_option_query->row;
		if ($product_option) {
			if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
				$product_option_value_data = array();
				$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ov.sort_order");
				foreach ($product_option_value_query->rows as $product_option_value) {
					$parent_name = '';
					if ($product_option_value['master_option_value'] > 0) {
						$master_value_query = $this->db->query("SELECT ovd.name FROM " . DB_PREFIX . "option_value_description ovd LEFT JOIN " . DB_PREFIX . "product_option_value pov ON (ovd.option_value_id = pov.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value['master_option_value'] . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
						$parent_name = ' ('.$master_value_query->row['name'].')';
					}
					$product_option_value_data[] = array(
						'product_option_value_id' => $product_option_value['product_option_value_id'],
						'option_value_id'         => $product_option_value['option_value_id'],
						'name'                    => $product_option_value['name'] . $parent_name,
						'image'                   => $product_option_value['image'],
						'quantity'                => $product_option_value['quantity'],
						'subtract'                => $product_option_value['subtract'],
						'price'                   => $product_option_value['price'],
						'price_prefix'            => $product_option_value['price_prefix'],
						'points'                  => $product_option_value['points'],
						'points_prefix'           => $product_option_value['points_prefix'],
						'weight'                  => $product_option_value['weight'],

							'option_image_color_swatch'		=> $product_option_value['option_image_color_swatch'],
			
						'weight_prefix'           => $product_option_value['weight_prefix']
					);
				}
				$product_option_data = array(
					'product_option_id'    => $product_option['product_option_id'],
					'option_id'            => $product_option['option_id'],
					'name'                 => $product_option['name'],
					'type'                 => $product_option['type'],
					'product_option_value' => $product_option_value_data,
					'required'             => $product_option['required']
				);
			} else {
				$product_option_data = array(
					'product_option_id' => $product_option['product_option_id'],
					'option_id'         => $product_option['option_id'],
					'name'              => $product_option['name'],
					'type'              => $product_option['type'],
					'option_value'      => $product_option['value'],
					'required'          => $product_option['required']
				);
			}
		}
		return $product_option_data;
	}
 
	public function editProductOptions($product_id, $data) {
	
		$qry1 = "DELETE FROM " . DB_PREFIX . "product_option 
			WHERE 
				product_id = '" . (int)$product_id . "'";
		$this->db->query($qry1);
		
		$qry2 = "DELETE FROM " . DB_PREFIX . "product_option_value 
			WHERE 
				product_id = '" . (int)$product_id . "'";
		$this->db->query($qry2);
		
		if (isset($data['product_option'])) {
		
			foreach ($data['product_option'] as $product_option) {
			
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
				
					if (isset($product_option['product_option_value'])) {
					
						$qry3 = "INSERT INTO " . DB_PREFIX . "product_option 
							SET 
								product_option_id = '" . (int)$product_option['product_option_id'] . "', 
								product_id = '" . (int)$product_id . "', 
								option_id = '" . (int)$product_option['option_id'] . "', 
								required = '" . (int)$product_option['required'] . "'";
						$this->db->query($qry3);
						
						$product_option_id = $this->db->getLastId();
						
						$qry4 = "UPDATE " . DB_PREFIX . "product_option 
							SET 
								master_option = '" . (int)$product_option['master_option'] . "' 
							WHERE 
								product_option_id = '" . $product_option_id . "'";
						$this->db->query($qry4);
						
						foreach ($product_option['product_option_value'] as $product_option_value) {
						
							$qry5 = "INSERT INTO " . DB_PREFIX . "product_option_value 
								SET 
									product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "', 
									product_option_id = '" . (int)$product_option_id . "', 
									product_id = '" . (int)$product_id . "', 
									option_id = '" . (int)$product_option['option_id'] . "', 
									option_value_id = '" . (int)$product_option_value['option_value_id'] . "', 
									quantity = '" . (int)$product_option_value['quantity'] . "', 
									subtract = '" . (int)$product_option_value['subtract'] . "', 
									price = '" . (float)$product_option_value['price'] . "', 
									price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', 
									points = '" . (int)$product_option_value['points'] . "', 
									points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', 
									weight = '" . (float)$product_option_value['weight'] . "', 
									weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'";
							$this->db->query($qry5);
							
							$product_option_value_id = $this->db->getLastId();
							
							if ((int)$product_option['master_option']) {
							
								$qry6 = "UPDATE " . DB_PREFIX . "product_option_value 
									SET 
										master_option_value = '" . (int)$product_option_value['master_option_value'] . "' 
									WHERE 
										product_option_value_id = '" . $product_option_value_id . "'";
								$this->db->query($qry6);
							}
							
							if ($product_option_value['option_image'] != "") {
							
								$qry7 = "UPDATE " . DB_PREFIX . "product_option_value 
									SET 
										option_image = '" . $this->db->escape($product_option_value['option_image']) . "' 
									WHERE 
										product_option_value_id = '" . $product_option_value_id . "'";
								$this->db->query($qry7);
							}
						}
					}
				} else {
					$qry8 = "INSERT INTO " . DB_PREFIX . "product_option 
						SET 
							product_option_id = '" . (int)$product_option['product_option_id'] . "', 
							product_id = '" . (int)$product_id . "', 
							option_id = '" . (int)$product_option['option_id'] . "', 
							value = '" . $this->db->escape($product_option['value']) . "', 
							required = '" . (int)$product_option['required'] . "'";
					$this->db->query($qry8);

					$product_option_id = $this->db->getLastId();
					$qry9 = "UPDATE " . DB_PREFIX . "product_option 
					SET 
						master_option = '" . (int)$product_option['master_option'] . "', 
						master_option_value = '" . (int)$product_option['master_option_value'] . "' 
					WHERE 
						product_option_id = '" . $this->db->getLastId() . "'";
					$this->db->query($qry9);
				}
			}
		}
	}
			

	public function getProductImages($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getProductDiscounts($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' ORDER BY quantity, priority, price");

		return $query->rows;
	}

	public function getProductSpecials($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' ORDER BY priority, price");

		return $query->rows;
	}

	public function getProductRewards($product_id) {
		$product_reward_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_reward_data[$result['customer_group_id']] = array('points' => $result['points']);
		}

		return $product_reward_data;
	}

	public function getProductDownloads($product_id) {
		$product_download_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_download_data[] = $result['download_id'];
		}

		return $product_download_data;
	}

	public function getProductStores($product_id) {
		$product_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_store_data[] = $result['store_id'];
		}

		return $product_store_data;
	}
	
	public function getProductSeoUrls($product_id) {
		$product_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $product_seo_url_data;
	}
	
	public function getProductLayouts($product_id) {
		$product_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $product_layout_data;
	}

	public function getProductRelated($product_id) {
		$product_related_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_related_data[] = $result['related_id'];
		}

		return $product_related_data;
	}

	public function getRecurrings($product_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = '" . (int)$product_id . "'");

		return $query->rows;
	}

	public function getTotalProducts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";

		$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		if (isset($data['filter_quantity_check']) && !is_null($data['filter_quantity_check'])) {
			$sql .= " AND p.quantity >=  '" . (int)$data['filter_quantity_check'] . "'";
		}
			


    if (!empty($data['filter_import_batch'])) {
			$sql .= " AND p.import_batch = '" . $this->db->escape($data['filter_import_batch']) . "'";
		}
      
		if (!empty($data['filter_name'])) {
			$search_filter_names = explode(' ', $this->db->escape($data['filter_name']));
			$sql .= " AND pd.name LIKE '%" . implode("%' AND pd.name LIKE '%", $search_filter_names) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$search_filter_names = explode(' ', $this->db->escape($data['filter_model']));
			$sql .= " AND p.model LIKE '%" . implode("%' AND p.model LIKE '%", $search_filter_names) . "%'";
		}

		if (!empty($data['filter_style_code'])) {
			$search_filter_names = explode(' ', $this->db->escape($data['filter_style_code']));
			$sql .= " AND p.style_code LIKE '%" . implode("%' AND p.style_code LIKE '%", $search_filter_names) . "%'";
		}
			

		if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}

		if (isset($data['filter_msrp']) && !is_null($data['filter_msrp'])) {
			$sql .= " AND p.msrp LIKE '" . $this->db->escape($data['filter_msrp']) . "%'";
		}
			
		if (isset($data['filter_quantity']) && $data['filter_quantity'] !== '') {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalProductsByTaxClassId($tax_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE tax_class_id = '" . (int)$tax_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByStockStatusId($stock_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE stock_status_id = '" . (int)$stock_status_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByWeightClassId($weight_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE weight_class_id = '" . (int)$weight_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByLengthClassId($length_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE length_class_id = '" . (int)$length_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByDownloadId($download_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_download WHERE download_id = '" . (int)$download_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByManufacturerId($manufacturer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByAttributeId($attribute_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_attribute WHERE attribute_id = '" . (int)$attribute_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByOptionId($option_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_option WHERE option_id = '" . (int)$option_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByProfileId($recurring_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_recurring WHERE recurring_id = '" . (int)$recurring_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}
}
