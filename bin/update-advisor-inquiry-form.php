<?php
/**
 * Create the Advisor Micro Intake form and point the Advisor Micros page at it.
 *
 * Usage:
 *   php -d mysqli.default_socket="$SOCK" -d pdo_mysql.default_socket="$SOCK" \
 *     wp eval-file wp-content/themes/windpeak/bin/update-advisor-inquiry-form.php --path=app/public
 */

if ( ! class_exists( 'GFAPI' ) ) {
	fwrite( STDERR, "GFAPI missing\n" );
	return;
}

$form    = require __DIR__ . '/advisor-inquiry-form.php';
$form_id = 0;
$forms   = GFAPI::get_forms( true );

foreach ( $forms as $existing_form ) {
	if ( isset( $existing_form['title'] ) && $existing_form['title'] === 'Advisor Micro Intake' ) {
		$form_id = (int) $existing_form['id'];
		break;
	}
}

$confirmation = array(
	'id'          => 'default',
	'name'        => 'Default Confirmation',
	'isDefault'   => true,
	'type'        => 'page',
	'pageId'      => '452',
	'message'     => '',
	'url'         => '',
	'queryString' => '',
);

if ( $form_id ) {
	$existing                 = GFAPI::get_form( $form_id );
	$form['id']               = $form_id;
	$form['is_active']        = '1';
	$form['confirmations']    = $existing['confirmations'] ?? array( 'default' => $confirmation );
	$form['notifications']    = $existing['notifications'] ?? array();
	$result                   = GFAPI::update_form( $form, $form_id );
	$action                   = 'Updated';
} else {
	$form['confirmations'] = array( 'default' => $confirmation );
	$result                = GFAPI::add_form( $form );
	$action                = 'Created';
	if ( ! is_wp_error( $result ) ) {
		$form_id = (int) $result;
	}
}

if ( is_wp_error( $result ) ) {
	fwrite( STDERR, $result->get_error_message() . "\n" );
	return;
}

echo "{$action} Advisor Micro Intake form {$form_id}\n";

$page = get_page_by_path( 'advisor-micros' );
if ( ! $page ) {
	fwrite( STDERR, "Advisor Micros page not found. Form was created, but the page was not updated.\n" );
	return;
}

$rows    = get_field( 'body_sections', $page->ID );
$updated = false;

if ( is_array( $rows ) ) {
	foreach ( $rows as $index => $row ) {
		if ( ( $row['acf_fc_layout'] ?? '' ) !== 'form_block' ) {
			continue;
		}
		$rows[ $index ]['form_id'] = (string) $form_id;
		$updated                   = true;
	}
}

if ( ! $updated ) {
	fwrite( STDERR, "No form block found on the Advisor Micros page.\n" );
	return;
}

$ok = update_field( 'body_sections', $rows, $page->ID );
echo $ok ? "Pointed Advisor Micros form block at form {$form_id}\n" : "Failed to update the Advisor Micros form block\n";
