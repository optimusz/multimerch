<?php  
class ControllerModuleMsTopsellers extends ControllerSellerCatalog {
	protected function index($setting) {
		static $module = 0;
		
		if (isset($setting['limit']) && (int)$setting['limit'] > 0)
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
				'order_by'               => 'total_sales',
				'order_way'              => 'DESC',
				'offset'              => 0,
				'limit'              => $this->data['limit']
			)
		);
		
		//$results = $this->MsLoader->MsSeller->getTopSellers($data, TRUE);

		$this->data['sellers'] = array();
		foreach ($results as $result) {
			$this->data['sellers'][] = array(
				'nickname'        => $result['ms.nickname'],
				'href'        => $this->url->link('seller/catalog-seller/profile','seller_id=' . $result['seller_id']),
				'image' => !empty($result['ms.avatar']) && file_exists(DIR_IMAGE . $result['ms.avatar']) ? $this->MsLoader->MsFile->resizeImage($result['ms.avatar'], $setting['width'], $setting['height']) : $this->MsLoader->MsFile->resizeImage('ms_no_image.jpg', $setting['width'], $setting['height']) 
			);
		}
		$this->data['module'] = $module++; 
		list($this->template, $this->children) = $this->MsLoader->MsHelper->loadTemplate('module-topsellers', array());
		$this->response->setOutput($this->render());
	}
}
?>