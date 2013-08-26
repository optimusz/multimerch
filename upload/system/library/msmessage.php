<?php
class MsMessage extends Model {
	public function createMessage($data) {
		$sql = "INSERT INTO `" . DB_PREFIX . "ms_message`
				SET conversation_id = " . (isset($data['conversation_id']) ? (int)$data['conversation_id'] : 'NULL') . ",
					`from` = " . (isset($data['from']) ? (int)$data['from'] : 'NULL') . ",
					`to` = " . (isset($data['to']) ? (int)$data['to'] : 'NULL') . ",
					message = '" . (isset($data['message']) ? $this->db->escape($data['message']) : '') . "',
					date_created = NOW()";

		$this->db->query($sql);
		return $this->db->getLastId();
	}
	
	public function getMessages($data = array(), $sort = array()) {
		$filters = '';
		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				$filters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
			}
		}

		$sql = "SELECT
			SQL_CALC_FOUND_ROWS
			message_id,
			conversation_id,
			message,
			`from`,
			`to`,
			(SELECT CONCAT(c.firstname, ' ', SUBSTR(c.lastname, 1, 1), '.') FROM `" . DB_PREFIX . "customer` c WHERE customer_id = `from`) as sender,
			date_created
			FROM `" . DB_PREFIX . "ms_message` mmesg
			WHERE 1 = 1 "
			. (isset($data['conversation_id']) ? " AND conversation_id =  " .  (int)$data['conversation_id'] : '')
			. (isset($data['from']) ? " AND `from` =  " .  (int)$data['from'] : '')
			. (isset($data['to']) ? " AND `to` =  " .  (int)$data['to'] : '')
			. (isset($data['message_id']) ? " AND message_id =  " .  (int)$data['message_id'] : '')

			. $filters

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];

		return ($res->num_rows == 1 && isset($data['single']) ? $res->row : $res->rows);
	}
	
	public function deleteMessage($message_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_message` WHERE message_id = " . (int)$message_id);
	}
}
?>