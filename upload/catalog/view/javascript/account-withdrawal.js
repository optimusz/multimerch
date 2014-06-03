$(function() {
	$('input[name="withdraw_all"]').change(function() {
		if ($(this).val() == 1) {
			$('input[name="withdraw_amount"]').attr('disabled','disabled');
		} else {
			$('input[name="withdraw_amount"]').removeAttr('disabled');
		}
	});
	
	$("#ms-submit-request").click(function() {
	    $.ajax({
			type: "POST",
			dataType: "json",
			url: $('base').attr('href') + 'index.php?route=seller/account-withdrawal/jxrequestmoney',
			data: $(this).parents("form").serialize(),
		    beforeSend: function() {
		    	$('#ms-withdrawal a.button').hide().before('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		    },
			success: function(jsonData) {
				$('.error').text('');
				if (!jQuery.isEmptyObject(jsonData.errors)) {
					$('#ms-withdrawal a.button').show().prev('span.wait').remove();
					for (error in jsonData.errors) {
					    if (!jsonData.errors.hasOwnProperty(error)) {
					        continue;
					    }
					    $('#error_'+error).text(jsonData.errors[error]);
					    window.scrollTo(0,0);
					    
					}				
				} else {
					location = jsonData['redirect'];
				}
	       	}
		});
	});
});
