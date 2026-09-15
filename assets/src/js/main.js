/**
 * WDOD Starter front-end entry.
 *
 * Importing the stylesheet here lets Vite emit main.css alongside main.js and
 * list it under this entry in the manifest.
 */
import '../scss/main.scss';
import { initNavigation } from './navigation.js';

function boot() {
	initNavigation();
}

if ( 'loading' === document.readyState ) {
	document.addEventListener( 'DOMContentLoaded', boot );
} else {
	boot();
}
