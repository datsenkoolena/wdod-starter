<?php
/**
 * Shown when a loop has no results.
 *
 * @package WDOD_Starter
 */

defined( 'ABSPATH' ) || exit;
?>

<section class="wdod-no-results no-results not-found">
	<header class="wdod-page-header">
		<h2 class="wdod-page-header__title"><?php esc_html_e( 'Nothing here yet', 'wdod-starter' ); ?></h2>
	</header>

	<div class="wdod-no-results__content">
		<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
			<p>
				<?php
				printf(
					wp_kses(
						/* translators: %s: link to the new post screen. */
						__( 'Ready to publish your first post? <a href="%s">Get started here</a>.', 'wdod-starter' ),
						array( 'a' => array( 'href' => array() ) )
					),
					esc_url( admin_url( 'post-new.php' ) )
				);
				?>
			</p>
		<?php elseif ( is_search() ) : ?>
			<p><?php esc_html_e( 'Nothing matched your search. Try a different keyword or two.', 'wdod-starter' ); ?></p>
			<?php get_search_form(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'We could not find what you are looking for. Perhaps a search can help.', 'wdod-starter' ); ?></p>
			<?php get_search_form(); ?>
		<?php endif; ?>
	</div>
</section>
