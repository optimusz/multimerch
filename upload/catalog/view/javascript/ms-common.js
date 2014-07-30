function ms_addToCart(product_id, quantity) {
	quantity = typeof(quantity) != 'undefined' ? quantity : 1;

	$.ajax({
		url: 'index.php?route=checkout/cart/add',
		type: 'post',
		data: 'product_id=' + product_id + '&quantity=' + quantity,
		dataType: 'json',
		success: function(json) {
			$('.success, .warning, .attention, .information, .error').remove();

			if (json['redirect']) {
				location = json['redirect'];
			}

			if (typeof(json['error']) != "undefined") {
			if (json['error']['seller']) {
				$('#notification').html('<div class="warning" style="display: none;">' + json['error']['seller'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');

				$('.warning').fadeIn('slow');

				$('html, body').animate({ scrollTop: 0 }, 'slow');
			}
			}

			if (json['success']) {
				$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');

				$('.success').fadeIn('slow');

				$('#cart-total').html(json['total']);

				$('html, body').animate({ scrollTop: 0 }, 'slow');
			}
		}
	});
}
