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
		<table class="list" style="text-align: center" id="list-attributes">
			<thead>
			<tr>
				<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
				<td><?php echo $ms_name; ?></a></td>
				<td><?php echo $ms_type; ?></a></td>
				<td><?php echo $ms_sort_order; ?></a></td>
				<td><?php echo $ms_status; ?></a></td>
				<td><?php echo $ms_action; ?></a></td>
			</tr>
			<tr class="filter">
				<td></td>
				<td><input type="text"/></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			</thead>
			<tbody></tbody>
		</table>
		</form>
	</div>
	</div>
</div>
<?php echo $footer; ?>

<script type="text/javascript">
$(function() {
	$('#list-attributes').dataTable( {
		"sAjaxSource": "index.php?route=multiseller/attribute/getTableData&token=<?php echo $token; ?>",
		"aoColumns": [
			{ "mData": "checkbox", "bSortable": false },
			{ "mData": "name" },
			{ "mData": "type" },
			{ "mData": "sort_order" },
			{ "mData": "status" },
			{ "mData": "actions", "bSortable": false, "sClass": "right" }
		],
	});

	$("#ms-delete-attribute").click(function() {
		var data  = $('#form').serialize();
		$('#ms-delete-attribute').before('<img src="view/image/loading.gif" alt="" />');
		$.ajax({
			type: "POST",
			//async: false,
			dataType: "json",
			url: 'index.php?route=multiseller/attribute/delete&token=<?php echo $token; ?>',
			data: data,
			success: function(jsonData) {
				window.location.reload();
			}
		});
	});
});
</script> 