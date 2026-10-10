<?php
	/**
	 * Form block — intro + Gravity Forms.
	 */

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$intro   = get_sub_field( 'intro' );
	$form_id = absint( get_sub_field( 'form_id' ) );

	if ( ! $intro && ! $form_id ) {
		return;
	}
?>

<section class="py-14 md:py-20 wrap">
	<div class="grid-12">
		<?php if ( $intro ) : ?>
			<div class="col-span-12 max-w-3xl">
				<div class="prose-theme">
					<?php echo wp_kses_post( $intro ); ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $form_id ) : ?>
			<div class="col-span-12">
				<?php echo do_shortcode( '[gravityform id="' . $form_id . '" title="false" description="false" ajax="true"]' ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
