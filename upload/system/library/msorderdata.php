<?php
class MsOrderData extends Model {
  	public function __construct($registry) {
  		parent::__construct($registry);
	}

	public function getOrders($data = array(), $sort = array()) {
		$sql = "SELECT *
				FROM `" . DB_PREFIX . "order` o
				WHERE order_id IN (
					SELECT * FROM (
						SELECT order_id
						FROM `" . DB_PREFIX . "ms_order_product_data` mopd
						WHERE seller_id = " . (int)$data['seller_id'] . "
						GROUP BY order_id" 
	    				. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '') . "
					) as t
    			)"
				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '');

		$res = $this->db->query($sql);

		return $res->rows;		
	}

	public function getOrderTotal($order_id, $data) {
		$sql = "SELECT SUM(seller_net_amt) as 'total'
				FROM `" . DB_PREFIX . "ms_order_product_data` mopd
				WHERE order_id = " . (int)$order_id
				. (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : ''); 

		$res = $this->db->query($sql);

		return $res->row['total'];		
	}

	public function getTotalOrders($data = array(), $sort = array()) {
		$sql = "SELECT COUNT(DISTINCT order_id) as 'total'
				FROM `" . DB_PREFIX . "ms_order_product_data` mopd
				WHERE seller_id = " . (int)$data['seller_id'];

		$res = $this->db->query($sql);

		return $res->row['total'];
	}

	

	/*
	public function existsOrderData($order_id) {
		$sql = "SELECT 1 FROM " . DB_PREFIX . "ms_order_product_data
					WHERE order_product_id IN ( 
						SELECT order_product_id FROM " . DB_PREFIX . "order_product 
						WHERE order_id = " . (int)$order_id . "
					)
				";
				
		$res = $this->db->query($sql);
		
		if ($res->num_rows)
			return true;
		
		return false;
	}
	*/
	
	/*
	public function existsOrderProductData($order_product_id) {
		$sql = "SELECT 1 FROM " . DB_PREFIX . "ms_order_product_data
					WHERE order_product_id = " . (int)$order_product_id;

		$res = $this->db->query($sql);
		
		if ($res->num_rows)
			return true;
		
		return false;
	}
	*/
	
	public function getOrderData($order_id, $data = array()) {
		$sql = "SELECT *
				FROM " . DB_PREFIX . "ms_order_product_data
				WHERE order_id = " . (int)$order_id
				. (isset($data['product_id']) ? " AND product_id =  " .  (int)$data['product_id'] : '');
		$res = $this->db->query($sql);

		return $res->rows;
	}
	
	public function getOrderProducts($data) {
		$sql = "SELECT *
				FROM " . DB_PREFIX . "order_product
				LEFT JOIN " . DB_PREFIX . "ms_order_product_data
					USING(order_id, product_id)
				WHERE 1 = 1"
				. (isset($data['order_id']) ? " AND order_id =  " .  (int)$data['order_id'] : '')
				. (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '');

		$res = $this->db->query($sql);

		return $res->rows;
	}
	
	public function addOrderProductData($order_id, $product_id, $data) {
		$sql = "INSERT INTO " . DB_PREFIX . "ms_order_product_data
				SET order_id = " . (int)$order_id . ",
					product_id = " . (int)$product_id . ",
					seller_id = " . (int)$data['seller_id'] . ",
             		store_commission_flat = " . (float)$data['store_commission_flat'] . ",
             		store_commission_pct = " . (float)$data['store_commission_pct'] . ",
             		seller_net_amt = " . (float)$data['seller_net_amt'];
             	
		$this->db->query($sql);
		
		$order_product_data_id = mysql_insert_id();
		return $order_product_data_id;
	}
	
	public function getTotalSales($data = array()) {
		$sql = "SELECT SUM(quantity) as 'total' FROM (
					SELECT quantity FROM `" . DB_PREFIX . "ms_order_product_data` mopd
					LEFT JOIN (SELECT order_id, order_product_id, sum(quantity) as quantity FROM `" . DB_PREFIX . "order_product` op GROUP BY order_product_id) as op
						USING(order_id)
					INNER JOIN `" . DB_PREFIX . "order` o
						USING(order_id)
					WHERE 1 = 1"
					. (isset($data['order_id']) ? " AND mopd.order_id =  " .  (int)$data['order_id'] : '')
					. (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '')
					. (isset($data['product_id']) ? " AND mopd.product_id =  " .  (int)$data['product_id'] : '')
					. (isset($data['period_start']) ? " AND DATEDIFF(o.date_added, '{$data['period_start']}') >= 0" : "")
					. " AND o.order_status_id IN  (" .  $this->db->escape(implode(',', $this->config->get('msconf_credit_order_statuses'))) . ")"
					. " GROUP BY order_product_id
				) t";

		$res = $this->db->query($sql);
		return (int)$res->row['total'];
	}	
}
?>