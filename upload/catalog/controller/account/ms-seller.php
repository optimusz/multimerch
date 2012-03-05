<?php

class ControllerAccountMsSeller extends Controller {
	private $name = 'ms-seller';
	private $seller;
	
	public function __construct($registry) {
		parent::__construct($registry);
		
		/*
    	if (!$this->customer->isLogged()) {
	  		$this->session->data['redirect'] = $this->url->link('account/ms-seller', '', 'SSL');
	  		$this->redirect($this->url->link('account/login', '', 'SSL')); 
    	}
		*/
		
		$this->document->addStyle('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/multiseller.css');
		$this->data = array_merge($this->data, $this->load->language('module/multiseller'),$this->load->language('account/account'));
		$this->seller =& $this->customer;
	}
	
	/*private function _loadAdminModel($model) {
		$file  = DIR_ADMINPPLICATION . 'model/' . $model . '.php';
		$class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);
		
		if (file_exists($file)) {
			include_once($file);
			
			$this->registry->set('model_' . str_replace('/', '_', $model), new $class($this->registry));
		} else {
			trigger_error('Error: Could not load model ' . $model . '!');
			exit();					
		}
	}*/
	
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
	
	public function jxSaveProduct() {
		var_dump($this->request->post);
	}
	
	public function newProduct() {
		$this->load->model('module/multiseller/seller');		
		$this->document->setTitle($this->language->get('ms_account_newproduct_heading'));
		
		$this->load->model('catalog/category');
		$this->data['categories'] = $this->model_module_multiseller_seller->getCategories(0);

		$this->_setBreadcrumbs('ms_account_newproduct_breadcrumbs', __FUNCTION__);
		$this->_renderTemplate('ms-account-newproduct');
	}
	
	public function products() {
		$this->load->model('module/multiseller/seller');
		$products = $this->model_module_multiseller_seller->getSellerProducts($this->seller->getId());

		
		
		$this->_setBreadcrumbs('text_account_products', __FUNCTION__);		
		$this->_renderTemplate('ms-account-products');
	}
	
	public function editProduct() {
		$this->_setBreadcrumbs('text_account_editproduct', __FUNCTION__);		
		$this->_renderTemplate('ms-account-editproduct');
	}
	

	public function editInfo() {
		$this->load->model('module/multiseller/seller');		
		$this->document->setTitle($this->language->get('ms_account_sellerinfo_heading'));
		

		$this->_setBreadcrumbs('text_account_editinfo', __FUNCTION__);		
		$this->_renderTemplate('ms-editinfo');
	}
	
	public function transactions() {
		$this->_setBreadcrumbs('text_account_transactions', __FUNCTION__);		
		$this->_renderTemplate('ms-transactions');
	}
	
	public function requestMoney() {
		$this->_setBreadcrumbs('text_account_requestmoney', __FUNCTION__);		
		$this->_renderTemplate('ms-requestmoney');
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
}
?>
