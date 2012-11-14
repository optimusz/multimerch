<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>

<div id="content" class="ms-account-profile">
	<?php echo $content_top; ?>
	
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
	
	<h1><?php echo $ms_account_sellerinfo_heading; ?></h1>
	
	<?php if (isset($error_warning) && ($error_warning)) { ?>
		<div class="warning"><?php echo $error_warning; ?></div>
	<?php } ?>	
	
	<?php if (isset($success) && ($success)) { ?>
		<div class="success"><?php echo $success; ?></div>
	<?php } ?>
	
	<?php if (isset($statustext) && ($statustext)) { ?>
		<div class="<?php echo $statusclass; ?>"><?php echo $statustext; ?></div>
	<?php } ?>
	
	<form id="ms-sellerinfo" class="ms-form">
		<input type="hidden" name="action" id="ms_action" />
		
		<div class="content">
			<!-- todo status check update -->
			<?php if ($seller['ms.seller_status'] == MsSeller::STATUS_DISABLED || $seller['ms.seller_status'] == MsSeller::STATUS_DELETED) { ?>
			<div class="ms-overlay"></div>
			<?php } ?>
			<table class="ms-product">
				<tr>
					<?php if (!empty($seller['ms.nickname'])) { ?>
						<td><?php echo $ms_account_sellerinfo_nickname; ?></td>
						<td style="padding-top: 5px">
							<b><?php echo $seller['ms.nickname']; ?></b>
						</td>
					<?php } else { ?>
						<td><span class="required">*</span> <?php echo $ms_account_sellerinfo_nickname; ?></td>
						<td>
							<input type="text" name="sellerinfo_nickname" value="<?php echo $seller['ms.nickname']; ?>" />
							<p class="ms-note"><?php echo $ms_account_sellerinfo_nickname_note; ?></p>
							<p class="error" id="error_sellerinfo_nickname"></p>
						</td>
					<?php } ?>
				</tr>
				
				<tr>
					<td><?php echo $ms_account_sellerinfo_description; ?></td>
					<td>
						<textarea name="sellerinfo_description"><?php echo $seller['ms.description']; ?></textarea>
						<p class="ms-note"><?php echo $ms_account_sellerinfo_description_note; ?></p>
						<p class="error" id="error_sellerinfo_description"></p>
					</td>
				</tr>
				
				<tr>
					<td><?php echo $ms_account_sellerinfo_company; ?></td>
					<td>
						<input type="text" name="sellerinfo_company" value="<?php echo $seller['ms.company']; ?>" />
						<p class="ms-note"><?php echo $ms_account_sellerinfo_company_note; ?></p>
						<p class="error" id="error_sellerinfo_company"></p>
					</td>
				</tr>
				
				<tr>
					<td><?php echo $ms_account_sellerinfo_country; ?></td>
					<td>
						<select name="sellerinfo_country">
							<option value="" selected="selected"><?php echo $ms_account_sellerinfo_country_dont_display; ?></option>
							<?php foreach ($countries as $country) { ?>
							<option value="<?php echo $country['country_id']; ?>" <?php if ($seller['ms.country_id'] == $country['country_id']) { ?>selected="selected"<?php } ?>><?php echo $country['name']; ?></option>
							<?php } ?>
						</select>
						<p class="ms-note"><?php echo $ms_account_sellerinfo_country_note; ?></p>
						<p class="error" id="error_sellerinfo_country"></p>
					</td>
				</tr>
				
				<tr>
					<td><?php echo $ms_account_sellerinfo_paypal; ?></td>
					<td>
						<input type="text" name="sellerinfo_paypal" value="<?php echo $seller['ms.paypal']; ?>" />
						<p class="ms-note"><?php echo $ms_account_sellerinfo_paypal_note; ?></p>
						<p class="error" id="error_sellerinfo_paypal"></p>
					</td>
				</tr>
				
				<tr>
					<td><?php echo $ms_account_sellerinfo_avatar; ?></td>
					<td>
						<input type="file" name="ms-file-selleravatar" id="ms-file-selleravatar" />
						<p class="ms-note"><?php echo $ms_account_sellerinfo_avatar_note; ?></p>
						<p class="error" id="error_sellerinfo_avatar"></p>
						
						<div id="sellerinfo_avatar_files">
						<?php if (!empty($seller['avatar'])) { ?>
							<div class="ms-image">
								<input type="hidden" name="sellerinfo_avatar_name" value="<?php echo $seller['avatar']['name']; ?>" />
								<img src="<?php echo $seller['avatar']['thumb']; ?>" />
								<img class="ms-remove" src="catalog/view/theme/default/image/remove.png" />
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
							<input type="checkbox" name="accept_terms" value="1" />
							<?php echo $ms_account_sellerinfo_terms_note; ?>
						</p>
						<p class="error" id="error_sellerinfo_terms"></p>
					</td>
				</tr>
				<?php } ?>
				
				<?php if (!isset($seller['seller_id']) &&$seller_validation != MsSeller::MS_SELLER_VALIDATION_NONE) { ?>
				<tr>
					<td><?php echo $ms_account_sellerinfo_reviewer_message; ?></td>
					<td>
						<textarea name="sellerinfo_reviewer_message"></textarea>
						<p class="ms-note"><?php echo $ms_account_sellerinfo_reviewer_message_note; ?></p>
						<p class="error" id="error_sellerinfo_review_message"></p>
					</td>
				</tr>
				<?php } ?>
			</table>
		</div>
		
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
	</form>
	<?php echo $content_bottom; ?>
</div>

<?php $timestamp = time(); ?>
<script>
	var msGlobals = {
		timestamp: '<?php echo $timestamp; ?>',
		token : '<?php echo md5($salt . $timestamp); ?>',
		session_id: '<?php echo session_id(); ?>',
	};
</script>
<?php echo $footer; ?>