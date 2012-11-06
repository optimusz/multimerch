<?php

class ControllerSellerAccountTransaction extends ControllerSellerAccount {
	public function index() {
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;

		$sort = array(
			'order_by'  => 'date_created',
			'order_way' => 'DESC',
			'page' => $page,
			'limit' => 5
		);

		$seller_id = $this->customer->getId();
		
		$balance_entries = $this->MsLoader->MsBalance->getSellerBalanceEntries($seller_id, $sort);
		
    	foreach ($balance_entries as $entry) {
    		$this->data['transactions'][] = array(
    			'description' => $entry['description'],
    			'amount' => $this->currency->format($entry['amount'], $this->config->get('config_currency')),
   				'date_created' => date($this->language->get('date_format_short'), strtotime($entry['date_created']))
   			);
   		}

		$this->data['balance'] =  $this->currency->format($this->MsLoader->MsBalance->getSellerBalance($seller_id),$this->config->get('config_currency'));
		$pagination = new Pagination();
		$pagination->total = $this->MsLoader->MsBalance->getTotalSellerBalanceEntries($seller_id);
		$pagination->page = $sort['page'];
		$pagination->limit = $sort['limit']; 
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('seller/account-transaction', 'page={page}', 'SSL');
		
		$this->data['pagination'] = $pagination->render();
		$this->data['link_back'] = $this->url->link('account/account', '', 'SSL');
		
		$this->document->setTitle($this->language->get('ms_account_transactions_heading'));
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_transactions_breadcrumbs'),
				'href' => $this->url->link('seller/account-transaction', '', 'SSL'),
			)
		));
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->loadTemplate('account-transaction');
		$this->response->setOutput($this->render());
	}
}

?>
