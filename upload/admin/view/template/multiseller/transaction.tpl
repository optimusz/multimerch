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
			<h1><img src="view/image/customer.png" alt="" /> <?php echo $ms_finances_transactions_heading; ?></h1>
		</div>
		
		<div class="content">
		<form id="form">
		<table class="list" style="text-align: center">
		<thead>
			<tr>
			<td><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
			<td><?php echo $ms_seller; ?></a></td>
			<td><?php echo $ms_net_amount; ?></a></td>
			<td><?php echo $ms_description; ?></a></td>
			<td><?php echo $ms_date; ?></a></td>
			</tr>
		</thead>
		
		<tbody>
			<?php if (isset($transactions) && $transactions) { ?>
			<?php foreach ($transactions as $transaction) { ?>
			<tr>
				<td><input type="checkbox" name="selected[]" value="<?php echo $transaction['request_id']; ?>" /></td>
				<td><?php echo $transaction['seller']; ?></td>
				<td><?php echo $transaction['net_amount']; ?></td>
				<td><?php echo $transaction['description']; ?></td>
				<td><?php echo $transaction['date_created']; ?></td>
			</tr>
			<?php } ?>
			<?php } else { ?>
			<tr>
				<td class="center" colspan="10"><?php echo $text_no_results; ?></td>
			</tr>
			<?php } ?>
		</tbody>
		</table>
		</form>
		
		<div class="pagination"><?php echo $pagination; ?></div>
		
		</div>
	</div>
</div>

<?php echo $footer; ?> 