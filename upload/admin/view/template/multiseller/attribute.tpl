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
		<h1><img src="view/image/order.png" alt="" /> <?php echo $ms_attribute_heading; ?></h1>
		<div class="buttons">
			<a href="index.php?route=multiseller/attribute/create&token=<?php echo $token; ?>" class="button"><?php echo $button_insert; ?></a>
			<a id="ms-delete-attribute" class="button"><?php echo $button_delete; ?></a>
		</div>
	</div>
	<div class="content">
		<form action="" method="post" enctype="multipart/form-data" id="form">
		<table class="list" style="text-align: center">
			<thead>
			<tr>
				<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
				<td><?php echo $ms_name; ?></a></td>
				<td><?php echo $ms_type; ?></a></td>
				<td><?php echo $ms_sort_order; ?></a></td>
				<td><?php echo $ms_status; ?></a></td>
				<td><?php echo $ms_action; ?></a></td>
			</tr>
			</thead>
			<tbody>
			<?php if (isset($attributes) && $attributes) { ?>
			<?php foreach ($attributes as $attribute) { ?>
			<tr>
				<td style="text-align: center;">
				<input type="checkbox" name="selected[]" value="<?php echo $attribute['attribute_id']; ?>" />
				</td>
				<td><?php echo $attribute['name']; ?></td>
				<td><?php echo $attribute['type']; ?></td>
				<td><?php echo $attribute['sort_order']; ?></td>
				<td><?php echo $attribute['enabled'] ? $ms_enabled : $ms_disabled; ?></td>
				<td>
					<a class="ms-button ms-button-edit" href="<?php echo $this->url->link('multiseller/attribute/update', 'token=' . $this->session->data['token'] . '&attribute_id=' . $attribute['attribute_id'], 'SSL'); ?>" title="<?php echo $text_edit; ?>"></a>
				</td>
			</tr>
			<?php } ?>
			<?php } else { ?>
			<tr>
				<td class="center" colspan="10"><?php echo $text_no_results; ?></td>
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

<script type="text/javascript">
$(function() {
	$("#ms-delete-attribute").click(function() {
		var data  = $('#form').serialize();
		$('#ms-delete-attribute').before('<img src="view/image/loading.gif" alt="" />');
		$.ajax({
			type: "POST",
			//async: false,
			dataType: "json",
			url: 'index.php?route=multiseller/attribute/jxDeleteAttribute&token=<?php echo $token; ?>',
			data: data,
			success: function(jsonData) {
				window.location.reload();
			}
		});
	});
});
</script> 