<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>

<div id="content"><?php echo $content_top; ?>

  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  
  <h1><?php echo $heading; ?></h1>
  
  <?php if (isset($error_warning) && ($error_warning)) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  
  <form id="ms-new-product" method="post" enctype="multipart/form-data">
  	<input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>" />
  	<input type="hidden" name="action" id="ms_action" />
    <div class="content">
	<div class="htabs" id="htabs">
		<?php foreach ($languages as $language) { ?>
		<a class="lang" href="#language<?php echo $language['language_id']; ?>"><img src="image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a>
		<?php } ?>
	</div>
	<?php
	reset($languages); $first = key($languages);
	foreach ($languages as $k => $language) {
	$langId = $language['language_id'];
	?>    
	<div class="ms-language-div" id="language<?php echo $langId; ?>">
      <table class="ms-product">
        <tr><td colspan="2"><h3><?php echo $ms_account_product_name_description; ?></h3></td></tr>      
        <tr>
          <td><span class="required"><?php if ($k == $first) { echo '*'; } ?></span> <?php echo $ms_account_product_name; ?></td>
          <td>
          	<input type="text" name="languages[<?php echo $langId; ?>][product_name]" value="<?php echo $product['languages'][$langId]['name']; ?>" />
          	<p class="ms-note"><?php echo $ms_account_product_name_note; ?></p>
          	<p class="error" id="error_product_name_<?php echo $langId; ?>"></p>
          </td>
        </tr>
        <tr>
          <td><span class="required"><?php if ($k == $first) { echo '*'; } ?></span> <?php echo $ms_account_product_description; ?></td>
          <td>
          	<textarea name="languages[<?php echo $langId; ?>][product_description]"><?php echo $product['languages'][$langId]['description']; ?></textarea>
          	<p class="ms-note"><?php echo $ms_account_product_description_note; ?></p>
          	<p class="error" id="error_product_description_<?php echo $langId; ?>"></p>
          </td>
        </tr>
        <tr>
          <td><?php echo $ms_account_product_tags; ?></td>
          <td>
          	<input type="text" name="languages[<?php echo $langId; ?>][product_tags]" value="<?php echo $product['languages'][$langId]['tags']; ?>" />
          	<p class="ms-note"><?php echo $ms_account_product_tags_note; ?></p>
          	<p class="error" id="error_product_tags_<?php echo $langId; ?>"></p>
          </td>
        </tr>        
      </table>
    </div>
    <?php } ?>
      <table class="ms-product">  
        <tr><td colspan="2"><h3><?php echo $ms_account_product_price_attributes; ?></h3></td></tr>
        <tr>
          <td><span class="required">*</span> <?php echo $ms_account_product_price; ?></td>
          <td>
          	<input type="text" name="product_price" value="<?php echo $product['price']; ?>" />
          	<p class="ms-note"><?php echo $ms_account_product_price_note; ?></p>
          	<p class="error" id="error_product_price"></p>
          </td>
        </tr>              
        <tr>
          <td><span class="required">*</span> <?php echo $ms_account_product_category; ?></td>
          <td>
			<select name="product_category">
            	<option value=""><?php echo ''; ?></option>
                  <?php foreach ($categories as $category) { ?>
                    <?php if (in_array($category['category_id'], array($product['category_id']))) { ?>
                    <option value="<?php echo $category['category_id']; ?>" selected="selected"><?php echo $category['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></option>
                    <?php } ?>
                  <?php } ?>
            </select>
          	<p class="ms-note"><?php echo $ms_account_product_category_note; ?></p>
          	<p class="error" id="error_product_category"></p>
          </td>
        </tr>
        
        <tr><td colspan="2"><h3><?php echo $ms_account_product_files; ?></h3></td></tr>
        <tr>
          <td><span class="required">*</span> <?php echo $ms_account_product_thumbnail; ?></td>
          <td>
          	<input type="file" name="product_thumbnail" id="product_thumbnail" />
          	<p class="ms-note"><?php echo $ms_account_product_thumbnail_note; ?></p>
          	<p class="error" id="error_product_thumbnail"></p>
          	<div id="product_thumbnail_files">
          		<?php if (!empty($product['thumbnail'])) { ?>
          		<div class="ms-image">
	          		<input type="hidden" name="product_thumbnail_name" value="<?php echo $product['thumbnail']['name']; ?>" />
	          		<img src="<?php echo $product['thumbnail']['thumb']; ?>" />
	          		<img class="ms-remove" src="catalog/view/theme/default/image/remove.png" />
          		</div>
          		<?php } ?>
          	</div>
          </td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $ms_account_product_image; ?></td>
          <td>
          	<input type="file" name="product_image" id="product_image" />
          	<p class="ms-note"><?php echo $ms_account_product_image_note; ?></p>
          	<p class="error" id="error_product_image"></p>
          	<div id="product_image_files">
          	<?php if (isset($product['images'])) { ?>
	          	<?php foreach ($product['images'] as $image) { ?>
          		<div class="ms-image">
          			<input type="hidden" name="product_images[]" value="<?php echo $image['name']; ?>" />
          			<img src="<?php echo $image['thumb']; ?>" />
          			<img class="ms-remove" src="catalog/view/theme/default/image/remove.png" />
          		</div>
	          	<?php } ?>
          	<?php } ?>
          	</div>
          </td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $ms_account_product_download; ?></td>
          <td>
          	<input type="file" name="product_download" id="product_download" />
          	<p class="ms-note"><?php echo $ms_account_product_download_note; ?></p>
          	<p class="error" id="error_product_download"></p>
          	<div id="product_download_files">
          	<?php if (isset($product['downloads'])) { ?>
	          	<?php foreach ($product['downloads'] as $download) { ?>
	          		<input type="hidden" name="product_downloads[]" value="<?php echo $download['src']; ?>" />
	          		<p><a href="<?php echo $download['href']; ?>"><?php echo $download['name']; ?></a> <span style="cursor: pointer">[ <?php echo $ms_delete; ?> ]</span></p>
	          	<?php } ?>
          	<?php } ?>
          	</div>
          </td>
        </tr>
        <?php if ($seller['product_validation'] == MsProduct::MS_PRODUCT_VALIDATION_APPROVAL) { ?>
        <tr><td colspan="2"><h3>Message to the reviewer</h3></td></tr>        
        <tr>
          <td><?php echo $ms_account_product_message; ?></td>
          <td>
          	<textarea name="product_message"></textarea>
          	<p class="ms-note"><?php echo $ms_account_product_message_note; ?></p>
          	<p class="error" id="error_product_message"></p>
          </td>          
        </tr>
        <?php } ?>
      </table>
    </div>
    <div class="buttons">
      <div class="left"><a href="<?php echo $back; ?>" class="button"><span><?php echo $ms_button_cancel; ?></span></a></div>
      <div class="right"><a class="button" id="ms-submit-button"><span><?php echo $ms_button_submit; ?></span></a></div>
	  <div class="right" style="margin-right: 20px;"><a class="button" id="ms-savedraft-button"><span><?php echo $ms_button_save_draft; ?></span></a></div>      
    </div>
  </form>
  
  <?php echo $content_bottom; ?></div>

<script>
$(function() {
	$('#htabs a.lang').tabs();

	$("#product_image_files, #product_thumbnail_files").delegate(".ms-remove", "click", function() {
		$(this).parent().remove();
	});

	$("#product_download_files").delegate("span", "click", function() {
		$(this).parent("p").prev("input:hidden").remove();
		$(this).parent("p").remove();
	});

	$('#ms-new-product input[type="file"]').live('change', function() {
		var element = $(this);
		var id = $(this).attr('id');
		$('#ms_action').val(id);
		$('#error_'+id).text('');
		$("#ms-new-product").ajaxForm({
			url:  "index.php?route=account/ms-seller/jxuploadfile",
			dataType: 'json', 
		    beforeSend: function() {
				$('#'+id+'_files').append('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
				
		    },
			success: function(jsonData) {
				$(element).replaceWith('<input type="file" name="'+$(element).attr('name')+'" id="'+$(element).attr('id')+'"/>');
				$('#'+id+'_files span.wait').remove();
			
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
					    //console.log(error + " -> " + jsonData.errors[error]);
					}
				} else {
					if (id == 'product_image') {
						var imageHtml = [ '<div class="ms-image">',
										  '<input type="hidden" value="'+jsonData.file.name+'" name="product_images[]" />',
										  '<img src="'+jsonData.file.thumb+'" />',
										  '<img class="ms-remove" />',
										  '</div>' ];
						$("#product_image_files").append(imageHtml.join('')); 
					} else if (id == 'product_thumbnail') {
						var imageHtml = [ '<div class="ms-image">',
										  '<input type="hidden" value="'+jsonData.file.name+'" name="product_thumbnail_name" />',
										  '<img src="'+jsonData.file.thumb+'" />',
										  '<img class="ms-remove" />',
										  '</div>' ];
						$("#product_thumbnail_files").html(imageHtml.join(''));
					} else {
						$("#product_download_files").append('<input type="hidden" value="'+jsonData.file.src+'" name="product_downloads[]" />');
						$("#product_download_files").append('<p><b>'+jsonData.file.name+'</b> <span style="cursor: pointer">[ <?php echo $ms_delete; ?> ]</span></p>');						
					}
				}			
			}
		}).submit();
	});

	$("#ms-savedraft-button, #ms-submit-button").click(function() {
		var button = $(this);
		if ($(this).attr('id') == 'ms-savedraft-button') {
			var url = 'jxsaveproductdraft';
		} else {
			var url = 'jxsubmitproduct';
		}
		
	    $.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=account/ms-seller/'+url,
			data: $(this).parents("form").serialize(),
		    beforeSend: function() {
		    	$('#ms-new-product a.button').hide();
		    	button.before('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		    },			
			success: function(jsonData) {
				$('.error').text('');
				if (!jQuery.isEmptyObject(jsonData.errors)) {
					$('#ms-new-product a.button').show();
					button.prev('span.wait').remove();
					for (error in jsonData.errors) {
					    if (!jsonData.errors.hasOwnProperty(error)) {
					        continue;
					    }
					    $('#error_'+error).text(jsonData.errors[error]);
					    window.scrollTo(0,0);
					    
					}				
				} else {
					console.log('success');
					location = jsonData['redirect'];
				}
	       	}
		});
	});
});
</script>  
<?php echo $footer; ?>