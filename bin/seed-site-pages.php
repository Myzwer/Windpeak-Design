<?php
/**
 * Seed Windpeak pages from mockups + approved copy.
 *
 * Usage (Local PHP + socket):
 *   php bin/seed-site-pages.php
 */

if ( php_sapi_name() !== 'cli' ) {
	exit( "CLI only\n" );
}

$theme_dir = dirname( __DIR__ );
$root      = dirname( $theme_dir, 3 );
$sock      = getenv( 'WINDPEAK_DB_SOCKET' ) ?: '/Users/josh/Library/Application Support/Local/run/ZSEFDTG9R/mysql/mysqld.sock';
$config    = $root . '/wp-config.php';
$orig      = file_get_contents( $config );

if ( false === $orig ) {
	fwrite( STDERR, "Could not read wp-config.php\n" );
	exit( 1 );
}

$patched = preg_replace(
	"/define\(\s*'DB_HOST'\s*,\s*'[^']*'\s*\)/",
	"define( 'DB_HOST', 'localhost:" . addcslashes( $sock, '\\' ) . "' )",
	$orig,
	1,
	$count
);

if ( ! $count ) {
	fwrite( STDERR, "Could not patch DB_HOST\n" );
	exit( 1 );
}

file_put_contents( $config, $patched );

$restore = static function () use ( $config, $orig ): void {
	file_put_contents( $config, $orig );
};

register_shutdown_function( $restore );

require_once $root . '/wp-load.php';

if ( ! function_exists( 'update_field' ) ) {
	fwrite( STDERR, "ACF not available\n" );
	exit( 1 );
}

if ( 'windpeak' !== get_stylesheet() ) {
	switch_theme( 'windpeak' );
	echo "Switched theme to windpeak\n";
}

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

$fa = static function ( string $class ): string {
	return '<i class="' . esc_attr( $class ) . '" aria-hidden="true"></i>';
};

$link = static function ( string $title, string $url ): array {
	return array(
		'title'  => $title,
		'url'    => $url,
		'target' => '',
	);
};

$home_url = home_url( '/' );
$contact  = home_url( '/contact/' );
$services = home_url( '/services/' );
$work     = home_url( '/our-work/' );
$tokens   = home_url( '/tokens/' );

// Stand-in photos (user-provided) + existing project/bio media.
$img = array(
	'chad'      => 618,
	'chris'     => 619,
	'jeremy'    => 620,
	'nathan'    => 621,
	'sam'       => 622,
	'will'      => 623,
	'video'     => 646,
	'coasters'  => 808,
	'lfa'       => 809,
	'bio1'      => 811,
	'bio2'      => 812,
	'bio3'      => 813,
	'app_wide'  => 624,
);

$btn = static function ( string $label, string $url ): string {
	return '<p><a class="btn_main" href="' . esc_url( $url ) . '"><span>' . esc_html( $label ) . '</span></a></p>';
};

$clear_flex = static function ( int $post_id ): void {
	delete_field( 'header_select', $post_id );
	delete_field( 'body_sections', $post_id );
};

$set_header = static function ( int $post_id, array $header_rows ): void {
	$ok = update_field( 'header_select', $header_rows, $post_id );
	echo $ok ? "  header ok\n" : "  header FAILED\n";
};

$set_body = static function ( int $post_id, array $body ): void {
	$ok = update_field( 'body_sections', $body, $post_id );
	$rows = get_field( 'body_sections', $post_id );
	$count = is_array( $rows ) ? count( $rows ) : 0;
	echo $ok ? "  body ok ({$count} rows)\n" : "  body FAILED\n";
	if ( is_array( $rows ) ) {
		foreach ( $rows as $row ) {
			echo '    - ' . ( $row['acf_fc_layout'] ?? '?' ) . "\n";
		}
	}
};

// ---------------------------------------------------------------------------
// Gravity Forms: Contact + Advisor Intake
// ---------------------------------------------------------------------------

$ensure_forms = static function () use ( $home_url ): array {
	$out = array(
		'contact' => 0,
		'advisor' => 0,
	);

	if ( ! class_exists( 'GFAPI' ) ) {
		echo "GFAPI missing — form blocks will be empty\n";
		return $out;
	}

	$find = static function ( string $title ): int {
		$forms = GFAPI::get_forms( true );
		if ( ! is_array( $forms ) ) {
			return 0;
		}
		foreach ( $forms as $form ) {
			if ( isset( $form['title'] ) && $form['title'] === $title ) {
				return (int) $form['id'];
			}
		}
		return 0;
	};

	$success_url = home_url( '/submission-successful/' );

	$contact_title = 'Start the Process';
	$contact_id    = $find( $contact_title );

	if ( ! $contact_id ) {
		$form = array(
			'title'              => $contact_title,
			'description'        => '',
			'labelPlacement'     => 'top_label',
			'descriptionPlacement' => 'below',
			'button'             => array(
				'type'     => 'text',
				'text'     => 'Submit',
				'imageUrl' => '',
			),
			'fields'             => array(
				array(
					'type'     => 'name',
					'id'       => 1,
					'label'    => 'Name',
					'isRequired' => true,
					'nameFormat' => 'advanced',
					'inputs'   => array(
						array( 'id' => '1.3', 'label' => 'First', 'name' => '' ),
						array( 'id' => '1.6', 'label' => 'Last', 'name' => '' ),
					),
				),
				array(
					'type'       => 'email',
					'id'         => 2,
					'label'      => 'Email Address',
					'isRequired' => true,
				),
				array(
					'type'  => 'text',
					'id'    => 3,
					'label' => 'Business Name',
					'isRequired' => true,
				),
				array(
					'type'  => 'website',
					'id'    => 4,
					'label' => 'Current Website (optional)',
					'isRequired' => false,
				),
				array(
					'type'  => 'text',
					'id'    => 5,
					'label' => 'Business Type / Industry',
					'isRequired' => true,
				),
				array(
					'type'    => 'select',
					'id'      => 6,
					'label'   => 'Team Size',
					'description' => 'Contractors can count as team here',
					'isRequired' => true,
					'choices' => array(
						array( 'text' => 'Just me', 'value' => 'Just me' ),
						array( 'text' => '2-5', 'value' => '2-5' ),
						array( 'text' => '6-20', 'value' => '6-20' ),
						array( 'text' => '21-50', 'value' => '21-50' ),
						array( 'text' => '50+', 'value' => '50+' ),
					),
				),
				array(
					'type'    => 'select',
					'id'      => 7,
					'label'   => 'Timeline',
					'isRequired' => true,
					'choices' => array(
						array( 'text' => "I'm exploring options", 'value' => "I'm exploring options" ),
						array( 'text' => 'Within the next few months', 'value' => 'Within the next few months' ),
						array( 'text' => 'I have a specific deadline', 'value' => 'I have a specific deadline' ),
						array( 'text' => 'As soon as reasonably possible', 'value' => 'As soon as reasonably possible' ),
					),
				),
				array(
					'type'        => 'checkbox',
					'id'          => 8,
					'label'       => 'What can we help with?',
					'description' => 'Check all that apply',
					'isRequired'  => true,
					'choices'     => array(
						array( 'text' => 'New Website', 'value' => 'New Website' ),
						array( 'text' => 'Redesign of existing website', 'value' => 'Redesign of existing website' ),
						array( 'text' => 'Writing / Copy Services', 'value' => 'Writing / Copy Services' ),
						array( 'text' => 'Logo Design and Visual Branding', 'value' => 'Logo Design and Visual Branding' ),
						array( 'text' => "I'm not sure yet", 'value' => "I'm not sure yet" ),
						array( 'text' => 'Something else', 'value' => 'Something else' ),
					),
					'inputs'      => array(
						array( 'id' => '8.1', 'label' => 'New Website' ),
						array( 'id' => '8.2', 'label' => 'Redesign of existing website' ),
						array( 'id' => '8.3', 'label' => 'Writing / Copy Services' ),
						array( 'id' => '8.4', 'label' => 'Logo Design and Visual Branding' ),
						array( 'id' => '8.5', 'label' => "I'm not sure yet" ),
						array( 'id' => '8.6', 'label' => 'Something else' ),
					),
				),
				array(
					'type'       => 'textarea',
					'id'         => 9,
					'label'      => 'Tell us what is going on and what you’re hoping to accomplish.',
					'isRequired' => true,
				),
			),
			'confirmations'      => array(
				'default' => array(
					'id'          => 'default',
					'name'        => 'Default Confirmation',
					'isDefault'   => true,
					'type'        => 'page',
					'message'     => '',
					'url'         => '',
					'pageId'      => (string) 452,
					'queryString' => '',
				),
			),
			'notifications'      => array(),
		);

		$result = GFAPI::add_form( $form );
		if ( is_wp_error( $result ) ) {
			echo 'Contact form error: ' . $result->get_error_message() . "\n";
		} else {
			$contact_id = (int) $result;
			echo "Created Contact form ID {$contact_id}\n";
		}
	} else {
		echo "Contact form already exists ID {$contact_id}\n";
	}

	$advisor_title = 'Advisor Micro Intake';
	$advisor_id    = $find( $advisor_title );
	$form          = require __DIR__ . '/advisor-inquiry-form.php';

	if ( ! $advisor_id ) {
		$form['confirmations'] = array(
			'default' => array(
				'id'        => 'default',
				'name'      => 'Default Confirmation',
				'isDefault'   => true,
				'type'        => 'page',
				'pageId'      => (string) 452,
				'queryString' => '',
			),
		);

		$result = GFAPI::add_form( $form );
		if ( is_wp_error( $result ) ) {
			echo 'Advisor form error: ' . $result->get_error_message() . "\n";
		} else {
			$advisor_id = (int) $result;
			echo "Created Advisor form ID {$advisor_id}\n";
		}
	} else {
		$existing                 = GFAPI::get_form( $advisor_id );
		$form['id']               = $advisor_id;
		$form['is_active']        = '1';
		$form['confirmations']    = $existing['confirmations'] ?? array();
		$form['notifications']    = $existing['notifications'] ?? array();
		$result                   = GFAPI::update_form( $form, $advisor_id );
		if ( is_wp_error( $result ) ) {
			echo 'Advisor form update error: ' . $result->get_error_message() . "\n";
		} else {
			echo "Updated Advisor form ID {$advisor_id}\n";
		}
	}

	$out['contact'] = $contact_id;
	$out['advisor'] = $advisor_id;

	return $out;
};

$forms = $ensure_forms();

// ---------------------------------------------------------------------------
// HOME (19)
// ---------------------------------------------------------------------------

echo "\n=== HOME ===\n";
$home_id = (int) get_option( 'page_on_front' ) ?: 19;
$clear_flex( $home_id );

$set_header(
	$home_id,
	array(
		array(
			'acf_fc_layout'          => 'video_header',
			'poster_image'           => $img['will'],
			'video_background'       => $img['video'],
			'background_tint_level'  => '45',
			'title'                  => 'Windpeak Design',
			'subtitle'               => 'Run your business // not your website.',
			'primary_cta'            => $link( 'Start the Process', $contact ),
			'secondary_cta'          => '',
		),
	)
);

$set_body(
	$home_id,
	array(
		array(
			'acf_fc_layout' => 'text_block',
			'content'       => '<h2>Imagine a website you don’t have to worry about.</h2><p>We get it: you started a business because you are really good at what you do. And then somewhere along the way you became responsible for a website. You have real work to do, and instead you’re chasing down an image that ran off-screen or trying to figure out why your form “looks like that” on mobile. So why not pass that off to a team you trust so you can focus on the thing you know how to do better than anyone else: Run your business.</p>' . $btn( 'Start the Process', $contact ),
		),
		array(
			'acf_fc_layout' => 'list_block',
			'intro'         => '<h2>Services</h2><p>A Windpeak website means you look as good online as you do in person, and you didn’t have to spend 4 months learning how to do it.</p>',
			'list_items'    => array(
				array( 'icon' => $fa( 'fa-solid fa-comments' ), 'list_item_title' => 'Collaborative planning' ),
				array( 'icon' => $fa( 'fa-solid fa-pen-nib' ), 'list_item_title' => 'Human-written copy and design' ),
				array( 'icon' => $fa( 'fa-solid fa-code' ), 'list_item_title' => 'Custom, flexible development' ),
				array( 'icon' => $fa( 'fa-solid fa-handshake' ), 'list_item_title' => 'Ongoing support' ),
			),
			'link'          => $link( 'See what we do', $services ),
		),
		array(
			'acf_fc_layout' => 'projects_block',
			'intro'         => '<h2>Projects</h2>',
			'projects'      => array(
				array(
					'screenshot' => $img['coasters'],
					'image_side' => 'left',
					'content'    => '<h3>Coasters &amp; Castles Travel</h3><p>Coasters &amp; Castles Travel is a travel agency with a full-service team of collaborative travel advisors. They had long outgrown their old Wix site and came to us because they were tired of arguing with it. It needed more features than Wix allowed, it constantly broke, and they felt like they never had control. With a new website, they have peace of mind and hours of their week back.</p>',
				),
				array(
					'screenshot' => $img['lfa'],
					'image_side' => 'right',
					'content'    => '<h3>Land and Farms Acquisitions</h3><p>Land and Farms Acquisitions had been in business for a while, but had yet to build a website to put that business out into the world. Their owner, Jamie, had enough to worry about while running the business and his own farm. Together, we built a site that brings clarity to the right audience and makes it easy for them to get in touch.</p>',
				),
			),
			'link'          => $link( 'See our Work', $work ),
		),
		array(
			'acf_fc_layout' => 'image_text_block',
			'image'         => $img['sam'],
			'content'       => '<p>Windpeak Design is a small team of freelance designers, developers, and copywriters creating websites for small businesses who want more than a template can offer them.</p><p>We are real humans who have worked in this industry for a combined 30+ years. You get all the benefits of that experience poured into a site made just for you, so you can keep doing what you do best: running your business.</p>' . $btn( 'Start the Process', $contact ),
		),
		array(
			'acf_fc_layout' => 'quote_block',
			'intro'         => '<h2>Don’t take our word for it</h2>',
			'quote'         => '<p>“We really appreciate all of your advice and guidance while navigating through the website building process! It’s very refreshing to deal with someone who can communicate WELL!”</p>',
			'name'          => 'Jamie',
			'attribution'   => 'Land & Farms Acquisitions',
		),
		array(
			'acf_fc_layout' => 'cta_block',
			'heading'       => "Big Site?\nLittle Site?\nWeird Site?",
			'content'       => 'We got you. :)',
			'link'          => $link( 'Start the Process', $contact ),
		),
	)
);

// ---------------------------------------------------------------------------
// SERVICES (629)
// ---------------------------------------------------------------------------

echo "\n=== SERVICES ===\n";
$services_id = 629;
$clear_flex( $services_id );

$set_header(
	$services_id,
	array(
		array(
			'acf_fc_layout' => 'simple_header',
			'eyebrow'       => 'Services',
			'title'         => 'A real website built the right way by real people.',
			'button'        => $link( 'Start the Process', $contact ),
		),
	)
);

$set_body(
	$services_id,
	array(
		array(
			'acf_fc_layout' => 'image_text_block',
			'image'         => $img['nathan'],
			'content'       => '<p>You launched your small business because you know you’re the best at what you do, not because you wanted to spend 3 nights a week fussing over how your website works and trying to make every pixel cooperate.</p><p>Windpeak Design removes that hassle by creating a custom site that is easy to maintain, looks incredible, and gives your business the online presence it deserves. We are here with you every step of the way, from planning to execution to launch, and beyond.</p><p>We can’t run your business for you; you’re the best at that. But we can run your website, and we’d like to think we are the best at that.</p>',
		),
		array(
			'acf_fc_layout' => 'selection_block',
			'intro'         => '<h2>Services</h2>',
			'services'      => array(
				array(
					'icon'           => $fa( 'fa-solid fa-code' ),
					'label'          => 'Custom WordPress Development',
					'detail_content' => '<h3>Custom WordPress Development</h3><p>Every site is built uniquely so you can look as personal as your business.</p>',
				),
				array(
					'icon'           => $fa( 'fa-solid fa-pen-nib' ),
					'label'          => 'Copywriting & Brand Messaging',
					'detail_content' => '<h3>Copywriting &amp; Brand Messaging</h3><p>Messaging that clarifies what you do, who it’s for, and what visitors should do next.</p>',
				),
				array(
					'icon'           => $fa( 'fa-solid fa-palette' ),
					'label'          => 'Full Graphic Design',
					'detail_content' => '<h3>Full Graphic Design</h3><p>Fonts, colors, and even a logo if you need it.</p>',
				),
				array(
					'icon'           => $fa( 'fa-solid fa-table-cells-large' ),
					'label'          => 'UX & UI Design',
					'detail_content' => '<h3>UX &amp; UI Design</h3><p>Clean, modern design that makes your site intuitive, readable, and easy to use.</p>',
				),
				array(
					'icon'           => $fa( 'fa-solid fa-chalkboard-user' ),
					'label'          => 'Training & Handoff',
					'detail_content' => '<h3>Training &amp; Handoff</h3><p>You’ll know how to not only edit your website, but feel confident in doing so.</p>',
				),
				array(
					'icon'           => $fa( 'fa-solid fa-headset' ),
					'label'          => 'Website Care & Support',
					'detail_content' => '<h3>Website Care &amp; Support</h3><p>The relationship doesn’t end when the site goes live. We are here to support you until the end of time (or you end your agreement).</p>',
				),
			),
		),
		array(
			'acf_fc_layout' => 'headers_text_block',
			'header'        => 'Working with us',
			'image'         => $img['jeremy'],
			'image_side'    => 'right',
			'items'         => array(
				array(
					'item_title' => 'Uniquely Yours',
					'item_text'  => '<p>Your business is unique, and your website needs to be too. Here at Windpeak, we craft every site with intention, so your template doesn’t look like every other Wix, Squarespace, or Elementor template out there. Skip the headaches of figuring out another platform and jump straight to a website that looks exactly how you want it to look.</p>',
				),
				array(
					'item_title' => 'Someone in your Corner, Always',
					'item_text'  => '<p>We are a small business that helps other small businesses. Our success is your success. That means we are by your side from the beginning to post-launch. Once your site is live, you aren’t left alone to figure it out from there. We are here to support you, answer questions, strategize, test, and grow your business right beside you.</p>',
				),
				array(
					'item_title' => 'Humans!',
					'item_text'  => '<p>We are a small team, you’re a small team! We value human connection, getting to know you and what you do (and why you’re the best), and how we can come alongside you in a world where it seems like robots talk to each other more than people do. All of our copy and design is created by real human beings for real human beings.</p>',
				),
			),
			'link'          => $link( 'Start the Process', $contact ),
		),
		array(
			'acf_fc_layout' => 'list_block',
			'intro'         => '<h2>Hold on, you need what?</h2><p>We got it. :)</p><p>Because all of our sites are custom, if you can dream it, we can build it. Maybe you got something half working on your current site, or you’re just dreaming about how much easier life would be if you could just add this one feature. Maybe you saw something on another site, maybe you have no clue what would help you save hours of your week.</p><p>We are more than happy to chat about your business’ unique situation and recommend solutions that are appropriate for YOU. We don’t ship every site with every feature, because why pay for what you don’t plan to use in the first place? We will look at your business, figure out what you actually need, and get you a solution that works for you and only you.</p>',
			'list_items'    => array(
				array( 'icon' => $fa( 'fa-solid fa-compass' ), 'list_item_title' => 'Full Brand Strategy' ),
				array( 'icon' => $fa( 'fa-solid fa-fingerprint' ), 'list_item_title' => 'Full Brand Identity (Logo + Visual System)' ),
				array( 'icon' => $fa( 'fa-solid fa-cart-shopping' ), 'list_item_title' => 'E-Commerce Setup' ),
				array( 'icon' => $fa( 'fa-solid fa-file-import' ), 'list_item_title' => 'Content Migration' ),
				array( 'icon' => $fa( 'fa-solid fa-plug' ), 'list_item_title' => 'CRM Integration' ),
				array( 'icon' => $fa( 'fa-solid fa-user-lock' ), 'list_item_title' => 'User Logins' ),
				array( 'icon' => $fa( 'fa-solid fa-door-open' ), 'list_item_title' => 'Client Portals' ),
				array( 'icon' => $fa( 'fa-solid fa-sparkles' ), 'list_item_title' => 'Pretty much anything else you can dream up' ),
			),
			'link'          => $link( 'Start the Process', $contact ),
		),
		array(
			'acf_fc_layout' => 'headers_text_block',
			'header'        => 'The Process',
			'image'         => $img['chris'],
			'image_side'    => 'left',
			'items'         => array(
				array(
					'item_title' => 'Chat',
					'item_text'  => '<p>The first step to getting you hours of your life back. We’ll set up a Zoom or a phone call and chat through everything that makes your business what it is so we can learn how Windpeak can best serve you.</p>',
				),
				array(
					'item_title' => 'Create',
					'item_text'  => '<p>We make your website while you spend your newfound free time making your business better. We stay in constant communication with deadlines, approvals, and feedback so you always know what’s happening next.</p>',
				),
				array(
					'item_title' => 'Launch',
					'item_text'  => '<p>We ship your new website to the internet for the world to see. You celebrate. We celebrate with you.</p>',
				),
				array(
					'item_title' => 'Support',
					'item_text'  => '<p>We provide training, answer questions, and offer a variety of care options to make sure that you can be as involved (or as uninvolved) with your new website as you’d like to be. Either way, we are here for you.</p>',
				),
			),
			'link'          => $link( 'Start the Process', $contact ),
		),
		array(
			'acf_fc_layout' => 'text_block',
			'content'       => '<h2>Priced for your business, not someone else\'s.</h2><p>A travel agency client with 100 advisors, custom logins and moderation, a CRM integration, and a full blog is a very different project than a farm acquisition client just trying to get their name on the internet.</p><p>We price based on the work required and the business, so you don’t overpay for what you don’t need. But don’t worry, we don’t play games with fees and upcharges—you’ll always know the price of the project before we get started.</p>',
		),
		array(
			'acf_fc_layout' => 'image_text_block',
			'image'         => $img['chad'],
			'content'       => '<h2>Launch is the starting line, not the finish line.</h2><p>Needs change. Bugs happen. A/B tests prove new data. There is always something your website will need as time goes on, and we are here to make sure you don’t have to worry about that now or later. We offer a variety of support options and training to make sure you feel confident making updates or passing them off to us.</p>',
		),
		array(
			'acf_fc_layout' => 'cta_block',
			'heading'       => "Big Site?\nLittle Site?\nWeird Site?",
			'content'       => 'We got you. :)',
			'link'          => $link( 'Start the Process', $contact ),
		),
	)
);

// ---------------------------------------------------------------------------
// ABOUT (626)
// ---------------------------------------------------------------------------

echo "\n=== ABOUT ===\n";
$about_id = 626;
$clear_flex( $about_id );

$set_header(
	$about_id,
	array(
		array(
			'acf_fc_layout' => 'photo_header',
			'eyebrow'       => 'About',
			'main_title'    => 'A better website starts with a better approach',
			'button'        => $link( 'Start the Process', $contact ),
			'image_darken'  => '45',
			'image_windpeak'=> $img['will'],
			'image_spring'  => $img['sam'],
			'image_summer'  => $img['nathan'],
			'image_autumn'  => $img['jeremy'],
			'image_winter'  => $img['chris'],
		),
	)
);

$set_body(
	$about_id,
	array(
		array(
			'acf_fc_layout' => 'text_block',
			'content'       => '<p>Windpeak is a web design and development studio built around the idea that small business owners shouldn’t have to worry about how they represent themselves online.</p><p>We are a small team of creatives who want to get to know you and create a website that you are proud to show off.</p>' . $btn( 'Start the Process', $contact ),
		),
		array(
			'acf_fc_layout' => 'headers_text_block',
			'header'        => 'Agency-Level Expertise - Studio-Sized Team',
			'image'         => $img['chad'],
			'image_side'    => 'right',
			'items'         => array(
				array(
					'item_title' => '',
					'item_text'  => '<p>Windpeak isn’t a giant marketing agency with a dozen departments, and we aren’t trying to be. Our core team is just 3 people: Josh, the owner and developer, and 2 specialized freelancers who are brought in to help you with their specific area of expertise.</p>',
				),
				array(
					'item_title' => 'Specialists where it counts',
					'item_text'  => '<p>We make websites, and we’re good at it. We don’t pretend to be social media experts, business advisors, or video editors. We have the lane that we know, and we know it well.</p>',
				),
				array(
					'item_title' => 'Freelancers under one roof',
					'item_text'  => '<p>Rather than finding one person to build your site, another to make your logo, and one more to write your copy, Windpeak brings each of those experts together so you can get everything you need from one team in one project.</p>',
				),
			),
			'link'          => $link( 'Start the Process', $contact ),
		),
		array(
			'acf_fc_layout' => 'bio_block',
			'content'       => '<h2>Hey 👋</h2><p>I’m Josh, the developer and founder here at Windpeak Design. No sales pitch here; this is just the section where I tell you about me, and you hopefully decide you want to chat, because I\'m the one you’ll talk to throughout the project.</p><p>I\'ve been building websites for something like 10 years, and freelancing on the side for about the same amount of time. I’ve made plenty of fantastic websites, and if I’m being honest, made plenty of bad ones at the start of my career. The first website I ever made was completely static, and you had to edit code to change anything. Not ideal. I’ve come a long way since those days.</p><p>I am married and have a daughter, and we live outside of Knoxville, TN (something something go vols) with 2 dogs and a cat. I was homeschooled (you’ll be able to tell) and also an Eagle Scout (try not to be too impressed, okay?), I have a degree in web technology from my local community college, and I LOVE making websites.</p><p>Thanks for stopping by! Here at Windpeak, all proceeds go to feeding hungry children: mine.</p>',
			'photos'        => array( $img['bio1'], $img['bio2'], $img['bio3'] ),
		),
		array(
			'acf_fc_layout' => 'headers_text_block',
			'header'        => 'Working with Windpeak',
			'image'         => $img['jeremy'],
			'image_side'    => 'left',
			'items'         => array(
				array(
					'item_title' => 'Talk to us',
					'item_text'  => '<p>Your questions and concerns don’t disappear into some ticketing system. If you need something, you just reach out.</p>',
				),
				array(
					'item_title' => 'Hidden fees are Netflix’s thing',
					'item_text'  => '<p>While every client gets a custom quote on their project, we don’t hide behind added fees and upcharges. We define the scope and price up front so you know exactly what you’re getting and how much it’s going to cost.</p>',
				),
				array(
					'item_title' => 'We are here for the long haul',
					'item_text'  => '<p>Our goal isn’t to get your website across the finish line as fast as possible so we can proceed to ignore you. We want to be the first people you call when you have a new idea, solution, or something wild you want to try.</p>',
				),
				array(
					'item_title' => 'We know our limits',
					'item_text'  => '<p>We are really good at the things we are really good at. We don’t pretend to be great at things we aren’t. Rest assured that when you work with us, we don’t oversell what we can do, but we will guide you with the expertise we’ve built over years of work.</p>',
				),
			),
			'link'          => $link( 'Start the Process', $contact ),
		),
		array(
			'acf_fc_layout' => 'cta_block',
			'heading'       => "Big Site?\nLittle Site?\nWeird Site?",
			'content'       => 'We got you. :)',
			'link'          => $link( 'Start the Process', $contact ),
		),
	)
);

// ---------------------------------------------------------------------------
// OUR WORK (628)
// ---------------------------------------------------------------------------

echo "\n=== OUR WORK ===\n";
$work_id = 628;
$clear_flex( $work_id );

$set_header(
	$work_id,
	array(
		array(
			'acf_fc_layout' => 'simple_header',
			'eyebrow'       => 'Our Work',
			'title'         => 'Your business is unique, and your website should be too.',
			'button'        => $link( 'Start the Process', $contact ),
		),
	)
);

$set_body(
	$work_id,
	array(
		array(
			'acf_fc_layout' => 'text_block',
			'content'       => '<p>Some websites need a dozen pages, tons of features, and a lot of ongoing attention.</p><p>Others just need to load fast and represent the business well.</p><p>At Windpeak, we don’t believe that bigger is somehow better. Instead, we look at your business and your needs, and build a plan and a site that supports you.</p>',
		),
		array(
			'acf_fc_layout' => 'case_study_block',
			'screenshot'    => $img['coasters'],
			'eyebrow'       => 'The Big Site',
			'title'         => 'Case Study 1: Coasters & Castles Travel',
			'subtitle'      => 'Travel Agency - 10 Key Pages - Feature Rich - High Monthly Maintenance',
			'intro'         => '<p>Coasters &amp; Castles Travel is a full-service travel agency with a team of collaborative travel advisors. They exist purely as an online presence since there is no brick-and-mortar establishment to represent them. With 100 advisors contracted with them, we knew we had a big task ahead of us.</p>',
			'sections'      => array(
				array(
					'section_title' => 'The Situation',
					'section_text'  => '<p>Lynne and April of Coasters and Castles Travel signed on with us for their EIGHTH website rebuild in the 10 years they have been a business. They were using a Wix site before, and were endlessly frustrated with how difficult it was to edit and maintain, and it wasn’t serving who they were as a business anymore.</p>',
				),
				array(
					'section_title' => 'What We Did',
					'section_text'  => '<p>We started with a full brand strategy, digging into all the nuances about how they speak about themselves. From there, we wrote full copy for the new website as we designed it from the ground up. When we launched, we had ensured the site looked just as good, if not better, on mobile than desktop, and their services sounded clear and high-quality to their audience.</p><p>Lynne and April got the training they needed to feel confident making changes, while also relying on us to handle maintenance, new features, analytics, and more.</p>',
				),
				array(
					'section_title' => 'The Result',
					'section_text'  => '<p>Lynne and April are now able to take a step back and stop worrying about their website for the first time in 10 years, trusting that it\'s handled by a team that cares about their success. We stay in constant communication regarding updates, questions, training, and new features. We handle the website; they handle their business.</p>',
				),
			),
			'work_heading'  => 'The Work Included',
			'work_items'    => array(
				array( 'icon' => $fa( 'fa-solid fa-cubes' ), 'item_label' => 'Custom Flex Theme' ),
				array( 'icon' => $fa( 'fa-solid fa-newspaper' ), 'item_label' => 'Full Blog' ),
				array( 'icon' => $fa( 'fa-solid fa-filter' ), 'item_label' => 'Filterable Advisor List' ),
				array( 'icon' => $fa( 'fa-solid fa-user-lock' ), 'item_label' => 'Custom Advisor Logins' ),
				array( 'icon' => $fa( 'fa-solid fa-screwdriver-wrench' ), 'item_label' => 'Advisor Page Moderation Tools' ),
				array( 'icon' => $fa( 'fa-solid fa-plug' ), 'item_label' => 'VacationCRM API Integration' ),
				array( 'icon' => $fa( 'fa-solid fa-shield-halved' ), 'item_label' => 'Custom Intake Form With Spam and Scam Protections' ),
			),
			'link'          => $link( 'View Live Site', 'https://coastersandcastlestravel.com/' ),
		),
		array(
			'acf_fc_layout' => 'case_study_block',
			'screenshot'    => $img['lfa'],
			'eyebrow'       => 'The Little Site',
			'title'         => 'Case Study 2: Land & Farms Acquisitions',
			'subtitle'      => 'Farmland Acquisition Services - 3 Key Pages - Simple - Low Monthly Maintenance',
			'intro'         => '<p>Land &amp; Farms Acquisitions is a service that helps the local farmland of East TN from being bought up by big corporations who just want to sell it for subdivision development. They believe that farmland that’s been passed down in the family should remain just that: Farmland.</p>',
			'sections'      => array(
				array(
					'section_title' => 'The Situation',
					'section_text'  => '<p>Jamie is a farmer and a businessman, and had been pursuing this business for quite some time before he reached out to us. He didn’t have a website, and he wanted something to establish himself online as a real entity.</p>',
				),
				array(
					'section_title' => 'What We Did',
					'section_text'  => '<p>We looked at the size of his business and what he needed to communicate, and created a plan for a site that didn’t have every single feature jammed into it just because it could.</p><p>The result was a simple website that establishes him as a real business and doesn’t try to be anything more than that.</p>',
				),
				array(
					'section_title' => 'The Result',
					'section_text'  => '<p>Jamie has a website he can send people that explains more about who he is and what he does. He has an established web presence that is small enough to complement his small business.</p>',
				),
			),
			'work_heading'  => 'The Work Included',
			'work_items'    => array(
				array( 'icon' => $fa( 'fa-solid fa-cubes' ), 'item_label' => 'Custom Flex Theme' ),
				array( 'icon' => $fa( 'fa-solid fa-file' ), 'item_label' => '3 Simple Pages' ),
				array( 'icon' => $fa( 'fa-solid fa-shield-halved' ), 'item_label' => 'Contact Form with spam protection' ),
			),
			'link'          => $link( 'View Live Site', 'https://landandfarmstn.com/' ),
		),
		array(
			'acf_fc_layout' => 'list_block',
			'intro'         => '<h2>The Right Amount of Website</h2><p>Templates and other pre-packaged web services often give you too much of what you don’t need and not enough of what you do. We don’t bother with that.</p>',
			'list_items'    => array(
				array( 'icon' => $fa( 'fa-solid fa-layer-group' ), 'list_item_title' => 'Complex when it needs to be' ),
				array( 'icon' => $fa( 'fa-solid fa-leaf' ), 'list_item_title' => 'Simple when it should be' ),
				array( 'icon' => $fa( 'fa-solid fa-bullseye' ), 'list_item_title' => 'Designed for YOUR business, not A business' ),
				array( 'icon' => $fa( 'fa-solid fa-ban' ), 'list_item_title' => 'No features just to have features' ),
			),
			'link'          => array(),
		),
		array(
			'acf_fc_layout' => 'cta_block',
			'heading'       => "Big Site?\nLittle Site?\nWeird Site?",
			'content'       => 'We got you. :)',
			'link'          => $link( 'Start the Process', $contact ),
		),
	)
);

// ---------------------------------------------------------------------------
// CONTACT (69)
// ---------------------------------------------------------------------------

echo "\n=== CONTACT ===\n";
$contact_id = 69;
$clear_flex( $contact_id );

$set_header(
	$contact_id,
	array(
		array(
			'acf_fc_layout'  => 'photo_header',
			'eyebrow'        => 'Contact',
			'main_title'     => 'Let’s talk about you',
			'button'         => $link( 'Start the Process', $contact ),
			'image_darken'   => '45',
			'image_windpeak' => $img['nathan'],
			'image_spring'   => $img['will'],
			'image_summer'   => $img['sam'],
			'image_autumn'   => $img['jeremy'],
			'image_winter'   => $img['chris'],
		),
	)
);

$set_body(
	$contact_id,
	array(
		array(
			'acf_fc_layout' => 'form_block',
			'intro'         => '<h2>Start the Process</h2>',
			'form_id'       => (string) ( $forms['contact'] ?: 1 ),
		),
		array(
			'acf_fc_layout' => 'text_block',
			'content'       => '<h2>Prefer to email us directly?</h2><p>Drop us a line:</p><p><a href="mailto:hello@windpeakdesign.com">hello@windpeakdesign.com</a></p>',
		),
	)
);

// ---------------------------------------------------------------------------
// SUBMISSION SUCCESSFUL (452) — switch to flex page to match mockup
// ---------------------------------------------------------------------------

echo "\n=== SUBMISSION SUCCESSFUL ===\n";
$success_id = 452;
update_post_meta( $success_id, '_wp_page_template', 'default' );
$clear_flex( $success_id );

$set_header(
	$success_id,
	array(
		array(
			'acf_fc_layout'  => 'photo_header',
			'eyebrow'        => 'You did it!',
			'main_title'     => 'You’re one step closer to getting your time back.',
			'button'         => '',
			'image_darken'   => '45',
			'image_windpeak' => $img['jeremy'],
			'image_spring'   => $img['will'],
			'image_summer'   => $img['sam'],
			'image_autumn'   => $img['nathan'],
			'image_winter'   => $img['chris'],
		),
	)
);

$set_body(
	$success_id,
	array(
		array(
			'acf_fc_layout' => 'headers_text_block',
			'header'        => 'So.. now what?',
			'image'         => $img['chad'],
			'image_side'    => 'right',
			'items'         => array(
				array(
					'item_title' => 'We’ll reach out',
					'item_text'  => '<p>Assuming you aren’t a robot, Josh will send you an email to get a meeting set up.</p>',
				),
				array(
					'item_title' => 'We’ll make a plan',
					'item_text'  => '<p>After the meeting, we will send you a full proposal with all the scope, cost, and timeline.</p>',
				),
				array(
					'item_title' => 'Sign the contract',
					'item_text'  => '<p>Once you sign the contract, we begin work on your site!</p>',
				),
			),
			'link'          => array(),
		),
	)
);

// ---------------------------------------------------------------------------
// ADVISOR MICROS (627)
// ---------------------------------------------------------------------------

echo "\n=== ADVISOR MICROS ===\n";
$advisor_page = 627;
$clear_flex( $advisor_page );
$advisor_anchor = home_url( '/advisor-micros/#advisor-intake' );

$set_header(
	$advisor_page,
	array(
		array(
			'acf_fc_layout'  => 'photo_header',
			'eyebrow'        => 'Mini Site. Massive Impact.',
			'main_title'     => 'Travel Advisor Micros',
			'button'         => $link( 'Let’s Get Started!', $advisor_anchor ),
			'image_darken'   => '45',
			'image_windpeak' => $img['chris'],
			'image_spring'   => $img['will'],
			'image_summer'   => $img['sam'],
			'image_autumn'   => $img['nathan'],
			'image_winter'   => $img['jeremy'],
		),
	)
);

$set_body(
	$advisor_page,
	array(
		array(
			'acf_fc_layout' => 'text_block',
			'content'       => '<h2>Imagine a website you don’t have to worry about.</h2><p>Advisor Micros are 4-page “Microsites” that pack the punch of a bigger custom site, packaged up into a service that Travel Advisors can uniquely take advantage of.</p><p>Advisor Micros are for advisors who want to point clients to something they own instead of an agency website. Imagine having total control over the fonts, colors, images, and language on your site.* From a custom domain to a form that you can build yourself to ask exactly what you want, Advisor Micros let you regain ultimate control over how you present yourself online.</p><p>And the best part of all of this? You don’t have to waste time learning how to build it. The time that you save arguing with “Themeforest Template #9765” (that looked WAY better in the photo) is now time you can spend getting leads. You get a professional-grade website without having to take on a second job.</p><p><em>*Note that some agencies have limitations on these things. Windpeak offers them, but your agency may have separate requirements you must adhere to.</em></p>',
		),
		array(
			'acf_fc_layout' => 'headers_text_block',
			'header'        => 'What do I get?',
			'image'         => $img['sam'],
			'image_side'    => 'right',
			'items'         => array(
				array(
					'item_title' => 'Custom Domain',
					'item_text'  => '<p>All microsites are given a domain name (up to $30 value) that represents who they are as a brand. A domain name is the web address people type in to find your site, like yourname.com. We host your domain for you, but it belongs to you should you ever want to move it somewhere else.</p>',
				),
				array(
					'item_title' => 'Three Main Pages',
					'item_text'  => '<p>A homepage to explain who you are, a services page to explain what you do, and a call to action page to land the lead!</p>',
				),
				array(
					'item_title' => 'And A “Link In Bio” Style Page',
					'item_text'  => '<p>Everyone has a link in their bio these days, and for good reason. With a microsite, you can stop paying a premium for a “Link in bio” style service and instead direct people to a page that does the same thing, except it\'s already on your site, and you have total control over it.</p>',
				),
				array(
					'item_title' => 'Easy Updates',
					'item_text'  => '<p>Advisor Micros are built like LEGOs, snapping blocks together as you want. This means that you can easily update your messaging, photos, add and delete sections, and more without the risk of breaking your site. The underlying system just works, and you can get back to doing what you do best: selling travel.</p>',
				),
				array(
					'item_title' => 'Ongoing Support',
					'item_text'  => '<p>Hosting, security, daily backups, updates, routine maintenance, all included in one monthly cost. Higher tier plans can even gain access to tokens, which allow for more customized features, additional pages, etc. See our <a href="' . esc_url( $tokens ) . '">hosting information</a> for more information.</p>',
				),
			),
			'link'          => $link( 'Let’s Get Started!', $advisor_anchor ),
		),
		array(
			'acf_fc_layout' => 'lead_capture_block',
			'intro'         => '<h2>Can I see one of these?</h2>',
			'screenshot'    => $img['app_wide'],
			'content'       => '<p>Check out a demo site here:</p>',
			'link'          => $link( 'See The Demo', '#' ),
		),
		array(
			'acf_fc_layout' => 'selection_block',
			'intro'         => '<h2>You can make it yours</h2><p>Not every advisor needs the same things, but we have all the common needs covered. We offer a variety of add-ons and extras to make your site exactly what you need it to be.</p>',
			'services'      => array(
				array(
					'icon'           => $fa( 'fa-solid fa-house' ),
					'label'          => 'Base Site: $900',
					'detail_content' => '<h3>Base Site: $900</h3><ul><li>Custom Domain (Up to $30)</li><li>3 pages + Link-in-bio page</li><li>Starter copy + “How to sound like me” helper pdf</li></ul>',
				),
				array(
					'icon'           => $fa( 'fa-solid fa-plug' ),
					'label'          => 'VacationCRM Integration: $250',
					'detail_content' => '<h3>VacationCRM Integration: $250</h3><p>With a VCRM integration, you can create a custom form that asks any questions you’d like, all while still sending the lead over to your VCRM, this time with all the information you need baked in from the start.</p>',
				),
				array(
					'icon'           => $fa( 'fa-solid fa-pen-nib' ),
					'label'          => 'White Glove Copy: $400',
					'detail_content' => '<h3>White Glove Copy: $400</h3><p>This service includes a meeting with our copywriter, full website copy, and a round of revisions.</p><p>You still have the freedom to edit your own copy as you see fit, but if you want a professional to get you 95% of the way there while you research hotel prices for a client, this is the service for you.</p>',
				),
				array(
					'icon'           => $fa( 'fa-solid fa-palette' ),
					'label'          => 'Graphic Design Package: $400',
					'detail_content' => '<h3>Graphic Design Package: $400</h3><p>This service includes a phone call with our graphic designer, a logo, full color suite, typography choices and a few pre-made graphics to use on socials and elsewhere. It allows one round of revisions.</p>',
				),
				array(
					'icon'           => $fa( 'fa-solid fa-wand-magic-sparkles' ),
					'label'          => 'Custom Work? Custom Pricing',
					'detail_content' => '<h3>Custom Work? Custom Pricing</h3><p>Trying to really upgrade your business by adding extra web features? A full branding workshop so you can speak confidently about your business? Extra graphic assets like email headers, social banners, profile pictures, etc? We can support all of these and more, just ask!</p>',
				),
			),
		),
		array(
			'acf_fc_layout'           => 'dynamic_pricing_block',
			'intro'                   => '<h2>You choose how you pay</h2><p>Travel Advisor Micros can be paid in two different ways.</p>',
			'payment_options'         => array(
				array(
					'option_title' => 'Pay Upfront',
					'option_text'  => '<p>Pay for your website and all its add ons immediately, then you only pay <a href="' . esc_url( $tokens ) . '">hosting fees</a> month to month, and that can be canceled anytime. No long term commitment required.</p>',
				),
				array(
					'option_title' => 'Spread it out',
					'option_text'  => '<p>If you prefer, you can pay $0 upfront and split your website and add ons cost across your first 12 months of service. There is no interest, it doesn’t cost anything more, its just an option for your convenience.</p><p>Your monthly total will include both your website and add-ons costs as well as your <a href="' . esc_url( $tokens ) . '">monthly hosting fees</a> during those 12 months. After month 12, the website cost disappears, and you continue paying only for ongoing service.</p><p>This option carries a 12-month commitment.</p>',
				),
			),
			'calculator_title'        => 'Pricing Calculator',
			'base_site'               => 900,
			'monthly_service'         => 69,
			'term_months'             => 12,
			'addon_vacation_crm'      => 250,
			'addon_white_glove_copy'  => 400,
			'addon_graphic_design'    => 400,
		),
		array(
			'acf_fc_layout'   => 'accordion_block',
			'image'           => $img['will'],
			'intro'           => '<h2>Still Have Questions?</h2>',
			'accordion_items' => array(
				array(
					'accordion_title'   => 'How involved do I have to be?',
					'accordion_content' => '<p>Well, the core idea is that you do as little as possible and you get a website, but we can’t sound like you if we don’t know you.</p><p>The most basic version of the site will require a form where you can upload who you are and what you do, but graphic design work or white glove copy will require a call where you speak to our designer or copywriter.</p><p>The site ships with pre-written “standard” copy that you are free to use, as well as a pdf to help you re-write sections to sound like you. How much time you want to spend editing is up to you, but we can promise you this: You won’t be fussing over design, just the wording you choose to use.</p>',
				),
				array(
					'accordion_title'   => 'When do I get my site?',
					'accordion_content' => '<p>Timeline varies depending on the level of service you selected, but we shoot for 2 weeks for the basic service.</p><p>White glove copy, graphic design work, etc., can all add delays. We do our absolute best to keep the entire product delivery under a month.</p><p>Note that this is a target, not a guarantee.</p>',
				),
				array(
					'accordion_title'   => 'Can I make changes or add pages myself?',
					'accordion_content' => '<p>Yes! You have access to pre-defined sections called “Blocks” that you can snap together like LEGO pieces as you want or need to change things.</p><p>You are free to edit, add, and change those sections as you see fit, and you can create your own pages.</p><p>If you would like us to build or write extra pages, or just help you out, there would be costs associated with that.</p>',
				),
				array(
					'accordion_title'   => 'What if my agency has its own branding or website requirements?',
					'accordion_content' => '<p>Just let us know what those are, and we will happily comply as best we can.</p><p>This could include colors, fonts, logos, legal items, etc. As long as we know what they are before we build your site, we do our best to accommodate anything reasonable.</p><p>If you change agencies later, there are no issues from our side. There may be a cost associated with branding updates or new requirements, such as swapping out agency colors, but otherwise your site can continue right along with you.</p>',
				),
				array(
					'accordion_title'   => 'What if I use a CRM other than VacationCRM?',
					'accordion_content' => '<p>Reach out to us via the form and ask.</p><p>We currently only support VCRM, but if you have a CRM that is popular and supports API integration, we might consider doing the development work to add it.</p><p>We would just need the name of the CRM, and the final call is made by our developer.</p>',
				),
				array(
					'accordion_title'   => 'Can I use a domain I already own? Who owns my domain?',
					'accordion_content' => '<p>You do!</p><p>If you already own a domain, you are more than welcome to use it. We will either need access, or if you are comfortable making DNS changes yourself, you’ll need to do that.</p><p>If you don’t already have one, we purchase and manage the domain for you to make things easy. DNS networking can be really tricky: updating records, pointing your site to the correct IP address, adding domain verifications, etc.</p><p>Either way, the domain belongs to you, and you are free to request a transfer.</p>',
				),
				array(
					'accordion_title'   => 'How does the 12-month payment option work if I cancel?',
					'accordion_content' => '<p>After the 12-month payment period, you move to the same situation as someone who paid up front: you pay hosting costs month to month.</p><p>If you cancel while you are still inside the 12-month term, you would still owe the remainder of the site + add-ons.</p><p>That remainder can be paid immediately or continue month to month. Your hosting billing would stop when the site is no longer hosted with Windpeak.</p><p>If you ultimately decide you no longer want to do travel work, the same terms apply.</p>',
				),
				array(
					'accordion_title'   => 'What does the monthly hosting cost include?',
					'accordion_content' => '<p>All hosting details can be found here:</p><p><a href="' . esc_url( $tokens ) . '">See Hosting &amp; Website Support</a></p>',
				),
				array(
					'accordion_title'   => 'Can I upgrade to a fully custom Windpeak website later?',
					'accordion_content' => '<p>Yes! Reach out for specifics on what that could look like.</p>',
				),
			),
		),
		array(
			'acf_fc_layout' => 'form_block',
			'intro'         => '<h2 id="advisor-intake">Ready to upgrade your Travel Advisor business?</h2>',
			'form_id'       => (string) ( $forms['advisor'] ?: 1 ),
		),
		array(
			'acf_fc_layout' => 'cta_block',
			'heading'       => 'Got more website than a microsite can handle?',
			'content'       => 'We love that! We are willing and able to take on custom requests outside the scope of these microsites. Hop over to our standard contact page and tell us what you’re dreaming up.',
			'link'          => $link( 'Start the Process', $contact ),
		),
	)
);

// ---------------------------------------------------------------------------
// TOKENS (630)
// ---------------------------------------------------------------------------

echo "\n=== TOKENS ===\n";
$tokens_id = 630;
$clear_flex( $tokens_id );

$set_header(
	$tokens_id,
	array(
		array(
			'acf_fc_layout'  => 'photo_header',
			'eyebrow'        => 'Hosting & Website Support',
			'main_title'     => 'Flexible support for the work your website needs.',
			'button'         => '',
			'image_darken'   => '45',
			'image_windpeak' => $img['chad'],
			'image_spring'   => $img['will'],
			'image_summer'   => $img['sam'],
			'image_autumn'   => $img['nathan'],
			'image_winter'   => $img['jeremy'],
		),
	)
);

$set_body(
	$tokens_id,
	array(
		array(
			'acf_fc_layout' => 'text_block',
			'content'       => '<h2>Hosting keeps your site healthy. Tokens change your site.</h2><p>After launch, Windpeak continues to host and maintain your website so you don’t have to think about the technical upkeep. Every plan covers the core essentials that keep the site running.</p><p>When you want us to make a change — update content, adjust a section, build something new, or help troubleshoot something outside routine maintenance — we use tokens instead of hourly retainers. You’ll know the token cost before work begins, so there is no clock to watch and no surprise invoice for a few extra minutes.</p>',
		),
		array(
			'acf_fc_layout'  => 'pricing_block',
			'intro'          => '<h2>Choose the Support Level That Fits</h2><p>Every plan includes the same core hosting and maintenance. The difference is how much hands-on help you want from Windpeak each month.</p>',
			'includes_label' => 'Every Plan Includes',
			'includes'       => array(
				array( 'include_label' => 'Managed WordPress Hosting' ),
				array( 'include_label' => 'SSL' ),
				array( 'include_label' => 'Daily Backups' ),
				array( 'include_label' => 'WordPress Updates' ),
				array( 'include_label' => 'Routine Maintenance' ),
			),
			'plans'          => array(
				array(
					'price'          => '$69',
					'price_suffix'   => '/mo',
					'plan_title'     => 'Hosting & Care',
					'tokens_line'    => '0 Tokens / month',
					'best_for_label' => 'Best for',
					'best_for'       => 'Mostly hands-off sites',
				),
				array(
					'price'          => '$129',
					'price_suffix'   => '/mo',
					'plan_title'     => 'Care + Edits',
					'tokens_line'    => '3 Tokens / month',
					'best_for_label' => 'Best for',
					'best_for'       => 'Regular small updates',
				),
				array(
					'price'          => '$229',
					'price_suffix'   => '/mo',
					'plan_title'     => 'Ongoing Support',
					'tokens_line'    => '8 Tokens / month',
					'best_for_label' => 'Best for',
					'best_for'       => 'Active, frequently changing sites',
				),
			),
			'footer'         => '<p>Need a little extra help? Additional tokens are available in packs of 5 for $175. If you’re buying token packs regularly, upgrading your plan will usually save you money.</p>',
		),
		array(
			'acf_fc_layout' => 'text_block',
			'content'       => '<h2>How Tokens Work</h2><h3>Scope, Not Hours</h3><p>Tokens are based on the scope and complexity of a request, not the number of minutes it takes to complete. Before work begins, we will let you know how many tokens the request is expected to use.</p><h3>1 Token</h3><p>A small, clearly defined update. This might be provided text, an image swap, a button or link change, updated contact information, or another minor adjustment. Small related edits may be grouped together when they can reasonably be handled as one request.</p><h3>2 Tokens</h3><p>A more involved change to something that already exists — for example, a larger batch of related edits, a layout adjustment, or a change that needs additional setup or testing.</p><h3>3–4 Tokens</h3><p>A standard design or development request, such as a new custom section, a new page built from the existing site system, a substantial change to an existing component, or an update to existing functionality.</p><h3>5 Tokens</h3><p>Significant but contained work. If a request gets larger, more custom, or more uncertain than this, we will stop forcing it into the token system and provide a normal project quote instead.</p>',
		),
		array(
			'acf_fc_layout' => 'text_block',
			'content'       => '<h2>Which Plan Is Right for Me?</h2><p>All plans include the same core managed hosting and maintenance. The right fit mostly depends on how often you would like Windpeak to make changes for you.</p><p>Not sure? Start with Hosting &amp; Care. You can always purchase a token pack when something comes up. If buying additional tokens becomes a regular thing, that is usually a good sign that moving to a plan with included monthly support will save you money.</p>',
		),
		array(
			'acf_fc_layout' => 'headers_text_block',
			'header'        => 'Select a Plan',
			'image'         => $img['nathan'],
			'image_side'    => 'right',
			'items'         => array(
				array(
					'item_title' => 'Hosting & Care',
					'item_text'  => '<p>Best for websites that mostly stay put. If you are comfortable making occasional content updates yourself and only need Windpeak once in a while, start here. Your site still receives the same core hosting and maintenance, and you can purchase a token pack whenever something comes up.</p>',
				),
				array(
					'item_title' => 'Care + Edits',
					'item_text'  => '<p>A good fit if you expect regular smaller updates throughout the year — new blog posts, videos, seasonal content, advisor or team updates, image changes, provided promotions, or occasional site improvements you would rather have us handle.</p>',
				),
				array(
					'item_title' => 'Ongoing Support',
					'item_text'  => '<p>Built for websites that change frequently and function as an active marketing tool. Think recurring campaigns, announcements, promotions, landing pages, advisor or team changes, form updates, supplier content, recurring publishing, or ongoing improvements.</p>',
				),
			),
			'link'          => $link( 'Start the Process', $contact ),
		),
		array(
			'acf_fc_layout'   => 'accordion_block',
			'image'           => $img['sam'],
			'intro'           => '<h2>Questions About Hosting &amp; Tokens</h2><p>The basics are simple. The details are here when you need them.</p>',
			'accordion_items' => array(
				array(
					'accordion_title'   => 'What are some examples of a 1-token request?',
					'accordion_content' => '<p>A 1-token request is a small, clearly defined change. Common examples include updating provided text, swapping an image, changing a button or link, updating contact information, adding a small amount of provided content, or making a minor adjustment to an existing section.</p><p>When several tiny edits are closely related and can reasonably be handled together, they may be grouped into one request rather than charged individually.</p>',
				),
				array(
					'accordion_title'   => 'What does a 2-token request look like?',
					'accordion_content' => '<p>Two tokens usually cover a more involved update to something that already exists. Examples include a larger batch of related edits, modifying the layout of an existing section, updating shared site elements, or completing a change that requires more setup, responsive work, or testing than a simple edit.</p>',
				),
				array(
					'accordion_title'   => 'What usually costs 3–4 tokens?',
					'accordion_content' => '<p>Most standard design and development requests fall into this range. That may include designing and building a new custom website section, creating a new page from the site’s existing flexible-content system, substantially changing an existing component, or extending an existing form or feature.</p><p>The exact cost depends on scope and complexity. You’ll always receive the token estimate before work begins.</p>',
				),
				array(
					'accordion_title'   => 'What if my request is larger than 5 tokens?',
					'accordion_content' => '<p>At that point, we stop being cute with the tokens.</p><p>Larger or highly custom work is normally scoped and quoted as a project in dollars instead. This may include custom plugins, major new features, complex third-party or CRM integrations, large data migrations, substantial redesign work, or other development that no longer fits neatly into a small support request.</p>',
				),
				array(
					'accordion_title'   => 'Do unused monthly tokens roll over or expire?',
					'accordion_content' => '<p>Yes. Included monthly tokens may roll over for one additional month. If those rollover tokens are still unused after that month, they expire.</p><p>When multiple types of tokens are available, they are used in this order:</p><ol><li>rollover tokens,</li><li>the current month’s included tokens,</li><li>purchased tokens.</li></ol><p>Purchased tokens do not expire while your website remains actively hosted with Windpeak.</p>',
				),
				array(
					'accordion_title'   => 'Can I borrow tokens from next month?',
					'accordion_content' => '<p>Yes. Clients on a plan with included monthly tokens may borrow up to one upcoming month’s token allowance from the immediately following month.</p><p>Borrowed tokens are deducted from the next month’s allocation when it is issued.</p><p>For example, if your plan includes 3 tokens per month and you borrow 1 token near the end of August, September begins with 2 available tokens rather than 3.</p><p>Borrowing cannot extend multiple months into the future.</p>',
				),
				array(
					'accordion_title'   => 'Can I buy additional tokens?',
					'accordion_content' => '<p>Yes. Additional tokens are available in one simple pack:</p><p><strong>5 Tokens — $175</strong></p><p>Purchased tokens do not expire while your website remains actively hosted with Windpeak.</p><p>If you’re purchasing additional token packs regularly, moving to a plan with more included monthly tokens will usually be a better value.</p>',
				),
				array(
					'accordion_title'   => 'Can I upgrade or downgrade my plan?',
					'accordion_content' => '<p>Yes. Your support needs may change over time, and you aren’t expected to stay on the same plan forever.</p><p>Upgrades are intended for an ongoing increase in support needs rather than a temporary way to access discounted tokens for one unusually busy month. After upgrading, clients should generally expect to remain on the new plan for at least three billing cycles before downgrading.</p><p>If you only need extra help for a particularly busy month, purchasing a 5-token pack will usually make more sense.</p><p>Downgrades take effect on a future billing cycle once the minimum period for the current plan has been met.</p>',
				),
				array(
					'accordion_title'   => 'How quickly are token requests completed?',
					'accordion_content' => '<p>Standard token requests are handled through Windpeak’s normal support queue. Most routine requests are generally targeted for completion within approximately five business days after the scope and token cost are approved.</p><p>Larger or more involved requests may take longer. If so, we’ll communicate the expected timeline before work begins.</p>',
				),
				array(
					'accordion_title'   => 'Can I rush a request?',
					'accordion_content' => '<p>When availability allows, an agreed accelerated deadline — such as next-business-day completion — generally costs the request’s normal token amount plus 1 additional token for priority scheduling.</p><p>For example, a normal 2-token request would become 3 tokens with priority turnaround.</p><p>Priority service must be approved before work begins and is always subject to availability.</p><p>Same-day, after-hours, weekend, holiday, or other drop-everything requests are not automatically available. When they can be accommodated, rush pricing may require additional tokens, a separate rush fee, or a custom quote depending on the request and timing.</p>',
				),
				array(
					'accordion_title'   => "What's included with hosting, and what uses tokens?",
					'accordion_content' => '<p>Hosting covers the managed environment required to keep your WordPress site online and healthy, including the hosting platform, SSL, backups, routine WordPress updates, and normal maintenance.</p><p>Tokens are used when you ask Windpeak to perform hands-on work outside that routine maintenance — such as editing content, changing layouts, adding new sections or functionality, troubleshooting a client-caused issue, or helping with another defined support request.</p><p>A simple rule of thumb:</p><p><strong>Hosting keeps the site healthy. Tokens change the site.</strong></p>',
				),
				array(
					'accordion_title'   => 'What happens if something breaks?',
					'accordion_content' => '<p>Genuine launch-related defects are covered during the site’s post-launch support period.</p><p>After that period, normal WordPress or hosting-platform issues within the managed environment are handled as part of hosting and maintenance.</p><p>If a clear defect in Windpeak’s original work is discovered later, we may correct it without using tokens. Problems caused by client changes, third-party changes, new requirements, or work outside normal managed maintenance may require tokens or a separate quote depending on the situation.</p>',
				),
				array(
					'accordion_title'   => 'Can I use tokens for training or a support call?',
					'accordion_content' => '<p>Yes. Tokens may be used for focused website training or support sessions.</p><p>Training is scoped by topic rather than billed by the minute. Before scheduling, we’ll let you know the expected token cost based on what you want to cover.</p><p>If a session expands substantially beyond the agreed topic, additional work may require another token or a separate follow-up.</p>',
				),
				array(
					'accordion_title'   => 'Can I use stored tokens toward a separately quoted project?',
					'accordion_content' => '<p>Tokens are units of website support, not cash or gift-card credit, so they do not automatically reduce the price of separately quoted project work dollar-for-dollar.</p><p>When it makes sense, Windpeak may allow existing tokens to cover an eligible portion of a larger project. If so, that will be clearly included in the project quote before work begins.</p>',
				),
			),
		),
	)
);

echo "\nDone.\n";
echo "Contact form: {$forms['contact']}\n";
echo "Advisor form: {$forms['advisor']}\n";
