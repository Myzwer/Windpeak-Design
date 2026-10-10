<?php
	/**
	 * Bio block — intro copy with optional photo row.
	 */

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$content = get_sub_field( 'content' );
	$photos  = get_sub_field( 'photos' );

	if ( ! $content && empty( $photos ) ) {
		return;
	}
?>

<section class="py-14 md:py-20 wrap">
	<div class="md:gap-8 grid-12">
		<?php if ( $content ) : ?>
			<div class="col-span-12">
				<div class="prose-theme">
					<?php echo wp_kses_post( $content ); ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $photos ) && is_array( $photos ) ) : ?>
			<div class="col-span-12">
				<div class="grid grid-cols-1 gap-4 sm:grid-cols-3 md:gap-6">
					<?php foreach ( $photos as $photo ) : ?>
						<?php
							$pid = ! empty( $photo['ID'] ) ? absint( $photo['ID'] ) : 0;
							if ( ! $pid ) {
								continue;
							}
						?>
						<div class="bio-photo">
							<?php
								echo wp_get_attachment_image(
									$pid,
									'large',
									false,
									array(
										'loading'  => 'lazy',
										'decoding' => 'async',
									)
								);
							?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
