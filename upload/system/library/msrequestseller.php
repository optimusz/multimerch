<?php
class MsRequestSeller extends Model {
	const TYPE_SELLER_CREATE = 1;
	const TYPE_SELLER_UPDATE = 2;
	const TYPE_SELLER_DELETE = 3;
		
	public function createSellerRequest($seller_id, $data) {
		$request_id = $this->MsLoader->MsRequest->createRequestData($data);
		
		$sql = "INSERT INTO " . DB_PREFIX . "ms_request_seller
				SET request_id = " . (int)$request_id  . ",
					seller_id = " . (int)$seller_id . ",
					request_type = " . (int)$data['request_type'];

		$this->db->query($sql);
		
		return $request_id;
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
}
?>