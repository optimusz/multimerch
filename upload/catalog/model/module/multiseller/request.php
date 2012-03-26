<?php
class ModelModuleMultisellerRequest extends Model {
	public function createRequest($data) {
		$sql = "INSERT INTO " . DB_PREFIX . "ms_request
				SET seller_id = " . (int)$this->customer->getId() . ",
					product_id = " . isset($data['product_id']) ? (int)$data['product_id'] : 0 . ",
					request_type = " . (int)$data['request_type'] . ",
					created_message = '" . $this->db->escape($data['message']) . "',
					date_created = NOW()";
		
		$this->db->query($sql);
	}
}