<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>

<div id="content" class="ms-account-transaction">
	<?php echo $content_top; ?>
	
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
	
	<h1><?php echo $ms_account_transactions_heading; ?></h1>
	
	<?php if (isset($success) && ($success)) { ?>
		<div class="success"><?php echo $success; ?></div>
	<?php } ?>
	
	<?php if (isset($error_warning) && ($error_warning)) { ?>
		<div class="warning"><?php echo $error_warning; ?></div>
	<?php } ?>
	
	<?php echo $ms_account_transactions_balance; ?> <b><?php echo $ms_balance_formatted; ?></b> <span style="color: gray"><?php echo $ms_reserved_formatted; ?></span><br /><br />
	<?php echo $ms_account_transactions_earnings; ?> <b><?php echo $earnings; ?></b><br /><br />	

	<!-- BALANCE RECORDS -->
	<h2><?php echo $ms_account_transactions_records; ?></h2>
	<table class="list" style="text-align: center" id="list-transactions">
		<thead>
			<tr>
				<td class="tiny"><?php echo $ms_id; ?></td>
				<td class="small"><?php echo $ms_account_transactions_amount; ?></td>
				<td><?php echo $ms_account_transactions_description; ?></td>
				<td class="medium"><?php echo $ms_date_created; ?></td>
			</tr>
			
			<tr class="filter">
				<td><input type="text"/></td>
				<td><input type="text"/></td>
				<td><input type="text"/></td>
				<td><input type="text"/></td>
			</tr>
		</thead>
		
		<tbody>
		</tbody>
	</table>
	
	<br />
	
	<!-- PAYMENTS -->
	<h2><?php echo $ms_payment_payments; ?></h2>
	<table class="list" style="text-align: center" id="list-payments">
	<thead>
	<tr>
		<td class="tiny"><?php echo $ms_id; ?></td>
		<td class="medium"><?php echo $ms_type; ?></td>
		<td class="small"><?php echo $ms_amount; ?></td>
		<td><?php echo $ms_description; ?></td>
		<td class="medium"><?php echo $ms_status; ?></td>
		<td class="medium"><?php echo $ms_date_paid; ?></td>
	</tr>
	
	<tr class="filter">
		<td><input type="text"/></td>
		<td></td>
		<td><input type="text"/></td>
		<td><input type="text"/></td>
		<td></td>
		<td><input type="text"/></td>
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
	$('#list-transactions').dataTable( {
		"sAjaxSource": $('base').attr('href') + "index.php?route=seller/account-transaction/getTransactionData",
		"aoColumns": [
			{ "mData": "transaction_id" },
			{ "mData": "amount" },
			{ "mData": "description", "bSortable": false },
			{ "mData": "date_created" }
		],
        "aaSorting":  [[3,'desc']]
	});

	$('#list-payments').dataTable( {
		"sAjaxSource": $('base').attr('href') + "index.php?route=seller/account-transaction/getPaymentData",
		"aoColumns": [
			{ "mData": "payment_id" },
			{ "mData": "payment_type" },
			{ "mData": "amount" },
			{ "mData": "description", "bSortable": false },
			{ "mData": "payment_status" },
			{ "mData": "date_created" },
		],
        "aaSorting":  [[5,'desc']]
	});
});
</script>

<?php echo $footer; ?>