<?php
/**
 * Article card (grid) or featured card.
 *
 * @package SaadEldinLibrary
 *
 * @var array $args { featured: bool }
 */

defined( 'ABSPATH' ) || exit;

$featured = ! empty( $args['featured'] );
$words    = $featured ? 45 : 24;
?>
<article id="post-<?php the_ID(); ?>" class="sdl-card<?php echo $featured ? ' sdl-card--featured' : ''; ?>">
	<a class="sdl-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( $featured ? 'large' : 'medium_large', array( 'loading' => $featured ? 'eager' : 'lazy', 'decoding' => 'async' ) );
		} else {
			echo '<span class="sdl-card__placeholder" aria-hidden="true"></span>';
		}
		?>
	</a>
	<div class="sdl-card__body">
		<div class="sdl-card__meta">
			<?php echo sdl_primary_term_link(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
			<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
		</div>
		<h2 class="sdl-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p class="sdl-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), $words, '…' ) ); ?></p>
		<a class="sdl-more" href="<?php the_permalink(); ?>">اقرأ المقال</a>
	</div>
</article>
