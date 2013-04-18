<?php echo $header; ?>

<div id="content" class="ms-comment-page">
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
			<h1><img src="view/image/multiseller/ms-bubble.png" alt="" /> <?php echo $ms_comments_heading; ?></h1>
		</div>
		
		<div class="content">
		<form id="form">
		<table class="list" style="text-align: center">
		<thead>
			<tr>
			<td class="checkbox"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" value="<?php echo $comment['comment_id']; ?>" /></td>
			<td><?php echo $ms_name; ?></a></td>
			<td><?php echo $ms_product; ?></a></td>
			<td class="comment"><?php echo $ms_comments_comment; ?></a></td>
			<td><?php echo $ms_date; ?></a></td>
			<td><?php echo $ms_action; ?></a></td>
			</tr>
		</thead>
		
		<tbody>
			<?php if (isset($comments) && $comments) { ?>
			<?php foreach ($comments as $comment) { ?>
			<tr>
				<td><input type="checkbox" name="selected[]" value="<?php echo $comment['comment_id']; ?>" /></td>
				<td>
					<?php if (!$comment['customer_link']) { ?>
						<?php echo $comment['name']; ?>
					<? } else { ?>
						<a href="<?php echo $comment['customer_link']; ?>"><?php echo $comment['name']; ?></a>
					<?php } ?> 
				</td>
				<td><b><?php echo $comment['product_name']; ?></b></td>
				<td><?php echo $comment['comment']; ?></td>
				<td><?php echo $comment['date_created']; ?></td>
				<td>
					<a class="ms-button ms-button-delete" title="<?php echo $ms_delete; ?>"></a>
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

<script type="text/javascript">
$(document).ready(function() {
	$(".ms-button-delete").click(function() {
		var comment_id = $(this).parents('tr').children('td:first').find('input:checkbox').val();
	    $.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=multiseller/comment/jxDelete&comment_id='+ comment_id +'&token=<?php echo $token; ?>',
			beforeSend: function() {
				$('.warning').text('').hide();
			},
			complete: function(jqXHR, textStatus) {
				//console.log(textStatus);
				window.location.reload();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				$('.warning').text(textStatus).show();
			},				
			success: function(jsonData) {
				window.location.reload();
			}
		});
	});
});
</script>

<?php echo $footer; ?> 