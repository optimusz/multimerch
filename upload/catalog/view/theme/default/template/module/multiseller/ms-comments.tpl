<script>
$(function() {
	$("#pcSubmitBtn").click(function() {
	    $.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=module/ms-comments/submitComment&product_id=<?php echo $product_id; ?>',
			data: 'pcEmail=' + encodeURIComponent($('input[name=\'pcEmail\']').val()) + '&pcName=' + encodeURIComponent($('input[name=\'pcName\']').val()) + '&pcText=' + encodeURIComponent($('textarea[name=\'pcText\']').val()) + '&captcha=' + encodeURIComponent($('input[name=\'pcCaptcha\']').val()),
			beforeSend: function() {
				$('.success, .warning').remove();
				$('#pcSubmitBtn').attr('disabled', true);
				$('#comment-title').after('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /> <?php echo $ms_comments_wait; ?></div>');
			},		
			complete: function() {
				$('#pcSubmitBtn').attr('disabled', false);
				$('.attention').remove();
			},
			success: function(data) {
				if (data.error) {
					$('#comment-title').after('<div class="warning">' + data.error + '</div>');
				}
				
				if (data.success) {
					$('#comment-title').after('<div class="success">' + data.success + '</div>');
					
					if ($('#pcCaptcha').length == 0) {
						$('textarea[name=\'pcText\']').val('');
					} else {
						$('textarea[name=\'pcText\']').val('');
						$('input[name=\'pcCaptcha\']').val('');
						var d = new Date();
						$('#pcCaptcha').attr("src", "index.php?route=product/product/captcha&"+d.getTime());
					}		
					$('#pcComments').load('index.php?route=module/ms-comments/loadComments&product_id=<?php echo $product_id; ?>');
				}
			}
		});
	});
	
    $('#pcText[maxlength]').keyup(function(){
        var limit = parseInt($(this).attr('maxlength'));
        var text = $(this).val();
        var chars = text.length;
        if(chars > limit){
            var new_text = text.substr(0, limit);
            $(this).val(new_text);
        }
  
    });  	
});
</script>

<h2 id="comment-title"><?php echo $ms_comments_post_comment; ?></h2>
<b><?php echo $ms_comments_name; ?></b><br />
	<input type="text" name="pcName" value="<?php echo $pcName; ?>" /><br /><br />
<b><?php echo $ms_comments_email; ?></b><br />
	<input type="text" name="pcEmail" value="<?php echo $pcEmail; ?>" /><br /> <br /> <br />
<b><?php echo $ms_comments_comment; ?></b>
	<textarea name="pcText" cols="40" rows="8" style="width: 98%;" <?php if ($msconf_comments_maxlen > 0) echo "maxlength='$msconf_comments_maxlen'"?>></textarea>
	
<span style="font-size: 11px;"><?php echo $ms_comments_note; ?></span><br /> <br />

<?php if (!$pcLogged) { ?>
	<b><?php echo $entry_captcha; ?></b><br />
	<input type="text" name="pcCaptcha" value="" />
	<br />
	<img src="index.php?route=product/product/captcha" alt="" id="pcCaptcha" /><br />
	<br />
<?php } ?>

<div class="buttons">
	<div class="right"><a id="pcSubmitBtn" class="button"><span><?php echo $button_continue; ?></span></a></div>
</div>

<div id="pcComments">
<?php if (!empty($pcComments)) {
	foreach ($pcComments as $q) { ?>
	<div class="content">
		<div class="comment-header">
	    	<span class="comment-name"><?php echo htmlspecialchars($q['name']); ?></span>
	    	<span class="comment-date"><?php echo date('d/m/Y',$q['create_time']); ?></span>
	    </div>
	    <div class="comment-content">
		    <?php echo nl2br($q['comment']); ?>
	    </div>
	</div>
	<?php }
} else { ?>
	<div class="content"><?php echo $ms_comments_no_comments_yet; ?></div>
<?php } ?>
</div>