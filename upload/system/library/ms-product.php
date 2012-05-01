<?php
class MsProduct {
	const MS_PRODUCT_STATUS_APPROVED = 1;
	const MS_PRODUCT_STATUS_PENDING = 2;
	const MS_PRODUCT_STATUS_DECLINED = 3;
	const MS_PRODUCT_STATUS_DRAFT = 4;
	
	const MS_PRODUCT_VALIDATION_NONE = 1;
	const MS_PRODUCT_VALIDATION_APPROVAL = 2;
	
	private $errors;
		
  	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
		$this->load = $registry->get('load');
		$this->language = $registry->get('language');
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
				LEFT JOIN " . DB_PREFIX . "ms_product mp
					USING(product_id)
				LEFT JOIN " . DB_PREFIX . "ms_seller ms
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
						ptc.category_id,
						mp.review_status_id
				FROM `" . DB_PREFIX . "product` p
				INNER JOIN `" . DB_PREFIX . "product_to_category` ptc
					ON p.product_id = ptc.product_id
				INNER JOIN `" . DB_PREFIX . "ms_product` mp
					ON ptc.product_id = mp.product_id
				WHERE p.product_id = " . (int)$product_id;

		$res = $this->db->query($sql);

		
		$sql = "SELECT pd.*,
					   group_concat(pt.tag separator ', ') as tags
				FROM " . DB_PREFIX . "product_description pd
				LEFT JOIN `" . DB_PREFIX . "product_tag` pt
					ON pd.product_id = pt.product_id
					AND pd.language_id = pt.language_id
				WHERE pd.product_id = " . (int)$product_id . "
				GROUP BY language_id";

		$descriptions = $this->db->query($sql);

		$product_description_data = array();
		foreach ($descriptions->rows as $result) {
			$product_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description'],
				'tags'      => $result['tags'],
				//'meta_keyword'     => $result['meta_keyword'],
				//'meta_description' => $result['meta_description']
			);
		}

		$res->row['languages'] = $product_description_data;
		return $res->row;
	}
		
	public function getProductImages($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
		
		return $query->rows;
	}

	public function getProductDownloads($product_id) {
		$sql = "SELECT 	*
				FROM `" . DB_PREFIX . "download` d
				LEFT JOIN `" . DB_PREFIX . "product_to_download` pd
					USING(download_id)
				WHERE pd.product_id = " . (int)$product_id;
		$res = $this->db->query($sql);
				
		return $res->rows;
	}

	public function getProductThumbnail($product_id) {
		$query = $this->db->query("SELECT image FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		
		return $query->row;
	}		
		
	public function getTotalProducts($nodrafts = false) {
		$sql = "SELECT COUNT(*) as total FROM " . DB_PREFIX . "product"
				. ($nodrafts ? " LEFT JOIN " . DB_PREFIX . "ms_product mp USING (product_id) WHERE ISNULL(mp.review_status_id) OR mp.review_status_id != " . (int)self::MS_PRODUCT_STATUS_DRAFT : '');

		$res = $this->db->query($sql);
		return $res->row['total'];
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

		if (isset($data['product_thumbnail_name'])) {
			$image = MsImage::byName($this->registry, $data['product_thumbnail_name']);
			$image->move('I');
			$thumbnail = $image->getName();
		} else {
			$thumbnail = '';
		}

		$sql = "INSERT INTO " . DB_PREFIX . "product
				SET price = " . (float)$data['product_price'] . ",
					model = '".$this->db->escape($data['languages'][$first]['product_name']) ."',
					image = '" .  $this->db->escape($thumbnail)  . "',
					subtract = 0,
					quantity = 1,
					shipping = 0,
					status = " . (int)$data['enabled'] . ",
					date_available = NOW(),				
					date_added = NOW(),
					date_modified = NOW()";
		
		$this->db->query($sql);
		$product_id = $this->db->getLastId();

		foreach ($data['languages'] as $language_id => $language) {
			$sql = "INSERT INTO " . DB_PREFIX . "product_description
					SET product_id = " . (int)$product_id . ",
						name = '". $this->db->escape($language['product_name']) ."',
						description = '". $this->db->escape($language['product_description']) ."',
						language_id = " . (int)$language_id;
			$this->db->query($sql);
			
			if ($language['product_tags']) {
				$tags = explode(',', $language['product_tags']);
					
				foreach ($tags as $tag) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_tag SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', tag = '" . $this->db->escape(trim($tag)) . "'");
				}
			}
		}
		
		$sql = "INSERT INTO " . DB_PREFIX . "ms_product
				SET product_id = " . (int)$product_id . ",
					seller_id = " . (int)$this->registry->get('customer')->getId() . ",
					review_status_id = " . (int)$data['review_status_id'];
		$this->db->query($sql);
		
		
		$sql = "INSERT INTO " . DB_PREFIX . "product_to_category
				SET product_id = " . (int)$product_id . ",
					category_id = " . (int)$data['product_category'];
		$this->db->query($sql);		


		$sql = "INSERT INTO " . DB_PREFIX . "product_to_store
				SET product_id = " . (int)$product_id . ",
					store_id = " . (int)$store_id;
		$this->db->query($sql);


		if (isset($data['product_images'])) {
			foreach ($data['product_images'] as $key => $img) {
				$image = MsImage::byName($this->registry, $img);
				$image->move('I');
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape(html_entity_decode($image->getName(), ENT_QUOTES, 'UTF-8')) . "', sort_order = '" . (int)$key . "'");
			}
		}
		
		if (isset($data['product_downloads'])) {
			foreach ($data['product_downloads'] as $key => $dl) {
				$image = MsImage::byName($this->registry, $dl);
				$image->move('F');
				$this->db->query("INSERT INTO " . DB_PREFIX . "download SET remaining = 5, filename = '" . $this->db->escape($image->getName()) . "', mask = '" . $this->db->escape(substr($image->getName(),0,strrpos($image->getName(),'.'))) . "'");
				$download_id = $this->db->getLastId();
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
				
				foreach ($data['languages'] as $language_id => $language) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "download_description SET download_id = '" . (int)$download_id . "', name = '" . $this->db->escape(substr($image->getName(),0,strrpos($image->getName(),'.'))) . "', language_id = '" . (int)$language_id . "'");
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
		
		if (!isset($data['product_thumbnail_name']) || ($old_thumbnail['image'] != $data['product_thumbnail_name'])) {
			$image = MsImage::byName($this->registry, $old_thumbnail['image']);
			$image->delete('I');				
		}
		
		if (isset($data['product_thumbnail_name'])) {
			$image = MsImage::byName($this->registry, $data['product_thumbnail_name']);
			$image->move('I');
			$thumbnail = $image->getName();
		} else {
			$thumbnail = '';
		}

		$sql = "UPDATE " . DB_PREFIX . "product
				SET price = " . (float)$data['product_price'] . ",
					status = " . (int)$data['enabled'] . ",
					image = '" . $this->db->escape($thumbnail) . "',
					date_modified = NOW()
				WHERE product_id = " . (int)$product_id;
		
		$this->db->query($sql);


		$sql = "DELETE FROM " . DB_PREFIX . "product_tag
				WHERE product_id = " . (int)$product_id;
		$this->db->query($sql);

		foreach ($data['languages'] as $language_id => $language) {
			$sql = "UPDATE " . DB_PREFIX . "product_description
					SET name = '". $this->db->escape($language['product_name']) ."',
						description = '". $this->db->escape($language['product_description']) ."'
					WHERE product_id = " . (int)$product_id . "
					AND language_id = " . (int)$language_id;
					
			$this->db->query($sql);
			
			if ($language['product_tags']) {
				$tags = explode(',', $language['product_tags']);
				foreach ($tags as $tag) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_tag SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', tag = '" . $this->db->escape(trim($tag)) . "'");
				}
			}
		}		
		
		$sql = "UPDATE " . DB_PREFIX . "ms_product
				SET review_status_id = " . (int)$data['review_status_id'] . "
				WHERE product_id = " . (int)$product_id; 
		
		$this->db->query($sql);
		
		$sql = "UPDATE " . DB_PREFIX . "product_to_category
				SET category_id = " . (int)$data['product_category'] . "
				WHERE product_id = " . (int)$product_id;
		
		$this->db->query($sql);		

		// delete old images		
		$old_images = $this->getProductImages($product_id);
		foreach($old_images as $old_image) {
			if (!isset($data['product_images']) || array_search($old_image['image'], $data['product_images']) === FALSE) {
				$image = MsImage::byName($this->registry, $old_image['image']);
				$image->delete('I');				
			}
		}
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
		
		// add new images
		if (isset($data['product_images'])) {
			foreach ($data['product_images'] as $key => $product_image) {
				$image = MsImage::byName($this->registry, $product_image);
				$image->move('I');				
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape(html_entity_decode($image->getName(), ENT_QUOTES, 'UTF-8')) . "', sort_order = '" . (int)$key . "'");
			}
		}		

		$old_downloads = $this->getProductDownloads($product_id);
		foreach($old_downloads as $old_download) {
			if (!isset($data['product_downloads']) || array_search($old_download['filename'], $data['product_downloads']) === FALSE) {
				$file = MsImage::byName($this->registry, $old_download['filename']);
				$file->delete('F');
			}
		}
		$this->db->query("DELETE FROM " . DB_PREFIX . "download WHERE download_id IN (SELECT download_id FROM " . DB_PREFIX . "product_to_download WHERE product_id ='" . (int)$product_id . "')");
		$this->db->query("DELETE FROM " . DB_PREFIX . "download_description WHERE download_id IN (SELECT download_id FROM " . DB_PREFIX . "product_to_download WHERE product_id ='" . (int)$product_id . "')");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");		
		if (isset($data['product_downloads'])) {
			foreach ($data['product_downloads'] as $key => $dl) {
				$image = MsImage::byName($this->registry, $dl);
				$image->move('F');
				$this->db->query("INSERT INTO " . DB_PREFIX . "download SET remaining = 5, filename = '" . $this->db->escape($image->getName()) . "', mask = '" . $this->db->escape(substr($image->getName(),0,strrpos($image->getName(),'.'))) . "'");
				$download_id = $this->db->getLastId();
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
				
				foreach ($data['languages'] as $language_id => $language) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "download_description SET download_id = '" . (int)$download_id . "', name = '" . $this->db->escape($image->getName()) . "', language_id = '" . (int)$language_id . "'");
				}
			}
		}
		
		$this->registry->get('cache')->delete('product');
		
		return $product_id;		
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