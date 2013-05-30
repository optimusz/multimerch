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
  <?php if (isset($success) && $success) { ?>
  <div class="success"><?php echo $success; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/multiseller/ms-profile.png" alt="" /> <?php echo $ms_catalog_sellers_heading; ?></h1>
		<div class="buttons">
			<a onclick="location = '<?php echo $link_create_seller; ?>'" class="button"><?php echo $ms_catalog_sellers_create; ?></a>
		</div>
    </div>
    <div class="content">
		<?php echo $total_balance; ?><br /><br />
      <form action="" method="post" enctype="multipart/form-data" id="form">
        <table class="list" style="text-align: center">
          <thead>
            <tr>
              	<!--<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>-->
				<td style="width:220px"><?php echo $ms_seller; ?></td>
				<td><?php echo $ms_catalog_sellers_email; ?></td>
				<td><?php echo $ms_catalog_sellers_total_products; ?></td>
				<td><?php echo $ms_catalog_sellers_total_sales; ?></td>
				<td><?php echo $ms_catalog_sellers_total_earnings; ?></td>
				<td><?php echo $ms_catalog_sellers_current_balance; ?></td>												
				<td><?php echo $ms_catalog_sellers_status; ?></td>
				<td><?php echo $ms_catalog_sellers_date_created; ?></td>
				<td style="width: 120px"><?php echo $ms_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <?php if (isset($sellers) and $sellers) { ?>
            <?php foreach ($sellers as $seller) { ?>
            <tr>
              <!--
              <td style="text-align: center;">
                <input type="checkbox" name="selected[]" value="<?php echo $seller['seller_id']; ?>" />
              </td>
              -->
              <td>
              	<input type="hidden" value="<?php echo $seller['seller_id']; ?>" />
              	<a href="<?php echo $seller['customer_link']; ?>"><?php echo $seller['c.name'] . ' (' . $seller['ms.nickname'] . ')'; ?></a>
              </td>
              <td><?php echo $seller['c.email']; ?></td>
              <td><?php echo $seller['total_products']; ?></td>
              <td><?php echo $seller['total_sales']; ?></td>
              <td><?php echo $seller['earnings']; ?></td>
              <td><?php echo $seller['current_balance']; ?> / <?php echo $seller['available_balance']; ?></td>
              <td><?php echo $seller['status']; ?></td>
              <td><?php echo $seller['date_created']; ?></td>
              <td class="right">
                <?php if ($this->MsLoader->MsBalance->getSellerBalance($seller['seller_id']) - $this->MsLoader->MsBalance->getReservedSellerFunds($seller['seller_id']) > 0) { ?>
	                <?php if (!empty($seller['ms.paypal']) && filter_var($seller['ms.paypal'], FILTER_VALIDATE_EMAIL)) { ?>
	                	<a class="ms-button ms-button-paypal" title="<?php echo $ms_catalog_sellers_balance_paypal; ?>"></a>
	                <?php } else { ?>
	                	<a class="ms-button ms-button-paypal-bw" title="<?php echo $ms_payment_payout_paypal_invalid; ?>"></a>
	                <?php } ?>                
                <?php } ?>

                <a class="ms-button ms-button-edit" href="<?php echo $this->url->link('multiseller/seller/update', 'token=' . $this->session->data['token'] . '&seller_id=' . $seller['seller_id'], 'SSL'); ?>" title="<?php echo $text_edit; ?>"></a>
                <a class="ms-button ms-button-delete" href="<?php echo $this->url->link('multiseller/seller/delete', 'token=' . $this->session->data['token'] . '&seller_id=' . $seller['seller_id'], 'SSL'); ?>" title="<?php echo $button_delete; ?>"></a>
              </td>
            </tr>
            <?php } ?>
            <?php } else { ?>
            <tr>
              <td class="center" colspan="9"><?php echo $text_no_results; ?></td>
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
	$(".ms-button-paypal").click(function() {
		var button = $(this);
		var seller_id = button.parents('tr').children('td:first').find('input:hidden').val();
		$(this).hide().before('<a class="ms-button ms-loading" />');
		$.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=multiseller/seller/jxPayBalance&seller_id='+ seller_id +'&token=<?php echo $token; ?>',
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