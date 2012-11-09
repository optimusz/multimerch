<?php echo $header; ?>
<div id="content">
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
	<div class="box">
		<div class="heading">
			<h1><img src="view/image/user-group.png" alt="" /> <?php echo $heading; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
		     	<div id="tabs" class="htabs">
		     		<a href="#tab-general"><?php echo $tab_general; ?></a>
		     		<a href="#tab-commission"><?php echo $ms_commission; ?></a>
		     	</div>
		     	<div id="tab-general">				
				<table class="form">
					<tr>
						<td><span class="required">*</span> <?php echo $ms_name; ?></td>
						<td>
						<?php foreach ($languages as $language) { ?>
							<input type="text" name="seller_group[description][<?php echo $language['language_id']; ?>][name]" value="<?php echo $seller_group['description'][$language['language_id']]['name']; ?>" />
							<img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /><br />
							<?php if (isset($error_name[$language['language_id']])) { ?>
								<span class="error"><?php echo $error_name[$language['language_id']]; ?></span><br />
							<?php } ?>
						<?php } ?>
						</td>
					</tr>
					
					<?php foreach ($languages as $language) { ?>
					<tr>
						<td><?php echo $ms_description; ?></td>
						<td>
							<textarea name="seller_group[description][<?php echo $language['language_id']; ?>][description]" cols="40" rows="5"><?php echo $seller_group['description'][$language['language_id']]['description']; ?></textarea>
							<img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" align="top" />
						</td>
					</tr>
					<?php } ?>
					
				</table>
				</div>
				
				<div id="tab-commission">
				<table class="form">
					<tr>
						<td><span class="required">*</span> <?php echo $ms_commission_sale; ?></td>
						<td>
							<input type="text" name="seller_group[commission][sale][flat]" value="<?php echo isset($seller_group['commission']['sale']['flat']) ? $this->currency->format($seller_group['commission']['sale']['flat'], $this->config->get('config_currency'), '', FALSE) : '' ?>" size="3"/><?php echo $this->config->get('config_currency'); ?>
							+<input type="text" name="seller_group[commission][sale][pct]" value="<?php echo $seller_group['commission']['sale']['pct']; ?>" size="3"/>%						
						</td>
					</tr>
				</table>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
$('#tabs a').tabs();
</script>
<?php echo $footer; ?> 