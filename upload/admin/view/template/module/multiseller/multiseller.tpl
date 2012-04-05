<?php echo $header; ?>

<div id="content">
	<div class="breadcrumb">
	  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
	  <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
	  <?php } ?>
	</div>
	<?php if ($error_warning) { ?>
	<div class="warning"><?php echo $error_warning; ?></div>
	<?php } ?>	
	<div class="box">
		<div class="heading">
	    	<h1><img src="view/image/module.png"/><?php echo $heading_title; ?></h1>
			<div class="buttons">
				<a class="button" id="saveSettings"><?php echo $button_save; ?></a>
				<a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a>
			</div>
	  	</div>
	  	<div class="content">
			<form id="settings" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
				<table class="form">
					<tr>
						<td>
							<span><?php echo $ms_config_seller_validation; ?></span>
							<span class="help"><?php echo $ms_config_seller_validation_note; ?></span>
						</td>
						<td>
			              	<select name="msconf_seller_validation">
			              	  <option value="1" <?php if($msconf_seller_validation == 1) { ?> selected="selected" <?php } ?>><?php echo $ms_config_seller_validation_none; ?></option>
			                  <option value="2" <?php if($msconf_seller_validation == 2) { ?> selected="selected" <?php } ?>><?php echo $ms_config_seller_validation_activation; ?></option>
			                  <option value="3" <?php if($msconf_seller_validation == 3) { ?> selected="selected" <?php } ?>><?php echo $ms_config_seller_validation_approval; ?></option>
			                </select>
						</td>
					</tr>
					
					<tr>
						<td>
							<span><?php echo $ms_config_product_validation; ?></span>
							<span class="help"><?php echo $ms_config_product_validation_note; ?></span>
						</td>
						<td>
			              	<select name="msconf_product_validation">
			              	  <option value="1" <?php if($msconf_product_validation == 1) { ?> selected="selected" <?php } ?>><?php echo $ms_config_product_validation_none; ?></option>
			                  <option value="2" <?php if($msconf_product_validation == 2) { ?> selected="selected" <?php } ?>><?php echo $ms_config_product_validation_approval; ?></option>
			                </select>
						</td>
					</tr>					
					
					<tr>
						<td>
							<span><?php echo $ms_config_seller_commission; ?></span>
							<span class="help"><?php echo $ms_config_seller_commission_note; ?></span>
						</td>
						<td>
							<input type="text" name="msconf_seller_commission" value="<?php echo $msconf_seller_commission; ?>" size="3"/>
						</td>
					</tr>
		            
		            <tr>
			              <td>
			              		<span><?php echo $ms_config_image_preview_size; ?></span>
								<span class="help"><?php echo $ms_config_image_preview_size_note; ?></span>
			              </td>
			              <td>
			              	<input type="text" name="msconf_image_preview_width" value="<?php echo $msconf_image_preview_width; ?>" size="3" />
			                x
			                <input type="text" name="msconf_image_preview_height" value="<?php echo $msconf_image_preview_height; ?>" size="3" />
			              </td>
		            </tr>
		            
		            <tr>
			              <td>
			              		<span><?php echo $ms_config_credit_order_statuses; ?></span>
								<span class="help"><?php echo $ms_config_credit_order_statuses_note; ?></span>
			              </td>
			              <td>
			              	<div class="scrollbox">
			                  <?php $class = 'odd'; ?>
			                  <?php foreach ($order_statuses as $status) { ?>
			                  <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
			                  <div class="<?php echo $class; ?>">
			                    <?php if (in_array($status['order_status_id'], explode(',',$msconf_credit_order_statuses))) { ?>
			                    <input type="checkbox" name="msconf_credit_order_statuses[]" value="<?php echo $status['order_status_id']; ?>" checked="checked" />
			                    <?php echo $status['name']; ?>
			                    <?php } else { ?>
			                    <input type="checkbox" name="msconf_credit_order_statuses[]" value="<?php echo $status['order_status_id']; ?>" />
			                    <?php echo $status['name']; ?>
			                    <?php } ?>
			                  </div>
			                  <?php } ?>
			                </div>
			              </td>
		            </tr>
		            
		            <tr>
			              <td>
			              		<span><?php echo $ms_config_debit_order_statuses; ?></span>
								<span class="help"><?php echo $ms_config_debit_order_statuses_note; ?></span>
			              </td>
			              <td>
			              	<div class="scrollbox">
			                  <?php $class = 'odd'; ?>
			                  <?php foreach ($order_statuses as $status) { ?>
			                  <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
			                  <div class="<?php echo $class; ?>">
			                    <?php if (in_array($status['order_status_id'], explode(',',$msconf_debit_order_statuses))) { ?>
			                    <input type="checkbox" name="msconf_debit_order_statuses[]" value="<?php echo $status['order_status_id']; ?>" checked="checked" />
			                    <?php echo $status['name']; ?>
			                    <?php } else { ?>
			                    <input type="checkbox" name="msconf_debit_order_statuses[]" value="<?php echo $status['order_status_id']; ?>" />
			                    <?php echo $status['name']; ?>
			                    <?php } ?>
			                  </div>
			                  <?php } ?>
			                </div>
			              </td>
		            </tr>
		            
					<tr>
						<td>
							<span><?php echo $ms_config_minimum_withdrawal; ?></span>
							<span class="help"><?php echo $ms_config_minimum_withdrawal_note; ?></span>
						</td>
						<td>
							<input type="text" name="msconf_minimum_withdrawal_amount" value="<?php echo $msconf_minimum_withdrawal_amount; ?>" size="3"/>
						</td>
					</tr>
					
		            <tr>
		            	<td>
							<span><?php echo $ms_config_allow_partial_withdrawal; ?></span>
							<span class="help"><?php echo $ms_config_allow_partial_withdrawal_note; ?></span>
						</td>
		            	<td>
			                <input type="radio" name="msconf_allow_partial_withdrawal" value="1" <?php if($msconf_allow_partial_withdrawal == 1) { ?> checked="checked" <?php } ?>  />
			                <?php echo $text_yes; ?>
			                <input type="radio" name="msconf_allow_partial_withdrawal" value="0" <?php if($msconf_allow_partial_withdrawal == 0) { ?> checked="checked" <?php } ?>  />
			                <?php echo $text_no; ?>
		              	</td>
		            </tr>
		            
					<tr>
						<td>
							<span><?php echo $ms_config_paypal_api_username; ?></span>
							<span class="help"><?php echo $ms_config_paypal_api_username_note; ?></span>
						</td>
						<td>
							<input type="text" name="msconf_paypal_api_username" value="<?php echo $msconf_paypal_api_username; ?>" size="30"/>
						</td>
					</tr>
					
					<tr>
						<td>
							<span><?php echo $ms_config_paypal_api_password; ?></span>
							<span class="help"><?php echo $ms_config_paypal_api_password_note; ?></span>
						</td>
						<td>
							<input type="text" name="msconf_paypal_api_password" value="<?php echo $msconf_paypal_api_password; ?>" size="30"/>
						</td>
					</tr>
					
					<tr>
						<td>
							<span><?php echo $ms_config_paypal_api_signature; ?></span>
							<span class="help"><?php echo $ms_config_paypal_api_signature_note; ?></span>
						</td>
						<td>
							<input type="text" name="msconf_paypal_api_signature" value="<?php echo $msconf_paypal_api_signature; ?>" size="30"/>
						</td>
					</tr>
					
		            <tr>
		            	<td>
							<span><?php echo $ms_config_allow_withdrawal_requests; ?></span>
							<span class="help"><?php echo $ms_config_allow_withdrawal_requests; ?></span>
						</td>
		            	<td>
			                <input type="radio" name="msconf_allow_withdrawal_requests" value="1" <?php if($msconf_allow_withdrawal_requests == 1) { ?> checked="checked" <?php } ?>  />
			                <?php echo $text_yes; ?>
			                <input type="radio" name="msconf_allow_withdrawal_requests" value="0" <?php if($msconf_allow_withdrawal_requests == 0) { ?> checked="checked" <?php } ?>  />
			                <?php echo $text_no; ?>
		              	</td>
		            </tr>				
		            
					<tr>
						<td>
							<span><?php echo $ms_config_comments_maxlen; ?></span>
							<span class="help"><?php echo $ms_config_comments_maxlen_note; ?></span>
						</td>
						<td>
							<input type="text" name="msconf_comments_maxlen" value="<?php echo $msconf_comments_maxlen; ?>" size="3"/>
						</td>
					</tr>		            
				</table>
			</form>
		</div>
	</div>
  </div>
</div>

<script>
$(function() {
	$("#saveSettings").click(function() {
	    $.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=module/multiseller/savesettings&token=<?php echo $token; ?>',
			data: $('#settings').serialize(),
			success: function(jsonData) {
				if (jsonData.errors) {
					for (error in jsonData.errors) {
					    if (!jsonData.errors.hasOwnProperty(error)) {
					        continue;
					    }
					    console.log(error + " -> " + jsonData.errors[error]);
					}				
				} else {
					console.log('success');
				}
	       	}
		});
	});
});
</script>  

<?php echo $footer; ?>	
</div>