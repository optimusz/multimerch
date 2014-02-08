<?php

class ControllerMultisellerSeller extends ControllerMultisellerBase {
	public function getTableData() {
		$colMap = array(
			'seller' => '`c.name`',
			'email' => 'c.email',
			'balance' => '`current_balance`',
			'date_created' => '`ms.date_created`',
			'status' => '`ms.seller_status`'
		);

		$sorts = array('seller', 'email', 'total_sales', 'total_products', 'total_earnings', 'date_created', 'balance', 'status', 'date_created');
		$filters = array_diff($sorts, array('status'));
		
		//var_dump($this->request->get);
		
		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsSeller->getSellers(
			array(),
			array(
				'order_by'  => $sortCol,
				'order_way' => $sortDir,
				'filters' => $filterParams,
				'offset' => $this->request->get['iDisplayStart'],
				'limit' => $this->request->get['iDisplayLength']
			),
			array(
				'total_products' => 1,
				'total_earnings' => 1,
				'current_balance' => 1
			)
		);

		$total = isset($results[0]) ? $results[0]['total_rows'] : 0;

		$columns = array();
		foreach ($results as $result) {
			// actions
			$actions = "";
			if ($this->MsLoader->MsBalance->getSellerBalance($result['seller_id']) - $this->MsLoader->MsBalance->getReservedSellerFunds($result['seller_id']) > 0) {
				if (!empty($result['ms.paypal']) && filter_var($result['ms.paypal'], FILTER_VALIDATE_EMAIL)) {
					$actions .= "<a class='ms-button ms-button-paypal' title='" . $this->language->get('ms_catalog_sellers_balance_paypal') . "'></a>";
				} else {
					$actions .= "<a class='ms-button ms-button-paypal-bw' title='".$this->language->get('ms_catalog_sellers_balance_invalid') . "'></a>";
				}
			}
			$actions .= "<a class='ms-button ms-button-edit' href='" . $this->url->link('multiseller/seller/update', 'token=' . $this->session->data['token'] . '&seller_id=' . $result['seller_id'], 'SSL') . "' title='".$this->language->get('text_edit')."'></a>";
			$actions .= "<a class='ms-button ms-button-delete' href='" . $this->url->link('multiseller/seller/delete', 'token=' . $this->session->data['token'] . '&seller_id=' . $result['seller_id'], 'SSL') . "' title='".$this->language->get('text_delete')."'></a>";

			$available = $this->MsLoader->MsBalance->getSellerBalance($result['seller_id']) - $this->MsLoader->MsBalance->getReservedSellerFunds($result['seller_id']);
			
			// build table data
			$columns[] = array_merge(
				$result,
				array(
					'seller' => "<input type='hidden' value='{$result['seller_id']}' /><a href='".$this->url->link('sale/customer/update', 'token=' . $this->session->data['token'] . '&customer_id=' . $result['seller_id'], 'SSL')."'>{$result['c.name']}({$result['ms.nickname']})</a>",
					'email' => $result['c.email'],
					'total_earnings' => $this->currency->format($this->MsLoader->MsSeller->getTotalEarnings($result['seller_id']), $this->config->get('config_currency')),
					'balance' => $this->currency->format($this->MsLoader->MsBalance->getSellerBalance($result['seller_id']), $this->config->get('config_currency')) . '/' . $this->currency->format($available > 0 ? $available : 0, $this->config->get('config_currency')),
					'status' => $this->language->get('ms_seller_status_' . $result['ms.seller_status']),
					'date_created' => date($this->language->get('date_format_short'), strtotime($result['ms.date_created'])),
					'actions' => $actions
				)
			);
		}

		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => $total,
  			'iTotalDisplayRecords' => $total, //count($results),
			'aaData' => $columns
		)));
	}
	
	public function jxSaveSellerInfo() {
		$this->validate(__FUNCTION__);
		$data = $this->request->post;
		$seller = $this->MsLoader->MsSeller->getSeller($data['seller']['seller_id']);
		$json = array();
		$this->load->model('sale/customer');
		
		if (empty($data['seller']['seller_id'])) {
			// creating new seller
			if (empty($data['seller']['nickname'])) {
				$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_empty'); 
			} else if (mb_strlen($data['seller']['nickname']) < 4 || mb_strlen($data['seller']['nickname']) > 128 ) {
				$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_length');			
			} else if ($this->MsLoader->MsSeller->nicknameTaken($data['seller']['nickname'])) {
				$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_taken');
			} else {
				switch($this->config->get('msconf_nickname_rules')) {
					case 1:
						// extended latin
						if(!preg_match("/^[a-zA-Z0-9_\-\s\x{00C0}-\x{017F}]+$/u", $data['seller']['nickname'])) {
							$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_latin');
						}
						break;
						
					case 2:
						// utf8
						if(!preg_match("/((?:[\x01-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3}){1,100})./x", $data['seller']['nickname'])) {
							$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_utf8');
						}
						break;
						
					case 0:
					default:
						// alnum
						if(!preg_match("/^[a-zA-Z0-9_\-\s]+$/", $data['seller']['nickname'])) {
							$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_alphanumeric');
						}
						break;
				}
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
								'status' => 1,
							)
						)
					);
					
					$customer_info = $this->model_sale_customer->getCustomerByEmail($data['customer']['email']);
					$this->db->query("UPDATE " . DB_PREFIX . "customer SET approved = '1' WHERE customer_id = '" . (int)$customer_info['customer_id'] . "'");
					
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
	// echo '<pre>'; print_r($seller); echo '</pre>'; die();
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
						if ($seller['ms.seller_status'] == MsSeller::STATUS_INACTIVE && $this->config->get('msconf_allow_inactive_seller_products')) {
							$products = $this->MsLoader->MsProduct->getProducts(array(
								'seller_id' => $seller['seller_id']
							));
							
							foreach ($products as $p) {
								$this->MsLoader->MsProduct->changeStatus($p['product_id'], $data['seller']['status']);
								if ($this->config->get('msconf_product_validation') == MsProduct::MS_PRODUCT_VALIDATION_NONE) {
									$this->MsLoader->MsProduct->approve($p['product_id']);
								}
							}
						}
						
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
	
	
	// simple paypal balance payout
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
			'payment_data' => $seller['ms.paypal'],
			'payment_method' => MsPayment::METHOD_PAYPAL,
			'amount' => $this->currency->format($amount, $this->config->get('config_currency'), '', FALSE),
			'currency_id' => $this->currency->getId($this->config->get('config_currency')),
			'currency_code' => $this->currency->getCode($this->config->get('config_currency')),
			'description' => sprintf($this->language->get('ms_payment_royalty_payout'), $seller['name'], $this->config->get('config_name'))
		));
		
		// render paypal form
		$this->data['payment_data'] = array(
			'sandbox' => $this->config->get('msconf_paypal_sandbox'),
			'action' => $this->config->get('msconf_paypal_sandbox') ? "https://www.sandbox.paypal.com/cgi-bin/webscr" : "https://www.paypal.com/cgi-bin/webscr",
			'business' => $seller['ms.paypal'],
			'item_name' => sprintf($this->language->get('ms_payment_royalty_payout'), $seller['name'], $this->config->get('config_name')),
			'amount' => $this->currency->format($amount, $this->config->get('config_currency'), '', FALSE),
			'currency_code' => $this->config->get('config_currency'),
			'return' => $this->url->link('multiseller/seller', 'token=' . $this->session->data['token']),
			'cancel_return' => $this->url->link('multiseller/seller', 'token=' . $this->session->data['token']),
			'notify_url' => HTTP_CATALOG . 'index.php?route=payment/multimerch-paypal/payoutIPN',
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

		// paypal listing payment confirmation
		if (isset($this->request->post['payment_status']) && strtolower($this->request->post['payment_status']) == 'completed') {
			$this->data['success'] = $this->language->get('ms_payment_completed');
		}

		$this->data['total_balance'] = sprintf($this->language->get('ms_catalog_sellers_total_balance'), $this->currency->format($this->MsLoader->MsBalance->getTotalBalanceAmount(), $this->config->get('config_currency')), $this->currency->format($this->MsLoader->MsBalance->getTotalBalanceAmount(array('seller_status' => array(MsSeller::STATUS_ACTIVE))), $this->config->get('config_currency')));
		
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
		$this->load->model('tool/image');
    	$this->data['countries'] = $this->model_localisation_country->getCountries();
		$this->data['customers'] = $this->MsLoader->MsSeller->getCustomers(array('seller_id' => 'NULL'));
		$this->data['seller_groups'] =$this->MsLoader->MsSellerGroup->getSellerGroups();  
		$this->data['seller'] = FALSE;

		$this->data['currency_code'] = $this->config->get('config_currency');
		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_catalog_sellerinfo_heading');
		$this->document->setTitle($this->language->get('ms_catalog_sellerinfo_heading'));
		
		// badges
		$badges = $this->MsLoader->MsBadge->getBadges();
		foreach($badges as &$badge) {
			$badge['image'] = $this->model_tool_image->resize($badge['image'], 30, 30);
		}
		$this->data['badges'] = $badges;
		
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
		$this->load->model('tool/image');
		$this->data['countries'] = $this->model_localisation_country->getCountries();		

		$seller = $this->MsLoader->MsSeller->getSeller($this->request->get['seller_id']);

		$this->data['seller_groups'] =$this->MsLoader->MsSellerGroup->getSellerGroups();  

		if (!empty($seller)) {
			$rates = $this->MsLoader->MsCommission->calculateCommission(array('seller_id' => $this->request->get['seller_id']));
			$actual_fees = '';
			foreach ($rates as $rate) {
				if ($rate['rate_type'] == MsCommission::RATE_SIGNUP) continue;
				$actual_fees .= '<span class="fee-rate-' . $rate['rate_type'] . '"><b>' . $this->language->get('ms_commission_short_' . $rate['rate_type']) . ':</b>' . $rate['percent'] . '%+' . $this->currency->getSymbolLeft() .  $this->currency->format($rate['flat'], $this->config->get('config_currency'), '', FALSE) . $this->currency->getSymbolRight() . '&nbsp;&nbsp;';
			}
			
			$this->data['seller'] = $seller;
			$this->data['seller']['actual_fees'] = $actual_fees;
			
			if (!empty($seller['ms.avatar'])) {
				$this->data['seller']['avatar']['name'] = $seller['ms.avatar'];
				$this->data['seller']['avatar']['thumb'] = $this->MsLoader->MsFile->resizeImage($seller['ms.avatar'], $this->config->get('msconf_preview_seller_avatar_image_width'), $this->config->get('msconf_preview_seller_avatar_image_height'));
				//$this->session->data['multiseller']['files'][] = $seller['avatar'];
			}
			
			if (is_null($seller['ms.commission_id']))
				$rates = NULL;
			else
				$rates = $this->MsLoader->MsCommission->getCommissionRates($seller['ms.commission_id']);
			
			$this->data['seller']['commission_id'] = $seller['ms.commission_id'];	
			$this->data['seller']['commission_rates'] = $rates;
			
			// badges
			$badges = $this->MsLoader->MsBadge->getBadges();
			foreach($badges as &$badge) {
				$badge['image'] = $this->model_tool_image->resize($badge['image'], 30, 30);
			}
			$this->data['badges'] = $badges;

			$seller_badges = $this->MsLoader->MsBadge->getSellerGroupBadges(array('seller_id' => $seller['seller_id']));
			$this->data['seller']['badges'] = array();
			foreach($seller_badges as $b) {
				$this->data['seller']['badges'][] = $b['badge_id'];
			}			
			$this->data['seller']['badges'] = $this->data['seller']['badges'];
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
