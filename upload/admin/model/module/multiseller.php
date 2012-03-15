<?php
class ModelModuleMultiseller extends Model {
	public function createTable() {
		$sql = "
			CREATE TABLE `" . DB_PREFIX . "ms_product` (
             `product_id` int(11) NOT NULL,
             `seller_id` int(11) DEFAULT NULL,
             `number_sold` int(11) UNSIGNED NOT NULL DEFAULT '0',
             `review_status_id` tinyint UNSIGNED NOT NULL DEFAULT '1',
        	PRIMARY KEY (`product_id`)) default CHARSET=utf8";
        
        $this->db->query($sql);
        
		$sql = "
			CREATE TABLE `" . DB_PREFIX . "ms_seller` (
             `seller_id` int(11) NOT NULL AUTO_INCREMENT,
             `nickname` VARCHAR(32) NOT NULL DEFAULT '',
             `company` VARCHAR(32) NOT NULL DEFAULT '',
             `website` VARCHAR(2083) NOT NULL DEFAULT '',
             `description` TEXT NOT NULL DEFAULT '',
			 `country_id` INT(11) NOT NULL DEFAULT '0',
			 `avatar_path` VARCHAR(255) DEFAULT NULL,
			 `paypal` VARCHAR(255) DEFAULT NULL,
			 `date_created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
			 `seller_status_id` TINYINT UNSIGNED NOT NULL DEFAULT '1',
			 `commission` DECIMAL(4,2) NOT NULL DEFAULT '0',
        	PRIMARY KEY (`seller_id`)) default CHARSET=utf8";
        
        $this->db->query($sql);
        
		$sql = "
			CREATE TABLE `" . DB_PREFIX . "ms_product_rating` (
             `product_id` int(11) NOT NULL,
             `seller_id` int(11) DEFAULT NULL,
             `customer_id` int(11) NOT NULL,
             `rating_value` TINYINT NOT NULL,
			 `date_created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        	PRIMARY KEY (`product_id`,`seller_id`,`customer_id`)) default CHARSET=utf8";
        
        $this->db->query($sql);
        
		$sql = "
			CREATE TABLE `" . DB_PREFIX . "ms_transaction` (
             `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
             `amount` DECIMAL(15,4) NOT NULL DEFAULT '0',
             `seller_id` int(11) NOT NULL,
             `order_id` int(11) DEFAULT NULL,
             `transaction_status_id` int(11) NOT NULL,
             `currency_id` int(11) NOT NULL,
             `currency_code` VARCHAR(3) NOT NULL,
             `currency_value` DECIMAL(15,8) NOT NULL,
             `commission` DECIMAL(4,2) NOT NULL DEFAULT '0',				
             `description` TEXT NOT NULL DEFAULT '',			              
			 `date_created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
			 `date_modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        	PRIMARY KEY (`transaction_id`)) default CHARSET=utf8";
        
        $this->db->query($sql);        
	}
	
	public function dropTable() {
		$sql = "DROP TABLE IF EXISTS
				`" . DB_PREFIX . "ms_product`,
				`" . DB_PREFIX . "ms_seller`,
				`" . DB_PREFIX . "ms_product_rating`,
				`" . DB_PREFIX . "ms_transaction`";
								
		$this->db->query($sql);
    }
}