<?php
/**
 * WooCommerce integration.
 *
 * @package WDOD_Starter
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/**
 * Declare WooCommerce support and gallery features.
 *
 * @return void
 */
function wdod_woocommerce_setup() {
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 600,
			'single_image_width'    => 900,
			'product_grid'          => array(
				'default_rows'    => 4,
				'min_rows'        => 1,
				'default_columns' => 3,
				'min_columns'     => 1,
				'max_columns'     => 4,
			),
		)
	);

	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'wdod_woocommerce_setup' );

/*
 * Replace WooCommerce's default content wrappers with the theme's own.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/**
 * Open the theme wrapper around WooCommerce pages.
 *
 * @return void
 */
function wdod_woocommerce_wrapper_before() {
	echo '<main id="primary" class="wdod-main wdod-main--woocommerce"><div class="wdod-container">';
}
add_action( 'woocommerce_before_main_content', 'wdod_woocommerce_wrapper_before', 10 );

/**
 * Close the theme wrapper.
 *
 * @return void
 */
function wdod_woocommerce_wrapper_after() {
	echo '</div></main>';
}
add_action( 'woocommerce_after_main_content', 'wdod_woocommerce_wrapper_after', 10 );

/**
 * Hide the default shop page title (the theme prints its own hero/breadcrumbs).
 *
 * @return bool
 */
function wdod_woocommerce_hide_page_title() {
	return false;
}
add_filter( 'woocommerce_show_page_title', 'wdod_woocommerce_hide_page_title' );

/**
 * Products per row.
 *
 * @return int
 */
function wdod_woocommerce_loop_columns() {
	return 3;
}
add_filter( 'loop_shop_columns', 'wdod_woocommerce_loop_columns' );

/**
 * Products per page.
 *
 * @return int
 */
function wdod_woocommerce_products_per_page() {
	return 12;
}
add_filter( 'loop_shop_per_page', 'wdod_woocommerce_products_per_page' );

/**
 * Add a body class so styles can target shop pages.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function wdod_woocommerce_body_class( $classes ) {
	$classes[] = 'wdod-woocommerce-active';

	return $classes;
}
add_filter( 'body_class', 'wdod_woocommerce_body_class' );

/**
 * Render the header mini-cart link.
 *
 * @return void
 */
function wdod_woocommerce_cart_link() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return;
	}

	$count = (int) WC()->cart->get_cart_contents_count();
	?>
	<a class="wdod-cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'wdod-starter' ); ?>">
		<span class="screen-reader-text"><?php esc_html_e( 'Cart', 'wdod-starter' ); ?></span>
		<svg class="wdod-cart-link__icon" aria-hidden="true" focusable="false" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
		<span class="wdod-cart-link__count" data-count="<?php echo esc_attr( $count ); ?>">
			<?php
			printf(
				/* translators: %d: number of items in the cart. */
				esc_html( _n( '%d item', '%d items', $count, 'wdod-starter' ) ),
				(int) $count
			);
			?>
		</span>
	</a>
	<?php
}

/**
 * Keep the mini-cart link fresh after AJAX add-to-cart.
 *
 * @param array $fragments Cart fragments.
 * @return array
 */
function wdod_woocommerce_cart_fragments( $fragments ) {
	ob_start();
	wdod_woocommerce_cart_link();
	$fragments['a.wdod-cart-link'] = ob_get_clean();

	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'wdod_woocommerce_cart_fragments' );

/**
 * Register a shop sidebar for filters and widgets.
 *
 * @return void
 */
function wdod_woocommerce_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Shop Sidebar', 'wdod-starter' ),
			'id'            => 'shop-sidebar',
			'description'   => esc_html__( 'Filters and widgets shown on shop and product archives.', 'wdod-starter' ),
			'before_widget' => '<div id="%1$s" class="wdod-shop-sidebar__widget widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="wdod-shop-sidebar__title widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'wdod_woocommerce_widgets_init' );

/**
 * Print the shop sidebar below the archive loop.
 *
 * @return void
 */
function wdod_woocommerce_sidebar() {
	if ( ! is_active_sidebar( 'shop-sidebar' ) || ! ( is_shop() || is_product_taxonomy() ) ) {
		return;
	}

	echo '<aside class="wdod-shop-sidebar" aria-label="' . esc_attr__( 'Shop sidebar', 'wdod-starter' ) . '">';
	dynamic_sidebar( 'shop-sidebar' );
	echo '</aside>';
}
add_action( 'woocommerce_after_main_content', 'wdod_woocommerce_sidebar', 5 );
