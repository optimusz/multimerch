<?php 
class ControllerAccountSellerSuccess extends Controller {
	public function index() {
		$this->language->load('account/success');
		$this->language->load('multiseller/multiseller');
		
		$this->document->setTitle($this->language->get('ms_account_register_seller_success_heading'));
		
		$this->data['breadcrumbs'] = array();
		
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home'),
			'separator' => false
		); 

		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', 'SSL'),
			'separator' => $this->language->get('text_separator')
		);
		
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_success'),
			'href' => $this->url->link('account/success'),
			'separator' => $this->language->get('text_separator')
		);
		
		$this->data['heading_title'] = $this->language->get('ms_account_register_seller_success_heading');
		
		if ($this->config->get('msconf_seller_validation') == MsSeller::MS_SELLER_VALIDATION_NONE) {
			$this->data['text_message'] = sprintf($this->language->get('ms_account_register_seller_success_message'), $this->config->get('config_name'), $this->url->link('information/contact'));
		} else {
			$this->data['text_message'] = sprintf($this->language->get('ms_account_register_seller_success_approval'), $this->config->get('config_name'), $this->url->link('information/contact'));
		}
		
		$this->data['button_continue'] = $this->language->get('button_continue');
		
		/*if ($this->cart->hasProducts()) {
			$this->data['continue'] = $this->url->link('checkout/cart', '', 'SSL');
		} else {
			$this->data['continue'] = $this->url->link('seller/account-dashboard', '', 'SSL');
		}*/
		if ($this->MsLoader->MsSeller->getStatus() == MsSeller::STATUS_INACTIVE && $this->config->get('msconf_allow_inactive_seller_products')) {
			$this->data['continue'] = $this->url->link('account/account', '', 'SSL');
		} else {
			$this->data['continue'] = $this->url->link('seller/account-dashboard', '', 'SSL');
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/success.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/common/success.tpl';
		} else {
			$this->template = 'default/template/common/success.tpl';
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
}
?>