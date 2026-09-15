<?php
/**
 * Default site footer (used unless Elementor Theme Builder overrides it).
 *
 * @package WDOD_Starter
 */

defined( 'ABSPATH' ) || exit;

$wdod_footer_text = wdod_option( 'footer_text', '' );
$wdod_phone       = wdod_option( 'phone', '' );
$wdod_email       = wdod_option( 'email', '' );
$wdod_socials     = function_exists( 'wdod_social_links' ) ? wdod_social_links() : array();
?>

<footer id="colophon" class="wdod-footer" role="contentinfo">
	<div class="wdod-container">
		<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
			<div class="wdod-footer__widgets">
				<?php dynamic_sidebar( 'footer-1' ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $wdod_phone || $wdod_email || $wdod_socials ) : ?>
			<div class="wdod-footer__contact">
				<?php if ( $wdod_phone ) : ?>
					<a class="wdod-footer__contact-item" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $wdod_phone ) ); ?>"><?php echo esc_html( $wdod_phone ); ?></a>
				<?php endif; ?>
				<?php if ( $wdod_email && is_email( $wdod_email ) ) : ?>
					<a class="wdod-footer__contact-item" href="mailto:<?php echo esc_attr( antispambot( $wdod_email ) ); ?>"><?php echo esc_html( antispambot( $wdod_email ) ); ?></a>
				<?php endif; ?>
				<?php if ( $wdod_socials ) : ?>
					<ul class="wdod-footer__social">
						<?php foreach ( $wdod_socials as $wdod_social ) : ?>
							<li>
								<a class="wdod-footer__social-link wdod-footer__social-link--<?php echo esc_attr( $wdod_social['network'] ); ?>" href="<?php echo esc_url( $wdod_social['url'] ); ?>" target="_blank" rel="noopener noreferrer">
									<?php echo esc_html( ucfirst( $wdod_social['network'] ) ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="wdod-footer__bottom">
			<?php
			if ( has_nav_menu( 'footer' ) ) {
				wp_nav_menu(
					array(
						'theme_location'  => 'footer',
						'menu_id'         => 'footer-menu',
						'menu_class'      => 'wdod-footer__menu',
						'container'       => 'nav',
						'container_class' => 'wdod-footer__nav',
						'fallback_cb'     => false,
						'depth'           => 1,
					)
				);
			}
			?>

			<div class="wdod-footer__text">
				<?php
				if ( $wdod_footer_text ) {
					echo wp_kses_post( $wdod_footer_text );
				} else {
					printf(
						/* translators: 1: current year, 2: site name. */
						esc_html__( '&copy; %1$s %2$s. All rights reserved.', 'wdod-starter' ),
						esc_html( gmdate( 'Y' ) ),
						esc_html( get_bloginfo( 'name' ) )
					);
				}
				?>
			</div>
		</div>
	</div>
</footer>
