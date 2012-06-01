<p style="text-align: center"><b>Following withdrawal requests will be marked as paid</b></p>
<table class="list">
  <thead>
    <tr>
		<td class="center"><?php echo $ms_seller; ?></a></td>
		<td class="center"><?php echo $ms_amount; ?></td>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($payments as $payment) { ?>
    <tr>
      <td class="center"><?php echo $payment['nickname']; ?></td>
      <td class="center"><?php echo $payment['amount']; ?></td>
    </tr>
    <?php } ?>
  </tbody>
</table>
