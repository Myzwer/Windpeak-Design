<?php
	/**
	 * Case Study block — laptop hero, narrative sections, work list, CTA.
	 */

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$shot         = get_sub_field( 'screenshot' );
	$shot_id      = ! empty( $shot['ID'] ) ? absint( $shot['ID'] ) : 0;
	$eyebrow      = get_sub_field( 'eyebrow' );
	$title        = get_sub_field( 'title' );
	$subtitle     = get_sub_field( 'subtitle' );
	$intro        = get_sub_field( 'intro' );
	$work_heading = get_sub_field( 'work_heading' );
	$link         = get_sub_field( 'link' );
	$sections_rows = get_sub_field( 'sections' ) ?: array();
	$work_rows     = get_sub_field( 'work_items' ) ?: array();
	$has_sections  = ! empty( $sections_rows );
	$has_work      = ! empty( $work_rows );

	if ( ! $shot_id && ! $title && ! $intro && ! $has_sections && ! $has_work ) {
		return;
	}
?>

<section class="py-14 md:py-20 wrap">
	<div class="md:gap-8 grid-12">
		<?php if ( $shot_id ) : ?>
			<div class="col-span-12">
				<div class="mx-auto max-w-4xl laptop-frame">
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
			</div>
		<?php endif; ?>

		<div class="col-span-12 p-6 bg-white rounded-2xl shadow-sm md:p-10">
			<?php if ( $eyebrow ) : ?>
				<p class="mb-2 text-sm font-bold tracking-wide uppercase"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>
			<?php if ( $title ) : ?>
				<h2 class="mb-2 heading-2"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( $subtitle ) : ?>
				<p class="mb-6 italic opacity-80"><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>

			<?php if ( $intro ) : ?>
				<div class="mb-8 prose-theme">
					<?php echo wp_kses_post( $intro ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $has_sections ) : ?>
				<div class="flex flex-col gap-8 mb-10">
					<?php while ( have_rows( 'sections' ) ) : the_row(); ?>
						<?php
							$section_title = get_sub_field( 'section_title' );
							$section_text  = get_sub_field( 'section_text' );
						?>
						<div>
							<?php if ( $section_title ) : ?>
								<h3 class="mb-2 heading-3"><?php echo esc_html( $section_title ); ?></h3>
							<?php endif; ?>
							<?php if ( $section_text ) : ?>
								<div class="prose-theme prose-compact">
									<?php echo wp_kses_post( $section_text ); ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endwhile; ?>
				</div>
			<?php endif; ?>

			<?php if ( $work_heading || $has_work ) : ?>
				<div class="mb-10">
					<?php if ( $work_heading ) : ?>
						<h3 class="mb-6 heading-3"><?php echo esc_html( $work_heading ); ?></h3>
					<?php endif; ?>
					<?php if ( $has_work ) : ?>
						<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
							<?php while ( have_rows( 'work_items' ) ) : the_row(); ?>
								<?php
									$icon  = get_sub_field( 'icon' );
									$label = get_sub_field( 'item_label' );
								?>
								<div class="flex gap-3 items-start">
									<?php if ( $icon ) : ?>
										<span class="text-xl text-black shrink-0" aria-hidden="true">
											<?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										</span>
									<?php endif; ?>
									<?php if ( $label ) : ?>
										<p class="mb-0 font-medium"><?php echo esc_html( $label ); ?></p>
									<?php endif; ?>
								</div>
							<?php endwhile; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $link['url'] ) ) : ?>
				<a
					class="border border-black shadow-md btn_main"
					href="<?php echo esc_url( $link['url'] ); ?>"
					<?php echo ! empty( $link['target'] ) ? ' target="' . esc_attr( $link['target'] ) . '" rel="noopener noreferrer"' : ''; ?>
				>
					<span><?php echo esc_html( $link['title'] ?: 'View Live Site' ); ?></span>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
