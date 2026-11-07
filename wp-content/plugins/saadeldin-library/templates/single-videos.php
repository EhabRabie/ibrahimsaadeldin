<?php
/**
 * Single video: the YouTube URL becomes a responsive nocookie iframe.
 *
 * @package SaadEldinLibrary
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="primary" class="sdl-page">
	<?php
	while ( have_posts() ) :
		the_post();
		$youtube_id = sdl_video_id();
		?>
		<div class="sdl-container sdl-single-video">
			<nav class="sdl-breadcrumb" aria-label="مسار التنقل">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">الرئيسية</a>
				<span aria-hidden="true">/</span>
				<a href="<?php echo esc_url( get_post_type_archive_link( SDL_Videos::POST_TYPE ) ); ?>">الفيديوهات</a>
			</nav>

			<article id="post-<?php the_ID(); ?>" class="sdl-video">
				<header class="sdl-article__head">
					<div class="sdl-card__meta">
						<?php echo sdl_primary_term_link(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
					</div>
					<h1 class="sdl-article__title"><?php the_title(); ?></h1>
				</header>

				<?php if ( $youtube_id ) : ?>
					<div class="sdl-embed">
						<iframe
							src="<?php echo esc_url( sdl_embed_url( $youtube_id ) ); ?>"
							title="<?php echo esc_attr( get_the_title() ); ?>"
							loading="lazy"
							allow="accelerometer; encrypted-media; picture-in-picture; fullscreen"
							referrerpolicy="strict-origin-when-cross-origin"
							allowfullscreen></iframe>
					</div>
				<?php endif; ?>

				<div class="sdl-article__content entry-content">
					<?php the_content(); ?>
				</div>

				<p class="sdl-cta-inline">
					<a class="sdl-btn" href="<?php echo esc_url( sdl_consultation_url() ); ?>" target="_blank" rel="noopener noreferrer">اطلب استشارتك الآن</a>
				</p>
			</article>

			<?php
			$related = new WP_Query(
				array(
					'post_type'           => SDL_Videos::POST_TYPE,
					'posts_per_page'      => 3,
					'post__not_in'        => array( get_the_ID() ),
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
				)
			);
			if ( $related->have_posts() ) :
				?>
				<section class="sdl-related">
					<h2>فيديوهات أخرى</h2>
					<div class="sdl-grid sdl-grid--3">
						<?php
						while ( $related->have_posts() ) {
							$related->the_post();
							sdl_part( 'video-card' );
						}
						wp_reset_postdata();
						?>
					</div>
				</section>
			<?php endif; ?>
		</div>
		<?php
	endwhile;
	?>
</main>
<?php
get_footer();
