<?php
/**
 * Elementor integration: Theme Builder locations.
 *
 * @package WDOD_Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register all core Theme Builder locations (header, footer, single, archive).
 *
 * @param \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $manager Locations manager.
 * @return void
 */
function wdod_elementor_register_locations( $manager ) {
	$manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'wdod_elementor_register_locations' );

/**
 * Whether an Elementor Theme Builder template replaced a location.
 *
 * Safe to call without Elementor Pro; always returns false then.
 *
 * @param string $location Location name.
 * @return bool
 */
function wdod_elementor_location( $location ) {
	if ( ! function_exists( 'elementor_theme_do_location' ) ) {
		return false;
	}

	return (bool) elementor_theme_do_location( $location );
}

/**
 * Expose the theme.json palette to Elementor's default colour picker.
 *
 * @return void
 */
function wdod_elementor_default_colors() {
	if ( ! class_exists( '\Elementor\Plugin' ) || ! function_exists( 'wp_get_global_settings' ) ) {
		return;
	}

	$palette = wp_get_global_settings( array( 'color', 'palette', 'theme' ) );

	if ( empty( $palette ) || ! is_array( $palette ) ) {
		return;
	}

	$kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit();

	if ( ! $kit || ! is_callable( array( $kit, 'get_settings' ) ) ) {
		return;
	}

	if ( ! empty( $kit->get_settings( 'custom_colors' ) ) ) {
		return; // Never overwrite user-defined kit colours.
	}

	$custom_colors = array();
	foreach ( $palette as $color ) {
		if ( empty( $color['slug'] ) || empty( $color['color'] ) ) {
			continue;
		}

		$custom_colors[] = array(
			'_id'   => 'wdod_' . sanitize_key( $color['slug'] ),
			'title' => isset( $color['name'] ) ? $color['name'] : ucfirst( $color['slug'] ),
			'color' => $color['color'],
		);
	}

	if ( $custom_colors && is_callable( array( $kit, 'update_settings' ) ) ) {
		$kit->update_settings( array( 'custom_colors' => $custom_colors ) );
	}
}
add_action( 'after_switch_theme', 'wdod_elementor_default_colors' );
