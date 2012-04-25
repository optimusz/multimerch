<?php
class ModelModuleMultisellerSeller extends Model {
	//TODO
	public function getSellerStatus($seller_status_id = NULL) {
		$this->load->language('module/multiseller');
		$result = array(
			MS_SELLER_STATUS_ACTIVE => $this->language->get('ms_seller_status_active'),
			MS_SELLER_STATUS_TOBEACTIVATED => $this->language->get('ms_seller_status_activation'),
			MS_SELLER_STATUS_TOBEAPPROVED => $this->language->get('ms_seller_status_approval'),
			MS_SELLER_STATUS_DISABLED => $this->language->get('ms_seller_status_disabled'),
		);		
		
		if ($seller_status_id) {
			return $result[$seller_status_id];
		} else {
			return $result;
		}
	}
	
	public function getCommissionForSeller($seller_id) {
		$sql = "SELECT 	commission
				FROM `" . DB_PREFIX . "ms_seller`
				WHERE seller_id = " . (int)$seller_id; 

		$res = $this->db->query($sql);

		return $res->row['commission'];		
	}	
	
	public function getSellerIdByProduct($product_id) {
		$sql = "SELECT seller_id FROM " . DB_PREFIX . "ms_product
				WHERE product_id = " . (int)$product_id;
				
		$res = $this->db->query($sql);
		return $res->row['seller_id'];
	}
	
	public function getSellerData($seller_id) {
		$sql = "SELECT * 
				FROM `" . DB_PREFIX . "ms_seller`
				WHERE seller_id = " . (int)$seller_id;
		
		$res = $this->db->query($sql);
		
		return $res->row;
	}
	
	public function getSellerDataForProduct($product_id) {
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
	
	public function getSellers($sort) {
		$sql = "SELECT  CONCAT(c.firstname, ' ', c.lastname) as name,
						c.email as email,
						ms.seller_id,
						ms.nickname,
						ms.seller_status_id,
						ms.date_created as date_created,
						ms.commission
				FROM `" . DB_PREFIX . "customer` c
				INNER JOIN `" . DB_PREFIX . "ms_seller` ms
					ON c.customer_id = ms.seller_id
        		ORDER BY {$sort['order_by']} {$sort['order_way']}" 
        		. ($sort['limit'] ? " LIMIT ".(int)(($sort['page'] - 1) * $sort['limit']).', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);
		
		return $res->rows;		
	}

	public function getTotalSellers() {
		$sql = "SELECT COUNT(*) as 'total'
				FROM `" . DB_PREFIX . "ms_seller`";
		
		$res = $this->db->query($sql);
		
		return $res->row['total'];		
	}
	
	public function getBalanceForSeller($seller_id) {
		$sql = "SELECT SUM(amount) as total
				FROM `" . DB_PREFIX . "ms_transaction`
				WHERE seller_id = " . (int)$seller_id;
		
		$res = $this->db->query($sql);
		
		return $res->row['total'];
	}

	public function getEarningsForSeller($seller_id) {
		$sql = "SELECT SUM(amount) as total
				FROM `" . DB_PREFIX . "ms_transaction`
				WHERE seller_id = " . (int)$seller_id . "
				AND	amount > 0";
		
		$res = $this->db->query($sql);
		
		return $res->row['total'];
	}

	public function getSalesForSeller($seller_id) {
		$sql = "SELECT IFNULL(SUM(number_sold),0) as total
				FROM `" . DB_PREFIX . "ms_product`
				WHERE seller_id = " . (int)$seller_id;
		
		$res = $this->db->query($sql);
		
		return $res->row['total'];
	}	
		
	public function getTotalSellerProducts($seller_id) {
		$sql = "SELECT COUNT(*) as 'total'
				FROM `" . DB_PREFIX . "ms_product` p
				WHERE p.seller_id = " . (int)$seller_id;
		
		$res = $this->db->query($sql);
		
		return $res->row['total'];		
	}
	
	public function getSellerProducts($seller_id, $sort) {
		$sql = "SELECT c.product_id, name, date_added, status as status_id, number_sold, review_status_id 
				FROM `" . DB_PREFIX . "product_description` a
				INNER JOIN `" . DB_PREFIX . "product` b
					ON a.product_id = b.product_id 
				INNER JOIN `" . DB_PREFIX . "ms_product` c
					ON b.product_id = c.product_id
				WHERE c.seller_id = " . (int)$seller_id . "
        		ORDER BY {$sort['order_by']} {$sort['order_way']}" 
        		. ($sort['limit'] ? " LIMIT ".(int)(($sort['page'] - 1) * $sort['limit']).', '.(int)($sort['limit']) : '');				
		
		$res = $this->db->query($sql);
		
		
		$review_statuses = $this->getProductStatusArray();
		foreach ($res->rows as &$row) {
			$row['review_status'] = $review_statuses[$row['review_status_id']];
			$row['status'] = $row['status_id'] ? $this->language->get('text_yes') : $this->language->get('text_no');
		}
		
		return $res->rows;
	}
	
	public function editSeller($data) {
		$seller_id = (int)$data['seller_id'];
		
		$sql = "UPDATE " . DB_PREFIX . "ms_seller
				SET description = '" . $this->db->escape($data['sellerinfo_description']) . "',
					company = '" . $this->db->escape($data['sellerinfo_company']) . "',
					country_id = " . (int)$data['sellerinfo_country'] . ",
					paypal = '" . $this->db->escape($data['sellerinfo_paypal']) . "',
					seller_status_id = '" .  (int)$data['seller_status_id'] .  "'
				WHERE seller_id = " . (int)$seller_id;
		
		$this->db->query($sql);	
	}	
}