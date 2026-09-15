<?php
/**
 * Theme Settings (ACF options page) and the wdod_option() accessor.
 *
 * @package WDOD_Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Read a theme option with or without ACF.
 *
 * With ACF the value comes from the "Theme Settings" options page. Without it
 * the raw option ACF would have written ("options_<key>") is read directly, so
 * values survive a plugin swap.
 *
 * @param string $key     Field name.
 * @param mixed  $default Fallback value.
 * @return mixed
 */
function wdod_option( $key, $default = '' ) {
	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $key, 'option' );
	} else {
		$value = get_option( 'options_' . $key, $default );
	}

	if ( null === $value || '' === $value || false === $value || array() === $value ) {
		$value = $default;
	}

	/**
	 * Filter a theme option value.
	 *
	 * @param mixed  $value Option value.
	 * @param string $key   Field name.
	 */
	return apply_filters( 'wdod_option', $value, $key );
}

/**
 * Register the ACF options page.
 *
 * @return void
 */
function wdod_register_options_page() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		array(
			'page_title' => esc_html__( 'Theme Settings', 'wdod-starter' ),
			'menu_title' => esc_html__( 'Theme Settings', 'wdod-starter' ),
			'menu_slug'  => 'theme-settings',
			'capability' => 'edit_theme_options',
			'position'   => 61,
			'icon_url'   => 'dashicons-admin-customizer',
			'redirect'   => false,
			'autoload'   => true,
		)
	);
}
add_action( 'acf/init', 'wdod_register_options_page' );

/**
 * Get the social links defined in Theme Settings.
 *
 * @return array<int,array{network:string,url:string}>
 */
function wdod_social_links() {
	$rows  = wdod_option( 'social', array() );
	$links = array();

	if ( ! is_array( $rows ) ) {
		return $links;
	}

	foreach ( $rows as $row ) {
		if ( empty( $row['url'] ) ) {
			continue;
		}

		$links[] = array(
			'network' => isset( $row['network'] ) ? sanitize_key( $row['network'] ) : 'link',
			'url'     => esc_url_raw( $row['url'] ),
		);
	}

	return $links;
}
