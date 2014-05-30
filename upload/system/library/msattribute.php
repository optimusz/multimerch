<?php
class MsAttribute extends Model {
	const TYPE_CHECKBOX = 1;
	const TYPE_DATE = 2;
	const TYPE_DATETIME = 3;
	const TYPE_FILE = 4;
	const TYPE_IMAGE = 5;
	const TYPE_RADIO = 6;
	const TYPE_SELECT = 7;
	const TYPE_TEXT = 8;
	const TYPE_TEXTAREA = 9;
	const TYPE_TIME = 10;
	
	
	public function migrateAttributes() {
		// todo create a new attribute group and assign all attributes to it
		$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_group SET sort_order = 0");
		$attribute_group_id = $this->db->getLastId();
		$languages = $this->model_localisation_language->getLanguages();
		foreach ($languages as $code => $language) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_group_description SET attribute_group_id = '" . (int)$attribute_group_id . "', language_id = '" . (int)$language['language_id'] . "', name = 'Generic'");
		}
		
		foreach ($this->MsLoader->MsAttribute->getAttributes() as $attribute) {
			$attribute['attribute_description'] = $this->MsLoader->MsAttribute->getAttributeDescriptions($attribute['attribute_id']);
			$attribute['attribute_value'] = $this->MsLoader->MsAttribute->getAttributeValues($attribute['attribute_id']);
			foreach ($attribute['attribute_value'] as &$value) {
				$value['attribute_value_description'] = $this->MsLoader->MsAttribute->getAttributeValueDescriptions($value['attribute_value_id']);
			}

			$attribute['attribute_group_id'] = $attribute_group_id;

			// oc attribute
			$this->db->query("INSERT INTO " . DB_PREFIX . "attribute SET sort_order = '" . (int)$attribute['sort_order'] . "', attribute_group_id = " . (int)$attribute['attribute_group_id']);
			$oc_attribute_id = $this->db->getLastId();
			
			// attribute attribute
			$sql = "INSERT INTO " . DB_PREFIX . "ms_attribute_attribute
			SET ms_attribute_id = " . (int)$attribute['attribute_id'] . ",
			oc_attribute_id = " . (int)$oc_attribute_id;
			$this->db->query($sql);
			
			foreach ($attribute['attribute_description'] as $language_id => $value) {
				// oc attribute description
				$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_description SET attribute_id = '" . (int)$oc_attribute_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			}
		}
	}
	
	public function createAttribute($data) {
		$sql = "INSERT INTO " . DB_PREFIX . "ms_attribute
				SET attribute_type = " . (int)$data['attribute_type'] . ",
					sort_order = " . (int)$data['sort_order'] . ",
					number = " . (isset($data['number']) && $data['text_type'] == 'number' ? 1 : 0) . ",
					multilang = " . (isset($data['text_type']) && $data['text_type'] == 'multilang' ? 1 : 0) . ",
					tab_display = " . (isset($data['tab_display']) && $data['tab_display'] == '1' ? 1 : 0) . ",
					enabled = " . (isset($data['enabled']) ? 1 : 0) . ",
					required = " . (isset($data['required']) ? 1 : 0);
		$this->db->query($sql);
		$attribute_id = $this->db->getLastId();

		// oc attribute
		$this->db->query("INSERT INTO " . DB_PREFIX . "attribute SET sort_order = '" . (isset($data['sort_order']) ? (int)$data['sort_order'] : 0) . "', attribute_group_id = " . (int)$data['attribute_group_id']);
		$oc_attribute_id = $this->db->getLastId();

		// attribute attribute
		$sql = "INSERT INTO " . DB_PREFIX . "ms_attribute_attribute
				SET ms_attribute_id = " . (int)$attribute_id . ",
					oc_attribute_id = " . (int)$oc_attribute_id;
		$this->db->query($sql);

		foreach ($data['attribute_description'] as $language_id => $value) {
			$sql = "INSERT INTO " . DB_PREFIX . "ms_attribute_description
					SET attribute_id = " . (int)$attribute_id . ",
						language_id = " . $language_id . ",
						name = '" . $this->db->escape($value['name']) . "',
						description = '" . (isset($value['description']) ? $this->db->escape($value['description']) : '') . "'";
			$this->db->query($sql);
			
			// oc attribute description
			$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_description SET attribute_id = '" . (int)$oc_attribute_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}

		if (isset($data['attribute_value'])) {
			foreach ($data['attribute_value'] as $attribute_value) {
				$sql = "INSERT INTO " . DB_PREFIX . "ms_attribute_value
						SET attribute_id = " . (int)$attribute_id . ",
							image = '" . $this->db->escape(html_entity_decode($attribute_value['image'], ENT_QUOTES, 'UTF-8')) . "',
							sort_order = '" . (isset($attribute_value['sort_order']) ? (int)$attribute_value['sort_order'] : 0) . "'";
				
				$this->db->query($sql);
				$attribute_value_id = $this->db->getLastId();

				foreach ($attribute_value['attribute_value_description'] as $language_id => $attribute_value_description) {
					$sql = "INSERT INTO " . DB_PREFIX . "ms_attribute_value_description
							SET attribute_id = " . (int)$attribute_id . ",
								attribute_value_id = " . (int)$attribute_value_id . ",
								language_id = " . $language_id . ",
								name = '" . $this->db->escape($attribute_value_description['name']) . "'";
					$this->db->query($sql);
				}
			}
		}
		
		return $attribute_id;
	}
	
	public function updateAttribute($attribute_id, $data) {
		$query = $this->db->query("SELECT oc_attribute_id FROM " . DB_PREFIX . "ms_attribute_attribute WHERE ms_attribute_id = '" . (int)$attribute_id . "' AND oc_attribute_id IS NOT NULL LIMIT 1");
		$oc_attribute_id = $query->row['oc_attribute_id'];
		
		$sql = "UPDATE " . DB_PREFIX . "ms_attribute
				SET attribute_type = " . (int)$data['attribute_type'] . ",
					sort_order = " . (int)$data['sort_order'] . ",
					number = " . (isset($data['text_type']) && $data['text_type'] == 'number' ? 1 : 0) . ",
					multilang = " . (isset($data['text_type']) && $data['text_type'] == 'multilang' ? 1 : 0) . ",
					tab_display = " . (isset($data['tab_display']) && $data['tab_display'] == '1' ? 1 : 0) . ",
					enabled = " . (isset($data['enabled']) ? 1 : 0) . ",
					required = " . (isset($data['required']) ? 1 : 0) . "
				WHERE attribute_id = " . (int)$attribute_id;
		$this->db->query($sql);

		// oc attribute
		$this->db->query("UPDATE " . DB_PREFIX . "attribute SET sort_order = '" . (int)$data['sort_order'] . "', attribute_group_id = " . (int)$data['attribute_group_id'] . " WHERE attribute_id = " . (int)$oc_attribute_id);

		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_attribute_description WHERE attribute_id = " . (int)$attribute_id);
		$this->db->query("DELETE FROM " . DB_PREFIX . "attribute_description WHERE attribute_id = " . (int)$oc_attribute_id);
		
		foreach ($data['attribute_description'] as $language_id => $value) {
			$sql = "INSERT INTO " . DB_PREFIX . "ms_attribute_description
					SET attribute_id = " . (int)$attribute_id . ",
						language_id = " . $language_id . ",
						name = '" . $this->db->escape($value['name']) . "',
						description = '" . (isset($value['description']) ? $this->db->escape($value['description']) : '') . "'";
			$this->db->query($sql);
			
			// oc attributes
			$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_description SET attribute_id = '" . (int)$oc_attribute_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}

		// keep text attribute values intact
		if (in_array($data['attribute_type'], array(MsAttribute::TYPE_SELECT, MsAttribute::TYPE_RADIO, MsAttribute::TYPE_IMAGE, MsAttribute::TYPE_CHECKBOX, MsAttribute::TYPE_DATE, MsAttribute::TYPE_DATETIME))) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "ms_attribute_value WHERE attribute_id = '" . (int)$attribute_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "ms_attribute_value_description WHERE attribute_id = '" . (int)$attribute_id . "'");
			
			if (isset($data['attribute_value'])) {
				foreach ($data['attribute_value'] as $attribute_value) {
					if (isset($attribute_value['attribute_value_id'])) {
						$sql ="INSERT INTO " . DB_PREFIX . "ms_attribute_value 
								SET attribute_value_id = '" . (int)$attribute_value['attribute_value_id'] . "', 
									attribute_id = '" . (int)$attribute_id . "', 
									image = '" . $this->db->escape(html_entity_decode($attribute_value['image'], ENT_QUOTES, 'UTF-8')) . "', 
									sort_order = '" . (isset($attribute_value['sort_order']) ? (int)$attribute_value['sort_order'] : 0) . "'";
					} else {
						$sql = "INSERT INTO " . DB_PREFIX . "ms_attribute_value 
								SET attribute_id = '" . (int)$attribute_id . "', 
									image = '" . $this->db->escape(html_entity_decode($attribute_value['image'], ENT_QUOTES, 'UTF-8')) . "', 
									sort_order = '" . (isset($attribute_value['sort_order']) ? (int)$attribute_value['sort_order'] : 0) . "'";
					}
					$this->db->query($sql);
					$attribute_value_id = $this->db->getLastId();
					
					foreach ($attribute_value['attribute_value_description'] as $language_id => $attribute_value_description) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "ms_attribute_value_description SET attribute_value_id = '" . (int)$attribute_value_id . "', language_id = '" . (int)$language_id . "', attribute_id = '" . (int)$attribute_id . "', name = '" . $this->db->escape($attribute_value_description['name']) . "'");
					}
				}
			}
		} else {
			if ($data['text_type'] != 'multilang') {
				$this->db->query("DELETE FROM " . DB_PREFIX . "ms_attribute_value_description WHERE attribute_id = " . (int)$attribute_id . " AND language_id != " . $this->MsLoader->MsHelper->getLanguageId($this->config->get('config_language')));
				$this->db->query("UPDATE " . DB_PREFIX . "ms_attribute_value_description SET language_id = 0 WHERE attribute_id = " . (int)$attribute_id);
			} else if ($data['text_type'] == 'multilang') {
				$this->db->query("UPDATE " . DB_PREFIX . "ms_attribute_value_description SET language_id = " . $this->MsLoader->MsHelper->getLanguageId($this->config->get('config_language')) . " WHERE attribute_id = " . (int)$attribute_id . " AND language_id = 0");
			}
		}
	}
	
	public function deleteAttribute($attribute_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_attribute WHERE attribute_id = '" . (int)$attribute_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_attribute_description WHERE attribute_id = '" . (int)$attribute_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "attribute WHERE attribute_group_id = $attribute_id");
		$this->db->query("DELETE FROM " . DB_PREFIX . "attribute_description WHERE attribute_id IN (SELECT oc_attribute_id FROM " . DB_PREFIX . "ms_attribute_attribute WHERE ms_attribute_id = $attribute_id AND oc_attribute_id IS NOT NULL)");
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_attribute_value WHERE attribute_id = '" . (int)$attribute_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_attribute_value_description WHERE attribute_id = '" . (int)$attribute_id . "'");
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_attribute_attribute WHERE ms_attribute_id = $attribute_id");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_product_attribute WHERE attribute_id = '" . (int)$attribute_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE attribute_id = '" . (int)$attribute_id . "'");
	}	
	
	public function getAttribute($attribute_id) {
		$sql = "SELECT *,
						ma.attribute_type as 'ma.attribute_type',
						ma.enabled as 'ma.enabled',
						(SELECT attribute_group_id FROM " . DB_PREFIX . "attribute WHERE attribute_id = (SELECT oc_attribute_id FROM  " . DB_PREFIX . "ms_attribute_attribute WHERE ms_attribute_id = " . (int)$attribute_id . ")) as attribute_group_id
				FROM " . DB_PREFIX . "ms_attribute ma
				LEFT JOIN " . DB_PREFIX . "ms_attribute_description mad
					ON (ma.attribute_id = mad.attribute_id)
				WHERE ma.attribute_id = " . (int)$attribute_id . "
				AND mad.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		$res = $this->db->query($sql);
		return $res->row;
	}
		
	public function getAttributes($data = array(), $sort = array()) {
		$filters = '';
		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				$filters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
			}
		}

		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					*,
					ma.attribute_type as 'ma.attribute_type',
					ma.sort_order as 'ma.sort_order',
					ma.enabled as 'ma.enabled',
					mad.name as 'mad.name'
				FROM " . DB_PREFIX . "ms_attribute ma
				LEFT JOIN " . DB_PREFIX . "ms_attribute_description mad
					ON (ma.attribute_id = mad.attribute_id)
				WHERE 1 = 1 "
				. (isset($data['language_id']) ? " AND language_id =  " .  (int)$data['language_id'] : "AND language_id =  " .  (int)$this->config->get('config_language_id'))
				. (isset($data['multilang']) ? " AND multilang =  " .  (int)$data['multilang'] : '')
				. (isset($data['enabled']) ? " AND enabled =  " .  (int)$data['enabled'] : '')
				. $filters
				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
				. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];

		return $res->rows;
	}
		
	public function getAttributeDescriptions($attribute_id) {
		$attribute_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ms_attribute_description WHERE attribute_id = '" . (int)$attribute_id . "'");
				
		foreach ($query->rows as $result) {
			$attribute_data[$result['language_id']] = array(
				'name' => $result['name'],
				'description' => $result['description']
			);
		}
		
		return $attribute_data;
	}
	
	public function getAttributeValue($attribute_value_id) {
		$sql = "SELECT *
				FROM " . DB_PREFIX . "ms_attribute_value mav
				LEFT JOIN " . DB_PREFIX . "ms_attribute_value_description mavd
					ON (mav.attribute_value_id = mavd.attribute_value_id)
				WHERE mav.attribute_value_id = " . (int)$attribute_value_id
				. (isset($data['language_id']) ? " AND mavd.language_id =  " .  (int)$data['language_id'] : " AND mavd.language_id =  " .  (int)$this->config->get('config_language_id'));

		$res = $this->db->query($sql);
		
		return $query->row;
	}
	
	public function getAttributeValues($attribute_id) {
		$attribute_value_data = array();
		$sql = "SELECT *
				FROM " . DB_PREFIX . "ms_attribute_value mav
				LEFT JOIN " . DB_PREFIX . "ms_attribute_value_description mavd
					ON (mav.attribute_value_id = mavd.attribute_value_id)
				WHERE mav.attribute_id = " . (int)$attribute_id
				. (isset($data['language_id']) ? " AND mavd.language_id =  " .  (int)$data['language_id'] : " AND mavd.language_id =  " .  (int)$this->config->get('config_language_id')) .  "
				ORDER BY mav.sort_order ASC";
		$attribute_value_query = $this->db->query($sql);
		
		foreach ($attribute_value_query->rows as $attribute_value) {
			$attribute_value_data[] = array(
				'attribute_value_id' => $attribute_value['attribute_value_id'],
				'name'            => $attribute_value['name'],
				'image'           => $attribute_value['image'],
				'sort_order'      => $attribute_value['sort_order']
			);
		}
		
		return $attribute_value_data;
	}
	
	public function getAttributeValueDescriptions($attribute_value_id) {
		$attribute_value_data = array();
		
		$sql = "SELECT *
				FROM " . DB_PREFIX . "ms_attribute_value mav
				LEFT JOIN " . DB_PREFIX . "ms_attribute_value_description mavd
					ON (mav.attribute_value_id = mavd.attribute_value_id)
				WHERE mav.attribute_value_id = " . (int)$attribute_value_id;
				
		$attribute_value_query = $this->db->query($sql);		
				
		foreach ($attribute_value_query->rows as $attribute_value) {
			$attribute_value_data[$attribute_value['language_id']] = array('name' => $attribute_value['name']);
		}
		
		return $attribute_value_data;
	}	
	
	public function getTotalAttributes() {
		$sql = "SELECT COUNT(*) as 'total'
				FROM " . DB_PREFIX . "ms_attribute";
				
		$res = $this->db->query($sql);

		return $res->row['total'];
	}
	
	public function getProductAttributes($product_id, $data = array()) {
		$attribute_data = array();
		
		$sql = "SELECT *,
						mad.name as attribute_name,
						mavd.name as attribute_value_name,
						ma.attribute_type as attribute_type,
						mav.image as image
				FROM " . DB_PREFIX . "ms_product_attribute mpa
				LEFT JOIN `" . DB_PREFIX . "ms_attribute` ma
					ON (mpa.attribute_id = ma.attribute_id)
				LEFT JOIN " . DB_PREFIX . "ms_attribute_description mad
					ON (mpa.attribute_id = mad.attribute_id)
				LEFT JOIN " . DB_PREFIX . "ms_attribute_value mav
					ON (mpa.attribute_value_id = mav.attribute_value_id)
				LEFT JOIN " . DB_PREFIX . "ms_attribute_value_description mavd
					ON (mpa.attribute_value_id = mavd.attribute_value_id)
				WHERE mpa.product_id = '".(int)$product_id."'"
				. (isset($data['mad.language_id']) ? " AND mad.language_id =  " .  (int)$data['mad.language_id'] : " AND mad.language_id =  " .  (int)$this->config->get('config_language_id'))
				. (isset($data['mavd.language_id']) ? " AND mavd.language_id =  " .  (int)$data['mavd.language_id'] : " AND mavd.language_id =  " .  (int)$this->config->get('config_language_id'))
				. (isset($data['multilang']) ? " AND multilang =  " .  (int)$data['multilang'] : '')
				. (isset($data['enabled']) ? " AND enabled =  " .  (int)$data['enabled'] : '')
				. (isset($data['attribute_type']) ? " AND attribute_type IN  (" .  $this->db->escape(implode(',', $data['attribute_type'])) . ")" : '') . "

				ORDER BY ma.sort_order ASC";

		$attributes = $this->db->query($sql);

		foreach ($attributes->rows as $attribute) {
			$attribute_data[$attribute['attribute_id']]['attribute_id'] = $attribute['attribute_id'];
			$attribute_data[$attribute['attribute_id']]['name'] = $attribute['attribute_name'];
			$attribute_data[$attribute['attribute_id']]['values'][$attribute['attribute_value_id']] = $attribute['attribute_value_name'];
			$attribute_data[$attribute['attribute_id']]['tab_display'] = $attribute['tab_display'];
			$attribute_data[$attribute['attribute_id']]['attribute_type'] = $attribute['attribute_type'];
			$attribute_data[$attribute['attribute_id']]['image'] = $attribute['image'];
		}
		
		return $attribute_data;
	}
	
	public function getProductAttributeValues($product_id, $data = array()) {
		$attribute_data = array();
		$multilang_data = array();
		
		$sql = "SELECT *,
						mavd.name as attribute_value_name
				FROM " . DB_PREFIX . "ms_product_attribute mpa
				LEFT JOIN `" . DB_PREFIX . "ms_attribute` ma
					ON (mpa.attribute_id = ma.attribute_id)
				LEFT JOIN " . DB_PREFIX . "ms_attribute_value_description mavd
					ON (mpa.attribute_value_id = mavd.attribute_value_id)
				WHERE mpa.product_id = '".(int)$product_id."'"
				. (isset($data['multilang']) ? " AND multilang =  " .  (int)$data['multilang'] : '')
				. (isset($data['attribute_type']) ? " AND attribute_type IN  (" .  $this->db->escape(implode(',', $data['attribute_type'])) . ")" : '') . "
				ORDER BY ma.sort_order ASC";

		$attributes = $this->db->query($sql);
		
		
		foreach ($attributes->rows as $attribute) {
			if ($attribute['multilang']) {
				$multilang_data[$attribute['attribute_id']][$attribute['language_id']] = array(
					'value' => $attribute['attribute_value_name'],
					'value_id' => $attribute['attribute_value_id']
				);
			} else {
				$attribute_data[$attribute['attribute_id']][$attribute['attribute_value_id']][$attribute['language_id']] = $attribute['attribute_value_name'];
			}
		}
		
		return array($attribute_data, $multilang_data);
	}	
	
	
	
	public function getTypeText($attribute_type) {
		switch($attribute_type) {
			case MsAttribute::TYPE_CHECKBOX:
				$type_text = $this->language->get('ms_type_checkbox');
				break;
							
			case MsAttribute::TYPE_DATE:
				$type_text = $this->language->get('ms_type_date');
				break;
							
			case MsAttribute::TYPE_DATETIME:
				$type_text = $this->language->get('ms_type_datetime');
				break;
				
			case MsAttribute::TYPE_FILE:
				$type_text = $this->language->get('ms_type_file');
				break;
				
			case MsAttribute::TYPE_IMAGE:
				$type_text = $this->language->get('ms_type_image');
				break;
				
			case MsAttribute::TYPE_RADIO:
				$type_text = $this->language->get('ms_type_radio');
				break;
				
			case MsAttribute::TYPE_SELECT:
				$type_text = $this->language->get('ms_type_select');
				break;
				
			case MsAttribute::TYPE_TEXT:
				$type_text = $this->language->get('ms_type_text');
				break;
				
			case MsAttribute::TYPE_TEXTAREA:
				$type_text = $this->language->get('ms_type_textarea');
				break;
				
			case MsAttribute::TYPE_TIME:
				$type_text = $this->language->get('ms_type_time');
				break;
				
			default:
				$type_text = '';
				break;				
		}
		
		return $type_text;
	}	
}
?>