<?php

class ControllerModuleMultiseller extends ControllerMultisellerBase {
	private $settings = array(
		"msconf_seller_validation" => MsSeller::MS_SELLER_VALIDATION_NONE,
		"msconf_product_validation" => MsProduct::MS_PRODUCT_VALIDATION_NONE,
		"msconf_seller_commission" => 5,
		"msconf_image_preview_width" => 100,
		"msconf_image_preview_height" => 100,
		"msconf_credit_order_statuses" => array(5),
		"msconf_debit_order_statuses" => array(8),
		"msconf_minimum_withdrawal_amount" => "50",
		"msconf_allow_partial_withdrawal" => 1,
		"msconf_paypal_sandbox" => 1,			
		"msconf_paypal_api_username" => "",
		"msconf_paypal_api_password" => "",
		"msconf_paypal_api_signature" => "",
		"msconf_allow_withdrawal_requests" => 1,
		"msconf_comments_maxlen" => 500,
		"msconf_allowed_image_types" => 'png,jpg,jpeg',
		"msconf_allowed_download_types" => 'zip,rar,pdf',
		"msconf_minimum_product_price" => 0,
		"msconf_notification_email" => "",
		"ms_carousel_module" => "",
		"ms_topsellers_module" => "",
		"ms_newsellers_module" => "",	
		"ms_sellerdropdown_module" => "",	
		"msconf_allow_free_products" => 0,
		"msconf_seller_commission_flat" => 0.5,
		"msconf_allow_multiple_categories" => 0,
		"msconf_images_limits" => array(0,0),
		"msconf_downloads_limits" => array(0,0),		
		"msconf_enable_shipping" => 0, // 0 - no, 1 - yes, 2 - seller select
		"msconf_provide_buyerinfo" => 0, // 0 - no, 1 - yes, 2 - shipping dependent
		"msconf_enable_quantities" => 0, // 0 - no, 1 - yes, 2 - shipping dependent
		"msconf_product_options" => array(),
		"msconf_enable_pdf_generator" => 0,
		"msconf_enable_seo_urls" => 0,
		"msconf_enable_update_seo_urls" => 0,
		"msconf_enable_non_alphanumeric_seo" => 0,
		"msconf_product_image_directory" => 'sellers'
	);
	
	public function __construct($registry) {
		parent::__construct($registry);		
		$this->registry = $registry;
	}
	
	private function _editSettings() {
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
	public function install() {
		$this->validate(__FUNCTION__);		
		$this->load->model("module/multiseller/settings");
		$this->load->model('setting/setting');
		$this->model_module_multiseller_settings->createTable();
		$this->model_setting_setting->editSetting($this->name, $this->settings);
	}

	public function uninstall() {
		$this->validate(__FUNCTION__);
		$this->load->model("module/multiseller/settings");
		$this->model_module_multiseller_settings->dropTable();
	}	

	public function saveSettings() {
		$this->validate(__FUNCTION__);
		
		/*magic
		if ($this->request->post['msconf_image_preview_width'] > 200)
			$this->request->post['msconf_image_preview_width'] = 200;

		if ($this->request->post['msconf_image_preview_height'] > 200)
			$this->request->post['msconf_image_preview_height'] = 200;
			
		$this->request->post['msconf_allowed_image_types'] = 'png,jpg';
		$this->request->post['msconf_allowed_download_types'] = 'zip,rar,pdf';
		
		$this->request->post['msconf_paypal_sandbox'] = 1;
		magic*/
		
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
		$this->load->model("localisation/order_status");
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		$this->load->model("catalog/option");
		$this->data['options'] = $this->model_catalog_option->getOptions();

        $this->data['action'] = $this->url->link("module/{$this->name}/settings", 'token=' . $this->session->data['token'], 'SSL');
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
}
?>
