<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>

<div id="content" class="ms-catalog-seller-products">
	<?php echo $content_top; ?>
	
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
	
	<div class="ms-sellerprofile">
		<div class="seller-data">
			<div class="avatar-box">
				<a style="text-decoration: none" href="<?php echo $seller['href']; ?>"><h2><?php echo $ms_catalog_seller_products; ?></h2></a>
				<a href="<?php echo $seller['href']; ?>"><img src="<?php echo $seller['thumb']; ?>" /></a>
			</div>
			
			<div class="info-box">
				<?php if ($seller['country']) { ?>
					<p><b><?php echo $ms_catalog_seller_profile_country; ?></b> <?php echo $seller['country']; ?></p>
				<?php } ?>
				
				<?php if ($seller['company']) { ?>
					<p><b><?php echo $ms_catalog_seller_profile_company; ?></b> <?php echo $seller['company']; ?></p>
				<?php } ?>
				
				<?php if ($seller['website']) { ?>
					<p><b><?php echo $ms_catalog_seller_profile_website; ?></b> <?php echo $seller['website']; ?></p>
				<?php } ?>
				
				<p><b><?php echo $ms_catalog_seller_profile_totalsales; ?></b> <?php echo $seller['total_sales']; ?></p>
				<p><b><?php echo $ms_catalog_seller_profile_totalproducts; ?></b> <?php echo $seller['total_products']; ?></p>
			</div>
		</div>
	</div>
	
	<?php if ($seller['products']) { ?>
		<div class="product-filter" style="margin-top: 20px">
			<div class="display"><b><?php echo $text_display; ?></b> <?php echo $text_list; ?> <b>/</b> <a onclick="display('grid');"><?php echo $text_grid; ?></a></div>

			<div class="limit"><?php echo $text_limit; ?>
				<select onchange="location = this.value;">
				<?php foreach ($limits as $limits) { ?>
				<option value="<?php echo $limits['href']; ?>" <?php if ($limits['value'] == $limit) { ?>selected="selected"<?php } ?>><?php echo $limits['text']; ?></option>
				<?php } ?>
				</select>
			</div>
		
			<div class="sort">
				<?php echo $text_sort; ?>
				<select onchange="location = this.value;">
					<?php foreach ($sorts as $sorts) { ?>
					<option value="<?php echo $sorts['href']; ?>" <?php if ($sorts['value'] == $sort . '-' . $order) { ?>selected="selected"<?php } ?>><?php echo $sorts['text']; ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		
		<div class="product-list">
			<?php foreach ($seller['products'] as $product) { ?>
			<div>
				<?php if ($product['thumb']) { ?>
				<div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" /></a></div>
				<?php } ?>
				
				<div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
				
				<div class="description"><?php echo $product['description']; ?></div>
				
				<?php if ($product['price']) { ?>
				<div class="price">
					<?php if (!$product['special']) { ?>
						<?php echo $product['price']; ?>
					<?php } else { ?>
						<span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>
					<?php } ?>
					<?php if ($product['tax']) { ?>
						<br />
						<span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
					<?php } ?>
				</div>
				<?php } ?>
				
				<?php if ($product['rating']) { ?>
					<div class="rating"><img src="catalog/view/theme/default/image/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></div>
				<?php } ?>
				
				<div class="cart"><input type="button" value="<?php echo $button_cart; ?>" onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button" /></div>
				<div class="wishlist"><a onclick="addToWishList('<?php echo $product['product_id']; ?>');"><?php echo $button_wishlist; ?></a></div>
				<div class="compare"><a onclick="addToCompare('<?php echo $product['product_id']; ?>');"><?php echo $button_compare; ?></a></div>
			</div>
			<?php } ?>
		</div>
		
		<div class="pagination"><?php echo $pagination; ?></div>
	<?php } else { ?>
		<div class="content"><?php echo $ms_catalog_seller_products_empty; ?></div>
	<?php }?>
	
	<?php echo $content_bottom; ?>
</div>

<script type="text/javascript"><!--
$('#content input[name=\'filter_name\']').keydown(function(e) {
	if (e.keyCode == 13) {
		$('#button-search').trigger('click');
	}
});

$('#button-search').bind('click', function() {
	url = $('base').attr('href') + 'index.php?route=product/search';
	
	var filter_name = $('#content input[name=\'filter_name\']').attr('value');
	
	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}

	var filter_category_id = $('#content select[name=\'filter_category_id\']').attr('value');
	
	if (filter_category_id > 0) {
		url += '&filter_category_id=' + encodeURIComponent(filter_category_id);
	}
	
	var filter_sub_category = $('#content input[name=\'filter_sub_category\']:checked').attr('value');
	
	if (filter_sub_category) {
		url += '&filter_sub_category=true';
	}
		
	var filter_description = $('#content input[name=\'filter_description\']:checked').attr('value');
	
	if (filter_description) {
		url += '&filter_description=true';
	}

	location = url;
});

function display(view) {
	if (view == 'list') {
		$('.product-grid').attr('class', 'product-list');
		
		$('.product-list > div').each(function(index, element) {
			html= '<div class="right">';
			html += '<div class="cart">' + $(element).find('.cart').html() + '</div>';
			html += '<div class="wishlist">' + $(element).find('.wishlist').html() + '</div>';
			html += '<div class="compare">' + $(element).find('.compare').html() + '</div>';
			html += '</div>';			
			
			html += '<div class="left">';
			
			var image = $(element).find('.image').html();
			
			if (image != null) { 
				html += '<div class="image">' + image + '</div>';
			}
			
			var price = $(element).find('.price').html();
			
			if (price != null) {
				html += '<div class="price">' + price+ '</div>';
			}
						
			html += '<div class="name">' + $(element).find('.name').html() + '</div>';
			html += '<div class="description">' + $(element).find('.description').html() + '</div>';
			
			var rating = $(element).find('.rating').html();
			
			if (rating != null) {
				html += '<div class="rating">' + rating + '</div>';
			}
				
			html += '</div>';

						
			$(element).html(html);
		});		
		
		$('.display').html('<b><?php echo $text_display; ?></b> <?php echo $text_list; ?> <b>/</b> <a onclick="display(\'grid\');"><?php echo $text_grid; ?></a>');
		
		<?php if (strcmp(VERSION,'1.5.5') >= 0) { ?> 
		$.totalStorage('display', 'list');
		<?php } else { ?>
		$.cookie('display', 'list');
		<?php } ?>
	} else {
		$('.product-list').attr('class', 'product-grid');
		
		$('.product-grid > div').each(function(index, element) {
			html = '';
			
			var image = $(element).find('.image').html();
			
			if (image != null) {
				html += '<div class="image">' + image + '</div>';
			}
			
			html += '<div class="name">' + $(element).find('.name').html() + '</div>';
			html += '<div class="description">' + $(element).find('.description').html() + '</div>';
			
			var price = $(element).find('.price').html();
			
			if (price != null) {
				html += '<div class="price">' + price+ '</div>';
			}	
					
			var rating = $(element).find('.rating').html();
			
			if (rating != null) {
				html += '<div class="rating">' + rating + '</div>';
			}
						
			html += '<div class="cart">' + $(element).find('.cart').html() + '</div>';
			html += '<div class="wishlist">' + $(element).find('.wishlist').html() + '</div>';
			html += '<div class="compare">' + $(element).find('.compare').html() + '</div>';
			
			$(element).html(html);
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
	display('list');
}
//--></script> 
<?php echo $footer; ?>