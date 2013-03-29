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
				'description' => $result['mb.description'],
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

		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_finances_transactions_heading');
		$this->document->setTitle($this->language->get('ms_finances_transactions_heading'));
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multiseller/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_finances_transactions_breadcrumbs'),
				'href' => $this->url->link('multiseller/transaction', '', 'SSL'),
			)
		));
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->admLoadTemplate('transaction');
		$this->response->setOutput($this->render());
	}
}
?>
