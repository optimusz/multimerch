<?php
class MsRequest extends Model {
	const MS_REQUEST_STATUS_PENDING = 1;
	
	const MS_REQUEST_STATUS_DECLINED = 2;
	const MS_REQUEST_STATUS_APPROVED = 3;
	const MS_REQUEST_STATUS_CLOSED = 4;
	const MS_REQUEST_STATUS_REVOKED = 5;

	const MS_REQUEST_TYPE_SELLER_CREATE = 1;
	const MS_REQUEST_TYPE_SELLER_UPDATE = 2;
	const MS_REQUEST_TYPE_SELLER_DELETE = 3;
		
	const MS_REQUEST_TYPE_PRODUCT_CREATE = 4;
	const MS_REQUEST_TYPE_PRODUCT_UPDATE = 5;	
	const MS_REQUEST_TYPE_PRODUCT_DELETE = 6;
	
	const MS_REQUEST_TYPE_WITHDRAWAL_CREATE = 7;
		
  	public function __construct($registry) {
  		parent::__construct($registry);
	}

	/* CREATE */
	
	public function createRequestData($data) {
		$sql = "INSERT INTO " . DB_PREFIX . "ms_request_data
				SET request_type = " . (int)$data['request_type'] . ",
					request_status = " . (int)self::MS_REQUEST_STATUS_PENDING . ",
					date_created = NOW(),
		            message_created = '" . (isset($data['message']) ? $this->db->escape($data['message']) : '') . "'";

		$this->db->query($sql);

		$request_id = mysql_insert_id();
	
		return $request_id;
	}
	
	public function createSellerRequest($seller_id, $data) {
		$request_id = $this->createRequestData($data);
		
		$sql = "INSERT INTO " . DB_PREFIX . "ms_request_seller
				SET request_id = " . (int)$request_id  . ",
					seller_id = " . (int)$seller_id;

		$this->db->query($sql);
		
		return $request_id;
	}
	
	public function createProductRequest($product_id, $data) {
		$request_id = $this->createRequestData($data);
		
		$sql = "INSERT INTO " . DB_PREFIX . "ms_request_product
				SET request_id = " . (int)$request_id  . ",
					product_id = " . (int)$product_id;

		$this->db->query($sql);
		
		return $request_id;
	}
	
	public function createWithdrawalRequest($seller_id, $data) {
		$request_id = $this->createRequestData($data);
				
		$sql = "INSERT INTO " . DB_PREFIX . "ms_request_withdrawal
				SET request_id = " . (int)$request_id  . ",
					seller_id = " . (int)$seller_id  . ",
             		withdrawal_method_id = 0,
             		withdrawal_method_data = '',
             		amount = " . (float)$data['amount'] . ",
             		currency_id = " . (int)$data['currency_id'] . ",
             		currency_code = '" . $this->db->escape($data['currency_code']) . "',
             		currency_value = " . (float)$data['currency_value'];

		$this->db->query($sql);

		return $request_id;
	}
	
	
	/* RETRIEVE */	
	
	public function getWithdrawalRequests($data, $sort) {
		$sql = "SELECT *,
						mrw.amount as 'mrw.amount',
						mrw.currency_code as 'mrw.currency_code',
						mrd.message_created as 'mrd.message_created',
						mrd.date_created as 'mrd.date_created',
						mrd.date_processed as 'mrd.date_processed',
						ms.seller_id as 'ms.seller_id',
						ms.nickname as 'ms.nickname',
						ms.paypal as 'ms.paypal',
						u.username as 'u.username'
				FROM " . DB_PREFIX . "ms_request_data mrd
				INNER JOIN " . DB_PREFIX . "ms_request_withdrawal mrw
					USING (request_id)
				INNER JOIN " . DB_PREFIX . "ms_seller ms
					USING (seller_id)
				LEFT JOIN " . DB_PREFIX . "user u
					ON (u.user_id = mrd.processed_by)
				WHERE 1 = 1 "
				. (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '')
				. (isset($data['request_id']) ? " AND request_id =  " .  (int)$data['request_id'] : '')				
				. (isset($data['payout_method_id']) ? " AND payout_method_id =  " .  (int)$data['payout_method_id'] : '')
				. (isset($data['currency_id']) ? " AND seller_id =  " .  (int)$data['currency_id'] : '')

				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
    			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['page'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);

		if ($res->num_rows == 1)
			return $res->row;
		else
			return $res->rows;
	}

	public function getSellerRequests($data, $sort = array()) {
		$sql = "SELECT *
				FROM " . DB_PREFIX . "ms_request_data mrd
				INNER JOIN " . DB_PREFIX . "ms_request_seller mrs
					USING (request_id)
				INNER JOIN " . DB_PREFIX . "ms_seller ms
					USING (seller_id)
				WHERE 1 = 1 "
				. (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '')
				. (isset($data['request_type']) ? " AND request_type IN  (" .  $this->db->escape(implode(',', $data['request_type'])) . ")" : '')
				. (isset($data['request_status']) ? " AND request_status IN  (" .  $this->db->escape(implode(',', $data['request_status'])) . ")" : '')

				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
    			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['page'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);

		return $res->rows;
	}
	
	public function getProductRequests($data, $sort) {
		$sql = "
			SELECT *
			FROM " . DB_PREFIX . "ms_request_data mrd
			INNER JOIN " . DB_PREFIX . "ms_request_product mrp
				USING (request_id)
			INNER JOIN " . DB_PREFIX . "ms_product mp
				USING (product_id)
			INNER JOIN " . DB_PREFIX . "product p
				USING (product_id)
			INNER JOIN " . DB_PREFIX . "ms_seller ms
				USING (seller_id)
			WHERE 1 = 1 "
			. (isset($data['product_id']) ? " AND product_id =  " .  (int)$data['product_id'] : '')

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['page'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);

		return $res->rows;
	}
	
	public function getTotalRequests($request_type) {
		$sql = "SELECT COUNT(*) as 'total'
				FROM " . DB_PREFIX . "ms_request_data
				WHERE request_type = " . (int)$request_type;

		$res = $this->db->query($sql);

		return $res->row['total'];
	}
	
	/*
	public function getWithdrawalRequestPaymentData($request_id) {
		$sql = "SELECT 	*,
						mrd.request_id as 'mrd.request_id',
						mrw.amount as 'mrw.amount',
						ms.nickname as 'ms.nickname',
						ms.paypal as 'ms.paypal'
				FROM " . DB_PREFIX . "ms_request_data mrd
				INNER JOIN	" . DB_PREFIX . "ms_request_withdrawal mrw
					USING(request_id)
				INNER JOIN	" . DB_PREFIX . "ms_seller ms
					USING(seller_id)
				WHERE mrd.request_id = " . (int)$request_id;
		
		$res = $this->db->query($sql);
		return $res->row;
	}
	*/	
	
	/* PROCESS */
	
	public function processRequest($request_id, $data) {
		$sql = "UPDATE " . DB_PREFIX . "ms_request_data
				SET message_processed = '" . (isset($data['message']) ? $this->db->escape($data['message']) : '') . "',
			 		request_status = " . (int)$data['request_status'] . ",
             		processed_by = " . (isset($data['processed_by']) ? (int)$data['processed_by'] : 'NULL') . ",
			 		date_processed = NOW()
				WHERE request_id = " . (int)$request_id;
		
		$this->db->query($sql);
	}
	
	public function revokeRequest($request_id, $data) {
		$sql = "UPDATE " . DB_PREFIX . "ms_request_data
				SET message_processed = '" . $this->db->escape('Revoked by user') . "',
			 		request_status = " . (int)self::MS_REQUEST_STATUS_REVOKED . ",
			 		date_processed = NOW()
				WHERE request_id = " . (int)$request_id;
		
		$this->db->query($sql);
	}
	
	//
	public function getRequests($data, $sort) {
		$sql = "SELECT *
				FROM " . DB_PREFIX . "ms_request_data mrd
				INNER JOIN " . DB_PREFIX . "ms_seller ms
					ON (mb.seller_id = ms.seller_id)
    			ORDER BY {$sort['order_by']} {$sort['order_way']}"
    			. ($sort['limit'] ? " LIMIT ".(int)(($sort['page'] - 1) * $sort['limit']).', '.(int)($sort['limit']) : '');
		$res = $this->db->query($sql);

		return $res->rows;
	}
}
?>