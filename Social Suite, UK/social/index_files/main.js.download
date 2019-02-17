$(document).ready(function() {
	/* Submit disable after 1 click */
	$('[type=submit][name=submit]').on('click', function() {
		$(this).addClass('disabled');
	});

	/* Confirm delete handler */
	$('body').on('click', '[data-confirm]', function(){
		var message = $(this).attr('data-confirm');
		if(confirm(message) == false) return false;
	});


    /* Enable tooltips everywhere */
    $('[data-toggle="tooltip"]').tooltip()

});

