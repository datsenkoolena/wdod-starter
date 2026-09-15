<?php
/**
 * WDOD Hero block template.
 *
 * @package WDOD_Starter
 *
 * @var array  $block      The block settings and attributes.
 * @var string $content    Inner block content (unused, jsx disabled).
 * @var bool   $is_preview True while rendering in the editor.
 * @var int    $post_id    ID of the post being edited/rendered.
 */

defined( 'ABSPATH' ) || exit;

$wdod_eyebrow = wdod_get_field( 'eyebrow' );
$wdod_heading = wdod_get_field( 'heading' );
$wdod_text    = wdod_get_field( 'text' );
$wdod_button  = wdod_get_field( 'button', false, array() );
$wdod_image   = wdod_get_field( 'background_image', false, array() );
$wdod_overlay = (int) wdod_get_field( 'overlay_opacity', false, 50 );

// Editor placeholder so the block is never invisible while empty.
if ( ! empty( $is_preview ) && ! $wdod_heading && ! $wdod_text && ! $wdod_eyebrow ) {
	$wdod_eyebrow = esc_html__( 'WDOD Hero', 'wdod-starter' );
	$wdod_heading = esc_html__( 'Add a headline in the sidebar', 'wdod-starter' );
	$wdod_text    = esc_html__( 'Fill in the Hero fields to replace this placeholder. Choose a background image and a button link for the full effect.', 'wdod-starter' );
}

if ( ! $wdod_heading && ! $wdod_text && ! $wdod_eyebrow ) {
	return;
}

$wdod_classes = array();
if ( ! empty( $wdod_image['url'] ) ) {
	$wdod_classes[] = 'has-background';
}

$wdod_style = sprintf( '--wdod-hero-overlay:%s;', esc_attr( max( 0, min( 100, $wdod_overlay ) ) / 100 ) );

$wdod_button_url    = ! empty( $wdod_button['url'] ) ? $wdod_button['url'] : '';
$wdod_button_label  = ! empty( $wdod_button['title'] ) ? $wdod_button['title'] : esc_html__( 'Learn more', 'wdod-starter' );
$wdod_button_target = ! empty( $wdod_button['target'] ) ? $wdod_button['target'] : '';
?>
<section <?php echo wdod_block_wrapper_attributes( $block, 'wdod-hero', $wdod_classes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?> style="<?php echo esc_attr( $wdod_style ); ?>">
	<?php if ( ! empty( $wdod_image['ID'] ) ) : ?>
		<div class="wdod-hero__media" aria-hidden="true">
			<?php
			echo wp_get_attachment_image(
				(int) $wdod_image['ID'],
				'full',
				false,
				array(
					'class'   => 'wdod-hero__image',
					'loading' => 'eager',
					'alt'     => '',
				)
			);
			?>
		</div>
	<?php endif; ?>

	<div class="wdod-hero__inner wdod-container">
		<?php if ( $wdod_eyebrow ) : ?>
			<p class="wdod-hero__eyebrow"><?php echo esc_html( $wdod_eyebrow ); ?></p>
		<?php endif; ?>

		<?php if ( $wdod_heading ) : ?>
			<h2 class="wdod-hero__heading"><?php echo esc_html( $wdod_heading ); ?></h2>
		<?php endif; ?>

		<?php if ( $wdod_text ) : ?>
			<div class="wdod-hero__text"><?php echo wp_kses_post( wpautop( $wdod_text ) ); ?></div>
		<?php endif; ?>

		<?php if ( $wdod_button_url ) : ?>
			<p class="wdod-hero__actions">
				<a class="wdod-button wdod-button--accent wdod-hero__button" href="<?php echo esc_url( $wdod_button_url ); ?>"
					<?php if ( '_blank' === $wdod_button_target ) : ?>
						target="_blank" rel="noopener noreferrer"
					<?php endif; ?>
				>
					<?php echo esc_html( $wdod_button_label ); ?>
				</a>
			</p>
		<?php endif; ?>
	</div>
</section>
