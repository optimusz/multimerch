<?php

class ControllerMultisellerSeller extends ControllerMultisellerBase {
	public function jxSaveSellerInfo() {
		$this->validate(__FUNCTION__);
		$data = $this->request->post;
		$seller = $this->MsLoader->MsSeller->getSeller($data['seller']['seller_id']);
		$json = array();
		$this->load->model('sale/customer');
		
		if (empty($data['seller']['seller_id'])) {
			// creating new seller
			if (empty($data['seller']['nickname'])) {
				$json['errors']['seller[nickname]'] = 'Username cannot be empty'; 
			} else if (!ctype_alnum($data['seller']['nickname'])) {
				$json['errors']['seller[nickname]'] = 'Username can only contain alphanumeric characters';
			} else if (strlen($data['seller']['nickname']) < 4 || strlen($data['seller']['nickname']) > 50 ) {
				$json['errors']['seller[nickname]'] = 'Username should be between 4 and 50 characters';			
			} else if ($this->MsLoader->MsSeller->nicknameTaken($data['seller']['nickname'])) {
				$json['errors']['seller[nickname]'] = 'This username is already taken';
			}
			
			if (empty($data['customer']['customer_id'])) {
				// creating new customer
				$this->language->load('sale/customer');

		    	if ((mb_strlen($data['customer']['firstname']) < 1) || (mb_strlen($data['customer']['firstname']) > 32)) {
		      		$json['errors']['customer[firstname]'] = $this->language->get('error_firstname');
		    	}
		
		    	if ((mb_strlen($data['customer']['lastname']) < 1) || (mb_strlen($data['customer']['lastname']) > 32)) {
		      		$json['errors']['customer[lastname]'] = $this->language->get('error_lastname');
		    	}
		
				if ((mb_strlen($data['customer']['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $data['customer']['email'])) {
		      		$json['errors']['customer[email]'] = $this->language->get('error_email');
		    	}
				
				$customer_info = $this->model_sale_customer->getCustomerByEmail($data['customer']['email']);
				
				if (!isset($this->request->get['customer_id'])) {
					if ($customer_info) {
						$json['errors']['customer[email]'] = $this->language->get('error_exists');
					}
				} else {
					if ($customer_info && ($this->request->get['customer_id'] != $customer_info['customer_id'])) {
						$json['errors']['customer[email]'] = $this->language->get('error_exists');
					}
				}
				
		    	if ($data['customer']['password'] || (!isset($this->request->get['customer_id']))) {
		      		if ((mb_strlen($data['customer']['password']) < 4) || (mb_strlen($data['customer']['password']) > 20)) {
		        		$json['errors']['customer[password]'] = $this->language->get('error_password');
		      		}
			
			  		if ($data['customer']['password'] != $data['customer']['password_confirm']) {
			    		$json['errors']['customer[password_confirm]'] = $this->language->get('error_confirm');
			  		}
		    	}				
			}
		}
		
		if (strlen($data['seller']['company']) > 50 ) {
			$json['errors']['seller[company]'] = 'Company name cannot be longer than 50 characters';
		}
		if (empty($json['errors'])) {
			$mails = array();
			if (empty($data['seller']['seller_id'])) {
				// creating new seller
				if (empty($data['customer']['customer_id'])) {
					// creating new customer
					$this->model_sale_customer->addCustomer(
						array_merge(
							$data['customer'],
							array(
								'telephone' => '',
								'fax' => '',
								'customer_group_id' => $this->config->get('config_customer_group_id'),
								'newsletter' => 1,
								'status' => 1
							)
						)
					);
					
					$customer_info = $this->model_sale_customer->getCustomerByEmail($data['customer']['email']);
					$data['seller']['seller_id'] = $customer_info['customer_id'];
				} else {
					$data['seller']['seller_id'] = $data['customer']['customer_id'];
				}

				$this->MsLoader->MsSeller->createSeller(
					array_merge(
						$data['seller'],
						array(
							'approved' => 1,
						)
					)
				);
			} else {
				// edit seller
				$mails[] = array(
					'type' => MsMail::SMT_SELLER_ACCOUNT_MODIFIED,
					'data' => array(
						'recipients' => $seller['c.email'],
						'addressee' => $seller['ms.nickname'],
						'message' => (isset($data['seller']['message']) ? $data['seller']['message'] : ''),
						'seller_id' => $seller['seller_id']
					)
				);
	
				switch ($data['seller']['status']) {
					case MsSeller::STATUS_INACTIVE:
					case MsSeller::STATUS_DISABLED:
					case MsSeller::STATUS_DELETED:
						$products = $this->MsLoader->MsProduct->getProducts(array(
							'seller_id' => $seller['seller_id']
						));
						
						foreach ($products as $p) {
							$this->MsLoader->MsProduct->changeStatus($p['product_id'], $data['seller']['status']);
						}
						
						$data['seller']['approved'] = 0;
						break;
					case MsSeller::STATUS_ACTIVE:
						$data['seller']['approved'] = 1;
						break;
				}
							
				$this->MsLoader->MsSeller->adminEditSeller(
					array_merge(
						$data['seller'],
						array(
							'approved' => 1,
						)
					)				
				);
			}
			
			if ($data['seller']['notify']) {
				$this->MsLoader->MsMail->sendMails($mails);
			}
			
			$this->session->data['success'] = 'Seller account data saved.';
		}
		
		$this->response->setOutput(json_encode($json));
	}	
	
	public function jxPayBalance() {
		$json = array();
		$seller_id = isset($this->request->get['seller_id']) ? $this->request->get['seller_id'] : 0;
		$seller = $this->MsLoader->MsSeller->getSeller($seller_id);
		
		if (!$seller) return;
		
		$amount = $this->MsLoader->MsBalance->getSellerBalance($seller_id) - $this->MsLoader->MsBalance->getReservedSellerFunds($seller_id);
		
		if (!$amount) return;

		//create payment
		$payment_id = $this->MsLoader->MsPayment->createPayment(array(
			'seller_id' => $seller_id,
			'payment_type' => MsPayment::TYPE_PAYOUT,
			'payment_status' => MsPayment::STATUS_UNPAID,
			'payment_method' => MsPayment::METHOD_PAYPAL,
			'amount' => $amount,
			'currency_id' => $this->currency->getId($this->config->get('config_currency')),
			'currency_code' => $this->currency->getCode($this->config->get('config_currency')),
			'description' => sprintf($this->language->get('ms_payment_royalty_payout'), $seller['name'], $this->config->get('config_name'))
		));
		
		// render paypal form
		$this->data['payment_data'] = array(
			'sandbox' => $this->config->get('msconf_paypal_sandbox'),
			'action' => $this->config->get('msconf_paypal_sandbox') ? "https://www.sandbox.paypal.com/cgi-bin/webscr" : "https://www.paypal.com/cgi-bin/webscr",
			'business' => $this->config->get('msconf_paypal_address'),
			'item_name' => sprintf($this->language->get('ms_payment_royalty_payout'), $seller['name'], $this->config->get('config_name')),
			'amount' => $amount,
			'currency_code' => $this->config->get('config_currency'),
			'return' => $this->url->link('multiseller/seller', 'token=' . $this->session->data['token']),
			'cancel_return' => $this->url->link('multiseller/seller', 'token=' . $this->session->data['token']),
			'notify_url' => HTTP_CATALOG . 'index.php?route=payment/multimerch-paypal/paymentIPN',
			'custom' => $payment_id
		);
		
		list($this->template) = $this->MsLoader->MsHelper->admLoadTemplate('payment/multimerch-paypal');
		
		$json['form'] = $this->render();
		$json['success'] = 1;
		$this->response->setOutput(json_encode($json));
	}
	
	public function delete() {
		$seller_id = isset($this->request->get['seller_id']) ? $this->request->get['seller_id'] : 0;
		$this->MsLoader->MsSeller->deleteSeller($seller_id);
		$this->redirect($this->url->link('multiseller/seller', 'token=' . $this->session->data['token'], 'SSL'));
	}
	
	public function index() {
		$this->validate(__FUNCTION__);
		
		/*
		$columns = array(
			'name',
			'nickname',
			'email',
			'total_products',
			'total_sales',
			'total_earnings',	
			'current_balance',
			'seller_status',
			'date_created',
		);
		*/
		
		$this->data['total_balance'] = sprintf($this->language->get('ms_catalog_sellers_total_balance'), $this->currency->format($this->MsLoader->MsBalance->getTotalBalanceAmount(), $this->config->get('config_currency')), $this->currency->format($this->MsLoader->MsBalance->getTotalBalanceAmount(array('seller_status' => array(MsSeller::STATUS_ACTIVE))), $this->config->get('config_currency')));
		
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
		
		$orderby = isset($this->request->get['orderby']) && in_array($this->request->get['orderby'], $columns) ? $this->request->get['orderby'] : 'date_created';
		
		$orderway = isset($this->request->get['orderway']) ? $this->request->get['orderway'] : 'DESC';
		
		$results = $this->MsLoader->MsSeller->getSellers(
			array(),
			array(
				'order_by'  => $orderby,
				'order_way' => $orderway,
				'offset' => ($page - 1) * $this->config->get('config_admin_limit'),
				'limit' => $this->config->get('config_admin_limit')
			)
		);
			
		$total_sellers = $this->MsLoader->MsSeller->getTotalSellers();

    	foreach ($results as &$result) {
    		$result['date_created'] = date($this->language->get('date_format_short'), strtotime($result['ms.date_created']));
    		$result['total_products'] = $this->MsLoader->MsProduct->getTotalProducts(array(
				'seller_id' => $result['seller_id'],
			));
			
			//$result['total_earnings'] = $this->currency->format($this->MsLoader->MsSeller->getEarningsForSeller($result['seller_id']), $this->config->get('config_currency'));
			$result['current_balance'] = $this->currency->format($this->MsLoader->MsBalance->getSellerBalance($result['seller_id']), $this->config->get('config_currency'));
			$result['earnings'] = $this->currency->format($this->MsLoader->MsSeller->getTotalEarnings($result['seller_id']), $this->config->get('config_currency'));
			$result['total_sales'] = $this->MsLoader->MsSeller->getSalesForSeller($result['seller_id']);
			$result['status'] = $this->MsLoader->MsSeller->getStatusText($result['ms.seller_status']);
			$result['customer_link'] = $this->url->link('sale/customer/update', 'token=' . $this->session->data['token'] . '&customer_id=' . $result['seller_id'], 'SSL');
		}
			
		$this->data['sellers'] = $results;
			
		$pagination = new Pagination();
		$pagination->total = $total_sellers;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link("multiseller/seller", 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');
			
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

		/*
		foreach($columns as $column) {
			$this->data["link_sort_$column"] = $this->url->link("multiseller/sellers", 'token=' . $this->session->data['token'] . "&orderby=$column" . $url, 'SSL');
		}
		*/
		$this->data['token'] = $this->session->data['token'];		
		$this->data['heading'] = $this->language->get('ms_catalog_sellers_heading');
		$this->data['link_create_seller'] = $this->url->link('multiseller/seller/create', 'token=' . $this->session->data['token'], 'SSL');
		$this->document->setTitle($this->language->get('ms_catalog_sellers_heading'));
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multiseller/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_catalog_sellers_breadcrumbs'),
				'href' => $this->url->link('multiseller/seller', '', 'SSL'),
			)
		));
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->admLoadTemplate('seller');
		$this->response->setOutput($this->render());
	}
	
	public function create() {
		$this->validate(__FUNCTION__);
		$this->load->model('localisation/country');
    	$this->data['countries'] = $this->model_localisation_country->getCountries();
		$this->data['customers'] = $this->MsLoader->MsSeller->getCustomers(array('seller_id' => 'NULL'));
		$this->data['seller_statuses'] =$this->MsLoader->MsSeller->getStatuses();
		$this->data['seller_groups'] =$this->MsLoader->MsSellerGroup->getSellerGroups();  
		$this->data['seller'] = FALSE;

		$this->data['currency_code'] = $this->config->get('config_currency');
		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_catalog_sellerinfo_heading');
		$this->document->setTitle($this->language->get('ms_catalog_sellerinfo_heading'));
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multiseller/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_catalog_sellers_breadcrumbs'),
				'href' => $this->url->link('multiseller/seller', '', 'SSL'),
			),			
			array(
				'text' => $this->language->get('ms_catalog_sellers_newseller'),
				'href' => $this->url->link('multiseller/seller/create', 'SSL'),
			)
		));		
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->admLoadTemplate('seller-form');
		$this->response->setOutput($this->render());
	}	
	
	public function update() {
		$this->validate(__FUNCTION__);
		
		$this->load->model('localisation/country');
    	$this->data['countries'] = $this->model_localisation_country->getCountries();		

		$seller = $this->MsLoader->MsSeller->getSeller($this->request->get['seller_id']);

		$this->data['seller_statuses'] =$this->MsLoader->MsSeller->getStatuses();
		$this->data['seller_groups'] =$this->MsLoader->MsSellerGroup->getSellerGroups();  

		if (!empty($seller)) {
			$this->data['seller'] = $seller;
			if (!empty($seller['ms.avatar'])) {
				$this->data['seller']['avatar']['name'] = $seller['ms.avatar'];
				$this->data['seller']['avatar']['thumb'] = $this->MsLoader->MsFile->resizeImage($seller['ms.avatar'], $this->config->get('msconf_image_preview_width'), $this->config->get('msconf_image_preview_height'));
				//$this->session->data['multiseller']['files'][] = $seller['avatar'];
			}
			
			if (is_null($seller['ms.commission_id']))
				$rates = NULL;
			else
				$rates = $this->MsLoader->MsCommission->getCommissionRates($seller['ms.commission_id']);
			
			$this->data['seller']['commission_id'] = $seller['ms.commission_id'];	
			$this->data['seller']['commission_rates'] = $rates;
		}

		$this->data['currency_code'] = $this->config->get('config_currency');
		$this->data['token'] = $this->session->data['token'];		
		$this->data['heading'] = $this->language->get('ms_catalog_sellerinfo_heading');
		$this->document->setTitle($this->language->get('ms_catalog_sellerinfo_heading'));
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multiseller/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_catalog_sellers_breadcrumbs'),
				'href' => $this->url->link('multiseller/seller', '', 'SSL'),
			),			
			array(
				'text' => $seller['ms.nickname'],
				'href' => $this->url->link('multiseller/seller/update', '&seller_id=' . $seller['seller_id'], 'SSL'),
			)
		));		
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->admLoadTemplate('seller-form');
		$this->response->setOutput($this->render());
	}
}
?>
