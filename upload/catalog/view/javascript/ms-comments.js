$(function() {
	$(document).delegate("#tab-comments .links a", 'click', function() {
		var page = $(this).attr('href').match(/\d*$/);
		$('#tab-comments .pcComments').load($(this).attr('href'));
		return false;
	});

	$(document).delegate('#mc-submit:not(.disabled)', 'click', function() {
        if(typeof ms_comments_product_id !== 'undefined'){
            var url = 'index.php?route=module/ms-comments/submitComment&product_id='+ms_comments_product_id;
            var render_url = 'index.php?route=module/ms-comments/renderComments&product_id='+ms_comments_product_id;
        }else if(typeof ms_comments_seller_id !== 'undefined'){
            var url = 'index.php?route=module/ms-comments/submitComment&seller_id='+ms_comments_seller_id;
            var render_url = 'index.php?route=module/ms-comments/renderComments&seller_id='+ms_comments_seller_id;
        }
        
		$.ajax({
			type: "POST",
			dataType: "json",
			url: url,
			data: $('#pcForm').serialize(),
			beforeSend: function() {
				$('#tab-comments .success, #tab-comments .warning').remove();
				$('#mc-submit').addClass('disabled');
				$('#comment-title').after('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /> ' + ms_comments_wait + '</div>');
			},		
			complete: function() {
				$('#mc-submit').removeClass('disabled');
				$('#tab-comments .attention').remove();
			},
			success: function(jsonData) {
				if (!$.isEmptyObject(jsonData.errors)) {
					var errors = '';
					jQuery.each(jsonData.errors, function(index, item) {
					    errors += '<li>' + item + '</li>';
					});
					
					$('#comment-title').after('<div class="warning"><ul>' + errors + '</ul></div>');
				} else {
					$('#comment-title').after('<div class="success">' + jsonData.success + '</div>');
					$('#tab-comments input[type="text"]:not(:disabled), #tab-comments textarea:not(:disabled)').val('');
					$('#tab-comments .pcComments').load(render_url);
				}
			}
		});
	});
	
	$('#mc_text[maxlength]').keyup(function(){
		var limit = parseInt($(this).attr('maxlength'));
		if($(this).val().length > limit){
			$(this).val($(this).val().substr(0, limit));
		}
	});
});
