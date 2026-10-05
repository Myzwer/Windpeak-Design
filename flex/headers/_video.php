<?php
	/**
	 * Homepage video header.
	 *
	 * Rounded media frame with left-aligned XL display title, tagline, and CTA.
	 * Top padding clears the absolute transparent navbar (see background.css).
	 */

	$video_background      = get_sub_field( 'video_background' );
	$poster_image          = get_sub_field( 'poster_image' );
	$background_tint_level = get_sub_field( 'background_tint_level' );
	$title                 = get_sub_field( 'title' );
	$subtitle              = get_sub_field( 'subtitle' );
	$primary_cta           = get_sub_field( 'primary_cta' );
	$secondary_cta         = get_sub_field( 'secondary_cta' );

	$video_url  = '';
	$poster_url = '';

	if ( is_array( $video_background ) && ! empty( $video_background['url'] ) ) {
		$video_url = $video_background['url'];
	} elseif ( is_numeric( $video_background ) ) {
		$video_url = wp_get_attachment_url( $video_background );
	} elseif ( is_string( $video_background ) ) {
		$video_url = $video_background;
	}

	if ( is_array( $poster_image ) && ! empty( $poster_image['url'] ) ) {
		$poster_url = $poster_image['url'];
	} elseif ( is_numeric( $poster_image ) ) {
		$poster_url = wp_get_attachment_image_url( $poster_image, 'full' );
	} elseif ( is_string( $poster_image ) ) {
		$poster_url = $poster_image;
	}

	/**
	 * Normalize background tint level.
	 *
	 * This field is expected to return one of:
	 * - 0
	 * - 30
	 * - 45
	 * - 60
	 * - 70
	 *
	 * Fail-safe is 0/None so a missing ACF value does not accidentally darken the video.
	 */
	$background_tint_level = trim( (string) $background_tint_level );

	$allowed_tint_levels = array( '0', '30', '45', '60', '70' );

	if ( ! in_array( $background_tint_level, $allowed_tint_levels, true ) ) {
		$background_tint_level = '0';
	}

	$overlay_classes = 'video-hero-overlay video-overlay-' . $background_tint_level;

	if ( '0' !== $background_tint_level ) {
		$overlay_classes .= ' video-overlay-gradient';
	}

	// Global Options → Topography (texture sits on the primary gradient only).
	$topography = function_exists( 'get_field' ) ? get_field( 'topography', 'option' ) : null;
	$topo_url   = '';

	if ( is_array( $topography ) && ! empty( $topography['url'] ) ) {
		$topo_url = $topography['url'];
	} elseif ( is_numeric( $topography ) ) {
		$topo_url = (string) wp_get_attachment_image_url( (int) $topography, 'full' );
	} elseif ( is_string( $topography ) && '' !== $topography ) {
		$topo_url = $topography;
	}

	$section_classes = 'video-header section theme-invert bg-primary-gradient';

	if ( $topo_url ) {
		$section_classes .= ' bg-texture';
	}
?>

<section
	class="<?php echo esc_attr( $section_classes ); ?>"
	<?php if ( $topo_url ) : ?>
		style="--bg-texture: url('<?php echo esc_url( $topo_url ); ?>');"
	<?php endif; ?>
>
	<div class="video-header-shell">
		<div class="video-header-frame viewport-header min-h-[560px] md:min-h-[650px] lg:min-h-[720px]">

			<div
				class="video-hero-media"
				<?php if ( $poster_url ) : ?>
					style="--video-poster-image: url('<?php echo esc_url( $poster_url ); ?>');"
				<?php endif; ?>
				aria-hidden="true"
			>
				<?php if ( $video_url ) : ?>
					<video
						class="header-video"
						muted
						loop
						playsinline
						preload="none"
						<?php if ( $poster_url ) : ?>
							poster="<?php echo esc_url( $poster_url ); ?>"
						<?php endif; ?>
					>
						<source data-src="<?php echo esc_url( $video_url ); ?>" type="video/mp4">
					</video>
				<?php endif; ?>

				<div class="<?php echo esc_attr( $overlay_classes ); ?>"></div>
			</div>

			<div class="video-hero-content">
				<div class="wrap-wide grid-12">
					<div class="grid col-span-12 md:col-span-9 lg:col-span-7 justify-items-start text-left">

						<?php if ( $title ) : ?>
							<?php
							// Stack the brand name like the mockup (first word / rest).
							$title_parts = preg_split( '/\s+/', trim( (string) $title ), 2 );
							?>
							<h1 class="heading-xl text-white text-balance">
								<?php if ( is_array( $title_parts ) && 2 === count( $title_parts ) ) : ?>
									<?php echo esc_html( $title_parts[0] ); ?>
									<br><?php echo esc_html( $title_parts[1] ); ?>
								<?php else : ?>
									<?php echo esc_html( $title ); ?>
								<?php endif; ?>
							</h1>
						<?php endif; ?>

						<?php if ( $subtitle ) : ?>
							<p class="video-hero-tagline mt-5 text-white">
								<?php echo esc_html( $subtitle ); ?>
							</p>
						<?php endif; ?>

						<?php if ( $primary_cta || $secondary_cta ) : ?>
							<div class="flex flex-wrap gap-4 justify-start mt-8">

								<?php
									if ( $primary_cta ) :
										$primary_url = esc_url( $primary_cta['url'] ?? '' );
										$primary_label = esc_html( $primary_cta['title'] ?? '' );
										$primary_target = ! empty( $primary_cta['target'] ) ? esc_attr( $primary_cta['target'] ) : '_self';
										$primary_rel = '_blank' === $primary_target ? 'noopener noreferrer' : '';
										?>

										<?php if ( $primary_url && $primary_label ) : ?>
										<a
											class="btn_main min-w-56"
											href="<?php echo $primary_url; ?>"
											target="<?php echo $primary_target; ?>"
											<?php echo $primary_rel ? 'rel="' . esc_attr( $primary_rel ) . '"' : ''; ?>
										>
											<?php echo $primary_label; ?>
										</a>
									<?php endif; ?>
									<?php endif; ?>

								<?php
									if ( $secondary_cta ) :
										$secondary_url = esc_url( $secondary_cta['url'] ?? '' );
										$secondary_label = esc_html( $secondary_cta['title'] ?? '' );
										$secondary_target = ! empty( $secondary_cta['target'] ) ? esc_attr( $secondary_cta['target'] ) : '_self';
										$secondary_rel = '_blank' === $secondary_target ? 'noopener noreferrer' : '';
										?>

										<?php if ( $secondary_url && $secondary_label ) : ?>
										<a
											class="btn_ghost_white min-w-56"
											href="<?php echo $secondary_url; ?>"
											target="<?php echo $secondary_target; ?>"
											<?php echo $secondary_rel ? 'rel="' . esc_attr( $secondary_rel ) . '"' : ''; ?>
										>
											<?php echo $secondary_label; ?>
										</a>
									<?php endif; ?>
									<?php endif; ?>

							</div>
						<?php endif; ?>

					</div>
				</div>
			</div>

		</div>
	</div>

	<?php if ( $video_url ) : ?>
		<button
			type="button"
			class="video-pause-btn"
			data-header-video-toggle
		>
			Pause background video
		</button>
	<?php endif; ?>
</section>
