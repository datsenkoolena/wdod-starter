<?php
/**
 * Template for single posts.
 *
 * Defers to an Elementor Theme Builder "single" template when one applies.
 *
 * @package WDOD_Starter
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'single' ) ) {
	get_footer();
	return;
}
?>

<main id="primary" class="wdod-main">
	<div class="wdod-container wdod-container--narrow">
		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', get_post_type() );

			the_post_navigation(
				array(
					'prev_text' => '<span class="wdod-post-nav__label">' . esc_html__( 'Previous', 'wdod-starter' ) . '</span> <span class="wdod-post-nav__title">%title</span>',
					'next_text' => '<span class="wdod-post-nav__label">' . esc_html__( 'Next', 'wdod-starter' ) . '</span> <span class="wdod-post-nav__title">%title</span>',
					'class'     => 'wdod-post-nav',
				)
			);

			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;
		endwhile;
		?>
	</div>
</main>

<?php
get_footer();
