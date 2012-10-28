$(function() {
	$("#ms-submit-button").click(function() {
		$('.success').remove();	
		var id = $(this).attr('id');
	    $.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=seller/account-profile/jxsavesellerinfo',
			data: $(this).parents("form").serialize(),
		    beforeSend: function() {
		    	$('#ms-sellerinfo a.button').hide().before('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		    },			
			success: function(jsonData) {
				if (!jQuery.isEmptyObject(jsonData.errors)) {
					$('#ms-sellerinfo a.button').show().prev('span.wait').remove();				
					$('#error_'+id).text('');
					for (error in jsonData.errors) {
					    if (!jsonData.errors.hasOwnProperty(error)) {
					        continue;
					    }
					    
					    if ($('#error_'+error).length > 0) {
					    	$('#error_'+error).text(jsonData.errors[error]);
					    } else {
					    	$('#error_'+id).text(jsonData.errors[error]);
					   	}
					    //console.log(error + " -> " + jsonData.errors[error]);
					}
					window.scrollTo(0,0);
				} else {
					window.location.reload();
				}
	       	}
		});
	});
	
	$("#sellerinfo_avatar_files").delegate(".ms-remove", "click", function() {
		$(this).parent().remove();
	});	

	/* uploadify buttons */
	$('#ms-file-selleravatar').each(function() {
    	var fileTag = $(this);
       	fileTag.uploadify({
			'hideButton'   : true,
			'buttonClass'  : 'ms-button-upload',
			'height': 25,
			'debug' : true,
			'multi': false,
			'method'   : 'post',
			//'buttonImage' : 'catalog/view/theme/default/image/ms-update-30px.png',
			'formData'     : {
				'timestamp' : msGlobals.timestamp,
				'token'     : msGlobals.token,
				'session_id': msGlobals.session_id,
			},
			'swf'      : 'catalog/view/javascript/uploadify.swf',
			'uploader' : 'index.php?route=seller/account-profile/jxUploadSellerAvatar',
	        'onUploadStart' : function(file) {
	        },
	        'onSelect' : function(file) {
	            $('#error_sellerinfo_avatar').html('');
	        },	        
	        'onUploadSuccess' : function(file, data, response) {
				try {
   					data = $.parseJSON(data);
				} catch(e) {
					console.log('Invalid JSON response: ');
					console.log(data);
				}

				if (!$.isEmptyObject(data.errors)) {
					var errorText = '';
					for (var i = 0; i < data.errors.length; i++) {
						errorText += data.errors[i] + '<br />';
					}
					$('#error_sellerinfo_avatar').append(errorText).hide().fadeIn(2000);
				}

				if (!$.isEmptyObject(data.files)) {
					for (var i = 0; i < data.files.length; i++) {
						$("#sellerinfo_avatar_files").html(
	        			'<div class="ms-image">' +
	        			'<input type="hidden" value="'+data.files[i].name+'" name="sellerinfo_avatar_name" />' +
	        			'<img src="'+data.files[i].thumb+'" />' +
	        			'<span class="ms-remove"></span>' +
	        			'</div>').children(':last').hide().fadeIn(2000);
					}
				}
				
				if (data.cancel) {
					$('#ms-file-selleravatar').uploadify('cancel','*');
					console.log('cancelling queue');
				}
	        },
	        'onUploadError' : function(file, errorCode, errorMsg, errorString) {
	        	//$('#error_product_image').append(errorString).hide().fadeIn(2000);
	        	console.log(errorCode + ' ' + errorMsg + ' ' + errorString);
	        }
		});
	});
});
