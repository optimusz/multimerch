<?php echo $header; ?>

<div id="content" class="ms-account-conversation">
	<?php echo $content_top; ?>
	
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>

	<h1><?php echo $ms_account_conversations_heading; ?></h1>

	<?php if (isset($error_warning) && ($error_warning)) { ?>
		<div class="warning"><?php echo $error_warning; ?></div>
	<?php } ?>
	
	<?php if (isset($success) && ($success)) { ?>
		<div class="success"><?php echo $success; ?></div>
	<?php } ?>
	
	<table class="list" id="list-conversations">
	<thead>
	<tr>
		<td><?php echo $ms_account_conversations_status; ?></td>
		<td><?php echo $ms_account_conversations_with; ?></td>
		<td><?php echo $ms_account_conversations_title; ?></td>
		<td><?php echo $ms_last_message; ?></td>
		<td class="small"><?php echo $ms_action; ?></td>
	</tr>
	</thead>
	<tbody></tbody>
	</table>

	<div class="buttons">
		<div class="left">
			<a href="<?php echo $link_back; ?>" class="button">
				<span><?php echo $button_back; ?></span>
			</a>
		</div>
	</div>

	<?php echo $content_bottom; ?>
</div>

<script>
	$(function() {
		$('#list-conversations').dataTable( {
			"sAjaxSource": "index.php?route=account/msconversation/getTableData",
			"aaSorting": [[ 3, "desc" ]],
			"aoColumns": [
				{ "mData": "icon", "bSortable": false },
				{ "mData": "with", "bSortable": false },
				{ "mData": "title" },
				{ "mData": "last_message_date" },
				{ "mData": "actions", "bSortable": false, "sClass": "center" }
			],
		});
	});
</script>
<?php echo $footer; ?>