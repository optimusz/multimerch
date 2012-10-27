<?php
class MsOrderData extends Model {
  	public function __construct($registry) {
  		parent::__construct($registry);
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
	
	public function getOrderData($order_id) {
		$sql = "SELECT *
				FROM " . DB_PREFIX . "ms_order_product_data
				WHERE order_id = " . (int)$order_id;
		$res = $this->db->query($sql);

		return $res->rows;
	}
	
	public function getOrderProductData($order_id, $product_id) {
		$sql = "SELECT *
				FROM " . DB_PREFIX . "ms_order_product_data
				WHERE order_id = " . (int)$order_id . "
				AND product_id = " . (int)$product_id;
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
}
?>