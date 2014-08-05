<?php
class MsProduct extends Model {
	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 2;
	const STATUS_DISABLED = 3;
	const STATUS_DELETED = 4;
	const STATUS_UNPAID = 5;
	
	const MS_PRODUCT_VALIDATION_NONE = 1;
	const MS_PRODUCT_VALIDATION_APPROVAL = 2;
	
	private $errors;
	
	
	private function _getDepth($a, $eid) {
		foreach ($a as $key => $val) {
			if ($val['category_id'] == $eid) {
				if ($val['parent_id'] == 0) {
					return 0;
				} else {
					return 1 + $this->_getDepth($a, $val['parent_id']);
				}
			}
		}
	}
	
	private function _getPath($category_id) {
		$query = $this->db->query("SELECT name, parent_id FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name ASC");
		if ($query->row['parent_id']) {
			return $this->getPath($query->row['parent_id'], $this->config->get('config_language_id')) . $this->language->get('text_separator') . $query->row['name'];
		} else {
			return $query->row['name'];
		}
	}
	
	public function getCategories($parent_id = 0) {
		//$category_data = $this->cache->get('category.' . (int)$this->config->get('config_language_id') . '.' . (int)$parent_id);
		$category_data = FALSE;
		
		if (!$category_data) {
			$category_data = array();
		
			$sql = "SELECT
					c.category_id,
					c.parent_id,
					cd.name,
					(SELECT COUNT(*) FROM `" . DB_PREFIX . "category` cc WHERE cc.parent_id = c.category_id) as children
			FROM `" . DB_PREFIX . "category` c
			LEFT JOIN `" . DB_PREFIX . "category_description` cd
				ON (c.category_id = cd.category_id)
			WHERE c.parent_id = " . (int)$parent_id . "
			AND c.status = 1
			AND cd.language_id = " . (int)$this->config->get('config_language_id') . "
			ORDER BY c.sort_order, cd.name ASC";
			
			$query = $this->db->query($sql);
		
			foreach ($query->rows as $result) {
				$category_data[] = array(
					'category_id' => $result['category_id'],
					'parent_id' => $result['parent_id'],
					'name'        => $result['name'],
					'children'        => $result['children'],
					'disabled' => ((in_array($result['category_id'], $this->config->get('msconf_restrict_categories')) || ($this->config->get('msconf_additional_category_restrictions') == 1 && $result['parent_id'] == 0) || ($this->config->get('msconf_additional_category_restrictions') == 2 && $result['children'] > 0)) ? TRUE : FALSE)
				);
			
				//Recursive call of the function and merge of all the categories together
				$category_data = array_merge($category_data, $this->getCategories($result['category_id']));
			}
	
			//$this->cache->set('category.' . (int)$this->config->get('config_language_id') . '.' . (int)$parent_id, $category_data);
		}
		
		// The first calls of the function (for the root categories), where indentation takes place
		if ($parent_id == 0) {
			$category_data_indented = array();
			foreach ($category_data as $category) {
				$category_data_indented[] = array(
					'category_id' => $category['category_id'],
					'name'        => str_repeat('&nbsp;&nbsp;&nbsp;',$this->_getDepth($category_data, $category['category_id'])) . $category['name'],
					'parent_id' => $category['parent_id'],
					'children' => $category['children'],
					'disabled' => $category['disabled'],
				);
			}
			return $category_data_indented;
		}
		
		return $category_data;
	}
	
	public function getSellerId($product_id) {
		$sql = "SELECT seller_id FROM " . DB_PREFIX . "ms_product
				WHERE product_id = " . (int)$product_id;
				
		$res = $this->db->query($sql);
		
		if (isset($res->row['seller_id']))
			return $res->row['seller_id'];
		else
			return 0;
	}
	
	public function isEnabled($product_id) {
		$sql = "SELECT	p.status as enabled,
				FROM `" . DB_PREFIX . "product` p
				WHERE p.product_id = " . (int)$product_id;

		$res = $this->db->query($sql);
		
		if (!$res->row['enabled'])
			return false;
		else
			return true;
	}	
	
	public function getProductImages($product_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC";
		$res = $this->db->query($sql);
		
		$images = array();
		foreach ($res->rows as $row) {
			$images[$row['product_image_id']] = $row;
		}
		
		return $images;
	}

	public function getProductCategories($product_id) {
		$sql = "SELECT group_concat(ptc.category_id separator ',') as category_id FROM `" . DB_PREFIX . "product_to_category` ptc WHERE product_id = " . (int)$product_id;
		$res = $this->db->query($sql);
		return $res->row['category_id'];
	}

	public function getProductDownloads($product_id) {
		$sql = "SELECT 	*
				FROM `" . DB_PREFIX . "download` d
				LEFT JOIN `" . DB_PREFIX . "product_to_download` pd
					USING(download_id)
				WHERE pd.product_id = " . (int)$product_id;
		$res = $this->db->query($sql);
		
		$downloads = array();
		foreach ($res->rows as $row) {
			$downloads[$row['download_id']] = $row;
		}
		
		return $downloads;
	}

	public function getProductSpecials($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' ORDER BY priority, price");
		
		return $query->rows;
	}
	
	public function getProductDiscounts($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' ORDER BY quantity, priority, price");
		
		return $query->rows;
	}

	public function getProductThumbnail($product_id) {
		$query = $this->db->query("SELECT image FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		
		return $query->row;
	}		
		
	public function saveProduct($data) {
		reset($data['languages']); $first = key($data['languages']);
		$store_id = $this->config->get('config_store_id');

		if (isset($data['product_thumbnail'])) {
			$thumbnail = $this->MsLoader->MsFile->moveImage($data['product_thumbnail']);
		} else {
			$thumbnail = '';
		}

        $model = isset($data['product_model']) ? $data['product_model'] : $this->db->escape($data['languages'][$first]['product_name']);
        $sku = isset($data['product_sku']) ? $data['product_sku'] : '';
        $upc = isset($data['product_upc']) ? $data['product_upc'] : '';
        $ean = isset($data['product_ean']) ? $data['product_ean'] : '';
        $jan = isset($data['product_jan']) ? $data['product_jan'] : '';
        $isbn = isset($data['product_isbn']) ? $data['product_isbn'] : '';
        $mpn = isset($data['product_mpn']) ? $data['product_mpn'] : '';
        $manufacturer_id = isset($data['product_manufacturer_id']) ? $data['product_manufacturer_id'] : 0;
        $tax_class_id = isset($data['product_tax_class_id']) ? $data['product_tax_class_id'] : 0;
        $stock_status_id = isset($data['product_stock_status_id']) ? $data['product_stock_status_id'] : $this->config->get('config_stock_status_id');
        $date_available = isset($data['product_date_available']) ? $data['product_date_available'] : date('Y-m-d', time() - 86400);

		$sql = "INSERT INTO " . DB_PREFIX . "product
				SET model = '" . $this->db->escape($model) . "',
				    sku = '" . $this->db->escape($sku) . "',
				    upc = '" . $this->db->escape($upc) . "',
				    ean = '" . $this->db->escape($ean) . "',
				    jan = '" . $this->db->escape($jan) . "',
				    isbn = '" . $this->db->escape($isbn) . "',
				    mpn = '" . $this->db->escape($mpn) . "',
				    manufacturer_id = '" . (int)$manufacturer_id . "',
				    price = " . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($data['product_price']) . ",
					image = '" .  $this->db->escape($thumbnail)  . "',
					subtract = " . (int)$data['product_subtract'] . ",
                    tax_class_id = '" . $this->db->escape($tax_class_id) . "',
					stock_status_id = '" . (int)$stock_status_id . "',
					date_available = '" . $this->db->escape($date_available) . "',
					quantity = " . (int)$data['product_quantity'] . ",
					shipping = " . (int)$data['product_enable_shipping'] . ",
					status = " . (int)$data['enabled'] . ",
					date_added = NOW(),
					date_modified = NOW()";
		
		$this->db->query($sql);
		$product_id = $this->db->getLastId();
		
		if (isset($data['keyword'])) {
			//$similarity_query = $this->db->query("SELECT * FROM ". DB_PREFIX . "url_alias WHERE keyword LIKE '" . $this->db->escape($data['keyword']) . "%' AND query LIKE 'product_id=%'");
			$similarity_query = $this->db->query("SELECT * FROM ". DB_PREFIX . "url_alias WHERE keyword LIKE '" . $this->db->escape($data['keyword']) . "%'");
			$number = $similarity_query->num_rows;
			
			if ($number > 0) {
				$data['keyword'] = $data['keyword'] . "-" . $number;
			}
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}
		
		foreach ($data['languages'] as $language_id => $language) {
            $meta_description = isset($language['product_meta_description']) ? htmlspecialchars(nl2br($language['product_meta_description']), ENT_COMPAT) : '';
            $meta_keyword = isset($language['product_meta_keyword']) ? htmlspecialchars(nl2br($language['product_meta_keyword']), ENT_COMPAT) : '';

			$sql = "INSERT INTO " . DB_PREFIX . "product_description
					SET product_id = " . (int)$product_id . ",
						name = '". $this->db->escape($language['product_name']) ."',
						description = '". $this->db->escape($language['product_description']) ."',
						meta_description = '". $this->db->escape($meta_description) ."',
						meta_keyword = '". $this->db->escape($meta_keyword) ."',
						tag = '" . $this->db->escape($language['product_tags']) . "',
						language_id = " . (int)$language_id;
			$this->db->query($sql);
			
			// multilang attributes
			if (isset($language['product_attributes'])) {
				foreach($language['product_attributes'] as $attribute_id => $attr) {
					if (in_array($attr['attribute_type'], array(MsAttribute::TYPE_TEXT, MsAttribute::TYPE_TEXTAREA, MsAttribute::TYPE_DATE, MsAttribute::TYPE_DATETIME, MsAttribute::TYPE_TIME))) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "ms_attribute_value SET attribute_id = " . (int)$attribute_id);
						$attribute_value_id = $this->db->getLastId();
				
						$query = $this->db->query("SELECT oc_attribute_id FROM " . DB_PREFIX . "ms_attribute_attribute WHERE ms_attribute_id = '" . (int)$attribute_id . "' AND oc_attribute_id IS NOT NULL LIMIT 1");
						$oc_attribute_id = $query->row['oc_attribute_id'];
				
						$sql = "INSERT INTO " . DB_PREFIX . "ms_attribute_value_description
								SET attribute_id = " . (int)$attribute_id . ",
									attribute_value_id = " . (int)$attribute_value_id . ",
									language_id = $language_id,
									name = '" . $this->db->escape($attr['value']) . "'";
						$this->db->query($sql);
						
						$this->db->query("INSERT INTO " . DB_PREFIX . "ms_product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', attribute_value_id = '" . (int)$attribute_value_id . "'");
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$oc_attribute_id . "', language_id = '" . (int)$language_id . "', text = '" . $this->db->escape($attr['value']) . "'");
					}
				}
			}
		}
		
		$sql = "INSERT INTO " . DB_PREFIX . "ms_product
				SET product_id = " . (int)$product_id . ",
					seller_id = " . (int)$this->registry->get('customer')->getId() . ",
					product_status = " . (int)$data['product_status'] . ",
					product_approved = " . (int)$data['product_approved']
					. ( (isset($data['list_until']) && $data['list_until'] != NULL ) ? ", list_until = '" . $this->db->escape($data['list_until']) . "'" : "");

		$this->db->query($sql);
		
		foreach ($data['product_category'] as $id => $category_id) {
			$sql = "INSERT INTO " . DB_PREFIX . "product_to_category
					SET product_id = " . (int)$product_id . ",
						category_id = " . (int)$category_id;
			$this->db->query($sql);
		}

		$sql = "INSERT INTO " . DB_PREFIX . "product_to_store
				SET product_id = " . (int)$product_id . ",
					store_id = " . (int)$store_id;
		$this->db->query($sql);


		if (isset($data['product_images'])) {
			foreach ($data['product_images'] as $key => $img) {
				$newImagePath = $this->MsLoader->MsFile->moveImage($img);
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape(html_entity_decode($newImagePath, ENT_QUOTES, 'UTF-8')) . "', sort_order = '" . (int)$key . "'");
			}
		}
		
		if (isset($data['product_downloads'])) {
			foreach ($data['product_downloads'] as $key => $dl) {
				$newFile = $this->MsLoader->MsFile->moveDownload($dl['filename']);
				$fileMask = substr($newFile,0,strrpos($newFile,'.'));
				
				$this->db->query("INSERT INTO " . DB_PREFIX . "download SET remaining = 100, filename = '" . $this->db->escape($newFile) . "', mask = '" . $this->db->escape($fileMask) . "'");
				$download_id = $this->db->getLastId();
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
				
				foreach ($data['languages'] as $language_id => $language) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "download_description SET download_id = '" . (int)$download_id . "', name = '" . $this->db->escape($fileMask) . "', language_id = '" . (int)$language_id . "'");
				}
			}
		}

		if (isset($data['product_attributes'])) {
			foreach ($data['product_attributes'] as $attribute_id => $attr) {
				$query = $this->db->query("SELECT oc_attribute_id FROM " . DB_PREFIX . "ms_attribute_attribute WHERE ms_attribute_id = '" . (int)$attribute_id . "' AND oc_attribute_id IS NOT NULL LIMIT 1");
				$oc_attribute_id = $query->row['oc_attribute_id'];
				if (in_array($attr['attribute_type'], array(MsAttribute::TYPE_SELECT, MsAttribute::TYPE_RADIO, MsAttribute::TYPE_IMAGE))) {
					$val = array();
					$descriptions = $this->MsLoader->MsAttribute->getAttributeValueDescriptions($attr['value']);
					foreach ($descriptions as $language_id => $d) {
						$val[$language_id][] = $d['name'];
					}
					
					$this->db->query("INSERT INTO " . DB_PREFIX . "ms_product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', attribute_value_id = '" . (int)$attr['value'] . "'");
					
					foreach ($val as $language_id => $v) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$oc_attribute_id . "', language_id = '" . (int)$language_id . "', text = '" . $this->db->escape(implode(',', $v)) . "'");
					}					
				} else if ($attr['attribute_type'] == MsAttribute::TYPE_CHECKBOX) {
					$val = array();
					foreach ($attr['values'] as $attribute_value_id) {
						$descriptions = $this->MsLoader->MsAttribute->getAttributeValueDescriptions($attribute_value_id);
						foreach ($descriptions as $language_id => $d) {
							$val[$language_id][] = $d['name'];
						}
						$this->db->query("INSERT INTO " . DB_PREFIX . "ms_product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', attribute_value_id = '" . (int)$attribute_value_id . "'");
					}
					
					foreach ($val as $language_id => $v) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$oc_attribute_id . "', language_id = '" . (int)$language_id . "', text = '" . $this->db->escape(implode(',', $v)) . "'");
					}
				} else if (in_array($attr['attribute_type'], array(MsAttribute::TYPE_TEXT, MsAttribute::TYPE_TEXTAREA, MsAttribute::TYPE_DATE, MsAttribute::TYPE_DATETIME, MsAttribute::TYPE_TIME))) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "ms_attribute_value SET attribute_id = " . (int)$attribute_id);
					$attribute_value_id = $this->db->getLastId();
			
					$sql = "INSERT INTO " . DB_PREFIX . "ms_attribute_value_description
							SET attribute_id = " . (int)$attribute_id . ",
								attribute_value_id = " . (int)$attribute_value_id . ",
								language_id = 0,
								name = '" . $this->db->escape($attr['value']) . "'";
					$this->db->query($sql);
					
					$this->db->query("INSERT INTO " . DB_PREFIX . "ms_product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', attribute_value_id = '" . (int)$attribute_value_id . "'");
					
					foreach ($data['languages'] as $language_id => $language) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$oc_attribute_id . "', language_id = '" . (int)$language_id . "', text = '" . $this->db->escape($attr['value']) . "'");
					}
				}
			}
		}

		// options
		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				// unset sample
				if (isset($product_option['product_option_value'][0])) unset($product_option['product_option_value'][0]);
				
				// get type 
				$o = $this->MsLoader->MsOption->getOptions(array('option_id' => $product_option['option_id'], 'single' => 1));
				if (!$o) continue; else { $product_option['type'] = $o['type']; }
				
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");
		
					$product_option_id = $this->db->getLastId();
		
					if (isset($product_option['product_option_value']) && count($product_option['product_option_value']) > 0 ) {
						foreach ($product_option['product_option_value'] as $product_option_value) {
							$product_option_value['price_prefix'] = ($product_option_value['price_prefix'] == '-' ? '-' : '+'); 
							//$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', price = '" . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($product_option_value['price']) . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "'");
						}
					}else{
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_option_id = '".$product_option_id."'");
					}
				} else {
					//$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value = '" . $this->db->escape($product_option['option_value']) . "', required = '" . (int)$product_option['required'] . "'");
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value = '', required = '" . (int)0 . "'");
				}
			}
		}
		
		// specials
		if (isset($data['product_specials'])) {
			foreach ($data['product_specials'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($product_special['price']) . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}

		if (isset($data['product_discounts'])) {
			foreach ($data['product_discounts'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($product_discount['price']) . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}

		$this->registry->get('cache')->delete('product');
		
		return $product_id;
	}	

	public function editProduct($data) {
		reset($data['languages']); $first = key($data['languages']);
		$product_id = $data['product_id'];

		/*
		 * thumbnails
		 */
		$old_thumbnail = $this->getProductThumbnail($product_id);
		$old_images = $this->getProductImages($product_id);
		
		if (isset($data['product_thumbnail'])) {
			$keep_thumbnail = false;
			foreach ($old_images as $old_image) {
				if ($old_image['image'] == $data['product_thumbnail']) {
					$keep_thumbnail = true;
					$thumbnail = $old_image['image']; 
					break;
				}
			}
			
			if (!$keep_thumbnail) {
				if ($old_thumbnail['image'] == $data['product_thumbnail']) {
					$thumbnail = $old_thumbnail['image'];
				} else {
					$this->MsLoader->MsFile->deleteImage($old_thumbnail['image']);
					$thumbnail = $this->MsLoader->MsFile->moveImage($data['product_thumbnail']);				
				}
			}
		} else {
			$this->MsLoader->MsFile->deleteImage($old_thumbnail['image']);
			$thumbnail = '';
		}

        $included_field_sql = '';
        isset($data['product_model']) ? $included_field_sql .= " model = '" . $this->db->escape($data['product_model']) . "',"  : '';
        isset($data['product_sku']) ? $included_field_sql .= " sku = '" . $this->db->escape($data['product_sku']) . "',"  : '';
        isset($data['product_upc']) ? $included_field_sql .= " upc = '" . $this->db->escape($data['product_upc']) . "',"  : '';
        isset($data['product_ean']) ? $included_field_sql .= " ean = '" . $this->db->escape($data['product_ean']) . "',"  : '';
        isset($data['product_jan']) ? $included_field_sql .= " jan = '" . $this->db->escape($data['product_jan']) . "',"  : '';
        isset($data['product_isbn']) ? $included_field_sql .= " isbn = '" . $this->db->escape($data['product_isbn']) . "',"  : '';
        isset($data['product_mpn']) ? $included_field_sql .= " mpn = '" . $this->db->escape($data['product_mpn']) . "',"  : '';
        isset($data['product_manufacturer_id']) ? $included_field_sql .= " manufacturer_id = '" . (int)$data['product_manufacturer_id'] . "',"  : '';
        isset($data['product_tax_class_id']) ? $included_field_sql .= " tax_class_id = '" . $this->db->escape($data['product_tax_class_id']) . "',"  : '';
        isset($data['product_stock_status_id']) ? $included_field_sql .= " stock_status_id = '" . (int)$data['product_stock_status_id'] . "',"  : '';
        isset($data['product_date_available']) ? $included_field_sql .= " date_available = '" . $this->db->escape($data['product_date_available']) . "',"  : '';

		$sql = "UPDATE " . DB_PREFIX . "product
				SET" . $included_field_sql . " price = " . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($data['product_price']) . ",
					status = " . (int)$data['enabled'] . ",
					image = '" . $this->db->escape($thumbnail) . "',
					subtract = " . (int)$data['product_subtract'] . ",
					quantity = " . (int)$data['product_quantity'] . ",
					shipping = " . (int)$data['product_enable_shipping'] . ",
					date_modified = NOW()
				WHERE product_id = " . (int)$product_id;
		
		$this->db->query($sql);

		// this needs to be here
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_product_attribute WHERE product_id = " . (int)$product_id);
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = " . (int)$product_id);
		
		/*
		 * languages
		 */
		foreach ($data['languages'] as $language_id => $language) {
            $included_field_sql = '';
            isset($language['product_meta_description']) ? $included_field_sql .= " meta_description = '". $this->db->escape(htmlspecialchars(nl2br($language['product_meta_description']), ENT_COMPAT)) ."',"  : '';
            isset($language['product_meta_keyword']) ? $included_field_sql .= " meta_keyword = '". $this->db->escape(htmlspecialchars(nl2br($language['product_meta_keyword']), ENT_COMPAT)) ."',"  : '';

			$sql = "UPDATE " . DB_PREFIX . "product_description
					SET" . $included_field_sql . " name = '". $this->db->escape($language['product_name']) ."',
						description = '". $this->db->escape($language['product_description']) ."',
						tag = '". $this->db->escape($language['product_tags']) ."'
					WHERE product_id = " . (int)$product_id . "
					AND language_id = " . (int)$language_id;
					
			$this->db->query($sql);
			
			/*
			 * multilanguage attributes
			 */
			if (isset($language['product_attributes'])) {
				foreach($language['product_attributes'] as $attribute_id => $attr) {
					if (in_array($attr['attribute_type'], array(MsAttribute::TYPE_TEXT, MsAttribute::TYPE_TEXTAREA, MsAttribute::TYPE_DATE, MsAttribute::TYPE_DATETIME, MsAttribute::TYPE_TIME))) {
						if ((int)$attr['value_id'] == 0) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "ms_attribute_value SET attribute_id = " . (int)$attribute_id);
							$attr['value_id'] = $this->db->getLastId();
							
							$sql = "INSERT INTO " . DB_PREFIX . "ms_attribute_value_description
									SET attribute_id = " . (int)$attribute_id . ",
										attribute_value_id = " . (int)$attr['value_id'] . ",
										language_id = $language_id,
										name = '" . $this->db->escape($attr['value']) . "'";
							$this->db->query($sql);
						} else {
							$sql = "UPDATE " . DB_PREFIX . "ms_attribute_value_description
									SET name = '" . $this->db->escape($attr['value']) . "'
									WHERE attribute_id = " . (int)$attribute_id . "
									AND language_id = $language_id
									AND attribute_value_id = " . (int)$attr['value_id'];
							$this->db->query($sql);
						}
						
						$query = $this->db->query("SELECT oc_attribute_id FROM " . DB_PREFIX . "ms_attribute_attribute WHERE ms_attribute_id = '" . (int)$attribute_id . "' AND oc_attribute_id IS NOT NULL LIMIT 1");
						$oc_attribute_id = $query->row['oc_attribute_id'];						
						
						$this->db->query("INSERT INTO " . DB_PREFIX . "ms_product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', attribute_value_id = '" . (int)$attr['value_id'] . "'");
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$oc_attribute_id . "', language_id = '" . (int)$language_id . "', text = '" . $this->db->escape($attr['value']) . "'");
					}
				}
			}			
		}
		
		$sql = "UPDATE " . DB_PREFIX . "ms_product
				SET product_status = " . (int)$data['product_status'] . ",
					product_approved = " . (int)$data['product_approved']
					. ( (isset($data['list_until']) && $data['list_until'] != NULL && $data['list_until'] != 0 ) ? ", list_until = '" . $this->db->escape($data['list_until']) . "'" : " ") .
				"WHERE product_id = " . (int)$product_id; 
		$this->db->query($sql);
		
		$sql = "DELETE FROM " . DB_PREFIX . "product_to_category
				WHERE product_id = " . (int)$product_id;
		$this->db->query($sql);

		foreach ($data['product_category'] as $id => $category_id) {
			$sql = "INSERT INTO " . DB_PREFIX . "product_to_category
					SET product_id = " . (int)$product_id . ",
						category_id = " . (int)$category_id;
			$this->db->query($sql);	
		}

		/*
		 * images
		 */
		if (isset($data['product_images'])) {
			
			$new_images = $data['product_images'];
			
			foreach($old_images as $k => $old_image) {
				$key = array_search($old_image['image'], $data['product_images']);
				if ($key !== FALSE) {
					unset($old_images[$k]);
					unset($data['product_images'][$key]);
				}
			}
			
			foreach ($data['product_images'] as $key => $product_image) {
				$newImagePath = $this->MsLoader->MsFile->moveImage($product_image);
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape(html_entity_decode($newImagePath, ENT_QUOTES, 'UTF-8')) . "', sort_order = '" . (int)array_search($product_image, $new_images) . "'");
			}
			
			$i = 0;
			foreach ($new_images as $key => $image) {
				$this->db->query("UPDATE " . DB_PREFIX . "product_image SET sort_order = " . $i++ . " WHERE product_id = '" . (int)$product_id . "' AND image = '" . $this->db->escape(html_entity_decode($image, ENT_QUOTES, 'UTF-8')) . "'");
			}
		}

		foreach($old_images as $old_image) {
			if ($old_image['image'] != $thumbnail) {
				$this->MsLoader->MsFile->deleteImage($old_image['image']);
			}
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' AND product_image_id = '" . (int)$old_image['product_image_id'] . "'");
		}

		/*
		 * downloads
		 */
		$old_downloads = $this->getProductDownloads($product_id);
		if (isset($data['product_downloads'])) {
			foreach ($data['product_downloads'] as $key => $dl) {
				if (!empty($dl['download_id'])) {
					if (!empty($dl['filename'])) {
						// update download #download_id:
						$newFile = $this->MsLoader->MsFile->moveDownload($dl['filename']);
						$fileMask = substr($newFile,0,strrpos($newFile,'.'));
						
						$this->db->query("UPDATE " . DB_PREFIX . "download SET remaining = 100, filename = '" . $this->db->escape($newFile) . "', mask = '" . $this->db->escape($fileMask) . "' WHERE download_id = '" . (int)$dl['download_id'] . "'");
						
						if (isset($data['push_downloads'])) {
							$this->db->query("UPDATE " . DB_PREFIX . "order_download SET remaining = 100, `filename` = '" . $this->db->escape($newFile) . "', mask = '" . $this->db->escape($fileMask) . "', name = '" . $this->db->escape($fileMask) . "' WHERE `filename` = '" . $this->db->escape($old_downloads[$dl['download_id']]['filename']) . "'");
						}
						
						foreach ($data['languages'] as $language_id => $language) {
							$this->db->query("UPDATE " . DB_PREFIX . "download_description SET name = '" . $this->db->escape($fileMask) . "' WHERE download_id = '" . (int)$dl['download_id'] . "' AND language_id = '" . (int)$language_id . "'");
						}
						
						$this->MsLoader->MsFile->deleteDownload($old_downloads[$dl['download_id']]['filename']);
					} else {
						// do nothing
					}
					
					// don't remove the download
					unset($old_downloads[$dl['download_id']]);
				} else if (!empty($dl['filename'])) {
					// add new download
					$newFile = $this->MsLoader->MsFile->moveDownload($dl['filename']);
					$fileMask = substr($newFile,0,strrpos($newFile,'.'));					
					
					$this->db->query("INSERT INTO " . DB_PREFIX . "download SET remaining = 100, filename = '" . $this->db->escape($newFile) . "', mask = '" . $this->db->escape($fileMask) . "'");
					$download_id = $this->db->getLastId();
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
					
					foreach ($data['languages'] as $language_id => $language) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "download_description SET download_id = '" . (int)$download_id . "', name = '" . $this->db->escape($fileMask) . "', language_id = '" . (int)$language_id . "'");
					}
					
					if (isset($data['push_downloads'])) {
						$orders = $this->db->query("SELECT order_product_id, order_id FROM " . DB_PREFIX . "order_product WHERE product_id = '"  . (int)$product_id . "'");
						foreach ($orders->rows as $row) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "order_download SET order_id = '" . (int)$row['order_id'] . "', order_product_id = '" . (int)$row['order_product_id'] . "', remaining = 100, `filename` = '" . $this->db->escape($newFile) . "', mask = '" . $this->db->escape($fileMask) . "', name = '" . $this->db->escape($fileMask) . "'");
						}
					}
				}
			}
		}

		if (!empty($old_downloads)) {
			foreach($old_downloads as $old_download) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "download WHERE download_id ='" . (int)$old_download['download_id'] . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "download_description WHERE download_id ='" . (int)$old_download['download_id'] . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE download_id ='" . (int)$old_download['download_id'] . "'");
				$this->MsLoader->MsFile->deleteDownload($old_download['filename']);
			}
		}

		/*
		 * attributes
		 */
		if (isset($data['product_attributes'])) {
		foreach ($data['product_attributes'] as $attribute_id => $attr) {
		$query = $this->db->query("SELECT oc_attribute_id FROM " . DB_PREFIX . "ms_attribute_attribute WHERE ms_attribute_id = '" . (int)$attribute_id . "' AND oc_attribute_id IS NOT NULL LIMIT 1");
		$oc_attribute_id = $query->row['oc_attribute_id'];
						
		if (in_array($attr['attribute_type'], array(MsAttribute::TYPE_SELECT, MsAttribute::TYPE_RADIO, MsAttribute::TYPE_IMAGE))) {
			$val = array();
			$descriptions = $this->MsLoader->MsAttribute->getAttributeValueDescriptions($attr['value']);
			foreach ($descriptions as $language_id => $d) {
				$val[$language_id][] = $d['name'];
			}
			$this->db->query("INSERT INTO " . DB_PREFIX . "ms_product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', attribute_value_id = '" . (int)$attr['value'] . "'");
			
			foreach ($val as $language_id => $v) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$oc_attribute_id . "', language_id = '" . (int)$language_id . "', text = '" . $this->db->escape(implode(',', $v)) . "'");
			}
		} else if ($attr['attribute_type'] == MsAttribute::TYPE_CHECKBOX) {
			$val = array();
			foreach ($attr['values'] as $attribute_value_id) {
				$descriptions = $this->MsLoader->MsAttribute->getAttributeValueDescriptions($attribute_value_id);
				foreach ($descriptions as $language_id => $d) {
					$val[$language_id][] = $d['name'];
				}
				$this->db->query("INSERT INTO " . DB_PREFIX . "ms_product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', attribute_value_id = '" . (int)$attribute_value_id . "'");
			}
			foreach ($val as $language_id => $v) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$oc_attribute_id . "', language_id = '" . (int)$language_id . "', text = '" . $this->db->escape(implode(',', $v)) . "'");
			}
		} else if (in_array($attr['attribute_type'], array(MsAttribute::TYPE_TEXT, MsAttribute::TYPE_TEXTAREA, MsAttribute::TYPE_DATE, MsAttribute::TYPE_DATETIME, MsAttribute::TYPE_TIME))) {
			if ((int)$attr['value_id'] == 0) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "ms_attribute_value SET attribute_id = " . (int)$attribute_id);
				$attr['value_id'] = $this->db->getLastId();
				
				$sql = "INSERT INTO " . DB_PREFIX . "ms_attribute_value_description
						SET attribute_id = " . (int)$attribute_id . ",
							attribute_value_id = " . (int)$attr['value_id'] . ",
							language_id = " . $this->config->get('config_language_id') . ",
							name = '" . $this->db->escape($attr['value']) . "'";
				$this->db->query($sql);
			} else { 
				$sql = "UPDATE " . DB_PREFIX . "ms_attribute_value_description
						SET name = '" . $this->db->escape($attr['value']) . "'
						WHERE attribute_id = " . (int)$attribute_id . "
						AND attribute_value_id = " . (int)$attr['value_id'];
				$this->db->query($sql);
			}
			
			$this->db->query("INSERT INTO " . DB_PREFIX . "ms_product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', attribute_value_id = '" . (int)$attr['value_id'] . "'");
			
			foreach ($data['languages'] as $language_id => $language) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$oc_attribute_id . "', language_id = '" . (int)$language_id . "', text = '" . $this->db->escape($attr['value']) . "'");
					}
				}
			}
		}
		
		// options
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");		

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				// unset sample
				if (isset($product_option['product_option_value'][0])) unset($product_option['product_option_value'][0]);
		
				// get type
				$o = $this->MsLoader->MsOption->getOptions(array('option_id' => $product_option['option_id'], 'single' => 1));
				if (!$o) continue; else { $product_option['type'] = $o['type'];
				}
		
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");
		
					$product_option_id = $this->db->getLastId();
		
					if (isset($product_option['product_option_value']) && count($product_option['product_option_value']) > 0 ) {
						foreach ($product_option['product_option_value'] as $product_option_value) {
							$product_option_value['price_prefix'] = ($product_option_value['price_prefix'] == '-' ? '-' : '+');
							//$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "'");
						}
					}else{
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_option_id = '".$product_option_id."'");
					}
				} else {
					//$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value = '" . $this->db->escape($product_option['option_value']) . "', required = '" . (int)$product_option['required'] . "'");
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value = '', required = '" . (int)0 . "'");
				}
			}
		}		
		
		// specials
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
		if (isset($data['product_specials'])) {
			foreach ($data['product_specials'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($product_special['price']) . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}		
		
		// discounts
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");
		if (isset($data['product_discounts'])) {
			foreach ($data['product_discounts'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($product_discount['price']) . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}		
		
		$this->registry->get('cache')->delete('product');
		
		return $product_id;
	}
	
	public function hasDownload($product_id, $download_id) {
		$sql = "SELECT COUNT(*) as 'total'
				FROM `" . DB_PREFIX . "product_to_download`
				WHERE product_id = " . (int)$product_id . " 
				AND download_id = " . (int)$download_id;
		
		$res = $this->db->query($sql);
		
		return $res->row['total'];			
	}
	
	public function getDownload($download_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "download WHERE download_id = '" . (int)$download_id . "'");
		
		return $query->row;
	}
	
	public function productOwnedBySeller($product_id, $seller_id) {
		$sql = "SELECT COUNT(*) as 'total'
				FROM `" . DB_PREFIX . "ms_product`
				WHERE seller_id = " . (int)$seller_id . " 
				AND product_id = " . (int)$product_id;
		
		$res = $this->db->query($sql);
		
		return $res->row['total'];			
	}
	
	public function deleteProduct($product_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_product WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_product_attribute WHERE product_id = '" . (int)$product_id . "'");
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");
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
		$this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id. "'");
		
		$this->registry->get('cache')->delete('product');		
	}
	
	/*****************************************/
	
	public function getTotalProducts($data) {
		$sql = "
			SELECT COUNT(*) as total
			FROM " . DB_PREFIX . "product p
			LEFT JOIN " . DB_PREFIX . "ms_product mp
				USING (product_id)
			LEFT JOIN " . DB_PREFIX . "ms_seller ms
				USING (seller_id)
			WHERE 1 = 1 "
			. (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '')
			. (isset($data['product_status']) ? " AND product_status IN  (" .  $this->db->escape(implode(',', $data['product_status'])) . ")" : '')
			. (isset($data['enabled']) ? " AND status =  " .  (int)$data['enabled'] : '');

		$res = $this->db->query($sql);

		return $res->row['total'];
	}
	
	//todo
	public function getProduct($product_id) {
		$sql = "SELECT 	p.price,
		                p.model, p.sku, p.upc, p.ean, p.jan, p.isbn, p.mpn,
		                p.manufacturer_id, p.tax_class_id, p.subtract, p.stock_status_id, p.date_available,
						p.product_id as 'product_id',
						mp.product_status as 'mp.product_status',
						p.status as enabled,
						p.image as thumbnail,
						p.shipping as shipping,
						p.quantity as quantity,
						mp.product_status,
						mp.product_approved
				FROM `" . DB_PREFIX . "product` p
				LEFT JOIN `" . DB_PREFIX . "ms_product` mp
					ON p.product_id = mp.product_id
				WHERE p.product_id = " . (int)$product_id;
		$res = $this->db->query($sql);

		if (!$res->num_rows) return FALSE;

		$sql = "SELECT pd.*,
					   pd.description as 'pd.description'
				FROM " . DB_PREFIX . "product_description pd
				WHERE pd.product_id = " . (int)$product_id . "
				GROUP BY language_id";

		$descriptions = $this->db->query($sql);
		$product_description_data = array();
		foreach ($descriptions->rows as $result) {
			$product_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description'],
				'tags'      => $result['tag'],
				'meta_keyword'     => $result['meta_keyword'],
				'meta_description' => $result['meta_description']
			);
		}

		$res->row['languages'] = $product_description_data;
		return $res->row;
	}	
	
	public function getProducts($data = array(), $sort = array(), $cols = array()) {
		$hFilters = $wFilters = '';

		if(isset($sort['filters'])) {
			$cols = array_merge($cols, array("`p.date_created`" => 1));
			foreach($sort['filters'] as $k => $v) {
				if (!isset($cols[$k])) {
					$wFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				} else {
					$hFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				}
			}
		}
		
		// todo validate order parameters
		$sql = "SELECT
					SQL_CALC_FOUND_ROWS "
					// additional columns
					. (isset($cols['product_earnings']) ? "
						(SELECT SUM(seller_net_amt) AS seller_total
						FROM " . DB_PREFIX . "order_product op
						INNER JOIN `" . DB_PREFIX . "ms_order_product_data` mopd
							ON (op.product_id = mopd.product_id)
						WHERE op.product_id = p.product_id) as product_earnings,
					" : "")
					
					."p.product_id as 'product_id',
					p.image as 'p.image',
					p.price as 'p.price',
					pd.name as 'pd.name',
					ms.seller_id as 'seller_id',
					ms.nickname as 'ms.nickname',
					mp.product_status as 'mp.product_status',
					mp.product_approved as 'mp.product_approved',
					mp.number_sold as 'mp.number_sold',
					mp.list_until as 'mp.list_until',
					p.date_added as 'p.date_created',
					p.date_modified  as 'p.date_modified',
					pd.description as 'pd.description'
				FROM " . DB_PREFIX . "product p
				INNER JOIN " . DB_PREFIX . "product_description pd
					USING(product_id)
				LEFT JOIN " . DB_PREFIX . "ms_product mp
					USING(product_id)
				LEFT JOIN " . DB_PREFIX . "ms_seller ms
					USING (seller_id)
				WHERE 1 = 1"

				. (isset($data['seller_id']) ? " AND ms.seller_id =  " .  (int)$data['seller_id'] : '')
				. (isset($data['language_id']) ? " AND pd.language_id =  " .  (int)$data['language_id'] : '')				
				. (isset($data['product_status']) ? " AND product_status IN  (" .  $this->db->escape(implode(',', $data['product_status'])) . ")" : '')
				
				. $wFilters
				
				. " GROUP BY p.product_id HAVING 1 = 1 "
				
				. $hFilters
				
				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
				. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);
		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];

		return $res->rows;
	}
	
	public function getStatus($product_id) {
		$sql = "SELECT mp.product_status AS status
				FROM `" . DB_PREFIX . "ms_product` mp
				WHERE product_id = " . (int)$product_id;
		
		$res = $this->db->query($sql);
		
		return $res->row['status'];
	}

	public function changeStatus($product_id, $product_status) {
		$sql = "UPDATE " . DB_PREFIX . "ms_product
				SET	product_status =  " .  (int)$product_status . "
				WHERE product_id = " . (int)$product_id;
		
		$res = $this->db->query($sql);

		if ($product_status == MsProduct::STATUS_ACTIVE)
			$enabled = 1;
		else
			$enabled = 0;
		
		$sql = "UPDATE " . DB_PREFIX . "product
				SET status = " . (int)$enabled . " WHERE product_id = " . (int)$product_id;

		$res = $this->db->query($sql);
		$this->registry->get('cache')->delete('product');
	}
	
	public function approve($product_id) {
		$sql = "UPDATE " . DB_PREFIX . "ms_product
				SET	product_approved =  1
				WHERE product_id = " . (int)$product_id;
		
		$res = $this->db->query($sql);
		$this->registry->get('cache')->delete('product');
	}
	
	public function disapprove($product_id) {
		$sql = "UPDATE " . DB_PREFIX . "ms_product
				SET	product_approved =  0
				WHERE product_id = " . (int)$product_id;
		
		$res = $this->db->query($sql);
		
		$this->registry->get('cache')->delete('product');
	}
	
	public function createRecord($product_id, $data = array()) {
		$sql = "INSERT IGNORE INTO " . DB_PREFIX . "ms_product
				SET	product_id =  " . (int)$product_id . ",
					product_status = " . (int)MsProduct::STATUS_INACTIVE
				. (isset($data['seller_id']) ? ", seller_id =  " .  (int)$data['seller_id'] : '');
		
		$res = $this->db->query($sql);
	}
	
	public function getSaleData($product_id) {
		// note: change getProducts() if changing this
		$sql = "SELECT SUM(seller_net_amt) AS seller_total
				FROM " . DB_PREFIX . "order_product op
				INNER JOIN `" . DB_PREFIX . "ms_order_product_data` mopd
					ON (op.product_id = mopd.product_id)
				WHERE op.product_id = " . (int)$product_id . "
				GROUP BY order_product_id";

		$res = $this->db->query($sql);
		return $res->num_rows ? $res->row : FALSE;
	}
	
	public function changeSeller($product_id, $seller_id) {
		$sql = "UPDATE " . DB_PREFIX . "ms_product
				SET	seller_id =  " . (int)$seller_id . "
				WHERE product_id = " . (int)$product_id;
		$res = $this->db->query($sql);
		$this->registry->get('cache')->delete('product');
	}

    public function getManufacturers($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "manufacturer";

        if (!empty($data['filter_name'])) {
            $sql .= " WHERE name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        $sort_data = array(
            'name',
            'sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY name";
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
}
?>
