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
        $this->load->model('account/order');

		$orders = $this->MsLoader->MsOrderData->getOrders(
			array(
				'seller_id' => $seller_id,
				'order_status' => $this->config->get('msconf_credit_order_statuses')
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
                $products .= "<p style='text-align:left'>";
				$products .= "<span class='name'>" . ($p['quantity'] > 1 ? "{$p['quantity']} x " : "") . "<a href='" . $this->url->link('product/product', 'product_id=' . $p['product_id'], 'SSL') . "'>{$p['name']}</a></span>";

                $options   = $this->model_account_order->getOrderOptions($order['order_id'], $p['order_product_id']);

                foreach ($options as $option)
                {
                    if ($option['type'] != 'file') {
                        $value = $option['value'];
                    } else {
                        $value = utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.'));
                    }

                    $option['value']	=  utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value;

                    $products .= "<br />";
                    $products .= "<small> - {$option['name']} : {$option['value']} </small>";
                }

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
					'total_amount' => $this->currency->format($order['total_amount'], $this->config->get('config_currency')),
					'view_order' => '<a href="' . $this->url->link('seller/account-order/viewOrder', 'order_id=' . $order['order_id']) . '">Open</a>'
				)
			);
		}
		
		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => $total,
			'iTotalDisplayRecords' => $total,
			'aaData' => $columns
		)));
	}

	public function viewOrder() { 
		$this->language->load('account/order');

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

		$this->data['redirect'] = $this->url->link('seller/account-order/viewOrder', 'order_id=' . $order_id, 'SSL');
		if (!$this->customer->isLogged()) {

			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}

		$this->load->model('localisation/order_status');
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		$this->data['order_status_id'] = $this->model_localisation_order_status->getSuborderStatusId($order_id, $this->customer->getId());
		$suborder_id = $this->model_localisation_order_status->getSuborderId($order_id);

		if (isset($this->request->post['order_status_edit'])) {
			$this->model_localisation_order_status->editSuborderStatus($suborder_id, $this->request->post['order_status_edit']);
			$this->redirect($this->data['redirect']);
		}

		$this->load->model('account/order');

		$order_info = $this->model_account_order->getOrder($order_id, 'seller');

		if ($order_info) {
			$this->document->setTitle($this->language->get('text_order'));

			$this->data['heading_title'] = $this->language->get('text_order');

			$this->data['text_order_detail'] = $this->language->get('text_order_detail');
			// $this->data['text_invoice_no'] = $this->language->get('text_invoice_no');
			$this->data['text_order_id'] = $this->language->get('text_order_id');
			$this->data['text_date_added'] = $this->language->get('text_date_added');
			// $this->data['text_shipping_method'] = $this->language->get('text_shipping_method');
			// $this->data['text_shipping_address'] = $this->language->get('text_shipping_address');
			$this->data['text_payment_method'] = $this->language->get('text_payment_method');
			$this->data['text_payment_address'] = $this->language->get('text_payment_address');
			$this->data['text_history'] = $this->language->get('text_history');
			// $this->data['text_comment'] = $this->language->get('text_comment');

			$this->data['column_name'] = $this->language->get('column_name');
			$this->data['column_model'] = $this->language->get('column_model');
			$this->data['column_quantity'] = $this->language->get('column_quantity');
			$this->data['column_price'] = $this->language->get('column_price');
			$this->data['column_total'] = $this->language->get('column_total');
			// $this->data['column_action'] = $this->language->get('column_action');
			// $this->data['column_date_added'] = $this->language->get('column_date_added');
			$this->data['column_status'] = $this->language->get('column_status');
			$this->data['column_comment'] = $this->language->get('column_comment');

			$this->data['button_return'] = $this->language->get('button_return');
			$this->data['button_continue'] = $this->language->get('button_continue');

			if ($order_info['invoice_no']) {
				$this->data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
			} else {
				$this->data['invoice_no'] = '';
			}

			$this->data['order_id'] = $this->request->get['order_id'];
			$this->data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));

			if ($order_info['payment_address_format']) {
				$format = $order_info['payment_address_format'];
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}

			$find = array(
				'{firstname}',
				'{lastname}',
				'{company}',
				'{address_1}',
				'{address_2}',
				'{city}',
				'{postcode}',
				'{zone}',
				'{zone_code}',
				'{country}'
			);

			$replace = array(
				'firstname' => $order_info['payment_firstname'],
				'lastname'  => $order_info['payment_lastname'],
				'company'   => $order_info['payment_company'],
				'address_1' => $order_info['payment_address_1'],
				'address_2' => $order_info['payment_address_2'],
				'city'      => $order_info['payment_city'],
				'postcode'  => $order_info['payment_postcode'],
				'zone'      => $order_info['payment_zone'],
				'zone_code' => $order_info['payment_zone_code'],
				'country'   => $order_info['payment_country']
			);

			$this->data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

			$this->data['payment_method'] = $order_info['payment_method'];

			if ($order_info['shipping_address_format']) {
				$format = $order_info['shipping_address_format'];
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}

			$find = array(
				'{firstname}',
				'{lastname}',
				'{company}',
				'{address_1}',
				'{address_2}',
				'{city}',
				'{postcode}',
				'{zone}',
				'{zone_code}',
				'{country}'
			);

			$replace = array(
				'firstname' => $order_info['shipping_firstname'],
				'lastname'  => $order_info['shipping_lastname'],
				'company'   => $order_info['shipping_company'],
				'address_1' => $order_info['shipping_address_1'],
				'address_2' => $order_info['shipping_address_2'],
				'city'      => $order_info['shipping_city'],
				'postcode'  => $order_info['shipping_postcode'],
				'zone'      => $order_info['shipping_zone'],
				'zone_code' => $order_info['shipping_zone_code'],
				'country'   => $order_info['shipping_country']
			);

			$this->data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

			$this->data['shipping_method'] = $order_info['shipping_method'];

			$this->data['products'] = array();

			$products = $this->model_account_order->getOrderProducts($this->request->get['order_id'], 'seller');

			foreach ($products as $product) {
				$option_data = array();

				$options = $this->model_account_order->getOrderOptions($this->request->get['order_id'], $product['order_product_id']);

				foreach ($options as $option) {
					if ($option['type'] != 'file') {
						$value = $option['value'];
					} else {
						$value = utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.'));
					}

					$option_data[] = array(
						'name'  => $option['name'],
						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
					);					
				}

				$this->data['products'][] = array(
					'product_id' => $product['product_id'],
					'name'     => $product['name'],
					'model'    => $product['model'],
					'option'   => $option_data,
					'quantity' => $product['quantity'],
					'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
					'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
					'return'   => $this->url->link('account/return/insert', 'order_id=' . $order_info['order_id'] . '&product_id=' . $product['product_id'], 'SSL')
				);
			}

			// Voucher
			$this->data['vouchers'] = array();

			$vouchers = $this->model_account_order->getOrderVouchers($this->request->get['order_id']);

			foreach ($vouchers as $voucher) {
				$this->data['vouchers'][] = array(
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
				);
			}

			$this->data['totals'] = $this->model_account_order->getOrderTotals($this->request->get['order_id']);

			$this->data['comment'] = nl2br($order_info['comment']);

			$this->data['histories'] = array();

			$results = $this->model_account_order->getOrderHistories($this->request->get['order_id']);

			foreach ($results as $result) {
				$this->data['histories'][] = array(
					'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
					'status'     => $result['status'],
					'comment'    => nl2br($result['comment'])
				);
			}

			$this->data['continue'] = $this->url->link('account/order', '', 'SSL');


			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/multiseller/account-order-info.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/multiseller/account-order-info.tpl';
			} else {
				$this->template = 'default/template/multiseller/account-order-info.tpl';
			}

			$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'	
			);

			$this->response->setOutput($this->render());		
		} else {
			$this->document->setTitle($this->language->get('text_order'));

			$this->data['heading_title'] = $this->language->get('text_order');

			$this->data['text_error'] = $this->language->get('text_error');

			$this->data['button_continue'] = $this->language->get('button_continue');

			$this->data['continue'] = $this->url->link('account/order', '', 'SSL');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . '/1.1 404 Not Found');

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/error/not_found.tpl';
			} else {
				$this->template = 'default/template/error/not_found.tpl';
			}

			$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'	
			);

			$this->response->setOutput($this->render());				
		}
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
