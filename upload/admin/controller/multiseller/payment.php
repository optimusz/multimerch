<?php

class ControllerMultisellerPayment extends ControllerMultisellerBase {
	public function index() {
		$this->validate(__FUNCTION__);
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
		
		$this->data['payout_requests']['amount_pending'] = $this->currency->format($this->MsLoader->MsPayment->getTotalAmount(array(
			'payment_type' => array(MsPayment::TYPE_PAYOUT_REQUEST),
			'payment_status' => array(MsPayment::STATUS_UNPAID)
		)), $this->config->get('config_currency'));
		
		$this->data['payout_requests']['amount_paid'] = $this->currency->format($this->MsLoader->MsPayment->getTotalAmount(array(
			'payment_type' => array(MsPayment::TYPE_PAYOUT_REQUEST),
			'payment_status' => array(MsPayment::STATUS_PAID)
		)), $this->config->get('config_currency'));

		$this->data['payouts']['amount_pending'] = $this->currency->format($this->MsLoader->MsPayment->getTotalAmount(array(
			'payment_type' => array(MsPayment::TYPE_PAYOUT),
			'payment_status' => array(MsPayment::STATUS_UNPAID)
		)), $this->config->get('config_currency'));
		
		$this->data['payouts']['amount_paid'] = $this->currency->format($this->MsLoader->MsPayment->getTotalAmount(array(
			'payment_type' => array(MsPayment::TYPE_PAYOUT),
			'payment_status' => array(MsPayment::STATUS_PAID)
		)), $this->config->get('config_currency'));
		
		$sort = array(
			'order_by'  => 'mpay.date_created',
			'order_way' => 'DESC',
			'offset' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);

		$results = $this->MsLoader->MsPayment->getPayments(array(), $sort);
		$total_payments = $this->MsLoader->MsPayment->getTotalPayments(array());

		foreach ($results as $result) {
			$this->data['payments'][] = array_merge(
				$result,
				array(
					'amount_text' => $this->currency->format(abs($result['amount']),$result['currency_code']),
					'description' => (mb_strlen($result['mpay.description']) > 80 ? mb_substr($result['mpay.description'], 0, 80) . '...' : $result['mpay.description']),
					'date_created' => date($this->language->get('date_format_short'), strtotime($result['mpay.date_created'])),
					'date_paid' => $result['mpay.date_paid'] ? date($this->language->get('date_format_short'), strtotime($result['mpay.date_paid'])) : ''					
				)
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $total_payments;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link("multiseller/payment", 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');
		$this->data['pagination'] = $pagination->render();
		
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}		
		
		$this->data['token'] = $this->session->data['token'];		
		$this->data['heading'] = $this->language->get('ms_payment_heading');
		$this->document->setTitle($this->language->get('ms_payment_heading'));
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multiseller/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_payment_breadcrumbs'),
				'href' => $this->url->link('multiseller/payment', '', 'SSL'),
			)
			/*
			array(
				'text' => $this->language->get('ms_payment_breadcrumbs'),
				'href' => $this->url->link('multiseller/request/withdrawal', '', 'SSL'),
			)*/
		));
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->admLoadTemplate('payment');
		$this->response->setOutput($this->render());
	}
	
	public function jxUpdateStatus() {
		$data = $this->request->get;
		$json = array();
		if (isset($data['payment_id']) && isset($data['payment_status'])) {
			$this->MsLoader->MsPayment->updatePayment($data['payment_id'], array(
				'payment_status' => $data['payment_status'],
				'date_paid' => ($data['payment_status'] == MsPayment::STATUS_PAID ? date( 'Y-m-d H:i:s') : NULL)
			));
			
			$payment = $this->MsLoader->MsPayment->getPayments(array('payment_id' => $data['payment_id'], 'single' => 1));
			
			switch($data['payment_status']) {
				case MsPayment::STATUS_PAID:
					switch($payment['payment_type']) {
						case MsPayment::TYPE_SIGNUP:
							// activate seller
							$this->MsLoader->MsSeller->changeStatus($payment['seller_id'], MsSeller::STATUS_ACTIVE);
							break;
							
						case MsPayment::TYPE_LISTING:
							// publish product
							$this->MsLoader->MsProduct->changeStatus($payment['product_id'], MsProduct::STATUS_ACTIVE);
							break;
							
						case MsPayment::TYPE_PAYOUT:
						case MsPayment::TYPE_PAYOUT_REQUEST:
							// charge balance
							$this->MsLoader->MsBalance->addBalanceEntry($payment['seller_id'], array(
								'withdrawal_id' => $payment['payment_id'],
								'balance_type' => MsBalance::MS_BALANCE_TYPE_WITHDRAWAL,
								'amount' => -$payment['amount'],
								'description' => $payment['mpay.description']
							));
							break;
					}
					break;
			}
			
			$json['payment'] = array(
				'payment_type' => $payment['payment_type'],
				'payment_status' => $payment['payment_status'],
				'payment_date' => $payment['mpay.date_paid'] ? date($this->language->get('date_format_short'), strtotime($payment['mpay.date_paid'])) : ''
			);
		}
		
		return $this->response->setOutput(json_encode($json));
	}
	
	public function jxDelete() {
		$data = $this->request->get;
		$json = array();
		if (isset($data['payment_id'])) {
			$this->MsLoader->MsPayment->deletePayment($data['payment_id']);
			$this->session->data['success'] = $this->language->get('ms_success_payment_deleted');
		}
		
		return $this->response->setOutput(json_encode($json));
	}	
	
	public function jxConfirmPayment() {
		$this->validate(__FUNCTION__);
		$json = array();

		if (isset($this->request->post['selected'])) {
			$payments = array();
			$total = 0;
			foreach ($this->request->post['selected'] as $payment_id) {
				$result = $this->MsLoader->MsPayment->getPayments(
					array(
						'payment_id' => $payment_id,
						'payment_status' => array(MsPayment::STATUS_UNPAID),
						'payment_type' => array(MsPayment::TYPE_PAYOUT, MsPayment::TYPE_PAYOUT_REQUEST),
						'single' => 1
					)
				);
				
				if ($result) {
					$total += abs($result['amount']);
					$payments[] = array (
						'nickname' => $result['nickname'],
						'paypal' => $result['paypal'],
						'amount' => $this->currency->format(abs($result['amount']),$result['currency_code'])
					);
				}
			}
			
			if ($payments) {
				$this->data['total_amount'] = $this->currency->format($total, $this->config->get('config_currency'));
				$this->data['payments'] = $payments;
				list($this->template, $this->children) = $this->MsLoader->MsHelper->admLoadTemplate('dialog-withdrawal-masspay');
				$json['html'] = $this->render();
			} else {
				$json['error'] = $this->language->get('ms_error_withdraw_norequests');
			}
		} else {
			$json['error'] = $this->language->get('ms_error_withdraw_norequests');
		}
		return $this->response->setOutput(json_encode($json));
	}
	
	public function jxCompletePayment() {
		$this->validate(__FUNCTION__);
		$json = array();
		
		if (!isset($this->request->post['selected'])) {
			$json['error'] = $this->language->get('ms_error_withdraw_norequests');
			$this->response->setOutput(json_encode($json));
			return;
		}
		
		require_once(DIR_SYSTEM . 'library/ms-paypal.php');

		$requestParams = array(
			'RECEIVERTYPE' => 'EmailAddress',
			'CURRENCYCODE' => $this->config->get('config_currency')
		);
		
		$paymentParams = array();
		
		$i = 0;		
		foreach ($this->request->post['selected'] as $payment_id) {
			$result = $this->MsLoader->MsPayment->getPayments(
				array(
					'payment_id' => $payment_id,
					'payment_status' => array(MsPayment::STATUS_UNPAID),
					'payment_type' => array(MsPayment::TYPE_PAYOUT, MsPayment::TYPE_PAYOUT_REQUEST),
					'single' => 1
				)
			);
			
			if (!empty($result)) {
				$paymentParams['L_EMAIL' . $i] = $result['ms.paypal'];
				$paymentParams['L_AMT' . $i] = abs($result['mw.amount']);
				$i++;
			}
		}
		
		if (empty($paymentParams)) {
			$json['error'] = $this->language->get('ms_error_withdraw_norequests');
			$this->response->setOutput(json_encode($json));
			return;
		}
		
		$paypal = new PayPal($this->config->get('msconf_paypal_api_username'), $this->config->get('msconf_paypal_api_password'), $this->config->get('msconf_paypal_api_signature'), $this->config->get('msconf_paypal_sandbox'));
		$response = $paypal->request('MassPay',$requestParams + $paymentParams);
		
		if (!$response) {
			$json['error'] = $this->language->get('ms_error_withdraw_response');
			$json['response'] = print_r($paypal->getErrors(), true);
		} else if ($response['ACK'] != 'Success') {
			$json['error'] = $this->language->get('ms_error_withdraw_status');
			$json['response'] = print_r($response, true);
		} else {
			$json['success'] = $this->language->get('ms_success_transactions');
			$json['response'] = print_r($response, true);
			//$mails = array();
			foreach ($this->request->post['selected'] as $payment_id) {
				$result = array_shift($this->MsLoader->MsPayment->getPayments(
					array(
						'payment_id' => $payment_id,
						'payment_status' => array(MsPayment::STATUS_UNPAID),
						'payment_type' => array(MsPayment::TYPE_PAYOUT, MsPayment::TYPE_PAYOUT_REQUEST),
						'single' => 1
					)
				));
				
				$this->MsLoader->MsPayment->updatePayment($payment_id,
					array(
						'payment_status' => MsPayment::STATUS_PAID,
						'description' => 'Paid',
						'date_paid' => 1
					)
				);
				
				$this->MsLoader->MsBalance->addBalanceEntry(
					$result['seller_id'],
					array(
						'payment_id' => $payment_id,
						'balance_type' => MsBalance::MS_BALANCE_TYPE_WITHDRAWAL,
						'amount' => -$result['amount'],
						'description' => 'Payout'
					)
				);
			}
		}
		
		return $this->response->setOutput(json_encode($json));
	}
}
?>
