<?php echo $header; ?>
<div id="content">
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
	<div class="box">
		<div class="heading">
			<h1><img src="view/image/badge-add.png" alt="" /> <?php echo $heading; ?></h1>
			<div class="buttons">
				<a id="ms-submit-button" class="button"><?php echo $button_save; ?></a>
				<a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a>
			</div>
		</div>
		<div class="content">
			<form method="post" enctype="multipart/form-data" id="form">
			<input type="hidden" name="badge[badge_id]" value="<?php echo $badge['badge_id']; ?>" />
				<table class="form">
					<tr>
						<td><span class="required">*</span> <?php echo $ms_name; ?></td>
						<td>
						<?php foreach ($languages as $language) { ?>
							<input type="text" name="badge[description][<?php echo $language['language_id']; ?>][name]" value="<?php echo $badge['description'][$language['language_id']]['name']; ?>" />
							<img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /><br />
							<p class="error" id="error_name_<?php echo $language['language_id']; ?>"></p>
						<?php } ?>
						</td>
					</tr>

					<tr>
						<td><?php echo $ms_description; ?></td>
						<td>					
					<?php foreach ($languages as $language) { ?>
							<textarea name="badge[description][<?php echo $language['language_id']; ?>][description]" cols="40" rows="5"><?php echo $badge['description'][$language['language_id']]['description']; ?></textarea>
							<img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" align="top" />
							<p class="error" id="error_description"></p>
					<?php } ?>
						</td>
					</tr>
					<tr>
						<td><?php echo $ms_badges_image; ?></td>
						<td>
							<div class="image">
								<?php if($badge['image'] != "") { ?> 
									<img src="<?php echo $badge['image']; ?>" alt="" />
								<?php }else {?>
									<img src="<?php echo $no_image; ?>" alt="" />
								<?php } ?>
								<input type="hidden" name="badge[image]" value="" id="field0"/><br />
								<a class="browseFiles"><?php echo $text_browse; ?></a>
								&nbsp;&nbsp;|&nbsp;&nbsp;
								<a onclick="$(this).prevAll('img').attr('src', '<?php echo $no_image; ?>'); $(this).prevAll('input').attr('value', '');"><?php echo $text_clear; ?></a>
							</div>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>

<script>
$('#tabs a').tabs();

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

$("#ms-submit-button").click(function() {
	var id = $(this).attr('id');
    $.ajax({
		type: "POST",
		dataType: "json",
		url: 'index.php?route=multiseller/badge/jxSave&token=<?php echo $token; ?>',
		data: $('#form').serialize(),
		success: function(jsonData) {
			console.log(jsonData);
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
				}
				window.scrollTo(0,0);
				$("#ms-submit-button").show();
			} else {
				window.location = 'index.php?route=multiseller/badge&token=<?php echo $token; ?>';
			}
       	}
	});
});
</script>
<?php echo $footer; ?> 