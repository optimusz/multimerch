<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/customer.png" alt="" /> <?php echo $ms_catalog_sellerinfo_heading; ?></h1>
      <div class="buttons"><a id="ms-submit-button" class="button"><?php echo $button_save; ?></a></div>
    </div>
    <div class="content">
    <form id="ms-sellerinfo">
    	<input type="hidden" id="seller_id" name="seller_id" value="<?php echo $seller['seller_id']; ?>" />
      <table class="ms-product form" id="ms-sellerinfo">
        <tr>
			<?php if (!empty($seller['ms.nickname'])) { ?>
	          <td><?php echo $ms_catalog_sellerinfo_nickname; ?></td>
	          <td style="padding-top: 5px">
	          	<b><?php echo $seller['ms.nickname']; ?></b>
	          </td>			
			<?php } else { ?>
	          <td><span class="required">*</span> <?php echo $ms_catalog_sellerinfo_nickname; ?></td>
	          <td>
	          	<input type="text" name="sellerinfo_nickname" value="<?php echo $seller['ms.nickname']; ?>" />
	          	<p class="error" id="error_sellerinfo_nickname"></p>
	          </td>          		
          	<?php } ?>
        </tr>
        <tr>
          <td><?php echo $ms_catalog_sellerinfo_commission; ?></td>
          <td>
          	<input size="3" type="text" name="sellerinfo_commission_flat" value="<?php echo $this->currency->format($seller['ms.commission_flat'], $this->config->get('config_currency'), '', FALSE); ?>" /><?php echo $currency_code; ?>
			+<input size="3" type="text" name="sellerinfo_commission" value="<?php echo $seller['ms.commission']; ?>" size="3"/>%          	
          	<p class="error" id="error_sellerinfo_commission"></p>
          </td>
        </tr>
        
		<tr>
			<td>
				<span><?php echo $ms_catalog_sellerinfo_product_validation; ?></span>
				<span class="help"><?php echo $ms_catalog_sellerinfo_product_validation_note; ?></span>
			</td>
			<td>
              	<select name="sellerinfo_product_validation">
              	  <option value="1" <?php if($seller['ms.product_validation'] == 1) { ?> selected="selected" <?php } ?>><?php echo $ms_config_product_validation_none; ?></option>
                  <option value="2" <?php if($seller['ms.product_validation'] == 2) { ?> selected="selected" <?php } ?>><?php echo $ms_config_product_validation_approval; ?></option>
                </select>
			</td>
		</tr>        
        
        <tr>
          <td><?php echo $ms_catalog_sellerinfo_description; ?></td>
          <td>
          	<textarea name="sellerinfo_description"><?php echo $seller['ms.description']; ?></textarea>
          	<p class="error" id="error_sellerinfo_description"></p>
          </td>
        </tr>
        <tr>
          <td><?php echo $ms_catalog_sellerinfo_company; ?></td>
          <td>
          	<input type="text" name="sellerinfo_company" value="<?php echo $seller['ms.company']; ?>" />
          	<p class="error" id="error_sellerinfo_company"></p>
          </td>
        </tr>
        <tr>
          <td><?php echo $ms_catalog_sellerinfo_country; ?></td>
          <td><select name="sellerinfo_country">
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
          	<p class="error" id="error_sellerinfo_country"></p>
          </td>          	
        </tr>
        
        <tr>
          <td><?php echo $ms_catalog_sellerinfo_paypal; ?></td>
          <td>
          	<input type="text" name="sellerinfo_paypal" value="<?php echo $seller['ms.paypal']; ?>" />
          	<p class="error" id="error_sellerinfo_paypal"></p>
          </td>
        </tr>
                
        <tr>
          <td><?php echo $ms_catalog_sellerinfo_avatar; ?></td>
          <td>
          	<p class="error" id="error_sellerinfo_avatar"></p>
          	<div id="sellerinfo_avatar_files">
          		<?php if (!empty($seller['avatar'])) { ?>
          		<input type="hidden" name="sellerinfo_avatar_name" value="<?php echo $seller['avatar']['name']; ?>" />
          		<img src="<?php echo $seller['avatar']['thumb']; ?>" />
          		<?php } ?>
          	</div>
          </td>
        </tr>
        
        <tr>
          <td><?php echo $ms_status; ?></td>
          <td style="padding-top: 5px">
          	<b><?php echo $seller['ms.seller_status']; ?></b>
          </td>
        </tr>        
        
        <tr>
          <td><?php echo $ms_action; ?></td>
          <td>
          	<select name="sellerinfo_action">
			  <option value="0" selected="selected"></option>
			  <?php foreach ($actions as $action) { ?>          	
              <option value="<?php echo $action['value']; ?>"><?php echo $action['text']; ?></option>
              <?php } ?>
            </select>
          	<p class="error" id="error_sellerinfo_country"></p>
          </td>
        </tr>

        <tr>
          <td>
          	<span><?php echo $ms_catalog_sellerinfo_notify; ?></span>
          </td>
          <td>
	        <input type="radio" name="sellerinfo_notify" value="1" />
	        <?php echo $text_yes; ?>
	        <input type="radio" name="sellerinfo_notify" value="0" checked="checked" />
	        <?php echo $text_no; ?>
          </td>
        </tr>
        
        <tr>
          <td>
          	<span><?php echo $ms_catalog_sellerinfo_message; ?></span>
          	<span class="help"><?php echo $ms_catalog_sellerinfo_message_note; ?></span>
          </td>
          <td>
          	<textarea name="sellerinfo_message" disabled="disabled"></textarea>
          	<p class="error" id="error_sellerinfo_message"></p>
          </td>
        </tr>
      </table>
    </div>
  </form>
  </div>
  </div>
<script>
$(function() {
	$('input[name="sellerinfo_notify"]').change(function() {
		if ($(this).val() == 0) {
			$('textarea[name="sellerinfo_message"]').val('').attr('disabled','disabled');
		} else {
			$('textarea[name="sellerinfo_message"]').removeAttr('disabled');
		}
	});

	$("#ms-submit-button").click(function() {
		$("#ms-submit-button").after('<span class="wait">&nbsp;<img src="view/image/loading.gif" alt="" /></span>').hide();
		var id = $(this).attr('id');
	    $.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=multiseller/seller/jxsavesellerinfo&token=<?php echo $token; ?>',
			data: $('#ms-sellerinfo').serialize(),
			success: function(jsonData) {
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
					    console.log(error + " -> " + jsonData.errors[error]);
					}
					window.scrollTo(0,0);
					$("#ms-submit-button").show();
				} else {
					window.location = 'index.php?route=multiseller/seller&token=<?php echo $token; ?>';
				}
	       	}
		});
	});
});
</script>  
<?php echo $footer; ?>