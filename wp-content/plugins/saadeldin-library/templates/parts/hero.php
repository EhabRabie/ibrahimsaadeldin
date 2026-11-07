<?php
/**
 * Hero banner with optional search form.
 *
 * @package SaadEldinLibrary
 *
 * @var array $args { eyebrow, title, subtitle, search: array|null }
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="sdl-hero">
	<div class="sdl-container">
		<?php if ( ! empty( $args['eyebrow'] ) ) : ?>
			<p class="sdl-hero__eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></p>
		<?php endif; ?>
		<h1 class="sdl-hero__title"><?php echo esc_html( $args['title'] ); ?></h1>
		<?php if ( ! empty( $args['subtitle'] ) ) : ?>
			<p class="sdl-hero__sub"><?php echo esc_html( $args['subtitle'] ); ?></p>
		<?php endif; ?>
		<?php
		if ( ! empty( $args['search'] ) ) {
			sdl_part( 'search-form', $args['search'] );
		}
		?>
	</div>
</section>
