<?php
/**
 * Front-end Gravity Forms adjustments.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the form is the Advisor Micros inquiry form.
 *
 * @param array $form Form object.
 * @return bool
 */
function windpeak_is_advisor_inquiry_form( $form ): bool {
	return is_array( $form ) && ( $form['title'] ?? '' ) === 'Advisor Micro Intake';
}

/**
 * Explain the asterisk once, at the top of the inquiry form.
 *
 * @param string $legend Default legend.
 * @param array  $form   Form object.
 * @return string
 */
function windpeak_advisor_inquiry_required_legend( $legend, $form ) {
	if ( ! windpeak_is_advisor_inquiry_form( $form ) ) {
		return $legend;
	}

	return '<span class="gfield_required gfield_required_asterisk">*</span> Indicates a required field.';
}
add_filter( 'gform_required_legend', 'windpeak_advisor_inquiry_required_legend', 10, 2 );

/**
 * Keep the optional notes field short. CSS lets it grow with the text.
 *
 * Gravity Forms passes the form ID, not the form array, as the fifth argument.
 *
 * @param string   $content Field HTML.
 * @param GF_Field $field   Field object.
 * @param string   $value   Field value.
 * @param int      $lead_id Entry ID.
 * @param int      $form_id Form ID.
 * @return string
 */
function windpeak_advisor_inquiry_notes_rows( $content, $field, $value, $lead_id, $form_id ) {
	unset( $value, $lead_id, $form_id );

	if ( (int) $field->id !== 14 ) {
		return $content;
	}

	$form = GFAPI::get_form( (int) $field->formId );
	if ( ! windpeak_is_advisor_inquiry_form( $form ) ) {
		return $content;
	}

	return str_replace(
		array( "rows='10'", 'rows="10"' ),
		array( "rows='4'", 'rows="4"' ),
		$content
	);
}
add_filter( 'gform_field_content', 'windpeak_advisor_inquiry_notes_rows', 1001, 5 );
