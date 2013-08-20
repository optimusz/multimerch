<?php
class ControllerModuleMsComments extends Controller {
	public function __construct($registry) {
		parent::__construct($registry);
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'));
	}

	public function renderComments() {
        if(isset($this->request->get['product_id'])){
            $product_id = $this->request->get['product_id'];
            $this->data['product_id'] = $product_id;
        }elseif(isset($this->request->get['seller_id'])){
            $seller_id = $this->request->get['seller_id'];
            $this->data['seller_id'] = $seller_id;
        }
		

        if(isset($seller_id)){
            $comments_per_page = $this->config->get('msconf_seller_comments_perpage');
            $query_array = array(
                'seller_id' => $seller_id,
                'displayed' => 1,
            );
            $comments_template = 'catalog-seller-comments';
            $total_comments_array = array(
                'displayed' => 1,
                'seller_id' => $seller_id                
            );
            $pagination_url = '&page={page}' . '&seller_id=' . $seller_id;
        }else{
            $comments_per_page = $this->config->get('msconf_seller_perpage');
            $query_array = array(
                'product_id' => $product_id,
                'displayed' => 1,
            );
            $comments_template = 'catalog-comments';
            $total_comments_array = array(
                'displayed' => 1,
                'product_id' => $product_id                
            );
            $pagination_url = '&page={page}' . '&product_id=' . $product_id;
        }

        if ((int)$comments_per_page == 0)
            $comments_per_page = 10;

        $page = (isset($this->request->get['page'])) ? (int)$this->request->get['page'] : 1;

        $this->data['ms_comments'] = $this->MsLoader->MsComments->getComments(
            $query_array,
            array(
                'order_by' => 'mc.create_time',
                'order_way' => 'DESC',
                'offset' => ($page - 1) * $comments_per_page,
                'limit' => $comments_per_page
            )
        );

        $total_comments = $this->MsLoader->MsComments->getTotalComments($total_comments_array);
        $pagination = new Pagination();
        $pagination->total = $total_comments;
        $pagination->page = $page;
        $pagination->limit = $comments_per_page;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->url->link('module/productcomments/renderComments', $pagination_url);
        $this->data['pagination'] = $pagination->render();

        list($this->template, $this->children) = $this->MsLoader->MsHelper->loadTemplate($comments_template, array());

		$this->response->setOutput($this->render());	
	}
	
	public function renderForm() {
		if (!$this->config->get('msconf_comments_enable')) return;
		
		$product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;
		$seller_id = isset($this->request->get['seller_id']) ? (int)$this->request->get['seller_id'] : 0;
		if  (!$product_id && !$seller_id) return;

        if($seller_id){
            $msconf_comments_allow_guests = $this->config->get('msconf_seller_comments_allow_guests');
            $msconf_comments_enforce_customer_data = $this->config->get('msconf_seller_comments_enforce_customer_data');
            $msconf_comments_maxlen = $this->config->get('msconf_seller_comments_maxlen');
            $comments_template = 'catalog-seller-comments-form';
        }else{
            $msconf_comments_allow_guests = $this->config->get('msconf_comments_allow_guests');
            $msconf_comments_enforce_customer_data = $this->config->get('msconf_comments_enforce_customer_data');
            $msconf_comments_maxlen = $this->config->get('msconf_comments_maxlen');
            $comments_template = 'catalog-comments-form';
        }

		if  (!$this->customer->isLogged() && !$msconf_comments_allow_guests) {
			echo sprintf($this->language->get('ms_comments_login_register'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL'));
		} else {
			$this->data['mc_name'] = $this->customer->getFirstName() . ' ' . $this->customer->getLastName();
			$this->data['mc_email'] = $this->customer->getEmail();
			$this->data['mc_logged'] = $this->customer->isLogged();
			
			$this->data['msconf_comments_maxlen'] = $msconf_comments_maxlen;
			$this->data['msconf_comments_enforce_customer_data'] = $msconf_comments_enforce_customer_data;
 			$this->data['product_id'] = $product_id;
			$this->data['seller_id'] = $seller_id;

			list($this->template, $this->children) = $this->MsLoader->MsHelper->loadTemplate($comments_template, array());
			$this->response->setOutput($this->render());
		}
	}
	
	public function submitComment() {
        $comment = $this->request->get;
        
        if(isset($comment['seller_id'])){
            if (!$this->config->get('msconf_seller_comments_enable')) return;

            $msconf_comments_enforce_customer_data = $this->config->get('msconf_seller_comments_enforce_customer_data');
            $msconf_comments_allow_guests = $this->config->get('msconf_seller_comments_allow_guests');
            $msconf_comments_maxlen = $this->config->get('msconf_seller_comments_maxlen');
            $msconf_comments_enable_customer_captcha = $this->config->get('msconf_seller_comments_enable_customer_captcha');
        }else{
            if (!$this->config->get('msconf_comments_enable')) return;

            $msconf_comments_enforce_customer_data = $this->config->get('msconf_comments_enforce_customer_data');
            $msconf_comments_allow_guests = $this->config->get('msconf_comments_allow_guests');
            $msconf_comments_maxlen = $this->config->get('msconf_comments_maxlen');
            $msconf_comments_enable_customer_captcha = $this->config->get('msconf_comments_enable_customer_captcha');
        }
			
        if (!isset($this->request->get['product_id']) && !isset($this->request->get['seller_id'])) return;
            
        if(isset($this->request->get['product_id'])){
            $comment['product_id'] = $this->request->get['product_id'];
        }else if (isset($this->request->get['seller_id'])){
            $comment['seller_id'] = $this->request->get['seller_id'];
        }
		
		$json = array();
		
		//if (is admin) {
			//admin logged in
		//} else 	
		if ($this->customer->isLogged()) {
			//customer logged in
			$comment['customer_id'] = $this->customer->getId();
			if  ($this->config->get('msconf_comments_enforce_customer_data')) {
				$comment['name'] = $this->customer->getFirstName() . ' ' . $this->customer->getLastName();
	    		$comment['email'] = $this->customer->getEmail();
			} else {
	    		$comment['name'] = trim(strip_tags(htmlspecialchars_decode($this->request->post['mc_name'])));
	    		$comment['email'] = trim(strip_tags(htmlspecialchars_decode($this->request->post['mc_email'])));
			}			
		} else {
			// guest
			$comment['customer_id'] = 0;
			if (!$this->config->get('msconf_comments_allow_guests')) {
				//$json['errors'][] = 'Not allowed to post';
				return;			
			} else {
	    		$comment['name'] = trim(strip_tags(htmlspecialchars_decode($this->request->post['mc_name'])));
	    		$comment['email'] = trim(strip_tags(htmlspecialchars_decode($this->request->post['mc_email'])));
			}
		}

	    $comment['comment'] = trim(strip_tags(htmlspecialchars_decode($this->request->post['mc_text'])));

		if (mb_strlen($comment['name'],'UTF-8') < 3 || mb_strlen($comment['name'],'UTF-8') > 25) {
			$json['errors'][] = sprintf($this->language->get('ms_comments_error_name'), 3, 25);
		}

		if (!filter_var($comment['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($comment['email'], 'UTF-8') > 128) {
			$json['errors'][] = $this->language->get('ms_comments_error_email');
		}

		if (mb_strlen($comment['comment']) < 10) {
			$json['errors'][] = sprintf($this->language->get('ms_comments_error_comment_short'), 10);
		}

		if (mb_strlen($comment['comment']) > $msconf_comments_maxlen && $msconf_comments_maxlen > 0) {
			$json['errors'][] = sprintf($this->language->get('ms_comments_error_comment_long'), $msconf_comments_maxlen);
		}

		if (!$this->customer->isLogged() || ($this->customer->isLogged() && $msconf_comments_enable_customer_captcha)) {
			if (!isset($this->request->post['mc_captcha']) || ($this->session->data['captcha'] != $this->request->post['mc_captcha'])) {
				$json['errors'][] = $this->language->get('ms_comments_error_captcha');
			}
		}
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && !isset($json['errors'])) {
			$this->MsLoader->MsComments->addComment($comment);
			$json['success'] = $this->language->get('ms_comments_success');
		}
		
		$this->response->setOutput(json_encode($json));
	}	
}
?>
