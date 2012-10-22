<?php  
class ControllerModuleMsCarousel extends Controller {
	protected function index($setting) {
		require_once(DIR_SYSTEM . 'library/ms-seller.php');
		require_once(DIR_SYSTEM . 'library/ms-file.php');
		$this->msSeller = new MsSeller($this->registry);			
		$this->msFile = new MsFile($this->registry);		
		
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
			$this->data['limit'] = (int)$setting['limit'];
		else
			$this->data['limit'] = 3;		

		if (isset($setting['scroll']))
			$this->data['scroll'] = (int)$setting['scroll'];
		else
			$this->data['scroll'] = 1;		
		
		if (!isset($setting['width']) || (int)$setting['width'] <= 0)
			$setting['width'] = $this->config->get('config_image_category_width');
		
		if (!isset($setting['height']) || (int)$setting['height'] <= 0)
			$setting['height'] = $this->config->get('config_image_category_height');		
		
		$this->data['sellers_href'] = $this->url->link('product/seller');
				
		$data = array(
			'order_by'               => 'ms.nickname',
			'order_way'              => 'ASC',
		);
		
		$results = $this->msSeller->getSellers($data, TRUE);

		$this->data['sellers'] = array();
		foreach ($results as $result) {
			$this->data['sellers'][] = array(
				'nickname'        => $result['nickname'],
				'href'        => $this->url->link('product/seller/profile','seller_id=' . $result['seller_id']),
				'image' => !empty($result['avatar_path']) && file_exists(DIR_IMAGE . $result['avatar_path']) ? $this->msFile->resizeImage($result['avatar_path'], $setting['width'], $setting['height']) : $this->msFile->resizeImage('ms_no_image.jpg', $setting['width'], $setting['height'])
			);
		}
		
		shuffle($this->data['sellers']);
		$this->data['module'] = $module++; 
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/muliseller/ms-carousel.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/muliseller/ms-carousel.tpl';
		} else {
			$this->template = 'default/template/module/multiseller/ms-carousel.tpl';
		}
		
		$this->render(); 
	}
}
?>