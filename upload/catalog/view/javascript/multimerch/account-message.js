$(function() {
	$("#ms-message-reply").click(function() {
		$.ajax({
			type: "POST",
			dataType: "json",
			url: $('base').attr('href') + 'index.php?route=account/msmessage/jxSendMessage',
			data: $(this).parents("form").serialize(),
			beforeSend: function() {
				$('#ms-message-form a.button').hide().before('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			},
			success: function(jsonData) {
				$('.error').text('');
				if (!jQuery.isEmptyObject(jsonData.errors)) {
					$('#ms-message-form a.button').show().prev('span.wait').remove();
					for (error in jsonData.errors) {
						if (!jsonData.errors.hasOwnProperty(error)) {
							continue;
						}
						$('#error_text').text(jsonData.errors[error]);
						window.scrollTo(0,0);
					}
				} else {
					location = jsonData['redirect'];
				}
			}
		});
	});
	
	$("#ms-message-text").focus(function() {
		$(this).val('').unbind('focus');
	});
});
