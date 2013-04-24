<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>

<div id="content" class="ms-account-order">
	<?php echo $content_top; ?>
	
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
	
	<h1><?php echo $ms_account_orders_heading; ?></h1>
	
	<table class="list">
		<thead>
			<tr>
				<td><?php echo $ms_account_orders_id; ?></td>
				<td><?php echo $ms_account_orders_customer; ?></td>
				<td style="width: 40%"><?php echo $ms_account_orders_products; ?></td>
				<td><?php echo $ms_date_created; ?></td>
				<td><?php echo $ms_account_orders_total; ?></td>
			</tr>
		</thead>
		
		<tbody>
		<?php if (isset($orders) && $orders) { ?>
			<?php foreach ($orders as $order) { ?>
			<tr>
				<td><?php echo $order['order_id']; ?></td>
				<td><?php echo $order['customer']; ?></td>
				<td class="left products">
				<?php foreach ($order['products'] as $p) { ?>
				<p>
					<span class="name"><?php if ($p['quantity'] > 1) { echo "{$p['quantity']} x "; } ?> <a href="<?php echo $this->url->link('product/product', 'product_id=' . $p['product_id'], 'SSL'); ?>"><?php echo $p['name']; ?></a></span>
					<span class="total"><?php echo $this->currency->format($p['seller_net_amt'], $this->config->get('config_currency')); ?></span>
				</p>
				<?php } ?>
				</td>
				<td><?php echo $order['date_created']; ?></td>
				<td><?php echo $order['total']; ?></td>
			</tr>
			<?php } ?>
		<?php } else { ?>
			<tr>
				<td class="center" colspan="5"><?php echo $ms_account_orders_noorders; ?></td>
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
	</div>
	
	<?php echo $content_bottom; ?>
</div>

<?php echo $footer; ?>