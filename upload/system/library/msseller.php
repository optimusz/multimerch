<?php
final class MsSeller extends Model {
	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 2;
	const STATUS_DISABLED = 3;
	const STATUS_DELETED = 4;
	const STATUS_UNPAID = 5;
		
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
		
	public function createSeller($data) {
		if (isset($data['avatar_name'])) {
			$avatar = $this->MsLoader->MsFile->moveImage($data['avatar_name']);
		} else {
			$avatar = '';
		}
		
		if (isset($data['commission']))
			$commission_id = $this->MsLoader->MsCommission->createCommission($data['commission']);
		
		$sql = "INSERT INTO " . DB_PREFIX . "ms_seller
				SET seller_id = " . (int)$data['seller_id'] . ",
					seller_status = " . (int)$data['status'] . ",
					seller_approved = " . (int)$data['approved'] . ",
					seller_group = " .  (isset($data['seller_group']) ? (int)$data['seller_group'] : $this->config->get('msconf_default_seller_group_id'))  .  ",
					nickname = '" . $this->db->escape($data['nickname']) . "',
					description = '" . $this->db->escape($data['description']) . "',
					company = '" . $this->db->escape($data['company']) . "',
					country_id = " . (int)$data['country'] . ",
					zone_id = " . (int)$data['zone'] . ",
					commission_id = " . (isset($commission_id) ? $commission_id : 'NULL') . ",
					product_validation = " . (int)$data['product_validation'] . ",
					paypal = '" . $this->db->escape($data['paypal']) . "',
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
		
		if (!isset($data['avatar_name']) || ($old_avatar['avatar'] != $data['avatar_name'])) {
			$this->MsLoader->MsFile->deleteImage($old_avatar['avatar']);
		}
		
		if (isset($data['avatar_name'])) {
			if ($old_avatar['avatar'] != $data['avatar_name']) {			
				$avatar = $this->MsLoader->MsFile->moveImage($data['avatar_name']);
			} else {
				$avatar = $old_avatar['avatar'];
			}
		} else {
			$avatar = '';
		}

		$sql = "UPDATE " . DB_PREFIX . "ms_seller
				SET description = '" . $this->db->escape($data['description']) . "',
					company = '" . $this->db->escape($data['company']) . "',
					nickname = '" . $this->db->escape($data['nickname']) . "',
					country_id = " . (int)$data['country'] . ",
					zone_id = " . (int)$data['zone'] . ","
					. (isset($data['status']) ? "seller_status=  " .  (int)$data['status'] . "," : '')
					. (isset($data['approved']) ? "seller_approved=  " .  (int)$data['approved'] . "," : '')
					. "paypal = '" . $this->db->escape($data['paypal']) . "',
					avatar = '" . $this->db->escape($avatar) . "'
				WHERE seller_id = " . (int)$seller_id;
		
		$this->db->query($sql);
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'seller_id=" . (int)$seller_id. "'");
		if (isset($data['keyword'])) {
			$similarity_query = $this->db->query("SELECT * FROM ". DB_PREFIX . "url_alias WHERE keyword LIKE '" . $this->db->escape($data['keyword']) . "%'");
			$number = $similarity_query->num_rows;
			
			if ($number > 0) {
				$data['keyword'] = $data['keyword'] . "-" . $number;
			}
			
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'seller_id=" . (int)$seller_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}
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

		// commissions
		if (!$data['commission_id']) {
			$commission_id = $this->MsLoader->MsCommission->createCommission($data['commission']);
		} else {
			$commission_id = $this->MsLoader->MsCommission->editCommission($data['commission_id'], $data['commission']);
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'seller_id=" . (int)$seller_id. "'");
		
		if (isset($data['keyword'])) {
			$similarity_query = $this->db->query("SELECT * FROM ". DB_PREFIX . "url_alias WHERE keyword LIKE '" . $this->db->escape($data['keyword']) . "%'");
			$number = $similarity_query->num_rows;
			
			if ($number > 0) {
				$data['keyword'] = $data['keyword'] . "-" . $number;
			}
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'seller_id=" . (int)$seller_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}

		$sql = "UPDATE " . DB_PREFIX . "ms_seller
				SET description = '" . $this->db->escape($data['description']) . "',
					company = '" . $this->db->escape($data['company']) . "',
					country_id = " . (int)$data['country'] . ",
					paypal = '" . $this->db->escape($data['paypal']) . "',
					seller_status = '" .  (int)$data['status'] .  "',
					zone_id = " . (int)$data['zone'] . ",
					seller_approved = '" .  (int)$data['approved'] .  "',
					product_validation = '" .  (int)$data['product_validation'] .  "',
					commission_id = " . (!is_null($commission_id) ? (int)$commission_id : 'NULL' ) . ",
					seller_group = '" .  (int)$data['seller_group'] .  "'
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
						ms.product_validation as 'ms.product_validation',
						ms.avatar as 'ms.avatar',
						ms.country_id as 'ms.country_id',
						ms.zone_id as 'ms.zone_id',
						ms.description as 'ms.description',
						ms.commission_id as 'ms.commission_id',
						ms.seller_group as 'ms.seller_group',
						IFNULL(SUM(mp.number_sold), 0) as 'total_sales',
						(SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE `query` = 'seller_id=" . (int)$seller_id . "' LIMIT 1) AS keyword
				FROM `" . DB_PREFIX . "customer` c
				INNER JOIN `" . DB_PREFIX . "ms_seller` ms
					ON (c.customer_id = ms.seller_id)
				LEFT JOIN `" . DB_PREFIX . "ms_product` mp
					ON (c.customer_id = mp.seller_id)
				WHERE ms.seller_id = " .  (int)$seller_id
				. (isset($data['product_id']) ? " AND mp.product_id =  " .  (int)$data['product_id'] : '')
				. (isset($data['seller_status']) ? " AND seller_status IN  (" .  $this->db->escape(implode(',', $data['seller_status'])) . ")" : '')
				. " GROUP BY ms.seller_id
				LIMIT 1";
				
		$res = $this->db->query($sql);

		if (!isset($res->row['seller_id']) || !$res->row['seller_id'])
			return FALSE;
		else
			return $res->row;
	}	
	
	public function getSellers($data = array(), $sort = array(), $cols = array()) {
		$hFilters = $wFilters = '';
		if(isset($sort['filters'])) {
			$cols = array_merge($cols, array("`c.name`" => 1, "total_sales" => 1, "`ms.date_created`" => 1));
			foreach($sort['filters'] as $k => $v) {
				if (!isset($cols[$k])) {
					$wFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				} else {
					$hFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				}
			}
		}
		
		$sql = "SELECT
					SQL_CALC_FOUND_ROWS"
					// additional columns
					. (isset($cols['total_products']) ? "
						(SELECT COUNT(*) FROM " . DB_PREFIX . "product p
						LEFT JOIN " . DB_PREFIX . "ms_product mp USING (product_id)
						LEFT JOIN " . DB_PREFIX . "ms_seller USING (seller_id)
						WHERE seller_id = ms.seller_id) as total_products,
					" : "")

					. (isset($cols['total_earnings']) ? "
						(SELECT COALESCE(SUM(amount),0)
							- (SELECT COALESCE(ABS(SUM(amount)),0)
								FROM `" . DB_PREFIX . "ms_balance`
								WHERE seller_id = ms.seller_id
								AND balance_type = ". MsBalance::MS_BALANCE_TYPE_REFUND
						. ") as total
						FROM `" . DB_PREFIX . "ms_balance`
						WHERE seller_id = ms.seller_id
						AND balance_type = ". MsBalance::MS_BALANCE_TYPE_SALE . ") as total_earnings,
					" : "")
					
					. (isset($cols['current_balance']) ? "
						(SELECT COALESCE(
							(SELECT balance FROM " . DB_PREFIX . "ms_balance
								WHERE seller_id = ms.seller_id  
								ORDER BY balance_id DESC
								LIMIT 1
							),
							0
						)) as current_balance,
					" : "")	
					
					// default columns
					." CONCAT(c.firstname, ' ', c.lastname) as 'c.name',
					c.email as 'c.email',
					ms.seller_id as 'seller_id',
					ms.nickname as 'ms.nickname',
					ms.company as 'ms.company',
					ms.website as 'ms.website',
					ms.seller_status as 'ms.seller_status',
					ms.seller_approved as 'ms.seller_approved',
					ms.date_created as 'ms.date_created',
					ms.avatar as 'ms.avatar',
					ms.country_id as 'ms.country_id',
					ms.zone_id as 'ms.zone_id',
					ms.description as 'ms.description',
					ms.paypal as 'ms.paypal',
					IFNULL(SUM(mp.number_sold), 0) as 'total_sales'
				FROM `" . DB_PREFIX . "customer` c
				INNER JOIN `" . DB_PREFIX . "ms_seller` ms
					ON (c.customer_id = ms.seller_id)
				LEFT JOIN `" . DB_PREFIX . "ms_product` mp
					ON (c.customer_id = mp.seller_id)
				WHERE 1 = 1 "
				. (isset($data['seller_id']) ? " AND ms.seller_id =  " .  (int)$data['seller_id'] : '')
				. (isset($data['seller_status']) ? " AND seller_status IN  (" .  $this->db->escape(implode(',', $data['seller_status'])) . ")" : '')
				
				. $wFilters
				
				. " GROUP BY ms.seller_id HAVING 1 = 1 "
				
				. $hFilters
				
				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
				. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);
		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];
		
		return $res->rows;
	}
	
	public function getCustomers($sort = array()) {
		$sql = "SELECT  CONCAT(c.firstname, ' ', c.lastname) as 'c.name',
						c.email as 'c.email',
						c.customer_id as 'c.customer_id',
						ms.seller_id as 'seller_id'
				FROM `" . DB_PREFIX . "customer` c
				LEFT JOIN `" . DB_PREFIX . "ms_seller` ms
					ON (c.customer_id = ms.seller_id)
				WHERE ms.seller_id IS NULL"
				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
    			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);
		
		return $res->rows;
	}
	
	public function getTotalEarnings($seller_id, $data = array()) {
		// note: update getSellers() if updating this
		$sql = "SELECT COALESCE(SUM(amount),0)
					   - (SELECT COALESCE(ABS(SUM(amount)),0)
						  FROM `" . DB_PREFIX . "ms_balance`
					 	  WHERE seller_id = " . (int)$seller_id . "
						  AND balance_type = ". MsBalance::MS_BALANCE_TYPE_REFUND
						  . (isset($data['period_start']) ? " AND DATEDIFF(date_created, '{$data['period_start']}') >= 0" : "")
				. ") as total
				FROM `" . DB_PREFIX . "ms_balance`
				WHERE seller_id = " . (int)$seller_id . "
				AND balance_type = ". MsBalance::MS_BALANCE_TYPE_SALE
				. (isset($data['period_start']) ? " AND DATEDIFF(date_created, '{$data['period_start']}') >= 0" : "");

		$res = $this->db->query($sql);
		return $res->row['total'];
	}
	
	public function changeStatus($seller_id, $seller_status) {
		$sql = "UPDATE " . DB_PREFIX . "ms_seller
				SET	seller_status =  " .  (int)$seller_status . "
				WHERE seller_id = " . (int)$seller_id;
		
		$res = $this->db->query($sql);
	}
	
	public function changeApproval($seller_id, $approved) {
		$sql = "UPDATE " . DB_PREFIX . "ms_seller
				SET	approved =  " .  (int)$approved . "
				WHERE seller_id = " . (int)$seller_id;
		
		$res = $this->db->query($sql);
	}
	
	public function deleteSeller($seller_id) {
		$products = $this->MsLoader->MsProduct->getProducts(array('seller_id' => $seller_id));

		foreach ($products as $product) {
			$this->MsLoader->MsProduct->changeStatus($product['product_id'], MsProduct::STATUS_DELETED);
		}
	
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_seller WHERE seller_id = '" . (int)$seller_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_balance WHERE seller_id = '" . (int)$seller_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_payment WHERE seller_id = '" . (int)$seller_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE `query` = 'seller_id=".(int)$seller_id."'");
	}
}

?>
