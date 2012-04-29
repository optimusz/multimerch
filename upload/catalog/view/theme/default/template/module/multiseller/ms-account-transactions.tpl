<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>

<div id="content"><?php echo $content_top; ?>

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
  <?php echo $ms_account_transactions_balance; ?> <b><?php echo $balance; ?></b>
	<table class="list">
	<thead>
		<tr>
			<td class="left"><?php echo $ms_account_transactions_date_created; ?></td>
			<!--<td class="left"><?php echo $ms_account_transactions_date_modified; ?></td>-->
			<td class="left"><?php echo $ms_account_transactions_description; ?></td>
			<td class="left"><?php echo $ms_account_transactions_amount; ?></td>
			<!--<td class="left"><?php echo $ms_account_transactions_status; ?></td>-->			
		</tr>
	</thead>
	<tbody>
		<?php if ($transactions) { ?>
		<?php foreach ($transactions  as $transaction) { ?>
		<tr <?php if ($transaction['transaction_status_id'] == MsTransaction::MS_TRANSACTION_STATUS_PENDING) { ?>class="ms-pending"<?php }?>>
			<td class="left"><?php echo $transaction['date_created']; ?></td>
			<!--<td class="left"><?php echo $transaction['date_modified']; ?></td>-->
			<td class="left"><?php echo $transaction['description']; ?></td>
			<td class="left"><?php echo $transaction['net_amount']; ?></td>
			<!--<td class="left"><?php echo $transaction['status']; ?></td>-->			
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
	<div class="pagination"><?php echo $pagination; ?></div>		
  
    <div class="buttons">
    	<div class="right"><a href="<?php echo $continue; ?>" class="button"><span><?php echo $button_continue; ?></span></a></div>
    </div>
  
  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?>