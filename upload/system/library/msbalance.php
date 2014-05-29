<?php
class MsBalance extends Model {
	const MS_BALANCE_TYPE_SALE = 1;
	const MS_BALANCE_TYPE_REFUND = 2;
	const MS_BALANCE_TYPE_WITHDRAWAL = 3;
	const MS_BALANCE_TYPE_GENERIC = 4;
	const MS_BALANCE_TYPE_LISTING = 5;
	const MS_BALANCE_TYPE_SIGNUP = 6;
	const MS_BALANCE_TYPE_RECURRING = 7;
	
	public function getSellerBalanceEntries($seller_id, $sort) {
		$sql = "SELECT *
				FROM " . DB_PREFIX . "ms_balance
				WHERE seller_id = " . (int)$seller_id . "
				ORDER BY {$sort['order_by']} {$sort['order_way']}"
				. ($sort['limit'] ? " LIMIT ".(int)(($sort['page'] - 1) * $sort['limit']).', '.(int)($sort['limit']) : '');
		$res = $this->db->query($sql);

		return $res->rows;
	}
	
	public function getTotalSellerBalanceEntries($seller_id) {
		$sql = "SELECT COUNT(*) as 'total'
				FROM " . DB_PREFIX . "ms_balance
				WHERE seller_id = " . (int)$seller_id;
				
		$res = $this->db->query($sql);

		return $res->row['total'];
	}

	public function getBalanceEntries($data = array(), $sort = array()) {
		$filters = '';
		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				$filters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
			}
		}

		// todo fix other getBalanceEntries calls
		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					*,
					mb.description as 'mb.description',
					mb.date_created as 'mb.date_created'
				FROM " . DB_PREFIX . "ms_balance mb
				INNER JOIN " . DB_PREFIX . "ms_seller ms
					ON (mb.seller_id = ms.seller_id)
				WHERE 1 = 1"
				. (isset($data['order_id']) ? " AND mb.order_id =  " .  (int)$data['order_id'] : '')
				. (isset($data['product_id']) ? " AND mb.product_id =  " .  (int)$data['product_id'] : '')
				. (isset($data['seller_id']) ? " AND mb.seller_id =  " .  (int)$data['seller_id'] : '')
				. (isset($data['withdrawal_id']) ? " AND seller_id =  " .  (int)$data['withdrawal_id'] : '')
				. (isset($data['balance_type']) ? " AND balance_type =  " .  (int)$data['balance_type'] : '')
				. $filters
				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
				. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');
		$res = $this->db->query($sql);

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];

		return $res->rows;
	}
	
	public function getBalanceEntry($data) {
		$sql = "SELECT *
				FROM " . DB_PREFIX . "ms_balance mb
				WHERE 1 = 1 "
				. (isset($data['order_id']) ? " AND order_id =  " .  (int)$data['order_id'] : '')
				. (isset($data['product_id']) ? " AND product_id =  " .  (int)$data['product_id'] : '')
				. (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '')
				. (isset($data['withdrawal_id']) ? " AND seller_id =  " .  (int)$data['withdrawal_id'] : '')
				. (isset($data['balance_type']) ? " AND balance_type =  " .  (int)$data['balance_type'] : '')
				. " LIMIT 1";
				
		$res = $this->db->query($sql);
		
		if ($res->num_rows)
			return $res->row;
		else
			return FALSE;
	}	
	
	public function getTotalBalanceEntries() {
		$sql = "SELECT COUNT(*) as 'total'
				FROM " . DB_PREFIX . "ms_balance";
				
		$res = $this->db->query($sql);

		return $res->row['total'];
	}

	public function getTotalBalanceAmount($data = array()) {
		$sql = "SELECT COALESCE(
					(SELECT SUM(balance) FROM " . DB_PREFIX . "ms_balance
					 WHERE balance_id IN (
						SELECT MAX(balance_id) FROM " . DB_PREFIX . "ms_balance
						LEFT JOIN " . DB_PREFIX . "ms_seller
							USING(seller_id)"
						. (isset($data['seller_status']) ? " WHERE seller_status IN  (" .  $this->db->escape(implode(',', $data['seller_status'])) . ")" : '') .
						" GROUP BY seller_id
					)),
					0
				) as total";
		$res = $this->db->query($sql);

		return $res->row['total'];
	}


	public function getSellerBalance($seller_id) {
		// note: update getSellers() if updating this
		$sql = "SELECT COALESCE(
					(SELECT balance FROM " . DB_PREFIX . "ms_balance
						WHERE seller_id = " . (int)$seller_id . " 
						ORDER BY balance_id DESC
						LIMIT 1
					),
					0
				) as balance";
		$res = $this->db->query($sql);

		return $res->row['balance'];
	}

	public function getAvailableSellerFunds($seller_id) {
		return max(0, $this->getSellerBalance($seller_id) - $this->getReservedSellerFunds($seller_id) - $this->getWaitingSellerFunds($seller_id, $this->config->get('msconf_withdrawal_waiting_period')));
	}

	public function getReservedSellerFunds($seller_id) {
		$sql = "SELECT SUM(amount) as total
				FROM " . DB_PREFIX . "ms_payment
				WHERE seller_id = " . (int)$seller_id . "
				AND payment_type IN (" . (int)MsPayment::TYPE_PAYOUT . "," . (int)MsPayment::TYPE_PAYOUT_REQUEST . ")
				AND payment_status = " . (int)MsPayment::STATUS_UNPAID;
				
		$res = $this->db->query($sql);

		return $res->row['total'];
	}
	
	public function getWaitingSellerFunds($seller_id) {
		$sql = "SELECT SUM(amount) as total
				FROM " . DB_PREFIX . "ms_balance
				WHERE seller_id = " . (int)$seller_id . " 
				AND balance_type = " . (int)MsBalance::MS_BALANCE_TYPE_SALE . "
				AND DATEDIFF(NOW(), date_created) < " . (int)$this->config->get('msconf_withdrawal_waiting_period');
				
		$res = $this->db->query($sql);
		
		$pending_diff = $this->getSellerBalance($seller_id) - $this->getReservedSellerFunds($seller_id);
		
		if ($pending_diff >= $res->row['total'])
			return $res->row['total'];
		else
			return $pending_diff;
	}	
	
	public function addBalanceEntry($seller_id, $data) {
		$sql = "INSERT INTO " . DB_PREFIX . "ms_balance
				SET seller_id = " . (int)$seller_id . ",
					order_id = " . (isset($data['order_id']) ? (int)$data['order_id'] : 'NULL') . ",
					product_id = " . (isset($data['product_id']) ? (int)$data['product_id'] : 'NULL') . ",
					withdrawal_id = " . (isset($data['withdrawal_id']) ? (int)$data['withdrawal_id'] : 'NULL') . ",
					balance_type = " . (int)$data['balance_type'] . ",
					amount = ". (float)$this->currency->format($data['amount'], $this->config->get('config_currency'), '', FALSE) . ",
					balance = amount + (
						SELECT balance FROM (
							SELECT COALESCE(
								(SELECT balance FROM " . DB_PREFIX . "ms_balance
						  			WHERE seller_id = " . (int)$seller_id . "
						  			ORDER BY balance_id DESC LIMIT 1),
								0
							) as balance
						) as tmpTable
					),
					description = '" . $this->db->escape($data['description']) . "',
					date_created = NOW()";

		$this->db->query($sql);
		
		$balance_id = $this->db->getLastId();
		return $balance_id;
	}
}
?>
