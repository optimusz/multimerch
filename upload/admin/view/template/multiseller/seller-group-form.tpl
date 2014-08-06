<?php echo $header; ?>
<div id="content">
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
	<div class="box">
		<div class="heading">
			<h1><img src="view/image/user-group.png" alt="" /> <?php echo $heading; ?></h1>
			<div class="buttons">
				<a id="ms-submit-button" class="button"><?php echo $button_save; ?></a>
				<a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a>
			</div>
		</div>
		<div class="content">
			<form method="post" enctype="multipart/form-data" id="form">
			<input type="hidden" name="seller_group[seller_group_id]" value="<?php echo $seller_group['seller_group_id']; ?>" />
				<div id="tabs" class="htabs">
					<a href="#tab-general"><?php echo $tab_general; ?></a>
					<a href="#tab-commission"><?php echo $ms_commissions_fees; ?></a>
				</div>
				<div id="tab-general">
				<table class="form">
					<tr>
						<td><span class="required">*</span> <?php echo $ms_name; ?></td>
						<td>
						<?php foreach ($languages as $language) { ?>
							<input type="text" name="seller_group[description][<?php echo $language['language_id']; ?>][name]" value="<?php echo $seller_group['description'][$language['language_id']]['name']; ?>" />
							<img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /><br />
							<p class="error" id="error_name_<?php echo $language['language_id']; ?>"></p>
						<?php } ?>
						</td>
					</tr>

					<tr>
						<td><?php echo $ms_description; ?></td>
						<td>					
					<?php foreach ($languages as $language) { ?>
							<textarea name="seller_group[description][<?php echo $language['language_id']; ?>][description]" cols="40" rows="5"><?php echo $seller_group['description'][$language['language_id']]['description']; ?></textarea>
							<img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" align="top" />
							<p class="error" id="error_description"></p>
					<?php } ?>
						</td>
					</tr>
					
					<tr>
						<td><?php if($seller_group['seller_group_id'] == $this->config->get('msconf_default_seller_group_id')) { ?><span class="required">*</span><?php } ?> <?php echo $this->language->get('ms_product_period'); ?></td>
						<td>
							<input type="text" name="seller_group[product_period]" value="<?php echo isset($seller_group['product_period']) ? $seller_group['product_period'] : '' ?>" size="5"/>
							<p class="error" id="error_product_period"></p>
						</td>
					</tr>
					
					<tr>
						<td><?php if($seller_group['seller_group_id'] == $this->config->get('msconf_default_seller_group_id')) { ?><span class="required">*</span><?php } ?> <?php echo $this->language->get('ms_product_quantity'); ?></td>
						<td>
							<input type="text" name="seller_group[product_quantity]" value="<?php echo isset($seller_group['product_quantity']) ? $seller_group['product_quantity'] : '' ?>" size="5"/>
							<p class="error" id="error_product_quantity"></p>
						</td>
					</tr>
				</table>
				</div>
				
				<div id="tab-commission">
				<input type="hidden" name="seller_group[commission_id]" value="<?php echo $seller_group['commission_id']; ?>" />
				<table class="form">
					<tr>
						<td><?php if($seller_group['seller_group_id'] == $this->config->get('msconf_default_seller_group_id')) { ?><span class="required">*</span><?php } ?> <?php echo $this->language->get('ms_commission_' . MsCommission::RATE_SALE); ?></td>
						<td>
							<input type="hidden" name="seller_group[commission_rates][<?php echo MsCommission::RATE_SALE; ?>][rate_id]" value="<?php echo $seller_group['commission_rates'][MsCommission::RATE_SALE]['rate_id']; ?>" />
							<input type="hidden" name="seller_group[commission_rates][<?php echo MsCommission::RATE_SALE; ?>][rate_type]" value="<?php echo MsCommission::RATE_SALE; ?>" /> 
							<?php echo $this->currency->getSymbolLeft(); ?>
							<input type="text" name="seller_group[commission_rates][<?php echo MsCommission::RATE_SALE; ?>][flat]" value="<?php echo isset($seller_group['commission_rates'][MsCommission::RATE_SALE]['flat']) ? $this->currency->format($seller_group['commission_rates'][MsCommission::RATE_SALE]['flat'], $this->config->get('config_currency'), '', FALSE) : '' ?>" size="3"/>
							<?php echo $this->currency->getSymbolRight(); ?>
							+<input type="text" name="seller_group[commission_rates][<?php echo MsCommission::RATE_SALE; ?>][percent]" value="<?php echo isset($seller_group['commission_rates'][MsCommission::RATE_SALE]['percent']) ? $seller_group['commission_rates'][MsCommission::RATE_SALE]['percent'] : ''; ?>" size="3"/>%
							<p class="error" id="error_commission_sale"></p>
						</td>
					</tr>
					
					<tr>
						<td><?php if($seller_group['seller_group_id'] == $this->config->get('msconf_default_seller_group_id')) { ?><span class="required">*</span><?php } ?> <?php echo $this->language->get('ms_commission_' . MsCommission::RATE_LISTING); ?></td>
						<td>
							<input type="hidden" name="seller_group[commission_rates][<?php echo MsCommission::RATE_LISTING; ?>][rate_id]" value="<?php echo $seller_group['commission_rates'][MsCommission::RATE_LISTING]['rate_id']; ?>" />
							<input type="hidden" name="seller_group[commission_rates][<?php echo MsCommission::RATE_LISTING; ?>][rate_type]" value="<?php echo MsCommission::RATE_LISTING; ?>" /> 
							<?php echo $this->currency->getSymbolLeft(); ?>
							<input type="text" name="seller_group[commission_rates][<?php echo MsCommission::RATE_LISTING; ?>][flat]" value="<?php echo isset($seller_group['commission_rates'][MsCommission::RATE_LISTING]['flat']) ? $this->currency->format($seller_group['commission_rates'][MsCommission::RATE_LISTING]['flat'], $this->config->get('config_currency'), '', FALSE) : '' ?>" size="3"/>
							<?php echo $this->currency->getSymbolRight(); ?>
							+<input type="text" name="seller_group[commission_rates][<?php echo MsCommission::RATE_LISTING; ?>][percent]" value="<?php echo isset($seller_group['commission_rates'][MsCommission::RATE_LISTING]['percent']) ? $seller_group['commission_rates'][MsCommission::RATE_LISTING]['percent'] : ''; ?>" size="3"/>%
							<select name="seller_group[commission_rates][<?php echo MsCommission::RATE_LISTING; ?>][payment_method]">
								<optgroup label="<?php echo $ms_payment_method; ?>">
									<?php if($seller_group['seller_group_id'] != $this->config->get('msconf_default_seller_group_id')) { ?>
									<option value="0" <?php if(isset($seller_group['commission_rates'][MsCommission::RATE_LISTING]) && $seller_group['commission_rates'][MsCommission::RATE_LISTING]['payment_method'] == 0) { ?> selected="selected" <?php } ?>><?php echo $ms_payment_method_inherit; ?></option>
									<?php } ?>
									<option value="<?php echo MsPayment::METHOD_BALANCE; ?>" <?php if(isset($seller_group['commission_rates'][MsCommission::RATE_LISTING]) && $seller_group['commission_rates'][MsCommission::RATE_LISTING]['payment_method'] == MsPayment::METHOD_BALANCE) { ?> selected="selected" <?php } ?>><?php echo $ms_payment_method_balance; ?></option>
									<option value="<?php echo MsPayment::METHOD_PAYPAL; ?>" <?php if(isset($seller_group['commission_rates'][MsCommission::RATE_LISTING]) && $seller_group['commission_rates'][MsCommission::RATE_LISTING]['payment_method'] == MsPayment::METHOD_PAYPAL) { ?> selected="selected" <?php } ?>><?php echo $ms_payment_method_paypal; ?></option>
								</optgroup>
							</select>
							<p class="error" id="error_commission_percent"></p>
						</td>
					</tr>
					
					
					<tr>
						<td><?php if($seller_group['seller_group_id'] == $this->config->get('msconf_default_seller_group_id')) { ?><span class="required">*</span><?php } ?> <?php echo $this->language->get('ms_commission_' . MsCommission::RATE_SIGNUP); ?></td>
						<td>
							<input type="hidden" name="seller_group[commission_rates][<?php echo MsCommission::RATE_SIGNUP; ?>][rate_id]" value="<?php echo $seller_group['commission_rates'][MsCommission::RATE_SIGNUP]['rate_id']; ?>" />
							<input type="hidden" name="seller_group[commission_rates][<?php echo MsCommission::RATE_SIGNUP; ?>][rate_type]" value="<?php echo MsCommission::RATE_SIGNUP; ?>" /> 
							<?php echo $this->currency->getSymbolLeft(); ?>
							<input type="text" name="seller_group[commission_rates][<?php echo MsCommission::RATE_SIGNUP; ?>][flat]" value="<?php echo isset($seller_group['commission_rates'][MsCommission::RATE_SIGNUP]['flat']) ? $this->currency->format($seller_group['commission_rates'][MsCommission::RATE_SIGNUP]['flat'], $this->config->get('config_currency'), '', FALSE) : '' ?>" size="3"/>
							<?php echo $this->currency->getSymbolRight(); ?>
							<select name="seller_group[commission_rates][<?php echo MsCommission::RATE_SIGNUP; ?>][payment_method]">
								<optgroup label="<?php echo $ms_payment_method; ?>">
									<?php if($seller_group['seller_group_id'] != $this->config->get('msconf_default_seller_group_id')) { ?>
									<option value="0" <?php if(isset($seller_group['commission_rates'][MsCommission::RATE_SIGNUP]) && $seller_group['commission_rates'][MsCommission::RATE_SIGNUP]['payment_method'] == 0) { ?> selected="selected" <?php } ?>><?php echo $ms_payment_method_inherit; ?></option>
									<?php } ?>
									<option value="<?php echo MsPayment::METHOD_BALANCE; ?>" <?php if(isset($seller_group['commission_rates'][MsCommission::RATE_SIGNUP]) && $seller_group['commission_rates'][MsCommission::RATE_SIGNUP]['payment_method'] == MsPayment::METHOD_BALANCE) { ?> selected="selected" <?php } ?>><?php echo $ms_payment_method_balance; ?></option>
									<option value="<?php echo MsPayment::METHOD_PAYPAL; ?>" <?php if(isset($seller_group['commission_rates'][MsCommission::RATE_SIGNUP]) && $seller_group['commission_rates'][MsCommission::RATE_SIGNUP]['payment_method'] == MsPayment::METHOD_PAYPAL) { ?> selected="selected" <?php } ?>><?php echo $ms_payment_method_paypal; ?></option>
								</optgroup>
							</select>
							<p class="error" id="error_commission_signup"></p>
						</td>
					</tr>
				</table>
				</div>
				<!--  end commission tab -->
			</form>
		</div>
	</div>
</div>

<script>
$('#tabs a').tabs();

$("#ms-submit-button").click(function() {
	var id = $(this).attr('id');
    $.ajax({
		type: "POST",
		dataType: "json",
		url: 'index.php?route=multiseller/seller-group/jxSave&token=<?php echo $token; ?>',
		data: $('#form').serialize(),
		success: function(jsonData) {
			console.log(jsonData);
			if (!jQuery.isEmptyObject(jsonData.errors)) {
				$('#error_'+id).text('');
				for (error in jsonData.errors) {
				    if (!jsonData.errors.hasOwnProperty(error)) {
				        continue;
				    }
				    
				    if ($('#error_'+error).length > 0) {
				    	$('#error_'+error).text(jsonData.errors[error]);
				    } else {
				    	$('#error_'+id).text(jsonData.errors[error]);
				   	}
				}
				window.scrollTo(0,0);
				$("#ms-submit-button").show();
			} else {
				window.location = 'index.php?route=multiseller/seller-group&token=<?php echo $token; ?>';
			}
       	}
	});
});
</script>
<?php echo $footer; ?> 