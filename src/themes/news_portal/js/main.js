(function ($, Drupal, drupalSettings) {

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

	// Show/hide sidebar
	$('.menu-toggle-btn').click(function(e){
		e.preventDefault();

		$('.sidebar').addClass('active');

		if( !$('.aside-overlay').length ) {
			$('body').append('<div class="aside-overlay"></div>');

			$('.aside-overlay').click(function() {
				e.preventDefault();

				$('.sidebar').removeClass('active');
				$('.aside-overlay').css('display', 'none');
			});
		} else {
			$('.aside-overlay').css('display', 'block');
		}
	});

	// Menu swipe
	$('body').on("swiperight",function(e){
		if ($('body').width() < 1366) {
			$('.sidebar').addClass('active');

			if( !$('.aside-overlay').length ) {
				$('body').append('<div class="aside-overlay"></div>');
				
				$('.aside-overlay').click(function() {
					e.preventDefault();

					$('.sidebar').removeClass('active');
					$('.aside-overlay').css('display', 'none');
				});
			} else {
				$('.aside-overlay').css('display', 'block');
			}
		}
	});

	$('body').on("swipeleft",function(e){
		$('.sidebar').removeClass('active');
		$('.aside-overlay').css('display', 'none');
	});

	// Share btns show/hide
	$('.share-toggle-btn').click(function(e) {
		e.preventDefault();

		$(this).parent().toggleClass('active');
	});

	// Slider init
  var slider_items = $('.slider .field__items');
  slider_items.each(function () {
    var self = $(this);
    if (self.find('.field__item').length > 1) {
      self.slick({
        dots: true,
        arrows: true
      });
    }
	});

  /* set required to a search term input in header */
  var sterm = $("#edit-search-term");
  $(sterm).attr('required',true);

})(jQuery, Drupal, drupalSettings);