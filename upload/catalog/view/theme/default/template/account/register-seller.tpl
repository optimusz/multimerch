<?php echo $header; ?>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  
  <!-- Buyer account part -->
  
  <h1><?php echo $ms_account_register_seller; ?></h1>
  <p><?php echo $text_account_already; ?></p>
  <form id="ms-accountinfo">
    <h2><?php echo $text_your_details; ?></h2>
    <div class="content">
      <table class="form">
        <tr>
          <td><span class="required">*</span> <?php echo $entry_firstname; ?></td>
          <td><input type="text" name="firstname" value="<?php echo $firstname; ?>" />
            <?php if ($error_firstname) { ?>
            <span class="error"><?php echo $error_firstname; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_lastname; ?></td>
          <td><input type="text" name="lastname" value="<?php echo $lastname; ?>" />
            <?php if ($error_lastname) { ?>
            <span class="error"><?php echo $error_lastname; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_email; ?></td>
          <td><input type="text" name="email" value="<?php echo $email; ?>" />
            <?php if ($error_email) { ?>
            <span class="error"><?php echo $error_email; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_telephone; ?></td>
          <td><input type="text" name="telephone" value="<?php echo $telephone; ?>" />
            <?php if ($error_telephone) { ?>
            <span class="error"><?php echo $error_telephone; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><?php echo $entry_fax; ?></td>
          <td><input type="text" name="fax" value="<?php echo $fax; ?>" /></td>
        </tr>
      </table>
    </div>
    <h2><?php echo $text_your_address; ?></h2>
    <div class="content">
      <table class="form">
        <tr>
          <td><?php echo $entry_company; ?></td>
          <td><input type="text" name="company" value="<?php echo $company; ?>" /></td>
        </tr>        
        <tr style="display: <?php echo (count($customer_groups) > 1 ? 'table-row' : 'none'); ?>;">
          <td><?php echo $entry_customer_group; ?></td>
          <td><?php foreach ($customer_groups as $customer_group) { ?>
            <?php if ($customer_group['customer_group_id'] == $customer_group_id) { ?>
            <input type="radio" name="customer_group_id" value="<?php echo $customer_group['customer_group_id']; ?>" id="customer_group_id<?php echo $customer_group['customer_group_id']; ?>" checked="checked" />
            <label for="customer_group_id<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></label>
            <br />
            <?php } else { ?>
            <input type="radio" name="customer_group_id" value="<?php echo $customer_group['customer_group_id']; ?>" id="customer_group_id<?php echo $customer_group['customer_group_id']; ?>" />
            <label for="customer_group_id<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></label>
            <br />
            <?php } ?>
            <?php } ?></td>
        </tr>      
        <tr id="company-id-display">
          <td><span id="company-id-required" class="required">*</span> <?php echo $entry_company_id; ?></td>
          <td><input type="text" name="company_id" value="<?php echo $company_id; ?>" />
            <?php if ($error_company_id) { ?>
            <span class="error"><?php echo $error_company_id; ?></span>
            <?php } ?></td>
        </tr>
        <tr id="tax-id-display">
          <td><span id="tax-id-required" class="required">*</span> <?php echo $entry_tax_id; ?></td>
          <td><input type="text" name="tax_id" value="<?php echo $tax_id; ?>" />
            <?php if ($error_tax_id) { ?>
            <span class="error"><?php echo $error_tax_id; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_address_1; ?></td>
          <td><input type="text" name="address_1" value="<?php echo $address_1; ?>" />
            <?php if ($error_address_1) { ?>
            <span class="error"><?php echo $error_address_1; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><?php echo $entry_address_2; ?></td>
          <td><input type="text" name="address_2" value="<?php echo $address_2; ?>" /></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_city; ?></td>
          <td><input type="text" name="city" value="<?php echo $city; ?>" />
            <?php if ($error_city) { ?>
            <span class="error"><?php echo $error_city; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span id="postcode-required" class="required">*</span> <?php echo $entry_postcode; ?></td>
          <td><input type="text" name="postcode" value="<?php echo $postcode; ?>" />
            <?php if ($error_postcode) { ?>
            <span class="error"><?php echo $error_postcode; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_country; ?></td>
          <td><select name="country_id">
              <option value=""><?php echo $text_select; ?></option>
              <?php foreach ($countries as $country) { ?>
              <?php if ($country['country_id'] == $country_id) { ?>
              <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select>
            <?php if ($error_country) { ?>
            <span class="error"><?php echo $error_country; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_zone; ?></td>
          <td><select name="zone_id">
            </select>
            <?php if ($error_zone) { ?>
            <span class="error"><?php echo $error_zone; ?></span>
            <?php } ?></td>
        </tr>
      </table>
    </div>
    <h2><?php echo $text_your_password; ?></h2>
    <div class="content">
      <table class="form">
        <tr>
          <td><span class="required">*</span> <?php echo $entry_password; ?></td>
          <td><input type="password" name="password" value="<?php echo $password; ?>" />
            <?php if ($error_password) { ?>
            <span class="error"><?php echo $error_password; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_confirm; ?></td>
          <td><input type="password" name="confirm" value="<?php echo $confirm; ?>" />
            <?php if ($error_confirm) { ?>
            <span class="error"><?php echo $error_confirm; ?></span>
            <?php } ?></td>
        </tr>
      </table>
    </div>
	
	<!-- Seller account part -->
	<h2><?php echo $ms_account_sellerinfo_heading; ?></h2>

	<!--<p class="warning main"></p>-->
	
	<input type="hidden" name="action" id="ms_action" />
	
	<div class="content">
		<table class="ms-product">
			<tr>
				<td><span class="required">*</span> <?php echo $ms_account_sellerinfo_nickname; ?></td>
				<td>
					<input type="text" name="seller_nickname" value="<?php echo $seller_nickname; ?>" />
					<p class="ms-note"><?php echo $ms_account_sellerinfo_nickname_note; ?></p>
					<?php if ($error_seller_nickname) { ?>
						<span class="error"><?php echo $error_seller_nickname; ?></span>
					<?php } ?></td>
				</td>
			</tr>
			
			<tr>
				<td><?php echo $ms_account_sellerinfo_description; ?></td>
				<td>
					<!-- todo strip tags if rte disabled -->
					<textarea name="seller_description" id="seller_textarea" class="<?php echo $this->config->get('msconf_enable_rte') ? "ckeditor" : ''; ?>"><?php echo $this->config->get('msconf_enable_rte') ? htmlspecialchars_decode($seller_description) : strip_tags(htmlspecialchars_decode($seller_description)); ?></textarea>
					<p class="ms-note"><?php echo $ms_account_sellerinfo_description_note; ?></p>
					<?php if ($error_seller_description) { ?>
						<span class="error"><?php echo $error_seller_description; ?></span>
					<?php } ?></td>
				</td>
			</tr>
			
			<tr>
				<td><?php echo $ms_account_sellerinfo_company; ?></td>
				<td>
					<input type="text" name="seller_company" value="<?php echo $seller_company; ?>" />
					<p class="ms-note"><?php echo $ms_account_sellerinfo_company_note; ?></p>
					<?php if ($error_seller_company) { ?>
						<span class="error"><?php echo $error_seller_company; ?></span>
					<?php } ?></td>
				</td>
			</tr>
			
			<tr>
				<td><?php echo $ms_account_sellerinfo_country; ?></td>
				<td>
					<select name="seller_country_id">
						<option value="" selected="selected"><?php echo $ms_account_sellerinfo_country_dont_display; ?></option>
						<?php foreach ($countries as $country) { ?>
						<option value="<?php echo $country['country_id']; ?>" <?php if ($seller_country == $country['country_id']) { ?>selected="selected"<?php } ?>><?php echo $country['name']; ?></option>
						<?php } ?>
					</select>
					<p class="ms-note"><?php echo $ms_account_sellerinfo_country_note; ?></p>
				</td>
			</tr>
			
			<tr>
				<td><?php echo $ms_account_sellerinfo_zone; ?></td>
				<td>
					<select name="seller_zone">
					</select>
					<p class="ms-note"><?php echo $ms_account_sellerinfo_zone_note; ?></p>
				</td>
			</tr>
			
			<tr>
				<td><?php echo $ms_account_sellerinfo_paypal; ?></td>
				<td>
					<input type="text" name="seller_paypal" value="<?php echo $seller_paypal; ?>" />
					<p class="ms-note"><?php echo $ms_account_sellerinfo_paypal_note; ?></p>
					<?php if ($error_seller_paypal) { ?>
						<span class="error"><?php echo $error_seller_paypal; ?></span>
					<?php } ?></td>
				</td>
			</tr>
			
			<tr>
				<td><?php echo $ms_account_sellerinfo_avatar; ?></td>
				<td>
					<div class="buttons">
						<!--<input type="file" name="ms-file-selleravatar" id="ms-file-selleravatar" />-->
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
						<p class="ms-note"><?php echo $ms_account_sellerinfo_avatar_note; ?></p>
						<p class="error" id="error_sellerinfo_avatar"></p>
						<div id="sellerinfo_avatar_files">
						<?php if (!empty($seller_avatar)) { ?>
							<div class="ms-image">
								<input type="hidden" name="seller_avatar_name" value="<?php echo $seller_avatar['name']; ?>" />
								<img src="<?php echo $seller_avatar['thumb']; ?>" />
								<img class="ms-remove" src="catalog/view/theme/default/image/remove.png" />
							</div>
						<?php } ?>
						</div>
					</div>
				</td>
			</tr>
			
			<?php if ($ms_account_sellerinfo_terms_note) { ?>
			<tr>
				<td><?php echo $ms_account_sellerinfo_terms; ?></td>
				<td>
					<p style="margin-bottom: 0">
						<input type="checkbox" name="seller_terms" value="1" />
						<?php echo $ms_account_sellerinfo_terms_note; ?>
					</p>
				</td>
				<?php if ($error_seller_terms) { ?>
					<span class="error"><?php echo $error_seller_terms; ?></span>
				<?php } ?></td>
			</tr>
			<?php } ?>
			
			<?php if ($seller_validation != MsSeller::MS_SELLER_VALIDATION_NONE) { ?>
			<tr>
				<td><?php echo $ms_account_sellerinfo_reviewer_message; ?></td>
				<td>
					<textarea name="seller_reviewer_message" id="seller_reviewer_message"></textarea>
					<p class="ms-note"><?php echo $ms_account_sellerinfo_reviewer_message_note; ?></p>
				</td>
			</tr>
			<?php } ?>
		</table>
	</div>
		
	<?php if (isset($group_commissions) && $group_commissions[MsCommission::RATE_SIGNUP]['flat'] > 0) { ?>
		<p class="attention ms-commission">
			<?php echo sprintf($this->language->get('ms_account_sellerinfo_fee_flat'),$this->currency->format($group_commissions[MsCommission::RATE_SIGNUP]['flat'], $this->config->get('config_currency')), $this->config->get('config_name')); ?>
			<?php echo $ms_commission_payment_type; ?>
		</p>
	<?php } ?>
	
	<!-- Common part -->
	
    <!--<h2><?php //echo $text_newsletter; ?></h2>
    <div class="content">
      <table class="form">
        <tr>
          <td><?php //echo $entry_newsletter; ?></td>
          <td><?php //if ($newsletter) { ?>
            <input type="radio" name="newsletter" value="1" checked="checked" />
            <?php //echo $text_yes; ?>
            <input type="radio" name="newsletter" value="0" />
            <?php //echo $text_no; ?>
            <?php //} else { ?>
            <input type="radio" name="newsletter" value="1" />
            <?php //echo $text_yes; ?>
            <input type="radio" name="newsletter" value="0" checked="checked" />
            <?php //echo $text_no; ?>
            <?php //} ?></td>
        </tr>
      </table>
    </div>-->
    <?php //if ($text_agree) { ?>
    <!--<div class="buttons">
      <div class="right">--><?php //echo $text_agree; ?>
        <?php //if ($agree) { ?>
        <!--<input type="checkbox" name="agree" value="1" checked="checked" />-->
        <?php //} else { ?>
        <!--<input type="checkbox" name="agree" value="1" />-->
        <?php //} ?>
        <!--<a class="button" id="ms-submit-button" value="<?php //echo $button_continue; ?>"><span><?php //echo $ms_button_save; ?></span></a>
      </div>
    </div>-->
    <?php //} else { ?>
    <div class="buttons">
      <div class="right">
        <a class="button" id="ms-submit-button" value="<?php echo $button_continue; ?>"><span><?php echo $ms_button_save; ?></span></a>
      </div>
    </div>
    <?php //} ?>
  </form>
  
  <!-- Payment Form -->
  	<?php if (isset($group_commissions) && $group_commissions[MsCommission::RATE_SIGNUP]['flat'] > 0) { ?>
		<?php if(isset($payment_form)) { ?><div class="ms-payment-form"><?php echo $payment_form; ?></div><?php } ?>
	<?php } ?>
  
  <?php echo $content_bottom; ?></div>

<!-- Seller account part -->
<?php $timestamp = time(); ?>
<script type="text/javascript">
	var msGlobals = {
		timestamp: '<?php echo $timestamp; ?>',
		token : '<?php echo md5($timestamp); ?>',
		session_id: '<?php echo session_id(); ?>',
		uploadError: '<?php echo htmlspecialchars($ms_error_file_upload_error, ENT_QUOTES, "UTF-8"); ?>',
		config_enable_rte: '<?php echo $this->config->get('msconf_enable_rte'); ?>'
	};
</script>

<!-- Buyer account part -->
<script type="text/javascript"><!--
$('input[name=\'customer_group_id\']:checked').live('change', function() {
	var customer_group = [];
	
<?php foreach ($customer_groups as $customer_group) { ?>
	customer_group[<?php echo $customer_group['customer_group_id']; ?>] = [];
	customer_group[<?php echo $customer_group['customer_group_id']; ?>]['company_id_display'] = '<?php echo $customer_group['company_id_display']; ?>';
	customer_group[<?php echo $customer_group['customer_group_id']; ?>]['company_id_required'] = '<?php echo $customer_group['company_id_required']; ?>';
	customer_group[<?php echo $customer_group['customer_group_id']; ?>]['tax_id_display'] = '<?php echo $customer_group['tax_id_display']; ?>';
	customer_group[<?php echo $customer_group['customer_group_id']; ?>]['tax_id_required'] = '<?php echo $customer_group['tax_id_required']; ?>';
<?php } ?>	

	if (customer_group[this.value]) {
		if (customer_group[this.value]['company_id_display'] == '1') {
			$('#company-id-display').show();
		} else {
			$('#company-id-display').hide();
		}
		
		if (customer_group[this.value]['company_id_required'] == '1') {
			$('#company-id-required').show();
		} else {
			$('#company-id-required').hide();
		}
		
		if (customer_group[this.value]['tax_id_display'] == '1') {
			$('#tax-id-display').show();
		} else {
			$('#tax-id-display').hide();
		}
		
		if (customer_group[this.value]['tax_id_required'] == '1') {
			$('#tax-id-required').show();
		} else {
			$('#tax-id-required').hide();
		}	
	}
});

$('input[name=\'customer_group_id\']:checked').trigger('change');
//--></script> 
<script type="text/javascript"><!--
$('select[name=\'country_id\']').bind('change', function() {
	$.ajax({
		url: $('base').attr('href') + 'index.php?route=account/register-seller/country&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('select[name=\'country_id\']').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},
		complete: function() {
			$('.wait').remove();
		},			
		success: function(json) {
			if (json['postcode_required'] == '1') {
				$('#postcode-required').show();
			} else {
				$('#postcode-required').hide();
			}
			
			html = '<option value=""><?php echo $text_select; ?></option>';
			
			if (json['zone'] != '') {
				for (i = 0; i < json['zone'].length; i++) {
        			html += '<option value="' + json['zone'][i]['zone_id'] + '"';
	    			
					if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
	      				html += ' selected="selected"';
	    			}
	
	    			html += '>' + json['zone'][i]['name'] + '</option>';
				}
			} else {
				html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
			}
			
			$('select[name=\'zone_id\']').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('select[name=\'seller_country_id\']').bind('change', function() {
	$.ajax({
		url: $('base').attr('href') + 'index.php?route=account/register-seller/country&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('select[name=\'seller_country_id\']').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},
		complete: function() {
			$('.wait').remove();
		},
		success: function(json) {
			html = '<option value=""><?php echo $text_select; ?></option>';
			
			if (json['zone'] != '') {
				for (i = 0; i < json['zone'].length; i++) {
        			html += '<option value="' + json['zone'][i]['zone_id'] + '"';
	    			
					if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
	      				html += ' selected="selected"';
	    			}
	
	    			html += '>' + json['zone'][i]['name'] + '</option>';
				}
			} else {
				html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
			}
			
			$('select[name=\'seller_zone\']').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('select[name=\'country_id\']').trigger('change');
$('select[name=\'seller_country_id\']').trigger('change');
//--></script>
<script type="text/javascript"><!--
$(document).ready(function() {
	$('.colorbox').colorbox({
		width: 640,
		height: 480
	});
});
//--></script>
<?php if ($this->config->get('msconf_avatars_for_sellers') == 1 || $this->config->get('msconf_avatars_for_sellers') == 2) { ?>
	<script type="text/javascript">
		$('#ms-predefined-avatars').colorbox({
			width:'600px', height:'70%', inline:true, href:'#ms-predefined-avatars-container'
		});

		$('.avatars-list img').click(function() {
			if ($('.ms-image img').length == 0) {
				$('#sellerinfo_avatar_files').html('<div class="ms-image">' +
					'<input type="hidden" value="'+$(this).data('value')+'" name="seller_avatar_name" />' +
					'<img src="'+$(this).attr('src')+'" />' +
					'<span class="ms-remove"></span>' +
					'</div>');
			} else {
				$('.ms-image input[name="seller_avatar_name"]').val($(this).data('value'));
				$('.ms-image img').attr('src', $(this).attr('src'));
			}
			$(window).colorbox.close();
		});
	</script>
<?php } ?>
<?php echo $footer; ?>