<div id="ms-sellercontact-dialog" title="<?php echo $ms_sellercontact_title; ?>">
	<?php if ($seller_thumb) { ?>
	<div class="ms-sellercontact-image">
	<a href="<?php echo $seller_href; ?>">
	<img src="<?php echo $seller_thumb; ?>" />
	</a>
	<h3><?php echo $ms_sellercontact_sendmessage; ?></h3>
	</div>
	<?php } ?>
	<div class="ms-form">
	<form class="dialog">
		<?php if ($this->config->get('msconf_enable_private_messaging') == 2) { ?>
			<label for="ms-sellercontact-name"><?php echo $ms_sellercontact_name; ?></label>
			<input type="text" name="ms-sellercontact-name" id="ms-sellercontact-name" value="<?php echo $customer_name; ?>"></input>
				
			<label for="ms-sellercontact-email"><?php echo $ms_sellercontact_email; ?></label>
			<input type="text" name="ms-sellercontact-email" id="ms-sellercontact-email" value="<?php echo $customer_email; ?>"></input>
		<?php } ?>

		<label for="ms-sellercontact-text"><?php echo $ms_sellercontact_text; ?></label>
		<textarea rows="3" name="ms-sellercontact-text" id="ms-sellercontact-text"></textarea>
		
		<label for="ms-sellercontact-captcha"><?php echo $ms_sellercontact_captcha; ?></label>
		<img src="index.php?route=product/product/captcha" id="ms-captcha" style="vertical-align:top; margin: 5px 0" />
		<input type="text" name="ms-sellercontact-captcha" id="ms-sellercontact-captcha" style="height: 25px; width:100px"></input>
			
		<input type="hidden" name="seller_id" value="<?php echo $seller_id; ?>" />
		<input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
	</form>
	</div>
</div>		
