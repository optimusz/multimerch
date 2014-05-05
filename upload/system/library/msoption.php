<?php
class MsOption extends Model {
	public function getOptions($data = array(), $sort = array()) {		
		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					*
				FROM `" . DB_PREFIX . "option` o
				LEFT JOIN " . DB_PREFIX . "option_description od
					ON (o.option_id = od.option_id)
				WHERE 1 = 1 "
				. (isset($data['option_id']) ? " AND o.option_id =  " .  (int)$data['option_id'] : '')
				. (isset($data['language_id']) ? " AND language_id =  " .  (int)$data['language_id'] : " AND language_id =  " .  (int)$this->config->get('config_language_id'))
				. (isset($data['enabled']) ? " AND enabled =  " .  (int)$data['enabled'] : '')
				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
				. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];

		return ($res->num_rows == 1 && isset($data['single']) ? $res->row : $res->rows);
	}
	
	public function getOptionValues($option_id) {
		$option_value_data = array();
		$sql = "SELECT *
			FROM " . DB_PREFIX . "option_value ov
			LEFT JOIN " . DB_PREFIX . "option_value_description ovd
				ON (ov.option_value_id = ovd.option_value_id)
			WHERE ov.option_id = " . (int)$option_id
			. (isset($data['language_id']) ? " AND ovd.language_id =  " .  (int)$data['language_id'] : " AND ovd.language_id =  " .  (int)$this->config->get('config_language_id')) .  "
		ORDER BY ov.sort_order ASC";
		
		$option_value_query = $this->db->query($sql);
	
		foreach ($option_value_query->rows as $option_value) {
			$option_value_data[] = array(
				'option_value_id' => $option_value['option_value_id'],
				'name'            => $option_value['name'],
				'image'           => $option_value['image'],
				'sort_order'      => $option_value['sort_order']
			);
		}
	
		return $option_value_data;
	}
}
?>