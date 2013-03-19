<?php
class MsProduct extends Model {
	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 2;
	const STATUS_DISABLED = 3;
	const STATUS_DELETED = 4;
	
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
					cd.name
			FROM `" . DB_PREFIX . "category` c
			LEFT JOIN `" . DB_PREFIX . "category_description` cd
				ON (c.category_id = cd.category_id)
			WHERE c.parent_id = " . (int)$parent_id . "
			AND c.status = 1
			AND cd.language_id = " . (int)$this->config->get('config_language_id') . "
			ORDER BY c.sort_order, cd.name ASC";
			
			$query = $this->db->query($sql);
			//"SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name ASC");
		
			foreach ($query->rows as $result) {
				$category_data[] = array(
					'category_id' => $result['category_id'],
					'parent_id' => $result['parent_id'],
					'name'        => $result['name'],
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
				);
			}
			return $category_data_indented;
		}
		
		return $category_data;
	}
	
	public function getMultipleCategories($parent_id = 0) {
		$category_data = $this->cache->get('category.' . (int)$this->config->get('config_language_id') . '.' . (int)$parent_id);
	
		if (!$category_data) {
			$category_data = array();
			
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name ASC");
			
			foreach ($query->rows as $result) {
				$category_data[] = array(
					'category_id' => $result['category_id'],
					'name'        => $this->_getPath($result['category_id'], $this->config->get('config_language_id')),
					'status'  	  => $result['status'],
					'sort_order'  => $result['sort_order']
				);
			
				$category_data = array_merge($category_data, $this->getCategories($result['category_id']));
			}
	
			$this->cache->set('category.' . (int)$this->config->get('config_language_id') . '.' . (int)$parent_id, $category_data);
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

		$sql = "INSERT INTO " . DB_PREFIX . "product
				SET price = " . (float)$data['product_price'] . ",
					model = '".$this->db->escape($data['languages'][$first]['product_name']) ."',
					image = '" .  $this->db->escape($thumbnail)  . "',
					subtract = " . (int)$data['product_subtract'] . ",
					quantity = " . (int)$data['product_quantity'] . ",
					shipping = " . (int)$data['product_enable_shipping'] . ",
					status = " . (int)$data['enabled'] . ",
					date_available = NOW(),				
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
			if (strcmp(VERSION,'1.5.4') >= 0) {
				$sql = "INSERT INTO " . DB_PREFIX . "product_description
						SET product_id = " . (int)$product_id . ",
							name = '". $this->db->escape($language['product_name']) ."',
							description = '". $this->db->escape(htmlspecialchars(nl2br($language['product_description']), ENT_COMPAT)) ."',
							tag = '" . $this->db->escape($language['product_tags']) . "',
							language_id = " . (int)$language_id;
				$this->db->query($sql);
			} else {
				$sql = "INSERT INTO " . DB_PREFIX . "product_description
						SET product_id = " . (int)$product_id . ",
							name = '". $this->db->escape($language['product_name']) ."',
							description = '". $this->db->escape(htmlspecialchars(nl2br($language['product_description']), ENT_COMPAT)) ."',
							language_id = " . (int)$language_id;
				$this->db->query($sql);
				
				if ($language['product_tags']) {
					$tags = explode(',', $language['product_tags']);
						
					foreach ($tags as $tag) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_tag SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', tag = '" . $this->db->escape(trim($tag)) . "'");
					}
				}
			}
			
			// multilang attributes
			if (isset($language['product_attributes'])) {
				foreach($language['product_attributes'] as $attribute_id => $attr) {
					if (in_array($attr['attribute_type'], array(MsAttribute::TYPE_TEXT, MsAttribute::TYPE_TEXTAREA, MsAttribute::TYPE_DATE, MsAttribute::TYPE_DATETIME, MsAttribute::TYPE_TIME))) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "ms_attribute_value SET attribute_id = " . (int)$attribute_id);
						$attribute_value_id = $this->db->getLastId();
				
						$sql = "INSERT INTO " . DB_PREFIX . "ms_attribute_value_description
								SET attribute_id = " . (int)$attribute_id . ",
									attribute_value_id = " . (int)$attribute_value_id . ",
									language_id = $language_id,
									name = '" . $this->db->escape($attr['value']) . "'";
						$this->db->query($sql);
						
						$this->db->query("INSERT INTO " . DB_PREFIX . "ms_product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', attribute_value_id = '" . (int)$attribute_value_id . "'");
					}
				}
			}
		}
		
		$sql = "INSERT INTO " . DB_PREFIX . "ms_product
				SET product_id = " . (int)$product_id . ",
					seller_id = " . (int)$this->registry->get('customer')->getId() . ",
					product_status = " . (int)$data['product_status'] . ",
					product_approved = " . (int)$data['product_approved'];

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
				
				$this->db->query("INSERT INTO " . DB_PREFIX . "download SET remaining = 5, filename = '" . $this->db->escape($newFile) . "', mask = '" . $this->db->escape($fileMask) . "'");
				$download_id = $this->db->getLastId();
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
				
				foreach ($data['languages'] as $language_id => $language) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "download_description SET download_id = '" . (int)$download_id . "', name = '" . $this->db->escape($fileMask) . "', language_id = '" . (int)$language_id . "'");
				}
			}
		}

		if (isset($data['product_attributes'])) {
			foreach ($data['product_attributes'] as $attribute_id => $attr) {
				if (in_array($attr['attribute_type'], array(MsAttribute::TYPE_SELECT, MsAttribute::TYPE_RADIO, MsAttribute::TYPE_IMAGE))) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "ms_product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', attribute_value_id = '" . (int)$attr['value'] . "'");
				} else if ($attr['attribute_type'] == MsAttribute::TYPE_CHECKBOX) {
					foreach ($attr['values'] as $attribute_value_id) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "ms_product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', attribute_value_id = '" . (int)$attribute_value_id . "'");
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
				}
			}
		}

		if (isset($data['product_specials'])) {
			foreach ($data['product_specials'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}

		if (isset($data['product_discounts'])) {
			foreach ($data['product_discounts'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}

		$this->registry->get('cache')->delete('product');
		
		return $product_id;
	}	

	
	public function editProduct($data) {
		reset($data['languages']); $first = key($data['languages']);
		$product_id = $data['product_id'];

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

		$sql = "UPDATE " . DB_PREFIX . "product
				SET price = " . (float)$data['product_price'] . ",
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
		
		// languages
		foreach ($data['languages'] as $language_id => $language) {
			if (strcmp(VERSION,'1.5.4') >= 0) {
				$sql = "UPDATE " . DB_PREFIX . "product_description
						SET name = '". $this->db->escape($language['product_name']) ."',
							description = '". $this->db->escape(htmlspecialchars(nl2br($language['product_description']), ENT_COMPAT)) ."',
							tag = '". $this->db->escape($language['product_tags']) ."'
						WHERE product_id = " . (int)$product_id . "
						AND language_id = " . (int)$language_id;
						
				$this->db->query($sql);
			} else {
				$sql = "UPDATE " . DB_PREFIX . "product_description
						SET name = '". $this->db->escape($language['product_name']) ."',
							description = '". $this->db->escape(htmlspecialchars(nl2br($language['product_description']), ENT_COMPAT)) ."'
						WHERE product_id = " . (int)$product_id . "
						AND language_id = " . (int)$language_id;
						
				$this->db->query($sql);
				
				$sql = "DELETE FROM " . DB_PREFIX . "product_tag
						WHERE product_id = " . (int)$product_id;
				$this->db->query($sql);
				
				if ($language['product_tags']) {
					$tags = explode(',', $language['product_tags']);
					foreach ($tags as $tag) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_tag SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', tag = '" . $this->db->escape(trim($tag)) . "'");
					}
				}
			}
			
			// multilang attributes
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
						
						$this->db->query("INSERT INTO " . DB_PREFIX . "ms_product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', attribute_value_id = '" . (int)$attr['value_id'] . "'");
					}
				}
			}			
		}
		
		$sql = "UPDATE " . DB_PREFIX . "ms_product
				SET product_status = " . (int)$data['product_status'] . ",
					product_approved = " . (int)$data['product_approved'] . "
				WHERE product_id = " . (int)$product_id; 
		
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

		// Images
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

		// downloads
		$old_downloads = $this->getProductDownloads($product_id);
		if (isset($data['product_downloads'])) {
			foreach ($data['product_downloads'] as $key => $dl) {
				if (!empty($dl['download_id'])) {
					if (!empty($dl['filename'])) {
						// update download #download_id:
						$newFile = $this->MsLoader->MsFile->moveDownload($dl['filename']);
						$fileMask = substr($newFile,0,strrpos($newFile,'.'));
						
						$this->db->query("UPDATE " . DB_PREFIX . "download SET remaining = 5, filename = '" . $this->db->escape($newFile) . "', mask = '" . $this->db->escape($fileMask) . "' WHERE download_id = '" . (int)$dl['download_id'] . "'");
						
						if (isset($data['push_downloads'])) {
							$this->db->query("UPDATE " . DB_PREFIX . "order_download SET remaining = 5, `filename` = '" . $this->db->escape($newFile) . "', mask = '" . $this->db->escape($fileMask) . "', name = '" . $this->db->escape($fileMask) . "' WHERE `filename` = '" . $this->db->escape($old_downloads[$dl['download_id']]['filename']) . "'");
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
					
					$this->db->query("INSERT INTO " . DB_PREFIX . "download SET remaining = 5, filename = '" . $this->db->escape($newFile) . "', mask = '" . $this->db->escape($fileMask) . "'");
					$download_id = $this->db->getLastId();
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
					
					foreach ($data['languages'] as $language_id => $language) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "download_description SET download_id = '" . (int)$download_id . "', name = '" . $this->db->escape($fileMask) . "', language_id = '" . (int)$language_id . "'");
					}
					
					if (isset($data['push_downloads'])) {
						$orders = $this->db->query("SELECT order_product_id, order_id FROM " . DB_PREFIX . "order_product WHERE product_id = '"  . (int)$product_id . "'");
						foreach ($orders->rows as $row) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "order_download SET order_id = '" . (int)$row['order_id'] . "', order_product_id = '" . (int)$row['order_product_id'] . "', remaining = 5, `filename` = '" . $this->db->escape($newFile) . "', mask = '" . $this->db->escape($fileMask) . "', name = '" . $this->db->escape($fileMask) . "'");
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


		// attributes
		if (isset($data['product_attributes'])) {
			foreach ($data['product_attributes'] as $attribute_id => $attr) {
				if (in_array($attr['attribute_type'], array(MsAttribute::TYPE_SELECT, MsAttribute::TYPE_RADIO, MsAttribute::TYPE_IMAGE))) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "ms_product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', attribute_value_id = '" . (int)$attr['value'] . "'");
				} else if ($attr['attribute_type'] == MsAttribute::TYPE_CHECKBOX) {
					foreach ($attr['values'] as $attribute_value_id) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "ms_product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', attribute_value_id = '" . (int)$attribute_value_id . "'");
					}
				} else if (in_array($attr['attribute_type'], array(MsAttribute::TYPE_TEXT, MsAttribute::TYPE_TEXTAREA, MsAttribute::TYPE_DATE, MsAttribute::TYPE_DATETIME, MsAttribute::TYPE_TIME))) {
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
								AND attribute_value_id = " . (int)$attr['value_id'];
						$this->db->query($sql);
					}
					
					$this->db->query("INSERT INTO " . DB_PREFIX . "ms_product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', attribute_value_id = '" . (int)$attr['value_id'] . "'");
				}
			}
		}
		
		// specials
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
		if (isset($data['product_specials'])) {
			foreach ($data['product_specials'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}		
		
		// discounts
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");
		if (isset($data['product_discounts'])) {
			foreach ($data['product_discounts'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
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
		
		$sql = "DELETE FROM " . DB_PREFIX . "ms_product
				WHERE product_id = " . (int)$product_id;
		
		$this->db->query($sql);

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
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_tag WHERE product_id='" . (int)$product_id. "'");
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
						p.product_id as 'product_id',
						mp.product_status as 'mp.product_status',
						p.status as enabled,
						p.image as thumbnail,
						p.shipping as shipping,
						p.quantity as quantity,
						group_concat(ptc.category_id separator ',') as category_id,
						mp.product_status
				FROM `" . DB_PREFIX . "product` p
				INNER JOIN `" . DB_PREFIX . "product_to_category` ptc
					ON p.product_id = ptc.product_id
				INNER JOIN `" . DB_PREFIX . "ms_product` mp
					ON ptc.product_id = mp.product_id
				WHERE p.product_id = " . (int)$product_id;
		$res = $this->db->query($sql);

		if (strcmp(VERSION,'1.5.4') >= 0) {
			$sql = "SELECT pd.*,
						   pd.description as 'pd.description'
					FROM " . DB_PREFIX . "product_description pd
					WHERE pd.product_id = " . (int)$product_id . "
					GROUP BY language_id";

		} else {
			$sql = "SELECT pd.*,
						   pd.description as 'pd.description'
						   group_concat(pt.tag separator ', ') as tag
					FROM " . DB_PREFIX . "product_description pd
					LEFT JOIN `" . DB_PREFIX . "product_tag` pt
						ON pd.product_id = pt.product_id
						AND pd.language_id = pt.language_id
					WHERE pd.product_id = " . (int)$product_id . "
					GROUP BY language_id";
		}
		


		$descriptions = $this->db->query($sql);
		$product_description_data = array();
		foreach ($descriptions->rows as $result) {
			$product_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description'],
				'tags'      => $result['tag']
				//'meta_keyword'     => $result['meta_keyword'],
				//'meta_description' => $result['meta_description']
			);
		}

		$res->row['languages'] = $product_description_data;
		return $res->row;
	}	
	
	public function getProducts($data, $sort = array()) {
		// todo validate order parameters
		$sql = "SELECT  p.product_id as 'product_id',
						p.image as 'p.image',
						p.price as 'p.price',
						pd.name as 'pd.name',
						ms.seller_id as 'seller_id',
						ms.nickname as 'ms.nickname',
						mp.product_status as 'mp.product_status',
						mp.product_approved as 'mp.product_approved',
						mp.number_sold as 'mp.number_sold',
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
				. " GROUP BY p.product_id"
				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
    			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);
		
		return $res->rows;
	}
	
	public function getStatus($product_id) {
		$sql = "SELECT mp.product_status AS status
				FROM `" . DB_PREFIX . "ms_product` mp
				WHERE product_id = " . (int)$product_id;
		
		$res = $this->db->query($sql);
		
		return $res->row['status'];
	}

	public function getStatusText($product_status) {
		switch($product_status) {
			case MsProduct::STATUS_ACTIVE:
				$status_text = $this->language->get('ms_status_published');
				break;
			case MsProduct::STATUS_INACTIVE:
				$status_text = $this->language->get('ms_status_notpublished');
				break;
			case MsProduct::STATUS_DISABLED:
				$status_text = $this->language->get('ms_status_disabled');
				break;
			case MsProduct::STATUS_DELETED:
				$status_text = $this->language->get('ms_status_deleted');
				break;
			default:
				$status_text = '';
				break;				
		}
		
		return $status_text;
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
		$sql = "SELECT SUM(op.quantity) AS quantity,
					   SUM(op.total + op.total * op.tax / 100) AS total,
					   SUM(seller_net_amt) AS seller_total
				FROM " . DB_PREFIX . "order_product op
				LEFT JOIN `" . DB_PREFIX . "order` o
					ON (op.order_id = o.order_id)
				LEFT JOIN `" . DB_PREFIX . "ms_order_product_data` mopd
					ON (op.order_id = mopd.order_id)
				WHERE op.product_id = " . (int)$product_id;
		
		$res = $this->db->query($sql);
		return $res->row;
	}
	
	public function changeSeller($product_id, $seller_id) {
		$sql = "UPDATE " . DB_PREFIX . "ms_product
				SET	seller_id =  " . (int)$seller_id . "
				WHERE product_id = " . (int)$product_id;
		$res = $this->db->query($sql);
		$this->registry->get('cache')->delete('product');
	}
}
?>
