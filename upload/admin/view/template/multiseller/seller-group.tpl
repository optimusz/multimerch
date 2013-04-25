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
			<h1><img src="view/image/user-group.png" alt="" /> <?php echo $heading; ?></h1>
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
					<td><?php echo $ms_seller_groups_column_id; ?></td>
					<td style="width: 100px"><?php echo $ms_seller_groups_column_name; ?></td>
					<td><?php echo $ms_description; ?></td>
					<td style="width: 450px"><?php echo $ms_commission_actual; ?></td>
					<td><?php echo $ms_seller_groups_column_action; ?></td>
				</tr>
			</thead>
			<tbody>
				<?php if ($seller_groups) { ?>
				<?php foreach ($seller_groups as $seller_group) { ?>
				<tr>
					<td style="text-align: center;"><?php if ($seller_group['selected']) { ?>
						<input type="checkbox" name="selected[]" value="<?php echo $seller_group['seller_group_id']; ?>" checked="checked" />
						<?php } else { ?>
						<input type="checkbox" name="selected[]" value="<?php echo $seller_group['seller_group_id']; ?>" />
						<?php } ?></td>
					<td><?php echo $seller_group['seller_group_id']; ?></td>
					<td><?php echo $seller_group['name']; ?></td>
					<td><?php echo $seller_group['description']; ?></td>
					<td><?php echo $seller_group['actual_fees']; ?></td>
					<td>
						<a class="ms-button ms-button-edit" href="<?php echo $this->url->link('multiseller/seller-group/update', 'token=' . $this->session->data['token'] . '&seller_group_id=' . $seller_group['seller_group_id'], 'SSL'); ?>" title="<?php echo $text_edit; ?>"></a>
						<a class="ms-button ms-button-delete" href="<?php echo $this->url->link('multiseller/seller-group/delete', 'token=' . $this->session->data['token'] . '&seller_group_id=' . $seller_group['seller_group_id'], 'SSL'); ?>" title="<?php echo $button_delete; ?>"></a>
					</td>
				</tr>
				<?php } ?>
				<?php } else { ?>
				<tr>
					<td class="center" colspan="4"><?php echo $text_no_results; ?></td>
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