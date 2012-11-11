<?php
class MsSellerGroup extends Model {
	public function getSellerGroup($seller_group_id, $data = array()) {
		$sql = "SELECT *,
						msg.commission_id as 'msg.commission_id'
				FROM " . DB_PREFIX . "ms_seller_group msg
				WHERE msg.seller_group_id = '" . (int)$seller_group_id . "'";
		
		$res = $this->db->query($sql);
		
		return $res->row;
	}
	
	public function getSellerGroups($data = array()) {
		$sql = "SELECT * 
					FROM " . DB_PREFIX . "ms_seller_group msg 
					LEFT JOIN " . DB_PREFIX . "ms_seller_group_description msgd 
						ON (msg.seller_group_id = msgd.seller_group_id) 
					WHERE msgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		
		$sort_data = array(
			'msgd.name'
		);
		
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY msgd.name";
		}
		
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}
		
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}
			
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
			
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		
		$query = $this->db->query($sql);
		
		return $query->rows;
	}
	
	public function getSellerGroupDescriptions($seller_group_id) {
		$seller_group_data = array();
	
		$sql = "SELECT * 
				FROM " . DB_PREFIX . "ms_seller_group_description 
				WHERE seller_group_id = '" . (int)$seller_group_id . "'";
	
		$res = $this->db->query($sql);
	
		foreach ($res->rows as $result) {
			$seller_group_data[$result['language_id']] = array(
				'name'        => $result['name'],
				'description' => $result['description']
			);
		}
		
		return $seller_group_data;
	}
	
	public function getTotalSellerGroups() {
		$sql = "SELECT COUNT(*) as total 
					FROM " . DB_PREFIX . "ms_seller_group";

		$res = $this->db->query($sql);
		return $res->row['total'];
	}
	
	public function createSellerGroup($data) {
		$commission_id = $this->MsLoader->MsCommission->createCommission($data['commission_rates']);
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "ms_seller_group (commission_id) VALUES(". (!is_null($commission_id) ? $commission_id : 'NULL') . ")");
		$seller_group_id = $this->db->getLastId();
		
		foreach ($data['description'] as $language_id => $value) {
			$this->db->query("
				INSERT INTO " . DB_PREFIX . "ms_seller_group_description 
					SET seller_group_id = '" . (int)$seller_group_id . "', 
						language_id = '" . (int)$language_id . "', 
						name = '" . $this->db->escape($value['name']) . "', 
						description = '" . $this->db->escape($value['description']) . "'");
		}
		
		
	}
	
	// Edit seller group
	public function editSellerGroup($seller_group_id, $data) {
		if (!$data['commission_id']) {
			$commission_id = $this->MsLoader->MsCommission->createCommission($data['commission_rates']);
		} else {
			$commission_id = $this->MsLoader->MsCommission->editCommission($data['commission_id'], $data['commission_rates']);
		}
		
		$sql = "UPDATE " . DB_PREFIX . "ms_seller_group
				SET commission_id = " . (!is_null($commission_id) ? (int)$commission_id : 'NULL' ) . "
				WHERE seller_group_id = " . (int)$seller_group_id;
		$this->db->query($sql);
		
		foreach ($data['description'] as $language_id => $language) {
			$sql = "UPDATE " . DB_PREFIX . "ms_seller_group_description
					SET name = '". $this->db->escape($language['name']) ."',
						description = '". $this->db->escape(htmlspecialchars(nl2br($language['description']), ENT_COMPAT)) ."'
					WHERE seller_group_id = " . (int)$seller_group_id . "
					AND language_id = " . (int)$language_id;
					
			$this->db->query($sql);
		}
	}
		
	// Delete seller group
	public function deleteSellerGroup($seller_group_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_seller_group_description 
							WHERE seller_group_id = '" . (int)$seller_group_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_seller_group WHERE seller_group_id = '" . (int)$seller_group_id . "'");
	}
}
?>
