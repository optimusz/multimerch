<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>

<div id="content"><?php echo $content_top; ?>

  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  
  <h1><?php echo $ms_account_sellerinfo_heading; ?></h1>
  
  <?php if (isset($error_warning) && ($error_warning)) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  
  <form id="ms-sellerinfo">
    <div class="content">
      <table class="ms-product">
        <tr>
          <td><span class="required">*</span> <?php echo $ms_account_sellerinfo_nickname; ?></td>
          <td>
          	<input type="text" name="sellerinfo_nickname" value="<?php echo ''; ?>" />
          	<p class="ms-note"><?php echo $ms_account_sellerinfo_nickname_note; ?></p>
          </td>
        </tr>
        <tr>
          <td><?php echo $ms_account_sellerinfo_description; ?></td>
          <td>
          	<textarea name="sellerinfo_description"><?php echo ''; ?></textarea>
          	<p class="ms-note"><?php echo $ms_account_sellerinfo_description_note; ?></p>
          </td>
        </tr>
        <tr>
          <td><?php echo $ms_account_sellerinfo_company; ?></td>
          <td>
          	<input type="text" name="sellerinfo_company" value="<?php echo ''; ?>" />
          	<p class="ms-note"><?php echo $ms_account_sellerinfo_company_note; ?></p>
          </td>
        </tr>
        <tr>
          <td><?php echo $ms_account_sellerinfo_country; ?></td>
          <td><select name="sellerinfo_country">
          	  <?php if (1==1) { ?>
              <option value="" selected="selected"><?php echo $ms_account_sellerinfo_country_dont_display; ?></option>
              <?php } else { ?>
              <option value=""><?php echo $ms_account_sellerinfo_country_dont_display; ?></option>
              <?php } ?>
              
              <?php foreach ($countries as $country) { ?>
              <?php if (1 == 0) { ?>
              <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select>
          	<p class="ms-note"><?php echo $ms_account_sellerinfo_company_note; ?></p>            
        </tr>
        
        <tr>
          <td><?php echo $ms_account_sellerinfo_paypal; ?></td>
          <td>
          	<input type="text" name="sellerinfo_paypal" value="<?php echo ''; ?>" />
          	<p class="ms-note"><?php echo $ms_account_sellerinfo_paypal_note; ?></p>
          </td>
        </tr>
                
        <tr>
          <td><?php echo $ms_account_sellerinfo_avatar; ?></td>
          <td><input type="file" name="sellerinfo_avatar" /></td>
        </tr>
      </table>
    </div>
    <div class="buttons">
      <div class="left"><a href="<?php echo $back; ?>" class="button"><span><?php echo $button_back; ?></span></a></div>
      <div class="right"><a class="button" id="ms-submit-button"><span><?php echo $button_continue; ?></span></a></div>
    </div>
  </form>
  
  <?php echo $content_bottom; ?></div>
  
<script>
$(function() {
	$("#ms-submit-button").click(function() {
	//$("#ms-submit-button").after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
	//$("#ms-submit-button").hide();
	    $.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=account/ms-seller/jxsavesellerinfo',
			data: $(this).parents("form").serialize(),
			success: function(jsonData) {
				if (jsonData.errors) {
					for (error in jsonData.errors) {
					    if (!jsonData.errors.hasOwnProperty(error)) {
					        continue;
					    }
					    console.log(error + " -> " + jsonData.errors[error]);
					}				
				} else {
					alert('success');
				}
	       	}
		});
	});
});
</script>  
<?php echo $footer; ?>