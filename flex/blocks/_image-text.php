<?php
	/**
	 * Image + text content block — full-width banner image with WYSIWYG below.
	 */

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$content  = get_sub_field( 'content' );
	$image    = get_sub_field( 'image' );
	$slot     = (int) get_sub_field( 'seasonal_slot' );
	$urls     = prelaunch_block_media_urls( $image, $slot );
	$image_url = prelaunch_active_media_url( $urls );
	$image_id = ! empty( $image['ID'] ) ? absint( $image['ID'] ) : 0;

	if ( ! $content && ! $image_url ) {
		return;
	}
?>

<section class="py-14 md:py-20 wrap">
	<div class="grid-12">
		<div class="col-span-12">
			<?php if ( $image_url ) : ?>
				<div class="mb-8 image-text-banner">
					<img
						src="<?php echo esc_url( $image_url ); ?>"
						alt="<?php echo esc_attr( $image_id ? (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true ) : '' ); ?>"
						<?php echo prelaunch_image_dimensions_attr( $image ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>
						<?php echo prelaunch_seasonal_media_attributes( $urls ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>
						loading="lazy"
						decoding="async"
					>
				</div>
			<?php endif; ?>

			<?php if ( $content ) : ?>
				<div class="prose-theme">
					<?php echo wp_kses_post( $content ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
