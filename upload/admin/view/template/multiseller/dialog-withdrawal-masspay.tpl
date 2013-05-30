<p style="text-align: center"><b><?php echo $ms_payment_dialog_confirm; ?></b></p>
<table class="list">
  <thead>
    <tr>
		<td class="center"><?php echo $ms_seller; ?></a></td>
		<td class="center"><?php echo $ms_paypal; ?></a></td>
		<td class="center"><?php echo $ms_amount; ?></td>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($payments as $payment) { ?>
    <tr>
      <td class="center"><?php echo $payment['nickname']; ?></td>
      <td class="center"><?php echo $payment['paypal']; ?></td>
      <td class="center"><?php echo $payment['amount']; ?></td>
    </tr>
    <?php } ?>
    <tr>
		<td class="center" colspan="2">Total</td>
		<td class="center"><?php echo $total_amount; ?> <?php echo $ms_payment_dialog_ppfee; ?></td>
    </tr>    
  </tbody>
</table>
