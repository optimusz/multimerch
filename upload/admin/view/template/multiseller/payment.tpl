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
  <?php if (isset($success) && $success) { ?>
  <div class="success"><?php echo $success; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/multiseller/ms-dollar.png" alt="" /> <?php echo $ms_payment_heading; ?></h1>
      <div class="buttons">
      	<a onclick="location = '<?php echo $this->url->link('multiseller/payment/create', 'token=' . $this->session->data['token'], 'SSL'); ?>'" class="button"><?php echo $ms_payment_new; ?></a>
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
	<table class="list" style="text-align: center" id="list-payments">
	<thead>
	<tr>
		<td class="tiny"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
		<td class="small"><?php echo $ms_type; ?></td>
		<td class="medium"><?php echo $ms_seller; ?></td>
		<td class="small"><?php echo $ms_amount; ?></td>
		<td><?php echo $ms_description; ?></td>
		<td class="medium"><?php echo $ms_status; ?></td>
		<td class="medium"><?php echo $ms_date_created; ?></td>
		<td class="medium"><?php echo $ms_date_paid; ?></td>
		<td class="medium"><?php echo $ms_action; ?></td>
	</tr>
	<tr class="filter">
		<td></td>
		<td></td>
		<td><input type="text"/></td>
		<td><input type="text"/></td>
		<td><input type="text"/></td>
		<td></td>
		<td><input type="text"/></td>
		<td><input type="text"/></td>
		<td></td>
	</tr>
	</thead>
	
	<tbody></tbody>
	</table>
	</form>
	</div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$('#list-payments').dataTable( {
		"sAjaxSource": "index.php?route=multiseller/payment/getTableData&token=<?php echo $token; ?>",
		"aoColumns": [
			{ "mData": "checkbox", "bSortable": false },
			{ "mData": "payment_type" },
			{ "mData": "seller" },
			{ "mData": "amount" },
			{ "mData": "description" },
			{ "mData": "payment_status" },
			{ "mData": "date_created" },
			{ "mData": "date_paid" },
			{ "mData": "actions", "bSortable": false, "sClass": "right" }
		],
        "aaSorting":  [[6,'desc']]
	});

	$(document).on('click', '.ms-button-status, .ms-button-mark', function() {
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
	
	$(document).on('click', '.ms-button-delete', function() {
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
	
	$(document).on('click', '.ms-button-paypal', function() {
		var button = $(this);
		var payment_id = button.parents('tr').children('td:first').find('input:checkbox').val();
		$(this).hide().before('<a class="ms-button ms-loading" />');
	    $.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=multiseller/payment/jxPay&payment_id='+ payment_id +'&token=<?php echo $token; ?>',
			complete: function(jqXHR, textStatus) {
				if (textStatus != 'success') {
					button.show().prev('.ms-loading').remove();
				}
			},
			success: function(jsonData) {
				if (jsonData.success) {
					$("<div style='display:none'>" + jsonData.form + "</div>").appendTo('body').children("form").submit();
				} else {
					button.show().prev('.ms-loading').remove();
				}
			}
		});
	});
});
</script>
<?php echo $footer; ?> 
