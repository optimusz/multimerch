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
	<table class="list" style="text-align: center">
		<thead>
			<tr>
				<td width="1"><?php echo $ms_id; ?></td>
				<td><?php echo $ms_account_transactions_amount; ?></td>
				<td><?php echo $ms_account_transactions_description; ?></td>
				<td><?php echo $ms_date_created; ?></td>
			</tr>
		</thead>
		
		<tbody>
		<?php if (isset($transactions) && $transactions) { ?>
			<?php foreach ($transactions as $transaction) { ?>
			<tr>
				<td><?php echo $transaction['balance_id']; ?></td>
				<td><?php echo $transaction['amount']; ?></td>
				<td><?php echo $transaction['description']; ?></td>
				<td><?php echo $transaction['date_created']; ?></td>
			</tr>
			<?php } ?>
		<?php } else { ?>
			<tr>
				<td class="center" colspan="3"><?php echo $ms_account_transactions_notransactions; ?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	
	<br />
	
	<!--<div class="pagination"><?php echo $pagination; ?></div>-->
	
	<!-- PAYMENTS -->
	<h2><?php echo $ms_payment_payments; ?></h2>
	<table class="list" style="text-align: center">
	<thead>
	<tr>
		<td width="1" style="text-align: center;"><?php echo $ms_id; ?></td>
		<td style="width: 150px"><?php echo $ms_type; ?></td>
		<td style="width: 50px"><?php echo $ms_amount; ?></td>
		<td><?php echo $ms_description; ?></td>
		<td style="width: 100px"><?php echo $ms_status; ?></td>
		<td style="width: 120px"><?php echo $ms_date_paid; ?></td>
	</tr>
	</thead>
	
	<tbody>
		<?php if (isset($payments) && $payments) { ?>
		<?php $msPayment = new ReflectionClass('MsPayment'); ?>
		<?php foreach ($payments as $payment) { ?>
			<tr>
				<td style="text-align: center;"><?php echo $payment['payment_id']; ?></td>
				<td><?php echo ($payment['payment_type'] == MsPayment::TYPE_SALE ? $this->language->get('ms_payment_type_' . $payment['payment_type']) . ' (' . sprintf($this->language->get('ms_payment_order'), $payment['order_id']) . ')' : $this->language->get('ms_payment_type_' . $payment['payment_type'])); ?></td>
				<td><?php echo $payment['amount_text']; ?></td>
				<td><?php echo $payment['description']; ?></td>
				<td><?php echo $this->language->get('ms_payment_status_' . $payment['payment_status']); ?></td>
				<td><?php echo $payment['date_paid']; ?></td>
			</tr>
		<?php } ?>
		<?php } else { ?>
			<tr><td class="center" colspan="10"><?php echo $text_no_results; ?></td></tr>
		<?php } ?>
	</tbody>
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

<?php echo $footer; ?>