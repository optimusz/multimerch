<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>

<div id="content" class="ms-account-withdrawal">
	<?php echo $content_top; ?>
	
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
	
	<h1><?php echo $ms_account_withdraw_heading; ?></h1>
	
	<?php if (isset($error_warning) && ($error_warning)) { ?>
		<div class="warning"><?php echo $error_warning; ?></div>
	<?php } ?>
	
	<p><?php echo $ms_account_withdraw_balance; ?> <b><?php echo $ms_account_balance_formatted; ?></b> <span style="color: gray"><?php echo $ms_account_reserved_formatted; ?></span></p>
	<p><?php echo $ms_account_withdraw_balance_available; ?> <b><?php echo $balance_available_formatted; ?></b></p>
	<p><?php echo $ms_account_withdraw_minimum; ?> <b><?php echo $this->currency->format($this->config->get('msconf_minimum_withdrawal_amount'),$this->config->get('config_currency')); ?></b></p>
	
	<?php if ($balance_available <= 0) { ?>
		<div class="attention"><?php echo $ms_account_withdraw_no_funds; ?></div>
	<?php } else if (!isset($paypal) || empty($paypal)) { ?>
		<div class="attention"><?php echo $ms_account_withdraw_no_paypal; ?></div>
	<?php } else if (!$withdrawal_minimum_reached) { ?>
		<div class="attention"><?php echo $ms_account_withdraw_minimum_not_reached; ?></div>
	<?php } ?>
	
	<form id="ms-withdrawal" class="ms-form">
		<div class="content">
			<?php if (!$withdrawal_minimum_reached || !isset($paypal) || empty($paypal) || $balance_available <= 0) { ?>
			<div class="ms-overlay"></div>
			<?php } ?>
			
			<table class="ms-product">
				<tr>
					<td><?php echo $ms_account_withdraw_amount; ?></td>
					<td>
						<?php if ($msconf_allow_partial_withdrawal) { ?>
						<p>
							<input type="radio" name="withdraw_all" value="0" checked="checked" />
							<input type="text" name="withdraw_amount" value="<?php echo $this->currency->format($this->config->get('msconf_minimum_withdrawal_amount'),$this->config->get('config_currency'), '', FALSE); ?>" />
							<?php echo $currency_code; ?>
						</p>
						<?php } ?>
						
						<p>
							<input type="radio" name="withdraw_all" value="1" <?php if (!$msconf_allow_partial_withdrawal) { ?>checked="checked"<?php } ?> />
							<span><?php echo $ms_account_withdraw_all; ?> (<?php echo $balance_available_formatted; ?>)</span>
						</p>
						<p class="ms-note"><?php echo $ms_account_withdraw_amount_note; ?></p>
						<p class="error" id="error_withdraw_amount"></p>
					</td>
				</tr>
				
				<tr>
					<td><?php echo $ms_account_withdraw_method; ?></td>
					<td>
						<p>
							<input type="radio" name="withdraw_method" value="paypal" checked="checked" />
							<span><?php echo $ms_account_withdraw_method_paypal; ?></span>
						</p>
						<p class="ms-note"><?php echo $ms_account_withdraw_method_note; ?></p>
						<p class="error" id="error_withdraw_method"></p>
					</td>
				</tr>
			</table>
		</div>
		
		<div class="buttons">
			<div class="left">
				<a href="<?php echo $link_back; ?>" class="button">
					<span><?php echo $button_back; ?></span>
				</a>
			</div>
			
			<?php if ($withdrawal_minimum_reached && isset($paypal) && !empty($paypal)) { ?>
			<div class="right">
				<a class="button" id="ms-submit-request">
					<span><?php echo $ms_button_submit_request; ?></span>
				</a>
			</div>
			<?php } ?>
		</div>
	</form>
	
	<?php echo $content_bottom; ?>
</div>

<?php echo $footer; ?>