<?php
	/**
	 * Accordion block with optional banner image and intro.
	 */

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$image    = get_sub_field( 'image' );
	$slot     = (int) get_sub_field( 'seasonal_slot' );
	$urls     = prelaunch_block_media_urls( $image, $slot );
	$image_url = prelaunch_active_media_url( $urls );
	$image_id = ! empty( $image['ID'] ) ? absint( $image['ID'] ) : 0;
	$intro    = get_sub_field( 'intro' );
	$has_items_rows = get_sub_field( 'accordion_items' ) ?: array();
	$has_items = ! empty( $has_items_rows );

	if ( ! $image_url && ! $intro && ! $has_items ) {
		return;
	}
?>

<section class="py-14 md:py-20 wrap accordion-block">
	<div class="md:gap-8 grid-12">
		<?php if ( $image_url ) : ?>
			<div class="col-span-12">
				<img
					src="<?php echo esc_url( $image_url ); ?>"
					alt="<?php echo esc_attr( $image_id ? (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true ) : '' ); ?>"
					class="object-cover w-full h-auto rounded-2xl max-h-[22rem]"
					<?php echo prelaunch_seasonal_media_attributes( $urls ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>
					loading="lazy"
					decoding="async"
				>
			</div>
		<?php endif; ?>

		<?php if ( $intro ) : ?>
			<div class="col-span-12 text-center">
				<div class="accordion-block__intro prose-theme">
					<?php echo wp_kses_post( $intro ); ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $has_items ) : ?>
			<div class="col-span-12">
				<div class="accordion" data-accordion>
					<?php while ( have_rows( 'accordion_items' ) ) : the_row(); ?>
						<?php
							$title   = get_sub_field( 'accordion_title' );
							$content = get_sub_field( 'accordion_content' );
						?>
						<?php if ( $title || $content ) : ?>
							<details class="accordion__item">
								<summary class="accordion__summary">
									<span class="accordion__title"><?php echo esc_html( $title ); ?></span>
								</summary>
								<div class="accordion__panel prose-theme prose-compact">
									<?php echo wp_kses_post( $content ); ?>
								</div>
							</details>
						<?php endif; ?>
					<?php endwhile; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
