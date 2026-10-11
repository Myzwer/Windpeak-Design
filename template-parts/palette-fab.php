<?php
/**
 * Palette FAB
 *
 * Fixed bottom-right control. The corner button shows the active choice.
 * Opening the menu lists the others in a fixed order and skips the active one.
 */

$logo = function_exists( 'get_field' ) ? get_field( 'site_nav_logo', 'option' ) : null;

if ( ! is_array( $logo ) || empty( $logo['url'] ) ) {
	return;
}

$choices = [
	'windpeak' => [
		'label' => __( 'Windpeak', 'prelaunch-wp' ),
		'icon'  => '',
	],
	'winter'   => [
		'label' => __( 'Winter', 'prelaunch-wp' ),
		'icon'  => 'fa-solid fa-snowflake',
	],
	'spring'   => [
		'label' => __( 'Spring', 'prelaunch-wp' ),
		'icon'  => 'fa-solid fa-flower',
	],
	'summer'   => [
		'label' => __( 'Summer', 'prelaunch-wp' ),
		'icon'  => 'fa-solid fa-sun',
	],
	'autumn'   => [
		'label' => __( 'Fall', 'prelaunch-wp' ),
		'icon'  => 'fa-solid fa-leaf',
	],
];
?>

<div class="palette-fab-stack" data-palette-fab data-active="windpeak" data-menu="windpeak">
	<div class="palette-fab-seasons" id="palette-fab-seasons" data-palette-fab-seasons hidden>
		<?php foreach ( $choices as $slug => $choice ) : ?>
			<button
				type="button"
				class="palette-fab-season<?php echo $slug === 'windpeak' ? ' palette-fab-season--mark' : ''; ?>"
				data-palette="<?php echo esc_attr( $slug ); ?>"
				aria-label="<?php echo esc_attr( $choice['label'] ); ?>"
			>
				<?php if ( $slug === 'windpeak' ) : ?>
					<img
						class="palette-fab-mark"
						src="<?php echo esc_url( $logo['url'] ); ?>"
						alt=""
						<?php echo prelaunch_image_dimensions_attr( $logo ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>
						decoding="async"
					/>
				<?php else : ?>
					<i class="<?php echo esc_attr( $choice['icon'] ); ?>" aria-hidden="true"></i>
				<?php endif; ?>
			</button>
		<?php endforeach; ?>
	</div>

	<button
		type="button"
		class="palette-fab"
		data-palette-fab-toggle
		aria-expanded="false"
		aria-controls="palette-fab-seasons"
		aria-label="<?php echo esc_attr( $choices['windpeak']['label'] ); ?>"
	>
		<img
			class="palette-fab-mark"
			data-palette-icon="windpeak"
			src="<?php echo esc_url( $logo['url'] ); ?>"
			alt=""
			<?php echo prelaunch_image_dimensions_attr( $logo ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>
			decoding="async"
		/>
		<?php foreach ( $choices as $slug => $choice ) : ?>
			<?php if ( $slug === 'windpeak' ) : ?>
				<?php continue; ?>
			<?php endif; ?>
			<i
				class="<?php echo esc_attr( $choice['icon'] ); ?>"
				data-palette-icon="<?php echo esc_attr( $slug ); ?>"
				aria-hidden="true"
			></i>
		<?php endforeach; ?>
	</button>
</div>
<?php
$notices = [
	'spring' => 'Spring has sprung!',
	'summer' => 'Step into Summer!',
	'autumn' => "Its Fall ya'll!",
	'winter' => "It's a winter wonderland!",
];

if ( function_exists( 'get_field' ) ) {
	foreach ( $notices as $slug => $fallback ) {
		$saved = get_field( 'seasonal_notice_' . $slug, 'option' );

		if ( is_string( $saved ) && '' !== trim( $saved ) ) {
			$notices[ $slug ] = $saved;
		}
	}
}
?>
<div
	class="palette-notice"
	data-palette-notice
	hidden
	role="status"
	aria-live="polite"
	<?php foreach ( $notices as $slug => $text ) : ?>
		data-notice-<?php echo esc_attr( $slug ); ?>="<?php echo esc_attr( $text ); ?>"
	<?php endforeach; ?>
>
	<div class="palette-notice-card">
		<i class="fa-solid fa-flower" data-palette-notice-icon aria-hidden="true"></i>
		<p class="palette-notice-text" data-palette-notice-text></p>
	</div>
</div>
<script>
	(function () {
		var active;

		try {
			active = sessionStorage.getItem("prelaunch-palette");
		} catch (error) {
			return;
		}

		if (!active) {
			return;
		}

		var stack = document.querySelector("[data-palette-fab]");

		if (!stack || !stack.hasAttribute("data-palette-fab")) {
			return;
		}

		var option = stack.querySelector('[data-palette="' + active.replace(/[^a-z]/g, "") + '"]');

		if (!option) {
			return;
		}

		stack.setAttribute("data-active", option.getAttribute("data-palette"));
		stack.setAttribute("data-menu", option.getAttribute("data-palette"));

		var toggle = stack.querySelector("[data-palette-fab-toggle]");
		var label = option.getAttribute("aria-label");

		if (toggle && label) {
			toggle.setAttribute("aria-label", label);
		}
	})();
</script>
