<?php

class ControllerSellerAccountProduct extends ControllerSellerAccount {
	public function getTableData() {
		$colMap = array(
			'product_name' => '`pd.name`',
			'product_status' => '`mp.product_status`',
			'date_created' => '`p.date_created`',
			'list_until' => '`mp.list_until`',
			'number_sold' => 'mp.number_sold',
			'product_price' => 'p.price',
		);
		
		$sorts = array('product_name', 'product_price', 'date_created', 'list_until', 'product_status', 'product_earnings', 'number_sold');
		
		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);

		$seller_id = $this->customer->getId();
		$products = $this->MsLoader->MsProduct->getProducts(
			array(
				'seller_id' => $seller_id,
				'language_id' => $this->config->get('config_language_id'),
				'product_status' => array(MsProduct::STATUS_ACTIVE, MsProduct::STATUS_INACTIVE, MsProduct::STATUS_DISABLED, MsProduct::STATUS_UNPAID)
			),
			array(
				'order_by'  => $sortCol,
				'order_way' => $sortDir,
				'offset' => $this->request->get['iDisplayStart'],
				'limit' => $this->request->get['iDisplayLength']
			),
			array(
				'product_earnings' => 1
			)
		);
		
		$total = isset($products[0]) ? $products[0]['total_rows'] : 0;

		$columns = array();
		foreach ($products as $product) {
			$sale_data = $this->MsLoader->MsProduct->getSaleData($product['product_id']);

			// special price
			$specials = $this->MsLoader->MsProduct->getProductSpecials($product['product_id']);
			$special = false;
			foreach ($specials as $product_special) {
				if (($product_special['date_start'] == '0000-00-00' || $product_special['date_start'] < date('Y-m-d')) && ($product_special['date_end'] == '0000-00-00' || $product_special['date_end'] > date('Y-m-d'))) {
					$special = $this->currency->format($product_special['price'], $this->config->get('config_currency'));
					break;
				}
			}

			// price
			$product['p.price'] = $this->currency->format($product['p.price'], $this->config->get('config_currency'));
			if ($special) {
				$price = "<span style='text-decoration: line-through;'>{$product['p.price']}></span><br/>";
				$price .= "<span class='special-price' style='color: #b00;'>$special</span>";
			} else {
				$price = $product['p.price'];
			}

			// Product Image
			if ($product['p.image'] && file_exists(DIR_IMAGE . $product['p.image'])) {
				$image = $this->MsLoader->MsFile->resizeImage($product['p.image'], $this->config->get('msconf_product_seller_product_list_seller_area_image_width'), $this->config->get('msconf_product_seller_product_list_seller_area_image_height'));
			} else {
				$image = $this->MsLoader->MsFile->resizeImage('no_image.jpg', $this->config->get('msconf_product_seller_product_list_seller_area_image_width'), $this->config->get('msconf_product_seller_product_list_seller_area_image_height'));
			}
			
			// actions
			$actions = "";
			if ($product['mp.product_status'] != MsProduct::STATUS_DISABLED) {
				if ($product['mp.product_status'] == MsProduct::STATUS_ACTIVE)
					$actions .= "<a href='" . $this->url->link('product/product', 'product_id=' . $product['product_id'], 'SSL') ."' class='ms-button ms-button-view' title='" . $this->language->get('ms_viewinstore') . "'></a>";
	
				if ($product['mp.product_approved']) {
					if ($product['mp.product_status'] == MsProduct::STATUS_INACTIVE)
						$actions .= "<a href='" . $this->url->link('seller/account-product/publish', 'product_id=' . $product['product_id'], 'SSL') ."' class='ms-button ms-button-publish' title='" . $this->language->get('ms_publish') . "'></a>";
		
					if ($product['mp.product_status'] == MsProduct::STATUS_ACTIVE)
						$actions .= "<a href='" . $this->url->link('seller/account-product/unpublish', 'product_id=' . $product['product_id'], 'SSL') ."' class='ms-button ms-button-unpublish' title='" . $this->language->get('ms_unpublish') . "'></a>";
				}
				
				$actions .= "<a href='" . $this->url->link('seller/account-product/update', 'product_id=' . $product['product_id'], 'SSL') ."' class='ms-button ms-button-edit' title='" . $this->language->get('ms_edit') . "'></a>";
				$actions .= "<a href='" . $this->url->link('seller/account-product/update', 'product_id=' . $product['product_id'] . "&clone=1", 'SSL') ."' class='ms-button ms-button-clone' title='" . $this->language->get('ms_clone') . "'></a>";
				$actions .= "<a href='" . $this->url->link('seller/account-product/delete', 'product_id=' . $product['product_id'], 'SSL') ."' class='ms-button ms-button-delete' title='" . $this->language->get('ms_delete') . "'></a>";
			} else {
				if ($this->config->get('msconf_allow_relisting')) {
					$actions .= "<a href='" . $this->url->link('seller/account-product/update', 'product_id=' . $product['product_id'] . "&relist=1", 'SSL') ."' class='ms-button ms-button-relist' title='" . $this->language->get('ms_relist') . "'></a>";
				}
			}
			
			// product status
			$status = "";
			if ($product['mp.product_status'] == MsProduct::STATUS_ACTIVE) { 
				$status = "<span class='active' style='color: #080;'>" . $this->language->get('ms_product_status_' . $product['mp.product_status']) . "</td></span>";
			} else {
				$status = "<span class='inactive' style='color: #b00;'>" . $this->language->get('ms_product_status_' . $product['mp.product_status']) . "</td></span>";
			}
			
			// List until
			if (isset($product['mp.list_until']) && $product['mp.list_until'] != NULL) {
				$list_until = date($this->language->get('date_format_short'), strtotime($product['mp.list_until']));
			} else {
				$list_until = $this->language->get('ms_not_defined');
			}
			
			$columns[] = array_merge(
				$product,
				array(
					'image' => "<img src='$image' style='padding: 1px; border: 1px solid #DDDDDD' />",
					'product_name' => $product['pd.name'],
					'product_price' => $price,
					'number_sold' => $product['mp.number_sold'],
					'product_earnings' => $this->currency->format($sale_data['seller_total'], $this->config->get('config_currency')),
					'product_status' => $status,
					'date_created' => date($this->language->get('date_format_short'), strtotime($product['p.date_created'])),
					'list_until' => $list_until,
					'actions' => $actions
				)
			);
		}
		
		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => $total,
			'iTotalDisplayRecords' => $total,
			'aaData' => $columns
		)));
	}
	
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
				$thumbUrl = $this->MsLoader->MsFile->resizeImage($this->config->get('msconf_temp_image_path') . $fileName, $this->config->get('msconf_preview_product_image_width'), $this->config->get('msconf_preview_product_image_height'));
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
			if ($msconf_images_limits[1] > 0 && $this->request->post['fileCount'] >= $msconf_images_limits[1]) {
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

					$thumbUrl = $this->MsLoader->MsFile->resizeImage($this->config->get('msconf_temp_image_path') . $fileName, $this->config->get('msconf_preview_product_image_width'), $this->config->get('msconf_preview_product_image_height'));
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
			if ($msconf_downloads_limits[1] > 0 && $this->request->post['fileCount'] >= $msconf_downloads_limits[1]) {
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
	
	public function jxGetFee() {
		$data = $this->request->get;
		
		if (!isset($data['price']) && !is_numeric($data['price'])) 
			$data['price'] = 0;

		$rates = $this->MsLoader->MsCommission->calculateCommission(array('seller_id' => $this->customer->getId()));
		echo $this->currency->format((float)$rates[MsCommission::RATE_LISTING]['flat'] + ((float)$rates[MsCommission::RATE_LISTING]['percent'] * $data['price'] / 100), $this->config->get('config_currency'));
	}
	
	public function jxSubmitProduct() {
		//ob_start();
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

		// Only check default language for errors
		$i = 0;
		$default = 0;
		$attributes = array();
		$product_attributes = array();
		
		foreach ($this->MsLoader->MsAttribute->getAttributes(array('multilang' => 1, 'enabled' => 1)) as $attribute) {
			$attributes[$attribute['attribute_id']] = $attribute;
			$attributes[$attribute['attribute_id']]['values'] = $this->MsLoader->MsAttribute->getAttributeValues($attribute['attribute_id']);
		}		
		
		foreach ($data['languages'] as $language_id => $language) {
			// main language inputs are mandatory

			$description_length = $this->config->get('msconf_enable_rte') ? mb_strlen(strip_tags(htmlspecialchars_decode($language['product_description'], ENT_COMPAT))) : mb_strlen(htmlspecialchars_decode($language['product_description'], ENT_COMPAT));
			if ($i == 0) {
				$default = $language_id;
				
				if (empty($language['product_name'])) {
					$json['errors']['product_name_' . $language_id] = $this->language->get('ms_error_product_name_empty'); 
				} else if (mb_strlen($language['product_name']) < 4 || mb_strlen($language['product_name']) > 50 ) {
					$json['errors']['product_name_' . $language_id] = sprintf($this->language->get('ms_error_product_name_length'), 4, 50);
				}

				if (empty($language['product_description'])) {
					$json['errors']['product_description_' . $language_id] = $this->language->get('ms_error_product_description_empty'); 
				} else if ($description_length < 25 || $description_length > 4000) {
					$json['errors']['product_description_' . $language_id] = sprintf($this->language->get('ms_error_product_description_length'), 25, 4000);
				}
			} else {
				if (!empty($language['product_name']) && (mb_strlen($language['product_name']) < 4 || mb_strlen($language['product_name']) > 50)) {
					$json['errors']['product_name_' . $language_id] = sprintf($this->language->get('ms_error_product_name_length'), 4, 50);
				} else if (empty($language['product_name'])) {
					$data['languages'][$language_id]['product_name'] = $data['languages'][$default]['product_name'];
				}

				if (!empty($language['product_description']) && ($description_length < 25 || $description_length > 4000)) {
					$json['errors']['product_description_' . $language_id] = sprintf($this->language->get('ms_error_product_description_length'), 25, 4000);
				} else if (empty($language['product_description'])) {
					$data['languages'][$language_id]['product_description'] = $data['languages'][$default]['product_description'];
				}
			}
			
			if (in_array('metaDescription', $this->config->get('msconf_product_included_fields'))) {
				$data['languages'][$language_id]['product_meta_description'] = $data['languages'][$default]['product_meta_description'];
			}
			if (in_array('metaKeywords', $this->config->get('msconf_product_included_fields'))) {
				$data['languages'][$language_id]['product_meta_keyword'] = $data['languages'][$default]['product_meta_keyword'];
			}

			if (!empty($language['product_tags']) && mb_strlen($language['product_tags']) > 1000) {
				$json['errors']['product_tags_' . $language_id] = $this->language->get('ms_error_product_tags_length');			
			}
			
			// strip disallowed tags in description
			if ($this->config->get('msconf_enable_rte')) {
				if ($this->config->get('msconf_rte_whitelist') != '') {
					$allowed_tags = explode(",", $this->config->get('msconf_rte_whitelist'));
					$allowed_tags_ready = "";
					foreach($allowed_tags as $tag) {
						$allowed_tags_ready .= "<".trim($tag).">";
					}
					$data['languages'][$language_id]['product_description'] = htmlspecialchars(strip_tags(htmlspecialchars_decode($language['product_description'], ENT_COMPAT), $allowed_tags_ready), ENT_COMPAT, 'UTF-8');
				}
			} else {
				$data['languages'][$language_id]['product_description'] = htmlspecialchars(nl2br($language['product_description']), ENT_COMPAT, 'UTF-8');
			}
			
			// multilang attributes
			if (isset($language['product_attributes'])) {
				$product_attributes = $language['product_attributes'];
				unset($data['languages'][$language_id]['product_attributes']);
				
				foreach ($attributes as $attribute_id => $attribute) {
					// required attributes empty, errors, for first language only
					if ($i == 0 && $attribute['required'] && (!isset($product_attributes[$attribute_id]) || empty($product_attributes[$attribute_id]) || empty($product_attributes[$attribute_id]['value']))) {
						$json['errors']["languages[$language_id][product_attributes][$attribute_id]"] = $this->language->get('ms_error_product_attribute_required'); 
						continue;
					}
					
					// attribute validation
					if ($attribute['attribute_type'] == MsAttribute::TYPE_TEXT) {
						if (mb_strlen($product_attributes[$attribute_id]['value']) > 100) {
							$json['errors']["languages[$language_id][product_attributes][$attribute_id]"] = sprintf($this->language->get('ms_error_product_attribute_long'), 100);
							continue;
						}
					// text input validation
					} else if ($attribute['attribute_type'] == MsAttribute::TYPE_TEXTAREA) {
						if (mb_strlen($product_attributes[$attribute_id]['value']) > 2000) {
							$json['errors']["languages[$language_id][product_attributes][$attribute_id]"] = sprintf($this->language->get('ms_error_product_attribute_long'), 2000);
							continue;
						}

						// enable to allow RTE for attributes
						// $product_attributes[$attribute_id]['value'] = strip_tags(html_entity_decode($product_attributes[$attribute_id]['value']), $allowed_tags_ready);
					}

					// set attributes
					$data['languages'][$language_id]['product_attributes'][$attribute_id] = array(
						'attribute_type' => $attribute['attribute_type'],
						// sorcery
						'value' => !empty($product_attributes[$attribute_id]['value']) ? $product_attributes[$attribute_id]['value'] :  (isset($data['languages'][$default]['product_attributes'][$attribute_id]['value']) ? $data['languages'][$default]['product_attributes'][$attribute_id]['value'] : ''),
						'value_id' => $product_attributes[$attribute_id]['value_id']
					);
				}
			}
			
			$i++;
		}

		if ((float)$data['product_price'] == 0) {
			if (!is_numeric($data['product_price'])) {
				$json['errors']['product_price'] = $this->language->get('ms_error_product_price_invalid');			
			} else if ($this->config->get('msconf_allow_free_products') == 0) {
				$json['errors']['product_price'] = $this->language->get('ms_error_product_price_empty');
			}
		} else if ((float)$data['product_price'] < (float)$this->config->get('msconf_minimum_product_price')) {
			$json['errors']['product_price'] = $this->language->get('ms_error_product_price_low');
		} else if (($this->config->get('msconf_maximum_product_price') != 0) && ((float)$data['product_price'] > (float)$this->config->get('msconf_maximum_product_price'))) {
			$json['errors']['product_price'] = $this->language->get('ms_error_product_price_high');
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
							$json['errors']['product_download'] = $this->language->get('ms_error_file_upload_error');
						}						
					} else if (!empty($download['download_id']) && !empty($product['product_id'])) {
						if (!$this->MsLoader->MsProduct->hasDownload($product['product_id'],$download['download_id'])) {
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
		
		// Special Prices
		unset($data['product_specials'][0]); // Remove sample row
		if (isset($data['product_specials']) && is_array($data['product_specials'])) {
			$product_specials = $data['product_specials'];
			foreach ($product_specials as $product_special) {
				if (!isset($product_special['priority']) || $product_special['priority'] == null || $product_special['priority'] == "") {
					$json['errors']['specials'] = $this->language->get('ms_error_invalid_special_price_priority');
				}
				if ((!$this->MsLoader->MsHelper->isUnsignedFloat($product_special['price'])) || ((float)$product_special['price'] < (float)0)) {
					$json['errors']['specials'] = $this->language->get('ms_error_invalid_special_price_price');
				}
				if ( !isset($product_special['date_start']) || ($product_special['date_start'] == NULL) || (!isset($product_special['date_end']) || $product_special['date_end'] == NULL) ) {
					$json['errors']['specials'] = $this->language->get('ms_error_invalid_special_price_dates');
				}
			}
		}
		
		// Quantity Discounts
		unset($data['product_discounts'][0]); // Remove sample row
		if (isset($data['product_discounts']) && is_array($data['product_discounts'])) {
			$product_discounts = $data['product_discounts'];
			foreach ($product_discounts as $product_discount) {
				if (!isset($product_discount['priority']) || $product_discount['priority'] == null || $product_discount['priority'] == "") {
					$json['errors']['quantity_discounts'] = $this->language->get('ms_error_invalid_quantity_discount_priority');
				}
				if ((int)$product_discount['quantity'] < (int)2) {
					$json['errors']['quantity_discounts'] = $this->language->get('ms_error_invalid_quantity_discount_quantity');
				}
				if ((!$this->MsLoader->MsHelper->isUnsignedFloat($product_discount['price'])) || ((float)$product_discount['price'] < (float)0)) {
					$json['errors']['quantity_discounts'] = $this->language->get('ms_error_invalid_quantity_discount_price');
				}
				if ( !isset($product_discount['date_start']) || ($product_discount['date_start'] == NULL) || (!isset($product_discount['date_end']) || $product_discount['date_end'] == NULL) ) {
					$json['errors']['quantity_discounts'] = $this->language->get('ms_error_invalid_quantity_discount_dates');
				}
			}
		}
		
		// uncomment to enable RTE for message field 
		/*
		if(isset($data['product_message'])) {
			$data['product_message'] = strip_tags(html_entity_decode($data['product_message']), $allowed_tags_ready);
		}
		*/
		
		if (isset($data['product_category']) && !empty($data['product_category'])) {
			$categories = $this->MsLoader->MsProduct->getCategories();
			$disabled = array();
			foreach ($categories as $k => $c) {
				if ($c['disabled']) $disabled[] = $c['category_id'];
			}

			// convert to array if needed
			$data['product_category'] = is_array($data['product_category']) ? $data['product_category'] : array($data['product_category']);
			
			// remove disabled categories if set
			$data['product_category'] = array_diff($data['product_category'], $disabled);				
			
			if (!$this->config->get('msconf_allow_multiple_categories') && count($data['product_category']) > 1) {
				$data['product_category'] = array($data['product_category'][0]);
			}
		}

		// data array could have been modified in the previous step
		if (!isset($data['product_category']) || empty($data['product_category'])) {
			$json['errors']['product_category'] = $this->language->get('ms_error_product_category_empty'); 		
		}

		if (in_array('model', $this->config->get('msconf_product_included_fields'))) {
			if (empty($data['product_model'])) {
				$json['errors']['product_model'] = $this->language->get('ms_error_product_model_empty');
			} else if (mb_strlen($data['product_model']) < 4 || mb_strlen($data['product_model']) > 64 ) {
				$json['errors']['product_model'] = sprintf($this->language->get('ms_error_product_model_length'), 4, 64);
			}
		}

		// generic attributes
		$attributes = array();
		$product_attributes = array();		

		if (isset($data['product_attributes'])) {
			$product_attributes = $data['product_attributes'];
			unset($data['product_attributes']);
		}
		
		foreach ($this->MsLoader->MsAttribute->getAttributes(array('multilang' => 0, 'enabled' => 1)) as $attribute) {
			$attributes[$attribute['attribute_id']] = $attribute;
			$attributes[$attribute['attribute_id']]['values'] = $this->MsLoader->MsAttribute->getAttributeValues($attribute['attribute_id']);
		}
		
		foreach ($attributes as $attribute_id => $attribute) {
			// attributes with no values defined, skip
			if (empty($attribute['values']) && in_array($attribute['attribute_type'], array(MsAttribute::TYPE_CHECKBOX, MsAttribute::TYPE_SELECT, MsAttribute::TYPE_RADIO)))
				continue;				
			
			// required attributes empty, errors
			// haha
			if (($attribute['required'] || $attribute['attribute_type'] == MsAttribute::TYPE_RADIO) && (!isset($product_attributes[$attribute_id]) || empty($product_attributes[$attribute_id]) || (isset($product_attributes[$attribute_id]['value'])) && empty($product_attributes[$attribute_id]['value']))) {
				$json['errors']["product_attributes[$attribute_id]"] = $this->language->get('ms_error_product_attribute_required'); 
				continue;
			}
			
			// attribute validation
			if (in_array($attribute['attribute_type'], array(MsAttribute::TYPE_SELECT, MsAttribute::TYPE_RADIO, MsAttribute::TYPE_IMAGE)) && isset($product_attributes[$attribute_id])) {
				// select, radio, image
				if ((int)$product_attributes[$attribute_id] == 0) {
					// not required, not checked
				} else {
					// @TODO check for permitted value id
					$data['product_attributes'][$attribute_id] = array(
						'attribute_type' => $attribute['attribute_type'],
						'value' => $product_attributes[$attribute_id]
					);
				}
				continue;
			} else if ($attribute['attribute_type'] == MsAttribute::TYPE_CHECKBOX) {
				// checkbox
				if (isset($product_attributes[$attribute_id])) {
					foreach ($product_attributes[$attribute_id] as $key => $attribute_value_id) {
						if ((int)$attribute_value_id != 0) {
							// @TODO check for permitted value id
							$data['product_attributes'][$attribute_id]['attribute_type']  = $attribute['attribute_type'];
							$data['product_attributes'][$attribute_id]['values'][]  = (int)$attribute_value_id;
						}
					}
				}
				continue;
			} else if ($attribute['attribute_type'] == MsAttribute::TYPE_TEXT) {
				if (mb_strlen($product_attributes[$attribute_id]['value']) > 100) {
					$json['errors']["product_attributes[$attribute_id]"] = sprintf($this->language->get('ms_error_product_attribute_long'), 100);
					continue;
				}
				// text input validation
			} else if ($attribute['attribute_type'] == MsAttribute::TYPE_TEXTAREA) {
				if (mb_strlen($product_attributes[$attribute_id]['value']) > 2000) {
					$json['errors']["product_attributes[$attribute_id]"] = sprintf($this->language->get('ms_error_product_attribute_long'), 2000);
					continue;
				}
			} else if ($attribute['attribute_type'] == MsAttribute::TYPE_DATE) {
				// date input validation
			} else if ($attribute['attribute_type'] == MsAttribute::TYPE_DATETIME) {
				// datetime input validation
			} else if ($attribute['attribute_type'] == MsAttribute::TYPE_TIME) {
				// datetime input validation
			}

			// set attributes
			//if (isset($data['product_attributes'][$attribute_id])) { ?
				$data['product_attributes'][$attribute_id] = array(
					'attribute_type' => $attribute['attribute_type'],
					'value' => $product_attributes[$attribute_id]['value'],
					'value_id' => $product_attributes[$attribute_id]['value_id'],
				);
			//}
		}
		
		// options
		//unset($data['product_option'][0]); // Remove sample row		
		
        if(!isset($data['product_subtract'])){
            $data['product_subtract'] = 0;
        }
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
		
		// Set the quantity
		$seller_group = $this->MsLoader->MsSellerGroup->getSellerGroup($seller['ms.seller_group']);
		if ($this->config->get('msconf_enable_quantities') == 1) { // Enable quantities
			if (isset($seller_group['product_quantity']) && $seller_group['product_quantity'] != 0) { // Seller group quantity is set
				$data['product_quantity'] = (int)$seller_group['product_quantity'];
			} else {
				$data['product_quantity'] = (int)$data['product_quantity'];
			}
			$data['product_subtract'] = 1;
		} else if ($this->config->get('msconf_enable_quantities') == 2) { // Shipping dependent
			if ($this->config->get('msconf_enable_shipping') == 1) {
				$data['product_subtract'] = 1;
				if (isset($seller_group['product_quantity']) && $seller_group['product_quantity'] != 0) { // Seller group quantity is set
					$data['product_quantity'] = (int)$seller_group['product_quantity'];
				} else {
					if (!isset($data['product_quantity']))
						$data['product_quantity'] = 0;
				}
			} else if ($this->config->get('msconf_enable_shipping') == 2) {
				if (!$data['product_enable_shipping']) {
					$data['product_quantity'] = 999;
				} else {
					$data['product_subtract'] = 1;
					if (isset($seller_group['product_quantity']) && $seller_group['product_quantity'] != 0) { // Seller group quantity is set
						$data['product_quantity'] = (int)$seller_group['product_quantity'];
					} else {
						if (!isset($data['product_quantity']))
							$data['product_quantity'] = 0;
					}
				}
			} else { // Shipping disabled and quantity is shipping dependent
				$data['product_quantity'] = 999;
			}
		} else { // Disable quantities
			$data['product_quantity'] = 999;
		}
		
		// SEO urls generation for products
		if ($this->config->get('msconf_enable_seo_urls_product')) {
			$latin_check = '/[^\x{0030}-\x{007f}]/u';
			$product_name = $data['languages'][$default]['product_name'];
			$non_latin_chars = preg_match($latin_check, $product_name);
			if ($this->config->get('msconf_enable_non_alphanumeric_seo') && $non_latin_chars) {
				$data['keyword'] = implode("-", str_replace("-", "", explode(" ", preg_replace("/[^\p{L}\p{N} ]/u", '', strtolower($product_name)))));
			}
			else {
				$data['keyword'] = implode("-", str_replace("-", "", explode(" ", preg_replace("/[^A-Za-z0-9 ]/", '', strtolower($product_name)))));
			}
		}
		
		// Listing until
		if (!isset($data['listing_until']) || $data['listing_until'] == "") {
			$data['listing_until'] = NULL;
		}

		// post-validation
		if (empty($json['errors'])) {
			$mails = array();
			
			// Relist the product
			if ($this->config->get('msconf_allow_relisting')) {
				if ((isset($data['product_id']) && !empty($data['product_id'])) && $this->MsLoader->MsProduct->getStatus((int)$data['product_id']) == MsProduct::STATUS_DISABLED) {
					$this->MsLoader->MsProduct->changeStatus((int)$data['product_id'], MsProduct::STATUS_ACTIVE);
				}
			}
			
			// If it is allowed for inactive seller to list new products
			if ($this->config->get('msconf_allow_inactive_seller_products') && $this->MsLoader->MsSeller->getStatus() == MsSeller::STATUS_INACTIVE) {
				$data['enabled'] = 0;
				$data['product_status'] = MsProduct::STATUS_INACTIVE;
				$data['product_approved'] = 0;
				// No e-mails are sent here
			} else {
				// Set product status
				switch ($seller['ms.product_validation']) {
					case MsProduct::MS_PRODUCT_VALIDATION_APPROVAL:
						$data['enabled'] = 0;
						$data['product_status'] = MsProduct::STATUS_INACTIVE;
						$data['product_approved'] = 0;
						/*if (isset($data['product_id']) && !empty($data['product_id'])) {
							//$request_type = MsRequestProduct::TYPE_PRODUCT_UPDATE;
						} else {
							//$request_type = MsRequestProduct::TYPE_PRODUCT_CREATE;
						}*/
						
						if (!isset($data['product_id']) || empty($data['product_id'])) {
							$mails[] = array(
								'type' => MsMail::SMT_PRODUCT_AWAITING_MODERATION
							);
							$mails[] = array(
								'type' => MsMail::AMT_NEW_PRODUCT_AWAITING_MODERATION,
								'data' => array(
									'message' => isset($data['product_message']) ? $data['product_message'] : ''
								)
							);
						} else {
							$mails[] = array(
								'type' => MsMail::SMT_PRODUCT_AWAITING_MODERATION
							);
							$mails[] = array(
								'type' => MsMail::AMT_EDIT_PRODUCT_AWAITING_MODERATION,
								'data' => array(
									'message' => isset($data['product_message']) ? $data['product_message'] : ''
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
			}
			
			if (isset($data['product_id']) && !empty($data['product_id'])) {
				$product_id = $this->MsLoader->MsProduct->editProduct($data);
				if ($product['product_status'] == MsProduct::STATUS_UNPAID) {
					$commissions = $this->MsLoader->MsCommission->calculateCommission(array('seller_id' => $this->customer->getId()));
					$fee = (float)$commissions[MsCommission::RATE_LISTING]['flat'] + $commissions[MsCommission::RATE_LISTING]['percent'] * $data['product_price'] / 100;
					if ($fee > 0) {
						switch($commissions[MsCommission::RATE_LISTING]['payment_method']) {
							case MsPayment::METHOD_PAYPAL:
								// initiate paypal payment
								
								// change status to unpaid
								$this->MsLoader->MsProduct->changeStatus($product_id, MsProduct::STATUS_UNPAID);
								
								// unset seller email
								foreach ($mails as $key => $value) {
									if ($value['type'] == SMT_PRODUCT_AWAITING_MODERATION) unset($mails[$key]);
								}
								// Send product edited emails
								foreach ($mails as &$mail) {
									$mail['data']['product_id'] = $product_id;
								}
								$this->MsLoader->MsMail->sendMails($mails);
								
								// check if payment exists
								$payment = $this->MsLoader->MsPayment->getPayments(array(
									'seller_id' => $this->customer->getId(),
									'product_id' => $product_id,
									'payment_type' => array(MsPayment::TYPE_LISTING),
									'payment_status' => array(MsPayment::STATUS_UNPAID),
									'payment_method' => array(MsPayment::METHOD_PAYPAL),
									'single' => 1
								));
								
								if (!$payment) {
									// create new payment
									$payment_id = $this->MsLoader->MsPayment->createPayment(array(
										'seller_id' => $this->customer->getId(),
										'product_id' => $product_id,
										'payment_type' => MsPayment::TYPE_LISTING,
										'payment_status' => MsPayment::STATUS_UNPAID,
										'payment_method' => MsPayment::METHOD_PAYPAL,
										'amount' => $fee,
										'currency_id' => $this->currency->getId($this->config->get('config_currency')),
										'currency_code' => $this->currency->getCode($this->config->get('config_currency')),
										'description' => sprintf($this->language->get('ms_transaction_listing'), $data['languages'][$default]['product_name'], $this->currency->format(-$fee, $this->config->get('config_currency')))
									));
								} else {
									$payment_id = $payment['payment_id'];
									
									// edit payment
									$this->MsLoader->MsPayment->updatePayment($payment_id, array(
										'amount' => $fee,
										'date_created' => 1,
										'description' => sprintf($this->language->get('ms_transaction_listing'), $data['languages'][$default]['product_name'], $this->currency->format(-$fee, $this->config->get('config_currency')))
									));
								}
								// assign payment variables
								$json['data']['amount'] = $this->currency->format($fee, $this->config->get('config_currency'), '', FALSE);
								$json['data']['custom'] = $payment_id;
			
								return $this->response->setOutput(json_encode($json));
								break;
	
							case MsPayment::METHOD_BALANCE:
							default:
								// deduct from balance
								$this->MsLoader->MsBalance->addBalanceEntry($this->customer->getId(),
									array(
										'product_id' => $product_id,
										'balance_type' => MsBalance::MS_BALANCE_TYPE_LISTING,
										'amount' => -$fee,
										'description' => sprintf($this->language->get('ms_transaction_listing'), $data['languages'][$default]['product_name'], $this->currency->format(-$fee, $this->config->get('config_currency')))
									)
								);
								
								break;
						}
					}
				}
				
				$this->session->data['success'] = $this->language->get('ms_success_product_updated');
			} else {
				//$data['list_until'] = date('Y-m-d', strtotime($data['list_until']));
				$commissions = $this->MsLoader->MsCommission->calculateCommission(array('seller_id' => $this->customer->getId()));
				$fee = (float)$commissions[MsCommission::RATE_LISTING]['flat'] + $commissions[MsCommission::RATE_LISTING]['percent'] * $data['product_price'] / 100;
				$product_id = $this->MsLoader->MsProduct->saveProduct($data);

				// send product created emails
				foreach ($mails as &$mail) {
					$mail['data']['product_id'] = $product_id;
				}
				$this->MsLoader->MsMail->sendMails($mails);				
				
				if ($fee > 0) {
					switch($commissions[MsCommission::RATE_LISTING]['payment_method']) {
						case MsPayment::METHOD_PAYPAL:
							// initiate paypal payment
							
							// set product status to unpaid
							$this->MsLoader->MsProduct->changeStatus($product_id, MsProduct::STATUS_UNPAID);
							
							// add payment details
							$payment_id = $this->MsLoader->MsPayment->createPayment(array(
								'seller_id' => $this->customer->getId(),
								'product_id' => $product_id,
								'payment_type' => MsPayment::TYPE_LISTING,
								'payment_status' => MsPayment::STATUS_UNPAID,
								'payment_method' => MsPayment::METHOD_PAYPAL,
								'amount' => $fee,
								'currency_id' => $this->currency->getId($this->config->get('config_currency')),
								'currency_code' => $this->currency->getCode($this->config->get('config_currency')),
								'description' => sprintf($this->language->get('ms_transaction_listing'), $data['languages'][$default]['product_name'], $this->currency->format(-$fee, $this->config->get('config_currency')))								
							));
							
							// assign payment variables
							$json['data']['amount'] = $this->currency->format($fee, $this->config->get('config_currency'), '', FALSE);
							$json['data']['custom'] = $payment_id;
		
							return $this->response->setOutput(json_encode($json));
							break;

						case MsPayment::METHOD_BALANCE:
						default:
							// deduct from balance
							$this->MsLoader->MsBalance->addBalanceEntry($this->customer->getId(),
								array(
									'product_id' => $product_id,
									'balance_type' => MsBalance::MS_BALANCE_TYPE_LISTING,
									'amount' => -$fee,
									'description' => sprintf($this->language->get('ms_transaction_listing'), $data['languages'][$default]['product_name'], $this->currency->format(-$fee, $this->config->get('config_currency')))
								)
							);
							
							break;
					}
				}
				
				$this->session->data['success'] = $this->language->get('ms_success_product_created');
			}
			
			$json['redirect'] = $this->url->link('seller/account-product', '', 'SSL');
		}
		
		/*
		$output = ob_get_clean();
		if ($output) {
			$this->log->write('MMERCH PRODUCT FORM: ' . $output);
			if (!$this->session->data['success']) $json['fail'] = 1;
		}
		*/
		$this->response->setOutput(json_encode($json));
	}

	public function jxRenderOptions() {
		$this->data['options'] = $this->MsLoader->MsOption->getOptions();
		foreach ($this->data['options'] as &$option) {
			$option['values'] = $this->MsLoader->MsOption->getOptionValues($option['option_id']);
		}
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/multiseller/account-product-form-options.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/multiseller/account-product-form-options.tpl';
		} else {
			$this->template = 'default/template/multiseller/account-product-form-options.tpl';
		}
		
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
	}
	
	public function jxRenderOptionValues() {
		$this->data['option'] = $this->MsLoader->MsOption->getOptions(
			array(
				'option_id' => 	$this->request->get['option_id'],
				'single' => 1
			)
		);
		
		$this->data['values'] = $this->MsLoader->MsOption->getOptionValues($this->request->get['option_id']);
		$this->data['option_index'] = 0;
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/multiseller/account-product-form-options-values.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/multiseller/account-product-form-options-values.tpl';
		} else {
			$this->template = 'default/template/multiseller/account-product-form-options-values.tpl';
		}
	
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
	}
	
	public function jxRenderProductOptions() {
		$this->load->model('catalog/product');
		$options = $this->model_catalog_product->getProductOptions($this->request->get['product_id']);
		
		$output = '';
		if ($options) {
			$this->data['option_index'] = 0;
			foreach ($options as $o) {
				$this->data['option'] = $o;
				$this->data['product_option_values'] = $o['option_value'];
				$this->data['values'] = $this->MsLoader->MsOption->getOptionValues($o['option_id']);
				
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/multiseller/account-product-form-options-values.tpl')) {
					$this->template = $this->config->get('config_template') . '/template/multiseller/account-product-form-options-values.tpl';
				} else {
					$this->template = 'default/template/multiseller/account-product-form-options-values.tpl';
				}
				
				$output .= $this->render();
				$this->data['option_index']++;
			}
		}
	
		$this->response->setOutput($output);
	}	
	
	public function jxAutocomplete() {
        $data = $this->request->post;
        $json = array();

        switch($data['type']){
            case 'manufacturers':
                if (isset($data['filter_name'])) {
                    $data = array(
                        'filter_name' => $data['filter_name'],
                        'start'       => 0,
                        'limit'       => 20
                    );
					
					$this->load->model('catalog/manufacturer');
					$results = $this->model_catalog_manufacturer->getManufacturers($data);

                    foreach ($results as $result) {
                        $json[] = array(
                            'manufacturer_id' => $result['manufacturer_id'],
                            'name'            => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
                        );
                    }
                }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->setOutput(json_encode($json));
    }

    public function jxShippingCategories()
    {
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');

        $product_id = empty($this->request->post['product_id']) ? 0 : $this->request->post['product_id'];
        $seller_id  = $this->customer->getId();
        $product    = NULL;

        if(!empty($product_id))
        {
            if($this->MsLoader->MsProduct->productOwnedBySeller($product_id, $seller_id))
            {
                $product = $this->MsLoader->MsProduct->getProduct($product_id);
            }
            else
                $product = NULL;
        }

        $this->data['product'] = $product;
        $this->data['product']['category_id'] = $this->MsLoader->MsProduct->getProductCategories($product_id);
        $this->data['product']['shipping'] = $this->request->post['type'];
        $this->data['categories'] = $this->MsLoader->MsProduct->getCategories();
        $this->data['msconf_allow_multiple_categories'] = $this->config->get('msconf_allow_multiple_categories');
        $this->data['msconf_enable_shipping'] = $this->config->get('msconf_enable_shipping');
        $this->data['msconf_enable_categories'] = $this->config->get('msconf_enable_categories');
        $this->data['msconf_physical_product_categories'] = $this->config->get('msconf_physical_product_categories');
        $this->data['msconf_digital_product_categories'] = $this->config->get('msconf_digital_product_categories');

        $this->template = 'default/template/multiseller/account-product-form-shipping-categories.tpl';

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    public function index() {
		// paypal listing payment confirmation
		if (isset($this->request->post['payment_status']) && strtolower($this->request->post['payment_status']) == 'completed') {
			$this->data['success'] = $this->language->get('ms_success_product_published');
		}
		
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
				'text' => $this->language->get('ms_account_dashboard_breadcrumbs'),
				'href' => $this->url->link('seller/account-dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_products_breadcrumbs'),
				'href' => $this->url->link('seller/account-product', '', 'SSL'),
			)
		));
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->loadTemplate('account-product');
		$this->response->setOutput($this->render());
	}
	
	private function _initForm() {
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('localisation/currency');
		$this->load->model('localisation/language');
		
		$this->document->addScript('catalog/view/javascript/plupload/plupload.full.js');
		$this->document->addScript('catalog/view/javascript/plupload/jquery.plupload.queue/jquery.plupload.queue.js');
		$this->document->addScript('catalog/view/javascript/jquery/ui/jquery-ui-timepicker-addon.js');
		$this->document->addScript('catalog/view/javascript/account-product-form.js');
		$this->document->addScript('catalog/view/javascript/multimerch/account-product-form-options.js');
		$this->document->addScript('catalog/view/javascript/jquery/tabs.js');
		
		// ckeditor
		if($this->config->get('msconf_enable_rte'))
			$this->document->addScript('catalog/view/javascript/multimerch/ckeditor/ckeditor.js');

		$this->data['seller'] = $this->MsLoader->MsSeller->getSeller($this->customer->getId());
		$this->data['seller_group'] = $this->MsLoader->MsSellerGroup->getSellerGroup($this->data['seller']['ms.seller_group']);
		
		$product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;
		if ($product_id) $product_status = $this->MsLoader->MsProduct->getStatus($product_id);

		if (!$product_id || $product_status == MsProduct::STATUS_UNPAID) {
			$this->data['seller']['commissions'] = $this->MsLoader->MsCommission->calculateCommission(array('seller_id' => $this->customer->getId()));
			switch($this->data['seller']['commissions'][MsCommission::RATE_LISTING]['payment_method']) {
				case MsPayment::METHOD_PAYPAL:
					$this->data['ms_commission_payment_type'] = $this->language->get('ms_account_product_listing_paypal');
					$this->data['payment_data'] = array(
						'sandbox' => $this->config->get('msconf_paypal_sandbox'),
						'action' => $this->config->get('msconf_paypal_sandbox') ? "https://www.sandbox.paypal.com/cgi-bin/webscr" : "https://www.paypal.com/cgi-bin/webscr",
						'business' => $this->config->get('msconf_paypal_address'),
						'item_name' => sprintf($this->language->get('ms_account_product_listing_itemname'), $this->config->get('config_name')),
						'item_number' => isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : '',
						'amount' => '',
						'currency_code' => $this->config->get('config_currency'),
						'return' => $this->url->link('seller/account-product'),
						'cancel_return' => $this->url->link('seller/account-product'),
						'notify_url' => $this->url->link('payment/multimerch-paypal/listingIPN'),
						'custom' => 'custom'
					);
					
					list($this->template, $this->children) = $this->MsLoader->MsHelper->loadTemplate('payment-paypal', array());
					$this->data['payment_form'] = $this->render();
					break;
					
				case MsPayment::METHOD_BALANCE:
				default:
					$this->data['ms_commission_payment_type'] = $this->language->get('ms_account_product_listing_balance');
					break;
			} 
		}
		$this->data['salt'] = $this->MsLoader->MsSeller->getSalt($this->customer->getId());
		$this->data['categories'] = $this->MsLoader->MsProduct->getCategories();
        $this->data['date_available'] = date('Y-m-d', time() - 86400);
        $this->data['tax_classes'] = $this->MsLoader->MsHelper->getTaxClasses();
        $this->data['stock_statuses'] = $this->MsLoader->MsHelper->getStockStatuses();

		$attributes = $this->MsLoader->MsAttribute->getAttributes(
			array(
				// current language
				'language_id' => $this->config->get('config_language_id'),
				'enabled' => 1
			),
			array(
				'order_by' => 'ma.sort_order',
				'order_way' => 'ASC'
			)
		);

		if (!empty($attributes)) {
			foreach ($attributes as $attr) {
				if ($attr['attribute_type'] == MsAttribute::TYPE_RADIO) $attr['required'] = 1;
				
				$attr['values'] = $this->MsLoader->MsAttribute->getAttributeValues($attr['attribute_id']);
				
				if (empty($attr['values']) && in_array($attr['attribute_type'], array(MsAttribute::TYPE_CHECKBOX, MsAttribute::TYPE_SELECT, MsAttribute::TYPE_RADIO)))
					continue;

				foreach ($attr['values'] as &$value) {
					$value['image'] = (!empty($value['image']) ? $this->MsLoader->MsFile->resizeImage($value['image'], 50, 50) : $this->MsLoader->MsFile->resizeImage('no_image.jpg', 50, 50));
				}
				
				if ($attr['multilang'] && in_array($attr['attribute_type'], array(MsAttribute::TYPE_TEXT, MsAttribute::TYPE_TEXTAREA))) {
					$this->data['multilang_attributes'][] = $attr;
				} else {
					$this->data['normal_attributes'][] = $attr;
				}
			}
		}
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		$this->data['msconf_allow_multiple_categories'] = $this->config->get('msconf_allow_multiple_categories');
		$this->data['msconf_enable_shipping'] = $this->config->get('msconf_enable_shipping');
		$this->data['msconf_images_limits'] = $this->config->get('msconf_images_limits');
		$this->data['msconf_downloads_limits'] = $this->config->get('msconf_downloads_limits');
		$this->data['msconf_enable_quantities'] = $this->config->get('msconf_enable_quantities');
        $this->data['msconf_enable_categories'] = $this->config->get('msconf_enable_categories');
        $this->data['msconf_physical_product_categories'] = $this->config->get('msconf_physical_product_categories');
        $this->data['msconf_digital_product_categories'] = $this->config->get('msconf_digital_product_categories');
        $this->data['ms_account_product_download_note'] = sprintf($this->language->get('ms_account_product_download_note'), $this->config->get('msconf_allowed_download_types'));
		$this->data['ms_account_product_image_note'] = sprintf($this->language->get('ms_account_product_image_note'), $this->config->get('msconf_allowed_image_types'));
		$this->data['back'] = $this->url->link('seller/account-product', '', 'SSL');
	}
	
	public function create() {
		$this->_initForm();
		$this->data['product_attributes'] = FALSE;
		$this->data['product'] = FALSE;
		$this->data['heading'] = $this->language->get('ms_account_newproduct_heading');
		$this->document->setTitle($this->language->get('ms_account_newproduct_heading'));

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
				'text' => $this->language->get('ms_account_products_breadcrumbs'),
				'href' => $this->url->link('seller/account-product', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_newproduct_breadcrumbs'),
				'href' => $this->url->link('seller/account-product/create', '', 'SSL'),
			)
		));
		
		// Product listing period
		if ($this->data['seller_group']['product_period'] > 0) {
			$this->data['list_until'] = date('Y-m-d', strtotime(date('Y-m-d')) + (24 * 3600 * $this->data['seller_group']['product_period']));
		} else {
			$this->data['list_until'] = NULL;
		}
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->loadTemplate('account-product-form');
		$this->response->setOutput($this->render());
	}
	
	public function update() {
		$product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;
		$clone = isset($this->request->get['clone']) ? (int)$this->request->get['clone'] : 0;
		$relist = isset($this->request->get['relist']) ? (int)$this->request->get['relist'] : 0;
		$seller_id = $this->customer->getId();
		
		if  ($this->MsLoader->MsProduct->productOwnedBySeller($product_id, $seller_id)) {
    		$product = $this->MsLoader->MsProduct->getProduct($product_id);
		} else {
			$product = NULL;
		}

		if (!$product)
			return $this->redirect($this->url->link('seller/account-product', '', 'SSL'));
			
		// Fees for re-listing
		if ($relist) {
			$commissions = $this->MsLoader->MsCommission->calculateCommission(array('seller_id' => $this->customer->getId()));
			$fee = (float)$commissions[MsCommission::RATE_LISTING]['flat'] + $commissions[MsCommission::RATE_LISTING]['percent'] * $product['price'] / 100;
			
			if ($fee > 0) {
				$this->MsLoader->MsProduct->changeStatus($product_id, MsProduct::STATUS_UNPAID);
			}
		}
			
		$this->_initForm();

		if (!empty($this->data['normal_attributes']) || !empty($this->data['multilang_attributes'])) {
			$a = $this->MsLoader->MsAttribute->getProductAttributeValues($product_id);
			$this->data['multilang_attribute_values'] = $a[1];
			$this->data['normal_attribute_values'] = $a[0]; 
		}
		
		$product['specials'] = $this->MsLoader->MsProduct->getProductSpecials($product_id);
		$product['discounts'] = $this->MsLoader->MsProduct->getProductDiscounts($product_id);
		
		if (!empty($product['thumbnail'])) {
		
			if ($clone){
				$oldPath				= DIR_IMAGE . $product['thumbnail'];
				$product['thumbnail']	= $this->config->get('msconf_temp_image_path') .basename($product['thumbnail']);
				copy($oldPath, DIR_IMAGE . $product['thumbnail']);
			}
			
			$product['images'][] = array(
				'name' => $product['thumbnail'],
				'thumb' => $this->MsLoader->MsFile->resizeImage($product['thumbnail'], $this->config->get('msconf_preview_product_image_width'), $this->config->get('msconf_preview_product_image_height'))
			);
			
			if (!in_array($product['thumbnail'], $this->session->data['multiseller']['files']))
				$this->session->data['multiseller']['files'][] = $product['thumbnail'];
		}
		
		$images = $this->MsLoader->MsProduct->getProductImages($product_id);
		foreach ($images as $image) {
			
			if ($clone){
				$oldPath		= DIR_IMAGE . $image['image'];
				$image['image'] = $this->config->get('msconf_temp_image_path') .basename($image['image']);
				copy($oldPath, DIR_IMAGE . $image['image']);
			}
			
			$product['images'][] = array(
				'name' => $image['image'],
				'thumb' => $this->MsLoader->MsFile->resizeImage($image['image'], $this->config->get('msconf_preview_product_image_width'), $this->config->get('msconf_preview_product_image_height'))
			);
			
			if (!in_array($image['image'], $this->session->data['multiseller']['files']))
				$this->session->data['multiseller']['files'][] = $image['image'];
		}

		$downloads = $this->MsLoader->MsProduct->getProductDownloads($product_id);
		$product['downloads'] = array();
		foreach ($downloads as $download) {
			
			//$download_seller = $this->MsLoader->MsSeller->getSeller($this->MsLoader->MsProduct->getSellerId($download['product_id']));
			
			if ($clone){
				$oldPath				= DIR_DOWNLOAD . $download['filename'];
				//$download['filename']	= time() . '_' . md5(rand()) . '.' . $this->MsLoader->MsSeller->getNickname() . substr($download['mask'], strlen($download_seller['ms.nickname']));
				$download['filename']	= time() . '_' . md5(rand()) . '.' . $download['mask'];
				copy($oldPath, DIR_DOWNLOAD . $this->config->get('msconf_temp_download_path') . $download['filename']);
			}
			
			//$ext = explode('.', $download['mask']); $ext = end($ext);
			$product['downloads'][] = array(
				'name' => $download['mask'],
				'src' => $download['filename'],
				//'href' => HTTPS_SERVER . 'download/' . $download['filename'],
				'href' => $this->url->link('seller/account-product/download', 'download_id=' . $download['download_id'] . '&product_id=' . $product_id, 'SSL'),
				'id' => $download['download_id'],
			);
			
			if (!in_array($download['filename'], $this->session->data['multiseller']['files']))
				$this->session->data['multiseller']['files'][] = $download['filename'];
		}

		$currencies = $this->model_localisation_currency->getCurrencies();
  		$decimal_place = $currencies[$this->config->get('config_currency')]['decimal_place'];
  		$decimal_point = $this->language->get('decimal_point');
  		$thousand_point = $this->language->get('thousand_point');
		//$product['price'] = number_format(round($this->currency->convert($product['price'], $this->MsLoader->MsProduct->getDefaultCurrency(), $_SESSION['currency'] ), (int)$decimal_place), (int)$decimal_place, $decimal_point, '');
		$product['price'] = number_format(round($product['price'], (int)$decimal_place), (int)$decimal_place, $decimal_point, $thousand_point);
		
        if(isset($product['manufacturer_id'])){
            $product['manufacturer_id'] = (int)$product['manufacturer_id'];
			$this->load->model('catalog/manufacturer');
            $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($product['manufacturer_id']);
            if ($manufacturer_info) {
                $product['manufacturer'] = $manufacturer_info['name'];
            } else {
                $product['manufacturer'] = '';
            }
        } else {
            $product['manufacturer_id'] = 0;
            $product['manufacturer'] = '';
        };

        if (isset($product['tax_class_id'])) {
            $product['tax_class_id'] = $product['tax_class_id'];
        } else {
            $product['tax_class_id'] = 0;
        }

        if (isset($product['stock_status_id'])) {
            $product['stock_status_id'] = $product['stock_status_id'];
        } else {
            $product['stock_status_id'] = $this->config->get('config_stock_status_id');
        }

        if (isset($product['date_available'])) {
            $this->data['date_available'] = date('Y-m-d', strtotime($product['date_available']));
        }

        $this->data['product'] = $product;
		$this->data['product']['category_id'] = $this->MsLoader->MsProduct->getProductCategories($product_id);
		
		$breadcrumbs = array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_dashboard_breadcrumbs'),
				'href' => $this->url->link('seller/account-dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_products_breadcrumbs'),
				'href' => $this->url->link('seller/account-product', '', 'SSL'),
			)
		);
		
		if ($clone) {
			$this->data['product']['product_id'] = 0;
			$this->data['product']['cloned_product_id'] = $product_id;
			$this->data['clone'] = 1;
			// Product listing period
			if ($this->data['seller_group']['product_period'] > 0) {
				$this->data['list_until'] = date('Y-m-d', strtotime(date('Y-m-d')) + (24 * 3600 * $this->data['seller_group']['product_period']));
			} else {
				$this->data['list_until'] = NULL;
			}
			
			$breadcrumbs[] = array(
				'text' => $this->language->get('ms_account_cloneproduct_breadcrumbs'),
				'href' => $this->url->link('seller/account-product/update', '', 'SSL'),
			);
			$this->data['heading'] = $this->language->get('ms_account_cloneproduct_heading');
			$this->document->setTitle($this->language->get('ms_account_cloneproduct_heading'));
		} else if ($relist) {
			// Product listing period
			if ($this->data['seller_group']['product_period'] > 0) {
				$this->data['list_until'] = date('Y-m-d', strtotime(date('Y-m-d')) + (24 * 3600 * $this->data['seller_group']['product_period']));
			} else {
				$this->data['list_until'] = NULL;
			}
			
			$breadcrumbs[] = array(
				'text' => $this->language->get('ms_account_relist_product_breadcrumbs'),
				'href' => $this->url->link('seller/account-product/update', '', 'SSL'),
			);
			$this->data['heading'] = $this->language->get('ms_account_relist_product_heading');
			$this->document->setTitle($this->language->get('ms_account_relist_product_heading'));
		} else {
			$breadcrumbs[] = array(
				'text' => $this->language->get('ms_account_editproduct_breadcrumbs'),
				'href' => $this->url->link('seller/account-product/update', '', 'SSL'),
			);
			$this->data['heading'] = $this->language->get('ms_account_editproduct_heading');
			$this->document->setTitle($this->language->get('ms_account_editproduct_heading'));
		}
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs($breadcrumbs);
	
		list($this->template, $this->children) = $this->MsLoader->MsHelper->loadTemplate('account-product-form');
		$this->response->setOutput($this->render());
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
		
		if (!$this->MsLoader->MsProduct->hasDownload($product_id,$download_id) || !$this->MsLoader->MsProduct->productOwnedBySeller($product_id,$this->customer->getId()))
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
