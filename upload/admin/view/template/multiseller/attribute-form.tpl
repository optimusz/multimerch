<?php echo $header; ?>
<div id="content">
<div class="breadcrumb">
	<?php foreach ($breadcrumbs as $breadcrumb) { ?>
	<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
	<?php } ?>
</div>
<div class="warning error" style="display: none"></div>
<div class="box">
	<div class="heading">
		<h1><img src="view/image/order.png" alt="" /> <?php echo $heading; ?></h1>
		<div class="buttons">
			<a id="ms-submit-button" class="button"><?php echo $button_save; ?></a>
			<a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a>
		</div>
	</div>
	<div class="content">
	<form method="post" enctype="multipart/form-data" id="ms-attribute">
	<input type="hidden" name="attribute_id" value="<?php echo $attribute['attribute_id']; ?>" />
	<table class="form">
		<tr>
			<td><span class="required">*</span> <?php echo $ms_name; ?></td>
			<td>
			<?php foreach ($languages as $language) { ?>
				<input type="text" name="attribute_description[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($attribute['attribute_description'][$language['language_id']]['name']) ? $attribute['attribute_description'][$language['language_id']]['name'] : ''; ?>" />
				<img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" />
				<span class="error"></span>
			<?php } ?>
			</td>
		</tr>
		
		<tr>
			<td><?php echo $ms_description; ?></td>
			<td>
			<?php foreach ($languages as $language) { ?>
				<input type="text" size="60" name="attribute_description[<?php echo $language['language_id']; ?>][description]" value="<?php echo isset($attribute['attribute_description'][$language['language_id']]['description']) ? $attribute['attribute_description'][$language['language_id']]['description'] : ''; ?>" />
				<img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /><br />
				<span class="error"></span>
			<?php } ?>
			</td>
		</tr>

		<tr>
			<td><?php echo $ms_attribute_group; ?></td>
			<td>
				<select name="attribute_group_id">
				<?php foreach ($attribute_groups as $attribute_group) { ?>
				<option value="<?php echo $attribute_group['attribute_group_id']; ?>" <?php if ($attribute_group['attribute_group_id'] == $attribute['attribute_group_id']) { ?>selected="selected"<?php } ?>><?php echo $attribute_group['name']; ?></option>
				<?php } ?>
				</select>
			</td>
		</tr>
		
		<tr>
			<td><?php echo $ms_type; ?></td>
			<td>
			<select name="attribute_type">
				<option value="<?php echo MsAttribute::TYPE_CHECKBOX; ?>" <?php if (isset($attribute['attribute_type']) && $attribute['attribute_type'] == MsAttribute::TYPE_CHECKBOX) { ?>selected<?php } ?>><?php echo $ms_type_checkbox; ?></option>
				<option value="<?php echo MsAttribute::TYPE_DATE; ?>" <?php if (isset($attribute['attribute_type']) && $attribute['attribute_type'] == MsAttribute::TYPE_DATE) { ?>selected<?php } ?>><?php echo $ms_type_date; ?></option>
				<option value="<?php echo MsAttribute::TYPE_DATETIME; ?>" <?php if (isset($attribute['attribute_type']) && $attribute['attribute_type'] == MsAttribute::TYPE_DATETIME) { ?>selected<?php } ?>><?php echo $ms_type_datetime; ?></option>
				<?php /* ?><option value="<?php echo MsAttribute::TYPE_FILE; ?>" <?php if (isset($attribute['attribute_type']) && $attribute['attribute_type'] == MsAttribute::TYPE_FILE) { ?>selected<?php } ?>><?php echo $ms_type_file; ?></option><?php */ ?>
				<option value="<?php echo MsAttribute::TYPE_IMAGE; ?>" <?php if (isset($attribute['attribute_type']) && $attribute['attribute_type'] == MsAttribute::TYPE_IMAGE) { ?>selected<?php } ?>><?php echo $ms_type_image; ?></option>
				<option value="<?php echo MsAttribute::TYPE_RADIO; ?>" <?php if (isset($attribute['attribute_type']) && $attribute['attribute_type'] == MsAttribute::TYPE_RADIO) { ?>selected<?php } ?>><?php echo $ms_type_radio; ?></option>
				<option value="<?php echo MsAttribute::TYPE_SELECT; ?>" <?php if (isset($attribute['attribute_type']) && $attribute['attribute_type'] == MsAttribute::TYPE_SELECT) { ?>selected<?php } ?>><?php echo $ms_type_select; ?></option>
				<option value="<?php echo MsAttribute::TYPE_TEXT; ?>" <?php if (isset($attribute['attribute_type']) && $attribute['attribute_type'] == MsAttribute::TYPE_TEXT) { ?>selected<?php } ?>><?php echo $ms_type_text; ?></option>
				<option value="<?php echo MsAttribute::TYPE_TEXTAREA; ?>" <?php if (isset($attribute['attribute_type']) && $attribute['attribute_type'] == MsAttribute::TYPE_TEXTAREA) { ?>selected<?php } ?>><?php echo $ms_type_textarea; ?></option>
				<option value="<?php echo MsAttribute::TYPE_TIME; ?>" <?php if (isset($attribute['attribute_type']) && $attribute['attribute_type'] == MsAttribute::TYPE_TIME) { ?>selected<?php } ?>><?php echo $ms_type_time; ?></option>
			</select>
			<span class="error"></span><br />
			</td>
		</tr>
		
		<tr>
			<td><?php echo $ms_attribute_text_type; ?></td>
			<td>
				<input type="radio" name="text_type" value="normal" <?php if(!$attribute['multilang'] && !$attribute['number']) { ?>checked="checked"<?php } ?> /><?php echo $ms_attribute_normal; ?>
				<input type="radio" name="text_type" value="multilang" <?php if($attribute['multilang']) { ?>checked="checked"<?php } ?> /><?php echo $ms_attribute_multilang; ?>
				<!-- <input type="radio" name="text_type" value="number" <?php if($attribute['number']) { ?>checked="checked"<?php } ?> /><?php echo $ms_attribute_number; ?> -->
				<span class="error"></span><br />
			</td>
		</tr>
		
		<tr>
			<td><?php echo $ms_attribute_tab_display; ?></td>
			<td>
				<input type="radio" name="tab_display" value="1" <?php if(isset($attribute['tab_display']) && $attribute['tab_display']) { ?>checked="checked"<?php } ?> /><?php echo $text_yes; ?>
				<input type="radio" name="tab_display" value="0" <?php if(!$attribute['tab_display'] || !isset($attribute['tab_display'])) { ?>checked="checked"<?php } ?> /><?php echo $text_no; ?>
			</td>
		</tr>		
		
		<tr>
			<td><?php echo $ms_attribute_required; ?></td>
			<td>
				<input type="checkbox" name="required" <?php if($attribute['required']) { ?>checked="checked"<?php } ?> />
				<span class="error"></span><br />
			</td>
		</tr>
		
		<tr>
			<td><?php echo $ms_sort_order; ?></td>
			<td>
				<input type="text" name="sort_order" value="<?php echo $attribute['sort_order']; ?>" size="1" />
				<span class="error"></span><br />
			</td>
		</tr>
		
		<tr>
			<td><?php echo $ms_enabled; ?></td>
			<td>
				<input type="checkbox" name="enabled" <?php if($attribute['enabled'] || !isset($attribute['attribute_id'])) { ?>checked="checked"<?php } ?> />
				<span class="error"></span><br />
			</td>
		</tr>		
	</table>
	
	<table id="attribute-value" class="list">
		<thead>
			<tr>
				<td class="left"><span class="required">*</span> <?php echo $ms_attribute_value; ?></td>
				<td class="left"><?php echo $ms_image; ?></td>
				<td class="right"><?php echo $ms_sort_order; ?></td>
				<td></td>
			</tr>
		</thead>

		<tbody>		
		<tr class="ffSample">
			<td>
				<?php foreach ($languages as $language) { ?>
				<input type="text" name="attribute_value[0][attribute_value_description][<?php echo $language['language_id']; ?>][name]" value="" />
				<img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /><br />
				<span class="error"></span><br />
				<?php } ?>
			</td>
			
			<td>
				<div class="image">
					<img src="<?php echo $no_image; ?>" alt="" />
					<input type="hidden" name="attribute_value[0][image]" value="" id="field0"/><br />
					<a class="browseFiles"><?php echo $text_browse; ?></a>
					&nbsp;&nbsp;|&nbsp;&nbsp;
					<a onclick="$(this).prevAll('img').attr('src', '<?php echo $no_image; ?>'); $(this).prevAll('input').attr('value', '');"><?php echo $text_clear; ?></a>
				</div>
			</td>
			
			<td>
				<input type="text" name="attribute_value[0][sort_order]" value="" size="1" />
				<span class="error"></span><br />
			</td>
			
			<td>
				<a class="button ms-button-delete" title="<?php echo $ms_delete; ?>"><?php echo $ms_delete; ?></a>
			</td>
		</tr>
		
		<?php $attribute_value_row = 1; ?>
		<?php if (isset($attribute['attribute_values'])) { ?>
		<?php foreach ($attribute['attribute_values'] as $attribute_value) { ?>
		<tr>
			<td>
				<input type="hidden" name="attribute_value[<?php echo $attribute_value_row; ?>][attribute_value_id]" value="<?php echo $attribute_value['attribute_value_id']; ?>" />
				<?php foreach ($languages as $language) { ?>
				<input type="text" name="attribute_value[<?php echo $attribute_value_row; ?>][attribute_value_description][<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($attribute_value['attribute_value_description'][$language['language_id']]) ? $attribute_value['attribute_value_description'][$language['language_id']]['name'] : ''; ?>" />
				<img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /><br />
				<span class="error"></span><br />
				<?php } ?>
			</td>
			
			<td>
				<div class="image">
					<img src="<?php echo $attribute_value['thumb']; ?>" alt="" />
					<input type="hidden" name="attribute_value[<?php echo $attribute_value_row; ?>][image]" value="<?php echo $attribute_value['image']; ?>" id="field<?php echo $attribute_value_row; ?>"/>
					<br />
					<a class="browseFiles"><?php echo $text_browse; ?></a>
					&nbsp;&nbsp;|&nbsp;&nbsp;
					<a onclick="$(this).prevAll('img').attr('src', '<?php echo $no_image; ?>'); $(this).prevAll('input').attr('value', '');"><?php echo $text_clear; ?></a>
				</div>
			</td>
			
			<td>
				<input type="text" name="attribute_value[<?php echo $attribute_value_row; ?>][sort_order]" value="<?php echo $attribute_value['sort_order']; ?>" size="1" />
				<span class="error"></span><br />
			</td>
			
			<td>
				<a class="button ms-button-delete" title="<?php echo $ms_delete; ?>"><?php echo $ms_delete; ?></a>
			</td>
		</tr>
		<?php $attribute_value_row++; ?>
		<?php } ?>
		<?php } ?>
		</tbody>
				
		<tfoot>
			<tr>
				<td colspan="4" class="center">
					<a class="button ffClone"><?php echo $ms_add_attribute_value; ?></a>
				</td>
			</tr>
		</tfoot>
	</table>
	</form>
	</div>
</div>
</div>

<script type="text/javascript">

$(function() {
	$('body').delegate(".browseFiles", "click", function() {
		var thumb = $(this).prevAll('img');
		var field = $(this).prevAll('input');
		$('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/filemanager&token=<?php echo $token; ?>&field=' + encodeURIComponent(field.attr('id')) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
		$('#dialog').dialog({
			title: '<?php echo $text_image_manager; ?>',
			close: function (event, ui) {
				if (field.val()) {
					$.ajax({
						url: 'index.php?route=common/filemanager/image&token=<?php echo $token; ?>&image=' + encodeURIComponent(field.val()),
						dataType: 'text',
						success: function(data) {
							thumb.replaceWith('<img src="' + data + '" alt="" />');
						}
					});
				}
			},	
			bgiframe: false,
			width: 800,
			height: 400,
			resizable: false,
			modal: false
		});	
	});

	$('select[name="attribute_type"]').bind('change', function() {
		if (this.value == '<?php echo MsAttribute::TYPE_SELECT; ?>' || this.value == '<?php echo MsAttribute::TYPE_RADIO; ?>' || this.value == '<?php echo MsAttribute::TYPE_CHECKBOX; ?>' || this.value == '<?php echo MsAttribute::TYPE_IMAGE; ?>') {
			$('#attribute-value').show();
		} else {
			$('#attribute-value').hide();
		}
		
		if (this.value == '<?php echo MsAttribute::TYPE_TEXT; ?>' || this.value == '<?php echo MsAttribute::TYPE_TEXTAREA; ?>') {
			$('[name="text_type"], [name="tab_display"]').parents('tr').show();
		} else {
			$('[name="text_type"], [name="tab_display"]').parents('tr').hide();
		}
	}).change();

	$("#ms-submit-button").click(function() {
		var button = $(this);
		var id = $(this).attr('id');
		$.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=multiseller/attribute/jxsubmitattribute&token=<?php echo $token; ?>',
			data: $('#ms-attribute').serialize(),
			beforeSend: function() {
				button.hide().before('<span class="wait">&nbsp;<img src="view/image/loading.gif" alt="" /></span>');
				$('.error').text('');
				$('.warning').text('').hide();
			},
			complete: function(jqXHR, textStatus) {
				button.show().prev('span.wait').remove();
				console.log(textStatus);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				$('.warning').text(textStatus).show();
			},
			success: function(jsonData) {
				if (!jQuery.isEmptyObject(jsonData.errors)) {
					for (error in jsonData.errors) {
						if (!jsonData.errors.hasOwnProperty(error)) {
							continue;
						}
						
						$('[name="'+error+'"]').nextAll('.error:first').text(jsonData.errors[error]);
					}				
				} else {
					location = jsonData['redirect'];
				}
			}
		});
	});
	
	$('body').delegate("a.ffClone", "click", function() {
		var lastRow = $(this).parents('table').find('tbody tr:last input:last').attr('name');
		if (typeof lastRow == "undefined") {
			var newRowNum = 1;
		} else {
			var newRowNum = parseInt(lastRow.match(/[0-9]+/)) + 1;
		}

		var newRow = $(this).parents('table').find('tbody tr.ffSample').clone();
		newRow.find('input,select').attr('name', function(i,name) {
			return name.replace('[0]','[' + newRowNum + ']');
		});
		
		// %!@#$!!
		newRow.find('input[id^="field"]').attr('id', function(i,id) {
			return id.replace('0',newRowNum);
		});		
		
		$(this).parents('table').find('tbody').append(newRow.removeAttr('class'));
	});
	
	$("body").delegate(".ms-button-delete", "click", function() {
		$(this).parents('tr').remove();
	});
});
</script> 
<?php echo $footer; ?>