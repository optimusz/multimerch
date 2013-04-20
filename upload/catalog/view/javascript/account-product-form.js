$(function() {
	$('#language-tabs a.lang').tabs();
	$('#general-tabs a').tabs();
	$( "#product_image_files" ).sortable();

	$("body").delegate(".ms-price-dynamic", "propertychange input paste focusout", function(){
		$(".attention.ms-commission span").load("index.php?route=seller/account-product/jxGetFee&price=" + $(".ms-price-dynamic").val());
	});	
	
	$("body").delegate(".date", "focusin", function(){
		$(this).datepicker({dateFormat: 'yy-mm-dd'});
	});

	$("body").delegate(".datetime", "focusin", function(){
		$(this).datetimepicker({
			dateFormat: 'yy-mm-dd',
			timeFormat: 'h:m'
		});
	});
	
	$("body").delegate(".time", "focusin", function(){
		$(this).timepicker({timeFormat: 'h:m'});
	});
	
	$('body').delegate("a.ms-button-delete", "click", function() {	
		$(this).parents('tr').remove();
	});
	
	$('body').delegate("a.ffClone", "click", function() {
		var lastRow = $(this).parents('table').find('tbody tr:last input:last').attr('name');
		if (typeof lastRow == "undefined") {
			var newRowNum = 1;
		} else {
			var newRowNum = parseInt(lastRow.match(/[0-9]+/)) + 1;
		}

		var newRow = $(this).parents('table').find('tbody tr.ffSample').clone();
		newRow.find('input,select').attr('name', function(i,name) {
			return name.replace('[0]','[' + newRowNum + ']');
		});
		
		$(this).parents('table').find('tbody').append(newRow.removeAttr('class'));
	});
	
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

	$("#ms-submit-button").click(function() {
		var button = $(this);
		var url = 'jxsubmitproduct';
		
		$.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=seller/account-product/'+url,
			data: $("form#ms-new-product").serialize(),
			beforeSend: function() {
				button.hide().before('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			},			
			success: function(jsonData) {
				$('.error').text('');
				if (!jQuery.isEmptyObject(jsonData.errors)) {
					button.show().prev('span.wait').remove();
					for (error in jsonData.errors) {
						if (!jsonData.errors.hasOwnProperty(error)) {
							continue;
						}
						
						if ($('#error_'+error).length > 0)
							$('#error_'+error).text(jsonData.errors[error]);
						else
							$('[name^="'+error+'"]').nextAll('.error:first').text(jsonData.errors[error]);
						
						window.scrollTo(0,0);
					}				
				} else if (!jQuery.isEmptyObject(jsonData.data) && jsonData.data.amount) {
					console.log(jsonData.data);
					$(".ms-payment-form form input[name='custom']").val(jsonData.data.custom);
					$(".ms-payment-form form input[name='amount']").val(jsonData.data.amount);
					$(".ms-payment-form form").submit();
				} else {
					location = jsonData['redirect'];
				}
		   	}
		});
	});
	
	new plupload.Uploader({
		runtimes : 'gears,html5,flash',
		//runtimes : 'flash',
		browse_button: 'ms-file-addimages',
		url: 'index.php?route=seller/account-product/jxUploadImages',
		flash_swf_url: 'catalog/view/javascript/plupload/plupload.flash.swf',
		silverlight_xap_url : 'catalog/view/javascript/plupload/plupload.silverlight.xap',
		
		multipart_params : {
			'timestamp' : msGlobals.timestamp,
			'token'	 : msGlobals.token,
			'session_id': msGlobals.session_id,
			'product_id': msGlobals.product_id
		},
		
		filters : [
			//{title : "Image files", extensions : "png,jpg,jpeg"},
		],
		
		preinit : {
			Init: function(up, info) {
			},
 
			UploadFile: function(up, file) {
				up.settings.multipart_params.imageCount = $('.ms-image').length;
			}
		},
		
		init : {
			FilesAdded: function(up, files) {
				$('#error_product_image').html('');
				up.start();
			},

			StateChanged: function(up) {
				if (up.state == plupload.STARTED) {
					$('<div class="ms-image progress"><div></div></div>').appendTo("#product_image_files").children("div").progressbar({
						value: 0
					});
				} else {
					// todo remove
					//$("#product_download_files").children(".progress:last").progressbar("destroy").remove();
				}
			},
			
			UploadProgress: function(up, file) {
				console.log(up.total.percent);
				$("#product_image_files").children(".progress:last").progressbar("value", up.total.percent);
			},	
			
			UploadProgress: function(up, file) {
				console.log(up.total.percent);
			},
			
			FileUploaded: function(up, file, info) {
				try {
   					data = $.parseJSON(info.response);
				} catch(e) {
					console.log('Invalid JSON response: ');
					console.log(info.response);
					$('#error_product_image').append(msGlobals.uploadError).hide().fadeIn(2000);
					return;
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
					up.stop();
				}
			},
			
			Error: function(up, args) {
				$('#error_product_image').append(msGlobals.uploadError).hide().fadeIn(2000);
				console.log('[error] ', args);
			}
		}
	}).init();
	
	new plupload.Uploader({
		runtimes : 'gears,html5,flash,silverlight',
		//runtimes : 'flash',
		browse_button: 'ms-file-addfiles',
		url: 'index.php?route=seller/account-product/jxUploadDownloads',
		flash_swf_url: 'catalog/view/javascript/plupload/plupload.flash.swf',
		silverlight_xap_url : 'catalog/view/javascript/plupload/plupload.silverlight.xap',
		
		multipart_params : {
			'timestamp' : msGlobals.timestamp,
			'token'	 : msGlobals.token,
			'session_id': msGlobals.session_id,
			'product_id': msGlobals.product_id
		},
		
		filters : [
			//{title : "Archives", extensions : "rar,zip"},
		],
		
		preinit : {
			Init: function(up, info) {
			},
 
			UploadFile: function(up, file) {
				up.settings.multipart_params.downloadCount = $('.ms-download').length;
			}
		},
		
		init : {
			FilesAdded: function(up, files) {
				$('#error_product_download').html('');
				up.start();
			},
			
			StateChanged: function(up) {
				if (up.state == plupload.STARTED) {
					$('<div class="ms-download progress"></div>').appendTo("#product_download_files").progressbar({
						value: 0
					});
				} else {
					// todo remove
					//$("#product_download_files").children(".progress:last").progressbar("destroy").remove();
				}
			},
			
			UploadProgress: function(up, file) {
				console.log(up.total.percent);
				$("#product_download_files").children(".progress:last").progressbar("value", up.total.percent);
			},			
			
			FileUploaded: function(up, file, info) {
				try {
   					data = $.parseJSON(info.response);
				} catch(e) {
					console.log('Invalid JSON response: ');
					console.log(info.response);
					$('#error_product_download').append(msGlobals.uploadError).hide().fadeIn(2000);
					return;
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
				  			//'<a class="ms-button-delete" title="'+msGlobals.text_delete+'"></a>' +
				  			'</div>' +
				  			'</div>';
						$("#product_download_files").append(downloadTag).children(':last').hide().fadeIn(1000);
					}
					
					if (msGlobals.product_id.length > 0) {
						$("#push_downloads").parent('div').fadeIn(1000);
					}
				}
				
				if (data.cancel) {
					up.stop();
				}
			},
			
			Error: function(up, args) {
				console.log('[error] ', args);
				$('#error_product_download').append(uploadError).children('span:last').hide().fadeIn(1000);
			}
		}
	}).init();	
	
	$('.ms-file-updatedownload').each(function() {
		var fileTag = $(this);
		var parentContainer = $(this).parents('.ms-download');
		new plupload.Uploader({
			runtimes : 'gears,html5,flash',
			//runtimes : 'flash',
			browse_button: fileTag.attr('id'),
			url: 'index.php?route=seller/account-product/jxUpdateFile',
			flash_swf_url: 'catalog/view/javascript/plupload/plupload.flash.swf',
			silverlight_xap_url : 'catalog/view/javascript/plupload/plupload.silverlight.xap',
			
			multipart_params : {
				'timestamp' : msGlobals.timestamp,
				'token'	 : msGlobals.token,
				'session_id': msGlobals.session_id,
				'product_id': msGlobals.product_id
			},
			
			filters : [
				//{title : "Archives", extensions : "zip,rar"},
			],
			
			preinit : {
				Init: function(up, info) {
				},
	 
				UploadFile: function(up, file) {
					up.settings.multipart_params.file_id = fileTag.attr('id');
				}
			},
			
			init : {
				FilesAdded: function(up, files) {
					$('#error_product_download').html('');
					up.start();
				},
				
				FileUploaded: function(up, file, info) {
					try {
	   					data = $.parseJSON(info.response);
					} catch(e) {
						console.log('Invalid JSON response: ');
						console.log(info.response);
						$('#error_product_download').append(msGlobals.uploadError).hide().fadeIn(2000);
						return;
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
				
				Error: function(up, args) {
					$('#error_product_download').append(msGlobals.uploadError).hide().fadeIn(2000);
					console.log('[error] ', args);
				}
			}
		}).init();
	});
});
