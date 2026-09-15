/**
 * Mobile navigation toggle.
 *
 * - Toggles `.is-open` on the nav and `aria-expanded` on the button.
 * - Escape closes the menu and returns focus to the toggle.
 * - Clicking outside the header closes the menu.
 * - Resizing to desktop resets state so the desktop layout never inherits
 *   a stale "open" class.
 */

const DESKTOP_QUERY = '(min-width: 64rem)';

/**
 * Wire up a single toggle/nav pair.
 *
 * @param {HTMLElement} toggle Button element with `aria-controls`.
 */
function setupToggle( toggle ) {
	const targetId = toggle.getAttribute( 'aria-controls' );
	const nav = targetId ? document.getElementById( targetId ) : null;

	if ( ! nav ) {
		return;
	}

	const i18n = ( window.wdodStarter && window.wdodStarter.i18n ) || {};
	const mql = window.matchMedia( DESKTOP_QUERY );

	const setState = ( open ) => {
		nav.classList.toggle( 'is-open', open );
		toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		document.body.classList.toggle( 'wdod-menu-open', open );

		const label = open ? i18n.closeMenu : i18n.openMenu;
		if ( label ) {
			toggle.setAttribute( 'aria-label', label );
		}
	};

	const isOpen = () => 'true' === toggle.getAttribute( 'aria-expanded' );

	toggle.addEventListener( 'click', () => {
		setState( ! isOpen() );
	} );

	document.addEventListener( 'keydown', ( event ) => {
		if ( 'Escape' === event.key && isOpen() ) {
			setState( false );
			toggle.focus();
		}
	} );

	document.addEventListener( 'click', ( event ) => {
		if ( ! isOpen() ) {
			return;
		}

		const header = toggle.closest( 'header' ) || nav.parentElement;
		if ( header && ! header.contains( event.target ) ) {
			setState( false );
		}
	} );

	// Close when a link inside the menu is followed (same-page anchors).
	nav.addEventListener( 'click', ( event ) => {
		const link = event.target.closest( 'a[href^="#"]' );
		if ( link && isOpen() ) {
			setState( false );
		}
	} );

	const onMediaChange = ( event ) => {
		if ( event.matches ) {
			setState( false );
		}
	};

	if ( typeof mql.addEventListener === 'function' ) {
		mql.addEventListener( 'change', onMediaChange );
	} else if ( typeof mql.addListener === 'function' ) {
		mql.addListener( onMediaChange );
	}

	setState( false );
}

/**
 * Initialise every `[data-wdod-menu-toggle]` on the page.
 */
export function initNavigation() {
	document.querySelectorAll( '[data-wdod-menu-toggle]' ).forEach( setupToggle );
}
