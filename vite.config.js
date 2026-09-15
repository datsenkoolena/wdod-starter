import { defineConfig } from 'vite';
import { dirname, relative, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import * as sass from 'sass';

/**
 * Vite build for WDOD Starter.
 *
 * - `main` is the only Rollup input so the bundle can be a classic IIFE
 *   script (Rollup refuses IIFE with several entries). WordPress then
 *   enqueues it with a plain <script> tag, no type="module".
 * - The editor stylesheet has no JS, so a small plugin compiles it with Sass
 *   and emits `editor.css` as an asset. It is registered under its source
 *   path in the manifest, exactly like a CSS entry would be.
 * - Stable file names (main.js, main.css, editor.css) let the PHP loader work
 *   without the manifest; the manifest is still written for wdod_asset().
 */

const root = dirname( fileURLToPath( import.meta.url ) );

/**
 * Compile a standalone SCSS file and emit it as a named asset.
 *
 * @param {string} entry    Source path relative to the theme root.
 * @param {string} fileName Output file name inside build.outDir.
 * @return {import('vite').Plugin}
 */
function standaloneStylesheet( entry, fileName ) {
	const absolute = resolve( root, entry );

	return {
		name: 'wdod-standalone-stylesheet',
		apply: 'build',
		buildStart() {
			const result = sass.compile( absolute, {
				style: 'compressed',
				loadPaths: [ dirname( absolute ) ],
			} );

			// Rebuild in --watch mode when the entry or any partial changes.
			for ( const url of result.loadedUrls ) {
				if ( 'file:' === url.protocol ) {
					this.addWatchFile( fileURLToPath( url ) );
				}
			}

			this.emitFile( {
				type: 'asset',
				fileName,
				name: fileName,
				originalFileName: relative( root, absolute ).split( '\\' ).join( '/' ),
				source: result.css,
			} );
		},
	};
}

export default defineConfig( {
	base: './',
	plugins: [ standaloneStylesheet( 'assets/src/scss/editor.scss', 'editor.css' ) ],
	build: {
		manifest: true,
		outDir: 'assets/dist',
		emptyOutDir: true,
		sourcemap: false,
		// One stylesheet per build instead of CSS injected into the JS bundle
		// (Vite inlines CSS for IIFE output when code-splitting is on).
		cssCodeSplit: false,
		rollupOptions: {
			input: {
				main: resolve( root, 'assets/src/js/main.js' ),
			},
			output: {
				format: 'iife',
				entryFileNames: '[name].js',
				chunkFileNames: '[name].js',
				assetFileNames: ( info ) => {
					// Vite names the non-split stylesheet "style.css"; keep it next to main.js as main.css.
					const name = info.names ? info.names[ 0 ] : info.name;
					return 'style.css' === name ? 'main.css' : '[name][extname]';
				},
			},
		},
	},
	css: {
		preprocessorOptions: {
			scss: {
				api: 'modern-compiler',
			},
		},
	},
} );
