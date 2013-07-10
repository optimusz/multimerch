<?php 
class ModelPaymentMSPPAdaptive extends Model {
	public function getMethod($address, $total) {
		$this->load->language('payment/ms_pp_adaptive');
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('ms_pp_adaptive_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
		
		if ($this->config->get('ms_pp_adaptive_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('ms_pp_adaptive_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$store_owner = $this->config->get('msppaconf_receiver');
		if (empty($store_owner) || !filter_var($store_owner, FILTER_VALIDATE_EMAIL)) {
			$status = false;
		}
		
		// check valid paypal addresses
		$receivers = array();
		//$receivers[0]['ms.paypal'] = $this->config->get('msppaconf_receiver');
		foreach ($this->cart->getProducts() as $product) {
			// create unique receiver array element
			$seller_id = $this->MsLoader->MsProduct->getSellerId($product['product_id']);
			if (!isset($receivers[$seller_id])) {
				$seller = $this->MsLoader->MsSeller->getSeller($seller_id);
				$receivers[$seller_id] = $seller;
				$receivers[$seller_id]['amount'] = 0;
			}
		}

		if(count($receivers) > 5) {
			$status = false;
		}
		
		foreach ($receivers as $receiver) {
			if (!isset($receiver['ms.paypal']) || empty($receiver['ms.paypal']) || !filter_var($receiver['ms.paypal'], FILTER_VALIDATE_EMAIL)) {
				if ($this->config->get('msppaconf_invalid_email') == 0) {
					$status = false;
					break;
				}
			}
		}
		
		$currencies = array(
			'AUD',
			'CAD',
			'EUR',
			'GBP',
			'JPY',
			'USD',
			'NZD',
			'CHF',
			'HKD',
			'SGD',
			'SEK',
			'DKK',
			'PLN',
			'NOK',
			'HUF',
			'CZK',
			'ILS',
			'MXN',
			'MYR',
			'BRL',
			'PHP',
			'TWD',
			'THB',
			'TRY'
		);
		
		if (!in_array(strtoupper($this->currency->getCode()), $currencies)) {
			$status = false;
		}			
					
		$method_data = array();
	
		if ($status) {  
			$method_data = array( 
				'code'       => 'ms_pp_adaptive',
				'title'      => $this->language->get('ppa_title'),
				'sort_order' => $this->config->get('ms_pp_adaptive_sort_order')
			);
		}

		return $method_data;
	}
}
?>