(function ( $ ) {
 
$.fn.stickySidebar = function( options ) {
 
var config = $.extend({
headerSelector: 'header',
navSelector: 'nav',
contentSelector: '#content',
footerSelector: 'footer',
sidebarTopMargin: 20,
footerThreshold: 40
}, options);
 
var fixSidebr = function() {
 
		var sidebarSelector = $(this);
		var viewportHeight = $(window).height();
		var viewportWidth = $(window).width();
		var documentHeight = $(document).height();
		var headerHeight = $(config.headerSelector).outerHeight();
		
		var navHeight = $(config.navSelector).outerHeight();
		var sidebarHeight = sidebarSelector.outerHeight();
		var sidebarWidth = sidebarSelector.outerWidth();
		var contentHeight = $(config.contentSelector).outerHeight();
		var footerHeight = $(config.footerSelector).outerHeight();
		var scroll_top = $(window).scrollTop();
		var fixPosition = contentHeight - sidebarHeight;
		var breakingPoint1 = headerHeight + navHeight;
		
		var breakingPoint2 = documentHeight - (sidebarHeight + footerHeight + config.footerThreshold);
 
		// calculate
		if ( (contentHeight > sidebarHeight) && (viewportHeight > sidebarHeight) ) {
		 
				if (scroll_top < breakingPoint1) {
				 
				sidebarSelector.removeClass('sticky');
				sidebarSelector.css('width','auto');
		 
		} else if ((scroll_top >= breakingPoint1) && (scroll_top < breakingPoint2)) {
		 
				sidebarSelector.addClass('sticky').css('top', (headerHeight + 20 ));
				sidebarSelector.css('width',sidebarWidth);
		} else {
		 
			var negative = breakingPoint2 - scroll_top;
			sidebarSelector.addClass('sticky').css('top',negative);
		    sidebarSelector.css('width',sidebarWidth);
		}
 
	}
};
 
return this.each( function() {
	if($(window).width() > 512)
	{
		$(window).on('scroll', $.proxy(fixSidebr, this));
		$(window).on('resize', $.proxy(fixSidebr, this))
		$.proxy(fixSidebr, this)();
	}
});
 
};
 
}( jQuery ));