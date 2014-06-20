<?php

class ControllerSellerAccountStats extends ControllerSellerAccount {
	public function index() {
		$this->data['link_back'] = $this->url->link('account/account', '', 'SSL');

		$this->document->setTitle($this->language->get('ms_account_stats_heading'));
		$this->document->addScript('catalog/view/javascript/jquery/tabs.js');
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_dashboard_breadcrumbs'),
				'href' => $this->url->link('seller/account-dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_stats_breadcrumbs'),
				'href' => $this->url->link('seller/account-stats', '', 'SSL'),
			)
		));

		$seller_id = $this->customer->getId();

		$years = $this->MsLoader->MsStatistic->getYearsOfOrdersBySeller($seller_id);
		if(empty($years)){
			$years = array(date("Y")=>date("Y"));
		}

		$years = array_combine($years, $years); // Copy value as key
		$years = str_replace(date("Y"), $this->language->get('ms_account_stats_this_year'), $years); // Replace this year
		$this->data['years'] = $years;

		$sales = $this->MsLoader->MsStatistic->getSalesByYear($seller_id, date("Y"));
		$this->data['sales'] = $sales;

		$today = date("Y-m-d");
		$yesterday = date("Y-m-d",strtotime($today)-86400);

		$summary_today =  $this->MsLoader->MsStatistic->getSalesByDay(
			array(
				'seller_id' => $seller_id,
				'date'=> $today
			)
		);

		$summary_yesterday =  $this->MsLoader->MsStatistic->getSalesByDay(
			array(
				'seller_id' => $seller_id,
				'date'=> $yesterday
			)
		);

		$this->data['today'] = $today;
		$this->data['yesterday'] = $yesterday;
		$this->data['summary_today'] = $summary_today;
		$this->data['summary_yesterday'] = $summary_yesterday;

		$summary_month =  $this->MsLoader->MsStatistic->getSalesByMonth(
			array(
				'seller_id' => $seller_id,
				'date'=> $today
			)
		);

		$day_of_month = date("j");
		$summary_month_daily['order_num'] = round($summary_month['order_num'] / $day_of_month, 2);
		$summary_month_daily['total_revenue'] = $summary_month['total_revenue'] / $day_of_month;
		$summary_month_daily['average_revenue'] = $summary_month['average_revenue'] / $day_of_month;

		$this->data['summary_month_daily'] = $summary_month_daily;

		$days_in_month = date("t");
		$summary_month_projected['order_num'] = round($summary_month['order_num'] / $day_of_month * $days_in_month, 2);
		$summary_month_projected['total_revenue'] = $summary_month['total_revenue'] / $day_of_month * $days_in_month;
		$summary_month_projected['average_revenue'] = $summary_month['average_revenue'] / $day_of_month * $days_in_month;

		$this->data['summary_month_projected'] = $summary_month_projected;

		$grand_total =  $this->MsLoader->MsStatistic->getGrandTotalSales(
			array(
				'seller_id' => $seller_id
			)
		);

		$this->data['grand_total'] = $grand_total;


		list($this->template, $this->children) = $this->MsLoader->MsHelper->loadTemplate('account-stats');
		$this->response->setOutput($this->render());
	}

	public function getSalesByYear(){
		if(isset($this->request->get['year'])){
			$year = (int)$this->request->get['year'];
		}else{
			$year = date("Y");
		}

		$seller_id = $this->customer->getId();

		$sales = $this->MsLoader->MsStatistic->getSalesByYear($seller_id, $year);

		$this->response->setOutput(json_encode(array(
			'sales' => $sales
		)));
	}

	public function getTotalByYear(){
		if(isset($this->request->get['year'])){
			$year = (int)$this->request->get['year'];
		}else{
			$year = date("Y");
		}

		$seller_id = $this->customer->getId();

		$result = $this->MsLoader->MsStatistic->getTotalByYear($seller_id, $year);

		$columns = array();
		$columns[] = array(
			'total_text' => $this->language->get('ms_account_stats_total') . " " . $year,
			'order_num' => (int)$result['order_num'],
			'total_revenue' => $this->currency->format($result['total_revenue'], $this->config->get('config_currency')),
			'average_revenue' => $this->currency->format($result[ 'average_revenue'], $this->config->get('config_currency'))
		);

		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => 1,
			'iTotalDisplayRecords' => 1,
			'aaData' => $columns
		)));
	}

	public function getByProductData() {
		$colMap = array(
			'total_formatted' => 'total_by_product',
			'product_html' => 'name'
		);

		$sorts = array('product_id', 'sold', 'total_formatted');
		$filters = array_merge($sorts, array('product_html'));

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$seller_id = $this->customer->getId();

		$products = $this->MsLoader->MsStatistic->getStatsByProducts(
			array(
				'seller_id' => $seller_id
			),
			array(
				'order_by'  => $sortCol,
				'order_way' => $sortDir,
				'offset' => $this->request->get['iDisplayStart'],
				'limit' => $this->request->get['iDisplayLength'],
				'filters' => $filterParams
			),
			array(
				'sold' => 1,
				'total_by_product' => 1,
			)
		);

		$columns = array();
		foreach($products as $p){
			$product_html = "<span class='name'><a href='" . $this->url->link('product/product', 'product_id=' . $p['product_id'], 'SSL') . "'>{$p['name']}</a></span>";
			$columns[] = array_merge(
				$p,
				array(
					'product_html' => $product_html,
					'total_formatted' => $this->currency->format($p['total_by_product'], $this->config->get('config_currency'))
				)
			);
		}

		$total = isset($products[0]) ? $products[0]['total_rows'] : 0;

		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => $total,
			'iTotalDisplayRecords' => $total,
			'aaData' => $columns
		)));
	}

	public function getByYearData() {
		$colMap = array('date_added' => 'date_added');

		$sorts = array('month', 'order_num', 'total_revenue', 'average_revenue');

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);

		$seller_id = $this->customer->getId();

		if(isset($this->request->get['year'])){
			$year = (int)$this->request->get['year'];
		}else{
			$year = date("Y");
		}


		$months = $this->MsLoader->MsStatistic->getStatsByYears(
			array(
				'seller_id' => $seller_id,
				'year' => $year
			),
			array(
				'order_by'  => $sortCol,
				'order_way' => $sortDir,
				'offset' => $this->request->get['iDisplayStart'],
				'limit' => $this->request->get['iDisplayLength']
			)
		);

		$columns = array();
		foreach($months as $key=>$m){
			if($m === false){
				$columns[] = array(
					'date_added' => date("M Y", strtotime($year . "-" . $key . "-" . "01")),
					'order_num' => 0,
					'total_revenue' => $this->currency->format($m['total_revenue'], $this->config->get('config_currency')),
					'average_revenue' => $this->currency->format($m['average_revenue'], $this->config->get('config_currency'))
				);
			}else{
				$m['date_added'] = date("M Y", strtotime($m['date_added']));
				$m['total_revenue'] = $this->currency->format($m['total_revenue'], $this->config->get('config_currency'));
				$m['average_revenue'] = $this->currency->format($m['average_revenue'], $this->config->get('config_currency'));
				$columns[] = $m;
			}
		}

		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => 12,
			'iTotalDisplayRecords' => 12,
			'aaData' => $columns
		)));
	}

}

?>
