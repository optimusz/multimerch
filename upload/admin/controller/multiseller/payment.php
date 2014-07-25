<?php

class ControllerMultisellerPayment extends ControllerMultisellerBase {
	public function getTableData() {
		$colMap = array(
			'seller' => 'ms.nickname',
			'type' => 'payment_type',
			'description' => 'mpay.description',
			'date_created' => 'mpay.date_created',
			'date_paid' => 'mpay.date_paid'
		);
		
		$sorts = array('payment_type', 'seller', 'amount', 'description', 'payment_status', 'date_created', 'date_paid');
		$filters = array_diff($sorts, array('payment_status', 'type'));
		
		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsPayment->getPayments(
			array(),
			array(
				'order_by'  => $sortCol,
				'order_way' => $sortDir,
				'filters' => $filterParams,
				'offset' => $this->request->get['iDisplayStart'],
				'limit' => $this->request->get['iDisplayLength']
			)
		);

		$total = isset($results[0]) ? $results[0]['total_rows'] : 0;

		$columns = array();
		foreach ($results as $result) {
			// actions
			$actions = "";
			if ($result['amount'] > 0 && $result['payment_status'] == MsPayment::STATUS_UNPAID && in_array($result['payment_type'], array(MsPayment::TYPE_PAYOUT, MsPayment::TYPE_PAYOUT_REQUEST))) {
			if (!empty($result['payment_data']) && filter_var($result['payment_data'], FILTER_VALIDATE_EMAIL)) { 
				$actions .= "<a class='ms-button ms-button-paypal' title='" . $this->language->get('ms_payment_payout_paypal') . "'></a>";
			} else {
				$actions .= "<a class='ms-button ms-button-paypal-bw' title='" . $this->language->get('ms_payment_payout_paypal_invalid') . "'></a>";
			}
			}
			if ($result['amount'] > 0 && $result['payment_status'] == MsPayment::STATUS_UNPAID) { 
				$actions .= "<a class='ms-button ms-button-mark' title='" . $this->language->get('ms_payment_mark') . "'></a>";
			}
			$actions .= "<a class='ms-button ms-button-delete' title='" . $this->language->get('ms_payment_delete') . "'></a>";
			
			// paymentstatus
			$paymentstatus = "<select name='ms-payment-status'>";
			
			$msPayment = new ReflectionClass('MsPayment');
			foreach ($msPayment->getConstants() as $cname => $cval) {
			if (strpos($cname, 'STATUS_') !== FALSE) {
				$paymentstatus .= "<option value='$cval'" . ($result['payment_status'] == $cval ? "selected='selected'" : '') . ">" . $this->language->get('ms_payment_status_' . $cval) . "</option>";
			}
			}
			$paymentstatus .= "
			</select>
			<span class='ms-button-small ms-button-apply ms-button-status' title='Save' />
			";

			
			$columns[] = array_merge(
				$result,
				array(
					'checkbox' => "<input type='checkbox' name='selected[]' value='{$result['payment_id']}' />",
					'payment_type' => $this->language->get('ms_payment_type_' . $result['payment_type']),
					'seller' => "<a href='".$this->url->link('multiseller/seller/update', 'token=' . $this->session->data['token'] . '&seller_id=' . $result['seller_id'], 'SSL')."'>{$result['nickname']}</a>",
					'amount' => $this->currency->format(abs($result['amount']),$result['currency_code']),
					'description' => (mb_strlen($result['mpay.description']) > 80 ? mb_substr($result['mpay.description'], 0, 80) . '...' : $result['mpay.description']),
					'payment_status' => $paymentstatus,
					'date_created' => date($this->language->get('date_format_short'), strtotime($result['mpay.date_created'])),
					'date_paid' => $result['mpay.date_paid'] ? date($this->language->get('date_format_short'), strtotime($result['mpay.date_paid'])) : '',
					'actions' => $actions
				)
			);
		}
		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => $total,
			'iTotalDisplayRecords' => $total,
			'aaData' => $columns
		)));
	}
		
	public function index() {
		$this->validate(__FUNCTION__);
		
		// paypal listing payment confirmation
		if (isset($this->request->post['payment_status']) && strtolower($this->request->post['payment_status']) == 'completed') {
			$this->data['success'] = $this->language->get('ms_payment_completed');
		}
				
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
		
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
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
	
	public function create() {
		$results = $this->MsLoader->MsSeller->getSellers(
			array(),
			array(
				'order_by'  => 'ms.nickname',
				'order_way' => 'ASC',
			)
		);
	
		foreach ($results as $r) {
			$this->data['sellers'][] = array(
				'name' => "{$r['ms.nickname']} ({$r['c.name']})",
				'seller_id' => $r['seller_id']
				);
		}
	
		$msPayment = new ReflectionClass('MsPayment');
		foreach ($msPayment->getConstants() as $cname => $cval) {
			if (strpos($cname, 'TYPE_') !== FALSE && !in_array($cname, array('TYPE_RECURRING', 'TYPE_SALE'))) {
				$this->data['payment_types'][$cval] = $this->language->get('ms_payment_type_' . $cval);
			}
		}

		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_payment_new');
		$this->document->setTitle($this->language->get('ms_payment_new'));
	
		$this->load->model('setting/setting');
		$store_info = $this->model_setting_setting->getSetting('config', 0);
		$this->data['store_name'] = $store_info['config_name'];
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multiseller/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_payment_breadcrumbs'),
				'href' => $this->url->link('multiseller/transaction', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_payment_new'),
				'href' => $this->url->link('multiseller/transaction', '', 'SSL'),
			)
		));
	
		list($this->template, $this->children) = $this->MsLoader->MsHelper->admLoadTemplate('payment-form');
		$this->response->setOutput($this->render());
	}
	
	public function jxSave() {
		$json = array();
		$data = $this->request->post['payment'];
		
		if ((!$data['from'] && !$data['to']) || ($data['from'] && $data['to'])) {
			$json['errors']['payment[from]'] = $this->language->get('ms_error_payment_fromto');
			$json['errors']['payment[to]'] = $this->language->get('ms_error_payment_fromto');
		}

		if (!$data['from'] && !in_array($data['type'], array(MsPayment::TYPE_PAYOUT, MsPayment::TYPE_PAYOUT_REQUEST))) {
			$json['errors']['payment[type]'] = $this->language->get('ms_error_payment_fromstore');
		} else if ($data['from'] && in_array($data['type'], array(MsPayment::TYPE_PAYOUT, MsPayment::TYPE_PAYOUT_REQUEST))) {
			$json['errors']['payment[type]'] = $this->language->get('ms_error_payment_tostore');
		}
		
		if ((float)$data['amount'] <= 0) {
			$json['errors']['payment[amount]'] = $this->language->get('ms_error_payment_amount');
		}
	
		if (empty($json['errors'])) {
			$payment_id = $this->MsLoader->MsPayment->createPayment(array(
				'seller_id' => ($data['from'] ? $data['from'] : $data['to']),
				'payment_type' => $data['type'],
				'payment_status' => (isset($data['paid']) && $data['paid'] ? 2 : 1),
				'payment_method' => $data['method'],
				'amount' => $data['amount'],
				'currency_id' => $this->currency->getId($this->config->get('config_currency')),
				'currency_code' => $this->currency->getCode($this->config->get('config_currency')),
				'description' => $data['description']
			));

			if (isset($data['paid']) && $data['paid']) {
				$this->MsLoader->MsPayment->updatePayment($payment_id, array(
					'date_paid' => date( 'Y-m-d H:i:s')
				));
			}
			
			if (isset($data['deduct']) && $data['deduct']) {
				switch ($data['type']) {
					case MsPayment::TYPE_SIGNUP:
						$balance_type = MsBalance::MS_BALANCE_TYPE_SIGNUP;
						break;
						
					case MsPayment::TYPE_LISTING:
						$balance_type = MsBalance::MS_BALANCE_TYPE_LISTING;
						break;
						
					case MsPayment::TYPE_PAYOUT:
					case MsPayment::TYPE_PAYOUT_REQUEST:
						$balance_type = MsBalance::MS_BALANCE_TYPE_WITHDRAWAL;
						break;
						
					default: 
						$balance_type = MsBalance::MS_BALANCE_TYPE_GENERIC;
				}
				
				$this->MsLoader->MsBalance->addBalanceEntry($data['from'] ? $data['from'] : $data['to'], array(
					'balance_type' => $balance_type,
					'amount' => -$data['amount'],
					'description' => $data['description']
				));
			}
			
			$this->session->data['success'] = $this->language->get('ms_success_payment_created');
		} else {
		}
	
		$this->response->setOutput(json_encode($json));
	}
	
	// todo
	public function jxPay() {
		$json = array();
		$payment_id = isset($this->request->get['payment_id']) ? $this->request->get['payment_id'] : 0;
		$payment = $this->MsLoader->MsPayment->getPayments(array('payment_id' => $payment_id, 'single' => 1));
		if (empty($payment['payment_data']) || !filter_var($payment['payment_data'], FILTER_VALIDATE_EMAIL) || !$payment || !$payment['amount'] || $payment['amount'] <= 0 || $payment['payment_status'] == MsPayment::STATUS_PAID) return;

		
		$seller = $this->MsLoader->MsSeller->getSeller($payment['seller_id']);
		if (!$seller) return;
		
		// render paypal form
		$this->data['payment_data'] = array(
			'sandbox' => $this->config->get('msconf_paypal_sandbox'),
			'action' => $this->config->get('msconf_paypal_sandbox') ? "https://www.sandbox.paypal.com/cgi-bin/webscr" : "https://www.paypal.com/cgi-bin/webscr",
			'business' => $payment['payment_data'],
			'item_name' => $payment['mpay.description'] ? $payment['mpay.description'] : sprintf($this->language->get('ms_payment_generic'), $payment['payment_id'], $this->config->get('config_name')),
			'amount' => $this->currency->format($payment['amount'], $this->config->get('config_currency'), '', FALSE),
			'currency_code' => $this->config->get('config_currency'),
			'return' => $this->url->link('multiseller/payment', 'token=' . $this->session->data['token']),
			'cancel_return' => $this->url->link('multiseller/payment', 'token=' . $this->session->data['token']),
			'notify_url' => HTTP_CATALOG . 'index.php?route=payment/multimerch-paypal/payoutIPN',
			'custom' => $payment_id
		);
		
		list($this->template) = $this->MsLoader->MsHelper->admLoadTemplate('payment/multimerch-paypal');
		
		$json['form'] = $this->render();
		$json['success'] = 1;
		$this->response->setOutput(json_encode($json));
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
}
?>
