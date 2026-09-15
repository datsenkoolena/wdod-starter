<?php
/**
 * WPML helpers. All of them degrade gracefully when WPML is inactive.
 *
 * @package WDOD_Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether WPML is active.
 *
 * @return bool
 */
function wdod_is_wpml_active() {
	return defined( 'ICL_SITEPRESS_VERSION' ) || class_exists( 'SitePress' );
}

/**
 * Current language code ("en", "de", ...).
 *
 * @return string
 */
function wdod_current_lang() {
	$lang = apply_filters( 'wpml_current_language', null );

	if ( is_string( $lang ) && '' !== $lang ) {
		return $lang;
	}

	return substr( get_locale(), 0, 2 );
}

/**
 * Get the translated object ID for the current language.
 *
 * @param int    $id   Original object ID.
 * @param string $type Element type: post type or taxonomy slug.
 * @return int
 */
function wdod_translated_id( $id, $type = 'page' ) {
	$translated = apply_filters( 'wpml_object_id', (int) $id, $type, true );

	return $translated ? (int) $translated : (int) $id;
}

/**
 * Get the permalink of a page in the current language.
 *
 * @param int $id Original page ID.
 * @return string
 */
function wdod_translated_permalink( $id ) {
	$url = get_permalink( wdod_translated_id( $id, 'page' ) );

	return $url ? $url : '';
}

/**
 * Print the WPML language switcher when available.
 *
 * @return void
 */
function wdod_language_switcher() {
	if ( ! has_action( 'wpml_add_language_selector' ) ) {
		return;
	}

	echo '<div class="wdod-language-switcher">';
	do_action( 'wpml_add_language_selector' );
	echo '</div>';
}

/**
 * Get a value that WPML can translate via wpml-config.xml admin-texts.
 *
 * Provided as a thin wrapper so templates never call WPML directly.
 *
 * @param string $value   String to translate.
 * @param string $name    String name registered with WPML.
 * @param string $context String context (defaults to the theme).
 * @return string
 */
function wdod_translate_string( $value, $name, $context = 'WDOD Starter' ) {
	return (string) apply_filters( 'wpml_translate_single_string', $value, $context, $name );
}
