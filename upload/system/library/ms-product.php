<?php
class MsProduct {
	const MS_PRODUCT_STATUS_APPROVED = 1;
	const MS_PRODUCT_STATUS_PENDING = 2;
	const MS_PRODUCT_STATUS_DECLINED = 3;
	const MS_PRODUCT_STATUS_DRAFT = 4;
	const MS_PRODUCT_STATUS_SELLER_DELETED = 5;
	
	const MS_PRODUCT_VALIDATION_NONE = 1;
	const MS_PRODUCT_VALIDATION_APPROVAL = 2;
	
	private $errors;
		
  	public function __construct($registry) {
		require_once(DIR_SYSTEM . 'library/ms-file.php');
		$this->msFile = new MsFile($registry);
  		
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
		$this->load = $registry->get('load');
		$this->language = $registry->get('language');
		$this->cache = $registry->get('cache');		
		$this->errors = array();
		$this->registry = $registry;
	}
	
	public function getProductStatusArray() {
		$this->load->language('module/multiseller');
		return array(
			MsProduct::MS_PRODUCT_STATUS_DRAFT => $this->language->get('ms_product_review_status_draft'),
			MsProduct::MS_PRODUCT_STATUS_PENDING => $this->language->get('ms_product_review_status_pending'),
			MsProduct::MS_PRODUCT_STATUS_APPROVED => $this->language->get('ms_product_review_status_approved'),
			MsProduct::MS_PRODUCT_STATUS_DECLINED => $this->language->get('ms_product_review_status_declined'),
			MsProduct::MS_PRODUCT_STATUS_SELLER_DELETED => $this->language->get('ms_product_status_seller_deleted'),
		);		
	}	
	
	private function _getDepth($a, $eid) {
		foreach ($a as $key => $val) {
			if ($val['category_id'] == $eid) {
				if ($val['parent_id'] == 0) {
					return 0;
				} else {
					return 1+$this->_getDepth($a, $val['parent_id']);
				}
			}
		}
	}
	
	public function getProducts($sort, $nodrafts = false) {
		$sql = "SELECT  pr.product_id as 'prd.product_id',
						pr.image as 'prd.image',
						pd.name as 'prd.name',
						ms.nickname as 'sel.nickname',
						mp.review_status_id as 'prd.status_id',
						pr.date_added as 'prd.date_created',
						pr.date_modified  as 'prd.date_modified'
				FROM " . DB_PREFIX . "product pr
				INNER JOIN " . DB_PREFIX . "product_description pd
					USING(product_id)
				INNER JOIN " . DB_PREFIX . "ms_product mp
					USING(product_id)
				INNER JOIN " . DB_PREFIX . "ms_seller ms
					ON (mp.seller_id = ms.seller_id)
				WHERE pd.language_id = " . (int)$this->config->get('config_language_id')
				. ($nodrafts ? " AND (ISNULL(mp.review_status_id) OR mp.review_status_id != " . (int)self::MS_PRODUCT_STATUS_DRAFT . ")" : '') . "
    			ORDER BY {$sort['order_by']} {$sort['order_way']}" 
    			. ($sort['limit'] ? " LIMIT ".(int)(($sort['page'] - 1) * $sort['limit']).', '.(int)($sort['limit']) : '');
        
		$res = $this->db->query($sql);
		
		$product_statuses = $this->getProductStatusArray();
		
		foreach ($res->rows as &$row) {
			if (isset($product_statuses[$row['prd.status_id']]))
				$row['prd.status'] = $product_statuses[$row['prd.status_id']];
			else
				$row['prd.status'] = '';
		}
		
		return $res->rows;
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
					'name'        => str_repeat('&nbsp;&nbsp;',$this->_getDepth($query->rows, $result['category_id'])) . $result['name'],
					//'status'  	  => $result['status'],
					//'sort_order'  => $result['sort_order'],
				);
			
				$category_data = array_merge($category_data, $this->getCategories($result['category_id']));
			}
	
			//$this->cache->set('category.' . (int)$this->config->get('config_language_id') . '.' . (int)$parent_id, $category_data);
		}
		
		return $category_data;
	}	
	
	private function _getPath($category_id) {
		$query = $this->db->query("SELECT name, parent_id FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name ASC");
		
		if ($query->row['parent_id']) {
			return $this->getPath($query->row['parent_id'], $this->config->get('config_language_id')) . $this->language->get('text_separator') . $query->row['name'];
		} else {
			return $query->row['name'];
		}
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
	
	public function getStatsForProduct($product_id) {
		$sql = "SELECT 	p.date_added,
						mp.seller_id,
						mp.number_sold as sales,
						ms.nickname,
						ms.country_id,
						ms.avatar_path
				FROM `" . DB_PREFIX . "product` p
				INNER JOIN `" . DB_PREFIX . "ms_product` mp
					ON p.product_id = mp.product_id
				INNER JOIN `" . DB_PREFIX . "ms_seller` ms
					ON mp.seller_id = ms.seller_id
				WHERE p.product_id = " . (int)$product_id; 

		$res = $this->db->query($sql);

		return $res->row;		
	}	
	
	public function getStatus($product_id) {
		$sql = "SELECT	review_status_id as 'status'
				FROM `" . DB_PREFIX . "ms_product`
				WHERE product_id = " . (int)$product_id;

		$res = $this->db->query($sql);		
		
		return ($res->row['status']);
	}
	
	public function getProduct($product_id) {
		$sql = "SELECT 	p.price,
						p.product_id,
						p.status as enabled,
						p.image as thumbnail,
						p.shipping as shipping,
						p.quantity as quantity,
						group_concat(ptc.category_id separator ',') as category_id,
						mp.review_status_id
				FROM `" . DB_PREFIX . "product` p
				INNER JOIN `" . DB_PREFIX . "product_to_category` ptc
					ON p.product_id = ptc.product_id
				INNER JOIN `" . DB_PREFIX . "ms_product` mp
					ON ptc.product_id = mp.product_id
				WHERE p.product_id = " . (int)$product_id;
		$res = $this->db->query($sql);

		if (strcmp(VERSION,'1.5.4') >= 0) {
			$sql = "SELECT pd.*
					FROM " . DB_PREFIX . "product_description pd
					WHERE pd.product_id = " . (int)$product_id . "
					GROUP BY language_id";

		} else {
			$sql = "SELECT pd.*,
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
		
	public function getProductImages($product_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'";
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

	public function getOptions($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "option` o LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE od.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		
		if (isset($data['option_ids'])) {
			if (!empty($data['option_ids']))
				$sql .= " AND o.option_id IN (" . $data['option_ids'] . ")";
			else
				$sql .= " AND o.option_id IN (NULL)";
		}
		
		$query = $this->db->query($sql);

		return $query->rows;
	}
	
	public function getOptionValues($option_id) {
		$option_value_data = array();
		
		$option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value ov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE ov.option_id = '" . (int)$option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ov.sort_order ASC");
				
		foreach ($option_value_query->rows as $option_value) {
			$option_value_data[] = array(
				'option_value_id' => $option_value['option_value_id'],
				'name'            => $option_value['name'],
				'image'           => $option_value['image'],
				'sort_order'      => $option_value['sort_order']
			);
		}
		
		return $option_value_data;
	}
	
	public function getProductAttributes($product_id) {
		$attribute_data = array();
		
		$attributes = $this->db->query("SELECT *,od.name as option_name, ovd.name as option_value_name FROM " . DB_PREFIX . "ms_product_attribute mpa LEFT JOIN `" . DB_PREFIX . "option` o ON (mpa.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (mpa.option_id = od.option_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (mpa.option_value_id = ovd.option_value_id) WHERE mpa.product_id = '".(int)$product_id."' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.sort_order ASC");

		foreach ($attributes->rows as $attribute) {
			$attribute_data[$attribute['option_id']]['name'] = $attribute['option_name'];
			$attribute_data[$attribute['option_id']]['values'][$attribute['option_value_id']] = $attribute['option_value_name'];
		}
		
		return $attribute_data;
	}	
	
	public function getProductThumbnail($product_id) {
		$query = $this->db->query("SELECT image FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		
		return $query->row;
	}		
		
	public function getTotalProducts($nodrafts = false, $hasSeller = true) {
		$sql = "SELECT COUNT(*) as total FROM " . DB_PREFIX . "product "
				. ($hasSeller ? "INNER JOIN " : "LEFT JOIN ") . DB_PREFIX . "ms_product mp
					USING(product_id) "
				. ($hasSeller ? "INNER JOIN " : "LEFT JOIN ") . DB_PREFIX . "ms_seller ms
					ON (mp.seller_id = ms.seller_id)"
				. ($nodrafts ? " WHERE (ISNULL(mp.review_status_id) OR mp.review_status_id != " . (int)self::MS_PRODUCT_STATUS_DRAFT . ")" : '');

		$res = $this->db->query($sql);
		return $res->row['total'];
	}
	
	public function hideProduct($product_id) {
		$sql = "UPDATE " . DB_PREFIX . "ms_product
				SET review_status_id = " . self::MS_PRODUCT_STATUS_SELLER_DELETED . "
				WHERE product_id = " . (int)$product_id;
		$res = $this->db->query($sql);
		
		$sql = "UPDATE " . DB_PREFIX . "product
				SET status = 0 WHERE product_id = " . (int)$product_id;
				
		$res = $this->db->query($sql);
	}
	
	public function disableProduct($product_id) {
		$sql = "UPDATE " . DB_PREFIX . "ms_product
				SET review_status_id = " . self::MS_PRODUCT_STATUS_DECLINED . "
				WHERE product_id = " . (int)$product_id;
		$res = $this->db->query($sql);

		$sql = "UPDATE " . DB_PREFIX . "product
				SET status = 0 WHERE product_id = " . (int)$product_id;
				
		$res = $this->db->query($sql);
	}
	
	public function enableProduct($product_id) {
		$sql = "UPDATE " . DB_PREFIX . "ms_product
				SET review_status_id = " . self::MS_PRODUCT_STATUS_APPROVED . "
				WHERE product_id = " . (int)$product_id;
		$res = $this->db->query($sql);

		$sql = "UPDATE " . DB_PREFIX . "product
				SET status = 1 WHERE product_id = " . (int)$product_id;
				
		$res = $this->db->query($sql);		
	}
	
	public function saveProduct($data) {
		reset($data['languages']); $first = key($data['languages']);
		$store_id = $this->config->get('config_store_id');

		if (isset($data['product_thumbnail'])) {
			$thumbnail = $this->msFile->moveImage($data['product_thumbnail']);
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
		
		if ($data['keyword']) {
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
		}
		
		$sql = "INSERT INTO " . DB_PREFIX . "ms_product
				SET product_id = " . (int)$product_id . ",
					seller_id = " . (int)$this->registry->get('customer')->getId() . ",
					review_status_id = " . (int)$data['review_status_id'];
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
				$newImagePath = $this->msFile->moveImage($img);
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape(html_entity_decode($newImagePath, ENT_QUOTES, 'UTF-8')) . "', sort_order = '" . (int)$key . "'");
			}
		}
		
		if (isset($data['product_downloads'])) {
			foreach ($data['product_downloads'] as $key => $dl) {
				$newFile = $this->msFile->moveDownload($dl);
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
			foreach ($data['product_attributes'] as $option_id => $attr) {
				if ($attr['type'] == 'select' || $attr['type'] == 'radio') {
					$this->db->query("INSERT INTO " . DB_PREFIX . "ms_product_attribute SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$option_id . "', option_value_id = '" . (int)$attr['value'] . "'");
				} else if ($attr['type'] == 'checkbox') {
					foreach ($attr['values'] as $option_value_id) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "ms_product_attribute SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$option_id . "', option_value_id = '" . (int)$option_value_id . "'");
					}
				}
			}
		}

		$this->registry->get('cache')->delete('product');
		
		return $product_id;
	}	

	
	public function editProduct($data) {
		reset($data['languages']); $first = key($data['languages']);
		$product_id = $data['product_id'];

		$old_thumbnail = $this->getProductThumbnail($product_id);
		if (!isset($data['product_thumbnail']) || ($old_thumbnail['image'] != $data['product_thumbnail'])) {
			$this->msFile->deleteImage($old_thumbnail['image']);
		}
		
		if (isset($data['product_thumbnail'])) {
			if ($old_thumbnail['image'] != $data['product_thumbnail']) {			
				$thumbnail = $this->msFile->moveImage($data['product_thumbnail']);
			} else {
				$thumbnail = $old_thumbnail['image'];
			}
		} else {
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
		}		
		
		$sql = "UPDATE " . DB_PREFIX . "ms_product
				SET review_status_id = " . (int)$data['review_status_id'] . "
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

		// images		
		$old_images = $this->getProductImages($product_id);
		if (isset($data['product_images'])) {
			foreach($old_images as $k => $old_image) {
				$key = array_search($old_image['image'], $data['product_images']);
				if ($key !== FALSE) {
					unset($old_images[$k]);
					unset($data['product_images'][$key]);
				}
			}
			
			foreach ($data['product_images'] as $key => $product_image) {
				$newImagePath = $this->msFile->moveImage($product_image);
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape(html_entity_decode($newImagePath, ENT_QUOTES, 'UTF-8')) . "', sort_order = '" . (int)$key . "'");
			}
		}		

		foreach($old_images as $old_image) {
			if ($old_image['image'] != $thumbnail) {
				$this->msFile->deleteImage($old_image['image']);
			}
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' AND product_image_id = '" . (int)$old_image['product_image_id'] . "'");
		}


		// downloads
		$old_downloads = $this->getProductDownloads($product_id);
		if (isset($data['product_downloads'])) {
			foreach ($data['product_downloads'] as $key => $dl) {
				if (!empty($dl['download_id'])) {
					if (!empty($dl['filename'])) {
						var_dump('updating ' . $dl['download_id'] . ' with ' . $dl['filename']);
						// update download #download_id:
						$newFile = $this->msFile->moveDownload($dl['filename']);
						$fileMask = substr($newFile,0,strrpos($newFile,'.'));
						
						$this->db->query("UPDATE " . DB_PREFIX . "download SET remaining = 5, filename = '" . $this->db->escape($newFile) . "', mask = '" . $this->db->escape($fileMask) . "' WHERE download_id = '" . (int)$dl['download_id'] . "'");
						
			        	if (isset($data['push_downloads'])) {
			        		var_dump('pushing download ' . $dl['download_id']);
			      			$this->db->query("UPDATE " . DB_PREFIX . "order_download SET remaining = 5, `filename` = '" . $this->db->escape($newFile) . "', mask = '" . $this->db->escape($fileMask) . "', name = '" . $this->db->escape($fileMask) . "' WHERE `filename` = '" . $this->db->escape($old_downloads[$dl['download_id']]['filename']) . "'");
			      		}
						
						foreach ($data['languages'] as $language_id => $language) {
							$this->db->query("UPDATE " . DB_PREFIX . "download_description SET name = '" . $this->db->escape($fileMask) . "' WHERE download_id = '" . (int)$dl['download_id'] . "' AND language_id = '" . (int)$language_id . "'");
						}						
						
						$this->msFile->deleteDownload($old_downloads[$dl['download_id']]['filename']);
					} else {
						// do nothing
					}
					
					// don't remove the download
					unset($old_downloads[$dl['download_id']]);
				} else if (!empty($dl['filename'])) {
					var_dump('adding ' . $dl['filename']);
					// add new download
					$newFile = $this->msFile->moveDownload($dl['filename']);
					$fileMask = substr($newFile,0,strrpos($newFile,'.'));					
					
					$this->db->query("INSERT INTO " . DB_PREFIX . "download SET remaining = 5, filename = '" . $this->db->escape($newFile) . "', mask = '" . $this->db->escape($fileMask) . "'");
					$download_id = $this->db->getLastId();
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
					
					foreach ($data['languages'] as $language_id => $language) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "download_description SET download_id = '" . (int)$download_id . "', name = '" . $this->db->escape($fileMask) . "', language_id = '" . (int)$language_id . "'");
					}
					
		        	if (isset($data['push_downloads'])) {
		        		$orders = $this->db->query("SELECT order_product_id, order_id FROM " . DB_PREFIX . "order_product WHERE product_id = '"  . (int)$product_id . "'");
		        		var_dump($orders);
		        		var_dump("SELECT order_product_id, order_id FROM " . DB_PREFIX . "order_product WHERE product_id = '"  . (int)$product_id . "'");
		        		foreach ($orders->rows as $row) {
							//var_dump('pushing download ' . $newFile . ' for ' . $row['order_product_id']);
							//var_dump("INSERT INTO " . DB_PREFIX . "order_download SET order_id = '" . (int)$row['order_id'] . "', order_product_id = '" . (int)$row['order_product_id'] . "', remaining = 5, `filename` = '" . $this->db->escape($newFile) . "', mask = '" . $this->db->escape($fileMask) . "', name = '" . $this->db->escape($fileMask) . "'");
		      				$this->db->query("INSERT INTO " . DB_PREFIX . "order_download SET order_id = '" . (int)$row['order_id'] . "', order_product_id = '" . (int)$row['order_product_id'] . "', remaining = 5, `filename` = '" . $this->db->escape($newFile) . "', mask = '" . $this->db->escape($fileMask) . "', name = '" . $this->db->escape($fileMask) . "'");
		        		}
		      		}					
				}
			}
		}

		if (!empty($old_downloads)) {
			foreach($old_downloads as $old_download) {
				var_dump('deleting ' . $old_download['filename']);
				$this->db->query("DELETE FROM " . DB_PREFIX . "download WHERE download_id ='" . (int)$old_download['download_id'] . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "download_description WHERE download_id ='" . (int)$old_download['download_id'] . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE download_id ='" . (int)$old_download['download_id'] . "'");
				$this->msFile->deleteDownload($old_download['filename']);
			}
		}


		// attributes
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_product_attribute WHERE product_id = '" . (int)$product_id . "'");
		if (isset($data['product_attributes'])) {
			foreach ($data['product_attributes'] as $option_id => $attr) {
				if ($attr['type'] == 'select' || $attr['type'] == 'radio') {
					$this->db->query("INSERT INTO " . DB_PREFIX . "ms_product_attribute SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$option_id . "', option_value_id = '" . (int)$attr['value'] . "'");
				} else if ($attr['type'] == 'checkbox') {
					foreach ($attr['values'] as $option_value_id) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "ms_product_attribute SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$option_id . "', option_value_id = '" . (int)$option_value_id . "'");
					}
				} 
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
}
?>
