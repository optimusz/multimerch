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
	<?php if ($success) { ?>
		<div class="success"><?php echo $success; ?></div>
	<?php } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="view/image/badge.png" alt="" /> <?php echo $heading; ?></h1>
			<div class="buttons">
				<a onclick="location = '<?php echo $insert; ?>'" class="button"><?php echo $button_insert; ?></a>
				<a onclick="$('form').submit();" class="button"><?php echo $button_delete; ?></a>
			</div>
		</div>
		<div class="content">
		<form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
			<table class="list" style="text-align: center">
			<thead>
				<tr>
					<td width="1"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
					<td><?php echo $ms_badges_column_id; ?></td>
					<td style="width: 100px"><?php echo $ms_badges_column_name; ?></td>
					<td><?php echo $ms_description; ?></td>
					<td style="width: 450px"><?php echo $ms_badges_image; ?></td>
					<td><?php echo $ms_badges_column_action; ?></td>
				</tr>
			</thead>
			<tbody>
				<?php if ($badges) { ?>
				<?php foreach ($badges as $badge) { ?>
				<tr>
					<td style="text-align: center;"><?php if ($badge['selected']) { ?>
						<input type="checkbox" name="selected[]" value="<?php echo $badge['badge_id']; ?>" checked="checked" />
						<?php } else { ?>
						<input type="checkbox" name="selected[]" value="<?php echo $badge['badge_id']; ?>" />
						<?php } ?></td>
					<td><?php echo $badge['badge_id']; ?></td>
					<td><?php echo $badge['name']; ?></td>
					<td><?php echo $badge['description']; ?></td>
					<td><img src="<?php echo $badge['image']; ?>"/></td>
					<td>
						<a class="ms-button ms-button-edit" href="<?php echo $this->url->link('multiseller/badge/update', 'token=' . $this->session->data['token'] . '&badge_id=' . $badge['badge_id'], 'SSL'); ?>" title="<?php echo $text_edit; ?>"></a>
						<a class="ms-button ms-button-delete" href="<?php echo $this->url->link('multiseller/badge/delete', 'token=' . $this->session->data['token'] . '&badge_id=' . $badge['badge_id'], 'SSL'); ?>" title="<?php echo $button_delete; ?>"></a>
					</td>
				</tr>
				<?php } ?>
				<?php } else { ?>
				<tr>
					<td class="center" colspan="6"><?php echo $text_no_results; ?></td>
				</tr>
				<?php } ?>
			</tbody>
			</table>
		</form>
		<div class="pagination"><?php echo $pagination; ?></div>
		</div>
	</div>
</div>
<?php echo $footer; ?> 