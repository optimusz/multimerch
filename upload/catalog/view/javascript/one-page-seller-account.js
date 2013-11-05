$(function() {
	$("#sellerinfo_avatar_files").delegate(".ms-remove", "click", function() {
		$(this).parent().remove();
	});

	var uploader = new plupload.Uploader({
		runtimes : 'gears,html5,flash,silverlight',
		//runtimes : 'flash',
		multi_selection:false,
		browse_button: 'ms-file-selleravatar',
		url: 'index.php?route=account/register-seller/jxUploadSellerAvatar',
		flash_swf_url: 'catalog/view/javascript/plupload/plupload.flash.swf',
		silverlight_xap_url : 'catalog/view/javascript/plupload/plupload.silverlight.xap',
		
	    multipart_params : {
			'timestamp' : msGlobals.timestamp,
			'token'     : msGlobals.token,
			'session_id': msGlobals.session_id
	    },
		
		filters : [
			//{title : "Image files", extensions : "png,jpg,jpeg"},
		],
		
		init : {
			FilesAdded: function(up, files) {
				$('#error_sellerinfo_avatar').html('');
				up.start();
			},
			
			FileUploaded: function(up, file, info) {
				try {
   					data = $.parseJSON(info.response);
				} catch(e) {
					data = []; data.errors = []; data.errors.push(msGlobals.uploadError);
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
						'<input type="hidden" value="'+data.files[i].name+'" name="seller_avatar_name" />' +
						'<img src="'+data.files[i].thumb+'" />' +
						'<span class="ms-remove"></span>' +
						'</div>').children(':last').hide().fadeIn(2000);
					}
				}
				
				up.stop();
			},
			
			Error: function(up, args) {
				$('#error_sellerinfo_avatar').append(msGlobals.uploadError).hide().fadeIn(2000);
				console.log('[error] ', args);
			}
		}
	}).init();
	
	$('.colorbox').colorbox({
		width: 640,
		height: 480
	});
	
	if (msGlobals.config_enable_rte == 1) {
		CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
		CKEDITOR.replaceClass = 'ckeditor';
	}
});
