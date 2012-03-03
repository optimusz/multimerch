<?php
class ModelModuleMultiseller extends Model {
	public function createTable() {
		$createTable = "
			CREATE TABLE " . DB_PREFIX . "multiseller (
             `id` int(11) NOT NULL AUTO_INCREMENT,
             `id_product` int(11) NOT NULL,
             `id_customer` int(10) UNSIGNED DEFAULT NULL,
             `name` varchar(32) NOT NULL DEFAULT '',
             `email` varchar(128) NOT NULL DEFAULT '',
             `comment` text NOT NULL,
             `create_time` int(11) NOT NULL DEFAULT '0',
             `display` tinyint(1) NOT NULL DEFAULT '0',
        	PRIMARY KEY (`id`)) default CHARSET=utf8";
        
        //$this->db->query($createTable);
	}
	
	public function dropTable() {
		$dropTable = "DROP TABLE IF EXISTS " . DB_PREFIX . "multiseller";
		//$this->db->query($dropTable);
    }
}