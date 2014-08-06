<?php echo $header; ?>
<div id="content">
	<div class="breadcrumb">
	<?php foreach ($breadcrumbs as $breadcrumb) { ?>
	<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
	<?php } ?>
	</div>
	<div class="box">
	<div class="heading">
		<h1><img src="view/image/multiseller/ms-profile.png" alt="" /> <?php echo isset($seller['seller_id']) ? $ms_catalog_sellerinfo_heading : $ms_catalog_sellers_newseller; ?></h1>
		<div class="buttons"><a id="ms-submit-button" class="button"><?php echo $button_save; ?></a></div>
	</div>
	<div class="content">
	<form id="ms-sellerinfo">
		<input type="hidden" id="seller_id" name="seller[seller_id]" value="<?php echo $seller['seller_id']; ?>" />
		<div id="tabs" class="htabs">
			<a href="#tab-general"><?php echo $tab_general; ?></a>
			<a href="#tab-commission"><?php echo $ms_commissions_fees; ?></a>
		</div>
		<div id="tab-general">		
		<table class="ms-product form" id="ms-sellerinfo">
		<tr><td colspan="2"><h3><?php echo $ms_catalog_sellerinfo_customer_data; ?></h3></td></tr>
		<tr>
			<td><span class="required">*</span> <?php echo $ms_catalog_sellerinfo_customer; ?></td>
			<td>
			<?php if (!$seller['seller_id']) { ?>
			<select name="customer[customer_id]">
				<optgroup label="<?php echo $ms_catalog_sellerinfo_customer_new; ?>">
				<option value="0"><?php echo $ms_catalog_sellerinfo_customer_create_new; ?></option>
				</optgroup>
				<?php if (isset($customers)) { ?>
				<optgroup label="<?php echo $ms_catalog_sellerinfo_customer_existing; ?>">
				<?php foreach ($customers as $c) { ?>
				<option value="<?php echo $c['c.customer_id']; ?>"><?php echo $c['c.name']; ?></option>
				<?php } ?>
				</optgroup>
				<?php } ?>
			</select>
			<?php } else { ?>
				<a href="<?php echo $this->url->link('sale/customer/update', 'token=' . $this->session->data['token'] . '&customer_id=' . $seller['seller_id'], 'SSL'); ?>"><?php echo $seller['name']; ?></a>
			<?php } ?>
			</td>
		</tr>

		<tr>
			<td><?php echo $ms_catalog_sellerinfo_customer_firstname; ?></td>
			<td>
				<input type="text" name="customer[firstname]" value="" />
			</td>
		</tr>

		<tr>
			<td><?php echo $ms_catalog_sellerinfo_customer_lastname; ?></td>
			<td>
				<input type="text" name="customer[lastname]" value="" />
			</td>
		</tr>

		<tr>
			<td><?php echo $ms_catalog_sellerinfo_customer_email; ?></td>
			<td>
				<input type="text" name="customer[email]" value="" />
			</td>
		</tr>

		<tr>
			<td><?php echo $ms_catalog_sellerinfo_customer_password; ?></td>
			<td>
				<input type="password" name="customer[password]" value="" />
			</td>
		</tr>

		<tr>
			<td><?php echo $ms_catalog_sellerinfo_customer_password_confirm; ?></td>
			<td>
				<input type="password" name="customer[password_confirm]" value="" />
			</td>
		</tr>

		<tr><td colspan="2"><h3><?php echo $ms_catalog_sellerinfo_seller_data; ?></h3></td></tr>
		<tr>
			<?php if (!empty($seller['ms.nickname'])) { ?>
				<td><?php echo $ms_catalog_sellerinfo_nickname; ?></td>
				<td style="padding-top: 5px">
					<b><?php echo $seller['ms.nickname']; ?></b>
				</td>			
			<?php } else { ?>
				<td><span class="required">*</span> <?php echo $ms_catalog_sellerinfo_nickname; ?></td>
				<td>
					<input type="text" name="seller[nickname]" value="<?php echo $seller['ms.nickname']; ?>" />
				</td>
				<?php } ?>
		</tr>

		<tr>
			<td><?php echo $ms_catalog_sellerinfo_keyword; ?></td>
			<td>
				<input type="text" name="seller[keyword]" value="<?php echo $seller['keyword']; ?>" />
			</td>
		</tr>

		<tr>
			<td><?php echo $ms_catalog_sellerinfo_sellergroup; ?></td>
			<td><select name="seller[seller_group]">
				<?php foreach ($seller_groups as $group) { ?>
				<option value="<?php echo $group['seller_group_id']; ?>" <?php if ($seller['ms.seller_group'] == $group['seller_group_id']) { ?>selected="selected"<?php } ?>><?php echo $group['name']; ?></option>
				<?php } ?>
			</select>
			</td>
		</tr>
		
		<tr>
			<td>
				<span><?php echo $ms_catalog_sellerinfo_product_validation; ?></span>
				<span class="help"><?php echo $ms_catalog_sellerinfo_product_validation_note; ?></span>
			</td>
			<td>
					<select name="seller[product_validation]">
						<option value="1" <?php if($seller['ms.product_validation'] == 1) { ?> selected="selected" <?php } ?>><?php echo $ms_config_product_validation_none; ?></option>
					<option value="2" <?php if($seller['ms.product_validation'] == 2) { ?> selected="selected" <?php } ?>><?php echo $ms_config_product_validation_approval; ?></option>
				</select>
			</td>
		</tr>		
		
		<tr>
			<td><?php echo $ms_catalog_sellerinfo_description; ?></td>
			<td>
				<textarea name="seller[description]"><?php echo $seller['ms.description']; ?></textarea>
			</td>
		</tr>
		<tr>
			<td><?php echo $ms_catalog_sellerinfo_company; ?></td>
			<td>
				<input type="text" name="seller[company]" value="<?php echo $seller['ms.company']; ?>" />
			</td>
		</tr>
		<tr>
			<td><?php echo $ms_catalog_sellerinfo_country; ?></td>
			<td><select name="seller[country]">
					<?php if (1==1) { ?>
				<option value="" selected="selected"><?php echo $ms_catalog_sellerinfo_country_dont_display; ?></option>
				<?php } else { ?>
				<option value=""><?php echo $ms_catalog_sellerinfo_country_dont_display; ?></option>
				<?php } ?>
				
				<?php foreach ($countries as $country) { ?>
				<?php if ($seller['ms.country_id'] == $country['country_id']) { ?>
				<option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
				<?php } else { ?>
				<option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
				<?php } ?>
				<?php } ?>
			</select>
			</td>
		</tr>
		
		<tr>
			<td><?php echo $ms_catalog_sellerinfo_zone; ?></td>
			<td>
				<select name="seller[zone]">
				</select>
				<p class="ms-note"><?php echo $ms_catalog_sellerinfo_zone_note; ?></p>
			</td>
		</tr>
		
		<tr>
			<td><?php echo $ms_catalog_sellerinfo_paypal; ?></td>
			<td>
				<input type="text" name="seller[paypal]" value="<?php echo $seller['ms.paypal']; ?>" />
			</td>
		</tr>
				
		<tr>
			<td><?php echo $ms_catalog_sellerinfo_avatar; ?></td>
			<td>
				<div id="sellerinfo_avatar_files">
					<?php if (!empty($seller['avatar'])) { ?>
					<input type="hidden" name="seller[avatar_name]" value="<?php echo $seller['avatar']['name']; ?>" />
					<img src="<?php echo $seller['avatar']['thumb']; ?>" />
					<?php } ?>
				</div>
			</td>
		</tr>
		
		<?php $msSeller = new ReflectionClass('MsSeller'); ?>
		<tr>
			<td><?php echo $ms_status; ?></td>
			<td>
				<select name="seller[status]">
				<?php foreach ($msSeller->getConstants() as $cname => $cval) { ?>
					<?php if (strpos($cname, 'STATUS_') !== FALSE) { ?>
						<option value="<?php echo $cval; ?>" <?php if ($seller['ms.seller_status'] == $cval) { ?>selected="selected"<?php } ?>><?php echo $this->language->get('ms_seller_status_' . $cval); ?></option>
					<?php } ?>
				<?php } ?>
			</select>
			</td>
		</tr>

		<tr>
			<td>
				<span><?php echo $ms_catalog_sellerinfo_notify; ?></span>
			</td>
			<td>
			<input type="radio" name="seller[notify]" value="1" />
			<?php echo $text_yes; ?>
			<input type="radio" name="seller[notify]" value="0" checked="checked" />
			<?php echo $text_no; ?>
			</td>
		</tr>
		
		<tr>
			<td>
				<span><?php echo $ms_catalog_sellerinfo_message; ?></span>
				<span class="help"><?php echo $ms_catalog_sellerinfo_message_note; ?></span>
			</td>
			
			<td>
				<textarea name="seller[message]" disabled="disabled"></textarea>
			</td>
		</tr>
		</table>
		</div>
	
		<div id="tab-commission">
		<table class="form">
		<input type="hidden" name="seller[commission_id]" value="<?php echo $seller['commission_id']; ?>" />
		<?php if (isset($seller['actual_fees'])) { ?>
		<tr>
			<td><?php echo $ms_commission_actual; ?></td>
			<td><?php echo $seller['actual_fees']; ?></td>
		</tr>
		<?php } ?>
		
		<tr>
			<td><?php echo $this->language->get('ms_commission_' . MsCommission::RATE_SALE); ?></td>
			<td>
				<input type="hidden" name="seller[commission][<?php echo MsCommission::RATE_SALE; ?>][rate_id]" value="<?php echo isset($seller['commission_rates'][MsCommission::RATE_SALE]['rate_id']) ? $seller['commission_rates'][MsCommission::RATE_SALE]['rate_id'] : ''; ?>" />
				<input type="hidden" name="seller[commission][<?php echo MsCommission::RATE_SALE; ?>][rate_type]" value="<?php echo MsCommission::RATE_SALE; ?>" />
				<?php echo $this->currency->getSymbolLeft(); ?>
				<input type="text" name="seller[commission][<?php echo MsCommission::RATE_SALE; ?>][flat]" value="<?php echo isset($seller['commission_rates'][MsCommission::RATE_SALE]['flat']) ? $this->currency->format($seller['commission_rates'][MsCommission::RATE_SALE]['flat'], $this->config->get('config_currency'), '', FALSE) : '' ?>" size="3"/>
				<?php echo $this->currency->getSymbolRight(); ?>
				+<input type="text" name="seller[commission][<?php echo MsCommission::RATE_SALE; ?>][percent]" value="<?php echo isset($seller['commission_rates'][MsCommission::RATE_SALE]['percent']) ? $seller['commission_rates'][MsCommission::RATE_SALE]['percent'] : ''; ?>" size="3"/>%
			</td>
		</tr>

		<tr>
			<td><?php echo $this->language->get('ms_commission_' . MsCommission::RATE_LISTING); ?></td>
			<td>
				<input type="hidden" name="seller[commission][<?php echo MsCommission::RATE_LISTING; ?>][rate_id]" value="<?php echo isset($seller['commission_rates'][MsCommission::RATE_LISTING]['rate_id']) ? $seller['commission_rates'][MsCommission::RATE_LISTING]['rate_id'] : ''; ?>" />
				<input type="hidden" name="seller[commission][<?php echo MsCommission::RATE_LISTING; ?>][rate_type]" value="<?php echo MsCommission::RATE_LISTING; ?>" /> 
				<?php echo $this->currency->getSymbolLeft(); ?>
				<input type="text" name="seller[commission][<?php echo MsCommission::RATE_LISTING; ?>][flat]" value="<?php echo isset($seller['commission_rates'][MsCommission::RATE_LISTING]['flat']) ? $this->currency->format($seller['commission_rates'][MsCommission::RATE_LISTING]['flat'], $this->config->get('config_currency'), '', FALSE) : '' ?>" size="3"/>
				<?php echo $this->currency->getSymbolRight(); ?>
				+<input type="text" name="seller[commission][<?php echo MsCommission::RATE_LISTING; ?>][percent]" value="<?php echo isset($seller['commission_rates'][MsCommission::RATE_LISTING]['percent']) ? $seller['commission_rates'][MsCommission::RATE_LISTING]['percent'] : ''; ?>" size="3"/>%
				<select name="seller[commission][<?php echo MsCommission::RATE_LISTING; ?>][payment_method]">
					<optgroup label="<?php echo $ms_payment_method; ?>">
						<option value="0" <?php if(isset($seller['commission_rates'][MsCommission::RATE_LISTING]) && $seller['commission_rates'][MsCommission::RATE_LISTING]['payment_method'] == 0) { ?> selected="selected" <?php } ?>><?php echo $ms_payment_method_inherit; ?></option>
						<option value="<?php echo MsPayment::METHOD_BALANCE; ?>" <?php if(isset($seller['commission_rates'][MsCommission::RATE_LISTING]) && $seller['commission_rates'][MsCommission::RATE_LISTING]['payment_method'] == MsPayment::METHOD_BALANCE) { ?> selected="selected" <?php } ?>><?php echo $ms_payment_method_balance; ?></option>
						<option value="<?php echo MsPayment::METHOD_PAYPAL; ?>" <?php if(isset($seller['commission_rates'][MsCommission::RATE_LISTING]) && $seller['commission_rates'][MsCommission::RATE_LISTING]['payment_method'] == MsPayment::METHOD_PAYPAL) { ?> selected="selected" <?php } ?>><?php echo $ms_payment_method_paypal; ?></option>
					</optgroup>
				</select>
			</td>
		</tr>
		</table>
		</div>
		<!--  end commission tab -->
	</div>
	</form>
	</div>
	</div>
	
	<script type="text/javascript">
	$(function() {
		$('#tabs a').tabs();
	
		$('input[name^="customer"]').parents('tr').hide();
		$('[name="seller[notify]"], [name="seller[message]"]').parents('tr').show();
		$('select[name="customer[customer_id]"]').bind('change', function() {
			if (this.value == '0') {
				$('input[name^="customer"]').parents('tr').show();
				$('[name="seller[notify]"], [name="seller[message]"]').parents('tr').hide();
			} else {
				$('input[name^="customer"]').parents('tr').hide();
				$('[name="seller[notify]"], [name="seller[message]"]').parents('tr').show();
			}
		}).change();
	
		$('input[name="seller[notify]"]').change(function() {
			if ($(this).val() == 0) {
				$('textarea[name="seller[message]"]').val('').attr('disabled','disabled');
			} else {
				$('textarea[name="seller[message]"]').removeAttr('disabled');
			}
		});
	
		$("#ms-submit-button").click(function() {
			var button = $(this);
			var id = $(this).attr('id');
			$.ajax({
				type: "POST",
				dataType: "json",
				url: 'index.php?route=multiseller/seller/jxsavesellerinfo&token=<?php echo $token; ?>',
				data: $('#ms-sellerinfo').serialize(),
				beforeSend: function() {
					button.hide().before('<span class="wait">&nbsp;<img src="view/image/loading.gif" alt="" /></span>');
					$('p.error').remove();
					$('.warning').text('').hide();
				},
				complete: function(jqXHR, textStatus) {
					button.show().prev('span.wait').remove();
				},
				error: function(jqXHR, textStatus, errorThrown) {
					$('.warning').text(textStatus).show();
				},
				success: function(jsonData) {
					if (!jQuery.isEmptyObject(jsonData.errors)) {
						$('#error_'+id).text('');
						for (error in jsonData.errors) {
							$('[name="'+error+'"]').after('<p class="error">' + jsonData.errors[error] + '</p>');
						}
						window.scrollTo(0,0);
					} else {
						window.location = 'index.php?route=multiseller/seller&token=<?php echo $token; ?>';
					}
				 	}
			});
		});

		$("select[name='seller[country]']").bind('change', function() {
			$.ajax({
				url: 'index.php?route=sale/customer/country&token=<?php echo $token; ?>&country_id=' + this.value,
				dataType: 'json',
				beforeSend: function() {
					$("select[name='seller[country]']").after('<span class="wait">&nbsp;<img src="view/image/loading.gif" alt="" /></span>');
				},
				complete: function() {
					$('.wait').remove();
				},
				success: function(json) {
					html = '<option value=""><?php echo $ms_catalog_sellerinfo_zone_select; ?></option>';

					if (json['zone']) {
						for (i = 0; i < json['zone'].length; i++) {
							html += '<option value="' + json['zone'][i]['zone_id'] + '"';
							
							if (json['zone'][i]['zone_id'] == '<?php echo $seller['ms.zone_id']; ?>') {
								html += ' selected="selected"';
							}
			
							html += '>' + json['zone'][i]['name'] + '</option>';
						}
					} else {
						html += '<option value="0" selected="selected"><?php echo $ms_catalog_sellerinfo_zone_not_selected; ?></option>';
					}
					
					$("select[name='seller[zone]']").html(html);
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}).trigger('change');
	});
	</script>
<?php echo $footer; ?>