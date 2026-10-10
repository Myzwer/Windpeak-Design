<?php
	/**
	 * Headers and Text block — side image + labeled stacked items.
	 */

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$header     = get_sub_field( 'header' );
	$image      = get_sub_field( 'image' );
	$slot       = (int) get_sub_field( 'seasonal_slot' );
	$urls       = prelaunch_block_media_urls( $image, $slot );
	$image_url  = prelaunch_active_media_url( $urls );
	$image_id   = ! empty( $image['ID'] ) ? absint( $image['ID'] ) : 0;
	$image_side = get_sub_field( 'image_side' ) ?: 'right';
	$link       = get_sub_field( 'link' );
	$has_items_rows = get_sub_field( 'items' ) ?: array();
	$has_items = ! empty( $has_items_rows );

	if ( ! $header && ! $image_url && ! $has_items && empty( $link['url'] ) ) {
		return;
	}

	$image_first = ( 'left' === $image_side );

	// Skip srcset: the strip is only ~2 cols wide, so browsers pick a tiny
	// candidate and object-cover pixelates the tall crop. Force full source.
	$image_alt  = $image_id ? (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true ) : '';
	$image_attr = prelaunch_seasonal_media_attributes( $urls );
?>

<section class="overflow-hidden">
	<div class="grid grid-cols-12 min-h-[28rem] lg:min-h-[36rem]">
		<?php if ( $image_url && $image_first ) : ?>
			<div class="relative col-span-2 lg:col-span-4">
				<img
					src="<?php echo esc_url( $image_url ); ?>"
					alt="<?php echo esc_attr( $image_alt ); ?>"
					class="object-cover absolute inset-0 w-full h-full"
					<?php echo $image_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>
					loading="lazy"
					decoding="async"
				>
			</div>
		<?php endif; ?>

		<div class="px-5 py-14 lg:px-12 lg:py-20 <?php echo $image_url ? 'col-span-10 lg:col-span-8' : 'col-span-12 wrap'; ?>">
			<div class="<?php echo $image_url ? 'max-w-3xl' : 'wrap max-w-3xl px-0'; ?>">
				<?php if ( $header ) : ?>
					<p class="mb-8 text-sm font-bold tracking-wide uppercase"><?php echo esc_html( $header ); ?></p>
				<?php endif; ?>

				<?php if ( $has_items ) : ?>
					<div class="flex flex-col gap-10">
						<?php while ( have_rows( 'items' ) ) : the_row(); ?>
							<?php
								$title = get_sub_field( 'item_title' );
								$text  = get_sub_field( 'item_text' );
							?>
							<div>
								<?php if ( $title ) : ?>
									<h2 class="mb-3 heading-2"><?php echo esc_html( $title ); ?></h2>
								<?php endif; ?>
								<?php if ( $text ) : ?>
									<div class="prose-theme prose-compact">
										<?php echo wp_kses_post( $text ); ?>
									</div>
								<?php endif; ?>
							</div>
						<?php endwhile; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $link['url'] ) ) : ?>
					<div class="mt-10">
						<a
							class="border border-black shadow-md btn_main"
							href="<?php echo esc_url( $link['url'] ); ?>"
							<?php echo ! empty( $link['target'] ) ? ' target="' . esc_attr( $link['target'] ) . '" rel="noopener noreferrer"' : ''; ?>
						>
							<span><?php echo esc_html( $link['title'] ?: 'Learn More' ); ?></span>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $image_url && ! $image_first ) : ?>
			<div class="relative col-span-2 lg:col-span-4">
				<img
					src="<?php echo esc_url( $image_url ); ?>"
					alt="<?php echo esc_attr( $image_alt ); ?>"
					class="object-cover absolute inset-0 w-full h-full"
					<?php echo $image_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>
					loading="lazy"
					decoding="async"
				>
			</div>
		<?php endif; ?>
	</div>
</section>
