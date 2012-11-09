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

}
?>