<?php

class ControllerSellerAccountOrder extends ControllerSellerAccount {
	public function getTableData() {
		$colMap = array(
			'customer_name' => 'firstname',
			'date_created' => 'o.date_added',
		);
		
		$sorts = array('order_id', 'customer_name', 'date_created', 'total_amount');
		$filters = array_merge($sorts, array('products'));
		
		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);
		
		$seller_id = $this->customer->getId();
		$orders = $this->MsLoader->MsOrderData->getOrders(
			array(
				'seller_id' => $seller_id,
			),
			array(
				'order_by'  => $sortCol,
				'order_way' => $sortDir,
				'offset' => $this->request->get['iDisplayStart'],
				'limit' => $this->request->get['iDisplayLength'],
				'filters' => $filterParams
			),
			array(
				'total_amount' => 1,
				'products' => 1,
			)
		);
		
		$total = isset($orders[0]) ? $orders[0]['total_rows'] : 0;

		$columns = array();
		foreach ($orders as $order) {
			$order_products = $this->MsLoader->MsOrderData->getOrderProducts(array('order_id' => $order['order_id'], 'seller_id' => $seller_id));
			
			if ($this->config->get('msconf_hide_customer_email')) {
				$customer_name = "{$order['firstname']} {$order['lastname']}";
			} else {
				$customer_name = "{$order['firstname']} {$order['lastname']} ({$order['email']})";
			}
			
			$products = "";
			foreach ($order_products as $p) {
				$products .= "<p>";
					$products .= "<span class='name'>" . ($p['quantity'] > 1 ? "{$p['quantity']} x " : "") . "<a href='" . $this->url->link('product/product', 'product_id=' . $p['product_id'], 'SSL') . "'>{$p['name']}</a></span>";
					$products .= "<span class='total'>" . $this->currency->format($p['seller_net_amt'], $this->config->get('config_currency')) . "</span>";
				$products .= "</p>";
			}
			
			$columns[] = array_merge(
				$order,
				array(
					'order_id' => $order['order_id'],
					'customer_name' => $customer_name,
					'products' => $products,
					'date_created' => date($this->language->get('date_format_short'), strtotime($order['date_added'])),
					'total_amount' => $this->currency->format($order['total_amount'], $this->config->get('config_currency'))
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
