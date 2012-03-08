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
							<input type="radio" name="msconf_seller_validation" value="1" <?php if($msconf_seller_validation) { ?> checked="checked" <?php } ?> />
                			<?php echo $text_yes; ?>
                			<input type="radio" name="msconf_seller_validation" value="0" <?php if(!$msconf_seller_validation) { ?> checked="checked" <?php } ?> />
               				<?php echo $text_no; ?>
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