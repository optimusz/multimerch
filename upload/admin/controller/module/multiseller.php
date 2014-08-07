<?php

class ControllerModuleMultiseller extends ControllerMultisellerBase {
	private $_controllers = array(
		"multiseller/base",
		"multiseller/product",
		"multiseller/attribute",
		"multiseller/payment",
		"multiseller/seller",
		"multiseller/transaction",
		"multiseller/seller-group"
	);
	
	private $settings = array(
		"msconf_seller_validation" => MsSeller::MS_SELLER_VALIDATION_NONE,
		"msconf_product_validation" => MsProduct::MS_PRODUCT_VALIDATION_NONE,
		"msconf_allow_inactive_seller_products" => 0,
		"msconf_nickname_rules" => 0, // 0 - alnum, 1 - latin extended, 2 - utf
		"msconf_credit_order_statuses" => array(5),
		"msconf_debit_order_statuses" => array(8),
		"msconf_minimum_withdrawal_amount" => "50",
		"msconf_allow_partial_withdrawal" => 1,
		
		"msconf_paypal_sandbox" => 1,
		"msconf_paypal_address" => "",
		
		"msconf_allow_withdrawal_requests" => 1,
		"msconf_allowed_image_types" => 'png,jpg,jpeg',
		"msconf_allowed_download_types" => 'zip,rar,pdf',
		"msconf_minimum_product_price" => 0,
		"msconf_maximum_product_price" => 0,
		"msconf_notification_email" => "",
		"msconf_allow_free_products" => 0,
		
		"msconf_allow_multiple_categories" => 0,
		"msconf_additional_category_restrictions" => 0, // 0 - none, 1 - topmost, 2 - all parents
		"msconf_restrict_categories" => array(),
		"msconf_product_included_fields" => array(),
		
		"msconf_images_limits" => array(0,0),
		"msconf_downloads_limits" => array(0,0),
		
		"msconf_enable_shipping" => 0, // 0 - no, 1 - yes, 2 - seller select
		"msconf_provide_buyerinfo" => 0, // 0 - no, 1 - yes, 2 - shipping dependent
		"msconf_enable_quantities" => 0, // 0 - no, 1 - yes, 2 - shipping dependent
        "msconf_enable_categories" => 0, // 0 - no, 1 - yes
        "msconf_physical_product_categories" => array(),
        "msconf_digital_product_categories" => array(),

		"msconf_disable_product_after_quantity_depleted" => 0,
		"msconf_allow_relisting" => 0,
		
		"msconf_enable_seo_urls_seller" => 0,
		"msconf_enable_seo_urls_product" => 0,
		"msconf_enable_update_seo_urls" => 0,
		"msconf_enable_non_alphanumeric_seo" => 0,
		"msconf_product_image_path" => 'sellers/',
		"msconf_predefined_avatars_path" => 'avatars/',
		"msconf_temp_image_path" => 'tmp/',
		"msconf_temp_download_path" => 'tmp/',
		"msconf_seller_terms_page" => "",
		"msconf_default_seller_group_id" => 1,
		"msconf_allow_specials" => 1,
		"msconf_allow_discounts" => 1,
		"msconf_withdrawal_waiting_period" => 0,
		"msconf_graphical_sellermenu" => 1,
		
		"msconf_enable_rte" => 0,
		"msconf_rte_whitelist" => "",
		
		"msconf_seller_avatar_seller_profile_image_width" => 100,
		"msconf_seller_avatar_seller_profile_image_height" => 100,
		"msconf_seller_avatar_seller_list_image_width" => 100,
		"msconf_seller_avatar_seller_list_image_height" => 100,
		"msconf_seller_avatar_product_page_image_width" => 100,
		"msconf_seller_avatar_product_page_image_height" => 100,
		"msconf_seller_avatar_dashboard_image_width" => 100,
		"msconf_seller_avatar_dashboard_image_height" => 100,
		"msconf_preview_seller_avatar_image_width" => 100,
		"msconf_preview_seller_avatar_image_height" => 100,
		"msconf_preview_product_image_width" => 100,
		"msconf_preview_product_image_height" => 100,
		"msconf_product_seller_profile_image_width" => 100,
		"msconf_product_seller_profile_image_height" => 100,
		"msconf_product_seller_products_image_width" => 100,
		"msconf_product_seller_products_image_height" => 100,
		"msconf_product_seller_product_list_seller_area_image_width" => 40,
		"msconf_product_seller_product_list_seller_area_image_height" => 40,
		
		"msconf_min_uploaded_image_width" => 0,
		"msconf_min_uploaded_image_height" => 0,
		"msconf_max_uploaded_image_width" => 0,
		"msconf_max_uploaded_image_height" => 0,
		
		"msconf_sellers_slug" => "sellers",
		
		"msconf_attribute_display" => 0, // 0 - MM, 1 - OC, 2 - both
		
		"msconf_hide_customer_email" => 0,
		"msconf_hide_emails_in_emails" => 0,
		"msconf_hide_sellers_product_count" => 1,
		"msconf_avatars_for_sellers" => 0, // 0 - Uploaded manually by seller, 1 - Both, uploaded by seller and pre-defined, 2 - Only pre-defined
		"msconf_change_seller_nickname" => 1,

		"msconf_enable_private_messaging" => 1, // 0 - no, 2 - yes (email only)
		"msconf_enable_one_page_seller_registration" => 0 // 0 - no, 1 - yes
	);
	
	public function __construct($registry) {
		parent::__construct($registry);	
		$this->registry = $registry;
	}
	
	private function _editSettings() {
		$this->load->model('setting/setting');
		$this->load->model('setting/extension');
		
		$set = $this->model_setting_setting->getSetting('multiseller');
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
				if (isset($this->settings[$s]))
					$this->data[$s] = $this->settings[$s];
			}
		}
		
		$this->model_setting_setting->editSetting('multiseller', $set);

		foreach ($extensions_to_be_installed as $ext) {
			$this->model_setting_extension->install('module',$ext);	
		}
	}
	public function install() {
		$this->validate(__FUNCTION__);
		$this->load->model("multiseller/install");
		$this->load->model('setting/setting');
		
		$this->model_multiseller_install->createSchema();
		$this->model_multiseller_install->createData();
		$this->model_setting_setting->editSetting('multiseller', $this->settings);
		
		$this->load->model('user/user_group');
		
		foreach ($this->_controllers as $c) {
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', $c);
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', $c);
		} 

		$dirs = array(
			DIR_IMAGE . $this->settings['msconf_product_image_path'],
			DIR_IMAGE . $this->settings['msconf_temp_image_path'],
			DIR_DOWNLOAD . $this->settings['msconf_temp_download_path']
		);
		
		$this->session->data['success'] = $this->language->get('ms_success_installed');
		$this->session->data['error'] = "";
		
		foreach ($dirs as $dir) {
			if (!file_exists($dir)) {
				if (!mkdir($dir, 0755)) {
					$this->session->data['error'] .= sprintf($this->language->get('ms_error_directory'), $dir);
				}
			} else {
				if (!is_writable($dir)) {
					$this->session->data['error'] .= sprintf($this->language->get('ms_error_directory_notwritable'), $dir);
				} else {
					$this->session->data['error'] .= sprintf($this->language->get('ms_error_directory_exists'), $dir);
				}
			}
		}
		
		// ckeditor
		/*if (!copy(DIR_APPLICATION . 'view/javascript/ckeditor/', DIR_CATALOG . 'view/javascript/multimerch/')) {
			$this->session->data['error'] .= sprintf($this->language->get('ms_error_ckeditor'), DIR_APPLICATION . 'view/javascript/ckeditor/', DIR_CATALOG . 'view/javascript/multimerch/');
		}*/
		$this->session->data['error'] .= sprintf($this->language->get('ms_notice_ckeditor'), DIR_APPLICATION . 'view/javascript/ckeditor/', DIR_CATALOG . 'view/javascript/multimerch/ckeditor/');
	}

	public function uninstall() {
		$this->validate(__FUNCTION__);
		$this->load->model("multiseller/install");
		$this->model_multiseller_install->deleteSchema();
		$this->model_multiseller_install->deleteData();
	}	

	public function saveSettings() {
		$this->validate(__FUNCTION__);
		
		/*magic
		$this->request->post['msconf_allowed_image_types'] = 'png,jpg';
		$this->request->post['msconf_allowed_download_types'] = 'zip,rar,pdf';
		
		$this->request->post['msconf_paypal_sandbox'] = 1;
		magic*/

		if (!isset($this->request->post['msconf_credit_order_statuses']))
			$this->request->post['msconf_credit_order_statuses'] = array();
		
		if (!isset($this->request->post['msconf_debit_order_statuses']))
			$this->request->post['msconf_debit_order_statuses'] = array();
		
		if (!isset($this->request->post['msconf_product_options']))
			$this->request->post['msconf_product_options'] = array();

		if (!isset($this->request->post['msconf_restrict_categories']))
			$this->request->post['msconf_restrict_categories'] = array();

        if (!isset($this->request->post['msconf_product_included_fields']))
			$this->request->post['msconf_product_included_fields'] = array();
		
		// todo setting validation
		$this->_editSettings();
		$json = array();
		$this->response->setOutput(json_encode($json));
	}
	
	public function index() {
		$this->validate(__FUNCTION__);
		
		foreach($this->settings as $s=>$v) {
			//var_dump($s,$this->config->get($s));
			$this->data[$s] = $this->config->get($s);
		}
		
		$this->document->addScript('view/javascript/multimerch/settings.js');
		
		$this->load->model("localisation/order_status");
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		$this->load->model("catalog/option");
		$this->data['options'] = $this->model_catalog_option->getOptions();

		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		
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
		
		$this->load->model('catalog/information');
		$this->data['informations'] = $this->model_catalog_information->getInformations();
		$this->data['categories'] = $this->MsLoader->MsProduct->getCategories();
		$this->data['product_included_fieds'] = array(
			'model' => $this->language->get('ms_catalog_products_field_model'),
			'sku' => $this->language->get('ms_catalog_products_field_sku'),
			'upc' => $this->language->get('ms_catalog_products_field_upc'),
			'ean' => $this->language->get('ms_catalog_products_field_ean'),
			'jan' => $this->language->get('ms_catalog_products_field_jan'),
			'isbn' => $this->language->get('ms_catalog_products_field_isbn'),
			'mpn' => $this->language->get('ms_catalog_products_field_mpn'),
			'manufacturer' => $this->language->get('ms_catalog_products_field_manufacturer'),
			'dateAvailable' => $this->language->get('ms_catalog_products_field_date_available'),
			'taxClass' => $this->language->get('ms_catalog_products_field_tax_class'),
			'subtract' => $this->language->get('ms_catalog_products_field_subtract'),
			'stockStatus' => $this->language->get('ms_catalog_products_field_stock_status'),
			'metaDescription' => $this->language->get('ms_catalog_products_field_meta_description'),
			'metaKeywords' => $this->language->get('ms_catalog_products_field_meta_keyword')
		);
		
		$this->document->setTitle($this->language->get('ms_settings_heading'));
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => ''//$this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_settings_breadcrumbs'),
				'href' => $this->url->link('multiseller/settings', '', 'SSL'),
			)
		));
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->admLoadTemplate('settings');
		$this->response->setOutput($this->render());
	}
	
	public function upgradeDb() {
		$this->load->model("multiseller/upgrade");
		if ($this->MsLoader->MsHelper->isInstalled() && !$this->model_multiseller_upgrade->isDbLatest()) {
			$this->model_multiseller_upgrade->upgradeDb();
			$this->session->data['ms_db_latest'] = $this->language->get('ms_db_success');
		} else {
			$this->session->data['ms_db_latest'] = $this->language->get('ms_db_latest');
		}
		
		$this->redirect($this->url->link('module/multiseller', 'token=' . $this->session->data['token'], 'SSL'));
	}
}
?>
