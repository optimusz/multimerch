<?php echo $header; ?>

<div id="content">
	<div class="breadcrumb">
	  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
	  <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
	  <?php } ?>
	</div>
	<div class="error" id="error"></div>
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
		     	<div id="tabs" class="htabs">
		     		<a href="#tab-general"><?php echo $tab_general; ?></a>
		     		<a href="#tab-carousel"><?php echo $ms_config_carousel; ?></a>
		     	</div>
		     	
		     	<div id="tab-general">
				<table class="form">
					<tr>
						<td>
							<span><?php echo $ms_config_notification_email; ?></span>
							<span class="help"><?php echo $ms_config_notification_email_note; ?></span>
						</td>
						<td>
							<input size="20" type="text" name="msconf_notification_email" value="<?php echo $msconf_notification_email; ?>" />
						</td>
					</tr>				
				
					<tr>
						<td>
							<span><?php echo $ms_config_seller_validation; ?></span>
							<span class="help"><?php echo $ms_config_seller_validation_note; ?></span>
						</td>
						<td>
			              	<select name="msconf_seller_validation">
			              	  <option value="1" <?php if($msconf_seller_validation == 1) { ?> selected="selected" <?php } ?>><?php echo $ms_config_seller_validation_none; ?></option>
			                  <!--<option value="2" <?php if($msconf_seller_validation == 2) { ?> selected="selected" <?php } ?>><?php echo $ms_config_seller_validation_activation; ?></option>-->
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
							<input type="text" name="msconf_seller_commission_flat" value="<?php echo $this->currency->format($msconf_seller_commission_flat, $this->config->get('config_currency'), '', FALSE); ?>" size="3"/><?php echo $currency_code; ?>
							+<input type="text" name="msconf_seller_commission" value="<?php echo $msconf_seller_commission; ?>" size="3"/>%
						</td>
					</tr>
		            
					<tr>
						<td>
							<span><?php echo $ms_config_minimum_product_price; ?></span>
							<span class="help"><?php echo $ms_config_minimum_product_price_note; ?></span>
						</td>
						<td>
							<input type="text" name="msconf_minimum_product_price" value="<?php echo $msconf_minimum_product_price; ?>" size="3"/>
						</td>
					</tr>		            
		            
		   			<tr>
						<td>
							<span><?php echo $ms_config_allow_free_products; ?></span>
							<span class="help"><?php echo $ms_config_allow_free_products_note; ?></span>
						</td>
		            	<td>
			                <input type="radio" name="msconf_allow_free_products" value="1" <?php if($msconf_allow_free_products == 1) { ?> checked="checked" <?php } ?>  />
			                <?php echo $text_yes; ?>
			                <input type="radio" name="msconf_allow_free_products" value="0" <?php if($msconf_allow_free_products == 0) { ?> checked="checked" <?php } ?>  />
			                <?php echo $text_no; ?>
		              	</td>
					</tr>	         
		            
		            <tr>
			              <td>
			              		<span><?php echo $ms_config_allowed_image_types; ?></span>
								<span class="help"><?php echo $ms_config_allowed_image_types_note; ?></span>
			              </td>
			              <td>
			              	<input type="text" name="msconf_allowed_image_types" value="<?php echo $msconf_allowed_image_types; ?>" />
			              </td>
		            </tr>
		            
		            <tr>
			              <td>
			              		<span><?php echo $ms_config_allowed_download_types; ?></span>
								<span class="help"><?php echo $ms_config_allowed_download_types_note; ?></span>
			              </td>
			              <td>
			              	<input type="text" name="msconf_allowed_download_types" value="<?php echo $msconf_allowed_download_types; ?>" />
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
							<span><?php echo $ms_config_paypal_sandbox; ?></span>
							<span class="help"><?php echo $ms_config_paypal_sandbox_note; ?></span>
						</td>
		            	<td>
			                <input type="radio" name="msconf_paypal_sandbox" value="1" <?php if($msconf_paypal_sandbox == 1) { ?> checked="checked" <?php } ?>  />
			                <?php echo $text_yes; ?>
			                <input type="radio" name="msconf_paypal_sandbox" value="0" <?php if($msconf_paypal_sandbox == 0) { ?> checked="checked" <?php } ?>  />
			                <?php echo $text_no; ?>
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
				</div>
				
				<div id="tab-carousel">
		      <table id="module" class="list">
		        <thead>
		          <tr>
		            <td class="left"><?php echo $ms_config_layout; ?></td>
		            <td class="left"><?php echo $ms_config_position; ?></td>		          
		            <td class="left"><?php echo $ms_config_limit; ?></td>
		            <td class="left"><?php echo $ms_config_scroll; ?></td>
		            <td class="left"><?php echo $ms_config_image; ?></td>
		            <td class="left"><?php echo $ms_config_status; ?></td>
		            <td class="right"><?php echo $ms_config_sort_order; ?></td>
		            <td></td>
		          </tr>
		        </thead>
		        <?php $module_row = 0; ?>
		        
		        <?php if (isset($ms_carousel_module) && is_array($ms_carousel_module)) { ?>
		        <?php foreach ($ms_carousel_module as $module) { ?>
		        <tbody id="module-row<?php echo $module_row; ?>">
		          <tr>
		            <td class="left"><select name="ms_carousel_module[<?php echo $module_row; ?>][layout_id]">
		                <?php foreach ($layouts as $layout) { ?>
		                <?php if ($layout['layout_id'] == $module['layout_id']) { ?>
		                <option value="<?php echo $layout['layout_id']; ?>" selected="selected"><?php echo $layout['name']; ?></option>
		                <?php } else { ?>
		                <option value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
		                <?php } ?>
		                <?php } ?>
		              </select></td>
		            <td class="left"><select name="ms_carousel_module[<?php echo $module_row; ?>][position]">
		                <?php if ($module['position'] == 'content_top') { ?>
		                <option value="content_top" selected="selected"><?php echo $ms_config_top; ?></option>
		                <?php } else { ?>
		                <option value="content_top"><?php echo $ms_config_top; ?></option>
		                <?php } ?>
		                <?php if ($module['position'] == 'content_bottom') { ?>
		                <option value="content_bottom" selected="selected"><?php echo $ms_config_bottom; ?></option>
		                <?php } else { ?>
		                <option value="content_bottom"><?php echo $ms_config_bottom; ?></option>
		                <?php } ?>
		                <?php if ($module['position'] == 'column_left') { ?>
		                <option value="column_left" selected="selected"><?php echo $ms_config_column_left; ?></option>
		                <?php } else { ?>
		                <option value="column_left"><?php echo $ms_config_column_left; ?></option>
		                <?php } ?>
		                <?php if ($module['position'] == 'column_right') { ?>
		                <option value="column_right" selected="selected"><?php echo $ms_config_column_right; ?></option>
		                <?php } else { ?>
		                <option value="column_right"><?php echo $ms_config_column_right; ?></option>
		                <?php } ?>
		              </select></td>		          
		            <td class="left"><input type="text" name="ms_carousel_module[<?php echo $module_row; ?>][limit]" value="<?php echo $module['limit']; ?>" size="1" /></td>
		            <td class="left"><input type="text" name="ms_carousel_module[<?php echo $module_row; ?>][scroll]" value="<?php echo $module['scroll']; ?>" size="3" /></td>
		            <td class="left"><input type="text" name="ms_carousel_module[<?php echo $module_row; ?>][width]" value="<?php echo $module['width']; ?>" size="3" />
		              <input type="text" name="ms_carousel_module[<?php echo $module_row; ?>][height]" value="<?php echo $module['height']; ?>" size="3" />
		              <?php if (isset($error_image[$module_row])) { ?>
		              <span class="error"><?php echo $error_image[$module_row]; ?></span>
		              <?php } ?></td>
		            <td class="left"><select name="ms_carousel_module[<?php echo $module_row; ?>][status]">
		                <?php if ($module['status']) { ?>
		                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
		                <option value="0"><?php echo $text_disabled; ?></option>
		                <?php } else { ?>
		                <option value="1"><?php echo $text_enabled; ?></option>
		                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
		                <?php } ?>
		              </select></td>
		            <td class="right"><input type="text" name="ms_carousel_module[<?php echo $module_row; ?>][sort_order]" value="<?php echo $module['sort_order']; ?>" size="3" /></td>
		            <td class="left"><a onclick="$('#module-row<?php echo $module_row; ?>').remove();" class="button"><?php echo $button_remove; ?></a></td>
		          </tr>
		        </tbody>
		        <?php $module_row++; ?>
		        <?php } ?>
		        <?php } ?>		        
		        <tfoot>
		          <tr>
		            <td colspan="7"></td>
		            <td class="left"><a onclick="addModule();" class="button"><?php echo $button_add_module; ?></a></td>
		          </tr>
		        </tfoot>
		      </table>
		      </div>
			</form>
		</div>
	</div>
  </div>
</div>

<script>
$('#tabs a').tabs();

	
var module_row = <?php echo $module_row; ?>;

function addModule() {
	html  = '<tbody id="module-row' + module_row + '">';
	html += '  <tr>';
	html += '    <td class="left"><select name="ms_carousel_module[' + module_row + '][layout_id]">';
	<?php foreach ($layouts as $layout) { ?>
	html += '      <option value="<?php echo $layout['layout_id']; ?>"><?php echo addslashes($layout['name']); ?></option>';
	<?php } ?>
	html += '    </select></td>';	
	html += '    <td class="left"><select name="ms_carousel_module[' + module_row + '][position]">';
	html += '      <option value="content_top"><?php echo $ms_config_top; ?></option>';
	html += '      <option value="content_bottom"><?php echo $ms_config_bottom; ?></option>';
	html += '      <option value="column_left"><?php echo $ms_config_column_left; ?></option>';
	html += '      <option value="column_right"><?php echo $ms_config_column_right; ?></option>';
	html += '    </select></td>';		
	html += '    <td class="left"><input type="text" name="ms_carousel_module[' + module_row + '][limit]" value="5" size="1" /></td>';
	html += '    <td class="left"><input type="text" name="ms_carousel_module[' + module_row + '][scroll]" value="3" size="1" /></td>';
	html += '    <td class="left"><input type="text" name="ms_carousel_module[' + module_row + '][width]" value="80" size="3" /> <input type="text" name="ms_carousel_module[' + module_row + '][height]" value="80" size="3" /></td>';	
	html += '    <td class="left"><select name="ms_carousel_module[' + module_row + '][status]">';
    html += '      <option value="1" selected="selected"><?php echo $ms_enabled; ?></option>';
    html += '      <option value="0"><?php echo $ms_disabled; ?></option>';
    html += '    </select></td>';
	html += '    <td class="right"><input type="text" name="ms_carousel_module[' + module_row + '][sort_order]" value="" size="3" /></td>';
	html += '    <td class="left"><a onclick="$(\'#module-row' + module_row + '\').remove();" class="button"><?php echo $button_remove; ?></a></td>';
	html += '  </tr>';
	html += '</tbody>';
	
	$('#module tfoot').before(html);
	
	module_row++;
}

$(function() {
	$("#saveSettings").click(function() {
	    $.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=module/multiseller/savesettings&token=<?php echo $token; ?>',
			data: $('#settings').serialize(),
			success: function(jsonData) {
				if (jsonData.errors) {
					$("#error").html('');
					for (error in jsonData.errors) {
					    if (!jsonData.errors.hasOwnProperty(error)) {
					        continue;
					    }
					    $("#error").append('<p>'+jsonData.errors[error]+'</p>');
					}				
				} else {
					window.location.reload();
				}
	       	}
		});
	});
});
</script>  

<?php echo $footer; ?>	
</div>