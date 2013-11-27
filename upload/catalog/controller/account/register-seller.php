<?php 
class ControllerAccountRegisterSeller extends Controller {
	
	/**********************
	 * Buyer account part *
	 **********************/

	private $error = array();
	      
  	public function index() {
		// ***** Buyer account part *****
		if ($this->customer->isLogged()) {
	  		$this->redirect($this->url->link('account/account', '', 'SSL'));
    	}
		
    	$this->language->load('account/register');
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'));
		
		$this->document->setTitle($this->language->get('ms_account_register_seller'));
		$this->document->addScript('catalog/view/javascript/jquery/colorbox/jquery.colorbox-min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/colorbox/colorbox.css');
		
		$this->load->model('account/customer');
		
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			
			// Buyer account part
			
			$this->model_account_customer->addCustomer($this->request->post);

			$this->customer->login($this->request->post['email'], $this->request->post['password']);
			
			unset($this->session->data['guest']);
			
			// Default Shipping Address
			if ($this->config->get('config_tax_customer') == 'shipping') {
				$this->session->data['shipping_country_id'] = $this->request->post['country_id'];
				$this->session->data['shipping_zone_id'] = $this->request->post['zone_id'];
				$this->session->data['shipping_postcode'] = $this->request->post['postcode'];				
			}
			
			// Default Payment Address
			if ($this->config->get('config_tax_customer') == 'payment') {
				$this->session->data['payment_country_id'] = $this->request->post['country_id'];
				$this->session->data['payment_zone_id'] = $this->request->post['zone_id'];			
			}
			
			// Seller account part
			$json = array();
			$mails = array();
			unset($this->session->data['seller']['commission']);
			$this->session->data['seller']['approved'] = 0;
			// Create new seller
			switch ($this->config->get('msconf_seller_validation')) {
				case MsSeller::MS_SELLER_VALIDATION_APPROVAL:
					$mails[] = array(
						'type' => MsMail::SMT_SELLER_ACCOUNT_AWAITING_MODERATION
					);
					$mails[] = array(
						'type' => MsMail::AMT_SELLER_ACCOUNT_AWAITING_MODERATION,
						'data' => array(
							'message' => $this->session->data['seller']['reviewer_message']
						)
					);
					$this->session->data['seller']['status'] = MsSeller::STATUS_INACTIVE;
					$json['redirect'] = $this->url->link('seller/account-profile');
					break;
				
				case MsSeller::MS_SELLER_VALIDATION_NONE:
				default:
					$mails[] = array(
						'type' => MsMail::SMT_SELLER_ACCOUNT_CREATED
					);
					$mails[] = array(
						'type' => MsMail::AMT_SELLER_ACCOUNT_CREATED
					);
					$this->session->data['seller']['status'] = MsSeller::STATUS_ACTIVE;
					$this->session->data['seller']['approved'] = 1;
					break;
			}
			
			$this->session->data['seller']['nickname'] = $this->request->post['seller_nickname'];
			
			// SEO urls generation for sellers
			if ($this->config->get('msconf_enable_seo_urls_seller')) {
				$latin_check = '/[^\x{0030}-\x{007f}]/u';
				$non_latin_chars = preg_match($latin_check, $this->session->data['seller']['nickname']);
				if ($this->config->get('msconf_enable_non_alphanumeric_seo') && $non_latin_chars) {
					$this->session->data['seller']['keyword'] = implode("-", str_replace("-", "", explode(" ", strtolower($this->session->data['seller']['nickname']))));
				}
				else {
					$this->session->data['seller']['keyword'] = implode("-", str_replace("-", "", explode(" ", preg_replace("/[^A-Za-z0-9 ]/", '', strtolower($this->session->data['seller']['nickname'])))));
				}
			}
			
			$this->session->data['seller']['description'] = $this->request->post['seller_description'];
			$this->session->data['seller']['company'] = $this->request->post['seller_company'];
			$this->session->data['seller']['country'] = $this->request->post['seller_country_id'];
			$this->session->data['seller']['zone'] = $this->request->post['seller_zone'];
			$this->session->data['seller']['paypal'] = $this->request->post['seller_paypal'];
			$this->session->data['seller']['avatar_name'] = $this->request->post['seller_avatar_name'];
			
			$this->session->data['seller']['seller_id'] = $this->customer->getId();
			$this->session->data['seller']['product_validation'] = $this->config->get('msconf_product_validation'); 
			$this->MsLoader->MsSeller->createSeller($this->session->data['seller']);
			
			$commissions = $this->MsLoader->MsCommission->calculateCommission(array('seller_group_id' => $this->config->get('msconf_default_seller_group_id')));
			$fee = (float)$commissions[MsCommission::RATE_SIGNUP]['flat'];
			
			if ($fee > 0) {
				switch($commissions[MsCommission::RATE_SIGNUP]['payment_method']) {
					case MsPayment::METHOD_PAYPAL:
						// initiate paypal payment
						// set seller status to unpaid
						$this->MsLoader->MsSeller->changeStatus($this->customer->getId(), MsSeller::STATUS_UNPAID);
						
						// unset seller profile creation emails
						unset($mails[0]);
						
						// add payment details
						$payment_id = $this->MsLoader->MsPayment->createPayment(array(
							'seller_id' => $this->customer->getId(),
							'payment_type' => MsPayment::TYPE_SIGNUP,
							'payment_status' => MsPayment::STATUS_UNPAID,
							'payment_method' => MsPayment::METHOD_PAYPAL,
							'amount' => $fee,
							'currency_id' => $this->currency->getId($this->config->get('config_currency')),
							'currency_code' => $this->currency->getCode($this->config->get('config_currency')),
							'description' => sprintf($this->language->get('ms_transaction_signup'), $this->config->get('config_name'))
						));
						
						// assign payment variables
						$json['data']['amount'] = $this->currency->format($fee, $this->config->get('config_currency'), '', FALSE);
						$json['data']['custom'] = $payment_id;
	
						$this->MsLoader->MsMail->sendMails($mails);
						return $this->response->setOutput(json_encode($json));
						break;

					case MsPayment::METHOD_BALANCE:
					default:
						// deduct from balance
						$this->MsLoader->MsBalance->addBalanceEntry($this->customer->getId(),
							array(
								'balance_type' => MsBalance::MS_BALANCE_TYPE_SIGNUP,
								'amount' => -$fee,
								'description' => sprintf($this->language->get('ms_transaction_signup'), $this->config->get('config_name'))
							)
						);
						
						$this->MsLoader->MsMail->sendMails($mails);
						break;
				}
			} else {
				$this->MsLoader->MsMail->sendMails($mails);
			}
			
	  		$this->redirect($this->url->link('account/success'));
    	} 

      	$this->data['breadcrumbs'] = array();

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),        	
        	'separator' => false
      	); 

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_account'),
			'href'      => $this->url->link('account/account', '', 'SSL'),      	
        	'separator' => $this->language->get('text_separator')
      	);
		
      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_register'),
			'href'      => $this->url->link('account/register-seller', '', 'SSL'),      	
        	'separator' => $this->language->get('text_separator')
      	);
		
    	$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['text_account_already'] = sprintf($this->language->get('text_account_already'), $this->url->link('account/login', '', 'SSL'));
		$this->data['text_your_details'] = $this->language->get('text_your_details');
    	$this->data['text_your_address'] = $this->language->get('text_your_address');
    	$this->data['text_your_password'] = $this->language->get('text_your_password');
		$this->data['text_newsletter'] = $this->language->get('text_newsletter');
		$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');
		$this->data['text_select'] = $this->language->get('text_select');
		$this->data['text_none'] = $this->language->get('text_none');
						
    	$this->data['entry_firstname'] = $this->language->get('entry_firstname');
    	$this->data['entry_lastname'] = $this->language->get('entry_lastname');
    	$this->data['entry_email'] = $this->language->get('entry_email');
    	$this->data['entry_telephone'] = $this->language->get('entry_telephone');
    	$this->data['entry_fax'] = $this->language->get('entry_fax');
		$this->data['entry_company'] = $this->language->get('entry_company');
		$this->data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$this->data['entry_company_id'] = $this->language->get('entry_company_id');
		$this->data['entry_tax_id'] = $this->language->get('entry_tax_id');
    	$this->data['entry_address_1'] = $this->language->get('entry_address_1');
    	$this->data['entry_address_2'] = $this->language->get('entry_address_2');
    	$this->data['entry_postcode'] = $this->language->get('entry_postcode');
    	$this->data['entry_city'] = $this->language->get('entry_city');
    	$this->data['entry_country'] = $this->language->get('entry_country');
    	$this->data['entry_zone'] = $this->language->get('entry_zone');
		$this->data['entry_newsletter'] = $this->language->get('entry_newsletter');
    	$this->data['entry_password'] = $this->language->get('entry_password');
    	$this->data['entry_confirm'] = $this->language->get('entry_confirm');

		$this->data['button_continue'] = $this->language->get('button_continue');
    
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->error['firstname'])) {
			$this->data['error_firstname'] = $this->error['firstname'];
		} else {
			$this->data['error_firstname'] = '';
		}	
		
		if (isset($this->error['lastname'])) {
			$this->data['error_lastname'] = $this->error['lastname'];
		} else {
			$this->data['error_lastname'] = '';
		}		
	
		if (isset($this->error['email'])) {
			$this->data['error_email'] = $this->error['email'];
		} else {
			$this->data['error_email'] = '';
		}
		
		if (isset($this->error['telephone'])) {
			$this->data['error_telephone'] = $this->error['telephone'];
		} else {
			$this->data['error_telephone'] = '';
		}
		
		if (isset($this->error['password'])) {
			$this->data['error_password'] = $this->error['password'];
		} else {
			$this->data['error_password'] = '';
		}
		
 		if (isset($this->error['confirm'])) {
			$this->data['error_confirm'] = $this->error['confirm'];
		} else {
			$this->data['error_confirm'] = '';
		}
		
  		if (isset($this->error['company_id'])) {
			$this->data['error_company_id'] = $this->error['company_id'];
		} else {
			$this->data['error_company_id'] = '';
		}
		
  		if (isset($this->error['tax_id'])) {
			$this->data['error_tax_id'] = $this->error['tax_id'];
		} else {
			$this->data['error_tax_id'] = '';
		}
								
  		if (isset($this->error['address_1'])) {
			$this->data['error_address_1'] = $this->error['address_1'];
		} else {
			$this->data['error_address_1'] = '';
		}
   		
		if (isset($this->error['city'])) {
			$this->data['error_city'] = $this->error['city'];
		} else {
			$this->data['error_city'] = '';
		}
		
		if (isset($this->error['postcode'])) {
			$this->data['error_postcode'] = $this->error['postcode'];
		} else {
			$this->data['error_postcode'] = '';
		}

		if (isset($this->error['country'])) {
			$this->data['error_country'] = $this->error['country'];
		} else {
			$this->data['error_country'] = '';
		}

		if (isset($this->error['zone'])) {
			$this->data['error_zone'] = $this->error['zone'];
		} else {
			$this->data['error_zone'] = '';
		}
		
		// Seller account field errors
		if (isset($this->error['seller_nickname'])) {
			$this->data['error_seller_nickname'] = $this->error['seller_nickname'];
		} else {
			$this->data['error_seller_nickname'] = '';
		}
		
		if (isset($this->error['seller_terms'])) {
			$this->data['error_seller_terms'] = $this->error['seller_terms'];
		} else {
			$this->data['error_seller_terms'] = '';
		}
		
		if (isset($this->error['seller_company'])) {
			$this->data['error_seller_company'] = $this->error['seller_company'];
		} else {
			$this->data['error_seller_company'] = '';
		}
		
		if (isset($this->error['seller_description'])) {
			$this->data['error_seller_description'] = $this->error['seller_description'];
		} else {
			$this->data['error_seller_description'] = '';
		}
		
		if (isset($this->error['seller_paypal'])) {
			$this->data['error_seller_paypal'] = $this->error['seller_paypal'];
		} else {
			$this->data['error_seller_paypal'] = '';
		}
		
    	$this->data['action'] = $this->url->link('account/register-seller', '', 'SSL');
		
		if (isset($this->request->post['firstname'])) {
    		$this->data['firstname'] = $this->request->post['firstname'];
		} else {
			$this->data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
    		$this->data['lastname'] = $this->request->post['lastname'];
		} else {
			$this->data['lastname'] = '';
		}
		
		if (isset($this->request->post['email'])) {
    		$this->data['email'] = $this->request->post['email'];
		} else {
			$this->data['email'] = '';
		}
		
		if (isset($this->request->post['telephone'])) {
    		$this->data['telephone'] = $this->request->post['telephone'];
		} else {
			$this->data['telephone'] = '';
		}
		
		if (isset($this->request->post['fax'])) {
    		$this->data['fax'] = $this->request->post['fax'];
		} else {
			$this->data['fax'] = '';
		}
		
		if (isset($this->request->post['company'])) {
    		$this->data['company'] = $this->request->post['company'];
		} else {
			$this->data['company'] = '';
		}

		$this->load->model('account/customer_group');
		
		$this->data['customer_groups'] = array();
		
		if (is_array($this->config->get('config_customer_group_display'))) {
			$customer_groups = $this->model_account_customer_group->getCustomerGroups();
			
			foreach ($customer_groups as $customer_group) {
				if (in_array($customer_group['customer_group_id'], $this->config->get('config_customer_group_display'))) {
					$this->data['customer_groups'][] = $customer_group;
				}
			}
		}
		
		if (isset($this->request->post['customer_group_id'])) {
    		$this->data['customer_group_id'] = $this->request->post['customer_group_id'];
		} else {
			$this->data['customer_group_id'] = $this->config->get('config_customer_group_id');
		}
		
		// Company ID
		if (isset($this->request->post['company_id'])) {
    		$this->data['company_id'] = $this->request->post['company_id'];
		} else {
			$this->data['company_id'] = '';
		}
		
		// Tax ID
		if (isset($this->request->post['tax_id'])) {
    		$this->data['tax_id'] = $this->request->post['tax_id'];
		} else {
			$this->data['tax_id'] = '';
		}
						
		if (isset($this->request->post['address_1'])) {
    		$this->data['address_1'] = $this->request->post['address_1'];
		} else {
			$this->data['address_1'] = '';
		}

		if (isset($this->request->post['address_2'])) {
    		$this->data['address_2'] = $this->request->post['address_2'];
		} else {
			$this->data['address_2'] = '';
		}

		if (isset($this->request->post['postcode'])) {
    		$this->data['postcode'] = $this->request->post['postcode'];
		} elseif (isset($this->session->data['shipping_postcode'])) {
			$this->data['postcode'] = $this->session->data['shipping_postcode'];		
		} else {
			$this->data['postcode'] = '';
		}
		
		if (isset($this->request->post['city'])) {
    		$this->data['city'] = $this->request->post['city'];
		} else {
			$this->data['city'] = '';
		}

    	if (isset($this->request->post['country_id'])) {
      		$this->data['country_id'] = $this->request->post['country_id'];
		} elseif (isset($this->session->data['shipping_country_id'])) {
			$this->data['country_id'] = $this->session->data['shipping_country_id'];		
		} else {	
      		$this->data['country_id'] = $this->config->get('config_country_id');
    	}

    	if (isset($this->request->post['zone_id'])) {
      		$this->data['zone_id'] = $this->request->post['zone_id']; 	
		} elseif (isset($this->session->data['shipping_zone_id'])) {
			$this->data['zone_id'] = $this->session->data['shipping_zone_id'];			
		} else {
      		$this->data['zone_id'] = '';
    	}
		
		$this->load->model('localisation/country');
		
    	$this->data['countries'] = $this->model_localisation_country->getCountries();
		
		if (isset($this->request->post['password'])) {
    		$this->data['password'] = $this->request->post['password'];
		} else {
			$this->data['password'] = '';
		}
		
		if (isset($this->request->post['confirm'])) {
    		$this->data['confirm'] = $this->request->post['confirm'];
		} else {
			$this->data['confirm'] = '';
		}
		
		if (isset($this->request->post['newsletter'])) {
    		$this->data['newsletter'] = $this->request->post['newsletter'];
		} else {
			$this->data['newsletter'] = '';
		}
		
		// Seller account fields
		if (isset($this->request->post['seller_nickname'])) {
    		$this->data['seller_nickname'] = $this->request->post['seller_nickname'];
		} else {
			$this->data['seller_nickname'] = '';
		}
		
		if (isset($this->request->post['seller_description'])) {
    		$this->data['seller_description'] = $this->request->post['seller_description'];
		} else {
			$this->data['seller_description'] = '';
		}
		
		if (isset($this->request->post['seller_company'])) {
    		$this->data['seller_company'] = $this->request->post['seller_company'];
		} else {
			$this->data['seller_company'] = '';
		}
		
		/*if (isset($this->request->post['seller_country'])) {
    		$this->data['seller_country'] = $this->request->post['seller_country'];
		} else {
			$this->data['seller_country'] = '';
		}*/
		if (isset($this->request->post['seller_country'])) {
			$this->data['seller_country'] = $this->request->post['seller_country'];
		} else {
      		$this->data['seller_country'] = $this->config->get('config_country_id');
    	}
		
		if (isset($this->request->post['seller_zone'])) {
    		$this->data['seller_zone'] = $this->request->post['seller_zone'];
		} else {
			$this->data['seller_zone'] = '';
		}
		
		if (isset($this->request->post['seller_paypal'])) {
    		$this->data['seller_paypal'] = $this->request->post['seller_paypal'];
		} else {
			$this->data['seller_paypal'] = '';
		}
		
		if (isset($this->request->post['seller_avatar'])) {
    		$this->data['seller_avatar'] = $this->request->post['seller_avatar'];
		} else {
			$this->data['seller_avatar'] = '';
		}
		
		if ($this->config->get('config_account_id')) {
			$this->load->model('catalog/information');
			
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
			
			if ($information_info) {
				$this->data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_account_id'), 'SSL'), $information_info['title'], $information_info['title']);
			} else {
				$this->data['text_agree'] = '';
			}
		} else {
			$this->data['text_agree'] = '';
		}
		
		if (isset($this->request->post['agree'])) {
      		$this->data['agree'] = $this->request->post['agree'];
		} else {
			$this->data['agree'] = false;
		}
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/register-seller.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/account/register-seller.tpl';
		} else {
			$this->template = 'default/template/account/register-seller.tpl';
		}
		
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'	
		);
		
		// ***** Seller account part *****
		$this->document->addScript('catalog/view/javascript/one-page-seller-account.js');
		$this->document->addScript('catalog/view/javascript/plupload/plupload.full.js');
		$this->document->addScript('catalog/view/javascript/plupload/jquery.plupload.queue/jquery.plupload.queue.js');
		
		// ckeditor
		if($this->config->get('msconf_enable_rte'))
			$this->document->addScript('catalog/view/javascript/multimerch/ckeditor/ckeditor.js');

		// colorbox
		$this->document->addScript('catalog/view/javascript/jquery/colorbox/jquery.colorbox.js');
		$this->document->addStyle('catalog/view/javascript/jquery/colorbox/colorbox.css');
		
		$this->load->model('localisation/country');
		$this->data['countries'] = $this->model_localisation_country->getCountries();
		
		$this->session->data['multiseller']['files'] = array();
		
		if ($this->config->get('msconf_seller_terms_page')) {
			$this->load->model('catalog/information');
			
			$information_info = $this->model_catalog_information->getInformation($this->config->get('msconf_seller_terms_page'));
			
			if ($information_info) {
				$this->data['ms_account_sellerinfo_terms_note'] = sprintf($this->language->get('ms_account_sellerinfo_terms_note'), $this->url->link('information/information/info', 'information_id=' . $this->config->get('msconf_seller_terms_page'), 'SSL'), $information_info['title'], $information_info['title']);
			} else {
				$this->data['ms_account_sellerinfo_terms_note'] = '';
			}
		} else {
			$this->data['ms_account_sellerinfo_terms_note'] = '';
		}
		
		$this->data['group_commissions'] = $this->MsLoader->MsCommission->calculateCommission(array('seller_group_id' => $this->config->get('msconf_default_seller_group_id')));
		switch($this->data['group_commissions'][MsCommission::RATE_SIGNUP]['payment_method']) {
			case MsPayment::METHOD_PAYPAL:
				$this->data['ms_commission_payment_type'] = $this->language->get('ms_account_sellerinfo_fee_paypal');
				$this->data['payment_data'] = array(
					'sandbox' => $this->config->get('msconf_paypal_sandbox'),
					'action' => $this->config->get('msconf_paypal_sandbox') ? "https://www.sandbox.paypal.com/cgi-bin/webscr" : "https://www.paypal.com/cgi-bin/webscr",
					'business' => $this->config->get('msconf_paypal_address'),
					'item_name' => sprintf($this->language->get('ms_account_sellerinfo_signup_itemname'), $this->config->get('config_name')),
					'item_number' => isset($this->request->get['seller_id']) ? (int)$this->request->get['seller_id'] : '',
					'amount' => '',
					'currency_code' => $this->config->get('config_currency'),
					'return' => $this->url->link('seller/account-dashboard'),
					'cancel_return' => $this->url->link('account/account'),
					'notify_url' => $this->url->link('payment/multimerch-paypal/signupIPN'),
					'custom' => 'custom'
				);
				
				list($this->template, $this->children) = $this->MsLoader->MsHelper->loadTemplate('payment-paypal');
				$this->data['payment_form'] = $this->render();
				break;
				
			case MsPayment::METHOD_BALANCE:
			default:
				$this->data['ms_commission_payment_type'] = $this->language->get('ms_account_sellerinfo_fee_balance');
				break;
		}

		// Get avatars
		if ($this->config->get('msconf_avatars_for_sellers') == 1 || $this->config->get('msconf_avatars_for_sellers') == 2) {
			$this->data['predefined_avatars'] = $this->MsLoader->MsFile->getPredefinedAvatars();
		}

		$this->data['seller_validation'] = $this->config->get('msconf_seller_validation');
		
		$this->response->setOutput($this->render());	
  	}

  	protected function validate() {
	
		// ***** Buyer account part *****
		
    	if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
      		$this->error['firstname'] = $this->language->get('error_firstname');
    	}

    	if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
      		$this->error['lastname'] = $this->language->get('error_lastname');
    	}

    	if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
      		$this->error['email'] = $this->language->get('error_email');
    	}

    	if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
      		$this->error['warning'] = $this->language->get('error_exists');
    	}
		
    	if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
      		$this->error['telephone'] = $this->language->get('error_telephone');
    	}
		
		// Customer Group
		$this->load->model('account/customer_group');
		
		if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->post['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$customer_group = $this->model_account_customer_group->getCustomerGroup($customer_group_id);
			
		if ($customer_group) {	
			// Company ID
			if ($customer_group['company_id_display'] && $customer_group['company_id_required'] && empty($this->request->post['company_id'])) {
				$this->error['company_id'] = $this->language->get('error_company_id');
			}
			
			// Tax ID 
			if ($customer_group['tax_id_display'] && $customer_group['tax_id_required'] && empty($this->request->post['tax_id'])) {
				$this->error['tax_id'] = $this->language->get('error_tax_id');
			}						
		}
		
    	if ((utf8_strlen($this->request->post['address_1']) < 3) || (utf8_strlen($this->request->post['address_1']) > 128)) {
      		$this->error['address_1'] = $this->language->get('error_address_1');
    	}

    	if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 128)) {
      		$this->error['city'] = $this->language->get('error_city');
    	}

		$this->load->model('localisation/country');
		
		$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);
		
		if ($country_info) {
			if ($country_info['postcode_required'] && (utf8_strlen($this->request->post['postcode']) < 2) || (utf8_strlen($this->request->post['postcode']) > 10)) {
				$this->error['postcode'] = $this->language->get('error_postcode');
			}
			
			// VAT Validation
			$this->load->helper('vat');
			
			if ($this->config->get('config_vat') && $this->request->post['tax_id'] && (vat_validation($country_info['iso_code_2'], $this->request->post['tax_id']) == 'invalid')) {
				$this->error['tax_id'] = $this->language->get('error_vat');
			}
		}

    	if ($this->request->post['country_id'] == '') {
      		$this->error['country'] = $this->language->get('error_country');
    	}
		
    	if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '') {
      		$this->error['zone'] = $this->language->get('error_zone');
    	}

    	if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
      		$this->error['password'] = $this->language->get('error_password');
    	}

    	if ($this->request->post['confirm'] != $this->request->post['password']) {
      		$this->error['confirm'] = $this->language->get('error_confirm');
    	}
		
		if ($this->config->get('config_account_id')) {
			$this->load->model('catalog/information');
			
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
			
			if ($information_info && !isset($this->request->post['agree'])) {
      			$this->error['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
			}
		}
		
		// ***** Seller account part *****
		
		$data = $this->request->post;
		if (empty($data['seller_nickname'])) {
			//$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_empty');
			$this->error['seller_nickname'] = $this->language->get('ms_error_sellerinfo_nickname_empty');
		} else if (mb_strlen($data['seller_nickname']) < 4 || mb_strlen($data['seller_nickname']) > 128 ) {
			//$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_length');
			$this->error['seller_nickname'] = $this->language->get('ms_error_sellerinfo_nickname_length');
		} else if ($this->MsLoader->MsSeller->nicknameTaken($data['seller_nickname'])) {
			//$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_taken');
			$this->error['seller_nickname'] = $this->language->get('ms_error_sellerinfo_nickname_taken');
		} else {
			switch($this->config->get('msconf_nickname_rules')) {
				case 1:
					// extended latin
					if(!preg_match("/^[a-zA-Z0-9_\-\s\x{00C0}-\x{017F}]+$/u", $data['seller_nickname'])) {
						//$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_latin');
						$this->error['seller_nickname'] = $this->language->get('ms_error_sellerinfo_nickname_latin');
					}
					break;
					
				case 2:
					// utf8
					if(!preg_match("/((?:[\x01-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3}){1,100})./x", $data['seller_nickname'])) {
						//$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_utf8');
						$this->error['seller_nickname'] = $this->language->get('ms_error_sellerinfo_nickname_utf8');
					}
					break;
					
				case 0:
				default:
					// alnum
					if(!preg_match("/^[a-zA-Z0-9_\-\s]+$/", $data['seller_nickname'])) {
						//$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_alphanumeric');
						$this->error['seller_nickname'] = $this->language->get('ms_error_sellerinfo_nickname_alphanumeric');
					}
					break;
			}
		}
		
		if ($this->config->get('msconf_seller_terms_page')) {
			$this->load->model('catalog/information');
			$information_info = $this->model_catalog_information->getInformation($this->config->get('msconf_seller_terms_page'));
			
			if ($information_info && !isset($data['seller_terms'])) {
				//$json['errors']['seller[terms]'] = htmlspecialchars_decode(sprintf($this->language->get('ms_error_sellerinfo_terms'), $information_info['title']));
				$this->error['seller_terms'] = htmlspecialchars_decode(sprintf($this->language->get('ms_error_sellerinfo_terms'), $information_info['title']));
			}
		}
		
		if (mb_strlen($data['seller_company']) > 50 ) {
			//$json['errors']['seller[company]'] = $this->language->get('ms_error_sellerinfo_company_length');
			$this->error['seller_company'] = $this->language->get('ms_error_sellerinfo_company_length');
		}
		
		if (mb_strlen($data['seller_description']) > 1000) {
			//$json['errors']['seller[description]'] = $this->language->get('ms_error_sellerinfo_description_length');
			$this->error['seller_description'] = $this->language->get('ms_error_sellerinfo_description_length');
		}

		if (mb_strlen($data['seller_paypal']) > 256) {
			//$json['errors']['seller[paypal]'] = $this->language->get('ms_error_sellerinfo_paypal');
			$this->error['seller_paypal'] = $this->language->get('ms_error_sellerinfo_paypal');
		}
		
		if (isset($data['seller_avatar_name']) && !empty($data['seller_avatar_name'])) {
			if ($this->config->get('msconf_avatars_for_sellers') == 2 && !$this->MsLoader->MsFile->checkPredefinedAvatar($data['seller_avatar_name'])) {
				$this->error['seller_avatar'] = $this->language->get('ms_error_file_upload_error');
			} elseif ($this->config->get('msconf_avatars_for_sellers') == 1 && !$this->MsLoader->MsFile->checkPredefinedAvatar($data['seller_avatar_name']) && !$this->MsLoader->MsFile->checkFileAgainstSession($data['seller_avatar_name'])) {
				$this->error['seller_avatar'] = $this->language->get('ms_error_file_upload_error');
			} elseif ($this->config->get('msconf_avatars_for_sellers') == 0 && !$this->MsLoader->MsFile->checkFileAgainstSession($data['seller_avatar_name'])) {
				$this->error['seller_avatar'] = $this->language->get('ms_error_file_upload_error');
			}
		}
		
		// strip disallowed tags in description
		if ($this->config->get('msconf_enable_rte')) {
			if ($this->config->get('msconf_rte_whitelist') != '') {		
				$allowed_tags = explode(",", $this->config->get('msconf_rte_whitelist'));
				$allowed_tags_ready = "";
				foreach($allowed_tags as $tag) {
					$allowed_tags_ready .= "<".trim($tag).">";
				}
				$data['seller_description'] = htmlspecialchars(strip_tags(htmlspecialchars_decode($data['seller_description'], ENT_COMPAT), $allowed_tags_ready), ENT_COMPAT, 'UTF-8');
			}
		} else {
			$data['seller_description'] = htmlspecialchars(nl2br($data['seller_description']), ENT_COMPAT, 'UTF-8');
		}
		
    	if (!$this->error) {
      		return true;
    	} else {
      		return false;
    	}
  	}
	
	public function country() {
		$json = array();
		
		$this->load->model('localisation/country');

    	$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);
		
		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']		
			);
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	/***********************
	 * Seller account part *
	 ***********************/
	 
	public function jxUploadSellerAvatar() {
		$json = array();
		$file = array();
		
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'));
		
		$json['errors'] = $this->MsLoader->MsFile->checkPostMax($_POST, $_FILES);

		if ($json['errors']) {
			return $this->response->setOutput(json_encode($json));
		}

		foreach ($_FILES as $file) {
			$errors = $this->MsLoader->MsFile->checkImage($file);
			
			if ($errors) {
				$json['errors'] = array_merge($json['errors'], $errors);
			} else {
				$fileName = $this->MsLoader->MsFile->uploadImage($file);
				$thumbUrl = $this->MsLoader->MsFile->resizeImage($this->config->get('msconf_temp_image_path') . $fileName, $this->config->get('msconf_preview_seller_avatar_image_width'), $this->config->get('msconf_preview_seller_avatar_image_height'));
				$json['files'][] = array(
					'name' => $fileName,
					'thumb' => $thumbUrl
				);
			}
		}
		
		return $this->response->setOutput(json_encode($json));
	}
}
?>