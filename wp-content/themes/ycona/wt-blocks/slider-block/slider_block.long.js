/**
 * Slider Block – Travel Geometric style.
 * Single container: replace content on next/prev so enter animations run (like reference).
 */
(function () {
	"use strict";

	function init_slider( section ) {
		var track       = section.querySelector( ".slider-block-track" );
		var slides      = section.querySelectorAll( ".slider-block-slide" );
		var count       = slides.length;
		var btn_prev    = section.querySelector( ".slider-block-btn-prev" );
		var btn_next    = section.querySelector( ".slider-block-btn-next" );
		var autoplay_delay = parseInt( section.getAttribute( "data-autoplay" ), 10 ) || 0;

		if ( count === 0 ) {
			return;
		}

		var slides_data = Array.from( slides ).map( function ( el ) {
			return el.outerHTML;
		} );

		var current    = 0;
		var autoplay_id = null;

		function render_slide( index ) {
			current = index;
			track.innerHTML = slides_data[current];
			var slide_el = track.firstElementChild;
			if ( slide_el ) {
				slide_el.classList.add( "slide-enter-active" );
			}
		}

		function next_slide() {
			if ( count <= 1 ) return;
			current = ( current + 1 ) % count;
			render_slide( current );
			reset_autoplay();
		}

		function prev_slide() {
			if ( count <= 1 ) return;
			current = ( current - 1 + count ) % count;
			render_slide( current );
			reset_autoplay();
		}

		if ( btn_prev ) {
			btn_prev.addEventListener( "click", prev_slide );
		}
		if ( btn_next ) {
			btn_next.addEventListener( "click", next_slide );
		}

		function start_autoplay() {
			if ( autoplay_delay <= 0 || count <= 1 ) return;
			stop_autoplay();
			autoplay_id = setInterval( next_slide, autoplay_delay );
		}

		function stop_autoplay() {
			if ( autoplay_id ) {
				clearInterval( autoplay_id );
				autoplay_id = null;
			}
		}

		function reset_autoplay() {
			stop_autoplay();
			start_autoplay();
		}

		section.addEventListener( "keydown", function ( e ) {
			if ( e.key === "ArrowLeft" ) {
				prev_slide();
			} else if ( e.key === "ArrowRight" ) {
				next_slide();
			}
		} );

		section.addEventListener( "mouseenter", stop_autoplay );
		section.addEventListener( "mouseleave", start_autoplay );
		section.addEventListener( "focusin", stop_autoplay );
		section.addEventListener( "focusout", start_autoplay );

		document.addEventListener( "visibilitychange", function () {
			if ( document.hidden ) {
				stop_autoplay();
			} else {
				start_autoplay();
			}
		} );

		render_slide( 0 );
		start_autoplay();
	}

	document.addEventListener( "DOMContentLoaded", function () {
		document.querySelectorAll( ".slider-block-section" ).forEach( init_slider );
	} );
})();
