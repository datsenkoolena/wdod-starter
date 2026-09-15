<?php
/**
 * WDOD Starter functions and definitions.
 *
 * Defines theme constants and loads the modular includes from /inc.
 *
 * @package WDOD_Starter
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WDOD_STARTER_VERSION' ) ) {
	$wdod_theme = wp_get_theme( get_template() );
	define( 'WDOD_STARTER_VERSION', $wdod_theme->exists() && $wdod_theme->get( 'Version' ) ? $wdod_theme->get( 'Version' ) : '1.0.0' );
	unset( $wdod_theme );
}

if ( ! defined( 'WDOD_STARTER_DIR' ) ) {
	define( 'WDOD_STARTER_DIR', trailingslashit( get_template_directory() ) );
}

if ( ! defined( 'WDOD_STARTER_URI' ) ) {
	define( 'WDOD_STARTER_URI', trailingslashit( get_template_directory_uri() ) );
}

/**
 * Load the theme modules.
 *
 * Each file is self-guarding: integration modules (WooCommerce, ACF, WPML,
 * Elementor) return early or register no-op helpers when their plugin is absent.
 */
$wdod_includes = array(
	'inc/setup.php',
	'inc/enqueue.php',
	'inc/template-tags.php',
	'inc/acf-blocks.php',
	'inc/theme-options.php',
	'inc/woocommerce.php',
	'inc/wpml.php',
	'inc/elementor.php',
);

foreach ( $wdod_includes as $wdod_include ) {
	$wdod_include_path = WDOD_STARTER_DIR . $wdod_include;

	if ( file_exists( $wdod_include_path ) ) {
		require_once $wdod_include_path;
	}
}

unset( $wdod_includes, $wdod_include, $wdod_include_path );
