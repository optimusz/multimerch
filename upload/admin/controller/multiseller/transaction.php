<?php

class ControllerMultisellerTransaction extends ControllerMultisellerBase {
	public function index() {
		$this->validate(__FUNCTION__);
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;

		$sort = array(
			'order_by'  => 'mb.date_created',
			'order_way' => 'DESC',
			'page' => $page,
			'limit' => $this->config->get('config_admin_limit')
		);

		$balance_entries = $this->MsLoader->MsBalance->getBalanceEntries($sort);

		foreach ($balance_entries as $result) {
			$this->data['transactions'][] = array(
				'seller' => $result['nickname'],
				'description' => (mb_strlen($result['mb.description']) > 80 ? mb_substr($result['mb.description'], 0, 80) . '...' : $result['mb.description']),
				'net_amount' => $this->currency->format($result['amount'], $this->config->get('config_currency')),			
				'date_created' => date($this->language->get('date_format_short'), strtotime($result['mb.date_created'])),
				//'date_modified' => date($this->language->get('date_format_short'), strtotime($result['trn.date_modified'])),
				//'status' => empty($result['req.date_processed']) ? 'Pending' : 'Completed',
			);
		}
		
		$pagination = new Pagination();
		$pagination->page = $page;
		$pagination->total = $this->MsLoader->MsBalance->getTotalBalanceEntries();		
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link("multiseller/transaction", 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');
		
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

		$this->data['link_create_transaction'] = $this->url->link('multiseller/transaction/create', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_transactions_heading');
		$this->document->setTitle($this->language->get('ms_transactions_heading'));
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multiseller/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_transactions_breadcrumbs'),
				'href' => $this->url->link('multiseller/transaction', '', 'SSL'),
			)
		));
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->admLoadTemplate('transaction');
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
		
		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_transactions_heading');
		$this->document->setTitle($this->language->get('ms_transactions_heading'));
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multiseller/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_transactions_breadcrumbs'),
				'href' => $this->url->link('multiseller/transaction', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_transactions_new'),
				'href' => $this->url->link('multiseller/transaction', '', 'SSL'),
			)			
		));
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->admLoadTemplate('transaction-form');
		$this->response->setOutput($this->render());		
	}
	
	public function jxSave() {
		$json = array();
		$data = $this->request->post['transaction'];
		if (!$data['from'] && !$data['to']) {
			$json['errors']['transaction[from]'] = $this->language->get('ms_error_transaction_fromto');
			$json['errors']['transaction[to]'] = $this->language->get('ms_error_transaction_fromto');
		} else if ($data['from'] == $data['to']) {
			$json['errors']['transaction[from]'] = $this->language->get('ms_error_transaction_fromto_same');
			$json['errors']['transaction[to]'] = $this->language->get('ms_error_transaction_fromto_same');
		}
		
		if ((float)$data['amount'] <= 0) {
			$json['errors']['transaction[amount]'] = $this->language->get('ms_error_transaction_amount');
		}
		
		if (empty($json['errors'])) {
			if($data['from']) {
				$this->MsLoader->MsBalance->addBalanceEntry($data['from'], array(
					'balance_type' => MsBalance::MS_BALANCE_TYPE_GENERIC,
					'amount' => -$data['amount'],
					'description' => $data['description']
				));
			}
			
			if($data['to']) {
				$this->MsLoader->MsBalance->addBalanceEntry($data['to'], array(
					'balance_type' => MsBalance::MS_BALANCE_TYPE_GENERIC,
					'amount' => $data['amount'],
					'description' => $data['description']
				));
			}
			
			$this->session->data['success'] = $this->language->get('ms_success_transaction_created');
		} else {
		}
		
		$this->response->setOutput(json_encode($json));
	}
}
?>
