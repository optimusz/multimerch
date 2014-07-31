$(function() {
	$('#language-tabs a.lang').tabs();
	$('#general-tabs a').tabs();
	$( ".product_image_files" ).sortable();

	$("body").delegate(".ms-price-dynamic", "propertychange input paste focusout", function(){
		$(".attention.ms-commission span").load($('base').attr('href') + "index.php?route=seller/account-product/jxGetFee&price=" + $(".ms-price-dynamic").val());
	});
	$(".attention.ms-commission span").load($('base').attr('href') + "index.php?route=seller/account-product/jxGetFee&price=" + $(".ms-price-dynamic").val());
	
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
		var lastRow = $(this).closest('table').find('tbody tr:last input:last').attr('name');
		if (typeof lastRow == "undefined") {
			var newRowNum = 1;
		} else {
			var newRowNum = parseInt(lastRow.match(/[0-9]+/)) + 1;
		}

		var newRow = $(this).closest('table').find('tbody tr.ffSample').clone();
		newRow.find('input,select').attr('name', function(i,name) {
			return name.replace('[0]','[' + newRowNum + ']');
		});
		
		$(this).closest('table').find('tbody').append(newRow.removeAttr('class'));
	});
	
	$("input[name='product_enable_shipping']").live('change', function() {
		if ($(this).val() == 1) {
            ProductShippingCategories(1)
			if (!$("input[name='product_quantity']").hasClass("ffUnchangeable")) {
				$("input[name='product_quantity']").parents("tr").show();
			}
			if (typeof msGlobals.downloadsLimitApplication != 'undefined') {
				if (msGlobals.downloadsLimit > 0 && msGlobals.downloadsLimitApplication == 1) {
					$("span[name='downloads_required']").hide();
				}
			}
		} else {
            ProductShippingCategories(0)
			if (!$("input[name='product_quantity']").hasClass("ffUnchangeable")) {
				$("input[name='product_quantity']").parents("tr").hide();
			}
			if (typeof msGlobals.downloadsLimitApplication != 'undefined') {
				if (msGlobals.downloadsLimit > 0 && msGlobals.downloadsLimitApplication == 1) {
					$("span[name='downloads_required']").show();
				}
			}
		}
	});


    function ProductShippingCategories(type)
    {
        var product_id  =   $('input[name=product_id]').val();

        $.ajax({
            type: "POST",
            url: $('base').attr('href') + 'index.php?route=seller/account-product/jxshippingcategories',
            data: {'type':type,'product_id':product_id},
            success: function(out)
            {
                $('#product_category_block').html(out);
            }
        });
    }

    // Manufacturer
    $('input[name=\'product_manufacturer\']').autocomplete({
        delay: 500,
        source: function(request, response) {
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: $('base').attr('href') + 'index.php?route=seller/account-product/jxautocomplete',
                data: {'type':'manufacturers', 'filter_name': encodeURIComponent(request.term)},
                success: function(json) {
                    response($.map(json, function(item) {
                        return {
                            label: item.name,
                            value: item.manufacturer_id
                        }
                    }));
                }
            });
        },
        select: function(event, ui) {
            $('input[name=\'product_manufacturer\']').attr('value', ui.item.label);
            $('input[name=\'product_manufacturer_id\']').attr('value', ui.item.value);

            return false;
        },
        focus: function(event, ui) {
            return false;
        }
    });

	$(".product_image_files").delegate(".ms-remove", "click", function() {
		$(this).parent().remove();
	});

	$(".product_download_files").delegate(".ms-button-delete", "click", function() {
		$(this).parents('.ms-download').remove();
		return false;
	});

	$("#ms-submit-button").click(function() {
		var button = $(this);
		var url = 'jxsubmitproduct';
		
		if (msGlobals.config_enable_rte == 1) {
			for (instance in CKEDITOR.instances) {
				CKEDITOR.instances[instance].updateElement();
			}
		}
		
		$.ajax({
			type: "POST",
			dataType: "json",
			url: $('base').attr('href') + 'index.php?route=seller/account-product/'+url,
			data: $("form#ms-new-product").serialize(),
			beforeSend: function() {
				button.hide().before('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			},
			complete: function(jqXHR, textStatus) {
				if (textStatus != 'success') {
					button.show().prev('span.wait').remove();
					$(".warning.main").text(msGlobals.formError).show();
					window.scrollTo(0,0);
				}
			},
			success: function(jsonData) {
				$('.error').text('');
				$('.warning.main').text('').hide();

				if (jsonData.fail) {
					$(".warning.main").text(msGlobals.formError).show();
					window.scrollTo(0,0);
				} else 	if (!jQuery.isEmptyObject(jsonData.errors)) {
					button.show().prev('span.wait').remove();
					for (error in jsonData.errors) {
						if (!jsonData.errors.hasOwnProperty(error)) {
							continue;
						}
						
						if ($('#error_'+error).length > 0)
							$('#error_'+error).text(jsonData.errors[error]);
						else
							$('[name^="'+error+'"]').nextAll('.error:first').text(jsonData.errors[error]);
					}
					$(".warning.main").text(msGlobals.formNotice).show();
					window.scrollTo(0,0);
				} else if (!jQuery.isEmptyObject(jsonData.data) && jsonData.data.amount) {
					$(".ms-payment-form form input[name='custom']").val(jsonData.data.custom);
					$(".ms-payment-form form input[name='amount']").val(jsonData.data.amount);
					$(".ms-payment-form form").submit();
				} else {
					location = jsonData['redirect'];
				}
		   	}
		});
	});
	
	var uploaderParams = {
		runtimes : 'html5,html4,flash,silverlight',
		flash_swf_url: 'catalog/view/javascript/plupload/plupload.flash.swf',
		silverlight_xap_url : 'catalog/view/javascript/plupload/plupload.silverlight.xap',		     
		
		multipart_params : {
			'timestamp' : msGlobals.timestamp,
			'token'	 : msGlobals.token,
			'session_id': msGlobals.session_id,
			'product_id': msGlobals.product_id
		},
		
		preinit : {
			UploadFile: function(up, file) {
				up.settings.multipart_params.fileCount = $('#' + up.id + " div").length;
			}
		},
		
		init: {
			StateChanged: function(up) {
				if (up.state == plupload.STOPPED) {
					$("."+up.id+".progress").fadeOut(500, function() { $(this).html("").hide(); });
				} else {
					$("."+up.id+".progress").show();
				}
			},
			
			UploadProgress: function(up, file) {
				$("#"+file.id).progressbar("value", file.percent);
				$("#"+file.id + ' div.label').text("Uploading " + file.name + ": " + file.percent + "%");
			},

			FilesAdded: function(up, files) {
				plupload.each(files, function(file) {
					$('<div id="'+file.id+'"><div class="label"></div></div>').appendTo("."+up.id+".progress").progressbar({
						value: 0
					});
				});
				
				$("."+up.id+".error").html('');
				up.start();
			},
			
			Error: function(up, args) {
				$("."+up.id+".error").append(msGlobals.uploadError).hide().fadeIn(2000);
			}
		}
	}
	
	new plupload.Uploader($.extend(true, uploaderParams, {
		browse_button: 'ms-file-addimages',
		url: $('base').attr('href') + 'index.php?route=seller/account-product/jxUploadImages',
		
		preinit : {
			Init: function(up, info) {
				$(".product_image_files").attr("id", up.id);
				$(".image.progress, #error_product_image").addClass(up.id);
			}
		},
		
		init : {
			FileUploaded: function(up, file, info) {
				$("#"+file.id).fadeOut(500, function() { $(this).progressbar("destroy"); $(this).remove(); });
				
				try {
					data = $.parseJSON(info.response);
				} catch(e) {
					data = []; data.errors = []; data.errors.push(msGlobals.uploadError);
				}

				if (!$.isEmptyObject(data.errors)) {
					var errorText = '';
					for (var i = 0; i < data.errors.length; i++) {
						errorText += '<p>' + file.name + ': ' + data.errors[i] + '</p>';
					}
					$("."+up.id+".error").append(errorText).fadeIn(1000);
				}

				if (!$.isEmptyObject(data.files)) {
					for (var i = 0; i < data.files.length; i++) {
						$(".product_image_files").append(
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
			}
		}
	})).init();
	
	new plupload.Uploader($.extend(true, uploaderParams, {
		browse_button: 'ms-file-addfiles',
		url: $('base').attr('href') + 'index.php?route=seller/account-product/jxUploadDownloads',
		
		preinit : {
			Init: function(up, info) {
				$(".product_download_files").attr("id", up.id);
				$(".download.progress, #error_product_download").addClass(up.id);
			}
		},
		
		init : {
			FileUploaded: function(up, file, info) {
				$("#"+file.id).fadeOut(500, function() { $(this).progressbar("destroy"); $(this).remove(); });
				
				try {
					data = $.parseJSON(info.response);
				} catch(e) {
					data = []; data.errors = []; data.errors.push(msGlobals.uploadError);
				}
				
				if (!$.isEmptyObject(data.errors)) {
					var errorText = '';
					for (var i = 0; i < data.errors.length; i++) {
						errorText += '<p>' + file.name + ': ' + data.errors[i] + '</p>';
					}
					$("."+up.id+".error").append(errorText).fadeIn(1000);
				}

				if (!$.isEmptyObject(data.files)) {
					var lastFileTag = $('.product_download_files .ms-download:last').find('input:hidden[name$="[filename]"]').attr('name');
					if (typeof lastFileTag == "undefined") {
						var newFileNum = 0;
					} else {
						var newFileNum = parseInt(lastFileTag.match(/[0-9]+/)) + 1;
					}				
					
					for (var i = 0; i < data.files.length; i++) {
						$(".product_download_files").append(
							'<div class="ms-download">' +
			  				'<input type="hidden" name="product_downloads[' + newFileNum + '][filename]" value="' + data.files[i].fileName + '" />' +
			  				(data.files[i].filePages ? '<input type="hidden" name="product_downloads[' + newFileNum + '][filePages]" value="' + data.files[i].filePages + '" />' : '') +
			  				'<span class="ms-download-name">'+data.files[i].fileMask+'</span>' +
			  				'<div class="ms-buttons">' +
			  				(data.files[i].filePages ? '<a href="' + $('base').attr('href') + 'index.php?route=module/multimerch_pdfgen/jxRenderPdfgenDialog" class="ms-button-pdf" title="'+msGlobals.button_generate+'"></a>' : '') +
			  				'<span class="ms-button-download disabled"></span>' +
			  				'<span class="ms-button-update disabled"></span>' +
				  			'</div>' +
				  			'</div>');
					}
					
					if (msGlobals.product_id.length > 0) {
						$("#push_downloads").parent('div').fadeIn(1000);
					}
				}
				
				if (data.cancel) {
					up.stop();
				}
			}
		}
	})).init();
	
	$('.ms-file-updatedownload').each(function() {
		var fileTag = $(this);
		var parentContainer = $(this).parents('.ms-download');
		new plupload.Uploader($.extend(true, uploaderParams, {
			browse_button: fileTag.attr('id'),
			url: $('base').attr('href') + 'index.php?route=seller/account-product/jxUpdateFile',

			preinit : {
				Init: function(up, info) {
					$(".product_download_files").attr("id", up.id);
					$(".download.progress, #error_product_download").addClass(up.id);
				},
	 
				UploadFile: function(up, file) {
					up.settings.multipart_params.file_id = fileTag.attr('id');
				}
			},
			
			init : {
				FileUploaded: function(up, file, info) {
					$("#"+file.id).fadeOut(500, function() { $(this).progressbar("destroy"); $(this).remove(); });
					
					try {
						data = $.parseJSON(info.response);
					} catch(e) {
						//console.log(info.response);
						data.errors.push(msGlobals.uploadError);
					}
					
					if (!$.isEmptyObject(data.errors)) {
						var errorText = '';
						for (var i = 0; i < data.errors.length; i++) {
							errorText += '<p>' + file.name + ': ' + data.errors[i] + '</p>';
						}
						$("."+up.id+".error").append(errorText).fadeIn(1000);
					}

					if (!$.isEmptyObject(data.fileName)) {
						parentContainer.find('.ms-download-name').text(data.fileMask);
						parentContainer.find('input:hidden[name$="[filename]"]').val(data.fileName);
						parentContainer.find('.ms-button-download').replaceWith('<span class="ms-button-download disabled"></span>');
						
						$("#push_downloads").parent('div').fadeIn(1000);
					}
				}
			}
		})).init();
	});
	
	if (msGlobals.config_enable_rte == 1) {
		CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
		CKEDITOR.replaceClass = 'ckeditor';
	}
});
