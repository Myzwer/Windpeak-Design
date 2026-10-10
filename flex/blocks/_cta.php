<?php
	/**
	 * CTA block.
	 *
	 * Split layout: stacked heading on the left, supporting line + button on the right.
	 */

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$heading = get_sub_field( 'heading' );
	$content = get_sub_field( 'content' );
	$link    = get_sub_field( 'link' );

	// Plain text field; strip leftover HTML if the field was previously a WYSIWYG.
	$content = is_string( $content ) ? trim( wp_strip_all_tags( $content ) ) : '';

	if ( ! $heading && ! $content && empty( $link['url'] ) ) {
		return;
	}
?>

<section class="py-14 md:py-20 wrap">
	<div class="items-center md:gap-8 grid-12">
		<div class="col-span-12 md:col-span-6">
			<?php if ( $heading ) : ?>
				<h2 class="heading-1">
					<?php echo nl2br( esc_html( $heading ) ); ?>
				</h2>
			<?php endif; ?>
		</div>

		<div class="col-span-12 md:col-span-4 md:col-start-9">
			<?php if ( $content ) : ?>
				<p class="mb-5 text-xl leading-snug md:text-2xl">
					<?php echo esc_html( $content ); ?>
				</p>
			<?php endif; ?>

			<?php if ( ! empty( $link['url'] ) ) : ?>
				<a
					class="border border-black shadow-md btn_main"
					href="<?php echo esc_url( $link['url'] ); ?>"
					<?php echo ! empty( $link['target'] ) ? ' target="' . esc_attr( $link['target'] ) . '" rel="noopener noreferrer"' : ''; ?>
				>
					<span><?php echo esc_html( $link['title'] ?: 'Learn More' ); ?></span>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
