<?php
/**
 * Single article (native post).
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
		?>
		<div class="sdl-container sdl-layout sdl-layout--single">
			<article id="post-<?php the_ID(); ?>" class="sdl-article">
				<nav class="sdl-breadcrumb" aria-label="مسار التنقل">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">الرئيسية</a>
					<span aria-hidden="true">/</span>
					<?php
					$posts_page = (int) get_option( 'page_for_posts' );
					if ( $posts_page ) :
						?>
						<a href="<?php echo esc_url( get_permalink( $posts_page ) ); ?>">المقالات</a>
					<?php endif; ?>
				</nav>

				<header class="sdl-article__head">
					<div class="sdl-card__meta">
						<?php echo sdl_primary_term_link(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
					</div>
					<h1 class="sdl-article__title"><?php the_title(); ?></h1>
				</header>

				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="sdl-article__image"><?php the_post_thumbnail( 'large', array( 'loading' => 'eager' ) ); ?></figure>
				<?php endif; ?>

				<div class="sdl-article__content entry-content">
					<?php the_content(); ?>
				</div>

				<?php the_tags( '<p class="sdl-tags">', '', '</p>' ); ?>
			</article>

			<?php sdl_part( 'sidebar-articles' ); ?>
		</div>
		<?php
	endwhile;
	?>
</main>
<?php
get_footer();
