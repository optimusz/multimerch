<?php

class ControllerMultisellerRequestWithdrawal extends ControllerMultisellerBase {
	public function index() {
		$this->validate(__FUNCTION__);
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
		
		$sort = array(
			'order_by'  => 'mr.date_created',
			'order_way' => 'DESC',
			'page' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);

		$results = $this->MsLoader->MsRequestWithdrawal->getWithdrawalRequests(array(), $sort);
		$total_withdrawals = $this->MsLoader->MsRequest->getTotalRequests(MsRequestWithdrawal::TYPE_WITHDRAWAL_SUBMIT);

		foreach ($results as $result) {
			$this->data['requests'][] = array(
				'request_id' => $result['request_id'],
				'seller' => $result['nickname'],
				'amount' => $this->currency->format(abs($result['mrw.amount']),$result['mrw.currency_code']),
				'date_created' => date($this->language->get('date_format_short'), strtotime($result['mr.date_created'])),
				'status' => empty($result['mr.date_processed']) ? 'Pending' : 'Completed',
				'processed_by' => $result['u.username'],
				'date_processed' => $result['mr.date_processed'] ? date($this->language->get('date_format_short'), strtotime($result['mr.date_processed'])) : ''
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $total_withdrawals;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link("module/{$this->name}/withdrawals", 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');
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
			foreach ($this->request->post['selected'] as $request_id) {
				$result = $this->MsLoader->MsRequestWithdrawal->getWithdrawalRequests(
					array(
						'request_id' => $request_id
					),
					array(
						'page' => 0,
						'limit' => 1
					)
				);

				if (!empty($result) && ($result['mr.date_processed'] == NULL)) {
					$total += abs($result['mrw.amount']);
					$payments[] = array (
						'nickname' => $result['ms.nickname'],
						'paypal' => $result['ms.paypal'],
						'amount' => $this->currency->format(abs($result['mrw.amount']),$result['mrw.currency_code'])
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
	
	public function jxConfirmWithdrawalPaid() {
		$this->validate(__FUNCTION__);
		$json = array();

		if (isset($this->request->post['selected'])) {
			$payments = array();
			$total = 0;
			foreach ($this->request->post['selected'] as $request_id) {
				$result = $this->MsLoader->MsRequestWithdrawal->getWithdrawalRequests(
					array(
						'request_id' => $request_id
					),
					array(
						'page' => 0,
						'limit' => 1
					)
				);
				
				if (!empty($result) && ($result['mr.date_processed'] == NULL)) {
					$payments[] = array (
						'nickname' => $result['ms.nickname'],
						'amount' => $this->currency->format(abs($result['mrw.amount']),$result['mrw.currency_code'])
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
		foreach ($this->request->post['selected'] as $request_id) {
			$result = $this->MsLoader->MsRequestWithdrawal->getWithdrawalRequests(
				array(
					'request_id' => $request_id
				),
				array(
					'page' => 0,
					'limit' => 1
				)
			);
			if (!empty($result) && ($result['mr.date_processed'] == NULL)) {
				$paymentParams['L_EMAIL' . $i] = $result['ms.paypal'];
				$paymentParams['L_AMT' . $i] = abs($result['mrw.amount']);
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
			foreach ($this->request->post['selected'] as $request_id) {
				$result = $this->MsLoader->MsRequestWithdrawal->getWithdrawalRequests(
					array(
						'request_id' => $request_id
					),
					array(
						'page' => 0,
						'limit' => 1
					)
				);
				
				$this->MsLoader->MsRequest->processRequest($request_id,
					array(
						'resolution_type' => MsRequest::RESOLUTION_APPROVED,
						'processed_by' => $this->user->getId(),
						'message_processed' => 'Paid'
					)
				);
				
				$this->MsLoader->MsBalance->addBalanceEntry(
					$result['seller_id'],
					array(
						'withdrawal_id' => $request_id,
						'balance_type' => MsBalance::MS_BALANCE_TYPE_WITHDRAWAL,
						'amount' => -1 * $result['mrw.amount'],
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
		foreach ($this->request->post['selected'] as $request_id) {
			$result = $this->MsLoader->MsRequestWithdrawal->getWithdrawalRequests(
				array(
					'request_id' => $request_id
				),
				array(
					'page' => 0,
					'limit' => 1
				)
			);			
			
			$this->MsLoader->MsRequest->processRequest($request_id,
				array(
					'resolution_type' => MsRequest::RESOLUTION_APPROVED,
					'processed_by' => $this->user->getId(),
					'message_processed' => 'Marked as paid'
				)
			);
			
			$this->MsLoader->MsBalance->addBalanceEntry(
				$result['seller_id'],
				array(
					'withdrawal_id' => $request_id,
					'balance_type' => MsBalance::MS_BALANCE_TYPE_WITHDRAWAL,
					'amount' => -1 * $result['mrw.amount'],
					'description' => 'Withdrawal'
				)
			);
		}
		$json['success'] = $this->language->get('ms_success_withdrawals_marked');
		return $this->response->setOutput(json_encode($json));
	}
}
?>
