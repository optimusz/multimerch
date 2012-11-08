<?php

class ControllerMultisellerRequestWithdrawal extends ControllerMultisellerBase {
	public function index() {
		$this->validate(__FUNCTION__);
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
		
		$sort = array(
			'order_by'  => 'mw.date_created',
			'order_way' => 'DESC',
			'offset' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);

		$results = $this->MsLoader->MsWithdrawal->getWithdrawals(array(), $sort);
		$total_withdrawals = $this->MsLoader->MsWithdrawal->getTotalWithdrawals(array());

		foreach ($results as $result) {
			$this->data['withdrawals'][] = array(
				'withdrawal_id' => $result['withdrawal_id'],
				'seller' => $result['nickname'],
				'amount' => $this->currency->format(abs($result['mw.amount']),$result['mw.currency_code']),
				'date_created' => date($this->language->get('date_format_short'), strtotime($result['mw.date_created'])),
				'status' => empty($result['mw.date_processed']) ? 'Pending' : 'Completed',
				'processed_by' => $result['u.username'],
				'date_processed' => $result['mw.date_processed'] ? date($this->language->get('date_format_short'), strtotime($result['mw.date_processed'])) : ''
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $total_withdrawals;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link("multiseller/request-withdrawal", 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');
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
		$this->data['heading'] = $this->language->get('ms_finances_withdrawals_heading');
		$this->document->setTitle($this->language->get('ms_finances_withdrawals_heading'));
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multiseller/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_finances_withdrawals_breadcrumbs'),
				'href' => $this->url->link('multiseller/request-withdrawal', '', 'SSL'),
			)
			/*
			array(
				'text' => $this->language->get('ms_finances_withdrawals_breadcrumbs'),
				'href' => $this->url->link('multiseller/request/withdrawal', '', 'SSL'),
			)*/
		));
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->admLoadTemplate('request-withdrawal');
		$this->response->setOutput($this->render());
	}
	
	public function jxConfirmPayment() {
		$this->validate(__FUNCTION__);
		$json = array();

		if (isset($this->request->post['selected'])) {
			$payments = array();
			$total = 0;
			foreach ($this->request->post['selected'] as $withdrawal_id) {
				$result = $this->MsLoader->MsWithdrawal->getWithdrawals(
					array(
						'withdrawal_id' => $withdrawal_id,
						'withdrawal_status' => array(MsWithdrawal::STATUS_PENDING)
					)
				);
				
				$result = array_shift($result);

				if (!empty($result) && ($result['mw.date_processed'] == NULL)) {
					$total += abs($result['mw.amount']);
					$payments[] = array (
						'nickname' => $result['ms.nickname'],
						'paypal' => $result['ms.paypal'],
						'amount' => $this->currency->format(abs($result['mw.amount']),$result['mw.currency_code'])
					);
				}
			}
			
			if (!empty($payments)) {
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
	
	public function jxConfirmWithdrawalPaid() {
		$this->validate(__FUNCTION__);
		$json = array();

		if (isset($this->request->post['selected'])) {
			$payments = array();
			$total = 0;
			foreach ($this->request->post['selected'] as $withdrawal_id) {
				$result = $this->MsLoader->MsWithdrawal->getWithdrawals(
					array(
						'withdrawal_id' => $withdrawal_id,
						'withdrawal_status' => array(MsWithdrawal::STATUS_PENDING)
					)
				);
				
				$result = array_shift($result);
				
				if (!empty($result) && ($result['mw.date_processed'] == NULL)) {
					$payments[] = array (
						'nickname' => $result['ms.nickname'],
						'amount' => $this->currency->format(abs($result['mw.amount']),$result['mw.currency_code'])
					);
				}
			}
			
			if (!empty($payments)) {
				$this->data['total_amount'] = $this->currency->format($total, $this->config->get('config_currency'));
				$this->data['payments'] = $payments;
				list($this->template, $this->children) = $this->MsLoader->MsHelper->admLoadTemplate('dialog-withdrawal-markpaid');
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
		foreach ($this->request->post['selected'] as $withdrawal_id) {
			$result = $this->MsLoader->MsWithdrawal->getWithdrawals(
				array(
					'withdrawal_id' => $withdrawal_id,
					'withdrawal_status' => array(MsWithdrawal::STATUS_PENDING)
				)
			);
			
			$result = array_shift($result);
			
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
			foreach ($this->request->post['selected'] as $withdrawal_id) {
				$result = array_shift($this->MsLoader->MsWithdrawal->getWithdrawals(
					array(
						'withdrawal_id' => $withdrawal_id,
						'withdrawal_status' => array(MsWithdrawal::STATUS_PENDING)						
					)
				));
				
				$result = array_shift($result);
				
				$this->MsLoader->MsWithdrawal->processWithdrawal($withdrawal_id,
					array(
						'withdrawal_status' => MsWithdrawal::STATUS_PAID,
						'processed_by' => $this->user->getId(),
						'description' => 'Paid'
					)
				);
				
				$this->MsLoader->MsBalance->addBalanceEntry(
					$result['seller_id'],
					array(
						'withdrawal_id' => $withdrawal_id,
						'balance_type' => MsBalance::MS_BALANCE_TYPE_WITHDRAWAL,
						'amount' => -1 * $result['mw.amount'],
						'description' => 'Withdrawal'
					)
				);
			}
		}
		
		return $this->response->setOutput(json_encode($json));
	}

	public function jxCompleteWithdrawalPaid() {
		$this->validate(__FUNCTION__);
		$json = array();
		
		if (!isset($this->request->post['selected'])) {
			$json['error'] = $this->language->get('ms_error_withdraw_norequests');
			$this->response->setOutput(json_encode($json));
			return;
		}
		
		$i = 0;		
		foreach ($this->request->post['selected'] as $withdrawal_id) {
			$result = $this->MsLoader->MsWithdrawal->getWithdrawals(
				array(
					'withdrawal_id' => $withdrawal_id,
					'withdrawal_status' => array(MsWithdrawal::STATUS_PENDING)					
				)
			);
			
			$result = array_shift($result);
			
			$this->MsLoader->MsWithdrawal->processWithdrawal($withdrawal_id,
				array(
					'withdrawal_status' => MsWithdrawal::STATUS_PAID,
					'processed_by' => $this->user->getId(),
					'description' => 'Marked as paid'
				)
			);
			
			$this->MsLoader->MsBalance->addBalanceEntry(
				$result['seller_id'],
				array(
					'withdrawal_id' => $withdrawal_id,
					'balance_type' => MsBalance::MS_BALANCE_TYPE_WITHDRAWAL,
					'amount' => -1 * $result['mw.amount'],
					'description' => 'Withdrawal'
				)
			);
		}
		
		$json['success'] = $this->language->get('ms_success_withdrawals_marked');
		return $this->response->setOutput(json_encode($json));
	}
}
?>
