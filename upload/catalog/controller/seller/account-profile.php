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
				$thumbUrl = $this->MsLoader->MsFile->resizeImage($this->MsLoader->MsFile->getTmpPath() . $fileName, $this->config->get('msconf_image_preview_width'), $this->config->get('msconf_image_preview_height'));
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
		$seller = $this->MsLoader->MsSeller->getSellerData($this->customer->getId());
		$json = array();
		
		if (!empty($seller) && ($seller['seller_status'] != MsSeller::STATUS_ACTIVE)) {
			return $this->response->setOutput(json_encode($json));
		}
		
		if (empty($seller)) {
			// seller doesn't exist yet
			if (empty($data['sellerinfo_nickname'])) {
				$json['errors']['sellerinfo_nickname'] = $this->language->get('ms_error_sellerinfo_nickname_empty'); 
			} else if (!ctype_alnum($data['sellerinfo_nickname'])) {
				$json['errors']['sellerinfo_nickname'] = $this->language->get('ms_error_sellerinfo_nickname_alphanumeric');
			} else if (mb_strlen($data['sellerinfo_nickname']) < 4 || mb_strlen($data['sellerinfo_nickname']) > 50 ) {
				$json['errors']['sellerinfo_nickname'] = $this->language->get('ms_error_sellerinfo_nickname_length');			
			} else if ($this->MsLoader->MsSeller->nicknameTaken($data['sellerinfo_nickname'])) {
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
			if (!$this->MsLoader->MsFile->checkFileAgainstSession($data['sellerinfo_avatar_name'])) {
				$json['errors']['sellerinfo_avatar'] = $this->language->get('ms_error_file_upload_error');
			}
		}
		
		if (empty($json['errors'])) {
			$mails = array();
			if (empty($seller)) {
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
								'message' => $data['sellerinfo_reviewer_message']
							)
						);
						$data['seller_status'] = MsSeller::STATUS_INACTIVE;

						$this->MsLoader->MsRequestSeller->createSellerRequest($this->customer->getId(),
							array(
								'message' => $data['sellerinfo_reviewer_message'],
								'request_type' => MsRequestSeller::TYPE_SELLER_CREATE
							)
						);
						break;
					
					case MsSeller::MS_SELLER_VALIDATION_NONE:
					default:
						$mails[] = array(
							'type' => MsMail::SMT_SELLER_ACCOUNT_CREATED
						);
						$mails[] = array(
							'type' => MsMail::AMT_SELLER_ACCOUNT_CREATED
						);					
						$data['seller_status'] = MsSeller::STATUS_ACTIVE;
						break;
				}
				
				// SEO urls generation for sellers
				if ($this->config->get('msconf_enable_seo_urls')) {
					$latin_check = '/[^\x{0030}-\x{007f}]/u';
					$non_latin_chars = preg_match($latin_check, $_POST['full_name']);
					if ($this->config->get('msconf_enable_non_alphanumeric_seo') && $non_latin_chars) {
						$data['keyword'] = implode("-", str_replace("-", "", explode(" ", strtolower($data['sellerinfo_nickname']))));
					}
					else {
						$data['keyword'] = implode("-", str_replace("-", "", explode(" ", preg_replace("/[^A-Za-z0-9 ]/", '', strtolower($data['sellerinfo_nickname'])))));
					}
				}
				
				$data['seller_id'] = $this->customer->getId();
				$data['sellerinfo_product_validation'] = $this->config->get('msconf_product_validation'); 
				$this->MsLoader->MsSeller->createSeller($data);
				$this->MsLoader->MsMail->sendMails($mails);
				$this->session->data['success'] = $this->language->get('ms_account_sellerinfo_saved');
			} else {
				// edit seller
				$data['seller_id'] = $seller['seller_id'];
				$this->MsLoader->MsSeller->editSeller($data);
				$this->session->data['success'] = $this->language->get('ms_account_sellerinfo_saved');
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}

	public function index() {
		$this->document->addScript('catalog/view/javascript/jquery.form.js');
		$this->document->addScript('catalog/view/javascript/account-seller-profile.js');
		$this->document->addScript('catalog/view/javascript/jquery.uploadify.js');
		
		$this->load->model('localisation/country');
    	$this->data['countries'] = $this->model_localisation_country->getCountries();		

		$seller = $this->MsLoader->MsSeller->getSellers(
			array(
				'seller_id' => $this->customer->getId()
			),
			array(
				'offset' => 0,			
				'limit' => 1
			)
		);
		
		$this->data['salt'] = $this->MsLoader->MsSeller->getSalt($this->customer->getId());
		
		if (!empty($seller)) {
			$this->data['seller'] = $seller;
			if (!empty($seller['avatar_path'])) {
				$this->data['seller']['avatar']['name'] = $seller['avatar_path'];
				$this->data['seller']['avatar']['thumb'] = $this->MsLoader->MsFile->resizeImage($seller['avatar_path'], $this->config->get('msconf_image_preview_width'), $this->config->get('msconf_image_preview_height'));
				$this->session->data['multiseller']['files'][] = $seller['avatar_path'];
			}
			
			switch ($seller['seller_status']) {
				case MsSeller::STATUS_INACTIVE:
					$this->data['statustext'] = $this->language->get('ms_account_status') . $this->language->get('ms_account_status_activation');
					break;
				case MsSeller::STATUS_INACTIVE:
					$this->data['statustext'] = $this->language->get('ms_account_status') . $this->language->get('ms_account_status_approval');
					break;
				case MsSeller::STATUS_DISABLED:
					$this->data['statustext'] = $this->language->get('ms_account_status') . $this->language->get('ms_account_status_disabled');
					break;					
				case MsSeller::STATUS_ACTIVE:
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
