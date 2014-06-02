<?php if ($sandbox) { ?>
<div class="warning"><?php echo $ppa_sandbox; ?></div>
<?php } ?>
<div class="buttons">
	<div class="right">
		<input type="submit" value="<?php echo $button_confirm; ?>" class="button" id="button-confirm" />
	</div>
</div>

<script type="text/javascript"><!--
$('#button-confirm').bind('click', function() {
	$.ajax({
		type: 'POST',
		url: $('base').attr('href') . 'index.php?route=payment/ms_pp_adaptive/send',
		dataType: 'json',
		beforeSend: function() {
			$('#button-confirm').attr('disabled', true);
			$('.payment').before('<div class="alert attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /> <?php echo $ppa_wait; ?></div>');
		},
		success: function(json) {
			$('.attention, .warning').remove();
			$('#button-confirm').attr('disabled', false);
							
			if (json['error']) {
				$('.payment').before('<div class="alert warning">'+ json['error'] +'</div>');
				return;
			}
			
			if (json['redirect']) {
				location = json['redirect'];
			}
		}
	});
});
//--></script> 
