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
      <table class="ms-product">
        <tr><td><h3><?php echo $ms_account_product_name_description; ?></h3></td></tr>      
        <tr>
          <td><span class="required">*</span> <?php echo $ms_account_product_name; ?></td>
          <td>
          	<input type="text" name="product_name" value="<?php echo $product['name']; ?>" />
          	<p class="ms-note"><?php echo $ms_account_product_name_note; ?></p>
          	<p class="error" id="error_product_name"></p>
          </td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $ms_account_product_description; ?></td>
          <td>
          	<textarea name="product_description"><?php echo $product['description']; ?></textarea>
          	<p class="ms-note"><?php echo $ms_account_product_description_note; ?></p>
          	<p class="error" id="error_product_description"></p>
          </td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $ms_account_product_tags; ?></td>
          <td>
          	<input type="text" name="product_tags" value="<?php echo $product['tags']; ?>" />
          	<p class="ms-note"><?php echo $ms_account_product_tags_note; ?></p>
          	<p class="error" id="error_product_tags"></p>
          </td>
        </tr>        
        
        <tr><td><h3><?php echo $ms_account_product_price_attributes; ?></h3></td></tr>
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
        
        <tr><td><h3><?php echo $ms_account_product_files; ?></h3></td></tr>
        <tr>
          <td><span class="required">*</span> <?php echo $ms_account_product_thumbnail; ?></td>
          <td>
          	<input type="file" name="product_thumbnail" id="product_thumbnail" />
          	<p class="ms-note"><?php echo $ms_account_product_thumbnail_note; ?></p>
          	<p class="error" id="error_product_thumbnail"></p>
          	<div id="product_thumbnail_images">
          		<?php if (!empty($product['thumbnail'])) { ?>
          		<input type="hidden" name="product_thumbnail_name" value="<?php echo $product['thumbnail']['name']; ?>" />
          		<img src="<?php echo $product['thumbnail']['src']; ?>" />
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
          	<div id="product_image_images">
          	<?php if (isset($product['images'])) { ?>
	          	<?php foreach ($product['images'] as $image) { ?>
	          		<input type="hidden" name="product_images[]" value="<?php echo $image['name']; ?>" />
	          		<img src="<?php echo $image['src']; ?>" />
	          	<?php } ?>
          	<?php } ?>
          	</div>
          </td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $ms_account_product_download; ?></td>
          <td></td>
        </tr>
        
        <tr><td><h3>Message to the reviewer</h3></td></tr>        
        <tr>
          <td><span class="required">*</span> <?php echo $ms_account_product_message; ?></td>
          <td>
          	<textarea name="product_message"></textarea>
          	<p class="ms-note"><?php echo $ms_account_product_message_note; ?></p>
          	<p class="error" id="error_product_message"></p>
          </td>          
        </tr>
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
	$("#product_image_images img, #product_thumbnail_images img").click(function() {
		$(this).prev("input:hidden").remove();
		$(this).remove();
	});

	$('#product_image').live('change', function() {
		$('#ms_action').val('image');
		$('#error_product_thumbnail').text('');
		$('#error_product_image').text('');		
		$("#ms-new-product").ajaxForm({
			url:  "index.php?route=account/ms-seller/jxuploadimage",
			dataType: 'json', 
		    beforeSend: function() {
				$("#product_image_images").append('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
				
		    },
			success: function(jsonData) {
				$('#product_image_images span.wait').remove();
			
				if (!jQuery.isEmptyObject(jsonData.errors)) {
					$('#error_product_image').text('');
					for (error in jsonData.errors) {
					    if (!jsonData.errors.hasOwnProperty(error)) {
					        continue;
					    }
					    if (error == '') {
					    $('#error_product_image').text(jsonData.errors[error]);
					    } else {
					    $('#error_'+error).text(jsonData.errors[error]);
					    }
					    console.log(error + " -> " + jsonData.errors[error]);
					}				
				} else {
					$("#product_image_images").append('<input type="hidden" value="'+jsonData.image.name+'" name="product_images[]" />');
					$("#product_image_images").append('<img src="'+jsonData.image.thumb+'" />');				
				}			
			}
		}).submit();
	});
	
	$('#product_thumbnail').live('change', function() {
		$('#ms_action').val('thumbnail');
		$('#error_product_thumbnail').text('');
		$('#error_product_image').text('');
		$("#ms-new-product").ajaxForm({
			url:  "index.php?route=account/ms-seller/jxuploadimage",
			dataType: 'json', 
		    beforeSend: function() {
				$("#product_thumbnail_images").html('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		    },
			success: function(jsonData) {
				$('#product_thumbnail_images span.wait').remove();
				if (!jQuery.isEmptyObject(jsonData.errors)) {
					$("#product_thumbnail_images").html('');
					$('#error_product_thumbnail').text('');
					for (error in jsonData.errors) {
					    if (!jsonData.errors.hasOwnProperty(error)) {
					        continue;
					    }
					    if (error == '') {
					    $('#error_product_thumbnail').text(jsonData.errors[error]);
					    } else {
					    $('#error_'+error).text(jsonData.errors[error]);
					    }
					    console.log(error + " -> " + jsonData.errors[error]);
					}				
				} else {
					$("#product_thumbnail_images").html('<input type="hidden" value="'+jsonData.image.name+'" name="product_thumbnail_name" />');
					$("#product_thumbnail_images").append('<img src="'+jsonData.image.thumb+'" />');				
					//$("#product_thumbnail_images").append('<input type="hidden" value="'+jsonData.image.name+'" />');
					//$("#product_thumbnail_images").append('<img src="'+jsonData.image.thumb+'" />');
				}			
			}
		}).submit();
	});

	$("#ms-savedraft-button").click(function() {
	    $.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=account/ms-seller/jxsaveproductdraft',
			data: $(this).parents("form").serialize(),
			success: function(jsonData) {
				$('.error').text('');
				if (!jQuery.isEmptyObject(jsonData.errors)) {
					for (error in jsonData.errors) {
					    if (!jsonData.errors.hasOwnProperty(error)) {
					        continue;
					    }
					    $('#error_'+error).text(jsonData.errors[error]);
					    console.log(error + " -> " + jsonData.errors[error]);
					    window.scrollTo(0,0);
					    
					}				
				} else {
					console.log('success');
					//location = jsonData['redirect'];
				}
	       	}
		});
	});
	
	$("#ms-submit-button").click(function() {
	    $.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=account/ms-seller/jxsubmitproduct',
			data: $(this).parents("form").serialize(),
			success: function(jsonData) {
				$('.error').text('');
				if (!jQuery.isEmptyObject(jsonData.errors)) {
					for (error in jsonData.errors) {
					    if (!jsonData.errors.hasOwnProperty(error)) {
					        continue;
					    }
					    $('#error_'+error).text(jsonData.errors[error]);
					    console.log(error + " -> " + jsonData.errors[error]);
					    window.scrollTo(0,0);
					    
					}				
				} else {
					//success
					//location = jsonData['redirect'];
				}
	       	}
		});
	});
});
</script>  
<?php echo $footer; ?>