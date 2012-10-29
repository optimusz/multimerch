<?php
class MsRequestWithdrawal extends Model {
	const TYPE_WITHDRAWAL_SUBMIT = 1;

	public function createWithdrawalRequest($seller_id, $data) {
		$request_id = $request_id = $this->MsLoader->MsRequest->createRequestData($data);
				
		$sql = "INSERT INTO " . DB_PREFIX . "ms_request_withdrawal
				SET request_id = " . (int)$request_id  . ",
					seller_id = " . (int)$seller_id  . ",
					request_type = " . (int)$data['request_type'] . ",
             		withdrawal_method_id = 0,
             		withdrawal_method_data = '',
             		amount = " . (float)$data['amount'] . ",
             		currency_id = " . (int)$data['currency_id'] . ",
             		currency_code = '" . $this->db->escape($data['currency_code']) . "',
             		currency_value = " . (float)$data['currency_value'];

		$this->db->query($sql);

		return $request_id;
	}
	
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
}
?>