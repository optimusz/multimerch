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
	
	<table class="list">
	<thead>
	<tr>
		<td class="left"><?php echo $ms_account_products_product; ?></td>
		<td class="center"><?php echo $ms_account_products_sales; ?></td>
		<td class="center"><?php echo $ms_account_products_status; ?></td>
		<td class="center"><?php echo $ms_account_products_date; ?></td>
		<td class="center" style="width: 150px"><?php echo $ms_account_products_action; ?></td>
	</tr>
	</thead>
	
	<tbody>
	<?php if (isset($products)) { ?>
	<?php foreach ($products  as $product) { ?>
	<tr>
		<td class="left"><?php echo $product['pd.name']; ?></td>
		<td class="center"><?php echo $product['mp.number_sold']; ?></td>
		<td class="center"><?php echo $product['status_text']; ?></td>
		<td class="center"><?php echo $product['p.date_created']; ?></td>
		<td class="right">
			<?php if (isset($product['view_link'])) { ?>
			<a href="<?php echo $product['view_link']; ?>" class="ms-button ms-button-view" title="<?php echo $ms_viewinstore; ?>"></a>
			<?php } ?>
			
			<?php if (isset($product['publish_link'])) { ?>
			<a href="<?php echo $product['publish_link']; ?>" class="ms-button ms-button-publish" title="<?php echo $ms_publish; ?>"></a>
			<?php } ?>
			
			<?php if (isset($product['unpublish_link'])) { ?>
			<a href="<?php echo $product['unpublish_link']; ?>" class="ms-button ms-button-unpublish" title="<?php echo $ms_unpublish; ?>"></a>
			<?php } ?>
			
			<?php if (isset($product['edit_link'])) { ?>
			<a href="<?php echo $product['edit_link']; ?>" class="ms-button ms-button-edit" title="<?php echo $ms_edit; ?>"></a>
			<?php } ?>
			
			<?php if (isset($product['delete_link'])) { ?>
			<a href="<?php echo $product['delete_link']; ?>" class="ms-button ms-button-delete" title="<?php echo $ms_delete; ?>"></a>
			<?php } ?>
		</td>
	</tr>
	<?php } ?>
	<?php } else { ?>
	<tr>
		<td class="center" colspan="6"><?php echo $ms_account_products_noproducts; ?></td>
	</tr>
	<?php } ?>
	</tbody>

	</table>

	<br />

	<div class="pagination"><?php echo $pagination; ?></div>

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
<?php echo $footer; ?>