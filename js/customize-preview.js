/**
 * Live-update changed settings in real time in the Customizer preview.
 */

( function( $ ) {
	"use strict";

	var style = $( '#surely-color-scheme-css' ),
		api = wp.customize;

	if ( ! style.length ) {
		style = $( 'head' ).append( '<style type="text/css" id="surely-color-scheme-css" />' )
		                    .find( '#surely-color-scheme-css' );
	}

	// Site title.
	api( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title a' ).text( to );
		} );
	} );

	// Site tagline.
	api( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description' ).text( to );
		} );
	} );

	// Add custom-background-image body class when background image is added.
	api( 'background_image', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).toggleClass( 'custom-background-image', '' !== to );
		} );
	} );

	// Color Scheme CSS.
	api.bind( 'preview-ready', function() {
		api.preview.bind( 'update-color-scheme-css', function( css ) {
			style.html( css );
		} );
	} );

	function writeCSS(){
		var cssOutput = '';
		var before = '';
		var after = '';

		for ( i = 0; i < _customizerCSS.length ; i++ ){
			if ( api.instance( _customizerCSS[i].id ).get() && ( api.instance( _customizerCSS[i].id ).get() !== _customizerCSS[i].default ) ) {
				if ( _customizerCSS[i].mq !== 'global' ) {
					before = _customizerCSS[i].mq + ' { ';
					after = '}';
				}else{
					before = '';
					after = '';
				}
				cssOutput += before;
				if ( _customizerCSS[i].value_in_text == '' ){
					cssOutput += _customizerCSS[i].selector + '{' + _customizerCSS[i].property + ' : ' + api.instance( _customizerCSS[i].id ).get() + _customizerCSS[i].unit + '; }';
				}else{
					str = _customizerCSS[i].value_in_text;
					val = str.replace('%value%', api.instance( _customizerCSS[i].id ).get() );
					cssOutput += _customizerCSS[i].selector + '{' + _customizerCSS[i].property + ' : ' + val + '; }';
				}
				cssOutput += after;
			}
		}

		$('#surely-preview-style-inline-css').text(cssOutput);
	}

	for ( var i = 0; i < _customizerCSS.length ; i++ ){
		wp.customize( _customizerCSS[i].id, function( value ) {
			value.bind( function( to ){
				writeCSS();
			} );
		});
	}

	$( document ).ready( function() {
		wp.customize.selectiveRefresh.bind( 'partial-content-rendered', function( placement ) {
			var slider, sliderOpts;
			if ( placement.container ) {
				slider = $( '.featured-slider', placement.container );
				if ( slider.length > 0 ) {
					sliderOpts = slider.data('slider-options');
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
					});
				}

			}
		} );
	});
} )( jQuery );
