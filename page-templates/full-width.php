<?php
/**
 * Template Name: Full Width
 * Template Post Type: page, post
 *
 * A page without the narrow content container. Intended for landing pages
 * built with blocks (wide/full alignments) or Elementor.
 *
 * @package WDOD_Starter
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="primary" class="wdod-main wdod-main--full-width">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'wdod-entry wdod-entry--full-width' ); ?>>
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
		</article>
		<?php
	endwhile;
	?>
</main>

<?php
get_footer();
