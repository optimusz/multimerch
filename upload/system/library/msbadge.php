<?php
class MsBadge extends Model {
	public function createBadge($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "ms_badge (image) VALUES('". $this->db->escape($data['image']) . "')");
		$badge_id = $this->db->getLastId();
		
		foreach ($data['description'] as $language_id => $value) {
			$sql = "INSERT INTO " . DB_PREFIX . "ms_badge_description 
					SET badge_id = '" . (int)$badge_id . "', 
						language_id = '" . (int)$language_id . "', 
						name = '" . $this->db->escape($value['name']) . "', 
						description = '" . $this->db->escape($value['description']) . "'";
						
			$this->db->query($sql);
		}
	}
	
	// Edit Badge
	public function editBadge($badge_id, $data) {
		$sql = "UPDATE " . DB_PREFIX . "ms_badge
				SET image = '" . $this->db->escape($data['image']) . "'
				WHERE badge_id = " . (int)$badge_id;
		$this->db->query($sql);
		
		foreach ($data['description'] as $language_id => $language) {
			$sql = "UPDATE " . DB_PREFIX . "ms_badge_description
					SET name = '". $this->db->escape($language['name']) ."',
						description = '". $this->db->escape(htmlspecialchars(nl2br($language['description']), ENT_COMPAT)) ."'
					WHERE badge_id = " . (int)$badge_id . "
					AND language_id = " . (int)$language_id;
					
			$this->db->query($sql);
		}
	}
		
	public function getBadges($data = array(), $sort = array()) {
		$filters = '';
		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				$filters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
			}
		}

		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					*,
					mb.badge_id as 'mb.badge_id'
				FROM " . DB_PREFIX . "ms_badge mb 
				LEFT JOIN " . DB_PREFIX . "ms_badge_description mbd 
					ON (mb.badge_id = mbd.badge_id) 
				WHERE mbd.language_id = '" . (int)$this->config->get('config_language_id') . "'"
				. (isset($data['badge_id']) ? " AND mb.badge_id =  " .  (int)$data['badge_id'] : '')

				. $filters

				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
				. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');		

		$res = $this->db->query($sql);
		
		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];
		
		return ($res->num_rows == 1 && isset($data['single']) ? $res->row : $res->rows);
	}
	
	public function getTotalBadges() {
		$sql = "SELECT COUNT(*) as total 
				FROM " . DB_PREFIX . "ms_badge mb
				WHERE 1 = 1"
				//. (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '')
				. (isset($data['seller_group_id']) ? " AND seller_group_id =  " .  (int)$data['seller_group_id'] : '');

		$res = $this->db->query($sql);
		return $res->row['total'];
	}
	
	public function getSellerGroupBadges($data = array(), $sort = array()) {
		$sql = "SELECT *,
						mb.badge_id as 'mb.badge_id'
				FROM " . DB_PREFIX . "ms_badge mb 
				LEFT JOIN " . DB_PREFIX . "ms_badge_description mbd 
					ON (mb.badge_id = mbd.badge_id) 
				LEFT JOIN " . DB_PREFIX . "ms_badge_seller_group mbsg 
					ON (mb.badge_id = mbsg.badge_id)
				WHERE 1=1"
				. (isset($data['language_id']) ? " AND mbd.language_id =  " .  (int)$data['language_id'] : '')
				. (isset($data['badge_id']) ? " AND mb.badge_id =  " .  (int)$data['badge_id'] : '')
				. (isset($data['seller_id']) ? " AND mbsg.seller_id =  " .  (int)$data['seller_id'] : '')
				. (isset($data['seller_group_id']) ? " AND mbsg.seller_group_id =  " .  (int)$data['seller_group_id'] : '')

				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
				. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');		

		$res = $this->db->query($sql);
		return ($res->num_rows == 1 && isset($data['single']) ? $res->row : $res->rows);
	}
	
	public function getBadgeDescriptions($badge_id) {
		$seller_group_data = array();
	
		$sql = "SELECT * 
				FROM " . DB_PREFIX . "ms_badge_description 
				WHERE badge_id = '" . (int)$badge_id . "'";
	
		$res = $this->db->query($sql);
	
		foreach ($res->rows as $result) {
			$badge_data[$result['language_id']] = array(
				'name'        => $result['name'],
				'description' => $result['description']
			);
		}
		
		return $badge_data;
	}
	
	// Delete badge
	public function deleteBadge($badge_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_badge_description WHERE badge_id = '" . (int)$badge_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_badge WHERE badge_id = '" . (int)$badge_id . "'");
	}
}
?>
