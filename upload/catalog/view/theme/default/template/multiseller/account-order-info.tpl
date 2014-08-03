<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>

<div id="content"><?php echo $content_top; ?>

	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>

	<h1><?php echo $heading_title; ?></h1>
	
	<table class="list">
		<thead>
			<tr>
				<td class="left" colspan="2"><?php echo $text_order_detail; ?></td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="left" style="width: 50%;"><?php if ($invoice_no) { ?>
					<b><?php echo $text_invoice_no; ?></b> <?php echo $invoice_no; ?><br />
					<?php } ?>
					<b><?php echo $text_order_id; ?></b> #<?php echo $order_id; ?><br />
					<b><?php echo $text_date_added; ?></b> <?php echo $date_added; ?></td>
				<td class="left" style="width: 50%;"><?php if ($payment_method) { ?>
					<b><?php echo $text_payment_method; ?></b> <?php echo $payment_method; ?><br />
					<?php } ?>
					<?php if ($shipping_method) { ?>
					<b><?php echo $text_shipping_method; ?></b> <?php echo $shipping_method; ?>
					<?php } ?></td>
			</tr>
		</tbody>
	</table>
	<table class="list">
		<thead>
			<tr>
				<td class="left"><?php echo $text_payment_address; ?></td>
				<?php if ($shipping_address) { ?>
				<td class="left"><?php echo $text_shipping_address; ?></td>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="left"><?php echo $payment_address; ?></td>
				<?php if ($shipping_address) { ?>
				<td class="left"><?php echo $shipping_address; ?></td>
				<?php } ?>
			</tr>
		</tbody>
	</table>
	<table class="list">
		<thead>
			<tr>
				<td class="left"><?php echo $column_name; ?></td>
				<td class="left"><?php echo $column_model; ?></td>
				<td class="right"><?php echo $column_quantity; ?></td>
				<td class="right"><?php echo $column_price; ?></td>
				<td class="right"><?php echo $column_total; ?></td>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($products as $product) { ?>
			<tr>
				<td class="left"><?php echo $product['name']; ?>
				<td class="left"><?php echo $product['model']; ?></td>
				<td class="right"><?php echo $product['quantity']; ?></td>
				<td class="right"><?php echo $product['price']; ?></td>
				<td class="right"><?php echo $product['total']; ?></td>
			</tr>
			<?php } ?>
		</tbody>
		<tfoot>
			<?php foreach ($totals as $total) { ?>
			<tr>
				<td colspan="3"></td>
				<td class="right"><b><?php echo $total['title']; ?>:</b></td>
				<td class="right"><?php echo $total['text']; ?></td>
			</tr>
			<?php } ?>
		</tfoot>
	</table>
	<table class="list">
		<tr>
			<td><?php echo $ms_account_orders_change_status ?></td>
			<td>
				<form method="POST" action="<?= $redirect ?>">
					<select name="order_status_edit">
							<?php foreach ($order_statuses as $order_statuses) { ?>
							 <?php if ($order_statuses['order_status_id'] == $order_status_id) { ?>
									<option value="<?php echo $order_statuses['order_status_id']; ?>" selected="selected"><?php echo $order_statuses['name']; ?></option>
								<?php } else { ?>
								 <option value="<?php echo $order_statuses['order_status_id']; ?>"><?php echo $order_statuses['name']; ?></option>
								<?php } ?>
							<?php } ?>
					</select>
						<button><?php echo $ms_button_submit; ?></button>
				</form>
		</td>
		</tr>
	</table>
	<?php echo $content_bottom; ?></div>
<?php echo $footer; ?> 