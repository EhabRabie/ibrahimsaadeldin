<?php
/**
 * Video card. Opens a lightbox when JS is available, otherwise the video page.
 *
 * @package SaadEldinLibrary
 */

defined( 'ABSPATH' ) || exit;

$youtube_id = sdl_video_id();
?>
<article id="post-<?php the_ID(); ?>" class="sdl-card sdl-card--video">
	<a class="sdl-card__media" href="<?php the_permalink(); ?>"<?php echo $youtube_id ? ' data-youtube="' . esc_attr( $youtube_id ) . '" data-title="' . esc_attr( get_the_title() ) . '"' : ''; ?> aria-label="<?php echo esc_attr( 'تشغيل: ' . get_the_title() ); ?>">
		<?php
		$thumb = sdl_video_thumbnail();
		echo $thumb ? wp_kses_post( $thumb ) : '<span class="sdl-card__placeholder" aria-hidden="true"></span>';
		?>
		<span class="sdl-play" aria-hidden="true"></span>
	</a>
	<div class="sdl-card__body">
		<div class="sdl-card__meta">
			<?php echo sdl_primary_term_link(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
			<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
		</div>
		<h2 class="sdl-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p class="sdl-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22, '…' ) ); ?></p>
	</div>
</article>
