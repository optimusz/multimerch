<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>

<div id="content"><?php echo $content_top; ?>

  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  
  <h1><?php echo $ms_account_products_heading; ?></h1>
  
  <?php if (isset($error_warning) && ($error_warning)) { ?>
  	<div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
	<table class="list">
	<thead>
		<tr>
			<td class="left"><?php echo $ms_account_products_product; ?></td>
			<td class="left"><?php echo $ms_account_products_sales; ?></td>
			<td class="left"><?php echo $ms_account_products_publish; ?></td>
			<td class="left"><?php echo $ms_account_products_status; ?></td>
			<td class="left"><?php echo $ms_account_products_date; ?></td>
			<td class="center"><?php echo $ms_account_products_action; ?></td>			
		</tr>
	</thead>
	<tbody>
		<?php if ($products) { ?>
		<?php foreach ($products  as $product) { ?>
		<tr>
			<td class="left"><?php echo $product['name']; ?></td>
			<td class="left"><?php echo $product['number_sold']; ?></td>
			<td class="left"><?php echo $product['status']; ?></td>
			<td class="left"><?php echo $product['review_status']; ?></td>
			<td class="left"><?php echo $product['date_added']; ?></td>
			<td class="center">
				<!--<a href="#">View</a>-->
				[ <a href="#"><?php echo $ms_account_products_action_edit; ?></a> ]
				[ <a href="#"><?php echo $ms_account_products_action_delete; ?></a> ]
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
    	<div class="right"><a href="<?php echo $continue; ?>" class="button"><span><?php echo $button_continue; ?></span></a></div>
    </div>
  
  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?>