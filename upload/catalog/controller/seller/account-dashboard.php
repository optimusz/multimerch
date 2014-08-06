<?php

class ControllerSellerAccountDashboard extends ControllerSellerAccount {
	public function index() {
		// paypal listing payment confirmation
		if (isset($this->request->post['payment_status']) && strtolower($this->request->post['payment_status']) == 'completed') {
			$this->data['success'] = $this->language->get('ms_account_sellerinfo_saved');
		}
		
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
        $this->load->model('account/order');
		
		$seller_id = $this->customer->getId();
		
		$seller = $this->MsLoader->MsSeller->getSeller($seller_id);
		$seller_group_names = $this->MsLoader->MsSellerGroup->getSellerGroupDescriptions($seller['ms.seller_group']);
		$my_first_day = date('Y-m-d H:i:s', mktime(0, 0, 0, date("n"), 1));
		
		$this->data['seller'] = array_merge(
			$seller,
			array('balance' => $this->currency->format($this->MsLoader->MsBalance->getSellerBalance($seller_id), $this->config->get('config_currency'))),
			array('commission_rates' => $this->MsLoader->MsCommission->calculateCommission(array('seller_id' => $seller_id))),
			array('total_earnings' => $this->currency->format($this->MsLoader->MsSeller->getTotalEarnings($seller_id), $this->config->get('config_currency'))),
			array('earnings_month' => $this->currency->format($this->MsLoader->MsSeller->getTotalEarnings($seller_id, array('period_start' => $my_first_day)), $this->config->get('config_currency'))),
			array('sales_month' => $this->MsLoader->MsOrderData->getTotalSales(array(
				'seller_id' => $seller_id,
				'period_start' => $my_first_day
			))),
			array('seller_group' => $seller_group_names[$this->config->get('config_language_id')]['name']),
			array('date_created' => date($this->language->get('date_format_short'), strtotime($seller['ms.date_created'])))
			//array('total_products' => $this->MsLoader->MsProduct->getTotalProducts(array(
				//'seller_id' => $seller_id,
				//'enabled' => ))
		);
		
		if ($seller['ms.avatar'] && file_exists(DIR_IMAGE . $seller['ms.avatar'])) {
			$this->data['seller']['avatar'] = $this->MsLoader->MsFile->resizeImage($seller['ms.avatar'], $this->config->get('msconf_seller_avatar_dashboard_image_width'), $this->config->get('msconf_seller_avatar_dashboard_image_height'));
		} else {
			$this->data['seller']['avatar'] = $this->MsLoader->MsFile->resizeImage('ms_no_image.jpg', $this->config->get('msconf_seller_avatar_dashboard_image_width'), $this->config->get('msconf_seller_avatar_dashboard_image_height'));
		}		
		
		$payments = $this->MsLoader->MsPayment->getPayments(
			array(
				'seller_id' => $seller_id,
			),
			array(
				'order_by'  => 'mpay.date_created',
				'order_way' => 'DESC',
				'offset' => 0,
				'limit' => 5
			)
		);
		 
		$orders = $this->MsLoader->MsOrderData->getOrders(
			array(
				'seller_id' => $seller_id,
				'order_status' => $this->config->get('msconf_credit_order_statuses')
			),
			array(
				'order_by'  => 'date_added',
				'order_way' => 'DESC',
				'offset' => 0,
				'limit' => 5
			)
		);		
		
    	foreach ($orders as $order) {

            $products	=	$this->MsLoader->MsOrderData->getOrderProducts(array('order_id' => $order['order_id'], 'seller_id' => $seller_id));

            foreach($products as $key=>$p)
                $products[$key]['options']	=  $this->model_account_order->getOrderOptions($order['order_id'], $p['order_product_id']);

    		$this->data['orders'][] = array(
    			'order_id' => $order['order_id'],
    			'customer' => "{$order['firstname']} {$order['lastname']} ({$order['email']})",
    			'products' => $products,
    			'date_created' => date($this->language->get('date_format_short'), strtotime($order['date_added'])),
   				'total' => $this->currency->format($this->MsLoader->MsOrderData->getOrderTotal($order['order_id'], array('seller_id' => $seller_id)), $this->config->get('config_currency'))
   			);
   		}
		
		$this->data['link_back'] = $this->url->link('account/account', '', 'SSL');
		
		$this->document->setTitle($this->language->get('ms_account_dashboard_heading'));
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_dashboard_breadcrumbs'),
				'href' => $this->url->link('seller/account-dashboard', '', 'SSL'),
			)
		));
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->loadTemplate('account-dashboard');
		$this->response->setOutput($this->render());
	}
}

?>
