<?php

class ControllerAccountMsSeller extends Controller {
	private $name = 'ms-seller';
	private $msSeller;
	private $msProduct;
	
	
	public function __construct($registry) {
		parent::__construct($registry);

		require_once(DIR_SYSTEM . 'library/ms-image.php');
		require_once(DIR_SYSTEM . 'library/ms-request.php');
		require_once(DIR_SYSTEM . 'library/ms-transaction.php');
		require_once(DIR_SYSTEM . 'library/ms-product.php');
		require_once(DIR_SYSTEM . 'library/ms-seller.php');
		$this->msSeller = new MsSeller($this->registry);
		$this->msProduct = new MsProduct($this->registry);
		
		
		$parts = explode('/', $this->request->request['route']);

    	if (!$this->customer->isLogged()) {
	  		$this->session->data['redirect'] = $this->url->link('account/ms-seller', '', 'SSL');
	  		$this->redirect($this->url->link('account/login', '', 'SSL')); 
    	} else if (!$this->seller->isSeller()) {
    		if (!in_array($parts[2], array('jxuploadfile','sellerinfo','jxsavesellerinfo'))) {
    			$this->redirect($this->url->link('account/ms-seller/sellerinfo', '', 'SSL'));
    		}
    	}

		//if (!empty($sller) && ($seller['seller_status_id'] != MS_SELLER_STATUS_ACTIVE)) {
		//	$this->_setJsonResponse($json);
		//	return;
		//}
		
		$this->document->addStyle('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/multiseller.css');
		$this->data = array_merge($this->data, $this->load->language('module/multiseller'),$this->load->language('account/account'));
		
		//$config = $this->registry->get('config');
		$this->load->config('ms-config');
		
		//$parts = explode('/', $this->request->get['route']);
		//if ($seller_account_status !== 1 && $parts[2] != 'sellerstatus') {
		//	$this->redirect($this->url->link('account/ms-seller/sellerstatus', '', 'SSL'));
		//}
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
	
	public function jxUploadFile() {
		$json = array();
		$file = array();
				
		if (!empty($this->request->post) && !empty($_FILES)) {
	
			if  (isset($_FILES[$this->request->post['action']]))
				//$file[$this->request->post['action']] = $_FILES[$this->request->post['action']]; 
				$file = $_FILES[$this->request->post['action']];
	
			// allow a maximum of N images
			if ($this->request->post['action'] == 'product_image' && isset($this->request->post['product_images']) && count($this->request->post['product_images']) >= 3) {
				$json['errors'][] = 'No more images allowed';

			}
		} else {
			$POST_MAX_SIZE = ini_get('post_max_size');
			$mul = substr($POST_MAX_SIZE, -1);
			$mul = ($mul == 'M' ? 1048576 : ($mul == 'K' ? 1024 : ($mul == 'G' ? 1073741824 : 1)));
	 		if ($_SERVER['CONTENT_LENGTH'] > $mul * (int)$POST_MAX_SIZE && $POST_MAX_SIZE) {
				$json['errors'][] = 'File too big';	 			
	 		} else {
	 			$json['errors'][] = 'Unknown upload error';
	 		}
			$this->_setJsonResponse($json);
			return;
		}
		
		$image = new MsImage($this->registry);
		
		if ($this->request->post['action'] != 'product_download') {
			if (!$image->validate($file,'I')) {
				$errors = $image->getErrors();
				$json['errors'][$this->request->post['action']] = $errors[0];
			} else {
				$name = $image->upload($file,'I');
				$thumb = $image->resize($image->getTmpPath() . $name, $this->config->get('msconf_image_preview_width'), $this->config->get('msconf_image_preview_height'));
				$json['file'] = array(
					'name' => $name,
					'thumb' => $thumb
				);				
			}
		} else {
			if (!$image->validate($file,'F')) {
				$errors = $image->getErrors();
				$json['errors'][key($_FILES)] = $errors[0];
			} else {
				$name = $image->upload($file,'F');
				$json['file'] = array(
					'name' => $file['name'],
					'src' => $name
				);				
			}			
		}
		
		$this->_setJsonResponse($json);
	}	
	
	public function jxSaveProductDraft() {
		
		$data = $this->request->post;
		
		$this->load->model('module/multiseller/seller');
		
		if (isset($data['product_id']) && !empty($data['product_id'])) {
			$product = $this->msProduct->getProduct($data['product_id'], $this->customer->getId());
			$data['product_thumbnail_path'] = $product['thumbnail'];
			$data['images'] = $this->msProduct->getProductImages($data['product_id']);
		}

		$json = array();

		// only check default language for errors
		$i = 0;
		foreach ($data['languages'] as $language_id => $language) {
			// main language inputs are mandatory
			if ($i == 0) {
				if (empty($language['product_name'])) {
					$json['errors']['product_name_' . $language_id] = $this->language->get('ms_error_product_name_empty'); 
				} else if (mb_strlen($language['product_name']) > 50 ) {
					$json['errors']['product_name_' . $language_id] = $this->language->get('ms_error_product_name_length');			
				}
		
				if (mb_strlen($language['product_description']) > 1000 ) {
					$json['errors']['product_description_' . $language_id] = $this->language->get('ms_error_product_description_length');			
				}
			} else {
				if (!empty($language['product_name']) && (mb_strlen($language['product_name']) < 4 || mb_strlen($language['product_name']) > 50)) {
					$json['errors']['product_name_' . $language_id] = $this->language->get('ms_error_product_name_length');			
				}

				if (!empty($language['product_description']) && (mb_strlen($language['product_description']) < 25 || mb_strlen($language['product_description']) > 1000)) {
					$json['errors']['product_description_' . $language_id] = $this->language->get('ms_error_product_description_length');			
				}
			}
			
			if (!empty($language['product_tags']) && mb_strlen($language['product_tags']) > 1000) {
				$json['errors']['product_tags_' . $language_id] = $this->language->get('ms_error_product_tags_length');			
			}
						
			$i++;
		}
		
		if (!empty($data['product_price'])) {
			if (!is_numeric($data['product_price'])) {
				$json['errors']['product_price'] = $this->language->get('ms_error_product_price_invalid');			
			} else if ($data['product_price'] < $this->config->get('msconf_minimum_product_price')) {
				$json['errors']['product_price'] = $this->language->get('ms_error_product_price_low');
			}
		}
		
		if (isset($data['product_thumbnail_name']) && !empty($data['product_thumbnail_name'])) {
			$thumbnail = MsImage::byName($this->registry, $data['product_thumbnail_name']);
			if (!$thumbnail->checkFileAgainstSession()) {
				$json['errors']['product_thumbnail'] = $thumbnail->getErrors();
			}
			unset($thumbnail);
		}

		if (isset($data['product_downloads'])) {
			foreach ($data['product_downloads'] as $download) {
				$dl = MsImage::byName($this->registry, $download);
				if (!$dl->checkFileAgainstSession()) {
					$json['errors']['product_download'] = $dl->getErrors();
				}
				unset($dl);
			}
		}
		
		if (isset($data['product_images'])) {
			foreach ($data['product_images'] as $image) {
				$img = MsImage::byName($this->registry, $image);
				if (!$img->checkFileAgainstSession()) {
					$json['errors']['product_image'] = $img->getErrors();
				}
				unset($img);
			}
		}

		if (empty($json['errors'])) {
			$data['enabled'] = 0;
			$data['review_status_id'] = MsProduct::MS_PRODUCT_STATUS_DRAFT;
			
			if (isset($data['product_id']) && !empty($data['product_id'])) {
				$this->msProduct->editProduct($data);
			} else {
				$this->msProduct->saveProduct($data);
			}
			
			$json['redirect'] = $this->url->link('account/ms-seller/products', '', 'SSL');			
		}

		$this->_setJsonResponse($json);
	}
	
	public function jxSubmitProduct() {
		$data = $this->request->post;
		
		$this->load->model('module/multiseller/seller');

		if (isset($data['product_id']) && !empty($data['product_id'])) {
			$product = $this->msProduct->getProduct($data['product_id'], $this->customer->getId());
			$data['product_thumbnail_path'] = $product['thumbnail'];
			$data['images'] = $this->msProduct->getProductImages($data['product_id']);
		}
		
		$json = array();

		// only check default language for errors
		$i = 0;
		foreach ($data['languages'] as $language_id => $language) {
			// main language inputs are mandatory
			if ($i == 0) {
				if (empty($language['product_name'])) {
					$json['errors']['product_name_' . $language_id] = $this->language->get('ms_error_product_name_empty'); 
				} else if (mb_strlen($language['product_name']) < 4 || mb_strlen($language['product_name']) > 50 ) {
					$json['errors']['product_name_' . $language_id] = $this->language->get('ms_error_product_name_length');			
				}
		
				if (empty($language['product_description'])) {
					$json['errors']['product_description_' . $language_id] = $this->language->get('ms_error_product_description_empty'); 
				} else if (mb_strlen($language['product_description']) < 25 || mb_strlen($language['product_description']) > 1000 ) {
					$json['errors']['product_description_' . $language_id] = $this->language->get('ms_error_product_description_length');			
				}
			} else {
				if (!empty($language['product_name']) && (mb_strlen($language['product_name']) < 4 || mb_strlen($language['product_name']) > 50)) {
					$json['errors']['product_name_' . $language_id] = $this->language->get('ms_error_product_name_length');			
				}

				if (!empty($language['product_description']) && (mb_strlen($language['product_description']) < 25 || mb_strlen($language['product_description']) > 1000)) {
					$json['errors']['product_description_' . $language_id] = $this->language->get('ms_error_product_description_length');			
				}
			}
			
			if (!empty($language['product_tags']) && mb_strlen($language['product_tags']) > 1000) {
				$json['errors']['product_tags_' . $language_id] = $this->language->get('ms_error_product_tags_length');			
			}
						
			$i++;
		}
		
		if (empty($data['product_price'])) {
			$json['errors']['product_price'] = $this->language->get('ms_error_product_price_empty'); 
		} else if (!is_numeric($data['product_price'])) {
			$json['errors']['product_price'] = $this->language->get('ms_error_product_price_invalid');
		} else if ($data['product_price'] < $this->config->get('msconf_minimum_product_price')) {
			$json['errors']['product_price'] = $this->language->get('ms_error_product_price_low');
		}		

		if (empty($data['product_category'])) {
			$json['errors']['product_category'] = $this->language->get('ms_error_product_category_empty'); 
		}
		
		if (isset($data['product_thumbnail_name']) && !empty($data['product_thumbnail_name'])) {
			$thumbnail = MsImage::byName($this->registry, $data['product_thumbnail_name']);
			if (!$thumbnail->checkFileAgainstSession()) {
				$json['errors']['product_thumbnail'] = $thumbnail->getErrors();
			}
			unset($thumbnail);
		} else {
			$json['errors']['product_thumbnail'] = $this->language->get('ms_error_product_thumbnail_empty');			
		}

		if (isset($data['product_downloads'])) {
			foreach ($data['product_downloads'] as $download) {
				$dl = MsImage::byName($this->registry, $download);
				if (!$dl->checkFileAgainstSession()) {
					$json['errors']['product_download'] = $dl->getErrors();
				}
				unset($dl);
			}
		} else {
			$json['errors']['product_download'] = $this->language->get('ms_error_product_download_empty');
		}
		
		if (isset($data['product_images'])) {
			foreach ($data['product_images'] as $image) {
				$img = MsImage::byName($this->registry, $image);
				if (!$img->checkFileAgainstSession()) {
					$json['errors']['product_image'] = $img->getErrors();
				}
				unset($img);
			}
		} else {
			$json['errors']['product_image'] = $this->language->get('ms_error_product_image_empty');
		}
		
		if (empty($json['errors'])) {
			// set product status
			switch ($this->config->get('msconf_product_validation')) {
				case MsProduct::MS_PRODUCT_VALIDATION_APPROVAL:
					$data['enabled'] = 0;
					$data['review_status_id'] = MsProduct::MS_PRODUCT_STATUS_PENDING;
					
					if (isset($data['product_id']) && !empty($data['product_id'])) {
						$request_type = MsRequest::MS_REQUEST_PRODUCT_CREATED;
					} else {
						$request_type = MsRequest::MS_REQUEST_PRODUCT_UPDATED;
					}
					break;
					
				case MsProduct::MS_PRODUCT_VALIDATION_NONE:
				default:
					$data['enabled'] = 1;
					$data['review_status_id'] = MsProduct::MS_PRODUCT_STATUS_APPROVED;
					break;
			}

			if (isset($data['product_id']) && !empty($data['product_id'])) {
				$product_id = $this->msProduct->editProduct($data);
			} else {
				$product_id = $this->msProduct->saveProduct($data);
			}
			
			if (isset($request_type)) {
				$r = new MsRequest($this->registry);
				$r->createRequest(array(
					'product_id' => $product_id,
					'seller_id' => $this->customer->getId(),
					'request_type' => $request_type,
					'created_message' => isset($data['product_message']) ? $data['product_message'] : '',
				));
				unset($r);
			}
			
			$json['redirect'] = $this->url->link('account/ms-seller/products', '', 'SSL');
		}
		
		$this->_setJsonResponse($json);
	}

	public function jxRequestMoney() {
		$this->load->model('module/multiseller/seller');
		$msTransaction = new MsTransaction($this->registry);
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
		
		$seller = $this->msSeller->getSellerData($this->customer->getId());
		
		$balance = $this->model_module_multiseller_seller->getBalanceForSeller($this->customer->getId());
		$json = array();
		
		if (preg_match("/[^0-9.]/",$data['withdraw_amount'])) {
			$json['errors']['withdraw_amount'] = 'Incorrect amount';
		} else if (round($data['withdraw_amount'],2) < $balance) {
			$json['errors']['withdraw_amount'] = 'Low balance';
		} else if (round($data['withdraw_amount'],2) < $this->config->get('msconf_minimum_withdrawal_amount')) {
			$json['errors']['withdraw_amount'] = 'You cannot withdraw less than minumum amount';
		}
		/*
		if (empty($json['errors'])) {
			$transaction = array(
				'parent_transaction_id' => 0,
				'order_id' => 0,
				'product_id' => 0,
				'seller_id' => 0,
				'amount' => round($data['withdraw_amount'],2),
				'currency_id' => '',
				'currency_code' =>'',
				'currency_value' =>'',
				'commission' =>''		
			);
			
			$t = new MsTransaction();
			$transaction->addTransaction()				
			
			$r = new MsRequest($this->registry);
			$r->createRequest(array(
				'seller_id' => $this->customer->getId(),
				'request_type' => MsRequest::MS_REQUEST_WITHDRAWAL,
			));
			$this->session->data['success'] = 'Your request is submitted.';
		}
		*/
		$this->_setJsonResponse($json);
	}
	
	public function jxSaveSellerInfo() {
		$this->load->model('module/multiseller/seller');
		$data = $this->request->post;
		$seller = $this->msSeller->getSellerData($this->customer->getId());
		$json = array();
		
		if (!empty($seller) && ($seller['seller_status_id'] != MS_SELLER_STATUS_ACTIVE)) {
			$this->_setJsonResponse($json);
			return;
		}
		
		if (empty($seller)) {
			// seller doesn't exist yet
			if (empty($data['sellerinfo_nickname'])) {
				$json['errors']['sellerinfo_nickname'] = $this->language->get('ms_error_sellerinfo_nickname_empty'); 
			} else if (!ctype_alnum($data['sellerinfo_nickname'])) {
				$json['errors']['sellerinfo_nickname'] = $this->language->get('ms_error_sellerinfo_nickname_alphanumeric');
			} else if (mb_strlen($data['sellerinfo_nickname']) < 4 || mb_strlen($data['sellerinfo_nickname']) > 50 ) {
				$json['errors']['sellerinfo_nickname'] = $this->language->get('ms_error_sellerinfo_nickname_length');			
			} else if ($this->model_module_multiseller_seller->nicknameTaken($data['sellerinfo_nickname'])) {
				$json['errors']['sellerinfo_nickname'] = $this->language->get('ms_error_sellerinfo_nickname_taken');
			}
		}
		
		if (mb_strlen($data['sellerinfo_company']) > 50 ) {
			$json['errors']['sellerinfo_company'] = $this->language->get('ms_error_sellerinfo_company_length');			
		}
		
		if (mb_strlen($data['sellerinfo_description']) > 1000) {
			$json['errors']['sellerinfo_description'] = $this->language->get('ms_error_sellerinfo_description_length');			
		}

		if (mb_strlen($data['sellerinfo_paypal']) > 256) {
			$json['errors']['sellerinfo_paypal'] = $this->language->get('ms_error_sellerinfo_paypal');			
		}
		
		if (isset($data['sellerinfo_avatar_name']) && !empty($data['sellerinfo_avatar_name'])) {
			$avatar = MsImage::byName($this->registry, $data['sellerinfo_avatar_name']);
			if (!$avatar->checkFileAgainstSession()) {
				$json['errors']['sellerinfo_avatar'] = $avatar->getErrors();
			}
			unset($avatar);
		}
		
		if (empty($json['errors'])) {
			if (empty($seller)) {
				// create new seller
				switch ($this->config->get('msconf_seller_validation')) {
					case MS_SELLER_VALIDATION_ACTIVATION:
						$data['seller_status_id'] = MS_SELLER_STATUS_TOBEACTIVATED;
						break;
						
					case MS_SELLER_VALIDATION_APPROVAL:
						$data['seller_status_id'] = MS_SELLER_STATUS_TOBEAPPROVED;

						$r = new MsRequest($this->registry);
						$r->createRequest(array(
							'seller_id' => $this->customer->getId(),
							'request_type' => MsRequest::MS_REQUEST_SELLER_CREATED,
						));
						unset($r);						
						break;
					
					case MS_SELLER_VALIDATION_NONE:
					default:
						$data['seller_status_id'] = MS_SELLER_STATUS_ACTIVE;
						break;
				}
				
				$data['seller_id'] = $this->customer->getId();
				$this->msSeller->createSeller($data);
				
				$this->session->data['success'] = $this->language->get('ms_account_sellerinfo_saved');
			} else {
				// edit seller
				$data['seller_id'] = $seller['seller_id'];
				$this->msSeller->editSeller($data);
				$this->session->data['success'] = $this->language->get('ms_account_sellerinfo_saved');
			}
		}
		
		$this->_setJsonResponse($json);
	}

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

		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;

		$sort = array(
			'order_by'  => 'date_added',
			'order_way' => 'DESC',
			'page' => $page,
			'limit' => 5
		);

		$seller_id = $this->customer->getId();
		
		
		$products = $this->model_module_multiseller_seller->getSellerProducts($seller_id, $sort);
		
		foreach ($products as $product) {
			$this->data['products'][] = Array(
			'name' => $product['name'],
			'number_sold' => $product['number_sold'],
			'status' => $product['status'],
			'review_status' => $product['review_status'],
			'date_added' => date($this->language->get('date_format_short'), strtotime($product['date_added'])),
			'edit_link' => $this->url->link('account/ms-seller/editproduct', 'product_id=' . $product['product_id'], 'SSL'),
			'delete_link' => $this->url->link('account/ms-seller/deleteproduct', 'product_id=' . $product['product_id'], 'SSL')
			);
		}
		
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
		
    	$product = $this->msProduct->getProduct($product_id,$seller_id);		

		if (!$product['product_id']) {
			$this->redirect($this->url->link('account/ms-seller/products', '', 'SSL'));
		} else {
			if (!empty($product['thumbnail'])) {
				$thumbnail = $product['thumbnail'];
				unset($product['thumbnail']);
				$image = MsImage::byName($this->registry, $thumbnail);
				$product['thumbnail']['name'] = $thumbnail;
				$product['thumbnail']['thumb'] = $image->resize($thumbnail, $this->config->get('msconf_image_preview_width'), $this->config->get('msconf_image_preview_height'));
				$this->session->data['multiseller']['files'][] = $thumbnail;
			}
			
			$images = $this->msProduct->getProductImages($product_id);
			foreach ($images as $image) {
				$img = MsImage::byName($this->registry, $image['image']);
				$product['images'][] = array(
					'name' => $image['image'],
					'thumb' => $img->resize($image['image'], $this->config->get('msconf_image_preview_width'), $this->config->get('msconf_image_preview_height'))
				);
				$this->session->data['multiseller']['files'][] = $image['image'];
			}

			$downloads = $this->msProduct->getProductDownloads($product_id);
			foreach ($downloads as $download) {
				$product['downloads'][] = array(
					'name' => $download['mask'],
					'src' => $download['filename'],
					'href' => HTTPS_SERVER . 'download/' . $download['filename'],
				);
				$this->session->data['multiseller']['files'][] = $download['filename'];
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
		
		if ($this->msProduct->productOwnedBySeller($product_id, $seller_id)) {
			$this->msProduct->deleteProduct($product_id);			
		}
		
		$this->redirect($this->url->link('account/ms-seller/products', '', 'SSL'));		
	}	
	

	/* ********************* */
	public function sellerInfo() {
		$this->document->addScript('catalog/view/javascript/jquery.form.js');
		$this->load->model('localisation/country');
    	$this->data['countries'] = $this->model_localisation_country->getCountries();		

		$seller = $this->msSeller->getSellerData($this->customer->getId());

		if (!empty($seller)) {
			$this->data['seller'] = $seller;
			
			if (!empty($seller['avatar_path'])) {
				$image = MsImage::byName($this->registry, $seller['avatar_path']);
				$this->data['seller']['avatar']['name'] = $seller['avatar_path'];
				$this->data['seller']['avatar']['thumb'] = $image->resize($seller['avatar_path'], $this->config->get('msconf_image_preview_width'), $this->config->get('msconf_image_preview_height'));
				$this->session->data['multiseller']['files'][] = $seller['avatar_path'];
			}
			
			switch ($seller['seller_status_id']) {
				case MS_SELLER_STATUS_TOBEACTIVATED:
					$this->data['statustext'] = $this->language->get('ms_account_status') . '<b>' . $this->language->get('ms_account_status_activation') . '</b>';
					$this->data['statustext'] .= '<br />' . $this->language->get('ms_account_status_pleaseactivate');
					break;
				case MS_SELLER_STATUS_TOBEAPPROVED:
					$this->data['statustext'] = $this->language->get('ms_account_status') . '<b>' . $this->language->get('ms_account_status_approval') . '</b>';
					$this->data['statustext'] .= '<br />' . $this->language->get('ms_account_status_willbeapproved');
					break;
				case MS_SELLER_STATUS_ACTIVE:
				default:
					//$this->data['statustext'] = $this->language->get('ms_account_status') . $this->language->get('ms_account_status_active');
					//$this->data['statustext'] .= '<br />' . $this->language->get('ms_account_status_fullaccess');
					break;
			}
		} else { 		
			$this->data['seller'] = FALSE;
			$this->data['statustext'] = $this->language->get('ms_account_status_please_fill_in');			
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
    		unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->data['back'] = $this->url->link('account/account', '', 'SSL');
		$this->document->setTitle($this->language->get('ms_account_sellerinfo_heading'));
		$this->_setBreadcrumbs('ms_account_sellerinfo_breadcrumbs', __FUNCTION__);		
		$this->_renderTemplate('ms-account-sellerinfo');
	}
	/* ********************* */
	
	
	public function transactions() {
		$msTransaction = new MsTransaction($this->registry);
		$this->load->model('module/multiseller/seller');
		
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;

		$sort = array(
			'order_by'  => 'date_created',
			'order_way' => 'DESC',
			'page' => $page,
			'limit' => 5
		);

		$seller_id = $this->customer->getId();
		
		$transactions = $msTransaction->getSellerTransactions($seller_id, $sort);
		
    	foreach ($transactions as &$transaction) {
   			$transaction['amount'] = $this->currency->format($transaction['amount'], $this->config->get('config_currency'));
   			$transaction['net_amount'] = $this->currency->format($transaction['net_amount'], $this->config->get('config_currency'));
   			$transaction['date_created'] = date($this->language->get('date_format_short'), strtotime($transaction['date_created']));
		}

		$this->data['transactions'] = $transactions;
		$this->data['balance'] =  $this->currency->format($this->model_module_multiseller_seller->getBalanceForSeller($seller_id),$this->config->get('config_currency'));
		$pagination = new Pagination();
		$pagination->total = $msTransaction->getTotalSellerTransactions($seller_id);
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
		$this->data['msconf_minimum_withdrawal_amount'] =  $this->currency->format($this->config->get('msconf_minimum_withdrawal_amount'),$this->config->get('config_currency'));
		$this->data['msconf_allow_partial_withdrawal'] = $this->config->get('msconf_allow_partial_withdrawal');
		$this->data['msconf_allow_withdrawal_requests'] = $this->config->get('msconf_allow_withdrawal_requests');
		$this->data['currency_code'] = $this->config->get('config_currency');
		
		if ($this->model_module_multiseller_seller->getBalanceForSeller($seller_id) - $this->config->get('msconf_minimum_withdrawal_amount') > 0) {
			$this->data['withdrawal_minimum_reached'] = TRUE;
		} else {
			$this->data['withdrawal_minimum_reached'] = FALSE;
		}
			
		$this->data['back'] = $this->url->link('account/account', '', 'SSL');
		$this->document->setTitle($this->language->get('ms_account_withdraw_heading'));
		$this->_setBreadcrumbs('ms_account_withdraw_breadcrumbs', __FUNCTION__);		
		$this->_renderTemplate('ms-account-withdraw');
	}
	
	public function test() {
		unset($this->session->data['multiseller']);
		$msTransaction = new MsTransaction($this->registry);
		$msTransaction->addTransactionsForOrder(1);
	}
}
?>
