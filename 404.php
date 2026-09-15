<?php
/**
 * Template for "page not found" responses.
 *
 * @package WDOD_Starter
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="primary" class="wdod-main">
	<div class="wdod-container wdod-container--narrow">
		<section class="wdod-404">
			<header class="wdod-page-header">
				<p class="wdod-404__code" aria-hidden="true">404</p>
				<h1 class="wdod-page-header__title"><?php esc_html_e( 'That page could not be found.', 'wdod-starter' ); ?></h1>
			</header>

			<div class="wdod-404__content">
				<p><?php esc_html_e( 'The link may be out of date, or the page may have moved. Try a search, or head back to the homepage.', 'wdod-starter' ); ?></p>

				<?php get_search_form(); ?>

				<p>
					<a class="wdod-button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to homepage', 'wdod-starter' ); ?></a>
				</p>
			</div>
		</section>
	</div>
</main>

<?php
get_footer();
