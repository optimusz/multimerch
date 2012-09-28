<?php

class ControllerModuleMultiseller extends Controller {
	private $name = 'multiseller';
	
	private $settings;
	
	private $error = array();

	public function __construct($registry) {
		parent::__construct($registry);		
		require_once(DIR_SYSTEM . 'library/ms-request.php');
		require_once(DIR_SYSTEM . 'library/ms-product.php');
		require_once(DIR_SYSTEM . 'library/ms-transaction.php');
		require_once(DIR_SYSTEM . 'library/ms-image.php');
		require_once(DIR_SYSTEM . 'library/ms-mail.php');
		require_once(DIR_SYSTEM . 'library/ms-seller.php');
		
		$this->load->config('ms-config');
		
		$parts = explode('/', $this->request->request['route']);
		if (!isset($parts[2]) || !in_array($parts[2], array('install','uninstall'))) {
			$this->msMail = new MsMail($registry);
			$this->msSeller = new MsSeller($registry);
		}
		
		$this->data = array_merge($this->data, $this->load->language('module/multiseller'));
		$this->data['token'] = $this->session->data['token'];
		$this->document->addStyle('view/stylesheet/multiseller.css');
		
		$this->settings = Array(
			"msconf_seller_validation" => MS_SELLER_VALIDATION_NONE,
			"msconf_product_validation" => MsProduct::MS_PRODUCT_VALIDATION_NONE,
			"msconf_seller_commission" => 5,
			"msconf_image_preview_width" => 100,
			"msconf_image_preview_height" => 100,
			"msconf_credit_order_statuses" => "5",
			"msconf_debit_order_statuses" => "8",
			"msconf_minimum_withdrawal_amount" => "50",
			"msconf_allow_partial_withdrawal" => 1,
			"msconf_paypal_sandbox" => 1,			
			"msconf_paypal_api_username" => "",
			"msconf_paypal_api_password" => "",
			"msconf_paypal_api_signature" => "",
			"msconf_allow_withdrawal_requests" => 1,
			"msconf_comments_maxlen" => 500,
			"msconf_allowed_image_types" => "png,jpg",
			"msconf_allowed_download_types" => "zip,rar",
			"msconf_minimum_product_price" => 0,
			"msconf_notification_email" => "",
			"ms_carousel_module" => "",
			"ms_topsellers_module" => "",
			"ms_newsellers_module" => "",	
			"ms_sellerdropdown_module" => "",	
			"msconf_allow_free_products" => 0,
			"msconf_seller_commission_flat" => 0.5,
			"msconf_allow_multiple_categories" => 0,
			"msconf_images_limits" => "0,0",
			"msconf_downloads_limits" => "0,0",			
			"msconf_enable_shipping" => 0, // 0 - no, 1 - yes, 2 - seller select
			"msconf_provide_buyerinfo" => 0, // 0 - no, 1 - yes, 2 - shipping dependent
			"msconf_enable_quantities" => 0, // 0 - no, 1 - yes, 2 - shipping dependent
			"msconf_product_options" => "",
			"msconf_enable_pdf_generator" => 0
		);
	}	
	
	private function _validate($action, $level = 'access') {
//		if (in_array(strtolower($action), array('sellers', 'install','uninstall','jxsavesellerinfo', 'savesettings', 'jxconfirmpayment', 'jxcompletepayment', 'jxproductstatus'))
		if (!$this->user->hasPermission($level, 'module/multiseller')) {
			return $this->forward('error/permission');
		} 			
	}
	
	private function _editSettings() {
		$this->load->model("module/{$this->name}/settings");
		$this->load->model('setting/setting');
		$this->load->model('setting/extension');
		
		$set = $this->model_setting_setting->getSetting($this->name);
		$installed_extensions = $this->model_setting_extension->getInstalled('module');

		$extensions_to_be_installed = array();
		foreach ($this->settings as $name=>$value) {
			if (!array_key_exists($name,$set))
				$set[$name] = $value;
				
			if ((strpos($name,'_module') !== FALSE) && (!in_array(str_replace('_module','',$name),$installed_extensions))) {
				$extensions_to_be_installed[] = str_replace('_module','',$name);
			}
		}

		foreach($set as $s=>$v) {
			if ((strpos($s,'_module') !== FALSE)) {
				if (!isset($this->request->post[$s])) {
					$set[$s] = '';
				} else {
					unset($this->request->post[$s][0]);
					$set[$s] = $this->request->post[$s];
				}
				continue;
			}
			
			if (isset($this->request->post[$s])) {
				$set[$s] = $this->request->post[$s];
				$this->data[$s] = $this->request->post[$s];
			} elseif ($this->config->get($s)) {
				$this->data[$s] = $this->config->get($s);
			} else {
				$this->data[$s] = $this->settings[$s];
			}
		}
		
		$this->model_setting_setting->editSetting($this->name, $set);

		foreach ($extensions_to_be_installed as $ext) {
			$this->model_setting_extension->install('module',$ext);	
		}
	}
	
	private function _setBreadcrumbs($textVar, $function) {
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('ms_menu_multiseller'),
			'href'      => $this->url->link('module/multiseller', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
   				
      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get($textVar),
			'href'      => $this->url->link("module/{$this->name}/" . strtolower($function), 'token=' . $this->session->data['token'], 'SSL'),       	
        	'separator' => $this->language->get('text_separator')
      	);
	}
	
	private function _renderTemplate($templateName) {
		$this->template = "module/multiseller/$templateName.tpl";
		
		$this->children = array(
			'common/footer',
			'common/header'	
		);

		$this->response->setOutput($this->render());
		return($this->render());
	}	
	
	private function _setJsonResponse($json) {
		if (strcmp(VERSION,'1.5.1.3') >= 0) {
			$this->response->setOutput(json_encode($json));
		} else {
			$this->load->library('json');
			$this->response->setOutput(Json::encode($json));			
		}
	}	
	
	public function install() {
		$this->_validate(__FUNCTION__);		
		$this->load->model("module/{$this->name}/settings");
		$this->load->model('setting/setting');
		$this->model_module_multiseller_settings->createTable();
		$this->model_setting_setting->editSetting($this->name, $this->settings);
	}

	public function uninstall() {
		$this->_validate(__FUNCTION__);
		$this->load->model("module/{$this->name}/settings");
		$this->model_module_multiseller_settings->dropTable();
	}	
	
	public function jxSaveSellerInfo() {
		
		$this->_validate(__FUNCTION__);
		$data = $this->request->post;
		$seller = $this->msSeller->getSellerData($data['seller_id']);
		$json = array();
		
		if (empty($seller)) {
			if (empty($data['sellerinfo_nickname'])) {
				$json['errors']['sellerinfo_nickname'] = 'Username cannot be empty'; 
			} else if (!ctype_alnum($data['sellerinfo_nickname'])) {
				$json['errors']['sellerinfo_nickname'] = 'Username can only contain alphanumeric characters';
			} else if (strlen($data['sellerinfo_nickname']) < 4 || strlen($data['sellerinfo_nickname']) > 50 ) {
				$json['errors']['sellerinfo_nickname'] = 'Username should be between 4 and 50 characters';			
			} else if ($this->msSeller->nicknameTaken($data['sellerinfo_nickname'])) {
				$json['errors']['sellerinfo_nickname'] = 'This username is already taken';
			}
		}
		
		if (strlen($data['sellerinfo_company']) > 50 ) {
			$json['errors']['sellerinfo_company'] = 'Company name cannot be longer than 50 characters';			
		}
		
		if (empty($json['errors'])) {
			if (!isset($data['sellerinfo_message'])) $data['sellerinfo_message'] = '';
				
			$mails = array();
			if ($data['sellerinfo_action'] != 0) {
				switch ($data['sellerinfo_action']) {
					// enable
					case 1:
						$data['seller_status_id'] = MsSeller::MS_SELLER_STATUS_ACTIVE;
						$mails[] = array(
							'type' => MsMail::SMT_SELLER_ACCOUNT_ENABLED,
							'data' => array(
								'recipients' => $this->msSeller->getSellerEmail($data['seller_id']),
								'addressee' => $this->msSeller->getSellerName($data['seller_id']),
								'message' => $data['sellerinfo_message']
							)
						);
						break;
					
					// disable
					case 2:
						$data['seller_status_id'] = MsSeller::MS_SELLER_STATUS_DISABLED;
						$mails[] = array(
							'type' => MsMail::SMT_SELLER_ACCOUNT_DISABLED,
							'data' => array(
								'recipients' => $this->msSeller->getSellerEmail($data['seller_id']),
								'addressee' => $this->msSeller->getSellerName($data['seller_id']),
								'message' => $data['sellerinfo_message']
							)
						);
						break;
						
					// approve
					case 3:
						$data['seller_status_id'] = MsSeller::MS_SELLER_STATUS_ACTIVE;
						$mails[] = array(
							'type' => MsMail::SMT_SELLER_ACCOUNT_APPROVED,
							'data' => array(
								'recipients' => $this->msSeller->getSellerEmail($data['seller_id']),
								'addressee' => $this->msSeller->getSellerName($data['seller_id']),
								'message' => $data['sellerinfo_message']
							)
						);
						break;
						
					// decline
					case 4:
						$data['seller_status_id'] = MsSeller::MS_SELLER_STATUS_INACTIVE;
						$mails[] = array(
							'type' => MsMail::SMT_SELLER_ACCOUNT_DECLINED,
							'data' => array(
								'recipients' => $this->msSeller->getSellerEmail($data['seller_id']),
								'addressee' => $this->msSeller->getSellerName($data['seller_id']),
								'message' => $data['sellerinfo_message']
							)
						);
						break;
				}
				
				//process requests
				$r = new MsRequest($this->registry);
				$r->processSellerRequests($data['seller_id'], $this->user->getId(), $data['sellerinfo_message']);
				unset($r);
			} else {
				$data['seller_status_id'] = $seller['seller_status_id'];
				$mails[] = array(
					'type' => MsMail::SMT_SELLER_ACCOUNT_MODIFIED,
					'data' => array(
						'recipients' => $this->msSeller->getSellerEmail($data['seller_id']),
						'addressee' => $this->msSeller->getSellerName($data['seller_id']),
						'message' => $data['sellerinfo_message']
					)
				);				
			}
			// edit seller
			$this->msSeller->adminEditSeller($data);
			
			if ($data['sellerinfo_notify']) {
				$this->msMail->sendMails($mails);
			}
			
			$this->session->data['success'] = 'Seller account data saved.';
		}
		
		$this->_setJsonResponse($json);
	}	
	
	public function sellers() {
		$this->_validate(__FUNCTION__);
		
		/*
		$columns = array(
			'name',
			'nickname',
			'email',
			'total_products',
			'total_sales',
			'total_earnings',	
			'current_balance',
			'seller_status_id',
			'date_created',
		);
		*/
		
		
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
		
		$orderby = isset($this->request->get['orderby']) && in_array($this->request->get['orderby'], $columns) ? $this->request->get['orderby'] : 'date_created';
		
		$orderway = isset($this->request->get['orderway']) ? $this->request->get['orderway'] : 'DESC';
		
		$sort = array(
			'order_by'  => $orderby,
			'order_way' => $orderway,
			'page' => $page,
			'limit' => $this->config->get('config_admin_limit')
		);
				
		$results = $this->msSeller->getSellers($sort);
		$total_sellers = $this->msSeller->getTotalSellers($sort);

    	foreach ($results as &$result) {
    		$result['date_created'] = date($this->language->get('date_format_short'), strtotime($result['date_created']));
    		$result['total_products'] = $this->msSeller->getTotalSellerProducts($result['seller_id']);
			//$result['total_earnings'] = $this->currency->format($this->msSeller->getEarningsForSeller($result['seller_id']), $this->config->get('config_currency'));
			$result['current_balance'] = $this->currency->format($this->msSeller->getBalanceForSeller($result['seller_id']), $this->config->get('config_currency'));
			$result['total_sales'] = $this->msSeller->getSalesForSeller($result['seller_id']);
			$result['status'] = $this->msSeller->getSellerStatus($result['seller_status_id']);
			$result['action'][] = array(
				'text' => $this->language->get('text_view'),
				'href' => $this->url->link('module/multiseller/sellerinfo', 'token=' . $this->session->data['token'] . '&seller_id=' . $result['seller_id'], 'SSL')
			);
			
			$result['customer_link'] = $this->url->link('sale/customer/update', 'token=' . $this->session->data['token'] . '&customer_id=' . $result['seller_id'], 'SSL');
		}
			
		$this->data['sellers'] = $results;
			
		$pagination = new Pagination();
		$pagination->total = $total_sellers;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link("module/{$this->name}/sellers", 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');
			
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
			$this->data["link_sort_$column"] = $this->url->link("module/{$this->name}/sellers", 'token=' . $this->session->data['token'] . "&orderby=$column" . $url, 'SSL');
		}
		*/
		$this->data['token'] = $this->session->data['token'];		
		$this->data['heading'] = $this->language->get('ms_catalog_sellers_heading');
		$this->document->setTitle($this->language->get('ms_catalog_sellers_heading'));
		$this->_setBreadcrumbs('ms_catalog_sellers_breadcrumbs', __FUNCTION__);
		$this->_renderTemplate('ms-catalog-sellers');		
	}
	
	public function sellerInfo() {
		$this->_validate(__FUNCTION__);
		
		$this->load->model('localisation/country');
    	$this->data['countries'] = $this->model_localisation_country->getCountries();		

		$seller = $this->msSeller->getSellerData($this->request->get['seller_id']);

		if (!empty($seller)) {
			$this->data['seller'] = $seller;
			$this->data['seller']['status'] = $this->msSeller->getSellerStatus($seller['seller_status_id']);
			if (!empty($seller['avatar_path'])) {
				$image = MsImage::byName($this->registry, $seller['avatar_path']);
				$this->data['seller']['avatar']['name'] = $seller['avatar_path'];
				$this->data['seller']['avatar']['thumb'] = $image->resize($seller['avatar_path'], $this->config->get('msconf_image_preview_width'), $this->config->get('msconf_image_preview_height'));
				//$this->session->data['multiseller']['files'][] = $seller['avatar_path'];
			}
			
			// seller status action selector
			if (in_array($seller['seller_status_id'], array(
					MsSeller::MS_SELLER_STATUS_INACTIVE,
					MsSeller::MS_SELLER_STATUS_DISABLED,
					MsSeller::MS_SELLER_STATUS_TOBEACTIVATED
			))) {			
				$this->data['actions'][] = array(
					'text' => $this->language->get('ms_enable'),
					'value' => 1
				);
			}
			
			if (in_array($seller['seller_status_id'], array(
					MsSeller::MS_SELLER_STATUS_ACTIVE,
					MsSeller::MS_SELLER_STATUS_TOBEACTIVATED
			))) {			
				$this->data['actions'][] = array(
					'text' => $this->language->get('ms_disable'),
					'value' => 2
				);
			}
			
			if ($seller['seller_status_id'] == MsSeller::MS_SELLER_STATUS_TOBEAPPROVED) {
				$this->data['actions'][] = array(
					'text' => $this->language->get('ms_approve'),
					'value' => 3
				);
				$this->data['actions'][] = array(
					'text' => $this->language->get('ms_decline'),
					'value' => 4
				);
			}
			//
		}

		$this->data['currency_code'] = $this->config->get('config_currency');
		$this->data['token'] = $this->session->data['token'];		
		$this->data['heading'] = $this->language->get('ms_catalog_sellerinfo_heading');
		$this->document->setTitle($this->language->get('ms_catalog_sellerinfo_heading'));
		$this->_setBreadcrumbs('ms_catalog_sellerinfo_breadcrumbs', __FUNCTION__);
		$this->_renderTemplate('ms-catalog-sellerinfo');		
	}

	public function saveSettings() {
		$this->_validate(__FUNCTION__);
		
		/*magic
		if ($this->request->post['msconf_image_preview_width'] > 200)
			$this->request->post['msconf_image_preview_width'] = 200;

		if ($this->request->post['msconf_image_preview_height'] > 200)
			$this->request->post['msconf_image_preview_height'] = 200;
			
		$this->request->post['msconf_allowed_image_types'] = 'png,jpg';
		$this->request->post['msconf_allowed_download_types'] = 'zip,rar,pdf';
		
		$this->request->post['msconf_paypal_sandbox'] = 1;
		magic*/
		
		if (isset($this->request->post['msconf_credit_order_statuses'])) 
			$this->request->post['msconf_credit_order_statuses'] = implode(',',$this->request->post['msconf_credit_order_statuses']);
		else
			$this->request->post['msconf_credit_order_statuses'] = '';

		if (isset($this->request->post['msconf_debit_order_statuses'])) 
			$this->request->post['msconf_debit_order_statuses'] = implode(',',$this->request->post['msconf_debit_order_statuses']);
		else
			$this->request->post['msconf_debit_order_statuses'] = '';

		if (isset($this->request->post['msconf_product_options'])) 
			$this->request->post['msconf_product_options'] = implode(',',$this->request->post['msconf_product_options']);
		else
			$this->request->post['msconf_product_options'] = '';	
		
		foreach($this->request->post['msconf_images_limits'] as &$limit)
			$limit = (int)$limit;
		$this->request->post['msconf_images_limits'] = implode(',',$this->request->post['msconf_images_limits']);

		foreach($this->request->post['msconf_downloads_limits'] as &$limit)
			$limit = (int)$limit;
		$this->request->post['msconf_downloads_limits'] = implode(',',$this->request->post['msconf_downloads_limits']);
		
		$this->_editSettings();
		
		$json = array();
		if (strcmp(VERSION,'1.5.1.3') >= 0) {
			$this->response->setOutput(json_encode($json));
		} else {
			$this->load->library('json');
			$this->response->setOutput(Json::encode($json));			
		}		
	}
	
	public function index() {
		$this->_validate(__FUNCTION__);
		$this->load->language("module/{$this->name}");
		$this->load->model("module/{$this->name}/settings");
		
		foreach($this->settings as $s=>$v) {
			$this->data[$s] = $this->config->get($s);
		}

		$this->load->model("localisation/order_status");	
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		$this->data['msconf_images_limits'] = explode(',',$this->data['msconf_images_limits']);
		$this->data['msconf_downloads_limits'] = explode(',',$this->data['msconf_downloads_limits']);
		$this->load->model("catalog/option");	
		$this->data['options'] = $this->model_catalog_option->getOptions();

		$this->data = array_merge($this->data, $this->load->language('module/multiseller'));
				
        $this->data['action'] = $this->url->link("module/{$this->name}/settings", 'token=' . $this->session->data['token'], 'SSL');
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['token'] = $this->session->data['token'];
		
		$this->load->model('design/layout');
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
		$this->data['currency_code'] = $this->config->get('config_currency');
		
		$this->data['button_add_module'] = $this->language->get('button_add_module');
		$this->data['button_remove'] = $this->language->get('button_remove');
		
		if (isset($this->error['image'])) {
			$this->data['error_image'] = $this->error['image'];
		} else {
			$this->data['error_image'] = array();
		}

		$this->_setBreadcrumbs('ms_settings_breadcrumbs', __FUNCTION__);
		$this->document->setTitle($this->language->get('ms_settings_heading'));
		$this->_renderTemplate('multiseller');
	}
	
	public function withdrawals() {
		$this->_validate(__FUNCTION__);
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
		
		$sort = array(
			'order_by'  => 'mr.date_created',
			'order_way' => 'DESC',
			'page' => $page,
			'limit' => $this->config->get('config_admin_limit')
		);

		$r = new MsRequest($this->registry);
		
		$results = $r->getWithdrawalRequests($sort);
		$total_withdrawals = $r->getTotalWithdrawalRequests();

		foreach ($results as $result) {

		$this->data['requests'][] = array(
			'request_id' => $result['req.id'],
			'seller' => $result['sel.nickname'],
			'amount' => $this->currency->format(abs($result['trn.amount']),$this->config->get('config_currency')),
			'date_created' => date($this->language->get('date_format_short'), strtotime($result['req.date_created'])),
			'status' => empty($result['req.date_processed']) ? 'Pending' : 'Completed',
			'processed_by' => $result['u.username'],
			'date_processed' => $result['req.date_processed'] ? date($this->language->get('date_format_short'), strtotime($result['req.date_processed'])) : ''
		);
		}
		
		$pagination = new Pagination();
		$pagination->total = $total_withdrawals;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link("module/{$this->name}/withdrawals", 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');
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
		$this->data['heading'] = $this->language->get('ms_finances_withdrawals_heading');
		$this->document->setTitle($this->language->get('ms_finances_withdrawals_heading'));
		$this->_setBreadcrumbs('ms_finances_withdrawals_breadcrumbs', __FUNCTION__);
		$this->_renderTemplate('ms-finances-withdrawals');
	}
	
	public function transactions() {
		$this->_validate(__FUNCTION__);
		$msTransaction = new MsTransaction($this->registry);
		
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;

		$sort = array(
			'order_by'  => 'mt.date_created',
			'order_way' => 'DESC',
			'page' => $page,
			'limit' => 5
		);

		$results = $msTransaction->getTransactions($sort);
		$total_transactions = $msTransaction->getTotalTransactions();

		foreach ($results as $result) {
			$this->data['transactions'][] = array(
				'seller' => $result['sel.nickname'],
				'description' => $result['trn.description'],
				'net_amount' => $this->currency->format($result['trn.net_amount'], $this->config->get('config_currency')),			
				'date_created' => date($this->language->get('date_format_short'), strtotime($result['trn.date_created'])),
				'date_modified' => date($this->language->get('date_format_short'), strtotime($result['trn.date_modified'])),
				//'status' => empty($result['req.date_processed']) ? 'Pending' : 'Completed',
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $total_transactions;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link("module/{$this->name}/transactions", 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');
		
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
		$this->_setBreadcrumbs('ms_finances_transactions_breadcrumbs', __FUNCTION__);
		$this->_renderTemplate('ms-finances-transactions');
	}
	
	public function products() {
		$this->_validate(__FUNCTION__);
		$msProduct = new MsProduct($this->registry);
		$msImage = new MsImage($this->registry);
		
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;

		$sort = array(
			'order_by'  => 'pr.date_modified',
			'order_way' => 'DESC',
			'page' => $page,
			'limit' => 5
		);

		$results = $msProduct->getProducts($sort, true);
		$total_products = $msProduct->getTotalProducts(true);

		foreach ($results as $result) {
			if ($result['prd.image'] && file_exists(DIR_IMAGE . $result['prd.image'])) {
				$image = $msImage->resize($result['prd.image'], 40, 40);
			} else {
				$image = $msImage->resize('no_image.jpg', 40, 40);
			}		
			
			$action = array();
			$action[] = array(
				'text' => $this->language->get('ms_edit'),
				'href' => $this->url->link('catalog/product/update', 'token=' . $this->session->data['token'] . '&product_id=' . $result['prd.product_id'], 'SSL')
			);
			
			$this->data['products'][] = array(
				'image' => $image,
				'name' => $result['prd.name'],
				'seller' => $result['sel.nickname'],
				'date_created' => date($this->language->get('date_format_short'), strtotime($result['prd.date_created'])),
				'date_modified' => date($this->language->get('date_format_short'), strtotime($result['prd.date_modified'])),
				'status' => $result['prd.status'],
				'action' => $action,
				'product_id' => $result['prd.product_id']
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $total_products;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link("module/{$this->name}/products", 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');
		
		$this->data['pagination'] = $pagination->render();
		
		if (isset($this->session->data['error'])) {
			$this->data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
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
		$this->data['heading'] = $this->language->get('ms_catalog_products_heading');
		$this->document->setTitle($this->language->get('ms_catalog_products_heading'));
		$this->_setBreadcrumbs('ms_catalog_products_breadcrumbs', __FUNCTION__);
		$this->_renderTemplate('ms-catalog-products');
	}	
	
	public function jxConfirmPayment() {
		$this->_validate(__FUNCTION__);
		$json = array();
		require_once(DIR_SYSTEM . 'library/ms-request.php');
		if (isset($this->request->post['selected'])) {
			$r = new MsRequest($this->registry);
			$payments = array();
			$total = 0;
			foreach ($this->request->post['selected'] as $request_id) {
				$result = $r->getRequestPaymentData($request_id);
				if (!empty($result) && ($result['date_processed'] == NULL)) {
					$total += abs($result['amount']);
					$payments[] = array (
						'nickname' => $result['sel.nickname'],
						'paypal' => $result['sel.paypal'],
						'amount' => $this->currency->format(abs($result['trn.amount']),$this->config->get('config_currency'))
					);
				}
			}
			
			if (!empty($payments)) {
				$this->data['total_amount'] = $this->currency->format($total, $this->config->get('config_currency'));
				$this->data['payments'] = $payments;
				$json['html'] = $this->_renderTemplate('ms-masspay-confirmation');
			} else {
				$json['error'] = $this->language->get('ms_error_withdraw_norequests');
			}
		} else {
			$json['error'] = $this->language->get('ms_error_withdraw_norequests');
		}
		$this->_setJsonResponse($json);
		return;		
	}
	
	public function jxConfirmWithdrawalPaid() {
		$this->_validate(__FUNCTION__);
		$json = array();
		require_once(DIR_SYSTEM . 'library/ms-request.php');
		if (isset($this->request->post['selected'])) {
			$r = new MsRequest($this->registry);
			$payments = array();
			$total = 0;
			foreach ($this->request->post['selected'] as $request_id) {
				$result = $r->getRequestPaymentData($request_id);
				if (!empty($result) && ($result['date_processed'] == NULL)) {
					$payments[] = array (
						'nickname' => $result['sel.nickname'],
						'amount' => $this->currency->format(abs($result['trn.amount']),$this->config->get('config_currency'))
					);
				}
			}
			
			if (!empty($payments)) {
				$this->data['total_amount'] = $this->currency->format($total, $this->config->get('config_currency'));
				$this->data['payments'] = $payments;
				$json['html'] = $this->_renderTemplate('ms-withdrawals-confirmation');
			} else {
				$json['error'] = $this->language->get('ms_error_withdraw_norequests');
			}
		} else {
			$json['error'] = $this->language->get('ms_error_withdraw_norequests');
		}
		$this->_setJsonResponse($json);
		return;		
	}	
	
	public function jxCompletePayment() {
		$this->_validate(__FUNCTION__);
		$json = array();
		
		if (!isset($this->request->post['selected'])) {
			$json['error'] = $this->language->get('ms_error_withdraw_norequests');
			$this->_setJsonResponse($json);
			return;
		}
		
		require_once(DIR_SYSTEM . 'library/ms-request.php');
		require_once(DIR_SYSTEM . 'library/ms-paypal.php');

		$requestParams = array(
			'RECEIVERTYPE' => 'EmailAddress',
			'CURRENCYCODE' => $this->config->get('config_currency')
		);
		
		$paymentParams = array();
		
		$r = new MsRequest($this->registry);
		$msTransaction = new MsTransaction($this->registry);
		$i = 0;		
		foreach ($this->request->post['selected'] as $request_id) {
			$result = $r->getRequestPaymentData($request_id);
			if (!empty($result) && ($result['date_processed'] == NULL)) {
				$paymentParams['L_EMAIL' . $i] = $result['sel.paypal'];
				$paymentParams['L_AMT' . $i] = abs($result['trn.amount']);
				$i++;
			}
		}
		
		if (empty($paymentParams)) {
			$json['error'] = $this->language->get('ms_error_withdraw_norequests');
			$this->_setJsonResponse($json);
			return;
		}
		
		$paypal = new PayPal($this->config->get('msconf_paypal_api_username'), $this->config->get('msconf_paypal_api_password'), $this->config->get('msconf_paypal_api_signature'), $this->config->get('msconf_paypal_sandbox'));
		$response = $paypal->request('MassPay',$requestParams + $paymentParams);
		
		if (!$response) {
			$json['error'] = $this->language->get('ms_error_withdraw_response');
			$json['response'] = print_r($paypal->getErrors(), true);
		} else if ($response['ACK'] != 'Success') {
			$json['error'] = $this->language->get('ms_error_withdraw_status');
			$json['response'] = print_r($response, true);
		} else {
			$json['success'] = $this->language->get('ms_success_transactions');
			$json['response'] = print_r($response, true);
			//$mails = array();
			foreach ($this->request->post['selected'] as $request_id) {
				$r->processRequest($request_id, $this->user->getId());
				$msTransaction->completeWithdrawal($r->getAssociatedTransaction($request_id));
				/*
				$mails[] = array(
					'type' => MsProduct::SMT_WITHDRAW_REQUEST_COMPLETED,
					'data' => array(
						'product_id' => $product_id,
						'recipients' => $this->msSeller->getSellerEmail($seller_id),
						'addressee' => $this->msSeller->getSellerName($seller_id),
						'message' => $this->request->post['product_message']
					)
				);*/
			}		
		}
		$this->_setJsonResponse($json);
		return;
	}

	public function jxCompleteWithdrawalPaid() {
		$this->_validate(__FUNCTION__);
		$json = array();
		
		if (!isset($this->request->post['selected'])) {
			$json['error'] = $this->language->get('ms_error_withdraw_norequests');
			$this->_setJsonResponse($json);
			return;
		}
		
		require_once(DIR_SYSTEM . 'library/ms-request.php');

		$r = new MsRequest($this->registry);
		$msTransaction = new MsTransaction($this->registry);
		$i = 0;		
		foreach ($this->request->post['selected'] as $request_id) {
			$r->processRequest($request_id, $this->user->getId());
			$msTransaction->completeWithdrawal($r->getAssociatedTransaction($request_id));
		}
		$json['success'] = $this->language->get('ms_success_withdrawals_marked');
		$this->_setJsonResponse($json);
		return;
	}

	public function jxProductStatus() {
		$this->_validate(__FUNCTION__);
		$mails = array();
		if (isset($this->request->post['selected'])) {
			$msProduct = new MsProduct($this->registry);
			$msRequest = new MsRequest($this->registry);			
			foreach ($this->request->post['selected'] as $product_id) {
				$seller_id = $msProduct->getSellerId($product_id);
				if ($this->request->post['ms-action'] == 'ms-enable') {
					$msProduct->enableProduct($product_id);
					$mails[] = array(
						'type' => $msProduct->getStatus($product_id) == MsProduct::MS_PRODUCT_STATUS_PENDING ? MsMail::SMT_PRODUCT_APPROVED : MsMail::SMT_PRODUCT_ENABLED,
						'data' => array(
							'product_id' => $product_id,
							'recipients' => $this->msSeller->getSellerEmail($seller_id),
							'addressee' => $this->msSeller->getSellerName($seller_id),
							'message' => $this->request->post['product_message']
						)
					);
				} else {
					$msProduct->disableProduct($product_id);
					$mails[] = array(
						'type' => $msProduct->getStatus($product_id) == MsProduct::MS_PRODUCT_STATUS_PENDING ? MsMail::SMT_PRODUCT_DECLINED : MsMail::SMT_PRODUCT_DISABLED,
						'data' => array(
							'product_id' => $product_id,
							'recipients' => $this->msSeller->getSellerEmail($seller_id),
							'addressee' => $this->msSeller->getSellerName($seller_id),
							'message' => $this->request->post['product_message']
						)
					);					
				}
				$msRequest->processProductRequests($product_id,$this->user->getId(),$this->request->post['product_message']);
			}
			unset($msProduct,$msRequest);
			$this->msMail->sendMails($mails);
			$this->session->data['success'] = 'Successfully changed product status.';
		} else {
			$this->session->data['error'] = 'Error changing product status.';
		}
	}	
}
?>
