<?php
	/**
	 * Selection block — interactive service tiles with detail panel.
	 */

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$intro     = get_sub_field( 'intro' );
	$services  = get_sub_field( 'services' );

	if ( ! $intro && empty( $services ) ) {
		return;
	}

	$uid = wp_unique_id( 'selection-' );
?>

<section class="py-12 md:py-16 selection-block wrap" data-selection-block>
	<?php if ( $intro ) : ?>
		<div class="mb-8 md:mb-10 prose-theme selection-intro">
			<?php echo wp_kses_post( $intro ); ?>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $services ) && is_array( $services ) ) : ?>
		<div class="selection-layout">
			<div class="selection-grid" role="tablist" aria-label="Services">
					<?php foreach ( $services as $index => $service ) : ?>
						<?php
							$icon     = $service['icon'] ?? '';
							$label    = $service['label'] ?? '';
							$panel_id = $uid . '-panel-' . $index;
							$tab_id   = $uid . '-tab-' . $index;
							$active   = 0 === $index;
						?>
						<button
							type="button"
							id="<?php echo esc_attr( $tab_id ); ?>"
							class="selection-item <?php echo $active ? 'is-active' : ''; ?>"
							role="tab"
							aria-selected="<?php echo $active ? 'true' : 'false'; ?>"
							aria-controls="<?php echo esc_attr( $panel_id ); ?>"
							data-selection-tab
							data-index="<?php echo esc_attr( (string) $index ); ?>"
						>
							<span class="selection-tile">
								<?php if ( $icon ) : ?>
									<span class="selection-tile__icon" aria-hidden="true">
										<?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</span>
								<?php endif; ?>
							</span>
							<?php if ( $label ) : ?>
								<span class="selection-tile__label"><?php echo esc_html( $label ); ?></span>
							<?php endif; ?>
						</button>
					<?php endforeach; ?>
			</div>

			<div class="selection-detail">
				<?php foreach ( $services as $index => $service ) : ?>
					<?php
						$detail   = $service['detail_content'] ?? '';
						$panel_id = $uid . '-panel-' . $index;
						$tab_id   = $uid . '-tab-' . $index;
						$active   = 0 === $index;
					?>
					<div
						id="<?php echo esc_attr( $panel_id ); ?>"
						class="selection-panel <?php echo $active ? 'is-active' : ''; ?>"
						role="tabpanel"
						aria-labelledby="<?php echo esc_attr( $tab_id ); ?>"
						data-selection-panel
						data-index="<?php echo esc_attr( (string) $index ); ?>"
						<?php echo $active ? '' : 'hidden'; ?>
					>
						<?php if ( $detail ) : ?>
							<div class="prose-theme">
								<?php echo wp_kses_post( $detail ); ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>
</section>
