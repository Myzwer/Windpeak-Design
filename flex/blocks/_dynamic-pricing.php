<?php
	/**
	 * Dynamic Pricing block — payment options copy + interactive calculator.
	 */

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$intro            = get_sub_field( 'intro' );
	$calculator_title = get_sub_field( 'calculator_title' ) ?: 'Pricing Calculator';
	$base_site        = (float) ( get_sub_field( 'base_site' ) ?: 900 );
	$monthly_service  = (float) ( get_sub_field( 'monthly_service' ) ?: 69 );
	$term_months      = (int) ( get_sub_field( 'term_months' ) ?: 12 );
	$addon_crm        = (float) ( get_sub_field( 'addon_vacation_crm' ) ?: 250 );
	$addon_copy       = (float) ( get_sub_field( 'addon_white_glove_copy' ) ?: 400 );
	$addon_design     = (float) ( get_sub_field( 'addon_graphic_design' ) ?: 400 );
	$has_options_rows = get_sub_field( 'payment_options' ) ?: array();
	$has_options = ! empty( $has_options_rows );

	$config = array(
		'baseSite'       => $base_site,
		'monthlyService' => $monthly_service,
		'termMonths'     => $term_months,
		'addons'         => array(
			'vacationCrm'     => $addon_crm,
			'whiteGloveCopy'  => $addon_copy,
			'graphicDesign'   => $addon_design,
		),
	);

	$uid = wp_unique_id( 'dynprice-' );
?>

<section class="py-14 md:py-20 wrap">
	<div class="md:gap-10 grid-12">
		<?php if ( $intro ) : ?>
			<div class="col-span-12">
				<div class="prose-theme">
					<?php echo wp_kses_post( $intro ); ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $has_options ) : ?>
			<div class="flex flex-col col-span-12 gap-8">
				<?php while ( have_rows( 'payment_options' ) ) : the_row(); ?>
					<?php
						$title = get_sub_field( 'option_title' );
						$text  = get_sub_field( 'option_text' );
					?>
					<div>
						<?php if ( $title ) : ?>
							<h3 class="mb-2 heading-3"><?php echo esc_html( $title ); ?></h3>
						<?php endif; ?>
						<?php if ( $text ) : ?>
							<div class="prose-theme prose-compact">
								<?php echo wp_kses_post( $text ); ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>

		<div class="col-span-12">
			<div
				class="p-6 bg-white rounded-2xl shadow-md md:p-8"
				data-pricing-calculator
				data-pricing-config="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"
			>
				<h3 class="mb-6 heading-3"><?php echo esc_html( $calculator_title ); ?></h3>

				<div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
					<div class="flex flex-col gap-5">
						<fieldset class="p-0 m-0 border-0">
							<legend class="mb-2 text-sm font-bold">I'd like to pay:</legend>
							<div class="flex flex-wrap gap-4">
								<label class="inline-flex gap-2 items-center">
									<input type="radio" name="<?php echo esc_attr( $uid ); ?>-pay" value="upfront" data-pricing-pay checked>
									<span>Up Front</span>
								</label>
								<label class="inline-flex gap-2 items-center">
									<input type="radio" name="<?php echo esc_attr( $uid ); ?>-pay" value="spread" data-pricing-pay>
									<span>Spread Out</span>
								</label>
							</div>
						</fieldset>

						<fieldset class="p-0 m-0 border-0">
							<legend class="mb-2 text-sm font-bold">VacationCRM Integration?</legend>
							<div class="flex flex-wrap gap-4">
								<label class="inline-flex gap-2 items-center">
									<input type="radio" name="<?php echo esc_attr( $uid ); ?>-crm" value="0" data-pricing-addon="vacationCrm" checked>
									<span>No</span>
								</label>
								<label class="inline-flex gap-2 items-center">
									<input type="radio" name="<?php echo esc_attr( $uid ); ?>-crm" value="1" data-pricing-addon="vacationCrm">
									<span>Yes</span>
								</label>
							</div>
						</fieldset>

						<fieldset class="p-0 m-0 border-0">
							<legend class="mb-2 text-sm font-bold">White Glove Copy?</legend>
							<div class="flex flex-wrap gap-4">
								<label class="inline-flex gap-2 items-center">
									<input type="radio" name="<?php echo esc_attr( $uid ); ?>-copy" value="0" data-pricing-addon="whiteGloveCopy" checked>
									<span>No</span>
								</label>
								<label class="inline-flex gap-2 items-center">
									<input type="radio" name="<?php echo esc_attr( $uid ); ?>-copy" value="1" data-pricing-addon="whiteGloveCopy">
									<span>Yes</span>
								</label>
							</div>
						</fieldset>

						<fieldset class="p-0 m-0 border-0">
							<legend class="mb-2 text-sm font-bold">Graphic Design Package?</legend>
							<div class="flex flex-wrap gap-4">
								<label class="inline-flex gap-2 items-center">
									<input type="radio" name="<?php echo esc_attr( $uid ); ?>-design" value="0" data-pricing-addon="graphicDesign" checked>
									<span>No</span>
								</label>
								<label class="inline-flex gap-2 items-center">
									<input type="radio" name="<?php echo esc_attr( $uid ); ?>-design" value="1" data-pricing-addon="graphicDesign">
									<span>Yes</span>
								</label>
							</div>
						</fieldset>
					</div>

					<div class="flex flex-col gap-6 justify-center" data-pricing-results>
						<div data-pricing-upfront-wrap>
							<p class="mb-1 text-xs font-bold tracking-wide uppercase" data-pricing-upfront-label>Up Front</p>
							<p class="mb-0 text-5xl leading-none font-display" data-pricing-upfront>$900</p>
						</div>
						<div data-pricing-monthly-wrap>
							<p class="mb-1 text-xs font-bold tracking-wide uppercase" data-pricing-monthly-label>Monthly</p>
							<p class="mb-0 text-5xl leading-none font-display" data-pricing-monthly>$69</p>
						</div>
						<div class="hidden" data-pricing-then-wrap>
							<p class="mb-1 text-xs font-bold tracking-wide uppercase">Then</p>
							<p class="mb-0 text-3xl leading-none font-display" data-pricing-then>$69</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
