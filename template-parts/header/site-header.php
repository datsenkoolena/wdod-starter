<?php
/**
 * Default site header (used unless Elementor Theme Builder overrides it).
 *
 * @package WDOD_Starter
 */

defined( 'ABSPATH' ) || exit;
?>

<header id="masthead" class="wdod-header" role="banner">
	<div class="wdod-container wdod-header__inner">
		<div class="wdod-header__brand">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<?php if ( is_front_page() && is_home() ) : ?>
					<h1 class="wdod-header__title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
				<?php else : ?>
					<p class="wdod-header__title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
				<?php endif; ?>
				<?php
				$wdod_description = get_bloginfo( 'description', 'display' );
				if ( $wdod_description || is_customize_preview() ) :
					?>
					<p class="wdod-header__tagline"><?php echo esc_html( $wdod_description ); ?></p>
				<?php endif; ?>
			<?php endif; ?>
		</div>

		<button class="wdod-header__toggle" type="button" aria-controls="site-navigation" aria-expanded="false" data-wdod-menu-toggle>
			<span class="wdod-header__toggle-bar" aria-hidden="true"></span>
			<span class="wdod-header__toggle-bar" aria-hidden="true"></span>
			<span class="wdod-header__toggle-bar" aria-hidden="true"></span>
			<span class="wdod-header__toggle-label"><?php esc_html_e( 'Menu', 'wdod-starter' ); ?></span>
		</button>

		<nav id="site-navigation" class="wdod-nav" aria-label="<?php esc_attr_e( 'Primary', 'wdod-starter' ); ?>" data-wdod-menu>
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'menu_id'        => 'primary-menu',
						'menu_class'     => 'wdod-nav__list',
						'container'      => false,
						'fallback_cb'    => false,
						'depth'          => 3,
					)
				);
			} elseif ( current_user_can( 'edit_theme_options' ) ) {
				printf(
					'<a class="wdod-nav__assign" href="%1$s">%2$s</a>',
					esc_url( admin_url( 'nav-menus.php' ) ),
					esc_html__( 'Assign a Primary menu', 'wdod-starter' )
				);
			}
			?>

			<div class="wdod-nav__extras">
				<?php wdod_language_switcher(); ?>
				<?php
				if ( function_exists( 'WC' ) && function_exists( 'wdod_woocommerce_cart_link' ) ) {
					wdod_woocommerce_cart_link();
				}
				?>
			</div>
		</nav>
	</div>
</header>
