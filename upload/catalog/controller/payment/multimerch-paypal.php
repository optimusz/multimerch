<?php
class ControllerPaymentMultiMerchPayPal extends Controller {
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
					
					// change payment status
					$this->MsLoader->MsPayment->updatePayment($payment_id, array('payment_status' => MsPayment::STATUS_PAID));
					
					// change product status
					$seller = $this->MsLoader->MsSeller->getSeller($this->MsLoader->MsProduct->getSellerId($product_id));
					$product = $this->MsLoader->MsProduct->getProduct($product_id);
					
					$mails = array();
					switch ($seller['ms.product_validation']) {
						case MsProduct::MS_PRODUCT_VALIDATION_APPROVAL:
							if ($product['product_approved']) {
								$this->MsLoader->MsProduct->changeStatus($product_id, MsProduct::STATUS_ACTIVE);
							} else {
								$mails[] = array(
									'type' => MsMail::SMT_PRODUCT_AWAITING_MODERATION
								);
								
								$this->MsLoader->MsMail->sendMails($mails);
							}
							break;
							
						case MsProduct::MS_PRODUCT_VALIDATION_NONE:
						default:
							$this->MsLoader->MsProduct->changeStatus($product_id, MsProduct::STATUS_ACTIVE);
							$this->MsLoader->MsProduct->approve($product_id);
							break;
					}
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
					
					// change payment status
					$this->MsLoader->MsPayment->updatePayment($payment_id, array('payment_status' => MsPayment::STATUS_PAID));
					
					// send customer mails and change seller status
					$mails = array();
					switch ($this->config->get('msconf_seller_validation')) {
						case MsSeller::MS_SELLER_VALIDATION_APPROVAL:
							$mails[] = array(
								'type' => MsMail::SMT_SELLER_ACCOUNT_AWAITING_MODERATION
							);
							
							$this->MsLoader->MsSeller->changeStatus($seller_id, MsSeller::STATUS_INACTIVE);
							$this->MsLoader->MsSeller->changeApproval($seller_id, 0);
							break;
						
						case MsSeller::MS_SELLER_VALIDATION_NONE:
						default:
							$mails[] = array(
								'type' => MsMail::SMT_SELLER_ACCOUNT_CREATED
							);
							$this->MsLoader->MsSeller->changeStatus($seller_id, MsSeller::STATUS_ACTIVE);
							$this->MsLoader->MsSeller->changeApproval($seller_id, 1);
							break;
					}
					
					$this->MsLoader->MsMail->sendMails($mails);
					break;
				
				default:
					break;
			}
		}
	}
	
	public function payoutIPN() {
		if (isset($this->request->post['custom'])) {
			$payment_id = (int)$this->request->post['custom'];
		} else {
			$payment_id = 0;
		}
		
		if ($payment_id <= 0)
			return $this->log->write("MMERCH PP PAYMENT #$payment_id: Invalid or no payment id received");
		
		$payment = $this->MsLoader->MsPayment->getPayments(array('payment_id' => $payment_id, 'single' => 1));

		if (!$payment)
			return $this->log->write("MMERCH PP PAYMENT #$payment_id: Invalid payment id received");
		
		$seller_id = $payment['seller_id'];

		if ($seller_id <= 0)
			return $this->log->write("MMERCH PP PAYMENT #$payment_id: Invalid or no seller id for this payment");
		
		$response = $this->_validateResponse();
		
		if (!$response)
			return $this->log->write("MMERCH PP PAYMENT #$payment_id: CURL failed ' . curl_error($curl) . '(' . curl_errno($curl) . ')");
		
		if ($response == 'INVALID')
			return $this->log->write("MMERCH PP PAYMENT #$payment_id: IPN response INVALID");
		
		if ($response == 'VERIFIED' && isset($this->request->post['payment_status'])) {
			switch($this->request->post['payment_status']) {
				case 'Completed':
					// check receiver
					if ((strtolower($this->request->post['receiver_email']) != $payment['payment_data']))
						return $this->log->write("MMERCH PP PAYMENT #$payment_id: IPN receiver email mismatch");
					
					// check amount
					if ((float)$this->request->post['mc_gross'] != $this->currency->format($payment['amount'], $payment['currency_code'], 1, false))
						return $this->log->write("MMERCH PP PAYMENT #$payment_id: IPN amount mismatch");
					
					// change payment status
					$this->MsLoader->MsPayment->updatePayment($payment_id, array('payment_status' => MsPayment::STATUS_PAID));
					
					// deduct from balance
					$this->MsLoader->MsBalance->addBalanceEntry($payment['seller_id'], array(
						'withdrawal_id' => $payment['payment_id'],
						'balance_type' => MsBalance::MS_BALANCE_TYPE_WITHDRAWAL,
						'amount' => -$payment['amount'],
						'description' => $payment['mpay.description']
					));
					break;
				
				default:
					break;
			}
		}
	}
}
?>