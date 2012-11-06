$(function() {
	$('#htabs a.lang').tabs();

	$("input[name='product_enable_shipping']").live('change', function() {
		if ($(this).val() == 1) {
			if (!$("input[name='product_quantity']").hasClass("ffUnchangeable")) {
				$("input[name='product_quantity']").parents("tr").show();
			}
		} else {
			if (!$("input[name='product_quantity']").hasClass("ffUnchangeable")) {
				$("input[name='product_quantity']").parents("tr").hide();
			}
		}
	});

	$("#product_image_files, #product_thumbnail_files").delegate(".ms-remove", "click", function() {
		$(this).parent().remove();
	});

	$("#product_download_files").delegate(".ms-button-delete", "click", function() {
		$(this).parents('.ms-download').remove();
		return false;
	});

	$("#ms-savedraft-button, #ms-submit-button").click(function() {
		var button = $(this);
		if ($(this).attr('id') == 'ms-savedraft-button') {
			var url = 'jxsaveproductdraft';
		} else {
			var url = 'jxsubmitproduct';
		}
		
	    $.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=seller/account-product/'+url,
			data: $(this).parents("form").serialize(),
		    beforeSend: function() {
		    	$('#ms-new-product a.button').hide();
		    	button.before('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		    },			
			success: function(jsonData) {
				$('.error').text('');
				if (!jQuery.isEmptyObject(jsonData.errors)) {
					$('#ms-new-product a.button').show();
					button.prev('span.wait').remove();
					for (error in jsonData.errors) {
					    if (!jsonData.errors.hasOwnProperty(error)) {
					        continue;
					    }
					    $('#error_'+error).text(jsonData.errors[error]);
					    window.scrollTo(0,0);
					    
					}				
				} else {
					console.log('success');
					location = jsonData['redirect'];
				}
	       	}
		});
	});
	
	/* uploadify buttons */
	$('#ms-file-addimages').each(function() {
    	var fileTag = $(this);
       	fileTag.uploadify({
			'hideButton'   : true,
			'buttonClass'  : 'ms-button-upload',
			'height': 25,
			//'debug' : true,
			'method'   : 'post',
			//'buttonImage' : 'catalog/view/theme/default/image/ms-update-30px.png',
			'formData'     : {
				'timestamp' : msGlobals.timestamp,
				'token'     : msGlobals.token,
				'session_id': msGlobals.session_id,
				'product_id': msGlobals.product_id
			},
			'swf'      : 'catalog/view/javascript/uploadify.swf',
			'uploader' : 'index.php?route=seller/account-product/jxUploadImages',
	        'onUploadStart' : function(file) {
	        	$('#ms-file-addimages').uploadify('settings','formData',{'imageCount':$('.ms-image').length});
	        },
	        'onSelect' : function(file) {
	            $('#error_product_image').html('');
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
					$('#error_product_image').append(errorText).hide().fadeIn(2000);
				}

				if (!$.isEmptyObject(data.files)) {
					for (var i = 0; i < data.files.length; i++) {
	        			$("#product_image_files").append(
	        			'<div class="ms-image">' +
	        			'<input type="hidden" value="'+data.files[i].name+'" name="product_images[]" />' +
	        			'<img src="'+data.files[i].thumb+'" />' +
	        			'<span class="ms-remove"></span>' +
	        			'</div>').children(':last').hide().fadeIn(2000);
					}
				}
				
				if (data.cancel) {
					$('#ms-file-addimages').uploadify('cancel','*');
					console.log('cancelling queue');
				}
	        },
	        'onUploadError' : function(file, errorCode, errorMsg, errorString) {
	        	//$('#error_product_image').append(errorString).hide().fadeIn(2000);
	        	console.log(errorCode + ' ' + errorMsg + ' ' + errorString);
	        }
		});
	});
	
	$('.ms-file-updatedownload').each(function() {
    	var fileTag = $(this);
    	var parentContainer = $(this).parents('.ms-download');
       	fileTag.uploadify({	
			'hideButton'   : true,
			'buttonClass'  : 'ms-button-update',
			'width': 30,
			'height': 30,
			//'debug' : true,
			'method'   : 'post',
			'wmode'      : 'transparent',
			'buttonImage' : 'catalog/view/theme/default/image/ms-update-30px.png',
			'formData'     : {
				'timestamp' : msGlobals.timestamp,
				'token'     : msGlobals.token,
				'session_id': msGlobals.session_id,
				'product_id': msGlobals.product_id
			},
			'swf'      : 'catalog/view/javascript/uploadify.swf',
			'uploader' : 'index.php?route=seller/account-product/jxUpdateFile',
	        'onUploadStart' : function(file) {
	        	fileTag.uploadify('settings','formData',{'file_id':fileTag.attr('id')});
	        },
	        'onSelect' : function(file) {
	            $('#error_product_download').html('');
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
					$('#error_product_download').append(errorText).hide().fadeIn(2000);
				}

				if (!$.isEmptyObject(data.fileName)) {
					parentContainer.find('.ms-download-name').text(data.fileMask);
					parentContainer.find('input:hidden[name$="[filename]"]').val(data.fileName);
					parentContainer.find('.ms-button-download').replaceWith('<span class="ms-button-download disabled"></span>');
					
					$("#push_downloads").parent('div').fadeIn(1000);
				}
	        },
	        'onUploadError' : function(file, errorCode, errorMsg, errorString) {
	        	//$('#error_product_download').append(errorString).hide().fadeIn(2000);
	        	console.log(errorCode + ' ' + errorMsg + ' ' + errorString);
	        }
		});
	});
	
	$('#ms-file-addfiles').each(function() {
    	var fileTag = $(this);
       	fileTag.uploadify({
			'hideButton'   : true,
			'buttonClass'  : 'ms-button-upload',
			'height': 25,
			//'debug' : true,
			'method'   : 'post',
			//'buttonImage' : 'catalog/view/theme/default/image/ms-update-30px.png',
			'formData'     : {
				'timestamp' : msGlobals.timestamp,
				'token'     : msGlobals.token,
				'session_id': msGlobals.session_id,
				'product_id': msGlobals.product_id
			},
			'swf'      : 'catalog/view/javascript/uploadify.swf',
			'uploader' : 'index.php?route=seller/account-product/jxUploadDownloads',
	        'onUploadStart' : function(file) {
	        	fileTag.uploadify('settings','formData',{'downloadCount':$('.ms-download').length});
	        },
	        'onSelect' : function(file) {
	            $('#error_product_download').html('');
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
						errorText += '<span>' + data.errors[i] + '</span><br />';
					}
					console.log(errorText);
					$('#error_product_download').append(errorText).children('span:last').hide().fadeIn(1000);
				}

				if (!$.isEmptyObject(data.files)) {
					var lastFileTag = $('#product_download_files .ms-download:last').find('input:hidden[name$="[filename]"]').attr('name');
					console.log(lastFileTag)
					if (typeof lastFileTag == "undefined") {
						var newFileNum = 0;
					} else {
						var newFileNum = parseInt(lastFileTag.match(/[0-9]+/)) + 1;
					}				
				
					for (var i = 0; i < data.files.length; i++) {
		        		var downloadTag = 
		        			'<div class="ms-download">' +
	          				'<input type="hidden" name="product_downloads[' + newFileNum + '][filename]" value="' + data.files[i].fileName + '" />' +
	          				(data.files[i].filePages ? '<input type="hidden" name="product_downloads[' + newFileNum + '][filePages]" value="' + data.files[i].filePages + '" />' : '') +
	          				'<span class="ms-download-name">'+data.files[i].fileMask+'</span>' +
	          				'<div class="ms-buttons">' +
	          				(data.files[i].filePages ? '<a href="index.php?route=seller/account-product/jxRenderPdfgenDialog" class="ms-button-pdf" title="'+msGlobals.button_generate+'"></a>' : '') +
	          				'<span class="ms-button-download disabled"></span>' +
	          				'<span class="ms-button-update disabled"></span>' +
		          			'<a class="ms-button-delete" title="'+msGlobals.text_delete+'"></a>' +
		          			'</div>' +
		          			'</div>';
		        		$("#product_download_files").append(downloadTag).children(':last').hide().fadeIn(1000);
					}
					
					$("#push_downloads").parent('div').fadeIn(1000);
				}
				
				if (data.cancel) {
					fileTag.uploadify('cancel','*');
					console.log('cancelling queue');
				}
	        },
	        'onUploadError' : function(file, errorCode, errorMsg, errorString) {
	        	console.log(errorCode + ' ' + errorMsg + ' ' + errorString);
	            //$('#error_product_download').append(errorString).hide().fadeIn(2000);
	        }
		});
	});
});
