<?php
/**
 * Advisor Micros inquiry form.
 *
 * Uses Gravity Forms' current markup (not legacy) and native fields.
 * Sections are visual only. The form stays on one page.
 *
 * @return array
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$radio = static function ( int $id, string $label, array $choices, array $extra = array() ): array {
	$field = array_merge(
		array(
			'type'                 => 'radio',
			'id'                   => $id,
			'label'                => $label,
			'isRequired'           => true,
			'layoutGridColumnSpan' => 12,
			'choices'              => array(),
		),
		$extra
	);

	foreach ( $choices as $choice ) {
		$field['choices'][] = array(
			'text'       => $choice,
			'value'      => $choice,
			'isSelected' => false,
		);
	}

	return $field;
};

$show_when = static function ( int $field_id, string $value ): array {
	return array(
		'actionType' => 'show',
		'logicType'  => 'all',
		'rules'      => array(
			array(
				'fieldId'  => (string) $field_id,
				'operator' => 'is',
				'value'    => $value,
			),
		),
	);
};

$section = static function ( int $id, string $label ): array {
	return array(
		'type'     => 'section',
		'id'       => $id,
		'label'    => $label,
		'cssClass' => 'gf-section',
	);
};

return array(
	'title'                => 'Advisor Micro Intake',
	'description'          => '',
	'markupVersion'        => 2,
	'cssClass'             => 'gf-inquiry',
	'labelPlacement'       => 'top_label',
	'descriptionPlacement' => 'above',
	'subLabelPlacement'    => 'above',
	'requiredIndicator'    => 'asterisk',
	'button'               => array(
		'type'     => 'text',
		'text'     => 'Submit',
		'imageUrl' => '',
	),
	'fields'               => array(
		$section( 15, '01 — A Little About You' ),
		array(
			'type'                 => 'name',
			'id'                   => 1,
			'label'                => 'Your Name',
			'isRequired'           => true,
			'nameFormat'           => 'advanced',
			'layoutGridColumnSpan' => 6,
			'layoutGroupId'        => 'row-name-email',
			'inputs'               => array(
				array(
					'id'        => '1.2',
					'label'     => 'Prefix',
					'isHidden'  => true,
					'inputType' => 'radio',
				),
				array(
					'id'                    => '1.3',
					'label'                 => 'First',
					'autocompleteAttribute' => 'given-name',
				),
				array(
					'id'                    => '1.4',
					'label'                 => 'Middle',
					'isHidden'              => true,
					'autocompleteAttribute' => 'additional-name',
				),
				array(
					'id'                    => '1.6',
					'label'                 => 'Last',
					'autocompleteAttribute' => 'family-name',
				),
				array(
					'id'                    => '1.8',
					'label'                 => 'Suffix',
					'isHidden'              => true,
					'autocompleteAttribute' => 'honorific-suffix',
				),
			),
		),
		array(
			'type'                 => 'email',
			'id'                   => 2,
			'label'                => 'Email Address',
			'isRequired'           => true,
			'layoutGridColumnSpan' => 6,
			'layoutGroupId'        => 'row-name-email',
		),
		array(
			'type'                 => 'text',
			'id'                   => 3,
			'label'                => 'Your Business Name',
			'isRequired'           => true,
			'layoutGridColumnSpan' => 6,
			'layoutGroupId'        => 'row-business',
		),
		$radio(
			4,
			'Do you belong to a host agency?',
			array(
				'No — I operate independently / own my own agency',
				'Yes',
				"I'm not sure",
			)
		),
		array(
			'type'                 => 'text',
			'id'                   => 5,
			'label'                => 'What is the name of your host agency?',
			'isRequired'           => true,
			'layoutGridColumnSpan' => 6,
			'layoutGroupId'        => 'row-host',
			'conditionalLogic'     => $show_when( 4, 'Yes' ),
		),
		$radio(
			6,
			'Does your agency have any website requirements?',
			array(
				'No',
				'Yes',
				"I'm not sure",
			),
			array(
				'description' => 'For example, branding guidelines, required legal disclaimers, or content restrictions.',
			)
		),
		array(
			'type'                 => 'textarea',
			'id'                   => 7,
			'label'                => 'Tell us what you know.',
			'description'          => "Don't worry if you don't have every detail — we can sort through the specifics with you.",
			'isRequired'           => false,
			'layoutGridColumnSpan' => 12,
			'conditionalLogic'     => $show_when( 6, 'Yes' ),
		),
		$radio(
			8,
			'Do you already have a website?',
			array(
				'No',
				'Yes',
			)
		),
		array(
			'type'                 => 'website',
			'id'                   => 9,
			'label'                => "What's your current website address?",
			'isRequired'           => true,
			'layoutGridColumnSpan' => 6,
			'layoutGroupId'        => 'row-website',
			'conditionalLogic'     => $show_when( 8, 'Yes' ),
		),
		$section( 16, '02 — Make It Yours' ),
		$radio(
			10,
			'VacationCRM Integration',
			array(
				'No',
				'Yes',
				'Not sure yet',
			),
			array(
				'description'          => 'With a VCRM integration, you can create a custom form that asks any questions you’d like, all while still sending the lead over to your VCRM, this time with all the information you need baked in from the start.',
				'cssClass'             => 'gf-addon',
				'layoutGridColumnSpan' => 4,
				'layoutGroupId'        => 'row-addons',
			)
		),
		$radio(
			11,
			'White Glove Copywriting',
			array(
				'No',
				'Yes',
				'Not sure yet',
			),
			array(
				'description'          => 'This service includes a meeting with our copywriter, full website copy, and a round of revisions.',
				'cssClass'             => 'gf-addon',
				'layoutGridColumnSpan' => 4,
				'layoutGroupId'        => 'row-addons',
			)
		),
		$radio(
			12,
			'Graphic Design Package',
			array(
				'No',
				'Yes',
				'Not sure yet',
			),
			array(
				'description'          => 'This service includes a phone call with our graphic designer, a logo, full color suite, typography choices and a few pre-made graphics to use on socials and elsewhere. It allows one round of revisions.',
				'cssClass'             => 'gf-addon',
				'layoutGridColumnSpan' => 4,
				'layoutGroupId'        => 'row-addons',
			)
		),
		$radio(
			13,
			'How would you prefer to pay?',
			array(
				'Pay in full',
				'Payment plan',
				"I'm not sure yet",
			)
		),
		$section( 17, '03 — Anything Else?' ),
		$radio(
			18,
			'Did someone refer you to Windpeak Design?',
			array(
				'Yes',
				'No',
			),
			array(
				'isRequired' => false,
			)
		),
		array(
			'type'                 => 'text',
			'id'                   => 19,
			'label'                => 'Who can we thank for the referral?',
			'isRequired'           => false,
			'layoutGridColumnSpan' => 6,
			'layoutGroupId'        => 'row-referral',
			'conditionalLogic'     => $show_when( 18, 'Yes' ),
		),
		array(
			'type'                 => 'textarea',
			'id'                   => 14,
			'label'                => "Anything else you'd like us to know?",
			'description'          => "Questions, special requests, or anything else you'd like to discuss.",
			'isRequired'           => false,
			'cssClass'             => 'gf-notes',
			'layoutGridColumnSpan' => 12,
		),
	),
);
