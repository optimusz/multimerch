<?php

class ControllerSellerAccountOrder extends ControllerSellerAccount {
	public function index() {
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;

		$sort = array(
			'order_by'  => 'date_added',
			'order_way' => 'DESC',
			'offset' => ($page - 1) * 5,
			'limit' => 5
		);

		$seller_id = $this->customer->getId();
		
		$orders = $this->MsLoader->MsOrderData->getOrders(
			array(
				'seller_id' => $seller_id,
			),
			$sort
		);
		
    	foreach ($orders as $order) {
    		$this->data['orders'][] = array(
    			'order_id' => $order['order_id'],
    			'customer' => "{$order['firstname']} {$order['lastname']} ({$order['email']})",
    			'products' => $this->MsLoader->MsOrderData->getOrderProducts(array('order_id' => $order['order_id'], 'seller_id' => $seller_id)),
    			'date_created' => date($this->language->get('date_format_short'), strtotime($order['date_added'])),
   				'total' => $this->currency->format($this->MsLoader->MsOrderData->getOrderTotal($order['order_id'], array('seller_id' => $seller_id)), $this->config->get('config_currency'))
   			);
   		}
		
		$pagination = new Pagination();
		$pagination->total = $this->MsLoader->MsOrderData->getTotalOrders(array('seller_id' => $seller_id));
		$pagination->page = $page;
		$pagination->limit = $sort['limit']; 
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('seller/account-order', 'page={page}', 'SSL');
		
		$this->data['pagination'] = $pagination->render();
		$this->data['link_back'] = $this->url->link('account/account', '', 'SSL');
		
		$this->document->setTitle($this->language->get('ms_account_orders_heading'));
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_dashboard_breadcrumbs'),
				'href' => $this->url->link('seller/account-dashboard', '', 'SSL'),
			),			
			array(
				'text' => $this->language->get('ms_account_orders_breadcrumbs'),
				'href' => $this->url->link('seller/account-order', '', 'SSL'),
			)
		));
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->loadTemplate('account-order');
		$this->response->setOutput($this->render());
	}
}

?>
