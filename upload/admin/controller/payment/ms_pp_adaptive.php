<?php
class ControllerPaymentMSPPAdaptive extends Controller {
	private $name = 'ms_pp_adaptive';
	
	private $settings;
	private $encryption;
	private $errors = array();

	public function __construct($registry) {
		parent::__construct($registry);
		require_once(DIR_SYSTEM . 'library/ms-paypal.php');
		$this->data = array_merge($this->data, $this->load->language('payment/ms_pp_adaptive'));
		$this->data['token'] = $this->session->data['token'];
		
		$this->settings = Array(
			"msppaconf_payment_type" => 'PARALLEL', // PARALLEL, CHAINED
			"msppaconf_feespayer" => 'SENDER', // PRIMARYRECEIVER, EACHRECEIVER, SECONDARYONLY
			"msppaconf_debug" => 0,
			//"msppaconf_receivers" => "",
			"msppaconf_receiver" => "",
			"msppaconf_api_username" => "",
			"msppaconf_api_password" => "",
			"msppaconf_api_signature" => "",
			"msppaconf_api_appid" => "",
			"msppaconf_secret_key" => "",
			"msppaconf_secret_value" => "",
			"msppaconf_sandbox" => 1,

			"ms_pp_adaptive_total" => 0,			
			"ms_pp_adaptive_sort_order" => 0,
			"ms_pp_adaptive_status" => 0,			
			"ms_pp_adaptive_geo_zone_id" => 0,
			
			"msppaconf_completed_status_id" => 0,
			"msppaconf_incomplete_status_id" => 0,
			"msppaconf_error_status_id" => 0,
			"msppaconf_reversalerror_status_id" => 0,
			"msppaconf_processing_status_id" => 0,
			"msppaconf_pending_status_id" => 0,

			"msppaconf_invalid_email" => 0, // 0 - abort module, 1 - balance transaction
			"msppaconf_too_many_receivers" => 0 // 0 - abort module, 1 - balance transaction			
		);
		
		$this->encryption = new Encryption($this->config->get('config_encryption'));	
	}	
	
	private function _editSettings() {
		$this->load->model('setting/setting');
		
		$set = $this->model_setting_setting->getSetting($this->name);

		foreach ($this->settings as $name=>$value) {
			if (!array_key_exists($name,$set))
				$set[$name] = $value;
		}

		foreach($set as $s=>$v) {
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
	}	
	
	private function _setBreadcrumbs() {
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link("module/{$this->name}", 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);		
	}	
	
	public function index() {
		//$this->load->model("module/{$this->name}");
		$this->load->model('setting/setting');
		
		foreach($this->settings as $s=>$v) {
			$this->data[$s] = $this->config->get($s);
		}

		if ($this->config->get('msppaconf_api_password') != '')
			$this->data['msppaconf_api_password'] = $this->encryption->decrypt($this->config->get('msppaconf_api_password'));

		if ($this->config->get('msppaconf_api_signature') != '')
			$this->data['msppaconf_api_signature'] = $this->encryption->decrypt($this->config->get('msppaconf_api_signature'));

		$this->load->model('localisation/order_status');
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$this->load->model('localisation/geo_zone');
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$this->_setBreadcrumbs();
			
		//$this->data['receivers'] = $this->config->get('msppaconf_receivers');
		$this->data['currency_code'] = $this->config->get('config_currency');			
				
        $this->data['action'] = $this->url->link("module/{$this->name}", 'token=' . $this->session->data['token'], 'SSL');
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['token'] = $this->session->data['token'];
		
		$this->template = "payment/{$this->name}.tpl";
		$this->children = array(
			'common/header',	
			'common/footer'	
		);
		
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
	}
	
	public function saveSettings() {
		$json = array();	
		if ($this->_validate(__FUNCTION__)) {
			$this->request->post['msppaconf_api_password'] = $this->encryption->encrypt($this->request->post['msppaconf_api_password']);
			$this->request->post['msppaconf_api_signature'] = $this->encryption->encrypt($this->request->post['msppaconf_api_signature']);
			//$this->request->post['msppaconf_secret_key'] = $this->encryption->encrypt($this->request->post['msppaconf_secret_key']);
			//$this->request->post['msppaconf_secret_value'] = $this->encryption->encrypt($this->request->post['msppaconf_secret_value']);
			
			$this->_editSettings();
			$json['success'] = $this->language->get('ppa_success');
		} else {
			$json['errors'] = $this->errors;
		}
		
		if (strcmp(VERSION,'1.5.1.3') >= 0) {
			$this->response->setOutput(json_encode($json));
		} else {
			$this->load->library('json');
			$this->response->setOutput(Json::encode($json));			
		}		
	}	
	
	private function _validate() {
		if (!$this->user->hasPermission('modify', 'payment/ms_pp_adaptive')) {
			$this->errors[] = $this->language->get('error_permission');
		}

		if (($this->request->post['msppaconf_feespayer'] == 'SECONDARYONLY' || $this->request->post['msppaconf_feespayer'] == 'PRIMARYRECEIVER') && $this->request->post['msppaconf_payment_type'] != 'CHAINED') {
			$this->errors[] = $this->language->get('ppa_error_secondaryonly');
		}

		if (!isset($this->request->post['msppaconf_receiver']) || empty($this->request->post['msppaconf_receiver'])) {
			$this->errors[] = $this->language->get('ppa_error_receiver');
		}
		
		if (!$this->request->post['msppaconf_api_username'] || !$this->request->post['msppaconf_api_password'] || !$this->request->post['msppaconf_api_signature'] || !$this->request->post['msppaconf_api_appid']) {
			$this->errors[] = $this->language->get('ppa_error_credentials');
		}
		
		if (!$this->request->post['msppaconf_secret_key'] || !$this->request->post['msppaconf_secret_value']) {
			$this->errors[] = $this->language->get('ppa_error_secret');
		}		
				
		if (!$this->errors) {
			return true;
		} else {
			return false;
		}
	}
}
?>
