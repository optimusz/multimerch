<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>

<div id="content" class="ms-account-order">
	<?php echo $content_top; ?>
	
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
	
	<h1><?php echo $ms_account_orders_heading; ?></h1>
	
	<table class="list" id="list-orders">
		<thead>
			<tr>
				<td class="tiny"><?php echo $ms_account_orders_id; ?></td>
				<td class="large"><?php echo $ms_account_orders_customer; ?></td>
				<td><?php echo $ms_account_orders_products; ?></td>
				<td><?php echo $ms_status; ?></td>
				<td class="medium"><?php echo $ms_date_created; ?></td>
				<td class="small"><?php echo $ms_account_orders_total; ?></td>
				<td class="small"><?php echo $ms_account_orders_view; ?></td>
			</tr>
			<tr class="filter">
				<td><input type="text"/></td>
				<td><input type="text"/></td>
				<td><input type="text"/></td>
				<td></td>
				<td><input type="text"/></td>
				<td><input type="text"/></td>
				<td></td>
			</tr>
		</thead>
		
		<tbody></tbody>
	</table>
	
	<div class="buttons">
		<div class="left">
			<a href="<?php echo $link_back; ?>" class="button">
				<span><?php echo $button_back; ?></span>
			</a>
		</div>
	</div>
	
	<?php echo $content_bottom; ?>
</div>

<script>
	$(function() {
		$('#list-orders').dataTable( {
			"sAjaxSource": $('base').attr('href') + "index.php?route=seller/account-order/getTableData",
			"aoColumns": [
				{ "mData": "order_id" },
				{ "mData": "customer_name" },
				{ "mData": "products", "bSortable": false, "sClass": "products" },
				{ "mData": "suborder_status", "bSortable": false },
				{ "mData": "date_created" },
				{ "mData": "total_amount" },
				{ "mData": "view_order" }
			],
			"aaSorting":  [[4,'desc']]
		});
	});
</script>

<?php echo $footer; ?>
