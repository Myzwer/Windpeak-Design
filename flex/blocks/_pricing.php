<?php
	/**
	 * Pricing block — support level cards on permanent dark green.
	 */

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$intro          = get_sub_field( 'intro' );
	$includes_label = get_sub_field( 'includes_label' );
	$footer         = get_sub_field( 'footer' );
	$includes_rows  = get_sub_field( 'includes' ) ?: array();
	$plans_rows     = get_sub_field( 'plans' ) ?: array();

	if ( ! $intro && empty( $includes_rows ) && empty( $plans_rows ) && ! $footer ) {
		return;
	}
?>

<section class="py-16 md:py-24 theme-invert bg-impact-gradient">
	<div class="wrap">
		<div class="md:gap-8 grid-12">
			<?php if ( $intro ) : ?>
				<div class="col-span-12 mx-auto max-w-3xl text-center">
					<div class="prose-theme">
						<?php echo wp_kses_post( $intro ); ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $includes_label || ! empty( $includes_rows ) ) : ?>
				<div class="col-span-12 text-center">
					<?php if ( $includes_label ) : ?>
						<p class="mb-4 text-xs font-bold uppercase tracking-[0.15em]"><?php echo esc_html( $includes_label ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $includes_rows ) ) : ?>
						<div class="flex flex-wrap gap-2 justify-center">
							<?php foreach ( $includes_rows as $include ) : ?>
								<?php $label = $include['include_label'] ?? ''; ?>
								<?php if ( $label ) : ?>
									<span class="inline-flex py-2 px-4 text-sm rounded-full border border-white/40"><?php echo esc_html( $label ); ?></span>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $plans_rows ) ) : ?>
				<div class="col-span-12">
					<div class="grid grid-cols-1 gap-4 md:grid-cols-3 md:gap-6">
						<?php foreach ( $plans_rows as $plan ) : ?>
							<?php
								$price      = $plan['price'] ?? '';
								$suffix     = ! empty( $plan['price_suffix'] ) ? $plan['price_suffix'] : '/mo';
								$title      = $plan['plan_title'] ?? '';
								$tokens     = $plan['tokens_line'] ?? '';
								$best_label = ! empty( $plan['best_for_label'] ) ? $plan['best_for_label'] : 'Best for';
								$best_for   = $plan['best_for'] ?? '';
							?>
							<article class="p-6 text-black bg-white rounded-2xl md:p-8">
								<?php if ( $price ) : ?>
									<p class="mb-1 text-5xl leading-none font-display">
										<?php echo esc_html( $price ); ?><span class="text-lg font-body"><?php echo esc_html( $suffix ); ?></span>
									</p>
								<?php endif; ?>
								<?php if ( $title ) : ?>
									<p class="mb-1 text-lg font-bold"><?php echo esc_html( $title ); ?></p>
								<?php endif; ?>
								<?php if ( $tokens ) : ?>
									<p class="mb-4 text-sm opacity-80"><?php echo esc_html( $tokens ); ?></p>
								<?php endif; ?>
								<hr class="mb-4 border-black/15">
								<?php if ( $best_for ) : ?>
									<p class="mb-1 text-sm font-bold"><?php echo esc_html( $best_label ); ?></p>
									<p class="mb-0 text-sm"><?php echo esc_html( $best_for ); ?></p>
								<?php endif; ?>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $footer ) : ?>
				<div class="col-span-12 mx-auto max-w-3xl text-center">
					<div class="text-sm prose-theme prose-compact">
						<?php echo wp_kses_post( $footer ); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
