/**
 * WDOD Hero: reveal on scroll via IntersectionObserver.
 *
 * Runs on the front end and inside the block editor (ACF re-renders the
 * block, so we watch for new .wdod-hero elements too).
 */
( function () {
	'use strict';

	var SELECTOR = '.wdod-hero';
	var VISIBLE = 'is-visible';

	function reveal( el ) {
		el.classList.add( VISIBLE );
	}

	var observer = null;

	if ( 'IntersectionObserver' in window ) {
		observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						reveal( entry.target );
						observer.unobserve( entry.target );
					}
				} );
			},
			{ rootMargin: '0px 0px -10% 0px', threshold: 0.15 }
		);
	}

	function init( root ) {
		var scope = root && root.querySelectorAll ? root : document;
		var heroes = scope.querySelectorAll( SELECTOR + ':not(.' + VISIBLE + ')' );

		Array.prototype.forEach.call( heroes, function ( hero ) {
			if ( observer ) {
				observer.observe( hero );
			} else {
				reveal( hero );
			}
		} );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			init( document );
		} );
	} else {
		init( document );
	}

	// Block editor: ACF fires this after each (re)render of a block preview.
	if ( window.acf && typeof window.acf.addAction === 'function' ) {
		window.acf.addAction( 'render_block_preview/type=hero', function ( $el ) {
			var node = $el && $el[ 0 ] ? $el[ 0 ] : null;
			if ( node ) {
				init( node );
				// The editor canvas may not scroll; show immediately.
				Array.prototype.forEach.call( node.querySelectorAll( SELECTOR ), reveal );
			}
		} );
	}
} )();
