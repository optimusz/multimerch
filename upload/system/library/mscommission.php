<?php
class MsCommission extends Model {
	const TYPE_SALES_QUANTITY = 1;
	const TYPE_SALES_AMOUNT = 2;
	const TYPE_PERIODIC = 3;
	const TYPE_DATE_UNTIL = 4;
	
	const TYPES_INT = 1;
	const TYPES_DECIMAL = 2;
	const TYPES_PERIODIC = 4;
	
	// Get commissions
	public function getCommissions($commission_id) {
		$sql = "SELECT 	regcr.flat as `regcr.flat`, regcr.percent as `regcr.percent`,
						moncr.flat as `moncr.flat`, moncr.percent as `moncr.percent`,
						lstcr.flat as `lstcr.flat`, lstcr.percent as `lstcr.percent`,
						salcr.flat as `salcr.flat`, salcr.percent as `salcr.percent`
				FROM `" . DB_PREFIX . "ms_commission` cm
				LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` regcr ON(cm.reg_rate_id = regcr.commission_rate_id)
				LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` moncr ON(cm.monthly_rate_id = moncr.commission_rate_id)
				LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` lstcr ON(cm.list_rate_id = lstcr.commission_rate_id)
				LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` salcr ON(cm.sale_rate_id = salcr.commission_rate_id)
				WHERE cm.commission_id = " . (int)$commission_id;
		$res = $this->db->query($sql);
		
		$commissions = array(
			'reg_rate' => array(
				'flat'=> $res->row['regcr.flat'],
				'percent'=> $res->row['regcr.percent']
			),
			'monthly_rate' => array(
				'flat'=> $res->row['moncr.flat'],
				'percent'=> $res->row['moncr.percent']
			),
			'list_rate' => array(
				'flat'=> $res->row['lstcr.flat'],
				'percent'=> $res->row['lstcr.percent']
			),
			'sale_rate' => array(
				'flat'=> $res->row['salcr.flat'],
				'percent'=> $res->row['salcr.percent']
			)
		);
		
		return $commissions;
	}
	
	// Calculate commissions
	public function calculateCommissions($seller_id) {
		// Get seller commission id
		$sql = "SELECT seller_group as `seller_group`, commission_id as `commission_id`
				FROM `" . DB_PREFIX . "ms_seller`
				WHERE ms_seller.seller_id = " . (int)$seller_id;
		$res = $this->db->query($sql);
		
		$seller_group_id = $res['seller_group'];
		$seller_commission_id = $res['commission_id'];
		
		// Get seller group commission id
		$sql = "SELECT commission_id as `commission_id`
				FROM `" . DB_PREFIX . "ms_seller_group`
				WHERE ms_seller_group.seller_group_id = " . (int)$seller_group_id;
		$res = $this->db->query($sql);
		
		$seller_group_commission_id = $res['commission_id'];
		
		// Get default store commissions if not default seller group
		if ($seller_group_id != 1) {
			// 1 - Get default store commissions
			$sql = "SELECT 	regcr.flat as `regcr.flat`, regcr.percent as `regcr.percent`,
							moncr.flat as `moncr.flat`, moncr.percent as `moncr.percent`,
							lstcr.flat as `lstcr.flat`, lstcr.percent as `lstcr.percent`,
							salcr.flat as `salcr.flat`, salcr.percent as `salcr.percent`
					FROM `" . DB_PREFIX . "ms_commission` cm
					LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` regcr ON(cm.reg_rate_id = regcr.commission_rate_id)
					LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` moncr ON(cm.monthly_rate_id = moncr.commission_rate_id)
					LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` lstcr ON(cm.list_rate_id = lstcr.commission_rate_id)
					LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` salcr ON(cm.sale_rate_id = salcr.commission_rate_id)
					WHERE cm.commission_id = " . 1;
			$res = $this->db->query($sql);
		
			$commissions = array(
				'reg_rate' => array(
					'flat'=> $res->rows[0]['regcr.flat'],
					'percent'=> $res->rows[0]['regcr.percent']
				),
				'monthly_rate' => array(
					'flat'=> $res->rows[0]['moncr.flat'],
					'percent'=> $res->rows[0]['moncr.percent']
				),
				'list_rate' => array(
					'flat'=> $res->rows[0]['lstcr.flat'],
					'percent'=> $res->rows[0]['lstcr.percent']
				),
				'sale_rate' => array(
					'flat'=> $res->rows[0]['salcr.flat'],
					'percent'=> $res->rows[0]['salcr.percent']
				)
			);
		}
		
		// 2 - Get seller group commissions
		$sql = "SELECT 	regcr.flat as `regcr.flat`, regcr.percent as `regcr.percent`,
				moncr.flat as `moncr.flat`, moncr.percent as `moncr.percent`,
				lstcr.flat as `lstcr.flat`, lstcr.percent as `lstcr.percent`,
				salcr.flat as `salcr.flat`, salcr.percent as `salcr.percent`
		FROM `" . DB_PREFIX . "ms_commission` cm
		LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` regcr ON(cm.reg_rate_id = regcr.commission_rate_id)
		LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` moncr ON(cm.monthly_rate_id = moncr.commission_rate_id)
		LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` lstcr ON(cm.list_rate_id = lstcr.commission_rate_id)
		LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` salcr ON(cm.sale_rate_id = salcr.commission_rate_id)
		WHERE cm.commission_id = " . (int)$seller_group_commission_id;
		$res = $this->db->query($sql);
			
		if ($res->rows[0]['regcr.flat'] != NULL) {
			$commissions['reg_rate']['flat'] = $res->rows[0]['regcr.flat'];
		}
		if ($res->rows[0]['regcr.percent'] != NULL) {
			$commissions['reg_rate']['percent'] = $res->rows[0]['regcr.percent'];
		}
		
		if ($res->rows[0]['moncr.flat'] != NULL) {
			$commissions['monthly_rate']['flat'] = $res->rows[0]['moncr.flat'];
		}
		if ($res->rows[0]['moncr.percent'] != NULL) {
			$commissions['monthly_rate']['percent'] = $res->rows[0]['moncr.percent'];
		}
		
		if ($res->rows[0]['lstcr.flat'] != NULL) {
			$commissions['list_rate']['flat'] = $res->rows[0]['lstcr.flat'];
		}
		if ($res->rows[0]['lstcr.percent'] != NULL) {
			$commissions['list_rate']['percent'] = $res->rows[0]['lstcr.percent'];
		}
		
		if ($res->rows[0]['salcr.flat'] != NULL) {
			$commissions['sale_rate']['flat'] = $res->rows[0]['salcr.flat'];
		}
		if ($res->rows[0]['salcr.percent'] != NULL) {
			$commissions['sale_rate']['percent'] = $res->rows[0]['salcr.percent'];
		}
		
		// 3 - Get seller commissions
		$sql = "SELECT 	regcr.flat as `regcr.flat`, regcr.percent as `regcr.percent`,
				moncr.flat as `moncr.flat`, moncr.percent as `moncr.percent`,
				lstcr.flat as `lstcr.flat`, lstcr.percent as `lstcr.percent`,
				salcr.flat as `salcr.flat`, salcr.percent as `salcr.percent`
		FROM `" . DB_PREFIX . "ms_commission` cm
		LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` regcr ON(cm.reg_rate_id = regcr.commission_rate_id)
		LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` moncr ON(cm.monthly_rate_id = moncr.commission_rate_id)
		LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` lstcr ON(cm.list_rate_id = lstcr.commission_rate_id)
		LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` salcr ON(cm.sale_rate_id = salcr.commission_rate_id)
		WHERE cm.commission_id = " . (int)$seller_commission_id;
		$res = $this->db->query($sql);		
	
	
		if ($res->rows[0]['regcr.flat'] != NULL) {
			$commissions['reg_rate']['flat'] = $res->rows[0]['regcr.flat'];
		}
		if ($res->rows[0]['regcr.percent'] != NULL) {
			$commissions['reg_rate']['percent'] = $res->rows[0]['regcr.percent'];
		}
		
		if ($res->rows[0]['moncr.flat'] != NULL) {
			$commissions['monthly_rate']['flat'] = $res->rows[0]['moncr.flat'];
		}
		if ($res->rows[0]['moncr.percent'] != NULL) {
			$commissions['monthly_rate']['percent'] = $res->rows[0]['moncr.percent'];
		}
		
		if ($res->rows[0]['lstcr.flat'] != NULL) {
			$commissions['list_rate']['flat'] = $res->rows[0]['lstcr.flat'];
		}
		if ($res->rows[0]['lstcr.percent'] != NULL) {
			$commissions['list_rate']['percent'] = $res->rows[0]['lstcr.percent'];
		}
		
		if ($res->rows[0]['salcr.flat'] != NULL) {
			$commissions['sale_rate']['flat'] = $res->rows[0]['salcr.flat'];
		}
		if ($res->rows[0]['salcr.percent'] != NULL) {
			$commissions['sale_rate']['percent'] = $res->rows[0]['salcr.percent'];
		}
		
		return $commissions;
	}
	
	
	public function betterCalculateCommissions($seller_id) {
		$default_commission_id = 1;
		
		$sql = "SELECT seller_group as `seller_group`,
						commission_id as `commission_id`
				FROM `" . DB_PREFIX . "ms_seller`
				WHERE seller_id = " . (int)$seller_id;
		$res = $this->db->query($sql);
		
		$seller_group_id = $res->row['seller_group'];
		$seller_commission_id = $res->row['commission_id'];

		$sql = "SELECT commission_id as `commission_id`
				FROM `" . DB_PREFIX . "ms_seller_group`
				WHERE seller_group_id = " . (int)$seller_group_id;
		$res = $this->db->query($sql);
		
		$group_commission_id = $res->row['commission_id'];
		
		// Get default commissions
		$commissions = $this->getCommissions($default_commission_id);
		
		// Apply group commissions
		if ($group_commission_id != $default_commission_id) {
			$group_commissions = $this->getCommissions($group_commission_id);
			foreach ($group_commissions as $rate => $c) {
				foreach ($c as $ctype => $cval) {
					if (!is_null($cval)) {
						$commissions[$rate][$ctype] = $cval;
					}
				}
			}
		}
		
		// Apply individual seller commissions
		if (!is_null($seller_commission_id)) {
			$seller_commissions = $this->getCommissions($seller_commission_id);
			foreach ($seller_commissions as $rate => $c) {
				foreach ($c as $ctype => $cval) {
					if (!is_null($cval)) {
						$commissions[$rate][$ctype] = $cval;
					}
				}
			}
		}
		
		return $commissions;
	}
	
	// fun
	public function myLittleCalculateCommissions($seller_id) {
		$sql = "SELECT 	def_reg.flat as `def_reg.flat`,
						def_reg.percent as `def_reg.percent`,
						def_mon.flat as `def_mon.flat`,
						def_mon.percent as `def_mon.percent`,
						def_list.flat as `def_list.flat`,
						def_list.percent as `def_list.percent`,
						def_sale.flat as `def_sale.flat`,
						def_sale.percent as `def_sale.percent`,
		
						seller_reg.flat as `seller_reg.flat`,
						seller_reg.percent as `seller_reg.percent`,
						seller_mon.flat as `seller_mon.flat`,
						seller_mon.percent as `seller_mon.percent`,
						seller_list.flat as `seller_list.flat`,
						seller_list.percent as `seller_list.percent`,
						seller_sale.flat as `seller_sale.flat`,
						seller_sale.percent as `seller_sale.percent`,
		
						group_reg.flat as `group_reg.flat`,
						group_reg.percent as `group_reg.percent`,
						group_mon.flat as `group_mon.flat`,
						group_mon.percent as `group_mon.percent`,
						group_list.flat as `group_list.flat`,
						group_list.percent as `group_list.percent`,
						group_sale.flat as `group_sale.flat`,
						group_sale.percent as `group_sale.percent`
				FROM `" . DB_PREFIX . "ms_commission` cm
				LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` def_reg
					ON (cm.reg_rate_id = def_reg.commission_rate_id AND cm.commission_id = $default_commission_id)
				LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` def_mon
					ON (cm.monthly_rate_id = def_mon.commission_rate_id AND cm.commission_id = $default_commission_id)
				LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` def_list
					ON (cm.list_rate_id = def_list.commission_rate_id AND cm.commission_id = $default_commission_id)
				LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` def_sale
					ON (cm.sale_rate_id = def_sale.commission_rate_id AND cm.commission_id = $default_commission_id)
		
				LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` seller_reg
					ON (cm.reg_rate_id = seller_reg.commission_rate_id AND cm.commission_id = $seller_commission_id)
				LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` seller_mon
					ON (cm.monthly_rate_id = seller_mon.commission_rate_id AND cm.commission_id = $seller_commission_id)
				LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` seller_list
					ON (cm.list_rate_id = seller_list.commission_rate_id AND cm.commission_id = $seller_commission_id)
				LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` seller_sale
					ON (cm.sale_rate_id = seller_sale.commission_rate_id AND cm.commission_id = $seller_commission_id)
		
				LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` group_reg
					ON (cm.reg_rate_id = group_reg.commission_rate_id AND cm.commission_id = $group_commission_id)
				LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` group_mon
					ON (cm.monthly_rate_id = group_mon.commission_rate_id AND cm.commission_id = $group_commission_id)
				LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` group_list
					ON (cm.list_rate_id = group_list.commission_rate_id AND cm.commission_id = $group_commission_id)
				LEFT JOIN `" . DB_PREFIX . "ms_commission_rate` group_sale
					ON (cm.sale_rate_id = group_sale.commission_rate_id AND cm.commission_id = $group_commission_id)";
	}	

}
?>