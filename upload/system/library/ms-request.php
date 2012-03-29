<?php
class MsRequest {
	private $errors;
	private $fileName;
		
  	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
		
		$this->errors = array();
	}
	
	public static function byName($registry, $name) {
		$instance = new self($registry);
        $instance->fileName = $name;
        return $instance;
	}
	
	public function createRequest($data) {
		$created_message = isset($data['created_message']) ? $this->db->escape($data['created_message']) : '';		
		$seller_id = isset($data['seller_id']) ? (int)$data['seller_id'] : 'NULL';
		$product_id = isset($data['product_id']) ? (int)$data['product_id'] : 'NULL';
		
		$sql = "INSERT INTO " . DB_PREFIX . "ms_request
				SET seller_id = " . $seller_id  . ",
					product_id = " . $product_id . ",
					request_type = " . (int)$data['request_type'] . ",
					created_message = '" . $created_message . "',
					date_created = NOW()";

		$this->db->query($sql);
	}
	
	public function processRequest($data) {
		$processed_message = isset($data['processed_message']) ? $this->db->escape($data['processed_message']) : '';
		$sql = "UPDATE " . DB_PREFIX . "ms_request
				SET processed_message = '" . $processed_message . "',
					processed_by_user_id = " . (int)$data['user_id'] . ",
					date_processed = NOW()";
		
		$this->db->query($sql);
	}	
}
?>