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
          	<textarea name="languages[<?php echo $langId; ?>][product_description]"><?php echo strip_tags(htmlspecialchars_decode($product['languages'][$langId]['description'])); ?></textarea>
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
          	<?php if (!$msconf_allow_multiple_categories) { ?> 
			<select name="product_category">
            	<option value=""><?php echo ''; ?></option>
                  <?php foreach ($categories as $category) { ?>
                    <?php if (in_array($category['category_id'], explode(',',$product['category_id']))) { ?>
                    <option value="<?php echo $category['category_id']; ?>" selected="selected"><?php echo $category['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></option>
                    <?php } ?>
                  <?php } ?>
            </select>
            
            <?php } else { ?>
            
			<div class="scrollbox">
				<?php $class = 'odd'; ?>
				<?php foreach ($categories as $category) { ?>
					<?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
					<div class="<?php echo $class; ?>">
					<?php if (in_array($category['category_id'], explode(',',$product['category_id']))) { ?>
						<input type="checkbox" name="product_category[]" value="<?php echo $category['category_id']; ?>" checked="checked" />
						<?php echo $category['name']; ?>
					<?php } else { ?>
						<input type="checkbox" name="product_category[]" value="<?php echo $category['category_id']; ?>" />
						<?php echo $category['name']; ?>
					<?php } ?>
					</div>
				<?php } ?>
			</div>
            <?php } ?>
            
          	<p class="ms-note"><?php echo $ms_account_product_category_note; ?></p>
          	<p class="error" id="error_product_category"></p>
          </td>
      </tr>

<?php var_dump($msconf_enable_shipping); ?>
		<?php if ($msconf_enable_shipping == 2) { ?>
		    <tr>
		      <td><?php echo $ms_account_product_enable_shipping; ?></td>
		      <td>
		        <input type="radio" name="product_enable_shipping" value="1" <?php if($product['shipping'] == 1) { ?> checked="checked" <?php } ?>  />
		        <?php echo $text_yes; ?>
		        <input type="radio" name="product_enable_shipping" value="0" <?php if($product['shipping'] == 0) { ?> checked="checked" <?php } ?>  />
		        <?php echo $text_no; ?>
		      	<p class="ms-note"><?php echo $ms_account_product_enable_shipping_note; ?></p>
		      	<p class="error" id="error_product_enable_shipping"></p>
		      </td>
		    </tr>
		<?php } ?>

        <tr <?php if ($msconf_enable_quantities == 0 || ($msconf_enable_shipping != 1 && $msconf_enable_quantities == 2 && $product['shipping'] == 0)) { ?>style="display: none"<?php } ?>>
          <td><?php echo $ms_account_product_quantity; ?></td>
          <td>
          	<input type="text" name="product_quantity" value="<?php echo $product['quantity']; ?>" <?php if ($msconf_enable_quantities < 2) { ?>class="ffUnchangeable"<?php } ?> />
          	<p class="ms-note"><?php echo $ms_account_product_quantity_note; ?></p>
          	<p class="error" id="error_product_tags_<?php echo $langId; ?>"></p>
          </td>
        </tr>   

		<?php if ($options) { ?>
		<?php foreach ($options as $option) { ?>
			<tr>
			<td>
				<?php /* if ($option['required']) { ?> <span class="required">*</span> <?php } */ ?>
				<?php echo $option['name']; ?>
			</td>

			<td>

			<?php if ($option['type'] == 'select') { ?>
				<select name="product_attributes[<?php echo $option['option_id']; ?>]">
					<option value=""><?php echo $text_select; ?></option>
					<?php foreach ($option['values'] as $option_value) { ?>
					<option value="<?php echo $option_value['option_value_id']; ?>" <?php if (array_key_exists($option_value['option_value_id'], $product_attributes[$option['option_id']]['values'])) { ?>selected="selected"<?php } ?>><?php echo $option_value['name']; ?></option>
					<?php } ?>
				</select>
			<?php } ?>
			
			<?php if ($option['type'] == 'radio') { ?>
				<?php foreach ($option['values'] as $option_value) { ?>
				<input type="radio" name="product_attributes[<?php echo $option['option_id']; ?>]" value="<?php echo $option_value['option_value_id']; ?>" <?php if (array_key_exists($option_value['option_value_id'], $product_attributes[$option['option_id']]['values'])) { ?>checked="checked"<?php } ?> />
				<label><?php echo $option_value['name']; ?></label>
				<br />
				<?php } ?>
			<?php } ?>
			
			<?php if ($option['type'] == 'checkbox') { ?>
				<?php foreach ($option['values'] as $option_value) { ?>
				<input type="checkbox" name="product_attributes[<?php echo $option['option_id']; ?>][]" value="<?php echo $option_value['option_value_id']; ?>" <?php if (array_key_exists($option_value['option_value_id'], $product_attributes[$option['option_id']]['values'])) { ?>checked="checked"<?php } ?> />
				<label><?php echo $option_value['name']; ?></label>
				<br />
				<?php } ?>
			<?php } ?>
			
			<?php if ($option['type'] == 'text') { ?>
				<input type="text" name="product_attributes[<?php echo $option['option_id']; ?>]" value="<?php echo $option['option_value']; ?>" />
			<?php } ?>
			
			<?php if ($option['type'] == 'textarea') { ?>
				<textarea name="product_attributes[<?php echo $option['option_id']; ?>]" cols="40" rows="5"><?php echo $option['option_value']; ?></textarea>
			<?php } ?>
			
			<?php if ($option['type'] == 'date') { ?>
				<input type="text" name="product_attributes[<?php echo $option['option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="date" />
			<?php } ?>
			
			<?php if ($option['type'] == 'datetime') { ?>
				<input type="text" name="product_attributes[<?php echo $option['option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="datetime" />
			<?php } ?>
			
			<?php if ($option['type'] == 'time') { ?>
				<input type="text" name="product_attributes[<?php echo $option['option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="time" />
			<?php } ?>
		</td>
		</tr>
		<?php } ?>
		<?php } ?>
      
        <tr><td colspan="2"><h3><?php echo $ms_account_product_files; ?></h3></td></tr>
        <tr>
          <td><?php if ($msconf_required_images > 0) { ?><span class="required">*</span><?php } ?> <?php echo $ms_account_product_image; ?></td>
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

	$("input[name='product_enable_shipping']").live('change', function() {
		if ($(this).val() == 1) {
			if (!$("input[name='product_quantity']").hasClass("ffUnchangeable")) {
				$("input[name='product_quantity']").parents("tr").show();
			}
		} else {
			if (!$("input[name='product_quantity']").hasClass("ffUnchangeable")) {
				$("input[name='product_quantity']").parents("tr").hide();
			}
		}
	});

	$("#product_image_files, #product_thumbnail_files").delegate(".ms-remove", "click", function() {
		$(this).parent().remove();
	});

	$("#product_download_files").delegate("span.remove", "click", function() {
		$(this).parent().remove();
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
										  '<span class="ms-remove"></span>',
										  '</div>' ];
						$("#product_image_files").append(imageHtml.join('')); 
					} else {
						var imageHtml = [ '<div class="ms-download">',
										  '<input type="hidden" value="'+jsonData.file.src+'" name="product_downloads[]" />',
										  '<b>'+jsonData.file.name+'</b>' ];

						if (jsonData.file.pages > 0) {
							imageHtml.push('<input value="0-'+ jsonData.file.pages +'" name="pages" type="text" style="width:30px; margin-left: 50px" /> <a class="button ms-generate-images"><span><?php echo $ms_button_generate; ?></span></a>');
						}
						
						imageHtml.push('<span style="cursor: pointer" class="remove">[ <?php echo $ms_delete; ?> ]</span></div>');
										  
						$("#product_download_files").append(imageHtml.join(''));
						

					}
				}
			}
		}).submit();
	});

	$("#product_download_files").delegate(".ms-generate-images", "click", function() {
		var generateButton = $(this);
		$('#error_product_download').text('');
    	generateButton.hide();
    	generateButton.before('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
    	
	    $.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=account/ms-seller/jxgenerateimages',
			data: $(this).parent().find('input').serialize(),
		    beforeSend: function() {
		    	//$('#ms-new-product a.button').hide();
		    	//button.before('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		    },			
			success: function(jsonData) {
				generateButton.find('span').text('<?php echo $ms_button_regenerate; ?>');
				generateButton.show().prev('span.wait').remove();
				console.log(jsonData);
				if (!jQuery.isEmptyObject(jsonData.errors)) {
					for (error in jsonData.errors) {
					    if (!jsonData.errors.hasOwnProperty(error)) {
					        continue;
					    }
					    $('#error_'+error).text(jsonData.errors[error]);
					}				
				} else {
					var imageHtml = [];
					for(var i=0; i<jsonData.previews.length; i++) {
						imageHtml.push(
							'<div class="ms-image ms-pdf">',
							  '<input type="hidden" value="'+jsonData.previews[i].name+'" name="product_images[]" />',
							  '<img src="'+jsonData.previews[i].thumb+'" />',
							  '<span class="ms-remove"></span>',
							'</div>'
						);
					}
					console.log(jsonData.token);
					console.log();
					$('#product_image_files input[value^="'+jsonData.token+'"]').parent().remove();
					//$('#product_image_files .ms-image[value^="'+jsonData.token+'"]').remove();
					$("#product_image_files").append(imageHtml.join(''));
				}
	       	}
		});
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