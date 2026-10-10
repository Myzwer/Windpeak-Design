<?php
	/**
	 * Text content block — single WYSIWYG (headings, copy, and in-editor buttons).
	 */

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$content = get_sub_field( 'content' );

	if ( ! $content ) {
		return;
	}
?>

<section class="py-14 md:py-20 wrap">
	<div class="grid-12">
		<div class="col-span-12">
			<div class="prose-theme">
				<?php echo wp_kses_post( $content ); ?>
			</div>
		</div>
	</div>
</section>
