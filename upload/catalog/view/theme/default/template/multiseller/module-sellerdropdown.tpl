<div class="box">
	<div class="box-heading"><?php echo $ms_sellerdropdown_sellers; ?></div>
	<div class="box-content">
		<?php if (isset($sellers) && !empty($sellers)) { ?>
			<select onchange="if (this.value) window.location.href=this.value">
				<option><?php echo $ms_sellerdropdown_select; ?></option>
				<?php foreach ($sellers as $seller) { ?>
				<option value="<?php echo $seller['href']; ?>"><?php echo $seller['nickname']; ?></option>
				<?php } ?>
			</select>
		<?php } else { ?>
			<p style="text-align: center; margin: 0"><?php echo $ms_catalog_sellers_empty; ?></p>
		<?php } ?>
	</div>
</div>
