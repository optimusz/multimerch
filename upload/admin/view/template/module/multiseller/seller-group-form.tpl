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
			<h1><img src="view/image/user-group.png" alt="" /> <?php echo $heading; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
				
					<tr>
						<td><span class="required">*</span> <?php echo $entry_name; ?></td>
						<td>
						<?php foreach ($languages as $language) { ?>
							<input type="text" name="seller_group_description[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($seller_group_description[$language['language_id']]) ? $seller_group_description[$language['language_id']]['name'] : ''; ?>" />
							<img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /><br />
							<?php if (isset($error_name[$language['language_id']])) { ?>
								<span class="error"><?php echo $error_name[$language['language_id']]; ?></span><br />
							<?php } ?>
						<?php } ?>
						</td>
					</tr>
					
					<?php foreach ($languages as $language) { ?>
					<tr>
						<td><?php echo $entry_description; ?></td>
						<td>
							<textarea name="seller_group_description[<?php echo $language['language_id']; ?>][description]" cols="40" rows="5"><?php echo isset($seller_group_description[$language['language_id']]) ? $seller_group_description[$language['language_id']]['description'] : ''; ?></textarea>
							<img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" align="top" />
						</td>
					</tr>
					<?php } ?>
					
				</table>
			</form>
		</div>
	</div>
</div>
<?php echo $footer; ?> 