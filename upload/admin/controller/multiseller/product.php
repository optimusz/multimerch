<?php

class ControllerMultisellerProduct extends ControllerMultisellerBase {
	public function __construct($registry) {
		parent::__construct($registry);		
		$this->registry = $registry;
	}

	public function index() {
		$this->validate(__FUNCTION__);
		
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;

		$sort = array(
			'order_by'  => 'pr.date_modified',
			'order_way' => 'DESC',
			'page' => $page,
			'limit' => 5
		);

		$results = $this->MsLoader->MsProduct->getProducts($sort, true);
		$total_products = $this->MsLoader->MsProduct->getTotalProducts(true);

		foreach ($results as $result) {
			if ($result['prd.image'] && file_exists(DIR_IMAGE . $result['prd.image'])) {
				$image = $this->MsLoader->MsFile->resizeImage($result['prd.image'], 40, 40);
			} else {
				$image = $this->MsLoader->MsFile->resizeImage('no_image.jpg', 40, 40);
			}		
			
			$action = array();
			$action[] = array(
				'text' => $this->language->get('ms_edit'),
				'href' => $this->url->link('catalog/product/update', 'token=' . $this->session->data['token'] . '&product_id=' . $result['prd.product_id'], 'SSL')
			);
			
			$this->data['products'][] = array(
				'image' => $image,
				'name' => $result['prd.name'],
				'seller' => $result['sel.nickname'],
				'date_created' => date($this->language->get('date_format_short'), strtotime($result['prd.date_created'])),
				'date_modified' => date($this->language->get('date_format_short'), strtotime($result['prd.date_modified'])),
				'status' => $result['prd.status'],
				'action' => $action,
				'product_id' => $result['prd.product_id']
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $total_products;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link("multiseller/product", 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');
		
		$this->data['pagination'] = $pagination->render();
		
		if (isset($this->session->data['error'])) {
			$this->data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}		

		$this->data['token'] = $this->session->data['token'];		
		$this->data['heading'] = $this->language->get('ms_catalog_products_heading');
		$this->document->setTitle($this->language->get('ms_catalog_products_heading'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multiseller/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_catalog_products_breadcrumbs'),
				'href' => $this->url->link('multiseller/product', '', 'SSL'),
			)
		));
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->admLoadTemplate('product');
		$this->response->setOutput($this->render());
	}	
	
	public function jxProductStatus() {
		$this->validate(__FUNCTION__);
		$mails = array();
		if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $product_id) {
				$seller_id = $this->MsLoader->MsProduct->getSellerId($product_id);
				// todo
				$result = $this->MsLoader->MsRequest->getProductRequests(
					array(
						'product_id' => $product_id
					),
					array(
						'page' => 0,
						'limit' => 1
					)
				);			
				
				if ($this->request->post['ms-action'] == 'ms-enable') {
					$this->MsLoader->MsProduct->enableProduct($product_id);
					$mails[] = array(
						'type' => $this->MsLoader->MsProduct->getStatus($product_id) == MsProduct::MS_PRODUCT_STATUS_PENDING ? MsMail::SMT_PRODUCT_APPROVED : MsMail::SMT_PRODUCT_ENABLED,
						'data' => array(
							'product_id' => $product_id,
							'recipients' => $this->MsLoader->MsSeller->getSellerEmail($seller_id),
							'addressee' => $this->MsLoader->MsSeller->getSellerName($seller_id),
							'message' => $this->request->post['product_message']
						)
					);
				} else {
					$this->MsLoader->MsProduct->disableProduct($product_id);
					$mails[] = array(
						'type' => $this->MsLoader->MsProduct->getStatus($product_id) == MsProduct::MS_PRODUCT_STATUS_PENDING ? MsMail::SMT_PRODUCT_DECLINED : MsMail::SMT_PRODUCT_DISABLED,
						'data' => array(
							'product_id' => $product_id,
							'recipients' => $this->MsLoader->MsSeller->getSellerEmail($seller_id),
							'addressee' => $this->MsLoader->MsSeller->getSellerName($seller_id),
							'message' => $this->request->post['product_message']
						)
					);					
				}
				$msRequest->processProductRequests($product_id,$this->user->getId(),$this->request->post['product_message']);
			}

			$this->MsLoader->MsMail->sendMails($mails);
			$this->session->data['success'] = 'Successfully changed product status.';
		} else {
			$this->session->data['error'] = 'Error changing product status.';
		}
	}	
}
?>
