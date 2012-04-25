<?php
class MsRequest {
	const MS_REQUEST_PRODUCT_CREATED = 1;
	const MS_REQUEST_PRODUCT_UPDATED = 2;
	const MS_REQUEST_SELLER_CREATED = 3;
	const MS_REQUEST_WITHDRAWAL = 4;
	
	private $errors;
	private $fileName;
		
  	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
		
		$this->errors = array();
	}
	
	public static function byName($registry, $name) {
		$instance = new self($registry);
        $instance->fileName = $name;
        return $instance;
	}
	
	public function createRequest($data) {
		$created_message = isset($data['created_message']) ? $this->db->escape($data['created_message']) : '';		
		$seller_id = isset($data['seller_id']) ? (int)$data['seller_id'] : 'NULL';
		$product_id = isset($data['product_id']) ? (int)$data['product_id'] : 'NULL';
		
		$sql = "INSERT INTO " . DB_PREFIX . "ms_request
				SET seller_id = " . $seller_id  . ",
					product_id = " . $product_id . ",
					request_type = " . (int)$data['request_type'] . ",
					created_message = '" . $created_message . "',
					date_created = NOW()";

		$this->db->query($sql);
	}
	
	public function processRequest($request_id, $processed_by, $message = '') {
		$sql = "UPDATE " . DB_PREFIX . "ms_request
				SET processed_message = '" . $this->db->escape($message) . "',
					processed_by_user_id = " . (int)$processed_by . ",
					date_processed = NOW()
				WHERE request_id = " . (int)$request_id;
		
		$this->db->query($sql);
	}

	public function processSellerRequests($seller_id, $processed_by, $message = '') {
		$sql = "SELECT * FROM " . DB_PREFIX . "ms_request
				WHERE request_type = " . (int)self::MS_REQUEST_SELLER_CREATED . "
				AND seller_id = " . (int)$seller_id . "
				AND date_processed  IS NOT NULL";
		
		$res = $this->db->query($sql);
		
		foreach ($res->rows as $row) {
			$this->processRequest($row['request_id'], $processed_by, $message);
		}
	}
	
	public function processProductRequests($product_id, $processed_by, $message = '') {
		$sql = "SELECT * FROM " . DB_PREFIX . "ms_request
				WHERE (request_type = " . (int)self::MS_REQUEST_PRODUCT_CREATED . " OR request_type = " . (int)self::MS_REQUEST_PRODUCT_UPDATED . ")
				AND product_id = " . (int)$product_id . "
				AND date_processed  IS NOT NULL";
		
		$res = $this->db->query($sql);
		
		foreach ($res->rows as $row) {
			$this->processRequest($row['request_id'], $processed_by, $message);
		}
	}	
	
	public function getRequests($type) {
		$sql = "SELECT * FROM " . DB_PREFIX . "ms_request
				WHERE request_type = " . (int)$type;
		
		$res = $this->db->query($sql);
		return $res->rows;
	}
	
	public function getWithdrawalRequests() {
		$sql = "SELECT 	*,
						mr.request_id as 'req.id',
						ms.nickname as 'sel.nickname',
						mt.amount as 'trn.amount',
					   	mr.date_created as 'req.date_created',
					   	mr.date_processed as 'req.date_processed',
						u.username as 'u.username'
				FROM " . DB_PREFIX . "ms_request mr
				INNER JOIN	" . DB_PREFIX . "ms_transaction mt
					USING(transaction_id)
				INNER JOIN	" . DB_PREFIX . "ms_seller ms
					ON mt.seller_id = ms.seller_id
				LEFT JOIN	" . DB_PREFIX . "user u
					ON mr.processed_by_user_id = u.user_id
				WHERE mr.request_type = " . (int)self::MS_REQUEST_WITHDRAWAL;
		
		$res = $this->db->query($sql);
		return $res->rows;
	}

	public function getRequestPaymentData($request_id) {
		$sql = "SELECT 	*,
						mr.request_id as 'req.id',
						ms.nickname as 'sel.nickname',
						ms.paypal as 'sel.paypal',
						mt.amount as 'trn.amount'
				FROM " . DB_PREFIX . "ms_request mr
				INNER JOIN	" . DB_PREFIX . "ms_transaction mt
					USING(transaction_id)
				INNER JOIN	" . DB_PREFIX . "ms_seller ms
					ON mt.seller_id = ms.seller_id
				WHERE mr.request_id = " . (int)$request_id;
		
		$res = $this->db->query($sql);
		return $res->row;
	}	
}
?>