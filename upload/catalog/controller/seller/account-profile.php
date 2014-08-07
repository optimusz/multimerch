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
				$thumbUrl = $this->MsLoader->MsFile->resizeImage($this->config->get('msconf_temp_image_path') . $fileName, $this->config->get('msconf_preview_seller_avatar_image_width'), $this->config->get('msconf_preview_seller_avatar_image_height'));
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
		$json['redirect'] = $this->url->link('seller/account-dashboard');
		
		if (!empty($seller) && (in_array($seller['ms.seller_status'], array(MsSeller::STATUS_DISABLED, MsSeller::STATUS_DELETED)))) {
			return $this->response->setOutput(json_encode($json));
		}
		
		if ($this->config->get('msconf_change_seller_nickname') || empty($seller)) {
			// seller doesn't exist yet
			if (empty($data['seller']['nickname'])) {
				$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_empty'); 
			} else if (mb_strlen($data['seller']['nickname']) < 4 || mb_strlen($data['seller']['nickname']) > 128 ) {
				$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_length');			
			} else if ( ($data['seller']['nickname'] != $seller['ms.nickname']) && ($this->MsLoader->MsSeller->nicknameTaken($data['seller']['nickname'])) ) {
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
		} else {
			$data['seller']['nickname'] = $seller['ms.nickname'];
		}
		
		if (empty($seller)) {
			if ($this->config->get('msconf_seller_terms_page')) {
				$this->load->model('catalog/information');
				$information_info = $this->model_catalog_information->getInformation($this->config->get('msconf_seller_terms_page'));
				
				if ($information_info && !isset($data['seller']['terms'])) {
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
		
		if (($data['seller']['paypal'] != "") && ((utf8_strlen($data['seller']['paypal']) > 128) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $data['seller']['paypal']))) {
			$json['errors']['seller[paypal]'] = $this->language->get('ms_error_sellerinfo_paypal');
		}
		
		if (isset($data['seller']['avatar_name']) && !empty($data['seller']['avatar_name'])) {
			if ($this->config->get('msconf_avatars_for_sellers') == 2 && !$this->MsLoader->MsFile->checkPredefinedAvatar($data['seller']['avatar_name'])) {
				$json['errors']['seller[avatar]'] = $this->language->get('ms_error_file_upload_error');
			} elseif ($this->config->get('msconf_avatars_for_sellers') == 1 && !$this->MsLoader->MsFile->checkPredefinedAvatar($data['seller']['avatar_name']) && !$this->MsLoader->MsFile->checkFileAgainstSession($data['seller']['avatar_name'])) {
				$json['errors']['seller[avatar]'] = $this->language->get('ms_error_file_upload_error');
			} elseif ($this->config->get('msconf_avatars_for_sellers') == 0 && !$this->MsLoader->MsFile->checkFileAgainstSession($data['seller']['avatar_name'])) {
				$json['errors']['seller[avatar]'] = $this->language->get('ms_error_file_upload_error');
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
				$data['seller']['description'] = htmlspecialchars(strip_tags(htmlspecialchars_decode($data['seller']['description'], ENT_COMPAT), $allowed_tags_ready), ENT_COMPAT, 'UTF-8');
			}
		} else {
			$data['seller']['description'] = htmlspecialchars(nl2br($data['seller']['description']), ENT_COMPAT, 'UTF-8');
		}
		
		// uncomment to enable RTE for message field
		/*
		if(isset($data['reviewer_message'])) {
			$data['seller']['reviewer_message'] = strip_tags(html_entity_decode($data['seller']['reviewer_message']), $allowed_tags_ready);
		}
		*/

		if (empty($json['errors'])) {
			$mails = array();
			unset($data['seller']['commission']);
			
			if ($this->config->get('msconf_change_seller_nickname') || empty($seller)) {
				// SEO urls generation for sellers
				if ($this->config->get('msconf_enable_seo_urls_seller')) {
					$latin_check = '/[^\x{0030}-\x{007f}]/u';
					$non_latin_chars = preg_match($latin_check, $data['seller']['nickname']);
					if ($this->config->get('msconf_enable_non_alphanumeric_seo') && $non_latin_chars) {
						$data['seller']['keyword'] = implode("-", str_replace("-", "", explode(" ", strtolower($data['seller']['nickname']))));
					}
					else {
						$data['seller']['keyword'] = trim(implode("-", str_replace("-", "", explode(" ", preg_replace("/[^A-Za-z0-9 ]/", '', strtolower($data['seller']['nickname']))))), "-");
					}
				}
			}
			
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
								'message' => $data['seller']['reviewer_message'],
								'seller_name' => $data['seller']['nickname'],
								'customer_name' => $this->customer->getFirstname() . ' ' . $this->customer->getLastname(),
								'customer_email' => $this->MsLoader->MsSeller->getSellerEmail($this->customer->getId())
							)
						);
						$data['seller']['status'] = MsSeller::STATUS_INACTIVE;
						if ($this->config->get('msconf_allow_inactive_seller_products')) {
							$json['redirect'] = $this->url->link('account/account');
						} else {
							$json['redirect'] = $this->url->link('seller/account-profile');
						}
						break;
					
					case MsSeller::MS_SELLER_VALIDATION_NONE:
					default:
						$mails[] = array(
							'type' => MsMail::SMT_SELLER_ACCOUNT_CREATED
						);
						$mails[] = array(
							'type' => MsMail::AMT_SELLER_ACCOUNT_CREATED,
							'data' => array(
								'seller_name' => $data['seller']['nickname'],
								'customer_name' => $this->customer->getFirstname() . ' ' . $this->customer->getLastname(),
								'customer_email' => $this->MsLoader->MsSeller->getSellerEmail($this->customer->getId())
							)
						);
						$data['seller']['status'] = MsSeller::STATUS_ACTIVE;
						$data['seller']['approved'] = 1;
						break;
				}
				
				$data['seller']['seller_id'] = $this->customer->getId();
				$data['seller']['product_validation'] = $this->config->get('msconf_product_validation'); 
				$this->MsLoader->MsSeller->createSeller($data['seller']);
				
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
				
				$this->session->data['success'] = $this->language->get('ms_account_sellerinfo_saved');
			} else {
				// edit seller
				$data['seller']['seller_id'] = $seller['seller_id'];
				$this->MsLoader->MsSeller->editSeller($data['seller']);
				
				if ($seller['ms.seller_status'] == MsSeller::STATUS_UNPAID) {
					$commissions = $this->MsLoader->MsCommission->calculateCommission(array('seller_group_id' => $this->config->get('msconf_default_seller_group_id')));
					$fee = (float)$commissions[MsCommission::RATE_SIGNUP]['flat'];
					
					if ($fee > 0) {
						switch($commissions[MsCommission::RATE_SIGNUP]['payment_method']) {
							case MsPayment::METHOD_PAYPAL:
								// initiate paypal payment
								
								// set product status to unpaid
								$this->MsLoader->MsSeller->changeStatus($this->customer->getId(), MsSeller::STATUS_UNPAID);
								
								// check if payment exists
								$payment = $this->MsLoader->MsPayment->getPayments(array(
									'seller_id' => $this->customer->getId(),
									'payment_type' => array(MsPayment::TYPE_SIGNUP),
									'payment_status' => array(MsPayment::STATUS_UNPAID),
									'payment_method' => array(MsPayment::METHOD_PAYPAL),
									'single' => 1
								));
								
								if (!$payment) {
									// create new payment
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
								} else {
									$payment_id = $payment['payment_id'];
									
									// edit payment
									$this->MsLoader->MsPayment->updatePayment($payment_id, array(
										'amount' => $fee,
										'date_created' => 1,
										'description' => sprintf($this->language->get('ms_transaction_signup'), $this->config->get('config_name'))
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
										'balance_type' => MsBalance::MS_BALANCE_TYPE_SIGNUP,
										'amount' => -$fee,
										'description' => sprintf($this->language->get('ms_transaction_signup'), $this->config->get('config_name'))
									)
								);
								
								break;
						}
					}
				}
				
				$this->session->data['success'] = $this->language->get('ms_account_sellerinfo_saved');
			}

            /*------------------------------Remove seller cache-----------------------------------------*/
            $this->cache->delete("seller" . $data['seller']['seller_id']);
            $this->cache->delete("catalog_seller");
            $this->cache->delete("catalog_seller_total");
            $this->cache->delete("ms_carousel");
            $this->cache->delete("ms_newsellers");
            $this->cache->delete("ms_topsellers");
            /*----------------------------------------------------------------------------------------------------*/
		}
		
		$this->response->setOutput(json_encode($json));
	}

	public function index() {
		$this->document->addScript('catalog/view/javascript/account-seller-profile.js');
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

		$seller = $this->MsLoader->MsSeller->getSeller($this->customer->getId());
		
		$this->data['salt'] = $this->MsLoader->MsSeller->getSalt($this->customer->getId());
		$this->data['statusclass'] = 'attention';

		if ($seller) {
			switch ($seller['ms.seller_status']) {
				case MsSeller::STATUS_UNPAID:
					$this->data['statusclass'] = 'attention';
					break;				
				case MsSeller::STATUS_ACTIVE:
					$this->data['statusclass'] = 'success';
					break;
				case MsSeller::STATUS_DISABLED:
				case MsSeller::STATUS_DELETED:
					$this->data['statusclass'] = 'warning';
					break;
			}
			
			$this->data['seller'] = $seller;
			$this->data['country_id'] = $seller['ms.country_id'];

			if (!empty($seller['ms.avatar'])) {
				$this->data['seller']['avatar']['name'] = $seller['ms.avatar'];
				$this->data['seller']['avatar']['thumb'] = $this->MsLoader->MsFile->resizeImage($seller['ms.avatar'], $this->config->get('msconf_preview_seller_avatar_image_width'), $this->config->get('msconf_preview_seller_avatar_image_height'));
				$this->session->data['multiseller']['files'][] = $seller['ms.avatar'];
			}

			$this->data['statustext'] = $this->language->get('ms_account_status') . $this->language->get('ms_seller_status_' . $seller['ms.seller_status']);
			
			if ($seller['ms.seller_status'] == MsSeller::STATUS_INACTIVE && !$seller['ms.seller_approved']) {
				$this->data['statustext'] .= $this->language->get('ms_account_status_tobeapproved');
			}
			
			$this->data['ms_account_sellerinfo_terms_note'] = '';
		} else {
			$this->data['seller'] = FALSE;
			$this->data['country_id'] = $this->config->get('config_country_id');


			$this->data['statustext'] = $this->language->get('ms_account_status_please_fill_in');
			
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
		
		if (!$seller || $seller['ms.seller_status'] == MsSeller::STATUS_UNPAID) {
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
				'text' => $this->language->get('ms_account_dashboard_breadcrumbs'),
				'href' => $this->url->link('seller/account-dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_sellerinfo_breadcrumbs'),
				'href' => $this->url->link('seller/account-profile', '', 'SSL'),
			)
		));

		// Get avatars
		if ($this->config->get('msconf_avatars_for_sellers') == 1 || $this->config->get('msconf_avatars_for_sellers') == 2) {
			$this->data['predefined_avatars'] = $this->MsLoader->MsFile->getPredefinedAvatars();
		}
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->loadTemplate('account-profile');
		$this->response->setOutput($this->render());
	}
}
?>
