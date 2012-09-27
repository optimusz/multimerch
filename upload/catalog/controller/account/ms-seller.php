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
		require_once(DIR_SYSTEM . 'library/ms-mail.php');
		$this->msSeller = new MsSeller($this->registry);
		$this->msProduct = new MsProduct($this->registry);
		$this->msMail = new MsMail($this->registry);
		
		
		$parts = explode('/', $this->request->request['route']);

    	if (!$this->customer->isLogged()) {
	  		$this->session->data['redirect'] = $this->url->link('account/account', '', 'SSL');
	  		$this->redirect($this->url->link('account/login', '', 'SSL')); 
    	} else if (!$this->msSeller->isSeller()) {
    		if (!in_array($parts[2], array('jxuploadfile','sellerinfo','jxsavesellerinfo'))) {
    			$this->redirect($this->url->link('account/ms-seller/sellerinfo', '', 'SSL'));
    		}
    	} else if ($this->msSeller->getStatus() != MsSeller::MS_SELLER_STATUS_ACTIVE) {
    		if (!in_array($parts[2], array('sellerinfo'))) {
    			$this->redirect($this->url->link('account/ms-seller/sellerinfo', '', 'SSL'));
    		}
    	}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
    		unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
		if (file_exists('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/multiseller.css')) {
			$this->document->addStyle('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/multiseller.css');
		} else {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/multiseller.css');
		}
		
		
		$this->data = array_merge($this->data, $this->load->language('module/multiseller'),$this->load->language('account/account'));
		
		//$config = $this->registry->get('config');
		$this->load->config('ms-config');
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
	
			if  (isset($_FILES[$this->request->post['action']])) {
				//$file[$this->request->post['action']] = $_FILES[$this->request->post['action']]; 
				$file = $_FILES[$this->request->post['action']];
			}
	
			// allow a maximum of N images
			$msconf_images_limits = explode(',',$this->config->get('msconf_images_limits'));

			if ($this->request->post['action'] == 'product_image' && isset($this->request->post['product_images']) && $msconf_images_limits[1] > 0 && count($this->request->post['product_images']) >= $msconf_images_limits[1]) {
				$json['errors'][] = sprintf($this->language->get('ms_error_product_image_maximum'),$msconf_images_limits[1]);
				$this->_setJsonResponse($json);
				return;
			}
		} else {
			$POST_MAX_SIZE = ini_get('post_max_size');
			$mul = substr($POST_MAX_SIZE, -1);
			$mul = ($mul == 'M' ? 1048576 : ($mul == 'K' ? 1024 : ($mul == 'G' ? 1073741824 : 1)));
	 		if ($_SERVER['CONTENT_LENGTH'] > $mul * (int)$POST_MAX_SIZE && $POST_MAX_SIZE) {
				$json['errors'][] = $this->language->get('ms_error_file_size');
	 		} else {
	 			$json['errors'][] = $this->language->get('ms_error_file_upload_error');
	 			
	 			
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
				
				if ($file['type'] == 'application/pdf' && $this->config->get('msconf_enable_pdf_generator') && extension_loaded('imagick')) {
					$im = new imagick(DIR_IMAGE . $image->getTmpPath() . $name);
					$pages = $im->getNumberImages() - 1;		
				} else {
					$pages = 0;
				}
				
				$json['file'] = array(
					'name' => $file['name'],
					'src' => $name,
					'pages' => $pages
				);
			}			
		}
		
		$this->_setJsonResponse($json);
	}
	
	public function jxGenerateImages() {
		if (!$this->config->get('msconf_enable_pdf_generator') || !extension_loaded('imagick'))
			return;
			
		$data = $this->request->post;
		$json = array();
		if (isset($data['product_downloads'][0])) {
			$dl = MsImage::byName($this->registry, $data['product_downloads'][0]);
			if (!$dl->checkFileAgainstSession()) {
				$json['errors']['product_download'] = $dl->getErrors();
			} else {
				if (preg_match('/[^-0-9,]/', $data['pages'])) {
					$json['errors']['product_download'] = $this->language->get('ms_error_product_invalid_pdf_range');
				} else {
					$offsets = explode(',',$data['pages']);
					foreach ($offsets as $offset) {
						if (!preg_match('/^[0-9]+(-[0-9]+)?$/', $offset)) {
							$json['errors']['product_download'] = $this->language->get('ms_error_product_invalid_pdf_range');
							break;
						}
					}
				}

				if (!empty($json['errors'])) {
					$this->_setJsonResponse($json);
					return;
				}
				
				$pathinfo = pathinfo(DIR_IMAGE . $dl->getTmpPath() . $data['product_downloads'][0]);
				$list = glob(DIR_IMAGE . $dl->getTmpPath() . $pathinfo['filename'] . '*\.png');
				//var_dump($list);
				foreach ($list as $pagePreview) {
					//var_dump('unlinking ' . $pagePreview);
					@unlink($pagePreview);
				}
				//return;
				$name = DIR_IMAGE . $dl->getTmpPath() . $data['product_downloads'][0] . "[" . $data['pages'] . "]";
				$im = new imagick($name);
				$pages = $im->getNumberImages();

				$im->setImageFormat( "png" );
				$im->setImageCompressionQuality(100);

				$pathinfo = pathinfo(DIR_IMAGE . $dl->getTmpPath() . $data['product_downloads'][0]);
				$json['token'] = substr($pathinfo['basename'], 0, strrpos($pathinfo['basename'], '.'));
				
				if ($im->writeImages(DIR_IMAGE . $dl->getTmpPath() . $pathinfo['filename'] . '.png', false)) {
					$list = glob(DIR_IMAGE . $dl->getTmpPath() . $pathinfo['filename'] . '*\.png');
					foreach ($list as $pagePreview) {
						/*
						var_dump($pagePreview,preg_match('/[0-9]+x[0-9]+\.png$/',$pagePreview));
						if (preg_match('/[0-9]+x[0-9]+\.png$/',$pagePreview)) {
							
							echo 'next ' . $pagePreview;
							continue;
						}
						*/
						$pathinfo = pathinfo($pagePreview);
						$this->session->data['multiseller']['files'][] = $pathinfo['basename'];
						$image = new MsImage($this->registry);
						$thumb = $image->resize($image->getTmpPath() . $pathinfo['basename'], $this->config->get('msconf_image_preview_width'), $this->config->get('msconf_image_preview_height'));
						$json['previews'][] = array(
							'name' => $pathinfo['basename'],
							'thumb' => $thumb
						);
					}
					//var_dump($this->session->data['multiseller']['files']);
					$this->_setJsonResponse($json);
				}
			}
			unset($dl);
		}
	}
	
	public function jxSaveProductDraft() {
		$data = $this->request->post;
		
		if (isset($data['product_id']) && !empty($data['product_id'])) {
			if  ($this->msProduct->productOwnedBySeller($data['product_id'], $this->customer->getId())) {
				$product = $this->msProduct->getProduct($data['product_id']);
				$data['images'] = $this->msProduct->getProductImages($data['product_id']);
			} else {
				return;
			}
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
		
		if (isset($data['product_downloads'])) {
			foreach ($data['product_downloads'] as &$download) {
				//str_replace($this->msSeller->getNickname() . '_', '', $download);
				//$download = substr_replace($download, '.' . $this->msSeller->getNickname() . '_', strpos($download,'.'), strlen('.'));				
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
			$data['product_thumbnail'] = array_shift($data['product_images']);
		}

		if (isset($data['product_category'])) {
			if (is_array($data['product_category'])) {
				if (!$this->config->get('msconf_allow_multiple_categories')) {
					$data['product_category'] = $data['product_category'][0];
				}
			} else {
				$data['product_category'] = array($data['product_category']);
			}
		} else {
			$data['product_category'] = array();			
		}
		
		$data['product_subtract'] = 0;
		if ($this->config->get('msconf_enable_shipping') == 1) { // enable shipping
			$data['product_enable_shipping'] = 1;
		} else if ($this->config->get('msconf_enable_shipping') == 2) { // seller select
		 	if  (!isset($data['product_enable_shipping']) || $data['product_enable_shipping'] != 1) {
		 		$data['product_enable_shipping'] = 0;
		 	} else {
		 		$data['product_enable_shipping'] = 1;
		 	}
		} else { // disable shipping
			$data['product_enable_shipping'] = 0;
		}
		
		if ($this->config->get('msconf_enable_quantities') == 1) { // enable quantities
			$data['product_quantity'] = (int)$data['product_quantity'];
			$data['product_subtract'] = 1;
		} else if ($this->config->get('msconf_enable_quantities') == 2) { // shipping dependent
			if ($this->config->get('msconf_enable_shipping') == 1) {
				$data['product_subtract'] = 1;
				if (!isset($data['product_quantity']))
					$data['product_quantity'] = 0;						
			} else if ($this->config->get('msconf_enable_shipping') == 2) {
				if (!$data['product_enable_shipping']) {
					$data['product_quantity'] = 999;
				} else {
					$data['product_subtract'] = 1;
					if (!isset($data['product_quantity']))
						$data['product_quantity'] = 0;
				}
			} else { // shipping disabled
				$data['product_quantity'] = 999;
			}
		} else { // disable quantities
			$data['product_quantity'] = 999;
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

		$seller = $this->msSeller->getSellerData($this->customer->getId());

		if (isset($data['product_id']) && !empty($data['product_id'])) {
			if  ($this->msProduct->productOwnedBySeller($data['product_id'], $this->customer->getId())) {
				$product = $this->msProduct->getProduct($data['product_id']);
				$data['images'] = $this->msProduct->getProductImages($data['product_id']);
			} else {
				return;
			}
		}
		
		$json = array();

		// only check default language for errors
		$i = 0;
		$default = 0;		
		foreach ($data['languages'] as $language_id => $language) {
			// main language inputs are mandatory
			if ($i == 0) {
				$default = $language_id;
				
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
				} else if (empty($language['product_name'])) {
					$data['languages'][$language_id]['product_name'] = $data['languages'][$default]['product_name'];
				}

				if (!empty($language['product_description']) && (mb_strlen($language['product_description']) < 25 || mb_strlen($language['product_description']) > 1000)) {
					$json['errors']['product_description_' . $language_id] = $this->language->get('ms_error_product_description_length');			
				} else if (empty($language['product_description'])) {
					$data['languages'][$language_id]['product_description'] = $data['languages'][$default]['product_description'];
				}
			}
			
			if (!empty($language['product_tags']) && mb_strlen($language['product_tags']) > 1000) {
				$json['errors']['product_tags_' . $language_id] = $this->language->get('ms_error_product_tags_length');			
			}

			$i++;
		}
		
		if (empty($data['product_price'])) {
			if ($data['product_price'] !== "0" || $this->config->get('msconf_allow_free_products') == 0) {
				$json['errors']['product_price'] = $this->language->get('ms_error_product_price_empty');
			}
		} else if (!is_numeric($data['product_price'])) {
			$json['errors']['product_price'] = $this->language->get('ms_error_product_price_invalid');
		} else if ($data['product_price'] < $this->config->get('msconf_minimum_product_price')) {
			$json['errors']['product_price'] = $this->language->get('ms_error_product_price_low');
		}		

		if (isset($data['product_downloads'])) {
			foreach ($data['product_downloads'] as &$download) {
				//str_replace($this->msSeller->getNickname() . '_', '', $download);
				//$download = substr_replace($download, '.' . $this->msSeller->getNickname() . '_', strpos($download,'.'), strlen('.'));
				$dl = MsImage::byName($this->registry, $download);
				if (!$dl->checkFileAgainstSession()) {
					$json['errors']['product_download'] = $dl->getErrors();
				}
				unset($dl);
			}
		} else {
			$json['errors']['product_download'] = $this->language->get('ms_error_product_download_empty');
		}
		
		$msconf_images_limits = explode(',',$this->config->get('msconf_images_limits'));
		if (!isset($data['product_images'])) {
			if ($msconf_images_limits[0] > 0) {
				$json['errors']['product_image'] = sprintf($this->language->get('ms_error_product_image_count'),$msconf_images_limits[0]);
			}			
		} else {
			if ($msconf_images_limits[1] > 0 && count($data['product_images']) > $msconf_images_limits[1]) {
				$json['errors']['product_image'] = sprintf($this->language->get('ms_error_product_image_maximum'),$msconf_images_limits[1]);
			} else if ($msconf_images_limits[0] > 0 && count($data['product_images']) < $msconf_images_limits[0]) {
				$json['errors']['product_image'] = sprintf($this->language->get('ms_error_product_image_count'), $msconf_images_limits[0]);
			} else {
				foreach ($data['product_images'] as $image) {
					$img = MsImage::byName($this->registry, $image);
					if (!$img->checkFileAgainstSession()) {
						$json['errors']['product_image'] = $img->getErrors();
					}
					unset($img);
				}
				
				$data['product_thumbnail'] = array_shift($data['product_images']);
			}
		}
		
		if (!empty($data['product_message']) && mb_strlen($data['product_message']) > 1000) {
			$json['errors']['product_message'] = $this->language->get('ms_error_product_message_length');			
		}		
		
		if (isset($data['product_category'])) {
			if (is_array($data['product_category'])) {
				if (!$this->config->get('msconf_allow_multiple_categories')) {
					$data['product_category'] = $data['product_category'][0];
				}
			} else {
				$data['product_category'] = array($data['product_category']);
			}
		} else {
			$json['errors']['product_category'] = $this->language->get('ms_error_product_category_empty'); 		
		}
		

		if (isset($data['product_attributes'])) {
			$product_attributes = $data['product_attributes'];
			unset($data['product_attributes']);
						
			foreach ($this->msProduct->getOptions(array('option_ids' => $this->config->get('msconf_product_options'))) as $option) {
				$options[$option['option_id']] = $option;
				$options[$option['option_id']]['values'] = $this->msProduct->getOptionValues($option['option_id']);
			}
			foreach ($product_attributes as $option_id => $attr) {
				if (!isset($options[$option_id])) continue;

				// @TODO check for correct value id
				if ($options[$option_id]['type'] == 'select' || $options[$option_id]['type'] == 'radio') {
					if ((int)$attr != 0) {
						$data['product_attributes'][$option_id] = array(
							'type' => $options[$option_id]['type'],
							'value' => (int)$attr
						);
					}
				} else if ($options[$option_id]['type'] == 'checkbox') {
					foreach ($attr as $key => $option_value_id) {
						if ((int)$option_value_id != 0) {
							$data['product_attributes'][$option_id]['type']  = $options[$option_id]['type'];
							$data['product_attributes'][$option_id]['values'][]  = (int)$option_value_id;
						}
					} 
				}
			}
		}	
		
		$data['product_subtract'] = 0;
		if ($this->config->get('msconf_enable_shipping') == 1) { // enable shipping
			$data['product_enable_shipping'] = 1;
		} else if ($this->config->get('msconf_enable_shipping') == 2) { // seller select
		 	if  (!isset($data['product_enable_shipping']) || $data['product_enable_shipping'] != 1) {
		 		$data['product_enable_shipping'] = 0;
		 	} else {
		 		$data['product_enable_shipping'] = 1;
		 	}
		} else { // disable shipping
			$data['product_enable_shipping'] = 0;
		}
		
		if ($this->config->get('msconf_enable_quantities') == 1) { // enable quantities
			$data['product_quantity'] = (int)$data['product_quantity'];
			$data['product_subtract'] = 1;
		} else if ($this->config->get('msconf_enable_quantities') == 2) { // shipping dependent
			if ($this->config->get('msconf_enable_shipping') == 1) {
				$data['product_subtract'] = 1;
				if (!isset($data['product_quantity']))
					$data['product_quantity'] = 0;						
			} else if ($this->config->get('msconf_enable_shipping') == 2) {
				if (!$data['product_enable_shipping']) {
					$data['product_quantity'] = 999;
				} else {
					$data['product_subtract'] = 1;
					if (!isset($data['product_quantity']))
						$data['product_quantity'] = 0;
				}
			} else { // shipping disabled
				$data['product_quantity'] = 999;
			}
		} else { // disable quantities
			$data['product_quantity'] = 999;
		}

		if (empty($json['errors'])) {
			$mails = array();
			// set product status
			switch ($seller['product_validation']) {
				case MsProduct::MS_PRODUCT_VALIDATION_APPROVAL:
					$data['enabled'] = 0;
					$data['review_status_id'] = MsProduct::MS_PRODUCT_STATUS_PENDING;
					
					if (isset($data['product_id']) && !empty($data['product_id'])) {
						$request_type = MsRequest::MS_REQUEST_PRODUCT_CREATED;
					} else {
						$request_type = MsRequest::MS_REQUEST_PRODUCT_UPDATED;
					}
					
					if (!isset($data['product_id']) || empty($data['product_id']) || ($product['review_status_id'] == MsProduct::MS_PRODUCT_STATUS_DRAFT)) {
						$mails[] = array(
							'type' => MsMail::SMT_PRODUCT_AWAITING_MODERATION
						);
						$mails[] = array(
							'type' => MsMail::AMT_NEW_PRODUCT_AWAITING_MODERATION,
							'data' => array(
								'message' => $data['product_message']
							)
						);
					} else {
						$mails[] = array(
							'type' => MsMail::SMT_PRODUCT_AWAITING_MODERATION
						);
						$mails[] = array(
							'type' => MsMail::AMT_EDIT_PRODUCT_AWAITING_MODERATION,
							'data' => array(
								'message' => $data['product_message']
							)
						);						
					}
					break;
					
				case MsProduct::MS_PRODUCT_VALIDATION_NONE:
				default:
					$data['enabled'] = 1;
					$data['review_status_id'] = MsProduct::MS_PRODUCT_STATUS_APPROVED;
					
					if (!isset($data['product_id']) || empty($data['product_id'])) {		
						$mails[] = array(
							'type' => MsMail::AMT_PRODUCT_CREATED
						);
					} else {
						// product edited mail if needed
					}
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
			
			foreach ($mails as &$mail) {
				$mail['data']['product_id'] = $product_id;
			}
			
			$this->msMail->sendMails($mails);
			
			$json['redirect'] = $this->url->link('account/ms-seller/products', '', 'SSL');
		}

		$this->_setJsonResponse($json);
	}

	public function jxRequestMoney() {
		$msTransaction = new MsTransaction($this->registry);
		$data = $this->request->post;

		$seller = $this->msSeller->getSellerData($this->customer->getId());
		
		$balance = $this->msSeller->getBalanceForSeller($this->customer->getId());
		$json = array();
		
		if (!$this->msSeller->getPaypal()) {
			$json['errors']['withdraw_amount'] = $this->language->get('ms_account_withdraw_no_paypal');
			$this->_setJsonResponse($json);
			return;
		}
		
		if (!isset($data['withdraw_amount'])) {
			$data['withdraw_amount'] = $balance;
		}
		
		if (preg_match("/[^0-9.]/",$data['withdraw_amount']) || (float)$data['withdraw_amount'] <= 0) {
			$json['errors']['withdraw_amount'] = $this->language->get('ms_error_withdraw_amount');
		} else {
			$data['withdraw_amount'] = (float)$data['withdraw_amount'];
			if ($data['withdraw_amount'] > $balance) {
				$json['errors']['withdraw_amount'] = $this->language->get('ms_error_withdraw_balance');
			} else if ($data['withdraw_amount'] < $this->config->get('msconf_minimum_withdrawal_amount')) {
				$json['errors']['withdraw_amount'] = $this->language->get('ms_error_withdraw_minimum');
			}			
		}
		
		if (empty($json['errors'])) {
			$transaction = array(
				'parent_transaction_id' => 0,
				'order_id' => 0,
				'product_id' => 0,
				'seller_id' => $this->customer->getId(),
				'amount' => -1*($data['withdraw_amount']),
				'currency_id' => '',
				'currency_code' =>'',
				'currency_value' =>'',
				'commission' =>'',
				'commission_flat' =>'',
				'description' => sprintf($this->language->get('ms_transaction_pending_withdrawal'),$this->currency->format($data['withdraw_amount'], $this->config->get('config_currency')))
			);
			
			$transaction_id = $msTransaction->addTransaction($transaction);				
			
			$r = new MsRequest($this->registry);
			$r->createRequest(array(
				'seller_id' => $this->customer->getId(),
				'transaction_id' => $transaction_id,
				'request_type' => MsRequest::MS_REQUEST_WITHDRAWAL,
			));
			
			$mails[] = array(
				'type' => MsMail::SMT_WITHDRAW_REQUEST_SUBMITTED
			);
			$mails[] = array(
				'type' => MsMail::AMT_WITHDRAW_REQUEST_SUBMITTED,
			);
			
			$this->msMail->sendMails($mails);
			
			$this->session->data['success'] = $this->language->get('ms_request_submitted');
			$json['redirect'] = $this->url->link('account/ms-seller/transactions', '', 'SSL');
		}
		$this->_setJsonResponse($json);
	}
	
	public function jxSaveSellerInfo() {
		$data = $this->request->post;
		$seller = $this->msSeller->getSellerData($this->customer->getId());
		$json = array();
		
		if (!empty($seller) && ($seller['seller_status_id'] != MsSeller::MS_SELLER_STATUS_ACTIVE)) {
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
			} else if ($this->msSeller->nicknameTaken($data['sellerinfo_nickname'])) {
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
			$mails = array();
			if (empty($seller)) {
				// create new seller
				switch ($this->config->get('msconf_seller_validation')) {
					/*
					case MS_SELLER_VALIDATION_ACTIVATION:
						$data['seller_status_id'] = MsSeller::MS_SELLER_STATUS_TOBEACTIVATED;
						break;
					*/
					
					case MS_SELLER_VALIDATION_APPROVAL:
						$mails[] = array(
							'type' => MsMail::SMT_SELLER_ACCOUNT_AWAITING_MODERATION
						);
						$mails[] = array(
							'type' => MsMail::AMT_SELLER_ACCOUNT_AWAITING_MODERATION,
							'data' => array(
								'message' => $data['sellerinfo_reviewer_message']
							)
						);
						$data['seller_status_id'] = MsSeller::MS_SELLER_STATUS_TOBEAPPROVED;

						$r = new MsRequest($this->registry);
						$r->createRequest(array(
							'seller_id' => $this->customer->getId(),
							'request_type' => MsRequest::MS_REQUEST_SELLER_CREATED,
						));
						unset($r);
						break;
					
					case MS_SELLER_VALIDATION_NONE:
					default:
						$mails[] = array(
							'type' => MsMail::SMT_SELLER_ACCOUNT_CREATED
						);
						$mails[] = array(
							'type' => MsMail::AMT_SELLER_ACCOUNT_CREATED
						);					
						$data['seller_status_id'] = MsSeller::MS_SELLER_STATUS_ACTIVE;
						break;
				}
				
				$data['seller_id'] = $this->customer->getId();
				$data['sellerinfo_product_validation'] = $this->config->get('msconf_product_validation'); 
				$this->msSeller->createSeller($data);
				$this->msMail->sendMails($mails);
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
		$this->load->model('catalog/category');
		$this->document->addScript('catalog/view/javascript/jquery.form.js');
		$this->data['seller'] = $this->msSeller->getSellerData($this->customer->getId());
		
		if (!$this->config->get('msconf_allow_multiple_categories'))
			$this->data['categories'] = $this->msProduct->getCategories();		
		else
			$this->data['categories'] = $this->msProduct->getMultipleCategories(0);

//

		$this->data['options'] = array();
		$options = $this->msProduct->getOptions(array('option_ids' => $this->config->get('msconf_product_options')));
		foreach ($options as $option) {
			$option_values = $this->msProduct->getOptionValues($option['option_id']);
			$option['values'] = $option_values;
			$this->data['options'][] = $option;
		}
//

		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		$this->data['product'] = FALSE;
		$this->data['msconf_allow_multiple_categories'] = $this->config->get('msconf_allow_multiple_categories');
		$this->data['msconf_enable_shipping'] = $this->config->get('msconf_enable_shipping');
		$this->data['msconf_required_images'] = $this->config->get('msconf_required_images');
		$this->data['msconf_enable_quantities'] = $this->config->get('msconf_enable_quantities');
		
		$this->data['back'] = $this->url->link('account/ms-seller/products', '', 'SSL');
		$this->data['heading'] = $this->language->get('ms_account_newproduct_heading');
		$this->document->setTitle($this->language->get('ms_account_newproduct_heading'));
		$this->_setBreadcrumbs('ms_account_newproduct_breadcrumbs', __FUNCTION__);
		$this->_renderTemplate('ms-account-product-form');
	}
	
	public function products() {
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
		
		$products = $this->msSeller->getSellerProducts($seller_id, $sort);
		
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
		$pagination->total = $this->msSeller->getTotalSellerProducts($seller_id);
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
		$this->load->model('tool/image');
		$this->load->model('catalog/category');
		$this->document->addScript('catalog/view/javascript/jquery.form.js');
		$this->data['seller'] = $this->msSeller->getSellerData($this->customer->getId());
		
		
		
		if (!$this->config->get('msconf_allow_multiple_categories'))
			$this->data['categories'] = $this->msProduct->getCategories();		
		else
			$this->data['categories'] = $this->msProduct->getMultipleCategories(0);

		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();		
		
		$product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;
		$seller_id = $this->customer->getId();
		
		if  ($this->msProduct->productOwnedBySeller($product_id,$seller_id)) {
    		$product = $this->msProduct->getProduct($product_id);
		} else {
			$product = NULL;
		}

		if (!$product['product_id']) {
			$this->redirect($this->url->link('account/ms-seller/products', '', 'SSL'));
		} else {
			$this->data['options'] = array();
			$options = $this->msProduct->getOptions(array('option_ids' => $this->config->get('msconf_product_options')));
			foreach ($options as $option) {
				$option_values = $this->msProduct->getOptionValues($option['option_id']);
				$option['values'] = $option_values;
				$this->data['options'][] = $option;
			}
			
			$this->data['product_attributes'] = $this->msProduct->getProductAttributes($product_id);
			
			if (!empty($product['thumbnail'])) {
				$img = MsImage::byName($this->registry, $product['thumbnail']);
				$product['images'][] = array(
					'name' => $product['thumbnail'],
					'thumb' => $img->resize($product['thumbnail'], $this->config->get('msconf_image_preview_width'), $this->config->get('msconf_image_preview_height'))
				);
				$this->session->data['multiseller']['files'][] = $product['thumbnail'];
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
			$this->data['msconf_allow_multiple_categories'] = $this->config->get('msconf_allow_multiple_categories');
			$this->data['msconf_enable_shipping'] = $this->config->get('msconf_enable_shipping');
			$this->data['msconf_enable_quantities'] = $this->config->get('msconf_enable_quantities');
			
			$this->data['msconf_required_images'] = $this->config->get('msconf_required_images');
			
		$this->data['back'] = $this->url->link('account/ms-seller/products', '', 'SSL');						
			$this->data['heading'] = $this->language->get('ms_account_editproduct_heading');
			$this->document->setTitle($this->language->get('ms_account_editproduct_heading'));		
			$this->_setBreadcrumbs('ms_account_editproduct_breadcrumbs', __FUNCTION__);		
			$this->_renderTemplate('ms-account-product-form');
		}
	}
	
	public function deleteProduct() {
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
				case MsSeller::MS_SELLER_STATUS_TOBEACTIVATED:
					$this->data['statustext'] = $this->language->get('ms_account_status') . $this->language->get('ms_account_status_activation');
					break;
				case MsSeller::MS_SELLER_STATUS_TOBEAPPROVED:
					$this->data['statustext'] = $this->language->get('ms_account_status') . $this->language->get('ms_account_status_approval');
					break;
				case MsSeller::MS_SELLER_STATUS_DISABLED:
					$this->data['statustext'] = $this->language->get('ms_account_status') . $this->language->get('ms_account_status_disabled');
					break;					
				case MsSeller::MS_SELLER_STATUS_ACTIVE:
				default:
					//$this->data['statustext'] = $this->language->get('ms_account_status') . $this->language->get('ms_account_status_active');
					//$this->data['statustext'] .= '<br />' . $this->language->get('ms_account_status_fullaccess');
					break;
			}
		} else { 		
			$this->data['seller'] = FALSE;
			$this->data['statustext'] = $this->language->get('ms_account_status_please_fill_in');			
		}

		$this->data['seller_validation'] = $this->config->get('msconf_seller_validation');
		$this->data['back'] = $this->url->link('account/account', '', 'SSL');
		$this->document->setTitle($this->language->get('ms_account_sellerinfo_heading'));
		$this->_setBreadcrumbs('ms_account_sellerinfo_breadcrumbs', __FUNCTION__);		
		$this->_renderTemplate('ms-account-sellerinfo');
	}
	/* ********************* */
	
	
	public function transactions() {
		$msTransaction = new MsTransaction($this->registry);
		
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
		$this->data['balance'] =  $this->currency->format($this->msSeller->getBalanceForSeller($seller_id),$this->config->get('config_currency'));
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
		$seller_id = $this->customer->getId();
		$this->data['balance'] =  $this->msSeller->getBalanceForSeller($seller_id);
		$this->data['balance_formatted'] =  $this->currency->format($this->msSeller->getBalanceForSeller($seller_id),$this->config->get('config_currency'));
		$this->data['paypal'] =  $this->msSeller->getPaypal();
		$this->data['msconf_minimum_withdrawal_amount'] =  $this->currency->format($this->config->get('msconf_minimum_withdrawal_amount'),$this->config->get('config_currency'));
		$this->data['msconf_allow_partial_withdrawal'] = $this->config->get('msconf_allow_partial_withdrawal');
		$this->data['msconf_allow_withdrawal_requests'] = $this->config->get('msconf_allow_withdrawal_requests');
		$this->data['currency_code'] = $this->config->get('config_currency');
		
		if ($this->msSeller->getBalanceForSeller($seller_id) - $this->config->get('msconf_minimum_withdrawal_amount') > 0) {
			$this->data['withdrawal_minimum_reached'] = TRUE;
		} else {
			$this->data['withdrawal_minimum_reached'] = FALSE;
		}
			
		$this->data['back'] = $this->url->link('account/account', '', 'SSL');
		$this->document->setTitle($this->language->get('ms_account_withdraw_heading'));
		$this->_setBreadcrumbs('ms_account_withdraw_breadcrumbs', __FUNCTION__);		
		$this->_renderTemplate('ms-account-withdraw');
	}
	
	/*
	public function test() {
		$msMail = new MsMail($this->registry);
		$msMail->sendMail(MsMail::SMT_PRODUCT_AWAITING_MODERATION, array('product_id' => 76, 'message' => "We don't like it, lol\n Deal with it"));
	}
	*/
}
?>
