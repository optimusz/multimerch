<div class="box">
	<div class="box-heading"><?php echo $ms_carousel_sellers; ?></div>
	<div class="box-content">
		<?php if (isset($sellers) && !empty($sellers)) { ?>
		<div id="carousel<?php echo $module; ?>" class="ms-carousel">
			<ul class="jcarousel-skin-opencart">
				<?php foreach ($sellers as $seller) { ?>
				<li>
					<div class="image"><a href="<?php echo $seller['href']; ?>"><img src="<?php echo $seller['image']; ?>" title="<?php echo $seller['nickname']; ?>" /></a></div>
					<div class="name"><a href="<?php echo $seller['href']; ?>"><?php echo $seller['nickname']; ?></a></div>
				</li>
				<?php } ?>
			</ul>
			<p style="text-align: right; margin: 0"><a href="<?php echo $sellers_href ?>"><?php echo $ms_carousel_view; ?></a></p>		  
		</div>
		<?php } else { ?>
			<p style="text-align: center; margin: 0"><?php echo $ms_catalog_sellers_empty; ?></p>
		<?php } ?>
	</div>
</div>

<script type="text/javascript">
$('#carousel<?php echo $module; ?> ul').jcarousel({
	vertical: false,
	visible: <?php echo $limit; ?>,
	scroll: <?php echo $scroll; ?>
});
/</script>