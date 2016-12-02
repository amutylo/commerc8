$(function() {

	// show settings
	$('#categories-settings').click(function(e) {
		e.preventDefault();

		$(this).closest('.main-nav').toggleClass('settings-showed');
	});

	// Show mobile search
	$('#show-h-search').click(function(e) {
		e.preventDefault();

		$('header').addClass('search-show');
	});

	// Hide mobile search
	$('#hide-h-mobile').click(function(e) {
		e.preventDefault();

		$('header').removeClass('search-show');
	});


	 $.fancybox("#popup");

	// Show/hide sidebar
	$('body').click(function(e) {
		if ($(e.target).parent().hasClass('menu-toggle-btn')) {
			e.preventDefault();

			$('.sidebar').addClass('active');
		} else if ( $('.sidebar').has($(e.target)).length === 0 ) {
			$('.sidebar').removeClass('active');
		}
	});
});