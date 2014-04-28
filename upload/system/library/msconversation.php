<?php
class MsConversation extends Model {
	public function createConversation($data) {
		$sql = "INSERT INTO `" . DB_PREFIX . "ms_conversation`
				SET title = '" . (isset($data['title']) ? $this->db->escape($data['title']) : '') . "',
					product_id = " . (isset($data['product_id']) && $data['product_id'] ? (int)$data['product_id'] : 'NULL') . ",
					order_id = " . (isset($data['order_id']) && $data['order_id'] ? (int)$data['order_id'] : 'NULL') . ",
					date_created = NOW()";

		$this->db->query($sql);
		return $this->db->getLastId();
	}
	
	public function updateConversation($conversation_id, $data) {
		$sql = "UPDATE `" . DB_PREFIX . "ms_conversation`
				SET conversation_id = conversation_id"
					. (isset($data['title']) ? ", title = " . $this->db->escape($data['title']) : '')
					. (isset($data['product_id']) ? ", product_id = " . (int)$data['product_id'] : '')
					. (isset($data['order_id']) ? ", order_id = " . (int)$data['order_id'] : '') . "
				WHERE conversation_id = " . (int)$conversation_id;

		return $this->db->query($sql);
	}
	
	public function getConversations($data = array(), $sort = array(), $cols = array()) {
		$hFilters = $wFilters = '';
		if(isset($sort['filters'])) {
			$cols = array_merge($cols, array("last_message_date" => 1));
			foreach($sort['filters'] as $k => $v) {
				if (!isset($cols[$k])) {
					$wFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				} else {
					$hFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				}
			}
		}

		$sql = "SELECT
			SQL_CALC_FOUND_ROWS
			conversation_id,
			title,
			product_id,
			order_id,
			date_created,
			(SELECT date_created FROM `" . DB_PREFIX . "ms_message` WHERE conversation_id = mconv.conversation_id ORDER BY message_id DESC LIMIT 1) as last_message_date
			FROM `" . DB_PREFIX . "ms_conversation` mconv
			WHERE 1 = 1 "
			. (isset($data['product_id']) ? " AND product_id =  " . (int)$data['product_id'] : '')
			. (isset($data['order_id']) ? " AND order_id =  " . (int)$data['order_id'] : '')
			. (isset($data['conversation_id']) ? " AND conversation_id =  " . (int)$data['conversation_id'] : '')

			. (isset($data['participant_id']) ? " AND conversation_id IN (SELECT conversation_id FROM `" . DB_PREFIX . "ms_message` WHERE `from` = " .  (int)$data['participant_id'] . " OR `to` = " .  (int)$data['participant_id'] . ")" : '')

			. $wFilters
			
			. " GROUP BY mconv.conversation_id HAVING 1 = 1 "
			
			. $hFilters

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];

		return ($res->num_rows == 1 && isset($data['single']) ? $res->row : $res->rows);
	}
	
	public function isRead($conversation_id, $data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "ms_message`
		WHERE conversation_id = " . (int)$conversation_id . "
		ORDER BY message_id DESC LIMIT 1";
		
		$res = $this->db->query($sql);
		
		if (!$res->num_rows) return false;
		
		if ($res->rows[0]['from'] == $data['participant_id'])
			return 1;
		else
			return $res->rows[0]['read'];
	}
	
	public function unreadMessages($user_id) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "ms_message`
		WHERE `to` = " . (int)$user_id . " AND `read` = 0";
		
		$res = $this->db->query($sql);
		return $res->num_rows;
	}
	
	public function markRead($conversation_id, $data = array()) {
		$sql = "UPDATE `" . DB_PREFIX . "ms_message`
		SET `read` = 1
		WHERE conversation_id = " . (int)$conversation_id 
		. (isset($data['participant_id']) ? " AND `to` =  " .  (int)$data['participant_id'] : '');
		$this->db->query($sql);
	}
	
	public function getWith($conversation_id, $data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "ms_message`
		WHERE conversation_id = " . (int)$conversation_id . "
		ORDER BY message_id DESC LIMIT 1";
		
		$res = $this->db->query($sql);
		
		if (!$res->num_rows) return false;
		
		if ($res->rows[0]['from'] == $data['participant_id'])
			return $res->rows[0]['to'];
		else
			return $res->rows[0]['from'];
	}
	
	public function isParticipant($conversation_id, $data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "ms_message`
		WHERE conversation_id = " . (int)$conversation_id . "
		ORDER BY message_id DESC LIMIT 1";
		
		$res = $this->db->query($sql);
		
		if (!$res->num_rows) return false;
		if ($res->rows[0]['from'] == $data['participant_id'] || $res->rows[0]['to'] == $data['participant_id']) return true;
		return false;
	}
	
	public function deleteConversation($conversation_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_message` WHERE conversation_id = " . (int)$conversation_id);

		$sql = "DELETE FROM `" . DB_PREFIX . "ms_conversation`
				WHERE conversation_id = " . (int)$conversation_id;

		$this->db->query($sql);
	}
}
?>