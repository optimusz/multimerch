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
      <h1><img src="view/image/customer.png" alt="" /> <?php echo $ms_seller_heading; ?></h1>
      <div class="buttons"><a onclick="$('form').attr('action', '<?php echo $approve; ?>'); $('form').submit();" class="button"><?php echo $button_approve; ?></a><a onclick="$('form').attr('action', '<?php echo $delete; ?>'); $('form').submit();" class="button"><?php echo $button_delete; ?></a></div>
    </div>
    <div class="content">
      <form action="" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              	<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
				<td class="left"><a href="<?php echo $link_sort_name; ?>"><?php echo $ms_seller_name; ?></a></td>
				<td class="left"><a href="<?php echo $link_sort_nickname; ?>"><?php echo $ms_seller_display_name; ?></a></td>
				<td class="left"><a href="<?php echo $link_sort_email; ?>"><?php echo $ms_seller_email; ?></a></td>
				<td class="left"><a href="<?php echo $link_sort_total_products; ?>"><?php echo $ms_seller_total_products; ?></a></td>
				<td class="left"><a href="<?php echo $link_sort_total_sales; ?>"><?php echo $ms_seller_total_sales; ?></a></td>
				<td class="left"><a href="<?php echo $link_sort_total_earnings; ?>"><?php echo $ms_seller_total_earnings; ?></a></td>
				<td class="left"><a href="<?php echo $link_sort_current_balance; ?>"><?php echo $ms_seller_current_balance; ?></a></td>												
				<td class="left"><a href="<?php echo $link_sort_seller_status_id; ?>"><?php echo $ms_seller_status; ?></a></td>
				<td class="left"><a href="<?php echo $link_sort_date_created; ?>"><?php echo $ms_seller_date_created; ?></a></td>
				<td class="left"><?php echo $ms_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <?php if ($sellers) { ?>
            <?php foreach ($sellers as $seller) { ?>
            <tr>
              <td style="text-align: center;">
                <input type="checkbox" name="selected[]" value="<?php echo $seller['seller_id']; ?>" />
              </td>
              <td class="left"><?php echo $seller['name']; ?></td>
              <td class="left"><?php echo $seller['nickname']; ?></td>
              <td class="left"><?php echo $seller['email']; ?></td>
              <td class="center"><?php echo $seller['total_products']; ?></td>
              <td class="center"><?php echo $seller['total_sales']; ?></td>
              <td class="center"><?php echo $seller['total_earnings']; ?></td>
              <td class="center"><?php echo $seller['current_balance']; ?></td>
              <td class="center"><?php echo $seller['status']; ?></td>
              <td class="left"><?php echo $seller['date_created']; ?></td>
              <td class="right"><?php foreach ($seller['action'] as $action) { ?>
                [ <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a> ]
                <?php } ?></td>                            
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
});
//--></script>
<?php echo $footer; ?> 