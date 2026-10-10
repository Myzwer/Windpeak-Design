<?php
	/**
	 * Photo header.
	 *
	 * Rounded media frame with left-aligned eyebrow, title, and CTA.
	 * Shares the video-header shell (topo band + absolute navbar clearance).
	 * Per-page seasonal images are ready for hot-swap; Windpeak is the default.
	 */

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$eyebrow    = get_sub_field( 'eyebrow' );
	$heading    = get_sub_field( 'main_title' );
	$button     = get_sub_field( 'button' );
	$darken     = get_sub_field( 'image_darken' );
	$images     = array(
		'windpeak' => get_sub_field( 'image_windpeak' ),
		'spring'   => get_sub_field( 'image_spring' ),
		'summer'   => get_sub_field( 'image_summer' ),
		'autumn'   => get_sub_field( 'image_autumn' ),
		'winter'   => get_sub_field( 'image_winter' ),
	);

	$palette_urls = array();

	foreach ( $images as $palette => $image ) {
		$url = prelaunch_image_url( $image );

		if ( $url ) {
			$palette_urls[ $palette ] = $url;
		}
	}

	if ( empty( $palette_urls['windpeak'] ) ) {
		return;
	}

	$active_palette = prelaunch_active_palette();

	if ( empty( $palette_urls[ $active_palette ] ) ) {
		$active_palette = 'windpeak';
	}

	$active_url = $palette_urls[ $active_palette ];

	$darken = trim( (string) $darken );
	$allowed_darken = array( '0', '30', '45', '60', '70' );

	if ( ! in_array( $darken, $allowed_darken, true ) ) {
		$darken = '0';
	}

	$overlay_classes = 'video-hero-overlay video-overlay-' . $darken;

	$topo_urls       = prelaunch_topography_urls();
	$section_classes = 'photo-header section theme-invert bg-primary-gradient';

	if ( '' !== $topo_urls['brand'] || '' !== $topo_urls['season'] ) {
		$section_classes .= ' bg-texture';
	}
?>

<section class="<?php echo esc_attr( $section_classes ); ?>">
	<div class="video-header-shell">
		<div class="video-header-frame viewport-header min-h-[560px] md:min-h-[650px] lg:min-h-[720px]">

			<div
				class="video-hero-media"
				data-seasonal-media
				data-palette-active="<?php echo esc_attr( $active_palette ); ?>"
				<?php foreach ( $palette_urls as $palette => $url ) : ?>
					data-palette-src-<?php echo esc_attr( $palette ); ?>="<?php echo esc_url( $url ); ?>"
				<?php endforeach; ?>
				style="--video-poster-image: url('<?php echo esc_url( $active_url ); ?>');"
				aria-hidden="true"
			>
				<div class="<?php echo esc_attr( $overlay_classes ); ?>"></div>
			</div>

			<div class="video-hero-content">
				<div class="wrap-wide grid-12">
					<div class="grid col-span-12 justify-items-start text-left md:col-span-9 lg:col-span-7">

						<?php if ( $eyebrow ) : ?>
							<p class="text-white photo-hero-eyebrow">
								<?php echo esc_html( $eyebrow ); ?>
							</p>
						<?php endif; ?>

						<?php if ( $heading ) : ?>
							<h1 class="heading-1 text-white text-balance max-md:!text-4xl md:max-lg:!text-5xl">
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
										class="btn_main min-w-56"
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

		</div>
	</div>
</section>
