$(function() {
	// load options tab
	$('#tab-options').load($('base').attr('href') + 'index.php?route=seller/account-product/jxRenderOptions&product_id=' + msGlobals.product_id, function(data){
		// load existing product options
		if (msGlobals.product_id.length > 0) {
			$.get($('base').attr('href') + 'index.php?route=seller/account-product/jxRenderProductOptions&product_id=' + msGlobals.product_id, function(data) {
				$('div.options').append(data).find('input[name$="[option_id]"]').each(function(index) {
					$(this).closest('.ms-options').find('.select_option option[value="'+ $(this).val() + '"]').attr('disabled', true );
				});
			});
		}
	});
	
	// option handlers
	// delete
	$('body').delegate(".option_delete", "click", function() {
		var option_id = $(this).closest('.option').find('input[name$="[option_id]"]').val();
		$(this).closest('.ms-options').find('.select_option option[value="'+ option_id + '"]').attr('disabled', false);
		$(this).closest('.option').remove();
	});
	
	// add
	$('body').delegate(".select_option", "change", function() {
		$(this).children(':selected').attr('disabled', 'disabled');
		var option_id = $(this).children(':selected').val();
		var select = this;
		
		$.get($('base').attr('href') + 'index.php?route=seller/account-product/jxRenderOptionValues&option_id=' + option_id, function(data) {
			var lastRow = $(select).parents('.ms-options').find('.option:last input:last').attr('name');

			if (typeof lastRow == "undefined") {
				var newRowNum = 1;
			} else {
				var newRowNum = parseInt(lastRow.match(/[0-9]+/g).shift()) + 1;
			}
			
			var data = $(data);
			data.find('input,select').attr('name', function(i,name) {
				if (name) return name.replace('product_option[0]','product_option[' + newRowNum + ']');
			});
			$('div.options').append(data);
		});
		$(this).val(0);
	});


	// value handlers
	// add
	$('body').delegate("select.select_option_value", "change", function() {
		$(this).children(':selected').attr('disabled', 'disabled');
		var newVal = $(this).closest('.o-content').find('.option_value.ffSample').mmClone();
		newVal.find('.option_name').text($(this).children(':selected').text());
		newVal.find('input[name$="[option_value_id]"]').val($(this).children(':selected').val());
		$(this).val(0);
	});

	$.fn.mmClone = function() {
		var lastRow = $(this).closest('div.mmCtr').find('div:last input:last').attr('name');

		if (typeof lastRow == "undefined") {
			var newRowNum = 1;
		} else {
			var newRowNum = parseInt(lastRow.match(/[0-9]+/g).pop()) + 1;
		}

		var newRow = $(this).clone();
		newRow.find('input,select').attr('name', function(i,name) {
			return name.replace('[product_option_value][0]','[product_option_value][' + newRowNum + ']');
		});

		$(this).closest('div.mmCtr').append(newRow.removeClass('ffSample'));

		return newRow;
	}	
	
	// change prefix
	$('body').delegate(".option_price_prefix", "click", function() {
		$(this).toggleClass('plus minus');
		var prefix = $(this).closest('.option_value').find('input[name$="[price_prefix]"]');
		prefix.val(prefix.val() === "-" ? "+" : "-");
	});

	// change required
	$('body').delegate(".option_required", "click", function() {
		$(this).toggleClass('bw');
		var required = $(this).closest('.option').find('input[name$="[required]"]');
		required.val(required.val() === "1" ? "0" : "1");
	});
	
	// delete
	$('body').delegate(".option_value_delete", "click", function() {
		var option_value_id = $(this).closest('.option_value').find('input[name$="[option_value_id]"]').val();
		$(this).closest('.option').find('.select_option_value option[value="'+ option_value_id + '"]').attr('disabled', false);
		$(this).closest('.option_value').remove();
	});	
});