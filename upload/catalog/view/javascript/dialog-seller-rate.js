$(function() {
	$('.ms-seller-rate').live('click', function() {
		var url = this.href;
		var title = this.title;
	    var dialog = $("#ms-seller-rate-dialog-div");
	    if ($("#ms-seller-rate-dialog-div").length == 0) {
	        dialog = $('<div id="ms-seller-rate-dialog-div" style="display:hidden"></div>').appendTo('body');
		    dialog.load(
		    		url,
		            {},
		            function(responseText, textStatus, XMLHttpRequest) {
		                dialog.dialog({
		            		autoOpen: true,
		            		height: 500,
		            		width: 480,
		            		resizable: false,
		            		draggable: false,
		            		modal: true,
		            		title: title,
		            		dialogClass: 'ms-seller-rate-dialog ms-jquery-dialog',
		            		buttons: {
		            			"Rate": function() {
		            				$.ajax({
		            					type: "POST",
		            					dataType: "json",
		            					url: 'index.php?route=seller/catalog-seller/jxSubmitRateDialog',
		            					data: $("#ms-seller-rate-dialog form").serialize(),
		            					beforeSend: function() {
		            						$('.ms-seller-rate-dialog .success,.ms-seller-rate-dialog .warning').remove();
		            						$('#ms-seller-rate-dialog button').attr('disabled', true);
		            						$('.ms-seller-rate-dialog .ui-dialog-buttonpane').before('<p class="attention" style="clear:both"><img src="catalog/view/theme/default/image/loading.gif" alt="" />Please wait...</div>');
		            					},		
		            					complete: function() {
		            						$('#ms-seller-rate-dialog button').attr('disabled', true);
		            						$('.ms-seller-rate-dialog .attention').remove();
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
		            							$('.ms-seller-rate-dialog .ui-dialog-buttonpane').before('<p class="warning" style="clear:both">' + errortext + '</div>');
		            						}
		            						
		            						if (data.success) {
												$('.ms-seller-rate-dialog .attention').remove();
		            							$('.ms-seller-rate-dialog .ui-dialog-buttonpane').before('<p class="success" style="clear:both">' + data.success + '</div>');
		            							$('.ms-seller-rate-dialog textarea').val('');
												$('.ms-seller-rate-dialog hidden').val('');
												$('.ms-seller-rate-dialog input[type="radio"]').prop('checked', false);
												jQuery('#ms-seller-rate-dialog-div').dialog('close');
												location = data.redirect;
		            						}
		            					}
		            				});
		            			}
		            		},
							close: function( event, ui ) {
								$('.ms-seller-rate-dialog textarea').val('');
								$('.ms-seller-rate-dialog hidden').val('');
								$('.ms-seller-rate-dialog input[type="radio"]').prop('checked', false);
								$('.ms-seller-rate-dialog .attention').remove();
								$('.ms-seller-rate-dialog .success').remove();
								$('.ms-seller-rate-dialog .warning').remove();
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
