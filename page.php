<?php
/**
 * Template for static pages.
 *
 * @package WDOD_Starter
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="primary" class="wdod-main">
	<div class="wdod-container wdod-container--narrow">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'wdod-entry wdod-entry--page' ); ?>>
				<header class="wdod-entry__header">
					<?php wdod_breadcrumbs(); ?>
					<?php the_title( '<h1 class="wdod-entry__title entry-title">', '</h1>' ); ?>
				</header>

				<?php wdod_post_thumbnail( 'large' ); ?>

				<div class="wdod-entry__content entry-content">
					<?php
					the_content();

					wp_link_pages(
						array(
							'before' => '<div class="wdod-page-links">' . esc_html__( 'Pages:', 'wdod-starter' ),
							'after'  => '</div>',
						)
					);
					?>
				</div>

				<?php
				if ( get_edit_post_link() ) :
					?>
					<footer class="wdod-entry__footer">
						<?php wdod_entry_footer(); ?>
					</footer>
				<?php endif; ?>
			</article>

			<?php
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;
		endwhile;
		?>
	</div>
</main>

<?php
get_footer();
