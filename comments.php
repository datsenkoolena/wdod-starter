<?php
/**
 * Comments list and form.
 *
 * @package WDOD_Starter
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>

<section id="comments" class="wdod-comments comments-area">
	<?php if ( have_comments() ) : ?>
		<h2 class="wdod-comments__title comments-title">
			<?php
			$wdod_comment_count = get_comments_number();

			if ( '1' === (string) $wdod_comment_count ) {
				printf(
					/* translators: %s: post title. */
					esc_html__( 'One comment on &ldquo;%s&rdquo;', 'wdod-starter' ),
					'<span>' . wp_kses_post( get_the_title() ) . '</span>'
				);
			} else {
				printf(
					/* translators: 1: number of comments, 2: post title. */
					esc_html( _n( '%1$s comment on &ldquo;%2$s&rdquo;', '%1$s comments on &ldquo;%2$s&rdquo;', (int) $wdod_comment_count, 'wdod-starter' ) ),
					esc_html( number_format_i18n( $wdod_comment_count ) ),
					'<span>' . wp_kses_post( get_the_title() ) . '</span>'
				);
			}
			?>
		</h2>

		<?php the_comments_navigation( array( 'class' => 'wdod-comments__nav' ) ); ?>

		<ol class="wdod-comments__list comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 48,
				)
			);
			?>
		</ol>

		<?php the_comments_navigation( array( 'class' => 'wdod-comments__nav' ) ); ?>

		<?php if ( ! comments_open() ) : ?>
			<p class="wdod-comments__closed no-comments"><?php esc_html_e( 'Comments are closed.', 'wdod-starter' ); ?></p>
		<?php endif; ?>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'class_container' => 'wdod-comments__form comment-respond',
			'title_reply'     => esc_html__( 'Leave a comment', 'wdod-starter' ),
			'class_submit'    => 'wdod-button submit',
		)
	);
	?>
</section>
