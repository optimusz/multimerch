<?php
class MsRequest extends Model {
	const STATUS_PENDING = 1;
	const STATUS_PROCESSED = 2;

	const RESOLUTION_APPROVED = 1;
	const RESOLUTION_DECLINED = 2;
	const RESOLUTION_CLOSED = 3;
	const RESOLUTION_REVOKED = 4;
	
	public function createRequestData($data) {
		$sql = "INSERT INTO " . DB_PREFIX . "ms_request
				SET request_status = " . (int)self::STATUS_PENDING . ",
					date_created = NOW(),
		            message_created = '" . (isset($data['message']) ? $this->db->escape($data['message']) : '') . "'";

		$this->db->query($sql);

		$request_id = mysql_insert_id();
	
		return $request_id;
	}
	
	public function getTotalRequests($request_type) {
		$sql = "SELECT COUNT(*) as 'total'
				FROM " . DB_PREFIX . "ms_request
				WHERE request_type = " . (int)$request_type;

		$res = $this->db->query($sql);

		return $res->row['total'];
	}
	
	/*
	public function getWithdrawalRequestPaymentData($request_id) {
		$sql = "SELECT 	*,
						mr.request_id as 'mr.request_id',
						mrw.amount as 'mrw.amount',
						ms.nickname as 'ms.nickname',
						ms.paypal as 'ms.paypal'
				FROM " . DB_PREFIX . "ms_request mr
				INNER JOIN	" . DB_PREFIX . "ms_request_withdrawal mrw
					USING(request_id)
				INNER JOIN	" . DB_PREFIX . "ms_seller ms
					USING(seller_id)
				WHERE mr.request_id = " . (int)$request_id;
		
		$res = $this->db->query($sql);
		return $res->row;
	}
	*/	
	
	/* PROCESS */
	
	public function processRequest($request_id, $data) {
		$sql = "UPDATE " . DB_PREFIX . "ms_request
				SET message_processed = '" . (isset($data['message']) ? $this->db->escape($data['message']) : '') . "',
			 		request_status = " . (int)self::STATUS_PROCESSED . ",
			 		resolution_type = " . (int)$data['resolution_type'] . ",
             		processed_by = " . (isset($data['processed_by']) ? (int)$data['processed_by'] : 'NULL') . ",
			 		date_processed = NOW()
				WHERE request_id = " . (int)$request_id;
		
		$this->db->query($sql);
	}
	
	//todo
	public function revokeRequest($request_id, $data) {
		$sql = "UPDATE " . DB_PREFIX . "ms_request
				SET message_processed = '" . $this->db->escape('Revoked by user') . "',
			 		request_status = " . (int)self::MS_REQUEST_STATUS_REVOKED . ",
			 		date_processed = NOW()
				WHERE request_id = " . (int)$request_id;
		
		$this->db->query($sql);
	}
	
	//todo
	public function getRequests($data, $sort) {
		$sql = "SELECT *
				FROM " . DB_PREFIX . "ms_request mr
				INNER JOIN " . DB_PREFIX . "ms_seller ms
					ON (mb.seller_id = ms.seller_id)
    			ORDER BY {$sort['order_by']} {$sort['order_way']}"
    			. ($sort['limit'] ? " LIMIT ".(int)(($sort['page'] - 1) * $sort['limit']).', '.(int)($sort['limit']) : '');
		$res = $this->db->query($sql);

		return $res->rows;
	}
}
?>