<?php
final class MsSeller {
	const MS_SELLER_STATUS_ACTIVE = 1;
	const MS_SELLER_STATUS_TOBEACTIVATED = 2;
	const MS_SELLER_STATUS_TOBEAPPROVED = 3;
	const MS_SELLER_STATUS_DISABLED = 4;
	const MS_SELLER_STATUS_INACTIVE = 5;
		
	private $isSeller = FALSE; 
	private $nickname;
	private $description;
	private $company;
	private $country_id;
	private $avatar_path;
	private $seller_status_id;
	private $paypal;
	
  	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
		$this->registry = $registry;
		$this->language = $registry->get('language');
		if (isset($this->session->data['customer_id'])) {
			//TODO 
			//$seller_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ms_seller WHERE seller_id = '" . (int)$this->session->data['customer_id'] . "' AND seller_status_id = '1'");
			$seller_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ms_seller WHERE seller_id = '" . (int)$this->session->data['customer_id'] . "'");			
			
			if ($seller_query->num_rows) {
				$this->isSeller = TRUE;
				$this->nickname = $seller_query->row['nickname'];
				$this->description = $seller_query->row['description'];
				$this->company = $seller_query->row['company'];
				$this->country_id = $seller_query->row['country_id'];
				$this->avatar_path = $seller_query->row['avatar_path'];
				$this->seller_status_id = $seller_query->row['seller_status_id'];
				$this->paypal = $seller_query->row['paypal'];
			}
  		}
  		
		require_once(DIR_SYSTEM . 'library/ms-product.php');
		$this->msProduct = new MsProduct($registry);  		
	}
		
  	public function isCustomerSeller($customer_id) {
		$sql = "SELECT COUNT(*) as 'total'
				FROM `" . DB_PREFIX . "ms_seller`
				WHERE seller_id = " . (int)$customer_id;
		
		$res = $this->db->query($sql);
		
		if ($res->row['total'] == 0)
			return FALSE;
		else
			return TRUE;	  		
  	}
  	
	public function getSellerData($seller_id) {
		$sql = "SELECT * 
				FROM `" . DB_PREFIX . "ms_seller`
				WHERE seller_id = " . (int)$seller_id;
		
		$res = $this->db->query($sql);
		
		return $res->row;
	}
	
	public function getSellerName($seller_id) {
		$sql = "SELECT firstname as 'firstname'
				FROM `" . DB_PREFIX . "customer`
				WHERE customer_id = " . (int)$seller_id;
		
		$res = $this->db->query($sql);
		
		return $res->row['firstname'];
	}	
	
	public function getSellerEmail($seller_id) {
		$sql = "SELECT email as 'email' 
				FROM `" . DB_PREFIX . "customer`
				WHERE customer_id = " . (int)$seller_id;
		
		$res = $this->db->query($sql);
		
		return $res->row['email'];
	}
		
	//TODO
	public function getSellerStatus($seller_status_id = NULL) {
		$result = array(
			self::MS_SELLER_STATUS_ACTIVE => $this->language->get('ms_seller_status_active'),
			self::MS_SELLER_STATUS_TOBEACTIVATED => $this->language->get('ms_seller_status_activation'),
			self::MS_SELLER_STATUS_TOBEAPPROVED => $this->language->get('ms_seller_status_approval'),
			self::MS_SELLER_STATUS_DISABLED => $this->language->get('ms_seller_status_disabled'),
		);		
		
		if ($seller_status_id) {
			return $result[$seller_status_id];
		} else {
			return $result;
		}
	}		
		
	public function getTotalSellerProducts($seller_id, $onlyActive = FALSE) {
		$sql = "SELECT COUNT(*) as 'total'
				FROM `" . DB_PREFIX . "ms_product` mp"
        		. ($onlyActive ? " INNER JOIN `" . DB_PREFIX . "product` p USING(product_id)" : '') . "
				WHERE mp.seller_id = " . (int)$seller_id
        		. ($onlyActive ? " AND p.status = 1" : '');				
		
		$res = $this->db->query($sql);
		
		return $res->row['total'];		
	}		
		
	public function getSellerProducts($seller_id, $sort, $onlyActive = FALSE) {
		$orders = array(
			'pd.name'
		);
		
		$order_sql = '';
		if (isset($sort['order_by']) && in_array($sort['order_by'], $orders)) {
			if ($sort['order_by'] == 'pd.name' || $sort['order_by'] == 'p.model') {
				$order_sql .= " ORDER BY LCASE(" . $sort['order_by'] . ")";
			} else {
				$order_sql .= " ORDER BY " . $sort['order_by'];
			}
		} else {
			$order_sql .= " ORDER BY LCASE(pd.name)";	
		}
		
		if (isset($sort['order_way']) && ($sort['order_way'] == 'DESC')) {
			$order_sql .= " DESC, LCASE(pd.name) DESC";
		} else {
			$order_sql .= " ASC, LCASE(pd.name) ASC";
		}
		
		$sql = "SELECT mp.product_id, name, date_added, status as status_id, number_sold, review_status_id 
				FROM `" . DB_PREFIX . "product_description` pd
				INNER JOIN `" . DB_PREFIX . "product` p
					ON pd.product_id = p.product_id 
				INNER JOIN `" . DB_PREFIX . "ms_product` mp
					ON p.product_id = mp.product_id
				WHERE mp.seller_id = " . (int)$seller_id . "
				AND pd.language_id = " . $this->config->get('config_language_id') 
        		. ($onlyActive ? " AND p.status = 1" : '')
        		. $order_sql 
        		. (isset($sort['limit']) ? " LIMIT ".(int)(($sort['page'] - 1) * $sort['limit']).', '.(int)($sort['limit']) : '');				

		$res = $this->db->query($sql);
		
		$review_statuses = $this->msProduct->getProductStatusArray();
		foreach ($res->rows as &$row) {
			$row['review_status'] = $review_statuses[$row['review_status_id']];
			$row['status'] = $row['status_id'] ? $this->language->get('text_yes') : $this->language->get('text_no');
		}
		
		return $res->rows;
	}
	
	/*public function getSellerProductsFull($seller_id, $sort) {
		
		$sql = "SELECT p.product_id,
				(SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating,		
					   p.price, name, date_added, status as status_id, number_sold, review_status_id
			    FROM `" . DB_PREFIX . "product` p 
				LEFT JOIN `" . DB_PREFIX . "product_description` pd
					ON p.product_id = pd.product_id 
				INNER JOIN `" . DB_PREFIX . "ms_product` c
					ON p.product_id = c.product_id
				WHERE c.seller_id = " . (int)$seller_id . "
				AND a.language_id = " . $this->config->get('config_language_id'). "
        		ORDER BY {$sort['order_by']} {$sort['order_way']}" 
        		. (isset($sort['limit']) ? " LIMIT ".(int)(($sort['page'] - 1) * $sort['limit']).', '.(int)($sort['limit']) : '');				
		
		$res = $this->db->query($sql);
		
		
		$review_statuses = $this->msProduct->getProductStatusArray();
		foreach ($res->rows as &$row) {
			$row['review_status'] = $review_statuses[$row['review_status_id']];
			$row['status'] = $row['status_id'] ? $this->language->get('text_yes') : $this->language->get('text_no');
		}
		
		return $res->rows;
	}	*/
		
	public function getReservedAmount($seller_id) {
		$sql = "SELECT SUM(amount - (amount*commission/100) - commission_flat) as total
				FROM `" . DB_PREFIX . "ms_transaction`
				WHERE seller_id = " . (int)$seller_id . "
				AND type = " . MsTransaction::MS_TRANSACTION_WITHDRAWAL . ";
				AND transaction_status_id = " . MsTransaction::MS_TRANSACTION_STATUS_PENDING;
		
		$res = $this->db->query($sql);
		
		return $res->row['total'];
	}		
		
	public function createSeller($data) {
		if (isset($data['sellerinfo_avatar_name'])) {
			$image = MsImage::byName($this->registry, $data['sellerinfo_avatar_name']);
			$image->move('I');
			$avatar = $image->getName();
		} else {
			$avatar = '';
		}
		$sql = "INSERT INTO " . DB_PREFIX . "ms_seller
				SET seller_id = " . (int)$data['seller_id'] . ",
					seller_status_id = " . (int)$data['seller_status_id'] . ",
					commission = " . (float)$this->config->get('msconf_seller_commission') . ",
					commission_flat = " . (float)$this->config->get('msconf_seller_commission_flat') . ",
					nickname = '" . $this->db->escape($data['sellerinfo_nickname']) . "',
					description = '" . $this->db->escape($data['sellerinfo_description']) . "',
					company = '" . $this->db->escape($data['sellerinfo_company']) . "',
					country_id = " . (int)$data['sellerinfo_country'] . ",
					product_validation = " . (int)$data['sellerinfo_product_validation'] . ",
					paypal = '" . $this->db->escape($data['sellerinfo_paypal']) . "',
					avatar_path = '" . $this->db->escape($avatar) . "',
					date_created = NOW()";
		
		$this->db->query($sql);
	}
	
	public function nicknameTaken($nickname) {
		$sql = "SELECT nickname
				FROM `" . DB_PREFIX . "ms_seller` p
				WHERE p.nickname = '" . $this->db->escape($nickname) . "'";
		
		$res = $this->db->query($sql);
		
		return $res->num_rows;
	}
	
	public function editSeller($data) {
		$seller_id = (int)$data['seller_id'];

		$old_avatar = $this->getSellerAvatar($seller_id);
		
		if (!isset($data['sellerinfo_avatar_name']) || ($old_avatar['avatar'] != $data['sellerinfo_avatar_name'])) {
			$image = MsImage::byName($this->registry, $old_avatar['avatar']);
			$image->delete('I');				
		}
		
		if (isset($data['sellerinfo_avatar_name'])) {
			$image = MsImage::byName($this->registry, $data['sellerinfo_avatar_name']);
			$image->move('I');
			$avatar = $image->getName();
		} else {
			$avatar = '';
		}

		$sql = "UPDATE " . DB_PREFIX . "ms_seller
				SET description = '" . $this->db->escape($data['sellerinfo_description']) . "',
					company = '" . $this->db->escape($data['sellerinfo_company']) . "',
					country_id = " . (int)$data['sellerinfo_country'] . ",
					paypal = '" . $this->db->escape($data['sellerinfo_paypal']) . "',
					avatar_path = '" . $avatar . "'
				WHERE seller_id = " . (int)$seller_id;
		
		$this->db->query($sql);	
	}		
		
	public function getBalanceForSeller($seller_id) {
		$sql = "SELECT SUM(amount - (amount*commission/100) - commission_flat) as total
				FROM `" . DB_PREFIX . "ms_transaction`
				WHERE seller_id = " . (int)$seller_id . " 
				AND transaction_status_id != " . MsTransaction::MS_TRANSACTION_STATUS_CLOSED;
		
		$res = $this->db->query($sql);
		
		return (float)$res->row['total'];
	}
		
	public function getCommissionPercentForSeller($seller_id) {
		$sql = "SELECT 	commission
				FROM `" . DB_PREFIX . "ms_seller`
				WHERE seller_id = " . (int)$seller_id; 

		$res = $this->db->query($sql);

		if (isset($res->row['commission']))
			return $res->row['commission'];
		else
			return 0;
	}

	public function getCommissionFlatForSeller($seller_id) {
		$sql = "SELECT 	commission_flat
				FROM `" . DB_PREFIX . "ms_seller`
				WHERE seller_id = " . (int)$seller_id; 

		$res = $this->db->query($sql);

		if (isset($res->row['commission_flat']))
			return $res->row['commission_flat'];
		else
			return 0;
	}

	public function getSellerAvatar($seller_id) {
		$query = $this->db->query("SELECT avatar_path as avatar FROM " . DB_PREFIX . "ms_seller WHERE seller_id = '" . (int)$seller_id . "'");
		
		return $query->row;
	}		
		
  	public function getNickname() {
  		return $this->nickname;
  	}

  	public function getCompany() {
  		return $this->company;
  	}
  	
  	public function getCountryId() {
  		return $this->country_id;
  	}

  	public function getDescription() {
  		return $this->description;
  	}
  	
  	public function getAvatarPath() {
  		return $this->avatar_path;
  	}
  	
  	public function getStatus() {
  		return $this->seller_status_id;
  	}

  	public function getPaypal() {
  		return $this->paypal;
  	}
  	
  	public function isSeller() {
  		return $this->isSeller;
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

	public function getSellers($sort, $onlyActive = FALSE) {
		$sql = "SELECT  CONCAT(c.firstname, ' ', c.lastname) as name,
						c.email as email,
						ms.seller_id,
						ms.nickname,
						ms.seller_status_id,
						ms.date_created as date_created,
						ms.commission,
						ms.avatar_path,
						ms.country_id,
						ms.description
				FROM `" . DB_PREFIX . "customer` c
				INNER JOIN `" . DB_PREFIX . "ms_seller` ms
					ON c.customer_id = ms.seller_id "
        		. ($onlyActive ? " WHERE ms.seller_status_id = " . self::MS_SELLER_STATUS_ACTIVE : '') . "
        		ORDER BY {$sort['order_by']} {$sort['order_way']}" 
        		. (isset($sort['limit']) ? " LIMIT ".(int)(($sort['page'] - 1) * $sort['limit']).', '.(int)($sort['limit']) : '');
		$res = $this->db->query($sql);
		return $res->rows;
	}

	public function getTotalSellers($onlyActive = FALSE) {
		$sql = "SELECT COUNT(*) as 'total'
				FROM `" . DB_PREFIX . "ms_seller` "
        		. ($onlyActive ? " WHERE seller_status_id = " . self::MS_SELLER_STATUS_ACTIVE : '');				
		
		$res = $this->db->query($sql);
		
		return $res->row['total'];		
	}
	
	//
	public function getEarningsForSeller($seller_id) {
		$sql = "SELECT SUM(amount) as total
				FROM `" . DB_PREFIX . "ms_transaction`
				WHERE seller_id = " . (int)$seller_id . "
				AND	amount > 0
				AND transaction_status_id != " . MsTransaction::MS_TRANSACTION_STATUS_CLOSED;				
		
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
	
	
	public function adminEditSeller($data) {
		$seller_id = (int)$data['seller_id'];
		
		$sql = "UPDATE " . DB_PREFIX . "ms_seller
				SET description = '" . $this->db->escape($data['sellerinfo_description']) . "',
					company = '" . $this->db->escape($data['sellerinfo_company']) . "',
					country_id = " . (int)$data['sellerinfo_country'] . ",
					paypal = '" . $this->db->escape($data['sellerinfo_paypal']) . "',
					seller_status_id = '" .  (int)$data['seller_status_id'] .  "',
					product_validation = '" .  (int)$data['sellerinfo_product_validation'] .  "',
					commission = '" .  (float)$data['sellerinfo_commission'] .  "',
					commission_flat = '" .  (float)$data['sellerinfo_commission_flat'] .  "'
				WHERE seller_id = " . (int)$seller_id;
		
		$this->db->query($sql);	
	}
}

?>