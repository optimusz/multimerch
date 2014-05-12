<div class="option">
	<input type="hidden" name="product_option[<?php echo $option_index; ?>][option_id]" value="<?php echo $option['option_id']; ?>"></td>
	<input type="hidden" name="product_option[<?php echo $option_index; ?>][required]" value="1"></td>
	<div class="o-heading"><span class="option_required <?php echo isset($option['required']) && !$option['required'] ? "bw" : ""; ?>" title="<?php echo $ms_options_required; ?>"></span><?php echo $option['name']; ?><a class="ms-button-delete option_delete" title="<?php echo $ms_delete; ?>"></a></div>
	
	<?php if (!empty($values)) { ?>
	<div class="o-content">
		<div class="option_values mmCtr">
			<div class="option_value ffSample">
				<span class="option_name"></span>
				<input type="hidden" name="product_option[<?php echo $option_index; ?>][product_option_value][0][option_value_id]" value=""></td>
				<input type="hidden" name="product_option[<?php echo $option_index; ?>][product_option_value][0][price_prefix]" value="+"></td>
				<span class="option_price_prefix plus" title="<?php echo $ms_options_price_prefix; ?>"></span>
				<span class="option_price">
					<?php echo $this->currency->getSymbolLeft(); ?>
					<input type="text" placeholder="<?php echo $ms_options_price; ?>" name="product_option[<?php echo $option_index; ?>][product_option_value][0][price]" value="" size="5"></td>
					<?php echo $this->currency->getSymbolRight(); ?>
				</span>
				<input class="option_quantity" type="text" placeholder="<?php echo $ms_options_quantity; ?>" name="product_option[<?php echo $option_index; ?>][product_option_value][0][quantity]" value="" size="5"></td>
				<a class="ms-button-delete option_value_delete" title="<?php echo $ms_delete; ?>"></a>
			</div>
			
			<?php if (!empty($product_option_values)) { ?>
			<?php $i = 1; ?>
			<?php foreach ($product_option_values as $value) {?>
				<div class="option_value">
					<span class="option_name"><?php echo $value['name']; ?></span>`
					<input type="hidden" name="product_option[<?php echo $option_index; ?>][product_option_value][<?php echo $i; ?>][option_value_id]" value="<?php echo $value['option_value_id']; ?>"></td>
					<input type="hidden" name="product_option[<?php echo $option_index; ?>][product_option_value][<?php echo $i; ?>][price_prefix]" value="<?php echo $value['price_prefix']; ?>"></td>
					<span class="option_price_prefix <?php echo ($value['price_prefix'] == '+' ? "plus" : "minus"); ?>" title="<?php echo $ms_options_price_prefix; ?>"></span>
					<span class="option_price">
						<?php echo $this->currency->getSymbolLeft(); ?>
						<input type="text" placeholder="<?php echo $ms_options_price; ?>" name="product_option[<?php echo $option_index; ?>][product_option_value][<?php echo $i; ?>][price]" value="<?php echo $this->MsLoader->MsHelper->trueCurrencyFormat($value['price']); ?>" size="5"></td>
						<?php echo $this->currency->getSymbolRight(); ?>
					</span>
					<input class="option_quantity" type="text" placeholder="<?php echo $ms_options_quantity; ?>" name="product_option[<?php echo $option_index; ?>][product_option_value][<?php echo $i; ?>][quantity]" value="<?php echo $value['quantity']; ?>" size="5"></td>
					<a class="ms-button-delete option_value_delete" title="<?php echo $ms_delete; ?>"></a>
				</div>			
			<?php $i++; ?>
			<?php } ?>
			<?php } ?>
		</div>
	
		<select class="select_option_value" id="select_option_value<?php echo $option['option_id']; ?>">
			<option value="0" disabled="disabled" selected="selected"><?php echo $ms_options_add_value; ?></option>
			<?php foreach($values as $value) { ?>
			<option value="<?php echo $value['option_value_id']?>"><?php echo $value['name']; ?></option>
			<?php } ?>
		</select>
	</div>
	<?php } ?>
</div>

<script type="text/javascript">
	$('input[name$="[option_value_id]"]').each(function(index) {
		$(this).closest('.option').find('.select_option_value option[value="'+ $(this).val() + '"]').attr('disabled', true );
	});
</script>