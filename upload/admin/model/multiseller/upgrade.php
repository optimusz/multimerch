<?php
class ModelMultisellerUpgrade extends Model {
	public function __construct($registry) {
		parent::__construct($registry);
		$this->load->model('localisation/language');
	}

	public function getDbVersion() {
		$res = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "ms_db_schema'");
		if (!$res->num_rows) return '0.0.0.0';
		
		$res = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ms_db_schema` ORDER BY schema_change_id DESC LIMIT 1");

		if ($res->num_rows)
			return $res->row['major'] . '.' . $res->row['minor'] . '.' . $res->row['build'] . '.' . $res->row['revision'];
		else
			return '0.0.0.0';
	}
	
	public function isDbLatest() {
		$current = $this->getDbVersion();
		if ($this->MsLoader->dbVer == $current) return true;
		return false;
	}
	
	private function _createSchemaEntry($version) {
		$schema = explode(".", $version);
		$this->db->query("INSERT INTO `" . DB_PREFIX . "ms_db_schema` (major, minor, build, revision, date_applied) VALUES({$schema[0]},{$schema[1]},{$schema[2]},{$schema[3]}, NOW())");
	}
	
	public function upgradeDb() {
		$version = $this->getDbVersion();
		
		if (version_compare($version, '1.0.0.0') < 0) {
			$this->db->query("
			CREATE TABLE `" . DB_PREFIX . "ms_db_schema` (
				`schema_change_id` int(11) NOT NULL AUTO_INCREMENT,
				`major` TINYINT NOT NULL,
				`minor` TINYINT NOT NULL,
				`build` TINYINT NOT NULL,
				`revision` SMALLINT NOT NULL,
				`date_applied` DATETIME NOT NULL,
			PRIMARY KEY (`schema_change_id`)) default CHARSET=utf8");
			
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_suborder` (
			`suborder_id` int(11) NOT NULL AUTO_INCREMENT,
			`order_id` int(11) NOT NULL,
			`seller_id` int(11) NOT NULL,
			`order_status_id` int(11) NOT NULL,
			PRIMARY KEY (`suborder_id`)
			) DEFAULT CHARSET=utf8");

			/*$sql = "
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_seller_tax_class` (
			`seller_id` int(11) NOT NULL,
			`tax_class_id` int(11) NOT NULL,
			PRIMARY KEY (`seller_id`),
			UNIQUE KEY `seller_id` (`seller_id`)
			) DEFAULT CHARSET=utf8";
			$this->db->query($sql);*/
			
			$this->_createSchemaEntry('1.0.0.0');
		}
	}
}