<?php
class MsStatistic extends Model {
	public function getSalesByDay($data){
		$sql = "SELECT count(DISTINCT order_id) as order_num, SUM(seller_net_amt) as total_revenue, AVG(seller_net_amt) as average_revenue
		FROM `" . DB_PREFIX . "order` o
		INNER JOIN `" . DB_PREFIX . "ms_order_product_data` mopd
		USING (order_id)
		WHERE  1 = 1"
		. (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '')
		. (isset($data['date']) ? " AND DATE(date_added) =  DATE('" .  $data['date'] . "')"  : '');

		$res = $this->db->query($sql);

		return $res->row;
	}

	public function getSalesByMonth($data){
		$sql = "SELECT count(DISTINCT order_id) as order_num, SUM(seller_net_amt) as total_revenue, AVG(seller_net_amt) as average_revenue
		FROM `" . DB_PREFIX . "order` o
		INNER JOIN `" . DB_PREFIX . "ms_order_product_data` mopd
		USING (order_id)
		WHERE  1 = 1"
		. (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '')
		. (isset($data['date']) ? " AND YEAR(date_added) =  YEAR('" .  $data['date'] . "') AND MONTH(date_added) = MONTH('" .  $data['date'] . "') "  : '');

		$res = $this->db->query($sql);

		return $res->row;
	}

	public function getGrandTotalSales($data){
		$sql = "SELECT SUM(seller_net_amt) as grand_total
		FROM `" . DB_PREFIX . "order` o
		INNER JOIN `" . DB_PREFIX . "ms_order_product_data` mopd
		USING (order_id)
		WHERE  1 = 1"
		. (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '');

		$res = $this->db->query($sql);

		$grand_total = $res->rows ? $res->row['grand_total'] : 0;
		$grand_total = $grand_total ? $grand_total : 0;

		return $grand_total;
	}

	public function getStatsByProducts($data, $sort = array(), $cols = array()) {
		$hFilters = $wFilters = '';

		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				if (!isset($cols[$k])) {
					$wFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				} else {
					$hFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				}
			}
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS  product_id, name, SUM(seller_net_amt) AS total_by_product, SUM(quantity) as sold
				FROM " . DB_PREFIX . "order_product
				LEFT JOIN " . DB_PREFIX . "ms_order_product_data
					USING(order_id, product_id)
				WHERE 1 = 1"
				. (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '')

				. $wFilters

				. " GROUP BY product_id  HAVING 1 = 1 "

				. $hFilters

				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '');

		$res = $this->db->query($sql);
		$total = $this->db->query("SELECT FOUND_ROWS() as total");

		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];
		return $res->rows;
	}

	public function getYearsOfOrdersBySeller($seller_id){
		$sql = "SELECT YEAR(date_added) as year
				FROM `" . DB_PREFIX . "order` o
				INNER JOIN `" . DB_PREFIX . "ms_order_product_data` mopd
				USING (order_id)
				WHERE seller_id = " . (int)$seller_id
				. " GROUP BY YEAR(date_added)
				ORDER BY year DESC";

		$res = $this->db->query($sql);

		$result = array();
		foreach($res->rows as $row){
			$result[] = $row['year'];
		}

		return $result;
	}

	public function getSalesByYear($seller_id, $year){
		$sql = "SELECT SUM(quantity) as sales
				FROM `" . DB_PREFIX . "order` o
				INNER JOIN `" . DB_PREFIX . "ms_order_product_data` mopd
				USING ( order_id )
				LEFT JOIN " . DB_PREFIX . "order_product
				USING ( order_id, product_id )
				WHERE seller_id =  " . (int)$seller_id
				. " AND YEAR( date_added ) = " .(int)$year;


		$res = $this->db->query($sql);

		$sales = $res->rows ? $res->row['sales'] : 0;

		return $sales;
	}


	public function getTotalByYear($seller_id, $year){
		$sql = "SELECT count(DISTINCT order_id) as order_num, SUM(seller_net_amt) as total_revenue, AVG(seller_net_amt) as average_revenue
				FROM `" . DB_PREFIX . "order` o
				INNER JOIN `" . DB_PREFIX . "ms_order_product_data` mopd
				USING ( order_id )
				LEFT JOIN " . DB_PREFIX . "order_product
				USING ( order_id, product_id )
				WHERE seller_id =  " . (int)$seller_id
				. " AND YEAR( date_added ) = " .(int)$year;

		$res = $this->db->query($sql);

		$result = array(
			'order_num' => $res->rows ? $res->row['order_num'] : 0,
			'total_revenue' => $res->rows ? $res->row['total_revenue'] : 0,
			'average_revenue' => $res->rows ? $res->row['average_revenue'] : 0
		);

		return $result;
	}


	public function getStatsByYears($data, $sort = array()){
		$sql = "SELECT date_added, MONTH(date_added) as month, count(DISTINCT order_id) as order_num, SUM(seller_net_amt) as total_revenue, AVG(seller_net_amt) as average_revenue
		FROM `" . DB_PREFIX . "order` o
		INNER JOIN `" . DB_PREFIX . "ms_order_product_data` mopd
		USING (order_id)
		WHERE  1 = 1"
		. (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '')
		. (isset($data['year']) ? " AND YEAR(date_added) =  " .  (int)$data['year'] : '')
		. " GROUP BY month"

		. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : 'ORDER BY month DESC"');

		$res = $this->db->query($sql);


		$result = array_fill(1, 12, false);

		foreach($res->rows as $month){

			$result[$month['month']]['date_added'] =  $month['date_added'];
			$result[$month['month']]['order_num'] =  $month['order_num'];
			$result[$month['month']]['total_revenue'] =  $month['total_revenue'];
			$result[$month['month']]['average_revenue'] =  $month['average_revenue'];
		}

		return $result;
	}

}
?>