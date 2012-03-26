<?php

class ControllerAccountMsSeller extends Controller {
	private $name = 'ms-seller';
	
	public function __construct($registry) {
		parent::__construct($registry);
		
		$seller_account_status = 1;
		
		// commented out for testing purposes
		/*
    	if (!$this->customer->isLogged()) {
	  		$this->session->data['redirect'] = $this->url->link('account/ms-seller', '', 'SSL');
	  		$this->redirect($this->url->link('account/login', '', 'SSL')); 
    	} else if (!$this->seller->isSeller()) {
    		// redirect to seller info edit page
    	}
		*/
		
		$this->document->addStyle('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/multiseller.css');
		$this->data = array_merge($this->data, $this->load->language('module/multiseller'),$this->load->language('account/account'));
		
		
		
		//$config = $this->registry->get('config');
		$this->load->config('ms-config');
		
		$parts = explode('/', $this->request->get['route']);
		if ($seller_account_status !== 1 && $parts[2] != 'sellerstatus') {
			$this->redirect($this->url->link('account/ms-seller/sellerstatus', '', 'SSL'));
		}
	}
	
	private function _setBreadcrumbs($textVar, $function) {
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
        	'text'      => $this->language->get($textVar),
			'href'      => $this->url->link("account/{$this->name}/" . strtolower($function), '', 'SSL'),       	
        	'separator' => $this->language->get('text_separator')
      	);
	}
	
	private function _renderTemplate($templateName) {
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . "/template/module/multiseller/$templateName.tpl")) {
			$this->template = $this->config->get('config_template') . "/template/module/multiseller/$templateName.tpl";
		} else {
			$this->template = "default/template/module/multiseller/$templateName.tpl";
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
	
	private function _setJsonResponse($json) {
		if (strcmp(VERSION,'1.5.1.3') >= 0) {
			$this->response->setOutput(json_encode($json));
		} else {
			$this->load->library('json');
			$this->response->setOutput(Json::encode($json));			
		}
	}
	
	public function jxUploadImage() {
		require_once(DIR_SYSTEM . 'library/ms-image.php');
		$json = array();
		
		if (!empty($this->request->post)) {
			// unset the second file input field
			if ($this->request->post['action'] == 'image') {
				unset($_FILES['product_thumbnail']);
			} else if ($this->request->post['action'] == 'thumbnail') {
				unset($_FILES['product_image']);
			}
	
			// allow a maximum of N images
			if ($this->request->post['action'] == 'image' && isset($this->request->post['product_images']) && count($this->request->post['product_images']) >= 3) {
				$json['errors'][] = 'No more images allowed';
				$this->_setJsonResponse($json);
				return;
			}
		}
		
		// only uploading one image at a time
		reset($_FILES);
		$file = current($_FILES);
		
		$image = new MsImage($this->registry);
		
		if (!$image->validate($file)) {
			$errors = $image->getErrors();
			$json['errors'][key($_FILES)] = $errors[0];
		} else {
			$name = $image->upload($file);
			$this->load->model('tool/image');
			$thumb = $this->model_tool_image->resize($name, $this->config->get('msconf_image_preview_width'), $this->config->get('msconf_image_preview_height'));
			$json['image'] = array(
				'name' => $name,
				'thumb' => $thumb
			);				
		} 
		
		$this->_setJsonResponse($json);
	}	
	
	public function jxSaveProductDraft() {
		require_once(DIR_SYSTEM . 'library/ms-image.php');
		$data = $this->request->post;
		$this->load->model('module/multiseller/seller');
		
		if (isset($data['product_id']) && !empty($data['product_id'])) {
			$product = $this->model_module_multiseller_seller->getProduct($data['product_id'], $this->customer->getId());
			$data['product_thumbnail_path'] = $product['thumbnail'];
			$data['images'] = $this->model_module_multiseller_seller->getProductImages($data['product_id']);
		}

		$json = array();

		if (empty($data['product_name'])) {
			$json['errors']['product_name'] = 'Product name cannot be empty';
		} else if (strlen($data['product_name']) > 50 ) {
			$json['errors']['product_name'] = 'Product name too long';			
		}

		if (strlen($data['product_description']) > 1000 ) {
			$json['errors']['product_description'] = 'Product description too long';			
		}
		
		if (!is_numeric($data['product_price']) && (!empty($data['product_price']))) {
			$json['errors']['product_price'] = 'Invalid price';			
		}		

		if (isset($data['product_thumbnail_name']) && !empty($data['product_thumbnail_name'])) {
			$thumbnail = MsImage::byName($this->registry, $data['product_thumbnail_name']);
			if (!$thumbnail->checkImageAgainstSession()) {
				$json['errors']['product_thumbnail'] = $thumbnail->getErrors();
			}
		}
		
		if (isset($data['product_images'])) {
			foreach ($data['product_images'] as $image) {
				$img = MsImage::byName($this->registry, $image);
				if (!$img->checkImageAgainstSession()) {
					$json['errors']['product_image'] = $img->getErrors();
				}
			}
		}

		if (empty($json['errors'])) {
			$data['enabled'] = 0;
			$data['review_status_id'] = MS_PRODUCT_STATUS_DRAFT;
			
			if (isset($data['product_id']) && !empty($data['product_id'])) {
				$this->model_module_multiseller_seller->editProduct($data);
			} else {
				$this->model_module_multiseller_seller->saveProduct($data);
			}
			
			$json['redirect'] = $this->url->link('account/ms-seller/products', '', 'SSL');			
		}

		$this->_setJsonResponse($json);
	}
	
	public function jxSubmitProduct() {
		require_once(DIR_SYSTEM . 'library/ms-image.php');
		$data = $this->request->post;
		$this->load->model('module/multiseller/seller');

		if (isset($data['product_id']) && !empty($data['product_id'])) {
			$product = $this->model_module_multiseller_seller->getProduct($data['product_id'], $this->customer->getId());
			$data['product_thumbnail_path'] = $product['thumbnail'];
			$data['images'] = $this->model_module_multiseller_seller->getProductImages($data['product_id']);
		}
		
		$json = array();

		if (empty($data['product_name'])) {
			$json['errors']['product_name'] = 'Product name cannot be empty'; 
		} else if (strlen($data['product_name']) < 4 || strlen($data['product_name']) > 50 ) {
			$json['errors']['product_name'] = 'Product name should be between 4 and 50 characters';			
		}

		if (empty($data['product_description'])) {
			$json['errors']['product_description'] = 'Product description cannot be empty'; 
		} else if (strlen($data['product_description']) < 25 || strlen($data['product_description']) > 1000 ) {
			$json['errors']['product_description'] = 'Product description should be between 25 and 1000 characters';			
		}
		
		if (empty($data['product_price'])) {
			$json['errors']['product_price'] = 'Please specify a price for your product'; 
		} else if (!is_numeric($data['product_price'])) {
			$json['errors']['product_price'] = 'Invalid price';			
		}		

		if (empty($data['product_category'])) {
			$json['errors']['product_category'] = 'Please select a category'; 
		}
		
		if (isset($data['product_thumbnail_name']) && !empty($data['product_thumbnail_name'])) {
			$thumbnail = MsImage::byName($this->registry, $data['product_thumbnail_name']);
			if (!$thumbnail->checkImageAgainstSession()) {
				$json['errors']['product_thumbnail'] = $thumbnail->getErrors();
			}
		} else {
			$json['errors']['product_thumbnail'] = 'Please upload a thumbnail!';			
		}
		
		if (isset($data['product_images'])) {
			foreach ($data['product_images'] as $image) {
				$img = MsImage::byName($this->registry, $image);
				if (!$img->checkImageAgainstSession()) {
					$json['errors']['product_image'] = $img->getErrors();
				}
			}
		}
		
		if (empty($json['errors'])) {
			// set product status
			switch ($this->config->get('msconf_product_validation')) {
				case MS_PRODUCT_VALIDATION_APPROVAL:
					$data['enabled'] = 0;
					$data['review_status_id'] = MS_PRODUCT_STATUS_PENDING;
					break;
					
				case MS_PRODUCT_VALIDATION_NONE:
				default:
					$data['enabled'] = 1;
					$data['review_status_id'] = MS_PRODUCT_STATUS_APPROVED;
					break;
			}

			if (isset($data['product_id']) && !empty($data['product_id'])) {
				$this->model_module_multiseller_seller->editProduct($data);
			} else {
				$this->model_module_multiseller_seller->saveProduct($data);
			}
			
			$json['redirect'] = $this->url->link('account/ms-seller/products', '', 'SSL');
		}
		
		$this->_setJsonResponse($json);
	}
	
	public function jxSaveSellerInfo() {
		$this->load->model('module/multiseller/seller');
		//require_once(DIR_APPLICATION . 'model/module/multiseller/validator.php');
		$data = $this->request->post;
		/*$data = $this->request->post;
		
		var_dump($data);
		$validator = new MsValidator($data);
		
		$validator->isEmpty('sellerinfo_nickname', 'error');
		
		$errors = $validator->getErrors();
		
		var_dump($data);
		//var_dump($errors);

		return;*/
		
		$json = array();
		
		if (empty($data['sellerinfo_nickname'])) {
			$json['errors']['sellerinfo_nickname'] = 'Display name cannot be empty'; 
		} else if (!ctype_alnum($data['sellerinfo_nickname'])) {
			$json['errors']['sellerinfo_nickname'] = 'Display name can only contain alphanumeric characters';
		} else if (strlen($data['sellerinfo_nickname']) < 4 || strlen($data['sellerinfo_nickname']) > 50 ) {
			$json['errors']['sellerinfo_nickname'] = 'Display name should be between 4 and 50 characters';			
		} else if ($this->model_module_multiseller_seller->nicknameTaken($data['sellerinfo_nickname'])) {
			$json['errors']['sellerinfo_nickname'] = 'This display name is already taken';
		}
		
		if (strlen($data['sellerinfo_company']) > 50 ) {
			$json['errors']['sellerinfo_company'] = 'Company name cannot be longer than 50 characters';			
		}		
		
		
		if (empty($json['errors'])) {
			switch ($this->config->get('msconf_seller_validation')) {
				case MS_SELLER_VALIDATION_ACTIVATION:
					$data['seller_status_id'] = MS_SELLER_STATUS_TOBEACTIVATED;
					break;
					
				case MS_SELLER_VALIDATION_APPROVAL:
					$data['seller_status_id'] = MS_SELLER_STATUS_TOBEAPPROVED;
					break;
				
				case MS_SELLER_VALIDATION_APPROVAL:
				default:
					$data['seller_status_id'] = MS_SELLER_STATUS_ACTIVE;
					break;
			}
			
			$data['avatar_path'] = '';
			$this->model_module_multiseller_seller->saveSellerData($data);
		}
		
		$this->_setJsonResponse($json);
	}

	public function sellerStatus() {
		$this->load->model('module/multiseller/seller');
		$this->document->setTitle($this->language->get('ms_account_status_heading'));
		
		$seller = $this->registry->get('seller');
		
		$this->data['thankyou'] = sprintf($this->language->get('ms_account_sellerinfo_mail_account_thankyou'), $this->config->get('config_name'));
		
		switch ($seller->getStatus()) {
			case MS_SELLER_STATUS_TOBEACTIVATED:
				$this->data['status'] = $this->language->get('ms_account_status_activation');
				$this->data['message1'] = $this->language->get('ms_account_status_pleaseactivate');
				break;
			case MS_SELLER_STATUS_TOBEAPPROVED:
				$this->data['status'] = $this->language->get('ms_account_status_approval');
				$this->data['message1'] = $this->language->get('ms_account_status_willbeapproved');
				break;
			case MS_SELLER_STATUS_ACTIVE:
			default:
				$this->data['status'] = $this->language->get('ms_account_status_active');
				$this->data['message1'] = $this->language->get('ms_account_status_fullaccess');
				break;
		}
		
		$this->data['continue'] = $this->url->link('account/account', '', 'SSL');		
		$this->_setBreadcrumbs('ms_account_status_breadcrumbs', __FUNCTION__);
		$this->_renderTemplate('ms-account-sellerstatus');
	}
		
	//
	public function newProduct() {
		$this->load->model('module/multiseller/seller');
		$this->document->addScript('catalog/view/javascript/jquery.form.js');
				
		$this->data['categories'] = $this->model_module_multiseller_seller->getCategories(0);

		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		$this->data['product'] = FALSE;

		$this->data['heading'] = $this->language->get('ms_account_newproduct_heading');
		$this->document->setTitle($this->language->get('ms_account_newproduct_heading'));
		$this->_setBreadcrumbs('ms_account_newproduct_breadcrumbs', __FUNCTION__);
		$this->_renderTemplate('ms-account-product-form');
	}
	
	public function products() {
		$this->load->model('module/multiseller/seller');

		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;

		$sort = array(
			'order_by'  => 'date_added',
			'order_way' => 'DESC',
			'page' => $page,
			'limit' => 5
		);

		$seller_id = $this->customer->getId();
		
		
		$products = $this->model_module_multiseller_seller->getSellerProducts($seller_id, $sort);
		
		foreach ($products as &$product) {
			$product['edit_link'] = $this->url->link('account/ms-seller/editproduct', 'product_id=' . $product['product_id'], 'SSL');
			$product['delete_link'] = $this->url->link('account/ms-seller/deleteproduct', 'product_id=' . $product['product_id'], 'SSL');
		}
		
		$this->data['products'] = $products; 
		$pagination = new Pagination();
		$pagination->total = $this->model_module_multiseller_seller->getTotalSellerProducts($seller_id);
		$pagination->page = $sort['page'];
		$pagination->limit = $sort['limit']; 
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('account/' . $this->name . '/' . __FUNCTION__, 'page={page}', 'SSL');
		
		$this->data['pagination'] = $pagination->render();
		$this->data['continue'] = $this->url->link('account/account', '', 'SSL');
		
		$this->document->setTitle($this->language->get('ms_account_products_heading'));		
		$this->_setBreadcrumbs('ms_account_products_breadcrumbs', __FUNCTION__);		
		$this->_renderTemplate('ms-account-products');
	}
	
	public function editProduct() {
		$this->load->model('module/multiseller/seller');
		$this->load->model('tool/image');
		$this->document->addScript('catalog/view/javascript/jquery.form.js');
		
		$this->data['categories'] = $this->model_module_multiseller_seller->getCategories(0);		
		
		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();		
		
		$product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;
		$seller_id = $this->customer->getId();
		
    	$product = $this->model_module_multiseller_seller->getProduct($product_id,$seller_id);		

		if (!$product['product_id']) {
			$this->redirect($this->url->link('account/ms-seller/products', '', 'SSL'));
		} else {
			var_dump($product);
			if (!empty($product['thumbnail'])) {
				$thumb_name = $product['thumbnail'];
				unset($product['thumbnail']);
				$product['thumbnail']['name'] = $thumb_name;
				$product['thumbnail']['src'] = $this->model_tool_image->resize($thumb_name, $this->config->get('msconf_image_preview_width'), $this->config->get('msconf_image_preview_height'));
				$this->session->data['multiseller']['images'][] = $thumb_name;
			}
			
			
			$images = $this->model_module_multiseller_seller->getProductImages($product_id);

			foreach ($images as $image) {
				$product['images'][] = array(
					'name' => $image['image'],
					'src' => $this->model_tool_image->resize($image['image'], $this->config->get('msconf_image_preview_width'), $this->config->get('msconf_image_preview_height'))
				);
				$this->session->data['multiseller']['images'][] = $image['image'];
			}

			$this->data['product'] = $product;
			
			$this->data['heading'] = $this->language->get('ms_account_editproduct_heading');
			$this->document->setTitle($this->language->get('ms_account_editproduct_heading'));		
			$this->_setBreadcrumbs('ms_account_editproduct_breadcrumbs', __FUNCTION__);		
			$this->_renderTemplate('ms-account-product-form');
		}
	}
	
	public function deleteProduct() {
		$this->load->model('module/multiseller/seller');
		
		$product_id = (int)$this->request->get['product_id'];
		$seller_id = (int)$this->customer->getId();
		
		if ($this->model_module_multiseller_seller->productOwnedBySeller($product_id, $seller_id)) {
			$this->model_module_multiseller_seller->deleteProduct($product_id);			
		}
		
		$this->redirect($this->url->link('account/ms-seller/products', '', 'SSL'));		
	}	
	

	//
	public function sellerInfo() {
		$this->load->model('module/multiseller/seller');

		$this->load->model('localisation/country');
    	$this->data['countries'] = $this->model_localisation_country->getCountries();		

		$this->document->setTitle($this->language->get('ms_account_sellerinfo_heading'));
		$this->_setBreadcrumbs('ms_account_sellerinfo_breadcrumbs', __FUNCTION__);		
		$this->_renderTemplate('ms-account-sellerinfo');
	}
	
	public function transactions() {
		$this->load->model('module/multiseller/transaction');
		$this->load->model('module/multiseller/seller');
		
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;

		$sort = array(
			'order_by'  => 'date_created',
			'order_way' => 'DESC',
			'page' => $page,
			'limit' => 5
		);

		$seller_id = $this->customer->getId();
		
		$transactions = $this->model_module_multiseller_transaction->getSellerTransactions($seller_id, $sort);
		
    	foreach ($transactions as &$transaction) {
   			$transaction['amount'] = $this->currency->format($transaction['amount'], $this->config->get('config_currency'));
   			$transaction['date_created'] = date($this->language->get('date_format_short'), strtotime($transaction['date_created']));
		}

		$this->data['transactions'] = $transactions;
		$this->data['balance'] =  $this->currency->format($this->model_module_multiseller_seller->getBalanceForSeller($seller_id),$this->config->get('config_currency'));
		$pagination = new Pagination();
		$pagination->total = $this->model_module_multiseller_transaction->getTotalSellerTransactions($seller_id);
		$pagination->page = $sort['page'];
		$pagination->limit = $sort['limit']; 
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('account/' . $this->name . '/' . __FUNCTION__, 'page={page}', 'SSL');
		
		$this->data['pagination'] = $pagination->render();
		$this->data['continue'] = $this->url->link('account/account', '', 'SSL');
		
		$this->document->setTitle($this->language->get('ms_account_transactions_heading'));
		$this->_setBreadcrumbs('ms_account_transactions_breadcrumbs', __FUNCTION__);		
		$this->_renderTemplate('ms-account-transactions');
	}
	
	public function withdraw() {
		$this->load->model('module/multiseller/seller');
		
		$seller_id = $this->customer->getId();
		$this->data['balance'] =  $this->currency->format($this->model_module_multiseller_seller->getBalanceForSeller($seller_id),$this->config->get('config_currency'));
		
		$this->data['continue'] = $this->url->link('account/account', '', 'SSL');
		$this->document->setTitle($this->language->get('ms_account_withdraw_heading'));
		$this->_setBreadcrumbs('ms_account_withdraw_breadcrumbs', __FUNCTION__);		
		$this->_renderTemplate('ms-account-withdraw');
	}

	public function index() {
		$this->load->language("module/{$this->name}");
		$this->load->model("module/{$this->name}");
		$this->load->model('setting/setting');
		
		foreach($this->settings as $s=>$v) {
			$this->data[$s] = $this->config->get($s);
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			if (isset($this->request->post['saveComment'])) {
				
	        } else if (isset($this->request->post['delComment'])) {
	        	
	        } else if (isset($this->request->post['saveConfig']) || isset($this->request->post['submitPositions'])) {
	        	
        	}
	        $this->session->data['success'] = $this->language->get('text_success');
		}
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$this->setBreadcrumbs();
		$this->setTranslations();
				
        $this->data['action'] = $this->url->link("module/{$this->name}", 'token=' . $this->session->data['token'], 'SSL');
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['token'] = $this->session->data['token'];
		$this->load->model('design/layout');
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
		
		$this->template = "module/{$this->name}.tpl";
		$this->children = array(
			'common/header',	
			'common/footer'	
		);
		
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
	}
	
	
	
	
	
	public function test() {
		unset($this->session->data['multiseller']);
		$this->load->model('module/multiseller/transaction');
		$this->model_module_multiseller_transaction->addTransactionsForOrder(1);
	}
}
?>
