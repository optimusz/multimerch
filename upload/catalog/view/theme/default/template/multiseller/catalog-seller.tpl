<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>

<div id="content" class="ms-catalog-seller">
	<?php echo $content_top; ?>
	
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
	
	<h1><?php echo $ms_catalog_sellers_heading; ?></h1>

	<?php if (isset($sellers) && $sellers) { ?>
		<div class="product-filter seller-filter">
			<div class="display"><b><?php echo $text_display; ?></b> <?php echo $text_list; ?> <b>/</b> <a onclick="display('grid');"><?php echo $text_grid; ?></a></div>
			
			<div class="limit"><b><?php echo $text_limit; ?></b>
				<select onchange="location = this.value;">
					<?php foreach ($limits as $limits) { ?>
					<option value="<?php echo $limits['href']; ?>" <?php if ($limits['value'] == $limit) { ?>selected="selected"<?php } ?>><?php echo $limits['text']; ?></option>
					<?php } ?>
				</select>
			</div>
			
			<div class="sort"><b><?php echo $text_sort; ?></b>
				<select onchange="location = this.value;">
					<?php foreach ($sorts as $sorts) { ?>
					<option value="<?php echo $sorts['href']; ?>" <?php if ($sorts['value'] == $sort . '-' . $order) { ?>selected="selected"<?php } ?>><?php echo $sorts['text']; ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		
		<div class="ms-sellerlist">
		<?php foreach ($sellers as $seller) { ?>
			<div class="seller-data">
			<div class="avatar-box">
			<p class="name"><a href="<?php echo $seller['href']; ?>"><?php echo $seller['nickname']; ?></a></p>
			<div class="image"><a href="<?php echo $seller['href']; ?>"><img src="<?php echo $seller['thumb']; ?>" title="<?php echo $seller['nickname']; ?>" alt="<?php echo $seller['nickname']; ?>" /></a></div>
			</div>
			<div class="info-box">
			<?php if ($seller['country']) { ?>
			<p class="country">
			<b><?php echo $ms_catalog_sellers_country; ?></b>
			<img class="country-flag" src="<?php echo $seller['country_flag']; ?>" alt="<?php echo $seller['country']; ?>" title="<?php echo $seller['country']; ?>" /> <span class="country-name"><?php echo $seller['country']; ?></span>
			</p>
			<?php } ?>
			
			<?php if ($seller['company']) { ?>
			<p class="company"><b><?php echo $ms_catalog_sellers_company; ?></b> <?php echo $seller['company']; ?></p>
			<?php } ?>
			
			<?php if ($seller['website']) { ?>
			<p class="website"><b><?php echo $ms_catalog_sellers_website; ?></b> <?php echo $seller['website']; ?></p>
			<?php } ?>
			
			<p class="totalsales"><b><?php echo $ms_catalog_sellers_totalsales; ?></b> <?php echo $seller['total_sales']; ?></p>
			<p class="totalproducts"><b><?php echo $ms_catalog_sellers_totalproducts; ?></b> <?php echo $seller['total_products']; ?></p>
			</div>
			<div class="seller-description"><?php echo $seller['description']; ?></div>
			</div>
		<?php } ?>
		</div>
		
		<div class="pagination"><?php echo $pagination; ?></div>
	<?php } else { ?>
		<div class="content"><?php echo $ms_catalog_sellers_empty; ?></div>
		<div class="buttons">
			<div class="right">
				<a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue; ?></a>
			</div>
		</div>
	<?php } ?>
	
	<?php echo $content_bottom; ?>
</div>

<script type="text/javascript"><!--
function display(view) {
	if (view == 'list') {
		$('.ms-sellergrid').attr('class', 'ms-sellerlist');
		
		$('.ms-sellerlist > div').each(function(index, element) {
			html = '<div class="avatar-box">';
				html += '<span class="name">' + $(element).find('.name').html() + '</span>';			
			
				var image = $(element).find('.image').html();
				if (image != null) { 
					html += '<div class="image">' + image + '</div>';
				}
			html += '</div>';

			html += '<div class="info-box">';
				var country = $(element).find('.country').html();
				if (country != null) { 
					html += '<p class="country">' + country + '</p>';
				}
			
				var company = $(element).find('.company').html();
				if (company != null) { 
					html += '<p class="company">' + company + '</p>';
				}
				
				var website = $(element).find('.website').html();
				if (website != null) { 
					html += '<p class="website">' + website + '</p>';
				}
				
				html += '<p class="totalsales">' + $(element).find('.totalsales').html() + '</p>';
				html += '<p class="totalproducts">' + $(element).find('.totalproducts').html() + '</p>';
			html += '</div>';

			html += '<div class="seller-description">' + $(element).find('.seller-description').html() + '</div>';
						
			$(element).html(html);
			$('.seller-description, .country-name').show();
			$('.country-flag').hide();
		});		
		
		$('.display').html('<b><?php echo $text_display; ?></b> <?php echo $text_list; ?> <b>/</b> <a onclick="display(\'grid\');"><?php echo $text_grid; ?></a>');
		
		<?php if (strcmp(VERSION,'1.5.5') >= 0) { ?> 
		$.totalStorage('display', 'list');
		<?php } else { ?>
		$.cookie('display', 'list');
		<?php } ?>
	} else {
		$('.ms-sellerlist').attr('class', 'ms-sellergrid');
		
		$('.ms-sellergrid > div').each(function(index, element) {
			html = '<div class="avatar-box">';
				html += '<span class="name">' + $(element).find('.name').html() + '</span>';			
			
				var image = $(element).find('.image').html();
				if (image != null) { 
					html += '<div class="image">' + image + '</div>';
				}
			html += '</div>';

			html += '<div class="info-box">';
				var country = $(element).find('.country').html();
				if (country != null) { 
					html += '<p class="country">' + country + '</p>';
				}
			
				var company = $(element).find('.company').html();
				if (company != null) { 
					html += '<p class="company">' + company + '</p>';
				}
				
				var website = $(element).find('.website').html();
				if (website != null) { 
					html += '<p class="website">' + website + '</p>';
				}
				
				html += '<p class="totalsales">' + $(element).find('.totalsales').html() + '</p>';
				html += '<p class="totalproducts">' + $(element).find('.totalproducts').html() + '</p>';				
			html += '</div>';

			html += '<div class="seller-description">' + $(element).find('.seller-description').html() + '</div>';
			
			$(element).html(html);
			$('.seller-description, .country-name').hide();
			$('.country-flag').show();
		});	
					
		$('.display').html('<b><?php echo $text_display; ?></b> <a onclick="display(\'list\');"><?php echo $text_list; ?></a> <b>/</b> <?php echo $text_grid; ?>');
		
		<?php if (strcmp(VERSION,'1.5.5') >= 0) { ?> 
		$.totalStorage('display', 'grid');
		<?php } else { ?>
		$.cookie('display', 'grid');
		<?php } ?>
	}
}

<?php if (strcmp(VERSION,'1.5.5') >= 0) { ?> 
view = $.totalStorage('display');
<?php } else { ?>
view = $.cookie('display');
<?php } ?>

if (view) {
	display(view);
} else {
	display('grid');
}
//--></script>

<?php echo $footer; ?>
