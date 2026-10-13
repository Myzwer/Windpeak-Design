<?php
	/**
	 * Lead Capture block — heading, laptop screenshot, copy + CTA.
	 */

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$intro          = get_sub_field( 'intro' );
	$shot           = get_sub_field( 'screenshot' );
	$shot_id        = ! empty( $shot['ID'] ) ? absint( $shot['ID'] ) : 0;
	$content        = get_sub_field( 'content' );
	$link           = get_sub_field( 'link' );
	$secondary_link = get_sub_field( 'secondary_link' );

	if ( ! $intro && ! $shot_id && ! $content && empty( $link['url'] ) && empty( $secondary_link['url'] ) ) {
		return;
	}
?>

<section class="py-14 md:py-20 wrap">
	<div class="md:gap-10 grid-12">
		<?php if ( $intro ) : ?>
			<div class="col-span-12 text-center">
				<div class="prose-theme">
					<?php echo wp_kses_post( $intro ); ?>
				</div>
			</div>
		<?php endif; ?>

		<div class="col-span-12 md:col-span-7">
			<?php if ( $shot_id ) : ?>
				<div class="laptop-frame">
					<?php
						echo wp_get_attachment_image(
							$shot_id,
							'large',
							false,
							array(
								'class'    => 'block w-full h-auto',
								'loading'  => 'lazy',
								'decoding' => 'async',
							)
						);
					?>
				</div>
			<?php endif; ?>
		</div>

		<div class="col-span-12 self-center md:col-span-5">
			<?php if ( $content ) : ?>
				<div class="mb-6 prose-theme">
					<?php echo wp_kses_post( $content ); ?>
				</div>
			<?php endif; ?>
			<?php if ( ! empty( $link['url'] ) || ! empty( $secondary_link['url'] ) ) : ?>
				<div class="flex flex-wrap gap-4">
					<?php if ( ! empty( $link['url'] ) ) : ?>
						<a
							class="border border-black shadow-md btn_main"
							href="<?php echo esc_url( $link['url'] ); ?>"
							<?php echo ! empty( $link['target'] ) ? ' target="' . esc_attr( $link['target'] ) . '" rel="noopener noreferrer"' : ''; ?>
						>
							<span><?php echo esc_html( $link['title'] ?: 'See The Demo' ); ?></span>
						</a>
					<?php endif; ?>
					<?php if ( ! empty( $secondary_link['url'] ) ) : ?>
						<a
							class="btn_ghost_black"
							href="<?php echo esc_url( $secondary_link['url'] ); ?>"
							<?php echo ! empty( $secondary_link['target'] ) ? ' target="' . esc_attr( $secondary_link['target'] ) . '" rel="noopener noreferrer"' : ''; ?>
						>
							<span><?php echo esc_html( $secondary_link['title'] ?: 'Button Label' ); ?></span>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
