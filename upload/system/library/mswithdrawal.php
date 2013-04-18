<?php
class MsWithdrawal extends Model {
	const STATUS_PENDING = 1;
	const STATUS_PAID = 2;
	const STATUS_MARKEDASPAID = 3;
	const STATUS_DECLINED = 4;
	const STATUS_REVOKED = 5;
	
	public function getWithdrawals($data, $sort = array()) {
		$sql = "SELECT *,
						mw.amount as 'mw.amount',
						mw.currency_code as 'mw.currency_code',
						mw.date_created as 'mw.date_created',
						mw.date_processed as 'mw.date_processed',
						mw.withdrawal_status as 'mw.withdrawal_status',
						ms.seller_id as 'seller_id',
						ms.nickname as 'ms.nickname',
						ms.paypal as 'ms.paypal',
						u.username as 'u.username'
				FROM " . DB_PREFIX . "ms_withdrawal mw
				INNER JOIN " . DB_PREFIX . "ms_seller ms
					USING (seller_id)
				LEFT JOIN " . DB_PREFIX . "user u
					ON (u.user_id = mw.processed_by)
				WHERE 1 = 1 "
				. (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '')
				. (isset($data['withdrawal_id']) ? " AND withdrawal_id =  " .  (int)$data['withdrawal_id'] : '')				
				. (isset($data['currency_id']) ? " AND seller_id =  " .  (int)$data['currency_id'] : '')
				. (isset($data['withdrawal_status']) ? " AND withdrawal_status IN  (" .  $this->db->escape(implode(',', $data['withdrawal_status'])) . ")" : '')

				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
    			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);

		return $res->rows;
	}
}
?>