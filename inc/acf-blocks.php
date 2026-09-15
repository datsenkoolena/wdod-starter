<?php
/**
 * ACF block registration and field helpers.
 *
 * Blocks live in blocks/acf/<name>/block.json and are registered with
 * register_block_type() so ACF's block.json support handles the rest.
 * Without ACF the theme renders nothing and shows a dismissible admin notice.
 *
 * @package WDOD_Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get a field value with an ACF-free fallback.
 *
 * @param string    $name    Field name.
 * @param int|false $post_id Post ID, or false for the current post.
 * @param mixed     $default Value returned when the field is empty.
 * @return mixed
 */
function wdod_get_field( $name, $post_id = false, $default = '' ) {
	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $name, $post_id );
	} else {
		$post_id = $post_id ? $post_id : get_the_ID();
		$value   = $post_id ? get_post_meta( (int) $post_id, $name, true ) : '';
	}

	if ( null === $value || '' === $value || false === $value || array() === $value ) {
		return $default;
	}

	return $value;
}

/**
 * Whether ACF Pro (block support) is available.
 *
 * @return bool
 */
function wdod_has_acf_blocks() {
	return function_exists( 'acf_register_block_type' );
}

/**
 * Dismiss the ACF notice for the current user.
 *
 * @return void
 */
function wdod_dismiss_acf_notice() {
	if ( ! isset( $_GET['wdod_dismiss_acf_notice'] ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	check_admin_referer( 'wdod_dismiss_acf_notice' );

	update_user_meta( get_current_user_id(), 'wdod_acf_notice_dismissed', 1 );

	wp_safe_redirect( remove_query_arg( array( 'wdod_dismiss_acf_notice', '_wpnonce' ) ) );
	exit;
}

/**
 * Recommend ACF Pro when it is not active.
 *
 * @return void
 */
function wdod_acf_missing_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( get_user_meta( get_current_user_id(), 'wdod_acf_notice_dismissed', true ) ) {
		return;
	}

	$dismiss_url = wp_nonce_url( add_query_arg( 'wdod_dismiss_acf_notice', '1' ), 'wdod_dismiss_acf_notice' );

	printf(
		'<div class="notice notice-info"><p><strong>%1$s</strong> %2$s <a href="%3$s">%4$s</a></p></div>',
		esc_html__( 'WDOD Starter:', 'wdod-starter' ),
		esc_html__( 'the Hero and Testimonials blocks and the Theme Settings page need Advanced Custom Fields PRO. The theme works without it, but those features stay hidden.', 'wdod-starter' ),
		esc_url( $dismiss_url ),
		esc_html__( 'Dismiss', 'wdod-starter' )
	);
}

if ( ! wdod_has_acf_blocks() ) {
	add_action( 'admin_init', 'wdod_dismiss_acf_notice' );
	add_action( 'admin_notices', 'wdod_acf_missing_notice' );
	return;
}

/**
 * Register every block that ships a block.json under blocks/acf.
 *
 * @return void
 */
function wdod_register_acf_blocks() {
	$block_dirs = glob( WDOD_STARTER_DIR . 'blocks/acf/*', GLOB_ONLYDIR );

	if ( empty( $block_dirs ) ) {
		return;
	}

	foreach ( $block_dirs as $dir ) {
		if ( file_exists( trailingslashit( $dir ) . 'block.json' ) ) {
			register_block_type( $dir );
		}
	}
}
add_action( 'init', 'wdod_register_acf_blocks', 5 );

/**
 * Add the "WDOD Blocks" inserter category.
 *
 * @param array $categories Registered categories.
 * @return array
 */
function wdod_block_categories( $categories ) {
	foreach ( $categories as $category ) {
		if ( isset( $category['slug'] ) && 'wdod' === $category['slug'] ) {
			return $categories;
		}
	}

	array_unshift(
		$categories,
		array(
			'slug'  => 'wdod',
			'title' => esc_html__( 'WDOD Blocks', 'wdod-starter' ),
			'icon'  => null,
		)
	);

	return $categories;
}
add_filter( 'block_categories_all', 'wdod_block_categories' );

/**
 * Save ACF field groups as JSON inside the theme.
 *
 * @return string
 */
function wdod_acf_json_save_point() {
	return WDOD_STARTER_DIR . 'acf-json';
}
add_filter( 'acf/settings/save_json', 'wdod_acf_json_save_point' );

/**
 * Load ACF field groups from the theme's acf-json folder.
 *
 * @param string[] $paths Existing load paths.
 * @return string[]
 */
function wdod_acf_json_load_point( $paths ) {
	$paths[] = WDOD_STARTER_DIR . 'acf-json';

	return array_unique( $paths );
}
add_filter( 'acf/settings/load_json', 'wdod_acf_json_load_point' );

/**
 * Build the wrapper attributes for a block render template.
 *
 * @param array  $block      ACF block array.
 * @param string $base_class Base CSS class for the block.
 * @param array  $extra      Extra classes.
 * @return string Escaped attribute string.
 */
function wdod_block_wrapper_attributes( $block, $base_class, $extra = array() ) {
	$id = ! empty( $block['anchor'] ) ? $block['anchor'] : $base_class . '-' . ( isset( $block['id'] ) ? $block['id'] : wp_unique_id() );

	$classes = array_merge( array( $base_class ), (array) $extra );

	if ( ! empty( $block['className'] ) ) {
		$classes[] = $block['className'];
	}

	if ( ! empty( $block['align'] ) ) {
		$classes[] = 'align' . $block['align'];
	}

	$classes = array_filter( array_map( 'sanitize_html_class', preg_split( '/\s+/', implode( ' ', $classes ) ) ) );

	return sprintf( 'id="%1$s" class="%2$s"', esc_attr( $id ), esc_attr( implode( ' ', array_unique( $classes ) ) ) );
}
