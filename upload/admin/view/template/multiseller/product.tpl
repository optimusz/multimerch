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
  
  <?php $msProduct = new ReflectionClass('MsProduct'); ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/multiseller/ms-bag.png" alt="" /> <?php echo $ms_catalog_products_heading; ?></h1>
      <div class="buttons">
      	<form id="bulk" method="post" enctype="multipart/form-data">
      	<select name="bulk_product_status">
      		<option><?php echo $ms_catalog_products_bulk; ?></option>
			<?php foreach ($msProduct->getConstants() as $cname => $cval) { ?>
				<?php if (strpos($cname, 'STATUS_') !== FALSE) { ?>
					<option value="<?php echo $cval; ?>"><?php echo $this->language->get('ms_product_status_' . $cval); ?></option>
				<?php } ?>
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
        <table class="list" style="text-align: center" id="list-products">
          <thead>
            <tr>
              	<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              	<td><?php echo $ms_image; ?></td>
              	<td><?php echo $ms_product; ?></td>
				<td><?php echo $ms_seller; ?></td>
				<td class="medium"><?php echo $ms_status; ?></td>
				<td class="medium"><?php echo $ms_date_created; ?></td>
				<td class="medium"><?php echo $ms_date_modified; ?></td>
				<td class="medium"><?php echo $ms_action; ?></td>
            </tr>
			<tr class="filter">
				<td></td>
				<td></td>
				<td><input type="text"/></td>
				<td><input type="text"/></td>
				<td></td>
				<td><input type="text"/></td>
				<td><input type="text"/></td>
				<td></td>
			</tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$('#list-products').dataTable( {
		"sAjaxSource": "index.php?route=multiseller/product/getTableData&token=<?php echo $token; ?>",
		"aoColumns": [
			{ "mData": "checkbox", "bSortable": false },
			{ "mData": "image", "bSortable": false },
			{ "mData": "name" },
			{ "mData": "seller" },
			{ "mData": "status" },
			{ "mData": "date_created" },
			{ "mData": "date_modified" },
			{ "mData": "actions", "bSortable": false, "sClass": "right" }
		],
	});

	$('#date').datepicker({dateFormat: 'yy-mm-dd'});

	$(document).on( 'click', '.ms-assign-seller', function() {
		var button = $(this);
		var product_id = button.parents('tr').children('td:first').find('input:checkbox').val();
		var seller_id = button.prev('select').find('option:selected').val();
		$(this).hide().before('<img src="view/image/loading.gif" alt="" />');
		$.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=multiseller/product/jxProductSeller&product_id='+ product_id +'&seller_id='+ seller_id +'&token=<?php echo $token; ?>',
			success: function(jsonData) {
				button.show().prev().remove();
				button.parents('td').effect("highlight", {color: '#BBDF8D'}, 2000);
				if (jsonData.product_status) {
					button.parents('td').next('td').html(jsonData.product_status).effect("highlight", {color: '#BBDF8D'}, 2000);
				}
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
</script>
<?php echo $footer; ?> 