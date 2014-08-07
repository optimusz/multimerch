<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>

<div id="content" class="ms-account-profile">
	<?php echo $content_top; ?>
	
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
	
	<h1><?php echo $ms_account_sellerinfo_heading; ?></h1>
	
	<?php if (isset($success) && ($success)) { ?>
		<div class="success"><?php echo $success; ?></div>
	<?php } ?>
	
	<?php if (isset($statustext) && ($statustext)) { ?>
		<div class="<?php echo $statusclass; ?>"><?php echo $statustext; ?></div>
	<?php } ?>

	<p class="warning main"></p>
	
	<form id="ms-sellerinfo" class="ms-form">
		<input type="hidden" name="action" id="ms_action" />
		
		<div class="content">
			<!-- todo status check update -->
			<?php if ($seller['ms.seller_status'] == MsSeller::STATUS_DISABLED || $seller['ms.seller_status'] == MsSeller::STATUS_DELETED) { ?>
			<div class="ms-overlay"></div>
			<?php } ?>
			<table class="ms-product">
				<tr>
					<?php if (!$this->config->get('msconf_change_seller_nickname') && !empty($seller['ms.nickname'])) { ?>
						<td><?php echo $ms_account_sellerinfo_nickname; ?></td>
						<td style="padding-top: 5px">
							<b><?php echo $seller['ms.nickname']; ?></b>
						</td>
					<?php } else { ?>
						<td><span class="required">*</span> <?php echo $ms_account_sellerinfo_nickname; ?></td>
						<td>
							<input type="text" name="seller[nickname]" value="<?php echo $seller['ms.nickname']; ?>" />
							<p class="ms-note"><?php echo $ms_account_sellerinfo_nickname_note; ?></p>
						</td>
					<?php } ?>
				</tr>
				
				<tr>
					<td><?php echo $ms_account_sellerinfo_description; ?></td>
					<td>
						<!-- todo strip tags if rte disabled -->
						<textarea name="seller[description]" id="seller_textarea" class="<?php echo $this->config->get('msconf_enable_rte') ? "ckeditor" : ''; ?>"><?php echo $this->config->get('msconf_enable_rte') ? htmlspecialchars_decode($seller['ms.description']) : strip_tags(htmlspecialchars_decode($seller['ms.description'])); ?></textarea>
						<p class="ms-note"><?php echo $ms_account_sellerinfo_description_note; ?></p>
					</td>
				</tr>
				
				<tr>
					<td><?php echo $ms_account_sellerinfo_company; ?></td>
					<td>
						<input type="text" name="seller[company]" value="<?php echo $seller['ms.company']; ?>" />
						<p class="ms-note"><?php echo $ms_account_sellerinfo_company_note; ?></p>
					</td>
				</tr>
				
				<tr>
					<td><?php echo $ms_account_sellerinfo_country; ?></td>
					<td>
						<select name="seller[country]">
							<option value="" selected="selected"><?php echo $ms_account_sellerinfo_country_dont_display; ?></option>
							<?php foreach ($countries as $country) { ?>
							<option value="<?php echo $country['country_id']; ?>" <?php if ($seller['ms.country_id'] == $country['country_id'] || $country_id == $country['country_id']) { ?>selected="selected"<?php } ?>><?php echo $country['name']; ?></option>
							<?php } ?>
						</select>
						<p class="ms-note"><?php echo $ms_account_sellerinfo_country_note; ?></p>
					</td>
				</tr>
				
				<tr>
					<td><?php echo $ms_account_sellerinfo_zone; ?></td>
					<td>
						<select name="seller[zone]">
						</select>
						<p class="ms-note"><?php echo $ms_account_sellerinfo_zone_note; ?></p>
					</td>
				</tr>
				
				<tr>
					<td><?php echo $ms_account_sellerinfo_paypal; ?></td>
					<td>
						<input type="text" name="seller[paypal]" value="<?php echo $seller['ms.paypal']; ?>" />
						<p class="ms-note"><?php echo $ms_account_sellerinfo_paypal_note; ?></p>
					</td>
				</tr>
				
				<tr>
					<td><?php echo $ms_account_sellerinfo_avatar; ?></td>
					<td>
						<!--<input type="file" name="ms-file-selleravatar" id="ms-file-selleravatar" />-->
						<div class="buttons">
						<?php if ($this->config->get('msconf_avatars_for_sellers') != 2) { ?>
							<a name="ms-file-selleravatar" id="ms-file-selleravatar" class="button"><span><?php echo $ms_button_select_image; ?></span></a>
						<?php } ?>
						<?php if ($this->config->get('msconf_avatars_for_sellers') == 1 || $this->config->get('msconf_avatars_for_sellers') == 2) { ?>
							<a name="ms-predefined-avatars" id="ms-predefined-avatars" class="button"><span><?php echo $ms_button_select_predefined_avatar; ?></span></a>

							<div style="display: none"><div id="ms-predefined-avatars-container">
								<?php if ($predefined_avatars) { ?>
									<?php foreach ($predefined_avatars as $avatar_category_name => $avatars) { ?>
									<div class="avatars-group">
										<h4><?php echo $avatar_category_name; ?></h4>
										<div class="avatars-list">
										<?php foreach ($avatars as $key => $avatar) { ?>
											<img src="<?php echo $avatar['image']; ?>" data-value="<?php echo $avatar['dir'] . $avatar['filename']; ?>">
										<?php } ?>
										</div>
									</div>
									<?php } ?>
								<?php } ?>
							</div></div>
						<?php } ?>
						</div>
						<p class="ms-note"><?php echo $ms_account_sellerinfo_avatar_note; ?></p>
						<p class="error" id="error_sellerinfo_avatar"></p>
						
						<div id="sellerinfo_avatar_files">
						<?php if (!empty($seller['avatar'])) { ?>
							<div class="ms-image">
								<input type="hidden" name="seller[avatar_name]" value="<?php echo $seller['avatar']['name']; ?>" />
								<img src="<?php echo $seller['avatar']['thumb']; ?>" />
								<span class="ms-remove"></span>
							</div>
						<?php } ?>
						</div>
					</td>
				</tr>
				
				<?php if ($ms_account_sellerinfo_terms_note) { ?>
				<tr>
					<td><?php echo $ms_account_sellerinfo_terms; ?></td>
					<td>
						<p style="margin-bottom: 0">
							<input type="checkbox" name="seller[terms]" value="1" />
							<?php echo $ms_account_sellerinfo_terms_note; ?>
						</p>
					</td>
				</tr>
				<?php } ?>
				
				<?php if (!isset($seller['seller_id']) &&$seller_validation != MsSeller::MS_SELLER_VALIDATION_NONE) { ?>
				<tr>
					<td><?php echo $ms_account_sellerinfo_reviewer_message; ?></td>
					<td>
						<textarea name="seller[reviewer_message]" id="message_textarea"></textarea>
						<p class="ms-note"><?php echo $ms_account_sellerinfo_reviewer_message_note; ?></p>
					</td>
				</tr>
				<?php } ?>
			</table>
		</div>
		</form>
		
		<?php if (isset($group_commissions) && $group_commissions[MsCommission::RATE_SIGNUP]['flat'] > 0) { ?>
			<p class="attention ms-commission">
				<?php echo sprintf($this->language->get('ms_account_sellerinfo_fee_flat'),$this->currency->format($group_commissions[MsCommission::RATE_SIGNUP]['flat'], $this->config->get('config_currency')), $this->config->get('config_name')); ?>
				<?php echo $ms_commission_payment_type; ?>
			</p>
			
			<?php if(isset($payment_form)) { ?><div class="ms-payment-form"><?php echo $payment_form; ?></div><?php } ?>
		<?php } ?>
		
		<div class="buttons">
			<div class="left">
				<a href="<?php echo $link_back; ?>" class="button">
					<span><?php echo $button_back; ?></span>
				</a>
			</div>
			
			<?php if ($seller['ms.seller_status'] != MsSeller::STATUS_DISABLED && $seller['ms.seller_status'] != MsSeller::STATUS_DELETED) { ?>
			<div class="right">
				<a class="button" id="ms-submit-button">
					<span><?php echo $ms_button_save; ?></span>
				</a>
			</div>
			<?php } ?>
		</div>
	<?php echo $content_bottom; ?>
</div>

<?php $timestamp = time(); ?>
<script>
	var msGlobals = {
		timestamp: '<?php echo $timestamp; ?>',
		token : '<?php echo md5($salt . $timestamp); ?>',
		session_id: '<?php echo session_id(); ?>',
		zone_id: '<?php echo $seller['ms.zone_id'] ?>',
		uploadError: '<?php echo htmlspecialchars($ms_error_file_upload_error, ENT_QUOTES, "UTF-8"); ?>',
		config_enable_rte: '<?php echo $this->config->get('msconf_enable_rte'); ?>',
		zoneSelectError: '<?php echo htmlspecialchars($ms_account_sellerinfo_zone_select, ENT_QUOTES, "UTF-8"); ?>',
		zoneNotSelectedError: '<?php echo htmlspecialchars($ms_account_sellerinfo_zone_not_selected, ENT_QUOTES, "UTF-8"); ?>'
	};
</script>

<?php if ($this->config->get('msconf_avatars_for_sellers') == 1 || $this->config->get('msconf_avatars_for_sellers') == 2) { ?>
<script type="text/javascript">
	$('#ms-predefined-avatars').colorbox({
		width:'600px', height:'70%', inline:true, href:'#ms-predefined-avatars-container'
	});

	$('.avatars-list img').click(function() {
		if ($('.ms-image img').length == 0) {
			$('#sellerinfo_avatar_files').html('<div class="ms-image">' +
				'<input type="hidden" value="'+$(this).data('value')+'" name="seller[avatar_name]" />' +
				'<img src="'+$(this).attr('src')+'" />' +
				'<span class="ms-remove"></span>' +
				'</div>');
		} else {
			$('.ms-image input[name="seller[avatar_name]"]').val($(this).data('value'));
			$('.ms-image img').attr('src', $(this).attr('src'));
		}
		$(window).colorbox.close();
	});
</script>
<?php } ?>
<?php echo $footer; ?>