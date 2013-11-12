<div id="ms-seller-rate-dialog" title="<?php echo $ms_seller_rate_title; ?>">

	<div class="ms-form"><form class="dialog">
	<input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
	<?php foreach ($sellers as $seller) { ?>
	
		<h3><?php echo $seller['seller_nick']; ?></h3>
		<?php if ($seller['seller_thumb']) { ?>
			<div class="ms-seller-rate-image">
				<a href="<?php echo $seller['seller_href']; ?>">
					<img src="<?php echo $seller['seller_thumb']; ?>" />
				</a>
			</div>
		<?php } ?>
		<div class="ms-seller-rate-products">
			<?php foreach ($seller['products'] as $product) { ?>
				<div class="ms-seller-rate-product">
					<?php echo $product['name']; ?> x <?php echo $product['quantity']; ?>, <?php echo $this->currency->format($product['total'], $this->config->get('config_currency')); ?>
				</div>
			<?php } ?>
		</div>
		
		<h3><?php echo $ms_seller_ratings; ?><h3>
		<?php if (isset($seller['rating']['rating_communication'])) { ?>
			<div><b><?php echo $ms_seller_rating_communication; ?></b><img src="catalog/view/theme/default/image/stars-<?php echo $seller['rating']['rating_communication']; ?>.png" alt="<?php echo $ms_rating; ?>" /></div>
		<?php } else { ?>
			<label for="rating_communication[<?php echo $seller['seller_id']; ?>]"><?php echo $ms_seller_rating_communication; ?></label>
			<div class="ratings">
				<span><?php echo $entry_bad; ?></span>&nbsp;
				<input type="radio" name="rating_communication[<?php echo $seller['seller_id']; ?>]" value="1" />&nbsp;<input type="radio" name="rating_communication[<?php echo $seller['seller_id']; ?>]" value="2" />&nbsp;<input type="radio" name="rating_communication[<?php echo $seller['seller_id']; ?>]" value="3" />&nbsp;<input type="radio" name="rating_communication[<?php echo $seller['seller_id']; ?>]" value="4" />&nbsp;<input type="radio" name="rating_communication[<?php echo $seller['seller_id']; ?>]" value="5" />&nbsp;
				<span><?php echo $entry_good; ?></span><br />
			</div>
		<?php } ?>
		
		<?php if (isset($seller['rating']['rating_honesty'])) { ?>
			<div><b><?php echo $ms_seller_rating_honesty; ?></b><img src="catalog/view/theme/default/image/stars-<?php echo $seller['rating']['rating_honesty']; ?>.png" alt="<?php echo $ms_rating; ?>" /></div>
		<?php } else { ?>
			<label for="rating_honesty[<?php echo $seller['seller_id']; ?>]"><?php echo $ms_seller_rating_honesty; ?></label>
			<div class="ratings">
				<span><?php echo $entry_bad; ?></span>&nbsp;
				<input type="radio" name="rating_honesty[<?php echo $seller['seller_id']; ?>]" value="1" />&nbsp;<input type="radio" name="rating_honesty[<?php echo $seller['seller_id']; ?>]" value="2" />&nbsp;<input type="radio" name="rating_honesty[<?php echo $seller['seller_id']; ?>]" value="3" />&nbsp;<input type="radio" name="rating_honesty[<?php echo $seller['seller_id']; ?>]" value="4" />&nbsp;<input type="radio" name="rating_honesty[<?php echo $seller['seller_id']; ?>]" value="5" />&nbsp;
				<span><?php echo $entry_good; ?></span><br />
			</div>
		<?php } ?>
		
		<?php if (isset($seller['rating']['rating_overall'])) { ?>
			<div><b><?php echo $ms_seller_rating_overall; ?></b><img src="catalog/view/theme/default/image/stars-<?php echo $seller['rating']['rating_overall']; ?>.png" alt="<?php echo $ms_rating; ?>" /></div>
		<?php } else { ?>
			<label for="rating_overall[<?php echo $seller['seller_id']; ?>]"><?php echo $ms_seller_rating_overall; ?></label>
			<div class="ratings">
				<span><?php echo $entry_bad; ?></span>&nbsp;
				<input type="radio" name="rating_overall[<?php echo $seller['seller_id']; ?>]" value="1" />&nbsp;<input type="radio" name="rating_overall[<?php echo $seller['seller_id']; ?>]" value="2" />&nbsp;<input type="radio" name="rating_overall[<?php echo $seller['seller_id']; ?>]" value="3" />&nbsp;<input type="radio" name="rating_overall[<?php echo $seller['seller_id']; ?>]" value="4" />&nbsp;<input type="radio" name="rating_overall[<?php echo $seller['seller_id']; ?>]" value="5" />&nbsp;
				<span><?php echo $entry_good; ?></span><br />
			</div>
		<?php } ?>
		
		<label for="ms-seller-rate-text[<?php echo $seller['seller_id']; ?>]"><?php echo $ms_seller_rate_comment_text; ?></label>
		<textarea rows="3" name="ms-seller-rate-text[<?php echo $seller['seller_id']; ?>]" id="ms-seller-rate-text[<?php echo $seller['seller_id']; ?>]" <?php if (isset($seller['rating']['comment'])) { echo "readonly"; } ?> ><?php if (isset($seller['rating']['comment'])) { echo $seller['rating']['comment']; } ?></textarea>
		<input type="hidden" name="seller_id[<?php echo $seller['seller_id']; ?>]" value="<?php echo $seller['seller_id']; ?>" />
	<?php } ?>
	</form></div>
</div>		
