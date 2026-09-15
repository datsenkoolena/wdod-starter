<?php
/**
 * Asset loading via the Vite manifest, with a no-build fallback.
 *
 * @package WDOD_Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Read and cache the Vite manifest.
 *
 * @return array<string,array> Manifest keyed by source path, or empty array.
 */
function wdod_asset_manifest() {
	static $manifest = null;

	if ( null !== $manifest ) {
		return $manifest;
	}

	$manifest = array();
	$path     = WDOD_STARTER_DIR . 'assets/dist/.vite/manifest.json';

	if ( is_readable( $path ) ) {
		$json = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local theme file.
		$data = json_decode( (string) $json, true );

		if ( is_array( $data ) ) {
			$manifest = $data;
		}
	}

	return $manifest;
}

/**
 * Find a manifest entry by its short name ("main", "editor").
 *
 * @param string $entry Entry name.
 * @return array|null Manifest chunk or null.
 */
function wdod_asset_manifest_entry( $entry ) {
	$manifest = wdod_asset_manifest();

	// 1. JS entries carry a "name" (from rollupOptions.input keys).
	foreach ( $manifest as $chunk ) {
		if ( ! empty( $chunk['isEntry'] ) && isset( $chunk['name'] ) && $entry === $chunk['name'] ) {
			return $chunk;
		}
	}

	// 2. Anything else (e.g. the emitted editor.css) is keyed by its source path.
	foreach ( $manifest as $src => $chunk ) {
		if ( pathinfo( (string) $src, PATHINFO_FILENAME ) === $entry ) {
			return $chunk;
		}
	}

	return null;
}

/**
 * Resolve a built asset to theme-relative paths.
 *
 * @param string $entry Entry name ("main" or "editor").
 * @return array{file:string,css:string[]} Relative paths inside the theme, empty strings/arrays when missing.
 */
function wdod_asset_relative( $entry ) {
	$result = array(
		'file' => '',
		'css'  => array(),
	);

	$chunk = wdod_asset_manifest_entry( $entry );

	if ( $chunk ) {
		if ( ! empty( $chunk['file'] ) ) {
			$result['file'] = 'assets/dist/' . ltrim( $chunk['file'], '/' );
		}

		if ( ! empty( $chunk['css'] ) && is_array( $chunk['css'] ) ) {
			foreach ( $chunk['css'] as $css ) {
				$result['css'][] = 'assets/dist/' . ltrim( $css, '/' );
			}
		}

		// With build.cssCodeSplit=false Vite lists the stylesheet as its own
		// "style.css" asset instead of under the entry; pick up the sibling file.
		if ( empty( $result['css'] ) && file_exists( WDOD_STARTER_DIR . 'assets/dist/' . $entry . '.css' ) ) {
			$result['css'][] = 'assets/dist/' . $entry . '.css';
		}

		return $result;
	}

	// No manifest: rely on the stable file names produced by vite.config.js.
	$js  = 'assets/dist/' . $entry . '.js';
	$css = 'assets/dist/' . $entry . '.css';

	if ( file_exists( WDOD_STARTER_DIR . $js ) ) {
		$result['file'] = $js;
	}

	if ( file_exists( WDOD_STARTER_DIR . $css ) ) {
		$result['css'][] = $css;
	}

	return $result;
}

/**
 * Get a built asset as URLs.
 *
 * @param string $entry Entry name ("main" or "editor").
 * @return array{file:string,css:string[]} URL of the JS/CSS entry file and its CSS dependencies.
 */
function wdod_asset( $entry ) {
	$relative = wdod_asset_relative( $entry );

	return array(
		'file' => $relative['file'] ? WDOD_STARTER_URI . $relative['file'] : '',
		'css'  => array_map(
			static function ( $path ) {
				return WDOD_STARTER_URI . $path;
			},
			$relative['css']
		),
	);
}

/**
 * Theme-relative path for a CSS-only entry (used by add_editor_style()).
 *
 * @param string $entry Entry name, e.g. "editor".
 * @return string Relative path or empty string.
 */
function wdod_asset_path_relative( $entry ) {
	$relative = wdod_asset_relative( $entry );

	if ( ! empty( $relative['css'][0] ) ) {
		return $relative['css'][0];
	}

	// A pure-CSS Vite entry lists its stylesheet as "file".
	if ( $relative['file'] && '.css' === substr( $relative['file'], -4 ) ) {
		return $relative['file'];
	}

	return '';
}

/**
 * Cache-busting version for a theme-relative file.
 *
 * @param string $relative Theme-relative path.
 * @return string|false
 */
function wdod_asset_version( $relative ) {
	$path = WDOD_STARTER_DIR . $relative;

	if ( file_exists( $path ) ) {
		$mtime = filemtime( $path );
		if ( $mtime ) {
			return (string) $mtime;
		}
	}

	return WDOD_STARTER_VERSION;
}

/**
 * Whether a production build exists in assets/dist.
 *
 * @return bool
 */
function wdod_has_build() {
	$main = wdod_asset_relative( 'main' );

	return ! empty( $main['file'] ) || ! empty( $main['css'] );
}

/**
 * Enqueue front-end scripts and styles.
 *
 * @return void
 */
function wdod_enqueue_assets() {
	$main = wdod_asset_relative( 'main' );

	foreach ( $main['css'] as $index => $css ) {
		$handle = 0 === $index ? 'wdod-starter' : 'wdod-starter-' . $index;
		wp_enqueue_style( $handle, WDOD_STARTER_URI . $css, array(), wdod_asset_version( $css ) );
	}

	if ( $main['file'] ) {
		wp_enqueue_script(
			'wdod-starter',
			WDOD_STARTER_URI . $main['file'],
			array(),
			wdod_asset_version( $main['file'] ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);

		wp_localize_script(
			'wdod-starter',
			'wdodStarter',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'i18n'    => array(
					'openMenu'  => esc_html__( 'Open menu', 'wdod-starter' ),
					'closeMenu' => esc_html__( 'Close menu', 'wdod-starter' ),
				),
			)
		);
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'wdod_enqueue_assets' );

/**
 * Warn administrators when the theme has not been built.
 *
 * @return void
 */
function wdod_missing_build_notice() {
	if ( ! current_user_can( 'manage_options' ) || wdod_has_build() ) {
		return;
	}

	printf(
		'<div class="notice notice-warning"><p><strong>%1$s</strong> %2$s <code>npm install &amp;&amp; npm run build</code> %3$s <code>%4$s</code>.</p></div>',
		esc_html__( 'WDOD Starter:', 'wdod-starter' ),
		esc_html__( 'compiled assets are missing. Run', 'wdod-starter' ),
		esc_html__( 'inside the theme folder', 'wdod-starter' ),
		esc_html( wp_normalize_path( WDOD_STARTER_DIR ) )
	);
}
add_action( 'admin_notices', 'wdod_missing_build_notice' );
