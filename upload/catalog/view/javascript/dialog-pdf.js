$(function() {
	$("#product_download_files").delegate(".ms-button-pdf", "click", function() {
		var url = this.href;
		var title = this.title;
	    var dialog = $("#ms-pdfgen-dialog-div");
	    var button = this;
	    if ($("#ms-pdfgen-dialog-div").length == 0) {
	        dialog = $('<div id="ms-pdfgen-dialog-div" style="display:hidden"></div>').appendTo('body');
	    } else {
	    	dialog.dialog("destroy");	    
	    }
	    
	    dialog.load(
    		url,
    	    {
    			fileName: $(button).parents('.ms-download').find('input:hidden[name$="[filename]"]').val(),
    			fileId: $(button).parents('.ms-download').find('input:hidden[name$="[download_id]"]').val()
    	    },
            function(responseText, textStatus, XMLHttpRequest) {
                dialog.dialog({
            		autoOpen: true,
            		height: 250,
            		width: 400,
            		resizable: false,
            		draggable: false,
            		modal: true,
            		title: title,
            		dialogClass: 'ms-pdfgen-dialog ms-jquery-dialog',
            		buttons: {
            			"Send": function() {
            				$.ajax({
            					type: "POST",
            					dataType: "json",
            					url: 'index.php?route=seller/account-product/jxSubmitPdfgenDialog',
            					data: $("#ms-pdfgen-dialog form").serialize(),		            					beforeSend: function() {
            						$('.ms-pdfgen-dialog .success, .ms-pdfgen-dialog .warning').remove();
            						$('#ms-pdfgen-dialog button').attr('disabled', true);
            						$('.ms-pdfgen-dialog .ui-dialog-buttonpane').before('<p class="attention" style="clear:both"><img src="catalog/view/theme/default/image/loading.gif" alt="" />Please wait...</div>');
            					},		
            					complete: function() {
            						$('#ms-pdfgen-dialog button').attr('disabled', true);
            						$('.ms-pdfgen-dialog .attention').remove();
            					},
            					success: function(data) {
            						var errortext = '';
            						if (!jQuery.isEmptyObject(data.errors)) {
            							for (error in data.errors) {
            							    if (!data.errors.hasOwnProperty(error)) {
            							        continue;
            							    }
            							    errortext += data.errors[error] + '<br />';
            							}
            							$('.ms-pdfgen-dialog .ui-dialog-buttonpane').before('<p class="warning" style="clear:both">' + errortext + '</div>');
            						} else {
	            						if (data.images) {
            								$('#product_image_files input[value^="'+data.token+'"]').parent().remove();
	            							for(var i=0; i<data.images.length; i++) {
	            								var imageTag = 
	            									'<div class="ms-image ms-pdf">' +
	            									'<input type="hidden" value="'+data.images[i].name+'" name="product_images[]" />' +
	            									'<img src="'+data.images[i].thumb+'" />' +
	            									'<span class="ms-remove"></span>' +
	            									'</div>';
	            							
	            								$("#product_image_files").append(imageTag).children(':last').hide().fadeIn(1000);
	            							}
	            						}
	            						$("html").scrollTop($("#product_image_files").scrollTop() + 100);
	            						dialog.dialog("close");
            						}
            					}
            				});
            			}
            		}
            	});
            }
	    );
	    return false;	
	});
});
