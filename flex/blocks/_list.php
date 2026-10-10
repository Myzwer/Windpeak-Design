<?php
	/**
	 * List block — icon + title grid with optional intro and CTA.
	 */

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$intro          = get_sub_field( 'intro' );
	$link           = get_sub_field( 'link' );
	$has_items_rows = get_sub_field( 'list_items' ) ?: array();
	$has_items      = ! empty( $has_items_rows );

	if ( ! $intro && ! $has_items && empty( $link['url'] ) ) {
		return;
	}
?>

<section class="py-14 md:py-20 wrap">
	<div class="md:gap-8 grid-12">
		<?php if ( $intro ) : ?>
			<div class="col-span-12">
				<div class="prose-theme">
					<?php echo wp_kses_post( $intro ); ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $has_items ) : ?>
			<?php while ( have_rows( 'list_items' ) ) : the_row(); ?>
				<?php
				$icon  = get_sub_field( 'icon' );
				$title = get_sub_field( 'list_item_title' );
				?>
				<div class="col-span-12 sm:col-span-6">
					<div class="flex gap-4 items-start">
						<?php if ( $icon ) : ?>
							<span class="text-2xl shrink-0 [&>i]:text-black" aria-hidden="true">
								<?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- FA element from ACF ?>
							</span>
						<?php endif; ?>
						<?php if ( $title ) : ?>
							<p class="mb-2 text-lg font-bold"><?php echo esc_html( $title ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			<?php endwhile; ?>
		<?php endif; ?>

		<?php if ( ! empty( $link['url'] ) ) : ?>
			<div class="col-span-12 pt-4 text-center">
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
</section>
