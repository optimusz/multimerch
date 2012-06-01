<?php  
class ControllerModuleMsCarousel extends Controller {
	protected function index($setting) {
		require_once(DIR_SYSTEM . 'library/ms-seller.php');
		require_once(DIR_SYSTEM . 'library/ms-image.php');
		$this->msImage = new MsImage($this->registry);			
		$this->msSeller = new MsSeller($this->registry);		
		
		static $module = 0;
		$this->load->model('tool/image');
		$this->document->addScript('catalog/view/javascript/jquery/jquery.jcarousel.min.js');
		
		if (file_exists('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/carousel.css')) {
			$this->document->addStyle('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/carousel.css');
		} else {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/carousel.css');
		}
		
		$this->document->addStyle('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/multiseller.css');
		$this->data = array_merge($this->data, $this->load->language('module/multiseller'));
		
		if (isset($setting['limit']))
			$this->data['limit'] = $setting['limit'];
		else
			$this->data['limit'] = 3;		

		if (isset($setting['scroll']))		
			$this->data['scroll'] = $setting['scroll'];
		else
			$this->data['scroll'] = 1;		
		
		$this->data['sellers_href'] = $this->url->link('product/seller');
				
				
		$data = array(
			//'filter_category_id' => $category_id, 
			'order_by'               => 'ms.nickname',
			'order_way'              => 'ASC',
			//'page'              => $page,
			//'limit'              => $limit
		);
		
		$results = $this->msSeller->getSellers($data, TRUE);

		$this->data['sellers'] = array();
		foreach ($results as $result) {
			if (!empty($result['avatar_path']) && file_exists(DIR_IMAGE . $result['avatar_path'])) {
				$this->data['sellers'][] = array(
					'nickname'        => $result['nickname'],
					'href'        => $this->url->link('product/seller/profile','seller_id=' . $result['seller_id']),
					'image' => $this->msImage->resize($result['avatar_path'], $setting['width'], $setting['height'])
				);
			}
		}
		shuffle($this->data['sellers']);		
		$this->data['module'] = $module++; 
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/multiseller/module/ms-carousel.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/multiseller/module/ms-carousel.tpl';
		} else {
			$this->template = 'default/template/module/multiseller/ms-carousel.tpl';
		}
		
		$this->render(); 
	}
}
?>