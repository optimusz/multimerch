<div class="box">
  <div class="box-heading"><?php echo $ms_newsellers_sellers; ?></div>
  <div class="box-content">
  	<?php if (isset($sellers) && !empty($sellers)) { ?>
    <div class="box-product">
    <?php foreach ($sellers as $seller) { ?>
    <div>
    	<div class="image"><a href="<?php echo $seller['href']; ?>"><img src="<?php echo $seller['image']; ?>" title="<?php echo $seller['nickname']; ?>" /></a></div>
    	<div class="name"><a href="<?php echo $seller['href']; ?>"><?php echo $seller['nickname']; ?></a></div>
	</div>
    <?php } ?>
    </div>
    <p style="text-align: right; margin: 0"><a href="<?php echo $sellers_href ?>"><?php echo $ms_newsellers_view; ?></a></p>
	<?php } else { ?>
		<p style="text-align: center; margin: 0"><?php echo $ms_catalog_sellers_empty; ?></p>
	<?php } ?>
  </div>
</div>
