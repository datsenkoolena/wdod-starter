<?php
/**
 * Template for archives (categories, tags, dates, authors, custom post types).
 *
 * Defers to an Elementor Theme Builder "archive" template when one applies.
 *
 * @package WDOD_Starter
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'archive' ) ) {
	get_footer();
	return;
}
?>

<main id="primary" class="wdod-main">
	<div class="wdod-container">
		<header class="wdod-page-header">
			<?php wdod_breadcrumbs(); ?>
			<?php the_archive_title( '<h1 class="wdod-page-header__title">', '</h1>' ); ?>
			<?php the_archive_description( '<div class="wdod-page-header__description">', '</div>' ); ?>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="wdod-grid wdod-grid--posts">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content', get_post_type() );
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
