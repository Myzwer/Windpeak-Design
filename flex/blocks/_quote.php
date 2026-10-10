<?php
	/**
	 * Quote / testimonial block.
	 *
	 * Permanent dark green (impact) background — excluded from alternating stripes.
	 */

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$intro       = get_sub_field( 'intro' );
	$quote       = get_sub_field( 'quote' );
	$name        = get_sub_field( 'name' );
	$attribution = get_sub_field( 'attribution' );

	if ( ! $intro && ! $quote && ! $name && ! $attribution ) {
		return;
	}
?>

<section class="py-16 md:py-24 theme-invert bg-impact-gradient">
	<div class="wrap">
		<div class="md:gap-8 grid-12">
			<?php if ( $intro ) : ?>
				<div class="col-span-12 max-w-4xl">
					<div class="prose-theme">
						<?php echo wp_kses_post( $intro ); ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $quote ) : ?>
				<div class="col-span-12 max-w-4xl">
					<div class="text-lg leading-relaxed md:text-xl prose-theme prose-compact">
						<?php echo wp_kses_post( $quote ); ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $name || $attribution ) : ?>
				<div class="col-span-12 text-right">
					<?php if ( $name ) : ?>
						<p class="mb-0 font-bold"><?php echo esc_html( $name ); ?></p>
					<?php endif; ?>
					<?php if ( $attribution ) : ?>
						<p class="mb-0 italic opacity-90"><?php echo esc_html( $attribution ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
