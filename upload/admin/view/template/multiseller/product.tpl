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
      <h1><img src="view/image/customer.png" alt="" /> <?php echo $ms_catalog_products_heading; ?></h1>
      <div class="buttons">
      	<form id="bulk" method="post" enctype="multipart/form-data">
      	<!--
      	<select name="bulk_product_seller">
      		<option>--Bulk seller change--</option>
      		<?php for ($i = 1; $i<5; $i++) { ?>
      		<option value="<?php echo $i; ?>"><?php echo $this->MsLoader->MsProduct->getStatusText($i); ?></option>
      		<?php } ?>
      	</select>
      	-->      
      	<select name="bulk_product_status">
      		<option>--Bulk status change--</option>
      		<?php for ($i = 1; $i<5; $i++) { ?>
      		<option value="<?php echo $i; ?>"><?php echo $this->MsLoader->MsProduct->getStatusText($i); ?></option>
      		<?php } ?>
      	</select>
      	<input type="checkbox" name="bulk_mail" id="bulk_mail"><?php echo $ms_catalog_products_notify_sellers; ?></input>
      	<a class="ms-action button" id="ms-bulk-apply"><?php echo $ms_apply; ?></a>
      	</form>
	  </div>
    </div>
    <div class="content">
      <style type="text/css">
      	.msBlack .ui-widget-header {
		    background: url("view/image/ui-bg_gloss-wave_35_000000_500x100.png") repeat-x scroll 50% 50% #F6A828;
		    border: 1px solid #000000;
      	}
      </style>    
      <form action="" method="post" enctype="multipart/form-data" id="form">
        <table class="list" style="text-align: center">
          <thead>
            <tr>
              	<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              	<td><?php echo $ms_image; ?></td>
              	<td><?php echo $ms_product; ?></td>
				<td><?php echo $ms_seller; ?></td>
				<td><?php echo $ms_status; ?></td>
				<td><?php echo $ms_date_created; ?></td>
				<td><?php echo $ms_date_modified; ?></td>
				<td><?php echo $ms_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <?php if (isset($products) and $products) { ?>
            <?php foreach ($products as $product) { ?>
            <tr>
              <td style="text-align: center;">
                <input type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" />
              </td>
              <td><img src="<?php echo $product['p.image']; ?>" /></td>
              <td><?php echo $product['pd.name']; ?></td>
              <td>
              	<select>
              		<option value="0">--No seller--</option>
              		<?php foreach($sellers as $s) { ?>
              		<option value="<?php echo $s['seller_id']; ?>" <?php if ($s['seller_id'] == $product['seller_id']) { ?>selected="selected"<?php } ?>><?php echo $s['ms.nickname']; ?></option>
              		<?php } ?>
              	</select>
              	<span class="ms-assign-seller" style="background-image: url('view/image/success.png'); width: 16px; height: 16px; display: inline-block; cursor: pointer; vertical-align: middle" title="Save" />
              </td>
              <td>
              	<?php echo $this->MsLoader->MsProduct->getStatusText($product['mp.product_status']); ?>
              </td>
              <td><?php echo $product['p.date_created']; ?></td>
              <td><?php echo $product['p.date_modified']; ?></td>
              <td>
              	<?php foreach ($product['action'] as $action) { ?>
                [ <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a> ]
                <?php } ?>
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
<script type="text/javascript"><!--
$(document).ready(function() {
	$('#date').datepicker({dateFormat: 'yy-mm-dd'});
	
	$(".ms-assign-seller").click(function() {
		var button = $(this);
		var product_id = button.parents('tr').children('td:first').find('input:checkbox').val();
		var seller_id = button.prev('select').find('option:selected').val();
		$(this).hide().before('<img src="view/image/loading.gif" alt="" />');
	    $.ajax({
			type: "POST",
			//dataType: "json",
			url: 'index.php?route=multiseller/product/jxProductSeller&product_id='+ product_id +'&seller_id='+ seller_id +'&token=<?php echo $token; ?>',
			success: function(jsonData) {
				button.show().prev().remove();
				button.parents('td').effect("highlight", {color: '#BBDF8D'}, 2000);
			}
		});
	});
	
	$("#ms-bulk-apply").click(function() {
		if ($('#form tbody input:checkbox:checked').length == 0)
			return;
		
		if ($("#bulk_mail").is(":checked")) {
			$('<div />').html('<p>Optional note to the sellers:</p><textarea style="width:100%; height:70%" id="product_message" name="product_message"></textarea>').dialog({
				resizable: false,
				dialogClass: "msBlack",
				width: 600,
				height: 300,
				title: 'Change product status',
				modal: true,
				buttons: [
					{
	    				id: "button-submit",
	    				text: "Submit",
						click: function() {
							var data  = $('#form,#product_message,#bulk').serialize();
							var dialog = $(this);
							$('#button-submit').before('<p style="text-align: center"><img src="view/image/loading.gif" alt="" /></p>');
							$('#button-submit,#button-cancel').remove();
						    $.ajax({
								type: "POST",
								//async: false,
								dataType: "json",
								url: 'index.php?route=multiseller/product/jxProductStatus&token=<?php echo $token; ?>',
								data: data,
								success: function(jsonData) {
									window.location.reload();
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
		} else {
			var data  = $('#form,#product_message,#bulk').serialize();
			$('#ms-bulk-apply').before('<img src="view/image/loading.gif" alt="" />');
		    $.ajax({
				type: "POST",
				//async: false,
				dataType: "json",
				url: 'index.php?route=multiseller/product/jxProductStatus&token=<?php echo $token; ?>',
				data: data,
				success: function(jsonData) {
					window.location.reload();
				}
			});
		}
	});	
});
//--></script>
<?php echo $footer; ?> 