<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>

<div id="content" class="ms-account-product">
	<?php echo $content_top; ?>
	
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>

	<h1><?php echo $ms_account_products_heading; ?></h1>

	<?php if (isset($error_warning) && ($error_warning)) { ?>
		<div class="warning"><?php echo $error_warning; ?></div>
	<?php } ?>
	
	<?php if (isset($success) && ($success)) { ?>
		<div class="success"><?php echo $success; ?></div>
	<?php } ?>
	
	<table class="list" id="list-products">
	<thead>
	<tr>
		<td><?php echo $ms_account_products_image; ?></td>
		<td><?php echo $ms_account_products_product; ?></td>
		<td><?php echo $ms_account_product_price; ?></td>
		<td><?php echo $ms_account_products_sales; ?></td>
		<td><?php echo $ms_account_products_earnings; ?></td>
		<td><?php echo $ms_account_products_status; ?></td>
		<td><?php echo $ms_account_products_date; ?></td>
		<td><?php echo $ms_account_products_listing_until; ?></td>
		<td class="large"><?php echo $ms_account_products_action; ?></td>
	</tr>
	</thead>
	<tbody></tbody>
	</table>

	<div class="buttons">
		<div class="left">
			<a href="<?php echo $link_back; ?>" class="button">
				<span><?php echo $button_back; ?></span>
			</a>
		</div>
		<div class="right">
			<a href="<?php echo $link_create_product; ?>" class="button">
				<span><?php echo $ms_create_product; ?></span>
			</a>
		</div>
	</div>

	<?php echo $content_bottom; ?>
</div>

<script>
	$(function() {
		$('#list-products').dataTable( {
			"sAjaxSource": $('base').attr('href') + "index.php?route=seller/account-product/getTableData",
			"aoColumns": [
				{ "mData": "image" },
				{ "mData": "product_name" },
				{ "mData": "product_price" },
				{ "mData": "number_sold" },
				{ "mData": "product_earnings" },
				{ "mData": "product_status" },
				{ "mData": "date_created" },
				{ "mData": "list_until" },
				{ "mData": "actions", "bSortable": false, "sClass": "right" }
			],
		});
	
		$(document).on('click', '.ms-button-delete', function() {
			if (!confirm('<?php echo $ms_account_products_confirmdelete; ?>')) return false;
		});
	});
</script>
<?php echo $footer; ?>