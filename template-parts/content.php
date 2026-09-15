<?php
/**
 * Post content: a card in loops, the full article on singular views.
 *
 * @package WDOD_Starter
 */

defined( 'ABSPATH' ) || exit;

$wdod_is_single = is_singular();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $wdod_is_single ? 'wdod-entry wdod-entry--single' : 'wdod-entry wdod-card' ); ?>>
	<?php if ( $wdod_is_single ) : ?>
		<header class="wdod-entry__header">
			<?php wdod_breadcrumbs(); ?>
			<?php the_title( '<h1 class="wdod-entry__title entry-title">', '</h1>' ); ?>
			<?php if ( 'post' === get_post_type() ) : ?>
				<div class="wdod-entry__meta wdod-meta entry-meta">
					<?php wdod_posted_on(); ?>
				</div>
			<?php endif; ?>
		</header>

		<?php wdod_post_thumbnail( 'large' ); ?>

		<div class="wdod-entry__content entry-content">
			<?php
			the_content(
				sprintf(
					/* translators: %s: post title. */
					esc_html__( 'Continue reading %s', 'wdod-starter' ),
					'<span class="screen-reader-text">' . get_the_title() . '</span>'
				)
			);

			wp_link_pages(
				array(
					'before' => '<div class="wdod-page-links">' . esc_html__( 'Pages:', 'wdod-starter' ),
					'after'  => '</div>',
				)
			);
			?>
		</div>

		<footer class="wdod-entry__footer">
			<?php wdod_entry_footer(); ?>
		</footer>
	<?php else : ?>
		<?php wdod_post_thumbnail( 'wdod-card' ); ?>

		<div class="wdod-card__body">
			<?php if ( 'post' === get_post_type() ) : ?>
				<div class="wdod-card__meta wdod-meta entry-meta">
					<?php wdod_posted_on(); ?>
				</div>
			<?php endif; ?>

			<?php the_title( sprintf( '<h2 class="wdod-card__title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

			<div class="wdod-card__excerpt entry-summary">
				<?php the_excerpt(); ?>
			</div>

			<a class="wdod-card__more" href="<?php the_permalink(); ?>">
				<?php esc_html_e( 'Read more', 'wdod-starter' ); ?>
				<span class="screen-reader-text"><?php echo esc_html( get_the_title() ); ?></span>
				<span aria-hidden="true">&rarr;</span>
			</a>
		</div>
	<?php endif; ?>
</article>
