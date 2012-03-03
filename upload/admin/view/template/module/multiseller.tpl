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
				<a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a>
				<a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a>
			</div>
	  	</div>
	  	<div class="content">
			<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
				<table class="form">
					<tr>
						<td>
							<span><?php echo $text_test; ?></span>
							<span class="help"><?php echo $text_test; ?></span>
						</td>
						<td>
							<input size="2" type="text" name="multiseller_conf_maxlen" value="<?php echo $multiseller_conf_maxlen; ?>" />
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
  </div>
</div>

<?php echo $footer; ?>	
</div>