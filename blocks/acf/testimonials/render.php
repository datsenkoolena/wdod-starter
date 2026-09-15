<?php
/**
 * WDOD Testimonials block template.
 *
 * @package WDOD_Starter
 *
 * @var array  $block      The block settings and attributes.
 * @var string $content    Inner block content (unused, jsx disabled).
 * @var bool   $is_preview True while rendering in the editor.
 * @var int    $post_id    ID of the post being edited/rendered.
 */

defined( 'ABSPATH' ) || exit;

$wdod_heading = wdod_get_field( 'heading' );
$wdod_items   = wdod_get_field( 'items', false, array() );

if ( ! is_array( $wdod_items ) ) {
	$wdod_items = array();
}

// Editor placeholder rows so the layout is visible before content exists.
if ( ! empty( $is_preview ) && empty( $wdod_items ) ) {
	$wdod_heading = $wdod_heading ? $wdod_heading : esc_html__( 'What clients say', 'wdod-starter' );
	$wdod_items   = array(
		array(
			'quote'  => esc_html__( 'Add testimonials in the block sidebar. Each row needs a quote and an author; role and avatar are optional.', 'wdod-starter' ),
			'author' => esc_html__( 'Placeholder Author', 'wdod-starter' ),
			'role'   => esc_html__( 'Role, Company', 'wdod-starter' ),
			'avatar' => array(),
		),
		array(
			'quote'  => esc_html__( 'Use the arrows or swipe to move between items. Scrolling snaps to each card.', 'wdod-starter' ),
			'author' => esc_html__( 'Second Placeholder', 'wdod-starter' ),
			'role'   => '',
			'avatar' => array(),
		),
	);
}

$wdod_items = array_values(
	array_filter(
		$wdod_items,
		static function ( $item ) {
			return is_array( $item ) && ! empty( $item['quote'] );
		}
	)
);

if ( empty( $wdod_items ) ) {
	return;
}

$wdod_count   = count( $wdod_items );
$wdod_classes = array( 1 === $wdod_count ? 'has-one-item' : 'has-many-items' );
?>
<section <?php echo wdod_block_wrapper_attributes( $block, 'wdod-testimonials', $wdod_classes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?> data-wdod-testimonials>
	<div class="wdod-container">
		<?php if ( $wdod_heading ) : ?>
			<h2 class="wdod-testimonials__heading"><?php echo esc_html( $wdod_heading ); ?></h2>
		<?php endif; ?>

		<div class="wdod-testimonials__track" data-wdod-testimonials-track tabindex="0" aria-label="<?php esc_attr_e( 'Testimonials', 'wdod-starter' ); ?>">
			<?php foreach ( $wdod_items as $wdod_index => $wdod_item ) : ?>
				<?php
				$wdod_quote  = $wdod_item['quote'];
				$wdod_author = isset( $wdod_item['author'] ) ? $wdod_item['author'] : '';
				$wdod_role   = isset( $wdod_item['role'] ) ? $wdod_item['role'] : '';
				$wdod_avatar = isset( $wdod_item['avatar'] ) && is_array( $wdod_item['avatar'] ) ? $wdod_item['avatar'] : array();
				?>
				<figure class="wdod-testimonials__item" data-wdod-testimonials-item aria-roledescription="<?php esc_attr_e( 'slide', 'wdod-starter' ); ?>" aria-label="<?php echo esc_attr( sprintf( '%1$d / %2$d', $wdod_index + 1, $wdod_count ) ); ?>">
					<blockquote class="wdod-testimonials__quote">
						<?php echo wp_kses_post( wpautop( $wdod_quote ) ); ?>
					</blockquote>

					<?php if ( $wdod_author || $wdod_role || ! empty( $wdod_avatar['ID'] ) ) : ?>
						<figcaption class="wdod-testimonials__meta">
							<?php if ( ! empty( $wdod_avatar['ID'] ) ) : ?>
								<?php
								echo wp_get_attachment_image(
									(int) $wdod_avatar['ID'],
									'thumbnail',
									false,
									array(
										'class'   => 'wdod-testimonials__avatar',
										'loading' => 'lazy',
									)
								);
								?>
							<?php endif; ?>
							<span class="wdod-testimonials__who">
								<?php if ( $wdod_author ) : ?>
									<span class="wdod-testimonials__author"><?php echo esc_html( $wdod_author ); ?></span>
								<?php endif; ?>
								<?php if ( $wdod_role ) : ?>
									<span class="wdod-testimonials__role"><?php echo esc_html( $wdod_role ); ?></span>
								<?php endif; ?>
							</span>
						</figcaption>
					<?php endif; ?>
				</figure>
			<?php endforeach; ?>
		</div>

		<?php if ( $wdod_count > 1 ) : ?>
			<div class="wdod-testimonials__controls">
				<button class="wdod-testimonials__button wdod-testimonials__button--prev" type="button" data-wdod-testimonials-prev aria-label="<?php esc_attr_e( 'Previous testimonial', 'wdod-starter' ); ?>">
					<span aria-hidden="true">&larr;</span>
				</button>
				<span class="wdod-testimonials__status" data-wdod-testimonials-status aria-live="polite">1 / <?php echo esc_html( $wdod_count ); ?></span>
				<button class="wdod-testimonials__button wdod-testimonials__button--next" type="button" data-wdod-testimonials-next aria-label="<?php esc_attr_e( 'Next testimonial', 'wdod-starter' ); ?>">
					<span aria-hidden="true">&rarr;</span>
				</button>
			</div>
		<?php endif; ?>
	</div>
</section>
