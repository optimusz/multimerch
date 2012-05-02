<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <?php if ($success) { ?>
  <div class="success"><?php echo $success; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/customer.png" alt="" /> <?php echo $ms_finances_withdrawals_heading; ?></h1>
      <div class="buttons"><a id="ms-pay" class="button"><?php echo $ms_button_pay; ?></a></div>
    </div>
    <div class="content">
      <style type="text/css">
      	.msBlack .ui-widget-header {
		    background: url("view/javascript/jquery/ui/themes/ui-lightness/images/ui-bg_gloss-wave_35_000000_500x100.png") repeat-x scroll 50% 50% #F6A828;
		    border: 1px solid #000000;
      	}
      </style>
      <form action="" method="post" enctype="multipart/form-data" id="form">
        <table class="list" style="text-align: center">
          <thead>
            <tr>
              	<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
				<td><?php echo $ms_seller; ?></a></td>
				<td><?php echo $ms_amount; ?></a></td>
				<td><?php echo $ms_date_created; ?></a></td>
				<td><?php echo $ms_status; ?></a></td>				
				<td><?php echo $ms_processed_by; ?></a></td>
				<td><?php echo $ms_date_processed; ?></a></td>
            </tr>
          </thead>
          <tbody>
            <?php if (isset($requests) && $requests) { ?>
            <?php foreach ($requests as $request) { ?>
            <tr>
              <td style="text-align: center;">
                <input type="checkbox" name="selected[]" <?php if($request['date_processed'] !== NULL) { ?>  <?php } ?> value="<?php echo $request['request_id']; ?>" />
              </td>
              <td><?php echo $request['seller']; ?></td>
              <td><?php echo $request['amount']; ?></td>
              <td><?php echo $request['date_created']; ?></td>
              <td><?php echo $request['status']; ?></td>
              <td><?php echo $request['processed_by']; ?></td>
              <td><?php echo $request['date_processed']; ?></td>
            </tr>
            <?php } ?>
            <?php } else { ?>
            <tr>
              <td class="center" colspan="10"><?php echo $text_no_results; ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </form>
      <div class="pagination"><?php echo $pagination; ?></div>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
$(document).ready(function() {
	$('#date').datepicker({dateFormat: 'yy-mm-dd'});
	$("#ms-pay").click(function() {
		if ($('#form tbody input:checkbox:checked').length == 0)
			return;
			
	    $.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=module/multiseller/jxConfirmPayment&token=<?php echo $token; ?>',
			data: $('#form').serialize(),
			success: function(jsonData) {
				if (jsonData.error) {
				    alert(jsonData.error);
				} else {
					console.log('success');
					$('<div />').html(jsonData.html).dialog({
						dialogClass: "msBlack",
						resizable: false,
						width: 600,
						title: 'Payment Confirmation',
						modal: true,
						buttons: [
							{
	            				id: "button-pay",
	            				text: "Pay!",
								click: function() {
									var dialog = $(this);
									$('#button-pay').remove();
									$('#button-cancel').attr('disabled','disabled');									
									dialog.html('<p style="text-align: center"><img src="view/image/loading.gif" alt="" /></p>');
								    $.ajax({
										type: "POST",
										dataType: "json",
										url: 'index.php?route=module/multiseller/jxCompletePayment&token=<?php echo $token; ?>',
										data: $('#form').serialize(),
										success: function(jsonData) {
											$('#button-pay').remove();
											$('#button-cancel').removeAttr('disabled').find("span").html("OK");
											
											if (!jQuery.isEmptyObject(jsonData.error)) {
												dialog.html('<p class="warning">'+jsonData.error+'</p>');
												if (!jQuery.isEmptyObject(jsonData.response)) {
													dialog.append('<p class="warning">'+jsonData.response+'</p>');											
												}
												dialog.children('.ui-dialog-buttonset button:first').remove();
											} else {
												dialog.html('<p class="success">'+jsonData.success+'</p>');
												$('#button-cancel').unbind('click').click(function() {
													dialog.dialog("close");
													window.location.reload();
												});												
											}
										}
									});
								}
							},
							{
	            				id: "button-cancel",
	            				text: "Cancel",
								click: function() {
									$(this).dialog("close");
								}
							}
						]
					});
				}
	       	}
		});
	});
});
//--></script>
<?php echo $footer; ?> 