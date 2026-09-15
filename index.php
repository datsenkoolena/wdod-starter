<?php
/**
 * The main template file (blog index and generic fallback).
 *
 * @package WDOD_Starter
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="primary" class="wdod-main">
	<div class="wdod-container">
		<?php if ( is_home() && ! is_front_page() ) : ?>
			<header class="wdod-page-header">
				<h1 class="wdod-page-header__title"><?php single_post_title(); ?></h1>
			</header>
		<?php endif; ?>

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
