<?php

class ControllerSellerAccountProfile extends ControllerSellerAccount {
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
	
	public function jxSaveSellerInfo() {
		$data = $this->request->post;
		$seller = $this->MsLoader->MsSeller->getSeller($this->customer->getId());
		$json = array();
		
		if (!empty($seller) && (in_array($seller['ms.seller_status'], array(MsSeller::STATUS_DISABLED, MsSeller::STATUS_DELETED)))) {
			return $this->response->setOutput(json_encode($json));
		}
		
		if (empty($seller)) {
			// seller doesn't exist yet
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
							$json['errors']['seller[nickname]'] = 'only extended latin';
						}
						break;
						
					case 2:
						// utf8
						if(!preg_match("/((?:[\x01-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3}){1,100})./x", $data['seller']['nickname'])) {
							$json['errors']['seller[nickname]'] = 'invalid ebanij';
						}
						break;
						
					case 0:
					default:
						// alnum
						if(!preg_match("/^[a-zA-Z0-9_\-\s]+$/", $data['seller']['nickname'])) {
							$json['errors']['seller[nickname]'] = 'only alphanumerics allowed';
						}
						break;
				}
			}
			
			if ($this->config->get('msconf_seller_terms_page')) {
				$this->load->model('catalog/information');
				$information_info = $this->model_catalog_information->getInformation($this->config->get('msconf_seller_terms_page'));
				
				if ($information_info && !isset($data['accept_terms'])) {
	      			$json['errors']['seller[terms]'] = htmlspecialchars_decode(sprintf($this->language->get('ms_error_sellerinfo_terms'), $information_info['title']));
				}
			}			
		}
		
		if (mb_strlen($data['seller']['company']) > 50 ) {
			$json['errors']['seller[company]'] = $this->language->get('ms_error_sellerinfo_company_length');			
		}
		
		if (mb_strlen($data['seller']['description']) > 1000) {
			$json['errors']['seller[description]'] = $this->language->get('ms_error_sellerinfo_description_length');			
		}

		if (mb_strlen($data['seller']['paypal']) > 256) {
			$json['errors']['seller[paypal]'] = $this->language->get('ms_error_sellerinfo_paypal');			
		}
		
		if (isset($data['seller']['avatar_name']) && !empty($data['seller']['avatar_name'])) {
			if (!$this->MsLoader->MsFile->checkFileAgainstSession($data['seller']['avatar_name'])) {
				$json['errors']['seller[avatar]'] = $this->language->get('ms_error_file_upload_error');
			}
		}
		
		if (empty($json['errors'])) {
			$mails = array();
			unset($data['seller']['commission']);
			if (empty($seller)) {
				$data['seller']['approved'] = 0;
				// create new seller
				switch ($this->config->get('msconf_seller_validation')) {
					/*
					case MsSeller::MS_SELLER_VALIDATION_ACTIVATION:
						$data['seller_status'] = MsSeller::STATUS_TOBEACTIVATED;
						break;
					*/
					
					case MsSeller::MS_SELLER_VALIDATION_APPROVAL:
						$mails[] = array(
							'type' => MsMail::SMT_SELLER_ACCOUNT_AWAITING_MODERATION
						);
						$mails[] = array(
							'type' => MsMail::AMT_SELLER_ACCOUNT_AWAITING_MODERATION,
							'data' => array(
								'message' => $data['seller']['reviewer_message']
							)
						);
						$data['seller']['status'] = MsSeller::STATUS_INACTIVE;
						break;
					
					case MsSeller::MS_SELLER_VALIDATION_NONE:
					default:
						$mails[] = array(
							'type' => MsMail::SMT_SELLER_ACCOUNT_CREATED
						);
						$mails[] = array(
							'type' => MsMail::AMT_SELLER_ACCOUNT_CREATED
						);					
						$data['seller']['status'] = MsSeller::STATUS_ACTIVE;
						$data['seller']['approved'] = 1;
						break;
				}
				
				// SEO urls generation for sellers
				if ($this->config->get('msconf_enable_seo_urls_seller')) {
					$latin_check = '/[^\x{0030}-\x{007f}]/u';
					$non_latin_chars = preg_match($latin_check, $data['seller']['nickname']);
					if ($this->config->get('msconf_enable_non_alphanumeric_seo') && $non_latin_chars) {
						$data['keyword'] = implode("-", str_replace("-", "", explode(" ", strtolower($data['seller']['nickname']))));
					}
					else {
						$data['keyword'] = implode("-", str_replace("-", "", explode(" ", preg_replace("/[^A-Za-z0-9 ]/", '', strtolower($data['seller']['nickname'])))));
					}
				}
				
				$data['seller']['seller_id'] = $this->customer->getId();
				$data['seller']['product_validation'] = $this->config->get('msconf_product_validation'); 
				$this->MsLoader->MsSeller->createSeller($data['seller']);
				
				$commissions = $this->MsLoader->MsCommission->calculateCommission(array('seller_group_id' => $this->config->get('msconf_default_seller_group_id')));
				$fee = (float)$commissions[MsCommission::RATE_SIGNUP]['flat'];
				
				if ($fee > 0) {
				// 	todo
					switch(MsCommission::PAYMENT_TYPE_BALANCE) {
						case MsCommission::PAYMENT_TYPE_BALANCE:
							// deduct from balance
							$this->MsLoader->MsBalance->addBalanceEntry($this->customer->getId(),
								array(
									'balance_type' => MsBalance::MS_BALANCE_TYPE_SIGNUP,
									'amount' => -$fee,
									'description' => sprintf($this->language->get('ms_transaction_signup'), $this->config->get('config_name'))
								)
							);
							
							break;
					}
				}

				$this->MsLoader->MsMail->sendMails($mails);
				$this->session->data['success'] = $this->language->get('ms_account_sellerinfo_saved');
			} else {
				// edit seller
				$data['seller']['seller_id'] = $seller['seller_id'];
				$this->MsLoader->MsSeller->editSeller($data['seller']);
				$this->session->data['success'] = $this->language->get('ms_account_sellerinfo_saved');
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}

	public function index() {
		$this->document->addScript('catalog/view/javascript/account-seller-profile.js');
		//$this->document->addScript('catalog/view/javascript/jquery.uploadify.js');
		//$this->document->addScript('http://bp.yahooapis.com/2.4.21/browserplus-min.js');
		$this->document->addScript('catalog/view/javascript/plupload/plupload.full.js');
		$this->document->addScript('catalog/view/javascript/plupload/jquery.plupload.queue/jquery.plupload.queue.js');
		
		$this->load->model('localisation/country');
    	$this->data['countries'] = $this->model_localisation_country->getCountries();		

		$seller = $this->MsLoader->MsSeller->getSeller($this->customer->getId());
		
		$this->data['salt'] = $this->MsLoader->MsSeller->getSalt($this->customer->getId());
		$this->data['statusclass'] = 'attention';
		if ($seller) {
			switch ($seller['ms.seller_status']) {
				case MsSeller::STATUS_ACTIVE:
					$this->data['statusclass'] = 'success';
					break;
				case MsSeller::STATUS_DISABLED:
				case MsSeller::STATUS_DELETED:
					$this->data['statusclass'] = 'warning';
					break;
			}
			
			$this->data['seller'] = $seller;
			if (!empty($seller['ms.avatar'])) {
				$this->data['seller']['avatar']['name'] = $seller['ms.avatar'];
				$this->data['seller']['avatar']['thumb'] = $this->MsLoader->MsFile->resizeImage($seller['ms.avatar'], $this->config->get('msconf_image_preview_width'), $this->config->get('msconf_image_preview_height'));
				$this->session->data['multiseller']['files'][] = $seller['ms.avatar'];
			}

			$this->data['statustext'] = $this->language->get('ms_account_status') . $this->MsLoader->MsSeller->getStatusText($seller['ms.seller_status']);
			
			if ($seller['ms.seller_status'] == MsSeller::STATUS_INACTIVE && !$seller['ms.seller_approved']) {
				$this->data['statustext'] .= $this->language->get('ms_account_status_tobeapproved');
			}
			
			$this->data['ms_account_sellerinfo_terms_note'] = '';
			/*			
			switch ($status_data['seller_status']['id']) {
				case MsSeller::STATUS_DELETED:
					 //$this->data['statustext'] .= $this->language->get('ms_account_status_activation');
					break;
				case MsSeller::STATUS_INACTIVE:
					//$this->data['statustext'] .=  $this->language->get('ms_account_status') . $this->language->get('ms_account_status_approval');
					break;
				case MsSeller::STATUS_DISABLED:
					//$this->data['statustext'] .= $this->language->get('ms_account_status_disabled');
					break;					
				case MsSeller::STATUS_ACTIVE:
				default:
					$this->data['statustext'] = '';
					//$this->data['statustext'] = $this->language->get('ms_account_status') . $this->language->get('ms_account_status_active');
					//$this->data['statustext'] .= '<br />' . $this->language->get('ms_account_status_fullaccess');
					break;
			
			}
			*/
		} else {
			$this->data['seller'] = FALSE;
			$this->data['group_commissions'] = $this->MsLoader->MsCommission->calculateCommission(array('seller_group_id' => $this->config->get('msconf_default_seller_group_id')));
			$this->data['statustext'] = $this->language->get('ms_account_status_please_fill_in');
			$this->data['ms_fee_payment_type'] = $this->language->get('ms_account_sellerinfo_fee_balance');
			
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
		}

		$this->data['seller_validation'] = $this->config->get('msconf_seller_validation');
		$this->data['link_back'] = $this->url->link('account/account', '', 'SSL');
		$this->document->setTitle($this->language->get('ms_account_sellerinfo_heading'));
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_sellerinfo_breadcrumbs'),
				'href' => $this->url->link('seller/account-profile', '', 'SSL'),
			)
		));
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->loadTemplate('account-profile');
		$this->response->setOutput($this->render());
	}
}
?>
