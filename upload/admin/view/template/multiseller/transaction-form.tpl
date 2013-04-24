<?php echo $header; ?>
<div id="content">
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
	
	<p class="warning" style="display: none"></p>
	
	<div class="box">
		<div class="heading">
			<h1><img src="view/image/multiseller/ms-book.png" alt="" /> <?php echo $heading; ?></h1>
			<div class="buttons">
				<a id="ms-submit-button" class="button"><?php echo $button_save; ?></a>
				<a onclick="location = '<?php echo $this->url->link('multiseller/transaction', 'token=' . $this->session->data['token']); ?>'" class="button"><?php echo $button_cancel; ?></a>
			</div>
		</div>
		<div class="content">
			<form method="post" enctype="multipart/form-data" id="form">
			<table class="form">
				<tr>
					<td><?php echo $ms_from; ?></td>
					<td>
					<select name="transaction[from]">
						<option value=""><?php echo $ms_none; ?></option>
						<?php foreach($sellers as $seller) { ?>
						<option value="<?php echo $seller['seller_id']; ?>"><?php echo $seller['name']; ?></option>
						<?php } ?>
					</select>
					</td>
				</tr>

				<tr>
					<td><?php echo $ms_to; ?></td>
					<td>
					<select name="transaction[to]">
						<option value=""><?php echo $ms_none; ?></option>
						<?php foreach($sellers as $seller) { ?>
						<option value="<?php echo $seller['seller_id']; ?>"><?php echo $seller['name']; ?></option>
						<?php } ?>
					</select>
					</td>
				</tr>

				<tr>
					<td><span class="required">*</span> <?php echo $ms_amount; ?></td>
					<td>
						<?php echo $this->currency->getSymbolLeft(); ?>
						<input type="text" name="transaction[amount]" size="5"></input>
						<?php echo $this->currency->getSymbolRight(); ?>
					</td>
				</tr>

				<tr>
					<td><?php echo $ms_description; ?></td>
					<td><textarea name="transaction[description]" cols="40" rows="5"></textarea></td>
				</tr>
			</table>
			</form>
		</div>
	</div>
</div>

<script>
$("#ms-submit-button").click(function() {
	var button = $(this);
	$.ajax({
		type: "POST",
		dataType: "json",
		url: 'index.php?route=multiseller/transaction/jxSave&token=<?php echo $token; ?>',
		data: $('#form').serialize(),
		beforeSend: function() {
			button.hide().before('<span class="wait">&nbsp;<img src="view/image/loading.gif" alt="" /></span>');
			$('p.error').remove();
			$('.warning').text('').hide();
		},
		complete: function(jqXHR, textStatus) {
			button.show().prev('span.wait').remove();
		},
		error: function(jqXHR, textStatus, errorThrown) {
			$('.warning').text(textStatus).show();
		},			
		success: function(jsonData) {
			if (!jQuery.isEmptyObject(jsonData.errors)) {
				for (error in jsonData.errors) {
					$('[name="'+error+'"]').after('<p class="error">' + jsonData.errors[error] + '</p>');				
				}
			} else {
				window.location = 'index.php?route=multiseller/transaction&token=<?php echo $token; ?>';
			}
		}
	});
});
</script>
<?php echo $footer; ?> 