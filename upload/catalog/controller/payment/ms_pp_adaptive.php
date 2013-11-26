<?php
class ControllerPaymentMSPPAdaptive extends Controller {
	private $_log;
	private $_paypal;

	public function __construct($registry) {
		parent::__construct($registry);
		$this->_log = new Log("paypal.log");
		require_once(DIR_SYSTEM . 'library/ms-paypal.php');
		
		if ($this->config->get('msppaconf_sandbox')) {
			$endPoint = "https://svcs.sandbox.paypal.com/AdaptivePayments/";
		} else {
			$endPoint = "https://svcs.paypal.com/AdaptivePayments/";
		}
		
		$this->_paypal = new PayPal($this->config->get('msppaconf_api_username'), $this->encryption->decrypt($this->config->get('msppaconf_api_password')), $this->encryption->decrypt($this->config->get('msppaconf_api_signature')), $this->config->get('msppaconf_sandbox'), $endPoint, $this->config->get('msppaconf_api_appid'));
		//$this->_paypal = new PayPal($this->config->get('msppaconf_api_username'), $this->encryption->decrypt($this->config->get('msppaconf_api_password')), $this->encryption->decrypt($this->config->get('msppaconf_api_signature')), $this->config->get('msppaconf_api_appid'), $this->config->get('msppaconf_sandbox'));
	}
	
	protected function index() {
		$this->data = array_merge($this->data, $this->load->language('payment/ms_pp_adaptive'));
		$this->data['sandbox'] = $this->config->get('msppaconf_sandbox');		
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/ms_pp_adaptive.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/ms_pp_adaptive.tpl';
		} else {
			$this->template = 'default/template/payment/ms_pp_adaptive.tpl';
		}
		
		$this->render();
		return;		
	}
	
	public function callback() {
		$response = @file_get_contents('php://input');

		$this->_log->write('IPN callback received: ' . $response . print_r($this->request->get,true));
		
		$key = $this->encryption->encrypt($this->config->get('msppaconf_secret_key'));
		$value = $this->encryption->encrypt($this->config->get('msppaconf_secret_value'));

		//$this->_log->write($key . ' ' . $value);		
		//$this->_log->write($this->encryption->decrypt($this->request->get[$this->encryption->encrypt('secre  t')]));
		
		if ($this->config->get('msppaconf_debug'))
			$this->_log->write('IPN callback received: ' . $response);

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($order_id);
		
		if ($order_info) {
			$message = '';
			if (!isset($this->request->get[$key]) || $this->request->get[$key] != $value) {
				$this->_log->write('IPN callback error: shared secret validation failed');
				$message = $response;
				$this->model_checkout_order->update($order_id, $this->config->get('msppaconf_error_status_id'), $message, false);				
			} else if ($this->_paypal->validateIPN()) {
				if ($this->config->get('msppaconf_debug'))
					$this->_log->write('PayPal IPN Verified for order ID' . $order_id . ', payment status: ' . $this->request->post['status']);

				$order_status_id = $this->config->get('config_order_status_id');

				switch(strtolower($this->request->post['status'])) {
					case 'completed':
						$err = false;
						$payments = $this->MsLoader->MsPayment->getPayments(array('order_id' => $order_id));
						$paypalResponse = $this->_paypal->decodePayPalIPN(file_get_contents('php://input'));
						$this->_log->write('Payments: ' . print_r($payments, true));
						
						
						$p = array();
						foreach ($payments as $payment) {
							$p[strtolower($payment['payment_data'])] = $payment;
						}
						
						$this->_log->write('Order data: ' . print_r($paypalResponse, true) . print_r($order_info, true) . print_r($p, true));
						
						foreach ($paypalResponse['transaction'] as $trn) {
							$payment = isset($p[strtolower($trn['receiver'])]) ? $p[strtolower($trn['receiver'])] : false; 
							
							if (!$payment) {
								$this->_log->write('Payment receiver validation error');
								$err = true;
								break;
							} else {
								// required since pp returns it as a string
								preg_match('!\d+(?:\.\d+)?!', $trn['amount'], $matches);
								if ((float)$matches[0] != $this->currency->format($payment['amount'], $payment['currency_code'], 1, false)) {
									$this->_log->write('Payment amount validation error');
									$err = true;
									break;
								}
							}
						}
						
						if ($err) {
							$order_status_id = $this->config->get('msppaconf_error_status_id');
							break;
						} else {
							foreach($paypalResponse['transaction'] as $trn) {
								$payment = $p[strtolower($trn['receiver'])];
								
								$this->MsLoader->MsPayment->updatePayment($payment['payment_id'], array(
									'payment_status' => MsPayment::STATUS_PAID,
									'payment_data' => print_r($trn),
									'date_paid' => date( 'Y-m-d H:i:s')
								));
							}
							
							$order_status_id = $this->config->get('msppaconf_completed_status_id');
						}
						
						break;
					case 'pending':
					case 'processing':
					case 'incomplete':					
						$order_status_id = $this->config->get('msppaconf_pending_status_id');
						$message = $response;
						break;

					case 'error':
					case 'reversal error':
					default:
						$order_status_id = $this->config->get('msppaconf_error_status_id');
						$message = $response;
						break;
				}
				
				if (!$order_info['order_status_id']) {
					if ($this->config->get('msppaconf_debug'))
						$this->_log->write('Confirming order #' . $order_id . ' with status id ' . $order_status_id);
						
					$this->model_checkout_order->confirm($order_id, $order_status_id, $message, false);
				} else {
					if ($this->config->get('msppaconf_debug'))
						$this->_log->write('Updating order #' . $order_id . ' with status id ' . $order_status_id);
						
					$this->model_checkout_order->update($order_id, $order_status_id, $message, false);
				}
			} else {
				$this->_log->write('IPN callback error: PayPal IPN Validation failed');
				$message = $response;
				$this->model_checkout_order->update($order_id, $this->config->get('msppaconf_error_status_id'), $message, false);
			}
		} else {
			$this->_log->write('IPN callback error: No order ID or wrong order ID specified');
		}
	}
	
	public function send() {
		$json = array();
		$this->language->load('payment/ms_pp_adaptive');
		$this->load->model('checkout/order');
		$this->load->model('account/order');
		
		if (!isset($this->session->data['order_id'])) {
			$this->_log->write('PayPal Adaptive error: No order ID specified');
			return;
		}
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		if (!$order_info) {
			$this->_log->write('PayPal Adaptive error: Invalid order ID');
			return;
		}
		
		if ($this->config->get('msppaconf_debug'))
			$this->_log->write("Initializing payment for order ID {$order_info['order_id']}");
		
		$requestParams = array(
			'actionType' => 'PAY',
			'feesPayer' => $this->config->get('msppaconf_feespayer'),
			'reverseAllParallelPaymentsOnError' => 'true', 			
			'currencyCode' => $order_info['currency_code'],
			'returnUrl' => $this->url->link('checkout/success'),
			'ipnNotificationUrl' => $this->url->link('payment/ms_pp_adaptive/callback', '', 'SSL') . '&order_id=' . $order_info['order_id'] . '&' . $this->encryption->encrypt($this->config->get('msppaconf_secret_key')) . '=' . $this->encryption->encrypt($this->config->get('msppaconf_secret_value')),
			'cancelUrl' => $this->url->link('checkout/checkout', '', 'SSL'),
			'requestEnvelope.errorLanguage' => 'en_US',
		);
		
		$paymentParams = array();
		$receivers = array();
		
		// primary (store)
		$receivers[0] = array();
		$receivers[0]['amount'] = 0;
		$receivers[0]['ms.paypal'] = $this->config->get('msppaconf_receiver');
		
		$order_products = $this->model_account_order->getOrderProducts($this->session->data['order_id']);
		
		foreach ($order_products as $order_product) {
			// create unique receiver array element
			$seller_id = $this->MsLoader->MsProduct->getSellerId($order_product['product_id']);
			if (!isset($receivers[$seller_id])) {
				$seller = $this->MsLoader->MsSeller->getSeller($seller_id);
				$receivers[$seller_id] = $seller;
				$receivers[$seller_id]['amount'] = 0;
			}
			
			// don't calculate fees for free products
			if ($order_product['total'] > 0) {
				$commissions = $this->MsLoader->MsCommission->calculateCommission(array('seller_id' => $seller_id));
				$store_commission_flat = $commissions[MsCommission::RATE_SALE]['flat'];
				$store_commission_pct = $order_product['total'] * $commissions[MsCommission::RATE_SALE]['percent'] / 100;
				$seller_net_amt = $order_product['total'] - ($store_commission_flat + $store_commission_pct);
			} else {
				$store_commission_flat = $store_commission_pct = $seller_net_amt = 0;
			}
			
			// create order data if required
			$order_data = $this->MsLoader->MsOrderData->getOrderData(
				array(
					'product_id' => $order_product['product_id'],
					'order_id' => $order_product['order_id'],
				)
			);
			
			if (!$order_data) {
				// create order product data
				$this->MsLoader->MsOrderData->addOrderProductData(
					$order_product['order_id'],
					$order_product['product_id'],
					array(
						'seller_id' => $seller_id,
						'store_commission_flat' => $store_commission_flat,
						'store_commission_pct' => $store_commission_pct,
						'seller_net_amt' => $seller_net_amt
					)
				);
			}
			
			// store commission
			$receivers[0]['amount'] += $this->currency->format($store_commission_flat, $order_info['currency_code'], 1, false) + $this->currency->format($store_commission_pct, $order_info['currency_code'], 1, false);
			
			// seller royalty
			if (isset($receivers[$seller_id]['ms.paypal']) && !empty($receivers[$seller_id]['ms.paypal']) && filter_var($receivers[$seller_id]['ms.paypal'], FILTER_VALIDATE_EMAIL)) {
				// paypal present, add seller net amount to the payment total
				$receivers[$seller_id]['amount'] += $this->currency->format($seller_net_amt, $order_info['currency_code'], 1, false);
			} else {
				// paypal not set, create balance transaction instead
				// add the amount to the store payment
				$receivers[0]['amount'] += $this->currency->format($seller_net_amt, $order_info['currency_code'], 1, false);
			}
		}
		
		foreach ($receivers as $seller_id => $receiver) {
			if ($receiver['amount'] == 0) {
				 unset($receivers[$seller_id]);
			} else {
				// add payment details
				$payment_id = $this->MsLoader->MsPayment->createPayment(array(
					'seller_id' => $seller_id,
					'order_id' => $order_product['order_id'],
					'payment_type' => MsPayment::TYPE_SALE,
					'payment_status' => MsPayment::STATUS_UNPAID,
					'payment_method' => MsPayment::METHOD_PAYPAL_ADAPTIVE,
					'payment_data' => $receiver['ms.paypal'],
					'amount' => $receiver['amount'],
					'currency_id' => $this->currency->getId($this->config->get('config_currency')),
					'currency_code' => $this->currency->getCode($this->config->get('config_currency')),
					'description' => sprintf($this->language->get('ms_transaction_order'), $order_product['order_id'])
				));
			}
		}
		
		$toPay = $total = $this->currency->format($order_info['total'], $order_info['currency_code'], false, false);
		$i = $payableAmount = 0;
		
		if ($this->config->get('msppaconf_debug')) {
			$this->_log->write("Generating amounts for order ID {$order_info['order_id']}");
			$this->_log->write("Total: $total");
		}
		
		if ($this->config->get('msppaconf_payment_type') == "CHAINED") {
			foreach ($receivers as $seller_id => $receiver) {
				if ($seller_id == 0) {
					// primary receiver (store)
					$paymentParams["receiverList.receiver($i).email"] = $this->config->get('msppaconf_receiver');
					$paymentParams["receiverList.receiver($i).amount"] = $total;
					$paymentParams["receiverList.receiver($i).primary"] = "true";
					$toPay -= $total;
					$i++;
				} else {
					// secondary receivers
					if (!empty($receiver['ms.paypal']) && $receiver['amount'] > 0 && filter_var($receiver['ms.paypal'], FILTER_VALIDATE_EMAIL)) {
						$payableAmount += $receiver['amount'];
						$toPay -= $receiver['amount'];
						$paymentParams["receiverList.receiver($i).email"] = $receiver['ms.paypal'];
						$paymentParams["receiverList.receiver($i).amount"] = $receiver['amount'];
						$i++;
					}
				}
			}
		} else if ($this->config->get('msppaconf_payment_type') == "PARALLEL") {
			// PARALLEL
			foreach ($receivers as $seller_id => $receiver) {
				if (!empty($receiver['ms.paypal']) && $receiver['amount'] > 0 && filter_var($receiver['ms.paypal'], FILTER_VALIDATE_EMAIL)) {
					$payableAmount += $receiver['amount'];
					$toPay -= $receiver['amount'];
					$paymentParams["receiverList.receiver($i).email"] = $receiver['ms.paypal'];
					$paymentParams["receiverList.receiver($i).amount"] = $receiver['amount'];
					$i++;
				}
			}
		} else {
			// SIMPLE
		}
		
		if ($toPay > 0) {
			if ($this->config->get('msppaconf_debug'))
				$this->_log->write("This shouldn't have happened.");
			
			return;
			
			$was = $paymentParams["receiverList.receiver(0).amount"];
			$paymentParams["receiverList.receiver(0).amount"] = round(abs($paymentParams["receiverList.receiver(0).amount"] + $toPay), '2', PHP_ROUND_HALF_DOWN);
			$now = $paymentParams["receiverList.receiver(0).amount"];
			if ($this->config->get('msppaconf_debug'))
				$this->_log->write("Adding leftover {$toPay} to the first receiver's amount, was {$was} now {$now}. Order ID {$order_info['order_id']}");
				
		}

		if ($this->config->get('msppaconf_debug'))
			$this->_log->write("Final transaction amounts: " . print_r($paymentParams, true));

		if ($this->config->get('msppaconf_debug'))
			$this->_log->write("Creating PayPal Request, Order ID {$order_info['order_id']}: " . print_r($requestParams + $paymentParams, true));
		
		if ($payableAmount > $total) {
			$this->_log->write("Configuration Error: Invalid Amount Distribution. Order ID: {$order_info['order_id']} Order total: {$total} Payable amount: {$payableAmount}");
			$json['error'] = sprintf($this->language->get('ppa_error_distribution'), $order_info['order_id']);
			$this->response->setOutput(json_encode($json));
			return;
		}		
		
		if (empty($paymentParams)) {
			$this->_log->write("Configuration Error: No valid receivers. Order ID: {$order_info['order_id']}");
			$json['error'] = sprintf($this->language->get('ppa_error_noreceivers'), $order_info['order_id']);
			$this->response->setOutput(json_encode($json));
			return;
		}

		$response = $this->_paypal->request('Pay',$requestParams + $paymentParams);

		if (!$response) {
			$this->_log->write("PayPal Request Error. Order ID {$order_info['order_id']}: " . $this->_paypal->getErrors());
			$json['error'] = "PayPal Request Error. Order ID {$order_info['order_id']}";			
		} else if (isset($response['responseEnvelope_ack']) && $response['responseEnvelope_ack'] != 'Success') {
				$this->_log->write("PayPal Request Error: " . print_r($response, true));
				$json['error'] = sprintf($this->language->get('ppa_error_request'), $response['responseEnvelope_correlationId']);
		} else if (isset($response['paymentExecStatus'])) {
			if ($this->config->get('msppaconf_debug'))
				$this->_log->write('Received PayPal Response: ' . print_r($response, true));
				
			switch ($response['paymentExecStatus']) {
				case "CREATED":
				case "COMPLETED":
				case "PROCESSING":
				case "PENDING":
					$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('msppaconf_pending_status_id'));
				
					if (!$this->config->get('msppaconf_sandbox')) {		
						$json['redirect'] = "https://www.paypal.com/webscr?cmd=_ap-payment&paykey={$response['payKey']}";
					} else {
						$json['redirect'] = "https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_ap-payment&paykey={$response['payKey']}";
					}
					
					$message = 'payKey: ' . $response['payKey'] . "\n";					
					$message .= 'Envelope_correlationId: ' . $response['responseEnvelope_correlationId'] . "\n";
					$message .= 'paymentExecStatus: ' . $response['paymentExecStatus'] . "\n";

					$this->model_checkout_order->update($this->session->data['order_id'], $this->config->get('msppaconf_pending_status_id'), $message, false);
					
					break;
					
				case "INCOMPLETE":
				case "ERROR":
				case "REVERSALERROR":
				default:
					$this->_log->write("PayPal Response Error: " . print_r($response, true));
					$json['error'] = sprintf($this->language->get('ppa_error_response'), $response['paymentExecStatus'], $response['responseEnvelope_correlationId']);
					break;
			}
		} else {
			$this->_log->write("PayPal Request Error: " . print_r($response, true));
			$json['error'] = sprintf($this->language->get('ppa_error_request'), $response['responseEnvelope_correlationId']);
		}

		$this->response->setOutput(json_encode($json));
	}	
}
?>