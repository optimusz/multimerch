<?php
class ModelModuleMultisellerTransaction extends Model {
	private function _getOrderProducts($order_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "order_product
				WHERE order_id = " . (int)$order_id;
		
		$res = $this->db->query($sql);

		return $res->rows;
		
	}
	
	public function addTransactionsForOrder($order_id) {
		$this->load->model('module/multiseller/seller');
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($order_id);
		$order_products = $this->_getOrderProducts($order_id);
		
		if (!$order_products)
			return false;
		
		foreach ($order_products as $product) {
			$seller_id = $this->model_module_multiseller_seller->getSellerIdByProduct($product['product_id']);
			//$seller_id='1';
			$sql = "INSERT INTO " . DB_PREFIX . "ms_transaction
					SET order_id = " . (int)$order_id . ",
						product_id = " .(int)$product['product_id'] . ",
						seller_id = " . (int)$seller_id . ",
						amount = ". $product['total'] . ",
						currency_id = ". $order_info['currency_id'] . ",
						currency_code = '" . $order_info['currency_code'] . "',
						currency_value = " . $order_info['currency_value'] . ",
						transaction_status_id = ". $order_info['order_status_id'] . ",
						commission = " . $this->model_module_multiseller_seller->getCommissionForSeller($seller_id) . ",
						description = '" . $this->db->escape($product['name']) . "',
						date_created = NOW(),
						date_modified = NOW()";

			$this->db->query($sql);
		}
	}
	
	public function updateTransactionsForOrder($order_id) {
		$this->load->model('module/multiseller/seller');
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($order_id);
		$order_products = $this->_getOrderProducts($order_id);
		
		if (!$order_products)
			return false;
		
		foreach ($order_products as $product) {
			$seller_id = $this->model_module_multiseller_seller->getSellerIdByProduct($product['product_id']);
			
			$sql = "UPDATE " . DB_PREFIX . "ms_transaction
					SET amount = ". $product['total'] . ",
						transaction_status_id = ". $order_info['order_status_id'] . ",
						date_modified = NOW()
					WHERE order_id = " . (int)$order_id . "
					AND product_id = " . (int)$product['product_id'];
						
			$this->db->query($sql);
		}		
	}
	
	public function getSellerTransactions($seller_id, $sort) {
		$language_id = 1;
		
		$sql = "SELECT * FROM " . DB_PREFIX . "ms_transaction
				WHERE seller_id = " . (int)$seller_id . "
    			ORDER BY {$sort['order_by']} {$sort['order_way']}" 
    			. ($sort['limit'] ? " LIMIT ".(int)(($sort['page'] - 1) * $sort['limit']).', '.(int)($sort['limit']) : '');
        			
		$res = $this->db->query($sql);
		

		$this->load->model('module/multiseller/seller');
		$review_statuses = $this->model_module_multiseller_seller->getOrderStatusArray($language_id);
		
		foreach ($res->rows as &$row) {
			$row['status'] = $review_statuses[$row['transaction_status_id']];
		}		
		
		return $res->rows;
	}
	
	public function getTotalSellerTransactions($seller_id) {
		$sql = "SELECT COUNT(*) as total FROM " . DB_PREFIX . "ms_transaction
				WHERE seller_id = " . (int)$seller_id;
				
		$res = $this->db->query($sql);
		return $res->row['total'];
	}
}