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
			<table class="list" style="text-align: center" id="list-seller-groups">
			<thead>
				<tr>
					<td width="1"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
					<td><?php echo $ms_seller_groups_column_id; ?></td>
					<td style="width: 100px"><?php echo $ms_seller_groups_column_name; ?></td>
					<td><?php echo $ms_description; ?></td>
					<td style="width: 450px"><?php echo $ms_commission_actual; ?></td>
					<td><?php echo $ms_seller_groups_column_action; ?></td>
				</tr>
				<tr class="filter">
					<td></td>
					<td><input type="text"/></td>
					<td><input type="text"/></td>
					<td><input type="text"/></td>
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

<script type="text/javascript">
$(document).ready(function() {
	$('#list-seller-groups').dataTable( {
		"sAjaxSource": "index.php?route=multiseller/seller-group/getTableData&token=<?php echo $token; ?>",
		"aoColumns": [
			{ "mData": "checkbox", "bSortable": false },
			{ "mData": "id" },
			{ "mData": "name" },
			{ "mData": "description" },
			{ "mData": "rates", "bSortable": false },
			{ "mData": "actions", "bSortable": false, "sClass": "right" }
		],
	});
});
</script>
<?php echo $footer; ?> 