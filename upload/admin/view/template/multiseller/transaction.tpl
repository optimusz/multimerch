<?php echo $header; ?>

<div id="content" class="ms-transaction-page">
	<div class="breadcrumb">
	<?php foreach ($breadcrumbs as $breadcrumb) { ?>
	<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
	<?php } ?>
	</div>
	
	<?php if ($error_warning) { ?>
	<div class="warning"><?php echo $error_warning; ?></div>
	<?php } ?>
	
	<?php if ($success) { ?>
	<div class="success"><?php echo $success; ?></div>
	<?php } ?>
	
	<div class="box">
		<div class="heading">
			<h1><img src="view/image/multiseller/ms-book.png" alt="" /> <?php echo $ms_transactions_heading; ?></h1>
			<div class="buttons">
				<a onclick="location = '<?php echo $link_create_transaction; ?>'" class="button"><?php echo $ms_transactions_new; ?></a>
			</div>			
		</div>
		
		<div class="content">
		<table class="list" style="text-align: center" id="list-transactions">
		<thead>
			<tr>
				<td class="tiny"><?php echo $ms_id; ?></td>
				<td class="medium"><?php echo $ms_seller; ?></a></td>
				<td class="small"><?php echo $ms_net_amount; ?></a></td>
				<td><?php echo $ms_description; ?></a></td>
				<td class="medium"><?php echo $ms_date; ?></a></td>
			</tr>
			<tr class="filter">
				<td><input type="text"/></td>
				<td><input type="text"/></td>
				<td><input type="text"/></td>
				<td><input type="text"/></td>
				<td><input type="text"/></td>
			</tr>
		</thead>
		<tbody></tbody>
		</table>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	$('#list-transactions').dataTable( {
		"sAjaxSource": "index.php?route=multiseller/transaction/getTableData&token=<?php echo $token; ?>",
		"aoColumns": [
			{ "mData": "id" },
			{ "mData": "seller" },
			{ "mData": "amount" },
			{ "mData": "description" },
			{ "mData": "date_created" },
		],
        "aaSorting":  [[4,'desc']]
	});
});
</script>
<?php echo $footer; ?> 