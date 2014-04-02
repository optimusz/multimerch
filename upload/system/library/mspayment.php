<?php
class MsPayment extends Model {
	const METHOD_BALANCE = 1;
	const METHOD_PAYPAL = 2;
	const METHOD_PAYPAL_ADAPTIVE = 3;
	
	const TYPE_SIGNUP = 1;
	const TYPE_LISTING = 2;
	const TYPE_PAYOUT = 3;
	const TYPE_PAYOUT_REQUEST = 4;
	const TYPE_RECURRING = 5;
	const TYPE_SALE = 6;
		
	const STATUS_UNPAID = 1;
	const STATUS_PAID = 2;
	
	public function createPayment($data) {
		$sql = "INSERT INTO `" . DB_PREFIX . "ms_payment`
				SET seller_id = " . (int)$data['seller_id'] . ",
					product_id = " . (isset($data['product_id']) ? (int)$data['product_id'] : 'NULL') . ",
					order_id = " . (isset($data['order_id']) ? (int)$data['order_id'] : 'NULL') . ",
					payment_type = " . (int)$data['payment_type'] . ",
					payment_status = " . (int)$data['payment_status'] . ",
					payment_method = " . (int)$data['payment_method'] . ",
					payment_data = '" . (isset($data['payment_data']) ? $this->db->escape($data['payment_data']) : '') . "',
					amount = ". (float)$this->MsLoader->MsHelper->uniformDecimalPoint($data['amount']) . ",
					currency_id = " . (int)$data['currency_id'] . ",
					currency_code = '" . $this->db->escape($data['currency_code']) . "',
					description = '" . (isset($data['description']) ? $this->db->escape($data['description']) : '') . "',
					date_created = NOW()";

		$this->db->query($sql);
		return $this->db->getLastId();
	}
	
	public function updatePayment($payment_id, $data) {
		$sql = "UPDATE `" . DB_PREFIX . "ms_payment`
				SET payment_id = payment_id"
					. (isset($data['seller_id']) ? ", seller_id = " . (int)$data['seller_id'] : '')
					. (isset($data['product_id']) ? ", product_id = " . (int)$data['product_id'] : '')
					. (isset($data['order_id']) ? ", order_id = " . (int)$data['order_id'] : '')					
					. (isset($data['payment_type']) ? ", payment_type = " . (int)$data['payment_type'] : '')
					. (isset($data['payment_status']) ? ", payment_status = " . (int)$data['payment_status'] : '')
					. (isset($data['payment_method']) ? ", payment_method = " . (int)$data['payment_method'] : '')
					. (isset($data['payment_data']) ? ", payment_data = '" . $this->db->escape($data['payment_data']) . "'" : '')
					. (isset($data['amount']) ? ", amount = " . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($data['amount']) : '')
					. (isset($data['currency_id']) ? ", currency_id = " . (int)$data['currency_id'] : '')
					. (isset($data['currency_code']) ? ", currency_code = " . $this->db->escape($data['currency_code']) : '')
					. (isset($data['description']) ? ", description = '" . $this->db->escape($data['description']) . "'" : '')
					. (isset($data['date_created']) ? ", date_created = NOW()" : '')
					. (isset($data['date_paid']) && !is_null($data['date_paid']) ? ", date_paid = '" . $this->db->escape($data['date_paid']) . "'" : ", date_paid = NULL") . "
				WHERE payment_id = " . (int)$payment_id;

		return $this->db->query($sql);
	}
	
	public function getPayments($data = array(), $sort = array()) {
		$filters = '';
		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				$filters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
			}
		}

		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					payment_id,
					payment_type,
					payment_status,
					payment_method,
					payment_data,
					amount,
					currency_code,
					mpay.date_created as 'mpay.date_created',
					mpay.date_paid as 'mpay.date_paid',
					mpay.description as 'mpay.description',
					ms.seller_id as 'seller_id',
					ms.nickname,
					ms.paypal,
					product_id,
					order_id
				FROM `" . DB_PREFIX . "ms_payment` mpay
				LEFT JOIN `" . DB_PREFIX . "ms_seller` ms
					USING (seller_id)
				WHERE 1 = 1 "
				. (isset($data['payment_id']) ? " AND payment_id =  " .  (int)$data['payment_id'] : '')
				. (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '')
				. (isset($data['product_id']) ? " AND product_id =  " .  (int)$data['product_id'] : '')
				. (isset($data['order_id']) ? " AND order_id =  " .  (int)$data['order_id'] : '')
				. (isset($data['currency_id']) ? " AND currency_id =  " .  (int)$data['currency_id'] : '')
				. (isset($data['payment_type']) ? " AND payment_type IN  (" .  $this->db->escape(implode(',', $data['payment_type'])) . ")" : '')
				. (isset($data['payment_method']) ? " AND payment_method IN  (" .  $this->db->escape(implode(',', $data['payment_method'])) . ")" : '')
				. (isset($data['payment_status']) ? " AND payment_status IN  (" .  $this->db->escape(implode(',', $data['payment_status'])) . ")" : '')

				. $filters

				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
				. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);
		
		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];
		
		return ($res->num_rows == 1 && isset($data['single']) ? $res->row : $res->rows);
	}
	
	public function getTotalPayments($data){
		$sql = "SELECT COUNT(*) as 'total'
				FROM `" . DB_PREFIX . "ms_payment`
				WHERE 1 = 1 "
				. (isset($data['payment_id']) ? " AND payment_id =  " .  (int)$data['payment_id'] : '')
				. (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '')
				. (isset($data['product_id']) ? " AND product_id =  " .  (int)$data['product_id'] : '')
				. (isset($data['order_id']) ? " AND order_id =  " .  (int)$data['order_id'] : '')
				. (isset($data['currency_id']) ? " AND currency_id =  " .  (int)$data['currency_id'] : '')
				. (isset($data['payment_type']) ? " AND payment_type IN  (" .  $this->db->escape(implode(',', $data['payment_type'])) . ")" : '')
				. (isset($data['payment_method']) ? " AND payment_method IN  (" .  $this->db->escape(implode(',', $data['payment_method'])) . ")" : '')
				. (isset($data['payment_status']) ? " AND payment_status IN  (" .  $this->db->escape(implode(',', $data['payment_status'])) . ")" : '');
				
		$res = $this->db->query($sql);
		return $res->row['total'];		
	}
	
	public function getTotalAmount($data) {
		$sql = "SELECT SUM(amount) as 'total'
				FROM `" . DB_PREFIX . "ms_payment`
				WHERE 1 = 1 "
				. (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '')
				. (isset($data['payment_type']) ? " AND payment_type IN  (" .  $this->db->escape(implode(',', $data['payment_type'])) . ")" : '')
				. (isset($data['payment_status']) ? " AND payment_status IN  (" .  $this->db->escape(implode(',', $data['payment_status'])) . ")" : '');
				
		$res = $this->db->query($sql);

		return $res->row['total'];		
	}	
	
	public function deletePayment($payment_id) {
		$sql = "DELETE FROM `" . DB_PREFIX . "ms_payment`
				WHERE payment_id = " . (int)$payment_id;
		
		$this->db->query($sql);
	}
}
?>