<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>

<div id="content" class="ms-catalog-seller-profile">
	<?php echo $content_top; ?>

	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
			<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>

	<div class="ms-sellerprofile">
		<div class="seller-data">
			<div class="avatar-box">
				<h2><?php echo $seller['nickname']; ?></h2>
				<img src="<?php echo $seller['thumb']; ?>" />

				<br />
				<?php foreach($seller['badges'] as $badge) { ?>
					<img src="<?php echo $badge['image']; ?>" title="<?php echo $badge['description']; ?>" />
				<?php } ?>
			</div>

			<div class="info-box">
				<?php
				if ($total_votes % 10 == 1) {
					$ms_rating_word = $ms_catalog_seller_profile_ratings_singular;
				} else {
					$ms_rating_word = $ms_catalog_seller_profile_ratings_plural;
				}
				?>

				<p><b><?php echo $ms_catalog_seller_profile_rating_overall; ?></b> <?php echo $avg_overall; ?> (<?php echo $total_votes . " " . $ms_rating_word; ?>)</p>
				<p><b><?php echo $ms_catalog_seller_profile_rating_communication; ?></b> <?php echo $avg_communication; ?></p>
				<p><b><?php echo $ms_catalog_seller_profile_rating_honesty; ?></b> <?php echo $avg_honesty; ?></p>
			</div>
		</div>
	</div>

	<div class="ms-seller-ratings">
	<?php if ($ms_ratings) { ?>
		<?php foreach ($ms_ratings as $rate) { ?>
			<div class="content">
				<div class="comment-column-right">
					<table class="comment-ratings">
						<tr><td><b><?php echo $ms_catalog_seller_profile_rating_overall; ?></b></td><td><?php echo $rate['rating_overall']; ?></td></tr>
						<tr><td><b><?php echo $ms_catalog_seller_profile_rating_communication; ?></b></td><td><?php echo $rate['rating_communication']; ?></td></tr>
						<tr><td><b><?php echo $ms_catalog_seller_profile_rating_honesty; ?></b></td><td><?php echo $rate['rating_honesty']; ?></td></tr>
					</table>
				</div>
				<div class="comment-column-left">
					<div class="comment-header">
						<span class="comment-name"><b><?php echo $rate['evaluator_name']; ?></b></span>
					</div>
					<div class="comment-content"><?php echo $rate['comment']; ?></div>
				</div>
			</div>
		<?php } ?>
		<div class="pagination"><?php echo $pagination; ?></div>
	<?php } else { ?>
	<div class="content"><?php echo $ms_catalog_seller_profile_rating_not_defined; ?></div>
	<?php } ?>
	</div>

	<?php echo $content_bottom; ?>
</div>
<?php echo $footer; ?>
