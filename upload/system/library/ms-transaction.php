<?php
final class MsTransaction extends Model {
	const MS_TRANSACTION_TYPE_PRODUCT = 1;
	const MS_TRANSACTION_TYPE_WITHDRAWAL = 2;
	
	const MS_TRANSACTION_STATUS_COMPLETE = 1;
	const MS_TRANSACTION_STATUS_PENDING = 2;
	const MS_TRANSACTION_STATUS_CLOSED = 0;
	
	private $data;
	
	private function _modelExists($model) {
		$file  = DIR_APPLICATION . 'model/' . $model . '.php';
		return file_exists($file);
	}
	
  	public function __construct($registry) {
  		parent::__construct($registry);
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
		$this->load = $registry->get('load');
		$this->language = $registry->get('language');
		$this->load->language('module/multiseller');
		
		require_once(DIR_SYSTEM . 'library/ms-seller.php');
		require_once(DIR_SYSTEM . 'library/ms-product.php');
		$this->msSeller = new MsSeller($registry);
		$this->msProduct = new MsProduct($registry);
	}
	
	private function _getOrderProducts($order_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "order_product
				WHERE order_id = " . (int)$order_id;
		
		$res = $this->db->query($sql);

		return $res->rows;
		
	}
	
	public function getTotalTransactions($includingClosed = FALSE) {
		$sql = "SELECT COUNT(*) as total FROM " . DB_PREFIX . "ms_transaction"
				. (!$includingClosed ? " WHERE (transaction_status_id != " . (int)self::MS_TRANSACTION_STATUS_CLOSED . ")" : '');
		
		$res = $this->db->query($sql);
		return $res->row['total'];
		
	}	
	
	public function getTransactionData($transaction_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "ms_transaction
				WHERE transaction_id = " . (int)$transaction_id;
		
		$res = $this->db->query($sql);
		return $res->row;
	}
	
	public function copyTransaction($transaction_id) {
		$sql = "INSERT INTO " . DB_PREFIX . "ms_transaction
					(parent_transaction_id, amount, seller_id, order_id, product_id, transaction_status_id, currency_id, currency_code, currency_value, commission, description, date_created, date_modified, type)
				SELECT parent_transaction_id, amount, seller_id, order_id, product_id, transaction_status_id, currency_id, currency_code, currency_value, commission, description, date_created, date_modified, type 
				FROM " . DB_PREFIX . "ms_transaction WHERE transaction_id = " . (int)$transaction_id;
		$this->db->query($sql);
		
		return mysql_insert_id();
	}

	public function closeTransaction($transaction_id) {
		$sql = "UPDATE " . DB_PREFIX . "ms_transaction
				SET transaction_status_id = " . (int)self::MS_TRANSACTION_STATUS_CLOSED . ",
					date_modified = NOW()
				WHERE transaction_id = " . (int)$transaction_id;

		$this->db->query($sql);
	}
	
	public function getTransactionsForOrder($order_id, $includingClosed = FALSE) {
		$sql = "SELECT transaction_id, product_id FROM " . DB_PREFIX . "ms_transaction
				WHERE order_id = " . (int)$order_id
				. (!$includingClosed ? " AND (transaction_status_id != " . (int)self::MS_TRANSACTION_STATUS_CLOSED . ")" : '');				
		
		$res = $this->db->query($sql);

		$result = array();
		
		foreach ($res->rows as $r) {
			$result[$r['product_id']] = $r['transaction_id'];
		}
		
		return $result;
	}

	public function addTransaction($data) {
			$sql = "INSERT INTO " . DB_PREFIX . "ms_transaction
					SET parent_transaction_id = " . (int)$data['parent_transaction_id'] . ",
						order_id = " . (int)$data['order_id'] . ",
						product_id = " .(int)$data['product_id'] . ",
						seller_id = " . (int)$data['seller_id'] . ",
						amount = ". (float)$data['amount'] . ",
						currency_id = ". (int)$data['currency_id'] . ",
						currency_code = '" . $this->db->escape($data['currency_code']) . "',
						currency_value = " . (float)$data['currency_value'] . ",
						commission = " . (float)$data['commission'] . ",
						description = '" . $this->db->escape($data['description']) . "',
						date_created = NOW(),
						date_modified = NOW()";

			$this->db->query($sql);
			
			$transaction_id = mysql_insert_id();
			
			if ((int)$data['parent_transaction_id'] == 0) {
				$this->db->query("UPDATE " . DB_PREFIX . "ms_transaction SET parent_transaction_id = LAST_INSERT_ID() WHERE transaction_id = LAST_INSERT_ID()");
			}
			
			return $transaction_id;
	}
	
	public function addTransactionsForOrder($order_id, $debit = FALSE) {
		$this->load->model('module/multiseller/seller');
		
		if ($this->_modelExists('checkout/order')) {
			$this->load->model('checkout/order');
			$order_info = $this->model_checkout_order->getOrder($order_id);			
		} else {
			$this->load->model('sale/order');
			$order_info = $this->model_sale_order->getOrder($order_id);			
		}
		
		$order_products = $this->_getOrderProducts($order_id);
		
		if (!$order_products)
			return false;
		
		$parent_transactions = $this->getTransactionsForOrder($order_id);
		
		foreach ($order_products as $product) {
			$seller_id = $this->msProduct->getSellerId($product['product_id']);
			$parent_tr_id = isset($parent_transactions[$product['product_id']]) ? $parent_transactions[$product['product_id']] : NULL;

			if ($debit)
				$product['total'] = -1 * abs($product['total']);

			$description = sprintf($this->language->get('ms_transaction_sale'),$product['name'],$this->msSeller->getCommissionForSeller($seller_id));

			$sql = "INSERT INTO " . DB_PREFIX . "ms_transaction
					SET parent_transaction_id = " . (int)$parent_tr_id . ",
						order_id = " . (int)$order_id . ",
						product_id = " .(int)$product['product_id'] . ",
						seller_id = " . (int)$seller_id . ",
						amount = ". $product['total'] . ",
						currency_id = ". $order_info['currency_id'] . ",
						currency_code = '" . $order_info['currency_code'] . "',
						currency_value = " . $order_info['currency_value'] . ",
						commission = " . $this->msSeller->getCommissionForSeller($seller_id) . ",
						description = '" . $this->db->escape($description) . "',
						date_created = NOW(),
						date_modified = NOW()";

			$this->db->query($sql);
			
			if ($parent_tr_id == NULL) {
				$this->db->query("UPDATE " . DB_PREFIX . "ms_transaction SET parent_transaction_id = LAST_INSERT_ID() WHERE transaction_id = LAST_INSERT_ID()");
			} else {
				$this->closeTransaction($parent_tr_id);
			}
		}
	}	
	
	public function getSellerTransactions($seller_id, $sort, $includingClosed = FALSE) {
		$sql = "SELECT *, (amount-(amount*commission/100)) as net_amount FROM " . DB_PREFIX . "ms_transaction
				WHERE seller_id = " . (int)$seller_id
        		. (!$includingClosed ? " AND (transaction_status_id != " . (int)self::MS_TRANSACTION_STATUS_CLOSED . ")" : '') . " 
    			ORDER BY {$sort['order_by']} {$sort['order_way']}" 
    			. ($sort['limit'] ? " LIMIT ".(int)(($sort['page'] - 1) * $sort['limit']).', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);
		return $res->rows;
	}
	
	public function getTotalSellerTransactions($seller_id, $includingClosed = FALSE) {
		$sql = "SELECT COUNT(*) as total FROM " . DB_PREFIX . "ms_transaction
				WHERE seller_id = " . (int)$seller_id
				. (!$includingClosed ? " AND (transaction_status_id != " . (int)self::MS_TRANSACTION_STATUS_CLOSED . ")" : '');

		$res = $this->db->query($sql);
		return $res->row['total'];
	}
	
	public function getTransactions($sort, $includingClosed = FALSE) {
		$sql = "SELECT  (mt.amount-(mt.amount*mt.commission/100)) as 'trn.net_amount',
						mt.date_created as 'trn.date_created',
						mt.date_modified as 'trn.date_modified',
						mt.description as 'trn.description',
						ms.nickname as 'sel.nickname'
				FROM " . DB_PREFIX . "ms_transaction mt
				INNER JOIN " . DB_PREFIX . "ms_seller ms
					ON (mt.seller_id = ms.seller_id) "
				. (!$includingClosed ? " WHERE (mt.transaction_status_id != " . (int)self::MS_TRANSACTION_STATUS_CLOSED . ")" : '') . "
    			ORDER BY {$sort['order_by']} {$sort['order_way']}" 
    			. ($sort['limit'] ? " LIMIT ".(int)(($sort['page'] - 1) * $sort['limit']).', '.(int)($sort['limit']) : '');
				        
		$res = $this->db->query($sql);
		return $res->rows;
	}
	
	public function completeWithdrawal($transaction_id) {
		$t = $this->getTransactionData($transaction_id);
		$new_tr_id = $this->copyTransaction($transaction_id);
		$this->closeTransaction($transaction_id);
		
		$sql = "UPDATE " . DB_PREFIX . "ms_transaction
				SET description = '" . sprintf($this->language->get('ms_transaction_withdrawal'), $this->currency->format($t['amount'], $this->config->get('config_currency'))) . "',
					date_modified = NOW()
				WHERE transaction_id = " . (int)$new_tr_id;

		$this->db->query($sql);		
	}
}
?>