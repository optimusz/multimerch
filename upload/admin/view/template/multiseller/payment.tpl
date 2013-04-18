<?php echo $header; ?>
<div id="content" class="ms-payout">
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
      <h1><img src="view/image/multiseller/ms-dollar.png" alt="" /> <?php echo $ms_payment_heading; ?></h1>
      <div class="buttons">
      	<a id="ms-pay" class="button"><?php echo $ms_button_pay_masspay; ?></a>
      </div>
    </div>
    <div class="content">
      <style type="text/css">
      	.msBlack .ui-widget-header {
		    background: url("view/image/ui-bg_gloss-wave_35_000000_500x100.png") repeat-x scroll 50% 50% #F6A828;
		    border: 1px solid #000000;
      	}
      </style>
      <p><?php echo $ms_payment_payout_requests; ?>: <b><?php echo $payout_requests['amount_pending'];?></b> <?php echo strtolower($ms_payment_pending); ?> / <b><?php echo $payout_requests['amount_paid'];?></b> <?php echo strtolower($ms_payment_paid); ?></p>
      <p><?php echo $ms_payment_payouts; ?>: <b><?php echo $payouts['amount_pending'];?></b> <?php echo strtolower($ms_payment_pending); ?> / <b><?php echo $payouts['amount_paid'];?></b> <?php echo strtolower($ms_payment_paid); ?></p>
      <form action="" method="post" enctype="multipart/form-data" id="form">
        <table class="list" style="text-align: center">
          <thead>
            <tr>
              	<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
				<td style="width: 250px"><?php echo $ms_type; ?></td>
				<td><?php echo $ms_seller; ?></td>
				<td style="width: 50px"><?php echo $ms_amount; ?></td>
				<td><?php echo $ms_description; ?></td>
				<td style="width: 100px"><?php echo $ms_status; ?></td>
				<td style="width: 120px"><?php echo $ms_date_created; ?></td>
				<td style="width: 120px"><?php echo $ms_date_paid; ?></td>
				<td style="width: 120px"><?php echo $ms_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <?php if (isset($payments) && $payments) { ?>
            <?php $msPayment = new ReflectionClass('MsPayment'); ?>
            <?php foreach ($payments as $payment) { ?>
            <tr>
              <td style="text-align: center;">
                <input type="checkbox" name="selected[]" value="<?php echo $payment['payment_id']; ?>" />
              </td>
              <td><?php echo $this->language->get('ms_payment_type_' . $payment['payment_type']); ?></td>
              <td><a href="<?php echo $this->url->link('multiseller/seller/update', 'token=' . $this->session->data['token'] . '&seller_id=' . $payment['seller_id'], 'SSL');?>"><?php echo $payment['nickname']; ?></a></td>
              <td><?php echo $payment['amount_text']; ?></td>
              <td><?php echo $payment['description']; ?></td>
              <td>
              	<select name="ms-payment-status">
				<?php foreach ($msPayment->getConstants() as $cname => $cval) { ?>
					<?php if (strpos($cname, 'STATUS_') !== FALSE) { ?>
					<option value="<?php echo $cval; ?>" <?php if ($payment['payment_status'] == $cval) { ?>selected="selected"<?php } ?>><?php echo $this->language->get('ms_payment_status_' . $cval); ?></option>
					<?php } ?>
				<?php } ?>
				</select>
				<span class="ms-button-small ms-button-apply ms-button-status" title="Save" />
              </td>
              <td><?php echo $payment['date_created']; ?></td>
              <td><?php echo $payment['date_paid']; ?></td>
              <td class="right">
                <?php if ($payment['amount'] > 0 && $payment['payment_status'] == MsPayment::STATUS_UNPAID && in_array($payment['payment_type'], array(MsPayment::TYPE_PAYOUT, MsPayment::TYPE_PAYOUT_REQUEST))) { ?>
                <a class="ms-button ms-button-paypal" title="<?php echo $ms_payment_payout_paypal; ?>"></a>
                <?php } ?>
                <?php if ($payment['amount'] > 0 && $payment['payment_status'] == MsPayment::STATUS_UNPAID) { ?>
                <a class="ms-button ms-button-mark" title="<?php echo $ms_payment_mark; ?>"></a>
                <?php } ?>
                <a class="ms-button ms-button-delete" title="<?php echo $ms_payment_delete; ?>"></a>
              </td>
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
<script type="text/javascript">
$(document).ready(function() {
	$(".ms-button-status, .ms-button-mark").click(function() {
		var button = $(this);
		var row = button.parents('tr');
		var payment_id = row.children('td:first').find('input:checkbox').val();
		var payment_status = button.hasClass('ms-button-mark') ? '<?php echo MsPayment::STATUS_PAID; ?>' : button.prev('select').find('option:selected').val();
		button.hide().before(button.hasClass('ms-button-mark') ? '<a class="ms-button ms-loading" />' : '<a class="ms-button ms-button-small ms-loading" />');

		$.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=multiseller/payment/jxUpdateStatus&payment_id='+ payment_id +'&payment_status='+ payment_status +'&token=<?php echo $token; ?>',
			complete: function(jqXHR, textStatus) {
				button.show();
				row.find('.ms-loading').remove();
			},
			success: function(jsonData) {
				if (jsonData.payment) {
					if (jsonData.payment.payment_status == <?php echo MsPayment::STATUS_UNPAID; ?>) {
						if (row.children('td:last-child').find('.ms-button-mark').length == 0) row.children('td:last-child').prepend('<a class="ms-button ms-button-mark" title="<?php echo $ms_payment_mark; ?>"></a>');
						
						if (jsonData.payment.payment_type == <?php echo MsPayment::TYPE_PAYOUT; ?> || jsonData.payment.payment_type == <?php echo MsPayment::TYPE_PAYOUT_REQUEST; ?>)
							if (row.children('td:last-child').find('.ms-button-paypal').length == 0) row.children('td:last-child').prepend('<a class="ms-button ms-button-paypal" title="<?php echo $ms_payment_payout_paypal; ?>"></a>');

					} else if (jsonData.payment.payment_status == <?php echo MsPayment::STATUS_PAID; ?>) {
						row.children('td:last-child').find('.ms-button-mark, .ms-button-paypal').remove();
					}
					
					row.children('td:last-child').prev('td').html(jsonData.payment.payment_date);
					row.find('select[name="ms-payment-status"]').val(jsonData.payment.payment_status);
				}
				row.children('td').effect("highlight", {color: '#BBDF8D'}, 2000);
			}
		});
	});
	
	$(".ms-button-delete").click(function() {
		var payment_id = $(this).parents('tr').children('td:first').find('input:checkbox').val();
	    $.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=multiseller/payment/jxDelete&payment_id='+ payment_id +'&token=<?php echo $token; ?>',
			beforeSend: function() {
				$('.warning').text('').hide();
			},
			complete: function(jqXHR, textStatus) {
				window.location.reload();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				$('.warning').text(textStatus).show();
			},				
			success: function(jsonData) {
				window.location.reload();
			}
		});
	});	
	
	$("#ms-pay").click(function() {
		if ($('#form tbody input:checkbox:checked').length == 0)
			return;
			
	    $.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=multiseller/payment/jxConfirmPayment&token=<?php echo $token; ?>',
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
										url: 'index.php?route=multiseller/payment/jxCompletePayment&token=<?php echo $token; ?>',
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
</script>
<?php echo $footer; ?> 