jQuery(document).ready(function($) {

	"use strict";
	
	$('p').each(function() {
		var $this = $(this);
		if($this.html().replace(/\s|&nbsp;/g, '').length == 0)
			$this.remove();
	});
	
	var scrollNav = function(){
	  
		if($(window).scrollTop()>5) {
			$( ".header-section" ).addClass("fixed-scroll");
		} else {
			$( ".header-section" ).removeClass("fixed-scroll");
		}
		$(window).scroll(function() {
			if($(this).scrollTop()>5) {
				$( ".header-section" ).addClass("fixed-scroll");
			} else {
				$( ".header-section" ).removeClass("fixed-scroll");
			}
		});
		
	}
	scrollNav();
	
	var loaderPage = function() {
		$(".site-loader").fadeOut("slow");
	};
	loaderPage();
	

	var siteMenuClone = function() {

		$('.js-clone-nav').each(function() {
			var $this = $(this);
			$this.clone().attr('class', 'site-nav-wrap').appendTo('.site-mobile-menu-body');
		});


		setTimeout(function() {
			
			var counter = 0;
      $('.site-mobile-menu .has-children').each(function(){
        var $this = $(this);
        
        $this.prepend('<span class="arrow-collapse collapsed">');

        $this.find('.arrow-collapse').attr({
          'data-toggle' : 'collapse',
          'data-target' : '#collapseItem' + counter,
        });

        $this.find('> ul').attr({
          'class' : 'collapse',
          'id' : 'collapseItem' + counter,
        });

        counter++;

      });

    }, 1000);

	$('body').on('click', '.arrow-collapse', function(e) {
		var $this = $(this);
		if ( $this.closest('li').find('.collapse').hasClass('show') ) {
			$this.removeClass('active');
		}
		else 
		{
			$this.addClass('active');
		}
		e.preventDefault();
    });

		$(window).resize(function() {
			var $this = $(this),
				w = $this.width();

			if ( w > 768 ) {
				if ( $('body').hasClass('offcanvas-menu') ) {
					$('body').removeClass('offcanvas-menu');
				}
			}
		})

		$('body').on('click', '.js-menu-toggle', function(e) {
			var $this = $(this);
			e.preventDefault();

			if ( $('body').hasClass('offcanvas-menu') ) {
				$('body').removeClass('offcanvas-menu');
				$this.removeClass('active');
			} else {
				$('body').addClass('offcanvas-menu');
				$this.addClass('active');
			}
		}) 

		// click outisde offcanvas
		$(document).mouseup(function(e) {
	    var container = $(".site-mobile-menu");
	    if (!container.is(e.target) && container.has(e.target).length === 0) {
	      if ( $('body').hasClass('offcanvas-menu') ) {
					$('body').removeClass('offcanvas-menu');
				}
	    }
		});
	}; 
	siteMenuClone();


	var sitePlusMinus = function() {
		$('.js-btn-minus').on('click', function(e){
			e.preventDefault();
			if ( $(this).closest('.input-group').find('.form-control').val() != 0  ) {
				$(this).closest('.input-group').find('.form-control').val(parseInt($(this).closest('.input-group').find('.form-control').val()) - 1);
			} else {
				$(this).closest('.input-group').find('.form-control').val(parseInt(0));
			}
		});
		$('.js-btn-plus').on('click', function(e){
			e.preventDefault();
			$(this).closest('.input-group').find('.form-control').val(parseInt($(this).closest('.input-group').find('.form-control').val()) + 1);
		});
	};
	// sitePlusMinus();


	var siteSliderRange = function() {
    $( "#slider-range" ).slider({
      range: true,
      min: 0,
      max: 500,
      values: [ 75, 300 ],
      slide: function( event, ui ) {
        $( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
      }
    });
    $( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) +
      " - $" + $( "#slider-range" ).slider( "values", 1 ) );
	};
	// siteSliderRange();


	var siteMagnificPopup = function() {
		$('.image-popup').magnificPopup({
	    type: 'image',
	    closeOnContentClick: true,
	    closeBtnInside: false,
	    fixedContentPos: true,
	    mainClass: 'mfp-no-margins mfp-with-zoom', // class to remove default margin from left and right side
	     gallery: {
	      enabled: true,
	      navigateByImgClick: true,
	      preload: [0,1] // Will preload 0 - before current, and 1 after the current image
	    },
	    image: {
	      verticalFit: true
	    },
	    zoom: {
	      enabled: true,
	      duration: 300 // don't foget to change the duration also in CSS
	    }
	  });

	  $('.popup-youtube, .popup-vimeo, .popup-gmaps').magnificPopup({
	    disableOn: 700,
	    type: 'iframe',
	    mainClass: 'mfp-fade',
	    removalDelay: 160,
	    preloader: false,

	    fixedContentPos: false
	  });
	};
	siteMagnificPopup();


	var siteCarousel = function () {
		
		if ( $('.nonloop-block-13').length > 0 ) {
			$('.nonloop-block-13').owlCarousel({
		    center: false,
		    items: 1,
		    loop: true,
				stagePadding: 0,
				autoplay: true,
		    margin: 20,
		    nav: false,
		    dots: true,
				navText: ['<span class="icon-arrow_back">', '<span class="icon-arrow_forward">'],
		    responsive:{
	        600:{
	        	margin: 20,
	        	stagePadding: 0,
	          items: 1
	        },
	        1000:{
	        	margin: 20,
	        	stagePadding: 0,
	          items: 2
	        },
	        1200:{
	        	margin: 20,
	        	stagePadding: 0,
	          items: 3
	        }
		    }
			});
		}
		
		
	  if ( $('.grid-carousel').length > 0 ) {
			$('.grid-carousel').each(function() {
				
				var thiss = $(this)
				
				var autoplay = false;
				if(thiss.attr('data-autoplay') && thiss.attr('data-autoplay') == 'yes')
				{
					autoplay = true;
				}
				
				var nav = false;
				if(thiss.attr('data-nav') && thiss.attr('data-nav') == 'yes')
				{
					nav = true;
				}
				
				var dots = false;
				if(thiss.attr('data-dots') && thiss.attr('data-dots') == 'yes')
				{
					dots = true;
				}
				
				var interval = 3000;
				if(thiss.attr('data-interval') && thiss.attr('data-interval') != '')
				{
					interval = thiss.attr('data-interval');
				}
				
				var items = 3000;
				if(thiss.attr('data-items') && thiss.attr('data-items') != '')
				{
					items = thiss.attr('data-items');
				}
				
				thiss.owlCarousel({
					'center': false,
					'items': items,
					'loop': true,
					'stagePadding': 0,
					'margin': 30,
					'autoplay': autoplay,
					'autoplayTimeout' : interval,
					'nav': nav,
					'dots':dots,
					'rtl':is_rtl,
					'navText': ['<span class="icon-arrow_back">', '<span class="icon-arrow_forward">'],
					'responsiveClass':true,
					'responsive':{
						0:{
							items:1,
							'nav':nav
						},
						600:{
							items:3,
							'nav':nav
						},
					}
				 });
		   });
	  }
	  
	  
	  if ( $('.product-gallery-carousel').length > 0 ) {
			$('.product-gallery-carousel').each(function() {
				
				var thiss = $(this)
				
				var autoplay = false;
				if(thiss.attr('data-autoplay') && thiss.attr('data-autoplay') == 'yes')
				{
					autoplay = true;
				}
				
				var nav = false;
				if(thiss.attr('data-nav') && thiss.attr('data-nav') == 'yes')
				{
					nav = true;
				}
				
				var dots = false;
				if(thiss.attr('data-dots') && thiss.attr('data-dots') == 'yes')
				{
					dots = true;
				}
				
				var interval = 3000;
				if(thiss.attr('data-interval') && thiss.attr('data-interval') != '')
				{
					interval = thiss.attr('data-interval');
				}
				
				var items = 1;
				if(thiss.attr('data-items') && thiss.attr('data-items') != '')
				{
					items = thiss.attr('data-items');
				}
				
				thiss.owlCarousel({
					'center': false,
					'items': items,
					'loop': true,
					'stagePadding': 0,
					'margin': 30,
					'autoplay': autoplay,
					'pauseOnHover': true,
					'autoplayTimeout' : interval,
					'slideSpeed' : 1000,
					'nav': nav,
					'dots':dots,
					'rtl':is_rtl,
					'navText': ['<i class="fa fa-chevron-left">', '<i class="fa fa-chevron-right">'],
					'responsiveClass':true,
					'responsive':{
						
					}
				 });
		   });
	  }
	  
	  if ( $('.related-property').length > 0 ) {
			$('.related-property').owlCarousel({
		    center: false,
		    items: 3,
		    loop: false,
			stagePadding: 0,
		    margin: 15,
		    autoplay: false,
		    pauseOnHover: false,
		    nav: true,
			rtl:is_rtl,
		    animateOut: 'fadeOut',
		    animateIn: 'fadeIn',
		    navText: ['<span class="icon-arrow_back">', '<span class="icon-arrow_forward">'],
			responsiveClass:true,
			responsive:{
				0:{
					items:1,
					nav:true
				},
				600:{
					items:1,
					nav:true
				},
				1000:{
					items:3,
					nav:true,
					loop:false
				}
			}
		  });
	  }
	  
	  if ( $('.video_url_container').length > 0 ) {
			$('.video_url_container').owlCarousel({
		    center: false,
		    items: 1,
		    loop: true,
			stagePadding: 0,
		    margin: 15,
		    autoplay: false,
		    pauseOnHover: false,
		    nav: true,
			rtl:is_rtl,
		    animateOut: 'fadeOut',
		    animateIn: 'fadeIn',
		    navText: ['<span class="icon-arrow_back">', '<span class="icon-arrow_forward">']
		  });
	  }
	  
	  if ( $('.property-type-carousel').length > 0 ) {
		  
			var items = 3;
			if($('.property-type-carousel').attr('data-items') && $('.property-type-carousel').attr('data-items') != '')
			{
				items = $('.property-type-carousel').attr('data-items');
			}
			
			var nav = false;
			if($('.property-type-carousel').attr('data-nav') && $('.property-type-carousel').attr('data-nav') == 'yes')
			{
				nav = true;
			}
			
			var dots = false;
			if($('.property-type-carousel').attr('data-dots') && $('.property-type-carousel').attr('data-dots') == 'yes')
			{
				dots = true;
			}
			
			var autoplay = false;
			if($('.property-type-carousel').attr('data-autoplay') && $('.property-type-carousel').attr('data-autoplay') == 'yes')
			{
				autoplay = true;
			}
			
			var interval = 3000;
			if($('.property-type-carousel').attr('data-interval') && $('.property-type-carousel').attr('data-interval') != '')
			{
				interval = $('.property-type-carousel').attr('data-interval');
			}
		  
			$('.property-type-carousel').owlCarousel({
		    'center': false,
		    'items': items,
		    'loop': true,
			'stagePadding': 0,
		    'margin': 30,
		    'autoplay': autoplay,
			'autoplayTimeout' : interval,
		    'pauseOnHover': false,
		    'nav': nav,
			'dots': dots,
			'rtl':is_rtl,
		    'navText': ['<span class="icon-arrow_back">', '<span class="icon-arrow_forward">'],
			'responsiveClass':true,
			'responsive':{
				0:{
					items:1,
					'nav':nav
				},
				600:{
					items:items,
					'nav':nav
				},
			}
		  });
	  }
	  
	  if ( $('.home-slider').length > 0 ) {
			var autoplay = false;
			if($('.home-slider').attr('data-autoplay') && $('.home-slider').attr('data-autoplay') == 'true')
			{
				autoplay = true;
			}
			
			var nav = false;
			if($('.home-slider').attr('data-nav') && $('.home-slider').attr('data-nav') == 'yes')
			{
				nav = true;
			}
			
			var dots = false;
			if($('.home-slider').attr('data-dots') && $('.home-slider').attr('data-dots') == 'yes')
			{
				dots = true;
			}
			
			var interval = 3000;
			if($('.home-slider').attr('data-interval') && $('.home-slider').attr('data-interval') != '')
			{
				interval = $('.home-slider').attr('data-interval');
			}
			
			$('.home-slider').owlCarousel({
		    'center': false,
		    'items': 1,
		    'loop': true,
			'stagePadding': 0,
		    'margin': 0,
		    'autoplay': autoplay,
		    'nav': nav,
			'dots':dots,
			'autoplayTimeout' : interval,
			'rtl':is_rtl,
		    'animateOut': 'fadeOut',
		    'animateIn': 'fadeIn',
			'autoHeight':true,
		    'navText': ['<span class="icon-arrow_back">', '<span class="icon-arrow_forward">']
		  });
	  }
	  
	  if ( $('#sl-slider').length > 0  && $('#sl-slider .sl-item').length > 1) {
	    var sync1 = $("#sl-slider");
		var sync2 = $("#sl-slider-thumb");
		var slidesPerPage = 4; //globaly define number of elements per page
		var syncedSecondary = true;

		sync1.owlCarousel({
			items : 1,
			slideSpeed : 2000,
			nav: false,
			autoplay: true,
			dots: true,
			loop: true,
			rtl:is_rtl,
			animateOut: 'fadeOut',
		    animateIn: 'fadeIn',
			responsiveRefreshRate : 200,
			navText: ['<span class="icon-arrow_back">', '<span class="icon-arrow_forward">'],
		}).on('changed.owl.carousel', syncPosition);

		sync2.on('initialized.owl.carousel', function () {
			sync2.find(".owl-item").eq(0).addClass("current");
		}).owlCarousel({
			items : slidesPerPage,
			dots: true,
			nav: true,
			margin: 10,
			smartSpeed: 200,
			rtl:is_rtl,
			slideSpeed : 500,
			//navText: ['<span class="icon-arrow_back">', '<span class="icon-arrow_forward">'],
			navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
			slideBy: slidesPerPage, //alternatively you can slide by 1, this way the active slide will stick to the first item in the second carousel
			responsiveRefreshRate : 100
		}).on('changed.owl.carousel', syncPosition2);

		function syncPosition(el) {
			//if you set loop to false, you have to restore this next line
			//var current = el.item.index;
			//if you disable loop you have to comment this block
			var count = el.item.count-1;
			var current = Math.round(el.item.index - (el.item.count/2) - .5);

			if(current < 0) {
				current = count;
			}
			if(current > count) {
				current = 0;
			}

			//end block
			sync2.find(".owl-item").removeClass("current").eq(current).addClass("current");
			var onscreen = sync2.find('.owl-item.active').length - 1;
			var start = sync2.find('.owl-item.active').first().index();
			var end = sync2.find('.owl-item.active').last().index();

			if (current > end) {
				sync2.data('owl.carousel').to(current, 100, true);
			}
			if (current < start) {
				sync2.data('owl.carousel').to(current - onscreen, 100, true);
			}
		}

		function syncPosition2(el) {
			if(syncedSecondary) {
				var number = el.item.index;
				sync1.data('owl.carousel').to(number, 100, true);
			}
		}

		sync2.on("click", ".owl-item", function(e){
			e.preventDefault();
			var number = $(this).index();
			sync1.data('owl.carousel').to(number, 300, true);
		});
	  }
	  
	  if ( $('.nonloop-block-4').length > 0 ) {
		  $('.nonloop-block-4').owlCarousel({
		    center: true,
		    items:1,
		    loop:false,
		    margin:10,
			rtl:is_rtl,
		    nav: true,
				navText: ['<span class="icon-arrow_back">', '<span class="icon-arrow_forward">'],
		    responsive:{
	        600:{
	          items:1
	        }
		    }
			});
		}

	};
	siteCarousel();

	var siteStellar = function() {
		$(window).stellar({
	    responsive: false,
	    parallaxBackgrounds: true,
	    parallaxElements: true,
	    horizontalScrolling: false,
	    hideDistantElements: false,
	    scrollProperty: 'scroll'
	  });
	};
	siteStellar();

	var siteCountDown = function() {

		if ( $('#date-countdown').length > 0 ) {
			$('#date-countdown').countdown('2020/10/10', function(event) {
			  var $this = $(this).html(event.strftime(''
			    + '<span class="countdown-block"><span class="label">%w</span> weeks </span>'
			    + '<span class="countdown-block"><span class="label">%d</span> days </span>'
			    + '<span class="countdown-block"><span class="label">%H</span> hr </span>'
			    + '<span class="countdown-block"><span class="label">%M</span> min </span>'
			    + '<span class="countdown-block"><span class="label">%S</span> sec</span>'));
			});
		}
				
	};
	siteCountDown();

	var siteDatePicker = function() {

		if ( $('.datepicker').length > 0 ) {
			$('.datepicker').datepicker();
		}

	};
	siteDatePicker();

	

});