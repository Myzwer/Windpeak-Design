<?php
/**
 * One-shot: sync Body Section ACF JSON + seed Home (ID 19) with all flex blocks.
 *
 * Usage (Local PHP):
 *   php bin/seed-homepage-flex.php
 */

if ( php_sapi_name() !== 'cli' ) {
	exit( "CLI only\n" );
}

$theme_dir = dirname( __DIR__ ); // .../themes/windpeak
$root      = dirname( $theme_dir, 3 ); // .../app/public
require_once $root . '/wp-load.php';

if ( ! function_exists( 'update_field' ) ) {
	fwrite( STDERR, "ACF not available\n" );
	exit( 1 );
}

// Ensure Windpeak is the active theme for this Local site.
if ( 'windpeak' !== get_stylesheet() ) {
	switch_theme( 'windpeak' );
	echo "Switched active theme to windpeak\n";
}

$json_path = $theme_dir . '/acf-json/group_659a3e1127750.json';
$json      = json_decode( (string) file_get_contents( $json_path ), true );
if ( ! $json ) {
	fwrite( STDERR, "Could not read ACF JSON at {$json_path}\n" );
	exit( 1 );
}

// Soft sync: only import if the group is not already loaded (avoids heavy re-import).
$existing = function_exists( 'acf_get_field_group' ) ? acf_get_field_group( 'group_659a3e1127750' ) : null;
if ( function_exists( 'acf_import_field_group' ) && ( ! $existing || empty( $existing['ID'] ) ) ) {
	$result = acf_import_field_group( $json );
	echo "Imported field group: " . ( is_array( $result ) ? ( $result['key'] ?? 'ok' ) : 'ok' ) . "\n";
} else {
	echo "Field group already present — skipping import\n";
}

$home_id = (int) get_option( 'page_on_front' ) ?: 19;

$fa = static function ( string $class ): string {
	return '<i class="' . esc_attr( $class ) . '" aria-hidden="true"></i>';
};

$link = static function ( string $title, string $url = '#' ): array {
	return array(
		'title'  => $title,
		'url'    => $url,
		'target' => '',
	);
};

$body = array(
	// 1 Standard text
	array(
		'acf_fc_layout' => 'text_block',
		'content'       => '<h2>IMAGINE A WEBSITE YOU DON\'T HAVE TO WORRY ABOUT.</h2><p>We get it, you started a business because you are really good at what you do. And then somewhere along the way you became responsible for a website. You have real work to do, and instead you\'re chasing down an image that ran off screen or trying to figure out why your form “looks like that” on mobile. So why not pass that off to a team you trust so you can focus on the thing you know how to do better than anyone else: Run your business.</p><p><a class="btn_main" href="#"><span>Start the Process</span></a></p>',
	),
	// 2 Image + text
	array(
		'acf_fc_layout' => 'image_text_block',
		'image'         => 807,
		'content'       => '<h2>Built for people who would rather run their business than babysit a website.</h2><p>Windpeak handles the strategy, design, development, and ongoing care so your site stays sharp without eating your week.</p>',
	),
	// 3 CTA
	array(
		'acf_fc_layout' => 'cta_block',
		'heading'       => "BIG SITE?\nLITTLE SITE?\nWEIRD SITE?",
		'content'       => 'We got you. :)',
		'link'          => $link( 'Start the Process' ),
	),
	// 4 List
	array(
		'acf_fc_layout' => 'list_block',
		'intro'         => '<h2>SERVICES</h2><p>A Windpeak website means you look as good online as you do in person, and you didn’t have to spend 4 months learning how to do it.</p>',
		'list_items'    => array(
			array( 'icon' => $fa( 'fa-solid fa-pen-to-square' ), 'list_item_title' => 'Collaborative planning' ),
			array( 'icon' => $fa( 'fa-solid fa-pen-to-square' ), 'list_item_title' => 'Human written copy and design' ),
			array( 'icon' => $fa( 'fa-solid fa-pen-to-square' ), 'list_item_title' => 'Custom, flexible development' ),
			array( 'icon' => $fa( 'fa-solid fa-pen-to-square' ), 'list_item_title' => 'Ongoing support' ),
		),
		'link'          => $link( 'Start the Process' ),
	),
	// 5 Quote
	array(
		'acf_fc_layout' => 'quote_block',
		'intro'         => '<h2>DON’T TAKE OUR WORD FOR IT</h2>',
		'quote'         => '<p>“We really appreciate all of your advice and guidance while navigating through the website building process! It’s very refreshing to deal with someone who can communicate WELL!”</p>',
		'name'          => 'Jamie',
		'attribution'   => 'Land & Farm Acquisitions',
	),
	// 6 Form
	array(
		'acf_fc_layout' => 'form_block',
		'intro'         => '<h2>START THE PROCESS</h2>',
		'form_id'       => '1',
	),
	// 7 Accordion
	array(
		'acf_fc_layout'   => 'accordion_block',
		'image'           => 624,
		'intro'           => '<h2>STILL HAVE QUESTIONS?</h2>',
		'accordion_items' => array(
			array(
				'accordion_title'   => 'How long does a typical site take?',
				'accordion_content' => '<p>Most Windpeak builds land in a clear timeline we set together during the kickoff chat — with deadlines, reviews, and launch support baked in.</p>',
			),
			array(
				'accordion_title'   => 'Do I have to write my own copy?',
				'accordion_content' => '<p>Nope. We can start from starter copy, white-glove messaging, or a hybrid — whatever matches how hands-on you want to be.</p>',
			),
			array(
				'accordion_title'   => 'What happens after launch?',
				'accordion_content' => '<p>Hosting, care, and optional token packs keep the site moving without you hunting plugins at midnight.</p>',
			),
		),
	),
	// 8 Headers and text (process — image right)
	array(
		'acf_fc_layout' => 'headers_text_block',
		'header'        => 'THE PROCESS',
		'image'         => 795,
		'image_side'    => 'right',
		'items'         => array(
			array(
				'item_title' => 'CHAT',
				'item_text'  => '<p>The first step to getting you hours of your life back. We’ll set up a zoom or a phone call and chat through everything that makes your business what it is so we can learn how Windpeak can best serve you.</p>',
			),
			array(
				'item_title' => 'CREATE',
				'item_text'  => '<p>We make your website while you spend your new found free time making your business better. We stay in constant communication with deadlines, approvals and such so you always know what is going on.</p>',
			),
			array(
				'item_title' => 'LAUNCH',
				'item_text'  => '<p>We ship your incredible new website to the internet for the world to see. You celebrate. We celebrate.</p>',
			),
			array(
				'item_title' => 'SUPPORT',
				'item_text'  => '<p>We provide training, answer questions, and offer a variety of care options so you can be as involved — or uninvolved — as you’d like.</p>',
			),
		),
		'link'          => $link( 'Start the Process' ),
	),
	// 9 Headers and text (working with us — image left)
	array(
		'acf_fc_layout' => 'headers_text_block',
		'header'        => 'WORKING WITH US',
		'image'         => 803,
		'image_side'    => 'left',
		'items'         => array(
			array(
				'item_title' => 'UNIQUELY YOURS',
				'item_text'  => '<p>No cookie-cutter templates pretending to be custom. Your site is built around how you actually work.</p>',
			),
			array(
				'item_title' => 'SOMEONE IN YOUR CORNER, ALWAYS',
				'item_text'  => '<p>Questions, tweaks, “why does this look like that” — you have a real person who already knows your project.</p>',
			),
			array(
				'item_title' => 'HUMANS!',
				'item_text'  => '<p>Copy, design, and development by people. Not a content farm. Not a mystery offshore roulette wheel.</p>',
			),
		),
		'link'          => $link( 'Start the Process' ),
	),
	// 10 Bio
	array(
		'acf_fc_layout' => 'bio_block',
		'content'       => '<h2>HEY</h2><p>I’m Josh — the human behind Windpeak. I’ve spent years building WordPress sites for people who are great at their craft and would rather not become accidental webmasters.</p><p>When I’m not shipping websites I’m usually outside with my family, drinking too much coffee, or explaining why “just one more plugin” is rarely the answer.</p><p>A portion of proceeds goes toward feeding my children. They are very persuasive.</p>',
		'photos'        => array( 811, 812, 813 ),
	),
	// 11 Projects
	array(
		'acf_fc_layout' => 'projects_block',
		'intro'         => '<h2>PROJECTS</h2>',
		'projects'      => array(
			array(
				'screenshot' => 808,
				'image_side' => 'left',
				'content'    => '<h3>Coasters &amp; Castles Travel</h3><p>A travel agency that outgrew their Wix site and needed a flexible custom theme, advisor tooling, and room to grow.</p>',
			),
			array(
				'screenshot' => 809,
				'image_side' => 'right',
				'content'    => '<h3>Land and Farms Acquisitions</h3><p>A farmer who needed a professional website without having to manage the technical side himself.</p>',
			),
		),
		'link'          => $link( 'See Our Work' ),
	),
	// 12 Lead capture
	array(
		'acf_fc_layout' => 'lead_capture_block',
		'intro'         => '<h2>CAN I SEE ONE OF THESE?</h2>',
		'screenshot'    => 808,
		'content'       => '<p>Check out a demo site here:</p>',
		'link'          => $link( 'See The Demo' ),
	),
	// 13 Selection
	array(
		'acf_fc_layout' => 'selection_block',
		'intro'         => '<h2>SERVICES</h2>',
		'services'      => array(
			array(
				'icon'           => $fa( 'fa-solid fa-code' ),
				'label'          => 'CUSTOM WORDPRESS DEVELOPMENT',
				'detail_content' => '<h3>Custom WordPress Development</h3><p>Flexible themes, custom tooling, and builds that match how your business actually runs.</p>',
			),
			array(
				'icon'           => $fa( 'fa-solid fa-pen-nib' ),
				'label'          => 'COPYWRITING & BRAND MESSAGING',
				'detail_content' => '<h3>Copywriting &amp; Brand Messaging</h3><p>Messaging that clarifies what you do, who it’s for, and what visitors should do next.</p>',
			),
			array(
				'icon'           => $fa( 'fa-solid fa-image' ),
				'label'          => 'FULL GRAPHIC DESIGN',
				'detail_content' => '<h3>Full Graphic Design</h3><p>Visual systems that look intentional on every page — not a pile of mismatched stock photos.</p>',
			),
			array(
				'icon'           => $fa( 'fa-solid fa-table-cells-large' ),
				'label'          => 'UX & UI DESIGN',
				'detail_content' => '<h3>UX &amp; UI Design</h3><p>Clear paths, readable layouts, and interfaces people can actually use on their phones.</p>',
			),
			array(
				'icon'           => $fa( 'fa-solid fa-handshake' ),
				'label'          => 'TRAINING & HANDOFF',
				'detail_content' => '<h3>Training &amp; Handoff</h3><p>So you’re never left guessing which button does what after launch day.</p>',
			),
			array(
				'icon'           => $fa( 'fa-solid fa-laptop-code' ),
				'label'          => 'WEBSITE CARE & SUPPORT',
				'detail_content' => '<h3>Website Care &amp; Support</h3><p>Hosting, updates, and human help when something needs a tweak.</p>',
			),
		),
	),
	// 14 Pricing
	array(
		'acf_fc_layout'   => 'pricing_block',
		'intro'           => '<h2>CHOOSE THE SUPPORT LEVEL THAT FITS</h2><p>Every plan includes the same core hosting and maintenance. The difference is how much hands-on help you want from Windpeak each month.</p>',
		'includes_label'  => 'EVERY PLAN INCLUDES',
		'includes'        => array(
			array( 'include_label' => 'Managed Wordpress Hosting' ),
			array( 'include_label' => 'Managed Wordpress Hosting' ),
			array( 'include_label' => 'Managed Wordpress Hosting' ),
			array( 'include_label' => 'Managed Wordpress Hosting' ),
			array( 'include_label' => 'Managed Wordpress Hosting' ),
		),
		'plans'           => array(
			array(
				'price'          => '$69',
				'price_suffix'   => '/mo',
				'plan_title'     => 'Hosting & Care',
				'tokens_line'    => '0 Tokens / month',
				'best_for_label' => 'Best for',
				'best_for'       => 'Mostly hands-off sites',
			),
			array(
				'price'          => '$69',
				'price_suffix'   => '/mo',
				'plan_title'     => 'Hosting & Care',
				'tokens_line'    => '0 Tokens / month',
				'best_for_label' => 'Best for',
				'best_for'       => 'Mostly hands-off sites',
			),
			array(
				'price'          => '$69',
				'price_suffix'   => '/mo',
				'plan_title'     => 'Hosting & Care',
				'tokens_line'    => '0 Tokens / month',
				'best_for_label' => 'Best for',
				'best_for'       => 'Mostly hands-off sites',
			),
		),
		'footer'          => '<p>Need A Little Extra Help? Additional Tokens Are Available In Packs Of 5 For $175. If You’re Buying Token Packs Regularly, Upgrading Your Plan Will Usually Save You Money.</p>',
	),
	// 15 Dynamic pricing
	array(
		'acf_fc_layout'     => 'dynamic_pricing_block',
		'intro'             => '<h2>YOU CHOOSE HOW YOU PAY</h2><p>Travel advisor Micros can be paid in two different ways.</p>',
		'payment_options'   => array(
			array(
				'option_title' => 'Pay Upfront',
				'option_text'  => '<p>Pay for the website and any add-ons immediately, then keep a simple monthly hosting fee going forward.</p>',
			),
			array(
				'option_title' => 'Spread it out',
				'option_text'  => '<p>Split the site and add-ons across the first 12 months, then drop back to the standard monthly hosting fee.</p>',
			),
		),
		'calculator_title'  => 'Pricing Calculator',
		'base_site'         => 900,
		'monthly_service'   => 69,
		'term_months'       => 12,
		'addon_vacation_crm'=> 250,
		'addon_lead_capture'=> 250,
		'addon_white_glove_copy' => 400,
		'addon_graphic_design'   => 400,
	),
	// 16 Case study
	array(
		'acf_fc_layout' => 'case_study_block',
		'screenshot'    => 808,
		'eyebrow'       => 'THE BIG SITE',
		'title'         => 'CASE STUDY 1: COASTERS & CASTLES TRAVEL',
		'subtitle'      => 'Travel Agency - 10 Key Pages - Feature Rich - High Monthly Maintenance',
		'intro'         => '<p>Coasters &amp; Castles needed more than a brochure site — advisors, CRM hooks, and a flexible theme that could grow with the agency.</p>',
		'sections'      => array(
			array(
				'section_title' => 'The Situation',
				'section_text'  => '<p>They were outgrowing Wix and needed a maintainable custom WordPress build without losing the personality of the brand.</p>',
			),
			array(
				'section_title' => 'What We Did',
				'section_text'  => '<p>Brand strategy, full copy, custom flex theme, advisor tooling, and launch support — then ongoing partnership for care.</p>',
			),
			array(
				'section_title' => 'The Result',
				'section_text'  => '<p>A feature-rich marketing site that still feels human, with systems that make day-to-day updates realistic.</p>',
			),
		),
		'work_heading'  => 'The Work Included',
		'work_items'    => array(
			array( 'icon' => $fa( 'fa-solid fa-pen-to-square' ), 'item_label' => 'Custom Flex Theme' ),
			array( 'icon' => $fa( 'fa-solid fa-pen-to-square' ), 'item_label' => 'Filterable Advisor List' ),
			array( 'icon' => $fa( 'fa-solid fa-pen-to-square' ), 'item_label' => 'Advisor Page Moderation Tools' ),
			array( 'icon' => $fa( 'fa-solid fa-pen-to-square' ), 'item_label' => 'Custom Intake Form With Spam and Scam Protections' ),
			array( 'icon' => $fa( 'fa-solid fa-pen-to-square' ), 'item_label' => 'Full Blog' ),
			array( 'icon' => $fa( 'fa-solid fa-pen-to-square' ), 'item_label' => 'Custom Advisor Logins' ),
			array( 'icon' => $fa( 'fa-solid fa-pen-to-square' ), 'item_label' => 'VacationCRM API Integration' ),
		),
		'link'          => $link( 'View Live Site' ),
	),
);

$ok = update_field( 'body_sections', $body, $home_id );
echo $ok ? "Updated body_sections on post {$home_id}\n" : "update_field returned false (check field group sync)\n";

// Verify layout count
$rows = get_field( 'body_sections', $home_id );
echo 'Rows: ' . ( is_array( $rows ) ? count( $rows ) : 0 ) . "\n";
if ( is_array( $rows ) ) {
	foreach ( $rows as $row ) {
		echo ' - ' . ( $row['acf_fc_layout'] ?? '?' ) . "\n";
	}
}
