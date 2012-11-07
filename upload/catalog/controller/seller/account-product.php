<?php

class ControllerSellerAccountProduct extends ControllerSellerAccount {
	public function jxUpdateFile() {
		$json = array();
		$json['errors'] = $this->MsLoader->MsFile->checkPostMax($_POST, $_FILES);

		if ($json['errors']) {
			return $this->response->setOutput(json_encode($json));
		}
		
		if (isset($this->request->post['file_id']) && isset($this->request->post['product_id'])) {
			$download_id = (int)substr($this->request->post['file_id'], strrpos($this->request->post['file_id'], '-')+1);
			$product_id = (int)$this->request->post['product_id'];
			$seller_id = $this->customer->getId();
			if  ($this->MsLoader->MsProduct->productOwnedBySeller($product_id,$seller_id) && $this->MsLoader->MsProduct->hasDownload($product_id,$download_id)) {
				$file = array_shift($_FILES);
				$errors = $this->MsLoader->MsFile->checkDownload($file);
				
				if ($errors) {
					$json['errors'] = array_merge($json['errors'], $errors);
				} else {
					$fileData = $this->MsLoader->MsFile->uploadDownload($file);
					$json['fileName'] = $fileData['fileName'];
					$json['fileMask'] = $fileData['fileMask'];
				}
			}
		}
			
		return $this->response->setOutput(json_encode($json));
	}
	
	public function jxUploadSellerAvatar() {
		$json = array();
		$file = array();
		
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
				$thumbUrl = $this->MsLoader->MsFile->resizeImage($this->config->get('msconf_temp_image_path') . $fileName, $this->config->get('msconf_image_preview_width'), $this->config->get('msconf_image_preview_height'));
				$json['files'][] = array(
					'name' => $fileName,
					'thumb' => $thumbUrl
				);
			}
		}
		
		return $this->response->setOutput(json_encode($json));
	}
	
	public function jxUploadImages() {
		$json = array();
		$file = array();
		$json['errors'] = $this->MsLoader->MsFile->checkPostMax($_POST, $_FILES);

		if ($json['errors']) {
			return $this->response->setOutput(json_encode($json));
		}

		// allow a maximum of N images
		$msconf_images_limits = $this->config->get('msconf_images_limits');
		foreach ($_FILES as $file) {
			if ($msconf_images_limits[1] > 0 && $this->request->post['imageCount'] >= $msconf_images_limits[1]) {
				$json['errors'][] = sprintf($this->language->get('ms_error_product_image_maximum'),$msconf_images_limits[1]);
				$json['cancel'] = 1;
				$this->response->setOutput(json_encode($json));
				return;
			} else {
				$errors = $this->MsLoader->MsFile->checkImage($file);
				
				if ($errors) {
					$json['errors'] = array_merge($json['errors'], $errors);
				} else {
					$fileName = $this->MsLoader->MsFile->uploadImage($file);
					$thumbUrl = $this->MsLoader->MsFile->resizeImage($this->config->get('msconf_temp_image_path') . $fileName, $this->config->get('msconf_image_preview_width'), $this->config->get('msconf_image_preview_height'));
					$json['files'][] = array(
						'name' => $fileName,
						'thumb' => $thumbUrl
					);
				}
			}
		}
		
		return $this->response->setOutput(json_encode($json));
	}
	
	public function jxUploadDownloads() {
		$json = array();
		$file = array();
		
		$json['errors'] = $this->MsLoader->MsFile->checkPostMax($_POST, $_FILES);

		if ($json['errors']) {
			return $this->response->setOutput(json_encode($json));
		}

		// allow a maximum of N images
		$msconf_downloads_limits = $this->config->get('msconf_downloads_limits');
		foreach ($_FILES as $file) {
			if ($msconf_downloads_limits[1] > 0 && $this->request->post['downloadCount'] >= $msconf_downloads_limits[1]) {
				$json['errors'][] = sprintf($this->language->get('ms_error_product_download_maximum'),$msconf_downloads_limits[1]);
				$json['cancel'] = 1;
				$this->response->setOutput(json_encode($json));
				return;
			} else {
				$errors = $this->MsLoader->MsFile->checkDownload($file);
				
				if ($errors) {
					$json['errors'] = array_merge($json['errors'], $errors);
				} else {
					$fileData = $this->MsLoader->MsFile->uploadDownload($file);
					
					if ($this->config->get('msconf_enable_pdf_generator') && extension_loaded('imagick')) {
						$ext = explode('.', $file['name']); $ext = end($ext);
						if (strtolower($ext) == 'pdf') {
							$im = new imagick(DIR_IMAGE . $this->config->get('msconf_temp_image_path') . $fileData['fileName']);
							$pages = $im->getNumberImages() - 1;
						}
					}

					$json['files'][] = array (
						'fileName' => $fileData['fileName'],
						'fileMask' => $fileData['fileMask'],
						'filePages' => isset($pages) ? $pages : ''
					);
				}
			}
		}
		
		return $this->response->setOutput(json_encode($json));
	}
	
	public function jxSubmitProduct() {
		$data = $this->request->post;
		$seller = $this->MsLoader->MsSeller->getSeller($this->customer->getId());

		if (isset($data['product_id']) && !empty($data['product_id'])) {
			if  ($this->MsLoader->MsProduct->productOwnedBySeller($data['product_id'], $this->customer->getId())) {
				$product = $this->MsLoader->MsProduct->getProduct($data['product_id']);
				$data['images'] = $this->MsLoader->MsProduct->getProductImages($data['product_id']);
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

		$msconf_downloads_limits = $this->config->get('msconf_downloads_limits');
		if (!isset($data['product_downloads'])) {
			if ($msconf_downloads_limits[0] > 0) {
				$json['errors']['product_download'] = sprintf($this->language->get('ms_error_product_download_count'),$msconf_downloads_limits[0]);
			}			
		} else {
			if ($msconf_downloads_limits[1] > 0 && count($data['product_downloads']) > $msconf_downloads_limits[1]) {
				$json['errors']['product_download'] = sprintf($this->language->get('ms_error_product_download_maximum'),$msconf_downloads_limits[1]);
			} else if ($msconf_downloads_limits[0] > 0 && count($data['product_downloads']) < $msconf_downloads_limits[0]) {
				$json['errors']['product_download'] = sprintf($this->language->get('ms_error_product_download_count'), $msconf_downloads_limits[0]);
			} else {
				foreach ($data['product_downloads'] as $key => $download) {
					if (!empty($download['filename'])) {
						if (!$this->MsLoader->MsFile->checkFileAgainstSession($download['filename'])) {
							//var_dump($download);
							$json['errors']['product_download'] = $this->language->get('ms_error_file_upload_error');
						}						
					} else if (!empty($download['download_id']) && !empty($product['product_id'])) {
						if (!$this->MsLoader->MsProduct->hasDownload($product['product_id'],$download['download_id'])) {
							//var_dump($download);
							$json['errors']['product_download'] = $this->language->get('ms_error_file_upload_error');
						}
					} else {
						unset($data['product_downloads'][$key]);	
					}
					//str_replace($this->MsLoader->MsSeller->getNickname() . '_', '', $download);
					//$download = substr_replace($download, '.' . $this->MsLoader->MsSeller->getNickname() . '_', strpos($download,'.'), strlen('.'));
				}
			}
		}
		
		$msconf_images_limits = $this->config->get('msconf_images_limits');
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
					if (!$this->MsLoader->MsFile->checkFileAgainstSession($image)) {
						$json['errors']['product_image'] = $this->language->get('ms_error_file_upload_error');
					}
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
						
			foreach ($this->MsLoader->MsProduct->getOptions(array('option_ids' => $this->config->get('msconf_product_options'))) as $option) {
				$options[$option['option_id']] = $option;
				$options[$option['option_id']]['values'] = $this->MsLoader->MsProduct->getOptionValues($option['option_id']);
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
		
		// SEO urls generation for products
		if ($this->config->get('msconf_enable_seo_urls')) {
			$latin_check = '/[^\x{0030}-\x{007f}]/u';
			$non_latin_chars = preg_match($latin_check, $_POST['full_name']);
			if ($this->config->get('msconf_enable_non_alphanumeric_seo') && $non_latin_chars) {
				$data['keyword'] = implode("-", str_replace("-", "", explode(" ", strtolower($language['product_name']))));
			}
			else {
				$data['keyword'] = implode("-", str_replace("-", "", explode(" ", preg_replace("/[^A-Za-z0-9 ]/", '', strtolower($language['product_name'])))));
			}
		}

		if (empty($json['errors'])) {
			$mails = array();
			// set product status
			switch ($seller['ms.product_validation']) {
				case MsProduct::MS_PRODUCT_VALIDATION_APPROVAL:
					$data['enabled'] = 0;
					$data['product_status'] = MsProduct::STATUS_INACTIVE;
					$data['product_approved'] = 0;
					if (isset($data['product_id']) && !empty($data['product_id'])) {
						//$request_type = MsRequestProduct::TYPE_PRODUCT_UPDATE;
					} else {
						//$request_type = MsRequestProduct::TYPE_PRODUCT_CREATE;
					}
					
					if (!isset($data['product_id']) || empty($data['product_id'])) {
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
					$data['product_status'] = MsProduct::STATUS_ACTIVE;
					$data['product_approved'] = 1;
					
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
				$product_id = $this->MsLoader->MsProduct->editProduct($data);
				$this->session->data['success'] = $this->language->get('ms_success_product_updated');
			} else {
				$product_id = $this->MsLoader->MsProduct->saveProduct($data);
				$this->session->data['success'] = $this->language->get('ms_success_product_created');
			}

			foreach ($mails as &$mail) {
				$mail['data']['product_id'] = $product_id;
			}
			
			$this->MsLoader->MsMail->sendMails($mails);
			
			$json['redirect'] = $this->url->link('seller/account-product', '', 'SSL');
		}

		$this->response->setOutput(json_encode($json));
	}

  	public function jxSubmitPdfgenDialog() {
		$json = array();

		if (!$this->config->get('msconf_enable_pdf_generator') || !extension_loaded('imagick'))
			return;
			
		$data = $this->request->post;
		
		$json = $this->MsLoader->MsFile->generatePdfImages($this->request->post['ms-pdfgen-filename'], $this->request->post['ms-pdfgen-pages']);
		return $this->response->setOutput(json_encode($json));
  	}
  	
  	public function jxRenderPdfgenDialog() {
		if (!$this->config->get('msconf_enable_pdf_generator') || !extension_loaded('imagick'))
			return;  		
  		
  		if (!empty($this->request->post['fileName'])) {
  			$fileName = $this->request->post['fileName'];
			$this->data['fileMask'] = substr($fileName,strpos($fileName,'.')+1,mb_strlen($fileName));
  		} else {
  			return;
  		}

  		$pages = $this->MsLoader->MsFile->getPdfPages($fileName);
  		
  		if ($pages == 0)
  			return;
  		
		$this->data['fileName'] = $fileName;		
		$this->data['filePages'] = $pages;

		list($this->template, $this->children) = $this->MsLoader->MsHelper->loadTemplate('dialog-pdf');
		return $this->response->setOutput($this->render());
  	}
	
	public function create() {
		$this->load->model('catalog/category');
		$this->document->addScript('catalog/view/javascript/jquery.uploadify.js');
		if ($this->config->get('msconf_enable_pdf_generator') && extension_loaded('imagick')) {
			$this->document->addScript('catalog/view/javascript/dialog-pdf.js');
		}		
		
		$this->document->addScript('catalog/view/javascript/account-product-form.js');
		
		$this->data['seller'] = $this->MsLoader->MsSeller->getSeller($this->customer->getId());
		$this->data['salt'] = $this->MsLoader->MsSeller->getSalt($this->customer->getId());
		if (!$this->config->get('msconf_allow_multiple_categories'))
			$this->data['categories'] = $this->MsLoader->MsProduct->getCategories();		
		else
			$this->data['categories'] = $this->MsLoader->MsProduct->getMultipleCategories(0);

//
		$this->data['options'] = array();
		$options = $this->MsLoader->MsProduct->getOptions(array('option_ids' => $this->config->get('msconf_product_options')));
		foreach ($options as $option) {
			$option_values = $this->MsLoader->MsProduct->getOptionValues($option['option_id']);
			$option['values'] = $option_values;
			$this->data['options'][] = $option;
		}
//

		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
		$this->data['product_attributes'] = FALSE;
		$this->data['product'] = FALSE;
		$this->data['msconf_allow_multiple_categories'] = $this->config->get('msconf_allow_multiple_categories');
		$this->data['msconf_enable_shipping'] = $this->config->get('msconf_enable_shipping');
		$this->data['msconf_images_limits'] = $this->config->get('msconf_images_limits');
		$this->data['msconf_downloads_limits'] = $this->config->get('msconf_downloads_limits');
		$this->data['msconf_enable_quantities'] = $this->config->get('msconf_enable_quantities');
		$this->data['ms_account_product_download_note'] = sprintf($this->language->get('ms_account_product_download_note'), $this->config->get('msconf_allowed_download_types'));
		$this->data['ms_account_product_image_note'] = sprintf($this->language->get('ms_account_product_image_note'), $this->config->get('msconf_allowed_image_types'));		
		
		$this->data['back'] = $this->url->link('seller/account-product', '', 'SSL');
		$this->data['heading'] = $this->language->get('ms_account_newproduct_heading');
		$this->document->setTitle($this->language->get('ms_account_newproduct_heading'));
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_products_breadcrumbs'),
				'href' => $this->url->link('seller/account-product', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_newproduct_breadcrumbs'),
				'href' => $this->url->link('seller/account-product/create', '', 'SSL'),
			)
		));
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->loadTemplate('account-product-form');
		$this->response->setOutput($this->render());
	}
	
	public function index() {
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
		$seller_id = $this->customer->getId();
		
		$products = $this->MsLoader->MsProduct->getProducts(
			array(
				'seller_id' => $seller_id,
				'language_id' => $this->config->get('config_language_id'),
				'product_status' => array(MsProduct::STATUS_ACTIVE, MsProduct::STATUS_INACTIVE, MsProduct::STATUS_DISABLED)
			),
			array(
				'order_by'  => 'date_added',
				'order_way' => 'DESC',
				'offset' => ($page - 1) * 5,
				'limit' => 5
			)
		);
		
		foreach ($products as $product) {
			$links = array();
			
			if ($product['mp.product_status'] != MsProduct::STATUS_DISABLED) {
				if ($product['mp.product_status'] == MsProduct::STATUS_ACTIVE)
					$links['view'] = $this->url->link('product/product', 'product_id=' . $product['product_id'], 'SSL');
	
				if ($product['mp.product_approved']) {
					if ($product['mp.product_status'] == MsProduct::STATUS_INACTIVE)
						$links['publish'] = $this->url->link('seller/account-product/publish', 'product_id=' . $product['product_id'], 'SSL');
		
					if ($product['mp.product_status'] == MsProduct::STATUS_ACTIVE)
						$links['unpublish'] = $this->url->link('seller/account-product/unpublish', 'product_id=' . $product['product_id'], 'SSL');
				}
				
				$links['edit'] = $this->url->link('seller/account-product/update', 'product_id=' . $product['product_id'], 'SSL');
				$links['delete'] = $this->url->link('seller/account-product/delete', 'product_id=' . $product['product_id'], 'SSL');
			}
			
			$this->data['products'][] = Array(
				'pd.name' => $product['pd.name'],  
				'mp.number_sold' => $product['mp.number_sold'],
				'mp.product_status' => $product['mp.product_status'],
				'status_text' => $this->MsLoader->MsProduct->getStatusText($product['mp.product_status']),
				'p.date_created' => date($this->language->get('date_format_short'), strtotime($product['p.date_created'])),
				'view_link' => isset($links['view']) ? $links['view'] : NULL,
				'publish_link' => isset($links['publish']) ? $links['publish'] : NULL,
				'unpublish_link' => isset($links['unpublish']) ? $links['unpublish'] : NULL,
				'edit_link' => isset($links['edit']) ? $links['edit'] : NULL,
				'delete_link' => isset($links['delete']) ? $links['delete'] : NULL,
			);
		}
		
		// Pagination
		$pagination = new Pagination();
		$pagination->total = $this->MsLoader->MsProduct->getTotalProducts(array(
			'seller_id' => $seller_id,
			'product_status' => array(MsProduct::STATUS_ACTIVE, MsProduct::STATUS_INACTIVE, MsProduct::STATUS_DISABLED)
		));
		$pagination->page = ($page - 1) * 5;
		$pagination->limit = 5;
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('seller/account-product', 'page={page}', 'SSL');
		$this->data['pagination'] = $pagination->render();

		// Links
		$this->data['link_back'] = $this->url->link('account/account', '', 'SSL');
		$this->data['link_create_product'] = $this->url->link('seller/account-product/create', '', 'SSL');

		// Title and friends
		$this->document->setTitle($this->language->get('ms_account_products_heading'));		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_products_breadcrumbs'),
				'href' => $this->url->link('seller/account-product', '', 'SSL'),
			)
		));
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->loadTemplate('account-product');
		$this->response->setOutput($this->render());
	}
	
	public function update() {
		$this->load->model('tool/image');
		$this->load->model('catalog/category');
		$this->document->addScript('catalog/view/javascript/jquery.uploadify.js');
		
		if ($this->config->get('msconf_enable_pdf_generator') && extension_loaded('imagick')) {
			$this->document->addScript('catalog/view/javascript/dialog-pdf.js');
		}

		$this->document->addScript('catalog/view/javascript/account-product-form.js');
		
		$this->data['seller'] = $this->MsLoader->MsSeller->getSeller($this->customer->getId());
		
		if (!$this->config->get('msconf_allow_multiple_categories'))
			$this->data['categories'] = $this->MsLoader->MsProduct->getCategories();		
		else
			$this->data['categories'] = $this->MsLoader->MsProduct->getMultipleCategories(0);

		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();		
		
		$product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;
		$seller_id = $this->customer->getId();
		
		if  ($this->MsLoader->MsProduct->productOwnedBySeller($product_id,$seller_id)) {
    		$product = $this->MsLoader->MsProduct->getProduct($product_id);
		} else {
			$product = NULL;
		}

		$this->data['salt'] = $this->MsLoader->MsSeller->getSalt($seller_id);

		if (!$product['product_id']) {
			$this->redirect($this->url->link('seller/account-product', '', 'SSL'));
		} else {
			$this->data['options'] = array();
			$options = $this->MsLoader->MsProduct->getOptions(array('option_ids' => $this->config->get('msconf_product_options')));
			foreach ($options as $option) {
				$option_values = $this->MsLoader->MsProduct->getOptionValues($option['option_id']);
				$option['values'] = $option_values;
				$this->data['options'][] = $option;
			}
			
			$this->data['product_attributes'] = $this->MsLoader->MsProduct->getProductAttributes($product_id);
			
			if (!empty($product['thumbnail'])) {
				$product['images'][] = array(
					'name' => $product['thumbnail'],
					'thumb' => $this->MsLoader->MsFile->resizeImage($product['thumbnail'], $this->config->get('msconf_image_preview_width'), $this->config->get('msconf_image_preview_height'))
				);
				
				if (!in_array($product['thumbnail'], $this->session->data['multiseller']['files']))
					$this->session->data['multiseller']['files'][] = $product['thumbnail'];
			}
			
			$images = $this->MsLoader->MsProduct->getProductImages($product_id);
			foreach ($images as $image) {
				$product['images'][] = array(
					'name' => $image['image'],
					'thumb' => $this->MsLoader->MsFile->resizeImage($image['image'], $this->config->get('msconf_image_preview_width'), $this->config->get('msconf_image_preview_height'))
				);
				
				if (!in_array($image['image'], $this->session->data['multiseller']['files']))
					$this->session->data['multiseller']['files'][] = $image['image'];
			}

			$downloads = $this->MsLoader->MsProduct->getProductDownloads($product_id);
			foreach ($downloads as $download) {
				//$ext = explode('.', $download['mask']); $ext = end($ext);
				
				$product['downloads'][] = array(
					'name' => $download['mask'],
					'src' => $download['filename'],
					//'href' => HTTPS_SERVER . 'download/' . $download['filename'],
					'href' => $this->url->link('seller/account-product/download', 'download_id=' . $download['download_id'] . '&product_id=' . $product_id, 'SSL'),
					'id' => $download['download_id'],
					//'pdf' => ($this->config->get('msconf_enable_pdf_generator') && extension_loaded('imagick') && strtolower($ext) == 'pdf') ? 1 : 0
				);
				
				if (!in_array($download['filename'], $this->session->data['multiseller']['files']))
					$this->session->data['multiseller']['files'][] = $download['filename'];
			}

			$this->data['product'] = $product;
			$this->data['msconf_allow_multiple_categories'] = $this->config->get('msconf_allow_multiple_categories');
			$this->data['msconf_enable_shipping'] = $this->config->get('msconf_enable_shipping');
			$this->data['msconf_enable_quantities'] = $this->config->get('msconf_enable_quantities');
			$this->data['msconf_images_limits'] = $this->config->get('msconf_images_limits');
			$this->data['msconf_downloads_limits'] = $this->config->get('msconf_downloads_limits');			
			$this->data['ms_account_product_download_note'] = sprintf($this->language->get('ms_account_product_download_note'), $this->config->get('msconf_allowed_download_types'));
			$this->data['ms_account_product_image_note'] = sprintf($this->language->get('ms_account_product_image_note'), $this->config->get('msconf_allowed_image_types'));			
			
			$this->data['back'] = $this->url->link('seller/account-product', '', 'SSL');						
			$this->data['heading'] = $this->language->get('ms_account_editproduct_heading');
			
			$this->document->setTitle($this->language->get('ms_account_editproduct_heading'));
			
			$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
				array(
					'text' => $this->language->get('text_account'),
					'href' => $this->url->link('account/account', '', 'SSL'),
				),
				array(
					'text' => $this->language->get('ms_account_products_breadcrumbs'),
					'href' => $this->url->link('seller/account-product', '', 'SSL'),
				),				
				array(
					'text' => $this->language->get('ms_account_editproduct_breadcrumbs'),
					'href' => $this->url->link('seller/account-product/update', '', 'SSL'),
				)
			));
		
			list($this->template, $this->children) = $this->MsLoader->MsHelper->loadTemplate('account-product-form');
			$this->response->setOutput($this->render());
		}
	}
	
	public function delete() {
		$product_id = (int)$this->request->get['product_id'];
		$seller_id = (int)$this->customer->getId();
		
		if ($this->MsLoader->MsProduct->productOwnedBySeller($product_id, $seller_id)) {
			$this->MsLoader->MsProduct->changeStatus($product_id, MsProduct::STATUS_DELETED);
			$this->session->data['success'] = $this->language->get('ms_success_product_deleted');			
		}
		
		$this->redirect($this->url->link('seller/account-product', '', 'SSL'));		
	}
	
	public function publish() {
		$product_id = (int)$this->request->get['product_id'];
		$seller_id = (int)$this->customer->getId();
		
		if ($this->MsLoader->MsProduct->productOwnedBySeller($product_id, $seller_id)
			&& $this->MsLoader->MsProduct->getStatus($product_id) == MsProduct::STATUS_INACTIVE) {
			$this->MsLoader->MsProduct->changeStatus($product_id, MsProduct::STATUS_ACTIVE);
			$this->session->data['success'] = $this->language->get('ms_success_product_published');
		}
		
		$this->redirect($this->url->link('seller/account-product', '', 'SSL'));		
	}	
	
	public function unpublish() {
		$product_id = (int)$this->request->get['product_id'];
		$seller_id = (int)$this->customer->getId();
		
		if ($this->MsLoader->MsProduct->productOwnedBySeller($product_id, $seller_id)
			&& $this->MsLoader->MsProduct->getStatus($product_id) == MsProduct::STATUS_ACTIVE) {
			$this->MsLoader->MsProduct->changeStatus($product_id, MsProduct::STATUS_INACTIVE);
			$this->session->data['success'] = $this->language->get('ms_success_product_unpublished');
		}
		
		$this->redirect($this->url->link('seller/account-product', '', 'SSL'));		
	}	
	
	public function download() {
		if (!$this->customer->isLogged()) {
			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}

		if (isset($this->request->get['download_id'])) {
			$download_id = $this->request->get['download_id'];
		} else {
			$download_id = 0;
		}
		
		if (isset($this->request->get['product_id'])) {
			$product_id = $this->request->get['product_id'];
		} else {
			$product_id = 0;
		}
		
		if (!$this->MsLoader->MsProduct->hasDownload($product_id,$download_id))
			$this->redirect($this->url->link('seller/account-product', '', 'SSL'));
			
		$download_info = $this->MsLoader->MsProduct->getDownload($download_id);
		
		if ($download_info) {
			$file = DIR_DOWNLOAD . $download_info['filename'];
			$mask = basename($download_info['mask']);

			if (!headers_sent()) {
				if (file_exists($file)) {
					header('Content-Type: application/octet-stream');
					header('Content-Description: File Transfer');
					header('Content-Disposition: attachment; filename="' . ($mask ? $mask : basename($file)) . '"');
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					header('Content-Length: ' . filesize($file));
					
					readfile($file, 'rb');
					exit;
				} else {
					exit('Error: Could not find file ' . $file . '!');
				}
			} else {
				exit('Error: Headers already sent out!');
			}
		} else {
			$this->redirect($this->url->link('seller/account-product', '', 'SSL'));
		}
	}	
}
?>
