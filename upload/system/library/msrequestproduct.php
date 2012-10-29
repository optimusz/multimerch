<?php
class MsRequestProduct extends Model {
	const TYPE_PRODUCT_CREATE = 1;
	const TYPE_PRODUCT_UPDATE = 2;
	const TYPE_PRODUCT_DELETE = 3;
		
	public function createProductRequest($product_id, $data) {
		$request_id = $this->MsLoader->MsRequest->createRequestData($data);
		
		$sql = "INSERT INTO " . DB_PREFIX . "ms_request_product
				SET request_id = " . (int)$request_id  . ",
					product_id = " . (int)$product_id  . ",
					request_type = " . (int)$data['request_type'];

		$this->db->query($sql);
		
		return $request_id;
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
}
?>