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
			<h1><img src="view/image/multiseller/ms-dollar.png" alt="" /> <?php echo $heading; ?></h1>
			<div class="buttons">
				<a id="ms-submit-button" class="button"><?php echo $button_save; ?></a>
				<a onclick="location = '<?php echo $this->url->link('multiseller/payment', 'token=' . $this->session->data['token']); ?>'" class="button"><?php echo $button_cancel; ?></a>
			</div>
		</div>
		<div class="content">
			<form method="post" enctype="multipart/form-data" id="form">
			<table class="form">
				<tr>
					<td><?php echo $ms_from; ?></td>
					<td>
					<select name="payment[from]">
						<optgroup label="<?php echo $ms_store; ?>">
							<option value="0"><?php echo $store_name; ?></option>
						</optgroup>
					
						<optgroup label="<?php echo $ms_seller; ?>">
							<?php foreach($sellers as $seller) { ?>
							<option value="<?php echo $seller['seller_id']; ?>"><?php echo $seller['name']; ?></option>
							<?php } ?>
						</optgroup>
					</select>
					</td>
				</tr>

				<tr>
					<td><?php echo $ms_to; ?></td>
					<td>
					<select name="payment[to]">
						<optgroup label="<?php echo $ms_store; ?>">
							<option value="0"><?php echo $store_name; ?></option>
						</optgroup>
					
						<optgroup label="<?php echo $ms_seller; ?>">
							<?php foreach($sellers as $seller) { ?>
							<option value="<?php echo $seller['seller_id']; ?>"><?php echo $seller['name']; ?></option>
							<?php } ?>
						</optgroup>
					</select>
					</td>
				</tr>

				<tr>
					<td><?php echo $ms_type; ?></td>
					<td>
					<select name="payment[type]">
						<?php foreach($payment_types as $type => $name) { ?>
						<option value="<?php echo $type; ?>"><?php echo $name; ?></option>
						<?php } ?>
					</select>
					</td>
				</tr>
				
				<tr>
					<td><?php echo $ms_amount; ?></td>
					<td>
						<?php echo $this->currency->getSymbolLeft(); ?>
						<input type="text" name="payment[amount]" size="5"></input>
						<?php echo $this->currency->getSymbolRight(); ?>
					</td>
				</tr>

				<tr>
					<td><?php echo $ms_payment_method; ?></td>
					<td>
					<select name="payment[method]">
						<option value="<?php echo MsPayment::METHOD_BALANCE; ?>"><?php echo $ms_payment_method_balance; ?></option>
						<option value="<?php echo MsPayment::METHOD_PAYPAL; ?>"><?php echo $ms_payment_method_paypal; ?></option>
					</select>
					</td>
				</tr>
				
				<tr>
					<td><?php echo $ms_payment_paid; ?></td>
					<td>
						<input type="checkbox" name="payment[paid]"></input>
					</td>
				</tr>

				<tr>
					<td><?php echo $ms_payment_deduct; ?></td>
					<td>
						<input type="checkbox" name="payment[deduct]"></input>
					</td>
				</tr>
				
				<tr>
					<td><?php echo $ms_description; ?></td>
					<td><textarea name="payment[description]" cols="40" rows="5"></textarea></td>
				</tr>
			</table>
			</form>
		</div>
	</div>
</div>

<script>
$("select[name='payment[from]'], select[name='payment[to]']").change(function() {
	var other = ($(this).attr('name') == 'payment[from]') ? "select[name='payment[to]']" : "select[name='payment[from]']";

	if ($(this).val() == 0) {
		$(other).find('option[value="0"]').parents('select').find("option[value!='0']:first").attr('selected',true);
	} else {
		$(other).find('option[value="0"]').attr("selected",true);
	}
	
})

$("select[name='payment[from]']").change();

$("#ms-submit-button").click(function() {
	var button = $(this);
	$.ajax({
		type: "POST",
		dataType: "json",
		url: 'index.php?route=multiseller/payment/jxSave&token=<?php echo $token; ?>',
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
				window.location = 'index.php?route=multiseller/payment&token=<?php echo $token; ?>';
			}
		}
	});
});
</script>
<?php echo $footer; ?> 