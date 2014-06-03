$(function() {
	$('.ms-sellercontact').live('click', function() {
		var url = this.href;
		var title = this.title;
	    var dialog = $("#ms-sellercontact-dialog-div");
	    if ($("#ms-sellercontact-dialog-div").length == 0) {
	        dialog = $('<div id="ms-sellercontact-dialog-div" style="display:hidden"></div>').appendTo('body');
		    dialog.load(
		    		url,
		            {},
		            function(responseText, textStatus, XMLHttpRequest) {
		                dialog.dialog({
		            		autoOpen: true,
		            		height: 560,
		            		width: 450,
		            		resizable: false,
		            		draggable: false,
		            		modal: true,
		            		title: title,
		            		dialogClass: 'ms-sellercontact-dialog ms-jquery-dialog',
		            		buttons: {
		            			"Send": function() {
		            				$.ajax({
		            					type: "POST",
		            					dataType: "json",
		            					url: $('base').attr('href') + 'index.php?route=seller/catalog-seller/jxSubmitContactDialog',
		            					data: $("#ms-sellercontact-dialog form").serialize(),
		            					beforeSend: function() {
		            						$('.ms-sellercontact-dialog .success,.ms-sellercontact-dialog .warning').remove();
		            						$('#ms-sellercontact-dialog button').attr('disabled', true);
		            						$('.ms-sellercontact-dialog .ui-dialog-buttonpane').before('<p class="attention" style="clear:both"><img src="catalog/view/theme/default/image/loading.gif" alt="" />Please wait...</div>');
		            					},		
		            					complete: function() {
		            						$('#ms-sellercontact-dialog button').attr('disabled', true);
		            						$('.ms-sellercontact-dialog .attention').remove();
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
		            							$('.ms-sellercontact-dialog .ui-dialog-buttonpane').before('<p class="warning" style="clear:both">' + errortext + '</div>');
		            						}
		            						
		            						if (data.success) {
		            							$('.ms-sellercontact-dialog .ui-dialog-buttonpane').before('<p class="success" style="clear:both">' + data.success + '</div>');
		            							$('.ms-sellercontact-dialog input[type="text"], .ms-sellercontact-dialog textarea').val('');
		            						}
		            					}
		            				});
		            			}
		            		}
		            	});
		            }
		        );	        
	    } else {
	    	dialog.dialog("open");
	    }
	    return false;
	});
});
