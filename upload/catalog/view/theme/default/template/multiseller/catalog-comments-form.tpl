<h2 id="comment-title"><?php echo $ms_comments_post_comment; ?></h2>

<form id="pcForm">
<b><?php echo $ms_comments_name; ?></b><br />
<input type="text" name="mc_name" value="<?php echo $mc_name; ?>" <?php if ($this->customer->isLogged() && $msconf_comments_enforce_customer_data) { ?> disabled="disabled" <?php } ?> /><br /><br />

<b><?php echo $ms_comments_email; ?></b><br />
<input type="text" name="mc_email" value="<?php echo $mc_email; ?>" <?php if ($this->customer->isLogged() && $msconf_comments_enforce_customer_data) { ?> disabled="disabled" <?php } ?> /><br /> <br /> <br />

<b><?php echo $ms_comments_comment; ?></b>
<textarea id="mc_text" name="mc_text" cols="40" rows="8" style="width: 98%;" <?php if ($msconf_comments_maxlen > 0) echo "maxlength='$msconf_comments_maxlen'" ?>></textarea>
<span style="font-size: 11px;"><?php echo $ms_comments_note; ?></span><br /> <br />

<?php if (!$this->customer->isLogged() || ($this->customer->isLogged() && $this->config->get('msconf_comments_enable_customer_captcha'))) { ?>
<b><?php echo $ms_comments_captcha; ?></b><br />
<input type="text" name="mc_captcha" value="" />
<br />
<img src="index.php?route=product/product/captcha" alt="" id="mc_captcha" /><br />
<br />
<?php } ?>
</form>
<div class="buttons">
	<div class="right"><a id="mc-submit" class="button"><span><?php echo $button_continue; ?></span></a></div>
</div>

<script type="text/javascript">
	var ms_comments_product_id = <?php echo $product_id; ?>;
	var ms_comments_wait = '<?php echo $ms_comments_wait; ?>';
</script>