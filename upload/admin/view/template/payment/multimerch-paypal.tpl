<form action="<?php echo $payment_data['action']; ?>" method="post">
	<input type="hidden" name="cmd" value="_xclick" />
	<input type="hidden" name="business" value="<?php echo $payment_data['business']; ?>" />
	
	<input type="hidden" name="item_name" value="<?php echo $payment_data['item_name']; ?>" />
	<input type="hidden" name="amount" value="<?php echo $payment_data['amount']; ?>" />
	
	<input type="hidden" name="currency_code" value="<?php echo $payment_data['currency_code']; ?>" />
	
	<input type="hidden" name="return" value="<?php echo $payment_data['return']; ?>" />
	<input type="hidden" name="cancel_return" value="<?php echo $payment_data['cancel_return']; ?>" />
	<input type="hidden" name="notify_url" value="<?php echo $payment_data['notify_url']; ?>" />
	
	<input type="hidden" name="custom" value="<?php echo $payment_data['custom']; ?>" />	
</form>