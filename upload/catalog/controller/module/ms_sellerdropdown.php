<?php  
class ControllerModuleMsSellerdropdown extends ControllerSellerCatalog {
	protected function index($setting) {
		static $module = 0;
		
		if (isset($setting['limit']))
			$this->data['limit'] = (int)$setting['limit'];
		else
			$this->data['limit'] = 3;		

		if (!isset($setting['width']) || (int)$setting['width'] <= 0)
			$setting['width'] = $this->config->get('config_image_category_width');
		
		if (!isset($setting['height']) || (int)$setting['height'] <= 0)
			$setting['height'] = $this->config->get('config_image_category_height');
			
		$this->data['sellers_href'] = $this->url->link('seller/catalog-seller');

		$results = $this->MsLoader->MsSeller->getSellers(
			array(
				'seller_status' => array(MsSeller::STATUS_ACTIVE)
			),
			array(
				'order_by'               => 'ms.date_created',
				'order_way'              => 'DESC',
				'offset'              => 0,
				'limit'              => $this->data['limit']
			)
		);

		$this->data['sellers'] = array();
		foreach ($results as $result) {
			$this->data['sellers'][] = array(
				'nickname'        => $result['ms.nickname'],
				'href'        => $this->url->link('seller/catalog-seller/profile','seller_id=' . $result['seller_id']),
			);
		}
		$this->data['module'] = $module++; 
		list($this->template, $this->children) = $this->MsLoader->MsHelper->loadTemplate('module-sellerdropdown', array());
		$this->response->setOutput($this->render());
	}
}
?>