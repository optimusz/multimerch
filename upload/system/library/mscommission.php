<?php
class MsCommission extends Model {
	const RATE_SALE = 1;
	
	const TYPE_SALES_QUANTITY = 1;
	const TYPE_SALES_AMOUNT = 2;
	const TYPE_PERIODIC = 3;
	const TYPE_DATE_UNTIL = 4;
	
	public function createCommission($rates) {
		foreach ($rates as $type => $rate) {
			if ( (!isset($rate['flat']) || $rate['flat'] === '') && (!isset($rate['percent']) || $rate['percent'] === '') ) {
				unset($rates[$type]);
			}
		}

		if (!empty($rates)) {
			$sql = "INSERT INTO " . DB_PREFIX . "ms_commission () VALUES ()";
			$this->db->query($sql);
			$commission_id = $this->db->getLastId();
			
			foreach ($rates as $type => $rate) {
				$sql = "INSERT INTO " . DB_PREFIX . "ms_commission_rate
						SET commission_id = " . (int)$commission_id . ",
							rate_type = " . (int)$type . ",
							flat = " . (isset($rate['flat']) && $rate['flat'] !== '' ? (float)$rate['flat'] : 'NULL') . ",
							percent = " . (isset($rate['percent']) && $rate['percent'] !== '' ? (float)$rate['percent'] : 'NULL');

				$this->db->query($sql);	
			}
		} else {
			$commission_id = NULL;
		}
		
		return $commission_id;
	}
	
	public function editCommission($commission_id, $rates) {
		foreach ($rates as $type => $rate) {
			if ( (!isset($rate['flat']) || $rate['flat'] === '') && (!isset($rate['percent']) || $rate['percent'] === '') ) {
				$sql = "DELETE FROM " . DB_PREFIX . "ms_commission_rate WHERE rate_id = " . (int)$rate['rate_id'];
				$this->db->query($sql);
				unset($rates[$type]);
			} else {
				$sql = "UPDATE " . DB_PREFIX . "ms_commission_rate
						SET flat = " . (isset($rate['flat']) && $rate['flat'] !== '' ? (float)$rate['flat'] : 'NULL') . ",
							percent = " . (isset($rate['percent']) && $rate['percent'] !== '' ? (float)$rate['percent'] : 'NULL') . "
						WHERE rate_id = " . (int)$rate['rate_id'];
				$this->db->query($sql);
			}
		}
		
		if (empty($rates)) {
			$commission_id = NULL;
		}
		
		return $commission_id;
	}
		
		
	
	// Get commissions
	public function getCommissionRates($commission_id) {
		$sql = "SELECT 	mcr.rate_id as 'mcr.rate_id',
						mcr.rate_type as 'mcr.rate_type',
						mcr.flat as 'mcr.flat',
						mcr.percent as 'mcr.percent'
				FROM `" . DB_PREFIX . "ms_commission_rate` mcr
				WHERE mcr.commission_id = " . (int)$commission_id;
		$res = $this->db->query($sql);
		$rates = array();
		
		foreach ($res->rows as $row) {
			$rates[$row['mcr.rate_type']] = array(
				'rate_id' => $row['mcr.rate_id'],			
				'rate_type' => $row['mcr.rate_type'],
				'flat' => $row['mcr.flat'],
				'percent' => $row['mcr.percent']
			);
		}
		return $rates;
	}
	
	public function calculateCommission($seller_id) {
		$default_seller_group = $this->MsLoader->MsSellerGroup->getSellerGroup($this->config->get('msconf_default_seller_group_id'));
		$default_commission_id = $default_seller_group['msg.commission_id'];
		
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
		$commissions = $this->getCommissionRates($default_commission_id);
		
		// Apply group commissions
		if ($group_commission_id != $default_commission_id) {
			$group_commissions = $this->getCommissionRates($group_commission_id);
			foreach ($group_commissions as $rate_type => $rate_val) {
					if (!is_null($rate_val['flat'])) $commissions[$rate_type]['flat'] = $rate_val['flat'];
					if (!is_null($rate_val['percent'])) $commissions[$rate_type]['percent'] = $rate_val['percent'];
			}
		}
		
		// Apply individual seller commissions
		if (!is_null($seller_commission_id)) {
			$seller_commissions = $this->getCommissionRates($seller_commission_id);
			foreach ($seller_commissions as $rate_type => $rate_val) {
					if (!is_null($rate_val['flat'])) $commissions[$rate_type]['flat'] = $rate_val['flat'];
					if (!is_null($rate_val['percent'])) $commissions[$rate_type]['percent'] = $rate_val['percent'];
			}
		}
		
		return $commissions;
	}
	
	// fun
	public function myLittleCalculateCommission($seller_id) {
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