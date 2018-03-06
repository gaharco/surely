/* global screenReaderText */
/**
 * Theme functions file.
 *
 * Contains handlers for navigation and widget area.
 */

( function( $ ) {
	"use strict";

	var body			 = $( document.body ); 
	var masthead         = $( '#masthead' );
	var menuToggle       = masthead.find( '#menu-toggle' );
	var siteHeaderMenu   = masthead.find( '#site-header-menu' );
	var siteNavigation   = masthead.find( '#site-navigation' );
	var socialNavigation = masthead.find( '#social-navigation' );
	var lastScrollTop 	 = 0;
	var isSinglePost 	 = body.hasClass('single');
	var originalHeaderHeight, resizeTimer, didScroll;

	function initMainNavigation( container ) {

		// Add dropdown toggle that displays child menu items.
		var dropdownToggle = $( '<button />', {
			'class': 'dropdown-toggle',
			'aria-expanded': false
		} ).append( $( '<span />', {
			'class': 'screen-reader-text',
			text: screenReaderText.expand
		} ) );

		container.find( '.menu-item-has-children > a' ).after( dropdownToggle );

		// Toggle buttons and submenu items with active children menu items.
		container.find( '.current-menu-ancestor > button' ).addClass( 'toggled-on' );
		container.find( '.current-menu-ancestor > .sub-menu' ).addClass( 'toggled-on' );

		// Add menu items with submenus to aria-haspopup="true".
		container.find( '.menu-item-has-children' ).attr( 'aria-haspopup', 'true' );

		container.find( '.dropdown-toggle' ).click( function( e ) {
			var _this            = $( this ),
				screenReaderSpan = _this.find( '.screen-reader-text' );

			e.preventDefault();
			_this.toggleClass( 'toggled-on' );
			_this.next( '.children, .sub-menu' ).toggleClass( 'toggled-on' );

			// jscs:disable
			_this.attr( 'aria-expanded', _this.attr( 'aria-expanded' ) === 'false' ? 'true' : 'false' );
			// jscs:enable
			screenReaderSpan.text( screenReaderSpan.text() === screenReaderText.expand ? screenReaderText.collapse : screenReaderText.expand );
		} );
	}
	initMainNavigation( $( '.main-navigation' ) );

	// Enable menuToggle.
	( function() {

		// Return early if menuToggle is missing.
		if ( ! menuToggle.length ) {
			return;
		}

		menuToggle.on( 'click.surely', function() {
			$( this ).add( siteHeaderMenu ).toggleClass( 'toggled-on' );
			$('.search-toggle').removeClass('toggled');

		} );
	} )();

	// Fix sub-menus for touch devices and better focus for hidden submenu items for accessibility.
	( function() {
		if ( ! siteNavigation.length || ! siteNavigation.children().length ) {
			return;
		}

		// Toggle `focus` class to allow submenu access on tablets.
		function toggleFocusClassTouchScreen() {
			if ( window.innerWidth >= 910 ) {
				$( document.body ).on( 'touchstart.surely', function( e ) {
					if ( ! $( e.target ).closest( '.main-navigation li' ).length ) {
						$( '.main-navigation li' ).removeClass( 'focus' );
					}
				} );
				siteNavigation.find( '.menu-item-has-children > a' ).on( 'touchstart.surely', function( e ) {
					var el = $( this ).parent( 'li' );

					if ( ! el.hasClass( 'focus' ) ) {
						e.preventDefault();
						el.toggleClass( 'focus' );
						el.siblings( '.focus' ).removeClass( 'focus' );
					}
				} );
			} else {
				siteNavigation.find( '.menu-item-has-children > a' ).unbind( 'touchstart.surely' );
			}
		}

		if ( 'ontouchstart' in window ) {
			$( window ).on( 'resize.surely', toggleFocusClassTouchScreen );
			toggleFocusClassTouchScreen();
		}

		siteNavigation.find( 'a' ).on( 'focus.surely blur.surely', function() {
			$( this ).parents( '.menu-item' ).toggleClass( 'focus' );
		} );
	} )();

	function sliderWidthInit( slider ) {
		if ( sliderOptions.slideType == 'carousel' ) {
			slider.vars.minItems = Math.floor( slider.width() / 280 );
		}
	}

	function hasScrolled() {
		var st = $(document).scrollTop();
		var singleTitleTop = $('.single .site-main > article > .entry-header').offset();
		var singleTitleHeight = $('.single .site-main > article > .entry-header').outerHeight();
		var singleContentTop = $('.single .site-main > article > .entry-header').next().offset();

		//Don't do if the sticky header disabled
		if ( ! miscThemeOptions.enableStickyHeader )
			return;

		// Make sure they scroll more than delta
		if( Math.abs(lastScrollTop - st) <= 60 || body.outerWidth() < 910 )
			return;

		// If they scrolled down and are past the navbar, add class .nav-up.
		// This is necessary so you never see what is "behind" the navbar.
		if (st > lastScrollTop ){
			// Scroll Down

			if ( isSinglePost && st > singleContentTop.top ) {
				//masthead.css('background-color', '#eeeeee')
				siteHeaderMenu.addClass('sticky-post-title-show');
				body.addClass('sticky-menu').css( 'padding-top', originalHeaderHeight );
			} else {
				body.removeClass('sticky-menu').css( 'padding-top', 0 );
			}
		} else {
			// Scroll Up
			//masthead.css('background-color', '#ffffff')
			siteHeaderMenu.removeClass('sticky-post-title-show');
			if ( st > originalHeaderHeight + 80 ) {
				body.addClass('sticky-menu').css( 'padding-top', originalHeaderHeight );
			} else {
				body.removeClass('sticky-menu').css( 'padding-top', 0 );
			}
		}
		
		lastScrollTop = st;
	}		

	$( document ).ready( function() {
		originalHeaderHeight = masthead.outerHeight();

		if ( isSinglePost ) {
			siteHeaderMenu.append(
				'<div class="sticky-post-title">' + 
				'<span>Reading:</span> <span class="entry-title">' +
				$('.single .site-main > article > .entry-header .entry-title').html() +
				'</span></div>');
		}

		setInterval(function() {
			if (didScroll) {
				hasScrolled();
				didScroll = false;
			}
		}, 250);

		$( window )
			.on( 'scroll.surely', function() {
				didScroll = true;
			})
			.on( 'resize.surely', function() {
				if ( ! body.hasClass('sticky-menu') )
					originalHeaderHeight = masthead.outerHeight();
			} );

		$(".hentry").fitVids();

		$('.featured-slider').each( function(){
			var slider = $(this);
			var sliderOpts = slider.data('slider-options');
			slider.flexslider( {
				selector: '.slides > article',
				animation: sliderOpts.animation,
				direction: sliderOpts.direction,
				controlNav: false,
				prevText: sliderOpts.prevText,
				nextText: sliderOpts.nextText,
				minItems: 1,
				maxItems: 1,
				slideshow: sliderOpts.slideshow,
				slideshowSpeed: sliderOpts.slideshow_time,
				init: function( slider ) {
					//sliderWidthInit(slider);
					$(window).on('resize.surely', function() {
						//sliderWidthInit(slider);
						slider.doMath();
					});
				}
			});
		} );

		$('.search-toggle').on( 'click.surely', function() {
			$(this).toggleClass('toggled');
			if ( $(this).hasClass('toggled') ) {
				$('.site-search .search-field').focus();
				menuToggle.add(siteHeaderMenu).removeClass('toggled-on');
			}
		} );

		$('.site-search .search-field, .site-search .search-submit').on( 'focus', function() {
			$('.search-toggle').addClass('toggled');
		});

 		$('.load-more a').on('click.surely', function (e) {
			e.preventDefault();

			//widgetId = $(this).parents('.widget').attr("id");
			$(this).addClass('loading').text( screenReaderText.loadingText );

			$.ajax({
				type: "GET",
				url: $(this).attr('href') + '#main',
				dataType: "html",
				success: function (out) {
					var result = $(out).find('#main .post');
					var nextlink = $(out).find('#main .load-more a').attr('href');
					$('#main .load-more').before( result.fadeIn(800) );
					$('#main .load-more a').removeClass('loading').text( screenReaderText.loadMoreText );
					if (nextlink != undefined) {
						$('#main .load-more a').attr('href', nextlink);
					} else {
						$('#main .load-more').remove();
					}
				}
			});
		});

	} );
} )( jQuery );
