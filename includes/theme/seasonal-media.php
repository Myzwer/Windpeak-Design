<?php
	/**
	 * Seasonal photo swap.
	 *
	 * Photo headers carry their own Windpeak + season images.
	 * Decorative blocks keep a local Windpeak photo and optionally
	 * pull a numbered slot from Globals → Seasonal Images.
	 *
	 * Only the active photo is used as the real src / background.
	 * The other URLs sit on data attributes until the palette changes.
	 */

	defined( 'ABSPATH' ) || exit;

	/**
	 * Branded topography, plus the black one used on seasonal palettes.
	 * If the black file is missing, seasons keep the branded texture.
	 *
	 * @return array{brand: string, season: string}
	 */
	function prelaunch_topography_urls(): array {
		$brand = '';
		$black = '';

		if ( function_exists( 'get_field' ) ) {
			$brand = prelaunch_image_url( get_field( 'topography', 'option' ) );
			$black = prelaunch_image_url( get_field( 'topography_black', 'option' ) );
		}

		return array(
			'brand'  => $brand,
			'season' => '' !== $black ? $black : $brand,
		);
	}

	/**
	 * Print the two topography URLs so CSS can swap them with the palette.
	 *
	 * @return void
	 */
	function prelaunch_topography_css_vars(): void {
		$urls = prelaunch_topography_urls();

		if ( '' === $urls['brand'] && '' === $urls['season'] ) {
			return;
		}

		echo '<style>:root{';

		if ( '' !== $urls['brand'] ) {
			echo '--topo-brand:url("' . esc_url( $urls['brand'] ) . '");';
		}

		if ( '' !== $urls['season'] ) {
			echo '--topo-season:url("' . esc_url( $urls['season'] ) . '");';
		}

		echo '}</style>' . "\n";
	}

	/**
	 * Palette saved for this browser session, or windpeak.
	 *
	 * @return string
	 */
	function prelaunch_active_palette(): string {
		$allowed = array( 'windpeak', 'spring', 'summer', 'autumn', 'winter' );
		$raw     = isset( $_COOKIE['prelaunch-palette'] ) ? (string) wp_unslash( $_COOKIE['prelaunch-palette'] ) : '';
		$palette = preg_replace( '/[^a-z]/', '', $raw );

		if ( ! is_string( $palette ) || ! in_array( $palette, $allowed, true ) ) {
			return 'windpeak';
		}

		return $palette;
	}

	/**
	 * URL for an ACF image value.
	 *
	 * @param mixed $image Image array, attachment ID, or URL.
	 * @return string
	 */
	function prelaunch_image_url( $image ): string {
		if ( is_array( $image ) && ! empty( $image['url'] ) ) {
			return (string) $image['url'];
		}

		if ( is_numeric( $image ) ) {
			$url = wp_get_attachment_image_url( (int) $image, 'full' );

			return $url ? (string) $url : '';
		}

		if ( is_string( $image ) && '' !== $image ) {
			return $image;
		}

		return '';
	}

	/**
	 * Local Windpeak photo plus a Globals seasonal slot.
	 *
	 * Slot 0 keeps only the local photo.
	 *
	 * @param mixed $local_image ACF image for the Windpeak / default photo.
	 * @param int   $slot        Globals image number, 1–5.
	 * @return array<string, string>
	 */
	function prelaunch_block_media_urls( $local_image, int $slot ): array {
		$urls  = array();
		$local = prelaunch_image_url( $local_image );

		if ( $local ) {
			$urls['windpeak'] = $local;
		}

		if ( $slot < 1 || $slot > 5 || ! function_exists( 'get_field' ) ) {
			return $urls;
		}

		foreach ( array( 'spring', 'summer', 'autumn', 'winter' ) as $season ) {
			$seasonal = get_field( 'seasonal_' . $season . '_' . $slot, 'option' );
			$url      = prelaunch_image_url( $seasonal );

			if ( $url ) {
				$urls[ $season ] = $url;
			}
		}

		return $urls;
	}

	/**
	 * Photo to show for the current palette.
	 * Missing seasonal photos fall back to Windpeak.
	 *
	 * @param array<string, string> $urls Palette slug => URL.
	 * @return string
	 */
	function prelaunch_active_media_url( array $urls ): string {
		$palette = prelaunch_active_palette();

		if ( 'windpeak' !== $palette && ! empty( $urls[ $palette ] ) ) {
			return $urls[ $palette ];
		}

		return $urls['windpeak'] ?? '';
	}

	/**
	 * Data attributes that let the palette control swap this photo.
	 * Empty when there is nothing to swap to.
	 *
	 * @param array<string, string> $urls Palette slug => URL.
	 * @return string
	 */
	function prelaunch_seasonal_media_attributes( array $urls ): string {
		$seasonal = array_diff_key( $urls, array( 'windpeak' => true ) );

		if ( ! $seasonal ) {
			return '';
		}

		$html = ' data-seasonal-media';

		foreach ( $urls as $palette => $url ) {
			$html .= ' data-palette-src-' . esc_attr( $palette ) . '="' . esc_url( $url ) . '"';
		}

		return $html;
	}
