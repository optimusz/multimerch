<?php
final class MsSeller extends Model {
	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 2;
	const STATUS_DISABLED = 3;
	const STATUS_DELETED = 4;
		
	const MS_SELLER_VALIDATION_NONE = 1;
	const MS_SELLER_VALIDATION_ACTIVATION = 2;
	const MS_SELLER_VALIDATION_APPROVAL = 3;

	private $isSeller = FALSE; 
	private $nickname;
	private $description;
	private $company;
	private $country_id;
	private $avatar;
	private $seller_status;
	private $paypal;
	
  	public function __construct($registry) {
  		parent::__construct($registry);
  		
  		//$this->log->write('creating seller object: ' . $this->session->data['customer_id']);
		if (isset($this->session->data['customer_id'])) {
			//TODO 
			//$seller_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ms_seller WHERE seller_id = '" . (int)$this->session->data['customer_id'] . "' AND seller_status = '1'");
			$seller_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ms_seller WHERE seller_id = '" . (int)$this->session->data['customer_id'] . "'");			
			
			if ($seller_query->num_rows) {
				$this->isSeller = TRUE;
				$this->nickname = $seller_query->row['nickname'];
				$this->description = $seller_query->row['description'];
				$this->company = $seller_query->row['company'];
				$this->country_id = $seller_query->row['country_id'];
				$this->avatar = $seller_query->row['avatar'];
				$this->seller_status = $seller_query->row['seller_status'];
				$this->paypal = $seller_query->row['paypal'];
			}
  		}
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
		
	public function getTotalSellerProducts($seller_id, $onlyActive = FALSE) {
		$sql = "SELECT COUNT(*) as 'total'
				FROM `" . DB_PREFIX . "ms_product` mp"
        		. ($onlyActive ? " INNER JOIN `" . DB_PREFIX . "product` p USING(product_id)" : '') . "
				WHERE mp.seller_id = " . (int)$seller_id
        		. ($onlyActive ? " AND p.status = 1" : '');				
		
		$res = $this->db->query($sql);
		
		return $res->row['total'];		
	}		
	
	
	public function _getSellerProducts($seller_id, $sort, $onlyActive = FALSE) {
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
		
		$review_statuses = $this->MsLoader->MsProduct->getProductStatusArray();
		foreach ($res->rows as &$row) {
			if ($row['review_status_id'] != MsProduct::MS_PRODUCT_STATUS_SELLER_DELETED) {
				$row['review_status'] = $review_statuses[$row['review_status_id']];
			}
			else {
				$row['review_status'] = $row['review_status_id'];
			}
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
		
		
		$review_statuses = $this->MsLoader->MsProduct->getProductStatusArray();
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
			$avatar = $this->MsLoader->MsFile->moveImage($data['sellerinfo_avatar_name']);
		} else {
			$avatar = '';
		}
		
		$sql = "INSERT INTO " . DB_PREFIX . "ms_seller
				SET seller_id = " . (int)$data['seller_id'] . ",
					seller_status = " . (int)$data['seller_status'] . ",
					seller_approved = " . (int)$data['seller_approved'] . ",
					commission = " . (float)$this->config->get('msconf_seller_commission') . ",
					commission_flat = " . (float)$this->config->get('msconf_seller_commission_flat') . ",
					nickname = '" . $this->db->escape($data['sellerinfo_nickname']) . "',
					description = '" . $this->db->escape($data['sellerinfo_description']) . "',
					company = '" . $this->db->escape($data['sellerinfo_company']) . "',
					country_id = " . (int)$data['sellerinfo_country'] . ",
					product_validation = " . (int)$data['sellerinfo_product_validation'] . ",
					paypal = '" . $this->db->escape($data['sellerinfo_paypal']) . "',
					avatar = '" . $this->db->escape($avatar) . "',
					date_created = NOW()";
		
		$this->db->query($sql);
		$seller_id = $this->db->getLastId();
		
		if (isset($data['keyword'])) {
			$similarity_query = $this->db->query("SELECT * FROM ". DB_PREFIX . "url_alias WHERE keyword LIKE '" . $this->db->escape($data['keyword']) . "%'");
			$number = $similarity_query->num_rows;
			
			if ($number > 0) {
				$data['keyword'] = $data['keyword'] . "-" . $number;
			}
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'seller_id=" . (int)$seller_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}
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
			$this->MsLoader->MsFile->deleteImage($old_avatar['avatar']);
		}
		
		if (isset($data['sellerinfo_avatar_name'])) {
			if ($old_avatar['avatar'] != $data['sellerinfo_avatar_name']) {			
				$avatar = $this->MsLoader->MsFile->moveImage($data['sellerinfo_avatar_name']);
			} else {
				$avatar = $old_avatar['avatar'];
			}
		} else {
			$avatar = '';
		}

		$sql = "UPDATE " . DB_PREFIX . "ms_seller
				SET description = '" . $this->db->escape($data['sellerinfo_description']) . "',
					company = '" . $this->db->escape($data['sellerinfo_company']) . "',
					country_id = " . (int)$data['sellerinfo_country'] . ",
					seller_status = " . (int)$data['seller_status'] . ",
					seller_approved = " . (int)$data['seller_approved'] . ",
					paypal = '" . $this->db->escape($data['sellerinfo_paypal']) . "',
					avatar = '" . $this->db->escape($avatar) . "'
				WHERE seller_id = " . (int)$seller_id;
		
		$this->db->query($sql);	
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
		$query = $this->db->query("SELECT avatar as avatar FROM " . DB_PREFIX . "ms_seller WHERE seller_id = '" . (int)$seller_id . "'");
		
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
  		return $this->avatar;
  	}
  	
  	public function getStatus() {
  		return $this->seller_status;
  	}

  	public function getPaypal() {
  		return $this->paypal;
  	}
  	
  	public function isSeller() {
  		return $this->isSeller;
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
	
	public function getSalt($seller_id) {
		$sql = "SELECT salt
				FROM `" . DB_PREFIX . "customer`
				WHERE customer_id = " . (int)$seller_id;
		
		$res = $this->db->query($sql);
		
		return $res->row['salt'];		
	}
	
	
	public function adminEditSeller($data) {
		$seller_id = (int)$data['seller_id'];
		
		$sql = "UPDATE " . DB_PREFIX . "ms_seller
				SET description = '" . $this->db->escape($data['sellerinfo_description']) . "',
					company = '" . $this->db->escape($data['sellerinfo_company']) . "',
					country_id = " . (int)$data['sellerinfo_country'] . ",
					paypal = '" . $this->db->escape($data['sellerinfo_paypal']) . "',
					seller_status = '" .  (int)$data['seller_status'] .  "',
					seller_approved = '" .  (int)$data['seller_approved'] .  "',
					product_validation = '" .  (int)$data['sellerinfo_product_validation'] .  "',
					commission = '" .  (float)$data['sellerinfo_commission'] .  "',
					commission_flat = '" .  (float)$data['sellerinfo_commission_flat'] .  "'
				WHERE seller_id = " . (int)$seller_id;
		
		$this->db->query($sql);	
	}
	
	
	
	
	
	
	/********************************************************/
	
	
	public function getTotalSellers($data = array()) {
		$sql = "
			SELECT COUNT(*) as total
			FROM " . DB_PREFIX . "ms_seller ms
			WHERE 1 = 1 "
			. (isset($data['seller_status']) ? " AND seller_status IN  (" .  $this->db->escape(implode(',', $data['seller_status'])) . ")" : '');

		$res = $this->db->query($sql);

		return $res->row['total'];
	}
	
	public function getSeller($seller_id, $data = array()) {
		$sql = "SELECT	CONCAT(c.firstname, ' ', c.lastname) as name,
						c.email as 'c.email',
						ms.seller_id as 'seller_id',
						ms.nickname as 'ms.nickname',
						ms.company as 'ms.company',
						ms.website as 'ms.website',
						ms.paypal as 'ms.paypal',
						ms.seller_status as 'ms.seller_status',
						ms.seller_approved as 'ms.seller_approved',
						ms.date_created as 'ms.date_created',
						ms.commission as 'ms.commission',
						ms.commission_flat as 'ms.commission_flat',
						ms.product_validation as 'ms.product_validation',
						ms.avatar as 'ms.avatar',
						ms.country_id as 'ms.country_id',
						ms.description as 'ms.description',
						IFNULL(SUM(mp.number_sold), 0) as 'total_sales'
				FROM `" . DB_PREFIX . "customer` c
				INNER JOIN `" . DB_PREFIX . "ms_seller` ms
					ON (c.customer_id = ms.seller_id)
				LEFT JOIN `" . DB_PREFIX . "ms_product` mp
					ON (c.customer_id = mp.seller_id)
				WHERE ms.seller_id = " .  (int)$seller_id
				. (isset($data['product_id']) ? " AND mp.product_id =  " .  (int)$data['product_id'] : '')
				. (isset($data['seller_status']) ? " AND seller_status IN  (" .  $this->db->escape(implode(',', $data['seller_status'])) . ")" : '')
				. " LIMIT 1";
				
		$res = $this->db->query($sql);

		if (!($res->row['seller_id']))
			return FALSE;
		else
			return $res->row;
	}	
	
	public function getSellers($data, $sort = array()) {
		$sql = "SELECT  CONCAT(c.firstname, ' ', c.lastname) as 'c.name',
						c.email as 'c.email',
						ms.seller_id as 'seller_id',
						ms.nickname as 'ms.nickname',
						ms.company as 'ms.company',
						ms.website as 'ms.website',
						ms.seller_status as 'ms.seller_status',
						ms.seller_approved as 'ms.seller_approved',
						ms.date_created as 'ms.date_created',
						ms.commission as 'ms.commission',
						ms.commission_flat as 'ms.commission_flat',
						ms.avatar as 'ms.avatar',
						ms.country_id as 'ms.country_id',
						ms.description as 'ms.description',
						IFNULL(SUM(mp.number_sold), 0) as 'total_sales'
				FROM `" . DB_PREFIX . "customer` c
				INNER JOIN `" . DB_PREFIX . "ms_seller` ms
					ON (c.customer_id = ms.seller_id)
				LEFT JOIN `" . DB_PREFIX . "ms_product` mp
					ON (c.customer_id = mp.seller_id)
				WHERE 1 = 1 "
				. (isset($data['seller_id']) ? " AND ms.seller_id =  " .  (int)$data['seller_id'] : '')
				. (isset($data['seller_status']) ? " AND seller_status IN  (" .  $this->db->escape(implode(',', $data['seller_status'])) . ")" : '')
				. " GROUP BY ms.seller_id"
				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
    			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);
		
		return $res->rows;
	}
	
	public function getStatusText($seller_status) {
		$status_text = '';
		switch ($seller_status) {
			case MsSeller::STATUS_ACTIVE:
				$status_text = $this->language->get('ms_status_active');
				break;
			case MsSeller::STATUS_INACTIVE:
				$status_text = $this->language->get('ms_status_inactive');
				break;
			case MsSeller::STATUS_DISABLED:
				$status_text = $this->language->get('ms_status_disabled');
				break;
			case MsSeller::STATUS_DELETED:
				$status_text = $this->language->get('ms_status_deleted');
				break;
		}
		
		return $status_text;
	}
	
	public function getStatuses() {
		return array(
			MsSeller::STATUS_ACTIVE =>  $this->language->get('ms_status_active'),
			MsSeller::STATUS_INACTIVE => $this->language->get('ms_status_inactive'),
			MsSeller::STATUS_DISABLED => $this->language->get('ms_status_disabled'),
			MsSeller::STATUS_DELETED => $this->language->get('ms_status_deleted')
		);
	}	
	
	/*
	public function getStatusText($seller_id) {
		$sql = "SELECT ms.seller_status as 'ms.seller_status',
					   ms.seller_approved as 'ms.seller_approved',

IFNULL(mrs.request_type, 0) as 'mrs.request_type'
				FROM `" . DB_PREFIX . "ms_seller` ms
				LEFT JOIN `" . DB_PREFIX . "ms_request_seller` mrs
					USING (seller_id)
				LEFT JOIN `" . DB_PREFIX . "ms_request` mr
					ON (mr.request_id = mrs.request_id) AND mr.request_status = " . (int)MsRequest::STATUS_PENDING. "
				WHERE seller_id = " . (int)$seller_id . "
				ORDER BY mr.request_id DESC
				LIMIT 1";
		
		$res = $this->db->query($sql);
		
		$result = $res->row;

		$status_text = '';
		$type_text = '';
		
		switch($result['mrs.request_type']) {
			case MsRequestSeller::TYPE_SELLER_CREATE:
			case MsRequestSeller::TYPE_SELLER_UPDATE:	
				$type_text = $this->language->get('ms_status_pending_approval');
				break;
			case MsRequestSeller::TYPE_SELLER_DELETE:
				$type_text = $this->language->get('ms_status_pending_deletion');
				break;
			default:
				break;
		}
				
		switch($result['ms.seller_status']) {
			case MsSeller::STATUS_ACTIVE:
				$status_text = $this->language->get('ms_status_active');
				$type_text = '';
				break;
			case MsSeller::STATUS_INACTIVE:
				$status_text = $this->language->get('ms_status_inactive');
				break;
			case MsSeller::STATUS_DISABLED:
				$status_text = $this->language->get('ms_status_disabled');
				break;
			case MsSeller::STATUS_DELETED:
				$status_text = $this->language->get('ms_status_deleted');
				break;
		}

		return array(
			'seller_status' => array(
				'id' => $result['ms.seller_status'],
				'text' => $status_text
			),
			'request_type' => array(
				'id' => $result['mrs.request_type'],
				'text' => $type_text
			),
			'text' => $status_text . '. ' . $type_text
		);
	}*/
}

?>