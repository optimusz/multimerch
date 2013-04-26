<?php
class MsCommission extends Model {
	const RATE_SALE = 1;
	const RATE_LISTING = 2;
	const RATE_SIGNUP = 3;
	
	/*
	const PAYMENT_TYPE_BALANCE = 1;
	const PAYMENT_TYPE_GATEWAY = 2;
	const PAYMENT_TYPE_COMBINED = 3;
	*/
	
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
							percent = " . (isset($rate['percent']) && $rate['percent'] !== '' ? (float)$rate['percent'] : 'NULL') . ",
							payment_method = " . (isset($rate['payment_method']) && (int)$rate['payment_method'] > 0 ? (int)$rate['payment_method'] : 'NULL');

				$this->db->query($sql);	
			}
		} else {
			$commission_id = NULL;
		}
		
		return $commission_id;
	}
	
	public function editCommission($commission_id, $rates) {
		foreach ($rates as $type => $rate) {
			if (!isset($rate['rate_id']) || $rate['rate_id'] === '') {
				//create new rate
				if ((isset($rate['flat']) && $rate['flat'] !== '') || (isset($rate['percent']) && $rate['percent'] !== '') ) {
					$sql = "INSERT INTO " . DB_PREFIX . "ms_commission_rate
							SET commission_id = " . (int)$commission_id . ",
								rate_type = " . (int)$type . ",
								flat = " . (isset($rate['flat']) && $rate['flat'] !== '' ? (float)$rate['flat'] : 'NULL') . ",
								percent = " . (isset($rate['percent']) && $rate['percent'] !== '' ? (float)$rate['percent'] : 'NULL') . ",
								payment_method = " . (isset($rate['payment_method']) && (int)$rate['payment_method'] > 0 ? (int)$rate['payment_method'] : 'NULL');
							
					$this->db->query($sql);
				}
			} else {
				// update rate
				if ( (!isset($rate['flat']) || $rate['flat'] === '') && (!isset($rate['percent']) || $rate['percent'] === '') ) {
					$sql = "DELETE FROM " . DB_PREFIX . "ms_commission_rate WHERE rate_id = " . (int)$rate['rate_id'];
					$this->db->query($sql);
					unset($rates[$type]);
				} else {
					$sql = "UPDATE " . DB_PREFIX . "ms_commission_rate
							SET flat = " . (isset($rate['flat']) && $rate['flat'] !== '' ? (float)$rate['flat'] : 'NULL') . ",
								percent = " . (isset($rate['percent']) && $rate['percent'] !== '' ? (float)$rate['percent'] : 'NULL') . ",
								payment_method = " . (isset($rate['payment_method']) && (int)$rate['payment_method'] > 0 ? (int)$rate['payment_method'] : 'NULL') . "
							WHERE rate_id = " . (int)$rate['rate_id'];
					$this->db->query($sql);
				}
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
						mcr.percent as 'mcr.percent',
						mcr.payment_method as 'mcr.payment_method'
				FROM `" . DB_PREFIX . "ms_commission_rate` mcr
				WHERE mcr.commission_id = " . (int)$commission_id . "
				ORDER BY mcr.rate_type";
				
		$res = $this->db->query($sql);
		$rates = array();

		foreach ($res->rows as $row) {
			$rates[$row['mcr.rate_type']] = array(
				'rate_id' => $row['mcr.rate_id'],			
				'rate_type' => $row['mcr.rate_type'],
				'flat' => $row['mcr.flat'],
				'percent' => $row['mcr.percent'],
				'payment_method' => $row['mcr.payment_method'],
			);
		}
		return $rates;
	}
	
	public function calculateCommission($data) {
		$default_seller_group = $this->MsLoader->MsSellerGroup->getSellerGroup($this->config->get('msconf_default_seller_group_id'));
		$default_commission_id = $default_seller_group['msg.commission_id'];
		
		if (isset($data['seller_id'])) {
			$sql = "SELECT seller_group as `seller_group`,
							commission_id as `commission_id`
					FROM `" . DB_PREFIX . "ms_seller`
					WHERE seller_id = " . (int)$data['seller_id'];
			$res = $this->db->query($sql);
			
			//!
			$seller_group_id = $res->row['seller_group'];
			$seller_commission_id = $res->row['commission_id'];
		} else if (isset($data['seller_group_id'])) {
			$seller_group_id = $data['seller_group_id'];
			$seller_commission_id = NULL;
		} else {
			 return FALSE;
		}

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
					if (!is_null($rate_val['payment_method'])) $commissions[$rate_type]['payment_method'] = $rate_val['payment_method'];
			}
		}
		
		// Apply individual seller commissions
		if (!is_null($seller_commission_id)) {
			$seller_commissions = $this->getCommissionRates($seller_commission_id);
			foreach ($seller_commissions as $rate_type => $rate_val) {
					if (!is_null($rate_val['flat'])) $commissions[$rate_type]['flat'] = $rate_val['flat'];
					if (!is_null($rate_val['percent'])) $commissions[$rate_type]['percent'] = $rate_val['percent'];
					if (!is_null($rate_val['payment_method'])) $commissions[$rate_type]['payment_method'] = $rate_val['payment_method'];
			}
		}
		
		return $commissions;
	}
}
?>