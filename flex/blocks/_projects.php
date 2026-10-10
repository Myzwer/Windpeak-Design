<?php
	/**
	 * Projects block — alternating laptop screenshot + copy rows.
	 */

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$intro     = get_sub_field( 'intro' );
	$link      = get_sub_field( 'link' );
	$has_rows_rows = get_sub_field( 'projects' ) ?: array();
	$has_rows = ! empty( $has_rows_rows );

	if ( ! $intro && ! $has_rows && empty( $link['url'] ) ) {
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

		<?php if ( $has_rows ) : ?>
			<div class="flex flex-col col-span-12 gap-16 md:gap-20">
				<?php while ( have_rows( 'projects' ) ) : the_row(); ?>
					<?php
						$shot      = get_sub_field( 'screenshot' );
						$shot_id   = ! empty( $shot['ID'] ) ? absint( $shot['ID'] ) : 0;
						$content   = get_sub_field( 'content' );
						$side      = get_sub_field( 'image_side' ) ?: 'left';
						$img_first = ( 'left' === $side );
					?>
					<div class="grid grid-cols-1 gap-8 items-center md:grid-cols-2 md:gap-12">
						<div class="<?php echo $img_first ? 'md:order-1' : 'md:order-2'; ?>">
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
						<div class="<?php echo $img_first ? 'md:order-2' : 'md:order-1'; ?>">
							<?php if ( $content ) : ?>
								<div class="prose-theme">
									<?php echo wp_kses_post( $content ); ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $link['url'] ) ) : ?>
			<div class="col-span-12 text-center">
				<a
					class="border border-black shadow-md btn_main"
					href="<?php echo esc_url( $link['url'] ); ?>"
					<?php echo ! empty( $link['target'] ) ? ' target="' . esc_attr( $link['target'] ) . '" rel="noopener noreferrer"' : ''; ?>
				>
					<span><?php echo esc_html( $link['title'] ?: 'See Our Work' ); ?></span>
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>
