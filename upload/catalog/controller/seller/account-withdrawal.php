<?php

class ControllerSellerAccountWithdrawal extends ControllerSellerAccount {
	public function jxRequestMoney() {
		$data = $this->request->post;

		$seller = $this->MsLoader->MsSeller->getSeller($this->customer->getId());
		$balance = $this->MsLoader->MsBalance->getSellerBalance($this->customer->getId()) - $this->MsLoader->MsBalance->getReservedSellerFunds($this->customer->getId());

		$json = array();
		
		if (!$this->MsLoader->MsSeller->getPaypal()) {
			$json['errors']['withdraw_amount'] = $this->language->get('ms_account_withdraw_no_paypal');
			$this->response->setOutput(json_encode($json));
			return;
		}
		
		if (!isset($data['withdraw_amount'])) {
			$data['withdraw_amount'] = $balance;
		}
		
		if (preg_match("/[^0-9.]/",$data['withdraw_amount']) || (float)$data['withdraw_amount'] <= 0) {
			$json['errors']['withdraw_amount'] = $this->language->get('ms_error_withdraw_amount');
		} else {
			$data['withdraw_amount'] = (float)$data['withdraw_amount'];
			if ($data['withdraw_amount'] > $balance) {
				$json['errors']['withdraw_amount'] = $this->language->get('ms_error_withdraw_balance');
			} else if ($data['withdraw_amount'] < $this->config->get('msconf_minimum_withdrawal_amount')) {
				$json['errors']['withdraw_amount'] = $this->language->get('ms_error_withdraw_minimum');
			}			
		}
		
		if (empty($json['errors'])) {
			$this->MsLoader->MsWithdrawal->createWithdrawal($this->customer->getId(),
				array(
					'amount' => $data['withdraw_amount'],
					'currency_id' => $this->currency->getId($this->config->get('config_currency')),
					'currency_code' => $this->currency->getCode($this->config->get('config_currency')),
					'currency_value' => "1",
				)
			);
			
			$mails[] = array(
				'type' => MsMail::SMT_WITHDRAW_REQUEST_SUBMITTED
			);
			$mails[] = array(
				'type' => MsMail::AMT_WITHDRAW_REQUEST_SUBMITTED,
			);
			
			$this->MsLoader->MsMail->sendMails($mails);
			
			$this->session->data['success'] = $this->language->get('ms_request_submitted');
			$json['redirect'] = $this->url->link('account/account', '', 'SSL');
		}
		$this->response->setOutput(json_encode($json));
	}
	
	public function index() {
		if (!$this->config->get('msconf_allow_withdrawal_requests'))
			$this->redirect($this->url->link('account/account', '', 'SSL'));
		
		$seller_id = $this->customer->getId();
		
		$seller_balance = $this->MsLoader->MsBalance->getSellerBalance($seller_id);
		$available_balance = $seller_balance - $this->MsLoader->MsBalance->getReservedSellerFunds($seller_id);
		
		$this->document->addScript('catalog/view/javascript/account-withdrawal.js');
		
		$this->data['balance'] =  $seller_balance;
		$this->data['balance_formatted'] =  $this->currency->format($this->data['balance'],$this->config->get('config_currency'));

		$this->data['balance_available'] =  $available_balance;
		$this->data['balance_available_formatted'] =  $this->currency->format($available_balance, $this->config->get('config_currency'));
		
		$this->data['paypal'] =  $this->MsLoader->MsSeller->getPaypal();
		$this->data['msconf_minimum_withdrawal_amount'] =  $this->currency->format($this->config->get('msconf_minimum_withdrawal_amount'),$this->config->get('config_currency'));
		$this->data['msconf_allow_partial_withdrawal'] = $this->config->get('msconf_allow_partial_withdrawal');
		$this->data['currency_code'] = $this->config->get('config_currency');
		
		if ($available_balance - $this->config->get('msconf_minimum_withdrawal_amount') > 0) {
			$this->data['withdrawal_minimum_reached'] = TRUE;
		} else {
			$this->data['withdrawal_minimum_reached'] = FALSE;
		}
			
		$this->data['back'] = $this->url->link('account/account', '', 'SSL');
		$this->document->setTitle($this->language->get('ms_account_withdraw_heading'));
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_withdraw_breadcrumbs'),
				'href' => $this->url->link('seller/account-withdrawal', '', 'SSL'),
			)
		));		
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->loadTemplate('account-withdrawal');
		$this->response->setOutput($this->render());
	}
}

?>
