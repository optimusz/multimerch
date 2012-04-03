<?php
final class MsTransaction extends Model {
	private $data;
	
	private function _modelExists($model) {
		$file  = DIR_APPLICATION . 'model/' . $model . '.php';
		return file_exists($file);
	}
	
	private function _prepareData(&$data) {
	}
	
  	public function __construct($registry) {
  		parent::__construct($registry);
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
		$this->load = $registry->get('load');
	}
	
	private function _getOrderProducts($order_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "order_product
				WHERE order_id = " . (int)$order_id;
		
		$res = $this->db->query($sql);

		return $res->rows;
		
	}
	
	public function getTransactionsForOrder($order_id) {
		$sql = "SELECT transaction_id, product_id FROM " . DB_PREFIX . "ms_transaction
				WHERE order_id = " . (int)$order_id;
		
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
			
			if ((int)$data['parent_transaction_id'] == 0) {
				$this->db->query("UPDATE " . DB_PREFIX . "ms_transaction SET parent_transaction_id = LAST_INSERT_ID() WHERE transaction_id = LAST_INSERT_ID()");
			}
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
			$seller_id = $this->model_module_multiseller_seller->getSellerIdByProduct($product['product_id']);
			$parent_tr_id = isset($parent_transactions[$product['product_id']]) ? $parent_transactions[$product['product_id']] : NULL;
			
			if ($debit)
				$product['total'] = -1 * abs($product['total']);
			 
			
			$sql = "INSERT INTO " . DB_PREFIX . "ms_transaction
					SET parent_transaction_id = " . (int)$parent_tr_id . ",
						order_id = " . (int)$order_id . ",
						product_id = " .(int)$product['product_id'] . ",
						seller_id = " . (int)$seller_id . ",
						amount = ". $product['total'] . ",
						currency_id = ". $order_info['currency_id'] . ",
						currency_code = '" . $order_info['currency_code'] . "',
						currency_value = " . $order_info['currency_value'] . ",
						commission = " . $this->model_module_multiseller_seller->getCommissionForSeller($seller_id) . ",
						description = '" . $this->db->escape($product['name']) . "',
						date_created = NOW(),
						date_modified = NOW()";

			$this->db->query($sql);
			
			if ($parent_tr_id == NULL) {
				$this->db->query("UPDATE " . DB_PREFIX . "ms_transaction SET parent_transaction_id = LAST_INSERT_ID() WHERE transaction_id = LAST_INSERT_ID()");
			}
		}
	}	
	
	public function getSellerTransactions($seller_id, $sort) {
		$sql = "SELECT *, (amount-(amount*commission/100)) as net_amount FROM " . DB_PREFIX . "ms_transaction
				WHERE seller_id = " . (int)$seller_id . "
    			ORDER BY {$sort['order_by']} {$sort['order_way']}" 
    			. ($sort['limit'] ? " LIMIT ".(int)(($sort['page'] - 1) * $sort['limit']).', '.(int)($sort['limit']) : '');
        
		$res = $this->db->query($sql);
		return $res->rows;
	}
	
	public function getTotalSellerTransactions($seller_id) {
		$sql = "SELECT COUNT(*) as total FROM " . DB_PREFIX . "ms_transaction
				WHERE seller_id = " . (int)$seller_id;
				
		$res = $this->db->query($sql);
		return $res->row['total'];
	}
}
?>