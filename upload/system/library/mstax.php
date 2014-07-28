<?php 
class MsTax extends Model {

	public function getTaxClasses() {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "tax_class`");

		return $query->rows;
	}

	public function getSellerTaxClassId($seller_id) {
		$query = $this->db->query("SELECT `tax_class_id` FROM `" . DB_PREFIX . "ms_seller_tax_class` WHERE `seller_id` = " . $seller_id);

		return $query->row['tax_class_id'];
	}

	public function setSellerTaxClass($seller_id, $tax_class_id) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "ms_seller_tax_class` (`seller_id`, `tax_class_id`) VALUES(" . $seller_id . ", " . $tax_class_id . ") ON DUPLICATE KEY UPDATE `tax_class_id` = " . $tax_class_id);
	}

	public function setProductTaxClass($product_id, $seller_tax_class) {
		$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `tax_class_id` = " . $seller_tax_class . " WHERE `product_id` = " . $product_id);
	}

}
?>