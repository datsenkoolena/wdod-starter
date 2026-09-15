<?php
/**
 * Theme setup: supports, menus, image sizes, widgets, text domain.
 *
 * @package WDOD_Starter
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $content_width ) ) {
	$content_width = 1200; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
}

/**
 * Register theme features and text domain.
 *
 * @return void
 */
function wdod_setup() {
	load_theme_textdomain( 'wdod-starter', WDOD_STARTER_DIR . 'languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'customize-selective-refresh-widgets' );

	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
			'navigation-widgets',
		)
	);

	add_theme_support(
		'custom-logo',
		array(
			'height'               => 80,
			'width'                => 240,
			'flex-height'          => true,
			'flex-width'           => true,
			'unlink-homepage-logo' => true,
		)
	);

	// Editor styles: prefer the built stylesheet, resolved through the Vite manifest.
	add_theme_support( 'editor-styles' );
	$wdod_editor_css = function_exists( 'wdod_asset_path_relative' ) ? wdod_asset_path_relative( 'editor' ) : 'assets/dist/editor.css';
	if ( $wdod_editor_css ) {
		add_editor_style( $wdod_editor_css );
	}

	register_nav_menus(
		array(
			'primary' => esc_html__( 'Primary Menu', 'wdod-starter' ),
			'footer'  => esc_html__( 'Footer Menu', 'wdod-starter' ),
		)
	);

	add_image_size( 'wdod-card', 600, 400, true );
}
add_action( 'after_setup_theme', 'wdod_setup' );

/**
 * Expose the custom image size in the media picker.
 *
 * @param array $sizes Registered size labels.
 * @return array
 */
function wdod_image_size_names( $sizes ) {
	return array_merge(
		$sizes,
		array(
			'wdod-card' => esc_html__( 'Card (600x400)', 'wdod-starter' ),
		)
	);
}
add_filter( 'image_size_names_choose', 'wdod_image_size_names' );

/**
 * Register widget areas.
 *
 * @return void
 */
function wdod_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer', 'wdod-starter' ),
			'id'            => 'footer-1',
			'description'   => esc_html__( 'Widgets shown in the footer columns.', 'wdod-starter' ),
			'before_widget' => '<div id="%1$s" class="wdod-footer__widget widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="wdod-footer__widget-title widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'wdod_widgets_init' );

/**
 * Add helpful body classes.
 *
 * @param string[] $classes Existing classes.
 * @return string[]
 */
function wdod_body_classes( $classes ) {
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	if ( is_active_sidebar( 'footer-1' ) ) {
		$classes[] = 'has-footer-widgets';
	}

	if ( is_page_template( 'page-templates/full-width.php' ) ) {
		$classes[] = 'is-full-width';
	}

	return $classes;
}
add_filter( 'body_class', 'wdod_body_classes' );

/**
 * Add a pingback URL for singular content.
 *
 * @return void
 */
function wdod_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'wdod_pingback_header' );

/**
 * Trim the excerpt and replace the ellipsis.
 *
 * @param int $length Default length.
 * @return int
 */
function wdod_excerpt_length( $length ) {
	return is_admin() ? $length : 28;
}
add_filter( 'excerpt_length', 'wdod_excerpt_length' );

/**
 * Replace the excerpt "more" string.
 *
 * @param string $more Default string.
 * @return string
 */
function wdod_excerpt_more( $more ) {
	return is_admin() ? $more : '&hellip;';
}
add_filter( 'excerpt_more', 'wdod_excerpt_more' );
