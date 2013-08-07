$(function() {
	$.fn.dataTableExt.sErrMode = 'throw';

	if (typeof config_language != 'undefined') {
		$.extend($.fn.dataTable.defaults, {
			"oLanguage": {
				"sUrl": config_language
			}
		});
	}
	
	$.extend($.fn.dataTable.defaults, {
		"bProcessing": true,
		"bSortCellsTop": true,
		"bServerSide": true,
		"sPaginationType": "full_numbers",
		"aaSorting": [],
		"bAutoWidth": false,
		"bLengthChange": false,
		"sDom": 'rt<"pagination"pi><"clear">',
		"asStripeClasses": [],
		// todo insert proper value
		"iDisplayLength": 10
	});
	
	$("body").delegate(".dataTable .filter input[type='text']", "keyup",  function() {
		$(this).parents(".dataTable").dataTable().fnFilter(this.value, $(this).parents(".dataTable").find("thead tr.filter td").index($(this).parent("td")));
	});
});