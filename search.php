<?php
/**
 * Template for search results.
 *
 * @package WDOD_Starter
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="primary" class="wdod-main">
	<div class="wdod-container">
		<header class="wdod-page-header">
			<?php wdod_breadcrumbs(); ?>
			<h1 class="wdod-page-header__title">
				<?php
				printf(
					/* translators: %s: search query. */
					esc_html__( 'Search results for: %s', 'wdod-starter' ),
					'<span class="wdod-page-header__query">' . esc_html( get_search_query() ) . '</span>'
				);
				?>
			</h1>
			<div class="wdod-page-header__search">
				<?php get_search_form(); ?>
			</div>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="wdod-grid wdod-grid--posts">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content', 'search' );
				endwhile;
				?>
			</div>

			<?php wdod_pagination(); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content', 'none' ); ?>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
