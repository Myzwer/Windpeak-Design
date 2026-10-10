<?php
	/**
	 * Simple header.
	 *
	 * Full-bleed primary topo band with left-aligned eyebrow, title, and CTA.
	 * Transparent navbar overlays the same band (see background.css).
	 */

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$eyebrow = get_sub_field( 'eyebrow' );
	$heading = get_sub_field( 'title' );
	$button  = get_sub_field( 'button' );

	$topo_urls       = prelaunch_topography_urls();
	$section_classes = 'simple-header section bg-primary-gradient';

	if ( '' !== $topo_urls['brand'] || '' !== $topo_urls['season'] ) {
		$section_classes .= ' bg-texture';
	}
?>

<section class="<?php echo esc_attr( $section_classes ); ?>">
	<div class="simple-header-shell">
		<div class="wrap-wide grid-12 !px-8 lg:!px-12">
			<div class="grid col-span-12 justify-items-start text-left">

				<?php if ( $eyebrow ) : ?>
					<p class="photo-hero-eyebrow">
						<?php echo esc_html( $eyebrow ); ?>
					</p>
				<?php endif; ?>

				<?php if ( $heading ) : ?>
					<h1 class="heading-1 max-md:!text-4xl md:max-lg:!text-5xl">
						<?php echo esc_html( $heading ); ?>
					</h1>
				<?php endif; ?>

				<?php if ( $button ) : ?>
					<?php
						$button_url    = esc_url( $button['url'] ?? '' );
						$button_label  = esc_html( $button['title'] ?? '' );
						$button_target = ! empty( $button['target'] ) ? esc_attr( $button['target'] ) : '_self';
						$button_rel    = '_blank' === $button_target ? 'noopener noreferrer' : '';
					?>

					<?php if ( $button_url && $button_label ) : ?>
						<div class="flex flex-wrap gap-4 justify-start mt-8">
							<a
								class="border border-black shadow-md btn_main min-w-56"
								href="<?php echo $button_url; ?>"
								target="<?php echo $button_target; ?>"
								<?php echo $button_rel ? 'rel="' . esc_attr( $button_rel ) . '"' : ''; ?>
							>
								<?php echo $button_label; ?>
							</a>
						</div>
					<?php endif; ?>
				<?php endif; ?>

			</div>
		</div>
	</div>
</section>
