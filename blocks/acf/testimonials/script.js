/**
 * WDOD Testimonials: prev/next controls for a scroll-snap track.
 *
 * The track itself scrolls and snaps natively; this script only wires the
 * buttons, keeps the "n / total" status current and disables buttons at
 * either end. Works without it (users can swipe/scroll).
 */
( function () {
	'use strict';

	var INIT_FLAG = 'wdodTestimonialsReady';

	function setup( root ) {
		if ( root.dataset[ INIT_FLAG ] ) {
			return;
		}
		root.dataset[ INIT_FLAG ] = '1';

		var track = root.querySelector( '[data-wdod-testimonials-track]' );
		var items = track ? Array.prototype.slice.call( track.querySelectorAll( '[data-wdod-testimonials-item]' ) ) : [];
		var prev = root.querySelector( '[data-wdod-testimonials-prev]' );
		var next = root.querySelector( '[data-wdod-testimonials-next]' );
		var status = root.querySelector( '[data-wdod-testimonials-status]' );

		if ( ! track || items.length < 2 ) {
			return;
		}

		function currentIndex() {
			var left = track.scrollLeft;
			var closest = 0;
			var closestDist = Infinity;

			items.forEach( function ( item, i ) {
				var dist = Math.abs( item.offsetLeft - track.offsetLeft - left );
				if ( dist < closestDist ) {
					closestDist = dist;
					closest = i;
				}
			} );

			return closest;
		}

		function goTo( index ) {
			var clamped = Math.max( 0, Math.min( items.length - 1, index ) );
			var target = items[ clamped ];
			track.scrollTo( { left: target.offsetLeft - track.offsetLeft, behavior: 'smooth' } );
		}

		function update() {
			var index = currentIndex();
			var atEnd = track.scrollLeft + track.clientWidth >= track.scrollWidth - 2;

			if ( status ) {
				status.textContent = ( index + 1 ) + ' / ' + items.length;
			}
			if ( prev ) {
				prev.disabled = 0 === index;
			}
			if ( next ) {
				next.disabled = atEnd || index === items.length - 1;
			}
		}

		if ( prev ) {
			prev.addEventListener( 'click', function () {
				goTo( currentIndex() - 1 );
			} );
		}

		if ( next ) {
			next.addEventListener( 'click', function () {
				goTo( currentIndex() + 1 );
			} );
		}

		track.addEventListener( 'keydown', function ( event ) {
			if ( 'ArrowRight' === event.key ) {
				event.preventDefault();
				goTo( currentIndex() + 1 );
			} else if ( 'ArrowLeft' === event.key ) {
				event.preventDefault();
				goTo( currentIndex() - 1 );
			}
		} );

		var ticking = false;
		track.addEventListener( 'scroll', function () {
			if ( ticking ) {
				return;
			}
			ticking = true;
			window.requestAnimationFrame( function () {
				update();
				ticking = false;
			} );
		}, { passive: true } );

		window.addEventListener( 'resize', update );
		update();
	}

	function init( root ) {
		var scope = root && root.querySelectorAll ? root : document;
		Array.prototype.forEach.call( scope.querySelectorAll( '[data-wdod-testimonials]' ), setup );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			init( document );
		} );
	} else {
		init( document );
	}

	if ( window.acf && typeof window.acf.addAction === 'function' ) {
		window.acf.addAction( 'render_block_preview/type=testimonials', function ( $el ) {
			var node = $el && $el[ 0 ] ? $el[ 0 ] : null;
			if ( node ) {
				init( node );
			}
		} );
	}
} )();
