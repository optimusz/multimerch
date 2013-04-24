<?php
class ControllerPaymentMultiMerchPayPal extends Controller {
	public function index() {
		echo 'lol';
		return;
		$this->language->load('payment/pp_standard');
		
		$this->data['text_testmode'] = $this->language->get('text_testmode');		
    	
		$this->data['button_confirm'] = $this->language->get('button_confirm');

		$this->data['testmode'] = $this->config->get('pp_standard_test');
		
		if (!$this->config->get('pp_standard_test')) {
    		$this->data['action'] = 'https://www.paypal.com/cgi-bin/webscr';
  		} else {
			$this->data['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		}

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		if ($order_info) {
			$this->data['business'] = $this->config->get('pp_standard_email');
			$this->data['item_name'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');				
			
			$this->data['products'] = array();
			
			foreach ($this->cart->getProducts() as $product) {
				$option_data = array();
	
				foreach ($product['option'] as $option) {
					if ($option['type'] != 'file') {
						$value = $option['option_value'];	
					} else {
						$filename = $this->encryption->decrypt($option['option_value']);
						
						$value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
					}
										
					$option_data[] = array(
						'name'  => $option['name'],
						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
					);
				}
				
				$this->data['products'][] = array(
					'name'     => $product['name'],
					'model'    => $product['model'],
					'price'    => $this->currency->format($product['price'], $order_info['currency_code'], false, false),
					'quantity' => $product['quantity'],
					'option'   => $option_data,
					'weight'   => $product['weight']
				);
			}	
			
			$this->data['discount_amount_cart'] = 0;
			
			$total = $this->currency->format($order_info['total'] - $this->cart->getSubTotal(), $order_info['currency_code'], false, false);

			if ($total > 0) {
				$this->data['products'][] = array(
					'name'     => $this->language->get('text_total'),
					'model'    => '',
					'price'    => $total,
					'quantity' => 1,
					'option'   => array(),
					'weight'   => 0
				);	
			} else {
				$this->data['discount_amount_cart'] -= $total;
			}
			
			$this->data['currency_code'] = $order_info['currency_code'];
			$this->data['first_name'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');	
			$this->data['last_name'] = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');	
			$this->data['address1'] = html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');	
			$this->data['address2'] = html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8');	
			$this->data['city'] = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');	
			$this->data['zip'] = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');	
			$this->data['country'] = $order_info['payment_iso_code_2'];
			$this->data['email'] = $order_info['email'];
			$this->data['invoice'] = $this->session->data['order_id'] . ' - ' . html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
			$this->data['lc'] = $this->session->data['language'];
			$this->data['return'] = $this->url->link('checkout/success');
			$this->data['notify_url'] = $this->url->link('payment/pp_standard/callback', '', 'SSL');
			$this->data['cancel_return'] = $this->url->link('checkout/checkout', '', 'SSL');
			
			if (!$this->config->get('pp_standard_transaction')) {
				$this->data['paymentaction'] = 'authorization';
			} else {
				$this->data['paymentaction'] = 'sale';
			}
			
			$this->data['custom'] = $this->session->data['order_id'];
		
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/pp_standard.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/payment/pp_standard.tpl';
			} else {
				$this->template = 'default/template/payment/pp_standard.tpl';
			}
	
			$this->render();
		}
	}
	
	private function _validateResponse() {
		$request = 'cmd=_notify-validate';
	
		foreach ($this->request->post as $key => $value) {
			$request .= '&' . $key . '=' . urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
		}
		
		if (!$this->config->get('msconf_paypal_sandbox')) {
			$curl = curl_init('https://www.paypal.com/cgi-bin/webscr');
		} else {
			$curl = curl_init('https://www.sandbox.paypal.com/cgi-bin/webscr');
		}

		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		
		return curl_exec($curl);		
	}
	
	public function listingIPN() {
		if (isset($this->request->post['custom'])) {
			$payment_id = (int)$this->request->post['custom'];
		} else {
			$payment_id = 0;
		}
		
		if ($payment_id <= 0)
			return $this->log->write("MMERCH PP LISTING PAYMENT #$payment_id: Invalid or no payment id received");
		
		$payment = $this->MsLoader->MsPayment->getPayments(array('payment_id' => $payment_id, 'single' => 1));
		
		if (!$payment)
			return $this->log->write("MMERCH PP LISTING PAYMENT #$payment_id: Invalid payment id received");
		
		$product_id = $payment['product_id'];

		if ($product_id <= 0)
			return $this->log->write("MMERCH PP LISTING PAYMENT #$payment_id: Invalid or no product id for this payment");
		
		$response = $this->_validateResponse();
		
		if (!$response)
			return $this->log->write("MMERCH PP LISTING PAYMENT #$payment_id: CURL failed ' . curl_error($curl) . '(' . curl_errno($curl) . ')");
		
		if ($response == 'INVALID')
			return $this->log->write("MMERCH PP LISTING PAYMENT #$payment_id: IPN response INVALID");
		
		if ($response == 'VERIFIED' && isset($this->request->post['payment_status'])) {
			switch($this->request->post['payment_status']) {
				case 'Completed':
					// check receiver
					if ((strtolower($this->request->post['receiver_email']) != strtolower($this->config->get('msconf_paypal_address'))))
						return $this->log->write("MMERCH PP LISTING PAYMENT #$payment_id: IPN receiver email mismatch");
					
					// check amount
					if ((float)$this->request->post['mc_gross'] != $this->currency->format($payment['amount'], $payment['currency_code'], 1, false))
						return $this->log->write("MMERCH PP LISTING PAYMENT #$payment_id: IPN amount mismatch");
					
					// change payment and product status
					$this->MsLoader->MsPayment->updatePayment($payment_id, array('payment_status' => MsPayment::STATUS_PAID));
					$this->MsLoader->MsProduct->changeStatus($product_id, MsProduct::STATUS_ACTIVE);
					break;
				
				default:
					break;
			}
		}
	}
	
	public function signupIPN() {
		if (isset($this->request->post['custom'])) {
			$payment_id = (int)$this->request->post['custom'];
		} else {
			$payment_id = 0;
		}
		
		if ($payment_id <= 0)
			return $this->log->write("MMERCH PP SIGNUP PAYMENT #$payment_id: Invalid or no payment id received");
		
		$payment = $this->MsLoader->MsPayment->getPayments(array('payment_id' => $payment_id, 'single' => 1));
		var_dump($payment);
		if (!$payment)
			return $this->log->write("MMERCH PP SIGNUP PAYMENT #$payment_id: Invalid payment id received");
		
		$seller_id = $payment['seller_id'];

		if ($seller_id <= 0)
			return $this->log->write("MMERCH PP SIGNUP PAYMENT #$payment_id: Invalid or no seller id for this payment");
		
		$response = $this->_validateResponse();
		
		if (!$response)
			return $this->log->write("MMERCH PP SIGNUP PAYMENT #$payment_id: CURL failed ' . curl_error($curl) . '(' . curl_errno($curl) . ')");
		
		if ($response == 'INVALID')
			return $this->log->write("MMERCH PP SIGNUP PAYMENT #$payment_id: IPN response INVALID");
		
		if ($response == 'VERIFIED' && isset($this->request->post['payment_status'])) {
			switch($this->request->post['payment_status']) {
				case 'Completed':
					// check receiver
					if ((strtolower($this->request->post['receiver_email']) != strtolower($this->config->get('msconf_paypal_address'))))
						return $this->log->write("MMERCH PP SIGNUP PAYMENT #$payment_id: IPN receiver email mismatch");
					
					// check amount
					if ((float)$this->request->post['mc_gross'] != $this->currency->format($payment['amount'], $payment['currency_code'], 1, false))
						return $this->log->write("MMERCH PP SIGNUP PAYMENT #$payment_id: IPN amount mismatch");
					
					// change payment and product status
					$this->MsLoader->MsPayment->updatePayment($payment_id, array('payment_status' => MsPayment::STATUS_PAID));
					$this->MsLoader->MsSeller->changeStatus($seller_id, MsSeller::STATUS_ACTIVE);
					break;
				
				default:
					break;
			}
		}
	}
}
?>