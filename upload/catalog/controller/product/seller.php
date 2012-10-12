<?php 
class ControllerProductSeller extends Controller {
	public function __construct($registry) {
		parent::__construct($registry);
		require_once(DIR_SYSTEM . 'library/ms-image.php');
		require_once(DIR_SYSTEM . 'library/ms-request.php');
		require_once(DIR_SYSTEM . 'library/ms-transaction.php');
		require_once(DIR_SYSTEM . 'library/ms-product.php');
		require_once(DIR_SYSTEM . 'library/ms-seller.php');
		require_once(DIR_SYSTEM . 'library/ms-mail.php');
		$this->msSeller = new MsSeller($this->registry);
		$this->msProduct = new MsProduct($this->registry);
		$this->msMail = new MsMail($this->registry);
		$this->msImage = new MsImage($this->registry);
		
		$this->document->addStyle('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/multiseller.css');
		$this->data = array_merge($this->data, $this->load->language('module/multiseller'),$this->load->language('account/account'),$this->language->load('product/product'));
		
		$this->load->config('ms-config');
	}
		
	private function _setBreadcrumbs($textVar, $function, $parms = '') {
      	$this->data['breadcrumbs'] = array();

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),     	
        	'separator' => false
      	); 

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('ms_catalog_sellers'),
			'href'      => $this->url->link('product/seller', '', 'SSL'),        	
        	'separator' => $this->language->get('text_separator')
      	);

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get($textVar),
			'href'      => $this->url->link("product/seller/" . strtolower($function), $parms, 'SSL'),       	
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

	public function index() {
		$this->load->model('localisation/country');
		$this->language->load('product/category');
		
		$this->data['text_display'] = $this->language->get('text_display');
		$this->data['text_list'] = $this->language->get('text_list');
		$this->data['text_grid'] = $this->language->get('text_grid');
		$this->data['text_sort'] = $this->language->get('text_sort');
		$this->data['text_limit'] = $this->language->get('text_limit');
				
				
		if (isset($this->request->get['sort'])) {
			$order_by = $this->request->get['sort'];
		} else {
			$order_by = 'ms.nickname';
		}

		if (isset($this->request->get['order'])) {
			$order_way = $this->request->get['order'];
		} else {
			$order_way = 'ASC';
		}
		
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;	
							
		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = $this->config->get('config_catalog_limit');
		}
		
		$this->data['products'] = array();
		
		$data = array(
			//'filter_category_id' => $category_id, 
			'order_by'               => $order_by,
			'order_way'              => $order_way,
			'page'              => $page,
			'limit'              => $limit
		);
		
		$total_sellers = $this->msSeller->getTotalSellers(TRUE);
		$results = $this->msSeller->getSellers($data, TRUE);
		
		foreach ($results as $result) {
			if ($result['avatar_path'] && file_exists(DIR_IMAGE . $result['avatar_path'])) {
				$image = $this->msImage->resize($result['avatar_path'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
			} else {
				$image = $this->msImage->resize('no_image.jpg', $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
			}

			$country = $this->model_localisation_country->getCountry($result['country_id']);
			$this->data['sellers'][] = array(
				'seller_id'  => $result['seller_id'],
				'thumb'       => $image,
				'nickname'        => $result['nickname'],
				'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 200) . '..',
				//'rating'      => $result['rating'],
				'country' => ($country ? $country['name'] : NULL),
				'country_flag' => ($country ? 'image/flags/' . strtolower($country['iso_code_2']) . '.png' : NULL),
				'total_sales' => $this->msSeller->getSalesForSeller($result['seller_id']),
				'total_products' => $this->msSeller->getTotalSellerProducts($result['seller_id'], TRUE),
				'href'        => $this->url->link('product/seller/profile', '&seller_id=' . $result['seller_id'])
			);
		}
		
		$url = '';

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}
						
		$this->data['sorts'] = array();
		
		$this->data['sorts'][] = array(
			'text'  => $this->language->get('ms_sort_nickname_asc'),
			'value' => 'ms.nickname-ASC',
			'href'  => $this->url->link('product/seller', '&sort=ms.nickname&order=ASC' . $url)
		);

		$this->data['sorts'][] = array(
			'text'  => $this->language->get('ms_sort_nickname_desc'),
			'value' => 'ms.nickname-DESC',
			'href'  => $this->url->link('product/seller', '&sort=ms.nickname&order=DESC' . $url)
		);

		$this->data['sorts'][] = array(
			'text'  => $this->language->get('ms_sort_country_asc'),
			'value' => 'ms.country_id-ASC',
			'href'  => $this->url->link('product/seller', '&sort=ms.country_id&order=ASC' . $url)
		); 

		$this->data['sorts'][] = array(
			'text'  => $this->language->get('ms_sort_country_desc'),
			'value' => 'ms.country_id-DESC',
			'href'  => $this->url->link('product/seller', '&sort=ms.country_id&order=DESC' . $url)
		); 
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}	

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		
		$this->data['limits'] = array();
		
		$this->data['limits'][] = array(
			'text'  => $this->config->get('config_catalog_limit'),
			'value' => $this->config->get('config_catalog_limit'),
			'href'  => $this->url->link('product/seller', $url . '&limit=' . $this->config->get('config_catalog_limit'))
		);
					
		$this->data['limits'][] = array(
			'text'  => 25,
			'value' => 25,
			'href'  => $this->url->link('product/seller', $url . '&limit=25')
		);
		
		$this->data['limits'][] = array(
			'text'  => 50,
			'value' => 50,
			'href'  => $this->url->link('product/seller', $url . '&limit=50')
		);

		$this->data['limits'][] = array(
			'text'  => 75,
			'value' => 75,
			'href'  => $this->url->link('product/seller', $url . '&limit=75')
		);
		
		$this->data['limits'][] = array(
			'text'  => 100,
			'value' => 100,
			'href'  => $this->url->link('product/seller', $url . '&limit=100')
		);
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}	

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}
		
		$pagination = new Pagination();
		$pagination->total = $total_sellers;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('product/seller', $url . '&page={page}');
	
		$this->data['pagination'] = $pagination->render();
		
		$this->data['sort'] = $order_by;
		$this->data['order'] = $order_way;
		$this->data['limit'] = $limit;		
		
		$this->data['continue'] = $this->url->link('common/home');

		$this->document->setTitle($this->language->get('ms_catalog_sellers_heading'));
      	$this->data['breadcrumbs'] = array();

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),     	
        	'separator' => false
      	); 

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('ms_catalog_sellers'),
			'href'      => $this->url->link('product/seller', '', 'SSL'),        	
        	'separator' => $this->language->get('text_separator')
      	);
      	
		$this->_renderTemplate('ms-catalog-sellerlist');
	}
		
	public function profile() {
		$this->load->model('localisation/country');
		$this->load->model('catalog/product');
    	
		$seller = $this->msSeller->getSellerData($this->request->get['seller_id']);
		$this->document->addScript('catalog/view/javascript/ms-contactseller.js');

		if (empty($seller) || $seller['seller_status_id'] != MsSeller::MS_SELLER_STATUS_ACTIVE) {
			$this->redirect($this->url->link('product/seller', '', 'SSL'));
			return;
		}
			
		if ($seller['avatar_path'] && file_exists(DIR_IMAGE . $seller['avatar_path'])) {
			$image = $this->msImage->resize($seller['avatar_path'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
		} else {
			$image = $this->msImage->resize('no_image.jpg', $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
		}
		
		$this->data['seller']['nickname'] = $seller['nickname'];
		$this->data['seller']['description'] = $seller['description'];
		$this->data['seller']['thumb'] = $image;
		$this->data['seller']['href'] = $this->url->link('product/seller/products', 'seller_id=' . $seller['seller_id']);
		$this->data['seller_id'] = $this->request->get['seller_id'];
		
		$country = $this->model_localisation_country->getCountry($seller['country_id']);
		
		if (!empty($country)) {			
			$this->data['seller']['country'] = $country['name'];
		} else {
			$this->data['seller']['country'] = NULL;
		}
		
		if (!empty($seller['company'])) {
			$this->data['seller']['company'] = $seller['company'];
		} else {
			$this->data['seller']['company'] = NULL;
		}
		
		if (!empty($seller['website'])) {
			$this->data['seller']['website'] = $seller['website'];
		} else {
			$this->data['seller']['website'] = NULL;
		}
		
		$this->data['seller']['total_sales'] = $this->msSeller->getSalesForSeller($seller['seller_id']);
		$this->data['seller']['total_products'] = $this->msSeller->getTotalSellerProducts($seller['seller_id'], TRUE);
				
		$sort = array(
			'order_by'  => 'pd.name',
			'order_way' => 'ASC',
			'page'              => 1,
			'limit'              => 5
		);
		
		$products = $this->msSeller->getSellerProducts($seller['seller_id'], $sort, TRUE);

		if (!empty($products)) {
			foreach ($products as $product) {
				$product_data = $this->model_catalog_product->getProduct($product['product_id']);
				if ($product_data['image'] && file_exists(DIR_IMAGE . $product_data['image'])) {
					$image = $this->msImage->resize($product_data['image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
				} else {
					$image = $this->msImage->resize('no_image.jpg', $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
				}

				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($product_data['price'], $product_data['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$price = false;
				}
						
				if ((float)$product_data['special']) {
					$special = $this->currency->format($this->tax->calculate($product_data['special'], $product_data['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$special = false;
				}
				
				if ($this->config->get('config_review_status')) {
					$rating = $product_data['rating'];
				} else {
					$rating = false;
				}
							
				$this->data['seller']['products'][] = array(
					'product_id' => $product['product_id'],				
					'thumb' => $image,
					'name' => $product_data['name'],
					'price' => $price,
					'special' => $special,
					'rating' => $rating,
					'reviews'    => sprintf($this->language->get('text_reviews'), (int)$product_data['reviews']),
					'href'    	 => $this->url->link('product/product', 'product_id=' . $product_data['product_id']),						
				);				
			}
		} else {
			$this->data['seller']['products'] = NULL;
		}


		$this->data['ms_catalog_seller_profile_view'] = sprintf($this->language->get('ms_catalog_seller_profile_view'), $this->data['seller']['nickname']);
		$this->document->setTitle(sprintf($this->language->get('ms_catalog_seller_profile_heading'), $this->data['seller']['nickname']));
		$this->_setBreadcrumbs(sprintf($this->language->get('ms_catalog_seller_profile_breadcrumbs'), $this->data['seller']['nickname']), __FUNCTION__, '&seller_id='.$seller['seller_id']);
		$this->_renderTemplate('ms-catalog-sellerprofile');
  	}
  	
	public function products() { 
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('localisation/country');
    	$this->language->load('product/category');
    	
		$seller = $this->msSeller->getSellerData($this->request->get['seller_id']);

		if (empty($seller) || $seller['seller_status_id'] != MsSeller::MS_SELLER_STATUS_ACTIVE) {
			$this->redirect($this->url->link('product/seller', '', 'SSL'));
			return;
		}
		
		/* seller info part */	
		if ($seller['avatar_path'] && file_exists(DIR_IMAGE . $seller['avatar_path'])) {
			$image = $this->msImage->resize($seller['avatar_path'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
		} else {
			$image = $this->msImage->resize('no_image.jpg', $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
		}
		
		$this->data['seller']['nickname'] = $seller['nickname'];
		$this->data['seller']['description'] = $seller['description'];
		$this->data['seller']['thumb'] = $image;
		$this->data['seller']['href'] = $this->url->link('product/seller/profile', 'seller_id=' . $seller['seller_id']);
		
		$country = $this->model_localisation_country->getCountry($seller['country_id']);
		
		if (!empty($country)) {			
			$this->data['seller']['country'] = $country['name'];
		} else {
			$this->data['seller']['country'] = NULL;
		}
		
		if (!empty($seller['company'])) {
			$this->data['seller']['company'] = $seller['company'];
		} else {
			$this->data['seller']['company'] = NULL;
		}
		
		if (!empty($seller['website'])) {
			$this->data['seller']['website'] = $seller['website'];
		} else {
			$this->data['seller']['website'] = NULL;
		}
		
		$this->data['seller']['total_sales'] = $this->msSeller->getSalesForSeller($seller['seller_id']);
		$this->data['seller']['total_products'] = $this->msSeller->getTotalSellerProducts($seller['seller_id'], TRUE);

		/* seller products part */
		$this->data['text_display'] = $this->language->get('text_display');
		$this->data['text_list'] = $this->language->get('text_list');
		$this->data['text_grid'] = $this->language->get('text_grid');
		$this->data['text_sort'] = $this->language->get('text_sort');
		$this->data['text_limit'] = $this->language->get('text_limit');
				
				
		if (isset($this->request->get['sort'])) {
			$order_by = $this->request->get['sort'];
		} else {
			$order_by = 'pd.name';
		}

		if (isset($this->request->get['order'])) {
			$order_way = $this->request->get['order'];
		} else {
			$order_way = 'ASC';
		}
		
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;	
							
		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = $this->config->get('config_catalog_limit');
		}
		
		$this->data['products'] = array();
		
		$sort = array(
			//'filter_category_id' => $category_id, 
			'order_by'               => $order_by,
			'order_way'              => $order_way,
			'page'              => $page,
			'limit'              => $limit
		);
		
		$total_products = $this->msSeller->getTotalSellerProducts($seller['seller_id'], TRUE);
		$products = $this->msSeller->getSellerProducts($seller['seller_id'], $sort, TRUE);
		if (!empty($products)) {
			foreach ($products as $product) {
				$product_data = $this->model_catalog_product->getProduct($product['product_id']);
				if ($product_data['image'] && file_exists(DIR_IMAGE . $product_data['image'])) {
					$image = $this->msImage->resize($product_data['image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
				} else {
					$image = $this->msImage->resize('no_image.jpg', $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
				}

				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($product_data['price'], $product_data['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$price = false;
				}
						
				if ((float)$product_data['special']) {
					$special = $this->currency->format($this->tax->calculate($product_data['special'], $product_data['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$special = false;
				}
				
				if ($this->config->get('config_review_status')) {
					$rating = $product_data['rating'];
				} else {
					$rating = false;
				}
				
				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$product_data['special'] ? $product_data['special'] : $product_data['price']);
				} else {
					$tax = false;
				}
							
				$this->data['seller']['products'][] = array(
					'product_id' => $product['product_id'],
					'thumb' => $image,
					'name' => $product_data['name'],
					'price' => $price,
					'tax' => $tax,
					'special' => $special,
					'rating' => $rating,
					'description' => utf8_substr(strip_tags(html_entity_decode($product_data['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..',					
					'reviews'    => sprintf($this->language->get('text_reviews'), (int)$product_data['reviews']),
					'href'    	 => $this->url->link('product/product', 'product_id=' . $product_data['product_id']),						
				);				
			}
		} else {
			$this->data['seller']['products'] = NULL;
		}
		
		
		$url = '';

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}
						
		$this->data['sorts'] = array();
		
		$this->data['sorts'][] = array(
			'text'  => $this->language->get('ms_sort_nickname_asc'),
			'value' => 'pd.name-ASC',
			'href'  => $this->url->link('product/seller/products', '&sort=pd.name&order=ASC&seller_id=' . $seller['seller_id'] . $url)
		);

		$this->data['sorts'][] = array(
			'text'  => $this->language->get('ms_sort_nickname_desc'),
			'value' => 'pd.name-DESC',
			'href'  => $this->url->link('product/seller/products', '&sort=pd.name&order=DESC&seller_id=' . $seller['seller_id'] . $url)
		);

		/*
		$this->data['sorts'][] = array(
			'text'  => $this->language->get('ms_sort_country_asc'),
			'value' => 'ms.country_id-ASC',
			'href'  => $this->url->link('product/seller/products', '&sort=ms.country_id&order=ASC' . $url)
		); 

		$this->data['sorts'][] = array(
			'text'  => $this->language->get('ms_sort_country_desc'),
			'value' => 'ms.country_id-DESC',
			'href'  => $this->url->link('product/seller/products', '&sort=ms.country_id&order=DESC' . $url)
		); 
		*/
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}	

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		
		$this->data['limits'] = array();
		
		$this->data['limits'][] = array(
			'text'  => $this->config->get('config_catalog_limit'),
			'value' => $this->config->get('config_catalog_limit'),
			'href'  => $this->url->link('product/seller/products', $url . '&limit=' . $this->config->get('config_catalog_limit') . '&seller_id=' . $seller['seller_id'])
		);
					
		$this->data['limits'][] = array(
			'text'  => 25,
			'value' => 25,
			'href'  => $this->url->link('product/seller/products', $url . '&limit=25&seller_id=' . $seller['seller_id'])
		);
		
		$this->data['limits'][] = array(
			'text'  => 50,
			'value' => 50,
			'href'  => $this->url->link('product/seller/products', $url . '&limit=50&seller_id=' . $seller['seller_id'])
		);

		$this->data['limits'][] = array(
			'text'  => 75,
			'value' => 75,
			'href'  => $this->url->link('product/seller/products', $url . '&limit=75&seller_id=' . $seller['seller_id'])
		);
		
		$this->data['limits'][] = array(
			'text'  => 100,
			'value' => 100,
			'href'  => $this->url->link('product/seller/products', $url . '&limit=100&seller_id=' . $seller['seller_id'])
		);
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}	

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}
		
		$pagination = new Pagination();
		$pagination->total = $total_products;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('product/seller/products', $url . '&page={page}');
	
		$this->data['pagination'] = $pagination->render();
		
		$this->data['sort'] = $order_by;
		$this->data['order'] = $order_way;
		$this->data['limit'] = $limit;		
		
		$this->data['ms_catalog_seller_products'] = sprintf($this->language->get('ms_catalog_seller_products_heading'), $seller['nickname']);
		$this->document->setTitle(sprintf($this->language->get('ms_catalog_seller_products_heading'), $seller['nickname']));
		$this->_setBreadcrumbs(sprintf($this->language->get('ms_catalog_seller_products_breadcrumbs'), $seller['nickname']), __FUNCTION__, '&seller_id='.$seller['seller_id']);
		$this->_renderTemplate('ms-catalog-sellerproducts');
  	}
  	
  	public function jxSubmitContactDialog() {
  		if (!isset($this->request->post['seller_id']))
  			return false;
  			
  		$seller_id = $this->request->post['seller_id'];
  		$product_id = $this->request->post['product_id'];
  		$seller_email = $this->msSeller->getSellerEmail($seller_id);
  		$seller_name = $this->msSeller->getSellerName($seller_id);
  		$message_text = trim($this->request->post['ms-sellercontact-text']);
  		$customer_name = mb_substr(trim($this->request->post['ms-sellercontact-name']),0,50);
  		$customer_email = $this->request->post['ms-sellercontact-email'];

		$json = array();

		if (empty($message_text) || empty($customer_name) || empty($customer_email) || empty($this->request->post['ms-sellercontact-captcha'])) {
			$json['errors'][] = $this->language->get('ms_error_contact_allfields');
			$this->response->setOutput(json_encode($json));
			return;
		}

		if (!isset($this->session->data['captcha']) || ($this->session->data['captcha'] != $this->request->post['ms-sellercontact-captcha'])) {
			$json['errors'][] = $this->language->get('ms_error_contact_captcha');
		}

		if (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
			$json['errors'][] = $this->language->get('ms_error_contact_email');			
		}

		if (mb_strlen($message_text) > 2000) {
			$json['errors'][] = $this->language->get('ms_error_contact_text');
		}

		if (!isset($json['errors'])) {	
			$mails[] = array(
				'type' => MsMail::SMT_SELLER_CONTACT,
				'data' => array(
					'recipients' => $seller_email,
					'customer_name' => $customer_name,
					'customer_email' => $customer_email,
					'customer_message' => $message_text,
					'product_id' => $product_id,
					'addressee' => $seller_name
				)
			);  		
	  		$this->msMail->sendMails($mails);
  			$json['success'] = $this->language->get('ms_sellercontact_success');
  		}
  		$this->response->setOutput(json_encode($json));
  	}
  	
  	public function jxRenderContactDialog() {
  		if (isset($this->request->get['product_id'])) {
			$seller_id = $this->msProduct->getSellerId($this->request->get['product_id']);
			$this->data['product_id'] = (int)$this->request->get['product_id'];
  		} else {
			$seller_id = $this->request->get['seller_id'];
			$this->data['product_id'] = 0;
  		}
		$seller = $this->msSeller->getSellerData($seller_id);
		
		if (empty($seller))
			return false;

		$this->data['seller_id'] = $seller_id;			
		
		$this->data['customer_email'] = $this->customer->getEmail();
		$this->data['customer_name'] = $this->customer->getFirstname() . ' ' . $this->customer->getLastname();
		
		if (!empty($seller['avatar_path']))
			$this->data['seller_thumb'] = $this->msImage->resize($seller['avatar_path'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
			
		$this->data['seller_href'] = $this->url->link('product/seller/profile', 'seller_id=' . $seller['seller_id']);
		$this->data['ms_sellercontact_sendmessage'] = sprintf($this->language->get('ms_sellercontact_sendmessage'), $seller['nickname']);
		
  		return $this->_renderTemplate('ms-catalog-sellercontact');
  	}
  	
}
?>